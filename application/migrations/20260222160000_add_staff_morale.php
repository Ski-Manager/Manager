<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add morale and on_strike columns to game_hired_staff table
 *
 * Run this migration to enable the Staff Morale & Strikes feature.
 * SQL equivalent:
 *   ALTER TABLE `game_hired_staff`
 *     ADD COLUMN `morale` TINYINT UNSIGNED NOT NULL DEFAULT 75,
 *     ADD COLUMN `on_strike` TINYINT(1) NOT NULL DEFAULT 0;
 */
class Migration_Add_staff_morale extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_hired_staff', [
            'morale' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => TRUE,
                'null'       => FALSE,
                'default'    => 75,
                'after'      => 'type_item_assigned',
            ],
            'on_strike' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'morale',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_hired_staff', 'on_strike');
        $this->dbforge->drop_column('game_hired_staff', 'morale');
    }
}
