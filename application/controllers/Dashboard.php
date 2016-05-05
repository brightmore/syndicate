<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dashboard
 *
 * @author bright
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('captcha');

        $this->load->model(array('users', 'Preferences', 'Bet', 'Lottery_numbers', 'User_profiles'));
        $this->load->helper('mixin');
        $this->load->model('messages');

//        if ($this->agent->is_robot()) {
//            show_error('We think you are a bot and bot is not allow on this site... :)');
//            exit;
//        }
    }

/*
*@return NULL
*/
    function index() {

        if (!is_login()) {
            redirect('Auth/login');
        }

        $this->load->model('Lottery_numbers');
        $this->load->model('Transaction');

        $random_number = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        // setting up captcha config
        $vals = array(
            'word' => $random_number,
            'img_path' => './captcha/',
            'img_url' => base_url() . 'captcha/',
            'img_width' => 140,
            'img_height' => 32,
            'expiration' => 7200
        );

        $username = $_SESSION['username'];

        $content['currentNumber'] = $this->Lottery_numbers->currentNumber();
        $content['logs'] = $this->Transaction->get_user_logs($username, 30);
        $content['isCurrentNumberViewable'] = $this->Lottery_numbers->isCurrentNumberViewable();
        $content['numbers_draw'] = $this->Lottery_numbers->get_draws(30);
        $content['people_bets_today'] = $this->Bet->all_people_bets_today(30);
        $content['bet_place_today'] = $this->Bet->bet_place_today();

        $content['total_bet_placed'] = $this->Bet->total_bet_place_today();
        $content['transaction_activities'] = $this->Transaction->get_user_logs($username, 30);

        $content['csrf'] = _get_csrf_nonce();
        $content['captcha'] = create_captcha($vals);
        $data['baseurl'] = base_url();
        $data['isPremium'] = $_SESSION['userType'];

        $this->session->set_userdata('captchaWord', $content['captcha']['word']);
        $data['content'] = $this->load->view('index', $content, TRUE);
        $this->load->view('template', $data);
    }


