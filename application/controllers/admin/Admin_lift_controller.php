<?php

class Admin_lift_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        // Authentication for admins
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)), 2);
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } else if ($_SERVER['PHP_AUTH_USER'] == ADMIN_USERNAME && $_SERVER['PHP_AUTH_PW'] == ADMIN_PASSWORD){
            // nothing to display
        }
        else {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        }
        // constructor
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('admin_pages',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('lift',$siteLang);
        $ci->lang->load('resort',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        //$this->load->model('admin/admin_lift_model');
        $this->load->model('admin/admin_lift_model');
        $this->load->model('admin/admin_slope_model');
        $this->load->model('item_model');
    }
    
    public function index(){
        
        // Get all information from all the created resorts
        $data_lift = $this->admin_lift_model->get_all_lift_data();
        $total = 0;
        foreach ($data_lift->result() as $value_type){
            $count_lift_data = $this->admin_lift_model->count_lift_db($value_type->id_group, $value_type->level);
            $count_lift_row = $count_lift_data->row();
            $lift_count[$value_type->id_lift] = $count_lift_row->count;
            $total = $total + $count_lift_row->count;
            
            if ($total > 0) {
                $count_lift_data_open = $this->admin_lift_model->count_open_lift_db();
                $count_lift_row_open = $count_lift_data_open->row();
                $total_for_level_open = $count_lift_row_open->count_open;
                $perc_open = number_format(($total_for_level_open/$total)*100, 0, ',', ' ');
            }
            else {
                $total_for_level_open = 0;
                $perc_open = 0;
            }
        }
        
        $table = '<table id="admin_lift_table" align="center" class="table table-responsive achievements small_text11">';
        $table .= '<thead><tr>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_lift'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_group'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_english'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_french'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['level'].'</th>';
            $table .= '<th style="width: 53px;">'.$this->lang->line('admin_page')['type'].'</th>';
            $table .= '<th>'.$this->lang->line('lift')['grip_type'].'</th>';
            $table .= '<th>'.$this->lang->line('lift')['length_speed_column'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['seats'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['cost'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['building_time'].'</th>';
            $table .= '<th>'.$this->lang->line('lift')['throughput'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['rep'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['daily_cost'].'</th>';
            $table .= '<th>#'.$total.' (open:'.$total_for_level_open.' = '.$perc_open.'%)</th>';
            $table .= '<th style="width: 80px;">'.$this->lang->line('admin_page')['Actions'].'</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
            $previous_lift_group = '';
            
            $table .= '</tbody></table>';
        
        $data['table'] = $table;
        $data['title'] = $this->lang->line('admin_page')['liftlist'];
        $data['page_type'] = 'lift';
        $data['main_content'] = 'admin/adminBuildingItemsView';
        $this->load->view('templates/default_admin',$data); 
            
    }
    public function getDataTable(){
        $data_lift = $this->admin_lift_model->get_all_lift_data();
        $data_table = $data_lift->result();
        foreach ($data_table as $value){
            
            $count_lift_data = $this->admin_lift_model->count_lift_db($value->id_group, $value->level);
            $count_lift_row = $count_lift_data->row();
            $total_for_level = $count_lift_row->count;
                        
            $value->total = $total_for_level;
            $value->building_time = display_friendly_time($value->building_time/ACCELERATOR_FACTOR);      // displays friendly time (hours, minutes...);
            $lift_type_data = $this->item_model->get_lift_types_DB($value->lift_type); // MAKE this query properly to return typeonly, (not full select)
            $lift_type_name = $lift_type_data->row();
            $value->lift_type = $lift_type_name->name_english;      
            $grip_type_data = $this->item_model->get_grip_types_DB($value->grip_type); // MAKE this query properly to return typeonly, (not full select)
            $grip_type_name = $grip_type_data->row();
            $value->grip_type = $grip_type_name->name_english;      
        }
        echo json_encode(array('Data' => $data_table));
    }
        
    protected function get_lift_types($lift_type) {
        $lift_types = $this->item_model->get_lift_types_DB();
        $data = '';
        foreach ($lift_types->result() as $array_lift_types) {
            if ($array_lift_types->id_lift_type == $lift_type) {
                $data .= '<option value="'.$array_lift_types->id_lift_type.'" selected>'.$array_lift_types->id_lift_type.': '.$array_lift_types->name_english.'</option>';
            }
            else
                $data .= '<option value="'.$array_lift_types->id_lift_type.'">'.$array_lift_types->id_lift_type.': '.$array_lift_types->name_english.'</option>';
        }
        return $data;
    }
    
    protected function get_grip_types($grip_type) {
        $grip_types = $this->item_model->get_grip_types_DB();
        $data = '';
        foreach ($grip_types->result() as $array_grip_types) {
            if ($array_grip_types->id_grip_type == $grip_type) {
                $data .= '<option value="'.$array_grip_types->id_grip_type.'" selected>'.$array_grip_types->id_grip_type.': '.$array_grip_types->name_english.'</option>';
            }
            else
                $data .= '<option value="'.$array_grip_types->id_grip_type.'">'.$array_grip_types->id_grip_type.': '.$array_grip_types->name_english.'</option>';
        }
        return $data;
    }
    
     
    public function edit_lifts($id_group, $mode){
        $data['mode'] = $mode;
        $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
        $data['lift_array'] = $data_lift->result();
        // dropdown type
        $row_lift_array = $data_lift->row();
        $id_lift_type = $row_lift_array->lift_type;
        $id_grip_type = $row_lift_array->grip_type;
        //$id_lift = $row_lift_array->id_lift;
        $lift_types = $this->get_lift_types($id_lift_type);
        $grip_types = $this->get_grip_types($id_grip_type);
        $data['select_lift_types'] = $lift_types;
        $data['select_grip_types'] = $grip_types;
        if ($mode == 'duplicate') {
            $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
            $data['id_group'] = $max_id_group+1;
        }
        else 
            $data['id_group'] = $id_group;
        $data['page_type'] = 'lift';
        $data['main_content'] = 'admin/editLiftView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function add_new(){
        $data['title'] = $this->lang->line('admin_page')['add_new_lift'];
        $lift_types = $this->get_lift_types(null);
        $grip_types = $this->get_grip_types(null);
        $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
        $data['select_lift_types'] = $lift_types;
        $data['select_grip_types'] = $grip_types;
        $data['max_id_group'] = $max_id_group+1;
        //$data['difficulty_select'] = $this->get_select_difficulty(null);
        $data['main_content'] = 'admin/addLiftView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    
    public function add_new_lift_validation() {
        if (isset ($_POST['add_new_lift'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_group', $this->lang->line('admin_page')['id_group'], 'trim|required|max_length[2]|integer');
                $this->form_validation->set_rules('lift_type', $this->lang->line('admin_page')['type'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('grip_type', $this->lang->line('lift_page')['grip_type'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('name_english[]', $this->lang->line('admin_page')['name_english'], 'trim|required|min_length[3]|max_length[45]');
                $this->form_validation->set_rules('name_french[]', $this->lang->line('admin_page')['name_french'], 'trim|required|min_length[3]|max_length[45]');
                $this->form_validation->set_rules('speed[]', $this->lang->line('lift')['length_speed_column'], 'trim|required|max_length[5]|integer');
                $this->form_validation->set_rules('capacity[]', $this->lang->line('lift')['capacity_seats'], 'trim|required|max_length[2]|integer');
                $this->form_validation->set_rules('building_time[]', $this->lang->line('home')['building_time'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('base_cost[]', $this->lang->line('home')['base_cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('meter_cost[]', $this->lang->line('home')['meter_cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('throughput[]', $this->lang->line('lift')['throughput'], 'trim|required|max_length[5]|integer');
                $this->form_validation->set_rules('reputation[]', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('daily_cost[]', $this->lang->line('home')['daily_cost'], 'trim|required|max_length[10]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_lift_error_id_group'] = form_error('id_group');
                    $data['add_lift_error_lift_type'] = form_error('lift_type');
                    $data['add_lift_error_grip_type'] = form_error('grip_type');
                    $data['add_lift_error_name_english'] = form_error('name_english[]');
                    $data['add_lift_error_name_french'] = form_error('name_french[]');
                    $data['add_lift_error_speed'] = form_error('speed[]');
                    $data['add_lift_error_capacity'] = form_error('capacity[]');
                    $data['add_lift_error_building_time'] = form_error('building_time[]');
                    $data['add_lift_error_base_cost'] = form_error('base_cost[]');
                    $data['add_lift_error_meter_cost'] = form_error('meter_cost[]');
                    $data['add_lift_error_throughput'] = form_error('throughput[]');
                    $data['add_lift_error_reputation'] = form_error('reputation[]');
                    $data['add_lift_error_daily_cost'] = form_error('daily_cost[]');
                    $this->session->set_flashdata('msg','');

                    $id_group_page = $this->input->post('id_group_posted', TRUE);
                    $data_lift = $this->admin_lift_model->get_group_lift_data(null);
                    $data['lift_array'] = $data_lift->result();
                    // dropdown type
                    $lift_types = $this->get_lift_types($this->input->post('lift_type', TRUE));
                    $grip_types = $this->get_grip_types($this->input->post('grip_type', TRUE));
                    $data['select_lift_types'] = $lift_types;
                    $data['select_grip_types'] = $grip_types;
                    $data['id_group'] = $id_group_page;
                    $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
                    $data['max_id_group'] = $max_id_group+1;
                    $data['title'] = $this->lang->line('admin_page')['add_new_lift'];
                    $data['page_type'] = 'lift';
                    $data['main_content'] = 'admin/addLiftView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_group = $this->input->post('id_group', TRUE);
                    $lift_type = $this->input->post('lift_type', TRUE);
                    $grip_type = $this->input->post('grip_type', TRUE);
                    $name_english[] = array_map('trim',$this->input->post('name_english[]', TRUE));
                    $name_french[] = array_map('trim',$this->input->post('name_french[]', TRUE));
                    $building_time[] = array_map('trim',$this->input->post('building_time[]', TRUE));
                    $base_cost[] = array_map('trim',$this->input->post('base_cost[]', TRUE));
                    $meter_cost[] = array_map('trim',$this->input->post('meter_cost[]', TRUE));
                    $speed[] = array_map('trim',$this->input->post('speed[]', TRUE));
                    $reputation[] = array_map('trim',$this->input->post('reputation[]', TRUE));
                    $capacity[] = array_map('trim',$this->input->post('capacity[]', TRUE));
                    $throughput[] = array_map('trim',$this->input->post('throughput[]', TRUE));
                    $daily_cost[] = array_map('trim',$this->input->post('daily_cost[]', TRUE));
                    
                    for ($i=0; $i<3; $i++) {
                        $data_insert = array (
                            'id_group' => $id_group,
                            'level' => $i+1,
                            'lift_type' => $lift_type,
                            'grip_type' => $grip_type,
                            'name_english' => $name_english[0][$i],
                            'name_french' => $name_french[0][$i],
                            'building_time' => $building_time[0][$i],
                            'base_cost' => $base_cost[0][$i],
                            'meter_cost' => $meter_cost[0][$i],
                            'speed' => $speed[0][$i],
                            'reputation' => $reputation[0][$i],
                            'capacity' => $capacity[0][$i],
                            'throughput' => $throughput[0][$i],
                            'daily_cost' => $daily_cost[0][$i]
                        );
                        $query[] = $this->admin_slope_model->add_item_admin($data_insert, 'game_lifts');       //Add lift to game_lifts table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succedded
                    
                            //$id_lift[] = $this->input->post('id_lift[]', TRUE);
                            $id_group = $this->input->post('id_group_posted', TRUE);
                            $grip_types = $this->get_grip_types($this->input->post('grip_type', TRUE));
                            $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
                            $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['lift_added'].'</div>');
                            $data['lift_array'] = $data_lift->result();
                            $lift_types = $this->get_lift_types($lift_type);
                            $grip§_types = $this->get_lift_types($grip_type);
                            $data['select_lift_types'] = $lift_types;
                            $data['select_grip_types'] = $grip_types;
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['add_new_lift'];
                            $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
                            $data['max_id_group'] = $max_id_group+1;
                            $data['page_type'] = 'lift';
                            $data['main_content'] = 'admin/addLiftView';
                            $this->load->view('templates/default_admin',$data);
                        }
                        else {
                            //$id_lift = $this->input->post('id_lift', TRUE);
                            $id_group = $this->input->post('id_group_posted', TRUE);
                            $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['lift_not_added_all_failed'].'</div>');
                            $data['lift_array'] = $data_lift->result();
                            $lift_types = $this->get_lift_types($lift_type);
                            $grip§_types = $this->get_lift_types($grip_type);
                            $data['select_lift_types'] = $lift_types;
                            $data['select_grip_types'] = $grip_types;
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['add_new_lift'];
                            $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
                            $data['max_id_group'] = $max_id_group+1;
                            $data['page_type'] = 'lift';
                            $data['main_content'] = 'admin/addLiftView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {
                        //$id_lift = $this->input->post('id_lift', TRUE);
                        $id_group = $this->input->post('id_group_posted', TRUE);
                        $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['lift_not_added_one_failed'].'</div>');
                        $data['lift_array'] = $data_lift->result();
                        $lift_types = $this->get_lift_types($this->input->post('lift_type', TRUE));
                        $grip_types = $this->get_grip_types($this->input->post('grip_type', TRUE));
                        $data['select_lift_types'] = $lift_types;
                        $data['select_grip_types'] = $grip_types;
                        $data['id_group'] = $id_group;
                        $data['title'] = $this->lang->line('admin_page')['add_new_lift'];
                        $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
                        $data['max_id_group'] = $max_id_group+1;
                        $data['page_type'] = 'lift';
                        $data['main_content'] = 'admin/addLiftView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_lift_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            //$id_lift = $this->input->post('id_lift', TRUE);
            $id_group = $this->input->post('id_group_posted', TRUE);
            $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['lift_not_added_no_post'].'</div>');
            $data['lift_array'] = $data_lift->result();
            $lift_types = $this->get_lift_types(null);
            $grip_types = $this->get_grip_types(null);
            $data['select_lift_types'] = $lift_types;
            $data['select_grip_types'] = $grip_types;
            $data['id_group'] = $id_group;
            $data['title'] = $this->lang->line('admin_page')['add_new_lift'];
            $max_id_group = $this->admin_lift_model->get_max_value_DB('id_group', 'game_lifts');
            $data['max_id_group'] = $max_id_group+1;
            $data['page_type'] = 'lift';
            $data['main_content'] = 'admin/addLiftView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    public function update_lift_admin() {
        if (isset ($_POST['edit_lift_admin'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_lift[]', $this->lang->line('admin_page')['id_lift'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('id_group', $this->lang->line('admin_page')['id_group'], 'trim|required|max_length[2]|integer');
                $this->form_validation->set_rules('lift_type', $this->lang->line('admin_page')['type'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('grip_type', $this->lang->line('lift_page')['grip_type'], 'trim|required|max_length[45]|integer');
                $this->form_validation->set_rules('name_english[]', $this->lang->line('admin_page')['name_english'], 'trim|required|min_length[3]|max_length[45]');
                $this->form_validation->set_rules('name_french[]', $this->lang->line('admin_page')['name_french'], 'trim|required|min_length[3]|max_length[45]');
                $this->form_validation->set_rules('speed[]', $this->lang->line('lift')['length_speed_column'], 'trim|required|max_length[5]|integer');
                $this->form_validation->set_rules('capacity[]', $this->lang->line('lift')['capacity_seats'], 'trim|required|max_length[2]|integer');
                $this->form_validation->set_rules('building_time[]', $this->lang->line('home')['building_time'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('base_cost[]', $this->lang->line('home')['base_cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('meter_cost[]', $this->lang->line('home')['meter_cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('throughput[]', $this->lang->line('lift')['throughput'], 'trim|required|max_length[5]|integer');
                $this->form_validation->set_rules('reputation[]', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('daily_cost[]', $this->lang->line('home')['daily_cost'], 'trim|required|max_length[10]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_lift_error_id_lift'] = form_error('id_lift[]');
                    $data['edit_lift_error_id_group'] = form_error('id_group');
                    $data['edit_lift_error_lift_type'] = form_error('lift_type');
                    $data['edit_lift_error_grip_type'] = form_error('grip_type');
                    $data['edit_lift_error_name_english'] = form_error('name_english[]');
                    $data['edit_lift_error_name_french'] = form_error('name_french[]');
                    $data['edit_lift_error_speed'] = form_error('speed[]');
                    $data['edit_lift_error_capacity'] = form_error('capacity[]');
                    $data['edit_lift_error_building_time'] = form_error('building_time[]');
                    $data['edit_lift_error_base_cost'] = form_error('base_cost[]');
                    $data['edit_lift_error_meter_cost'] = form_error('meter_cost[]');
                    $data['edit_lift_error_throughput'] = form_error('throughput[]');
                    $data['edit_lift_error_reputation'] = form_error('reputation[]');
                    $data['edit_lift_error_daily_cost'] = form_error('daily_cost[]');
                    $this->session->set_flashdata('msg','');

                    $id_group_page = $this->input->post('id_group_posted', TRUE);
                    $data_lift = $this->admin_lift_model->get_group_lift_data($id_group_page);
                    $data['lift_array'] = $data_lift->result();
                    // dropdown type
                    $row_lift_array = $data_lift->row();
                    $id_lift_type = $row_lift_array->lift_type;
                    $id_grip_type = $row_lift_array->grip_type;
                    $lift_types = $this->get_lift_types($id_lift_type);
                    $grip_types = $this->get_grip_types($id_grip_type);
                    $data['select_lift_types'] = $lift_types;
                    $data['select_grip_types'] = $grip_types;
                    $data['id_group'] = $id_group_page;
                    $data['mode'] = $this->input->post('mode', TRUE);
                    $data['title'] = $this->lang->line('admin_page')['liftlist'];
                    $data['page_type'] = 'lift';
                    $data['main_content'] = 'admin/editLiftView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_lift[] = array_map('trim',$this->input->post('id_lift[]', TRUE));
                    $id_group = $this->input->post('id_group', TRUE);
                    $lift_type = $this->input->post('lift_type', TRUE);
                    $grip_type = $this->input->post('grip_type', TRUE);
                    $name_english[] = array_map('trim',$this->input->post('name_english[]', TRUE));
                    $name_french[] = array_map('trim',$this->input->post('name_french[]', TRUE));
                    $building_time[] = array_map('trim',$this->input->post('building_time[]', TRUE));
                    $base_cost[] = array_map('trim',$this->input->post('base_cost[]', TRUE));
                    $meter_cost[] = array_map('trim',$this->input->post('meter_cost[]', TRUE));
                    $speed[] = array_map('trim',$this->input->post('speed[]', TRUE));
                    $reputation[] = array_map('trim',$this->input->post('reputation[]', TRUE));
                    $capacity[] = array_map('trim',$this->input->post('capacity[]', TRUE));
                    $throughput[] = array_map('trim',$this->input->post('throughput[]', TRUE));
                    $daily_cost[] = array_map('trim',$this->input->post('daily_cost[]', TRUE));
                    
                    if ($this->input->post('mode', TRUE) == 'edit') {           // Edit mode
                        for ($i=0; $i<3; $i++) {
                            $query[] = $this->admin_lift_model->update_lift_admin($id_lift[0][$i], $id_group, $i+1, $lift_type, $grip_type, $name_english[0][$i], $name_french[0][$i], $building_time[0][$i], $base_cost[0][$i], $meter_cost[0][$i], $speed[0][$i], $reputation[0][$i], $capacity[0][$i], $throughput[0][$i], $daily_cost[0][$i]);       //update lift table
                        }
                    }
                    else if ($this->input->post('mode', TRUE) == 'duplicate'){      // Duplicate mode
                        for ($i=0; $i<3; $i++) {
                            $data_insert = array (
                                'id_group' => $id_group,
                                'level' => $i+1,
                                'lift_type' => $lift_type,
                                'grip_type' => $grip_type,
                                'name_english' => $name_english[0][$i],
                                'name_french' => $name_french[0][$i],
                                'building_time' => $building_time[0][$i],
                                'base_cost' => $base_cost[0][$i],
                                'meter_cost' => $meter_cost[0][$i],
                                'speed' => $speed[0][$i],
                                'reputation' => $reputation[0][$i],
                                'capacity' => $capacity[0][$i],
                                'throughput' => $throughput[0][$i],
                                'daily_cost' => $daily_cost[0][$i]
                            );
                            $query[] = $this->admin_slope_model->add_item_admin($data_insert, 'game_lifts');       //Add lift to game_lifts table
                        }
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succeedded
                    
                            //$id_lift[] = $this->input->post('id_lift[]', TRUE);
                            $id_group = $this->input->post('id_group_posted', TRUE);
                            $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
                            $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['lift_updated'].'</div>');
                            $data['lift_array'] = $data_lift->result();
                            // dropdown type
                            $row_lift_array = $data_lift->row();
                            $id_lift_type = $row_lift_array->lift_type;
                            $id_grip_type = $row_lift_array->grip_type;
                            $lift_types = $this->get_lift_types($id_lift_type);
                            $grip_types = $this->get_grip_types($id_grip_type);
                            $data['select_lift_types'] = $lift_types;
                            $data['mode'] = $this->input->post('mode', TRUE);
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['liftlist'];
                            $data['page_type'] = 'lift';
                            $data['main_content'] = 'admin/editLiftView';
                            $this->load->view('templates/default_admin',$data);
                        }
                        else {
                            //$id_lift = $this->input->post('id_lift', TRUE);
                            $id_group = $this->input->post('id_group_posted', TRUE);
                            $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['lift_not_updated_all_failed'].'</div>');
                            $data['lift_array'] = $data_lift->result();
                            // dropdown type
                            $row_lift_array = $data_lift->row();
                            $id_lift_type = $row_lift_array->lift_type;
                            $id_grip_type = $row_lift_array->grip_type;
                            $lift_types = $this->get_lift_types($id_lift_type);
                            $grip_types = $this->get_lift_types($id_grip_type);
                            $data['select_lift_types'] = $lift_types;
                            $data['select_grip_types'] = $grip_types;
                            $data['mode'] = $this->input->post('mode', TRUE);
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['liftlist'];
                            $data['page_type'] = 'lift';
                            $data['main_content'] = 'admin/editLiftView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {
                        $id_group = $this->input->post('id_group_posted', TRUE);
                        $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['lift_not_updated_one_failed'].'</div>');
                        $data['lift_array'] = $data_lift->result();
                        // dropdown type
                        $row_lift_array = $data_lift->row();
                        $id_lift_type = $row_lift_array->lift_type;
                        $id_grip_type = $row_lift_array->grip_type;
                        $lift_types = $this->get_lift_types($id_lift_type);
                        $grip_types = $this->get_lift_types($id_grip_type);
                        $data['select_lift_types'] = $lift_types;
                        $data['select_grip_types'] = $grip_types;
                        $data['mode'] = $this->input->post('mode', TRUE);
                        $data['id_group'] = $id_group;
                        $data['title'] = $this->lang->line('admin_page')['liftlist'];
                        $data['page_type'] = 'lift';
                        $data['main_content'] = 'admin/editLiftView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_lift_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            //$id_lift = $this->input->post('id_lift', TRUE);
            $id_group = $this->input->post('id_group_posted', TRUE);
            $data_lift = $this->admin_lift_model->get_group_lift_data($id_group);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['lift_not_updated_no_post'].'</div>');
            $data['lift_array'] = $data_lift->result();
            // dropdown type
            $row_lift_array = $data_lift->row();
            $id_lift_type = $row_lift_array->lift_type;
            $id_grip_type = $row_lift_array->grip_type;
            $lift_types = $this->get_lift_types($id_lift_type);
            $grip_types = $this->get_grip_types($id_grip_type);
            $data['select_lift_types'] = $lift_types;
            $data['select_grip_types'] = $grip_types;
            $data['mode'] = $this->input->post('mode', TRUE);
            //$data['id_lift'] = $id_lift;
            $data['id_group'] = $id_group;
            $data['title'] = $this->lang->line('admin_page')['liftlist'];
            $data['page_type'] = 'lift';
            $data['main_content'] = 'admin/editLiftView';
            $this->load->view('templates/default_admin',$data);
        }
    }
}

?>