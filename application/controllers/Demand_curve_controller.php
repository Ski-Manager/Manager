<?php
/**
 * Demand_curve_controller      Displays the dynamic demand curve factors
 *                              affecting the number of visitors at the player's resort.
 *
 * Factors shown:
 *   - Weather (Sunny / Snowing / Raining / Normal)
 *   - Price (skipass daily/weekly pricing coefficient already baked into slope visitors)
 *   - Reputation (resort reputation → visitor multiplier)
 *   - Peak season / Holidays (day-of-season curve with Christmas & February peaks)
 *   - Competition (informational placeholder until competitor system is available)
 */
class Demand_curve_controller extends CI_Controller {

    private $siteLang;

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
        $ci->lang->load('demand_curve', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('weather_model');
    }

    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        // --- Reputation factor ---
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $reputation  = isset($resort_info->reputation) ? (int)$resort_info->reputation : 0;
        $skipass_daily  = isset($resort_info->skipass_daily)  ? (int)$resort_info->skipass_daily  : 30;
        $skipass_weekly = isset($resort_info->skipass_weekly) ? (int)$resort_info->skipass_weekly : 200;
        $bonus_reputation = min(1.0 + ($reputation / 10000), 1.5);   // +1% per 100 reputation, capped at +50% (at 5000 reputation)

        // --- Peak-season factor ---
        $day_of_season    = get_day_of_season($currentResortID);
        $day_int          = (int)$day_of_season;
        $bonus_peak_season = $this->calc_peak_season_bonus($day_int);

        // --- Weather factor (today's forecast) ---
        $today        = strtotime('now');
        $today_GMT    = gmdate('Y-m-d', $today);
        $weather_today = $this->weather_model->select_weather_forecast($today_GMT);
        $weather_name  = '';
        $bonus_weather = 1.0;
        if ($weather_today && $weather_today->num_rows() > 0) {
            $weather_row  = $weather_today->row();
            $weather_name = $weather_row->name_english ?? '';
            if ($weather_name === 'Sunny')   $bonus_weather = 1.2;
            elseif ($weather_name === 'Snowing') $bonus_weather = 0.9;
            elseif ($weather_name === 'Raining') $bonus_weather = 0.8;
        }

        // --- Peak-season schedule table (full 135-day breakdown) ---
        $peak_season_schedule = $this->build_peak_season_schedule();

        // --- Pass data to view ---
        $data['reputation']          = $reputation;
        $data['bonus_reputation']    = $bonus_reputation;
        $data['skipass_daily']       = $skipass_daily;
        $data['skipass_weekly']      = $skipass_weekly;
        $data['day_of_season']       = $day_of_season;
        $data['bonus_peak_season']   = $bonus_peak_season;
        $data['weather_name']        = $weather_name;
        $data['bonus_weather']       = $bonus_weather;
        $data['peak_season_schedule'] = $peak_season_schedule;

        $data['main_content'] = 'demand_curve';
        $this->load->view('templates/default', $data);
    }

    /**
     * calc_peak_season_bonus   Delegates to the global helper calc_peak_season_bonus().
     *                          Kept as a public method for testability.
     *
     * @param int $day_of_season
     * @return float
     */
    public function calc_peak_season_bonus($day_of_season) {
        return calc_peak_season_bonus($day_of_season);
    }

    /**
     * build_peak_season_schedule   Returns an array of [label, day_start, day_end, multiplier, css_class]
     *                              for rendering the demand-curve schedule table.
     */
    protected function build_peak_season_schedule() {
        return [
            ['label_key' => 'season_opening',   'day_start' =>   1, 'day_end' =>  15, 'multiplier' => 0.75, 'badge' => 'secondary'],
            ['label_key' => 'shoulder_period',  'day_start' =>  16, 'day_end' =>  40, 'multiplier' => 0.90, 'badge' => 'warning'],
            ['label_key' => 'christmas_peak',   'day_start' =>  41, 'day_end' =>  50, 'multiplier' => 1.30, 'badge' => 'success'],
            ['label_key' => 'post_christmas',   'day_start' =>  51, 'day_end' =>  60, 'multiplier' => 1.00, 'badge' => 'info'],
            ['label_key' => 'feb_holidays',     'day_start' =>  61, 'day_end' =>  90, 'multiplier' => 1.20, 'badge' => 'success'],
            ['label_key' => 'late_season',      'day_start' =>  91, 'day_end' => 115, 'multiplier' => 0.85, 'badge' => 'warning'],
            ['label_key' => 'season_closing',   'day_start' => 116, 'day_end' => 135, 'multiplier' => 0.70, 'badge' => 'secondary'],
        ];
    }
}
