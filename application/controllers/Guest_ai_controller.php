<?php
/**
 * Guest_ai_controller
 *
 * Displays the data-driven guest AI analysis page.
 * Shows each slope with a breakdown of the five factors guests use when
 * choosing where to ski: difficulty, snow quality, crowd level, lift speed
 * and ticket price.
 */
class Guest_ai_controller extends CI_Controller {

    private string $siteLang;

    public function __construct() {
        parent::__construct();

        $ci =& get_instance();
        $this->siteLang = $ci->session->userdata('site_lang') ?: 'english';
        $this->session->set_userdata('site_lang', $this->siteLang);

        $ci->lang->load('home',       $this->siteLang);
        $ci->lang->load('login_form', $this->siteLang);
        $ci->lang->load('navbar',     $this->siteLang);
        $ci->lang->load('slope',      $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('guest_ai_model');
    }

    /**
     * index    Guest AI analysis dashboard
     */
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $scores_result = $this->guest_ai_model->get_scores_for_resort($currentResortID);
        $scores        = ($scores_result->num_rows() > 0) ? $scores_result->result() : [];

        $data = [
            'currentUserID'   => $currentUserID,
            'currentResortID' => $currentResortID,
            'main_content'    => 'guest_ai',
            'scores'          => $scores,
        ];

        $this->load->view('templates/default', $data);
    }
}
