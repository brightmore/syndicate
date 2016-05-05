<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EWallet
 *
 * @author bright
 */
class MyWallet extends CI_Controller {

    private $total_sec = 900; //5sec * 3

    public function __construct() {
        parent::__construct();
        $this->load->model(
                array(
                'users',
                'Preferences',
                'User_profiles',
                 'Transaction',
                 'ewallet',
                 'messages'
               )
              );
               
        $this->load->helper('mixin');
    }

    public function index() {

        if (!is_login()) {
            redirect('/Auth/login');
            exit;
        }

        if(is_suspended()){
            redirect('Dashboard/account');
        }

        $username = $_SESSION['username'];
        $this->load->model('ewallet');
        //  $this->load->model('Transaction');
        $content['logs'] = $this->ewallet->get_TopUp_Log($username, 50);
        $content['balance'] = $this->ewallet->get_balance($username);
        $content['csrf'] = _get_csrf_nonce();

        $data['content'] = $this->load->view('ewalletView', $content, TRUE);
        $this->load->view('template', $data);
    }

    function process_change_mobile_money(){
        if (!is_login()) {
            echo json_encode(array('message'=>'login_failed','status'=>'failed'));
            exit;
        }

        if(is_suspended()){
            echo json_encode(array('message'=>'suspended','status'=>'security_issues'));
            exit;
        }

         if (!$this->input->is_ajax_request()) {
           echo json_encode(array('message'=>'security_issues','status'=>'force_access'));
           exit;
        }

        $this->form_validation->set_rules("mobile_money","Mobile Money",'trim|required|exact_length[10]|numeric');

        if($this->form_validation->run() === FALSE){
            echo json_encode(array('message'=> 'form_error','error'=> validation_errors(),'status'=>'failed'));
            exit;
        }

        $mobile_money = $this->input->post('mobile_money');
        $username = $_SESSION['username'];

//        $this->db->select('1',FALSE);
//        $this->db->where('phone',$mobile_money);
//        $this->db->where('username',$username);
//        $query = $this->db->get('membership');
//        if($query->num_rows() === 0){
//            echo json_encode(array('message'=>'invalid_phone','status'=>'failed'));
//            exit;
//        }
        $phone = $_SESSION['phone'];

        $phone = '233'.substr($phone, 1,  strlen($phone));
        $code = random_str(10);

        $this->session->set_tempdata('code', $code, $this->total_sec);
        $this->session->set_tempdata('phone_number', $mobile_money, $this->total_sec);
        $uri = base_url().'index.php/MyWallet/verify_mobile_money';

        $message = "You requested to change your mobile money, verify the code at below at $uri\n $code";


        send_sms($message, $phone);

        echo json_encode(array('message'=>'success','status'=>'success'));
        exit;
    }

    function process_verify_mobile_mobile(){
        if (!is_login()) {
            echo json_encode(array('message'=>'login_failed','status'=>'failed'));
            exit;
        }

        if(is_suspended()){
            echo json_encode(array('message'=>'suspended','status'=>'security_issues'));
            exit;
        }

         if (!$this->input->is_ajax_request()) {
           echo json_encode(array('message'=>'security_issues','status'=>'force_access'));
           exit;
        }

        $username  = $_SESSION['username'];

        if($this->session->set_tempdata('code')){
            echo json_encode(array('message'=>'code_expired','status'=>'failed'));
            exit;
        }

        $enter_code = trim($this->input->post('code'));
        $code = $this->session->set_tempdata('code');
        $mobile_mobile = $this->session->set_tempdata('phone_number');

        if(empty($enter_code)){
            echo json_encode(array('message'=>'empty','status'=>'failed'));
            exit;
        }

        if($enter_code !== $code){
            echo json_encode(array('message'=>'invalid_code','status'=>'failed'));
            exit;
        }

        $this->db->set('mobile_money',$mobile_mobile);
        $this->db->where('username',$username);
        $this->db->update('membership');

        if($this->db->affected_rows() > 0){
            echo json_encode(array('message'=>'success','status'=>'success'));
            exit;
        }else{
            echo json_encode(array('message'=>'update_failed','status'=>'failed'));
            exit;
        }

    }

