<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rd_model
 *
 * Manages the Experimental Tech & R&D system for each resort.
 *
 * Database table: game_resort_rd
 *   id_rd        INT  AUTO_INCREMENT PRIMARY KEY
 *   id_resort    INT  NOT NULL
 *   project_key  VARCHAR(40) NOT NULL
 *   status       ENUM('in_progress','completed','failed') NOT NULL DEFAULT 'in_progress'
 *   rushed       TINYINT(1) NOT NULL DEFAULT 0
 *   started_at   DATETIME NOT NULL
 *   finish_at    DATETIME NOT NULL
 *   UNIQUE KEY uq_resort_project (id_resort, project_key)
 *
 * The table is created automatically on first use (ensure_table_exists).
 */
class Rd_model extends CI_Model {

    /**
     * ensure_table_exists  Creates game_resort_rd if it does not yet exist.
     */
    public function ensure_table_exists() {
        if (!$this->db->table_exists('game_resort_rd')) {
            $this->db->query("
                CREATE TABLE game_resort_rd (
                    id_rd       INT          NOT NULL AUTO_INCREMENT,
                    id_resort   INT          NOT NULL,
                    project_key VARCHAR(40)  NOT NULL,
                    status      ENUM('in_progress','completed','failed') NOT NULL DEFAULT 'in_progress',
                    rushed      TINYINT(1)   NOT NULL DEFAULT 0,
                    started_at  DATETIME     NOT NULL,
                    finish_at   DATETIME     NOT NULL,
                    PRIMARY KEY (id_rd),
                    UNIQUE KEY uq_resort_project (id_resort, project_key)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * get_all_projects_DB  Returns all R&D rows for a resort.
     *
     * @param  int  $id_resort
     * @return object  CI query result
     */
    public function get_all_projects_DB($id_resort) {
        return $this->db
            ->select('project_key, status, rushed, started_at, finish_at')
            ->from('game_resort_rd')
            ->where('id_resort', (int)$id_resort)
            ->get();
    }

    /**
     * get_project_row_DB  Returns a single R&D row for a resort + project key.
     *
     * @param  int    $id_resort
     * @param  string $project_key
     * @return object|null
     */
    public function get_project_row_DB($id_resort, $project_key) {
        return $this->db
            ->select('project_key, status, rushed, started_at, finish_at')
            ->from('game_resort_rd')
            ->where('id_resort',    (int)$id_resort)
            ->where('project_key',  $project_key)
            ->get()
            ->row();
    }

    /**
     * get_in_progress_due_DB  Returns all in_progress rows whose finish_at is past,
     *                         across all resorts (used by nightly job).
     *
     * @return object  CI query result
     */
    public function get_in_progress_due_DB() {
        return $this->db
            ->select('id_rd, id_resort, project_key, rushed')
            ->from('game_resort_rd')
            ->where('status',     'in_progress')
            ->where('finish_at <=', gmdate('Y-m-d H:i:s'))
            ->get();
    }

    /**
     * get_completed_by_resort_DB  Returns all completed project keys for a resort.
     *
     * @param  int  $id_resort
     * @return object  CI query result
     */
    public function get_completed_by_resort_DB($id_resort) {
        return $this->db
            ->select('project_key')
            ->from('game_resort_rd')
            ->where('id_resort', (int)$id_resort)
            ->where('status',    'completed')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * start_project_DB  Inserts a new in_progress R&D row.
     *
     * @param  int    $id_resort
     * @param  string $project_key
     * @param  int    $duration_days
     * @param  int    $rushed         0 = normal, 1 = rushed
     * @return bool
     */
    public function start_project_DB($id_resort, $project_key, $duration_days, $rushed = 0) {
        $now       = gmdate('Y-m-d H:i:s');
        $finish_at = gmdate('Y-m-d H:i:s', strtotime("+{$duration_days} days"));
        return $this->db->insert('game_resort_rd', [
            'id_resort'   => (int)$id_resort,
            'project_key' => $project_key,
            'status'      => 'in_progress',
            'rushed'      => (int)$rushed,
            'started_at'  => $now,
            'finish_at'   => $finish_at,
        ]);
    }

    /**
     * complete_project_DB  Marks a row as completed.
     *
     * @param  int  $id_rd
     * @return bool
     */
    public function complete_project_DB($id_rd) {
        $this->db->where('id_rd', (int)$id_rd);
        return $this->db->update('game_resort_rd', ['status' => 'completed']);
    }

    /**
     * fail_project_DB  Marks a row as failed.
     *
     * @param  int  $id_rd
     * @return bool
     */
    public function fail_project_DB($id_rd) {
        $this->db->where('id_rd', (int)$id_rd);
        return $this->db->update('game_resort_rd', ['status' => 'failed']);
    }
}
