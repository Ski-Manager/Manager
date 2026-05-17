<?php
/**
 * Season_pass_controller
 *
 * Manages the Season Ski Pass feature:
 *   - Enable / disable season passes
 *   - Set the season pass price
 */
class Season_pass_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',     $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',   $siteLang);
        $ci->lang->load('building', $siteLang);
        $ci->lang->load('logs',     $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('season_pass_model');
    }

    /**
     * index    Season ski pass management page.
     */
    public function index($data = NULL) {
        // If called directly (not from save() on validation failure), redirect to the combined page
        if ($data === NULL) {
            // Signal building_access_controller to activate the Season Ski Passes tab
            $this->session->set_flashdata('show_season_tab', true);
            redirect('building_access_controller');
            return;
        }
        $data = (array) $data;

        $flash = $this->session->flashdata('infoMessage');
        if ($flash && empty($data['infoMessage'])) {
            $data['infoMessage'] = $flash;
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']   = $currentUserID;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'season_pass';

        $settings = $this->season_pass_model->get_settings_DB($currentResortID);

        $data['enabled']                = (int)$settings->enabled;
        $data['season_pass_price']      = (int)$settings->season_pass_price;
        $data['passes_sold']            = (int)$settings->passes_sold;
        $data['current_season']         = (int)$settings->current_season;
        $data['early_bird_enabled']     = (int)$settings->early_bird_enabled;
        $data['early_bird_discount_pct'] = (int)$settings->early_bird_discount_pct;

        $data['min_price']   = SEASON_PASS_MIN_PRICE;
        $data['max_price']   = SEASON_PASS_MAX_PRICE;
        $data['season_length'] = SEASON_PASS_SEASON_LENGTH;
        $data['loyalty_threshold'] = SEASON_PASS_HIGH_SALES_THRESHOLD;
        $data['loyalty_rep_bonus'] = SEASON_PASS_LOYALTY_REP_BONUS;
        $data['early_bird_min_discount'] = SEASON_PASS_EARLY_BIRD_MIN_DISCOUNT;
        $data['early_bird_max_discount'] = SEASON_PASS_EARLY_BIRD_MAX_DISCOUNT;

        // Preview: estimated passes sold at current price with current reputation
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $reputation  = $resort_info ? (int)$resort_info->reputation : 0;
        $data['estimated_passes'] = $this->season_pass_model->calculate_passes_sold($reputation, $data['season_pass_price'], (bool)$data['early_bird_enabled']);
        $effective_price = $data['early_bird_enabled']
            ? (int)round($data['season_pass_price'] * (1 - $data['early_bird_discount_pct'] / 100))
            : $data['season_pass_price'];
        $data['estimated_daily_revenue'] = (int)floor($data['estimated_passes'] * $effective_price / SEASON_PASS_SEASON_LENGTH);

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves season pass settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('season_pass_form')) {
            redirect('building_access_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules(
            'season_pass_price',
            'season_pass_price',
            'trim|required|integer|greater_than_equal_to[' . SEASON_PASS_MIN_PRICE . ']|less_than_equal_to[' . SEASON_PASS_MAX_PRICE . ']'
        );
        $this->form_validation->set_rules(
            'early_bird_discount_pct',
            'early_bird_discount_pct',
            'trim|required|integer|greater_than_equal_to[' . SEASON_PASS_EARLY_BIRD_MIN_DISCOUNT . ']|less_than_equal_to[' . SEASON_PASS_EARLY_BIRD_MAX_DISCOUNT . ']'
        );

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('infoMessage', 'season_pass_invalid_settings');
            redirect('building_access_controller');
            return;
        }

        $enabled                = ($this->input->post('enabled', TRUE) == '1') ? 1 : 0;
        $season_pass_price      = (int)$this->input->post('season_pass_price', TRUE);
        $early_bird_enabled     = ($this->input->post('early_bird_enabled', TRUE) == '1') ? 1 : 0;
        $early_bird_discount_pct = (int)$this->input->post('early_bird_discount_pct', TRUE);

        $saved = $this->season_pass_model->save_settings_DB($currentResortID, $enabled, $season_pass_price, $early_bird_enabled, $early_bird_discount_pct);

        $this->session->set_flashdata('infoMessage', $saved ? 'season_pass_settings_saved' : 'season_pass_save_error');
        redirect('building_access_controller');
    }
}
