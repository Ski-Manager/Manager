<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Guest_skill_model
 *
 * Manages the guest skill-level distribution stored in
 * game_guest_skill_progression (one row per resort).
 *
 * Skill levels:
 *   beginner_pct     – percentage of guests that are beginners   (0–100)
 *   intermediate_pct – percentage of guests that are intermediate (0–100)
 *   advanced_pct     – percentage of guests that are advanced     (0–100)
 *
 * The three values always sum to 100.
 */
class Guest_skill_model extends CI_Model {

    /**
     * get_or_init_DB   Returns the skill record for a resort.
     *                  If no record exists yet, inserts the default row
     *                  (100 % beginners) and returns it.
     *
     * @param  int $id_resort
     * @return object  Row with beginner_pct, intermediate_pct, advanced_pct, seasons_played
     */
    public function get_or_init_DB($id_resort) {
        $row = $this->db
            ->where('id_resort', (int)$id_resort)
            ->get('game_guest_skill_progression')
            ->row();

        if ($row === null) {
            $this->db->insert('game_guest_skill_progression', [
                'id_resort'        => (int)$id_resort,
                'beginner_pct'     => 100,
                'intermediate_pct' => 0,
                'advanced_pct'     => 0,
                'seasons_played'   => 0,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $row = $this->db
                ->where('id_resort', (int)$id_resort)
                ->get('game_guest_skill_progression')
                ->row();
        }

        return $row;
    }

    /**
     * update_skills_DB     Persists an updated skill distribution.
     *
     * @param  int $id_resort
     * @param  int $beginner_pct      0–100
     * @param  int $intermediate_pct  0–100
     * @param  int $advanced_pct      0–100
     * @param  int $seasons_played
     * @return bool
     */
    public function update_skills_DB($id_resort, $beginner_pct, $intermediate_pct, $advanced_pct, $seasons_played) {
        $this->db->trans_start();
        $this->db->set('beginner_pct',     (int)$beginner_pct);
        $this->db->set('intermediate_pct', (int)$intermediate_pct);
        $this->db->set('advanced_pct',     (int)$advanced_pct);
        $this->db->set('seasons_played',   (int)$seasons_played);
        $this->db->set('updated_at',       date('Y-m-d H:i:s'));
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_guest_skill_progression');
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_revenue_multiplier   Returns the revenue multiplier for the resort
     *                          based on its guest skill distribution.
     *
     * multiplier = (beginner_pct/100 * 1.0)
     *            + (intermediate_pct/100 * (1.0 + GUEST_SKILL_INTERMEDIATE_REVENUE_BONUS))
     *            + (advanced_pct/100     * (1.0 + GUEST_SKILL_ADVANCED_REVENUE_BONUS))
     *
     * @param  int $id_resort
     * @return float  Always >= 1.0
     */
    public function get_revenue_multiplier($id_resort) {
        $row = $this->get_or_init_DB($id_resort);

        $beg  = (int)$row->beginner_pct;
        $inter = (int)$row->intermediate_pct;
        $adv  = (int)$row->advanced_pct;

        $multiplier = ($beg / 100)
            + ($inter / 100) * (1.0 + GUEST_SKILL_INTERMEDIATE_REVENUE_BONUS)
            + ($adv  / 100) * (1.0 + GUEST_SKILL_ADVANCED_REVENUE_BONUS);

        return max(1.0, round($multiplier, 4));
    }
}
