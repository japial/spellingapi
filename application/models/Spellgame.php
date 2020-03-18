<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Spellgame extends CI_Model {

    public function get_game_player_data($user_id) {
        $this->db->select('sb_game_level.level, sb_game_level.target, sb_game_level.time, sb_game_start.is_complete, sb_user_infos.demo, sb_game_start.flip, sb_game_start.retry');
		$this->db->from('sb_game_start');
        $this->db->join('sb_game_level', 'sb_game_level.id=sb_game_start.level', 'inner');
        $this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
        $this->db->where('sb_game_start.user_id', $user_id);
        $game_data = $this->db->get()->row();
        if($game_data) {
            // if (in_array($user_id, array(61, 210)))
            // {
            //     $game_data->target = 5;
            // }
            return $game_data;
        }
        return false;
    }
    
    public function get_game_flip_retry($user_id) {
       $this->db->select('retry, flip')->from('sb_game_start');
       $this->db->where('user_id', $user_id);
       return $this->db->get()->row();
    }
    
    public function update_player_level($user_id) {
        $game = $this->db->select('id, level')->from('sb_game_start')
                                    ->where('user_id', $user_id)->get()->row();
        $level_up = $game->level + 1;
        $next_level = $this->db->select('id')->from('sb_game_level')->where('id', $level_up)->get()->row();
        if ( $next_level )
        {
            if($next_level->id){
                if($level_up == 2){
                    $this->db->update('sb_game_start', array('retry' => 1, 'level' => $level_up), array('id' => $game->id));
                }else{
    			    $this->db->update('sb_game_start', array('level' => $level_up), array('id' => $game->id));
                }
            }
        }
        else
        {
            $this->db->update('sb_game_start', array('level' => $game->level, 'is_complete' => 1), array('id' => $game->id));
        }
    }

	public function update_player_history($user_id, $score = 0, $time = 0, $completed = 0) {
		$game = $this->db->select('id, score, level, attempt, time, is_complete')->from('sb_game_start')->where('user_id', $user_id)->get()->row();
		$this->db->insert('sb_game_history', array('user_id' => $user_id, 'level' => $game->level , 'score' => $score, 'time_to_complete' => $time, 'mobile' => 1));
		if($completed){
		    if($game->is_complete){
		        $this->db->update('sb_game_start',
				array('score' => $score, 'attempt' => $game->attempt + 1, 'time' => $time), array('id' => $game->id));
		    }else{
		        $this->db->update('sb_game_start',
				array('score' => 0, 'attempt' => $game->attempt + 1, 'time' => 0), array('id' => $game->id));
		    }
		}else{
		    if($game->score < $score){
		        $this->db->update('sb_game_start',
				array('score' => $score, 'attempt' => $game->attempt + 1, 'time' => $time), array('id' => $game->id));
		    }
		}
	}
    
    public function update_shared($user_id) {
        $game = $this->db->select('id, flip, level, user_id')->from('sb_game_start')->where('user_id', $user_id)->get()->row();
        $share = $this->db->select('id')->from('sb_game_fb_start')->where('user_id', $user_id)->where('level', $game->level)->get()->row();
        if(!$share){
            if($game->level < 7){
                $this->db->update('sb_game_start', array('flip' => 1), array('id' => $game->id));
            }
            $this->db->insert('sb_game_fb_start', array('user_id' => $game->user_id, 'level' => $game->level, 'mobile' => 1 ));
        }
    }
    
    public function update_fliped($user_id) {
        $game = $this->db->select('id, flip')->from('sb_game_start')->where('user_id', $user_id)->get()->row();
        if($game->flip == 1){
            $this->db->update('sb_game_start', array('flip' => 0), array('id' => $game->id));
        }
    }
    
    public function update_retry($user_id) {
        $game = $this->db->select('id, retry')->from('sb_game_start')->where('user_id', $user_id)->get()->row();
        if($game->retry == 1){
            $this->db->update('sb_game_start', array('retry' => 0), array('id' => $game->id));
        }
    }

    public function get_game_word($level) {
        $this->db->select('id, bangla_meaning, definition, sentence, wtype, voice');
        $this->db->from('sb_spelling_b');
        $this->db->where('level', $level);
        $this->db->order_by('id','RANDOM');
        return  $this->db->get()->row();
    }

    public function get_levels() {
        $this->db->select('id, level as name, target, time');
        $this->db->from('sb_game_level');
        return  $this->db->get()->result();
    }

    public function get_game_level_words($player, $user_id = 0) {
		if ($player->level > 1 && $player->level < 7){
			$limit = $player->target + 1;
		}else if($player->is_complete){
		    $limit = 100;
		}else{
		    $limit = $player->target;
		}
        $this->db->select("id, word, bangla_meaning, TRIM(REPLACE(REPLACE(`definition`, '\n', ''), '\t', '' )) as definition, sentence, wtype, voice");
        $this->db->from('sb_spelling_b');
        //if ( in_array($user_id, array(61,159)) )
        $this->db->where('level', $player->level);
        $this->db->order_by('id','RANDOM');
		$this->db->limit($limit);
        return  $this->db->get()->result();
    }

    public function get_practice_word() {
        $this->db->select('id, bangla_meaning, definition, sentence, wtype, voice');
        $this->db->from('sb_spelling_b');
        $this->db->order_by('id','RANDOM');
        return  $this->db->get()->row();
    }

    public function check_word($answer, $word) {
        $spell = $this->db->select('word')->from('sb_spelling_b')->where('id', $word)->get()->row();
        if(isset($spell->word) && isset($answer) && $answer == $spell->word){
            return TRUE;
        }
        return FALSE;
    }
    
    public function create_new_player($user_id) {
        $player['user_id'] = $user_id;
        $player['score'] = 0;
        $player['level'] = 1;
        $player['start_date'] = date("Y-m-d H:i:s");
        $this->db->insert('sb_game_start', $player);
    }
}
