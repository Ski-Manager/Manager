<?php
/**
 *  
 *  $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
    $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
    $this->lang->load('reporting',$player_preferred_lang);
    // START Adds reporting data to DB
    $reporting_data = $this->lang->line('reporting')['no_open_lifts'];
    $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'skipass', $reporting_data);
    // END Adds reporting data to DB
 * 
 * 
 *x NIGHTMAIN: $avg_condition_slope = $total_condition / $num_deserved_slopes; < 50 > improve Q
 *x NIGHTMAIN: $avg_condition_slope = '0'; > no deserving slopes
 *x NIGHTMAIN: $ratio_lift_length_deserved_distance this lift doesnt deserve many slopes (bad ratio)
 *x NIGHTMAIN: $this_lift_max_daily_attraction this lift actual max capacity
 *x NIGHTMAIN: $cumul_lift_max_daily_attraction all lift actual max capacity
 *x NIGHTMAIN: $hotel_capacity hotel capacity
 *x NIGHTMAIN: $max_capacity_housing_access resort tourist capacity
 *x NIGHTMAIN: $daily_infrastructure_attration either lift or access/housing capacity limiting affluence
 *x NIGHTMAIN: $data_daily price info
 *x NIGHTMAIN: $data_weekly price info
 *x NIGHTMAIN: echo ' No open lifts for resort: '.$current_resort.'.'; > open some lifts!
 *x NIGHTMAIN: if $risk_injury_this_slope too high
 *x NIGHTMAIN: if ($danger == 1) > you should have closed the resort yesterday
 *x $max_skibus_use = $capacity * 16;v> you have enough skibuses for your resort
 *x $max_skibus_use = $daily_visitors;   > you could earn more from skibuses by increasing the number of buses or driver's efficiency
 *x if ($get_info_staff->num_rows() > 0) { > get some buses and drivers to generate revenue
 *x foreach ($get_info_instructors->result() as $get_info_instructors_array){ > get some intrucstors!
 *x if $avg_efficiency ski buses < xx > improve efficiency
 *x if $avg_efficiency instructors < xx > improve efficiency
 *x if if ($data_get_info_created_buildings->num_rows() > 0) { = 0 > get some $buildingtype!
 *x $handled_tourists = $daily_visitors/$capacity; > enough capacity this type
 *x if $handled_tourists = 1; > not enough type for your resort
 *x $handled_tourists = 0; > either no visitor or no capacity
 *x NEW FILE list_all_closed_resorts > your resort was closed and generated no revenue yesterday. Open tourist info
 *x NEW FILE check if snow level < 10 > build cannons in case
 *x NEW FILE check quality lifts < 20 > repair
 *x NEW FILE check quality slopes < 50 > better ski patrol or cannons
 *x NEW FILE check if closed lifts > open them
 *x NEW FILE check if closed slopes > open them
 * manage translations
 */
class Reporting_controller extends CI_Controller{
    
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
        $ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('reporting',$siteLang);
        $ci->lang->load('statistics',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('reporting_model');
        $this->load->model('resort_model');
        $this->load->model('statistics_model');
        
        $this->load->helper('download'); // To download the file
        
    }
    
    public function index($action = NULL, $class = NULL){
       
        $currentUserID = $this->users_model->get_user_id(); 
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        
        $data['currentUserID'] = $currentUserID;
                
        $data['list_reports'] = $this->reporting_model->list_reports($currentResortID); 

        // Statistics data for the combined view
        $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);
        $data['resort_built']    = ($checkIfResortExists->num_rows() > 0);
        $data['currentResortId'] = $currentResortID;

        // Displaying the account view
        $data['main_content'] = 'reporting_statistics';
        $this->load->view('templates/default',$data);  
    }
   
    // NOT WORKING!!
    public function download($fileName = NULL) {   
        if ($fileName) {
            $file = base_url().'files/reports//' . $fileName;
            // check file exists    
            if (file_exists ( $file )) {
                // get file content
                $data = file_get_contents ( $file );
                //force download
                force_download ( $fileName, $data );
            } else {
                echo $file;
                // Redirect to base url
                //redirect ( base_url () );
            }
        }
    }
  
    public function order_report() {
        
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today);
            
        $currentUserID = $this->users_model->get_user_id(); 
        $currentResortID = $this->users_model->get_resort_id($currentUserID);  
        $current_genepis = $this->users_model->get_user_genepis_amount($currentUserID); 
        
        $report_today_data = $this->reporting_model->check_report_date($currentResortID, $today_GMT); 
                
        if ($report_today_data->num_rows() == 0 ) {
            if ($current_genepis >= COST_GENEPIS_REPORT) {

                $this->load->library('uuid');
                $uuid_report = $this->uuid->v4(); //Output a v4 UUID 


                $data = array (
                    'uuid_report' => $uuid_report,
                    'id_resort' => $currentResortID,
                    'status' => 'pending',
                    'date' => $today_GMT
                );   

                if ($this->reporting_model->order_report_db($data) && $this->users_model->remove_genepis_cost_DB(COST_GENEPIS_REPORT)) {
                    echo json_encode(array('returned' => true, 'message' => $this->lang->line('reporting')['report_ordered']));
                }
                else {
                    echo json_encode(array('returned' => false, 'message' => $this->lang->line('reporting')['report_not_ordered']));
                }

            }
            else {
                    echo json_encode(array('returned' => true, 'message' => $this->lang->line('reporting')['not_enough_genepis']));
            }
        }
        else {
            echo json_encode(array('returned' => true, 'message' => $this->lang->line('reporting')['already_ordered_today']));
        }
            
        return true;
        
    }
    
    
    
    
    
}