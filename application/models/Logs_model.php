<?php

class Logs_model extends CI_Model{
    
    
    public function get_player_logs_DB($currentUserID){
        $query = $this->db
            ->select('*')
            ->from('game_player_logs')
            ->where('id_player', $currentUserID)
            ->order_by('id_log', 'DESC')
            ->get();
       return $query->result();
    }
    
    public function change_read_status_DB($currentUserID){
        $this->db->trans_start();
        $this->db->set('unread', 0);
        $this->db->where('id_player' , $currentUserID);  
        $this->db->update('game_player_logs');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    public function call_notification_DB($data){
        $this->db->trans_start();
        $query = $this->db->insert('game_player_logs', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
}

?>