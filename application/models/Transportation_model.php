<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transportation_model
 *
 * Manages per-resort accessibility & transportation settings
 * stored in game_resort_transportation.
 */
class Transportation_model extends CI_Model {

    /**
     * get_settings_DB  Returns the transportation settings row for a resort.
     *                  Returns defaults when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('shuttle_level, ski_storage, gondola_link')
            ->from('game_resort_transportation')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'shuttle_level' => 0,
                'ski_storage'   => 0,
                'gondola_link'  => 0,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the transportation settings for a resort.
     *
     * @param int $id_resort
     * @param int $shuttle_level  0–TRANSPORT_SHUTTLE_MAX_LEVEL
     * @param int $ski_storage    0 or 1
     * @param int $gondola_link   0 or 1
     * @return bool
     */
    public function save_settings_DB($id_resort, $shuttle_level, $ski_storage, $gondola_link) {
        $data = [
            'id_resort'     => (int)$id_resort,
            'shuttle_level' => (int)$shuttle_level,
            'ski_storage'   => (int)$ski_storage,
            'gondola_link'  => (int)$gondola_link,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_transportation');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_transportation', $data);
        } else {
            $this->db->insert('game_resort_transportation', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
