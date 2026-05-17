<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Town_model
 *
 * Manages the local town development state (game_resort_town) for each resort.
 *
 * Town levels (0–5):
 *   0 – No town
 *   1 – Hamlet
 *   2 – Village
 *   3 – Town
 *   4 – Resort Town
 *   5 – Alpine City
 *
 * Growth is driven by open hotels and resort reputation (see TOWN_* constants).
 * When no hotels are open the town decays and the resort's reputation is penalised.
 */
class Town_model extends CI_Model {

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * get_town_DB      Returns the town record for a resort, or NULL if none exists yet.
     *
     * @param int $id_resort
     * @return object|null
     */
    public function get_town_DB($id_resort) {
        return $this->db
            ->from('game_resort_town')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * upsert_town_DB   Inserts or updates the town record for a resort.
     *
     * @param int   $id_resort
     * @param array $data   Keys: town_level, growth_points, updated_at
     * @return bool
     */
    public function upsert_town_DB($id_resort, array $data) {
        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_town');

        $data['id_resort'] = (int)$id_resort;

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_town', $data);
        } else {
            $this->db->insert('game_resort_town', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * get_open_hotels_count_DB     Returns the number of open hotels for a resort.
     *
     * @param int $id_resort
     * @return int
     */
    public function get_open_hotels_count_DB($id_resort) {
        return (int)$this->db
            ->from('game_created_buildings')
            ->where('id_resort', (int)$id_resort)
            ->where('type', 'hotel')
            ->where('id_status', '1')
            ->count_all_results();
    }
}
