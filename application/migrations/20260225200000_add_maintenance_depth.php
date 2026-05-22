<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Maintenance Depth
 *
 * Creates the game_resort_maintenance_depth table which stores per-resort
 * preventive maintenance plan settings.  The nightly job uses this data to:
 *   - Apply random mechanical-failure checks scaled by lift type, age, and usage.
 *   - Reduce failure probability or repair costs depending on the chosen plan.
 *   - Factor in lift-mechanic staff skill when computing repair cost.
 */
class Migration_Add_maintenance_depth extends CI_Migration {

    public function up() {
        if (!$this->db->table_exists('game_resort_maintenance_depth')) {
            $this->dbforge->add_field([
                'id_resort' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'null'           => FALSE,
                ],
                'maintenance_plan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => FALSE,
                    'default'    => 'basic',
                ],
            ]);
            $this->dbforge->add_key('id_resort', TRUE);
            $this->dbforge->create_table('game_resort_maintenance_depth');
        }
    }

    public function down() {
        if ($this->db->table_exists('game_resort_maintenance_depth')) {
            $this->dbforge->drop_table('game_resort_maintenance_depth');
        }
    }
}
