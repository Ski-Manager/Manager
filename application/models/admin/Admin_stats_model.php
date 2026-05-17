<?php

class Admin_stats_model extends CI_Model{
    
    
    public function active_players_last_X_days($num_days){
        $this->db->select('id_player');
        $this->db->from('game_players');
        $this->db->where('last_connection >=', $num_days);
        $query = $this->db->get();
        $num_players = $query->num_rows();
        return $num_players;
    }
    
    public function number_resorts(){
        $this->db->select('id_resort');
        $this->db->from('game_resorts');
        $query = $this->db->get();
        $num_resorts = $query->num_rows();
        return $num_resorts;
    }
    
    public function number_open_resorts(){
        $this->db->select('game_resorts_tbl.id_resort');
        $this->db->from('game_created_buildings');
        $this->db->join('game_resorts as game_resorts_tbl', 'game_created_buildings.id_resort = game_resorts_tbl.id_resort', 'inner');
        $this->db->where('game_created_buildings.type', 'tourist_info');
        $this->db->where('game_created_buildings.id_status', '1');
        $query = $this->db->get();
        $num_resorts = $query->num_rows();
        return $num_resorts;
    }
    
    public function activated_accounts(){
        $this->db->reset_query();
        $this->db->select('id_player');
        $this->db->from('game_players');
        $this->db->where('activated', '1');
        $query = $this->db->get();
        $num_players = $query->num_rows();
        return $num_players;
    }
    
    public function get_non_vacation_account(){
        $this->db->reset_query();
        $this->db->select('id_player');
        $this->db->from('game_players');
        $this->db->where('vacation_mode', '0');
        $query = $this->db->get();
        $num_players = $query->num_rows();
        return $num_players;
    }
    
    public function get_history_admin_players($field1, $field2, $field3, $field4){
        $query = $this->db
            ->select('date, '.$field1.', '.$field2.', '.$field3.', '.$field4)
            ->from('game_admin_stats')
            ->order_by('date', 'asc')
            ->get();
        return $query;
    }
}

?>