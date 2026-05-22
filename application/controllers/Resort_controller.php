<?php
/**
 * 
 */
class Resort_controller extends CI_Controller{
    
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
        $ci->lang->load('resort',$siteLang);
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('lift',$siteLang);
        $ci->lang->load('staff',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $ci->lang->load('finances',$siteLang);
        $logged_status = $this->session->is_logged_in;
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('resort_model');
        $this->load->model('equipment_model');
        $this->load->model('item_model');
        $this->load->model('users_model');
        $this->load->model('achievements_model');
        $this->load->model('logs_model');
        $this->load->model('building_model');
    }
    
    /**
     * index Main function that displays the resort info or the create resort form
     * 
     * @param type $resort_created If the resort has just been created = true. If not, we set to empty
     */
    public function index($resort_created = '', $action = ''){
        if (empty($_COOKIE['beta_access']) || $_COOKIE['beta_access'] !== 'beta_access_granted') {
            redirect('beta_controller/accept');
            return;
        }
        $currentUserID = $this->users_model->get_user_id();
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
            $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)      
            if ($resultResort->num_rows() > 0) {                                        // if the player has a resort, we display the info
                $this->display_resort_info($resort_created, $resultResort, $currentResortID, $action); // call the function displaying the resort info
            }
            else {                                  // The user doesn't have a resort. We display the empty form to create one
                $this->display_create_resort_form();
            }  
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        } 
    }
    
    /**
     * display_create_resort_form Displays the empty form to create a new resort (by setting user_has_resort to false and displaying the view accordingly)
     */
    public function display_create_resort_form(){
        $data['user_has_resort'] = false;                               // Allows us to display the empty form
        $data['infoResort'] = $this->lang->line('resort')['no_resort'];    // invite the player to create a resort message
        $data['main_content'] = 'resort';
        $data['error_msg'] = $this->session->flashdata('error');
        $this->load->view('templates/default',$data);
    }
    
    /**
     * display_resort_info Displays the users resort information
     * 
     * @param type $resort_created  If the resort has just been created = true. If not, we set to empty
     * @param type $resultResort    Array with the resort information
     * @param type $currentResortID   ID of the resort
     */
    public function display_resort_info($resort_created, $resultResort, $currentResortID, $action = false){
        $data['infoResort'] = '';                           // Initialize the resort information
        $data['user_has_resort'] = true;                    // For displaying the right message in the page

        // Legacy data
        $legacy_data = $this->resort_model->get_legacy_data_DB($currentResortID);
        $data['legacy_rating']     = $legacy_data ? $legacy_data->legacy_rating     : NULL;
        $data['legendary_status']  = $legacy_data ? (int)$legacy_data->legendary_status : 0;

        // Collect any idle income that accumulated while the player was offline
        $this->collect_idle_income($currentResortID);

        // If the resort was just created and has more cash than START_CASH, a legacy bonus was applied
        if ($resort_created == true) {
            $resort_row_tmp = $resultResort->row();
            $applied_bonus = (int)$resort_row_tmp->cash - (int)START_CASH;
            $data['legacy_bonus_applied'] = ($applied_bonus > 0) ? $applied_bonus : 0;
        } else {
            $data['legacy_bonus_applied'] = 0;
        }
        
        
        
        // Summary of buildings
        $data['summaryBuildings'] ='<div class="table-responsive mt-2">
            <table class="table sector_table building">
            <thead>';
        $data['summaryBuildings'] .='<tr>
            <th>'.$this->lang->line('building')['building_type'].'</th>
            <th style="border-top-left-radius: 0px;">'.$this->lang->line('home')['level'].' 1</th>
            <th style="border-top-left-radius: 0px;">'.$this->lang->line('home')['level'].' 2</th>
            <th style="border-top-left-radius: 0px;">'.$this->lang->line('home')['level'].' 3</th>
            <th style="border-top-left-radius: 0px;"><div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('building')['total_capacity_help'].'">'.$this->lang->line('building')['total_capacity'].' <i class="fa-regular fa-circle-question opacity-60"></i></div></th>
            <th style="border-top-left-radius: 0px;"><div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('building')['max_tourists_help'].'">'.$this->lang->line('building')['max_tourists'].' <i class="fa-regular fa-circle-question opacity-60"></i></div></th>
            </tr>
            </thead>
            <tbody>';

        $list_all_building_types = $this->building_model->list_all_building_types();
        foreach ($list_all_building_types->result() as $row_types){
            
            $total_capacity_building_type = 0;
            $max_visitors_type = 0;
            
            $data['summaryBuildings'] .= '<tr>';
            $building_icons = [
                'tourist_info' => 'fa-solid fa-signs-post',
                'hotel'        => 'fa-solid fa-hotel',
                'restaurant'   => 'fa-solid fa-utensils',
                'parking'      => 'fa-solid fa-square-parking',
                'leisure'      => 'fa-solid fa-gamepad',
                'rental'       => 'fa-solid fa-ski-boot-ski',
                'medical'      => 'fa-solid fa-truck-medical',
                'access'       => 'fa-solid fa-signs-post',
                'cannon'       => 'fa-solid fa-snowflake',
            ];
            $btype = $row_types->type;
            $bicon = isset($building_icons[$btype]) ? '<i class="'.$building_icons[$btype].' me-1"></i>' : '';
            $data['summaryBuildings'] .='<th>'.$bicon.$this->lang->line('finances')[$btype].'</th>';
            for ($i = 1; $i <= 3; $i++){
                $list_all_build_buildings = $this->building_model->get_all_created_buildings_for_player( $currentResortID, $row_types->type, $i);
                
                // Move capacity lookup outside the foreach loop since it doesn't depend on row_building
                $get_building_capacity = $this->building_model->get_building_capacity( $row_types->type, $i);

                foreach ($list_all_build_buildings->result() as $row_building){
                    if ($row_types->type == 'tourist_info' && $i != 1) {
                        $data['summaryBuildings'] .='<td>-</td>';
                    }
                    else {
                        $data['summaryBuildings'] .='<td>'.$row_building->count.'</td>';
                        $total_capacity_building_type = round($total_capacity_building_type + ($get_building_capacity->capacity * $row_building->count),0);
                        $max_visitors_type = round(($total_capacity_building_type + ($get_building_capacity->capacity * $row_building->count))/PERC_TOURISTS_BUILDING[$row_types->type],0);
                    }
                }
            }
            if ($row_types->type != 'tourist_info' && $row_types->type != 'access' && $row_types->type != 'cannon') {
                $data['summaryBuildings'] .='<td>'.number_format($total_capacity_building_type, 0, ',', ' ').'</td>';
                $data['summaryBuildings'] .='<td>'.number_format($max_visitors_type, 0, ',', ' ').'</td>';
            }
            else {
                $data['summaryBuildings'] .='<td>-</td>';
                $data['summaryBuildings'] .='<td>-</td>';
            }

            $data['summaryBuildings'] .= '</tr>';
        }
        $data['summaryBuildings'] .= '</tbody></table></div>';
                    
                    
                    
            if ($action != 'resort_updated' &&$resort_created == true && $action != 'bad_action' && $action != 'item_not_sold' && $action != 'item_sold' && $action != 'item_opened' && $action != 'item_closed' ){                   //We have just created the resort, display success message
                $data['infoResort'] .= $this->lang->line('resort')['creation_successful'];
            }
            if ($action == 'resort_updated') {
                $data['infoResort'] .= $this->lang->line('resort')['update_successful'];
            }
            if ($action == 'item_opened') {
                $data['infoResort'] .= $this->lang->line('resort')['item_opened'];
            }
            if ($action == 'item_closed') {
                $data['infoResort'] .= $this->lang->line('resort')['item_closed'];
            }
            if ($action == 'lift_unsellable') {
                $data['infoResort'] .= $this->lang->line('resort')['lift_unsellable'];
            }
            $resultResort2 = $resultResort->row();              // we put the result of the resort info query (done in Index) in an array
            $guide_link = base_url('help_controller');
            $data['infoResort'] .= '<div class="alert alert-info d-flex align-items-center gap-2 mb-4"><i class="fa-solid fa-lightbulb flex-shrink-0"></i><div>'.$this->lang->line('home')['need_help_check_out'].'<a href="'.$guide_link.'" target="_blank" class="alert-link fw-semibold">'.$this->lang->line('home')['beginners_guide'].'!</a></div></div>';

            // Star Rating – derived from current reputation
            $resort_reputation = isset($resultResort2->reputation) ? (int)$resultResort2->reputation : 0;
            $star_rating = 1;
            foreach (STAR_RATING_THRESHOLDS as $stars => $min_rep) {
                if ($resort_reputation >= $min_rep) {
                    $star_rating = $stars;
                }
            }
            $stars_html = str_repeat('★', $star_rating) . str_repeat('☆', 5 - $star_rating);

            // Quick stats grid – shown FIRST so key numbers are immediately visible
            $open_slopes_count = $this->db->from('game_created_slopes')
                ->where('id_resort', $currentResortID)
                ->where('id_status', 1)
                ->count_all_results();
            $open_lifts_count = $this->db->from('game_created_lifts')
                ->where('id_resort', $currentResortID)
                ->where('id_status', 1)
                ->count_all_results();
            $total_staff_count = $this->db->from('game_hired_staff')
                ->where('id_resort', $currentResortID)
                ->count_all_results();
            $today_revenue_row = $this->db
                ->select('COALESCE(SUM(revenue), 0) as total_rev')
                ->from('game_resort_revenue')
                ->where('id_resort', $currentResortID)
                ->where('date >=', gmdate('Y-m-d'))
                ->get()->row();
            $today_revenue = isset($today_revenue_row->total_rev) ? (int)$today_revenue_row->total_rev : 0;

            $data['infoResort'] .= '<div class="resort-stat-grid grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">';
            $data['infoResort'] .= '<div class="resort-stat-box"><div class="resort-stat-icon text-warning"><i class="fa-solid fa-star"></i></div><div class="resort-stat-label">'.$this->lang->line('resort')['star_rating_label'].'</div><div class="resort-stat-value">'.$star_rating.'/5</div><div class="resort-stat-desc">'.$stars_html.' ('.$resort_reputation.' rep)</div></div>';
            $data['infoResort'] .= '<div class="resort-stat-box"><div class="resort-stat-icon text-info"><i class="fa-solid fa-person-skiing"></i></div><div class="resort-stat-label">'.$this->lang->line('resort')['stat_open_slopes'].'</div><div class="resort-stat-value">'.$open_slopes_count.'</div><div class="resort-stat-desc">'.$this->lang->line('resort')['stat_active_runs'].'</div></div>';
            $data['infoResort'] .= '<div class="resort-stat-box"><div class="resort-stat-icon text-success"><i class="fa-solid fa-cable-car"></i></div><div class="resort-stat-label">'.$this->lang->line('resort')['stat_open_lifts'].'</div><div class="resort-stat-value">'.$open_lifts_count.'</div><div class="resort-stat-desc">'.$this->lang->line('resort')['stat_operating'].'</div></div>';
            $data['infoResort'] .= '<div class="resort-stat-box"><div class="resort-stat-icon text-primary"><i class="fa-solid fa-users"></i></div><div class="resort-stat-label">'.$this->lang->line('resort')['stat_staff'].'</div><div class="resort-stat-value">'.$total_staff_count.'</div><div class="resort-stat-desc">'.$this->lang->line('resort')['stat_hired'].'</div></div>';
            $data['infoResort'] .= '</div>';

            // Info / Microclimate / Map – 3-col grid below the stats
            // Map is first so it's immediately visible on mobile
            $data['infoResort'] .= '<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">';

            // Resort map card (first = left column / top on mobile)
            $data['infoResort'] .= '<div class="card card-body h-100 text-center">';
            $data['infoResort'] .= '<a href="'.base_url('resort_map_controller/').'" class="d-flex flex-column align-items-center justify-content-center h-100 text-decoration-none">';
            $data['infoResort'] .= '<p class="fw-bold mb-2">'.$this->lang->line('resort')['click_start_building'].'</p>';
            $data['infoResort'] .= '<img src="'.base_url('img/images/mini_map.jpg').'" title="'.$this->lang->line('resort')['trail_map'].'" class="img-fluid rounded"/>';
            $data['infoResort'] .= '</a>';
            $data['infoResort'] .= '</div>';

            // Microclimate info card (middle column)
            $altitude  = isset($resultResort2->altitude) ? $resultResort2->altitude  : 'medium';
            $aspect    = isset($resultResort2->aspect)   ? $resultResort2->aspect    : 'north';
            $wind_risk = ['low' => 'wind_risk_low', 'medium' => 'wind_risk_medium', 'high' => 'wind_risk_high'];
            $data['infoResort'] .= '<div class="card card-body h-100">';
            $data['infoResort'] .= '<h5 class="card-title">'.$this->lang->line('resort')['microclimate_info'].' <a href="'.base_url('microclimate_controller/').'" title="'.$this->lang->line('resort')['microclimate_edit_title'].'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a></h5>';
            $data['infoResort'] .= '<p class="mb-1"><b>'.$this->lang->line('resort')['altitude_label'].':</b> '.$this->lang->line('resort')['altitude_'.$altitude].'</p>';
            $data['infoResort'] .= '<p class="mb-1"><b>'.$this->lang->line('resort')['aspect_label'].':</b> '.$this->lang->line('resort')['aspect_'.$aspect].'</p>';
            $data['infoResort'] .= '<p class="mb-1"><b>'.$this->lang->line('resort')['altitude_build_cost_info'].':</b> x'.number_format(get_altitude_build_cost_multiplier($altitude), 2).'</p>';
            $data['infoResort'] .= '<p class="mb-0"><b>'.$this->lang->line('resort')[$wind_risk[$altitude]].'</b></p>';
            $data['infoResort'] .= '</div>';

            // Resort info card (last column – details below the map on mobile)
            $star_tooltip = $this->lang->line('resort')['star_rating_tooltip'];
            $data['infoResort'] .= '<div class="card card-body h-100" id="infoResort">';
            $data['infoResort'] .= '<h4 class="card-title">'.$this->lang->line('resort')['text'].$resultResort2->resort_name.' ';
            $data['infoResort'] .= '<i class="fa-solid fa-pen-to-square cursor-pointer ml-1" aria-hidden="true" title="'.$this->lang->line('home')['edit'].'" id="edit_resort_mode"></i></h4>';
            $data['infoResort'] .= '<p class="mb-1"><b>'.$this->lang->line('resort')['location_show'].'</b> '.$resultResort2->resort_country.'</p>';
            $data['infoResort'] .= '<p class="mb-1"><b>'.$this->lang->line('resort')['description_show'].'</b> '.$resultResort2->resort_description.'</p>';
            $data['infoResort'] .= '<p class="mb-2"><b>'.$this->lang->line('resort')['star_rating_label'].':</b> <span class="text-warning tooltip tooltip-bottom" data-tip="'.htmlspecialchars($star_tooltip).'">'.$stars_html.'</span> ('.$star_rating.'/5)</p>';
            $data['infoResort'] .= '<button type="button" class="btn btn-sm btn-outline-secondary share-resort-btn mt-2"'
                .' data-resort-name="'.htmlspecialchars($resultResort2->resort_name, ENT_QUOTES, 'UTF-8').'"'
                .' data-resort-country="'.htmlspecialchars($resultResort2->resort_country, ENT_QUOTES, 'UTF-8').'">'
                .'<i class="fa-solid fa-share-nodes"></i> '.$this->lang->line('resort')['share_resort']
                .'</button>';
            $data['infoResort'] .= '</div>';

            $data['infoResort'] .= '</div>';
            $data['infoResort'] .= '<hr class="my-4">';
            for ($sector_id = 0; $sector_id <= ACTIVE_SECTORS; $sector_id++){    // We have 3 sectors for now, for browsing all of them. Needs to be adjusted before release if necessary
                // getting info about the sector
                $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                //echo ' id: '.$sector_id.' >array: '.$sector_access_array[$sector_id];
                if (isset($sector_access_array[$sector_id]) && $sector_access_array[$sector_id] == $sector_id){ // If sector is unlocked
                    $mini_groomer_area_info[$sector_id] = '';
                    $status_for_table_data = '1';       // Used to pass data to JS for the current sector/table
                    $arrow_direction = 'collapse';
                    
                    // Gets how many items (groomers) are purchased for this sector
                    $items_for_this_sector = $this->resort_model->get_purchased_equipment_sector($currentResortID, '1', $sector_id);
                    $num_items_for_this_sector = $items_for_this_sector->num_rows();
                    $item_count_assigned = 0;
                    $item_count_not_assigned = 0;
                    $count_groomer_coverage = 0;
                    $count_groomer_coverage_and_staffed = 0;
                    $mini_groomer_area = '';
                    // For each item we need to check if there is a staff assigned
                    
                    foreach ($items_for_this_sector->result() as $row_groomer){
                        $assigned_groomers_this_slope = $this->resort_model->get_associated_staff_DB('game_hired_staff', 'id_resort', $currentResortID, 'id_item_assigned', $row_groomer->id_purchased_equipments, 'type_item_assigned', 'groomer');
                        
                        $num_assigned_groomers_this_slope = $assigned_groomers_this_slope->num_rows();  //number of employees assigned to the item (0 or 1 only)

                        $count_groomer_coverage = $count_groomer_coverage + $row_groomer->level;    // Grooming capacity depending of groomer level
                        $items_for_this_sector->result();

                        if ($num_assigned_groomers_this_slope > 0) {
                            $item_count_assigned ++;     // There is a staff assigned to the groomer ID
                        }
                        else if ($num_assigned_groomers_this_slope == 0) {
                            $item_count_not_assigned ++;     // There is no staff assigned to the groomer ID
                        }
                    }
                    // Counting the number of open and built slopes for this sector
                    $slopes_for_this_sector = $this->resort_model->get_num_slopes_sector($currentResortID, $sector_id);
                    // Putting as an integer value
                    $num_slopes_for_this_sector = $slopes_for_this_sector->num_rows();
                    // For each built and open slope of the sector
                    for ($j = 1; $j <= $num_slopes_for_this_sector; $j++){

                        if ($j <= $count_groomer_coverage && ($item_count_assigned >= $j || $item_count_assigned >= $num_items_for_this_sector)){         // If item covering and staff assigned
                            $mini_groomer_area .= '<i class="fa-solid fa-circle-check text-success mr-1" title="Groomer assigned &amp; staffed"></i>';
                        }
                        else if ($j <= $count_groomer_coverage && $item_count_assigned < $num_items_for_this_sector){              // Item covering but no staff assigned
                            $mini_groomer_area .= '<i class="fa-solid fa-circle-exclamation text-warning mr-1" title="Groomer present but no mechanic"></i>';
                        }
                        else {                                                  // If no item covering
                            $mini_groomer_area .= '<i class="fa-solid fa-circle-xmark text-error mr-1" title="Slope not covered"></i>';
                        }
                    }
                    // Building text for tooltip
                    $mini_groomer_area_info[$sector_id] .= '<b>'.$this->lang->line('home')['sector'].$sector_id.':</b><br>'
                        .$this->lang->line('resort')['grooming_requirements'].': '.$num_slopes_for_this_sector.'    '
                        .$this->lang->line('resort')['grooming_cap_avail'].': '.$count_groomer_coverage.'     '
                        .'('.$this->lang->line('resort')['groomers_available'].': '.$num_items_for_this_sector.')<br>'
                        .$this->lang->line('hireStaff')['mechanicGroomer'].' '.$this->lang->line('resort')['required'].': '.$num_items_for_this_sector.'    '
                        .$this->lang->line('hireStaff')['mechanicGroomer'].' '.$this->lang->line('resort')['available'].': '.$item_count_assigned.'    ';

                    
                    // Building the table
                    $data['infoResort'] .='<div class="table-responsive mt-3">
                                            <table class="table sector_table building" id="'.$sector_id.'" data-access="'.$status_for_table_data.'">
                                                <thead>';
                    $link = false;
                    $data['infoResort'] .='<tr class="collapsable_cmd_header">
                                            <th><i class="fa-solid fa-mountain me-1"></i>'.$this->lang->line('home')['sector'].' '.$sector_id.'</th>
                                            <th colspan="8" style="border-top-left-radius: 0px;">';
                    if ($item_count_assigned < $num_items_for_this_sector) {
                        $data['infoResort'] .= '<a href='.base_url('hire_staff_controller/').'>';
                        $link = true;
                    }
                    else if ($count_groomer_coverage < $num_slopes_for_this_sector) {
                        $data['infoResort'] .= '<a href='.base_url('groomer_controller/').'>';
                        $link = true;
                    }
                    $data['infoResort'] .= '<div style="display:inline-flex;align-items:center;text-align:left;" class="tooltip tooltip-bottom" data-tip="'.$mini_groomer_area_info[$sector_id].'">'.$mini_groomer_area.'</div>';
                    if ($link)
                        $data['infoResort'] .= '</a>';
                    $data['infoResort'] .= '<span class="align_right" id="collapse_expand-'.$sector_id.'"><i class="fa-solid fa-angle-down transition-transform" id="collapse-icon-'.$sector_id.'"></i></span></th>
                                            </tr></thead><tbody>';

                    $array_item = ['lift', 'slope'];
                    foreach ($array_item as $current_item){

                        // START SLOPES

                        // START DEFINE VALUES
                        $type_item = $current_item;
                        $modelName = 'item_model';
                        $id_item = 'id_'.$current_item;
                        $item_build = $current_item.'_build';
                        $item_status = $current_item.'_status';
                        $item_condition = $current_item.'_condition';
                        // END DEFINE VALUES
                        
                        $function = 'get_built_item_info_'.$type_item;
                        $items_for_this_sector = $this->$modelName->$function($currentResortID, $sector_id);
                        
                        if ($items_for_this_sector->num_rows() > 0) {                          // if there are existing items for this sector (in DB)
                       
                            // Header of the item type
                            $data['infoResort'] .='<tr class="datarow collapsable_block">                                                            
                                                    <th>'.$this->lang->line($type_item)['name'].'</th>
                                                    <th>'.$this->lang->line($type_item)['diff_type_column'].'</th>';
                            if ($type_item == 'lift'){
                                $data['infoResort'] .= '<th>'.$this->lang->line('home')['level'].'</th>';
                            }
                            else
                                $data['infoResort'] .= '<th>'.$this->lang->line('slope')['length'].'</th>';

                            $data['infoResort'] .= '<th>'.$this->lang->line('slope')['condition'].'</th>';
                            if ($type_item == 'lift') {
                                $data['infoResort'] .= '<th>'.$this->lang->line($type_item)['length_speed_column'].'</th>';
                                $data['infoResort'] .= '<th>'.$this->lang->line('lift')['throughput'].'</th>';
                                $data['infoResort'] .= '<th>'.$this->lang->line('home')['upgrade_cost'].'</th>';
                            }
                            else
                                $data['infoResort'] .= '<th colspan="3">'.$this->lang->line('slope')['deserving_lift'].'</th>';
                            $data['infoResort'] .= '<th>'.$this->lang->line('home')['status'].'</th>
                                                <th>'.$this->lang->line('home')['action'].'</th>
                                            </tr>';
                        
                            $name_language = 'name_'.$this->session->userdata('site_lang');     // get the current language setting (for selecting language in DB)
                            foreach ($items_for_this_sector->result() as $info_item){
                                //var_dump($info_item);
                                if ($type_item == 'lift'){
                                    $start_lift_sector_info = $this->item_model->get_start_lift_sector($currentResortID, $info_item->id_group_location);
                                    $start_lift_sector_row = $start_lift_sector_info->row();
                                    $start_lift_sector = $start_lift_sector_row->id_sector;
                                }
                                else if ($type_item == 'slope'){
                                    $slope_type = $info_item->slope_type;
                                }
                                if ((isset($start_lift_sector) && $start_lift_sector == $sector_id) || !isset($start_lift_sector) || $type_item == 'slope') {
                                    // initializing the variables
                                        $deserving_lifts_status = '';
                                        $num_deserving_lifts = '';
                                        if ($type_item == 'lift') {
                                            $value_id = $info_item->id_group_lift;
                                            $id_created_item = $info_item->id_created_lifts;
                                            $value_location = $info_item->id_group_location;
                                        }
                                        else if ($type_item == 'slope'){
                                            $value_id = $info_item->$id_item;
                                            $value_location = $info_item->$id_item;
                                            $id_created_item = $info_item->id_created_slopes;
                                        }
                                        $item_build = true;
                                        $item_status = '';
                                        $level = '';
                                        // To check deserving lifts
                                        if ($type_item == 'slope'){  
                                            //$building_status_to_show = $this->lang->line('slope')['building_status_yes'];
                                            $names_deserving_lifts = '';
                                            $number_deserving_lifts = 0;
                                            $result_deserving_lifts = get_deserving_lifts($currentResortID, $info_item->id_slope, 'lift_names');
                                            foreach ($result_deserving_lifts as $names) {
                                                $names_deserving_lifts .= $names.', '; // concatenate the name of lifts deserving the slope (or area)
                                            } 
                                            $number_deserving_lifts = get_deserving_lifts($currentResortID, $info_item->id_slope, 'number');

                                            $deserving_lift_column = substr($names_deserving_lifts, 0, -2); // Remove the last two characters of the string (, )
                                            // End: Get deserving lifts

                                            //$num_deserving_lifts = $this->count_deserving_lifts($currentResortID, $info_item->id_created_slopes);
                                            $num_assigned_skipatrol = $this->count_assigned_staff($currentResortID, $info_item->id_created_slopes);
                                            if ($slope_type == 1 || $slope_type == 2 || $slope_type == 3 || $slope_type == 6) { // Only for downhill, snowpark, boardercross and terrain park
                                                if ($number_deserving_lifts == '0') {
                                                    if ($num_assigned_skipatrol != '0')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_deserving_lift'].'"><i class="fa-solid fa-triangle-exclamation" style="color: rgb(255, 212, 59);"></i></div>';
                                                    else if($num_assigned_skipatrol == '0')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_deserving_lift'].$this->lang->line('lift')['and'].$this->lang->line('lift')['no_assigned_skipatrol'].'"><i class="fa-solid fa-triangle-exclamation" style="color: rgb(255, 212, 59);"></i></div>';
                                                }
                                                else if ($number_deserving_lifts == '1') {
                                                    if($num_assigned_skipatrol == '0')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_assigned_skipatrol'].'"><i class="fa-solid fa-triangle-exclamation" style="color: rgb(255, 212, 59);"></i></div>';
                                                    else if($num_assigned_skipatrol == '1')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['deserving_lift_sing'].$this->lang->line('lift')['and'].$this->lang->line('lift')['assigned_skipatrol_sing'].'"><i class="fa-solid fa-check"></i></div>';
                                                    else if($num_assigned_skipatrol >= '2')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['deserving_lift_sing'].$this->lang->line('lift')['and'].$this->lang->line('lift')['deserving_lift_part1_plur'].$num_assigned_skipatrol.$this->lang->line('lift')['assigned_skipatrol_part2_plur'].'"><i class="fa-solid fa-check"></i></div>';
                                                }
                                                else if ($number_deserving_lifts >= '2') {
                                                    if($num_assigned_skipatrol == '0')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_assigned_skipatrol'].'"><i class="fa-solid fa-triangle-exclamation" style="color: rgb(255, 212, 59);"></i></div>';
                                                    else if($num_assigned_skipatrol == '1')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['deserving_lift_part1_plur'].$number_deserving_lifts.$this->lang->line('lift')['deserving_lift_part2_plur'].$this->lang->line('lift')['and'].$this->lang->line('lift')['assigned_skipatrol_sing'].'"><i class="fa-solid fa-check"></i></div>';
                                                    else if($num_assigned_skipatrol >= '2')
                                                        $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['deserving_lift_part1_plur'].$number_deserving_lifts.$this->lang->line('lift')['deserving_lift_part2_plur'].$this->lang->line('lift')['and'].$this->lang->line('lift')['deserving_lift_part1_plur'].$num_assigned_skipatrol.$this->lang->line('lift')['assigned_skipatrol_part2_plur'].'"><i class="fa-solid fa-check"></i></div>';
                                                }
                                            }
                                            else {  // if $slope_type != 1 , 2 or 3 (so only for crosscountry 4 or luge 5)
                                                $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_lift_or_patrol_needed_slope_type'].'"><i class="fa-solid fa-check"></i></div>';
                                            }
                                            $id_group_location_for_lifts = '';
                                        }
                                        if ($type_item == 'lift'){ 
                                            $level = $info_item->level;
                                            $full_level_name = $this->lang->line('home')['level'].' '.$level;
                                            if ($level)
                                            $building_status_to_show = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$full_level_name.'"><span class="badge badge-neutral badge-sm">Lv.'.$level.'</span>';

                                            // Check if staff is assigned to the lift
                                            $num_assigned_staff = $this->count_assigned_staff($currentResortID, $info_item->id_created_lifts);
                                            if ($num_assigned_staff == '0') {
                                                $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['no_deserving_mechanic'].'"><i class="fa-solid fa-triangle-exclamation" style="color: rgb(255, 212, 59);"></i></div>';
                                            }
                                            else if ($num_assigned_staff == '1') {
                                                $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['deserving_mechanic_sing'].'"><i class="fa-solid fa-check"></i></div>';
                                            }
                                            else if ($num_assigned_staff >= '2') {
                                                $deserving_lifts_status = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('lift')['deserving_lift_part1_plur'].$num_assigned_staff.$this->lang->line('lift')['deserving_mechanic_part2_plur'].'"><i class="fa-solid fa-check"></i></div>';
                                            }
                                            $length_speed_column = $info_item->speed.' m/s';     // Speed of lift in common column
                                            
                                            $location_info = $this->resort_model->get_item_location(null, $info_item->id_group_location, 'lift'); 
                                            $location_info_row = $location_info->row();
                                            $length = $location_info_row->length;
                                            
                                            if ($level != 3) {
                                                $items_for_this_sector_max_level = $this->$modelName->get_generic_item_info_for_level($value_id, $type_item, $level+1); // Need to check the max speed of the lift
                                                $items_for_this_sector_max_level_data = $items_for_this_sector_max_level->row(); 
                                                $base_cost_next_level = $items_for_this_sector_max_level_data->base_cost;
                                                $meter_cost_next_level = $items_for_this_sector_max_level_data->meter_cost;
                                                $upg_cost = $base_cost_next_level + $meter_cost_next_level*$length;
                                                $building_cost = number_format($upg_cost, 0, ',', ' ').' €';
                                            }
                                            else {
                                                $building_cost = '-';
                                            }
                                            $throughput = $info_item->throughput.' s/h';
                                            $id_group_location_for_lifts = $info_item->id_group_location;
                                        }

                                        // display info if applicable

                                        $cond_val = min((int)$info_item->$item_condition, 100);
                                        $cond_color = ($cond_val >= 70) ? 'progress-success' : (($cond_val >= 40) ? 'progress-warning' : 'progress-error');
                                        $item_building_condition_to_show = '<div class="flex items-center gap-1 min-w-[80px]"><progress class="progress '.$cond_color.' w-14" value="'.$cond_val.'" max="100"></progress><span class="text-xs opacity-70">'.$cond_val.'%</span></div>';
                                        if ($info_item->custom_name == NULL) {                        // If the built slope has no name, 
                                            $item_name = $info_item->$name_language;                                  // We display the default name ("Slope X")
                                        }
                                        else {
                                            $item_name = $info_item->custom_name;                    // If name is not NULL, we display the custom name
                                        }

                                        if ($info_item->id_status == 1) {
                                            $item_status = 'close';
                                            $item_opposite_action = 'click_to_close';
                                            $item_building_status_to_show = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')[$item_opposite_action].'"><a href="'.base_url('resort_controller/'.$item_status.'_item/'.$value_location.'/'.$currentResortID.'/'.$type_item.'').'"><span class="badge badge-success gap-1"><i class="fa-solid fa-circle-play"></i> Open</span></a></div>';
                                            }
                                        else if ($info_item->id_status == 2) {
                                            $item_status = 'open';
                                            $item_opposite_action = 'click_to_open';
                                            $item_building_status_to_show = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')[$item_opposite_action].'"><a href="'.base_url('resort_controller/'.$item_status.'_item/'.$value_location.'/'.$currentResortID.'/'.$type_item.'').'"><span class="badge badge-warning gap-1"><i class="fa-solid fa-circle-pause"></i> Closed</span></a></div>';
                                        }
                                        else if ($info_item->id_status == 3) {
                                            $item_status = 'maintenance';
                                            $item_opposite_action = 'maintenance_click_for_details';
                                            $item_building_status_to_show = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')[$item_opposite_action].'"><a href="'.base_url($type_item.'_controller/show_info_block_'.$type_item.'/'.$value_id.'/'.$currentResortID.'/null/'.$id_group_location_for_lifts).'"><span class="badge badge-info gap-1"><i class="fa-solid fa-wrench"></i> Maintenance</span></a></div>';
                                        }
                                        else if ($info_item->id_status == 5) {
                                            $item_status = 'out_of_order';
                                            $item_opposite_action = 'out_of_order_click_for_details';
                                            $deserving_lifts_status = ''; // Emptying the variable to avoid showing the checkmark defined previously
                                            $item_building_status_to_show = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')[$item_opposite_action].'"><a href="'.base_url($type_item.'_controller/show_info_block_'.$type_item.'/'.$value_id.'/'.$currentResortID.'/null/'.$id_group_location_for_lifts).'"><span class="badge badge-error gap-1"><i class="fa-solid fa-ban"></i> Out of Order</span></a></div>';
                                        }
                                        else if ($info_item->id_status == 4) {
                                            $item_status = 'under_construction';
                                            $item_opposite_action = 'construction_click_for_details';
                                            $item_building_status_to_show = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')[$item_opposite_action].'"><a href="'.base_url($type_item.'_controller/show_info_block_'.$type_item.'/'.$value_id.'/'.$currentResortID.'/null/'.$id_group_location_for_lifts).'"><span class="badge badge-accent gap-1"><i class="fa-solid fa-helmet-safety"></i> Under Construction</span></a></div>';
                                        }

                                        $item_building_status = $this->$modelName->get_status($info_item->id_status, $name_language); // check if player has built this slope
                                        $item_building_status_array2 = $item_building_status->row();

                                    // Only for slopes
                                    if ($type_item == 'slope'){
                                        $slope_type_info = $this->item_model->get_slope_type_name($info_item->slope_type);     // gets the slope type friendly name base on slope type id
                                        $slope_type_info_row = $slope_type_info->row();
                                        $diff_type_column = $slope_type_info_row->$name_language;   // Downhill, Snowpark, Boardercross, Crosscountry...
                                        //$diff_type_column = $this->lang->line('slope')['diff_'.$info_item->id_difficulty];
                                        $diff_type_column_english = $this->lang->line('slope')['diff_'.$info_item->id_difficulty.'_english'];
                                        $building_status_to_show = $info_item->length.' m';
                                        $length_column = $info_item->length.' m';
                                        $button_name = 'destroy';
                                    }
                                    // Only for lifts
                                    if ($type_item == 'lift'){
                                        $diff_type_column = $info_item->lift_type;       // Type of lift in common column
                                        $diff_type_column_english = '';
                                        $lift_types = $this->item_model->get_lift_types_DB($info_item->lift_type);
                                        $lift_types_object = $lift_types->row();
                                        $diff_type_column = $lift_types_object->$name_language;
                                        $button_name = 'sell';
                                    }
                                    // Building the rows
                                    $data['infoResort'] .=  '
                                                            <tr class="collapsable_block datarow" data-id_created_item="'.$id_created_item.'" data-id_item="'.$value_id.'" data-type="'.$type_item.'" data-currentResortId="'.$currentResortID.'" data-friendly_name="'.$item_name.'">
                                                            <td><a href='.base_url($type_item.'_controller/show_info_block_'.$type_item.'/'.$value_id.'/'.$currentResortID.'/null/'.$id_group_location_for_lifts).'>'.$item_name.'</a></td>
                                                            <td style="border-bottom-left-radius: 0px;" class="'.$diff_type_column_english.'">'.$diff_type_column.'</td>
                                                            <td data-column="level_'.$value_id.'">'.$building_status_to_show.'</td>
                                                            <td data-column="condition_'.$value_id.'">'.$item_building_condition_to_show.'</td>';
                                    if ($type_item == 'lift'){
                                        $data['infoResort'] .=  '<td>'.$length_speed_column.'</td>';
                                        $data['infoResort'] .=  '<td data-column="throughput_'.$value_id.'">'.$throughput.'</td>';
                                        $data['infoResort'] .=  '<td data-column="cost_'.$value_id.'">'.$building_cost.'</td>';
                                    }
                                    else 
                                        $data['infoResort'] .= '<td colspan="3" data-column="deserving_'.$value_id.'">'.$deserving_lift_column.'</td>';
                                    $data['infoResort'] .= '<td style="text-align:left;" data-column="status_'.$value_id.'">'.$item_building_status_to_show.' '.$deserving_lifts_status.'</td>
                                    <td data-column="action_'.$value_id.'">';
                                    $sell_icon = ($button_name === 'sell')
                                        ? '<i class="fa-solid fa-money-bill-wave text-warning"></i>'
                                        : '<i class="fa-solid fa-bomb text-error"></i>';
                                    $data['infoResort'] .= '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('resort')[$button_name.'_tooltip'].'"><a href="?action=sell" class="sell-dialog">'.$sell_icon.'</a></div>';
                                    $data['infoResort'] .= '</td></tr>';  // Closing the table line
                                }
                            }
                        }
                        
                        else $data['infoResort'] .= '<tr><td colspan="9">'.$this->lang->line('home')['there_are_no'].' '.$type_item.' '.$this->lang->line('resort')['built_in_sector'].'.</td></tr>';  // line showing no lift/slope
                        // END SLOPES
                    } 
                    $data['infoResort'] .= '</tbody></table></div>';  // Closing the table
                }
                else 
                    $data['infoResort'] .= '<div class="alert alert-info text-center mt-3">'.$this->lang->line('home')['sector'].$sector_id.' '.$this->lang->line('home')['is_locked'].'</div>';
            }
            if (isset($action) && $action == 'bad_action'){      //Bad action (tried to open/close the wrong slope)
                $data['error'] = 'bad_action';
            }
            else {              // Not error, opening / closing went well
                $data['error'] = $action;
            }
            $data['main_content'] = 'resort';
            $this->load->view('templates/default',$data);
    }
      
    
    
    public function edit_resort_mode_info(){  
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->resort_model->get_player_resort($currentUserID);
                    
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID);
        $data_array = array();
        foreach ($resort_info->result() as $resort_info_array){
            $resort_name = $resort_info_array->resort_name;
            $resort_description = $resort_info_array->resort_description;
            $resort_country = $resort_info_array->resort_country;
        }
        
        
        
        $data_array = '<div class="card card-body">';
        $data_array .= '<div id="create_resort_form">';
        $attributes = array('id' => 'resort_form');
        $data_array .= form_open('resort_controller/update_resort', $attributes);
        $data_array .= form_hidden('updateResort', 'updateResort');              // to show which form we post
        $data_array .= '<div class="mb-3">';
        $data_array .= form_label($this->lang->line('resort')['choose_name'], 'resort_name', array('class' => 'form-label'));
        $id_resort = array('id' => 'resort_name', 'class' => 'input w-full max-w-lg');
        $data_array .= form_input('resort_name', set_value('resort_name', $resort_name), $id_resort);
        $data_array .= '<span class="form-text">'.$this->lang->line('alpha_dash_space_resort').'</span>';
        $data_array .= '<span class="errorTxt"></span>';
        if (isset($resort_error_name))
            $data_array .= $resort_error_name;
        $data_array .= '</div>';
        $data_array .= '<div class="mb-3">';
        $data_array .= form_label($this->lang->line('home')['country_field'], 'resort_country', array('class' => 'form-label'));
        $data_array .= form_input('resort_country', set_value('resort_country', $resort_country), array('id' => 'resort_country', 'class' => 'input w-full max-w-lg'));
        if (isset($resort_error_country))
            $data_array .= '<div class="text-danger small mt-1">'.$resort_error_country.'</div>';
        $data_array .= '</div>';
        $data_array .= '<div class="mb-3">';
        $data_array .= form_label($this->lang->line('resort')['description'], 'resort_description', array('class' => 'form-label'));
        $data = array(
            'name'        => 'resort_description',
            'id'          => 'resort_description',
            'value'       => set_value('resort_description', $resort_description),
            'rows'        => '6',
            'cols'        => '50',
            'class'       => 'textarea w-full max-w-lg'
        );
        $data_array .= form_textarea($data);
        $data_array .= '<div id="chars">500</div>';           // default max size, will be updated when typing characters
        if (isset($resort_error_description))
            echo '<div class="text-danger small mt-1">'.$resort_error_description.'</div>';
        $data_array .= '</div>';

        $data_array .= '<div class="mt-3">';
        $data_button = array(
            'name'        => $this->lang->line('resort')['update'],
            'id'          => 'submit_edit_resort',
            'value'       => set_value('resort_description', $this->lang->line('resort')['update']),
            'class'        => 'btn btn-success resort_update'
        );
        $data_array .= form_submit($data_button); 

        $data_array .= form_close();
    $data_array .= '</div>';
    $data_array .= '</div>';
    $data_array .= '</div>';
    $data_array .= '<div class="clearfix"></div>';
    
    
    
        
        echo json_encode(array('returned' => true, 'data' => $data_array,));
        
    
    }
    
    
    /**
     * sell_item        Sells the item
     *                  For a lift, we refund 10% of the accumulated value (original price + upgrades). This covers the dismantling cost and resell second hand
     *                  For a slope, we don't refund anything as there is no real cost or revenue.
     * 
     */
    public function sell_item(){
        
        $id_item = trim($this->input->post('id_item', TRUE));         // generic ID in the game_created_lifts/slopes table
        $id_created_item = trim($this->input->post('id_created_item', TRUE));         // id_created_item in the game_created_lifts/slopes table
        $currentResortID = trim($this->input->post('currentResortId', TRUE));
        $type = trim($this->input->post('type', TRUE));               // slope or lift
        $friendly_name = trim($this->input->post('friendly_name', TRUE));              
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // Get the generic info of the item, in the game_slopes or game_lifts table
        $item_generic_info_data = $this->item_model->get_generic_item_info_simple($id_item, $type);
        if ($item_generic_info_data->num_rows() > 0) {                      // the generic item exists in the DB (always!)
            $item_generic_info_dataArray = $item_generic_info_data->row();
        }
        
        // check if user has built this lift/slope
        $item_level_info = $this->item_model->check_if_player_has_built_created_item($id_created_item, $currentResortID, $type);  
         
        if ($item_level_info->num_rows() > 0) {                                         // ok to sell/destroy (not in maintenance or construction)
            $item_level_info_Data = $item_level_info->row();                            // we put the result in a array
            switch ($item_level_info_Data->id_status) {
                case 1:         // Status = 1 (open)
                case 2:         // Status = 2 (closed)
                    // We proceed with the selling
                    switch ($type) {
                        case 'lift':        // For a lift, we refund 10% of the accumulated value (original price + upgrades). This covers the dismantling cost and resell second hand
                            $lift_level = $item_level_info_Data->level;     // Get the current level of the lift
                            $item_original_value = 0;
                            for ($level = 1; $level <= $lift_level ; $level++) {      // For each level we add the value to the original price, to calculate the real value
                                $item_original_value = $item_original_value + $item_generic_info_dataArray->base_cost;
                                $resell_value = round($item_original_value*0.1);        // Resell value = 10% of the value
                            }
                            $action_notif_lang = 'sold';
                            $confirmation_message = $this->lang->line('resort')['item_sold'];
                            break;
                        case 'slope':          // For a slope, we don't refund anything as there is no real cost or revenue.
                            $resell_value = 0;
                            $action_notif_lang = 'destroyed';
                            $confirmation_message = $this->lang->line('resort')['item_destroyed'];
                            break;
                    }

                    // Detects if the page was refreshed and prevents multiple entries
                    $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

                    switch ($pageWasRefreshed ) {
                        case false:     // Page not refreshed (ok)
                            $cash_player = $this->users_model->get_cash_player();                     // Get how much cash the player has (in euros)
                            $removeCashQuery = $this->users_model->sell_item($resell_value, $cash_player);

                            switch ($removeCashQuery) {   
                                case true:      //the money for the item has been added to the DB
                                    $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                                    $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                                    // Adds the revenue to the revenue table
                                    $add_revenue_history_query_main_table = add_revenue_stat_table($currentResortID, $resell_value, 'revenue');
                                    $add_revenue_history_query_other_table = add_revenue_stat_table($currentResortID, $resell_value, 'rev_other');
                                    $id_to_sell = $this->item_model->select_id_to_sell($currentResortID, $type, $id_item);   // getting the ID to sell, so we can re-use later
                                    $delete_item = $this->item_model->sell_item_db($currentResortID, $id_created_item, $type);   // DB request to sell the item
        
                                    if ($delete_item) {
                                        $returned = true;
                                        $this->session->set_flashdata('update_token', time());
                                        $null_assigned_item = $this->equipment_model->null_assigned_staff_DB($currentResortID, $id_created_item, $type);   // set NULL to ID and type is assigned staff
                                        $currentUserID = $this->users_model->get_user_id_from_resortID($currentResortID);
                                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')[$type], 'data' => $friendly_name.$this->lang->line('logs')[$action_notif_lang]) );   // Add a log row to the game_player_logs table
                                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')[$type], 'data' => $friendly_name.$this->lang->line('logs')[$action_notif_lang]) );   // Add a log row to the game_player_logs table
                                    }
                                    else {
                                        $confirmation_message = $this->lang->line('resort')['item_not_sold'];
                                        $returned = false;
                                    }
                                    break;
                                case false:         //the money for the item has NOT been added to the DB
                                    
                                    $confirmation_message = $this->lang->line('resort')['item_not_sold'];
                                    $returned = false;
                                    break;
                            }
                            break;
                        case true:      // Page was reloaded. No need to display anything but "data" needs to exist
                            $confirmation_message = '';
                            break;
                    }
                    break;
                case 3:         // Status = 3 (under maintenance)
                case 4:         // Status = 4 (under construction)
                    // The lift is under maintenance, construction or is not built (it is not open or closed)
                    $confirmation_message = $this->lang->line('resort')['lift_unsellable'];
                    $returned = false;
                    break;
            }
        }
        else {  // The lift is under maintenance, construction or is not built (it is not open or closed)
            $confirmation_message = $this->lang->line('resort')['lift_unsellable'];
            $returned = false;
        }
        echo json_encode(array('returned' => $returned, 'message' => $confirmation_message));
        
        //$resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
       // $this->display_resort_info(false, $resultResort, $currentResortID, $data['infoMessage']);
    }
    
    
    
    public function create_history_rows($resort_id){  
        $history_tables = array('rev_marketing', 'rev_achievements', 'affluence', 'reputation', 'prestige', 'cash', 'cost_purchases', 'cost_tournaments', 'cost_salaries', 'cost_upkeep', 'expenses', 'injuries', 'revenue', 'rev_hotel', 'rev_instructor', 'rev_leisure', 'rev_luxury', 'rev_medical', 'rev_off_season', 'rev_other', 'rev_parking', 'rev_rental', 'rev_restaurant', 'rev_skibus', 'rev_skipass', 'snow_level');
        foreach ($history_tables as $type) {
            if ($type == 'snow_level')
                $value = START_SNOW;    
            else
                $value = 0;
            $data = array ('id_resort' => $resort_id, 'date' => gmdate('Y-m-d'), $type => $value );
            $this->resort_model->create_history_stats($type, $data);         // create a row of the history for this resort
        }
    }
    
    public function create_season_row_rows($resort_id){  
        $data = array ('id_resort' => $resort_id, 'season' => '1', 'start_date' => gmdate('Y-m-d H:m:i') );
            $this->resort_model->create_history_stats('season', $data);         // create a row for the first season
    }
    
    
    /**
     * create_resort_preparation Checks all the fields for creating the resort in the DB
     */
    public function create_resort_preparation(){                   
        
        if (isset ($_POST['createResort'])) {       // Only if this form is POSTED
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('resort_name', $this->lang->line('resort')['name_field'], 'trim|required|min_length[3]|max_length[35]|callback_resort_available|callback_different_from_input[name]|callback_alpha_dash_space_resort');
            $this->form_validation->set_rules('resort_country', $this->lang->line('home')['country_field'], 'trim|required|min_length[1]|max_length[45]|callback_different_from_input[country]');
            $this->form_validation->set_rules('resort_description', $this->lang->line('resort')['description_field_error'], 'trim|required|min_length[10]|max_length[500]|callback_different_from_input[description]');

            if ($this->form_validation->run() == FALSE){ // didn't validate (at least one field is incorrect)
                $data['resort_created'] = false;
                $data['resort_error_name'] = form_error('resort_name');
                $data['resort_error_country'] = form_error('resort_country');
                $data['resort_error_description'] = form_error('resort_description');
                $data['main_content'] = 'resort';               // We display the same form again
                $this->load->view('templates/default',$data);
            }
            else {   // all fields are correct
            
                $currentUserID = $this->users_model->get_user_id();
                        
                // Check if not resort created
                $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
                if ($currentResortID == NULL) {
                    if ($query = $this->resort_model->create_resort()) {         // creation of resort passed. We redirect to the resort page
                        $currentResortID = $this->resort_model->get_player_resort($currentUserID);
                        // Calls the function that add rows in the history tables for the newly created resort 
                        $give_access_sector_0 = $this->resort_model->give_access_sector($currentResortID, '0');
                        $give_access_sector_1 = $this->resort_model->give_access_sector($currentResortID, '1');
                        $create_history_rows = $this->create_history_rows($currentResortID); // Creates all the tables for the history/stats
                        $create_season_row_rows = $this->create_season_row_rows($currentResortID);  // Creates the season row with season 1
                        $data_achievement = array (
                            'type' => 'created',    
                            'id_resort' => $currentResortID       
                        );
                        $call_achievements_check = call_achievements_check($data_achievement, 'resort');   // Check if a corresponding achievement should be updated
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $this->lang->line('logs')['resort_created']) );   // Add a log row to the game_player_logs table
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $this->lang->line('logs')['resort_created']) );   // Add a log row to the game_player_logs table
                        redirect('resort_controller/index/true');                // Reloads theresort_controllerr and calls the index function with resort_created=true         

                    }
                    else {                        //creation of resort failed
                      $data['infoResort'] = $this->lang->line('resort')['creation_failed'];
                      $data['main_content'] = 'resort';
                      $this->load->view('templates/default',$data);
                    }
                }
                else  {
                    redirect('resort_controller');
                }
            }
        } else {
            $data['main_content'] = 'resort';
            $this->load->view('templates/default',$data);
        }
    }
    
    
    public function update_resort(){                   
        
        if (isset ($_POST['updateResort'])) {       // Only if this form is POSTED
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('resort_name', $this->lang->line('resort')['name_field'], 'trim|required|min_length[3]|max_length[35]|callback_resort_available|callback_alpha_dash_space_resort');
            $this->form_validation->set_rules('resort_country', $this->lang->line('home')['country_field'], 'trim|required|min_length[1]|max_length[45]');
            $this->form_validation->set_rules('resort_description', $this->lang->line('resort')['description_field_error'], 'trim|required|min_length[10]|max_length[500]');

            if ($this->form_validation->run() == FALSE){ // didn't validate (at least one field is incorrect)
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('returned' => false, 'errors' => validation_errors()));
                } else {
                    $data['user_has_resort'] = false;
                    $data['resort_mode'] = 'edit';
                    $data['resort_error_name'] = form_error('resort_name');
                    $data['resort_error_country'] = form_error('resort_country');
                    $data['resort_error_description'] = form_error('resort_description');
                    $data['infoResort'] = $this->lang->line('resort')['update_failed'];
                    $data['main_content'] = 'resort';               // We display the same form again
                    $this->load->view('templates/default',$data);
                }
            }
            else {   // all fields are correct
                if ($query = $this->resort_model->update_resort()) {         // creation of resort passed. We redirect to the resort page
                    // Calls the function that add rows in the history tables for the newly created resort 
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(array('returned' => true));
                    } else {
                        $data['main_content'] = 'resort';
                        redirect('resort_controller/index/true/resort_updated');                // Reloads the resort_controller and calls the index function with resort_updated=true
                    }
                }
              else {                        //creation of resort failed
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('returned' => false));
                } else {
                    $data['infoResort'] = $this->lang->line('resort')['update_failed'];
                    $data['main_content'] = 'resort';
                    $this->load->view('templates/default',$data);
                }
              }
            }
        } else {
            $data['main_content'] = 'resort';
            $this->load->view('templates/default',$data);
        }
    }
    
    /**
     * resort_available_in_DB Checks if the resort name is available in the database, by checking the name field
     * 
     * @param type $suggested_resort_name Name of the resort entered by the user
     * @return boolean  TRUE if the resort can be created, FALSE if the resort can't be created
     */
    public function resort_available($suggested_resort_name){ //custom callback function to check if username already exists
        $currentUserID = $this->users_model->get_user_id();
        $resort_available = $this->resort_model->resort_available_in_DB($suggested_resort_name, $currentUserID);
        
        if ($resort_available) {
                return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * different_from_input Checks if the entered value is different from the default one (and rejects the form validation)
     * 
     * @param type $input_value         The value present in the field when validating the form
     * @param type $field_name          The field name
     * @return boolean                  FALSE if the name is the same (not valid). TRUE is name is different from default (valid)
     */
    public function different_from_input($input_value, $field_name){
        
        $field = $field_name.'_field';
        $field_desc = $field_name.'_field_error';
        if ($input_value == $this->lang->line('resort')[$field] || $input_value == $this->lang->line('resort')[$field_desc]) {
            return FALSE;
        }
        else {
           return TRUE;
        }
    }
    
    
    /**
     * open_item   Open the item if it is closed
     * 
     * @param type $id_sector       ID of the sector
     * @param type $id_item         ID of the item to open (id_created_lifts / id_created_slopes)
     * @param type $currentResortID   ID of resort    
     * @param type $type_item       Type of item slope/lift
     */
    public function open_item($id_item, $currentResortID, $type_item){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $currentUserID = $this->users_model->get_user_id();
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        if ($type_item == 'slope')
            $is_the_item_built = $this->item_model->check_if_player_has_built_item($id_item, $currentResortID, $type_item);     // check if item is built
        else if ($type_item == 'lift')
            $is_the_item_built = $this->item_model->check_if_player_has_built_item_location($id_item, $currentResortID, $type_item);     // check if item is built
        $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
        if ($is_the_item_built->num_rows() > 0) {                                    // item is built, let's continue
            $item_status = $this->item_model->check_status_item($id_item, $currentResortID, $type_item);     // check if item is open or closed (returned open/closed...)
            if ($item_status->id_status == '2'){   // If closed
                $result = $this->resort_model->open_item_db($currentResortID, 'game_created_'.$type_item.'s', $id_item, 'id_'.$type_item);    // Opens the item in the database. Second param is the table name in DB (e.g game_creates_slopes)
                
                if ($type_item == 'lift') {
                    $get_item_sector = $this->item_model->get_lift_sector_group_location($currentResortID, $id_item);
                    $get_item_sector_result = $get_item_sector->row();
                    $id_slope_type = '';
                }
                else if ($type_item == 'slope') {
                    $get_item_sector = $this->item_model->get_slope_sector($currentResortID, $id_item);
                    $get_item_sector_result = $get_item_sector->row();
                    $id_slope_type = $get_item_sector_result->slope_type;
                }
                $id_sector = $get_item_sector_result->id_sector;
                
                
                $data_achievement = array (
                    'id_sector' => $id_sector,
                    'id_resort' => $currentResortID,
                    'type' => $type_item
                );
                $data_achievement_any_sector = array (
                    'id_sector' => '*',
                    'id_resort' => $currentResortID,
                    'type' => $type_item
                );
                $data_achievement_slope_type = array (
                    'id_resort' => $currentResortID,
                    'id_slope_type' => $id_slope_type,       
                    'quantity' => '1'
                );
                $call_achievements_check = call_achievements_check($data_achievement, 'open');
                $call_achievements_check = call_achievements_check($data_achievement_any_sector, 'open');
                $call_achievements_check = call_achievements_check($data_achievement_slope_type, 'open_slope_type');
                $data['user_has_resort'] = true;
                $this->display_resort_info(true, $resultResort, $currentResortID, 'item_opened');
            }
            else {      // Already open
                $this->display_resort_info(true, $resultResort, $currentResortID, 'bad_action');
            }
        }
        else {      // not built
            $this->display_resort_info(true, $resultResort, $currentResortID, 'bad_action');
        }
    }
    
    /**
     * close_item   Close the item if it is opened
     * 
     * @param type $id_item         ID of the item to close (id_created_lifts / id_created_slopes)
     * @param type $currentResortID   ID of resort    
     * @param type $type_item       Type of item slope/lift
     */
    public function close_item($id_item, $currentResortID, $type_item){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        $currentUserID = $this->users_model->get_user_id();
        
        if ($type_item == 'lift') {
            $get_item_sector = $this->item_model->get_lift_sector_group_location($currentResortID, $id_item);
        }
        else if ($type_item == 'slope') {
            $get_item_sector = $this->item_model->get_slope_sector($currentResortID, $id_item);
        }
        $get_item_sector_result = $get_item_sector->row();
        $id_sector = $get_item_sector_result->id_sector;
                
        if ($type_item == 'slope')
            $is_the_item_built = $this->item_model->check_if_player_has_built_item($id_item, $currentResortID, $type_item);     // check if item is built
        else if ($type_item == 'lift')
            $is_the_item_built = $this->item_model->check_if_player_has_built_item_location($id_item, $currentResortID, $type_item);     // check if item is built
        
        $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)
        if ($is_the_item_built->num_rows() > 0) {                                          // item is built, let's continue
            $item_status = $this->item_model->check_status_item($id_item, $currentResortID, $type_item);     // check if item is open or closed (returned open/closed...)
            
            if ($item_status->id_status == '1'){       // If opened
                $result = $this->resort_model->close_item_db($currentResortID, 'game_created_'.$type_item.'s', $id_item, 'id_'.$type_item);    // Closes the item in the database. Second param is the table name in DB (e.g game_creates_slopes)
                $data_achievement = array (
                        'id_sector' => $id_sector,
                        'id_resort' => $currentResortID,
                        'type' => $type_item
                );
                $data_achievement_any_sector = array (
                        'id_sector' => '*',
                        'id_resort' => $currentResortID,
                        'type' => $type_item
                );
                $call_achievements_check = call_achievements_check($data_achievement, 'close');
                $call_achievements_check = call_achievements_check($data_achievement_any_sector, 'close');
                $data['user_has_resort'] = true;
                $this->display_resort_info(true, $resultResort, $currentResortID ,'item_closed');
            }
            else {          // Is already closed
            $this->display_resort_info(true, $resultResort, $currentResortID, 'bad_action');
            }        
        }
        else {      // Slope not built
            $this->display_resort_info(true, $resultResort, $currentResortID, 'bad_action');
        }
    }
    
    /**
     * count_deserving_lifts    Returns how many lifts are deserving the slope. Make a loop for the 3 positions
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $id_slope        ID of the slope
     * @return type                 Number of deserving lifts
     */
    public function count_deserving_lifts($currentResortID, $id_slope){
      $num_deserving_lifts = 0;
      $num_deserving_lifts_temp = 0;

      for($count=1; $count<=3; $count++){
          $num_deserving_lifts_temp = $this->resort_model->count_deserving_lifts_db($currentResortID, $id_slope, $count);
          $num_deserving_lifts = $num_deserving_lifts + $num_deserving_lifts_temp;
      }
      return $num_deserving_lifts;
    }
    
    /**
     * count_assigned_staff    Returns how many employees are deserving the item. 
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $id_lift        ID of the item
     * @return type                 Number of deserving employees
     */
    public function count_assigned_staff($currentResortID, $id_item){

          $num_deserving_staff = $this->resort_model->count_assigned_staff_db($currentResortID, $id_item);
    
      return $num_deserving_staff;
    }
    
    function alpha_dash_space_resort($str)    {
        return ( ! preg_match("/^([a-z0-9\-\s\_À-ÿ])+$/i", $str)) ? FALSE : TRUE;
    }

    /**
     * collect_idle_income  Transfers any accumulated idle income to the player's cash.
     *
     * Called each time the player's resort page is loaded.  If the nightly cron
     * has placed income in game_resorts.pending_idle_income this method:
     *   1. Adds the amount to the resort's cash balance.
     *   2. Resets pending_idle_income to 0.
     *   3. Shows a flash-data notification so the player sees the earnings.
     *   4. Writes a log entry for the player's activity log.
     *
     * @param int $currentResortID  The current resort ID
     */
    protected function collect_idle_income($currentResortID) {
        $resort_row = $this->db
            ->select('pending_idle_income')
            ->from('game_resorts')
            ->where('id_resort', $currentResortID)
            ->get()
            ->row();

        if (!$resort_row) {
            return;
        }

        $pending = (int)$resort_row->pending_idle_income;
        if ($pending <= 0) {
            return;
        }

        // Add idle income to cash and clear the pending balance
        $this->db->trans_start();
        $this->db->set('cash', 'cash + ' . $pending, FALSE);
        $this->db->set('pending_idle_income', 0);
        $this->db->where('id_resort', $currentResortID);
        $this->db->update('game_resorts');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return;
        }

        // Update the cash in session so the sidebar reflects the new balance
        $current_cash = $this->users_model->get_cash_player();
        $this->session->set_userdata('cash', $current_cash);

        // Show a flash notification on the resort page
        $formatted_amount = number_format($pending, 0, ',', ' ');
        $idle_msg = '<div class="alert alert-success text-center">'
            . '<i class="fa-solid fa-piggy-bank me-1"></i>'
            . ($this->lang->line('logs')['idle_income_collected'] ?? 'Your resort earned while you were away: +')
            . ' <strong>' . $formatted_amount . ' €</strong>'
            . '</div>';
        $this->session->set_flashdata('idle_income_msg', $idle_msg);

        // Write to the player's activity log
        $currentUserID = $this->users_model->get_user_id();
        $log_data = ($this->lang->line('logs')['idle_income_collected'] ?? 'Your resort earned while you were away: +')
            . ' ' . $formatted_amount . ' €';
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => ($this->lang->line('logs')['idle_income'] ?? 'Idle Income'),
            'data'      => $log_data,
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => ($this->lang->line('logs')['idle_income'] ?? 'Idle Income'),
            'data'      => $log_data,
        ]);
    }

}