<?php
class CronMorning_Controller extends CI_Controller {

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
        $this->logToFile($this->Log_filename, 'INFO', '[START]', 'index', "CronMorning_Controller \n");
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('weather_model');
        $this->load->model('weather_model');
		$this->load->model('admin/Admin_maintenance_model');
        
        // Listing all resorts with Tourist Info Center opened, for all players
        $list_all_opened_resorts = $this->list_all_opened_resorts();
        $number_opened_resorts = $list_all_opened_resorts->num_rows();
        $infoMessage = "There are ".$number_opened_resorts." opened resorts\n";
        $this->logToFile($this->Log_filename, 'INFO', "[ ]", 'list_all_opened_resorts', $infoMessage);
		
		// Cleanup ci_sessions
        $empty_ci_sessions_DB = $this->empty_ci_sessions_DB('ci_sessions');
        
        // Only run the scripts if there is at least one existing resort
        if ($number_opened_resorts > 0) {

            /* START close resort if bad weather if resort is in extended-forecast mode */
            //echo '<br>***********************************************<br>';
            //echo '******** START CLOSE PREMIUM RESORT BAD WEATHER **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CLOSE PREMIUM RESORT BAD WEATHER **************\n");
            $safety_close = $this->safety_close_functions($list_all_opened_resorts);
            //echo $safety_close;
            //echo '******** END CLOSE PREMIUM RESORT BAD WEATHER **************<br>';
            //echo '*********************************************<br><br>';    
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START CLOSE PREMIUM RESORT BAD WEATHER **************\n");
          
        } // End of open resorts
        
        $reward_if_referral_confirmed = $this->reward_if_referral_confirmed();
        
