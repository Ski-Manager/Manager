<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create game_trail_snowmaking table
 *
 * Adds per-trail snowmaking equipment. Players can purchase a lance gun,
 * fan gun, or snow factory from one of several brands for each of their
 * built slopes. Equipment adds snow to the resort's snow level each night.
 *
 * SQL equivalent:
 *   CREATE TABLE `game_trail_snowmaking` (
 *     `id_trail_snowmaking` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `id_resort`           INT UNSIGNED NOT NULL,
 *     `id_created_slopes`   INT UNSIGNED NOT NULL,
 *     `equipment_type`      VARCHAR(10)  NOT NULL DEFAULT 'lance',
 *     `brand`               VARCHAR(32) NOT NULL DEFAULT 'demaclenko',
 *     `is_active`           TINYINT(1)  NOT NULL DEFAULT 1,
 *     `snow_output`         SMALLINT    NOT NULL DEFAULT 0,
 *     `daily_cost`          INT         NOT NULL DEFAULT 0,
 *     `purchased_at`        DATETIME    NOT NULL,
 *     UNIQUE KEY `uq_trail_equip` (`id_resort`, `id_created_slopes`),
 *     INDEX `idx_resort` (`id_resort`)
 *   );
 */
class Migration_Add_trail_snowmaking extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_trail_snowmaking' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'id_created_slopes' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'equipment_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => FALSE,
                'default'    => 'lance',
            ],
            'brand' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => FALSE,
                'default'    => 'demaclenko',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 1,
            ],
            'snow_output' => [
                'type'    => 'SMALLINT',
                'null'    => FALSE,
                'default' => 0,
            ],
            'daily_cost' => [
                'type'    => 'INT',
                'null'    => FALSE,
                'default' => 0,
            ],
            'purchased_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
            ],
        ]);

        $this->dbforge->add_key('id_trail_snowmaking', TRUE);
        $this->dbforge->create_table('game_trail_snowmaking');

        // Unique constraint so one slot of equipment per trail per resort
        $this->db->query('ALTER TABLE `game_trail_snowmaking` ADD UNIQUE KEY `uq_trail_equip` (`id_resort`, `id_created_slopes`)');
    }

    public function down() {
        $this->dbforge->drop_table('game_trail_snowmaking', TRUE);
    }
}
