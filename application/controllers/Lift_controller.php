<?php
class Lift_controller extends CI_Controller{
    
    // Defines global variables
    private $currentUserID;
    
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('resort',$siteLang);
            //$ci->lang->load('staff',$siteLang);
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('lift',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        // $ci->lang->load('equipment',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)       // Only for logged in users
            redirect('home_controller');                             // If not logged in, redirect to homepage
        $this->load->model('item_model');
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('building_model');
        $this->load->model('achievements_model');
    }
    
    public function index(){        
        redirect('resort_controller'); 
    }
    
    /**
     * display_lift_to_build   Called when the user wants to build the lift for the first time
     * 
     * @param type $id_group        Generic group ID of the lift
     * @param type $currentResortID   Current resort ID
     * @param type $id_sector       ID of the sector where the lift is located (used for displaying info)
     */
    public function display_lift_to_build($id_group, $currentResortID, $id_sector = null){        
        if ($this->item_model->get_generic_item_info_group($id_group, 'lift')){  // If there is a matching generic lift with the passed variables...
            // Check if sector unlocked
            $currentUserID = $this->users_model->get_user_id();
            $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
            if ($resultResort->num_rows() > 0) {        // the user has built a resort
                $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                if (isset($id_sector) && isset($sector_access_array[$id_sector]) && $sector_access_array[$id_sector] == $id_sector){
                    $status_sector = 'sector_unlocked';
                }
                else {
                    $status_sector = 'sector_locked';
                }
                $data = $this->show_info_block_lift($id_group, $currentResortID, $status_sector, NULL);    // Gets all the player lift info with "sector_locked or unlocked" status
            }
            else {  // The user has no resort built
                redirect('resort_controller/');
            }
        } else {    // bad lift passed as parameter
            $data = $this->show_info_block_lift($id_group, $currentResortID, 'lift_not_found', NULL);    // Shows that the lift couldn't be found
        }  
    }
    
