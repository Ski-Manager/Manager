<?php

class Admin_building_controller extends CI_Controller{
    
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
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('resort',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_building_model');
        $this->load->model('admin/admin_slope_model');
    }
    
    public function index(){
        
        // Get all information from all the created resorts
        $building_type = array ('tourist_info', 'access', 'hotel', 'restaurant', 'rental', 'leisure', 'medical', 'parking', 'cannon');
        $data_building = $this->admin_building_model->get_all_building_data();
        $total = 0;
        foreach ($building_type as $value_type){
            $count_building_data = $this->admin_building_model->count_building_db($value_type);
            $count_building_row = $count_building_data->row();
            $building_count[$value_type][$count_building_row->level] = $count_building_row->count;
            $total = $total + $count_building_row->count;
        }
        $table = '<table align="center" class="table table-responsive achievements myTableLeaderboard small_text11">';
        $table .= '<thead><tr>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_building'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['type'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_english'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_french'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['level'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['building_time'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['cost'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['reputation'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['capacity'].'</th>';
            $table .= '<th>'.$this->lang->line('building')['max_income'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['daily_cost'].'</th>';
            $table .= '<th># ('.$total.')</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['Actions'].'</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
            $previous_building_type = '';
        // For each building
        foreach ($data_building->result() as $data_array_building) {
            $building_type = $data_array_building->type;
            
            if ($previous_building_type == '') {
                $class = 'ach_even';
            }
            else if ($previous_building_type == $building_type) {
                $class = $previous_class;
            }
            else if ($previous_class == 'ach_even') {
                $class = 'ach_odd';
            }
            else if ($previous_class == 'ach_odd') {
                $class = 'ach_even';
            }
            
            $table .= '<tr class="'.$class.'" data-id_item="'.$data_array_building->type.'" data-item_type="building">';
            $table .= '<td>'.$data_array_building->id_building.'</th>';
            $table .= '<td>'.$data_array_building->type.'</td>';
            $table .= '<td>'.$data_array_building->name_english.'</td>';
            $table .= '<td>'.$data_array_building->name_french.'</td>';
            $table .= '<td>'.$data_array_building->level.'</td>';
            $table .= '<td>'.display_friendly_time($data_array_building->building_time/ACCELERATOR_FACTOR).'</td>';
            $table .= '<td>'.number_format($data_array_building->building_cost, 0, ',', ' ').' €</td>';
            $table .= '<td>'.number_format($data_array_building->reputation, 0, ',', ' ').'</td>';
            $table .= '<td>'.number_format($data_array_building->capacity, 0, ',', ' ').'</td>';
            $table .= '<td>'.number_format($data_array_building->max_income, 0, ',', ' ').' €</td>';
            $table .= '<td>'.number_format($data_array_building->daily_cost, 0, ',', ' ').' €</td>';
            $table .= '<td>';
            if(isset($building_count[$building_type][$data_array_building->level]))
                $table .= number_format($building_count[$building_type][$data_array_building->level], 0, ',', ' ');
            else
                $table .= '0';
            $table .= '</td>';                
            $table .= '<td><a href="'.base_url('admin/admin_building_controller/edit_buildings/'.$data_array_building->type).'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
            $table .= ' <a href="?action=delete" class="delete-dialog-admin-items btn-danger">'.$this->lang->line('admin_page')['delete'].'</a></td>';
            $table .= '</tr>';
            $previous_building_type = $building_type;
            $previous_class = $class;
        }
        $table .= '</tbody></table>';
        
        $data['table'] = $table;
        $data['title'] = $this->lang->line('admin_page')['buildinglist'];
        $data['page_type'] = 'building';
        $data['main_content'] = 'admin/adminBuildingItemsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
     
    public function edit_buildings($building_type){
        $data_building_type = $this->admin_building_model->get_building_type_data($building_type);
        $data['building_type_array'] = $data_building_type->result();
        
        $data['building_type'] = $building_type;
        $data['title'] = $this->lang->line('admin_page')['edit_building_type'];
        $data['page_type'] = 'building';
        $data['main_content'] = 'admin/editBuildingView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function update_building_type() {
        if (isset ($_POST['edit_building_type'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('type', $this->lang->line('admin_page')['type'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('id_building[]', $this->lang->line('admin_page')['id_building'], 'trim|required|max_length[10]|integer');
                $this->form_validation->set_rules('level[]', $this->lang->line('home')['level'], 'trim|required|max_length[1]|integer');
                $this->form_validation->set_rules('name_english[]', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french[]', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('building_time[]', $this->lang->line('home')['building_time'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('building_cost[]', $this->lang->line('home')['cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('reputation[]', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('capacity[]', $this->lang->line('home')['capacity'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('max_income[]', $this->lang->line('building')['max_income'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('daily_cost[]', $this->lang->line('home')['daily_cost'], 'trim|required|max_length[11]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_building_type_error_type'] = form_error('type');
                    $data['edit_building_type_error_id_building'] = form_error('id_building[]');
                    $data['edit_building_type_error_level'] = form_error('level[]');
                    $data['edit_building_type_error_name_english'] = form_error('name_english[]');
                    $data['edit_building_type_error_name_french'] = form_error('name_french[]');
                    $data['edit_building_type_error_building_time'] = form_error('building_time[]');
                    $data['edit_building_type_error_building_cost'] = form_error('building_cost[]');
                    $data['edit_building_type_error_reputation'] = form_error('reputation[]');
                    $data['edit_building_type_error_capacity'] = form_error('capacity[]');
                    $data['edit_building_type_error_max_income'] = form_error('max_income[]');
                    $data['edit_building_type_error_daily_cost'] = form_error('daily_cost[]');
                    $this->session->set_flashdata('msg','');

                    $data['building_type'] = $this->input->post('type', TRUE);
                    $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                    $data['building_type_array'] = $data_building_type->result();
                    $data['title'] = $this->lang->line('admin_page')['edit_building_type'];
                    $data['page_type'] = 'building';
                    $data['main_content'] = 'admin/editBuildingView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $building_type = $this->input->post('type', TRUE);
                    $id_building[] = array_map('trim',$this->input->post('id_building[]', TRUE));
                    $level[] = array_map('trim',$this->input->post('level[]', TRUE));
                    $name_english[] = array_map('trim',$this->input->post('name_english[]', TRUE));
                    $name_french[] = array_map('trim',$this->input->post('name_french[]', TRUE));
                    $building_time[] = array_map('trim',$this->input->post('building_time[]', TRUE));
                    $building_cost[] = array_map('trim',$this->input->post('building_cost[]', TRUE));
                    $reputation[] = array_map('trim',$this->input->post('reputation[]', TRUE));
                    $capacity[] = array_map('trim',$this->input->post('capacity[]', TRUE));
                    $max_income[] = array_map('trim',$this->input->post('max_income[]', TRUE));
                    $daily_cost[] = array_map('trim',$this->input->post('daily_cost[]', TRUE));
                    if ($building_type == 'tourist_info')
                        $max_queries = '1';
                    else
                        $max_queries = '3';
                    for ($i=0; $i<$max_queries; $i++) {
                        $query[] = $this->admin_building_model->update_building_type($id_building[0][$i], $building_type, $level[0][$i], $name_english[0][$i], $name_french[0][$i], $building_time[0][$i], $building_cost[0][$i], $reputation[0][$i], $capacity[0][$i], $max_income[0][$i], $daily_cost[0][$i]);       //update building table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succedded
                        $data['building_type'] = $this->input->post('type', TRUE);
                        $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['building_updated'].'</div>');
                        $data['building_type_array'] = $data_building_type->result();
                        $data['title'] = $this->lang->line('admin_page')['edit_building_type'];
                        $data['page_type'] = 'building';
                        $data['main_content'] = 'admin/editBuildingView';
                        $this->load->view('templates/default_admin',$data);  
                        }
                        else {
                            $data['building_type'] = $this->input->post('type', TRUE);
                            $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['building_not_updated_all_failed'].'</div>');
                            $data['building_type_array'] = $data_building_type->result();
                            $data['title'] = $this->lang->line('admin_page')['edit_building_type'];
                            $data['page_type'] = 'building';
                            $data['main_content'] = 'admin/editBuildingView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {                     //at least one of the query failed
                        $data['building_type'] = $this->input->post('type', TRUE);
                        $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['building_not_updated_one_failed'].'</div>');
                        $data['building_type_array'] = $data_building_type->result();
                        $data['title'] = $this->lang->line('admin_page')['edit_building_type'];
                        $data['page_type'] = 'building';
                        $data['main_content'] = 'admin/editBuildingView';
                        $this->load->view('templates/default_admin',$data); 
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_building_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['building_type'] = $this->input->post('type', TRUE);
            $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['building_not_updated_no_post'].'</div>');
            $data['building_type_array'] = $data_building_type->result();
            $data['title'] = $this->lang->line('admin_page')['edit_building_type'];
            $data['page_type'] = 'building';
            $data['main_content'] = 'admin/editBuildingView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    public function add_new_building_validation() {
        if (isset ($_POST['add_new_building'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('type', $this->lang->line('admin_page')['type'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_english[]', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french[]', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('building_time[]', $this->lang->line('home')['building_time'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('building_cost[]', $this->lang->line('home')['cost'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('reputation[]', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('capacity[]', $this->lang->line('home')['capacity'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('max_income[]', $this->lang->line('building')['max_income'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('daily_cost[]', $this->lang->line('home')['daily_cost'], 'trim|required|max_length[11]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_building_error_type'] = form_error('type');
                    $data['add_building_error_name_english'] = form_error('name_english[]');
                    $data['add_building_error_name_french'] = form_error('name_french[]');
                    $data['add_building_error_building_time'] = form_error('building_time[]');
                    $data['add_building_error_building_cost'] = form_error('building_cost[]');
                    $data['add_building_error_reputation'] = form_error('reputation[]');
                    $data['add_building_error_capacity'] = form_error('capacity[]');
                    $data['add_building_error_max_income'] = form_error('max_income[]');
                    $data['add_building_error_daily_cost'] = form_error('daily_cost[]');
                    $this->session->set_flashdata('msg','');

                    $data['building_type'] = $this->input->post('type', TRUE);
                    $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                    $data['building_type_array'] = $data_building_type->result();
                    $data['title'] = $this->lang->line('admin_page')['add_new_building'];
                    $data['page_type'] = 'building';
                    $data['main_content'] = 'admin/addBuildingView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $building_type = $this->input->post('type', TRUE);
                    $name_english[] = array_map('trim',$this->input->post('name_english[]', TRUE));
                    $name_french[] = array_map('trim',$this->input->post('name_french[]', TRUE));
                    $building_time[] = array_map('trim',$this->input->post('building_time[]', TRUE));
                    $building_cost[] = array_map('trim',$this->input->post('building_cost[]', TRUE));
                    $reputation[] = array_map('trim',$this->input->post('reputation[]', TRUE));
                    $capacity[] = array_map('trim',$this->input->post('capacity[]', TRUE));
                    $max_income[] = array_map('trim',$this->input->post('max_income[]', TRUE));
                    $daily_cost[] = array_map('trim',$this->input->post('daily_cost[]', TRUE));
                    
                    for ($i=0; $i<3; $i++) {
                        $data_insert = array (
                            'type' => $building_type,
                            'level' => $i+1,
                            'name_english' => $name_english[0][$i],
                            'name_french' => $name_french[0][$i],
                            'building_time' => $building_time[0][$i],
                            'building_cost' => $building_cost[0][$i],
                            'reputation' => $reputation[0][$i],
                            'capacity' => $capacity[0][$i],
                            'max_income' => $max_income[0][$i],
                            'daily_cost' => $daily_cost[0][$i]
                        );
                        $query[] = $this->admin_slope_model->add_item_admin($data_insert, 'game_buildings');       //Add lift to game_lifts table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succedded
                        $data['building_type'] = $this->input->post('type', TRUE);
                        $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['building_added'].'</div>');
                        $data['building_type_array'] = $data_building_type->result();
                        $data['title'] = $this->lang->line('admin_page')['add_new_building'];
                        $data['page_type'] = 'building';
                        $data['main_content'] = 'admin/addBuildingView';
                        $this->load->view('templates/default_admin',$data);  
                        }
                        else {
                            $data['building_type'] = $this->input->post('type', TRUE);
                            $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['building_not_added_all_failed'].'</div>');
                            $data['building_type_array'] = $data_building_type->result();
                            $data['title'] = $this->lang->line('admin_page')['add_new_building'];
                            $data['page_type'] = 'building';
                            $data['main_content'] = 'admin/addBuildingView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {                     //at least one of the query failed
                        $data['building_type'] = $this->input->post('type', TRUE);
                        $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['building_not_added_one_failed'].'</div>');
                        $data['building_type_array'] = $data_building_type->result();
                        $data['title'] = $this->lang->line('admin_page')['add_new_building'];
                        $data['page_type'] = 'building';
                        $data['main_content'] = 'admin/addBuildingView';
                        $this->load->view('templates/default_admin',$data); 
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_building_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['building_type'] = $this->input->post('type', TRUE);
            $data_building_type = $this->admin_building_model->get_building_type_data($data['building_type']);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['building_not_added_no_post'].'</div>');
            $data['building_type_array'] = $data_building_type->result();
            $data['title'] = $this->lang->line('admin_page')['add_new_building'];
            $data['page_type'] = 'building';
            $data['main_content'] = 'admin/addBuildingView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    
    public function add_new(){
        $data['title'] = $this->lang->line('admin_page')['add_new_building'];
        $data['building_type'] = '';
        $data['main_content'] = 'admin/addBuildingView';
        $this->load->view('templates/default_admin',$data); 
    }
    
}

?>