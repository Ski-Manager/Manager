<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lift_tech_model
 *
 * Handles database operations for the Lift Technology Research Tree.
 * Each resort can research lift upgrades that take time and money to complete.
 */
class Lift_tech_model extends CI_Model {

    /**
     * get_all_research_DB  Returns all research rows for a resort.
     *
     * @param  int   $id_resort
     * @return object  CI query result
     */
    public function get_all_research_DB($id_resort) {
        return $this->db
            ->select('tech_key, status, started_at, finish_at')
            ->from('game_lift_tech_research')
            ->where('id_resort', $id_resort)
            ->get();
    }

    /**
     * get_research_row_DB  Returns a single research row for a resort + tech.
     *
     * @param  int    $id_resort
     * @param  string $tech_key
     * @return object|null  Row object or null
     */
    public function get_research_row_DB($id_resort, $tech_key) {
        $query = $this->db
            ->select('tech_key, status, started_at, finish_at')
            ->from('game_lift_tech_research')
            ->where('id_resort', $id_resort)
            ->where('tech_key',  $tech_key)
            ->get();
        return $query->row();
    }

    /**
     * start_research_DB  Inserts a new research record (in_progress).
     *
     * @param  int    $id_resort
     * @param  string $tech_key
     * @param  int    $duration_days  Number of days until research completes
     * @return bool
     */
    public function start_research_DB($id_resort, $tech_key, $duration_days) {
        $now       = gmdate('Y-m-d H:i:s');
        $finish_at = gmdate('Y-m-d H:i:s', strtotime("+{$duration_days} days"));
        return $this->db->insert('game_lift_tech_research', [
            'id_resort'  => $id_resort,
            'tech_key'   => $tech_key,
            'status'     => 'in_progress',
            'started_at' => $now,
            'finish_at'  => $finish_at,
        ]);
    }

    /**
     * complete_overdue_research_DB  Marks all in_progress rows as completed
     *                               where finish_at is in the past.
     *                               Called from the nightly job.
     *
     * @return int  Number of rows updated
     */
    public function complete_overdue_research_DB() {
        $this->db->set('status', 'completed');
        $this->db->where('status', 'in_progress');
        $this->db->where('finish_at <=', gmdate('Y-m-d H:i:s'));
        $this->db->update('game_lift_tech_research');
        return $this->db->affected_rows();
    }
}
