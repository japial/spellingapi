<?php


class Statistics extends CI_Model {

	public function count_total_user() {
		$this->db->select('COUNT(sb_users.id) as total');
		$this->db->from('sb_users');
		$user = $this->db->get()->row();
		return $user->total;
	}

	public function count_total_player() {
		$this->db->select('COUNT(sb_game_start.id) as total');
		$this->db->from('sb_game_start');
		$this->db->join('sb_game_history','sb_game_history.user_id=sb_game_start.user_id', 'inner');
		$user = $this->db->get()->row();
		return $user->total;
	}
	
	public function count_total_game_played() {
		$this->db->select('COUNT(sb_game_history.id) as total');
		$this->db->from('sb_game_history');
		$played = $this->db->get()->row();
		return $played->total;
	}
	
	public function count_mobile_game_played() {
		$this->db->select('COUNT(sb_game_history.id) as total');
		$this->db->from('sb_game_history');
		$this->db->where('sb_game_history.mobile', 1);
		$played = $this->db->get()->row();
		return $played->total;
	}

	public function count_total_words() {
		$this->db->select('COUNT(sb_spelling_b.id) as total');
		$this->db->from('sb_spelling_b');
		$user = $this->db->get()->row();
		return $user->total;
	}

	public function count_mobile_users() {
		$this->db->select('COUNT(sb_users.id) as total');
		$this->db->from('sb_users');
		$this->db->where('sb_users.mobile', 1);
		$user = $this->db->get()->row();
		return $user->total;
	}

	public function get_game_history() {
		$this->db->select('COUNT(sb_game_history.id) as game, DATE(sb_game_history.start_date) as date');
		$this->db->from('sb_game_history');
		$this->db->group_by('DATE(sb_game_history.start_date)');
		$this->db->order_by('sb_game_history.id', 'DESC');
		return $this->db->get()->result();
	}
	
	public function get_user_registration_history() {
		$this->db->select('COUNT(sb_users.id) as game, DATE(sb_users.user_registered) as date');
		$this->db->from('sb_users');
		$this->db->group_by('DATE(sb_users.user_registered)');
		$this->db->order_by('sb_users.id', 'DESC');
		return $this->db->get()->result();
	}
	
	public function get_game_hourly_history() {
		$this->db->select('COUNT(sb_game_history.id) as game, sb_game_history.start_date as hour');
		$this->db->from('sb_game_history');
		$this->db->where('DATE(sb_game_history.start_date)', date('Y-m-d'));
		$this->db->group_by('HOUR(sb_game_history.start_date)');
		$this->db->order_by('sb_game_history.id', 'DESC');
		$this->db->limit(24);
		return $this->db->get()->result();
	}

	public function get_game_level_players() {
		$this->db->select('COUNT(sb_game_start.id) as player, sb_game_start.level');
		$this->db->from('sb_game_start');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
		$this->db->where('sb_user_infos.demo', 0);
		$this->db->group_by('sb_game_start.level');
		return $this->db->get()->result();
	}
	
	public function get_players_game_history($limit, $start) {
		$this->db->select('sb_user_infos.name, sb_user_infos.school, sb_user_infos.phone, sb_divisions.name as division, sb_game_history.user_id, COUNT(sb_game_history.id) as played');
		$this->db->from('sb_game_history');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_history.user_id', 'inner');
		$this->db->join('sb_divisions', 'sb_divisions.id=sb_user_infos.division_id', 'inner');
		$this->db->where('sb_game_history.user_id >', 0);
		$this->db->group_by('sb_game_history.user_id');
		$this->db->order_by('COUNT(sb_game_history.id)', 'DESC');
		$this->db->limit($limit, $start);
		return $this->db->get()->result();
	}
	
	public function get_player_level_history($user) {
		$this->db->select('sb_user_infos.name, level, score, time_to_complete, mobile');
		$this->db->from('sb_game_history');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_history.user_id', 'inner');
		$this->db->where('sb_game_history.user_id', $user);
		$user_histories = $this->db->get()->result();
	}
	
