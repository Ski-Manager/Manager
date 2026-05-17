<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Early-Bird columns to game_resort_season_passes
 *
 * Adds:
 *   - early_bird_enabled     : 0 = disabled, 1 = enabled
 *   - early_bird_discount_pct: discount percentage applied during early-bird period
 */
class Migration_Add_early_bird_to_season_passes extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('early_bird_enabled', 'game_resort_season_passes')) {
            $this->dbforge->add_column('game_resort_season_passes', [
                'early_bird_enabled' => [
                    'type'     => 'TINYINT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                    'default'  => 0,
                    'after'    => 'current_season',
                ],
            ]);
        }
        if (!$this->db->field_exists('early_bird_discount_pct', 'game_resort_season_passes')) {
            $this->dbforge->add_column('game_resort_season_passes', [
                'early_bird_discount_pct' => [
                    'type'     => 'TINYINT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                    'default'  => 10,
                    'after'    => 'early_bird_enabled',
                ],
            ]);
        }
    }

    public function down() {
        $this->dbforge->drop_column('game_resort_season_passes', 'early_bird_discount_pct');
        $this->dbforge->drop_column('game_resort_season_passes', 'early_bird_enabled');
    }
}
