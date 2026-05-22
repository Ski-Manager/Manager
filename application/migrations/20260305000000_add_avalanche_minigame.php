<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Avalanche Escape Minigame
 *
 * Seeds the game_minigames catalogue with the Avalanche Escape mini-game:
 *   Avalanche Escape – dodge falling boulders game
 */
class Migration_Add_avalanche_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'avalanche')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Avalanche Escape',
                    'name_french'           => 'Fuite d\'Avalanche',
                    'description_english'   => 'An avalanche is coming! Dodge falling snow boulders by moving left and right. The more you dodge, the higher your score. Quick reflexes earn you bonus cash and reputation!',
                    'description_french'    => 'Une avalanche approche ! Esquivez les blocs de neige en vous déplaçant à gauche et à droite. Plus vous esquivez, plus votre score augmente. De bons réflexes vous rapportent du cash et de la réputation !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 3500,
                    'max_reward_reputation' => 8,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'avalanche',
                    'active'                => 1,
                    'game_order'            => 6,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'avalanche')->delete('game_minigames');
        }
    }
}
