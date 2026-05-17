<?php
/**
 * Event Venues controller
 * Manages buildings required for tournaments: housing_complex, icerink, curling_center, open_stage
 */
class Event_venues_controller extends CI_Controller {

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
        $ci->lang->load('building', $siteLang);
        $ci->lang->load('logs', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('logs_model');
    }

    /**
     * index    Main function – displays all event venue buildings
     */
    public function index($data = NULL) {
        $data['mainTitle']        = '<h2>' . $this->lang->line('event_venues_buildings')['titleMain'] . '</h2>';
        $data['introEventVenues'] = '<div>' . $this->lang->line('event_venues_buildings')['intro'] . '</div>';

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated  = $this->users_model->check_account_activated($currentUserID);

        if ($user_activated) {
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);
            if ($checkIfResortExists->num_rows() > 0) {

                $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');
                if ($tourist_info_data->num_rows() == 1) {
                    $data['hideBuilding'] = false;
                    $this->ensure_event_venue_buildings_exist();
                    $data1 = $this->eventVenuesBuildingBlock($currentResortID);
                    $data  = array_merge($data, $data1);
                } else {
                    $data['hideBuilding'] = true;
                    $data['infoMessage']  = 'tourist_info_required';
                }

                $data['main_content'] = 'eventVenues';
                $this->load->view('templates/default', $data);
            } else {
                $this->session->set_flashdata('error', 'no_resort');
                redirect('resort_controller');
            }
        } else {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    /**
     * eventVenuesBuildingBlock    Builds the data arrays for all four event venue building types
     *
     * @param  int   $currentResortID
     * @return array
     */
    public function eventVenuesBuildingBlock($currentResortID) {
        $building_type_array = array('housing_complex', 'icerink', 'curling_center', 'open_stage');
        $name_language       = 'name_' . $this->session->userdata('site_lang');
        $currentUserID       = $this->users_model->get_user_id();
        $data                = array();

        foreach ($building_type_array as $type) {
            $data['title'][$type] = '<h3>' . $this->lang->line($type)['title'] . '</h3>';
            $data['logo'][$type]  = '<img src="' . base_url('img/icons/' . $type . '.png') . '" title="' . $this->lang->line($type)['title'] . '"/>';
            $data['desc'][$type]  = $this->lang->line($type)['desc'];

            for ($i = 1; $i <= 3; $i++) {
                $building_data = $this->building_model->get_generic_building_data($type, $i);

                $data['pre_buildingTime'][$type][$i]  = '';
                $data['post_buildingTime'][$type][$i] = '';

                if ($building_data->num_rows() > 0) {
                    $building_dataArray = $building_data->row();

                    $data['infrastructureName'][$type][$i] = $building_dataArray->$name_language;
                    $data['buildingCost'][$type][$i]        = number_format($building_dataArray->building_cost, 0, ',', ' ');
                    $data['reputation'][$type][$i]          = number_format($building_dataArray->reputation, 0, ',', ' ');
                    $data['capacity'][$type][$i]            = number_format($building_dataArray->capacity, 0, ',', ' ');
                    $data['daily_cost'][$type][$i]          = number_format($building_dataArray->daily_cost, 0, ',', ' ');

                    // Construction time
                    $count_under_construction = count_this_building_level($type, $i, '4');
                    $time_left_value          = 0;

                    if ($count_under_construction == '1') {
                        $timestamp       = strtotime(get_time_left_for_building($currentResortID, $type, $i) . " UTC");
                        $currenttime     = time();
                        $time_left_value = $timestamp - $currenttime;

                        if ($time_left_value <= 0) {
                            $data['pre_buildingTime'][$type][$i]  = '<a href="' . base_url() . 'event_venues_controller/"><div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('home')['wait_tooltip'] . '">';
                            $data['post_buildingTime'][$type][$i] = '</div></a>';
                            $data['buildingTime'][$type][$i]      = $this->lang->line('home')['wait'];
                        } else {
                            $data['buildingTime'][$type][$i] = gmdate("Y-m-d H:i:s", $timestamp);
                        }
                    } else {
                        $data['buildingTime'][$type][$i] = display_friendly_time($building_dataArray->building_time / ACCELERATOR_FACTOR, '');
                    }

                    // Rush button
                    $button_rush = '';
                    if ($time_left_value > 0) {
                        $genepis_required = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                        $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
                        if ($genepis_required <= $genepis_available) {
                            $button_rush = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('home')['speed_up_for_genepis'] . '"><a href="' . base_url('event_venues_controller/rush/' . $type . '/' . $i) . '"><button class="btn btn-success">' . $this->lang->line('home')['rush'] . ' ' . $this->lang->line('home')['for'] . ' ' . $genepis_required . ' ' . $this->lang->line('home')['genepis_title'] . '</button></a></div>';
                        } else {
                            $button_rush = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('home')['not_enough_genepis_to_rush'] . '"><a href="' . base_url('genepis_controller') . '"><button class="btn btn-warning">' . $this->lang->line('home')['not_enough_genepis'] . '</button></a></div>';
                        }
                    }

                    // Build/Upgrade button
                    $count_any_under_construction  = count_this_building_level($type, '', '4');
                    $count_previous_level_built    = count_this_building_level($type, $i - 1);

                    if ($count_any_under_construction == 0) {
                        if ($i == 1) {
                            $data['button'][$type][$i] = '<td><a href="' . base_url() . 'event_venues_controller/build_building/' . $currentResortID . '/' . $type . '/' . $i . '"><button class="btn btn-success">' . $this->lang->line('building')['build'] . '</button></a></td>';
                        } else {
                            if ($count_previous_level_built == 0) {
                                $data['button'][$type][$i] = '<td><button class="btn btn-danger disabled">' . $this->lang->line('building')['upgrade'] . '</button></td>';
                            } else {
                                $data['button'][$type][$i] = '<td><a href="' . base_url() . 'event_venues_controller/upgrade_building/' . $currentResortID . '/' . $type . '/' . $i . '"><button class="btn btn-success">' . $this->lang->line('building')['upgrade'] . '</button></a></td>';
                            }
                        }
                    } else {
                        if ($count_under_construction == '0') {
                            if ($i == 1) {
                                $data['button'][$type][$i] = '<td><button class="btn btn-warning disabled">' . $this->lang->line('building')['build'] . '</button></td>';
                            } else {
                                $data['button'][$type][$i] = '<td><button class="btn btn-warning disabled">' . $this->lang->line('building')['upgrade'] . '</button></td>';
                            }
                        } else {
                            if ($i == 1) {
                                $data['button'][$type][$i] = '<td><button class="btn btn-warning disabled">' . $this->lang->line('building')['building'] . '</button>' . $button_rush . '</td>';
                            } else {
                                $data['button'][$type][$i] = '<td><button class="btn btn-warning disabled">' . $this->lang->line('building')['upgrading'] . '</button>' . $button_rush . '</td>';
                            }
                        }
                    }
                } else {
                    // Building catalogue row missing – show placeholder
                    $data['infrastructureName'][$type][$i] = '-';
                    $data['buildingCost'][$type][$i]        = '-';
                    $data['buildingTime'][$type][$i]        = '-';
                    $data['reputation'][$type][$i]          = '-';
                    $data['capacity'][$type][$i]            = '-';
                    $data['daily_cost'][$type][$i]          = '-';
                    $data['button'][$type][$i]              = '<td></td>';
                }
            }
        }
        return $data;
    }

    /**
     * build_building   Builds level 1 of the given event venue building
     */
    public function build_building($currentResortID, $building_type, $level = '1') {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $building_generic_info_data = $this->building_model->get_generic_building_data($building_type, $level);
        if ($building_generic_info_data->num_rows() > 0) {
            $building_generic_info_dataArray = $building_generic_info_data->row();
            $id_building = $building_generic_info_dataArray->id_building;
        }

        $count_this_building_under_construction = count_this_building_level($building_type, '', '4');

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if (!$pageWasRefreshed) {
            if ($count_this_building_under_construction == 0) {
                $cost_building   = $building_generic_info_dataArray->building_cost;
                $gain_reputation = $building_generic_info_dataArray->reputation;
                $cash_player     = $this->users_model->get_cash_player();
                $money_after_payment = $cash_player - $cost_building;

                if ($money_after_payment >= 0) {
                    if ($removeCashQuery = $this->users_model->pay_item($cost_building, $cash_player)) {
                        $cash_player  = $this->users_model->get_cash_player();
                        $updated_cash = $this->session->set_userdata('cash', $cash_player);
                        add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                        add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                    }
                    $add_reputation      = $this->users_model->add_reputation($gain_reputation);
                    $reputation_player   = $this->users_model->get_reputation_player();
                    $updated_reputation  = $this->session->set_userdata('reputation', $reputation_player);

                    $end_construction = calculate_end_construction($building_type, $level);
                    $data_insert = array(
                        'id_resort'        => $currentResortID,
                        'id_building'      => $id_building,
                        'type'             => $building_type,
                        'level'            => '1',
                        'end_construction' => $end_construction,
                        'id_status'        => '4',
                    );
                    $build_building = $this->building_model->build_building_db($data_insert);
                    if ($build_building) {
                        $data['infoMessage'] = 'building_built';
                        $this->session->set_flashdata('update_token', time());
                        $currentUserID = $this->users_model->get_user_id();
                        $data_achievement = array(
                            'id_sector'  => '*',
                            'id_resort'  => $currentResortID,
                            'id_building'=> $id_building,
                            'type'       => $building_type,
                            'level'      => '1',
                        );
                        call_achievements_check($data_achievement, 'build');
                        call_achievements_check(array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building), 'build_amount');
                        $this->logs_model->call_notification_DB(array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'] . $this->lang->line($building_type)['title_sing'] . $this->lang->line('logs')['has_started']));
                        log_user_action(array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['construction_of'] . $this->lang->line($building_type)['title_sing'] . $this->lang->line('logs')['has_started']));
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

    /**
     * upgrade_building     Upgrades an event venue building to the given level
     */
    public function upgrade_building($currentResortID, $building_type, $level) {
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $building_generic_info_data = $this->building_model->get_generic_building_data($building_type, $level);
        if ($building_generic_info_data->num_rows() > 0) {
            $building_generic_info_dataArray = $building_generic_info_data->row();
            $id_building = $building_generic_info_dataArray->id_building;
        }

        $count_this_building_under_construction = count_this_building_level($building_type, '', '4');
        $count_num_building_previous_level      = count_this_building_level($building_type, $level - 1);

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if (!$pageWasRefreshed) {
            if ($count_num_building_previous_level >= '1') {
                if ($count_this_building_under_construction == 0) {
                    $cost_building   = $building_generic_info_dataArray->building_cost;
                    $gain_reputation = $building_generic_info_dataArray->reputation;
                    $cash_player     = $this->users_model->get_cash_player();
                    $money_after_payment = $cash_player - $cost_building;

                    if ($money_after_payment >= 0) {
                        if ($removeCashQuery = $this->users_model->pay_item($cost_building, $cash_player)) {
                            $cash_player  = $this->users_model->get_cash_player();
                            $updated_cash = $this->session->set_userdata('cash', $cash_player);
                            add_cost_stat_table($currentResortID, $cost_building, 'cost_purchases');
                            add_cost_stat_table($currentResortID, $cost_building, 'expenses');
                        }
                        $add_reputation     = $this->users_model->add_reputation($gain_reputation);
                        $reputation_player  = $this->users_model->get_reputation_player();
                        $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);

                        $end_construction = calculate_end_construction($building_type, $level);
                        $data_upg = array(
                            'level'            => $level,
                            'end_construction' => $end_construction,
                            'id_status'        => '4',
                        );
                        $build_building = $this->building_model->update_building_db($currentResortID, $building_type, $level - 1, $data_upg);
                        if ($build_building) {
                            $data['infoMessage'] = 'building_upgraded';
                            $currentUserID = $this->users_model->get_user_id();
                            $data_ach = array(
                                'id_resort'  => $currentResortID,
                                'id_building'=> $id_building,
                                'level'      => $level,
                                'type'       => $building_type,
                            );
                            call_achievements_check($data_ach, 'upgrade');
                            call_achievements_check(array('id_resort' => $currentResortID, 'quantity' => $cost_building), 'upgrade_amount');
                            call_achievements_check(array('id_sector' => '*', 'id_resort' => $currentResortID, 'quantity' => $cost_building), 'build_amount');
                            $this->logs_model->call_notification_DB(array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'] . $this->lang->line($building_type)['title_sing'] . ' ' . $this->lang->line('logs')['level'] . $level . $this->lang->line('logs')['has_started']));
                            log_user_action(array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['building'], 'data' => $this->lang->line('logs')['upgrade_of'] . $this->lang->line($building_type)['title_sing'] . ' ' . $this->lang->line('logs')['level'] . $level . $this->lang->line('logs')['has_started']));
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

    /**
     * rush     Rush construction of an event venue building using genepis
     */
    public function rush($building_type, $level) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
            redirect('home_controller');

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

        if (!$pageWasRefreshed) {
            $building_data       = $this->building_model->get_time_left_for_building_db($currentResortID, $building_type, $level);
            $building_data_array = $building_data->row();

            if ($building_data_array === null) {
                $data['infoMessage'] = 'already_completed';
                $this->index($data);
                return;
            }

            $end_construction = $building_data_array->end_construction;
            $timestamp        = strtotime($end_construction . " UTC");
            $currenttime      = time();
            $time_left_value  = $timestamp - $currenttime;

            if ($time_left_value > 0) {
                $genepis_required  = round($time_left_value / SECONDS_PER_GENEPIS, 0);
                $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);

                if ($genepis_required <= $genepis_available) {
                    $this->users_model->remove_genepis_cost_DB($genepis_required);
                    $data_log = $this->lang->line('home')['you_have_rushed'] . ' ' . $this->lang->line($building_type)['title_sing'] . ' ' . $this->lang->line('home')['for'] . ' ' . $genepis_required . ' ' . $this->lang->line('home')['genepis_title'];
                    $this->logs_model->call_notification_DB(array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log));
                    log_user_action(array('id_player' => $currentUserID, 'type' => $this->lang->line('home')['genepis_no_accent'], 'data' => $data_log));
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

    /**
     * ensure_event_venue_buildings_exist   Seeds the game_buildings catalogue rows for event
     *                                       venue buildings if they are not already present.
     */
    private function ensure_event_venue_buildings_exist() {
        $catalogue = array(
            'housing_complex' => array(
                array('level' => 1, 'name_english' => 'Housing Complex',   'name_french' => 'Complexe immobilier',         'building_time' => 259200, 'building_cost' => 500000,  'capacity' => 100, 'reputation' => 10, 'max_income' => 0, 'daily_cost' => 1000),
                array('level' => 2, 'name_english' => 'Athletes Village',   'name_french' => 'Village des athlètes',        'building_time' => 432000, 'building_cost' => 1500000, 'capacity' => 200, 'reputation' => 20, 'max_income' => 0, 'daily_cost' => 2500),
                array('level' => 3, 'name_english' => 'Olympic Village',    'name_french' => 'Village olympique',           'building_time' => 604800, 'building_cost' => 3000000, 'capacity' => 400, 'reputation' => 40, 'max_income' => 0, 'daily_cost' => 5000),
            ),
            'icerink' => array(
                array('level' => 1, 'name_english' => 'Ice Rink',           'name_french' => 'Patinoire',                   'building_time' => 259200, 'building_cost' => 1000000, 'capacity' => 50,  'reputation' => 15, 'max_income' => 0, 'daily_cost' => 3000),
                array('level' => 2, 'name_english' => 'Competition Ice Rink','name_french' => 'Patinoire de compétition',  'building_time' => 432000, 'building_cost' => 2500000, 'capacity' => 100, 'reputation' => 30, 'max_income' => 0, 'daily_cost' => 6000),
                array('level' => 3, 'name_english' => 'Olympic Ice Arena',  'name_french' => 'Arène olympique de glace',    'building_time' => 604800, 'building_cost' => 5000000, 'capacity' => 200, 'reputation' => 50, 'max_income' => 0, 'daily_cost' => 10000),
            ),
            'curling_center' => array(
                array('level' => 1, 'name_english' => 'Curling Center',     'name_french' => 'Halle de curling',            'building_time' => 172800, 'building_cost' => 600000,  'capacity' => 30,  'reputation' => 10, 'max_income' => 0, 'daily_cost' => 1500),
                array('level' => 2, 'name_english' => 'Curling Complex',    'name_french' => 'Complexe de curling',         'building_time' => 345600, 'building_cost' => 1500000, 'capacity' => 60,  'reputation' => 20, 'max_income' => 0, 'daily_cost' => 3000),
                array('level' => 3, 'name_english' => 'World-Class Curling Center', 'name_french' => 'Centre de curling de classe mondiale', 'building_time' => 518400, 'building_cost' => 3000000, 'capacity' => 120, 'reputation' => 35, 'max_income' => 0, 'daily_cost' => 5000),
            ),
            'open_stage' => array(
                array('level' => 1, 'name_english' => 'Open Stage',         'name_french' => 'Scène de concert',            'building_time' => 86400,  'building_cost' => 300000,  'capacity' => 500, 'reputation' => 15, 'max_income' => 0, 'daily_cost' => 500),
                array('level' => 2, 'name_english' => 'Concert Stage',      'name_french' => 'Grande scène de concert',     'building_time' => 259200, 'building_cost' => 800000,  'capacity' => 1000,'reputation' => 25, 'max_income' => 0, 'daily_cost' => 1000),
                array('level' => 3, 'name_english' => 'Grand Arena Stage',  'name_french' => 'Scène d\'arène',              'building_time' => 432000, 'building_cost' => 2000000, 'capacity' => 2000, 'reputation' => 40, 'max_income' => 0, 'daily_cost' => 2000),
            ),
        );

        foreach ($catalogue as $type => $levels) {
            foreach ($levels as $lvl) {
                $exists = $this->db
                    ->from('game_buildings')
                    ->where('type', $type)
                    ->where('level', $lvl['level'])
                    ->count_all_results();

                if ($exists === 0) {
                    $this->db->insert('game_buildings', array(
                        'type'          => $type,
                        'level'         => $lvl['level'],
                        'name_english'  => $lvl['name_english'],
                        'name_french'   => $lvl['name_french'],
                        'building_time' => $lvl['building_time'],
                        'building_cost' => $lvl['building_cost'],
                        'capacity'      => $lvl['capacity'],
                        'reputation'    => $lvl['reputation'],
                        'max_income'    => $lvl['max_income'],
                        'daily_cost'    => $lvl['daily_cost'],
                    ));
                }
            }
        }
    }
}
