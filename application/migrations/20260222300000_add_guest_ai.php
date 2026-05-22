<?php
/**
 * Migration: add_guest_ai
 *
 * Creates the game_guest_ai table which stores per-slope guest preference
 * scores used by the data-driven guest AI.  One row per (resort, slope) pair
 * is upserted each nightly run.
 *
 * Schema (DDL reference):
 *
 *   CREATE TABLE `game_guest_ai` (
 *     `id_guest_ai`        INT            NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `id_resort`          INT            NOT NULL,
 *     `id_created_slope`   INT            NOT NULL,
 *     `slope_name`         VARCHAR(100)   NOT NULL DEFAULT '',
 *     `score_difficulty`   DECIMAL(5,2)   NOT NULL DEFAULT 0,
 *     `score_snow_quality` DECIMAL(5,2)   NOT NULL DEFAULT 0,
 *     `score_crowd`        DECIMAL(5,2)   NOT NULL DEFAULT 0,
 *     `score_lift_speed`   DECIMAL(5,2)   NOT NULL DEFAULT 0,
 *     `score_ticket_price` DECIMAL(5,2)   NOT NULL DEFAULT 0,
 *     `total_score`        DECIMAL(5,2)   NOT NULL DEFAULT 0,
 *     `daily_visitors`     INT            NOT NULL DEFAULT 0,
 *     `updated_at`         DATETIME,
 *     UNIQUE KEY `uq_resort_slope` (`id_resort`, `id_created_slope`)
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
class Migration_Add_guest_ai extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_guest_ai` (
                `id_guest_ai`        INT            NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `id_resort`          INT            NOT NULL,
                `id_created_slope`   INT            NOT NULL,
                `slope_name`         VARCHAR(100)   NOT NULL DEFAULT '',
                `score_difficulty`   DECIMAL(5,2)   NOT NULL DEFAULT 0,
                `score_snow_quality` DECIMAL(5,2)   NOT NULL DEFAULT 0,
                `score_crowd`        DECIMAL(5,2)   NOT NULL DEFAULT 0,
                `score_lift_speed`   DECIMAL(5,2)   NOT NULL DEFAULT 0,
                `score_ticket_price` DECIMAL(5,2)   NOT NULL DEFAULT 0,
                `total_score`        DECIMAL(5,2)   NOT NULL DEFAULT 0,
                `daily_visitors`     INT            NOT NULL DEFAULT 0,
                `updated_at`         DATETIME,
                UNIQUE KEY `uq_resort_slope` (`id_resort`, `id_created_slope`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    public function down() {
        $this->dbforge->drop_table('game_guest_ai', TRUE);
    }
}
