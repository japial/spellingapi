<?php

if(!function_exists('formatMobileDate')){
	function formatMobileDate($dateString)
	{
	    $dateParts = explode(":", $dateString);
	    $validDate = "";
	    foreach($dateParts as $key => $part){
	        if(intval($part) < 10){
	            $part = '0'.$part;
	        }
	        if($key == 0){
	            $validDate = $part;
	        }else{
	            $validDate = $validDate.'/'.$part;
	        }
	        
	    }
		return str_replace(' ', '', $validDate);
	}
}


if (!function_exists('send_spelling_bee_email')) {
    function send_spelling_bee_email($to_email, $subject, $body) {
        $from_email = 'info@teamworkbd.com';
        $password = 'infochamps21';
        $CI = & get_instance();
        $CI->load->library('email');
        $config = array(
            'protocol' => 'ssmtp', 
            'smtp_host' => 'ssmtp.gmail.com', 
            'smtp_port' => '465',
            'smtp_user' => $from_email,
            'smtp_pass' => $password,
            'wordwrap' => TRUE,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'smtp_crypto' => 'ssl',
        );
        $CI->email->initialize($config);
        $CI->email->set_mailtype("html");
        $CI->email->set_newline("\r\n");
        $CI->email->to($to_email);
        $CI->email->from($from_email, 'Spelling Bee Bangladesh');
        $CI->email->subject($subject);
        $CI->email->message($body);
        $CI->email->send();
        return TRUE;
    }
}

