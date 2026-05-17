<?php
/**
 * Environment_controller
 *
 * Manages the Environmental System:
 *   - Dashboard showing eco reputation, carbon footprint, noise pollution
 *   - Wildlife protection zone toggle
 *   - Green investments: solar panels, electric groomers,
 *     reforestation program, water recycling system
 */
class Environment_controller extends CI_Controller {

    /** Cost constants (euros) */
    const SOLAR_PANELS_COST     = 50000;
    const ELECTRIC_GROOMER_COST = 30000;
    const REFORESTATION_COST    = 20000;
    const WATER_RECYCLING_COST  = 40000;

    private $siteLang;

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
        $this->load->model('environment_model');
    }

    /**
     * index    Environmental dashboard page
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        // Pick up flashdata message set after a POST redirect
        if (empty($data['infoMessage'])) {
            $flash = $this->session->flashdata('infoMessage');
            if ($flash)
                $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'environment';

        // Load environment record (creates row if missing)
        $env = $this->environment_model->get_environment_DB($currentResortID);
        $data['env'] = $env;

        // Investment costs
        $data['solar_panels_cost']     = self::SOLAR_PANELS_COST;
        $data['electric_groomer_cost'] = self::ELECTRIC_GROOMER_COST;
        $data['reforestation_cost']    = self::REFORESTATION_COST;
        $data['water_recycling_cost']  = self::WATER_RECYCLING_COST;

        // Carbon thresholds for display
        $data['carbon_fine_threshold']    = ENV_CARBON_FINE_THRESHOLD;
        $data['carbon_restrict_threshold'] = ENV_CARBON_RESTRICT_THRESHOLD;
        $data['noise_fine_threshold']     = ENV_NOISE_FINE_THRESHOLD;

        // Current cash (needed for "can afford" check in view)
        $data['cash_player'] = $this->users_model->get_cash_player();

        $this->load->view('templates/default', $data);
    }

    /**
     * toggle_wildlife_zone     Activates or deactivates the wildlife protection zone.
     *
     * @param int $new_value  0 or 1
     */
    public function toggle_wildlife_zone($new_value) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $new_value = (int)(bool)$new_value;

        $ok = $this->environment_model->set_wildlife_zone_DB($currentResortID, $new_value);

        if ($ok) {
            $msg = $new_value ? 'env_wildlife_zone_enabled' : 'env_wildlife_zone_disabled';
            $log_text = $new_value
                ? $this->lang->line('building')['env_wildlife_zone_enabled']
                : $this->lang->line('building')['env_wildlife_zone_disabled'];
            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);
            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);
        } else {
            $msg = 'bad_action';
        }

        $this->session->set_flashdata('infoMessage', $msg);
        redirect('environment_controller');
    }

    /**
     * invest_solar     Purchases solar panels (one-time investment).
     */
    public function invest_solar() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $env  = $this->environment_model->get_environment_DB($currentResortID);
        $cash = $this->users_model->get_cash_player();

        if ($env->solar_panels == 1) {
            $msg = 'env_solar_already_installed';
        } elseif ($cash < self::SOLAR_PANELS_COST) {
            $msg = 'env_not_enough_cash';
        } else {
            if ($this->users_model->pay_item(self::SOLAR_PANELS_COST, $cash)) {
                $cash_after = $this->users_model->get_cash_player();
                $this->session->set_userdata('cash', $cash_after);

                $this->environment_model->set_solar_panels_DB($currentResortID, 1);

                // Update finances stats
                $this->load->model('finances_model');
                add_cost_stat_table($currentResortID, self::SOLAR_PANELS_COST, 'cost_purchases');

                $log_text = $this->lang->line('building')['env_solar_installed'];
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);

                $msg = 'env_solar_installed';
            } else {
                $msg = 'bad_action';
            }
        }

        $this->session->set_flashdata('infoMessage', $msg);
        redirect('environment_controller');
    }

    /**
     * invest_electric_groomer  Purchases one additional electric groomer.
     */
    public function invest_electric_groomer() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $cash = $this->users_model->get_cash_player();

        if ($cash < self::ELECTRIC_GROOMER_COST) {
            $msg = 'env_not_enough_cash';
        } else {
            if ($this->users_model->pay_item(self::ELECTRIC_GROOMER_COST, $cash)) {
                $cash_after = $this->users_model->get_cash_player();
                $this->session->set_userdata('cash', $cash_after);

                $this->environment_model->increment_electric_groomers_DB($currentResortID);

                // Update finances stats
                $this->load->model('finances_model');
                add_cost_stat_table($currentResortID, self::ELECTRIC_GROOMER_COST, 'cost_purchases');

                $log_text = $this->lang->line('building')['env_electric_groomer_purchased'];
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);

                $msg = 'env_electric_groomer_purchased';
            } else {
                $msg = 'bad_action';
            }
        }

        $this->session->set_flashdata('infoMessage', $msg);
        redirect('environment_controller');
    }

    /**
     * invest_water_recycling   Purchases the water recycling system (one-time).
     */
    public function invest_water_recycling() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $env  = $this->environment_model->get_environment_DB($currentResortID);
        $cash = $this->users_model->get_cash_player();

        if ($env->water_recycling == 1) {
            $msg = 'env_water_recycling_already_installed';
        } elseif ($cash < self::WATER_RECYCLING_COST) {
            $msg = 'env_not_enough_cash';
        } else {
            if ($this->users_model->pay_item(self::WATER_RECYCLING_COST, $cash)) {
                $cash_after = $this->users_model->get_cash_player();
                $this->session->set_userdata('cash', $cash_after);

                $this->environment_model->set_water_recycling_DB($currentResortID, 1);

                $this->load->model('finances_model');
                add_cost_stat_table($currentResortID, self::WATER_RECYCLING_COST, 'cost_purchases');

                $log_text = $this->lang->line('building')['env_water_recycling_installed'];
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);

                $msg = 'env_water_recycling_installed';
            } else {
                $msg = 'bad_action';
            }
        }

        $this->session->set_flashdata('infoMessage', $msg);
        redirect('environment_controller');
    }

    /**
     * invest_reforestation     Plants one additional batch of trees (up to ENV_MAX_TREE_COUNT).
     */
    public function invest_reforestation() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $env  = $this->environment_model->get_environment_DB($currentResortID);
        $cash = $this->users_model->get_cash_player();

        $tree_count = isset($env->tree_count) ? (int)$env->tree_count : 0;

        if ($tree_count >= ENV_MAX_TREE_COUNT) {
            $msg = 'env_reforestation_max_reached';
        } elseif ($cash < self::REFORESTATION_COST) {
            $msg = 'env_not_enough_cash';
        } else {
            if ($this->users_model->pay_item(self::REFORESTATION_COST, $cash)) {
                $cash_after = $this->users_model->get_cash_player();
                $this->session->set_userdata('cash', $cash_after);

                $this->environment_model->increment_tree_count_DB($currentResortID);

                $this->load->model('finances_model');
                add_cost_stat_table($currentResortID, self::REFORESTATION_COST, 'cost_purchases');

                $log_text = $this->lang->line('building')['env_reforestation_planted'];
                log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);
                $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['environment'], 'data' => $log_text]);

                $msg = 'env_reforestation_planted';
            } else {
                $msg = 'bad_action';
            }
        }

        $this->session->set_flashdata('infoMessage', $msg);
        redirect('environment_controller');
    }
}
