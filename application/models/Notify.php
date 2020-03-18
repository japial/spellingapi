<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Notify extends CI_Model {

    public function get_user_histories() {
        $this->db->select('sb_game_history.user_id');
        $this->db->from('sb_game_history');
        $this->db->where('sb_game_history.start_date >', date("Y-m-d H:i:s", strtotime('-6 hours')));
        $this->db->group_by('sb_game_history.user_id');
        return $this->db->get()->result();
    }
    
    public function get_all_fcm_users() {
        $this->db->select('user_fcm.user_id');
        $this->db->from('user_fcm');
        $this->db->group_by('user_fcm.user_id');
        return $this->db->get()->result();
    }
    
    public function send_push_notification($notifyUsers, $title, $description, $target_type) {
        $all_fcm_keys = $this->db->select('fcm.fcm_id')->from("fcm")->join("user_fcm", "user_fcm.fcm_id=fcm.id", "inner")
                        ->where_in("user_fcm.user_id", $notifyUsers)
                        ->get()->result();
        if (!empty($all_fcm_keys)) {
            $all_fcm_users = $this->getFcmId($all_fcm_keys);
            define('API_ACCESS_KEY', 'AAAAs_8W32I:APA91bGBSU9FDVywqf4pH2hZgvtavwk_UHLsSAkHkAkGZdteOmXQFNbAVZtl7b7Ncbfs-GhBcx4j3EQx9etT8mc5Xp3XUpXOLSL7bgSFqAE63MWQGp_6-SIZJK0WaUbhc0faE7FEm1Hk');
            $msg = array(
                "subject" => $title,
                "description" => $description,
                "target_type" => $target_type,
            );
            $fields = array(
                'registration_ids' => $all_fcm_users,
                'data' => $msg
            );

            $headers = array(
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
    
    public function getMessage($notifyType, $paramOne = 0, $paramTwo = 0 ){
        $title = "";
        $description = "";
        switch ($notifyType) {
            case 1:
                $iaMessages = array(
                        array(
                            'title' => "Are you the next winner?", 
                            'description' => "To be the next winner of the Spelling Battle, keep playing the online game"
                        ),
                        array(
                            'title' => "Play as much as you want", 
                            'description' => "You still have a chance to try as many times as you want and win the game. So,What are you waiting for?"
                        ),
                        array(
                            'title' => "Are you a spelling Genius?", 
                            'description' => "To find out how sharp yours spelling skills are, keep playing the game"
                        )
                    );
                shuffle($iaMessages);
                $title = $iaMessages[0]['title'];
                $description = $iaMessages[0]['description'];
                break;
            case 2:
                $title = "Earn 1 Flip word";
                $description = "Share your certificate & earn a special flip word feature";
                break;
            case 3:
                $title = "Collect your e-certificate";
                $description = "You have completed level ".$paramOne." & earned level ".$paramOne." certificate";
                break;
            case 4:
                $title = "Ranking Update";
                $description = "Your new position is ".$paramOne."th, Old position was ".$paramTwo."th";
                break;
            case 5:
                $title = "See who is topping your division";
                $description = "To see who is currently dominating the spelling battle from your division, check out the leaderboard";
                break;
            default:
                $title = "Are you a spelling Genius?";
                $description = "To find out how sharp yours spelling skills are, keep playing the game";
        } 
        return array('title' => $title, 'description' => $description);
    }
    
    private function getFcmId($arrObjs){
        $values = array();
        foreach($arrObjs as $obj){
            $values[] = $obj->fcm_id;
        }
        return $values;
    }
    
}
