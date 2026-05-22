<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add stream_mode and social_media columns to game_resort_mountain_cams
 *
 * stream_mode  : 0 = still images (default), 1 = live video stream
 * social_media : 0 = disabled (default),     1 = auto-share snapshots to social media
 */
class Migration_Add_mountain_cam_stream_social extends CI_Migration {

    public function up() {
        $this->db->query("
            ALTER TABLE `game_resort_mountain_cams`
                ADD COLUMN `stream_mode`  TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `cam_quality`,
                ADD COLUMN `social_media` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `stream_mode`
        ");
    }

    public function down() {
        $this->db->query("
            ALTER TABLE `game_resort_mountain_cams`
                DROP COLUMN `stream_mode`,
                DROP COLUMN `social_media`
        ");
    }
}
