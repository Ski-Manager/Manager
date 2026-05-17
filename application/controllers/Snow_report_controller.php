<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Snow_report_controller
 *
 * Allows logged-in players to publish a daily snow conditions report for
 * their resort. Publishing a quality report awards a reputation bonus.
 */
class Snow_report_controller extends CI_Controller {

    private $siteLang;

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $this->siteLang = $ci->session->userdata('site_lang');
        } else {
            $this->siteLang = 'english';
            $this->session->set_userdata('site_lang', $this->siteLang);
        }

        $ci->lang->load('home',        $this->siteLang);
        $ci->lang->load('login_form',  $this->siteLang);
        $ci->lang->load('navbar',      $this->siteLang);
        $ci->lang->load('snow_report', $this->siteLang);
        $ci->lang->load('logs',        $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('snow_report_model');
        $this->load->model('weather_model');
        $this->load->model('logs_model');
    }

    // -----------------------------------------------------------------------
    // index  –  display the snow-report page
    // -----------------------------------------------------------------------
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_activated = $this->users_model->check_account_activated($currentUserID);
        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $L = $this->lang->line('snow_report');

        $today_report   = $this->snow_report_model->get_todays_report($currentResortID);
        $latest_report  = $this->snow_report_model->get_latest_report($currentResortID);
        $history        = $this->snow_report_model->get_history($currentResortID, 14);

        // Fetch actual game data to pre-populate the form
        $resort_row      = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $game_snow_level = $resort_row ? max(0, min(500, (int)$resort_row->snow_level)) : 0;

        $game_fresh_snow  = 0;
        $game_weather     = null;
        $game_weather_name = null;
        $today_forecast   = $this->weather_model->select_weather_forecast(gmdate('Y-m-d'));
        if ($today_forecast) {
            $forecast_row = $today_forecast->row();
            $cond_result  = $this->weather_model->select_weather_conditions($forecast_row->id_condition);
            if ($cond_result && $cond_result->num_rows() > 0) {
                $game_weather      = $cond_result->row();
                $game_fresh_snow   = max(0, min(200, (int)$game_weather->snow_level));
                $game_weather_name = ($this->siteLang === 'french')
                    ? $game_weather->name_french
                    : $game_weather->name_english;
            }
        }

        $data['title']            = '<h2><i class="fa-solid fa-snowflake me-2"></i>' . htmlspecialchars($L['title'], ENT_QUOTES, 'UTF-8') . '</h2>';
        $data['intro']            = '<p>' . htmlspecialchars($L['intro'], ENT_QUOTES, 'UTF-8') . '</p>';
        $data['today_report']     = $today_report;
        $data['latest_report']    = $latest_report;
        $data['history']          = $history;
        $data['game_snow_level']   = $game_snow_level;
        $data['game_fresh_snow']   = $game_fresh_snow;
        $data['game_weather']      = $game_weather;
        $data['game_weather_name'] = $game_weather_name;
        $data['lang']             = $L;
        $data['publish_success']  = $this->session->flashdata('publish_success');
        $data['publish_error']    = $this->session->flashdata('publish_error');
        $data['main_content']     = 'snow_report';

        $this->load->view('templates/default', $data);
    }

    // -----------------------------------------------------------------------
    // publish  –  POST handler: save today's snow report
    // -----------------------------------------------------------------------
    public function publish() {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('snow_report_controller');
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            redirect('snow_report_controller');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('snow_depth_cm',  'Snow depth',     'trim|integer|greater_than_equal_to[0]|less_than_equal_to[500]');
        $this->form_validation->set_rules('fresh_snow_cm',  'Fresh snow',     'trim|integer|greater_than_equal_to[0]|less_than_equal_to[200]');
        $this->form_validation->set_rules('conditions',     'Conditions',     'trim|required|in_list[poor,fair,good,excellent]');
        $this->form_validation->set_rules('piste_coverage', 'Piste coverage', 'trim|integer|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('note',           'Note',           'trim|max_length[500]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('publish_error', validation_errors());
            redirect('snow_report_controller');
            return;
        }

        $report_data = [
            'snow_depth_cm'  => $this->input->post('snow_depth_cm', TRUE),
            'fresh_snow_cm'  => $this->input->post('fresh_snow_cm', TRUE),
            'conditions'     => $this->input->post('conditions', TRUE),
            'piste_coverage' => $this->input->post('piste_coverage', TRUE),
            'note'           => $this->input->post('note', TRUE),
        ];

        $result = $this->snow_report_model->publish($currentResortID, $report_data);

        $L = $this->lang->line('snow_report');

        if ($result['already_published']) {
            $this->session->set_flashdata('publish_error', $L['already_published_today']);
            redirect('snow_report_controller');
            return;
        }

        if (!$result['ok']) {
            $this->session->set_flashdata('publish_error', $L['publish_error']);
            redirect('snow_report_controller');
            return;
        }

        // Log the action
        $log_type = $L['log_type'] ?? 'Snow Report';
        $rep_text = $result['rep_bonus'] > 0
            ? ' +' . $result['rep_bonus'] . ' rep'
            : '';
        $log_data = ucfirst($report_data['conditions']) . $rep_text;

        $this->logs_model->call_notification_DB([
            'id_player' => $currentUserID,
            'type'      => $log_type,
            'data'      => $log_data,
        ]);
        log_user_action([
            'id_player' => $currentUserID,
            'type'      => $log_type,
            'data'      => $log_data,
        ]);

        $this->session->set_flashdata('publish_success', $result['rep_bonus']);
        redirect('snow_report_controller');
    }
}
