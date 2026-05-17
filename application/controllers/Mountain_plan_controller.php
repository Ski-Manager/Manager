<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mountain_plan_controller
 *
 * Manages the Mountain Master Plan System:
 *   - Viewing the current plan and plan history
 *   - Creating / editing a draft plan
 *   - Submitting a plan for government review (costs MASTER_PLAN_SUBMISSION_COST)
 *   - Activating an approved plan
 *   - Revising an approved/active plan (costs money + reputation)
 *   - Deleting a draft plan
 */
class Mountain_plan_controller extends CI_Controller {

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
        $ci->lang->load('building', $siteLang);
        $ci->lang->load('logs', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('mountain_plan_model');
    }

    // -------------------------------------------------------------------------
    // index – overview page
    // -------------------------------------------------------------------------

    /**
     * index     Displays the Mountain Master Plan overview for the current resort.
     */
    public function index() {
        $data = [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'mountain_plan';

        // All plans for this resort (newest first)
        $data['all_plans'] = $this->mountain_plan_model->get_all_plans_DB($currentResortID);

        // Auto-approve submitted plans that have been waiting long enough
        $this->mountain_plan_model->check_approval_due_DB($currentResortID);

        // Expire active plans that have exceeded their duration
        $this->mountain_plan_model->check_expired_plans_DB($currentResortID);

        // Refresh plan list after potential auto-approval / expiry
        $data['all_plans'] = $this->mountain_plan_model->get_all_plans_DB($currentResortID);

        // Resort cash for UI affordability checks
        $resort_info        = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $data['resort_cash'] = $resort_info ? (int)$resort_info->cash : 0;

        // Constants for the view
        $data['submission_cost']   = number_format(MASTER_PLAN_SUBMISSION_COST, 0, ',', ' ');
        $data['revision_cost']     = number_format(MASTER_PLAN_REVISION_COST,   0, ',', ' ');
        $data['revision_rep']      = (int)MASTER_PLAN_REVISION_REP_PENALTY;
        $data['approval_days']     = (int)MASTER_PLAN_APPROVAL_DAYS;
        $data['duration_days']     = (int)MASTER_PLAN_DURATION_DAYS;
        $data['max_slopes']        = (int)MASTER_PLAN_MAX_SLOPES;
        $data['max_lifts']         = (int)MASTER_PLAN_MAX_LIFTS;
        $data['max_buildings']     = (int)MASTER_PLAN_MAX_BUILDINGS;

        $this->load->view('templates/default', $data);
    }

    // -------------------------------------------------------------------------
    // create / edit
    // -------------------------------------------------------------------------

    /**
     * create    Displays the "new plan" form.
     */
    public function create() {
        $data = [];

        $flash = $this->session->flashdata('infoMessage');
        if ($flash) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'mountain_plan_form';
        $data['form_action']     = 'save_new';
        $data['plan']            = null;

        $data['max_slopes']    = (int)MASTER_PLAN_MAX_SLOPES;
        $data['max_lifts']     = (int)MASTER_PLAN_MAX_LIFTS;
        $data['max_buildings'] = (int)MASTER_PLAN_MAX_BUILDINGS;

        $this->load->view('templates/default', $data);
    }

    /**
     * edit      Displays the edit form for an existing draft plan.
     *
     * @param int $id_master_plan
     */
    public function edit($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || $plan->status !== 'draft') {
            $this->session->set_flashdata('infoMessage', 'plan_not_editable');
            redirect('mountain_plan_controller');
            return;
        }

        $data = [];
        $flash = $this->session->flashdata('infoMessage');
        if ($flash) {
            $data['infoMessage'] = $flash;
        }

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'mountain_plan_form';
        $data['form_action']     = 'save_edit/' . (int)$id_master_plan;
        $data['plan']            = $plan;

        $data['max_slopes']    = (int)MASTER_PLAN_MAX_SLOPES;
        $data['max_lifts']     = (int)MASTER_PLAN_MAX_LIFTS;
        $data['max_buildings'] = (int)MASTER_PLAN_MAX_BUILDINGS;

        $this->load->view('templates/default', $data);
    }

    // -------------------------------------------------------------------------
    // save (POST handlers)
    // -------------------------------------------------------------------------

    /**
     * save_new     Handles the "create new plan" form submission (POST).
     */
    public function save_new() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $fields = $this->_extract_form_fields();
        if (!$this->_validate_fields($fields)) {
            $this->session->set_flashdata('infoMessage', 'plan_validation_error');
            redirect('mountain_plan_controller/create');
            return;
        }

