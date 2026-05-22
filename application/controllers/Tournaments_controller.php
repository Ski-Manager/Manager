<?php
/**
 * 
 */
class Tournaments_controller extends CI_Controller{
    
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
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('tournaments',$siteLang);
        $ci->lang->load('building',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('achievements_model');
        $this->load->model('building_model');
        $this->load->model('tournaments_model');
    }
    
    /**
     * index    Main function with top of the page (title, page description...)
     * 
     * @param type $data
     */
    public function index($data = NULL){
        // Initialize a few variables
//LINE BELOW TO EDIT 
        $building_type = 'achievements';
//LINE ABOVE TO EDIT 
        $data['title'] = '<h2>'.$this->lang->line('tournaments')['titleMain'].'</h2>'; 
        $data['intro'] = '<div>'.$this->lang->line('tournaments')['intro'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        
        $data['tournamentLogo'] = '<img src="'.base_url('img/icons/tournaments.jpg').'" title="'.$this->lang->line('tournaments')['titleMain'].'"/>';
        $data['tournamentDesc'] = ''.$this->lang->line('tournaments')['desc'].'';
        
        
        
        
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        
        $history_count_all_tournaments = $this->tournaments_model->history_count_all_tournaments($currentResortID);
        $data['history_all_tournaments'] = '<b>'.$this->lang->line('tournaments')['you_have_organized_total'].' '.$history_count_all_tournaments.' '.$this->lang->line('tournaments')['tournaments_in_resort'].'</b>';
        
        $data['for_help_with_tournaments'] = $this->lang->line('tournaments')['for_help_with_tournaments'];

        $player_preferred_lang_hist = $this->users_model->get_user_preferred_lang($currentUserID);
        $name_language_hist = 'name_'.$player_preferred_lang_hist;
        $tournament_stats = $this->tournaments_model->get_tournament_stats_DB($currentResortID);
        $data['tournament_stats'] = ($tournament_stats->num_rows() > 0) ? $tournament_stats->row() : null;
        $data['tournament_history'] = $this->tournaments_model->get_tournament_history_with_info_DB($currentResortID, $name_language_hist);
        
        if ($user_activated) {      // If the account is activated, we show the page
        
            
            $building_ach_unlocked = $this->building_model->get_building_ach_unlocked2($currentUserID, 124);   // Checks if the player has uncloked the achievement for extra slope types (ID 124)
            $achievement_data = $this->achievements_model->get_specific_achievements_data(124);   // get generic achievement info
                
            $row_building_ach_unlocked = $building_ach_unlocked->row();
            $info_achievement_data = $achievement_data->row();
            $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $column_name = 'name_'.$player_preferred_lang;
            $ach_name = ($info_achievement_data !== null) ? ($info_achievement_data->$column_name ?? '') : '';
            
            if ($building_ach_unlocked->num_rows() == 1) {  // achievement at least started (or completed)
                $claimed = $row_building_ach_unlocked->claimed;
                $progress = $row_building_ach_unlocked->progress;
                if ($progress == NULL)  // Achievement not started
                    $progress = 0;
                if ($progress == 100 && $claimed == 1) {
                    $data1 = $this->tournamentsListBlock($currentUserID);    // Calls the generic block funtion
                    $data['hideList'] = false;                  // To display specific blocks in the View (here we display the building)
                    //$data1 = $this->standardBuildingBlock($building_type, $currentResortID);    // Calls the generic block funtion for the right building type
                    $data = array_merge($data,$data1);      // Merges all data to "data" for the view 
                }
                elseif ($progress != 100) { // Achievement started but not completed
                    $data['hideList'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                    $data['infoMessage'] = 'achievement_locked';
                    $data['infoMessage_text'] = '<div class="alert alert-warning text-center">'.$this->lang->line('building')['the_achievement'].' "'.$ach_name.'" '.$this->lang->line('building')['ach_not_completed_tournaments'].'<br>'.$this->lang->line('building')['current_progress_is'].' '.$progress.'%. '.$this->lang->line('building')['achievement_link_info'].'</div>';
                }
                elseif ($claimed != 1) {  // Achievement completed but not claimed
                    $data['hideList'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                    $data['infoMessage'] = 'achievement_locked';
                    $data['infoMessage_text'] = '<div class="alert alert-warning text-center">'.$this->lang->line('building')['the_achievement'].' "'.$ach_name.'" '.$this->lang->line('building')['ach_not_claimed'].' '.$this->lang->line('building')['achievement_link_info'].'</div>';
                }
            }
            else {  // achievement not even started
                $data['infoMessage_text'] = '<div class="alert alert-warning text-center">'.$this->lang->line('building')['the_achievement'].' "'.$ach_name.'" '.$this->lang->line('building')['ach_not_completed_tournaments'].' '.$this->lang->line('building')['achievement_link_info'].'</div>';
                $data['hideList'] = true;                   // To display specific blocks in the View (here we display a message but no building)
                $data['infoMessage'] = 'achievement_locked';  
            }
                    
            $last_tournament = $this->tournaments_model->select_last_tournament_player($currentResortID);   // get info from last tournament (if existing)

            // Lazy-completion: if the cron missed completing a tournament, do it now
            if ($last_tournament->num_rows() > 0) {
                $last_tournament_data = $last_tournament->row();
                if ($last_tournament_data->completed == 0) {
                    date_default_timezone_set('UTC');
                    $end_date_check = gmdate('Y-m-d', strtotime($last_tournament_data->end_date));
                    $now_check      = gmdate('Y-m-d');
                    if ($end_date_check <= $now_check) {
                        $this->_complete_overdue_tournament($last_tournament_data, $currentResortID, $currentUserID);
                        // Re-fetch updated tournament state
                        $last_tournament = $this->tournaments_model->select_last_tournament_player($currentResortID);
                    }
                }
            }

            if ($last_tournament->num_rows() > 0) {                 // If at least one tournament was started
                $last_tournament_data = $last_tournament->row();
                if ($last_tournament_data->completed == 0) {        // The tournament is ongoing (not completed)
                    $data['lastTournamentTable'] = '<div id="info_message_div">'.$this->lang->line('tournaments')['there_is_ongoing_tournament'].'</div>'; 
                    $data['lastTournamentTable'] .= $this->ongoingTournamentTable($last_tournament); 
                }
                else if ($last_tournament_data->completed == 1) {   // The tournament is completed and no new one has been started yet
                    $data['lastTournamentTable'] = '<div id="info_message_div">'.$this->lang->line('tournaments')['no_ongoing_tournament_completed_below'].'</div>'; 
                    $data['lastTournamentTable'] .= $this->completedTournamentTable($last_tournament); 
                }
            }
            else {   // No tournaments started yet
                $data['lastTournamentTable'] = '<div id="info_message_div">'.$this->lang->line('tournaments')['no_tournament_history'].'</div>';
            }
            
            $data['main_content'] = 'tournaments';
            $this->load->view('templates/default',$data);
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    
    /**
     * _complete_overdue_tournament
     * Completes a tournament that has passed its end date but was not marked
     * completed by the cron (e.g. cron was down). Called lazily from index().
     * Guards against double-payment: cash/prestige are only added if this call
     * is the one that actually flips completed = 0 → 1 (affected_rows = 1).
     */
    protected function _complete_overdue_tournament($last_tournament_data, $currentResortID, $currentUserID) {
        $id_started_tournament  = $last_tournament_data->id_started_tournament;
        $id_tournament          = $last_tournament_data->id_tournament;
        $total_revenue          = (int)($last_tournament_data->aggregated_revenue ?? 0);
        $total_visitors         = (int)($last_tournament_data->aggregated_visitors ?? 0);

        $tournament_data = $this->tournaments_model->get_tournament_data($id_tournament);
        if ($tournament_data->num_rows() === 0) return;
        $tournament_data_array  = $tournament_data->row();
        $earned_prestige_points = $tournament_data_array->tournament_points;

        // Mark completed first; affected_rows = 1 means WE made the change,
        // preventing double-payment if the cron runs at the same moment.
        $marked = $this->tournaments_model->mark_tournaments_completed_DB($id_started_tournament);
        if ($marked == 1) {
            $this->tournaments_model->update_resort_column($currentUserID, 'prestige', $earned_prestige_points);
            $this->tournaments_model->update_resort_column($currentUserID, 'cash', $total_revenue);
            add_cost_stat_table($currentResortID, $total_revenue, 'rev_tournaments');
            add_cost_stat_table($currentResortID, $total_revenue, 'revenue');
            // Refresh the cash stored in the session so the sidebar updates
            $cash_player = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $cash_player);
        }
    }


    public function completedTournamentTable ($last_tournament) {
        
        $currentUserID = $this->users_model->get_user_id();     // $currentUserID to be used in this file
        $last_tournament_data = $last_tournament->row();        // Retrieves the last tournament data
        
        $tournament_data = $this->tournaments_model->get_tournament_data($last_tournament_data->id_tournament);   // Get data for this tournament
        $tournament_data_array = $tournament_data->row();

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $column_name = 'name_'.$player_preferred_lang;              // gives name_english or similar
        $tournament_name = $tournament_data_array->$column_name;    // Retrieves the name_english or french of the tournament

        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resort_name = $this->resort_model->get_resort_name($currentResortID);
        
        // replaces {resort} by the resort name of the player
        if (str_contains($tournament_name, '{resort}')) { //first we check if the tournament name contains the string '{resort}'
            $tournament_name = str_replace('{resort}', $resort_name, $tournament_name); //if yes, we simply replace it with the resort name
        }
        
        date_default_timezone_set('UTC');
        $start_date = strtotime(gmdate('Y-m-d', strtotime($last_tournament_data->started_datetime))); // Current GMT day as timestamp of the tournament start (at 00:00:00)
        $now = strtotime(gmdate('Y-m-d', strtotime('now')));        // Current GMT day as a timestamp (at 00:00:00)
        $nb_days_since_start = ($now - $start_date)/60/60/24;   // returns integer
        $percentage_achieved = 100; 

        $total_visitors = $last_tournament_data->aggregated_visitors;
        $total_revenue = $last_tournament_data->aggregated_revenue;

        $is_last_tournament_completed = $last_tournament_data->completed;
        $currentTournamentArea = '<table class="table table-responsive tournaments tournament_completed" align="center"><tbody>';
        $currentTournamentArea .= '<tr><th>'.$this->lang->line('tournaments')['last_tournament'].'</th>';
        $currentTournamentArea .= '<td colspan="1">'.$tournament_name.'</td>';
        $currentTournamentArea .= '<th>'.$this->lang->line('tournaments')['ended'].'</th>';
        $currentTournamentArea .= '<td>'.$last_tournament_data->end_date.'</td>';
        $currentTournamentArea .= '<td width="100" rowspan="3"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['tournament_completed'].'"><div class="chart center" id="ongoing_tournament_progress" data-percent="'.$percentage_achieved.'"></div></div></td></tr>';
        $currentTournamentArea .= '<tr><th colspan="4">'.$this->lang->line('tournaments')['final_results'].'</th></tr>';
        $currentTournamentArea .= '<tr><th>'.$this->lang->line('tournaments')['visitors_cap'].'</th>';
        $currentTournamentArea .= '<td>'.number_format($total_visitors, 0, ',', ' ').'</td>';
        $currentTournamentArea .= '<th rowspan="2">'.$this->lang->line('home')['prestige'].'</th>';
        $currentTournamentArea .= '<td rowspan="2">+ '.number_format($tournament_data_array->tournament_points, 0, ',', ' ').'</td></tr>';
        $currentTournamentArea .= '<tr data-id_tournament="'.$last_tournament_data->id_tournament.'"><th>'.$this->lang->line('tournaments')['revenue_cap'].'</th>';
        $currentTournamentArea .= '<td>+ '.number_format($total_revenue, 0, ',', ' ').'€</td>';
        $currentTournamentArea .= '<td class="no_border" style="text-align:center !important;" id="start_button_column-'.$last_tournament_data->id_tournament.'" ><div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('tournaments')['start_again_tournament'].'"><button class="btn btn-success start_tournament_button">'.$this->lang->line('tournaments')['start_again'].'</button></div></td></tr>';
        $currentTournamentArea .= '</tbody></table>';
       
        return $currentTournamentArea;
    }
    
    
    public function ongoingTournamentTable ($last_tournament) {
        
        $currentUserID = $this->users_model->get_user_id();     // $currentUserID to be used in this file
        $last_tournament_data = $last_tournament->row();        // Retrieves the last tournament data
        
        $tournament_data = $this->tournaments_model->get_tournament_data($last_tournament_data->id_tournament);   // Get data for this tournament
        $tournament_data_array = $tournament_data->row();

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $column_name = 'name_'.$player_preferred_lang;              // gives name_english or similar
        $tournament_name = $tournament_data_array->$column_name;    // Retrieves the name_english or french of the tournament

        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $resort_name = $this->resort_model->get_resort_name($currentResortID);
        
        // replaces {resort} by the resort name of the player
        if (str_contains($tournament_name, '{resort}')) { //first we check if the tournament name contains the string '{resort}'
            $tournament_name = str_replace('{resort}', $resort_name, $tournament_name); //if yes, we simply replace it with the resort name
        }
        
        date_default_timezone_set('UTC');
        $start_date = strtotime(gmdate('Y-m-d', strtotime($last_tournament_data->started_datetime))); // Current GMT day as timestamp of the tournament start (at 00:00:00)
        $now = strtotime(gmdate('Y-m-d', strtotime('now')));        // Current GMT day as a timestamp (at 00:00:00)

        $nb_days_since_start = ($now - $start_date)/60/60/24;   // returns integer
        $percentage_achieved = number_format(min(100,($nb_days_since_start/(ceil($tournament_data_array->duration/ACCELERATOR_FACTOR)))*100), 0, ',', ' '); 
        
        $total_visitors = $last_tournament_data->aggregated_visitors;
        $total_revenue = $last_tournament_data->aggregated_revenue;

        $is_last_tournament_completed = $last_tournament_data->completed;
        $currentTournamentArea = '<table class="table table-responsive tournaments tournament_ongoing" align="center"><tbody>';
        $currentTournamentArea .= '<tr><th>'.$this->lang->line('tournaments')['ongoing_tournament_header'].'</th>';
        $currentTournamentArea .= '<td colspan="2">'.$tournament_name.'</td>';
        $currentTournamentArea .= '<th colspan="2">'.$this->lang->line('tournaments')['partial_results'].'</th>';
        $currentTournamentArea .= '<td width="100" rowspan="3"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['ongoing_tournament_header'].'"><div class="chart center" id="ongoing_tournament_progress" data-percent="'.$percentage_achieved.'"></div></div></td>';
        $currentTournamentArea .= '</tr>';
        $currentTournamentArea .= '<tr><th>'.$this->lang->line('tournaments')['started'].'</th>';
        $currentTournamentArea .= '<td>'.$last_tournament_data->started_datetime.'</td>';
        $currentTournamentArea .= '<td rowspan="2">'.$this->lang->line('home')['big_day'].' '.min($nb_days_since_start, ceil($tournament_data_array->duration/ACCELERATOR_FACTOR)).' / '.ceil($tournament_data_array->duration/ACCELERATOR_FACTOR).'</td>';
        $currentTournamentArea .= '<th>'.$this->lang->line('tournaments')['visitors_cap'].'</th>';
        $currentTournamentArea .= '<td>'.number_format($total_visitors, 0, ',', ' ').'</td></tr>';
        $currentTournamentArea .= '<tr><th>'.$this->lang->line('tournaments')['ending'].'</th>';
        $currentTournamentArea .= '<td>'.$last_tournament_data->end_date.'</td>';
        $currentTournamentArea .= '<th>'.$this->lang->line('tournaments')['revenue_cap'].'</th>';
        $currentTournamentArea .= '<td>+ '.number_format($total_revenue, 0, ',', ' ').'€</td></tr>';
        $currentTournamentArea .= '</tbody></table>';
       
        return $currentTournamentArea;
    }
    
    
    /**
     * tournamentsListBlock        Displays the list of all tournaments
     * 
     * @param type $currentResortID   Current resort ID
     * @return string               Returns the content of the page
     */
    public function tournamentsListBlock($currentUserID){
        
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
        $description_language = 'description_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
        
        $data['table_tournaments'] = '';
        $data['table_tournaments'] .= '<table class="tournaments_table" align="center"><tbody>';
        $tournaments_data = $this->tournaments_model->get_all_tournaments_data($name_language, $description_language, 1, 'descending'); 
        
        $count = 0;
        $td_class = '';
        foreach ($tournaments_data->result() as $tournaments_data_array) {    // For each generic tournament
            $data_to_parse = $this->check_eligibility_tournament ($currentResortID, $tournaments_data_array, $count);
            $data['table_tournaments'] .= $data_to_parse['data'];
        }
        $data['table_tournaments'] .= '</tbody></table>';
        
        return $data;
    }
    
    
    protected function check_eligibility_slopes ($currentResortID, $slope_type) {
        $nb_slope_this_type = count_nb_open_slope_of_type($currentResortID ,$slope_type);
        if (isset($nb_slope_this_type) && $nb_slope_this_type > 0) {
            $eligibility_slopes = true;
        }
        else {
            $eligibility_slopes = false;
        }
        return $eligibility_slopes;
    }
    
    protected function check_eligibility_infrastructure ($currentResortID, $infrastructure_type, $level_required) {
        $building_built_array = $this->building_model->get_all_created_buildings_for_player($currentResortID, $infrastructure_type, $level_required, '>='); 
        $built_buildings = $building_built_array->row();
        $number_of_this_building_built = $built_buildings->count;
        //$building_built_array = $this->building_model->get_created_buildings_for_player($currentResortID, $infrastructure_type);  not used?
        if ($number_of_this_building_built > 0) {
             $eligibility_infrastructure = true;
        }
        else {
            $eligibility_infrastructure = false;
        }    
        return $eligibility_infrastructure;     
    }
    
    protected function check_eligibility_cash ($currentResortID, $running_cost) { 
        // Checks if player has enough cash
        $player_cash = $this->users_model->get_cash_player();
        if (isset($player_cash) && $player_cash >= $running_cost) {
            $eligible_cash = true;
        }
        else {
            $eligible_cash = false;
        } 
        return $eligible_cash;    
    }
    
    protected function check_eligibility_required_prestige ($currentResortID, $required_prestige) { 
        // Checks if player has enough cash
        $player_prestige = $this->users_model->get_prestige_resort($currentResortID);
        if (isset($player_prestige) && $player_prestige >= $required_prestige) {
            $eligible_prestige = true;
        }
        else {
            $eligible_prestige = false;
        } 
        return $eligible_prestige;    
    }
    
    protected function compile_eligibility_tournament ($eligibility_slopes, $eligibility_all_infrastructure, $eligibility_cash, $eligibility_required_points) {
        if ($eligibility_slopes === TRUE && $eligibility_all_infrastructure === TRUE && $eligibility_cash === TRUE && $eligibility_required_points === TRUE ) {
            $eligible_to_tournament = TRUE;
        }
        else
            $eligible_to_tournament = FALSE;
          
        return $eligible_to_tournament;  
    }
    
    protected function check_eligibility_tournament ($currentResortID, $tournaments_data_array, $count = NULL) { 
        
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
        $description_language = 'description_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
        
        $table_tournaments = '';
        
        if ($count == 0) {
            $tr_class = 'class="ach_even" ';
            $count ++;
        }
        else if($count == 1) {
            $tr_class = 'class="ach_odd" ';
            $count = 0;
        }
        
        $eligible_to_tournament = false;
        $t_id = $tournaments_data_array->id_tournament;
        $t_name = $tournaments_data_array->$name_language;
        
        $resort_name = $this->resort_model->get_resort_name($currentResortID);
        
        // replaces {resort} by the resort name of the player
        if (str_contains($t_name, '{resort}')) { //first we check if the tournament name contains the string '{resort}'
            $t_name = str_replace('{resort}', $resort_name, $t_name); //if yes, we simply replace it with the resort name
        }

        $t_description = $tournaments_data_array->$description_language;
        $t_running_cost = $tournaments_data_array->running_cost;
        $t_expected_revenue = $tournaments_data_array->expected_revenue;
        $t_expected_visitors = $tournaments_data_array->expected_visitors;
        $t_tournament_points = $tournaments_data_array->tournament_points;
        $t_required_points = $tournaments_data_array->required_points;
        $t_duration = ceil($tournaments_data_array->duration/ACCELERATOR_FACTOR);

        // slopes
        $t_downhill = $tournaments_data_array->downhill;
        $t_snowpark = $tournaments_data_array->snowpark;
        $t_bordercross = $tournaments_data_array->boardercross;
        $t_crosscountry = $tournaments_data_array->crosscountry;
        $t_luge = $tournaments_data_array->luge;

        // infrastructure
        $t_required_village_level = $tournaments_data_array->village;
        $t_required_icerink_level = $tournaments_data_array->icerink;
        $t_required_curling_center = $tournaments_data_array->curling_center;
        $t_required_open_stage = $tournaments_data_array->open_stage;

        $history_count = $this->tournaments_model->history_count_tournament($currentResortID, $t_id); 
        // Make an array of all slope types requirements (0 or 1)
        $slopes_array = array('downhill' => $t_downhill, 'snowpark' => $t_snowpark, 'boardercross' => $t_bordercross, 'crosscountry' => $t_crosscountry, 'luge' => $t_luge);

        // Make an array of all infrastructure requirements (level - integer)
        $infrastructure_array = array('housing_complex' => $t_required_village_level, 'icerink' => $t_required_icerink_level, 'curling_center' => $t_required_curling_center, 'open_stage' => $t_required_open_stage);

        // Build a list for slope types
        $slopes_requirements = '<ul class="table_list_no_indent">';
        foreach ($slopes_array as $slope_type => $required) {
            if ($required == 1) {
                $eligibility_slopes = $this->check_eligibility_slopes($currentResortID, $slope_type);
                if ($eligibility_slopes === true) {
                    $slopes_class = 'green_text';
                    $tooltip_slopes = '<div>';
                }
                else {
                    $slopes_class = 'red_text';
                    $tooltip_slopes = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['no_open_slopes_this_type'].'">';
                }

                $slopes_requirements .= $tooltip_slopes.'<li class="'.$slopes_class.'">'.$this->lang->line('home')[$slope_type].'</li></div>';
            }
        }
        $slopes_requirements .= '</ul>';

        // Build a list for infrastructure
        $infrastructure_requirements = '<ul class="table_list_no_indent">';
        $eligibility_all_infrastructure = true;


        foreach ($infrastructure_array as $infrastructure_type => $level_required) {
            if ($level_required != 0) {

                // Gets the friendly name of the infrastructure
               $building_friendly_name = $this->tournaments_model->get_building_friendly_name($infrastructure_type, $level_required, $name_language); 
               // Checks if required infrastructure level is built

               $building_built_array = $this->building_model->get_all_created_buildings_for_player($currentResortID, $infrastructure_type, $level_required); 
               $built_buildings = $building_built_array->row();
               $number_of_this_building_built = $built_buildings->count;
               
               // Check current built level
               $building_built_array_player = $this->building_model->get_created_buildings_for_player($currentResortID, $infrastructure_type); 

               $eligibility_infrastructure = $this->check_eligibility_infrastructure($currentResortID, $infrastructure_type, $level_required);
               if ($eligibility_infrastructure === TRUE) {
                    $built_buildings = $building_built_array_player->row();
                    $level_of_this_building_built = $built_buildings->level;
                    $tooltip_infrastructure = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['you_have_built_level'].' ' .$level_of_this_building_built.' '.$this->lang->line('tournaments')['of_this_building'].' '.$this->lang->line('tournaments')['level'].' '.$level_required.' '.$this->lang->line('tournaments')['is_required'].'">';
                   $infrastructure_class = 'green_text';
               }
               else {
                    $built_or_not = $building_built_array_player->num_rows();
                    if ($built_or_not > 0) {
                        $built_buildings = $building_built_array_player->row();
                        $level_of_this_building_built = $built_buildings->level;
                           $tooltip_infrastructure = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['only_upgraded_level'].' '.$level_of_this_building_built.' '.$this->lang->line('tournaments')['upgrade_to'].' '.$level_required.' '.$this->lang->line('tournaments')['be_eligible'].'">';
                    }
                    else {
                        $level_of_this_building_built = 0;
                        $tooltip_infrastructure = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['not_built'].' '.$level_required.' '.$this->lang->line('tournaments')['be_eligible'].'">';
                    }
                   
                   $infrastructure_class = 'red_text';
                   $eligibility_all_infrastructure = false;
               }

               $infrastructure_requirements .= $tooltip_infrastructure.'<li class="'.$infrastructure_class.'">'.$building_friendly_name.' ('.$this->lang->line('tournaments')['lvl'].' '.$level_required.')</li></div>';
            }
        }
        $infrastructure_requirements .= '</ul>';

        $eligibility_cash = $this->check_eligibility_cash($currentResortID, $t_running_cost);
        $eligibility_required_prestige = $this->check_eligibility_required_prestige($currentResortID, $t_required_points);

        // Checks if player has enough cash
        $player_cash = $this->users_model->get_cash_player();
        if ($eligibility_cash === true) {
            $running_cost_class = 'green_text';
            $running_cost_tooltip = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['you_have_enough_cash'].' ('.number_format($t_running_cost, 0, ',', ' ').'€) '.$this->lang->line('tournaments')['to_arrange_this_tournament'].'">';
        }
        else {
            $running_cost_class = 'red_text';
            $running_cost_tooltip = '<div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['you_need'].' '.number_format($t_running_cost, 0, ',', ' ').'€ '.$this->lang->line('tournaments')['to_arrange_this_tournament'].'">';
        }

        $eligible_to_tournament = $this->compile_eligibility_tournament($eligibility_slopes, $eligibility_all_infrastructure, $eligibility_cash, $eligibility_required_prestige);

        if ($eligibility_required_prestige === FALSE)
            $prestige_class = 'red_text';
        else
            $prestige_class = 'green_text';
            
        // Check is there is an ongoing tournament
        $ongoing_tournament = FALSE;
        $last_tournament = $this->tournaments_model->select_last_tournament_player($currentResortID);   // get info from last tournament (if existing)
        $last_tournament_data = $last_tournament->row();
        if ($last_tournament->num_rows() > 0) { // If at least one tournament was started
            $is_last_tournament_completed = $last_tournament_data->completed;
            if ($is_last_tournament_completed == 0) {  // there is an ongoing tournament
                $ongoing_tournament = TRUE;
            }
        }
        
        if ($eligible_to_tournament === FALSE) {
            $class_start_button = 'disabled';
            $start_button_tooltip = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('tournaments')['one_or_several_req'].'">';
        }
        else if ($ongoing_tournament === TRUE) {    // if there is an ongoing tournament, disable button
            $class_start_button = 'disabled';
            $start_button_tooltip = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('tournaments')['ongoing_tournament'].'">';
        }
        else {
            $class_start_button = '';
            $start_button_tooltip = '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('tournaments')['start'].'">';
        }

        $outcome_cell = '<ul class="table_list_no_bullet_no_indent">';
        $outcome_cell .= '<li class="'.$running_cost_class.'">'.$running_cost_tooltip.'<b>'.$this->lang->line('tournaments')['running_costs'].':</b> '.display_friendly_cash($t_running_cost).'€</div></li>';
        $outcome_cell .= '<li><div class="tooltip tooltip-bottom" data-tip="'.number_format($t_expected_revenue, 0, ',', ' ').'€"><b>'.$this->lang->line('tournaments')['expected_revenue'].':</b> '.display_friendly_cash($t_expected_revenue).'€</div></li>';
        $outcome_cell .= '<li><b>'.$this->lang->line('tournaments')['expected_visitors'].':</b> '.number_format($t_expected_visitors, 0, ',', ' ').'</li>';
        $outcome_cell .= '<li><b>'.$this->lang->line('tournaments')['prestige_points'].':</b> +'.number_format($t_tournament_points, 0, ',', ' ').'</li>';
        $outcome_cell .= '</ul>';

        $table_tournaments .= '<tr '.$tr_class.'>';
        $table_tournaments .= '<th rowspan="3">'.$t_name.'</th>';
        $table_tournaments .= '<th rowspan="4">'.$t_description.'</th>';
        $table_tournaments .= '<th colspan="2"><b>'.$this->lang->line('tournaments')['requirements'].'</b></th>';
        $table_tournaments .= '<th><b>Outcome</b></th>';
        $table_tournaments .= '</tr>';

        $table_tournaments .= '<tr '.$tr_class.'>';
        $table_tournaments .= '<td valign="top" class="nowrap_cell"><b>'.$this->lang->line('home')['big_slopes'].':</b>'.$slopes_requirements.'</td>';
        $table_tournaments .= '<td valign="top" class="nowrap_cell"><b>'.$this->lang->line('tournaments')['infrastructure'].':</b>'.$infrastructure_requirements.'</td>';
        $table_tournaments .= '<td valign="top" rowspan="2" class="nowrap_cell">'.$outcome_cell.'</td>';
        $table_tournaments .= '</tr>';

        $table_tournaments .= '<tr '.$tr_class.'>';
        $table_tournaments .= '<td class="nowrap_cell '.$prestige_class.'" style="text-align:center !important;" colspan="2"><b>'.$this->lang->line('tournaments')['prestige_required'].': </b>'.$t_required_points.'</td>';
        $table_tournaments .= '</tr>';
        
        $table_tournaments .= '<tr data-id_tournament="'.$t_id.'" '.$tr_class.'>';

        if ($t_duration > 1)
            $days_to_display = $this->lang->line('home')['days'];
        else
            $days_to_display = $this->lang->line('home')['small_day'];

        
        $table_tournaments .= '<td style="text-align:center !important;"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('tournaments')['history_text'].'"><b>'.$this->lang->line('tournaments')['history'].':</b> '.number_format($history_count, 0, ',', ' ').'</div></td>';
        $table_tournaments .= '<td style="text-align:center !important;" colspan="2"><b>'.$this->lang->line('tournaments')['duration'].': </b>'.$t_duration.' '.$days_to_display.'</td>';
        $table_tournaments .= '<td style="text-align:center !important;" colspan="2" id="start_button_column-'.$t_id.'" >'
                . $start_button_tooltip.'<button '.$class_start_button.' class="btn btn-success start_tournament_button">'.$this->lang->line('tournaments')['start'].'</button></div>'
                . '</td>';
        $table_tournaments .= '</tr>';

        $table_tournaments .= '<tr class="no_border"><td colspan="5" class="no_border"></td></tr>';
        
        $returned_data = array('data' => $table_tournaments, 'eligible_to_tournament' => $eligible_to_tournament);
        
        return $returned_data;
        
        
        
        

    }
    
    public function start_tournament () {
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        // Gets the values posted by the Jquery function
        $id_tournament = trim($this->input->post('id_tournament', TRUE));  
        
        $tournament_data = $this->tournaments_model->get_tournament_data($id_tournament);   // Get data for this tournament
        if ($tournament_data->num_rows() === 0) {
            echo json_encode(array('started' => false, 'infoMessage' => 'something_went_wrong'));
            return;
        }
        $tournament_data_array = $tournament_data->row();
        
        // Checks if tournament can be run by player. Returns data (for table - not used here) and eligible_to_tournament (boolean)
        $eligibility_tournament = $this->check_eligibility_tournament($currentResortID, $tournament_data_array);    
        
        if ($eligibility_tournament['eligible_to_tournament'] === true) {   // If tournament can be organized by player (eligible)
        
            $last_tournament = $this->tournaments_model->select_last_tournament_player($currentResortID);   // get info from last tournament (if existing)
            $last_tournament_data = $last_tournament->row();
            if ($last_tournament->num_rows() > 0) { // If at least one tournament was started
                $is_last_tournament_completed = $last_tournament_data->completed;
            }
            
            if ($last_tournament->num_rows() == 0 || $is_last_tournament_completed == 1) {  // there is no ongoing tournament or the last one is completed
                
                $running_cost = $tournament_data_array->running_cost;
                $id_tournament = $tournament_data_array->id_tournament;
                $duration = ceil($tournament_data_array->duration/ACCELERATOR_FACTOR);
                
                $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
                $column_name = 'name_'.$player_preferred_lang;
                $this->lang->load('logs',$player_preferred_lang);
                $tournament_name = $tournament_data_array->$column_name;
                
                $resort_name = $this->resort_model->get_resort_name($currentResortID);
                
                // replaces {resort} by the resort name of the player
                if (str_contains($tournament_name, '{resort}')) { //first we check if the tournament name contains the string '{resort}'
                    $tournament_name = str_replace('{resort}', $resort_name, $tournament_name); //if yes, we simply replace it with the resort name
                }

                // START PAYMENT TOURNAMENT (running cost, when starting)
                $cash_player = $this->users_model->get_cash_player();
                if ($removeCashQuery = $this->users_model->pay_item($running_cost, $cash_player)){      //the paiment for the tournament has been taken

                    $add_cost_history_table = add_cost_stat_table($currentResortID, $running_cost, 'cost_tournaments');
                    $add_cost_history_table = add_cost_stat_table($currentResortID, $running_cost, 'expenses');
                                
                    $today = strtotime('now');
                    $today_GMT = gmdate('Y-m-d H:i:s', $today);
                    $today_timestamp = strtotime('+'.$duration.' days', $today);
                    $end_date_GMT = gmdate('Y-m-d', $today_timestamp);
                    
                    // Update sidebar
                    $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                    $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                    
                    $data_insert = array (
                        'id_resort' => $currentResortID,
                        'id_tournament' => $id_tournament,    
                        'started_datetime' => $today_GMT,
                        'end_date' => $end_date_GMT,
                        'aggregated_visitors' => 0,
                        'aggregated_revenue' => 0
                    );
                    if ( $start_tournament = $this->tournaments_model->start_tournament_DB($data_insert) ) {

                        
                    $data_achievement = array (
                        'id_tournament' => $id_tournament       
                    );
                    $data_achievement2 = array (
                        'id_resort' => $currentResortID,
                        'id_achievement' => 132       
                    );
                    $call_achievements_check = call_achievements_check($data_achievement, 'organize_tournament_quantity');   // Check if a corresponding achievement should be updated
                    $call_achievements_check = call_achievements_check($data_achievement, 'organize_specific_tournament');   // Check if a corresponding achievement should be updated
                    $call_achievements_check = call_achievements_check($data_achievement2, 'unlock_item');   // Check if a corresponding achievement should be updated
                    if ($duration > 1)
                        $days_to_display = $this->lang->line('home')['days'];
                    else
                        $days_to_display = $this->lang->line('home')['small_day'];
        
                    $text_logs = $tournament_name.' '.$this->lang->line('tournaments')['started_small'].' '.$this->lang->line('tournaments')['and_will_last'].' '.$duration.' '.$days_to_display.'.';
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['tournaments'], 'data' => $text_logs) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['tournaments'], 'data' => $text_logs) );   // Add a log row to the game_player_logs table
                    
                    $last_tournament = $this->tournaments_model->select_last_tournament_player($currentResortID);   // get info from last tournament (if existing)
            
                    $lastTournamentTable = '<div id="info_message_div">'.$this->lang->line('tournaments')['tournament_started_confimation'].'</div>'; 
                    $lastTournamentTable .= $this->ongoingTournamentTable($last_tournament);
                    
                        echo json_encode(array('started' => true, 'infoMessage' => 'tournament_started', 'start_button_cell' => '<div style="display:inline;" class="tooltip tooltip-left" data-tip="'.$this->lang->line('tournaments')['tournament_started'].'"><button disabled class="btn btn-warning start_tournament_button">'.$this->lang->line('tournaments')['started'].'</button></div>', 'lastTournamentTable' => $lastTournamentTable));


                    }
                    else {
                        echo json_encode(array('started' => false, 'infoMessage' => 'something_went_wrong'));
                    }
                }
                else {
                    echo json_encode(array('started' => false, 'infoMessage' => 'something_went_wrong'));
                }
            }
            else {
                echo json_encode(array('started' => false, 'infoMessage' => 'already_ongoing_tournament'));
            }
        }
        else {      // Tournament cannot be organized by player (tried to cheat or something weird hapenned)
            echo json_encode(array('started' => false, 'infoMessage' => 'something_went_wrong'));
        }
    }
    
    /**
     * get_achievements_from_session simply retrieves the achievements from the userdata session for the javascript function in home.js (refresh_achievements_sidebar)
     * Is used to refresh the sidebar when the page is loaded and a new achievement is completed.
     */
    function get_achievements_from_session() {
        $tournaments = $this->session->userdata('achievements');
        $tournaments_to_claim = $this->session->userdata('achievements_to_claim');
        echo json_encode( array('achievements'=>$tournaments, 'achievements_to_claim'=>$tournaments_to_claim));
    }
    
}
