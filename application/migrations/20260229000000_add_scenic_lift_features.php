<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Scenic Lift Features
 *
 * Extends game_resort_scenic_lifts with two new per-resort settings:
 *   - capacity_level   : gondola fleet size 1–5 (default 3 preserves existing behaviour)
 *   - seasonal_discount: whether to offer off-peak discounts (0 = off, 1 = on)
 */
class Migration_Add_scenic_lift_features extends CI_Migration {

    public function up() {
        $this->db->query("
            ALTER TABLE `game_resort_scenic_lifts`
                ADD COLUMN `capacity_level`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 3
                    COMMENT 'Gondola fleet size 1-5; affects throughput and daily cost',
                ADD COLUMN `seasonal_discount` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
                    COMMENT '1 = apply off-peak price discount + visitor boost';
        ");
    }

    public function down() {
        $this->db->query("
            ALTER TABLE `game_resort_scenic_lifts`
                DROP COLUMN `capacity_level`,
                DROP COLUMN `seasonal_discount`;
        ");
    }
}
