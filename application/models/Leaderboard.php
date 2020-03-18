<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Leaderboard extends CI_Model {

	public function get_main_leaders() {
		$this->db->select('sb_user_infos.user_id, sb_user_infos.name, sb_user_infos.school, sb_user_infos.image, sb_game_level.level, sb_game_start.attempt');
		$this->db->from('sb_game_start');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
		$this->db->join('sb_game_level', 'sb_game_level.id=sb_game_start.level', 'inner');
		$this->db->join('sb_game_history', 'sb_game_history.user_id=sb_game_start.user_id', 'inner');
		$this->db->where('sb_user_infos.demo', 0);
		$this->db->order_by('CAST(sb_game_start.level as SIGNED INTEGER)', 'DESC');
		$this->db->order_by('sb_game_start.score', 'DESC');
		$this->db->order_by('CAST(sb_game_start.time as SIGNED INTEGER)', 'ASC');
		$this->db->order_by('sb_game_start.user_id', 'ASC');
		$this->db->group_by('sb_game_start.id');
		return $this->db->get()->result();
	}

	public function get_division_leaders($division, $level = 1) {
		$this->db->select('sb_user_infos.user_id, sb_user_infos.name, sb_user_infos.school, sb_user_infos.image, sb_game_start.score, sb_game_start.time');
		$this->db->from('sb_game_start');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
		$this->db->join('sb_game_history', 'sb_game_history.user_id=sb_game_start.user_id', 'inner');
		$this->db->where('sb_user_infos.division_id', $division);
		$this->db->where('sb_game_start.level', $level);
		$this->db->where('sb_user_infos.demo', 0);
		$this->db->order_by('CAST(sb_game_start.level as SIGNED INTEGER)', 'DESC');
		$this->db->order_by('sb_game_start.score', 'DESC');
		$this->db->order_by('CAST(sb_game_start.time as SIGNED INTEGER)', 'ASC');
		$this->db->order_by('sb_game_start.user_id', 'ASC');
		$this->db->group_by('sb_game_start.id');
		$this->db->limit(1000);
		return $this->db->get()->result();
	}
	
	public function get_leaders_by_division($division, $page = NULL) {
	    $start = 0;
	    $limit = 0;
	    $division = (int) $division;
	    if($page == NULL && $page == ''){
            if($division > 1){
	             $limit = 300;
	        }else{
	             $limit = 800;
	        }
	    }else{
	        if($division > 1 && $page < 3){
                $start = $page * 100;
                $limit = 100;
	        }else if($division ==1 && $page < 8){
                $start = $page * 100;
                $limit = 100;
	        }
	    }
		$this->db->select('sb_user_infos.user_id, sb_user_infos.name, sb_user_infos.school, sb_user_infos.image, sb_game_start.score, sb_game_start.time, sb_game_start.level');
		$this->db->from('sb_game_start');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
		$this->db->join('sb_game_history', 'sb_game_history.user_id=sb_game_start.user_id', 'inner');
		$this->db->where('sb_user_infos.division_id', $division);
		$this->db->where('sb_user_infos.demo', 0);
		$this->db->order_by('CAST(sb_game_start.level as SIGNED INTEGER)', 'DESC');
		$this->db->order_by('sb_game_start.score', 'DESC');
		$this->db->order_by('CAST(sb_game_start.time as SIGNED INTEGER)', 'ASC');
		$this->db->order_by('sb_game_start.user_id', 'ASC');
		$this->db->group_by('sb_game_start.id');
		$this->db->limit($limit, $start);
		return $this->db->get()->result();
	}

	public function get_player_ranking($user_id) {
	    $player =  $this->db->select('division_id')->from('sb_game_start')->where('sb_game_start.user_id', $user_id)->get()->row();
		$sql = "SELECT x.position
		        FROM (SELECT sb_game_start.user_id,  sb_game_start.score, sb_game_start.time, sb_game_start.level,
                @rownum := @rownum + 1 AS position
                FROM sb_game_start
                INNER JOIN (SELECT @rownum := 0) r
                WHERE sb_game_start.division_id = ? and sb_game_start.demo = 0
        		ORDER BY CAST(sb_game_start.level as SIGNED INTEGER) DESC, sb_game_start.score DESC, CAST(sb_game_start.time as SIGNED INTEGER) ASC, sb_game_start.user_id ASC ) x where x.user_id = ?";
		$query = $this->db->query($sql, array($player->division_id, $user_id));
		$division_ranking = $query->row();
        return ($division_ranking) ? $division_ranking->position : 0;
    }
    
    public function get_divisions() {
        $this->db->select('id, name');
        $this->db->from('sb_divisions');
        $this->db->order_by('sb_divisions.id', 'ASC');
        return $this->db->get()->result();
    }

    public function get_player_last_game($user) {
		$player =  $this->db->select('level, is_complete')->from('sb_game_start')->where('user_id', $user)->get()->row();
		$history =  $this->db->select('score, level')->from('sb_game_history')->where('user_id', $user)->order_by('id', 'DESC')->get()->row();
		$level = $this->db->select('target')->from('sb_game_level')->where('id', $player->level)->get()->row();
		$last_game = 0;
		if(isset($history->level) && $player->level == $history->level){
		    if($player->is_complete){
		        $last_game = $history->score;
		    }else{
		        $last_game = ($history->score / $level->target) * 100;
		    }
		}
		return $last_game;
    }

    public function get_division_districts($division) {
        $this->db->select('id, name');
        $this->db->from('sb_districts');
        $this->db->where('division_id', $division);
        return $this->db->get()->result();
    }
    
    public function get_school_winners($division = 1){
		if($division == 1){
			$divName = 'Dhaka';
		}
		if($division == 2){
			$divName = 'Chottogram';
		}
		if($division == 3){
			$divName = 'Barishal';
		}
		if($division == 4){
			$divName = 'Sylhet';
		}
		if($division == 5){
			$divName = 'Rajshahi';
		}
		if($division == 6){
			$divName = 'Khulna';
		}
		if($division == 7){
			$divName = 'Rangpur';
		}
		$this->db->select('id, name, school, class, district, image');
        $this->db->from('sb_activation_list');
        $this->db->where('district', $divName);
        return $this->db->get()->result();
	}

    public function get_district_players($district) {
        $this->db->select('sb_users.id as user_id');
        $this->db->from('sb_users');
        $this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_users.id', 'inner');
        $this->db->where('sb_user_infos.district', $district);
        return $this->db->get()->result();
    }

    public function get_division_players($division) {
        $districts = $this->get_division_districts($division);
        $players = [];
        foreach ($districts as $district) {
            $dis_players = $this->get_district_players($district->name);
            if (!empty($dis_players)) {
                foreach ($dis_players as $value) {
                    $players[] = $value;
                }
            }
        }
        return $players;
    }
    
}
