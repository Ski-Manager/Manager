<?php

class Item_model extends CI_Model{
    
    /**
     * generic_item_info Matches the sector ID and slope ID to a generic slope.
     * 
     * @param type $id_sector
     * @param type $id_slope
     * @param type $type_item       slope or lift to check which table to look
     * @return type Return all the generic slope info
     */
    public function get_generic_item_info($id_item, $type_item){                    // gets the slope information
        if ($type_item == 'lift')
                $id_column = 'id_lift';
        else
            $id_column = 'id_slope';
        $query = $this->db
            ->select('*')
            ->from('game_'.$type_item.'s')
            //->where('id_sector', $id_sector)
            //->like($id_column, $id_item, '')
            ->where($id_column, $id_item)
            ->get();
        return $query;
    }
    
    public function get_generic_lift_info($id_lift_type, $id_grip_type, $capacity){                    // gets the slope information
        $query = $this->db
            ->select('*')
            ->from('game_lifts')
            ->where('lift_type', $id_lift_type)
            ->where('grip_type', $id_grip_type)
            ->where('capacity', $capacity)
            ->where('level', '1')
            ->get();
        return $query;
    }
    
    public function get_lift_types_DB($lift_type = NULL){
        $query = $this->db->select('*');
        $query =  $this->db->from('game_lift_types'); 
        if ($lift_type != NULL)
            $query =  $this->db->where('id_lift_type', $lift_type);
        $query =  $this->db->get();
        return $query;
    }
    
    public function get_existing_id_lift_types_DB(){
        $query = $this->db->distinct('lift_type');
        $query =  $this->db->from('game_lifts'); 
        $query =  $this->db->group_by('lift_type'); 
        $query =  $this->db->get();
        return $query;
    }
    public function get_existing_id_grip_types_DB($id_lift_type){
        $query = $this->db->distinct('grip_type');
        $query =  $this->db->from('game_lifts'); 
        $query =  $this->db->group_by('grip_type'); 
        $query =  $this->db->where('lift_type', $id_lift_type); 
        $query =  $this->db->get();
        return $query;
    }
    public function get_grip_types_DB($grip_type = NULL){
        $query = $this->db->select('*');
        $query =  $this->db->from('game_grip_types'); 
        if ($grip_type != NULL)
            $query =  $this->db->where('id_grip_type', $grip_type);
        $query =  $this->db->get();
        return $query;
    }
    
    public function get_capacity_DB($id_lift_type, $id_grip_type){
        $query = $this->db->distinct('*');
        $query =  $this->db->from('game_lifts'); 
        $query =  $this->db->where('lift_type', $id_lift_type);
        $query =  $this->db->where('grip_type', $id_grip_type);
        $query =  $this->db->where('level', '1');
        $query =  $this->db->get();
        return $query;
    }
    
    public function get_difficulty_name_slope($id_difficulty = '', $column_name = 'name_english'){
        $query = $this->db->select($column_name);
        $query =  $this->db->from('game_difficulties'); 
        $query =  $this->db->where('id_difficulty', $id_difficulty);
        $query =  $this->db->get();
        return $query->row();
    }
    
    public function get_difficulty_slope($id_difficulty = ''){
        $query = $this->db->select('*');
        $query =  $this->db->from('game_difficulties'); 
        if (isset($id_difficulty) && $id_difficulty != '')
            $query =  $this->db->where('id_difficulty', $id_difficulty);
        $query =  $this->db->get();
        if (isset($id_difficulty) && $id_difficulty != '')
            return $query->row();
        else
            return $query;
    }
    
