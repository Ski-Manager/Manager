<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add entertainment and safety level columns to game_resorts
 *
 * night_skiing_entertainment  VARCHAR(20) DEFAULT 'none'  – nightly entertainment option
 * night_skiing_safety_level   TINYINT(1)  DEFAULT 1       – safety investment level (1–3)
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `night_skiing_entertainment` VARCHAR(20) NOT NULL DEFAULT 'none',
 *     ADD COLUMN `night_skiing_safety_level`  TINYINT(1)  NOT NULL DEFAULT 1;
 */
class Migration_Add_night_skiing_features extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'night_skiing_entertainment' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => FALSE,
                'default'    => 'none',
                'after'      => 'night_skiing_ticket_price',
            ],
            'night_skiing_safety_level' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 1,
                'after'      => 'night_skiing_entertainment',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'night_skiing_entertainment');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_safety_level');
    }
}
