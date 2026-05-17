<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * is_easy_mode   Returns TRUE when the logged-in player has chosen easy/simplified difficulty.
 */
if (!function_exists('is_easy_mode')) {
    function is_easy_mode() {
        $CI =& get_instance();
        return (bool) $CI->session->userdata('difficulty_mode');
    }
}

if(!function_exists('display_friendly_time')){
  /**
     * display_friendly_time    Displays a timestamp into a nice name ( day, hour, minute, second)
     * 
     * @param type $diff    Time difference between the end and now
     * 
     * @return string       The nice string returned
     */
    function display_friendly_time($diff, $time_left_text = NULL){
        $ci=& get_instance();
        // Get the current language
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');            // Store current language in variable
        } else {
            $siteLang = 'english';                                      // If no session, use English
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->load->helper('language');
        $ci->lang->load('slope',$siteLang); // this is where hour/min/sec is located
        if (is_numeric($diff)) {
            $j = intval($diff / 86400);
            $h = intval(($diff - ($j * 86400)) / 3600);
            $mn = intval(($diff - (($j * 86400 + $h * 3600))) / 60);
            $sec = intval($diff - (($j * 86400 + $h * 3600 + $mn * 60)));
            $display = $time_left_text.' ';
            if ($j == 1)
                    $display .= $j." ".$ci->lang->line('home')['small_day']." ";
            else if ($j > 1)
                    $display .= $j." ".$ci->lang->line('home')['days']." ";
            if ($h == 1)
                    $display .= $h." ".$ci->lang->line('home')['hour']." ";
            else if ($h > 1)
                    $display .= $h." ".$ci->lang->line('home')['hours']." ";
            if ($mn == 1)
                    $display .= $mn." ".$ci->lang->line('home')['minute']." ";
            else if ($mn > 1)
                    $display .= $mn." ".$ci->lang->line('home')['minutes']." ";
            if ($sec == 1 && $h == 0 && $j == 0)                            // only display seconds if at least an hour left (useless when a long time is left)
                    $display .= $sec." ".$ci->lang->line('home')['second']." ";
            else if ($sec > 1 && $h == 0 && $j == 0)                        // only display seconds if at least an hour left (useless when a long time is left)
                    $display .= $sec." ".$ci->lang->line('home')['seconds']." ";

            if ($display == '' || $display == ' ')
                $display = $ci->lang->line('home')['wait'];
            return $display;
        }
        else        // If not numeric value
            return $diff;
    }   
}


if(!function_exists('display_friendly_cash')){
  /**
     * display_friendly_cash    Displays a large integer into a nicely formatted grouped number (K, M, T)
     * 
     * @param type $value    The value to format
     * 
     * @return string       The nice string returned
     */
    function display_friendly_cash($value){
        
        $ci=& get_instance();
        // Get the current language
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');            // Store current language in variable
        } else {
            $siteLang = 'english';                                      // If no session, use English
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->load->helper('language');
        $ci->lang->load('slope',$siteLang); // this is where en: K/M/B or fr: k/M/G is located
        if (is_numeric($value)) {
            $thousand = intval($value / 1000);
            $million = intval($value / 1000000);
            $billion = intval($value / 1000000000);
            $display = '';
            
            if ($thousand >= 1 && $thousand < 1000)
                $display .= $thousand." ".$ci->lang->line('home')['thousand_short'];
            
            if ($million >= 1 && $million < 1000) {
                $million = $value/1000000;
                $display .= $million." ".$ci->lang->line('home')['million_short'];
            }
            
            if ($billion >= 1) {
                $billion = $value/1000000000;
                $display .= $billion." ".$ci->lang->line('home')['billion_short'];
            }

            if ($display == '' || $display == ' ')
                $display = $ci->lang->line('home')['wait'];
            return $display;
        }
        else        // If not numeric value
            return $value;
    }   
}

if(!function_exists('calculate_end_construction')){
    /**
     * calculate_end_construction   Calculate the end of the construction
     * 
     * @param type $type        ID of the building type (e.g tourist info center = 1, access = 2, hotel = 3, restaurant = 4, rental =5, leisure =6)
     * @param type $level       ID of the level of the building to check
     * @return boolean          Return false or the date if it works
     */
    function calculate_end_construction($type, $level){
        $ci=& get_instance();
        $get_generic_building_data = $ci->building_model->get_generic_building_data($type, $level);
        $current_time = time();
        if ($get_generic_building_data->num_rows() > 0) {                // the generic building exists in the DB (always!)
            $get_generic_building_dataArray = $get_generic_building_data->row();    
            $time_construction_duration = $get_generic_building_dataArray->building_time/ACCELERATOR_FACTOR;                 // store the building time of the building       
            $end_construction_timestamp = $current_time + $time_construction_duration;      // when the construction is supposed to end (timestamp format)
            //$end_construction_datetime = date('Y-m-d H:i:s', $end_construction_timestamp);  // when the construction is supposed to end (value to put in DB)
            return gmdate('Y-m-d H:i:s',$end_construction_timestamp);
        }
        else {
            return false;
            
        }
    }
}

if(!function_exists('calculate_end_delivery')){
    /**
     * calculate_end_delivery   Calculate the end of the delivery
     * 
     * @param type $type        ID of the equipment type (e.g tgroomer = 1)
     * @param type $level       ID of the level of the equipment to check
     * @return boolean          Return false or the date if it works
     */
    function calculate_end_delivery($type, $level){
        $ci=& get_instance();
        $get_generic_equipment_data = $ci->equipment_model->get_generic_equipment_data($type, $level);
        $current_time = time();
        if ($get_generic_equipment_data->num_rows() > 0) {                // the generic building exists in the DB (always!)
            $get_generic_equipment_dataArray = $get_generic_equipment_data->row();    
            $time_delivery_duration = $get_generic_equipment_dataArray->delivery_time/ACCELERATOR_FACTOR;                 // store the building time of the building       
            $end_delivery_timestamp = $current_time + $time_delivery_duration;      // when the delivery is supposed to end (timestamp format)
            //$end_delivery_datetime = date('Y-m-d H:i:s', $end_delivery_timestamp);  // when the delivery is supposed to end (value to put in DB)
            return gmdate('Y-m-d H:i:s',$end_delivery_timestamp);
        }
        else {
            return false;
            
        }
    }
}

if(!function_exists('display_friendly_status')){
    /**
     * display_friendly_status   Displays a friendly name for the status
     * 
     * @param type $idstatus        ID of the status
     * @return boolean          Return false or the status if OK
     */
    function display_friendly_status($idstatus){
        $ci=& get_instance();
        
        // Get the status of the lift and gives a nice name
        if ($idstatus == 1) {
            $friendly_status = 'open';
        }
        else if ($idstatus == 2) {
            $friendly_status = 'closed';
        }
        else if ($idstatus == 3) {
            $friendly_status = 'maintenance';
        }
        else if ($idstatus == 4) {             // if under construction, we display how long is left to achieve the construction
            $friendly_status = 'under_construction';
        }
        else if ($idstatus == 6) {             // if closed for safety reason (bad weather / premium player)
            $friendly_status = 'closed_safety';
        }
        return $friendly_status;
    }
}

if(!function_exists('get_time_left_for_building')){
   /**
     * get_time_left_for_building       Get the end date/time of the construction
     * 
     * @param type $currentResortID       Current resort ID
     * @param type $type                ID type od building
     * @param type $level               Level to check
     * @return type                     Date/time format returned
     */
    function get_time_left_for_building($currentResortID, $type, $level){
        $ci=& get_instance();
        $time_left_query = $ci->building_model->get_time_left_for_building_db($currentResortID, $type, $level);   // Gets the time left for this building
        if ($time_left_query->num_rows() > 0) { 
            $time_left_Array = $time_left_query->row();
        }
        return $time_left_Array->end_construction;
    } 
}

if(!function_exists('get_time_left_for_delivery')){
   /**
     * get_time_left_for_delivery       Get the end date/time of the delivery for the equipment
     * 
     * @param type $currentResortID       Current resort ID
     * @param type $type                ID type od equipment
     * @param type $level               Level to check
     * @return type                     Date/time format returned
     */
    function get_time_left_for_delivery($currentResortID, $type, $level){
        $ci=& get_instance();
        $time_left_query = $ci->equipment_model->get_time_left_for_equipment_db($currentResortID, $type, $level);   // Gets the time left for this building
        if ($time_left_query->num_rows() > 0) { 
            $time_left_Array = $time_left_query->row();
        }
        return $time_left_Array->end_delivery;
    } 
}



