<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Improve_bank extends CI_Migration {

    public function up() {
        // Investment / savings-account table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_investments` (
                `id_investment` INT(11) NOT NULL AUTO_INCREMENT,
                `id_resort`     INT(11) NOT NULL,
                `balance`       BIGINT  NOT NULL DEFAULT 0,
                `created_at`    DATETIME NOT NULL,
                `updated_at`    DATETIME NOT NULL,
                PRIMARY KEY (`id_investment`),
                UNIQUE KEY `uq_resort` (`id_resort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    public function down() {
        $this->db->query('DROP TABLE IF EXISTS `game_resort_investments`');
    }
}
