<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: add_visitor_needs
 *
 * Creates the game_visitor_needs table which stores per-resort visitor need
 * scores (hunger, fatigue, warmth, fun) computed each nightly run.
 * One row per resort; upserted every night.
 *
 * Schema (DDL reference):
 *
 *   CREATE TABLE `game_visitor_needs` (
 *     `id_resort`          INT            NOT NULL PRIMARY KEY,
 *     `hunger_score`       DECIMAL(5,2)   NOT NULL DEFAULT 50,
 *     `fatigue_score`      DECIMAL(5,2)   NOT NULL DEFAULT 50,
 *     `warmth_score`       DECIMAL(5,2)   NOT NULL DEFAULT 50,
 *     `fun_score`          DECIMAL(5,2)   NOT NULL DEFAULT 50,
 *     `needs_satisfaction` DECIMAL(5,2)   NOT NULL DEFAULT 50,
 *     `updated_at`         DATETIME
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
class Migration_Add_visitor_needs extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_visitor_needs` (
                `id_resort`          INT            NOT NULL,
                `hunger_score`       DECIMAL(5,2)   NOT NULL DEFAULT 50,
                `fatigue_score`      DECIMAL(5,2)   NOT NULL DEFAULT 50,
                `warmth_score`       DECIMAL(5,2)   NOT NULL DEFAULT 50,
                `fun_score`          DECIMAL(5,2)   NOT NULL DEFAULT 50,
                `needs_satisfaction` DECIMAL(5,2)   NOT NULL DEFAULT 50,
                `updated_at`         DATETIME,
                PRIMARY KEY (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    public function down() {
        $this->dbforge->drop_table('game_visitor_needs', TRUE);
    }
}
