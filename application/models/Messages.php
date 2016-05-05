<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of ewallet
 *
 * @author bright
 */
class Messages extends CI_Model {

  private $table_name = 'messages';

  public function __construct() {
      parent::__construct();

      $this->table_name = $this->config->item('db_table_prefix', 'syndicates') . $this->table_name;
  }

  public function set_message($username,$message){
    $this->db->set('username',$username);
    $this->db->set('date_created',date('d-m-Y'));
    $this->db->set('message',$message);
    $this->db->insert($this->db->table_name);
  }

  public function get_messages($username,$limit){
    $this->db->select('id,message,date_created');
    $this->db->where('username',$username);
    $this->db->limit($limit);
    $query = $this->db->get($this->table_name);
    $data = [];
    if($query->num_rows() > 0){
      $data = $query->result();
      $query->free_result();
    }

    return $data;
  }

  public function set_message_read($username,$message_id){
    $this->db->set('status','1');
    $this->db->where('username',$username);
    $this->db->where('id',$message_id);
    $this->db->update($this->table_name);
    if($this->db->affected_rows() > 0){
      return true;
    }

    return false;
  }

  public function get_all_messages($username){
    $query = $this->db->get_where($this->table_name,array('username'=>$username));
    $data = [];
    if($query->num_rows() > 0){
      $data = $query->result();
      $query->free_result();
    }
    return $data;
  }

  public function delete_messages($username){
    $this->db->where('username',$username);
    $this->db->delete($this->table_name);
    if($this->db->affected_rows() > 0){
      return true;
    }

    return false;
  }

  public function delete_message($message_id,$username){
    $this->db->where('id',$message_id);
    $this->db->where('username',$username);
    $this->db->limit(1);
    $this->db->delete($this->table_name);
    if($this->db->affected_rows() > 0){
      return true;
    }

    return false;
  }
  
 

  public function send_message_to_all($message){

  }
}
//end of file
