<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Snowmaking Challenge minigame
 *
 * Inserts a fourth minigame entry into game_minigames:
 *   Snowmaking Challenge – precision timing game where the player
 *   fires snow cannons by clicking when a moving needle hits the
 *   optimal zone.
 */
class Migration_Add_snowmaking_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'snowmaking')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Snowmaking Challenge',
                    'name_french'           => 'Défi d\'Enneigement',
                    'description_english'   => 'Take control of the snow cannons! Fire at the perfect moment to build up snow coverage across your slopes. Precision timing earns you bonus cash and reputation.',
                    'description_french'    => 'Prenez le contrôle des canons à neige ! Tirez au bon moment pour augmenter l\'enneigement de vos pistes. Une bonne précision vous rapporte du cash et de la réputation supplémentaires.',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 4000,
                    'max_reward_reputation' => 5,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'snowmaking',
                    'active'                => 1,
                    'game_order'            => 4,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'snowmaking')->delete('game_minigames');
        }
    }
}