    function verify_mobile_money(){
        if(! is_login()){
            redirect('Auth/login');
        }

        if(is_suspended()){
            redirect('Dashboard/account');
        }

        $content['crsf'] = _get_csrf_nonce();
        $data['isPremium'] = $_SESSION['userType'];
        $data['content'] = $this->load->view('verify_mobile_money', $content, TRUE);
        $this->load->view('template',$data);
    }

    public function valified_topup() {

        if (!is_login()) {
            echo json_encode(array('message'=>'login_issues','status'=>'login'));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
           echo json_encode(array('message'=>'security_issues','status'=>'force_access'));
           exit;
        }


        if (_valid_csrf_nonce() === FALSE) {
            //send notification to the administrator.
            echo json_encode(array('message'=>'security_issues','status'=>'csrf'));
            exit;
        }

        $this->form_validation->set_rules("amount", "Amount", "trim|required|callback_valid_money");
        $this->form_validation->set_rules('transaction_id', 'Transaction ID', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array('message' => validation_errors(), 'status' => 'form_error'));
            exit;
        }

        $amount = $this->input->post('amount');
        $transaction_id = $this->input->post('transaction_id', TRUE);

        $code = random_string(10);

        $username = $_SESSION['username'];

        //this is session will be there for 30mins
        $this->session->set_tempdata('topup_code', $code, $this->total_sec);
        $this->session->set_tempdata('topup_amount', $amount, $this->total_sec);
        $this->session->set_tempdata('transaction_id', $transaction_id, $this->total_sec);
        $balance = $this->user->get_balance($username);

