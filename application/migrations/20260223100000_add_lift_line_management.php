<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Lift Line Management
 *
 * Creates game_resort_lift_lines to store per-resort lift queue settings:
 *   - queue_tolerance_minutes : guests leave if they wait longer than this
 *   - vip_fastpass_enabled    : 0 = off, 1 = on
 *   - vip_fastpass_price      : daily price charged per VIP guest (€)
 */
class Migration_Add_lift_line_management extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_lift_lines` (
                `id_lift_line`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`               INT(11) UNSIGNED NOT NULL,
                `queue_tolerance_minutes` INT(3)  UNSIGNED NOT NULL DEFAULT 20,
                `vip_fastpass_enabled`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `vip_fastpass_price`      INT(4)  UNSIGNED NOT NULL DEFAULT 30,
                PRIMARY KEY (`id_lift_line`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_lift_lines`');
    }
}
