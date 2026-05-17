<?php
/**
 * Sponsorship_controller
 *
 * Manages the Sponsorship & Branding feature:
 *   - View available / active sponsor contracts
 *   - Sign a new contract (deducts signing fee)
 *   - Terminate an existing contract
 */
class Sponsorship_controller extends CI_Controller {

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
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('sponsorship_model');
    }

    /**
     * index    Sponsorship management page.
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
        $data['main_content']    = 'sponsorship';

        // Build a keyed map of existing contracts
        $existing = $this->sponsorship_model->get_sponsorships_DB($currentResortID);
        $contracts = [];
        foreach ($existing as $row) {
            $contracts[$row->sponsor_type] = $row;
        }

        // Current resort reputation (for requirement check in view)
        $resort_row = $this->db
            ->select('reputation, cash')
            ->from('game_resorts')
            ->where('id_resort', $currentResortID)
            ->limit(1)
            ->get()
            ->row();

        $data['resort_reputation'] = $resort_row ? (int)$resort_row->reputation : 0;
        $data['resort_cash']       = $resort_row ? (int)$resort_row->cash       : 0;
        $data['sponsor_types']     = SPONSORSHIP_TYPES;
        $data['contracts']         = $contracts;

        $this->load->view('templates/default', $data);
    }

    /**
     * sign     Signs (or upgrades) a sponsor contract.
     *          POST params: sponsorship_form, sponsor_type, contract_level
     */
    public function sign() {
        if (!$this->input->post('sponsorship_form')) {
            redirect('sponsorship_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $sponsor_type   = $this->input->post('sponsor_type',   TRUE);
        $contract_level = (int)$this->input->post('contract_level', TRUE);

        // Validate sponsor type
        if (!array_key_exists($sponsor_type, SPONSORSHIP_TYPES)) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_invalid_type');
            redirect('sponsorship_controller');
        }

        // Validate level
        if ($contract_level < 1 || $contract_level > 3) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_invalid_level');
            redirect('sponsorship_controller');
        }

        $type_cfg = SPONSORSHIP_TYPES[$sponsor_type];
        $idx      = $contract_level - 1;
        $min_rep  = $type_cfg['min_reputation'][$idx];
        $sign_cost= $type_cfg['sign_cost'][$idx];

        // Check resort resources
        $resort_row = $this->db
            ->select('reputation, cash')
            ->from('game_resorts')
            ->where('id_resort', $currentResortID)
            ->limit(1)
            ->get()
            ->row();

        if (!$resort_row) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_error');
            redirect('sponsorship_controller');
        }

        if ((int)$resort_row->reputation < $min_rep) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_rep_too_low');
            redirect('sponsorship_controller');
        }

        if ((int)$resort_row->cash < $sign_cost) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_insufficient_funds');
            redirect('sponsorship_controller');
        }

        // Deduct signing fee
        $this->db->trans_start();
        $this->db->set('cash', 'cash-' . $sign_cost, FALSE);
        $this->db->where('id_resort', $currentResortID);
        $this->db->update('game_resorts');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_error');
            redirect('sponsorship_controller');
        }

        // Save the contract
        $saved = $this->sponsorship_model->sign_sponsor_DB($currentResortID, $sponsor_type, $contract_level);

        if ($saved) {
            // Log the action
            $this->lang->load('building', $this->session->userdata('site_lang') ?? 'english');
            $log_data = $this->lang->line('building')['sponsorship_signed_log']
                . ' ' . $this->lang->line('building')['sponsorship_type_' . $sponsor_type]
                . ' (L' . $contract_level . ')';
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['revenues'],
                'data'      => $log_data,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['revenues'],
                'data'      => $log_data,
            ]);
            $this->session->set_flashdata('infoMessage', 'sponsorship_signed');
        } else {
            $this->session->set_flashdata('infoMessage', 'sponsorship_error');
        }

        redirect('sponsorship_controller');
    }

    /**
     * terminate    Terminates an active sponsor contract.
     *              POST params: sponsorship_terminate_form, sponsor_type
     */
    public function terminate() {
        if (!$this->input->post('sponsorship_terminate_form')) {
            redirect('sponsorship_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $sponsor_type = $this->input->post('sponsor_type', TRUE);

        if (!array_key_exists($sponsor_type, SPONSORSHIP_TYPES)) {
            $this->session->set_flashdata('infoMessage', 'sponsorship_invalid_type');
            redirect('sponsorship_controller');
        }

        $terminated = $this->sponsorship_model->terminate_sponsor_DB($currentResortID, $sponsor_type);

        $this->session->set_flashdata(
            'infoMessage',
            $terminated ? 'sponsorship_terminated' : 'sponsorship_error'
        );
        redirect('sponsorship_controller');
    }
}
