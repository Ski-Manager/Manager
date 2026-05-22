<?php
/**
 * 
 */
class Weather_controller extends CI_Controller{
    
    private $siteLang;  // To use the siteLang variable globally
    
    /**
     * __construct
     */
    public function __construct() {
        parent::__construct(); 
        $ci =& get_instance();
        
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');            // Store current language in variable
        } else {
            $siteLang = 'english';                                      // If no session, use English
            $this->session->set_userdata('site_lang', $siteLang);
        }
        // Loads the different language files
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        //$ci->lang->load('signup_form',$siteLang);
        //$ci->lang->load('contact_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('weather',$siteLang);
        //$ci->lang->load('bank',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $ci->lang->load('climate_change',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('weather_model');
        $this->load->model('resort_model');
        $this->load->model('climate_change_model');
    }
    

    public function index(){
       
        //$data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        
        //$player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
        //$player_info_data = $player_info->row();
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today);
        $test_forecast_status = $this->weather_model->test_forecast_status($currentUserID, $today_GMT); // Check if the player has subscribed to an extended forecast
        if ($test_forecast_status != FALSE && $test_forecast_status->end_forecast >= $today_GMT) {  // Subscription ongoing
            $data['button_subscribe'] = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('weather')['already_subscribed'].'"><a href="?action=signup_forecast" class="signup_forecast-dialog"><button class="btn btn-success disabled" disabled="true" id="forecastButton">'.$this->lang->line('weather')['subscribe_extended_forecast'].'</button></a></div>';
            $data['button_subscribe'] .= '<br>'.$this->lang->line('weather')['ongoing_subscription'].date('d/m/Y',strtotime($test_forecast_status->end_forecast));
            $max = 14;
        }
        else {  // No subscription ongoing
            $data['button_subscribe'] = '<div style="display:inline;" class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('weather')['subscribe_extended_forecast_tooltip'].'"><a href="?action=signup_forecast" class="signup_forecast-dialog"><button class="btn btn-success" id="forecastButton">'.$this->lang->line('weather')['subscribe_extended_forecast'].'</button></a></div>';
            $max = 3;
        } 
        $this->_ensure_forecast_data(30);
        $forecast_table = $this->build_forecast_table($max, $today);
               
        
        $data['table'] = $forecast_table;   
        
        // Current resort snow level
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resort_info = $this->resort_model->display_resort_info_DB($currentResortID)->row();
        $data['current_snow_level'] = isset($resort_info->snow_level) ? max(0, (int)$resort_info->snow_level) : 0;

        // Today's temperature (from today's weather forecast entry)
        $today_temperature = null;
        $today_condition   = null;
        $today_forecast = $this->weather_model->select_weather_forecast($today_GMT);
        if ($today_forecast !== false) {
            $today_condition_id = $today_forecast->row()->id_condition;
            $today_condition    = $this->weather_model->select_weather_conditions($today_condition_id)->row();
            if ($today_condition) {
                $today_temperature = (float)$today_condition->temperature;
            }
        }

        // Snow quality
        $snow_quality = get_snow_quality($data['current_snow_level'], $today_temperature);
        $data['snow_quality_key']   = $snow_quality['key'];
        $data['snow_quality_badge'] = $snow_quality['badge_class'];

        // Today's full condition details for hero card
        $data['today_temperature'] = $today_temperature;
        if ($today_condition !== null) {
            $tw_lang = $this->session->userdata('site_lang') ?: 'english';
            $tw_col  = 'name_' . $tw_lang;
            $data['today_condition_name'] = htmlspecialchars($today_condition->$tw_col ?? $today_condition->name_english, ENT_QUOTES, 'UTF-8');
            $data['today_condition_key']  = $today_condition->name_english;
            $data['today_wind']           = (float)$today_condition->wind_strength;
            $data['today_icon']           = $this->get_weather_icon($today_condition->name_english);
        }
        // Structured forecast array for new UI; $data['table'] kept for AJAX subscription compat
        $data['forecast_data'] = $this->build_forecast_data($max, $today);

        // Climate change data for the combined view
        $climate = $this->climate_change_model->get_climate_data_DB($currentResortID);
        if ($climate === FALSE) {
            $this->climate_change_model->init_climate_DB($currentResortID);
            $climate = $this->climate_change_model->get_climate_data_DB($currentResortID);
        }
        $data['climate']        = $climate;
        $data['climate_effects'] = $this->_get_climate_effects($climate->climate_level);
        $data['invest_costs']   = [
            'snowmaking' => CLIMATE_INVEST_SNOWMAKING,
            'altitude'   => CLIMATE_INVEST_ALTITUDE,
            'diversify'  => CLIMATE_INVEST_DIVERSIFY,
        ];
        $data['current_season'] = get_current_season($currentResortID);

        // Season phase for dynamic seasonal display
        $day_of_season          = (int) get_day_of_season($currentResortID);
        if ($day_of_season < 1)  $day_of_season = 1;
        $data['day_of_season']  = $day_of_season;
        $data['season_phase']   = get_season_phase($day_of_season);
        $data['seasonal_melt']  = calc_seasonal_melt_rate($day_of_season);

        // Displaying the account view
        $data['main_content'] = 'weather_climate';
        $this->load->view('templates/default',$data);  
    }
    
    
    public function build_forecast_table ($max = '14', $today = NULL) {
        if ($today == NULL) {
            $today = strtotime('now');
            $ajax_mode = TRUE;
        }
        else {
            $ajax_mode = false;
        }
        $siteLang = $this->session->userdata('site_lang');
        $table = '<table class="table table-responsive building_weather building_6th" align="center">
                <thead>
                    <tr>
                        <th></th>
                        <th>'.$this->lang->line('weather')['weather'].'</th>
                        <th>'.$this->lang->line('weather')['temperature'].'</th>
                        <th>'.$this->lang->line('weather')['wind'].'</th>
                        <th>'.$this->lang->line('weather')['snow_level'].'</th>
                        <th>'.$this->lang->line('weather')['visitor_impact'].'</th>
                    </tr>
                </thead>';
        $table .= '<tbody>';
        for ($i=0; $i<$max ; $i++) {
            $table .= '<tr>';
            $inXdays = strtotime('+'.$i.' days', $today);
            $inXdays_GMT = gmdate('Y-m-d', $inXdays);
            $date_label = gmdate('D, d M', $inXdays);
            if ($i == 0)
                $table .= '<th>'.$this->lang->line('home')['today'].'<br><small class="text-muted">'.$date_label.'</small></th>';
            else if ($i == 1)
                $table .= '<th>'.$this->lang->line('weather')['tomorrow'].'<br><small class="text-muted">'.$date_label.'</small></th>';
            else 
                $table .= '<th>'.$this->lang->line('weather')['day_plus'].$i.'<br><small class="text-muted">'.$date_label.'</small></th>';

            $array_weather = $this->weather_model->select_weather_forecast($inXdays_GMT);  // Check if there is an entry for current date in loop
            if ($array_weather != FALSE) {
                $array_weather_data = $array_weather->row();
                $id_condition = $array_weather_data->id_condition;
                $array_weather_date = $this->weather_model->select_weather_conditions($id_condition); // Get details for todays condition (snow level, name...)
                $result = $array_weather_date->row();
                $snow_difference = $result->snow_level;
                if ($snow_difference >= 0)
                    $snow_difference = '+'.$snow_difference;
                $temperature = $result->temperature;
                $wind_strength = $result->wind_strength;
                $column_lang = 'name_'.$siteLang;
                $name_weather = $result->$column_lang;
                $name_english = $result->name_english;
                if ($result->danger == 1){
                    $name_weather = $name_weather.' ('.$this->lang->line('weather')['danger'].')';
                }

                $icon_class = $this->get_weather_icon($name_english);
                $visitor_impact = $this->get_visitor_impact($name_english);

                $table .= '<td><i class="bi '.$icon_class.'" aria-hidden="true"></i> '.htmlspecialchars($name_weather, ENT_QUOTES, 'UTF-8').'</td>';
                $table .= '<td>'.$temperature.' °C</td>';
                $table .= '<td>'.$wind_strength.' m/s</td>';
                $table .= '<td>'.$snow_difference.' cm</td>';
                $table .= '<td>'.$visitor_impact.'</td>';
            } else {
                $table .= '<td colspan="5" class="text-muted text-center">'.$this->lang->line('weather')['no_forecast_data'].'</td>';
            }
            $table .= '</tr>';
        }

        $table .= '</tbody>';  
        $table .= '</table>';  
        if ($ajax_mode === TRUE)    // If requested via ajax (after subscription)
            echo json_encode(array('table' => $table));
        else                // Requested via PHP, first display of the page
            return $table;
    }

    private function build_forecast_data($max = '14', $today = NULL) {
        if ($today === NULL) $today = strtotime('now');
        $siteLang = $this->session->userdata('site_lang') ?: 'english';
        $forecast = [];
        for ($i = 0; $i < (int)$max; $i++) {
            $inXdays     = strtotime('+' . $i . ' days', $today);
            $inXdays_GMT = gmdate('Y-m-d', $inXdays);
            $date_label  = gmdate('D, d M', $inXdays);
            if ($i === 0)     $label = $this->lang->line('home')['today'];
            elseif ($i === 1) $label = $this->lang->line('weather')['tomorrow'];
            else              $label = $this->lang->line('weather')['day_plus'] . $i;

            $entry = ['label' => $label, 'date_label' => $date_label, 'has_data' => false];

            $aw = $this->weather_model->select_weather_forecast($inXdays_GMT);
            if ($aw != FALSE) {
                $aw_row = $aw->row();
                $cond   = $this->weather_model->select_weather_conditions($aw_row->id_condition)->row();
                $col    = 'name_' . $siteLang;
                $cname  = htmlspecialchars($cond->$col, ENT_QUOTES, 'UTF-8');
                $neng   = $cond->name_english;
                if ($cond->danger) $cname .= ' (' . $this->lang->line('weather')['danger'] . ')';
                $sdelta = (float)$cond->snow_level;

                $entry['has_data']     = true;
                $entry['name']         = $cname;
                $entry['name_english'] = $neng;
                $entry['icon']         = $this->get_weather_icon($neng);
                $entry['temperature']  = (float)$cond->temperature;
                $entry['wind']         = (float)$cond->wind_strength;
                $entry['snow_delta']   = ($sdelta >= 0 ? '+' : '') . $sdelta . ' cm';
                $entry['snow_num']     = $sdelta;
                $entry['impact_key']   = $this->_get_visitor_impact_key($neng);
                $entry['is_danger']    = (bool)$cond->danger;
            }
            $forecast[] = $entry;
        }
        return $forecast;
    }

    private function _get_visitor_impact_key($name_english) {
        $map = [
            'Sunny'    => 'up',
            'Snowing'  => 'down_medium',
            'Raining'  => 'down_high',
            'Storm'    => 'down_high',
            'Blizzard' => 'down_high',
        ];
        return $map[$name_english] ?? 'neutral';
    }

    private function get_weather_icon($name_english) {
        $map = [
            'Sunny'    => 'bi-sun',
            'Cloudy'   => 'bi-cloud',
            'Overcast' => 'bi-clouds',
            'Snowing'  => 'bi-cloud-snow',
            'Raining'  => 'bi-cloud-rain',
            'Storm'    => 'bi-cloud-lightning-rain',
            'Blizzard' => 'bi-snow2',
            'Fog'      => 'bi-cloud-fog',
            'Windy'    => 'bi-wind',
        ];
        return $map[$name_english] ?? 'bi-cloud';
    }

    private function get_visitor_impact($name_english) {
        if ($name_english === 'Sunny') {
            return '<span class="text-success">'.$this->lang->line('weather')['visitors_up'].'</span>';
        } else if ($name_english === 'Snowing') {
            return '<span class="text-warning">'.$this->lang->line('weather')['visitors_down_medium'].'</span>';
        } else if ($name_english === 'Raining') {
            return '<span class="text-danger">'.$this->lang->line('weather')['visitors_down_high'].'</span>';
        } else {
            return '<span class="text-secondary">'.$this->lang->line('weather')['visitors_neutral'].'</span>';
        }
    }
        
    public function subscribe_forecast () {
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);          // to be used in this file
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        $player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
        $player_info_data = $player_info->row();
        $genepis = $player_info_data->genepis;
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today);
        $test_forecast_status = $this->weather_model->test_forecast_status($currentUserID, $today_GMT); // Check if the player has subscribed to an extended forecast
        if ($test_forecast_status === false) {  // Subscription ongoing
            if ($genepis >= COST_EXT_FORECAST) {
                $today = strtotime('now');
                $in60days = strtotime('+60 days', $today);
                $in60days_GMT = gmdate('Y-m-d', $in60days);
                $data_ext_forecast = array (
                    'id_player' => $currentUserID,
                    'end_forecast' => $in60days_GMT
                );
                $subscribe_extended_forecast = $this->weather_model->subscribe_extended_forecast_DB($data_ext_forecast);
                if ($subscribe_extended_forecast === true) {
                    $remove_genepis_cost = $this->users_model->remove_genepis_cost_DB(COST_EXT_FORECAST);
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $this->lang->line('logs')['subscribed_forecast1']) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $this->lang->line('logs')['subscribed_forecast1']) );   // Add a log row to the game_player_logs table
                    echo json_encode(array('subscribed' => true, 'message' => $this->lang->line('weather')['forecast_subscribed']));
                }
            }
            else {
                echo json_encode(array('subscribed' => false, 'message' => $this->lang->line('home')['not_enough_genepis']));
            }
        }
        else {
                echo json_encode(array('subscribed' => false, 'message' => $this->lang->line('weather')['already_subscribed']));
            }
    }

    private function _ensure_forecast_data($days = 30) {
        $weightedValues = [
            1=>3,  2=>3,  3=>4,  4=>4,  5=>3,  6=>3,  7=>3,  8=>3,  9=>2, 10=>2,
            11=>2, 12=>1, 13=>1, 14=>1, 15=>3, 16=>5, 17=>3, 18=>3, 19=>3, 20=>3,
            21=>3, 22=>3, 23=>3, 24=>3, 25=>1, 26=>1, 27=>3, 28=>3, 29=>3, 30=>3,
            31=>3, 32=>3, 33=>5, 34=>4, 35=>4,
        ];
        $start_date = gmdate('Y-m-d');
        $end_date   = gmdate('Y-m-d', strtotime('+' . ($days - 1) . ' days'));
        $existing   = $this->weather_model->get_forecast_dates_in_range($start_date, $end_date);
        for ($i = 0; $i < $days; $i++) {
            $date = gmdate('Y-m-d', strtotime('+' . $i . ' days'));
            if (!in_array($date, $existing)) {
                $rand = mt_rand(1, 100);
                $id_condition = 1;
                foreach ($weightedValues as $key => $value) {
                    $rand -= $value;
                    if ($rand <= 0) {
                        $id_condition = $key;
                        break;
                    }
                }
                $this->weather_model->insert_weather_forecast_DB($date, $id_condition);
            }
        }
    }

    private function _get_climate_effects($level) {
        $level = max(0, min(10, (int)$level));
        return [
            'winter_snow_penalty'   => CLIMATE_SNOW_PENALTY_PER_LEVEL   * $level,
            'snowmaking_cost_mult'  => 1.0 + (CLIMATE_COST_MULT_PER_LEVEL * $level),
            'glacier_loss'          => CLIMATE_GLACIER_LOSS_PER_LEVEL   * $level,
            'season_length_penalty' => CLIMATE_SEASON_PENALTY_PER_LEVEL * $level,
        ];
    }
}