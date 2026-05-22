<?php
/**
 * Guest_ai_model
 *
 * Handles database access for the data-driven guest AI feature.
 * Scores are stored in game_guest_ai, one row per (resort, slope) pair.
 */
class Guest_ai_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * get_scores_for_resort    Fetch all guest AI rows for a resort, joining
     *                          slope name from game_created_slopes.
     *
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function get_scores_for_resort($id_resort) {
        return $this->db
            ->select('ga.*, gcs.custom_name AS slope_custom_name, gs.id_difficulty')
            ->from('game_guest_ai ga')
            ->join('game_created_slopes gcs', 'gcs.id_created_slopes = ga.id_created_slope', 'left')
            ->join('game_slopes gs', 'gs.id_slope = gcs.id_slope', 'left')
            ->where('ga.id_resort', $id_resort)
            ->order_by('ga.total_score', 'DESC')
            ->get();
    }

    /**
     * upsert_score     Insert or update a single slope AI score row.
     *
     * @param array $data  Keys: id_resort, id_created_slope, slope_name,
     *                     score_difficulty, score_snow_quality, score_crowd,
     *                     score_lift_speed, score_ticket_price,
     *                     total_score, daily_visitors, updated_at
     * @return bool
     */
    public function upsert_score(array $data): bool {
        $sql = "INSERT INTO game_guest_ai
                    (id_resort, id_created_slope, slope_name,
                     score_difficulty, score_snow_quality, score_crowd,
                     score_lift_speed, score_ticket_price,
                     total_score, daily_visitors, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    slope_name         = VALUES(slope_name),
                    score_difficulty   = VALUES(score_difficulty),
                    score_snow_quality = VALUES(score_snow_quality),
                    score_crowd        = VALUES(score_crowd),
                    score_lift_speed   = VALUES(score_lift_speed),
                    score_ticket_price = VALUES(score_ticket_price),
                    total_score        = VALUES(total_score),
                    daily_visitors     = VALUES(daily_visitors),
                    updated_at         = VALUES(updated_at)";

        return $this->db->query($sql, [
            $data['id_resort'],
            $data['id_created_slope'],
            $data['slope_name'],
            $data['score_difficulty'],
            $data['score_snow_quality'],
            $data['score_crowd'],
            $data['score_lift_speed'],
            $data['score_ticket_price'],
            $data['total_score'],
            $data['daily_visitors'],
            $data['updated_at'],
        ]);
    }
}
