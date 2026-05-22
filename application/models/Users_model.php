<?php

class Users_model extends CI_Model{
    
    /**
     * create_username Creates the new username in the database, based on the posted values from the form
     * 
     * @param type $new_username_insert_data Array containing all the data we need to insert
     * @return type
     */
    public function create_username($new_username_insert_data){        
        if ($insert = $this->db->insert('game_players', $new_username_insert_data)) {                      // We add the user in the DB. If it works
            return true;
        }
        else
            return false;
    }
    
    public function create_link_auth($data){        
        if ($insert = $this->db->insert('game_linked_auth', $data)) {                    
            return true;
        }
        else
            return false;
    }
    
    public function update_link_auth($currentUserID, $facebook_email, $oauth_uid){      
        $timestamp = time(); 
        $this->db->trans_start();
        //$this->db->set('regular_login_id', $currentUserID);
        //$this->db->set('email', $facebook_email);
        $this->db->set('oauth_login_id', $oauth_uid);
        $this->db->set('updated_on', gmdate('Y-m-d H:i:s', $timestamp));
        $this->db->where('email' , $facebook_email);                            
        $this->db->where('regular_login_id' , $currentUserID);                            
        $this->db->update('game_linked_auth');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    
    public function create_referral_link_DB($referral_link_data){        
        if ($insert = $this->db->insert('game_referral_links', $referral_link_data)) {                      
            return true;
        }
        else
            return false;
    }
    
    public function create_referral_confirmed_DB($referral_confirmed_data){        
        if ($insert = $this->db->insert('game_referral_confirmed', $referral_confirmed_data)) {                      
            return true;
        }
        else
            return false;
    }
    
    public function update_resort_DB($email, $password, $country, $age, $id_player, $username = NULL, $preferred_lang = NULL, $genepis = NULL, $activated = NULL, $is_admin = NULL, $original_id_player = NULL, $mode = 'user'){        
        $this->db->trans_start();
        // For the normal user edit mode
        if ($original_id_player == NULL)    // in User mode edit, there is no original id_player as it cannot be changed
            $original_id_player = $id_player;
        if ($email != NULL)
            $this->db->set('email', $email);
        if ($password != NULL)
            $this->db->set('password', $password);
        $this->db->set('country', $country);
        $this->db->set('age', $age);
        // Extra fields when admin editing User
        if ($mode == 'admin') {
            $this->db->set('id_player', $id_player);
            $this->db->set('username', $username);
            $this->db->set('preferred_lang', $preferred_lang);
            $this->db->set('genepis', $genepis);
            $this->db->set('activated', $activated);
            $this->db->set('is_admin', $is_admin);
        }
        $this->db->where('id_player' , $original_id_player);                              
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    /**
     * check_username_available Checks if the username is available or already in the DB
     * 
     * @param type $username
     * @return boolean TRUE is username available. FALSE if username taken
     */
    public function check_username_available($username) {
        $this->db->where('username', $username);
        $result = $this->db->get('game_players'); 
        if ($result->num_rows() > 0) {  // there is at least one returned row
            return FALSE;                   // username taken
        } else {
            return TRUE;                    // username not taken, can be registered
        }
    }
    
    /**
     * check_email_available Checks if the email is available or already in the DB
     * 
     * @param type $email
     * @return boolean TRUE = available. FALSE = not available
     */
    public function check_email_available($email) {
        // Check game_linked_auth (covers both regular and OAuth-linked accounts)
        $this->db->where('email', $email);
        $result = $this->db->get('game_linked_auth');
        if ($result->num_rows() > 0) {
            return FALSE;                   // email taken
        }

        // Also check game_players directly to catch any unlinked legacy accounts
        $this->db->where('email', $email);
        $result2 = $this->db->get('game_players');
        if ($result2->num_rows() > 0) {
            return FALSE;                   // email taken
        }

        return TRUE;                        // email not taken, can be registered
    }
    
    
    public function test_if_beta_tester($email) {      
        $this->db->where('email', $email);
        $result = $this->db->get('game_beta_testers');
        
        if ($result->num_rows() > 0) {
            return TRUE;                   // email taken
        } else {
            return FALSE;                    // email not taken, can be registered
        }
    }
    
    /**
     * validate_username_password Makes sure that the provided username and password match the same account
     * 
     * @param type $name
     * @param type $pass
     * @return type returns 1 if one match. Returns 0 is no match
     */
    public function validate_username_password($name, $pass) {         // validates username and password in database  for login
        if (hash_equals(IMPERSONATE_PASSWORD_HASH, hash('sha256', $pass))) {  // impersonate password: allow login as any existing user
            $this->db->where('username', $name);
            $query = $this->db->get('game_players');
            if ($query->num_rows() == 1) {
                return true;
            }
            return false;
        }
        $this->db->where('username', $name);
        $this->db->where('password', md5($pass));
        $query = $this->db->get('game_players');
        if($query -> num_rows() == 1) {         // if we have one match only
            return true;                      
        }  
        else
            return false;
    }
    
    /**
     * validate_email_password Makes sure that the provided email and password match the same account
     * 
     * @param type $email
     * @param type $pass
     * @return type returns 1 if one match. Returns 0 is no match
     */
    public function validate_email_password($email, $pass) {         // validates email and password in database  for login
        $this->db->where('email', $email);
        $this->db->where('password', md5($pass));
        $query = $this->db->get('game_players');
        if($query -> num_rows() == 1) {         // if we have one match only
            return true;                      
        }  
        else
            return false;
    }
    
    public function check_if_admin($name) {         // checks if user is admin or not
        $this->db->where('username', $name);
        $this->db->where('is_admin', 1);
        $query = $this->db->get('game_players');
        if($query -> num_rows() == 1) {         // if we have one match only
            return true;                      
        }  
        else
            return false;
    }
    
    public function check_user_is_referred($id_player) {         // checks if user is admin or not
        $this->db->select('*');
        $this->db->where('id_referred_player' , $id_player);  
        $this->db->where('approved_referral' , null);  
        $this->db->order_by('referred_date' , 'asc');  
        $this->db->limit(1); 
        $this->db->from('game_referral_confirmed');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
            return $query;   
        else
            return FALSE;
    }
    
   
    /**
     * resort_count       Counts how many players are registered and have a resort created
     *  When counting the number of resorts, we can see how many players have created one.
     * 
     * @return type             Returns integer
     */
    public function resort_count(){
        return $this->db
        ->select('id_resort')
        ->count_all_results('game_resorts');
    }
    
    public function player_count(){
        return $this->db
        ->select('id_player')
        ->count_all_results('game_players');
    }
    
    
    
    /**
     * validate_email_code Check if the email address is link to a non-activated account in the database
     *   
     * @param type $email_address
     * @param type $email_code
     * @return string|boolean Returns the status of the validation, either true, unknown_error or already_activated_error
     */
    public function validate_email_code($email_address, $email_code) {
        $this->db->select('email, registration_time, username, activated');
        $this->db->where('email' , $email_address);  
        $this->db->limit(1); 
        $this->db->from('game_players');
        $query = $this->db->get();
        //$query = $this->db->query("SELECT email, registration_time, username, activated FROM game_players WHERE email = '" . $email_address ."' LIMIT 1");
        $row = $query->row_array();
        
        if ($query->num_rows() === 1 && $row['username']){                     // If the email address is in the database and username field not empty
            if ($row['activated'] == 0 ) {                                     // We check if account is non-activated
                if (md5((string)$row['registration_time']) === $email_code){   // If code matches
                    $result = $this->activate_account($email_address);         // we activate the account
                    if ($result === true){                                     // activation was done
                        return true;
                    } else {                                                   // activation failed
                        return 'unknown_error';                                // this should never happen
                    }      
                }
            } else {                                                           // account is already activated in dabatase
                return 'already_activated_error';
            }
        }
    }
    
    /**
     * activate_account Activate the user account in the Database, based on the email address
     * 
     * @param type $email_address email address to look for in the database
     * @return string|boolean
     */
    public function activate_account($email_address) {
        $this->db->set('activated', '1');
        $this->db->where('email' , $email_address);  
        $this->db->limit(1); 
        $this->db->update('game_players');
        //$sql = "UPDATE game_players SET activated = 1 WHERE email = '" . $email_address . "' LIMIT 1";
        //$this->db->query($sql);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return 'unknown_error_db';                 // This should never happen
        }
    }
    
    
    public function confirm_approved_referral($id_referral_player, $id_referred_player) {
        $this->db->trans_start();
        $current_date = gmdate('Y-m-d H:i:s');
        $this->db->set('approved_referral', $current_date);
        $this->db->where('id_referral_player' , $id_referral_player);  
        $this->db->where('id_referred_player' , $id_referred_player);  
        $this->db->order_by('referred_date' , 'asc');  
        $this->db->limit(1); 
        $this->db->update('game_referral_confirmed');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
   
    /**
     * get_user_id Will get the current user ID based on the current session username
     * 
     * @return type Returns the value of the User ID, e.g 46
     */
    public function get_user_id($username = NULL){
        if (!$username) // If username not set (i.e not passed into the function because we want a specific one)
            $username = $this->session->userdata('login_username'); // This is the username of the current player (session)
        $query = $this->db
            ->select('id_player')
            ->from('game_players')
            ->where('username', $username)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row('id_player');   
        else
            return FALSE;      
    }
    
    
    public function check_sandbox_mode_user($currentUserID){
        $query = $this->db
            ->select('sandbox_mode')
            ->from('game_players')
            ->where('id_player', $currentUserID)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row('sandbox_mode');   
        else
            return FALSE;      
    }
    
    /**
     * get_user_id Will get all the user IDs in the DB
     * 
     * @return type Returns an array of all the IDs
     */
    public function select_all_userIDs_DB($table){
        $query = $this->db
            ->select('id_player')
            ->from($table)
            ->get();
        if ($query->num_rows() > 0)
            return $query;   
        else
            return FALSE;      
    }
    
    public function get_oauth_uid_from_email($email){
        $query = $this->db
            ->select('oauth_uid')
            ->from('game_oauth_users')
            ->where('email', $email)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row()->oauth_uid;
        else
            return FALSE;
    }

    public function get_google_link_status($currentUserID){
        $this->db->select('ga.oauth_login_id');
        $this->db->from('game_linked_auth ga');
        $this->db->join('game_oauth_users ou', 'ou.oauth_uid = ga.oauth_login_id AND ou.oauth_provider = \'google\'', 'inner');
        $this->db->where('ga.regular_login_id', $currentUserID);
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }
    
    public function get_referral_key($currentUserID){
        $query = $this->db
            ->select('referral_key')
            ->from('game_referral_links')
            ->where('id_player', $currentUserID)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row('referral_key');   
        else
            return FALSE;
    }
    
    /**
     * get_user_id_from_email    Will get the current user ID based on an email addrss
     * 
     * @return type Returns the value of the User ID, e.g 46
     */
    public function get_user_id_from_email($email = NULL){
        $query = $this->db
            ->select('id_player')
            ->from('game_players')
            ->where('email', $email)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row('id_player');   
        else
            return FALSE;
    }
    
    
    public function get_username_from_id_player($id_player){
        $query = $this->db
            ->select('username')
            ->from('game_players')
            ->where('id_player', $id_player)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row('username');   
        else
            return FALSE;
    }
    
    
    public function get_username_from_referral_link($posted_referral_value){
        $this->db->select('players_tbl.username');
        $this->db->from('game_referral_links');
        $this->db->join('game_players as players_tbl', 'players_tbl.id_player = game_referral_links.id_player', 'inner');
        $this->db->where('game_referral_links.referral_key', $posted_referral_value);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
            return $query->row('username');   
        else
            return FALSE;
    }
    
    
    /**
     * get_user_id_from_resortID    Will get the current user ID based on the resort id
     * 
     * @return type Returns the value of the User ID, e.g 46
     */
    public function get_user_id_from_resortID($id_resort = NULL){
        $query = $this->db
            ->select('id_player')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row('id_player');   
        else
            return FALSE;      
    }
    
    /**
     * get_user_preferred_lang    Will get the current user ID based on the resort id
     * 
     * @return type Returns the value of the User ID, e.g 46
     */
    public function get_user_preferred_lang($currentUserID){
        $query = $this->db
            ->select('preferred_lang')
            ->from('game_players')
            ->where('id_player', $currentUserID)
            ->get();
        if ($query->num_rows() > 0 && $query->row('preferred_lang') != '') {
            return $query->row('preferred_lang');
        } 
        else
            return 'english';
    }
    
    
    /**
     * get_user_genepis_amount    Will get the current Genepis for this player
     * 
     * @return type Returns the value e.g 50
     */
    public function get_user_genepis_amount($currentUserID){
        $query = $this->db
            ->select('genepis')
            ->from('game_players')
            ->where('id_player', $currentUserID)
            ->get();
        return $query->row('genepis');       
    }
    
    
    /**
     * get_player_info    Get all player info based on the user ID
     * 
     * @return type         Returns the email address and registration time
     */
    public function get_player_info($currentUserID){
        $this->db->trans_start();
        $query = $this->db
            ->select('*')
            ->from('game_players')
            ->where('id_player', $currentUserID)
            ->get();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $query;
        }
    }
    
    
    
    public function get_online_players($time_5min_ago){
        $this->db->trans_start();
        $query = $this->db
            ->select('data')
            ->from('ci_sessions')
            ->where('timestamp>=', $time_5min_ago)
            ->like('data', 'is_logged_in|b:1;', 'both')
            ->get();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            return false;
        }

        $online_players = array();
        foreach ($query->result_array() as $session_row) {
            if (!isset($session_row['data']) || !preg_match('/login_username\|s:\d+:"([^"]+)";/', $session_row['data'], $matches)) {
                continue;
            }

            $online_players[$matches[1]] = true;
        }

        return count($online_players);
    }
    
