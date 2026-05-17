<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Insurance
 *
 * Creates game_resort_insurance to store per-resort insurance plan settings:
 *   - plan              : 'none' | 'basic' | 'premium'
 *   - total_claims      : cumulative number of claims paid
 *   - total_claimed_amount : cumulative € paid out as insurance claims
 */
class Migration_Add_insurance extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_insurance` (
                `id_insurance`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`            INT(11) UNSIGNED NOT NULL,
                `plan`                 ENUM('none','basic','premium') NOT NULL DEFAULT 'none',
                `total_claims`         INT(11) UNSIGNED NOT NULL DEFAULT 0,
                `total_claimed_amount` BIGINT  UNSIGNED NOT NULL DEFAULT 0,
                `updated_at`           DATETIME NOT NULL,
                PRIMARY KEY (`id_insurance`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_insurance`');
    }
}
