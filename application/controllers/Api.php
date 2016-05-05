<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of API
 *
 * @author bright
 */
class API extends REST_Controller{
    
    private $username;
    private $password;

    public function __construct($config = 'rest') {
        parent::__construct($config);
    }
    
    function _perform_library_auth($username,$password){
        $this->password = $password;
        $this->username = $username;
    }
    
    function _check_login($username = NULL, $password = FALSE) {
        parent::_check_login($username, $password);
    }
}