 public function count_assigned_mechanics_db($id_resort, $id_item){
        $query = $this->db
            ->select('game_hired_staff.id_hired_staff, game_staff_tbl.efficiency, game_created_lifts_tbl.id_group, game_created_lifts_tbl.repair_cost, game_created_lifts_tbl.level')
            ->from('game_hired_staff')
            ->join('game_created_lifts as game_created_lifts_tbl', 'game_created_lifts_tbl.id_created_lifts = '.$id_item, 'inner')
            ->join('game_staff as game_staff_tbl', 'game_staff_tbl.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_item_assigned', $id_item)
            ->where('game_hired_staff.type_item_assigned', 'lift')
            ->where('game_hired_staff.id_resort', $id_resort)
            ->get();
        return $query;
    }
    /**
     * generic_item_info Matches the sector ID and slope ID to a generic slope.
     * 
     * @param type $id_sector
     * @param type $id_slope
     * @param type $type_item       slope or lift to check which table to look
     * @return type Return all the generic slope info
     */
    public function get_generic_item_info_general($id_sector, $type_item, $level){                    // gets the slope information
        if ($type_item == 'slope')
            $query = $this->db
            ->select('*')
            ->from('game_'.$type_item.'s')
            ->where('id_sector', $id_sector)
            ->get();
        else if ($type_item == 'lift')
            $query = $this->db
            ->select('*')
            ->from('game_'.$type_item.'s')
            ->where('id_sector', $id_sector)
            ->where('level', $level)
            ->get();
        return $query;
    }
    
    
    public function get_built_item_info_slope($currentResortID, $id_sector){                    // gets the slope information
        $query = $this->db
            ->select('game_slopes.*, game_created_slopes_tbl.*')
            ->from('game_slopes')
            ->join('game_created_slopes as game_created_slopes_tbl', 'game_created_slopes_tbl.id_slope = game_slopes.id_slope', 'inner')
            ->where('game_created_slopes_tbl.id_resort', $currentResortID)
            ->where('game_slopes.id_sector', $id_sector)
            ->get();
        return $query;
    }
    public function get_built_item_info_lift($currentResortID, $id_sector){                    // gets the lift information
        $query = $this->db
            ->select('game_lifts.throughput, game_lifts.name_english, game_lifts.lift_type, game_lifts.name_french, game_lifts.capacity, game_lifts.speed, game_created_lifts_tbl.id_created_lifts, game_created_lifts_tbl.id_group AS id_group_lift, game_created_lifts_tbl.level, game_created_lifts_tbl.custom_name, game_created_lifts_tbl.lift_condition, game_created_lifts_tbl.id_status, game_created_lifts_tbl.end_construction, game_created_lifts_tbl.id_group_location, game_locations_tbl.id_sector')
            ->from('game_lifts ')
            ->join('game_created_lifts as game_created_lifts_tbl', 'game_created_lifts_tbl.id_group = game_lifts.id_group AND game_created_lifts_tbl.level = game_lifts.level', 'inner')
            ->join('game_locations as game_locations_tbl', 'game_locations_tbl.id_group = game_created_lifts_tbl.id_group_location AND game_locations_tbl.id_sector = '.$id_sector, 'inner')
            ->where('game_created_lifts_tbl.id_resort', $currentResortID)
            ->where('game_locations_tbl.id_sector', $id_sector)
            ->group_by('game_created_lifts_tbl.id_created_lifts')
            ->get();
        return $query;
    } 
    
    public function get_start_lift_sector($currentResortID, $id_group_location){                    // gets the lift information
        $query = $this->db
            ->select('min(game_locations_tbl.id_sector) id_sector')
            ->from('game_created_lifts')
            ->join('game_locations as game_locations_tbl', 'game_created_lifts.id_group_location = game_locations_tbl.id_group', 'inner')
            ->where('game_created_lifts.id_resort', $currentResortID)
            ->where('game_created_lifts.id_group_location', $id_group_location)
            ->limit(1)
            ->order_by('id_location', 'asc')
            ->get();
        return $query;
    } 
    public function get_lift_sector_group_location($currentResortID, $id_group_location){                    // gets the lift information
        $query = $this->db
            ->select('min(game_locations_tbl.id_sector) id_sector')
            ->from('game_created_lifts')
            ->join('game_locations as game_locations_tbl', 'game_created_lifts.id_group_location = game_locations_tbl.id_group', 'inner')
            ->where('game_created_lifts.id_resort', $currentResortID)
            ->where('game_created_lifts.id_group_location', $id_group_location)
            ->get();
        return $query;
    } 
        
    
    public function get_generic_item_info_group($id_group, $type_item){                    // gets the slope information
        $query = $this->db
            ->select('*')
            ->from('game_'.$type_item.'s')
            ->where('id_group', $id_group)
            ->get();
        return $query;
    }
    public function get_generic_lift_build_mode($id_lift_type, $id_grip_type, $capacity){                    // gets the lift information before building
        $query = $this->db
            ->select('*')
            ->from('game_lifts')
            ->where('lift_type', $id_lift_type)
            ->where('grip_type', $id_grip_type)
            ->where('capacity', $capacity)
            ->where('level', '1')
            ->get();
        return $query;
    }
    public function get_lift_types_with_id_group($id_group){                    // gets the slope information
        $query = $this->db
            ->select('game_lifts.lift_type, game_lift_types_tbl.id_lift_type, game_lift_types_tbl.name_english')
            ->from('game_lifts')
            ->join('game_created_lifts as game_created_lifts_tbl', 'game_created_lifts_tbl.id_group = game_lifts.id_group', 'inner')
            ->join('game_lift_types as game_lift_types_tbl', 'game_lift_types_tbl.id_lift_type = game_lifts.lift_type', 'inner')
            ->where('game_lifts.id_group', $id_group)
            ->get();
        return $query;
    }
    
