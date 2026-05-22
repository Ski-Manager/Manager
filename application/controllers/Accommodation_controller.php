<?php
/**
 * Accommodation_controller
 *
 * Manages Accommodation Upgrades:
 *   - Choose an accommodation tier (cabin / lodge / luxury_hotel)
 *   - Enable or disable the active tier
 *   - One-time upgrade cost charged when activating a new tier
 */
class Accommodation_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
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
        $this->load->model('accommodation_model');
    }

    /**
     * index    Accommodation upgrades page.
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash && empty($data['infoMessage'])) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'accommodation';

        $settings = $this->accommodation_model->get_settings_DB($currentResortID);

        $data['accommodation_type'] = $settings->accommodation_type;
        $data['is_enabled']         = (int)$settings->is_enabled;
        $data['accommodation_types'] = ACCOMMODATION_TYPES;

        $this->load->view('templates/default', $data);
    }

    /**
     * upgrade  Processes a request to change the accommodation tier.
     *          Charges the upgrade_cost when switching to a new (or same) tier.
     */
    public function upgrade() {
        if (!$this->input->post('accommodation_upgrade_form')) {
            redirect('accommodation_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $new_type = $this->input->post('accommodation_type', TRUE);
        $allowed  = array_keys(ACCOMMODATION_TYPES);
        if (!in_array($new_type, $allowed, TRUE)) {
            $this->session->set_flashdata('infoMessage', 'accommodation_invalid_type');
            redirect('accommodation_controller');
        }

        $settings     = $this->accommodation_model->get_settings_DB($currentResortID);
        $current_type = $settings->accommodation_type;

        // Charge upgrade cost only when switching to a different (or new) tier
        if ($current_type !== $new_type) {
            $upgrade_cost = ACCOMMODATION_TYPES[$new_type]['upgrade_cost'];
            $cash_player  = $this->users_model->get_cash_player();
            if ($cash_player < $upgrade_cost) {
                $this->session->set_flashdata('infoMessage', 'accommodation_not_enough_money');
                redirect('accommodation_controller');
            }
            // Deduct the upgrade cost
            $this->users_model->pay_item($upgrade_cost, $cash_player);
        }

        $saved = $this->accommodation_model->save_settings_DB($currentResortID, $new_type, 1);

        $this->session->set_flashdata(
            'infoMessage',
            $saved ? 'accommodation_upgraded' : 'accommodation_save_error'
        );
        redirect('accommodation_controller');
    }

    /**
     * toggle   Enables or disables the current accommodation without charging cost.
     */
    public function toggle() {
        if (!$this->input->post('accommodation_toggle_form')) {
            redirect('accommodation_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $settings   = $this->accommodation_model->get_settings_DB($currentResortID);
        $new_status = ($settings->is_enabled == 1) ? 0 : 1;

        if ($settings->accommodation_type === 'none') {
            $this->session->set_flashdata('infoMessage', 'accommodation_no_type_selected');
            redirect('accommodation_controller');
        }

        $saved = $this->accommodation_model->save_settings_DB(
            $currentResortID,
            $settings->accommodation_type,
            $new_status
        );

        $this->session->set_flashdata(
            'infoMessage',
            $saved ? ($new_status ? 'accommodation_enabled' : 'accommodation_disabled') : 'accommodation_save_error'
        );
        redirect('accommodation_controller');
    }
}
