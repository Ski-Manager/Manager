<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Snow_report_model
 *
 * Handles database operations for daily snow condition reports.
 */
class Snow_report_model extends CI_Model {

    // -----------------------------------------------------------------------
    // Constants
    // -----------------------------------------------------------------------

    /** Reputation bonus awarded for each conditions tier. */
    const REP_BONUS = [
        'poor'      => 0,
        'fair'      => 2,
        'good'      => 5,
        'excellent' => 10,
    ];

    /**
     * get_latest_report  Returns the most recent snow report for a resort.
     *
     * @param  int $id_resort
     * @return object|null
     */
    public function get_latest_report($id_resort) {
        return $this->db
            ->where('id_resort', (int)$id_resort)
            ->order_by('report_date', 'DESC')
            ->limit(1)
            ->get('game_snow_reports')
            ->row();
    }

    /**
     * get_todays_report  Returns today's report for a resort, or NULL if none.
     *
     * @param  int $id_resort
     * @return object|null
     */
    public function get_todays_report($id_resort) {
        return $this->db
            ->where('id_resort',    (int)$id_resort)
            ->where('report_date',  gmdate('Y-m-d'))
            ->limit(1)
            ->get('game_snow_reports')
            ->row();
    }

    /**
     * get_history  Returns the last N snow reports for a resort.
     *
     * @param  int $id_resort
     * @param  int $limit
     * @return object  CI DB result object
     */
    public function get_history($id_resort, $limit = 10) {
        return $this->db
            ->where('id_resort', (int)$id_resort)
            ->order_by('report_date', 'DESC')
            ->limit((int)$limit)
            ->get('game_snow_reports');
    }

    /**
     * publish  Inserts a new snow report and awards a reputation bonus.
     *
     * @param  int    $id_resort
     * @param  array  $data  Keys: snow_depth_cm, fresh_snow_cm, conditions, piste_coverage, note
     * @return array  ['ok' => bool, 'rep_bonus' => int, 'already_published' => bool]
     */
    public function publish($id_resort, array $data) {
        $id_resort = (int)$id_resort;
        $today     = gmdate('Y-m-d');

        // One report per resort per day
        if ($this->get_todays_report($id_resort)) {
            return ['ok' => false, 'rep_bonus' => 0, 'already_published' => true];
        }

        $conditions = in_array($data['conditions'], ['poor', 'fair', 'good', 'excellent'], TRUE)
            ? $data['conditions']
            : 'fair';

        $rep_bonus = self::REP_BONUS[$conditions];

        $row = [
            'id_resort'      => $id_resort,
            'report_date'    => $today,
            'snow_depth_cm'  => max(0, min(500, (int)($data['snow_depth_cm'] ?? 0))),
            'fresh_snow_cm'  => max(0, min(200, (int)($data['fresh_snow_cm'] ?? 0))),
            'conditions'     => $conditions,
            'piste_coverage' => max(0, min(100, (int)($data['piste_coverage'] ?? 0))),
            'note'           => isset($data['note'])
                ? substr(strip_tags((string)$data['note']), 0, 500)
                : NULL,
            'rep_bonus'      => $rep_bonus,
            'created_at'     => gmdate('Y-m-d H:i:s'),
        ];

        $this->db->trans_start();

        $this->db->insert('game_snow_reports', $row);

        // Award reputation bonus to the resort
        if ($rep_bonus > 0) {
            $this->db->set('reputation', 'reputation + ' . $rep_bonus, FALSE);
            $this->db->where('id_resort', $id_resort);
            $this->db->limit(1);
            $this->db->update('game_resorts');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['ok' => false, 'rep_bonus' => 0, 'already_published' => false];
        }

        return ['ok' => true, 'rep_bonus' => $rep_bonus, 'already_published' => false];
    }
}
