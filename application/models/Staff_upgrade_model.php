<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Staff_upgrade_model
 *
 * Handles database operations for the Staff Upgrade Research Tree.
 */
class Staff_upgrade_model extends CI_Model {

    public function get_all_research_DB($id_resort) {
        return $this->db
            ->select('upgrade_key, status, started_at, finish_at')
            ->from('game_staff_upgrade_research')
            ->where('id_resort', $id_resort)
            ->get();
    }

    public function get_research_row_DB($id_resort, $upgrade_key) {
        $query = $this->db
            ->select('upgrade_key, status, started_at, finish_at')
            ->from('game_staff_upgrade_research')
            ->where('id_resort',    $id_resort)
            ->where('upgrade_key',  $upgrade_key)
            ->get();
        return $query->row();
    }

    public function start_research_DB($id_resort, $upgrade_key, $duration_days) {
        $now       = gmdate('Y-m-d H:i:s');
        $finish_at = gmdate('Y-m-d H:i:s', strtotime("+{$duration_days} days"));
        return $this->db->insert('game_staff_upgrade_research', [
            'id_resort'   => $id_resort,
            'upgrade_key' => $upgrade_key,
            'status'      => 'in_progress',
            'started_at'  => $now,
            'finish_at'   => $finish_at,
        ]);
    }

    public function complete_overdue_research_DB() {
        $this->db->set('status', 'completed');
        $this->db->where('status', 'in_progress');
        $this->db->where('finish_at <=', gmdate('Y-m-d H:i:s'));
        $this->db->update('game_staff_upgrade_research');
        return $this->db->affected_rows();
    }
}
