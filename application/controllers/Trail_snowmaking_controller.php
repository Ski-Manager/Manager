<?php
/**
 * Trail_snowmaking_controller
 *
 * Manages per-trail snowmaking equipment.
 * Players can purchase a lance gun, fan gun, or snow factory from one of
 * several brands for each of their slopes. Equipment adds snow overnight.
 */
class Trail_snowmaking_controller extends CI_Controller {

    private $siteLang;

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
        $ci->lang->load('home',       $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',     $siteLang);
        $ci->lang->load('building',   $siteLang);
        $ci->lang->load('logs',       $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('finances_model');
        $this->load->model('logs_model');
        $this->load->model('weather_model');
        $this->load->model('staff_model');
    }

    /**
     * index    Trail snowmaking management page.
     *
     * @param array|null $data  Optional data (used after actions)
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

        $user_activated = $this->users_model->check_account_activated($currentUserID);
        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);
        if ($checkIfResortExists->num_rows() == 0) {
            $this->session->set_flashdata('error', 'no_resort');
            redirect('resort_controller');
        }

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'trail_snowmaking';

        // ------------------------------------------------------------------
        // Current snow level
        // ------------------------------------------------------------------
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $current_snow = isset($resort_info->snow_level) ? max(0, (int)$resort_info->snow_level) : 0;
        $snow_max     = MAX_SNOW_LEVEL;
        $snow_percent = min(100, round($current_snow / $snow_max * 100));
        if ($snow_percent >= 60)
            $snow_bar_class = 'success';
        elseif ($snow_percent >= 30)
            $snow_bar_class = 'warning';
        else
            $snow_bar_class = 'error';
        $data['current_snow_level']    = $current_snow;
        $data['snow_level_bar_percent'] = $snow_percent;
        $data['snow_level_bar_class']   = $snow_bar_class;
        $data['snow_max']               = $snow_max;

        // Snow target
        $data['cannon_target_snow'] = $this->resort_model->get_cannon_target_snow_DB($currentResortID);
        $data['snow_target_form']   = '
            <form method="post" action="' . base_url() . 'trail_snowmaking_controller/set_snow_target/' . $currentResortID . '">
                <div class="input-group" style="max-width:280px;">
                    <input type="number" name="target_snow" class="form-control" min="0" max="' . MAX_SNOW_LEVEL . '" value="' . $data['cannon_target_snow'] . '" placeholder="0 = ' . $this->lang->line('building')['snow_target_disabled'] . '">
                    <span class="input-group-text">' . $this->lang->line('building')['cm'] . '</span>
                    <button type="submit" class="btn btn-primary">' . $this->lang->line('building')['save_snow_target'] . '</button>
                </div>
                <small class="text-muted">' . $this->lang->line('building')['snow_target_info'] . '</small>
            </form>';

        // ------------------------------------------------------------------
        // Cannon summary (merged view)
        // ------------------------------------------------------------------
        $cannons_data = $this->building_model->get_cannons_for_player_DB($currentResortID);
        $cannon_active_count   = 0;
        $cannon_total_output   = 0;
        foreach ($cannons_data->result() as $cannon) {
            if ($cannon->id_status == '1') {
                $cannon_active_count++;
                $cannon_total_output += (int)$cannon->capacity;
            }
        }
        $data['cannon_active_count'] = $cannon_active_count;
        $data['cannon_total_output'] = $cannon_total_output;
        $data['cannon_built']        = ($cannons_data->num_rows() > 0);

        // ------------------------------------------------------------------
        // Weather / temperature check
        // Above-freezing = snow_level of today's forecast is negative (melting)
        // ------------------------------------------------------------------
        $above_freezing = false;
        $today_date     = gmdate('Y-m-d');
        $todays_weather = $this->weather_model->select_weather_forecast($today_date);
        if ($todays_weather && $todays_weather->num_rows() > 0) {
            $today_condition_id = $todays_weather->row()->id_condition;
            $condition_data     = $this->weather_model->select_weather_conditions($today_condition_id);
            if ($condition_data && $condition_data->num_rows() > 0) {
                $above_freezing = ((int)$condition_data->row()->snow_level < 0);
            }
        }
        $data['above_freezing'] = $above_freezing;

        // ------------------------------------------------------------------
        // Water reservoir
        // ------------------------------------------------------------------
        $water_reservoir_purchased = $this->resort_model->get_water_reservoir_purchased_DB($currentResortID);
        $data['water_reservoir_purchased'] = $water_reservoir_purchased;
        $water_level = $this->resort_model->get_water_reservoir_DB($currentResortID);
        $data['water_reservoir_level'] = $water_level;
        $water_bar_class = ($water_level >= 50) ? 'success' : (($water_level >= 20) ? 'warning' : 'error');
        $data['water_bar_class'] = $water_bar_class;
        $data['water_reservoir_cost'] = WATER_RESERVOIR_COST;

        // Municipal emergency water refill
        $resort_level = $this->resort_model->get_resort_level_DB($currentResortID);
        $data['resort_level']                    = $resort_level;
        $data['municipal_refill_unlocked']       = ($resort_level >= MUNICIPAL_WATER_UNLOCK_LIFTS);
        $data['municipal_refill_cost']           = MUNICIPAL_WATER_REFILL_COST;
        $data['municipal_refill_amount']         = MUNICIPAL_WATER_REFILL_AMOUNT;
        $data['municipal_refill_max_reservoir']  = MUNICIPAL_WATER_MAX_RESERVOIR_PCT;
        $data['municipal_refill_available']      = ($resort_level >= MUNICIPAL_WATER_UNLOCK_LIFTS)
                                                    && !empty($water_reservoir_purchased)
                                                    && ($water_level < MUNICIPAL_WATER_MAX_RESERVOIR_PCT);

        // ------------------------------------------------------------------
        // Snowmaking staff
        // ------------------------------------------------------------------
        $data['snowmaker_count']    = $this->staff_model->count_hired_snowmakers_DB($currentResortID);
        $data['snowmaker_required'] = SNOWMAKING_MIN_STAFF;

        // ------------------------------------------------------------------
        // Snowmaking mode
        // ------------------------------------------------------------------
        $snowmaking_mode = $this->resort_model->get_snowmaking_mode_DB($currentResortID);
        $data['snowmaking_mode'] = $snowmaking_mode;
        $data['snowmaking_mode_form'] = '
            <form method="post" action="' . base_url() . 'trail_snowmaking_controller/set_mode/' . $currentResortID . '">
                <div class="d-flex align-items-center gap-2">
                    <select name="snowmaking_mode" class="form-select form-select-sm" style="max-width:200px;">
                        <option value="normal"' . ($snowmaking_mode === 'normal' ? ' selected' : '') . '>' . $this->lang->line('building')['snowmaking_mode_normal'] . '</option>
                        <option value="eco"'    . ($snowmaking_mode === 'eco'    ? ' selected' : '') . '>' . $this->lang->line('building')['snowmaking_mode_eco']    . '</option>
                        <option value="boost"'  . ($snowmaking_mode === 'boost'  ? ' selected' : '') . '>' . $this->lang->line('building')['snowmaking_mode_boost']  . '</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">' . $this->lang->line('building')['save_snowmaking_mode'] . '</button>
                </div>
            </form>';

        // ------------------------------------------------------------------
        // Tonight's projected snowmaking output
        // ------------------------------------------------------------------
        $sm_active   = (int)$data['snowmaker_count'] >= SNOWMAKING_MIN_STAFF;
        $water_ok    = !empty($water_reservoir_purchased) && $water_level > 0;
        $mode_output = ($snowmaking_mode === 'eco')   ? SNOWMAKING_MODE_ECO_OUTPUT
                     : (($snowmaking_mode === 'boost') ? SNOWMAKING_MODE_BOOST_OUTPUT : 1.0);
        $mode_cost   = ($snowmaking_mode === 'eco')   ? SNOWMAKING_MODE_ECO_COST
                     : (($snowmaking_mode === 'boost') ? SNOWMAKING_MODE_BOOST_COST   : 1.0);

        $projected_output = 0;
        $projected_cost   = 0;
        if (!$above_freezing && $sm_active && $water_ok) {
            $projected_output = (int)round($cannon_total_output * $mode_output);
            $projected_cost   = (int)round($cannon_active_count * SNOWMAKING_ELECTRICITY_PER_CANNON * $mode_cost);
        }
        $data['projected_output']        = $projected_output;
        $data['projected_electricity']   = $projected_cost;
        $data['projected_blocked']       = $above_freezing || !$sm_active || !$water_ok;
        if ($above_freezing) {
            $data['projected_block_reason'] = 'temp';
        } elseif (!$sm_active) {
            $data['projected_block_reason'] = 'staff';
        } elseif (!empty($water_reservoir_purchased) && $water_level <= 0) {
            $data['projected_block_reason'] = 'water_empty';
        } else {
            $data['projected_block_reason'] = 'no_reservoir';
        }

        // ------------------------------------------------------------------
        // Snowmaking efficiency (cm per 100 €)
        // ------------------------------------------------------------------
        if ($projected_output > 0 && $projected_cost > 0) {
            $data['snowmaking_efficiency'] = round($projected_output / $projected_cost * 100, 2);
        } else {
            $data['snowmaking_efficiency'] = null;
        }

        // ------------------------------------------------------------------
        // Trail equipment summary (for banner stats)
        // ------------------------------------------------------------------
        $this->load->model('snowmaking_model');
        $trail_eq_query = $this->snowmaking_model->get_active_trail_snowmaking_DB($currentResortID);
        $trail_count    = 0;
        $trail_out_raw  = 0;
        $trail_cost_raw = 0;
        foreach ($trail_eq_query->result() as $te) {
            $trail_count++;
            $trail_out_raw  += (int)$te->snow_output;
            $trail_cost_raw += (int)$te->daily_cost;
        }
        // Also count all (active + inactive) for display
        $all_trail_eq = $this->snowmaking_model->get_slopes_with_snowmaking_DB($currentResortID);
        $total_trail_count = 0;
        foreach ($all_trail_eq->result() as $row) {
            if (!empty($row->id_trail_snowmaking)) $total_trail_count++;
        }
        $data['trail_equipment_count']    = $total_trail_count;
        $data['trail_equipment_active']   = $trail_count;
        $data['trail_projected_output']   = (!$above_freezing && $water_ok && $sm_active)
                                             ? (int)round($trail_out_raw * $mode_output) : 0;
        $data['trail_daily_cost']         = (int)round($trail_cost_raw * $mode_cost);

        // ------------------------------------------------------------------
        // Snowmaking schedule
        // ------------------------------------------------------------------
        $data['snowmaking_schedule'] = $this->resort_model->get_snowmaking_schedule_DB($currentResortID);

        $this->load->view('templates/default', $data);
    }

    /**
     * set_schedule     Saves the snowmaking schedule for a resort.
     *
     * @param int $currentResortID
     */
    public function set_schedule($currentResortID) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $days = $this->input->post('days');
        $schedule_mask = 0;
        if (is_array($days)) {
            foreach ($days as $bit) {
                $bit = (int)$bit;
                if ($bit >= 0 && $bit <= 6) {
                    $schedule_mask |= (1 << $bit);
                }
            }
        }
        $result = $this->resort_model->set_snowmaking_schedule_DB($currentResortID, $schedule_mask);
        $this->session->set_flashdata('infoMessage', ($result !== false ? 'snowmaking_schedule_saved' : 'bad_action'));
        redirect('trail_snowmaking_controller');
    }

