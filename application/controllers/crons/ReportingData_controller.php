<?php
class ReportingData_controller extends CI_Controller {

    function __construct(){
        // Authentication for scripts
        parent::__construct();
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Cron Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } else if ($_SERVER['PHP_AUTH_USER'] == CRON_USERNAME && $_SERVER['PHP_AUTH_PW'] == CRON_PASSWORD){
            echo "<p>Authentication OK.<br></p>";
        }
        else {
            header('WWW-Authenticate: Basic realm="Cron Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        }
        
        // Base time and date used for simulations
        $today = strtotime('now');                                      // Do not change
        // Use line below under normal conditions
        $this->todays_time = strtotime('now');
        $this->yesterdays_time = strtotime('-1 day', $today);
        // Use line below and change days value to simulate further days
        //$this->todays_time = strtotime('+2 days', $today);
        $this->todays_datetime = gmdate('Y-m-d H:i:s', $this->todays_time);       // Do not change  
        $this->todays_date = gmdate('Y-m-d', $this->todays_time);       // Do not change  
        $this->yesterdays_date = gmdate('Y-m-d', $this->yesterdays_time);       // Do not change  
        
        $this->Log_filename = gmdate('Y-m-d H-i-s', $this->todays_time)."";     // Do not change  
        
                                  // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('resort_model');
        $this->load->model('reporting_model');
        
    }
    
    public function index(){
        
        $this->logToFile($this->Log_filename, "INFO", "[START]", "index", "ReportingData_controller \n");
        
        $this->load->model('users_model');
        
      /*  $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('item_model');
        $this->load->model('bank_model');
        $this->load->model('weather_model');
        $this->load->model('admin/Admin_stats_model');*/
        
        // Listing all resorts
        $list_all_resorts = $this->list_all_resorts();
        $number_resorts = $list_all_resorts->num_rows();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "list_all_resorts", "There are ".$number_resorts." resorts in total\n");
        
        // Listing all resorts with Tourist Info Center closed, for all players
        $list_all_closed_resorts = $this->list_all_closed_resorts();
        $number_closed_resorts = $list_all_closed_resorts->num_rows();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "list_all_closed_resorts", "There are ".$number_closed_resorts." closed resorts\n");
        
