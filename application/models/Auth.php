<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Auth
 *
 * @author bright
 */
class Auth extends CI_Model{
    //put your code here
    
    private $table = 'members';
    private $ci = null;
    public function __construct() {
        parent::__construct();
        
        $this->ci =& get_instance();
        
    }
    
    function login($login, $password, $remember, $login_by_username, $login_by_email)
	{
		if ((strlen($login) > 0) AND (strlen($password) > 0)) {

			if (!is_null($user = $this->users->$get_user_func($login))) {	// login ok

				// Does password match hash in database?
				$hasher = new PasswordHash(
						$this->config->item('phpass_hash_strength', 'voucher'),
						$this->config->item('phpass_hash_portable', 'voucher'));
				if ($hasher->CheckPassword($password, $user->password)) {		// password ok

					
				} else {														// fail - wrong password
					$this->increase_login_attempt($login);
					$this->error = array('password' => 'auth_incorrect_password');
				}
			} else {															// fail - wrong login
				$this->increase_login_attempt($login);
				$this->error = array('login' => 'auth_incorrect_login');
			}
		}
		return FALSE;
	}

        
        /**
	 * Create new user on the site and return some data about it:
	 * user_id, username, password, email, new_email_key (if any).
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	array
	 */
	function create_user($username, $email, $password,$type)
	{
		if ((strlen($username) > 0) AND !$this->users->is_username_available($username)) {
			$this->error = array('username' => 'auth_username_in_use');

		}  else {
			// Hash password using phpass
			$hasher = new PasswordHash(
					$this->config->item('phpass_hash_strength', 'Bvoucher'),
					$this->config->item('phpass_hash_portable', 'Bvoucher'));
			$hashed_password = $hasher->HashPassword($password);

			$data = array(
				'username'	=> $username,
				'password'	=> $hashed_password,
				'email'		=> $email,
				'last_ip'	=> $this->input->ip_address(),
			);

			if (!is_null($res = $this->users->create_user($data, !$email_activation))) {
				$data['id'] = $res['id'];
				$data['password'] = $password;
				unset($data['last_ip']);
				return $data;
			}
		}
		return NULL;
	}

        
        /**
	 * Check if login attempts exceeded max login attempts (specified in config)
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_max_login_attempts_exceeded($login)
	{
		if ($this->ci->config->item('login_count_attempts', 'syndicates')) {
			$this->ci->load->model('login_attempts');
			return $this->ci->login_attempts->get_attempts_num($this->ci->input->ip_address(), $login)
					>= $this->ci->config->item('login_max_attempts', 'syndicates');
		}
		return FALSE;
	}

	/**
	 * Increase number of attempts for given IP-address and login
	 * (if attempts to login is being counted)
	 *
	 * @param	string
	 * @return	void
	 */
	private function increase_login_attempt($username)
	{
		if ($this->ci->config->item('login_count_attempts', 'syndicates')) {
			if (!$this->is_max_login_attempts_exceeded($username)) {
				$this->ci->load->model('login_attempts');
				$this->ci->login_attempts->increase_attempt($this->ci->input->ip_address(), $username);
			}
		}
	}

	/**
	 * Clear all attempt records for given IP-address and login
	 * (if attempts to login is being counted)
	 *
	 * @param	string
	 * @return	void
	 */
	private function clear_login_attempts($username)
	{
		if ($this->ci->config->item('login_count_attempts', 'syndicates')) {
			$this->ci->load->model('login_attempts');
			$this->ci->login_attempts->clear_attempts($this->ci->input->ip_address(),$username,
			$this->ci->config->item('login_attempt_expire', 'syndicates'));
		}
	}
    
    function isBlock($username,$password){
        $query = $this->db->query('SELECT * FROM ? WHERE (name = ? AND password = ?) AND block = 0',[$username,$password]);
        if($query->now_rows() > 0){ 
            return false;
        }else{
            return TRUE;
        }
    }
    
    
    function changePassword($username,$newpassword,$oldpassword){
        
         //check if the old password exist
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where(array('username'=>$username,'password'=>  sha1($oldpassword)));
        $query = $this->db->get();
        
        if($query->num_rows() == 0){ // if there is no match
            return -1;
        }
            
        $this->db->update($this->table,array('password'=>  sha1($newpassword)),array('username'=>$username));
        if($this->db->affected_rows() > 0){
            return 1;
        }
        
        return 2;
        
    }
    
    function forgotPassword_get($username){
        
    }
    
    
    function block($user_id){
        $update = array();
        $where = array('id'=>$user_id);
       
        $this->db->update($this->table,$update,$where);
        if($this->db->affected_rows() > 0){
         return TRUE;   
        }
        
        return FALSE;
    }
    
}
