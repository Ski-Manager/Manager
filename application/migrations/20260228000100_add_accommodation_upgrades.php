<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Accommodation Upgrades
 *
 * Creates game_resort_accommodations to store per-resort accommodation type:
 *   - accommodation_type : none | cabin | lodge | luxury_hotel
 *   - is_enabled         : 0 = inactive, 1 = active
 */
class Migration_Add_accommodation_upgrades extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_accommodations` (
                `id_accommodation`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`          INT(11) UNSIGNED NOT NULL,
                `accommodation_type` ENUM('none','cabin','lodge','luxury_hotel') NOT NULL DEFAULT 'none',
                `is_enabled`         TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_accommodation`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_accommodations`');
    }
}
