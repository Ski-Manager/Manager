<?php

class Admin_staff_model extends CI_Model{
    
    public function get_all_staff_data(){
        $query = $this->db
            ->select('*')
            ->from('game_staff')
            ->order_by('id_staff', 'asc')
            ->get();
        return $query;
    }
    
    
    public function get_group_staff_data($id_staff){
        $query = $this->db
            ->select('*')
            ->from('game_staff')
            ->where('id_staff', $id_staff)
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
    
    
    
    
    public function count_staff_db($id_staff){
        $this->db->select('COUNT(*) as count');
        $this->db->from('game_hired_staff');
        $this->db->where('id_staff', $id_staff);
        $query = $this->db->get();
        return $query;    
    }
    public function count_assigned_staff_db($id_staff){
        $this->db->select('COUNT(*) as count');
        $this->db->from('game_hired_staff');
        $this->db->where('id_staff', $id_staff);
        $this->db->where('id_item_assigned!=', NULL);
        $query = $this->db->get();
        return $query;    
    }
    
    
    
    public function update_staff_admin ($id_staff, $name_english, $name_french, $position, $efficiency, $salary) {
        $this->db->trans_start();                                        
        $this->db->set('name_english', $name_english);                                             
        $this->db->set('name_french', $name_french);                                             
        $this->db->set('position', $position);                                             
        $this->db->set('efficiency', $efficiency);                                             
        $this->db->set('salary', $salary);                         
        $this->db->where('id_staff', $id_staff);                       
        $this->db->update('game_staff');
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