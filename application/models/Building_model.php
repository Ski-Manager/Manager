<?php

class Building_model extends CI_Model{
    
    /**
     * get_generic_building_data    Gets the generic building data based on type and level
     * 
     * @param type $type        Buildings type
     * @param type $level       Level of the building
     * @return type             Return the genetic info
     */
    public function get_generic_building_data($type, $level){
        $query = $this->db
            ->select('*')
            ->from('game_buildings')
            ->where('type', $type)
            ->where('level', $level)
            ->get();
        return $query;
    }
    
    /**
     * get_building_data_for_player     gets the player building data MAYBE NEEDS TO BE CHANGED WHEN WE CAN HAVE SEVERAL BUILDINGS?????
     * 
     * @param type $currentResortID       Current resort ID
     * @param type $type       Type of building (e.g 2 = access)
     * @param type $level      Level of the building (1, 2 ,3)
     * @return type             Return the genetic info
     */
    public function get_building_data_for_player($currentResortID, $type, $level){
        $query = $this->db
            ->select('*')
            ->from('game_created_buildings')
            ->where('id_resort', $currentResortID)
            ->where('type', $type)
            ->where('level', $level)
            ->get();
        return $query;
    }
    
    public function get_max_building_level_for_player($currentResortID, $type){
        $query = $this->db
            ->select('*')
            ->from('game_created_buildings')
            ->where('id_resort', $currentResortID)
            ->where('type', $type)
            ->get();
        return $query;
    }
    
    /**
     * count_this_building_level_db    Counts how many building the player has
     * 
     * @param type $currentResortID       Current resort ID
     * @param type $type                Type of building (3=hotel, 4=restaurant...)
     * @param type $level               Level to test (can be '')
     * @param type $id_status           id_status to check. '' is ommited means any status
     * @param type $match_level         both means %match% with double wildcards
     * @param type $match_status        none means 'match' without wildcards
     * @return type
     */
    public function count_this_building_level_db($currentResortID, $type, $level, $id_status='', $match_level='both', $match_status='none'){
        //echo ' level: '.$level.' id_status: '.$id_status.' match_level: '.$match_level.' match_status: '.$match_status.'<br><br>' ;
        $this->db->where('type', $type);
        $this->db->where('id_resort', $currentResortID);
        $this->db->like('id_status', $id_status, $match_status);
        $this->db->like('level', $level, $match_level);
        $this->db->from('game_created_buildings');
        //echo 'result: '.$this->db->count_all_results().'<br><br>';
        return $this->db->count_all_results();
    }
    
    public function get_time_left_for_building_db($currentResortID, $type, $level){        
        $query = $this->db
            ->select('*')
            ->from('game_created_buildings')
            ->where('id_resort', $currentResortID)
            ->where('type', $type)
            ->where('level', $level)
            ->where('id_status', '4')
            ->get();
        return $query;
    }
    
    /**
     * get_created_buildings_for_player     Gets the list of created buildings for the player
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type_building     type of the generic building
     * @return type                 Returns all info
     */
    public function get_created_buildings_for_player($currentResortID, $type_building){        
        $query = $this->db
            ->select('*')
            ->from('game_created_buildings')
            ->where('id_resort', $currentResortID)
            ->where('type', $type_building)
            ->get();
        return $query;
    }
    
    /**
     * get_all_created_buildings_for_player     Gets the list of created buildings for the player
     * 
     * @param type $currentResortID   Current resort ID
     * @return type                 Returns all info
     */
    public function get_all_created_buildings_for_player($currentResortID, $type_building, $level, $operator = ''){        
        $query = $this->db
            ->select('type, count(*) as count')
            ->from('game_created_buildings')
            ->where('id_resort', $currentResortID)
            ->where('type', $type_building)
            ->where('level'.$operator, $level)
            ->get();
        return $query;
    }
    
    
    /**
     * get_building_capacity     Gets the list of created buildings for the player
     * 
     * @param type $currentResortID   Current resort ID
     * @return type                 Returns all info
     */
    public function get_building_capacity($type_building, $level){        
        $query = $this->db
            ->select('capacity')
            ->from('game_buildings')
            ->where('type', $type_building)
            ->where('level', $level)
            ->get();
        return $query->row();
    }
    
    /**
     * get_all_created_buildings_for_player     Gets the list of created buildings for the player
     * 
     * @param type $currentResortID   Current resort ID
     * @return type                 Returns all info
     */
    public function list_all_building_types(){        
        $query = $this->db
            ->select('distinct(type)')
            ->from('game_buildings')
            ->get();
        return $query;
    }
    
    
    public function get_building_ach_unlocked($id_player, $achievement_id){
        $sql = 'SELECT achievements.name_english, user_ach.progress, user_ach.id_player, user_ach.id_user_achievements, user_ach.claimed, '
             . '(SELECT COUNT(id_player) FROM user_achievements WHERE id_player = ? AND id_achievement = ? AND claimed = 1 AND progress = 100) AS has_player_unlocked_ach '
             . 'FROM achievements '
             . 'LEFT JOIN user_achievements AS user_ach ON user_ach.id_achievement = achievements.id_achievement '
             . 'WHERE achievements.id_achievement = ?';
        return $this->db->query($sql, [(int)$id_player, (int)$achievement_id, (int)$achievement_id]);
    }
    
    
    public function get_building_ach_unlocked2($id_player, $achievement_id){        
        $query = $this->db
            ->select('id_user_achievements, claimed, progress')
            ->from('user_achievements')
            ->where('id_player', $id_player)
            ->where('id_achievement', $achievement_id)
            ->get();
        return $query;
    }
        
