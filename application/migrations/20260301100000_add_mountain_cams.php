<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Mountain Cams (Webcams)
 *
 * Creates game_resort_mountain_cams to store per-resort mountain webcam settings:
 *   - is_enabled  : 0 = cams off, 1 = cams on
 *   - num_cams    : number of cameras installed (1–10)
 *   - cam_quality : quality level (1=Standard, 2=HD, 3=4K)
 */
class Migration_Add_mountain_cams extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_mountain_cams` (
                `id_mountain_cam` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`       INT(11) UNSIGNED NOT NULL,
                `is_enabled`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `num_cams`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 1,
                `cam_quality`     TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
                PRIMARY KEY (`id_mountain_cam`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_mountain_cams`');
    }
}
