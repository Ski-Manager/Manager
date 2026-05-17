<?php

class Admin_building_model extends CI_Model{
    
    public function get_all_building_data(){
        $query = $this->db
            ->select('*')
            ->from('game_buildings')
            ->order_by('id_building', 'asc')
            ->get();
        return $query;
    }
    
    public function get_building_type_data($building_type){
        $query = $this->db
            ->select('*')
            ->from('game_buildings')
            ->where('type', $building_type)
            ->order_by('id_building', 'asc')
            ->get();
        return $query;
    }
    
    
    public function count_building_db($building_type){
        $this->db->select('level, COUNT(*) as count');
        $this->db->from('game_created_buildings');
        $this->db->where('type', $building_type);
        $query = $this->db->get();
        return $query;
            
    }
    
    public function update_building_type ($id_building, $building_type, $level, $name_english, $name_french, $building_time, $building_cost, $reputation, $capacity, $max_income, $daily_cost) {
        $this->db->trans_start();
        $this->db->set('level', $level);                                             
        $this->db->set('type', $building_type);                                             
        $this->db->set('name_english', $name_english);                                             
        $this->db->set('name_french', $name_french);                                             
        $this->db->set('building_time', $building_time);                                             
        $this->db->set('building_cost', $building_cost);                                             
        $this->db->set('reputation', $reputation);                                             
        $this->db->set('capacity', $capacity);                                             
        $this->db->set('max_income', $max_income);                                             
        $this->db->set('daily_cost', $daily_cost);                       
        $this->db->where('id_building', $id_building);                       
        $this->db->update('game_buildings');
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