<?php
/**
 * 
 */
class Finances_controller extends CI_Controller{
    
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
        $ci->lang->load('finances',$siteLang);
        $ci->lang->load('bank',$siteLang);
        $ci->lang->load('genepis',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('finances_model');
        $this->load->model('bank_model');
        $this->load->model('building_model');
        $this->load->model('logs_model');
    }
    

    public function index($data = NULL){
        $data['title'] = '<h2>'.$this->lang->line('finances')['title'];
        $data['title'] .= ' - ';
        $data['title'] .= $this->lang->line('finances')['revenuesAndCosts'].'</h2>'; 
        $data['introFinances'] = '<div>'.$this->lang->line('finances')['intro'].'</div>';
        
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)       
            if ($resultResort->num_rows() > 0) {                                        // if the player has a resort, we call the function calling the different blocks
                $data['resort_built'] = true;
                $data1 = $this->display_revenue_cost_table($currentResortID);   // call the function displaying the blocks
                //$data2 = $this->draw_revenue($currentResortID, 'revenue');   // call the function displaying the blocks

                $data = array_merge($data, $data1);      // Merges all data to "data" for the view            
            }
            else { // There is no resort created
                $this->session->set_flashdata('error', 'no_resort');            // redirect to resort contoller with error message
                redirect('resort_controller');
            } 

            // Load bank data for the combined Finances & Bank view
            $data['title_bank']    = '<h2>'.$this->lang->line('bank')['titleMain'].'</h2>';
            $data['introBank']     = '<div>'.$this->lang->line('bank')['intro'].'</div>';
            $bank_data_array       = $this->_load_bank_data($currentResortID);
            $data                  = array_merge($data, $bank_data_array);

            $data['main_content'] = 'finances_bank';
            $this->load->view('templates/default',$data);   
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    
    
    
    protected function display_revenue_cost_table($currentResortID){ 
        $today = strtotime('now');
        $yesterday = strtotime('-1 day', $today);
        $sevenDaysAgo = strtotime('-6 days', $yesterday);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);
        $today_GMT = gmdate('Y-m-d', $today);
        $current_season_start_date = strtotime(get_current_season_start_date($currentResortID));
        $current_season_start_date_GMT = gmdate('Y-m-d', $current_season_start_date);
        if ($current_season_start_date_GMT == $today_GMT)
            $current_season_start_date_GMT = $yesterday_GMT;    // because first day of season is equal to today.
        
