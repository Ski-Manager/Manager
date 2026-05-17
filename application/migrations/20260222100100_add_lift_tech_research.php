<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Create game_lift_tech_research table
 *
 * Stores the lift technology research progress for each resort.
 * Players spend money to start researching a technology; it completes after
 * a set number of in-game days (tracked via finish_at datetime).
 *
 * SQL equivalent:
 *   CREATE TABLE `game_lift_tech_research` (
 *     `id_lift_tech_research` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `id_resort`             INT UNSIGNED NOT NULL,
 *     `tech_key`              VARCHAR(40)  NOT NULL,
 *     `status`                ENUM('in_progress','completed') NOT NULL DEFAULT 'in_progress',
 *     `started_at`            DATETIME     NOT NULL,
 *     `finish_at`             DATETIME     NOT NULL,
 *     UNIQUE KEY `uq_resort_tech` (`id_resort`, `tech_key`),
 *     INDEX `idx_resort` (`id_resort`)
 *   );
 */
class Migration_Add_lift_tech_research extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_lift_tech_research' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'tech_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => FALSE,
            ],
            'status' => [
                'type'       => "ENUM('in_progress','completed')",
                'null'       => FALSE,
                'default'    => 'in_progress',
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
            ],
            'finish_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
            ],
        ]);

        $this->dbforge->add_key('id_lift_tech_research', TRUE);
        $this->dbforge->create_table('game_lift_tech_research');

        $this->db->query('ALTER TABLE `game_lift_tech_research` ADD UNIQUE KEY `uq_resort_tech` (`id_resort`, `tech_key`)');
        $this->db->query('ALTER TABLE `game_lift_tech_research` ADD INDEX `idx_resort` (`id_resort`)');
    }

    public function down() {
        $this->dbforge->drop_table('game_lift_tech_research', TRUE);
    }
}
