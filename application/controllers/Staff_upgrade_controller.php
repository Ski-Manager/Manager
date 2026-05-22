<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Staff_upgrade_controller
 *
 * Manages the Staff Upgrade Research Tree.
 * Players research staff improvements that take time and money to complete.
 */
class Staff_upgrade_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',            $siteLang);
        $ci->lang->load('login_form',      $siteLang);
        $ci->lang->load('navbar',          $siteLang);
        $ci->lang->load('staff_upgrade',   $siteLang);
        $ci->lang->load('logs',            $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('staff_upgrade_model');
        $this->load->model('logs_model');
    }

    public function index() {
        redirect('upgrades_controller');
    }

    public function start_research($currentResortID, $upgrade_key) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        if (!array_key_exists($upgrade_key, STAFF_UPGRADE_TREE)) {
            $this->session->set_flashdata('infoMessage', 'bad_action');
            $this->session->set_flashdata('message_tree', 'staff_upgrade');
            redirect('upgrades_controller');
        }

        $info = STAFF_UPGRADE_TREE[$upgrade_key];

        if (!is_null($info['prerequisite'])) {
            $prereq_row  = $this->staff_upgrade_model->get_research_row_DB($currentResortID, $info['prerequisite']);
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
                $this->session->set_flashdata('message_tree', 'staff_upgrade');
                redirect('upgrades_controller');
            }
        }

        $existing = $this->staff_upgrade_model->get_research_row_DB($currentResortID, $upgrade_key);
        if ($existing) {
            $this->session->set_flashdata('infoMessage', 'upgrade_already_researched');
            $this->session->set_flashdata('message_tree', 'staff_upgrade');
            redirect('upgrades_controller');
        }

        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $info['cost']) {
            $this->session->set_flashdata('infoMessage', 'not_enough_money');
            $this->session->set_flashdata('message_tree', 'staff_upgrade');
            redirect('upgrades_controller');
        }

        $result = $this->staff_upgrade_model->start_research_DB($currentResortID, $upgrade_key, $info['duration_days']);

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
                'type'      => $this->lang->line('staff_upgrade')['log_type'],
                'data'      => $this->lang->line('staff_upgrade')['log_started'] . ': ' . $upgrade_name,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $this->lang->line('staff_upgrade')['log_type'],
                'data'      => $this->lang->line('staff_upgrade')['log_started'] . ': ' . $upgrade_name,
            ]);

            $this->session->set_flashdata('infoMessage', 'upgrade_started');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        $this->session->set_flashdata('message_tree', 'staff_upgrade');
        redirect('upgrades_controller');
    }
}
