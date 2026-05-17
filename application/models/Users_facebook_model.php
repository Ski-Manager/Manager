<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Users_facebook_model extends CI_Model{
    function __construct() {
        $this->tableName = 'game_oauth_users';
        $this->primaryKey = 'id_oauth_users';
    }
    public function check_oauth_user($data = []){
        $this->db->select($this->primaryKey);
        $this->db->from($this->tableName);
        $this->db->where(['oauth_provider'=>$data['oauth_provider'],'oauth_uid'=>$data['oauth_uid']]);
        $prevQuery = $this->db->get();
        return $prevQuery;
    }   
    public function update_oauth_user($data = [], $prevResult){
        $data['modified'] = date("Y-m-d H:i:s");
        $update = $this->db->update($this->tableName,$data,['id_oauth_users'=>$prevResult['id_oauth_users']]);
        $userID = $prevResult['id_oauth_users'];
        return $userID?$userID:FALSE;
    }
    public function insert_oauth_user($data = []){
        $data['created'] = date("Y-m-d H:i:s");
        $data['modified'] = date("Y-m-d H:i:s");
        $insert = $this->db->insert($this->tableName,$data);
        $userID = $this->db->insert_id();
        return $userID?$userID:FALSE;
    }

    /*
    public function checkUser2($data = []){
        $this->db->select($this->primaryKey);
        $this->db->from($this->tableName);
        $this->db->where(['oauth_provider'=>$data['oauth_provider'],'oauth_uid'=>$data['oauth_uid']]);
        $prevQuery = $this->db->get();
        $prevCheck = $prevQuery->num_rows();
        
        if($prevCheck > 0){
            $prevResult = $prevQuery->row_array();
            $data['modified'] = date("Y-m-d H:i:s");
            $update = $this->db->update($this->tableName,$data,['id_oauth_users'=>$prevResult['id_oauth_users']]);
            $userID = $prevResult['id_oauth_users'];
        }else{
            $data['created'] = date("Y-m-d H:i:s");
            $data['modified'] = date("Y-m-d H:i:s");
            $insert = $this->db->insert($this->tableName,$data);
            $userID = $this->db->insert_id();
        }

        return $userID?$userID:FALSE;
    }
    
    public function check_linked_data($email, $oauth_uid){
        $this->db->select('oauth_login_id');
        $this->db->from('game_linked_auth');
        $this->db->where(['oauth_login_id'=>$oauth_uid,'email'=>$email]);
        $prevQuery = $this->db->get();
        $prevCheck = $prevQuery->num_rows();
        
        if($prevCheck > 0){
            $prevResult = $prevQuery->row_array();
            $data['modified'] = date("Y-m-d H:i:s");
            $update = $this->db->update($this->tableName,$data,['oauth_uid'=>$prevResult['oauth_uid']]);
            $userID = $prevResult['oauth_uid'];
        }else{
            $data['created'] = date("Y-m-d H:i:s");
            $data['modified'] = date("Y-m-d H:i:s");
            $insert = $this->db->insert($this->tableName,$data);
            $userID = $this->db->insert_id();
        }

        return $userID?$userID:FALSE;
    }
     */
}