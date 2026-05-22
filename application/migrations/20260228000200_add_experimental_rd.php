<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: add_experimental_rd
 *
 * Creates the game_resort_rd table for the Experimental Tech & R&D system.
 */
class Migration_add_experimental_rd extends CI_Migration {

    public function up() {
        if (!$this->db->table_exists('game_resort_rd')) {
            $this->db->query("
                CREATE TABLE game_resort_rd (
                    id_rd       INT          NOT NULL AUTO_INCREMENT,
                    id_resort   INT          NOT NULL,
                    project_key VARCHAR(40)  NOT NULL,
                    status      ENUM('in_progress','completed','failed') NOT NULL DEFAULT 'in_progress',
                    rushed      TINYINT(1)   NOT NULL DEFAULT 0,
                    started_at  DATETIME     NOT NULL,
                    finish_at   DATETIME     NOT NULL,
                    PRIMARY KEY (id_rd),
                    UNIQUE KEY uq_resort_project (id_resort, project_key)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_resort_rd', TRUE);
    }
}
