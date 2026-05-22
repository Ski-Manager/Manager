<?php

class Admin_slope_model extends CI_Model{
    
    public function get_all_slope_data(){
        $query = $this->db
            ->select('*')
            ->from('game_slopes')
            ->order_by('id_slope', 'asc')
            ->get();
        return $query;
    }
    
    
    public function get_single_slope_data($id_slope){
        $query = $this->db
            ->select('*')
            ->from('game_slopes')
            ->where('id_slope', $id_slope)
            ->limit('1')
            ->get();
        return $query;
    }
    
    public function get_difficulties($id_difficulty){
        $query = $this->db
            ->select('*')
            ->from('game_difficulties')
            ->get();
        return $query;
    }
    
    public function get_locations($id_location){
        $query = $this->db
            ->select('*')
            ->from('game_locations')
            ->get();
        return $query;
    }
    
    public function get_slope_types(){
        $query = $this->db
            ->select('*')
            ->from('game_slope_types')
            ->order_by('id_slope_types', 'asc')
            ->get();
        return $query;
    }
    
    public function add_item_admin($data_insert, $table){                      
        $this->db->trans_start(); 
        $insert = $this->db->insert($table, $data_insert);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $insert;
        }           
    } 
    
    
    
    public function count_slope_db($id_slope){
        $this->db->select('COUNT(*) as count');
        $this->db->from('game_created_slopes');
        $this->db->where('id_slope', $id_slope);
        $query = $this->db->get();
        return $query;
    }

    public function count_open_slope_db(){
        $this->db->select('COUNT(*) as count_open');
        $this->db->from('game_created_slopes');
        $this->db->where('id_status', '1');
        $query = $this->db->get();
        return $query;      
    }
    
    public function update_slope_admin ($id_slope, $id_sector, $name_english, $name_french, $length, $start_location, $end_location, $reputation, $slope_type, $path) {
        $this->db->trans_start();
        $this->db->set('id_sector', $id_sector);
        $this->db->set('name_english', $name_english);
        $this->db->set('name_french', $name_french);
        $this->db->set('start_location', $start_location);
        $this->db->set('end_location', $end_location);
        $this->db->set('reputation', $reputation);
        $this->db->set('slope_type', $slope_type);
        $this->db->set('path', $path);
        $this->db->set('length', $length);
        $this->db->where('id_slope', $id_slope);
        $this->db->update('game_slopes');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    /**
     * delete_items_db          Deletes the item from admin page (slopes, lifts, buildings)
     * 
     * @param type $id_item         ID of the item to delete
     * @param type $column_name   Name of the column
     * @param type $table       Table to delete the items from
     * @return boolean          Returns true or false
     */
    public function delete_items_admin_db($id_item, $column_name, $table){
        $this->db->trans_start();
        if ($id_item != 'all') {
            $this->db->where($column_name, $id_item);
            $this->db->delete($table);
        }
        else {
            $this->db->truncate($table);
        }
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