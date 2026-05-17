<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Retail Revenue Statistics Table
 *
 * Creates game_resort_rev_retail to track daily retail & amenities
 * revenue per resort.  This complements game_resort_retail (settings)
 * which was added in migration 20260226000000_add_retail_amenities.
 */
class Migration_Add_rev_retail_stat_table extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_rev_retail` (
                `id`         INT(11)       NOT NULL AUTO_INCREMENT,
                `id_resort`  INT(11)       NOT NULL,
                `date`       DATE          NOT NULL,
                `rev_retail` DECIMAL(15,2) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_resort_date` (`id_resort`, `date`),
                KEY `idx_rev_retail_resort_date` (`id_resort`, `date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_rev_retail`');
    }
}
