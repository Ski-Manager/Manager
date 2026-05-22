<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Scenic Lifts
 *
 * Creates game_resort_scenic_lifts to store per-resort scenic-lift settings:
 *   - is_enabled  : 0 = service off, 1 = service on
 *   - ticket_price: price charged per sightseeing visitor (€)
 */
class Migration_Add_scenic_lifts extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_scenic_lifts` (
                `id_scenic_lift` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`      INT(11) UNSIGNED NOT NULL,
                `is_enabled`     TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `ticket_price`   INT(4)  UNSIGNED NOT NULL DEFAULT 20,
                PRIMARY KEY (`id_scenic_lift`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_scenic_lifts`');
    }
}
