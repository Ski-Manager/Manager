<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Accommodation_model
 *
 * Manages per-resort accommodation upgrade settings stored in
 * game_resort_accommodations.
 */
class Accommodation_model extends CI_Model {

    /**
     * get_settings_DB  Returns the accommodation row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param  int    $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('accommodation_type, is_enabled')
            ->from('game_resort_accommodations')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'accommodation_type' => 'none',
                'is_enabled'         => 0,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the accommodation settings for a resort.
     *
     * @param  int    $id_resort
     * @param  string $accommodation_type  one of: none, cabin, lodge, luxury_hotel
     * @param  int    $is_enabled          0 or 1
     * @return bool
     */
    public function save_settings_DB($id_resort, $accommodation_type, $is_enabled) {
        $allowed = array_merge(['none'], array_keys(ACCOMMODATION_TYPES));
        if (!in_array($accommodation_type, $allowed, TRUE)) {
            $accommodation_type = 'none';
        }

        $data = [
            'id_resort'          => (int)$id_resort,
            'accommodation_type' => $accommodation_type,
            'is_enabled'         => (int)$is_enabled,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_accommodations');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_accommodations', $data);
        } else {
            $this->db->insert('game_resort_accommodations', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_all_resorts_settings_DB  Returns all accommodation rows (used by nightly job).
     *
     * @return object  CI DB result object
     */
    public function get_all_resorts_settings_DB() {
        return $this->db
            ->select('id_resort, accommodation_type, is_enabled')
            ->from('game_resort_accommodations')
            ->where('is_enabled', 1)
            ->where('accommodation_type !=', 'none')
            ->get();
    }
}
