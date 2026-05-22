<?php
/**
 * 
 */
class Building_access_controller extends CI_Controller{
    
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
        $this->load->model('season_pass_model');
    }
    
    /**
     * index    Main function with top of the page (title, page description...)
     */
    public function index($data = NULL){
        // Initialize empty variables
        $data['title'] = '<h2>'.$this->lang->line('common_buildings')['titleMain'];
        $data['title'] .= ' - ';
        $data['title'] .= $this->lang->line('access_resort')['title'].'</h2>'; 
        $data['introBuildingAccess'] = '<div>'.$this->lang->line('access_resort')['intro'].'</div>';
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $data['currentResortID'] = $currentResortID;
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // Checks if the resort exists       
            if ($checkIfResortExists->num_rows() > 0) {                                        // if the player has a resort, OK

                $data1 = $this->touristInfoBlock();
                // If toursit info center build, we display the other buildings
                $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  

                if ($tourist_info_data->num_rows() == 1) {           // Tourist info center is built
                    $data['hideAllBuildings'] = false;
                    $data2 = $this->accessResortBlock(); 
                    $data3 = $this->parkingBlock(); 
                    // We merge all functions
                    $data = array_merge($data,$data1,$data2, $data3);
                }
                else {
                    $data['hideAllBuildings'] = true;
                    $data = array_merge($data,$data1);
                }

                $data['main_content'] = 'season_pass_access';

                // Season pass data for the combined view
                $sp_flash = $this->session->flashdata('infoMessage');
                $show_season_tab = $this->session->flashdata('show_season_tab');
                $sp_settings = $this->season_pass_model->get_settings_DB($currentResortID);
                $data['enabled']                 = (int)$sp_settings->enabled;
                $data['season_pass_price']        = (int)$sp_settings->season_pass_price;
                $data['passes_sold']              = (int)$sp_settings->passes_sold;
                $data['current_season']           = (int)$sp_settings->current_season;
                $data['early_bird_enabled']       = (int)$sp_settings->early_bird_enabled;
                $data['early_bird_discount_pct']  = (int)$sp_settings->early_bird_discount_pct;
                $data['min_price']                = SEASON_PASS_MIN_PRICE;
                $data['max_price']                = SEASON_PASS_MAX_PRICE;
                $data['season_length']            = SEASON_PASS_SEASON_LENGTH;
                $data['loyalty_threshold']        = SEASON_PASS_HIGH_SALES_THRESHOLD;
                $data['loyalty_rep_bonus']        = SEASON_PASS_LOYALTY_REP_BONUS;
                $data['early_bird_min_discount']  = SEASON_PASS_EARLY_BIRD_MIN_DISCOUNT;
                $data['early_bird_max_discount']  = SEASON_PASS_EARLY_BIRD_MAX_DISCOUNT;
                $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
                $reputation  = $resort_info ? (int)$resort_info->reputation : 0;
                $data['estimated_passes'] = $this->season_pass_model->calculate_passes_sold($reputation, $data['season_pass_price'], (bool)$data['early_bird_enabled']);
                $effective_price = $data['early_bird_enabled']
                    ? (int)round($data['season_pass_price'] * (1 - $data['early_bird_discount_pct'] / 100))
                    : $data['season_pass_price'];
                $data['estimated_daily_revenue'] = (int)floor($data['estimated_passes'] * $effective_price / SEASON_PASS_SEASON_LENGTH);
                if ($sp_flash) {
                    $data['sp_infoMessage'] = $sp_flash;
                } elseif ($show_season_tab) {
                    // Navigated here from the Season Ski Passes navbar link: activate that tab
                    $data['sp_active_tab'] = 'season_pass';
                }

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
     * touristInfoBlock     Displays the first block for the tourist information center
     * 
     * @return type     Array containing all the information to push to the view
     */
    public function touristInfoBlock(){
        
        $data['touristInfoTitle'] = '<h2>'.$this->lang->line('tourist_info')['title'].'</h2>';
        $data['touristInfoLogo'] = '<img src="'.base_url('img/icons/tourist_info.png').'" title="'.$this->lang->line('tourist_info')['title'].'"/>';
        $data['touristInfoDesc'] = ''.$this->lang->line('tourist_info')['desc'].'';
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  
            if ($tourist_info_data->num_rows() == 0) {           // Tourist info center not built, we display the build form
                $data1 = $this->displayTouristInfoNotBuiltBlock();
            }
            else {  // Tourist info center is built, Display open/close button
                $data1 = $this->displayTouristInfoBuiltBlock($currentResortID);
            }
        $data = array_merge($data1,$data);      // We need to merge all the data previously built into a single array
        return $data;
    }
    
    /**
     * displayTouristInfoNotBuiltBlock      Set the variables to push to the view only when the Toursit info center is NOT built
     * 
     * @return type     Array containing all the information that will be merge with eventual other functions
     */
    public function displayTouristInfoNotBuiltBlock(){
        $data['displayTouristInfoNotBuiltBlock'] = true;
        // Get the generic tourist information data
        $tourist_info_data = $this->building_model->get_generic_building_data('tourist_info', '1');      // "tourist_info" is the type of the tourist info building. and "1" is the level of the building
        if ($tourist_info_data->num_rows() > 0) {                                        // the generic tourist info center exists in the DB (always!)
            $tourist_info_dataArray = $tourist_info_data->row();                                
            $data['touristInfoBuildingCost'] = number_format($tourist_info_dataArray->building_cost, 0, ',', ' ');
            $data['touristInfoReputation'] = number_format($tourist_info_dataArray->reputation, 0, ',', ' ');
            $data['touristInfoMaxIncome'] = number_format($tourist_info_dataArray->max_income, 0, ',', ' ');
            $time = display_friendly_time($tourist_info_dataArray->building_time/ACCELERATOR_FACTOR);      // displays friendly time (hours, minutes...)
            $data['touristInfoBuildingTime'] = $time;   
        }
        return $data;
    }
    
    /**
     * displayTouristInfoBuiltBlock      Set the variables to push to the view only when the Toursit info center is built
     * 
     * @return type     Array containing all the information that will be merge with eventual other functions
     */
    public function displayTouristInfoBuiltBlock($currentResortID){
        $data['displayTouristInfoNotBuiltBlock'] = false;
        // Get the generic tourist information data
        $tourist_info_data = $this->building_model->get_building_data_for_player($currentResortID, 'tourist_info', '1');      // "tourist_info" is the type of the tourist info building. and "1" is the level of the building
        if ($tourist_info_data->num_rows() > 0) {                                        // the generic tourist info center exists in the DB (always!)
            $tourist_info_dataArray = $tourist_info_data->row();
            $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
            //if ($resultResort->num_rows() > 0) {        // the user has built a resort
                $resultResortArray = $resultResort->row();
            //}
                
            $resort_status_block = get_resort_status_block($currentResortID);
            $friendly_status = $resort_status_block['friendly_status'];
            if ($friendly_status == 'open'){ 
                $class = '<span class="alert-success">';
            }
            else { 
                $class = '<span class="red_text">';
            }
            $data['touristInfoBuildingStatus_real'] = $class.$this->lang->line('home')['building_status_to_show_'.$friendly_status.''].'</span>';
            
            
            $data['opposite_status'] = $resort_status_block['opposite_status'];
            $data['status_text_button'] = $resort_status_block['status_text_button'];
            $data['tourist_info_status_to_show'] =  $resort_status_block['tourist_info_status_to_show']; 
            if (isset($resort_status_block['pre_touristInfoBuildingStatus']))
                $data['pre_touristInfoBuildingStatus'] = $resort_status_block['pre_touristInfoBuildingStatus'];
            if (isset($resort_status_block['post_touristInfoBuildingStatus']))
                $data['post_touristInfoBuildingStatus'] = $resort_status_block['post_touristInfoBuildingStatus'];
            $data['touristInfoBuildingStatus'] = $resort_status_block['touristInfoBuildingStatus'];
            $data['touristInfoStatusLabel'] = $resort_status_block['touristInfoStatusLabel'];
            $data['friendly_status'] = $friendly_status;
            
           // $data['pre_touristInfoBuildingStatus'] = '<a href="'.base_url().'building_access_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
            //$data['post_touristInfoBuildingStatus'] = '</div></a>';   // For toolpit (post)
           // $data['touristInfoBuildingStatus'] = $this->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
            
            
            
                
            // Calculating ski pass prices
            $skipassDaily = $resultResortArray->skipass_daily;
            $skipassWeekly = $resultResortArray->skipass_weekly;
            // The One Day dropdown
            $data['selectArrayOneDay'] = '<option value="10"'.($skipassDaily == 10 ? ' selected' : '').'>10</option>';
            for ($i=11;$i<=100;$i++) {
                if ($i==$skipassDaily) {
                    $data['selectArrayOneDay'] .= '<option value="'.$i.'" selected>'.$i.'</option>';
                }
                else
                    $data['selectArrayOneDay'] .= '<option value="'.$i.'">'.$i.'</option>';
            }
            
            
            
            // The One Week dropdown
            $data['selectArrayOneWeek'] = '<option value="70"'.($skipassWeekly == 70 ? ' selected' : '').'>70</option>';
            for ($i=80; $i<=700; $i+= 10) {
                if ($i==$skipassWeekly) {
                    $data['selectArrayOneWeek'] .= '<option value="'.$i.'" selected>'.$i.'</option>';
                }
                else
                    $data['selectArrayOneWeek'] .= '<option value="'.$i.'">'.$i.'</option>';
            }
            
            $prestige_bonus = calculate_prestige_bonus($currentResortID);
            $skipassDaily_with_prestige_bonus = $skipassDaily * $prestige_bonus['coef'];
            $skipassWeekly_with_prestige_bonus = $skipassWeekly * $prestige_bonus['coef'];
            $data['prestige_bonus_daily_text'] = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['prestige_bonus'].'"><a href="'.base_url('tournaments_controller').'"><div class="center">'.$this->lang->line('access')['thanks_prestige_bonus'].' '.$prestige_bonus['percentage'].'% '.$this->lang->line('access')['after_purchasing_skipass'].' '.number_format($skipassDaily_with_prestige_bonus, 1, ',', ' '). ' € '.$this->lang->line('access')['for_the_daily_skipass'].' '.number_format($skipassWeekly_with_prestige_bonus, 1, ',', ' ').' € '.$this->lang->line('access')['for_the_weekly_skipass'].'</div></a></div>';
            // Pass current prices for the summary cards
            $data['skipassDailyValue'] = number_format($skipassDaily, 0, ',', ' ');
            $data['skipassWeeklyValue'] = number_format($skipassWeekly, 0, ',', ' ');
            $data['skipassDailyEffective'] = number_format($skipassDaily_with_prestige_bonus, 1, ',', ' ');
            $data['skipassWeeklyEffective'] = number_format($skipassWeekly_with_prestige_bonus, 1, ',', ' ');
            $data['prestigePercentage'] = $prestige_bonus['percentage'];
            $data['friendly_status_for_badge'] = $friendly_status;

            // Dynamic pricing values
            $data['vip_pass_price']      = isset($resultResortArray->vip_pass_price)      ? (int)$resultResortArray->vip_pass_price      : 0;
            $data['family_discount_pct'] = isset($resultResortArray->family_discount_pct) ? (int)$resultResortArray->family_discount_pct : 0;
            $data['group_discount_pct']  = isset($resultResortArray->group_discount_pct)  ? (int)$resultResortArray->group_discount_pct  : 0;
            $data['max_vip_pass_price']      = MAX_VIP_PASS_PRICE;
            $data['max_family_discount_pct'] = MAX_FAMILY_DISCOUNT_PCT;
            $data['max_group_discount_pct']  = MAX_GROUP_DISCOUNT_PCT;
            
        }
        return $data;
    }
    
    /**
     * accessResortBlock     Displays the first block for the tourist information center
     * 
     * @return type         Array containing all the information that will be merge with eventual other functions
     */
    public function accessResortBlock(){
        $data['accessResortTitle'] = '<h2>'.$this->lang->line('access')['accessResortTitle'].'</h2>';
        $data['accessResortLogo'] = '<i class="fa-solid fa-signs-post" title="'.$this->lang->line('access')['accessResortTitle'].'"></i>';
        $data['accessResortDesc'] = ''.$this->lang->line('access')['accessResortDesc'].'';
        $name_language = 'name_'.$this->session->userdata('site_lang');
        $type = 'access';        // So far the type for Access is "2" in the DB
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        
        // If toursit info center built, we continue
        $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  
        if ($tourist_info_data->num_rows() == 1) {           // Tourist info center is built
            // Check the maximum level for this building (already updated)
            $max_building_level_for_player = $this->building_model->get_max_building_level_for_player($currentResortID, $type);
            if ($max_building_level_for_player->num_rows() > 0) {   // If it is built
                $max_building_level_for_playerArray = $max_building_level_for_player->row();
                $max_level = $max_building_level_for_playerArray->level;
                $id_status = $max_building_level_for_playerArray->id_status;
                $end_construction = $max_building_level_for_playerArray->end_construction;
            }
            else    // not built
                $max_level = '0';
            for ($i=1;$i<=3;$i++) {
                $accessResort_data = $this->building_model->get_generic_building_data($type, $i);    // Type (='access') abd for each level ($i = 1, 2, 3)
                $count_building_previous_level_under_construction = count_this_building_level($type, $i-1, '4', 'none', 'none');
                $count_building_previous_level_total = count_this_building_level($type, $i-1, '', 'none', 'both');

                if ($accessResort_data->num_rows() > 0) {                // the generic accessResort building exists in the DB (always!)
                    $accessResort_dataArray = $accessResort_data->row();         
                    // counts how many buildings under construction for this level (if >= 1, display time left)
                    $count_this_building_level_under_construction = count_this_building_level($type, $i, '4'); // '4' means "under construction" status
                    if ($count_this_building_level_under_construction == '1'){              // If the current level is under construction
                        //$end_construction_date_format = strtotime(get_time_left_for_building($currentResortID, $type, $i)." UTC");   // return the date/time from the function. strtotime converts string to timestamp
                        $timestamp = strtotime(get_time_left_for_building($currentResortID, $type, $i)." UTC");   // return the number of seconds until the end
                        $currenttime = time();                                          // current timestamp
                        $time_left_value = $timestamp - $currenttime;                   // Time left in seconds
                        if ($time_left_value <= '0'){                                   // If there is no time left (building finished)
                            $data['pre_accessResortBuildingTime'][$i] = '<a href="'.base_url().'building_access_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
                            $data['post_accessResortBuildingTime'][$i] = '</div></a>';   // For toolpit (post)
                            $data['accessResortBuildingTime'][$i] = $this->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
                        }
                        else    // If some time is left...
                            $data['accessResortBuildingTime'][$i] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                    }
                    else {
                        $data['accessResortBuildingTime'][$i] = display_friendly_time($accessResort_dataArray->building_time/ACCELERATOR_FACTOR, '');      // Displays the friendly time for construction (hours, minutes...)
                    }    
                    $data['accessResortInfrastructureName'][$i] = $accessResort_dataArray->$name_language;
                    $data['accessResortBuildingCost'][$i] = number_format($accessResort_dataArray->building_cost, 0, ',', ' ');
                    $data['accessResortReputation'][$i] = number_format($accessResort_dataArray->reputation, 0, ',', ' ');
                    $data['accessResortMaxBonusAffluence'][$i] = number_format($accessResort_dataArray->max_income, 0, ',', ' ');

                    // START RUSH BUTTON UPGRADE/BUILD
                    $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                    $button_level[$i] = '';
                    if ( isset($time_left_value) && $time_left_value > 0 ) {
                        $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                        $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                        if ($genepis_required_to_rush <= $genepis_available) {
                            $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url('building_access_controller/rush/access/'.$i).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                        }
                        else {
                            $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                        }   
                    }
                    // END RUSH BUTTON
                    
                    // If the building is built (at least level one) or under construction
                    if ($max_level >= '1'){
                        // 1st button = BUILT if construction level 1 finished (not building anymore) OR if higher than level one (any status)
                        if ($i == '1' && (($max_level >= $i && $id_status != '4') || ($max_level > $i))) {
                            $data['accessResortButton'][$i] = '<td><button class="btn btn-success disabled">'.$this->lang->line('home')['status_built'].'</button></td>';
                        }
                        // UPGRADING status if level 2 or 3, and upgrading current level
                        else if ($max_level == $i && $id_status == '4' && $i > '1') {
                            $data['accessResortButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrading'].'</button>'.$button_level[$i].'</td>';
                        }
                        // UPGRADED status if level is higher and processing buttons 2 or 3
                        else if ($max_level >= $i && $i > '1') {
                            $data['accessResortButton'][$i] = '<td> <button class="btn btn-success disabled">'.$this->lang->line('building')['upgraded'].'</button></td>';
                        }
                        // BUILDING status if level is 1 and status is building
                        else if ($max_level == $i && $id_status == '4') { 
                            $data['accessResortButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['building'].'</button>'.$button_level[$i].'</td>';
                        }
                        // UPGRADE button active if not building and level is below currently processed
                        else if ($max_level < $i && $id_status != '4' && $count_building_previous_level_total == '1' && $count_building_previous_level_under_construction == '0') {
                            $data['accessResortButton'][$i] = '<td><a href="'.base_url().'building_access_controller/upgrade_building/'.$currentResortID.'/'.$type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a></td>';
                        }
                        // UPGRADE button disabled if processed level is higher than current one (not accessible yet)
                        else if ($max_level < $i) {
                            $data['accessResortButton'][$i] = '<td><button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';
                        }
                    }
                    // If the building is not built (not even started)
                    else if ($max_level == '0'){
                        // BUILD button active
                        if ($i == '1') {
                            $data['accessResortButton'][$i] = '<td><a href="'.base_url().'building_access_controller/build_building/'.$currentResortID.'/'.$type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['build'].'</button></a></td>';
                        }
                        // UPGRADE buttons disabled (not accessible yet)
                        else {
                            $data['accessResortButton'][$i] = '<td><button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';
                        }
                    }
                }
            }
            $data['accessResortDesc'] .= '<br><b>'.$this->lang->line('building')['current_level'].' '.$max_level.'</b>';
            $data['accessResortMaxLevel'] = $max_level;
            if ($max_level > '0')
                $data['accessResortCurrentBonusAffluence'] = $data['accessResortMaxBonusAffluence'][$max_level];
            else
                $data['accessResortCurrentBonusAffluence'] = '0';
        }
        return $data;
    }
    
    
    
    /**
     * parkingBlock     Displays the third block for the parkings
     * 
     * @return type         Array containing all the information that will be merge with eventual other functions
     */
    public function parkingBlock(){
        $data['parkingTitle'] = '<h2>'.$this->lang->line('parking')['parkingTitle'].'</h2>';
        $data['parkingLogo'] = '<i class="fa-solid fa-square-parking" title="'.$this->lang->line('parking')['parkingTitle'].'"></i>';
        $data['parkingDesc'] = ''.$this->lang->line('parking')['parkingDesc'].'';
        $name_language = 'name_'.$this->session->userdata('site_lang');
        $type = 'parking';        // So far the type for Parking is "8" in the DB
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        // If toursit info center built, we continue
        $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  
        if ($tourist_info_data->num_rows() == 1) {           // Tourist info center is built
            // Check the maximum level for this building (already updated)
            $max_building_level_for_player = $this->building_model->get_max_building_level_for_player($currentResortID, $type);
            if ($max_building_level_for_player->num_rows() > 0) {   // If it is built
                $max_building_level_for_playerArray = $max_building_level_for_player->row();
                $max_level = $max_building_level_for_playerArray->level;
                $id_status = $max_building_level_for_playerArray->id_status;
                $end_construction = $max_building_level_for_playerArray->end_construction;
            }
            else    // not built
                $max_level = '0';
            for ($i=1;$i<=3;$i++) {
                $parking_data = $this->building_model->get_generic_building_data($type, $i);    // Type = 2 (='access') abd for each level ($i = 1, 2, 3)
                $count_building_previous_level_under_construction = count_this_building_level($type, $i-1, '4', 'none', 'none');
                $count_building_previous_level_total = count_this_building_level($type, $i-1, '', 'none', 'both');

                if ($parking_data->num_rows() > 0) {                // the generic parking building exists in the DB (always!)
                    $parking_dataArray = $parking_data->row();         
                    // counts how many buildings under construction for this level (if >= 1, display time left)
                    $count_this_building_level_under_construction = count_this_building_level($type, $i, '4'); // '4' means "under construction" status
                    if ($count_this_building_level_under_construction == '1'){              // If the current level is under construction
                        //$end_construction_date_format = strtotime(get_time_left_for_building($currentResortID, $type, $i)." UTC");   // return the date/time from the function. strtotime converts string to timestamp
                        $timestamp = strtotime(get_time_left_for_building($currentResortID, $type, $i)." UTC");   // return the number of seconds until the end
                        $currenttime = time();                                          // current timestamp
                        $time_left_value = $timestamp - $currenttime;                   // Time left in deconds
                        if ($time_left_value <= '0'){                                   // If there is no time left (building finished)
                            $data['pre_parkingBuildingTime'][$i] = '<a href="'.base_url().'building_access_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
                            $data['post_parkingBuildingTime'][$i] = '</div></a>';   // For toolpit (post)
                            $data['parkingBuildingTime'][$i] = $this->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
                        }
                        else {   // If some time is left...
                            $data['parkingBuildingTime'][$i] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                        }
                    }
                    else {
                        $data['parkingBuildingTime'][$i] = display_friendly_time($parking_dataArray->building_time/ACCELERATOR_FACTOR, '');      // Displays the friendly time for construction (hours, minutes...)
                    }    
                    $data['parkingInfrastructureName'][$i] = $parking_dataArray->$name_language;
                    $data['parkingBuildingCost'][$i] = number_format($parking_dataArray->building_cost, 0, ',', ' ');
                    $data['parkingReputation'][$i] = number_format($parking_dataArray->reputation, 0, ',', ' ');
                    $data['parkingMaxIncome'][$i] = number_format($parking_dataArray->max_income, 0, ',', ' ');

                    
                    // START RUSH BUTTON UPGRADE/BUILD
                    $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                    $button_level[$i] = '';
                    if ( isset($time_left_value) && $time_left_value > 0 ) {
                        $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                        $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                        if ($genepis_required_to_rush <= $genepis_available) {
                            $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url('building_access_controller/rush/parking/'.$i).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                        }
                        else {
                            $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                        }   
                    }
                    // END RUSH BUTTON
                    
                    // Checks if there is any building under construction
                    $count_building_under_construction = count_this_building_level($type, '', '4', 'both', 'none');
            
                    // If the building is built (at least level one) or under construction
                    if ($max_level >= '1'){
                        // 1st button = BUILT if construction level1 finished (not building anymore) OR if higher than level one (any status)
                        if ($i == '1' && (($max_level >= $i && $id_status != '4') || ($max_level > $i))) {
                            $data['parkingButton'][$i] = '<td><button class="btn btn-success disabled">'.$this->lang->line('home')['status_built'].'</button></td>';
                        }
                        // UPGRADING status if level 2 or 3, and upgrading current level
                        else if ($max_level == $i && $id_status == '4' && $i > '1') {
                            $data['parkingButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrading'].'</button>'.$button_level[$i].'</td>';
                        }
                        // UPGRADED status if level is higher and processing buttons 2 or 3
                        else if ($max_level >= $i && $i > '1') {
                            $data['parkingButton'][$i] = '<td> <button class="btn btn-success disabled">'.$this->lang->line('building')['upgraded'].'</button></td>';
                        }
                        // BUILDING status if level is 1 and status is building
                        else if ($max_level == $i && $id_status == '4') { 
                            $data['parkingButton'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['building'].'</button>'.$button_level[$i].'</td>';
                        }
                        // UPGRADE button active if not building and level is below currently processed
                        else if ($max_level < $i && $id_status != '4' && $count_building_previous_level_total == '1' && $count_building_previous_level_under_construction == '0') {
                            $data['parkingButton'][$i] = '<td><a href="'.base_url().'building_access_controller/upgrade_building/'.$currentResortID.'/'.$type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a></td>';
                        }
                        // UPGRADE button disabled if processed level is higher than current one (not accessible yet)
                        else if ($max_level < $i) {
                            $data['parkingButton'][$i] = '<td><button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';
                        }
                    }
                    // If the building is not built (not even started)
                    else if ($max_level == '0'){
                        // BUILD button active
                        if ($i == '1') {
                            $data['parkingButton'][$i] = '<td><a href="'.base_url().'building_access_controller/build_building/'.$currentResortID.'/'.$type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['build'].'</button></a></td>';
                        }
                        // UPGRADE buttons disabled (not accessible yet)
                        else {
                            $data['parkingButton'][$i] = '<td><button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';
                        }
                    }
                }
            }
            $data['parkingDesc'] .= '<br><b>'.$this->lang->line('building')['current_level'].' '.$max_level.'</b>';
            $data['parkingMaxLevel'] = $max_level;
            if ($max_level > '0')
                $data['parkingCurrentMaxIncome'] = $data['parkingMaxIncome'][$max_level];
            else
                $data['parkingCurrentMaxIncome'] = '0';

            // Parking fee values
            $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
            $data['parking_fee']     = isset($resort_info->parking_fee) ? (int)$resort_info->parking_fee : DEFAULT_PARKING_FEE;
            $data['min_parking_fee'] = MIN_PARKING_FEE;
            $data['max_parking_fee'] = MAX_PARKING_FEE;
        }
        return $data;
    }

    /**
     * build_building Prepare the construction of the building (depending of the type and level)
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type            Type of the building (a tourist info, 2 access....)
     * @param type $level           Level to build, usually 1 for first time
     */
    public function build_building($currentResortID, $type, $level = '1'){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // If tourist info center built, we continue
        $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center 
        if ($tourist_info_data->num_rows() == 1 || $type == 'tourist_info') {           // Tourist info center is built
            if ($type == 'tourist_info') {
                $access_to_build = true;
            }
            else if ($type != 'tourist_info' && $tourist_info_data->num_rows() == 1) {
                $tourist_info_dataArray = $tourist_info_data->row();
                if ($tourist_info_dataArray->id_status != '4')
                    $access_to_build = true;
                else
                    $access_to_build = false;
            }
            else if ($type != 'tourist_info' && $tourist_info_data->num_rows() == 0) {
                $access_to_build = false;
            }
            
            if ($access_to_build == true){     // If not under construction
                if ($level == '1') {
                    // Gets the id_building
                    $building_generic_info_data = $this->building_model->get_generic_building_data($type, $level);      // get the generic info (we need the id_building here)
                    if ($building_generic_info_data->num_rows() > 0) {                // the generic accessResort building exists in the DB (always!)
                        $building_generic_info_dataArray = $building_generic_info_data->row();
                        $id_building = $building_generic_info_dataArray->id_building;
                    }

                    // Checks if the player has already built the building ID 
                    $count_num_building = count_this_building_level($type, '', '', 'both', 'both');
                    if ($type == 'access')
                        $other_type = 'parking';
                    else if ($type == 'parking')
                        $other_type = 'access';
                    else
                        $other_type = 'tourist_info';
                    // Check if the other type of building is already under contruction (access type if current type is parking...)
                    $count_num_building_other_type = count_this_building_level($other_type, '', '4', 'both', 'none');
                    if ($count_num_building == 0 && $count_num_building_other_type == 0) {           // If not built, or if type is not Access or Tourist info, we are allowed to build

                        $cost_building = $building_generic_info_dataArray->building_cost;    // cost of the building
                        $gain_reputation = $building_generic_info_dataArray->reputation;    // reputation to gain
                        $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                        $money_after_payment = $cash_player - $cost_building;           // we calculate how much the player will have left after the payment
                        if ($money_after_payment >= 0) {                            // If enough cash
                            if ($removeCashQuery = $this->users_model->pay_item($cost_building, $cash_player)){      //the paiment for the building has been taken
                                // Adds the expense to the game_resort_cost_purchases table
                                $currentUserID = $this->users_model->get_user_id();
                                //$friendly_name = friendly_name($type);
                                $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                                $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                                $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                                $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                            }
                            $add_reputation = $this->users_model->add_reputation($gain_reputation);
                            $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                            $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                            // We prepare the building data
                            $end_construction = calculate_end_construction($type, $level);  
                            $data_ach = array (
                                'id_resort' => $currentResortID,
                                'id_building' => $id_building,       // ID of the building in the game_buildings table
                                'type' => $type,       // 1 id the ID of the tourist info type
                                'level' => '1',             // only one level for tourist info
                                'end_construction' => $end_construction,
                                'id_status' => '4'      // under construction
                            );
                            $data_achievement = array (
                                'id_sector' => '*',
                                'id_resort' => $currentResortID,
                                'id_building' => $id_building,       // ID of the building in the game_buildings table
                                'type' => $type,       // 1 id the ID of the tourist info type
                                'level' => '1',             // only one level for tourist info
                                'end_construction' => $end_construction,
                                'id_status' => '4'      // under construction
                            );
                            
                            $build_building = $this->building_model->build_building_db($data_ach);   // Builds the building in the DB
                            $call_achievements_check = call_achievements_check($data_achievement, 'build');   
                            $call_achievements_check = call_achievements_check($data_ach = array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building), 'build_amount');   // Check spending achievements
                            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'].$this->lang->line($type)['title'].$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'].$this->lang->line($type)['title'].$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            if ($build_building) {
                                $data['infoMessage'][$type] = 'building_built';
                            }
                            else {
                                $data['infoMessage'][$type] = 'building_not_built';
                            }
                        }
                        else {
                            $data['infoMessage'][$type] = 'not_enough_money';
                        }
                    }
                    else {      // Already built (display info message)
                        $data['infoMessage'][$type] = 'building_already_built';
                    }
                }
                else {      // Trying to build a different level
                        $data['infoMessage'][$type] = 'building_not_built';
                }
            }
            else {      // The tourist infor center is not built
                $data['infoMessage'][$type] = 'tourist_info_required';
            }
        }
        else {      // The tourist infor center is not built
            $data['infoMessage'][$type] = 'tourist_info_required';
        }
        $this->index($data);
    }
    
    public function upgrade_building($currentResortID, $type, $level){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        if ($level <= '3') {
          
            // Gets the id_building
            $building_generic_info_data = $this->building_model->get_generic_building_data($type, $level);      // get the generic info (we need the id_building here)
            if ($building_generic_info_data->num_rows() > 0) {                // the generic accessResort building exists in the DB (always!)
                $building_generic_info_dataArray = $building_generic_info_data->row();
                $id_building = $building_generic_info_dataArray->id_building;
            }

            // Checks if the player has already built the building ID for this level
            $count_building_level = count_this_building_level($type, $level, '', 'none', 'both');
            // Checks if the player has already built the same building but previous level
            $count_building_previous_level = count_this_building_level($type, $level-1, '', 'none', 'both');
            // Checks if there is any building under construction
            $count_building_under_construction = count_this_building_level($type, '', '4', 'both', 'none');
            if ($type == 'access')
                $other_type = 'parking';
            else if ($type == 'parking')
                $other_type = 'access';
            // Check if the other type of building is already upgrading (access type if current type is parking...)
            $count_num_building_other_type = count_this_building_level($other_type, '', '4', 'both', 'none');
            // Only if previous level is already built
            if ($count_building_previous_level == '1') {
                if ($count_building_level == 0 && $count_building_under_construction == 0 && $count_num_building_other_type == 0) {           // If current level not built, or if type if not Access or Tourist info, we are allowed to building more
                    $cost_building = $building_generic_info_dataArray->building_cost;    // cost of the building
                    $gain_reputation = $building_generic_info_dataArray->reputation;    // reputation to gain
                    $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                    $money_after_payment = $cash_player - $cost_building;           // we calculate how much the player will have left after the payment
                    if ($money_after_payment >= 0) {                            // If enough cash
                        if ($removeCashQuery = $this->users_model->pay_item($cost_building, $cash_player)){      //the paiment for the building has been taken
                            $currentUserID = $this->users_model->get_user_id();
                            $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                            $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                            // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                            $call_achievements_check = call_achievements_check($data_ach = array('id_resort' => $currentResortID, 'quantity' => $cost_building), 'upgrade_amount');   // Check upgrading achievements
                            $add_reputation = $this->users_model->add_reputation($gain_reputation);
                            $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                            $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                            // We prepare the building data
                            $end_construction = calculate_end_construction($type, $level);
                            $data_ach = array (
                                'id_resort' => $currentResortID,  
                                'level' => $level,             // only one level for tourist info
                                'type' => $type,
                                'end_construction' => $end_construction
                            );
                            $call_achievements_check = call_achievements_check($data_ach_build = array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building), 'build_amount');   // Check spending achievements
                            $build_building = $this->building_model->update_building_db($currentResortID, $type, $level-1 , $data_ach);
                            $call_achievements_check = call_achievements_check($data_ach, 'upgrade');   // Builds the building in the DB
                            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$this->lang->line($type)['title'].' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$this->lang->line($type)['title'].' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            $data['infoMessage'][$type] = 'building_upgraded';
                        }
                    }
                    else {
                        $data['infoMessage'][$type] = 'not_enough_money';
                    }
                }
                else {      // Already built (display info message)
                    $data['infoMessage'][$type] = 'building_not_upgraded';
                }
            }
            else {      // Need to build previous level first (display info message)
                $data['infoMessage'][$type] = 'building_not_built_previous';
            }
        }
        else {      // Trying to upgrade a wrong level
            $data['infoMessage'][$type] = 'building_not_upgraded';
        }
        $this->index($data);
    }
public function save_skipass_prices() {
    $currentUserID   = $this->users_model->get_user_id();
    $currentResortID = $this->users_model->get_resort_id($currentUserID);

    $daily_price  = (int) $this->input->post('daily_price', TRUE);
    $weekly_price = (int) $this->input->post('weekly_price', TRUE);

    // Validate prices
    if ($daily_price < MIN_SKIPASS_DAILY || $daily_price > MAX_SKIPASS_DAILY
        || $weekly_price < MIN_SKIPASS_WEEKLY || $weekly_price > MAX_SKIPASS_WEEKLY) {
        echo json_encode(['status' => 'error', 'message' => $this->lang->line('tourist_info')['price_not_updated']]);
        return;
    }

    $data = [
        'skipass_daily'  => $daily_price,
        'skipass_weekly' => $weekly_price,
    ];

    $updated = $this->building_model->update_skipass_price_db($data, $currentResortID);

    echo json_encode($updated
        ? ['status' => 'success', 'message' => $this->lang->line('tourist_info')['price_updated']]
        : ['status' => 'error',   'message' => $this->lang->line('tourist_info')['price_not_updated']]
    );
}
public function save_dynamic_pricing() {
    $currentUserID   = $this->users_model->get_user_id();
    $currentResortID = $this->users_model->get_resort_id($currentUserID);

    // Get POST data
    $vip_pass_price      = (int)$this->input->post('vip_pass_price', TRUE);
    $family_discount_pct = (int)$this->input->post('family_discount_pct', TRUE);
    $group_discount_pct  = (int)$this->input->post('group_discount_pct', TRUE);

    // Validate ranges
    if ($vip_pass_price < MIN_VIP_PASS_PRICE || $vip_pass_price > MAX_VIP_PASS_PRICE) {
        echo json_encode(['status' => 'error', 'message' => $this->lang->line('tourist_info')['price_not_updated']]);
        return;
    }

    if ($family_discount_pct < MIN_FAMILY_DISCOUNT_PCT || $family_discount_pct > MAX_FAMILY_DISCOUNT_PCT) {
        echo json_encode(['status' => 'error', 'message' => $this->lang->line('tourist_info')['price_not_updated']]);
        return;
    }

    if ($group_discount_pct < MIN_GROUP_DISCOUNT_PCT || $group_discount_pct > MAX_GROUP_DISCOUNT_PCT) {
        echo json_encode(['status' => 'error', 'message' => $this->lang->line('tourist_info')['price_not_updated']]);
        return;
    }

    // Save to database
    $updated = $this->building_model->save_dynamic_pricing_db(
        $currentResortID,
        $vip_pass_price,
        $family_discount_pct,
        $group_discount_pct
    );

    // Return proper JSON response
    echo json_encode($updated
        ? ['status' => 'success', 'message' => $this->lang->line('tourist_info')['dynamic_pricing_saved']]
        : ['status' => 'error',   'message' => $this->lang->line('tourist_info')['price_not_updated']]
    );
} // <--- make sure this closing brace exists
public function update_parking_fee() {
    $currentUserID   = $this->users_model->get_user_id();
    $currentResortID = $this->users_model->get_resort_id($currentUserID);

    $parking_fee = (int)$this->input->post('parking_fee', TRUE);

    if ($parking_fee < MIN_PARKING_FEE || $parking_fee > MAX_PARKING_FEE) {
        echo json_encode(['status' => 'error', 'message' => $this->lang->line('parking')['fee_not_updated']]);
        return;
    }

    $updated = $this->building_model->update_parking_fee_db($currentResortID, $parking_fee);

    echo json_encode($updated
        ? ['status' => 'success', 'message' => $this->lang->line('parking')['fee_updated']]
        : ['status' => 'error',   'message' => $this->lang->line('parking')['fee_not_updated']]
    );
}
    public function open_building($currentResortID, $type_building){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        $is_the_building_built = $this->building_model->get_created_buildings_for_player($currentResortID, $type_building);     // check if building is built for this player
        if ($is_the_building_built->num_rows() > 0) {           // building is built, let's continue
            $is_the_building_builtArray = $is_the_building_built->row(); 
            $building_status = $is_the_building_builtArray->id_status;
            $type = $is_the_building_builtArray->type;
            if ($building_status == '2' || $building_status == '6'){   // If currently closed (ok)
                $data = array (
                    'id_status' => '1'       // We set the id_status to 1 (open)
                );   
                $result = $this->building_model->update_open_close_building_db($currentResortID, $type_building, $data);    // Opens the building in the database
                $data_achievement = array (
                    'id_sector' => '*',
                    'level' => '*',       // Not used
                    'id_building' => '1',     // tourist info ID
                    'type' => $type_building,     // type = "tourist_info"
                    'id_resort' => $currentResortID       
                ); 
                $currentUserID = $this->users_model->get_user_id();
                $call_achievements_check = call_achievements_check($data_achievement, 'open');   // Check if a corresponding achievement should be updated
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['action'], 'data' => $this->lang->line('logs')['resort_opened']) );   // Add a log row to the game_player_logs table
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['action'], 'data' => $this->lang->line('logs')['resort_opened']) );   // Add a log row to the game_player_logs table
                $data['infoMessage'][$type] = 'building_opened';
                $this->index($data);
            }
            else {      // Already open - error message
                $data['infoMessage'][$type] = 'building_not_opened';
                $this->index($data);
            }
        }
        else {      // Building not built
            $data['infoMessage'][$type] = 'building_not_existing';
                $this->index($data);
        }
    }
    
    /**
     * close_building                Closes the building in the database
     * @param type $currentResortID   Current resort ID
     * @param type $type_building     type of the building to close
     */
    public function close_building($currentResortID, $type_building){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        $is_the_building_built = $this->building_model->get_created_buildings_for_player($currentResortID, $type_building);     // check if building is built for this player
        if ($is_the_building_built->num_rows() > 0) {           // building is built, let's continue
            $is_the_building_builtArray = $is_the_building_built->row(); 
            $building_status = $is_the_building_builtArray->id_status;
            $type = $is_the_building_builtArray->type;
            if ($building_status == '1'){   // If currently opened (ok)
                $data = array (
                    'id_status' => '2'       // We set the id_status to 2 (closed)
                );   
                $currentUserID = $this->users_model->get_user_id();
                $result = $this->building_model->update_open_close_building_db($currentResortID, $type_building, $data);    // Closes the building in the database
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['action'], 'data' => $this->lang->line('logs')['resort_closed']) );   // Add a log row to the game_player_logs table
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['action'], 'data' => $this->lang->line('logs')['resort_closed']) );   // Add a log row to the game_player_logs table
                $data['infoMessage'][$type] = 'building_closed';
                $this->index($data);
            }
            else {      // Already open - error message
                $data['infoMessage'][$type] = 'building_not_closed';
                $this->index($data);
            }
        }
        else {      // Building not built
            $data['infoMessage'][$type] = 'building_not_existing';
                $this->index($data);
        }
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