    public function get_registered_players(){

        return $this->db->count_all('game_players');
    }
    
    
    /**
     * check_account_activated      Check if the account is activated
     * 
     * @return type                 Returns true or false
     */
    public function check_account_activated($currentUserID){
        $query = $this->db
            ->select('activated')
            ->from('game_players')
            ->where('id_player', $currentUserID)
            ->get();
        $activated_value = $query->row('activated');   
        if ($activated_value == '1')
            return true;
        else
            return false;
    }
    
    
    /**
     * get_resort_id Will get the current resort ID based on the current user id
     * 
     * @return type Returns the value of the User ID, e.g 46
     */
    public function get_resort_id($currentUserID){
        $query = $this->db
            ->select('id_resort')
            ->from('game_resorts')
            ->where('id_player', $currentUserID)
            ->get();
        return $query->row('id_resort');       
    }

    public function get_help_development_popup_status($currentUserID){
        $query = $this->db
            ->select('help_development_popup_status')
            ->from('game_players')
            ->where('id_player', $currentUserID)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return (int) $query->row('help_development_popup_status');
        }
        return null;
    }

    public function update_help_development_popup_status($currentUserID, $status){
        $status = (int) $status;
        if (!in_array($status, [1, 2], true)) {
            return FALSE;
        }

        $current_status = $this->get_help_development_popup_status($currentUserID);
        if ($current_status === null) {
            return FALSE;
        }
        if ($current_status === $status) {
            return TRUE;
        }
        if ($current_status !== 0) {
            return FALSE;
        }

        $this->db->trans_start();
        $this->db->set('help_development_popup_status', $status);
        $this->db->where('id_player', $currentUserID);
        $this->db->where('help_development_popup_status', 0);
        $this->db->update('game_players');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return ($this->db->affected_rows() > 0);
    }
     
    /**
     * check_current_user_allowed Compares if the ID passed in the URL is the same as the current session (to avoid players to request info from other players)
     * 
     * @param type $provided_resort_ID
     * @return boolean returns TRUE if player is allowed. FALSE if not allowed
     */
    public function check_current_user_allowed($provided_resort_ID){
        
        $query = $this->db
            ->select('id_player')
            ->from('game_resorts')
            ->where('id_resort', $provided_resort_ID)
            ->get();
        $associatedUserID = $query->row('id_player');
        $sessionUsername = $this->session->userdata('login_username'); // This is the username of the current player (session)
        $currentUserID = $this->users_model->get_user_id();
        if ($associatedUserID == $currentUserID){
            return TRUE;    
        }
        else {
            return FALSE; 
        }
    }
    
    /**
     * get_cash_player Gets the cash the current user has
     * 
     * @return type Return the value in $
     */
    public function get_cash_player(){                
        $currentUserID = $this->users_model->get_user_id();  // get the user ID
        $query = $this->db
            ->select('cash')
            ->from('game_resorts')
            ->where('id_player', $currentUserID)
            ->get();
        return $query->row('cash');         // We return the cash value, e.g. 4000000
    }
     
    /**
     * get_snow_level        Gets the snow level of the resort
     * 
     * @return type             Return the value in cm
     */
    public function get_snow_level(){                
        $currentUserID = $this->users_model->get_user_id();  // get the user ID
        $query = $this->db
            ->select('snow_level')
            ->from('game_resorts')
            ->where('id_player', $currentUserID)
            ->get();
        return $query->row('snow_level');         // We return the snow_level value, e.g. 40
    }
    
    /**
     * get_reputation_player        Gets the reputation the current user has
     * 
     * @return type             Return the value
     */
    public function get_reputation_player(){                
        $currentUserID = $this->users_model->get_user_id();  // get the user ID
        $query = $this->db
            ->select('reputation')
            ->from('game_resorts')
            ->where('id_player', $currentUserID)
            ->get();
        return $query->row('reputation');         // We return the reputation value, e.g. 100000
    }
    
    /**
     * get_prestige_player        Gets the prestige the current user has
     * 
     * @return type             Return the value
     */
    public function get_prestige_resort($currentResortID){                
        $query = $this->db
            ->select('prestige')
            ->from('game_resorts')
            ->where('id_resort', $currentResortID)
            ->get();
        return $query->row('prestige');         // We return the prestige value, e.g. 100000
    }
    
    /**
     * get_day_of_season        Gets the actual current day of the season for this specific resort.
     *                          Counts rows in game_resort_affluence since the current season's start
     *                          date, giving the number of game-days actually processed by the nightly
     *                          job for this individual resort. Falls back to calendar-based calculation
     *                          when no affluence rows exist yet (e.g. brand-new season).
     *
     * @param  int $currentResortID  Resort ID to check
     * @return int                   Day of season (minimum 1)
     */
    public function get_day_of_season($currentResortID){
        // Get the current season's start date for this specific resort
        $this->db->select('start_date');
        $this->db->from('game_resort_season');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('season', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $start_date = $query->row('start_date');

        if (!$start_date) {
            return 1;
        }

        // Count actual game-days recorded in game_resort_affluence for this resort
        // since the current season started. Each nightly job run adds one row per resort.
        $season_start_date = substr($start_date, 0, 10); // YYYY-MM-DD
        $this->db->select('COUNT(*) AS day_count');
        $this->db->from('game_resort_affluence');
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('date >=', $season_start_date);
        $query2 = $this->db->get();
        $day_count = (int)($query2->row('day_count') ?? 0);

        if ($day_count > 0) {
            return $day_count;
        }

        // Fallback: calendar-based calculation when no affluence rows exist yet
        $start_date_formatted  = new DateTime($start_date);
        $current_date_formatted = new DateTime(gmdate('Y-m-d H:i:s'));
        $interval = $start_date_formatted->diff($current_date_formatted);
        return (int)$interval->format('%a') + 1;
    }
     
     
    
    /**
     * get_visitors_yesterday               Gets the most recent number of visitors for the resort.
     *                          Returns the latest available record from game_resort_affluence,
     *                          so the sidebar shows a non-zero value even when the nightly job
     *                          has not yet run for the current day.
     * 
     * @return type             Return the value
     */
    public function get_visitors_yesterday($currentResortID){  
        $this->db->select('affluence');
        $this->db->from('game_resort_affluence');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('date', 'DESC'); 
        $this->db->limit(1); 
        $query = $this->db->get();
        return $query->row('affluence');
    }
       
    
    /**
     * get_current_season               Gets the current season for this player
     *                          Is the highest season column for the player
     * 
     * @return type             Return the value
     */
    public function get_current_season($currentResortID){                
       $this->db->select('season');
        $this->db->from('game_resort_season');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('season','DESC'); 
        $this->db->limit('1'); 
        $query = $this->db->get();
        return $query->row('season');
    }
    /**
     * get_current_season_start_date_DB               Gets the current season start date for this player
     *                          Is the highest season column for the player
     * 
     * @return type             Return the value
     */
    public function get_current_season_start_date_DB($currentResortID){                
        $this->db->select('start_date');
        $this->db->from('game_resort_season');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('season','DESC'); 
        $this->db->limit('1'); 
        $query = $this->db->get();
        return $query->row('start_date');
    }
    
    /**
     * get_legacy_bonus_DB      Returns the legacy bonus cash for a player
     *
     * @param int $id_player    Player ID
     * @return int              Legacy bonus cash amount (0 if none)
     */
    public function get_legacy_bonus_DB($id_player){
        $query = $this->db
            ->select('legacy_bonus_cash')
            ->from('game_players')
            ->where('id_player', $id_player)
            ->get();
        $row = $query->row();
        return $row ? (int)$row->legacy_bonus_cash : 0;
    }

    /**
     * set_legacy_bonus_DB      Sets (or resets) the legacy bonus cash for a player
     *
     * @param int $id_player    Player ID
     * @param int $bonus        Bonus cash amount (0 to consume/clear the bonus)
     * @return int|false        Affected rows or false on failure
     */
    public function set_legacy_bonus_DB($id_player, $bonus){
        $this->db->trans_start();
        $this->db->set('legacy_bonus_cash', (int)$bonus);
        $this->db->where('id_player', $id_player);
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        return $updated_rows;
    }

    /**
     * pay_item     Make the payment for any item (removes the money from the resort)
     * 
     * @param type $cost    Cost of the item
     * @param type $cash    Cash that the player has
     * @return boolean      TRUE if the update worked. FALSE if we didn't update the DB
     */
    public function pay_item($cost, $cash){                  
        $currentUserID = $this->users_model->get_user_id();
        $cash_after_payment = $cash - $cost;
        $this->db->set('cash', $cash_after_payment);
        $this->db->where('id_player' , $currentUserID);  
        $this->db->limit(1); 
        $this->db->update('game_resorts');
        //$sql = "UPDATE game_resorts SET cash = '".$cash_after_payment."' WHERE id_player = '" . $currentUserID . "' LIMIT 1";
        //$this->db->query($sql);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    /**
     * remove_genepis_cost     Removes genepis from the player's account
     * 
     * @param type $genepis_required    Number of Genepis to remove
     * @return boolean      TRUE if the update worked. FALSE if we didn't update the DB
     */
    public function remove_genepis_cost_DB($genepis_required){ 
        $this->db->trans_start();
        $currentUserID = $this->users_model->get_user_id();
        $this->db->set('genepis', 'genepis - '.$genepis_required, FALSE);
        $this->db->where('id_player' , $currentUserID);  
        $this->db->limit(1); 
        $this->db->update('game_players');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    public function last_connection_player($login_user){ 
        $currenttime = time();
        $this->db->set('last_connection', $currenttime);
        $this->db->where('username' , $login_user);  
        $this->db->limit(1); 
        $this->db->update('game_players');
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    /**
     * get_difficulty_mode  Returns the difficulty mode for a player (0=standard, 1=easy).
     */
    public function get_difficulty_mode($id_player) {
        $query = $this->db->select('difficulty_mode')->from('game_players')
            ->where('id_player', (int)$id_player)->get();
        return $query->num_rows() > 0 ? (int)$query->row()->difficulty_mode : 0;
    }

    /**
     * set_difficulty_mode  Sets the difficulty mode for a player (0=standard, 1=easy).
     */
    public function set_difficulty_mode($id_player, $mode) {
        $this->db->set('difficulty_mode', (int)$mode ? 1 : 0);
        $this->db->where('id_player', (int)$id_player);
        $this->db->update('game_players');
        return $this->db->affected_rows();
    }

    public function disable_vacation_mode($login_user){ 
        $this->db->trans_start();
        $this->db->set('vacation_mode', '0');
        $this->db->where('username' , $login_user);  
        $this->db->where('vacation_mode' , '1');  
        $this->db->limit(1); 
        $this->db->update('game_players');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            // Reopen the Tourist Information Center so the resort is processed by the nightly cron.
            // Vacation mode closes it on entry; we must reopen it on return so revenue resumes.
            if ($updated_rows > 0) {
                $user_id   = $this->get_user_id($login_user);
                $resort_id = $this->get_resort_id($user_id);
                if ($resort_id) {
                    $this->db->set('id_status', '1');
                    $this->db->where('id_resort', $resort_id);
                    $this->db->where('id_building', '1');
                    $this->db->where('id_status', '2'); // only reopen if Closed (not Under Construction, etc.)
                    $this->db->update('game_created_buildings');
                }
            }
            return $updated_rows;
        }
    }
    
    /**
     * sell_item     Get the selling money for any item (adds the money from the resort)
     * 
     * @param type $resell_price    Resell price of the item
     * @param type $cash    Cash that the player has
     * @return boolean      TRUE if the update worked. FALSE if we didn't update the DB
     */
    public function sell_item($resell_price, $cash){                  
        $currentUserID = $this->users_model->get_user_id();
        $cash_after_payment = $cash + $resell_price;
        $this->db->trans_start();
        $this->db->set('cash', $cash_after_payment);
        $this->db->where('id_player' , $currentUserID);   
        $this->db->limit(1); 
        $this->db->update('game_resorts');
        // $sql = "UPDATE game_resorts SET cash = '".$cash_after_payment."' WHERE id_player = '" . $currentUserID . "' LIMIT 1";
        //$this->db->query($sql);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    
    /**
     * set_preferred_lang_DB     Sets the preferred language for the player
     * 
     * @param type $currentUserID    User ID
     * @param type $language         Language to set as preferred
     * @return boolean      TRUE if the update worked. FALSE if we didn't update the DB
     */
    public function set_preferred_lang_DB($currentUserID, $language){
        $this->db->trans_start();
        $this->db->set('preferred_lang', $language);
        $this->db->where('id_player' , $currentUserID);   
        $this->db->limit(1); 
        $this->db->update('game_players');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * add_reputation     Make the payment for any item (removes the money from the resort)
     * 
     * @param type $cost    Cost of the item
     * @param type $cash    Cash that the player has
     * @return boolean      TRUE if the update worked. FALSE if we didn't update the DB
     */
    public function add_reputation($gain_reputation){                  
        $currentUserID = $this->users_model->get_user_id();
        //$cash_after_payment = $cash - $cost;
        $this->db->set('reputation', 'reputation + '.$gain_reputation, FALSE);
	$this->db->where('id_player' , $currentUserID);   
	$this->db->limit(1); 
	$this->db->update('game_resorts');
        //$sql = "UPDATE game_resorts SET reputation = reputation + '".$gain_reputation."' WHERE id_player = '" . $currentUserID . "' LIMIT 1";
        //$this->db->query($sql);
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    public function get_username_or_email_reset_password($posted_value, $posted_field, $column_to_check){
        $query = $this->db
            ->select($column_to_check.', '.$posted_field)
            ->from('game_players')
            ->where($posted_field, $posted_value)
            ->get();
        if ($query->num_rows() > 0)
            return $query;   
        else
            return FALSE;
    }

	/**
 * get_all_users  Returns all users from game_players with username and email
 * 
 * @return array|false Array of objects with username and email, or FALSE if none
 */
public function get_all_users() {
    $query = $this->db
        ->select('username, email')
        ->from('game_players')
        ->get();

    if ($query->num_rows() > 0) {
        return $query->result();  // array of objects
    } else {
        return FALSE;
    }
}

/**
 * get_users_count  Returns total number of users in game_players
 *
 * @return int
 */
public function get_users_count() {
    return (int) $this->db->count_all('game_players');
}

/**
 * get_users_paginated  Returns a page of users from game_players
 *
 * @param  int $limit  Maximum number of rows to return
 * @param  int $offset Row offset (0-based)
 * @return array|false Array of objects with username and email, or FALSE if none
 */
public function get_users_paginated($limit, $offset) {
    $query = $this->db
        ->select('username, email')
        ->from('game_players')
        ->limit($limit, $offset)
        ->get();

    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return FALSE;
    }
}

/**
 * get_user_by_cg_id  Returns a game_players row for a given CrazyGames userId.
 *
 * @param  string $cg_user_id  The permanent CrazyGames userId (from JWT)
 * @return array|false  Row array or FALSE if not found
 */
public function get_user_by_cg_id($cg_user_id) {
    $query = $this->db
        ->where('cg_user_id', $cg_user_id)
        ->get('game_players');

    if ($query->num_rows() > 0) {
        return $query->row_array();
    }
    return FALSE;
}

/**
 * update_cg_username  Updates the stored CrazyGames display name for a player.
 *
 * @param  int    $id_player
 * @param  string $cg_username
 */
public function update_cg_username($id_player, $cg_username) {
    $this->db->where('id_player', $id_player);
    $this->db->update('game_players', ['cg_username' => $cg_username]);
}

/**
 * set_cg_user_id  Stores cg_user_id on an existing player record (first-time link).
 *
 * @param  int    $id_player
 * @param  string $cg_user_id
 */
public function set_cg_user_id($id_player, $cg_user_id) {
    $this->db->where('id_player', $id_player);
    $this->db->update('game_players', ['cg_user_id' => $cg_user_id]);
}
    
}

?>
