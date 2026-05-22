<?php
require_once APPPATH . 'libraries/Cached_result.php';
/**
 * 
 */
class Leaderboard_controller extends CI_Controller{
    
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
        $ci->lang->load('leaderboard',$siteLang);
        $ci->lang->load('logs',$siteLang);
        $this->load->model('leaderboard_model');
        $logged_status = $this->session->userdata('is_logged_in');
        if (isset($logged_status) && $logged_status == true) {
            $this->load->model('users_model');
            $this->load->model('resort_model');
        }
    }
    

    public function index($data = NULL){

        $logged_status = $this->session->userdata('is_logged_in');

        // Public read-only leaderboard for unauthenticated visitors
        if (!isset($logged_status) || $logged_status != true) {
            $data['title'] = '<h2>'.$this->lang->line('leaderboard')['titleMain'].'</h2>';
            $data['introLeaderboard'] = '<div>'.$this->lang->line('leaderboard')['intro'].'</div>';
            $data['main_content'] = 'leaderboard_public';
            $data['currentUserId'] = null;
            $data['resort_built'] = false;
            $this->load->view('templates/default', $data);
            return;
        }

        $currentUserID = $this->users_model->get_user_id();
        $sandbox_mode = $this->users_model->check_sandbox_mode_user($currentUserID);

        $data['title'] = '<h2>'.$this->lang->line('leaderboard')['titleMain'];
        if ($sandbox_mode == 1)
            $data['title'] .= ' '.$this->lang->line('leaderboard')['sandbox_mode_only'];
        $data['title'] .= '</h2>'; 
        $data['introLeaderboard'] = '<div>'.$this->lang->line('leaderboard')['intro'].'</div>';
        
        $currentResortID = $this->users_model->get_resort_id($currentUserID);  // get the resort ID
        $user_activated = $this->users_model->check_account_activated($currentUserID);  // check if the account is activated
        if ($user_activated) {      // If the account is activated, we show the page
            $checkIfResortExists = $this->resort_model->display_resort_info_DB($currentResortID);    // we get the resort linked to the player ID (if there are any)       
            if ($checkIfResortExists->num_rows() > 0) {    
                $data['main_content'] = 'leaderboard';
                $data['currentUserId'] = $currentUserID;
                $data['resort_built'] = true;
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

    
  public function getDataTable(){
        $currentUserID = $this->users_model->get_user_id();
        $sandbox_mode = $this->users_model->check_sandbox_mode_user($currentUserID);
        $cache_key = 'leaderboard_rows_' . (int)$sandbox_mode;
        $rows = $this->cache->get($cache_key);
        if ($rows === false) {
            $rows = $this->leaderboard_model->get_leaderboard_stats_DB($currentUserID, $sandbox_mode)->result_array();
            $this->cache->save($cache_key, $rows, 180);
        }
        echo json_encode($this->_build_leaderboard_response(new Cached_result($rows), $currentUserID));
    }

    /** Public leaderboard endpoint – no login required, returns top 50 resorts by reputation. */
    public function getPublicDataTable(){
        $cached = $this->cache->get('leaderboard_public');
        if ($cached !== false) { echo $cached; return; }
        $data1 = $this->leaderboard_model->get_leaderboard_stats_DB(0, 0);
        $rows = [];
        foreach ($data1->result_array() as $row) {
            $rows[] = [
                'resort_name'    => $row['resort_name'],
                'resort_country' => $row['resort_country'],
                'reputation'     => $row['reputation'],
                'prestige'       => $row['prestige'],
                'lift_count'     => $row['lift_count'] ?? 0,
                'slope_count'    => $row['slope_count'] ?? 0,
                'staff_count'    => $row['staff_count'] ?? 0,
            ];
        }
        $json = json_encode(['Data' => $rows]);
        $this->cache->save('leaderboard_public', $json, 300);
        echo $json;
    }

    public function getDataTableByCountry(){
        $currentUserID = $this->users_model->get_user_id();
        $sandbox_mode = $this->users_model->check_sandbox_mode_user($currentUserID);
        $country = $this->leaderboard_model->get_user_resort_country_DB($currentUserID);
        if (empty($country)) {
            echo json_encode(['data' => [], 'current_id_player' => $currentUserID, 'current_player_ranking' => 0, 'displayStart' => 0, 'country' => '']);
            return;
        }
        $cache_key = 'leaderboard_country_' . md5($country) . '_' . (int)$sandbox_mode;
        $rows = $this->cache->get($cache_key);
        if ($rows === false) {
            $rows = $this->leaderboard_model->get_leaderboard_by_country_DB($currentUserID, $sandbox_mode, $country)->result_array();
            $this->cache->save($cache_key, $rows, 180);
        }
        $values = $this->_build_leaderboard_response(new Cached_result($rows), $currentUserID);
        $values['country'] = $country;
        echo json_encode($values);
    }

    public function getDataTableBySlope(){
        $currentUserID = $this->users_model->get_user_id();
        $sandbox_mode = $this->users_model->check_sandbox_mode_user($currentUserID);
        $cache_key = 'leaderboard_slope_' . (int)$sandbox_mode;
        $rows = $this->cache->get($cache_key);
        if ($rows === false) {
            $rows = $this->leaderboard_model->get_leaderboard_by_slope_DB($currentUserID, $sandbox_mode)->result_array();
            $this->cache->save($cache_key, $rows, 180);
        }
        echo json_encode($this->_build_leaderboard_response(new Cached_result($rows), $currentUserID, true));
    }

    /**
     * Shared logic for processing a raw leaderboard DB result into the JSON
     * response expected by the DataTable JS code.
     *
     * @param  object $data1           CI query result object
     * @param  int    $currentUserID
     * @param  bool   $rank_by_slopes  When true the rank column reflects slope-count order
     * @return array
     */
    private function _build_leaderboard_response($data1, $currentUserID, $rank_by_slopes = false) {
        $data = $data1->result();
        foreach ($data as $item) {
            $old_date = $item->creation_time_resort;
            $now = time();
            $old_date_formatted = strtotime($old_date);
            $diff = $now - $old_date_formatted;
            $diff_days = $diff/3600/24;
            $diff_days_formatted = intval($diff_days);
            $item->creation_time_resort = number_format($diff_days_formatted, 0, ',', ' ');
        }
        $rank = 1;
        $current_player_ranking = 0;
        foreach ($data as $key) {
            $key->ranking = $rank;
            if ($key->id_player == $currentUserID)
                $current_player_ranking = $rank;
            $rank++;
        }
        foreach ($data as $key => $value) {
            if (is_null($value->lift_count))
                $data[$key]->lift_count = '-';
            if (is_null($value->slope_count))
                $data[$key]->slope_count = '-';
            if (is_null($value->staff_count))
                $data[$key]->staff_count = '-';
            if (is_null($value->tournament_count))
                $data[$key]->tournament_count = '-';
        }
        $array = (array) $data;
        $values = [];
        $values['data'] = $array;
        $values['current_id_player'] = $currentUserID;
        $values['current_player_ranking'] = $current_player_ranking;
        if ($current_player_ranking <= 15)
            $displayStart = 1;
        else
            $displayStart = $current_player_ranking - 15;
        $values['displayStart'] = $displayStart;
        return $values;
    }



}