    /**
     * get_generic_item_info_simple  Gets the info of a specificc item
     * 
     * @param type $id_item         ID of the item in the DB
     * @param type $type_item       slope or lift to check which table to look
     * @return type Return all the generic item info
     */
    public function get_generic_item_info_simple($id_item, $type_item){                    // gets the item information
        
        if ($type_item == 'slope')
            $query = $this->db
                ->select('*')
                ->from('game_'.$type_item.'s')
                ->where('id_slope', $id_item)
                ->get();
        else if ($type_item == 'lift')     
            $query = $this->db
                ->select('*')
                ->from('game_'.$type_item.'s')
                ->where('id_group', $id_item)
                ->get();
        return $query;
    }
    
    public function get_generic_slope_info(){                 
        $query = $this->db
            ->select('*')
            ->from('game_slopes')
            ->get();
        return $query;
    }
    
    public function get_generic_slope_info_sector($where_statement = null){      
        $this->db->select('*');
        $this->db->from('game_slopes');
        if (isset($where_statement))
            $this->db->where($where_statement);
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * get_generic_item_info_for_level  Gets the info of a specificc item for a specific level
     * 
     * @param type $id_item         ID of the item in the DB
     * @param type $type_item       slope or lift to check which table to look
     * @param type $level           Specific level to get the info from
     * @return type Return all the generic item info
     */
    public function get_generic_item_info_for_level($id_group, $type_item, $level){                    // gets the item information
        $query = $this->db
            ->select('*')
            ->from('game_'.$type_item.'s')
            ->where('id_group', $id_group)
            ->where('level', $level)
            ->get();
        //echo $this->db->last_query();
        return $query;
    }
    
    
    public function get_slope_sector($currentResortID, $id_slope){ 
        
        $this->db->select('game_locations_tbl.id_sector');
        $this->db->from('game_slopes');
        $this->db->join('game_locations as game_locations_tbl', 'game_slopes.start_location = game_locations_tbl.id_location', 'inner');
        $this->db->where('game_slopes.id_slope', $id_slope);
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * check_if_player_has_built_slope  Check if slope has been built
     * 
     * @param type $id_item         ID of the created item
     * @param type $currentResortID Current resort ID
     * @param type $type_item       slope or lift to check which table to look
     * @return type returns all the slope info if exists
     */
    public function check_if_player_has_built_item($id_item, $currentResortID, $type_item){ 
        $this->db->trans_start();
        
        if ($type_item == 'slope')
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_'.$type_item, $id_item)
                ->where('id_resort', $currentResortID)
                ->get();
        else if ($type_item == 'lift')     
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_group', $id_item)
                ->where('id_resort', $currentResortID)
                ->get();
        $this->db->trans_complete();
        //echo $this->db->last_query();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $query;
        }
    }
    
    public function check_if_player_has_built_item_location($id_group_location, $currentResortID, $type_item){ 
        $this->db->trans_start();
        
        if ($type_item == 'slope')
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_'.$type_item, $id_item)
                ->where('id_resort', $currentResortID)
                ->get();
        else if ($type_item == 'lift')     
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_group_location', $id_group_location)
                ->where('id_resort', $currentResortID)
                ->get();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $query;
        }
    }
    