        $result = $this->mountain_plan_model->create_plan_DB(
            $currentResortID,
            $fields['plan_name'],
            $fields['expansion_strategy'],
            $fields['environmental_notes'],
            $fields['zoning_limit_slopes'],
            $fields['zoning_limit_lifts'],
            $fields['zoning_limit_buildings']
        );

        if ($result) {
            $this->_log_action($currentUserID, $this->lang->line('building')['plan_log_created']);
            $this->session->set_flashdata('infoMessage', 'plan_created');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('mountain_plan_controller');
    }

    /**
     * save_edit    Handles the "edit plan" form submission (POST).
     *
     * @param int $id_master_plan
     */
    public function save_edit($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || $plan->status !== 'draft') {
            $this->session->set_flashdata('infoMessage', 'plan_not_editable');
            redirect('mountain_plan_controller');
            return;
        }

        $fields = $this->_extract_form_fields();
        if (!$this->_validate_fields($fields)) {
            $this->session->set_flashdata('infoMessage', 'plan_validation_error');
            redirect('mountain_plan_controller/edit/' . (int)$id_master_plan);
            return;
        }

        $result = $this->mountain_plan_model->update_plan_DB(
            (int)$id_master_plan,
            $currentResortID,
            $fields['plan_name'],
            $fields['expansion_strategy'],
            $fields['environmental_notes'],
            $fields['zoning_limit_slopes'],
            $fields['zoning_limit_lifts'],
            $fields['zoning_limit_buildings']
        );

