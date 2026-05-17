<?php
/**
 * Hire_staff_controller
 *
 * Revamped hire staff system featuring:
 *  - Per-resort rolling candidate pool (game_staff_candidates)
 *  - Named candidates with specialization, trait and contract terms
 *  - Signing bonus displayed at hire time
 *  - Instructor-per-slope cap preserved from the legacy system
 *  - "Refresh pool" action (costs CANDIDATE_REFRESH_COST €)
 *  - AJAX endpoint for candidate DataTable rendering
 *  - Legacy getDataTable() kept for backwards compatibility
 */
class Hire_staff_controller extends CI_Controller {

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $siteLang = $ci->session->userdata('site_lang');
        } else {
            $siteLang = 'english';
            $this->session->set_userdata('site_lang', $siteLang);
        }

        $ci->lang->load('home',       $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',     $siteLang);
        $ci->lang->load('staff',      $siteLang);
        $ci->lang->load('logs',       $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true)
            redirect('home_controller');

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('staff_model');
        $this->load->model('logs_model');
    }

    // -------------------------------------------------------------------------
    // index – main hire page
    // -------------------------------------------------------------------------
    public function index($data = NULL) {
        if ($data === NULL) $data = [];

        $data['title']           = '<h2>'.$this->lang->line('common_staff')['titleMain'].' - '.$this->lang->line('hireStaff')['title'].'</h2>';
        $data['introHireStaff']  = '<div>'.$this->lang->line('hireStaff')['intro'].'</div>';

        $currentUserID  = $this->users_model->get_user_id();
        $data['currentUserID'] = $currentUserID;

        $user_activated = $this->users_model->check_account_activated($currentUserID);
        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $currentResortID      = $this->users_model->get_resort_id($currentUserID);
        $checkIfResortExists  = $this->resort_model->display_resort_info_DB($currentResortID);
        if ($checkIfResortExists->num_rows() == 0) {
            $this->session->set_flashdata('error', 'no_resort');
            redirect('resort_controller');
        }

        // ------------------------------------------------------------------
        // Load staff from game_staff table, grouped by position
        // ------------------------------------------------------------------
        $lang_col = 'name_'.$this->session->userdata('site_lang');
        $staff_positions = $this->staff_model->get_distinct_positions_DB();

        $staff_by_position = [];
        foreach ($staff_positions as $pos) {
            $result = $this->staff_model->get_all_staff_DB($pos);
            foreach ($result as $s) {
                $s->display_name = isset($s->$lang_col) ? $s->$lang_col : $s->name_english;
            }
            $staff_by_position[$pos] = $result;
        }

        $data['staff_positions']   = $staff_positions;
        $data['staff_by_position'] = $staff_by_position;
        $data['main_content']      = 'hireStaff';
        $this->load->view('templates/default', $data);
    }

    // -------------------------------------------------------------------------
    // hire_from_candidate  (primary new hire action)
    // Hires a specific named candidate from the pool.
    // URL: hire_staff_controller/hire_from_candidate/<id_candidate>/<contract_months>
    // -------------------------------------------------------------------------
    public function hire_from_candidate($id_candidate, $contract_months = CONTRACT_SHORT) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        // Validate contract_months
        $allowed_contracts = [CONTRACT_SHORT, CONTRACT_MEDIUM, CONTRACT_LONG];
        $contract_months   = (int)$contract_months;
        if (!in_array($contract_months, $allowed_contracts)) {
            $contract_months = CONTRACT_SHORT;
        }

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        if ($pageWasRefreshed) {
            $this->index(['infoMessage' => '']);
            return;
        }

        // Load candidate
        $candidate = $this->staff_model->get_candidate_DB((int)$id_candidate);
        if (!$candidate || $candidate->is_hired) {
            $this->index(['infoMessage' => 'candidate_unavailable']);
            return;
        }

        // Validate the candidate belongs to this resort (or is global)
        if ($candidate->id_resort !== NULL && (int)$candidate->id_resort !== (int)$currentResortID) {
            $this->index(['infoMessage' => 'staff_not_hired']);
            return;
        }

        // Instructor cap: no more than 3 instructors per slope
        if ($candidate->position === 'skiinstructor') {
            if (!$this->_check_instructor_cap($currentResortID)) {
                $this->index(['infoMessage' => 'too_many_instructors']);
                return;
            }
        }

        // Pay signing bonus (if any) – taken from player cash
        $date_now = date('Y-m-d H:i:s');
        if ($candidate->hire_bonus > 0) {
            $cash_player = $this->users_model->get_cash_player();
            if ($cash_player < $candidate->hire_bonus) {
                $this->index(['infoMessage' => 'not_enough_money']);
                return;
            }
            $this->users_model->pay_item($candidate->hire_bonus, $cash_player);
            $cash_after = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $cash_after);
        }

        // Determine the id_staff to link against (match by position + efficiency in game_staff)
        $staff_row = $this->_resolve_game_staff_id($candidate->position, $candidate->efficiency);
        if (!$staff_row) {
            $this->index(['infoMessage' => 'staff_not_hired']);
            return;
        }
        $id_staff = $staff_row->id_staff;

        // Insert into game_hired_staff
        $hire_data = [
            'id_resort'        => $currentResortID,
            'id_staff'         => $id_staff,
            'id_item_assigned' => NULL,
            'date_hired'       => $date_now,
            'morale'           => MORALE_DEFAULT,
            'on_strike'        => 0,
            'experience_points'=> 0,
            'skill_level'      => 1,
            'contract_months'  => $contract_months,
            'contract_start'   => $date_now,
            'specialization'   => $candidate->specialization,
            'trait'            => $candidate->trait,
        ];

        $hired = $this->staff_model->hire_candidate_db($hire_data);
        if ($hired) {
            // Mark candidate as taken from the pool
            $this->staff_model->mark_candidate_hired_DB((int)$id_candidate);

            $this->session->set_flashdata('update_token', time());

            // Achievements & notifications
            $lang_col  = 'name_'.$this->users_model->get_user_preferred_lang($currentUserID);
            $staff_name = isset($candidate->$lang_col) ? $candidate->$lang_col : $candidate->name_english;

            call_achievements_check(['id_resort' => $currentResortID, 'position' => $candidate->position], 'hire');
            call_achievements_check(['id_resort' => $currentResortID, 'position' => '*'], 'hire');
            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['staff'], 'data' => $staff_name.$this->lang->line('logs')['was_recruited']]);
            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['staff'], 'data' => $staff_name.$this->lang->line('logs')['was_recruited']]);

            $this->index(['infoMessage' => 'staff_hired']);
        } else {
            $this->index(['infoMessage' => 'staff_not_hired']);
        }
    }

    // -------------------------------------------------------------------------
    // refresh_candidates  (AJAX – POST)
    // Expires the current pool for a position and seeds fresh candidates.
    // Costs CANDIDATE_REFRESH_COST € per call.
    // -------------------------------------------------------------------------
    public function refresh_candidates() {
        $position        = trim($this->input->post('position', TRUE));
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        if (empty($position)) {
            echo json_encode(['returned' => false, 'message' => 'invalid_position']);
            return;
        }

        $cash_player = $this->users_model->get_cash_player();
        if ($cash_player < CANDIDATE_REFRESH_COST) {
            echo json_encode(['returned' => false, 'message' => 'not_enough_money']);
            return;
        }

        $this->users_model->pay_item(CANDIDATE_REFRESH_COST, $cash_player);
        $this->session->set_userdata('cash', $this->users_model->get_cash_player());

        $this->staff_model->refresh_candidates_for_resort_db($currentResortID, $position);

        echo json_encode(['returned' => true, 'message' => 'pool_refreshed']);
    }

    // -------------------------------------------------------------------------
    // get_candidates_json  (AJAX – POST)
    // Returns the current candidate pool for a position as JSON for DataTable.
    // -------------------------------------------------------------------------
    public function get_candidates_json() {
        $position        = trim($this->input->post('position', TRUE));
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $lang_col        = 'name_'.$this->session->userdata('site_lang');

        $result = $this->staff_model->get_candidates_DB($currentResortID, $position);
        $rows   = [];
        $count  = 0;
        foreach ($result->result() as $c) {
            if ($count >= CANDIDATE_POOL_SIZE) break;
            $rows[] = [
                'id_candidate'   => $c->id_candidate,
                'display_name'   => isset($c->$lang_col) ? $c->$lang_col : $c->name_english,
                'position'       => $c->position,
                'efficiency'     => (int)$c->efficiency,
                'salary'         => (int)$c->salary,
                'hire_bonus'     => (int)$c->hire_bonus,
                'specialization' => $c->specialization,
                'trait'          => $c->trait,
                'contract_months'=> (int)$c->contract_months,
            ];
            $count++;
        }
        echo json_encode(['Data' => $rows]);
    }

    // -------------------------------------------------------------------------
    // Legacy: hire_staff  (kept for URL backward-compatibility)
    // Delegates to hire_from_candidate using the game_staff generic id.
    // -------------------------------------------------------------------------
    public function hire_staff($id_staff, $salary) {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        if ($pageWasRefreshed) {
            $this->index(['infoMessage' => '']);
            return;
        }

        $staff_position_data  = $this->staff_model->get_generic_staff_info_DB($id_staff);
        $staff_position_array = $staff_position_data->row();
        if (!$staff_position_array) {
            $this->index(['infoMessage' => 'staff_not_hired']);
            return;
        }

        // Instructor cap
        if ($staff_position_array->position === 'skiinstructor') {
            if (!$this->_check_instructor_cap($currentResortID)) {
                $this->index(['infoMessage' => 'too_many_instructors']);
                return;
            }
        }

        $date_hired = date('Y-m-d H:i:s');
        $hire_data  = [
            'id_resort'        => $currentResortID,
            'id_staff'         => $id_staff,
            'id_item_assigned' => NULL,
            'date_hired'       => $date_hired,
            'morale'           => MORALE_DEFAULT,
            'on_strike'        => 0,
            'experience_points'=> 0,
            'skill_level'      => 1,
            'contract_months'  => CONTRACT_SHORT,
            'contract_start'   => $date_hired,
            'specialization'   => NULL,
            'trait'            => NULL,
        ];

        $hired = $this->staff_model->hire_staff_db($hire_data);
        if ($hired) {
            $this->session->set_flashdata('update_token', time());
            $lang_col       = 'name_'.$this->users_model->get_user_preferred_lang($currentUserID);
            $staff_name_notif = $staff_position_array->$lang_col;

            call_achievements_check(['id_resort' => $currentResortID, 'position' => $staff_position_array->position], 'hire');
            call_achievements_check(['id_resort' => $currentResortID, 'position' => '*'], 'hire');
            $this->logs_model->call_notification_DB(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['staff'], 'data' => $staff_name_notif.$this->lang->line('logs')['was_recruited']]);
            log_user_action(['id_player' => $currentUserID, 'type' => $this->lang->line('logs')['staff'], 'data' => $staff_name_notif.$this->lang->line('logs')['was_recruited']]);

            $this->index(['infoMessage' => 'staff_hired']);
        } else {
            $this->index(['infoMessage' => 'staff_not_hired']);
        }
    }

    // -------------------------------------------------------------------------
    // Legacy: getDataTable  (kept for backward-compatibility with old JS)
    // -------------------------------------------------------------------------
    public function getDataTable() {
        $string = trim($this->input->post('string', TRUE));
        $data   = $this->staff_model->get_all_staff_DB($string);
        echo json_encode(['Data' => $data]);
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    /**
     * _check_instructor_cap
     *
     * Returns true if another ski instructor can be hired (cap not reached).
     *
     * @param int $currentResortID
     * @return bool
     */
    private function _check_instructor_cap($currentResortID) {
        $number_of_slopes      = $this->resort_model->count_items_resort($currentResortID, 'game_created_slopes');
        $instructors_query     = $this->staff_model->count_hired_staff_of_type_db($currentResortID, 'skiinstructor');
        $number_of_instructors = (int)$instructors_query->row()->count;
        $instructors_per_slope = ($number_of_slopes > 0) ? $number_of_instructors / $number_of_slopes : PHP_INT_MAX;
        return ($instructors_per_slope <= 3);
    }

    /**
     * _resolve_game_staff_id
     *
     * Finds the closest matching row in game_staff for a given position/efficiency.
     * Used when a candidate is hired to link the hired record to the legacy table.
     *
     * @param string $position
     * @param int    $efficiency
     * @return object|null
     */
    private function _resolve_game_staff_id($position, $efficiency) {
        $position   = $this->db->escape_str($position);
        $efficiency = (int)$efficiency;
        $query = $this->db->query(
            "SELECT id_staff, efficiency FROM game_staff
              WHERE position = '{$position}'
              ORDER BY ABS(efficiency - {$efficiency}) ASC
              LIMIT 1"
        );
        return ($query && $query->num_rows() > 0) ? $query->row() : NULL;
    }
}