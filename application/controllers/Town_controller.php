<?php
/**
 * Town_controller
 *
 * Displays the Local Town Development dashboard for the player's resort.
 * Shows the current town level, growth progress, property value index,
 * infrastructure level, and guidance on how to grow (or what happens if neglected).
 */
class Town_controller extends CI_Controller {

    /**
     * __construct
     */
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

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('town_model');
    }

    /**
     * index    Town development dashboard
     */
    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        // Town record (may be NULL for new resorts)
        $town = $this->town_model->get_town_DB($currentResortID);

        $town_level    = $town ? (int)$town->town_level    : 0;
        $growth_points = $town ? (int)$town->growth_points : 0;

        // Level names
        $level_names = [
            $this->lang->line('building')['town_level_0'],
            $this->lang->line('building')['town_level_1'],
            $this->lang->line('building')['town_level_2'],
            $this->lang->line('building')['town_level_3'],
            $this->lang->line('building')['town_level_4'],
            $this->lang->line('building')['town_level_5'],
        ];

        // Progress towards next level
        $thresholds   = TOWN_LEVEL_THRESHOLDS;
        $is_max_level = ($town_level >= TOWN_LEVEL_MAX);
        if ($is_max_level) {
            $progress_pct  = 100;
            $points_needed = 0;
        } else {
            $current_floor = $thresholds[$town_level];
            $next_ceiling  = $thresholds[$town_level + 1];
            $range         = $next_ceiling - $current_floor;
            $earned        = max(0, $growth_points - $current_floor);
            $progress_pct  = ($range > 0) ? min(100, (int)round($earned / $range * 100)) : 0;
            $points_needed = max(0, $next_ceiling - $growth_points);
        }

        // Property value index (100 = baseline, +20 per level)
        $property_value_index = 100 + $town_level * TOWN_PROPERTY_VALUE_PER_LEVEL;

        // Infrastructure level mirrors town level
        $infrastructure_level = $town_level;

        // Open hotels (for guidance)
        $open_hotels = $this->town_model->get_open_hotels_count_DB($currentResortID);

        // Neglect check: town exists but no hotels open
        $is_neglected = ($town_level > 0 && $open_hotels == 0);

        $data = [
            'main_content'          => 'town',
            'currentUserID'         => $currentUserID,
            'currentResortID'       => $currentResortID,
            'town_level'            => $town_level,
            'town_level_name'       => $level_names[$town_level],
            'growth_points'         => $growth_points,
            'progress_pct'          => $progress_pct,
            'points_needed'         => $points_needed,
            'is_max_level'          => $is_max_level,
            'property_value_index'  => $property_value_index,
            'infrastructure_level'  => $infrastructure_level,
            'open_hotels'           => $open_hotels,
            'is_neglected'          => $is_neglected,
            'level_names'           => $level_names,
            'thresholds'            => $thresholds,
            'growth_per_hotel'      => TOWN_GROWTH_PER_HOTEL,
            'growth_per_reputation' => TOWN_GROWTH_PER_REPUTATION,
            'neglect_penalty'       => TOWN_NEGLECT_PENALTY_PER_LEVEL,
        ];

        $this->load->view('templates/default', $data);
    }
}
