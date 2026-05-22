<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add game_climate_change table for the Climate Change System.
 *
 * Each resort has one row in this table that accumulates climate_level over
 * seasons.  The level drives four gameplay penalties:
 *   - winter_snow_penalty  : natural snowfall reduced (cm) each weather event
 *   - snowmaking_cost_mult : multiplier applied to daily cannon / trail costs
 *   - glacier_loss         : extra daily slope-condition degradation (points)
 *   - season_length_penalty: days removed from each season
 *
 * SQL equivalent:
 *   CREATE TABLE `game_climate_change` (
 *     `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *     `id_resort`            INT UNSIGNED NOT NULL,
 *     `climate_level`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
 *     `snowmaking_invest`    TINYINT(1) NOT NULL DEFAULT 0,
 *     `altitude_invest`      TINYINT(1) NOT NULL DEFAULT 0,
 *     `diversify_invest`     TINYINT(1) NOT NULL DEFAULT 0,
 *     `updated_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *     UNIQUE KEY `uq_resort` (`id_resort`)
 *   );
 */
class Migration_Add_Climate_Change extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
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
            'climate_level' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => TRUE,
                'null'       => FALSE,
                'default'    => 0,
            ],
            // Adaptation investment flags (player can invest to mitigate effects)
            'snowmaking_invest' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'altitude_invest' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'diversify_invest' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => FALSE,
                'default' => '1970-01-01 00:00:00',
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('id_resort');
        $this->dbforge->create_table('game_climate_change', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_climate_change', TRUE);
    }
}
