<?php
/**
 * Off_season_controller
 *
 * Handles the Off-Season Management page.  Players can build and upgrade
 * five summer-activity buildings (mountain biking, hiking, festival,
 * wedding venue, alpine coaster) that generate revenue independently of
 * ski-season conditions.
 */
class Off_season_controller extends CI_Controller {

    private $siteLang;

    /** Summer-activity building types managed by this controller */
    private $off_season_types = [
        'mountain_biking',
        'hiking',
        'festival',
        'wedding_venue',
        'alpine_coaster',
    ];

    // ---------------------------------------------------------------
    // __construct
    // ---------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }

        $ci->lang->load('home',      $siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',    $siteLang);
        $ci->lang->load('building',  $siteLang);
        $ci->lang->load('logs',      $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('achievements_model');
        $this->load->model('logs_model');
    }

    // ---------------------------------------------------------------
    // index
    // ---------------------------------------------------------------
    public function index($data = NULL) {

        $data['pageTitle'] = '<h2>'.$this->lang->line('common_buildings')['titleMain']
                        .' - '.$this->lang->line('off_season')['title'].'</h2>';
        $data['introOffSeason'] = '<div>'.$this->lang->line('off_season')['intro'].'</div>';

        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $data['currentUserID']  = $currentUserID;

        $user_activated = $this->users_model->check_account_activated($currentUserID);

        if ($user_activated) {
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);

            if ($checkIfResortExists->num_rows() > 0) {
                $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');

                if ($tourist_info_data->num_rows() == 1) {
                    $data['hideBuilding'] = false;

                    // Ensure catalogue rows exist in game_buildings (once per page load)
                    $this->ensure_off_season_buildings_exist();

                    // Build data block for each summer-activity type
                    foreach ($this->off_season_types as $building_type) {
                        $block = $this->standardBuildingBlock($building_type, $currentResortID);
                        foreach ($block as $key => $value) {
                            $data[$key][$building_type] = $value;
                        }
                    }
                } else {
                    $data['hideBuilding'] = true;
                    $data['infoMessage']  = 'tourist_info_required';
                }
            } else {
                redirect('resort_controller');
            }
        }

        $data['main_content'] = 'off_season';
        $this->load->view('templates/default', $data);
    }

