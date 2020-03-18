<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->config->load('http_status');
        $this->load->model('users');
        $this->load->model('leaderboard');
        $this->load->library('form_validation');
        $this->config->load('form_validation');
    }

    public function main() {
        $status = $this->config->item('status');
        $data['status'] = $status['success'];
        $data['data'] = array(
            'leaderboard' => $this->leaderboard->get_main_leaders()
        );
        echo json_encode($data);
    }

    public function divisions() {
        $status = $this->config->item('status');
        $data['status'] = $status['success'];
        $data['data'] = array(
            'divisions' => $this->leaderboard->get_divisions()
        );
        echo json_encode($data);
    }

    public function division_leaders() {
        $status = $this->config->item('status');
        if ($_POST) {
            if ($this->form_validation->run('division_leaders')) {
                $pageNo = $this->input->post('page');
                // $leaders = $this->leaderboard->get_leaders_by_division($this->input->post('division_id'), $pageNo);
                $school = $this->leaderboard->get_school_winners($this->input->post('division_id'));
                if($pageNo > 0){
                    $leaders = array();
                }else{
                    $leaders = $this->getJsonWinners($this->input->post('division_id'));
                }
                
                $data['status'] = $status['success'];
                $data['data'] = array(
                    'leaderboard' => $leaders,
                    'school' => $school,
                );
            } else {
                $data['status'] = $status['validation_failed'];
                $data['error'] = strip_tags(validation_errors());
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }

    public function profile() {
        $status = $this->config->item('status');
        if ($_POST) {
            $user_id = $this->users->check_user_token($this->input->post('token'));
            if ($user_id) {
                $profile = $this->users->get_player_info($user_id);
                $profileData = array();
                if($profile){
                    $ranking = $this->leaderboard->get_player_ranking($user_id);
                    $last_game = $this->leaderboard->get_player_last_game($user_id);
                    $profileData = array(
                        'profile' => $profile,
                        'last_game' => $last_game,
                        'ranking' => $ranking
                    );
                }
                $data['status'] = $status['success'];
                $data['data'] = $profileData;
                
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
    
    public function certificate() {
		$status = $this->config->item('status');
		if ($_POST) {
			$user_id = $this->users->check_user_token($this->input->post('token'));
			if ($user_id) {
				$data['status'] = $status['success'];
				$profile = $this->users->get_player_info($user_id);
				$siteUrl = str_replace("/api/","",base_url());
				$cert_level = $this->certificate_level($profile->level);
				$file_path = '../wp-content/uploads/certificates/'.$user_id.'-level-'.$cert_level.'.png';
				$generated = 0;
				if(is_file($file_path)){
				    $generated = 1;
				}
				$certificate_url = $siteUrl.'/wp-content/uploads/certificates/'.$user_id.'-level-'.$cert_level.'.png';
				$data['data'] = array(
					'generated' => $generated,
					'generate_url' => base_url().'board/generate_certificate/'.$user_id,
					'certificate' => $certificate_url
				);
			}else {
				$data['status'] = $status['auth_failed'];
			}
		} else {
			$data['status'] = $status['bad_request'];
		}
		echo json_encode($data);
	}

	public function generate_certificate($user_id){
		$data['player'] = $this->users->get_player_info($user_id);
		$cert_level = $this->certificate_level($data['player']->level);
		$siteUrl = str_replace("/api/","",base_url());
		$data['certificate_level'] = $cert_level;
		$data['user_id'] = $user_id;
		$data['certificate_path'] = $siteUrl.'/wp-content/uploads/certificates/'.$user_id.'-level-'.$cert_level.'.png';
		$this->load->view('certificate', $data);
	}

	public function save_certificate(){
		$dataImage = base64_decode($this->input->post('image_file'));
		$fileName = $this->input->post('file_name');
		file_put_contents('../wp-content/uploads/certificates/'.$fileName, $dataImage);
		echo 'Okay';
	}

	private function getJsonWinners($division = 1){
		if($division == 1){
			$path = base_url().'assets/winners/dhaka.json';
		}
		if($division == 2){
			$path = base_url().'assets/winners/chattagram.json';
		}
		if($division == 3){
			$path = base_url().'assets/winners/barishal.json';
		}
		if($division == 4){
			$path = base_url().'assets/winners/sylhet.json';
		}
		if($division == 5){
			$path = base_url().'assets/winners/rajshahi.json';
		}
		if($division == 6){
			$path = base_url().'assets/winners/khulna.json';
		}
		if($division == 7){
			$path = base_url().'assets/winners/rangpur.json';
		}
		$leaders = file_get_contents($path);
		return json_decode($leaders);
	}

	private function certificate_level($level = 1){
		if($level == 10){
			$cert_level = $level;
		}else{
			$cert_level = intval($level) - 1;
		}
		return $cert_level;
	}
	
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

}
