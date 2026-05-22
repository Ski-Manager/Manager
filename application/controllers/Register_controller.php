<?php

// Brevo (formerly Sendinblue) API library
require_once (APPPATH . 'libraries/vendor/autoload.php');

class Register_controller extends CI_Controller{
    
    private $siteLang;                  // Sometimes used in other functions (Global)
    
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');        // Store current language in variable
        } else {
            $siteLang = 'english';                                  // If no session, use English
            $this->session->set_userdata('site_lang', $siteLang);
        }
        // Loads the different language files
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('signup_form',$siteLang);
        $ci->lang->load('email_validation',$siteLang);
        $ci->lang->load('email',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $this->config->set_item('language', $siteLang);             // Set config file to english. Why???
        $this->load->model('users_model'); 
        $this->load->model('logs_model');
        $this->load->model('achievements_model');
    }
    
    /**
     * index Main signup controller, displays the signup View
     */
    public function index(){
        $data['language'] = $this->session->userdata('site_lang');
        
        
        // If email was sent
        if (isset($_SESSION['activation_email_resent'])) {
            unset($_SESSION['not_activated']);
            $data['account_created'] = true;
            $data['not_activated'] = true;
            $data['resend_button_text']  = $this->lang->line('signup')['account_created'];
        }
        else if (isset($_SESSION['not_activated'])) {   // Or if just landing on this page
            $data['account_created'] = true;
            $data['not_activated'] = true;
            $currentUserID = $this->users_model->get_user_id();
            $user_info = $this->users_model->get_player_info($currentUserID);
            if ($user_info->num_rows() > 0) {
                $user_info_data = $user_info->row();
                $email = $user_info_data->email;
                $registration_time = $user_info_data->registration_time;
                $email_code = md5((string)$registration_time);   // Creates the email code
                $data['resend_button_text']  = $this->lang->line('signup')['account_not_activated'];
                $data['resend_button_text']  .= '<a href="' . base_url() . 'register_controller/send_validation_email/resend/' . $email . '/'. $email_code . '">'.$this->lang->line('signup')['click_here'].'</a>';
                $data['resend_button_text']  .= $this->lang->line('signup')['to_send_email'].' '.$email;
            }
            else
                redirect('home_controller');
        }
        $posted_referral_value  = $this->input->get('key', TRUE);
        $posted_referral = $this->users_model->get_username_from_referral_link($posted_referral_value);
        $data['posted_referral']  = $posted_referral;
        
        
        $data['main_content'] = 'signupForm';
        $this->load->view('templates/default',$data);
        
        
        
    }
    
    /**
     * prepare_user_creation    Runs validation of the form for new user account.
     *                          If validation passes, the user is added to the database and an email is sent.
     * 
     */
    public function prepare_user_creation(){
        if (isset ($_POST['signup'])) {             // if we POST something from the signup form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<p class="text-error text-sm mt-1">', '</p>');
            $this->form_validation->set_rules('username', $this->lang->line('home')['username'], 'trim|required|min_length[3]|max_length[25]|callback_username_available|callback_different_from_input[username]|callback_alpha_dash_space');
            $this->form_validation->set_rules('email', $this->lang->line('home')['email'], 'trim|required|max_length[45]|valid_email|callback_validate_email_domain|callback_email_available');
            $this->form_validation->set_rules('password', $this->lang->line('home')['password'], 'trim|required|min_length[4]|max_length[25]');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('signup')['password_confirm'], 'trim|required|matches[password]');
            $this->form_validation->set_rules('country', $this->lang->line('home')['country_field'], 'trim|max_length[45]');
            $this->form_validation->set_rules('age', $this->lang->line('signup')['age'], 'trim|max_length[3]');
            $this->form_validation->set_rules('signup_referral', $this->lang->line('signup')['referral']);
            

            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $data['signup_error_username'] = form_error('username');
                $data['signup_error_email'] = form_error('email');
                $data['signup_error_password'] = form_error('password');
                $data['signup_error_password_confirm'] = form_error('password_confirm');
                $data['signup_error_country'] = form_error('country');
                $data['signup_error_age'] = form_error('age');
                $data['signup_error_signup_referral'] = form_error('signup_referral');
                $data['language'] = $this->session->userdata('site_lang');
                $data['main_content'] = 'signupForm';
                $this->load->view('templates/default',$data);
            }
            else {                  // all fields are correct
            
                // If country is empty or set to the default placeholder, store NULL
                $country_raw = $this->input->post('country', TRUE);
                if (empty($country_raw) || $country_raw == $this->lang->line('home')['country_field']){
                    $country = NULL;
                }
                else {
                    $country = $country_raw;
                }

                // If age is set to the default value, we set it to NULL
                if ($this->input->post('age', TRUE) == $this->lang->line('signup')['age']){
                    $age = NULL;
                }
                else {  // get the value from the form
                    $age = $this->input->post('age', TRUE);
                }
                
                $query = $this->users_model->test_if_beta_tester($this->input->post('email', TRUE));       //check if the email address was registered during beta
                if ($query) {
                    $genepis_to_grant = GENEPIS * 3;
                } 
                else {
                    $genepis_to_grant = GENEPIS;

                }

                $new_username_insert_data = array (
                    'username' => $this->input->post('username', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'password' => md5($this->input->post('password', TRUE)),
                    'country' => $country,
                    'age' => $age,
                    'genepis' => $genepis_to_grant,
                    'preferred_lang' => $this->session->userdata('site_lang') ?: 'english'
                );
                
        
                
                $query = $this->users_model->create_username($new_username_insert_data);       //creation of username passed
                if ($query) {   // If the query succedded
                    
                    
                // Register contact in Brevo newsletter list
                try {
                    $this->config->load('brevo');
                    $brevoConfig = SendinBlue\Client\Configuration::getDefaultConfiguration()
                        ->setApiKey('api-key', $this->config->item('brevo_api_key'));
                    $apiInstance = new SendinBlue\Client\Api\ContactsApi(new GuzzleHttp\Client(), $brevoConfig);

                    $createContact = new \SendinBlue\Client\Model\CreateContact();
                    $createContact['email'] = $new_username_insert_data['email'];
                    $createContact['updateEnabled'] = true;
                    $createContact['attributes'] = array('username' => $new_username_insert_data['username'], 'origin' => 'regular');
                    $createContact['listIds'] = array($this->config->item('brevo_newsletter_list_id'));

                    $apiInstance->createContact($createContact);
                } catch (Exception $e) {
                    // Newsletter subscription failure is non-fatal; continue with registration
                }

                    
                    set_session($new_username_insert_data['username'],$new_username_insert_data['email']);   // We set the current session with username and email address
                    $this->send_validation_email();             // We send the email validation
                    $currentUserID = $this->users_model->get_user_id($new_username_insert_data['username']);
                    $ip = $this->input->ip_address();
                    $data_tracking = array (
                        'username' => $this->input->post('username', TRUE),
                        'email' => $this->input->post('email', TRUE),
                        'country' => $country,
                        'age' => $age,
                        'ip' => $ip,
                        'signup_referral' => $this->input->post('signup_referral', TRUE),
                        'id_player' => $currentUserID,
                        'account_type' => $this->lang->line('email')['tracking_account_type_regular'],
                        'datetime' => gmdate('Y-m-d H:i:s')
                    );
                    $this->send_tracking_email($data_tracking);             // We send the email validation

                    $query_link_auth = $this->users_model->create_link_auth( array('regular_login_id' => $currentUserID, 'email' => $this->input->post('email', TRUE), 'oauth_login_id' => '') );
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_created']) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_created']) );   // Add a log row to the game_player_logs table
                    $referral_key = $this->makePassword();
                    $referral_link_data = array (
                        'id_player' => $currentUserID,
                        'referral_key' => $referral_key
                    );
                    $create_referral_link = $this->users_model->create_referral_link_DB($referral_link_data);
                    if ($this->input->post('signup_referral', TRUE) != '') {
                        $referralUserID = $this->users_model->get_user_id($this->input->post('signup_referral', TRUE));
                        if ($referralUserID){
                            $referral_confirmed_data = array (
                                'id_referral_player' => $referralUserID,
                                'id_referred_player' => $currentUserID,
                                'referred_date' => gmdate('Y-m-d H:i:s')
                            );
                            $create_referral_confirmed = $this->users_model->create_referral_confirmed_DB($referral_confirmed_data);
                            if ($create_referral_confirmed) {
                                $data['referral_confirmed'] = true; 
                            }
                        }
                    }
                    $data['account_created'] = 'ok';            // for displaying the right info message on the page
                    $data['main_content'] = 'signupForm';
                    $this->load->view('templates/default',$data);
                }
                else {                        //creation of username failed
                  $data['main_content'] = 'signupForm';
                  $this->load->view('templates/default',$data);
                }
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['language'] = $this->session->userdata('site_lang');
            $data['main_content'] = 'signupForm';
            $this->load->view('templates/default',$data);
        }
    }
   
    public function finalize_account(){
        if (isset ($_POST['submit_finalize_account'])) {             // if we POST something from the signup form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('username', $this->lang->line('home')['username'], 'trim|required|min_length[3]|max_length[25]|callback_username_available|callback_different_from_input[username]|callback_alpha_dash_space');
            $this->form_validation->set_rules('signup_referral', $this->lang->line('signup')['referral']);
            

            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $data['signup_error_username'] = form_error('username');
                $data['signup_error_signup_referral'] = form_error('signup_referral');
                $data['facebook_finalize'] = true;
                $data['facebook_email'] = $this->input->post('facebook_email', TRUE);
                if (empty($data['facebook_email'])) {
                    $userData = $this->session->userdata('userData');
                    $data['facebook_email'] = is_array($userData) ? ($userData['email'] ?? '') : '';
                }
                $data['main_content'] = 'signupForm';
                $this->load->view('templates/default',$data);
            }
            else {                  // all fields are correct
                
                $query = $this->users_model->test_if_beta_tester($this->input->post('facebook_email', TRUE));       //check if the email address was registered during beta
                if ($query) {
                    $genepis_to_grant = GENEPIS * 3;
                } 
                else {
                    $genepis_to_grant = GENEPIS;

                }
                
                
                $facebook_email = $this->input->post('facebook_email', TRUE);
                $new_username_insert_data = array (
                    'username' => $this->input->post('username', TRUE),
                    'email' => $facebook_email,
                    'genepis' => $genepis_to_grant,
                    'activated' => '1',
                    'preferred_lang' => $this->session->userdata('site_lang') ?: 'english'
                );
                $query = $this->users_model->create_username($new_username_insert_data);       //creation of username passed
                if ($query) {   // If the query succedded
       
                // Register contact in Brevo newsletter list
                try {
                    $this->config->load('brevo');
                    $brevoConfig = SendinBlue\Client\Configuration::getDefaultConfiguration()
                        ->setApiKey('api-key', $this->config->item('brevo_api_key'));
                    $apiInstance = new SendinBlue\Client\Api\ContactsApi(new GuzzleHttp\Client(), $brevoConfig);

                    $createContact = new \SendinBlue\Client\Model\CreateContact();
                    $createContact['email'] = $new_username_insert_data['email'];
                    $createContact['updateEnabled'] = true;
                    $createContact['attributes'] = array('username' => $new_username_insert_data['username'], 'origin' => 'facebook');
                    $createContact['listIds'] = array($this->config->item('brevo_newsletter_list_id'));

                    $apiInstance->createContact($createContact);
                } catch (Exception $e) {
                    // Newsletter subscription failure is non-fatal; continue with registration
                }
                
                    //set_session($new_username_insert_data['username'],$new_username_insert_data['email']);   // We set the current session with username and email address
                    //$this->send_validation_email();             // We send the email validation
                    $currentUserID = $this->users_model->get_user_id($new_username_insert_data['username']);
                    $ip = $this->input->ip_address();
                    $data_tracking = array (
                        'username' => $this->input->post('username', TRUE),
                        'email' => $this->input->post('facebook_email', TRUE),
                        'country' => '-',
                        'age' => '-',
                        'ip' => $ip,
                        'signup_referral' => $this->input->post('signup_referral', TRUE),
                        'id_player' => $currentUserID,
                        'account_type' => $this->lang->line('email')['tracking_account_type_facebook'],
                        'datetime' => gmdate('Y-m-d H:i:s')
                    );
                    $this->send_tracking_email($data_tracking);             // We send the email validation
                    $data_achievement = array (
                        'type' => 'activated',     
                        'email' => $this->input->post('facebook_email', TRUE)     
                    );
                    $call_achievements_check = call_achievements_check($data_achievement, 'account');   // Check if a corresponding achievement should be updated
                    $oauth_uid = $this->users_model->get_oauth_uid_from_email($facebook_email);
                    $query_link_auth = $this->users_model->create_link_auth( array('regular_login_id' => $currentUserID, 'email' => $facebook_email, 'oauth_login_id' => $oauth_uid) );
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_finalized']) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_finalized']) );   // Add a log row to the game_player_logs table
                    $referral_key = $this->makePassword();
                    $referral_link_data = array (
                        'id_player' => $currentUserID,
                        'referral_key' => $referral_key
                    );
                    $create_referral_link = $this->users_model->create_referral_link_DB($referral_link_data);
                    if ($this->input->post('signup_referral', TRUE) != '') {
                        $referralUserID = $this->users_model->get_user_id($this->input->post('signup_referral', TRUE));
                        if ($referralUserID){
                            $referral_confirmed_data = array (
                                'id_referral_player' => $referralUserID,
                                'id_referred_player' => $currentUserID,
                                'referred_date' => gmdate('Y-m-d H:i:s')
                            );
                            $create_referral_confirmed = $this->users_model->create_referral_confirmed_DB($referral_confirmed_data);
                            if ($create_referral_confirmed) {
                                $data['referral_confirmed'] = true; 
                            }
                        }
                    }
                    $data['account_finalized'] = 'ok';            // for displaying the right info message on the page

                    // Log the user in automatically after finalization
                    $resort_id   = $this->users_model->get_resort_id($currentUserID);
                    $is_admin    = $this->users_model->check_if_admin($new_username_insert_data['username']) ? 1 : 0;
                    $player_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                    $userData    = $this->session->userdata('userData');
                    $this->session->set_userdata([
                        'login_username'  => $new_username_insert_data['username'],
                        'login_id_resort' => $resort_id,
                        'is_logged_in'    => true,
                        'site_lang'       => $player_lang ?: ($this->session->userdata('site_lang') ?: 'english'),
                        'is_admin'        => $is_admin,
                        'userData'        => $userData,
                    ]);
                    $this->users_model->last_connection_player($new_username_insert_data['username']);

                    redirect('resort_controller');
                    return;
                }
                else {                        //creation of username failed
                  $this->session->set_flashdata('error', '<div class="mini-alert alert-danger text-center">Account creation failed. Please try again.</div>');
                  $data['facebook_finalize'] = true;
                  $data['facebook_email'] = $facebook_email;
                  $data['main_content'] = 'signupForm';
                  $this->load->view('templates/default',$data);
                }
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $userData = $this->session->userdata('userData');
            if (!is_array($userData) || empty($userData['email'])) {
                redirect('home_controller');
                return;
            }
            $data['facebook_finalize'] = true;
            $data['facebook_email'] = $userData['email'];
            $data['main_content'] = 'signupForm';
            $this->load->view('templates/default',$data);
        }
    }
   
    public function merge_account(){
        if (isset ($_POST['submit_merge_account'])) {             // if we POST something from the signup form. To avoid confusion with other forms (loginForm)
            // validation rules
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="mini-alert alert-danger text-center">', '</div>');
            $this->form_validation->set_rules('password', $this->lang->line('home')['password'], 'trim|required|min_length[4]|max_length[25]|callback_verifyPassword');
            

            if ($this->form_validation->run() == FALSE){        // didn't validate (at least one field is incorrect)
                $data['signup_error_password'] = form_error('password').'<br>';
                $data['facebook_merge'] = true;
                $data['facebook_email'] = $this->input->post('facebook_email', TRUE);
                $data['main_content'] = 'signupForm';
                $this->load->view('templates/default',$data);
            }
            else {                  // all fields are correct
                $facebook_email = $this->input->post('facebook_email', TRUE);
                $currentUserID = $this->users_model->get_user_id_from_email($facebook_email);
                $oauth_uid = $this->users_model->get_oauth_uid_from_email($facebook_email);

                $query_link_auth = $this->users_model->update_link_auth($currentUserID, $facebook_email, $oauth_uid );
                if ($query_link_auth == '1') {
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_merged']) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_merged']) );   // Add a log row to the game_player_logs table

                    $data['account_merged'] = 'ok';            // for displaying the right info message on the page
                    $data['main_content'] = 'signupForm';
                    $this->load->view('templates/default',$data);
                }
                else {
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_merged_failed']) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_merged_failed']) );   // Add a log row to the game_player_logs table 
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('logs')['account_merged_failed'].'</div>');
                    $data['main_content'] = 'signupForm';
                    $this->load->view('templates/default',$data);
                }
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $data['main_content'] = 'signupForm';
            $this->load->view('templates/default',$data);
        }
    }
   
    public function verifyPassword(){  
        $email = $this->input->post('facebook_email', TRUE);
        $password = $this->input->post('password', TRUE);
        $is_valid = $this->users_model->validate_email_password($email, $password);
        if ($is_valid) {                    // If there is a match (good email/password)
            return true;
        }
        else {          // no match, wrong username password
            return false;
        }
    }
    
    public function makePassword($length = 10){
        $password = "";
        $possible = "0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }
    
    /**
     * send_validation_email Send the validation email to the new user
     * 
     */
    public function send_validation_email($mode = '', $email = '', $email_code = '') {

      // If during inscription (no Mode set), we get the values from post and session. Otherwise, from the URL
      if ($mode != 'resend') {
        $email = $this->session->userdata('email');   // Get current email from Session
        $email_code = $this->email_code;              // From the session (set_session function) : based on registration time
      }
      
      $username    = $this->session->userdata('username');
      $button_url  = base_url() . 'register_controller/validate_email/' . $email . '/' . $email_code;

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
      $sent = send_brevo_email($email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('email')['activation_subject'], $message);
      if ( ! $sent) {
        log_message('error', 'Validation email could not be sent to: ' . $email);
      }

      // Only when re-sending the email, the page needs to be reloaded
      if (isset($mode) && $mode == 'resend') {
        $this->session->set_userdata('activation_email_resent', true);
        redirect('register_controller');
      }
    }
    /**
     * send_tracking_email Send a tracking email to the admin after account creation
     * 
     */
    public function send_tracking_email($data_tracking) {

      // Build plain-text HTML body (admin-facing, no CTA button needed)
      $body  = $this->lang->line('email')['tracking_account_created_text'] . '<br><br>';
      $body .= $this->lang->line('home')['username']       . ': ' . htmlspecialchars($data_tracking['username'],         ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('home')['email']          . ': ' . htmlspecialchars($data_tracking['email'],            ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('home')['country_field']  . ': ' . htmlspecialchars($data_tracking['country'],          ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('signup')['age']          . ': ' . htmlspecialchars($data_tracking['age'],              ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('signup')['referral']     . ': ' . htmlspecialchars($data_tracking['signup_referral'],  ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('home')['IDplayer']       . ': ' . htmlspecialchars($data_tracking['id_player'],        ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('home')['ip_address']     . ': ' . htmlspecialchars($data_tracking['ip'],               ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('email')['tracking_creation_time'] . ': ' . htmlspecialchars($data_tracking['datetime'], ENT_QUOTES, 'UTF-8') . '<br>';
      $body .= $this->lang->line('email')['tracking_account_type']  . ': ' . htmlspecialchars($data_tracking['account_type'], ENT_QUOTES, 'UTF-8');

      $message = build_html_email(
        $data_tracking['username'],
        $this->lang->line('email')['tracking_account_created'],
        $body
      );

      // Send via Brevo REST API so the email appears in Brevo's transactional logs
      $sent = send_brevo_email(CONST_TRACKING_EMAIL, CONST_NOREPLY_EMAIL, 'Ski-Manager', $this->lang->line('email')['tracking_account_created'].' ('.$data_tracking['username'].')', $message);
      if ( ! $sent) {
        log_message('error', 'Tracking email could not be sent for user: ' . $data_tracking['username']);
      }
    }
    
    
    /**
     * username_available Custom CALLBACK function from signup form. Will simply check if the username is already registered
     * 
     * @param type $requested_username
     * @return boolean TRUE if available. FALSE if already registered
     */
    public function username_available($requested_username){ //custom callback function to check if username already exists
       
        $username_available = $this->users_model->check_username_available($requested_username);
        
        switch ($username_available) {      // Test returned result
            case true:{
                return true;
            }
            case false:{
                return false;
            }
            default:
                return false;
        }
    }
    
    /**
     * different_from_input Checks if the entered value is different from the default one (and rejects the form validation)
     * 
     * @param type $input_value         The value present in the field when validating the form
     * @param type $field_name          The field name
     * @return boolean                  FALSE if the name is the same (not valid). TRUE is name is different from default (valid)
     */
    public function different_from_input($input_value, $field_name){
        
        $field = $field_name.'_field';
        $field_desc = $field_name.'_field_error';
        
        if ($input_value == $this->lang->line($field) || $input_value == $this->lang->line($field_desc)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    /**
     * email_available Custom CALLBACK function from signup form. Will simply check if the email is already registered
     * 
     * @param type $requested_email
     * @return boolean TRUE if available. FALSE if already registered
     */
    public function email_available($requested_email){ //custom callback function to check if emails already used
        $this->load->model('users_model');        
        $email_not_in_use = $this->users_model->check_email_available($requested_email);
        if ($email_not_in_use) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
   
    
    /**
     * validate_email The function is called by clicking on the link in the activation email
     * The input parameters are posted from the email, in the URL
     * 
     * @param type $email_address
     * @param type $email_code code similar to 926b118ee922367775b23585f9646546
     */
    public function validate_email($email_address, $email_code){
        $email_code = trim($email_code);
        $validated = $this->users_model->validate_email_code($email_address, $email_code);       
        $currentUserID = $this->users_model->get_user_id_from_email($email_address);
        $data_achievement = array (
            'type' => 'activated',     
            'email' => $email_address     
        );
        $call_achievements_check = call_achievements_check($data_achievement, 'account');   // Check if a corresponding achievement should be updated
        if ($call_achievements_check != FALSE) {
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_activated']) );   // Add a log row to the game_player_logs table
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('navbar')['account'], 'data' => $this->lang->line('logs')['account_activated']) );   // Add a log row to the game_player_logs table
        }
        $data['main_content'] = 'email_validation';
        $data['class_message'] = 'mini-alert alert-danger text-center';
        
        if ($validated === true){                                                               // validation in DB is ok
            $data['class_message'] = 'text-success';
            $data['validation_status'] = $this->lang->line('validation_passed');
        } elseif ($validated === 'already_activated_error'){                                    // The activation field is already set to 1            
            $data['validation_status'] = $this->lang->line('validation_already_activated');     
        } elseif ($validated === 'unknown_error') {                                             // This should never happen
            $data['validation_status'] = $this->lang->line('validation_failed_db');
        } else {
            $data['validation_status'] = $this->lang->line('validation_failed');
        }
        $this->load->view('templates/default',$data);
        
    }
 
    
    /**
     * validate_email_domain Checks if the email domain has valid DNS mail records (MX or A).
     * Runs after valid_email, so the format is already guaranteed to be correct.
     *
     * @param string $email
     * @return boolean TRUE if domain resolves. FALSE if domain appears invalid
     */
    public function validate_email_domain($email) {
        $domain = substr($email, strpos($email, '@') + 1);
        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            $this->form_validation->set_message('validate_email_domain', $this->lang->line('validate_email_domain'));
            return FALSE;
        }
        return TRUE;
    }

    function alpha_dash_space($str)    {
        return ( ! preg_match("/^([a-z0-9\-\s\_À-ÿ])+$/i", $str)) ? FALSE : TRUE;
    }
}

?>