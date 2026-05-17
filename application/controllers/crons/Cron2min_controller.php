<?php
// Force all errors to be displayed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline){
    echo "ERROR [$errno] $errstr in $errfile on line $errline\n";
});

register_shutdown_function(function() {
    $err = error_get_last();
    if ($err) {
        echo "FATAL ERROR [{$err['type']}] {$err['message']} in {$err['file']} on line {$err['line']}\n";
    }
});

class Cron2min_Controller extends CI_Controller {

    // Properties
    public $Log_filename;
    public $todays_time;
    public $yesterdays_time;
    public $todays_datetime;
    public $todays_date;

    function __construct() {
        parent::__construct();

        // Secret key for cron authentication
        $secret_key = 'Bordeaux147';
        $provided_key = $_GET['key'] ?? null;

        if ($provided_key !== $secret_key) {
            if (function_exists('show_error')) {
                show_error('Unauthorized', 401);
            } else {
                header('HTTP/1.0 401 Unauthorized');
                echo 'Unauthorized';
            }
            exit;
        }

        // Initialize properties
        $this->Log_filename = gmdate('Y-m-d', time());

        $today = strtotime('now');
        $this->todays_time = $today;
        $this->yesterdays_time = strtotime('-1 day', $today);
        $this->todays_datetime = gmdate('Y-m-d H:i:s', $this->todays_time);
        $this->todays_date = gmdate('Y-m-d', $this->todays_time);
    }

    public function index() {
        $this->logToFile($this->Log_filename, 'INFO', '[START]', 'index', "Cron2min_Controller \n");

        $this->load->model('achievements_model');

        $this->update_construction_item('slope', '4');
        $this->update_construction_item('lift', '4');
        $this->update_construction_item('lift', '3');
        $this->update_construction_item('building', '4');
        $this->update_equipment_purchase();
        $this->check_cron_achievement_based_on_other(124);
        $this->check_cron_achievement_based_on_other(131);

        $this->logToFile($this->Log_filename, 'INFO', '[END]', 'index', "Cron2min_Controller \n");
    }

    // ---------- HELPER METHODS ---------- //

    /**
     * update_construction_item     Completes all constructions/repairs of a given item type
     *                              that have passed their end_construction timestamp.
     *
     * @param string $item_type  'slope', 'lift', or 'building'
     * @param string $status     The id_status to match (e.g. '4' = under construction, '3' = under repair for lifts)
     */
    public function update_construction_item($item_type, $status){
        $table_map = [
            'slope'    => 'game_created_slopes',
            'lift'     => 'game_created_lifts',
            'building' => 'game_created_buildings',
        ];

        if (!isset($table_map[$item_type])) {
            $this->logToFile($this->Log_filename, 'WARN', '[ ]', 'update_construction_item', "Unknown item type: $item_type\n");
            return;
        }

        $table = $table_map[$item_type];
        $now   = gmdate('Y-m-d H:i:s');

        $this->db->where('id_status', $status);
        $this->db->where('end_construction <=', $now);
        $this->db->update($table, ['id_status' => 1]);
        $updated = $this->db->affected_rows();

        $this->logToFile($this->Log_filename, 'INFO', "[$item_type/status$status]", 'update_construction_item',
            "Completed $updated $item_type(s) with status $status.\n");
    }

    /**
     * update_equipment_purchase    Marks all equipment orders as delivered when their
     *                              end_delivery timestamp has passed.
     */
    public function update_equipment_purchase(){
        $now = gmdate('Y-m-d H:i:s');

        $this->db->where('delivered', '0');
        $this->db->where('end_delivery <=', $now);
        $this->db->update('game_purchased_equipments', ['delivered' => 1]);
        $updated = $this->db->affected_rows();

        $this->logToFile($this->Log_filename, 'INFO', '[ ]', 'update_equipment_purchase',
            "Marked $updated equipment order(s) as delivered.\n");
    }

    /**
     * check_cron_achievement_based_on_other    Checks whether any player should receive
     *                                          a given achievement based on other already-
     *                                          claimed achievements, and grants it if so.
     *
     * @param int $master_achievement  The achievement ID to check and potentially grant.
     */
    public function check_cron_achievement_based_on_other($master_achievement){
        $achievement_data = $this->achievements_model->get_specific_achievements_data($master_achievement);
        if (!$achievement_data || $achievement_data->num_rows() === 0) {
            $this->logToFile($this->Log_filename, 'WARN', "[$master_achievement]", 'check_cron_achievement_based_on_other',
                "Achievement $master_achievement not found.\n");
            return;
        }

        $ach_row     = $achievement_data->row();
        $ach_requires = isset($ach_row->requires) ? $ach_row->requires : null;
        if (!$ach_requires) {
            return;
        }

        $requires = json_decode($ach_requires, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($requires)) {
            return;
        }

        // Only handle achievements that are unlocked based on another achievement
        if (!isset($requires['action']) || $requires['action'] !== 'other_achievement' || empty($requires['id_achievement'])) {
            return;
        }

        $required_achievement_id = (int)$requires['id_achievement'];

        // Find all players who have claimed the prerequisite achievement but do NOT yet have the master achievement
        $query = $this->db
            ->select('pre.id_player')
            ->from('user_achievements pre')
            ->where('pre.id_achievement', $required_achievement_id)
            ->where('pre.claimed', 1)
            ->where('pre.progress', 100)
            ->where("pre.id_player NOT IN (SELECT id_player FROM user_achievements WHERE id_achievement = " . (int)$master_achievement . " AND progress = 100)", null, false)
            ->get();

        $count = 0;
        foreach ($query->result() as $row) {
            $id_player = $row->id_player;
            $exists = $this->db->select('id_user_achievements')
                ->from('user_achievements')
                ->where('id_player', $id_player)
                ->where('id_achievement', $master_achievement)
                ->get();

            if ($exists->num_rows() === 0) {
                $this->db->insert('user_achievements', [
                    'id_player'          => $id_player,
                    'id_achievement'     => $master_achievement,
                    'progress'           => 100,
                    'claimed'            => 1,
                    'unlocked_datetime'  => gmdate('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->where('id_player', $id_player)
                    ->where('id_achievement', $master_achievement)
                    ->update('user_achievements', ['progress' => 100, 'claimed' => 1]);
            }
            $count++;
        }

        $this->logToFile($this->Log_filename, 'INFO', "[$master_achievement]", 'check_cron_achievement_based_on_other',
            "Granted/updated achievement $master_achievement for $count player(s).\n");
    }

    /**
     * check_cron_achievements  Placeholder for additional cron-based achievement checks.
     */
    public function check_cron_achievements(){
        // Reserved for future cron-based achievement logic.
    }

    function logToFile($log_filename, $level, $thread, $function, $data){ 
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        if ( ! write_file(FCPATH . '/application/controllers/logs/Cron2min_'.$log_filename.'.log', $data_formatted, "a+")){
            echo "Unable to log ".$function." to file Cron2min_".$log_filename.".log :\n".$data_formatted;
        } else {
            echo "Logged ".$function."\n";
        }
    }

    protected function list_all_resorts(){
        $this->db->select('game_resorts.id_player, game_resorts.id_resort, players_tbl.preferred_lang, players_tbl.vacation_mode, players_tbl.last_connection, players_tbl.username, players_tbl.email');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_resorts.id_player', 'inner');
        $this->db->from('game_resorts');
        return $this->db->get();
    }

}
?>
