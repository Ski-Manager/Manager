<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add cannon_target_snow column to game_resorts table
 *
 * Adds a player-configurable snow level target for snow cannons.
 * When the resort's snow level reaches this target, cannons stop adding
 * snow during the nightly job. 0 = no target (always add snow up to MAX_SNOW_LEVEL).
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts` ADD COLUMN `cannon_target_snow` SMALLINT NOT NULL DEFAULT 0;
 */
class Migration_Add_cannon_target_snow extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'cannon_target_snow' => [
                'type'       => 'SMALLINT',
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'cannon_target_snow');
    }
}