    /**
     * build_lift  Bluids the lift and adds it to created lifts for this player
     * 
     * @param type $currentResortID
     */
    public function build_lift(){
        if (isset ($_POST['buildForm'])) {         // if the form was posted
            $currentUserID = $this->users_model->get_user_id();
            $currentResortID = $this->users_model->get_resort_id($currentUserID);
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
            //$this->form_validation->set_rules('lift_choose_name', $this->lang->line('resort')['name_field'], 'trim|required|min_length[3]|max_length[35]|callback_check_if_lift_already_built');
            $this->form_validation->set_rules('form_id_group_location', 'form_id_group_location', 'trim|required');
            $this->form_validation->set_rules('form_id_lift_type', 'form_id_lift_type', 'trim|required');
            $this->form_validation->set_rules('form_id_grip_type', 'form_id_grip_type', 'trim|required');
            $this->form_validation->set_rules('form_capacity', 'form_capacity', 'trim|required');
            $this->form_validation->set_rules('form_lift_length_meters', 'form_lift_length_meters', 'trim|required');
            $id_group_location = $this->input->post('form_id_group_location', TRUE);
            $id_lift_type = $this->input->post('form_id_lift_type', TRUE);
            $id_grip_type = $this->input->post('form_id_grip_type', TRUE);
            $capacity = $this->input->post('form_capacity', TRUE);
            $lift_length = $this->input->post('form_lift_length_meters', TRUE);
            $sector_of_current_id_location = $this->resort_model->get_sector_location($id_group_location);
            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                // We display the same form again, with errors
                $data['infoMessage'] = $this->lang->line('home')['something_went_wrong'];
                $data['main_content'] = 'lift';
                $this->load->view('templates/default',$data);
            }
            else {    // TRUE: all fields are correct
                $genericLift = $this->item_model->get_generic_lift_build_mode($id_lift_type, $id_grip_type, $capacity);  // we get the generic lift information
                $genericLiftData = $genericLift->row();
                $id_group = $genericLiftData->id_group;
                if ($genericLift->num_rows() > 0) {    // if the genereic lift exists 
                    // Check if we are not building another slope
                    $slopes_under_construction = count_ongoing_building_items('lift', '4');  // type = lift and "4" is Under construction status
                    $slopes_under_maintenance = count_ongoing_building_items('lift', '3');  // type = lift and "3" is Under maintenance status
                    if ($slopes_under_construction == 0 && $slopes_under_maintenance == 0) {    // if there is no ongoing lift under construction or maintenance
                        
                        $genericLift_specific_level = $this->item_model->get_generic_item_info_for_level($id_group, 'lift', '1');  // we get the generic lift information
                        $genericLift_specific_level_data = $genericLift_specific_level->row();
                        $time_construction_duration = $genericLift_specific_level_data->building_time/ACCELERATOR_FACTOR;      // store the building time of the lift
                        $base_cost_lift = $genericLift_specific_level_data->base_cost;
                        $meter_cost_lift = $genericLift_specific_level_data->meter_cost;
                        $gain_reputation = $genericLift_specific_level_data->reputation;        // reputation to gain
                        $id_group = $genericLift_specific_level_data->id_group;       
                        $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
                        // Checks if the player has access to the sector
                       if ($resultResort->num_rows() > 0) {
                            $resultResort2 = $resultResort->row(); 
                            // START CHECK SECTOR ACCESS
                            $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                            
                        }
                        
                        if (isset($sector_access_array[$sector_of_current_id_location]) && $sector_access_array[$sector_of_current_id_location] == $sector_of_current_id_location){
                        // END CHECK SECTOR ACCESS
                            $current_time = time(); 
                            $end_construction_timestamp = $current_time + $time_construction_duration;      // when the construction is supposed to end (timestamp format)
                            
                            $altitude_cost_mult = $this->get_altitude_build_cost_multiplier(isset($resultResort2->altitude) ? $resultResort2->altitude : 'medium');
                            $cost_lift = (int) round(($base_cost_lift + ($meter_cost_lift * $lift_length)) * $altitude_cost_mult);
                            $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                            $money_after_payment = $cash_player - $cost_lift;          // we calculate hos much the player will have left after the payment
                            if ($money_after_payment >= 0) {                            // If enough cash
                                if ($removeCashQuery = $this->users_model->pay_item($cost_lift, $cash_player)){      //the paiment for the lift has been taken
                                    $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                                    $column_name = 'name_'.$player_preferred_lang;
                                    $lift_name = $genericLift_specific_level_data->$column_name;
                                    // we create the lift in the DB
                                    if ($buildQuery = $this->item_model->build_lift($currentResortID, $id_group, $lift_name, $end_construction_timestamp, $id_group_location)){   //the lift has been built successfully
                                        $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                                        $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                                        $add_reputation = $this->users_model->add_reputation($gain_reputation);
                                        $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                                        $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                                        $data = $this->show_info_block_lift($id_group, $currentResortID, 'lift_built', $id_group_location);  // we display the info block with 'lift_built' parameter
                                        // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                                        $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_lift, 'cost_purchases');
                                        $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_lift, 'expenses');
                                        $data = array (
                                            'id_resort' => $currentResortID,
                                            'id_building' => $id_group,       // ID of the l in the game_lifts table
                                            'type' => 'lift',       
                                            'level' => '1'
                                        );
                                        $data_achievement = array (
                                            'id_resort' => $currentResortID,
                                            'id_building' => $id_group,       // ID of the l in the game_lifts table
                                            'type' => 'lift',       
                                            'level' => '1',
                                            'id_sector' => $sector_of_current_id_location
                                        );
                                        $call_achievements_check = call_achievements_check($data_achievement, 'build');
                                        $call_achievements_check = call_achievements_check($data = array('id_resort' => $currentResortID, 'quantity' => $cost_lift), 'build_amount');   // Check spending achievements
                                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $this->lang->line('logs')['construction_of'].$lift_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $this->lang->line('logs')['construction_of'].$lift_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                    }
                                    else {                        //creation of lift failed. Should NEVER happen
                                        $data['infoMessage'] = $this->lang->line('lift')['built_failed'];
                                        $data['main_content'] = 'lift';
                                        $this->load->view('templates/default',$data);
                                    }
                                }
                                else {                        //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['something_went_wrong'].'</div>');       
                                    redirect('resort_map_controller');
                                }
                            }
                            else {                        //not enough money
                                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['not_enough_money'].'</div>');       
                                redirect('resort_map_controller');
                               // $data['infoMessage'] = $this->lang->line('home')['not_enough_money'];
                               // $data = $this->show_info_block_lift($id_group, $currentResortID, 'not_enough_money', NULL);  // we display the info block with 'not_enough_money' parameter
                                
                            }
                        }
                        else {  // The sector is locked, user tried to cheat
                            redirect('resort_controller/');
                        }
                    }
                    else {                        // ongoing construction
                        $data = $this->show_info_block_lift($id_group, $currentResortID, 'ongoing_construction_lift', NULL);  // we display the info block with 'ongoing_construction' parameter
                    }
                }
            }
        }
        else {      // Nothing was posted
            redirect('resort_controller/');
        }
    }
    
    
    /**
     * show_info_block_lift  Display all the information for the lift
     * 
     * @param type $id_lift         ID of the lift
     * @param type $currentResortID   Current resort ID
     * @param type $action          Get action from previous function or set to NULL if undefined
     * @return string               Returns the content of the page
     */
    public function show_info_block_lift($id_group, $currentResortID, $action = NULL, $id_group_location = NULL){
        if ($action == 'null')
            $action = NULL;
        $data['currentResortID'] = $currentResortID;
        $currentUserID = $this->users_model->get_user_id();
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
            if ($user_activated) {      // If the account is activated, we show the page
            // START CHECK IF THE USER IS ALLOWED TO BE HERE
            $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
            if (!$user_is_allowed)
                redirect('home_controller');
            // END CHECK IF THE USER IS ALLOWED TO BE HERE
            $genericLift = $this->item_model->get_generic_item_info_group($id_group, 'lift'); // get generic lift information from DB
            if ($genericLift->num_rows() > 0) {                        // Should return a generic lift 
                $genericLiftData = $genericLift->row();
                $name_language = 'name_'.$this->session->userdata('site_lang');         // Prepare to get language info from DB such as "name_english"
                //$id_group = $genericLiftData->id_group;
                // START GETTING THE BUILDING TIME FOR EACH LEVEL
                
                if (isset($id_group_location)) {
                    $is_the_lift_built = $this->item_model->check_if_player_has_built_item_location($id_group_location, $currentResortID, 'lift');     // check if lift is built
                }
                else {
                    $is_the_lift_built = $this->item_model->check_if_player_has_built_item_group($id_group, $currentResortID, 'lift');     // check if lift is built
                }
                // START: the lift is already built, we will display custom values and other information
                if ($is_the_lift_built->num_rows() > 0) {
                    $var_lift_is_built = true;
                    $data['var_lift_is_built'] = true;
                    $is_the_lift_builtData = $is_the_lift_built->row();
                    $current_player_lift_level = $is_the_lift_builtData->level;
                    $lift_name_to_show = $is_the_lift_builtData->custom_name;                   // Slope custom name for this user
                    $data['lift_status'] = $this->lang->line('home')['status_built'];             // lift status ("Built" in this loop)
                    $data['pre_lift_status'] = '';
                    $data['post_lift_status'] = '';
                    $data['repair_cost'] = '';
                    $data['lift_condition'] = min($is_the_lift_builtData->lift_condition,100).' %';      // condition of the lift (e.g 90%)
                    // Lift age and wear calculations
                    $install_date = $is_the_lift_builtData->install_date ?? null;
                    if ($install_date) {
                        $today    = new DateTime('today', new DateTimeZone('UTC'));
                        $installed = new DateTime($install_date . ' 00:00:00', new DateTimeZone('UTC'));
                        $age_days  = max(0, (int) $today->diff($installed)->days);
                        $age_seasons = (int) ($age_days / LIFT_SEASON_DAYS);
                    } else {
                        $age_seasons = 0;
                    }
                    $data['lift_age_years']          = $age_seasons;
                    $data['lift_wear_pct']            = max(0, min(100, 100 - (int) $is_the_lift_builtData->lift_condition));
                    $data['lift_efficiency_penalty']  = min(round($age_seasons * LIFT_AGE_EFFICIENCY_DROP_PER_YEAR * 100, 1), 100);
                    $data['lift_cost_multiplier']     = 1 + min($age_seasons * LIFT_AGE_COST_MULTIPLIER_PER_YEAR, 1.0);
                    $data['lift_max_age_seasons']     = LIFT_MAX_AGE_SEASONS;
                    $data['lift_status'] .= ' ('.$this->lang->line('home')['level'].' '.$current_player_lift_level.'/3)';                   // Current level of the lift (e.g. 1/3)
                    $id_created_lifts = $is_the_lift_builtData->id_created_lifts;    // For hidden field

                    $data['id_created_lifts'] = $id_created_lifts;
                    $data['level'] = $current_player_lift_level;
                    $data['id_group'] = $is_the_lift_builtData->id_group;
                    $id_group_location = $is_the_lift_builtData->id_group_location;
                    $data['id_resort'] = $currentResortID;
                    $lift_status_id = $is_the_lift_builtData->id_status;   // ID status of the lift
                    // Raw data for new view
                    $data['lift_name']          = $lift_name_to_show;
                    $data['current_level']      = $current_player_lift_level;
                    $data['lift_status_id_raw'] = $lift_status_id;
                    $data['id_group_location']  = $id_group_location;
                    $data['lift_edit_url']      = base_url('lift_controller/edit_name_mode/'.$id_group.'/'.$currentResortID.'/'.$id_group_location);
                }
                else {
                    redirect('resort_controller');
                }

                for ($level = 1; $level <= 3; $level++) {       // For each level
                //$id_lift_to_check = $id_lift + $level -1;   // Since the ID in the DB is increased for each level (autoincrement), we need to adjust it's value here. Each level of a lift should have a following ID
                $genericLift_specific_level = $this->item_model->get_generic_item_info_for_level($id_group, 'lift', $level); 
                    if ($genericLift_specific_level->num_rows() > 0) {     
                        $get_item_sector = $this->item_model->get_lift_sector_group_location($currentResortID, $id_group_location);
                        $get_item_sector_result = $get_item_sector->row();
                        $id_sector = $get_item_sector_result ? $get_item_sector_result->id_sector : null;
                        $genericLift_specific_level_data = $genericLift_specific_level->row();
                        $building_time[$level] = $genericLift_specific_level_data->building_time/ACCELERATOR_FACTOR;
                        $base_cost[$level] = $genericLift_specific_level_data->base_cost;
                        $meter_cost[$level] = $genericLift_specific_level_data->meter_cost;
                        $upgrade_cost[$level] = get_cost ($id_group, 'lift', $level, $id_group_location);
                        $capacity[$level] = $genericLift_specific_level_data->capacity;
                        $throughput[$level] = $genericLift_specific_level_data->throughput;
                        $speed[$level] = $genericLift_specific_level_data->speed;
                        $reputation[$level] = $genericLift_specific_level_data->reputation;
                    }
                }    

                switch ($lift_status_id) {
                    case 4:                 // under_construction'
                        // if under construction, we display how long is left to achieve the construction
                        // START time calculation and Countdown
                        $end_construction = $is_the_lift_builtData->end_construction;
                        $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
                        $currenttime = time();                                          // current timestamp
                        $time_left_value = $timestamp - $currenttime;                   // Time left in seconds

                        if ($time_left_value <= 0){                                    // If there is no time left (building finished)
                            // Auto-complete construction: update DB status to open (1) and reload
                            $this->building_model->complete_construction_DB($id_created_lifts, 'game_created_lifts', 'id_created_lifts', 'end_construction');
                            redirect('lift_controller/show_info_block_lift/'.$id_group.'/'.$currentResortID.'/null/'.$id_group_location);
                        }
                        else {   // If some time is left...
                            $data['pre_lift_status'] = $this->lang->line('home')['building_status_to_show_under_construction'].' ('.$this->lang->line('slope')['time_left'].' ';   // For toolpit (pre)
                            $data['lift_status'] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                            $data['post_lift_status'] = ')';   // For toolpit (post)
                        }

                        // if the lift has just been built, we display the confimrmation message. We only do it in this loop because the status should aslway be under_construction straight after building
                        if (isset($action) && $action == 'lift_built'){
                            $data['action'] = 'lift_built';                // for the correct loop below
                        }

                        // START DISPLAYING COUNTDOWN OR WAIT MESSAGE IN BUILDING TIME
                        for ($level = 1; $level <= 3; $level++) {      // For each level
                            if ($current_player_lift_level == $level) { 
                                $building_time[$level] = '<div style="display:inline;" data-countdown="'.$data['lift_status'].'">'.$data['lift_status'].'</div>';
                            }
                            else 
                                $building_time[$level] = display_friendly_time($building_time[$level]);
                        }
                        // END DISPLAYING COUNTDOWN OR WAIT MESSAGE IN BUILDING TIME
                    break;
                    case 3:     // under maintenance
                        // if under maintenance, we display how long is left to achieve the repair
                        // START time calculation and Countdown
                        $end_construction = $is_the_lift_builtData->end_construction;
                        $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
                        $currenttime = time();                                          // current timestamp
                        $time_left_value = $timestamp - $currenttime;                   // Time left in seconds
                        
                        // START RUSH BUTTON REPAIR
                            $rush_button = '';
                            
                            if ( isset($time_left_value) && $time_left_value > 0 ) {
                                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                                if ($genepis_required_to_rush <= $genepis_available) {
                                    $rush_button = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url('lift_controller/rush/'.$id_group_location.'/'.$id_created_lifts).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                                }
                                else {
                                    $rush_button = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                                }
                                    
                            }
                            
                            // END RUSH BUTTON

                        if ($time_left_value <= 0){                                    // If there is no time left (repair finished)
                            // Auto-complete maintenance: update DB status to open (1) and reload
                            $this->building_model->complete_construction_DB($id_created_lifts, 'game_created_lifts', 'id_created_lifts', 'end_construction');
                            redirect('lift_controller/show_info_block_lift/'.$id_group.'/'.$currentResortID.'/null/'.$id_group_location);
                        }
                        else {   // If some time is left...
                            $data['pre_lift_status'] = $this->lang->line('home')['building_status_to_show_under_maintenance'].' ('.$this->lang->line('slope')['time_left'].' ';   // For toolpit (pre)
                            $data['lift_status'] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                            $data['post_lift_status'] = ') '.$rush_button;   // For toolpit (post)
                        }

                        // if the lift has just been built, we display the confimrmation message. We only do it in this loop because the status should aslway be under_construction straight after building
                        if (isset($action) && $action == 'lift_built'){
                            $data['action'] = 'lift_built';                // for the correct loop below
                        }

                        // START DISPLAYING COUNTDOWN OR WAIT MESSAGE IN BUILDING TIME
                        for ($level = 1; $level <= 3; $level++) {       // For each level
                            if ($current_player_lift_level == $level) {
                                $building_time[$level] = '<div style="display:inline;" data-countdown="'.$data['lift_status'].'">'.$data['lift_status'].'</div>';
                            }
                            else 
                                $building_time[$level] = display_friendly_time($building_time[$level]);
                        }
                        // END DISPLAYING COUNTDOWN OR WAIT MESSAGE IN BUILDING TIME
                        break;
                    case 5:     // out of order
                        // if out of order, we display the status
                        $data['pre_lift_status'] = '<div id="status_field" style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['out_of_order_text'].'">';   // For toolpit (pre)     
                        $data['lift_status'] = $this->lang->line('lift')['out_of_order'];   // Displays the Please Wait message in the table cell
                        $assigned_mechanics = $this->item_model->count_assigned_mechanics_db($currentResortID, $id_created_lifts);
                        //echo $this->db->last_query();
                        $nb_assigned_mechanics = $assigned_mechanics->num_rows();
                        if ($nb_assigned_mechanics == '1') {
                            $data_assigned_mechanics = $assigned_mechanics->row();
                            $id_group = $data_assigned_mechanics->id_group;
                            //$id_group_location = $data_assigned_mechanics->id_group_location;
                            $level = $data_assigned_mechanics->level;
                            $id_hired_staff = $data_assigned_mechanics->id_hired_staff;
                            $efficiency = $data_assigned_mechanics->efficiency;
                            $repair_cost = $data_assigned_mechanics->repair_cost;
                            $data['repair_cost'] = number_format($repair_cost, 0, ',', ' ');
                            $data['post_lift_status'] = '</div><span id="status_field_button" class="left_spacing"><a href="?action=repair_lift" class="" data-id_item="'.$id_created_lifts.'" data-id_group="'.$id_group.'" data-currentResortId="'.$currentResortID.'" data-repair_cost="'.$repair_cost.'" ><button class="btn btn-warning repair_lift-dialog">'.$this->lang->line('lift')['repair'].' '.$this->lang->line('home')['for'].' '.$repair_cost.'€</button></a></span>';
                        }
                        else {
                            $data['post_lift_status'] = '</div><div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_mechanics_assigned'].'"><button class="btn btn-warning disabled left_spacing">'.$this->lang->line('lift')['repair'].'</button></div>';
                        }
                        break;
                    case 1:     // open
                        $data['lift_status'] = $this->lang->line('home')['building_status_to_show_open'].' ('.$this->lang->line('home')['level'].' '.$current_player_lift_level.'/3)';
                        break;
                    case 2:     // closed
                        $data['lift_status'] = $this->lang->line('home')['building_status_to_show_closed'].' ('.$this->lang->line('home')['level'].' '.$current_player_lift_level.'/3)';
                        break;
                }
             
                // START BUILDING HEADER WHEN LIFT ALREADY BUILT (form for editing name)
                if ((((!isset($lift_to_build) || $lift_to_build == false) && $is_the_lift_built->num_rows() > 0) || $action == 'lift_to_edit' || $action == 'ongoing_construction_lift' || $action == 'bad_level' || $action == 'not_enough_money') && !isset ($_POST['buildForm']) && $action != 'lift_upgraded' && $action != 'lift_repaired' && $action != 'bad_status') {
                    $data['header'] = '';
                    if ($action == 'lift_to_edit') {
                        $data['header'] .= '<h2>';
                        $data['header'] .= form_open('lift_controller/edit_name/'.$id_created_lifts.'/'.$currentResortID.'/'.$id_group.'/'.$id_group_location);
                        $data['header'] .= $this->lang->line('lift')['name_edit'];
                        $data['header'] .= '</h2>';
                        $data_input = array(
                            'name'        => 'lift_choose_name',
                            'id'          => 'lift_choose_name',
                            'value'       => set_value('lift_choose_name', $lift_name_to_show),
                            'size'        => '35'
                          );
                        $data['header'] .= '<div class="inline">'.form_input($data_input);
                        $data['header'] .= form_submit($this->lang->line('home')['edit_name'], $this->lang->line('home')['edit_name'], "class='btn btn-success'").'</div>';
                        $data['header'] .= form_close();
                        $data['header'] .= form_error('lift_choose_name');
                        // New view: edit form data
                        $data['in_edit_mode']      = true;
                        $data['edit_form_open']    = form_open('lift_controller/edit_name/'.$id_created_lifts.'/'.$currentResortID.'/'.$id_group.'/'.$id_group_location);
                        $data['edit_name_current'] = set_value('lift_choose_name', $lift_name_to_show);
                        $data['edit_form_close']   = form_close();
                    }
                    $data['header'] .= '<div class="col-md-12">';
                    $data['header'] .= '<div class="col-md-4"><h3>'.$this->lang->line('lift')['build_info'].'</h3>';
                }
                // END BUILDING HEADER WHEN ALREADY BUILT

                // For a normal display, after changing the deserving slopes or editing the name or building the lift
                // We display the Edit logo (not the form yet)
                if (!isset($action) || $action == 'name_changed' || $action == 'ongoing_construction_lift' || $action == 'lift_upgraded' || $action == 'lift_repaired' || $action == 'bad_status' || $action == 'rush_completed' || $action == 'already_completed' || $action == 'not_enough_genepis' || isset ($_POST['buildForm'])) {
                    $data['header'] = '<h2>'.$lift_name_to_show.$this->lang->line('resort')['info_title_loc'].$id_sector;
                    if ($is_the_lift_built->num_rows() > 0)
                        $data['header'] .= '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['edit'].'"><a href="'.base_url('lift_controller/edit_name_mode/'.$id_group.'/'.$currentResortID.'/'.$id_group_location).'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a></div>';
                    $data['header'] .= '</h2>';
                    $data['header'] .= '<div class="col-md-12">';
                    $data['header'] .= '<div class="col-md-4"><h3>'.$this->lang->line('lift')['build_info'].'</h3>';
                }
                $only_display_info = true;              // For the correct loop below.
                
                // START CHECK SECTOR ACCESS
                $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                if (isset($sector_access_array[$id_sector]) && $sector_access_array[$id_sector] == $id_sector){            
                    $data['action'] = 'sector_unlocked';
                }
                else {
                    $data['action'] = 'sector_locked';
                }
                // END CHECK SECTOR ACCESS

                // Get the different fields for the info block
                $data['id_sector'] = $id_sector;
                $data['id_group'] = $id_group;
                $current_speed = $speed[$current_player_lift_level];
                $current_throughput = $throughput[$current_player_lift_level];
                $current_capacity = $capacity[$current_player_lift_level];
                $data['lift_speed'] = $current_speed;
                $data['lift_throughput'] = $current_throughput;
                $data['lift_capacity'] = $current_capacity;
                $length = get_lift_length ($id_group_location, 'lift');
                
                $type = $genericLiftData->lift_type;
                $lift_types = $this->item_model->get_lift_types_DB($type);
                $lift_types_object = $lift_types->row();
                $data['lift_type'] = $lift_types_object->$name_language;
                $data['length'] = $length;
                
                $grip_type = $genericLiftData->grip_type;
                $grip_types = $this->item_model->get_grip_types_DB($grip_type);
                $grip_types_object = $grip_types->row();
                $data['grip_type'] = $grip_types_object->$name_language;
                $lift_type_no_space = str_replace(' ', '_', $lift_types_object->name_english);
                $lift_type_no_space = strtolower($lift_type_no_space);
                $data['lift_type_img_name'] = $lift_type_no_space.'_grip'.$grip_type.'_cap'.$current_capacity;

                    
                // START BUILDING LIFT TO BUILD (FORM)
                    if ($action != 'sector_locked') {               // If the sector is locked, we display "Locked"
                        if ($var_lift_is_built == true) { // Only if the lift is built
                            
                            // START RUSH BUTTON UPGRADE/BUILD
                            $button_level = array();
                            for ($i=1;$i<=3;$i++) {
                                $button_level[$i] = '';
                            }
                            
                            $level = $is_the_lift_builtData->level;
                            
                            if ( isset($time_left_value) && $time_left_value > 0 && $lift_status_id != 3 ) {
                                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                                if ($genepis_required_to_rush <= $genepis_available) {
                                    $button_level[$level] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url('lift_controller/rush/'.$id_group_location.'/'.$id_created_lifts).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                                }
                                else {
                                    $button_level[$level] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                                }   
                            }
                            // END RUSH BUTTON
                            
                            
                            if ($is_the_lift_builtData->level >= 2){
                                $button_upgrade_1 = '<button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button>';
                            }
                            else if ($is_the_lift_builtData->id_status == '4' || $is_the_lift_builtData->id_status == '3' || $is_the_lift_builtData->id_status == '5'){
                                $button_upgrade_1 = '<button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrade'].'</button>';
                            }
                            else {
                                $button_upgrade_1 = '<a href="'.base_url('lift_controller/upgrade/'.$id_group_location.'/'.$id_created_lifts.'/'.$currentResortID.'/2').'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a>';   
                            }
                            if ($is_the_lift_builtData->level != 2) {
                                $button_upgrade_2 = '<button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button>';
                            }
                            else if ($is_the_lift_builtData->id_status == '4' || $is_the_lift_builtData->id_status == '3' || $is_the_lift_builtData->id_status == '5'){
                                $button_upgrade_2 = '<button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrade'].'</button>';
                            }
                            else {
                                $button_upgrade_2 = '<a href="'.base_url('lift_controller/upgrade/'.$id_group_location.'/'.$id_created_lifts.'/'.$currentResortID.'/3').'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a>';
                            }
                            $field_choose_name = '';
                            $data['button_upgrade_l2'] = $button_upgrade_1;
                            $data['button_upgrade_l3'] = $button_upgrade_2;
                            $data['button_rush']       = $button_level;
                                                    
                            if (isset($action) && $action != 'not_enough_money' && $action != 'lift_to_build' && $action != 'lift_to_edit') {
                                $data['infoMessage'] = $this->lang->line('lift')[$action];
                            }
                            else if (isset($action) && $action == 'not_enough_money') {
                                $data['infoMessage'] = '<div class="alert alert-danger text-center">'.$this->lang->line('home')[$action].'</div>';
                            }
                        }
                    }
                    // END BUILDING LIFT TO BUIL (FORM)  
                $data['levels_data'] = [];
                for ($l = 1; $l <= 3; $l++) {
                    $data['levels_data'][$l] = [
                        'speed'         => $speed[$l],
                        'capacity'      => $capacity[$l],
                        'throughput'    => $throughput[$l],
                        'base_cost'     => $base_cost[$l],
                        'meter_cost'    => $meter_cost[$l],
                        'upgrade_cost'  => isset($upgrade_cost[$l]) ? $upgrade_cost[$l] : null,
                        'building_time' => display_friendly_time($building_time[$l]),
                        'reputation'    => $reputation[$l],
                    ];
                }

                // START MODULAR UPGRADES PANEL
                if (isset($var_lift_is_built) && $var_lift_is_built && isset($id_created_lifts)) {
                    $all_modules   = $this->item_model->get_lift_module_upgrades_DB();
                    $inst_result   = $this->item_model->get_lift_modules_installed_DB($id_created_lifts);
                    $installed_ids = [];
                    foreach ($inst_result->result() as $inst) {
                        $installed_ids[] = (int) $inst->id_module;
                    }
                    // id_status: 3 = under maintenance, 4 = under construction, 5 = out of order
                    $lift_blocked = in_array($lift_status_id, ['3', '4', '5']);
                    $name_col_mod = 'name_'.$this->session->userdata('site_lang');
                    $desc_col_mod = 'description_'.$this->session->userdata('site_lang');
                    $data['lift_blocked']  = $lift_blocked;
                    $data['modules_data']  = [];

                    $data['modular_upgrades_section']  = '<div class="w-full overflow-x-auto">';
                    $data['modular_upgrades_section'] .= '<h4>'.$this->lang->line('lift')['modular_upgrades_title'].'</h4>';
                    $data['modular_upgrades_section'] .= '<table class="table table-responsive building_6th"><thead>';
                    $data['modular_upgrades_section'] .= '<tr><th>'.$this->lang->line('lift')['modular_upgrades_title'].'</th>';
                    $data['modular_upgrades_section'] .= '<th>'.$this->lang->line('lift')['modular_cost'].'</th>';
                    $data['modular_upgrades_section'] .= '<th>'.$this->lang->line('lift')['modular_speed_bonus'].'</th>';
                    $data['modular_upgrades_section'] .= '<th>'.$this->lang->line('lift')['modular_throughput_bonus'].'</th>';
                    $data['modular_upgrades_section'] .= '<th>'.$this->lang->line('lift')['modular_capacity_bonus'].'</th>';
                    $data['modular_upgrades_section'] .= '<th>'.$this->lang->line('lift')['modular_reputation_bonus'].'</th>';
                    $data['modular_upgrades_section'] .= '<th>'.$this->lang->line('lift')['modular_daily_cost'].'</th>';
                    $data['modular_upgrades_section'] .= '<th></th></tr></thead><tbody>';

                    foreach ($all_modules->result() as $mod) {
                        $is_installed = in_array((int) $mod->id_module, $installed_ids);
                        $mod_name = $mod->$name_col_mod ?? $mod->name_english ?? '';
                        $mod_desc = $mod->$desc_col_mod ?? $mod->description_english ?? '';
                        $mod_cost             = $mod->cost ?? 0;
                        $mod_speed_bonus      = $mod->speed_bonus ?? 0;
                        $mod_throughput_bonus = $mod->throughput_bonus ?? 0;
                        $mod_capacity_bonus   = $mod->capacity_bonus ?? 0;
                        $mod_reputation_bonus = $mod->reputation_bonus ?? 0;
                        $mod_daily_cost       = $mod->daily_cost_increase ?? 0;
                        $data['modular_upgrades_section'] .= '<tr>';
                        $data['modular_upgrades_section'] .= '<td><strong>'.$mod_name.'</strong><br><small>'.$mod_desc.'</small></td>';
                        $data['modular_upgrades_section'] .= '<td>'.number_format($mod_cost, 0, ',', ' ').' €</td>';
                        $data['modular_upgrades_section'] .= '<td>'.($mod_speed_bonus > 0 ? '+'.$mod_speed_bonus.' '.$this->lang->line('lift')['speed_unit'] : '-').'</td>';
                        $data['modular_upgrades_section'] .= '<td>'.($mod_throughput_bonus > 0 ? '+'.$mod_throughput_bonus.' '.$this->lang->line('lift')['throughput_unit'] : '-').'</td>';
                        $data['modular_upgrades_section'] .= '<td>'.($mod_capacity_bonus > 0 ? '+'.$mod_capacity_bonus : '-').'</td>';
                        $data['modular_upgrades_section'] .= '<td>'.($mod_reputation_bonus > 0 ? '+'.$mod_reputation_bonus : '-').'</td>';
                        $data['modular_upgrades_section'] .= '<td>+'.number_format($mod_daily_cost, 0, ',', ' ').' €</td>';
                        if ($is_installed) {
                            $data['modular_upgrades_section'] .= '<td><span class="badge bg-success">'.$this->lang->line('lift')['modular_installed'].'</span></td>';
                        } elseif ($lift_blocked) {
                            $data['modular_upgrades_section'] .= '<td><button class="btn btn-warning btn-sm disabled">'.$this->lang->line('lift')['modular_install_btn'].'</button></td>';
                        } else {
                            $data['modular_upgrades_section'] .= '<td><a href="'.base_url('lift_controller/install_module/'.$id_group_location.'/'.$id_created_lifts.'/'.$currentResortID.'/'.$mod->id_module).'"><button class="btn btn-success btn-sm">'.$this->lang->line('lift')['modular_install_btn'].'</button></a></td>';
                        }
                        $data['modular_upgrades_section'] .= '</tr>';
                        // Also build structured data for new view
                        $data['modules_data'][] = [
                            'id'               => $mod->id_module,
                            'name'             => $mod_name,
                            'description'      => $mod_desc,
                            'cost'             => $mod_cost,
                            'speed_bonus'      => $mod_speed_bonus,
                            'throughput_bonus' => $mod_throughput_bonus,
                            'capacity_bonus'   => $mod_capacity_bonus,
                            'reputation_bonus' => $mod_reputation_bonus,
                            'daily_cost'       => $mod_daily_cost,
                            'installed'        => $is_installed,
                            'install_url'      => base_url('lift_controller/install_module/'.$id_group_location.'/'.$id_created_lifts.'/'.$currentResortID.'/'.$mod->id_module),
                        ];
                    }
                    $data['modular_upgrades_section'] .= '</tbody></table></div>';
                }
                // END MODULAR UPGRADES PANEL

            }
            // No generic lift has been returned (something went wrong with the page/url)
            else {
                $data['action'] = 'lift_not_found';
                $data['infoMessage'] = $this->lang->line('lift')['not_found'];
                $data['main_content'] = 'lift';                                    // display the Slope view if the lift is not found
                $this->load->view('templates/default',$data);
            }
            if (isset($only_display_info) && $only_display_info == true){
                $data['main_content'] = 'lift';                                    // display the Slope view with no extra element (simply displaying info)
                $this->load->view('templates/default',$data); 
            }
            return $data;
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }    
    }
    
   
    /**
     * edit_name                        Edits the name of the lift
     * 
     * @param type $id_created_lifts    ID of the created lift by the user
     * @param type $currentResortID       Current user ID
     * @param type $id_lift             Generic ID of the lift
     */
    public function edit_name($id_created_lifts, $currentResortID, $id_lift, $id_group_location){  
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('lift_choose_name', $this->lang->line('resort')['name_field'], 'trim|required|min_length[3]|max_length[35]');

        if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
            // We display the same form again, with errors
            $data = $this->edit_name_mode($id_lift, $currentResortID);
        }
        else {    // TRUE: all fields are correct
            // Change the name
            $editLiftName = $this->item_model->editItemName($currentResortID, $id_created_lifts, $this->input->post('lift_choose_name', TRUE), 'lift'); 
            if ($editLiftName == true) {
                $data = $this->show_info_block_lift($id_lift, $currentResortID, 'name_changed', $id_group_location);    // Gets all the player lift info with "lift_to_edit" status  
            }
            else
                $data = $this->edit_name_mode($id_lift, $currentResortID);
        }
    }
    
    /**
     * edit_name_mode               Temporary page in edit mode
     * @param type $id_lift         Generic ID of the lift
     * @param type $currentResortID   Current resort ID
     */
    public function edit_name_mode($id_lift, $currentResortID, $id_group_location){  
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        $data = $this->show_info_block_lift($id_lift, $currentResortID, 'lift_to_edit', $id_group_location);    // Gets all the player lift info with "lift_to_edit" status (edit logo)
    }
    
    /**
     * upgrade                  Upgrades the lift
     * 
     * @param type $id_lift             ID of the generic lift
     * @param type $id_created_lifts    ID of the created lift
     * @param type $currentResortID       Current resort ID
     * @param type $new_level           New level (target level) to upgrade to
     */
    public function upgrade($id_group_location, $id_created_lifts, $currentResortID, $new_level){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        if ($new_level >1 && $new_level <= 3) {     // Only if level is 1 to 3
            
            $lift_is_built = $this->item_model->check_if_player_has_built_item_location($id_group_location, $currentResortID, 'lift');    // check if user has built this lift
            $lift_is_built_Data = $lift_is_built->row();  // we put the result in a array
            //$id_group_location = $lift_is_built_Data->id_group_location;
            $id_group = $lift_is_built_Data->id_group;
            if ($lift_is_built->num_rows() > 0 && $lift_is_built_Data->id_created_lifts == $id_created_lifts) {    // if the lift is already created (ok) and matches the one in argument
                $genericLift = $this->item_model->get_generic_item_info_for_level($id_group, 'lift', $new_level);  // we get the generic lift information
                if ($genericLift->num_rows() > 0) {             // if the genereic lift exists
                    $slopes_under_construction = count_ongoing_building_items('lift', '4');  // type = lift and "4" is Under construction status
                    $slopes_under_maintenance = count_ongoing_building_items('lift', '3');  // type = lift and "3" is Under maintenance status
                    if ($slopes_under_construction == 0 && $slopes_under_maintenance == 0) {    // if there is no ongoing slope construction
                        $genericLiftData = $genericLift->row();     // we put the result in a array
                        $current_time = time(); 
                        $time_construction_duration = $genericLiftData->building_time/ACCELERATOR_FACTOR;                 // store the building time of the lift
                        $gain_reputation = $genericLiftData->reputation;    // reputation to gain
                        $end_construction_timestamp = $current_time + $time_construction_duration;      // when the construction is supposed to end (timestamp format)
                        //$end_construction_datetime = date('Y-m-d H:i:s', $end_construction_timestamp);  // when the construction is supposed to end (value to put in DB)
                        $cost_lift = get_cost ($id_group, 'lift', $new_level, $id_group_location);         
                        $new_id_lift = $genericLiftData->id_lift; 
                        $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                        $money_after_payment = $cash_player - $cost_lift;           // we calculate hos much the player will have left after the payment
                        if ($money_after_payment >= 0) {                            // If enough cash
                            $id_status_lift = $lift_is_built_Data->id_status;       // Current status of the lift
                            if ($id_status_lift != '3' && $id_status_lift != '4' && $id_status_lift != '5') { // If the lift is not in maintenance or construction, we proceed
                                $current_level_lift = $lift_is_built_Data->level;
                                if (($new_level == '2' || $new_level == '3') && $new_level == $current_level_lift+1 ) { // The level has to be current + 1 only, either 2 or 3
                                    if ($removeCashQuery = $this->users_model->pay_item($cost_lift, $cash_player)){      //the paiment for the lift has been taken
                                        // we upgrade the lift in the DB
                                        if ($buildQuery = $this->item_model->upgrade_lift($new_level, $end_construction_timestamp, $id_created_lifts, 'lift')){   //the lift has been upgraded successfully
                                            $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                                            $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                                            $add_reputation = $this->users_model->add_reputation($gain_reputation);
                                            $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                                            $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                                            $data = $this->show_info_block_lift($id_group, $currentResortID, 'lift_upgraded', $id_group_location);  // we display the info block with 'lift_upgraded' parameter
                                            // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_lift, 'cost_purchases');
                                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_lift, 'expenses');
                                            $currentUserID = $this->users_model->get_user_id_from_resortID($currentResortID);
                                            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                                            $custom_name = $lift_is_built_Data->custom_name;
                                            $data = array (
                                                'id_resort' => $currentResortID,  
                                                'id_building' => $id_group,       // ID of the building in the game_buildings table
                                                'level' => $new_level,             // only one level for tourist info
                                                'type' => 'lift',
                                            );
                                            $call_achievements_check = call_achievements_check($data, 'upgrade');   // Builds the building in the DB
                                            $call_achievements_check = call_achievements_check($data = array('id_resort' => $currentResortID, 'quantity' => $cost_lift), 'upgrade_amount');   // Check upgrading achievements
                                            $call_achievements_check = call_achievements_check($data_ach_build = array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_lift), 'build_amount');   // Check spending achievements
                                            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $this->lang->line('logs')['upgrade_of'].$custom_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $this->lang->line('logs')['upgrade_of'].$custom_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                        }
                                        else {                        //upgrade of lift failed. Should NEVER happen
                                            $data['infoMessage'] = $this->lang->line('lift')['lift_upgraded_failed'];
                                            $data['main_content'] = 'lift';
                                            $this->load->view('templates/default',$data);
                                        }
                                    }
                                    else {                        //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                                            $data['infoMessage'] = $this->lang->line('home')['not_enough_money'];
                                            $data['main_content'] = 'lift';
                                            $this->load->view('templates/default',$data);
                                    }
                                }
                                else {
                                    $data = $this->show_info_block_lift($id_group, $currentResortID, 'bad_level', $id_group_location);  // we display the info block with 'bad_level' parameter
                                }
                            }
                            else {
                                    $data = $this->show_info_block_lift($id_group, $currentResortID, 'bad_status', $id_group_location);  // we display the info block with 'bad_status' parameter
                                }
                        }
                        else {                        //not enough money
                            $data = $this->show_info_block_lift($id_group, $currentResortID, 'not_enough_money', $id_group_location);  // we display the info block with 'not_enough_money' parameter
                        }
                    }
                    else {                        // ongoing construction
                        $data = $this->show_info_block_lift($id_group, $currentResortID, 'ongoing_construction_lift', $id_group_location);  // we display the info block with 'ongoing_construction' parameter
                    }
                }
                else    // Wrong ID/lift in argument
                    redirect('resort_controller');
            }
            else{    // Wrong ID/lift in argument
                redirect('resort_controller');
            }
        }
        else    // Wrong level in argument
            redirect('resort_controller');
    }
    
    
    public function repair_lift(){
        
        $id_created_item = trim($this->input->post('id_item', TRUE));         // generic ID in the game_created_lifts/slopes table
        $id_group = trim($this->input->post('id_group', TRUE));         // generic ID in the game_created_lifts/slopes table
        $currentResortID = trim($this->input->post('currentResortId', TRUE));
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE     
        
        $lift_is_built = $this->item_model->check_if_player_has_built_item($id_created_item, $currentResortID, 'lift');    // check if user has built this lift
        $lift_is_built_Data = $lift_is_built->row();  // we put the result in a array
        
        if ($lift_is_built->num_rows() > 0 && $lift_is_built_Data->id_created_lifts == $id_created_item) {    // if the lift is already created (ok) and matches the one in argument
            $genericLift = $this->item_model->get_generic_item_info_for_level($id_group, 'lift', $lift_is_built_Data->level);  // we get the generic lift information
            if ($genericLift->num_rows() > 0) {             // if the genereic lift exists
                    $genericLiftData = $genericLift->row();     // we put the result in a array
                    $current_time = time();
                    $end_repair_timestamp = $current_time + 21600;      // when the repair is supposed to end (timestamp format)
                    $end_repair_datetime = gmdate('Y-m-d H:i:s',$end_repair_timestamp);
                    $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                    $repair_cost = $lift_is_built_Data->repair_cost;            // Get repair cost from DB
                    $money_after_payment = $cash_player - $repair_cost;           // we calculate hos much the player will have left after the payment
                    if ($money_after_payment >= 0) {                            // If enough cash
                        $id_status_lift = $lift_is_built_Data->id_status;       // Current status of the lift
                        if ($id_status_lift == '5') { // If the lift is out of order (only)
                            $assigned_mechanics = $this->item_model->count_assigned_mechanics_db($currentResortID, $id_created_item);
                            $nb_assigned_mechanics = $assigned_mechanics->num_rows();
                            if ($nb_assigned_mechanics == '1') {
                                if ($removeCashQuery = $this->users_model->pay_item($repair_cost, $cash_player)){      //the paiment for the lift has been taken
                                    // we repair the lift in the DB
                                    if ($repairQuery = $this->item_model->repair_lift_DB($currentResortID, $end_repair_datetime, $id_created_item)){   //the lift has been repaired successfully
                                        $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                                        $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                                        // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                                        $add_cost_history_table = add_cost_stat_table($currentResortID, $repair_cost, 'cost_upkeep');
                                        $add_cost_history_table = add_cost_stat_table($currentResortID, $repair_cost, 'expenses');
                                        $currentUserID = $this->users_model->get_user_id_from_resortID($currentResortID);
                                        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                                        $custom_name = $lift_is_built_Data->custom_name;
                                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $this->lang->line('logs')['repair_of'].$custom_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $this->lang->line('logs')['repair_of'].$custom_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                        $data_achievement = array (
                                            'id_resort' => $currentResortID,
                                            'id_building' => $id_group,       
                                            'type' => 'lift'
                                        );
                                        $call_achievements_check = call_achievements_check($data_achievement, 'repair');
                                        echo json_encode(array('returned' => true, 'status' => 'lift_repaired'));                                        
                                    }
                                    else {                        //upgrade of lift failed. Should NEVER happen
                                        echo json_encode(array('returned' => false, 'status' => 'not_completed'));
                                    }
                                }
                                else {                        //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                                    echo json_encode(array('returned' => false, 'status' => 'not_enough_money'));
                                }
                            }
                            else {
                                echo json_encode(array('returned' => false, 'status' => 'no_mechanics'));
                            }
                        }
                        else {
                            echo json_encode(array('returned' => false, 'status' => 'not_completed'));
                        }
                    }
                    else {                        //not enough money
                        echo json_encode(array('returned' => false, 'status' => 'not_enough_money'));
                    }
            }
            else    // Wrong ID/lift in argument
                redirect('resort_controller');
        }
        else{    // Wrong ID/lift in argument
            redirect('resort_controller');
        }
    }
    
    
    public function rush($id_group_location, $id_created_item){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $currentUserID = $this->users_model->get_user_id();      
        $currentResortID = $this->users_model->get_resort_id($currentUserID); 
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
            
        $lift_is_built = $this->item_model->check_if_player_has_built_item_location($id_group_location, $currentResortID, 'lift');    // check if user has built this lift
        $lift_is_built_Data = $lift_is_built->row();  // we put the result in a array

        if ($lift_is_built->num_rows() > 0 && $lift_is_built_Data->id_created_lifts == $id_created_item) {    // if the lift is already created (ok) and matches the one in argument
            $end_construction = $lift_is_built_Data->end_construction;
            $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
            $currenttime = time();                                          // current timestamp
            $time_left_value = $timestamp - $currenttime;                   // Time left in seconds

            if ($time_left_value > 0){ 
                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);

                if ($genepis_required_to_rush <= $genepis_available) {
                    $remove_genepis_cost = $this->users_model->remove_genepis_cost_DB($genepis_required_to_rush);
                    $data = $this->lang->line('home')['you_have_rushed'].' '.$lift_is_built_Data->custom_name.' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'];
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data) );   // Add a log row to the game_player_logs table

                    $complete_construction = $this->building_model->complete_construction_DB($id_created_item, 'game_created_lifts', 'id_created_lifts', 'end_construction');

                    $data = $this->show_info_block_lift($lift_is_built_Data->id_group, $currentResortID, 'rush_completed', $id_group_location);
                }
                else {  // Not enough genepis
                    $data = $this->show_info_block_lift($lift_is_built_Data->id_group, $currentResortID, 'not_enough_genepis', $id_group_location);
                }
            }
            else {  // No time left. Refresh page
                $data = $this->show_info_block_lift($lift_is_built_Data->id_group, $currentResortID, 'already_completed', $id_group_location);
            }
        }
        else{    // Wrong ID/lift in argument
            redirect('resort_controller');
        }
        
    }

    /**
     * get_altitude_build_cost_multiplier   Proxy to shared helper function.
     *
     * @param string $altitude 'low' | 'medium' | 'high'
     * @return float
     */
    protected function get_altitude_build_cost_multiplier($altitude) {
        return get_altitude_build_cost_multiplier($altitude);
    }


    
    
    /**
     * install_module  Purchases and installs a modular upgrade on an existing lift.
     *
     * @param int $id_group_location   Location group of the lift
     * @param int $id_created_lifts    ID in game_created_lifts
     * @param int $currentResortID
     * @param int $id_module           ID of the module to install
     */
    public function install_module($id_group_location, $id_created_lifts, $currentResortID, $id_module) {
        $currentUserID = $this->users_model->get_user_id();
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $lift_is_built = $this->item_model->check_if_player_has_built_item_location($id_group_location, $currentResortID, 'lift');
        if ($lift_is_built->num_rows() == 0)
            redirect('resort_controller');

        $lift_data = $lift_is_built->row();
        $id_group = $lift_data->id_group;

        // Lift must be operational or closed - not under construction / maintenance / out of order
        if (in_array($lift_data->id_status, ['3', '4', '5'])) {
            $this->show_info_block_lift($id_group, $currentResortID, 'modular_bad_lift_status', $id_group_location);
            return;
        }

        // Load module definition
        $modules_result = $this->item_model->get_lift_module_upgrades_DB();
        $module_def = null;
        foreach ($modules_result->result() as $m) {
            if ($m->id_module == $id_module) {
                $module_def = $m;
                break;
            }
        }
        if ($module_def === null)
            redirect('resort_controller');

        // Check not already installed
        $installed = $this->item_model->get_lift_modules_installed_DB($id_created_lifts);
        foreach ($installed->result() as $inst) {
            if ($inst->id_module == $id_module) {
                $this->show_info_block_lift($id_group, $currentResortID, 'modular_already_exists', $id_group_location);
                return;
            }
        }

        // Check funds
        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $module_def->cost) {
            $this->show_info_block_lift($id_group, $currentResortID, 'modular_not_enough_money', $id_group_location);
            return;
        }

        // Deduct cost
        if (!$this->users_model->pay_item($module_def->cost, $cash_player)) {
            $this->show_info_block_lift($id_group, $currentResortID, 'modular_installed_fail', $id_group_location);
            return;
        }

        // Install module
        if ($this->item_model->install_lift_module_DB($currentResortID, $id_created_lifts, $id_module)) {
            $updated_cash = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $updated_cash);
            if ($module_def->reputation_bonus > 0) {
                $this->users_model->add_reputation($module_def->reputation_bonus);
                $reputation_player = $this->users_model->get_reputation_player();
                $this->session->set_userdata('reputation', $reputation_player);
            }
            add_cost_stat_table($currentResortID, $module_def->cost, 'cost_purchases');
            add_cost_stat_table($currentResortID, $module_def->cost, 'expenses');
            $log_data = $this->lang->line('logs')['lift'].': '.$module_def->name_english.' on '.$lift_data->custom_name;
            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
            $this->show_info_block_lift($id_group, $currentResortID, 'modular_installed_ok', $id_group_location);
        } else {
            $this->show_info_block_lift($id_group, $currentResortID, 'modular_installed_fail', $id_group_location);
        }
    }


}