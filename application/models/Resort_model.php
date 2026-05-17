<?php
require_once APPPATH . 'libraries/Cached_result.php';

class Resort_model extends CI_Model{
    
    /**
     * display_resort_info_DB Gets all the resort info from DB
     * 
     * @param type $currentResortID   ID of the resort we are interested in
     * @return type Returns all the fields from the table
     */
    public function display_resort_info_DB($currentResortID){                    // gets the resort information
        $query = $this->db
            ->select('*')
            ->from('game_resorts')
            ->where('id_resort', $currentResortID)
            ->get();
        return $query;
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
    
    public function get_all_sectors(){
        $ci = get_instance();
        $cached = $ci->cache->get('game_sectors_all');
        if ($cached !== false) return new Cached_result($cached);
        $query = $this->db->select('*')->from('game_sectors')->get();
        $ci->cache->save('game_sectors_all', $query->result_array(), 86400);
        return $query;
    }

    public function get_sectors_by_map_type($id_map_type) {
        return $this->db->select('*')->from('game_sectors')->where('id_map_type', (int)$id_map_type)->get();
    }

    public function get_map_type($id_map_type) {
        if (!$this->db->table_exists('game_map_types')) {
            return null;
        }
        return $this->db->select('*')->from('game_map_types')->where('id_map_type', (int)$id_map_type)->get()->row();
    }

    public function get_resort_map_type($id_resort) {
        if (!$this->db->field_exists('id_map_type', 'game_resorts')) {
            return 1;
        }
        $row = $this->db->select('id_map_type')->from('game_resorts')->where('id_resort', (int)$id_resort)->get()->row();
        return $row ? (int)$row->id_map_type : 1;
    }
    
    public function check_if_record_exists($id_resort, $type, $current_date){
        $this->db->where('id_resort', $id_resort);
        $this->db->where('date', $current_date);
        $this->db->from('game_resort_'.$type);
        return $this->db->count_all_results();
    }
    
    /**
     * count_deserving_lifts Counts how many lifts are deserving the provided slope
     * NOT USED ANYMORE. SHOULD BE REPLACED BY get_deserving_lifts OF MYCUSTOM_HELPER
     * get_deserving_lifts($currentResortID, $info_item->id_slope, 'lift_names');
     * 
     */
    /* public function count_deserving_lifts_db($id_resort, $id_slope, $index_loop){         
        $field_name = 'deserved_slope_'.$index_loop;
        $query = $this->db
            ->select('*')
            ->from('game_created_lifts')
            ->where($field_name, $id_slope)
            ->where('id_resort', $id_resort)
            ->where('id_status', '1')
            ->get();
        $result = $query->num_rows();
        return $result;
    }
    
     */
    
    /**
     * count_ongoing_items_db Counts how items are being built by the player
     * 
     */
    public function count_ongoing_items_db($id_resort, $type, $condition){         
        $table_name = 'game_created_'.$type.'s';
        return $this->db
            ->where('id_status', $condition)    // id_status = 4 (under construction)
            ->where('id_resort', $id_resort)
            ->count_all_results($table_name);
    }
    
    /**
     * count_assigned_staff_db Counts how many employees are deserving the item
     * 
     */
    public function count_assigned_staff_db($id_resort, $id_item){
        return $this->db
            ->where('id_item_assigned', $id_item)
            ->where('id_resort', $id_resort)
            ->count_all_results('game_hired_staff');
    }
   
    
    
    
    /**
     * resort_available_in_DB Checks if the resort name is available in the DB
     * 
     * @param type $suggested_resort_name Name of the resort entered by the user
     * @return boolean FALSE if we have a match (not available). TRUE if available (no match)
     */
    public function resort_available_in_DB($suggested_resort_name, $currentUserID = NULL) {   // checks if username already in DB
        $this->db->where('resort_name', $suggested_resort_name);
        if (isset($currentUserID) && $currentUserID != NULL)
            $this->db->where('id_player!=', $currentUserID);
        $result = $this->db->get('game_resorts');

        if ($result->num_rows() > 0) {
            return FALSE;   // resort name taken
        } else {
            return TRUE;    // resort name not taken, can be created
        }
    }
    
    /**
     * create_resort Creates the resort in the database
     * Access to specific sectors might need to be adjusted in the future
     * 
     * @return type Returns the result of the query
     */
    public function create_resort(){                    // create a new resort in the database for this user      
        $currentUserID = $this->users_model->get_user_id();  
        $allowed_altitudes = ['low', 'medium', 'high'];
        $allowed_aspects   = ['north', 'south', 'east', 'west'];
        $altitude = $this->input->post('resort_altitude', TRUE);
        $aspect   = $this->input->post('resort_aspect',   TRUE);
        if (!in_array($altitude, $allowed_altitudes)) $altitude = 'medium';
        if (!in_array($aspect,   $allowed_aspects))   $aspect   = 'north';
        $currentUserID = $this->users_model->get_user_id();
        $legacy_bonus = $this->users_model->get_legacy_bonus_DB($currentUserID);
        $starting_cash = (int)START_CASH + (int)$legacy_bonus;
        // Double starting cash in easy mode
        if ($this->users_model->get_difficulty_mode($currentUserID) == 1) {
            $starting_cash *= 2;
        }
        $new_resort_insert_data = array (
            'resort_name' => $this->input->post('resort_name', TRUE),
            'resort_country' => $this->input->post('resort_country', TRUE),
            'resort_description' => nl2br($this->input->post('resort_description', TRUE)),    // nl2br to add the line breaks
            'altitude' => $altitude,
            'aspect'   => $aspect,
            'id_player' => $currentUserID,
            'reputation' => '0',
            'prestige' => '0',
            'cash' => $starting_cash,
            'snow_level' => START_SNOW,        // Constant defined under config.php
            'skipass_daily' => DEFAULT_SKIPASS_DAILY,        // Constant defined under config.php
            'skipass_weekly' => DEFAULT_SKIPASS_WEEKLY        // Constant defined under config.php
        );   
        $this->db->trans_start(); 
        $insert = $this->db->insert('game_resorts', $new_resort_insert_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            if ($legacy_bonus > 0){
                $this->users_model->set_legacy_bonus_DB($currentUserID, 0);  // consume the bonus
            }
            return $insert;
        }           
    }  
    
  
    public function update_resort(){                        
        $currentUserID = $this->users_model->get_user_id();  
        $this->db->trans_start();
        $this->db->set('resort_name', $this->input->post('resort_name', TRUE));                          
        $this->db->set('resort_country', $this->input->post('resort_country', TRUE));                          
        $this->db->set('resort_description', nl2br($this->input->post('resort_description', TRUE)));
        $this->db->where('id_player', $currentUserID);                          
        $this->db->update('game_resorts');
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
     * update_microclimate  Updates altitude and aspect for a resort, deducting the change cost from cash.
     *                       The first time this is called (change_count == 0) the change is free.
     *                       Every subsequent call costs (change_count + 1) * MICROCLIMATE_CHANGE_BASE_COST.
     *
     * @param  int     $id_resort   Resort ID
     * @param  string  $altitude    New altitude value (low/medium/high)
     * @param  string  $aspect      New aspect value (north/south/east/west)
     * @return bool|string  'no_cash' if insufficient funds, false on DB error, true on success
     */
    public function update_microclimate($id_resort, $altitude, $aspect) {
        // Fetch current resort data
        $resort = $this->db->select('cash, microclimate_change_count')
                           ->where('id_resort', $id_resort)
                           ->get('game_resorts')
                           ->row();
        if (!$resort) {
            return false;
        }
        $change_count = isset($resort->microclimate_change_count) ? (int)$resort->microclimate_change_count : 0;
        $cost = ($change_count + 1) * MICROCLIMATE_CHANGE_BASE_COST;

        // First change (count == 0) is free
        if ($change_count > 0 && (int)$resort->cash < $cost) {
            return 'no_cash';
        }

        $this->db->trans_start();
        $this->db->set('altitude', $altitude);
        $this->db->set('aspect',   $aspect);
        $this->db->set('microclimate_change_count', $change_count + 1);
        if ($change_count > 0) {
            $this->db->set('cash', (int)$resort->cash - $cost);
        }
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return true;
    }
    
    public function update_resort_DB($id_resort, $original_id_resort, $resort_name, $country, $resort_description, $cash, $snow_level, $reputation, $skipass_daily, $skipass_weekly){                         // Used for admins        
        $this->db->trans_start();
        $this->db->set('id_resort', $id_resort);                                             
        $this->db->set('resort_name', $resort_name);                                             
        $this->db->set('resort_country', $country);                                             
        $this->db->set('resort_description', $resort_description);                                             
        $this->db->set('cash', $cash);                                             
        $this->db->set('snow_level', $snow_level);                                             
        $this->db->set('reputation', $reputation);                                             
        $this->db->set('skipass_daily', $skipass_daily);                                             
        $this->db->set('skipass_weekly', $skipass_weekly);                       
        $this->db->where('id_resort', $original_id_resort);                       
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    public function reset_snow_level($id_resort){                    // resets the snow level for the new season
        $this->db->trans_start();
        $this->db->set('snow_level', START_SNOW);                                                
        $this->db->where('id_resort', $id_resort);                          
        $this->db->update('game_resorts');
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
     * get_night_skiing_status      Returns the night skiing status for a resort (0 = disabled, 1 = enabled)
     *
     * @param type $id_resort   Resort ID
     * @return int              0 (disabled) or 1 (enabled)
     */
    public function get_night_skiing_status($id_resort){
        try {
            $query = $this->db
                ->select('night_skiing')
                ->from('game_resorts')
                ->where('id_resort', $id_resort)
                ->get();
            $row = $query->row();
            return $row ? (int)$row->night_skiing : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * set_night_skiing_DB      Enables or disables night skiing for a resort
     *
     * @param type $id_resort   Resort ID
     * @param type $status      0 = disable, 1 = enable
     * @return int|false        Affected rows or false on failure
     */
    public function set_night_skiing_DB($id_resort, $status){
        $this->db->trans_start();
        $this->db->set('night_skiing', (int)$status);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        return $updated_rows;
    }

    /**
     * get_cannon_target_snow_DB    Returns the cannon snow target for a resort (0 = no target)
     *
     * @param type $id_resort   Resort ID
     * @return int              Target snow level in cm (0 = disabled)
     */
    public function get_cannon_target_snow_DB($id_resort){
        try {
            $query = $this->db
                ->select('cannon_target_snow')
                ->from('game_resorts')
                ->where('id_resort', $id_resort)
                ->get();
            $row = $query->row();
            return $row ? (int)$row->cannon_target_snow : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * set_cannon_target_snow_DB    Sets the cannon snow target for a resort
     *
     * @param type $id_resort   Resort ID
     * @param type $target      Target snow level in cm (0 = disabled, max = MAX_SNOW_LEVEL)
     * @return int|false        Affected rows or false on failure
     */
    public function set_cannon_target_snow_DB($id_resort, $target){
        $this->db->trans_start();
        $this->db->set('cannon_target_snow', (int)$target);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        return $updated_rows;
    }

    /**
     * get_cannon_auto_start_DB     Returns the cannon auto-start threshold for a resort (0 = disabled)
     *
     * @param type $id_resort   Resort ID
     * @return int              Minimum snow level (cm) below which cannons auto-start (0 = disabled)
     */
    public function get_cannon_auto_start_DB($id_resort){
        try {
            $query = $this->db
                ->select('cannon_auto_start')
                ->from('game_resorts')
                ->where('id_resort', $id_resort)
                ->get();
            $row = $query->row();
            return $row ? (int)$row->cannon_auto_start : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * set_cannon_auto_start_DB     Sets the cannon auto-start threshold for a resort
     *
     * @param type $id_resort   Resort ID
     * @param type $threshold   Minimum snow level in cm (0 = disabled, max = MAX_SNOW_LEVEL)
     * @return int|false        Affected rows or false on failure
     */
    public function set_cannon_auto_start_DB($id_resort, $threshold){
        $this->db->trans_start();
        $this->db->set('cannon_auto_start', (int)$threshold);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        return $updated_rows;
    }

    /**
     * ensure_snowmaking_mode_column    Adds snowmaking_mode to game_resorts if missing.
     *                                  Called lazily before any read/write of that column so
     *                                  the feature works even when the migration has not been run.
     */
    public function ensure_snowmaking_mode_column() {
        if (!$this->db->field_exists('snowmaking_mode', 'game_resorts')) {
            $this->load->dbforge();
            $this->dbforge->add_column('game_resorts', [
                'snowmaking_mode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => FALSE,
                    'default'    => 'normal',
                ],
            ]);
        }
    }

    /**
     * get_snowmaking_mode_DB    Returns the snowmaking mode for a resort ('normal' by default).
     *
     * @param  int $id_resort
     * @return string  'normal', 'eco', or 'boost'
     */
    public function get_snowmaking_mode_DB($id_resort) {
        $this->ensure_snowmaking_mode_column();
        $result = $this->db
            ->select('snowmaking_mode')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get();
        $row = $result->row();
        $mode = $row ? $row->snowmaking_mode : 'normal';
        return in_array($mode, SNOWMAKING_MODES) ? $mode : 'normal';
    }

    /**
     * set_snowmaking_mode_DB    Persists the snowmaking mode for a resort.
     *
     * @param  int    $id_resort
     * @param  string $mode  'normal', 'eco', or 'boost'
     * @return int|false  Affected rows or false on failure
     */
    public function set_snowmaking_mode_DB($id_resort, $mode) {
        $this->ensure_snowmaking_mode_column();
        if (!in_array($mode, SNOWMAKING_MODES)) {
            $mode = 'normal';
        }
        $this->db->trans_start();
        $this->db->set('snowmaking_mode', $mode);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $updated_rows;
    }

    /**
     * get_snowmaking_schedule_DB    Returns the snowmaking schedule bitmask for a resort.
     *                               Bits 0–6 correspond to Mon–Sun. Default 127 = every night.
     *
     * @param  int $id_resort
     * @return int  0–127
     */
    public function get_snowmaking_schedule_DB($id_resort) {
        $row = $this->db
            ->select('snowmaking_schedule')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get()
            ->row();
        return $row ? (int)$row->snowmaking_schedule : 127;
    }

    /**
     * set_snowmaking_schedule_DB    Persists the snowmaking schedule bitmask for a resort.
     *
     * @param  int $id_resort
     * @param  int $schedule  0–127 bitmask
     * @return int|false  Affected rows or false on failure
     */
    public function set_snowmaking_schedule_DB($id_resort, $schedule) {
        $schedule = max(0, min(127, (int)$schedule));
        $this->db->trans_start();
        $this->db->set('snowmaking_schedule', $schedule);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $updated_rows;
    }

    /**
     * give_access_sector       Gives access to a new sector to the player (to his resort)
     * 
     * @return type Returns the result of the query
     */
    public function give_access_sector($currentResortID, $sector){                    // create a new entry with the resort ID and sector ID   
        $new_access = array (
            'id_resort' => $currentResortID,
            'sector' => $sector,
        ); 
        $this->db->trans_start();
        $insert = $this->db->insert('game_access_sector', $new_access);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $insert;
        }     
    }  
    
     public function create_history_stats($type, $data){
        if ($type == 'prestige'){
            $type = 'prestige_gains';  // The table for prestige is called prestige_gains instead of prestige
            $data['prestige_gains'] = $data['prestige'];
            unset($data['prestige']);
        }
        $insert = $this->db->insert('game_resort_'.$type, $data);
        return $insert;        
    }  
    
    /**
     * get_player_resort            Get the ID resort for the specific player
     * 
     * @param type $id_player       ID player
     * @return type                 Returns player's ID resort (integer)
     */
    public function get_player_resort($id_player){
        $this->db->select('id_resort');
        $this->db->from('game_resorts');
        $this->db->where('id_player', $id_player);
        $get_player_resort = $this->db->get();
        $result = $get_player_resort->row();
        $id_resort = $result->id_resort;
        return $id_resort;
    }
    
    
    public function get_resort_name($id_resort){
        $this->db->select('resort_name');
        $this->db->from('game_resorts');
        $this->db->where('id_resort', $id_resort);
        $get_player_resort = $this->db->get();
        $result = $get_player_resort->row();
        $resort_name = $result->resort_name;
        return $resort_name;
    }
    
    
    /**
     * get_sector_access            Get a array of the sectors the current player/resort has access to
     * 
     * @param type $id_resort       ID resort
     * @return type                 Returns the array 
     */
    public function get_sector_access($id_resort){
        $ci = get_instance();
        $key = 'sector_access_' . $id_resort;
        $cached = $ci->cache->get($key);
        if ($cached !== false) return $cached;
        $this->db->select('sector');
        $this->db->from('game_access_sector');
        $this->db->where('id_resort', $id_resort);
        $this->db->order_by('sector', 'asc');
        $get_sector_access = $this->db->get();
        $array_sectors = [];
        foreach ($get_sector_access->result() as $get_sector_access_array){
            $array_sectors[] = $get_sector_access_array->sector;
        }
        $ci->cache->save($key, $array_sectors, 3600);
        return $array_sectors;
    }
    
    public function get_sector_location($id_group_location){
        $this->db->select('id_sector');
        $this->db->from('game_locations');
        $this->db->where('id_group', $id_group_location);
        $this->db->order_by('id_sector', 'asc');
        $this->db->limit(1); 
        $id_sector_array = $this->db->get();
        $id_sector_row = $id_sector_array->row();
        return $id_sector_row->id_sector;
    }
    
    public function get_all_locations(){
        $ci = get_instance();
        $cached = $ci->cache->get('game_locations_all');
        if ($cached !== false) return new Cached_result($cached);
        $query = $this->db->select('*')->from('game_locations')->get();
        $ci->cache->save('game_locations_all', $query->result_array(), 86400);
        return $query;
    }
    
    /**
     * open_item_db   Opens the item based on the id of the item, the table and the current player
     * 
     * @param type $id_resort           Resort's ID
     * @param type $table               Table name (game_created_slopes/game_created_lifts)
     * @param type $id_item             ID of the item in game_created_item table
     * @param type $id_name_in_table    id_slope or id_lift
     * @return string|boolean           returns true or 'unknown_error_db'
     */
    public function open_item_db($id_resort, $table, $id_item, $id_name_in_table){                    // Opens the item in the database
        if ($id_name_in_table == 'id_lift') // The column name for the lift table is id_group instead of id_slope for the slope table
            $id_name_in_table = 'id_group_location';
        // $sql = "UPDATE ".$table." SET id_status = 1 WHERE id_resort = '" . $id_resort . "' AND " . $id_name_in_table . " = '" . $id_item . "' LIMIT 1";
        //$this->db->query($sql);
        $this->db->set('id_status', '1');
        $this->db->where('id_resort' , $id_resort);  
        $this->db->where($id_name_in_table , $id_item);  
        $this->db->limit(1); 
        $this->db->update($table);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return 'unknown_error_db';                 // This should never happen
        }
    }
    
    /**
     * close_item_db   Closes the item based on the id of the item, the table and the current player
     * 
     * @param type $id_resort           Resort's ID
     * @param type $table               Table name (game_created_slopes/game_created_lifts)
     * @param type $id_item             ID of the item in game_created_item table
     * @param type $id_name_in_table    id_slope or id_lift
     * @return string|boolean           returns true or 'unknown_error_db'
     */
    public function close_item_db($id_resort, $table, $id_item, $id_name_in_table){                    // Opens the item in the database
        if ($id_name_in_table == 'id_lift') // The column name for the lift table is id_group instead of id_slope for the slope table
            $id_name_in_table = 'id_group_location';
        //$sql = "UPDATE ".$table." SET id_status = 2 WHERE id_resort = '" . $id_resort . "' AND " . $id_name_in_table . " = '" . $id_item . "' LIMIT 1";
        //$this->db->query($sql);
        $this->db->set('id_status', '2');
        $this->db->where('id_resort' , $id_resort);  
        $this->db->where($id_name_in_table , $id_item);  
        $this->db->limit(1); 
        $this->db->update($table);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return 'unknown_error_db';                 // This should never happen
        }
    }
    
    public function get_purchased_equipment_sector($id_resort, $type, $sector_id){        
        $this->db->select('*');
        $this->db->where('id_resort', $id_resort);
        $this->db->where('type', $type);
        $this->db->where('delivered', '1');
        $this->db->where('assigned_to_sector', $sector_id);
        $this->db->from('game_purchased_equipments');
        return $this->db->get();
    }
    
    public function get_associated_staff_DB($table, $condition_field1, $currentResortID, $condition_field2, $condition_value2, $condition_field3, $condition_value3){
            $query = $this->db
            ->select('*')
            ->from($table)
            ->where($condition_field1, $currentResortID)
            ->where($condition_field2, $condition_value2)
            ->where($condition_field3, $condition_value3)
            ->get();
        return $query;
    }
    public function get_id_group_location_for_slope_start($area){
            $query = $this->db
            ->select('id_location')
            ->from('game_locations')
            ->where('area', $area)
            ->get();
            //$result = $query->row();
            //$id_location = $result->id_location;
        return $query;
    }
    
    public function get_area_for_id_group($id_group_location){
            $query = $this->db
            ->select('area')
            ->from('game_locations')
            ->where('id_group', $id_group_location)
            ->order_by('id_location', 'desc')
            ->limit('1')
            ->get();
            $result = $query->row();
            $area = $result->area;
        return $area;
    }
    
    public function get_location_info_for_lift($id_created_lift){
        $query = $this->db
        ->select('id_group_location')
        ->from('game_created_lifts')
        ->where('id_created_lifts', $id_created_lift)
        ->get();
        $result = $query->row();
        $id_group_location = $result->id_group_location;
        return $id_group_location;
    }
    
    public function get_id_group_of_location($area){
        $this->db->select('id_group');
        $this->db->from('game_locations');
        $this->db->where('area', $area);
        $query = $this->db->get();
        return $query;
    }
        
    public function get_lift_info_id_group($currentResortID, $id_group){
        $this->db->select('id_created_lifts, custom_name, id_status, level, id_group');
        $this->db->from('game_created_lifts');
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_group_location', $id_group);
        $query = $this->db->get();
        return $query;
    }
    
    
    public function get_num_slopes_sector($id_resort, $sector_id){ 
        $this->db->distinct('game_slopes.id_slope, game_slopes.id_sector, game_created_slopes.id_slope, game_created_slopes.id_created_slopes, game_created_slopes.id_resort');
        $this->db->from('game_slopes');
        $this->db->join('game_created_slopes as created_slopes_tbl', 'game_slopes.id_slope = created_slopes_tbl.id_slope', 'inner');
        $this->db->where('created_slopes_tbl.id_status', '1');
        $this->db->where('game_slopes.id_sector', $sector_id);
        $this->db->where('created_slopes_tbl.id_resort', $id_resort);
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * get_avg_slope_condition_sector   Returns the average slope_condition for open slopes in a sector
     *
     * @param int $id_resort     Resort ID
     * @param int $sector_id     Sector ID
     * @return float|null        Average condition (0–100) or null if no open slopes
     */
    public function get_avg_slope_condition_sector($id_resort, $sector_id){
        $this->db->select('AVG(created_slopes_tbl.slope_condition) as avg_condition');
        $this->db->from('game_slopes');
        $this->db->join('game_created_slopes as created_slopes_tbl', 'game_slopes.id_slope = created_slopes_tbl.id_slope', 'inner');
        $this->db->where('created_slopes_tbl.id_status', '1');
        $this->db->where('game_slopes.id_sector', $sector_id);
        $this->db->where('created_slopes_tbl.id_resort', $id_resort);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->avg_condition;
        }
        return null;
    }

    public function get_location_info_for_slope($currentResortID, $id_slope){ 
        $this->db->select('game_locations.area, game_locations.id_group, game_slopes.start_location');
        $this->db->from('game_slopes, game_locations');
        $this->db->join('game_created_slopes as created_slopes_tbl', 'game_slopes.id_slope = created_slopes_tbl.id_slope AND game_slopes.start_location = game_locations.id_location', 'inner');
        $this->db->where('created_slopes_tbl.id_slope', $id_slope);
        $this->db->where('created_slopes_tbl.id_resort', $currentResortID);
        $query = $this->db->get();
        return $query;
    }
    
    
    public function get_hotel_capacity($currentResortID){ 
        $this->db->select('SUM(game_buildings.capacity) as total_capacity');
        $this->db->from('game_buildings');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_buildings.id_building = created_buildings_tbl.id_building', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '1');
        $this->db->where('game_buildings.type', 'hotel');
        $this->db->where('created_buildings_tbl.id_resort', $currentResortID);
        $query = $this->db->get();
        return $query;
    }
    
    public function get_built_slopes_player_start_location($currentResortID, $id_location_for_slope_start){ 
        
        $this->db->select('game_slopes.id_slope, created_slopes_tbl.id_created_slopes, game_slopes.start_location, game_slopes.length, created_slopes_tbl.slope_condition');
        $this->db->from('game_slopes');
        $this->db->join('game_created_slopes as created_slopes_tbl', 'game_slopes.id_slope = created_slopes_tbl.id_slope AND game_slopes.start_location = '.$id_location_for_slope_start, 'inner');
        //$this->db->where('created_slopes_tbl.id_slope', $id_slope);
        $this->db->where('created_slopes_tbl.id_resort', $currentResortID);
        $this->db->where('created_slopes_tbl.id_status', '1');
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query;
    }
    
    
    
    public function get_slope_list_map(){
        $this->db->distinct('game_slopes.*, difficulties_tbl.id_difficulty, difficulties_tbl.name');
        $this->db->from('game_slopes');
        $this->db->join('game_difficulties as difficulties_tbl', 'game_slopes.id_difficulty = difficulties_tbl.id_difficulty', 'inner');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_generic_lift_list_map($id_sector){
        $this->db->distinct('game_lifts.*, lift_types_tbl.name_english');
        $this->db->from('game_lifts');
        $this->db->join('game_lift_types as lift_types_tbl', 'game_lifts.lift_type = lift_types_tbl.id_lift_type', 'inner');
        if ($id_sector != '')     // ONly if filtering for a specific sector
            $this->db->where('id_sector', $id_sector);
        $query = $this->db->get();
        return $query;
    }
    
   
    
    /**
     * get_item_location    Returns all location rows for lifts in accessible sectors,
     *                      plus player-drawn lift locations specific to $id_resort.
     *
     * @param  string|null $where_statement  SQL fragment for sector filter (e.g. "(id_sector = 1 OR id_sector = 2)")
     * @param  int|null    $id_group
     * @param  string      $item_type        'lift' or 'slope' (kept for API compatibility)
     * @param  int|null    $id_resort        When provided, also returns rows where id_resort = this value
     * @return object  Query result
     */
    public function get_item_location($where_statement = null, $id_group = null, $item_type = null, $id_resort = null){
        if (isset($where_statement)) {
            // Global (id_resort IS NULL) entries in accessible sectors
            // + player-drawn entries (id_resort = $id_resort) regardless of sector
            $has_id_resort = $this->db->field_exists('id_resort', 'game_locations');
            if ($id_resort !== null && $has_id_resort) {
                $inner_where = '('.$where_statement.' AND id_resort IS NULL) OR id_resort = '.(int)$id_resort;
            } else {
                $inner_where = $where_statement;
            }
            $get_location = $this->db->query(
                "SELECT * FROM game_locations WHERE id_group IN (SELECT DISTINCT id_group FROM game_locations WHERE ".$inner_where.") ORDER BY id_group, id_location"
            );
        } else {
            $get_location = $this->db->query(
                "SELECT * FROM game_locations WHERE id_group IN (SELECT DISTINCT id_group FROM game_locations) ORDER BY id_group, id_location"
            );
        }
        return $get_location;
    }
    
    public function get_lift_length_DB($id_group_location){
        $this->db->select('length');
         $this->db->from('game_locations');
         $this->db->where('id_group', $id_group_location);
         $query = $this->db->get();
        return $query;
    }
    
    
    public function give_season_bonus_db($currentResortID, $season_bonus){
        $this->db->trans_start();
        $this->db->set('cash', 'cash+'.$season_bonus,FALSE);
        $this->db->where('id_resort' , $currentResortID);      
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * get_legacy_data_DB       Returns the legacy_rating and legendary_status for a resort
     *
     * @param int $id_resort    Resort ID
     * @return object|null      Row with legacy_rating and legendary_status, or null
     */
    public function get_legacy_data_DB($id_resort){
        $query = $this->db
            ->select('legacy_rating, legendary_status')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get();
        return $query->row();
    }

    /**
     * set_legacy_rating_DB     Stores the historical rating and legendary status for a resort
     *
     * @param int $id_resort        Resort ID
     * @param int $rating           Legacy rating (0-100)
     * @param int $legendary_status 1 if Legendary Mountain unlocked, 0 otherwise
     * @return int|false            Affected rows or false on failure
     */
    public function set_legacy_rating_DB($id_resort, $rating, $legendary_status){
        $this->db->trans_start();
        $this->db->set('legacy_rating', (int)$rating);
        $this->db->set('legendary_status', (int)$legendary_status);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        return $updated_rows;
    }

    /**
     * get_water_reservoir_DB   Returns the current water reservoir level for a resort (0-100).
     *
     * @param  int $id_resort
     * @return int   Percentage full (0 = empty, 100 = full); defaults to 100 if column missing
     */
    public function get_water_reservoir_DB($id_resort) {
        try {
            $query = $this->db
                ->select('water_reservoir')
                ->from('game_resorts')
                ->where('id_resort', $id_resort)
                ->get();
            $row = $query->row();
            return $row ? max(0, min(100, (int)$row->water_reservoir)) : 100;
        } catch (Exception $e) {
            return 100;
        }
    }

    /**
     * update_water_reservoir_DB    Updates the water reservoir level for a resort.
     *
     * @param  int $id_resort
     * @param  int $level   New level (0-100); clamped to valid range
     * @return bool
     */
    public function update_water_reservoir_DB($id_resort, $level) {
        $level = max(0, min(100, (int)$level));
        $this->db->trans_start();
        $this->db->set('water_reservoir', $level);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE);
    }

    /**
     * get_water_reservoir_purchased_DB     Returns whether the resort has purchased a water reservoir.
     *
     * @param  int $id_resort
     * @return bool
     */
    public function get_water_reservoir_purchased_DB($id_resort) {
        $result = $this->db
            ->select('water_reservoir_purchased')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get();
        $row = $result->row();
        return $row ? ((int)$row->water_reservoir_purchased === 1) : false;
    }

    /**
     * set_water_reservoir_purchased_DB     Marks the resort's water reservoir as purchased.
     *
     * @param  int $id_resort
     * @return bool
     */
    public function set_water_reservoir_purchased_DB($id_resort) {
        $this->db->where('id_resort', $id_resort);
        return $this->db->update('game_resorts', ['water_reservoir_purchased' => 1]);
    }

    /**
     * get_resort_level_DB  Returns the resort level (1–3+) based on the number
     *                      of open lifts.  Used to gate features that require a
     *                      minimum resort size, e.g. municipal water refill.
     *
     * Level 1 : 0–1 open lifts
     * Level 2 : 2   open lifts
     * Level 3+: 3+  open lifts
     *
     * @param  int $id_resort
     * @return int  Resort level (minimum 1)
     */
    public function get_resort_level_DB($id_resort) {
        $open_lifts = $this->db
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', 1)
            ->count_all_results('game_created_lifts');

        if ($open_lifts >= 3) return 3;
        if ($open_lifts >= 2) return 2;
        return 1;
    }

    /**
     * apply_municipal_water_penalties_DB   Deducts the reputation penalty from
     *                                      game_resorts and the eco reputation
     *                                      penalty from game_resort_environment
     *                                      for a municipal water refill use.
     *
     * @param  int $id_resort
     * @return bool
     */
    public function apply_municipal_water_penalties_DB($id_resort) {
        $id_resort = (int)$id_resort;

        $this->db->trans_start();

        // Resort reputation penalty (slightly negative public opinion)
        $this->db->set('reputation', 'GREATEST(reputation - ' . (int)MUNICIPAL_WATER_REP_PENALTY . ', 0)', FALSE);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');

        // Eco reputation penalty (negative environmental impact)
        $this->db->set('eco_reputation', 'GREATEST(eco_reputation - ' . (int)MUNICIPAL_WATER_ECO_PENALTY . ', 0)', FALSE);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resort_environment');

        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE);
    }

    
    /**
     * insert_drawn_lift_location   Inserts two rows (start + end) into game_locations
     *                              for a player-drawn lift, generating a new id_group.
     *
     * @param  int   $id_resort
     * @param  int   $id_sector
     * @param  float $x_start
     * @param  float $y_start
     * @param  float $x_end
     * @param  float $y_end
     * @param  int   $length_meters   Pre-calculated length in meters
     * @return int   The new id_group assigned to this lift path
     */
    public function insert_drawn_lift_location($id_resort, $id_sector, $x_start, $y_start, $x_end, $y_end, $length_meters) {
        $row = $this->db->select_max('id_group', 'max_id_group')->from('game_locations')->get()->row();
        $new_id_group = intval($row->max_id_group) + 1;

        $this->db->trans_start();
        $this->db->insert('game_locations', [
            'id_group'      => $new_id_group,
            'id_sector'     => (int)$id_sector,
            'x_coordinates' => (float)$x_start,
            'y_coordinates' => (float)$y_start,
            'length'        => (int)$length_meters,
            'area'          => 0,
            'id_resort'     => (int)$id_resort,
        ]);
        $this->db->insert('game_locations', [
            'id_group'      => $new_id_group,
            'id_sector'     => (int)$id_sector,
            'x_coordinates' => (float)$x_end,
            'y_coordinates' => (float)$y_end,
            'length'        => (int)$length_meters,
            'area'          => 0,
            'id_resort'     => (int)$id_resort,
        ]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $new_id_group;
    }

    /**
     * insert_drawn_slope   Inserts a player-drawn slope into game_slopes.
     *
     * @param  int    $id_resort
     * @param  int    $id_sector
     * @param  int    $slope_type    1=downhill, 2=snowpark, 3=boardercross, 4=crosscountry, 5=luge, 6=terrain park
     * @param  string $path          Coordinate string in format "[lng,lat],[lng,lat],..."
     * @param  int    $length_meters
     * @return int|false  New id_slope on success, false on failure
     */
    public function insert_drawn_slope($id_resort, $id_sector, $slope_type, $path, $length_meters) {
        $data = [
            'id_resort'      => (int)$id_resort,
            'id_sector'      => (int)$id_sector,
            'slope_type'     => (int)$slope_type,
            'path'           => $path,
            'length'         => (int)$length_meters,
            'name_english'   => 'Custom Slope',
            'name_french'    => 'Piste Personnalisée',
            'start_location' => 0,
            'end_location'   => 0,
            'reputation'     => 10,
        ];
        $insert = $this->db->insert('game_slopes', $data);
        if (!$insert) {
            return false;
        }
        return $this->db->insert_id();
    }
}