    $this->logToFile($this->Log_filename, 'INFO', '[END]', 'index', "CronMorning_Controller \n");   
    } // End of Index function
    
	
	public function empty_ci_sessions_DB($table_name){
        $empty_table= $this->Admin_maintenance_model->empty_table_DB($table_name);
            if (!$empty_table) {
                $this->logToFile($this->Log_filename, "WARN", "[".$table_name."]", "empty_ci_sessions_DB", "There was a problem truncating table ".$table_name."\n");
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">At least table '.$table_name.' failed to be emptied, check log file at '.$this->Log_filename.' </div>');    
            }
            else {
                $this->logToFile($this->Log_filename, "INFO", "[".$table_name."]", "empty_ci_sessions_DB", "Table ".$table_name." was truncated\n");
            }
    }

   protected function count_nb_rewarded_referrals($id_player){
        $this->db->from('game_referral_confirmed');
        $this->db->where('id_referral_player' , $id_player); 
        $this->db->where('approved_referral is NOT NULL', NULL, FALSE);
        $this->db->where('reward_granted' , 1);
        $query = $this->db->get();
        $rowcount = $query->num_rows();
        return $rowcount;
    }
    
    /**
     * reward_if_referral_confirmed      Gives a reward to the referrer if the new account has been confirmed (and slope had been built)
     *      Account is confirmed in slope controller, function "confirm_approved_referral"
     * 
     * @return type                 Returns the query's results
     */
    protected function reward_if_referral_confirmed(){
        $query = $this->db
            ->select('id_referral_confirmed, id_referral_player, id_referred_player')
            ->from('game_referral_confirmed')
            ->where('approved_referral is NOT NULL', NULL, FALSE)
            ->where('reward_granted', 0)
            ->get();
        //$query_get = $this->db->get();
        
        foreach ($query->result() as $query_data){
            $id_player = $query_data->id_referral_player;
            $id_referred_player = $query_data->id_referred_player;
            $nb_rewarded_referrals = $this->count_nb_rewarded_referrals($id_player);
            if ($nb_rewarded_referrals < 5 ) {
                $grant_query = $this->grant_genepis($id_player);
                $reward = 1;
            }
            else {
                $grant_query = 1;
                $reward = 0;
            }
            if ($grant_query == '1') {
                $set_reward_granted_query = $this->set_reward_granted($query_data->id_referral_confirmed);
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
                $this->lang->load('logs',$player_preferred_lang);
                if ($set_reward_granted_query == 1 && $reward == 1) {
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['referral_confirmed']) );   // Add a log row to the game_player_logs table      
                    $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['referral_confirmed']) );   // Add a log row to the game_player_logs table      
                    $info_message = "Player ID ".$id_player." got rewarded 30 genepis because player ID ".$id_referred_player." is now confirmed.\n";
                    $this->logToFile($this->Log_filename, 'INFO', "[id_player_".$id_player."]", 'reward_if_referral_confirmed', $info_message);
                }
                else if ($reward == 0){
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['max_number_referrals']) );   // Add a log row to the game_player_logs table      
                    $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['genepis'], 'data' => $this->lang->line('logs')['max_number_referrals']) );   // Add a log row to the game_player_logs table      
                    $info_message = "Player ID ".$id_player." has reached the maximum number of genepis rewarded when inviting friends.\n";
                    $this->logToFile($this->Log_filename, 'INFO', "[id_player_".$id_player."]", 'reward_if_referral_confirmed', $info_message);
                }
            }
        }
    }
    
    protected function grant_genepis($id_player){
        $this->db->trans_start();
        $this->db->set('genepis', 'genepis + 30', FALSE);
        $this->db->where('id_player' , $id_player);                      
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    protected function set_reward_granted($id_referral_confirmed){
        $this->db->trans_start();
        $this->db->set('reward_granted', 1);
        $this->db->where('id_referral_confirmed' , $id_referral_confirmed);                      
        $this->db->update('game_referral_confirmed');
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
     * list_all_opened_resorts      List all open resorts of the game
     * 
     * @return type                 Returns the query's results
     */
    protected function list_all_opened_resorts(){
        $this->db->distinct('game_created_buildings.id_created_buildings', 'game_resorts_tbl.id_resort, game_resorts.id_player, players_tbl.preferred_lang');
        $this->db->from('game_resorts');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_resorts.id_resort = created_buildings_tbl.id_resort', 'inner');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '1');
        $this->db->where('created_buildings_tbl.id_building', '1');
        $query = $this->db->get();
        return $query;
    }
    
    
    protected function safety_close_functions($list_all_opened_resorts){
        $current_date = gmdate('Y-m-d');    // todays date
            //$info_message = '';
            $todays_weather = $this->weather_model->select_weather_forecast($current_date);    // Get id condition for today
            $todays_weather_data = $todays_weather->row(); 
            $today_id_condition = $todays_weather_data->id_condition;
            $array_weather = $this->weather_model->select_weather_conditions($today_id_condition); // Get details for todays condition (snow level, name...)
            $result = $array_weather->row();
            $danger = $result->danger;
            if ($danger == 1) { // Only if danger day
                foreach ($list_all_opened_resorts->result() as $list_all_opened_resorts_Array){
                    $id_player = $list_all_opened_resorts_Array->id_player;
                    $id_resort = $list_all_opened_resorts_Array->id_resort;
                    $player_extended_forecast = $this->check_player_extended_forecast($id_player);
                    $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
                    $this->lang->load('logs',$player_preferred_lang);
                    if ($player_extended_forecast->num_rows() == 1) {
                        $close_safety_resort = $this->close_safety_resort($id_resort);
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['resort_safely_closed']) );   // Add a log row to the game_player_logs table      
                        $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['resort_safely_closed']) );   // Add a log row to the game_player_logs table      
                        $info_message = "Resort ID ".$id_resort." for player ID ".$id_player." was put into \"Closed (for safety)\" status.\n";
                        $this->logToFile($this->Log_filename, 'INFO', "[id_resort_".$id_resort."]", 'safety_close_functions', $info_message);
                    }
                    else {                        
                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['resort_not_safely_closed']) );   // Add a log row to the game_player_logs table      
                        $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['resort_not_safely_closed']) );   // Add a log row to the game_player_logs table      
                        $info_message = "Resort ID ".$id_resort." for player ID ".$id_player." was not auto-closed (not Premium). Injuries will increase.\n";
                        $this->logToFile($this->Log_filename, 'INFO', "[id_resort_".$id_resort."]", 'safety_close_functions', $info_message);
                    }
                }
            }
            else {
                $info_message = "Today is not a dangerous day. No resort will be automatically closed.\n";
                $this->logToFile($this->Log_filename, 'INFO', "[danger_".$danger."]", 'safety_close_functions', $info_message);
            }
        //return $info_message;
    }
    
    protected function check_player_extended_forecast($id_player){
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today);
        $this->db->select('*');
        $this->db->from('game_extended_forecast');
        $this->db->where('id_player', $id_player);
        $this->db->where('end_forecast>=', $today_GMT);
        $query = $this->db->get();
        return $query;
    }
    
    protected function close_safety_resort($id_resort){
        $this->db->trans_start();
        $this->db->set('id_status', '6');            
        $this->db->where('id_resort', $id_resort);
        $this->db->where('id_building', '1');              
        $this->db->update('game_created_buildings');
        $this->db->limit('1');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    function logToFile($log_filename, $level, $thread, $function, $data){ 
        
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        if ( ! write_file(FCPATH . '/application/controllers/logs/CronMorning_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file Cron2min_".$log_filename."'.log :<br>".$data_formatted;
        }
        else{
            echo "Logged ".$function."<br>";
        }
    }
}
?>