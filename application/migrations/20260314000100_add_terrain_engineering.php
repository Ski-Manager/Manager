<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Terrain Engineering research table
 *
 * Creates game_terrain_engineering_research following the same pattern as the
 * other upgrade-tree research tables (slope, snowmaking, marketing, staff).
 * Each row tracks one resort's research progress on a single upgrade key.
 */
class Migration_Add_terrain_engineering extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_research' => [
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
            'upgrade_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => FALSE,
            ],
            'status' => [
                'type'    => "ENUM('in_progress','completed')",
                'null'    => FALSE,
                'default' => 'in_progress',
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
        $this->dbforge->add_key('id_research', TRUE);
        $this->dbforge->create_table('game_terrain_engineering_research');
        $this->db->query('ALTER TABLE `game_terrain_engineering_research` ADD UNIQUE KEY `uq_resort_upgrade` (`id_resort`, `upgrade_key`)');
        $this->db->query('ALTER TABLE `game_terrain_engineering_research` ADD INDEX `idx_resort` (`id_resort`)');
    }

    public function down() {
        $this->dbforge->drop_table('game_terrain_engineering_research', TRUE);
    }
}
