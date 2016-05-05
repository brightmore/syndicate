<?php

function random_str($length = 8){

$RandomString = substr(str_shuffle(md5(time())), 0, $length);
return $RandomString;
}

function __isString($value){
    if ( isset( $value ) && $value !== NULL ) {
        return TRUE;
    }
    return false;
}

 function _get_csrf_nonce() {
       $CI =& get_instance();

        $CI->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $CI->session->set_flashdata('csrfkey', $key);
        $CI->session->set_flashdata('csrfvalue', $value);

        $data = array(
            'type'  => 'hidden',
            'name'  => $key,
            'id'    => 'csrf',
            'value' => $value,
        );


        return $data;
    }

  function track_money($amount){
     $CI =& get_instance();

     $CI->db->set('amount',$amount);
     $CI->db->set('date_created',date('d-M-Y'));
     $CI->db->set('time',date('H:i:s'));
     $CI->db->inset('track_money');
  }

 function _valid_csrf_nonce() {

     $CI =& get_instance();

        if ($CI->input->post($CI->session->flashdata('csrfkey')) !== FALSE &&
                $CI->input->post($CI->session->flashdata('csrfkey')) == $CI->session->flashdata('csrfvalue')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


function send_sms($message,$phone){
            $OurSenderID = "Eagle_Eye";
            $ClientId = "wjtighps";
            $ClientSecret = "jatdyeic";
            $RegisteredDelivery = "true";
            $tomobile = urlencode($phone);
            $message1 = urlencode($message);

            $url = "https://api.smsgh.com/v3/messages/send?From=" . $OurSenderID
                . "&To=" . $tomobile
                . "&Content=" . $message1
                . "&ClientReference=1234"
                . "&ClientId=" . $ClientId
                . "&ClientSecret=" . $ClientSecret
                . "&RegisteredDelivery=" . $RegisteredDelivery;
                //Fire the request and wait for the response
                $response = file_get_contents($url);

}

function exportCSV($result){

    $CI =& get_instance();

        $CI->load->dbutil();
        $CI->load->helper('file');
        $CI->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = date().".csv";

        $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
        force_download($filename, $data);

}

function get_lottery_name($day){

    $data['monday'] = 'Monday Special';
    $data['tuesday'] = 'Lucky Tuesday';
    $data['wednesday'] = 'Midweek Special';
    $data['thursday'] = 'Fortune Thursday';
    $data['friday'] = 'Friday Bonanza';
    $data['saturday'] = 'National Weekly Lotto';

    return $data[strtolower($day)];
}

function valid_money($input){
    if(is_double($input) || (filter_var($input, FILTER_VALIDATE_INT) !== false)){
        return TRUE;
    }
//
//    if  (preg_match('/^[+\-]?\d+(\.\d+)?$/', $input)){
//        return TRUE;
//    }
    return FALSE;
}

function is_suspended(){

    $CI =& get_instance();
    if($CI->session->userdata('suspended')){
        return TRUE;
    }

}

function is_login(){
    $CI =& get_instance();
    if($CI->session->userdata('login') && $CI->session->userdata('username')){
        return TRUE;
    }

    return FALSE;
}

function failureSession(){
    $CI =& get_instance();
    if($CI->session->flashdata('failure')){
        echo "<div class='alert alert-danger'>".$CI->session->flashdata('failure')."</div>";
    }
}

function successSession(){
    $CI =& get_instance();
    if($CI->session->flashdata('success')){
        echo "<div alert class='alert-success'>".$CI->session->flashdata('success')."</div>";
    }
}

function regions(){
     $region_list = [
         'Greater Accra'=>'Greater Accra Region',
         'Ashanti'=>'Ashanti Region',
         'Brong Ahafo'=>'Brong Ahafo Region',
         'Central'=>'Central Region',
         'Eastern'=>'Eastern Region',
         'Western'=>'Western Region',
         'Northern'=>'Northern Region',
         'Upper West'=>'Upper West Region',
         'Upper East'=>'Upper East Region',
         'volta'=>'Volta Region'
         ];

         return $region_list;
}
