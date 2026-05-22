<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lift_tech_controller
 *
 * Manages the Lift Technology Research Tree.
 * Players can research five lift upgrades (Faster Chair Loading, Heated Seats,
 * Bubble Covers, AI Maintenance System, Smart Snowmaking) each costing money
 * and taking a set number of in-game days to complete.
 */
class Lift_tech_controller extends CI_Controller {

    private $siteLang;

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',      $siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',    $siteLang);
        $ci->lang->load('lift_tech', $siteLang);
        $ci->lang->load('logs',      $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('lift_tech_model');
        $this->load->model('logs_model');
    }

    /**
     * index  Display the research tree with current progress.
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

        // Build a keyed map of existing research rows for this resort
        $research_rows = $this->lift_tech_model->get_all_research_DB($currentResortID);
        $research_map  = [];
        if (is_object($research_rows)) {
            foreach ($research_rows->result() as $row) {
                // Promote in_progress to completed if finish_at has passed
                $status = $row->status;
                if ($status === 'in_progress' && strtotime($row->finish_at) <= time()) {
                    $status = 'completed';
                }
                $research_map[$row->tech_key] = [
                    'status'     => $status,
                    'started_at' => $row->started_at,
                    'finish_at'  => $row->finish_at,
                ];
            }
        }

        $site_lang = $this->session->userdata('site_lang') ?? 'english';

        // Build the list of technologies with computed status and prerequisite check
        $techs = [];
        foreach (LIFT_TECH_TREE as $key => $info) {
            $name        = ($site_lang === 'french') ? $info['name_french']        : $info['name_english'];
            $description = ($site_lang === 'french') ? $info['description_french'] : $info['description_english'];

            $row_status    = $research_map[$key]['status']     ?? 'not_started';
            $finish_at     = $research_map[$key]['finish_at']  ?? null;
            $started_at    = $research_map[$key]['started_at'] ?? null;

            // Check prerequisite
            $prereq_met = true;
            if (!is_null($info['prerequisite'])) {
                $prereq_status = $research_map[$info['prerequisite']]['status'] ?? 'not_started';
                // Promote in_progress prerequisite to completed if overdue
                if ($prereq_status === 'in_progress' && isset($research_map[$info['prerequisite']]['finish_at'])
                    && strtotime($research_map[$info['prerequisite']]['finish_at']) <= time()) {
                    $prereq_status = 'completed';
                }
                $prereq_met = ($prereq_status === 'completed');
            }

            $techs[$key] = [
                'key'         => $key,
                'name'        => $name,
                'description' => $description,
                'cost'        => $info['cost'],
                'duration'    => $info['duration_days'],
                'prerequisite'=> $info['prerequisite'],
                'prereq_met'  => $prereq_met,
                'status'      => $row_status,
                'finish_at'   => $finish_at,
                'started_at'  => $started_at,
            ];
        }

        $data['techs']         = $techs;
        $data['site_lang']     = $site_lang;
        $data['main_content']  = 'lift_tech';
        $this->load->view('templates/default', $data);
    }

    /**
     * start_research  Deducts the cost and starts researching a technology.
     *
     * @param int    $currentResortID
     * @param string $tech_key
     */
    public function start_research($currentResortID, $tech_key) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        if (!array_key_exists($tech_key, LIFT_TECH_TREE)) {
            $this->session->set_flashdata('infoMessage', 'bad_action');
            redirect('lift_tech_controller');
        }

        $info = LIFT_TECH_TREE[$tech_key];

        // Check prerequisite
        if (!is_null($info['prerequisite'])) {
            $prereq_row = $this->lift_tech_model->get_research_row_DB($currentResortID, $info['prerequisite']);
            $prereq_done = false;
            if ($prereq_row) {
                $prereq_status = $prereq_row->status;
                if ($prereq_status === 'in_progress' && strtotime($prereq_row->finish_at) <= time()) {
                    $prereq_status = 'completed';
                }
                $prereq_done = ($prereq_status === 'completed');
            }
            if (!$prereq_done) {
                $this->session->set_flashdata('infoMessage', 'lift_tech_prereq_not_met');
                redirect('lift_tech_controller');
            }
        }

        // Check not already researched or in progress
        $existing = $this->lift_tech_model->get_research_row_DB($currentResortID, $tech_key);
        if ($existing) {
            $this->session->set_flashdata('infoMessage', 'lift_tech_already_researched');
            redirect('lift_tech_controller');
        }

        // Check funds
        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $info['cost']) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('lift_tech_controller');
        }

        // Start research first — only deduct cash on success
        $result = $this->lift_tech_model->start_research_DB($currentResortID, $tech_key, $info['duration_days']);

        if ($result) {
            // Deduct cost
            $this->users_model->pay_item($info['cost'], $cash_player);
            $updated_cash = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $updated_cash);

            add_cost_stat_table($currentResortID, $info['cost'], 'cost_purchases');
            add_cost_stat_table($currentResortID, $info['cost'], 'expenses');

            // Log
            $site_lang = $this->session->userdata('site_lang') ?? 'english';
            $tech_name = ($site_lang === 'french') ? $info['name_french'] : $info['name_english'];
            $currentUserID = $this->users_model->get_user_id();
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('lift_tech')['log_type'],
                'data'      => $this->lang->line('lift_tech')['log_started'] . ': ' . $tech_name,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('lift_tech')['log_type'],
                'data'      => $this->lang->line('lift_tech')['log_started'] . ': ' . $tech_name,
            ]);

            $this->session->set_flashdata('infoMessage', 'lift_tech_started');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('lift_tech_controller');
    }
}
