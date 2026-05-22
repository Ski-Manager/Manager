<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Terrain_engineering_controller
 *
 * Manages the Terrain Engineering Research Tree.
 * Players research terrain features (terrain park, moguls, tree runs,
 * backcountry access, advanced slope program) that take time and money.
 */
class Terrain_engineering_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',                  $siteLang);
        $ci->lang->load('login_form',            $siteLang);
        $ci->lang->load('navbar',                $siteLang);
        $ci->lang->load('terrain_engineering',   $siteLang);
        $ci->lang->load('logs',                  $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('terrain_engineering_model');
        $this->load->model('logs_model');
    }

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

        $research_rows = $this->terrain_engineering_model->get_all_research_DB($currentResortID);
        $research_map  = [];
        if (is_object($research_rows)) {
            foreach ($research_rows->result() as $row) {
                $status = $row->status;
                if ($status === 'in_progress' && strtotime($row->finish_at) <= time()) {
                    $status = 'completed';
                }
                $research_map[$row->upgrade_key] = [
                    'status'     => $status,
                    'started_at' => $row->started_at,
                    'finish_at'  => $row->finish_at,
                ];
            }
        }

        $site_lang = $this->session->userdata('site_lang') ?? 'english';

        $upgrades = [];
        foreach (TERRAIN_ENGINEERING_TREE as $key => $info) {
            $name        = ($site_lang === 'french') ? $info['name_french']        : $info['name_english'];
            $description = ($site_lang === 'french') ? $info['description_french'] : $info['description_english'];

            $row_status = $research_map[$key]['status']    ?? 'not_started';
            $finish_at  = $research_map[$key]['finish_at'] ?? null;

            $prereq_met = true;
            if (!is_null($info['prerequisite'])) {
                $prereq_status = $research_map[$info['prerequisite']]['status'] ?? 'not_started';
                if ($prereq_status === 'in_progress' && isset($research_map[$info['prerequisite']]['finish_at'])
                    && strtotime($research_map[$info['prerequisite']]['finish_at']) <= time()) {
                    $prereq_status = 'completed';
                }
                $prereq_met = ($prereq_status === 'completed');
            }

            $upgrades[$key] = [
                'key'          => $key,
                'name'         => $name,
                'description'  => $description,
                'cost'         => $info['cost'],
                'duration'     => $info['duration_days'],
                'prerequisite' => $info['prerequisite'],
                'prereq_met'   => $prereq_met,
                'status'       => $row_status,
                'finish_at'    => $finish_at,
            ];
        }

        $data['upgrades']      = $upgrades;
        $data['site_lang']     = $site_lang;
        $data['tree_key']      = 'terrain_engineering';
        $data['main_content']  = 'terrain_engineering';
        $this->load->view('templates/default', $data);
    }

    public function start_research($currentResortID, $upgrade_key) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        if (!array_key_exists($upgrade_key, TERRAIN_ENGINEERING_TREE)) {
            $this->session->set_flashdata('infoMessage', 'bad_action');
            redirect('terrain_engineering_controller');
        }

        $info = TERRAIN_ENGINEERING_TREE[$upgrade_key];

        if (!is_null($info['prerequisite'])) {
            $prereq_row  = $this->terrain_engineering_model->get_research_row_DB($currentResortID, $info['prerequisite']);
            $prereq_done = false;
            if ($prereq_row) {
                $prereq_status = $prereq_row->status;
                if ($prereq_status === 'in_progress' && strtotime($prereq_row->finish_at) <= time()) {
                    $prereq_status = 'completed';
                }
                $prereq_done = ($prereq_status === 'completed');
            }
            if (!$prereq_done) {
                $this->session->set_flashdata('infoMessage', 'upgrade_prereq_not_met');
                redirect('terrain_engineering_controller');
            }
        }

        $existing = $this->terrain_engineering_model->get_research_row_DB($currentResortID, $upgrade_key);
        if ($existing) {
            $this->session->set_flashdata('infoMessage', 'upgrade_already_researched');
            redirect('terrain_engineering_controller');
        }

        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $info['cost']) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            redirect('terrain_engineering_controller');
        }

        $result = $this->terrain_engineering_model->start_research_DB($currentResortID, $upgrade_key, $info['duration_days']);

        if ($result) {
            $this->users_model->pay_item($info['cost'], $cash_player);
            $updated_cash = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $updated_cash);

            add_cost_stat_table($currentResortID, $info['cost'], 'cost_purchases');
            add_cost_stat_table($currentResortID, $info['cost'], 'expenses');

            $site_lang    = $this->session->userdata('site_lang') ?? 'english';
            $upgrade_name = ($site_lang === 'french') ? $info['name_french'] : $info['name_english'];
            $currentUserID = $this->users_model->get_user_id();
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('terrain_engineering')['log_type'],
                'data'      => $this->lang->line('terrain_engineering')['log_started'] . ': ' . $upgrade_name,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('terrain_engineering')['log_type'],
                'data'      => $this->lang->line('terrain_engineering')['log_started'] . ': ' . $upgrade_name,
            ]);

            $this->session->set_flashdata('infoMessage', 'upgrade_started');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('terrain_engineering_controller');
    }
}
