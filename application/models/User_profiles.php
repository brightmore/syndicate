<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User_Profiles
 *
 * @author bright
 */
class User_profiles extends CI_Model{
    
    private $table_name = "user_profiles";

    public function __construct() {
        parent::__construct();
        $this->table_name = $this->config->item('db_table_prefix','syndicates').$this->table_name;
    }
    
    public function is_sms_activated($username){
        $this->db->select('SELECT 1',false);
        $this->db->where('LOWER(username)',$username);
        $this->db->where('sms','1');
        $query = $this->db->get($this->table_name);
        if($query->num_rows() > 0){
            return true;
        }
        
        return false;
    }
    
    public function activate_sms($username){
        $this->db->limit(1);
        $this->db->update($this->table_name,['sms'=>1],['username'=>$username]);
        if($this->db->affected_rows() === 1){
            return TRUE;
        }else{
            return false;
        }
        
    }
    
    public function deactivate_sms($username){
        $this->db->limit(1);
        $this->db->where('username',$username);
        $this->db->update($this->table_name,['sms'=>0]);
        if($this->db->affected_rows() === 1){
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function isAutoBettingOn($username,$amount){
        $this->db->select('1',false);
        $this->db->where('username',$username);
        $query = $this->db->get($this->table_name);
        if($query->num_rows() > 0){
            return TRUE;
        }
        
        return false;
    }
    
    public function enableBetHead($username,$amount){
         
        if(! $this->isAutoBettingOn($username)){ // if it is not enable then enable it
            $this->db->set('bet_head_amount',$amount);
            $this->db->set('bet_head',1);
            $this->db->where('username',$username);
            $this->db->update($this->table_name);
            
            if($this->db->affected_rows() > 0){
                return TRUE;
            }
        }
        
        return false;
    }
    
    function deactivate_autobetting($username){
        $this->db->limit(1);
        $this->db->where('username',$username);
        $this->db->update($this->table_name,['bet_head'=>0]);
        if($this->db->affected_rows() === 1){
            return TRUE;
        }
        
        return FALSE;
    }
    
     public function activate_autobetting($username){
        $this->db->limit(1);
        $this->db->update($this->table_name,['bet_head'=>1],['username'=>$username]);
        if($this->db->affected_rows() === 1){
            return TRUE;
        }else{
            return false;
        }
        
    }
}
