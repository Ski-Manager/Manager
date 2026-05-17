<?php

class Admin_player_controller extends CI_Controller{
    
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
        $ci->lang->load('signup_form',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('slope',$siteLang);
        $ci->lang->load('leaderboard',$siteLang);
        $this->load->model('admin/admin_player_model');
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
    }
    
    public function index($offset = 0){
        $per_page = 25;

        $this->load->library('pagination');
        $total = $this->admin_player_model->get_player_count();

        $config['base_url']   = base_url('admin/admin_player_controller/index');
        $config['total_rows'] = $total;
        $config['per_page']   = $per_page;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);

        $data['data_player'] = $this->admin_player_model->get_player_Data($per_page, $offset);
        $data['pagination']  = $this->pagination->create_links();

        // Create the "delete all" button
        $data['delete_button_all'] = '<a href="?action=delete" class="delete-dialog-admin-all btn-danger">'.$this->lang->line('admin_page')['delete_all'].'</a>';
        
        // Create the delete button
        $data['delete_button'] = '<a href="?action=delete" class="delete-dialog-admin btn-danger">'.$this->lang->line('admin_page')['delete'].'</a>';
        $data['duplicate_button'] = '<a class="duplicate_button"><i class="fa-solid fa-clone" title="'.$this->lang->line('admin_page')['duplicate'].'"></i></a>';
        $data['activate_button'] = '<a class="activate_button"><i class="fa-solid fa-play" title="'.$this->lang->line('admin_page')['activate'].'"></i></a>';
        $data['impersonate_button'] = '<a class="impersonate_button btn btn-warning btn-sm" title="'.$this->lang->line('admin_page')['impersonate'].'"><img src="'.base_url('img/icons/impersonate.png').'" style="width:16px;height:16px;vertical-align:middle;"/> '.$this->lang->line('admin_page')['impersonate'].'</a>';
        $data['main_content'] = 'admin/adminPlayerView';
        $this->load->view('templates/default_admin',$data); 
    }
        
    /**
     * delete_action        Prepare the delete query for all the players (with ajax function in popup dialog)
     * 
     * @return boolean      Returns true or false
     */
    public function delete_action(){
        // parameters posted by the ajax function of the popup dialog
        $id_resort = trim($this->input->post('id_resort', TRUE));
        $id_player = trim($this->input->post('id_player', TRUE));
        // Deletes the player from the DB
        $delete_player = $this->admin_player_model->delete_player_db($id_player);
        if ($delete_player)
            echo json_encode(array('returned' => true));
        else
            echo json_encode(array('returned' => false)); 
    }
       
    public function duplicate_action(){
        // parameters posted by the ajax function
        $id_resort = trim($this->input->post('id_resort', TRUE));
        $id_player = trim($this->input->post('id_player', TRUE));
        $username = trim($this->input->post('username', TRUE));
                
        // Generate a random key that will be added at the end of some column for better idenfification and avoiding duplicates
        $rand_key = rand(1, 100000);
        // Duplicates the player in the DB, based on current user ID and random key
        $duplicate_player = $this->admin_player_model->duplicate_player_db($id_player, $rand_key);
        if ($duplicate_player) {
            // We get the last inserted ID based on the random key and original username
            // The new username should be <original_username>_<random_key>
            $last_inserted_id_player = $this->admin_player_model->get_last_inserted_id_player($username, $rand_key);
            // Duplicates the resort based on the current one
            $duplicate_resort = $this->admin_player_model->duplicate_resort_db($id_player, $id_resort, $rand_key, $last_inserted_id_player);
            // We get the last inserted resort ID
            $last_inserted_id_resort = $this->admin_player_model->get_last_inserted_id_resort($last_inserted_id_player);
         
            // Duplicates the history stats for the inserted resort
            // We need the newly created ID resort and the original one to copy the data from
            $array_tables = array ('affluence', 'reputation', 'prestige', 'cash', 'cost_purchases', 'cost_salaries', 'cost_upkeep', 'expenses', 'injuries', 'revenue', 'rev_marketing', 'rev_tournaments', 'rev_hotel', 'rev_instructor', 'cost_marketing', 'cost_tournaments', 'rev_leisure', 'rev_medical', 'rev_off_season', 'rev_other', 'rev_parking', 'rev_achievements', 'rev_rental', 'rev_restaurant', 'rev_skibus', 'rev_skipass', 'snow_level', 'season');
            $array_tables = array ('affluence', 'reputation', 'prestige', 'cash', 'cost_purchases', 'cost_salaries', 'cost_upkeep', 'expenses', 'injuries', 'revenue', 'rev_marketing', 'rev_tournaments', 'rev_hotel', 'rev_instructor', 'cost_marketing', 'cost_tournaments', 'rev_leisure', 'rev_luxury', 'rev_medical', 'rev_other', 'rev_parking', 'rev_achievements', 'rev_rental', 'rev_restaurant', 'rev_skibus', 'rev_skipass', 'snow_level', 'season');
            foreach ($array_tables as $table_name) {
                //$create_stat_new_day = $this->create_stat_new_day($id_resort, $table_name);
                $this->admin_player_model->duplicate_history_stats($last_inserted_id_resort, $id_resort, 'game_resort_'.$table_name);
            }
            // Duplicates the buildings for the the player
            $duplicate_buildings = $this->admin_player_model->duplicate_buildings_and_items($last_inserted_id_resort, $id_resort, 'game_created_buildings', 'id_created_buildings', $rand_key);
            // Duplicates the slopes for the the player
            $duplicate_slopes = $this->admin_player_model->duplicate_buildings_and_items($last_inserted_id_resort, $id_resort, 'game_created_slopes', 'id_created_slopes', $rand_key);
            // Duplicates the lifts for the the player           
            $duplicate_lifts = $this->admin_player_model->duplicate_buildings_and_items($last_inserted_id_resort, $id_resort, 'game_created_lifts', 'id_created_lifts', $rand_key);   
            // Duplicates the equipments for the player
            $duplicate_equipments = $this->admin_player_model->duplicate_buildings_and_items($last_inserted_id_resort, $id_resort, 'game_purchased_equipments', 'id_purchased_equipments', $rand_key);
            // Duplicates the achievements for the player
            $duplicate_achievements = $this->admin_player_model->duplicate_achievements($last_inserted_id_player, $id_player, 'user_achievements');
            // Duplicates the achievements for the resort
            $duplicate_access_sectors = $this->admin_player_model->duplicate_history_stats($last_inserted_id_resort, $id_resort, 'game_access_sector');
            // Duplicates the staff for the player and set the id_item_assigned to NULL
            $duplicate_staff = $this->admin_player_model->duplicate_buildings_and_items($last_inserted_id_resort, $id_resort, 'game_hired_staff', 'id_hired_staff', $rand_key);
            $get_new_player_staff = $this->admin_player_model->get_new_player_staff($last_inserted_id_resort);
            foreach ($get_new_player_staff as $staff_data) {
                if ($staff_data->type_item_assigned == 'slope'){
                    $get_last_unassigned_item_id = $this->admin_player_model->get_last_unassigned_item_id($last_inserted_id_resort, 'game_created_slopes', 'id_created_slopes');
                    foreach ($get_last_unassigned_item_id as $unassigned_item_id){
                        $assign_staff_to_item = $this->admin_player_model->assign_staff_to_item($last_inserted_id_resort, $unassigned_item_id->id_created_slopes, $staff_data->id_hired_staff);
                    }
                }
                if ($staff_data->type_item_assigned == 'lift'){
                    $get_last_unassigned_item_id = $this->admin_player_model->get_last_unassigned_item_id($last_inserted_id_resort, 'game_created_lifts', 'id_created_lifts');
                    foreach ($get_last_unassigned_item_id as $unassigned_item_id){
                        $assign_staff_to_item = $this->admin_player_model->assign_staff_to_item($last_inserted_id_resort, $unassigned_item_id->id_created_lifts, $staff_data->id_hired_staff);
                    }
                }
                if ($staff_data->type_item_assigned == 'groomer' || $staff_data->type_item_assigned == 'skibus'){
                    $get_last_unassigned_item_id = $this->admin_player_model->get_last_unassigned_item_id($last_inserted_id_resort, 'game_purchased_equipments', 'id_purchased_equipments');
                    foreach ($get_last_unassigned_item_id as $unassigned_item_id){
                        $assign_staff_to_item = $this->admin_player_model->assign_staff_to_item($last_inserted_id_resort, $unassigned_item_id->id_purchased_equipments, $staff_data->id_hired_staff);
                    }
                }
                if ($staff_data->type_item_assigned == 'sector'){
                    $get_assigned_id_sector = $this->admin_player_model->get_assigned_id_sector($id_resort, 'game_purchased_equipments', 'id_purchased_equipments');
                    if ($get_assigned_id_sector->num_rows() > 0) {
                        foreach ($get_assigned_id_sector->result() as $assigned_id_sector) {
                            $assign_staff_to_item = $this->admin_player_model->assign_staff_to_item($last_inserted_id_resort, $assigned_id_sector->id_item_assigned, $staff_data->id_hired_staff);
                        }
                    }
                }
            }
            
            if ($duplicate_resort) {   
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
    
    
    
    /**
     * create_stat_new_day    Add new entry in the DB for the new coming day
     * 
     * @param type $id_resort     Resort ID
     * @param type $type                        Returns the result of the database query           
     */
    // To be merged??
    protected function create_stat_new_day($id_resort, $type) {
            $current_day = get_day_of_season($id_resort);
            $current_season = get_current_season($id_resort);
            $create_stat_new_day_DB = $this->create_stat_new_day_DB($id_resort, $type);
            if ($create_stat_new_day_DB)
                return true;
            else 
                return $create_stat_new_day_DB;
    }    
    
    /**
     * create_stat_new_day_DB     Runs the query to add new stats entries for the new day
     * 
     * @param type $id_resort                       ID of the resort
     * @param type $type                            Type of item to handle (injuries, affluence...)
     * @return string                               Returns the result (info message)
     */
    protected function create_stat_new_day_DB($id_resort, $type){
        $result = '';
        $current_date = gmdate('Y-m-d'); 
        //$current_date = '2017-01-01';
        $this->db->trans_start();
        if ($type == 'season')
            $date_column = 'start_date';
        else {
            $date_column = 'date';
        }
        $data = array ('id_resort' => $id_resort, $date_column => $current_date, $type => '0');
        $query = $this->db->insert('game_resort_'.$type, $data);
        $this->db->trans_complete();
        
        $result .= 'Table '.$type.' updated for resort ID '.$id_resort.' with new row for '.$current_date.'<br>';
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $result;
        }
    }
    
    public function activate_action(){
        
        // parameters posted by the ajax function
        $id_player = trim($this->input->post('id_player', TRUE));
        
        $activate_player = $this->admin_player_model->activate_player($id_player);
        if ($activate_player) {   
            return true;
        }
        else {
            return false;
        }
    }
    
    public function impersonate_action(){
        
        $id_player = trim($this->input->post('id_player', TRUE));
        $impersonate_password = trim($this->input->post('impersonate_password', TRUE));
        $proceed_login = false;
        
        if (isset ($id_player) && isset ($impersonate_password) && $id_player != '' && $impersonate_password != '') {             // If login form was submitted
            if ($impersonate_password == IMPERSONATE_PASSWORD){
                $login_user = $this->users_model->get_username_from_id_player($id_player);
                $login_id_resort = $this->users_model->get_resort_id($id_player);
                $is_admin = $this->users_model->check_if_admin($login_user);
                if ($is_admin === TRUE)
                    $is_admin_session = 1;
                else
                    $is_admin_session = 0;
                $data = [
                    'login_username' => $login_user,
                    'login_id_resort' => $login_id_resort,
                    'is_logged_in' => true,
                    'is_admin' => $is_admin_session      // 0 or 1 depending if user is admin or not
                ];
                $this->session->unset_userdata('userData');
                $this->session->set_userdata($data);            // We set the session
                $proceed_login = true;     
            }
            else {
                $error = 'Could not impersonate. Wrong password';
            }
        }
        else {
            $error = 'Could not impersonate. The required fields were not posted or are empty';
        }
        
        if (isset($proceed_login) && $proceed_login === true) {
            if($is_admin_session === 1)   // he is admin (1)
                echo json_encode(array('returned' => true, 'admin' => true));
            else
                echo json_encode(array('returned' => true, 'admin' => false)); 
        }
        else {
            echo json_encode(array('returned' => false, 'error' => $error));
        }
    }
    
    public function edit_player($id_player){
        $data_player = $this->users_model->get_player_info($id_player);
        $data['data_player_object'] = $data_player->row();    
        // Create the "delete all" button
        $data['main_content'] = 'admin/editPlayerView';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function update_admin_player() {
        if (isset ($_POST['edit_admin_player'])) {             // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            if (isset ($_POST['submit'])) {             
                // validation rules
                $this->load->library('form_validation');
                $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
                $this->form_validation->set_rules('id_player', $this->lang->line('admin_page')['id_player'], 'trim|required|max_length[10]|integer');
                $this->form_validation->set_rules('username', $this->lang->line('home')['username'], 'trim|required|min_length[3]|max_length[25]|callback_username_available_or_current|callback_alpha_dash_space');
                $this->form_validation->set_rules('email', $this->lang->line('home')['email'], 'trim|required|max_length[45]|valid_email|callback_email_available_or_current');
                $this->form_validation->set_rules('hash_password', $this->lang->line('home')['hash_password'], 'trim|min_length[32]|max_length[32]');
                $this->form_validation->set_rules('country', $this->lang->line('home')['country_field'], 'trim|max_length[45]');
                $this->form_validation->set_rules('age', $this->lang->line('signup')['age'], 'trim|max_length[3]|integer');
                $this->form_validation->set_rules('preferred_lang', $this->lang->line('admin_page')['preferred_lang'], 'trim|max_length[20]');
                $this->form_validation->set_rules('genepis', $this->lang->line('navbar')['genepis'], 'trim|max_length[10]|integer');
                $this->form_validation->set_rules('activated', $this->lang->line('admin_page')['activated'], 'trim|max_length[1]|integer');
                $this->form_validation->set_rules('is_admin', $this->lang->line('admin_page')['is_admin'], 'trim|max_length[1]|integer');

                $currentUserID = $this->users_model->get_user_id();          // to be used in this file
                if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                    $data['edit_player_error_id_player'] = form_error('id_player');
                    $data['edit_player_error_username'] = form_error('username');
                    $data['edit_player_error_email'] = form_error('email');
                    $data['edit_player_error_hash_password'] = form_error('hash_password');
                    $data['edit_player_error_country'] = form_error('country');
                    $data['edit_player_error_age'] = form_error('age');
                    $data['edit_player_error_preferred_lang'] = form_error('preferred_lang');
                    $data['edit_player_error_genepis'] = form_error('genepis');
                    $data['edit_player_error_activated'] = form_error('activated');
                    $data['edit_player_error_is_admin'] = form_error('is_admin');


                    $data_player = $this->users_model->get_player_info(trim($this->input->post('original_id_player', TRUE)));
                    $data['data_player_object'] = $data_player->row();    
                    $data['main_content'] = 'admin/editPlayerView';
                    $this->load->view('templates/default_admin',$data);
                }
                else {                  // all fields are correct
                    $id_player = trim($this->input->post('id_player', TRUE));
                    $original_id_player = trim($this->input->post('original_id_player', TRUE));
                    $username = trim($this->input->post('username', TRUE));
                    $email = trim($this->input->post('email', TRUE));
                    $hash_password = trim($this->input->post('hash_password', TRUE));
                    $country = trim($this->input->post('country', TRUE));
                    $age = trim($this->input->post('age', TRUE));
                    $preferred_lang = trim($this->input->post('preferred_lang', TRUE));
                    $genepis = trim($this->input->post('genepis', TRUE));
                    $activated = trim($this->input->post('activated', TRUE));
                    $is_admin = trim($this->input->post('is_admin', TRUE));

                    $query = $this->users_model->update_resort_DB($email, $hash_password, $country, $age, $id_player, $username, $preferred_lang, $genepis, $activated, $is_admin, $original_id_player, 'admin');       //update user with admin mode
                    if ($query) {   // If the query succedded
                        $id_player = trim($this->input->post('id_player', TRUE));
                        $data_player = $this->users_model->get_player_info($id_player);
                        $data['data_player_object'] = $data_player->row();    
                        $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['player_updated'].'</div>');
                        $data['main_content'] = 'admin/editPlayerView';
                        $this->load->view('templates/default_admin',$data);
                    }
                    else {                        //creation of username failed
                        $original_id_player = trim($this->input->post('original_id_player', TRUE));
                        $data_player = $this->users_model->get_player_info($original_id_player);
                        $data['data_player_object'] = $data_player->row();    
                        $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['player_not_updated'].'</div>');
                        $data['main_content'] = 'admin/editPlayerView';
                        $this->load->view('templates/default_admin',$data);
                    }
                }
            }
            else {
                redirect('admin/admin_player_controller');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $original_id_player = trim($this->input->post('original_id_player', TRUE));
            $data_player = $this->users_model->get_player_info($original_id_player);
            $data['data_player_object'] = $data_player->row();    
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['player_not_updated_no_post'].'</div>');
            $data['main_content'] = 'admin/editPlayerView';
            $this->load->view('templates/default_admin',$data);
        }
    }
    
    
    public function username_available_or_current($requested_username){ //custom callback function to check if username is available or current (not changed?
        $id_player = trim($this->input->post('original_id_player', TRUE));
        $player_info = $this->users_model->get_player_info($id_player);        
        $player_info_data = $player_info->row();
        $username = $player_info_data->username;
        $username_available = $this->users_model->check_username_available($requested_username);          // FALSE = not available, TRUE = Available
        if ($username == $requested_username || $username_available === true){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function alpha_dash_space($str)    {
        return ( ! preg_match("/^([a-z0-9\-\s\_À-ÿ])+$/i", $str)) ? FALSE : TRUE;
    }
    
    public function email_available_or_current($requested_email){ //custom callback function to check if emails already used      
        $id_player = trim($this->input->post('original_id_player', TRUE));
        $player_info = $this->users_model->get_player_info($id_player);          // to be used in this file
        $player_info_data = $player_info->row();
        $current_email = $player_info_data->email;
        if ($requested_email != $current_email) {
            $email_not_in_use = $this->users_model->check_email_available($requested_email);
        }
        else {
            $email_not_in_use = true;
        }
        if ($email_not_in_use) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

?>