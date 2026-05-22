<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Maintenance_depth_model
 *
 * Manages per-resort preventive maintenance plan settings stored in
 * game_resort_maintenance_depth, and helper queries used by the nightly job.
 */
class Maintenance_depth_model extends CI_Model {

    /**
     * get_settings_DB  Returns the maintenance plan row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('maintenance_plan')
            ->from('game_resort_maintenance_depth')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)['maintenance_plan' => 'basic'];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the maintenance plan for a resort.
     *
     * @param int    $id_resort
     * @param string $maintenance_plan   'basic' | 'standard' | 'preventive'
     * @return bool
     */
    public function save_settings_DB($id_resort, $maintenance_plan) {
        $data = [
            'id_resort'        => (int)$id_resort,
            'maintenance_plan' => $maintenance_plan,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_maintenance_depth');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_maintenance_depth', $data);
        } else {
            $this->db->insert('game_resort_maintenance_depth', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_avg_liftmechanic_efficiency_DB   Returns the average efficiency of all
     *                                       liftmechanic staff hired by a resort.
     *                                       Returns 50 (neutral) when none are hired.
     *
     * @param int $id_resort
     * @return float
     */
    public function get_avg_liftmechanic_efficiency_DB($id_resort) {
        $row = $this->db
            ->select('AVG(game_staff.efficiency) AS avg_eff')
            ->from('game_hired_staff')
            ->join('game_staff', 'game_staff.id_staff = game_hired_staff.id_staff', 'inner')
            ->where('game_hired_staff.id_resort', (int)$id_resort)
            ->where('game_staff.position', 'liftmechanic')
            ->get()
            ->row();

        return ($row && $row->avg_eff !== NULL) ? (float)$row->avg_eff : 50.0;
    }
}
