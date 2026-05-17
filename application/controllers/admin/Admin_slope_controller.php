<?php

class Admin_slope_controller extends CI_Controller{
    
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
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('resort',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_slope_model');
        $this->load->model('admin/admin_lift_model');
        $this->load->model('admin/admin_resort_model');
    }
    
    public function index(){
        
        // Get all information from all the created resorts
        $data_slope = $this->admin_slope_model->get_all_slope_data();
        $total = 0;
        foreach ($data_slope->result() as $value_type){
            $count_slope_data = $this->admin_slope_model->count_slope_db($value_type->id_slope);
            $count_slope_row = $count_slope_data->row();
            $slope_count[$value_type->id_slope] = $count_slope_row->count;
            $total = $total + $count_slope_row->count;
            
            if ($total > 0) {
                $count_slope_data_open = $this->admin_slope_model->count_open_slope_db();
                $count_slope_row_open = $count_slope_data_open->row();
                $total_for_level_open = $count_slope_row_open->count_open;
                $perc_open = number_format(($total_for_level_open/$total)*100, 0, ',', ' ');
            }
            else {
                $total_for_level_open = 0;
                $perc_open = 0;
            }
        }
        
        $table = '<table align="center" class="table table-responsive table-striped blue_header">';
        $table .= '<thead><tr>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_slope'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_sector'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_english'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_french'].'</th>';
            $table .= '<th>'.$this->lang->line('slope')['length'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['reputation'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['slope_type'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['locations'].'</th>';
            $table .= '<th>'.$this->lang->line('home')['path'].'</th>';
            $table .= '<th>#'.$total.' (open:'.$total_for_level_open.' = '.$perc_open.'%)</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['Actions'].'</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
        // For each slope
        foreach ($data_slope->result() as $data_array_slope) {
            
            $id_slope = $data_array_slope->id_slope;
            $query_path = $data_array_slope->path;
            $display_path = substr($query_path, 0, strpos($query_path, "]")+1).' > '.substr($query_path, strrpos($query_path, '['));    // Returns the first coo and last one (before "]" and after last "[" )
            $table .= '<tr data-id_item="'.$id_slope.'" data-item_type="slope">';
            $table .= '<td>'.$id_slope.'</th>';
            $table .= '<td>'.$data_array_slope->id_sector.'</td>';
            $table .= '<td>'.$data_array_slope->name_english.'</td>';
            $table .= '<td>'.$data_array_slope->name_french.'</td>';
            $table .= '<td>'.$data_array_slope->length.'</td>';
            $table .= '<td>'.number_format($data_array_slope->reputation, 0, ',', ' ').'</td>';
            $table .= '<td>'.$data_array_slope->slope_type.'</td>';
            $table .= '<td>'.$data_array_slope->start_location.' > '.$data_array_slope->end_location.'</td>';
            $table .= '<td>'.$display_path.'</td>';
            $table .= '<td>';
            if(isset($slope_count[$id_slope]))
                $table .= number_format($slope_count[$id_slope], 0, ',', ' ');
            else
                $table .= '0';
             $table .= '</td>';
            $table .= '<td><a href="'.base_url('admin/admin_slope_controller/edit_slopes/'.$id_slope).'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
            $table .= ' <a href="?action=delete" class="delete-dialog-admin-items btn-danger">'.$this->lang->line('admin_page')['delete'].'</a></td>';

            $table .= '</tr>';
        }
        $table .= '</tbody></table>';
        
        $data['table'] = $table;
        $data['title'] = $this->lang->line('admin_page')['slopelist'];
        $data['page_type'] = 'slope';
        $data['main_content'] = 'admin/adminBuildingItemsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
     
    public function edit_slopes($id_slope){
        $data_slope = $this->admin_slope_model->get_single_slope_data($id_slope);
        $data['slope_array'] = $data_slope->result();
        $slope_row = $data_slope->row();
        $data['start_location_select'] = $this->get_select_locations($slope_row->start_location);
        $data['end_location_select'] = $this->get_select_locations($slope_row->end_location);
        $data['slope_type_select'] = $this->get_slope_type_select($slope_row->slope_type);
        $data['id_slope'] = $id_slope;
        $data['title'] = $this->lang->line('admin_page')['slopelist'];
        $data['page_type'] = 'slope';
        $data['main_content'] = 'admin/editSlopeView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function add_new(){
        $data['title'] = $this->lang->line('admin_page')['add_new_slope'];
        $data['start_location_select'] = $this->get_select_locations(null);
        $data['end_location_select'] = $this->get_select_locations(null);
        $max_id_slope = intval($this->admin_lift_model->get_max_value_DB('id_slope', 'game_slopes'));
        $data['max_sector'] = intval($this->admin_lift_model->get_max_value_DB('id_sector', 'game_sectors'));
        $next_id = $max_id_slope+1;
        $data['slope_id_english'] = $this->lang->line('admin_page')['slope_id_english'].' '.$next_id;
        $data['slope_id_french'] = $this->lang->line('admin_page')['slope_id_french'].' '.$next_id;
        $data['slope_id_french'] = $this->lang->line('admin_page')['slope_id_french'].' '.$next_id;
        $data['slope_type_select'] = $this->get_slope_type_select(null);
        $data['main_content'] = 'admin/addSlopeView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function add_new_slope_validation (){
        if (isset ($_POST['add_new_slope'])) {           
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_sector', $this->lang->line('admin_page')['id_sector'], 'trim|required|max_length[2]|integer');
                $this->form_validation->set_rules('name_english', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('length', $this->lang->line('slope')['length'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('start_location', $this->lang->line('home')['start_location'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('end_location', $this->lang->line('home')['end_location'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('reputation', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('slope_type', $this->lang->line('home')['slope_type'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('path', $this->lang->line('home')['path'], 'trim|required');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_slope_error_id_sector'] = form_error('id_sector');
                    $data['add_slope_error_name_english'] = form_error('name_english');
                    $data['add_slope_error_name_french'] = form_error('name_french');
                    $data['add_slope_error_length'] = form_error('length');
                    $data['add_slope_error_start_location'] = form_error('start_location');
                    $data['add_slope_error_end_location'] = form_error('end_location');
                    $data['add_slope_error_reputation'] = form_error('reputation');
                    $data['add_slope_error_slope_type'] = form_error('slope_type');
                    $data['add_slope_error_path'] = form_error('path');
                    $this->session->set_flashdata('msg','');
                    $data['title'] = $this->lang->line('admin_page')['add_new_slope'];
                    $data['start_location_select'] = $this->get_select_locations($this->input->post('start_location', TRUE));
                    $data['end_location_select'] = $this->get_select_locations($this->input->post('end_location', TRUE));
                    $max_id_slope = intval($this->admin_lift_model->get_max_value_DB('id_slope', 'game_slopes'));
                    $next_id = $max_id_slope+1;
                    $data['slope_id_english'] = $this->lang->line('admin_page')['slope_id_english'].' '.$next_id;
                    $data['slope_id_french'] = $this->lang->line('admin_page')['slope_id_french'].' '.$next_id;
                    $data['max_sector'] = intval($this->admin_lift_model->get_max_value_DB('id_sector', 'game_sectors'));
                    $data['slope_type_select'] = $this->get_slope_type_select($this->input->post('slope_type', TRUE));
                    $data['main_content'] = 'admin/addSlopeView';
                    $this->load->view('templates/default_admin',$data); 
                }
                else {                   // all fields are correct
                    $id_sector = $this->input->post('id_sector', TRUE);
                    $name_english = $this->input->post('name_english', TRUE);
                    $name_french = $this->input->post('name_french', TRUE);
                    $length = $this->input->post('length', TRUE);
                    $start_location = $this->input->post('start_location', TRUE);
                    $end_location = $this->input->post('end_location', TRUE);
                    $reputation = $this->input->post('reputation', TRUE);
                    $slope_type = $this->input->post('slope_type', TRUE);
                    $path = $this->input->post('path', TRUE);
                    $path = trim(preg_replace('/\s+/', '', $path)); // Removes whitespaces and linebreaks
                    // Strip outer double-bracket wrapping if present (e.g. [[x,y],[x,y]]) for backward compat
                    if (substr($path, 0, 2) === '[[' && substr($path, -2) === ']]') {
                        $path = substr($path, 1, -1);
                    }
                    $data_insert = array (
                        'id_sector' => $id_sector,
                        'name_english' => $name_english,
                        'name_french' => $name_french,
                        'length' => $length,
                        'start_location' => $start_location,
                        'end_location' => $end_location,
                        'reputation' => $reputation,
                        'slope_type' => $slope_type,
                        'path' => $path
                    );
                    $query = $this->admin_slope_model->add_item_admin($data_insert, 'game_slopes');       //Add slope to game_slopes table
                    if ($query === true){               // If the query succedded
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['slope_added'].'</div>');
                        $data['title'] = $this->lang->line('admin_page')['add_new_slope'];
                        $data['start_location_select'] = $this->get_select_locations($this->input->post('start_location', TRUE));
                        $data['end_location_select'] = $this->get_select_locations($this->input->post('end_location', TRUE));
                        $max_id_slope = intval($this->admin_lift_model->get_max_value_DB('id_slope', 'game_slopes'));
                        $next_id = $max_id_slope+1;
                        $data['slope_id_english'] = $this->lang->line('admin_page')['slope_id_english'].' '.$next_id;
                        $data['slope_id_french'] = $this->lang->line('admin_page')['slope_id_french'].' '.$next_id;
                        $data['max_sector'] = intval($this->admin_lift_model->get_max_value_DB('id_sector', 'game_sectors'));
                        $data['slope_type_select'] = $this->get_slope_type_select($this->input->post('slope_type', TRUE));
                        $data['main_content'] = 'admin/addSlopeView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['slope_not_added'].'</div>');
                        $data['title'] = $this->lang->line('admin_page')['add_new_slope'];
                        $data['start_location_select'] = $this->get_select_locations($this->input->post('start_location', TRUE));
                        $data['end_location_select'] = $this->get_select_locations($this->input->post('end_location', TRUE));
                        $max_id_slope = intval($this->admin_lift_model->get_max_value_DB('id_slope', 'game_slopes'));
                        $next_id = $max_id_slope+1;
                        $data['slope_id_english'] = $this->lang->line('admin_page')['slope_id_english'].' '.$next_id;
                        $data['slope_id_french'] = $this->lang->line('admin_page')['slope_id_french'].' '.$next_id;
                        $data['max_sector'] = intval($this->admin_lift_model->get_max_value_DB('id_sector', 'game_sectors'));
                        $data['slope_type_select'] = $this->get_slope_type_select($this->input->post('slope_type', TRUE));
                        $data['main_content'] = 'admin/addSlopeView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_slope_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['slope_not_added_no_post'].'</div>');
            $data['start_location_select'] = $this->get_select_locations(null);
            $data['end_location_select'] = $this->get_select_locations(null);
            $max_id_slope = intval($this->admin_lift_model->get_max_value_DB('id_slope', 'game_slopes'));
            $next_id = $max_id_slope+1;
            $data['slope_id_english'] = $this->lang->line('admin_page')['slope_id_english'].' '.$next_id;
            $data['slope_id_french'] = $this->lang->line('admin_page')['slope_id_french'].' '.$next_id;
            $data['max_sector'] = intval($this->admin_lift_model->get_max_value_DB('id_sector', 'game_sectors'));
            $data['slope_type_select'] = $this->get_slope_type_select($this->input->post('slope_type', TRUE));
            $data['main_content'] = 'admin/addSlopeView';
            $this->load->view('templates/default_admin',$data);
        }
    }


    
   /* protected function get_select_difficulty($id_difficulty) {
        $difficulties = $this->admin_slope_model->get_difficulties($id_difficulty);
        $data = '';
        foreach ($difficulties->result() as $array_difficulties) {
            if ($array_difficulties->id_difficulty == $id_difficulty) {
                $data .= '<option value="'.$array_difficulties->id_difficulty.'" selected>'.$array_difficulties->id_difficulty.': '.$array_difficulties->name_english.'</option>';
            }
            else
                $data .= '<option value="'.$array_difficulties->id_difficulty.'">'.$array_difficulties->id_difficulty.': '.$array_difficulties->name_english.'</option>';
        }
        return $data;
    }
    */
    protected function get_select_locations($id_location) {
        $locations = $this->admin_slope_model->get_locations($id_location);
        $data = '';
        foreach ($locations->result() as $array_locations) {
            if ($array_locations->id_location == $id_location) {
                $data .= '<option value="'.$array_locations->id_location.'" selected>'.$array_locations->id_location.' (Sector '.$array_locations->id_sector.')</option>';
            }
            else
                $data .= '<option value="'.$array_locations->id_location.'">'.$array_locations->id_location.' (Sector '.$array_locations->id_sector.')</option>';
        }
        return $data;
    }

    protected function get_slope_type_select($selected_type) {
        $slope_types = $this->admin_slope_model->get_slope_types();
        $data = '';
        foreach ($slope_types->result() as $type) {
            $selected = ($type->id_slope_types == $selected_type) ? ' selected' : '';
            $data .= '<option value="'.$type->id_slope_types.'"'.$selected.'>'.$type->id_slope_types.' – '.$type->slope_type_name.'</option>';
        }
        return $data;
    }
    
    
    public function update_slope_admin() {
        if (isset ($_POST['edit_slope_admin'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_slope', $this->lang->line('admin_page')['id_slope'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('id_sector', $this->lang->line('admin_page')['id_sector'], 'trim|required|max_length[2]|integer');
                $this->form_validation->set_rules('name_english', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('length', $this->lang->line('slope')['length'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('start_location', $this->lang->line('home')['start_location'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('end_location', $this->lang->line('home')['end_location'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('reputation', $this->lang->line('home')['reputation'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('slope_type', $this->lang->line('home')['slope_type'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('path', $this->lang->line('home')['path'], 'trim|required');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_slope_error_id_slope'] = form_error('id_slope');
                    $data['edit_slope_error_id_sector'] = form_error('id_sector');
                    $data['edit_slope_error_name_english'] = form_error('name_english');
                    $data['edit_slope_error_name_french'] = form_error('name_french');
                    $data['edit_slope_error_length'] = form_error('length');
                    $data['edit_slope_error_start_location'] = form_error('start_location');
                    $data['edit_slope_error_end_location'] = form_error('end_location');
                    $data['edit_slope_error_reputation'] = form_error('reputation');
                    $data['edit_slope_error_slope_type'] = form_error('slope_type');
                    $data['edit_slope_error_path'] = form_error('path');
                    $this->session->set_flashdata('msg','');

                    $id_slope = $this->input->post('id_slope', TRUE);
                    $data_slope = $this->admin_slope_model->get_single_slope_data($id_slope);
                    $data['slope_array'] = $data_slope->result();
                    $slope_row = $data_slope->row();
                    $data['id_slope'] = $id_slope;
                    $data['start_location_select'] = $this->get_select_locations($slope_row->start_location);
                    $data['end_location_select'] = $this->get_select_locations($slope_row->end_location);
                    $data['slope_type_select'] = $this->get_slope_type_select($this->input->post('slope_type', TRUE));
                    $data['title'] = $this->lang->line('admin_page')['slopelist'];
                    $data['page_type'] = 'slope';
                    $data['main_content'] = 'admin/editSlopeView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_slope = $this->input->post('id_slope', TRUE);
                    $id_sector = $this->input->post('id_sector', TRUE);
                    $name_english = $this->input->post('name_english', TRUE);
                    $name_french = $this->input->post('name_french', TRUE);
                    $length = $this->input->post('length', TRUE);
                    $start_location = $this->input->post('start_location', TRUE);
                    $end_location = $this->input->post('end_location', TRUE);
                    $reputation = $this->input->post('reputation', TRUE);
                    $slope_type = $this->input->post('slope_type', TRUE);
                    $path = $this->input->post('path', TRUE);
                    
                    $query = $this->admin_slope_model->update_slope_admin($id_slope, $id_sector, $name_english, $name_french, $length, $start_location, $end_location, $reputation, $slope_type, $path);       //update slope table
                    if ($query === true){               // If the query succedded
                        $data_slope = $this->admin_slope_model->get_single_slope_data($id_slope);
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['slope_updated'].'</div>');
                        $data['slope_array'] = $data_slope->result();
                        $slope_row = $data_slope->row();
                        $data['id_slope'] = $id_slope;
                        $data['title'] = $this->lang->line('admin_page')['slopelist'];
                        $data['page_type'] = 'slope';
                        $data['start_location_select'] = $this->get_select_locations($slope_row->start_location);
                        $data['end_location_select'] = $this->get_select_locations($slope_row->end_location);
                        $data['slope_type_select'] = $this->get_slope_type_select($slope_row->slope_type);
                        $data['main_content'] = 'admin/editSlopeView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {
                        $data_slope = $this->admin_slope_model->get_single_slope_data($id_slope);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['slope_not_updated'].'</div>');
                        $data['slope_array'] = $data_slope->result();
                        $slope_row = $data_slope->row();
                        $data['id_slope'] = $id_slope;
                        $data['start_location_select'] = $this->get_select_locations($slope_row->start_location);
                        $data['end_location_select'] = $this->get_select_locations($slope_row->end_location);
                        $data['slope_type_select'] = $this->get_slope_type_select($slope_row->slope_type);
                        $data['title'] = $this->lang->line('admin_page')['slopelist'];
                        $data['page_type'] = 'slope';
                        $data['main_content'] = 'admin/editSlopeView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_slope_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $id_slope = $this->input->post('id_slope', TRUE);
            $data_slope = $this->admin_slope_model->get_single_slope_data($id_slope);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['slope_not_updated_no_post'].'</div>');
            $data['slope_array'] = $data_slope->result();
            $slope_row = $data_slope->row();
            $data['id_slope'] = $id_slope;
            $data['start_location_select'] = $this->get_select_locations($slope_row->start_location);
            $data['end_location_select'] = $this->get_select_locations($slope_row->end_location);
            $data['slope_type_select'] = $this->get_slope_type_select($slope_row->slope_type);
            $data['title'] = $this->lang->line('admin_page')['slopelist'];
            $data['page_type'] = 'slope';
            $data['main_content'] = 'admin/editSlopeView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    
    
    /**
     * delete_action        Prepare the delete query for the posted item (with ajax function in popup dialog)
     * 
     * @return boolean      Returns true or false
     */
    public function delete_action(){
        // parameters posted by the ajax function of the popup dialog
        $id_item = trim($this->input->post('id_item', TRUE));
        $item_type = trim($this->input->post('item_type', TRUE)); 
        $table_name = 'game_'.$item_type.'s';
        if ($item_type == 'staff')       // for staff, there is no S at the end of the table
            $table_name = 'game_'.$item_type;
        else if ($item_type == 'lift' || $item_type == 'location')       // for lifts we delete based on id_group
            $column_name = 'id_group';
        else if ($item_type == 'building')       // for buildings we delete based on type
            $column_name = 'type';
        else                            // for slopes we delete based on id_slope
            $column_name = 'id_'.$item_type;
        
        
        if ($item_type == 'news')       // for buildings we delete based on type
            $table_name = 'game_news';
        
        // Deletes the resort from the DB
        $delete_item = $this->admin_slope_model->delete_items_admin_db($id_item, $column_name, $table_name);
        if ($delete_item === true) {
            return true;
        }
        else {
            return false;
        } 
    }
}

?>