    /**
     * build_building_db       Build a new building for this player
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function build_building_db($data){
        $insert = $this->db->insert('game_created_buildings', $data);
        return $insert;
    }
    
    /** insert_new_generic_building_db Inserts a new generic building in the DB (For admin...)
     * 
     * @param type $data    Array containing the data. WHich one?....
     * @return type         Returns the value of the result
     */
    public function insert_new_generic_building_db($data){
        $insert = $this->db->insert('game_buildings', $data);
        return $insert;
    }
    
    /**
     * update_skipass_price_db  Updates the price of the ski pass
     * 
     * @param type $data        Array containing "name of the column" => "New price"
     * @param type $currentResortID   Current resort ID
     * @return boolean          Return result true/false
     */
    public function update_skipass_price_db($data, $currentResortID){                  
        $this->db->where('id_resort', $currentResortID);
        $result = $this->db->update('game_resorts', $data);
        // Return true on success regardless of whether the value changed
        // (affected_rows() = 0 means no change was needed, which is still a success)
        return (bool)$result;
    }
    
    /**
     * save_dynamic_pricing_db  Saves VIP pass price, family discount and group discount percentage for a resort
     *
     * @param int $currentResortID
     * @param int $vip_pass_price       0 = disabled, 1–MAX_VIP_PASS_PRICE
     * @param int $family_discount_pct  0 = disabled, 1–MAX_FAMILY_DISCOUNT_PCT
     * @param int $group_discount_pct   0 = disabled, 1–MAX_GROUP_DISCOUNT_PCT
     * @return bool
     */
    public function save_dynamic_pricing_db($currentResortID, $vip_pass_price, $family_discount_pct, $group_discount_pct = 0) {
        $this->db->trans_start();
        $this->db->set('vip_pass_price',      (int)$vip_pass_price);
        $this->db->set('family_discount_pct', (int)$family_discount_pct);
        $this->db->set('group_discount_pct',  (int)$group_discount_pct);
        $this->db->where('id_resort', $currentResortID);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * update_parking_fee_db    Updates the parking fee for a resort
     *
     * @param int $currentResortID
     * @param int $parking_fee   Fee per vehicle per day (€)
     * @return bool
     */
    public function update_parking_fee_db($currentResortID, $parking_fee) {
        $this->db->trans_start();
        $this->db->set('parking_fee', (int)$parking_fee);
        $this->db->where('id_resort', $currentResortID);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * update_open_close_building_db    Either Opens or Closes the building
     *                                  Be aware that it will update all IDs, all buildings. Only checks the type!
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type_building     Generic type of the building to open/close
     * @param type $data            Contain the data to upsate (here id_status = X)
     * @return boolean              Returns True or False
     */
    public function update_open_close_building_db($currentResortID, $type_building, $data){  
        $this->db->trans_start();
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('type', $type_building);
        $this->db->where('id_status !=', '4');
        $this->db->update('game_created_buildings', $data);
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    public function update_building_db($currentResortID, $type, $level, $data){                  
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('type', $type);
        $this->db->where('level', $level); 
        $this->db->limit(1); 
        $this->db->set($data, FALSE);
        $this->db->set('id_building', 'id_building + 1', FALSE);
        $this->db->set('id_status', '4');
        $this->db->update('game_created_buildings');
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    
    public function complete_construction_DB($id_created_item, $table, $column, $field){  
        $now = gmdate('Y-m-d H:i:s');
        $this->db->where($column, $id_created_item);
        $this->db->limit(1); 
        $this->db->set($field, $now);
        if ($column != 'id_purchased_equipments')    // for buildings
            $this->db->set('id_status', 1);
        else                                        // For equipment
            $this->db->set('delivered', 1);
        $this->db->update($table);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }

    /**
     * get_cannons_for_player_DB    Gets all cannons for a player with building details
     *
     * @param type $currentResortID   Current resort ID
     * @return type                   Returns all cannons with their details
     */
    public function get_cannons_for_player_DB($currentResortID){
        $query = $this->db
            ->select('game_created_buildings.id_created_buildings, game_created_buildings.level, game_created_buildings.id_status, game_buildings.capacity, game_buildings.daily_cost')
            ->from('game_created_buildings')
            ->join('game_buildings', 'game_buildings.type = game_created_buildings.type AND game_buildings.level = game_created_buildings.level', 'inner')
            ->where('game_created_buildings.id_resort', $currentResortID)
            ->where('game_created_buildings.type', 'cannon')
            ->order_by('game_created_buildings.level', 'ASC')
            ->order_by('game_created_buildings.id_created_buildings', 'ASC')
            ->get();
        return $query;
    }

    /**
     * update_single_cannon_status_db    Opens or closes a single cannon
     *
     * @param type $currentResortID       Current resort ID
     * @param type $id_created_buildings  ID of the cannon to update
     * @param type $data                  Data to update (id_status)
     * @return boolean                    Returns True or False
     */
    public function update_single_cannon_status_db($currentResortID, $id_created_buildings, $data){
        $this->db->trans_start();
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_created_buildings', $id_created_buildings);
        $this->db->where('id_status !=', '4');
        $this->db->update('game_created_buildings', $data);
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }

    /**
     * auto_complete_constructions_DB    Auto-completes all expired constructions for
     *                                   buildings, slopes, and lifts where the
     *                                   end_construction time has passed.
     *
     * @param int|null $currentResortID  Resort to process, or null for all resorts
     */
    public function auto_complete_constructions_DB($currentResortID = null) {
        $now = gmdate('Y-m-d H:i:s');
        foreach (['game_created_buildings', 'game_created_slopes', 'game_created_lifts'] as $table) {
            if ($currentResortID !== null) {
                $this->db->where('id_resort', $currentResortID);
            }
            $this->db->where('id_status', '4');
            $this->db->where('end_construction <=', $now);
            $this->db->update($table, ['id_status' => 1]);
        }
    }
   
}

?>