<?php

class Admin_maintenance_controller extends CI_Controller{
    
    public function __construct() {
        parent::__construct(); 
        // Authentication for admins
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)), 2);
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        } else if ($_SERVER['PHP_AUTH_USER'] == ADMIN_USERNAME && $_SERVER['PHP_AUTH_PW'] == ADMIN_PASSWORD){
            // nothing to display
        }
        else {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You need to login to access this area';
            exit;
        }
        // constructor
        $ci =& get_instance();
        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }
        $ci->lang->load('home',$siteLang);
        $ci->lang->load('admin_pages',$siteLang);
        $ci->lang->load('login_form',$siteLang);
        $ci->lang->load('navbar',$siteLang);                                 
        $this->load->model('admin/Admin_maintenance_model');
        $this->load->model('users_model');
        $logged_status = $this->session->userdata('is_logged_in');
        $is_admin = $this->users_model->check_if_admin($this->session->userdata('login_username'));
        if (!isset($logged_status) || $logged_status != true || $is_admin != true)           // Only for logged in users and admin
            redirect('home_controller');    // If not logged in, or not admin redirect to homepage
        $this->Log_filename = gmdate('Y-m-d H-i-s', time())."";
    }
    
    public function index(){
        
        $data['reset_autoincrement_button'] = '<a href="'.base_url('admin/Admin_maintenance_controller/reset_autoincrement').'"><button class="btn btn-success">'.$this->lang->line('adminMaintenance')['reset_auto_inc'].'</button></a>';
        $data['db_backup_button'] = '<a href="'.base_url('crons/NightlyDBBackup_controller/index/Manual').'"><button class="btn btn-success">'.$this->lang->line('adminMaintenance')['manual_db_backup'].'</button></a>';
        $data['reset_game_button'] = '<a href="'.base_url('admin/Admin_maintenance_controller/reset_game').'"><button class="btn btn-success">'.$this->lang->line('adminMaintenance')['reset_game'].'</button></a>';
        $data['run_migration_button'] = '<a href="'.base_url('admin/Admin_maintenance_controller/run_migration').'"><button class="btn btn-warning">Run DB Migrations</button></a>';
        
        $table_list = array ('captcha', 'ci_sessions', 'game_access_sector', 'game_admin_stats', 'game_affluence_bonus', 'game_created_buildings', 'game_created_lifts', 'game_created_slopes', 'game_delete_account_codes', 'game_extended_forecast', 'game_hired_staff', 'game_invite_sent', 'game_linked_auth', 'game_oauth_users', 'game_players', 'game_player_logs', 'game_purchased_equipments', 'game_referral_confirmed', 'game_referral_links', 'game_reset_account_codes', 'game_reset_password_codes', 'game_resorts', 'game_resort_affluence', 'game_resort_cash', 'game_resort_cost_loans', 'game_resort_cost_taxes', 'game_resort_cost_marketing', 'game_resort_cost_purchases', 'game_resort_cost_tournaments', 'game_resort_cost_salaries', 'game_resort_cost_upkeep', 'game_resort_expenses', 'game_resort_injuries', 'game_resort_revenue', 'game_resort_rev_achievements', 'game_resort_rev_hotel', 'game_resort_rev_instructor', 'game_resort_rev_leisure', 'game_resort_rev_loan', 'game_resort_rev_marketing', 'game_resort_rev_medical', 'game_resort_rev_off_season', 'game_resort_rev_other', 'game_resort_rev_parking', 'game_resort_rev_rental', 'game_resort_rev_restaurant', 'game_resort_rev_skibus', 'game_resort_rev_skipass', 'game_resort_season', 'game_resort_snow_level', 'game_signed_loans', 'game_started_campaigns', 'game_weather_forecast', 'user_achievements');
        $table_list = array ('captcha', 'ci_sessions', 'game_access_sector', 'game_admin_stats', 'game_affluence_bonus', 'game_created_buildings', 'game_created_lifts', 'game_created_slopes', 'game_delete_account_codes', 'game_extended_forecast', 'game_hired_staff', 'game_invite_sent', 'game_linked_auth', 'game_oauth_users', 'game_players', 'game_player_logs', 'game_purchased_equipments', 'game_referral_confirmed', 'game_referral_links', 'game_reset_account_codes', 'game_reset_password_codes', 'game_resorts', 'game_resort_affluence', 'game_resort_cash', 'game_resort_cost_loans', 'game_resort_cost_taxes', 'game_resort_cost_marketing', 'game_resort_cost_purchases', 'game_resort_cost_tournaments', 'game_resort_cost_salaries', 'game_resort_cost_upkeep', 'game_resort_expenses', 'game_resort_injuries', 'game_resort_revenue', 'game_resort_rev_achievements', 'game_resort_rev_facility', 'game_resort_rev_hotel', 'game_resort_rev_instructor', 'game_resort_rev_leisure', 'game_resort_rev_loan', 'game_resort_rev_luxury', 'game_resort_rev_marketing', 'game_resort_rev_medical', 'game_resort_rev_other', 'game_resort_rev_parking', 'game_resort_rev_rental', 'game_resort_rev_restaurant', 'game_resort_rev_skibus', 'game_resort_rev_skipass', 'game_resort_season', 'game_resort_snow_level', 'game_signed_loans', 'game_started_campaigns', 'game_weather_forecast', 'user_achievements');
        $data['size_array_table'] = sizeof($table_list);
            
        foreach ($table_list as $table) {
            $count_rows = $this->Admin_maintenance_model->count_rows_table($table);
            if ($count_rows > 1)
                $class = 'class="red_text"';
            else if ($count_rows == 1)
                $class = 'class="orange_text"';
            if ($count_rows == 0)
                $class = 'class="green_text"';
            $data['empty_table_button'][$table][0] = $table;
            $data['empty_table_button'][$table][1] = '(<span '.$class.' id="rows_'.$table.'">'.$count_rows.'</span> rows)';
            $data['empty_table_button'][$table][2] = '<button class="btn btn-success empty_button" id="'.$table.'">'.$this->lang->line('adminMaintenance')['empty'].'</button><span id="message_'.$table.'"></span>';
            
        }
        
        $result = '';
        
     /*   FOR FIXING ACHIEVEMENTS
      * 
      * $id_equipment_purchased = '6';
        $level = '3';
        $id_achievement = '52';
        $selected_users= $this->Admin_maintenance_model->selected_users_to_update($id_equipment_purchased, $level);
        
      
      * if ($selected_users->num_rows() > 0) {
            foreach ($selected_users->result() as $selected_users_data) {
                $data_achievement = array ( 
                    'id_player' => $selected_users_data->id_player,
                    'id_achievement' => $id_achievement,       
                    'progress' => '100',       
                    'unlocked_datetime' => '2019-04-22 11:00:00',       
                    'claimed' => '0',       
                    'user_read' => '0'     
                );
                
                $select_existing_achievements= $this->Admin_maintenance_model->select_existing_achievements($selected_users_data->id_player, $id_achievement);
                if ($select_existing_achievements->num_rows() == 0) {   // Achievement not in table. procceed
                    $insert_achievement_query= $this->Admin_maintenance_model->insert_achievement($data_achievement);
                    if ($insert_achievement_query === TRUE) {
                        $result .= 'Achievement '.$id_achievement.' inserted for player '.$selected_users_data->id_player.'<br>';
                    }
                    else {
                        $result .=  'Failed to insert achievement '.$id_achievement.' for player '.$selected_users_data->id_player.'<br>';
                    }
                }
                else {
                    $result .=  'Achievement '.$id_achievement.' already in table for player '.$selected_users_data->id_player.'<br>';
                } 
            }
        }
        else {
            $result .=  'No user to update for achievement '.$id_achievement.'<br>';
        }   
      * 
      * FOR FIXING ACHIEVEMENTS
      */
        
        
        /* FOR GIVING GIFT TO NEW SEASON
        $give_value = SEASON_BONUS;
        $selected_users= $this->Admin_maintenance_model->selected_users_to_update2();
 
        
        if ($selected_users->num_rows() > 0) {
            echo '>>>>>>>>>>>>rows: '.$selected_users->num_rows();
            foreach ($selected_users->result() as $selected_users_data) {
                $give_money = $this->Admin_maintenance_model->give_money($selected_users_data->id_resort, $give_value);
                if ($give_money === TRUE) {
                    $result .= $give_value.' given to resort ID '.$selected_users_data->id_resort.' for starting season 2<br>';
                }
                else {
                    $result .=  'Failed to give money to '.$selected_users_data->id_resort.'<br>';
                }
            }
        }
        else {
            $result .=  'No user to update<br>';
        }   
            FOR GIVING GIFT TO NEW SEASON
         * 
         */
          
        $data['result_queries'] = $result;
        $data['main_content'] = 'admin/adminMaintenanceView';
        $this->load->view('templates/default_admin',$data); 
    }
        

    public function run_migration(){
        $this->config->load('migration');
        $this->load->library('migration', [
            'migration_enabled' => TRUE,
            'migration_type'    => $this->config->item('migration_type'),
            'migration_path'    => $this->config->item('migration_path'),
            'migration_table'   => $this->config->item('migration_table'),
        ]);
        if ($this->migration->latest() === FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Migration failed: ' . $this->migration->error_string() . '</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">Migrations ran successfully.</div>');
        }
        redirect('admin/admin_maintenance_controller');
    }

    public function reset_autoincrement(){ 
        $empty_tables_query= $this->Admin_maintenance_model->select_all_tables();
        if ($empty_tables_query->num_rows() > 0) {
            foreach ($empty_tables_query->result() as $empty_tables_query_data) {
                $reset_auto_inc= $this->Admin_maintenance_model->reset_auto_increment($empty_tables_query_data->table_name);
                if ($reset_auto_inc === TRUE) {
                    echo 'Table '.$empty_tables_query_data->table_name.' reset to AI=1 or MAX+1<br>';
                }
                else {
                    echo 'Failed to reset AI on table '.$empty_tables_query_data->table_name.'<br>';
                }
            }
        }
        else {
            echo 'No table to deal with.<br>';
        }
    }
    
    
    public function reset_game(){ 
        
        $array_tables = array ('captcha', 'ci_sessions', 'game_access_sector', 'game_admin_stats', 'game_affluence_bonus', 'game_created_buildings', 'game_created_lifts', 'game_created_slopes', 'game_delete_account_codes', 'game_extended_forecast', 'game_hired_staff', 'game_invite_sent', 'game_linked_auth', 'game_oauth_users', 'game_players', 'game_player_logs', 'game_purchased_equipments', 'game_referral_confirmed', 'game_referral_links', 'game_reset_account_codes', 'game_reset_password_codes', 'game_resorts', 'game_resort_affluence', 'game_resort_cash', 'game_resort_cost_loans', 'game_resort_cost_taxes' , 'game_resort_cost_marketing', 'game_resort_cost_tournaments', 'game_resort_cost_purchases' , 'game_resort_cost_salaries', 'game_resort_cost_upkeep', 'game_resort_expenses', 'game_resort_injuries', 'game_resort_revenue', 'game_resort_rev_achievements', 'game_resort_rev_hotel', 'game_resort_rev_instructor', 'game_resort_rev_leisure', 'game_resort_rev_loan', 'game_resort_rev_marketing', 'game_resort_rev_medical', 'game_resort_rev_off_season', 'game_resort_rev_other', 'game_resort_rev_parking', 'game_resort_rev_rental', 'game_resort_rev_restaurant', 'game_resort_rev_skibus', 'game_resort_rev_skipass', 'game_resort_season', 'game_resort_snow_level', 'game_signed_loans', 'game_started_campaigns', 'game_weather_forecast', 'user_achievements');
        $array_tables = array ('captcha', 'ci_sessions', 'game_access_sector', 'game_admin_stats', 'game_affluence_bonus', 'game_created_buildings', 'game_created_lifts', 'game_created_slopes', 'game_delete_account_codes', 'game_extended_forecast', 'game_hired_staff', 'game_invite_sent', 'game_linked_auth', 'game_oauth_users', 'game_players', 'game_player_logs', 'game_purchased_equipments', 'game_referral_confirmed', 'game_referral_links', 'game_reset_account_codes', 'game_reset_password_codes', 'game_resorts', 'game_resort_affluence', 'game_resort_cash', 'game_resort_cost_loans', 'game_resort_cost_taxes' , 'game_resort_cost_marketing', 'game_resort_cost_tournaments', 'game_resort_cost_purchases' , 'game_resort_cost_salaries', 'game_resort_cost_upkeep', 'game_resort_expenses', 'game_resort_injuries', 'game_resort_revenue', 'game_resort_rev_achievements', 'game_resort_rev_facility', 'game_resort_rev_hotel', 'game_resort_rev_instructor', 'game_resort_rev_leisure', 'game_resort_rev_loan', 'game_resort_rev_luxury', 'game_resort_rev_marketing', 'game_resort_rev_medical', 'game_resort_rev_other', 'game_resort_rev_parking', 'game_resort_rev_rental', 'game_resort_rev_restaurant', 'game_resort_rev_skibus', 'game_resort_rev_skipass', 'game_resort_season', 'game_resort_snow_level', 'game_signed_loans', 'game_started_campaigns', 'game_weather_forecast', 'user_achievements');
        foreach ($array_tables as $table_name) {
            $query = $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
            $empty_table= $this->Admin_maintenance_model->empty_table_DB($table_name);
            if (!$empty_table) {
                $this->logToFile($this->Log_filename, "WARN", "[".$table_name."]", "reset_game", "There was a problem truncating table ".$table_name."\n");
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">At least table '.$table_name.' failed to be emptied, check log file at '.$this->Log_filename.' </div>');    
            }
            else {
                $this->logToFile($this->Log_filename, "INFO", "[".$table_name."]", "reset_game", "Table ".$table_name." was truncated\n");
            }
        }
        redirect('admin/admin_maintenance_controller');
    }
    
    public function empty_table(){ 
        
        $table = trim($this->input->post('table', TRUE));
        $empty_table= $this->Admin_maintenance_model->empty_table_DB($table);

        if ($empty_table) {
            echo json_encode(array('returned' => true, 'message' => $this->lang->line('adminMaintenance')['table'].' '.$table.' '.$this->lang->line('adminMaintenance')['emptied']));
            $this->logToFile($this->Log_filename, "INFO", "[".$table."]", "empty_table", "Table ".$table." was truncated\n");   
        }   
        else {
            echo json_encode(array('returned' => false, 'message' => $this->lang->line('adminMaintenance')['table'].' '.$table.' '.$this->lang->line('adminMaintenance')['not_emptied']));
            $this->logToFile($this->Log_filename, "WARN", "[".$table."]", "empty_table", "There was a problem truncating table ".$table."\n");
        }
    }
    
    function logToFile($log_filename, $level, $thread, $function, $data){ 
        
        $timestamp = gmdate('Y-m-d H:i:s,000', time())." ";
        $data_formatted = $timestamp." ".$level." ".$thread." ".$function." - ".$data;
        write_file(FCPATH . '/application/controllers/logs/Admin_maintenance_controller_'.$log_filename.'.log', $data_formatted, "a+");
    }
    
       
}

?>