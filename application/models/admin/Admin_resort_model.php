<?php

class Admin_resort_model extends CI_Model{
    
    /**
     * get_resort_Data      Gets all resort's info, players ID and username for all players
     * 
     * @return type     Returns query's result
     */
    public function get_resort_Data(){
        $this->db->select('resorts_tbl.*, game_players.id_player, game_players.username');
        $this->db->from('game_players');
        $this->db->join('game_resorts as resorts_tbl', 'resorts_tbl.id_player = game_players.id_player', 'inner');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * count_items_resort       Counts how many items the current player has.
     * 
     * @param type $id_resort   ID resort to count for
     * @param type $table       Table name. Will search in game_created_slopes, game_purchased_equipments....
     * @return type             Returns integer
     */
    public function count_items_resort($id_resort, $table){
        return $this->db
        ->where('id_resort', $id_resort)
        ->count_all_results($table);
    }
    
     /**
     * get_tourist_info_status       Counts how many items the current player has.
     * 
     * @param type $id_resort   ID resort to count for
     * @param type $table       Table name. Will search in game_created_slopes, game_purchased_equipments....
     * @return type             Returns integer
     */
    public function get_building_info_status($id_resort, $id_building){
        $this->db->select('*');
        $this->db->from('game_created_buildings');
        $this->db->where('id_resort', $id_resort);
        $this->db->where('id_building', $id_building);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * get_last_day_stats      Get the stats from the history table for several items (affluence, injuries...)      
     * 
     * @param type $id_resort  ID of the resort
     * @param type $item       Item to get the stats from (affluence, injuries...)
     * @return type            Returns the integer number from the table
     */
    public function get_last_day_stats($id_resort, $item){
        $this->db->select($item);
        $this->db->from('game_resort_'.$item);
        $this->db->where('id_resort', $id_resort);
        $this->db->order_by('date','DESC'); 
        $this->db->limit('1');
        $query = $this->db->get();
        if ($query->num_rows() >=1){
            $result = $query->row();
            $result = $result->$item;
        }
        else
            $result = '-';
        return $result;
    }
    
    public function get_sum_stats($id_resort, $item){
        $this->db->select('SUM('.$item.') as sum_item');
        $this->db->from('game_resort_'.$item);
        $this->db->where('id_resort', $id_resort);
        $this->db->order_by('date','DESC'); 
        $this->db->limit('1');
        $query = $this->db->get();
        if ($query->num_rows() >=1){
            $result = $query->row();
            $result = $result->sum_item;
        }
        else
            $result = '-';
        return $result;
    }
    
    /**
     * delete_resort_db         Deletes a specific resort from the database
     * 
     * @param type $id_resort   ID of the resort to be deleted
     * @return boolean          Returns true or false
     */
    public function delete_resort_db($id_resort){
        $this->db->trans_start();
        if ($id_resort != 'all') {
            $this->db->where('id_resort', $id_resort);
            $this->db->limit(1);
        }
        else {
            $this->db->where('id_resort <>', '1');    // ID 1 is Javos Resort, we keep this resort
        }
        $this->db->delete('game_resorts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * delete_items_db          Delete all items created by the provided player (slopes, lifts...)
     * 
     * @param type $id_resort   ID of the resort
     * @param type $table       Table to delete the items from
     * @return boolean          Returns true or false
     */
    public function delete_items_db($id_resort, $table){
        $this->db->trans_start();
        if ($id_resort != 'all') {
            $this->db->where('id_resort', $id_resort);
            $this->db->limit(1); 
        }
        else {
            $this->db->not_like('id_resort', '1');    // ID 1 is Javo, we keep this
        }
        $this->db->delete($table);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    public function delete_items_db_player_id($id_player, $table){
        $this->db->trans_start();
        if ($id_player != 'all') {
            $this->db->where('id_player', $id_player);
        }
        else {
            $this->db->not_like('id_player', '1');    // ID 1 is Javo, we keep this
        }
        $this->db->delete($table);
        $updated_rows = $this->db->affected_rows();
        
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