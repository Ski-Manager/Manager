<?php
/**
 * Special Events controller
 */
class Special_events_controller extends CI_Controller {

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
        $ci->lang->load('special_events', $siteLang);
        $ci->lang->load('building', $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('building_model');
        $this->load->model('special_events_model');
    }

    public function index($data = NULL) {
        $data['title'] = '<h2>' . $this->lang->line('special_events')['titleMain'] . '</h2>';
        $data['intro'] = '<div>' . $this->lang->line('special_events')['intro'] . '</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();

        $data['eventLogo']  = '<img src="' . base_url('img/icons/tournaments.jpg') . '" title="' . $this->lang->line('special_events')['titleMain'] . '"/>';
        $data['eventDesc']  = $this->lang->line('special_events')['desc'];

        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated  = $this->users_model->check_account_activated($currentUserID);

        $history_count_all = $this->special_events_model->history_count_all_special_events($currentResortID);
        $data['history_all_events'] = '<b>' . $this->lang->line('special_events')['you_have_organized_total'] . ' ' . $history_count_all . ' ' . $this->lang->line('special_events')['events_in_resort'] . '</b>';

        $data['for_help_with_events'] = $this->lang->line('special_events')['for_help_with_events'];

        $player_preferred_lang_hist = $this->users_model->get_user_preferred_lang($currentUserID);
        $name_language_hist = 'name_' . $player_preferred_lang_hist;
        $event_stats = $this->special_events_model->get_special_event_stats_DB($currentResortID);
        $data['event_stats']   = ($event_stats->num_rows() > 0) ? $event_stats->row() : null;
        $data['event_history'] = $this->special_events_model->get_special_event_history_with_info_DB($currentResortID, $name_language_hist);

        if ($user_activated) {
            $last_event = $this->special_events_model->select_last_special_event_player($currentResortID);

            // Lazy-completion: if the cron missed completing a special event, do it now
            if ($last_event->num_rows() > 0) {
                $last_event_data = $last_event->row();
                if ($last_event_data->completed == 0) {
                    date_default_timezone_set('UTC');
                    $end_date_check = gmdate('Y-m-d', strtotime($last_event_data->end_date));
                    $now_check      = gmdate('Y-m-d');
                    if ($end_date_check <= $now_check) {
                        $this->_complete_overdue_special_event($last_event_data, $currentResortID, $currentUserID);
                        // Re-fetch updated state
                        $last_event = $this->special_events_model->select_last_special_event_player($currentResortID);
                    }
                }
            }

            if ($last_event->num_rows() > 0) {
                $last_event_data = $last_event->row();
                if ($last_event_data->completed == 0) {
                    $data['lastEventTable']  = '<div id="info_message_div_event">' . $this->lang->line('special_events')['there_is_ongoing_event'] . '</div>';
                    $data['lastEventTable'] .= $this->ongoingEventTable($last_event);
                } else {
                    $data['lastEventTable']  = '<div id="info_message_div_event">' . $this->lang->line('special_events')['no_ongoing_event_completed_below'] . '</div>';
                    $data['lastEventTable'] .= $this->completedEventTable($last_event);
                }
            } else {
                $data['lastEventTable'] = '<div id="info_message_div_event">' . $this->lang->line('special_events')['no_event_history'] . '</div>';
            }

            $data1 = $this->specialEventsListBlock($currentUserID);
            $data  = array_merge($data, $data1);

            $data['main_content'] = 'special_events';
            $this->load->view('templates/default', $data);
        } else {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    /**
     * _complete_overdue_special_event
     * Completes a special event that has passed its end date but was not marked
     * completed by the cron (e.g. cron was down). Called lazily from index().
     * Guards against double-payment: cash/reputation are only added if this call
     * is the one that actually flips completed = 0 → 1 (affected_rows = 1).
     */
    protected function _complete_overdue_special_event($last_event_data, $currentResortID, $currentUserID) {
        $id_started_special_event = $last_event_data->id_started_special_event;
        $id_special_event         = $last_event_data->id_special_event;
        $total_revenue            = (int)($last_event_data->aggregated_revenue ?? 0);

        $event_data = $this->special_events_model->get_special_event_data($id_special_event);
        if ($event_data->num_rows() === 0) return;
        $event_data_array         = $event_data->row();
        $earned_reputation_points = $event_data_array->reputation_points;

        // Mark completed first to prevent double-payment on concurrent calls
        $marked = $this->special_events_model->mark_special_events_completed_DB($id_started_special_event);
        if ($marked == 1) {
            $this->special_events_model->update_resort_column($currentUserID, 'reputation', $earned_reputation_points);
            $this->special_events_model->update_resort_column($currentUserID, 'cash', $total_revenue);
            add_cost_stat_table($currentResortID, $total_revenue, 'rev_special_events');
            add_cost_stat_table($currentResortID, $total_revenue, 'revenue');
            // Refresh the cash stored in the session so the sidebar updates
            $cash_player = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $cash_player);
        }
    }


    public function completedEventTable($last_event) {
        $currentUserID   = $this->users_model->get_user_id();
        $last_event_data = $last_event->row();

        $event_data       = $this->special_events_model->get_special_event_data($last_event_data->id_special_event);
        $event_data_array = $event_data->row();

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $column_name = 'name_' . $player_preferred_lang;
        $event_name  = $event_data_array->$column_name;

        $percentage_achieved = 100;
        $total_visitors = $last_event_data->aggregated_visitors;
        $total_revenue  = $last_event_data->aggregated_revenue;

        $area  = '<table class="table table-responsive special_events event_completed" align="center"><tbody>';
        $area .= '<tr><th>' . $this->lang->line('special_events')['last_event'] . '</th>';
        $area .= '<td colspan="1">' . htmlspecialchars($event_name, ENT_QUOTES, 'UTF-8') . '</td>';
        $area .= '<th>' . $this->lang->line('special_events')['ended'] . '</th>';
        $area .= '<td>' . htmlspecialchars($last_event_data->end_date, ENT_QUOTES, 'UTF-8') . '</td>';
        $area .= '<td width="100" rowspan="3"><div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['event_completed'] . '"><div class="chart center" id="ongoing_event_progress" data-percent="' . $percentage_achieved . '"></div></div></td></tr>';
        $area .= '<tr><th colspan="4">' . $this->lang->line('special_events')['final_results'] . '</th></tr>';
        $area .= '<tr><th>' . $this->lang->line('special_events')['visitors_cap'] . '</th>';
        $area .= '<td>' . number_format($total_visitors, 0, ',', ' ') . '</td>';
        $area .= '<th rowspan="2">' . $this->lang->line('home')['reputation'] . '</th>';
        $area .= '<td rowspan="2">+ ' . number_format($event_data_array->reputation_points, 0, ',', ' ') . '</td></tr>';
        $area .= '<tr data-id_special_event="' . $last_event_data->id_special_event . '"><th>' . $this->lang->line('special_events')['revenue_cap'] . '</th>';
        $area .= '<td>+ ' . number_format($total_revenue, 0, ',', ' ') . '€</td>';
        $area .= '<td class="no_border" style="text-align:center !important;" id="start_event_button_column-' . $last_event_data->id_special_event . '" ><div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('special_events')['start_again_event'] . '"><button class="btn btn-success start_special_event_button">' . $this->lang->line('special_events')['start_again'] . '</button></div></td></tr>';
        $area .= '</tbody></table>';

        return $area;
    }

    public function ongoingEventTable($last_event) {
        $currentUserID   = $this->users_model->get_user_id();
        $last_event_data = $last_event->row();

        $event_data       = $this->special_events_model->get_special_event_data($last_event_data->id_special_event);
        $event_data_array = $event_data->row();

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $column_name = 'name_' . $player_preferred_lang;
        $event_name  = $event_data_array->$column_name;

        date_default_timezone_set('UTC');
        $start_date        = strtotime(gmdate('Y-m-d', strtotime($last_event_data->started_datetime)));
        $now               = strtotime(gmdate('Y-m-d', strtotime('now')));
        $nb_days_since_start = ($now - $start_date) / 60 / 60 / 24;
        $percentage_achieved = number_format(min(100, ($nb_days_since_start / (ceil($event_data_array->duration / ACCELERATOR_FACTOR))) * 100), 0, ',', ' ');

        $total_visitors = $last_event_data->aggregated_visitors;
        $total_revenue  = $last_event_data->aggregated_revenue;

        $area  = '<table class="table table-responsive special_events event_ongoing" align="center"><tbody>';
        $area .= '<tr><th>' . $this->lang->line('special_events')['ongoing_event_header'] . '</th>';
        $area .= '<td colspan="2">' . htmlspecialchars($event_name, ENT_QUOTES, 'UTF-8') . '</td>';
        $area .= '<th colspan="2">' . $this->lang->line('special_events')['partial_results'] . '</th>';
        $area .= '<td width="100" rowspan="3"><div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['ongoing_event_header'] . '"><div class="chart center" id="ongoing_event_progress" data-percent="' . $percentage_achieved . '"></div></div></td>';
        $area .= '</tr>';
        $area .= '<tr><th>' . $this->lang->line('special_events')['started'] . '</th>';
        $area .= '<td>' . htmlspecialchars($last_event_data->started_datetime, ENT_QUOTES, 'UTF-8') . '</td>';
        $area .= '<td rowspan="2">' . $this->lang->line('home')['big_day'] . ' ' . $nb_days_since_start . ' / ' . ceil($event_data_array->duration / ACCELERATOR_FACTOR) . '</td>';
        $area .= '<th>' . $this->lang->line('special_events')['visitors_cap'] . '</th>';
        $area .= '<td>' . number_format($total_visitors, 0, ',', ' ') . '</td></tr>';
        $area .= '<tr><th>' . $this->lang->line('special_events')['ending'] . '</th>';
        $area .= '<td>' . htmlspecialchars($last_event_data->end_date, ENT_QUOTES, 'UTF-8') . '</td>';
        $area .= '<th>' . $this->lang->line('special_events')['revenue_cap'] . '</th>';
        $area .= '<td>+ ' . number_format($total_revenue, 0, ',', ' ') . '€</td></tr>';
        $area .= '</tbody></table>';

        return $area;
    }

    public function specialEventsListBlock($currentUserID) {
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $name_language        = 'name_' . $this->session->userdata('site_lang');
        $description_language = 'description_' . $this->session->userdata('site_lang');

        $data['table_events'] = '';
        $data['table_events'] .= '<table class="special_events_table" align="center"><tbody>';
        $events_data = $this->special_events_model->get_all_special_events_data($name_language, $description_language, 1);

        $count = 0;
        foreach ($events_data->result() as $event_row) {
            $data_to_parse        = $this->check_eligibility_special_event($currentResortID, $event_row, $count);
            $data['table_events'] .= $data_to_parse['data'];
        }
        $data['table_events'] .= '</tbody></table>';

        return $data;
    }

    protected function check_eligibility_infrastructure($currentResortID, $infrastructure_type, $level_required) {
        $building_built_array      = $this->building_model->get_all_created_buildings_for_player($currentResortID, $infrastructure_type, $level_required, '>=');
        $built_buildings           = $building_built_array->row();
        $number_of_this_building_built = $built_buildings->count;
        if ($number_of_this_building_built > 0) {
            return true;
        }
        return false;
    }

    protected function check_eligibility_cash($currentResortID, $running_cost) {
        $player_cash = $this->users_model->get_cash_player();
        if (isset($player_cash) && $player_cash >= $running_cost) {
            return true;
        }
        return false;
    }

    protected function check_eligibility_required_prestige($currentResortID, $required_prestige) {
        $player_prestige = $this->users_model->get_prestige_resort($currentResortID);
        if (isset($player_prestige) && $player_prestige >= $required_prestige) {
            return true;
        }
        return false;
    }

    protected function check_eligibility_special_event($currentResortID, $event_data_array, $count = NULL) {
        $name_language        = 'name_' . $this->session->userdata('site_lang');
        $description_language = 'description_' . $this->session->userdata('site_lang');

        $table_events = '';

        if ($count == 0) {
            $tr_class = 'class="ach_even" ';
            $count++;
        } else {
            $tr_class = 'class="ach_odd" ';
            $count = 0;
        }

        $e_id              = $event_data_array->id_special_event;
        $e_name            = $event_data_array->$name_language;
        $e_description     = $event_data_array->$description_language;
        $e_running_cost    = $event_data_array->running_cost;
        $e_expected_revenue  = $event_data_array->expected_revenue;
        $e_expected_visitors = $event_data_array->expected_visitors;
        $e_reputation_points = $event_data_array->reputation_points;
        $e_required_prestige = $event_data_array->required_prestige;
        $e_duration          = ceil($event_data_array->duration / ACCELERATOR_FACTOR);

        $e_required_open_stage      = $event_data_array->open_stage;
        $e_required_housing_complex = $event_data_array->housing_complex;

        $history_count = $this->special_events_model->history_count_special_event($currentResortID, $e_id);

        $infrastructure_array = [
            'open_stage'      => $e_required_open_stage,
            'housing_complex' => $e_required_housing_complex,
        ];

        $infrastructure_requirements = '<ul class="table_list_no_indent">';
        $eligibility_all_infrastructure = true;

        foreach ($infrastructure_array as $infrastructure_type => $level_required) {
            if ($level_required != 0) {
                $building_friendly_name = $this->special_events_model->get_building_friendly_name($infrastructure_type, $level_required, $name_language);
                $building_built_array   = $this->building_model->get_all_created_buildings_for_player($currentResortID, $infrastructure_type, $level_required);
                $built_buildings        = $building_built_array->row();
                $number_of_this_building_built = $built_buildings->count;

                $building_built_array_player = $this->building_model->get_created_buildings_for_player($currentResortID, $infrastructure_type);
                $eligibility_infrastructure  = $this->check_eligibility_infrastructure($currentResortID, $infrastructure_type, $level_required);

                if ($eligibility_infrastructure === true) {
                    $built_buildings_player    = $building_built_array_player->row();
                    $level_of_this_building_built = $built_buildings_player->level;
                    $tooltip_infrastructure    = '<div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['you_have_built_level'] . ' ' . $level_of_this_building_built . ' ' . $this->lang->line('special_events')['of_this_building'] . ' ' . $this->lang->line('special_events')['level'] . ' ' . $level_required . ' ' . $this->lang->line('special_events')['is_required'] . '">';
                    $infrastructure_class      = 'green_text';
                } else {
                    $built_or_not = $building_built_array_player->num_rows();
                    if ($built_or_not > 0) {
                        $built_buildings_player       = $building_built_array_player->row();
                        $level_of_this_building_built = $built_buildings_player->level;
                        $tooltip_infrastructure       = '<div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['only_upgraded_level'] . ' ' . $level_of_this_building_built . ' ' . $this->lang->line('special_events')['upgrade_to'] . ' ' . $level_required . ' ' . $this->lang->line('special_events')['be_eligible'] . '">';
                    } else {
                        $tooltip_infrastructure = '<div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['not_built'] . ' ' . $level_required . ' ' . $this->lang->line('special_events')['be_eligible'] . '">';
                    }
                    $infrastructure_class           = 'red_text';
                    $eligibility_all_infrastructure = false;
                }
                $infrastructure_requirements .= $tooltip_infrastructure . '<li class="' . $infrastructure_class . '">' . $building_friendly_name . ' (' . $this->lang->line('special_events')['lvl'] . ' ' . $level_required . ')</li></div>';
            }
        }
        $infrastructure_requirements .= '</ul>';

        $eligibility_cash     = $this->check_eligibility_cash($currentResortID, $e_running_cost);
        $eligibility_prestige = $this->check_eligibility_required_prestige($currentResortID, $e_required_prestige);

        if ($eligibility_cash === true) {
            $running_cost_class   = 'green_text';
            $running_cost_tooltip = '<div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['you_have_enough_cash'] . ' (' . number_format($e_running_cost, 0, ',', ' ') . '€) ' . $this->lang->line('special_events')['to_arrange_this_event'] . '">';
        } else {
            $running_cost_class   = 'red_text';
            $running_cost_tooltip = '<div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['you_need'] . ' ' . number_format($e_running_cost, 0, ',', ' ') . '€ ' . $this->lang->line('special_events')['to_arrange_this_event'] . '">';
        }

        $eligible_to_event = ($eligibility_all_infrastructure === true && $eligibility_cash === true && $eligibility_prestige === true);

        $prestige_class = $eligibility_prestige ? 'green_text' : 'red_text';

        // Check if there is already an ongoing special event
        $ongoing_event = false;
        $last_event    = $this->special_events_model->select_last_special_event_player($currentResortID);
        $last_event_data = $last_event->row();
        if ($last_event->num_rows() > 0 && $last_event_data->completed == 0) {
            $ongoing_event = true;
        }

        if ($eligible_to_event === false) {
            $class_start_button    = 'disabled';
            $start_button_tooltip  = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('special_events')['one_or_several_req'] . '">';
        } elseif ($ongoing_event === true) {
            $class_start_button   = 'disabled';
            $start_button_tooltip = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('special_events')['ongoing_event'] . '">';
        } else {
            $class_start_button   = '';
            $start_button_tooltip = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('special_events')['start'] . '">';
        }

        $outcome_cell  = '<ul class="table_list_no_bullet_no_indent">';
        $outcome_cell .= '<li class="' . $running_cost_class . '">' . $running_cost_tooltip . '<b>' . $this->lang->line('special_events')['running_costs'] . ':</b> ' . display_friendly_cash($e_running_cost) . '€</div></li>';
        $outcome_cell .= '<li><div class="tooltip tooltip-bottom" data-tip="' . number_format($e_expected_revenue, 0, ',', ' ') . '€"><b>' . $this->lang->line('special_events')['expected_revenue'] . ':</b> ' . display_friendly_cash($e_expected_revenue) . '€</div></li>';
        $outcome_cell .= '<li><b>' . $this->lang->line('special_events')['expected_visitors'] . ':</b> ' . number_format($e_expected_visitors, 0, ',', ' ') . '</li>';
        $outcome_cell .= '<li><b>' . $this->lang->line('special_events')['reputation_points'] . ':</b> +' . number_format($e_reputation_points, 0, ',', ' ') . '</li>';
        $outcome_cell .= '</ul>';

        $table_events .= '<tr ' . $tr_class . '>';
        $table_events .= '<th rowspan="3">' . htmlspecialchars($e_name, ENT_QUOTES, 'UTF-8') . '</th>';
        $table_events .= '<th rowspan="4">' . htmlspecialchars($e_description, ENT_QUOTES, 'UTF-8') . '</th>';
        $table_events .= '<th colspan="2"><b>' . $this->lang->line('special_events')['requirements'] . '</b></th>';
        $table_events .= '<th><b>' . $this->lang->line('special_events')['outcome'] . '</b></th>';
        $table_events .= '</tr>';

        $table_events .= '<tr ' . $tr_class . '>';
        $table_events .= '<td valign="top" colspan="2" class="nowrap_cell"><b>' . $this->lang->line('special_events')['infrastructure'] . ':</b>' . $infrastructure_requirements . '</td>';
        $table_events .= '<td valign="top" rowspan="2" class="nowrap_cell">' . $outcome_cell . '</td>';
        $table_events .= '</tr>';

        $table_events .= '<tr ' . $tr_class . '>';
        $table_events .= '<td class="nowrap_cell ' . $prestige_class . '" style="text-align:center !important;" colspan="2"><b>' . $this->lang->line('special_events')['prestige_required'] . ': </b>' . $e_required_prestige . '</td>';
        $table_events .= '</tr>';

        $table_events .= '<tr data-id_special_event="' . $e_id . '" ' . $tr_class . '>';

        if ($e_duration > 1)
            $days_to_display = $this->lang->line('home')['days'];
        else
            $days_to_display = $this->lang->line('home')['small_day'];

        $table_events .= '<td style="text-align:center !important;"><div class="tooltip tooltip-bottom" data-tip="' . $this->lang->line('special_events')['history_text'] . '"><b>' . $this->lang->line('special_events')['history'] . ':</b> ' . number_format($history_count, 0, ',', ' ') . '</div></td>';
        $table_events .= '<td style="text-align:center !important;" colspan="2"><b>' . $this->lang->line('special_events')['duration'] . ': </b>' . $e_duration . ' ' . $days_to_display . '</td>';
        $table_events .= '<td style="text-align:center !important;" colspan="2" id="start_event_button_column-' . $e_id . '" >'
                . $start_button_tooltip . '<button ' . $class_start_button . ' class="btn btn-success start_special_event_button">' . $this->lang->line('special_events')['start'] . '</button></div>'
                . '</td>';
        $table_events .= '</tr>';
        $table_events .= '<tr class="no_border"><td colspan="5" class="no_border"></td></tr>';

        $returned_data = ['data' => $table_events, 'eligible_to_event' => $eligible_to_event];

        return $returned_data;
    }

    public function start_special_event() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $id_special_event = trim($this->input->post('id_special_event', TRUE));

        $event_data = $this->special_events_model->get_special_event_data($id_special_event);
        if ($event_data->num_rows() === 0) {
            echo json_encode(['started' => false, 'infoMessage' => 'something_went_wrong']);
            return;
        }
        $event_data_array = $event_data->row();

        $eligibility = $this->check_eligibility_special_event($currentResortID, $event_data_array);

        if ($eligibility['eligible_to_event'] === true) {
            $last_event      = $this->special_events_model->select_last_special_event_player($currentResortID);
            $last_event_data = $last_event->row();

            $is_last_completed = 1;
            if ($last_event->num_rows() > 0) {
                $is_last_completed = $last_event_data->completed;
            }

            if ($last_event->num_rows() == 0 || $is_last_completed == 1) {
                $running_cost     = $event_data_array->running_cost;
                $id_special_event = $event_data_array->id_special_event;
                $duration         = ceil($event_data_array->duration / ACCELERATOR_FACTOR);

                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $column_name = 'name_' . $player_preferred_lang;
                $this->lang->load('logs', $player_preferred_lang);
                $event_name = $event_data_array->$column_name;

                $cash_player = $this->users_model->get_cash_player();
                if ($this->users_model->pay_item($running_cost, $cash_player)) {
                    add_cost_stat_table($currentResortID, $running_cost, 'cost_special_events');
                    add_cost_stat_table($currentResortID, $running_cost, 'expenses');

                    $today           = strtotime('now');
                    $today_GMT       = gmdate('Y-m-d H:i:s', $today);
                    $end_date_GMT    = gmdate('Y-m-d', strtotime('+' . $duration . ' days', $today));

                    $cash_player  = $this->users_model->get_cash_player();
                    $this->session->set_userdata('cash', $cash_player);

                    $data_insert = [
                        'id_resort'          => $currentResortID,
                        'id_special_event'   => $id_special_event,
                        'started_datetime'   => $today_GMT,
                        'end_date'           => $end_date_GMT,
                        'aggregated_visitors' => 0,
                        'aggregated_revenue'  => 0,
                    ];

                    if ($this->special_events_model->start_special_event_DB($data_insert)) {
                        if ($duration > 1)
                            $days_to_display = $this->lang->line('home')['days'];
                        else
                            $days_to_display = $this->lang->line('home')['small_day'];

                        $text_logs = $event_name . ' ' . $this->lang->line('special_events')['started_small'] . ' ' . $this->lang->line('special_events')['and_will_last'] . ' ' . $duration . ' ' . $days_to_display . '.';
                        $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['special_events'], 'data' => $text_logs]);
                        log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['special_events'], 'data' => $text_logs]);

                        $last_event    = $this->special_events_model->select_last_special_event_player($currentResortID);
                        $lastEventTable  = '<div id="info_message_div_event">' . $this->lang->line('special_events')['event_started_confirmation'] . '</div>';
                        $lastEventTable .= $this->ongoingEventTable($last_event);

                        echo json_encode([
                            'started'        => true,
                            'infoMessage'    => 'event_started',
                            'start_button_cell' => '<div style="display:inline;" class="tooltip tooltip-left" data-tip="' . $this->lang->line('special_events')['event_started'] . '"><button disabled class="btn btn-warning start_special_event_button">' . $this->lang->line('special_events')['started'] . '</button></div>',
                            'lastEventTable' => $lastEventTable,
                        ]);
                    } else {
                        echo json_encode(['started' => false, 'infoMessage' => 'something_went_wrong']);
                    }
                } else {
                    echo json_encode(['started' => false, 'infoMessage' => 'something_went_wrong']);
                }
            } else {
                echo json_encode(['started' => false, 'infoMessage' => 'already_ongoing_event']);
            }
        } else {
            echo json_encode(['started' => false, 'infoMessage' => 'something_went_wrong']);
        }
    }
}
