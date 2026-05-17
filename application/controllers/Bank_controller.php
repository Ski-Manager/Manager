<?php
/**
 * 
 */
class Bank_controller extends CI_Controller{
    
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
            //$ci->lang->load('lift',$siteLang);
            //$ci->lang->load('slope',$siteLang);
            //$ci->lang->load('resort',$siteLang);
        $ci->lang->load('bank',$siteLang);
            //$ci->lang->load('staff',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $ci->lang->load('genepis',$siteLang);
        $ci->lang->load('building',$siteLang);
        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)           // Only for logged in users
            redirect('home_controller');                                 // If not logged in, redirect to homepage
        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('building_model');
        $this->load->model('bank_model');
        $this->load->model('users_model');
        $this->load->model('logs_model');
        $this->load->model('finances_model');
    }
    
    /**
     * index    Main function with top of the page (title, page description...)
     * 
     * @param type $data
     */
    public function index($data = NULL){
        $data['title'] = '<h2>'.$this->lang->line('bank')['titleMain'].'</h2>';
        $data['introBank'] = '<div>'.$this->lang->line('bank')['intro'].'</div>';
        $data['currentUserID'] = $this->users_model->get_user_id();  // to be used in the view
        $currentUserID = $this->users_model->get_user_id();          // to be used in this file
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // Checks if the resort exists       
            if ($checkIfResortExists->num_rows() > 0) {                                        // if the player has a resort, OK

                // If toursit info center build, we can display the page
                $tourist_info_data = $this->building_model->get_created_buildings_for_player($currentResortID, 'tourist_info');   // Checks if the player has built the tourist info center  
                if ($tourist_info_data->num_rows() == 1) {          // Tourist info center is built
                    $data['hideBank'] = false;                  // To display specific blocks in the View (here we display the bank)

                    $max_daily_payment = $this->get_max_daily_payment($currentResortID);
                    //$data['max_daily_payment_text'] = $max_daily_payment;
                    $data['max_daily_payment_text'] = $this->lang->line('bank')['based_last_week_profit'].' '.number_format($max_daily_payment, 0, ',', ' ').' €.';
                    $data1 = $this->bankBlock($currentResortID);    // Calls the generic block funtion for the bank
                    $data = array_merge($data, $data1);      // Merges all data to "data" for the view            
                }
                // Tourist info not built. We inform player and show a link (make new function)
                else {
                    $data['hideBank'] = true;                   // To display specific blocks in the View (here we display a message but no bank info)
                    $data['infoMessage'] = 'tourist_info_required';
                }
                // Displaying the building view
                $data['main_content'] = 'bank';
                $this->load->view('templates/default',$data);   
            }
            else { // There is no resort created
                $this->session->set_flashdata('error', 'no_resort');            // redirect to resort contoller with error message
                redirect('resort_controller');
            }
        }
        else {      // The account is not activated, redirect to activation page
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }
    }

    
    /**
     * bankBlock        Displays an bank block
     * 
     * @param type $currentResortID   Current resort ID
     * @return string               Returns the content of the page
     */
    public function bankBlock($currentResortID){
        // Sets general variables
        $data['bankLogo'] = '<i class="fa-solid fa-building-columns" title="'.$this->lang->line('bank')['titleMain'].'"></i>';
        $data['bankDesc'] = ''.$this->lang->line('bank')['desc'].'';
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns

        $bank_data = $this->bank_model->get_generic_bank_data();  
        $num_banks = $bank_data->num_rows();
        if ($num_banks > 0) {                // the bank exists in the DB (always!)
            // For each of the banks
            for ($i=0; $i<$num_banks; $i++) {
                $row = $bank_data->row_array($i);
                $data['bankName'][$i] = $row[$name_language];
                $data['bankMinLoan'][$i] = number_format($row['min_loan'], 0, ',', ' ');
                $data['bankMinLoan_raw'][$i] = $row['min_loan'];
                $data['bankMaxLoan'][$i] = number_format($row['max_loan'], 0, ',', ' ');
                $data['bankMaxLoan_raw'][$i] = $row['max_loan'];
                $data['bankInterestRate'][$i] = $row['interest_rate'];
                $data['genepis_required'][$i] = $row['genepis_required'];
                $data['bankButton'][$i] = '<td data-id_bank="'.$row['id_bank'].'" data-bank_name="'.$data['bankName'][$i].'"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('bank')['sign_up_tooltip'].'"><a href="?action=signup_loan" class="signup_loan-dialog"><button class="btn btn-success" id="bankButton_'.$i.'">'.$this->lang->line('bank')['sign_up'].'</button></a></div></td>';
            }
        }
        $data1 = $this->ongoing_loans($currentResortID);
        $data = array_merge($data, $data1);
        $data2 = $this->loan_history($currentResortID);
        $data = array_merge($data, $data2);
        $data3 = $this->investment_block($currentResortID);
        $data = array_merge($data, $data3);
        return $data;
    }
    
    public function ongoing_loans ($currentResortID){
        $data['ongoing_loans_display'] = false;
        $loan_player = $this->bank_model->get_ongoing_loan_player($currentResortID);
        
        $data['body_ongoing_loans_table'] = '';
        
        if ($loan_player->num_rows() > 0) {
            
            $data['ongoing_loans_display'] = true;
           $data['body_ongoing_loans_table'] .= '<div class="col-md-12"><h2>';
           $data['body_ongoing_loans_table'] .= $this->lang->line('bank')['ongoing_loans'];
           $data['body_ongoing_loans_table'] .= '</h2>
                <table class="table table-responsive myTableLeaderboard center" align="center">
                    <thead>
                        <tr>
                            <th class="col-md-2">'.$this->lang->line('bank')['bank_name'].'</th>
                            <th class="col-md-2">'.$this->lang->line('bank')['signed_on'].'</th>
                            <th class="col-md-2">'.$this->lang->line('bank')['daily_payment'].'</th>
                            <th class="col-md-2">'.$this->lang->line('bank')['left_to_pay'].'</th>
                            <th class="col-md-2">'.$this->lang->line('bank')['last_payment_date'].'</th>
                            <th class="col-md-2">'.$this->lang->line('bank')['payoff'].'</th>
                        </tr>
                    </thead>
                    <tbody>';
                        
            
            
            foreach ($loan_player->result() as $loan_player_array){
                $specific_bank_data = $this->bank_model->get_specific_bank_data($loan_player_array->id_bank);
                $specific_bank_data_array = $specific_bank_data->row();
                $bank_name = $specific_bank_data_array->name_english;
                $today = strtotime('now');                        
                $real_payments_left = $loan_player_array->payments_left +1;// Current UTC timestamp
                $time_loan_paid = strtotime('+'.$real_payments_left.' days', $today);     // Add the duration of the loan to find end time
                $planned_end_date = gmdate('Y-m-d', $time_loan_paid);              // Convert end of loan payment to GMT date for friendly display
                $data['body_ongoing_loans_table'] .= '<tr><td>'.$bank_name.'</td>';
                $data['body_ongoing_loans_table'] .= '<td>'.gmdate('d M Y', strtotime($loan_player_array->signed_up_on)).'</td>';
                $data['body_ongoing_loans_table'] .= '<td>'.number_format($loan_player_array->daily_payment, 0, ',', ' ').' €</td>';
                $data['body_ongoing_loans_table'] .= '<td>'.number_format($loan_player_array->amount_left, 0, ',', ' ').' € / '.number_format($loan_player_array->borrowed_amount, 0, ',', ' ').' €</td>';
                $data['body_ongoing_loans_table'] .= '<td>'.gmdate('d M Y', strtotime($planned_end_date)).'</td>';
                $data['body_ongoing_loans_table'] .= '<td data-id_loan="'.$loan_player_array->id_loan.'" data-left_to_pay="'.$loan_player_array->amount_left.'"><div class="tooltip tooltip-bottom" data-tip="'.$this->lang->line('bank')['payoff_help'].' '.number_format($loan_player_array->amount_left, 0, ',', ' ').' €."><a href="?action=payoff_loan" class="payoff_loan-dialog"><button class="btn btn-success">'.$this->lang->line('bank')['payoff_now'].'</button></a></div></td></tr>';
            }
            
            $data['body_ongoing_loans_table'] .= '  </tbody>
                </table> 
            </div>';
        }
        return $data;
    }
    
   
    public function get_max_daily_payment($currentResortID){
                       
        // PREVENTS USERS FROM TAKING TOO HIGH LOANS
        $today = strtotime('now');
        $yesterday = strtotime('-1 day', $today);
        $sevenDaysAgo = strtotime('-6 days', $yesterday);
        $yesterday_GMT = gmdate('Y-m-d', $yesterday);
        $sevenDaysAgo_GMT = gmdate('Y-m-d', $sevenDaysAgo);
        $today_GMT = gmdate('Y-m-d', $today);

        $revenue_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'revenue', $sevenDaysAgo_GMT, $yesterday_GMT);
        $rev_achievements_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'rev_achievements', $sevenDaysAgo_GMT, $yesterday_GMT);
        $expenses_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'expenses', $sevenDaysAgo_GMT, $yesterday_GMT);
        $cost_loans_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'cost_loans', $sevenDaysAgo_GMT, $yesterday_GMT);
        $cost_taxes_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'cost_taxes', $sevenDaysAgo_GMT, $yesterday_GMT);
        //$rev_tournaments_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'rev_tournaments', $sevenDaysAgo_GMT, $yesterday_GMT);
        $cost_purchases_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'cost_purchases', $sevenDaysAgo_GMT, $yesterday_GMT);
        //$cost_tournaments_last7days = $this->finances_model->get_lastXdays_specific_amount_DB($currentResortID, 'cost_tournaments', $sevenDaysAgo_GMT, $yesterday_GMT);

        $ongoing_all_loans_player = $this->bank_model->get_ongoing_loan_player($currentResortID);
        $all_loans_daily_payment = 0;
        if ($ongoing_all_loans_player->num_rows() > 0) {
            foreach ($ongoing_all_loans_player->result() as $loan_player_array){
                $all_loans_daily_payment = $all_loans_daily_payment + $loan_player_array->daily_payment;        
            }
        }

        $revenue_without_achievements = ( ($revenue_last7days - $rev_achievements_last7days - $expenses_last7days + $cost_purchases_last7days + $cost_loans_last7days) / 7 ) - $all_loans_daily_payment;    // adding loans expenses but then removing ongoing loans
        
        $revenue_without_achievements = $revenue_without_achievements * 1.1;    // Adding 10% safety
        
        return $revenue_without_achievements;
    }
 
    /**
     * signup_loan       Signs up for a loan
     * 
     * @param type $currentResortID   Current resort ID
     * @param type $type            Type of the bank (1=groomer, 2=bus...)
     * @param type $level           Level to buy, usually 1 for first time
     */
    public function signup_loan(){
        $currentUserID = $this->users_model->get_user_id();          
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_bank = trim($this->input->post('id_bank', TRUE));
        $loan_duration = trim($this->input->post('loan_duration', TRUE));
        $to_borrow = trim($this->input->post('to_borrow', TRUE));
        $genepis_available = $this->users_model->get_user_genepis_amount($currentUserID);
        $enough_genepis = false; // Initializing genepis status
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // Get the generic bank info, in the game_banks table
        $generic_bank_info_data = $this->bank_model->get_generic_bank_info($id_bank);
        if ($generic_bank_info_data->num_rows() > 0) {                      // the generic bank exists in the DB (always!)
            $generic_bank_info_data_Array = $generic_bank_info_data->row();
        }
        
        $generic_min_loan = $generic_bank_info_data_Array->min_loan;
        $generic_max_loan = $generic_bank_info_data_Array->max_loan;
        $generic_interest_rate = $generic_bank_info_data_Array->interest_rate;
        $generic_genepis_required = $generic_bank_info_data_Array->genepis_required;
        $name_language = 'name_'.$this->session->userdata('site_lang');     // outputs name_english or name_french for the DB columns
        $bank_name = $generic_bank_info_data_Array->$name_language;
                    
        if ($generic_genepis_required > 0)
            $is_vip_loan = true;
        else
            $is_vip_loan = false;
            
        $standard_loan_player = $this->bank_model->count_ongoing_standard_loan_player($currentResortID);
        $nb_standard_loan_player = $standard_loan_player->num_rows();
        $vip_loan_player = $this->bank_model->count_ongoing_vip_loan_player($currentResortID);
        $nb_vip_loan_player = $vip_loan_player->num_rows();
        
        $total_nb_loans = $nb_standard_loan_player + $nb_vip_loan_player;
        
        if ( ($is_vip_loan == false && $nb_standard_loan_player == 0 && $total_nb_loans < 3) || ($is_vip_loan == true && $total_nb_loans < 3 )  ) {
        // CHECK IF NO LOAN ONGOING
        //if ($count_ongoing_loan_player == 0 || ($generic_genepis_required > 0 && $count_ongoing_loan_player < 3)) {  // No ongoing loan for this player or 1 max for paid loans (VIP)
            if ($genepis_available >= $generic_genepis_required) {
                $enough_genepis = true; 
            }
            else {
                $enough_genepis = false;        
            }

            if ($enough_genepis == true) {
                if ($to_borrow >= $generic_min_loan && $to_borrow <= $generic_max_loan) {   // Borrowing amount is within range for this bank

                    $interest_rate = $generic_interest_rate/365/100;
                    // Calculate the monthly payment based on interest rate, borrowed amount and duration
                    if ($interest_rate == 0) {
                        $daily_payment = floor($to_borrow / $loan_duration);
                    } else {
                        $daily_payment = floor($to_borrow * $interest_rate / (1 - (pow(1/(1 + $interest_rate), $loan_duration))));
                    }
                    
                    $max_daily_payment = $this->get_max_daily_payment($currentResortID);
                    
                    if ($max_daily_payment >= $daily_payment) {
                    
                        $total_to_pay_with_cost = $daily_payment * $loan_duration;
                        $payments_left = $loan_duration;
                        $signed_up_on = gmdate('Y-m-d H:i:s');
                        $new_loan_data = array (
                            'id_resort' => $currentResortID,
                            'id_bank' => $id_bank,
                            'borrowed_amount' => $to_borrow,
                            'payments_left' => $loan_duration,
                            'daily_payment' => $daily_payment,
                            'amount_left' => $total_to_pay_with_cost,
                            'signed_up_on' => $signed_up_on
                        );   
                        $signup_loan = $this->bank_model->signup_loan_DB($new_loan_data);
                        $remove_genepis_cost = $this->users_model->remove_genepis_cost_DB($generic_genepis_required);
                        $cash_player = $this->users_model->get_cash_player();                     // Get how much cash the player has (in euros)
                        // Adds revenue to the Other revenue table
                        $add_revenue_history_query_specific_table = add_revenue_stat_table($currentResortID, $to_borrow, 'rev_loan');
                        $add_cash_to_resort = $this->users_model->sell_item($to_borrow, $cash_player);
                        $today = strtotime('now');                                          // Current UTC timestamp
                        $time_loan_paid = strtotime('+'.$loan_duration.' days', $today);     // Add the duration of the loan to find end time
                        $planned_end_date = gmdate('Y-m-d', $time_loan_paid);              // Convert end of loan payment to GMT date for friendly display

                        $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $this->lang->line('logs')['subscribed_loan_1'].number_format($to_borrow, 0, ',', ' '). ' € '.$this->lang->line('logs')['subscribed_loan_2'].$bank_name.'. '.$this->lang->line('logs')['subscribed_loan_3'].' '.number_format($daily_payment, 0, ',', ' ').' € '.$this->lang->line('logs')['subscribed_loan_4'].' '.$loan_duration.' '.$this->lang->line('home')['days']) );   // Add a log row to the game_player_logs table
                        $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $this->lang->line('logs')['subscribed_loan_1'].number_format($to_borrow, 0, ',', ' '). ' € '.$this->lang->line('logs')['subscribed_loan_2'].$bank_name.'. '.$this->lang->line('logs')['subscribed_loan_3'].' '.number_format($daily_payment, 0, ',', ' ').' € '.$this->lang->line('logs')['subscribed_loan_4'].' '.$loan_duration.' '.$this->lang->line('home')['days']) );   // Add a log row to the game_player_logs table
                        $table_ongoing_loans = $this->ongoing_loans($currentResortID);
                        echo json_encode(array('signed' => true, 'planned_end_date' => $planned_end_date, 'daily_payment' => $daily_payment, 'ongoing_loans_table' => $table_ongoing_loans['body_ongoing_loans_table']));
                    }
                    else {  // The player doesn't earn enough to get the loan
                        echo json_encode(array('signed' => false, 'message' => $this->lang->line('bank')['not_enough_revenue']));
                    }
                }
                else {  // Borrowing amount not in range, warn user
                    echo json_encode(array('signed' => false, 'message' => $this->lang->line('bank')['loan_not_signed_up']));
                }
            }
            else {  // Not enough Genepis! warn user...
                echo json_encode(array('signed' => false, 'message' => $this->lang->line('home')['not_enough_genepis']));
            }
        }
        else {  // already ongoing loans...
            echo json_encode(array('signed' => false, 'message' => $this->lang->line('bank')['ongoing_loans_error']));
        }
        
       return true;
    }
    
    
    
    
    public function payoff_loan(){
        $currentUserID = $this->users_model->get_user_id();          
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $id_loan = trim($this->input->post('id_loan', TRUE));
        $left_to_pay = trim($this->input->post('left_to_pay', TRUE));
        
        // START CHECK IF THE USER IS ALLOWED TO BE HERE
        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed)
          redirect('home_controller');
        // END CHECK IF THE USER IS ALLOWED TO BE HERE
        
        // Get the generic bank info, in the game_banks table
        $loan_data = $this->bank_model->get_loan_info($id_loan, $currentResortID);
        if ($loan_data->num_rows() == 0) {
            echo json_encode(array('payed_off' => false, 'message' => 'Loan not found'));
            return false;
        }
        $loan_data_array = $loan_data->row();
        
        $generic_bank_info_data = $this->bank_model->get_generic_bank_info($loan_data_array->id_bank);
        if ($generic_bank_info_data->num_rows() > 0) {                      // the generic bank exists in the DB (always!)
            $generic_bank_info_data_Array = $generic_bank_info_data->row();
        }
        
        //$borrowed_amount = $loan_data_array->borrowed_amount;
        //$payments_left = $loan_data_array->payments_left;
        //$daily_payment = $loan_data_array->daily_payment;
        $amount_left = $loan_data_array->amount_left;
        
        //$interest_rate = $generic_bank_info_data_Array->interest_rate/365/100;
                
        //$interest_period = $interest_rate * $amount_left;
        
        //$today = strtotime('now');
        //$loan_duration = $loan_data_array->signed_up_on - gmdate('Y-m-d', $today) + $payments_left;
        
        //$total_to_pay_with_cost = $daily_payment * $loan_duration;
                    
        $cash_player = $this->users_model->get_cash_player();                     // Get how much cash the player has (in dollar)
        $money_after_payment = $cash_player - $amount_left;                    // we calculate how much the player will have left after the payment
        
        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $this->lang->load('logs',$player_preferred_lang);
            
        if ($money_after_payment >= 0) {                                         // If enough cash

            //$add_cost_history_query_specific_table = add_cost_stat_table($currentResortID, $amount_left, 'cost_loans'); // Not adding value from table or it will mess up max_loan_amount calculations
            $remove_cash_to_resort = $this->users_model->pay_item($amount_left, $cash_player);
            
            $todays_time = strtotime('now');
            $todays_datetime = gmdate('Y-m-d H:i:s', $todays_time);
        
            $finalize_loan = $this->bank_model->finalize_loan($todays_datetime, $currentResortID, $id_loan);
            
            $log_message = $this->lang->line('logs')['payoff_loan_1'].' '.number_format($amount_left, 0, ',', ' '). ' € '.$this->lang->line('logs')['payoff_loan_2'];

            $call_notification = $this->logs_model->call_notification_DB( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $log_message) );   // Add a log row to the game_player_logs table
            $log_user_action = log_user_action( array('id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $log_message) );   // Add a log row to the game_player_logs table
            
            $table_ongoing_loans = $this->ongoing_loans($currentResortID);
            
            echo json_encode(array('payed_off' => true, 'message' => $log_message, 'ongoing_loans_table' => $table_ongoing_loans['body_ongoing_loans_table']));
        }
        else {
            $log_message = $this->lang->line('bank')['not_enough_money_payoff'].' '.number_format($amount_left, 0, ',', ' '). ' € '.$this->lang->line('bank')['for_this_action'];
            
            $table_ongoing_loans = $this->ongoing_loans($currentResortID);
            echo json_encode(array('payed_off' => false, 'message' => $log_message, 'ongoing_loans_table' => $table_ongoing_loans['body_ongoing_loans_table']));
        }
        
              
        
       return true;
    }


    /**
     * loan_history     Returns the HTML block for past paid-off loans
     */
    public function loan_history($currentResortID) {
        $data['loan_history_display'] = false;
        $data['body_loan_history_table'] = '';

        $history = $this->bank_model->get_loan_history_DB($currentResortID);
        if ($history->num_rows() == 0)
            return $data;

        $name_language = 'name_'.$this->session->userdata('site_lang');
        $data['loan_history_display'] = true;
        $data['body_loan_history_table'] .= '<div class="col-md-12 padding_top_bot_15"><h3>'.$this->lang->line('bank')['loan_history'].'</h3>
            <table class="table table-responsive myTableLeaderboard center" align="center">
                <thead><tr>
                    <th class="col-md-3">'.$this->lang->line('bank')['bank_name'].'</th>
                    <th class="col-md-3">'.$this->lang->line('bank')['signed_on'].'</th>
                    <th class="col-md-3">'.$this->lang->line('bank')['borrowed_amount'].'</th>
                    <th class="col-md-3">'.$this->lang->line('bank')['reimbursed_on'].'</th>
                </tr></thead><tbody>';

        foreach ($history->result() as $row) {
            $bank_name = $row->$name_language;
            $data['body_loan_history_table'] .= '<tr>';
            $data['body_loan_history_table'] .= '<td>'.$bank_name.'</td>';
            $data['body_loan_history_table'] .= '<td>'.gmdate('d M Y', strtotime($row->signed_up_on)).'</td>';
            $data['body_loan_history_table'] .= '<td>'.number_format($row->borrowed_amount, 0, ',', ' ').' €</td>';
            $data['body_loan_history_table'] .= '<td>'.gmdate('d M Y', strtotime($row->reimbursed_date)).'</td>';
            $data['body_loan_history_table'] .= '</tr>';
        }
        $data['body_loan_history_table'] .= '</tbody></table></div>';
        return $data;
    }


    /**
     * investment_block     Builds the investment account display data
     */
    public function investment_block($currentResortID) {
        $data['investment_balance'] = 0;
        $data['investment_balance_display'] = '0';
        $data['investment_annual_rate'] = BANK_INVESTMENT_ANNUAL_RATE;
        $data['investment_min_deposit'] = number_format(BANK_INVESTMENT_MIN_DEPOSIT, 0, ',', ' ');
        $data['investment_min_deposit_raw'] = BANK_INVESTMENT_MIN_DEPOSIT;
        $data['investment_max_balance'] = number_format(BANK_INVESTMENT_MAX_BALANCE, 0, ',', ' ');
        $data['investment_max_balance_raw'] = BANK_INVESTMENT_MAX_BALANCE;

        $inv = $this->bank_model->get_investment_DB($currentResortID);
        if ($inv->num_rows() > 0) {
            $row = $inv->row();
            $data['investment_balance'] = (int)$row->balance;
            $data['investment_balance_display'] = number_format((int)$row->balance, 0, ',', ' ');
        }
        return $data;
    }


    /**
     * deposit_investment   AJAX: deposit an amount into the savings account
     */
    public function deposit_investment() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed) redirect('home_controller');

        $amount = (int)trim($this->input->post('amount', TRUE));

        if ($amount < BANK_INVESTMENT_MIN_DEPOSIT) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('bank')['investment_min_deposit_error']]);
            return true;
        }

        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < $amount) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('bank')['not_enough_money_payoff'].' '.number_format($amount, 0, ',', ' ').' € '.$this->lang->line('bank')['for_this_action']]);
            return true;
        }

        $inv = $this->bank_model->get_investment_DB($currentResortID);
        $current_balance = 0;
        if ($inv->num_rows() > 0)
            $current_balance = (int)$inv->row()->balance;

        $new_balance = $current_balance + $amount;
        if ($new_balance > BANK_INVESTMENT_MAX_BALANCE) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('bank')['investment_max_balance_error']]);
            return true;
        }

        $now = gmdate('Y-m-d H:i:s');
        $this->users_model->pay_item($amount, $cash_player);
        $this->bank_model->upsert_investment_DB($currentResortID, $new_balance, $now);

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $this->lang->load('logs', $player_preferred_lang);
        $log_msg = $this->lang->line('bank')['investment_deposited'].' '.number_format($amount, 0, ',', ' ').' €';
        $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $log_msg]);
        log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $log_msg]);

        echo json_encode([
            'success'         => true,
            'message'         => $log_msg,
            'new_balance'     => $new_balance,
            'new_balance_fmt' => number_format($new_balance, 0, ',', ' '),
        ]);
        return true;
    }


    /**
     * withdraw_investment   AJAX: withdraw all or part of the savings account
     */
    public function withdraw_investment() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_is_allowed = $this->users_model->check_current_user_allowed($currentResortID);
        if (!$user_is_allowed) redirect('home_controller');

        $amount = (int)trim($this->input->post('amount', TRUE));

        $inv = $this->bank_model->get_investment_DB($currentResortID);
        $current_balance = 0;
        if ($inv->num_rows() > 0)
            $current_balance = (int)$inv->row()->balance;

        if ($amount <= 0 || $amount > $current_balance) {
            echo json_encode(['success' => false, 'message' => $this->lang->line('bank')['investment_withdraw_error']]);
            return true;
        }

        $new_balance = $current_balance - $amount;
        $now = gmdate('Y-m-d H:i:s');
        $cash_player = $this->users_model->get_cash_player();
        $this->users_model->sell_item($amount, $cash_player);
        $this->bank_model->upsert_investment_DB($currentResortID, $new_balance, $now);

        $player_preferred_lang = $this->users_model->get_user_preferred_lang($currentUserID);
        $this->lang->load('logs', $player_preferred_lang);
        $log_msg = $this->lang->line('bank')['investment_withdrawn'].' '.number_format($amount, 0, ',', ' ').' €';
        $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $log_msg]);
        log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['loan'], 'data' => $log_msg]);

        echo json_encode([
            'success'         => true,
            'message'         => $log_msg,
            'new_balance'     => $new_balance,
            'new_balance_fmt' => number_format($new_balance, 0, ',', ' '),
        ]);
        return true;
    }
   
    
    
}