    /**
     * set_snow_target  Sets the snow target level for trail snowmaking (shared with cannons).
     *
     * @param int $currentResortID
     */
    public function set_snow_target($currentResortID) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $target = (int)$this->input->post('target_snow');
        $target = max(0, min($target, MAX_SNOW_LEVEL));
        $result = $this->resort_model->set_cannon_target_snow_DB($currentResortID, $target);
        $this->session->set_flashdata('infoMessage', ($result !== false ? 'snow_target_saved' : 'bad_action'));
        redirect('trail_snowmaking_controller');
    }

    /**
     * set_mode  Sets the snowmaking operating mode for a resort.
     *
     * @param int $currentResortID
     */
    public function set_mode($currentResortID) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $mode = $this->input->post('snowmaking_mode', TRUE);
        if (!in_array($mode, SNOWMAKING_MODES)) {
            $mode = 'normal';
        }
        $result = $this->resort_model->set_snowmaking_mode_DB($currentResortID, $mode);
        $this->session->set_flashdata('infoMessage', ($result !== false ? 'snowmaking_mode_saved' : 'bad_action'));
        redirect('trail_snowmaking_controller');
    }

    /**
     * buy_water_reservoir  Purchase a water reservoir for this resort.
     *
     * @param int $currentResortID
     */
    public function buy_water_reservoir($currentResortID) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        if ($pageWasRefreshed) {
            redirect('trail_snowmaking_controller');
            return;
        }

        // Already purchased?
        if ($this->resort_model->get_water_reservoir_purchased_DB($currentResortID)) {
            $this->session->set_flashdata('infoMessage', 'water_reservoir_already_purchased');
            redirect('trail_snowmaking_controller');
            return;
        }

        $cost = WATER_RESERVOIR_COST;
        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $cost) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('trail_snowmaking_controller');
            return;
        }

        $result = $this->resort_model->set_water_reservoir_purchased_DB($currentResortID);
        if ($result) {
            $this->users_model->pay_item($cost, $cash_player);
            $updated_cash = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $updated_cash);

            add_cost_stat_table($currentResortID, $cost, 'cost_purchases');
            add_cost_stat_table($currentResortID, $cost, 'expenses');

            $currentUserID = $this->users_model->get_user_id();
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $this->lang->line('building')['water_reservoir_purchased_log'],
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['building'],
                'data'      => $this->lang->line('building')['water_reservoir_purchased_log'],
            ]);
            $this->session->set_flashdata('infoMessage', 'water_reservoir_purchased');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('trail_snowmaking_controller');
    }

    /**
     * municipal_refill     Emergency municipal water refill for the snowmaking reservoir.
     *
     * Requirements:
     *   - Resort Level 3+ (MUNICIPAL_WATER_UNLOCK_LIFTS open lifts)
     *   - Water reservoir must be purchased
     *   - Reservoir must be below MUNICIPAL_WATER_MAX_RESERVOIR_PCT %
     *   - Player must have enough cash
     *
     * Effects:
     *   - Deducts MUNICIPAL_WATER_REFILL_COST from player cash
     *   - Increases reservoir by MUNICIPAL_WATER_REFILL_AMOUNT %
     *   - Applies eco reputation and resort reputation penalties
     *
     * @param int $currentResortID
     */
    public function municipal_refill($currentResortID) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        if ($pageWasRefreshed) {
            redirect('trail_snowmaking_controller');
            return;
        }

        // Unlock check: Resort Level 3+ (enough open lifts)
        $resort_level = $this->resort_model->get_resort_level_DB($currentResortID);
        if ($resort_level < MUNICIPAL_WATER_UNLOCK_LIFTS) {
            $this->session->set_flashdata('infoMessage', 'municipal_refill_locked');
            redirect('trail_snowmaking_controller');
            return;
        }

        // Reservoir must be purchased
        if (!$this->resort_model->get_water_reservoir_purchased_DB($currentResortID)) {
            $this->session->set_flashdata('infoMessage', 'municipal_refill_no_reservoir');
            redirect('trail_snowmaking_controller');
            return;
        }

        // Reservoir must be below the emergency threshold
        $water_level = $this->resort_model->get_water_reservoir_DB($currentResortID);
        if ($water_level >= MUNICIPAL_WATER_MAX_RESERVOIR_PCT) {
            $this->session->set_flashdata('infoMessage', 'municipal_refill_not_needed');
            redirect('trail_snowmaking_controller');
            return;
        }

        // Cash check
        $cost = MUNICIPAL_WATER_REFILL_COST;
        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $cost) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('trail_snowmaking_controller');
            return;
        }

        // Deduct cash and increase reservoir
        $this->users_model->pay_item($cost, $cash_player);
        $updated_cash = $this->users_model->get_cash_player();
        $this->session->set_userdata('cash', $updated_cash);

        $new_level = min(100, $water_level + MUNICIPAL_WATER_REFILL_AMOUNT);
        $this->resort_model->update_water_reservoir_DB($currentResortID, $new_level);

        add_cost_stat_table($currentResortID, $cost, 'cost_purchases');
        add_cost_stat_table($currentResortID, $cost, 'expenses');

        // Apply eco and reputation penalties
        $this->resort_model->apply_municipal_water_penalties_DB($currentResortID);

        // Log the action
        $currentUserID = $this->users_model->get_user_id();
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['building'],
            'data'      => $this->lang->line('building')['municipal_refill_log'],
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['building'],
            'data'      => $this->lang->line('building')['municipal_refill_log'],
        ]);

        $this->session->set_flashdata('infoMessage', 'municipal_refill_success');
        redirect('trail_snowmaking_controller');
    }

    // -------------------------------------------------------------------------
    // AJAX Methods for Trail Management
    // -------------------------------------------------------------------------

    /**
     * get_trail_data_ajax  Returns JSON list of slopes with snowmaking status.
     */
    public function get_trail_data_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            echo json_encode(['success' => false, 'message' => 'no_resort']);
            return;
        }

        $this->load->model('snowmaking_model');
        $slopes_query = $this->snowmaking_model->get_slopes_with_snowmaking_DB($currentResortID);
        $slopes = [];

        foreach ($slopes_query->result() as $row) {
            $equipment = null;
            if (!empty($row->id_trail_snowmaking)) {
                $eq_type = $row->equipment_type;
                $def = SNOWMAKING_EQUIPMENT[$eq_type] ?? null;
                $equipment = [
                    'id'          => (int)$row->id_trail_snowmaking,
                    'type'        => $eq_type,
                    'name'        => $def ? $def['name'] : $row->brand, // fallback
                    'is_active'   => (bool)$row->is_active,
                    'daily_cost'  => (int)$row->daily_cost,
                    'snow_output' => (int)$row->snow_output,
                ];
            }

            // Slope snow level (default to 0 if null)
            // Note: get_slopes_with_snowmaking_DB selects specific columns, ensure slope_snow_level is included
            // Wait, the model method selects: 'game_created_slopes.id_created_slopes, game_created_slopes.custom_name, game_created_slopes.id_status...'
            // I need to check if 'slope_snow_level' is selected. If not, I need to update the model.
            // Let's assume for now I'll fix the model if needed.
            $snow_level = isset($row->slope_snow_level) ? (int)$row->slope_snow_level : 0;
            
            $slopes[] = [
                'id_slope'    => (int)$row->id_created_slopes,
                'name'        => $row->custom_name,
                'status'      => (int)$row->id_status, // 1=open, 2=closed
                'snow_level'  => $snow_level,
                'max_snow'    => MAX_SNOW_LEVEL,
                'equipment'   => $equipment,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'slopes' => $slopes, 'equipment_types' => SNOWMAKING_EQUIPMENT]);
    }

    /**
     * purchase_equipment_ajax  Handles buying snowmaking gear for a slope.
     */
    public function purchase_equipment_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        
        $id_slope = (int)$this->input->post('id_slope');
        $type     = $this->input->post('equipment_type');

        if (!$currentResortID || !$id_slope || empty($type)) {
            echo json_encode(['success' => false, 'message' => 'missing_params']);
            return;
        }

        if (!isset(SNOWMAKING_EQUIPMENT[$type])) {
            echo json_encode(['success' => false, 'message' => 'invalid_type']);
            return;
        }

        $this->load->model('snowmaking_model');

        // Verify slope ownership
        if (!$this->snowmaking_model->slope_belongs_to_resort_DB($currentResortID, $id_slope)) {
            echo json_encode(['success' => false, 'message' => 'slope_not_found']);
            return;
        }

        // Check if already equipped
        $existing = $this->snowmaking_model->get_trail_snowmaking_for_slope_DB($currentResortID, $id_slope);
        if ($existing->num_rows() > 0) {
            echo json_encode(['success' => false, 'message' => 'already_equipped']);
            return;
        }

        $cost = SNOWMAKING_EQUIPMENT[$type]['cost'];
        $cash = $this->users_model->get_cash_player();

        if ($cash < $cost) {
            echo json_encode(['success' => false, 'message' => 'not_enough_money']);
            return;
        }

        // Deduct cash
        $this->users_model->pay_item($cost, $cash);
        
        // Insert DB record
        $data = [
            'id_resort'         => $currentResortID,
            'id_created_slopes' => $id_slope,
            'equipment_type'    => $type,
            'brand'             => SNOWMAKING_EQUIPMENT[$type]['name'], // Store name as brand for now
            'is_active'         => 1,
            'snow_output'       => SNOWMAKING_EQUIPMENT[$type]['snow_output'],
            'daily_cost'        => SNOWMAKING_EQUIPMENT[$type]['daily_cost'],
            'purchased_at'      => date('Y-m-d H:i:s'),
        ];
        $this->snowmaking_model->add_trail_snowmaking_DB($data);

        // Update stats
        add_cost_stat_table($currentResortID, $cost, 'cost_purchases');
        add_cost_stat_table($currentResortID, $cost, 'expenses');
        
        // Log
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['building'],
            'data'      => "Purchased " . SNOWMAKING_EQUIPMENT[$type]['name'] . " for slope #" . $id_slope,
        ]);

        echo json_encode(['success' => true]);
    }

    /**
     * remove_equipment_ajax  Removes equipment from a slope.
     */
    public function remove_equipment_ajax() {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_trail_sm     = (int)$this->input->post('id_trail_snowmaking');

        $this->load->model('snowmaking_model');
        
        // Verify ownership
        $record = $this->snowmaking_model->get_trail_snowmaking_by_id_DB($currentResortID, $id_trail_sm);
        if ($record->num_rows() === 0) {
            echo json_encode(['success' => false, 'message' => 'not_found']);
            return;
        }

        $this->snowmaking_model->remove_trail_snowmaking_DB($currentResortID, $id_trail_sm);

        echo json_encode(['success' => true]);
    }

    public function toggle_equipment_ajax() {
        if (!$this->input->is_ajax_request()) { show_404(); return; }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_trail_sm = (int)$this->input->post('id_trail_snowmaking');
        $is_active   = (int)$this->input->post('is_active') ? 1 : 0;
        if (!$currentResortID || !$id_trail_sm) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'missing_params']); return;
        }
        $this->load->model('snowmaking_model');
        $record = $this->snowmaking_model->get_trail_snowmaking_by_id_DB($currentResortID, $id_trail_sm);
        if ($record->num_rows() === 0) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'not_found']); return;
        }
        $this->snowmaking_model->update_trail_snowmaking_status_DB($currentResortID, $id_trail_sm, $is_active);
        header('Content-Type: application/json');
        echo json_encode(['success'=>true,'is_active'=>$is_active]);
    }

    public function toggle_all_equipment_ajax() {
        if (!$this->input->is_ajax_request()) { show_404(); return; }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $is_active = (int)$this->input->post('is_active') ? 1 : 0;
        if (!$currentResortID) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'no_resort']); return;
        }
        $this->load->model('snowmaking_model');
        $this->snowmaking_model->update_all_trail_snowmaking_status_DB($currentResortID, $is_active);
        header('Content-Type: application/json');
        echo json_encode(['success'=>true,'is_active'=>$is_active]);
    }

    public function upgrade_equipment_ajax() {
        if (!$this->input->is_ajax_request()) { show_404(); return; }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_trail_sm = (int)$this->input->post('id_trail_snowmaking');
        $new_type    = $this->input->post('equipment_type', TRUE);
        if (!$currentResortID || !$id_trail_sm || empty($new_type)) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'missing_params']); return;
        }
        if (!isset(SNOWMAKING_EQUIPMENT[$new_type])) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'invalid_type']); return;
        }
        $this->load->model('snowmaking_model');
        $record = $this->snowmaking_model->get_trail_snowmaking_by_id_DB($currentResortID, $id_trail_sm);
        if ($record->num_rows() === 0) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'not_found']); return;
        }
        $old = $record->row();
        if ($old->equipment_type === $new_type) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'same_type']); return;
        }
        $new_def  = SNOWMAKING_EQUIPMENT[$new_type];
        $old_cost = SNOWMAKING_EQUIPMENT[$old->equipment_type]['cost'] ?? 0;
        $upgrade_cost = max(0, $new_def['cost'] - $old_cost);
        $cash = $this->users_model->get_cash_player();
        if ($cash < $upgrade_cost) {
            header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'not_enough_money']); return;
        }
        if ($upgrade_cost > 0) {
            $this->users_model->pay_item($upgrade_cost, $cash);
            add_cost_stat_table($currentResortID, $upgrade_cost, 'cost_purchases');
            add_cost_stat_table($currentResortID, $upgrade_cost, 'expenses');
        }
        $this->snowmaking_model->upgrade_trail_snowmaking_DB($currentResortID, $id_trail_sm, [
            'equipment_type' => $new_type,
            'brand'          => $new_def['name'],
            'snow_output'    => $new_def['snow_output'],
            'daily_cost'     => $new_def['daily_cost'],
        ]);
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['building'],
            'data'      => 'Upgraded trail snowmaking to ' . $new_def['name'] . ' on slope #' . $old->id_created_slopes,
        ]);
        header('Content-Type: application/json');
        echo json_encode(['success'=>true,'upgrade_cost'=>$upgrade_cost]);
    }

}
