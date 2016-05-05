<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of My_Form_validation
 *
 * @author bright
 */
class MY_Form_validation extends CI_Form_validation {

    public function error_array() {
        return $this->_error_array;
    }

}
