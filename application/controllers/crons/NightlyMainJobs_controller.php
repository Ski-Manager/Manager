<?php
class NightlyMainJobs_controller extends CI_Controller {

function __construct() {
    parent::__construct();

    // Authentication for scripts (supports Apache/Nginx variants)
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (isset($auth_header)) {
        $decoded = base64_decode(substr($auth_header, 6));
        if ($decoded !== false && strpos($decoded, ':') !== false) {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $decoded, 2);
        }
    }

    $has_valid_basic_auth = (
        defined('CRON_USERNAME')
        && defined('CRON_PASSWORD')
        && isset($_SERVER['PHP_AUTH_USER'])
        && isset($_SERVER['PHP_AUTH_PW'])
        && $_SERVER['PHP_AUTH_USER'] === CRON_USERNAME
        && $_SERVER['PHP_AUTH_PW'] === CRON_PASSWORD
    );

    // Optional extra key for cron-job.org (use environment variable for security)
    $secret_key = getenv('CRON_SECRET_KEY') ?: '';
    $has_valid_secret_key = ($this->input->get('key') === $secret_key);

    // Allow either Basic Auth OR secret key
    if (!$has_valid_basic_auth && !$has_valid_secret_key) {
        header('WWW-Authenticate: Basic realm="Cron Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You need to login to access this area';
        exit;
    }

    // Nightly main job can run for a long time on large datasets.
    @set_time_limit(0);
    @ini_set('max_execution_time', '0');
    @ini_set('memory_limit', '512M');
    @ini_set('display_errors', '1');
    @ini_set('html_errors', '0');
    error_reporting(E_ALL);
    register_shutdown_function(array($this, 'handle_shutdown_fatal'));

    // For cron diagnostics: prevent CI database layer from hard-exiting with show_error(500)
    // so we can catch/report the failing query path.
    if (isset($this->db) && is_object($this->db)) {
        $this->db->db_debug = false;
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
}
    
    
    
     
    public function index(){
      try {
      echo 'STARTSTART : '.microtime(true);  
        $this->logToFile($this->Log_filename, "INFO", "[START]", "index", "NightlyMainJobs_controller \n");
        
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('item_model');
        $this->load->model('bank_model');
        $this->load->model('weather_model');
        $this->load->model('finances_model');
        $this->load->model('admin/Admin_stats_model');
        $this->load->model('night_skiing_model');
        $this->load->model('competitors_model');
        $this->load->model('guest_ai_model');
        $this->load->model('visitor_needs_model');
        $this->load->model('staff_model');
        $this->load->model('crisis_events_model');
        $this->load->model('micro_events_model');
        $this->load->model('guest_skill_model');
        $this->load->model('climate_change_model');
        $this->load->model('environment_model');
        $this->load->model('seasonal_objectives_model');
        $this->load->model('energy_model');
        $this->energy_model->ensure_table_exists();
        $this->load->model('real_estate_model');
        $this->load->model('town_model');
        $this->load->model('lift_tech_model');
        $this->load->model('insurance_model');
        $this->load->model('rd_model');
        $this->rd_model->ensure_table_exists();
        $this->load->model('transportation_model');
        $this->load->model('retail_model');
        $this->load->model('sponsorship_model');
        $this->load->model('government_model');
        $this->load->model('users_model');
        $this->load->model('building_model');
        $this->load->model('equipment_model');

$time_start1 = microtime(true);
        // Auto-complete all expired constructions and deliveries across all resorts
        $this->building_model->auto_complete_constructions_DB();
        $this->equipment_model->auto_complete_deliveries_DB();

        // vacation_mode players
        $list_all_vacation_mode_players = $this->list_all_vacation_mode_players();  // If vacation_mode = 1 in game_players table
        $number_vacation_mode_players = $list_all_vacation_mode_players->num_rows();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "list_all_vacation_mode_players", "There are ".$number_vacation_mode_players." players in vacation mode ".microtime(true)."\n");
        
        // Listing all resorts with Tourist Info Center opened, for all players
        $list_all_opened_resorts = $this->list_all_opened_resorts();
        $number_opened_resorts = $list_all_opened_resorts->num_rows();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "list_all_opened_resorts", "There are ".$number_opened_resorts." opened resorts ".microtime(true)."\n");
        
        // Listing all resorts
        $list_all_resorts = $this->list_all_resorts();
        $number_resorts = $list_all_resorts->num_rows();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "list_all_resorts", "There are ".$number_resorts." resorts in total ".microtime(true)."\n");
        
$time_end1 = microtime(true);
$execution_time1 = ($time_end1 - $time_start1);
echo '<b>Loop 1:</b> '.$execution_time1.' Sec';

        // Only run the scripts if there is at least one existing resort
        if ($number_resorts > 0) {

        $time_start2 = microtime(true);    
            switch (HOST_TYPE) {
                case 'subdomain':
                    echo 'not sending vacation mode email (subdomain)';
                    break;
                case 'localhost':
                    echo 'not sending vacation mode email (localhost)';
                    break;
                default:
                    $check_vacation_mode = $this->check_vacation_mode($list_all_resorts); 
                    echo 'checking vacation mode (default)';
                    break;
            }
                 
            //Create stats entries for the new day
            $array_tables = array ('affluence', 'cash', 'cost_purchases', 'cost_salaries', 'cost_upkeep', 'cost_loans', 'cost_taxes', 'expenses', 'injuries', 'revenue', 'rev_marketing', 'cost_marketing', 'rev_tournaments', 'cost_tournaments', 'rev_special_events', 'cost_special_events', 'rev_hotel', 'rev_instructor', 'rev_leisure', 'rev_loan', 'rev_luxury', 'rev_medical', 'rev_other', 'rev_parking', 'rev_achievements', 'rev_real_estate', 'rev_rental', 'rev_restaurant', 'rev_skibus', 'rev_skipass', 'rev_off_season', 'rev_retail', 'rev_idle', 'snow_level', 'reputation', 'prestige_gains');
            foreach ($array_tables as $table_name) {
                $create_stat_new_day = $this->create_stat_new_day($list_all_resorts, $table_name);
                if (!$create_stat_new_day)
                    $this->logToFile($this->Log_filename, "WARN", "[ ]", "create_stat_new_day", "There was a problem with adding new day to table ".$table_name." ".microtime(true)."\n");
            }
            
            // Move players to next season if needed
            $create_new_season = $this->create_new_season($list_all_resorts);

            // Progress guest skill levels at each season boundary
            $this->progress_guest_skills($list_all_resorts);
            
            /* START add current cash to history */
            //echo '<br>***********************************************<br>';
            //echo '******** ADD CURRENT CASH TO HISTORY **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ADD CURRENT CASH TO HISTORY ************** ".microtime(true)."\n");
            // Copies the cash to cash table for today's entry
            $add_daily_cash_to_history = $this->add_todays_stat_to_history('cash'); 
            //echo $add_daily_cash_to_history;
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ADD CURRENT CASH TO HISTORY ************** ".microtime(true)."\n");
            //echo '******** ADD CURRENT CASH TO HISTORY **************<br>';
            //echo '*********************************************<br><br>';
            /* END add current cash to history */
            
            /* START reputation current reputation to history */
            //echo '<br>***********************************************<br>';
            //echo '******** ADD CURRENT REPUTATION TO HISTORY **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ADD CURRENT REPUTATION TO HISTORY ************** ".microtime(true)."\n");
            // Copies the reputation to reputation table for today's entry
            $add_daily_cash_to_history = $this->add_todays_stat_to_history('reputation'); 
            //echo $add_daily_reputation_to_history;
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ADD CURRENT REPUTATION TO HISTORY ************** ".microtime(true)."\n");
            //echo '******** ADD CURRENT REPUTATION TO HISTORY **************<br>';
            //echo '*********************************************<br><br>';
            /* END add current reputation to history */
            
            /* START Take cost for loan */
            //echo '***********************************************<br>';
            //echo '******** START PAY LOANS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START PAY LOANS ************** ".microtime(true)."\n");
            $fn_pay_loans = $this->pay_loans($list_all_resorts);
            //echo $fn_pay_loans;
            //echo '******** END PAY LOANS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END PAY LOANS ************** ".microtime(true)."\n");
            /* END Take cost for loan  */
            
            /* START Check if loan is finally paid */
            //echo '***********************************************<br>';
            // echo '******** START CHECKING REIMBURSED LOAN **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CHECKING REIMBURSED LOAN ************** ".microtime(true)."\n");
            $fn_finalize_loans = $this->finalize_loans();
            //echo $fn_finalize_loans;
            //echo '******** END CHECKING REIMBURSED LOAN **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CHECKING REIMBURSED LOAN ************** ".microtime(true)."\n");
            /* END Check if loan is finally paid  */
          
            /* START add current snow level to history */
            //echo '***********************************************<br>';
            //echo '******** ADD CURRENT SNOW LEVEL TO HISTORY **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ADD CURRENT SNOW LEVEL TO HISTORY ************** ".microtime(true)."\n");
            // Copies the snow level to snow_level_d0
            $add_daily_snow_level_to_history = $this->add_todays_stat_to_history('snow_level'); 
            //echo $add_daily_snow_level_to_history;
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ADD CURRENT SNOW LEVEL TO HISTORY ************** ".microtime(true)."\n");
            //echo '******** ADD CURRENT SNOW LEVEL TO HISTORY **************<br>';
            //echo '*********************************************<br>';
            /* END add current snow level to history */
            
            /* START to calculate weather conditions */
            //echo '<br>***********************************************<br>';
            //echo '******** CALCULATE WEATHER **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CALCULATE WEATHER ************** ".microtime(true)."\n");
            $snow_change = $this->calculate_weather();
            //echo $snow_change;
            $change_snow_level_player = $this->change_snow_level_player($list_all_resorts);
            // Adds todays snow level to all the resorts
            $snow_change_result = $this->snow_change_fn($list_all_resorts, $change_snow_level_player['snow_level'], $change_snow_level_player['name_english']);

            // Per-slope aspect snow update for custom trails
            $this->update_custom_slope_snow($list_all_resorts, $change_snow_level_player['snow_level']);

            // Apply natural seasonal melt (late/closing season only)
            $this->apply_seasonal_melt($list_all_resorts);

            // Process per-trail snowmaking
            $this->process_trail_snowmaking($list_all_resorts);
                
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END CALCULATE WEATHER ************** ".microtime(true)."\n");
            //echo '<br>******** CALCULATE WEATHER **************<br>';
            //echo '*********************************************<br><br>';
            /* END to calculate weather conditions */  
            
$time_end2 = microtime(true);
$execution_time2 = ($time_end2 - $time_start2);
echo '<b>Loop 2:</b> '.$execution_time2.' Sec';    
        }
        else
            $this->logToFile($this->Log_filename, "INFO", "[ ]", "index", "There are no resort in the database. End of script ".microtime(true)."\n");
            //echo 'There are no resort in the database. End of script';

        if ($number_opened_resorts > 0) {
$time_start3 = microtime(true);            
            /* START Removes some points of quality/condition to any slope or lift */
            //echo '***************************************************<br>';
            //echo '******** START DEGRADE SLOPE/LIFT CONDITION ********<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START DEGRADE SLOPE/LIFT CONDITION ************** ".microtime(true)."\n");
            $quality_loss_per_Day_generic = '3';                                // We may want to change this in the future
            // If the snow is melting, we reduce the quality by 5.
            if ($change_snow_level_player['snow_level'] < 0)
                $quality_loss_weather = -5;
            // If the snow level is increasing, we increase the quality by 5
            else if ($change_snow_level_player['snow_level'] > 0 )
                $quality_loss_weather = 5;
            // If there is no snow level change, nothing changes
            else
                $quality_loss_weather = 0;
            // The final quality loss per day if the substraction of the generic value and the weather condition.
            $quality_loss_per_Day = $quality_loss_per_Day_generic + $quality_loss_weather;
            
            // For the slopes
            $fn_degrade_quality_item = $this->degrade_quality_item($quality_loss_per_Day, 'slope');
            //echo $fn_degrade_quality_item.' slopes quality was decreased by '.$quality_loss_per_Day.' points<br>';
            // For the lifts, the weather doesn't affect the quality loss
            $fn_degrade_quality_item = $this->degrade_quality_item($quality_loss_per_Day_generic, 'lift');
            //echo $fn_degrade_quality_item.' lifts quality was decreased by '.$quality_loss_per_Day_generic.' points<br>';

            // Night Skiing: extra slope degradation
            $this->apply_night_skiing_quality_loss();

            // Climate change: extra slope degradation from glacier loss
            $this->apply_climate_glacier_loss($list_all_opened_resorts);
            
            // If condition is too low, we set the lift into maintenance mode
            $fn_maintenance_mode_handling = $this->maintenance_mode_handling();
            //echo $fn_maintenance_mode_handling.' lifts were placed in maintenance mode due to low condition<br>';
            
            // Check for lifts that have reached end of life (mandatory replacement)
            $this->check_lift_end_of_life();
            
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END DEGRADE SLOPE/LIFT CONDITION ************** ".microtime(true)."\n");
            //echo '******** END DEGRADE SLOPE/LIFT CONDITION ********<br>';
            //echo '**************************************************<br><br>';
            /* END Removes some points of quality/condition to any slope or lift */
            
            /* START Increase quality for slopes & lifts with ski patrol and mechanic assigned */
            //echo '***********************************************<br>';
            //echo '******** START BONUS CONDITION **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START BONUS CONDITION ************** ".microtime(true)."\n");

            /* START Update staff morale (based on weather, salary, workload) */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START UPDATE STAFF MORALE ************** ".microtime(true)."\n");
            $this->update_staff_morale($list_all_opened_resorts, $change_snow_level_player['name_english']);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END UPDATE STAFF MORALE ************** ".microtime(true)."\n");
            /* END Update staff morale */

            $fn_improve_quality_item_grooming = $this->improve_quality_item_grooming($quality_loss_per_Day, 'slope');
            //echo $fn_improve_quality_item_grooming;
            //echo '<br>';
            $fn_improve_quality_item_lift = $this->improve_quality_item_lift('liftmechanic', $quality_loss_per_Day, 'lift');
            //echo $fn_improve_quality_item_lift;
            //echo '******** END BONUS CONDITION **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END BONUS CONDITION ************** ".microtime(true)."\n");
            /* END Increase quality for slopes & lifts with ski patrol and mechanic assigned */

            /* START Apply natural hazards (avalanche, storm damage, ice accumulation) */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START NATURAL HAZARDS ************** ".microtime(true)."\n");
            $this->apply_natural_hazards($list_all_opened_resorts, $change_snow_level_player);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END NATURAL HAZARDS ************** ".microtime(true)."\n");
            /* END Apply natural hazards */
            
            /* START Pay salaries to staff where resort is opened */
            //echo '***********************************************<br>';
            //echo '******** START PAY SALARIES **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START PAY SALARIES ************** ".microtime(true)."\n");
            $fn_pay_salaries = $this->pay_salaries($list_all_opened_resorts);
            //echo $fn_pay_salaries;
            //echo '******** END PAY SALARIES **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END PAY SALARIES ************** ".microtime(true)."\n");
            /* END Pay salaries to staff where resort is opened */
           
            /* START calculating visitors (involves many functions) */
            //echo '***********************************************<br>';
            //echo '******** START VISITOR CALCULATIONS ********<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START VISITOR CALCULATIONS ************** ".microtime(true)."\n");
            $visitor_calculations = $this->visitor_calculations($list_all_opened_resorts, $change_snow_level_player['name_english']);        // $change_snow_level_player['name_english'] contains "raining, sunny..."
            //echo '******** END VISITOR CALCULATIONS ********<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END VISITOR CALCULATIONS ************** ".microtime(true)."\n");
            /* END calculating visitors (involves many functions) */

            /* START counting injured visitors if ski patrol missing */
            //echo '***********************************************<br>';
            //echo '******** START INJURED VISITORS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START INJURED VISITORS ************** ".microtime(true)."\n");
            // Updates the DB (game_created_slopes) with injuries for each slope
            $fn_count_injured_visitors = $this->count_injuries_slope($visitor_calculations);
            // Updates each resort's reputation depending on number of injuries previous day
            $fn_withdraw_reputation_injuries = $this->withdraw_reputation_injuries($list_all_opened_resorts);
            //echo $fn_withdraw_reputation_injuries;
            //echo '******** END INJURED VISITORS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END INJURED VISITORS ************** ".microtime(true)."\n");
            /* END counting injured visitors if ski patrol missing */

            /* START Black Diamond reputation bonus */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START BLACK DIAMOND REPUTATION ************** ".microtime(true)."\n");
            $this->award_reputation_black_diamond($list_all_opened_resorts);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END BLACK DIAMOND REPUTATION ************** ".microtime(true)."\n");
            /* END Black Diamond reputation bonus */

            /* START adding snow from snow cannons */
            //echo '***********************************************<br>';
           //echo '******** START SNOW CHANGE FROM CANNONS **************<br>';
            // $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START SNOW CHANGE FROM CANNONS ************** ".microtime(true)."\n");
            // DEPRECATED by trail snowmaking: $message_snow_from_cannons = $this->add_snow_from_cannons($visitor_calculations, 'cannon', $change_snow_level_player['snow_level']);
            //echo $message_snow_from_cannons;
            //echo '******** END SNOW CHANGE FROM CANNONS **************<br>';
            //echo '*********************************************<br><br>';
            // $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END SNOW CHANGE FROM CANNONS ************** ".microtime(true)."\n");
            /* END adding snow from snow cannons */

            /* START refill/deplete water reservoir based on weather */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START WATER RESERVOIR UPDATE ************** ".microtime(true)."\n");
            $this->update_water_reservoir($visitor_calculations, $change_snow_level_player['name_english']);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END WATER RESERVOIR UPDATE ************** ".microtime(true)."\n");
            /* END water reservoir update */

            /* START night skiing bonus revenue */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START NIGHT SKIING REVENUE ************** ".microtime(true)."\n");
            $this->revenue_night_skiing($visitor_calculations, $change_snow_level_player['name_english']);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END NIGHT SKIING REVENUE ************** ".microtime(true)."\n");
            /* END night skiing bonus revenue */

            /* START adding revenue for bus services */
            //echo '***********************************************<br>';
            //echo '******** START REVENUE SKIBUS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START REVENUE SKIBUS ************** ".microtime(true)."\n");
            $message_bonus_skibus = $this->generate_revenue_skibus($list_all_opened_resorts, $visitor_calculations);
            //echo $message_bonus_skibus;
            //echo '******** END REVENUE SKIBUS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END REVENUE SKIBUS ************** ".microtime(true)."\n");
            /* END adding revenue for bus services */
            
            /* START adding revenue for ski schools */
            //echo '***********************************************<br>';
            //echo '******** START REVENUE SKI SCHOOLS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START REVENUE SKI SCHOOLS ************** ".microtime(true)."\n");
            $message_bonus_skischool = $this->generate_revenue_instructors($list_all_opened_resorts, $visitor_calculations);
            //echo $message_bonus_skischool;
            //echo '******** END REVENUE SKI SCHOOLS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END REVENUE SKI SCHOOLS ************** ".microtime(true)."\n");
            /* END adding revenue for ski schools */

                      
            /* START adding revenue visitors */
            //echo '***********************************************<br>';
            //echo '******** START REVENUE VISITORS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START REVENUE SKI SCHOOLS ************** ".microtime(true)."\n");
            $revenue_visitors = $this->revenue_visitors($visitor_calculations);
            //echo $revenue_visitors;
            //echo '******** END REVENUE VISITORS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END REVENUE SKI SCHOOLS ************** ".microtime(true)."\n");
            /* END adding revenue visitors */

            /* START adding revenue for buildings */
            //echo '***********************************************<br>';
            //echo '******** START REVENUE BUILDINGS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START REVENUE BUILDINGS ************** ".microtime(true)."\n");
            $message_bonus_building = $this->generate_revenue_building($visitor_calculations, 'hotel');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'restaurant');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'rental');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'leisure');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'luxury');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'medical');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'parking');
            $message_bonus_building .= $this->generate_revenue_building($visitor_calculations, 'facility');
            //echo $message_bonus_building;
            //echo '******** END REVENUE BUILDINGS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END REVENUE BUILDINGS ************** ".microtime(true)."\n");
            /* END adding revenue for building */

            /* START adding revenue for real estate */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START REVENUE REAL ESTATE ************** ".microtime(true)."\n");
            $this->generate_revenue_real_estate();
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END REVENUE REAL ESTATE ************** ".microtime(true)."\n");
            /* END adding revenue for real estate */

            /* START scenic lifts revenue */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START SCENIC LIFTS REVENUE ************** ".microtime(true)."\n");
            $this->load->model('scenic_lift_model');
            $this->generate_revenue_scenic_lifts($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END SCENIC LIFTS REVENUE ************** ".microtime(true)."\n");
            /* END scenic lifts revenue */

            /* START adding investment interest */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START INVESTMENT INTEREST ************** ".microtime(true)."\n");
            $this->generate_investment_interest();
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END INVESTMENT INTEREST ************** ".microtime(true)."\n");
            /* END adding investment interest */

            /* START season ski pass revenue */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START SEASON PASS REVENUE ************** ".microtime(true)."\n");
            $this->load->model('season_pass_model');
            $this->generate_revenue_season_passes();
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END SEASON PASS REVENUE ************** ".microtime(true)."\n");
            /* END season ski pass revenue */
            
            /* START adding cost for buildings */
            //echo '***********************************************<br>';
            //echo '******** START COST BUILDINGS **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START COST BUILDINGS ************** ".microtime(true)."\n");
            $message_cost_building = $this->generate_cost_building($visitor_calculations, 'cannon');
            $message_cost_building = $this->generate_cost_building($visitor_calculations, 'open_stage');
            $message_cost_building = $this->generate_cost_building($visitor_calculations, 'curling_center');
            $message_cost_building = $this->generate_cost_building($visitor_calculations, 'icerink');
            $message_cost_building = $this->generate_cost_building($visitor_calculations, 'housing_complex');
            $this->charge_snowmaking_electricity($visitor_calculations);
            $this->complete_lift_tech_research();
            $message_cost_groomer = $this->generate_cost_equipment($visitor_calculations, '1'); // 1 = groomer
            $message_cost_skibus = $this->generate_cost_equipment($visitor_calculations, '2'); // 2 = skibuss
            $message_cost_lift = $this->generate_cost_lift('lift');
            $this->generate_cost_energy($visitor_calculations);
            //echo '******** END COST BUILDINGS **************<br>';
            //echo '*********************************************<br><br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END COST BUILDINGS ************** ".microtime(true)."\n");
            /* END adding cost for building */

            /* START show prestige bonus in activity */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START PRESTIGE BONUS IN ACTIVITY ************** ".microtime(true)."\n");
            $message_add_entry_in_activity_logs = $this->add_entry_in_activity_logs($visitor_calculations, 'prestige_gains');
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END PRESTIGE BONUS IN ACTIVITY ************** ".microtime(true)."\n");
            /* END show prestige bonus in activity */

            /* START generate crisis events */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START GENERATE CRISIS EVENTS ************** ".microtime(true)."\n");
            $this->generate_crisis_events($list_all_opened_resorts);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END GENERATE CRISIS EVENTS ************** ".microtime(true)."\n");
            /* END generate crisis events */
            /* START generate micro-events (quick decisions) */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START GENERATE MICRO EVENTS ************** ".microtime(true)."\n");
            $this->micro_events_model->expire_old_events_DB();
            $this->generate_micro_events($list_all_opened_resorts);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END GENERATE MICRO EVENTS ************** ".microtime(true)."\n");
            /* END generate micro-events */
            /* START lift line management */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START LIFT LINE MANAGEMENT ************** ".microtime(true)."\n");
            $this->load->model('lift_line_model');
            $this->process_lift_line_management($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END LIFT LINE MANAGEMENT ************** ".microtime(true)."\n");
            /* END lift line management */
            /* START insurance premiums */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START INSURANCE PREMIUMS ************** ".microtime(true)."\n");
            $this->process_insurance_premiums($list_all_resorts);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END INSURANCE PREMIUMS ************** ".microtime(true)."\n");
            /* END insurance premiums */
            /* START VIP & loyalty programmes */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START VIP LOYALTY ************** ".microtime(true)."\n");
            $this->load->model('vip_loyalty_model');
            $this->process_vip_loyalty($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END VIP LOYALTY ************** ".microtime(true)."\n");
            /* END VIP & loyalty programmes */
            /* START celebrity / VIP visits */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CELEBRITY VISITS ************** ".microtime(true)."\n");
            $this->load->model('celebrity_visit_model');
            $this->process_celebrity_visits($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END CELEBRITY VISITS ************** ".microtime(true)."\n");
            /* END celebrity / VIP visits */
            /* START crowding system */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CROWDING SYSTEM ************** ".microtime(true)."\n");
            $this->load->model('crowding_model');
            $this->process_crowding_system($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END CROWDING SYSTEM ************** ".microtime(true)."\n");
            /* END crowding system */
            /* START maintenance depth */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START MAINTENANCE DEPTH ************** ".microtime(true)."\n");
            $this->load->model('maintenance_depth_model');
            $this->process_maintenance_depth($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END MAINTENANCE DEPTH ************** ".microtime(true)."\n");
            /* END maintenance depth */
            /* START town development update */
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START TOWN DEVELOPMENT UPDATE ************** ".microtime(true)."\n");
            $this->update_town_development($visitor_calculations);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END TOWN DEVELOPMENT UPDATE ************** ".microtime(true)."\n");
            /* END town development update */
$time_end3 = microtime(true);
$execution_time3 = ($time_end3 - $time_start3);
echo '<b>Loop 3:</b> '.$execution_time3.' Sec';               
        } // End of open resorts
        
$time_start4 = microtime(true);        
        /* START TAKE TAXES COST */
        //echo '***********************************************<br>';
        //echo '******** START TAKE TAXES COST **************<br>';
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START TAKE TAXES COST ************** ".microtime(true)."\n");
        $message_cost_building = $this->take_cost_taxes($list_all_resorts);
        //echo '******** END TAKE TAXES COST **************<br>';
        //echo '*********************************************<br><br>';
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END TAKE TAXES COST ************** ".microtime(true)."\n");
        /* END TAKE TAXES COST */ 

        /* START environmental system */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ENVIRONMENTAL SYSTEM ************** ".microtime(true)."\n");
        $this->process_environmental_system($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ENVIRONMENTAL SYSTEM ************** ".microtime(true)."\n");
        /* END environmental system */

        /* START government regulations */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START GOVERNMENT REGULATIONS ************** ".microtime(true)."\n");
        $this->process_government_regulations($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END GOVERNMENT REGULATIONS ************** ".microtime(true)."\n");
        /* END government regulations */

        /* START off-season revenue (year-round, independent of ski conditions) */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START OFF-SEASON REVENUE ************** ".microtime(true)."\n");
        $this->generate_revenue_off_season($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END OFF-SEASON REVENUE ************** ".microtime(true)."\n");
        /* END off-season revenue */

        /* START accessibility & transportation */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START TRANSPORTATION ************** ".microtime(true)."\n");
        $this->process_transportation($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END TRANSPORTATION ************** ".microtime(true)."\n");
        /* END accessibility & transportation */
        /* START retail & amenities revenue */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START RETAIL AMENITIES REVENUE ************** ".microtime(true)."\n");
        $this->generate_revenue_retail($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END RETAIL AMENITIES REVENUE ************** ".microtime(true)."\n");
        /* END retail & amenities revenue */

        /* START idle income accumulation */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START IDLE INCOME ************** ".microtime(true)."\n");
        $this->generate_idle_income($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END IDLE INCOME ************** ".microtime(true)."\n");
        /* END idle income accumulation */
        
        
        /* START daily admin stats */
        //echo '***********************************************<br>';
        //echo '******** START DAILY ADMIN STATS **************<br>';
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START COST BUILDINGS ************** ".microtime(true)."\n");
        $message_cost_building = $this->insert_admin_daily_statistics();
        //echo '******** END DAILY ADMIN STATS **************<br>';
        //echo '*********************************************<br><br>';
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END COST BUILDINGS ************** ".microtime(true)."\n");
        /* END daily admin stats */ 

        /* START competitor resorts nightly evolution */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START COMPETITOR RESORTS ************** ".microtime(true)."\n");
        $this->apply_competitor_pressure();
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END COMPETITOR RESORTS ************** ".microtime(true)."\n");
        /* END competitor resorts nightly evolution */

        /* START experimental R&D processing */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START EXPERIMENTAL R&D ************** ".microtime(true)."\n");
        $this->process_rd_projects($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END EXPERIMENTAL R&D ************** ".microtime(true)."\n");
        /* END experimental R&D processing */
        /* START research tree upgrade bonuses */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START RESEARCH TREE UPGRADES ************** ".microtime(true)."\n");
        $this->process_research_upgrades($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END RESEARCH TREE UPGRADES ************** ".microtime(true)."\n");
        /* END research tree upgrade bonuses */
        /* START accommodation upgrades nightly */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ACCOMMODATION UPGRADES ************** ".microtime(true)."\n");
        $this->load->model('accommodation_model');
        $this->process_accommodation_upgrades();
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ACCOMMODATION UPGRADES ************** ".microtime(true)."\n");
        /* END accommodation upgrades nightly */
        /* START contract expiry checks */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CONTRACT EXPIRY CHECKS ************** ".microtime(true)."\n");
        $this->check_expiring_contracts($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END CONTRACT EXPIRY CHECKS ************** ".microtime(true)."\n");
        /* END contract expiry checks */
        /* START emergency & rescue system */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START EMERGENCY RESCUE SYSTEM ************** ".microtime(true)."\n");
        $this->load->model('emergency_model');
        $this->process_emergency_system($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END EMERGENCY RESCUE SYSTEM ************** ".microtime(true)."\n");
        /* END emergency & rescue system */
        /* START sponsorship & branding */
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START SPONSORSHIP & BRANDING ************** ".microtime(true)."\n");
        $this->process_sponsorships($list_all_resorts);
        $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END SPONSORSHIP & BRANDING ************** ".microtime(true)."\n");
        /* END sponsorship & branding */

        $this->logToFile($this->Log_filename, "INFO", "[END]", "index", "NightlyMainJobs_controller \n");
$time_end4 = microtime(true);
$execution_time4 = ($time_end4 - $time_start4);
echo '<b>Loop 4:</b> '.$execution_time4.' Sec';



      echo 'ENDEND : '.microtime(true); 
      
      
      } catch (Throwable $e) {
          $error_message = 'NightlyMainJobs_controller failed: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
          if (isset($this->db) && is_object($this->db) && method_exists($this->db, 'error')) {
              $db_error = $this->db->error();
              if (is_array($db_error) && !empty($db_error['code'])) {
                  $error_message .= ' | DB['.$db_error['code'].']: '.$db_error['message'];
              }
          }
          $fallback_log_filename = isset($this->Log_filename) ? $this->Log_filename : gmdate('Y-m-d H-i-s', time());
          $this->logToFile($fallback_log_filename, "ERROR", "[FATAL]", "index", $error_message."\n");
          if (!headers_sent()) {
              header('HTTP/1.1 200 OK');
          }
          echo '<br><b>ERROR:</b> '.htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
      }
    } // End of Index function

    public function handle_shutdown_fatal() {
        $error = error_get_last();
        if (!$error) {
            return;
        }

        $fatal_types = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
        if (!in_array($error['type'], $fatal_types, true)) {
            return;
        }

        $error_message = 'NightlyMainJobs fatal: '.$error['message'].' in '.$error['file'].' on line '.$error['line'];
        if (isset($this->db) && is_object($this->db) && method_exists($this->db, 'error')) {
            $db_error = $this->db->error();
            if (is_array($db_error) && !empty($db_error['code'])) {
                $error_message .= ' | DB['.$db_error['code'].']: '.$db_error['message'];
            }
        }
        $fallback_log_filename = isset($this->Log_filename) ? $this->Log_filename : gmdate('Y-m-d H-i-s', time());
        $this->logToFile($fallback_log_filename, "ERROR", "[SHUTDOWN]", "handle_shutdown_fatal", $error_message."\n");

        if (!headers_sent()) {
            header('HTTP/1.1 200 OK');
        }
        echo '<br><b>FATAL:</b> '.htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * list_all_opened_resorts      List all open resorts of the game
     * 
     * @return type                 Returns the query's results
     */
    protected function list_all_opened_resorts(){
        $this->db->select('created_buildings_tbl.id_created_buildings, game_resorts.id_resort, game_resorts.id_player, players_tbl.preferred_lang');
        $this->db->select('game_resorts.skipass_daily, game_resorts.skipass_weekly, game_resorts.vip_pass_price, game_resorts.family_discount_pct, game_resorts.group_discount_pct');
        $this->db->distinct();
        $this->db->from('game_resorts');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_resorts.id_resort = created_buildings_tbl.id_resort', 'inner');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '1');
        $this->db->where('created_buildings_tbl.id_building', '1');
        $this->db->where('players_tbl.vacation_mode !=', '1');
        $query = $this->db->get();
        return $query;
    }
   
    protected function check_vacation_mode($list_all_resorts){
        
        
$set_to_vacation_mode = '';

        $two_weeks_ago = strtotime('-14 days', $this->todays_time);
        foreach ($list_all_resorts->result() as $resort_info) {
            if ($resort_info->last_connection <= $two_weeks_ago && $resort_info->vacation_mode == '0') {
                $set_vacation_mode = $this->set_vacation_mode($resort_info->id_player);
                if ($set_vacation_mode == 1) {
                    $this->logToFile($this->Log_filename, "INFO", "[ ]", "check_vacation_mode", "Player ".$resort_info->id_player." was set to vacation mode\n");
                    $player_preferred_lang = $this->users_model->get_user_preferred_lang($resort_info->id_player);
                    $this->lang->load('logs',$player_preferred_lang);
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $resort_info->id_player, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['set_to_vacation_mode']) );   // Add a log row to the game_player_logs table  
                    $log_user_action = log_user_action( array('id_player' => $resort_info->id_player, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['set_to_vacation_mode']) );   // Add a log row to the game_player_logs table  
                    
                    $this->email_vacation_mode($resort_info->username, $resort_info->email, $player_preferred_lang); 
                         
                }
                else {
                    $this->logToFile($this->Log_filename, "WARN", "[ ]", "check_vacation_mode", "Player ".$resort_info->id_player." was not set to vacation mode, failed with error: ".$set_to_vacation_mode."\n");
                }
            }
        }
    }
    
    
    
    
    
    
    protected function tax_calculation($calculateTaxOnAmount)   {
        $getTaxCharge = array(
                        array(
                            'amount_range_from' => 0,
                            'amount_range_to' => 10000,
                            'tax_percentage' => 0,
                             ),
                        array(
                            'amount_range_from' => 10001,
                            'amount_range_to' => 50000,
                            'tax_percentage' => 0.5,
                             ),
                        array(
                            'amount_range_from' => 50001,
                            'amount_range_to' => 200000,
                            'tax_percentage' =>2,
                             ),
                        array(
                            'amount_range_from' => 200001,
                            'amount_range_to' => 500000,
                            'tax_percentage' =>5,
                             ),
                        array(
                            'amount_range_from' => 500001,
                            'amount_range_to' => 1000000,
                            'tax_percentage' =>15,
                             ),
                        array(
                            'amount_range_from' => 1000001,
                            'amount_range_to' => 2500000,
                            'tax_percentage' =>30,
                             ),
                        array(
                            'amount_range_from' => 2500001,
                            'amount_range_to' => 5000000,
                            'tax_percentage' =>50,
                             ),
                        array(
                            'amount_range_from' => 5000001,
                            'amount_range_to' => 10000000,
                            'tax_percentage' =>70,
                             ),
                        array(
                            'amount_range_from' => 10000001,
                            'amount_range_to' => 25000000,
                            'tax_percentage' =>80,
                             ),
                        array(
                            'amount_range_from' => 25000001,
                            'amount_range_to' => 100000000,
                            'tax_percentage' =>90,
                             ),
                        array(
                            'amount_range_from' => 100000001,
                            'amount_range_to' => 1000000000,
                            'tax_percentage' => 95,
                             )

                    );
        $remainingAmount      = $calculateTaxOnAmount;
        $amount               = $calculateTaxOnAmount;
        $arrayAmount          = array();
        foreach ($getTaxCharge as $key => $value) {
            $resultArray = array();
            if ($calculateTaxOnAmount > $value['amount_range_to']) {
                $sum                       = $value['amount_range_to'] - $value['amount_range_from'];
                $resultArray['amount']     = $sum;
                $resultArray['percentage'] = $value['tax_percentage'];
                array_push($arrayAmount, $resultArray);
                $remainingAmount = $remainingAmount - $sum;
            } else {
                $resultArray['amount']     = $remainingAmount;
                $resultArray['percentage'] = $value['tax_percentage'];
                array_push($arrayAmount, $resultArray);
                break;
            }
        }
        $resultantTaxAmount = 0;
        foreach ($arrayAmount as $key => $value) {
            $cal                = (($value['amount'] * $value['percentage']) / 100);
            $resultantTaxAmount = $resultantTaxAmount + $cal;
        }
        //echo 'amount: '.$calculateTaxOnAmount.' tax: '.$resultantTaxAmount.'<br>';
        return $resultantTaxAmount;
     }
    
    // CALCULATES AND REMOVE TAXE FROM BALANCE, BASED ON REVENUES....
    protected function take_cost_taxes($list_all_opened_resorts){
        
        $today = strtotime('now');
        $sevenDaysAgo = strtotime('-7 days', $today);
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);
        $today_GMT = gmdate('Y-m-d', $today);
        $yesterday = strtotime('-1 day', $today);
        $today_GMT = gmdate('Y-m-d', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);
        
        foreach ($list_all_opened_resorts->result() as $list_all_opened_resorts_Array){
            $current_resort = $list_all_opened_resorts_Array->id_resort;
            $revenue_yesterday = $this->finances_model->get_lastXdays_specific_amount_DB($current_resort, 'revenue', $yesterday_GMT, $today_GMT);
            $rev_achievements_yesterday = $this->finances_model->get_lastXdays_specific_amount_DB($current_resort, 'rev_achievements', $yesterday_GMT, $today_GMT);
            $expenses_yesterday = $this->finances_model->get_lastXdays_specific_amount_DB($current_resort, 'expenses', $yesterday_GMT, $today_GMT);
            $cost_purchases_yesterday = $this->finances_model->get_lastXdays_specific_amount_DB($current_resort, 'cost_purchases', $yesterday_GMT, $today_GMT);
            $benefit_without_achievements = ($revenue_yesterday - $rev_achievements_yesterday - ($expenses_yesterday - $cost_purchases_yesterday )) / 1;

            if ($benefit_without_achievements > 0) {
                $taxes_to_pay_for_yesterday = $this->tax_calculation($benefit_without_achievements);
                $real_benefit = $benefit_without_achievements - $taxes_to_pay_for_yesterday;
                
                $take_cost_query = $this->take_cost_DB($current_resort, $taxes_to_pay_for_yesterday);
                // Adds the cost to the cost table
                $add_cost_history_query = add_cost_stat_table($current_resort, $taxes_to_pay_for_yesterday, 'cost_taxes', $this->yesterdays_date);
                $add_cost_history_query2 = add_cost_stat_table($current_resort, $taxes_to_pay_for_yesterday, 'expenses', $this->yesterdays_date);
                $formatted_daily_payment = number_format($taxes_to_pay_for_yesterday, 0, '.', ' ');
                $formatted_benefit_without_achievements = number_format($benefit_without_achievements, 0, '.', ' ');
                if ($take_cost_query) {
                    $message_pay_taxes = $this->lang->line('logs')['taxes_accounted']." ".$formatted_daily_payment.' € '.$this->lang->line('logs')['of_yesterdays_revenues']." (".$formatted_benefit_without_achievements." €) for resort ID ".$current_resort."\n";
                    $this->logToFile($this->Log_filename, "INFO", "[current_resort_".$current_resort."]", "pay_taxes", $message_pay_taxes);
                    $player_preferred_lang = $list_all_opened_resorts_Array->preferred_lang;
                    $currentUserID = $list_all_opened_resorts_Array->id_player;
                    $this->lang->load('logs',$player_preferred_lang);
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['taxes'], 'data' => $this->lang->line('logs')['taxes_accounted']." ".$formatted_daily_payment.' € '.$this->lang->line('logs')['of_yesterdays_revenues']." (".$formatted_benefit_without_achievements." €).") );   // Add a log row to the game_player_logs table  
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['taxes'], 'data' => $this->lang->line('logs')['taxes_accounted']." ".$formatted_daily_payment.' € '.$this->lang->line('logs')['of_yesterdays_revenues']." (".$formatted_benefit_without_achievements." €).") );   // Add a log row to the game_player_logs table  
                }
                else {
                    $message_pay_taxes = "Something went wrong with taking taxes from resort ".$current_resort." (".$formatted_daily_payment." € out of ".$formatted_benefit_without_achievements.")\n";
                    $this->logToFile($this->Log_filename, "WARN", "[current_resort_".$current_resort."]", "pay_taxes", $message_pay_taxes);
                }
            }
            else {
                $message_pay_taxes = "Resort ".$current_resort." - Revenues: ".number_format($benefit_without_achievements, 0, '.', ' ')."€ has no taxes to pay.\n";
                $this->logToFile($this->Log_filename, "INFO", "[current_resort_".$current_resort."]", "pay_taxes", $message_pay_taxes);
            }
        }
    }
    
    
    
    
    
    protected function set_vacation_mode($id_player){
        $this->db->trans_start();
        $this->db->set('vacation_mode', '1');  
        $this->db->where('id_player', $id_player);                     
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();

        // Close the Tourist Information Center so the resort is fully paused during vacation.
        // This ensures disable_vacation_mode() can reliably reopen it on the player's return.
        if ($updated_rows > 0) {
            $resort = $this->db->select('id_resort')->from('game_resorts')->where('id_player', $id_player)->get()->row();
            if ($resort) {
                $this->db->set('id_status', '2');
                $this->db->where('id_resort', $resort->id_resort);
                $this->db->where('id_building', '1');
                $this->db->where('id_status', '1'); // only close if currently Open
                $this->db->update('game_created_buildings');
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
       
            
    protected function list_all_vacation_mode_players(){
        $this->db->select('*');
        $this->db->from('game_players'); 
        $this->db->where('vacation_mode', '1');
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * visitor_calculations                     Runs several sub-functions to calculate the number of visitor generated by different parts of the game
     * Visitors are generated by:   SLOPES
     *                              INSTRUCTORS
     *                              BUILDINGS?????
     * 
     * @param type $list_all_opened_resorts     All open resorts of the game
     * @return type                             Returns an array of daily visitors for each player ID and also skipass prices
     */
    protected function visitor_calculations($list_all_opened_resorts, $weather_condition){
        $info_message = '';
        $array_daily_visitors = [];
        // For each open resort
        foreach ($list_all_opened_resorts->result() as $list_all_opened_resorts_Array){
            // Some variables to initialize
            $total_visitors ='0';
            $current_resort = $list_all_opened_resorts_Array->id_resort;
            $skipass_daily = $list_all_opened_resorts_Array->skipass_daily;
            $skipass_weekly = $list_all_opened_resorts_Array->skipass_weekly;
            $vip_pass_price      = isset($list_all_opened_resorts_Array->vip_pass_price)      ? (int)$list_all_opened_resorts_Array->vip_pass_price      : 0;
            $family_discount_pct = isset($list_all_opened_resorts_Array->family_discount_pct) ? (int)$list_all_opened_resorts_Array->family_discount_pct : 0;
            $group_discount_pct  = isset($list_all_opened_resorts_Array->group_discount_pct)  ? (int)$list_all_opened_resorts_Array->group_discount_pct  : 0;
                        
            // Calculation for each item
            // Visitor calculation thanks to slopes - returns an array with 'visitors_all_slopes' 'regular_slopes_visitors' and 'crosscountry_visitors'
            $visitors_slopes_array = $this->calc_visitors_slopes('slope', $current_resort, $skipass_daily, $skipass_weekly);   // Calculate visitors for the slopes

            // If the weather conditions are Sunny, we add 20% visitors
            if ($weather_condition == 'Sunny')
                $bonus_weather = 1.2;
            else if ($weather_condition == 'Snowing')
                $bonus_weather = 0.9;
            else if ($weather_condition == 'Raining')
                $bonus_weather = 0.8;
            else
                $bonus_weather = 1;
            
            // Get bonus given by marketing campaign
            $bonus_marketing = 1+($this->bonus_marketing_fn($current_resort)/100);   // Retrieve the current marketing bonus

            // Get penalty from competitor resorts (0–20 % visitor reduction)
            $competitor_penalty = 1 - ($this->competitors_model->get_competitor_penalty($current_resort) / 100);
            // Reputation-based demand multiplier: higher reputation attracts more visitors
            $resort_reputation = $this->get_resort_reputation_DB($current_resort);
            $bonus_reputation = min(1.0 + ($resort_reputation / 10000), 1.5);   // +1% per 100 reputation, capped at +50% (at 5000 reputation)

            // Peak-season / holiday demand curve: demand varies by day within the 135-day season
            $day_of_season = get_day_of_season($current_resort);
            $bonus_peak_season = calc_peak_season_bonus((int)$day_of_season);

            // End of sub-functions
            
            // Cap the combined multiplier so that stacking bonuses (weather + marketing +
            // reputation + peak-season) cannot produce unrealistically large visitor counts.
            // Without a cap, a perfect storm of all bonuses can reach ~3.5×, pushing well-
            // developed resorts into tens of thousands of visitors per day. Capping at 2.5×
            // keeps peak-day counts realistic while still rewarding good resort management.
            $combined_multiplier = min($bonus_weather * $bonus_marketing * $bonus_reputation * $bonus_peak_season, 2.5);

            // All slope's visitors – random variation widened to ±12 % (was ±5 %) to produce
            // more natural day-to-day fluctuation in visitor counts.
            $daily_rand_regular      = mt_rand(0.88 * 1000, 1.12 * 1000) / 1000;
            $daily_rand_crosscountry = mt_rand(0.88 * 1000, 1.12 * 1000) / 1000;
            $total_visitors_regular      = round($combined_multiplier * ($visitors_slopes_array['regular_slopes_visitors'])  * $daily_rand_regular);
            $total_visitors_crosscountry = round($combined_multiplier * ($visitors_slopes_array['crosscountry_visitors']) * $daily_rand_crosscountry);
            
            
            $number_downhill_slopes = $this->get_number_build_slopes_type($current_resort, 1);
            $number_snowpark_slopes = $this->get_number_build_slopes_type($current_resort, 2);
            $number_bordercross_slopes = $this->get_number_build_slopes_type($current_resort, 3);
            $number_crosscountry_slopes = max($this->get_number_build_slopes_type($current_resort, 4), 0);
            $number_luge_slopes = $this->get_number_build_slopes_type($current_resort, 5);
           
            $number_regular_slopes = max($number_downhill_slopes + $number_snowpark_slopes + $number_bordercross_slopes + $number_luge_slopes, 0);
            
            if ($number_regular_slopes > 0)
                $ratio_visitors_per_regular_slope = round($total_visitors_regular / $number_regular_slopes);
            else
                $ratio_visitors_per_regular_slope = 1;
            
            if ($number_crosscountry_slopes > 0)
                $ratio_visitors_per_crosscountry_slope = round($total_visitors_crosscountry / $number_crosscountry_slopes);
            else
                $ratio_visitors_per_crosscountry_slope = 1;
            
            
            //var_dump($ratio_visitors_per_crosscountry_slope);
            
            switch ($ratio_visitors_per_regular_slope) :
                case $ratio_visitors_per_regular_slope <= 1000:
                    $total_visitors_regular_new = $total_visitors_regular;
                break;
                case $ratio_visitors_per_regular_slope <= 1500:
                    $total_visitors_regular_new = $total_visitors_regular * 0.8;
                break;
                case $ratio_visitors_per_regular_slope <= 2000:
                    $total_visitors_regular_new = $total_visitors_regular * 0.7;
                break;
                case $ratio_visitors_per_regular_slope > 2000:
                    $total_visitors_regular_new = $total_visitors_regular * 0.5;
                break;
            endswitch;
            
            switch ($ratio_visitors_per_crosscountry_slope) :
                case $ratio_visitors_per_crosscountry_slope <= 1000:
                    $total_visitors_crosscountry_new = $total_visitors_crosscountry;
                break;
                case $ratio_visitors_per_crosscountry_slope <= 1500:
                    $total_visitors_crosscountry_new = $total_visitors_crosscountry * 0.8;
                break;
                case $ratio_visitors_per_crosscountry_slope <= 2000:
                    $total_visitors_crosscountry_new = $total_visitors_crosscountry * 0.7;
                break;
                case $ratio_visitors_per_crosscountry_slope > 2000:
                    $total_visitors_crosscountry_new = $total_visitors_crosscountry * 0.5;
                break;
            endswitch;
                
            
            $total_visitors = $total_visitors_regular + $total_visitors_crosscountry;
            $total_visitors_new = round($total_visitors_regular_new + $total_visitors_crosscountry_new);

            // Climate change: apply visitor penalty based on climate level (reduced snow → fewer tourists)
            $climate = $this->climate_change_model->get_climate_data_DB($current_resort);
            if ($climate !== FALSE && (int)$climate->climate_level > 0) {
                // 2% visitor reduction per climate level (up to 20% at level 10)
                $climate_visitor_penalty = (int)$climate->climate_level * 0.02;
                // diversify_invest halves the penalty
                if ($climate->diversify_invest == 1)
                    $climate_visitor_penalty = $climate_visitor_penalty / 2;
                $total_visitors_new = round($total_visitors_new * (1 - $climate_visitor_penalty));
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "visitor_calculations", "Climate visitor penalty: -".(round($climate_visitor_penalty * 100, 1))."% applied. New total: ".$total_visitors_new."\n");
            }

            // Transportation: shuttle service attracts more visitors
            $transport = $this->transportation_model->get_settings_DB($current_resort);
            if ($transport && (int)$transport->shuttle_level > 0) {
                $transport_visitor_mult = 1.0 + ((int)$transport->shuttle_level * TRANSPORT_VISITOR_BONUS_PER_LEVEL);
                $total_visitors_new = round($total_visitors_new * $transport_visitor_mult);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "visitor_calculations", "Transport visitor bonus: +".(((int)$transport->shuttle_level * TRANSPORT_VISITOR_BONUS_PER_LEVEL) * 100)."% applied (shuttle level ".(int)$transport->shuttle_level."). New total: ".$total_visitors_new."\n");
            }
            // Apparel sponsorship: visitor count bonus
            $apparel_bonus_pct = $this->sponsorship_model->get_visitor_bonus_pct($current_resort);
            if ($apparel_bonus_pct > 0) {
                $total_visitors_new = (int)round($total_visitors_new * (1 + $apparel_bonus_pct));
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "visitor_calculations", "Apparel sponsor visitor bonus: +".(round($apparel_bonus_pct * 100, 1))."% applied. New total: ".$total_visitors_new."\n");
            }
            
            $add_daily_visitors_to_DB = $this->add_daily_stats_to_DB($current_resort, $total_visitors_new, 'affluence');
            
            $info_message = "total_visitors: ".$total_visitors_new." for Resort ID ".$current_resort." (Old: ".$total_visitors." - New: ".$total_visitors_new."), combined_multiplier (capped): ".$combined_multiplier.", bonus weather: ".$bonus_weather.", bonus marketing: ".$bonus_marketing.", bonus reputation: ".$bonus_reputation." (rep: ".$resort_reputation."), bonus peak season: ".$bonus_peak_season." (day: ".$day_of_season.")). ratio_visitors_per_regular_slope: ".$ratio_visitors_per_regular_slope." ratio_visitors_per_crosscountry_slope: ".$ratio_visitors_per_crosscountry_slope."\n";
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_total_visitors_".$total_visitors_new."]", "visitor_calculations", $info_message);
            
            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $data_achievement = array (
                'currentUserID' => $currentUserID,
                'type' => 'visitors',     
                'quantity' => $total_visitors_new       
            ); 
            $call_achievements_check = call_achievements_check($data_achievement, 'total_amount');   // Check if a corresponding achievement should be updated  
            $call_achievements_check = call_achievements_check($data_achievement, 'single_amount');   // Check if a corresponding achievement should be updated  
        
            $prestige_bonus = calculate_prestige_bonus($current_resort);

            // Update seasonal objectives progress
            $current_season = get_current_season($current_resort);
            if ($current_season) {
                $this->seasonal_objectives_model->init_season_objectives($current_resort, $current_season);
                $this->seasonal_objectives_model->increment_visitor_count($current_resort, $current_season, $total_visitors_new);
                $resort_reputation = $this->db->select('reputation')->from('game_resorts')->where('id_resort', $current_resort)->limit(1)->get()->row('reputation');
                $rep_target = $this->seasonal_objectives_model->get_objective_by_key('maintain_reputation');
                if ($rep_target) {
                    $this->seasonal_objectives_model->check_reputation($current_resort, $current_season, (int) $resort_reputation, (int) $rep_target->target_value);
                }
            }
            
            // Guest AI: save per-slope attractiveness scores for today
            $this->save_guest_ai_scores(
                $current_resort,
                (int)$total_visitors_regular_new,
                (int)$ratio_visitors_per_regular_slope,
                (int)$skipass_daily,
                (int)$number_regular_slopes
            );

            // Visitor Needs: save hunger/fatigue/warmth/fun scores for today
            $this->save_visitor_needs_scores($current_resort, $number_regular_slopes);
            
            // Build an array containing the daily visitors stats for each player (open resort)
            $array_daily_visitors[] = array ('id_resort' => $current_resort, 'daily_visitors' => $total_visitors_new, 'daily_visitors_not_maxed' => $total_visitors, 'daily_visitors_regular' => $total_visitors_regular_new, 'daily_visitors_crosscountry' => $total_visitors_crosscountry_new, 'skipass_daily' => $skipass_daily, 'skipass_weekly' => $skipass_weekly, 'prestige_bonus' => $prestige_bonus, 'ratio_visitors_per_regular_slope' => $ratio_visitors_per_regular_slope, 'ratio_visitors_per_crosscountry_slope' => $ratio_visitors_per_crosscountry_slope, 'bonus_reputation' => $bonus_reputation, 'bonus_peak_season' => $bonus_peak_season, 'resort_reputation' => $resort_reputation, 'day_of_season' => $day_of_season, 'vip_pass_price' => $vip_pass_price, 'family_discount_pct' => $family_discount_pct, 'group_discount_pct' => $group_discount_pct);
        }
        return $array_daily_visitors;
    }

    /**
     * get_resort_reputation_DB     Retrieves the current reputation of a resort
     *
     * @param int $id_resort    Resort ID
     * @return int              Reputation value (0 or more)
     */
    protected function get_resort_reputation_DB($id_resort) {
        $this->db->select('reputation');
        $this->db->from('game_resorts');
        $this->db->where('id_resort', $id_resort);
        $query = $this->db->get();
        return (int)($query->row('reputation') ?? 0);
    }

    /**
     * calc_peak_season_bonus   Delegates to the global helper calc_peak_season_bonus().
     *                          Kept as a public method for backward compatibility and testability.
     *
     * @param int $day_of_season
     * @return float
     */
    public function calc_peak_season_bonus($day_of_season) {
        return calc_peak_season_bonus($day_of_season);
    }

    /**
     *                      The snow change is the same for all resorts
     * 
     * @param type $list_all_opened_resorts     All open resorts of the game
     * @param type $snow_change                 AMount of snow to add or remove
     * @return type                            
     */
    protected function snow_change_fn($list_all_resorts, $snow_change, $weather){
        
        $count_snow_updated = 0;
        // For each resort
        foreach ($list_all_resorts->result() as $resort_info) {
            if ($resort_info->vacation_mode == '0') {
                $id_resort = $resort_info->id_resort;

                // Climate change: reduce positive snowfall based on climate level
                $effective_snow_change = $snow_change;
                if ($snow_change > 0) {
                    $climate = $this->climate_change_model->get_climate_data_DB($id_resort);
                    if ($climate !== FALSE) {
                        $penalty = CLIMATE_SNOW_PENALTY_PER_LEVEL * (int)$climate->climate_level;
                        // altitude investment halves the penalty
                        if ($climate->altitude_invest == 1)
                            $penalty = (int)round($penalty / 2);
                        $effective_snow_change = max(0, $snow_change - $penalty);
                        if ($effective_snow_change < $snow_change)
                            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$id_resort."]", "snow_change_fn", "Climate snow penalty: reduced snowfall from ".$snow_change." to ".$effective_snow_change." cm.\n");
                    }
                }

                $add_remove_snow = $this->add_remove_snow_db($effective_snow_change, $id_resort);
                $altitude = isset($resort_info->altitude) ? $resort_info->altitude : 'medium';
                $aspect   = isset($resort_info->aspect)   ? $resort_info->aspect   : 'north';
                $effective_snow = $this->apply_altitude_aspect_modifier($snow_change, $altitude, $aspect);
                $add_remove_snow = $this->add_remove_snow_db($effective_snow, $resort_info->id_resort);
                if ($add_remove_snow == true) {
                    $count_snow_updated ++;
                }
            }
        }
     
        $result = "Amount of snow added/removed to each resort: ".$snow_change." cm (".$weather.") for ".$count_snow_updated." resorts.\n";
        $this->logToFile($this->Log_filename, "INFO", "[".$weather."]", "snow_change_fn", $result);

    }
    
    /**
     * apply_altitude_aspect_modifier   Adjusts a raw snow change value based on resort altitude and aspect.
     *
     * Altitude effects:
     *   low    – accumulation × 0.8 ; melt × 1.2
     *   medium – baseline (× 1.0)
     *   high   – accumulation × 1.2 ; melt × 0.8
     *
     * Aspect effects (melt only – aspect does not alter snowfall):
     *   north  – melt × 0.8  (retains snow)
     *   south  – melt × 1.3  (melts faster)
     *   east / west – × 1.0
     *
     * @param int    $snow_change  Raw global snow change in cm (positive = snow, negative = melt)
     * @param string $altitude     'low' | 'medium' | 'high'
     * @param string $aspect       'north' | 'south' | 'east' | 'west'
     * @return int                 Adjusted snow change (rounded to integer)
     */
    protected function apply_altitude_aspect_modifier($snow_change, $altitude, $aspect) {
        if ($snow_change == 0) {
            return 0;
        }

        $alt_accum = ['low' => 0.8, 'medium' => 1.0, 'high' => 1.2];
        $alt_melt  = ['low' => 1.2, 'medium' => 1.0, 'high' => 0.8];
        $asp_melt  = ['north' => 0.8, 'south' => 1.3, 'east' => 1.0, 'west' => 1.0];

        $altitude_mod = $snow_change > 0
            ? ($alt_accum[$altitude] ?? 1.0)
            : ($alt_melt[$altitude]  ?? 1.0);

        $aspect_mod = $snow_change > 0 ? 1.0 : ($asp_melt[$aspect] ?? 1.0);

        return (int) round($snow_change * $altitude_mod * $aspect_mod);
    }

    /**
     * update_custom_slope_snow
     * Applies per-slope aspect modifiers to custom trails' individual snow levels.
     * North-facing: retains snow better (0.8× melt rate)
     * South-facing: loses snow fastest (1.3× melt rate)
     * East/West: neutral (1.0×)
     *
     * @param CI_DB_result $list_all_resorts
     * @param int          $daily_snow_change   Raw global snow change in cm
     */
    protected function update_custom_slope_snow($list_all_resorts, $daily_snow_change) {
        $asp_melt = ['north' => 0.8, 'south' => 1.3, 'east' => 1.0, 'west' => 1.0];

        foreach ($list_all_resorts->result() as $resort) {
            if ($resort->vacation_mode == '1') continue;

            $custom_slopes = $this->db
                ->select('id_created_slopes, aspect, slope_snow_level, id_status')
                ->from('game_created_slopes')
                ->where('id_resort', $resort->id_resort)
                ->where('is_custom', 1)
                ->where('id_status !=', 4)
                ->get();

            if ($custom_slopes->num_rows() == 0) continue;

            foreach ($custom_slopes->result() as $slope) {
                $aspect_mod = $daily_snow_change > 0
                    ? 1.0
                    : ($asp_melt[$slope->aspect] ?? 1.0);

                $snow_change = (int) round($daily_snow_change * $aspect_mod);
                $new_snow    = max(0, min(300, (int) $slope->slope_snow_level + $snow_change));

                $this->db->set('slope_snow_level', $new_snow)
                         ->where('id_created_slopes', $slope->id_created_slopes)
                         ->update('game_created_slopes');
            }
        }

        $this->logToFile($this->Log_filename, "INFO", "[ ]", "update_custom_slope_snow", "Custom slope snow updated (daily change: ".$daily_snow_change."cm).\n");
    }

    /**
    protected function add_remove_snow_db($snow_change, $id_resort){
        if ($snow_change != 0) { // Only if different from 0, to avoid useless queries
            $data_show_snow_level = $this->show_snow_level($id_resort);
            $new_snow_level = $data_show_snow_level + $snow_change;
            $new_snow_level = max($new_snow_level, 0);              // floor at 0
            $new_snow_level = min($new_snow_level, MAX_SNOW_LEVEL); // cap at MAX_SNOW_LEVEL
            //echo '$data_show_snow_level1 : '.$data_show_snow_level1;
            $this->db->trans_start();
            $this->db->set('snow_level', $new_snow_level);      
            $this->db->where('id_resort', $id_resort);                    
            $this->db->update('game_resorts');
            $updated_rows = $this->db->affected_rows();
            $this->db->trans_complete();
        }
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * apply_seasonal_melt  Applies natural snow melt based on the current day
     *                      of the season for each resort.  Melt only begins in
     *                      the late and closing phases (days 91–135) to simulate
     *                      rising spring temperatures.  Altitude reduces melt:
     *                      high resorts keep snow longer than low-altitude ones.
     *
     * @param CI_DB_result $list_all_resorts  All active resort rows
     * @return void
     */
    protected function apply_seasonal_melt($list_all_resorts) {
        foreach ($list_all_resorts->result() as $resort_info) {
            if ($resort_info->vacation_mode == '0') {
                $id_resort   = $resort_info->id_resort;
                $day         = $this->users_model->get_day_of_season($id_resort);
                $melt_base   = calc_seasonal_melt_rate($day);

                if ($melt_base <= 0) {
                    continue;  // No seasonal melt needed for this resort
                }

                // Altitude modifier: high resorts melt slower, low resorts faster
                $altitude = isset($resort_info->altitude) ? $resort_info->altitude : 'medium';
                $alt_mult = ['low' => 1.2, 'medium' => 1.0, 'high' => 0.7];
                $melt_cm  = (int) round($melt_base * ($alt_mult[$altitude] ?? 1.0));

                if ($melt_cm <= 0) {
                    continue;
                }

                // Apply negative snow change (melt)
                $this->add_remove_snow_db(-$melt_cm, $id_resort);

                $this->logToFile(
                    $this->Log_filename, "INFO",
                    "[id_resort_{$id_resort}]",
                    "apply_seasonal_melt",
                    "Day {$day} of season – natural melt: -{$melt_cm} cm (altitude: {$altitude}).\n"
                );

                // Notify the player about seasonal melt
                $currentUserID = $resort_info->id_player;
                $player_lang   = $resort_info->preferred_lang ?: 'english';
                $this->lang->load('logs', $player_lang);
                $this->logs_model->call_notification_DB(array(
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['weather'],
                    'data'      => $this->lang->line('logs')['seasonal_melt_log'] . $melt_cm . ' cm.',
                ));
                log_user_action(array(
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['weather'],
                    'data'      => $this->lang->line('logs')['seasonal_melt_log'] . $melt_cm . ' cm.',
                ));
            }
        }
    }

    /**         
     * revenue_visitors                         Calculates the generated revenues according to the number of visitors today
     * 
     * @param type $list_all_opened_resorts     List of all open resorts
     * @param type $array_daily_visitors        Array of daily visitors for each player ID and also skipass prices
     * @return string                           Returns result's info message
     */
    protected function revenue_visitors($array_daily_visitors){
        //$info_message = '';
        //
        $today = strtotime('now');
        $yesterday = strtotime('-1 day', $today);
        $today_GMT = gmdate('Y-m-d', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);
            
        // For each open resort
        foreach ($array_daily_visitors as $list_all_opened_resorts_Array){
            $skipass_daily = $list_all_opened_resorts_Array['skipass_daily'];
            $skipass_weekly = $list_all_opened_resorts_Array['skipass_weekly'];
            $total_visitors = $list_all_opened_resorts_Array['daily_visitors'];
            $total_visitors_not_maxed = $list_all_opened_resorts_Array['daily_visitors_not_maxed'];
            $current_resort = $list_all_opened_resorts_Array['id_resort'];
            $prestige_bonus = $list_all_opened_resorts_Array['prestige_bonus'];
            $ratio_visitors_per_regular_slope = $list_all_opened_resorts_Array['ratio_visitors_per_regular_slope'];
            $ratio_visitors_per_crosscountry_slope = $list_all_opened_resorts_Array['ratio_visitors_per_crosscountry_slope'];
            $vip_pass_price      = $list_all_opened_resorts_Array['vip_pass_price']      ?? 0;
            $family_discount_pct = $list_all_opened_resorts_Array['family_discount_pct'] ?? 0;
            $group_discount_pct  = $list_all_opened_resorts_Array['group_discount_pct']  ?? 0;

            // We consider 65% of weekly skipass and 35% of daily skipass
            // src: http://www.rtl.fr/sport/ski-la-frequentation-des-pistes-reste-la-meme-malgre-la-crise-7767685336

            // --- Dynamic pricing: effective daily price and demand multiplier ---
            // Family discount: a share of daily visitors get a discounted price, but attract
            // more families (demand bonus = FAMILY_DISCOUNT_DEMAND_BONUS % per 1% discount).
            $family_demand_multiplier = 1.0 + (FAMILY_DISCOUNT_DEMAND_BONUS * $family_discount_pct / 100.0);
            $effective_daily_price    = (float)$skipass_daily * (1.0 - FAMILY_VISITOR_FRACTION * $family_discount_pct / 100.0);

            // Group discount: a share of daily visitors get a group rate, attracting more bookings.
            // Applies an additional price reduction over the family-adjusted price.
            $group_demand_multiplier  = 1.0 + (GROUP_DISCOUNT_DEMAND_BONUS * $group_discount_pct / 100.0);
            $effective_daily_price    = $effective_daily_price * (1.0 - GROUP_VISITOR_FRACTION * $group_discount_pct / 100.0);

            // VIP pass: VIP_VISITOR_FRACTION of daily visitors pay the premium VIP price.
            if ($vip_pass_price > $skipass_daily) {
                $effective_daily_price += VIP_VISITOR_FRACTION * ($vip_pass_price - $skipass_daily);
            }

            // Adjusted daily visitors reflect the family and group demand boosts.
            $effective_daily_visitors = $total_visitors * 0.35 * $family_demand_multiplier * $group_demand_multiplier;

            // Apply guest skill revenue multiplier (advanced guests spend more)
            $guest_skill_multiplier = $this->guest_skill_model->get_revenue_multiplier($current_resort);

            $revenue_visitors_before_prestige_bonus = round((($effective_daily_visitors * $effective_daily_price) + ($total_visitors * 0.65 * $skipass_weekly / 7)) * $guest_skill_multiplier);
            
            // Adds bonus give by prestige
            $revenue_visitors = round($revenue_visitors_before_prestige_bonus * $prestige_bonus['coef']);
                    
            $prestige_revenue_today = $revenue_visitors - $revenue_visitors_before_prestige_bonus;
            $add_revenue_history_query_specific_table = add_revenue_stat_table($current_resort, $prestige_revenue_today, 'prestige_gains', $yesterday_GMT);
            
            //if ($current_resort == 2)
               // echo '$prestige_revenue_today: '.$prestige_revenue_today;
            
            if($revenue_visitors < 0)
                $revenue_visitors = 0;
            
            $add_revenue_visitors = $this->add_revenue_DB($current_resort, $revenue_visitors);
            // Adds the revenue to the revenue table
            $add_revenue_history_query_main_table = add_revenue_stat_table($current_resort, $revenue_visitors, 'revenue', $yesterday_GMT);
            // Adds revenue to the revenue table for ski pass
            $add_revenue_history_query_specific_table = add_revenue_stat_table($current_resort, $revenue_visitors, 'rev_skipass', $yesterday_GMT);
            
            $info_message = "Resort ".$current_resort." earned ".number_format($revenue_visitors, 0, '.', ' ')." € thanks to the ski passes\n";
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_visitors", $info_message);

            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs',$player_preferred_lang);
            
            $data_log_visitors = $total_visitors.$this->lang->line('logs')['visitors_today'].number_format($revenue_visitors, 0, '.', ' ').'€ ('.$this->lang->line('logs')['skipasses'].').';
            $data_log_crowded = $this->lang->line('logs')['slopes_too_crowded'].' '.$ratio_visitors_per_regular_slope.' '.$this->lang->line('logs')['and_the_one_crosscountry'].' '.$ratio_visitors_per_crosscountry_slope.'.<br>'.$this->lang->line('logs')['try_to_keep_below_1000'];
            
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $data_log_visitors) );   // Add a log row to the game_player_logs table 
            
            if ($total_visitors_not_maxed > $total_visitors) { // If visitors were maxed out (slopes too crowded)
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['slope'], 'data' => $data_log_crowded) );   // Add a log row to the game_player_logs table  
                
                // START Adds reporting data to DB
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'slope', $data_log_crowded);
                // END Adds reporting data to DB
                
            }
            
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $data_log_visitors) );   // Add a log row to the game_player_logs table   
            
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['slope'], 'data' => $data_log_crowded) );   // Add a log row to the game_player_logs table      
            
            $data_achievement = array (
                'currentUserID' => $currentUserID,
                'type' => 'skipass',     
                'quantity' => $revenue_visitors       
            ); 
            $call_achievements_check = call_achievements_check($data_achievement, 'total_amount');   // Check if a corresponding achievement should be updated  
            $call_achievements_check = call_achievements_check($data_achievement, 'single_amount');   // Check if a corresponding achievement should be updated  
            
        }
        //return $info_message;
    }
    
    /**
     * calc_visitors_slopes             Calculates the visitors generated by the Slopes
     * The visitor affluence depends on the slope quality and length
     * 
     * @param type $item_type           Type of item (always "slope" for now)
     * @param type $current_resort        Current resort ID
     * @param type $skipass_daily       Daily skipass price for the player
     * @param type $skipass_weekly      Weekly skipass price for the player
     * @return type                     Returns the number of daily visitors for this player ID (integer)
     */
    public function calc_visitors_slopes($item_type, $current_resort, $skipass_daily, $skipass_weekly){
        // For each player we run this function
        $visitors_all_slopes = '0';
        $cumul_lift_max_daily_attraction = '0';
        $daily_infrastructure_attration = '0';
        $num_deserved_slopes = '0';
        $daily_coef = '0';
        $weekly_coef = '0';
        $max_capacity_housing_access = '0';
        $get_created_lifts_and_generic = $this->get_created_lifts_and_generic($current_resort);   // only status = 1 (open)
        //echo $this->db->last_query();
        //echo ' Rows: '.$get_created_lifts_and_generic->num_rows().' for resort '.$current_resort.'.';
        
        $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $this->lang->load('logs',$player_preferred_lang);
        $this->lang->load('reporting',$player_preferred_lang);

        // Guest AI: retrieve resort snow level once for use in snow-quality coefficient
        $resort_snow_level = (int)$this->show_snow_level($current_resort);
        
        if ($get_created_lifts_and_generic->num_rows() > 0){                       // If open lifts
            //echo ' Open lifts for resort: '.$current_resort.'.';
            $num_open_lifts = $get_created_lifts_and_generic->num_rows();

            // AI Guest Flow Simulation: pre-calculate snow level and restaurant proximity factors
            $resort_snow_level = $this->show_snow_level($current_resort);
            $resort_snow_level = ($resort_snow_level !== null) ? (int)$resort_snow_level : 0;
            if ($resort_snow_level < 20) {
                $snow_factor = 0.7;
            } elseif ($resort_snow_level < 60) {
                $snow_factor = 0.8 + (($resort_snow_level - 20) / 40) * 0.2;  // 0.80 → 1.00
            } elseif ($resort_snow_level <= 120) {
                $snow_factor = 1.0 + (($resort_snow_level - 60) / 60) * 0.1;  // 1.00 → 1.10
            } else {
                $snow_factor = 1.1;
            }

            $restaurant_buildings_info = $this->get_info_created_buildings($current_resort, 'restaurant');
            $total_restaurant_count = 0;
            if ($restaurant_buildings_info->num_rows() > 0) {
                foreach ($restaurant_buildings_info->result() as $rb) {
                    $total_restaurant_count += $rb->count_level;
                }
            }
            $restaurant_factor = min(1.2, 1.0 + ($total_restaurant_count * 0.05));

            // Per-lift data collected for queue simulation
            $lift_attraction_data = [];
            $total_lift_throughput = 0;

            foreach ($get_created_lifts_and_generic->result() as $row_created_lifts_and_generic){   // For each open lift
                $visitors_this_lift = 0;
                $current_lift = $row_created_lifts_and_generic->id_created_lifts;    // The lift handled in this iteration
                $current_lift_generic_id = $row_created_lifts_and_generic->id_lift;    // The lift handled in this iteration
                $throughput_for_lift_per_hour = $row_created_lifts_and_generic->throughput;
                $current_lift_length = $row_created_lifts_and_generic->length;
                $deserved_slopes_info = get_deserved_slopes($current_resort, $current_lift, 'number');
                
                $total_condition = $deserved_slopes_info['total_condition'];
                $total_length = $deserved_slopes_info['total_length'];
                $num_deserved_slopes = $deserved_slopes_info['count'];
                $ratio_lift_length_deserved_distance = ($current_lift_length > 0) ? $total_length / $current_lift_length : 0;
                if ($num_deserved_slopes != 0) {
                    $avg_condition_slope = $total_condition / $num_deserved_slopes;
                    if ($avg_condition_slope <= 60){    // If average slope quality below 60, we consider a loss of money, add to report
                        // START Adds reporting data to DB
                        $reporting_data = $this->lang->line('reporting')['avg_quality_slopes'].' "'.$row_created_lifts_and_generic->custom_name.'" '.$this->lang->line('reporting')['is_only'].' '.$avg_condition_slope.'%. '.$this->lang->line('reporting')['improve_their_quality'];
                        $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'slope', $reporting_data);
                        // END Adds reporting data to DB
                    }
                }
                else {
                    $avg_condition_slope = '0';
                    // START Adds reporting data to DB
                    $reporting_data = $this->lang->line('reporting')['no_slopes_deserving'].' "'.$row_created_lifts_and_generic->custom_name.'". '.$this->lang->line('reporting')['build_more_slopes'];
                    $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'slope', $reporting_data);
                    // END Adds reporting data to DB
                }
                $lift_condition = $row_created_lifts_and_generic->lift_condition;
                $lift_daily_capacity = $throughput_for_lift_per_hour * 7 / 10;  // 7 hours a days, 10 validation per skier = capacity until overload

                if ($ratio_lift_length_deserved_distance <= 2) {   
                    $reporting_data = $this->lang->line('reporting')['too_few_slopes_connected'].' "'.$row_created_lifts_and_generic->custom_name.'". '.$this->lang->line('reporting')['build_more_slopes'];
                    $coef_lift = 0.7;
                }
                else if ($ratio_lift_length_deserved_distance > 2 && $ratio_lift_length_deserved_distance <= 3) {  
                    $reporting_data = $this->lang->line('reporting')['too_few_slopes_connected'].' "'.$row_created_lifts_and_generic->custom_name.'". '.$this->lang->line('reporting')['build_more_slopes'];
                    $coef_lift = 0.8;
                }
                else if ($ratio_lift_length_deserved_distance > 3 && $ratio_lift_length_deserved_distance <= 4) {
                    $reporting_data = $this->lang->line('reporting')['adequate_slopes_connected'].' "'.$row_created_lifts_and_generic->custom_name.'". '.$this->lang->line('reporting')['build_few_slopes'];
                    $coef_lift = 1;
                }
                else if ($ratio_lift_length_deserved_distance > 4) {
                    $reporting_data = $this->lang->line('reporting')['adequate_slopes_connected'].' "'.$row_created_lifts_and_generic->custom_name.'". '.$this->lang->line('reporting')['not_necessary_slopes'];
                    $coef_lift = 1.2;
                }
                else { 
                    $reporting_data = $this->lang->line('reporting')['unknown_error_lift'].' "'.$row_created_lifts_and_generic->custom_name.'".';                              
                    $coef_lift = 0;
                }
                // START Adds reporting data to DB
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'lift', $reporting_data);
                // END Adds reporting data to DB
                
                $this_lift_max_daily_attraction = round(($lift_daily_capacity * $coef_lift * (($lift_condition +100) / 200) * (($avg_condition_slope+100) / 200)),0);
                // Apply age-based efficiency drop
                $lift_age_seasons = $this->lift_age_seasons($row_created_lifts_and_generic->install_date);
                $age_efficiency_factor = max(0.0, 1 - $lift_age_seasons * LIFT_AGE_EFFICIENCY_DROP_PER_YEAR);
                $this_lift_max_daily_attraction = round($this_lift_max_daily_attraction * $age_efficiency_factor, 0);
                
                // Guest AI: apply difficulty and snow-quality coefficients
                // These make the visitor calculation data-driven: guests prefer
                // blue/intermediate slopes (id=2) and deep, well-groomed snow.
                $difficulty_coef   = $this->calc_difficulty_coef($current_resort, $current_lift);
                $snow_quality_coef = $this->calc_snow_quality_coef($resort_snow_level, (float)$avg_condition_slope);
                $this_lift_max_daily_attraction = round($this_lift_max_daily_attraction * $difficulty_coef * $snow_quality_coef, 0);
                
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['the_lift'].' "'.$row_created_lifts_and_generic->custom_name.'" '.$this->lang->line('reporting')['could_attract_max'].' '.$this_lift_max_daily_attraction.' '.$this->lang->line('reporting')['tourists'].'.';     
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'lift', $reporting_data);
                // END Adds reporting data to DB
                
                /*echo '$lift_daily_capacity: '.$lift_daily_capacity.'<br>';
                echo '$coef_lift: '.$coef_lift.'<br>';
                echo '$lift_condition: '.$lift_condition.'<br>';
                echo '$avg_condition_slope: '.$avg_condition_slope.'<br>';*/
                $cumul_lift_max_daily_attraction = round($cumul_lift_max_daily_attraction + $this_lift_max_daily_attraction, 0);
                // Collect per-lift data for AI Guest Flow queue simulation
                $lift_attraction_data[] = ['throughput' => $throughput_for_lift_per_hour, 'max_daily_attraction' => $this_lift_max_daily_attraction];
                $total_lift_throughput += $throughput_for_lift_per_hour;
                
            }
                
            // AI Guest Flow Simulation: queue-based redistribution
            // Guests prefer lifts with shorter queues; overloaded lifts (attraction demand exceeds
            // throughput share) lose some visitors as guests leave the queue.
            if ($cumul_lift_max_daily_attraction > 0 && $total_lift_throughput > 0) {
                $revised_cumul = 0;
                foreach ($lift_attraction_data as $ld) {
                    if ($ld['max_daily_attraction'] <= 0) {
                        continue;
                    }
                    $throughput_share = $ld['throughput'] / $total_lift_throughput;
                    $attraction_share = $ld['max_daily_attraction'] / $cumul_lift_max_daily_attraction;
                    // queue_ratio > 1 means this lift handles more visitors per capacity unit than average
                    $queue_ratio = ($throughput_share > 0) ? ($attraction_share / $throughput_share) : 1.0;
                    // Linear penalty: 0% at queue_ratio=1.0, up to 20% at queue_ratio=2.0+
                    $queue_efficiency = max(0.8, 1.0 - max(0.0, ($queue_ratio - 1.0)) * 0.2);
                    $revised_cumul += $ld['max_daily_attraction'] * $queue_efficiency;
                }
                $cumul_lift_max_daily_attraction = round($revised_cumul, 0);
            }

            // AI Guest Flow Simulation: apply snow level factor (best snow attracts more guests)
            // and restaurant proximity factor (closest restaurant keeps guests on-site longer)
            $cumul_lift_max_daily_attraction = round($cumul_lift_max_daily_attraction * $snow_factor * $restaurant_factor, 0);

            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('reporting')['up_to'].' '.$cumul_lift_max_daily_attraction.' '.$this->lang->line('reporting')['tourists'].' '.$this->lang->line('reporting')['can_be_handled_lifts_downhil_slopes'];     
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'lift', $reporting_data);
            // END Adds reporting data to DB

            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('reporting')['guest_flow_snow_factor'].' '.round($snow_factor, 2).'. '.$this->lang->line('reporting')['guest_flow_restaurant_factor'].' '.round($restaurant_factor, 2).'.';
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'lift', $reporting_data);
            // END Adds reporting data to DB
            
            
            
            // Crosscountry part - START
            $number_downhill_slopes = $this->get_number_build_slopes_type($current_resort, 1);
            $number_snowpark_slopes = $this->get_number_build_slopes_type($current_resort, 2);
            $number_bordercross_slopes = $this->get_number_build_slopes_type($current_resort, 3);
            $number_crosscountry_slopes = $this->get_number_build_slopes_type($current_resort, 4);
            $number_luge_slopes = $this->get_number_build_slopes_type($current_resort, 5);
           
            $number_regular_slopes = $number_downhill_slopes + $number_snowpark_slopes + $number_bordercross_slopes + $number_luge_slopes;
            
            $ratio_crosscountry_regular = ($number_regular_slopes > 0) ? $number_crosscountry_slopes / $number_regular_slopes : 0;
            
            
            $average_condition_crosscountry_slopes = $this->get_average_slope_type_condition($current_resort, 4);  // crosscountry
            
            if (!isset($average_condition_crosscountry_slopes))
                $average_condition_crosscountry_slopes = 0;
            
            $total_visitors_crosscountry = round($average_condition_crosscountry_slopes/100 * ($ratio_crosscountry_regular * $cumul_lift_max_daily_attraction / 2) , 0);
            
                
            $total_visitors_all_slopes_before_infrastructure = $total_visitors_crosscountry + $cumul_lift_max_daily_attraction;
                 
            // Crosscountry part - END
            
            // START Adds reporting data to DB
            if ($total_visitors_crosscountry > 0)
                $reporting_data = $total_visitors_crosscountry.' '.$this->lang->line('reporting')['tourists'].' '.$this->lang->line('reporting')['have_used_cross_country_slopes'];     
            else
                $reporting_data = $this->lang->line('reporting')['no_cross_country_clopes'];  
        
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'slope', $reporting_data);
            // END Adds reporting data to DB
            
            
            // Adjust affluence depending on access resort building levels
            // Gets the bonus (max_income column) of the access resort building for the current player
            $data_access_building = $this->get_info_created_buildings($current_resort, 'access');
            // In case there is no access building built, we initialize the bonus to 1 (no change)
            $access_resort_bonus = '1';
            if ($data_access_building->num_rows() > 0) {    // There is an access building built
                foreach ($data_access_building->result() as $data_access_building_array){
                    $access_resort_bonus = ($data_access_building_array->max_income/100)+1; // Bonus = value in (DB/100)+1, i.e: 15/100+1= 1.15
                }
            }
            
            // Hotel capacity calculations
            $hotel_capacity_info = $this->resort_model->get_hotel_capacity($current_resort);  
            
            // Default values if untouched below
            $hotel_capacity = 0;
            $max_capacity_housing_access = $total_visitors_all_slopes_before_infrastructure * 0.40;    // Only attracting 40% of the capacity if there is no hotel built
                
            if ($hotel_capacity_info->num_rows() > 0) {
                $hotel_capaticy_row = $hotel_capacity_info->row();
                if (isset($hotel_capaticy_row->total_capacity) && $hotel_capaticy_row->total_capacity != 0) {
                    $hotel_capacity = $hotel_capaticy_row->total_capacity;
                    $reporting_data = $this->lang->line('reporting')['your_resort_hotel_capacity'].' '.$hotel_capacity.' '.$this->lang->line('reporting')['tourists'].'.'; 
                    $max_capacity_housing_access = $hotel_capacity * $access_resort_bonus * (1 + PERC_TOURISTS_BUILDING['day_only']);   // 60% for people always coming for the day only (PERC_TOURISTS_DAY_ONLY = 0.6)    
                }
                else {
                    $reporting_data = $this->lang->line('reporting')['no_hotel_in_resort']; 
                }
            }
            else {
                $reporting_data = $this->lang->line('reporting')['no_hotel_in_resort']; 
            }
            
            // START Adds reporting data to DB    
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'capacity', $reporting_data);
            // END Adds reporting data to DB
            
            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('reporting')['thanks_to_access_resort_housing_capacity'].' '.$max_capacity_housing_access.' '.$this->lang->line('reporting')['tourists'].'. '.$this->lang->line('reporting')['also_includes'];     
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'capacity', $reporting_data);
            // END Adds reporting data to DB
            
            if ($max_capacity_housing_access > $total_visitors_all_slopes_before_infrastructure) {
                $daily_infrastructure_attration = $total_visitors_all_slopes_before_infrastructure;
                $reporting_data = $this->lang->line('reporting')['lift_capacity_too_low'];
            }
            else if ($max_capacity_housing_access <= $total_visitors_all_slopes_before_infrastructure) {
                $daily_infrastructure_attration = $max_capacity_housing_access;
                $reporting_data = $this->lang->line('reporting')['infrastructure_capacity_too_low'];
            }
            
            // START Adds reporting data to DB    
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'capacity', $reporting_data);
            // END Adds reporting data to DB
            // 
            // Get number of open slopes to estimate resort size
            $get_created_slopes_and_generic = $this->get_created_slopes_and_generic($current_resort, 1);   // only status = 1 (open)
            if ($get_created_slopes_and_generic->num_rows() > 0){                       // If open slopes
                $num_open_slopes = $get_created_slopes_and_generic->num_rows();
            }
            else {
                $num_open_slopes = '0';
            }
            // calculate the coefficient given by the daily price
            $daily_coef = $this->get_coef($num_open_slopes, $skipass_daily, 'daily');   
            // calculate the coefficient given by the weekly price
            $weekly_coef = $this->get_coef($num_open_slopes, $skipass_weekly, 'weekly');
            
            
            switch ($daily_coef) :
                case (0):
                    $message_daily_coef = 'not_opened';
                    $data_daily = $this->lang->line('logs')[$message_daily_coef];
                break;
                case $daily_coef <= 0.5:
                    $message_daily_coef = 'very_expensive';
                    $data_daily = $this->lang->line('logs')['daily_type'].$this->lang->line('logs')[$message_daily_coef];
                break;
                case $daily_coef <= 0.8:
                    $message_daily_coef = 'expensive';
                    $data_daily = $this->lang->line('logs')['daily_type'].$this->lang->line('logs')[$message_daily_coef];
                break;
                case $daily_coef <= 1.2:
                    $message_daily_coef = 'adequate';
                    $data_daily = $this->lang->line('logs')['daily_type'].$this->lang->line('logs')[$message_daily_coef];
                break;
                case $daily_coef <= 1.6:
                    $message_daily_coef = 'cheap';
                    $data_daily = $this->lang->line('logs')['daily_type'].$this->lang->line('logs')[$message_daily_coef];
                break;
                case $daily_coef <= 2:
                    $message_daily_coef = 'very_cheap';
                    $data_daily = $this->lang->line('logs')['daily_type'].$this->lang->line('logs')[$message_daily_coef];
                break;
            endswitch;
            switch ($weekly_coef) :
                case (0):
                    $message_weekly_coef = 'not_opened';
                    $data_weekly = $this->lang->line('logs')[$message_weekly_coef];
                break;
                case $weekly_coef <= 0.5:
                    $message_weekly_coef = 'very_expensive';
                    $data_weekly = $this->lang->line('logs')['weekly_type'].$this->lang->line('logs')[$message_weekly_coef];
                break;
                case $weekly_coef <= 0.8:
                    $message_weekly_coef = 'expensive';
                    $data_weekly = $this->lang->line('logs')['weekly_type'].$this->lang->line('logs')[$message_weekly_coef];
                break;
                case $weekly_coef <= 1.2:
                    $message_weekly_coef = 'adequate';
                    $data_weekly = $this->lang->line('logs')['weekly_type'].$this->lang->line('logs')[$message_weekly_coef];
                break;
                case $weekly_coef <= 1.6:
                    $message_weekly_coef = 'cheap';
                    $data_weekly = $this->lang->line('logs')['weekly_type'].$this->lang->line('logs')[$message_weekly_coef];
                break;
                case $weekly_coef <= 2:
                    $message_weekly_coef = 'very_cheap';
                    $data_weekly = $this->lang->line('logs')['weekly_type'].$this->lang->line('logs')[$message_weekly_coef];
                break;
            endswitch;
            
            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('logs')['weekly_type'].$this->lang->line('logs')[$message_weekly_coef];
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'skipass', $reporting_data);
            // END Adds reporting data to DB
            
            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('logs')['daily_type'].$this->lang->line('logs')[$message_daily_coef];
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'skipass', $reporting_data);
            // END Adds reporting data to DB
    
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $data_daily) );   // Add a log row to the game_player_logs table     
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $data_daily) );   // Add a log row to the game_player_logs table     
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $data_weekly) );   // Add a log row to the game_player_logs table     
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $data_weekly) );   // Add a log row to the game_player_logs table     
            
            // adding a coef depending on the skipass price
            $visitors_all_slopes_after_infrastructure = round($daily_infrastructure_attration * $daily_coef * $weekly_coef, 0);
        }
        else {  // No open lift = no visitors                       // If open lifts
            echo ' No open lifts for resort: '.$current_resort.'.';
            $visitors_all_slopes_after_infrastructure = 0;
            $total_visitors_all_slopes_before_infrastructure = 0;
            // START Adds reporting data to DB
            $reporting_data = $this->lang->line('reporting')['no_open_lifts'];
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'lift', $reporting_data);
            // END Adds reporting data to DB
        }
        
        if (isset($total_visitors_crosscountry) && $total_visitors_crosscountry > 0) { // Only if the resort has some visitors on the crosscountry slopes (if any built)
            $crosscountry_visitors_after_infrastructure = round($ratio_crosscountry_regular * $visitors_all_slopes_after_infrastructure, 0);
            $regular_slopes_visitors_after_infrastructure = round($visitors_all_slopes_after_infrastructure - $crosscountry_visitors_after_infrastructure, 0);
        }
        else {
            $crosscountry_visitors_after_infrastructure = 0;
            $regular_slopes_visitors_after_infrastructure = $visitors_all_slopes_after_infrastructure;
        }

        // Black Diamond / Extreme Zone: expert-guest visitor bonus
        $num_black_diamond_slopes = $this->get_number_open_black_diamond_slopes($current_resort);
        if ($num_black_diamond_slopes > 0) {
            $black_diamond_visitor_bonus = round($regular_slopes_visitors_after_infrastructure * BLACK_DIAMOND_VISITOR_BONUS * $num_black_diamond_slopes);
            $regular_slopes_visitors_after_infrastructure += $black_diamond_visitor_bonus;
            $visitors_all_slopes_after_infrastructure     += $black_diamond_visitor_bonus;
            // START Adds reporting data to DB
            $reporting_data = $num_black_diamond_slopes.' Black Diamond slope(s) attracted '.$black_diamond_visitor_bonus.' additional expert guests.';
            $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'slope', $reporting_data);
            // END Adds reporting data to DB
        }
        
        $visitors_after_infrastructure = [ 'visitors_all_slopes' => $visitors_all_slopes_after_infrastructure, 'regular_slopes_visitors' => $regular_slopes_visitors_after_infrastructure, 'crosscountry_visitors' => $crosscountry_visitors_after_infrastructure ];
            
       // echo 'Final affluence resort = '.$visitors_all_slopes_after_infrastructure.' (resort ID: '.$current_resort.' (x'.$daily_coef.' & x'.$weekly_coef.'))<br><br>';
        $info_message = "visitors_all_slopes: ".$visitors_all_slopes_after_infrastructure." (regular: ".$regular_slopes_visitors_after_infrastructure.", crosscountry: ".$crosscountry_visitors_after_infrastructure.") for Resort ID ".$current_resort." (Daily coef: ".$daily_coef." (price: ".$skipass_daily."), weekly coef: ".$weekly_coef." (price: ".$skipass_weekly."), Infrastructure: ".$daily_infrastructure_attration. " (Housing capacity: ".$max_capacity_housing_access.", Slopes (all types) max: ".$total_visitors_all_slopes_before_infrastructure."))\n";
        $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_slope_visitors_".$visitors_all_slopes_after_infrastructure."]", "calc_visitors_slopes", $info_message);
            
        return $visitors_after_infrastructure;
    }
    
    protected function bonus_marketing_fn($current_resort) {
        $this->db->select('affluence_bonus');
        $this->db->from('game_affluence_bonus');
        $this->db->where('id_resort', $current_resort);  
        $this->db->order_by('date', 'DESC');
        $this->db->limit('1');
        //$this->db->where('date', gmdate('Y-m-d', $this->todays_time));  
        $query = $this->db->get();
        if ($query->num_rows() > 0) {  
            $result = $query->row();
            $affluence_bonus = $result->affluence_bonus;
        }
        else 
            $affluence_bonus = 1;
        return $affluence_bonus;
    }
    
    protected function get_throughput_for_lift($id_group, $lift_level) {
        $this->db->select('throughput');
        $this->db->from('game_lifts');
        $this->db->where('id_group', $id_group);  
        $this->db->where('level', $lift_level);  
        $query = $this->db->get();
        $result = $query->row();
        $throughput = $result->throughput;
        return $throughput;
    }
    
    
    protected function get_level_for_slope($current_resort, $current_slope) {
        $this->db->select('level, id_group');
        $this->db->from('game_created_lifts');
        $where= '(deserved_slope_1 = '.$current_slope.' OR deserved_slope_2 = '.$current_slope.' OR deserved_slope_3 = '.$current_slope.')';
        $this->db->where($where);
        $this->db->where('id_status', '1');             // Only open lifts
        $this->db->where('game_created_lifts.id_resort', $current_resort);
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * calc_difficulty_coef     Returns a difficulty-based multiplier (0.85–1.05) for the
     *                          given lift's served slopes.
     *
     * Guest AI factor 1 – Difficulty
     * Slope difficulties: 1=Green (wide market), 2=Blue (optimal), 3=Red, 4=Black (niche).
     * The coefficient is centred on Blue (id=2) as the most broadly attractive difficulty.
     *
     * @param int $id_resort        Resort ID
     * @param int $id_created_lift  Created-lift ID
     * @return float                Multiplier in [0.85, 1.05]
     */
    protected function calc_difficulty_coef($id_resort, $id_created_lift) {
        // Weights per difficulty level (1=Green ... 4=Black)
        $difficulty_weights = [1 => 1.0, 2 => 1.05, 3 => 0.95, 4 => 0.85];

        $this->db->select('gs.id_difficulty')
            ->from('game_created_lifts gcl')
            ->join('game_created_slopes gcs',
                '(gcl.deserved_slope_1 = gcs.id_created_slopes OR gcl.deserved_slope_2 = gcs.id_created_slopes OR gcl.deserved_slope_3 = gcs.id_created_slopes)',
                'inner')
            ->join('game_slopes gs', 'gs.id_slope = gcs.id_slope', 'inner')
            ->where('gcl.id_created_lifts', $id_created_lift)
            ->where('gcl.id_resort', $id_resort)
            ->where('gcs.id_status', 1);
        $result = $this->db->get();

        if ($result->num_rows() === 0) {
            return 1.0;
        }

        $total_weight = 0.0;
        $count        = 0;
        foreach ($result->result() as $row) {
            $diff = (int)$row->id_difficulty;
            $total_weight += $difficulty_weights[$diff] ?? 1.0;
            $count++;
        }

        $coef = $total_weight / $count;
        return (float)max(0.85, min(1.05, $coef));
    }

    /**
     * calc_snow_quality_coef   Returns a snow-quality multiplier (0.80–1.20).
     *
     * Guest AI factor 2 – Snow quality
     * Combines the resort's current snow depth with the average slope grooming
     * condition.  Deep, well-groomed snow attracts significantly more visitors.
     *
     * @param int   $snow_level         Resort snow depth (cm, 0–MAX_SNOW_LEVEL)
     * @param float $avg_slope_cond     Average slope condition (0–100 %)
     * @return float                    Multiplier in [0.80, 1.20]
     */
    protected function calc_snow_quality_coef($snow_level, $avg_slope_cond) {
        // Normalise snow depth to 0–100 scale
        $snow_pct   = ($snow_level / MAX_SNOW_LEVEL) * 100;
        // Combined quality is the average of snow depth % and slope condition %
        $combined   = ($snow_pct + $avg_slope_cond) / 2.0;
        // Map 0–100 combined score to multiplier 0.80–1.20
        $coef = 0.80 + ($combined / 100.0) * 0.40;
        return (float)max(0.80, min(1.20, $coef));
    }

    /**
     * save_guest_ai_scores     Persists per-slope guest AI scores to game_guest_ai.
     *
     * Called at the end of visitor_calculations() so that all five factors
     * (difficulty, snow quality, crowd, lift speed, ticket price) are
     * computed and stored for the player's dashboard.
     *
     * @param int $id_resort            Resort ID
     * @param int $total_regular_visitors Total regular (non cross-country) visitors today
     * @param int $crowd_ratio          Visitors per open regular slope
     * @param int $skipass_daily        Daily skipass price
     * @param int $num_open_slopes      Number of open regular slopes
     */
    protected function save_guest_ai_scores(
        $id_resort,
        $total_regular_visitors,
        $crowd_ratio,
        $skipass_daily,
        $num_open_slopes
    ) {
        // Get all open regular slopes with their difficulty and condition
        $this->db
            ->select('gcs.id_created_slopes, gcs.custom_name, gcs.slope_condition, gs.id_difficulty')
            ->from('game_created_slopes gcs')
            ->join('game_slopes gs', 'gs.id_slope = gcs.id_slope', 'inner')
            ->where('gcs.id_resort', $id_resort)
            ->where('gcs.id_status', 1)
            ->where_in('gs.slope_type', [1, 2, 3, 5, 6]); // regular types (not cross-country)
        $slopes_result = $this->db->get();

        if ($slopes_result->num_rows() === 0) {
            return;
        }

        $snow_level          = (int)$this->show_snow_level($id_resort);
        $now                 = gmdate('Y-m-d H:i:s');
        $num_slopes_actual   = $slopes_result->num_rows();
        $visitors_per_slope  = ($num_slopes_actual > 0) ? round($total_regular_visitors / $num_slopes_actual) : 0;

        // Difficulty weights (same as calc_difficulty_coef)
        $diff_scores = [1 => 90, 2 => 100, 3 => 80, 4 => 60];

        // --- Score: ticket price (daily_coef → 0.1 to 2.0 → map to 5–100) ---
        $daily_coef         = $this->get_coef($num_open_slopes, $skipass_daily, 'daily');
        $score_ticket_price = (float)min(round($daily_coef * 50), 100);

        // --- Score: crowd level (lower crowd = better score) ---
        // crowd_ratio <= 500 -> 100; 1,000 -> 75; 1,500 -> 50; >= 2,000 -> 10
        $score_crowd = (float)max(10, min(100, 100 - max(0, $crowd_ratio - 500) / 10));

        foreach ($slopes_result->result() as $slope_row) {
            $diff_id   = (int)$slope_row->id_difficulty;
            $cond      = (float)$slope_row->slope_condition;
            $slope_id  = (int)$slope_row->id_created_slopes;
            $name      = $slope_row->custom_name ?? '';

            // --- Score: difficulty ---
            $score_difficulty = (float)($diff_scores[$diff_id] ?? 70);

            // --- Score: snow quality (depth + grooming) ---
            $snow_pct           = ($snow_level / MAX_SNOW_LEVEL) * 100;
            $score_snow_quality = (float)min(100, round(($snow_pct + $cond) / 2.0));

            // --- Score: lift speed (avg throughput of lifts serving this slope) ---
            $this->db
                ->select('AVG(gl.throughput) as avg_throughput')
                ->from('game_created_lifts gcl')
                ->join('game_lifts gl', 'gl.id_group = gcl.id_group', 'inner')
                ->where('gcl.id_resort', $id_resort)
                ->where('gcl.id_status', 1)
                ->where("(gcl.deserved_slope_1 = $slope_id OR gcl.deserved_slope_2 = $slope_id OR gcl.deserved_slope_3 = $slope_id)");
            $lift_result    = $this->db->get()->row();
            $avg_throughput = $lift_result ? (float)$lift_result->avg_throughput : 0;
            // throughput: 0→50, 600→65, 1200→80, 2000+→95
            $score_lift_speed = (float)min(95, max(50, round(50 + ($avg_throughput / 2000) * 45)));

            // --- Total score: equal-weight average of all five factors ---
            $total_score = (float)round(
                ($score_difficulty + $score_snow_quality + $score_crowd + $score_lift_speed + $score_ticket_price) / 5.0,
                2
            );

            $this->guest_ai_model->upsert_score([
                'id_resort'          => $id_resort,
                'id_created_slope'   => $slope_id,
                'slope_name'         => $name,
                'score_difficulty'   => $score_difficulty,
                'score_snow_quality' => $score_snow_quality,
                'score_crowd'        => $score_crowd,
                'score_lift_speed'   => $score_lift_speed,
                'score_ticket_price' => $score_ticket_price,
                'total_score'        => $total_score,
                'daily_visitors'     => $visitors_per_slope,
                'updated_at'         => $now,
            ]);
        }
    }

    /**
     * save_visitor_needs_scores    Computes and persists the four visitor need scores
     *                              (hunger, fatigue, warmth, fun) for a single resort.
     *
     * Scoring rules (0–100 each):
     *   hunger  – base 30; +VISITOR_NEEDS_HUNGER_PER_RESTAURANT per restaurant building; capped at 100
     *   fatigue – base 30; +VISITOR_NEEDS_FATIGUE_PER_MEDICAL per medical building;
     *                       +VISITOR_NEEDS_FATIGUE_PER_HOTEL per hotel building; capped at 100
     *   warmth  – base 70; -VISITOR_NEEDS_WARMTH_COLD_PENALTY per °C below 0;
     *                       +VISITOR_NEEDS_WARMTH_PER_LUXURY per luxury building; clamped 0–100
     *   fun     – base 20; +VISITOR_NEEDS_FUN_PER_LEISURE per leisure building;
     *                       +VISITOR_NEEDS_FUN_PER_OPEN_SLOPE per open regular slope; capped at 100
     *
     * @param int $id_resort
     * @param int $num_open_slopes   Number of open regular slopes today
     */
    protected function save_visitor_needs_scores($id_resort, $num_open_slopes) {

        // --- Hunger: restaurants ---
        $restaurant_info = $this->get_info_created_buildings($id_resort, 'restaurant');
        $restaurant_count = 0;
        if ($restaurant_info->num_rows() > 0) {
            foreach ($restaurant_info->result() as $rb) {
                $restaurant_count += (int)$rb->count_level;
            }
        }
        $hunger_score = (float)min(100, 30 + $restaurant_count * VISITOR_NEEDS_HUNGER_PER_RESTAURANT);

        // --- Fatigue: medical + hotel ---
        $medical_info = $this->get_info_created_buildings($id_resort, 'medical');
        $medical_count = 0;
        if ($medical_info->num_rows() > 0) {
            foreach ($medical_info->result() as $mb) {
                $medical_count += (int)$mb->count_level;
            }
        }
        $hotel_info = $this->get_info_created_buildings($id_resort, 'hotel');
        $hotel_count = 0;
        if ($hotel_info->num_rows() > 0) {
            foreach ($hotel_info->result() as $hb) {
                $hotel_count += (int)$hb->count_level;
            }
        }
        $fatigue_score = (float)min(100,
            30 + $medical_count * VISITOR_NEEDS_FATIGUE_PER_MEDICAL
               + $hotel_count   * VISITOR_NEEDS_FATIGUE_PER_HOTEL
        );

        // --- Warmth: temperature + luxury buildings ---
        $temperature = 0.0;
        $forecast = $this->weather_model->select_weather_forecast($this->todays_date);
        if ($forecast && $forecast->num_rows() > 0) {
            $cond_row    = $this->weather_model->select_weather_conditions($forecast->row()->id_condition)->row();
            $temperature = $cond_row ? (float)$cond_row->temperature : 0.0;
        }
        $cold_penalty = ($temperature < 0) ? abs($temperature) * VISITOR_NEEDS_WARMTH_COLD_PENALTY : 0;

        $luxury_info = $this->get_info_created_buildings($id_resort, 'luxury');
        $luxury_count = 0;
        if ($luxury_info->num_rows() > 0) {
            foreach ($luxury_info->result() as $lb) {
                $luxury_count += (int)$lb->count_level;
            }
        }
        $warmth_score = (float)max(0, min(100,
            70 - $cold_penalty + $luxury_count * VISITOR_NEEDS_WARMTH_PER_LUXURY
        ));

        // --- Fun: leisure buildings + open slopes ---
        $leisure_info = $this->get_info_created_buildings($id_resort, 'leisure');
        $leisure_count = 0;
        if ($leisure_info->num_rows() > 0) {
            foreach ($leisure_info->result() as $lb) {
                $leisure_count += (int)$lb->count_level;
            }
        }
        $fun_score = (float)min(100,
            20 + $leisure_count * VISITOR_NEEDS_FUN_PER_LEISURE
               + $num_open_slopes * VISITOR_NEEDS_FUN_PER_OPEN_SLOPE
        );

        // --- Overall satisfaction: equal-weight average ---
        $needs_satisfaction = (float)round(
            ($hunger_score + $fatigue_score + $warmth_score + $fun_score) / 4.0, 2
        );

        $this->visitor_needs_model->upsert(
            $id_resort,
            round($hunger_score,  2),
            round($fatigue_score, 2),
            round($warmth_score,  2),
            round($fun_score,     2),
            $needs_satisfaction
        );
    }

    /**
     * get_average_slope_type_condition    Gets the average condition of slopes of a given type.
     *
     * @param int $current_resort   Resort ID
     * @param int $slope_type_id    Slope type ID
     * @return mixed                Average condition value
     */
    protected function get_average_slope_type_condition($current_resort, $slope_type_id) {
        $this->db->select('AVG(game_created_slopes.slope_condition) as average_condition');
        $this->db->from('game_created_slopes');
        $this->db->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner');
        $this->db->where('game_created_slopes.id_resort', $current_resort);
        $this->db->where('game_slopes_tbl.slope_type', $slope_type_id);
        $query = $this->db->get();
        $data = $query->row();
        $average_condition = $data->average_condition;
        return $average_condition;
    }
    
    /**
     * get_created_slopes_and_generic       Gets the generic info (length) of the created slopes of the player
     * 
     * @param type $id_resort               ID resort
     * @return type                         Returns query's result
     */
    protected function get_created_slopes_and_generic($id_resort, $slope_type_id = null){    
        $this->db->select('game_slopes_tbl.length, game_created_slopes.id_created_slopes, game_created_slopes.id_condition');
        $this->db->distinct();
        $this->db->from('game_created_slopes');
        $this->db->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner');
        $this->db->where('game_created_slopes.id_resort', $id_resort);
        $this->db->where('game_created_slopes.id_status', '1');
        if ($slope_type_id != null)
            $this->db->where('game_slopes_tbl.slope_type', $slope_type_id);
        return $this->db->get();  
    }
    
    protected function get_deserving_lift_info($current_resort, $current_slope) {
        $this->db->where('id_resort', $current_resort);
        $this->db->where('id_status', '1');             // Only open lifts
        $where= '(deserved_slope_1 = '.$current_slope.' OR deserved_slope_2 = '.$current_slope.' OR deserved_slope_3 = '.$current_slope.')';
        $this->db->where($where);
        $this->db->from('game_created_lifts');
        $num_deserving_lifts = $this->db->count_all_results();          // Counts the number of lifts deserving the slope
        return $num_deserving_lifts;
    }
    
    /**
     * get_coef                         Generates an affluence coefficient depending on the skipass price and number of slopes.
     * The coefficient should be maximum 2, minimum 0.1
     * The coefficient should be at its maximum for lower prices
     * Refer to Excel document for details and charts
     * 
     * @param type $num_open_slopes     Number of open slopes for the player
     * @param type $skipass_price       Skipass price
     * @param type $type                Weekenly or daily type
     * @return array                    Returns the coefficient value (decimal between 2 and 0.1)
     */
    protected function get_coef($num_open_slopes, $skipass_price, $type){
        // Associate a daily price to the index of the price in the coefficient array below
        $array_daily_prices = array('10' => '0','11' => '1', '12' => '2', '13' => '3', '14' => '4', '15' => '5', '16' => '6', '17' => '7', '18' => '8', '19' => '9', '20' => '10',
            '21' => '11', '22' => '12', '23' => '13', '24' => '14', '25' => '15', '26' => '16', '27' => '17', '28' => '18', '29' => '19', '30' => '20', '31' => '21', '32' => '22',
            '33' => '23', '34' => '24', '35' => '25', '36' => '26', '37' => '27', '38' => '28', '39' => '29', '40' => '30', '41' => '31', '42' => '32', '43' => '33', '44' => '34',
            '45' => '35', '46' => '36', '47' => '37', '48' => '38', '49' => '39', '50' => '40', '51' => '41', '52' => '42', '53' => '43', '54' => '44', '55' => '45', '56' => '46',
            '57' => '47', '58' => '48', '59' => '49', '60' => '50', '61' => '51', '62' => '52', '63' => '53', '64' => '54', '65' => '55', '66' => '56', '67' => '57', '68' => '58', '69' => '59', '70' => '60', '71' => '61', '72' => '62', '73' => '63', '74' => '64', '75' => '65', '76' => '66', '77' => '67', '78' => '68', '79' => '69', '80' => '70', '81' => '71', '82' => '72', '83' => '73', '84' => '74', '85' => '75', '86' => '76', '87' => '77', '88' => '78', '89' => '79', '90' => '80', '91' => '81', '92' => '82', '93' => '83', '94' => '84', '95' => '85', '96' => '86', '97' => '87', '98' => '88', '99' => '89', '100' => '90');
        // Associate a weekly price to the index of the price in the coefficient array below
        $array_weekly_prices = array('70' => '0', '80' => '1', '90' => '2', '100' => '3', '110' => '4', '120' => '5', '130' => '6', '140' => '7', '150' => '8', '160' => '9',
            '170' => '10', '180' => '11', '190' => '12', '200' => '13', '210' => '14', '220' => '15', '230' => '16', '240' => '17', '250' => '18', '260' => '19', '270' => '20',
            '280' => '21', '290' => '22', '300' => '23', '310' => '24', '320' => '25', '330' => '26', '340' => '27', '350' => '28', '360' => '29', '370' => '30', '380' => '31',
            '390' => '32', '400' => '33', '410' => '34', '420' => '35', '430' => '36', '440' => '37', '450' => '38', '460' => '39', '470' => '40', '480' => '41', '490' => '42', '500' => '43', '510' => '44', '520' => '45', '530' => '46', '540' => '47', '550' => '48', '560' => '49', '570' => '50', '580' => '51', '590' => '52', '600' => '53', '610' => '54', '620' => '55', '630' => '56', '640' => '57', '650' => '58', '660' => '59', '670' => '60', '680' => '61', '690' => '62', '700' => '63');
        
        // Gets the value of the price index depending if daily or weekly mode
        if ($type == 'daily')
        $position_price_column = $array_daily_prices[$skipass_price];
        else if ($type == 'weekly')
        $position_price_column = $array_weekly_prices[$skipass_price];
        
        $array_daily_coefficents = array(
                // 0
                array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                array(1.5,1.5,1.5,1.5,1.4,1.3,1.1,1.1,0.9,0.9,0.8,0.7,0.7,0.6,0.6,0.5,0.5,0.4,0.4,0.3,0.3,0.3,0.2,0.2,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(1.6,1.6,1.6,1.6,1.5,1.5,1.4,1.3,1.1,1,0.9,0.8,0.8,0.8,0.7,0.6,0.6,0.5,0.5,0.4,0.4,0.3,0.3,0.2,0.2,0.2,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(1.7,1.7,1.7,1.7,1.6,1.6,1.6,1.5,1.4,1.3,1.1,1,0.9,0.9,0.9,0.7,0.7,0.6,0.6,0.5,0.5,0.4,0.4,0.3,0.3,0.3,0.3,0.2,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(1.8,1.8,1.8,1.8,1.7,1.7,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.85,0.85,0.85,0.7,0.6,0.6,0.5,0.5,0.4,0.4,0.4,0.4,0.3,0.3,0.2,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 5
                array(1.9,1.9,1.9,1.9,1.9,1.9,1.8,1.8,1.8,1.8,1.7,1.6,1.4,1.2,1.1,1,0.95,0.95,0.8,0.7,0.7,0.7,0.6,0.6,0.5,0.5,0.5,0.4,0.4,0.3,0.3,0.2,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,1.9,1.9,1.9,1.9,1.8,1.7,1.6,1.4,1.3,1.1,1.1,1,0.9,0.8,0.8,0.7,0.7,0.6,0.6,0.5,0.5,0.5,0.4,0.4,0.3,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.7,1.5,1.4,1.2,1.1,1.1,1,0.9,0.8,0.8,0.7,0.7,0.6,0.6,0.6,0.5,0.5,0.4,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.7,1.5,1.3,1.2,1.1,1,0.9,0.9,0.8,0.8,0.7,0.7,0.7,0.6,0.6,0.5,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.7,1.5,1.3,1.2,1.1,1,1,0.9,0.9,0.8,0.8,0.8,0.7,0.7,0.6,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 10
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.7,1.5,1.4,1.3,1.2,1.1,1,1,0.9,0.9,0.9,0.8,0.8,0.7,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.7,1.6,1.5,1.4,1.2,1.1,1.1,1,1,1,0.9,0.9,0.8,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.7,1.6,1.4,1.2,1.2,1.1,1.1,1.1,1,1,0.9,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.7,1.5,1.3,1.3,1.2,1.2,1.2,1.1,1.1,1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.6,1.5,1.4,1.3,1.3,1.3,1.2,1.2,1.1,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 15
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.7,1.6,1.5,1.4,1.4,1.4,1.3,1.3,1.2,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.7,1.6,1.6,1.5,1.4,1.4,1.3,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.7,1.7,1.6,1.5,1.5,1.4,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.6,1.6,1.5,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.9,1.8,1.7,1.7,1.6,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 20
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 25
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 30
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 35
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 40
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 45
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,),
                // 50
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1,0.1),
                // 55
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2),
                // 60
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4,0.3),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5,0.4),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6,0.5),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7,0.6),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8,0.7),
                // 65
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9,0.8),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1,0.9),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 70
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 75
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 80
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 85
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 90
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 95
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 100
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                // 105
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.6,1.5,1.4,1.3,1.2,1.1,1)
                
            ); 
        $array_weekly_coefficents = array(
                // 0
                array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),                
                array(1.9,1.8,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,1.9,1.8,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                // 5
                array(2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                // 10
                array(2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                // 15
                array(2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                // 20
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),                
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),         
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 25
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 30
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 35
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 40
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                // 45
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1,0.1),
                // 50
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1,0.1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2,0.1),
                // 55
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4,0.2),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6,0.4),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8,0.6),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1,0.8),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 60
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 65
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 70
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 75
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 80
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 85
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 90
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 95
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 100
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                // 105
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1),
                array(2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1.9,1.8,1.8,1.7,1.7,1.5,1.3,1.1,1)
                
            ); 
        
        if ($type == 'daily')
            $coef = $array_daily_coefficents[$num_open_slopes][$position_price_column];
        else if ($type == 'weekly')
            $coef = $array_weekly_coefficents[$num_open_slopes][$position_price_column];
        return $coef;
    }
        
    /**
     * generate_revenue_instructors         Calculates how many revenues the instructors provide to this resort
     * 
     * @param type $current_resort                Current resort ID
     * @param type $fn_calc_visitors_slopes     Number of visitors given by previous slopes (only)
     * @return type                             Returns revenue of ski school (integer)
     */
    protected function generate_revenue_instructors($list_all_opened_resorts, $visitor_calculations){
        //$message_bonus_instructors = '';
        // Get instructor information for the current user
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];
            $daily_visitors = $visitor_calculations_Array['daily_visitors'];
            $get_info_instructors = $this->get_info_staff_DB($current_resort, 'skiinstructor');
            
            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('reporting',$player_preferred_lang);
            if ($get_info_instructors->num_rows() > 0) {
                foreach ($get_info_instructors->result() as $get_info_instructors_array){
                    $avg_efficiency = round($get_info_instructors_array->avg_efficiency,0);   // Average efficiency for the player's instructors
                    
                    // START Adds reporting data to DB
                    if ($avg_efficiency == 0 ){
                        $reporting_data = $this->lang->line('reporting')['no_instructors'];
                        $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'staff', $reporting_data);
                    }
                    else if ($avg_efficiency <= 60) {
                        $reporting_data = $this->lang->line('reporting')['avg_efficiency_instructors'].' '.$avg_efficiency.'%. '.$this->lang->line('reporting')['get_better_instructors'];
                        $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'staff', $reporting_data);
                        
                    }
                    // END Adds reporting data to DB
                    
                    $count_instructors = $get_info_instructors_array->count_staff;          // Number of instructors for the resort

                    $price_ski_lesson = '50';   // We consider a ski lesson of 50€
                    $occupancy_instructor = '0.9';   // We consider that an instructor is booked 20% of the time
                    $max_lessons_per_day = '6';     // We consider max 2 lessons per day per instructor
                    $coefficient_collective_course = 2; // We consider particular lessons and group once (cheaper but more paticipant)
                    $max_number_lessons_for_all_instructors = $count_instructors * $max_lessons_per_day * $occupancy_instructor * $coefficient_collective_course * $avg_efficiency/100;
                    $potential_tourists_taking_lessons = $daily_visitors * 0.5;    // Only 50% of tourists can take a lesson
                    $max_number_lessons_resort = min($max_number_lessons_for_all_instructors, $potential_tourists_taking_lessons);
                    // We consider 5% of the visitors will take lessons (0.05 coef)
                    //$revenue_instructor = round(($price_ski_lesson * $occupancy_instructor * $max_lessons_per_day * $daily_visitors * 0.05 * $avg_efficiency / 100 * $count_instructors),0);
                    //$revenue_instructor2 = round(($price_ski_lesson * $occupancy_instructor * $max_lessons_per_day * $count_instructors * $avg_efficiency),0);
                    $revenue_instructor = round(($price_ski_lesson * $max_number_lessons_resort),0);
                    // Trainer specialization bonus: each trainer instructor adds up to +10% revenue (max +50%)
                    $trainer_count = $this->db
                        ->where('hired_staff_tbl.id_resort', $current_resort)
                        ->where('hired_staff_tbl.specialization', 'trainer')
                        ->where('game_staff_tbl.position', 'skiinstructor')
                        ->join('game_staff as game_staff_tbl', 'game_staff_tbl.id_staff = hired_staff_tbl.id_staff', 'inner')
                        ->from('game_hired_staff as hired_staff_tbl')
                        ->count_all_results();
                    $trainer_mult = min(1.0 + ($trainer_count * 0.10), 1.50);
                    if ($trainer_mult > 1.0) {
                        $revenue_instructor = round($revenue_instructor * $trainer_mult);
                        $this->logToFile($this->Log_filename, "DEBUG", "[id_resort_".$current_resort."]", "generate_revenue_instructors",
                            "Trainer spec bonus: x".$trainer_mult." (".$trainer_count." trainer(s))\n");
                    }
                    //echo ' $current_resort: '.$current_resort.' total = '.$revenue_instructor.' => '.$price_ski_lesson.' * '.$occupancy_instructor.' * '.$max_lessons_per_day.' * '.$daily_visitors.' * 0.05 * '.$avg_efficiency.' / 100 * '.$count_instructors.'<br>';
                    //echo ' $current_resort2: '.$current_resort.' total = '.$revenue_instructor2.' => '.$price_ski_lesson.' * '.$occupancy_instructor.' * '.$max_lessons_per_day.' * '.$count_instructors.' * '.$avg_efficiency.'<br>';
                    //echo ' $revenue_instructor: '.$current_resort.' total = '.$revenue_instructor.' => '.$price_ski_lesson.' * '.$max_number_lessons_resort.'<br>';
                    //echo '$max_number_lessons_for_all_instructors: '.$max_number_lessons_for_all_instructors.'<br>';
                    //echo '$potential_tourists_taking_lessons: '.$potential_tourists_taking_lessons.'<br>';
                        
                    //$revenue_instructor2 = round(($price_ski_lesson * $occupancy_instructor / 100 * $max_lessons_per_day * $daily_visitors * 0.05 * $avg_efficiency / 100 * $count_instructors),0);
                    
                    $today = strtotime('now');
                    $yesterday = strtotime('-1 day', $today);
                    $yesterday_GMT = gmdate('Y-m-d', $yesterday);
                    $today_GMT = gmdate('Y-m-d', $today);
                    // Adds the revenue to the resort
                    $add_revenue_query = $this->add_revenue_DB($current_resort, $revenue_instructor);
                    $add_revenue_history_query_main_table = add_revenue_stat_table($current_resort, $revenue_instructor, 'revenue', $yesterday_GMT);
                    // Adds revenue to the revenue table for ski schools
                    $add_revenue_history_query_specific_table = add_revenue_stat_table($current_resort, $revenue_instructor, 'rev_instructor', $yesterday_GMT);
                    if ($add_revenue_history_query_main_table)
                        $message_bonus_instructors = "Resort ".$current_resort." earned ".$revenue_instructor." € thanks to ".$count_instructors." ski instructors\n";
                    else
                        $message_bonus_instructors = "Resort ".$current_resort." didn\'t earn anything with the ski schools\n";
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_revenue_instructors", $message_bonus_instructors);

                }
            }
            else {
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['error'].'Ref: generate_revenue_instructors > $get_info_instructors->num_rows()';
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'staff', $reporting_data);
                // END Adds reporting data to DB
            }
        }
        //return $message_bonus_instructors;
    }    
        
    /**
     * update_staff_morale           Updates morale and strike status for all hired staff in opened resorts.
     *                               Morale is affected daily by salary, workload, and current weather.
     *                               Staff traits and specializations modify the morale formula:
     *                                 - trait_easygoing   : extra recovery when morale is below default
     *                                 - trait_sensitive   : bad-weather morale penalty is amplified
     *                                 - trait_ambitious   : morale boost when staff levels up
     *                                 - spec_endurance    : bad-weather morale penalty is halved
     *                               Assigned staff also earn daily on-the-job XP (trait_hardworking earns 50 % more).
     *
     * @param object $list_all_opened_resorts   All open resorts result object
     * @param string $weather_name_english      Today's weather condition name in English
     */
    protected function update_staff_morale($list_all_opened_resorts, $weather_name_english) {
        $weather_delta = $this->get_weather_morale_delta($weather_name_english);
        foreach ($list_all_opened_resorts->result() as $resort_row) {
            $id_resort = $resort_row->id_resort;
            $staff_list = $this->staff_model->get_all_hired_staff_for_morale_DB($id_resort);
            if ($staff_list->num_rows() == 0) continue;
            foreach ($staff_list->result() as $staff_row) {
                $current_morale = (int)$staff_row->morale;
                $salary         = (int)$staff_row->salary;
                $is_assigned    = !is_null($staff_row->id_item_assigned);
                $on_strike      = (int)$staff_row->on_strike;
                $trait          = isset($staff_row->trait)          ? $staff_row->trait          : null;
                $specialization = isset($staff_row->specialization) ? $staff_row->specialization : null;
                $delta = 0;
                // Drift morale towards the default value each day
                if ($current_morale < MORALE_DEFAULT) {
                    $recovery = MORALE_DAILY_RECOVERY;
                    // trait_easygoing: recovers morale extra quickly
                    if ($trait === 'easygoing') $recovery += STAFF_EASYGOING_RECOVERY_BONUS;
                    // trait_loyal: stable morale, extra recovery toward default
                    if ($trait === 'loyal') $recovery += 2;
                    $delta += $recovery;
                } elseif ($current_morale > MORALE_DEFAULT) {
                    $delta -= MORALE_DAILY_RECOVERY;
                }
                // Pay factor
                if ($salary >= 3000)
                    $delta += MORALE_PAY_HIGH;
                elseif ($salary >= 1500)
                    $delta += MORALE_PAY_MED;
                elseif ($salary < 1000)
                    $delta += MORALE_PAY_LOW;
                // Workload factor
                $delta += $is_assigned ? MORALE_ASSIGNED : MORALE_UNASSIGNED;
                // Weather factor — modified by specialization and trait
                // Note on combined effects: when both spec_endurance and trait_sensitive apply,
                // endurance is applied first (physical resilience), then sensitive amplifies what remains.
                // A staff member with both ends up at 0.75× the original penalty (0.5 × 1.5).
                $effective_weather_delta = $weather_delta;
                if ($weather_delta < 0) {
                    // spec_endurance: halve bad-weather morale penalty
                    if ($specialization === 'endurance')
                        $effective_weather_delta = (int)round($weather_delta * STAFF_ENDURANCE_WEATHER_FACTOR);
                    // trait_sensitive: amplify bad-weather morale penalty (applied after endurance if both present)
                    if ($trait === 'sensitive')
                        $effective_weather_delta = (int)round($effective_weather_delta * STAFF_SENSITIVE_WEATHER_MULT);
                }
                $delta += $effective_weather_delta;
                $new_morale = max(MORALE_MIN, min(MORALE_MAX, $current_morale + $delta));
                // Strike: triggered when morale drops at or below threshold; ends when morale recovers above it
                if ($new_morale <= MORALE_STRIKE_THRESHOLD) {
                    $on_strike = 1;
                } elseif ($on_strike && $new_morale > MORALE_STRIKE_THRESHOLD) {
                    $on_strike = 0;
                }
                $this->staff_model->update_morale_DB($staff_row->id_hired_staff, $new_morale, $on_strike);
                $this->logToFile($this->Log_filename, "DEBUG", "[id_hired_staff_".$staff_row->id_hired_staff."]", "update_staff_morale",
                    "morale: ".$current_morale." -> ".$new_morale." (delta: ".$delta.", weather_delta: ".$effective_weather_delta."), on_strike: ".$on_strike.", trait: ".$trait.", spec: ".$specialization."\n");

                // Daily on-the-job XP: only assigned staff who are not on strike earn XP
                if ($is_assigned && !$on_strike) {
                    $xp_to_award = STAFF_DAILY_XP;
                    // trait_hardworking: gains XP 50 % faster
                    if ($trait === 'hardworking')
                        $xp_to_award = (int)round($xp_to_award * STAFF_HARDWORKING_XP_MULT);
                    $xp_result = $this->staff_model->add_experience_db($staff_row->id_hired_staff, $xp_to_award);
                    // trait_ambitious: morale boost when leveling up
                    if ($xp_result['leveled_up'] && $trait === 'ambitious') {
                        $boosted_morale = min(MORALE_MAX, $new_morale + STAFF_AMBITIOUS_LEVELUP_MORALE);
                        $this->staff_model->update_morale_DB($staff_row->id_hired_staff, $boosted_morale, $on_strike);
                        $this->logToFile($this->Log_filename, "INFO", "[id_hired_staff_".$staff_row->id_hired_staff."]", "update_staff_morale",
                            "Ambitious trait: level-up to ".$xp_result['new_level']." → morale boost +".(STAFF_AMBITIOUS_LEVELUP_MORALE)." → ".$boosted_morale."\n");
                    }
                    if ($xp_result['leveled_up']) {
                        $this->logToFile($this->Log_filename, "INFO", "[id_hired_staff_".$staff_row->id_hired_staff."]", "update_staff_morale",
                            "Staff leveled up to skill level ".$xp_result['new_level']."\n");
                    }
                }
            }
        }
    }

    /**
     * get_weather_morale_delta      Returns the morale delta for a given weather condition.
     *
     * @param string $weather_name_english   Weather condition name in English
     * @return int                           Morale adjustment value
     */
    private function get_weather_morale_delta($weather_name_english) {
        $map = [
            'Storm'    => MORALE_WEATHER_STORM,
            'Blizzard' => MORALE_WEATHER_STORM,
            'Raining'  => MORALE_WEATHER_BAD,
            'Fog'      => MORALE_WEATHER_CLOUDY,
            'Windy'    => MORALE_WEATHER_CLOUDY,
            'Overcast' => MORALE_WEATHER_NEUTRAL,
            'Cloudy'   => MORALE_WEATHER_NEUTRAL,
            'Snowing'  => MORALE_WEATHER_GOOD,
            'Sunny'    => MORALE_WEATHER_SUNNY,
        ];
        return $map[$weather_name_english] ?? MORALE_WEATHER_NEUTRAL;
    }

    /**
     * morale_efficiency_factor      Returns an efficiency multiplier (0.0-1.0) based on morale and strike status.
     *                               - On strike: 0.0 (no work done)
     *                               - Low morale (<=50): 0.70 (30% reduction)
     *                               - Medium morale (<=70): 0.85 (15% reduction)
     *                               - Good morale (>70): 1.0 (full efficiency)
     *
     * @param int $morale     Staff morale (0-100)
     * @param int $on_strike  Strike flag (0 or 1)
     * @return float          Efficiency multiplier
     */
    private function morale_efficiency_factor($morale, $on_strike) {
        if ($on_strike || $morale <= MORALE_STRIKE_THRESHOLD) return 0.0;
        if ($morale <= MORALE_LOW_THRESHOLD)     return 0.70;
        if ($morale <= MORALE_HIGH_THRESHOLD)    return 0.85;
        return 1.0;
    }

    /**
     * get_resort_avg_morale_factor  Returns the average morale efficiency factor for a resort.
     *                               Accounts for on-strike staff who are not working.
     *
     * @param int $id_resort  Resort ID
     * @return float          Efficiency multiplier (0.0-1.0)
     */
    private function get_resort_avg_morale_factor($id_resort) {
        $summary = $this->staff_model->get_resort_morale_summary_DB($id_resort);
        if (!$summary || (int)$summary->total_staff == 0) return 1.0;
        $avg_morale   = (float)$summary->avg_morale;
        $strike_count = (int)$summary->strike_count;
        $total_staff  = (int)$summary->total_staff;
        $factor = $this->morale_efficiency_factor((int)round($avg_morale), 0);
        // If any staff is on strike, reduce factor proportionally
        if ($strike_count > 0) {
            $factor *= (1.0 - ($strike_count / $total_staff) * 0.5);
        }
        return max(0.0, $factor);
    }

    /**
     * pay_salaries                  Pay the salary of employees if the resort is opened
     * 
     * @param type $list_all_opened_resorts     Array containing all opened resorts
     * @return string                           Returns info message indicating the salary amount payed for the resort
     */    
    protected function pay_salaries($list_all_opened_resorts){
        foreach ($list_all_opened_resorts->result() as $opened_resorts_Array){
            $current_resort = $opened_resorts_Array->id_resort;
            $get_info_staff = $this->get_salary_staff_DB($current_resort);
            $results = $get_info_staff->row();
            $total_salary = $results->total_salary;
            if ($total_salary == '')
                $total_salary = 0;
            else
                $total_salary = $total_salary/30;   // We only take a daily cost
            $take_cost_query = $this->take_cost_DB($current_resort, $total_salary);
            
            // Adds the cost to the cost table
            $add_cost_history_query = add_cost_stat_table($current_resort, $total_salary, 'cost_salaries', $this->yesterdays_date);
            $add_cost_history_query = add_cost_stat_table($current_resort, $total_salary, 'expenses', $this->yesterdays_date);
            $formatted_total_salary = number_format($total_salary, 0, '.', ' ');
                if ($add_cost_history_query) {
                    $message_pay_salaries = "Salary cost ".$formatted_total_salary." € taken for resort ID ".$current_resort."\n";
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "pay_salaries", $message_pay_salaries);
                    $player_preferred_lang = $opened_resorts_Array->preferred_lang;
                    $currentUserID = $opened_resorts_Array->id_player;
                    $this->lang->load('logs',$player_preferred_lang);
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['salaries'], 'data' => $formatted_total_salary.' € '.$this->lang->line('logs')['taken_salary']) );   // Add a log row to the game_player_logs table  
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['salaries'], 'data' => $formatted_total_salary.' € '.$this->lang->line('logs')['taken_salary']) );   // Add a log row to the game_player_logs table  
                }
                else {
                    $message_pay_salaries = "Something went wrong with taking salaries from resort ".$current_resort." (".$total_salary." €)\n";
                    $this->logToFile($this->Log_filename, "WARN", "[id_resort_".$current_resort."]", "pay_salaries", $message_pay_salaries);
                }
        }
    }
    /**
     * pay_loans                  Pay the loan if the resort is opened
     *              If the resort is closed, the loan has to be paid as well
     * 
     * @param type $list_all_resorts     Array containing all resorts
     * @return string                           Returns info message indicating the salary amount payed for the resort
     */    
    protected function pay_loans($list_all_resorts){
        //$message_pay_loans = '';
        //var_dump($list_all_resorts->result());
        foreach ($list_all_resorts->result() as $all_resorts_Array){
            $current_resort = $all_resorts_Array->id_resort;
            $get_ongoing_loan_player = $this->bank_model->get_ongoing_loan_player($current_resort);
            //var_dump($get_ongoing_loan_player);
            
            if ($get_ongoing_loan_player->num_rows() > 0) {
                foreach ($get_ongoing_loan_player->result() as $ongoing_loan_info) {
                    $amount_left = $ongoing_loan_info->amount_left;
                    $daily_payment_player = $ongoing_loan_info->daily_payment;
                    if ($amount_left < $daily_payment_player) {
                        $daily_payment_player = $amount_left;
                    }
                    $id_loan = $ongoing_loan_info->id_loan;
                    //echo '$current_resort: '.$current_resort;
                    //echo ' $daily_payment_player: '.$daily_payment_player;
                    $take_cost_query = $this->take_cost_DB($current_resort, $daily_payment_player);
                    // Adds the cost to the cost table
                    $add_cost_history_query = add_cost_stat_table($current_resort, $daily_payment_player, 'cost_loans', $this->yesterdays_date);
                    $add_cost_history_query2 = add_cost_stat_table($current_resort, $daily_payment_player, 'expenses', $this->yesterdays_date);
                    $formatted_daily_payment = number_format($daily_payment_player, 0, '.', ' ');
                    if ($take_cost_query) {
                        $update_loan_info = $this->update_loan_info_DB($current_resort, $daily_payment_player, $id_loan);
                        $message_pay_loans = "Loan cost ".$formatted_daily_payment." € taken for resort ID ".$current_resort."\n";
                        $this->logToFile($this->Log_filename, "INFO", "[current_resort_".$current_resort."]", "pay_loans", $message_pay_loans);
                        $player_preferred_lang = $all_resorts_Array->preferred_lang;
                        $currentUserID = $all_resorts_Array->id_player;
                        $this->lang->load('logs',$player_preferred_lang);
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $formatted_daily_payment.' € '.$this->lang->line('logs')['taken_loan']) );   // Add a log row to the game_player_logs table  
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $formatted_daily_payment.' € '.$this->lang->line('logs')['taken_loan']) );   // Add a log row to the game_player_logs table  
                    }
                    else {
                        $message_pay_loans = "Something went wrong with taking loan from resort ".$current_resort." (".$daily_payment_player." €)\n";
                        $this->logToFile($this->Log_filename, "WARN", "[current_resort_".$current_resort."]", "pay_loans", $message_pay_loans);
                    }
                }
            }
        }
        //return $message_pay_loans;
    }
    /**
     * finalize_loans                  Check if the loan is completely paid
     * 
     * @return string                           Returns info message indicating the salary amount payed for the resort
     */    
    protected function finalize_loans(){
        //$message_finalize_loans = '';
        $get_ongoing_loans_to_finalize = $this->bank_model->get_ongoing_loans_to_finalize();
        if ($get_ongoing_loans_to_finalize->num_rows() > 0) {
            foreach ($get_ongoing_loans_to_finalize->result() as $get_ongoing_loans_array){
                $reimbursed_date = $this->todays_datetime;
                $current_resort = $get_ongoing_loans_array->id_resort;
                $id_loan = $get_ongoing_loans_array->id_loan;
                $borrowed_amount = $get_ongoing_loans_array->borrowed_amount;
                $finalize_loan = $this->bank_model->finalize_loan($reimbursed_date, $current_resort, $id_loan);
                if ($finalize_loan !== false && $finalize_loan > 0) {
                    $message_finalize_loans = "Loan ID ".$id_loan." was finalized for resort ".$current_resort."\n";
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "finalize_loans", $message_finalize_loans);
                    $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
                    $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                    $this->lang->load('logs',$player_preferred_lang);
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $this->lang->line('logs')['your'].' '.number_format($borrowed_amount, 0, ',', ' ').' € '.$this->lang->line('logs')['fully_reimbursed']) );   // Add a log row to the game_player_logs table  
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $this->lang->line('logs')['your'].' '.number_format($borrowed_amount, 0, ',', ' ').' € '.$this->lang->line('logs')['fully_reimbursed']) );   // Add a log row to the game_player_logs table  
                }
                else {
                    $message_finalize_loans = "Something went wrong finalizing loan from resort ".$current_resort."\n";
                    $this->logToFile($this->Log_filename, "WARN", "[id_resort_".$current_resort."]", "finalize_loans", $message_finalize_loans);
                }
            }
        }
        //return $message_finalize_loans;
    }
    
    
    /**
     * update_loan_info_DB               Updates the payments_left and amount_left columns
     * 
     * @param type $current_resort    Current resort ID
     * @param type $revenue         Amount in euros to add
     * @return type                 Returns the transaction result
     */
    protected function update_loan_info_DB($current_resort, $daily_payment, $id_loan){
        $this->db->trans_start();
        $this->db->set('payments_left', 'payments_left-1',FALSE);
        $this->db->set('amount_left', 'amount_left-'.$daily_payment,FALSE);
        $this->db->where('id_resort' , $current_resort);                              
        $this->db->where('id_loan' , $id_loan);                              
        $this->db->update('game_signed_loans');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    
    /**
     * get_number_build_slopes_type       Get number of built slopes for that resort which specific slope type id        
     * 
     * @param type $current_resort    Current resort ID
     * @param type $slope_type_id      Slope type id to get
     * @return type                 Return the number of built slopes
     */
    protected function get_number_build_slopes_type($current_resort, $slope_type_id){
        $this->db->select('COUNT(game_created_slopes.id_created_slopes) as total_slopes');
        $this->db->from('game_created_slopes');
        $this->db->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner');
        $this->db->where('game_created_slopes.id_resort', $current_resort);
        $this->db->where('game_slopes_tbl.slope_type', $slope_type_id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->total_slopes;
    }
    
    
    /**
     * get_salary_staff_DB               
     * 
     * @param type $current_resort    Current resort ID
     * @param type $staff_type      Tupe of staff (driver, instructor...)
     * @return type                 Return the query results as "efficiency", "count_staff" and "total_capacity"
     */
    protected function get_salary_staff_DB($current_resort){
        $this->db->select('game_staff_tbl.salary, SUM(game_staff_tbl.salary) as total_salary');
        $this->db->from('game_hired_staff');
        $this->db->join('game_staff as game_staff_tbl', 'game_hired_staff.id_staff = game_staff_tbl.id_staff', 'inner');
        $this->db->where('game_hired_staff.id_resort', $current_resort);
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * generate_revenue_skibus                  Generates revenue (euros) for the skibuses of each player
     * 
     * @param type $list_all_opened_resorts     Array containing all opened resorts
     * @param type $visitor_calculations        Number of visitors given by previous functions (all)
     * @return string                           Returns info message indicating the revenue generated by the skibuses (in euros)
     */    
    protected function generate_revenue_skibus($list_all_opened_resorts, $visitor_calculations){
        //$message_bonus_skibus = '';
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];
            $daily_visitors = $visitor_calculations_Array['daily_visitors'];
            $get_info_staff = $this->get_info_staff_DB($current_resort, 'driver');
            
            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('reporting',$player_preferred_lang);
            
            if ($get_info_staff->num_rows() > 0) {
                foreach ($get_info_staff->result() as $get_info_staff_array){
                    $avg_efficiency = round($get_info_staff_array->avg_efficiency,0);
                    
                    // START Adds reporting data to DB
                    if ($avg_efficiency == 0 ){
                        $reporting_data = $this->lang->line('reporting')['no_instructors'];
                        $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'staff', $reporting_data);
                    }
                    else if ($avg_efficiency <= 60) {
                        $reporting_data = $this->lang->line('reporting')['avg_efficiency_drivers'].' '.$avg_efficiency.'%. '.$this->lang->line('reporting')['get_better_drivers'];
                        $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'staff', $reporting_data);
                        
                    }
                    // END Adds reporting data to DB
                    
                    //$count_staff = $get_info_staff_array->count_staff;
                    $capacity = $get_info_staff_array->total_capacity;
                    if ($daily_visitors > $capacity) {
                        $max_skibus_use = $capacity * 12;   // Multipy by 16 because bus trip takes 30min and there are about 6 working hours = 12 trips of capacity
                        $ratio = round(100*($max_skibus_use/$daily_visitors), 0);
                        if ($ratio < 100)
                            $reporting_data = $this->lang->line('reporting')['your_skibus_can_only'].' '.$ratio.$this->lang->line('reporting')['perc_of_tourists'].' '.$this->lang->line('reporting')['buy_more_skibuses'];
                        else
                            $reporting_data = $this->lang->line('reporting')['your_skibus_can_handle'].' '.$ratio.$this->lang->line('reporting')['perc_of_tourists'].' '.$this->lang->line('reporting')['enough_skibuses'];
                    }
                    else {
                        $max_skibus_use = $daily_visitors;
                        $reporting_data = $this->lang->line('reporting')['enough_skibuses'];
                    }
                    
                    // START Adds reporting data to DB
                    $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'skibus', $reporting_data);
                    // END Adds reporting data to DB
    
                    $today = strtotime('now');
                    $yesterday = strtotime('-1 day', $today);
                    $yesterday_GMT = gmdate('Y-m-d', $yesterday);
                    $today_GMT = gmdate('Y-m-d', $today);
                    $skibus_users_today = round($max_skibus_use * 0.5) ;       // 50 percent of the skiers can take the bus
                    $revenue_skibus = round($skibus_users_today * $avg_efficiency / 100 * 3);   // 3€ per ticket
                    // Adds the revenue to the resort
                    $add_revenue_query = $this->add_revenue_DB($current_resort, $revenue_skibus);
                    // Adds the revenue to the revenue table
                    $add_revenue_history_query_main_table = add_revenue_stat_table($current_resort, $revenue_skibus, 'revenue', $yesterday_GMT);
                    // Adds revenue to the revenue table for ski bus
                    $add_revenue_history_query_specific_table = add_revenue_stat_table($current_resort, $revenue_skibus, 'rev_skibus', $yesterday_GMT);
                    if ($add_revenue_history_query_main_table)
                        $message_bonus_skibus = "Resort ".$current_resort." earned ".$revenue_skibus." € thanks to ".$skibus_users_today." skibus customers\n";
                    else
                        $message_bonus_skibus = "Resort ".$current_resort." didn\'t earn anything with the skibus service\n";
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_revenue_skibus", $message_bonus_skibus);
                }
            }
            else {
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['error'].'Ref: generate_revenue_skibus > $get_info_staff->num_rows()';
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'staff', $reporting_data);
                // END Adds reporting data to DB
            }
        }
        //return $message_bonus_skibus;
    }
    
    /**
     * generate_revenue_building                  Generates revenue (euros) for the buildings of each player
     * 
     * @param type $visitor_calculations        Number of visitors given by previous functions (all)
     * @param type $building_type               Building type (hotel, access...)
     * @return string                           Returns info message indicating the revenue generated by the buildings (in euros)
     */    
    protected function generate_revenue_building($visitor_calculations, $building_type){
        //$message_bonus_building = '';
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];
            $daily_visitors = $visitor_calculations_Array['daily_visitors'];
            $prestige_bonus = $visitor_calculations_Array['prestige_bonus'];
            $data_get_info_created_buildings = $this->get_info_created_buildings($current_resort, $building_type);
                
            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('reporting',$player_preferred_lang);
            $total_capacity = 0;
            $total_max_income_building = 0;
                
            if ($data_get_info_created_buildings->num_rows() > 0) {
                foreach ($data_get_info_created_buildings->result() as $created_buildings_array){
                    $capacity = $created_buildings_array->capacity * $created_buildings_array->count_level;
                    $max_income_building = $created_buildings_array->max_income * $created_buildings_array->count_level;
                    
                    $total_capacity = $total_capacity + $capacity;
                    $total_max_income_building = $total_max_income_building + $max_income_building;
                }
                
                $today = strtotime('now');
                $yesterday = strtotime('-1 day', $today);
                $yesterday_GMT = gmdate('Y-m-d', $yesterday);
                $today_GMT = gmdate('Y-m-d', $today);
                
                $daily_visitors = round($daily_visitors * PERC_TOURISTS_BUILDING[$building_type], 0);   // Since not all visitors need all building type, we adjust percentage visiting each type
                
                
                // Calculate coefficient depending on capacity and visitors
                if ($total_capacity != 0 && $daily_visitors != 0 ){   // If capacity or visitors = 0 we do not earn anything
                    if ($total_capacity > $daily_visitors) {
                        $handled_tourists = $daily_visitors/$total_capacity;
                        $reporting_data = $this->lang->line('reporting')['enough_building_type'].' '.$building_type.' '.$this->lang->line('reporting')['to_handle_visitors'];
                    }
                    else if ($total_capacity <= $daily_visitors) {
                        $handled_tourists = 1;
                        $ratio = round(100*($total_capacity/$daily_visitors),0);
                        $reporting_data = $this->lang->line('reporting')['not_enough_building_type'].' "'.$building_type.'" '.$this->lang->line('reporting')['can_only_handle'].' '.$ratio.'% '.$this->lang->line('reporting')['of_your_visitors'].' '.$this->lang->line('reporting')['build_some_more'].' ( total capacity:'.$total_capacity.' -  daily_visitors:'.$daily_visitors.' =  ratio:'.$ratio.'. Tourists needing this type: '.PERC_TOURISTS_BUILDING[$building_type].' ) ';
                    }
                }
                else {
                    $handled_tourists = 0;
                    $reporting_data = $this->lang->line('reporting')['no_building_type'].' '.$building_type.' '.$this->lang->line('reporting')['or_no_visitors'];
                }
                
                // START Adds reporting data to DB
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'buildings', $reporting_data);
                // END Adds reporting data to DB

                $weightedValues = array( '0.5'=>5, '0.6'=>15, '0.7'=>30, '0.8'=>30, '0.9'=>15, 1=>5 );    // Percentage of chance to get the max gain
                $coefficient = getRandomWeightedElement($weightedValues);

                $gains_before_prestige_bonus = round($total_max_income_building * $coefficient * $handled_tourists);

                // Parking fee adjustment: fee above/below DEFAULT_PARKING_FEE shifts revenue and demand.
                if ($building_type == 'parking') {
                    $resort_data = $this->db->select('parking_fee')->from('game_resorts')->where('id_resort', $current_resort)->limit(1)->get()->row();
                    $parking_fee = isset($resort_data->parking_fee) ? (int)$resort_data->parking_fee : DEFAULT_PARKING_FEE;
                    $fee_delta = $parking_fee - DEFAULT_PARKING_FEE;
                    $demand_mult = max(0.1, 1.0 - PARKING_FEE_DEMAND_FACTOR * max(0, $fee_delta));
                    $revenue_mult = $parking_fee / DEFAULT_PARKING_FEE;
                    $gains_before_prestige_bonus = round($gains_before_prestige_bonus * $revenue_mult * $demand_mult);
                }
                
                if ( $building_type != 'medical' && $building_type != 'parking' ) {
                    $gains = round($gains_before_prestige_bonus * $prestige_bonus['coef']);
                }
                else {  // If medical or parking, prestige doesn't matter
                    $gains = $gains_before_prestige_bonus;
                }
                    
                $prestige_gains_today = $gains - $gains_before_prestige_bonus;
                
                $add_revenue_history_query_specific_table = add_revenue_stat_table($current_resort, $prestige_gains_today, 'prestige_gains', $yesterday_GMT);

                //if ($current_resort == 2)
                    //echo ' $prestige_gains_today: '.$prestige_gains_today.' (coef: '.$prestige_bonus['coef'].' ) - $building_type: '. $building_type.' - ';
                
                // Adds the revenue to the resort
                $add_revenue_query = $this->add_revenue_DB($current_resort, $gains);
                // Adds the revenue to the revenue table
                //$player_resort = $this->get_player_resort($current_user);
                $add_revenue_history_query_main_table = add_revenue_stat_table($current_resort, $gains, 'revenue', $yesterday_GMT);
                // Adds revenue to the revenue table for current building type
                $add_revenue_history_query_specific_table = add_revenue_stat_table($current_resort, $gains, 'rev_'.$building_type, $yesterday_GMT);
                $message_bonus_building = "Resort ".$created_buildings_array->id_resort." won ".$gains." € (max= ".$max_income_building." €) for building type ".$created_buildings_array->type." (capacity=".$capacity.", visitors=".round($daily_visitors, 0).")\n";                     
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_building_type_".$building_type."]", "generate_revenue_building", $message_bonus_building);
                  
            }
            else {
                // START Adds reporting data to DB
                $reporting_data = $this->lang->line('reporting')['no_building_type'].' '.$building_type.'. '.$this->lang->line('reporting')['building_buildings'];
                $add_reporting_data_db = $this->add_reporting_data_db($current_resort, 'buildings', $reporting_data);
                // END Adds reporting data to DB
            }
        } 
        //return $message_bonus_building;
    }
    
    /**
     * generate_revenue_real_estate     Generates nightly passive rental income for all
     *                                  renting real estate properties across all resorts.
     *                                  Also auto-completes any constructions that have
     *                                  passed their completion date.
     */
    protected function generate_revenue_real_estate() {
        // Auto-complete finished constructions for all resorts
        $this->real_estate_model->auto_complete_constructions_DB();

        // Get all renting properties grouped by resort and type
        $renting_result = $this->real_estate_model->get_all_renting_by_resort_DB();
        if ($renting_result->num_rows() == 0)
            return;

        $yesterday_GMT = $this->yesterdays_date;

        foreach ($renting_result->result() as $row) {
            $id_resort     = (int)$row->id_resort;
            $property_type = $row->property_type;
            $count         = (int)$row->count;

            if (!array_key_exists($property_type, REAL_ESTATE_TYPES))
                continue;

            $type_cfg   = REAL_ESTATE_TYPES[$property_type];
            $net_daily  = round($type_cfg['daily_rent'] * (1 - $type_cfg['property_tax']));
            $total_rent = $net_daily * $count;

            if ($total_rent <= 0)
                continue;

            // Add revenue to resort cash
            $this->add_revenue_DB($id_resort, $total_rent);

            // Add to finance stats
            add_revenue_stat_table($id_resort, $total_rent, 'revenue',          $yesterday_GMT);
            add_revenue_stat_table($id_resort, $total_rent, 'rev_real_estate',  $yesterday_GMT);

            // Log to player activity
            $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);
            if ($currentUserID) {
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $this->lang->load('building', $player_preferred_lang);
                $this->lang->load('logs',     $player_preferred_lang);
                $log_msg = $this->lang->line('building')['real_estate_rent_log'] . ' ' . number_format($total_rent, 0, '.', ' ') . ' €';
                $this->logs_model->call_notification_DB([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['revenues'],
                    'data'      => $log_msg,
                ]);
                log_user_action([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['revenues'],
                    'data'      => $log_msg,
                ]);
            }

            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$id_resort."]", "generate_revenue_real_estate", "Resort ".$id_resort." earned ".$total_rent." € from ".$count." ".$property_type." properties.\n");
        }
    }

    /**
     * generate_revenue_scenic_lifts    Credits daily sightseeing revenue, deducts operating cost,
     *                                   and adds a reputation bonus for each resort with scenic lifts enabled.
     *
     * @param array $visitor_calculations   Array produced by visitor_calculations()
     */
    protected function generate_revenue_scenic_lifts($visitor_calculations) {
        foreach ($visitor_calculations as $resort_data) {
            $id_resort          = $resort_data['id_resort'];
            $daily_visitors     = $resort_data['daily_visitors'];
            $bonus_peak_season  = $resort_data['bonus_peak_season'] ?? 1.0;

            $settings = $this->scenic_lift_model->get_settings_DB($id_resort);
            if ((int)$settings->is_enabled !== 1) {
                continue;
            }

            $ticket_price      = (int)$settings->ticket_price;
            $capacity_level    = max(SCENIC_LIFT_MIN_CAPACITY, min(SCENIC_LIFT_MAX_CAPACITY, (int)$settings->capacity_level));
            $seasonal_discount    = (int)$settings->seasonal_discount;
            $tour_theme           = (int)$settings->tour_theme;
            $photography_package  = (int)$settings->photography_package;
            $vip_gondola          = (int)$settings->vip_gondola;

            // Capacity level scales tourist throughput (level/default, same pattern as Retail stock_level/3)
            $capacity_multiplier = $capacity_level / SCENIC_LIFT_DEFAULT_CAPACITY;

            // Seasonal discount: during off-peak, boost visitor count but reduce effective ticket price
            $visitor_multiplier       = 1.0;
            $effective_price_factor   = 1.0;
            $is_offpeak               = ($bonus_peak_season < SCENIC_LIFT_OFFPEAK_THRESHOLD);
            if ($seasonal_discount === 1 && $is_offpeak) {
                $visitor_multiplier     = SCENIC_LIFT_DISCOUNT_VISITOR_BOOST;
                $effective_price_factor = SCENIC_LIFT_DISCOUNT_PRICE_FACTOR;
            }

            // Tour theme modifiers
            $theme_visitor_boost = 1.0;
            $theme_price_factor  = 1.0;
            $theme_rep_bonus     = 0;
            $theme_extra_cost    = 0;
            if ($tour_theme === SCENIC_LIFT_THEME_NATURE) {
                $theme_visitor_boost = SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST;
                $theme_rep_bonus     = SCENIC_LIFT_THEME_NATURE_REP_BONUS;
                $theme_extra_cost    = SCENIC_LIFT_THEME_NATURE_EXTRA_COST;
            } elseif ($tour_theme === SCENIC_LIFT_THEME_SUNSET) {
                $theme_price_factor  = SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR;
                $theme_rep_bonus     = SCENIC_LIFT_THEME_SUNSET_REP_BONUS;
                $theme_extra_cost    = SCENIC_LIFT_THEME_SUNSET_EXTRA_COST;
            } elseif ($tour_theme === SCENIC_LIFT_THEME_ADVENTURE) {
                $theme_visitor_boost = SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST;
                $theme_rep_bonus     = SCENIC_LIFT_THEME_ADVENTURE_REP_BONUS;
                $theme_extra_cost    = SCENIC_LIFT_THEME_ADVENTURE_EXTRA_COST;
            }

            // VIP gondola modifiers
            $vip_visitor_factor = 1.0;
            $vip_price_factor   = 1.0;
            $vip_rep_bonus      = 0;
            $vip_extra_cost     = 0;
            if ($vip_gondola === 1) {
                $vip_visitor_factor = SCENIC_LIFT_VIP_VISITOR_FACTOR;
                $vip_price_factor   = SCENIC_LIFT_VIP_PRICE_MULTIPLIER;
                $vip_rep_bonus      = SCENIC_LIFT_VIP_REP_BONUS;
                $vip_extra_cost     = SCENIC_LIFT_VIP_DAILY_COST;
            }

            // Photography package extra cost
            $photo_extra_cost = ($photography_package === 1) ? SCENIC_LIFT_PHOTO_DAILY_COST : 0;

            // Revenue from sightseeing tourists
            $scenic_visitors    = round($daily_visitors * SCENIC_LIFT_TOURIST_RATIO * $capacity_multiplier * $visitor_multiplier * $theme_visitor_boost * $vip_visitor_factor);
            $effective_price    = $ticket_price * $effective_price_factor * $theme_price_factor * $vip_price_factor;
            $photo_revenue      = ($photography_package === 1) ? round($scenic_visitors * SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR) : 0;
            $revenue            = round($scenic_visitors * $effective_price) + $photo_revenue;

            // Daily cost scales with capacity level (level 3 = base cost; each step adds/saves CAPACITY_COST_PER_LEVEL)
            $actual_daily_cost  = SCENIC_LIFT_DAILY_COST
                + ($capacity_level - SCENIC_LIFT_DEFAULT_CAPACITY) * SCENIC_LIFT_CAPACITY_COST_PER_LEVEL
                + $theme_extra_cost
                + $photo_extra_cost
                + $vip_extra_cost;

            // Total reputation bonus
            $total_rep_bonus = SCENIC_LIFT_REP_BONUS_PER_DAY + $theme_rep_bonus + $vip_rep_bonus;

            $net = $revenue - $actual_daily_cost;

            $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);

            $this->db->trans_start();
            // Apply net cash change
            if ($net >= 0) {
                $this->db->set('cash', 'cash+' . $net, FALSE);
            } else {
                $this->db->set('cash', 'cash-' . abs($net), FALSE);
            }
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_resorts');
            // Apply reputation bonus
            $this->db->set('reputation', 'reputation+' . $total_rep_bonus, FALSE);
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_resorts');
            $this->db->trans_complete();

            if ($this->db->trans_status() !== FALSE) {
                add_revenue_stat_table($id_resort, max(0, $revenue), 'rev_other');

                $log_data = $this->lang->line('logs')['scenic_lift_revenue_log'] . ' ' . number_format($net, 0, '.', ' ') . ' € (' . $scenic_visitors . ' visitors × ' . $ticket_price . ' €, capacity ' . $capacity_level . ', theme ' . $tour_theme . ($photography_package ? ', photo' : '') . ($vip_gondola ? ', VIP' : '') . ')';
                $this->logs_model->call_notification_DB([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['revenues'],
                    'data'      => $log_data,
                ]);
                log_user_action([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['revenues'],
                    'data'      => $log_data,
                ]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "generate_revenue_scenic_lifts", "Resort {$id_resort}: scenic lift net {$net} € ({$scenic_visitors} visitors × {$ticket_price} €, capacity={$capacity_level}, cost={$actual_daily_cost} €, theme={$tour_theme}, photo=" . ($photography_package ? 'on' : 'off') . ", vip=" . ($vip_gondola ? 'on' : 'off') . ", discount=" . ($seasonal_discount ? ($is_offpeak ? 'applied' : 'inactive') : 'off') . ").\n");
            }
        }
    }

    /**
     * generate_investment_interest     Applies daily compound interest to all resort savings accounts
     */
    protected function generate_investment_interest() {
        $daily_rate = BANK_INVESTMENT_ANNUAL_RATE / 100 / 365;
        $all_investments = $this->bank_model->get_all_investments_DB();
        if ($all_investments->num_rows() == 0)
            return;

        foreach ($all_investments->result() as $row) {
            $id_resort  = (int)$row->id_resort;
            $balance    = (int)$row->balance;
            $interest   = (int)floor($balance * $daily_rate);
            if ($interest <= 0)
                continue;

            $new_balance = $balance + $interest;
            $now = $this->todays_datetime;
            $this->bank_model->upsert_investment_DB($id_resort, $new_balance, $now);
            $this->add_revenue_DB($id_resort, $interest);
            add_revenue_stat_table($id_resort, $interest, 'revenue',      $this->yesterdays_date);
            add_revenue_stat_table($id_resort, $interest, 'rev_other',    $this->yesterdays_date);

            $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);
            if ($currentUserID) {
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $this->lang->load('bank', $player_preferred_lang);
                $this->lang->load('logs', $player_preferred_lang);
                $log_msg = $this->lang->line('bank')['investment_interest_log'].' '.number_format($interest, 0, '.', ' ').' €';
                $this->logs_model->call_notification_DB([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['revenues'],
                    'data'      => $log_msg,
                ]);
                log_user_action([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['revenues'],
                    'data'      => $log_msg,
                ]);
            }
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$id_resort."]", "generate_investment_interest", "Resort ".$id_resort." earned ".$interest." € in savings interest (balance=".$new_balance.").\n");
        }
    }

    /**
     * generate_revenue_season_passes   Distributes daily season pass revenue to each resort that
     *                                  has season passes enabled.
     *
     * For each enabled resort:
     *   1. If a new season has started, recalculate passes_sold.
     *   2. Credit daily revenue = passes_sold × price / SEASON_PASS_SEASON_LENGTH.
     *   3. Award a loyalty reputation bonus when passes_sold >= threshold.
     */
    protected function generate_revenue_season_passes() {
        $all_passes = $this->season_pass_model->get_all_enabled_DB();
        if ($all_passes->num_rows() === 0)
            return;

        $yesterday_GMT = $this->yesterdays_date;

        foreach ($all_passes->result() as $row) {
            $id_resort      = (int)$row->id_resort;
            $price          = (int)$row->season_pass_price;
            $passes_sold    = (int)$row->passes_sold;
            $stored_season  = (int)$row->current_season;
            $early_bird_enabled = isset($row->early_bird_enabled) ? (int)$row->early_bird_enabled : 0;

            $current_season = (int)get_current_season($id_resort);

            // Recalculate passes at the start of each new season
            if ($current_season != $stored_season) {
                $resort_info = $this->resort_model->display_resort_info_DB($id_resort)->row();
                $reputation  = $resort_info ? (int)$resort_info->reputation : 0;
                $passes_sold = $this->season_pass_model->calculate_passes_sold($reputation, $price, (bool)$early_bird_enabled);
                $this->season_pass_model->update_passes_sold_DB($id_resort, $passes_sold, $current_season);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "generate_revenue_season_passes", "Season {$current_season}: recalculated passes_sold={$passes_sold} (rep={$reputation}, price={$price}, early_bird={$early_bird_enabled}).\n");
            }

            if ($passes_sold <= 0 || $price <= 0)
                continue;

            // Daily revenue share — early-bird discount reduces effective price per pass
            $effective_price = $price;
            if ($early_bird_enabled && isset($row->early_bird_discount_pct) && $row->early_bird_discount_pct > 0) {
                $effective_price = (int)round($price * (1 - $row->early_bird_discount_pct / 100));
            }
            $daily_revenue = (int)floor($passes_sold * $effective_price / SEASON_PASS_SEASON_LENGTH);
            if ($daily_revenue > 0) {
                $this->add_revenue_DB($id_resort, $daily_revenue);
                add_revenue_stat_table($id_resort, $daily_revenue, 'revenue',      $yesterday_GMT);
                add_revenue_stat_table($id_resort, $daily_revenue, 'rev_skipass',  $yesterday_GMT);

                $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);
                if ($currentUserID) {
                    $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                    $this->lang->load('building', $player_preferred_lang);
                    $this->lang->load('logs',     $player_preferred_lang);
                    $log_msg = $this->lang->line('building')['season_pass_title'] . ': +' . number_format($daily_revenue, 0, '.', ' ') . ' € (' . $passes_sold . ' ' . $this->lang->line('building')['season_pass_passes_unit'] . ')';
                    $this->logs_model->call_notification_DB([
                        'id_player' => $currentUserID,
                        'type'      => $this->lang->line('logs')['revenues'],
                        'data'      => $log_msg,
                    ]);
                    log_user_action([
                        'id_player' => $currentUserID,
                        'type'      => $this->lang->line('logs')['revenues'],
                        'data'      => $log_msg,
                    ]);
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "generate_revenue_season_passes", "Resort {$id_resort} earned {$daily_revenue} € from season passes ({$passes_sold} passes × {$price} € / " . SEASON_PASS_SEASON_LENGTH . " days).\n");
            }

            // Loyalty reputation bonus
            if ($passes_sold >= SEASON_PASS_HIGH_SALES_THRESHOLD) {
                $rep_bonus = SEASON_PASS_LOYALTY_REP_BONUS;
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation+' . $rep_bonus, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "generate_revenue_season_passes", "Resort {$id_resort} earned +{$rep_bonus} reputation from season pass loyalty ({$passes_sold} passes sold).\n");
            }
        }
    }

    /**
     * generate_cost_building                  Generates cost (euros) for the buildings of each player
     * 
     * @param type $visitor_calculations        Number of visitors given by previous functions (all)
     * @param type $building_type               Building type (cannon...)
     * @return string                           Returns info message indicating the cost of the building (in euros)
     */    
    protected function generate_cost_building($visitor_calculations, $building_type){
        //$message_cost_building = '';
        foreach ($visitor_calculations as $visitor_calculations_Array){ 
            $current_resort = $visitor_calculations_Array['id_resort'];
            $data_get_info_created_buildings = $this->get_info_created_buildings($current_resort, $building_type);
            //var_dump($data_get_info_created_buildings);
            if ($data_get_info_created_buildings->num_rows() > 0) {
                $cost_building = '0';
                foreach ($data_get_info_created_buildings->result() as $created_buildings_array){
                    $cost_building = $cost_building + $created_buildings_array->daily_cost * $created_buildings_array->count_level;
                }

                // Climate change: apply snowmaking cost multiplier for cannon buildings
                if ($building_type === 'cannon') {
                    $climate = $this->climate_change_model->get_climate_data_DB($current_resort);
                    if ($climate !== FALSE && (int)$climate->climate_level > 0) {
                        $cost_mult = 1.0 + (CLIMATE_COST_MULT_PER_LEVEL * (int)$climate->climate_level);
                        // snowmaking investment halves the extra cost
                        if ($climate->snowmaking_invest == 1)
                            $cost_mult = 1.0 + ((CLIMATE_COST_MULT_PER_LEVEL * (int)$climate->climate_level) / 2);
                        $cost_building = round($cost_building * $cost_mult);
                        $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_cost_building", "Climate cannon cost multiplier x".$cost_mult." applied. Total: ".$cost_building." €\n");
                    }
                }

                $take_cost_query = $this->take_cost_DB($current_resort, $cost_building);
                // Adds the cost to the cost table
                $player_resort = $this->get_player_resort($current_resort);
                $add_cost_history_query = add_cost_stat_table($player_resort, $cost_building, 'cost_upkeep', $this->yesterdays_date);
                $add_cost_history_query = add_cost_stat_table($player_resort, $cost_building, 'expenses', $this->yesterdays_date);
                
                $message_cost_building = "Resort ".$current_resort." spent ".$cost_building." € for building type ".$created_buildings_array->type."\n"; 
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_building_type_".$building_type."]", "generate_cost_building", $message_cost_building);
            }
        } 
        //return $message_cost_building;
    }
    
    
    /**
     * generate_cost_equipment                  Generates cost (euros) for the equipments of each player
     * 
     * @param type $visitor_calculations        Number of visitors given by previous functions (all)
     * @param type $equipment_type               Equipment type (groomer...)
     * @return string                           Returns info message indicating the cost of the equipment (in euros)
     */    
    protected function generate_cost_equipment($visitor_calculations, $equipment_type){
        //$message_cost_equipment = '';
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];

            // For snow groomers (type 1) apply per-groomer intensity multiplier to the daily cost
            if ($equipment_type == '1') {
                $groomers_query = $this->db
                    ->select('game_equipments.daily_cost, game_equipments.type,
                              game_purchased_equipments_tbl.id_resort,
                              game_purchased_equipments_tbl.grooming_intensity,
                              game_purchased_equipments_tbl.grooming_active')
                    ->from('game_equipments')
                    ->join('game_purchased_equipments as game_purchased_equipments_tbl',
                           'game_equipments.type = game_purchased_equipments_tbl.type
                            AND game_equipments.level = game_purchased_equipments_tbl.level', 'inner')
                    ->where('game_purchased_equipments_tbl.id_resort', $current_resort)
                    ->where('game_purchased_equipments_tbl.type', '1')
                    ->where('game_purchased_equipments_tbl.delivered', '1')
                    ->where('(game_purchased_equipments_tbl.grooming_active IS NULL OR game_purchased_equipments_tbl.grooming_active = 1)', NULL, FALSE)
                    ->get();

                if ($groomers_query->num_rows() > 0) {
                    $cost_equipment = 0;
                    foreach ($groomers_query->result() as $gr) {
                        $gi = $gr->grooming_intensity ?? 'standard';
                        if ($gi === 'light') {
                            $multiplier = GROOMER_INTENSITY_LIGHT;
                        } else if ($gi === 'intensive') {
                            $multiplier = GROOMER_INTENSITY_INTENSIVE;
                        } else {
                            $multiplier = GROOMER_INTENSITY_STANDARD;
                        }
                        $cost_equipment += $gr->daily_cost * $multiplier;
                    }
                    $cost_equipment = round($cost_equipment);
                    $take_cost_query = $this->take_cost_DB($current_resort, $cost_equipment);
                    $player_resort = $this->get_player_resort($current_resort);
                    $add_cost_history_query = add_cost_stat_table($player_resort, $cost_equipment, 'cost_upkeep', $this->yesterdays_date);
                    $add_cost_history_query = add_cost_stat_table($player_resort, $cost_equipment, 'expenses', $this->yesterdays_date);
                    $message_cost_equipment = "Resort ".$current_resort." spent ".$cost_equipment." € for equipment type 1 (groomer, intensity-adjusted)\n";
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_equipment_type_1]", "generate_cost_equipment", $message_cost_equipment);
                }
                continue;
            }

            $data_get_info_created_equipments = $this->get_info_purchased_equipments($current_resort, $equipment_type);
            //var_dump($data_get_info_created_equipments);
            if ($data_get_info_created_equipments->num_rows() > 0) {
                $cost_equipment = '0';
                foreach ($data_get_info_created_equipments->result() as $created_equipments_array){
                    $cost_equipment = $cost_equipment + $created_equipments_array->daily_cost * $created_equipments_array->count_level;
                }
                $take_cost_query = $this->take_cost_DB($current_resort, $cost_equipment);
                // Adds the cost to the cost table
                $player_resort = $this->get_player_resort($current_resort);
                $add_cost_history_query = add_cost_stat_table($player_resort, $cost_equipment, 'cost_upkeep', $this->yesterdays_date);
                $add_cost_history_query = add_cost_stat_table($player_resort, $cost_equipment, 'expenses', $this->yesterdays_date);
                
                $message_cost_equipment = "Resort ".$created_equipments_array->id_resort." spent ".$cost_equipment." € for equipment type ".$created_equipments_array->type."\n"; 
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_equipment_type_".$equipment_type."]", "generate_cost_equipment", $message_cost_equipment);
            }
        } 
        //return $message_cost_equipment;
    }
    
    /**
     * generate_cost_lift                  Generates cost (euros) for the open lifts of each player
     * 
     * @param type $building_type               Building type (lift...)
     * @return string                           Returns info message indicating the cost of the lift (in euros)
     */    
    protected function generate_cost_lift($building_type){
        //$message_cost_lift = '';
        $opened_lifts = $this->list_all_opened_lifts_and_daily_cost();
        // Group per resort and apply age-based cost multiplier per lift
        $resort_totals = [];
        foreach ($opened_lifts->result() as $lift_row) {
            $age_seasons    = $this->lift_age_seasons($lift_row->install_date);
            $cost_mult      = 1 + min($age_seasons * LIFT_AGE_COST_MULTIPLIER_PER_YEAR, 1.0);
            $effective_cost = round($lift_row->daily_cost * $cost_mult);
            $resort_totals[$lift_row->id_resort] = ($resort_totals[$lift_row->id_resort] ?? 0) + $effective_cost;
        }
        foreach ($resort_totals as $current_resort => $total_daily_cost) {
            // Equipment sponsorship: reduce maintenance cost
            $saving_pct = $this->sponsorship_model->get_maintenance_saving_pct($current_resort);
            if ($saving_pct > 0) {
                $saved = (int)round($total_daily_cost * $saving_pct);
                $total_daily_cost -= $saved;
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_cost_lift", "Equipment sponsor saved {$saved} € on lift maintenance (saving_pct={$saving_pct}).\n");
            }
            $take_cost_query = $this->take_cost_DB($current_resort, $total_daily_cost);
            // Adds the cost to the cost table
            $add_cost_history_query = add_cost_stat_table($current_resort, $total_daily_cost, 'cost_upkeep', $this->yesterdays_date);
            $add_cost_history_query = add_cost_stat_table($current_resort, $total_daily_cost, 'expenses', $this->yesterdays_date);

            $message_cost_lift = "Resort ".$current_resort." spent ".$total_daily_cost." € for its lifts\n"; 
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."_building_type_".$building_type."]", "generate_cost_lift", $message_cost_lift);
        } 
        //return $message_cost_lift;
    }
    
    /**
     * generate_cost_energy     Calculates and deducts the daily grid electricity cost
     *                          for lifts and snow cannons, offset by renewable production
     *                          (solar panels and hydro plant).
     *
     *  Consumption:
     *    - Each open lift:          ENERGY_LIFT_KWH_PER_DAY kWh
     *    - Each active snow cannon: ENERGY_CANNON_KWH_PER_DAY kWh
     *  Production:
     *    - Each solar panel unit:   ENERGY_SOLAR_KWH_PER_PANEL kWh
     *    - Hydro plant (if built):  ENERGY_HYDRO_KWH_PER_DAY kWh
     *  Net grid cost = max(0, consumption - production) * ENERGY_GRID_COST_PER_KWH €
     *
     * Note: Night skiing electricity is handled separately in revenue_night_skiing().
     *
     * @param array $visitor_calculations   Resort array from visitor_calculations()
     */
    protected function generate_cost_energy($visitor_calculations) {
        foreach ($visitor_calculations as $vc) {
            $current_resort = $vc['id_resort'];

            // --- Consumption ---
            $open_lifts = (int)$this->db
                ->where('id_resort', $current_resort)
                ->where('id_status', '1')
                ->count_all_results('game_created_lifts');

            $active_cannons = (int)$this->db
                ->where('id_resort', $current_resort)
                ->where('type', 'cannon')
                ->where('id_status', '1')
                ->count_all_results('game_created_buildings');

            $total_kwh = ($open_lifts * ENERGY_LIFT_KWH_PER_DAY)
                       + ($active_cannons * ENERGY_CANNON_KWH_PER_DAY);

            if ($total_kwh <= 0) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_cost_energy", "No energy consumption.\n");
                continue;
            }

            // --- Production ---
            $energy_settings = $this->energy_model->get_energy_settings_DB($current_resort);
            $solar_kwh = (int)$energy_settings->solar_panels * ENERGY_SOLAR_KWH_PER_PANEL;
            $hydro_kwh = ((int)$energy_settings->hydro_plant === 1) ? ENERGY_HYDRO_KWH_PER_DAY : 0;
            $total_production_kwh = $solar_kwh + $hydro_kwh;

            // --- Net grid cost ---
            $net_kwh   = max(0, $total_kwh - $total_production_kwh);
            $grid_cost = (int)round($net_kwh * ENERGY_GRID_COST_PER_KWH);

            if ($grid_cost <= 0) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_cost_energy", "Renewable production covers full demand (".$total_kwh." kWh). No grid cost.\n");
                continue;
            }

            // Deduct cost
            $this->take_cost_DB($current_resort, $grid_cost);
            $player_resort = $this->get_player_resort($current_resort);
            add_cost_stat_table($player_resort, $grid_cost, 'cost_upkeep', $this->yesterdays_date);
            add_cost_stat_table($player_resort, $grid_cost, 'expenses',    $this->yesterdays_date);

            // Log
            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);
            $log_data = $this->lang->line('logs')['energy_grid_cost'] . number_format($grid_cost, 0, '.', ' ') . ' €';
            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $log_data]);
            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $log_data]);

            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_cost_energy", "Resort ".$current_resort." paid ".$grid_cost." € for grid electricity (".$net_kwh." kWh net, ".$open_lifts." lifts, ".$active_cannons." cannons).\n");
        }
    }

    /**
     * add_snow_from_cannons                  Adds snow to resort if the player has opened snow cannons.
     *                                        Requires: temperature below freezing, water in reservoir, staff present.
     *
     * @param array  $visitor_calculations    Per-resort visitor data
     * @param string $building_type           Building type ('cannon')
     * @param int    $weather_snow_level      Today's weather snow level change (negative = above freezing)
     * @return string                         Returns info message indicating the amount of snow added (cm)
     */
    protected function add_snow_from_cannons($visitor_calculations, $building_type, $weather_snow_level = 0){
        // If snow is melting (weather change < 0) temperature is above 0°C — cannons cannot produce snow
        $above_freezing = ((int)$weather_snow_level < 0);

        //$message_snow_added = '';
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];

            // Temperature check: skip if above freezing
            if ($above_freezing) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Temperature above freezing — building cannons skipped.\n");
                continue;
            }

            // Schedule check: respect the resort's snowmaking schedule
            $schedule_mask   = $this->resort_model->get_snowmaking_schedule_DB($current_resort);
            $today_day_mask  = 1 << ((int)gmdate('N') - 1); // 'N' = 1 (Mon) to 7 (Sun); bit 0 = Mon
            if (!($schedule_mask & $today_day_mask)) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Snowmaking schedule: cannons disabled for today (".gmdate('l')."). Skipped.\n");
                continue;
            }

            // Staff check: at least SNOWMAKING_MIN_STAFF snowmakers must be hired
            $snowmaker_count = $this->staff_model->count_hired_snowmakers_DB($current_resort);
            if ($snowmaker_count < SNOWMAKING_MIN_STAFF) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "No snowmaking staff — building cannons skipped (hired: ".$snowmaker_count.", required: ".SNOWMAKING_MIN_STAFF.").\n");
                continue;
            }

            // Apply snowmaker skill bonus: avg effective efficiency scales snow output
            $snowmaker_efficiency_query = $this->db
                ->select('AVG(LEAST(gs.efficiency + (ghs.skill_level - 1) * ' . (int)STAFF_SKILL_EFFICIENCY_BONUS . ', 100)) as avg_eff')
                ->from('game_hired_staff ghs')
                ->join('game_staff gs', 'gs.id_staff = ghs.id_staff', 'inner')
                ->where('ghs.id_resort', $current_resort)
                ->where('gs.position', 'snowmaker')
                ->get();
            $snowmaker_eff_row = $snowmaker_efficiency_query->row();
            $snowmaker_efficiency_mult = isset($snowmaker_eff_row->avg_eff) ? (max(50, (float)$snowmaker_eff_row->avg_eff) / 100) : 1.0;

            // Water reservoir check: skip if not purchased
            if (!$this->resort_model->get_water_reservoir_purchased_DB($current_resort)) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Water reservoir not purchased — building cannons skipped.\n");
                continue;
            }

            // Water reservoir check: skip if empty
            $water_level = $this->resort_model->get_water_reservoir_DB($current_resort);
            if ($water_level <= 0) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Water reservoir empty — building cannons skipped.\n");
                continue;
            }

            // Auto-start cannons if snow level is below the auto-start threshold
            $cannon_auto_start = $this->resort_model->get_cannon_auto_start_DB($current_resort);
            $current_snow = (int)$this->show_snow_level($current_resort);
            if ($cannon_auto_start > 0 && $current_snow < $cannon_auto_start) {
                $auto_start_data = ['id_status' => '1'];
                $this->building_model->update_open_close_building_db($current_resort, $building_type, $auto_start_data);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Snow level ".$current_snow." cm is below auto-start threshold ".$cannon_auto_start." cm. Cannons auto-started.\n");
            }

            $data_get_info_created_buildings = $this->get_info_created_buildings($current_resort, $building_type);
            if ($data_get_info_created_buildings->num_rows() > 0) {
                // Check if the resort has a snow target set and already reached it
                $cannon_target = $this->resort_model->get_cannon_target_snow_DB($current_resort);
                if ($cannon_target > 0 && $current_snow >= $cannon_target) {
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Snow target of ".$cannon_target." cm already reached (current: ".$current_snow." cm). Cannons skipped.\n");
                    continue;
                }

                $added_level = '0';
                $active_cannon_count = 0;
                foreach ($data_get_info_created_buildings->result() as $created_buildings_array){
                    $added_level = $added_level + $created_buildings_array->capacity * $created_buildings_array->count_level;
                    $active_cannon_count += (int)$created_buildings_array->count_level;
                }

                // Apply snowmaking mode multiplier
                $snowmaking_mode = $this->resort_model->get_snowmaking_mode_DB($current_resort);
                if ($snowmaking_mode === 'eco') {
                    $added_level = round($added_level * SNOWMAKING_MODE_ECO_OUTPUT);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Eco mode: output reduced to ".round(SNOWMAKING_MODE_ECO_OUTPUT * 100)."% (".$added_level." cm).\n");
                } elseif ($snowmaking_mode === 'boost') {
                    $added_level = round($added_level * SNOWMAKING_MODE_BOOST_OUTPUT);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Boost mode: output increased to ".round(SNOWMAKING_MODE_BOOST_OUTPUT * 100)."% (".$added_level." cm).\n");
                }

                // Apply snowmaker efficiency multiplier (skill-level-aware avg efficiency)
                $added_level = round($added_level * $snowmaker_efficiency_mult);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Snowmaker efficiency mult: ".$snowmaker_efficiency_mult." → adjusted added_level: ".$added_level." cm.\n");

                // If a target is set, only add snow up to the target
                if ($cannon_target > 0 && ($current_snow + $added_level) > $cannon_target) {
                    $added_level = $cannon_target - $current_snow;
                }

                // Deduct water from reservoir (capped at 0)
                $water_depletion = min($water_level, $active_cannon_count * SNOWMAKING_WATER_PER_CANNON_NIGHT);
                $new_water = max(0, $water_level - $water_depletion);
                $this->resort_model->update_water_reservoir_DB($current_resort, $new_water);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_snow_from_cannons", "Water reservoir depleted by ".$water_depletion."% (new level: ".$new_water."%).\n");

                $add_snow = $this->add_remove_snow_db($added_level, $current_resort);
                $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['cannon_added_total'].' '.$added_level.' '.$this->lang->line('logs')['cm_of_snow'].'.') );   // Add a log row to the game_player_logs table
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['cannon_added_total'].' '.$added_level.' '.$this->lang->line('logs')['cm_of_snow'].'.') );   // Add a log row to the game_player_logs table
                
                $message_snow_added = "Resort ".$created_buildings_array->id_resort." got ".$added_level." cm of snow added to his resort thanks to ".$created_buildings_array->type."\n"; 
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$created_buildings_array->id_resort."]", "add_snow_from_cannons", $message_snow_added);
            }
        } 
        //return $message_snow_added;
    }

    /**
     * charge_snowmaking_electricity    Deducts electricity cost for active building cannons
     *                                  each night.
     *
     * @param array $visitor_calculations   Array of visitor data per resort
     */
    protected function charge_snowmaking_electricity($visitor_calculations) {
        foreach ($visitor_calculations as $visitor_calculations_Array) {
            $current_resort = $visitor_calculations_Array['id_resort'];

            $elec_cost = 0;

            // Building cannons: count active (id_status=1) cannons
            $cannon_data = $this->get_info_created_buildings($current_resort, 'cannon');
            if ($cannon_data->num_rows() > 0) {
                $base_cannon_cost = 0;
                foreach ($cannon_data->result() as $cannon_row) {
                    $base_cannon_cost += (int)$cannon_row->count_level * SNOWMAKING_ELECTRICITY_PER_CANNON;
                }
                // Apply snowmaking mode cost multiplier to cannon electricity
                $snowmaking_mode = $this->resort_model->get_snowmaking_mode_DB($current_resort);
                if ($snowmaking_mode === 'eco') {
                    $base_cannon_cost = round($base_cannon_cost * SNOWMAKING_MODE_ECO_COST);
                } elseif ($snowmaking_mode === 'boost') {
                    $base_cannon_cost = round($base_cannon_cost * SNOWMAKING_MODE_BOOST_COST);
                }
                $elec_cost += $base_cannon_cost;
            }

            if ($elec_cost <= 0)
                continue;

            $this->take_cost_DB($current_resort, $elec_cost);
            $player_resort = $this->get_player_resort($current_resort);
            add_cost_stat_table($player_resort, $elec_cost, 'cost_upkeep', $this->yesterdays_date);
            add_cost_stat_table($player_resort, $elec_cost, 'expenses',    $this->yesterdays_date);
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "charge_snowmaking_electricity", "Resort ".$current_resort." paid ".$elec_cost." € snowmaking electricity.\n");
        }
    }

    /**
     * update_water_reservoir   Refills the water reservoir based on tonight's weather.
     *                          Called after snowmaking so depletion is applied first.
     *
     * @param array  $visitor_calculations  Per-resort visitor data (provides resort IDs)
     * @param string $weather_name_english  English name of tonight's weather condition
     */
    protected function update_water_reservoir($visitor_calculations, $weather_name_english) {
        $refill = 0;
        if ($weather_name_english === 'Snowing') {
            $refill = SNOWMAKING_WATER_REFILL_SNOW;
        } elseif ($weather_name_english === 'Raining') {
            $refill = SNOWMAKING_WATER_REFILL_RAIN;
        }

        foreach ($visitor_calculations as $visitor_calculations_Array) {
            $current_resort = $visitor_calculations_Array['id_resort'];
            if ($refill > 0 && $this->resort_model->get_water_reservoir_purchased_DB($current_resort)) {
                $current_level = $this->resort_model->get_water_reservoir_DB($current_resort);
                $new_level = min(100, $current_level + $refill);
                if ($new_level !== $current_level) {
                    $this->resort_model->update_water_reservoir_DB($current_resort, $new_level);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "update_water_reservoir", "Water reservoir refilled by ".$refill."% due to ".$weather_name_english." (new level: ".$new_level."%).\n");
                }
            }
        }
    }

    /**
     * revenue_night_skiing     Applies night skiing bonus revenue and deducts electricity cost.
     *                          Revenue bonus and electricity cost now account for per-trail
     *                          lighting settings (light type, brightness, pole spacing).
     *                          Also handles weather auto-suspend and night ski school revenue.
     *
     * @param array  $visitor_calculations    Array of visitor data per resort (from visitor_calculations)
     * @param string $weather_name            Current weather condition name in English (e.g. 'Sunny', 'Raining')
     */
    protected function revenue_night_skiing($visitor_calculations, $weather_name = ''){
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];

            // Check if night skiing is globally enabled for this resort
            $night_skiing = $this->resort_model->get_night_skiing_status($current_resort);
            if ($night_skiing != 1)
                continue;

            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);

            // Resort-level settings (needed for weather-suspend check)
            $night_settings = $this->night_skiing_model->get_night_settings_DB($current_resort);

            // Weather auto-suspend: skip this resort's night skiing if conditions are bad
            $weather_suspend = ($night_settings && isset($night_settings->night_skiing_weather_suspend))
                ? (int)$night_settings->night_skiing_weather_suspend : 0;
            if ($weather_suspend && in_array($weather_name, NIGHT_SKIING_WEATHER_SUSPEND_CONDITIONS, TRUE)) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Night skiing suspended due to weather: ".$weather_name.".\n");
                continue;
            }

            // Get per-trail settings for all night-skiing-enabled (and open) slopes
            $enabled_trails = $this->night_skiing_model->get_enabled_trails_with_settings_DB($current_resort)->result();
            $ns_trail_count = count($enabled_trails);

            if ($ns_trail_count === 0)
                continue;

            // Compute per-trail electricity cost and revenue multiplier adjustments
            $total_trail_electricity = 0;
            $trail_revenue_multiplier_sum = 0;
            foreach ($enabled_trails as $trail) {
                $lt  = $trail->light_type   ?? 'led';
                $br  = (int)($trail->brightness   ?? 3);
                $ps  = (int)($trail->pole_spacing  ?? 25);

                // Electricity: base per-slope cost * light-type multiplier * brightness factor * spacing multiplier
                $lt_cost = NIGHT_SKIING_LIGHT_TYPE_COST[$lt]    ?? 1.0;
                $ps_cost = NIGHT_SKIING_POLE_SPACING_COST[$ps]  ?? 1.0;
                $brightness_cost_multiplier = 1 + (($br - 1) * NIGHT_SKIING_BRIGHTNESS_COST_FACTOR);
                $trail_elec = NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE * $lt_cost * $brightness_cost_multiplier * $ps_cost;
                $total_trail_electricity += $trail_elec;

                // Revenue: per-trail bonus modifier
                $lt_rev  = NIGHT_SKIING_LIGHT_TYPE_REVENUE[$lt]   ?? 1.0;
                $ps_rev  = NIGHT_SKIING_POLE_SPACING_REVENUE[$ps] ?? 1.0;
                $br_rev  = 1 + (($br - 1) * NIGHT_SKIING_BRIGHTNESS_REVENUE_FACTOR);
                $trail_revenue_multiplier_sum += ($lt_rev * $br_rev * $ps_rev);
            }

            // Effective electricity cost: base + per-trail totals
            $electricity_cost = NIGHT_SKIING_ELECTRICITY_COST + $total_trail_electricity;

            // Effective revenue bonus: base + slope-count factor, weighted by average per-trail multiplier
            $avg_trail_rev_multiplier = ($ns_trail_count > 0) ? ($trail_revenue_multiplier_sum / $ns_trail_count) : 1.0;
            $base_bonus   = NIGHT_SKIING_REVENUE_BONUS + (max(0, $ns_trail_count - 1) * NIGHT_SKIING_SLOPE_REVENUE_FACTOR);
            $effective_bonus = $base_bonus * $avg_trail_rev_multiplier;

            // Calculate bonus revenue (mirrors revenue_visitors formula)
            $skipass_daily   = $visitor_calculations_Array['skipass_daily'];
            $skipass_weekly  = $visitor_calculations_Array['skipass_weekly'];
            $total_visitors  = $visitor_calculations_Array['daily_visitors'];
            $prestige_bonus  = $visitor_calculations_Array['prestige_bonus'];

            $revenue_base = round(($total_visitors * 0.35 * $skipass_daily) + ($total_visitors * 0.65 * $skipass_weekly / 7));
            $revenue_base = round($revenue_base * $prestige_bonus['coef']);

            // Add night ticket price contribution if set
            $night_ticket_price = $night_settings ? (int)$night_settings->night_skiing_ticket_price : 0;
            $night_ticket_revenue = 0;
            
            // DYNAMIC VISITOR DEMAND
            // Calculate day-of-week factor
            $dow = (int)date('N', $this->yesterdays_time); // 1=Mon, 7=Sun
            $dow_factor = NIGHT_SKIING_DOW_FACTOR[$dow] ?? 1.0;
            
            // Calculate season factor based on bonus_peak_season
            $bonus_peak_season = $visitor_calculations_Array['bonus_peak_season'] ?? 1.0;
            if ($bonus_peak_season >= NIGHT_SKIING_PEAK_HIGH_THRESHOLD) {
                $season_factor = NIGHT_SKIING_PEAK_HIGH_FACTOR;
            } elseif ($bonus_peak_season >= 1.0) {
                $season_factor = NIGHT_SKIING_PEAK_NORMAL_FACTOR;
            } else {
                $season_factor = NIGHT_SKIING_OFFPEAK_FACTOR;
            }
            
            // Calculate weather factor
            $weather_factor = NIGHT_SKIING_WEATHER_VISITOR_FACTOR[$weather_name] ?? 1.0;
            
            // Compute dynamic visitor fraction
            $dynamic_visitor_fraction = NIGHT_SKIING_VISITOR_FRACTION * $dow_factor * $season_factor * $weather_factor;
            
            // --- EVENT LOGIC START ---
            $event_bonus_pct = 0;
            $event_revenue_mult = 1.0;
            $event_cost = 0;
            $event_rep = 0;
            $event_type_name = '';

            // Check for scheduled event for yesterday (since cron runs for yesterday)
            $pending_events = $this->night_skiing_model->get_scheduled_events($current_resort, $this->yesterdays_date, $this->yesterdays_date, 'scheduled');
            if ($pending_events === false) $pending_events = [];
            
            if (!empty($pending_events)) {
                $event = $pending_events[0]; // Process one event per night
                $event_bonus_pct    = (float)$event->visitor_bonus_pct;
                $event_revenue_mult = (float)$event->revenue_multiplier;
                $event_cost         = (int)$event->cost;
                $event_rep          = (int)$event->reputation_bonus;
                $event_type_name    = $event->event_type;

                // Removed premature completion marking. Will be marked after processing financials.
                
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Event executed: " . $event_type_name . " (Bonus: +" . $event_bonus_pct . "%, Rev: x" . $event_revenue_mult . ")\n");
            }
            // --- EVENT LOGIC END ---

            // Torchlight descent boosts night visitor count
            $torchlight_active = ($night_settings && isset($night_settings->night_skiing_torchlight))
                ? (int)$night_settings->night_skiing_torchlight : 0;
            $night_visitor_multiplier    = $torchlight_active ? (1 + NIGHT_SKIING_TORCHLIGHT_VISITOR_BONUS) : 1.0;
            
            // Apply event visitor bonus
            if ($event_bonus_pct > 0) {
                $night_visitor_multiplier += ($event_bonus_pct / 100.0);
            }
            $effective_night_visitors    = round($total_visitors * $dynamic_visitor_fraction * $night_visitor_multiplier);
            if ($night_ticket_price > 0) {
                // Estimate night visitors as a fraction of total (dynamic), boosted by torchlight
                $night_ticket_revenue = max(0, $effective_night_visitors * $night_ticket_price);
            }

            // Entertainment multiplier and cost
            $entertainment = ($night_settings && isset($night_settings->night_skiing_entertainment))
                ? $night_settings->night_skiing_entertainment : 'none';
            if (!in_array($entertainment, NIGHT_SKIING_VALID_ENTERTAINMENT, TRUE)) {
                $entertainment = 'none';
            }
            $ent_revenue_multiplier = NIGHT_SKIING_ENTERTAINMENT_REVENUE[$entertainment] ?? 1.0;
            $ent_cost               = NIGHT_SKIING_ENTERTAINMENT_COST[$entertainment]    ?? 0;

            // Safety level cost and reputation bonus
            $safety_level = ($night_settings && isset($night_settings->night_skiing_safety_level))
                ? max(NIGHT_SKIING_SAFETY_MIN_LEVEL, min(NIGHT_SKIING_SAFETY_MAX_LEVEL, (int)$night_settings->night_skiing_safety_level))
                : NIGHT_SKIING_SAFETY_MIN_LEVEL;
            $safety_cost     = NIGHT_SKIING_SAFETY_COST[$safety_level]             ?? 50;
            $safety_rep_gain = NIGHT_SKIING_SAFETY_REPUTATION_BONUS[$safety_level] ?? 0;

            // Apply entertainment multiplier to the base + ticket revenue
            // Also apply event revenue multiplier
            $night_skiing_revenue = max(0, round((round($revenue_base * $effective_bonus) + $night_ticket_revenue) * $ent_revenue_multiplier * $event_revenue_mult));

            // Add bonus revenue
            $this->add_revenue_DB($current_resort, $night_skiing_revenue);
            add_revenue_stat_table($current_resort, $night_skiing_revenue, 'revenue', $this->yesterdays_date);
            add_revenue_stat_table($current_resort, $night_skiing_revenue, 'rev_skipass', $this->yesterdays_date);

            // Deduct electricity cost
            $this->take_cost_DB($current_resort, $electricity_cost);
            $player_resort = $this->get_player_resort($current_resort);
            add_cost_stat_table($player_resort, $electricity_cost, 'cost_upkeep', $this->yesterdays_date);
            add_cost_stat_table($player_resort, $electricity_cost, 'expenses', $this->yesterdays_date);

            // Deduct entertainment cost
            if ($ent_cost > 0) {
                $this->take_cost_DB($current_resort, $ent_cost);
                add_cost_stat_table($player_resort, $ent_cost, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($player_resort, $ent_cost, 'expenses',    $this->yesterdays_date);
            }

            // Deduct safety cost
            if ($safety_cost > 0) {
                $this->take_cost_DB($current_resort, $safety_cost);
                add_cost_stat_table($player_resort, $safety_cost, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($player_resort, $safety_cost, 'expenses',    $this->yesterdays_date);
            }

            // Apply safety reputation bonus
            if ($safety_rep_gain > 0) {
                $this->db->set('reputation', 'reputation + ' . (int)$safety_rep_gain, FALSE);
                $this->db->where('id_resort', $current_resort);
                $this->db->update('game_resorts');
            }
            
            // Process event costs and reputation
            if ($event_cost > 0) {
                $this->take_cost_DB($current_resort, $event_cost);
                add_cost_stat_table($player_resort, $event_cost, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($player_resort, $event_cost, 'expenses',    $this->yesterdays_date);
            }
            if ($event_rep != 0) {
                $this->db->set('reputation', 'reputation + ' . (int)$event_rep, FALSE);
                $this->db->where('id_resort', $current_resort);
                $this->db->update('game_resorts');
            }

            // Night ski school: additional lesson revenue and reputation bonus
            $school_enabled = ($night_settings && isset($night_settings->night_skiing_school_enabled))
                ? (int)$night_settings->night_skiing_school_enabled : 0;
            $school_price   = ($night_settings && isset($night_settings->night_skiing_school_price))
                ? max(0, (int)$night_settings->night_skiing_school_price) : 0;
            if ($school_enabled && $school_price > 0) {
                $lesson_students = round($effective_night_visitors * NIGHT_SKIING_SCHOOL_VISITOR_FRACTION);
                $school_revenue  = max(0, $lesson_students * $school_price);
                if ($school_revenue > 0) {
                    $this->add_revenue_DB($current_resort, $school_revenue);
                    add_revenue_stat_table($current_resort, $school_revenue, 'revenue', $this->yesterdays_date);
                    add_revenue_stat_table($current_resort, $school_revenue, 'rev_skipass', $this->yesterdays_date);
                }
                if (NIGHT_SKIING_SCHOOL_REPUTATION_BONUS > 0) {
                    $this->db->set('reputation', 'reputation + ' . (int)NIGHT_SKIING_SCHOOL_REPUTATION_BONUS, FALSE);
                    $this->db->where('id_resort', $current_resort);
                    $this->db->update('game_resorts');
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Night ski school: ".$school_revenue." € earned (".$lesson_students." students × ".$school_price." €).\n");
            }

            // Torchlight descent: nightly cost, visitor bonus already applied in revenue calc, reputation bonus
            $torchlight = ($night_settings && isset($night_settings->night_skiing_torchlight))
                ? (int)$night_settings->night_skiing_torchlight : 0;
            if ($torchlight) {
                $this->take_cost_DB($current_resort, NIGHT_SKIING_TORCHLIGHT_COST);
                if (NIGHT_SKIING_TORCHLIGHT_REPUTATION_BONUS > 0) {
                    $this->db->set('reputation', 'reputation + ' . (int)NIGHT_SKIING_TORCHLIGHT_REPUTATION_BONUS, FALSE);
                    $this->db->where('id_resort', $current_resort);
                    $this->db->update('game_resorts');
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Torchlight descent: ".NIGHT_SKIING_TORCHLIGHT_COST." € cost, +".NIGHT_SKIING_TORCHLIGHT_REPUTATION_BONUS." rep.\n");
            }

            // Night photography package: additional revenue and reputation bonus
            $photo_enabled = ($night_settings && isset($night_settings->night_skiing_photo_enabled))
                ? (int)$night_settings->night_skiing_photo_enabled : 0;
            $photo_price   = ($night_settings && isset($night_settings->night_skiing_photo_price))
                ? max(0, (int)$night_settings->night_skiing_photo_price) : 0;
            if ($photo_enabled && $photo_price > 0) {
                $photo_customers = round($effective_night_visitors * NIGHT_SKIING_PHOTO_VISITOR_FRACTION);
                $photo_revenue   = max(0, $photo_customers * $photo_price);
                if ($photo_revenue > 0) {
                    $this->add_revenue_DB($current_resort, $photo_revenue);
                    add_revenue_stat_table($current_resort, $photo_revenue, 'revenue', $this->yesterdays_date);
                    add_revenue_stat_table($current_resort, $photo_revenue, 'rev_skipass', $this->yesterdays_date);
                }
                if (NIGHT_SKIING_PHOTO_REPUTATION_BONUS > 0) {
                    $this->db->set('reputation', 'reputation + ' . (int)NIGHT_SKIING_PHOTO_REPUTATION_BONUS, FALSE);
                    $this->db->where('id_resort', $current_resort);
                    $this->db->update('game_resorts');
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Night photography package: ".$photo_revenue." € earned (".$photo_customers." guests × ".$photo_price." €).\n");
            }

            // TRAIL QUALITY DEGRADATION
            // Night skiing increases slope wear; apply quality loss proportional to brightness
            $total_grooming_surcharge = 0;
            foreach ($enabled_trails as $trail) {
                $br = (int)($trail->brightness ?? 3);
                $extra_quality_loss = round(NIGHT_SKIING_QUALITY_LOSS_BASE + ($br - 1) * NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR);

                // Apply quality loss to slope (floor at 0)
                $this->db->set('quality', 'GREATEST(0, quality - ' . (int)$extra_quality_loss . ')', FALSE);
                $this->db->where('id_created_slopes', (int)$trail->id_created_slope);
                $this->db->update('game_created_slopes');

                $total_grooming_surcharge += NIGHT_SKIING_GROOMING_SURCHARGE_PER_TRAIL;
            }

            // Deduct total grooming surcharge
            if ($total_grooming_surcharge > 0) {
                $this->take_cost_DB($current_resort, $total_grooming_surcharge);
                add_cost_stat_table($player_resort, $total_grooming_surcharge, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($player_resort, $total_grooming_surcharge, 'expenses', $this->yesterdays_date);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Trail grooming surcharge: ".$total_grooming_surcharge." € (".$ns_trail_count." trails).\n");
            }

            // NIGHT EVENTS
            // Process any scheduled events for this resort on this date
            $pending_events = $this->night_skiing_model->get_scheduled_events($current_resort, $this->yesterdays_date, $this->yesterdays_date, 'scheduled');
            if ($pending_events === false) $pending_events = [];

            foreach ($pending_events as $event) {
                $event_config = NIGHT_SKIING_EVENTS[$event->event_type] ?? null;
                if (!$event_config) continue;

                // Apply event visitor bonus on top of dynamic visitors
                $event_visitor_bonus = $effective_night_visitors * ($event->visitor_bonus_pct / 100);
                $event_night_visitors = round($effective_night_visitors + $event_visitor_bonus);

                // Extra revenue from event multiplier
                $event_extra_revenue = round($night_skiing_revenue * ($event->revenue_multiplier - 1.0));
                if ($event_extra_revenue > 0) {
                    $this->add_revenue_DB($current_resort, $event_extra_revenue);
                    add_revenue_stat_table($current_resort, $event_extra_revenue, 'revenue', $this->yesterdays_date);
                    add_revenue_stat_table($current_resort, $event_extra_revenue, 'rev_skipass', $this->yesterdays_date);
                }

                // Deduct event cost
                if ($event->cost > 0) {
                    $this->take_cost_DB($current_resort, (int)$event->cost);
                    add_cost_stat_table($player_resort, (int)$event->cost, 'cost_upkeep', $this->yesterdays_date);
                    add_cost_stat_table($player_resort, (int)$event->cost, 'expenses', $this->yesterdays_date);
                }

                // Apply reputation bonus
                if ((int)$event->reputation_bonus > 0) {
                    $this->db->set('reputation', 'reputation + ' . (int)$event->reputation_bonus, FALSE);
                    $this->db->where('id_resort', $current_resort);
                    $this->db->update('game_resorts');
                }

                // Mark event completed
                $this->night_skiing_model->complete_event((int)$event->id);

                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Night event ({$event->event_type}): " . (int)$event->cost . " € cost, " . $event_extra_revenue . " € extra revenue, +" . (int)$event->reputation_bonus . " rep.\n");
            }

            $log_data = $this->lang->line('logs')['night_skiing_revenue'].number_format($night_skiing_revenue, 0, '.', ' ').' € ('.$this->lang->line('logs')['night_skiing_electricity_cost'].number_format($electricity_cost, 0, '.', ' ').' €)';
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $log_data) );
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $log_data) );

            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "revenue_night_skiing", "Night skiing bonus: ".$night_skiing_revenue." € earned (bonus: ".round($effective_bonus * 100, 1)."%, ".$ns_trail_count." trails, ent: ".$entertainment.", safety: ".$safety_level."), ".$electricity_cost." € electricity cost.\n");
        }
    }

    
    /**
     * update_town_development   Updates town growth for each opened resort.
     *
     * Called nightly after visitor/revenue calculations.  For each resort:
     *  – If hotels are open: award growth points based on hotel count + reputation.
     *  – If no hotels are open and the town already has a level: decay growth
     *    points and penalise the resort's reputation.
     *
     * Level-ups (and level-downs due to decay) are applied automatically.
     *
     * @param array $visitor_calculations   Array returned by visitor_calculations()
     */
    protected function update_town_development($visitor_calculations) {
        $thresholds  = TOWN_LEVEL_THRESHOLDS;
        $level_max   = TOWN_LEVEL_MAX;

        foreach ($visitor_calculations as $row) {
            $current_resort = $row['id_resort'];

            // Fetch current town record and resort reputation
            $town        = $this->town_model->get_town_DB($current_resort);
            $resort_info = $this->resort_model->display_resort_info_DB($current_resort)->row();
            $reputation  = $resort_info ? max(0, (int)$resort_info->reputation) : 0;

            $town_level    = $town ? (int)$town->town_level    : 0;
            $growth_points = $town ? (int)$town->growth_points : 0;

            // Count open hotels for this resort
            $hotel_count = $this->town_model->get_open_hotels_count_DB($current_resort);

            if ($hotel_count == 0 && $town_level > 0) {
                // --- Neglect: decay growth and penalise reputation ---
                $growth_points -= TOWN_DECAY_POINTS_PER_NIGHT;

                $reputation_penalty = $town_level * TOWN_NEGLECT_PENALTY_PER_LEVEL;
                $this->db->set('reputation', 'reputation - ' . (int)$reputation_penalty, FALSE);
                $this->db->where('id_resort', $current_resort);
                $this->db->update('game_resorts');

                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$current_resort}]", "update_town_development",
                    "Town neglected. Decay: -" . TOWN_DECAY_POINTS_PER_NIGHT . " pts, reputation penalty: -{$reputation_penalty}.\n");
            } elseif ($hotel_count > 0) {
                // --- Growth: add points from hotels and reputation ---
                $nightly_gain  = ($hotel_count * TOWN_GROWTH_PER_HOTEL)
                               + round($reputation * TOWN_GROWTH_PER_REPUTATION);
                $growth_points += (int)$nightly_gain;

                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$current_resort}]", "update_town_development",
                    "Town growth: +{$nightly_gain} pts (hotels: {$hotel_count}, reputation: {$reputation}).\n");
            }

            // --- Level-up ---
            while ($town_level < $level_max && $growth_points >= $thresholds[$town_level + 1]) {
                $town_level++;
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$current_resort}]", "update_town_development",
                    "Town levelled UP to level {$town_level}.\n");
            }

            // --- Level-down (decay) ---
            while ($town_level > 0 && $growth_points < $thresholds[$town_level]) {
                $town_level--;
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$current_resort}]", "update_town_development",
                    "Town levelled DOWN to level {$town_level}.\n");
            }

            // Clamp growth_points to zero minimum
            $growth_points = max(0, $growth_points);

            // Persist
            $this->town_model->upsert_town_DB($current_resort, [
                'town_level'    => $town_level,
                'growth_points' => $growth_points,
                'updated_at'    => gmdate('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * add_entry_in_activity_logs                  Adds snow to resort if the player has opened snow cannons
     * 
     * @param type $visitor_calculations        Number of visitors given by previous functions (all)
     * @param type $building_type               Building type (cannon...). We use that in case there are more buildings addind snow in the future
     * @return string                           Returns info message indicating the amount of snow added (cm)
     */    
    protected function add_entry_in_activity_logs($visitor_calculations, $type){
        //$message_snow_added = '';
        foreach ($visitor_calculations as $visitor_calculations_Array){
            $current_resort = $visitor_calculations_Array['id_resort'];
            $currentUserID = $this->users_model->get_user_id_from_resortID($current_resort);
            
            $prestige_bonus_yesterday = number_format($this->get_stats_current_day($current_resort, $type, $this->yesterdays_date), 0, '.', ' ');
            
            $data_message = $this->lang->line('logs')['your_visitors_spent'].' '.$prestige_bonus_yesterday.' € '.$this->lang->line('logs')['thanks_to_prestige'];
            
            
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $data_message) );   // Add a log row to the game_player_logs table
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $data_message) );   // Add a log row to the game_player_logs table
                
            $message_prestige_bonus = "Resort ".$current_resort." got ".$prestige_bonus_yesterday." € bonus thanks to its prestige\n"; 
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "add_entry_in_activity_logs", $message_prestige_bonus);
        } 
        //return $message_snow_added;
    }

    /**
     * generate_crisis_events       Randomly triggers one of four rare crisis events for each open resort.
     *
     * Crisis types (5 % chance per resort per night):
     *   - lift_failure     : A random open lift is forced into maintenance; reputation -10.
     *   - avalanche        : A random open slope is closed; snow level -20 cm; reputation -15.
     *   - power_outage     : Emergency costs deducted (15 % of resort cash); reputation -5.
     *   - viral_negative   : Reputation -20.
     *
     * @param object $list_all_opened_resorts   CI result object from list_all_opened_resorts()
     */
    protected function generate_crisis_events($list_all_opened_resorts) {
        $event_types = ['lift_failure', 'avalanche', 'power_outage', 'viral_negative'];

        foreach ($list_all_opened_resorts->result() as $resort_row) {
            $current_resort = $resort_row->id_resort;
            $currentUserID  = $resort_row->id_player;

            // Skip crisis events in easy mode
            $player_difficulty = $this->users_model->get_difficulty_mode($resort_row->id_player);
            if ($player_difficulty == 1) continue;

            if (mt_rand(1, 100) > CRISIS_EVENT_PROBABILITY) {
                continue;
            }

            $event_type = $event_types[array_rand($event_types)];

            $player_preferred_lang = $resort_row->preferred_lang;
            $this->lang->load('logs', $player_preferred_lang);

            $impact_description = '';

            switch ($event_type) {
                case 'lift_failure':
                    $impact_description = $this->crisis_lift_failure($current_resort, $currentUserID, $player_preferred_lang);
                    break;

                case 'avalanche':
                    $impact_description = $this->crisis_avalanche($current_resort, $currentUserID, $player_preferred_lang);
                    break;

                case 'power_outage':
                    $impact_description = $this->crisis_power_outage($current_resort, $currentUserID, $player_preferred_lang);
                    break;

                case 'viral_negative':
                    $impact_description = $this->crisis_viral_negative($current_resort, $currentUserID, $player_preferred_lang);
                    break;
            }

            $this->crisis_events_model->insert_crisis_event_DB(
                $current_resort,
                $currentUserID,
                $event_type,
                $this->todays_datetime,
                $impact_description
            );

            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$current_resort."]", "generate_crisis_events", "Crisis event '".$event_type."' triggered for resort ".$current_resort.".\n");
        }
    }

    /**
     * generate_micro_events    Randomly creates a pending quick-decision micro-event for each open resort.
     *
     * Probability: MICRO_EVENT_PROBABILITY % per resort per night.
     * At most one new pending micro-event is created per resort per night.
     * Events expire after MICRO_EVENT_EXPIRY_HOURS hours.
     *
     * @param object $list_all_opened_resorts   CI result object from list_all_opened_resorts()
     */
    protected function generate_micro_events($list_all_opened_resorts) {
        $event_types = array_keys(Micro_events_model::get_event_definitions());

        foreach ($list_all_opened_resorts->result() as $resort_row) {
            $current_resort = $resort_row->id_resort;
            $currentUserID  = $resort_row->id_player;

            if (mt_rand(1, 100) > MICRO_EVENT_PROBABILITY) {
                continue;
            }

            // Skip if the resort already has a pending micro-event
            if ($this->micro_events_model->count_pending_micro_events_DB($currentUserID) > 0) {
                continue;
            }

            $event_type = $event_types[array_rand($event_types)];
            $created_at = $this->todays_datetime;
            $expires_at = gmdate('Y-m-d H:i:s', strtotime($created_at) + MICRO_EVENT_EXPIRY_HOURS * 3600);

            $this->micro_events_model->insert_micro_event_DB(
                $current_resort,
                $currentUserID,
                $event_type,
                $created_at,
                $expires_at
            );

            $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$current_resort}]", "generate_micro_events", "Micro-event '{$event_type}' created for resort {$current_resort}.\n");
        }
    }

    /**
     * crisis_lift_failure      Handles a major lift failure crisis event.
     *
     * @param int    $id_resort             Resort ID
     * @param int    $id_player             Player ID
     * @param string $player_preferred_lang Player language key
     * @return string                       Human-readable impact description
     */
    protected function crisis_lift_failure($id_resort, $id_player, $player_preferred_lang) {
        $this->lang->load('logs', $player_preferred_lang);

        $open_lift = $this->get_random_open_lift($id_resort);
        $lift_name = '';

        if ($open_lift) {
            $this->set_maintenance_mode($open_lift->id_created_lifts, 0);
            $lift_name = $open_lift->custom_name;
        }

        $this->withdraw_reputation_injuries_DB($id_resort, CRISIS_LIFT_FAILURE_REP_PENALTY);

        $msg = $this->lang->line('logs')['crisis_lift_failure_msg'];
        if ($lift_name !== '') {
            $msg .= ' (' . $lift_name . ')';
        }

        $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);
        log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);

        // Insurance payout for lift accidents
        $insurance = $this->insurance_model->get_settings_DB($id_resort);
        $payout    = 0;
        if ($insurance->plan === 'premium') {
            $payout = INSURANCE_LIFT_PAYOUT_PREMIUM;
        } elseif ($insurance->plan === 'basic') {
            $payout = INSURANCE_LIFT_PAYOUT_BASIC;
        }
        if ($payout > 0) {
            $this->add_revenue_DB($id_resort, $payout);
            add_revenue_stat_table($id_resort, $payout, 'revenue', $this->yesterdays_date);
            $this->insurance_model->record_claim_DB($id_resort, $payout);
            $claim_msg = ($this->lang->line('logs')['insurance_lift_claim'] ?? 'Insurance claim paid (lift accident):') . ' ' . number_format($payout, 0, '.', ' ') . ' €';
            $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['insurance'] ?? 'Insurance', 'data' => $claim_msg]);
            log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['insurance'] ?? 'Insurance', 'data' => $claim_msg]);
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "crisis_lift_failure", "Insurance payout {$payout} € (plan={$insurance->plan}).\n");
        }

        return $msg;
    }

    /**
     * crisis_avalanche     Handles an avalanche crisis event.
     *
     * @param int    $id_resort             Resort ID
     * @param int    $id_player             Player ID
     * @param string $player_preferred_lang Player language key
     * @return string                       Human-readable impact description
     */
    protected function crisis_avalanche($id_resort, $id_player, $player_preferred_lang) {
        $this->lang->load('logs', $player_preferred_lang);

        $open_slope = $this->get_random_open_slope($id_resort);
        $slope_name = '';

        if ($open_slope) {
            $this->db->trans_start();
            $this->db->set('id_status', '2');
            $this->db->where('id_created_slopes', $open_slope->id_created_slopes);
            $this->db->update('game_created_slopes');
            $this->db->trans_complete();
            $slope_name = $open_slope->custom_name;
        }

        $this->add_remove_snow_db(-CRISIS_AVALANCHE_SNOW_LOSS, $id_resort);
        $this->withdraw_reputation_injuries_DB($id_resort, CRISIS_AVALANCHE_REP_PENALTY);

        $msg = $this->lang->line('logs')['crisis_avalanche_msg'];
        if ($slope_name !== '') {
            $msg .= ' (' . $slope_name . ')';
        }

        $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);
        log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);

        return $msg;
    }

    /**
     * crisis_power_outage  Handles a power outage crisis event.
     *
     * @param int    $id_resort             Resort ID
     * @param int    $id_player             Player ID
     * @param string $player_preferred_lang Player language key
     * @return string                       Human-readable impact description
     */
    protected function crisis_power_outage($id_resort, $id_player, $player_preferred_lang) {
        $this->lang->load('logs', $player_preferred_lang);

        $current_cash   = (int)$this->get_data_resort($id_resort, 'cash');
        $emergency_cost = max(CRISIS_POWER_OUTAGE_MIN_COST, (int)round($current_cash * CRISIS_POWER_OUTAGE_COST_PERC));
        $this->take_cost_DB($id_resort, $emergency_cost);
        add_cost_stat_table($id_resort, $emergency_cost, 'cost_upkeep', $this->yesterdays_date);
        add_cost_stat_table($id_resort, $emergency_cost, 'expenses',    $this->yesterdays_date);

        $this->withdraw_reputation_injuries_DB($id_resort, CRISIS_POWER_OUTAGE_REP_PENALTY);

        $msg = $this->lang->line('logs')['crisis_power_outage_msg'] . ' (' . number_format($emergency_cost, 0, '.', ' ') . ' €)';

        $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);
        log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);

        return $msg;
    }

    /**
     * crisis_viral_negative    Handles a viral negative media story crisis event.
     *
     * @param int    $id_resort             Resort ID
     * @param int    $id_player             Player ID
     * @param string $player_preferred_lang Player language key
     * @return string                       Human-readable impact description
     */
    protected function crisis_viral_negative($id_resort, $id_player, $player_preferred_lang) {
        $this->lang->load('logs', $player_preferred_lang);

        $this->withdraw_reputation_injuries_DB($id_resort, CRISIS_VIRAL_NEGATIVE_REP_PENALTY);

        $msg = $this->lang->line('logs')['crisis_viral_negative_msg'];

        $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);
        log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['crisis'], 'data' => $msg]);

        return $msg;
    }

    /**
     * get_random_open_lift     Returns a random open lift for the given resort, or NULL if none.
     *
     * @param int $id_resort    Resort ID
     * @return object|null
     */
    protected function get_random_open_lift($id_resort) {
        $query = $this->db
            ->select('id_created_lifts, custom_name')
            ->from('game_created_lifts')
            ->where('id_resort', $id_resort)
            ->where('id_status', '1')
            ->order_by('RAND()')
            ->limit(1)
            ->get();
        return ($query->num_rows() > 0) ? $query->row() : null;
    }

    /**
     * get_random_open_slope    Returns a random open slope for the given resort, or NULL if none.
     *
     * @param int $id_resort    Resort ID
     * @return object|null
     */
    protected function get_random_open_slope($id_resort) {
        $query = $this->db
            ->select('id_created_slopes, custom_name')
            ->from('game_created_slopes')
            ->where('id_resort', $id_resort)
            ->where('id_status', '1')
            ->order_by('RAND()')
            ->limit(1)
            ->get();
        return ($query->num_rows() > 0) ? $query->row() : null;
    }
    
    /**
     * max_revenue_player_building           
     */
    protected function max_revenue_player_building($level, $type){
        $this->db->select('max_income, capacity');
        $this->db->from('game_buildings');
        $this->db->where('game_buildings.level', $level);
        $this->db->where('game_buildings.type', $type);
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * get_info_purchased_equipments           
     */
    protected function get_info_purchased_equipments($current_resort, $equipment_type){
        $this->db->select('game_purchased_equipments_tbl.id_resort, game_equipments.capacity, game_equipments.max_income, game_equipments.daily_cost, game_purchased_equipments_tbl.level, game_purchased_equipments_tbl.type, COUNT(game_purchased_equipments_tbl.level) as count_level');
        $this->db->from('game_equipments');
        $this->db->join('game_purchased_equipments as game_purchased_equipments_tbl', 'game_equipments.type = game_purchased_equipments_tbl.type and game_equipments.level = game_purchased_equipments_tbl.level', 'inner');
        $this->db->where('game_purchased_equipments_tbl.id_resort', $current_resort);
        $this->db->where('game_purchased_equipments_tbl.type', $equipment_type);
        $this->db->where('game_purchased_equipments_tbl.delivered', '1');
        $this->db->group_by('game_purchased_equipments_tbl.level');
        $query = $this->db->get();
        return $query;
    }
    
    
    /**
     * get_info_created_buildings           
     */
    protected function get_info_created_buildings($current_resort, $building_type){
        $this->db->select('game_created_buildings_tbl.id_resort, game_buildings.capacity, game_buildings.max_income, game_buildings.daily_cost, game_created_buildings_tbl.level, game_created_buildings_tbl.type, COUNT(game_created_buildings_tbl.level) as count_level');
        $this->db->from('game_buildings');
        $this->db->join('game_created_buildings as game_created_buildings_tbl', 'game_buildings.type = game_created_buildings_tbl.type and game_buildings.level = game_created_buildings_tbl.level', 'inner');
        $this->db->where('game_created_buildings_tbl.id_resort', $current_resort);
        $this->db->where('game_created_buildings_tbl.type', $building_type);
        $this->db->where('game_created_buildings_tbl.id_status', '1');
        $this->db->group_by('game_created_buildings_tbl.level');
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * add_revenue_DB               Adds any kind of revenue to the players resort
     * 
     * @param type $current_resort    Current resort ID
     * @param type $revenue         Amount in euros to add
     * @return type                 Returns the transaction result
     */
    protected function add_revenue_DB($current_resort, $revenue){
        //echo 'adding '.$revenue.' € to ID '.$current_user.'<br>';
        $updated_rows = 0;
        if ($revenue != 0) { // Only if different from 0, to avoid useless queries
            $this->db->trans_start();
            $this->db->set('cash', 'cash+'.$revenue,FALSE);
            $this->db->where('id_resort' , $current_resort);                              
            $this->db->update('game_resorts');
            $updated_rows = $this->db->affected_rows();
            $this->db->trans_complete();
        }
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    /**
     * take_cost_DB               Takes any kind of cost to the players resort
     * 
     * @param type $current_resort    Current resort ID
     * @param type $cost         Amount in euros to take
     * @return type                 Returns the transaction result
     */
    protected function take_cost_DB($current_resort, $cost){
        //echo 'taking '.$cost.' € to ID '.$current_user.'<br>';
        $cost_db = $cost*-1;
        $updated_rows = 0;
        if ($cost_db != 0) { // Only if different from 0, to avoid useless queries
            $this->db->trans_start();
            $this->db->set('cash', 'cash+'.$cost_db,FALSE);
            $this->db->where('id_resort' , $current_resort);                              
            $this->db->update('game_resorts');
            $updated_rows = $this->db->affected_rows();
            $this->db->trans_complete();
        }
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    /**
     * get_info_staff_DB            Gives the average efficiency, the total count and sums the capacity of the specific staff type given into parameter, for the current player      
     * 
     * @param type $current_resort    Current resort ID
     * @param type $staff_type      Tupe of staff (driver, instructor...)
     * @return type                 Return the query results as "efficiency", "count_staff" and "total_capacity"
     */
    protected function get_info_staff_DB($current_resort, $staff_type){
        if ($staff_type == 'driver' || $staff_type == 'groomer') {
            $this->db->select('AVG(LEAST(game_staff_tbl.efficiency + (hired_staff_tbl.skill_level - 1) * ' . (int)STAFF_SKILL_EFFICIENCY_BONUS . ', 100)) as avg_efficiency, COUNT(*) as count_staff, SUM(equipment_tbl.capacity) as total_capacity');
        }
        else
            $this->db->select('AVG(LEAST(game_staff_tbl.efficiency + (hired_staff_tbl.skill_level - 1) * ' . (int)STAFF_SKILL_EFFICIENCY_BONUS . ', 100)) as avg_efficiency, COUNT(*) as count_staff');
        $this->db->from('game_resorts');
        $this->db->join('game_hired_staff as hired_staff_tbl', 'game_resorts.id_resort = hired_staff_tbl.id_resort', 'inner');
        $this->db->join('game_staff as game_staff_tbl', 'hired_staff_tbl.id_staff = game_staff_tbl.id_staff', 'inner');
        if ($staff_type == 'driver' || $staff_type == 'groomer') {
            $this->db->join('game_purchased_equipments as purchased_equipment_tbl', 'purchased_equipment_tbl.id_purchased_equipments = hired_staff_tbl.id_item_assigned', 'inner');
            $this->db->join('game_equipments as equipment_tbl', 'purchased_equipment_tbl.id_equipment = equipment_tbl.id_equipment', 'inner');
        }
        $this->db->where('hired_staff_tbl.id_resort', $current_resort);
        $this->db->where('game_staff_tbl.position', $staff_type);
        $this->db->where('hired_staff_tbl.id_item_assigned !=', NULL);
        if ($staff_type == 'driver' || $staff_type == 'groomer') {
            $this->db->where('purchased_equipment_tbl.assigned_to_sector !=', NULL);
        }
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * degrade_quality_item                 Gives a penalty of quality for each slope or lift
     * 
     * @param type $quality_loss_per_Day    Penalty to give daily for each slope or lift (global variable
     * @param type $type_item               Type of item (slope, lift...)
     * @return type $updated_slopes         Returns how many (open) slopes/lifts were updated
     */
    protected function degrade_quality_item($quality_loss_per_Day, $type_item) {
        $this->db->trans_start();
        $this->db->set($type_item.'_condition', $type_item.'_condition-'.$quality_loss_per_Day,FALSE);
        $this->db->where($type_item.'_condition >=' , $quality_loss_per_Day);
        $this->db->where('id_status' , '1');                                // Only for open slopes/lifts
        $this->db->update('game_created_'.$type_item.'s');
        $updated_slopes = $this->db->affected_rows();
        $this->db->trans_complete();
        
        $result = $updated_slopes." ".$type_item."s quality was decreased by ".$quality_loss_per_Day." points.\n";

        if ($this->db->trans_status() === FALSE){
            $this->logToFile($this->Log_filename, "WARN", "[item_".$type_item."_".$quality_loss_per_Day."]", "degrade_quality_item", 'Transaction failed');
            return false;
        }
        else {
            $this->logToFile($this->Log_filename, "INFO", "[item_".$type_item."_".$quality_loss_per_Day."]", "degrade_quality_item", $result);
            return true;
        }
    }

    /**
     * apply_night_skiing_quality_loss
     * Applies extra slope degradation for slopes that have night skiing enabled.
     * The degradation amount depends on brightness setting.
     */
    protected function apply_night_skiing_quality_loss() {
        $this->db->trans_start();
        
        // Calculate degradation: base + (brightness - 1) * factor
        // Only for enabled night skiing trails on open slopes
        $sql = "UPDATE game_created_slopes gcs
                JOIN game_night_skiing_trails gnst ON gnst.id_created_slope = gcs.id_created_slopes
                SET gcs.slope_condition = GREATEST(0, gcs.slope_condition - (
                    " . (int)NIGHT_SKIING_QUALITY_LOSS_BASE . " + (gnst.brightness - 1) * " . (float)NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR . "
                ))
                WHERE gnst.night_skiing_enabled = 1 AND gcs.id_status = 1";
        
        $this->db->query($sql);
        $affected = $this->db->affected_rows();
        
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->logToFile($this->Log_filename, "WARN", "[night_skiing_quality]", "apply_night_skiing_quality_loss", "Transaction failed");
        } else {
            $this->logToFile($this->Log_filename, "INFO", "[night_skiing_quality]", "apply_night_skiing_quality_loss", "Degraded quality for $affected night skiing slopes.");
        }
    }

    /**
     * apply_climate_glacier_loss   Applies extra per-resort slope degradation based on climate level.
     *                              Simulates glacier shrinkage reducing the quality of ski terrain.
     *
     * @param object $list_all_opened_resorts   All open resorts
     */
    protected function apply_climate_glacier_loss($list_all_opened_resorts) {
        foreach ($list_all_opened_resorts->result() as $resort_info) {
            $id_resort = $resort_info->id_resort;
            $climate = $this->climate_change_model->get_climate_data_DB($id_resort);
            if ($climate === FALSE || (int)$climate->climate_level <= 0)
                continue;
            $glacier_loss = CLIMATE_GLACIER_LOSS_PER_LEVEL * (int)$climate->climate_level;
            if ($glacier_loss <= 0)
                continue;
            // Apply extra degradation to open slopes of this resort
            $this->db->trans_start();
            $this->db->set('slope_condition', 'slope_condition - ' . (int)$glacier_loss, FALSE);
            $this->db->where('slope_condition >=', $glacier_loss);
            $this->db->where('id_status', '1');
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_created_slopes');
            $this->db->trans_complete();
            if ($this->db->trans_status() !== FALSE)
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$id_resort."]", "apply_climate_glacier_loss", "Glacier loss: extra ".$glacier_loss." points degradation applied to open slopes.\n");
        }
    }
    
    /**
     * maintenance_mode_handling                 
     * 
     */
    protected function maintenance_mode_handling() {
        $select_maintenance_mode = $this->select_maintenance_mode();
        $weightedValues = array( '2'=>20, '8'=>25, '15'=>30, '20'=>15, '30'=>5, '40'=>5);    // Percentage of chance to get a specific price for repair
        $coefficient = getRandomWeightedElement($weightedValues);
        $count = 0;
        foreach ($select_maintenance_mode->result() as $select_maintenance_mode_array) {
            $level = $select_maintenance_mode_array->level;
            $id_group = $select_maintenance_mode_array->id_group;
            $id_resort = $select_maintenance_mode_array->id_resort;
            $lift_condition = $select_maintenance_mode_array->lift_condition;
            $id_created_lifts = $select_maintenance_mode_array->id_created_lifts;
            $generic_item = $this->item_model->get_generic_item_info_for_level($id_group, 'lift', $level);
            $data_generic_item = $generic_item->row();
            $cost_lift = $data_generic_item->base_cost;
            $repair_cost = (int)round($cost_lift / $coefficient * (100-$lift_condition)/100); // e.g. 1000000 / 50 * (100 - 30)/100 = 14 000€
            $set_maintenance_mode = $this->set_maintenance_mode($id_created_lifts, $repair_cost);
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $select_maintenance_mode_array->id_player, 'type' => $this->lang->line('logs')['lift'], 'data' => $select_maintenance_mode_array->custom_name.$this->lang->line('logs')['put_in_maintenance'] ) );   // Add a log row to the game_player_logs table      
            $log_user_action = log_user_action( array('id_player' => $select_maintenance_mode_array->id_player, 'type' => $this->lang->line('logs')['lift'], 'data' => $select_maintenance_mode_array->custom_name.$this->lang->line('logs')['put_in_maintenance'] ) );   // Add a log row to the game_player_logs table      
            // Track lift breakdown for seasonal objective
            $current_season_breakdown = get_current_season($id_resort);
            if ($current_season_breakdown) {
                $this->seasonal_objectives_model->record_lift_breakdown($id_resort, $current_season_breakdown);
            }
            $count ++;
            $result = $count." lifts were placed in maintenance mode due to low condition\n";
            $this->logToFile($this->Log_filename, "INFO", "[ ]", "maintenance_mode_handling", $result);
        }
    }
    
    
    protected function select_maintenance_mode() {  
        $this->db->select('game_created_lifts.id_resort, game_created_lifts.id_created_lifts, game_created_lifts.custom_name, game_created_lifts.repair_cost, game_created_lifts.lift_condition, game_created_lifts.level, game_created_lifts.id_group, game_resorts_tbl.id_player');
        $this->db->from('game_created_lifts');
        $this->db->join('game_resorts as game_resorts_tbl', 'game_resorts_tbl.id_resort = game_created_lifts.id_resort', 'inner');
        $this->db->where('game_created_lifts.lift_condition <=' , '20');
        $this->db->where('game_created_lifts.id_status !=' , '3');
        $this->db->where('game_created_lifts.id_status !=' , '5');
        $require_maintenance = $this->db->get();
        $selected_rows = $require_maintenance->num_rows();
        return $require_maintenance;
    }
    
    protected function set_maintenance_mode($id_created_lifts, $repair_cost) {  
        $this->db->trans_start();
        $this->db->set('id_status', '5');
        $this->db->set('repair_cost', $repair_cost);
        $this->db->where('id_created_lifts', $id_created_lifts);
        $this->db->update('game_created_lifts');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    /**
     * lift_age_seasons     Calculates the age of a lift in game-seasons
     *
     * @param  string|null $install_date   DATE string (Y-m-d) or NULL
     * @return int                         Age in seasons (0 if not yet installed)
     */
    protected function lift_age_seasons($install_date) {
        if (empty($install_date)) {
            return 0;
        }
        $today   = new DateTime($this->todays_date . ' 00:00:00', new DateTimeZone('UTC'));
        $install = new DateTime($install_date . ' 00:00:00', new DateTimeZone('UTC'));
        $days    = max(0, (int) $today->diff($install)->days);
        return (int) ($days / LIFT_SEASON_DAYS);
    }

    /**
     * check_lift_end_of_life   Puts lifts that have reached LIFT_MAX_AGE_SEASONS into forced
     *                          out-of-order status (id_status = 5) so the player is informed
     *                          they need to replace the lift.
     */
    protected function check_lift_end_of_life() {
        $max_days = LIFT_MAX_AGE_SEASONS * LIFT_SEASON_DAYS;
        $cutoff   = (new DateTime($this->todays_date . ' 00:00:00', new DateTimeZone('UTC')))
                        ->sub(new DateInterval('P' . $max_days . 'D'))
                        ->format('Y-m-d');

        $this->db->select('game_created_lifts.id_created_lifts, game_created_lifts.custom_name, game_created_lifts.id_resort, game_created_lifts.level, game_created_lifts.id_group, game_resorts_tbl.id_player');
        $this->db->from('game_created_lifts');
        $this->db->join('game_resorts as game_resorts_tbl', 'game_resorts_tbl.id_resort = game_created_lifts.id_resort', 'inner');
        $this->db->where('game_created_lifts.install_date IS NOT NULL', NULL, FALSE);
        $this->db->where('game_created_lifts.install_date <=', $cutoff);
        $this->db->where('game_created_lifts.id_status !=', '3');
        $this->db->where('game_created_lifts.id_status !=', '5');
        $end_of_life_lifts = $this->db->get();

        foreach ($end_of_life_lifts->result() as $lift) {
            $generic_item = $this->item_model->get_generic_item_info_for_level($lift->id_group, 'lift', $lift->level);
            $data_generic  = $generic_item->row();
            // Replacement cost equals the base cost of the lift (full rebuild)
            $replacement_cost = $data_generic ? $data_generic->base_cost : 0;

            $this->db->trans_start();
            $this->db->set('id_status', '5');
            $this->db->set('repair_cost', $replacement_cost);
            $this->db->where('id_created_lifts', $lift->id_created_lifts);
            $this->db->update('game_created_lifts');
            $this->db->trans_complete();

            $player_preferred_lang = $this->users_model->get_user_preferred_lang($lift->id_player);
            $this->lang->load('logs', $player_preferred_lang);
            $this->lang->load('lift', $player_preferred_lang);
            $msg = $lift->custom_name . $this->lang->line('lift')['end_of_life_notification'];
            $this->logs_model->call_notification_DB(['id_player' => $lift->id_player, 'type' => $this->lang->line('logs')['lift'], 'data' => $msg]);
            log_user_action(['id_player' => $lift->id_player, 'type' => $this->lang->line('logs')['lift'], 'data' => $msg]);
            $this->logToFile($this->Log_filename, "INFO", "[id_created_lifts_".$lift->id_created_lifts."]", "check_lift_end_of_life", "Lift '".$lift->custom_name."' (resort ".$lift->id_resort.") reached end of life and was placed out of order.\n");
        }
    }

    /**
     * improve_quality_item_grooming        Improves the quality of the slope  depending of the type of staff (mechanicGroomer here)
     * 
     * @param type $quality_loss_per_Day    Global variable containing daily quality loss
     * @param type $type_item               Type of item ("slope" only in this case)
     * @return string                       Returns info message with result
     */
    public function improve_quality_item_grooming($quality_loss_per_Day, $type_item) {
$this->logToFile($this->Log_filename, "INFO", "START FUNCTION", "improve_quality_item_grooming", gmdate('Y-m-d H:i:s,u', strtotime('now'))."\n");
        $info_message = '';
        $max_out_at_100 = '';
        // For each sector
        for ($sector_id=0;$sector_id<=ACTIVE_SECTORS;$sector_id++){
            // Gets all the open items of the sector PER PLAYER
            $all_opened_items_from_sector = $this->list_all_opened_items_from_sector($sector_id, $type_item);
            // Loop to get action items requirements
            foreach ($all_opened_items_from_sector->result() as $row_all_opened_items_from_sector){   // For each sector
            // Init variables
            $bonus_quality = 0;
            $action_required = 0;
            $action_capacity = 0;
               $id_resort = $row_all_opened_items_from_sector->id_resort;
               $action_required = $row_all_opened_items_from_sector->COUNT;      // Number of slopes / required items
                // Gets action items with assigned staff for this sector and player
                $get_assigned_items_sector = $this->get_assigned_items_sector($sector_id, $id_resort); 
                // Counts items with assigned staff for this sector and player
                $count_assigned_items_sector = $get_assigned_items_sector->num_rows();  
                // Only if there are any action items assigned to a sector
                if($count_assigned_items_sector) {
                    // Updates the item action capacity and put staff efficiency in array (for each staff/equipment)
                    $efficiency_staff = [];
                    $intensity_multipliers = [];
                    $has_precision_spec = false;
                    $has_tech_spec = false;
                    foreach ($get_assigned_items_sector->result() as $row_assigned_items_sector){ 
                        $action_capacity = $action_capacity + $row_assigned_items_sector->level;      // The  action capacity depending of each item level
                        $eff = isset($row_assigned_items_sector->effective_efficiency) ? $row_assigned_items_sector->effective_efficiency : $row_assigned_items_sector->efficiency;
                        $efficiency_staff[] = $eff;                   // Store effective efficiency of each staff in a array
                        // Track specializations
                        if (isset($row_assigned_items_sector->specialization)) {
                            if ($row_assigned_items_sector->specialization === 'precision') $has_precision_spec = true;
                            if ($row_assigned_items_sector->specialization === 'tech')      $has_tech_spec = true;
                        }
                        // Collect grooming intensity multiplier for this groomer
                        $gi = $row_assigned_items_sector->grooming_intensity ?? 'standard';
                        if ($gi === 'light') {
                            $intensity_multipliers[] = GROOMER_INTENSITY_LIGHT;
                        } else if ($gi === 'intensive') {
                            $intensity_multipliers[] = GROOMER_INTENSITY_INTENSIVE;
                        } else {
                            $intensity_multipliers[] = GROOMER_INTENSITY_STANDARD;
                        }
                        $this->logToFile($this->Log_filename, "INFO", "[efficiency_staff_idresort_".$id_resort."]", "efficiency_staff", "row:".$eff." level: ".$row_assigned_items_sector->level." intensity: ".$gi." (total: ".$action_capacity." in sector ".$sector_id.")\n");
                    }

      //  echo 'id_resort efficiency:'.$id_resort;
       // var_dump($efficiency_staff);
                    // calculate average efficiency of all staff for this sector/player
                    $average_efficiency = $this->average($efficiency_staff); 
                    // calculate average intensity multiplier across all groomers in this sector
                    $avg_intensity_multiplier = !empty($intensity_multipliers)
                        ? array_sum($intensity_multipliers) / count($intensity_multipliers)
                        : GROOMER_INTENSITY_STANDARD;
       // echo 'average_efficiency:'.$average_efficiency;                    

                    // How many extra action items the player has for this sector
                    $diff_action = $this->diff_grooming($action_capacity, $action_required);
                    // Example: Bonus = 5 * 0.75 * (4-3) = 5 * 0.75 * 1 = 3.75
                    // Example2 : Bonus = 5 * 0.60 * (4-2) = 5 * 0.60 * 2 = 6
                    // Example3 : Bonus = 5 * 0.90 * (2-2) = 5 * 0.90 * 1 = 4.5
                    // Example2 : Bonus = 5 * 0.80 * (3-5) = 5 * 0.80 * 0 = 0
                    // Intensity multiplier scales the bonus (light=0.75x, standard=1.0x, intensive=1.5x)
                    $bonus_quality = round(($quality_loss_per_Day +3) * $average_efficiency/100 * $diff_action * $avg_intensity_multiplier);
                    // Apply staff morale factor: low morale reduces repair effectiveness; strikes stop it entirely
                    $morale_factor = $this->get_resort_avg_morale_factor($id_resort);
                    $bonus_quality = round($bonus_quality * $morale_factor);
                    // precision spec: +30% quality repair; tech spec: +20% quality repair (stacked if both present)
                    if ($has_precision_spec) $bonus_quality = round($bonus_quality * 1.30);
                    if ($has_tech_spec)      $bonus_quality = round($bonus_quality * 1.20);
                    // We update each slope with new quality bonus
                    $get_player_created_slopes = $this->get_player_created_slopes($sector_id, $id_resort);
                    foreach ($get_player_created_slopes->result() as $row_player_created_slopes){   // For each sector
                       if ($bonus_quality != 0) {
                        $add_quality_bonus_slope = $this->add_quality_bonus_item($sector_id, $bonus_quality, $row_player_created_slopes->id_created_slopes, $type_item);
                        $this->logToFile($this->Log_filename, "INFO", "[id_created_slopes_".$row_player_created_slopes->id_created_slopes."]", "add_quality_bonus_item", $add_quality_bonus_slope." (details: quality_loss_per_Day= ".$quality_loss_per_Day." average_efficiency= ".$average_efficiency." diff_action= ".$diff_action." action_capacity= ".$action_capacity." action_required= ".$action_required." avg_intensity= ".$avg_intensity_multiplier.")\n");
                       }
                    }
                }
                else {
                    $info_message = "There is no relevant groomer (assigned)\n";
                    $this->logToFile($this->Log_filename, "INFO", "[quality_loss_per_Day_".$quality_loss_per_Day."_type_item_".$type_item."]", "improve_quality_item_grooming", $info_message);
                }
            }
        } 
        
        $max_out_at_100 = $this->max_out_at_100('slope');
        $info_message_max = $max_out_at_100." ".$type_item." conditions were maxed out at 100\n"; // Displays the results
        $this->logToFile($this->Log_filename, "INFO", "[type_item_".$type_item."]", "max_out_at_100", $info_message_max);
        
$this->logToFile($this->Log_filename, "INFO", "STOP FUNCTION", "improve_quality_item_grooming", gmdate('Y-m-d H:i:s,u', strtotime('now'))."\n");
    }
    
    /**
     * improve_quality_item_lift            Improves the quality of the lift  depending of the type of staff (mechanic here)  
     * 
     * @param type $staff_type              Staff type (mechanic here)
     * @param type $quality_loss_per_Day    Global variable containing daily quality loss
     * @param type $type_item               Type of item ("lift" only in this case)
     * @return string                       Returns info message with result
     */
    public function improve_quality_item_lift($staff_type, $quality_loss_per_Day, $type_item) {
        $info_message = '';
        $max_out_at_100 = '';
        // Gets all the opened slopes with staff assigned
        $all_opened_lifts_with_staff = $this->list_all_opened_items_game_with_staff_assigned($staff_type, 'lift');
        foreach ($all_opened_lifts_with_staff->result() as $row_all_opened_lifts_with_staff){   // For each lift
            $efficiency_staff = isset($row_all_opened_lifts_with_staff->effective_efficiency) ? $row_all_opened_lifts_with_staff->effective_efficiency : $row_all_opened_lifts_with_staff->efficiency;
            $spec = isset($row_all_opened_lifts_with_staff->specialization) ? $row_all_opened_lifts_with_staff->specialization : null;
            $bonus_quality = round(($quality_loss_per_Day + 3) * $efficiency_staff/100);
            // Apply staff morale factor: low morale reduces repair effectiveness; strikes stop it entirely
            $morale_factor = $this->get_resort_avg_morale_factor($row_all_opened_lifts_with_staff->id_resort);
            $bonus_quality = round($bonus_quality * $morale_factor);
            // precision spec: +30% quality repair; tech spec: +20% quality repair
            if ($spec === 'precision') $bonus_quality = round($bonus_quality * 1.30);
            if ($spec === 'tech')      $bonus_quality = round($bonus_quality * 1.20);
            // We update each lift with new quality bonus
                $add_quality_bonus_lift = $this->add_quality_bonus_item('', $bonus_quality, $row_all_opened_lifts_with_staff->id_created_lifts, $type_item);
                $this->logToFile($this->Log_filename, "INFO", "[id_created_lifts".$row_all_opened_lifts_with_staff->id_created_lifts."]", "add_quality_bonus_item", $add_quality_bonus_lift." (quality_loss_per_Day= ".$quality_loss_per_Day.", efficiency_staff= ".$efficiency_staff."%, bonus_quality= ".$bonus_quality.", spec= ".($spec ?? 'none').")\n");
                $info_message_max = $max_out_at_100." ".$type_item." conditions were maxed out at 100\n"; // Displays the results
                $this->logToFile($this->Log_filename, "INFO", "[type_item_".$type_item."]", "max_out_at_100", $info_message_max);
        }
    }
    
    /**
     * withdraw_reputation_injuries             Withdraws reputation based on injuries for current day
     * 
     * @param type $list_all_opened_resorts     List of all opened resorts
     * @return string                           Returns result's info message
     */
    public function withdraw_reputation_injuries($list_all_opened_resorts) {
        //$info_message = '';
        // For each open resort
        foreach ($list_all_opened_resorts->result() as $row_list_all_opened_resorts){
            $injuries_d0 = $this->get_stats_current_day($row_list_all_opened_resorts->id_resort, 'injuries');
            // Calculates reputation to withdraw
            $reputation_to_withdraw = round($injuries_d0 * 0.2);       // can be adjusted
            // Halve the injury reputation loss for easy mode players
            $currentUserID = $row_list_all_opened_resorts->id_player;
            if ($this->users_model->get_difficulty_mode($currentUserID) == 1) {
                $reputation_to_withdraw = (int)ceil($reputation_to_withdraw / 2);
            }
            $withdraw_reputation_injuries = $this->withdraw_reputation_injuries_DB($row_list_all_opened_resorts->id_resort, $reputation_to_withdraw);
            $info_message = "resort ". $row_list_all_opened_resorts->id_resort." lost ".$reputation_to_withdraw." reputation due to ".$injuries_d0." injuries today\n"; // Displays the results
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$row_list_all_opened_resorts->id_resort."]", "withdraw_reputation_injuries", $info_message);
            $player_preferred_lang = $row_list_all_opened_resorts->preferred_lang;
            $currentUserID = $row_list_all_opened_resorts->id_player;
            $this->lang->load('logs',$player_preferred_lang);
            if ($injuries_d0 > 0) {
                $test_notif_injuries = $this->lang->line('logs')['your_resort_lost'].$reputation_to_withdraw.$this->lang->line('logs')['reputation_points'].$this->lang->line('logs')['due_to'].$injuries_d0.$this->lang->line('logs')['injuries_today'];
            }
            else {
                $test_notif_injuries = $this->lang->line('logs')['no_lost_injuries'];
            }
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['injuries'], 'data' => $test_notif_injuries) );   // Add a log row to the game_player_logs table  
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['injuries'], 'data' => $test_notif_injuries) );   // Add a log row to the game_player_logs table  
            
        }
        //return $info_message;
    }
    
    /**
     * get_stats_current_day     Get the daily injuries for the resort
     * 
     * @param type $id_resort       ID Resort
     * @return type                 Returns an integer, indicating the number of injuries today
     */
    protected function get_stats_current_day($id_resort, $type, $date = null){
        if ($date == null)
            $date = $this->todays_date;
        $this->db->select($type);
        $this->db->from('game_resort_'.$type);
        $this->db->where('id_resort', $id_resort);
        $this->db->where('date', $date);
        $get_stats_today = $this->db->get();
        if ($get_stats_today->num_rows() > '0') {   // If the entry for today exists (it should)
            $result = $get_stats_today->row();
            $stats_today = $result->$type;
        }
        else // in case it doesn't return anything
            $stats_today = '0';
        return $stats_today;
    }
    
        /**
     * withdraw_reputation_injuries_DB          Database query updating the reputation based on injuries number
     * 
     * @param type $id_resort                   ID resort
     * @param type $reputation_to_withdraw      Amount of reputation to withdraw
     * @return type                             Should return 1
     */
    protected function withdraw_reputation_injuries_DB($id_resort, $reputation_to_withdraw) {
        $updated_rows = 0;
        if ($reputation_to_withdraw != 0) {
            $this->db->trans_start();
            $this->db->set('reputation', 'reputation-'.$reputation_to_withdraw, FALSE);
            $this->db->where('id_resort' , $id_resort);  
            $this->db->update('game_resorts');
            $updated_rows = $this->db->affected_rows();
            $this->db->trans_complete();
        }
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    

    /**
     * get_number_open_black_diamond_slopes   Counts open Black Diamond slopes for a resort
     *
     * @param  int  $id_resort
     * @return int
     */
    protected function get_number_open_black_diamond_slopes($id_resort) {
        return $this->db
            ->from('game_created_slopes')
            ->where('id_resort', $id_resort)
            ->where('id_status', '1')
            ->where('id_difficulty', BLACK_DIAMOND_DIFFICULTY_ID)
            ->count_all_results();
    }

    /**
     * award_reputation_black_diamond         Awards nightly reputation for open Black Diamond slopes
     *
     * @param  object  $list_all_opened_resorts   Query result of all opened resorts
     */
    public function award_reputation_black_diamond($list_all_opened_resorts) {
        foreach ($list_all_opened_resorts->result() as $row) {
            $num_bd = $this->get_number_open_black_diamond_slopes($row->id_resort);
            if ($num_bd === 0) {
                continue;
            }
            $reputation_bonus = (int) ($num_bd * BLACK_DIAMOND_REPUTATION_PER_SLOPE);
            if ($reputation_bonus <= 0) {
                continue;
            }
            $this->db->trans_start();
            $this->db->set('reputation', 'reputation+' . $reputation_bonus, FALSE);
            $this->db->where('id_resort', $row->id_resort);
            $this->db->update('game_resorts');
            $this->db->trans_complete();

            $currentUserID = $this->users_model->get_user_id_from_resortID($row->id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('reporting', $player_preferred_lang);
            $reporting_data = sprintf($this->lang->line('reporting')['black_diamond_rep_bonus'], $reputation_bonus, $num_bd);
            $this->add_reporting_data_db($row->id_resort, 'slope', $reporting_data);
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$row->id_resort}]", "award_reputation_black_diamond", "resort {$row->id_resort} earned {$reputation_bonus} reputation from {$num_bd} Black Diamond slope(s)\n");
        }
    }

   
    /**
     * create_stat_new_day    Add new entry in the DB for the new coming day
     * 
     * @param type $list_all_resorts     All resorts of the games
     * @param type $type                        Returns the result of the database query           
     */
    protected function create_stat_new_day($list_all_resorts, $type) {
        $result = true;
        foreach ($list_all_resorts->result() as $list_all_resorts_Array2){
 
            $id_resort = $list_all_resorts_Array2->id_resort;
            $create_stat_new_day_DB = $this->create_stat_new_day_DB($id_resort, $type);
            if (!$create_stat_new_day_DB) {
                $result = false;
            }
        }
        return $result;
    }   
    
    /**
     * create_new_season    Creates a new season if there are at least 135days of history
     * 
     * @param type $list_all_resorts     All resorts of the games
     * @param type $type                        Returns the result of the database query           
     */
    protected function create_new_season($list_all_resorts) {
        // Ensure users_model is available for get_day_of_season.
        $this->load->model('users_model');

        foreach ($list_all_resorts->result() as $list_all_resorts_Array){
            $new_season_message = '';
            $id_resort = $list_all_resorts_Array->id_resort;
            // Use the model directly to get the raw integer day value.
            // The get_day_of_season() helper converts values ≥ 136 to the
            // string '?' for display purposes, which breaks the numeric
            // comparison below and prevents the season from ever rolling over.
            $current_day = $this->users_model->get_day_of_season($id_resort);

            // Climate change: shorten season threshold based on climate_level
            $climate = $this->climate_change_model->get_climate_data_DB($id_resort);
            if ($climate === FALSE) {
                $this->climate_change_model->init_climate_DB($id_resort);
                $climate = $this->climate_change_model->get_climate_data_DB($id_resort);
            }
            $climate_level         = (int)($climate ? $climate->climate_level : 0);
            $season_length_penalty = CLIMATE_SEASON_PENALTY_PER_LEVEL * $climate_level;
            $season_end_threshold  = 136 - $season_length_penalty;

            if ($current_day >= $season_end_threshold){
                $current_season = get_current_season($id_resort);
                $new_season = $current_season + 1;
                $start_date = $this->todays_datetime;
                $data = array ('id_resort' => $id_resort, 'season' => $new_season, 'start_date' => $start_date );
                $this->resort_model->create_history_stats('season', $data);         // create a row for the new season
                $this->resort_model->reset_snow_level($id_resort);         // resets the snow level for the new season
                $new_season_message .= "Resort ID ".$id_resort." has started season ".$new_season." on ".$start_date."\n";
                
                $player_preferred_lang = $list_all_resorts_Array->preferred_lang;
                $currentUserID = $list_all_resorts_Array->id_player;
                $season_bonus = SEASON_BONUS;
                $this->lang->load('logs',$player_preferred_lang);
                $this->lang->load('climate_change',$player_preferred_lang);
                
                // Give 2M for new season bonus
               
                $this->resort_model->give_season_bonus_db($id_resort, $season_bonus);         // Gives a season bonus to start the new season
                $new_season_message .= "Resort ID ".$id_resort." has received a season bonus of ".$season_bonus." € \n";
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $this->lang->line('logs')['received_bonus1'].$season_bonus.$this->lang->line('logs')['received_bonus2']) );   // Add a log row to the game_player_logs table                
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $this->lang->line('logs')['has_received_bonus'].$season_bonus.$this->lang->line('logs')['received_bonus2']) );   // Add a log row to the game_player_logs table                
                
                $level = 'INFO';
                
                // Evaluate seasonal objectives for the ending season and credit rewards
                $season_start_date = get_current_season_start_date($id_resort);
                $obj_rewards = $this->seasonal_objectives_model->evaluate_and_award($id_resort, $currentUserID, $current_season, $season_start_date);
                if (!empty($obj_rewards['completed_names'])) {
                    $reward_msg = $this->lang->line('logs')['seasonal_objectives_reward']
                        . $obj_rewards['total_prestige'] . $this->lang->line('logs')['seasonal_objectives_prestige']
                        . number_format($obj_rewards['total_cash'], 0, ',', ' ') . $this->lang->line('logs')['seasonal_objectives_cash']
                        . $obj_rewards['total_genepis'] . $this->lang->line('logs')['seasonal_objectives_genepis'];
                    $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $reward_msg) );
                    log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $reward_msg) );
                    $new_season_message .= "Resort ID ".$id_resort." earned seasonal objective rewards: prestige=".$obj_rewards['total_prestige']." cash=".$obj_rewards['total_cash']." genepis=".$obj_rewards['total_genepis']."\n";
                }
                // Initialize objective progress records for the new season
                $this->seasonal_objectives_model->init_season_objectives($id_resort, $new_season);
                
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['login_season'].$new_season.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table                
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['login_season'].$new_season.$this->lang->line('logs')['has_started']) );   // Add a log row to the game_player_logs table                
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['snow_reset']) );   // Add a log row to the game_player_logs table                
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['snow_reset']) );   // Add a log row to the game_player_logs table                

                // Climate change: increment level each season starting from CLIMATE_FIRST_SEASON
                if ($new_season >= CLIMATE_FIRST_SEASON) {
                    $this->climate_change_model->increment_climate_level_DB($id_resort);
                    $new_climate = $this->climate_change_model->get_climate_data_DB($id_resort);
                    $new_level   = $new_climate ? (int)$new_climate->climate_level : $climate_level + 1;
                    $new_season_message .= "Resort ID ".$id_resort." climate level incremented to ".$new_level."\n";
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['climate'], 'data' => $this->lang->line('logs')['climate_level_increased'].' '.$new_level.'. '.$this->lang->line('logs')['climate_effects_active']) );
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['climate'], 'data' => $this->lang->line('logs')['climate_level_increased'].' '.$new_level.'. '.$this->lang->line('logs')['climate_effects_active']) );
                    if ($season_length_penalty > 0) {
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['climate'], 'data' => $this->lang->line('climate_change')['season_shorter']) );
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['climate'], 'data' => $this->lang->line('climate_change')['season_shorter']) );
                    } // end if season_length_penalty
                } // end if CLIMATE_FIRST_SEASON

                // Legacy System: calculate historical rating when resort reaches LEGACY_SEASON_THRESHOLD seasons
                if ($new_season == LEGACY_SEASON_THRESHOLD) {
                    $resort_info_row = $this->resort_model->display_resort_info_DB($id_resort)->row();
                    if ($resort_info_row) {
                        $reputation = (int)$resort_info_row->reputation;
                        $prestige   = (int)$resort_info_row->prestige;
                        $legacy_rating = min(100, (int)(($reputation / 8) + ($prestige / 20)));
                        $legendary_status = ($legacy_rating >= LEGACY_LEGENDARY_MIN_RATING) ? 1 : 0;
                        $this->resort_model->set_legacy_rating_DB($id_resort, $legacy_rating, $legendary_status);
                        $new_season_message .= "Resort ID ".$id_resort." earned legacy rating ".$legacy_rating."/100".($legendary_status ? " - LEGENDARY MOUNTAIN UNLOCKED" : "")."\n";

                        $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['legacy_rating_earned'].$legacy_rating.'/100') );
                        log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['legacy_rating_earned'].$legacy_rating.'/100') );

                        if ($legendary_status) {
                            $this->load->model('users_model');
                            $this->users_model->set_legacy_bonus_DB($currentUserID, LEGACY_BONUS_CASH);
                            $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['legendary_mountain_unlocked']) );
                            log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['progress'], 'data' => $this->lang->line('logs')['legendary_mountain_unlocked']) );
                        }
                    }
                }
            }
            else if ($current_day < $season_end_threshold){
                // do nothing
                $new_season_message .= "Resort ID ".$id_resort." is playing day ".$current_day." (no action)\n";
                $level = 'INFO';
            }
            else {
                $new_season_message .= "Could not determine if new season needs to be created for resort ID ".$id_resort."\n";
                $level = 'WARN';
            }
        
        $this->logToFile($this->Log_filename, $level, '[ ]', 'create_new_season', $new_season_message); 
        }
        return $new_season_message;
    }    
    
    /**
     * create_stat_new_day_DB     Runs the query to add new stats entries for the new day
     * 
     * @param type $id_resort                       ID of the resort
     * @param type $type                            Type of item to handle (injuries, affluence...)
     * @return string                               Returns the result (info message)
     */
    protected function create_stat_new_day_DB($id_resort, $type){
        $result = '';
        $record_exists = $this->resort_model->check_if_record_exists($id_resort, $type, $this->todays_date);
        if ($record_exists) { 
            // Record already exists for today, we do nothing to avoid resetting stats if the cron runs multiple times
             $result .= "Table ".$type." ALREADY updated for resort ID ".$id_resort." with new row for ".$this->todays_date." (Skipping)\n";
        }
        else { // INSERT: There is no entry matching the ID resort and the date
            $this->db->trans_start();
            $data = array ('id_resort' => $id_resort, 'date' => $this->todays_date, $type => '0');
            $query = $this->db->insert('game_resort_'.$type, $data);
            $this->db->trans_complete();
            
            $result .= "Table ".$type." updated for resort ID ".$id_resort." with new row for ".$this->todays_date."\n";
        }
        
        $this->logToFile($this->Log_filename, 'INFO', '[id_resort_'.$id_resort.']', 'create_stat_new_day_DB', $result);
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $result;
        }
    }
    
    
    /**
     * count_injuries_slope                 Count how many injuries each open slope is generating
     * 
     * @param type $array_daily_visitors    Contains all the resorts/players and their daily visitors number
     */
    protected function count_injuries_slope($array_daily_visitors) {
        foreach ($array_daily_visitors as $row_daily_visitors){      // For each returned slope
            // Lists all opened slopes that have a skipatrol staff assigned
            $list_all_opened_slopes = $this->list_all_opened_slopes($row_daily_visitors['id_resort']);
            
            $loop_count = 0;
            foreach ($list_all_opened_slopes->result() as $row_all_opened_slopes){      // For each returned slope
                
                $player_resort = $this->get_player_resort($row_all_opened_slopes->id_resort);
                $currentUserID = $this->users_model->get_user_id_from_resortID($player_resort);
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $this->lang->load('reporting',$player_preferred_lang);

                $info_patrol = $this->get_slope_info_with_staff_assigned('skipatrol', 'slope', $row_all_opened_slopes->id_created_slopes);
                // echo $this->db->last_query();
                if ($info_patrol->num_rows() > 0) {
                    $total_efficiency = 0;
                    $patrol_count = 0;
                    foreach($info_patrol->result() as $info_patrol_array) {
                        $eff = isset($info_patrol_array->effective_efficiency) ? $info_patrol_array->effective_efficiency : $info_patrol_array->efficiency;
                        $total_efficiency += $eff;
                        $patrol_count++;
                    }
                    if ($patrol_count > 0) {
                        // Average efficiency across all assigned ski patrols
                        $efficiency = round($total_efficiency / $patrol_count);
                        // Multi-patrol bonus: each additional patrol beyond the first adds 5% to effective efficiency (capped at 100)
                        if ($patrol_count > 1) {
                            $efficiency = min(100, $efficiency + (($patrol_count - 1) * 5));
                        }
                    } else {
                        $efficiency = 0;
                    }
                }
                else {
                    $efficiency = 0;
                }

                // The risk of injury depends of the efficiency of the ski patrol assigned to the slope and the condition of the slope.
                // The risk of injury for the current slope is 100-percentage_of_efficiency + 100-slope_condition.
                // REMOVED THIS ON 18/08: We add +3 malus in case the employee is 100% efficient, or condition is 100% we still want a probability of injuries.
                // e.g: "risk = 100 - 85 + 3 + 100 - 90 + 3 = 31%" of chance for this slope
                //$risk_injury_this_slope = (100 - $efficiency + 3) + (100 - $row_all_opened_slopes->slope_condition + 3);
                // Low staff morale reduces ski patrol effectiveness, increasing injury risk
                $morale_factor = $this->get_resort_avg_morale_factor($row_daily_visitors['id_resort']);
                $effective_efficiency = round($efficiency * $morale_factor);
                $risk_injury_this_slope = min((100 - $effective_efficiency + 1 ) + (100 - $row_all_opened_slopes->slope_condition + 1), 100);

                // Specialization bonus: safety reduces injury risk, speed also helps
                if ($info_patrol->num_rows() > 0) {
                    $has_safety_spec = false;
                    $has_speed_spec  = false;
                    foreach ($info_patrol->result() as $patrol_row) {
                        if (isset($patrol_row->specialization)) {
                            if ($patrol_row->specialization === 'safety') $has_safety_spec = true;
                            if ($patrol_row->specialization === 'speed')  $has_speed_spec  = true;
                        }
                    }
                    $spec_risk_reduction = 0;
                    if ($has_safety_spec) $spec_risk_reduction += 25;  // safety: -25% injury risk
                    if ($has_speed_spec)  $spec_risk_reduction += 15;  // speed: -15% injury risk
                    if ($spec_risk_reduction > 0) {
                        $risk_injury_this_slope = max(1, $risk_injury_this_slope - $spec_risk_reduction);
                        $this->logToFile($this->Log_filename, "DEBUG", "[id_created_slopes_".$row_all_opened_slopes->id_created_slopes."]", "count_injuries_slope",
                            "Patrol spec reduction: -".$spec_risk_reduction."% risk (safety: ".($has_safety_spec?'yes':'no').", speed: ".($has_speed_spec?'yes':'no').")\n");
                    }
                }

                // Black Diamond / Extreme Zone: higher injury risk
                if ((int) $row_all_opened_slopes->id_difficulty === BLACK_DIAMOND_DIFFICULTY_ID) {
                    $risk_injury_this_slope = min(round($risk_injury_this_slope * BLACK_DIAMOND_INJURY_MULTIPLIER), 100);
                    // START Adds reporting data to DB
                    $reporting_data = sprintf($this->lang->line('reporting')['black_diamond_injury_warning'], $row_all_opened_slopes->custom_name);
                    $add_reporting_data_db = $this->add_reporting_data_db($player_resort, 'injuries', $reporting_data);
                    // END Adds reporting data to DB
                }

                if ($risk_injury_this_slope >= 15) {
                    // START Adds reporting data to DB
                    $reporting_data = $this->lang->line('reporting')['risk_injury_slope'].' '.$risk_injury_this_slope.'% '.$this->lang->line('reporting')['on_slope'].' "'.$row_all_opened_slopes->custom_name.'". '.$this->lang->line('reporting')['reduce_injuries_tip'];
                    $add_reporting_data_db = $this->add_reporting_data_db($player_resort, 'injuries', $reporting_data);
                    // END Adds reporting data to DB
                }
                // Search the array key for the current player
                // $array_daily_visitors contains all the resorts/players and their daily visitors number
                $player_key = array_search($player_resort, array_column($array_daily_visitors, 'id_resort'));
                
                $daily_visitors_resort = $array_daily_visitors[$player_key]['daily_visitors'];          // Number of visitors for the resort
                // $max_injured_today_slope = The max number of injuries
                // We actually never reach this value, it's only a percentage based on number of visitors and risk of injury
                $max_injured_today_slope = $daily_visitors_resort / 100 * $risk_injury_this_slope;
                // Real risk of injuries. From 0.5% of the max number, to 5%. Not number of visitors!
                $injured_percent_05_max = round($max_injured_today_slope * 0.005);
                $injured_percent_1_max = round($max_injured_today_slope * 0.01);
                $injured_percent_2_max = round($max_injured_today_slope * 0.015);
                $injured_percent_3_max = round($max_injured_today_slope * 0.02);
                $injured_percent_4_max = round($max_injured_today_slope * 0.025);
                $injured_percent_5_max = round($max_injured_today_slope * 0.03);
                // We add some weight on each value to influence the probability.
                // The sum of all weights should be 100.
                // Put highest weigths first to influence speed
                // http://stackoverflow.com/questions/445235/generating-random-results-by-weight-in-php
                $weightedValues = array( $injured_percent_1_max=>30, $injured_percent_2_max=>25, $injured_percent_05_max=>15, $injured_percent_3_max=>15, $injured_percent_4_max=>10, $injured_percent_5_max=>5);
                $slope_total_injuries = getRandomWeightedElement($weightedValues);
                // If bad weather (danger), multiply injuries by 3.
            
                $yesterday = strtotime('-1 day', $this->todays_time);
                $yesterday_GMT = gmdate('Y-m-d', $yesterday);
                $todays_weather = $this->weather_model->select_weather_forecast($yesterday_GMT);    // Get id condition for yesterday, because script runs after midnight
                if ($todays_weather) {
                $todays_weather_data = $todays_weather->row(); 
                $today_id_condition = $todays_weather_data->id_condition;
                $array_weather = $this->weather_model->select_weather_conditions($today_id_condition); // Get details for todays condition (snow level, name...)
                $result = $array_weather->row();
                $danger = $result->danger;
                if ($danger == 1) {
                    $slope_total_injuries = $slope_total_injuries * 3; // If bad weather (danger), multiply injuries by 3.
                    // START Adds reporting data to DB
                    if ($loop_count == 0) {
                        $reporting_data = $this->lang->line('reporting')['danger_today'];
                        $add_reporting_data_db = $this->add_reporting_data_db($player_resort, 'injuries', $reporting_data);
                        $loop_count ++;
                    }
                    // END Adds reporting data to DB
                }
                }
           
                $add_daily_stats_to_DB = $this->add_daily_stats_to_DB($player_resort, $slope_total_injuries, 'injuries'); 
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$player_resort."]", "before", microtime(true)."\n");
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$player_resort."]", "count_injuries_slope", $add_daily_stats_to_DB);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$player_resort."]", "after", microtime(true)."\n");
                //echo $add_daily_stats_to_DB;
            }
        }
    }
        
    /**
     * add_daily_stats_to_DB                Add the daily cash, affluence or injuries to the DB
     * This function must run after the daily calculations for affluence and injuries.
     * 
     * @param type $id_resort               ID of the slope's resort
     * @param type $total_number            Amount of injuries/visitors
     * @param type $type                    Type of table to update (injuries, affluence...)
     * @return string                       Return the result info message
     */
    protected function add_daily_stats_to_DB($id_resort, $total_number, $type){
        $result = '';
        
        $record_exists = $this->resort_model->check_if_record_exists($id_resort, $type, $this->yesterdays_date);
            
        if ($record_exists) { // UPDATE: There is an entry matching the ID resort and the date
            if ($total_number != 0) { // Only if different from 0, to avoid useless queries
                $this->db->trans_start();
                $this->db->set($type, $type .'+'. $total_number, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->where('date' , $this->yesterdays_date);
                //$this->db->where('date' , $this->todays_date);
                $this->db->update('game_resort_'.$type); 
                $this->db->limit('1');
                $this->db->trans_complete();
            }
        }
        else { // INSERT: There is no entry matching the ID resort and the date
            $this->db->trans_start();
            $data = array ('id_resort' => $id_resort, 'date' => $this->yesterdays_date, $type => $total_number);
            $query = $this->db->insert('game_resort_'.$type, $data);
            $this->db->limit('1');
            $this->db->trans_complete();
        }
      
        $result .= "Daily ".$type." for resort ID ".$id_resort." updated: (".$total_number.") for date ".$this->yesterdays_date."\n";
        
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $result;
        }
    }

    /**
     * list_all_opened_items_game_with_staff_assigned   Lists all opened slopes/lifts which have an assigned staff
     * 
     * @param type $staff_type                          "mechanic" here
     * @param type $type_item                           "lift" here
     * @return type                                     Returns query's result
     */
    protected function list_all_opened_items_game_with_staff_assigned($staff_type, $type_item){
        $this->db->select('created_'.$type_item.'s_tbl.id_created_'.$type_item.'s, created_'.$type_item.'s_tbl.'.$type_item.'_condition, staff_tbl.efficiency, LEAST(staff_tbl.efficiency + (game_hired_staff.skill_level - 1) * ' . (int)STAFF_SKILL_EFFICIENCY_BONUS . ', 100) as effective_efficiency, game_hired_staff.specialization, created_'.$type_item.'s_tbl.id_resort');
        $this->db->distinct();
        $this->db->from('game_hired_staff');
        $this->db->join('game_created_'.$type_item.'s as created_'.$type_item.'s_tbl', 'created_'.$type_item.'s_tbl.id_created_'.$type_item.'s = game_hired_staff.id_item_assigned', 'inner');
        $this->db->join('game_staff as staff_tbl', 'staff_tbl.id_staff = game_hired_staff.id_staff', 'inner');
        $this->db->where('staff_tbl.position', $staff_type);
        $this->db->where('created_'.$type_item.'s_tbl.id_status', '1');
        $result = $this->db->get();
        return $result;
    }
    
    /**
     * get_slope_info_with_staff_assigned       Returns slope info for slopes that have a staff (ski patrol) assigned 
     *   
     * @param type $staff_type                  Staff type (ski patrol here)
     * @param type $type_item                   Item type (slope here)
     * @param type $id_created_slopes           Specific slope ID
     * @return type                             Returns query's result
     */
    protected function get_slope_info_with_staff_assigned($staff_type, $type_item, $id_created_slopes){
        $this->db->select('game_hired_staff.id_hired_staff, staff_tbl.efficiency, LEAST(staff_tbl.efficiency + (game_hired_staff.skill_level - 1) * ' . (int)STAFF_SKILL_EFFICIENCY_BONUS . ', 100) as effective_efficiency, game_hired_staff.specialization');
        //$this->db->distinct('created_'.$type_item.'s_tbl.id_created_'.$type_item.'s, created_'.$type_item.'s_tbl.'.$type_item.'_condition, hired_staff_tbl.efficiency, created_'.$type_item.'s_tbl.id_resort');
        $this->db->from('game_hired_staff');
        $this->db->join('game_created_'.$type_item.'s as created_'.$type_item.'s_tbl', 'created_'.$type_item.'s_tbl.id_created_'.$type_item.'s = game_hired_staff.id_item_assigned', 'inner');
        $this->db->join('game_staff as staff_tbl', 'staff_tbl.id_staff = game_hired_staff.id_staff', 'inner');
        $this->db->where('staff_tbl.position', $staff_type);
        $this->db->where('created_'.$type_item.'s_tbl.id_created_slopes', $id_created_slopes);
        $this->db->where('created_'.$type_item.'s_tbl.id_status', '1');
        $result = $this->db->get();
        return $result;
    }
     
    /**
     * get_player_resort            Get the ID resort for the specific player
     * 
     * @param type $id_resort       ID resort
     * @return type                 Returns player's ID resort (integer)
     */
    protected function get_player_resort($id_resort){
        $this->db->select('id_resort');
        $this->db->from('game_resorts');
        $this->db->where('id_resort', $id_resort);
        $get_player_resort = $this->db->get();
        $result = $get_player_resort->row();
        $id_resort = $result->id_resort;
        return $id_resort;
    }
    
    /**
     * diff_grooming            Calculates how many extra grooming capacity the player has.
     * If the grooming capacity equals the required grooming nneds, we set to 1 instead of 0 to make a normal coefficient.
     * 
     * @param type $capacity    Grooming capacity (different from number of grooming due to different levels)
     * @param type $required    Grooming required (number of slopes for the sector)
     * @return string           Returns the result (integer)
     */
    protected function diff_grooming($capacity, $required) {
        $result = $capacity - $required;
        if ($result == 0){
            $result = '1';
        }
        else if ($result < 0){
            $result = '0';
        }
        return $result;
    }
    
    /**
     * average              Calculates the average value of an integers array
     * 
     * @param type $array   Array containin integers
     * @return type         Average value
     */
    protected function average($array) {
        if (empty($array)) return 0;
        return array_sum($array) / count($array);
    }
    
    /**
     * list_all_opened_items_from_sector    Lists all opened items in a specific sector (slopes in this case)
     * 
     * @param type $sector_id               Sector ID
     * @param type $type_item               Type of item (slope in this case)
     * @return type                         Returns query's result
     */
    protected function list_all_opened_items_from_sector($sector_id, $type_item){
        $this->db->select('created_'.$type_item.'s_tbl.id_created_'.$type_item.'s, created_'.$type_item.'s_tbl.custom_name, game_'.$type_item.'s_tbl.id_sector, created_'.$type_item.'s_tbl.id_resort, COUNT(created_'.$type_item.'s_tbl.id_resort) AS COUNT');
        $this->db->from('game_resorts');
        $this->db->join('game_created_'.$type_item.'s as created_'.$type_item.'s_tbl', 'game_resorts.id_resort = created_'.$type_item.'s_tbl.id_resort', 'inner');
        $this->db->join('game_'.$type_item.'s as game_'.$type_item.'s_tbl', 'created_'.$type_item.'s_tbl.id_'.$type_item.' = game_'.$type_item.'s_tbl.id_'.$type_item, 'inner');
        $this->db->where('game_'.$type_item.'s_tbl.id_sector', $sector_id);
        $this->db->where('created_'.$type_item.'s_tbl.id_status', '1');
        $this->db->order_by('created_'.$type_item.'s_tbl.id_resort');
        $this->db->group_by('created_'.$type_item.'s_tbl.id_resort');
        $query = $this->db->get();
        return $query;
    }
    
    /**
     * add_quality_bonus_item           Database query adding the quality bonus to a specific item (slope/lift)
     * 
     * @param type $sector_id           Sector ID
     * @param type $bonus_quality       Value of the bonus to be added
     * @param type $id_created_item     Specific ID item
     * @param type $type_item           Type of item (used for table name) - slope/lift
     * @return string                   Returns info message result
     */
    protected function add_quality_bonus_item($sector_id, $bonus_quality, $id_created_item, $type_item){
        if ($bonus_quality != 0) { // Only if different from 0, to avoid useless queries
            $this->db->trans_start();
            $this->db->set($type_item.'_condition', $type_item.'_condition + ' . (int) $bonus_quality, FALSE);
            $this->db->where('id_created_'.$type_item.'s', $id_created_item);
            $this->db->update('game_created_'.$type_item.'s'); 
            $this->db->trans_complete();
        }
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            $result = $bonus_quality." quality points were added to ".$type_item." ID ".$id_created_item."\n";
            return $result;
        }  
    }
    
    /**
     * max_out_at_100               Evens out item condition if value is above 100
     * 
     * @param type $type_item       Type item (slope / lift)
     * @return type                 Returns the number of updated rows
     */
    protected function max_out_at_100($type_item){
        $this->db->trans_start();
        $this->db->set($type_item.'_condition', '100');
        $this->db->where($type_item.'_condition >', '100');
        $this->db->update('game_created_'.$type_item.'s'); 
        $updated_slopes = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_slopes;
        }  
    }
    
    /**
     * get_assigned_items_sector        Gets action items (groomers...) with assigned staff for this sector and player
     * 
     * @param type $sector_id           Sector ID
     * @param type $id_resort           ID resort
     * @return type                     Returns query's result
     */
    protected function get_assigned_items_sector($sector_id, $id_resort){    
        $this->db->select('game_purchased_equipments_tbl.id_purchased_equipments, game_purchased_equipments_tbl.level, game_purchased_equipments_tbl.grooming_intensity, staff_tbl.efficiency, LEAST(staff_tbl.efficiency + (game_hired_staff.skill_level - 1) * ' . (int)STAFF_SKILL_EFFICIENCY_BONUS . ', 100) as effective_efficiency, game_hired_staff.specialization');
        $this->db->distinct();
        $this->db->from('game_hired_staff');
        $this->db->join('game_purchased_equipments as game_purchased_equipments_tbl', 'game_purchased_equipments_tbl.id_purchased_equipments = game_hired_staff.id_item_assigned', 'inner');
        $this->db->join('game_staff as staff_tbl', 'staff_tbl.id_staff = game_hired_staff.id_staff', 'inner');
        $this->db->where('game_hired_staff.type_item_assigned', 'groomer');
        $this->db->where('game_hired_staff.id_resort', $id_resort);
        $this->db->where('game_purchased_equipments_tbl.assigned_to_sector', $sector_id);
        // Only include groomers that are set to active (grooming_active = 1 or column absent = legacy)
        $this->db->where('(game_purchased_equipments_tbl.grooming_active IS NULL OR game_purchased_equipments_tbl.grooming_active = 1)', NULL, FALSE);
        return $this->db->get();  
    }
    
    /**
     * get_created_lifts_and_generic       Gets the generic info (throughput) of the created lifts of the player
     * 
     * @param type $id_resort               ID resort
     * @return type                         Returns query's result
     */
    protected function get_created_lifts_and_generic($id_resort){    
        $this->db->select('game_lifts_tbl.throughput, game_created_lifts.id_created_lifts, game_lifts_tbl.id_lift, game_created_lifts.lift_condition, game_created_lifts.custom_name, game_locations.length, game_created_lifts.install_date');
        $this->db->from('game_created_lifts, game_locations');
        $this->db->join('game_lifts as game_lifts_tbl', 'game_lifts_tbl.id_group = game_created_lifts.id_group AND game_created_lifts.id_group_location = game_locations.id_group', 'inner');
        $this->db->where('game_created_lifts.id_resort', $id_resort);
        $this->db->where('game_created_lifts.id_status', '1');
        $this->db->group_by('game_created_lifts.id_created_lifts', 'asc');
        
        return $this->db->get();  
    }
    
    /**
     * get_player_created_slopes        Gets player's created slopes for the current ID sector
     * 
     * @param type $sector_id           ID sector
     * @param type $id_resort           ID resort
     * @return type                     Returns query's result
     */
    protected function get_player_created_slopes($sector_id ,$id_resort){    
        $this->db->distinct('game_created_slopes.id_created_slopes');
        $this->db->from('game_created_slopes');
        $this->db->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner');
        $this->db->where('game_created_slopes.id_resort', $id_resort);
        $this->db->where('game_created_slopes.id_status', '1');
        $this->db->where('game_slopes_tbl.id_sector', $sector_id);
        return $this->db->get();  
    }
    
    /**
     * list_all_opened_slopes   Lists all opened slopes
     * @return type             Returns query's result
     */
    protected function list_all_opened_slopes($id_resort){    
        $this->db->select('id_created_slopes, id_resort, slope_condition, custom_name, id_difficulty');
        $this->db->from('game_created_slopes');
        $this->db->where('id_status', '1');
        $this->db->where('id_resort', $id_resort);
        return $this->db->get();  
        
    }
    
    /**
     * list_all_opened_lifts_and_daily_cost    Get daily_cost of all opened lifts for all players 
     * @return type             Returns query's result
     */
    protected function list_all_opened_lifts_and_daily_cost(){    
        $this->db->select('game_created_lifts.id_created_lifts, game_lifts_tbl.daily_cost, game_created_lifts.id_resort, game_created_lifts.install_date');
        $this->db->from('game_created_lifts');
        $this->db->join('game_lifts as game_lifts_tbl', 'game_lifts_tbl.id_group = game_created_lifts.id_group AND game_lifts_tbl.level = game_created_lifts.level', 'inner');
        $this->db->where('game_created_lifts.id_status', '1');
        return $this->db->get();  
    }
    
    /**
     * list_all_resorts      List all resorts of the game
     * 
     * @return type                 Returns the query's results
     */
    protected function list_all_resorts(){
        $this->db->select('game_resorts.id_player, game_resorts.id_resort, game_resorts.altitude, game_resorts.aspect, players_tbl.preferred_lang, players_tbl.vacation_mode, players_tbl.last_connection, players_tbl.username, players_tbl.email');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->from('game_resorts');
        return $this->db->get();
    }
        
    /**
     * add_todays_stat_to_history             Adds the cash, snow level possessed today into the history.
     * This function should run after all other cash update functions
     * 
     * @param type $data_type     Type of data to handle (cash, snow_level...)
     * @return string                           Returns result's info message
     */
    protected function add_todays_stat_to_history($data_type) {
        // Listing all resorts
        $list_all_resorts = $this->list_all_resorts();
        $add_daily_value_to_history_DB = false;
        // For each resort
        foreach ($list_all_resorts->result() as $row_list_all_resorts){
            $current_value = $this->get_data_resort($row_list_all_resorts->id_resort, $data_type);
            // Do not add negative values
            $current_value = max($current_value,0); // returns 0 if value is negative
            // Adds the value to the history table
            $add_daily_value_to_history_DB = $this->add_daily_stats_to_DB($row_list_all_resorts->id_resort, $current_value, $data_type);
            $this->logToFile($this->Log_filename, "DEBUG", "[id_resort:".$row_list_all_resorts->id_resort."]", "add_todays_stat_to_history", "Data_type: ".$data_type.". Value from query: ".$this->get_data_resort($row_list_all_resorts->id_resort, $data_type).". After maxing: ".$current_value."\n");
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$row_list_all_resorts->id_resort."_data_type_".$data_type."]", "add_daily_stats_to_DB", $add_daily_value_to_history_DB);
            
        }
        
        return $add_daily_value_to_history_DB;
    }
    
    /**
     * get_data_resort          Get current resort cash or snow level (data)
     * 
     * @param type $id_resort   ID resort
     * @return type             Return data value
     */
    protected function get_data_resort($id_resort, $type_data){  
        $query = $this->db
            ->select($type_data)
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            //->where('date', $this->todays_date)
            ->get();
        return $query->row($type_data);         // We return the stat value, e.g. 125000 or 40
    }
    
    protected function change_snow_level_player($list_all_resorts) {
        $todays_weather = $this->weather_model->select_weather_forecast($this->todays_date);    // Get id condition for today
        if (!$todays_weather) {
            $this->logToFile($this->Log_filename, "WARNING", "change_snow_level_player", "select_weather_forecast", "No weather forecast found for ".$this->todays_date.". Skipping snow level update.\n");
            return;
        }
        $todays_weather_data = $todays_weather->row();
        $today_id_condition = $todays_weather_data->id_condition;
        $array_weather = $this->weather_model->select_weather_conditions($today_id_condition); // Get details for todays condition (snow level, name...)
        $result = $array_weather->row();
        $snow_difference = $result->snow_level;
        $name_english = $result->name_english;
        $name_french = $result->name_french;
        
        $todays_weather_array['snow_level'] = $snow_difference;
        $todays_weather_array['name_english'] = $name_english;
        $todays_weather_array['name_french'] = $name_french;
        foreach ($list_all_resorts->result() as $list_all_resorts_Array){
            $player_preferred_lang = $list_all_resorts_Array->preferred_lang;
            if ($player_preferred_lang == '') {
                $player_preferred_lang = 'english';
            }
            $currentUserID = $list_all_resorts_Array->id_player;
            $this->lang->load('logs',$player_preferred_lang);
            $column_lang = 'name_'.$player_preferred_lang;
            $todays_weather_translated = $result->$column_lang;
            
            if ($snow_difference > 0)
                $diff_text = $this->lang->line('logs')['increased'].$snow_difference.'cm.';
            else if ($snow_difference < 0)
                $diff_text = $this->lang->line('logs')['decreased'].$snow_difference.'cm.';
            else
                $diff_text = $this->lang->line('logs')['no_change'];
            if ($list_all_resorts_Array->vacation_mode == 0) {
                $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['todays_weather'].$todays_weather_translated.'. '.$this->lang->line('logs')['snow_level'].$diff_text) );   // Add a log row to the game_player_logs table
                $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['todays_weather'].$todays_weather_translated.'. '.$this->lang->line('logs')['snow_level'].$diff_text) );   // Add a log row to the game_player_logs table
            }
        } 
        return $todays_weather_array;
       
    }
    protected function calculate_weather() {
        
        $info_message = '';
        for ($i=0 ; $i<30 ; $i ++) {        // For each next 30 days
            // Array containing the ID to be selected in the first part and then the percentage to get it (i.e 3% in most of the cases)
            $weightedValues = array( 1=>3, 2=>3, 3=>4, 4=>4, 5=>3, 6=>3, 7=>3, 8=>3, 9=>2, 10=>2, 11=>2, 12=>1, 13=>1, 14=>1, 15=>3, 16=>5, 17=>3, 18=>3, 19=>3, 20=>3, 21=>3, 22=>3, 23=>3, 24=>3, 25=>1, 26=>1, 27=>3, 28=>3, 29=>3, 30=>3, 31=>3, 32=>3, 33=>5, 34=>4, 35=>4 );
            $IDtoBeSelected = getRandomWeightedElement($weightedValues);    // id condition
            
            $inXdays = strtotime('+'.$i.' days', $this->todays_time);
            $inXdays_GMT = gmdate('Y-m-d', $inXdays);
            
            $array_weather = $this->weather_model->select_weather_forecast($inXdays_GMT);  // Check if there is an entry for current date in loop
            if ($array_weather === false) {      // Only if the entry doesn't exist in the DB; we want to add it
                $data_forecast = array(
                    'id_condition' => $IDtoBeSelected,
                    'date' => $inXdays_GMT
                );
                $this->db->trans_start();
                $query = $this->db->insert('game_weather_forecast', $data_forecast);
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE){   // Error
                    $this->logToFile($this->Log_filename, "WARN", "[ ]", "calculate_weather", 'Transaction failed');
                }
                else {      // Added to DB successfully
                    $info_message .= "ID weather condition ".$IDtoBeSelected." added to database for date ".$inXdays_GMT."\n";
                }
                $this->logToFile($this->Log_filename, "INFO", "[ ]", "calculate_weather", $info_message);
            }   // End of "not in DB, need to add entry"
        }       // end of 30 days loop
        //return $info_message;
    }
    
    
    
    
    /*
    protected function check_Xly_income($list_all_resorts, $period, $table){   
        
        foreach ($list_all_resorts->result() as $list_all_resorts_Array){
            $currentUserID = $list_all_resorts_Array->id_player;
            $currentResortID = $list_all_resorts_Array->id_resort;
            $revenue_value_resort = $this->check_Xly_income_DB($currentResortID, $period, $table);
            $total_since_date = $revenue_value_resort->row('total');
            echo ' resort ID: '.$currentResortID;
            echo ' total: '.$total_since_date.'<br>';
        }        
    }
    
    protected function check_Xly_income_DB($currentResortID, $period, $table){                
        $query = $this->db
            ->select('SUM('.$table.') as total')                    // something like "revenue"
            ->from('game_resort_'.$table)   
            ->where('id_resort', $currentResortID)
            ->where('date>=', $period)
            ->get();
        return $query;         // We return the selected array
    }
    */
     protected function insert_admin_daily_statistics(){  
        $activated_accounts = $this->Admin_stats_model->activated_accounts();
        $number_resorts = $this->Admin_stats_model->number_resorts();
        $number_open_resorts = $this->Admin_stats_model->number_open_resorts();
        $total_accounts = $this->users_model->get_registered_players();
        $total_accounts_non_vacation = $this->Admin_stats_model->get_non_vacation_account();
        $nb_slopes = count_nb_slopes();
        $nb_open_slopes = count_nb_open_slopes();
        $nb_lifts = count_nb_lifts();
        $nb_open_lifts = count_nb_open_lifts();
        $nb_visitors = count_total_accumulated_visitors();
        $completed_achievements = count_completed_achievements();
        $claimed_achievements = count_claimed_achievements();
        $daily_visitors = count_daily_visitors($this->yesterdays_date);
        if ($number_open_resorts > 0)
            $daily_visitor_per_open_resort = $daily_visitors / $number_open_resorts;
        else
            $daily_visitor_per_open_resort = 0;
        //$this->db->trans_start();
        $data = array ('date' => $this->todays_date, 'activated_accounts' => $activated_accounts, 'non_vacation' => $total_accounts_non_vacation, 'number_resorts' => $number_resorts, 'open_resorts' => $number_open_resorts, 'total_accounts' => $total_accounts, 'slopes' => $nb_slopes, 'lifts' => $nb_lifts, 'open_slopes' => $nb_open_slopes, 'open_lifts' => $nb_open_lifts, 'total_visitors' => $nb_visitors, 'daily_visitors' => $daily_visitors, 'daily_visitors_per_open_resort' => $daily_visitor_per_open_resort, 'completed_achievements' => $completed_achievements, 'claimed_achievements' => $claimed_achievements);
        $query = $this->db->insert('game_admin_stats', $data);
        //$this->db->trans_complete();
        return $query;         // We return the selected array
    }
        
    public function show_snow_level($id_resort){  
        $query = $this->db
            ->select('snow_level')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get();
        return $query->row('snow_level');         // We return the stat value, e.g. 125000 or 40
    }
    
    /**
     * complete_lift_tech_research  Marks any in_progress lift tech research rows
     *                              as completed once their finish_at has passed.
     *                              Called once per nightly run.
     */
    protected function complete_lift_tech_research() {
        $updated = $this->lift_tech_model->complete_overdue_research_DB();
        $this->logToFile($this->Log_filename, "INFO", "[ ]", "complete_lift_tech_research", "Completed {$updated} lift tech research rows.\n");
    }

    // -------------------------------------------------------------------------
    // Experimental R&D
    // -------------------------------------------------------------------------

    /**
     * process_research_upgrades
     *
     * Applies nightly bonuses for every *completed* research-tree upgrade
     * across all 6 trees:
     *   Slope · Terrain Engineering · Snowmaking · Marketing · Staff · Lift Tech
     *
     * Each completed upgrade grants a flat nightly bonus (rep, revenue, and/or snow).
     * Bonuses are cumulative — owning the full tree stacks all rewards.
     *
     * rep   → added directly to game_resorts.reputation
     * cash  → added as revenue (add_revenue_DB + rev_other stat)
     * snow  → added via add_remove_snow_db (cm, capped at resort max)
     */
    protected function process_research_upgrades($list_all_resorts) {

        // Nightly bonuses per upgrade key: [rep, cash (€), snow (cm)]
        $effects = [
            // ── Slope Upgrade Tree ────────────────────────────────────────────
            'enhanced_grooming'       => ['rep' => 1, 'cash' => 0,    'snow' => 0],
            'safety_netting'          => ['rep' => 1, 'cash' => 100,  'snow' => 0],
            'terrain_improvement'     => ['rep' => 2, 'cash' => 0,    'snow' => 0],
            'advanced_piste_marking'  => ['rep' => 1, 'cash' => 150,  'snow' => 0],
            'premium_slope_surface'   => ['rep' => 2, 'cash' => 200,  'snow' => 0],
            // ── Terrain Engineering Tree ──────────────────────────────────────
            'terrain_park_features'   => ['rep' => 1, 'cash' => 250,  'snow' => 0],
            'moguls_section'          => ['rep' => 1, 'cash' => 200,  'snow' => 0],
            'tree_runs'               => ['rep' => 2, 'cash' => 300,  'snow' => 0],
            'backcountry_access'      => ['rep' => 2, 'cash' => 400,  'snow' => 0],
            'advanced_slope_program'  => ['rep' => 3, 'cash' => 500,  'snow' => 0],
            // ── Snowmaking Upgrade Tree ───────────────────────────────────────
            'water_efficiency'        => ['rep' => 0, 'cash' => 200,  'snow' => 0],
            'energy_recovery'         => ['rep' => 0, 'cash' => 150,  'snow' => 0],
            'automated_scheduling'    => ['rep' => 0, 'cash' => 200,  'snow' => 2],
            'high_altitude_guns'      => ['rep' => 0, 'cash' => 100,  'snow' => 3],
            'underground_pipeline'    => ['rep' => 0, 'cash' => 300,  'snow' => 2],
            // ── Marketing Upgrade Tree ────────────────────────────────────────
            'social_media_strategy'   => ['rep' => 0, 'cash' => 300,  'snow' => 0],
            'influencer_program'      => ['rep' => 1, 'cash' => 500,  'snow' => 0],
            'loyalty_program_upgrade' => ['rep' => 1, 'cash' => 700,  'snow' => 0],
            'international_advertising'=> ['rep'=> 1, 'cash' => 1000, 'snow' => 0],
            'brand_ambassador'        => ['rep' => 3, 'cash' => 1500, 'snow' => 0],
            // ── Staff Upgrade Tree ────────────────────────────────────────────
            'staff_training_center'   => ['rep' => 1, 'cash' => 0,    'snow' => 0],
            'performance_bonus_scheme'=> ['rep' => 1, 'cash' => 200,  'snow' => 0],
            'advanced_instructor_cert'=> ['rep' => 1, 'cash' => 400,  'snow' => 0],
            'staff_accommodation'     => ['rep' => 1, 'cash' => 300,  'snow' => 0],
            'expert_patrol_team'      => ['rep' => 2, 'cash' => 200,  'snow' => 0],
            // ── Lift Tech Tree ────────────────────────────────────────────────
            'loading_carpet'          => ['rep' => 0, 'cash' => 100,  'snow' => 0],
            'faster_loading'          => ['rep' => 1, 'cash' => 150,  'snow' => 0],
            'heated_seats'            => ['rep' => 1, 'cash' => 200,  'snow' => 0],
            'bubble_covers'           => ['rep' => 1, 'cash' => 300,  'snow' => 0],
            'ai_maintenance'          => ['rep' => 0, 'cash' => 400,  'snow' => 0],
            'smart_snowmaking'        => ['rep' => 0, 'cash' => 200,  'snow' => 3],
        ];

        // All 6 research tables
        $tables = [
            'game_slope_upgrade_research',
            'game_terrain_engineering_research',
            'game_snowmaking_upgrade_research',
            'game_marketing_upgrade_research',
            'game_staff_upgrade_research',
            'game_lift_tech_research',
        ];

        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort  = (int) $resort_row->id_resort;
            $total_rep  = 0;
            $total_cash = 0;
            $total_snow = 0;

            foreach ($tables as $table) {
                $rows = $this->db
                    ->where('id_resort', $id_resort)
                    ->where('status', 'completed')
                    ->get($table);

                if (!$rows) continue;

                foreach ($rows->result() as $row) {
                    $key = $row->upgrade_key;
                    if (!isset($effects[$key])) continue;
                    $total_rep  += $effects[$key]['rep'];
                    $total_cash += $effects[$key]['cash'];
                    $total_snow += $effects[$key]['snow'];
                }
            }

            // Apply reputation
            if ($total_rep > 0) {
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation + ' . $total_rep, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();
            }

            // Apply cash bonus (tracked as revenue)
            if ($total_cash > 0) {
                $this->add_revenue_DB($id_resort, $total_cash);
                add_revenue_stat_table($id_resort, $total_cash, 'rev_other', $this->yesterdays_date);
            }

            // Apply snow bonus
            if ($total_snow > 0) {
                $this->add_remove_snow_db($total_snow, $id_resort);
            }

            if ($total_rep > 0 || $total_cash > 0 || $total_snow > 0) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_research_upgrades",
                    "Applied nightly research bonuses: +{$total_rep} rep, +{$total_cash} € revenue, +{$total_snow} cm snow.\n");
            }
        }
    }

    /**
     * process_rd_projects  Resolves completed R&D experiments (success or failure)
     *                      and applies nightly bonuses for all completed projects.
     *
     * Called once per nightly run for all resorts.
     *
     * @param object $list_all_resorts  CI query result of all resort rows
     */
    protected function process_rd_projects($list_all_resorts) {
        // 1. Resolve in_progress projects whose finish_at has passed
        $due_rows = $this->rd_model->get_in_progress_due_DB();
        foreach ($due_rows->result() as $due) {
            $id_resort   = (int)$due->id_resort;
            $project_key = $due->project_key;
            $rushed      = (int)$due->rushed;
            $id_rd       = (int)$due->id_rd;

            if (!array_key_exists($project_key, RD_PROJECTS)) {
                continue;
            }
            $info            = RD_PROJECTS[$project_key];
            $failure_chance  = $rushed ? $info['failure_chance_rush'] : $info['failure_chance_normal'];
            $roll            = mt_rand(1, 100);

            if ($roll <= $failure_chance) {
                // --- FAILURE ---
                $this->rd_model->fail_project_DB($id_rd);

                // Accident cost
                $this->take_cost_DB($id_resort, RD_ACCIDENT_COST);
                add_cost_stat_table($id_resort, RD_ACCIDENT_COST, 'cost_upkeep',  $this->yesterdays_date);
                add_cost_stat_table($id_resort, RD_ACCIDENT_COST, 'expenses',     $this->yesterdays_date);

                // Reputation penalty
                $this->db->trans_start();
                $this->db->set('reputation', 'GREATEST(reputation - ' . (int)RD_FAILURE_REP_PENALTY . ', 0)', FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();

                // Log
                $site_lang = $this->_get_resort_lang($id_resort);
                $this->lang->load('logs', $site_lang);
                $tech_name = ($site_lang === 'french') ? $info['name_french'] : $info['name_english'];
                $msg = $this->lang->line('logs')['rd_failed'] . ': ' . $tech_name
                     . ' (−' . RD_FAILURE_REP_PENALTY . ' rep, −' . number_format(RD_ACCIDENT_COST, 0, '.', ' ') . ' €)';
                $id_player = $this->_get_player_for_resort($id_resort);
                if ($id_player) {
                    $this->logs_model->call_notification_DB([
                        'id_player' => $id_player,
                        'type'      => $this->lang->line('logs')['rd_log_type'],
                        'data'      => $msg,
                    ]);
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_rd_projects",
                    "Project {$project_key} FAILED (roll {$roll} <= {$failure_chance}%). Rep -" . RD_FAILURE_REP_PENALTY . ", cost -" . RD_ACCIDENT_COST . " €\n");
            } else {
                // --- SUCCESS ---
                $this->rd_model->complete_project_DB($id_rd);

                // Log
                $site_lang = $this->_get_resort_lang($id_resort);
                $this->lang->load('logs', $site_lang);
                $tech_name = ($site_lang === 'french') ? $info['name_french'] : $info['name_english'];
                $msg = $this->lang->line('logs')['rd_completed'] . ': ' . $tech_name;
                $id_player = $this->_get_player_for_resort($id_resort);
                if ($id_player) {
                    $this->logs_model->call_notification_DB([
                        'id_player' => $id_player,
                        'type'      => $this->lang->line('logs')['rd_log_type'],
                        'data'      => $msg,
                    ]);
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_rd_projects",
                    "Project {$project_key} COMPLETED (roll {$roll} > {$failure_chance}%).\n");
            }
        }

        // 2. Apply nightly bonuses for all completed projects, per resort
        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort      = (int)$resort_row->id_resort;
            $completed_rows = $this->rd_model->get_completed_by_resort_DB($id_resort);

            foreach ($completed_rows->result() as $comp) {
                $project_key = $comp->project_key;
                if (!array_key_exists($project_key, RD_PROJECTS)) {
                    continue;
                }
                $info = RD_PROJECTS[$project_key];

                if ($info['bonus_type'] === 'reputation') {
                    $this->db->trans_start();
                    $this->db->set('reputation', 'reputation + ' . (int)$info['bonus_value'], FALSE);
                    $this->db->where('id_resort', $id_resort);
                    $this->db->update('game_resorts');
                    $this->db->trans_complete();
                } elseif ($info['bonus_type'] === 'cost_saving') {
                    $this->add_revenue_DB($id_resort, (int)$info['bonus_value']);
                    add_revenue_stat_table($id_resort, (int)$info['bonus_value'], 'rev_other', $this->yesterdays_date);
                }
            }
        }
    }

    /**
     * _get_resort_lang     Returns the preferred language for a resort's player.
     *
     * @param  int    $id_resort
     * @return string  'english' | 'french'
     */
    private function _get_resort_lang($id_resort) {
        $row = $this->db
            ->select('players_tbl.preferred_lang')
            ->from('game_resorts')
            ->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'left')
            ->where('game_resorts.id_resort', (int)$id_resort)
            ->get()
            ->row();
        return ($row && $row->preferred_lang === 'french') ? 'french' : 'english';
    }

    /**
     * _get_player_for_resort   Returns the id_player for a given resort.
     *
     * @param  int  $id_resort
     * @return int|null
     */
    private function _get_player_for_resort($id_resort) {
        $row = $this->db
            ->select('id_player')
            ->from('game_resorts')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();
        return $row ? (int)$row->id_player : null;
    }

    public function logToFile($log_filename, $level, $thread, $function, $data){ 
        
        $microtime = microtime(true);
        $now = DateTime::createFromFormat('U.u', $microtime);
        
        // https://stackoverflow.com/a/35435632/893204
        //In the specific moments where microtime returns a float with only zeros as decimals, this error appeared.
        if (is_bool($now))   
            $microtime += 0.001;          
        $now = DateTime::createFromFormat('U.u', $microtime );
        
         $timestamp = $now->format("Y-m-d H:i:s,u");
         
         $timestamp = substr($timestamp, 0, -3);
        // $timestamp = gmdate('Y-m-d H:i:s,u', strtotime('now'))." ";
        $data_formatted = $timestamp."  ".$level." ".$thread." ".$function." - ".$data;
        if ( ! write_file(FCPATH . '/application/controllers/logs/NightlyMainJobs_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file NightlyMainJobs_".$log_filename.".log :<br>".$data_formatted;
        }
        else{
            // echo "Logged ".$function."<br>";
        }
    }
    
    public function email_vacation_mode($username, $email = '', $siteLang) {

      $this->lang->load('email',$siteLang);

      // Build styled HTML email body (no CTA button – informational only)
      $message = build_html_email(
        $username,
        $this->lang->line('email')['vacation_mode_heading'],
        $this->lang->line('email')['vacation_mode_body']
      );

      // Send via Brevo REST API so the email appears in Brevo's transactional logs
      $sent = send_brevo_email($email, CONST_ADMIN_EMAIL, 'Ski-Manager', $this->lang->line('email')['vacation_mode_subject'], $message);
      if ( ! $sent) {
        log_message('error', 'Vacation mode email could not be sent to: ' . $email);
      }
      return $sent;
    }
    
    
    /**
     * progress_guest_skills    At the end of a season (day 136+), advances guest skill
     *                          levels for every resort:
     *                            - GUEST_SKILL_BEGINNER_TO_INTERMEDIATE_RATE of beginners -> intermediate
     *                            - GUEST_SKILL_INTERMEDIATE_TO_ADVANCED_RATE of intermediate -> advanced
     *
     * @param CI_DB_result $list_all_resorts     All resorts
     */
    protected function progress_guest_skills($list_all_resorts) {
        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort   = (int)$resort_row->id_resort;
            $current_day = get_day_of_season($id_resort);

            // Only progress at the season boundary (day = 136, i.e. new season just started)
            if ((int)$current_day !== 1) {
                continue;
            }

            $skill = $this->guest_skill_model->get_or_init_DB($id_resort);

            $beg   = (int)$skill->beginner_pct;
            $inter = (int)$skill->intermediate_pct;
            $adv   = (int)$skill->advanced_pct;
            $seasons = (int)$skill->seasons_played + 1;

            // Calculate how many percentage points graduate each level
            $beg_grad   = (int)round($beg   * GUEST_SKILL_BEGINNER_TO_INTERMEDIATE_RATE);
            $inter_grad = (int)round($inter  * GUEST_SKILL_INTERMEDIATE_TO_ADVANCED_RATE);

            $new_beg   = $beg   - $beg_grad;
            $new_inter = $inter + $beg_grad - $inter_grad;
            $new_adv   = $adv   + $inter_grad;

            // Clamp to [0, 100] and normalise so they sum to 100
            $new_beg   = max(0, $new_beg);
            $new_inter = max(0, $new_inter);
            $new_adv   = max(0, $new_adv);
            $total = $new_beg + $new_inter + $new_adv;
            if ($total > 0 && $total !== 100) {
                // Rounding may cause off-by-one; adjust beginner_pct to compensate
                $new_beg += (100 - $total);
                $new_beg = max(0, $new_beg);
            }

            $this->guest_skill_model->update_skills_DB($id_resort, $new_beg, $new_inter, $new_adv, $seasons);
            $this->logToFile($this->Log_filename, 'INFO', '[id_resort_' . $id_resort . ']', 'progress_guest_skills',
                "Season $seasons – beg:{$new_beg}% inter:{$new_inter}% adv:{$new_adv}%\n");
        }
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

    /**
     * apply_competitor_pressure    Nightly AI evolution for all competitor resorts.
     *
     * For each row in game_player_competitors:
     *   - 25 % chance: marketing_level += 1 (max 10)
     *   - 15 % chance: ticket_discount  += 5 (max 50)
     *   - 10 % chance: lift_investment  += 1 (max 5)
     *   - natural decay: marketing_level -= 1, ticket_discount -= 2  (min 0, applied after)
     *
     * The net effect is that without counter-action from the player the competitor
     * gradually builds up pressure, but the decay prevents it from reaching the
     * ceiling without continued investment.
     */
    protected function apply_competitor_pressure() {
        $rows = $this->competitors_model->get_all_player_competitors_for_nightly();
        if ($rows->num_rows() === 0) {
            $this->logToFile($this->Log_filename, "INFO", "[ ]", "apply_competitor_pressure", "No competitor rows found.\n");
            return;
        }

        foreach ($rows->result() as $row) {
            $delta_marketing = 0;
            $delta_discount  = 0;
            $delta_lift      = 0;

            // Random AI actions
            $rand = mt_rand(1, 100);
            if ($rand <= 25) {
                $delta_marketing += 1;  // competitor runs a marketing campaign
            }
            $rand2 = mt_rand(1, 100);
            if ($rand2 <= 15) {
                $delta_discount += 5;   // competitor offers cheaper tickets
            }
            $rand3 = mt_rand(1, 100);
            if ($rand3 <= 10) {
                $delta_lift += 1;       // competitor invests in mega lifts
            }

            // Natural decay – keeps pressure manageable over time
            $delta_marketing -= 1;
            $delta_discount  -= 2;

            // Only write if there is something to change
            if ($delta_marketing !== 0 || $delta_discount !== 0 || $delta_lift !== 0) {
                $this->competitors_model->nightly_evolve_competitor(
                    $row->id_player_competitor,
                    $delta_marketing,
                    $delta_discount,
                    $delta_lift
                );
                $this->logToFile(
                    $this->Log_filename, "INFO",
                    "[id_resort_".$row->id_resort."_competitor_".$row->id_competitor."]",
                    "apply_competitor_pressure",
                    "delta_mkt=".$delta_marketing.", delta_disc=".$delta_discount.", delta_lift=".$delta_lift."\n"
                );
            }
        }
    }

    /**
     * process_lift_line_management     Evaluates lift queue times for every open resort and applies:
     *                                  - Reputation penalty when queue time exceeds the resort's tolerance
     *                                  - VIP fast-pass revenue when the VIP lane is enabled
     *                                  - Random lift breakdown when a lift is overloaded
     *
     * @param array $visitor_calculations   Array produced by visitor_calculations()
     */
    protected function process_lift_line_management($visitor_calculations) {
        foreach ($visitor_calculations as $resort_data) {
            $id_resort      = $resort_data['id_resort'];
            $daily_visitors = $resort_data['daily_visitors'];

            $currentUserID        = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);

            // Fetch per-resort lift line settings (returns defaults when no row exists)
            $settings = $this->lift_line_model->get_settings_DB($id_resort);
            $tolerance_minutes  = (int)$settings->queue_tolerance_minutes;
            $vip_enabled        = (int)$settings->vip_fastpass_enabled;
            $vip_price          = (int)$settings->vip_fastpass_price;

            // Get all open lifts for this resort
            $open_lifts = $this->get_created_lifts_and_generic($id_resort);
            if ($open_lifts->num_rows() == 0) {
                continue;
            }

            $total_daily_capacity = 0;
            $lift_capacities      = [];
            $num_open_lifts       = 0;

            foreach ($open_lifts->result() as $lift) {
                // Daily throughput-based capacity (mirrors calc_visitors_slopes logic)
                $lift_daily_capacity = $lift->throughput * 7 / 10;
                $total_daily_capacity += $lift_daily_capacity;
                $lift_capacities[]    = ['lift' => $lift, 'capacity' => $lift_daily_capacity];
                $num_open_lifts++;
            }

            // Estimate each lift's share of visitors (proportional to its capacity)
            $overloaded_lifts = [];
            foreach ($lift_capacities as $entry) {
                $lift            = $entry['lift'];
                $lift_capacity   = $entry['capacity'];
                if ($total_daily_capacity > 0 && $lift_capacity > 0) {
                    // Proportional share of visitors this lift handles
                    $lift_visitor_share = $daily_visitors * ($lift_capacity / $total_daily_capacity);
                    if ($lift_visitor_share / $lift_capacity >= LIFT_LINE_OVERLOAD_RATIO) {
                        $overloaded_lifts[] = $lift;
                    }
                }
            }

            // ----------------------------------------------------------------
            // Queue time estimate (minutes)
            // If total capacity = 0 assume no wait (resort has no open lifts)
            // ----------------------------------------------------------------
            if ($total_daily_capacity > 0 && $daily_visitors > 0) {
                // Fraction of visitors that exceed capacity => extra wait time
                $overload_fraction = max(0, ($daily_visitors - $total_daily_capacity) / $total_daily_capacity);
                // Each full capacity worth of excess adds ~60 min (one full operational hour)
                $estimated_queue_minutes = round($overload_fraction * 60);
            } else {
                $estimated_queue_minutes = 0;
            }

            // ----------------------------------------------------------------
            // Reputation penalty for long queues
            // ----------------------------------------------------------------
            if ($estimated_queue_minutes > $tolerance_minutes) {
                $excess_minutes  = $estimated_queue_minutes - $tolerance_minutes;
                $rep_penalty     = min($excess_minutes * LIFT_LINE_REP_PENALTY_PER_MIN, LIFT_LINE_MAX_REP_PENALTY);

                // VIP fast pass reduces the penalty
                if ($vip_enabled == 1) {
                    $rep_penalty = round($rep_penalty * (1 - LIFT_LINE_VIP_REP_REDUCTION));
                }

                // Halve penalty for easy mode players
                if ($this->users_model->get_difficulty_mode($currentUserID) == 1) {
                    $rep_penalty = (int)ceil($rep_penalty / 2);
                }

                $rep_penalty = max(0, round($rep_penalty));

                if ($rep_penalty > 0) {
                    $this->db->trans_start();
                    $this->db->set('reputation', 'reputation-' . $rep_penalty, FALSE);
                    $this->db->where('id_resort', $id_resort);
                    $this->db->update('game_resorts');
                    $this->db->trans_complete();

                    $log_data = $this->lang->line('logs')['your_resort_lost'] . $rep_penalty . $this->lang->line('logs')['reputation_points'] . ' ' . $this->lang->line('logs')['due_to'] . ' ' . $this->lang->line('logs')['lift_queue_wait'];
                    $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
                    log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_lift_line_management", "Resort {$id_resort} lost {$rep_penalty} reputation from lift queue ({$estimated_queue_minutes} min, tolerance {$tolerance_minutes} min).\n");
                }
            }

            // ----------------------------------------------------------------
            // VIP fast-pass revenue
            // ----------------------------------------------------------------
            if ($vip_enabled == 1 && $daily_visitors > 0 && $vip_price > 0) {
                $vip_guests  = round($daily_visitors * LIFT_LINE_VIP_BYPASS_RATIO);
                $vip_revenue = $vip_guests * $vip_price;
                if ($vip_revenue > 0) {
                    $this->add_revenue_DB($id_resort, $vip_revenue);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_lift_line_management", "Resort {$id_resort} earned {$vip_revenue} € from VIP fast passes ({$vip_guests} guests × {$vip_price} €).\n");
                }
            }

            // ----------------------------------------------------------------
            // Overloaded lift breakdown chance
            // ----------------------------------------------------------------
            foreach ($overloaded_lifts as $lift) {
                $roll = mt_rand(1, 100);
                if ($roll <= LIFT_LINE_BREAKDOWN_CHANCE) {
                    // Retrieve id_group and level for repair cost calculation
                    $lift_details = $this->db
                        ->select('game_created_lifts.id_group, game_created_lifts.level')
                        ->from('game_created_lifts')
                        ->where('game_created_lifts.id_created_lifts', (int)$lift->id_created_lifts)
                        ->get()
                        ->row();

                    $repair_cost = 0;
                    if ($lift_details) {
                        $generic_item = $this->item_model->get_generic_item_info_for_level($lift_details->id_group, 'lift', $lift_details->level);
                        if ($generic_item && $generic_item->num_rows() > 0) {
                            $data_generic   = $generic_item->row();
                            $weightedValues = ['2' => 20, '8' => 25, '15' => 30, '20' => 15, '30' => 5, '40' => 5];
                            $coefficient    = getRandomWeightedElement($weightedValues);
                            $lift_condition = isset($lift->lift_condition) ? (int)$lift->lift_condition : 50;
                            $repair_cost    = (int)round($data_generic->base_cost / $coefficient * (100 - $lift_condition) / 100);
                        }
                    }

                    $this->set_maintenance_mode($lift->id_created_lifts, $repair_cost);
                    $log_data = $lift->custom_name . $this->lang->line('logs')['put_in_maintenance'];
                    $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
                    log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_lift_line_management", "Lift {$lift->id_created_lifts} ({$lift->custom_name}) in resort {$id_resort} broke down due to overload.\n");
                }
            }
        }
    }
    // =========================================================================
    // INSURANCE
    // =========================================================================

    /**
     * process_insurance_premiums   Charges the daily insurance premium for every resort
     *                              that has an active plan (basic or premium).
     *
     * @param CI_DB_result $list_all_resorts   All resort rows
     */
    protected function process_insurance_premiums($list_all_resorts) {
        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort = $resort_row->id_resort;
            $id_player = $resort_row->id_player;
            $pref_lang = !empty($resort_row->preferred_lang) ? $resort_row->preferred_lang : 'english';
            $this->lang->load('logs', $pref_lang);

            $insurance = $this->insurance_model->get_settings_DB($id_resort);

            $premium = 0;
            if ($insurance->plan === 'premium') {
                $premium = INSURANCE_DAILY_PREMIUM_PREMIUM;
            } elseif ($insurance->plan === 'basic') {
                $premium = INSURANCE_DAILY_PREMIUM_BASIC;
            }

            if ($premium > 0) {
                $this->take_cost_DB($id_resort, $premium);
                add_cost_stat_table($id_resort, $premium, 'cost_upkeep',  $this->yesterdays_date);
                add_cost_stat_table($id_resort, $premium, 'expenses',     $this->yesterdays_date);

                $msg = ($this->lang->line('logs')['insurance_premium_charged'] ?? 'Insurance premium charged:') . ' ' . number_format($premium, 0, '.', ' ') . ' € (' . $insurance->plan . ')';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['insurance'] ?? 'Insurance', 'data' => $msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['insurance'] ?? 'Insurance', 'data' => $msg]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_insurance_premiums", "Charged {$premium} € for {$insurance->plan} plan.\n");
            }   // close if ($premium > 0)
        }       // close foreach
    }           // close process_insurance_premiums

    // VIP & LOYALTY PROGRAMMES
    // =========================================================================

    /**
     * process_vip_loyalty      Runs nightly VIP & loyalty programme calculations:
     *   1. Apply loyalty discount cost (revenue reduction for discounted guests).
     *   2. Award reputation for active loyalty / VIP services.
     *   3. Charge nightly operating costs for private lift, premium slopes, concierge.
     *   4. Generate concierge and private-lift premium revenue.
     *
     * @param array $visitor_calculations   Per-resort visitor data from the open-resorts loop
     */
    protected function process_vip_loyalty($visitor_calculations) {
        foreach ($visitor_calculations as $resort_data) {
            $id_resort      = $resort_data['id_resort'];
            $daily_visitors = $resort_data['daily_visitors'];

            $settings = $this->vip_loyalty_model->get_settings_DB($id_resort);
            $loyalty_enabled    = (int)$settings->loyalty_enabled;
            $loyalty_discount   = (int)$settings->loyalty_discount_pct;
            $vip_private_lift   = (int)$settings->vip_private_lift;
            $vip_premium_slopes = (int)$settings->vip_premium_slopes;
            $vip_concierge      = (int)$settings->vip_concierge;
            $vip_airport_transfer = (int)$settings->vip_airport_transfer;
            $vip_apreski_lounge   = (int)$settings->vip_apreski_lounge;

            $total_rep_gain = 0;
            $total_revenue  = 0;
            $total_cost     = 0;

            // ----------------------------------------------------------------
            // Loyalty discount programme
            // ----------------------------------------------------------------
            if ($loyalty_enabled == 1 && $daily_visitors > 0) {
                $loyal_guest_count    = round($daily_visitors * VIP_LOYALTY_VISITOR_PCT);
                $loyalty_discount_cost = round($loyal_guest_count * VIP_LOYALTY_AVG_PASS_PRICE * ($loyalty_discount / 100));
                if ($loyalty_discount_cost > 0) {
                    $total_cost += $loyalty_discount_cost;
                }
                $total_rep_gain += VIP_LOYALTY_REP_BONUS;
            }

            // ----------------------------------------------------------------
            // VIP private lift service
            // ----------------------------------------------------------------
            if ($vip_private_lift == 1) {
                $total_cost     += VIP_PRIVATE_LIFT_COST;
                $total_rep_gain += VIP_PRIVATE_LIFT_REP_BONUS;
                if ($daily_visitors > 0) {
                    $vip_lift_guest_count = round($daily_visitors * VIP_PRIVATE_LIFT_GUEST_RATIO);
                    $total_revenue       += $vip_lift_guest_count * VIP_PRIVATE_LIFT_REVENUE_PER_VISITOR;
                }
            }

            // ----------------------------------------------------------------
            // VIP premium slopes
            // ----------------------------------------------------------------
            if ($vip_premium_slopes == 1) {
                $total_cost     += VIP_PREMIUM_SLOPES_COST;
                $total_rep_gain += VIP_PREMIUM_SLOPES_REP_BONUS;
            }

            // ----------------------------------------------------------------
            // VIP concierge service
            // ----------------------------------------------------------------
            if ($vip_concierge == 1) {
                $total_cost     += VIP_CONCIERGE_COST;
                $total_rep_gain += VIP_CONCIERGE_REP_BONUS;
                if ($daily_visitors > 0) {
                    $concierge_guest_count = round($daily_visitors * VIP_CONCIERGE_GUEST_RATIO);
                    $total_revenue        += $concierge_guest_count * VIP_CONCIERGE_REVENUE_PER_VISITOR;
                }
            }

            // ----------------------------------------------------------------
            // VIP airport transfer service
            // ----------------------------------------------------------------
            if ($vip_airport_transfer == 1) {
                $total_cost     += VIP_AIRPORT_TRANSFER_COST;
                $total_rep_gain += VIP_AIRPORT_TRANSFER_REP_BONUS;
                if ($daily_visitors > 0) {
                    $transfer_guest_count = round($daily_visitors * VIP_AIRPORT_TRANSFER_GUEST_RATIO);
                    $total_revenue       += $transfer_guest_count * VIP_AIRPORT_TRANSFER_REVENUE_PER_VISITOR;
                }
            }

            // ----------------------------------------------------------------
            // VIP après-ski lounge
            // ----------------------------------------------------------------
            if ($vip_apreski_lounge == 1) {
                $total_cost     += VIP_APRESKI_LOUNGE_COST;
                $total_rep_gain += VIP_APRESKI_LOUNGE_REP_BONUS;
                if ($daily_visitors > 0) {
                    $lounge_guest_count = round($daily_visitors * VIP_APRESKI_LOUNGE_GUEST_RATIO);
                    $total_revenue     += $lounge_guest_count * VIP_APRESKI_LOUNGE_REVENUE_PER_VISITOR;
                }
            }

            // ----------------------------------------------------------------
            // Apply reputation gain
            // ----------------------------------------------------------------
            if ($total_rep_gain > 0) {
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation+' . $total_rep_gain, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();
            }

            // ----------------------------------------------------------------
            // Apply revenue
            // ----------------------------------------------------------------
            if ($total_revenue > 0) {
                $this->add_revenue_DB($id_resort, $total_revenue);
                add_revenue_stat_table($id_resort, $total_revenue, 'revenue',   $this->yesterdays_date);
                add_revenue_stat_table($id_resort, $total_revenue, 'rev_other', $this->yesterdays_date);
            }

            // ----------------------------------------------------------------
            // Apply costs
            // ----------------------------------------------------------------
            if ($total_cost > 0) {
                $this->take_cost_DB($id_resort, $total_cost);
                add_cost_stat_table($id_resort, $total_cost, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($id_resort, $total_cost, 'expenses',    $this->yesterdays_date);
            }

            if ($total_rep_gain > 0 || $total_revenue > 0 || $total_cost > 0) {
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_vip_loyalty",
                    "Resort {$id_resort}: rep+{$total_rep_gain}, rev+{$total_revenue}€, cost-{$total_cost}€\n");
            }
        }
    }

    // CROWDING SYSTEM
    // =========================================================================

    /**
     * process_crowding_system      Evaluates daily visitor counts against each resort's
     *                              capacity settings and applies:
     *   - Reputation penalty when visitors exceed the crowd alert threshold
     *   - Halved penalty when timed entry is enabled
     *   - Reputation bonus when timed entry is on and crowding is within the threshold
     *
     * @param array $visitor_calculations   Array produced by visitor_calculations()
     */
    protected function process_crowding_system($visitor_calculations) {
        foreach ($visitor_calculations as $resort_data) {
            $id_resort      = $resort_data['id_resort'];
            $daily_visitors = $resort_data['daily_visitors'];

            $currentUserID         = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);

            // Fetch per-resort crowding settings (returns defaults when no row exists)
            $settings              = $this->crowding_model->get_settings_DB($id_resort);
            $capacity_limit        = (int)$settings->capacity_limit;
            $timed_entry_enabled   = (int)$settings->timed_entry_enabled;
            $crowd_alert_threshold = (int)$settings->crowd_alert_threshold;

            if ($capacity_limit <= 0) {
                continue;
            }

            // Calculate how crowded the resort is as a % of capacity
            $crowd_ratio = ($daily_visitors / $capacity_limit) * 100;

            if ($crowd_ratio > $crowd_alert_threshold) {
                // ----------------------------------------------------------------
                // Reputation penalty for overcrowding
                // ----------------------------------------------------------------
                $excess_pct  = $crowd_ratio - $crowd_alert_threshold;
                $rep_penalty = min($excess_pct * CROWDING_REP_PENALTY_PER_PCT, CROWDING_MAX_REP_PENALTY);

                // Timed entry reduces the penalty
                if ($timed_entry_enabled == 1) {
                    $rep_penalty = round($rep_penalty * (1 - CROWDING_TIMED_ENTRY_REP_REDUCTION));
                }

                // Halve penalty for easy mode players
                if ($this->users_model->get_difficulty_mode($currentUserID) == 1) {
                    $rep_penalty = (int)ceil($rep_penalty / 2);
                }

                $rep_penalty = max(0, round($rep_penalty));

                if ($rep_penalty > 0) {
                    $this->db->trans_start();
                    $this->db->set('reputation', 'reputation-' . $rep_penalty, FALSE);
                    $this->db->where('id_resort', $id_resort);
                    $this->db->update('game_resorts');
                    $this->db->trans_complete();

                    $log_data = $this->lang->line('logs')['your_resort_lost'] . $rep_penalty . $this->lang->line('logs')['reputation_points'] . ' ' . $this->lang->line('logs')['due_to'] . ' ' . $this->lang->line('logs')['crowding_overcrowded'];
                    $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['crowding'], 'data' => $log_data]);
                    log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['crowding'], 'data' => $log_data]);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_crowding_system", "Resort {$id_resort} lost {$rep_penalty} reputation from overcrowding ({$daily_visitors} visitors, {$crowd_ratio}% of capacity, threshold {$crowd_alert_threshold}%).\n");
                }
            } elseif ($timed_entry_enabled == 1 && $daily_visitors > 0) {
                // ----------------------------------------------------------------
                // Reputation bonus: timed entry is active and crowding is controlled
                // ----------------------------------------------------------------
                $rep_bonus = CROWDING_TIMED_ENTRY_REP_BONUS;
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation+' . $rep_bonus, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();

                $log_data = $this->lang->line('logs')['crowding_rep_bonus'];
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['crowding'], 'data' => $log_data]);
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['crowding'], 'data' => $log_data]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_crowding_system", "Resort {$id_resort} earned +{$rep_bonus} reputation for well-managed crowding (timed entry active, {$daily_visitors} visitors, {$crowd_ratio}% of capacity).\n");
            }
        }
    }

    // MAINTENANCE DEPTH
    // =========================================================================

    /**
     * process_maintenance_depth    Nightly mechanical-failure simulation for open lifts.
     *
     * For each open resort the method:
     *   1. Fetches the resort's maintenance plan (basic / standard / preventive).
     *   2. Charges the daily plan cost (standard or preventive) for every open lift.
     *   3. For each open lift that is NOT already in maintenance:
     *      a. Calculates a failure probability from base chance, lift type, age, and usage.
     *      b. Applies a failure-chance reduction for the preventive plan.
     *      c. Rolls a random number; on failure the lift is placed in maintenance.
     *      d. Computes repair cost reduced by the plan discount and the resort's
     *         average lift-mechanic efficiency (up to MAINT_STAFF_MAX_REPAIR_DISCOUNT).
     *
     * @param array $visitor_calculations   Array produced by visitor_calculations()
     */
    protected function process_maintenance_depth($visitor_calculations) {
        foreach ($visitor_calculations as $resort_data) {
            $id_resort      = $resort_data['id_resort'];
            $daily_visitors = $resort_data['daily_visitors'];

            $currentUserID       = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_lang         = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs',     $player_lang);
            $this->lang->load('building', $player_lang);

            // Maintenance plan for this resort
            $settings = $this->maintenance_depth_model->get_settings_DB($id_resort);
            $plan     = $settings->maintenance_plan;

            // Average liftmechanic efficiency  (0–100)
            $avg_eff = $this->maintenance_depth_model->get_avg_liftmechanic_efficiency_DB($id_resort);
            // Staff repair discount: proportional to efficiency, capped at MAINT_STAFF_MAX_REPAIR_DISCOUNT
            $staff_discount = min(($avg_eff / 100) * MAINT_STAFF_MAX_REPAIR_DISCOUNT, MAINT_STAFF_MAX_REPAIR_DISCOUNT);

            // Fetch all open lifts for this resort (including type, age data)
            $open_lifts = $this->db
                ->select('game_created_lifts.id_created_lifts, game_created_lifts.custom_name,
                          game_created_lifts.id_group, game_created_lifts.level,
                          game_created_lifts.lift_condition, game_created_lifts.install_date,
                          game_lifts.lift_type, game_lifts.throughput')
                ->from('game_created_lifts')
                ->join('game_lifts', 'game_lifts.id_group = game_created_lifts.id_group AND game_lifts.level = game_created_lifts.level', 'inner')
                ->where('game_created_lifts.id_resort', $id_resort)
                ->where('game_created_lifts.id_status', '1')
                ->get();

            if ($open_lifts->num_rows() == 0) {
                continue;
            }

            $num_open_lifts = $open_lifts->num_rows();

            // --- Daily plan cost ---
            $plan_cost_per_lift = 0;
            if ($plan === 'standard') {
                $plan_cost_per_lift = MAINT_PLAN_STANDARD_COST_PER_LIFT;
            } elseif ($plan === 'preventive') {
                $plan_cost_per_lift = MAINT_PLAN_PREVENTIVE_COST_PER_LIFT;
            }
            if ($plan_cost_per_lift > 0) {
                $total_plan_cost = $plan_cost_per_lift * $num_open_lifts;
                $this->take_cost_DB($id_resort, $total_plan_cost);
                add_cost_stat_table($id_resort, $total_plan_cost, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($id_resort, $total_plan_cost, 'expenses',    $this->yesterdays_date);
                $log_plan = $this->lang->line('building')['maint_depth_plan_cost_log'] . number_format($total_plan_cost) . ' €';
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_plan]);
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_plan]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_maintenance_depth", "Plan cost {$total_plan_cost} € charged ({$num_open_lifts} lifts × {$plan_cost_per_lift} €).\n");
            }

            // Total lift throughput for usage ratio
            $total_throughput = 0;
            foreach ($open_lifts->result() as $lift) {
                $total_throughput += max(1, (int)$lift->throughput);
            }

            // --- Per-lift failure check ---
            foreach ($open_lifts->result() as $lift) {
                // Base failure chance
                $failure_chance = (float)MAINT_BASE_FAILURE_CHANCE;

                // Lift-type multiplier
                $lift_type_mult = MAINT_LIFT_TYPE_FAILURE_MULT[(int)$lift->lift_type] ?? 1.0;
                $failure_chance *= $lift_type_mult;

                // Age bonus
                $age_seasons     = $this->lift_age_seasons($lift->install_date);
                $failure_chance += $age_seasons * MAINT_AGE_FAILURE_PER_SEASON;

                // Usage bonus: proportion of visitors this lift handles vs 50 % capacity threshold
                if ($total_throughput > 0) {
                    $lift_capacity_day  = max(1, (int)$lift->throughput) * 7 / 10;
                    $lift_visitor_share = $daily_visitors * ((max(1, (int)$lift->throughput) / $total_throughput));
                    $load_pct           = ($lift_capacity_day > 0) ? ($lift_visitor_share / $lift_capacity_day) * 100 : 0;
                    if ($load_pct > 50) {
                        $excess_10pct_blocks = floor(($load_pct - 50) / 10);
                        $failure_chance     += $excess_10pct_blocks * MAINT_USAGE_FAILURE_PER_10PCT;
                    }
                }

                // Preventive plan halves the failure chance
                if ($plan === 'preventive') {
                    $failure_chance *= (1 - MAINT_PLAN_PREVENTIVE_FAILURE_REDUCTION);
                }

                $failure_chance = max(0.0, min($failure_chance, 50.0)); // cap at 50 %

                // Roll
                $roll = mt_rand(1, 10000);
                if ($roll > (int)round($failure_chance * 100)) {
                    continue; // no failure
                }

                // Compute repair cost
                $repair_cost = 0;
                $generic_item = $this->item_model->get_generic_item_info_for_level($lift->id_group, 'lift', $lift->level);
                if ($generic_item && $generic_item->num_rows() > 0) {
                    $data_generic   = $generic_item->row();
                    $weightedValues = ['2' => 20, '8' => 25, '15' => 30, '20' => 15, '30' => 5, '40' => 5];
                    $coefficient    = getRandomWeightedElement($weightedValues);
                    $lift_condition = max(1, (int)$lift->lift_condition);
                    $base_repair    = $data_generic->base_cost / $coefficient * (100 - $lift_condition) / 100;

                    // Apply plan repair discount
                    $plan_discount = 0.0;
                    if ($plan === 'standard') {
                        $plan_discount = MAINT_PLAN_STANDARD_REPAIR_DISCOUNT;
                    } elseif ($plan === 'preventive') {
                        $plan_discount = MAINT_PLAN_PREVENTIVE_REPAIR_DISCOUNT;
                    }
                    $total_discount = min(1.0, $plan_discount + $staff_discount);
                    $repair_cost    = (int)round($base_repair * (1 - $total_discount));
                }

                $this->set_maintenance_mode($lift->id_created_lifts, $repair_cost);
                $log_data = $lift->custom_name . $this->lang->line('building')['maint_depth_failure_log'];
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['lift'], 'data' => $log_data]);

                // Track lift breakdown for seasonal objectives
                $current_season_breakdown = get_current_season($id_resort);
                if ($current_season_breakdown) {
                    $this->seasonal_objectives_model->record_lift_breakdown($id_resort, $current_season_breakdown);
                }
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_maintenance_depth", "Lift {$lift->id_created_lifts} ({$lift->custom_name}) failed (chance={$failure_chance}%, roll={$roll}, plan={$plan}, staff_discount=" . round($staff_discount * 100) . "%).\n");
            }
        }
    }
    // SPONSORSHIP & BRANDING
    // =========================================================================

    /**
     * process_sponsorships     Runs nightly sponsor contract processing for all resorts:
     *   1. Pay daily sponsor revenue into resort cash.
     *   2. Apply event_title reputation bonus.
     *   3. Check resort reputation vs sponsor minimum; update brand satisfaction.
     *   4. Cancel contract and apply reputation penalty when satisfaction reaches 0.
     *
     * @param object $list_all_resorts   Result of list_all_resorts query (all resorts)
     */
    protected function process_sponsorships($list_all_resorts) {
        $all_active = $this->sponsorship_model->get_all_active_sponsorships_DB();
        if (empty($all_active)) {
            return;
        }

        // Group contracts by resort
        $contracts_by_resort = [];
        foreach ($all_active as $contract) {
            $contracts_by_resort[(int)$contract->id_resort][] = $contract;
        }

        foreach ($contracts_by_resort as $id_resort => $contracts) {
            $currentUserID         = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('building', $player_preferred_lang);
            $this->lang->load('logs',     $player_preferred_lang);

            // Fetch current reputation
            $resort_row        = $this->db->select('reputation')->from('game_resorts')->where('id_resort', $id_resort)->limit(1)->get()->row();
            $resort_reputation = $resort_row ? (int)$resort_row->reputation : 0;
            $total_revenue     = 0;

            foreach ($contracts as $contract) {
                $sponsor_type  = $contract->sponsor_type;
                $contract_level= (int)$contract->contract_level;
                $satisfaction  = (int)$contract->brand_satisfaction;

                if (!isset(SPONSORSHIP_TYPES[$sponsor_type])) {
                    continue;
                }
                $cfg = SPONSORSHIP_TYPES[$sponsor_type];
                $idx = $contract_level - 1;

                // 1. Daily revenue
                $daily_rev = (int)($cfg['revenue_per_level'][$idx] ?? 0);
                if ($daily_rev > 0) {
                    $total_revenue += $daily_rev;
                }

                // 2. Event title reputation bonus
                if ($sponsor_type === 'event_title' && isset($cfg['rep_bonus_per_level'][$idx])) {
                    $rep_bonus = (int)$cfg['rep_bonus_per_level'][$idx];
                    if ($rep_bonus > 0) {
                        $this->db->trans_start();
                        $this->db->set('reputation', 'reputation+' . $rep_bonus, FALSE);
                        $this->db->where('id_resort', $id_resort);
                        $this->db->update('game_resorts');
                        $this->db->trans_complete();
                        $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_sponsorships", "Event title sponsor added {$rep_bonus} reputation to resort {$id_resort}.\n");
                    }
                }

                // 3. Brand satisfaction update
                $min_rep = (int)($cfg['min_reputation'][$idx] ?? 0);
                if ($resort_reputation >= $min_rep) {
                    $new_sat = $satisfaction + SPONSORSHIP_SATISFACTION_GAIN;
                } else {
                    $new_sat = $satisfaction - SPONSORSHIP_SATISFACTION_LOSS;
                }

                $result = $this->sponsorship_model->update_satisfaction_DB($id_resort, $sponsor_type, $new_sat);

                // 4. Sponsor cancellation
                if ($result === 'cancelled') {
                    // Apply reputation penalty
                    $this->db->trans_start();
                    $this->db->set('reputation', 'reputation-' . SPONSORSHIP_CANCEL_REP_PENALTY, FALSE);
                    $this->db->where('id_resort', $id_resort);
                    $this->db->update('game_resorts');
                    $this->db->trans_complete();

                    $log_data = $this->lang->line('building')['sponsorship_cancelled_log']
                        . ' ' . $this->lang->line('building')['sponsorship_type_' . $sponsor_type];
                    $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $log_data]);
                    log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['resort'], 'data' => $log_data]);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_sponsorships", "Sponsor '{$sponsor_type}' cancelled contract for resort {$id_resort} (satisfaction=0). Reputation penalty applied.\n");
                }
            }

            // Pay total daily revenue in one shot
            if ($total_revenue > 0) {
                $this->add_revenue_DB($id_resort, $total_revenue);
                $log_data = $this->lang->line('building')['sponsorship_revenue_log'] . ' ' . number_format($total_revenue, 0, '.', ' ') . ' €';
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $log_data]);
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['revenues'], 'data' => $log_data]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_sponsorships", "Resort {$id_resort} earned {$total_revenue} € in sponsorship revenue.\n");
            }
        }
    }

    // =========================================================================
    // ENVIRONMENTAL SYSTEM
    // =========================================================================

    /**
     * process_environmental_system     Runs all nightly environmental calculations:
     *   1. Compute carbon footprint and noise pollution from active equipment.
     *   2. Update eco reputation.
     *   3. Apply fines for high pollution.
     *   4. Set / lift expansion restriction flag.
     *   5. Log player notifications.
     *
     * @param CI_DB_result $list_all_resorts  All resort rows (vacation mode excluded elsewhere)
     */
    protected function process_environmental_system($list_all_resorts) {
        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort   = $resort_row->id_resort;
            $id_player   = $resort_row->id_player;
            $player_lang = $resort_row->preferred_lang ?? 'english';
            $this->lang->load('logs', $player_lang);

            // Ensure environment row exists
            $env = $this->environment_model->get_environment_DB($id_resort);

            // ------------------------------------------------------------------
            // 1. Count active equipment
            // ------------------------------------------------------------------
            // Open lifts
            $open_lifts = (int)$this->db
                ->where('id_resort', $id_resort)
                ->where('id_status', 1)
                ->count_all_results('game_created_lifts');

            // Active snow cannons
            $active_cannons = (int)$this->db
                ->where('id_resort', $id_resort)
                ->where('id_status', 1)
                ->where('id_building', 2)   // cannon building type
                ->count_all_results('game_created_buildings');

            // All purchased groomers (type 1)
            $total_groomers = (int)$this->db
                ->where('id_resort', $id_resort)
                ->where('id_equipment', 1)
                ->count_all_results('game_purchased_equipments');

            $electric_groomers = (int)$env->electric_groomers;
            // Diesel groomers = max(0, total - electric)
            $diesel_groomers = max(0, $total_groomers - $electric_groomers);

            $tree_count      = isset($env->tree_count)      ? (int)$env->tree_count      : 0;
            $water_recycling = isset($env->water_recycling) ? (int)$env->water_recycling : 0;

            // ------------------------------------------------------------------
            // 2. Calculate carbon footprint
            // ------------------------------------------------------------------
            $carbon = ($open_lifts    * ENV_CARBON_PER_LIFT)
                    + ($active_cannons * ENV_CARBON_PER_CANNON)
                    + ($diesel_groomers * ENV_CARBON_PER_GROOMER)
                    + ($electric_groomers * ENV_CARBON_PER_ELECTRIC_GROOMER);

            // Solar panels reduce carbon by 20 %
            if ($env->solar_panels == 1)
                $carbon = (int)round($carbon * (1 - ENV_SOLAR_CARBON_REDUCTION));

            // Each reforestation batch reduces carbon
            $carbon = max(0, $carbon - ($tree_count * ENV_TREE_CARBON_REDUCTION));

            // ------------------------------------------------------------------
            // 3. Calculate noise pollution
            // ------------------------------------------------------------------
            // Water recycling reduces snow cannon noise by 30 %
            $cannon_noise = $water_recycling
                ? (int)round($active_cannons * ENV_NOISE_PER_CANNON * (1 - ENV_WATER_RECYCLING_NOISE_REDUCTION))
                : ($active_cannons * ENV_NOISE_PER_CANNON);

            $noise = ($open_lifts    * ENV_NOISE_PER_LIFT)
                   + $cannon_noise
                   + ($diesel_groomers * ENV_NOISE_PER_GROOMER)
                   + ($electric_groomers * ENV_NOISE_PER_ELECTRIC_GROOMER);

            // ------------------------------------------------------------------
            // 4. Update eco reputation
            // ------------------------------------------------------------------
            $eco_rep = (int)$env->eco_reputation;

            if ($env->solar_panels == 1)     $eco_rep += ENV_REP_SOLAR_BONUS;
            if ($electric_groomers > 0)      $eco_rep += $electric_groomers * ENV_REP_ELECTRIC_GROOMER_BONUS;
            if ($env->wildlife_zone == 1)    $eco_rep += ENV_REP_WILDLIFE_BONUS;
            if ($tree_count > 0)             $eco_rep += $tree_count * ENV_TREE_REP_BONUS;
            if ($water_recycling)            $eco_rep += ENV_WATER_RECYCLING_REP_BONUS;
            if ($carbon > ENV_CARBON_RESTRICT_THRESHOLD) {
                $eco_rep += ENV_REP_VERY_HIGH_CARBON_PENALTY;
            } elseif ($carbon > ENV_CARBON_FINE_THRESHOLD) {
                $eco_rep += ENV_REP_HIGH_CARBON_PENALTY;
            }
            if ($noise > ENV_NOISE_FINE_THRESHOLD) $eco_rep += ENV_REP_HIGH_NOISE_PENALTY;

            // Clamp to 0–100
            $eco_rep = max(0, min(100, $eco_rep));

            // ------------------------------------------------------------------
            // 5. Expansion restriction
            // ------------------------------------------------------------------
            $was_restricted  = (int)$env->expansion_restricted;
            $now_restricted  = ($carbon > ENV_CARBON_RESTRICT_THRESHOLD) ? 1 : 0;

            // ------------------------------------------------------------------
            // 6. Save updated values
            // ------------------------------------------------------------------
            $this->environment_model->update_environment_DB($id_resort, [
                'carbon_footprint'     => $carbon,
                'noise_pollution'      => $noise,
                'eco_reputation'       => $eco_rep,
                'expansion_restricted' => $now_restricted,
            ]);

            // ------------------------------------------------------------------
            // 7. Apply fines
            // ------------------------------------------------------------------
            $fine_total = 0;
            $fine_msg   = '';

            if ($carbon > ENV_CARBON_RESTRICT_THRESHOLD) {
                $fine_total += ENV_CARBON_RESTRICT_FINE;
                $fine_msg   .= ENV_CARBON_RESTRICT_FINE . ' € ';
            } elseif ($carbon > ENV_CARBON_FINE_THRESHOLD) {
                $fine_total += ENV_CARBON_FINE_AMOUNT;
                $fine_msg   .= ENV_CARBON_FINE_AMOUNT . ' € ';
            }

            if ($noise > ENV_NOISE_FINE_THRESHOLD && $env->wildlife_zone == 1) {
                $fine_total += ENV_NOISE_FINE_AMOUNT;
                $fine_msg   .= '+ ' . ENV_NOISE_FINE_AMOUNT . ' € (noise) ';
            }

            if ($fine_total > 0) {
                $this->take_cost_DB($id_resort, $fine_total);
                add_cost_stat_table($id_resort, $fine_total, 'cost_upkeep', $this->yesterdays_date);
                add_cost_stat_table($id_resort, $fine_total, 'expenses',    $this->yesterdays_date);

                $pollution_fine_label = $this->lang->line('logs')['env_pollution_fine'] ?? 'Environmental fine:';
                $notification_text = $pollution_fine_label . ' ' . number_format($fine_total, 0, '.', ' ') . ' € (' . trim($fine_msg) . ')';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['environment'], 'data' => $notification_text]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['environment'], 'data' => $notification_text]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_environmental_system", "Fine of {$fine_total} € applied (carbon={$carbon}, noise={$noise})\n");
            }

            // Notify about restriction change
            if ($now_restricted && !$was_restricted) {
                $restrict_msg = $this->lang->line('logs')['env_expansion_restricted'] ?? 'Resort expansion restricted due to excessive carbon footprint.';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['environment'], 'data' => $restrict_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['environment'], 'data' => $restrict_msg]);
            } elseif (!$now_restricted && $was_restricted) {
                $restore_msg = $this->lang->line('logs')['env_expansion_restored'] ?? 'Resort expansion restrictions lifted.';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['environment'], 'data' => $restore_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['environment'], 'data' => $restore_msg]);
            }

            $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_environmental_system",
                "carbon={$carbon} noise={$noise} eco_rep={$eco_rep} restricted={$now_restricted}\n");
        }
    }

    /**
     * process_government_regulations   Runs nightly government & regulation processing:
     *   1. Update compliance score based on eco reputation and expansion status.
     *   2. Block/unblock resort expansion when compliance crosses threshold.
     *   3. Random safety inspection audit.
     *   4. Apply daily regulation tax on yesterday's gross revenue.
     *   5. Award eco-subsidy at season start when eco reputation qualifies.
     *
     * @param CI_DB_result $list_all_resorts  All resort rows
     */
    protected function process_government_regulations($list_all_resorts) {
        $yesterday_GMT = $this->yesterdays_date;
        $today_GMT     = $this->todays_date;

        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort   = $resort_row->id_resort;
            $id_player   = $resort_row->id_player;
            $player_lang = $resort_row->preferred_lang ?? 'english';
            $this->lang->load('logs', $player_lang);
            $this->lang->load('building', $player_lang);

            // Ensure government row exists
            $gov = $this->government_model->get_government_DB($id_resort);
            $env = $this->environment_model->get_environment_DB($id_resort);

            $compliance   = (int)$gov->compliance_score;
            $eco_rep      = (int)$env->eco_reputation;
            $env_restricted = (int)$env->expansion_restricted;

            // ------------------------------------------------------------------
            // 1. Update compliance score
            // ------------------------------------------------------------------
            if ($eco_rep >= 70) {
                $compliance += GOV_COMPLIANCE_HIGH_ECO_BONUS;
            } elseif ($eco_rep < 30) {
                $compliance += GOV_COMPLIANCE_LOW_ECO_PENALTY;
            }
            if ($env_restricted) {
                $compliance += GOV_COMPLIANCE_RESTRICT_PENALTY;
            }
            $compliance = max(0, min(100, $compliance));

            // ------------------------------------------------------------------
            // 2. Expansion blocking
            // ------------------------------------------------------------------
            $was_blocked = (int)$gov->expansion_blocked;
            $now_blocked = ($compliance < GOV_COMPLIANCE_BLOCK_THRESHOLD) ? 1 : 0;

            if (!$was_blocked && $now_blocked) {
                $block_msg = $this->lang->line('logs')['gov_expansion_blocked'] ?? 'Expansion blocked by government (low compliance).';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $block_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $block_msg]);
            } elseif ($was_blocked && !$now_blocked && $compliance >= GOV_COMPLIANCE_RESTORE_THRESHOLD) {
                $now_blocked = 0;
                $restore_msg = $this->lang->line('logs')['gov_expansion_unblocked'] ?? 'Expansion restriction lifted (compliance restored).';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $restore_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $restore_msg]);
            } elseif ($was_blocked && $compliance < GOV_COMPLIANCE_RESTORE_THRESHOLD) {
                $now_blocked = 1; // remains blocked until compliance reaches restore threshold
            }

            // ------------------------------------------------------------------
            // 3. Safety inspection audit (random chance)
            // ------------------------------------------------------------------
            $audit_result = $gov->last_audit_result;
            $audit_date   = $gov->last_audit_date;
            $roll = rand(1, 100);
            if ($roll <= GOV_AUDIT_CHANCE) {
                $audit_date = $today_GMT;
                if ($compliance >= GOV_AUDIT_PASS_THRESHOLD) {
                    $audit_result = 'pass';
                    $compliance   = min(100, $compliance + GOV_COMPLIANCE_AUDIT_PASS_BONUS);
                    $this->add_revenue_DB($id_resort, GOV_AUDIT_PASS_REWARD);
                    add_revenue_stat_table($id_resort, GOV_AUDIT_PASS_REWARD, 'rev_other', $yesterday_GMT);
                    $audit_msg = ($this->lang->line('logs')['gov_audit_pass'] ?? 'Safety audit passed:') . ' +' . number_format(GOV_AUDIT_PASS_REWARD, 0, '.', ' ') . ' €';
                } else {
                    $audit_result = 'fail';
                    $compliance   = max(0, $compliance + GOV_COMPLIANCE_AUDIT_FAIL_PENALTY);
                    $fine = GOV_AUDIT_FAIL_FINE;
                    $this->take_cost_DB($id_resort, $fine);
                    add_cost_stat_table($id_resort, $fine, 'cost_upkeep', $yesterday_GMT);
                    add_cost_stat_table($id_resort, $fine, 'expenses',    $yesterday_GMT);
                    $this->db->trans_start();
                    $this->db->set('total_fines_paid', 'total_fines_paid + ' . (int)$fine, FALSE);
                    $this->db->where('id_resort', (int)$id_resort);
                    $this->db->update('game_resort_government');
                    $this->db->trans_complete();
                    $audit_msg = ($this->lang->line('logs')['gov_audit_fail'] ?? 'Safety audit failed – fine:') . ' -' . number_format($fine, 0, '.', ' ') . ' €';
                }
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $audit_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $audit_msg]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_government_regulations", "Audit: {$audit_result} (compliance={$compliance})\n");
            }

            // ------------------------------------------------------------------
            // 4. Daily regulation tax on gross revenue
            // ------------------------------------------------------------------
            $current_season = get_current_season($id_resort);
            $tax_rate       = (float)$gov->tax_rate;
            $tax_season     = (int)$gov->tax_season;

            // Rotate tax rate at the start of a new season
            if ($current_season > $tax_season) {
                $tax_rate   = (float)rand(GOV_TAX_RATE_MIN * 10, GOV_TAX_RATE_MAX * 10) / 10.0;
                $tax_season = $current_season;
                $rate_msg   = ($this->lang->line('logs')['gov_tax_rate_updated'] ?? 'Regulation tax rate updated for new season:') . ' ' . $tax_rate . '%';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $rate_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $rate_msg]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_government_regulations", "Tax rate updated to {$tax_rate}% for season {$tax_season}\n");
            }

            // Charge the regulation tax on yesterday's gross revenue
            $gross_revenue = (float)$this->finances_model->get_lastXdays_specific_amount_DB($id_resort, 'revenue', $yesterday_GMT, $today_GMT);
            if ($gross_revenue > 0) {
                $regulation_tax = (int)round($gross_revenue * $tax_rate / 100.0);
                if ($regulation_tax > 0) {
                    $this->take_cost_DB($id_resort, $regulation_tax);
                    add_cost_stat_table($id_resort, $regulation_tax, 'cost_taxes',  $yesterday_GMT);
                    add_cost_stat_table($id_resort, $regulation_tax, 'expenses',    $yesterday_GMT);
                    $tax_msg = ($this->lang->line('logs')['gov_tax_charged'] ?? 'Regulation tax charged:') . ' -' . number_format($regulation_tax, 0, '.', ' ') . ' € (' . $tax_rate . '%)';
                    $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $tax_msg]);
                    log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $tax_msg]);
                    $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_government_regulations", "Regulation tax {$regulation_tax} € charged ({$tax_rate}% of {$gross_revenue} €)\n");
                }
            }

            // ------------------------------------------------------------------
            // 5. Eco subsidy at season start
            // ------------------------------------------------------------------
            $subsidy_season    = (int)$gov->subsidy_season;
            $subsidy_available = (int)$gov->subsidy_available;

            if ($current_season > $subsidy_season && $eco_rep >= GOV_SUBSIDY_ECO_THRESHOLD) {
                $subsidy_available = GOV_SUBSIDY_AMOUNT;
                $subsidy_season    = $current_season;
                $sub_msg = ($this->lang->line('logs')['gov_subsidy_awarded'] ?? 'Eco-friendly subsidy available:') . ' ' . number_format(GOV_SUBSIDY_AMOUNT, 0, '.', ' ') . ' €';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $sub_msg]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['government'] ?? 'Government & Regulations', 'data' => $sub_msg]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_government_regulations", "Eco subsidy " . GOV_SUBSIDY_AMOUNT . " € awarded (eco_rep={$eco_rep})\n");
            } elseif ($current_season > $subsidy_season) {
                // New season but eco_rep too low — reset subsidy season tracker without awarding
                $subsidy_season = $current_season;
            }

            // ------------------------------------------------------------------
            // 6. Save all updates
            // ------------------------------------------------------------------
            $this->government_model->update_government_DB($id_resort, [
                'compliance_score'  => $compliance,
                'expansion_blocked' => $now_blocked,
                'tax_rate'          => $tax_rate,
                'tax_season'        => $tax_season,
                'subsidy_available' => $subsidy_available,
                'subsidy_season'    => $subsidy_season,
                'last_audit_result' => $audit_result,
                'last_audit_date'   => $audit_date,
            ]);
        }
    }   // close process_government_regulations

    /**
     * generate_revenue_off_season     Generates daily revenue for off-season summer activities.
     *
     * Unlike ski-season buildings this function iterates all resorts (not just
     * open ones) so that summer activities produce income year-round, independent
     * of ski conditions or whether the Tourist Information Center is open.
     *
     * Revenue is tracked in the 'rev_off_season' stats table and added directly
     * to the player's cash.
     *
     * @param object $list_all_resorts  Result set from list_all_resorts()
     */
    protected function generate_revenue_off_season($list_all_resorts) {
        $off_season_types = ['mountain_biking', 'hiking', 'festival', 'wedding_venue', 'alpine_coaster'];

        $today     = strtotime('now');
        $yesterday = strtotime('-1 day', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);

        $weightedValues = ['0.5' => 5, '0.6' => 15, '0.7' => 30, '0.8' => 30, '0.9' => 15, 1 => 5];

        foreach ($list_all_resorts->result() as $resort_row) {
            $current_resort = $resort_row->id_resort;
            $total_gains    = 0;

            foreach ($off_season_types as $building_type) {
                $buildings = $this->get_info_created_buildings($current_resort, $building_type);

                if ($buildings->num_rows() > 0) {
                    $total_max_income = 0;
                    foreach ($buildings->result() as $bld) {
                        $total_max_income += $bld->max_income * $bld->count_level;
                    }

                    $coefficient = getRandomWeightedElement($weightedValues);
                    $gains       = round($total_max_income * $coefficient);
                    $total_gains += $gains;

                    $this->logToFile(
                        $this->Log_filename, "INFO",
                        "[id_resort_{$current_resort}_type_{$building_type}]",
                        "generate_revenue_off_season",
                        "Resort {$current_resort} earned {$gains} € from {$building_type} (max_income={$total_max_income}, coef={$coefficient})\n"
                    );
                }
            }

            if ($total_gains > 0) {
                $this->add_revenue_DB($current_resort, $total_gains);
                add_revenue_stat_table($current_resort, $total_gains, 'revenue',         $yesterday_GMT);
                add_revenue_stat_table($current_resort, $total_gains, 'rev_off_season',  $yesterday_GMT);
            }
        }
    }

    // =========================================================================
    // RETAIL & AMENITIES
    // =========================================================================

    /**
     * generate_revenue_retail      Calculates nightly revenue for all enabled slope-side
     *                              shops (ski_shop, souvenir_shop, cafe, bar).
     *
     * Revenue formula per shop:
     *   revenue = base_revenue × (stock_level / 3) × pricing_multiplier
     *             × (popularity / RETAIL_POPULARITY_BASE) × seasonal_bonus
     *
     * Popularity drifts each night by:
     *   pricing_drift (RETAIL_PRICING_POP_DRIFT) + stock_drift (RETAIL_STOCK_POP_DRIFT)
     * clamped to [RETAIL_POPULARITY_MIN, RETAIL_POPULARITY_MAX].
     *
     * Seasonal bonus (RETAIL_SEASONAL_BONUS) applies when seasonal_items = 1
     * and the resort is currently open (Tourist Information Center built & open).
     *
     * @param CI_DB_result $list_all_resorts  All-resort result set
     */
    protected function generate_revenue_retail($list_all_resorts) {
        $today     = strtotime('now');
        $yesterday = strtotime('-1 day', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);

        $base_revenues      = RETAIL_BASE_REVENUE;
        $pricing_multiplier = RETAIL_PRICING_MULTIPLIER;
        $pricing_pop_drift  = RETAIL_PRICING_POP_DRIFT;
        $stock_pop_drift    = RETAIL_STOCK_POP_DRIFT;

        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort  = (int)$resort_row->id_resort;
            $id_player  = (int)$resort_row->id_player;

            $this->lang->load('logs', $resort_row->preferred_lang ?? 'english');

            // Determine if the resort is currently open (Tourist Information Center open)
            $is_open = $this->db
                ->from('game_created_buildings')
                ->where('id_resort', $id_resort)
                ->where('id_building', 1)
                ->where('id_status', 1)
                ->count_all_results() > 0;

            $enabled_shops = $this->retail_model->get_enabled_shops_DB($id_resort);

            if (empty($enabled_shops)) {
                continue;
            }

            $total_revenue = 0;

            foreach ($enabled_shops as $shop) {
                $shop_type        = $shop->shop_type;
                $stock_level      = max(RETAIL_STOCK_MIN, min(RETAIL_STOCK_MAX, (int)$shop->stock_level));
                $pricing_strategy = $shop->pricing_strategy;
                $seasonal_items   = (int)$shop->seasonal_items;
                $popularity       = max(RETAIL_POPULARITY_MIN, min(RETAIL_POPULARITY_MAX, (int)$shop->popularity));

                if (!isset($base_revenues[$shop_type])) {
                    continue;
                }

                $base     = $base_revenues[$shop_type];
                $p_mult   = $pricing_multiplier[$pricing_strategy] ?? 1.0;
                $pop_mult = $popularity / RETAIL_POPULARITY_BASE;
                $s_bonus  = ($seasonal_items == 1 && $is_open) ? RETAIL_SEASONAL_BONUS : 1.0;

                $shop_revenue = round($base * ($stock_level / 3) * $p_mult * $pop_mult * $s_bonus);
                $total_revenue += $shop_revenue;

                // Update popularity drift
                $pop_drift = ($pricing_pop_drift[$pricing_strategy] ?? 0)
                           + ($stock_pop_drift[$stock_level] ?? 0);
                $new_popularity = max(RETAIL_POPULARITY_MIN, min(RETAIL_POPULARITY_MAX, $popularity + $pop_drift));

                if ($new_popularity !== $popularity) {
                    $this->retail_model->update_popularity_DB($id_resort, $shop_type, $new_popularity);
                }

                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]",
                    "generate_revenue_retail",
                    "Shop {$shop_type}: revenue={$shop_revenue} € (stock={$stock_level}, pricing={$pricing_strategy}, pop={$popularity}→{$new_popularity}, seasonal={$seasonal_items})\n");
            }

            if ($total_revenue > 0) {
                $this->add_revenue_DB($id_resort, $total_revenue);
                add_revenue_stat_table($id_resort, $total_revenue, 'revenue',     $yesterday_GMT);
                add_revenue_stat_table($id_resort, $total_revenue, 'rev_retail',  $yesterday_GMT);

                $log_data = ($this->lang->line('logs')['retail_revenue'] ?? 'Retail revenue:') . ' ' . number_format($total_revenue, 0, '.', ' ') . ' €';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => ($this->lang->line('logs')['retail'] ?? 'Retail'), 'data' => $log_data]);
                log_user_action(['id_player' => $id_player, 'type' => ($this->lang->line('logs')['retail'] ?? 'Retail'), 'data' => $log_data]);
            }
        }
    }

    // =========================================================================
    // IDLE INCOME
    // =========================================================================

    /**
     * generate_idle_income     Accumulates passive offline income into game_resorts.pending_idle_income.
     *
     * Every night the cron adds a small amount of passive income for each
     * non-vacation-mode resort based on the number of open slopes and open
     * lifts.  The amount is capped at IDLE_INCOME_MAX_DAYS × (daily rate) so
     * that players who are away for a very long time do not receive a
     * disproportionate windfall on return.
     *
     * The income is NOT added directly to the player's cash here.  Instead it
     * is held in pending_idle_income until the player next visits their resort,
     * where it is collected in Resort_controller::display_resort_info().
     *
     * @param CI_DB_result $list_all_resorts  All-resort result set (includes vacation-mode)
     */
    protected function generate_idle_income($list_all_resorts) {
        $today     = strtotime('now');
        $yesterday = strtotime('-1 day', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);

        $per_slope = (int)IDLE_INCOME_PER_OPEN_SLOPE;
        $per_lift  = (int)IDLE_INCOME_PER_OPEN_LIFT;

        foreach ($list_all_resorts->result() as $resort_row) {
            // Skip vacation-mode players — their resort is paused
            if ($resort_row->vacation_mode == '1') {
                continue;
            }

            $id_resort = (int)$resort_row->id_resort;

            // Count open slopes (id_status = 1)
            $open_slopes = $this->db
                ->where('id_resort', $id_resort)
                ->where('id_status', '1')
                ->count_all_results('game_created_slopes');

            // Count open lifts (id_status = 1)
            $open_lifts = $this->db
                ->where('id_resort', $id_resort)
                ->where('id_status', '1')
                ->count_all_results('game_created_lifts');

            $daily_idle = ($open_slopes * $per_slope) + ($open_lifts * $per_lift);

            if ($daily_idle <= 0) {
                continue;
            }

            // Retrieve current pending balance to enforce the cap
            $resort_row_db = $this->db
                ->select('pending_idle_income')
                ->from('game_resorts')
                ->where('id_resort', $id_resort)
                ->get()
                ->row();

            if (!$resort_row_db) {
                continue;
            }

            $current_pending = (int)$resort_row_db->pending_idle_income;
            $max_pending     = $daily_idle * (int)IDLE_INCOME_MAX_DAYS;

            // Only add income up to the cap
            $space_remaining = $max_pending - $current_pending;
            if ($space_remaining <= 0) {
                continue;
            }
            $income_to_add = min($daily_idle, $space_remaining);

            // Accumulate in game_resorts.pending_idle_income
            $this->db->set('pending_idle_income', 'pending_idle_income + ' . $income_to_add, FALSE);
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_resorts');

            // Record in daily stat table (tracks total idle income generated per day)
            add_revenue_stat_table($id_resort, $income_to_add, 'rev_idle', $yesterday_GMT);

            $this->logToFile(
                $this->Log_filename, "INFO",
                "[id_resort_{$id_resort}]",
                "generate_idle_income",
                "Resort {$id_resort}: +{$income_to_add} € idle income accumulated"
                . " (slopes={$open_slopes}, lifts={$open_lifts}, pending_now=" . ($current_pending + $income_to_add) . ")\n"
            );
        }
    }

    /**
     * apply_natural_hazards    Runs all three seasonal hazard checks for every open resort.
     *
     * Hazards scale with season number to provide mid/late-game difficulty spikes:
     *   - Season 1 : base probability
     *   - Season 2+: increased probability
     *
     * @param CI_DB_result $list_all_opened_resorts   Open-resort query result
     * @param array        $weather_today             ['snow_level', 'name_english', ...]
     */
    protected function apply_natural_hazards($list_all_opened_resorts, $weather_today) {

        $snow_change    = (int)$weather_today['snow_level'];
        $weather_name   = $weather_today['name_english'];

        // Fetch today's weather condition row once (temperature, wind, danger)
        $todays_forecast = $this->weather_model->select_weather_forecast($this->todays_date);
        if (!$todays_forecast || $todays_forecast->num_rows() === 0) {
            $this->logToFile($this->Log_filename, "WARN", "[ ]", "apply_natural_hazards", "No weather forecast found for today, skipping hazards.\n");
            return;
        }
        $forecast_row  = $todays_forecast->row();
        $weather_cond  = $this->weather_model->select_weather_conditions($forecast_row->id_condition)->row();
        $is_danger     = (int)$weather_cond->danger === 1;
        $temperature   = (float)$weather_cond->temperature;

        foreach ($list_all_opened_resorts->result() as $resort_row) {
            $id_resort   = $resort_row->id_resort;
            $id_player   = $resort_row->id_player;
            $pref_lang   = !empty($resort_row->preferred_lang) ? $resort_row->preferred_lang : 'english';

            $this->lang->load('logs',      $pref_lang);
            $this->lang->load('reporting', $pref_lang);

            // Season multiplier: season 2+ = 1.5×, season 1 = 1×
            $current_season  = (int)get_current_season($id_resort);
            $season_mult     = ($current_season >= 2) ? 1.5 : 1.0;

            // ------------------------------------------------------------------
            // 1. AVALANCHE RISK  (steep slopes: Red=3, Black=4)
            //    Triggered when: danger day OR heavy snowfall (>= +15 cm/day)
            //    AND resort snow level is high enough for a slide (>= 80 cm)
            // ------------------------------------------------------------------
            $resort_snow = $this->get_resort_snow_level($id_resort);
            $heavy_snow  = ($snow_change >= 15) || in_array($weather_name, ['Blizzard', 'Storm']);

            if ($heavy_snow && $resort_snow >= 80) {
                // Base probability: Red 20 %, Black 35 % — scaled by season
                $steep_slopes = $this->get_open_steep_slopes($id_resort);
                foreach ($steep_slopes->result() as $slope_row) {
                    $difficulty = (int)$slope_row->id_difficulty;
                    $base_prob  = ($difficulty >= 4) ? 35 : 20;
                    $prob       = min((int)round($base_prob * $season_mult), 100);

                    if (mt_rand(1, 100) <= $prob) {
                        // Condition drop: Red −20, Black −30 (clamped to 0)
                        $drop = ($difficulty >= 4) ? 30 : 20;
                        $this->degrade_slope_condition_by_id($slope_row->id_created_slopes, $drop);

                        $msg_log  = $this->lang->line('logs')['avalanche_slope']
                                  . $slope_row->custom_name
                                  . $this->lang->line('logs')['avalanche_condition_drop'];
                        $this->logs_model->call_notification_DB([
                            'id_player' => $id_player,
                            'type'      => $this->lang->line('logs')['natural_hazards'],
                            'data'      => $msg_log,
                        ]);
                        log_user_action([
                            'id_player' => $id_player,
                            'type'      => $this->lang->line('logs')['natural_hazards'],
                            'data'      => $msg_log,
                        ]);

                        $msg_report = $this->lang->line('reporting')['avalanche_risk_steep']
                                    . ' "' . $slope_row->custom_name . '" '
                                    . $this->lang->line('reporting')['avalanche_condition_loss'];
                        $this->add_reporting_data_db($id_resort, 'injuries', $msg_report);

                        $this->logToFile($this->Log_filename, "INFO",
                            "[id_resort_{$id_resort}]", "apply_natural_hazards",
                            "Avalanche on slope {$slope_row->id_created_slopes} (difficulty {$difficulty}), "
                            . "condition -{$drop}.\n");
                    }
                }
            }

            // ------------------------------------------------------------------
            // 2. STORM DAMAGE TO LIFTS
            //    Triggered when: danger day (storm / blizzard)
            //    Each open lift has a chance of losing condition
            // ------------------------------------------------------------------
            if ($is_danger) {
                $open_lifts = $this->get_open_lifts_for_resort($id_resort);
                $insurance  = $this->insurance_model->get_settings_DB($id_resort);
                foreach ($open_lifts->result() as $lift_row) {
                    // Base probability 40 % per lift per storm, scaled by season
                    $prob = min((int)round(40 * $season_mult), 100);
                    if (mt_rand(1, 100) <= $prob) {
                        $drop = mt_rand(10, 20);    // −10 to −20 condition points
                        $this->degrade_lift_condition_by_id($lift_row->id_created_lifts, $drop);

                        $msg_log = $this->lang->line('logs')['storm_lift_damage']
                                 . $lift_row->custom_name
                                 . $this->lang->line('logs')['storm_lift_condition_drop'];
                        $this->logs_model->call_notification_DB([
                            'id_player' => $id_player,
                            'type'      => $this->lang->line('logs')['natural_hazards'],
                            'data'      => $msg_log,
                        ]);
                        log_user_action([
                            'id_player' => $id_player,
                            'type'      => $this->lang->line('logs')['natural_hazards'],
                            'data'      => $msg_log,
                        ]);

                        $msg_report = $this->lang->line('reporting')['storm_damaged_lift']
                                    . ' "' . $lift_row->custom_name . '" '
                                    . $this->lang->line('reporting')['storm_condition_loss'];
                        $this->add_reporting_data_db($id_resort, 'slope', $msg_report);

                        $this->logToFile($this->Log_filename, "INFO",
                            "[id_resort_{$id_resort}]", "apply_natural_hazards",
                            "Storm damaged lift {$lift_row->id_created_lifts}, condition -{$drop}.\n");

                        // Insurance payout for storm damage (premium plan only)
                        if ($insurance->plan === 'premium') {
                            $storm_payout = INSURANCE_STORM_PAYOUT_PER_LIFT;
                            $this->add_revenue_DB($id_resort, $storm_payout);
                            add_revenue_stat_table($id_resort, $storm_payout, 'revenue', $this->yesterdays_date);
                            $this->insurance_model->record_claim_DB($id_resort, $storm_payout);
                            $claim_msg = ($this->lang->line('logs')['insurance_storm_claim'] ?? 'Insurance claim paid (storm damage):') . ' ' . number_format($storm_payout, 0, '.', ' ') . ' € (' . $lift_row->custom_name . ')';
                            $this->logs_model->call_notification_DB([
                                'id_player' => $id_player,
                                'type'      => $this->lang->line('logs')['insurance'] ?? 'Insurance',
                                'data'      => $claim_msg,
                            ]);
                            log_user_action([
                                'id_player' => $id_player,
                                'type'      => $this->lang->line('logs')['insurance'] ?? 'Insurance',
                                'data'      => $claim_msg,
                            ]);
                            $this->logToFile($this->Log_filename, "INFO",
                                "[id_resort_{$id_resort}]", "apply_natural_hazards",
                                "Insurance storm payout {$storm_payout} € for lift {$lift_row->id_created_lifts}.\n");
                        }
                    }
                }
            }

            // ------------------------------------------------------------------
            // 3. ICE ACCUMULATION ON SLOPES
            //    Triggered when: freezing temperature (<= −3 °C) AND
            //    snow is melting or rain is refreezing (snow_change <= 0)
            // ------------------------------------------------------------------
            if ($temperature <= -3.0 && $snow_change <= 0) {
                // Base probability 30 %, scaled by season
                $prob = min((int)round(30 * $season_mult), 100);
                if (mt_rand(1, 100) <= $prob) {
                    $ice_drop = mt_rand(5, 10);   // −5 to −10 condition points on all slopes
                    $this->degrade_all_slopes_for_resort($id_resort, $ice_drop);

                    $msg_log = $this->lang->line('logs')['ice_accumulation'];
                    $this->logs_model->call_notification_DB([
                        'id_player' => $id_player,
                        'type'      => $this->lang->line('logs')['natural_hazards'],
                        'data'      => $msg_log,
                    ]);
                    log_user_action([
                        'id_player' => $id_player,
                        'type'      => $this->lang->line('logs')['natural_hazards'],
                        'data'      => $msg_log,
                    ]);

                    $msg_report = $this->lang->line('reporting')['ice_slopes_affected'];
                    $this->add_reporting_data_db($id_resort, 'injuries', $msg_report);

                    $this->logToFile($this->Log_filename, "INFO",
                        "[id_resort_{$id_resort}]", "apply_natural_hazards",
                        "Ice accumulation on all slopes, condition -{$ice_drop}.\n");
                }
            }
        }
    }

    /**
     * get_open_steep_slopes    Returns open Red (id_difficulty=3) and Black (id_difficulty=4) slopes for a resort.
     *
     * @param  int  $id_resort
     * @return CI_DB_result
     */
    protected function get_open_steep_slopes($id_resort) {
        return $this->db
            ->select('game_created_slopes.id_created_slopes, game_created_slopes.custom_name, game_slopes_tbl.id_difficulty')
            ->from('game_created_slopes')
            ->join('game_slopes as game_slopes_tbl', 'game_slopes_tbl.id_slope = game_created_slopes.id_slope', 'inner')
            ->where('game_created_slopes.id_resort', $id_resort)
            ->where('game_created_slopes.id_status', '1')
            ->where_in('game_slopes_tbl.id_difficulty', [3, 4])
            ->get();
    }

    /**
     * get_open_lifts_for_resort    Returns all open lifts for a specific resort.
     *
     * @param  int  $id_resort
     * @return CI_DB_result
     */
    protected function get_open_lifts_for_resort($id_resort) {
        return $this->db
            ->select('id_created_lifts, custom_name, lift_condition')
            ->from('game_created_lifts')
            ->where('id_resort', $id_resort)
            ->where('id_status', '1')
            ->get();
    }

    /**
     * degrade_slope_condition_by_id    Reduces the condition of a specific slope by a given amount.
     *
     * @param  int  $id_created_slopes
     * @param  int  $drop               Condition points to subtract
     */
    protected function degrade_slope_condition_by_id($id_created_slopes, $drop) {
        $this->db->trans_start();
        $this->db->set('slope_condition', 'GREATEST(slope_condition - ' . (int)$drop . ', 0)', FALSE);
        $this->db->where('id_created_slopes', $id_created_slopes);
        $this->db->update('game_created_slopes');
        $this->db->trans_complete();
    }

    /**
     * degrade_lift_condition_by_id     Reduces the condition of a specific lift by a given amount.
     *
     * @param  int  $id_created_lifts
     * @param  int  $drop               Condition points to subtract
     */
    protected function degrade_lift_condition_by_id($id_created_lifts, $drop) {
        $this->db->trans_start();
        $this->db->set('lift_condition', 'GREATEST(lift_condition - ' . (int)$drop . ', 0)', FALSE);
        $this->db->where('id_created_lifts', $id_created_lifts);
        $this->db->update('game_created_lifts');
        $this->db->trans_complete();
    }

    /**
     * degrade_all_slopes_for_resort    Reduces the condition of all open slopes for a resort.
     *
     * @param  int  $id_resort
     * @param  int  $drop               Condition points to subtract
     */
    protected function degrade_all_slopes_for_resort($id_resort, $drop) {
        $this->db->trans_start();
        $this->db->set('slope_condition', 'GREATEST(slope_condition - ' . (int)$drop . ', 0)', FALSE);
        $this->db->where('id_resort', $id_resort);
        $this->db->where('id_status', '1');
        $this->db->update('game_created_slopes');
        $this->db->trans_complete();
    }

    /**
     * get_resort_snow_level    Returns the current snow level (cm) for a resort.
     *
     * @param  int  $id_resort
     * @return int
     */
    protected function get_resort_snow_level($id_resort) {
        $row = $this->db
            ->select('snow_level')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get()
            ->row();
        return $row ? max(0, (int)$row->snow_level) : 0;
    }

    // =========================================================================
    // CELEBRITY / VIP VISITS
    // =========================================================================

    /**
     * process_celebrity_visits     Each night, each open resort has a random chance
     *                              of receiving a celebrity visit (influencer, pro skier
     *                              or film crew).
     *
     *   - If avg slope condition >= CELEBRITY_GOOD_SLOPE_THRESHOLD → big rep spike.
     *   - Otherwise                                                → modest base bonus.
     *   - If any lift is in maintenance (id_status = 5)           → huge rep penalty.
     *     (The lift penalty stacks with and can override the slope bonus.)
     *
     * @param array $visitor_calculations  Array produced by visitor_calculations()
     */
    protected function process_celebrity_visits($visitor_calculations) {
        $this->celebrity_visit_model->ensure_table_exists();

        $visit_types = ['influencer', 'pro_skier', 'film_crew'];

        foreach ($visitor_calculations as $resort_data) {
            $id_resort     = $resort_data['id_resort'];
            $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);

            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);

            // Roll for celebrity visit
            if (mt_rand(1, 100) > CELEBRITY_VISIT_CHANCE) {
                continue;
            }

            // Pick a random visitor type
            $visit_type = $visit_types[array_rand($visit_types)];

            // Check average slope condition across all open regular slopes
            $avg_slope_row = $this->db
                ->select('AVG(game_created_slopes.slope_condition) AS avg_cond')
                ->from('game_created_slopes')
                ->join('game_slopes AS gs', 'gs.id_slope = game_created_slopes.id_slope', 'inner')
                ->where('game_created_slopes.id_resort', (int)$id_resort)
                ->where('game_created_slopes.id_status', '1')
                ->get()
                ->row();
            $avg_slope_condition = $avg_slope_row ? (float)$avg_slope_row->avg_cond : 0;
            $slopes_good         = ($avg_slope_condition >= CELEBRITY_GOOD_SLOPE_THRESHOLD) ? 1 : 0;

            // Check whether any lift is in maintenance mode (id_status = 5)
            $failed_lift_count = $this->db
                ->where('id_resort', (int)$id_resort)
                ->where('id_status', 5)
                ->count_all_results('game_created_lifts');
            $lift_failed = ($failed_lift_count > 0) ? 1 : 0;

            // Calculate net reputation change
            $rep_change = $slopes_good ? CELEBRITY_REP_GOOD_SLOPES : CELEBRITY_REP_BASE;
            if ($lift_failed) {
                $rep_change -= CELEBRITY_REP_LIFT_FAIL;
            }

            // Apply reputation change
            if ($rep_change != 0) {
                $this->db->trans_start();
                if ($rep_change > 0) {
                    $this->db->set('reputation', 'reputation+' . $rep_change, FALSE);
                } else {
                    $this->db->set('reputation', 'reputation-' . abs($rep_change), FALSE);
                }
                $this->db->where('id_resort', (int)$id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();
            }

            // Log visit in history table
            $this->celebrity_visit_model->log_visit_DB(
                $id_resort,
                $visit_type,
                $slopes_good,
                $lift_failed,
                $rep_change,
                $this->todays_date
            );

            // Build notification message
            $type_label = $this->lang->line('logs')['celebrity_visit_' . $visit_type];
            if ($lift_failed) {
                $msg = sprintf($this->lang->line('logs')['celebrity_visit_lift_fail_msg'], $type_label, $rep_change);
            } elseif ($slopes_good) {
                $msg = sprintf($this->lang->line('logs')['celebrity_visit_good_slopes_msg'], $type_label, $rep_change);
            } else {
                $msg = sprintf($this->lang->line('logs')['celebrity_visit_avg_slopes_msg'], $type_label, $rep_change);
            }

            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['celebrity_visit'],
                'data'      => $msg,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['celebrity_visit'],
                'data'      => $msg,
            ]);
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_celebrity_visits",
                "Resort {$id_resort}: {$visit_type} visit. slopes_good={$slopes_good}, lift_failed={$lift_failed}, rep_change={$rep_change}.\n");
        }
    }
    // ACCESSIBILITY & TRANSPORTATION
    // =========================================================================

    /**
     * process_transportation   Runs nightly transport effects for every resort:
     *   1. Deduct daily operating costs (shuttle / ski storage / gondola link).
     *   2. Award reputation bonuses to families and professional skiers.
     *   3. Apply a visitor-count bonus from shuttle infrastructure.
     *
     * @param CI_DB_result $list_all_resorts  All resort rows
     */
    protected function process_transportation($list_all_resorts) {
        foreach ($list_all_resorts->result() as $resort) {
            $id_resort     = (int)$resort->id_resort;
            $currentUserID = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_preferred_lang);

            $settings = $this->transportation_model->get_settings_DB($id_resort);
            $shuttle_level = (int)$settings->shuttle_level;
            $ski_storage   = (int)$settings->ski_storage;
            $gondola_link  = (int)$settings->gondola_link;

            // ----------------------------------------------------------------
            // Daily operating costs
            // ----------------------------------------------------------------
            $daily_costs = TRANSPORT_SHUTTLE_DAILY_COST[$shuttle_level]
                         + ($ski_storage  == 1 ? TRANSPORT_SKI_STORAGE_DAILY_COST : 0)
                         + ($gondola_link == 1 ? TRANSPORT_GONDOLA_DAILY_COST      : 0);

            if ($daily_costs > 0) {
                $this->db->trans_start();
                $this->db->set('cash', 'cash - ' . (int)$daily_costs, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();
            }

            // ----------------------------------------------------------------
            // Nightly reputation bonus (families + pros combined)
            // ----------------------------------------------------------------
            $rep_families = TRANSPORT_SHUTTLE_FAMILY_REP[$shuttle_level]
                          + ($ski_storage  == 1 ? TRANSPORT_SKI_STORAGE_FAMILY_REP : 0)
                          + ($gondola_link == 1 ? TRANSPORT_GONDOLA_FAMILY_REP      : 0);

            $rep_pros     = TRANSPORT_SHUTTLE_PRO_REP[$shuttle_level]
                          + ($gondola_link == 1 ? TRANSPORT_GONDOLA_PRO_REP : 0);

            $rep_total = $rep_families + $rep_pros;

            if ($rep_total > 0) {
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation + ' . (int)$rep_total, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();

                $log_data = $this->lang->line('logs')['your_resort_earned'] . $rep_total . $this->lang->line('logs')['reputation_points'] . ' ' . $this->lang->line('logs')['thanks_to'] . ' ' . $this->lang->line('logs')['transport_rep_bonus'];
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $log_data]);
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $log_data]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_transportation", "Resort {$id_resort} earned {$rep_total} reputation from transport (families: {$rep_families}, pros: {$rep_pros}).\n");
            }
        }
    }

    /**
     * process_accommodation_upgrades   Charges nightly maintenance costs and awards
     *                                  reputation for every enabled accommodation.
     *
     * For each resort with an enabled accommodation tier the function:
     *   1. Deducts the nightly maintenance cost from the resort's cash.
     *   2. Adds the nightly reputation bonus.
     */
    protected function process_accommodation_upgrades() {
        $rows = $this->accommodation_model->get_all_resorts_settings_DB();
        if (!$rows || $rows->num_rows() == 0) {
            return;
        }

        foreach ($rows->result() as $row) {
            $id_resort = (int)$row->id_resort;
            $type      = $row->accommodation_type;

            if (!isset(ACCOMMODATION_TYPES[$type])) {
                continue;
            }

            $nightly_cost = (int)ACCOMMODATION_TYPES[$type]['nightly_cost'];
            $rep_bonus    = (int)ACCOMMODATION_TYPES[$type]['reputation_bonus'];

            // Deduct maintenance cost
            $this->db->trans_start();
            $this->db->set('cash', 'cash - ' . $nightly_cost, FALSE);
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_resorts');
            $this->db->trans_complete();

            // Award reputation bonus
            $this->db->trans_start();
            $this->db->set('reputation', 'reputation + ' . $rep_bonus, FALSE);
            $this->db->where('id_resort', $id_resort);
            $this->db->update('game_resorts');
            $this->db->trans_complete();

            $this->logToFile(
                $this->Log_filename, "INFO",
                "[id_resort_{$id_resort}]",
                "process_accommodation_upgrades",
                "Resort {$id_resort} accommodation ({$type}): -{$nightly_cost}€ maintenance, +{$rep_bonus} reputation.\n"
            );
        }
    }
    // =========================================================================
    // EMERGENCY & RESCUE SYSTEM
    // =========================================================================

    /**
     * process_emergency_system     Runs all nightly emergency & rescue calculations:
     *   1. Deduct daily operating costs (rescue team + medical stations + insurance).
     *   2. Apply nightly reputation bonus/penalty based on response time.
     *   3. Roll for incident occurrence and apply fines + reputation loss.
     *
     * @param object $list_all_resorts  Result set from list_all_resorts()
     */
    protected function process_emergency_system($list_all_resorts) {
        foreach ($list_all_resorts->result() as $resort_row) {
            $id_resort = $resort_row->id_resort;

            $id_player = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs', $player_preferred_lang);

            // Fetch settings (returns defaults when no row exists)
            $settings          = $this->emergency_model->get_settings_DB($id_resort);
            $rescue_level      = (int)$settings->rescue_team_level;
            $medical_level     = (int)$settings->medical_stations;
            $insurance_enabled = (int)$settings->insurance_enabled;

            // ----------------------------------------------------------------
            // 1. Daily operating costs
            // ----------------------------------------------------------------
            $daily_cost = EMERGENCY_RESCUE_COST[$rescue_level]
                        + EMERGENCY_MEDICAL_COST[$medical_level]
                        + ($insurance_enabled ? EMERGENCY_INSURANCE_DAILY_COST : 0);

            if ($daily_cost > 0) {
                $this->take_cost_DB($id_resort, $daily_cost);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_emergency_system", "Operating cost {$daily_cost} € charged (rescue={$rescue_level}, medical={$medical_level}, insurance={$insurance_enabled}).\n");
            }

            // ----------------------------------------------------------------
            // 2. Reputation effect based on response time
            // ----------------------------------------------------------------
            $response_time = EMERGENCY_RESPONSE_TIME_BASE
                           - EMERGENCY_RESCUE_RESPONSE_REDUCTION[$rescue_level]
                           - EMERGENCY_MEDICAL_RESPONSE_REDUCTION[$medical_level];

            if ($response_time < EMERGENCY_RESPONSE_FAST_THRESHOLD) {
                $rep_change = EMERGENCY_REP_FAST_RESPONSE_BONUS;
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation+' . $rep_change, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();

                $log_data = $this->lang->line('logs')['emergency_fast_response'];
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['emergency'], 'data' => $log_data]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['emergency'], 'data' => $log_data]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_emergency_system", "Fast response ({$response_time} min): +{$rep_change} reputation.\n");

            } elseif ($response_time > EMERGENCY_RESPONSE_POOR_THRESHOLD) {
                $rep_penalty = abs(EMERGENCY_REP_POOR_RESPONSE_PENALTY);
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation-' . $rep_penalty, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();

                $log_data = $this->lang->line('logs')['emergency_poor_response'];
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['emergency'], 'data' => $log_data]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['emergency'], 'data' => $log_data]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_emergency_system", "Poor response ({$response_time} min): -{$rep_penalty} reputation.\n");
            }

            // ----------------------------------------------------------------
            // 3. Incident simulation
            // ----------------------------------------------------------------
            $roll = mt_rand(1, 100);
            if ($roll <= EMERGENCY_INCIDENT_CHANCE_PCT) {
                $fine      = $insurance_enabled ? EMERGENCY_FINE_WITH_INSURANCE : EMERGENCY_FINE_NO_INSURANCE;
                $rep_loss  = EMERGENCY_INCIDENT_REP_LOSS;

                // Apply fine
                $this->take_cost_DB($id_resort, $fine);

                // Apply reputation loss
                $this->db->trans_start();
                $this->db->set('reputation', 'reputation-' . $rep_loss, FALSE);
                $this->db->where('id_resort', $id_resort);
                $this->db->update('game_resorts');
                $this->db->trans_complete();

                $log_data = $this->lang->line('logs')['emergency_incident']
                    . ' Fine: ' . $fine . ' €. Reputation: -' . $rep_loss . '.';
                $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['emergency'], 'data' => $log_data]);
                log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['emergency'], 'data' => $log_data]);
                $this->logToFile($this->Log_filename, "INFO", "[id_resort_{$id_resort}]", "process_emergency_system", "Incident! Fine={$fine} €, rep_loss={$rep_loss}, insurance={$insurance_enabled}.\n");
            }
        }
    }

    /**
     * check_expiring_contracts     Sends notifications for contracts expiring within 3 days,
     *                              and auto-fires staff whose contracts have already expired.
     *
     * @param object $list_all_resorts  All resorts result object
     */
    protected function check_expiring_contracts($list_all_resorts) {
        // 1. Fire staff with already-expired contracts
        $expired_query = $this->db
            ->select('ghs.id_hired_staff, ghs.id_resort, gs.position, gr.id_player')
            ->from('game_hired_staff ghs')
            ->join('game_staff gs', 'gs.id_staff = ghs.id_staff', 'inner')
            ->join('game_resorts gr', 'gr.id_resort = ghs.id_resort', 'inner')
            ->where('DATE_ADD(ghs.contract_start, INTERVAL ghs.contract_months MONTH) < NOW()', NULL, FALSE)
            ->get();
        foreach ($expired_query->result() as $row) {
            $id_player = $row->id_player;
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs', $player_preferred_lang);
            $msg = 'Contract for '.$row->position.' has expired and they have left.';
            $this->db->where('id_hired_staff', $row->id_hired_staff)->delete('game_hired_staff');
            $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => 'Staff', 'data' => $msg]);
            log_user_action(['id_player' => $id_player, 'type' => 'Staff', 'data' => $msg]);
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$row->id_resort."]", "check_expiring_contracts",
                "Staff id_hired_staff=".$row->id_hired_staff." (".$row->position.") fired: contract expired.\n");
        }

        // 2. Notify for contracts expiring within 3 days
        $expiring_query = $this->db
            ->select('ghs.id_hired_staff, ghs.id_resort, ghs.contract_months, ghs.contract_start, gs.position, gr.id_player,
                      DATE_ADD(ghs.contract_start, INTERVAL ghs.contract_months MONTH) as contract_end')
            ->from('game_hired_staff ghs')
            ->join('game_staff gs', 'gs.id_staff = ghs.id_staff', 'inner')
            ->join('game_resorts gr', 'gr.id_resort = ghs.id_resort', 'inner')
            ->where('DATE_ADD(ghs.contract_start, INTERVAL ghs.contract_months MONTH) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)', NULL, FALSE)
            ->get();
        foreach ($expiring_query->result() as $row) {
            $id_player = $row->id_player;
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs', $player_preferred_lang);
            $days_left = (int)ceil((strtotime($row->contract_end) - time()) / 86400);
            $days_left = max(0, $days_left);
            $msg = 'Contract for '.$row->position.' expires in '.$days_left.' day'.($days_left == 1 ? '' : 's').' — consider renewing.';
            $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => 'Staff', 'data' => $msg]);
            log_user_action(['id_player' => $id_player, 'type' => 'Staff', 'data' => $msg]);
            $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$row->id_resort."]", "check_expiring_contracts",
                "Staff id_hired_staff=".$row->id_hired_staff." (".$row->position.") contract expiring in ".$days_left." day(s).\n");
        }
    }

    /**
     * process_trail_snowmaking
     * Handles per-trail snowmaking: checks temp/water/power, updates slope snow level.
     */
    protected function process_trail_snowmaking($list_all_resorts) {
        $this->load->model('snowmaking_model');
        
        // Get today's temperature
        $todays_weather = $this->weather_model->select_weather_forecast($this->todays_date);
        $temperature = 0; // Default
        if ($todays_weather && $todays_weather->num_rows() > 0) {
            $cond_id = $todays_weather->row()->id_condition;
            $cond = $this->weather_model->select_weather_conditions($cond_id);
            if ($cond && $cond->num_rows() > 0) {
                $temperature = (float)$cond->row()->temperature;
            }
        }

        $day_bit = 1 << ((int)gmdate('N') - 1);

        foreach ($list_all_resorts->result() as $resort) {
            if ($resort->vacation_mode == '1') continue;

            $id_resort = $resort->id_resort;
            
            // Schedule check
            $schedule = $this->resort_model->get_snowmaking_schedule_DB($id_resort);
            if (!($schedule & $day_bit)) continue;

            // Water check
            $water_level = $this->resort_model->get_water_reservoir_DB($id_resort);
            if ($water_level <= 0) continue;

            // Mode check
            $mode = $this->resort_model->get_snowmaking_mode_DB($id_resort);
            $out_mult = 1.0;
            $cost_mult = 1.0;
            if ($mode === 'eco') { $out_mult = SNOWMAKING_MODE_ECO_OUTPUT; $cost_mult = SNOWMAKING_MODE_ECO_COST; }
            elseif ($mode === 'boost') { $out_mult = SNOWMAKING_MODE_BOOST_OUTPUT; $cost_mult = SNOWMAKING_MODE_BOOST_COST; }

            // Staff check
            $staff_count = $this->staff_model->count_hired_snowmakers_DB($id_resort);
            if ($staff_count < SNOWMAKING_MIN_STAFF) continue;

            $equipment = $this->snowmaking_model->get_active_trail_snowmaking_DB($id_resort);
            $total_water_used = 0;
            $total_elec_cost  = 0;
            $slopes_updated   = 0;
            $resort_updated   = false;

            foreach ($equipment->result() as $eq) {
                $def = SNOWMAKING_EQUIPMENT[$eq->equipment_type] ?? null;
                if (!$def) continue;

                // Temp Check
                if ($temperature > $def['min_temp']) continue; // Too warm

                // Calculate Output
                $added_snow = round($def['snow_output'] * $out_mult);
                
                // Update Slope
                $this->db->set('slope_snow_level', 'LEAST(' . MAX_SNOW_LEVEL . ', slope_snow_level + ' . (int)$added_snow . ')', FALSE);
                $this->db->where('id_created_slopes', $eq->id_created_slopes);
                $this->db->update('game_created_slopes');

                // Usage
                $water = $def['water_usage'];
                $elec  = $def['daily_cost'] * $cost_mult;

                $total_water_used += $water;
                $total_elec_cost  += $elec;
                $slopes_updated++;
                
                // Stop if out of water
                if ($total_water_used >= $water_level) break;
            }

            if ($slopes_updated > 0) {
                // Deduct Water
                $new_water = max(0, $water_level - $total_water_used);
                $this->resort_model->update_water_reservoir_DB($id_resort, $new_water);

                // Deduct Money (Electricity)
                $this->finances_model->add_expense_DB($id_resort, $total_elec_cost);
                add_cost_stat_table($id_resort, $total_elec_cost, 'expenses');
                add_cost_stat_table($id_resort, $total_elec_cost, 'cost_upkeep');

                $this->logToFile($this->Log_filename, "INFO", "[id_resort_".$id_resort."]", "process_trail_snowmaking", "Updated ".$slopes_updated." slopes. Water: -".$total_water_used.", Cost: -".$total_elec_cost.".");
            }
        }
    }
}
?>
