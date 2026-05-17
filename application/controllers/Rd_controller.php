<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rd_controller
 *
 * Manages the Experimental Tech & R&D system.
 * Players can research three experimental projects (advanced lift motors,
 * snowmaking efficiency, slope treatment) at normal pace or rushed.
 * Rushing cuts research time in half but greatly increases failure risk.
 */
class Rd_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        $siteLang = $ci->session->userdata('site_lang') ?: 'english';
        $this->session->set_userdata('site_lang', $siteLang);

        $ci->lang->load('home',     $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',   $siteLang);
        $ci->lang->load('rd',       $siteLang);
        $ci->lang->load('logs',     $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('rd_model');
        $this->rd_model->ensure_table_exists();
    }

    // -------------------------------------------------------------------------
    // Main page
    // -------------------------------------------------------------------------

    /**
     * index  Display all R&D projects with their current status.
     */
    public function index() {
        $data = [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_activated = $this->users_model->check_account_activated($currentUserID);
        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);
        if ($checkIfResortExists->num_rows() == 0) {
            $this->session->set_flashdata('error', 'no_resort');
            redirect('resort_controller');
        }

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;

        // Build a keyed map of existing project rows
        $project_rows = $this->rd_model->get_all_projects_DB($currentResortID);
        $project_map  = [];
        foreach ($project_rows->result() as $row) {
            $project_map[$row->project_key] = [
                'status'     => $row->status,
                'rushed'     => (int)$row->rushed,
                'started_at' => $row->started_at,
                'finish_at'  => $row->finish_at,
            ];
        }

        $site_lang = $this->session->userdata('site_lang') ?? 'english';

        // Build the list of projects
        $projects = [];
        foreach (RD_PROJECTS as $key => $info) {
            $name        = ($site_lang === 'french') ? $info['name_french']        : $info['name_english'];
            $description = ($site_lang === 'french') ? $info['description_french'] : $info['description_english'];

            $row_status = $project_map[$key]['status']     ?? 'not_started';
            $finish_at  = $project_map[$key]['finish_at']  ?? null;
            $started_at = $project_map[$key]['started_at'] ?? null;
            $rushed     = $project_map[$key]['rushed']     ?? 0;

            $projects[$key] = [
                'key'                   => $key,
                'name'                  => $name,
                'description'           => $description,
                'cost'                  => $info['cost'],
                'rush_cost'             => $info['rush_cost'],
                'duration_days'         => $info['duration_days'],
                'rush_duration_days'    => $info['rush_duration_days'],
                'failure_chance_normal' => $info['failure_chance_normal'],
                'failure_chance_rush'   => $info['failure_chance_rush'],
                'bonus_type'            => $info['bonus_type'],
                'bonus_value'           => $info['bonus_value'],
                'status'                => $row_status,
                'rushed'                => $rushed,
                'finish_at'             => $finish_at,
                'started_at'            => $started_at,
            ];
        }

        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $data['cash']         = $resort_info ? (int)$resort_info->cash : 0;
        $data['projects']     = $projects;
        $data['site_lang']    = $site_lang;
        $data['main_content'] = 'rd';

        $this->load->view('templates/default', $data);
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /**
     * start_project  Starts an R&D project (normal or rushed).
     *
     * @param int    $currentResortID
     * @param string $project_key
     * @param string $mode             'normal' | 'rush'
     */
    public function start_project($currentResortID, $project_key, $mode = 'normal') {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        if (!array_key_exists($project_key, RD_PROJECTS)) {
            $this->session->set_flashdata('infoMessage', 'bad_action');
            redirect('rd_controller');
            return;
        }

        $info   = RD_PROJECTS[$project_key];
        $rushed = ($mode === 'rush') ? 1 : 0;
        $cost   = $rushed ? $info['rush_cost'] : $info['cost'];
        $days   = $rushed ? $info['rush_duration_days'] : $info['duration_days'];

        // Check not already active or completed
        $existing = $this->rd_model->get_project_row_DB($currentResortID, $project_key);
        if ($existing && in_array($existing->status, ['in_progress', 'completed'], true)) {
            $this->session->set_flashdata('infoMessage', 'rd_already_active');
            redirect('rd_controller');
            return;
        }

        // Check funds
        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $cost) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('rd_controller');
            return;
        }

        $result = $this->rd_model->start_project_DB($currentResortID, $project_key, $days, $rushed);

        if ($result) {
            $this->users_model->pay_item($cost, $cash_player);
            $updated_cash = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $updated_cash);

            add_cost_stat_table($currentResortID, $cost, 'cost_purchases');
            add_cost_stat_table($currentResortID, $cost, 'expenses');

            $site_lang   = $this->session->userdata('site_lang') ?? 'english';
            $tech_name   = ($site_lang === 'french') ? $info['name_french'] : $info['name_english'];
            $currentUserID = $this->users_model->get_user_id();
            $log_msg     = $this->lang->line('logs')['rd_started'] . ': ' . $tech_name
                         . ($rushed ? ' (' . $this->lang->line('rd')['mode_rush'] . ')' : '');

            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['rd_log_type'],
                'data'      => $log_msg,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('logs')['rd_log_type'],
                'data'      => $log_msg,
            ]);

            $this->session->set_flashdata('infoMessage', 'rd_started');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('rd_controller');
    }
}
