<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Emergency & Rescue System
 *
 * Creates game_resort_emergency to store per-resort emergency settings:
 *   - rescue_team_level  : 0=none, 1=basic, 2=standard, 3=advanced
 *   - medical_stations   : 0=none, 1=basic, 2=standard, 3=advanced
 *   - insurance_enabled  : 0=off, 1=on
 */
class Migration_Add_emergency_rescue extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_emergency` (
                `id_emergency`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`           INT(11) UNSIGNED NOT NULL,
                `rescue_team_level`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `medical_stations`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `insurance_enabled`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_emergency`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_emergency`');
    }
}