    // ---------------------------------------------------------------
    // ensure_off_season_buildings_exist
    // ---------------------------------------------------------------
    /**
     * Inserts the off-season building catalogue rows into game_buildings
     * if they are not already present. This removes the dependency on the
     * migration system being enabled.
     */
    private function ensure_off_season_buildings_exist() {
        $catalogue = [
            // Mountain Biking
            ['type' => 'mountain_biking', 'level' => 1, 'name_english' => 'Mountain Biking Trail',        'name_french' => 'Piste de VTT',                    'building_time' => 86400,  'building_cost' => 400000,  'reputation' => 10, 'capacity' => 50,  'max_income' => 800,  'daily_cost' => 100],
            ['type' => 'mountain_biking', 'level' => 2, 'name_english' => 'Mountain Biking Park',         'name_french' => 'Parc de VTT',                     'building_time' => 172800, 'building_cost' => 800000,  'reputation' => 20, 'capacity' => 100, 'max_income' => 1600, 'daily_cost' => 200],
            ['type' => 'mountain_biking', 'level' => 3, 'name_english' => 'Mountain Biking Resort',       'name_french' => 'Station de VTT',                  'building_time' => 345600, 'building_cost' => 1500000, 'reputation' => 30, 'capacity' => 200, 'max_income' => 3000, 'daily_cost' => 400],
            // Hiking
            ['type' => 'hiking',          'level' => 1, 'name_english' => 'Hiking Trail',                 'name_french' => 'Sentier de randonnée',             'building_time' => 86400,  'building_cost' => 200000,  'reputation' => 8,  'capacity' => 80,  'max_income' => 400,  'daily_cost' => 50],
            ['type' => 'hiking',          'level' => 2, 'name_english' => 'Hiking Network',               'name_french' => 'Réseau de randonnées',             'building_time' => 172800, 'building_cost' => 500000,  'reputation' => 16, 'capacity' => 160, 'max_income' => 900,  'daily_cost' => 100],
            ['type' => 'hiking',          'level' => 3, 'name_english' => 'Guided Hiking Center',         'name_french' => 'Centre de randonnée guidée',       'building_time' => 259200, 'building_cost' => 1000000, 'reputation' => 25, 'capacity' => 320, 'max_income' => 1800, 'daily_cost' => 200],
            // Festival
            ['type' => 'festival',        'level' => 1, 'name_english' => 'Festival Grounds',             'name_french' => 'Terrain de festival',              'building_time' => 172800, 'building_cost' => 600000,  'reputation' => 20, 'capacity' => 200, 'max_income' => 1500, 'daily_cost' => 200],
            ['type' => 'festival',        'level' => 2, 'name_english' => 'Open-Air Festival Park',       'name_french' => 'Parc de festival en plein air',    'building_time' => 259200, 'building_cost' => 1200000, 'reputation' => 35, 'capacity' => 400, 'max_income' => 3000, 'daily_cost' => 400],
            ['type' => 'festival',        'level' => 3, 'name_english' => 'Festival & Concert Hall',      'name_french' => 'Festival & Salle de concert',      'building_time' => 432000, 'building_cost' => 2500000, 'reputation' => 55, 'capacity' => 800, 'max_income' => 6000, 'daily_cost' => 800],
            // Wedding Venue
            ['type' => 'wedding_venue',   'level' => 1, 'name_english' => 'Garden Wedding Venue',         'name_french' => 'Salle de mariage au jardin',       'building_time' => 172800, 'building_cost' => 500000,  'reputation' => 15, 'capacity' => 50,  'max_income' => 2000, 'daily_cost' => 150],
            ['type' => 'wedding_venue',   'level' => 2, 'name_english' => 'Panorama Wedding Lodge',       'name_french' => 'Lodge panoramique de mariage',     'building_time' => 259200, 'building_cost' => 1000000, 'reputation' => 30, 'capacity' => 100, 'max_income' => 4000, 'daily_cost' => 300],
            ['type' => 'wedding_venue',   'level' => 3, 'name_english' => 'Luxury Alpine Wedding Palace', 'name_french' => 'Palais de mariage alpin de luxe',  'building_time' => 432000, 'building_cost' => 2000000, 'reputation' => 50, 'capacity' => 200, 'max_income' => 8000, 'daily_cost' => 600],
            // Alpine Coaster
            ['type' => 'alpine_coaster',  'level' => 1, 'name_english' => 'Alpine Coaster',               'name_french' => 'Luge alpine',                      'building_time' => 259200, 'building_cost' => 800000,  'reputation' => 20, 'capacity' => 100, 'max_income' => 2000, 'daily_cost' => 200],
            ['type' => 'alpine_coaster',  'level' => 2, 'name_english' => 'Alpine Coaster Plus',          'name_french' => 'Luge alpine Plus',                 'building_time' => 345600, 'building_cost' => 1500000, 'reputation' => 35, 'capacity' => 200, 'max_income' => 4000, 'daily_cost' => 400],
            ['type' => 'alpine_coaster',  'level' => 3, 'name_english' => 'Extreme Alpine Coaster',       'name_french' => 'Luge alpine extrême',              'building_time' => 518400, 'building_cost' => 3000000, 'reputation' => 50, 'capacity' => 300, 'max_income' => 7000, 'daily_cost' => 700],
        ];

        // Fetch all existing off-season type+level combinations in one query
        $existing = $this->db
            ->select('type, level')
            ->from('game_buildings')
            ->where_in('type', $this->off_season_types)
            ->get()
            ->result();

        $existingKeys = [];
        foreach ($existing as $row) {
            $existingKeys[$row->type . '_' . $row->level] = true;
        }

        foreach ($catalogue as $row) {
            if (!isset($existingKeys[$row['type'] . '_' . $row['level']])) {
                $this->db->insert('game_buildings', $row);
            }
        }
    }

