<?php
/**
 * Emergency_controller
 *
 * Manages the Emergency & Rescue System:
 *   - Avalanche rescue team level
 *   - Medical station level
 *   - Risk insurance toggle
 */
class Emergency_controller extends CI_Controller {

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
        $this->load->model('emergency_model');
        $this->load->model('crisis_events_model');
    }

    /**
     * index    Emergency & Rescue management page.
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
        $data['main_content']    = 'emergency_crisis';

        $settings = $this->emergency_model->get_settings_DB($currentResortID);

        $data['rescue_team_level'] = (int)$settings->rescue_team_level;
        $data['medical_stations']  = (int)$settings->medical_stations;
        $data['insurance_enabled'] = (int)$settings->insurance_enabled;

        // Constants for the view
        $data['rescue_cost']          = EMERGENCY_RESCUE_COST;
        $data['medical_cost']         = EMERGENCY_MEDICAL_COST;
        $data['insurance_daily_cost'] = EMERGENCY_INSURANCE_DAILY_COST;
        $data['response_time_base']   = EMERGENCY_RESPONSE_TIME_BASE;
        $data['rescue_reduction']     = EMERGENCY_RESCUE_RESPONSE_REDUCTION;
        $data['medical_reduction']    = EMERGENCY_MEDICAL_RESPONSE_REDUCTION;
        $data['fast_threshold']       = EMERGENCY_RESPONSE_FAST_THRESHOLD;
        $data['poor_threshold']       = EMERGENCY_RESPONSE_POOR_THRESHOLD;
        $data['rep_fast_bonus']       = EMERGENCY_REP_FAST_RESPONSE_BONUS;
        $data['rep_poor_penalty']     = EMERGENCY_REP_POOR_RESPONSE_PENALTY;
        $data['incident_chance']      = EMERGENCY_INCIDENT_CHANCE_PCT;
        $data['fine_no_insurance']    = EMERGENCY_FINE_NO_INSURANCE;
        $data['fine_with_insurance']  = EMERGENCY_FINE_WITH_INSURANCE;
        $data['incident_rep_loss']    = EMERGENCY_INCIDENT_REP_LOSS;

        // Computed response time for current settings
        $data['current_response_time'] =
            EMERGENCY_RESPONSE_TIME_BASE
            - EMERGENCY_RESCUE_RESPONSE_REDUCTION[$data['rescue_team_level']]
            - EMERGENCY_MEDICAL_RESPONSE_REDUCTION[$data['medical_stations']];

        // Crisis events data for the combined view
        $data['crisis_title']         = '<h2>' . $this->lang->line('logs')['crisis_title'] . '</h2>';
        $data['crisis_intro']         = '<div>' . $this->lang->line('logs')['crisis_intro'] . '</div>';
        $data['crisis_resort_built']  = true;
        $active_crisis                = $this->crisis_events_model->get_active_crisis_events_DB($currentUserID);
        $all_crisis                   = $this->crisis_events_model->get_all_crisis_events_DB($currentUserID);
        $data['active_crisis_events'] = $active_crisis->result();
        $data['all_crisis_events']    = $all_crisis->result();

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves emergency settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('emergency_form')) {
            redirect('emergency_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('rescue_team_level', 'rescue_team_level', 'trim|required|integer|greater_than_equal_to[0]|less_than_equal_to[3]');
        $this->form_validation->set_rules('medical_stations',  'medical_stations',  'trim|required|integer|greater_than_equal_to[0]|less_than_equal_to[3]');

        if ($this->form_validation->run() == FALSE) {
            $data['infoMessage'] = 'emergency_invalid_settings';
            $this->index($data);
            return;
        }

        $rescue_level      = (int)$this->input->post('rescue_team_level', TRUE);
        $medical_level     = (int)$this->input->post('medical_stations',  TRUE);
        $insurance_enabled = ($this->input->post('insurance_enabled', TRUE) == '1') ? 1 : 0;

        $saved = $this->emergency_model->save_settings_DB(
            $currentResortID,
            $rescue_level,
            $medical_level,
            $insurance_enabled
        );

        $this->session->set_flashdata('infoMessage', $saved ? 'emergency_settings_saved' : 'emergency_save_error');
        redirect('emergency_controller');
    }
}
