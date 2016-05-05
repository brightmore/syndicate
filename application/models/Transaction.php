<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of transaction
 *
 * @author bright
 */
class Transaction extends CI_Model{
    private $table_name = 'transaction_log';
    private $ci = null;
    private $transaction =[
        'placing_betting',
        'credit_acccount',
        'company_paying_you',
        'premium_membership',
        'sms',
        'debit_account',
        'supended_account'
    ];
    public function __construct() {
        parent::__construct();
        $this->ci =& get_instance();
        $this->table_name = $this->ci->config->item('db_table_prefix', 'syndicates').$this->table_name;
    }
    
    public function set_log($username,$log_type,$description,$amount){
        
        if(! in_array($log_type, $this->transaction)){
            return FALSE;
        }
        
        $data['date_created'] = date('d M Y H:s:i');
        $data['username'] = $username;
        $data['log_type'] = $log_type;
        $data['description'] = $description;
        $data['amount'] = $amount;
        
        $this->ci->db->insert($this->table_name,$data);
        
        if($this->ci->db->affected_rows() > 0){
            return TRUE;
        }
        return false;
    }
    
  
    public function get_user_logs($username,$limit){
        $this->ci->db->select('*');
        $this->ci->db->where('LOWER(username)',  strtolower($username));
        $this->ci->db->limit($limit);
        $query = $this->ci->db->get($this->table_name);
        
        $data = null;
        if($query->num_rows() > 0){
            $data = $query->result();
            $query->free_result();
        }
        
        return $data;
    }
    
    public function get_user_logs_type($username,$log_type){
        
        $this->ci->db->select('*');
        $this->ci->db->where('LOWER(username)',  strtolower($username));
        $this->ci->db->where('LOWER(log_type)',  strtolower($log_type));
        $query = $this->ci->db->get($this->table_name);
        
        $data = null;
        if($query->num_rows() > 0){
            $data = $query->result();
            $query->free_result();
        }
        
        return $data;
    }
    
}
