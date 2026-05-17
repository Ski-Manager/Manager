<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add snowmaking_schedule column to game_resorts
 *
 * Stores a 7-bit bitmask (0–127) that controls which nights of the week
 * snowmaking cannons are allowed to run:
 *   Bit 0 = Monday, Bit 1 = Tuesday, …, Bit 6 = Sunday
 *   127 (all bits set) = every night (default)
 *   0  = snowmaking disabled every night
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `snowmaking_schedule` TINYINT UNSIGNED NOT NULL DEFAULT 127;
 */
class Migration_Add_snowmaking_schedule extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('snowmaking_schedule', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', [
                'snowmaking_schedule' => [
                    'type'       => 'TINYINT',
                    'constraint' => 3,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 127,
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('snowmaking_schedule', 'game_resorts')) {
            $this->dbforge->drop_column('game_resorts', 'snowmaking_schedule');
        }
    }
}
