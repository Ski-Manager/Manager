<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add upgrade tree research tables
 *
 * Creates four tables following the same pattern as game_lift_tech_research:
 *   game_slope_upgrade_research
 *   game_snowmaking_upgrade_research
 *   game_marketing_upgrade_research
 *   game_staff_upgrade_research
 *
 * Each stores per-resort research progress for the corresponding upgrade tree.
 */
class Migration_Add_upgrade_trees extends CI_Migration {

    /** Helper: add one research table */
    private function _create_research_table($table_name) {
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
        $this->dbforge->create_table($table_name);
        $this->db->query("ALTER TABLE `{$table_name}` ADD UNIQUE KEY `uq_resort_upgrade` (`id_resort`, `upgrade_key`)");
        $this->db->query("ALTER TABLE `{$table_name}` ADD INDEX `idx_resort` (`id_resort`)");
    }

    public function up() {
        $this->_create_research_table('game_slope_upgrade_research');
        $this->_create_research_table('game_snowmaking_upgrade_research');
        $this->_create_research_table('game_marketing_upgrade_research');
        $this->_create_research_table('game_staff_upgrade_research');
    }

    public function down() {
        $this->dbforge->drop_table('game_slope_upgrade_research',      TRUE);
        $this->dbforge->drop_table('game_snowmaking_upgrade_research',  TRUE);
        $this->dbforge->drop_table('game_marketing_upgrade_research',   TRUE);
        $this->dbforge->drop_table('game_staff_upgrade_research',       TRUE);
    }
}
