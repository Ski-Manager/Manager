<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ski_school_controller
 *
 * Allows logged-in players to run ski-school sessions that generate revenue
 * and reputation for their resort.
 */
class Ski_school_controller extends CI_Controller {

    private $siteLang;

    /** Maximum sessions a resort may run per in-game day. */
    const MAX_SESSIONS_PER_DAY = 5;

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $this->siteLang = $ci->session->userdata('site_lang');
        } else {
            $this->siteLang = 'english';
            $this->session->set_userdata('site_lang', $this->siteLang);
        }

        $ci->lang->load('home',       $this->siteLang);
        $ci->lang->load('login_form', $this->siteLang);
        $ci->lang->load('navbar',     $this->siteLang);
        $ci->lang->load('ski_school', $this->siteLang);
        $ci->lang->load('logs',       $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('ski_school_model');
        $this->load->model('logs_model');
    }

    // -----------------------------------------------------------------------
    // index  –  display the ski-school management page
    // -----------------------------------------------------------------------
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_activated = $this->users_model->check_account_activated($currentUserID);
        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $L = $this->lang->line('ski_school');

        $lesson_types      = $this->ski_school_model->get_all_lesson_types();
        $history           = $this->ski_school_model->get_history($currentResortID, 14);
        $totals            = $this->ski_school_model->get_totals($currentResortID);
        $sessions_today    = $this->ski_school_model->count_todays_sessions($currentResortID);
        $can_run_session   = ($sessions_today < self::MAX_SESSIONS_PER_DAY);

        $name_col = 'name_' . $this->siteLang;
        $desc_col = 'description_' . $this->siteLang;

        $data['title']            = '<h2><i class="fa-solid fa-chalkboard-user me-2"></i>' . htmlspecialchars($L['title'], ENT_QUOTES, 'UTF-8') . '</h2>';
        $data['intro']            = '<p>' . htmlspecialchars($L['intro'], ENT_QUOTES, 'UTF-8') . '</p>';
        $data['lesson_types']     = $lesson_types;
        $data['history']          = $history;
        $data['totals']           = $totals;
        $data['sessions_today']   = $sessions_today;
        $data['can_run_session']  = $can_run_session;
        $data['max_per_day']      = self::MAX_SESSIONS_PER_DAY;
        $data['lang']             = $L;
        $data['name_col']         = $name_col;
        $data['desc_col']         = $desc_col;
        $data['session_success']  = $this->session->flashdata('session_success');
        $data['session_error']    = $this->session->flashdata('session_error');
        $data['main_content']     = 'ski_school';

        $this->load->view('templates/default', $data);
    }

    // -----------------------------------------------------------------------
    // run_session  –  POST handler: run a ski lesson session
    // -----------------------------------------------------------------------
    public function run_session() {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('ski_school_controller');
            return;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (!$currentResortID) {
            redirect('ski_school_controller');
            return;
        }

        $L = $this->lang->line('ski_school');

        // Daily session cap check
        $sessions_today = $this->ski_school_model->count_todays_sessions($currentResortID);
        if ($sessions_today >= self::MAX_SESSIONS_PER_DAY) {
            $this->session->set_flashdata('session_error', $L['max_sessions_reached']);
            redirect('ski_school_controller');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id_lesson_type', 'Lesson type',      'trim|required|integer|greater_than[0]');
        $this->form_validation->set_rules('guests_enrolled', 'Guests enrolled', 'trim|required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('session_error', validation_errors());
            redirect('ski_school_controller');
            return;
        }

        $id_lesson_type  = (int)$this->input->post('id_lesson_type', TRUE);
        $guests_enrolled = (int)$this->input->post('guests_enrolled', TRUE);

        $result = $this->ski_school_model->run_session($currentResortID, $id_lesson_type, $guests_enrolled);

        if (!$result['ok']) {
            $err_key = 'error_' . ($result['error'] ?? 'generic');
            $err_msg = $L[$err_key] ?? $L['error_generic'];
            $this->session->set_flashdata('session_error', $err_msg);
            redirect('ski_school_controller');
            return;
        }

        // Activity log
        $log_type = $L['log_type'] ?? 'Ski School';
        $log_data = number_format($result['revenue']) . ' €';
        if ($result['rep_earned'] > 0) {
            $log_data .= ', +' . $result['rep_earned'] . ' rep';
        }

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

        $this->session->set_flashdata('session_success', [
            'revenue'    => $result['revenue'],
            'rep_earned' => $result['rep_earned'],
        ]);
        redirect('ski_school_controller');
    }
}
