<?php /*defined('BASEPATH') OR exit('No direct script access allowed');
class User_Authentication extends CI_Controller
{
    function __construct() {
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
        $this->load->model('user');

        // Load facebook library
        $this->load->library('facebook');

    }

    public function index(){
        $userData = array();
        // Check if user is logged in
        if($this->facebook->is_authenticated()){
            // Get user facebook profile details
            $userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email');

            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid'] = $userProfile['id'];
            $userData['first_name'] = $userProfile['first_name'];
            $userData['last_name'] = $userProfile['last_name'];
            $userData['email'] = $userProfile['email'];
            //$userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['oauth_uid'];
            //$userData['picture_url'] = $userProfile['picture']['data']['url'];

            // Insert or update user data
            $userID = $this->user->checkUser($userData);
            $existing_regular_login = $this->users_model->get_user_id_from_email($userData['email']);
            


            //$linkedID = $this->user->check_linked_data($userData['email'], $userData['oauth_uid']);

            // Check user data insert or update status
            if(!empty($userID)){
                $data['userData'] = $userData;
                $this->session->set_userdata('userData',$userData);
            }else{
               $data['userData'] = array();
            }

            // Get logout URL
            $data['logoutUrl'] = $this->facebook->logout_url(); // change this to registerform....?
        }
        else{ 
            $fbuser = '';

            // Get login URL
            $data['authUrl'] =  $this->facebook->login_url();
        }
        
        if (isset($existing_regular_login)) {
            if ($existing_regular_login === false) {  // Not existing in regular database, need to create username
                $data['facebook_finalize'] = true;
                $data['facebook_email'] = $userData['email'];
                $data['main_content'] = 'signupForm';
                $this->load->view('templates/default',$data);
            }
            else if ($existing_regular_login !== false){
                $data['facebook_merge'] = true;
                $data['facebook_email'] = $userData['email'];
                $data['main_content'] = 'signupForm';
                $this->load->view('templates/default',$data);
            }
        }
        else
            $this->load->view('user_authentication/index',$data);       // Load login & profile view
    }

    public function logout() {
        // Remove local Facebook session
        $this->facebook->destroy_session();

        // Remove user data from session
        $this->session->unset_userdata('userData');

        // Redirect to login page
        redirect('/user_authentication');
    }
} */