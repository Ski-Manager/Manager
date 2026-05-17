<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Crowding_model
 *
 * Manages per-resort crowding settings stored in game_resort_crowding.
 */
class Crowding_model extends CI_Model {

    /**
     * get_settings_DB  Returns the crowding settings row for a resort.
     *                  Returns default values when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('capacity_limit, timed_entry_enabled, crowd_alert_threshold')
            ->from('game_resort_crowding')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'capacity_limit'        => CROWDING_DEFAULT_CAPACITY_LIMIT,
                'timed_entry_enabled'   => 0,
                'crowd_alert_threshold' => CROWDING_DEFAULT_ALERT_THRESHOLD,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the crowding settings for a resort.
     *
     * @param int $id_resort
     * @param int $capacity_limit
     * @param int $timed_entry_enabled   0 or 1
     * @param int $crowd_alert_threshold
     * @return bool
     */
    public function save_settings_DB($id_resort, $capacity_limit, $timed_entry_enabled, $crowd_alert_threshold) {
        $data = [
            'id_resort'              => (int)$id_resort,
            'capacity_limit'         => (int)$capacity_limit,
            'timed_entry_enabled'    => (int)$timed_entry_enabled,
            'crowd_alert_threshold'  => (int)$crowd_alert_threshold,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_crowding');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_crowding', $data);
        } else {
            $this->db->insert('game_resort_crowding', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
