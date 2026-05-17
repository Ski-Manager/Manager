<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Energy_controller
 *
 * Manages the Power & Energy system:
 *   - Displays current daily consumption (lifts, snow cannons) vs production
 *     (solar panels, hydro plant) and the resulting grid cost.
 *   - Allows the player to purchase / sell solar panel units.
 *   - Allows the player to build / demolish the hydro plant.
 */
class Energy_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        $siteLang = $ci->session->userdata('site_lang') ?: 'english';
        $this->session->set_userdata('site_lang', $siteLang);

        $ci->lang->load('home',     $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',   $siteLang);
        $ci->lang->load('building', $siteLang);
        $ci->lang->load('logs',     $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('energy_model');
        $this->energy_model->ensure_table_exists();
    }

    // -------------------------------------------------------------------------
    // Main page
    // -------------------------------------------------------------------------

    /**
     * index    Energy management page.
     */
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data = [];

        // Flash message from a previous POST → redirect
        $flash = $this->session->flashdata('infoMessage');
        if ($flash) {
            $data['infoMessage'] = $flash;
        }

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'energy';

        // --- Current energy settings ---
        $settings = $this->energy_model->get_energy_settings_DB($currentResortID);
        $data['solar_panels'] = (int)$settings->solar_panels;
        $data['hydro_plant']  = (int)$settings->hydro_plant;

        // --- Daily consumption ---
        $open_lifts    = $this->_count_open_lifts($currentResortID);
        $active_cannons = $this->_count_active_cannons($currentResortID);

        $lift_kwh   = $open_lifts    * ENERGY_LIFT_KWH_PER_DAY;
        $cannon_kwh = $active_cannons * ENERGY_CANNON_KWH_PER_DAY;
        $total_consumption_kwh = $lift_kwh + $cannon_kwh;

        $data['open_lifts']            = $open_lifts;
        $data['active_cannons']        = $active_cannons;
        $data['lift_kwh']              = $lift_kwh;
        $data['cannon_kwh']            = $cannon_kwh;
        $data['total_consumption_kwh'] = $total_consumption_kwh;

        // --- Daily production ---
        $solar_kwh = $data['solar_panels'] * ENERGY_SOLAR_KWH_PER_PANEL;
        $hydro_kwh = $data['hydro_plant']  ? ENERGY_HYDRO_KWH_PER_DAY : 0;
        $total_production_kwh = $solar_kwh + $hydro_kwh;

        $data['solar_kwh']             = $solar_kwh;
        $data['hydro_kwh']             = $hydro_kwh;
        $data['total_production_kwh']  = $total_production_kwh;

        // --- Grid cost ---
        $net_kwh       = max(0, $total_consumption_kwh - $total_production_kwh);
        $daily_grid_cost = round($net_kwh * ENERGY_GRID_COST_PER_KWH, 2);

        $data['net_kwh']        = $net_kwh;
        $data['daily_grid_cost'] = $daily_grid_cost;

        // --- Savings vs 100 % grid ---
        $full_grid_cost = round($total_consumption_kwh * ENERGY_GRID_COST_PER_KWH, 2);
        $data['full_grid_cost'] = $full_grid_cost;
        $data['daily_savings']  = round($full_grid_cost - $daily_grid_cost, 2);

        // --- Player cash (for affordability checks) ---
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $data['cash'] = $resort_info ? (int)$resort_info->cash : 0;

        // --- Constants for the view ---
        $data['solar_panel_cost']    = ENERGY_SOLAR_PANEL_COST;
        $data['solar_panel_max']     = ENERGY_SOLAR_PANEL_MAX;
        $data['solar_kwh_per_panel'] = ENERGY_SOLAR_KWH_PER_PANEL;
        $data['hydro_plant_cost']    = ENERGY_HYDRO_PLANT_COST;
        $data['hydro_kwh_per_day']   = ENERGY_HYDRO_KWH_PER_DAY;
        $data['grid_cost_per_kwh']   = ENERGY_GRID_COST_PER_KWH;
        $data['lift_kwh_per_day']    = ENERGY_LIFT_KWH_PER_DAY;
        $data['cannon_kwh_per_day']  = ENERGY_CANNON_KWH_PER_DAY;

        $this->load->view('templates/default', $data);
    }

    // -------------------------------------------------------------------------
    // Solar panels
    // -------------------------------------------------------------------------

    /**
     * buy_solar_panel  Purchases one additional solar panel unit.
     */
    public function buy_solar_panel() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $settings = $this->energy_model->get_energy_settings_DB($currentResortID);
        $current  = (int)$settings->solar_panels;

        if ($current >= ENERGY_SOLAR_PANEL_MAX) {
            $this->session->set_flashdata('infoMessage', 'energy_solar_max_reached');
            redirect('energy_controller');
            return;
        }

        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        if (!$resort_info || (int)$resort_info->cash < ENERGY_SOLAR_PANEL_COST) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('energy_controller');
            return;
        }

        // Deduct cost
        $this->_take_cost($currentResortID, ENERGY_SOLAR_PANEL_COST);

        // Update panel count
        $this->energy_model->set_solar_panels_DB($currentResortID, $current + 1);

        // Log
        $log_msg = $this->lang->line('logs')['energy_solar_panel_bought'];
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

        $this->session->set_flashdata('infoMessage', 'energy_solar_panel_bought');
        redirect('energy_controller');
    }

    /**
     * sell_solar_panel  Sells one solar panel unit (partial refund).
     */
    public function sell_solar_panel() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $settings = $this->energy_model->get_energy_settings_DB($currentResortID);
        $current  = (int)$settings->solar_panels;

        if ($current <= 0) {
            $this->session->set_flashdata('infoMessage', 'energy_solar_none_to_sell');
            redirect('energy_controller');
            return;
        }

        // Refund 50 % of purchase price
        $refund = (int)round(ENERGY_SOLAR_PANEL_COST * 0.5);
        $this->_add_revenue($currentResortID, $refund);
        $this->energy_model->set_solar_panels_DB($currentResortID, $current - 1);

        // Log
        $log_msg = $this->lang->line('logs')['energy_solar_panel_sold'];
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

        $this->session->set_flashdata('infoMessage', 'energy_solar_panel_sold');
        redirect('energy_controller');
    }

    // -------------------------------------------------------------------------
    // Hydro plant
    // -------------------------------------------------------------------------

    /**
     * build_hydro_plant  Builds the hydro plant (one-time purchase).
     */
    public function build_hydro_plant() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $settings = $this->energy_model->get_energy_settings_DB($currentResortID);
        if ((int)$settings->hydro_plant === 1) {
            $this->session->set_flashdata('infoMessage', 'energy_hydro_already_built');
            redirect('energy_controller');
            return;
        }

        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        if (!$resort_info || (int)$resort_info->cash < ENERGY_HYDRO_PLANT_COST) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('energy_controller');
            return;
        }

        $this->_take_cost($currentResortID, ENERGY_HYDRO_PLANT_COST);
        $this->energy_model->set_hydro_plant_DB($currentResortID, 1);

        // Log
        $log_msg = $this->lang->line('logs')['energy_hydro_built'];
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

        $this->session->set_flashdata('infoMessage', 'energy_hydro_built');
        redirect('energy_controller');
    }

    /**
     * demolish_hydro_plant  Demolishes the hydro plant (no refund).
     */
    public function demolish_hydro_plant() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $settings = $this->energy_model->get_energy_settings_DB($currentResortID);
        if ((int)$settings->hydro_plant === 0) {
            $this->session->set_flashdata('infoMessage', 'energy_hydro_not_built');
            redirect('energy_controller');
            return;
        }

        $this->energy_model->set_hydro_plant_DB($currentResortID, 0);

        // Log
        $log_msg = $this->lang->line('logs')['energy_hydro_demolished'];
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

        $this->session->set_flashdata('infoMessage', 'energy_hydro_demolished');
        redirect('energy_controller');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * _count_open_lifts    Returns the number of open lifts for a resort.
     *
     * @param int $id_resort
     * @return int
     */
    private function _count_open_lifts($id_resort) {
        return (int)$this->db
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', '1')
            ->count_all_results('game_created_lifts');
    }

    /**
     * _count_active_cannons    Returns the number of active snow cannons (open buildings)
     *                          for a resort.
     *
     * @param int $id_resort
     * @return int
     */
    private function _count_active_cannons($id_resort) {
        return (int)$this->db
            ->where('id_resort', (int)$id_resort)
            ->where('type', 'cannon')
            ->where('id_status', '1')
            ->count_all_results('game_created_buildings');
    }

    /**
     * _take_cost   Deducts an amount from the player's cash.
     *
     * @param int $id_resort
     * @param int $amount
     */
    private function _take_cost($id_resort, $amount) {
        $this->db->trans_start();
        $this->db->set('cash', 'cash - ' . (int)$amount, FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
    }

    /**
     * _add_revenue     Adds an amount to the player's cash.
     *
     * @param int $id_resort
     * @param int $amount
     */
    private function _add_revenue($id_resort, $amount) {
        $this->db->trans_start();
        $this->db->set('cash', 'cash + ' . (int)$amount, FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
    }
}
