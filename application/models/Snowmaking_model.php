<?php

/**
 * Snowmaking_model
 *
 * Handles per-trail snowmaking equipment (game_trail_snowmaking table).
 * Equipment types and brands are defined as constants in config.php.
 */
class Snowmaking_model extends CI_Model {

    /**
     * get_slopes_with_snowmaking_DB    Returns all player slopes with their
     *                                  snowmaking equipment (if any).
     *
     * @param  int $id_resort
     * @return CI_DB_result
     */
    public function get_slopes_with_snowmaking_DB($id_resort) {
        $this->db->select('game_created_slopes.id_created_slopes, game_created_slopes.custom_name, game_created_slopes.id_status, game_created_slopes.slope_snow_level,
            tsm.id_trail_snowmaking, tsm.equipment_type, tsm.brand, tsm.is_active, tsm.snow_output, tsm.daily_cost, tsm.purchased_at');
        $this->db->from('game_created_slopes');
        $this->db->join('game_trail_snowmaking tsm',
            'tsm.id_resort = ' . (int)$id_resort . ' AND tsm.id_created_slopes = game_created_slopes.id_created_slopes',
            'left', FALSE);
        $this->db->where('game_created_slopes.id_resort', $id_resort);
        $this->db->where_in('game_created_slopes.id_status', ['1', '2']);  // open or closed slopes
        $this->db->order_by('game_created_slopes.id_created_slopes', 'ASC');
        return $this->db->get();
    }

    /**
     * get_trail_snowmaking_by_id_DB    Returns one trail snowmaking record.
     *
     * @param  int $id_resort
     * @param  int $id_trail_snowmaking
     * @return CI_DB_result
     */
    public function get_trail_snowmaking_by_id_DB($id_resort, $id_trail_snowmaking) {
        return $this->db
            ->select('*')
            ->from('game_trail_snowmaking')
            ->where('id_resort', $id_resort)
            ->where('id_trail_snowmaking', $id_trail_snowmaking)
            ->get();
    }

    /**
     * slope_belongs_to_resort_DB   Checks whether a slope belongs to a resort.
     *
     * @param  int $id_resort
     * @param  int $id_created_slopes
     * @return bool
     */
    public function slope_belongs_to_resort_DB($id_resort, $id_created_slopes) {
        $result = $this->db
            ->select('id_created_slopes')
            ->from('game_created_slopes')
            ->where('id_resort', $id_resort)
            ->where('id_created_slopes', $id_created_slopes)
            ->get();
        return ($result->num_rows() > 0);
    }

    /**
     * get_trail_snowmaking_for_slope_DB    Returns the snowmaking record for a
     *                                      specific slope (if purchased).
     *
     * @param  int $id_resort
     * @param  int $id_created_slopes
     * @return CI_DB_result
     */
    public function get_trail_snowmaking_for_slope_DB($id_resort, $id_created_slopes) {
        return $this->db
            ->select('*')
            ->from('game_trail_snowmaking')
            ->where('id_resort', $id_resort)
            ->where('id_created_slopes', $id_created_slopes)
            ->get();
    }

    /**
     * add_trail_snowmaking_DB  Inserts a new trail snowmaking record.
     *
     * @param  array $data  Keys: id_resort, id_created_slopes, equipment_type,
     *                            brand, is_active, snow_output, daily_cost, purchased_at
     * @return bool
     */
    public function add_trail_snowmaking_DB($data) {
        return $this->db->insert('game_trail_snowmaking', $data);
    }

    /**
     * update_trail_snowmaking_status_DB    Toggles active/inactive for one piece
     *                                      of trail snowmaking equipment.
     *
     * @param  int $id_resort
     * @param  int $id_trail_snowmaking
     * @param  int $is_active   1 = active, 0 = inactive
     * @return bool
     */
    public function update_trail_snowmaking_status_DB($id_resort, $id_trail_snowmaking, $is_active) {
        $this->db->where('id_resort', $id_resort);
        $this->db->where('id_trail_snowmaking', $id_trail_snowmaking);
        return $this->db->update('game_trail_snowmaking', ['is_active' => $is_active]);
    }

    /**
     * remove_trail_snowmaking_DB   Removes snowmaking equipment from a trail.
     *
     * @param  int $id_resort
     * @param  int $id_trail_snowmaking
     * @return bool
     */
    public function remove_trail_snowmaking_DB($id_resort, $id_trail_snowmaking) {
        $this->db->where('id_resort', $id_resort);
        $this->db->where('id_trail_snowmaking', $id_trail_snowmaking);
        return $this->db->delete('game_trail_snowmaking');
    }

    /**
     * update_all_trail_snowmaking_status_DB    Sets is_active for all trail
     *                                          snowmaking equipment in a resort.
     *
     * @param  int $id_resort
     * @param  int $is_active   1 = active, 0 = inactive
     * @return bool
     */
    public function update_all_trail_snowmaking_status_DB($id_resort, $is_active) {
        $this->db->where('id_resort', $id_resort);
        return $this->db->update('game_trail_snowmaking', ['is_active' => $is_active]);
    }

    /**
     * upgrade_trail_snowmaking_DB  Replaces the equipment type and brand on an
     *                              existing trail snowmaking record and updates
     *                              snow_output and daily_cost accordingly.
     *
     * @param  int   $id_resort
     * @param  int   $id_trail_snowmaking
     * @param  array $data   Keys: equipment_type, brand, snow_output, daily_cost
     * @return bool
     */
    public function upgrade_trail_snowmaking_DB($id_resort, $id_trail_snowmaking, $data) {
        $this->db->where('id_resort', $id_resort);
        $this->db->where('id_trail_snowmaking', $id_trail_snowmaking);
        return $this->db->update('game_trail_snowmaking', $data);
    }

    /**
     * get_active_trail_snowmaking_DB   Returns all active trail snowmaking
     *                                  records for a resort (used by nightly job).
     *
     * @param  int $id_resort
     * @return CI_DB_result
     */
    public function get_active_trail_snowmaking_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_trail_snowmaking')
            ->where('id_resort', $id_resort)
            ->where('is_active', 1)
            ->get();
    }

    /**
     * get_all_active_trail_snowmaking_DB   Returns all active trail snowmaking
     *                                      records across all resorts (nightly job).
     *
     * @return CI_DB_result
     */
    public function get_all_active_trail_snowmaking_DB() {
        return $this->db
            ->select('id_resort, SUM(snow_output) as total_snow_output, SUM(daily_cost) as total_daily_cost')
            ->from('game_trail_snowmaking')
            ->where('is_active', 1)
            ->group_by('id_resort')
            ->get();
    }
}
