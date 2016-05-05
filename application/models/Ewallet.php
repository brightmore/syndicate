<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ewallet
 *
 * @author bright
 */
class Ewallet extends CI_Model {

    private $table_name = 'ewallet';
    private $ci = null;

    const SMS_LOG = 'sms';
    const DEBIT_LOG = 'debit_account';
    const BETTING_LOG = 'placing_betting';
    const PAYMENT_LOG = 'payment';
    const PREMIUM_MEMBERSHP_LOG = 'premium_membership';
    const CREDIT_ACCOUNT_LOG = 'credit_account';
    const TRANSFER_CREDIT = 'transfer_credit';
    const TRANSFER_DEBIT = 'transfer_debit';

    public function __construct() {
        parent::__construct();


        $this->table_name = $this->config->item('db_table_prefix', 'syndicates') . $this->table_name;
        $this->load->model('transaction', 'transLog');
    }

    function has_money($username) {
        if ($this->get_balance($username) && $this->get_balance($username) > 1.00) {
            return TRUE;
        }
        return FALSE;
    }

    function get_balance($username) {
        $this->db->select('account_balance');
        $this->db->where('LOWER(username)', $username);
        $this->db->limit(1);
        $query = $this->db->get($this->table_name);

        if ($query->num_rows() == 1) {
            return $query->row()->account_balance;
        }

        return FALSE;
    }

    /*
     * @param string
     * @param string
     * @return boolean
     */

    function credit_wallet($username, $amount) {

            if ($this->has_topup_before($username)) {
                $balance = $this->get_balance($username);
                $this->db->set('amount', $amount);
                $this->db->set('lastupdated', date('Y-m-d H:i:s'));
                $this->db->set('account_balance', $balance);
                $this->db->where('LOWER(username)', $username);
                $this->db->update($this->table_name);

                $description = "E-wallet was topup with $amount on " . date('d-m-Y H:i:s');
                $this->transLog->set_log($username, Ewallet::CREDIT_ACCOUNT_LOG, $description, $amount);
                if ($this->db->affected_rows() > 0) {
                    return true;
                }

                return FALSE;
            } else {
                $this->db->set('amount', $amount);
                $this->db->set('lastupdated', date('Y-m-d H:i:s'));
                $this->db->set('account_balance', $amount);
                $this->db->set('username', $username);
                $this->db->insert($this->table_name);

                $description = "Ewallet account was created with - $amount on " . date('d-m-Y H:i:s');
                $this->transLog->set_log($username, Ewallet::CREDIT_ACCOUNT_LOG, $description, $amount);

                if ($this->db->insert_id()) {
                    return TRUE;
                }

                return false;
            }

    }

    /*
     *
     * @param string
     * @return boolean
     */
    function has_topup_before($username) {
        $this->db->select('1', FALSE);
        $this->db->where('LOWER(username)', $username);
        $this->db->limit(1);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1) {
            return TRUE;
        }

        return false;
    }

    /*
     * @param string
     * @param double
     * @return boolean
     */

    function debit_wallet($username, $amount) {
        if ($this->has_topup_before($username)) {
            $balance = $this->get_balance($username);

            if ($balance >= $amount) { //there is enough amount to be debited
                $balance = $balance - $amount;

                $this->db->set('account_balance', $balance);
                $this->db->set('lastupdated', date('Y-m-d H:i:s'));
                $this->db->where('LOWER(username)', $username);
                $this->db->update($this->table_name);
                if ($this->db->affected_rows() > 0) {
                    return TRUE;
                }

                return FALSE;
            } else {
                //there is no enough money in the wallet
                return FALSE;
            }
        } else {

            return FALSE;
        }
    }

    /*
     * @param string
     * @param integer
     * @return mixed
     */
    function get_TopUp_Log($username, $limit) {
        $this->db->select('id,description,date_created,amount');
        $this->db->where('LOWER(username)', strtolower($username));
        $this->db->where('LOWER(log_type)', strtolower(Ewallet::CREDIT_ACCOUNT_LOG));
        $this->db->limit($limit);
        $query = $this->db->get('transaction_log');

        $data = [];
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $query->free_result();
            return $data;
        }

        return $data;
    }

    /*
     * @param string
     * @param integer
     * @return mixed
     */
    function get_ewallet_activities($username,$limit){
        $this->db->select('id,description,date_created,amount');
        $this->db->where('LOWER(username)', strtolower($username));
        $this->db->where('LOWER(log_type)', strtolower(Ewallet::CREDIT_ACCOUNT_LOG));
        $this->db->limit($limit);
        $query = $this->db->get('transaction_log');

        $data = [];
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $query->free_result();
            return $data;
        }

        return $data;
    }

    function email_my_transaction() {
        //not implemented
    }

    function transfer_money($to,$from,$amount){
        $this->db->trans_start();

        if($amount <= 50 ){
          $transfer_below_50 = $this->Preferences->get_value('transfer_below_50');
          $transfer_charge = ($amount / 100 ) * $transfer_below_50;

        }else if(($amount > 50) && ($amount <= 200)){
          $transfer_above_50_less_200 = $this->Preferences->get_value('transfer_above_50_less_200');
          $transfer_charge = ($amount / 100 ) * $transfer_above_50_less_200;
        }else{
            $transfer_above_200 = $this->Preferences->get_value('transfer_above_200');
            $transfer_charge = ($amount / 100 ) * $transfer_above_200;
        }

        trace_money($tranfer);

        $sent_amount = $amount - $transfer_charge;
        $this->credit_wallet($to, $sent_amount);
        $this->debit_wallet($from, $amount);

        $description_debit = "You transfered ".$amount.' to '.$to.' and mobile number is '.$this->users->get_user_phone($username);
        $this->transLog->set_log($from, Ewallet::TRANSFER_DEBIT, $description_debit, $amount);
        $description_credit = "You have recieve GHc ".$sent_amount.' from '.$from;
        $this->transLog->set_log($to,  Ewallet::TRANSFER_CREDIT,$description_credit,$amount);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
        // generate an error... or use the log_message() function to log your error
            return FALSE;
        }

        return TRUE;
    }



}
