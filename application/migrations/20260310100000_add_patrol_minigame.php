<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Ski Patrol Rush Minigame
 *
 * Seeds the game_minigames catalogue with the Ski Patrol Rush mini-game:
 *   Ski Patrol Rush – click injured skiers before they vanish.
 *   12 injured skiers appear at random positions over 15 seconds.
 *   Win threshold: 40 % rescued.
 */
class Migration_Add_patrol_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'patrol')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Ski Patrol Rush',
                    'name_french'           => 'Patrouille de Ski',
                    'description_english'   => 'Injured skiers need your help! Click each fallen skier before they disappear from the slope. The more rescues you complete, the more your resort\'s reputation soars!',
                    'description_french'    => 'Des skieurs blessés ont besoin d\'aide ! Cliquez sur chaque skieur tombé avant qu\'il ne disparaisse de la piste. Plus vous effectuez de sauvetages, plus la réputation de votre station grimpe !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 0,
                    'max_reward_reputation' => 15,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'patrol',
                    'active'                => 1,
                    'game_order'            => 10,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'patrol')->delete('game_minigames');
        }
    }
}
