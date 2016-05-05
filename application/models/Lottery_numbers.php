<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LotteryNumbers
 *
 * @author bright
 */
class Lottery_numbers extends CI_Model {
    
    private $table = "lotteryNumbers";
    public function __construct() {
        parent::__construct();
    }
    
    function isCurrentNumberViewable(){
        $this->db->select('MAX(id) as id');
        $this->db->from($this->table);
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            $max_id = $query->row()->id;
            
            $this->db->select('numbers',FALSE);
            $this->db->limit(1);
            $this->db->where('id',$max_id);
            $this->db->where('viewable','1');
            $query = $this->db->get($this->table);
            
             $numbers = $query->row()->numbers;
            
            $xx = substr($numbers,0,2); //we are checking for this xx-xx-xx-xx-xx-xx
            
            if($xx === 'XX'){
                return TRUE;
            }
            
            return false;
        }
        
        return FALSE;
    }
    
    function currentNumber(){
       
        $this->db->select('*');
        $this->db->limit(1);
        $this->db->from($this->table);
        $this->db->where('viewable','1');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        
        return $query->row();
        
    }
    
    function get_draws($limit){
        $this->db->select('*');
        $this->db->limit($limit);
        $this->db->from($this->table);
        $this->db->where('viewable','1');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        
        $data = [];
        if($query->num_rows() > 0){
            $data = $query->result();
            $query->free_result();
            
            if($this->isCurrentNumberViewable()){
               unset($data[0]);  //remove the current number from the list
            }
            
        }
        
        return $data;
    }
}
