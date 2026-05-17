<?php
/**
 * Retail_controller
 *
 * Manages the Retail & Amenities feature:
 *   - Enable / disable each shop type
 *   - Set stock level (1–5)
 *   - Choose pricing strategy (budget / standard / premium)
 *   - Toggle seasonal items for relevant shop types
 */
class Retail_controller extends CI_Controller {

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
        $this->load->model('retail_model');
    }

    /**
     * index    Retail & Amenities management page.
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
        $data['main_content']    = 'retail';

        $data['shops']       = $this->retail_model->get_all_shops_DB($currentResortID);
        $data['shop_types']  = Retail_model::shop_types();
        $data['stock_min']   = RETAIL_STOCK_MIN;
        $data['stock_max']   = RETAIL_STOCK_MAX;

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves retail shop settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('retail_form')) {
            redirect('retail_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $valid_types      = Retail_model::shop_types();
        $valid_strategies = ['budget', 'standard', 'premium'];
        $all_saved        = true;

        foreach ($valid_types as $shop_type) {
            $enabled          = ($this->input->post('enabled_'  . $shop_type, TRUE) == '1') ? 1 : 0;
            $stock_level      = (int)$this->input->post('stock_'   . $shop_type, TRUE);
            $pricing_strategy = $this->input->post('pricing_'  . $shop_type, TRUE);
            $seasonal_items   = ($this->input->post('seasonal_' . $shop_type, TRUE) == '1') ? 1 : 0;

            // Validate stock level
            $stock_level = max(RETAIL_STOCK_MIN, min(RETAIL_STOCK_MAX, $stock_level));

            // Validate pricing strategy
            if (!in_array($pricing_strategy, $valid_strategies, TRUE)) {
                $pricing_strategy = 'standard';
            }

            $saved = $this->retail_model->save_shop_DB(
                $currentResortID,
                $shop_type,
                $enabled,
                $stock_level,
                $pricing_strategy,
                $seasonal_items
            );

            if (!$saved) {
                $all_saved = false;
            }
        }

        $this->session->set_flashdata('infoMessage', $all_saved ? 'retail_settings_saved' : 'retail_save_error');
        redirect('retail_controller');
    }
}
