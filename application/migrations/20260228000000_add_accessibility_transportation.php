<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Accessibility & Transportation
 *
 * Creates game_resort_transportation to store per-resort transport settings:
 *   - shuttle_level     : 0 = none, 1 = bus, 2 = tram, 3 = premium shuttle
 *   - ski_storage       : 0 = disabled, 1 = enabled
 *   - gondola_link      : 0 = disabled, 1 = enabled
 */
class Migration_Add_accessibility_transportation extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_transportation` (
                `id_transportation`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`          INT(11) UNSIGNED NOT NULL,
                `shuttle_level`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `ski_storage`        TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `gondola_link`       TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_transportation`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_transportation`');
    }
}
