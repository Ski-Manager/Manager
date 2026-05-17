<?php

class Admin_queries_controller extends CI_Controller{
    
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
        $ci->lang->load('logs',$siteLang);
        $this->load->model('admin/Admin_player_model');
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/admin_queries_model');
    }
    
    public function index(){
        $data['main_content'] = 'admin/admin_queries';
        $this->load->view('templates/default_admin',$data); 
    }
    
    public function run_queries(){
        
        if (isset ($_POST['submit'])) {
            $amount = $this->input->post('amount', TRUE);
            $column = $this->input->post('column', TRUE);
            $to_player = $this->input->post('to_player', TRUE);
            if(is_numeric($amount)) {
            
                
                switch ($column) {
                    case 'genepis':
                        $table = 'game_players';
                        $gift_type_lang = 'genepis_title';
                    break;
                    case 'cash':
                        $table = 'game_resorts';
                        $gift_type_lang = 'euros';
                    break;
                    case 'reputation':
                        $table = 'game_resorts';
                        $gift_type_lang = 'reputation';
                    break;
                    case 'snow_level':
                        $table = 'game_resorts';
                        $gift_type_lang = 'cm_of_snow';
                    break;
                }
                $query = $this->admin_queries_model->run_query_on_db($amount, $column, $table, $to_player);

                if(is_numeric($query)){               // If the query succeedded
                    if ($to_player == '') {
                        $select_all_userIDs = $this->users_model->select_all_userIDs_DB($table);   // GEts all the user IDs in the DB
                        $userIDs_array = $select_all_userIDs->result_array();
                    }
                    else {
                        $userIDs_array = array(array('id_player' => $to_player));
                    }
                    $output = '';
                    foreach ($userIDs_array as $select_all_userIDs_value){
                        $currentUserID = $select_all_userIDs_value['id_player'];
                        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                        $this->lang->load('logs',$player_preferred_lang);
                        $gift_type_logs = $this->lang->line('home')[$gift_type_lang];
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['gift'], 'data' => $this->lang->line('logs')['received_gift'].' '.$amount.' '.$gift_type_logs.'.') );   // Add a log row to the game_player_logs table
                        $this->lang->load('logs','english');
                        $gift_type_logs = $this->lang->line('home')[$gift_type_lang]; // reverting back to english for the admin logs
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['gift'], 'data' => 'Player ID '.$currentUserID.' '.$this->lang->line('logs')['has_received_gift'].' '.$amount.' '.$gift_type_logs.'.') );   // Add a log row to the game_player_logs table
                        $output .= 'Player ID '.$currentUserID.' '.$this->lang->line('logs')['has_received_gift'].' '.$amount.' '.$gift_type_logs.'.<br>';
                    }  
                    $this->session->set_flashdata('msg','<div class="alert alert-success text-center">'.$this->lang->line('admin_page')['success'].' '.$query.' rows.<br> Result:<br>'.$output.'</div>');
                }
                else {
                    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['error_query'].'</div>');
                }
            }
            else {
                $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['error_amount_not_numeric'].'</div>');
            }
        }
        else {                    // if nothing POSTED, we display empty form
            $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">'.$this->lang->line('admin_page')['error'].'</div>');
        }
        
        $data['main_content'] = 'admin/admin_queries';
        $this->load->view('templates/default_admin',$data); 
    }
    
    
    
}

?>
