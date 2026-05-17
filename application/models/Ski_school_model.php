<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ski_school_model
 *
 * Handles database operations for the Ski School feature.
 */
class Ski_school_model extends CI_Model {

    /**
     * get_all_lesson_types  Returns all active lesson types.
     *
     * @return object  CI DB result object
     */
    public function get_all_lesson_types() {
        return $this->db
            ->where('active', 1)
            ->order_by('skill_level', 'ASC')
            ->order_by('price_per_guest', 'ASC')
            ->get('game_ski_school_types');
    }

    /**
     * get_lesson_type  Returns a single lesson type by ID.
     *
     * @param  int $id_lesson_type
     * @return object|null
     */
    public function get_lesson_type($id_lesson_type) {
        return $this->db
            ->where('id_lesson_type', (int)$id_lesson_type)
            ->where('active', 1)
            ->limit(1)
            ->get('game_ski_school_types')
            ->row();
    }

    /**
     * count_todays_sessions  How many sessions a resort has run today.
     *
     * @param  int $id_resort
     * @return int
     */
    public function count_todays_sessions($id_resort) {
        return (int)$this->db
            ->where('id_resort',    (int)$id_resort)
            ->where('session_date', gmdate('Y-m-d'))
            ->count_all_results('game_ski_school_sessions');
    }

    /**
     * get_history  Returns the last N sessions for a resort.
     *
     * @param  int $id_resort
     * @param  int $limit
     * @return object  CI DB result object
     */
    public function get_history($id_resort, $limit = 14) {
        return $this->db
            ->select('s.*, t.name_english, t.name_french, t.skill_level')
            ->from('game_ski_school_sessions s')
            ->join('game_ski_school_types t', 's.id_lesson_type = t.id_lesson_type', 'left')
            ->where('s.id_resort', (int)$id_resort)
            ->order_by('s.session_date', 'DESC')
            ->order_by('s.id_session',   'DESC')
            ->limit((int)$limit)
            ->get();
    }

    /**
     * get_totals  Returns aggregate revenue and rep earned for a resort.
     *
     * @param  int $id_resort
     * @return object|null  {total_revenue, total_rep, total_sessions}
     */
    public function get_totals($id_resort) {
        return $this->db
            ->select('SUM(revenue) AS total_revenue, SUM(rep_earned) AS total_rep, COUNT(*) AS total_sessions')
            ->where('id_resort', (int)$id_resort)
            ->get('game_ski_school_sessions')
            ->row();
    }

    /**
     * run_session  Runs a ski-school session and credits the resort.
     *
     * @param  int $id_resort
     * @param  int $id_lesson_type
     * @param  int $guests_enrolled  1 .. max_guests_per_session
     * @return array {ok, revenue, rep_earned, error}
     */
    public function run_session($id_resort, $id_lesson_type, $guests_enrolled) {
        $id_resort      = (int)$id_resort;
        $id_lesson_type = (int)$id_lesson_type;
        $guests_enrolled = max(1, (int)$guests_enrolled);

        $lt = $this->get_lesson_type($id_lesson_type);
        if (!$lt) {
            return ['ok' => false, 'revenue' => 0, 'rep_earned' => 0, 'error' => 'invalid_lesson_type'];
        }

        // Cap guests at max
        $guests_enrolled = min($guests_enrolled, (int)$lt->max_guests_per_session);

        $gross_revenue   = $guests_enrolled * (int)$lt->price_per_guest;
        $instructor_cost = (int)$lt->instructor_cost;
        $net_revenue     = $gross_revenue - $instructor_cost;
        $rep_earned      = (int)$lt->rep_bonus;

        $today = gmdate('Y-m-d');
        $now   = gmdate('Y-m-d H:i:s');

        $this->db->trans_start();

        // Insert session record
        $this->db->insert('game_ski_school_sessions', [
            'id_resort'       => $id_resort,
            'id_lesson_type'  => $id_lesson_type,
            'session_date'    => $today,
            'guests_enrolled' => $guests_enrolled,
            'revenue'         => $net_revenue,
            'rep_earned'      => $rep_earned,
            'created_at'      => $now,
        ]);

        // Credit cash and reputation to the resort
        $update_sql = 'cash = cash + ' . $net_revenue;
        if ($rep_earned > 0) {
            $update_sql .= ', reputation = reputation + ' . $rep_earned;
        }
        $this->db->query(
            "UPDATE game_resorts SET {$update_sql} WHERE id_resort = ?",
            [$id_resort]
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['ok' => false, 'revenue' => 0, 'rep_earned' => 0, 'error' => 'db_error'];
        }

        return [
            'ok'         => true,
            'revenue'    => $net_revenue,
            'rep_earned' => $rep_earned,
            'error'      => null,
        ];
    }
}
