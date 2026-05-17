<?php

/**
 * Micro_events_controller
 *
 * Handles AJAX requests for the clickable micro-events (quick decisions) feature.
 * The frontend renders the modal from the default template; this controller
 * processes the player's choice and returns a JSON response.
 */
class Micro_events_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('micro_events', $siteLang);
        $ci->lang->load('logs',         $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('micro_events_model');
        $this->load->model('logs_model');
    }

    /**
     * respond   Processes the player's choice for a pending micro-event (AJAX POST).
     *
     * POST params:
     *   id_micro_event  (int)    – ID of the pending event
     *   choice          (string) – 'a' or 'b'
     *
     * Returns JSON: { ok: bool, msg: string, cash_delta: int, rep_delta: int }
     */
    public function respond() {
        header('Content-Type: application/json');

        if (!$this->input->is_ajax_request()) {
            echo json_encode(['ok' => false, 'msg' => 'Invalid request']);
            return;
        }

        $id_micro_event = (int)$this->input->post('id_micro_event');
        $choice         = $this->input->post('choice', TRUE);

        if ($id_micro_event <= 0 || !in_array($choice, ['a', 'b'], TRUE)) {
            echo json_encode(['ok' => false, 'msg' => 'Invalid parameters']);
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        // Fetch the pending event (ownership check included)
        $now = gmdate('Y-m-d H:i:s');
        $event = $this->db
            ->select('*')
            ->from('game_resort_micro_events')
            ->where('id_micro_event', $id_micro_event)
            ->where('id_player',      $currentUserID)
            ->where('status',         'pending')
            ->where('expires_at >',   $now)
            ->limit(1)
            ->get()
            ->row();

        if (!$event) {
            echo json_encode(['ok' => false, 'msg' => 'Event not found or already resolved']);
            return;
        }

        // Determine consequences from the event definition
        $definitions = Micro_events_model::get_event_definitions();
        if (!isset($definitions[$event->event_type][$choice])) {
            echo json_encode(['ok' => false, 'msg' => 'Unknown event type or choice']);
            return;
        }

        $consequences  = $definitions[$event->event_type][$choice];
        $cash_delta    = (int)$consequences['cash'];
        $rep_delta     = (int)$consequences['rep'];

        // Apply the choice
        $ok = $this->micro_events_model->respond_to_event_DB(
            $id_micro_event,
            $currentUserID,
            $currentResortID,
            $choice,
            $cash_delta,
            $rep_delta
        );

        if (!$ok) {
            echo json_encode(['ok' => false, 'msg' => 'Could not save your choice']);
            return;
        }

        // Build a result message key: e.g. "vip_queue_jump_a_result"
        $siteLang = $this->session->userdata('site_lang') ?: 'english';
        $this->lang->load('micro_events', $siteLang);
        $msg_key = $event->event_type . '_' . $choice . '_result';
        $msg     = $this->lang->line('micro_events')[$msg_key] ?? '';

        // Write an activity log entry
        $log_type = $this->lang->line('logs')['micro_events'] ?? 'Micro-Event';
        $log_data = $this->lang->line('micro_events')[$event->event_type . '_title'] ?? $event->event_type;
        if ($cash_delta !== 0) {
            $log_data .= ' ' . ($cash_delta > 0 ? '+' : '') . $cash_delta . ' €';
        }
        if ($rep_delta !== 0) {
            $log_data .= ' ' . ($rep_delta > 0 ? '+' : '') . $rep_delta . ' rep';
        }
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $log_type,
            'data'      => $log_data,
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => $log_type,
            'data'      => $log_data,
        ]);

        echo json_encode([
            'ok'         => true,
            'msg'        => htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'),
            'cash_delta' => $cash_delta,
            'rep_delta'  => $rep_delta,
        ]);
    }
}
