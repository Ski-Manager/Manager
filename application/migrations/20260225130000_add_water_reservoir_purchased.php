<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add water_reservoir_purchased flag to game_resorts
 *
 * Adds a `water_reservoir_purchased` TINYINT(1) column to game_resorts.
 * When 0 (default), snowmaking equipment is blocked until the player
 * purchases a water reservoir from the Trail Snowmaking page.
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `water_reservoir_purchased` TINYINT(1) NOT NULL DEFAULT 0
 *     AFTER `water_reservoir`;
 */
class Migration_Add_water_reservoir_purchased extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('water_reservoir_purchased', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', [
                'water_reservoir_purchased' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                    'after'      => 'water_reservoir',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('water_reservoir_purchased', 'game_resorts')) {
            $this->dbforge->drop_column('game_resorts', 'water_reservoir_purchased');
        }
    }
}
