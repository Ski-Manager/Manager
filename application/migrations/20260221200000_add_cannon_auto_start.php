<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add cannon_auto_start column to game_resorts table
 *
 * Adds a player-configurable minimum snow level for snow cannon auto-start.
 * When the resort's snow level drops below this value during the nightly job,
 * all inactive cannons are automatically started.
 * 0 = disabled (cannons are never auto-started).
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts` ADD COLUMN `cannon_auto_start` SMALLINT NOT NULL DEFAULT 0;
 */
class Migration_Add_cannon_auto_start extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'cannon_auto_start' => [
                'type'    => 'SMALLINT',
                'null'    => FALSE,
                'default' => 0,
                'after'   => 'cannon_target_snow',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'cannon_auto_start');
    }
}
