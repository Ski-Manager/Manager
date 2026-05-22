<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Grooming Minigame
 *
 * Seeds the game_minigames catalogue with a fourth mini-game:
 *   4. Grooming Rush – click-to-groom skill game
 */
class Migration_Add_grooming_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $exists = $this->db->where('name_english', 'Grooming Rush')->get('game_minigames');
            if ($exists->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Grooming Rush',
                    'name_french'           => 'Ruée au Damage',
                    'description_english'   => 'Race against the clock to groom as many slope sections as possible! Click the ungroomed tiles before time runs out and earn bonus cash and reputation for a well-prepared mountain.',
                    'description_french'    => 'Battez la montre pour damer le plus de sections de piste possible ! Cliquez sur les cases non damées avant la fin du temps et gagnez du cash et de la réputation pour une montagne bien préparée.',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 2000,
                    'max_reward_reputation' => 5,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'grooming',
                    'active'                => 1,
                    'game_order'            => 4,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('name_english', 'Grooming Rush')->delete('game_minigames');
        }
    }
}
