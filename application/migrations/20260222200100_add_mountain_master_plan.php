<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Mountain Master Plan System
 *
 * Creates the `game_master_plans` table which stores the 5-year development
 * plans for each resort.  A plan tracks:
 *   - Plan name and expansion strategy text
 *   - Environmental notes (environmental approval process)
 *   - Zoning limits (max new slopes / lifts / buildings over the plan period)
 *   - Status lifecycle: draft → submitted → approved → active
 *   - Change count and financial/reputation penalties on revision
 */
class Migration_Add_mountain_master_plan extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_master_plans` (
                `id_master_plan`          INT          NOT NULL AUTO_INCREMENT,
                `id_resort`               INT          NOT NULL,
                `plan_name`               VARCHAR(100) NOT NULL,
                `expansion_strategy`      TEXT         NOT NULL,
                `environmental_notes`     TEXT         NOT NULL,
                `zoning_limit_slopes`     INT          NOT NULL DEFAULT 5,
                `zoning_limit_lifts`      INT          NOT NULL DEFAULT 3,
                `zoning_limit_buildings`  INT          NOT NULL DEFAULT 10,
                `status`                  ENUM('draft','submitted','approved','active')
                                                       NOT NULL DEFAULT 'draft',
                `change_count`            INT          NOT NULL DEFAULT 0,
                `submitted_at`            DATETIME     NULL,
                `approved_at`             DATETIME     NULL,
                `activated_at`            DATETIME     NULL,
                `created_at`              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at`              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                       ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_master_plan`),
                KEY `idx_mp_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_master_plans`');
    }
}
