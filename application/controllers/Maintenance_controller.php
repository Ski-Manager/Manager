<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Maintenance_controller
 *
 * Handles both site-level maintenance/closed pages (HTTP 503) and the
 * in-game Maintenance Depth feature (preventive maintenance plans for lifts).
 */
class Maintenance_controller extends CI_Controller{
    
   public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('maintenance',$siteLang);
        $ci->lang->load('building',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $this->load->model('maintenance_model');
    }
  
    public function maintenance() {
      $this->output->set_status_header('503'); 
      $data['main_content'] = 'maintenance';
        $this->load->view('templates/maintenance_tpl',$data); 
    }
    
    public function closed() {
            
        $this->output->set_status_header('503'); 
        $origin  = $this->input->get('origin', TRUE);
        $data['beta_link'] = '<a href="'.base_url().'beta_controller?origin='.$origin.'">';
        $data['origin'] = $origin;
        $data['main_content'] = 'closed';
        $this->load->view('templates/maintenance_tpl',$data); 
    }

    /**
     * index    Maintenance Depth management page (requires login).
     */
    public function index($data = NULL) {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('maintenance_depth_model');
        $this->load->model('item_model');

        $data = $data ?? [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash && empty($data['infoMessage'])) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'maintenance_depth';

        $settings = $this->maintenance_depth_model->get_settings_DB($currentResortID);
        $data['maintenance_plan'] = $settings->maintenance_plan;

        // Count open lifts (for cost preview)
        $open_lifts_count = (int)$this->db
            ->where('id_resort', $currentResortID)
            ->where('id_status', '1')
            ->count_all_results('game_created_lifts');
        $data['open_lifts_count'] = $open_lifts_count;

        // Staff skill summary
        $data['avg_mechanic_efficiency'] = round(
            $this->maintenance_depth_model->get_avg_liftmechanic_efficiency_DB($currentResortID)
        );

        // Constants passed to view
        $data['maint_standard_cost']              = MAINT_PLAN_STANDARD_COST_PER_LIFT;
        $data['maint_preventive_cost']            = MAINT_PLAN_PREVENTIVE_COST_PER_LIFT;
        $data['maint_standard_repair_discount']   = (int)(MAINT_PLAN_STANDARD_REPAIR_DISCOUNT * 100);
        $data['maint_preventive_failure_reduc']   = (int)(MAINT_PLAN_PREVENTIVE_FAILURE_REDUCTION * 100);
        $data['maint_preventive_repair_discount'] = (int)(MAINT_PLAN_PREVENTIVE_REPAIR_DISCOUNT * 100);
        $data['maint_staff_max_discount']         = (int)(MAINT_STAFF_MAX_REPAIR_DISCOUNT * 100);
        $data['maint_base_failure']               = MAINT_BASE_FAILURE_CHANCE;

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves maintenance plan from the form POST (requires login).
     */
    public function save() {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('maintenance_depth_model');

        if (!$this->input->post('maintenance_depth_form')) {
            redirect('maintenance_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $valid_plans = ['basic', 'standard', 'preventive'];
        $plan = $this->input->post('maintenance_plan', TRUE);

        if (!in_array($plan, $valid_plans, TRUE)) {
            $this->session->set_flashdata('infoMessage', 'maint_depth_invalid_plan');
            redirect('maintenance_controller');
            return;
        }

        $saved = $this->maintenance_depth_model->save_settings_DB($currentResortID, $plan);

        $this->session->set_flashdata('infoMessage', $saved ? 'maint_depth_saved' : 'maint_depth_save_error');
        redirect('maintenance_controller');
    }
    
}
   