<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add per-trail night skiing settings and resort-level night settings
 *
 * Creates game_night_skiing_trails to hold per-slope lighting configuration.
 * Adds night_skiing_start_hour, night_skiing_end_hour, and night_skiing_ticket_price
 * columns to game_resorts.
 *
 * SQL equivalent:
 *   CREATE TABLE `game_night_skiing_trails` (
 *     `id_night_skiing_trail` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `id_created_slope`      INT UNSIGNED NOT NULL,
 *     `id_resort`             INT UNSIGNED NOT NULL,
 *     `night_skiing_enabled`  TINYINT(1)   NOT NULL DEFAULT 0,
 *     `light_type`            VARCHAR(20)  NOT NULL DEFAULT 'led',
 *     `brightness`            TINYINT(1)   NOT NULL DEFAULT 3,
 *     `pole_spacing`          TINYINT(2)   NOT NULL DEFAULT 25,
 *     UNIQUE KEY `uq_trail_resort` (`id_created_slope`, `id_resort`)
 *   );
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `night_skiing_start_hour`   TINYINT(2) NOT NULL DEFAULT 18,
 *     ADD COLUMN `night_skiing_end_hour`     TINYINT(2) NOT NULL DEFAULT 22,
 *     ADD COLUMN `night_skiing_ticket_price` SMALLINT   NOT NULL DEFAULT 0;
 */
class Migration_Add_night_skiing_trails extends CI_Migration {

    public function up() {
        // Per-trail night skiing settings table
        $this->dbforge->add_field([
            'id_night_skiing_trail' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
                'null'           => FALSE,
            ],
            'id_created_slope' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'id_resort' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'night_skiing_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'light_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => FALSE,
                'default'    => 'led',
            ],
            'brightness' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => FALSE,
                'default'    => 3,
            ],
            'pole_spacing' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => FALSE,
                'default'    => 25,
            ],
        ]);
        $this->dbforge->add_key('id_night_skiing_trail', TRUE);
        $this->dbforge->create_table('game_night_skiing_trails');

        // Unique constraint on slope + resort (one settings row per slope per resort)
        $this->db->query('ALTER TABLE `game_night_skiing_trails` ADD UNIQUE KEY `uq_trail_resort` (`id_created_slope`, `id_resort`)');

        // Resort-level night settings: operating hours and night ticket price
        $this->dbforge->add_column('game_resorts', [
            'night_skiing_start_hour' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => FALSE,
                'default'    => 18,
                'after'      => 'night_skiing',
            ],
            'night_skiing_end_hour' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => FALSE,
                'default'    => 22,
                'after'      => 'night_skiing_start_hour',
            ],
            'night_skiing_ticket_price' => [
                'type'       => 'SMALLINT',
                'constraint' => 5,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_end_hour',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_table('game_night_skiing_trails', TRUE);
        $this->dbforge->drop_column('game_resorts', 'night_skiing_start_hour');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_end_hour');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_ticket_price');
    }
}
