<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of preferences
 *
 * @author bright
 */
class Preferences extends CI_Model{
    private $table_name = 'preferences';
    public function __construct() {
        parent::__construct();
        $ci = & get_instance();
        $this->table_name = $ci->config->item('db_table_prefix', 'syndicates') . $this->table_name;
    }
    
    public function get_value($name){
        $this->db->select('value');
        $this->db->where('LOWER(name)',strtolower($name));
        $query = $this->db->get($this->table_name);
        
        return $query->row()->value;
    }
}