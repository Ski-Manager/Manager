<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Biathlon Minigame
 *
 * Seeds the game_minigames catalogue with the Biathlon mini-game:
 *   Biathlon – moving-target shooting game.
 *   An oscillating marker moves left-right across the range.
 *   Player clicks "Shoot!" when the marker is inside the centre target zone.
 *   5 shots total. Win threshold: 40 % hits (2/5).
 */
class Migration_Add_biathlon_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'biathlon')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Biathlon',
                    'name_french'           => 'Biathlon',
                    'description_english'   => 'Grab your rifle and take aim! A target marker oscillates across the range — click Shoot at exactly the right moment to hit the centre zone. Land enough shots to earn bonus reputation for your resort!',
                    'description_french'    => 'Saisissez votre carabine et visez ! Un marqueur se déplace de gauche à droite sur la cible — cliquez sur Tirer au bon moment pour toucher la zone centrale. Réussissez suffisamment de tirs pour gagner de la réputation !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 0,
                    'max_reward_reputation' => 10,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'biathlon',
                    'active'                => 1,
                    'game_order'            => 12,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'biathlon')->delete('game_minigames');
        }
    }
}
