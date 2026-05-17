<?php
/**
 * 
 */
class Marketing_controller extends CI_Controller{
    
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
        $ci->lang->load('logs',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);
        $ci->lang->load('marketing',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('marketing_model');

    }
    

    public function index($action = NULL, $class = NULL){
        
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today);
        
        $yesterday = strtotime('-1 day', $today);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);
            
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $player_info = $this->users_model->get_player_info($currentUserID);          // to be used in this file
        
        $current_campaign_player_info = $this->marketing_model->select_last_ran_campaign_player($currentResortID);
        $current_campaign_player_row = $current_campaign_player_info->row();
        if ($current_campaign_player_info->num_rows() > 0) {
            $id_ongoing_campaign = $current_campaign_player_row->id_campaign;
            $last_executed = $current_campaign_player_row->last_executed;
            
            $createDate = new DateTime($last_executed);
            $last_executed_timestamp = strtotime($last_executed);
            $last_executed_tmp = date('Y-m-d H:i:s', $last_executed_timestamp); // Here we do not use GM date because the date is already put in GMT timestamp in the database, when the campaign is published.
            
            $last_executed = date('Y-m-d', $last_executed_timestamp); // Here we do not use GM date because the date is already put in GMT timestamp in the database, when the campaign is published.
            
            $level = $current_campaign_player_row->level;
            $id_started_campaign = $current_campaign_player_row->id_started_campaign;
        }
        else {
            $last_executed = '';
            $level = 1;
            $id_started_campaign = '';
            $id_ongoing_campaign = '';
        }
      
        $mode = 'zero';
        $id_selected_campaign = trim((string) $this->input->post('id_selected_campaign', TRUE));
        if ($id_selected_campaign === '') {
            $id_selected_campaign = null;
        }

        if (null !== $this->input->post('id_ongoing_campaign', TRUE) || null !== $this->input->post('id_selected_campaign', TRUE)) {
            if ($this->input->post('id_selected_campaign', TRUE) !== NULL && $this->input->post('id_selected_campaign', TRUE) !== '') {
                $campaign_to_run = $this->input->post('id_selected_campaign', TRUE);
            }
            else {
                $campaign_to_run = $this->input->post('id_ongoing_campaign', TRUE);
            }
            $campaign_status_yesterday = $this->get_campaign_status($campaign_to_run);
            $obj_message = json_decode($campaign_status_yesterday);
            $mode = $obj_message->{'mode'};
            if ($last_executed != $today_GMT) {
                $captcha_validated_action = $this->captcha_validated_action($currentResortID, $campaign_to_run, $mode, $level, $id_started_campaign);
                redirect('marketing_controller');
            }
        }

        $captcha_data = '';
        $captcha_button = '';
        if ($id_ongoing_campaign !== '' && $last_executed != $today_GMT) {
            $captcha_data = '<form method="post">';
            $captcha_data .= $this->lang->line('marketing')['click_financing'];
            $captcha_data .= '<input type="hidden" name="id_ongoing_campaign"  id="id_ongoing_campaign" value="'.$id_ongoing_campaign.'"/>';
            $captcha_data .= '<input type="hidden" name="id_selected_campaign"  id="id_selected_campaign" value=""/>';
            $captcha_data .= '<input id="done" type="submit" class="btn btn-primary btn-lg" value="'.$this->lang->line('marketing')['publish'].'" tabIndex=1/></form>';
        }
        $data['captcha_data'] = $captcha_data;
        
        $data['table_campaigns'] = '<div><table class="table table-responsive marketing_table center" align="center">
            <thead>
                <tr>
                    <th rowspan="2" class="rowspaned">'.$this->lang->line('marketing')['id'].'</th>
                    <th rowspan="2" class="rowspaned">'.$this->lang->line('marketing')['campaign'].'</th>
                    <th rowspan="2" class="rowspaned">'.$this->lang->line('marketing')['cost'].'</th>
                    <th colspan="4" class="toprow">'.$this->lang->line('home')['reward'].'</th>
                    <th rowspan="2" class="rowspaned">'.$this->lang->line('home')['level'].'</th>
                </tr>
                <tr>
                    <th class="bottomrow">'.$this->lang->line('marketing')['reward_cash'].'</th>
                    <th class="bottomrow">'.$this->lang->line('home')['genepis_title'].'</th>
                    <th class="bottomrow">'.$this->lang->line('marketing')['reward_affluence'].'</th>
                    <th class="bottomrow">'.$this->lang->line('marketing')['reward_reputation'].'</th>
                </tr>
            </thead>
            <tbody>';
        $all_campaigns = $this->marketing_model->get_all_active_campaigns();
        if ($all_campaigns->num_rows() > 0) {       // If there are campaigns in the DB (should always be true!)
            // Get last campaign ran by player
            $current_campaign_player_info = $this->marketing_model->select_last_ran_campaign_player($currentResortID);
            if ($current_campaign_player_info->num_rows() > 0) {        // Player has ran at least one campaign in the past
                $current_campaign_player_row = $current_campaign_player_info->row();
                $id_ongoing_campaign = $current_campaign_player_row->id_campaign;
                $last_executed = $current_campaign_player_row->last_executed;
                $level = $current_campaign_player_row->level-1;
                $level_display = $current_campaign_player_row->level;
                $data['id_ongoing_campaign'] = $id_ongoing_campaign;
                // Prepare message div
                $campaign_status_yesterday = $this->get_campaign_status($id_ongoing_campaign);
                $obj_message = json_decode($campaign_status_yesterday);
                //var_dump($campaign_status_yesterday);
                //$data['campaign_message'] = $obj_message->{'campaign_message'};
                $data['campaign_message'] = '';
                $mode = $obj_message->{'mode'};
            }
            else {
                $id_ongoing_campaign = '';
                $data['id_ongoing_campaign'] = '';
                $data['campaign_message'] = '<div class="alert alert-info text-center">'.$this->lang->line('marketing')['never_ran_campaign_or_not_yesterday'].'.<br></div>';
                $level = 1;
                $mode = 'zero';
            }
            foreach ($all_campaigns->result() as $all_campaigns_array){
                $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
                $description_language = 'description_'.$this->session->userdata('site_lang');     // outputs description_english or description_french for the DB columns
                $campaign_name = $all_campaigns_array->$name_language;
                $campaign_desc = $all_campaigns_array->$description_language;
                $reward_affluence = $all_campaigns_array->reward_affluence;
                $row_campaign_id = $all_campaigns_array->id_campaign;
              
                // declare ogininal reward values
                $original_cost = $all_campaigns_array->cost;
                $original_reward_cash = $all_campaigns_array->reward_cash;
                $original_reward_genepis = $all_campaigns_array->reward_genepis;
                $original_reward_affluence = ($reward_affluence-1)*100;
                $original_reward_reputation = $all_campaigns_array->reward_reputation;
                
                if ($id_ongoing_campaign == $row_campaign_id && $mode != 'zero') {
                    $class_row_table = 'selected';
                    if ($level <= 30)
                        $bonus_values = $this->get_bonus_values($level_display, $row_campaign_id);
                    else
                        $bonus_values = $this->get_bonus_values(1, $row_campaign_id);
                    
                    if ($bonus_values !== FALSE) {
                        $data['table_campaigns'] .= '<tr class="campaign_row '.$class_row_table.'" data-id_campaign="'.$row_campaign_id.'">';
                        $data['table_campaigns'] .= '<td nowrap>'.$all_campaigns_array->id_campaign.'</td>';
                        $data['table_campaigns'] .= '<td><b>'.$all_campaigns_array->$name_language.':</b> '.$all_campaigns_array->$description_language.'</td>';
                        if ($original_cost != 0)
                            $data['table_campaigns'] .= '<td nowrap><span class="invalid_bonus">'.number_format($original_cost, 0, ',', ' ').' €</span> '.number_format($bonus_values['display_cost'], 0, ',', ' ').' €<br><span class="bonus_value_green">(+'.$bonus_values['display_cost_bonus'].'%)</span></td>';
                        else
                            $data['table_campaigns'] .= '<td nowrap>0</td>';
                        if ($original_reward_cash != 0)
                            $data['table_campaigns'] .= '<td nowrap><span class="invalid_bonus">+'.number_format($original_reward_cash, 0, ',', ' ').' €</span> +'.number_format($bonus_values['display_reward_cash'], 0, ',', ' ').' €<br><span class="bonus_value_green">(+'.$bonus_values['display_cash_bonus'].'%)</span></td>';
                        else
                            $data['table_campaigns'] .= '<td nowrap>+0</td>';
                        if ($original_reward_genepis != 0)
                            $data['table_campaigns'] .= '<td nowrap><span class="invalid_bonus">+'.number_format($original_reward_genepis, 0, ',', ' ').'</span> +'.number_format($bonus_values['display_reward_genepis'], 0, ',', ' ').'<br><span class="bonus_value_green">(+'.$bonus_values['display_genepis_bonus'].'%)</span></td>';
                        else
                            $data['table_campaigns'] .= '<td nowrap>+0</td>';
                        if ($original_reward_affluence != 0)
                            $data['table_campaigns'] .= '<td nowrap><span class="invalid_bonus">+'.number_format($original_reward_affluence, 2, ',', ' ').'%</span> +'.number_format($bonus_values['display_reward_affluence'], 2, ',', ' ').'%<br><span class="bonus_value_green">(+'.$bonus_values['display_affluence_bonus'].'%)</span></td>';
                        else
                            $data['table_campaigns'] .= '<td nowrap>+0</td>';
                        if ($original_reward_reputation != 0)
                            $data['table_campaigns'] .= '<td nowrap><span class="invalid_bonus">+'.number_format($original_reward_reputation, 0, ',', ' ').'</span> +'.number_format($bonus_values['display_reward_reputation'], 0, ',', ' ').'<br><span class="bonus_value_green">(+'.$bonus_values['display_reputation_bonus'].'%)</span></td>';
                        else
                            $data['table_campaigns'] .= '<td nowrap>+0</td>';
                        $progress_pct = round(((int)$level_display / 30) * 100);
                        $data['table_campaigns'] .= '<td nowrap style="min-width:100px"><div class="progress" title="'.(int)$level_display.'/30" style="min-width:80px"><div class="progress-bar bg-success" role="progressbar" style="width:'.(int)$progress_pct.'%" aria-valuenow="'.(int)$level_display.'" aria-valuemin="0" aria-valuemax="30">'.(int)$level_display.'/30</div></div></td>';
                        $data['table_campaigns'] .= '</tr>';
                    }
                    else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['something_went_wrong'].'</div>'); 
                        redirect('marketing_controller');
                    }
                }
                else {
                    $data['table_campaigns'] .= '<tr class="campaign_row" data-id_campaign="'.$row_campaign_id.'">
                        <td nowrap>'.$all_campaigns_array->id_campaign.'</td>
                        <td><b>'.$all_campaigns_array->$name_language.':</b> '.$all_campaigns_array->$description_language.'</td>
                        <td nowrap>'.number_format($original_cost, 0, ',', ' ').' €</td>
                        <td nowrap>+'.number_format($original_reward_cash, 0, ',', ' ').' €</td>
                        <td nowrap>+'.number_format($original_reward_genepis, 0, ',', ' ').'</td>
                        <td nowrap>+'.number_format($original_reward_affluence, 2, ',', ' ').'%</td>
                        <td nowrap>+'.number_format($original_reward_reputation, 0, ',', ' ').'</td>
                        <td nowrap>1/30</td>
                    </tr>';
                }
            }
            $data['table_campaigns'] .= '</tbody></table></div>';
        }

        // Campaign history
        $name_language = 'name_'.$this->session->userdata('site_lang');
        $campaign_history = $this->marketing_model->get_campaign_history_with_info_DB($currentResortID, $name_language);
        $data['campaign_history'] = $campaign_history;

        // Campaign statistics
        $campaign_stats = $this->marketing_model->get_campaign_stats_DB($currentResortID);
        if ($campaign_stats->num_rows() > 0) {
            $stats_row = $campaign_stats->row();
            $data['stats_total_published'] = (int)$stats_row->total_published;
            $data['stats_max_level'] = (int)$stats_row->max_level;
        } else {
            $data['stats_total_published'] = 0;
            $data['stats_max_level'] = 0;
        }
            
      
        // Displaying the marketing view
        $data['main_content'] = 'marketing';
        $this->load->view('templates/default',$data);  
        
        
        $data = "IP: ".$_SERVER['REMOTE_ADDR']." Browser: ".$_SERVER['HTTP_USER_AGENT']." --- ID campaign: ".$id_ongoing_campaign." ID started campaign: ".$id_started_campaign." Last executed: ".$last_executed." Level: ".$level." Mode: ".$mode."\n";
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." INFO [".$currentUserID."-".$currentResortID."] TopPage - ".$data;
        write_file(FCPATH . '/application/controllers/logs_marketing/Marketing_campaigns_problem.log', $data_formatted, "a+");
    }
   
    
    public function get_bonus_values($level, $campaign_id) {  
        
        $level = $level-1;  // Adjusting level to the bonus level (not the level the player will reach)
        
        $campaign_info = $this->marketing_model->get_specific_campaign_info($campaign_id);
        if ($campaign_info->num_rows() > 0) {
            $campaign_info_row = $campaign_info->row();
            // declare ogininal reward values
            $original_cost = $campaign_info_row->cost;
            $original_reward_cash = $campaign_info_row->reward_cash;
            $original_reward_genepis = $campaign_info_row->reward_genepis;
            $reward_affluence = $campaign_info_row->reward_affluence;
            $original_reward_affluence = ($reward_affluence-1)*100;
            $original_reward_reputation = $campaign_info_row->reward_reputation;

            $array_result = array();
            // Actual reward/cost values (e.g. level 5 for 10000€) : (1.03^5)*10000 = 1.159*10000 = 11592€
            $array_result['display_cost'] = pow(MARKETING_COST_INCREASE_PER_LEVEL,$level) * $original_cost;             // cost, e.g. 10000€
            // Bonus is calculated (e.g. level 5) : (1.03^5)-1*100 = (1.159-1)*100 = 0.159*100 = round(15.9%) = 16%
            $array_result['display_cost_bonus'] = round((float)(pow(MARKETING_COST_INCREASE_PER_LEVEL,$level)-1) * 100 );   // Bonus, e.g. 16%
            $array_result['display_reward_cash'] = pow(MARKETING_CASH_INCREASE_PER_LEVEL,$level) * $original_reward_cash;
            $array_result['display_cash_bonus'] = round((float)(pow(MARKETING_CASH_INCREASE_PER_LEVEL,$level)-1) * 100 );
            $array_result['display_reward_genepis'] = pow(MARKETING_GENEPIS_INCREASE_PER_LEVEL,$level) * $original_reward_genepis;
            $array_result['display_genepis_bonus'] = round((float)(pow(MARKETING_GENEPIS_INCREASE_PER_LEVEL,$level)-1) * 100 );
            $array_result['display_reward_affluence'] = pow(MARKETING_AFFLUENCE_INCREASE_PER_LEVEL,$level) * $original_reward_affluence;
            $array_result['display_affluence_bonus'] = round((float)(pow(MARKETING_AFFLUENCE_INCREASE_PER_LEVEL,$level)-1) * 100 );
            $array_result['display_reward_reputation'] = pow(MARKETING_REP_INCREASE_PER_LEVEL,$level) * $original_reward_reputation;
            $array_result['display_reputation_bonus'] = round((float)(pow(MARKETING_REP_INCREASE_PER_LEVEL,$level)-1) * 100 );
            
            return $array_result;
        }
        else {
            return FALSE;
        }
        
    }  
    
    
    
    
    public function get_campaign_status_yesterday($id_selected_campaign, $last_run_is_selected, $ran_today, $level) {
        //echo '$ran_today: '.$ran_today.' END';
        if($last_run_is_selected === true && $ran_today == false) {  // Same campaign as last time > CONTINUE
            if ($level < 30)  {         // Max level not reach: continue levels
                $campaign_message_result = $this->get_campaign_message($id_selected_campaign, 'continue');
                //echo 'CHECK1';
            }
            else  {          // If the user reached the max level, we reset the awards and change the info text
                $campaign_message_result = $this->get_campaign_message($id_selected_campaign, 'zero_max');
                //echo 'CHECK2';
            }
        }
        else if ($ran_today === true) { // Same campaign as yesterday is selected and is already ran today > no_action
            $campaign_message_result = $this->get_campaign_message($id_selected_campaign, 'no_action');
            //echo 'CHECK4';
        }
        else if ($ran_today == false ) { // Regardless of selected campaign, not campaign was ran yesterday > RESET TO ZERO
            //echo 'CHECK5';
            $campaign_message_result = $this->get_campaign_message($id_selected_campaign, 'zero'); 
        }
        return $campaign_message_result;
    }
                    
                    
    public function get_campaign_status($id_selected_campaign = NULL) {
        
        if (null !== trim($this->input->post('id_campaign', TRUE)) && trim($this->input->post('id_campaign', TRUE)) != '')   // If posted (from JS only), we take posted value. Otherwise, take from method declaration 
            $id_selected_campaign = trim($this->input->post('id_campaign', TRUE));
        
            $currentUserID = $this->users_model->get_user_id();          // to be used in this file
            $currentResortID = $this->users_model->get_resort_id($currentUserID);

            $today = strtotime('now');
            $yesterday = strtotime('-1 day', $today);
            $yesterday_GMT = gmdate('Y-m-d', $yesterday);
            $today_GMT = gmdate('Y-m-d', $today);

            $last_run_is_selected = false;
            $ran_today = false;
            
            $current_campaign_player_info = $this->marketing_model->select_last_ran_campaign_player($currentResortID);
            if ($current_campaign_player_info->num_rows() > 0) {    // The user has at least one campaign running in the past
                $current_campaign_player_row = $current_campaign_player_info->row();
                $last_run_campaign = $current_campaign_player_row->id_campaign;     // ID of the last executed campaign
                $last_executed = $current_campaign_player_row->last_executed;   // Last day the last campaign was executed
                $level = $current_campaign_player_row->level;   // Current level of the campaign
                //$campaign_completed_status = $current_campaign_player_row->completed;
                $last_executed_day = date('Y-m-d', strtotime($last_executed)); // Here we do not use GM date because the date is already put in GMT timestamp in the database, when the campaign is published.
                if ($last_executed_day == $today_GMT) {
                    $ran_today = true;
                }
                else {
                    $ran_today = false;
                }
                
                if ($last_run_campaign == $id_selected_campaign) {      // If last campaign is same as selected
                    $last_run_is_selected = true;
                }
                else {              // Last campaign is different than selected
                    $last_run_is_selected = false;
                }
            }
            else {      // The player has never run any campaign
                $level = 1;
                $last_run_is_selected = false;
                //$campaign_completed_status = 0;
                $ran_today = false;
            }
        
            $get_campaign_message = $this->get_campaign_status_yesterday($id_selected_campaign, $last_run_is_selected, $ran_today, $level);
        
        if (null !== trim($this->input->post('id_campaign', TRUE)) && trim($this->input->post('id_campaign', TRUE)) != '')   // If posted (from JS only), we echo the result, if not, we return it in this file
            echo $get_campaign_message;    
        else
            return $get_campaign_message;
    }
    
    public function get_campaign_message($id_ongoing_campaign, $mode) {
        
        $captcha_data = '<form method="post">';
        $captcha_data .= $this->lang->line('marketing')['click_financing'].' ';
        $captcha_data .= '<input type="hidden" name="id_ongoing_campaign"  id="id_ongoing_campaign" value="'.$id_ongoing_campaign.'"/>';
        $captcha_data .= '<input type="hidden" name="id_selected_campaign"  id="id_selected_campaign" value=""/>';
        $captcha_data .= '<input id="done" type="submit" class="btn btn-primary btn-lg" value="'.$this->lang->line('marketing')['publish'].'" tabIndex=1/></form>';

        if ($mode == 'continue') {
            $campaign_message = '<div class="alert alert-info text-center">'.$this->lang->line('marketing')['campaign_id'].' '.$id_ongoing_campaign.' '.$this->lang->line('marketing')['is_selected'].' '.$this->lang->line('marketing')['was_running_yesterday'].'.<br>'.$this->lang->line('marketing')['continue_to_increase'].'</div>';
            $mode = 'continue';
            $display_captcha = true;
        }
        else if ($mode == 'zero' && $id_ongoing_campaign == 'undefined') {
            $campaign_message = '<div class="alert alert-info text-center">'.$this->lang->line('marketing')['never_ran_campaign_or_not_yesterday'].'</div>';
            $captcha_data = '';
            $mode = 'no_action';
            $display_captcha = false;
        }
        else if ($mode == 'zero') {
            $campaign_message = '<div class="alert alert-warning text-center">'.$this->lang->line('marketing')['campaign_id'].' '.$id_ongoing_campaign.' '.$this->lang->line('marketing')['is_selected'].'. '.$this->lang->line('marketing')['start_zero_or_change'].'</div>';
            $mode = 'zero';
            $display_captcha = true;
        }
        else if ($mode == 'zero_max') {
            $campaign_message = '<div class="alert alert-warning text-center">'.$this->lang->line('marketing')['campaign_id'].' '.$id_ongoing_campaign.' '.$this->lang->line('marketing')['is_selected'].'. '.$this->lang->line('marketing')['start_zero_max'].'</div>';
            $display_captcha = true;
            $mode = 'zero_max';
        }
        else if ($mode == 'no_action') {
            $campaign_message = '<div class="alert alert-info text-center">'.$this->lang->line('marketing')['campaign_id'].' '.$id_ongoing_campaign.' '.$this->lang->line('marketing')['is_selected'].'<br>'.$this->lang->line('marketing')['keep_increase'].'</div>';
            $display_captcha = false;
            $captcha_data = '';
            $mode = 'no_action';
        }
        
        $json = json_encode(array('campaign_message' => $campaign_message, 'display_captcha' => $display_captcha, 'mode' => $mode, 'captcha_data' => $captcha_data));
        return $json;
    }
    
    public function captcha_validated_action($currentResortID, $id_selected_campaign, $mode, $level, $id_started_campaign = null) {
        
        if ($id_selected_campaign != null) {
            $currentUserID = $this->users_model->get_user_id();
            switch ($mode) :
                case ('continue'):
                    // Get bonus values for new level of existing campaign
                    $bonus_values = $this->get_bonus_values($level, $id_selected_campaign);
                break;
                case ('zero'):
                case ('zero_max'):
                    // Get bonus values for new campaign (level 0)
                    $bonus_values = $this->get_bonus_values(1, $id_selected_campaign);
                break;
                case ('no_action'):
                break;
            endswitch;

            if (isset($bonus_values) && $bonus_values !== FALSE) {
                // initiates bonuses
                $cost = $bonus_values['display_cost'];
                $reward_cash = $bonus_values['display_reward_cash'];
                $reward_genepis = $bonus_values['display_reward_genepis'];
                $reward_affluence = $bonus_values['display_reward_affluence'];
                $reward_reputation = $bonus_values['display_reward_reputation'];
                
                $bonus_values_previous_level = $this->get_bonus_values($level-1, $id_selected_campaign);
                $reward_affluence_previous_level = $bonus_values_previous_level['display_reward_affluence'];
                
                $cash_player = $this->users_model->get_cash_player();        // Get how much cash the player has (in dollar)
                $money_after_payment = $cash_player - $cost;          // we calculate hos much the player will have left after the payment
                if ($money_after_payment >= 0) {                            // If enough cash
                    if ($removeCashQuery = $this->users_model->pay_item($cost, $cash_player)){      //the paiment for the campaign has been taken
                        $reward_campaign_to_resort = $this->marketing_model->reward_campaign_to_resort_DB($currentResortID, $reward_cash, $reward_reputation);  // Updates cash and reputation
                        $reward_campaign_to_player = $this->marketing_model->reward_campaign_to_player_DB($currentUserID, $reward_genepis); // Updates genepis
                        $check_affluence_bonus_exists = $this->marketing_model->check_affluence_bonus_exists($currentResortID); // Checks if there is already a bonus set for alluence today for this player
                        if ($check_affluence_bonus_exists->num_rows() > 0) {
                            $reward_affluence_to_add = $reward_affluence - $reward_affluence_previous_level;
                            $reward_campaign_affluence_bonus = $this->marketing_model->reward_campaign_update_affluence_bonus_DB($currentResortID, $reward_affluence_to_add); // Updates existing game_affluence_bonus entry table for next script
                        }
                        else {
                            $data_reward_affluence = array(
                                'id_resort' => $currentResortID,
                                'affluence_bonus' => $reward_affluence,
                                'date' => gmdate('Y-m-d')
                            );
                            $reward_campaign_affluence_bonus = $this->marketing_model->reward_campaign_insert_affluence_bonus_DB($currentResortID, $data_reward_affluence); // Add entry in game_affluence_bonus table for next script
                        }
                        // Adds revenue to the general revenue table 
                        $add_revenue_history_query_main_table = add_revenue_stat_table($currentResortID, $reward_cash, 'revenue');
                        $add_cost_history_query_main_table = add_revenue_stat_table($currentResortID, $cost, 'expenses');
                        // Adds revenue to the Marketing revenue table
                        $add_revenue_history_query_specific_table = add_revenue_stat_table($currentResortID, $reward_cash, 'rev_marketing');
                        $add_cost_history_query_specific_table = add_revenue_stat_table($currentResortID, $cost, 'cost_marketing');
                        if ($reward_campaign_to_resort && $reward_campaign_to_player && $reward_campaign_affluence_bonus) {
                            $need_to_update_campaign = true;
                        }
                        else {
                            $need_to_update_campaign = false;
                        }
                    }
                    else {                        //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['something_went_wrong'].'</div>'); 
                        redirect('marketing_controller');
                    }
                }
                else {                        //not enough money
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['not_enough_money'].'</div>');      
                    redirect('marketing_controller');
                }
                
                if ($need_to_update_campaign === true) {        
                    switch ($mode) :
                        case ('continue'):
                            
                            $data = "IP: ".$_SERVER['REMOTE_ADDR']." Browser: ".$_SERVER['HTTP_USER_AGENT']." --- ID campaign: ".$id_selected_campaign." ID started campaign: ".$id_started_campaign." Level: ".$level." Mode: ".$mode."\n";
                            $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
                            $data_formatted = $timestamp." INFO [".$currentUserID."-".$currentResortID."] ContinueMode - ".$data;
                            write_file(FCPATH . '/application/controllers/logs_marketing/Marketing_campaigns_problem.log', $data_formatted, "a+");
                    
                            // Updates the started_marketing table to valid the progress
                            $update_campaign = $this->marketing_model->update_campaign_DB($currentResortID, $id_started_campaign);
                            if ($level == 29) { // If upgrading to the highest level
                                $reward_campaign_to_resort = $this->marketing_model->mark_campaign_completed_DB($currentResortID, $id_started_campaign);  // Mark the campaign as completed
                            }
                            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('marketing')['campaign_published'].'</div>');    
                        break;        
                        case ('zero'):
                        case ('zero_max'):
                            
                            $data = "IP: ".$_SERVER['REMOTE_ADDR']." Browser: ".$_SERVER['HTTP_USER_AGENT']." --- ID campaign: ".$id_selected_campaign." ID started campaign: ".$id_started_campaign." Level: ".$level." Mode: ".$mode."\n";
                            $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
                            $data_formatted = $timestamp." INFO [".$currentUserID."-".$currentResortID."] ZeroZeroMaxMode - ".$data;
                            write_file(FCPATH . '/application/controllers/logs_marketing/Marketing_campaigns_problem.log', $data_formatted, "a+");
                            
                            $data_insert = array (
                                'id_resort' => $currentResortID,
                                'id_campaign' => $id_selected_campaign,              
                                'level' => 2,                     
                                'started_datetime' => gmdate('Y-m-d H:i:s'),
                                'last_executed' => gmdate('Y-m-d H:i:s')
                            );
                            $start_campaign = $this->marketing_model->start_campaign_DB($data_insert);

                            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">'.$this->lang->line('marketing')['campaign_published'].'</div>');    
                        break;
                        case ('no_action'):
                            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('marketing')['campaign_not_published'].'</div>'); 
                        break;       
                    endswitch;

                    
                    
        
        
                    // Achievement handling
                    $data_ach = array (
                        'id_resort' => $currentResortID,
                        'id_campaign' => $id_selected_campaign,       
                        'item' => 'marketing_campaign',      
                        'level' => $level
                    );
                    $call_achievements_check = call_achievements_check($data_ach, 'publish_level');     // For "publish first campaign" or "publish a level X campaign"
                    $call_achievements2_check = call_achievements_check($data_ach, 'publish_number');   // For "publish X number of campaigns"

                    // Notifications for user activity and admin logs
                    $level_text = $level;
                    //$level_text = $level - 1;
                    $notification_text = $this->lang->line('marketing')['title'].' '.$id_selected_campaign.' '.$this->lang->line('logs')['level'].$level_text.' '.$this->lang->line('logs')['published'];
                    $notification_text .= ' ('.$this->lang->line('marketing')['cost'].': '.number_format($cost, 0, '.', ' ').'€, '.$this->lang->line('home')['rewards'].': ';
                    if ($reward_cash != 0)
                        $notification_text .= '+'.number_format($reward_cash, 0, '.', ' ').'€, ';
                    if ($reward_genepis != 0)
                        $notification_text .= '+'.number_format($reward_genepis, 0, '.', ' ').' '.$this->lang->line('navbar')['genepis'].', ';
                    if ($reward_affluence != 0)
                        $notification_text .= '+'.number_format($reward_affluence, 2, ',', ' ').'% '.$this->lang->line('home')['affluence_min'].', ';
                    if ($reward_reputation != 0)
                        $notification_text .= '+'.number_format($reward_reputation, 0, '.', ' ').' '.$this->lang->line('home')['mini_reputation'].', ';
                    $notification_text = substr($notification_text, 0, -2);
                    $notification_text .= ')';
                    $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['marketing'], 'data' => $notification_text) );   // Add a log row to the game_player_logs table
                    $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['marketing'], 'data' => $notification_text) );   // Add a log row to the game_player_logs table     
                } 
                else {                        //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['something_went_wrong'].'</div>'); 
                    redirect('marketing_controller');
                }       
            }
            else {                        //query for payment didn't succeed for some reasons. Could be that user didn't have enough money
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$this->lang->line('home')['something_went_wrong'].'</div>'); 
                redirect('marketing_controller');
                //echo 'values: $currentResortID: '.$currentResortID.' /$id_selected_campaign: '.$id_selected_campaign.' /$mode: '.$mode.' /$level: '.$level.' /$id_started_campaign: '.$id_started_campaign;
            } 
        }
    }   
}
