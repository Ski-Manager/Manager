<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lift_line_model
 *
 * Manages per-resort lift line (queue) settings stored in game_resort_lift_lines.
 */
class Lift_line_model extends CI_Model {

    /**
     * get_settings_DB  Returns the lift-line settings row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('queue_tolerance_minutes, vip_fastpass_enabled, vip_fastpass_price')
            ->from('game_resort_lift_lines')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            // Return sensible defaults when the resort has no row yet
            $row = (object)[
                'queue_tolerance_minutes' => LIFT_LINE_DEFAULT_TOLERANCE,
                'vip_fastpass_enabled'    => 0,
                'vip_fastpass_price'      => LIFT_LINE_VIP_DEFAULT_PRICE,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the lift-line settings for a resort.
     *
     * @param int $id_resort
     * @param int $queue_tolerance_minutes
     * @param int $vip_fastpass_enabled   0 or 1
     * @param int $vip_fastpass_price
     * @return bool
     */
    public function save_settings_DB($id_resort, $queue_tolerance_minutes, $vip_fastpass_enabled, $vip_fastpass_price) {
        $data = [
            'id_resort'               => (int)$id_resort,
            'queue_tolerance_minutes' => (int)$queue_tolerance_minutes,
            'vip_fastpass_enabled'    => (int)$vip_fastpass_enabled,
            'vip_fastpass_price'      => (int)$vip_fastpass_price,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_lift_lines');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_lift_lines', $data);
        } else {
            $this->db->insert('game_resort_lift_lines', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