        $table_array = array('rev_marketing', 'cost_marketing', 'rev_achievements', 'rev_tournaments', 'rev_skipass', 'rev_skibus', 'rev_instructor', 'rev_hotel', 'rev_leisure', 'rev_luxury', 'rev_loan', 'rev_medical', 'rev_parking', 'rev_rental', 'rev_real_estate', 'rev_restaurant', 'revenue', 'cost_salaries', 'cost_upkeep', 'rev_other', 'rev_off_season', 'rev_idle', 'expenses', 'cost_purchases', 'cost_tournaments', 'cost_loans', 'cost_taxes');
        foreach ($table_array as $table) {
            $tabledata['yesterday_'.$table] = $this->finances_model->get_yesterday_specific_amount_DB($currentResortID, $table, $yesterday_GMT);
            $tabledata['last7days_'.$table] = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, $table, $sevenDaysAgo_GMT, $yesterday_GMT);
            $tabledata['season_'.$table] = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, $table, $current_season_start_date_GMT, $yesterday_GMT);
        }
        $tabledata['currentResortId'] = $currentResortID;
        return $tabledata;
    }
    
    /** draw_dual_lineChart     Draws a dual axis chart type from two tables in the DB. Options are defined in this function
     * 
     */
    public function draw_dual_lineChart(){            
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $table1 = trim($this->input->post('table1', TRUE));         //Name of the table1/field1
        $table2 = trim($this->input->post('table2', TRUE));         //Name of the table2/field2
        $title = trim($this->input->post('title', TRUE));         // Chart's title
        // Getting the season start date, used for calculating the X-axis beginning
        $current_season_start_date = strtotime(get_current_season_start_date($currentResortID));
        $current_season_start_date_GMT = gmdate('Y-m-d', $current_season_start_date);           // Converting to GMT
        $today = strtotime('now');                                          // Todays timestamp
        $sevenDaysAgo = strtotime('-7 days', $today);                       // subtract 7 days to current timestamp
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);                 // Converting to GMT
        $sevenDaysAgo_GMT_formatted = new DateTime($sevenDaysAgo_GMT);      // Create a DateTime type for 7 days ago, required when using the "diff" function
        $current_season_start_date_GMT_formatted = new DateTime($current_season_start_date_GMT);        // Create a DateTime type for the season start
        $interval = $current_season_start_date_GMT_formatted->diff($sevenDaysAgo_GMT_formatted);   // returns result of diffence in days
        $interval_formatted = $interval->format('%R%a');                    // Formatted: return +4 or -5 or similar values
        
        // If the difference between the beginning of the current season and 7 days ago is 0 or more (meaning same age or resort is older), we choose the season start as begining of the chart
        if ($interval_formatted >= 0) {
            $graph_start_date = $current_season_start_date_GMT;     // Set the start of the graph
            $language_index = $title.'SeasonStart';                 // Build the language variable to be retrieved, dependign on the passed title
            $graph_displayed_date = 1 ;                             // Value to be incremented in a loop to say "Day X" (Day 1, Day 2..) on the X-axis
            $graph_title = $this->lang->line('finances')[$language_index];
        }
        else {  // If the season start is less than 7 days ago, we choose 7 days of data instead. If problem with returned interval, we also display 7 days data
            $graph_start_date = $sevenDaysAgo_GMT;              // Set the start of the graph, 7 days ago (may overlap 2 seasons or contain less than 7 days if first season)
            $language_index = $title.'7DaysAgo';                // Build the language variable to be retrieved, dependign on the passed title
            $graph_title = $this->lang->line('finances')[$language_index];
            $current_season = get_current_season($currentResortID);
            if ($current_season == 1) {     // If first season, the first day to display is "Day 1"
                $graph_displayed_date = 1;
            }
            else {  // If not first season, we need to display the "old days" from previous season (day 130, day 131...)
                $graph_displayed_date = 135 + $interval_formatted + 1;      // Note: $interval_formatted in netative
            }
        }
        // We get the data for the current resort, the provided table (revenue, expenses...) since the start date defined previously
        $graph_data = $this->finances_model->get_history_for_graph($currentResortID, $table1, $graph_start_date);
        $rows = array();    // Initiating the Row array
        
        // detecting size of array to identify last iteration
        $i = 0;
        $number_of_days_to_show = count($graph_data->result());
        // For each day containing data
        foreach($graph_data->result() as $row){
            // We get the data for the current resort, the provided table (revenue, expenses...) for the current day only  
            $graph_data_2 = $this->finances_model->get_history_for_graph_spec_date($currentResortID, $table2, $row->date);
            $temp = array();  
            $table_values['cols'] = array(
            //Labels for the chart, these represent the column titles
            array('id' => '', 'label' => $this->lang->line('finances')['date'], 'type' => 'string'),
            array('id' => '', 'label' => ucfirst($table1), 'type' => 'number'),     // We use ucfirst to make string's first character uppercase
            array('id' => '', 'label' => ucfirst($table2), 'type' => 'number')
            );
            //Values
            if ($i == $number_of_days_to_show - 1) {
                $adjusted_graph_displayed_date = $this->lang->line('finances')['yesterday'];
            }
            else {
                $adjusted_graph_displayed_date = $this->lang->line('home')['big_day'].' '.$graph_displayed_date;
            }
            $temp[] = array('v' => (string) $adjusted_graph_displayed_date);
            $temp[] = array('v' => (float) $row->$table1); 
            $temp[] = array('v' => (float) $graph_data_2); 
            $rows[] = array('c' => $temp);
            
            if ($graph_displayed_date == 135)   // If we displayed 135 in the last loop occurence, we reset the Days to "1"
                $graph_displayed_date = 1;
            else                            // If Day 135 not reached, we simply increment
                $graph_displayed_date ++;
            $i++;
        }
        
        $graph_data->free_result();     // Need to free the results before the next query
        $table_values['rows'] = $rows;
        
        // Defines the options of the Dual axis chart
        $chart_options = array('title' => $graph_title,     // Chart's title, from a language file and defined above
            'width' => '100%',
            'height' => 350,
            'series' => array('0' => array('targetAxisIndex' => 0), '1' => array('targetAxisIndex' => 1)),      // Which axis to assign this series to, where 0 is the default axis, and 1 is the opposite axis.
            'vAxes' => array('0' => array('title' => $this->lang->line('finances')['revenues'].' (€)'), '1' => array('title' => $this->lang->line('finances')['expenses'].' (€)'))  // Specifies properties for individual vertical axes, if the chart has multiple vertical axes.
            //'vAxes' => array('0' => array('title' => $this->lang->line('finances')['in_euros'].' (€)'))  // Specifies properties for individual vertical axes, if the chart has multiple vertical axes.
            );
        
        // Initiates an array that will contain both arrays (data and options). Required for Json if we want the options to be defined in PHP instead of JavaScript
        $chart_package = array();
        $chart_package[] = array('data' => $table_values);
        $chart_package[] = array('options' => $chart_options);
        $jsonTable = json_encode($chart_package, true);
        echo $jsonTable;
    }
    
    
    /** draw_single_lineChart     Draws a single axis chart type from one table in the DB. Options are defined in this function
     * 
     */
    public function draw_single_lineChart(){            
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $table1 = trim($this->input->post('table1', TRUE));         //Name of the table1/field1
        $title = trim($this->input->post('title', TRUE));         // Chart's title
        // Getting the season start date, used for calculating the X-axis beginning
        $current_season_start_date = strtotime(get_current_season_start_date($currentResortID));
        $current_season_start_date_GMT = gmdate('Y-m-d', $current_season_start_date);           // Converting to GMT
        $today = strtotime('now');                                          // Todays timestamp
        $sevenDaysAgo = strtotime('-7 days', $today);                       // subtract 7 days to current timestamp
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);                 // Converting to GMT
        $sevenDaysAgo_GMT_formatted = new DateTime($sevenDaysAgo_GMT);      // Create a DateTime type for 7 days ago, required when using the "diff" function
        $current_season_start_date_GMT_formatted = new DateTime($current_season_start_date_GMT);        // Create a DateTime type for the season start
        $interval = $current_season_start_date_GMT_formatted->diff($sevenDaysAgo_GMT_formatted);   // returns result of diffence in days
        $interval_formatted = $interval->format('%R%a');                    // Formatted: return +4 or -5 or similar values
        
        // If the difference between the beginning of the current season and 7 days ago is 0 or more (meaning same age or resort is older), we choose the season start as begining of the chart
        if ($interval_formatted >= 0) {
            $graph_start_date = $current_season_start_date_GMT;     // Set the start of the graph
            $language_index = $title.'SeasonStart';                 // Build the language variable to be retrieved, dependign on the passed title
            $graph_displayed_date = 1 ;                             // Value to be incremented in a loop to say "Day X" (Day 1, Day 2..) on the X-axis
            $graph_title = $this->lang->line('finances')[$language_index];
        }
        else {  // If the season start is less than 7 days ago, we choose 7 days of data instead. If problem with returned interval, we also display 7 days data
            $graph_start_date = $sevenDaysAgo_GMT;              // Set the start of the graph, 7 days ago (may overlap 2 seasons or contain less than 7 days if first season)
            $language_index = $title.'7DaysAgo';                // Build the language variable to be retrieved, dependign on the passed title
            $graph_title = $this->lang->line('finances')[$language_index];
            $current_season = get_current_season($currentResortID);
            if ($current_season == 1) {     // If first season, the first day to display is "Day 1"
                $graph_displayed_date = 1;
            }
            else {  // If not first season, we need to display the "old days" from previous season (day 130, day 131...)
                $graph_displayed_date = 135 + $interval_formatted + 1;      // Note: $interval_formatted in netative
            }
        }
        // We get the data for the current resort, the provided table (revenue, expenses...) since the start date defined previously
        $graph_data = $this->finances_model->get_history_for_graph($currentResortID, $table1, $graph_start_date);
        $rows = array();    // Initiating the Row array
        // detecting size of array to identify last iteration
        $i = 0;
        $number_of_days_to_show = count($graph_data->result());
        
        // For each day containing data
        foreach($graph_data->result() as $row){
            
            // We get the data for the current resort, the provided table (revenue, expenses...) for the current day only  
            $temp = array();  
            $table_values['cols'] = array(
            //Labels for the chart, these represent the column titles
            array('id' => '', 'label' => $this->lang->line('finances')['date'], 'type' => 'string'),
            array('id' => '', 'label' => $this->lang->line('finances')[$table1], 'type' => 'number')     // We use ucfirst to make string's first character uppercase
            );
       
            //Values
            if ($i == $number_of_days_to_show - 1) {
                $adjusted_graph_displayed_date = $this->lang->line('finances')['yesterday'];
            }
            else {
                $adjusted_graph_displayed_date = $this->lang->line('home')['big_day'].' '.$graph_displayed_date;
            }
            $temp[] = array('v' => (string) $adjusted_graph_displayed_date);
            $temp[] = array('v' => (float) $row->$table1); 
            $rows[] = array('c' => $temp);
            
            if ($graph_displayed_date == 135)   // If we displayed 135 in the last loop occurence, we reset the Days to "1"
                $graph_displayed_date = 1;
            else                            // If Day 135 not reached, we simply increment
                $graph_displayed_date ++;
            $i++;
        }
        
        $graph_data->free_result();     // Need to free the results before the next query
        $table_values['rows'] = $rows;
        
        // Defines the options of the Dual axis chart
        $chart_options = array('title' => $graph_title,     // Chart's title, from a language file and defined above
            'width' => '100%',
            'height' => 350,
            'legend' => 'none',
            //'vAxis' => array ('format' => '#'),     // Removes decimals in labels (keeps only integers)
            'vAxes' => array('0' => array('title' => $this->lang->line('finances')[$table1], 'minValue' => '0'))  // Specifies properties for individual vertical axes, if the chart has multiple vertical axes. 
            );
        
        // Initiates an array that will contain both arrays (data and options). Required for Json if we want the options to be defined in PHP instead of JavaScript
        $chart_package = array();
        $chart_package[] = array('data' => $table_values);
        $chart_package[] = array('options' => $chart_options);
        $jsonTable = json_encode($chart_package, true);
        echo $jsonTable;
    }
    
    
    /** draw_PieChartRevenues     Draws a PieChart chart of the split of the revenues of the season
     * 
     */
    public function draw_PieChartRevenues(){            
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $title = trim($this->input->post('title', TRUE));         // Chart's title
        // Getting the season start date, used for calculating the X-axis beginning
        $current_season_start_date = strtotime(get_current_season_start_date($currentResortID));
        $current_season_start_date_GMT = gmdate('Y-m-d', $current_season_start_date);           // Converting to GMT
        $today = strtotime('now');                                          // Todays timestamp
        $sevenDaysAgo = strtotime('-7 days', $today);                       // subtract 7 days to current timestamp
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);                 // Converting to GMT
        $today_GMT = gmdate('Y-m-d', $today);                 // Converting to GMT
        $sevenDaysAgo_GMT_formatted = new DateTime($sevenDaysAgo_GMT);      // Create a DateTime type for 7 days ago, required when using the "diff" function
        $current_season_start_date_GMT_formatted = new DateTime($current_season_start_date_GMT);        // Create a DateTime type for the season start
        $interval = $current_season_start_date_GMT_formatted->diff($sevenDaysAgo_GMT_formatted);   // returns result of diffence in days
        $interval_formatted = $interval->format('%R%a');                    // Formatted: return +4 or -5 or similar values
        
        // If the difference between the beginning of the current season and 7 days ago is 0 or more (meaning same age or resort is older), we choose the season start as begining of the chart
        if ($interval_formatted >= 0) {
            $graph_start_date = $current_season_start_date_GMT;     // Set the start of the graph
            $language_index = $title.'SeasonStart';                 // Build the language variable to be retrieved, dependign on the passed title
            $graph_displayed_date = 1 ;                             // Value to be incremented in a loop to say "Day X" (Day 1, Day 2..) on the X-axis
            $graph_title = $this->lang->line('finances')[$language_index];
        }
        else {  // If the season start is less than 7 days ago, we choose 7 days of data instead. If problem with returned interval, we also display 7 days data
            $graph_start_date = $sevenDaysAgo_GMT;              // Set the start of the graph, 7 days ago (may overlap 2 seasons or contain less than 7 days if first season)
            $language_index = $title.'7DaysAgo';                // Build the language variable to be retrieved, dependign on the passed title
            $graph_title = $this->lang->line('finances')[$language_index];
            $current_season = get_current_season($currentResortID);
            if ($current_season == 1) {     // If first season, the first day to display is "Day 1"
                $graph_displayed_date = 1;
            }
            else {  // If not first season, we need to display the "old days" from previous season (day 130, day 131...)
                $graph_displayed_date = 135 + $interval_formatted + 1;      // Note: $interval_formatted in netative
            }
        }
        // We get the data for the current resort, the provided table (revenue, expenses...) since the start date defined previously
        //$graph_data = $this->finances_model->get_history_for_graph($currentResortID, $table1, $graph_start_date);
        
        $graph_data = array();
        
        $array_tables = array ('rev_marketing', 'rev_achievements', 'rev_hotel', 'rev_instructor', 'rev_leisure', 'rev_luxury', 'rev_medical', 'rev_other', 'rev_off_season', 'rev_parking', 'rev_real_estate', 'rev_rental', 'rev_restaurant', 'rev_skibus', 'rev_skipass');
        foreach ($array_tables as $table_name) {
            
            $temp = array(); 
            $table_values['cols'] = array(
            //Labels for the chart, these represent the column titles
            array('id' => '', 'label' => $table_name, 'type' => 'string'),
            array('id' => '', 'label' => $this->lang->line('finances')['revenues_split'], 'type' => 'number')
            );
            $friendly_field_name = $this->lang->line('finances')[substr($table_name, 4)];
            $temp[] = array('v' => (string) $friendly_field_name);
            $temp[] = array('v' => (float) $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, $table_name, $graph_start_date, $today_GMT) ); 
            $graph_data[] = array('c' => $temp);
            
            //$graph_data[$table_name] = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, $table_name, $graph_start_date, $today_GMT);
            
            if ($graph_displayed_date == 135)   // If we displayed 135 in the last loop occurence, we reset the Days to "1"
                $graph_displayed_date = 1;
            else                            // If Day 135 not reached, we simply increment
                $graph_displayed_date ++;
        }
           // var_dump ($graph_data);
        //$rows = array();    // Initiating the Row array
        
          
        
        //$array_tables->free_result();     // Need to free the results before the next query
        $table_values['rows'] = $graph_data;
        
        // Defines the options of the Dual axis chart
        $chart_options = array('title' => $graph_title,     // Chart's title, from a language file and defined above
            'width' => '100%',
            'height' => 350,
            'legend' => array ('textStyle' => array ('fontSize' => '12'))
            );
        
        // Initiates an array that will contain both arrays (data and options). Required for Json if we want the options to be defined in PHP instead of JavaScript
        $chart_package = array();
        $chart_package[] = array('data' => $table_values);
        $chart_package[] = array('options' => $chart_options);
        $jsonTable = json_encode($chart_package, true);
        //var_dump ($jsonTable);
        echo $jsonTable;
    }
    private function _load_bank_data($currentResortID) {
        $data = [];
        $name_language = 'name_'.$this->session->userdata('site_lang');

        // Check if tourist info is built (required for bank access)
        $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');
        if ($tourist_info_data->num_rows() == 1) {
            $data['hideBank'] = false;

            // Max daily payment
            $today        = strtotime('now');
            $yesterday    = strtotime('-1 day', $today);
            $sevenDaysAgo = strtotime('-6 days', $yesterday);
            $yesterday_GMT    = gmdate('Y-m-d', $yesterday);
            $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);

            $revenue_last7days              = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'revenue',           $sevenDaysAgo_GMT, $yesterday_GMT);
            $rev_achievements_last7days     = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'rev_achievements',  $sevenDaysAgo_GMT, $yesterday_GMT);
            $expenses_last7days             = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'expenses',          $sevenDaysAgo_GMT, $yesterday_GMT);
            $cost_loans_last7days           = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'cost_loans',        $sevenDaysAgo_GMT, $yesterday_GMT);
            $cost_purchases_last7days       = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'cost_purchases',    $sevenDaysAgo_GMT, $yesterday_GMT);
            $ongoing_all_loans_player       = $this->bank_model->get_ongoing_loan_player($currentResortID);
            $all_loans_daily_payment        = 0;
            if ($ongoing_all_loans_player->num_rows() > 0) {
                foreach ($ongoing_all_loans_player->result() as $loan_player_array) {
                    $all_loans_daily_payment += $loan_player_array->daily_payment;
                }
            }
            $max_daily_payment = ( ($revenue_last7days - $rev_achievements_last7days - $expenses_last7days + $cost_purchases_last7days + $cost_loans_last7days) / 7 ) - $all_loans_daily_payment;
            $max_daily_payment = $max_daily_payment * 1.1;
            $data['max_daily_payment_text'] = $this->lang->line('bank')['based_last_week_profit'].' '.number_format($max_daily_payment, 0, ',', ' ').' €.';

            // Bank data
            $data['bankLogo'] = '<i class="fa-solid fa-building-columns" title="'.$this->lang->line('bank')['titleMain'].'"></i>';
            $data['bankDesc'] = $this->lang->line('bank')['desc'];
            $bank_data        = $this->bank_model->get_generic_bank_data();
            $num_banks        = $bank_data->num_rows();
            for ($i = 0; $i < $num_banks; $i++) {
                $row = $bank_data->row_array($i);
                $data['bankName'][$i]          = $row[$name_language];
                $data['bankMinLoan'][$i]        = number_format($row['min_loan'],  0, ',', ' ');
                $data['bankMinLoan_raw'][$i]    = $row['min_loan'];
                $data['bankMaxLoan'][$i]        = number_format($row['max_loan'],  0, ',', ' ');
                $data['bankMaxLoan_raw'][$i]    = $row['max_loan'];
                $data['bankInterestRate'][$i]   = $row['interest_rate'];
                $data['genepis_required'][$i]   = $row['genepis_required'];
                $data['bankButton'][$i]         = '<td data-id_bank="'.$row['id_bank'].'" data-bank_name="'.$data['bankName'][$i].'"><div class="tooltip" data-tip="'.$this->lang->line('bank')['sign_up_tooltip'].'"><a href="?action=signup_loan" class="signup_loan-dialog"><button class="btn btn-success" id="bankButton_'.$i.'">'.$this->lang->line('bank')['sign_up'].'</button></a></div></td>';
            }

            // Ongoing loans
            $data['ongoing_loans_display']    = false;
            $data['body_ongoing_loans_table'] = '';
            $loan_player = $this->bank_model->get_ongoing_loan_player($currentResortID);
            if ($loan_player->num_rows() > 0) {
                $data['ongoing_loans_display'] = true;
                $data['body_ongoing_loans_table'] .= '<div class="w-full"><h2>'.$this->lang->line('bank')['ongoing_loans'].'</h2>
                    <div class="overflow-x-auto">
                    <table class="table table-zebra myTableLeaderboard center" align="center">
                        <thead><tr>
                            <th>'.$this->lang->line('bank')['bank_name'].'</th>
                            <th>'.$this->lang->line('bank')['signed_on'].'</th>
                            <th>'.$this->lang->line('bank')['daily_payment'].'</th>
                            <th>'.$this->lang->line('bank')['left_to_pay'].'</th>
                            <th>'.$this->lang->line('bank')['last_payment_date'].'</th>
                            <th>'.$this->lang->line('bank')['payoff'].'</th>
                        </tr></thead><tbody>';
                foreach ($loan_player->result() as $loan_player_array) {
                    $specific_bank_data      = $this->bank_model->get_specific_bank_data($loan_player_array->id_bank);
                    $bank_name               = $specific_bank_data->row()->name_english;
                    $real_payments_left      = $loan_player_array->payments_left + 1;
                    $planned_end_date        = gmdate('Y-m-d', strtotime('+'.$real_payments_left.' days'));
                    $data['body_ongoing_loans_table'] .= '<tr>';
                    $data['body_ongoing_loans_table'] .= '<td>'.$bank_name.'</td>';
                    $data['body_ongoing_loans_table'] .= '<td>'.gmdate('d M Y', strtotime($loan_player_array->signed_up_on)).'</td>';
                    $data['body_ongoing_loans_table'] .= '<td>'.number_format($loan_player_array->daily_payment, 0, ',', ' ').' €</td>';
                    $data['body_ongoing_loans_table'] .= '<td>'.number_format($loan_player_array->amount_left, 0, ',', ' ').' € / '.number_format($loan_player_array->borrowed_amount, 0, ',', ' ').' €</td>';
                    $data['body_ongoing_loans_table'] .= '<td>'.gmdate('d M Y', strtotime($planned_end_date)).'</td>';
                    $data['body_ongoing_loans_table'] .= '<td data-id_loan="'.$loan_player_array->id_loan.'" data-left_to_pay="'.$loan_player_array->amount_left.'"><div class="tooltip" data-tip="'.$this->lang->line('bank')['payoff_help'].' '.number_format($loan_player_array->amount_left, 0, ',', ' ').' €."><a href="?action=payoff_loan" class="payoff_loan-dialog"><button class="btn btn-success">'.$this->lang->line('bank')['payoff_now'].'</button></a></div></td></tr>';
                }
                $data['body_ongoing_loans_table'] .= '</tbody></table></div></div>';
            }

            // Loan history
            $data['loan_history_display']    = false;
            $data['body_loan_history_table'] = '';
            $history = $this->bank_model->get_loan_history_DB($currentResortID);
            if ($history->num_rows() > 0) {
                $data['loan_history_display'] = true;
                $data['body_loan_history_table'] .= '<div class="w-full padding_top_bot_15"><h3>'.$this->lang->line('bank')['loan_history'].'</h3>
                    <div class="overflow-x-auto">
                    <table class="table table-zebra myTableLeaderboard center" align="center">
                        <thead><tr>
                            <th>'.$this->lang->line('bank')['bank_name'].'</th>
                            <th>'.$this->lang->line('bank')['signed_on'].'</th>
                            <th>'.$this->lang->line('bank')['borrowed_amount'].'</th>
                            <th>'.$this->lang->line('bank')['reimbursed_on'].'</th>
                        </tr></thead><tbody>';
                foreach ($history->result() as $row) {
                    $bank_name = $row->$name_language;
                    $data['body_loan_history_table'] .= '<tr><td>'.$bank_name.'</td><td>'.gmdate('d M Y', strtotime($row->signed_up_on)).'</td><td>'.number_format($row->borrowed_amount, 0, ',', ' ').' €</td><td>'.gmdate('d M Y', strtotime($row->reimbursed_date)).'</td></tr>';
                }
                $data['body_loan_history_table'] .= '</tbody></table></div></div>';
            }

            // Investment account
            $data['investment_balance']         = 0;
            $data['investment_balance_display'] = '0';
            $data['investment_annual_rate']     = BANK_INVESTMENT_ANNUAL_RATE;
            $data['investment_min_deposit']     = number_format(BANK_INVESTMENT_MIN_DEPOSIT, 0, ',', ' ');
            $data['investment_min_deposit_raw'] = BANK_INVESTMENT_MIN_DEPOSIT;
            $data['investment_max_balance']     = number_format(BANK_INVESTMENT_MAX_BALANCE, 0, ',', ' ');
            $data['investment_max_balance_raw'] = BANK_INVESTMENT_MAX_BALANCE;
            $inv = $this->bank_model->get_investment_DB($currentResortID);
            if ($inv->num_rows() > 0) {
                $inv_row = $inv->row();
                $data['investment_balance']         = (int)$inv_row->balance;
                $data['investment_balance_display'] = number_format((int)$inv_row->balance, 0, ',', ' ');
            }
        } else {
            $data['hideBank']      = true;
            $data['infoMessage']   = 'tourist_info_required';
        }
        return $data;
    }


}