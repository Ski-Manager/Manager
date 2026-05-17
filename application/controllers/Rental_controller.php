<?php
/**
 * 
 */
class Rental_controller extends CI_Controller{
    
    private $siteLang;  // To use the siteLang variable globally
    
    /**
     * __construct
     */
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');            // Store current language in variable
        } else {
            $siteLang = 'english';                                      // If no session, use English
            $this->session->set_userdata('site_lang', $siteLang);
        }
        // Loads the different language files
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        //$ci->lang->load('lift',$siteLang);
        //$ci->lang->load('slope',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('achievements_model');
        $this->load->model('logs_model');
    }
    
    /**
     * index    Main function with top of the page (title, page description...)
     * 
     * @param type $data
     */
    public function index($data = NULL){
        // Initialize a few variables
//LINE BELOW TO EDIT 
        $building_type = 'rental';
//LINE ABOVE TO EDIT 
        $data['title'] = '<h2>'.$this->lang->line('common_buildings')['titleMain'];
        $data['title'] .= ' - ';
        $data['title'] .= $this->lang->line($building_type)['title'].'</h2>'; 
        $data['introBuilding'] = '<div>'.$this->lang->line($building_type)['intro'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // Checks if the resort exists       
            if ($checkIfResortExists->num_rows() > 0) {                                        // if the player has a resort, OK
                // If toursit info center build, we can display the page
                $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  
                $building_ach_uncloked = $this->building_model->get_building_ach_unlocked2($currentUserID, 26);   // Checks if the player has uncloked the achievement for extra buildings (ID 26)
                $achievement_data = $this->achievements_model->get_specific_achievements_data(26);   // get generic achievement info
                
                $row_building_ach_uncloked = $building_ach_uncloked->row();
                $info_achievement_data = $achievement_data->row();
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $column_name = 'name_'.$player_preferred_lang;
                $ach_name = ($info_achievement_data !== null && isset($info_achievement_data->$column_name)) ? $info_achievement_data->$column_name : '';
                
                if ($tourist_info_data->num_rows() == 1) {          // Tourist info center is built
                    if ($building_ach_uncloked->num_rows() == 1) {  // achievement at least started (or completed)
                        $claimed = $row_building_ach_uncloked->claimed;
                        $progress = $row_building_ach_uncloked->progress;
                        if ($progress == NULL)  // Achievement not started
                            $progress = 0;
                        if ($progress == 100 && $claimed == 1) {
                            $data['hideBuilding'] = false;                  // To display specific blocks in the View (here we display the building)
                            $data1 = $this->standardBuildingBlock($building_type, $currentResortID);    // Calls the generic block funtion for the right building type
                            $data = array_merge($data,$data1);      // Merges all data to "data" for the view 
                        }
                        elseif ($progress != 100) { // Achievement started but not completed
                            $data['hideBuilding'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                            $data['infoMessage'] = 'achievement_locked';
                            $data['infoMessage_text'] = '<div class="alert alert-warning text-center">'.$this->lang->line('building')['the_achievement'].' "'.$ach_name.'" '.$this->lang->line('building')['ach_not_completed'].'<br>'.$this->lang->line('building')['current_progress_is'].' '.$progress.'%. '.$this->lang->line('building')['achievement_link_info'].'</div>';
                        }
                        elseif ($claimed != 1) {  // Achievement completed but not claimed
                            $data['hideBuilding'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                            $data['infoMessage'] = 'achievement_locked';
                            $data['infoMessage_text'] = '<div class="alert alert-warning text-center">'.$this->lang->line('building')['the_achievement'].' "'.$ach_name.'" '.$this->lang->line('building')['ach_not_claimed'].' '.$this->lang->line('building')['achievement_link_info'].'</div>';
                        }
                    }
                    else {  // achievement not even started
                        $data['infoMessage_text'] = '<div class="alert alert-warning text-center">'.$this->lang->line('building')['the_achievement'].' "'.$ach_name.'" '.$this->lang->line('building')['ach_not_completed'].' '.$this->lang->line('building')['achievement_link_info'].'</div>';
                        $data['hideBuilding'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                        $data['infoMessage'] = 'achievement_locked';  
                    }
                }
                // Tourist info not built. We inform player and show a link (make new function)
                else {
                    $data['hideBuilding'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                    $data['infoMessage'] = 'tourist_info_required';
                }
                // Displaying the building view
                $data['main_content'] = 'building';
                $this->load->view('templates/default',$data);   
            }
            else { // There is no resort created
                $this->session->set_flashdata('error', 'no_resort');            // redirect to resort contoller with error message
                redirect('resort_controller');
            } 
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    /**
     * standardBuildingBlock        Displays a standard building block. Can be used for any building except touriste info or access resort
     *          With standard buildings, several of them can be built, but only one at a time
     * 
     * @param type $building_type   Type of building (name: hotel, restaurant...)
     * @param type $currentResortID   Current resort ID
     * @return string               Returns the content of the page
     */
    public function standardBuildingBlock($building_type, $currentResortID){
        // Sets general variables
        $data['buildingLogo'] = '<img src="'.base_url('img/icons/'.$building_type.'.png').'" title="'.$this->lang->line($building_type)['title'].'"/>';
        $data['buildingDesc'] = ''.$this->lang->line($building_type)['desc'].'';
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns

        // For each of the three levels
        for ($i=1;$i<=3;$i++) {
            // Gets the generic data fpr the building type and level
            $building_data = $this->building_model->get_generic_building_data($building_type, $i);    // Type (3 = hotel, 4 = restaurants...) and "i" for each level ($i = 1, 2, 3)
            if ($building_data->num_rows() > 0) {                // the generic building exists in the DB (always!)
                $building_dataArray = $building_data->row();
                // initializes variables in case we don't do into the right loop
                $data['pre_building_time'][$i] = '';
                $data['post_building_time'][$i] = '';

                // counts how many buildings are built for this level (to show Quantity)
                $count_this_building_level = count_this_building_level($building_type, $i);   // returns an integer
                $data['buildingQuantity'][$i]= $count_this_building_level;                  // quantity for the current level
                
                // counts how many buildings under construction for this level (if >= 1, display time left)
                $count_this_building_level_under_construction = count_this_building_level($building_type, $i, '4'); // '4' means "under construction" status
                if ($count_this_building_level_under_construction == '1'){              // If the current level is under construction
                    //$end_construction_date_format = strtotime(get_time_left_for_building($currentResortID, $building_type, $i));   // return the date/time from the function. strtotime converts string to timestamp
                    $timestamp = strtotime(get_time_left_for_building($currentResortID, $building_type, $i)." UTC");   // return the number of seconds until the end
                    $currenttime = time();                                          // current timestamp
                    $time_left_value = $timestamp - $currenttime;                   // Time left in deconds
                    if ($time_left_value <= '0'){                                   // If there is no time left (building finished)
                        $data['wait_status'][$i] = true;   // If there is no time left, we define a new variable for the view
                        // Tooltip + link to refresh the page if time left = 0
                        $data['pre_building_time'][$i] = '<a href="'.base_url().'rental_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
                        $data['post_building_time'][$i] = '</div></a>';   // For toolpit (post)
                        $data['buildingTime'][$i] = $this->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
                    }
                    else    // If some time is left...
                        $data['buildingTime'][$i] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                }
                else {
                    $data['buildingTime'][$i] = display_friendly_time($building_dataArray->building_time/ACCELERATOR_FACTOR, '');      // Displays the friendly time for construction (hours, minutes...)
                }

                // count how many buildings of previous level - any status (to allow upgrade)
                $count_num_building_previous_level = count_this_building_level($building_type, $i-1);

                // counts how many buildings under construction for any level (used to disable button)
                $count_this_building_under_construction = count_this_building_level($building_type, '', '4'); // "4" = under construction status

                // parameters from generic building
                $data['buildingName'][$i] = $building_dataArray->$name_language;
                $data['buildingCost'][$i] = number_format($building_dataArray->building_cost, 0, ',', ' ');
                $data['buildingReputation'][$i] = number_format($building_dataArray->reputation, 0, ',', ' ');
                $default_max_income = $building_dataArray->max_income;
                $prestige_bonus = calculate_prestige_bonus($currentResortID);
                $real_max_income = $default_max_income * $prestige_bonus['coef'];
                $data['buildingMaxIncome'][$i] = number_format($real_max_income, 0, ',', ' '). ' € <div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['prestige_bonus'].'"><a href="'.base_url('tournaments_controller').'"><span class="bonus_value_green">(+'.$prestige_bonus['percentage'].'%)</span></div>';

                // START RUSH BUTTON UPGRADE/BUILD
                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                $button_level[$i] = '';
                if ( isset($time_left_value) && $time_left_value > 0 ) {
                    $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                    $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                    if ($genepis_required_to_rush <= $genepis_available) {
                        $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url($building_type.'_controller/rush/'.$building_type.'/'.$i).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                    }
                    else {
                        $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                    }   
                }
                // END RUSH BUTTON
                
                if ($count_this_building_under_construction == 0) { // If the building is not under construction nor upgrade
                    if ($i == '1'){         // for the first level, we allow the BUILD button
                        $data['buildingButton'][$i] = '<td><a href="'.base_url().'rental_controller/build_building/'.$currentResortID.'/'.$building_type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['build'].'</button></a></td>';
                    }
                    else if ($i > '1'){         // For the other levels
                      if ($count_num_building_previous_level == '0') {      // If the previous one is NOT built, we DISABLE upgrade
                            $data['buildingButton'][$i] = '<td><button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';  
                        }
                        else if ($count_num_building_previous_level >= '1') {       // If the previous one is built, we allow upgrade
                            $data['buildingButton'][$i] = '<td><a href="'.base_url().'rental_controller/upgrade_building/'.$currentResortID.'/'.$building_type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a></td>';  
                        }  
                    }
                }
                else {          // If the building is under construction or upgrade
                    if ($count_this_building_level_under_construction == '0') { // If the current level is NOT under construction/upgrade
                        if ($i == '1'){ // for the first level, we DISABLE "Build"
                            $data['buildingButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['build'].'</button></td>';
                        }
                        else if ($i > '1'){ // For the other levels, we DISABLE "Upgrade"
                            $data['buildingButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';  
                        }
                    }
                    else if ($count_this_building_level_under_construction > '0'){      // If the current level is under construction/upgrade
                        if ($i == '1'){     // We display BUILDING for first level
                            $data['buildingButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['building'].'</button>'.$button_level[$i].'</td>';
                        }
                        else if ($i > '1'){     // We display UPGRADING for the other levels
                            $data['buildingButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrading'].'</button>'.$button_level[$i].'</td>';  
                        }
                    }
                }
            }
        }
        return $data;
    }
    
 
    /**
     * build_building       Builds the first level of the building, or a new one (depending of the type and level)
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $building_type            Type of the building (hotel = 3, restaurant = 4...)
     * @param type $level           Level to build, usually 1 for first time
     */
    public function build_building($currentResortID, $building_type, $level = '1'){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
                    
        // Gets the id_building and generic info
        $building_generic_info_data = $this->building_model->get_generic_building_data($building_type, $level);
        if ($building_generic_info_data->num_rows() > 0) {                // the generic building exists in the DB (always!)
            $building_generic_info_dataArray = $building_generic_info_data->row();
            $id_building = $building_generic_info_dataArray->id_building;
        }
        
        // counts how many buildings under construction for any level (to prevent building)
        $count_this_building_under_construction = count_this_building_level($building_type, '', '4');
        
        // Detects if the page was refreshed and prevents multiple entries
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if(!$pageWasRefreshed ) {
            if ($count_this_building_under_construction == 0) {                         // If not under construction for this building, we are allowed to building more
                $cost_building = $building_generic_info_dataArray->building_cost;        // cost of the building
                $gain_reputation = $building_generic_info_dataArray->reputation;    // reputation to gain
                $cash_player = $this->users_model->get_cash_player();                     // Get how much cash the player has (in dollar)
                $money_after_payment = $cash_player - $cost_building;                    // we calculate how much the player will have left after the payment
                if ($money_after_payment >= 0) {                                         // If enough cash
                    if ($removeCashQuery = $this->users_model->pay_item($cost_building, $cash_player)){      //the paiment for the building has been taken
                        $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                        $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                        // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                        $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                        $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                    }
                    $add_reputation = $this->users_model->add_reputation($gain_reputation);
                    $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                    $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                    // We prepare the building data
                    $end_construction = calculate_end_construction($building_type, $level);  // calculate the end of the construction
                    $data_insert = array (
                        'id_resort' => $currentResortID,
                        'id_building' => $id_building,       // ID of the building in the game_buildings table
                        'type' => $building_type,                    //  ID type of the building
                        'level' => '1',                     // first level for construction
                        'end_construction' => $end_construction,
                        'id_status' => '4'                  // under construction status
                    );
                    $build_building = $this->building_model->build_building_db($data_insert);   // DB request
                    if ($build_building) {
                        $data['infoMessage'] = 'building_built';
                        $this->session->set_flashdata('update_token', time());
                        $currentUserID = $this->users_model->get_user_id();
                        $data_achievement = array (
                            'id_sector' => '*',
                            'id_resort' => $currentResortID,
                            'id_building' => $id_building,       // ID of the building in the game_buildings table
                            'type' => $building_type,       // 1 id the ID of the tourist info type
                            'level' => '1',             // only one level for tourist info
                        );
                        $call_achievements_check = call_achievements_check($data_achievement, 'build');
                        $call_achievements_check = call_achievements_check($data_ach = array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building), 'build_amount');   // Check spending achievements 
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'].$this->lang->line($building_type)['title_sing'].$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'].$this->lang->line($building_type)['title_sing'].$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                    }
                    else {
                        $data['infoMessage'] = 'building_not_built';
                    }
                }
                else {
                    $data['infoMessage'] = 'not_enough_money';
                }
            }
            else {      // Already ongoing construction (display info message)
                $data['infoMessage'] = 'building_one_at_a_time';
            }
        }
        else {  // This is in case we reload the page. No need to display anything but "data" needs to exist
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }
    
    /**
     * upgrade_building     Upgrades the building level
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $building_type            Type of the building (hotel = 3, restaurant = 4...)
     * @param type $level           Level to build, usually 1 for first time
     */
    public function upgrade_building($currentResortID, $building_type, $level){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // Gets the id_building and generic info
        $building_generic_info_data = $this->building_model->get_generic_building_data($building_type, $level);     
        if ($building_generic_info_data->num_rows() > 0) {                // the generic building exists in the DB (always!)
            $building_generic_info_dataArray = $building_generic_info_data->row();
            $id_building = $building_generic_info_dataArray->id_building;
        }
        
        // counts how many buildings under construction for any level (to prevent upgrade)
        $count_this_building_under_construction = count_this_building_level($building_type, '', '4');
        
        // Checks if the player has already built the same building but previous level (to allow upgrade of current level)
        $count_num_building_previous_level = count_this_building_level($building_type, $level-1);
        
        // Detects if the page was refreshed and prevents multiple entries
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if(!$pageWasRefreshed ) {
            // Only if previous level is already built
            if ($count_num_building_previous_level >= '1') {
                if ($count_this_building_under_construction == 0) {                     // If not under construction for this building, we are allowed to upgrade
                    $cost_building = $building_generic_info_dataArray->building_cost;    // cost of the building
                    $gain_reputation = $building_generic_info_dataArray->reputation;    // reputation to gain
                    $cash_player = $this->users_model->get_cash_player();                // Get how much cash the player has (in dollar)
                    $money_after_payment = $cash_player - $cost_building;               // we calculate how much the player will have left after the payment
                    if ($money_after_payment >= 0) {                                    // If enough cash
                        if ($removeCashQuery = $this->users_model->pay_item($cost_building, $cash_player)){      //the paiment for the building has been taken
                            $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                            $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                            // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                        }
                        $add_reputation = $this->users_model->add_reputation($gain_reputation);
                        $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                        $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                        // We prepare the building data
                        $end_construction = calculate_end_construction($building_type, $level);
                        $data_upg = array (
                            'level' => $level,                          // we update with the new level
                            'end_construction' => $end_construction,
                            'id_status' => '4'                          // under construction
                        );
                        $build_building = $this->building_model->update_building_db($currentResortID, $building_type, $level-1 , $data_upg);    // Build request in the DB
                            
                        if ($build_building) {
                            $data['infoMessage'] = 'building_upgraded';
                            $currentUserID = $this->users_model->get_user_id();
                            $data_ach = array (
                                'id_resort' => $currentResortID,  
                                'id_building' => $id_building,       // ID of the building in the game_buildings table
                                'level' => $level,             // only one level for tourist info
                                'type' => $building_type,
                            );
                            $call_achievements_check = call_achievements_check($data_ach, 'upgrade');   // Builds the building in the DB
                            $call_achievements_check = call_achievements_check($data_ach2 = array('id_resort' => $currentResortID, 'quantity' => $cost_building), 'upgrade_amount');   // Check spending achievements
                            $call_achievements_check = call_achievements_check($data_ach_build = array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building), 'build_amount');   // Check spending achievements
                            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$this->lang->line($building_type)['title_sing'].' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$this->lang->line($building_type)['title_sing'].' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                        }
                        else {
                            $data['infoMessage'] = 'building_not_upgraded';
                        }
                    }
                    else {
                        $data['infoMessage'] = 'not_enough_money';
                    }
                }
                else {      // If there is already a construction ongoing (display info message)
                    $data['infoMessage'] = 'building_one_at_a_time';
                }
            }
            else {      // Need to build previous level first (display info message)
                $data['infoMessage'] = 'building_not_built_previous';
            }
        }
        else {  // This is in case we reload the page. No need to display anything but "data" needs to exist
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }
   
    public function rush($building_type, $level){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $currentUserID = $this->users_model->get_user_id();      
        $currentResortID = $this->users_model->get_resort_id($currentUserID); 
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // Detects if the page was refreshed and prevents multiple entries
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if(!$pageWasRefreshed ) {
            // counts how many buildings under construction for any level (to prevent upgrade)
            $building_data = $this->building_model->get_time_left_for_building_db($currentResortID, $building_type, $level);
            $building_data_array = $building_data->row();
            
            $end_construction = $building_data_array->end_construction;
            $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
            $currenttime = time();                                          // current timestamp
            $time_left_value = $timestamp - $currenttime;                   // Time left in seconds

            if ($time_left_value > 0){ 
                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);

                if ($genepis_required_to_rush <= $genepis_available) {
                    $remove_genepis_cost = $this->users_model->remove_genepis_cost_DB($genepis_required_to_rush);
                    $data_log = $this->lang->line('home')['you_have_rushed'].' '.$this->lang->line($building_type)['title_sing'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'];
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log) );   // Add a log row to the game_player_logs table

                    $complete_construction = $this->building_model->complete_construction_DB($building_data_array->id_created_buildings, 'game_created_buildings', 'id_created_buildings', 'end_construction');

                    $data['infoMessage'] = 'rush_completed';
                }
                else {  // Not enough genepis
                    $data['infoMessage'] = 'not_enough_genepis';
                }
            }
            else {  // No time left. Refresh page
                $data['infoMessage'] = 'already_completed';
            }
        }
        else{    // page refreshed
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }
    
}