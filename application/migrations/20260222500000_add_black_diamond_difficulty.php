<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Black Diamond / Extreme Zone difficulty to game_difficulties
 *
 * Inserts difficulty level 5 "Black Diamond" (Double Noire in French) with:
 *   - Higher injury risk (applied via BLACK_DIAMOND_INJURY_MULTIPLIER constant)
 *   - Higher reputation reward (applied via BLACK_DIAMOND_REPUTATION_PER_SLOPE constant)
 *   - Attracts expert guests (visitor bonus via BLACK_DIAMOND_VISITOR_BONUS constant)
 *
 * SQL equivalent:
 *   INSERT INTO `game_difficulties` (`id_difficulty`, `name_english`, `name_french`)
 *   VALUES (5, 'Black Diamond', 'Double Noire');
 */
class Migration_Add_black_diamond_difficulty extends CI_Migration {

    public function up() {
        $exists = $this->db
            ->from('game_difficulties')
            ->where('id_difficulty', BLACK_DIAMOND_DIFFICULTY_ID)
            ->count_all_results();

        if ($exists === 0) {
            $this->db->insert('game_difficulties', [
                'id_difficulty' => BLACK_DIAMOND_DIFFICULTY_ID,
                'name_english'  => 'Black Diamond',
                'name_french'   => 'Double Noire',
            ]);
        }
    }

    public function down() {
        $this->db
            ->where('id_difficulty', BLACK_DIAMOND_DIFFICULTY_ID)
            ->delete('game_difficulties');
    }
}