if(!function_exists('count_this_building_level')){
   /**
     * count_this_building_level       counts how many of this building the player has (could be any or specific level/status)
     * 
     * @param type $type        id_type of the building (3=hotel..)
     * @param type $level       level to check (optional)
     * @param type $id_status   id_status to check (optional). if omitted, NULL.
     * @return type             Return the value (integer)
     */
    function count_this_building_level($type, $level = NULL, $id_status = NULL, $match_level='both', $match_status='none'){
        $ci=& get_instance();
        $currentUserID = $ci->users_model->get_user_id();
        $currentResortID = $ci->users_model->get_resort_id($currentUserID);
        if ($id_status == NULL && $level != '') {
            // count of many of this building and this level the player has.
            // the ID type should match but ID status doesn't matter. Includes under construction
            $building_info_data = $ci->building_model->count_this_building_level_db($currentResortID, $type, $level, $id_status, 'none', 'both');   
        }
        else if ($id_status == NULL && $level == NULL) {
            // count of many of this building the player has. included any level and any status
            $building_info_data = $ci->building_model->count_this_building_level_db($currentResortID, $type, '', '', 'both', 'both');   
        }
        else if ($id_status == '4' && $level != '') {
            // count of many of this building and this level the player has, that are not under construction
            // the ID type and ID status should match
            $building_info_data = $ci->building_model->count_this_building_level_db($currentResortID, $type, $level, $id_status, 'none', 'none');
        }
        else {
            // count of many of this building and ANY level the player has, that are under construction or not
            $building_info_data = $ci->building_model->count_this_building_level_db($currentResortID, $type, '', $id_status, 'both', 'none');  
        }
        return $building_info_data;     // returns integer
    }
}

if(!function_exists('count_this_equipment_level')){
   /**
     * count_this_equipment_level       counts how many of this equipment the player has (could be any or specific level)
     * 
     * @param type $type        id_type of the building (3=hotel..)
     * @param type $level       level to check (optional)
     * @return type             Return the value (integer)
     */
    function count_this_equipment_level($type, $level = NULL, $condition=NULL){
        $ci=& get_instance();
        $currentUserID = $ci->users_model->get_user_id();
        $currentResortID = $ci->users_model->get_resort_id($currentUserID);
            // count of many of this equipment and this level the player has.
        if ($condition != NULL)
            $equipment_info_data = $ci->equipment_model->count_this_equipment_level_db($currentResortID, $level, $type, $condition); 
        else
            $equipment_info_data = $ci->equipment_model->count_this_equipment_level_db($currentResortID, $level, $type, NULL);   
        
        return $equipment_info_data;     // returns integer
    }
}

if(!function_exists('count_ongoing_building_items')){
   /**
     * count_ongoing_building_items       counts how many items the player is building
     * 
     * @param type $type        name of the type of item ("slope" for "game_created_slopes" table ...)
     * @return type             Return the value (integer)
     */
    function count_ongoing_building_items($type, $condition=NULL){
        $ci=& get_instance();
        $currentUserID = $ci->users_model->get_user_id();
        $currentResortID = $ci->users_model->get_resort_id($currentUserID);
            // count of many items are under contruction for this player
            $slope_info_data = $ci->resort_model->count_ongoing_items_db($currentResortID, $type, $condition);
        
        return $slope_info_data;     // returns integer
    }
}

if(!function_exists('get_day_of_season')){
   /**
     * get_day_of_season        Gets the current day of the season.
     * 
     * @param type $currentResortID        Resort ID to check
     * @return type             Return the value (integer)
     */
    function get_day_of_season($currentResortID){
        $ci=& get_instance();
        $day_of_season = $ci->users_model->get_day_of_season($currentResortID);
        if ($day_of_season >= 136) {
            $day_of_season = 135;
        }
        return $day_of_season;     // returns integer
        
    }
}

if(!function_exists('calculate_prestige_bonus')){
   /**
     * get_day_of_season        Calculates the prestige bonus of the player
     * 
     * @param type $currentResortID        Resort ID to check
     * @return type             Return the value (float)
     */
    function calculate_prestige_bonus($currentResortID){
        $ci=& get_instance();
        $prestige_player = $ci->users_model->get_prestige_resort($currentResortID);
        $prestige_bonus['coef'] = 1 + PRESTIGE_COEF * $prestige_player;
        $prestige_bonus['coef'] = min ($prestige_bonus['coef'], 3);
        $prestige_bonus['percentage'] = number_format(($prestige_bonus['coef']-1)*100, 1, ',', ' ');
        return $prestige_bonus;     // returns bonus value (float)
        
    }
}

if(!function_exists('get_genepis')){
   /**
     * get_day_of_season        Gets the current amount of Genepis the player has
     * 
     * @param type $currentUserID        Player ID to check
     * @return type             Return the value (integer)
     */
    function get_genepis($currentUserID){
        $ci=& get_instance();
        $genepis = $ci->users_model->get_user_genepis_amount($currentUserID);    
        return $genepis;     // returns integer
        
    }
}

if(!function_exists('get_current_season')){
   /**
     * get_current_season               Gets the highest season value
     * 
     * @param type $currentResortID        resort ID
     * @return type             Return the value (integer)
     */
    function get_current_season($currentResortID){
        $ci=& get_instance();
        $season = $ci->users_model->get_current_season($currentResortID);    
        return $season;     // returns integer
    }
}


if(!function_exists('get_visitors_yesterday')){
   /**
     * get_visitors_yesterday               Gets the number of visitors from yesterday
     * 
     * @param type $currentResortID        resort ID
     * @return type                     Return the value (integer)
     */
    function get_visitors_yesterday($currentResortID){
        $ci=& get_instance();
        $season = $ci->users_model->get_visitors_yesterday($currentResortID);    
        return $season;     // returns integer
    }
}

if(!function_exists('get_current_season_start_date')){
   /**
     * get_current_season_start_date               Gets the highest season start_date
     * 
     * @param type $currentResortID        resort ID
     * @return type             Return the value (gmdate format)
     */
    function get_current_season_start_date($currentResortID){
        $ci=& get_instance();
        $season_start_date = $ci->users_model->get_current_season_start_date_DB($currentResortID);  
        return $season_start_date;     // returns integer
    }
}


