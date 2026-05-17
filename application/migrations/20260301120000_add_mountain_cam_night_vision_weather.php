<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add night_vision and weather_overlay columns to game_resort_mountain_cams
 *
 * night_vision    : 0 = disabled (default), 1 = night-vision / infrared mode enabled
 * weather_overlay : 0 = disabled (default), 1 = live weather data overlaid on feeds
 */
class Migration_Add_mountain_cam_night_vision_weather extends CI_Migration {

    public function up() {
        $this->db->query("
            ALTER TABLE `game_resort_mountain_cams`
                ADD COLUMN `night_vision`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `social_media`,
                ADD COLUMN `weather_overlay` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `night_vision`
        ");
    }

    public function down() {
        $this->db->query("
            ALTER TABLE `game_resort_mountain_cams`
                DROP COLUMN `night_vision`,
                DROP COLUMN `weather_overlay`
        ");
    }
}
