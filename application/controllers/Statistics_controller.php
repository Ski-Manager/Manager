<?php
/**
 * Statistics_controller    Displays the statistics dashboard with detailed charts
 */
class Statistics_controller extends CI_Controller{

    private $siteLang;

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
        $this->siteLang = $siteLang;
        $ci->lang->load('home', $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar', $siteLang);
        $ci->lang->load('statistics', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('statistics_model');
    }


    public function index($data = NULL){
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated = $this->users_model->check_account_activated($currentUserID);

        if ($user_activated) {
            $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);
            if ($resultResort->num_rows() > 0) {
                $data['resort_built'] = true;
                $data['currentResortId'] = $currentResortID;
            } else {
                $this->session->set_flashdata('error', 'no_resort');
                redirect('resort_controller');
            }
            $data['main_content'] = 'statistics';
            $this->load->view('templates/default', $data);
        } else {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }


    /**
     * get_lift_usage_chart     Returns JSON data for peak lift usage bar chart
     */
    public function get_lift_usage_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_lift_usage_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $result = $this->statistics_model->get_lift_usage_DB($currentResortID);

        $lang_col = 'name_' . $this->siteLang;

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['lift'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['effective_throughput'], 'type' => 'number'],
        ];

        $rows = [];
        foreach ($result->result() as $row) {
            $lift_name = ($row->custom_name !== '' && $row->custom_name !== NULL)
                ? $row->custom_name
                : (isset($row->$lang_col) ? $row->$lang_col : $row->name_english);
            $effective = round($row->throughput * $row->lift_condition / 100);
            $rows[] = ['c' => [
                ['v' => (string) $lift_name],
                ['v' => (int) $effective],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_lift_usage_title'],
            'width'  => '100%',
            'height' => 350,
            'legend' => 'none',
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['persons_per_hour'], 'minValue' => 0]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * get_revenue_per_lift_chart   Returns JSON data for estimated revenue per lift bar chart
     */
    public function get_revenue_per_lift_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_revenue_lift_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $yesterday_GMT = gmdate('Y-m-d', strtotime('-1 day'));

        $lifts = $this->statistics_model->get_revenue_per_lift_DB($currentResortID);
        $skipass_revenue = $this->statistics_model->get_skipass_revenue_DB($currentResortID, $yesterday_GMT);

        $lang_col = 'name_' . $this->siteLang;

        // Calculate total weighted throughput to distribute revenue proportionally
        $total_weighted = 0;
        foreach ($lifts->result() as $row) {
            $total_weighted += $row->throughput * max($row->lift_condition, 1) / 100;
        }

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['lift'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['estimated_revenue'], 'type' => 'number'],
            ['id' => '', 'label' => $this->lang->line('statistics')['operating_cost'], 'type' => 'number'],
        ];

        $rows = [];
        foreach ($lifts->result() as $row) {
            $lift_name = ($row->custom_name !== '' && $row->custom_name !== NULL)
                ? $row->custom_name
                : (isset($row->$lang_col) ? $row->$lang_col : $row->name_english);
            $weight = ($total_weighted > 0)
                ? ($row->throughput * max($row->lift_condition, 1) / 100) / $total_weighted
                : 0;
            $estimated_rev = round($skipass_revenue * $weight);
            $rows[] = ['c' => [
                ['v' => (string) $lift_name],
                ['v' => (int) $estimated_rev],
                ['v' => (int) $row->daily_cost],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_revenue_per_lift_title'],
            'width'  => '100%',
            'height' => 350,
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['euros'], 'minValue' => 0]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * get_slope_popularity_chart   Returns JSON data for most popular slopes bar chart
     */
    public function get_slope_popularity_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_slope_pop_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $result = $this->statistics_model->get_slope_popularity_DB($currentResortID);

        $lang_col = 'name_' . $this->siteLang;

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['slope'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['condition'], 'type' => 'number'],
        ];

        $rows = [];
        foreach ($result->result() as $row) {
            $slope_name = isset($row->$lang_col) ? $row->$lang_col : $row->name_english;
            $rows[] = ['c' => [
                ['v' => (string) $slope_name],
                ['v' => (int) $row->slope_condition],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_slope_popularity_title'],
            'width'  => '100%',
            'height' => 350,
            'legend' => 'none',
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['condition_pct'], 'minValue' => 0, 'maxValue' => 100]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * get_satisfaction_chart   Returns JSON data for guest satisfaction (reputation) line chart
     */
    public function get_satisfaction_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_satisfaction_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $graph_start_date = $this->_get_graph_start_date($currentResortID);
        $result = $this->statistics_model->get_satisfaction_history_DB($currentResortID, $graph_start_date);

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['date'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['reputation'], 'type' => 'number'],
        ];

        $all_rows = $result->result();
        $total = count($all_rows);
        $rows = [];
        foreach ($all_rows as $i => $row) {
            $label = ($i === $total - 1)
                ? $this->lang->line('statistics')['yesterday']
                : $row->date;
            $rows[] = ['c' => [
                ['v' => (string) $label],
                ['v' => (float) $row->reputation],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_satisfaction_title'],
            'width'  => '100%',
            'height' => 350,
            'legend' => 'none',
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['reputation'], 'minValue' => 0]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * get_weather_history_chart    Returns JSON data for weather history (snow level) area chart
     */
    public function get_weather_history_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_weather_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $graph_start_date = $this->_get_graph_start_date($currentResortID);
        $result = $this->statistics_model->get_weather_history_DB($currentResortID, $graph_start_date);

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['date'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['snow_level'], 'type' => 'number'],
        ];

        $all_rows = $result->result();
        $total = count($all_rows);
        $rows = [];
        foreach ($all_rows as $i => $row) {
            $label = ($i === $total - 1)
                ? $this->lang->line('statistics')['yesterday']
                : $row->date;
            $rows[] = ['c' => [
                ['v' => (string) $label],
                ['v' => (float) $row->snow_level],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_weather_title'],
            'width'  => '100%',
            'height' => 350,
            'legend' => 'none',
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['snow_level_cm'], 'minValue' => 0]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * get_visitor_count_chart  Returns JSON data for daily visitor count line chart
     */
    public function get_visitor_count_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_visitors_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $graph_start_date = $this->_get_graph_start_date($currentResortID);
        $result = $this->statistics_model->get_visitor_count_history_DB($currentResortID, $graph_start_date);

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['date'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['visitors'], 'type' => 'number'],
        ];

        $all_rows = $result->result();
        $total = count($all_rows);
        $rows = [];
        foreach ($all_rows as $i => $row) {
            $label = ($i === $total - 1)
                ? $this->lang->line('statistics')['yesterday']
                : $row->date;
            $rows[] = ['c' => [
                ['v' => (string) $label],
                ['v' => (int) $row->affluence],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_visitor_count_title'],
            'width'  => '100%',
            'height' => 350,
            'legend' => 'none',
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['visitors_label'], 'minValue' => 0]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * get_revenue_expenses_chart   Returns JSON data for daily revenue vs expenses line chart
     */
    public function get_revenue_expenses_chart(){
        $currentResortID = trim($this->input->post('currentResortID', TRUE));
        $cache_key = 'stat_rev_exp_' . $currentResortID . '_' . $this->siteLang;
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) { echo $cached; return; }

        $graph_start_date = $this->_get_graph_start_date($currentResortID);

        $revenue_result  = $this->statistics_model->get_revenue_history_DB($currentResortID, $graph_start_date);
        $expenses_result = $this->statistics_model->get_expenses_history_DB($currentResortID, $graph_start_date);

        // Build a date-keyed expenses lookup to avoid per-row queries
        $expenses_by_date = [];
        foreach ($expenses_result->result() as $row) {
            $expenses_by_date[$row->date] = (float) $row->expenses;
        }

        $table_values['cols'] = [
            ['id' => '', 'label' => $this->lang->line('statistics')['date'], 'type' => 'string'],
            ['id' => '', 'label' => $this->lang->line('statistics')['revenue_label'], 'type' => 'number'],
            ['id' => '', 'label' => $this->lang->line('statistics')['expenses_label'], 'type' => 'number'],
        ];

        $all_rows = $revenue_result->result();
        $total = count($all_rows);
        $rows = [];
        foreach ($all_rows as $i => $row) {
            $label = ($i === $total - 1)
                ? $this->lang->line('statistics')['yesterday']
                : $row->date;
            $expenses = $expenses_by_date[$row->date] ?? 0.0;
            $rows[] = ['c' => [
                ['v' => (string) $label],
                ['v' => (float) $row->revenue],
                ['v' => $expenses],
            ]];
        }
        $table_values['rows'] = $rows;

        $chart_options = [
            'title'  => $this->lang->line('statistics')['chart_revenue_expenses_title'],
            'width'  => '100%',
            'height' => 350,
            'vAxes'  => ['0' => ['title' => $this->lang->line('statistics')['euros'], 'minValue' => 0]],
        ];

        $chart_package = [
            ['data' => $table_values],
            ['options' => $chart_options],
        ];
        $json = json_encode($chart_package);
        $this->cache->save($cache_key, $json, 300);
        echo $json;
    }


    /**
     * _get_graph_start_date    Returns the start date for history charts (season start or 7 days ago)
     *
     * @param int $currentResortID  ID of the resort
     * @return string               Start date in Y-m-d format
     */
    private function _get_graph_start_date($currentResortID){
        $today = strtotime('now');
        $sevenDaysAgo = strtotime('-7 days', $today);
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);
        $current_season_start_date = strtotime(get_current_season_start_date($currentResortID));
        $current_season_start_date_GMT = gmdate('Y-m-d', $current_season_start_date);
        $interval = (new DateTime($current_season_start_date_GMT))->diff(new DateTime($sevenDaysAgo_GMT));
        $interval_formatted = $interval->format('%R%a');
        return ($interval_formatted >= 0) ? $current_season_start_date_GMT : $sevenDaysAgo_GMT;
    }
}
