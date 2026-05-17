<?php
/**
 * 
 */
class Resort_map_controller extends CI_Controller{
    
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
        //$ci->lang->load('staff',$siteLang);
        $ci->lang->load('lift',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->is_logged_in;
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('resort_model');
        $this->load->model('equipment_model');
        $this->load->model('item_model');
        $this->load->model('users_model');
        $this->load->model('building_model');
        $this->load->model('achievements_model');
        $this->load->model('logs_model');
    }
    
    /**
     * index Main function that displays the resort info or the create resort form
     * 
     * @param type $resort_created If the resort has just been created = true. If not, we set to empty
     */
    public function index(){
        $currentUserID = $this->users_model->get_user_id();
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
            $data['infoMessage'] = '';
            $data['build_button_status'] = '';
            $data['build_slope_button_status'] = '';
            $data['build_lift_button_status'] = '';
            
            $lifts_under_construction = count_ongoing_building_items('lift', '4');  // type = lift and "4" is Under construction status
            $lifts_under_maintenance = count_ongoing_building_items('lift', '3');  // type = lift and "3" is Under maintenance status
            if ($lifts_under_construction != 0 || $lifts_under_maintenance != 0) {    // if there is no ongoing lift under construction or maintenance
               $data['infoMessage'] .= $this->lang->line('lift')['ongoing_construction_lift']; 
               $data['build_lift_button_status'] = 'disabled';
            }
            $slopes_under_construction = count_ongoing_building_items('slope', '4');  // type = lift and "4" is Under construction status
            if ($slopes_under_construction != 0) {    // if there is no ongoing lift under construction or maintenance
               $data['infoMessage'] .= $this->lang->line('slope')['ongoing_construction_slope']; 
               $data['build_slope_button_status'] = 'disabled';
            }
            
            if (($lifts_under_construction != 0 || $lifts_under_maintenance != 0) && $slopes_under_construction != 0) {
                $data['build_button_status'] = 'disabled';
            }
            
            // Pass accessible sector list so JS can populate the sector selector for the draw tool
            $data['accessible_sectors'] = $this->resort_model->get_sector_access($currentResortID);

            // Map type: load image URL and default view for this resort's map style
            $id_map_type = $this->resort_model->get_resort_map_type($currentResortID);
            $map_type    = $this->resort_model->get_map_type($id_map_type);
            if (!$map_type) {
                $map_type = (object)['map_image' => 'img/images/map.jpg', 'default_center_x' => 300, 'default_center_y' => 170, 'default_zoom' => 1];
            }
            $data['map_type'] = $map_type;

            $data['infoMessage'] .= '<div class="alert alert-info">' . $this->lang->line('resort_map')['sector_6_building'] . '</div>';
                                     
            $data['main_content'] = 'resort_map';                                    // display the Slope view with no extra element (simply displaying info)
            $this->load->view('templates/default',$data); 
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        } 
    }

    /**
     * lift_building Redirects to the main resort map page (dedicated lift building page removed)
     */
    public function lift_building() {
        redirect('resort_map_controller');
    }

    /**
     * slope_building Redirects to the main resort map page (dedicated slope building page removed)
     */
    public function slope_building() {
        redirect('resort_map_controller');
    }

    public function show_lift_types()    {
        $id_lift_types = $this->item_model->get_existing_id_lift_types_DB();  // get all the lift types
        $lift_types_button_row = '';
        $currentUserID = $this->users_model->get_user_id();
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        foreach ($id_lift_types->result() as $id_lift_types_array) {
            $lift_types = $this->item_model->get_lift_types_DB($id_lift_types_array->lift_type);
            $column_name = 'name_'.$player_preferred_lang;
            $lift_types_row = $lift_types->row();
            $lift_type_name = $lift_types_row->$column_name;
            $lift_type_id = $id_lift_types_array->lift_type;
            $lift_types_button_row .= '<button type="button" class="btn btn-secondary button_menu lift_type" id="'.$lift_type_id.'" >'.$lift_type_name.'</button>';
        }
        $lift_types_button = $lift_types_button_row;
        $data_returned = array('returned' => true, 'data' => $lift_types_button);
        echo json_encode($data_returned);
        
    }
    
    public function show_slope_difficulty(){
        $currentUserID = $this->users_model->get_user_id();
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        
        $id_slope_difficulty = $this->item_model->get_difficulty_slope();  // get all the slope difficulty
        $slope_difficulty_button_row = '';
        foreach ($id_slope_difficulty->result() as $id_slope_difficulty_array) {
            $column_name = 'name_'.$player_preferred_lang;
            $slope_difficulty_name = $id_slope_difficulty_array->$column_name;
            $slope_difficulty_id = $id_slope_difficulty_array->id_difficulty;
            $slope_difficulty_button_row .= '<button type="button" class="btn btn-secondary button_menu slope_difficulty" id="difficulty-'.$slope_difficulty_id.'" >'.$slope_difficulty_name.'</button>';
        }
        $data_returned = array('returned' => true, 'data' => $slope_difficulty_button_row);
        echo json_encode($data_returned);
    }

    public function show_slopes() {
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $allowed_langs = array('english', 'french');
        $column_name = in_array($player_preferred_lang, $allowed_langs) ? 'name_'.$player_preferred_lang : 'name_english';

        $where_statement = '';
        $sector_access_data = $this->resort_model->get_sector_access($currentResortID);
        foreach ($sector_access_data as $sector_access_array) {
            if ($sector_access_array != '') {
                $where_statement .= 'id_sector = '.(int)$sector_access_array.' OR ';
            }
        }
        if ($where_statement == '') {
            echo json_encode(array('returned' => false));
            return;
        }
        $where_statement = substr($where_statement, 0, -4);
        $where_statement = '('.$where_statement.')';

        $id_slope_type = $this->input->post('id_slope_type', TRUE);
        if ($id_slope_type != '' && $id_slope_type != null) {
            if (!is_numeric($id_slope_type)) {
                echo json_encode(array('returned' => false));
                return;
            }
            $where_statement .= ' AND slope_type = '.(int)$id_slope_type;
        }
        $id_difficulty = $this->input->post('id_difficulty', TRUE);
        if ($id_difficulty != '' && $id_difficulty != null) {
            if (!is_numeric($id_difficulty)) {
                echo json_encode(array('returned' => false));
                return;
            }
            $where_statement .= ' AND id_difficulty = '.(int)$id_difficulty;
        }

        $slopes = $this->item_model->get_generic_slope_info_sector($where_statement);
        $slopes_button_row = '';
        foreach ($slopes->result() as $slope) {
            $slope_name = $slope->$column_name;
            $slope_id   = (int)$slope->id_slope;
            $slopes_button_row .= '<button type="button" class="btn btn-secondary button_menu slope_item"'
                .' id="slope-'.$slope_id.'"'
                .' data-id_slope="'.$slope_id.'"'
                .' data-length="'.(int)$slope->length.'"'
                .' data-slope_type="'.(int)$slope->slope_type.'">'
                .htmlspecialchars($slope_name, ENT_QUOTES, 'UTF-8').'</button>';
        }
        $data_returned = array('returned' => true, 'data' => $slopes_button_row);
        echo json_encode($data_returned);
    }
    
    
    
    
    
    public function show_slope_types(){
        $currentUserID = $this->users_model->get_user_id();
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        
        // check if player has unlocked achievement 124 (unlock new slope types
        // to do
        $ach_uncloked = $this->building_model->get_building_ach_unlocked2($currentUserID, 124);   // Checks if the player has uncloked the achievement for extra slope types (ID 124)
        $row_ach_uncloked = $ach_uncloked->row();
        //var_dump($ach_uncloked);
        //var_dump($row_ach_uncloked);
        //echo $this->db->last_query();
        
        $column_name = 'name_'.$player_preferred_lang;

        $class_slope_types_enabled = 'btn btn-secondary button_menu slope_type';
        $default_title = '';
        $id_slope_type = $this->item_model->get_slope_types();  // get all the slope types
                
        if ($ach_uncloked->num_rows() == 1) {  // achievement at least started (or completed)
            $claimed = $row_ach_uncloked->claimed;
            $progress = $row_ach_uncloked->progress;
            if ($progress == NULL)  // Achievement not started
                $progress = 0;
            if ($progress == 100 && $claimed == 1) {
                $class_slope_types = 'btn btn-secondary button_menu slope_type';
                $title_other_types = $default_title;
            }
            elseif ($progress != 100) { // Achievement started but not completed
                $class_slope_types = 'btn btn-secondary button_menu disabled';
                $title_other_types = $this->lang->line('resort')['slope_type_locked'];
            }
            else {  // Achievement completed but not claimed
                $class_slope_types = 'btn btn-secondary button_menu disabled';
                $title_other_types = $this->lang->line('resort')['slope_type_not_claimed'];
            }                
        }
        else {
            $class_slope_types = 'btn btn-secondary button_menu disabled';
            $title_other_types = $this->lang->line('resort')['slope_type_locked'];
        }
        
        
        //var_dump($id_slope_type);
        $slope_type_button_row = '';
        foreach ($id_slope_type->result() as $id_slope_type_array) {
            $slope_type_name = $id_slope_type_array->$column_name;
            $slope_type_id = $id_slope_type_array->id_slope_types;
            
            if ($slope_type_id == 1) {
                $class_slope_types_to_use = $class_slope_types_enabled;
                $title_to_use = $default_title;
            }
            else {
                $class_slope_types_to_use = $class_slope_types;
                $title_to_use = $title_other_types;
            }
            
            $slope_type_button_row .= '<button type="button" title="'.$title_to_use.'" class="'.$class_slope_types_to_use.'" id="type-'.$slope_type_id.'" >'.$slope_type_name.'</button>';
        }
        $data_returned = array('returned' => true, 'data' => $slope_type_button_row);
        echo json_encode($data_returned);
    }
    
    
    public function show_grip_types() {
        $id_lift_type = $this->input->post('id_lift_type', TRUE);
        $id_grip_types = $this->item_model->get_existing_id_grip_types_DB($id_lift_type);  // get all the grip types for selected lift_type
        $grip_types_button_row = '';
        $currentUserID = $this->users_model->get_user_id();
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        foreach ($id_grip_types->result() as $id_grip_types_array) {
            $grip_types = $this->item_model->get_grip_types_DB($id_grip_types_array->grip_type);
            $column_name = 'name_'.$player_preferred_lang;
            $grip_types_row = $grip_types->row();
            $grip_type_name = $grip_types_row->$column_name;
            $grip_type_id = $id_grip_types_array->grip_type;
            $grip_types_button_row .= '<button type="button" class="btn btn-secondary button_menu grip_type" data-id_grip_type="'.$grip_type_id.'" id="'.$id_lift_type.'" >'.$grip_type_name.'</button>';
        }
        //$grip_types_button = '<div class="btn-group-vertical" role="group" aria-label="Grip types">';
        $grip_types_button = $grip_types_button_row;
        //$grip_types_button .= '</div>';
        $data_returned = array('returned' => true, 'data' => $grip_types_button);
        echo json_encode($data_returned);
    }
    
    public function show_capacity() {
        $id_lift_type = $this->input->post('id_lift_type', TRUE);
        $id_grip_type = $this->input->post('id_grip_type', TRUE);
        $capacity = $this->item_model->get_capacity_DB($id_lift_type, $id_grip_type);  // get all the capacities for selected lift_type, for level 1
        //echo $this->db->last_query();
        $res = $capacity->result();
        //var_dump($res);
        $capacity_button_row = '';
        $currentUserID = $this->users_model->get_user_id();
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        foreach ($res as $capacity_array) {
            //$capacity_row = $capacity->row();
            $capacity_result = $capacity_array->capacity;
          //  echo '$capacity_result: '.$capacity_result;
            $capacity_button_row .= '<button type="button" class="btn btn-secondary button_menu capacity" data-id_grip_type="'.$id_grip_type.'" data-capacity="'.$capacity_result.'" id="'.$id_lift_type.'" >'.$capacity_result.' '.$this->lang->line('home')['seats'].'</button>';
        }
        //$capacity_button = '<div class="btn-group-vertical" role="group" aria-label="Grip types">';
        $capacity_button = $capacity_button_row;
        //$capacity_button .= '</div>';
                  
        $data_returned = array('returned' => true, 'data' => $capacity_button);
        echo json_encode($data_returned);
    }
    
    public function show_lift_info() {
        $id_lift_type = $this->input->post('id_lift_type', TRUE);
        $id_grip_type = $this->input->post('id_grip_type', TRUE);
        $capacity = $this->input->post('capacity', TRUE);
        $lift_info = $this->item_model->get_generic_lift_info($id_lift_type, $id_grip_type, $capacity);  // get all the details for the generic lift matching the options and level 1
        $lift_info_fields = [];
        $currentUserID = $this->users_model->get_user_id();
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $lift_info_row = $lift_info->row();
            $lift_info_result = array (
                'throughput' => $lift_info_row->throughput,
                'base_cost' => $lift_info_row->base_cost,
                'meter_cost' => $lift_info_row->meter_cost,
                'building_time' => $lift_info_row->building_time/ACCELERATOR_FACTOR,
                'reputation' => $lift_info_row->reputation,
                'level' => $lift_info_row->level);
            $id_group = $lift_info_row->id_group;
        
        for ($level = 1; $level <= 3; $level++) {       // For each level
            //$id_lift_to_check = $id_lift + $level -1;   // Since the ID in the DB is increased for each level (autoincrement), we need to adjust it's value here. Each level of a lift should have a following ID
            $genericLift_specific_level = $this->item_model->get_generic_item_info_for_level($id_group, 'lift', $level); 
            if ($genericLift_specific_level->num_rows() > 0) {                       
                $genericLift_specific_level_data = $genericLift_specific_level->row();
                $building_time[$level] = $genericLift_specific_level_data->building_time/ACCELERATOR_FACTOR;
                $base_cost[$level] = $genericLift_specific_level_data->base_cost;
                $meter_cost[$level] = $genericLift_specific_level_data->meter_cost;
                $capacity_table[$level] = $genericLift_specific_level_data->capacity;
                $throughput[$level] = $genericLift_specific_level_data->throughput;
                $speed[$level] = $genericLift_specific_level_data->speed;
                $reputation[$level] = $genericLift_specific_level_data->reputation;
            }
        }    
                
        $table_lift = '<table class="table table-responsive building_6th">
                        <thead>';
        $table_lift .= '<tr><th></th><th>'.$this->lang->line('home')['level'].' 1</th><th>'.$this->lang->line('home')['level'].' 2</th><th>'.$this->lang->line('home')['level'].' 3</th></tr></thead>';
        $table_lift .= '<tr><th>'.$this->lang->line('lift')['length_speed_column'].'</th><td>'.$speed[1].' '.$this->lang->line('lift')['speed_unit'].'</td><td>'.$speed[2].' '.$this->lang->line('lift')['speed_unit'].'</td><td>'.$speed[3].' '.$this->lang->line('lift')['speed_unit'].'</td>';
        $table_lift .= '<tr><th>'.$this->lang->line('lift')['capacity_seats'].'</th><td>'.$capacity_table[1].'</td><td>'.$capacity_table[2].'</td><td>'.$capacity_table[3].'</td>';
        $table_lift .= '<tr><th>'.$this->lang->line('lift')['throughput'].'</th><td>'.$throughput[1].' s/h</td><td>'.$throughput[2].' s/h</td><td>'.$throughput[3].' s/h</td>';
        $table_lift .= '<tr><th>'.$this->lang->line('home')['base_cost'].'</th><td>'.number_format($base_cost[1], 0, ',', ' ').' €</td><td>'.number_format($base_cost[2], 0, ',', ' ').' €</td><td>'.number_format($base_cost[3], 0, ',', ' ').' €</td>';
        $table_lift .= '<tr><th>'.$this->lang->line('home')['meter_cost'].'</th><td>'.number_format($meter_cost[1], 0, ',', ' ').' €</td><td>'.number_format($meter_cost[2], 0, ',', ' ').' €</td><td>'.number_format($meter_cost[3], 0, ',', ' ').' €</td>';
        $table_lift .= '</table>';
        /*
        $result_info = '<div class="col-md-5" role="group" aria-label="Lifts info">';
        $result_info .= '<strong>Selected segment: </strong><span id="id_group_location"></span><br>';
        $result_info .= '<strong>Selected length: </strong><span id="location_length"></span> meters<br>';
        $result_info .= '<strong>Total price: </strong><span id="total_price"></span> €</div>';*/
        
        $data_returned = array('returned' => true, 'data' => $lift_info_result, 'table' => $table_lift);
        echo json_encode($data_returned);
    }
    
    
    public function show_slope_info() {
        $id_slope_type = $this->input->post('id_slope_type', TRUE);
        if (!is_numeric($id_slope_type) || $id_slope_type < 1 || $id_slope_type > count(SLOPE_METER_PRICE)) {
            echo json_encode(array('returned' => false));
            return;
        }
        $slope_meter_price         = SLOPE_METER_PRICE[$id_slope_type - 1];
        $slope_meter_building_time = SLOPE_METER_BUILDING_TIME[$id_slope_type - 1];

        $table_slope = '<table class="table table-responsive building_6th"><thead>';
        $table_slope .= '<tr><th></th><th>'.$this->lang->line('home')['big_slopes'].'</th></tr></thead>';
        $table_slope .= '<tr><th>'.$this->lang->line('home')['meter_cost'].'</th><td>'.number_format($slope_meter_price, 0, ',', ' ').' €</td></tr>';
        $table_slope .= '<tr><th>'.$this->lang->line('home')['building_time'].'</th><td>'.$slope_meter_building_time.' s/m</td></tr>';
        $table_slope .= '</table>';

        $data_returned = array(
            'returned' => true,
            'table'    => $table_slope,
        );
        echo json_encode($data_returned);
    }
    
    
    public function get_all_locations() {
        $all_locations = $this->resort_model->get_all_locations();  
        $all_locations_result = $all_locations->result();
        $data_returned = array('returned' => true, 'data' => $all_locations_result);
        echo json_encode($data_returned);
    }


    public function get_sectors_map() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_map_type     = $this->resort_model->get_resort_map_type($currentResortID);

        // game_map_types may not exist yet on older installs — fall back gracefully
        if ($this->db->table_exists('game_map_types') && $this->db->field_exists('id_map_type', 'game_sectors')) {
            $all_sectors = $this->resort_model->get_sectors_by_map_type($id_map_type);
        } else {
            $all_sectors = $this->resort_model->get_all_sectors();
        }
        if ($all_sectors->num_rows() > 0) {
            $path       = array();
            $style      = array();
            $id_sector  = array();
            foreach ($all_sectors->result() as $sector) {
                if ($sector->path != null && $sector->path != '') {
                    $path[]      = $sector->path;
                    $style[]     = array(
                        'color'       => $sector->color,
                        'weight'      => 2,
                        'opacity'     => 0.8,
                        'fillOpacity' => 0.15,
                    );
                    $id_sector[] = $sector->id_sector;
                }
            }
            $data_returned = array(
                'returned'  => true,
                'path'      => $path,
                'style'     => $style,
                'id_sector' => $id_sector,
            );
        } else {
            $data_returned = array('returned' => false);
        }
        echo json_encode($data_returned);
    }


    public function get_slopes_map()    {
        $slope_mode = $this->input->post('slope_mode', TRUE);
        $id_slope_type = $this->input->post('id_slope_type', TRUE);
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
        $previous_id_group = '';
        $debug = '';
        
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        //$column_name = 'name_'.$player_preferred_lang;
        $column_name = 'name_english'; // Because DB is in english
        $slope_built = $this->item_model->check_if_player_has_built_item_sector($currentResortID, 'slope');
        if ($slope_built->num_rows() > 0) {      // Player has at least one slope built
            foreach ($slope_built->result() as $slope_array) {        // Get the locations of the slopes
                $difficulty_name = $this->item_model->get_difficulty_name_slope($slope_array->id_difficulty, $column_name); // Get the difficulty name for color style
                $array_of_difficulty[] = array ('id_slope' => $slope_array->id_slope, 'difficulty' => $difficulty_name->$column_name);
                $array_of_built_id_slopes[] = $slope_array->id_slope;               // id_slope of all created slopes
                $array_of_custom_names[] = array ('id_slope' => $slope_array->id_slope, 'custom_name' => $slope_array->custom_name); // Array containing custom names of the built lifts. Size of array: number of built lifts
                $array_of_status[] = array ('id_slope' => $slope_array->id_slope, 'id_status' => $slope_array->id_status); // Array containing custom names of the built lifts. Size of array: number of built lifts
            }
        }
        else {
            $array_of_difficulty[] = '';
            $array_of_built_id_slopes[] = '';           
            $array_of_custom_names[] = '';
            $array_of_status[] = '' ;
        }
  
        $where_statement = '';
        $sector_access_data = $this->resort_model->get_sector_access($currentResortID);
        foreach ($sector_access_data as $sector_access_array) {        // Get the locations of the lifts
            if ($sector_access_array != '') {
                $where_statement .= 'id_sector = '.(int)$sector_access_array.' OR ';
            }
        }
        if ($where_statement === '') {
            echo json_encode(array('returned' => false, 'debug' => $debug));
            return;
        }
        $where_statement = substr($where_statement, 0, -4);  // remove trailing ' OR ' (4 characters)
        $where_statement = '('.$where_statement.')';
        
        // Include player-drawn slopes via $currentResortID
        $all_generic_slope_info = $this->item_model->get_generic_slope_info_sector($where_statement, $currentResortID); // Get details about all slopes (including path and locations)
        $all_generic_slope_info_result = $all_generic_slope_info->result();  // Result of all locations

        $debug .= '$id_slope_type: '.$id_slope_type.' ';
        $debug .= $this->db->last_query();
        
        if ($all_generic_slope_info->num_rows() > 0 ) {
            foreach ($all_generic_slope_info_result as $all_generic_slope_info_array) {        // Get the locations of the slopes 
                $id_slope_query = $all_generic_slope_info_array->id_slope;
                $slope_type_query[] = $all_generic_slope_info_array->slope_type;

                $length[] = $all_generic_slope_info_array->length;
                $path[] = $all_generic_slope_info_array->path;
                $current_difficulty = '';
                foreach($array_of_difficulty as $key => $difficulties){
                    if ($slope_built->num_rows() > 0) {
                        if ( $difficulties['id_slope'] === $id_slope_query ) {
                           $current_difficulty = $difficulties['difficulty'];
                        }
                    }
                    else
                        $current_difficulty = '';
                }

                if ($slope_mode == 'true' && in_array($id_slope_query, $array_of_built_id_slopes)) {   //building new slope => Display built slopes is smaller colored line
                    $style[] = array(           
                        'color' => $current_difficulty,
                        'smoothFactor' => '0',
                        'weight' => '2',
                        'opacity' => '0.7',
                        'dashArray' => '5, 0');
                    foreach($array_of_custom_names as $key => $custom_names){
                        if ( $custom_names['id_slope'] === $id_slope_query )
                           $current_name[] = $custom_names['custom_name'];
                    }
                    foreach($array_of_status as $key => $statuses){
                        if ( $statuses['id_slope'] === $id_slope_query )
                           $current_status[] = $statuses['id_status'];
                        //else
                            //$current_status[] = '';
                    }
                    $segment [] = '';
                }  
                else if (($slope_mode == 'true' || $slope_mode == 1) && !in_array($id_slope_query, $array_of_built_id_slopes)) {      // In building slope mode
                    //$debug .= '$id_slope_type: '.$id_slope_type.' - $slope_type_query: '.$all_generic_slope_info_array->slope_type;
                    //$debug .= var_dump($slope_type_query);
                    $style[] = array(           
                    'color' => 'purple',
                    'smoothFactor' => '0',
                    'weight' => '8',
                    'opacity' => '1',
                    //'id_slope_type' => $all_generic_slope_info_array->slope_type,
                    'dashArray' => '5, 10');
                    $current_name [] = '';
                    $current_status [] = '';
                    $segment [] = $all_generic_slope_info_array->start_location.'-'.$all_generic_slope_info_array->end_location;
                    
                }
                else{
                    $name_populated = 0;
                    foreach($array_of_custom_names as $key => $custom_names){
                        if ($slope_built->num_rows() > 0) {
                            if ( $custom_names['id_slope'] === $id_slope_query) {
                               $current_name[] = $custom_names['custom_name'];
                               $name_populated = 1;         // Means we have entered a current_name and no need to add ""
                            }
                        }
                        else {
                            $current_name[] = '';
                            $current_status[] = '';
                        }
                    }
                    if ($name_populated == 0) { // Means we have not entered a current_name (slope not built), so we need to add an empty value to keep the size of the array correct
                        $current_name[] = '';
                        $current_status[] = '';
                    }
                    //var_dump($array_of_status);
                    foreach($array_of_status as $key => $statuses){
                        if ($slope_built->num_rows() > 0) {
                            if ( $statuses['id_slope'] === $id_slope_query ) {
                                $current_status[] = $statuses['id_status'];
                                if ($statuses['id_status'] == '4')
                                    $dashArray = '5, 5';
                                else
                                    $dashArray = '5, 0';
                            }
                            else
                                $dashArray = '2, 2';
                        }
                        else  {
                            $dashArray = '5, 0';
                            $current_status[] = '';
                        }
                        // echo ' -- id_slope: '.$statuses['id_slope'].' -- $status: '.$statuses['id_status'];
                    }
                    $segment [] = $all_generic_slope_info_array->start_location.'-'.$all_generic_slope_info_array->end_location;
                    $style[] = array(           // Defines the style of the already built slopes of the resort
                        'color' => $current_difficulty,
                        'smoothFactor' => '0',
                        'weight' => '4',
                        'opacity' => '1',
                        //'id_slope_type' => $all_generic_slope_info_array->slope_type,
                        'dashArray' => $dashArray);
                }
                $id_slope[] = $id_slope_query;
            }
            //var_dump($current_status);
                   
            $array_data = array (
                'returned' => true,
                'style' => $style,
                'slope_type' => $slope_type_query,
                'path' => $path,
                'length' => $length, 
                'custom_name' => $current_name,
                'segment' => $segment,
                'id_slope' => $id_slope,
                'id_status' => $current_status,
                'debug' => $debug
            );     
        }
        else {
            $array_data = array (
                'returned' => false,
                'debug' => $debug
            );
        }

        // Append custom slopes drawn by this player (open, path set)
        $custom_slopes_query = $this->db
            ->where('id_resort', $currentResortID)
            ->where('is_custom', 1)
            ->where('id_status', 1)
            ->where('path IS NOT NULL', null, false)
            ->get('game_created_slopes');

        if ($custom_slopes_query->num_rows() > 0) {
            $diff_color_map = [1 => 'Green', 2 => 'Blue', 3 => 'Red', 4 => 'Black'];

            if (!isset($array_data['path'])) {
                // No regular slopes were returned; bootstrap the arrays
                $array_data = [
                    'returned'   => true,
                    'style'      => [],
                    'slope_type' => [],
                    'path'       => [],
                    'length'     => [],
                    'custom_name'=> [],
                    'segment'    => [],
                    'id_slope'   => [],
                    'id_status'  => [],
                    'debug'      => $debug,
                ];
            } else {
                $array_data['returned'] = true;
            }

            foreach ($custom_slopes_query->result() as $cs) {
                $diff_id    = (int) $cs->id_difficulty;
                $diff_color = isset($diff_color_map[$diff_id]) ? $diff_color_map[$diff_id] : 'Blue';
                $cs_status  = (int) $cs->id_status;
                $dash       = ($cs_status == 4) ? '5, 5' : '5, 0';

                $array_data['path'][]       = $cs->path;
                $array_data['slope_type'][] = (int) $cs->custom_slope_type;
                $array_data['length'][]     = (int) $cs->custom_length;
                $array_data['custom_name'][]= $cs->custom_name;
                $array_data['segment'][]    = '';
                $array_data['id_slope'][]   = 0;
                $array_data['id_status'][]  = $cs_status;
                $array_data['style'][]      = [
                    'color'        => $diff_color,
                    'smoothFactor' => '0',
                    'weight'       => '4',
                    'opacity'      => '1',
                    'dashArray'    => $dash,
                ];
            }
        }
        
        echo json_encode($array_data);
   }
    
    public function get_lifts_map(){
        
        $lift_mode = $this->input->post('lift_mode', TRUE); // If true, it's time to prepare the area for building (show available locations...)
        $id_lift_type = $this->input->post('id_lift_type', TRUE);       // ID of the Selected lift_type
        $id_grip_type = $this->input->post('id_grip_type', TRUE);       // ID of the Selected Grip type
        $capacity = $this->input->post('capacity', TRUE);               // Selected Capacity
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
        $previous_id_group = '';            // Used to detect first loop
        $lift_built = $this->item_model->check_if_player_has_built_item_sector($currentResortID, 'lift');   // Checks if the player has already built a lift
        $lift_info_query = $this->item_model->get_generic_lift_info($id_lift_type, $id_grip_type, $capacity); // Get the generic info for the built lifts 
        if ($lift_built->num_rows() > 0) {              // Player has at least one lift built
            foreach ($lift_built->result() as $lift_array) {        // For each built lift
                if ($lift_mode == 'true' || $lift_mode == 1) {      // In building lift mode
                    $lift_info_row = $lift_info_query->row();
                    $base_cost = $lift_info_row->base_cost;
                    $meter_cost = $lift_info_row->meter_cost;
                }
                else {      // No generic info to retrieve in building lift mode, because not in building lift mode
                    $base_cost = '';
                    $meter_cost = '';
                }
                $id_group = $lift_array->id_group;                  // id_group of lift (by stack of three)
                $array_of_built_lifts[] = $lift_array->id_group_location;           // Array containing ID location of the built lifts. Size of array: number of built lifts
                $lift_type_query = $this->item_model->get_lift_types_with_id_group($id_group);  // Get the type of item 
                $lift_type = $lift_type_query->row();       // Value of the lift type (chairlift...)
                
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $column_name = 'name_'.$player_preferred_lang;
                $lift_type_name = $lift_type->$column_name; // name_english, name_french
                $lift_type_id = $lift_type->lift_type; // name_english, name_french
                $array_of_custom_names[] = array ('id_group_location' => $lift_array->id_group_location, 'custom_name' => $lift_array->custom_name, 'type_name' => $lift_type_name, 'type_id' => $lift_type_id, 'status' => $lift_array->id_status); // Array containing custom names of the built lifts. Size of array: number of built lifts
            } // End of loop for built lifts only
        }
        else {
            if ($lift_info_query->num_rows() > 0 ) {
                $lift_info_row = $lift_info_query->row();
                $base_cost = $lift_info_row->base_cost;
                $meter_cost = $lift_info_row->meter_cost;      
            }
            else {
                $base_cost = '';
                $meter_cost = '';      
            }
            $array_of_built_lifts[] = '';
            $array_of_custom_names[] = '';
        }
        $where_statement = '';
        $sector_access_data = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
        foreach ($sector_access_data as $sector_access_array) {        // Get the locations of the lifts
            if ($sector_access_array != '') {   // not for first iteration (NULL)
                $where_statement .= 'id_sector = '.(int)$sector_access_array.' OR ';
            }
        }
        if ($where_statement === '') {
            echo json_encode(array('returned' => false));
            return;
        }
        $where_statement = "(".substr($where_statement, 0, -4).")";  // remove ' OR ' at the end
   
        $lift_list_map = $this->resort_model->get_item_location($where_statement, null , 'lift', $currentResortID); // Get details about all lift locations
        $location_info = $lift_list_map->result();  // Result of all locations

        foreach ($location_info as $key=>$location_info_array) {        // Get the locations of the lifts 
            $id_group_location = $location_info_array->id_group;
            if ($lift_mode == 'true' && in_array($id_group_location, $array_of_built_lifts)) {   //building new lift => Display built lifts is smaller black line
                $style_info_built = array(           
                    'color' => 'black',
                    'opacity' => '0.7',
                    'weight' => '3',
                    'dashArray' => '5, 0');
                foreach($array_of_custom_names as $key => $custom_names){
                   if ( $custom_names['id_group_location'] === $id_group_location ) {
                      $current_name = $custom_names['custom_name'];
                      $current_type_name = $custom_names['type_name'];
                      $current_type_id = $custom_names['type_id'];
                   }
                }
            }
            else if ($lift_mode == 'true' && !in_array($id_group_location, $array_of_built_lifts)) {   //building new lift => Available location (purple)
                $style_info_built = array(           
                    'color' => 'purple',
                    'opacity' => '1',
                    'weight' => '8',
                    'dashArray' => '5, 10');
                $current_name = '';
                $current_type_name = '';
                $current_type_id = '';
            }
            else if ($lift_mode == 'false' && in_array($id_group_location, $array_of_built_lifts)) {   // Not building new lift mode => Display only built lifts in thick black
                foreach($array_of_custom_names as $key => $custom_names){
                   if ( $custom_names['id_group_location'] === $id_group_location ){
                        $current_name = $custom_names['custom_name'];
                        $current_type_name = $custom_names['type_name'];
                        $current_type_id = $custom_names['type_id'];
                        if ($custom_names['status'] == '4')
                            $dashArray = '5, 5';
                        else
                            $dashArray = '5, 0';
                   }
                }
                $style_info_built = array(           
                    'color' => 'black',
                    'opacity' => '1',
                    'weight' => '5',
                    'dashArray' => $dashArray);
            }
            else if ($lift_mode == 'false' && !in_array($id_group_location, $array_of_built_lifts)){
                $style_info_built = array(   // Not building new lift mode => Not built lift. Should not draw anything      
                    'color' => 'white',
                    'opacity' => '0.5',
                    'weight' => '0',
                    'dashArray' => '5, 0');
                $current_name = '';
                $current_type_name = '';
                $current_type_id = '';
            }

            $x = $location_info_array->x_coordinates;
            $y = $location_info_array->y_coordinates;
            if ($previous_id_group == '') {
                $start_location_coordinates[] = array ($x, $y);
                $id_group_to_send[] = $location_info_array->id_group;
                $style[] = $style_info_built;
                $lift_type_to_send[] = $current_type_name;
                $id_lift_type_to_send[] = $current_type_id;
                $base_cost_to_send[] = $base_cost;
                $meter_cost_to_send[] = $meter_cost;
                $name_to_send[] = $current_name;
            }
            else if ($location_info_array->id_group == $previous_id_group) {
                $end_location_coordinates[] = array ($x, $y);
            }
            else {
                $start_location_coordinates[] = array ($x, $y);
                $id_group_to_send[] = $location_info_array->id_group;
                $lift_type_to_send[] = $current_type_name;
                $id_lift_type_to_send[] = $current_type_id;
                $style[] = $style_info_built;
                $base_cost_to_send[] = $base_cost;
                $meter_cost_to_send[] = $meter_cost;
                $name_to_send[] = $current_name;
            }
            $previous_id_group = $location_info_array->id_group;
        }
        
        $array_data = array (
            'returned' => true,
            'style' => $style,
            'start_location_coordinates' => $start_location_coordinates,
            'end_location_coordinates' => $end_location_coordinates,
            'id_group' => $id_group_to_send,
            'name' => $name_to_send,
            'lift_type' => $lift_type_to_send,
            'id_lift_type' => $id_lift_type_to_send,
            'base_cost' => $base_cost_to_send,
            'meter_cost' => $meter_cost_to_send,
            'lift_mode' => $lift_mode 
        ); 
        echo json_encode($array_data);
   }
   
   
    public function get_all_lifts_map(){
        $previous_id_group = '';            // Used to detect first loop
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $where_statement = '';
        $sector_access_data = $this->resort_model->get_sector_access($currentResortID);
        foreach ($sector_access_data as $sector_access_array) {
            if ($sector_access_array != '') {
                $where_statement .= 'id_sector = '.(int)$sector_access_array.' OR ';
            }
        }
        if ($where_statement === '') {
            echo json_encode(array('returned' => false));
            return;
        }
        $where_statement = "(".substr($where_statement, 0, -4).")";

        $lift_list_map = $this->resort_model->get_item_location($where_statement, null, 'lift', $currentResortID); // Get details about all lift locations in accessible sectors
        $location_info = $lift_list_map->result();  // Result of all locations

        foreach ($location_info as $key=>$location_info_array) {        // Get the locations of the lifts 
            $x = $location_info_array->x_coordinates;
            $y = $location_info_array->y_coordinates;
            
            if ($location_info_array->id_group == $previous_id_group) {
                $end_location_coordinates[] = array ($x, $y);
            }
            else {
                $start_location_coordinates[] = array ($x, $y);
                $id_group_to_send[] = $location_info_array->id_group;
            }
            $previous_id_group = $location_info_array->id_group;
        }
        
        $array_data = array (
            'returned' => true,
            'start_location_coordinates' => $start_location_coordinates,
            'end_location_coordinates' => $end_location_coordinates,
            'id_group' => $id_group_to_send
        ); 
        echo json_encode($array_data);
   }
   
   
   public function get_all_slopes_map()    {
       
       $slope_type = $this->input->post('slope_type', TRUE);
       
        if (!isset($slope_type)) {
            $where_condition = 'slope_type != "2"';  // exclude snowparks (2) as they are not really considered slopes, but areas / sectors instead
        }
        else {
             $where_condition = 'slope_type = "'.$slope_type.'"';   // for non-regular slopes (snowpark...)
        }
        $all_generic_slope_info = $this->item_model->get_generic_slope_info_sector($where_condition); // Get details about all slopes (including path and locations)
        //echo $this->db->last_query();
        //echo 'rows: '.$all_generic_slope_info->num_rows();
        if ($all_generic_slope_info->num_rows() > 0) {
            
            $all_generic_slope_info_result = $all_generic_slope_info->result();  // Result of all locations
        
            foreach ($all_generic_slope_info_result as $all_generic_slope_info_array) {        // Get the locations of the slopes 
                $path[] = $all_generic_slope_info_array->path;

                if( $all_generic_slope_info_array->slope_type == '3' || $all_generic_slope_info_array->slope_type == '5') {   // thicker than slope for boardercross (3)
                    $weight[] = 10;
                }
                else {  // regular slopes, snowpark (not used because considered sector, not a line/slope)
                    $weight[] = 4;
                }


            }


            $array_data = array (
                'returned' => true,
                'weight' => $weight,
                'path' => $path
            );    
        echo json_encode($array_data);
        }
   }
   
       
    protected function isEven($n) {
        return $n % 2 == 0;
    }

    /**
     * build_drawn_slope    AJAX endpoint: saves a player-drawn slope to game_slopes then
     *                      immediately starts construction (deducts cash, creates
     *                      game_created_slopes entry).
     *
     * POST params:
     *   path          – Coordinate string in "[lng,lat],[lng,lat],..." format
     *   length        – Slope length in metres (integer)
     *   slope_type    – Slope type ID (1–6)
     *   id_sector     – Sector the slope belongs to
     *   id_difficulty – Difficulty level ID
     */
    public function build_drawn_slope() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $path         = $this->input->post('path', TRUE);
        $length       = (int)$this->input->post('length', TRUE);
        $slope_type   = (int)$this->input->post('slope_type', TRUE);
        $id_sector    = (int)$this->input->post('id_sector', TRUE);
        $id_difficulty = (int)$this->input->post('id_difficulty', TRUE);

        // Basic validation
        if (!$path || $length <= 0 || $slope_type < 1 || $id_sector < 1 || $id_difficulty < 1) {
            echo json_encode(array('returned' => false, 'error' => 'invalid_params'));
            return;
        }

        // Verify sector access
        $accessible_sectors = $this->resort_model->get_sector_access($currentResortID);
        if (!in_array($id_sector, $accessible_sectors)) {
            echo json_encode(array('returned' => false, 'error' => 'sector_not_accessible'));
            return;
        }

        // Check no ongoing slope construction
        $slopes_under_construction = count_ongoing_building_items('slope', '4');
        if ($slopes_under_construction > 0) {
            echo json_encode(array('returned' => false, 'error' => 'ongoing_construction'));
            return;
        }

        // Insert slope into game_slopes
        $new_id_slope = $this->resort_model->insert_drawn_slope(
            $currentResortID, $id_sector, $slope_type, $path, $length
        );
        if (!$new_id_slope) {
            echo json_encode(array('returned' => false, 'error' => 'insert_failed'));
            return;
        }

        // Build the slope (deduct cash, set under construction)
        $slope_meter_price_arr         = SLOPE_METER_PRICE;
        $slope_meter_building_time_arr = SLOPE_METER_BUILDING_TIME;

        if (!isset($slope_meter_price_arr[$slope_type - 1])) {
            echo json_encode(array('returned' => false, 'error' => 'invalid_slope_type'));
            return;
        }
        $slope_meter_price_local         = $slope_meter_price_arr[$slope_type - 1];
        $slope_meter_building_time_local = $slope_meter_building_time_arr[$slope_type - 1];

        $resort_info_row = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $altitude_cost_mult = $this->_get_altitude_build_cost_multiplier(isset($resort_info_row->altitude) ? $resort_info_row->altitude : 'medium');
        $cost_slope = (int) round($length * $slope_meter_price_local * $altitude_cost_mult);

        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $cost_slope) {
            // Rollback the inserted slope
            $this->db->where('id_slope', $new_id_slope)->delete('game_slopes');
            echo json_encode(array('returned' => false, 'error' => 'not_enough_money', 'cost' => $cost_slope, 'cash' => $cash_player));
            return;
        }

        $time_construction_duration  = $length * $slope_meter_building_time_local / ACCELERATOR_FACTOR;
        $current_time                = time();
        $end_construction_timestamp  = $current_time + $time_construction_duration;

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $column_name           = 'name_' . $player_preferred_lang;

        $new_slope_data = array(
            'id_resort'       => $currentResortID,
            'id_slope'        => $new_id_slope,
            'id_difficulty'   => $id_difficulty,
            'custom_name'     => 'Custom Slope',
            'slope_condition' => '100',
            'id_status'       => '4',
            'end_construction'=> gmdate('Y-m-d H:i:s', $end_construction_timestamp),
        );

        $this->users_model->pay_item($cost_slope, $cash_player);
        $this->load->model('item_model');
        $build_ok = $this->item_model->build_slope($new_slope_data);

        if (!$build_ok) {
            echo json_encode(array('returned' => false, 'error' => 'build_failed'));
            return;
        }

        $cash_player = $this->users_model->get_cash_player();
        $this->session->set_userdata('cash', $cash_player);

        $gain_reputation = 10;
        $this->users_model->add_reputation($gain_reputation);
        $reputation_player = $this->users_model->get_reputation_player();
        $this->session->set_userdata('reputation', $reputation_player);

        add_cost_stat_table($currentResortID, $cost_slope, 'cost_purchases');
        add_cost_stat_table($currentResortID, $cost_slope, 'expenses');

        $data_achievement = array(
            'id_resort'  => $currentResortID,
            'id_building'=> $new_id_slope,
            'type'       => 'slope',
            'level'      => '1',
            'id_sector'  => $id_sector,
        );
        call_achievements_check($data_achievement, 'build');
        call_achievements_check(array('id_resort' => $currentResortID, 'quantity' => $cost_slope), 'build_amount');

        $this->logs_model->call_notification_DB(array(
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['slope'],
            'data'      => $this->lang->line('logs')['construction_of'] . 'Custom Slope' . $this->lang->line('logs')['has_started'],
        ));

        echo json_encode(array(
            'returned'         => true,
            'id_slope'         => $new_id_slope,
            'cost'             => $cost_slope,
            'end_construction' => gmdate('Y-m-d H:i:s', $end_construction_timestamp),
        ));
    }

    /**
     * build_drawn_lift     AJAX endpoint: saves a player-drawn lift to game_locations then
     *                      immediately starts construction.
     *
     * POST params:
     *   x_start       – Start longitude
     *   y_start       – Start latitude
     *   x_end         – End longitude
     *   y_end         – End latitude
     *   length        – Lift length in metres
     *   id_sector     – Sector ID
     *   id_lift_type  – Lift type (from game_lifts.lift_type)
     *   id_grip_type  – Grip type
     *   capacity      – Capacity per car
     */
    public function build_drawn_lift() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $x_start     = (float)$this->input->post('x_start', TRUE);
        $y_start     = (float)$this->input->post('y_start', TRUE);
        $x_end       = (float)$this->input->post('x_end', TRUE);
        $y_end       = (float)$this->input->post('y_end', TRUE);
        $length      = (int)$this->input->post('length', TRUE);
        $id_sector   = (int)$this->input->post('id_sector', TRUE);
        $id_lift_type = $this->input->post('id_lift_type', TRUE);
        $id_grip_type = $this->input->post('id_grip_type', TRUE);
        $capacity     = $this->input->post('capacity', TRUE);

        if ($length <= 0 || $id_sector < 1 || !$id_lift_type || !$id_grip_type || !$capacity) {
            echo json_encode(array('returned' => false, 'error' => 'invalid_params'));
            return;
        }

        // Verify sector access
        $accessible_sectors = $this->resort_model->get_sector_access($currentResortID);
        if (!in_array($id_sector, $accessible_sectors)) {
            echo json_encode(array('returned' => false, 'error' => 'sector_not_accessible'));
            return;
        }

        // Check no ongoing lift construction
        $lifts_under_construction = count_ongoing_building_items('lift', '4');
        $lifts_under_maintenance  = count_ongoing_building_items('lift', '3');
        if ($lifts_under_construction > 0 || $lifts_under_maintenance > 0) {
            echo json_encode(array('returned' => false, 'error' => 'ongoing_construction'));
            return;
        }

        // Get generic lift info (cost, build time)
        $lift_info_query = $this->item_model->get_generic_lift_info($id_lift_type, $id_grip_type, $capacity);
        if ($lift_info_query->num_rows() == 0) {
            echo json_encode(array('returned' => false, 'error' => 'lift_type_not_found'));
            return;
        }
        $lift_info_row = $lift_info_query->row();
        $id_group_template         = $lift_info_row->id_group;
        $base_cost_lift            = $lift_info_row->base_cost;
        $meter_cost_lift           = $lift_info_row->meter_cost;
        $time_construction_duration= $lift_info_row->building_time / ACCELERATOR_FACTOR;
        $gain_reputation           = $lift_info_row->reputation;

        // Get level-1 specific data for the lift name
        $genericLift_l1 = $this->item_model->get_generic_item_info_for_level($id_group_template, 'lift', '1');
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $column_name = 'name_' . $player_preferred_lang;
        $lift_name = ($genericLift_l1->num_rows() > 0) ? $genericLift_l1->row()->$column_name : 'Custom Lift';

        // Insert location into game_locations
        $new_id_group = $this->resort_model->insert_drawn_lift_location(
            $currentResortID, $id_sector, $x_start, $y_start, $x_end, $y_end, $length
        );
        if (!$new_id_group) {
            echo json_encode(array('returned' => false, 'error' => 'location_insert_failed'));
            return;
        }

        // Calculate cost
        $resort_info_row = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $altitude_cost_mult = $this->_get_altitude_build_cost_multiplier(isset($resort_info_row->altitude) ? $resort_info_row->altitude : 'medium');
        $cost_lift = (int) round(($base_cost_lift + ($meter_cost_lift * $length)) * $altitude_cost_mult);

        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $cost_lift) {
            // Rollback
            $this->db->where('id_group', $new_id_group)->delete('game_locations');
            echo json_encode(array('returned' => false, 'error' => 'not_enough_money', 'cost' => $cost_lift, 'cash' => $cash_player));
            return;
        }

        $current_time               = time();
        $end_construction_timestamp = $current_time + $time_construction_duration;

        $this->users_model->pay_item($cost_lift, $cash_player);
        $build_ok = $this->item_model->build_lift(
            $currentResortID, $id_group_template, $lift_name, $end_construction_timestamp, $new_id_group
        );

        if (!$build_ok) {
            echo json_encode(array('returned' => false, 'error' => 'build_failed'));
            return;
        }

        $cash_player = $this->users_model->get_cash_player();
        $this->session->set_userdata('cash', $cash_player);
        $this->users_model->add_reputation($gain_reputation);
        $reputation_player = $this->users_model->get_reputation_player();
        $this->session->set_userdata('reputation', $reputation_player);

        add_cost_stat_table($currentResortID, $cost_lift, 'cost_purchases');
        add_cost_stat_table($currentResortID, $cost_lift, 'expenses');

        $data_achievement = array(
            'id_resort'  => $currentResortID,
            'id_building'=> $id_group_template,
            'type'       => 'lift',
            'level'      => '1',
            'id_sector'  => $id_sector,
        );
        call_achievements_check($data_achievement, 'build');
        call_achievements_check(array('id_resort' => $currentResortID, 'quantity' => $cost_lift), 'build_amount');

        $this->logs_model->call_notification_DB(array(
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['lift'],
            'data'      => $this->lang->line('logs')['construction_of'] . $lift_name . $this->lang->line('logs')['has_started'],
        ));

        echo json_encode(array(
            'returned'          => true,
            'id_group_location' => $new_id_group,
            'cost'              => $cost_lift,
            'end_construction'  => gmdate('Y-m-d H:i:s', $end_construction_timestamp),
        ));
    }

    /**
     * _get_altitude_build_cost_multiplier  Delegates to the shared helper.
     */
    private function _get_altitude_build_cost_multiplier($altitude) {
        return get_altitude_build_cost_multiplier($altitude);
    }
}