<?php

class Admin_equipment_model extends CI_Model{
    
    public function get_all_equipment_data(){
        $query = $this->db
            ->select('*')
            ->from('game_equipments')
            ->order_by('id_equipment', 'asc')
            ->get();
        return $query;
    }
    
    public function get_equipment_type_data($equipment_type){
        $query = $this->db
            ->select('*')
            ->from('game_equipments')
            ->where('type', $equipment_type)
            ->order_by('id_equipment', 'asc')
            ->get();
        return $query;
    }
    
    public function get_specific_equipment_type($equipment_type_id){
        $query = $this->db
            ->select('name_type')
            ->from('game_equipment_type')
            ->where('id_type', $equipment_type_id)
            ->get();
        return $query->row();
    }
    
    public function get_types($id_type){
        $query = $this->db
            ->select('*')
            ->from('game_equipment_type')
            ->get();
        return $query;
    }
    
    public function count_equipment_db($equipment_type){
        $this->db->select('id_equipment, level, COUNT(*) as count');
        $this->db->from('game_purchased_equipments');
        $this->db->where('type', $equipment_type);
        $this->db->group_by('level');
        $query = $this->db->get();
        return $query;
            
    }
    public function count_assigned_equipment_db($equipment_type){
        $this->db->select('id_equipment, level, COUNT(*) as count');
        $this->db->from('game_purchased_equipments');
        $this->db->where('type', $equipment_type);
        $this->db->where('assigned_to_sector!=', NULL);
        $this->db->group_by('level');
        $query = $this->db->get();
        return $query;
            
    }
    
    public function update_equipment_type ($id_equipment, $equipment_type, $level, $name_english, $name_french, $delivery_time, $buying_cost, $reputation, $capacity, $max_income) {
        $this->db->trans_start();
        $this->db->set('level', $level);                                             
        $this->db->set('type', $equipment_type);                                             
        $this->db->set('name_english', $name_english);                                             
        $this->db->set('name_french', $name_french);                                             
        $this->db->set('delivery_time', $delivery_time);                                             
        $this->db->set('buying_cost', $buying_cost);                                             
        $this->db->set('reputation', $reputation);                                             
        $this->db->set('capacity', $capacity);                                             
        $this->db->set('max_income', $max_income);                       
        $this->db->where('id_equipment', $id_equipment);                       
        $this->db->update('game_equipments');
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