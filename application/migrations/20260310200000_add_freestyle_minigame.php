<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Freestyle Jump Minigame
 *
 * Seeds the game_minigames catalogue with the Freestyle Jump mini-game:
 *   Freestyle Jump – precision timing game.
 *   A progress bar fills from 0 → 100 over ~2.5 s.
 *   Player must click "Jump!" when the bar is inside the green zone (65–90 %).
 *   Win threshold: score >= 50.
 */
class Migration_Add_freestyle_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'freestyle')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Freestyle Jump',
                    'name_french'           => 'Saut Freestyle',
                    'description_english'   => 'Race down the slope and time your jump perfectly! Watch the ramp meter fill up and press Jump at exactly the right moment to land in the target zone. Perfect timing earns you the biggest cash reward!',
                    'description_french'    => 'Dévalez la pente et chronométrez votre saut parfaitement ! Regardez le compteur de rampe se remplir et appuyez sur Sauter au bon moment pour atterrir dans la zone cible. Un timing parfait vous rapporte la plus grosse récompense !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 4000,
                    'max_reward_reputation' => 0,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'freestyle',
                    'active'                => 1,
                    'game_order'            => 11,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'freestyle')->delete('game_minigames');
        }
    }
}
