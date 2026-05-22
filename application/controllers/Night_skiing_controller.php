<?php
/**
 * Night_skiing_controller
 *
 * Manages the night skiing feature:
 *   - Global resort toggle (enable / disable night skiing)
 *   - Per-trail night skiing toggle and lighting configuration
 *   - Resort-level operating hours and night ticket price
 */
class Night_skiing_controller extends CI_Controller {

    /**
     * __construct
     */
    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home', $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('building', $siteLang);
        $ci->lang->load('logs', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('night_skiing_model');
    }

    /**
     * index    Night skiing management page
     *
     * @param array|null $data  Optional data array (used after an action)
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        // Pick up flashdata message set after a POST redirect
        if (empty($data['infoMessage'])) {
            $flash = $this->session->flashdata('infoMessage');
            if ($flash) {
                $data['infoMessage'] = $flash;
            }
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'night_skiing';

        // Resort info (skipass prices, snow level, etc.)
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();

        // Global night skiing status
        $night_skiing_status         = $this->resort_model->get_night_skiing_status($currentResortID);
        $data['night_skiing_status'] = $night_skiing_status;

        // Global toggle button
        if ($night_skiing_status == 1) {
            $data['night_skiing_button'] = '<a href="' . base_url() . 'night_skiing_controller/toggle/' . $currentResortID . '/0"><button class="btn btn-danger">' . $this->lang->line('building')['disable_night_skiing'] . '</button></a>';
        } else {
            $data['night_skiing_button'] = '<a href="' . base_url() . 'night_skiing_controller/toggle/' . $currentResortID . '/1"><button class="btn btn-success">' . $this->lang->line('building')['enable_night_skiing'] . '</button></a>';
        }

        // Skipass prices
        $data['skipass_daily']  = isset($resort_info->skipass_daily)  ? (int)$resort_info->skipass_daily  : 0;
        $data['skipass_weekly'] = isset($resort_info->skipass_weekly) ? (int)$resort_info->skipass_weekly : 0;

        // Daily visitors and events for live preview panel
        $daily_visitors_count = isset($resort_info->daily_visitors) ? (int)$resort_info->daily_visitors : 100;
        $data['ns_daily_visitors'] = $daily_visitors_count;
        $data['ns_skipass_daily'] = $data['skipass_daily'];
        $data['ns_skipass_weekly'] = $data['skipass_weekly'];
        $ns_trail_count_val = 0;
        $data['night_skiing_revenue_bonus_pct'] = (int)(NIGHT_SKIING_REVENUE_BONUS * 100);
        $data['night_skiing_electricity_cost']  = number_format(NIGHT_SKIING_ELECTRICITY_COST, 0, ',', ' ');
        $data['night_skiing_cost_per_slope']    = number_format(NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE, 0, ',', ' ');

        // Resort-level night settings (hours, night ticket price, entertainment, safety)
        $night_settings = $this->night_skiing_model->get_night_settings_DB($currentResortID);
        $data['night_skiing_start_hour']    = $night_settings ? (int)$night_settings->night_skiing_start_hour   : 18;
        $data['night_skiing_end_hour']      = $night_settings ? (int)$night_settings->night_skiing_end_hour     : 22;
        $data['night_skiing_ticket_price']  = $night_settings ? (int)$night_settings->night_skiing_ticket_price : 0;
        $data['night_skiing_entertainment'] = ($night_settings && isset($night_settings->night_skiing_entertainment))
                                            ? $night_settings->night_skiing_entertainment : 'none';
        $data['night_skiing_safety_level']  = ($night_settings && isset($night_settings->night_skiing_safety_level))
                                            ? max(NIGHT_SKIING_SAFETY_MIN_LEVEL, min(NIGHT_SKIING_SAFETY_MAX_LEVEL, (int)$night_settings->night_skiing_safety_level))
                                            : NIGHT_SKIING_SAFETY_MIN_LEVEL;
        $data['night_skiing_school_enabled']  = ($night_settings && isset($night_settings->night_skiing_school_enabled))
                                            ? (int)$night_settings->night_skiing_school_enabled : 0;
        $data['night_skiing_school_price']    = ($night_settings && isset($night_settings->night_skiing_school_price))
                                            ? (int)$night_settings->night_skiing_school_price : 0;
        $data['night_skiing_weather_suspend'] = ($night_settings && isset($night_settings->night_skiing_weather_suspend))
                                            ? (int)$night_settings->night_skiing_weather_suspend : 0;
        $data['night_skiing_torchlight']      = ($night_settings && isset($night_settings->night_skiing_torchlight))
                                            ? (int)$night_settings->night_skiing_torchlight : 0;
        $data['night_skiing_photo_enabled']   = ($night_settings && isset($night_settings->night_skiing_photo_enabled))
                                            ? (int)$night_settings->night_skiing_photo_enabled : 0;
        $data['night_skiing_photo_price']     = ($night_settings && isset($night_settings->night_skiing_photo_price))
                                            ? (int)$night_settings->night_skiing_photo_price : 0;

        // Per-trail settings (open slopes only)
        $trail_rows = $this->night_skiing_model->get_trail_settings_DB($currentResortID);
        $data['trail_settings'] = $trail_rows ? $trail_rows->result() : [];

        // Number of night-skiing-enabled trails (for cost/bonus preview)
        $ns_trail_count = $this->night_skiing_model->get_night_skiing_trails_enabled_count($currentResortID);
        $data['ns_trail_count'] = $ns_trail_count;

        // Effective values based on enabled night-skiing trails
        $effective_bonus_pct        = NIGHT_SKIING_REVENUE_BONUS + (max(0, $ns_trail_count - 1) * NIGHT_SKIING_SLOPE_REVENUE_FACTOR);
        $estimated_electricity_cost = NIGHT_SKIING_ELECTRICITY_COST + ($ns_trail_count * NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE);
        $data['night_skiing_effective_bonus_pct'] = round($effective_bonus_pct * 100, 1);
        $data['night_skiing_estimated_cost']      = number_format($estimated_electricity_cost, 0, ',', ' ');

        // Light type and pole spacing options for the view
        $data['light_type_options']   = NIGHT_SKIING_VALID_LIGHT_TYPES;
        $data['pole_spacing_options'] = NIGHT_SKIING_VALID_POLE_SPACINGS;
        $data['min_start_hour']       = NIGHT_SKIING_MIN_START_HOUR;
        $data['max_start_hour']       = NIGHT_SKIING_MAX_START_HOUR;
        $data['min_end_hour']         = NIGHT_SKIING_MIN_END_HOUR;
        $data['max_end_hour']         = NIGHT_SKIING_MAX_END_HOUR;

        // Entertainment and safety options for the view
        $data['entertainment_options']    = NIGHT_SKIING_VALID_ENTERTAINMENT;
        $data['entertainment_costs']      = NIGHT_SKIING_ENTERTAINMENT_COST;
        $data['entertainment_revenue']    = NIGHT_SKIING_ENTERTAINMENT_REVENUE;
        $data['safety_min_level']         = NIGHT_SKIING_SAFETY_MIN_LEVEL;
        $data['safety_max_level']         = NIGHT_SKIING_SAFETY_MAX_LEVEL;
        $data['safety_costs']             = NIGHT_SKIING_SAFETY_COST;
        $data['safety_reputation_bonus']  = NIGHT_SKIING_SAFETY_REPUTATION_BONUS;

        // Night ski school constants for the view
        $data['school_max_price']         = NIGHT_SKIING_SCHOOL_MAX_PRICE;
        $data['school_visitor_fraction']  = NIGHT_SKIING_SCHOOL_VISITOR_FRACTION;
        $data['school_reputation_bonus']  = NIGHT_SKIING_SCHOOL_REPUTATION_BONUS;

        // Torchlight descent constants for the view
        $data['torchlight_cost']              = NIGHT_SKIING_TORCHLIGHT_COST;
        $data['torchlight_visitor_bonus_pct'] = (int)(NIGHT_SKIING_TORCHLIGHT_VISITOR_BONUS * 100);
        $data['torchlight_reputation_bonus']  = NIGHT_SKIING_TORCHLIGHT_REPUTATION_BONUS;

        // Night photography package constants for the view
        $data['photo_max_price']          = NIGHT_SKIING_PHOTO_MAX_PRICE;
        $data['photo_visitor_fraction']   = NIGHT_SKIING_PHOTO_VISITOR_FRACTION;
        $data['photo_reputation_bonus']   = NIGHT_SKIING_PHOTO_REPUTATION_BONUS;

        // Tonight's scheduled events summary (for forecasting and highlights)
        $today = date('Y-m-d');
        $tonight_events = $this->night_skiing_model->get_scheduled_events($currentResortID, $today, $today);
        if ($tonight_events === false) $tonight_events = [];

        $tonight_visitor_bonus    = 0.0;
        $tonight_revenue_multiplier = 1.0;
        $tonight_cost             = 0;
        $tonight_labels           = [];

        foreach ($tonight_events as $ev) {
            $tonight_visitor_bonus += isset($ev->visitor_bonus_pct) ? (float) $ev->visitor_bonus_pct : 0.0;
            if (isset($ev->revenue_multiplier) && (float) $ev->revenue_multiplier > 0) {
                $tonight_revenue_multiplier *= (float) $ev->revenue_multiplier;
            }
            $tonight_cost   += isset($ev->cost) ? (int) $ev->cost : 0;
            $tonight_labels[] = (string) $ev->event_type;
        }

        $data['ns_today_date']                 = $today;
        $data['ns_tonight_events']             = $tonight_events;
        $data['ns_tonight_visitor_bonus_pct']  = $tonight_visitor_bonus;
        $data['ns_tonight_revenue_multiplier'] = $tonight_revenue_multiplier;
        $data['ns_tonight_cost']               = $tonight_cost;
        $data['ns_tonight_event_labels']       = $tonight_labels;

        $this->load->view('templates/default', $data);
    }

    /**
     * _json_or_redirect    Returns JSON for AJAX requests, redirects otherwise.
     */
    private function _json_or_redirect($isSuccess, $message, $redirectTo = 'night_skiing_controller', array $extra = []) {
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json; charset=utf-8');
            $payload = array_merge(['success' => (bool) $isSuccess, 'message' => $message], $extra);
            echo json_encode($payload);
            exit;
        }
        $this->session->set_flashdata('infoMessage', $message);
        redirect($redirectTo);
    }

    /**
     * ajax_toggle  Enables or disables night skiing globally — AJAX-only endpoint.
     *              POST: resort_id, action (1=enable, 0=disable)
     */
    public function ajax_toggle() {
        if (!$this->input->is_ajax_request()) {
            redirect('home_controller');
            return;
        }
        $resortID = (int)$this->input->post('resort_id', TRUE);
        $action   = (int)$this->input->post('action',    TRUE);

        $user_is_allowed = $this->users_model->check_current_user_allowed($resortID);
        if (!$user_is_allowed) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'not_allowed']);
            exit;
        }

        if (!in_array($action, [0, 1], true)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'bad_action']);
            exit;
        }

        $result = $this->resort_model->set_night_skiing_DB($resortID, $action);
        if ($result !== false) {
            $currentUserID = $this->users_model->get_user_id();
            $log_msg = ($action === 1)
                ? $this->lang->line('logs')['night_skiing_enabled']
                : $this->lang->line('logs')['night_skiing_disabled'];
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $log_msg,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $log_msg,
            ]);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $result !== false, 'enabled' => (bool)$action]);
        exit;
    }

    /**
     * toggle   Enables or disables night skiing globally for a resort
     *
     * @param int $currentResortID  Resort ID
     * @param int $action           1 = enable, 0 = disable
     */
    public function toggle($currentResortID, $action) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $data = [];
        if ($action == '1' || $action == '0') {
            $result = $this->resort_model->set_night_skiing_DB($currentResortID, (int)$action);
            if ($result !== false) {
                $data['infoMessage'] = ($action == '1') ? 'night_skiing_enabled' : 'night_skiing_disabled';
                $currentUserID = $this->users_model->get_user_id();
                $log_msg = ($action == '1')
                    ? $this->lang->line('logs')['night_skiing_enabled']
                    : $this->lang->line('logs')['night_skiing_disabled'];
                $this->logs_model->call_notification_DB([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['building'],
                    'data'      => $log_msg,
                ]);
                log_user_action([
                    'id_player' => $currentUserID,
                    'type'      => $this->lang->line('logs')['building'],
                    'data'      => $log_msg,
                ]);
            } else {
                $data['infoMessage'] = 'bad_action';
            }
        } else {
            $data['infoMessage'] = 'bad_action';
        }

        $this->session->set_flashdata('infoMessage', $data['infoMessage']);
        redirect('night_skiing_controller');
    }

    /**
     * save_resort_settings     Saves resort-level operating hours and night ticket price
     *                          (POST: night_start_hour, night_end_hour, night_ticket_price,
     *                                 night_entertainment, night_safety_level)
     */
    public function save_resort_settings() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $start_hour    = (int)$this->input->post('night_start_hour',    TRUE);
        $end_hour      = (int)$this->input->post('night_end_hour',      TRUE);
        $ticket_price  = (int)$this->input->post('night_ticket_price',  TRUE);
        $entertainment = $this->input->post('night_entertainment',       TRUE);
        $safety_level  = (int)$this->input->post('night_safety_level',  TRUE);
        $school_enabled  = $this->input->post('night_school_enabled',   TRUE) ? 1 : 0;
        $school_price    = (int)$this->input->post('night_school_price', TRUE);
        $weather_suspend = $this->input->post('night_weather_suspend',   TRUE) ? 1 : 0;
        $torchlight      = $this->input->post('night_torchlight',        TRUE) ? 1 : 0;
        $photo_enabled   = $this->input->post('night_photo_enabled',     TRUE) ? 1 : 0;
        $photo_price     = (int)$this->input->post('night_photo_price',  TRUE);

        // Validate hours, entertainment, safety level, and pricing
        $validationErrors = [];
        $isValid = $this->_validate_night_settings(
            $start_hour,
            $end_hour,
            $ticket_price,
            $entertainment,
            $safety_level,
            $school_price,
            $photo_price,
            $validationErrors
        );

        if (!$isValid) {
            $this->_json_or_redirect(false, 'night_skiing_settings_invalid', 'night_skiing_controller', ['errors' => $validationErrors]);
            return;
        }

        $result = $this->night_skiing_model->save_night_settings_DB(
            $currentResortID,
            $start_hour,
            $end_hour,
            $ticket_price,
            $entertainment,
            $safety_level,
            $school_enabled,
            $school_price,
            $weather_suspend,
            $torchlight,
            $photo_enabled,
            $photo_price
        );
        $infoMessage = $result ? 'night_skiing_settings_saved' : 'bad_action';

        $this->_json_or_redirect($infoMessage === 'night_skiing_settings_saved', $infoMessage);
    }

    /**
     * save_trail_settings      Saves per-trail night skiing settings
     *                          (POST: id_created_slope, trail_night_skiing_enabled,
     *                                 trail_light_type, trail_brightness, trail_pole_spacing)
     */
    public function save_trail_settings() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $id_created_slope    = (int)$this->input->post('id_created_slope',           TRUE);
        $ns_enabled          = (int)$this->input->post('trail_night_skiing_enabled', TRUE);
        $light_type          = $this->input->post('trail_light_type',                TRUE);
        $brightness          = (int)$this->input->post('trail_brightness',           TRUE);
        $pole_spacing        = (int)$this->input->post('trail_pole_spacing',         TRUE);

        // Validate inputs
        if ($id_created_slope <= 0
            || !in_array($light_type, NIGHT_SKIING_VALID_LIGHT_TYPES, TRUE)
            || $brightness < 1 || $brightness > 5
            || !in_array($pole_spacing, NIGHT_SKIING_VALID_POLE_SPACINGS, TRUE)) {
            $this->_json_or_redirect(false, 'night_skiing_settings_invalid');
            return;
        }

        // Verify the slope belongs to this resort
        $slope_belongs = $this->db
            ->where('id_created_slopes', $id_created_slope)
            ->where('id_resort', $currentResortID)
            ->count_all_results('game_created_slopes');
        if ($slope_belongs == 0) {
            $this->_json_or_redirect(false, 'bad_action', 'home_controller');
            return;
        }

        $result = $this->night_skiing_model->save_trail_settings_DB(
            $id_created_slope, $currentResortID,
            $ns_enabled ? 1 : 0,
            $light_type, $brightness, $pole_spacing
        );

        $this->_json_or_redirect((bool)$result, $result ? 'night_skiing_trail_saved' : 'bad_action');
    }

    /**
     * _validate_night_settings     Validates resort-level night skiing settings.
     *
     * @param int    $start_hour     Start hour (must be within allowed range)
     * @param int    $end_hour       End hour   (must be > start_hour and within allowed range)
     * @param int    $ticket_price   Night ticket price (>= 0)
     * @param string $entertainment  Must be one of NIGHT_SKIING_VALID_ENTERTAINMENT
     * @param int    $safety_level   Must be within NIGHT_SKIING_SAFETY_MIN_LEVEL–MAX_LEVEL
     * @param int    $school_price   Lesson price per person (0–NIGHT_SKIING_SCHOOL_MAX_PRICE)
     * @param int    $photo_price    Photography package price per person (0–NIGHT_SKIING_PHOTO_MAX_PRICE)
     * @return bool                  TRUE when all values are valid
     */
    private function _validate_night_settings($start_hour, $end_hour, $ticket_price, $entertainment = 'none', $safety_level = 1, $school_price = 0, $photo_price = 0, &$errors = null) {
        $localErrors = [];

        // Start / end hours within allowed global window
        if ($start_hour < NIGHT_SKIING_MIN_START_HOUR || $start_hour > NIGHT_SKIING_MAX_START_HOUR) {
            $localErrors[] = 'start_hour_out_of_range';
        }
        if ($end_hour < NIGHT_SKIING_MIN_END_HOUR || $end_hour > NIGHT_SKIING_MAX_END_HOUR) {
            $localErrors[] = 'end_hour_out_of_range';
        }

        // Duration: minimum 2 hours, maximum 4 hours, and end after start + 2h
        $duration = $end_hour - $start_hour;
        if ($duration < 2) {
            $localErrors[] = 'duration_too_short';
        }
        if ($duration > 4) {
            $localErrors[] = 'duration_too_long';
        }

        // Ticket price: 0–500 €
        if ($ticket_price < 0 || $ticket_price > 500) {
            $localErrors[] = 'ticket_price_out_of_range';
        }

        // Entertainment option
        if (!in_array($entertainment, NIGHT_SKIING_VALID_ENTERTAINMENT, TRUE)) {
            $localErrors[] = 'entertainment_invalid';
        }

        // Safety level bounds
        if ($safety_level < NIGHT_SKIING_SAFETY_MIN_LEVEL || $safety_level > NIGHT_SKIING_SAFETY_MAX_LEVEL) {
            $localErrors[] = 'safety_level_out_of_range';
        }

        // School / photo pricing: >= 0 and capped (defensive upper bound 5000 €)
        if ($school_price < 0 || $school_price > min(NIGHT_SKIING_SCHOOL_MAX_PRICE, 5000)) {
            $localErrors[] = 'school_price_out_of_range';
        }
        if ($photo_price < 0 || $photo_price > min(NIGHT_SKIING_PHOTO_MAX_PRICE, 5000)) {
            $localErrors[] = 'photo_price_out_of_range';
        }

        if ($errors !== null) {
            $errors = $localErrors;
        }

        return empty($localErrors);
    }

    /**
     * get_events  Return JSON list of events for the current resort.
     *             Filter: upcoming | past | all (default: upcoming).
     */
    public function get_events() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            log_message('error', 'Night_skiing_controller::get_events – missing resort for user ' . (int) $currentUserID);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'no_resort']);
            return;
        }

        $filter = $this->input->get_post('filter', TRUE);
        $filter = $filter ? strtolower($filter) : 'upcoming';
        if (!in_array($filter, ['upcoming', 'past', 'all'], true)) {
            $filter = 'upcoming';
        }

        $today = date('Y-m-d');

        $events = $this->night_skiing_model->get_scheduled_events($currentResortID);
        if ($events === false) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_query_failed']);
            return;
        }

        $out = [];
        foreach ($events as $event) {
            $date = (string) $event->scheduled_date;
            if ($filter === 'upcoming' && $date < $today) {
                continue;
            }
            if ($filter === 'past' && $date >= $today) {
                continue;
            }

            $out[] = [
                'id'                 => (int) $event->id,
                'event_type'         => (string) $event->event_type,
                'scheduled_date'     => $date,
                'status'             => (string) $event->status,
                'visitor_bonus_pct'  => (float) $event->visitor_bonus_pct,
                'revenue_multiplier' => (float) $event->revenue_multiplier,
                'cost'               => (int) $event->cost,
                'reputation_bonus'   => (int) $event->rep_bonus,
            ];
        }

        log_message('info', 'Night_skiing_controller::get_events – user ' . (int) $currentUserID . ' resort ' . (int) $currentResortID . ' filter ' . $filter . ' count ' . count($out));

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'events' => $out]);
    }

    /**
     * schedule_event  Create a new event for the current resort.
     *                 POST: event_type, scheduled_date, [visitor_bonus_pct,
     *                 revenue_multiplier, cost, reputation_bonus].
     */
    public function schedule_event() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'no_resort']);
            return;
        }

        $event_type     = trim((string) $this->input->post('event_type', TRUE));
        $scheduled_date = trim((string) $this->input->post('scheduled_date', TRUE));

        $errors = [];

        if ($event_type === '') {
            $errors[] = 'event_type_required';
        }

        $allowed_types = array_merge(array_keys(NIGHT_SKIING_EVENTS), ['custom']);
        if ($event_type !== '' && !in_array($event_type, $allowed_types, true)) {
            $errors[] = 'event_type_invalid';
        }

        if ($scheduled_date === '') {
            $errors[] = 'scheduled_date_required';
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduled_date)) {
                $errors[] = 'scheduled_date_invalid';
            } else {
                $dt = DateTime::createFromFormat('Y-m-d', $scheduled_date);
                if (!$dt || $dt->format('Y-m-d') !== $scheduled_date) {
                    $errors[] = 'scheduled_date_invalid';
                } elseif ($scheduled_date < date('Y-m-d')) {
                    $errors[] = 'scheduled_date_in_past';
                }
            }
        }

        $visitor_bonus_pct  = null;
        $revenue_multiplier = null;
        $cost               = null;
        $reputation_bonus   = null;

        if (empty($errors)) {
            if ($event_type === 'custom') {
                $visitor_bonus_pct  = (float) $this->input->post('visitor_bonus_pct', TRUE);
                $revenue_multiplier = (float) $this->input->post('revenue_multiplier', TRUE);
                $cost               = (int) $this->input->post('cost', TRUE);
                $reputation_bonus   = (int) $this->input->post('reputation_bonus', TRUE);

                if ($visitor_bonus_pct < 0 || $visitor_bonus_pct > 50) {
                    $errors[] = 'visitor_bonus_out_of_range';
                }
                if ($revenue_multiplier < 1.0 || $revenue_multiplier > 2.0) {
                    $errors[] = 'revenue_multiplier_out_of_range';
                }
                if ($cost < 0 || $cost > 100000) {
                    $errors[] = 'cost_out_of_range';
                }
                // DB column is UNSIGNED TINYINT; allow 0–5 here
                if ($reputation_bonus < 0 || $reputation_bonus > 5) {
                    $errors[] = 'reputation_bonus_out_of_range';
                }
            } else {
                // Preset event; use server-side config
                $config = NIGHT_SKIING_EVENTS[$event_type] ?? null;
                if (!$config) {
                    $errors[] = 'event_type_invalid';
                } else {
                    $visitor_bonus_pct  = (float) ($config['visitor_bonus_pct'] ?? 0);
                    $revenue_multiplier = (float) ($config['revenue_multiplier'] ?? 1.0);
                    $cost               = (int) ($config['cost'] ?? 0);
                    $reputation_bonus   = (int) ($config['rep_bonus'] ?? 0);
                }
            }
        }

        if (!empty($errors)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'validation_failed', 'errors' => $errors]);
            return;
        }

        // Check funds
        $this->load->model('users_model');
        $current_cash = $this->users_model->get_cash_player();
        
        if ($current_cash < $cost) {
             header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'not_enough_cash']);
            return;
        }

        // Deduct cash
        $this->users_model->pay_item($cost, $current_cash);
        
        // Log action
        $id_player = $this->users_model->get_user_id();
        log_user_action([
            'id_player' => $id_player,
            'type' => 'night_skiing_event', 
            'data' => "Scheduled $event_type event ($cost €)"
        ]);

        $event_id = $this->night_skiing_model->create_event(
            $currentResortID,
            $event_type,
            $scheduled_date,
            $visitor_bonus_pct,
            $revenue_multiplier,
            $cost,
            $reputation_bonus
        );

        if ($event_id === false) {
            log_message('error', 'Night_skiing_controller::schedule_event – failed to create event for resort ' . (int) $currentResortID);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_not_created']);
            return;
        }

        $event = $this->night_skiing_model->get_event_by_id($event_id);

        log_message('info', 'Night_skiing_controller::schedule_event – user ' . (int) $currentUserID . ' resort ' . (int) $currentResortID . ' type ' . $event_type . ' date ' . $scheduled_date . ' id ' . $event_id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'event_created',
            'event'   => $event ? [
                'id'                 => (int) $event->id,
                'event_type'         => (string) $event->event_type,
                'scheduled_date'     => (string) $event->scheduled_date,
                'status'             => (string) $event->status,
                'visitor_bonus_pct'  => (float) $event->visitor_bonus_pct,
                'revenue_multiplier' => (float) $event->revenue_multiplier,
                'cost'               => (int) $event->cost,
                'reputation_bonus'   => (int) $event->reputation_bonus,
            ] : null,
        ]);
    }

    /**
     * update_event  Update an existing pending event for the current resort.
     */
    public function update_event() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'no_resort']);
            return;
        }

        $event_id = (int) $this->input->post('event_id', TRUE);
        if ($event_id <= 0) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_id_required']);
            return;
        }

        $event = $this->night_skiing_model->get_event_by_id($event_id);
        if (!$event || (int) $event->id_resort !== (int) $currentResortID) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_not_found']);
            return;
        }

        if ($event->status !== 'scheduled') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_not_editable']);
            return;
        }

        $scheduled_date = trim((string) $this->input->post('scheduled_date', TRUE));
        $visitor_bonus  = $this->input->post('visitor_bonus_pct', TRUE);
        $revenue_mult   = $this->input->post('revenue_multiplier', TRUE);
        $cost           = $this->input->post('cost', TRUE);
        $rep_bonus      = $this->input->post('reputation_bonus', TRUE);

        $errors = [];
        $update = [];

        if ($scheduled_date !== '') {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduled_date)) {
                $errors[] = 'scheduled_date_invalid';
            } else {
                $dt = DateTime::createFromFormat('Y-m-d', $scheduled_date);
                if (!$dt || $dt->format('Y-m-d') !== $scheduled_date) {
                    $errors[] = 'scheduled_date_invalid';
                } elseif ($scheduled_date < date('Y-m-d')) {
                    $errors[] = 'scheduled_date_in_past';
                } else {
                    $update['scheduled_date'] = $scheduled_date;
                }
            }
        }

        if ($visitor_bonus !== null && $visitor_bonus !== '') {
            $vb = (float) $visitor_bonus;
            if ($vb < 0 || $vb > 50) {
                $errors[] = 'visitor_bonus_out_of_range';
            } else {
                $update['visitor_bonus_pct'] = $vb;
            }
        }

        if ($revenue_mult !== null && $revenue_mult !== '') {
            $rm = (float) $revenue_mult;
            if ($rm < 1.0 || $rm > 2.0) {
                $errors[] = 'revenue_multiplier_out_of_range';
            } else {
                $update['revenue_multiplier'] = $rm;
            }
        }

        if ($cost !== null && $cost !== '') {
            $ct = (int) $cost;
            if ($ct < 0 || $ct > 1000) {
                $errors[] = 'cost_out_of_range';
            } else {
                $update['cost'] = $ct;
            }
        }

        if ($rep_bonus !== null && $rep_bonus !== '') {
            $rb = (int) $rep_bonus;
            if ($rb < 0 || $rb > 5) {
                $errors[] = 'reputation_bonus_out_of_range';
            } else {
                $update['reputation_bonus'] = $rb;
            }
        }

        if (!empty($errors)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'validation_failed', 'errors' => $errors]);
            return;
        }

        if (empty($update)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'nothing_to_update']);
            return;
        }

        $ok = $this->night_skiing_model->update_event($event_id, $update);
        if (!$ok) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'update_failed']);
            return;
        }

        log_message('info', 'Night_skiing_controller::update_event – user ' . (int) $currentUserID . ' resort ' . (int) $currentResortID . ' event ' . $event_id);

        $updated = $this->night_skiing_model->get_event_by_id($event_id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'event_updated',
            'event'   => $updated ? [
                'id'                 => (int) $updated->id,
                'event_type'         => (string) $updated->event_type,
                'scheduled_date'     => (string) $updated->scheduled_date,
                'status'             => (string) $updated->status,
                'visitor_bonus_pct'  => (float) $updated->visitor_bonus_pct,
                'revenue_multiplier' => (float) $updated->revenue_multiplier,
                'cost'               => (int) $updated->cost,
                'reputation_bonus'   => (int) $updated->reputation_bonus,
            ] : null,
        ]);
    }

    /**
     * delete_event  Delete (hard-delete) a pending event for the current resort.
     */
    public function delete_event() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'no_resort']);
            return;
        }

        $event_id = (int) $this->input->post('event_id', TRUE);
        if ($event_id <= 0) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_id_required']);
            return;
        }

        $event = $this->night_skiing_model->get_event_by_id($event_id);
        if (!$event || (int) $event->id_resort !== (int) $currentResortID) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_not_found']);
            return;
        }

        if ($event->status !== 'scheduled') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'event_not_deletable']);
            return;
        }

        $ok = $this->night_skiing_model->delete_event($event_id);
        if (!$ok) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'delete_failed']);
            return;
        }

        log_message('info', 'Night_skiing_controller::delete_event – user ' . (int) $currentUserID . ' resort ' . (int) $currentResortID . ' event ' . $event_id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => 'event_deleted']);
    }

    /**
     * get_revenue_trends  Returns a lightweight revenue breakdown for recent
     *                     nights (ticket vs school vs photos vs events).
     *                     This is intended for the management UI and uses
     *                     heuristics when detailed history is unavailable.
     */
    public function get_revenue_trends() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'no_resort']);
            return;
        }

        // Basic resort info (daily visitors and skipass pricing)
        $resort_info   = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $daily_visitors = isset($resort_info->daily_visitors) ? (int) $resort_info->daily_visitors : 100;
        $skipass_daily  = isset($resort_info->skipass_daily)  ? (int) $resort_info->skipass_daily  : 50;

        // Current night settings
        $night_settings     = $this->night_skiing_model->get_night_settings_DB($currentResortID);
        $night_ticket_price = $night_settings && isset($night_settings->night_skiing_ticket_price)
            ? (int) $night_settings->night_skiing_ticket_price
            : 0;
        if ($night_ticket_price <= 0) {
            $night_ticket_price = max(5, (int) round($skipass_daily * 0.6));
        }

        $school_enabled = $night_settings && !empty($night_settings->night_skiing_school_enabled);
        $school_price   = $night_settings && isset($night_settings->night_skiing_school_price)
            ? (int) $night_settings->night_skiing_school_price
            : 0;
        if ($school_price <= 0) {
            $school_price = (int) round($night_ticket_price * 0.7);
        }

        $photo_enabled = $night_settings && !empty($night_settings->night_skiing_photo_enabled);
        $photo_price   = $night_settings && isset($night_settings->night_skiing_photo_price)
            ? (int) $night_settings->night_skiing_photo_price
            : 0;
        if ($photo_price <= 0) {
            $photo_price = (int) round($night_ticket_price * 0.5);
        }

        // Build a small date window (last 7 days including today)
        $endDate   = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $events    = $this->night_skiing_model->get_scheduled_events($currentResortID, $startDate, $endDate);

        $eventsByDate = [];
        if (is_array($events)) {
            foreach ($events as $ev) {
                $date = (string) ($ev->scheduled_date ?? '');
                if ($date === '' || $date < $startDate || $date > $endDate) {
                    continue;
                }
                if (!isset($eventsByDate[$date])) {
                    $eventsByDate[$date] = [
                        'visitor_bonus_pct' => 0.0,
                    ];
                }
                $eventsByDate[$date]['visitor_bonus_pct'] += (float) ($ev->visitor_bonus_pct ?? 0);
            }
        }

        $labels       = [];
        $tickets      = [];
        $school       = [];
        $photos       = [];
        $eventsSeries = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $labels[] = $date;

            $dow         = (int) date('N', strtotime($date));
            $dow_factor  = NIGHT_SKIING_DOW_FACTOR[$dow] ?? 1.0;
            $night_fraction = NIGHT_SKIING_VISITOR_FRACTION * $dow_factor;

            $night_visitors = (int) round($daily_visitors * $night_fraction);
            if ($night_visitors < 0) {
                $night_visitors = 0;
            }

            $ticketRev = (int) round($night_visitors * $night_ticket_price);
            $tickets[] = $ticketRev;

            $schoolRev = 0;
            if ($school_enabled) {
                $schoolRev = (int) round($night_visitors * NIGHT_SKIING_SCHOOL_VISITOR_FRACTION * max(0, $school_price));
            }
            $school[] = $schoolRev;

            $photoRev = 0;
            if ($photo_enabled) {
                $photoRev = (int) round($night_visitors * NIGHT_SKIING_PHOTO_VISITOR_FRACTION * max(0, $photo_price));
            }
            $photos[] = $photoRev;

            $eventBonusPct = isset($eventsByDate[$date]) ? (float) $eventsByDate[$date]['visitor_bonus_pct'] : 0.0;
            $eventRev      = (int) round(($ticketRev + $schoolRev + $photoRev) * ($eventBonusPct / 100.0));
            $eventsSeries[] = $eventRev;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'labels'  => $labels,
            'tickets' => $tickets,
            'school'  => $school,
            'photos'  => $photos,
            'events'  => $eventsSeries,
        ]);
    }

    // --- NIGHT SKIING EVENTS ---

    public function get_upcoming_events() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id_resort = $this->session->userdata('id_resort');
        $this->load->model('night_skiing_model');
        $events = $this->night_skiing_model->get_scheduled_events($id_resort, date('Y-m-d'));
        if ($events === false) $events = [];

        // Format for frontend
        $formatted = [];
        foreach ($events as $e) {
            if ($e->status === 'cancelled') continue;

            // Get localized type label
            $type_key = 'night_skiing_event_' . $e->event_type;
            // Fallback to English/Code if translation missing
            $type_label = $this->lang->line('building')[$type_key] ?? ucfirst(str_replace('_', ' ', $e->event_type));
            
            $formatted[] = [
                'id' => $e->id,
                'date' => $e->scheduled_date,
                'type' => $e->event_type,
                'type_label' => $type_label,
                'cost' => $e->cost,
                'status' => $e->status
            ];
        }

        echo json_encode(['success' => true, 'events' => $formatted]);
    }


    public function cancel_event($id_event) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id_resort = $this->session->userdata('id_resort');
        
        $this->load->model('night_skiing_model');
        
        // Verify ownership and status
        $event = $this->night_skiing_model->get_event_by_id($id_event);
        if (!$event || $event->id_resort != $id_resort) {
            echo json_encode(['success' => false, 'message' => 'Event not found or access denied.']);
            return;
        }

        if ($event->status !== 'scheduled') {
             echo json_encode(['success' => false, 'message' => 'Event cannot be cancelled (already ' . $event->status . ').']);
             return;
        }

        if ($this->night_skiing_model->update_event($id_event, ['status' => 'cancelled'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
    }
}