	public function count_unique_user_history() {
		$this->db->select('sb_game_history.id');
		$this->db->from('sb_game_history');
		$this->db->where('sb_game_history.user_id >', 0);
		$this->db->group_by('sb_game_history.user_id');
		$history = $this->db->get()->result();
		return sizeof($history);
	}
	
	public function get_division_leaders($division) {
		$this->db->select('sb_user_infos.user_id, sb_user_infos.name, sb_user_infos.school, sb_user_infos.image, sb_game_level.level');
		$this->db->from('sb_game_start');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
		$this->db->join('sb_game_level', 'sb_game_level.id=sb_game_start.level', 'inner');
		$this->db->join('sb_game_history', 'sb_game_history.user_id=sb_game_start.user_id', 'inner');
		$this->db->where('sb_user_infos.demo', 0);
		$this->db->where('sb_user_infos.division_id', $division);
		$this->db->order_by('CAST(sb_game_start.level as SIGNED INTEGER)', 'DESC');
		$this->db->order_by('sb_game_start.score', 'DESC');
		$this->db->order_by('CAST(sb_game_start.time as SIGNED INTEGER)', 'ASC');
		$this->db->order_by('sb_game_start.user_id', 'ASC');
		$this->db->group_by('sb_game_start.id');
		$this->db->limit(5);
		return $this->db->get()->result();
	}

	public function get_national_leaders() {
		$this->db->select('sb_user_infos.user_id, sb_user_infos.name, sb_user_infos.school, sb_user_infos.image, sb_game_level.level');
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
		$this->db->limit(5);
		return $this->db->get()->result();
	}
	
	public function get_all_players($division, $limit, $start) {
		$this->db->select('sb_user_infos.user_id, sb_user_infos.name, sb_user_infos.school, sb_user_infos.image, sb_game_level.level, sb_game_start.score, sb_game_start.time');
		$this->db->from('sb_game_start');
		$this->db->join('sb_user_infos', 'sb_user_infos.user_id=sb_game_start.user_id', 'inner');
		$this->db->join('sb_game_level', 'sb_game_level.id=sb_game_start.level', 'inner');
		$this->db->join('sb_game_history', 'sb_game_history.user_id=sb_game_start.user_id', 'inner');
		if($division != 'National'){
			$this->db->join('sb_divisions', 'sb_divisions.id=sb_user_infos.division_id', 'inner');
			$this->db->where('sb_divisions.name', $division);
		}
		$this->db->where('sb_user_infos.demo', 0);
		$this->db->order_by('CAST(sb_game_start.level as SIGNED INTEGER)', 'DESC');
		$this->db->order_by('sb_game_start.score', 'DESC');
		$this->db->order_by('CAST(sb_game_start.time as SIGNED INTEGER)', 'ASC');
		$this->db->order_by('sb_game_start.user_id', 'ASC');
		$this->db->group_by('sb_game_start.id');
		$this->db->limit($limit, $start);
		return $this->db->get()->result();
	}
	
	public function count_division_players($division) {
		$this->db->select('COUNT(sb_game_start.id) as total');
		$this->db->from('sb_game_start');
		if($division != 'National'){
		    $this->db->join('sb_divisions', 'sb_divisions.id=sb_game_start.division_id', 'inner');
			$this->db->where('sb_divisions.name', $division);
		}
		$result = $this->db->get()->row();
		return $result->total;
	}
	
	public function get_mobile_registrations() {
		$this->db->select('COUNT(sb_users.id) as users, DATE(sb_users.user_registered) as date');
		$this->db->from('sb_users');
		$this->db->where('sb_users.mobile', 1);
		$this->db->group_by('DATE(sb_users.user_registered)');
		$this->db->order_by('sb_users.id', 'DESC');
		return $this->db->get()->result();
	}
	
	public function get_web_registrations() {
		$this->db->select('COUNT(sb_users.id) as users, DATE(sb_users.user_registered) as date');
		$this->db->from('sb_users');
		$this->db->where('sb_users.mobile', 0);
		$this->db->group_by('DATE(sb_users.user_registered)');
		$this->db->order_by('sb_users.id', 'DESC');
		return $this->db->get()->result();
	}


}