    public function check_if_player_has_built_item_group($id_group, $currentResortID, $type_item){ 
        $this->db->trans_start();
        
        if ($type_item == 'slope')
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_'.$type_item, $id_item)
                ->where('id_resort', $currentResortID)
                ->get();
        else if ($type_item == 'lift')     
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_group', $id_group)
                ->where('id_resort', $currentResortID)
                ->get();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $query;
        }
    }
    
    public function check_if_player_has_built_item_sector($currentResortID, $type_item){ // The sector is already filtered in a previous query
        $this->db->trans_start();
        $query = $this->db
            ->select('*')
            ->from('game_created_'.$type_item.'s')
            ->where('id_resort', $currentResortID)
            ->get();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $query;
        }
    }
    
    /**
     * select_id_to_sell       Select the id that will be sold
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function select_id_to_sell($currentResortID, $type, $id_group_or_item){
        if ($type == 'lift')
            $column_name = 'id_group';
        else if ($type == 'slope')
            $column_name = 'id_slope';
        $id_name = 'id_created_'.$type.'s';
        $query = $this->db
            ->select($id_name)
            ->from('game_created_'.$type.'s')
            ->where('id_resort', $currentResortID)
            ->where($column_name, $id_group_or_item)
            ->limit(1)
            ->get();
        $id_data = $query->row();
        $id_to_sell = $id_data->$id_name;
        return $id_to_sell;
    }
    
    /**
     * sell_item_db       Sells an item for this player
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function sell_item_db($currentResortID, $id_item, $type){
        $this->db->trans_start();
        $id_name = 'id_created_'.$type.'s';
        $this->db->where('id_resort', $currentResortID);
        $this->db->where($id_name, $id_item);
        $this->db->limit(1); 
        $delete_item = $this->db->delete('game_created_'.$type.'s');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * check_status_item Checks if the item is open/closed/maintenance...
     * 
     * @param type $id_slope
     * @param type $currentResortID
     * @return type     Returns the status string (open...)
     */
    public function check_status_item($id_item, $currentResortID, $type_item){ 
        if ($type_item == 'slope')
            $query = $this->db
                ->select('id_status')
                ->from('game_created_'.$type_item.'s')
                ->where('id_'.$type_item, $id_item)
                ->where('id_resort', $currentResortID)
                ->get();
        else if ($type_item == 'lift')
            $query = $this->db
                ->select('id_status')
                ->from('game_created_'.$type_item.'s')
                ->where('id_group_location', $id_item)
                ->where('id_resort', $currentResortID)
                ->get();
        return $query->row();
    }
    
    /**
     * get_status Gets the Displayed name of the status of the item in the correct language, i.e Built / Not built
     * 
     * @param type $id_status
     * @param type $name_language
     * @return type     Returns the name of the status
     */
    public function get_status($id_status, $name_language){
        $query = $this->db
            ->select($name_language)
            ->from('game_statuses')
            ->where('id_status', $id_status)
            ->get();
        return $query;
    }
        
    /**
     * check_if_item_name_taken    checks if item name already taken
     * 
     * @param type $suggested_name  Name suggested by user
     * @param type $type_item       slope or lift to check which table to look
     * @return boolean FALSE = Name taken. TRUE = Name not taken
     */
    public function check_if_item_name_taken($suggested_name, $type_item) {  
        $this->db->where('custom_name', $suggested_name);
        $result = $this->db->get('game_created_'.$type_item.'s');

        if ($result->num_rows() > 0) {
            return FALSE;   // name taken
        } else {
            return TRUE;    // name not taken
        }
    }
    
    /**
     * build_slope  Builds the slope in the DB for the user
     * 
     * @param type $date_time_construction_duration
     * @return type Returns true/false depending of insert result
     */
    public function build_slope($new_slope_created_insert_data){
        $insert = $this->db->insert('game_created_slopes', $new_slope_created_insert_data);
        return $insert;
    }
    
    /**
     * build_lift       Builds the lift in the DB for the user
     * 
     * @param type      $id_group
     * @param type      $date_time_construction_duration
     * @return type     Returns true/false depending of insert result
     */
    public function build_lift($currentResortID, $id_group, $lift_name, $date_time_construction_duration, $id_group_location){
        $new_lift_created_insert_data = array (
            'id_resort' => $currentResortID,
            'id_group' => $id_group,
            'custom_name' => $lift_name,
            'id_group_location' => $id_group_location,
            'lift_condition' => '100',
            'id_status' => '4',
            'end_construction' => gmdate('Y-m-d H:i:s',$date_time_construction_duration)
        );
        $insert = $this->db->insert('game_created_lifts', $new_lift_created_insert_data);
        return $insert;
    }
    
    
    /**
     * get_item_info_player  Gets the info for the built item for this user
     * 
     * @param type $id_item         get ID item in game_created_item table        
     * @param type $currentResortID   Current resort's ID
     * @param type $type_item       Either slope or lift
     * @return type returns all the slope info if exists
     */
    public function get_item_info_player($id_item, $currentResortID, $type_item){ 
        $query = $this->db
            ->select('*')
            ->from('game_created_'.$type_item.'s')
            ->where('id_created_'.$type_item.'s', $id_item)
            ->where('id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    
   /**
    * get_all_items_player  Gets all the items built and owned by the player
    * 
    * @param type $currentResortID Current resort ID
    * @param type $type_item    Type of tem (slope/lift)
    * @return type      Returns the whole array
    */
    public function get_all_items_player($currentResortID, $type_item){ 
        $query = $this->db
            ->select('*')
            ->from('game_created_'.$type_item.'s')
            ->where('id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    
    public function editDerservedSlope($position, $new_id = NULL, $id_created_lifts){
        $this->db->trans_start();
        $table_pos = 'deserved_slope_'.$position;
        if ($new_id == '' || $new_id == '0') {
            $this->db->set($table_pos, NULL);
            //$sql = "UPDATE game_created_lifts SET ".$table_pos." = NULL WHERE id_created_lifts = '".$id_created_lifts."' LIMIT 1";
        }
        else {
            $this->db->set($table_pos, $new_id);
            //$sql = "UPDATE game_created_lifts SET ".$table_pos." = '".$new_id."' WHERE id_created_lifts = '".$id_created_lifts."' LIMIT 1";
        }
        
        $this->db->where('id_created_lifts' , $id_created_lifts);  
        $this->db->limit(1); 
        $this->db->update('game_created_lifts');
        //$this->db->query($sql);
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    
    public function editItemName($currentResortID, $id_created_lifts, $new_name, $type_item){

        //$sql = "UPDATE game_created_".$type_item."s SET custom_name = '".$new_name."' WHERE id_created_".$type_item."s = '".$id_created_lifts."' LIMIT 1";
        $this->db->set('custom_name', $new_name);
        $this->db->where('id_created_'.$type_item.'s' , $id_created_lifts);  
        $this->db->limit(1); 
        $this->db->update('game_created_'.$type_item.'s');
        //$this->db->query($sql);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return 'unknown_error_db';                 // This should never happen
        }
    }
    
    /**
     * upgrade_lift             Upgrade the item in the DB
     * 
     * @param type $new_level                   New level (target level) to upgrade to
     * @param type $end_construction_timestamp   End of the upgrade
     * @param type $id_created_items            ID of the created items
     * @param type $type_item                   Type of item (lift...)
     * @return string|boolean                   True if passed
     */
    public function upgrade_lift($new_level, $end_construction_timestamp, $id_created_items, $type_item){

        $this->db->set('level', $new_level);
        $this->db->set('end_construction' , gmdate('Y-m-d H:i:s',$end_construction_timestamp));  
        $this->db->set('id_status' , '4');  
        $this->db->where('id_created_'.$type_item.'s' , $id_created_items);  
        $this->db->limit(1); 
        $this->db->update('game_created_'.$type_item.'s');
        //$sql = "UPDATE game_created_".$type_item."s SET level = '".$new_level."', end_construction = '".gmdate('Y-m-d H:i:s',$end_construction_timestamp)."', id_status = '4' WHERE id_created_".$type_item."s = '".$id_created_items."' LIMIT 1";
        //$this->db->query($sql);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return 'unknown_error_db';                 // This should never happen
        }
    }
    
    
    public function repair_lift_DB($currentResortID, $end_repair_timestamp, $id_item){
        $this->db->trans_start();
        $this->db->set('id_status', '3');
        $this->db->set('end_construction', $end_repair_timestamp);
        $this->db->where('id_created_lifts', $id_item);
        $this->db->where('id_resort', $currentResortID);
        $this->db->update('game_created_lifts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
   
}

?>