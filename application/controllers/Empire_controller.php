<?php
/**
 * Empire_controller
 *
 * Handles the Multi-Mountain Ownership (Resort Empire) feature.
 * Players can purchase subsidiary resorts (nearby resort, glacier resort,
 * budget ski hill) to build an empire with shared marketing and finances.
 */
class Empire_controller extends CI_Controller {

    private $siteLang;

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $this->siteLang = $ci->session->userdata('site_lang');
        } else {
            $this->siteLang = 'english';
            $this->session->set_userdata('site_lang', $this->siteLang);
        }

        $ci->lang->load('home',   $this->siteLang);
        $ci->lang->load('login_form', $this->siteLang);
        $ci->lang->load('navbar', $this->siteLang);
        $ci->lang->load('empire', $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('empire_model');
    }

    /**
     * index    Displays the empire management page.
     */
    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $user_activated = $this->users_model->check_account_activated($currentUserID);

        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resultResort    = $this->resort_model->display_resort_info_DB($currentResortID);

        if ($resultResort->num_rows() == 0) {
            $this->session->set_flashdata('error', $this->lang->line('empire')['no_resort']);
            redirect('resort_controller');
        }

        $resortRow = $resultResort->row();

        // Owned subsidiaries
        $subsidiaries_result = $this->empire_model->get_subsidiaries_DB($currentResortID);
        $subsidiaries        = $subsidiaries_result->result();

        // Aggregate empire stats
        $stats = $this->empire_model->get_empire_stats_DB($currentResortID);

        // Calculate combined marketing bonus (product of all individual bonuses)
        $combined_marketing_bonus = 1.00;
        foreach ($subsidiaries as $sub) {
            $combined_marketing_bonus *= (float)$sub->marketing_bonus;
        }

        // Static catalogue of purchasable types
        $catalogue = Empire_model::get_subsidiary_type_catalogue();

        // ── Franchise Mode data ──────────────────────────────────────────────
        $branding        = $this->empire_model->get_franchise_branding_DB($currentResortID);
        $brand_tiers     = Empire_model::get_brand_tiers();
        $shared_staff    = $this->empire_model->get_all_shared_staff_DB($currentResortID);
        $cross_promos    = $this->empire_model->get_active_cross_promos_DB($currentResortID);
        $promo_catalogue = Empire_model::get_cross_promo_catalogue();
        $budget_history  = $this->empire_model->get_budget_transfers_DB($currentResortID);

        $data['resort_name']              = htmlspecialchars($resortRow->resort_name, ENT_QUOTES, 'UTF-8');
        $data['resort_cash']              = (int)$resortRow->cash;
        $data['subsidiaries']             = $subsidiaries;
        $data['catalogue']                = $catalogue;
        $data['stats_total_subsidiaries'] = $stats ? (int)$stats->total_subsidiaries : 0;
        $data['stats_total_daily_rev']    = $stats ? (int)$stats->total_daily_revenue : 0;
        $data['combined_marketing_bonus'] = round($combined_marketing_bonus, 2);
        $data['currentResortID']          = $currentResortID;
        $data['feedback_msg']             = $this->session->flashdata('empire_msg');

        // Franchise
        $data['branding']        = $branding;
        $data['brand_tiers']     = $brand_tiers;
        $data['shared_staff']    = $shared_staff;
        $data['cross_promos']    = $cross_promos;
        $data['promo_catalogue'] = $promo_catalogue;
        $data['budget_history']  = $budget_history;

        $data['main_content'] = 'empire';
        $this->load->view('templates/default', $data);
    }

    /**
     * purchase     Handles the acquisition of a subsidiary resort.
     *              Accepts POST: subsidiary_type, subsidiary_name
     */
    public function purchase() {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resultResort    = $this->resort_model->display_resort_info_DB($currentResortID);

        if ($resultResort->num_rows() == 0) {
            redirect('resort_controller');
        }

        $resortRow = $resultResort->row();
        $type      = $this->input->post('subsidiary_type', TRUE);
        $name      = trim($this->input->post('subsidiary_name', TRUE));

        $catalogue = Empire_model::get_subsidiary_type_catalogue();

        // Validate type
        if (!array_key_exists($type, $catalogue)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['purchase_invalid_type']);
            redirect('empire_controller');
        }

        // Validate name: only Unicode letters, numbers, spaces, dashes
        if (empty($name) || !preg_match('/^[\p{L}\p{N} \-]{1,100}$/u', $name)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['purchase_invalid_name']);
            redirect('empire_controller');
        }

        $def           = $catalogue[$type];
        $purchase_price = $def['purchase_price'];
        $daily_revenue  = $def['daily_revenue'];
        $marketing_bonus = $def['marketing_bonus'];

        // Check sufficient funds
        if ((int)$resortRow->cash < $purchase_price) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['purchase_insufficient_funds']);
            redirect('empire_controller');
        }

        $result = $this->empire_model->purchase_subsidiary_DB(
            $currentResortID,
            $type,
            $name,
            $purchase_price,
            $daily_revenue,
            $marketing_bonus
        );

        if ($result) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['purchase_success']);
        } else {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['purchase_failed']);
        }

        redirect('empire_controller');
    }

    // =========================================================================
    // Franchise Mode actions
    // =========================================================================

    /**
     * set_branding     Updates the franchise brand name and tier.
     *                  POST: brand_name, brand_tier
     */
    public function set_branding() {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resultResort    = $this->resort_model->display_resort_info_DB($currentResortID);

        if ($resultResort->num_rows() == 0) {
            redirect('resort_controller');
        }

        $resortRow  = $resultResort->row();
        $brand_name = trim($this->input->post('brand_name', TRUE));
        $brand_tier = (int)$this->input->post('brand_tier', TRUE);
        $brand_tiers = Empire_model::get_brand_tiers();

        if (empty($brand_name) || !preg_match('/^[\p{L}\p{N} \-]{1,100}$/u', $brand_name)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_brand_name']);
            redirect('empire_controller');
        }

        if (!array_key_exists($brand_tier, $brand_tiers)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_tier']);
            redirect('empire_controller');
        }

        $def          = $brand_tiers[$brand_tier];
        $upgrade_cost = (int)$def['upgrade_cost'];

        // Check the player can afford the tier upgrade cost (tier 1 is free)
        if ($upgrade_cost > 0 && (int)$resortRow->cash < $upgrade_cost) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_insufficient_funds']);
            redirect('empire_controller');
        }

        // Deduct upgrade cost if applicable
        if ($upgrade_cost > 0) {
            $this->db->set('cash', 'cash - ' . $upgrade_cost, FALSE)
                     ->where('id_resort', $currentResortID)
                     ->update('game_resorts');
        }

        $result = $this->empire_model->set_franchise_branding_DB(
            $currentResortID,
            $brand_name,
            $brand_tier,
            (float)$def['branding_bonus']
        );

        if ($result) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_branding_updated']);
        } else {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_branding_failed']);
        }

        redirect('empire_controller');
    }

    /**
     * share_staff      Assigns shared staff to a subsidiary resort.
     *                  POST: id_subsidiary, shared_staff_count
     */
    public function share_staff() {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $id_subsidiary       = (int)$this->input->post('id_subsidiary', TRUE);
        $shared_staff_count  = (int)$this->input->post('shared_staff_count', TRUE);

        // Clamp to valid range 0–10
        $shared_staff_count = max(0, min(10, $shared_staff_count));

        // Verify the subsidiary belongs to this resort
        $owns = $this->db
            ->where('id_subsidiary', $id_subsidiary)
            ->where('id_resort',     $currentResortID)
            ->count_all_results('game_empire_subsidiaries');

        if ($owns === 0) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_subsidiary']);
            redirect('empire_controller');
        }

        // Staff bonus: each shared staff member adds 1 % (up to +10 %)
        $staff_bonus = round(1 + $shared_staff_count * 0.01, 2);

        $result = $this->empire_model->set_shared_staff_DB($id_subsidiary, $shared_staff_count, $staff_bonus);

        if ($result) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_staff_updated']);
        } else {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_staff_failed']);
        }

        redirect('empire_controller');
    }

    /**
     * transfer_budget  Moves funds between the main resort and a subsidiary.
     *                  POST: id_subsidiary, amount, direction
     */
    public function transfer_budget() {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resultResort    = $this->resort_model->display_resort_info_DB($currentResortID);

        if ($resultResort->num_rows() == 0) {
            redirect('resort_controller');
        }

        $resortRow     = $resultResort->row();
        $id_subsidiary = (int)$this->input->post('id_subsidiary', TRUE);
        $amount        = (int)$this->input->post('amount', TRUE);
        $direction     = $this->input->post('direction', TRUE);

        if ($amount <= 0) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_amount']);
            redirect('empire_controller');
        }

        if (!in_array($direction, ['to_subsidiary', 'from_subsidiary'], TRUE)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_direction']);
            redirect('empire_controller');
        }

        // Verify subsidiary belongs to this resort
        $subResult = $this->db
            ->select('*')
            ->from('game_empire_subsidiaries')
            ->where('id_subsidiary', $id_subsidiary)
            ->where('id_resort',     $currentResortID)
            ->get();

        if ($subResult->num_rows() == 0) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_subsidiary']);
            redirect('empire_controller');
        }

        $subRow = $subResult->row();

        // Funds check
        if ($direction === 'to_subsidiary' && (int)$resortRow->cash < $amount) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_insufficient_funds']);
            redirect('empire_controller');
        }

        if ($direction === 'from_subsidiary' && (int)$subRow->daily_revenue < $amount) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_insufficient_subsidiary_funds']);
            redirect('empire_controller');
        }

        $result = $this->empire_model->transfer_budget_DB($currentResortID, $id_subsidiary, $amount, $direction);

        if ($result) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_transfer_success']);
        } else {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_transfer_failed']);
        }

        redirect('empire_controller');
    }

    /**
     * launch_cross_promo   Launches a cross-promotional campaign across all empire resorts.
     *                      POST: promo_name, promo_type
     */
    public function launch_cross_promo() {
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resultResort    = $this->resort_model->display_resort_info_DB($currentResortID);

        if ($resultResort->num_rows() == 0) {
            redirect('resort_controller');
        }

        $resortRow       = $resultResort->row();
        $promo_name      = trim($this->input->post('promo_name', TRUE));
        $promo_type      = $this->input->post('promo_type', TRUE);
        $promo_catalogue = Empire_model::get_cross_promo_catalogue();

        if (empty($promo_name) || !preg_match('/^[\p{L}\p{N} \-]{1,100}$/u', $promo_name)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_promo_name']);
            redirect('empire_controller');
        }

        if (!array_key_exists($promo_type, $promo_catalogue)) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_invalid_promo_type']);
            redirect('empire_controller');
        }

        $def  = $promo_catalogue[$promo_type];
        $cost = (int)$def['cost'];

        if ((int)$resortRow->cash < $cost) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_insufficient_funds']);
            redirect('empire_controller');
        }

        $result = $this->empire_model->launch_cross_promo_DB(
            $currentResortID,
            $promo_name,
            $promo_type,
            $cost,
            (float)$def['guest_bonus'],
            (int)$def['duration_days']
        );

        if ($result) {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_promo_launched']);
        } else {
            $this->session->set_flashdata('empire_msg', $this->lang->line('empire')['franchise_promo_failed']);
        }

        redirect('empire_controller');
    }
}
