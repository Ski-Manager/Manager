<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Environmental Management features
 *
 * Adds two new columns to game_resort_environment:
 *   - tree_count     : number of reforestation investments made (0–5)
 *   - water_recycling: 1 when the water recycling system is installed
 */
class Migration_Add_env_features extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('tree_count', 'game_resort_environment')) {
            $this->dbforge->add_column('game_resort_environment', [
                'tree_count' => [
                    'type'    => 'INT',
                    'null'    => FALSE,
                    'default' => 0,
                    'after'   => 'electric_groomers',
                ],
            ]);
        }

        if (!$this->db->field_exists('water_recycling', 'game_resort_environment')) {
            $this->dbforge->add_column('game_resort_environment', [
                'water_recycling' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                    'after'      => 'tree_count',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('water_recycling', 'game_resort_environment')) {
            $this->dbforge->drop_column('game_resort_environment', 'water_recycling');
        }
        if ($this->db->field_exists('tree_count', 'game_resort_environment')) {
            $this->dbforge->drop_column('game_resort_environment', 'tree_count');
        }
    }
}
