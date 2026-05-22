<?php

class Staff_model extends CI_Model{
    
    public function get_all_staff_DB($string){
        $query = $this->db
            ->select('*')
            ->from('game_staff')
            ->where('position', $string)
            ->get();
       return $query->result();
    }

    /**
     * get_distinct_positions_DB     Returns all distinct staff positions present in game_staff
     *
     * @return array    Flat array of position strings (e.g. ['driver','liftmechanic',...])
     */
    public function get_distinct_positions_DB(){
        $query = $this->db
            ->select('position')
            ->distinct()
            ->from('game_staff')
            ->order_by('position', 'ASC')
            ->get();
        return array_column($query->result_array(), 'position');
    }
    
    
    /**
     * hire_staff_db       Add a new hired staff into the database
     * 
     * @param type $data    Array containing the data
     * @return type         Returns the value of the result
     */
    public function hire_staff_db($data){
        $insert = $this->db->insert('game_hired_staff', $data);
        return $insert;
    }
    
    /**
     * get_hired_staff_player_DB     gets the player hired staff
     * 
     * @param type $currentResortID       Current resort ID
     * @return type             Return the info
     */
    public function get_hired_staff_player_DB($currentResortID){
        $query = $this->db
            ->select('*')
            ->from('game_hired_staff')
            ->where('id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    /**
     * get_hired_staff_with_info_DB     gets the player hired staff with generic staff info (optimized JOIN)
     * This method reduces N+1 queries by joining game_hired_staff with game_staff in a single query
     * 
     * @param type $currentResortID       Current resort ID
     * @return type             Return the combined info
     */
    public function get_hired_staff_with_info_DB($currentResortID){
        $query = $this->db
            ->select('game_hired_staff.id_hired_staff, game_hired_staff.id_staff, game_hired_staff.id_resort, 
                      game_hired_staff.date_hired, game_hired_staff.id_item_assigned, game_hired_staff.type_item_assigned,
                      game_hired_staff.morale, game_hired_staff.on_strike,
                      game_hired_staff.experience_points, game_hired_staff.skill_level,
                      game_hired_staff.contract_months, game_hired_staff.contract_start,
                      game_hired_staff.specialization, game_hired_staff.trait,
                      game_staff.position, game_staff.salary, game_staff.efficiency, 
                      game_staff.name_english, game_staff.name_french')
            ->from('game_hired_staff')
            ->join('game_staff', 'game_staff.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    /**
     * get_generic_staff_info_DB     gets the generic staff info
     * 
     * @param type db       ID of the staff to get info from
     * @return type             Return the info
     */
    public function get_generic_staff_info_DB($id_staff){
        $query = $this->db
            ->select('*')
            ->from('game_staff')
            ->where('id_staff', $id_staff)
            ->get();
        return $query;
    }
    
    
    public function get_associated_items_DB($currentResortID, $table, $where){
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('id_resort', $currentResortID);
        if (isset($where) && $where != null)
             $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    public function count_assigned_staff_DB($type, $id_item_assigned, $currentResortID) {
        $this->db->from('game_hired_staff');
        $this->db->where('type_item_assigned', $type);         
        $this->db->where('id_item_assigned', $id_item_assigned);
        $this->db->where('id_resort', $currentResortID);
        return $this->db->count_all_results();
    }
                            
    public function get_accessible_sectors($currentResortID){
            $query = $this->db
            ->select('*')
            ->from('game_resorts')
            ->where('id_resort', $currentResortID)
            ->get();
        return $query;
    }
    
    
    public function fire_staff_db($currentResortID, $id_staff){
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_hired_staff', $id_staff);
        $this->db->limit(1); 
        $this->db->delete('game_hired_staff');
        $afftectedRows = $this->db->affected_rows();
        if ($afftectedRows == 1) {
            return true; 
        }
        else
            return false;
    }
    
    
    public function edit_assigned_item_DB($currentResortID, $id_hired_staff, $data){
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_hired_staff', $id_hired_staff);
        $this->db->update('game_hired_staff', $data); 
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    
    /**
     * count_skipatrol_assigned_DB    Counts how many ski patrol staff are assigned to a specific slope
     *
     * @param type $currentResortID   Current resort ID
     * @param type $id_slope          Slope ID (id_created_slopes) to count patrols for
     * @return int                    Number of ski patrol assigned to the slope
     */
    public function count_skipatrol_assigned_DB($currentResortID, $id_slope) {
        return $this->db
            ->from('game_hired_staff')
            ->join('game_staff', 'game_staff.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_resort', $currentResortID)
            ->where('game_hired_staff.id_item_assigned', $id_slope)
            ->where('game_hired_staff.type_item_assigned', 'slope')
            ->where('game_staff.position', 'skipatrol')
            ->count_all_results();
    }

    public function check_staff_assigned_to_item_DB($currentResortID, $idOfSelectedOption, $type_item_assigned){
        $this->db->where('id_item_assigned', $idOfSelectedOption);
        $this->db->where('type_item_assigned', $type_item_assigned);
        $this->db->where('id_resort', $currentResortID);
        $this->db->from('game_hired_staff');
        $count = $this->db->count_all_results();
               // echo $this->db->last_query();
        return $count;
    }
    
    public function get_id_item_assigned($currentResortID, $id_hired_staff){
        $query = $this->db
            ->select('id_item_assigned')
            ->from('game_hired_staff')
            ->where('id_resort', $currentResortID)
            ->where('id_hired_staff', $id_hired_staff)
            ->get();
        return $query;
    }
    
    
    public function count_staff_db($id_staff){
        $this->db->select('COUNT(*) as count');
        $this->db->from('game_hired_staff');
        $this->db->where('id_staff', $id_staff);
        $query = $this->db->get();
        return $query;    
    }
    
    public function count_hired_staff_of_type_db($id_resort, $position){
        $query = $this->db
            ->select('COUNT(*) as count')
            ->from('game_hired_staff')
            ->join('game_staff as game_staff_tbl', 'game_staff_tbl.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_staff_tbl.position', $position)
            ->where('game_hired_staff.id_resort', $id_resort)
            ->get();
        return $query;
    }

    /**
     * get_mechanics_for_groomers_DB     Gets all mechanics assigned to groomers for a resort
     *
     * @param type $currentResortID       Current resort ID
     * @return type             Returns hired mechanic rows with id_item_assigned and name/efficiency
     */
    public function get_mechanics_for_groomers_DB($currentResortID){
        $query = $this->db
            ->select('game_hired_staff.id_hired_staff, game_hired_staff.id_item_assigned,
                      game_staff.name_english, game_staff.name_french, game_staff.efficiency')
            ->from('game_hired_staff')
            ->join('game_staff', 'game_staff.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_resort', $currentResortID)
            ->where('game_hired_staff.type_item_assigned', 'groomer')
            ->get();
        return $query;
    }

    /**
     * get_drivers_for_skibuses_DB     Gets all drivers assigned to ski buses for a resort
     *
     * @param type $currentResortID       Current resort ID
     * @return type             Returns hired driver rows with id_item_assigned and name/efficiency
     */
    public function get_drivers_for_skibuses_DB($currentResortID){
        $query = $this->db
            ->select('game_hired_staff.id_hired_staff, game_hired_staff.id_item_assigned,
                      game_staff.name_english, game_staff.name_french, game_staff.efficiency')
            ->from('game_hired_staff')
            ->join('game_staff', 'game_staff.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_resort', $currentResortID)
            ->where('game_hired_staff.type_item_assigned', 'skibus')
            ->get();
        return $query;
    }

    /**
     * update_morale_DB     Updates the morale and strike status of a hired staff member
     *
     * @param int $id_hired_staff    ID of the hired staff
     * @param int $morale            New morale value (0-100)
     * @param int $on_strike         Strike status (0 or 1)
     * @return bool                  True on success
     */
    public function update_morale_DB($id_hired_staff, $morale, $on_strike){
        $this->db->where('id_hired_staff', $id_hired_staff);
        $this->db->update('game_hired_staff', [
            'morale'    => max(MORALE_MIN, min(MORALE_MAX, (int)$morale)),
            'on_strike' => (int)(bool)$on_strike,
        ]);
        return $this->db->affected_rows() >= 1;
    }

    /**
     * get_all_hired_staff_for_morale_DB     Gets all hired staff for a resort with morale info
     *
     * @param int $id_resort    Resort ID
     * @return object           Query result
     */
    public function get_all_hired_staff_for_morale_DB($id_resort){
        $query = $this->db
            ->select('game_hired_staff.id_hired_staff, game_hired_staff.id_item_assigned,
                      game_hired_staff.morale, game_hired_staff.on_strike,
                      game_hired_staff.skill_level, game_hired_staff.experience_points,
                      game_hired_staff.trait, game_hired_staff.specialization,
                      game_staff.salary')
            ->from('game_hired_staff')
            ->join('game_staff', 'game_staff.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_resort', $id_resort)
            ->get();
        return $query;
    }

    /**
     * get_resort_morale_summary_DB     Gets average morale and strike count for a resort
     *
     * @param int $id_resort    Resort ID
     * @return object|false     Row with avg_morale and strike_count, or false
     */
    public function get_resort_morale_summary_DB($id_resort){
        $query = $this->db
            ->select('AVG(morale) as avg_morale, SUM(on_strike) as strike_count, COUNT(*) as total_staff')
            ->from('game_hired_staff')
            ->where('id_resort', $id_resort)
            ->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            if ($row->total_staff > 0)
                return $row;
        }
        return false;
    }

    /**
     * count_hired_snowmakers_DB    Returns the number of hired snowmaking operators for a resort.
     *
     * @param  int $id_resort
     * @return int
     */
    public function count_hired_snowmakers_DB($id_resort) {
        $query = $this->db
            ->select('COUNT(*) as count')
            ->from('game_hired_staff')
            ->join('game_staff as game_staff_tbl', 'game_staff_tbl.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_staff_tbl.position', 'snowmaker')
            ->where('game_hired_staff.id_resort', $id_resort)
            ->get();
        $row = $query->row();
        return $row ? (int)$row->count : 0;
    }

    /**
     * get_hired_staff_contract_DB     Returns contract_months for a specific hired staff row.
     *
     * @param int $id_resort
     * @param int $id_hired_staff
     * @return int   contract_months (0 if not found or column missing)
     */
    public function get_hired_staff_contract_DB($id_resort, $id_hired_staff) {
        $query = $this->db
            ->select('contract_months')
            ->from('game_hired_staff')
            ->where('id_hired_staff', $id_hired_staff)
            ->where('id_resort', $id_resort)
            ->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return isset($row->contract_months) ? (int)$row->contract_months : 0;
        }
        return 0;
    }

    // =========================================================================
    // Candidate Pool (new hire system)
    // =========================================================================

    /**
     * get_candidates_DB     Returns available (not yet hired) resort-specific candidates.
     *
     * Returns only resort-specific rows (id_resort = $id_resort), ordered by
     * position then efficiency.  Global template rows (id_resort IS NULL) are
     * excluded so that refreshing the pool always shows a visibly different set.
     *
     * @param int         $id_resort  Current resort ID
     * @param string|null $position   Filter by position (optional)
     * @return object                 Query result object
     */
    public function get_candidates_DB($id_resort, $position = null) {
        $this->db
            ->select('*')
            ->from('game_staff_candidates')
            ->where('is_hired', 0)
            ->where('id_resort', $id_resort);

        if ($position !== null) {
            $this->db->where('position', $position);
        }

        // Exclude expired resort-specific rows
        $today = date('Y-m-d');
        $this->db
            ->group_start()
                ->where('available_until IS NULL', NULL, FALSE)
                ->or_where('available_until >=', $today)
            ->group_end()
            ->order_by('position', 'ASC')
            ->order_by('efficiency', 'ASC');

        return $this->db->get();
    }

    /**
     * get_candidate_DB     Returns a single candidate by ID.
     *
     * @param int $id_candidate
     * @return object|false   Row object or false
     */
    public function get_candidate_DB($id_candidate) {
        $query = $this->db
            ->select('*')
            ->from('game_staff_candidates')
            ->where('id_candidate', $id_candidate)
            ->get();
        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    /**
     * mark_candidate_hired_DB     Marks a candidate as hired so they leave the pool.
     *
     * @param int $id_candidate
     * @return bool
     */
    public function mark_candidate_hired_DB($id_candidate) {
        $this->db->where('id_candidate', $id_candidate);
        $this->db->update('game_staff_candidates', ['is_hired' => 1]);
        return $this->db->affected_rows() >= 1;
    }

    /**
     * hire_candidate_db     Inserts a new hired-staff record from a candidate.
     *
     * Wraps hire_staff_db() but includes the new career-progression fields.
     *
     * @param array $data   Full data array for game_hired_staff
     * @return bool
     */
    public function hire_candidate_db($data) {
        return $this->db->insert('game_hired_staff', $data);
    }

    /**
     * seed_candidates_for_resort_db
     *
     * Clones CANDIDATE_POOL_SIZE global candidates per position into
     * resort-specific rows with a 7-day expiry (available_until).
     * Skips positions that already have unexpired resort-specific candidates.
     *
     * @param int   $id_resort
     * @param array $positions   List of position strings to seed
     * @return void
     */
    public function seed_candidates_for_resort_db($id_resort, array $positions) {
        $expiry = date('Y-m-d', strtotime('+'.CANDIDATE_EXPIRY_DAYS.' days'));
        $today  = date('Y-m-d');
        $now    = date('Y-m-d H:i:s');

        foreach ($positions as $position) {
            // Check how many unexpired resort-specific candidates still exist
            $existing = $this->db
                ->where('id_resort', $id_resort)
                ->where('position',  $position)
                ->where('is_hired',  0)
                ->group_start()
                    ->where('available_until IS NULL', NULL, FALSE)
                    ->or_where('available_until >=', $today)
                ->group_end()
                ->count_all_results('game_staff_candidates');

            if ($existing >= CANDIDATE_POOL_SIZE) {
                continue;   // Pool already has enough candidates
            }

            $needed = CANDIDATE_POOL_SIZE - $existing;

            // Pick random global candidates for this position
            $global = $this->db
                ->select('*')
                ->from('game_staff_candidates')
                ->where('id_resort IS NULL', NULL, FALSE)
                ->where('position', $position)
                ->where('is_hired', 0)
                ->order_by('RAND()', NULL, FALSE)
                ->limit($needed)
                ->get();

            foreach ($global->result() as $g) {
                $this->db->insert('game_staff_candidates', [
                    'id_resort'      => $id_resort,
                    'position'       => $g->position,
                    'name_english'   => $g->name_english,
                    'name_french'    => $g->name_french,
                    'efficiency'     => $g->efficiency,
                    'salary'         => $g->salary,
                    'hire_bonus'     => $g->hire_bonus,
                    'specialization' => $g->specialization,
                    'trait'          => $g->trait,
                    'contract_months'=> $g->contract_months,
                    'available_until'=> $expiry,
                    'is_hired'       => 0,
                    'created_at'     => $now,
                ]);
            }
        }
    }

    /**
     * refresh_candidates_for_resort_db
     *
     * Expires all current resort-specific candidates for a position, then
     * re-seeds a fresh pool.  Called when the player clicks "Refresh pool".
     *
     * @param int    $id_resort
     * @param string $position
     * @return void
     */
    public function refresh_candidates_for_resort_db($id_resort, $position) {
        // Expire (soft-delete) existing resort-specific candidates for this position
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->db
            ->where('id_resort', $id_resort)
            ->where('position',  $position)
            ->where('is_hired',  0)
            ->update('game_staff_candidates', ['available_until' => $yesterday]);

        // Seed a fresh pool
        $this->seed_candidates_for_resort_db($id_resort, [$position]);
    }

    /**
     * add_experience_db     Adds XP to a hired staff member and promotes if threshold met.
     *
     * @param int $id_hired_staff
     * @param int $xp_to_add
     * @return array  ['leveled_up' => bool, 'new_level' => int]
     */
    public function add_experience_db($id_hired_staff, $xp_to_add) {
        // Fetch current XP and skill level
        $row = $this->db
            ->select('experience_points, skill_level')
            ->from('game_hired_staff')
            ->where('id_hired_staff', $id_hired_staff)
            ->get()->row();

        if (!$row) return ['leveled_up' => false, 'new_level' => 1];

        $old_level = (int)$row->skill_level;
        $new_xp    = (int)$row->experience_points + (int)$xp_to_add;
        $new_level = $old_level;

        while ($new_xp >= STAFF_XP_PER_LEVEL && $new_level < STAFF_MAX_SKILL_LEVEL) {
            $new_xp   -= STAFF_XP_PER_LEVEL;
            $new_level++;
        }

        $this->db->where('id_hired_staff', $id_hired_staff);
        $this->db->update('game_hired_staff', [
            'experience_points' => $new_xp,
            'skill_level'       => $new_level,
        ]);

        return ['leveled_up' => ($new_level > $old_level), 'new_level' => $new_level];
    }

    /**
     * ensure_training_column   Adds last_training_date to game_hired_staff if it is missing.
     * Called lazily before any training-related query.
     */
    public function ensure_training_column() {
        if (!$this->db->field_exists('last_training_date', 'game_hired_staff')) {
            $this->load->dbforge();
            $this->dbforge->add_column('game_hired_staff', [
                'last_training_date' => [
                    'type'    => 'DATETIME',
                    'null'    => TRUE,
                    'default' => NULL,
                ],
            ]);
        }
    }

    /**
     * check_staff_belongs_to_resort_db   Confirms the hired staff row belongs to the resort.
     *
     * @param int $id_resort
     * @param int $id_hired_staff
     * @return bool
     */
    public function check_staff_belongs_to_resort_db($id_resort, $id_hired_staff) {
        $count = $this->db
            ->where('id_hired_staff', (int)$id_hired_staff)
            ->where('id_resort',      (int)$id_resort)
            ->count_all_results('game_hired_staff');
        return ($count > 0);
    }

    /**
     * can_train_db   Checks whether a staff member can receive a training session.
     *
     * Returns ['ok' => true] when training is allowed, or
     * ['ok' => false, 'reason' => string, ...] when it is not.
     *
     * @param  int   $id_hired_staff
     * @return array
     */
    public function can_train_db($id_hired_staff) {
        $this->ensure_training_column();

        $row = $this->db
            ->select('skill_level, last_training_date')
            ->from('game_hired_staff')
            ->where('id_hired_staff', (int)$id_hired_staff)
            ->get()->row();

        if (!$row) {
            return ['ok' => false, 'reason' => 'not_found'];
        }

        if ((int)$row->skill_level >= STAFF_MAX_SKILL_LEVEL) {
            return ['ok' => false, 'reason' => 'max_level'];
        }

        if ($row->last_training_date !== null) {
            $last_trained     = strtotime($row->last_training_date);
            $cooldown_seconds = STAFF_TRAINING_COOLDOWN_HOURS * 3600;
            if (time() - $last_trained < $cooldown_seconds) {
                $next_at = date('Y-m-d H:i', $last_trained + $cooldown_seconds);
                return ['ok' => false, 'reason' => 'cooldown', 'next_training' => $next_at];
            }
        }

        return ['ok' => true];
    }

    /**
     * record_training_db   Awards XP to a staff member and stamps last_training_date.
     *
     * @param int $id_hired_staff
     */
    public function record_training_db($id_hired_staff) {
        $this->ensure_training_column();
        $this->add_experience_db((int)$id_hired_staff, STAFF_TRAINING_XP);
        $this->db
            ->set('last_training_date', gmdate('Y-m-d H:i:s'))
            ->where('id_hired_staff', (int)$id_hired_staff)
            ->update('game_hired_staff');
    }

    /**
     * count_candidates_for_resort_DB     Returns the count of available candidates per position.
     *
     * @param int $id_resort
     * @return array   ['position' => count, ...]
     */
    public function count_candidates_for_resort_DB($id_resort) {
        $today = date('Y-m-d');
        $query = $this->db
            ->select('position, COUNT(*) as cnt')
            ->from('game_staff_candidates')
            ->where('is_hired', 0)
            ->group_start()
                ->where('id_resort', $id_resort)
                ->or_where('id_resort IS NULL', NULL, FALSE)
            ->group_end()
            ->group_start()
                ->where('available_until IS NULL', NULL, FALSE)
                ->or_where('available_until >=', $today)
            ->group_end()
            ->group_by('position')
            ->get();

        $result = [];
        foreach ($query->result() as $row) {
            $result[$row->position] = (int)$row->cnt;
        }
        return $result;
    }

}

?>