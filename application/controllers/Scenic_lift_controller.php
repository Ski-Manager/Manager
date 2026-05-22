<?php
/**
 * Scenic_lift_controller
 *
 * Manages the Scenic Lifts feature:
 *   - Enable / disable the sightseeing gondola service
 *   - Set the per-person ticket price
 *   - Set the gondola capacity level (1–5)
 *   - Toggle seasonal off-peak discount
 */
class Scenic_lift_controller extends CI_Controller {

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
        $this->load->model('scenic_lift_model');
    }

    /**
     * index    Scenic lifts management page.
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
        $data['main_content']    = 'scenic_lift';

        $settings = $this->scenic_lift_model->get_settings_DB($currentResortID);

        $data['is_enabled']       = (int)$settings->is_enabled;
        $data['ticket_price']     = (int)$settings->ticket_price;
        $data['capacity_level']   = (int)$settings->capacity_level;
        $data['seasonal_discount']= (int)$settings->seasonal_discount;
        $data['tour_theme']       = (int)$settings->tour_theme;
        $data['photography_package'] = (int)$settings->photography_package;
        $data['vip_gondola']      = (int)$settings->vip_gondola;

        $data['min_ticket_price'] = SCENIC_LIFT_MIN_TICKET_PRICE;
        $data['max_ticket_price'] = SCENIC_LIFT_MAX_TICKET_PRICE;
        $data['daily_cost']       = SCENIC_LIFT_DAILY_COST;
        $data['tourist_ratio']    = SCENIC_LIFT_TOURIST_RATIO;
        $data['rep_bonus']        = SCENIC_LIFT_REP_BONUS_PER_DAY;
        $data['min_capacity']     = SCENIC_LIFT_MIN_CAPACITY;
        $data['max_capacity']     = SCENIC_LIFT_MAX_CAPACITY;

        // Effective daily cost depends on capacity level
        $data['actual_daily_cost'] = SCENIC_LIFT_DAILY_COST
            + ((int)$settings->capacity_level - SCENIC_LIFT_DEFAULT_CAPACITY)
            * SCENIC_LIFT_CAPACITY_COST_PER_LEVEL;

        // Add theme extra cost
        $theme = (int)$settings->tour_theme;
        $theme_extra = 0;
        if ($theme === SCENIC_LIFT_THEME_NATURE)    $theme_extra = SCENIC_LIFT_THEME_NATURE_EXTRA_COST;
        elseif ($theme === SCENIC_LIFT_THEME_SUNSET) $theme_extra = SCENIC_LIFT_THEME_SUNSET_EXTRA_COST;
        elseif ($theme === SCENIC_LIFT_THEME_ADVENTURE) $theme_extra = SCENIC_LIFT_THEME_ADVENTURE_EXTRA_COST;
        $data['actual_daily_cost'] += $theme_extra;

        if ((int)$settings->photography_package === 1) {
            $data['actual_daily_cost'] += SCENIC_LIFT_PHOTO_DAILY_COST;
        }
        if ((int)$settings->vip_gondola === 1) {
            $data['actual_daily_cost'] += SCENIC_LIFT_VIP_DAILY_COST;
        }

        $this->load->view('templates/default', $data);
    }

    /**
     * save     Saves scenic-lift settings from the form POST.
     */
    public function save() {
        if (!$this->input->post('scenic_lift_form')) {
            redirect('scenic_lift_controller');
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger text-center">', '</div>');
        $this->form_validation->set_rules('ticket_price',   'ticket_price',   'trim|required|integer|greater_than_equal_to[' . SCENIC_LIFT_MIN_TICKET_PRICE . ']|less_than_equal_to[' . SCENIC_LIFT_MAX_TICKET_PRICE . ']');
        $this->form_validation->set_rules('capacity_level', 'capacity_level', 'trim|required|integer|greater_than_equal_to[' . SCENIC_LIFT_MIN_CAPACITY . ']|less_than_equal_to[' . SCENIC_LIFT_MAX_CAPACITY . ']');
        $this->form_validation->set_rules('tour_theme',     'tour_theme',     'trim|required|integer|greater_than_equal_to[' . SCENIC_LIFT_THEME_STANDARD . ']|less_than_equal_to[' . SCENIC_LIFT_THEME_ADVENTURE . ']');

        if ($this->form_validation->run() == FALSE) {
            $data['infoMessage'] = 'scenic_lift_invalid_settings';
            $this->index($data);
            return;
        }

        $ticket_price         = (int)$this->input->post('ticket_price', TRUE);
        $is_enabled           = ($this->input->post('is_enabled', TRUE) == '1') ? 1 : 0;
        $capacity_level       = (int)$this->input->post('capacity_level', TRUE);
        $seasonal_discount    = ($this->input->post('seasonal_discount', TRUE) == '1') ? 1 : 0;
        $tour_theme           = (int)$this->input->post('tour_theme', TRUE);
        $photography_package  = ($this->input->post('photography_package', TRUE) == '1') ? 1 : 0;
        $vip_gondola          = ($this->input->post('vip_gondola', TRUE) == '1') ? 1 : 0;

        $saved = $this->scenic_lift_model->save_settings_DB(
            $currentResortID,
            $is_enabled,
            $ticket_price,
            $capacity_level,
            $seasonal_discount,
            $tour_theme,
            $photography_package,
            $vip_gondola
        );

        $this->session->set_flashdata('infoMessage', $saved ? 'scenic_lift_settings_saved' : 'scenic_lift_save_error');
        redirect('scenic_lift_controller');
    }
}
