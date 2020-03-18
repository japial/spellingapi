<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->config->load('http_status');
        $this->load->model('users');
        $this->load->library('form_validation');
        $this->config->load('form_validation');
    }
    
    public function review() {
        $status = $this->config->item('status');
        if ($_POST) {
            $user_id = $this->users->check_user_token($this->input->post('token'));
            if ($user_id) {
                $review = $this->input->post('review');
                if($review){
                    $this->db->insert('sb_user_reviews',array('user_id' => $user_id, 'review' => $review));
                    $data['status'] = $status['success'];
                }else{
                    $data['status'] = $status['validation_failed'];
                }
            } else {
                $data['status'] = $status['auth_failed'];
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }
    
    public function update_password() {
        $status = $this->config->item('status');
        if ($_POST) {
            $user_id = $this->users->check_user_token($this->input->post('token'));
            if ($user_id) {
                if($this->users->check_current_password($user_id, $this->input->post('password'))){
                    $this->db->update('sb_users', array('user_pass_mobile' => md5($this->input->post('new_password'))), array('id' => $user_id));
                    $data['status'] = $status['success'];
                }else{
                    $data['status'] = $status['validation_failed'];
                }
            } else {
                $data['status'] = $status['auth_failed'];
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }
    
    public function update_profile() {
        $status = $this->config->item('status');
        if ($_POST) {
            $user_id = $this->users->check_user_token($this->input->post('token'));
            if ($user_id) {
                if ($this->form_validation->run('update_profile')) {
                    $error_message = $this->updateUserData($user_id);
                    $this->updateUserInfo($user_id);
                    if($error_message){
                        $data['status'] = $status['validation_failed'];
                        $data['data'] = array('error' => $error_message);
                    }else{
                        $data['status'] = $status['success'];
                    }
                }else{
                    $data['status'] = $status['validation_failed'];
                    $data['data'] = array('error' => strip_tags(validation_errors()));
                }
            } else {
                $data['status'] = $status['auth_failed'];
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }
    
    public function password_reset_email() {
        $status = $this->config->item('status');
        if ($_POST) {
            $phone = $this->input->post('phone');
            if ($phone) {
                $email = $this->users->get_user_email($phone);
                if ($email == 'UNAUTH'){
                    $data['status'] = $status['auth_failed'];
                }else {
                    if($email){
                        $this->sendPasswordResetEmail($email, $phone);
                    }
                   $data['status'] = $status['success'];
                   $data['data'] = array('email' => $email);
                }
            } else {
                $data['status'] = $status['validation_failed'];
                $data['data'] = array('error' => 'Phone Number Required');
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }
    
    public function password_reset_request() {
        $status = $this->config->item('status');
        if ($_POST) {
            if ($this->form_validation->run('password_reset_request')) {
                $user_id = $this->users->valid_user_info($this->input->post('phone'), formatMobileDate($this->input->post('dob')), $this->input->post('class'));
                if($user_id){
                    $this->db->update('sb_users', array('user_email' => $this->input->post('email')), array('id' => $user_id));
                    $this->sendPasswordResetEmail($this->input->post('email'), $this->input->post('phone'));
                    $data['status'] = $status['success'];
                }else {
                   $data['status'] = $status['auth_failed'];
                   $data['data'] = array('error' => "Information Not Matched!");
                }
            } else {
                $data['status'] = $status['validation_failed'];
                $data['data'] = array('error' => strip_tags(validation_errors()));
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }
    
    //Web Methods Start
    public function reset_password(){
        $phone = $this->decrypt_phone($this->input->get('key'));
        $user = NULL;
        if ($phone) {
            $user = $this->db->get_where('sb_users', array('user_login' => $phone))->row();
        }
        if($user){
            $data['key'] = $this->encrypt_phone($phone);
            $this->load->view('email/change_password', $data);
        }else{
            $this->load->helper('url');
            redirect('http://spellingbee.champs21.com', 'refresh');
        }
    }
    
    public function change_password(){
        $phone = $this->decrypt_phone($this->input->post('key'));
        $user = NULL;
        if ($phone) {
            $user = $this->db->get_where('sb_users', array('user_login' => $phone))->row();
        }
        if($user){
             if($this->form_validation->run('change_password')) {
                $this->db->update('sb_users', array('user_pass_mobile' => md5($this->input->post('password')), 'user_pass' => ''), array('id' => $user->ID));
                redirect('http://spellingbee.champs21.com', 'refresh');
             }else{
                $data['key'] = $this->encrypt_phone($phone);
                $this->load->view('email/change_password', $data); 
             }
        }else{
            $this->load->helper('url');
            redirect('http://spellingbee.champs21.com', 'refresh');
        }
    }
    //Web Methods End
	
	private function updateUserData($user_id){
        $error_message = NULL;
        $email = $this->input->post('email');
        $profile = $this->users->get_player_info($user_id);
        if($email == $profile->email){
             $this->db->update('sb_users', array('display_name' => $this->input->post('name')), array('id' => $user_id));
        }else{
            $exist = $this->db->select('id')->from('sb_users')->where('user_email', $email)->get()->row();
            if($exist){
                $error_message = "Email Already Exist";
            }else{
                $this->db->update('sb_users', array('user_email' => $email, 'display_name' => $this->input->post('name')), array('id' => $user_id));
            }
        }
        return $error_message;
    }
    
    private function updateUserInfo($user_id){
        $image = $this->update_profile_image();
        $data['name'] = $this->input->post('name');
        $data['class'] = $this->input->post('class');
        $data['dob'] = formatMobileDate($this->input->post('dob'));
        if($image){
            $data['image'] = $image;
        }
        $this->db->update('sb_user_infos', $data, array('user_id' => $user_id));
    }
    
	private function update_profile_image() {       
        $config = array(
            'upload_path' => '../wp-content/uploads',
            'allowed_types' => "jpg|png|jpeg",
            'overwrite' => TRUE,
            'max_size' => "8048",
            'encrypt_name' => TRUE
        );
        $this->load->library('upload', $config);
        $siteUrl = str_replace("/api/","",base_url());
        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();
            return $siteUrl.'/wp-content/uploads/'.$data['file_name'];
        } else {
            return FALSE;
        }
    }
    
    private function sendPasswordResetEmail($email, $phone){
        $link = base_url()."profile/reset_password?key=".$this->encrypt_phone($phone);
        $emailData['subject'] = "Password Reset Request";
        $emailData['body'] = "Use this link to reset your password: <a href='".$link."'>Reset Link</a>";
        $body = $this->load->view('email/password_reset', $emailData, true);
        send_spelling_bee_email($email, $emailData['subject'], $body);
    }
    
    private function encrypt_phone($phone){
        return urlencode(encrypted_string($phone));
    }
    
    private function decrypt_phone($key){
        return decrypted_string(urldecode($key));
    }
    
}
