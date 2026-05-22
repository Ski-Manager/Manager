<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add VIP & Loyalty Programs
 *
 * Creates game_resort_vip_loyalty to store per-resort VIP and loyalty settings:
 *   - loyalty_enabled     : 0 = off, 1 = on (loyalty discount programme)
 *   - loyalty_discount_pct: percentage discount for loyal returning guests
 *   - vip_private_lift    : 0 = off, 1 = on (exclusive private lift service)
 *   - vip_premium_slopes  : 0 = off, 1 = on (premium/VIP slope access)
 *   - vip_concierge       : 0 = off, 1 = on (personal concierge service)
 */
class Migration_Add_vip_loyalty extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_vip_loyalty` (
                `id_vip_loyalty`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`            INT(11) UNSIGNED NOT NULL,
                `loyalty_enabled`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `loyalty_discount_pct` INT(2)  UNSIGNED NOT NULL DEFAULT 10,
                `vip_private_lift`     TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `vip_premium_slopes`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `vip_concierge`        TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_vip_loyalty`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_vip_loyalty`');
    }
}
