<?php
/**
 * Microclimate_controller   Displays the Microclimate, Altitude, Slope Aspect and Wind Risk page.
 *                            Also handles paid edits to Altitude and Slope Aspect.
 */
class Microclimate_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }

        $ci->lang->load('home',   $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('resort', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
    }

    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID);

        if ($resort_info && $resort_info->num_rows() > 0) {
            $resort_row = $resort_info->row();
            $data['altitude']  = isset($resort_row->altitude) ? $resort_row->altitude  : 'medium';
            $data['aspect']    = isset($resort_row->aspect)   ? $resort_row->aspect    : 'north';
            $change_count      = isset($resort_row->microclimate_change_count) ? (int)$resort_row->microclimate_change_count : 0;
            $data['current_cash'] = isset($resort_row->cash) ? (int)$resort_row->cash : 0;
        } else {
            $data['altitude']  = 'medium';
            $data['aspect']    = 'north';
            $change_count      = 0;
            $data['current_cash'] = 0;
        }

        $data['change_count'] = $change_count;
        // Cost for next change: first change (count == 0) is free (0 €); after that: (count + 1) × base
        $data['next_change_cost'] = ($change_count === 0) ? 0 : ($change_count + 1) * MICROCLIMATE_CHANGE_BASE_COST;
        $data['current_resort_id'] = $currentResortID;

        $data['main_content'] = 'microclimate';
        $this->load->view('templates/default', $data);
    }

    /**
     * update   Handles the POST request to change Altitude and Slope Aspect.
     *           First change (change_count == 0) is free.
     *           Every subsequent change costs (change_count + 1) * MICROCLIMATE_CHANGE_BASE_COST.
     */
    public function update() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        // Security: only allow POST
        if (!$this->input->post('update_microclimate')) {
            redirect('microclimate_controller');
        }

        $allowed_altitudes = ['low', 'medium', 'high'];
        $allowed_aspects   = ['north', 'south', 'east', 'west'];
        $altitude = $this->input->post('resort_altitude', TRUE);
        $aspect   = $this->input->post('resort_aspect',   TRUE);
        if (!in_array($altitude, $allowed_altitudes)) $altitude = 'medium';
        if (!in_array($aspect,   $allowed_aspects))   $aspect   = 'north';

        $result = $this->resort_model->update_microclimate($currentResortID, $altitude, $aspect);

        if ($result === 'no_cash') {
            $this->session->set_flashdata('microclimate_error', 'no_cash');
        } elseif ($result === false) {
            $this->session->set_flashdata('microclimate_error', 'failed');
        } else {
            $this->session->set_flashdata('microclimate_success', true);
        }

        redirect('microclimate_controller');
    }
}
