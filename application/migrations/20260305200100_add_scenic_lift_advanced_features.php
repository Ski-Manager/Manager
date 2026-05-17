<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Scenic Lift Advanced Features
 *
 * Extends game_resort_scenic_lifts with three new per-resort settings:
 *   - tour_theme          : tour theme selection (0=standard, 1=nature, 2=sunset, 3=adventure)
 *   - photography_package : optional on-gondola photo service (0=off, 1=on)
 *   - vip_gondola         : VIP-only gondola mode (0=off, 1=on)
 */
class Migration_Add_scenic_lift_advanced_features extends CI_Migration {

    public function up() {
        $this->db->query("
            ALTER TABLE `game_resort_scenic_lifts`
                ADD COLUMN `tour_theme`          TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
                    COMMENT '0=standard, 1=nature, 2=sunset, 3=adventure',
                ADD COLUMN `photography_package` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
                    COMMENT '1 = on-gondola photo service active',
                ADD COLUMN `vip_gondola`         TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
                    COMMENT '1 = VIP-only gondola mode active';
        ");
    }

    public function down() {
        $this->db->query("
            ALTER TABLE `game_resort_scenic_lifts`
                DROP COLUMN `tour_theme`,
                DROP COLUMN `photography_package`,
                DROP COLUMN `vip_gondola`;
        ");
    }
}
