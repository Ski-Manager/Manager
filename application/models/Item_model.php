<?php
require_once APPPATH . 'libraries/Cached_result.php';

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
        $ci = get_instance();
        $key = 'lift_types_' . ($lift_type ?? 'all');
        $cached = $ci->cache->get($key);
        if ($cached !== false) return new Cached_result($cached);
        $this->db->select('*')->from('game_lift_types');
        if ($lift_type != NULL)
            $this->db->where('id_lift_type', $lift_type);
        $result = $this->db->get();
        $ci->cache->save($key, $result->result_array(), 86400);
        return $result;
    }
    
    public function get_difficulties($id_difficulty){
        $ci = get_instance();
        $cached = $ci->cache->get('game_difficulties');
        if ($cached !== false) return new Cached_result($cached);
        $query = $this->db->select('*')->from('game_difficulties')->get();
        $ci->cache->save('game_difficulties', $query->result_array(), 86400);
        return $query;
    }
    
    public function get_existing_id_lift_types_DB(){
        return $this->db
            ->select('lift_type')
            ->from('game_lifts')
            ->group_by('lift_type')
            ->get();
    }
    public function get_existing_id_grip_types_DB($id_lift_type){
        return $this->db
            ->select('grip_type')
            ->from('game_lifts')
            ->where('lift_type', $id_lift_type)
            ->group_by('grip_type')
            ->get();
    }
    
    public function get_grip_types_DB($grip_type = NULL){
        $ci = get_instance();
        $key = 'grip_types_' . ($grip_type ?? 'all');
        $cached = $ci->cache->get($key);
        if ($cached !== false) return new Cached_result($cached);
        $this->db->select('*')->from('game_grip_types');
        if ($grip_type != NULL)
            $this->db->where('id_grip_type', $grip_type);
        $result = $this->db->get();
        $ci->cache->save($key, $result->result_array(), 86400);
        return $result;
    }
    
    public function get_slope_type_name($slope_type_id){
        return $this->db
            ->select('*')
            ->from('game_slope_types')
            ->where('id_slope_types', $slope_type_id)
            ->get();
    }
    
    public function get_capacity_DB($id_lift_type, $id_grip_type){
        return $this->db
            ->select('*')
            ->from('game_lifts')
            ->where('lift_type', $id_lift_type)
            ->where('grip_type', $id_grip_type)
            ->where('level', '1')
            ->get();
    }
    
    public function get_difficulty_name_slope($id_difficulty, $column_name){
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
    
    
    public function get_slope_types($id_type = ''){
        $ci = get_instance();
        $key = 'slope_types_' . ($id_type !== '' ? $id_type : 'all');
        $cached = $ci->cache->get($key);
        if ($cached !== false) return new Cached_result($cached);
        $this->db->select('*')->from('game_slope_types');
        if (isset($id_type) && $id_type != '')
            $this->db->where('id_slope_types', $id_type);
        $query = $this->db->get();
        $ci->cache->save($key, $query->result_array(), 86400);
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
            ->select('game_lifts.lift_type, game_lift_types_tbl.id_lift_type, game_lift_types_tbl.name_english, game_lift_types_tbl.name_french')
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
    
    /**
     * get_generic_slope_info_sector    Returns slope rows that match the given WHERE clause.
     *                                  When $id_resort is provided the result also includes
     *                                  slopes that were drawn by that resort (id_resort = $id_resort).
     *
     * @param  string|null $where_statement  Raw SQL fragment, e.g. "(id_sector=1 OR id_sector=2)"
     * @param  int|null    $id_resort        Optional resort ID to include player-drawn slopes
     * @return object  CI query result
     */
    public function get_generic_slope_info_sector($where_statement = null, $id_resort = null){      
        $this->db->select('*');
        $this->db->from('game_slopes');
        $has_id_resort = $this->db->field_exists('id_resort', 'game_slopes');
        if (isset($where_statement) && $where_statement !== '') {
            if ($id_resort !== null && $has_id_resort) {
                // Show global slopes matching the sector filter OR slopes drawn by this resort
                $this->db->where('(('.$where_statement.' AND id_resort IS NULL) OR id_resort = '.(int)$id_resort.')', null, false);
            } else {
                $this->db->where($where_statement);
            }
        } elseif ($id_resort !== null && $has_id_resort) {
            $this->db->where('id_resort', (int)$id_resort);
        }
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
        
        $this->db->select('game_locations_tbl.id_sector, game_slopes.slope_type');
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
    public function check_if_player_has_built_item($id_created_item, $currentResortID, $type_item){ 
        $this->db->trans_start();
        //echo 'IN $id_created_item : '.$id_created_item;
        if ($type_item == 'slope')
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_'.$type_item, $id_created_item)
                ->where('id_resort', $currentResortID)
                ->get();
        else if ($type_item == 'lift')     
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_created_lifts', $id_created_item)
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
    
    public function check_if_player_has_built_created_item($id_created_item, $currentResortID, $type_item){ 
        $this->db->trans_start();
        //echo 'IN $id_created_item : '.$id_created_item;
        if ($type_item == 'slope')
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_created_slopes', $id_created_item)
                ->where('id_resort', $currentResortID)
                ->get();
        else if ($type_item == 'lift')     
            $query = $this->db
                ->select('*')
                ->from('game_created_'.$type_item.'s')
                ->where('id_created_lifts', $id_created_item)
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
     * build_custom_slope  Inserts a player-designed custom trail into game_created_slopes.
     */
    public function build_custom_slope($data) {
        return $this->db->insert('game_created_slopes', $data);
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
    
    
    public function editItemDifficulty($currentResortID, $id_created_slopes, $new_difficulty, $type_item){

        $this->db->set('id_difficulty', $new_difficulty);
        $this->db->where('id_created_'.$type_item.'s' , $id_created_slopes);  
        $this->db->limit(1); 
        $this->db->update('game_created_'.$type_item.'s');
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
    

    /**
     * get_lift_module_upgrades_DB  Returns all available modular lift upgrade definitions.
     *
     * Self-healing: if the catalogue is empty or every row has a zero cost
     * (which happens when the DB was initialised before the seed migration ran),
     * the canonical five modules are (re)inserted automatically.
     *
     * @return CI_DB_result
     */
    public function get_lift_module_upgrades_DB() {
        $has_valid = $this->db
            ->where('cost >', 0)
            ->count_all_results('game_lift_module_upgrades') > 0;

        if (!$has_valid) {
            $this->_seed_lift_module_upgrades();
        }

        return $this->db
            ->select('*')
            ->from('game_lift_module_upgrades')
            ->order_by('id_module', 'asc')
            ->get();
    }

    /**
     * _seed_lift_module_upgrades  Truncates and reseeds the module catalogue.
     *
     * Called automatically by get_lift_module_upgrades_DB() when it detects
     * that the table is empty or contains only zero-cost rows.
     */
    private function _seed_lift_module_upgrades() {
        $this->db->trans_start();
        $this->db->query("DELETE FROM `game_lift_module_upgrades`");
        $this->db->insert_batch('game_lift_module_upgrades', [
            [
                'module_key'          => 'motor',
                'name_english'        => 'Motor Upgrade',
                'name_french'         => 'Mise à niveau du moteur',
                'description_english' => 'Higher-power drive unit increases haul-rope speed and passenger throughput.',
                'description_french'  => 'Un moteur plus puissant augmente la vitesse du câble et le débit de passagers.',
                'cost'                => 150000,
                'speed_bonus'         => 1.00,
                'throughput_bonus'    => 300,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 5,
                'daily_cost_increase' => 200,
            ],
            [
                'module_key'          => 'chairs',
                'name_english'        => 'Chair Upgrade',
                'name_french'         => 'Mise à niveau des sièges',
                'description_english' => 'Wider, more ergonomic chairs increase per-vehicle capacity and rider comfort.',
                'description_french'  => 'Des sièges plus larges et ergonomiques augmentent la capacité et le confort.',
                'cost'                => 100000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 200,
                'capacity_bonus'      => 2,
                'reputation_bonus'    => 5,
                'daily_cost_increase' => 150,
            ],
            [
                'module_key'          => 'bubble_cover',
                'name_english'        => 'Bubble Cover',
                'name_french'         => 'Bulle coupe-vent',
                'description_english' => 'Retractable bubble wind shields protect riders in poor weather, boosting resort reputation.',
                'description_french'  => 'Des bulles rétractables protègent les passagers par mauvais temps et améliorent la réputation.',
                'cost'                => 75000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 0,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 10,
                'daily_cost_increase' => 100,
            ],
            [
                'module_key'          => 'night_lighting',
                'name_english'        => 'Night Lighting',
                'name_french'         => 'Éclairage nocturne',
                'description_english' => 'LED floodlight rig allows safe night operations and significantly boosts resort reputation.',
                'description_french'  => "Un système d'éclairage LED permet les opérations de nuit et améliore nettement la réputation.",
                'cost'                => 200000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 0,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 15,
                'daily_cost_increase' => 250,
            ],
            [
                'module_key'          => 'rfid_gates',
                'name_english'        => 'RFID Gates',
                'name_french'         => 'Portiques RFID',
                'description_english' => 'Automated RFID boarding gates eliminate ticket queues, maximising passenger throughput.',
                'description_french'  => 'Les portiques RFID automatisés suppriment les files d\'attente et maximisent le débit.',
                'cost'                => 50000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 400,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 5,
                'daily_cost_increase' => 50,
            ],
        ]);
        $this->db->trans_complete();
    }

    /**
     * get_lift_modules_installed_DB  Returns all modules already installed on a created lift.
     *
     * @param int $id_created_lifts
     * @return CI_DB_result
     */
    public function get_lift_modules_installed_DB($id_created_lifts) {
        return $this->db
            ->select('game_created_lift_modules.id_lift_module, game_created_lift_modules.installed_at, game_lift_module_upgrades.*')
            ->from('game_created_lift_modules')
            ->join('game_lift_module_upgrades', 'game_lift_module_upgrades.id_module = game_created_lift_modules.id_module', 'inner')
            ->where('game_created_lift_modules.id_created_lifts', $id_created_lifts)
            ->get();
    }

    /**
     * install_lift_module_DB  Records the purchase / installation of a module on a created lift.
     *
     * @param int $id_resort
     * @param int $id_created_lifts
     * @param int $id_module
     * @return bool
     */
    public function install_lift_module_DB($id_resort, $id_created_lifts, $id_module) {
        $this->db->trans_start();
        $this->db->insert('game_created_lift_modules', [
            'id_resort'        => $id_resort,
            'id_created_lifts' => $id_created_lifts,
            'id_module'        => $id_module,
        ]);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_lift_network_data  Gets all built lifts with throughput, status, sector and slope assignments.
     * Slopes are resolved via location-area matching (same logic as get_deserved_slopes helper):
     * a lift serves every built & open slope whose start_location sits in the same area as the lift.
     * Used by Lift_network_controller to calculate network efficiency metrics.
     *
     * @param  int $currentResortID  ID of the resort
     * @return CI_DB_result          Query result with one row per lift
     */
    public function get_lift_network_data($currentResortID) {
        return $this->db
            ->select('game_created_lifts.id_created_lifts, game_created_lifts.id_status,
                      game_created_lifts.lift_condition, game_created_lifts.level,
                      game_created_lifts.id_group AS id_group_lift,
                      game_created_lifts.id_group_location, game_created_lifts.custom_name,
                      game_lifts.throughput, game_lifts.lift_type,
                      MIN(game_locations.id_sector) AS id_sector,
                      GROUP_CONCAT(DISTINCT game_created_slopes.id_slope) AS served_slopes')
            ->select('(SELECT MAX(gl2.throughput) FROM game_lifts gl2
                       WHERE gl2.id_group = game_created_lifts.id_group) AS max_throughput', FALSE)
            ->from('game_created_lifts')
            ->join('game_lifts', 'game_lifts.id_group = game_created_lifts.id_group AND game_lifts.level = game_created_lifts.level', 'inner')
            ->join('game_locations', 'game_locations.id_group = game_created_lifts.id_group_location', 'inner')
            ->join('game_locations AS slope_locs', 'slope_locs.area = game_locations.area', 'left')
            ->join('game_slopes', 'game_slopes.start_location = slope_locs.id_location', 'left')
            ->join('game_created_slopes', 'game_created_slopes.id_slope = game_slopes.id_slope
                      AND game_created_slopes.id_resort = game_created_lifts.id_resort', 'left')
            ->where('game_created_lifts.id_resort', $currentResortID)
            ->where_not_in('game_created_lifts.id_status', ['4'])
            ->group_by('game_created_lifts.id_created_lifts')
            ->get();
    }

   
}


?>
