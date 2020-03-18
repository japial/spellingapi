<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('notify');
    }
    
    public function inactivity() {
        $userHistories =  $this->notify->get_user_histories();
        $allFcmUsers =  $this->notify->get_all_fcm_users();
        $activeUsers = $this->getUserId($userHistories);
        $notifyUsers = array();
        foreach($allFcmUsers as $usr){
            if(!in_array($usr->user_id, $activeUsers)){
                $notifyUsers[] = $usr->user_id;
            }
        }
        $message = $this->notify->getMessage(1);
        $notifyChunk = array_chunk($notifyUsers, 100);
        foreach($notifyChunk as $nusers){
            $this->notify->send_push_notification($nusers, $message['title'], $message['description'], 1);
        }
        echo json_encode($notifyChunk);
    }
    
    public function leaderboard() {
        $allFcmUsers =  $this->notify->get_all_fcm_users();
        $message = $this->notify->getMessage(5);
        $notifyUsers = $this->getUserId($allFcmUsers);
        $notifyChunk = array_chunk($notifyUsers, 100);
        foreach($notifyChunk as $nusers){
            //  $this->notify->send_push_notification($nusers, $message['title'], $message['description'], 5);
             $this->notify->send_push_notification($nusers, 'Check Winners List', 'Update SpellChamps app to check the winners list', 5);
        }
        echo json_encode($notifyChunk);
    }
    
    private function getUserId($arrObjs){
        $values = array();
        foreach($arrObjs as $obj){
            $values[] = $obj->user_id;
        }
        return $values;
    }
    
}