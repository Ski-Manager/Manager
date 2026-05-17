<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Sponsorships
 *
 * Creates game_resort_sponsorships to store active sponsor contracts per resort:
 *   - sponsor_type        : one of lift_equipment / apparel / energy_drink / resort_map / event_title
 *   - contract_level      : 1 = basic, 2 = standard, 3 = premium
 *   - is_active           : 0 = terminated, 1 = active
 *   - brand_satisfaction  : 0–100 sponsor happiness score
 *   - signed_at           : timestamp of contract activation
 */
class Migration_Add_sponsorships extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_sponsorships` (
                `id_sponsorship`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`           INT(11) UNSIGNED NOT NULL,
                `sponsor_type`        VARCHAR(30)      NOT NULL,
                `contract_level`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
                `is_active`           TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
                `brand_satisfaction`  INT(3)  UNSIGNED NOT NULL DEFAULT 70,
                `signed_at`           DATETIME         NOT NULL,
                PRIMARY KEY (`id_sponsorship`),
                UNIQUE KEY `uq_resort_type` (`id_resort`, `sponsor_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_sponsorships`');
    }
}