        // Listing all resorts with Tourist Info Center opened, for all players
        $list_all_open_resorts = $this->list_all_open_resorts();
        $number_open_resorts = $list_all_open_resorts->num_rows();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "list_all_open_resorts", "There are ".$number_open_resorts." open resorts\n");


        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WARN RESORT CLOSED **************\n");
        $fn_warn_resort_closed = $this->warn_resort_closed($list_all_closed_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WARN RESORT CLOSED **************\n");
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WARN LOW SNOW LEVEL **************\n");
        $fn_warn_low_snow_level = $this->warn_low_snow_level($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WARN LOW SNOW LEVEL **************\n");
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WARN LOW CONDITION LIFT **************\n");
        $fn_warn_low_condition_lift = $this->warn_low_condition_lift($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WARN LOW CONDITION LIFT **************\n");
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WARN LOW CONDITION SLOPE **************\n");
        $fn_warn_low_condition_slope = $this->warn_low_condition_slope($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WARN LOW CONDITION SLOPE **************\n");
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WARN CLOSED SLOPE **************\n");
        $fn_warn_closed_item = $this->warn_closed_item(2, 'slope');
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WARN CLOSED SLOPE **************\n");
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WARN CLOSED LIFT **************\n");
        $fn_warn_closed_item = $this->warn_closed_item(2, 'lift');
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WARN CLOSED LIFT **************\n");
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START GENERATE REPORTS **************\n");
        $fn_warn_closed_item = $this->generate_reports_resorts();
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END GENERATE REPORTS **************\n"); 
        
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CLEANUP REPORTS DB **************\n");
        $fn_cleanup_reports_db = $this->cleanup_reports_db();
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END CLEANUP REPORTS DB **************\n"); 
            
        $this->logToFile($this->Log_filename, "INFO", "[END]", "index", "ReportingData_controller \n");
        
        
    } // End of Index function
    
    
    /**
     * list_all_resorts      List all resorts of the game
     * 
     * @return type                 Returns the query's results
     */
    protected function list_all_resorts(){
        $this->db->select('game_resorts.id_player, game_resorts.id_resort, players_tbl.preferred_lang, players_tbl.vacation_mode, players_tbl.last_connection, players_tbl.username, players_tbl.email');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->from('game_resorts');
        return $this->db->get();
    }
    
    /**
     * list_all_closed_resorts      List all closed resorts of the game
     * 
     * @return type                 Returns the query's results
     */
    protected function list_all_closed_resorts(){
        $this->db->distinct('game_created_buildings.id_created_buildings', 'game_resorts_tbl.id_resort, game_resorts.id_player, players_tbl.preferred_lang');
        $this->db->from('game_resorts');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_resorts.id_resort = created_buildings_tbl.id_resort', 'inner');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '2');
        $this->db->where('created_buildings_tbl.id_building', '1');
        $this->db->where('players_tbl.vacation_mode !=', '1');
        $query = $this->db->get();
        return $query;
    }
    
    protected function list_all_open_resorts(){
        $this->db->distinct('game_created_buildings.id_created_buildings', 'game_resorts_tbl.id_resort, game_resorts.id_player, players_tbl.preferred_lang');
        $this->db->from('game_resorts');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_resorts.id_resort = created_buildings_tbl.id_resort', 'inner');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '1');
        $this->db->where('created_buildings_tbl.id_building', '1');
        $this->db->where('players_tbl.vacation_mode !=', '1');
        $query = $this->db->get();
        return $query;
    }

    protected function warn_resort_closed($list_all_closed_resorts){
        foreach ($list_all_closed_resorts->result() as $row_list_all_closed_resorts){   // For each closed resort 
            $current_resort = $row_list_all_closed_resorts->id_resort; 
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($row_list_all_closed_resorts->id_player);
            $this->lang->load('reporting',$player_preferred_lang);        
            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('reporting')['resort_closed'];
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'resort', $reporting_data);
            // END Adds reporting data to DB
        }
    }
    
    protected function warn_low_snow_level($list_all_resorts){
        foreach ($list_all_resorts->result() as $row_list_all_resorts){   // For each resort
            $current_resort = $row_list_all_resorts->id_resort; 
            $snow_level_current_resort = $this->get_low_snow_level($current_resort, 10);   // 10 cm
            foreach ($snow_level_current_resort->result() as $row_snow_level_current_resort) {
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($row_list_all_resorts->id_player);
                $this->lang->load('reporting',$player_preferred_lang);        
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['there_is_only'].' '.$row_snow_level_current_resort->snow_level.' '.$this->lang->line('reporting')['low_snow_level'];
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'resort', $reporting_data);
                // END Adds reporting data to DB
            }
        }
    }
    
    protected function warn_low_condition_lift($list_all_resorts){
        foreach ($list_all_resorts->result() as $row_list_all_resorts){   // For each resort
            $current_resort = $row_list_all_resorts->id_resort; 
            $lift_info = $this->get_low_condition_item($current_resort, 'lift', 20);   // condition = 20
            $lift_result = $lift_info->row();
            if ($lift_info->num_rows() > 0 ) {
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($row_list_all_resorts->id_player);
                $this->lang->load('reporting',$player_preferred_lang);        
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['your_lift'].' "'.$lift_result->custom_name.'" '.$this->lang->line('reporting')['has_low_condition'].' ('.$lift_result->lift_condition.'%) '.$this->lang->line('reporting')['not_operate_well'];
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'lift', $reporting_data);
                // END Adds reporting data to DB
            }
        }
    }
    
    protected function warn_low_condition_slope($list_all_resorts){
        foreach ($list_all_resorts->result() as $row_list_all_resorts){   // For each resort
            $current_resort = $row_list_all_resorts->id_resort; 
            $slope_info = $this->get_low_condition_item($current_resort, 'slope', 60);   // condition = 60
            $slope_result = $slope_info->row();
            if ($slope_info->num_rows() > 0 ) {
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($row_list_all_resorts->id_player);
                $this->lang->load('reporting',$player_preferred_lang);        
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['your_slope'].' "'.$slope_result->custom_name.'" '.$this->lang->line('reporting')['has_low_condition'].' ('.$slope_result->slope_condition.'%) '.$this->lang->line('reporting')['more_injuries'];
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'slope', $reporting_data);
                // END Adds reporting data to DB
            }
        }
    }
    
    protected function warn_closed_item($status, $item_type){
        
        $get_created_items_status = $this->get_created_items_status($status, $item_type);   // only status = 2 (closed)
                
        foreach ($get_created_items_status->result() as $row_created_items_status){   // For each resort
            if ($get_created_items_status->num_rows() > 0 ) {
                $current_resort = $row_created_items_status->id_resort; 
                $custom_name = $row_created_items_status->custom_name; 
                $id_player = $row_created_items_status->id_player; 
                $preferred_lang = $row_created_items_status->preferred_lang; 
                $this->lang->load('reporting',$preferred_lang);        
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['your_'.$item_type].' "'.$custom_name.'" '.$this->lang->line('reporting')['is_closed'];
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'resort', $reporting_data);
                // END Adds reporting data to DB
            }
        }
    }
    
    protected function get_created_items_status($status, $item_type){   
        $this->db->distinct('game_resorts.id_resort, game_resorts.id_player, item_tbl.custom_name, players_tbl.preferred_lang');
        $this->db->from('game_resorts');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_resorts.id_resort = created_buildings_tbl.id_resort', 'inner');
        $this->db->join('game_created_'.$item_type.'s as item_tbl', 'game_resorts.id_resort = item_tbl.id_resort', 'inner');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '1');
        $this->db->where('item_tbl.id_status', $status);
        $this->db->where('created_buildings_tbl.id_building', '1');
        $this->db->where('players_tbl.vacation_mode !=', '1');
        return $this->db->get();  
    }
    
    public function get_low_snow_level($id_resort, $min_snow_level){  
        $query = $this->db
            ->select('snow_level')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->where('snow_level <=', $min_snow_level)
            ->get();
        return $query;         // We return the stat value, e.g. 2cm or 3cm
    }
    
    public function get_low_condition_item($id_resort, $item_type, $min_condition){  
        $query = $this->db
            ->select('custom_name, '.$item_type.'_condition')
            ->from('game_created_'.$item_type.'s')
            ->where('id_resort', $id_resort)
            ->where($item_type.'_condition <=', $min_condition)
            ->get();
        return $query;         
    }
    
    public function logToFile($log_filename, $level, $thread, $function, $data){ 
        
        $timestamp = gmdate('Y-m-d H:i:s,000', $this->todays_time)." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        if ( ! write_file(FCPATH . '/application/controllers/logs/ReportingData_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file ReportingData_".$log_filename."'.log :<br>".$data_formatted;
        }
        else{
            echo "Logged ".$function."<br>";
        }
    }
    
    
    protected function generate_reports_resorts(){
        
        $this->todays_time = strtotime('now');
        $yesterday = strtotime('-1 day', $this->todays_time);
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);
        
        $all_reports_to_generate = $this->reporting_model->get_all_reports_to_generate($today_GMT);

        foreach ($all_reports_to_generate->result() as $data_reports_to_generate) {
            $id_resort = $data_reports_to_generate->id_resort;
            $uuid_report = $data_reports_to_generate->uuid_report;
            $this->generate_pdf($id_resort, $uuid_report, $today_GMT);
            $update_report_generated = $this->reporting_model->update_report_generated($uuid_report);
            
            $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs',$player_preferred_lang); 
            
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$id_resort."]", "generate_reports_resorts", "Report ID ".$uuid_report." generated\n");
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['analysis'], 'data' => $this->lang->line('logs')['your_report'].' '.$yesterday_GMT.' '.$this->lang->line('logs')['is_ready']) );   // Add a log row to the game_player_logs table      
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $this->lang->line('logs')['your_report'].' '.$yesterday_GMT.' '.$this->lang->line('logs')['is_ready'].' Resort ID: '.$id_resort) );   // Add a log row to the game_player_logs table         
        } 
    }
    
    
    protected function cleanup_reports_db(){
        
        $this->todays_time = strtotime('now');
        $sixtyDaysAgo = strtotime('-60 day', $this->todays_time);
        $sixtyDaysAgo_GMT = gmdate('Y-m-d', $sixtyDaysAgo);
        $all_reports_to_generate = $this->reporting_model->delete_old_reports($sixtyDaysAgo_GMT);

    }
    
    public function generate_pdf($id_resort, $uuid_report, $report_date) {
	//load pdf library
	$this->load->library('Pdf');
        
        $resort_name = $this->resort_model->get_resort_name($id_resort);
        $id_player = $this->users_model->get_user_id_from_resortID($id_resort);
	
	$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
        $this->lang->load('reporting',$player_preferred_lang);
        $this->lang->load('home',$player_preferred_lang);
            
        $title = $this->lang->line('reporting')['resort_analysis'];
        
        $yesterday = gmdate('Y-m-d', strtotime($report_date));
        $subtitle = $resort_name.' - '.$this->lang->line('reporting')['report_for'].' '.$yesterday.' - '.$this->lang->line('reporting')['produced_on'].' '.$report_date;
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('https://www.ski-manager.net - J@V0');
	$pdf->SetTitle($title);
	$pdf->SetSubject($title);
	$pdf->SetKeywords($title.' https://www.ski-manager.net - J@V0');

        // set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, $subtitle);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set font
	$pdf->SetFont('times', 'BI', 12);
	
	// ---------------------------------------------------------
	
	
	//Generate HTML table data from MySQL - start
	$template = array(
		'table_open' => '<table border="0" cellpadding="2" cellspacing="1" width="100%">',
            'heading_cell_start'  => '',
              'heading_cell_end'    => '',
            'cell_start'          => '',
              'cell_end'            => '',
              'cell_alt_start'      => '',
              'cell_alt_end'        => ''
	);

	$this->table->set_template($template);

	$this->table->set_heading('<th width="12%" style="font-weight: strong; font-size:16px;">'.$this->lang->line('reporting')['type'].'</th>', '<th width="88%" style="font-weight: strong; font-size:16px;">'.$this->lang->line('reporting')['comments'].'</th>');
        
	$report_data = $this->reporting_model->get_report($id_resort, $report_date);
        
        $previous_type = '';
	foreach ($report_data as $data):
            
            if ($previous_type != $data->type) {
                $this->table->add_row('<td width="12%" class="strong font16"></td>', '<td width="88%" style="font-weight: normal; font-size:11px;"></td>');
                $this->table->add_row('<td width="12%" class="strong font16">'.$data->type.'</td>', '<td width="88%" style="font-weight: normal; font-size:11px;">'.$data->data.'</td>');
                $previous_type = $data->type;
            }
            else {
                $this->table->add_row('<td width="12%"></td>', '<td width="88%" style="font-weight: normal; font-size:11px;">'.$data->data.'</td>');
                //$previous_type = $data->type;
            }

        endforeach;
	
	$html = $this->table->generate();
	//Generate HTML table data from MySQL - end
	
	// add a page
	$pdf->AddPage();
	
        $report_intro_text = $this->lang->line('reporting')['report_intro_text'].'<br><br>';
        //$pdf->Image(FCPATH.'img/logo-files/logo.png', 15, 140, 75, 113, 'PNG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);
        
	$pdf->writeHTML($report_intro_text, true, false, true, false, '');
        
	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');
	
	// reset pointer to the last page
	$pdf->lastPage();

	//Close and output PDF document
	$pdf->Output(FCPATH.'files/reports/'.$uuid_report.'.pdf', 'F');
	//$pdf->Output(md5(time()).'.pdf', 'D');
    }
    
    /**
     * add_reporting_data_db     Adds some data to the reporting table
     * 
     * @param type $id_resort                       ID of the resort
     * @param type $type                            Type of data to add
     * @param type $data                            Actual string
     */
    protected function add_reporting_data_db($id_resort, $type, $data){
        
        $this->db->trans_start();
        $data = array ('id_resort' => $id_resort, 'date' => $this->todays_date, 'type' => $type, 'data' => $data);
        $query = $this->db->insert('game_reporting_data', $data);
        $this->db->trans_complete();
        
    }
}
?>