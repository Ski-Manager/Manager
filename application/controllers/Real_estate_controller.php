<?php
/**
 * Real_estate_controller
 *
 * Manages private real estate development:
 *   - Develop ski-in ski-out properties, luxury chalets, and condos
 *   - Properties can be kept for long-term passive rental income
 *   - Properties can be sold for a one-time revenue
 */
class Real_estate_controller extends CI_Controller {

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
        $ci->lang->load('home',         $siteLang);
        $ci->lang->load('login_form',   $siteLang);
        $ci->lang->load('navbar',       $siteLang);
        $ci->lang->load('building',     $siteLang);
        $ci->lang->load('logs',         $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('real_estate_model');
        $this->load->model('logs_model');
    }

    /**
     * index    Main real estate development page
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        // Pick up flashdata message
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
            return;
        }

        $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);
        if ($checkIfResortExists->num_rows() == 0) {
            $this->session->set_flashdata('error', 'no_resort');
            redirect('resort_controller');
            return;
        }

        // Require tourist info center
        $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');
        if ($tourist_info_data->num_rows() == 0) {
            $data['hideContent']   = true;
            $data['infoMessage']   = 'tourist_info_required';
        } else {
            $data['hideContent'] = false;

            // Load all properties for this resort
            $properties_result = $this->real_estate_model->get_properties_DB($currentResortID);
            $data['properties'] = $properties_result->result();

            // Count under construction (only one at a time)
            $data['under_construction_count'] = $this->real_estate_model->count_under_construction_DB($currentResortID);

            // Property type definitions
            $data['property_types'] = REAL_ESTATE_TYPES;
            $data['statuses']       = REAL_ESTATE_STATUSES;
        }

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'real_estate';
        $this->load->view('templates/default', $data);
    }

    /**
     * develop      Start construction of a new property
     *
     * @param string $property_type  Key from REAL_ESTATE_TYPES
     */
    public function develop($property_type) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!array_key_exists($property_type, REAL_ESTATE_TYPES)) {
            $this->session->set_flashdata('infoMessage', 'real_estate_bad_type');
            redirect('real_estate_controller');
            return;
        }

        // Only one property under construction at a time
        if ($this->real_estate_model->count_under_construction_DB($currentResortID) > 0) {
            $this->session->set_flashdata('infoMessage', 'real_estate_construction_ongoing');
            redirect('real_estate_controller');
            return;
        }

        $type_config = REAL_ESTATE_TYPES[$property_type];
        $build_cost  = $type_config['build_cost'];

        // Check funds
        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $build_cost) {
            $this->session->set_flashdata('infoMessage', 'real_estate_not_enough_money');
            redirect('real_estate_controller');
            return;
        }

        // Deduct build cost
        $this->users_model->pay_item($build_cost, $cash_player);

        // Record purchase cost in finances
        add_cost_stat_table($currentResortID, $build_cost, 'cost_purchases');

        // Create property record
        $this->real_estate_model->build_property_DB($currentResortID, $property_type, $type_config['build_time']);

        // Log the action
        $log_msg = $this->lang->line('building')['real_estate_develop_log'] . ' ' . $this->lang->line('building')['real_estate_type_' . $property_type];
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

        $this->session->set_flashdata('infoMessage', 'real_estate_construction_started');
        redirect('real_estate_controller');
    }

    /**
     * sell     Mark a completed property as sold (one-time revenue)
     *
     * @param int $id_real_estate
     */
    public function sell($id_real_estate) {
        $id_real_estate  = (int)$id_real_estate;
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $property_result = $this->real_estate_model->get_property_DB($id_real_estate, $currentResortID);
        if ($property_result->num_rows() == 0) {
            redirect('home_controller');
            return;
        }

        $property = $property_result->row();

        // Can only sell a renting property (status 1)
        if ((int)$property->id_status !== 1) {
            $this->session->set_flashdata('infoMessage', 'real_estate_bad_action');
            redirect('real_estate_controller');
            return;
        }

        $type_config = REAL_ESTATE_TYPES[$property->property_type];
        $sale_price  = $type_config['sale_price'];

        // Add sale revenue
        $cash_player = $this->users_model->get_cash_player();
        $this->users_model->sell_item($sale_price, $cash_player);

        // Record in finances
        add_revenue_stat_table($currentResortID, $sale_price, 'revenue',         gmdate('Y-m-d'));
        add_revenue_stat_table($currentResortID, $sale_price, 'rev_real_estate', gmdate('Y-m-d'));

        // Mark as sold
        $this->real_estate_model->update_status_DB($id_real_estate, $currentResortID, 3);

        // Log
        $log_msg = $this->lang->line('building')['real_estate_sold_log'] . ' ' . $this->lang->line('building')['real_estate_type_' . $property->property_type] . ' (' . number_format($sale_price, 0, ',', ' ') . ' €)';
        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['revenues'],
            'data'      => $log_msg,
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => $this->lang->line('logs')['revenues'],
            'data'      => $log_msg,
        ]);

        $this->session->set_flashdata('infoMessage', 'real_estate_sold');
        redirect('real_estate_controller');
    }

    /**
     * toggle_rent   Switch a completed property between renting and for-sale status
     *
     * @param int $id_real_estate
     * @param int $action   1 = set to renting, 2 = set to for_sale
     */
    public function toggle_rent($id_real_estate, $action) {
        $id_real_estate  = (int)$id_real_estate;
        $action          = (int)$action;
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!in_array($action, [1, 2], true)) {
            $this->session->set_flashdata('infoMessage', 'real_estate_bad_action');
            redirect('real_estate_controller');
            return;
        }

        $property_result = $this->real_estate_model->get_property_DB($id_real_estate, $currentResortID);
        if ($property_result->num_rows() == 0) {
            redirect('home_controller');
            return;
        }

        $property = $property_result->row();

        // Can only toggle between renting (1) and for_sale (2)
        if (!in_array((int)$property->id_status, [1, 2], true)) {
            $this->session->set_flashdata('infoMessage', 'real_estate_bad_action');
            redirect('real_estate_controller');
            return;
        }

        $this->real_estate_model->update_status_DB($id_real_estate, $currentResortID, $action);

        $this->session->set_flashdata('infoMessage', $action === 1 ? 'real_estate_set_renting' : 'real_estate_set_for_sale');
        redirect('real_estate_controller');
    }
}
