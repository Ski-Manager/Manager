<?php
/**
 * 
 */

// Brevo (formerly Sendinblue) API library
require_once (APPPATH . 'libraries/vendor/autoload.php');

class Account_controller extends CI_Controller{
    
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
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('reset_password_model');
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
        

        $data['account_activated'] = $this->users_model->check_account_activated($currentUserID);
        $data['difficulty_mode'] = $this->users_model->get_difficulty_mode($currentUserID);
        $data['user_has_subscribed_to_newsletter'] = false;
        $data['user_has_linked_google'] = $this->users_model->get_google_link_status($currentUserID);

        // Check Brevo newsletter subscription status
        try {
            $this->config->load('brevo');
            $brevoConfig = SendinBlue\Client\Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', $this->config->item('brevo_api_key'));
            $apiInstance = new SendinBlue\Client\Api\ContactsApi(new GuzzleHttp\Client(), $brevoConfig);

            $result = $apiInstance->getContactInfo($player_info_data->email);
            if (in_array($this->config->item('brevo_newsletter_list_id'), $result['listIds']) && $result['emailBlacklisted'] == false) {
                $data['user_has_subscribed_to_newsletter'] = true;
            }
        } catch (Exception $e) {
            $data['user_has_subscribed_to_newsletter'] = false;
        }
                
        
        
        
        // Displaying the account view
        $data['main_content'] = 'account';
        $this->load->view('templates/default',$data);  
    }

    public function update_account() {
        if (isset ($_POST['update_account'])) {             // if we POST something from the update_account form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('email', $this->lang->line('home')['email'], 'trim|required|max_length[45]|valid_email|callback_email_available_or_current');
            $this->form_validation->set_rules('password', $this->lang->line('signup')['new_password_confirm'], 'trim|min_length[4]|max_length[25]');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('home')['password'], 'trim|matches[password]');
            $this->form_validation->set_rules('country', $this->lang->line('home')['country_field'], 'trim|max_length[45]');
            $this->form_validation->set_rules('age', $this->lang->line('signup')['age'], 'trim|max_length[3]|integer');

            $currentUserID = $this->users_model->get_user_id();          // to be used in this file
            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $data['signup_error_email'] = form_error('email');
                $data['signup_error_password'] = form_error('password');
                $data['signup_error_password_confirm'] = form_error('password_confirm');
                $data['signup_error_country'] = form_error('country');
                $data['signup_error_age'] = form_error('age');
                
                $data['title'] = '<h2>'.$this->lang->line('signup')['account_info'].'</h2>';
                $data['intro_update_account'] = '<div>'.$this->lang->line('signup')['intro_update_account'].'</div>';
                $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view

                $player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
                $player_info_data = $player_info->row();
                $data['username'] = $player_info_data->username;
                $data['email'] = $player_info_data->email;
                $data['country'] = $player_info_data->country;
                $data['age'] = $player_info_data->age;
                $data['account_activated'] = $this->users_model->check_account_activated($currentUserID);
        
                $data['main_content'] = 'account';
                $this->load->view('templates/default',$data);
            }
            else {                  // all fields are correct
                // If country is set to the default value, we set it to NULL
                if ($this->input->post('country', TRUE) == $this->lang->line('home')['country_field']){
                    $country = NULL;
                }
                else {  // get the value from the form
                    $country = $this->input->post('country', TRUE);
                }

                // If age is set to the default value, we set it to NULL
                if ($this->input->post('age', TRUE) == $this->lang->line('signup')['age']){
                    $age = NULL;
                }
                else {  // get the value from the form
                    $age = $this->input->post('age', TRUE);
                }
                
                if (NULL != $this->input->post('password', TRUE)) {
                    $password = md5($this->input->post('password', TRUE));
                }
                else {
                    $password = NULL;
                }
                
                if (NULL != $this->input->post('email', TRUE)) {
                    $email = $this->input->post('email', TRUE);
                }
                else {
                    $email = NULL;
                }
        
                
                $query = $this->users_model->update_resort_DB($email, $password, $country, $age, $currentUserID);       //creation of username passed
                if ($query) {   // If the query succedded
                    //set_session($this->input->post('username', TRUE),$email);   // We set the current session with username and email address
                    redirect('account_controller/index/account_updated');
                }
                else {                        //creation of username failed
                  var_dump($_POST);
                  //redirect('account_controller/index/no_data_changed');
                }
            }
        }
        else {                    // if nothing POSTED, redirect to account page
            redirect('account_controller');
        }
    }
    
    
    public function email_available_or_current($requested_email){ //custom callback function to check if emails already used
        $this->load->model('users_model');        
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
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
    
    public function link_google_account() {
        $this->load->model('users_facebook_model');
        $id_token = $this->input->post('id_token', TRUE);
        if (empty($id_token)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Google link failed. Please try again.</div>');
            redirect('account_controller');
            return;
        }

        // Verify the token using Google's tokeninfo endpoint
        $ch = curl_init('https://oauth2.googleapis.com/tokeninfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['id_token' => $id_token]),
        ]);
        $raw = curl_exec($ch);
        $curl_err = curl_errno($ch);
        curl_close($ch);

        if ($curl_err || !$raw) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Google link failed. Please try again.</div>');
            redirect('account_controller');
            return;
        }

        $token_data = json_decode($raw, true);

        $expected_client_id = $this->config->item('google_client_id');
        if (!is_array($token_data) || json_last_error() !== JSON_ERROR_NONE
            || empty($token_data['sub']) || empty($token_data['email'])
            || !isset($token_data['aud']) || $token_data['aud'] !== $expected_client_id
            || empty($token_data['exp']) || (int) $token_data['exp'] <= time()) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Google link failed. Please try again.</div>');
            redirect('account_controller');
            return;
        }

        $currentUserID = $this->users_model->get_user_id();
        $player_info = $this->users_model->get_player_info($currentUserID);
        $player_info_data = $player_info->row();

        $userData = [
            'oauth_provider' => 'google',
            'oauth_uid'      => $token_data['sub'],
            'first_name'     => isset($token_data['given_name'])  ? $token_data['given_name']  : '',
            'last_name'      => isset($token_data['family_name']) ? $token_data['family_name'] : '',
            'email'          => $token_data['email'],
        ];

        // Insert or update the OAuth user record
        $oauth_user_info = $this->users_facebook_model->check_oauth_user($userData);
        if ($oauth_user_info->num_rows() > 0) {
            $oauth_user_result = $oauth_user_info->row_array();
            $this->users_facebook_model->update_oauth_user($userData, $oauth_user_result);
        } else {
            $this->users_facebook_model->insert_oauth_user($userData);
        }

        // Link the Google account to the existing regular account
        $this->users_model->update_link_auth($currentUserID, $player_info_data->email, $userData['oauth_uid']);

        $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('signup')['google_account_linked'] . '</div>');
        redirect('account_controller');
    }
    
    public function send_email_reset_account(){
        if (isset ($_POST['reset_account_submit'])) {             // if we POST something from the signup form. To avoid confusion with other forms (loginForm)
            $currentUserID = $this->users_model->get_user_id();          // to be used in this file
            $player_info = $this->users_model->get_player_info($currentUserID);       
            $player_info_data = $player_info->row();
            $email = $player_info_data->email;
            $username = $player_info_data->username;
            $reset_code = md5((string)time());   // Creates the reset code
            $ip_address = $this->input->ip_address();
            $reset_data = array (
                'email' => $email,
                'id_player' => $currentUserID,
                'reset_code' => $reset_code,
                'ip_address' => $ip_address,
            );
            $query = $this->reset_password_model->insert_reset_code_DB('game_reset_account_codes', $reset_data);       // insert code in DB
            $reset_code_and_mode = $reset_code.'/regular';
            $send_email = $this->email_account_reset($username, $email, $reset_code_and_mode);
            if ($send_email) {
                 $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('signup')['email_sent_reset_account'].'</div>');       
                 redirect('account_controller');
            }
            else {
                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('contact_form')['email_failed'].'</div>');            // redirect to resort contoller with error message
                 redirect('account_controller');
            }
        }
        else {                    // if nothing POSTED, redirect to account page
            redirect('account_controller');
        }
    }
    
    public function send_email_delete_account(){
        if (isset ($_POST['delete_account_submit'])) {             // if we POST something from the signup form. To avoid confusion with other forms (loginForm)
            $currentUserID = $this->users_model->get_user_id();          // to be used in this file
            $player_info = $this->users_model->get_player_info($currentUserID);       
            $player_info_data = $player_info->row();
            $email = $player_info_data->email;
            $username = $player_info_data->username;
            $delete_code = md5((string)time());   // Creates the delete code
            $ip_address = $this->input->ip_address();
            $delete_data = array (
                'email' => $email,
                'id_player' => $currentUserID,
                'delete_code' => $delete_code,
                'ip_address' => $ip_address,
            );
            $query = $this->reset_password_model->insert_reset_code_DB('game_delete_account_codes', $delete_data);       // insert code in DB
            $reset_code_and_mode = $delete_code.'/regular';
            $send_email = $this->email_account_delete($username, $email, $reset_code_and_mode);
            if ($send_email) {
                 $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('signup')['email_sent_delete_account'].'</div>');       
                 redirect('account_controller');
            }
            else {
                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('contact_form')['email_failed'].'</div>');            // redirect to resort contoller with error message
                 redirect('account_controller');
            }
        }
        else {                    // if nothing POSTED, redirect to account page
            redirect('account_controller');
        }
    }
    
    public function email_account_reset($username, $email = '', $reset_code = '') {

      $button_url = base_url() . 'reset_controller/page_confirm_reset_account/' . $email . '/' . $reset_code;

      // Build styled HTML email body
      $message = build_html_email(
        $username,
        $this->lang->line('email')['reset_account_heading'],
        $this->lang->line('email')['reset_account_body'],
        $button_url,
        $this->lang->line('email')['reset_account_cta'],
        $this->lang->line('email')['reset_account_note']
      );
      // Send via Brevo REST API so the email appears in Brevo's transactional logs
      return send_brevo_email($email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('email')['reset_account_subject'], $message);
    }
    
    public function email_account_delete($username, $email = '', $delete_code = '') {

      $button_url = base_url() . 'reset_controller/page_confirm_delete_account/' . $email . '/' . $delete_code;

      // Build styled HTML email body
      $message = build_html_email(
        $username,
        $this->lang->line('email')['delete_account_heading'],
        $this->lang->line('email')['delete_account_body'],
        $button_url,
        $this->lang->line('email')['delete_account_cta'],
        $this->lang->line('email')['delete_account_note']
      );
      // Send via Brevo REST API so the email appears in Brevo's transactional logs
      return send_brevo_email($email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('email')['delete_account_subject'], $message);
    }
    
    public function resend_verification_email() {
        if (isset($_POST['resend_verification_submit'])) {
            $currentUserID = $this->users_model->get_user_id();
            // Only send if account is not already activated
            if ($this->users_model->check_account_activated($currentUserID)) {
                $this->session->set_flashdata('msg', '<div class="alert alert-info text-center">' . $this->lang->line('signup')['account_already_activated'] . '</div>');
                redirect('account_controller');
                return;
            }
            $player_info      = $this->users_model->get_player_info($currentUserID);
            $player_info_data = $player_info->row();
            $email            = $player_info_data->email;
            $username         = $player_info_data->username;
            $email_code       = md5((string)$player_info_data->registration_time);
            $send_email       = $this->email_verification($username, $email, $email_code);
            if ($send_email) {
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('signup')['email_sent_verification'] . '</div>');
                redirect('account_controller');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('contact_form')['email_failed'] . '</div>');
                redirect('account_controller');
            }
        } else {
            redirect('account_controller');
        }
    }

    public function email_verification($username, $email = '', $email_code = '') {
        $button_url = base_url() . 'register_controller/validate_email/' . $email . '/' . $email_code;

        // Build styled HTML email body
        $message = build_html_email(
            $username,
            $this->lang->line('email')['activation_heading'],
            $this->lang->line('email')['activation_body'],
            $button_url,
            $this->lang->line('email')['activation_cta'],
            $this->lang->line('email')['activation_note']
        );
        // Send via Brevo REST API so the email appears in Brevo's transactional logs
        return send_brevo_email($email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('email')['activation_subject'], $message);
    }
    public function save_difficulty_mode() {
        if (!$this->session->userdata('is_logged_in')) {
            redirect('login_controller');
            return;
        }
        $mode = ($this->input->post('difficulty_mode') === 'easy') ? 1 : 0;
        $user_id = $this->users_model->get_user_id();
        $this->users_model->set_difficulty_mode($user_id, $mode);
        $this->session->set_userdata('difficulty_mode', $mode);
        redirect('account_controller');
    }


}