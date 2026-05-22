<?php
/**
 * 
 */
class Skibus_controller extends CI_Controller{
    
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
       // $ci->lang->load('slope',$siteLang);
       // $ci->lang->load('resort',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('equipment',$siteLang);
        //$ci->lang->load('staff',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('equipment_model');
        $this->load->model('staff_model');
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
        $type = 'skibus';
        $type_id = '2';
//LINE ABOVE TO EDIT 
        $data['title'] = '<h2>'.$this->lang->line('common_equipment')['titleMain'];
        $data['title'] .= ' - ';
        $data['title'] .= $this->lang->line($type)['title'].'</h2>'; 
        $data['introEquipment'] = '<div>'.$this->lang->line($type)['intro'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // Checks if the resort exists       
            if ($checkIfResortExists->num_rows() > 0) {                                        // if the player has a resort, OK

                // If toursit info center build, we can display the page
                $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  
                if ($tourist_info_data->num_rows() == 1) {          // Tourist info center is built
                    $data['hideEquipment'] = false;                  // To display specific blocks in the View (here we display the equipment)

                    $data1 = $this->equipmentBlock($type, $type_id, $currentResortID);    // Calls the generic block funtion for the right equipment type
                    $data2 = $this->assignEquipment($type, $type_id, $currentResortID, $type);    // Calls the assign equipment block for specific slopes
                    $data = array_merge($data, $data1, $data2);      // Merges all data to "data" for the view            
                }
                // Tourist info not built. We inform player and show a link (make new function)
                else {
                    $data['hideEquipment'] = true;                   // To display specific blocks in the View (here we display a message but no equipment)
                    $data['infoMessage'] = 'tourist_info_required';
                }
                // Displaying the building view
                $data['main_content'] = 'equipment';
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
     * equipmentBlock        Displays an equipment block. Can be used for any equipment
     * 
     * @param type $equipment_type   Type of equipment (name: groomer, bus...)
     * @param type $type            ID type of equipment (1, 2...)
     * @param type $currentResortID   Current resort ID
     * @return string               Returns the content of the page
     */
    public function equipmentBlock($equipment_type, $type, $currentResortID){
        // Sets general variables
        $data['equipmentLogo'] = '<img src="'.base_url('img/icons/'.$equipment_type.'-big.png').'" title="'.$this->lang->line($equipment_type)['title'].'"/>';
        $data['equipmentDesc'] = ''.$this->lang->line($equipment_type)['desc'].'';
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns

        // For each of the three levels
        for ($i=1;$i<=3;$i++) {
            // Gets the generic data for the equipment type and level
            $equipment_data = $this->equipment_model->get_generic_equipment_data($type, $i);    // Type (1 = groomer, 2=bus...) and "i" for each level ($i = 1, 2, 3)
            if ($equipment_data->num_rows() > 0) {                // the generic equipment exists in the DB (always!)
                $equipment_dataArray = $equipment_data->row();
                // initializes variables in case we don't do into the right loop
                $data['pre_delivery_time'][$i] = '';
                $data['post_delivery_time'][$i] = '';
                $disabled = '';

                // counts how many equipments are purchased for this level (to show Quantity)
                $count_this_equipment_level = count_this_equipment_level($type, $i);   // returns an integer
                $data['equipmentQuantity'][$i]= $count_this_equipment_level;                  // quantity for the current level

                // counts how many equipments under delivery for this level (if >= 1, display time left)
                $count_this_equipment_level_under_delivery = count_this_equipment_level($type, $i, '0');
                // Counts how many equipments are possessed by the player (only delivered) - for reselling
                $count_this_equipment_level_delivered = count_this_equipment_level($type, $i, '1');
                
                // count how many equipments of previous level - any status (to allow upgrade)
                $count_num_equipment_previous_level = count_this_equipment_level($type, $i-1);
                // counts how many equipments under delivery for any level (used to disable button)
                $count_this_equipment_under_delivery = count_this_equipment_level($type, '', '0'); // "0" = under delivery status
                
                if ($count_this_equipment_level_delivered == 0) {
                    $resell_button = '';
                }
                else {
                    if ($count_this_equipment_under_delivery != 0 )
                        $disabled = 'disabled';
                    $resell_price = $equipment_dataArray->buying_cost/2;
                    $resell_button = '<div class="tooltip tooltip-bottom" style="display:inline;" data-tip="'.$this->lang->line('common_equipment')['sell_equip_tooltip'].' ('.$resell_price.' €)" data-type="'.$type.'" data-i="'.$i.'" data-equipment_type="'.$equipment_type.'" data-currentResortId="'.$currentResortID.'"><a href="?action=sellequip" class="sellequip-dialog btn-danger">'.$this->lang->line('home')['sell_item'].'</a></div> ';
                }
                if ($count_this_equipment_level_under_delivery == '1'){              // If the current level is under delivery
                    //$end_delivery_date_format = strtotime(get_time_left_for_delivery($currentResortID, $type, $i)." UTC");    // return the date/time from the function. strtotime converts string to timestamp
                    $timestamp = strtotime(get_time_left_for_delivery($currentResortID, $type, $i)." UTC");    // return the number of seconds until the end
                    $currenttime = time();                                          // current timestamp
                    $time_left_value = $timestamp - $currenttime;                   // Time left in seconds
                    
                    // START RUSH BUTTON UPGRADE/BUILD
                    $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                    $button_level[$i] = '';
                    if ( isset($time_left_value) && $time_left_value > 0 ) {
                        $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                        $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                        if ($genepis_required_to_rush <= $genepis_available) {
                            $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url($equipment_type.'_controller/rush/'.$type.'/'.$i).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                        }
                        else {
                            $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                        }   
                    }
                    // END RUSH BUTTON
                
                    if ($time_left_value <= '0'){                                   // If there is no time left (delivery done)
                        $data['wait_status'][$i] = true;   // If there is no time left, we define a new variable for the view
                        // Tooltip + link to refresh the page if time left = 0
                        $data['pre_delivery_time'][$i] = '<a href="'.base_url().$equipment_type.'_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';   // For toolpit (pre)
                        $data['post_delivery_time'][$i] = '</div></a>';   // For toolpit (post)
                        $data['deliveryTime'][$i] = $this->lang->line('home')['wait'];   // Displays the Please Wait message in the table cell
                    }
                    else {    // If some time is left...
                        $data['deliveryTime'][$i] = gmdate("Y-m-d H:i:s", $timestamp);   // We add the date/time format to the view. The javascript function will take care of the countdown
                    }
                }
                else {
                    $data['deliveryTime'][$i] = display_friendly_time($equipment_dataArray->delivery_time/ACCELERATOR_FACTOR, '');      // Displays the friendly time for delivery (hours, minutes...)
                }

                
                // parameters from generic building
                $data['equipmentName'][$i] = $equipment_dataArray->$name_language;
                $data['equipmentCost'][$i] = number_format($equipment_dataArray->buying_cost, 0, ',', ' ');
                $data['equipmentReputation'][$i] = number_format($equipment_dataArray->reputation, 0, ',', ' ');
                $data['coverage'][$i] = number_format($equipment_dataArray->capacity, 0, ',', ' ');
                $data['equipment_type'] = $equipment_type;
                
                if ($count_this_equipment_under_delivery == 0) { // If the equipment is not under delivery nor upgrade
                    if ($i == '1'){         // for the first level, we allow the BUY button
                        $data['equipmentButton'][$i] = '<td>'.$resell_button.'<a href="'.base_url().$equipment_type.'_controller/buy_equipment/'.$currentResortID.'/'.$type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('common_equipment')['buy'].'</button></a></td>';
                    }
                    else if ($i > '1'){         // For the other levels
                      if ($count_num_equipment_previous_level == '0') {      // If the previous one is NOT purchased, we DISABLE upgrade
                            $data['equipmentButton'][$i] = '<td>'.$resell_button.'<button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';  
                        }
                        else if ($count_num_equipment_previous_level >= '1') {       // If the previous one is purchased, we allow upgrade
                            $data['equipmentButton'][$i] = '<td>'.$resell_button.'<a href="'.base_url().$equipment_type.'_controller/upgrade_equipment/'.$currentResortID.'/'.$type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a></td>';  
                        }  
                    }
                }
                else {          // If the equipment is under delivery or upgrade
                    if ($count_this_equipment_level_under_delivery == '0') { // If the current level is NOT under delivery/upgrade
                        if ($i == '1'){ // for the first level, we DISABLE "BUY"
                            $data['equipmentButton'][$i] = '<td>'.$resell_button.'<button class="btn btn-warning disabled">'.$this->lang->line('common_equipment')['buy'].'</button></td>';
                        }
                        else if ($i > '1'){ // For the other levels, we DISABLE "Upgrade"
                            $data['equipmentButton'][$i] = '<td>'.$resell_button.'<button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';  
                        }
                    }
                    else if ($count_this_equipment_level_under_delivery > '0'){      // If the current level is under delivery/upgrade
                        if ($i == '1'){     // We display DELIVERING for first level
                            $data['equipmentButton'][$i] = '<td>'.$resell_button.'<button class="btn btn-warning disabled">'.$this->lang->line('common_equipment')['delivering'].'</button>'.$button_level[$i].'</td>';
                        }
                        else if ($i > '1'){     // We display UPGRADING for the other levels
                            $data['equipmentButton'][$i] = '<td>'.$resell_button.'<button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrading'].'</button>'.$button_level[$i].'</td>';  
                        }
                    }
                }
            }
        }
        return $data;
    }
    
    
    public function assignEquipment($type, $type_id, $currentResortID, $equipment_type){
        $data['rowEquipment'] = '';
        $get_purchased_equipment_player_query = $this->equipment_model->get_purchased_equipment_player($currentResortID, $type_id);
        $array_sectors = $this->resort_model->get_sector_access($currentResortID);

        // Build a lookup of drivers assigned to ski buses (indexed by id_item_assigned)
        $name_language = 'name_'.$this->session->userdata('site_lang');
        $drivers_query = $this->staff_model->get_drivers_for_skibuses_DB($currentResortID);
        $drivers_by_bus = [];
        if ($drivers_query->num_rows() > 0) {
            foreach ($drivers_query->result() as $driver_row) {
                if ($driver_row->id_item_assigned !== NULL) {
                    $drivers_by_bus[$driver_row->id_item_assigned] = $driver_row;
                }
            }
        }

        $total_delivered = 0;
        $total_operational = 0;
        $rows_html = '';

        if ($get_purchased_equipment_player_query->num_rows() > 0) {                // the player has already purchased some equipment
            foreach ($get_purchased_equipment_player_query->result() as $row){
                if ($row->delivered != '1') {
                    $pre_tooltip = '<span class="tooltip tooltip-bottom" style="display:inline;" data-tip="'.$this->lang->line('common_equipment')['come_back_delivered'].'">';
                    $disabled_attr = 'disabled';
                    $post_tooltip = '</span>';
                }
                else {
                    $post_tooltip = '';
                    $disabled_attr = '';
                    $pre_tooltip = '';
                }

                // Determine driver assigned to this bus
                $driver_assigned = isset($drivers_by_bus[$row->id_purchased_equipments]) ? $drivers_by_bus[$row->id_purchased_equipments] : NULL;

                // Determine operational status (only for delivered buses)
                $status_badge = '';
                if ($row->delivered == '1') {
                    $total_delivered++;
                    $has_sector = ($row->assigned_to_sector !== NULL && $row->assigned_to_sector !== '');
                    $has_driver = ($driver_assigned !== NULL);
                    if (!$has_driver && !$has_sector) {
                        $status_badge = $this->lang->line('skibus')['status_idle'];
                    } elseif ($has_sector && $has_driver) {
                        $total_operational++;
                        $status_badge = $this->lang->line('skibus')['status_operational'];
                    } elseif (!$has_driver) {
                        $status_badge = $this->lang->line('skibus')['status_no_driver'];
                    } else {
                        $status_badge = $this->lang->line('skibus')['status_no_sector'];
                    }
                }

                // Build driver info display
                if ($driver_assigned !== NULL) {
                    $driver_name = $driver_assigned->$name_language;
                    $driver_display = $this->lang->line('skibus')['assigned_driver'].': <b>'.htmlspecialchars($driver_name, ENT_QUOTES).'</b> ('.$driver_assigned->efficiency.'%)';
                } else {
                    $driver_display = '<i>'.$this->lang->line('skibus')['no_driver'].'</i>';
                }

                $rename_icon = '&nbsp;<a href="#" class="rename-skibus-btn" data-id="'.$row->id_purchased_equipments.'" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['edit_name'].'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
                $rename_form = '<span class="skibus-rename-form" id="skibus_rename_form_'.$row->id_purchased_equipments.'" style="display:none;">'
                    .'<input type="text" class="input input-sm" id="skibus_rename_input_'.$row->id_purchased_equipments.'" value="'.htmlspecialchars($row->custom_name, ENT_QUOTES).'" maxlength="50">'
                    .'&nbsp;<button class="btn btn-sm btn-success skibus-rename-submit" data-id="'.$row->id_purchased_equipments.'">&#10003;</button>'
                    .'&nbsp;<button class="btn btn-sm btn-secondary skibus-rename-cancel" data-id="'.$row->id_purchased_equipments.'">&#10005;</button>'
                    .'</span>';
                $rows_html .= '<p><b><span class="skibus-name-display" id="skibus_name_display_'.$row->id_purchased_equipments.'">'.$row->custom_name.'</span>'.$rename_icon.$rename_form.'</b>';
                $rows_html .= ' '.$status_badge;
                $rows_html .= '<br>'.$driver_display;
                $rows_html .= '<br><b>'.$this->lang->line('common_equipment')['assigned_to'].':</b> ';
                $rows_html .= $pre_tooltip.'<select class="select select-sm" '.$disabled_attr.' data-id_purchased_equipments="'.$row->id_purchased_equipments.'" data-type="'.$type.'" id="'.$type.'_assigned_sector_'.$row->id_purchased_equipments.'">';
                $associated_items = $this->staff_model->get_accessible_sectors($currentResortID);
                if ($associated_items->num_rows() > 0) {                // the player has accessible sectors
                    if ($row->assigned_to_sector == NULL) {    // If not assigned to anything
                        $rows_html .= '<option selected value=""></option>';
                    }
                    foreach ($associated_items->result() as $associated_items_row){
                        for ($i = 0; $i < count($array_sectors); $i++) {
                            if ($row->assigned_to_sector == $i) {   // If assigned to current sector
                                $selected_status = 'selected';
                            }
                            else {      // If not assigned to current
                                $selected_status = '';
                            }
                            $sector_access_array = $this->resort_model->get_sector_access($currentResortID); // returns: [0] => NULL, [1] => [1], [2] => [2]
                            if (isset($sector_access_array[$i]) && $sector_access_array[$i] == $i){
                                $rows_html .= '<option '.$selected_status.' value="'.$i.'">'.$this->lang->line('home')['sector'].$i.'</option>';
                            }
                        }
                    }
                }
                $generic_equipment_info = $this->equipment_model->get_generic_equipment_data($type_id, $row->level);
                $generic_equipment_info_array = $generic_equipment_info->row();
                $rows_html .= '</select>'.$post_tooltip.'<span id="result_'.$type.'_assigned_sector_'.$row->id_purchased_equipments.'"></span> '.$this->lang->line($equipment_type)['capacity_text'].' '.$generic_equipment_info_array->capacity.' '.$this->lang->line($equipment_type)['tourists'].'.</p>';
            }
        }

        // Build summary block (shown above the individual bus rows)
        if ($total_delivered > 0) {
            $data['rowEquipment'] .= '<div class="alert alert-info"><b>'.$this->lang->line('skibus')['summary_title'].':</b> '
                .$total_delivered.' '.$this->lang->line('skibus')['summary_total'].', '
                .$total_operational.' '.$this->lang->line('skibus')['summary_operational'].'.</div>';
        }
        $data['rowEquipment'] .= $rows_html;

        $data['test'] = '';
        return $data;
    }
 
    /**
     * buy_equipment       Buys the first level of the equipment
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type            Type of the equipment (1=groomer, 2=bus...)
     * @param type $level           Level to buy, usually 1 for first time
     */
    public function buy_equipment($currentResortID, $type, $level = '1'){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
                    
        $name_language = 'name_'.$this->session->userdata('site_lang');
        // Gets the id_equipment and generic info
        $equipment_generic_info_data = $this->equipment_model->get_generic_equipment_data($type, $level);
        if ($equipment_generic_info_data->num_rows() > 0) {                // the generic equipment exists in the DB (always!)
            $equipment_generic_info_dataArray = $equipment_generic_info_data->row();
            $id_equipment = $equipment_generic_info_dataArray->id_equipment;
        }
        
        // counts how many equipments under delivery for any level (to prevent buying)
        $count_this_equipment_under_delivery = count_this_equipment_level($type, '', '0');
        
        // Detects if the page was refreshed and prevents multiple entries
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if(!$pageWasRefreshed ) {
            if ($count_this_equipment_under_delivery == 0) {                         // If not under delivery for this equipment, we are allowed to buying more
                $cost_equipment = $equipment_generic_info_dataArray->buying_cost;        // cost of the equipment
                $gain_reputation = $equipment_generic_info_dataArray->reputation;    // reputation to gain
                $cash_player = $this->users_model->get_cash_player();                     // Get how much cash the player has (in dollar)
                $money_after_payment = $cash_player - $cost_equipment;                    // we calculate how much the player will have left after the payment
                if ($money_after_payment >= 0) {                                         // If enough cash
                    if ($removeCashQuery = $this->users_model->pay_item($cost_equipment, $cash_player)){      //the paiment for the equipment has been taken
                        $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                        $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                        // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                        $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_equipment, 'cost_purchases');
                        $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_equipment, 'expenses');
                    }
                    $add_reputation = $this->users_model->add_reputation($gain_reputation);
                    $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after byuing
                    $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                    // We prepare the purchase data
                    $end_delivery = calculate_end_delivery($type, $level);  // calculate the end of the delivery
                    $data_buy = array (
                        'id_resort' => $currentResortID,
                        'id_equipment' => $id_equipment,       // ID of the generic equipment in the game_equipments table                    
                        'type' => $type,                    //  ID type of the equipment
                        'level' => '1',                     // first level for purchase
                        'end_delivery' => $end_delivery,
                        'delivered' => '0'
                    );
                    $insert_id = $this->equipment_model->buy_equipment_db($data_buy);   // DB request to purchase the equipment
                    if ($insert_id) {
                        $custom_name_to_insert = $equipment_generic_info_dataArray->$name_language.' '.$insert_id;
                        $data_name = array (
                            'custom_name' => $custom_name_to_insert
                        );
                        $add_custom_name_from_ID = $this->equipment_model->upgrade_equipment_custom_name_db($currentResortID, $insert_id , $data_name);
                        $data['infoMessage'] = 'equipment_purchased';
                        $this->session->set_flashdata('update_token', time());
                        $currentUserID = $this->users_model->get_user_id();
                        $data_ach = array (
                            'id_resort' => $currentResortID,
                            'id_equipment' => $id_equipment,       
                            'item' => 'equipment',       
                            'type' => '2',       
                            'level' => '1'
                        );
                        $call_achievements_check = call_achievements_check($data_ach, 'buy');
                        $call_achievements_check = call_achievements_check($data_ach2 = array('id_resort' => $currentResortID, 'quantity' => $cost_equipment), 'buy_amount');   // Check spending achievements
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['equipment'], 'data' => $custom_name_to_insert.$this->lang->line('logs')['ordered']) );   // Add a log row to the game_player_logs table
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['equipment'], 'data' => $custom_name_to_insert.$this->lang->line('logs')['ordered']) );   // Add a log row to the game_player_logs table
                    }
                    else {
                        $data['infoMessage'] = 'equipment_not_purchased';
                    }
                }
                else {
                    $data['infoMessage'] = 'not_enough_money';
                }
            }
            else {      // Already ongoing delivery (display info message)
                $data['infoMessage'] = 'equipment_one_at_a_time';
            }
        }
        else {  // This is in case we reload the page. No need to display anything but "data" needs to exist
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }
    
    /**
     * upgrade_equipment     Upgrades the equipment level
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type            Type of the equipment (1=groomer, 2=bus...)
     * @param type $level           Level to upgrade to
     */
    public function upgrade_equipment($currentResortID, $type, $level){
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // Detects if the page was refreshed and prevents multiple entries
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        if(!$pageWasRefreshed ) {
            // Gets the id_equipment and generic info
            $equipment_generic_info_data = $this->equipment_model->get_generic_equipment_data($type, $level);     
            if ($equipment_generic_info_data->num_rows() > 0) {                // the generic equipment exists in the DB (always!)
                $equipment_generic_info_dataArray = $equipment_generic_info_data->row();
            }

            // counts how many equipments under delivery for any level (to prevent upgrade)
            $count_this_equipment_under_delivery = count_this_equipment_level($type, '', '0');

            // Checks if the player has already built the same equipment but previous level (to allow upgrade of current level)
            $count_num_equipment_previous_level = count_this_equipment_level($type, $level-1);
            $get_id_equipment_previous_level = $this->equipment_model->get_this_equipment_level_db($currentResortID, $type, $level-1);
            if ($get_id_equipment_previous_level->num_rows() > 0) {
                $get_id_equipment_previous_level_array = $get_id_equipment_previous_level->row();
                $id_to_be_updated = $get_id_equipment_previous_level_array->id_purchased_equipments;
            }

            // Only if previous level is already purchased
            if ($count_num_equipment_previous_level >= '1') {
                if ($count_this_equipment_under_delivery == 0) {                     // If not under delivery for this equipment, we are allowed to upgrade
                    $cost_equipment = $equipment_generic_info_dataArray->buying_cost;    // cost of the equipment
                    $gain_reputation = $equipment_generic_info_dataArray->reputation;    // reputation to gain
                    $cash_player = $this->users_model->get_cash_player();                // Get how much cash the player has (in dollar)
                    $money_after_payment = $cash_player - $cost_equipment;               // we calculate how much the player will have left after the payment
                    if ($money_after_payment >= 0) {                                    // If enough cash
                        if ($removeCashQuery = $this->users_model->pay_item($cost_equipment, $cash_player)){      //the paiment for the equipment has been taken
                            $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                            $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                            // Adds the expense to the game_resort_cost_purchases and game_resort_expenses tables
                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_equipment, 'cost_purchases');
                            $add_cost_history_table = add_cost_stat_table($currentResortID, $cost_equipment, 'expenses');
                        }
                        $add_reputation = $this->users_model->add_reputation($gain_reputation);
                        $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after purchased/upgrade
                        $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)
                        // We prepare the purchase data
                        $end_delivery = calculate_end_delivery($type, $level);
                        $name_language = 'name_'.$this->session->userdata('site_lang');
                        $new_id_equipment = $equipment_generic_info_dataArray->id_equipment;
                        $custom_name_to_insert = $equipment_generic_info_dataArray->$name_language.' '.$id_to_be_updated;
                        $data_upg = array (
                            'level' => $level,                    // we update with the new level
                            'end_delivery' => $end_delivery,      // We update the new delivery time
                            'delivered' => '0',                    // The equipment is not delivered
                            'custom_name' => $custom_name_to_insert,        // Update the name to new level
                            'id_equipment' => $new_id_equipment        // Update the id_equipment to new level
                        );
                        $data_ach = array (
                            'id_resort' => $currentResortID,       
                            'type' => 'skibus',        
                            'level' => $level
                        );
                        $upgrade_equipment = $this->equipment_model->upgrade_equipment_db($currentResortID, $type, $level-1 , $data_upg, $id_to_be_updated);    // Build request in the DB
                        $call_achievements_check = call_achievements_check($data_ach, 'upgrade');
                        $call_achievements_check = call_achievements_check($data_ach2 = array('id_resort' => $currentResortID, 'quantity' => $cost_equipment), 'upgrade_amount');   // Check spending achievements
                        if ($upgrade_equipment) {
                            $data['infoMessage'] = 'equipment_upgraded';
                            $currentUserID = $this->users_model->get_user_id();
                            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$custom_name_to_insert.' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$custom_name_to_insert.' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table
                        }
                        else {
                            $data['infoMessage'] = 'equipment_not_upgraded';
                        }
                    }
                    else {
                        $data['infoMessage'] = 'not_enough_money';
                    }
                }
                else {      // If there is already a delivery ongoing (display info message)
                    $data['infoMessage'] = 'equipment_one_at_a_time';
                }
            }
            else {      // Need to buy previous level first (display info message)
                $data['infoMessage'] = 'equipment_not_built_previous';
            }
        }
        else {  // This is in case we reload the page. No need to display anything but "data" needs to exist
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }
   
    
/**
     * sell_equipment       Sells the equipment
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type            Type of the equipment (1 = groomer, 2=bus...)
     * @param type $equipment_type            Type of the equipment (groomer, bus...)
     * @param type $level           Level to sell
     */
    public function sell_equipment(){
        
        $currentResortID = trim($this->input->post('currentResortId', TRUE));
        $type = trim($this->input->post('type', TRUE));
        $equipment_type = trim($this->input->post('equipment_type', TRUE));
        $level = trim($this->input->post('i', TRUE));
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
                    
        $name_language = 'name_'.$this->session->userdata('site_lang');
        // Gets the id_equipment and generic info for the equipment.
        $equipment_generic_info_data = $this->equipment_model->get_generic_equipment_data($type, $level);
        if ($equipment_generic_info_data->num_rows() > 0) {                // the generic building exists in the DB (always!)
            $equipment_generic_info_dataArray = $equipment_generic_info_data->row();
        }
        
        // counts how many equipments under delivery for any level (to prevent buying more)
        $count_this_equipment_under_delivery = count_this_equipment_level($type, '', '0');
        
        // Detects if the page was refreshed and prevents multiple entries
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if(!$pageWasRefreshed ) {
            if ($count_this_equipment_under_delivery == 0) {                         // If not under delivery for this equipment, we are allowed to sell
                $resell_price = $equipment_generic_info_dataArray->buying_cost/2;        // cost of the equipment divided by 2 = resell price
                $cash_player = $this->users_model->get_cash_player();                     // Get how much cash the player has (in dollar)
                    if ($removeCashQuery = $this->users_model->sell_item($resell_price, $cash_player)){      //the money for the equipment has been added
                        $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                        $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                        // Adds the revenue to the revenue table
                        $add_revenue_history_query_main_table = add_revenue_stat_table($currentResortID, $resell_price, 'revenue');
                        $add_revenue_history_query_main_table = add_revenue_stat_table($currentResortID, $resell_price, 'rev_other');
                    }
                    $id_to_sell = $this->equipment_model->select_id_to_sell($currentResortID, $type, $level);   // getting the ID to sell, so we can re-use later
                    $delete_equipment = $this->equipment_model->sell_equipment_db($currentResortID, $id_to_sell);   // DB request to sell the equipment
                    if ($delete_equipment) {
                        $data['infoMessage'] = 'equipment_sold';
                        $this->session->set_flashdata('update_token', time());
                        $currentUserID = $this->users_model->get_user_id();
                        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                        $column_lang = 'name_'.$player_preferred_lang;
                        $null_assigned_staff = $this->equipment_model->null_assigned_staff_DB($currentResortID, $id_to_sell, $equipment_type);   // set NULL to ID and type is assigned staff
                        $generic_name = $equipment_generic_info_dataArray->$column_lang;
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['equipment'], 'data' => $generic_name.$this->lang->line('logs')['sold']) );   // Add a log row to the game_player_logs table
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['equipment'], 'data' => $generic_name.$this->lang->line('logs')['sold']) );   // Add a log row to the game_player_logs table
                        $count_this_equipment_left = count_this_equipment_level($type, $level);
                        $result_to_return = array('returned' => true, 'left_level' => $count_this_equipment_left, 'infoMessage' => 'equipment_sold');
                    }
                    else {
                        $result_to_return = array('returned' => false);
                    }
            }
            else {      // Already ongoing delivery (display info message)
                 $result_to_return = array('returned' => false);
            }
        }
        else {  // This is in case we reload the page. No need to display anything but "data" needs to exist
            $result_to_return = array('returned' => false);
        }
        echo json_encode ($result_to_return);
    }
    
    
    
    public function edit_assigned_item(){         // Changes the equipment assignation    
        $id_equipment = trim($this->input->post('id_equipment', TRUE));
        $idOfSelectedOption = trim($this->input->post('idSector', TRUE));
        $type = trim($this->input->post('type', TRUE));
        
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);          
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        
            $data_assign = array (
                'assigned_to_sector' => $idOfSelectedOption,       // Type to assign to the staff
            );
            $data_achievement = array (
                'sector_id' => $idOfSelectedOption,
                'type' => $type,
            );
            $edit_assigned_item = $this->equipment_model->edit_assigned_equipment_DB($currentResortID, $id_equipment, $data_assign);
            if ($edit_assigned_item){
                $call_achievements_check = call_achievements_check($data_achievement, 'assign_equipment');
                echo json_encode(array('returned' => true));   
            }
            else
                echo json_encode(array('returned' => false));
    }

    /**
     * rename_equipment     Renames a purchased ski bus equipment item (AJAX)
     */
    public function rename_equipment(){
        $id_purchased_equipments = (int) trim($this->input->post('id_equipment', TRUE));
        $new_name = trim($this->input->post('new_name', TRUE));

        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE

        if ($id_purchased_equipments > 0 && $new_name !== '' && strlen($new_name) <= 50) {
            $data_name = array('custom_name' => $new_name);
            // upgrade_equipment_custom_name_db enforces ownership via WHERE id_resort = $currentResortID
            $update = $this->equipment_model->upgrade_equipment_custom_name_db($currentResortID, $id_purchased_equipments, $data_name);
            if ($update) {
                echo json_encode(array('returned' => true, 'new_name' => $new_name));
            } else {
                echo json_encode(array('returned' => false));
            }
        } else {
            echo json_encode(array('returned' => false));
        }
    }
    
    public function rush($building_type_id, $level){
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
            $building_data = $this->equipment_model->get_time_left_for_equipment_db($currentResortID, $building_type_id, $level);
            
            $building_data_array = $building_data->row();
            
            $end_construction = $building_data_array->end_delivery;
            $timestamp = strtotime($end_construction." UTC");   // return the number of seconds until the end
            $currenttime = time();                                          // current timestamp
            $time_left_value = $timestamp - $currenttime;                   // Time left in seconds

            if ($time_left_value > 0){ 
                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);

                if ($genepis_required_to_rush <= $genepis_available) {
                    $remove_genepis_cost = $this->users_model->remove_genepis_cost_DB($genepis_required_to_rush);
                    $data_log = $this->lang->line('home')['you_have_rushed'].' '.$building_data_array->custom_name.' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'];
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log) );   // Add a log row to the game_player_logs table

                    $complete_construction = $this->building_model->complete_construction_DB($building_data_array->id_purchased_equipments, 'game_purchased_equipments', 'id_purchased_equipments', 'end_delivery');

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