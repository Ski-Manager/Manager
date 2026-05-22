<?php
/**
 * Guest_skill_controller
 *
 * Displays the guest skill progression dashboard.
 * Shows the current skill distribution of the resort's visitors
 * and explains how skill level-ups are earned over seasons.
 */
class Guest_skill_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',       $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',     $siteLang);
        $ci->lang->load('building',   $siteLang);
        $ci->lang->load('logs',       $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('guest_skill_model');
    }

    /**
     * index     Guest skill progression overview page.
     */
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data = [];
        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'guest_skill';

        // Skill record (auto-initialised for new resorts)
        $skill = $this->guest_skill_model->get_or_init_DB($currentResortID);
        $data['beginner_pct']     = (int)$skill->beginner_pct;
        $data['intermediate_pct'] = (int)$skill->intermediate_pct;
        $data['advanced_pct']     = (int)$skill->advanced_pct;
        $data['seasons_played']   = (int)$skill->seasons_played;

        // Revenue multiplier for display
        $data['revenue_multiplier'] = $this->guest_skill_model->get_revenue_multiplier($currentResortID);

        // Constants for display
        $data['beginner_to_intermediate_rate'] = (int)(GUEST_SKILL_BEGINNER_TO_INTERMEDIATE_RATE * 100);
        $data['intermediate_to_advanced_rate'] = (int)(GUEST_SKILL_INTERMEDIATE_TO_ADVANCED_RATE * 100);
        $data['intermediate_revenue_bonus']    = (int)(GUEST_SKILL_INTERMEDIATE_REVENUE_BONUS * 100);
        $data['advanced_revenue_bonus']        = (int)(GUEST_SKILL_ADVANCED_REVENUE_BONUS * 100);

        $this->load->view('templates/default', $data);
    }
}
