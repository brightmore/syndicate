<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Users extends CI_Model
{
	private $table_name = 'membership';			// user accounts
	private $profile_table_name = 'user_profiles';	// user profiles

	function __construct()
	{
		parent::__construct();

		$ci =& get_instance();
		$this->table_name		= $ci->config->item('db_table_prefix', 'syndicates').$this->table_name;
		$this->profile_table_name	= $ci->config->item('db_table_prefix', 'syndicates').$this->profile_table_name;
	}

	/**
	 * Get user record by Id
	 *
	 * @param	int
	 * @param	bool
	 * @return	object
	 */
	function get_user_by_id($user_id, $activated)
	{
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	

	/**
	 * Get user record by username
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_username($username)
	{
                $this->db->select('*');
                $this->db->where('LOWER(username)=', strtolower($username));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email)
	{
		$this->db->where('LOWER(email)=', strtolower($email));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Check if username available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_user_available($username)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(username)=', strtolower($username));

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Check if email available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(email)=', strtolower($email));
//		$this->db->or_where('LOWER(new_email)=', strtolower($email));

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Create new user record
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	function create_user($data, $activated = TRUE)
	{
//		$data['date_created'] = date('d-m-Y H:i:s');
		$data['suspended'] = $activated ? '0' : '1';
                $data['active'] = 1;

		if ($this->db->insert($this->table_name, $data)) {
			$user_id = $this->db->insert_id();
                        if ($activated)	{
                            $this->create_profile($user_id);
                        }
			return array('user_id' => $user_id);
		}
		return NULL;
	}
        
        /*
         * @param string 
         * return object
         */
        function get_user_by_group($type='standard',$offset=0,$limit=50){
            $this->db->where('LOWER(type)',  strtolower($type));
        }
        

	/**
	 * Activate user if activation key is valid.
	 * Can be called for not activated users only.
	 *
	 * @param	int
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function activate_user($user_id, $activation_key, $activate_by_email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		if ($activate_by_email) {
			$this->db->where('new_email_key', $activation_key);
		} else {
			$this->db->where('new_password_key', $activation_key);
		}
		$this->db->where('activated', 0);
		$query = $this->db->get($this->table_name);

		if ($query->num_rows() == 1) {

			$this->db->set('activated', 1);
			$this->db->set('new_email_key', NULL);
			$this->db->where('id', $user_id);
			$this->db->update($this->table_name);

			$this->create_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Purge table of non-activated users
	 *
	 * @param	int
	 * @return	void
	 */
	function purge_notActiveUser($expire_period = 172800)
	{
		$this->db->where('activated', 0);
		$this->db->where('UNIX_TIMESTAMP(date_created) <', time() - $expire_period);
		$this->db->delete($this->table_name);
	}

	/**
	 * Delete user record
	 *
	 * @param	int
	 * @return	bool
	 */
	function delete_user($username)
	{
		$this->db->where('username', $username);
		$this->db->delete($this->table_name);
		if ($this->db->affected_rows() > 0) {
			//remove all staff registered devices
                        $this->device->delete($username);
			return TRUE;
		}
		return FALSE;
	}
        
        

	/**
	 * Set new password key for user.
	 * This key can be used for authentication when resetting user's password.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function set_password_key($username, $new_pass_key)
	{
		$this->db->set('new_password_key', $new_pass_key);
		$this->db->set('new_password_requested', date('d-m-Y H:i:s'));
		$this->db->where('username', $username);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Check if given password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	function can_reset_password($username, $new_pass_key, $expire_period = 900)
	{
		$this->db->select('1', FALSE);
		$this->db->where('username', $username);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 1;
	}

	/**
	 * Change user password if password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	function reset_password($username, $new_pass, $new_pass_key, $expire_period = 900)
	{
		$this->db->set('password', $new_pass);
		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);
		$this->db->where('username', $username);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Change user password
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function change_password($username, $new_pass)
	{
		$this->db->set('password', $new_pass);
		$this->db->where('username', $username);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Set new email for user (may be activated or not).
	 * The new email cannot be used for login or notification before it is activated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function set_new_email($username, $new_email, $new_email_key, $activated)
	{
		$this->db->set($activated ? 'new_email' : 'email', $new_email);
		$this->db->set('new_email_key', $new_email_key);
		$this->db->where('username', $username);
		$this->db->where('activated', $activated ? 1 : 0);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Activate new email (replace old email with new one) if activation key is valid.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function activate_new_email($username, $new_email_key)
	{
		$this->db->set('email', 'new_email', FALSE);
		$this->db->set('new_email', NULL);
		$this->db->set('new_email_key', NULL);
		$this->db->where('username', $username);
		$this->db->where('new_email_key', $new_email_key);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	function update_login_info($username, $record_ip, $record_time)
	{
		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);

		if ($record_ip)		$this->db->set('last_ip', $this->input->ip_address());
		if ($record_time)	$this->db->set('last_login', date('Y-m-d H:i:s'));

		$this->db->where('username', $username);
		$this->db->update($this->table_name);
	}

	/**
	 * Ban user
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function ban_user($username)
	{
		$this->db->where('username', $username);
		$this->db->update($this->table_name, array(
			'suspended'		=> 1
		));
	}
        
        /*
         * @param string
         * @return bool
         */
        function is_suspended($username){
            $this->db->select("username");
            $this->db->where('username',$username);
            $this->db->where('suspended','1');
            $row = $this->db->get($this->table_name);
            
            if($row->num_rows() > 0){
                return TRUE;
            }
            
            return false;
           
        }

	/**
	 * Unban user
	 *
	 * @param	int
	 * @return	void
	 */
	function unban_user($username)
	{
		$this->db->where('username', $username);
		$this->db->update($this->table_name, array(
			'suspended'		=> 0
		));
	}

	/**
	 * Create an empty profile for a new user
	 *
	 * @param	int
	 * @return	bool
	 */
	private function create_profile($username)
	{
		$this->db->set('username', $username);
		return $this->db->insert($this->profile_table_name);
	}

	/**
	 * Delete user profile
	 *
	 * @param	int
	 * @return	void
	 */
	private function delete_profile($username)
	{
		$this->db->where('user_id', $username);
		$this->db->delete($this->profile_table_name);
	}
        
        /**
         * Select users with sms activated
         * @return array 
         */
        
        public function users_with_sms_activated(){
            $query = "SELECT username,phone FROM $this->table_name AS u INNER JOIN $this->profile_table_name AS p ON u.username = p.user_id WHERE sms = 1";
            $result = $this->db->query($query);
            $data = [];
            if($result->num_rows() > 0){
                foreach ($result->result() as $value) {
                    $data[$value->username] = $value->phone;
                }
                
                $result->free_result();
                return $data;
            }
            
            return $data;
        }
        
        /**
         * @param String
         * @return String
         */
        public function get_user_password($username){
            $this->db->select('password');
            $this->db->where('username',$username);
            $this->db->from($this->table_name);
            $password = $this->db->get()->row()->password;
            return $password;
        }
        
        public function get_user_phone($username){
            $this->db->select('phone');
            $this->db->where('username',$username);
            $this->db->from($this->table_name);
            $phone = $this->db->get()->row()->phone;
            return $phone;
        }
        
        public function isPremium($username){
            $this->db->select("isPremium");
            $this->db->from($this->table_name);
            $this->db->where('username',$username);
            $this->db->where('isPremium','1');
            $row = $this->db->get();
            if($row->num_rows()  == 1){
                return TRUE;
            }
            
            return FALSE;
        }
        
        public function set_premium_membership($username){
            
            $this->db->set('isPremium','1');
            $this->db->where('username',$username);
            $this->db->update($this->table_name);
            if($this->db->affected_rows() > 0){
                return TRUE;
            }
            return false;
            
        }
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */