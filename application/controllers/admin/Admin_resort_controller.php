<?php

class Admin_resort_controller extends CI_Controller{
    
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
        $ci->lang->load('finances',$siteLang);
        $ci->lang->load('admin_pages',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('weather',$siteLang);
        $ci->lang->load('resort',$siteLang);
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_resort_model');
        $this->load->model('resort_model');
    }
    
    public function index(){
        // Get all information from all the created resorts
        $data['data_resort'] = $this->admin_resort_model->get_resort_Data();
        
        
        // For each resort we associate the information to a variable linked to the player ID
        foreach ($data['data_resort'] as $rec) {
            $data['num_slopes'][$rec->id_resort] = $this->admin_resort_model->count_items_resort($rec->id_resort, 'game_created_slopes');
            $data['num_lifts'][$rec->id_resort] = $this->admin_resort_model->count_items_resort($rec->id_resort, 'game_created_lifts');
            $data['num_buildings'][$rec->id_resort] = $this->admin_resort_model->count_items_resort($rec->id_resort, 'game_created_buildings');
            $data['num_staff'][$rec->id_resort] = $this->admin_resort_model->count_items_resort($rec->id_resort, 'game_hired_staff');
            $data['num_equipments'][$rec->id_resort] = $this->admin_resort_model->count_items_resort($rec->id_resort, 'game_purchased_equipments');
            $data['visitors_today'][$rec->id_resort] = $this->admin_resort_model->get_last_day_stats($rec->id_resort, 'affluence');
            $data['visitors_sum'][$rec->id_resort] = $this->admin_resort_model->get_sum_stats($rec->id_resort, 'affluence');
            $data['injuries_today'][$rec->id_resort] = $this->admin_resort_model->get_last_day_stats($rec->id_resort, 'injuries');
            $data['injuries_sum'][$rec->id_resort] = $this->admin_resort_model->get_sum_stats($rec->id_resort, 'injuries');
            $temp_status_tourist_info = $this->admin_resort_model->get_building_info_status($rec->id_resort, '1');
            $sector_access_array = $this->resort_model->get_sector_access($rec->id_resort); // returns: [0] => NULL, [1] => [1], [2] => [2]
            
            for ($sector_id = 0; $sector_id <= ACTIVE_SECTORS ; $sector_id ++) {
                if (isset($sector_access_array[$sector_id]))
                    $data['sector'][$rec->id_resort][$sector_id] = '1';
                else
                    $data['sector'][$rec->id_resort][$sector_id] = '0';
            }
            if (!isset($temp_status_tourist_info->id_status) || $temp_status_tourist_info->id_status == NULL)
                $data['status_tourist_info'][$rec->id_resort]['id_status'] = 'X';
            else if ($temp_status_tourist_info->id_status == 1)
                $data['status_tourist_info'][$rec->id_resort]['id_status'] = $this->lang->line('home')['building_status_to_show_open'];
            else if ($temp_status_tourist_info->id_status == 2)
                $data['status_tourist_info'][$rec->id_resort]['id_status'] = $this->lang->line('home')['building_status_to_show_closed'];
            else if ($temp_status_tourist_info->id_status == 3)
                $data['status_tourist_info'][$rec->id_resort]['id_status'] = $this->lang->line('home')['building_status_to_show_under_maintenance'];
            else if ($temp_status_tourist_info->id_status == 4)
                $data['status_tourist_info'][$rec->id_resort]['id_status'] = $this->lang->line('home')['building_status_to_show_under_construction'];
        }
        
        // Create the "delete all" button
        $data['delete_button_all'] = '<a href="?action=delete" class="delete-dialog-admin-all btn-danger">'.$this->lang->line('admin_page')['delete_all'].'</a>';
        
        // Create the delete button
        $data['delete_button'] = '<a href="?action=delete" class="delete-dialog-admin btn-danger">'.$this->lang->line('admin_page')['delete'].'</a>';
        $data['main_content'] = 'admin/adminResortView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    /**
     * delete_action        Prepare the delete query for the posted resort (with ajax function in popup dialog)
     * 
     * @return boolean      Returns true or false
     */
    public function delete_action(){
        // parameters posted by the ajax function of the popup dialog
        $id_resort = trim($this->input->post('id_resort', TRUE));
        $id_player = trim($this->input->post('id_player', TRUE));
        if ($id_resort != 'all')
            $id_resort = $this->users_model->get_resort_id($id_player);    
        // Deletes the resort from the DB
        $delete_resort = $this->admin_resort_model->delete_resort_db($id_resort);
        if ($delete_resort) {
            // Everything related to id_resort below is not needed since it is a foreign key and will be removed automatically when deleting the resort.
            // Only a few things related to id_player need to be deleted manually (logs and achievements)
            
           /* // Deletes the player's slopes from the DB
            $delete_related_slopes = $this->admin_resort_model->delete_items_db($id_resort, 'game_created_slopes');
            // Deletes the player's lifts from the DB
            $delete_related_lifts = $this->admin_resort_model->delete_items_db($id_resort, 'game_created_lifts');
            // Deletes the player's buildings from the DB
            $delete_related_buildings = $this->admin_resort_model->delete_items_db($id_resort, 'game_created_buildings');
            // Deletes the player's staff from the DB
            $delete_related_staff = $this->admin_resort_model->delete_items_db($id_resort, 'game_hired_staff');
            // Deletes the player's equipments from the DB
            $delete_related_equipments = $this->admin_resort_model->delete_items_db($id_resort, 'game_purchased_equipments');
            // Deletes the player's achievements from the DB*/
            $delete_related_achievements = $this->admin_resort_model->delete_items_db_player_id($id_player, 'user_achievements');
            // Deletes the player's logs from the DB
            $delete_related_logs = $this->admin_resort_model->delete_items_db_player_id($id_player, 'game_player_logs');
           /* // Deletes the player's access to sectors from the DB
            $delete_related_access_sectors = $this->admin_resort_model->delete_items_db($id_resort, 'game_access_sector');
            // Deletes the player's weather forecast from the DB
            $delete_related_forecast = $this->admin_resort_model->delete_items_db_player_id($currentUserID, 'game_extended_forecast');
                    */
            // If everything succeded, we return true
            if ($delete_related_achievements && $delete_related_logs) {
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

    
    public function edit_resort($id_resort){
        $data_resort = $this->resort_model->display_resort_info_DB($id_resort);
        $data['data_resort_object'] = $data_resort->row();    
        
        // Getting values for the SELECT of the skipasses price
        $data['skipass_data'] = $this->get_select_skipass($data['data_resort_object']->skipass_daily, $data['data_resort_object']->skipass_weekly);
      
                
        // Create the "delete all" button
        $data['main_content'] = 'admin/editResortView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function update_admin_resort() {
        if (isset ($_POST['edit_admin_resort'])) {           // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {     
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_resort', $this->lang->line('admin_page')['id_resort'], 'trim|required|max_length[10]|integer');
                $this->form_validation->set_rules('resort_name', $this->lang->line('home')['resort_name'], 'trim|required|min_length[3]|max_length[45]|callback_resort_name_available_or_current|callback_alpha_dash_space');
                $this->form_validation->set_rules('resort_country', $this->lang->line('home')['country_field'], 'trim|required|min_length[1]|max_length[45]');
                $this->form_validation->set_rules('resort_description', $this->lang->line('resort')['description_field_error'], 'trim|min_length[10]|max_length[550]');
                $this->form_validation->set_rules('cash', $this->lang->line('admin_page')['Cash'], 'trim|max_length[11]|integer');
                $this->form_validation->set_rules('snow_level', $this->lang->line('weather')['snow_level'], 'trim|max_length[4]|integer');
                $this->form_validation->set_rules('reputation', $this->lang->line('home')['reputation'], 'trim|max_length[11]|integer');
                $this->form_validation->set_rules('skipass_daily', $this->lang->line('home')['skipass_daily'], 'trim|required|max_length[4]|integer');
                $this->form_validation->set_rules('skipass_weekly', $this->lang->line('home')['skipass_weekly'], 'trim|required|max_length[4]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_resort_error_id_resort'] = form_error('id_resort');
                    $data['edit_resort_error_resort_name'] = form_error('resort_name');
                    $data['edit_resort_error_country'] = form_error('resort_country');
                    $data['edit_resort_error_resort_description'] = form_error('resort_description');
                    $data['edit_resort_error_cash'] = form_error('cash');
                    $data['edit_resort_error_snow_level'] = form_error('snow_level');
                    $data['edit_resort_error_reputation'] = form_error('reputation');
                    $data['edit_resort_error_skipass_daily'] = form_error('skipass_daily');
                    $data['edit_resort_error_skipass_weekly'] = form_error('skipass_weekly');

        
                    $data_resort = $this->resort_model->display_resort_info_DB(trim($this->input->post('original_id_resort', TRUE)));
                    $data['data_resort_object'] = $data_resort->row();  
                    // Getting values for the SELECT of the skipasses price
                    $data['skipass_data'] = $this->get_select_skipass($this->input->post('skipass_daily', TRUE), $this->input->post('skipass_weekly', TRUE));
                   
                    $data['main_content'] = 'admin/editResortView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                  // all fields are correct
                    $id_resort = trim($this->input->post('id_resort', TRUE));
                    $original_id_resort = trim($this->input->post('original_id_resort', TRUE));
                    $resort_name = trim($this->input->post('resort_name', TRUE));
                    $country = trim($this->input->post('resort_country', TRUE));
                    $resort_description = trim($this->input->post('resort_description', TRUE));
                    $cash = trim($this->input->post('cash', TRUE));
                    $snow_level = trim($this->input->post('snow_level', TRUE));
                    $reputation = trim($this->input->post('reputation', TRUE));
                    $skipass_daily = trim($this->input->post('skipass_daily', TRUE));
                    $skipass_weekly = trim($this->input->post('skipass_weekly', TRUE));

                    $query = $this->resort_model->update_resort_DB($id_resort, $original_id_resort, $resort_name, $country, $resort_description, $cash, $snow_level, $reputation, $skipass_daily, $skipass_weekly);       //update user with admin mode
                    if ($query === true) {   // If the query succedded
                        $id_resort = trim($this->input->post('id_resort', TRUE));
                        $data_resort = $this->resort_model->display_resort_info_DB($id_resort);
                        // Getting values for the SELECT of the skipasses price
                        $data['skipass_data'] = $this->get_select_skipass($this->input->post('skipass_daily', TRUE), $this->input->post('skipass_weekly', TRUE));
                        $data['data_resort_object'] = $data_resort->row();    
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['resort_updated'].'</div>');
                        $data['main_content'] = 'admin/editResortView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {                        //update query failed
                        $original_id_resort = trim($this->input->post('original_id_resort', TRUE));
                        $data_resort = $this->resort_model->display_resort_info_DB($original_id_resort);
                        // Getting values for the SELECT of the skipasses price
                        $data['skipass_data'] = $this->get_select_skipass($this->input->post('skipass_daily', TRUE), $this->input->post('skipass_weekly', TRUE));
                        $data['data_resort_object'] = $data_resort->row();   
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['resort_not_updated'].'</div>');
                        $data['main_content'] = 'admin/editResortView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {
                redirect('admin/admin_resort_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $original_id_resort = trim($this->input->post('original_id_resort', TRUE));
            $data_resort = $this->resort_model->display_resort_info_DB($original_id_resort);
            $data['skipass_data'] = $this->get_select_skipass($this->input->post('skipass_daily', TRUE), $this->input->post('skipass_weekly', TRUE));
            $data['data_resort_object'] = $data_resort->row();  
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['resort_not_updated_no_post'].'</div>');
            $data['main_content'] = 'admin/editResortView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    public function alpha_dash_space($str)    {
        return ( ! preg_match("/^([a-z0-9\-\s\_À-ÿ])+$/i", $str)) ? FALSE : TRUE;
    }
    
    public function resort_name_available_or_current($requested_resort_name){ //custom callback function to check if resort_name already used   or current (not changed)    
        $id_resort = trim($this->input->post('original_id_resort', TRUE));
        $resort_info = $this->resort_model->display_resort_info_DB($id_resort);          // to be used in this file
        $resort_info_data = $resort_info->row();
        $current_resort_name = $resort_info_data->resort_name;
        $resort_name_not_in_use = $this->resort_model->resort_available_in_DB($requested_resort_name);
        if ($requested_resort_name == $current_resort_name || $resort_name_not_in_use === true) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    protected function get_select_skipass($skipassDaily, $skipassWeekly) {
        // The One Day dropdown
        $data['selectArrayOneDay'] = '<option value="10">10</option>';
        for ($i=11;$i<=60;$i++) {
            if ($i==$skipassDaily) {
                $data['selectArrayOneDay'] .= '<option value="'.$i.'" selected>'.$i.'</option>';
            }
            else
                $data['selectArrayOneDay'] .= '<option value="'.$i.'">'.$i.'</option>';
        }
        // The One Week dropdown
        $data['selectArrayOneWeek'] = '<option value="70">70</option>';
        for ($i=80; $i<=420; $i+= 10) {
            if ($i==$skipassWeekly) {
                $data['selectArrayOneWeek'] .= '<option value="'.$i.'" selected>'.$i.'</option>';
            }
            else
                $data['selectArrayOneWeek'] .= '<option value="'.$i.'">'.$i.'</option>';
        }
        return $data;
    }
    
    
}

?>