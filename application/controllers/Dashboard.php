<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('users');
		$this->load->model('leaderboard');
		$this->load->model('statistics');
		$this->load->library('pagination');
	}

	public function index(){
		$admin = $this->session->userdata('admin');
		if($admin){
			$data['total_user'] = $this->statistics->count_total_user();
			$data['played'] = $this->statistics->count_total_game_played();
			$data['words'] = $this->statistics->count_total_words();
			$data['mobile'] = $this->statistics->count_mobile_game_played();
			$data['mobile_users'] = $this->statistics->count_mobile_users();
			$data['history'] = $this->game_history_chart();
			$data['user_registrations'] = $this->user_history_chart();
			$data['hourly_history'] = $this->game_hourly_chart();
			$data['level_players'] = $this->game_level_chart();
			$this->load->view('admin/dashboard', $data);
		}else{
			redirect('dashboard/login');
		}
	}
	
	public function data_test(){
	    $this->db->select('id, district');
		$this->db->from('sb_activation_list');
		$this->db->where('district', 1);
		$data = $this->db->get()->result();
        echo json_encode($this->db->get()->result());
	}
	
	public function user_history(){
	    $admin = $this->session->userdata('admin');
		if($admin){
    	    $config = $this->pagination_config();
            $config["base_url"] = base_url() . "dashboard/user_history";
            $config["total_rows"] = $this->statistics->count_unique_user_history();
            $config["per_page"] = 100;
            $config["uri_segment"] = 3;
            $choice = $config["total_rows"] / $config["per_page"];
            $config["num_links"] = round($choice);
            $this->pagination->initialize($config);
            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data["links"] = $this->pagination->create_links();
    		$data['player_history'] = $this->statistics->get_players_game_history($config["per_page"], $page);
    		$this->load->view('admin/user_history', $data);
		}else{
			redirect('dashboard/login');
		}
	}
	
	public function leaderboard(){
	    $admin = $this->session->userdata('admin');
		if($admin){
    	    $data['leaders'] = $this->leader_boards();
    		$data['national_leaders'] = $this->statistics->get_national_leaders();
    		$this->load->view('admin/leaderboard', $data);
		}else{
			redirect('dashboard/login');
		}
	}
	
	public function spellers($division = 'National'){
	    $admin = $this->session->userdata('admin');
		if($admin){
    		$data['division'] = ucfirst($division);
    		$config = $this->pagination_config();
            $config["base_url"] = base_url() . "dashboard/spellers/".$data['division'];
            $config["total_rows"] = $this->statistics->count_division_players($data['division']);
            $config["per_page"] = 300;
            $config["uri_segment"] = 4;
            $choice = $config["total_rows"] / $config["per_page"];
            $config["num_links"] = round($choice);
            $this->pagination->initialize($config);
            $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
            $data["links"] = $this->pagination->create_links();
    		$data['players'] = $this->statistics->get_all_players($data['division'], $config["per_page"], $page);
    		$this->load->view('admin/players', $data);
		}else{
			redirect('dashboard/login');
		}
	}

	public function login(){
		$admin = $this->session->userdata('admin');
		if($admin){
			redirect('dashboard/index');
		}else{
			$this->load->view('admin/login');
		}

	}

	public function login_submit(){
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		if($username == 'admin' && $password == '159753'){
			$this->session->set_userdata('admin', TRUE);
			redirect('dashboard/index');
		}else{
			redirect('dashboard/login');
		}
	}

	public function logout(){
		$this->session->unset_userdata('admin');
		redirect('dashboard/login');
	}

	private function game_history_chart(){
		$histories = $this->statistics->get_game_history();
		$dates = array();
		$games = array();
		foreach ($histories as $history){
			$dates[] = date("F j",strtotime($history->date));
			$games[] = $history->game;
		}
		return array('dates' => $dates, 'games' => $games);
	}
	
	private function user_history_chart(){
		$histories = $this->statistics->get_user_registration_history();
		$dates = array();
		$users = array();
		foreach ($histories as $history){
			$dates[] = date("F j",strtotime($history->date));
			$users[] = $history->game;
		}
		return array('dates' => $dates, 'regitration' => $users);
	}
	
	private function game_hourly_chart(){
		$histories = $this->statistics->get_game_hourly_history();
		$hours = array();
		$games = array();
		foreach ($histories as $history){
			$hours[] = date("F j, g a",strtotime($history->hour));
			$games[] = $history->game;
		}
		return array('hours' => $hours, 'games' => $games);
	}

	private function game_level_chart(){
		$game_players = $this->statistics->get_game_level_players();
		$levels = array();
		$players = array();
		foreach ($game_players as $gp){
			$levels[] = $gp->level;
			$players[] = $gp->player;
		}
		return array('levels' => $levels, 'players' => $players);
	}

	private function leader_boards(){
		$divisions = $this->leaderboard->get_divisions();
		$leader_boards = array();
		foreach ($divisions as $division){
			$leader_boards[$division->name] = $this->statistics->get_division_leaders($division->id);
		}
		return $leader_boards;
	}
	
	private function pagination_config(){
		return array(
            'per_page' => '1',
            'full_tag_open' => '<ul class="pagination">',
            'full_tag_close' => '</ul>',
            'num_tag_open' => '<li class="page-item"><span class="page-link">',
            'num_tag_close' => '</span></li>',
            'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close' => '</a></li>',
            'prev_tag_open' => '<li class="page-item"><span class="page-link">',
            'prev_tag_close' => '</span></li>',
            'next_tag_open' => '<li class="page-item"><span class="page-link">',
            'next_tag_close' => '</span></li>',
            'prev_link' => '<i class="fa fa-backward"></i>',
            'next_link' => '<i class="fa fa-forward"></i>',
            'last_tag_open' => '<li class="page-item"><span class="page-link">',
            'last_tag_close' => '</span></li>',
            'first_tag_open' => '<li class="page-item"><span class="page-link">',
            'first_tag_close' => '</span></li>');
	}
	
	private function user_registration_chart(){
		$web_regs = $this->statistics->get_web_registrations();
		$mobile_regs = $this->statistics->get_mobile_registrations();
		$web_dates = array();
		$mobile_dates = array();
		$mobile_users = array();
		$web_users = array();
		foreach ($web_regs as $history){
		    $web_dates[] = date("F j",strtotime($history->date));
			$web_users[] = $history->users;
		}
		
		foreach ($mobile_regs as $reg){
		    $mobile_dates[] = date("F j",strtotime($reg->date));
			$mobile_users[] = $reg->users;
		}
		return array('web_dates' => $web_dates, 'web_users' => $web_users, 'mobile_dates' => $mobile_dates, 'mobile_users' => $mobile_users);
	}
	
	
	//Check word Process
	public function check_word_existence(){
		$words = $this->db->select('id, word, voice')->from('sb_spelling_b')->get()->result();
		$voice_updated = array();
		foreach ($words as $word){
			if($word->voice == '' || $word->voice == NULL || $word->voice == ' ' || !file_exists('..'.$word->voice) ){
				$audioFile = $this->getWordPronoun($word->word);
				$file_path = "/wp-content/plugins/wp-spelling-b/uploads/".time().rand().".mp3";
				$this->db->update('sb_spelling_b', array('voice' => $file_path), array('id' => $word->id));
				$realFile_path = "..".$file_path;
				$this->write_audio_file($realFile_path, $audioFile);
				$voice_updated[] = $word->id;
			}
		}
		echo json_encode(array('voice_updated' => $voice_updated));
	}

	private function getWordPronoun($word){
		$url = 'https://code.responsivevoice.org/getvoice.php?t="'.$word.'"&tl=en-GB&sv=g1&vn=&pitch=0.5&rate=0.5&vol=1&gender=female';
		$start = curl_init();
		curl_setopt($start, CURLOPT_URL, $url);
		curl_setopt($start, CURLOPT_HEADER, 0 );
		curl_setopt($start, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($start, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($start, CURLOPT_SSL_VERIFYHOST, 2);
		$result = curl_exec($start);
		curl_close($start);
		return $result;
	}

	private function write_audio_file($file_path, $audioFile){
		$file = fopen($file_path, 'w+');
		fwrite($file, $audioFile);
		fclose($file);
	}
	//Check word Process
	
	private function insert_no_info_users_data(){
	    $all_users = $this->db->select('id, user_login, display_name')->from('sb_users')->get()->result();
        $no_info = array();
        foreach($all_users as $usr){
            $info = $this->db->get_where('sb_user_infos', array('user_id' => $usr->id))->row();
            if(!$info){
               $this->db->insert('sb_user_infos', array('user_id' => $usr->id, 'phone' => $usr->user_login, 'name' => $usr->display_name));
               $no_info[] = $usr->id;
            }
        }
        return $no_info;
	}
	
	private function insert_no_game_start_users_data(){
	    $all_users = $this->db->select('id, user_login, display_name')->from('sb_users')->get()->result();
        $no_game = array();
        foreach($all_users as $usr){
            $info = $this->db->get_where('sb_game_start', array('user_id' => $usr->id))->row();
            if(!$info){
               $this->db->insert('sb_game_start', array('user_id' => $usr->id, 'score' => 0, 'level' => 0, 'flip' => 0, 'retry' => 0, 'attempt'=> 0, 'time' => 0, 'division_id' => 1));
               $no_game[] = $usr->id;
            }
        }
        return $no_game;
	}
	
	private function update_mobile_user_meta(){
	    $user_infos = $this->db->select('id')->from('sb_users')->get()->result();
        $inv_user = array();
        foreach($user_infos as $usr){
            $userMeta = $this->db->select('umeta_id')->from('sb_usermeta')->where('user_id', $usr->id)->where('meta_key', 'sb_capabilities')->get()->row();
            if(!$userMeta){
                $metaString = 'a:1:{s:10:"subscriber";b:1;}';
                $this->db->insert('sb_usermeta', array( 'user_id' => $usr->id, 'meta_key'=> 'sb_capabilities', 'meta_value' => $metaString));
                $inv_user[] = $usr->id;
            }
        }
        return $inv_user;
	}
}
