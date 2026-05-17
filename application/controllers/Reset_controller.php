<?php
/**
 * 
 */
class Reset_controller extends CI_Controller{
    
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
        $ci->lang->load('signup_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('email',$siteLang);
        $this->load->model('users_model');
        $this->load->model('reset_password_model');
        $this->load->model('admin/Admin_resort_model');
        $this->load->model('admin/Admin_player_model');
    }
    

    public function index($action = NULL){
        $data['action'] = $action;
        $data['title'] = '<h2>'.$this->lang->line('signup')['account_info'].'</h2>';
        $data['intro_update_account'] = '<div>'.$this->lang->line('signup')['intro_update_account'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        
        $player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
        $player_info_data = $player_info->row();
        $data['username'] = $player_info_data->username;
        $data['email'] = $player_info_data->email;
        $data['country'] = $player_info_data->country;
        $data['age'] = $player_info_data->age;
        
        // Displaying the account view
        $data['main_content'] = 'account';
        $this->load->view('templates/default',$data);  
    }

    
    public function page_confirm_reset_account ($email, $reset_code, $mode) {
        $reset_code = trim($reset_code);
        $email = trim($email);
        $mode = trim($mode);
        $this->session->set_userdata('mode', $mode);
        $this->session->set_userdata('email', $email);
        $this->session->set_userdata('reset_code', $reset_code);
        $get_data_reset = $this->reset_password_model->validate_email_reset_password('game_reset_account_codes', $email, $reset_code, 'reset_code'); 
        if ($get_data_reset != false) {
            $data_reset = $get_data_reset->row();
            $timestamp = strtotime($data_reset->timestamp);
            $expiration = $timestamp + 86400;
            $now = time();
            
            if ($now < $expiration) {   // within time. OK to reset
                $data['reset_code'] = $reset_code;
                $data['email'] = $email;
                $data['mode'] = $mode;
                $data['language'] = $this->session->userdata('site_lang');
                $data['main_content'] = 'confirm_reset_account';
                $this->load->view('templates/default',$data);
            }
            else {
                // code expired
                $data['reset_code'] = $reset_code;
                $data['email'] = $email;
                $data['status'] = 'code_expired';
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('email')['code_expired'].'</div>');
                $data['main_content'] = 'confirm_reset_account';
                $this->load->view('templates/default',$data);
            }
        }
        else {
            // email not matching code
            $data['reset_code'] = $reset_code;
            $data['email'] = $email;
            $data['status'] = 'invalid_code';
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('email')['invalid_code'].'</div>');
            $data['main_content'] = 'confirm_reset_account';
            $this->load->view('templates/default',$data);
        } 
    }
    
    public function page_confirm_delete_account ($email, $delete_code, $mode) {
        $delete_code = trim($delete_code);
        $email = trim($email);
        $mode = trim($mode);
        $this->session->set_userdata('mode', $mode);
        $this->session->set_userdata('email', $email);
        $this->session->set_userdata('delete_code', $delete_code);
        $get_data_delete = $this->reset_password_model->validate_email_reset_password('game_delete_account_codes', $email, $delete_code, 'delete_code');    
        if ($get_data_delete != false) {
            $data_delete = $get_data_delete->row();
            $timestamp = strtotime($data_delete->timestamp);
            $expiration = $timestamp + 86400;
            $now = time();
            if ($now < $expiration) {   // within time. OK to delete
                $data['delete_code'] = $delete_code;
                $data['email'] = $email;
                $data['mode'] = $mode;
                $data['language'] = $this->session->userdata('site_lang');
                $data['main_content'] = 'confirm_delete_account';
                $this->load->view('templates/default',$data);
            }
            else {
                // code expired
                $data['delete_code'] = $delete_code;
                $data['email'] = $email;
                $data['status'] = 'code_expired';
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('email')['code_expired'].'</div>');
                $data['main_content'] = 'confirm_delete_account';
                $this->load->view('templates/default',$data);
            }
        }
        else {
            // email not matching code
            $data['delete_code'] = $delete_code;
            $data['email'] = $email;
            $data['status'] = 'invalid_code';
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('email')['invalid_code'].'</div>');
            $data['main_content'] = 'confirm_delete_account';
            $this->load->view('templates/default',$data);
        } 
    }
    
    public function proceed_reset_account () {
        if ($this->input->method() === 'post' && $this->input->post('confirm_reset', TRUE) === 'confirm_reset') {
                $reset_code = $this->input->post('reset_code', TRUE);
                $email = $this->input->post('email', TRUE);
                $password_confirm = $this->input->post('password_confirm', TRUE);
                $is_valid = $this->users_model->validate_email_password($email, $password_confirm);
            if ($is_valid) {                    // If there is a match (good email/password)
                $currentUserID = $this->users_model->get_user_id_from_email($email);
                $id_resort = $this->users_model->get_resort_id($currentUserID);
                $username = $this->users_model->get_username_from_id_player($currentUserID);
                // Deletes the resort from the DB
                $delete_resort = $this->admin_resort_model->delete_resort_db($id_resort);
                if ($delete_resort) {
                      
                    // Deletes the player's achievements from the DB*/
                    $delete_related_achievements = $this->admin_resort_model->delete_items_db_player_id($currentUserID, 'user_achievements');
                    // Deletes the player's logs from the DB
                    $delete_related_logs = $this->admin_resort_model->delete_items_db_player_id($currentUserID, 'game_player_logs');
                    if ($delete_related_achievements && $delete_related_logs) {
                        $result = true;
                        $data = array (
                            'id_player' => $currentUserID,
                            'id_resort' => $id_resort,
                            'email' => $email,
                            'username' => $username,
                            'ip_address' => $this->input->ip_address()   
                        );
                        email_admin('player_reset_account', 'tracking_reset_account', $data);    // Sends an email to tracking email address for admin info.
                        $this->reset_password_model->confirm_reset_code_DB('game_reset_account_codes', $email, $reset_code, 'reset_code');
                    }
                    else {
                        $result = false;
                    } 
                    $data_achievement = array (
                        'type' => 'activated',     
                        'email' => $email     
                    );
                    $call_achievements_check = call_achievements_check($data_achievement, 'account');   // Check if a corresponding achievement should be updated
                }
                else {
                    $result = false;
                } 
                if ($result === true) {
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('email')['account_reset_success'].'</div>');
                    $data['status'] = 'reset_successful';
                }
                else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('email')['account_reset_failed'].'</div>');
                    $data['status'] = 'reset_unsuccessful';
                }
            }
            else {          // no match, wrong email password
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('login_form')['wrong_password'].'</div>');
            }
            $data['reset_code'] = $reset_code;
            $data['email'] = $email;
            $data['main_content'] = 'confirm_reset_account';
            $this->load->view('templates/default',$data);
        }
        else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('email')['invalid_request'].'</div>');
            redirect('home');
        }
    }
    
    public function proceed_delete_account () {
        
        if ($this->input->method() === 'post' && $this->input->post('confirm_delete', TRUE) === 'confirm_delete') {
                $delete_code = $this->input->post('delete_code', TRUE);
                $email = $this->input->post('email', TRUE);
                $password_confirm = $this->input->post('password_confirm', TRUE);
                $is_valid = $this->users_model->validate_email_password($email, $password_confirm);
            
            if ($is_valid) {                    // If there is a match (good email/password)
                $currentUserID = $this->users_model->get_user_id_from_email($email);
                $id_resort = $this->users_model->get_resort_id($currentUserID);
                $username = $this->users_model->get_username_from_id_player($currentUserID);
                // Deletes the player from the DB
                $delete_player = $this->admin_player_model->delete_player_db($currentUserID);
                $oauth_uid = $this->users_model->get_oauth_uid_from_email($email);
                if (isset($oauth_uid))
                    $delete_player_fb = $this->admin_player_model->delete_player_facebook_db($oauth_uid);
                $delete_player_link = $this->admin_player_model->delete_player_linked_auth_db($currentUserID);
                if ($delete_player) {
                    $data = array (
                        'id_player' => $currentUserID,
                        'id_resort' => $id_resort,
                        'email' => $email,
                        'username' => $username,
                        'ip_address' => $this->input->ip_address()   
                    );
                    email_admin('player_delete_account', 'tracking_delete_account', $data);        // Sends an email to tracking email address for admin info.
                    $this->reset_password_model->confirm_reset_code_DB('game_delete_account_codes', $email, $delete_code, 'delete_code');
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('email')['account_delete_success'].'</div>');
                    $data['status'] = 'delete_successful';
                    // Regular logout
                    $this->session->sess_destroy();

                    $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
                    $this->output->set_header("Pragma: no-cache");
                }
                else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('email')['account_delete_failed'].'</div>');
                    $data['status'] = 'delete_unsuccessful';
                }
            }
            else {          // no match, wrong email password
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('login_form')['wrong_password'].'</div>');
            }
            $data['delete_code'] = $delete_code;
            $data['email'] = $email;
            $data['main_content'] = 'confirm_delete_account';
            $this->load->view('templates/default',$data);
        }
        else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('email')['invalid_request'].'</div>');
            redirect('home');
        }      
    }
    
    
}
