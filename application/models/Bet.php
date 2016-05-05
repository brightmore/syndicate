<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bet
 *
 * @author bright
 */
class Bet extends CI_Model {

    private $table_name = 'bets';
    private $table_bet_win = 'bet_win';
    private $ci = null;

    const BETTING_CLOSED = 1;
    const NO_MONEY = 2;
    const NOT_ENOUGH_MONEY = 3;
    const BET_PLACED = 4;
    const PLACE_TODAY = 5;
    const UNKNOWN_ERROR = 6;
    const DB_FAILED = 7;
    const UPDATE_BET = 8;
    const SMS_LOG = 'sms';
    const DEBIT_LOG = 'debit_account';
    const BETTING_LOG = 'placing_betting';
    const PAYMENT_LOG = 'payment';
    const PREMIUM_MEMBERSHP_LOG = 'premium_membership';
    const CREDIT_ACCOUNT_LOG = 'credit_account';

    public function __construct() {
        parent::__construct();
//        $this = & get_instance();
        $this->table_name = $this->config->item('db_table_prefix', 'syndicates') . $this->table_name;
        $this->load->model('Ewallet');
        $this->load->model('user_profiles', 'user_prof');
        $this->load->model('Transaction','transLog');
    }

    public function place_bet($username, $amount, $lottery_name) {

         $this->db->trans_start();

         $time_date = time();
         $today = date('d-M-Y');
         $time = date('H:i:s');

        $bet_closing_time = strtotime($this->Preferences->get_value('bet_closing_time'));

        if (($time_date > $bet_closing_time)) { //check if the you can place a bet base on the bet_closing_time
            return Bet::BETTING_CLOSED;
        }

        if (!$this->Ewallet->has_money($username)) { //check user has money
            return Bet::NO_MONEY;
        }

        $balance = $this->Ewallet->get_balance($username);
        if ($amount > $balance) {
            return Bet::NOT_ENOUGH_MONEY;
        }

        $query = $this->db->query("SELECT * FROM bets WHERE username = ? AND date_created = ?",[$username,$today]);

          if($query->num_rows() == 0){
              if ($this->is_premium($username)) {
                  $discount = ($amount / 100 ) * $this->Preferences->get_value('premium_percentage');
                  $discount_formatted = number_format($discount);
                  $description = "Premium member discount of $discount_formatted";
                  $this->transLog->set_log($username, Bet::CREDIT_ACCOUNT_LOG, $description, $discount_formatted);

                  $this->Ewallet->credit_wallet($username, $discount);
              }

              $data['username'] = $username;
              $data['amount'] = $amount;
              $data['date_created'] = date('d-M-Y');
              $data['lottery_name'] = $lottery_name;
              $data['time'] = $time;

            $this->db->insert($this->table_name, $data);

            if ($this->db->affected_rows() > 0) {

                $description = "placed bet on ".$today;
                $this->transLog->set_log($username, Bet::BETTING_LOG, $description, $amount);
                $this->Ewallet->debit_wallet($username,$amount);

                return Bet::BET_PLACED;
            }

          }else{

              $this->db->select('amount');
              $this->db->where('date_created',$date_created);
              $this->db->where('username',$username);
              $this->db->from($this->table_name);

              $previous_bet_amount = $this->db->get()->row()->amount;
              $update_amount = $previous_bet_amount + $amount;

              $this->db->set('amount',$update_amount);
              $this->db->where('username',$username);
              $this->db->update($this->table_name);

              return Bet::UPDATE_BET;
          }

           $this->db->trans_complete();
        
         if ($this->db->trans_status() === FALSE){
                 // generate an error... or use the log_message() function to log your error
             //    var_dump($this->db->error());
                 return Bet::DB_FAILED;
         }

        return Bet::UNKNOWN_ERROR;
    }

