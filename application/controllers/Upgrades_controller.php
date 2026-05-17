<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Upgrades_controller
 *
 * Combines Slope, Snowmaking, Marketing, and Staff upgrade research trees
 * into a single tabbed page.
 */
class Upgrades_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',                $siteLang);
        $ci->lang->load('login_form',          $siteLang);
        $ci->lang->load('navbar',              $siteLang);
        $ci->lang->load('slope_upgrade',       $siteLang);
        $ci->lang->load('snowmaking_upgrade',  $siteLang);
        $ci->lang->load('marketing_upgrade',   $siteLang);
        $ci->lang->load('staff_upgrade',       $siteLang);
        $ci->lang->load('logs',                $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('slope_upgrade_model');
        $this->load->model('snowmaking_upgrade_model');
        $this->load->model('marketing_upgrade_model');
        $this->load->model('staff_upgrade_model');
    }

    public function index() {
        $data = [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash) {
            $data['infoMessage'] = $flash;
        }

        $message_tree = $this->session->flashdata('message_tree');
        if ($message_tree) {
            $data['message_tree'] = $message_tree;
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

        $site_lang = $this->session->userdata('site_lang') ?? 'english';
        $data['site_lang'] = $site_lang;

        $trees = [
            'slope_upgrade'      => ['model' => 'slope_upgrade_model',     'const' => SLOPE_UPGRADE_TREE],
            'snowmaking_upgrade' => ['model' => 'snowmaking_upgrade_model', 'const' => SNOWMAKING_UPGRADE_TREE],
            'marketing_upgrade'  => ['model' => 'marketing_upgrade_model',  'const' => MARKETING_UPGRADE_TREE],
            'staff_upgrade'      => ['model' => 'staff_upgrade_model',      'const' => STAFF_UPGRADE_TREE],
        ];

        $all_trees = [];
        foreach ($trees as $tree_key => $tree_info) {
            $model         = $this->{$tree_info['model']};
            $research_rows = $model->get_all_research_DB($currentResortID);
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

            $upgrades = [];
            foreach ($tree_info['const'] as $key => $info) {
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

            $all_trees[$tree_key] = $upgrades;
        }

        $data['all_trees']    = $all_trees;
        $data['main_content'] = 'upgrades';
        $this->load->view('templates/default', $data);
    }
}
