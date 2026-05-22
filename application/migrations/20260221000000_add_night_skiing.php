<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add night_skiing column to game_resorts table
 *
 * Run this migration to enable the night skiing feature.
 * SQL equivalent:
 *   ALTER TABLE `game_resorts` ADD COLUMN `night_skiing` TINYINT(1) NOT NULL DEFAULT 0;
 */
class Migration_Add_night_skiing extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'night_skiing' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'snow_level',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'night_skiing');
    }
}
