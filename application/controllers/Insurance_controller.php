<?php
/**
 * Insurance_controller
 *
 * Manages the Insurance feature:
 *   - Plan selection (none / basic / premium)
 *   - Displays current plan, daily premium, claims history
 */
class Insurance_controller extends CI_Controller {

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
        $this->load->model('insurance_model');
    }

    /**
     * index    Insurance management page.
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
        $data['main_content']    = 'insurance';

        $settings = $this->insurance_model->get_settings_DB($currentResortID);

        $data['plan']                 = $settings->plan;
        $data['total_claims']         = (int)$settings->total_claims;
        $data['total_claimed_amount'] = (int)$settings->total_claimed_amount;

        $data['daily_premium_basic']   = INSURANCE_DAILY_PREMIUM_BASIC;
        $data['daily_premium_premium'] = INSURANCE_DAILY_PREMIUM_PREMIUM;
        $data['lift_payout_basic']     = INSURANCE_LIFT_PAYOUT_BASIC;
        $data['lift_payout_premium']   = INSURANCE_LIFT_PAYOUT_PREMIUM;
        $data['storm_payout']          = INSURANCE_STORM_PAYOUT_PER_LIFT;

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves insurance plan selection from the form POST.
     */
    public function save() {
        if (!$this->input->post('insurance_form')) {
            redirect('insurance_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->input->post('plan', TRUE);
        if (!in_array($plan, ['none', 'basic', 'premium'], TRUE)) {
            $plan = 'none';
        }

        $saved = $this->insurance_model->save_settings_DB($currentResortID, $plan);

        $this->session->set_flashdata('infoMessage', $saved ? 'insurance_settings_saved' : 'insurance_save_error');
        redirect('insurance_controller');
    }
}
