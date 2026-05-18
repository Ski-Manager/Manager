<?php
class CronSpecialEvents_controller extends CI_Controller {

    function __construct() {
        parent::__construct();

        // SIMPLE CRON SECURITY (works with cron-job.org)
        $secret_key = getenv('CRON_SECRET_KEY') ?: '';

        if ($this->input->get('key') !== $secret_key) {
            header('HTTP/1.0 401 Unauthorized');
            echo 'Unauthorized';
            exit;
        }

        $this->Log_filename = gmdate('Y-m-d H-i-s', time());
    }

    public function index() {
        $this->logToFile($this->Log_filename, 'INFO', '[START]', 'index', "CronSpecialEvents_controller \n");
        $this->load->model('special_events_model');
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('resort_model');

        $all_ongoing_events = $this->special_events_model->list_all_ongoing_special_events();
        $nb_ongoing_events  = $all_ongoing_events->num_rows();
        $infoMessage = "There are " . $nb_ongoing_events . " ongoing special events\n";
        $this->logToFile($this->Log_filename, 'INFO', "[ ]", 'list_all_ongoing_special_events', $infoMessage);
        echo $infoMessage;

        if ($nb_ongoing_events > 0) {
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START ADD DAILY SPECIAL EVENT VISITORS/REVENUE **************\n");
            $this->add_daily_special_event_stats($all_ongoing_events);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END ADD DAILY SPECIAL EVENT VISITORS/REVENUE **************\n");

            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** START MARK COMPLETED SPECIAL EVENTS **************\n");
            $all_ongoing_events = $this->special_events_model->list_all_ongoing_special_events();
            $this->mark_special_events_completed($all_ongoing_events);
            $this->logToFile($this->Log_filename, "DEBUG", "[ ]", "index", "******** END MARK COMPLETED SPECIAL EVENTS **************\n");
        }

        $this->logToFile($this->Log_filename, "INFO", "[END]", "index", "CronSpecialEvents_controller \n");
    }

    // ---- EVERYTHING BELOW REMAINS EXACTLY THE SAME ----

    protected function add_daily_special_event_stats($all_ongoing_events) {
        foreach ($all_ongoing_events->result() as $event_row) {
            $id_started_special_event = $event_row->id_started_special_event;
            $id_special_event         = $event_row->id_special_event;
            $id_resort                = $event_row->id_resort;
            $id_player                = $this->users_model->get_user_id_from_resortID($id_resort);
            $player_preferred_lang    = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs', $player_preferred_lang);
            $name_language = 'name_' . $player_preferred_lang;

            $event_data       = $this->special_events_model->get_special_event_data($id_special_event);
            $event_data_array = $event_data->row();

            $expected_visitors = $event_data_array->expected_visitors;
            $expected_revenue  = $event_data_array->expected_revenue;
            $duration          = $event_data_array->duration / ACCELERATOR_FACTOR;
            $event_name        = $event_data_array->$name_language;

            $weightedValues = ['0.7' => 5, '0.8' => 10, '0.85' => 10, '0.9' => 15, '1' => 20, '1.1' => 15, '1.15' => 10, '1.2' => 10, '1.3' => 5];
            $coefficient    = getRandomWeightedElement($weightedValues);

            $daily_visitors = round(($coefficient * $expected_visitors / $duration), 0);
            $daily_revenue  = round(($coefficient * $expected_revenue / $duration), 0);

            $this->special_events_model->add_daily_stats_special_event_DB($id_started_special_event, 'aggregated_visitors', $daily_visitors);
            $this->special_events_model->add_daily_stats_special_event_DB($id_started_special_event, 'aggregated_revenue', $daily_revenue);

            $notification_text = number_format($daily_visitors, 0, ',', ' ') . ' ' . $this->lang->line('logs')['visitors_special_event'] . ' ' . $event_name . ', ' . $this->lang->line('logs')['generating_revenue_of'] . ' ' . number_format($daily_revenue, 0, ',', ' ') . '€.';
            $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['special_events'], 'data' => $notification_text]);
            log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['special_events'], 'data' => $notification_text]);

            $infoMessage = $notification_text . " For player ID " . $id_player . " (coef:" . $coefficient . ").\n";
            $this->logToFile($this->Log_filename, 'INFO', "[id_resort_" . $id_resort . "]", 'add_daily_special_event_stats', $infoMessage);
        }
    }

    protected function mark_special_events_completed($all_ongoing_events) {
        foreach ($all_ongoing_events->result() as $event_row) {
            $id_started_special_event = $event_row->id_started_special_event;
            $id_special_event         = $event_row->id_special_event;
            $total_visitors           = (int)($event_row->aggregated_visitors ?? 0);
            $total_revenue            = (int)($event_row->aggregated_revenue ?? 0);

            $id_resort = $event_row->id_resort;
            $id_player = $this->users_model->get_user_id_from_resortID($id_resort);

            $player_preferred_lang = $this->users_model->get_user_preferred_lang($id_player);
            $this->lang->load('logs', $player_preferred_lang);
            $name_language = 'name_' . $player_preferred_lang;

            $event_data       = $this->special_events_model->get_special_event_data($id_special_event);
            $event_data_array = $event_data->row();
            $earned_reputation_points = $event_data_array->reputation_points;
            $event_name               = $event_data_array->$name_language;

            $resort_name = $this->resort_model->get_resort_name($id_resort);

            date_default_timezone_set('UTC');
            $end_date = gmdate('Y-m-d', strtotime($event_row->end_date));
            $now      = gmdate('Y-m-d');

            if ($end_date <= $now) {
                $marked = $this->special_events_model->mark_special_events_completed_DB($id_started_special_event);

                if ($marked == 1) {
                    $this->special_events_model->update_resort_column($id_player, 'reputation', $earned_reputation_points);
                    $this->special_events_model->update_resort_column($id_player, 'cash', $total_revenue);

                    add_cost_stat_table($id_resort, $total_revenue, 'rev_special_events');
                    add_cost_stat_table($id_resort, $total_revenue, 'revenue');

                    $notification_text = $event_name . ' ' . $this->lang->line('logs')['is_over_generating'] . ' ' . number_format($total_revenue, 0, ',', ' ') . '€. ' . number_format($total_visitors, 0, ',', ' ') . ' ' . $this->lang->line('logs')['visitors_attended'] . '<br>' . $this->lang->line('logs')['revenue_added'] . ' ' . number_format($earned_reputation_points, 0, ',', ' ') . ' ' . $this->lang->line('logs')['reputation_points'];
                    $this->logs_model->call_notification_DB(['id_player' => $id_player, 'type' => $this->lang->line('logs')['special_events'], 'data' => $notification_text]);
                    log_user_action(['id_player' => $id_player, 'type' => $this->lang->line('logs')['special_events'], 'data' => $notification_text]);
                    $infoMessage = $notification_text . " For player ID " . $id_player . ".\n";
                    $this->logToFile($this->Log_filename, 'INFO', "[id_resort_" . $id_resort . "]", 'mark_special_events_completed', $infoMessage);
                }
            }
        }
    }

    function logToFile($log_filename, $level, $thread, $function, $data) {
        $timestamp      = gmdate('Y-m-d H:i:s,000') . " ";
        $data_formatted = $timestamp . " " . $level . " " . $thread . " " . $function . " - " . $data;

        write_file(FCPATH . '/application/controllers/logs/CronSpecialEvents_' . $log_filename . '.log', $data_formatted, "a+");
    }
}
?>
