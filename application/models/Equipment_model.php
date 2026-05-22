<?php

class Equipment_model extends CI_Model{
    
        
    /**
     * get_generic_equipment_data    Gets the generic equipment data based on type and level
     * 
     * @param type $type        Equipments type
     * @param type $level       Level of the equipment
     * @return type             Return the genetic info
     */
    public function get_generic_equipment_data($type, $level){
        $query = $this->db
            ->select('*')
            ->from('game_equipments')
            ->where('type', $type)
            ->where('level', $level)
            ->get();
        return $query;
    }
    
    
    /**
     * buy_equipment_db       Buys a new equipment for this player
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function buy_equipment_db($data){
        $insert = $this->db->insert('game_purchased_equipments', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    /**
     * sell_equipment_db       Sells an equipment for this player
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function sell_equipment_db($currentResortID, $id_to_sell){
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_purchased_equipments', $id_to_sell);
        $this->db->limit(1); 
        $delete_equipment = $this->db->delete('game_purchased_equipments');
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    /**
     * select_id_to_sell       Select the id that will be sold
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function select_id_to_sell($currentResortID, $type, $level){
        $query = $this->db
            ->select('id_purchased_equipments')
            ->from('game_purchased_equipments')
            ->where('type', $type)
            ->where('id_resort', $currentResortID)
            ->where('level', $level)
            ->limit(1)
            ->get();
        $id_data = $query->row();
        $num = $query->num_rows();
        if ($num > 0) {
            $id_to_sell = $id_data->id_purchased_equipments;
            return $id_to_sell;
        }
        else 
            return false;
    }
    
    
    public function null_assigned_staff_DB($currentResortID, $sold_id, $type_item_assigned){
        $this->db->trans_start();
        $this->db->set('id_item_assigned', NULL);
        $this->db->set('type_item_assigned', NULL);
        $this->db->where('id_item_assigned' , $sold_id);
        $this->db->where('type_item_assigned' , $type_item_assigned);                      
        $this->db->update('game_hired_staff');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    public function upgrade_equipment_db($currentResortID, $type, $level, $data, $id_to_be_updated){
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('type', $type);
        $this->db->where('level', $level); 
        $this->db->where('id_purchased_equipments', $id_to_be_updated); 
        $this->db->limit(1); 
        $this->db->update('game_purchased_equipments', $data);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    public function upgrade_equipment_custom_name_db($currentResortID, $insert_id, $data){                  
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_purchased_equipments', $insert_id);
        $this->db->limit(1); 
        $this->db->update('game_purchased_equipments', $data);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    /**
     * count_this_equipment_level_db    Counts how many equipment the player has
     * 
     * @param type $currentResortID       Current resort ID
     * @param type $type                Type of equipment (9=groomer...)
     * @param type $level               Level to test (can be '')
     * @return type
     */
    public function count_this_equipment_level_db($currentResortID, $level, $type, $condition){
        $this->db->where('type', $type);
        $this->db->where('id_resort', $currentResortID);
        $this->db->like('level', $level, 'both');
        if ($condition != NULL)
        $this->db->where('delivered', $condition);
        $this->db->from('game_purchased_equipments');
        $count = $this->db->count_all_results();
        return $count;
    }
    
    public function get_this_equipment_level_db($currentResortID, $type, $level){
        $query = $this->db
            ->select('*')
            ->from('game_purchased_equipments')
            ->where('type', $type)
            ->where('id_resort', $currentResortID)
            ->where('level', $level)
            ->get();
        return $query;
    }
   
    public function get_time_left_for_equipment_db($currentResortID, $type, $level){        
        $query = $this->db
            ->select('*')
            ->from('game_purchased_equipments')
            ->where('id_resort', $currentResortID)
            ->where('type', $type)
            ->where('level', $level)
            ->order_by('end_delivery', 'desc')
            ->get();
        return $query;
    }
    
    
    public function get_purchased_equipment_player($currentResortID, $type){        
        $query = $this->db
            ->select('*')
            ->from('game_purchased_equipments')
            ->where('id_resort', $currentResortID)
            ->where('type', $type)
            ->get();
        return $query;
    }
    
    public function edit_assigned_equipment_DB($currentResortID, $id_equipment, $data){
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_purchased_equipments', $id_equipment);
        $this->db->update('game_purchased_equipments', $data); 
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }

    /**
     * update_grooming_intensity_db     Sets the grooming intensity for a purchased groomer
     *
     * @param int    $currentResortID          Owner resort ID (security check)
     * @param int    $id_purchased_equipments  Row to update
     * @param string $intensity                'light', 'standard', or 'intensive'
     * @return bool
     */
    public function update_grooming_intensity_db($currentResortID, $id_purchased_equipments, $intensity) {
        $allowed = ['light', 'standard', 'intensive'];
        if (!in_array($intensity, $allowed, true)) {
            return false;
        }
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_purchased_equipments', $id_purchased_equipments);
        $this->db->limit(1);
        $this->db->update('game_purchased_equipments', ['grooming_intensity' => $intensity]);
        return $this->db->affected_rows() === 1;
    }

    /**
     * toggle_grooming_active_db    Sets the grooming_active flag for a purchased groomer
     *
     * @param int $currentResortID          Owner resort ID (security check)
     * @param int $id_purchased_equipments  Row to update
     * @param int $active                   1 = active every night, 0 = standby (skip grooming)
     * @return bool
     */
    public function toggle_grooming_active_db($currentResortID, $id_purchased_equipments, $active) {
        $active = ($active == 0) ? 0 : 1;
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_purchased_equipments', $id_purchased_equipments);
        $this->db->limit(1);
        $this->db->update('game_purchased_equipments', ['grooming_active' => $active]);
        // affected_rows() is 0 when the value was already the same; treat as success
        return $this->db->affected_rows() >= 0;
    }

    /**
     * set_all_grooming_intensity_db    Sets grooming_intensity for all delivered groomers of a resort
     *
     * @param int    $currentResortID  Owner resort ID
     * @param string $intensity        'light', 'standard', or 'intensive'
     * @return bool
     */
    public function set_all_grooming_intensity_db($currentResortID, $intensity) {
        $allowed = ['light', 'standard', 'intensive'];
        if (!in_array($intensity, $allowed, true)) {
            return false;
        }
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('type', '1');
        $this->db->where('delivered', '1');
        $this->db->update('game_purchased_equipments', ['grooming_intensity' => $intensity]);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * auto_complete_deliveries_DB    Auto-completes all expired equipment deliveries
     *                                where the end_delivery time has passed.
     *
     * @param int|null $currentResortID  Resort to process, or null for all resorts
     */
    public function auto_complete_deliveries_DB($currentResortID = null) {
        $now = gmdate('Y-m-d H:i:s');
        if ($currentResortID !== null) {
            $this->db->where('id_resort', $currentResortID);
        }
        $this->db->where('delivered', '0');
        $this->db->where('end_delivery <=', $now);
        $this->db->update('game_purchased_equipments', ['delivered' => 1]);
    }
    
}

?>