        $phone = $this->users->get_user_phone($username);
        $message = "Enter the code below to verify your amount \n" . $code . "\n Balance before - " . $balance;
        send_sms($message, $phone);
    }

    function process_topup() {

        if (!is_login()) {
            echo json_encode(array('message'=>'login_issues','status'=>'login'));
            exit;
        }

        if(! $this->input->is_ajax_request()){
          echo json_encode(array('message'=>'security_issue','status'=>'failed')); exit;
        }

        $this->form_validation->set_rules('transaction_id','Transaction ID','required|trim|max_lenght[20]');
        $this->form_validation->set_rules('amount','Amount','required|trim|callback_valid_money');

        $topup_code = $this->session->get_tempdata('topup_code');
        $topup_amount = $this->session->get_tempdata('topup_amount');
        $transaction_id = $this->session->get_tempdata('transaction_id');

        if (($topup_code === NULL) || $topup_amount === NULL) {

        }

          //  $this->ewallet->credit_wallet($username, $amount);
          $this->load->model('topups');
        $bool =  $this->topups->add_topup($username,$amount,$transaction_id);

          if($bool){
            echo json_encode(array('message'=>'success','status'=>'success')); exit;
          }

          echo json_encode(array('message'=>'failed','status'=>'failed')); exit;

    }

    function valid_money($input) {
        if (is_double($input) || (filter_var($input, FILTER_VALIDATE_INT) !== false)) {
            return TRUE;
        }
//
//    if  (preg_match('/^[+\-]?\d+(\.\d+)?$/', $input)){
//        return TRUE;
//    }
        $this->form_validation->set_message('valid_money', 'The {field} field contain invalid money figure.');
        return FALSE;
    }

    function topup_code_verification() {

         if(! is_login()){
            redirect('Auth/login');
        }

        $content['crsf'] = _get_csrf_nonce();
        $data['isPremium'] = $_SESSION['userType'];
        $data['content'] = $this->load->view('topup_code_verification', $content, TRUE);
        $this->load->view('template',$data);
    }

    function process_transfer() {

         if (!is_login()) {
            echo json_encode(array('message'=>'login_issues','status'=>'force_access'));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
           echo json_encode(array('message'=>'security_issues','status'=>'force_access'));
            exit;
        }

        // if (_valid_csrf_nonce() === FALSE) {
        //
        //     echo json_encode(array('message'=>'security_alert','status'=>'csrf'));
        //     exit;
        // }

        $this->form_validation->set_rules('code','Code','trim|required|max_lenght[10]');

        if($this->form_validation->run() === FALSE){
            echo json_encode(array('message'=>  validation_errors(),'status'=>'form_error'));
            exit;
        }

        $to = $this->session->tempdata('transfer_to');
        $from = $this->session->tempdata('transfer_from');
        $amount = $this->session->tempdata('transfer_amount');
        $code = $this->session->tempdata('transfer_amount');

        if(($to === NULL)||($from === NULL) ||($amount === NULL) || ($code ===NULL)){
             echo json_encode(array('message'=>'transfer_timeout','status'=>'timeout'));
            exit;
        }

        $enter_code = $this->input->post('code');

        if($enter_code !== $code){
            echo json_encode(array('message'=>'code_mismatched','status'=>'transfer_code'));
            exit;
        }

        if($this->Ewallet->transfer_money($to,$from,$amount)){
            $phone = $_SESSION['phone'];


            //transfer charges
            $message = "Your e-wallet account was credited with GHc $amount from ".$_SESSION['phone'];


            $this->MMessages->set_message($username,$message);

            send_sms($message, $phone);

            echo json_encode(array('message'=>'message_sent','status'=>'transfer_code'));
            exit;

        }else{
            $message = 'Transfer failed';
            echo json_encode(array('message'=>'transfer_failed','status'=>'transfer_code'));
            exit;
        }
    }

    function valid_transfer() {

        if (!is_login()) {
            echo json_encode(array('message'=>'login_issues','status'=>'login'));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
           echo json_encode(array('message'=>'security_issues','status'=>'force_access'));
            exit;
        }

        if (_valid_csrf_nonce() === FALSE) {
            //send notification to the administrator.
             echo json_encode(array('message'=>'security_alert','status'=>'csrf'));
            exit;
        }

        $this->form_validation->set_rules("to", 'Transfer to', 'trim|requried');
        $this->form_validation->set_rules('from', 'Transfer from', 'trim|required');
        $this->form_validation->set_rules('amount','Amount','trim|required|callback_valid_money');

        if ($this->form_validation->run() === false) {
            echo json_encode(array('message'=>validation_errors(),'status'=>'form_error'));
            exit;
        }

        $transfer_to = $this->input->post('to');
        $transfer_from = $this->input->post('from');
        $transfer_amount = $this->input->post('amount');

        $this->session->tempdata('transfer_to', $transfer_to, $this->total_sec);
        $this->session->tempdata('transfer_from', $transfer_from, $this->total_sec);
        $this->session->tempdata('transfer_amount', $transfer_amount, $this->total_sec);

        //generate random string and send via email
        $code = random_string(8);

        $this->session->tempdata('transfer_code', $code, $this->total_sec);

        $url = base_url() . 'index.html/MyWallet/transfer_code_verify';
        $message = "Verify your transaction with the transaction code ".$code.". Click the link below to verify <br /> <a href='" . $url . "' > Link </a>";

        $this->MMessages->set_message($username,$message);

        $subject = "E-Transfer verification";
        $recipient = $_SESSION['email'];
        $this->email->from($this->Preferences->get_value('system_email'), '<no reply>' . $this->Preference->get_value('System_name'));
        $this->email->to($recipient);

        $this->email->subject($subject);
        $this->email->message($message);

        if($this->email->send()){
            echo json_encode(array('message'=>'email_sent','status'=>'ok'));
            exit;
        }else{
            echo json_encode(array('message'=>'email_failed','status'=>'failed'));
            exit;
        }
    }

    function transfer_code_verify() {

        if(! is_login()){
            redirect('Auth/login');
        }

        $content['crsf'] = _get_csrf_nonce();
        $data['isPremium'] = $_SESSION['userType'];
        $data['content'] = $this->load->view('transfer_code_verify', $content, TRUE);
        $this->load->view('template',$data);
    }

}
