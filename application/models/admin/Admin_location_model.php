<?php

class Admin_location_model extends CI_Model{
    
    public function get_all_location_data(){
        $query = $this->db
            ->select('*')
            ->from('game_locations')
            ->order_by('id_location', 'asc')
            ->get();
        return $query;
    }
    
    
    public function get_group_location_data($id_group){
        $query = $this->db
            ->select('*')
            ->from('game_locations')
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
        if (isset($result))
            $result_value = $result->$column;
        else
            $result_value = 0;
        return $result_value;
    }
    
    
    
    
    public function count_location_db($id_group, $level){
        $this->db->select('COUNT(*) as count');
        $this->db->from('game_created_locations');
        $this->db->where('id_group', $id_group);
        $this->db->where('level', $level);
        $query = $this->db->get();
        return $query;
            
    }
    
    public function count_open_location_db(){
        $this->db->select('COUNT(*) as count_open');
        $this->db->from('game_created_locations');
        $this->db->where('id_status', '1');
        $query = $this->db->get();
        return $query;      
    }
    
    
    
    public function update_location_admin ($id_location, $id_group, $id_sector, $x_coordinates, $y_coordinates, $length, $area) {
        $this->db->trans_start();
        $this->db->set('id_location', $id_location);                                               
        $this->db->set('id_group', $id_group);                                             
        $this->db->set('id_sector', $id_sector);                                             
        $this->db->set('x_coordinates', $x_coordinates);                                             
        $this->db->set('y_coordinates', $y_coordinates);                                             
        $this->db->set('length', $length);                                             
        $this->db->set('area', $area);                     
        $this->db->where('id_location', $id_location);                       
        $this->db->update('game_locations');
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