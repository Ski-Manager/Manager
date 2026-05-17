<?php

class Admin_stats_controller extends CI_Controller{
    
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
        $this->load->model('admin/Admin_player_model');
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->load->model('admin/Admin_stats_model');
        $this->load->model('resort_model');
    }
    
    public function index(){
        // Get all information from all the created resorts
        $data['registered_players'] = $this->session->userdata('registered_players');
        $data['online_players'] = $this->session->userdata('online_players');
        
        $data['activated_accounts'] = $this->Admin_stats_model->activated_accounts();
        $data['total_resorts'] = $this->Admin_stats_model->number_resorts();
        
        $now = strtotime('now');
        $two_days_ago = strtotime('-2 days', $now);
        $seven_days_ago = strtotime('-7 days', $now);
        $one_month_ago = strtotime('-30 days', $now);
        
        $active_2days = $this->Admin_stats_model->active_players_last_X_days($two_days_ago);
        $active_7days = $this->Admin_stats_model->active_players_last_X_days($seven_days_ago);
        $active_30days = $this->Admin_stats_model->active_players_last_X_days($one_month_ago);
        $data['active_2days'] = number_format(100*$active_2days/$data['activated_accounts'], 0, ',', ' ');
        $data['active_7days'] = number_format(100*$active_7days/$data['activated_accounts'], 0, ',', ' ');
        $data['active_30days'] = number_format(100*$active_30days/$data['activated_accounts'], 0, ',', ' ');
                
        
        $data['ratio_activated'] = number_format(100*$data['activated_accounts']/$data['registered_players'], 0, ',', ' ');
        $data['ratio_resorts_activated'] = number_format(100*$data['total_resorts']/$data['activated_accounts'], 0, ',', ' ');
        
        // Create the delete button
        $data['main_content'] = 'admin/admin_stats';
        $this->load->view('templates/default_admin',$data); 
    }
    
    /** draw_dual_lineChart     Draws a dual axis chart type from two tables in the DB. Options are defined in this function
     * 
     */
    public function draw_stacked_area_chart(){  
        
        $field1 = trim($this->input->post('field1', TRUE));         //Name of the field1
        $field2 = trim($this->input->post('field2', TRUE));         //Name of the field2
        $field3 = trim($this->input->post('field3', TRUE));         //Name of the field2
        $field4 = trim($this->input->post('field4', TRUE));         //Name of the field2
        $title = trim($this->input->post('title', TRUE));         //CHart title
        
        
        $graph_title = $this->lang->line('adminstats')[$title];
       
        // We get the data for the current resort, the provided table (revenue, expenses...) since the start date defined previously
        $graph_data = $this->Admin_stats_model->get_history_admin_players($field1, $field2, $field3, $field4);
        $rows = array();    // Initiating the Row array
        
        // For each day containing data
        foreach($graph_data->result() as $row){
        // We get the data for the current resort, the provided table (revenue, expenses...) for the current day only  
            $temp = array();  
            $table_values['cols'] = array(
            //Labels for the chart, these represent the column titles
            array('id' => '', 'label' => $this->lang->line('finances')['date'], 'type' => 'string'),
            array('id' => '', 'label' => $this->lang->line('adminstats')[$field1], 'type' => 'number'),     // We use ucfirst to make string's first character uppercase
            array('id' => '', 'label' => $this->lang->line('adminstats')[$field2], 'type' => 'number'),
            array('id' => '', 'label' => $this->lang->line('adminstats')[$field3], 'type' => 'number'),
            array('id' => '', 'label' => $this->lang->line('adminstats')[$field4], 'type' => 'number')
            );
            //Values
            $temp[] = array('v' => (string) $row->date);
            $temp[] = array('v' => (float) $row->$field1); 
            $temp[] = array('v' => (float) $row->$field2);
            $temp[] = array('v' => (float) $row->$field3);
            $temp[] = array('v' => (float) $row->$field4);
            $rows[] = array('c' => $temp);
        }       
        $graph_data->free_result();     // Need to free the results before the next query
        $table_values['rows'] = $rows;
        
        // Defines the options of the Dual axis chart
        $chart_options = array('title' => $graph_title,     // Chart's title, from a language file and defined above
            'width' => '100%',
            'height' => 400,
            'legend' =>  array ('position' => 'top'),
           //'vAxis' => array ('0' => array('format' => '#'), '1' => array('format' => '#')),
            'series' => array('0' => array('targetAxisIndex' => 0, 'type' => 'area'), '1' => array('targetAxisIndex' => 0, 'type' => 'area'), '2' => array('targetAxisIndex' => 1, 'type' => 'area', 'isStacked' => true), '3' => array('targetAxisIndex' => 1, 'type' => 'area', 'isStacked' => true), '4' => array('targetAxisIndex' => 1, 'type' => 'bars', 'isStacked' => true), '5' => array('targetAxisIndex' => 0, 'type' => 'area'))
            
            );
        // Initiates an array that will contain both arrays (data and options). Required for Json if we want the options to be defined in PHP instead of JavaScript
        $chart_package = array();
        $chart_package[] = array('data' => $table_values);
        $chart_package[] = array('options' => $chart_options);
        $jsonTable = json_encode($chart_package, true);
        echo $jsonTable;
    }
    
    
}

?>
