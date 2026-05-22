<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Lift Line Manager Minigame
 *
 * Seeds the game_minigames catalogue with the Lift Line Manager mini-game:
 *   Lift Line Manager – Simon-says memory sequence game
 */
class Migration_Add_liftline_minigame extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_minigames')) {
            $existing = $this->db->where('minigame_type', 'liftline')->get('game_minigames');
            if ($existing->num_rows() === 0) {
                $this->db->insert('game_minigames', [
                    'name_english'          => 'Lift Line Manager',
                    'name_french'           => 'Gestion de la File',
                    'description_english'   => 'Memorise the sequence of coloured ski passes and repeat it! Each round adds one more colour. How long can you keep up? A sharp memory earns you reputation!',
                    'description_french'    => 'Mémorisez la séquence de forfaits colorés et reproduisez-la ! Chaque manche ajoute une couleur. Jusqu\'où pouvez-vous aller ? Une bonne mémoire vous rapporte de la réputation !',
                    'play_cost'             => 0,
                    'max_reward_cash'       => 0,
                    'max_reward_reputation' => 12,
                    'cooldown_hours'        => 24,
                    'minigame_type'         => 'liftline',
                    'active'                => 1,
                    'game_order'            => 7,
                ]);
            }
        }
    }

    public function down() {
        if ($this->db->table_exists('game_minigames')) {
            $this->db->where('minigame_type', 'liftline')->delete('game_minigames');
        }
    }
}
