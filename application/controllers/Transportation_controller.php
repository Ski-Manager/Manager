<?php
/**
 * Transportation_controller
 *
 * Manages the Accessibility & Transportation feature:
 *   - Shuttle / tram / bus level between resort sections
 *   - Ski storage facility
 *   - Gondola link between sections
 */
class Transportation_controller extends CI_Controller {

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
        $this->load->model('transportation_model');
    }

    /**
     * index    Accessibility & Transportation management page.
     */
    public function index($data = NULL) {
        $data = $data ?? [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash && empty($data['infoMessage'])) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'transportation';

        $settings = $this->transportation_model->get_settings_DB($currentResortID);

        $data['shuttle_level'] = (int)$settings->shuttle_level;
        $data['ski_storage']   = (int)$settings->ski_storage;
        $data['gondola_link']  = (int)$settings->gondola_link;

        $data['shuttle_max_level']       = TRANSPORT_SHUTTLE_MAX_LEVEL;
        $data['shuttle_daily_costs']     = TRANSPORT_SHUTTLE_DAILY_COST;
        $data['shuttle_family_rep']      = TRANSPORT_SHUTTLE_FAMILY_REP;
        $data['shuttle_pro_rep']         = TRANSPORT_SHUTTLE_PRO_REP;
        $data['ski_storage_daily_cost']  = TRANSPORT_SKI_STORAGE_DAILY_COST;
        $data['ski_storage_family_rep']  = TRANSPORT_SKI_STORAGE_FAMILY_REP;
        $data['gondola_daily_cost']      = TRANSPORT_GONDOLA_DAILY_COST;
        $data['gondola_pro_rep']         = TRANSPORT_GONDOLA_PRO_REP;
        $data['gondola_family_rep']      = TRANSPORT_GONDOLA_FAMILY_REP;
        $data['visitor_bonus_pct']       = (int)(TRANSPORT_VISITOR_BONUS_PER_LEVEL * 100);

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves transportation settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('transportation_form')) {
            redirect('transportation_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('shuttle_level', 'shuttle_level', 'trim|required|integer|greater_than_equal_to[0]|less_than_equal_to[' . TRANSPORT_SHUTTLE_MAX_LEVEL . ']');

        if ($this->form_validation->run() == FALSE) {
            $data['infoMessage'] = 'transport_invalid_settings';
            $this->index($data);
            return;
        }

        $shuttle_level = (int)$this->input->post('shuttle_level', TRUE);
        $ski_storage   = ($this->input->post('ski_storage',   TRUE) == '1') ? 1 : 0;
        $gondola_link  = ($this->input->post('gondola_link',  TRUE) == '1') ? 1 : 0;

        $saved = $this->transportation_model->save_settings_DB(
            $currentResortID,
            $shuttle_level,
            $ski_storage,
            $gondola_link
        );

        $this->session->set_flashdata('infoMessage', $saved ? 'transport_settings_saved' : 'transport_save_error');
        redirect('transportation_controller');
    }
}
