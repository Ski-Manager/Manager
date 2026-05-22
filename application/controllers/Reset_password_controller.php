<?php

class Reset_password_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('reset_password',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('signup_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('email',$siteLang);
        $this->config->set_item('language', $siteLang);
        $this->load->model('users_model');
        $this->load->model('contact_model');
        $this->load->model('reset_password_model');
    }
    
    public function index(){
        
        $data['captcha'] = $this->get_captcha();
        
        $data['title'] = $this->lang->line('reset_password')['title'];
        $data['introResetPass'] = $this->lang->line('reset_password')['intro'];
        $data['main_content'] = 'reset_password';
        $this->load->view('templates/default',$data); 
    }
    
    
    public function reset_request(){
        if (isset ($_POST['reset_request'])) {             // if we POST something from the signup form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('email_or_username', $this->lang->line('contact_form')['email_or_username_field'], 'trim|required|max_length[45]|callback_valid_email_or_username');
         

            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $this->session->set_flashdata('msg', form_error('email_or_username'));            // redirect to resort contoller with error message
                redirect('reset_password_controller');
            }
            else {                  // all fields are correct
                // Checking Captcha
                // First, delete old captchas
                $expiration = time() - 7200; // Two hour limit
                // Delete captchas older than 2 hours in DB
                $delete_old_captcha_DB = $this->contact_model->delete_old_captcha_DB($expiration);

                // Then see if a captcha exists
                $retrieveCaptchaDB = $this->contact_model->retrieve_captcha_DB($_POST['captcha'], $this->input->ip_address(), $expiration);
                $row = $retrieveCaptchaDB->num_rows();

                if ($row == 0){  // No matching captcha
                    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('contact_form')['captcha_missing'].'</div>');
                    redirect('reset_password_controller');
                }
                else {  // Captcha OK. Ready to send email
                    // If country is set to the default value, we set it to NULL
                    $posted_email_or_username = $this->input->post('email_or_username', TRUE);
                    if (filter_var($posted_email_or_username, FILTER_VALIDATE_EMAIL)) {     // checking email
                        $posted_field = 'email';
                        $column_to_check = 'username';
                        $message = '<div class="alert alert-success text-center">'.$this->lang->line('reset_password')['email_sent_email'].'</div>';
                    }
                    else {      // checking username
                        $posted_field = 'username';
                        $column_to_check = 'email';
                        $message = '<div class="alert alert-success text-center">'.$this->lang->line('reset_password')['email_sent_email'].'</div>';
                    }
                    
                    $query = $this->reset_password_model->get_username_or_email_reset_password($posted_email_or_username, $posted_field, $column_to_check);       //get email or username       
                    if ($query != false) {
                       $query_data = $query->row();
                       
                       $username = $query_data->username;
                       $email = $query_data->email;
                       $reset_code = md5((string)time());   // Creates the reset code
                       $ip_address = $this->input->ip_address();
                       
                       $reset_data = array (
                           'username' => $username,
                           'email' => $email,
                           'reset_code' => $reset_code,
                           'ip_address' => $ip_address,
                       );
                       $query = $this->reset_password_model->insert_reset_code_DB('game_reset_password_codes', $reset_data);       // insert code in DB
                       $send_email = $this->send_email_reset($username, $email, $reset_code);
                       if ($send_email) {
                            $this->session->set_flashdata('msg', $message);            // redirect to resort contoller with error message
                            redirect('reset_password_controller');
                       }
                       else {
                            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('contact_form')['email_failed']).'</div>';            // redirect to resort contoller with error message
                            redirect('reset_password_controller');
                       }
                    }
                    else {
                        //$data['sent'] = 'sent';            // for displaying the right info message on the page
                        //$data['main_content'] = 'reset_password';
                        $this->session->set_flashdata('msg', $message);            // redirect to resort contoller with error message
                        redirect('reset_password_controller');
                    }
                }
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['main_content'] = 'reset_password';
            //$this->load->view('templates/default',$data);
        }
    }
    
    public function send_email_reset($username, $email, $reset_code) {

        $button_url = base_url() . 'reset_password_controller/reset_action/' . $email . '/' . $reset_code;

        // Build styled HTML email body
        $message = build_html_email(
            $username,
            $this->lang->line('reset_password')['reset_heading'],
            $this->lang->line('reset_password')['reset_body'],
            $button_url,
            $this->lang->line('reset_password')['reset_cta'],
            $this->lang->line('reset_password')['reset_note']
        );

        // Send via Brevo REST API so the email appears in Brevo's transactional logs
        return send_brevo_email($email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('reset_password')['reset_subject'], $message);
    }
    
    
    public function get_captcha() {
        $capache_config = array(
            'img_path'      => './img/captcha_folder/',
            'img_url'       => base_url('img/captcha_folder/'),
            'img_width' => 210,
            'img_height' => 45,
            'font_size' => 20,
            'font_path' => FCPATH. '/fonts/arial.ttf',
            'expiration' => 7200,
        );
        // Common captcha area if failing or passing
        $captcha_array = array();
        $captcha_array['label'] = $this->lang->line('contact_form')['captcha_label'].'<br>';
        $captcha_array['refresh'] = ' <i class="fa-solid fa-arrows-rotate reload-captcha activate_button"></i>';
        $captcha_array['input'] = ' <input type="text" name="captcha" value="" size="12"/>';
        
        /* Generate the captcha */
        $captcha = create_captcha();
        if ($captcha !== FALSE) {
            // Data to insert in the captcha table in the DB
            $data = array(
                    'captcha_time'  => $captcha['time'],
                    'ip_address'    => $this->input->ip_address(),
                    'word'          => $captcha['word']
            );
            // Insert the data in DB
            $insertCaptchaDB = $this->contact_model->insert_captcha_DB($data);
            // Building catptcha area
            
            $captcha_array['img'] = '<span class="captcha-img">'.$captcha['image'].'</span>';
        }
        else {    // Something went wrong, returning error message
            $captcha_array['img'] = $this->lang->line('contact_form')['captcha_not_created'];
        }
        
        if (isset($_POST['origin']) && $_POST['origin'] == 'javascript') {    // Called from reload button, need to format in JSON
                echo json_encode(array('img' => $captcha_array['img']));
        }
        else { // Not calles via Javascript, return result to PHP function (normal page - not Reload button)
            return $captcha_array;
        }
    }
    
    
    public function valid_email_or_username($input_value){ 
            // If the email is valid or if looks like username, return true
        if (filter_var($input_value, FILTER_VALIDATE_EMAIL)  || preg_match("/^([a-z0-9\-\s\_À-ÿ])+$/i", $input_value)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
   
    public function reset_action ($email, $reset_code) {
        $reset_code = trim($reset_code);
        $email = trim($email);
        $get_data_reset = $this->reset_password_model->validate_email_reset_password('game_reset_password_codes', $email, $reset_code, 'reset_code');    
        if ($get_data_reset != false) {
            $data_reset = $get_data_reset->row();
            $timestamp = strtotime($data_reset->timestamp);
            $expiration = $timestamp + 86400;
            $now = time();
            if ($now < $expiration) {   // within time. OK to reset
                $this->session->set_userdata('email_url', $email);
                $data['main_content'] = 'choose_password';
                $data['reset_code'] = $reset_code;
                $this->load->view('templates/default',$data); 
            }
            else {
                // Code expired
                echo 'code expired';
            }
        }
        else {
            // email not matching code
            echo 'email not matching code';
        } 
    }
    
    public function choose_password () {
        if (isset ($_POST['choose_password'])) {             // if we POST something from the choose_password form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('password', $this->lang->line('signup')['new_password'], 'trim|required|min_length[4]|max_length[25]');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('signup')['new_password_confirm'], 'trim|required|matches[password]');
         

            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $data['signup_error_password'] = form_error('password');
                $data['signup_error_password_confirm'] = form_error('password_confirm');
                $data['reset_code'] = md5($this->input->post('reset_code', TRUE));
                $data['main_content'] = 'choose_password';
                $this->load->view('templates/default',$data);
            }
            else {                  // all fields are correct
                $password = md5($this->input->post('password', TRUE));
                $reset_code = md5($this->input->post('reset_code', TRUE));
                $email = $this->session->userdata('email_url');
                $update_password = $this->reset_password_model->update_password($email, $password);       // update password for provided email
                if ($update_password === true) {
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('reset_password')['password_updated'].'</div>');
                    $currentUserID = $this->users_model->get_user_id_from_email($email);
                    $id_resort = $this->users_model->get_resort_id($currentUserID);
                    $username = $this->users_model->get_username_from_id_player($currentUserID);
                    $data = array (
                        'id_player' => $currentUserID,
                        'id_resort' => $id_resort,
                        'email' => $email,
                        'username' => $username,
                        'ip_address' => $this->input->ip_address()   
                    );
                    email_admin('player_reset_password', 'tracking_reset_password', $data);        // Sends an email to tracking email address for admin info.
                    $data['reset_code'] = md5($this->input->post('reset_code', TRUE));
                    $data['main_content'] = 'choose_password';
                    $this->load->view('templates/default',$data);
                }
                  
               
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['reset_code'] = md5($this->input->post('reset_code', TRUE));
            $data['main_content'] = 'choose_password';
            $this->load->view('templates/default',$data);
        }
    }
    
}

?>