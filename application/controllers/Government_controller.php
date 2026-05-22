<?php
/**
 * Government_controller
 *
 * Manages the Government & Regulations System:
 *   - Dashboard showing compliance score, current tax rate, audit history
 *   - Subsidy claiming for eco-friendly upgrades
 *
 * Regulations enforced nightly by NightlyMainJobs_controller:
 *   - Environmental protection limits expansion (low compliance blocks expansion)
 *   - Safety inspection audits (random daily chance)
 *   - Subsidies for eco-friendly upgrades (awarded when eco_reputation qualifies)
 *   - Tax rates changing each season
 */
class Government_controller extends CI_Controller {

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
        $this->load->model('government_model');
        $this->load->model('environment_model');
    }

    /**
     * index    Government & Regulations dashboard page.
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
        $data['main_content']    = 'government';

        $gov = $this->government_model->get_government_DB($currentResortID);
        $data['gov'] = $gov;

        $env = $this->environment_model->get_environment_DB($currentResortID);
        $data['eco_reputation'] = (int)$env->eco_reputation;

        // Pass constants for display
        $data['gov_compliance_block_threshold']   = GOV_COMPLIANCE_BLOCK_THRESHOLD;
        $data['gov_compliance_restore_threshold'] = GOV_COMPLIANCE_RESTORE_THRESHOLD;
        $data['gov_audit_pass_threshold']         = GOV_AUDIT_PASS_THRESHOLD;
        $data['gov_audit_pass_reward']            = GOV_AUDIT_PASS_REWARD;
        $data['gov_audit_fail_fine']              = GOV_AUDIT_FAIL_FINE;
        $data['gov_tax_rate_min']                 = GOV_TAX_RATE_MIN;
        $data['gov_tax_rate_max']                 = GOV_TAX_RATE_MAX;
        $data['gov_subsidy_eco_threshold']        = GOV_SUBSIDY_ECO_THRESHOLD;
        $data['gov_subsidy_amount']               = GOV_SUBSIDY_AMOUNT;

        $data['cash_player'] = $this->users_model->get_cash_player();

        $this->load->view('templates/default', $data);
    }

    /**
     * claim_subsidy    Claims the available eco-subsidy for the current resort.
     */
    public function claim_subsidy() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $amount = $this->government_model->claim_subsidy_DB($currentResortID);

        if ($amount > 0) {
            // Credit the subsidy to the resort account
            $this->db->trans_start();
            $this->db->set('cash', 'cash + ' . (int)$amount, FALSE);
            $this->db->where('id_resort', $currentResortID);
            $this->db->update('game_resorts');
            $this->db->trans_complete();

            // Update finances stats
            $this->load->model('finances_model');
            add_revenue_stat_table($currentResortID, $amount, 'rev_other');

            $log_text = $this->lang->line('building')['gov_subsidy_claimed_log'] . ' ' . number_format($amount, 0, '.', ' ') . ' €';
            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['government'], 'data' => $log_text]);
            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['government'], 'data' => $log_text]);

            $msg = 'gov_subsidy_claimed';
        } else {
            $msg = 'gov_no_subsidy_available';
        }

        $this->session->set_flashdata('infoMessage', $msg);
        redirect('government_controller');
    }
}