if(!function_exists('add_cost_stat_table')){
   /**
     * add_cost_stat_table               Adds any kind of cost to the main cost table
     * 
     * @param type $id_resort    Current resort ID
     * @param type $cost         Amount in euros to add
     * @param type $cost_type         Type: cost_upkeep, cost_salaries...
     * @return type                 Returns the transaction result
     */
    function add_cost_stat_table($id_resort, $cost, $cost_type, $current_date = NULL){
        $ci=& get_instance();
        if (!$current_date)
            $current_date = gmdate('Y-m-d');
        $record_exists = $ci->resort_model->check_if_record_exists($id_resort, $cost_type, $current_date);
        if ($record_exists) { // UPDATE: There is an entry matching the ID resort and the date
            if ($cost != 0) {   // Only if different from 0, to avoid useless queries
                $ci->db->trans_start();
                $ci->db->set($cost_type, $cost_type.'+'.$cost, FALSE);
                $ci->db->where('id_resort', $id_resort);
                $ci->db->where('date' , $current_date);
                $ci->db->update('game_resort_'.$cost_type); 
                $ci->db->trans_complete();
            }
        }
        else { // INSERT: There is no entry matching the ID resort and the date
            $ci->db->trans_start();
            $data = array ('id_resort' => $id_resort, 'date' => $current_date, $cost_type => $cost);
            $query = $ci->db->insert('game_resort_'.$cost_type, $data);
            $ci->db->trans_complete();
        }
        if ($ci->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
}


if(!function_exists('friendly_name')){
   /**
     * friendly_name               Changes to a friendly name (tourist_info = Tourist information Center)
     * 
     * @param type $id_resort    Current resort ID
     * @param type $cost         Amount in euros to add
     * @param type $cost_type         Type: cost_upkeep, cost_salaries...
     * @return type                 Returns the transaction result
     */
    function friendly_name($id_resort, $cost, $cost_type){
        $ci=& get_instance();
        $current_date = gmdate('Y-m-d');
        $record_exists = $ci->resort_model->check_if_record_exists($id_resort, $cost_type, $current_date);
        if ($record_exists) { // UPDATE: There is an entry matching the ID resort and the date
            $ci->db->trans_start();
            $ci->db->set($cost_type, $cost_type.'+'.$cost, FALSE);
            $ci->db->where('id_resort', $id_resort);
            $ci->db->where('date' , $current_date);
            $ci->db->update('game_resort_'.$cost_type); 
            $ci->db->trans_complete();
        }
        else { // INSERT: There is no entry matching the ID resort and the date
            $ci->db->trans_start();
            $data = array ('id_resort' => $id_resort, 'date' => $current_date, $cost_type => $cost);
            $query = $ci->db->insert('game_resort_'.$cost_type, $data);
            $ci->db->trans_complete();
        }
        if ($ci->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
}

if(!function_exists('add_revenue_stat_table')){
   /**
     * add_revenue_stat_table               Adds any kind of revenue to some stat revenue table (for each day revenue stats)
     * 
     * @param type $id_resort    Current resort ID
     * @param type $revenue         Amount in euros to add
     * @param type $table         Table to update
     * @return type                 Returns the transaction result
     */
    function add_revenue_stat_table($id_resort, $revenue, $type, $current_date = NULL){
        $ci=& get_instance();
        if (!$current_date)
            $current_date = gmdate('Y-m-d');
        $record_exists = $ci->resort_model->check_if_record_exists($id_resort, $type, $current_date);
        if ($record_exists) { // UPDATE: There is an entry matching the ID resort and the date
            if ($revenue != 0) {   // Only if different from 0, to avoid useless queries
                $ci->db->trans_start();
                $ci->db->set($type, $type.'+'.$revenue, FALSE);
                $ci->db->where('id_resort', $id_resort);
                $ci->db->where('date' , $current_date);
                $ci->db->update('game_resort_'.$type); 
                $ci->db->trans_complete();
            }
        }
        else { // INSERT: There is no entry matching the ID resort and the date
            $ci->db->trans_start();
            $data = array ('id_resort' => $id_resort, 'date' => $current_date, $type => $revenue);
            $query = $ci->db->insert('game_resort_'.$type, $data);
            $ci->db->trans_complete();
        }
        if ($ci->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
}

if(!function_exists('getRandomWeightedElement')){
    function getRandomWeightedElement(array $weightedValues){
        $rand = mt_rand(1, (int) array_sum($weightedValues));
        foreach ($weightedValues as $key => $value) {
          $rand -= $value;
          if ($rand <= 0) {
            return $key;
          }
        }
    }
}

/**
 * set_session      Creates a new session with username and email
 * 
 * @param type $username
 * @param type $email
 */
if(!function_exists('set_session')){
    function set_session($username, $email){
        $ci=& get_instance();
        // we select registration_time to set the private $email_code variable
        $result = $ci->db->select('username, email, registration_time')->where('email', $email)->limit(1)->get('game_players');
        $row = $result->row();
        $sess_data = [         // We create the session data
            'username' => $username,
            'email' => $email,
            'is_logged_in' => 0     // By default the user won't be logged in
        ];
        $ci->email_code = md5((string)$row->registration_time);   // Creates the email code
        $ci->session->set_userdata($sess_data);                   // Creates the session
    }
}



if(!function_exists('call_achievements_check')){
   /**
     * call_achievements_check               
     * 
     * @param type $id_resort    Current resort ID
     * @param type $revenue         Amount in euros to add
     * @param type $table         Table to update
     * @return type                 Returns the transaction result
     */
    function call_achievements_check($data, $action){
        $ci=& get_instance();
        
        /* $ci->db->trans_start(); */
        $currentUserID = $ci->users_model->get_user_id();
        switch ($action):
            case 'publish_level':
                $currentResortID = $data['id_resort'];
                $item = $data['item'];          // marketing_campaign
                $level = $data['level'];
                $progress = 100;
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"item":"'.$item.'"') 
                    ->like('requires', '"level":"'.$level.'"')   
                    ->from('achievements')
                    ->get();
                break;
            case 'publish_number':
                $currentResortID = $data['id_resort'];
                $item = $data['item'];          // marketing_campaign
                $amount_spent = 1;
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"item":"'.$item.'"')   
                    ->from('achievements')
                    ->get();
                break;
            case 'buy':
                $currentResortID = $data['id_resort'];
                $item = $data['item'];
                $type = $data['type'];
                $level = $data['level'];
                $amount_spent = 1;
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"item":"'.$item.'"') 
                    ->like('requires', '"type":"'.$type.'"') 
                    ->like('requires', '"level":"'.$level.'"')   
                    ->from('achievements')
                    ->get();
                break;
            case 'total_amount':
                $type = $data['type'];
                $amount_spent = $data['quantity'];
                $progress = '';        
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"type":"'.$type.'"')
                    ->from('achievements')
                    ->get(); 
                $currentUserID = $data['currentUserID']; 
                //echo '$data_quantity: '.$data['quantity'];
                break;
            case 'single_amount':
                $type = $data['type'];
                $amount_spent = $data['quantity'];
                $progress = '';        
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"type":"'.$type.'"')
                    ->from('achievements')
                    ->get(); 
                $currentUserID = $data['currentUserID']; 
                break;
            case 'hire':
                $staff_position = $data['position'];
                //$progress = 100;
                $amount_spent = '1';                 // ??
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"position":"'.$staff_position.'"')
                    ->from('achievements')
                    ->get(); 
                break;
            case 'fire':
                $staff_position = $data['position'];
                //$progress = 100;
                $amount_spent = '1';                 // ??
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"position":"'.$staff_position.'"')
                    ->from('achievements')
                    ->get();
                break;
            case 'assign_staff':
                $staff_position = $data['position'];
                //$progress = 100;
                $amount_spent = '1';                 // ??
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"position":"'.$staff_position.'"')
                    ->from('achievements')
                    ->get();
                break;
            case 'assign_equipment':
                $sector = $data['sector_id'];  // sector ID
                $type = $data['type'];      // 1 or 2
                //$progress = 100;
                $amount_spent = '1';                 // ??
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"type":"'.$type.'"')
                    ->like('requires', '"sector":"'.$sector.'"')
                    ->from('achievements')
                    ->get(); 
                break;
            case 'close':
            case 'open':
                if (isset($data['id_sector']))
                    $id_sector = $data['id_sector'];
                if (isset($data['type']))
                    $type = $data['type'];
                if (isset($data['level']))
                    $level = $data['level'];
                else
                    $level = '*';
                //$progress = 100;
                $currentResortID = $data['id_resort'];
                
                if ($type == 'slope')
                    $nb_open_items = count_nb_open_slopes_sector($id_sector, $currentResortID);
                else if ($type == 'lift')
                    $nb_open_items = count_nb_open_lifts_sector($id_sector, $currentResortID);
                else if ($type == 'tourist_info')
                    $nb_open_items = 1;
                else 
                    $nb_open_items = 0;
                //echo $ci->db->last_query();
                $amount_spent = $nb_open_items;                 // Not used
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"item":"'.$type.'"') 
                    ->like('requires', '"level":"'.$level.'"') 
                    ->like('requires', '"sector":"'.$id_sector.'"')   
                    ->from('achievements')
                    ->get();
                break;
           
            case 'repair':
            case 'build':
            case 'upgrade':            
                if (isset($data['type']))
                    $type = $data['type'];
                else
                    $type = '*';
                    $amount_spent = '1';
                if (isset($data['level']))
                    $level = $data['level'];
                else
                    $level = '*';
                if (isset($data['id_sector']))
                    $id_sector = $data['id_sector'];
                else
                    $id_sector = '*';
                $progress = 100;
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"item":"'.$type.'"') 
                    ->like('requires', '"level":"'.$level.'"') 
                    ->like('requires', '"sector":"'.$id_sector.'"')   
                    ->from('achievements')
                    ->get();
                break;
            case 'build_amount':
                $amount_spent = $data['quantity'];              // Amount the use just spent
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"') 
                    ->from('achievements')
                    ->get(); 
                break;
            case 'buy_amount':
                $amount_spent = $data['quantity'];              // Amount the use just spent
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"') 
                    ->from('achievements')
                    ->get(); 
                break;
            case 'upgrade_amount':
                $amount_spent = $data['quantity'];              // Amount the use just spent
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"') 
                    ->from('achievements')
                    ->get(); 
                break;
            case 'resort':
                //$email = '';
                $type = $data['type'];
                $progress = 100;               // Not used
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"type":"'.$type.'"')   
                    ->from('achievements')
                    ->get();
                break;
            case 'account':
                $type = $data['type'];
                if (!isset($email))
                    $email = $data['email'];
                $progress = 100;               // Not used
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"type":"'.$type.'"')   
                    ->from('achievements')
                    ->get();
                    $currentUserID = $ci->users_model->get_user_id_from_email($email); 
                break;
            case 'unlock_sector':
            case 'unlock_item':
                //$progress = 100;
                //$amount_spent = '1';                 // ??
                $achievementID = $data['id_achievement'];
                $currentResortID = $data['id_resort'];
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    //->like('requires', '"achievement_list":"'.$achievementID.'"')
                    ->from('achievements')
                    ->get();
                break;
            case 'earn_genepis':
                $amount_spent = $data['quantity'];              // Amount the use just bought/earned
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"') 
                    ->from('achievements')
                    ->get(); 
                break;
            case 'invite_friend':
                $amount_spent = $data['quantity'];
                $currentUserID = $data['currentUserID'];
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"') 
                    ->from('achievements')
                    ->get(); 
                break;
            case 'confirmed_active_referred_player':
                $progress = 100;
                $currentUserID = $data['currentUserID'];
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"') 
                    ->from('achievements')
                    ->get(); 
                break;
            case 'build_slope_type':            
                $id_slope_type = $data['id_slope_type'];
                $amount_spent = $data['quantity'];
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"id_slope_type":"'.$id_slope_type.'"') 
                    //->like('requires', '"quantity":"1"') 
                    ->from('achievements')
                    ->get();
                break;
            case 'open_slope_type':            
                $id_slope_type = $data['id_slope_type'];
                $amount_spent = $data['quantity'];
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"id_slope_type":"'.$id_slope_type.'"')
                    ->from('achievements')
                    ->get();
                break;
            case 'organize_tournament_quantity':            
                $id_tournament = $data['id_tournament'];
                $amount_spent = 1;
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->from('achievements')
                    ->get();
                break;
            case 'organize_specific_tournament':            
                $id_tournament = $data['id_tournament'];
                $amount_spent = 1;
                $get_achievements  = $ci->db
                    ->select('*')
                    ->like('requires', '"action":"'.$action.'"')
                    ->like('requires', '"id_tournament":"'.$id_tournament.'"')
                    ->from('achievements')
                    ->get();
                break;
            default:
                return FALSE;
        endswitch;
 
        // We don't need to check for the quantity as this is a triggered achievement. Quantity should only be used for cron scheduled achievements
        // Get achievements matching the current action in the DB
        $get_achievements_result = $get_achievements->result();
        //echo $ci->db->last_query();
        //echo '<br>ACHROWS : '.$get_achievements->num_rows().'<br>';
        // Create array of the query results
        if ($get_achievements->num_rows() > 0 && $currentUserID != FALSE) {                                // If the action has trigerred an achievement (at least one)
            foreach ($get_achievements_result as $get_achievements_resultData) {
                //echo ' currentUserID: '.$currentUserID;
                //echo ' action: '.$action;
                $a_id = $get_achievements_resultData->id_achievement;       // Achievement ID
                $a_requires = $get_achievements_resultData->requires;       // Contains of string of the different requirements to unlock
                $a_reward_rep = $get_achievements_resultData->reward_reputation;       // Contains reward of reputation (integer)
                $a_reward_cash = $get_achievements_resultData->reward_cash;       // Contains reward of cash (integer)
                $a_reward_genepis = $get_achievements_resultData->reward_genepis;       // Contains reward of cash (integer)
                $a_requires_decoded = json_decode($a_requires);             // Decode the string to create a associative array. That allows us to access any field.
                
                if ($a_requires_decoded === null) {
                    continue;   // Skip achievements with missing or invalid requires JSON
                }
                
                //echo '<br>$a_id:'.$a_id;
                //echo ' name_english:'.$get_achievements_resultData->name_english;
                //echo '$a_reward_genepis: '.$a_reward_genepis;
                
                if ($action == 'unlock_sector' || $action == 'unlock_item') {
                   // echo ' loop unlock_sector / unlock_item ';
                    $claimed_achievements = 0;
                    //$achievement_list_array = array();
                    $achievement_list = $a_requires_decoded->achievement_list ?? '';
                    $sector_to_unlock = $a_requires_decoded->sector ?? null;
                    $achievement_list_array = explode(",", $achievement_list);
                    $nb_achievement_to_complete_array = sizeof($achievement_list_array);
                    //echo ' $nb_achievement_to_complete_array: '.$nb_achievement_to_complete_array;
                    //echo ' $achievementID: '.$achievementID;
                    //var_dump($achievement_list_array);
                    //echo 'sector_to_unlock = '.$sector_to_unlock.', size: '.$nb_achievement_to_complete_array;
                    if (in_array($achievementID, $achievement_list_array)) {
                        //echo '(loop)';
                        $achievements_player_data = $ci->achievements_model->get_claimed_achievements_player($currentUserID, $achievement_list_array);
                        foreach ($achievements_player_data->result() as $res)
                            //echo ' id: '.$res->id_achievement;
                        $claimed_achievements = $achievements_player_data->num_rows();
                        //echo 'ROWS: '.$claimed_achievements.'<br>';
                    }
                    else {
                        //echo 'ACH '.$achievementID.' NOT exists in MASTER '.$a_id.'<br>';
                    } 
                    if ($claimed_achievements == $nb_achievement_to_complete_array) {   // If all achievements are claimed to unlock current one
                        $progress = 100;    // Achievement completed
                        //echo ' PROGRESS SHOULD BE 100%';
                    }
                    else {
                        $progress = $claimed_achievements/$nb_achievement_to_complete_array*100;
                        //echo ' PROGRESS SHOULD BE '.$progress;
                    }
                    
                   // echo ' $claimed_achievements: '.$claimed_achievements;
                    //echo ' claimed '.$claimed_achievements.' of '.$nb_achievement_to_complete_array.' / progress: '.$progress.'<br>';
                }
                //echo 'action: '.$action;
                //echo ' Before - $amount_spent: '.$amount_spent;
                if (isset($amount_spent)) {      // If $amount_spent is defined, we are in the accumulation case (build_amount, buy_amount, upgrade_amount)
                    //echo ' loop0 - $amount_spent: '.$amount_spent;
                    $quantity_required = $a_requires_decoded->quantity ?? 1;
                    $progress = $amount_spent/$quantity_required*100;
                    //echo ' -------------$quantity_required:'.$quantity_required;
                    //echo ' $amount_spent:'.$amount_spent;
                    //echo ' $progress:'.$progress;
                    if ($action == 'single_amount'){
                        //echo 'loop1';
                        if ($amount_spent >= $quantity_required) {
                            $progress = 100;    // Achievement completed
                            
                        }
                        else {
                            $progress = '0';    // Achievement not completed (didn't have enough visitors, or cash... So we keep at 0%
                        }
                    }
                }
                else if (!isset($progress) || $progress == '') {
                    //echo ' loop2';
                    $progress = '0';
                }
                // If progress close to 100 or above, we set it to 100
                if ($progress >= 99.7) {
                    $progress = 100;
                }
                //echo ' loop3';
                //echo ' $progress END:'.$progress.'<br><br>';
                $progress = number_format($progress, 5, '.', '');
                // Check if the event wasn't already unlocked by this player
                $ci->db->where('id_achievement', $a_id);
                $ci->db->where('id_player', $currentUserID);
                $ci->db->where('progress', '100');
                $ci->db->from('user_achievements');
                $count_already_unlocked = $ci->db->count_all_results();

                if ($count_already_unlocked == 0) {     // The achievement wasn't unlocked by this player, we can proceed to unlock
                    //echo ' $count_already_unlocked=0  ';
                   // echo 'loop4';
                    $current_date = gmdate('Y-m-d H:i:s');
                    $achievement_data_player = $ci->achievements_model->get_achievements_status_player($a_id, $currentUserID);
                    if ($achievement_data_player->num_rows() == 0) {    // Achievement needs to be added
                        //echo 'to be added';
                        // Add achievement for player in "user_achievements" table
                        
                        if ($progress < 0) {
                            $progress = 0;
                        }
                        
                        $data_unlocked = array (
                            'id_player' => $currentUserID,
                            'progress' => $progress,
                            'id_achievement' => $a_id,
                            'unlocked_datetime' => $current_date
                        );
                        $data_not_unlocked = array (
                            'id_player' => $currentUserID,
                            'progress' => $progress,
                            'id_achievement' => $a_id
                        );
                        
                        if ($progress == 100)
                            $data_to_insert = $data_unlocked;
                        else 
                            $data_to_insert = $data_not_unlocked;
                        $insert_achievement = $ci->db->insert('user_achievements', $data_to_insert);       // Adding achievement for the player  
                    }
                    else {      // Achievement already started and needs to be updated
                        //echo 'to be updated, progress before: '.$progress;
                        $achievement_data_player_data = $achievement_data_player->row();
                        $current_progress = $achievement_data_player_data->progress;
                        $claimed = $achievement_data_player_data->claimed;
                        if ($action != 'unlock_sector' && $action != 'open' && $action != 'close') {       // If unlock_sector, open or close item we want to replace the progress, not add it to current one. So we should NOT enter this loop
                            $progress = $current_progress + $progress;   // The progress to add is the SUM of the current value and the one earned by this action
                            //echo 'loop 2';
                        }
                        if ($progress >= 99.7 || $progress == 100) {     // If the progress goes over 100, we even it
                            $progress = 100;
                            $ci->db->set('unlocked_datetime', $current_date);
                        }
                        else if ($progress < 0) {
                            $progress = 0;
                        }
                        //echo 'to be updated, progress after: '.$progress;

                        $ci->db->set('progress', $progress);
                        $ci->db->where('id_player' , $currentUserID);                              
                        $ci->db->where('id_achievement' , $a_id);                              
                        $ci->db->update('user_achievements');
                    }
                    // We increase the counter for this achievement (number of players who unlocked it)
                    if ($progress >= 99.7) {
                        $ci->db->set('unlocked_count', 'unlocked_count+1', FALSE)->where('id_achievement', $a_id)->update('achievements');
                    }
                    //echo 'just before loop progress: '.$progress;
                    // We also unlock the sector if achievement is of right type (unlock_sector)
                    if ($action == 'unlock_sector' && $progress == 100 && $claimed == 1) {
                        //echo 'in LOOP to add sector';
                        $data_unlock_sector = array (
                            'id_resort' => $currentResortID,
                            'sector' => $sector_to_unlock,
                            'access_time' => $current_date
                        );
                        $insert_achievement = $ci->db->insert('game_access_sector', $data_unlock_sector);       // Adding sector access for the player
                        //echo '$insert_achievement: '.$insert_achievement;
                    }
                    //else 
                        //echo 'didnt go in loop, with progress: '.$progress;
                }
                else {  // The achievement was already unlocked by this player (can happen if player destroy some building or slopes...
                    //do nothing
                }
            /*    $ci->db->trans_complete();
                if ($ci->db->trans_status() === FALSE){
                    return false;
                }
            */
            }
        }
        else {
            return FALSE;
        }
        //echo '  END CALL';
    }
}

if(!function_exists('count_nb_slopes')){
    function count_nb_slopes(){
        $ci=& get_instance();
        $ci->db->from('game_created_slopes');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
}

if(!function_exists('count_nb_lifts')){
    function count_nb_lifts(){
        $ci=& get_instance();
        $ci->db->from('game_created_lifts');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
}

if(!function_exists('count_nb_open_slopes')){
    function count_nb_open_slopes(){
        $ci=& get_instance();
        $ci->db->from('game_created_slopes');
        $ci->db->where('id_status' , '1');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
}

if(!function_exists('count_nb_open_lifts')){
    function count_nb_open_lifts(){
        $ci=& get_instance();
        $ci->db->from('game_created_lifts');
        $ci->db->where('id_status' , '1');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
}

if(!function_exists('count_nb_open_lifts_sector')){
    function count_nb_open_lifts_sector($sector_id ,$id_resort){ 
        $ci=& get_instance();
        $ci->db->distinct('game_created_lifts.id_created_lifts, game_locations_tbl.id_location, game_locations_tbl.id_group_location');
        $ci->db->from('game_created_lifts');
        $ci->db->join('game_locations as game_locations_tbl', 'game_locations_tbl.id_group = game_created_lifts.id_group_location', 'inner');
        $ci->db->where('game_created_lifts.id_resort', $id_resort);
        $ci->db->where('game_created_lifts.id_status', '1');      
        $ci->db->group_by('game_created_lifts.id_created_lifts');
        $query_open_lifts = $ci->db->get();
        $count_results = 0;
        foreach ($query_open_lifts->result() as $data_open_lifts) {
            $sector_of_current_id_location = $ci->resort_model->get_sector_location($data_open_lifts->id_group_location);
            if ($sector_of_current_id_location == $sector_id || $sector_id == '*')
                $count_results ++;
        }
        return $count_results; 
    }
}


if(!function_exists('count_nb_open_slopes_sector')){
    function count_nb_open_slopes_sector($sector_id = null ,$id_resort = null){    
        //echo 'in count_nb_open_slopes_sector';  
        if ($id_resort === null) {
            return 0;
        }
        $ci=& get_instance();
        $ci->db->distinct('game_created_slopes.id_created_slopes');
        $ci->db->from('game_created_slopes');
        $ci->db->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner');
        $ci->db->where('game_created_slopes.id_resort', $id_resort);
        $ci->db->where('game_created_slopes.id_status', '1');
        if ($sector_id != '*')
            $ci->db->where('game_slopes_tbl.id_sector', $sector_id);
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount; 
    }
}

if(!function_exists('count_nb_open_slope_of_type')){
    function count_nb_open_slope_of_type($id_resort ,$slope_type){ 
        $ci=& get_instance();
        $ci->db->distinct('game_created_slopes.id_created_slopes');
        $ci->db->from('game_created_slopes');
        $ci->db->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner');
        $ci->db->join('game_slope_types as game_slope_types_tbl', 'game_slope_types_tbl.id_slope_types = game_slopes_tbl.slope_type', 'inner');
        $ci->db->where('game_slope_types_tbl.short_name', $slope_type);
        $ci->db->where('game_created_slopes.id_status', '1');      
        $ci->db->where('game_created_slopes.id_resort', $id_resort);      
        $query_open_slopes = $ci->db->get();
        $count_results = 0;
        foreach ($query_open_slopes->result() as $data_open_slopes) {
            $count_results ++;
        }
        return $count_results; 
    }
}

if(!function_exists('count_nb_tournaments')){
    function count_nb_tournaments(){
        $ci=& get_instance();
        $ci->db->from('game_started_tournaments');
        $ci->db->select('id_started_tournament');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        $total_row = number_format($rowcount, 0, '', '');
        return $total_row;
    }
}

if(!function_exists('count_total_accumulated_visitors')){
    function count_total_accumulated_visitors(){
        $ci=& get_instance();
        $ci->db->from('game_resort_affluence');
        $ci->db->select('SUM(affluence) as total_affluence');
        $query = $ci->db->get();
        $total = $query->row();
        $total_row = number_format($total->total_affluence, 0, '', '');
        return $total_row;
    }
}

if(!function_exists('count_daily_visitors')){
    function count_daily_visitors($current_date){
        $ci=& get_instance();
        $ci->db->from('game_resort_affluence');
        $ci->db->select('SUM(affluence) as total_affluence');
        $ci->db->where('date', $current_date);
        $query = $ci->db->get();
        $total = $query->row();
        $total_row = number_format($total->total_affluence, 0, '', '');
        return $total_row;
    }
}

if(!function_exists('count_completed_achievements')){
    function count_completed_achievements(){
        $ci=& get_instance();
        $ci->db->from('user_achievements');
        $ci->db->where('progress' , '100');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
}
if(!function_exists('count_claimed_achievements')){
    function count_claimed_achievements(){
        $ci=& get_instance();
        $ci->db->from('user_achievements');
        $ci->db->where('progress' , '100');
        $ci->db->where('claimed' , '1');
        $query = $ci->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
}

if(!function_exists('get_cost')){
    function get_cost($id_group, $type_item, $new_level, $id_group_location){
        $ci=& get_instance();
        $genericLift = $ci->item_model->get_generic_item_info_for_level($id_group, $type_item, $new_level);  // we get the generic lift information
        if ($genericLift->num_rows() > 0) {             // if the genereic lift exists
            $genericLiftData = $genericLift->row();     // we put the result in a array
            $base_cost_next_level = $genericLiftData->base_cost;
            $meter_cost_next_level = $genericLiftData->meter_cost;
            $length = get_lift_length ($id_group_location, $type_item);
            $cost_lift = $base_cost_next_level + $meter_cost_next_level*$length;
            return $cost_lift;
        }
        else
            return false;
    }
}


if(!function_exists('get_lift_length')){
    function get_lift_length($id_group, $type_item){
        $ci=& get_instance();
        $location_info = $ci->resort_model->get_lift_length_DB($id_group); 
        $location_info_row = $location_info->row();
        $length = $location_info_row->length;
        return $length;
    }
}

if(!function_exists('get_deserving_lifts')){
    function get_deserving_lifts($currentResortID, $id_slope, $mode){
        $ci=& get_instance();
        $count = 0;
        // Start: Get deserving lifts
        $location_info_for_slope = $ci->resort_model->get_location_info_for_slope($currentResortID, $id_slope);
        $location_info_for_slope_row = $location_info_for_slope->row(); // data regarding slope location

        $start_location = $location_info_for_slope_row->start_location; // top of the slope
        $area = $location_info_for_slope_row->area;         // area where the slope starts
        // Iniatializing variables
        $lift_names = array();
        $result_deserving_lifts = array();

        $id_group_of_location = $ci->resort_model->get_id_group_of_location($area);   // Retrieve the id_groups of the specific location. Can be multiple groups (if several lifts end up at the same spot/area)
        foreach ($id_group_of_location->result() as $id_group_of_location_array) {
            $id_group = $id_group_of_location_array->id_group;
            $lift_info_id_group = $ci->resort_model->get_lift_info_id_group($currentResortID, $id_group); // Get lift name for the id_group_location (only one)
            if ($lift_info_id_group->num_rows() >0) {
                foreach ($lift_info_id_group->result() as $lift_info_id_group_array) {
                    if ($lift_info_id_group_array->id_status == 1) {
                        $lift_names [] = $lift_info_id_group_array->custom_name;   // Make an array of the lift names deserving the slope
                        $count ++;     
                    }
                }
            }
        }
        
        if ($mode == 'lift_names')
            return $lift_names;
        else if ($mode == 'number')
            return $count;
        else
            return false;
    }
}

if(!function_exists('get_deserved_slopes')){
    function get_deserved_slopes($currentResortID, $id_created_lift, $mode){
        $ci=& get_instance();
        $count = 0;
        $deserved_slope = [
            'total_length' => 0,
            'total_condition' => 0,
            'count' => 0
        ];
        // Start: Get deserved slopes
        $id_group_location = $ci->resort_model->get_location_info_for_lift($id_created_lift);
        $area_for_slope_start = $ci->resort_model->get_area_for_id_group($id_group_location);
        $id_location_for_slope_start = $ci->resort_model->get_id_group_location_for_slope_start($area_for_slope_start);
        foreach ($id_location_for_slope_start->result() as $id_location) {
            $built_slopes_location = $ci->resort_model->get_built_slopes_player_start_location($currentResortID, $id_location->id_location);
            if ($built_slopes_location->num_rows() > 0) {
                foreach ($built_slopes_location->result() as $built_slopes_location_array) {
                    $deserved_slope = [
                        'total_length' => $deserved_slope['total_length'] + $built_slopes_location_array->length,
                        'total_condition' => $deserved_slope['total_condition'] + $built_slopes_location_array->slope_condition,
                        'count' => $deserved_slope['count'] +1
                    ];
                }
            }
        }
        return $deserved_slope;
    }
}

/* Not used?
if(!function_exists('get_deserving_lifts')){
 
    function get_deserving_lifts($currentResortID, $id_slope, $mode){
        $ci=& get_instance();
        $count = 0;
        // Start: Get deserving lifts
        $location_info_for_slope = $ci->resort_model->get_location_info_for_slope($currentResortID, $id_slope);
        $location_info_for_slope_row = $location_info_for_slope->row(); // data regarding slope location

        $start_location = $location_info_for_slope_row->start_location; // top of the slope
        $area = $location_info_for_slope_row->area;         // area where the slope starts
        // Iniatializing variables
        $lift_names = array();
        $result_deserving_lifts = array();

        $id_group_of_location = $ci->resort_model->get_id_group_of_location($area);   // Retrieve the id_groups of the specific location. Can be multiple groups (if several lifts end up at the same spot/area)
        foreach ($id_group_of_location->result() as $id_group_of_location_array) {
            $id_group = $id_group_of_location_array->id_group;
            $lift_info_id_group = $ci->resort_model->get_lift_info_id_group($currentResortID, $id_group); // Get lift name for the id_group_location (only one)
            if ($lift_info_id_group->num_rows() >0) {
                foreach ($lift_info_id_group->result() as $lift_info_id_group_array) {
                    if ($lift_info_id_group_array->id_status == 1) {
                        $lift_names [] = $lift_info_id_group_array->custom_name;   // Make an array of the lift names deserving the slope
                        $count ++;     
                    }
                }
            }
        }
        if ($mode == 'lift_names')
            return $lift_names;
        else if ($mode == 'number')
            return $count;
        else
            return false;
    }
}
*/

if(!function_exists('email_admin')){
    function email_admin($action, $title_email, $data){
        $ci=& get_instance();
        // Starts to build email
        $ci->email->set_mailtype('html');
        $ci->email->from(CONST_NOREPLY_EMAIL, 'Ski-Manager');
        $ci->email->to(CONST_TRACKING_EMAIL);
        $ci->email->subject($ci->lang->line('email')[$title_email]);
        // different message parts
        $message = $ci->lang->line('email')['activation_start_tags'];
        $message .= $ci->lang->line('email')[$action].':<br>';
        $message .= $ci->lang->line('contact_form')['name'].': ';
        $message .= $data['username'].'<br>';
        $message .= $ci->lang->line('home')['IDplayer'].': ' ;
        $message .= $data['id_player'].'<br>';
        $message .= $ci->lang->line('home')['email'].': ';
        $message .= $data['email'].'<br>';
        $message .= $ci->lang->line('home')['id_resort'].': ';
        $message .= $data['id_resort'].'<br>';
        $message .= $ci->lang->line('home')['ip_address'].': ';
        $message .= $data['ip_address'].'<br>';
        $message .= $ci->lang->line('email')['activation_end_tags'];
        $ci->email->message($message);      // Add the built message content to the body of the email
        return $ci->email->send();                 // Sends the email
    }
}
if(!function_exists('log_user_action')){
    function log_user_action($data){
        $ci=& get_instance();
        $id_player = $data['id_player'];
        $type = $data['type'];
        $data = $data['data'];
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $log_filename = gmdate('Y-m-d', time());
        $data_formatted = $timestamp." INFO [id_player_".$id_player."] ".$type." - ".$data."\n";
        write_file(FCPATH . '/application/controllers/logs/user_actions_'.$log_filename.'.log', $data_formatted, "a+");
    }
}


if(!function_exists('get_resort_status_block')){
    function get_resort_status_block(){
        $ci=& get_instance();
        
        $ci->load->model('building_model');
        $ci->load->model('users_model');
        $ci->lang->load('building', $ci->session->userdata('site_lang') ?: 'english');
        $user_id = $ci->users_model->get_user_id($ci->session->userdata('login_username'));
        $currentResortID = $ci->users_model->get_resort_id($user_id);
        $tourist_info_data = $ci->building_model->get_building_data_for_player($currentResortID, 'tourist_info', '1');      // "tourist_info" is the type of the tourist info building. and "1" is the level of the building
        if ($tourist_info_data->num_rows() > 0) {
            $tourist_info_dataArray = $tourist_info_data->row();
            $id_status = $tourist_info_dataArray->id_status;

            $data['touristInfoStatusLabel'] = $ci->lang->line('home')['status'];
            $data['id_building'] = $tourist_info_dataArray->id_building;
            $friendly_status = display_friendly_status($id_status);
            $data['friendly_status'] = $friendly_status;
            $data['touristInfoBuildingStatus'] = $ci->lang->line('home')['building_status_to_show_'.$friendly_status.''];

            // If still under construction
            if ($id_status == '4') { // if still under construction
                $data['needToHideBuildButton'] = true;
                $data['opposite_status'] = ''; 

                $data['status_text_button'] = 'open'; 
                //$end_construction_date_format = strtotime(get_time_left_for_building($currentResortID, 'tourist_info', '1'));   // return the date/time from the function. strtotime converts string to timestamp
                $timestamp = strtotime(get_time_left_for_building($currentResortID, 'tourist_info', '1')." UTC");   // return the number of seconds until the end
                $currenttime = time();                                          // current timestamp
                $time_left_value = $timestamp - $currenttime;                   // Time left in deconds
                if ($time_left_value <= '0'){                                 // If there is no time left (building finished)
                    $data['pre_touristInfoBuildingStatus'] = '<a href="'.base_url().'building_access_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$ci->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
                    $data['post_touristInfoBuildingStatus'] = '</div></a>';   // For toolpit (post)
                    $data['touristInfoBuildingStatus'] = $ci->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
                    $data['touristInfoBuildingStatus_real'] = $data['touristInfoBuildingStatus']; 
                    $data['tourist_info_status_to_show'] = $data['touristInfoBuildingStatus']; 
                }
                else  {  // If some time is left...
                    $data['touristInfoBuildingStatus'] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                    $data['touristInfoBuildingStatus_real'] = $data['touristInfoBuildingStatus']; 
                    $data['tourist_info_status_to_show'] = $data['touristInfoBuildingStatus'];
                }
                $image_name = 'maintenance.png';
                $tourist_info_opposite_action_lang = 'construction_click_for_info';
                $data['status_sidebar_construction'] = '<div class="inline tooltip tooltip-bottom" data-tip="'.$ci->lang->line('home')[$tourist_info_opposite_action_lang].'"><a class="inline" href="'.base_url('building_access_controller/').'"><img src="'.base_url('img/icons/'.$image_name).'"></a></div>';
            }
            // If completely built
            else if ($id_status == '1') { // If Open
                $tourist_info_opposite_action = 'close';
                $image_name = 'open.png';
                $tourist_info_opposite_action_lang = 'click_to_close';
                $data['opposite_status'] = $tourist_info_opposite_action; 
                $data['status_text_button'] = 'close';
                $close_confirm_msg = addslashes($ci->lang->line('home')['close_resort_confirm']);
                $data['tourist_info_status_to_show'] = '<div class="inline tooltip tooltip-bottom" data-tip="'.$ci->lang->line('home')[$tourist_info_opposite_action_lang].'"><a class="inline" href="'.base_url('building_access_controller/'.$tourist_info_opposite_action.'_building/'.$currentResortID.'/tourist_info').'" onclick="return confirm(\''.$close_confirm_msg.'\')"><img src="'.base_url('img/icons/'.$image_name).'"></a></div>';
            }
            else if ($id_status == '6') { // If closed for safety reasons
                $tourist_info_opposite_action = 'open';
                $image_name = 'maintenance.png';
                $tourist_info_opposite_action_lang = 'click_to_open_safety';
                $data['opposite_status'] = $tourist_info_opposite_action; 
                $data['status_text_button'] = 'open';
                $data['tourist_info_status_to_show'] = '<div class="inline tooltip tooltip-bottom" data-tip="'.$ci->lang->line('home')[$tourist_info_opposite_action_lang].'"><a class="inline" href="'.base_url('building_access_controller/'.$tourist_info_opposite_action.'_building/'.$currentResortID.'/tourist_info').'"><img src="'.base_url('img/icons/'.$image_name).'"></a></div>';
            }
            else if ($id_status == '2') {   // if closed
                $tourist_info_opposite_action = 'open';
                $image_name = 'closed.png';
                $tourist_info_opposite_action_lang = 'click_to_open';
                $data['opposite_status'] = $tourist_info_opposite_action; 
                $data['status_text_button'] = 'open'; 
                $data['tourist_info_status_to_show'] = '<div class="inline tooltip tooltip-bottom" data-tip="'.$ci->lang->line('home')[$tourist_info_opposite_action_lang].'"><a class="inline" href="'.base_url('building_access_controller/'.$tourist_info_opposite_action.'_building/'.$currentResortID.'/tourist_info').'"><img src="'.base_url('img/icons/'.$image_name).'"></a></div>';
            }
        }
        else {  // Tourist info center not built
            
            $data['touristInfoBuildingStatus'] = '';
            
            
            //$tourist_info_opposite_action = 'open';
            $image_name = 'closed.png';
            //$tourist_info_opposite_action_lang = 'click_to_open';
            //$data['opposite_status'] = $tourist_info_opposite_action; 
            //$data['status_text_button'] = 'open';
            $data['tourist_info_status_to_show'] = '<div class="inline tooltip tooltip-bottom" data-tip="'.$ci->lang->line('building')['no_tourist_info'].'"><a class="inline" href="'.base_url('building_access_controller/').'"><img src="'.base_url('img/icons/'.$image_name).'"></a></div>';
        }
        return $data;
    }
}  

if (!function_exists('calc_peak_season_bonus')) {
    /**
     * calc_peak_season_bonus   Returns a visitor-demand multiplier based on the day of the ski season.
     *                          The 135-day season follows a smooth, realistic ski-resort demand curve
     *                          with linear interpolation between key anchor points:
     *                          opening ramp-up → Christmas peak → post-Christmas lull →
     *                          February school-holiday peak → late-season decline → closing fade.
     *
     *                          A weekly weekday/weekend rhythm is layered on top of the base curve
     *                          so that Saturdays & Sundays attract ~30 % more visitors than midweek
     *                          days (Friday +10 %, Monday −10 %), mirroring real-resort behaviour.
     *
     * Approximate base multiplier anchors (before weekly rhythm):
     *   Day   1  : 0.55  (very start of season, thin snow)
     *   Day  15  : 0.72  (opening period complete)
     *   Day  40  : 0.92  (shoulder period peak)
     *   Day  45  : 1.25  (Christmas / New Year apex)
     *   Day  50  : 0.92  (Christmas over)
     *   Day  60  : 0.87  (post-Christmas lull)
     *   Day  68  : 1.18  (February school-holiday apex)
     *   Day  90  : 0.88  (February school holiday fading)
     *   Day 115  : 0.55  (late-season melting)
     *   Day 135  : 0.38  (season closing, very few visitors)
     *
     * @param int $day_of_season    Current day within the season (1–135; 0 or out-of-range → neutral 1.0)
     * @return float                Demand multiplier (~0.38–1.625 including weekly rhythm)
     */
    function calc_peak_season_bonus($day_of_season) {
        $day = (int)$day_of_season;
        if ($day <= 0 || $day > 135) return 1.0;

        // ── Base seasonal curve (smooth linear interpolation) ─────────────────
        // Each segment is a linear (or parabolic) interpolation where the formula
        // uses the *boundary* of the PREVIOUS segment as its reference point, not
        // the first day of the current block.  For example, the February build-up
        // block handles days 61–75 and uses `($day - 60) / 15`: at day 61 that is
        // 1/15, at day 75 it is 15/15 = 1.0, smoothly connecting the preceding
        // 0.87 endpoint to the 1.18 apex.  All segment denominators follow the
        // same convention: denominator = (segment_end − segment_start_reference).
        if ($day <= 15) {
            // Season opening: gradual ramp-up from 0.55 (day 1) to 0.72 (day 15)
            $base = 0.55 + ($day / 15) * 0.17;
        } elseif ($day <= 40) {
            // Shoulder period: steady build from 0.72 (day 15) to 0.92 (day 40)
            $base = 0.72 + (($day - 15) / 25) * 0.20;
        } elseif ($day <= 50) {
            // Christmas / New Year peak: parabolic arc peaking at day 45 (~1.25)
            $t    = ($day - 40) / 10;                        // 0.0 → 1.0
            $base = 0.92 + sin($t * M_PI) * 0.33;           // peak ≈ 1.25 at t=0.5
        } elseif ($day <= 60) {
            // Post-Christmas lull: 0.92 (day 50) down to 0.87 (day 60)
            $base = 0.92 - (($day - 50) / 10) * 0.05;
        } elseif ($day <= 75) {
            // February school-holiday build-up: 0.87 (day 60) → 1.18 (day 75)
            $base = 0.87 + (($day - 60) / 15) * 0.31;
        } elseif ($day <= 90) {
            // February peak + early-spring decline: 1.18 (day 75) → 0.88 (day 90)
            $base = 1.18 - (($day - 75) / 15) * 0.30;
        } elseif ($day <= 115) {
            // Late-season decline: 0.88 (day 90) → 0.55 (day 115)
            $base = 0.88 - (($day - 90) / 25) * 0.33;
        } else {
            // Season closing: 0.55 (day 115) → 0.38 (day 135)
            $base = 0.55 - (($day - 115) / 20) * 0.17;
        }

        // ── Weekly weekday / weekend rhythm ───────────────────────────────────
        // The season is assumed to start on a Monday (day 1 = Monday), so that the
        // weekly rhythm is consistent across the whole 135-day calendar.
        // day_of_week: 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat, 7=Sun
        $day_of_week = (($day - 1) % 7) + 1;
        if ($day_of_week >= 6) {
            $weekly_factor = 1.30;   // Saturday / Sunday: +30 %
        } elseif ($day_of_week == 5) {
            $weekly_factor = 1.10;   // Friday: +10 %
        } elseif ($day_of_week == 1) {
            $weekly_factor = 0.90;   // Monday: −10 % (post-weekend quiet day)
        } else {
            $weekly_factor = 1.0;    // Tuesday – Thursday: neutral
        }

        return max(0.38, round($base * $weekly_factor, 4));
    }
}

if (!function_exists('get_altitude_build_cost_multiplier')) {
    /**
     * get_altitude_build_cost_multiplier   Returns the build cost multiplier for a given resort altitude.
     *
     * Altitude effects:
     *   low    – no additional cost  (× 1.00)
     *   medium – moderate extra cost (× 1.15)
     *   high   – high extra cost     (× 1.30)
     *
     * @param  string $altitude  'low' | 'medium' | 'high'
     * @return float
     */
    function get_altitude_build_cost_multiplier($altitude) {
        $map = ['low' => 1.0, 'medium' => 1.15, 'high' => 1.30];
        return $map[$altitude] ?? 1.0;
    }
}

if (!function_exists('get_snow_quality')) {
    /**
     * get_snow_quality     Determines the snow quality label based on snow level and temperature.
     *
     * Quality types (in priority order):
     *   - Icy:    snow < 10 cm; or 0°C < temp AND snow < 20 cm
     *   - Powder: temp <= -3°C AND snow >= 60 cm
     *   - Packed: temp <= 0°C AND snow >= 20 cm
     *   - Slushy: temp > 3°C AND snow >= 10 cm
     *   - Wet:    0°C < temp <= 3°C AND snow >= 20 cm
     *   - Poor:   snow 10–19 cm with no temperature data (or temp == 0°C)
     *
     * @param int   $snow_level   Current snow level in cm
     * @param float $temperature  Current temperature in °C (null if unknown)
     * @return array  ['key' => string, 'badge_class' => string]
     */
    function get_snow_quality($snow_level, $temperature = null) {
        $snow_level = (int)$snow_level;

        // Not enough snow regardless of temperature
        if ($snow_level < 10) {
            return ['key' => 'icy', 'badge_class' => 'danger'];
        }

        if ($temperature !== null) {
            $temp = (float)$temperature;

            // Above freezing with low snow → icy conditions
            if ($temp > 0 && $snow_level < 20) {
                return ['key' => 'icy', 'badge_class' => 'danger'];
            }

            // Cold enough + plenty of snow → powder
            if ($temp <= -3 && $snow_level >= 60) {
                return ['key' => 'powder', 'badge_class' => 'info'];
            }

            // At or below freezing with decent snow → packed
            if ($temp <= 0 && $snow_level >= 20) {
                return ['key' => 'packed', 'badge_class' => 'success'];
            }

            // Warm with sufficient snow → slushy
            if ($temp > 3 && $snow_level >= 10) {
                return ['key' => 'slushy', 'badge_class' => 'warning'];
            }

            // Slightly above freezing (0°C < temp <= 3°C) with sufficient snow → wet
            if ($temp > 0 && $temp <= 3 && $snow_level >= 20) {
                return ['key' => 'wet', 'badge_class' => 'warning'];
            }
        }

        // Snow exists but no temperature info, or borderline
        if ($snow_level >= 20) {
            return ['key' => 'packed', 'badge_class' => 'success'];
        }

        return ['key' => 'poor', 'badge_class' => 'secondary'];
    }
}

if (!function_exists('get_season_phase')) {
    /**
     * get_season_phase  Returns the current phase name of the ski season
     *                   based on the day of the season.
     *
     * Phases:
     *   early   : days   1–15  (season opening, thin snow)
     *   buildup : days  16–50  (Christmas / New Year build-up)
     *   peak    : days  51–90  (mid-season peak)
     *   late    : days  91–115 (late season, snow starts melting)
     *   closing : days 116–135 (season closing, heavy melt)
     *
     * @param int $day_of_season  Current day within the season (1–135)
     * @return string             Phase key: 'early'|'buildup'|'peak'|'late'|'closing'
     */
    function get_season_phase($day_of_season) {
        $day = (int)$day_of_season;
        if ($day <= 15)  return 'early';
        if ($day <= 50)  return 'buildup';
        if ($day <= 90)  return 'peak';
        if ($day <= 115) return 'late';
        return 'closing';
    }
}

if (!function_exists('calc_seasonal_melt_rate')) {
    /**
     * calc_seasonal_melt_rate   Returns the natural snow melt in cm/day for the
     *                           current day of the season.
     *
     * Natural melt accounts for rising temperatures as the season progresses:
     *   early/buildup/peak (days   1–90)  : 0 cm/day – cold enough to retain snow
     *   late               (days  91–105) : 1 cm/day – gradual spring warming begins
     *   late+              (days 106–120) : 2 cm/day – moderate spring melt
     *   closing            (days 121–135) : 3 cm/day – heavy end-of-season melt
     *
     * @param int $day_of_season  Current day within the season (1–135)
     * @return int                Natural melt in cm (0 = no melt)
     */
    function calc_seasonal_melt_rate($day_of_season) {
        $day = (int)$day_of_season;
        if ($day <= 90)  return 0;
        if ($day <= 105) return 1;
        if ($day <= 120) return 2;
        return 3;
    }
}

if (!function_exists('build_html_email')) {
    /**
     * build_html_email  Builds a branded, responsive HTML email body for Ski-Manager
     *                   transactional emails.
     *
     * Generates a complete HTML document with inline CSS, a dark-navy header with
     * the Ski-Manager brand, a content area with an optional CTA button, and a
     * footer.  All user-visible strings are passed in as parameters so callers can
     * pull them from the language files; the structural HTML and colour palette are
     * fixed here.
     *
     * Colour palette – Alpine Summit:
     *   #0ea5e9  sky-blue primary  (button background, links)
     *   #0a1628  dark-navy header  (header background)
     *   #f0f7ff  light-blue tint   (page background)
     *
     * @param string $username    Recipient display name shown in the salutation.
     * @param string $heading     Short heading shown inside the email body.
     * @param string $body_html   Main body content (may contain safe inline HTML).
     * @param string $button_url  URL for the CTA button; omit or pass '' to hide it.
     * @param string $button_text Label on the CTA button.
     * @param string $note_html   Optional small-print text shown below the button.
     * @return string             Complete HTML email document.
     */
    function build_html_email($username, $heading, $body_html, $button_url = '', $button_text = '', $note_html = '') {
        $year     = date('Y');
        $base_url = rtrim(base_url(), '/');
        $safe_username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $safe_heading  = htmlspecialchars($heading,  ENT_QUOTES, 'UTF-8');
        $safe_btn_text = htmlspecialchars($button_text, ENT_QUOTES, 'UTF-8');

        $html  = '<!DOCTYPE html>';
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml">';
        $html .= '<head>';
        $html .= '<meta charset="utf-8" />';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
        $html .= '<title>Ski-Manager</title>';
        $html .= '</head>';
        $html .= '<body style="margin:0;padding:0;background-color:#f0f7ff;font-family:Arial,Helvetica,sans-serif;">';

        // Outer wrapper
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f7ff;padding:30px 0;">';
        $html .= '<tr><td align="center">';

        // Card
        $html .= '<table cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(14,165,233,0.12);">';

        // Header
        $html .= '<tr>';
        $html .= '<td style="background-color:#0a1628;padding:28px 40px;text-align:center;">';
        $html .= '<p style="color:#0ea5e9;font-size:26px;font-weight:700;margin:0;letter-spacing:1px;">&#x26F7; Ski-Manager</p>';
        $html .= '<p style="color:#94a3b8;font-size:12px;margin:6px 0 0;">ski-manager.net</p>';
        $html .= '</td>';
        $html .= '</tr>';

        // Body
        $html .= '<tr>';
        $html .= '<td style="padding:36px 40px;">';
        $html .= '<h2 style="color:#0a1628;font-size:20px;font-weight:700;margin:0 0 20px;">' . $safe_heading . '</h2>';
        $html .= '<p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 16px;">Dear ' . $safe_username . ',</p>';
        $html .= '<p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 28px;">' . $body_html . '</p>';

        // CTA button
        if (!empty($button_url) && !empty($safe_btn_text)) {
            $html .= '<table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">';
            $html .= '<tr><td style="background-color:#0ea5e9;border-radius:6px;text-align:center;">';
            $html .= '<a href="' . $button_url . '" style="display:inline-block;padding:14px 32px;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;letter-spacing:0.3px;">' . $safe_btn_text . '</a>';
            $html .= '</td></tr>';
            $html .= '</table>';
        }

        // Optional note
        if (!empty($note_html)) {
            $html .= '<p style="color:#6b7280;font-size:13px;line-height:1.6;margin:0;">' . $note_html . '</p>';
        }

        $html .= '</td>';
        $html .= '</tr>';

        // Footer
        $html .= '<tr>';
        $html .= '<td style="background-color:#f0f7ff;border-top:1px solid #bae6fd;padding:20px 40px;text-align:center;">';
        $html .= '<p style="color:#64748b;font-size:13px;line-height:1.6;margin:0 0 6px;">Sincerely,<br>/Ski-Manager team.</p>';
        $html .= '<p style="color:#94a3b8;font-size:12px;margin:0;">&copy; ' . $year . ' Ski-Manager &middot; <a href="' . $base_url . '" style="color:#0ea5e9;text-decoration:none;">ski-manager.net</a></p>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '</table>';  // card
        $html .= '</td></tr>';
        $html .= '</table>';  // outer wrapper
        $html .= '</body></html>';

        return $html;
    }
}

if (!function_exists('send_ses_email')) {
    /**
     * send_ses_email  Sends an email via Amazon SES SMTP.
     *
     * Loads the email_ses.php config, initialises CodeIgniter's Email library
     * with the SES SMTP credentials, and sends the message.  The from address
     * must be a domain or address that has been verified in the AWS SES console;
     * the original sender's address is preserved as the Reply-To header.
     *
     * @param string $to_email       Recipient email address
     * @param string $from_email     Verified SES sender address
     * @param string $from_name      Sender display name
     * @param string $subject        Email subject line
     * @param string $html_content   HTML body of the email
     * @param string $reply_to_email Optional reply-to address (e.g. the end-user's address)
     * @param string $reply_to_name  Optional reply-to display name
     * @return bool                  TRUE on success, FALSE on failure
     */
    function send_ses_email($to_email, $from_email, $from_name, $subject, $html_content, $reply_to_email = '', $reply_to_name = '') {
        $ci =& get_instance();
        $ci->config->load('email_ses', TRUE);

        $smtp_host = $ci->config->item('smtp_host', 'email_ses');
        $smtp_user = $ci->config->item('smtp_user', 'email_ses');
        $smtp_pass = $ci->config->item('smtp_pass', 'email_ses');

        if (empty($smtp_host) || empty($smtp_user) || empty($smtp_pass)) {
            log_message('error', 'SES email config is incomplete (email_ses.php). Email not sent to: ' . $to_email);
            return false;
        }

        $ci->load->library('email');
        $ci->email->initialize(array(
            'protocol'    => $ci->config->item('protocol',    'email_ses'),
            'smtp_host'   => $smtp_host,
            'smtp_port'   => $ci->config->item('smtp_port',   'email_ses'),
            'smtp_user'   => $smtp_user,
            'smtp_pass'   => $smtp_pass,
            'smtp_crypto' => $ci->config->item('smtp_crypto', 'email_ses'),
            'mailtype'    => $ci->config->item('mailtype',    'email_ses'),
            'charset'     => $ci->config->item('charset',     'email_ses'),
            'newline'     => $ci->config->item('newline',     'email_ses'),
            'crlf'        => $ci->config->item('crlf',        'email_ses'),
            'wordwrap'    => $ci->config->item('wordwrap',    'email_ses'),
        ));

        $ci->email->from($from_email, $from_name);
        if (!empty($reply_to_email)) {
            $ci->email->reply_to($reply_to_email, $reply_to_name);
        }
        $ci->email->to($to_email);
        $ci->email->subject($subject);
        $ci->email->message($html_content);

        if ($ci->email->send()) {
            return true;
        }
        log_message('error', 'SES email failed to send to: ' . $to_email);
        return false;
    }
}

if (!function_exists('send_brevo_email')) {
    /**
     * send_brevo_email  Sends a transactional email via the Brevo REST API.
     *
     * Uses SMTPApi::sendTransacEmail() so every sent email is visible in the
     * Brevo transactional-email dashboard/logs, regardless of SMTP relay
     * connectivity.
     *
     * @param string $to_email     Recipient email address
     * @param string $from_email   Sender email address (must be a verified Brevo sender)
     * @param string $from_name    Sender display name
     * @param string $subject      Email subject line
     * @param string $html_content HTML body of the email
     * @return bool                TRUE on success, FALSE on failure
     */
    function send_brevo_email($to_email, $from_email, $from_name, $subject, $html_content) {
        require_once(APPPATH . 'libraries/vendor/autoload.php');
        $ci =& get_instance();
        $ci->config->load('brevo');

        try {
            $brevoConfig = SendinBlue\Client\Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', $ci->config->item('brevo_api_key'));
            $apiInstance = new SendinBlue\Client\Api\SMTPApi(new GuzzleHttp\Client(), $brevoConfig);

            $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
            $sendSmtpEmail['sender']      = array('name' => $from_name, 'email' => $from_email);
            $sendSmtpEmail['to']          = array(array('email' => $to_email));
            $sendSmtpEmail['subject']     = $subject;
            $sendSmtpEmail['htmlContent'] = $html_content;

            $apiInstance->sendTransacEmail($sendSmtpEmail);
            return true;
        } catch (Exception $e) {
            log_message('error', 'Brevo transactional email failed to ' . $to_email . ': ' . $e->getMessage());
            return false;
        }
    }
}
  
