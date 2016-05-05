<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Auth
 *
 * @author bright
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Bcrypt');
        $this->load->library('User_agent');
        $this->load->model('users');
        $this->load->model('Preferences');
        if ($this->agent->is_robot()) {
            show_error('We think you are a bot and bots are not allow on this site... :)');
            exit;
        }
    }

    function login() {
        $data['csrf'] = _get_csrf_nonce();
        $data['baseurl'] = base_url();
        $this->load->view('auth/header');
        $this->load->view('auth/login', $data);
    }

    function logout() {
        unset($_SESSION['username']);
        unset($_SESSION['userType']);
        unset($_SESSION['login']);

        redirect('Auth/login');
    }

    function process_login() {

        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('message' => 'security_issues', 'status' => 'force_access'));
            exit;
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array('message' => "form_error", 'status' => 'failed', 'error' => validation_errors()));
            exit;
        }

        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));

        //loading model

        $this->load->model('Login_attempts', 'attempts');
        $this->load->model('Preferences');
        $this->load->model('Transaction');

        $userinfo = $this->users->get_user_by_username($username);

        if ($userinfo) {

            $ip_address = $this->input->ip_address();

            if ($this->bcrypt->check_password($password, $userinfo->password)) {

                $_SESSION['login'] = TRUE;
                $_SESSION['userType'] = $userinfo->isPremium;
                $_SESSION['username'] = $userinfo->username;
                $_SESSION['active'] = $userinfo->suspended;
                $_SESSION['phone'] = $userinfo->phone;
                $_SERVER['email'] = $userinfo->email;

                $this->attempts->clear_attempts($ip_address, $username);
                //  redirect('Dashboard');

                echo json_encode(array('message' => 'success', 'status' => 'success'));
                exit;
            } else { // Password does not match stored password.
                $this->attempts->increase_attempt($ip_address, $username);

                $login_count_attempts = $this->config->item('login_count_attempts', 'syndicates');

                $attemps_to_login = $this->attempts->get_attempts_num($ip_address, $username);

                if ($login_count_attempts === $attemps_to_login) {

                    $this->load->model('Ewallet');
                    $this->load->model('User_profile');

                    $this->users->ban($username); //suppend user account
                    //suppend the user account, the account will be activated when
                    $random_number = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                    //@todo send random number as sms to the user to activate his/her account
                    $phone = $userinfo->phone;
                    $sms_cost = $this->Preferences->get_value('sms_cost');
                    $this->Ewallet->debit_wallet($username, $sms_cost);
                    $log_type = 'sms';
                    $description = "Cost for sending code for activating your suspended account";

                    if (!$translog = $this->Transaction->set_log($username, $log_type, $description, $sms_cost)) {
                        //@todo this user is performing illegal operation. his/her account should be remove manually.
                    }

                    if ($this->Preferences->getPreferences('isSuspendedPaid') == 1) {

                        $suspendedPayAmount = $this->Preferences->get_value('suspendedPay');
                        $this->Ewallet->debit_wallet($username, $suspendedPayAmount);

                        $log_type = 'supended_account';
                        $description = "The system charged you of suspending your account.";
                        if (!$translog = $this->Transaction->set_log($username, $log_type, $description, $suspendedPayAmount)) {
                            //@todo this user is performing illegal operation. his/her account should be remove manually.
                        }
                    }

                    $message = "Your account was suspended because your attempts to login excessed the limit. Use the code below to activate it.\n $phone";

                    send_sms($message, $phone);

                    echo json_encode(array('message' => 'account_suspended_now', 'status' => 'failed'));
                    //redirect('Auth/suspended');
                }
            }
        } else {
            echo json_encode(array('message' => 'user_not_exist.', 'status' => 'failed'));
            exit;
        }
    }

    function verify_account() {

    }

    function activate_account() {

        $code = trim($this->input->post('code'));

        if (!isset($code)) {
            echo json_encode(array('message' => 'empty_code', 'status' => 'failed'));
            exit;
        }

        $token = $this->session->tempdata('token');
        $email = $this->session->tempdata('email');
        if ($token && ($code === $token)) {

            // unset values now
            unset($_SESSION['email']);
            unset($_SESSION['token']);

            //activate your account
            $this->db->set('active', 1);
            $this->db->from('membership');
            $this->db->where('email', $email);
            $this->db->update();

            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('message' => 'success', 'status' => 'success'));
                exit;
            } else {
                echo json_encode(array('message' => 'failed_to_activate', 'status' => 'failed'));
                exit;
            }
        } else {

            echo json_encode(array('message' => 'token_not_exist', 'status' => 'failed'));
            exit;
        }
    }

    function account_activation(){
        $this->load->view('auth/header');
        $this->load->view('verify_account');
    }

    function account_activated_successfully(){
        $content['uri'] = base_url() . 'index.php/Auth/login';
        $this->load->view('auth/header');
        $this->load->view('auth/email_verify_success',$content);
    }

    function activate_suspended_account() {

    }

    function process_change_password() {

        if (!is_login()) {
            echo json_encode(array('message' => 'login_issues', 'status' => 'failed'));
            exit;
        }

        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('message' => 'security_issues', 'status' => 'failed'));
            exit;
        }

        $this->form_validation->set_rules('oldPasswd', 'Old Password', 'trim|required|callback_checkOldpassword');
        $this->form_validation->set_rules('confirmPassword', 'New Password', 'trim|required|min_length[5]|max_length[36]');
        $this->form_validation->set_rules('newPassword', 'New Password', 'trim|required|matches[newPassword]');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array('message' =>  'form_error', 'error' =>validation_errors(),'status'=>'failed'));
            exit;
        }

        $username = $_SESSION['username'];

        if ($this->users->is_suspended($username)) {
            echo json_encode(array('message' => 'suspended', 'status' => 'failed'));
            exit;
        }


        $newPassword = $this->input->post('newPassword', TRUE);

        if (!$password = $this->bcrypt->hash_password($newPassword)) {

            echo json_encode(array('message' => 'error_hashing_password', 'status' => 'failed'));
            exit;
        }

        $this->db->set('password', $password);
        $this->db->where('username', $username);
        $this->db->update('membership');

        if ($this->db->affected_rows() > 0) {
            echo json_encode(array('message' => 'password_changed', 'status' => 'success'));
            exit;
        } else {
            echo json_encode(array('message' => 'password_failded', 'status' => 'failed'));
            exit;
        }
    }

    function suspended() {

        if (!is_login()) {
            redirect('Auth/login');
        }

        $content['suspendedAmount'] = ($this->Preferences->get_value('isSuspendedPaid')) ?
                $this->Preferences->get_value('suspendedPay') : NULL;
        $content['csrf'] = _get_csrf_nonce();
        $this->load->view('auth/header');
        $this->load->view('auth/suspended', $content, TRUE);
        $this->load->view('auth/footer');
    }

    function process_suspended() {

         if (!is_login()) {
            echo json_encode(array('message' => 'login_issues', 'status' => 'login'));
            exit;
        }

        $this->form_validation->set_rules('code', 'Code', 'required');
        $this->form_validation->set_rules("username", "Username", 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array('error' => validation_errors()));
            exit;
        }

        $code = $this->input->post('code');
        $username = $this->input->post('username');

        //check if the code is not used and exist
        $this->db->select("1", FALSE);
        $this->db->where('code', $code);
        $this->db->where('username', $username);
        $this->db->where('isUsed', '0');
        $this->db->from('suspended_profile');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            $this->db->limit(1);
            $this->db->from('suspended_profile');
            $this->db->where('username', $username);
            $this->db->where('code', $code);
            $this->db->delete();

            if ($this->db->affected_rows() > 0) {
                //
                redirect("Auth/login");
            } else {
                echo json_encode(array('error' => 'Either the code doesn\'t exist or it doesn\'t match with the username provided'));
                exit;
            }
        }
    }

    function forget_password() {
        $this->load->view('auth/header');
        $this->load->view('auth/forget_password');
        $this->load->view('auth/footer');
    }

    function proccess_forget_password() {

        if (!is_login()) {
            echo json_encode(array('message' => 'login_issues', 'status' => 'login'));
            exit;
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() === FALSE) {
            //
            echo json_encode(array('message' => 'form_error', 'result' => validation_errors(), 'status' => 'failed'));
            exit;
        }

        $email = $this->input->post('email');

        $password = random_string(8);

        $password_hashing_error = "Internal error hashing password, Please try again.";

        if (!$password = $this->bcrypt->hash_password($password)) {
            echo json_encode(array('message' => $password_hashing_error, 'status' => 'failed'));
            exit;
        }

        $this->db->set('password', $password);
        $this->db->where('email', $email);
        $this->db->update('membership');

        if ($this->db->affected_rows() > 0) {

            $this->load->library('email');

            $config['protocol'] = 'sendmail';
            $config['mailpath'] = '/usr/sbin/sendmail';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = TRUE;

            $this->email->initialize($config);
            $url = base_url() . 'index.php/Auth/login';
            // build message
            $message = <<<EOD
Please use this password: $password to login. After you have login, you can change it. Click the link below to <br />
<a href='$url'>login</a>
Thank you.
EOD;

            $this->email->from($this->Preferences->get_value('system_email'), 'Syndicate');
            $this->email->to($email);
            $this->email->subject('Syndicate Password Recovery');
            $this->email->message($message);

            if ($this->email->send()) {
                echo json_encode(array('message' => 'success', 'status' => 'success'));
                exit;
            } else {
                echo json_encode(array('message' => 'failed', 'status' => 'success'));
                exit;
            }
        }

        echo json_encode(array('message' => 'email_not_exist', 'status' => 'failed'));
        exit;
    }

    function process_profile() {


        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('message' => 'security_issues', 'status' => 'force_access'));
            exit;
        }

