<?php
/**
 * Crowding_controller
 *
 * Manages the Crowding System feature:
 *   - Daily visitor capacity limit
 *   - Timed entry (caps visitors to the capacity limit)
 *   - Crowd alert threshold (% of capacity before reputation penalty applies)
 */
class Crowding_controller extends CI_Controller {

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
        $this->load->model('logs_model');
        $this->load->model('crowding_model');
    }

    /**
     * index    Crowding management page.
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
        $data['main_content']    = 'crowding';

        $settings = $this->crowding_model->get_settings_DB($currentResortID);

        $data['capacity_limit']        = (int)$settings->capacity_limit;
        $data['timed_entry_enabled']   = (int)$settings->timed_entry_enabled;
        $data['crowd_alert_threshold'] = (int)$settings->crowd_alert_threshold;

        $data['min_capacity']       = CROWDING_MIN_CAPACITY;
        $data['max_capacity']       = CROWDING_MAX_CAPACITY;
        $data['min_threshold']      = CROWDING_MIN_ALERT_THRESHOLD;
        $data['max_threshold']      = CROWDING_MAX_ALERT_THRESHOLD;

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves crowding settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('crowding_form')) {
            redirect('crowding_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('capacity_limit',        'capacity_limit',        'trim|required|integer|greater_than_equal_to[' . CROWDING_MIN_CAPACITY . ']|less_than_equal_to[' . CROWDING_MAX_CAPACITY . ']');
        $this->form_validation->set_rules('crowd_alert_threshold', 'crowd_alert_threshold', 'trim|required|integer|greater_than_equal_to[' . CROWDING_MIN_ALERT_THRESHOLD . ']|less_than_equal_to[' . CROWDING_MAX_ALERT_THRESHOLD . ']');

        if ($this->form_validation->run() == FALSE) {
            $data['infoMessage'] = 'crowding_invalid_settings';
            $this->index($data);
            return;
        }

        $capacity_limit        = (int)$this->input->post('capacity_limit',        TRUE);
        $crowd_alert_threshold = (int)$this->input->post('crowd_alert_threshold', TRUE);
        $timed_entry_enabled   = ($this->input->post('timed_entry_enabled', TRUE) == '1') ? 1 : 0;

        $saved = $this->crowding_model->save_settings_DB(
            $currentResortID,
            $capacity_limit,
            $timed_entry_enabled,
            $crowd_alert_threshold
        );

        $this->session->set_flashdata('infoMessage', $saved ? 'crowding_settings_saved' : 'crowding_save_error');
        redirect('crowding_controller');
    }
}
