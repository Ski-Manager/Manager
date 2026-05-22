<?php
/**
 * 
 */
class Achievements_controller extends CI_Controller{
    
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
        $ci->lang->load('achievements',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('achievements_model');
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
        $data['title'] = '<h2>'.$this->lang->line('navbar')['achievements'].'</h2>'; 
        $data['introAchievements'] = '<div>'.$this->lang->line('achievements')['intro'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            // Displaying the building view
            $data1 = $this->achievementsBlock($currentUserID);    // Calls the generic block funtion
            
            $data['main_content'] = 'achievements';
            $data = array_merge($data,$data1);
            $this->load->view('templates/default',$data);
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    
    protected function get_unlock_price($current_cash_balance)   {
        $getTaxCharge = [
            [
                'amount_range_from' => 0,
                'amount_range_to' => 100000,
                'tax_percentage' => 1,
                 ],
            [
                'amount_range_from' => 100001,
                'amount_range_to' => 500000,
                'tax_percentage' => 5,
                 ],
            [
                'amount_range_from' => 500000,
                'amount_range_to' => 2000000,
                'tax_percentage' =>10,
                 ],
            [
                'amount_range_from' => 2000001,
                'amount_range_to' => 5000000,
                'tax_percentage' =>15,
                 ],
            [
                'amount_range_from' => 5000001,
                'amount_range_to' => 10000000,
                'tax_percentage' =>25,
                 ],
            [
                'amount_range_from' => 10000001,
                'amount_range_to' => 25000000,
                'tax_percentage' =>40,
                 ],
            [
                'amount_range_from' => 25000001,
                'amount_range_to' => 100000000,
                'tax_percentage' =>60,
                 ],
            [
                'amount_range_from' => 100000001,
                'amount_range_to' => 100000000000,
                'tax_percentage' =>70,
                 ]
        ];
        
        $resultArray = [];
        foreach ($getTaxCharge as $key => $value) {
            if ($current_cash_balance <= $value['amount_range_to']) {
                $resultArray['unlock_price'] = $value['tax_percentage'] * -1 * $current_cash_balance/100;
                $resultArray['percentage'] = $value['tax_percentage'];
                break;
            }
        }
        
        return $resultArray;
    }
    
    
    
    
    /**
     * achievementsBlock        Displays a standard achievements block.
     * 
     * @param type $currentUserID   Current user ID
     * @return array                Returns the content of the page
     */
    public function achievementsBlock($currentUserID){
        $data['test'] = '';
        $name_language = 'name_'.$this->session->userdata('site_lang');
        $description_language = 'description_'.$this->session->userdata('site_lang');
        $achievements_data = $this->achievements_model->get_all_achievements_data($name_language, $description_language, 1);
        $player_count = $this->users_model->player_count();

        $table_achievements  = '<div class="table-responsive">';
        $table_achievements .= '<table class="table table-hover achievements"><thead><tr>';
        $table_achievements .= '<th></th>';
        $table_achievements .= '<th>'.$this->lang->line('achievements')['col_achievement'].'</th>';
        $table_achievements .= '<th>'.$this->lang->line('achievements')['col_rewards'].'</th>';
        $table_achievements .= '<th>'.$this->lang->line('achievements')['col_rarity'].'</th>';
        $table_achievements .= '<th>'.$this->lang->line('achievements')['col_progress'].'</th>';
        $table_achievements .= '<th>'.$this->lang->line('achievements')['col_status'].'</th>';
        $table_achievements .= '</tr></thead><tbody>';

        $count = 0;
        foreach ($achievements_data->result() as $achievements_data_array) {
            $tr_class = ($count % 2 === 0) ? 'ach_even' : 'ach_odd';
            $count++;

            $a_id          = $achievements_data_array->id_achievement;
            $a_image       = $achievements_data_array->image_url;
            $a_name        = $achievements_data_array->$name_language;
            $a_description = $achievements_data_array->$description_language;
            $a_unlocked_count    = $achievements_data_array->unlocked_count;
            $a_reward_reputation = $achievements_data_array->reward_reputation;
            $a_reward_cash       = $achievements_data_array->reward_cash;
            $a_reward_genepis    = $achievements_data_array->reward_genepis;

            if ($a_reward_cash >= 0) {
                $sign_reward_cash  = '+';
                $class_reward_cash = '';
            } else {
                $sign_reward_cash     = '';
                $current_player_cash  = $this->users_model->get_cash_player();
                $tax_array            = $this->get_unlock_price($current_player_cash);
                $a_reward_cash        = $tax_array['unlock_price'];
                $a_reward_cash        = min($a_reward_cash, -1000000);
                $class_reward_cash    = 'class="red_text"';
            }

            $percentage_unlocked = ($player_count == 0) ? 0 : $a_unlocked_count / $player_count * 100;
            switch (true) {
                case ($percentage_unlocked < 2):
                    $rarity = $this->lang->line('achievements')['legendary'];
                    break;
                case ($percentage_unlocked < 7):
                    $rarity = $this->lang->line('achievements')['epic'];
                    break;
                case ($percentage_unlocked < 15):
                    $rarity = $this->lang->line('achievements')['ultra_rare'];
                    break;
                case ($percentage_unlocked < 30):
                    $rarity = $this->lang->line('achievements')['rare'];
                    break;
                case ($percentage_unlocked < 50):
                    $rarity = $this->lang->line('achievements')['uncommon'];
                    break;
                case ($percentage_unlocked < 75):
                    $rarity = $this->lang->line('achievements')['common'];
                    break;
                default:
                    $rarity = $this->lang->line('achievements')['really_common'];
                    break;
            }

            // Get achievement status for current player
            $achievements_player_data = $this->achievements_model->get_achievements_status_player($a_id, $currentUserID);
            $progress    = 0;
            $claimed     = 0;
            $status_cell = '';

            if ($achievements_player_data->num_rows() > 0) {
                $achievements_player_row = $achievements_player_data->row();
                $progress = (float) $achievements_player_row->progress;
                $claimed  = $achievements_player_row->claimed;

                if ($progress == 100) {
                    if ($claimed == 1) {
                        $unlocked_datetime = $achievements_player_row->unlocked_datetime;
                        $unlocked_date     = date('d/m/Y', strtotime($unlocked_datetime));
                        $unlocked_time     = date('H:i:s', strtotime($unlocked_datetime));
                        $status_cell = '<div class="small_text text-center tooltip tooltip-bottom" data-tip="'.$this->lang->line('achievements')['achievement_completed'].'">'
                            .'<img height="20" width="20" src="'.base_url('img/icons/unlocked.png').'"/> '
                            .$unlocked_date.'<br>'.$unlocked_time.'</div>'
                            .'<button type="button" class="btn btn-sm btn-outline-secondary share-achievement-btn mt-1"'
                            .' data-achievement-name="'.htmlspecialchars($a_name, ENT_QUOTES, 'UTF-8').'"'
                            .' data-achievement-rarity="'.htmlspecialchars($rarity, ENT_QUOTES, 'UTF-8').'">'
                            .'<i class="fa-solid fa-share-nodes"></i> '.$this->lang->line('achievements')['share_achievement']
                            .'</button>';
                    } else {
                        $status_cell = '<button type="button" class="btn btn-warning btn-sm claim_button claim-achievement-btn tooltip tooltip-bottom" id="achievement-'.$a_id.'"'
                            .' data-achievement-id="'.$a_id.'"'
                            .' data-achievement-name="'.htmlspecialchars($a_name, ENT_QUOTES, 'UTF-8').'"'
                            .' data-achievement-rarity="'.htmlspecialchars($rarity, ENT_QUOTES, 'UTF-8').'"'
                            .' data-tip="'.$this->lang->line('achievements')['claim'].'">'
                            .'<img src="'.base_url('img/icons/claim.png').'" height="20" width="20" alt=""/> '
                            .$this->lang->line('achievements')['claim'].'</button>';
                    }
                }
            }

            $img_src = !empty($a_image) ? base_url($a_image) : base_url('img/icons/ach1.png');

            $table_achievements .= '<tr class="'.$tr_class.'">';
            $table_achievements .= '<td class="text-center align-middle" style="width:60px"><img src="'.$img_src.'" height="50" width="50" alt="'.htmlspecialchars($a_name, ENT_QUOTES, 'UTF-8').'"></td>';
            $table_achievements .= '<td class="align-middle"><strong>'.$a_name.'</strong><br><small class="text-muted">'.$a_description.'</small></td>';
            $table_achievements .= '<td class="align-middle small">'
                .'+ '.number_format($a_reward_reputation, 0, ',', ' ').' '.$this->lang->line('home')['reputation'].'<br>'
                .'<span '.$class_reward_cash.'>'.$sign_reward_cash.' '.number_format($a_reward_cash, 0, ',', ' ').' €</span><br>'
                .'+ '.number_format($a_reward_genepis, 0, ',', ' ').' '.$this->lang->line('home')['genepis_title']
                .'</td>';
            $table_achievements .= '<td class="align-middle small text-center">'.$rarity.'<br><span class="text-muted">'.round($percentage_unlocked, 1).'%</span></td>';
            $table_achievements .= '<td class="text-center align-middle"><div class="chart center" id="graph-'.$a_id.'" data-size="50" data-line="6" data-percent="'.$progress.'"></div></td>';
            $table_achievements .= '<td class="text-center align-middle" id="status-'.$a_id.'" style="min-width:130px">'.$status_cell.'</td>';
            $table_achievements .= '</tr>';
            $table_achievements .= '<tr class="'.$tr_class.'"><td colspan="6" id="message_claimed-'.$a_id.'" class="p-0 border-0"></td></tr>';
        }

        $table_achievements .= '</tbody></table></div>';
        $data['table_achievements'] = $table_achievements;
        return $data;
    }
    
 /**
  * claim_reward    Offer the reward to the player for the clicked completed achievement.
  * The function is called by the Javascript function monitoring click on achievement-id button in home.js    
  */
    public function claim_reward () {
        $currentUserID = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        // Gets the values posted by the Jquery function
        $achievementID = trim($this->input->post('achievementID', TRUE));
        // Get the achievement status for current player
        $achievements_player_data = $this->achievements_model->get_achievements_status_player($achievementID, $currentUserID);
        if ($achievements_player_data->num_rows() > 0) {
            $achievements_player_row = $achievements_player_data->row();
            $current_progress = $achievements_player_row->progress;
            $claimed_status = $achievements_player_row->claimed;
            $unlocked_datetime = $achievements_player_row->unlocked_datetime;
            $unlocked_date = date('d/m/Y',strtotime($unlocked_datetime));
            $unlocked_time = date('H:i:s',strtotime($unlocked_datetime));
            if ($current_progress == 100 && $claimed_status == 0) { // The achievement is not claimed but completed (100%)
                $this->db->trans_start();
                // Get data for the current achievement
                $achievements_data = $this->achievements_model->get_specific_achievements_data($achievementID);
                $achievements_row = $achievements_data->row();
                $a_reward_cash = (int) $achievements_row->reward_cash;
                
                if ($a_reward_cash < 0) {
                    $current_player_cash = $this->users_model->get_cash_player();
                    $tax_array = $this->get_unlock_price($current_player_cash);
                    $a_reward_cash = $tax_array['unlock_price'] ?? 0;
                    $a_reward_cash = min($a_reward_cash, -1000000);
                }
                
                $a_reward_genepis = (int) $achievements_row->reward_genepis;
                $a_reward_reputation = (int) $achievements_row->reward_reputation;
                // Updates "claimed column"
                $this->db->set('claimed', 1);
                $this->db->where('id_player' , $currentUserID);                              
                $this->db->where('id_achievement' , $achievementID);                              
                $this->db->update('user_achievements');
                // START PAYMENT ACHIEVEMENT (when claiming)
                $this->db->set('cash', 'cash+'.$a_reward_cash,FALSE);
                $this->db->set('reputation', 'reputation+'.$a_reward_reputation,FALSE);
                $this->db->where('id_resort' , $currentResortID);                              
                $this->db->update('game_resorts');
                // START PAYMENT GENEPIS IF REQUIRED
                $this->db->set('genepis', 'genepis+'.$a_reward_genepis,FALSE);
                $this->db->where('id_player' , $currentUserID);                              
                $this->db->update('game_players');
                // Adds revenue to the general revenue table 
                $add_revenue_history_query_main_table = add_revenue_stat_table($currentResortID, $a_reward_cash, 'revenue');
                // Adds revenue to the Other revenue table
                $add_revenue_history_query_specific_table = add_revenue_stat_table($currentResortID, $a_reward_cash, 'rev_achievements');
                
                $a_requires = $achievements_row->requires;       // Contains of string of the different requirements to unlock
                $a_requires_decoded = json_decode($a_requires);  // Decode the string to create a stdClass object. That allows us to access any field.
                $action = $a_requires_decoded?->action ?? null;
                $sector_to_unlock = ($a_requires_decoded !== null && isset($a_requires_decoded->sector)) ? $a_requires_decoded->sector : null;
                
                if ($action === 'unlock_sector' && $sector_to_unlock !== null) {
                    $sector_already_unlocked = $this->db->where('id_resort', $currentResortID)->where('sector', $sector_to_unlock)->limit(1)->get('game_access_sector')->num_rows() > 0;
                    if (!$sector_already_unlocked) {
                        $current_date = gmdate('Y-m-d H:i:s');
                        $data_unlock_sector = array (
                            'id_resort' => $currentResortID,
                            'sector' => $sector_to_unlock,
                            'access_time' => $current_date
                        );
                        $this->db->insert('game_access_sector', $data_unlock_sector);       // Adding sector access for the player
                    }
                }
                    
                    
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    echo json_encode(array('returned' => 'error'));
                    return;
                }
                // Update sidebar
                $cash_player = $this->users_model->get_cash_player();                         // Get how much cash the player has after payment
                $genepis_player = $this->users_model->get_user_genepis_amount($currentUserID);                         // Get how much cash the player has after payment
                $updated_cash = $this->session->set_userdata('cash', $cash_player);          // New available cash to put in session (and sidebar)
                $updated_genepis = $this->session->set_userdata('genepis', $genepis_player);          // New available genepis to put in session (and sidebar)
                $reputation_player = $this->users_model->get_reputation_player();                         // Get how much reputation the player has after building/upgrading
                $updated_reputation = $this->session->set_userdata('reputation', $reputation_player);          // New available reputation to put in session (and sidebar)                  
                // END PAYMENT ACHIEVEMENT (when claiming)

                // Update achievements in session so sidebar reflects claimed state immediately
                $session_achievements = $this->session->userdata('achievements');
                if (is_array($session_achievements)) {
                    foreach ($session_achievements as &$ach) {
                        if ($ach['id_achievement'] == $achievementID) {
                            $ach['button'] = '<div><img width="23" height="23" src="'.base_url('img/icons/claim-grey.png').'"></div>';
                            break;
                        }
                    }
                    unset($ach);
                    $this->session->set_userdata('achievements', $session_achievements);
                }
                $current_to_claim = $this->session->userdata('achievements_to_claim');
                if ($current_to_claim > 0) {
                    $this->session->set_userdata('achievements_to_claim', $current_to_claim - 1);
                }

                // Check for unlocking sector
                $data_achievement = array ( 
                    'id_resort' => $currentResortID,
                    'id_achievement' => $achievementID       
                );
                $call_achievements_check = call_achievements_check($data_achievement, 'unlock_sector');   // Check if a corresponding achievement should be updated
                $call_achievements_check = call_achievements_check($data_achievement, 'unlock_item');   // Check if a corresponding achievement should be updated

                echo json_encode(array('returned' => true, 'unlocked_date' => $unlocked_date, 'unlocked_time' => $unlocked_time));
            }
            else {  // The achievement is either already claimed, or not completed
                if ($claimed_status == 1) {
                    echo json_encode(array('returned' => 'already_claimed'));
                } else {
                    echo json_encode(array('returned' => 'not_completed'));
                }
            }
            
        }
        else {
            echo json_encode(array('returned' => 'not_completed'));
        }
    }
    
    /**
     * get_achievements_from_session simply retrieves the achievements from the userdata session for the javascript function in home.js (refresh_achievements_sidebar)
     * Is used to refresh the sidebar when the page is loaded and a new achievement is completed.
     */
    public function get_achievements_from_session() {
        $achievements = $this->session->userdata('achievements');
        $achievements_to_claim = $this->session->userdata('achievements_to_claim');
        echo json_encode( array('achievements'=>$achievements, 'achievements_to_claim'=>$achievements_to_claim));
    }
    
}