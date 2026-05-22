<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Crowding System
 *
 * Creates game_resort_crowding to store per-resort crowding management settings:
 *   - capacity_limit       : maximum daily visitors the resort targets
 *   - timed_entry_enabled  : 0 = off, 1 = on (caps visitors and earns a rep bonus)
 *   - crowd_alert_threshold: % of capacity at which crowding becomes a problem
 */
class Migration_Add_crowding_system extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_crowding` (
                `id_crowding`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`              INT(11) UNSIGNED NOT NULL,
                `capacity_limit`         INT(11) UNSIGNED NOT NULL DEFAULT 500,
                `timed_entry_enabled`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `crowd_alert_threshold`  INT(3)  UNSIGNED NOT NULL DEFAULT 80,
                PRIMARY KEY (`id_crowding`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_crowding`');
    }
}
