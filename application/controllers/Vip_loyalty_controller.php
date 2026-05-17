<?php
/**
 * Vip_loyalty_controller
 *
 * Manages the VIP & Loyalty Programmes feature:
 *   - Loyalty discount programme for frequent skiers
 *   - VIP private lift service
 *   - VIP premium slope access
 *   - VIP concierge service
 */
class Vip_loyalty_controller extends CI_Controller {

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
        $this->load->model('vip_loyalty_model');
    }

    /**
     * index    VIP & Loyalty Programmes management page.
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
        $data['main_content']    = 'vip_loyalty';

        $settings = $this->vip_loyalty_model->get_settings_DB($currentResortID);

        $data['loyalty_enabled']      = (int)$settings->loyalty_enabled;
        $data['loyalty_discount_pct'] = (int)$settings->loyalty_discount_pct;
        $data['vip_private_lift']     = (int)$settings->vip_private_lift;
        $data['vip_premium_slopes']   = (int)$settings->vip_premium_slopes;
        $data['vip_concierge']        = (int)$settings->vip_concierge;
        $data['vip_airport_transfer'] = (int)$settings->vip_airport_transfer;
        $data['vip_apreski_lounge']   = (int)$settings->vip_apreski_lounge;

        $data['discount_min'] = VIP_LOYALTY_DISCOUNT_MIN;
        $data['discount_max'] = VIP_LOYALTY_DISCOUNT_MAX;

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves VIP/loyalty settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('vip_loyalty_form')) {
            redirect('vip_loyalty_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('loyalty_discount_pct', 'loyalty_discount_pct', 'trim|required|integer|greater_than_equal_to[' . VIP_LOYALTY_DISCOUNT_MIN . ']|less_than_equal_to[' . VIP_LOYALTY_DISCOUNT_MAX . ']');

        if ($this->form_validation->run() == FALSE) {
            $data['infoMessage'] = 'vip_loyalty_invalid_settings';
            $this->index($data);
            return;
        }

        $loyalty_enabled      = ($this->input->post('loyalty_enabled',    TRUE) == '1') ? 1 : 0;
        $loyalty_discount_pct = (int)$this->input->post('loyalty_discount_pct', TRUE);
        $vip_private_lift     = ($this->input->post('vip_private_lift',   TRUE) == '1') ? 1 : 0;
        $vip_premium_slopes   = ($this->input->post('vip_premium_slopes', TRUE) == '1') ? 1 : 0;
        $vip_concierge        = ($this->input->post('vip_concierge',      TRUE) == '1') ? 1 : 0;
        $vip_airport_transfer = ($this->input->post('vip_airport_transfer', TRUE) == '1') ? 1 : 0;
        $vip_apreski_lounge   = ($this->input->post('vip_apreski_lounge',  TRUE) == '1') ? 1 : 0;

        $saved = $this->vip_loyalty_model->save_settings_DB(
            $currentResortID,
            $loyalty_enabled,
            $loyalty_discount_pct,
            $vip_private_lift,
            $vip_premium_slopes,
            $vip_concierge,
            $vip_airport_transfer,
            $vip_apreski_lounge
        );

        $this->session->set_flashdata('infoMessage', $saved ? 'vip_loyalty_settings_saved' : 'vip_loyalty_save_error');
        redirect('vip_loyalty_controller');
    }
}
