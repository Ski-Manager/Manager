<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Ice Breaker Minigame
 *
 * Seeds the game_minigames catalogue with the Ice Breaker mini-game:
 *   Ice Breaker – rapid-click game
 */
class Migration_Add_icebreaker_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'icebreaker')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Ice Breaker',
                    'name_french'           => 'Brise-Glace',
                    'description_english'   => 'Smash through layers of ice as fast as you can! Click rapidly to chip away at the ice before time runs out. The more you break, the more cash you earn!',
                    'description_french'    => 'Brisez les couches de glace le plus vite possible ! Cliquez rapidement pour entamer la glace avant la fin du temps. Plus vous brisez, plus vous gagnez de cash !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 2500,
                    'max_reward_reputation' => 0,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'icebreaker',
                    'active'                => 1,
                    'game_order'            => 8,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'icebreaker')->delete('game_minigames');
        }
    }
}
