<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Snowboard Trick Minigame
 *
 * Seeds the game_minigames catalogue with the Snowboard Trick mini-game:
 *   Snowboard Trick – multi-attempt precision timing game.
 *   A progress bar fills from 0 → 100 over ~2.5 s across 3 attempts.
 *   Player clicks "Trick!" when the bar is inside the green zone (60–85 %).
 *   Win threshold: score >= 50 (land at least 2 of 3 tricks in the zone).
 */
class Migration_Add_snowboard_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'snowboard')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Snowboard Trick',
                    'name_french'           => 'Figure de Snowboard',
                    'description_english'   => 'Hit the kicker and time your trick perfectly! Watch the ramp meter fill up and press Trick at the right moment across three attempts. Land the most tricks in the target zone for the best cash reward!',
                    'description_french'    => 'Prenez le tremplin et chronométrez votre figure ! Regardez le compteur de rampe se remplir et appuyez sur Figure au bon moment lors de trois tentatives. Réussissez le plus de figures dans la zone cible pour la meilleure récompense !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 3000,
                    'max_reward_reputation' => 0,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'snowboard',
                    'active'                => 1,
                    'game_order'            => 13,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'snowboard')->delete('game_minigames');
        }
    }
}
