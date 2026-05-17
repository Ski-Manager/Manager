<?php

/**
 * get_info_general This class is called with the Hook, after loading the main constructor controller
 */
class get_info_general {
        private $CI;

        public function __construct() {
            $this->CI =& get_instance(); 
        }  
        
        public function prepare() {
            $this->CI->load->model('users_model');
            $logged_status = $this->CI->session->userdata('is_logged_in');
            if (!isset($logged_status) || $logged_status != true) { // not logged in
                
                if (!$this->CI->session->userdata('site_lang_selected')) {   // if site_lang_selected cookie doesn't exists
                    //$HTTP_ACCEPT_LANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        $this->CI->session->set_userdata('site_lang', 'english');
                    }
                    else {
                        $langs = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
                        if (strpos($langs[0], 'fr') !== false) {
                            $this->CI->session->set_userdata('site_lang', 'french');
                        }
                        else {
                            $this->CI->session->set_userdata('site_lang', 'english');
                        }
                    }
                }
                else {  // site_lang_selected COOKIE exists
                    $site_lang_selected = $this->CI->session->userdata('site_lang_selected');
                    $this->CI->session->set_userdata('site_lang', $site_lang_selected);
                }
            }
            else {  // Logged in, take from DB
                $currentUserID = $this->CI->users_model->get_user_id();
                $player_preferred_lang = $this->CI->users_model->get_user_preferred_lang($currentUserID);
                $this->CI->session->set_userdata('site_lang', $player_preferred_lang);
            }
        }
        
        
        public function online_players() {
            // Skip expensive queries when already cached in session for current request
            static $cached = false;
            if ($cached) {
                return;
            }

            $this->CI->load->model('users_model');
            $current_time = time();
            $time_5min_ago = $current_time - 300;
            $online_players = $this->CI->users_model->get_online_players($time_5min_ago);
            $registered_players = $this->CI->users_model->get_registered_players();
            $this->CI->session->set_userdata('online_players', $online_players);
            $this->CI->session->set_userdata('registered_players', $registered_players);
            $cached = true;
        }

        /**
         * get_info_general This function is used to display the cash and reputation in the sidebar.
         */
        public function get_info_general() {
            $logged_status = $this->CI->session->userdata('is_logged_in');
            if (!isset($logged_status) || $logged_status != true) {
                return; // Only compute sidebar data for logged-in players
            }

            $this->CI->load->model('users_model');
            $cash = $this->CI->users_model->get_cash_player();
            $snow_level = $this->CI->users_model->get_snow_level();
            
            $currentUserID = $this->CI->users_model->get_user_id();                    // get the user ID
            $currentResortID = $this->CI->users_model->get_resort_id($currentUserID);  // get the resort ID

            if (!$currentResortID) {
                return; // Player has not created a resort yet
            }

            // Auto-complete any expired constructions and equipment deliveries
            $this->CI->load->model('building_model');
            $this->CI->load->model('equipment_model');
            $this->CI->building_model->auto_complete_constructions_DB($currentResortID);
            $this->CI->equipment_model->auto_complete_deliveries_DB($currentResortID);

            $reputation = $this->CI->users_model->get_reputation_player();
            $prestige = $this->CI->users_model->get_prestige_resort($currentResortID);
            
            $day_of_season = get_day_of_season($currentResortID);
            $current_season = get_current_season($currentResortID);
            $yesterdays_visitors = get_visitors_yesterday($currentResortID);
            $genepis = get_genepis($currentUserID);
            
            
            if (!$cash)
                $cash = 0;
            $this->CI->session->set_userdata('cash', $cash);
            $this->CI->session->set_userdata('snow_level', $snow_level);
            $this->CI->session->set_userdata('reputation', $reputation);
            $this->CI->session->set_userdata('prestige', $prestige);
            $this->CI->session->set_userdata('day_of_season', $day_of_season);
            $this->CI->session->set_userdata('season', $current_season);
            $this->CI->session->set_userdata('affluence', $yesterdays_visitors);
            $this->CI->session->set_userdata('genepis', $genepis);
        } 
        
        /**
         * get_achievements This function is used to display the current state of achievements. It is currently called in the sidebar via the HOOKS.php file
         */
        public function get_achievements() {
            $achievements = array();
            $this->CI->session->set_userdata('achievements', $achievements);
            $this->CI->session->set_userdata('achievements_to_claim', 0);

            if ($this->CI->session->userdata('site_lang')) {
                $siteLang = $this->CI->session->userdata('site_lang');
            } else {
                $siteLang = 'english';
                $this->CI->session->set_userdata('site_lang', $siteLang);
            }
            
            $this->CI->load->model('users_model');
            $this->CI->load->model('achievements_model');
            $this->CI->lang->load('achievements',$siteLang);
            $name_language = 'name_'.$siteLang;                     // outputs name_english or name_french for the DB columns
            $description_language = 'description_'.$siteLang;       // outputs name_english or name_french for the DB columns
            $currentUserID = $this->CI->users_model->get_user_id();                    // get the user ID
            
            $logged_status = $this->CI->session->userdata('is_logged_in');
            if (isset($logged_status) && $logged_status == true) {        // Only for logged in users
                $achievements_data_array1 = array();
                $achievements_data_array2 = array();
                $achievements_data_array3 = array();
                $returned_results1 = 0;
                $returned_results2 = 0;
                $returned_results3 = 0;
                $array_ids0 = array();
                $array_ids1 = array();
                $array_ids2 = array();
                // Achievements that are completed but not claimed (get priority 1)
                $achievements_data_temp = $this->CI->achievements_model->get_achievements_player($currentUserID, $name_language, 100, 0, 0); 
                $achievements_data_array1 = $achievements_data_temp->result();
                $returned_results1 = $achievements_data_temp->num_rows();
                
                
                if ($returned_results1 < 2) {   // If there are less than 2 achievements to claim (need to fill-in the table with 3 achievements)
                    // Achievements that are not completed but started (get priority 2)
                    $achievements_data_temp = $this->CI->achievements_model->get_achievements_player($currentUserID, $name_language, '', 0, 0);
                    $achievements_data_array2 = $achievements_data_temp->result();
                    $returned_results2 = $achievements_data_temp->num_rows();
                }
                $returned_results1and2 = $returned_results1+$returned_results2;
                // Retrieve the achievements ids that were already presented, in order to not re-use them.
                // Achievements that are completed and claimed (to be remove from next query)
                $achievements_data_temp = $this->CI->achievements_model->get_achievements_player($currentUserID, $name_language, 100, 1, 0); 
                $achievements_data_array0 = $achievements_data_temp->result();
                $returned_results0 = $achievements_data_temp->num_rows();
                // Puts all the achievement IDs from first query in an array
                foreach ($achievements_data_array0 as $array0) {
                    $array_ids0[] = $array0->id_achievement;
                }
                // Puts all the achievement IDs from second query in an array
                foreach ($achievements_data_array1 as $array1) {
                    $array_ids1[] = $array1->id_achievement;
                }
                // Puts all the achievement IDs from third query in an array
                foreach ($achievements_data_array2 as $array2) {
                    $array_ids2[] = $array2->id_achievement;
                }
                        
                     //echo 'claimed(hide): '.$returned_results0;
                
                
                $array_ids = array_merge($array_ids0, $array_ids1, $array_ids2);     // Merge the three arrays
                $ids_to_exclude_temp = implode (",", $array_ids);       // might be unnecessary
                $ids_to_exclude = explode (",", $ids_to_exclude_temp);  // might be unnecessary

                // Achievements that are not started (priority 3)
                $achievements_data_temp = $this->CI->achievements_model->get_all_other_achievements_data($currentUserID, $name_language, $description_language, 0, $ids_to_exclude); 
                $achievements_data_array3 = $achievements_data_temp->result();
                $returned_results3 = $achievements_data_temp->num_rows();

/*echo ' // completed, not claimed: '.$returned_results1;
echo ' // started: '.$returned_results2;
echo ' //  not started: '.$returned_results3.'<br>';*/
                // Merge the three queries to a single array
                // We show a maximum of 2 started achievements to make sure there are some non-started onces in the top 5.
                $big_array_temp = array_merge($achievements_data_array1, array_slice($achievements_data_array2, 0, 2), $achievements_data_array3);
                $big_array = array_slice($big_array_temp, 0, 5);
                //var_dump($big_array);
                foreach ($big_array as $achievements_data_assoc) {    // For each achievement that should be displayed (only 3 in theory)
                    // ID and name are available from any query
                    $id_achievement = $achievements_data_assoc->id_achievement;
                    $name_achievement = $achievements_data_assoc->$name_language;
                    if (isset($achievements_data_assoc->progress) && isset($achievements_data_assoc->claimed)) {    // progress and claimed are only set in the first two queries (if achievement is present in user_achievements table)
                        $progress = $achievements_data_assoc->progress;
                        $claimed = $achievements_data_assoc->claimed;
                        if ($progress == 100 && $claimed == 0){     // Ready to claim button
                            $button = '<button type="button" class="btn btn-warning btn-sm claim_button claim-achievement-btn tooltip tooltip-bottom" data-achievement-id="'.$id_achievement.'" data-tip="'.$this->CI->lang->line('achievements')['claim'].'" aria-label="'.$this->CI->lang->line('achievements')['claim'].'"><img width="23" height="23" src="'.base_url('img/icons/claim.png').'" alt=""></button>';
                        }
                        else {      // Not ready to claim button (disabled/no link)
                            $button = '<div class="tooltip tooltip-bottom" data-tip="'.$this->CI->lang->line('achievements')['claim_not_available_tooltip'].'"><img width="23" height="23" src="'.base_url('img/icons/claim-grey.png').'"></div>';
                        }
                    }
                    else {      // Achievements from third query: not in user_achievements so progress and claimed are not available.
                        $progress = 0;
                        // Not ready to claim button (disabled/no link)
                        $button = '<div class="tooltip tooltip-bottom" data-tip="'.$this->CI->lang->line('achievements')['claim_not_available_tooltip'].'"><img width="23" height="23" src="'.base_url('img/icons/claim-grey.png').'"></div>';
                    }
                    
                    
                    // Variable to be passed to the table in the View.
                    $temp = array( 'id_achievement'  => $id_achievement,
                        'progress'     => $progress,
                        'button'     => $button,
                        'name'     => $name_achievement
                    );
                    $achievements[] = $temp;    // Putting the array in the final array (maybe not necessary)
                }
                $this->CI->session->set_userdata('achievements', $achievements);    // Puts the array and the different achievements in a session variable to be retrieved by the sidebar
                $this->CI->session->set_userdata('achievements_to_claim', $returned_results1);    // Puts the number of achievements to claim in session variable 
            
                
                return json_encode( array('achievements'=>$achievements));    
            }

            return json_encode(array('achievements' => $achievements));
        } 
        
        
        // More functions for the sidebar may be called here (current users logged in, stats...)
    
}
