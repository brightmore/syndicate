<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Login_attempts
 *
 * This model serves to watch on all attempts to login on the site
 * (to protect the site from brute-force attack to user database)
 *
 * 
 * 
 */
class Login_attempts extends CI_Model
{
	private $table_name = 'login_attempts';

	function __construct()
	{
		parent::__construct();

		$ci =& get_instance();
		$this->table_name = $ci->config->item('db_table_prefix', 'elearning').$this->table_name;
	}

	/**
	 * Get number of attempts to login occured from given IP-address or username
	 *
	 * @param	string
	 * @param	string
	 * @return	int
	 */
	function get_attempts_num($ip_address, $username)
	{
		$this->db->select('1', FALSE);
		$this->db->where('ip_address', $ip_address);
		if (strlen($username) > 0) $this->db->or_where('username', $username);

		$qres = $this->db->get($this->table_name);
		return $qres->num_rows();
	}

	/**
	 * Increase number of attempts for given IP-address and login
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	function increase_attempt($ip_address, $username)
	{
		$this->db->insert($this->table_name, array('ip_address' => $ip_address, 'username' => $username));
	}

	/**
	 * Clear all attempt records for given IP-address and login.
	 * Also purge obsolete login attempts (to keep DB clear).
	 *
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	function clear_attempts($ip_address, $username, $expire_period = 86400)
	{
		$this->db->where(array('ip_address' => $ip_address, 'username' => $username));

		// Purge obsolete login attempts
		$this->db->or_where('UNIX_TIMESTAMP(time) <', time() - $expire_period);

		$this->db->delete($this->table_name);
	}
}

/* End of file login_attempts.php */
/* Location: ./application/models/auth/login_attempts.php */