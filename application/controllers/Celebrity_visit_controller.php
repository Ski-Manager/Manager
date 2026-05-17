<?php
/**
 * Celebrity_visit_controller
 *
 * Read-only dashboard that shows the history of VIP / celebrity visit events
 * for the player's resort.
 */
class Celebrity_visit_controller extends CI_Controller {

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
        $this->load->model('celebrity_visit_model');
    }

    /**
     * index    Celebrity visits history page.
     */
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->celebrity_visit_model->ensure_table_exists();
        $visits = $this->celebrity_visit_model->get_recent_visits_DB($currentResortID);

        $data = [];
        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'celebrity_visit';
        $data['visits']          = $visits->result();
        $data['history_days']    = CELEBRITY_VISIT_HISTORY_DAYS;
        $data['rep_good_slopes'] = CELEBRITY_REP_GOOD_SLOPES;
        $data['rep_base']        = CELEBRITY_REP_BASE;
        $data['rep_lift_fail']   = CELEBRITY_REP_LIFT_FAIL;
        $data['visit_chance']    = CELEBRITY_VISIT_CHANCE;

        $this->load->view('templates/default', $data);
    }
}
