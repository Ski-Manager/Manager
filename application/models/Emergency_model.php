<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Emergency_model
 *
 * Manages per-resort emergency & rescue settings stored in game_resort_emergency.
 */
class Emergency_model extends CI_Model {

    /**
     * get_settings_DB  Returns the emergency settings row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('rescue_team_level, medical_stations, insurance_enabled')
            ->from('game_resort_emergency')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'rescue_team_level' => 0,
                'medical_stations'  => 0,
                'insurance_enabled' => 0,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the emergency settings for a resort.
     *
     * @param int $id_resort
     * @param int $rescue_team_level  0–3
     * @param int $medical_stations   0–3
     * @param int $insurance_enabled  0 or 1
     * @return bool
     */
    public function save_settings_DB($id_resort, $rescue_team_level, $medical_stations, $insurance_enabled) {
        $data = [
            'id_resort'          => (int)$id_resort,
            'rescue_team_level'  => (int)$rescue_team_level,
            'medical_stations'   => (int)$medical_stations,
            'insurance_enabled'  => (int)$insurance_enabled,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_emergency');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_emergency', $data);
        } else {
            $this->db->insert('game_resort_emergency', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