    // ---------------------------------------------------------------
    // standardBuildingBlock
    // ---------------------------------------------------------------
    /**
     * Builds the data array for one summer-activity building type.
     * Returns arrays keyed by level (1, 2, 3).
     */
    public function standardBuildingBlock($building_type, $currentResortID) {
        $data = [];
        $data['logo']  = '<img src="'.base_url('img/icons/'.$building_type.'.png').'" title="'.$this->lang->line($building_type)['title'].'"/>';
        $data['title'] = '<h3>'.$this->lang->line($building_type)['title'].'</h3>';
        $data['desc']  = $this->lang->line($building_type)['desc'];

        $name_language = 'name_'.$this->session->userdata('site_lang');

        for ($i = 1; $i <= 3; $i++) {
            $building_data = $this->building_model->get_generic_building_data($building_type, $i);

            if ($building_data->num_rows() > 0) {
                $building_dataArray = $building_data->row();

                $data['pre_buildingTime'][$i]  = '';
                $data['post_buildingTime'][$i] = '';

                $count_this_building_level = count_this_building_level($building_type, $i);
                $data['buildingQuantity'][$i]  = $count_this_building_level;

                $count_this_building_level_under_construction = count_this_building_level($building_type, $i, '4');

                if ($count_this_building_level_under_construction == '1') {
                    $timestamp    = strtotime(get_time_left_for_building($currentResortID, $building_type, $i)." UTC");
                    $currenttime  = time();
                    $time_left_value = $timestamp - $currenttime;

                    if ($time_left_value <= '0') {
                        $data['wait_status'][$i]       = true;
                        $data['pre_buildingTime'][$i]  = '<a href="'.base_url().'off_season_controller/"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('home')['wait_tooltip'].'">';
                        $data['post_buildingTime'][$i] = '</div></a>';
                        $data['buildingTime'][$i]      = $this->lang->line('home')['wait'];
                    } else {
                        $data['buildingTime'][$i] = gmdate("Y-m-d H:i:s", $timestamp);
                    }
                } else {
                    $data['buildingTime'][$i] = display_friendly_time($building_dataArray->building_time / ACCELERATOR_FACTOR, '');
                }

                $count_num_building_previous_level    = count_this_building_level($building_type, $i - 1);
                $count_this_building_under_construction = count_this_building_level($building_type, '', '4');

                $data['infrastructureName'][$i] = $building_dataArray->$name_language;
                $data['buildingCost'][$i]        = number_format($building_dataArray->building_cost, 0, ',', ' ');
                $data['reputation'][$i]          = number_format($building_dataArray->reputation,    0, ',', ' ');
                $data['capacity'][$i]            = number_format($building_dataArray->capacity,      0, ',', ' ');
                $data['daily_cost'][$i]          = number_format($building_dataArray->daily_cost,    0, ',', ' ');
                $data['max_income'][$i]          = number_format($building_dataArray->max_income,    0, ',', ' ');

                // Rush button
                $currentUserID   = $this->users_model->get_user_id();
                $button_level[$i] = '';
                if (isset($time_left_value) && $time_left_value > 0) {
                    $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                    $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                    if ($genepis_required_to_rush <= $genepis_available) {
                        $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['speed_up_for_genepis'].'"><a href="'.base_url('off_season_controller/rush/'.$building_type.'/'.$i).'"><button class="btn btn-success">'.$this->lang->line('home')['rush'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'].'</button></a></div>';
                    } else {
                        $button_level[$i] = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('home')['not_enough_genepis_to_rush'].'"><a href="'.base_url('genepis_controller').'"><button class="btn btn-warning">'.$this->lang->line('home')['not_enough_genepis'].'</button></a></div>';
                    }
                }

                // Build / Upgrade button
                if ($count_this_building_under_construction == 0) {
                    if ($i == '1') {
                        $data['button'][$i] = '<td><a href="'.base_url().'off_season_controller/build_building/'.$currentResortID.'/'.$building_type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['build'].'</button></a></td>';
                    } else {
                        if ($count_num_building_previous_level == '0') {
                            $data['button'][$i] = '<td><button class="btn btn-danger disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';
                        } else {
                            $data['button'][$i] = '<td><a href="'.base_url().'off_season_controller/upgrade_building/'.$currentResortID.'/'.$building_type.'/'.$i.'"><button class="btn btn-success">'.$this->lang->line('building')['upgrade'].'</button></a></td>';
                        }
                    }
                } else {
                    if ($count_this_building_level_under_construction == '0') {
                        if ($i == '1') {
                            $data['button'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['build'].'</button></td>';
                        } else {
                            $data['button'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrade'].'</button></td>';
                        }
                    } else {
                        if ($i == '1') {
                            $data['button'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['building'].'</button>'.$button_level[$i].'</td>';
                        } else {
                            $data['button'][$i] = '<td><button class="btn btn-warning disabled">'.$this->lang->line('building')['upgrading'].'</button>'.$button_level[$i].'</td>';
                        }
                    }
                }
            }
            // Reset time_left_value for next iteration
            unset($time_left_value);
        }

        return $data;
    }

    // ---------------------------------------------------------------
    // build_building
    // ---------------------------------------------------------------
    public function build_building($currentResortID, $building_type, $level = '1') {
        if (!in_array($building_type, $this->off_season_types))
            redirect('off_season_controller');

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $building_generic_info_data = $this->building_model->get_generic_building_data($building_type, $level);
        if ($building_generic_info_data->num_rows() == 0) {
            redirect('off_season_controller');
            return;
        }
        $building_generic_info_dataArray = $building_generic_info_data->row();
        $id_building = $building_generic_info_dataArray->id_building;

        $count_this_building_under_construction = count_this_building_level($building_type, '', '4');
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if (!$pageWasRefreshed) {
            if ($count_this_building_under_construction == 0) {
                $cost_building  = $building_generic_info_dataArray->building_cost;
                $gain_reputation = $building_generic_info_dataArray->reputation;
                $cash_player    = $this->users_model->get_cash_player();
                $money_after_payment = $cash_player - $cost_building;

                if ($money_after_payment >= 0) {
                    if ($this->users_model->pay_item($cost_building, $cash_player)) {
                        $cash_player   = $this->users_model->get_cash_player();
                        $this->session->set_userdata('cash', $cash_player);
                        add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                        add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                    }
                    $this->users_model->add_reputation($gain_reputation);
                    $reputation_player = $this->users_model->get_reputation_player();
                    $this->session->set_userdata('reputation', $reputation_player);

                    $end_construction = calculate_end_construction($building_type, $level);
                    $data_insert = [
                        'id_resort'        => $currentResortID,
                        'id_building'      => $id_building,
                        'type'             => $building_type,
                        'level'            => $level,
                        'end_construction' => $end_construction,
                        'id_status'        => '4',
                    ];
                    $build_building = $this->building_model->build_building_db($data_insert);

                    if ($build_building) {
                        $data['infoMessage'] = 'building_built';
                        $this->session->set_flashdata('update_token', time());
                        $currentUserID = $this->users_model->get_user_id();
                        call_achievements_check(['id_sector' => '*', 'id_resort' => $currentResortID, 'id_building' => $id_building, 'type' => $building_type, 'level' => $level], 'build');
                        call_achievements_check(['id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building], 'build_amount');
                        $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'].$this->lang->line($building_type)['title_sing'].$this->lang->line('logs')['has_started']]);
                        log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'].$this->lang->line($building_type)['title_sing'].$this->lang->line('logs')['has_started']]);
                    } else {
                        $data['infoMessage'] = 'building_not_built';
                    }
                } else {
                    $data['infoMessage'] = 'not_enough_money';
                }
            } else {
                $data['infoMessage'] = 'building_one_at_a_time';
            }
        } else {
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }

    // ---------------------------------------------------------------
    // upgrade_building
    // ---------------------------------------------------------------
    public function upgrade_building($currentResortID, $building_type, $level) {
        if (!in_array($building_type, $this->off_season_types))
            redirect('off_season_controller');

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $building_generic_info_data = $this->building_model->get_generic_building_data($building_type, $level);
        if ($building_generic_info_data->num_rows() == 0) {
            redirect('off_season_controller');
            return;
        }
        $building_generic_info_dataArray = $building_generic_info_data->row();
        $id_building = $building_generic_info_dataArray->id_building;

        $count_this_building_under_construction = count_this_building_level($building_type, '', '4');
        $count_num_building_previous_level      = count_this_building_level($building_type, $level - 1);
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if (!$pageWasRefreshed) {
            if ($count_num_building_previous_level >= '1') {
                if ($count_this_building_under_construction == 0) {
                    $cost_building  = $building_generic_info_dataArray->building_cost;
                    $gain_reputation = $building_generic_info_dataArray->reputation;
                    $cash_player    = $this->users_model->get_cash_player();
                    $money_after_payment = $cash_player - $cost_building;

                    if ($money_after_payment >= 0) {
                        if ($this->users_model->pay_item($cost_building, $cash_player)) {
                            $cash_player = $this->users_model->get_cash_player();
                            $this->session->set_userdata('cash', $cash_player);
                            add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                            add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                        }
                        $this->users_model->add_reputation($gain_reputation);
                        $reputation_player = $this->users_model->get_reputation_player();
                        $this->session->set_userdata('reputation', $reputation_player);

                        $end_construction = calculate_end_construction($building_type, $level);
                        $data_upg = [
                            'level'            => $level,
                            'end_construction' => $end_construction,
                            'id_status'        => '4',
                        ];
                        $build_building = $this->building_model->update_building_db($currentResortID, $building_type, $level - 1, $data_upg);

                        if ($build_building) {
                            $data['infoMessage'] = 'building_upgraded';
                            $currentUserID = $this->users_model->get_user_id();
                            call_achievements_check(['id_resort' => $currentResortID, 'id_building' => $id_building, 'level' => $level, 'type' => $building_type], 'upgrade');
                            call_achievements_check(['id_resort' => $currentResortID, 'quantity' => $cost_building], 'upgrade_amount');
                            call_achievements_check(['id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building], 'build_amount');
                            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$this->lang->line($building_type)['title_sing'].' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']]);
                            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'].$this->lang->line($building_type)['title_sing'].' '.$this->lang->line('logs')['level'].$level.$this->lang->line('logs')['has_started']]);
                        } else {
                            $data['infoMessage'] = 'building_not_upgraded';
                        }
                    } else {
                        $data['infoMessage'] = 'not_enough_money';
                    }
                } else {
                    $data['infoMessage'] = 'building_one_at_a_time';
                }
            } else {
                $data['infoMessage'] = 'building_not_built_previous';
            }
        } else {
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }

    // ---------------------------------------------------------------
    // rush
    // ---------------------------------------------------------------
    public function rush($building_type, $level) {
        if (!in_array($building_type, $this->off_season_types))
            redirect('off_season_controller');

        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if (!$pageWasRefreshed) {
            $building_data       = $this->building_model->get_time_left_for_building_db($currentResortID, $building_type, $level);
            $building_data_array = $building_data->row();
            $end_construction    = $building_data_array->end_construction;
            $timestamp           = strtotime($end_construction." UTC");
            $time_left_value     = $timestamp - time();

            if ($time_left_value > 0) {
                $genepis_required_to_rush = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                $genepis_available        = $this->users_model->get_user_genepis_amount($currentUserID);

                if ($genepis_required_to_rush <= $genepis_available) {
                    $this->users_model->remove_genepis_cost_DB($genepis_required_to_rush);
                    $data_log = $this->lang->line('home')['you_have_rushed'].' '.$this->lang->line($building_type)['title_sing'].' '.$this->lang->line('home')['for'].' '.$genepis_required_to_rush.' '.$this->lang->line('home')['genepis_title'];
                    $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log]);
                    log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log]);
                    $this->building_model->complete_construction_DB($building_data_array->id_created_buildings, 'game_created_buildings', 'id_created_buildings', 'end_construction');
                    $data['infoMessage'] = 'rush_completed';
                } else {
                    $data['infoMessage'] = 'not_enough_genepis';
                }
            } else {
                $data['infoMessage'] = 'already_completed';
            }
        } else {
            $data['infoMessage'] = '';
        }
        $this->index($data);
    }
}
