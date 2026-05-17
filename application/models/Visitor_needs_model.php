<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Visitor_needs_model
 *
 * Manages the per-resort visitor needs scores stored in game_visitor_needs.
 * Four need dimensions are tracked (0–100 each):
 *   hunger_score  – how well fed visitors feel (restaurants)
 *   fatigue_score – how rested visitors feel (medical, hotel)
 *   warmth_score  – how warm visitors feel (luxury buildings, temperature)
 *   fun_score     – how entertained visitors feel (leisure, open slopes)
 *
 * needs_satisfaction is the equal-weight average of all four scores.
 */
class Visitor_needs_model extends CI_Model {

    /**
     * get_or_init_DB   Returns the needs record for a resort.
     *                  Inserts a default row (all scores = 50) if none exists.
     *
     * @param  int $id_resort
     * @return object
     */
    public function get_or_init_DB(int $id_resort): object {
        $row = $this->db
            ->where('id_resort', $id_resort)
            ->get('game_visitor_needs')
            ->row();

        if ($row === null) {
            $this->db->insert('game_visitor_needs', [
                'id_resort'          => $id_resort,
                'hunger_score'       => 50,
                'fatigue_score'      => 50,
                'warmth_score'       => 50,
                'fun_score'          => 50,
                'needs_satisfaction' => 50,
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
            $row = $this->db
                ->where('id_resort', $id_resort)
                ->get('game_visitor_needs')
                ->row();
        }

        return $row;
    }

    /**
     * upsert   Insert or update the needs record for a resort.
     *
     * @param  int   $id_resort
     * @param  float $hunger_score
     * @param  float $fatigue_score
     * @param  float $warmth_score
     * @param  float $fun_score
     * @param  float $needs_satisfaction
     * @return bool
     */
    public function upsert(
        int   $id_resort,
        float $hunger_score,
        float $fatigue_score,
        float $warmth_score,
        float $fun_score,
        float $needs_satisfaction
    ): bool {
        $sql = "INSERT INTO game_visitor_needs
                    (id_resort, hunger_score, fatigue_score, warmth_score,
                     fun_score, needs_satisfaction, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    hunger_score       = VALUES(hunger_score),
                    fatigue_score      = VALUES(fatigue_score),
                    warmth_score       = VALUES(warmth_score),
                    fun_score          = VALUES(fun_score),
                    needs_satisfaction = VALUES(needs_satisfaction),
                    updated_at         = VALUES(updated_at)";

        return $this->db->query($sql, [
            $id_resort,
            $hunger_score,
            $fatigue_score,
            $warmth_score,
            $fun_score,
            $needs_satisfaction,
            date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * get_revenue_multiplier   Returns a revenue bonus multiplier based on
     *                          overall needs_satisfaction (0–100).
     *
     * satisfaction 50 → 1.00 (no bonus)
     * satisfaction 100 → 1.0 + VISITOR_NEEDS_REVENUE_BONUS_MAX
     * satisfaction 0   → 1.0 - VISITOR_NEEDS_REVENUE_BONUS_MAX
     *
     * @param  int $id_resort
     * @return float
     */
    public function get_revenue_multiplier(int $id_resort): float {
        $row = $this->get_or_init_DB($id_resort);
        $sat = (float)$row->needs_satisfaction;
        // Map 0–100 → (1 - MAX) to (1 + MAX)
        $bonus = (($sat - 50) / 50) * VISITOR_NEEDS_REVENUE_BONUS_MAX;
        return max(0.5, round(1.0 + $bonus, 4));
    }
}