/**
*
*@var string
*@return bool
*/
    public function check_captcha($str) {
        $word = $this->session->userdata('captchaWord');
        if (strcmp(strtoupper($str), strtoupper($word)) == 0) {
            return true;
        } else {
            $this->form_validation->set_message('check_captcha', 'Please enter correct words!');
            return false;
        }
    }


    function process_betting() {

        if (!is_login()) {

            json_encode(array('message' => 'login_failed', 'status' => 'login_issue'));
            exit;
        }


        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('message' => 'ajax_issues', 'status' => 'force_access'));
            exit;
        }


        $this->form_validation->set_rules('bet', 'Bet', 'trim|required|callback_valid_money');
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array('message' => validation_errors(), 'status' => 'form_error'));
            exit;
        }

        $bet = trim($this->input->post('bet', TRUE));

        $lottery_name = get_lottery_name(date('l'));
        $username = $_SESSION['username'];

        $betting = $this->Bet->place_bet($username, $bet, $lottery_name);

        switch ($betting) {
            case 1:
                echo json_encode(array('message' => 'betting_closed', 'status' => 'betting'));
                break;
            case 2:
                echo json_encode(array('message' => 'No_money', 'status' => 'betting'));
                break;
            case 3:
                echo json_encode(array('message' => 'not_enough_money', 'status' => 'betting'));
                break;
            case 4:
                echo json_encode(array('message' => 'bet_placed', 'status' => 'betting'));
                break;
            case 5:
                echo json_encode(array('message' => 'bet_placed_already', 'status' => 'betting'));
                break;
            default :
                echo json_encode(array('message' => 'unknown_error', 'status' => 'betting'));
                break;
        }
    }


    function account() {

        if (!is_login()) {
            redirect('/Auth/login');
            exit;
        }


        $username = $_SESSION['username'];

        $this->db->select('id');
        $this->db->where('username', $username);
        $this->db->from('membership');
        $query = $this->db->get();
        $id = $query->row()->id;

        $content['id'] = $id;
        $content['isSuspended'] = $this->users->is_suspended($username);
        $content['hasBalance'] = $this->Ewallet->has_money($username);
        $data['baseurl'] = base_url();
        $data['isPremium'] = $_SESSION['userType'];
        $content['record'] = $this->users->get_user_by_username($username);
        $data['content'] = $this->load->view('account', $content, TRUE);
        $this->load->view('template', $data);
    }

    function enable_sms() {

        if (!is_login()) {
            echo json_encode(array('message' => 'not_login', 'status' => 'failed'));
            exit;
        }

        $state = $this->input->post('state');
        $username = $_SESSION['username'];

        if ($state) {
            $state_value = $this->User_profiles->activate_sms($username);
        } else {
            $state_value = $this->User_profiles->deactivate_sms($username);
        }

        if ($state_value) {
            echo json_encode(array('message' => 'true', 'status' => 'success'));
            exit;
        }

        echo json_encode(array('message' => 'false', 'status' => 'failed'));
        exit;
    }

    function enable_bet_head() {

        if (!is_login()) {
            echo json_encode(array('message' => 'not_login', 'status' => 'failed'));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('message' => 'security_issues', 'status' => 'login'));
            exit;
        }

        $amount = $this->input->post('amount');
        $username = $_SESSION['username'];
        $state = $this->input->post('state');


        try {
            if ($state == TRUE) {
                $this->db->set('bet_head', '1');
                $this->db->set('bet_head_amount', $amount);
            } else {
                $this->db->set('bet_head', '0');
                $this->db->set('bet_head_amount', 0.00);
            }

            $this->db->where('username', $username);
            $this->db->update('user_profiles');

            echo json_encode(array('message' => 'success', 'status' => 'success'));
            exit;
        } catch (Exception $ex) {
            echo json_encode(array('message' => 'server_error', 'status' => 'failed'));
        }
    }

    function enable_premium_member() {

        if (!is_login()) {
            echo json_encode(array('message' => 'not_login', 'status' => 'failed'));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('message' => 'security_issues', 'status' => 'login'));
            exit;
        }

        $username = $_SESSION['username'];
        $state = $this->input->post('state');

        if($this->users->isPremium($username)){
            echo json_encode(array('message'=>'already_premium_member','status'=>'success'));exit;
        }

        if ($state) {

            $this->load->model('Transaction','transLog');

                $premium_charge = $this->Preferences->get_value('Premium_charge');
                $description = "Premium charges";
                $log_type = 'premium_membership';

                $balance = $this->Ewallet->get_balance($username);

                if($balance >= $premium_charge){
                    if($this->Ewallet->debit_wallet($username, $premium_charge)){
                        $this->transLog->set_log($username,$log_type,$description,$premium_charge);

                        if($this->users->set_premium_membership($username)){

                            $this->MMessagage->set_message($username,"We are happy to have as premium member as part of this amazing community, we know you will enjoy every benefit that comes with it. Let the play game...");

                            echo json_encode(array('message'=>'success','status'=>'success')); exit;

                        }else{

                              $this->MMessagage->set_message($username,"System failed to activite your premium membership, Please try again later.");

                            $this->Ewallet->credit_wallet($username, $premium_charge);
                           echo json_encode(array('message'=>'internal_server_error','status'=>'failed'));exit;
                        }
                    }
                }

                echo json_encode(array('message'=>'not_money','status'=>'failed'));
                exit;
        }else{
           echo json_encode(array('message'=>'security_issue','status'=>'failed'));
           exit;
        }
    }

    function settings() {

        if (!is_login()) {
            redirect('Auth/login');
            exit;
        }

        $data['baseurl'] = base_url();
        $data['isPremium'] = $this->users->isPremium($_SESSION['username']);
        $content['settings'] = "";
        $data['content'] = $this->load->view('settings', $content, TRUE);
        $this->load->view('template', $data);
    }

    function about_us() {

        if (!is_login()) {
            redirect('/Auth/login');
            exit;
        }

        $data['isPremium'] = $this->users->isPremium($_SESSION['username']);
        $content[''] = '';
        $data['content'] = $this->load->view('about_us', $content, TRUE);
        $this->load->view('template', $data);
    }

    function how_to_play() {

        if (!is_login()) {
            redirect('/Auth/login');
            exit;
        }

        $data['ispremium'] = $this->users->isPremium($_SESSION['username']);
        $content[''] = '';
        $data['content'] = $this->load->view('howtoplay', $content, TRUE);
        $this->load->view('template', $data);
    }

    function contact_us() {

        if (!is_login()) {
            redirect('/Auth/login');
            exit;
        }

        $data['ispremium'] = $this->users->isPremium($_SESSION['username']);
        $content[''] = '';
        $data['content'] = $this->load->view('contact', $content, TRUE);
        $this->load->view('template', $data);
    }

    function valid_money($value) {
        if (preg_match("/[1-9]^[0-9\,]{0,1}[0-9]*(\.\d{1,2})?$|[1-9]*[\.]([\d][\d]?)$|[0-9]{1,}$/", $value)) {
            return TRUE;
        }
        $this->form_validation->set_message('valid_money','Please provide a valid money');
        return false;
    }
}