//        if (_valid_csrf_nonce() === FALSE) {
//            //something fishy might be up
//            show_error("Code red, Something fishy might be up");
//            //send notification to the administrator.
//            //@todo
//        }

        $same_as_mobile_money = FALSE;

        $this->form_validation->set_rules('username', 'Username', array(
            'required',
            array($this->users, 'is_user_available')
        ));

        $this->form_validation->set_rules('phone', 'Phone', 'required');
        $this->form_validation->set_rules('region', 'Region', 'callback_valid_region');
        $this->form_validation->set_rules('email', 'Email', array(
            'required', 'valid_email',
            array($this->users, 'is_email_available')));

        $this->form_validation->set_rules('town', 'Town', 'required|trim');

        if ($this->input->post('same_phone_as_mobile_money')) {
            $this->form_validation->set_rules('same_phone_as_mobile_money');
            $same_as_mobile_money = TRUE;
        }

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array('message' => 'form_error', 'error' => validation_errors(), 'status' => 'failed'));
            exit;
        }

        $username = trim($this->input->post('username', TRUE));
        $email = trim($this->input->post('email'),TRUE);
        $region = trim($this->input->post('region'),TRUE);
        $town = trim($this->input->post('town'),TRUE);
        $phone = trim($this->input->post('phone'),TRUE);


        if(! $this->input->post('id')){ //not update

            if($this->phone_exist($phone)){
                echo json_encode(array('message'=>'phone_exist','status'=>'failed'));
                exit;
            }

            if($this->email_exist($email)){
                echo json_encode(array('message'=>'email_exist','status'=>'failed'));
                exit;
            }

            if($this->username_exist($username)){
                echo json_encode(array('message'=>'user_exist','status'=>'failed'));
                exit;
            }

        }


        if ($this->input->post('id')) {
             try {

                $id = $this->input->post('id');
                $this->db->set('username', $username);
                $this->db->set('email', $email);
                $this->db->set('phone', $phone);
                $this->db->set('region', $region);
                $this->db->set('town', $town);
                $this->db->where('id', $id);
                $this->db->update('membership');

                if ($this->db->affected_rows() > 0) {
                    echo json_encode(array('message' => 'success', 'status' => 'success'));
                    exit;
                } else {
                    echo json_encode(array('message' => 'update_failed', 'status' => 'failed'));
                    exit;
                }
            } catch (Exception $ex) {
                //@todo
                echo json_encode(array('message' => 'server_error', 'status' => 'failed'));
                exit;
            }
        }

            $password = random_str(7);

            $same_as_mobile_money = TRUE;

            $password_hashing_error = "Internal error hashing password, Please try again.";
            if (!$hash_password = $this->bcrypt->hash_password($password)) {

                echo json_encode(array('message' => 'error_hashing_password', 'status' => 'failed'));
                exit;
            }

            $data['username'] = $username;
            $data['password'] = $hash_password;
            $data['email'] = $email;
            $data['phone'] = $phone;
            $data['region'] = $region;
            $data['town'] = $town;
            $data['mobile_money_number'] = $same_as_mobile_money ? $phone :
                    $this->input->post('mobile_money');

            if ($this->users->create_user($data, TRUE)) {

                $this->load->library('email');
                // generate token
                $token = random_str(8);

                $ttl = 60 * 60;
                $this->session->set_tempdata('toke', $token, $ttl);
                $this->session->set_tempdata('email', $email, $ttl);

                // generate uri
                $uri = base_url()."index.php/Auth/verify_account";
                $uri = urlencode($uri);
                // build message
//                $message = <<<EOD
//Greetings. Please confirm your receipt of this email by visiting the following URI: <a href='$uri'>Login</a> Thank you. <br />
//                        Please note: the token expires in an hour
//EOD;
$message = "password: ".$password."\n username: $username" ;//"Use this $token to verify your account, follow this $uri \n The expire in an hour";
//                $config['protocol'] = 'sendmail';
//                $config['mailpath'] = '/usr/sbin/sendmail';
//                $config['charset'] = 'iso-8859-1';
//                $config['wordwrap'] = TRUE;
//
//                $this->email->initialize($config);

                send_sms($message, $phone);
                echo json_encode(array('message' => 'verify_token_email', 'status' => 'status'));
                    exit;
//                $this->email->from($this->Preferences->get_value('system_email'), 'Syndicate');
//                $this->email->to($email);
//                $this->email->subject('Syndicate Email verification');
//                $this->email->message($message);
//
//                if ($this->email->send()) {
//                    echo json_encode(array('message' => 'verify_token_email', 'status' => 'status'));
//                    exit;
//                } else {
//                    $message = "Use this $token to verify your account, follow this $uri \n The expire in an hour";
//                    send_sms($message, $phone);
//                    echo json_encode(array('message' => 'verify_token_sms', 'status' => 'status'));
//                    exit;
//                }
            } else {

                echo json_encode(array('message' => 'create_failed', 'status' => 'failed'));
            }

    }

    function valid_region($region) {
        if (array_key_exists($region, regions())) {
            return TRUE;
        } else {
            $this->form_validation->set_message('valid_region', 'Please provide a valid region');
            return FALSE;
        }
    }

    function checkOldpassword($password) {

        $username = $_SESSION['username'];

        $userinfo = $this->users->get_user_by_username($username);
        if ($this->bcrypt->check_password($password, $userinfo->password)) {
            return TRUE;
        }

        $this->form_validation->set_message('checkOldpassword', 'Invalid password provided.');
        return FALSE;
    }

    function username_exist($username){
        $this->db->select("username");
        $this->db->where('username',$username);
        $query = $this->db->get('membership');
        if($query->num_rows() > 0){
            return TRUE;
        }

        return FALSE;
    }

    function phone_exist($phone){
        $this->db->select("phone");
        $this->db->where('phone',$phone);
        $query = $this->db->get('membership');
        if($query->num_rows() > 0){
            return TRUE;
        }

        return FALSE;
    }

    function email_exist($email){
        $this->db->select("email");
        $this->db->where('email',$email);
        $query = $this->db->get('membership');
        if($query->num_rows() > 0){
            return TRUE;
        }

        return FALSE;
    }

}
