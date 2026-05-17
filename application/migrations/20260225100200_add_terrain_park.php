<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Terrain Park as a new slope type
 *
 * Inserts a new row in the game_slope_types table for Terrain Park (id 6).
 * A terrain park is a dedicated freestyle area featuring jumps, rails,
 * boxes and other obstacle features for skiers and snowboarders.
 */
class Migration_Add_terrain_park extends CI_Migration {

    public function up() {
        $exists = $this->db
            ->where('id_slope_types', 6)
            ->count_all_results('game_slope_types');

        if ($exists === 0) {
            $this->db->insert('game_slope_types', [
                'id_slope_types' => 6,
                'name_english'   => 'Terrain Park',
                'name_french'    => 'Terrain Park',
            ]);
        }
    }

    public function down() {
        $this->db->where('id_slope_types', 6)->delete('game_slope_types');
    }
}
