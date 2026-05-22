<?php
/**
 * Visitor_needs_controller
 *
 * Displays the visitor needs dashboard: hunger, fatigue, warmth and fun level.
 * Scores are recalculated each night by the nightly run and shown here
 * with explanations of what factors influence each need.
 */
class Visitor_needs_controller extends CI_Controller {

    private string $siteLang;

    public function __construct() {
        parent::__construct();

        $ci =& get_instance();
        $this->siteLang = $ci->session->userdata('site_lang') ?: 'english';
        $this->session->set_userdata('site_lang', $this->siteLang);

        $ci->lang->load('home',           $this->siteLang);
        $ci->lang->load('login_form',     $this->siteLang);
        $ci->lang->load('navbar',         $this->siteLang);
        $ci->lang->load('visitor_needs',  $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('visitor_needs_model');
    }

    /**
     * index    Visitor needs dashboard
     */
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $needs = $this->visitor_needs_model->get_or_init_DB($currentResortID);

        $data = [
            'currentUserID'      => $currentUserID,
            'currentResortID'    => $currentResortID,
            'main_content'       => 'visitor_needs',
            'hunger_score'       => (float)$needs->hunger_score,
            'fatigue_score'      => (float)$needs->fatigue_score,
            'warmth_score'       => (float)$needs->warmth_score,
            'fun_score'          => (float)$needs->fun_score,
            'needs_satisfaction' => (float)$needs->needs_satisfaction,
            'revenue_multiplier' => $this->visitor_needs_model->get_revenue_multiplier($currentResortID),
            'updated_at'         => $needs->updated_at,
        ];

        $this->load->view('templates/default', $data);
    }
}
