<?php

class Admin_staff_controller extends CI_Controller{
    
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
        $ci->lang->load('staff',$siteLang);
        $ci->lang->load('resort',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_staff_model');
        $this->load->model('admin/admin_slope_model');
    }
    
    public function index(){
        
        // Get all information from all the created resorts
        $data_staff = $this->admin_staff_model->get_all_staff_data();
        $total = 0;
        $total_assigned = 0;
        foreach ($data_staff->result() as $value_type){
            $count_staff_data = $this->admin_staff_model->count_staff_db($value_type->id_staff);
            $count_assigned_staff_data = $this->admin_staff_model->count_assigned_staff_db($value_type->id_staff);
            $count_staff_row = $count_staff_data->row();
            $count_assigned_staff_row = $count_assigned_staff_data->row();
            $staff_count[$value_type->id_staff] = $count_staff_row->count;
            $staff_assigned_count[$value_type->id_staff] = $count_assigned_staff_row->count;
            $total = $total + $count_staff_row->count;
            $total_assigned = $total_assigned + $count_assigned_staff_row->count;
        }
        
        $table = '<table align="center" class="table staff_table table-responsive table-striped achievements">';
        $table .= '<thead><tr>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_staff'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_english'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['name_french'].'</th>';
            $table .= '<th>'.$this->lang->line('common_staff')['position'].'</th>';
            $table .= '<th>'.$this->lang->line('common_staff')['efficiency'].'</th>';
            $table .= '<th>'.$this->lang->line('common_staff')['salary'].'</th>';
            $table .= '<th># ('.$total.')</th>';
            if ($total == 0)
                $percentage_total = 0;
            else
                $percentage_total = number_format(100*$total_assigned/$total, 0, ',', ' ');
            $table .= '<th>'.$this->lang->line('admin_page')['assigned'].' ('.$total_assigned.') ('.$percentage_total.'%)</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['Actions'].'</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
        // For each staff
        foreach ($data_staff->result() as $data_array_staff) {
            
            $id_staff = $data_array_staff->id_staff;
           // $id_staff = $data_array_staff->id_group;
            $position = $data_array_staff->position;
               
            $table .= '<tr data-id_item="'.$id_staff.'" data-item_type="staff">';
            $table .= '<td>'.$id_staff.'</td>';
            $table .= '<td>'.$data_array_staff->name_english.'</td>';
            $table .= '<td>'.$data_array_staff->name_french.'</td>';
            $table .= '<td>'.$position.'</td>';
            $table .= '<td>'.$data_array_staff->efficiency.' %</td>';
            $table .= '<td>'.number_format($data_array_staff->salary, 0, ',', ' ').' €</td>';            
            $table .= '<td>';
            // total column
            if(isset($staff_count[$id_staff]))
                $table .= number_format($staff_count[$id_staff], 0, ',', ' ');
            else
                $table .= '0';
            $table .= '</td>';
            // total assigned column
            $table .= '<td>';
            if(isset($staff_assigned_count[$id_staff])) {
                $table .= number_format($staff_assigned_count[$id_staff], 0, ',', ' ');
                if ($staff_assigned_count[$id_staff] != 0)
                    $table .= ' ('.number_format(100*$staff_assigned_count[$id_staff]/$staff_count[$id_staff], 0, ',', ' ').'%)';
                else
                    $table .= ' (0%)';
            }
            else
                $table .= '0';
             $table .= '</td>';
            $table .= '<td><a href="'.base_url('admin/admin_staff_controller/edit_staff/'.$id_staff).'"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>';
            $table .= ' <a href="?action=delete" class="delete-dialog-admin-items btn-danger">'.$this->lang->line('admin_page')['delete'].'</a></td>';
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';
        
        $data['table'] = $table;
        $data['title'] = $this->lang->line('admin_page')['stafflist'];
        $data['page_type'] = 'staff';
        $data['main_content'] = 'admin/adminBuildingItemsView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    
     
    public function edit_staff($id_staff){
        $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
        $data['staff_row'] = $data_staff->row();
        $data['select_position'] = $this->get_select_position($data['staff_row']->position);
        $data['page_type'] = 'staff';
        $data['main_content'] = 'admin/editStaffView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function add_new(){
        $data['title'] = $this->lang->line('admin_page')['add_new_staff'];
        $data['select_position'] = $this->get_select_position(null);
        $data['main_content'] = 'admin/addStaffView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    
    public function add_new_staff_validation() {
        if (isset ($_POST['add_new_staff'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('name_english', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('position', $this->lang->line('common_staff')['position'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('efficiency', $this->lang->line('common_staff')['efficiency'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('salary', $this->lang->line('common_staff')['salary'], 'trim|required|max_length[11]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_staff_error_name_english'] = form_error('name_english');
                    $data['add_staff_error_name_french'] = form_error('name_french');
                    $data['add_staff_error_position'] = form_error('position');
                    $data['add_staff_error_efficiency'] = form_error('efficiency');
                    $data['add_staff_error_salary'] = form_error('salary');
                    $this->session->set_flashdata('msg','');

                    $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
                    
                    $data['title'] = $this->lang->line('admin_page')['add_new_staff'];
                    $data['page_type'] = 'staff';
                    $data['main_content'] = 'admin/addStaffView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_staff = $this->input->post('id_staff', TRUE);
                    $name_english = $this->input->post('name_english', TRUE);
                    $name_french = $this->input->post('name_french', TRUE);
                    $position = $this->input->post('position', TRUE);
                    $efficiency = $this->input->post('efficiency', TRUE);
                    $salary = $this->input->post('salary', TRUE);
                    
                    $data_insert = array (
                        'id_staff' => $id_staff,
                        'name_english' => $name_english,
                        'name_french' => $name_french,
                        'position' => $position,
                        'efficiency' => $efficiency,
                        'salary' => $salary,
                    );
                    $query[] = $this->admin_slope_model->add_item_admin($data_insert, 'game_staff');       //Add staff to game_staff table
                        
                    if(current($query) === true){               // If the query succedded
                        $id_staff = $this->input->post('id_staff', TRUE);
                        $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
                        $data['staff_row'] = $data_staff->row();
                        $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['staff_added'].'</div>');
                        $data['title'] = $this->lang->line('admin_page')['add_new_staff'];
                        $data['page_type'] = 'staff';
                        $data['main_content'] = 'admin/addStaffView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {
                        $id_staff = $this->input->post('id_staff', TRUE);
                        $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
                        $data['staff_row'] = $data_staff->row();
                        $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['staff_not_added'].'</div>');
                        $data['title'] = $this->lang->line('admin_page')['add_new_staff'];
                        $data['page_type'] = 'staff';
                        $data['main_content'] = 'admin/addStaffView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_staff_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
            $data['staff_row'] = $data_staff->row();
            $data['select_position'] = $this->get_select_position(null);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['staff_not_added_no_post'].'</div>');
            $data['title'] = $this->lang->line('admin_page')['add_new_staff'];
            $data['page_type'] = 'staff';
            $data['main_content'] = 'admin/addStaffView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    public function update_staff_admin() {
        if (isset ($_POST['edit_staff_admin'])) {           // if we POST something from the edit_staff_admin form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_staff', $this->lang->line('admin_page')['id_staff'], 'trim|required|max_length[11]|integer');
                $this->form_validation->set_rules('name_english', $this->lang->line('admin_page')['name_english'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('name_french', $this->lang->line('admin_page')['name_french'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('position', $this->lang->line('common_staff')['position'], 'trim|required|max_length[45]');
                $this->form_validation->set_rules('efficiency', $this->lang->line('common_staff')['efficiency'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('salary', $this->lang->line('common_staff')['salary'], 'trim|required|max_length[11]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_staff_error_id_staff'] = form_error('id_staff');
                    $data['edit_staff_error_name_english'] = form_error('name_english');
                    $data['edit_staff_error_name_french'] = form_error('name_french');
                    $data['edit_staff_error_position'] = form_error('position');
                    $data['edit_staff_error_efficiency'] = form_error('efficiency');
                    $data['edit_staff_error_salary'] = form_error('salary');
                    $this->session->set_flashdata('msg','');

                    $id_staff = $this->input->post('id_staff', TRUE);
                    $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
                    $data['staff_row'] = $data_staff->row();
                    $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
                    $data['page_type'] = 'staff';
                    $data['main_content'] = 'admin/editStaffView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_staff = $this->input->post('id_staff', TRUE);
                    $name_english = $this->input->post('name_english', TRUE);
                    $name_french = $this->input->post('name_french', TRUE);
                    $position = $this->input->post('position', TRUE);
                    $efficiency = $this->input->post('efficiency', TRUE);
                    $salary = $this->input->post('salary', TRUE);
                    $query = $this->admin_staff_model->update_staff_admin($id_staff, $name_english, $name_french, $position, $efficiency, $salary);       //update staff table
                    if($query === true){  
                        $id_staff = $this->input->post('id_staff', TRUE);
                        $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['staff_updated'].'</div>');
                        $data['staff_row'] = $data_staff->row();
                        $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
                        $data['page_type'] = 'staff';
                        $data['main_content'] = 'admin/editStaffView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {
                        $id_staff = $this->input->post('id_staff', TRUE);
                        $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['staff_not_updated'].'</div>');
                        $data['staff_row'] = $data_staff->row();
                        $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
                        $data['page_type'] = 'staff';
                        $data['main_content'] = 'admin/editStaffView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_staff_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $id_staff = $this->input->post('id_staff', TRUE);
            $data_staff = $this->admin_staff_model->get_group_staff_data($id_staff);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['staff_not_updated_no_post'].'</div>');
            $data['staff_row'] = $data_staff->row();
            $data['select_position'] = $this->get_select_position($this->input->post('position', TRUE));
            $data['page_type'] = 'staff';
            $data['main_content'] = 'admin/editStaffView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    
    protected function get_select_position($position) {
        $data = '';
        $array_positions = array (
            'skipatrol', 'skiinstructor', 'liftmechanic', 'mechanicGroomer', 'driver'
        );
        foreach ($array_positions as $pos) {
            if ($position == $pos) {
                $data .= '<option value="'.$pos.'" selected>'.$this->lang->line('hireStaff')[$pos].'</option>';
            }
            else
                $data .= '<option value="'.$pos.'">'.$this->lang->line('hireStaff')[$pos].'</option>';
        }
        return $data;
    }
    
}

?>