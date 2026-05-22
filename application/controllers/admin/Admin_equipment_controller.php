<?php

class Admin_equipment_controller extends CI_Controller{
    
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
        $ci->lang->load('equipment',$siteLang);
        $ci->lang->load('building',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_equipment_model');
        $this->load->model('admin/admin_slope_model');
    }
    
    public function index(){
        
        // Get all information from all the created resorts
        $equipment_type = array ('groomer', 'skibus');
        $equipment_type_2 = array ('1', '2');
        $data_equipment = $this->admin_equipment_model->get_all_equipment_data();
        $total = 0;
        $total_assigned = 0;
        foreach ($equipment_type_2 as $value_type){
            $count_equipment_data = $this->admin_equipment_model->count_equipment_db($value_type);
            $count_equipment_row = $count_equipment_data->result();
            foreach ($count_equipment_row as $count_equipment_array) {
                $equipment_count[$count_equipment_array->id_equipment][$count_equipment_array->level] = $count_equipment_array->count;
                $total = $total + $count_equipment_array->count;
            }
            $count_assigned_equipment_data = $this->admin_equipment_model->count_assigned_equipment_db($value_type);
            $count_assigned_equipment_row = $count_assigned_equipment_data->result();
            foreach ($count_assigned_equipment_row as $count_assigned_equipment_array) {
                $equipment_assigned_count[$count_assigned_equipment_array->id_equipment][$count_assigned_equipment_array->level] = $count_assigned_equipment_array->count;
                $total_assigned = $total_assigned + $count_assigned_equipment_array->count;
            }
        }
        
        $table = '<table align="center" class="table table-responsive achievements myTableLeaderboard  small_text11" >';
        $table .= '<thead><tr>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_equipment'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['type'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_english'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_french'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['level'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['equipment_delivery_time'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['cost'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['reputation'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['capacity'].'</th>';
            $table .= '<th>'.$this->lang->line('building')['max_income'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['daily_cost'].'</th>';
            $table .= '<th># ('.$total.')</th>';
            if ($total == 0)
                $percentage_total = 0;
            else
                $percentage_total = number_format(100*$total_assigned/$total, 0, ',', ' ');
            $table .= '<th>'.$this->lang->line('admin_page')['assigned'].' ('.$total_assigned.') ('.$percentage_total.'%)</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['Actions'].'</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
            $previous_equipment_type = '';
        // For each equipment
        foreach ($data_equipment->result() as $data_array_equipment) {
            $equipment_id = $data_array_equipment->id_equipment;
            $equipment_type_id = $data_array_equipment->type;
            $data_equipment_type = $this->admin_equipment_model->get_specific_equipment_type($equipment_type_id);
            
            if ($previous_equipment_type == '') {
                $class = 'ach_even';
            }
            else if ($previous_equipment_type == $data_equipment_type) {
                $class = $previous_class;
            }
            else if ($previous_class == 'ach_even') {
                $class = 'ach_odd';
            }
            else if ($previous_class == 'ach_odd') {
                $class = 'ach_even';
            }
            
            $table .= '<tr class="'.$class.'" data-id_item="'.$equipment_id.'" data-item_type="equipment">';
            $table .= '<td>'.$equipment_id.'</th>';
            $table .= '<td>'.$data_equipment_type->name_type.'</td>';
            $table .= '<td>'.$data_array_equipment->name_english.'</td>';
            $table .= '<td>'.$data_array_equipment->name_french.'</td>';
            $table .= '<td>'.$data_array_equipment->level.'</td>';
            $table .= '<td>'.display_friendly_time($data_array_equipment->delivery_time/ACCELERATOR_FACTOR).'</td>';
            $table .= '<td>'.number_format($data_array_equipment->buying_cost, 0, ',', ' ').' €</td>';
            $table .= '<td>'.number_format($data_array_equipment->reputation, 0, ',', ' ').'</td>';
            $table .= '<td>'.number_format($data_array_equipment->capacity, 0, ',', ' ').'</td>';
            $table .= '<td>'.number_format($data_array_equipment->max_income, 0, ',', ' ').' €</td>';
            $table .= '<td>'.number_format($data_array_equipment->daily_cost, 0, ',', ' ').' €</td>';
            $table .= '<td>';
            // total column
            if(isset($equipment_count[$equipment_id][$data_array_equipment->level]))
                $table .= number_format($equipment_count[$equipment_id][$data_array_equipment->level], 0, ',', ' ');
            else
                $table .= '0';
            $table .= '</td>';  
            // total assigned column
            $table .= '<td>';
            if(isset($equipment_assigned_count[$equipment_id][$data_array_equipment->level])) {
                $table .= number_format($equipment_assigned_count[$equipment_id][$data_array_equipment->level], 0, ',', ' ');
                if ($equipment_assigned_count[$equipment_id][$data_array_equipment->level] != 0)
                    $table .= ' ('.number_format(100*$equipment_assigned_count[$equipment_id][$data_array_equipment->level]/$equipment_count[$equipment_id][$data_array_equipment->level], 0, ',', ' ').'%)';
                else
                    $table .= ' (0%)';
            }
            else
                $table .= '0';
            $table .= '<td><a href="'.base_url('admin/admin_equipment_controller/edit_equipments/'.$data_array_equipment->type).'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
            $table .= ' <a href="?action=delete" class="delete-dialog-admin-items btn-danger">'.$this->lang->line('admin_page')['delete'].'</a></td>';
            $table .= '</tr>';
            $previous_equipment_type = $data_equipment_type;
            $previous_class = $class;
        }
        $table .= '</tbody></table>';
        
        $data['table'] = $table;
        $data['title'] = $this->lang->line('admin_page')['equipmentlist'];
        $data['page_type'] = 'equipment';
        $data['main_content'] = 'admin/adminBuildingItemsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
     
    public function edit_equipments($equipment_type){
        $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($equipment_type);
        $data['equipment_type_array'] = $data_equipment_type->result();
        $equipment_row = $data_equipment_type->row();
        $data['type_select'] = $this->get_select_type($equipment_row->type);
        
        $data['equipment_type'] = $equipment_type;
        $data['title'] = $this->lang->line('admin_page')['edit_equipment_type'];
        $data['page_type'] = 'equipment';
        $data['main_content'] = 'admin/editEquipmentView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function update_equipment_type() {
        if (isset ($_POST['edit_equipment_type'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('type', $this->lang->line('admin_page')['type'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('id_equipment[]', $this->lang->line('admin_page')['id_equipment'], 'trim|required|max_length[10]|integer');
                $this->form_validation->set_rules('level[]', $this->lang->line('home')['level'], 'trim|required|max_length[1]|integer');
                $this->form_validation->set_rules('name_english[]', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french[]', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('delivery_time[]', $this->lang->line('home')['equipment_delivery_time'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('buying_cost[]', $this->lang->line('home')['cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('reputation[]', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('capacity[]', $this->lang->line('home')['capacity'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('max_income[]', $this->lang->line('equipment')['max_income'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('daily_cost[]', $this->lang->line('home')['daily_cost'], 'trim|required|max_length[11]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_equipment_error_type'] = form_error('type');
                    $data['edit_equipment_error_id_equipment'] = form_error('id_equipment[]');
                    $data['edit_equipment_error_level'] = form_error('level[]');
                    $data['edit_equipment_error_name_english'] = form_error('name_english[]');
                    $data['edit_equipment_error_name_french'] = form_error('name_french[]');
                    $data['edit_equipment_error_delivery_time'] = form_error('delivery_time[]');
                    $data['edit_equipment_error_buying_cost'] = form_error('buying_cost[]');
                    $data['edit_equipment_error_reputation'] = form_error('reputation[]');
                    $data['edit_equipment_error_capacity'] = form_error('capacity[]');
                    $data['edit_equipment_error_max_income'] = form_error('max_income[]');
                    $data['edit_equipment_error_daily_cost'] = form_error('daily_cost[]');
                    $this->session->set_flashdata('msg','');

                    $data['equipment_type'] = $this->input->post('type', TRUE);
                    $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                    $data['equipment_type_array'] = $data_equipment_type->result();
                    $equipment_row = $data_equipment_type->row();
                    $data['type_select'] = $this->get_select_type($equipment_row->type);
                    $data['title'] = $this->lang->line('admin_page')['edit_equipment_type'];
                    $data['page_type'] = 'equipment';
                    $data['main_content'] = 'admin/editEquipmentView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $equipment_type = $this->input->post('type', TRUE);
                    $id_equipment[] = array_map('trim',$this->input->post('id_equipment[]', TRUE));
                    $level[] = array_map('trim',$this->input->post('level[]', TRUE));
                    $name_english[] = array_map('trim',$this->input->post('name_english[]', TRUE));
                    $name_french[] = array_map('trim',$this->input->post('name_french[]', TRUE));
                    $delivery_time[] = array_map('trim',$this->input->post('delivery_time[]', TRUE));
                    $buying_cost[] = array_map('trim',$this->input->post('buying_cost[]', TRUE));
                    $reputation[] = array_map('trim',$this->input->post('reputation[]', TRUE));
                    $capacity[] = array_map('trim',$this->input->post('capacity[]', TRUE));
                    $max_income[] = array_map('trim',$this->input->post('max_income[]', TRUE));
                    $daily_cost[] = array_map('trim',$this->input->post('daily_cost[]', TRUE));
                    
                    for ($i=0; $i<3; $i++) {
                        $query[] = $this->admin_equipment_model->update_equipment_type($id_equipment[0][$i], $equipment_type, $level[0][$i], $name_english[0][$i], $name_french[0][$i], $delivery_time[0][$i], $buying_cost[0][$i], $reputation[0][$i], $capacity[0][$i], $max_income[0][$i], $daily_cost[0][$i]);       //update equipment table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succedded
                        $data['equipment_type'] = $this->input->post('type', TRUE);
                        $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['equipment_updated'].'</div>');
                        $data['equipment_type_array'] = $data_equipment_type->result();
                        $equipment_row = $data_equipment_type->row();
                        $data['type_select'] = $this->get_select_type($equipment_row->type);
                        $data['title'] = $this->lang->line('admin_page')['edit_equipment_type'];
                        $data['page_type'] = 'equipment';
                        $data['main_content'] = 'admin/editEquipmentView';
                        $this->load->view('templates/default_admin',$data);  
                        }
                        else {
                            $data['equipment_type'] = $this->input->post('type', TRUE);
                            $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['equipment_not_updated_all_failed'].'</div>');
                            $data['equipment_type_array'] = $data_equipment_type->result();
                            $equipment_row = $data_equipment_type->row();
                            $data['type_select'] = $this->get_select_type($equipment_row->type);
                            $data['title'] = $this->lang->line('admin_page')['edit_equipment_type'];
                            $data['page_type'] = 'equipment';
                            $data['main_content'] = 'admin/editEquipmentView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {                     //at least one of the query failed
                        $data['equipment_type'] = $this->input->post('type', TRUE);
                        $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['equipment_not_updated_one_failed'].'</div>');
                        $data['equipment_type_array'] = $data_equipment_type->result();
                        $equipment_row = $data_equipment_type->row();
                        $data['type_select'] = $this->get_select_type($equipment_row->type);
                        $data['title'] = $this->lang->line('admin_page')['edit_equipment_type'];
                        $data['page_type'] = 'equipment';
                        $data['main_content'] = 'admin/editEquipmentView';
                        $this->load->view('templates/default_admin',$data); 
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_equipment_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['equipment_type'] = $this->input->post('type', TRUE);
            $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['equipment_not_updated_no_post'].'</div>');
            $data['equipment_type_array'] = $data_equipment_type->result();
            $equipment_row = $data_equipment_type->row();
            $data['type_select'] = $this->get_select_type($equipment_row->type);
            $data['title'] = $this->lang->line('admin_page')['edit_equipment_type'];
            $data['page_type'] = 'equipment';
            $data['main_content'] = 'admin/editEquipmentView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    public function add_new_equipment_validation() {
        if (isset ($_POST['add_new_equipment'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('type', $this->lang->line('admin_page')['type'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_english[]', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french[]', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('delivery_time[]', $this->lang->line('home')['equipment_delivery_time'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('buying_cost[]', $this->lang->line('common_equipment')['equipment_cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('reputation[]', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('capacity[]', $this->lang->line('home')['capacity'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('max_income[]', $this->lang->line('equipment')['max_income'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('daily_cost[]', $this->lang->line('equipment')['daily_cost'], 'trim|required|max_length[11]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_equipment_error_type'] = form_error('type');
                    $data['add_equipment_error_name_english'] = form_error('name_english[]');
                    $data['add_equipment_error_name_french'] = form_error('name_french[]');
                    $data['add_equipment_error_delivery_time'] = form_error('delivery_time[]');
                    $data['add_equipment_error_buying_cost'] = form_error('buying_cost[]');
                    $data['add_equipment_error_reputation'] = form_error('reputation[]');
                    $data['add_equipment_error_capacity'] = form_error('capacity[]');
                    $data['add_equipment_error_max_income'] = form_error('max_income[]');
                    $data['add_equipment_error_daily_cost'] = form_error('daily_cost[]');
                    $this->session->set_flashdata('msg','');

                    $data['equipment_type'] = $this->input->post('type', TRUE);
                    $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                    $data['equipment_type_array'] = $data_equipment_type->result();
                    $equipment_row = $data_equipment_type->row();
                    $data['type_select'] = $this->get_select_type($equipment_row->type);
                    $data['title'] = $this->lang->line('admin_page')['add_new_equipment'];
                    $data['page_type'] = 'equipment';
                    $data['main_content'] = 'admin/addEquipmentView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $equipment_type = $this->input->post('type', TRUE);
                    $name_english[] = array_map('trim',$this->input->post('name_english[]', TRUE));
                    $name_french[] = array_map('trim',$this->input->post('name_french[]', TRUE));
                    $building_time[] = array_map('trim',$this->input->post('delivery_time[]', TRUE));
                    $buying_cost[] = array_map('trim',$this->input->post('buying_cost[]', TRUE));
                    $reputation[] = array_map('trim',$this->input->post('reputation[]', TRUE));
                    $capacity[] = array_map('trim',$this->input->post('capacity[]', TRUE));
                    $max_income[] = array_map('trim',$this->input->post('max_income[]', TRUE));
                    $daily_cost[] = array_map('trim',$this->input->post('daily_cost[]', TRUE));
                    
                    for ($i=0; $i<3; $i++) {
                        $data_insert = array (
                            'type' => $equipment_type,
                            'level' => $i+1,
                            'name_english' => $name_english[0][$i],
                            'name_french' => $name_french[0][$i],
                            'delivery_time' => $building_time[0][$i],
                            'buying_cost' => $buying_cost[0][$i],
                            'capacity' => $capacity[0][$i],
                            'reputation' => $reputation[0][$i],
                            'capacity' => $capacity[0][$i],
                            'max_income' => $max_income[0][$i],
                            'daily_cost' => $daily_cost[0][$i]
                        );
                        $query[] = $this->admin_slope_model->add_item_admin($data_insert, 'game_equipments');       //Add lift to game_lifts table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succedded
                        $data['equipment_type'] = $this->input->post('type', TRUE);
                        $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['equipment_added'].'</div>');
                        $data['equipment_type_array'] = $data_equipment_type->result();
                        $equipment_row = $data_equipment_type->row();
                        $data['type_select'] = $this->get_select_type($equipment_row->type);
                        $data['title'] = $this->lang->line('admin_page')['add_new_equipment'];
                        $data['page_type'] = 'equipment';
                        $data['main_content'] = 'admin/addEquipmentView';
                        $this->load->view('templates/default_admin',$data);  
                        }
                        else {
                            $data['equipment_type'] = $this->input->post('type', TRUE);
                            $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['equipment_not_added_all_failed'].'</div>');
                            $data['equipment_type_array'] = $data_equipment_type->result();
                            $equipment_row = $data_equipment_type->row();
                            $data['type_select'] = $this->get_select_type($equipment_row->type);
                            $data['title'] = $this->lang->line('admin_page')['add_new_equipment'];
                            $data['page_type'] = 'equipment';
                            $data['main_content'] = 'admin/addEquipmentView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {                     //at least one of the query failed
                        $data['equipment_type'] = $this->input->post('type', TRUE);
                        $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['equipment_not_added_one_failed'].'</div>');
                        $data['equipment_type_array'] = $data_equipment_type->result();
                        $equipment_row = $data_equipment_type->row();
                        $data['type_select'] = $this->get_select_type($equipment_row->type);
                        $data['title'] = $this->lang->line('admin_page')['add_new_equipment'];
                        $data['page_type'] = 'equipment';
                        $data['main_content'] = 'admin/addEquipmentView';
                        $this->load->view('templates/default_admin',$data); 
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_equipment_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['equipment_type'] = $this->input->post('type', TRUE);
            $data_equipment_type = $this->admin_equipment_model->get_equipment_type_data($data['equipment_type']);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['equipment_not_added_no_post'].'</div>');
            $data['equipment_type_array'] = $data_equipment_type->result();
            $equipment_row = $data_equipment_type->row();
            $data['type_select'] = $this->get_select_type(null);
            $data['title'] = $this->lang->line('admin_page')['add_new_equipment'];
            $data['page_type'] = 'equipment';
            $data['main_content'] = 'admin/addEquipmentView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    
    public function add_new(){
        $data['type_select'] = $this->get_select_type(null);
        $data['title'] = $this->lang->line('admin_page')['add_new_equipment'];
        $data['equipment_type'] = '';
        $data['main_content'] = 'admin/addEquipmentView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    protected function get_select_type($id_type) {
        $types = $this->admin_equipment_model->get_types($id_type);
        $data = '';
        foreach ($types->result() as $array_type) {
            if ($array_type->id_type == $id_type) {
                $data .= '<option value="'.$array_type->id_type.'" selected>'.$array_type->id_type.': '.$array_type->name_type.'</option>';
            }
            else
                $data .= '<option value="'.$array_type->id_type.'">'.$array_type->id_type.': '.$array_type->name_type.'</option>';
        }
        return $data;
    }
    
}

?>