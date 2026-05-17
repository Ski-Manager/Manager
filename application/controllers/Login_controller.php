<?php

class Login_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('signup_form',$siteLang);
        $ci->lang->load('email_validation',$siteLang);
        $ci->lang->load('email',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $this->config->set_item('language', $siteLang);
        $this->load->model('users_model');
        $this->load->model('users_facebook_model');
        $this->load->model('logs_model');
        $this->load->model('daily_bonus_model');
    }
    
    public function index(){
        $data['main_content'] = 'loginForm';
        $this->load->view('templates/default', $data);
    }
    
    /**
     * checkLogin // Checks if login info is correct (general function)
     */
    public function checkLogin () {   
        if (isset ($_POST['signin'])) {             // If login form was submitted
            $login_user = $this->input->post('login_username', TRUE);
           // $this->load->library('form_validation');
            $this->form_validation->set_rules('login_username', $this->lang->line('home')['username'], 'trim|required|min_length[3]|max_length[25]');
            $this->form_validation->set_rules('login_password', $this->lang->line('home')['password'], 'trim|required|min_length[4]|max_length[25]|callback_verifyUser');
            if($this->form_validation->run() === false){     // at least one field didn't pass the validation (wrong match or too short)
                $data['login_error_password'] = '<div class="mini-alert alert-danger text-center">'.$this->lang->line('login_form')['login_error_password'].'</div>';
                $this->session->set_flashdata('error', '<div class="mini-alert alert-danger text-center">'.$this->lang->line('login_form')['login_error_password'].'</div>');            // redirect to resort contoller with error message
                redirect('home_controller');
            }
            else {              // the form was validated / username password matched
                $login_id_resort = $this->users_model->get_resort_id($login_user);
                $is_admin = $this->users_model->check_if_admin($this->input->post('login_username', TRUE));
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
                $this->session->set_userdata($data);            // We set the session
                $proceed_login = true;
            }        
        }

        
        if (isset($proceed_login) && $proceed_login === true) {
            $last_connection_player = $this->users_model->last_connection_player($login_user);
            $disable_vacation_mode = $this->users_model->disable_vacation_mode($login_user);
            // Store difficulty mode in session
            $logged_user_id = $this->users_model->get_user_id($this->session->userdata('login_username'));
            $this->session->set_userdata('difficulty_mode', $this->users_model->get_difficulty_mode($logged_user_id));
            if ($disable_vacation_mode == 1) {
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('login_form')['vacation_mode_disable'].'</div>');            // redirect to resort contoller with error message
                $id_player = $this->users_model->get_user_id($login_user);
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
                $this->lang->load('logs',$player_preferred_lang);
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['disabled_vacation_mode']) );   // Add a log row to the game_player_logs table  
                $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['disabled_vacation_mode']) );   // Add a log row to the game_player_logs table  
            }
            if($is_admin_session === 1)   // he is admin (1)
                redirect('admin/Admin_player_controller');
            else {                                      // he is not admin (0)
                $id_player = $this->users_model->get_user_id($login_user);
                redirect('resort_controller');
            }
        }
        //$data['authUrl'] =  $this->facebook->login_url();
        //$data['language'] = $this->session->userdata('site_lang');
        //$data['main_content'] = 'home';
        //$this->load->view('templates/default',$data); 
    }

    /**
     * googleCallback  Verifies a Google ID token posted from the Sign-In button
     * and logs the user in (or redirects to account finalisation).
     */
    public function googleCallback() {
        $id_token = $this->input->post('id_token', TRUE);
        if (empty($id_token)) {
            redirect('home_controller');
            return;
        }

        // Verify the token using Google's tokeninfo endpoint (POST to avoid token in server logs)
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
            $this->session->set_flashdata('error', '<div class="mini-alert alert-danger text-center">Google Sign-In failed. Please try again.</div>');
            redirect('home_controller');
            return;
        }

        $token_data = json_decode($raw, true);

        // Validate JSON, required claims, audience, and token expiry
        $expected_client_id = $this->config->item('google_client_id');
        if (!is_array($token_data) || json_last_error() !== JSON_ERROR_NONE
            || empty($token_data['sub']) || empty($token_data['email'])
            || !isset($token_data['aud']) || $token_data['aud'] !== $expected_client_id
            || empty($token_data['exp']) || (int) $token_data['exp'] <= time()) {
            $this->session->set_flashdata('error', '<div class="mini-alert alert-danger text-center">Google Sign-In failed. Please try again.</div>');
            redirect('home_controller');
            return;
        }

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

        // Check if there is an existing regular account with the same email
        $existing_regular_login = $this->users_model->get_user_id_from_email($userData['email']);
        if (!$existing_regular_login) {
            // No matching account – ask the user to finalise / create one
            $this->session->set_userdata('userData', $userData);
            redirect('register_controller/finalize_account');
            return;
        }

        // Link the Google account to the existing regular account
        $this->users_model->update_link_auth($existing_regular_login, $userData['email'], $userData['oauth_uid']);

        $resort_id   = $this->users_model->get_resort_id($existing_regular_login);
        $username    = $this->users_model->get_username_from_id_player($existing_regular_login);
        $is_admin    = $this->users_model->check_if_admin($username) ? 1 : 0;
        $id_player   = $this->users_model->get_user_id($username);
        $player_lang = $this->users_model->get_user_preferred_lang($id_player);

        $this->session->set_userdata('userData', $userData);
        $this->session->set_userdata([
            'login_username'  => $username,
            'login_id_resort' => $resort_id,
            'is_logged_in'    => true,
            'site_lang'       => $player_lang,
            'is_admin'        => $is_admin,
        ]);

        $this->users_model->last_connection_player($username);
        $this->users_model->disable_vacation_mode($username);
        // Store difficulty mode in session
        $logged_user_id = $this->users_model->get_user_id($this->session->userdata('login_username'));
        $this->session->set_userdata('difficulty_mode', $this->users_model->get_difficulty_mode($logged_user_id));

        if ($is_admin === 1) {
            redirect('admin/Admin_player_controller');
        } else {
            redirect('resort_controller');
        }
    }

    /**
     * verifyUser Checks if the username and password match
     * @return string|boolean
     */
    public function verifyUser(){  
        $name = $this->input->post('login_username', TRUE);
        $pass = $this->input->post('login_password', TRUE);
        $is_admin = $this->users_model->validate_username_password($name, $pass);
        if ($is_admin) {                    // If there is a match (good username/password)
            return true;
        }
        else {          // no match, wrong username password
            return false;
        }
    }
    
    /**
     * logout To log the user out, we destroy the session, clear the cache (just in case) and redirect to homepage
     */
    function logout() {
        // Regular logout
        $this->session->sess_destroy();
        
        $this->clear_cache();
        redirect('/home_controller');
    }
    
    /**
     * clear_cache Clear the cache. This is maybe no required...?
     */
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }

    /**
     * _apply_daily_bonus  Claims the daily login bonus for a player (if not yet claimed today)
     *                     and stores the result as a flash notification.
     *
     * @deprecated  Bonus is now claimed manually via Daily_bonus_controller::claim().
     */
    private function _apply_daily_bonus($id_player) {
        $bonus_result = $this->daily_bonus_model->check_and_claim((int)$id_player);
        if (!$bonus_result['claimed']) {
            return;
        }
        $log_data = $this->lang->line('logs')['daily_bonus_claimed'] . $bonus_result['cash'] . ' €'
            . $this->lang->line('logs')['daily_bonus_streak'] . $bonus_result['streak'] . $this->lang->line('logs')['daily_bonus_streak_end'];
        $this->logs_model->call_notification_DB(array('id_player' => $id_player, 'type' => $this->lang->line('logs')['daily_bonus'], 'data' => $log_data));
        log_user_action(array('id_player' => $id_player, 'type' => $this->lang->line('logs')['daily_bonus'], 'data' => $log_data));
        $this->session->set_flashdata('daily_bonus_msg',
            '<div class="alert alert-success text-center"><i class="fa-solid fa-fire me-1"></i>'
            . $this->lang->line('logs')['daily_bonus_claimed'] . '<strong>' . number_format($bonus_result['cash']) . ' €</strong>'
            . $this->lang->line('logs')['daily_bonus_streak'] . $bonus_result['streak'] . $this->lang->line('logs')['daily_bonus_streak_end']
            . '</div>'
        );
    }
   
    
}

?>
