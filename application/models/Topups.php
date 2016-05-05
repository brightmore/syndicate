<?php defined('BASEPATH') OR exit('No direct script access allowed');


 /**
  *
  */
 class Topups extends CI_Model
 {

   private $table_name = 'topups';

   public function __construct() {
       parent::__construct();
       $this->table_name = $this->config->item('db_table_prefix', 'syndicates') . $this->table_name;
       $this->load->model('Ewallet');
       $this->load->model('user_profiles', 'user_prof');
       $this->load->model('Transaction','transLog');
   }

    public function add_topup($username,$Amount,$transaction_id)
    {
      $this->db->set('username',$username);
      $this->db->set('transaction_id',$transaction_id);
      $this->db->set('amount',$amount);
      $this->db->set('date_created',new Date('d-M-Y'));
      $this->db->insert($this->table_name);

      if($this->db->affected_rows() > 0){
        return true;
      }

      return false;
    }

    public function activate_topups($id){

      $data = $this->get_topup_info($id);
      $amount = $data->amount;
      $username = $data->username;
      $this->ewallet->credit_wallet($username, $amount);
      $this->delete_topup($id);
    }

    public function delete_topup($id)
    {
      $this->db->where('id',$id);
      $this->db->delete($this->table_name);
      if($this->db->affected_rows() > 0){
        return TRUE;
      }
      return FALSE;
    }

    public function get_topup_info($id){
      $this->db->select('amount,username');
      $this->db->where('id',$id);
      $this->db->order_by('id');
      $this->db->limit(1);
      $query = $this->db->get($this->table_name);
      $row = $query->row();
      return $row;
    }

    public function list_topups($limit)
    {
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->limit($limit);
        $query = $this->db->get();

        $data = [];
        if($query->num_rows() > 0){
          $data = $query->result();
          $query->free_result();
        }

        return $data;
    }

    public function search_topups($username){
      $this->db->select('*');
      $this->db->from('topups');
      $this->db->like('username', $username);
      $query = $this->db->get();
      $data = [];
      if($query->num_rows() > 0){
        $data = $query->result();
        $query->free_result();
      }

      return data;
    }
 }
