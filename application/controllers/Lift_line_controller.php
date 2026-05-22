<?php
/**
 * Lift_line_controller
 *
 * Manages the Lift Line feature:
 *   - Queue time tolerance setting
 *   - VIP fast pass lane (enabled/disabled + price)
 */
class Lift_line_controller extends CI_Controller {

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
        $this->load->model('lift_line_model');
    }

    /**
     * index    Lift line management page.
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
        $data['main_content']    = 'lift_line';

        $settings = $this->lift_line_model->get_settings_DB($currentResortID);

        $data['queue_tolerance_minutes'] = (int)$settings->queue_tolerance_minutes;
        $data['vip_fastpass_enabled']    = (int)$settings->vip_fastpass_enabled;
        $data['vip_fastpass_price']      = (int)$settings->vip_fastpass_price;

        $data['min_tolerance'] = LIFT_LINE_MIN_TOLERANCE;
        $data['max_tolerance'] = LIFT_LINE_MAX_TOLERANCE;
        $data['vip_min_price'] = LIFT_LINE_VIP_MIN_PRICE;
        $data['vip_max_price'] = LIFT_LINE_VIP_MAX_PRICE;

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves lift-line settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('lift_line_form')) {
            redirect('lift_line_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('queue_tolerance_minutes', 'queue_tolerance_minutes', 'trim|required|integer|greater_than_equal_to[' . LIFT_LINE_MIN_TOLERANCE . ']|less_than_equal_to[' . LIFT_LINE_MAX_TOLERANCE . ']');
        $this->form_validation->set_rules('vip_fastpass_price',      'vip_fastpass_price',      'trim|required|integer|greater_than_equal_to[' . LIFT_LINE_VIP_MIN_PRICE . ']|less_than_equal_to[' . LIFT_LINE_VIP_MAX_PRICE . ']');

        if ($this->form_validation->run() == FALSE) {
            $data['infoMessage'] = 'lift_line_invalid_settings';
            $this->index($data);
            return;
        }

        $queue_tolerance    = (int)$this->input->post('queue_tolerance_minutes', TRUE);
        $vip_fastpass_price = (int)$this->input->post('vip_fastpass_price',      TRUE);
        $vip_enabled        = ($this->input->post('vip_fastpass_enabled', TRUE) == '1') ? 1 : 0;

        $saved = $this->lift_line_model->save_settings_DB(
            $currentResortID,
            $queue_tolerance,
            $vip_enabled,
            $vip_fastpass_price
        );

        $this->session->set_flashdata('infoMessage', $saved ? 'lift_line_settings_saved' : 'lift_line_save_error');
        redirect('lift_line_controller');
    }
}
