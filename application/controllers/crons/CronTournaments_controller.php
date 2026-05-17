<?php
class CronTournaments_controller extends CI_Controller {

    function __construct(){
        parent::__construct();

        // Authentication: accept either HTTP Basic Auth or ?key=Bordeaux147 (for cron-job.org)
        $secret_key = 'Bordeaux147';
        $has_valid_secret_key = ($this->input->get('key') === $secret_key);

        if (!$has_valid_secret_key) {
            // Fall back to HTTP Basic Auth
            $auth_header = null;
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
            } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if ($auth_header !== null) {
                $decoded = base64_decode(substr($auth_header, 6));
                if ($decoded !== false && strpos($decoded, ':') !== false) {
                    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $decoded, 2);
                }
            }

            $has_valid_basic_auth = (
                defined('CRON_USERNAME') && defined('CRON_PASSWORD')
                && isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
                && $_SERVER['PHP_AUTH_USER'] === CRON_USERNAME
                && $_SERVER['PHP_AUTH_PW'] === CRON_PASSWORD
            );

            if (!$has_valid_basic_auth) {
                header('WWW-Authenticate: Basic realm="Cron Area"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'You need to login to access this area';
                exit;
            }
        }

        $this->Log_filename = gmdate('Y-m-d H-i-s', time())."";
    }
    
    public function index(){
        $this->logToFile($this->Log_filename, 'INFO', '[START]', 'index', "CronTournaments_controller \n");
        $this->load->model('tournaments_model');
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('resort_model');
        
        // Listing all ongoing tournaments
        $all_ongoing_tournaments = $this->tournaments_model->list_all_ongoing_tournaments();
        $nb_ongoing_tournaments = $all_ongoing_tournaments->num_rows();
        $infoMessage = "There are ".$nb_ongoing_tournaments." ongoing tournaments\n";
        $this->logToFile($this->Log_filename, 'INFO', "[ ]", 'list_all_ongoing_tournaments', $infoMessage);
        echo $infoMessage;
        
        // Only run these scripts if there is at least one ongoing tournament
        if ($nb_ongoing_tournaments > 0) {
            
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ADD DAILY TOURNAMENT VISITORS/REVENUE **************\n");
            $add_daily_tournament_visitors = $this->add_daily_tournament_stats($all_ongoing_tournaments);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ADD DAILY TOURNAMENT VISITORS/REVENUE **************\n");
            
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START MARK COMPLETED TOURNAMENTS **************\n");
            $all_ongoing_tournaments = $this->tournaments_model->list_all_ongoing_tournaments();
            $mark_tournaments_complete = $this->mark_tournaments_completed($all_ongoing_tournaments);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END MARK COMPLETED TOURNAMENTS **************\n");
            
            
        } 
        $this->logToFile($this->Log_filename, "INFO", "[END]", "index", "CronTournaments_controller \n");
    } // End of Index function
    

    protected function add_daily_tournament_stats($all_ongoing_tournaments){
        foreach ($all_ongoing_tournaments->result() as $all_ongoing_tournaments_array){
            $id_started_tournament = $all_ongoing_tournaments_array->id_started_tournament;
            $id_tournament = $all_ongoing_tournaments_array->id_tournament;
            
            date_default_timezone_set('UTC');
            $end_date = gmdate('Y-m-d', strtotime($all_ongoing_tournaments_array->end_date));
            $now = gmdate('Y-m-d', strtotime('now'));
            if ($end_date < $now) {
                // Tournament's end date is in the past; skip adding stats and let
                // mark_tournaments_completed handle the completion logic.
                continue;
            }

            $id_resort = $all_ongoing_tournaments_array->id_resort;
            $id_player = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs',$player_preferred_lang);
            $name_language = 'name_'.$player_preferred_lang;     // outputs name_english or name_french for the DB columns
            
            $tournament_data = $this->tournaments_model->get_tournament_data($id_tournament);
            $tournament_data_array = $tournament_data->row();
            
            $expected_visitors = $tournament_data_array->expected_visitors;
            $expected_revenue = $tournament_data_array->expected_revenue;
            $duration = $tournament_data_array->duration/ACCELERATOR_FACTOR;
            $tournament_name = $tournament_data_array->$name_language;
            
            $resort_name = $this->resort_model->get_resort_name($id_resort);
        
            // replaces {resort} by the resort name of the player
            if (str_contains($tournament_name, '{resort}')) { //first we check if the tournament name contains the string '{resort}'
                $tournament_name = str_replace('{resort}', $resort_name, $tournament_name); //if yes, we simply replace it with the resort name
            }
        
            date_default_timezone_set('UTC');
            $end_date = gmdate('Y-m-d', strtotime($all_ongoing_tournaments_array->end_date));
            $now = gmdate('Y-m-d', strtotime('now'));
            if ($end_date < $now) {
                continue; // Tournament end date has passed, skip adding stats
            }

            $weightedValues = array( '0.7'=>5, '0.8'=>10, '0.85'=>10, '0.9'=>15, '1'=>20, '1.1'=>15, '1.15'=>10, '1.2'=>10, '1.3'=>5 );    // Percentage of chance to get the max value
            $coefficient = getRandomWeightedElement($weightedValues);
                
            $daily_visitors = round( ($coefficient * $expected_visitors / $duration), 0);
            $daily_revenue = round( ($coefficient * $expected_revenue / $duration), 0);
            
            $add_daily_visitors_tournament_DB = $this->tournaments_model->add_daily_stats_tournament_DB($id_started_tournament, 'aggregated_visitors', $daily_visitors);
            $add_daily_revenue_tournament_DB = $this->tournaments_model->add_daily_stats_tournament_DB($id_started_tournament, 'aggregated_revenue', $daily_revenue);
            
            $notification_text = number_format($daily_visitors, 0, ',', ' ').' '.$this->lang->line('logs')['visitors_tournament'].' '.$tournament_name.', '.$this->lang->line('logs')['generating_revenue_of'].' '.number_format($daily_revenue, 0, ',', ' ').'€.';
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['tournaments'], 'data' => $notification_text ) );   // Add a log row to the game_player_logs table      
            $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['tournaments'], 'data' => $notification_text) );   // Add a log row to the game_player_logs table      
            $infoMessage = $notification_text." For player ID ".$id_player." (coef:".$coefficient.").\n";
            $this->logToFile($this->Log_filename, 'INFO', "[id_resort_".$id_resort."]", 'add_daily_tournament_visitors', $infoMessage);
        }
        //return $info_message;
    }
    
    
    protected function mark_tournaments_completed($all_ongoing_tournaments){
        foreach ($all_ongoing_tournaments->result() as $all_ongoing_tournaments_array){
            $id_started_tournament = $all_ongoing_tournaments_array->id_started_tournament;
            $id_tournament = $all_ongoing_tournaments_array->id_tournament;
            $total_visitors = (int)($all_ongoing_tournaments_array->aggregated_visitors ?? 0);
            $total_revenue = (int)($all_ongoing_tournaments_array->aggregated_revenue ?? 0);
            
            $id_resort = $all_ongoing_tournaments_array->id_resort;
            $id_player = $this->users_model->get_user_id_from_resortID($id_resort);
                        
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs',$player_preferred_lang);
            $this->lang->load('home',$player_preferred_lang);
            $name_language = 'name_'.$player_preferred_lang;     // outputs name_english or name_french for the DB columns
            
            $tournament_data = $this->tournaments_model->get_tournament_data($id_tournament);
            $tournament_data_array = $tournament_data->row();
            $earned_prestige_points = $tournament_data_array->tournament_points;
            
            $duration = $tournament_data_array->duration/ACCELERATOR_FACTOR;
            if ($duration == 1)
                $days_text_notif = $this->lang->line('logs')['days_comma'];
            else
                $days_text_notif = $this->lang->line('logs')['day_comma'];
                
            $resort_name = $this->resort_model->get_resort_name($id_resort);
            
            $tournament_name = $tournament_data_array->$name_language;
            // replaces {resort} by the resort name of the player
            if (str_contains($tournament_name, '{resort}')) { //first we check if the tournament name contains the string '{resort}'
                $tournament_name = str_replace('{resort}', $resort_name, $tournament_name); //if yes, we simply replace it with the resort name
            }
            
            date_default_timezone_set('UTC');
            $end_date = gmdate('Y-m-d', strtotime($all_ongoing_tournaments_array->end_date)); 
            $now = gmdate('Y-m-d', strtotime('now')); 
            
            if ($end_date <= $now){
                $mark_tournaments_completed_DB = $this->tournaments_model->mark_tournaments_completed_DB($id_started_tournament);  // Mark the tournament as completed

                if ($mark_tournaments_completed_DB == 1) {
                    $this->tournaments_model->update_resort_column($id_player, 'prestige', $earned_prestige_points);  // Update game_resorts table for $earned_prestige_points
                    $this->tournaments_model->update_resort_column($id_player, 'cash', $total_revenue);  // Update game_resorts table for $total_revenue

                    add_cost_stat_table($id_resort, $total_revenue, 'rev_tournaments');
                    add_cost_stat_table($id_resort, $total_revenue, 'revenue');

                    $notification_text = $tournament_name.' '.$this->lang->line('logs')['is_over_generating'].' '.number_format($total_revenue, 0, ',', ' ').'€. '.$this->lang->line('logs')['over_the'].' '.$duration.' '.$days_text_notif.' '.number_format($total_visitors, 0, ',', ' ').' '.$this->lang->line('logs')['visitors_attended'].'<br>'.$this->lang->line('logs')['revenue_added'].' '.number_format($earned_prestige_points, 0, ',', ' ').' '.$this->lang->line('logs')['prestige_points'];
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['tournaments'], 'data' => $notification_text ) );   // Add a log row to the game_player_logs table      
                    $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['tournaments'], 'data' => $notification_text) );   // Add a log row to the game_player_logs table      
                    $infoMessage = $notification_text." For player ID ".$id_player.".\n";
                    $this->logToFile($this->Log_filename, 'INFO', "[id_resort_".$id_resort."]", 'add_daily_tournament_visitors', $infoMessage);
                }
            }
            
        }
        //return $info_message;
    }
    
    
    
    function logToFile($log_filename, $level, $thread, $function, $data){ 
        
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        if ( ! write_file(FCPATH . '/application/controllers/logs/CronTournaments_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file CronTournaments_".$log_filename."'.log :<br>".$data_formatted;
        }
        else{
            echo "Logged ".$function."<br>";
        }
    }
}
?>