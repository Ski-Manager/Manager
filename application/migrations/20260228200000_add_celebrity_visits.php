<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Celebrity / VIP Visits
 *
 * Creates game_resort_celebrity_visits to log random celebrity visit events:
 *   - visit_type  : influencer | pro_skier | film_crew
 *   - slopes_good : whether avg slope condition was above the threshold
 *   - lift_failed : whether a lift was in maintenance during the visit
 *   - rep_change  : net reputation change applied (positive = gain, negative = loss)
 *   - visit_date  : calendar date of the event
 */
class Migration_Add_celebrity_visits extends CI_Migration {

    public function up() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_celebrity_visits` (
                `id_visit`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_resort`   INT(11) UNSIGNED NOT NULL,
                `visit_type`  ENUM('influencer','pro_skier','film_crew') NOT NULL,
                `slopes_good` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `lift_failed` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `rep_change`  INT(6) NOT NULL DEFAULT 0,
                `visit_date`  DATE NOT NULL,
                PRIMARY KEY (`id_visit`),
                KEY `idx_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_celebrity_visits`');
    }
}