    public function generate_wins($amount_won) {
        if ($this->__bet_today()) {

            $data = [];
            $result = $this->__bet_today();
            $today = date();
            foreach ($result as $value) {

                $amount = $value->amount;
                $phone = $value->phone;
                $username = $value->username;
                $lottery_name = $value->lottery_name;
            //    $my_win = floor(($amount / $result) * $amount_won); //@todo get the passawas off
                $charges = ($amount / 100 ) * $this->Preferences->get_value('win_charges');
                $my_win = $amount - $charges;

                $description = "You won " . $my_win . ' today ' . date("d-M-Y");

                $this->transLog->set_log($username, Bet::CREDIT_ACCOUNT_LOG, $description, $my_win);
                if ($this->user_prof->is_sms_activated($username)) {
                    //send sms

                    $message =  $description.' from '.$this->preferences->get_value('System_name');;

                    $sms_cost = $this->preferences->get_value('sms_cost');
                    $this->ewallet->debit_wallet($username, $sms_cost);
                    $this->transLog->set_log($username, Bet::DEBIT_LOG, "sms alert from winning", $sms_cost);
                    $track_amount =$charges+$sms_cost;

                    track_money($track_amount);

                    send_sms($message, $phone);
                }else{
                    track_money($charges);
                }

                array_push($data,array(
                     'amount'=>$amount,
                     'username'=>$username,
                     'datecreated'=>$today,
                     'lottery_name'=>$lottery_name
                    ));

            }

            $this->db->insert_batch($this->table_bet_win, $data);

            if($this->db->affected_rows() > 0){
                $this->db->select('*');
                $this->db->where('datecreated',$today);
                $this->db->from($this->table_bet_win);
                $result = $this->db->get();

                exportCSV($result);

            }
        }
    }

    public function remove_bet_place($username) {
        $today = date('d-M-Y');
        $this->db->where('date_created',$today);
        $this->db->where('LOWER(username)',$username);
        $this->db->delete($this->table_name);
        if($this->db->affected_rows() > 0){
            return TRUE;
        }

        return FALSE;
    }

    function bet_place_today(){
        $username = $_SESSION['username'];
        $today = date("d-M-Y",time());
        $this->db->select("amount");
        $this->db->from($this->table_name);
        $this->db->where('date_created',$today);
        $this->db->where('username',$username);
        $query = $this->db->get();
        if($query->num_rows()){
            return $query->row()->amount;
        }

        return FALSE;
    }

    function all_people_bets_today($limit){
        $today = date('d-M-Y');

        $this->db->select("*");
        $this->db->from($this->table_name);
        $this->db->where('date_created',$today);
        $this->db->limit($limit);
        $query = $this->db->get();

        $data = [];
        if($query->num_rows()){
            $data = $query->result();
            $query->free_result();
        }

        return $data;

    }

    function bet_head(){

      $this->db->trans_start();

      $today = date('d-M-Y');

      $has_run_today = $this->Preferences->get_value('has_bet_head_run_today');

      if($has_run_today == $today){
        echo json_encode(array('message'=>'bet_head_processed_before','status'=>'success')); exit;
      }

      // to determine whether bet head have been processed today
      $this->db->set('has_bet_head_run_today',$today);
      $this->db->where('name','has_bet_head_run_today');
      $this->db->update('Preferences');

      $data = [];
      $this->db->select('username,bet_head,bet_head_amount');
      $this->db->where('bet_head','1');
      $this->db->from('user_profiles');
      $query = $this->db->get();

      if($query->num_rows() > 0){
        $data = $query->result();

        foreach ($data as $key => $value) {
          $username = $value->username;
          $amount = $value->bet_head_amount;
          $status = $this->place_bet($username,$amount,get_lottery_name(date('l')));

          if(($status == BET::BET_PLACED) || ($status == BET::UPDATE_BET)){

            $this->MMessagage->set_message($username,"Bet was placed on ".$today);

          }else if(($status == BET::NO_MONEY) || ($status == BET::NOT_ENOUGH_MONEY)){
            $this->MMessagage->set_message($username,"There is no enough money in you wallet to place bet today, To contiune playing the game please topup now. - ".$today);
          }
        }

        $query->free_result();
      }

       
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE){
              // generate an error... or use the log_message() function to log your error
            
              log_message(1,$this->db->error());
              return Bet::DB_FAILED;
      }

       return BET::BET_PLACED;
    }

    function total_bet_place_today() {

        $today = date("d-M-Y",time());
        $this->db->select("SUM(amount) as total");
        $this->db->from($this->table_name);
        $this->db->where('date_created',$today);
        $query = $this->db->get();
        $total = $query->row()->total;
        if ($total) {
            return $total;
        }

        return false; //no bet placed
    }

    /* members who placed bet today and amount
     * @return object
     */

    private function __bet_today() {
        $today = date('d-M-Y');

        $this->db->select('phone,amount,username');
        $this->db->from('membership');
        $this->db->where('date_created', $today);
        $this->db->join($this->table_name, 'bets.username = membership.username');
        $query = $this->db->get();
        $data = null;
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $query->free_result();
        }

        return $data;
    }

    private function is_premium($username) {
        $this->db->select('1', TRUE);
        $this->db->from('membership');
        $this->db->where('LOWER(username)', strtolower($username));
        $this->db->where('isPremium', '1');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return TRUE;
        }

        return false;
    }

}
