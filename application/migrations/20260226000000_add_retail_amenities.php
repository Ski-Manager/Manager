<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Retail & Amenities
 *
 * Creates game_resort_retail to store per-resort settings for the four
 * shop types available along the slopes:
 *   ski_shop, souvenir_shop, cafe, bar
 *
 * Columns:
 *   enabled             : 0 = closed, 1 = open
 *   stock_level         : 1–5, controls revenue potential and popularity drift
 *   pricing_strategy    : 'budget' | 'standard' | 'premium'
 *   seasonal_items      : 0/1 – boosts winter revenue when enabled
 *   popularity          : 0–100, drifts nightly based on stock and pricing
 */
class Migration_Add_retail_amenities extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_retail` (
                `id_retail`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`          INT(11) UNSIGNED NOT NULL,
                `shop_type`          VARCHAR(20)  NOT NULL DEFAULT 'ski_shop',
                `enabled`            TINYINT(1)   UNSIGNED NOT NULL DEFAULT 0,
                `stock_level`        TINYINT(1)   UNSIGNED NOT NULL DEFAULT 3,
                `pricing_strategy`   VARCHAR(10)  NOT NULL DEFAULT 'standard',
                `seasonal_items`     TINYINT(1)   UNSIGNED NOT NULL DEFAULT 0,
                `popularity`         TINYINT(3)   UNSIGNED NOT NULL DEFAULT 50,
                PRIMARY KEY (`id_retail`),
                UNIQUE KEY `uq_resort_shop` (`id_resort`, `shop_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_retail`');
    }
}
