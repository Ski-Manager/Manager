<?php
class Slope_controller extends CI_Controller{
    
    // Defines global variables
    private $siteLang;
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
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('lift',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)       // Only for logged in users
            redirect('home_controller');                             // If not logged in, redirect to homepage
        $this->load->model('item_model');
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('building_model');
    }
    
    public function index(){        
        redirect('resort_controller');
    }
    
    /**
     * display_slope_to_build   Called when the user wants to build the slope
     * 
     * @param type $id_sector
     * @param type $id_slope
     * @param type $currentResortID
     */
    public function display_slope_to_build($id_sector, $id_slope, $currentResortID){        
        if ($test = $this->item_model->get_generic_item_info($id_slope, 'slope')){  // If there is a matching slope with the passed variables...
            // Check if sector unlocked
            $currentUserID = $this->users_model->get_user_id();
            $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
            if ($resultResort->num_rows() > 0) {
                $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                if (isset($sector_access_array[$id_sector]) && $sector_access_array[$id_sector] == $id_sector){
                    $status_sector = 'sector_unlocked';
                }
                else {
                    $status_sector = 'sector_locked';
                }
                $data = $this->show_info_block_slope($id_slope, $currentResortID, $status_sector);    // Gets all the player slope info with "sector_locked" status
            }
            else {
                $data = $this->show_info_block_slope($id_slope, $currentResortID, 'slope_to_build');    // Gets all the player slope info with "slope_to_build" status
            }
        } else {
            $data = $this->show_info_block_slope($id_slope, $currentResortID, 'slope_not_found');    // Shows that the slope couldn't be found
        }  
    }
    
    /**
     * build_slope  Bluids the slope and adds it to created slopes for this player
     * 
     * @param type $currentResortID
     */
    public function build_slope(){
        if (isset ($_POST['buildForm'])) {         // if the form was posted
            $currentUserID = $this->users_model->get_user_id();
            $currentResortID = $this->users_model->get_resort_id($currentUserID);
            
            $posted_id_slope = $this->input->post('form_id_slope', TRUE);
            $id_slope_type = $this->input->post('form_id_slope_type_from_page', TRUE);
            $posted_difficulty = $this->input->post('form_difficulty', TRUE);
            
            $slope_meter_price_local = SLOPE_METER_PRICE[$id_slope_type-1];
            $slope_meter_building_time_local = SLOPE_METER_BUILDING_TIME[$id_slope_type-1];
            
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('form_id_group_location', 'form_id_group_location', 'trim|required');
            $this->form_validation->set_rules('form_difficulty', 'form_difficulty', 'trim|required');
            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                // We display the same form again, with errors
                $data = $this->show_info_block_slope($posted_id_slope, $currentResortID, 'slope_to_build');
                $data['slope_error_name'] = form_error('form_id_group_location');
                $data['slope_form_difficulty'] = form_error('form_difficulty');
            }
            else {    // TRUE: all fields are correct
                $currentUserID = $this->users_model->get_user_id();
                $genericSlope = $this->item_model->get_generic_item_info($posted_id_slope, 'slope');  // we get the generic slope information

                // Check if we are not building another slope
                $slopes_under_construction = count_ongoing_building_items('slope', '4');  // type = slope and "4" is Under construction status
                if ($slopes_under_construction == 0) {    // if there is no ongoing slope construction
                    $genericSlopeData = $genericSlope->row();  // we put the result in a array
                    $current_time = time(); 
                    $id_sector = $genericSlopeData->id_sector;  
                    
                    $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                    $column_name = 'name_'.$player_preferred_lang;
                    $default_name = $genericSlopeData->$column_name;    
                    
                    $length = $genericSlopeData->length;

                    $time_construction_duration = $length * $slope_meter_building_time_local / ACCELERATOR_FACTOR;                 // store the building time of the slope    
                    $gain_reputation = $genericSlopeData->reputation;    // reputation to gain
                    $end_construction_timestamp = $current_time + $time_construction_duration;      // when the construction is supposed to end (timestamp format)
                    //$end_construction_datetime = date('Y-m-d H:i:s', $end_construction_timestamp);  // when the construction is supposed to end (value to put in DB)
                    $resort_info_row = $this->resort_model->display_resort_info_DB($currentResortID)->row();
                    $altitude_cost_mult = $this->get_altitude_build_cost_multiplier(isset($resort_info_row->altitude) ? $resort_info_row->altitude : 'medium');
                    $cost_slope = (int) round($length * $slope_meter_price_local * $altitude_cost_mult);
                    $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                    $money_after_payment = $cash_player - $cost_slope;          // we calculate hos much the player will have left after the payment
                    
                    
                    $new_slope_created_insert_data = array (
                        'id_resort' => $currentResortID,
                        'id_slope' => $posted_id_slope,
                        'id_difficulty' => $posted_difficulty,
                        'custom_name' => $default_name,
                        'slope_condition' => '100',
                        'id_status' => '4',
                        'end_construction' => gmdate('Y-m-d H:i:s',$end_construction_timestamp)
                    );
                    
                    
                    
                    if ($money_after_payment >= 0) { 
                        if ($removeCashQuery = $this->users_model->pay_item($cost_slope, $cash_player)){      //the paiment for the slope has been taken
                            // we create the slope in the DB
                            if ($buildQuery = $this->item_model->build_slope($new_slope_created_insert_data)){   //the slope has been built successfully
                                $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                                $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                                $add_reputation = $this->users_model->add_reputation($gain_reputation);
                                $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                                $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                                $data = $this->show_info_block_slope($posted_id_slope, $currentResortID, 'slope_built');  // we display the info block with 'slope_built' parameter
                                // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                                $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_slope, 'cost_purchases');
                                $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_slope, 'expenses'); 
                                $data['infoSlope'] = $this->lang->line('slope')['built_successful'];
                                $data = array (
                                    'id_resort' => $currentResortID,
                                    'id_building' => $posted_id_slope,       // ID of the l in the game_lifts table
                                    'type' => 'slope',       
                                    'level' => '1'
                                );
                                $data_achievement_slope_type = array (
                                    'id_resort' => $currentResortID,
                                    'id_slope_type' => $id_slope_type,       
                                    'quantity' => '1'
                                );
                                $data_achievement = array (
                                    'id_resort' => $currentResortID,
                                    'id_building' => $posted_id_slope,       // ID of the l in the game_lifts table
                                    'type' => 'slope',       
                                    'level' => '1',
                                    'id_sector' => $id_sector
                                );
                                $call_achievements_check = call_achievements_check($data_achievement, 'build');  
                                $call_achievements_check = call_achievements_check($data_achievement_slope_type, 'build_slope_type');  
                                $call_achievements_check = call_achievements_check($data = array('id_resort' => $currentResortID, 'quantity' => $cost_slope), 'build_amount');   // Check spending achievements
                                $user_is_referred =  $this->users_model->check_user_is_referred($currentUserID);
                                if ($user_is_referred !== false) {
                                    $user_is_referred_data = $user_is_referred->row();
                                    $id_referral_player = $user_is_referred_data->id_referral_player;
                                    $id_referred_player = $user_is_referred_data->id_referred_player;
                                    $call_achievements_check = call_achievements_check($data = array('currentUserID' => $id_referral_player, 'quantity' => '1'), 'invite_friend');   // Check spending achievements
                                    $call_achievements_check = call_achievements_check($data = array('currentUserID' => $id_referred_player, 'quantity' => '1'), 'confirmed_active_referred_player');   // Check spending achievements
                                    $confirm_approved_referral = $this->users_model->confirm_approved_referral($id_referral_player, $id_referred_player);   // change the approved_referral field of game_referral_confirmed table to the current date                                   
                                }
                                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['slope'], 'data' => $this->lang->line('logs')['construction_of'].$default_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['slope'], 'data' => $this->lang->line('logs')['construction_of'].$default_name.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            }
                            else {                      //creation of slope failed. Should NEVER happen
                                $data['infoSlope'] = $this->lang->line('slope')['built_failed'];
                                $data['main_content'] = 'slope';
                                $this->load->view('templates/default',$data);
                            }
                        }
                        else {                      //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                            $data['infoSlope'] = $this->lang->line('home')['not_enough_money'];
                            $data['main_content'] = 'slope';
                            $this->load->view('templates/default',$data);
                        }
                    }
                    else {                       //not enough money 
                        $data = $this->show_info_block_slope($posted_id_slope, $currentResortID, 'not_enough_money');  // we display the info block with 'not_enough_money' parameter
                        $data['action'] = 'not_enough_money';
                    }
                }
                else {                       // ongoing construction
                    $data = $this->show_info_block_slope($posted_id_slope, $currentResortID, 'ongoing_construction_slope');  // we display the info block with 'ongoing_construction' parameter
                }
            }
        }
        else {
            redirect('resort_controller/');
        }
    }
    
    /**
     * check_if_slope_already_built Custom callback function to check if the slope had already been built by this player
     * 
     * @return boolean FALSE = already built (not valid). TRUE = not built (valid)
     */
    public function check_if_slope_already_built(){
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_slope = $this->input->post('id_slope', TRUE);
        $builtSlopeData = $this->item_model->check_if_player_has_built_item($id_slope, $currentResortID, 'slope');
        if ($builtSlopeData->num_rows() > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
        
    
    /**
     * show_info_block_slope  Display all the information for the slope
     * @param type $id_sector   Sector of the specific slope
     * @param type $id_slope    ID of the slope
     */
    /**
     * show_info_block_slope  Display all the information for the slope
     * 
     * @param type $id_sector       Sector of the specific slope
     * @param type $id_slope        ID of the slope
     * @param type $currentResortID
     * @param type $action          Get action from previous function or set to NULL if undefined
     * @return string
     */
    public function show_info_block_slope($id_slope, $currentResortID, $action = NULL){
        //$data['currentUserID'] = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        //if (!$user_is_allowed)
            //redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        $currentUserID = $this->users_model->get_user_id();
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // Checks if the resort exists       
            if ($checkIfResortExists->num_rows() > 0) {                                        // if the player has a resort, OK
                $genericSlope = $this->item_model->get_generic_item_info($id_slope, 'slope'); // get generic slope information from DB
                if ($genericSlope->num_rows() > 0) {                        // Should return a generic slope
                    $genericSlopeData = $genericSlope->row();
                    $id_sector = $genericSlopeData->id_sector;
                    $name_language = 'name_'.$this->session->userdata('site_lang');         // Prepare to get language info from DB such as "name_english"

                    $slope_meter_price_local = SLOPE_METER_PRICE[$genericSlopeData->slope_type-1];
                    $slope_meter_building_time_local = SLOPE_METER_BUILDING_TIME[$genericSlopeData->slope_type-1];
                        
                    $is_the_slope_built = $this->item_model->check_if_player_has_built_item($id_slope, $currentResortID, 'slope');     // check if slope is built
                    if ($is_the_slope_built->num_rows() > 0) { 
                        $is_the_slope_builtData = $is_the_slope_built->row();
                        $data['pre_slope_status'] = '';
                        $data['post_slope_status'] = ''; 
                        $data['slope_name_to_show'] = $is_the_slope_builtData->custom_name;         // Slope name for this user
                        $id_created_slopes = $is_the_slope_builtData->id_created_slopes;         // Id of the specific created slope
                        $data['slope_status'] = $this->lang->line('home')['status_built'];            // status (built is this loop)
                        $data['slope_condition'] = $is_the_slope_builtData->slope_condition.' %';   // condition of the slope (e.g 90%)

                        $slope_type_info = $this->item_model->get_slope_type_name($genericSlopeData->slope_type);     // gets the slope type friendly name base on slope type id
                        $slope_type_info_row = $slope_type_info->row();
                        $slope_type_friendly_name = $slope_type_info_row->$name_language;
                        
            
                        // Get the different fields
                        $data['id_sector'] = $id_sector;
                        $data['id_slope'] = $id_slope;
                        $data['slope_length'] = $genericSlopeData->length;
                        if (isset($is_the_slope_builtData)){
                            $nice_name_difficulty = $this->lang->line('slope')['diff_'.$is_the_slope_builtData->id_difficulty];  // Gives "slope_diff_2" or similar. Lang file looks like slope_diff_2 = Blue
                            $data['slope_difficulty'] = $nice_name_difficulty;          // Green, Blue, Red, Black
                            $nice_name_difficulty_english = $this->lang->line('slope')['diff_'.$is_the_slope_builtData->id_difficulty.'_english'];  // Gives "slope_diff_2" or similar. Lang file looks like slope_diff_2 = Blue
                            $data['slope_difficulty_english'] = $nice_name_difficulty_english;          // Green, Blue, Red, Black
                            $data['slope_type'] = $slope_type_friendly_name;          // Downhill, Snowpark, Boardercross, Crosscountry...
                        }
                        else {
                            $data['slope_difficulty'] = '-';         
                            $data['slope_type'] = '-';          
                            $data['slope_difficulty_english'] = 'green';        // Just need to fill the name with a color to render the picture in the page (the slope is not built anyways)     
                        }
                    
                    
                        // START BUILDING HEADER WHEN LIFT ALREADY BUILT (form for editing name)
                        if ($action == 'slope_to_edit') {
                            $data['header'] = '<h2>';
                            $data['header'] .= form_open('slope_controller/edit_name/'.$id_sector.'/'.$id_created_slopes.'/'.$currentResortID.'/'.$id_slope);
                            $data['header'] .= $this->lang->line('slope')['name_edit'];
                            $data['header'] .= '</h2>';
                            $data_input = array(
                                'name'        => 'slope_choose_name',
                                'id'          => 'slope_choose_name',
                                'value'       => set_value('slope_choose_name', $data['slope_name_to_show']),
                                'size'        => '35'
                              );
                            $data['header'] .= '<div class="inline">'.form_input($data_input);
                            $data['header'] .= form_submit($this->lang->line('home')['edit_name'], $this->lang->line('home')['edit_name'], "class='btn btn-success'").'</div>';
                            $data['header'] .= form_close();
                            $data['header'] .= form_error('slope_choose_name');
                            $data['header'] .= '<h3>'.$this->lang->line('slope')['build_info'].'</h3>';
                        }
                        // END BUILDING HEADER WHEN ALREADY BUILT
                        
                        // START BUILDING HEADER WHEN LIFT ALREADY BUILT (form for editing name)
                        if ($action == 'slope_to_edit_difficulty') {
                            //$data['header_difficulty'] = '<h2>';
                            $data['header_difficulty'] = form_open('slope_controller/edit_difficulty/'.$id_sector.'/'.$id_created_slopes.'/'.$currentResortID.'/'.$id_slope);
                            $data['header_difficulty'] .= $this->lang->line('slope')['difficulty'].' ';
                            //$data['header_difficulty'] .= '</h2>';
                            $data_input = array(
                                'name'        => 'slope_choose_difficulty',
                                'id'          => 'slope_choose_difficulty',
                                'value'       => set_value('slope_choose_difficulty', $data['slope_difficulty']),
                                'size'        => '35'
                              );
                            $data['header_difficulty'] .= '<div class="inline"><select class="inline" id="slope_choose_difficulty" name="slope_choose_difficulty">';
                        
                            $data['header_difficulty'] .= $this->get_select_difficulty($is_the_slope_builtData->id_difficulty, $name_language);
                            
                            $data['header_difficulty'] .= '</select> ';

                            $data['header_difficulty'] .= form_submit($this->lang->line('home')['change_difficulty'], $this->lang->line('home')['change_difficulty'], "class='btn btn-success'").'</div>';
                            $data['header_difficulty'] .= form_close();
                            $data['header_difficulty'] .= form_error('slope_choose_difficulty');
                            $data['header_difficulty'] .= '<h3>'.$this->lang->line('slope')['build_info'].'</h3>';
                        }
                        // END BUILDING HEADER WHEN ALREADY BUILT

                        // We display the Edit logo (not the form yet)
                        if (!isset($action) || $action == 'null' || $action == 'name_changed' || $action == 'difficulty_changed' || $action == 'bad_status' || $action == 'rush_completed' || $action == 'already_completed' || $action == 'not_enough_genepis' || isset ($_POST['buildForm'])) {
                            $data['edit_name_button'] = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['edit'].'"><a href="'.base_url('slope_controller/edit_name_mode/'.$id_sector.'/'.$id_slope.'/'.$currentResortID.'').'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a></div>';
                                
                            $data['edit_difficulty_button'] = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['change_difficulty'].'"><a href="'.base_url('slope_controller/edit_difficulty_mode/'.$id_sector.'/'.$id_slope.'/'.$currentResortID.'').'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a></div>';
                        }
                        
                        // Get the status of the slope
                        if ($is_the_slope_builtData->id_status == 1) {
                            $slope_status = 'close';
                        }
                        else if ($is_the_slope_builtData->id_status == 2) {
                            $slope_status = 'open';
                        }
                        else if ($is_the_slope_builtData->id_status == 3) {
                            $slope_status = 'maintenance';
                        }
                        else if ($is_the_slope_builtData->id_status == 4) {             // if under construction, we display how long is left
                            $slope_status = 'under_construction';
                            
                            // START time calculation and Countdown
                            $end_construction = $is_the_slope_builtData->end_construction;
                            $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
                            $currenttime = time();                                          // current timestamp
                            $time_left_value = $timestamp - $currenttime;                   // Time left in seconds

                            // START RUSH BUTTON
                            $rush_button = '';
                            
                            if ( isset($time_left_value) && $time_left_value > 0 ) {
                                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                                if ($genepis_required_to_rush <= $genepis_available) {
                                    $rush_button = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url('slope_controller/rush/'.$id_slope).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                                }
                                else {
                                    $rush_button = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                                } 
                            }
                            // END RUSH BUTTON
                            
                            if ($time_left_value <= '0'){                                   // If there is no time left (building finished)
                                $data['wait_status'] = true;   // If there is no time left, we define a new variable for the view
                                $data['pre_slope_status'] = '<a href="'.base_url().'resort_controller/"><div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
                                $data['slope_status'] = $this->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
                                $data['post_slope_status'] = '</div></a>';   // For toolpit (post)
                            }
                            else {   // If some time is left...
                                $data['pre_slope_status'] = $this->lang->line('home')['building_status_to_show_under_construction'].' ('.$this->lang->line('slope')['time_left'].' ';   // For toolpit (pre)
                                $data['slope_status'] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                                $data['post_slope_status'] = ') '.$rush_button;   // For toolpit (post)
                            }

                            // if the slope has just been built, we display the confimrmation message. We only do it in this loop because the status should aslway be under_construction straight after building
                            if (isset($action) && $action == 'slope_built'){
                                $data['action'] = 'slope_built';                // for the correct loop in the slope view
                                $data['infoSlope'] = $this->lang->line('slope')['built_successful'];
                            } 
                        }
                    }
                    else {                                      // slope is not built
                        // Populating the default slope name (before the user renames it)
                        $data['slope_name_to_show'] = $genericSlopeData->$name_language;    // This is the default name of the slope, e.g. "Slope 1"
                        $data['slope_status'] = $this->lang->line('slope')['status_not_built'];   
                        $data['slope_condition'] = '-';             // There is no condition to display
                        $data['slope_to_build'] = true;             // For the correct loop in the slope view
                        $data['pre_slope_status'] = '';
                        $data['post_slope_status'] = ''; 
                    }
                    if (isset($action) && $action == 'ongoing_construction_slope'){
                        $data['action'] = 'ongoing_construction_slope';
                    }
                    
                    $only_display_info = true;              // For the correct loop below.

                    // START CHECK SECTOR ACCESS
                    $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                    if (isset($sector_access_array[$id_sector]) && $sector_access_array[$id_sector] == $id_sector){            
                        
                    }
                    else {
                        $data['action'] = 'sector_locked';
                    }
                    // END CHECK SECTOR ACCESS

                    
                    $data['slope_cost'] = number_format($genericSlopeData->length*$slope_meter_price_local, 0, ',', ' ');          // separate 1000 with space (2 000 000)

                    $time = display_friendly_time($genericSlopeData->length*$slope_meter_building_time_local/ACCELERATOR_FACTOR); // calls function to convert seconds to friendly time (day/hours/minutes)
                    $data['slope_building_time'] = $time;
                    $data['reputation'] = $genericSlopeData->reputation;
                    if (isset($action) && $action == 'slope_to_build')
                        $data['slope_error_name'] = form_error('slope_choose_name');
                    if (isset($action) && $action == 'not_enough_money')
                        $data['action'] = 'not_enough_money';
                    if (isset($action))
                        $data['action'] = $action;
                    
                }
                else {
                    $data['action'] = 'slope_not_found';
                    $data['main_content'] = 'slope';                                    // display the Slope view if the slope is not found
                    $this->load->view('templates/default',$data);
                }
                if (isset($only_display_info) && $only_display_info == true){     
                    $data['main_content'] = 'slope';                                    // display the Slope view with no extra element (simply displaying info)
                    $this->load->view('templates/default',$data); 
                }
            }
            else { // There is no resort created
                $this->session->set_flashdata('error', 'no_resort');            // redirect to resort contoller with error message
               // redirect('resort_controller');
            }
            return $data;
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
          //  redirect('register_controller');
        }
    }
    
    
    protected function get_select_difficulty($id_difficulty, $name_language) {
        $difficulties = $this->item_model->get_difficulties($id_difficulty);
        $data = '';
        foreach ($difficulties->result() as $array_difficulties) {
            if ($array_difficulties->id_difficulty == $id_difficulty) {
                $data .= '<option value="'.$array_difficulties->id_difficulty.'" selected>'.$array_difficulties->$name_language.'</option>';
            }
            else
                $data .= '<option value="'.$array_difficulties->id_difficulty.'">'.$array_difficulties->$name_language.'</option>';
        }
        return $data;
    }
    
    
    /**
     * edit_name_mode               Temporary page in edit mode
     * @param type $id_sector       ID of the slope's sector
     * @param type $id_slope         Generic ID of the slope
     * @param type $currentResortID   Current resort ID
     */
    public function edit_name_mode($id_sector, $id_slope, $currentResortID){  
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        $data = $this->show_info_block_slope($id_slope, $currentResortID, 'slope_to_edit');    // Gets all the player slope info with "slope_to_edit" status (edit logo)
    }
    
    
    /**
     * edit_difficulty_mode               Temporary page in edit mode
     * @param type $id_sector       ID of the slope's sector
     * @param type $id_slope         Generic ID of the slope
     * @param type $currentResortID   Current resort ID
     */
    public function edit_difficulty_mode($id_sector, $id_slope, $currentResortID){  
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        $data = $this->show_info_block_slope($id_slope, $currentResortID, 'slope_to_edit_difficulty');    // Gets all the player slope info with "slope_to_edit_difficulty" status (edit logo)
    }
    
    
    /**
     * edit_name                        Edits the name of the slope
     * 
     * @param type $id_sector           ID of the slope's sector
     * @param type $id_created_lifts    ID of the created slope by the user
     * @param type $currentResortID       Current resort ID
     * @param type $id_lift             Generic ID of the slope
     */
    public function edit_name($id_sector, $id_created_slopes, $currentResortID, $id_slope){  
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('slope_choose_name', $this->lang->line('resort')['name_field'], 'trim|required|min_length[3]|max_length[35]');

        if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
            // We display the same form again, with errors
            $data = $this->edit_name_mode($id_sector, $id_slope, $currentResortID);
        }
        else {    // TRUE: all fields are correct
            // Change the name
            $editslopeName = $this->item_model->editItemName($currentResortID, $id_created_slopes, $this->input->post('slope_choose_name', TRUE), 'slope'); 
            if ($editslopeName == true) {
                $data = $this->show_info_block_slope($id_slope, $currentResortID, 'name_changed');    // Gets all the player slope info with "slope_to_edit" status  
            }
            else
                $data = $this->edit_name_mode($id_sector, $id_slope, $currentResortID);
        }
    }
    
    
    
    /**
     * edit_difficulty                        Edits the difficulty of the slope
     * 
     * @param type $id_sector           ID of the slope's sector
     * @param type $id_created_slopes    ID of the created slope by the user
     * @param type $currentResortID       Current resort ID
     * @param type $id_slope             Generic ID of the slope
     */
    public function edit_difficulty($id_sector, $id_created_slopes, $currentResortID, $id_slope){  
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('slope_choose_difficulty', $this->lang->line('resort')['difficulty_field'], 'trim|required');

        if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
            // We display the same form again, with errors
            $data = $this->edit_difficulty_mode($id_sector, $id_slope, $currentResortID);
        }
        else {    // TRUE: all fields are correct
            // Change the name
            $editslopeDifficulty = $this->item_model->editItemDifficulty($currentResortID, $id_created_slopes, $this->input->post('slope_choose_difficulty', TRUE), 'slope'); 
            if ($editslopeDifficulty == true) {
                $data = $this->show_info_block_slope($id_slope, $currentResortID, 'difficulty_changed');    // Gets all the player slope info with "difficulty_changed" status  
            }
            else
                $data = $this->edit_difficulty_mode($id_sector, $id_slope, $currentResortID);
        }
    }
   
   public function rush($id_slope){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $currentUserID = $this->users_model->get_user_id();      
        $currentResortID = $this->users_model->get_resort_id($currentUserID); 
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
            
        $is_built = $this->item_model->check_if_player_has_built_item($id_slope, $currentResortID, 'slope');     // check if slope is built
        $is_built_data = $is_built->row();  // we put the result in a array

        if ($is_built->num_rows() > 0) {    // if the item is existings
            $end_construction = $is_built_data->end_construction;
            $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
            $currenttime = time();                                          // current timestamp
            $time_left_value = $timestamp - $currenttime;                   // Time left in seconds

            if ($time_left_value > 0){ 
                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);

                if ($genepis_required_to_rush <= $genepis_available) {
                    $remove_genepis_cost = $this->users_model->remove_genepis_cost_DB($genepis_required_to_rush);
                    $data = $this->lang->line('home')['you_have_rushed'].' '.$is_built_data->custom_name.' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'];
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data) );   // Add a log row to the game_player_logs table

                    $complete_construction = $this->building_model->complete_construction_DB($is_built_data->id_created_slopes, 'game_created_slopes', 'id_created_slopes', 'end_construction');

                    $data = $this->show_info_block_slope($id_slope, $currentResortID, 'rush_completed'); 
                }
                else {  // Not enough genepis
                    $data = $this->show_info_block_slope($id_slope, $currentResortID, 'not_enough_genepis');
                }
            }
            else {  // No time left. Refresh page
                $data = $this->show_info_block_slope($id_slope, $currentResortID, 'already_completed');
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
     * custom_slope_form    Redirect stub — custom trail builder removed; use resort map instead.
     */
    public function custom_slope_form() {
        $this->session->set_flashdata('msg', '<div class="alert alert-info text-center">Custom trail drawing has been removed. Select a slope on the resort map to build it.</div>');
        redirect('resort_map_controller');
    }

    /**
     * build_custom_slope   Redirect stub — custom trail builder removed.
     */
    public function build_custom_slope() {
        redirect('resort_map_controller');
    }

}