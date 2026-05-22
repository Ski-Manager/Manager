<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Idle Income System
 *
 * 1. Adds a `pending_idle_income` column to game_resorts so that passive
 *    income can accumulate while the player is offline.
 * 2. Creates game_resort_rev_idle to track the daily idle-income statistic
 *    per resort (mirrors the pattern of other game_resort_rev_* tables).
 */
class Migration_Add_idle_income extends CI_Migration {

    public function up() {
        // Add pending_idle_income to game_resorts if it does not already exist
        $fields = $this->db->list_fields('game_resorts');
        if (!in_array('pending_idle_income', $fields)) {
            $this->db->query("
                ALTER TABLE `game_resorts`
                ADD COLUMN `pending_idle_income` BIGINT NOT NULL DEFAULT 0
                    COMMENT 'Passive income accumulated while player is offline (€)'
            ");
        }

        // Create the daily idle-income statistics table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_rev_idle` (
                `id`         INT(11)       NOT NULL AUTO_INCREMENT,
                `id_resort`  INT(11)       NOT NULL,
                `date`       DATE          NOT NULL,
                `rev_idle`   DECIMAL(15,2) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_resort_date` (`id_resort`, `date`),
                KEY `idx_rev_idle_resort_date` (`id_resort`, `date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        // Remove stat table
        $this->db->query('DROP TABLE IF EXISTS `game_resort_rev_idle`');

        // Remove column
        $fields = $this->db->list_fields('game_resorts');
        if (in_array('pending_idle_income', $fields)) {
            $this->db->query('ALTER TABLE `game_resorts` DROP COLUMN `pending_idle_income`');
        }
    }
}
