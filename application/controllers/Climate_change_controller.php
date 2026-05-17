<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Climate_change_controller
 *
 * Displays the Climate Change overview page and handles adaptation investments.
 */
class Climate_change_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',    $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',  $siteLang);
        $ci->lang->load('logs',    $siteLang);
        $ci->lang->load('climate_change', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('climate_change_model');
    }

    /**
     * index   Main page: shows climate level, current effects, adaptation options.
     */
    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        // Ensure a row exists for this resort
        $climate = $this->climate_change_model->get_climate_data_DB($currentResortID);
        if ($climate === FALSE) {
            $this->climate_change_model->init_climate_DB($currentResortID);
            $climate = $this->climate_change_model->get_climate_data_DB($currentResortID);
        }

        $data['climate']          = $climate;
        $data['effects']          = $this->get_current_effects($climate->climate_level);
        $data['invest_costs']     = [
            'snowmaking' => CLIMATE_INVEST_SNOWMAKING,
            'altitude'   => CLIMATE_INVEST_ALTITUDE,
            'diversify'  => CLIMATE_INVEST_DIVERSIFY,
        ];
        $data['current_season']   = get_current_season($currentResortID);
        $data['main_content']     = 'climate_change';
        $this->load->view('templates/default', $data);
    }

    /**
     * invest   POST handler to purchase an adaptation investment.
     */
    public function invest() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed) {
            echo json_encode(['success' => false, 'message' => 'Not allowed']);
            return;
        }

        $invest_type = $this->input->post('invest_type');
        $allowed     = ['snowmaking_invest', 'altitude_invest', 'diversify_invest'];
        if (!in_array($invest_type, $allowed, TRUE)) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('climate_change')['invalid_invest']]);
            return;
        }

        $climate = $this->climate_change_model->get_climate_data_DB($currentResortID);
        if ($climate === FALSE) {
            $this->climate_change_model->init_climate_DB($currentResortID);
            $climate = $this->climate_change_model->get_climate_data_DB($currentResortID);
        }

        // Already invested?
        if ($climate->$invest_type == 1) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('climate_change')['already_invested']]);
            return;
        }

        // Cost map
        $cost_map = [
            'snowmaking_invest' => CLIMATE_INVEST_SNOWMAKING,
            'altitude_invest'   => CLIMATE_INVEST_ALTITUDE,
            'diversify_invest'  => CLIMATE_INVEST_DIVERSIFY,
        ];
        $cost = $cost_map[$invest_type];

        // Check cash
        $this->load->model('resort_model');
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        if ($resort_info->cash < $cost) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('climate_change')['not_enough_cash']]);
            return;
        }

        // Deduct cash
        $this->load->model('finances_model');
        $this->db->trans_start();
        $this->db->set('cash', 'cash - ' . (int)$cost, FALSE);
        $this->db->where('id_resort', $currentResortID);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('climate_change')['invest_failed']]);
            return;
        }

        // Record investment
        $ok = $this->climate_change_model->set_investment_DB($currentResortID, $invest_type);
        if (!$ok) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('climate_change')['invest_failed']]);
            return;
        }

        // Log
        $invest_label_key = str_replace('_invest', '', $invest_type) . '_invest_label';
        $label = $this->lang->line('climate_change')[$invest_label_key] ?? $invest_type;
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('climate_change')['climate_change'],
            'data'      => $this->lang->line('climate_change')['invested_in'] . ' ' . $label . ' (' . number_format($cost, 0, '.', ' ') . ' €).',
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('climate_change')['climate_change'],
            'data'      => $this->lang->line('climate_change')['invested_in'] . ' ' . $label . ' (' . number_format($cost, 0, '.', ' ') . ' €).',
        ]);

        echo json_encode(['success' => true, 'message' => $this->lang->line('climate_change')['invest_success']]);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * get_current_effects   Returns an array of active effect values for a given climate level.
     *                       Relies on constants defined in application/config/config.php.
     *
     * @param int $level   Climate level 0-10
     * @return array
     */
    public function get_current_effects($level) {
        $level = max(0, min(10, (int)$level));
        return [
            'winter_snow_penalty'    => CLIMATE_SNOW_PENALTY_PER_LEVEL   * $level,
            'snowmaking_cost_mult'   => 1.0 + (CLIMATE_COST_MULT_PER_LEVEL * $level),
            'glacier_loss'           => CLIMATE_GLACIER_LOSS_PER_LEVEL   * $level,
            'season_length_penalty'  => CLIMATE_SEASON_PENALTY_PER_LEVEL * $level,
        ];
    }
}
