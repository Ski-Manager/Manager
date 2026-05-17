<?php
/**
 * 
 */
class Overview_staff_controller extends CI_Controller{
    
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
           // $ci->lang->load('lift',$siteLang);
           // $ci->lang->load('resort',$siteLang);
           // $ci->lang->load('slope',$siteLang);
           // $ci->lang->load('building',$siteLang);
        $ci->lang->load('staff',$siteLang);
           // $ci->lang->load('equipment',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('staff_model');
        $this->load->model('logs_model');
        $this->load->model('item_model');
    }
    

    public function index($data = NULL){
        $data['title'] = '<h2>'.$this->lang->line('common_staff')['titleMain'];
        $data['title'] .= ' - ';
        $data['title'] .= $this->lang->line('overviewStaff')['title'].'</h2>'; 
        $data['introOverviewStaff'] = '<div>'.$this->lang->line('overviewStaff')['intro'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // Checks if the resort exists       
            if ($checkIfResortExists->num_rows() > 0) { 

                $data1 = $this->table_data_staff($currentResortID);    
                $data = array_merge($data,$data1);      // Merges all data to "data" for the view            

                // Displaying the staff view
                $data['main_content'] = 'overviewStaff';
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

    
    
    public function table_data_staff($currentResortID){
        // Sets general variables
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
        $data['rowsStaff'] = '';
        // Use optimized method that JOINs hired staff with generic staff info to avoid N+1 queries
        $hired_staff_player = $this->staff_model->get_hired_staff_with_info_DB($currentResortID);
        if ($hired_staff_player->num_rows() > 0) {                // the player has already recruted employees
            foreach ($hired_staff_player->result() as $row){
                $data['player_has_staff']  = true;
                // No need to query generic_staff_info separately - data is already in $row from the JOIN
                $friendly_name = $row->$name_language;
                $resort_query = false;
            
                if ($row->position == 'skipatrol'){   // If Staff type = skipatrol , choose type to assign SLOPE
                    $type = 'slope';
                    $table_name = 'game_created_'.$type.'s';
                    $field_name_type = 'id_created_'.$type.'s';
                    $where = "";
                    //$where = "id_status!= 4";
                }
                else if ($row->position == 'skiinstructor'){   // If Staff type = instructor , choose type to assign SLOPE
                    $type = 'sector';
                    $table_name = 'game_resorts';
                    $field_name_type = 'id_created_'.$type.'s';
                    //$where = "id_status!= 4";
                    $where = "";
                    $resort_query = true;
                }
                else if ($row->position == 'liftmechanic'){   // If Staff type = mechanic , choose type to assign LIFT
                    $type = 'lift';
                    $table_name = 'game_created_'.$type.'s';
                    $field_name_type = 'id_created_'.$type.'s';
                    $where = "";
                    //$where = "id_status!= 4";
                }
                else if ($row->position == 'mechanicGroomer'){   // If Staff type = mechanicGroomer , choose type to assign LIFT
                    $type = 'groomer'; 
                    $table_name = 'game_purchased_equipments';
                    $field_name_type = 'id_purchased_equipments';
                    $where = "type = 1";
                    //$where = "(delivered = 1 OR level > 1) AND type = 1";
                }
                else if ($row->position == 'driver'){   // If Staff type = driver , choose type to assign SKIBUS
                    $type = 'skibus'; // to be "skibus"
                    $table_name = 'game_purchased_equipments';
                    $field_name_type = 'id_purchased_equipments';
                    $where = "type = 2";
                    //$where = "(delivered = 1 OR level > 1) AND type = 2";
                }
                if ($resort_query != true) {
                    $associated_items = $this->staff_model->get_associated_items_DB($currentResortID, $table_name, $where);
                }
                else {
                    $associated_items = $this->staff_model->get_accessible_sectors($currentResortID);
                }
                $disabled_type = $type.'s';
                
                $data['rowsStaff'] .= '<tr data-id_hired_staff="'.$row->id_hired_staff.'" data-salary="'.$row->salary.'" data-type="'.$type.'" data-currentResortId="'.$currentResortID.' "data-position="'.$row->position.'" data-friendly_name="'.$friendly_name.'"><td>'.$friendly_name.'</td>';
                $data['rowsStaff'] .= '<td>'.$row->efficiency.' %</td>';
                // Skill level column (new career-progression field)
                $skill_level = isset($row->skill_level) ? (int)$row->skill_level : 1;
                $xp          = isset($row->experience_points) ? (int)$row->experience_points : 0;
                $specialization = isset($row->specialization) ? $row->specialization : null;
                $trait          = isset($row->trait)          ? $row->trait          : null;
                $skill_stars = str_repeat('★', $skill_level).str_repeat('☆', STAFF_MAX_SKILL_LEVEL - $skill_level);
                $skill_cell  = '<span class="skill-stars" title="'.$xp.' XP">'.$skill_stars.'</span>';
                if ($specialization) {
                    $spec_key   = 'spec_'.$specialization;
                    $spec_label = $this->lang->line('hireStaff')[$spec_key] ?? ucfirst($specialization);
                    $skill_cell .= ' <span class="badge badge-info ms-1 tooltip" data-tip="'.htmlspecialchars($spec_label, ENT_QUOTES, 'UTF-8').'">🎯 '.htmlspecialchars($spec_label, ENT_QUOTES, 'UTF-8').'</span>';
                }
                if ($trait) {
                    $trait_key   = 'trait_'.$trait;
                    $trait_label = $this->lang->line('hireStaff')[$trait_key] ?? ucfirst($trait);
                    $skill_cell .= ' <span class="badge badge-accent ms-1 tooltip" data-tip="'.htmlspecialchars($trait_label, ENT_QUOTES, 'UTF-8').'">🧠 '.htmlspecialchars($trait_label, ENT_QUOTES, 'UTF-8').'</span>';
                }
                $data['rowsStaff'] .= '<td>'.$skill_cell.'</td>';
                $data['rowsStaff'] .= '<td>'.$row->salary.' €</td>';
                $data['rowsStaff'] .= '<td>'.date("d-m-Y", strtotime($row->date_hired)).'</td>';
                // Morale column
                $morale = isset($row->morale) ? (int)$row->morale : MORALE_DEFAULT;
                $on_strike = isset($row->on_strike) ? (int)$row->on_strike : 0;
                if ($on_strike) {
                    $morale_color = 'progress-bar-error';
                    $morale_label = '<span class="badge badge-error ms-1">'.$this->lang->line('overviewStaff')['on_strike'].'</span>';
                } elseif ($morale < 50) {
                    $morale_color = 'progress-bar-error';
                    $morale_label = '';
                } elseif ($morale <= 70) {
                    $morale_color = 'progress-bar-warning';
                    $morale_label = '';
                } else {
                    $morale_color = 'progress-bar-success';
                    $morale_label = '';
                }
                $data['rowsStaff'] .= '<td>';
                $data['rowsStaff'] .= '<progress class="progress '.$morale_color.' w-20" value="'.$morale.'" max="100" title="'.$morale.'%"></progress>';
                $data['rowsStaff'] .= '<span class="text-xs ms-1">'.$morale.'%</span>'.$morale_label;
                $data['rowsStaff'] .= '</td>';
                $data['rowsStaff'] .= '<td>';
                $data['rowsStaff'] .= '<select id="assigned_to_'.$row->id_hired_staff.'" class="select select-sm w-full">';
                //$data['rowsStaff'] .= '<option disabled value="" class="colored_disabled">'.$this->lang->line($disabled_type).'</option>';
                $empty_row_done = false;  
                $list_not_empty = false;  
                if ($associated_items->num_rows() > 0) {                // the player has already recruted employees
                    foreach ($associated_items->result() as $associated_items_row){
                        if(isset($associated_items_row->id_slope)) {    // If the associated item is a slope
                            //echo 'id_slope: '.$associated_items_row->id_slope.' > ';
                            $generic_slope_info = $this->item_model->get_generic_slope_info_sector('id_slope = '.$associated_items_row->id_slope);  // get info for selected slope ID
                            $generic_slope_info_row = $generic_slope_info->row();
                            //var_dump($generic_slope_info_row->slope_type);
                            if (isset($generic_slope_info_row->slope_type))
                                $slope_type = $generic_slope_info_row->slope_type;
                            else 
                                $slope_type = NULL; // If not slope, need to have initialized variable
                        }
                        else {
                            $slope_type = NULL; // If not slope, need to have initialized variable
                        }
                        $selected_status = '';
                        if ($resort_query != true) {
                            $query_count_assigned_staff = $this->staff_model->count_assigned_staff_DB($type, $associated_items_row->$field_name_type, $currentResortID);
                            // Ski patrol allows up to MAX_PATROL_PER_SLOPE per slope; all other roles allow only 1
                            $max_assigned = ($row->position == 'skipatrol') ? MAX_PATROL_PER_SLOPE : 1;
                            if ($row->id_item_assigned == NULL && $empty_row_done === false){    // Only if there is no associated item, and only for first line: Make empty row
                                // Make empty option
                                $data['rowsStaff'] .= '<option disabled selected value=""> </option>';
                                if ($query_count_assigned_staff < $max_assigned) {
                                    $data['rowsStaff'] .= '<option value="'.$associated_items_row->$field_name_type.'">'.$associated_items_row->custom_name.'</option>';
                                    $list_not_empty = true;
                                }
                                $empty_row_done = true;
                            }
                            else if ($row->id_item_assigned == NULL && $empty_row_done){    // Only if there is no associated item, and for following lines
                                if ($query_count_assigned_staff < $max_assigned && ($slope_type == 1 || $slope_type == 2 || $slope_type == 3 || $slope_type == NULL ) )  {
                                    $data['rowsStaff'] .= '<option value="'.$associated_items_row->$field_name_type.'">'.$associated_items_row->custom_name.'</option>';
                                    $list_not_empty = true;
                                }
                            }
                            else if($row->id_item_assigned != NULL) {      // If there is associated item, or if second row (empty row already done)
                                if ($associated_items_row->$field_name_type == $row->id_item_assigned ){
                                    $selected_status = 'selected';
                                    $data['rowsStaff'] .= '<option '.$selected_status.' value="'.$associated_items_row->$field_name_type.'">'.$associated_items_row->custom_name.'</option>';
                                    $list_not_empty = true;
                                }
                                if ($query_count_assigned_staff < $max_assigned && ($slope_type == 1 || $slope_type == 2 || $slope_type == 3 || $slope_type == NULL ) && $associated_items_row->$field_name_type != $row->id_item_assigned) {
                                    $data['rowsStaff'] .= '<option value="'.$associated_items_row->$field_name_type.'">'.$associated_items_row->custom_name.'</option>';
                                    $list_not_empty = true;
                                }
                            }
                        }
                        else {
                            $id_hired_staff = $row->id_hired_staff;
                            $data['rowsStaff'] .= $this->return_data_sectors($currentResortID, $associated_items, $id_hired_staff);
                            $list_not_empty = true;
                        }
                    }
                    if ($list_not_empty == false) {
                        $data['rowsStaff'] .= '<option disabled value="">'.$this->lang->line('home')['there_are_no'].' '.$type.' '.$this->lang->line('overviewStaff')['available'].'.</option>';
                    }
                }
                $data['rowsStaff'] .= '</select><span id="result_assigned_to_'.$row->id_hired_staff.'"></span>';
                $data['rowsStaff'] .= '</td>';
                $data['rowsStaff'] .= '<td><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('overviewStaff')['fire_tooltip'].'"><a href="?action=delete" class="delete-dialog btn-danger">'.$this->lang->line('overviewStaff')['fire'].'</a></div>'
                    . '<button type="button" class="btn-train btn btn-sm btn-info ms-1 tooltip" '
                    . 'data-id="'.$row->id_hired_staff.'" '
                    . 'data-skill="'.$skill_level.'" '
                    . 'data-tip="'.htmlspecialchars($this->lang->line('overviewStaff')['train_tooltip'], ENT_QUOTES, 'UTF-8').'">'
                    . '<i class="fa-solid fa-graduation-cap me-1"></i>'.$this->lang->line('overviewStaff')['train'].'</button>'
                    . '<span class="train-result ms-1" id="train-result-'.$row->id_hired_staff.'"></span>'
                    . '</td></tr>';
            }
        }
        else {
            $data['player_has_staff'] = false;
            $data['infoMessage'] = 'no_staff_hired'; 
        }
        
        // Resort morale summary
        $data['morale_summary'] = $this->staff_model->get_resort_morale_summary_DB($currentResortID);
          
        return($data);
    }
    
    
    public function return_data_sectors($currentResortID, $sector_info, $id_hired_staff){
        $data = '';
        $empty_row_done = false;
        $associated_items = $this->staff_model->get_id_item_assigned($currentResortID, $id_hired_staff);
        if ($associated_items->num_rows() > 0) { 
            $associated_items_row = $associated_items->row();
        }
        $array_sectors = $this->resort_model->get_sector_access($currentResortID);
        foreach ($sector_info->result() as $sector_info_row){
            for ($i = 0; $i < count($array_sectors); $i++) {
                $selected_status = '';
                if ($associated_items_row->id_item_assigned == NULL && !$empty_row_done){
                    // Make empty option
                    $data .= '<option disabled selected value=""> </option>';
                    $empty_row_done = true;
                }
                else if ($associated_items_row->id_item_assigned == $i){
                    $selected_status = 'selected';
                }
                $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                if (isset($sector_access_array[$i]) && $sector_access_array[$i] == $i){
                    $data .= '<option '.$selected_status.' value="'.$i.'">'.$this->lang->line('home')['sector'].$i.'</option>';
                }
            }
        }
        //echo 'here:';
     return $data;
    }
    
    
    public function fire_staff(){
        $currentResortID = trim($this->input->post('currentResortId', TRUE));
        $id_hired_staff = trim($this->input->post('id_hired_staff', TRUE));
        $friendly_name = trim($this->input->post('friendly_name', TRUE));
        $salary = trim($this->input->post('salary', TRUE));        
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed) 
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE

        // Use contract-aware firing penalty multiplier
        $contract_months = $this->staff_model->get_hired_staff_contract_DB($currentResortID, $id_hired_staff);
        if ($contract_months <= CONTRACT_SHORT) {
            $penalty_multiplier = FIRING_PENALTY_SHORT;
        } elseif ($contract_months <= CONTRACT_MEDIUM) {
            $penalty_multiplier = FIRING_PENALTY_MEDIUM;
        } else {
            $penalty_multiplier = FIRING_PENALTY_LONG;
        }

        $firing_fee = $salary * $penalty_multiplier;
        $cash_player = $this->users_model->get_cash_player();
        if ($removeCashQuery = $this->users_model->pay_item($firing_fee, $cash_player)){
            $cash_player = $this->users_model->get_cash_player();
            $updated_cash = $this->session->set_userdata('cash', $cash_player);
            $fire_staff = $this->staff_model->fire_staff_db($currentResortID, $id_hired_staff);
            $add_cost_history_table = add_cost_stat_table($currentResortID, $firing_fee, 'cost_salaries');
            $add_cost_history_table = add_cost_stat_table($currentResortID, $firing_fee, 'expenses');
            if ($fire_staff) {
                $data_general_staff = array (
                    'id_resort' => $currentResortID,
                    'position' => '*'
                );
                $currentUserID = $this->users_model->get_user_id();
                $call_achievements_check = call_achievements_check($data_general_staff, 'fire');
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['staff'], 'data' => $friendly_name.$this->lang->line('logs')['was_fired']) );
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['staff'], 'data' => $friendly_name.$this->lang->line('logs')['was_fired']) );
                return true;
            }
            else {
                return false;
            } 
        }
        else {
            return false;
        } 
    }
    
    
    public function getDataTable(){
        $string = trim($this->input->post('search[value]', TRUE));
        $data = $this->staff_model->get_all_staff_DB($string);   
        echo json_encode(array('Data' => $data));
    }
    
    public function get_cash_player(){         // We need the Ajax function to go through a controller to access the get_cash_player Model           
        $cash = $this->users_model->get_cash_player();
        echo json_encode(array('cash' => $cash));         // We return the cash value, e.g. 4000000
    }
    
    public function get_reputation_player(){         // We need the Ajax function to go through a controller to access the get_reputation_player Model           
        $reputation = $this->users_model->get_reputation_player();
        echo json_encode(array('reputation' => $reputation));         // We return the reputation value, e.g. 15000
    }
    public function get_genepis_player(){         // We need the Ajax function to go through a controller to access the get_genepis_player Model
        $currentUserID = $this->users_model->get_user_id();
        $genepis_player = $this->users_model->get_user_genepis_amount($currentUserID);                         // Get how much genepis the player has after payment
        echo json_encode(array('genepis' => $genepis_player));         // We return the genepis value, e.g. 30
    }
    
    
    public function edit_assigned_item(){         // Changes the staff assignation    
        $id_hired_staff = trim($this->input->post('id_hired_staff', TRUE));
        $idOfSelectedOption = trim($this->input->post('idOfSelectedOption', TRUE));
        $type = trim($this->input->post('type', TRUE));
        $staff_position = trim($this->input->post('position', TRUE));
        
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);          
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // check if there is already an employee assigned to the selected item (it shouldn't be allowed)
        $table = 'game_created_'.$type.'s';
        $item_id_field_name = 'id_created_'.$type.'s';
        $check_staff_assigned_to_item = $this->staff_model->check_staff_assigned_to_item_DB($currentResortID, $idOfSelectedOption, $type);
        // Ski patrol allows up to MAX_PATROL_PER_SLOPE per slope; all other roles allow only 1 per item
        if ($staff_position == 'skipatrol') {
            $current_patrol_count = $this->staff_model->count_skipatrol_assigned_DB($currentResortID, $idOfSelectedOption);
            $can_assign = ($current_patrol_count < MAX_PATROL_PER_SLOPE);
        } else {
            $can_assign = ($check_staff_assigned_to_item == '0' || $type == 'sector');
        }
        if ($can_assign) {
            $data = array (
                'id_item_assigned' => $idOfSelectedOption,       // Type to assign to the staff
                'type_item_assigned' => $type       // Type to assign to the staff
            );
            $edit_assigned_item = $this->staff_model->edit_assigned_item_DB($currentResortID, $id_hired_staff, $data);
            if ($edit_assigned_item) {
                $call_achievements_check = call_achievements_check(array('position' =>$staff_position), 'assign_staff');
                echo json_encode(array('returned' => true, 'count' => $check_staff_assigned_to_item));
            }
        }
        else {
            echo json_encode(array('returned' => false, 'count' => $check_staff_assigned_to_item));
        }
    }
    
    /**
     * train_staff  AJAX endpoint: spend cash to give a staff member a training session.
     *
     * POST params: id_hired_staff (int)
     *
     * JSON response:
     *   { success: true,  new_cash: int, new_skill_level: int, new_xp: int, leveled_up: bool }
     *   { success: false, reason: string, [next_training: string] }
     */
    public function train_staff() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_hired_staff  = (int)$this->input->post('id_hired_staff', TRUE);

        // Verify staff belongs to this player's resort
        if (!$this->staff_model->check_staff_belongs_to_resort_db($currentResortID, $id_hired_staff)) {
            echo json_encode(['success' => false, 'reason' => 'not_found']);
            return;
        }

        // Check player cash
        $cash = $this->users_model->get_cash_player();
        if ($cash < STAFF_TRAINING_COST) {
            echo json_encode(['success' => false, 'reason' => 'not_enough_cash']);
            return;
        }

        // Check training eligibility (cooldown / max level)
        $check = $this->staff_model->can_train_db($id_hired_staff);
        if (!$check['ok']) {
            echo json_encode(array_merge(['success' => false], $check));
            return;
        }

        // Snapshot skill level before training to detect level-up
        $before = $this->db
            ->select('skill_level, experience_points')
            ->from('game_hired_staff')
            ->where('id_hired_staff', $id_hired_staff)
            ->get()->row();
        $level_before = $before ? (int)$before->skill_level : 1;

        // Deduct training cost
        $this->users_model->pay_item(STAFF_TRAINING_COST, $cash);
        $new_cash = $this->users_model->get_cash_player();
        $this->session->set_userdata('cash', $new_cash);

        // Record training cost in stats
        add_cost_stat_table($currentResortID, STAFF_TRAINING_COST, 'expenses');

        // Apply XP + stamp training date
        $this->staff_model->record_training_db($id_hired_staff);

        // Get updated staff row
        $after = $this->db
            ->select('skill_level, experience_points')
            ->from('game_hired_staff')
            ->where('id_hired_staff', $id_hired_staff)
            ->get()->row();
        $new_level = $after ? (int)$after->skill_level : $level_before;
        $new_xp    = $after ? (int)$after->experience_points : 0;
        $leveled_up = ($new_level > $level_before);

        // Log the action
        $log_text = $this->lang->line('overviewStaff')['train_log_entry']
            . ' -' . number_format(STAFF_TRAINING_COST, 0, ',', ' ') . ' €';
        if ($leveled_up) {
            $log_text .= ' (' . $this->lang->line('overviewStaff')['train_level_up'] . ' ' . $new_level . ')';
        }
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['staff'],
            'data'      => $log_text,
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['staff'],
            'data'      => $log_text,
        ]);

        echo json_encode([
            'success'         => true,
            'new_cash'        => $new_cash,
            'new_skill_level' => $new_level,
            'new_xp'          => $new_xp,
            'leveled_up'      => $leveled_up,
        ]);
    }

    public function call_notification_bridge(){
            $currentResortId = trim($this->input->post('currentResortId', TRUE));
            $friendly_name = trim($this->input->post('friendly_name', TRUE));
            $type = trim($this->input->post('type', TRUE));
            if ($type == 'lift') {
                $type_notif = 'lift';
                $action = 'sold';
            }
            else if ($type == 'slope') {
                $type_notif = 'slope';
                $action = 'was_destroyed';
            }
            else if ($type == 'fire_staff') {
                $type_notif = 'staff';
                $action = 'was_fired';
            }
            $currentUserID = $this->users_model->get_user_id_from_resortID($currentResortId);
            //$call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')[$type_notif], 'data' => $this->lang->line($friendly_name).$this->lang->line('logs')[$action]) );   // Add a log row to the game_player_logs table
            //$log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')[$type_notif], 'data' => $this->lang->line($friendly_name).$this->lang->line('logs')[$action]) );   // Add a log row to the game_player_logs table
            echo json_encode(array('result' => 'ok'));
    }
    
}