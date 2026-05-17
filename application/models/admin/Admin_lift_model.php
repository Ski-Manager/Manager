<?php

class Admin_lift_model extends CI_Model{
    
    public function get_all_lift_data(){
        $query = $this->db
            ->select('*')
            ->from('game_lifts')
            ->order_by('id_group', 'asc')
            ->order_by('level', 'asc')
            ->get();
        return $query;
    }
    
    
    public function get_group_lift_data($id_group){
        $query = $this->db
            ->select('*')
            ->from('game_lifts')
            ->where('id_group', $id_group)
            ->get();
        return $query;
    }
    
    public function get_max_value_DB($column, $table){
        $query = $this->db
            ->select($column)
            ->from($table)
            ->order_by($column, 'desc')
            ->limit(1)
            ->get();
        $result = $query->row();
        $result_value = $result->$column;
        return $result_value;
    }
    
    
    
    
    public function count_lift_db($id_group, $level){
        $this->db->select('COUNT(*) as count');
        $this->db->from('game_created_lifts');
        $this->db->where('id_group', $id_group);
        $this->db->where('level', $level);
        $query = $this->db->get();
        return $query;
            
    }
    
    public function count_open_lift_db(){
        $this->db->select('COUNT(*) as count_open');
        $this->db->from('game_created_lifts');
        $this->db->where('id_status', '1');
        $query = $this->db->get();
        return $query;      
    }
    
    
    
    public function update_lift_admin ($id_lift, $id_group, $level, $lift_type, $grip_type, $name_english, $name_french, $building_time, $base_cost, $meter_cost, $speed, $reputation, $capacity, $throughput, $daily_cost) {
        $this->db->trans_start();
        $this->db->set('id_group', $id_group);                                               
        $this->db->set('level', $level);                                             
        $this->db->set('lift_type', $lift_type);                                             
        $this->db->set('grip_type', $grip_type);                                             
        $this->db->set('name_english', $name_english);                                             
        $this->db->set('name_french', $name_french);                                             
        $this->db->set('building_time', $building_time);                                             
        $this->db->set('base_cost', $base_cost);                                             
        $this->db->set('meter_cost', $meter_cost);                                             
        $this->db->set('speed', $speed);                                             
        $this->db->set('reputation', $reputation);                                             
        $this->db->set('capacity', $capacity);                                             
        $this->db->set('throughput', $throughput);                                              
        $this->db->set('daily_cost', $daily_cost);                       
        $this->db->where('id_lift', $id_lift);                       
        $this->db->update('game_lifts');
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