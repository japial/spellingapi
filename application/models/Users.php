<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Users extends CI_Model {

    public function login($username, $password) {
        $user_data = $this->db->get_where('sb_users', array('user_login' => $username))->row();
        if ($user_data) {
            $encripted_password = md5($password);
            if ($encripted_password == $user_data->user_pass_mobile) {
                $token = $this->createUserKey($user_data->ID);
                return $token;
            }
        }
        return false;
    }

    public function create_user($user_data) {
        if (!empty($user_data)) {
            $encrypted_password = md5($user_data['password']);
            $new_user['user_login'] = $user_data['phone'];
            $new_user['user_pass_mobile'] = $encrypted_password;
            $new_user['user_nicename'] = $user_data['phone'];
            $new_user['user_email'] = $user_data['email'];
            $new_user['display_name'] = $user_data['name'];
            $new_user['user_registered '] = date("Y-m-d H:i:s");
            $new_user['mobile '] = 1;
            $this->db->insert('sb_users', $new_user);
            $user_id = $this->db->insert_id();
            if ($user_id) {
                $metaString = 'a:1:{s:10:"subscriber";b:1;}';
                $this->db->insert('sb_usermeta', array( 'user_id' => $user_id, 'meta_key'=> 'sb_capabilities', 'meta_value' => $metaString));
                $division = $this->get_district_division($user_data['district']);
                $this->createUserInfo($user_id, $user_data, $division);
                $this->create_new_player($user_id, $division);
                $token = $this->createUserKey($user_id);
                return $token;
            }
        }
        return false;
    }
    
    public function check_user_token($token = NULL) {
        if ($token) {
			$token = decrypted_string($token);
            $user_key = $this->db->get_where('sb_user_keys', array('token' => $token, 'status' => 1))->row();
            if ($user_key) {
                return $user_key->user_id;
            }
        }
        return false;
    }
    
    public function get_player_info($user_id) {
        $this->db->select('sb_users.id, sb_users.user_email as email, sb_user_infos.name, sb_user_infos.school, sb_user_infos.class as class_name, sb_user_infos.image, sb_user_infos.dob, sb_user_infos.division_id, sb_divisions.name as division_name, sb_game_level.level, sb_game_start.score, sb_game_start.attempt, sb_game_start.time');
        $this->db->from('sb_users');
        $this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_users.id', 'inner');
        $this->db->join('sb_game_start', 'sb_game_start.user_id=sb_users.id', 'inner');
		$this->db->join('sb_game_level', 'sb_game_level.id=sb_game_start.level', 'inner');
        $this->db->join('sb_divisions', 'sb_divisions.id=sb_user_infos.division_id', 'left');
        $this->db->where('sb_users.id', $user_id);
        return $this->db->get()->row();
    }
    
    public function get_user_email($phone = NULL) {
        $user_data = $this->db->get_where('sb_users', array('user_login' => $phone))->row();
        if($user_data){
            return $user_data->user_email;
        }
        return 'UNAUTH';
    }
    
    public function check_current_password($user_id, $password) {
        $user_data = $this->db->get_where('sb_users', array('id' => $user_id))->row();
        if($user_data->user_pass_mobile == md5($password)){
            return TRUE;
        }
        return FALSE;
    }
    
    public function valid_user_info($phone = 0, $dob = 0, $className = 0) {
        $user_info = $this->db->get_where('sb_user_infos', array('phone' => $phone, 'dob' => $dob, 'class' => $className))->row();
        if($user_info){
            return $user_info->user_id;
        }
        return FALSE;
    }

    private function createUserInfo($user_id, $user_data, $division = 1) {
        $info['user_id'] = $user_id;
        $info['name'] = $user_data['name'];
        $info['phone'] = $user_data['phone'];
        $info['dob'] = $user_data['dob'];
        $info['class'] = $user_data['class'];
        $info['school'] = $user_data['school'];
        $info['district'] = $user_data['district'];
        $info['division_id'] = $division;
        $info['image'] = $user_data['image'];
        $info['mobile_app'] = 1;
        $this->db->insert('sb_user_infos', $info);
    }

    private function createUserKey($user_id) {
        $this->db->update("sb_user_keys", array('status' => 0), array('user_id' => $user_id));
        $data['user_id'] = $user_id;
        $data['token'] = generateRandomString(32) . md5($user_id);
        $this->db->insert("sb_user_keys", $data);
        return $data['token'];
    }
    
    private function get_district_division($district) {
        $dist = $this->db->get_where("sb_districts", array('name' => $district))->row();
        if($dist->division_id){
            return $dist->division_id;
        }else{
            return 1;
        }
    }

    private function create_new_player($user_id, $division = 1) {
        $player['user_id'] = $user_id;
        $player['score'] = 0;
        $player['level'] = 1;
        $player['division_id'] = $division;;
        $player['start_date'] = date("Y-m-d H:i:s");
        $this->db->insert('sb_game_start', $player);
    }
    
}
