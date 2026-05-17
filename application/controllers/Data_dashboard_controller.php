<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data_dashboard_controller    Displays the Data Dashboard.
 */
class Data_dashboard_controller extends CI_Controller {

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
        $this->siteLang = $siteLang;

        $ci->lang->load('home',           $siteLang);
        $ci->lang->load('login_form',     $siteLang);
        $ci->lang->load('navbar',         $siteLang);
        $ci->lang->load('data_dashboard', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('data_dashboard_model');
    }


    public function index($data = NULL) {
        $data = $data ?? [];

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

        $data['kpi']           = $this->data_dashboard_model->get_kpi_data_DB($currentResortID);
        $data['resort_built']  = true;
        $data['currentResortId'] = $currentResortID;
        $data['slopes_detail'] = $this->data_dashboard_model->get_slopes_detail_DB($currentResortID)->result();
        $data['lifts_detail']  = $this->data_dashboard_model->get_lifts_detail_DB($currentResortID)->result();

        $data['main_content'] = 'data_dashboard';
        $this->load->view('templates/default', $data);
    }


    // ── AJAX chart endpoints ─────────────────────────────────────────────

    public function get_traffic_heatmap_chart() {
        $currentResortID = (int)$this->input->post('currentResortID', TRUE);
        $result          = $this->data_dashboard_model->get_traffic_heatmap_DB($currentResortID);
        $lang_col        = 'name_' . $this->siteLang;

        $rows = [];

        $max_throughput = 1;
        foreach ($result['lifts']->result() as $row) {
            $eff = $row->throughput * max((int)$row->lift_condition, 0) / 100;
            if ($eff > $max_throughput) $max_throughput = $eff;
        }
        foreach ($result['lifts']->result() as $row) {
            $name  = (!empty($row->custom_name)) ? $row->custom_name : (isset($row->$lang_col) ? $row->$lang_col : $row->name_english);
            $eff   = $row->throughput * max((int)$row->lift_condition, 0) / 100;
            $rows[] = ['label' => $name, 'value' => round($eff / $max_throughput * 100), 'type' => 'lift'];
        }
        foreach ($result['slopes']->result() as $row) {
            $name  = (!empty($row->custom_name)) ? $row->custom_name : (isset($row->$lang_col) ? $row->$lang_col : $row->name_english);
            $rows[] = ['label' => $name, 'value' => (int)$row->slope_condition, 'type' => 'slope'];
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'rows' => $rows]);
    }

    public function get_profit_breakdown_chart() {
        $currentResortID = (int)$this->input->post('currentResortID', TRUE);
        $yesterday       = gmdate('Y-m-d', strtotime('-1 day'));
        $breakdown       = $this->data_dashboard_model->get_profit_breakdown_DB($currentResortID, $yesterday);

        $labels = [
            'skipass'=>'Ski Pass','restaurant'=>'Restaurant','hotel'=>'Hotel','rental'=>'Rental',
            'leisure'=>'Leisure','luxury'=>'Luxury','medical'=>'Medical','skibus'=>'Ski Bus',
            'instructor'=>'Instructor','parking'=>'Parking','other'=>'Other',
        ];
        $colors = ['#60a5fa','#34d399','#f97316','#a855f7','#facc15','#f43f5e','#38bdf8','#fb923c','#4ade80','#c084fc','#94a3b8'];

        $data = []; $lbls = []; $clrs = []; $i = 0;
        foreach ($breakdown as $key => $amount) {
            if ($amount > 0) {
                $data[]  = round($amount);
                $lbls[]  = $labels[$key];
                $clrs[]  = $colors[$i % count($colors)];
            }
            $i++;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'labels' => $lbls, 'data' => $data, 'colors' => $clrs]);
    }

    public function get_visitor_segmentation_chart() {
        $currentResortID = (int)$this->input->post('currentResortID', TRUE);
        $result          = $this->data_dashboard_model->get_visitor_segmentation_DB($currentResortID)->result();

        $diff_labels = [1 => 'Beginner (Green)', 2 => 'Intermediate (Blue)', 3 => 'Advanced (Red)', 4 => 'Expert (Black)'];
        $diff_colors = [1 => '#4ade80',           2 => '#60a5fa',            3 => '#f87171',         4 => '#374151'];

        $data = []; $lbls = []; $clrs = [];
        foreach ($result as $row) {
            $d = (int)$row->id_difficulty;
            $lbls[] = $diff_labels[$d] ?? "Difficulty $d";
            $data[] = (int)$row->total_visitors;
            $clrs[] = $diff_colors[$d] ?? '#94a3b8';
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'labels' => $lbls, 'data' => $data, 'colors' => $clrs]);
    }

    public function get_accident_probability_chart() {
        $currentResortID = (int)$this->input->post('currentResortID', TRUE);
        $result          = $this->data_dashboard_model->get_accident_probability_DB($currentResortID)->result();
        $lang_col        = 'name_' . $this->siteLang;

        $lbls = []; $data = []; $clrs = [];
        foreach ($result as $row) {
            $name      = (!empty($row->custom_name)) ? $row->custom_name : (isset($row->$lang_col) ? $row->$lang_col : $row->name_english);
            $diff      = max(1, (int)$row->id_difficulty);
            $cond      = max(0, min(100, (int)$row->slope_condition));
            $base      = $diff * 20;
            $risk      = min((int)round($base + $base * (100 - $cond) / 200), 100);
            $lbls[]    = $name;
            $data[]    = $risk;
            $clrs[]    = $risk >= 60 ? '#ef4444' : ($risk >= 30 ? '#f97316' : '#22c55e');
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'labels' => $lbls, 'data' => $data, 'colors' => $clrs]);
    }

    public function get_revenue_trend_chart() {
        $currentResortID = (int)$this->input->post('currentResortID', TRUE);
        $trend           = $this->data_dashboard_model->get_revenue_trend_DB($currentResortID, 14);

        $dates  = array_column($trend, 'date');
        $totals = array_column($trend, 'total');
        $skipass = array_column($trend, 'skipass');
        $other   = array_map(function($row) { return $row['total'] - $row['skipass']; }, $trend);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'dates' => $dates, 'totals' => $totals, 'skipass' => $skipass, 'other' => $other]);
    }

    public function get_cost_trend_chart() {
        $currentResortID = (int)$this->input->post('currentResortID', TRUE);
        $trend           = $this->data_dashboard_model->get_cost_trend_DB($currentResortID, 14);

        $dates    = array_column($trend, 'date');
        $upkeep   = array_column($trend, 'upkeep');
        $salaries = array_column($trend, 'salaries');
        $expenses = array_column($trend, 'expenses');
        $purchases = array_column($trend, 'purchases');

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'dates' => $dates, 'upkeep' => $upkeep, 'salaries' => $salaries, 'expenses' => $expenses, 'purchases' => $purchases]);
    }
}
