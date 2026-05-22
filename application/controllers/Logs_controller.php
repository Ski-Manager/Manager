<?php
/**
 * 
 */
class Logs_controller extends CI_Controller{
    
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
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
    }
    

    public function index($data = NULL){
        $data['title'] = '<h2>'.$this->lang->line('logs')['title'].'</h2>'; 
        $data['introLogs'] = '<div>'.$this->lang->line('logs')['intro'].'</div>';
        
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)       
            if ($checkIfResortExists->num_rows() > 0) {    
                $data['main_content'] = 'logs';
                $data['resort_built'] = true;
                $this->load->view('templates/default',$data);       
            }
            else { // There is no resort created
                $this->session->set_flashdata('error', 'no_resort');            // redirect to resort contoller with error message
                redirect('resort_controller');
            } 
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    
    public function getDataTable(){
        $currentUserID = $this->users_model->get_user_id();
        $data = $this->logs_model->get_player_logs_DB($currentUserID);   
        echo json_encode($data);
    }
    
    public function change_read_status(){
        $currentUserID = $this->users_model->get_user_id();
        $data = $this->logs_model->change_read_status_DB($currentUserID);
        return $data;
    }
    
    
    
    
}