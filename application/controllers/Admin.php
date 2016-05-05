<?php

  /**
   *
   */
  class Admin extends CI_Controller
  {

    public function __construct() {
      parent::__construct();
      $this->load->model('Login_attempts', 'attempts');
      $this->load->model('Preferences');
      $this->load->model('Transaction');
      $this->load->model('Topups');
      $this->load->model('Bet');
    }

    public function index(){

    }

    public function create_account(){

    }

    public function login(){

    }

    function logout() {
        unset($_SESSION['username']);
        unset($_SESSION['userType']);
        unset($_SESSION['phone']);
        unset($_SESSION['email']);

        redirect('Admin/login');
    }

    public function process_login(){

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

              $userinfo = $this->users->get_user_by_username($username);

              if ($userinfo) {

                  $ip_address = $this->input->ip_address();

                  if ($this->bcrypt->check_password($password, $userinfo->password)) {

                      $_SESSION['login'] = TRUE;
                      $_SESSION['username'] = $userinfo->username;
                      $_SESSION['phone'] = $userinfo->phone;
                      $_SESSION['email'] = $userinfo->email;

                      $this->attempts->clear_attempts($ip_address, $username);
                      //  redirect('Dashboard');

                      echo json_encode(array('message' => 'success', 'status' => 'success'));
                      exit;
                  } else { // Password does not match stored password.
                      $this->attempts->increase_attempt($ip_address, $username);

                      $login_count_attempts = $this->config->item('login_count_attempts', 'syndicates');

                      $attemps_to_login = $this->attempts->get_attempts_num($ip_address, $username);

                      if ($login_count_attempts === $attemps_to_login) {
                          $this->users->ban($username); //suppend user account
                          echo json_encode(array('message' => 'account_suspended_now', 'status' => 'failed'));
                          //redirect('Auth/suspended');
                      }
                  }
              } else {
                  echo json_encode(array('message' => 'user_not_exist.', 'status' => 'failed'));
                  exit;
              }
    }

    public function process_auto_betting()
    {

    }

    public function process_topups_search(){

      $this->form_validation->set_rules('search_world','Keyword','required|mini-length[2]');
      if($this->form_validation->run() === false){
        echo json_encode(array('message'=>'search_failed','result'=>validation_errors(),'status'=>'form_error'));
        exit;
      }

      $search_keyword = $this->input->post();

    }

    public function topups(){

    }

    public function notification(){

    }

    public function activate_topups(){

    }

  }
