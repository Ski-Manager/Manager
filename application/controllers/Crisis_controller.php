<?php

class Crisis_controller extends CI_Controller {

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
        $ci->lang->load('home', $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('logs', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('crisis_events_model');
    }

    /**
     * index     Displays all crisis events (active and resolved) for the current player
     */
    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated  = $this->users_model->check_account_activated($currentUserID);

        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);
        if ($checkIfResortExists->num_rows() == 0) {
            $this->session->set_flashdata('error', 'no_resort');
            redirect('resort_controller');
        }

        $active_events  = $this->crisis_events_model->get_active_crisis_events_DB($currentUserID);
        $all_events     = $this->crisis_events_model->get_all_crisis_events_DB($currentUserID);

        $data['title']          = '<h2>' . $this->lang->line('logs')['crisis_title'] . '</h2>';
        $data['intro']          = '<div>' . $this->lang->line('logs')['crisis_intro'] . '</div>';
        $data['active_events']  = $active_events->result();
        $data['all_events']     = $all_events->result();
        $data['resort_built']   = true;
        $data['main_content']   = 'crisis';
        $this->load->view('templates/default', $data);
    }

    /**
     * resolve     Marks a crisis event as resolved (AJAX or form POST)
     */
    public function resolve() {
        $currentUserID = $this->users_model->get_user_id();
        $id_crisis     = (int) $this->input->post('id_crisis');

        if ($id_crisis > 0) {
            $this->crisis_events_model->resolve_crisis_event_DB($id_crisis, $currentUserID);
        }
        redirect('emergency_controller');
    }
}
