<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Slalom Race Minigame
 *
 * Seeds the game_minigames catalogue with the Slalom Race mini-game:
 *   Slalom Race – reaction / direction game
 *   8 gates appear one by one; each gate goes left or right.
 *   Player clicks the matching button. Win threshold: 60 % correct.
 */
class Migration_Add_slalom_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'slalom')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Slalom Race',
                    'name_french'           => 'Course de Slalom',
                    'description_english'   => 'Charge through 8 slalom gates! Each gate flashes left or right — react quickly and click the correct side. Clear enough gates to earn bonus cash and reputation for your resort!',
                    'description_french'    => 'Franchissez 8 portes de slalom ! Chaque porte indique gauche ou droite — réagissez vite et cliquez du bon côté. Passez assez de portes pour gagner du cash et de la réputation !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 3500,
                    'max_reward_reputation' => 5,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'slalom',
                    'active'                => 1,
                    'game_order'            => 9,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'slalom')->delete('game_minigames');
        }
    }
}
