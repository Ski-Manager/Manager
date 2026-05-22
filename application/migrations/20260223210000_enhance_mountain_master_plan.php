<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Enhance Mountain Master Plan System
 *
 * Adds the 'expired' status so that active plans can be automatically
 * retired after MASTER_PLAN_DURATION_DAYS game-days.
 */
class Migration_Enhance_mountain_master_plan extends CI_Migration {

    public function up() {
        $this->db->query("
            ALTER TABLE `game_master_plans`
                MODIFY COLUMN `status`
                    ENUM('draft','submitted','approved','active','expired')
                    NOT NULL DEFAULT 'draft'
        ");
    }

    public function down() {
        $this->db->query("
            ALTER TABLE `game_master_plans`
                MODIFY COLUMN `status`
                    ENUM('draft','submitted','approved','active')
                    NOT NULL DEFAULT 'draft'
        ");
    }
}
