<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends CI_Controller {

    public $online = 0;
    
    public function __construct() {
        parent::__construct();
        $this->config->load('http_status');
        $this->load->model('users');
        $this->load->model('spellgame');
        $this->load->model('notify');
        $this->load->library('form_validation');
        $this->config->load('form_validation');
    }

    public function start() {
        $status = $this->config->item('status');
        if ($_POST) {
            $user_id = $this->users->check_user_token($this->input->post('token'));
            if ($user_id) {
                $player = $this->game_player($user_id);
                if($player->is_complete){
        		    $player->target = 100;
        		}
        		if($user_id == 210){
        		    $player->target = 5;
        		}
        	    if($this->online){
        	        $welcome = 'Last Day of Online Round'; 
        	    }else{
        	        $welcome = 'Online round for season 5 is over! But you can play for your practice.';
        	    }
                $words = $this->spellgame->get_game_level_words($player, $user_id);
                $data['status'] = $status['success'];
                $data['data'] = array(
                    'app_version' => '2.0',
                    'online' => $this->online,
                    'welcome' => $welcome,
                    'incorrect' => 'Do not give up here. Try again.',
                    'player' => $player,
                    'words' => $this->encryptAllWords($words)
                );
            } else {
                $data['status'] = $status['auth_failed'];
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }

    public function complete() {
        $status = $this->config->item('status');
        if ($_POST && $this->online) {
            if ($this->form_validation->run('game_complete')) {
                $user_id = $this->users->check_user_token($this->input->post('token'));
                if ($user_id) {
					$completed = $this->input->post('completed');
					$this->spellgame->update_player_history($user_id, $this->input->post('score'), $this->input->post('time'), $completed);
					if ($completed) {
						$this->spellgame->update_player_level($user_id);
						$this->levelNotification($user_id);
					}
					$data['status'] = $status['success'];
                } else {
                    $data['status'] = $status['auth_failed'];
                }
            } else {
                $data['status'] = $status['validation_failed'];
                $data['error'] = strip_tags(validation_errors());
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }

    public function share_flip_retry() {
        $status = $this->config->item('status');
        if ($_POST) {
            $user_id = $this->users->check_user_token($this->input->post('token'));
            $shared = $this->input->post('shared');
            $flip = $this->input->post('flip');
            $retry = $this->input->post('retry');
            if ($user_id) {
                if ($shared) {
                    $this->spellgame->update_shared($user_id);
                }
                if ($flip) {
                    $this->spellgame->update_fliped($user_id);
                }
                if ($retry) {
                    $this->spellgame->update_retry($user_id);
                }
                $data['status'] = $status['success'];
                $data['data'] = $this->spellgame->get_game_flip_retry($user_id);
            } else {
                $data['status'] = $status['auth_failed'];
            }
        } else {
            $data['status'] = $status['bad_request'];
        }
        echo json_encode($data);
    }

    private function game_player($user_id) {
        $player = $this->spellgame->get_game_player_data($user_id);
        if (!$player) {
            $this->spellgame->create_new_player($user_id);
            $player = $this->spellgame->get_game_player_data($user_id);
        }
        return $player;
    }

	public function word_check() {
		$status = $this->config->item('status');
		if ($_POST) {
			if ($this->form_validation->run('word_check')) {
				$user_id = $this->users->check_user_token($this->input->post('token'));
				if ($user_id) {
					$player = $this->game_player($user_id);
					$data['status'] = $status['success'];
					$data['data'] = array(
						'result' => $this->spellgame->check_word($this->input->post('answer'), $this->input->post('word_id')),
						'word' => $this->spellgame->get_game_word($player->level)
					);
				} else {
					$data['status'] = $status['auth_failed'];
				}
			} else {
				$data['status'] = $status['validation_failed'];
				$data['error'] = strip_tags(validation_errors());
			}
		} else {
			$data['status'] = $status['bad_request'];
		}
		echo json_encode($data);
	}

	public function levels() {
		$status = $this->config->item('status');
		$data['status'] = $status['success'];
		$data['data'] = array(
			'levels' => $this->spellgame->get_levels()
		);
		echo json_encode($data);
	}

	public function practice() {
        $status = $this->config->item('status');
        if ($_POST) {
            if ($this->form_validation->run('practice')) {
                $data['status'] = $status['success'];
                $data['data'] = array(
                    'result' => $this->spellgame->check_word($this->input->post('answer'), $this->input->post('word_id')),
                    'word' => $this->spellgame->get_practice_word()
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

    private function encryptAllWords($words){
    	$wordList = array();
    	foreach ($words as $item){
    		$item->word = encrypted_string(strtolower(trim($item->word)));
    		$wordList[] = $item;
		}
    	return $wordList;
	}
	
	private function levelNotification($user_id){
        $player = $this->game_player($user_id);
        if($player->level < 7){
            $notifyMessage = $this->notify->getMessage(2);
		    $this->notify->send_push_notification(array($user_id), $notifyMessage['title'], $notifyMessage['description'], 2);
        }else{
            $level = intval($player->level) - 1;
            $notifyMessage = $this->notify->getMessage(3, $level);
		    $this->notify->send_push_notification(array($user_id), $notifyMessage['title'], $notifyMessage['description'], 3);
        }
    }

}
