<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->config->load('http_status');
        $this->load->model('users');
        $this->load->library('form_validation');
        $this->config->load('form_validation');
    }

    public function login() {
        $status = $this->config->item('status');
        if ($_POST) {
            $password = $this->input->post('password');
            $username = $this->input->post('username');
            if ($password && $username) {
                $token = $this->users->login($username, $password);
                if ($token) {
                    $this->createFcmAndUserFcm($token);
                    $data['status'] = $status['success'];
                    $data['data'] = array('token' => $token);
                } else {
                    $data['status'] = $status['auth_failed'];
                }
            } else {
                $data['status'] = $status['validation_failed'];
                $data['data'] = array('error' => 'Email and Password Required');
            }
        } else {
            $data = $status['bad_request'];
        }
        echo json_encode($data);
    }

    public function register() {
        $status = $this->config->item('status');
        if ($_POST) {
            if ($this->form_validation->run('registration')) {
                $user_data = $this->set_user_register_data();
                $token = $this->users->create_user($user_data);
                if ($token) {
                    $this->createFcmAndUserFcm($token);
                    $data['status'] = $status['success'];
                    $data['data'] = array('token' => $token);
                } else {
                    $data['status'] = $status['auth_failed'];
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
    
    public function add_fcm() {
        $fcm_id = $this->input->post('fcm_id');
        $status = $this->config->item('status');
        if ($fcm_id) {
            $fcmexist = $this->db->select('id')->from('fcm')->where('fcm_id', $fcm_id)->get()->row();
            if ($fcmexist) {
                $fcmID = $fcmexist->id;
            }else{
                $this->db->insert('fcm', array("fcm_id" => $fcm_id));
                $fcmID = $this->db->insert_id();
            }
            $user_fcm = $this->createUserFcm($fcmID, $this->input->post('token'));
            $data['status'] = $status['success'];
            $data['data'] = array('user_fcm' => $user_fcm);
        } else {
            $data['status'] = $status['validation_failed'];
        }
        echo json_encode($data);
    }
    
    private function createFcmAndUserFcm($token){
        $fcm_id = $this->input->post('fcm_id');
        if($fcm_id){
            $fcmexist = $this->db->select('id')->from('fcm')->where('fcm_id', $fcm_id)->get()->row();
            if ($fcmexist) {
                $fcmID = $fcmexist->id;
            }else{
                $this->db->insert('fcm', array("fcm_id" => $fcm_id));
                $fcmID = $this->db->insert_id();
            }
            $this->createUserFcm($fcmID, encrypted_string($token));
        }
    }
    
    private function createUserFcm($fcmID, $token = NULL){
        if($token){
            $user_id = $this->users->check_user_token($token);
            if($user_id){
                $user_fcm['user_id'] = $user_id;
                $user_fcm['fcm_id'] = $fcmID;
                $this->db->insert("user_fcm", $user_fcm);
                return TRUE;
            }
        }
        return FALSE;
    }
    
    private function set_user_register_data(){
        $data['name'] = $this->input->post('name');
        $data['phone'] = $this->input->post('phone');
        $data['email'] = $this->input->post('email');
        $data['dob'] = formatMobileDate($this->input->post('dob'));
        $data['password'] = $this->input->post('password');
        $data['class'] = $this->input->post('class');
        $data['school'] = $this->input->post('school');
        $data['district'] = $this->input->post('district');
        $data['image'] = $this->upload_image();
        $data['mobile_app'] = 1;
        return $data;
    }
    
    private function upload_image() {       
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
            return $siteUrl.'/wp-content/uploads/'.'no-image.png';
        }
    }
    
    public function make_local_user_token_for_em_only_not_for_others() {
		$token = encrypted_string($this->input->post('token'));
        echo json_encode($token);
    }

}
