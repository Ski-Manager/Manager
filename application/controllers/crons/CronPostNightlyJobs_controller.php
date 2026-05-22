<?php
class CronPostNightlyJobs_Controller extends CI_Controller {

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
        $this->Log_filename = gmdate('Y-m-d H-i-s', time())."";
    }
    
    public function index(){
        $this->logToFile($this->Log_filename, 'INFO', '[START]', 'index', "CronPostNightlyJobs_Controller \n");
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('weather_model');
        
        // Listing all resorts with Tourist Info Center opened, for all players
        $list_safely_closed_resorts = $this->list_all_safely_closed_resorts();
        $number_safely_closed = $list_safely_closed_resorts->num_rows();
        $infoMessage = "There are ".$number_safely_closed." safely closed resorts to re-open\n";
        $this->logToFile($this->Log_filename, 'INFO', "[ ]", 'list_all_safely_closed_resorts', $infoMessage);
        
        // Only run the scripts if there is at least one safely closed resort
        if ($number_safely_closed > 0) {
            /* START re-open resort it was closed due to bad weather */
            //echo '<br>***********************************************<br>';
            //echo '******** START OPEN PREMIUM RESORT **************<br>';
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START OPEN PREMIUM RESORT **************\n");
            $safety_open = $this->safety_open_functions($list_safely_closed_resorts);
            //echo $safety_open;
            //echo '******** END OPEN PREMIUM RESORT **************<br>';
            //echo '*********************************************<br><br>';    
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ADD OPEN PREMIUM RESORT **************\n");
        } 
        $this->logToFile($this->Log_filename, "INFO", "[END]", "index", "CronPostNightlyJobs_Controller \n");
    } // End of Index function
    

    protected function list_all_safely_closed_resorts(){
        $this->db->distinct('game_created_buildings.id_created_buildings', 'game_resorts_tbl.id_resort, game_resorts.id_player, players_tbl.preferred_lang');
        $this->db->from('game_resorts');
        $this->db->join('game_created_buildings as created_buildings_tbl', 'game_resorts.id_resort = created_buildings_tbl.id_resort', 'inner');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->where('created_buildings_tbl.id_status', '6');
        $this->db->where('created_buildings_tbl.id_building', '1');
        $query = $this->db->get();
        return $query;
    }

    protected function safety_open_functions($list_safely_closed_resorts){
        foreach ($list_safely_closed_resorts->result() as $list_safely_closed_resorts_Array){
            $id_player = $list_safely_closed_resorts_Array->id_player;
            $id_resort = $list_safely_closed_resorts_Array->id_resort;
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs',$player_preferred_lang);
            $open_resort = $this->open_resort($id_resort);
            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['resort_safely_opened']) );   // Add a log row to the game_player_logs table      
            $log_user_action = log_user_action( array('id_player' => $id_player, 'type' => $this->lang->line('logs')['weather'], 'data' => $this->lang->line('logs')['resort_safely_opened']) );   // Add a log row to the game_player_logs table      
            $info_message = "Resort ID ".$id_resort." for player ID ".$id_player." was re-opened after bad weather conditions.\n";
            $this->logToFile($this->Log_filename, 'INFO', "[id_resort_".$id_resort."]", 'safety_open_functions', $infoMessage);
        }
        //return $info_message;
    }
    
    protected function open_resort($id_resort){
        $this->db->trans_start();
        $this->db->set('id_status', '1');            
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
        if ( ! write_file(FCPATH . '/application/controllers/logs/CronPostNightlyJobs_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file Cron2min_".$log_filename."'.log :<br>".$data_formatted;
        }
        else{
            echo "Logged ".$function."<br>";
        }
    }
}
?>