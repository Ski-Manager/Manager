<?php

class Admin_location_controller extends CI_Controller{
    
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
        //$this->load->model('admin/admin_location_model');
        //$this->load->model('admin/admin_location_model');
        $this->load->model('admin/admin_slope_model');
        $this->load->model('admin/admin_location_model');
        //$this->load->model('item_model');
    }
    
    public function index(){
        
        // Get all information from all the created resorts
        $data_location = $this->admin_location_model->get_all_location_data();
        $total = 0;
        
        $table = '<table id="admin_location_table" align="center" class="table table-responsive achievements small_text11">';
        $table .= '<thead><tr>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_location'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_sector'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['coordinates'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['id_group'].'</th>';
            $table .= '<th>'.$this->lang->line('slope')['length'].'</th>';
            $table .= '<th>'.$this->lang->line('admin_page')['area'].'</th>';
            $table .= '<th style="width: 80px;">'.$this->lang->line('admin_page')['Actions'].'</th>';
            $table .= '</tr></thead>';
            $table .= '<tbody>';
            
            $table .= '</tbody></table>';
        
        $data['table'] = $table;
        $data['title'] = $this->lang->line('admin_page')['locationlist'];
        $data['page_type'] = 'location';
        $data['main_content'] = 'admin/adminBuildingItemsView';
        $this->load->view('templates/default_admin',$data); 
            
    }
    public function getDataTable(){
        $data_location = $this->admin_location_model->get_all_location_data();
        $data_table = $data_location->result();
        echo json_encode(array('Data' => $data_table));
    }
       
    
     
    public function edit_locations($id_group){
        $data_location = $this->admin_location_model->get_group_location_data($id_group);
        $data['location_array'] = $data_location->result();
        $row_location_array = $data_location->row();
        $data['length'] = $row_location_array->length;
        $data['id_group'] = $id_group;
        $data['page_type'] = 'location';
        $data['main_content'] = 'admin/editLocationView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function add_new(){
        $data['title'] = $this->lang->line('admin_page')['add_new_location'];
        $max_id_group = $this->admin_location_model->get_max_value_DB('id_group', 'game_locations');
        $data['max_id_group'] = $max_id_group+1;
        $max_id_area = $this->admin_location_model->get_max_value_DB('area', 'game_locations');
        $data['max_id_area'] = array($max_id_area+1,$max_id_area+2);
        $data['main_content'] = 'admin/addLocationView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    
    public function add_new_location_validation() {
        if (isset ($_POST['add_new_location'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_group', $this->lang->line('admin_page')['id_group'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('id_sector[]', $this->lang->line('admin_page')['id_sector'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('coordinates', $this->lang->line('admin_page')['coordinates'], 'trim|required|max_length[50]');
                $this->form_validation->set_rules('length', $this->lang->line('slope')['length'], 'trim|required|max_length[5]');
                $this->form_validation->set_rules('area[]', $this->lang->line('admin_page')['area'], 'trim|required|max_length[2]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['add_location_error_id_group'] = form_error('id_group');
                    $data['add_location_error_id_sector'] = form_error('id_sector[]');
                    $data['add_location_error_coordinates'] = form_error('coordinates');
                    $data['add_location_error_length'] = form_error('length');
                    $data['add_location_error_area'] = form_error('area[]');
                    $this->session->set_flashdata('msg','');

                    $id_group_page = $this->input->post('id_group', TRUE);
                    $data['id_group'] = $id_group_page;
                    $max_id_group = $this->admin_location_model->get_max_value_DB('id_group', 'game_locations');
                    $data['max_id_group'] = $max_id_group+1;
                    $max_id_area = $this->admin_location_model->get_max_value_DB('area', 'game_locations');
                    $data['max_id_area'] = array($max_id_area+1,$max_id_area+2);
                    $data['title'] = $this->lang->line('admin_page')['add_new_location'];
                    $data['page_type'] = 'location';
                    $data['main_content'] = 'admin/addLocationView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_group = $this->input->post('id_group', TRUE);
                    $id_sector[] = array_map('trim',$this->input->post('id_sector[]', TRUE));
                    
                    $posted_coordinates = $this->input->post('coordinates', TRUE);
                    $start_to_end = substr($posted_coordinates, strpos($posted_coordinates, '[')+2, strlen($posted_coordinates));
                    $first_x = substr($start_to_end, 0, strpos($start_to_end, ','));
                    $x_to_end_y = substr($start_to_end, strpos($start_to_end, ',')+1, strlen($start_to_end));
                    $first_y = substr($x_to_end_y, 0, strpos($x_to_end_y, ']'));
                    $first_y_to_end = substr($x_to_end_y, strpos($x_to_end_y, '[')+1, strlen($x_to_end_y));
                    $second_x = substr($first_y_to_end, 0, strpos($first_y_to_end, ','));
                    $second_x_to_end = substr($first_y_to_end, strpos($first_y_to_end, ',')+1, strlen($first_y_to_end));
                    $second_y = substr($second_x_to_end, 0, strpos($second_x_to_end, ']'));
                    $x_coordinates[0] = $first_x;
                    $y_coordinates[0] = $first_y;
                    $x_coordinates[1] = $second_x;
                    $y_coordinates[1] = $second_y;
                    
                    $length = $this->input->post('length', TRUE);
                    $area[] = array_map('trim',$this->input->post('area[]', TRUE));
                    
                    for ($i=0; $i<2; $i++) {
                        $data_insert = array (
                            'id_group' => $id_group,
                            'id_sector' => $id_sector[0][$i],
                            'x_coordinates' => $x_coordinates[$i],
                            'y_coordinates' => $y_coordinates[$i],
                            'length' => $length,
                            'area' => $area[0][$i],
                        );
                        $query[] = $this->admin_slope_model->add_item_admin($data_insert, 'game_locations');       //Add location to game_locations table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succeedded
                   
                            $id_group = $this->input->post('id_group', TRUE);
                            $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['location_added'].'</div>');
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['add_new_location'];
                            $max_id_group = $this->admin_location_model->get_max_value_DB('id_group', 'game_locations');
                            $data['max_id_group'] = $max_id_group+1;
                            $max_id_area = $this->admin_location_model->get_max_value_DB('area', 'game_locations');
                            $data['max_id_area'] = array($max_id_area+1,$max_id_area+2);
                            $data['page_type'] = 'location';
                            $data['main_content'] = 'admin/addLocationView';
                            $this->load->view('templates/default_admin',$data);
                        }
                        else {
                            $id_group = $this->input->post('id_group', TRUE);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['location_not_added_all_failed'].'</div>');
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['add_new_location'];
                            $max_id_group = $this->admin_location_model->get_max_value_DB('id_group', 'game_locations');
                            $data['max_id_group'] = $max_id_group+1;
                            $max_id_area = $this->admin_location_model->get_max_value_DB('area', 'game_locations');
                            $data['max_id_area'] = array($max_id_area+1,$max_id_area+2);
                            $data['page_type'] = 'location';
                            $data['main_content'] = 'admin/addLocationView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {
                        $id_group = $this->input->post('id_group', TRUE);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['location_not_added_one_failed'].'</div>');
                        $data['id_group'] = $id_group;
                        $data['title'] = $this->lang->line('admin_page')['add_new_location'];
                        $max_id_group = $this->admin_location_model->get_max_value_DB('id_group', 'game_locations');
                        $data['max_id_group'] = $max_id_group+1;
                        $max_id_area = $this->admin_location_model->get_max_value_DB('area', 'game_locations');
                        $data['max_id_area'] = array($max_id_area+1,$max_id_area+2);
                        $data['page_type'] = 'location';
                        $data['main_content'] = 'admin/addLocationView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_location_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            //$id_location = $this->input->post('id_location', TRUE);
            $id_group = $this->input->post('id_group', TRUE);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['location_not_added_no_post'].'</div>');
            $data['id_group'] = $id_group;
            $data['title'] = $this->lang->line('admin_page')['add_new_location'];
            $max_id_group = $this->admin_location_model->get_max_value_DB('id_group', 'game_locations');
            $data['max_id_group'] = $max_id_group+1;
            $max_id_area = $this->admin_location_model->get_max_value_DB('area', 'game_locations');
            $data['max_id_area'] = array($max_id_area+1,$max_id_area+2);
            $data['page_type'] = 'location';
            $data['main_content'] = 'admin/addLocationView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    public function update_location_admin() {
        if (isset ($_POST['edit_location_admin'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_location[]', $this->lang->line('admin_page')['id_location'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('id_group', $this->lang->line('admin_page')['id_group'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('id_sector[]', $this->lang->line('admin_page')['id_sector'], 'trim|required|max_length[3]|integer');
                $this->form_validation->set_rules('x_coordinates[]', $this->lang->line('admin_page')['x_coordinates'], 'trim|required|max_length[8]');
                $this->form_validation->set_rules('y_coordinates[]', $this->lang->line('admin_page')['y_coordinates'], 'trim|required|max_length[8]');
                $this->form_validation->set_rules('length', $this->lang->line('slope')['length'], 'trim|required|max_length[5]');
                $this->form_validation->set_rules('area[]', $this->lang->line('admin_page')['area'], 'trim|required|max_length[2]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_location_error_id_location'] = form_error('id_location[]');
                    $data['edit_location_error_id_group'] = form_error('id_group');
                    $data['edit_location_error_id_sector'] = form_error('id_sector[]');
                    $data['edit_location_error_x_coordinates'] = form_error('x_coordinates[]');
                    $data['edit_location_error_y_coordinates'] = form_error('y_coordinates[]');
                    $data['edit_location_error_length'] = form_error('length');
                    $data['edit_location_error_area'] = form_error('area[]');
                    $this->session->set_flashdata('msg','');
                    $data_location = $this->admin_location_model->get_group_location_data($this->input->post('id_group', TRUE));
                    $data['location_array'] = $data_location->result();
                    $row_location_array = $data_location->row();
                    $data['length'] = $row_location_array->length;
                    $data['id_group'] = $this->input->post('id_group', TRUE);
                    $data['title'] = $this->lang->line('admin_page')['locationlist'];
                    $data['page_type'] = 'location';
                    $data['main_content'] = 'admin/editLocationView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                   // all fields are correct
                    $id_location[] = array_map('trim',$this->input->post('id_location[]', TRUE));
                    $id_group = $this->input->post('id_group', TRUE);
                    $id_sector[] = array_map('trim',$this->input->post('id_sector[]', TRUE));
                    $x_coordinates[] = array_map('trim',$this->input->post('x_coordinates[]', TRUE));
                    $y_coordinates[] = array_map('trim',$this->input->post('y_coordinates[]', TRUE));
                    $length = $this->input->post('length', TRUE);
                    $area[] = array_map('trim',$this->input->post('area[]', TRUE));
                    
                    for ($i=0; $i<2; $i++) {
                        $query[] = $this->admin_location_model->update_location_admin($id_location[0][$i], $id_group, $id_sector[0][$i], $x_coordinates[0][$i], $y_coordinates[0][$i], $length, $area[0][$i]);       //update location table
                    }
                    if(count(array_unique($query)) === 1){  
                        if (current($query) === true){               // If the query succeedded
                    
                            //$id_location[] = $this->input->post('id_location[]', TRUE);
                            $id_group = $this->input->post('id_group', TRUE);
                            $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['location_updated'].'</div>');
                            $data_location = $this->admin_location_model->get_group_location_data($id_group);
                            $data['location_array'] = $data_location->result();
                            $row_location_array = $data_location->row();
                            $data['length'] = $row_location_array->length;
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['locationlist'];
                            $data['page_type'] = 'location';
                            $data['main_content'] = 'admin/editLocationView';
                            $this->load->view('templates/default_admin',$data);
                        }
                        else {
                            //$id_location = $this->input->post('id_location', TRUE);
                            $id_group = $this->input->post('id_group', TRUE);
                            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['location_not_updated_all_failed'].'</div>');
                            $data_location = $this->admin_location_model->get_group_location_data($id_group);
                            $data['location_array'] = $data_location->result();
                            $row_location_array = $data_location->row();
                            $data['length'] = $row_location_array->length;
                            $data['id_group'] = $id_group;
                            $data['title'] = $this->lang->line('admin_page')['locationlist'];
                            $data['page_type'] = 'location';
                            $data['main_content'] = 'admin/editLocationView';
                            $this->load->view('templates/default_admin',$data);
                        }
                    }
                    else {
                        $id_group = $this->input->post('id_group', TRUE);
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['location_not_updated_one_failed'].'</div>');
                        $data_location = $this->admin_location_model->get_group_location_data($id_group);
                        $data['location_array'] = $data_location->result();
                        $row_location_array = $data_location->row();
                        $data['length'] = $row_location_array->length;
                        $data['id_group'] = $id_group;
                        $data['title'] = $this->lang->line('admin_page')['locationlist'];
                        $data['page_type'] = 'location';
                        $data['main_content'] = 'admin/editLocationView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {  // Back button
                redirect('admin/admin_location_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            //$id_location = $this->input->post('id_location', TRUE);
            $id_group = $this->input->post('id_group', TRUE);
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['location_not_updated_no_post'].'</div>');
            $data_location = $this->admin_location_model->get_group_location_data($id_group);
            $data['location_array'] = $data_location->result();
            $row_location_array = $data_location->row();
            $data['length'] = $row_location_array->length;
            $data['id_group'] = $id_group;
            $data['title'] = $this->lang->line('admin_page')['locationlist'];
            $data['page_type'] = 'location';
            $data['main_content'] = 'admin/editLocationView';
            $this->load->view('templates/default_admin',$data);
        }
    }

}

?>