<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Season Ski Passes
 *
 * Creates game_resort_season_passes to store per-resort season pass settings:
 *   - enabled           : 0 = off, 1 = on
 *   - season_pass_price : price charged per season pass (€)
 *   - passes_sold       : number of passes sold for the current season
 *   - current_season    : season number for which passes_sold was last calculated
 */
class Migration_Add_season_ski_passes extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_season_passes` (
                `id_season_pass`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`         INT(11) UNSIGNED NOT NULL,
                `enabled`           TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `season_pass_price` INT(5)  UNSIGNED NOT NULL DEFAULT 500,
                `passes_sold`       INT(6)  UNSIGNED NOT NULL DEFAULT 0,
                `current_season`    INT(4)  UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_season_pass`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_season_passes`');
    }
}