        $this->session->set_flashdata('infoMessage', $result ? 'plan_saved' : 'bad_action');
        redirect('mountain_plan_controller');
    }

    // -------------------------------------------------------------------------
    // Status transitions
    // -------------------------------------------------------------------------

    /**
     * submit   Submits a draft plan for government review.
     *          Deducts MASTER_PLAN_SUBMISSION_COST from the resort's cash.
     *
     * @param int $id_master_plan
     */
    public function submit($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || $plan->status !== 'draft') {
            $this->session->set_flashdata('infoMessage', 'plan_not_submittable');
            redirect('mountain_plan_controller');
            return;
        }

        // Check the resort has enough cash
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        if (!$resort_info || (int)$resort_info->cash < MASTER_PLAN_SUBMISSION_COST) {
            $this->session->set_flashdata('infoMessage', 'plan_not_enough_cash');
            redirect('mountain_plan_controller');
            return;
        }

        $result = $this->mountain_plan_model->submit_plan_DB((int)$id_master_plan, $currentResortID);
        if ($result) {
            $this->_log_action($currentUserID, $this->lang->line('building')['plan_log_submitted']);
            $this->session->set_flashdata('infoMessage', 'plan_submitted');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('mountain_plan_controller');
    }

    /**
     * activate     Activates a government-approved plan.
     *
     * @param int $id_master_plan
     */
    public function activate($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || $plan->status !== 'approved') {
            $this->session->set_flashdata('infoMessage', 'plan_not_activatable');
            redirect('mountain_plan_controller');
            return;
        }

        $result = $this->mountain_plan_model->activate_plan_DB((int)$id_master_plan, $currentResortID);
        if ($result) {
            $this->_log_action($currentUserID, $this->lang->line('building')['plan_log_activated']);
            $this->session->set_flashdata('infoMessage', 'plan_activated');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('mountain_plan_controller');
    }

    /**
     * revise   Reverts an approved or active plan back to draft for amendment.
     *          Costs MASTER_PLAN_REVISION_COST and MASTER_PLAN_REVISION_REP_PENALTY.
     *
     * @param int $id_master_plan
     */
    public function revise($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || !in_array($plan->status, ['approved', 'active'], TRUE)) {
            $this->session->set_flashdata('infoMessage', 'plan_not_revisable');
            redirect('mountain_plan_controller');
            return;
        }

        // Check the resort has enough cash for the revision fee
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        if (!$resort_info || (int)$resort_info->cash < MASTER_PLAN_REVISION_COST) {
            $this->session->set_flashdata('infoMessage', 'plan_not_enough_cash');
            redirect('mountain_plan_controller');
            return;
        }

        $result = $this->mountain_plan_model->revise_plan_DB((int)$id_master_plan, $currentResortID);
        if ($result) {
            $this->_log_action($currentUserID, $this->lang->line('building')['plan_log_revised']);
            $this->session->set_flashdata('infoMessage', 'plan_revised');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('mountain_plan_controller');
    }

    /**
     * delete   Deletes a draft plan.
     *
     * @param int $id_master_plan
     */
    public function delete($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || $plan->status !== 'draft') {
            $this->session->set_flashdata('infoMessage', 'plan_not_deletable');
            redirect('mountain_plan_controller');
            return;
        }

        $result = $this->mountain_plan_model->delete_plan_DB((int)$id_master_plan, $currentResortID);
        $this->session->set_flashdata('infoMessage', $result ? 'plan_deleted' : 'bad_action');
        redirect('mountain_plan_controller');
    }

    /**
     * cancel_submission    Withdraws a submitted plan back to draft.
     *                      The submission fee is non-refundable.
     *
     * @param int $id_master_plan
     */
    public function cancel_submission($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan || $plan->status !== 'submitted') {
            $this->session->set_flashdata('infoMessage', 'plan_not_withdrawable');
            redirect('mountain_plan_controller');
            return;
        }

        $result = $this->mountain_plan_model->cancel_submission_DB((int)$id_master_plan, $currentResortID);
        if ($result) {
            $this->_log_action($currentUserID, $this->lang->line('building')['plan_log_withdrawn']);
            $this->session->set_flashdata('infoMessage', 'plan_withdrawn');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('mountain_plan_controller');
    }

    /**
     * duplicate    Creates a new draft plan by cloning an existing one.
     *
     * @param int $id_master_plan
     */
    public function duplicate($id_master_plan = null) {
        if ($id_master_plan === null || $id_master_plan === '') {
            redirect('mountain_plan_controller');
            return;
        }
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $plan = $this->mountain_plan_model->get_plan_by_id_DB((int)$id_master_plan, $currentResortID);
        if (!$plan) {
            $this->session->set_flashdata('infoMessage', 'bad_action');
            redirect('mountain_plan_controller');
            return;
        }

        $result = $this->mountain_plan_model->duplicate_plan_DB((int)$id_master_plan, $currentResortID);
        if ($result) {
            $this->_log_action($currentUserID, $this->lang->line('building')['plan_log_duplicated']);
            $this->session->set_flashdata('infoMessage', 'plan_duplicated');
        } else {
            $this->session->set_flashdata('infoMessage', 'bad_action');
        }
        redirect('mountain_plan_controller');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * _extract_form_fields     Reads and sanitises the POST fields for a plan form.
     *
     * @return array
     */
    private function _extract_form_fields() {
        return [
            'plan_name'              => trim($this->input->post('plan_name',              TRUE)),
            'expansion_strategy'     => trim($this->input->post('expansion_strategy',     TRUE)),
            'environmental_notes'    => trim($this->input->post('environmental_notes',    TRUE)),
            'zoning_limit_slopes'    => (int)$this->input->post('zoning_limit_slopes',    TRUE),
            'zoning_limit_lifts'     => (int)$this->input->post('zoning_limit_lifts',     TRUE),
            'zoning_limit_buildings' => (int)$this->input->post('zoning_limit_buildings', TRUE),
        ];
    }

    /**
     * _validate_fields     Validates the form fields for a plan.
     *
     * @param array $fields
     * @return bool
     */
    private function _validate_fields($fields) {
        if (empty($fields['plan_name'])           || strlen($fields['plan_name'])           > 100) return false;
        if (empty($fields['expansion_strategy']))   return false;
        if (empty($fields['environmental_notes']))  return false;
        if ($fields['zoning_limit_slopes']    < 1 || $fields['zoning_limit_slopes']    > MASTER_PLAN_MAX_SLOPES)    return false;
        if ($fields['zoning_limit_lifts']     < 1 || $fields['zoning_limit_lifts']     > MASTER_PLAN_MAX_LIFTS)     return false;
        if ($fields['zoning_limit_buildings'] < 1 || $fields['zoning_limit_buildings'] > MASTER_PLAN_MAX_BUILDINGS) return false;
        return true;
    }

    /**
     * _log_action  Writes a log entry for plan actions.
     *
     * @param int    $id_player
     * @param string $message
     */
    private function _log_action($id_player, $message) {
        $entry = [
            'id_player' => $id_player,
            'type'      => $this->lang->line('logs')['building'],
            'data'      => $message,
        ];
        $this->logs_model->call_notification_DB($entry);
        log_user_action($entry);
    }
}
