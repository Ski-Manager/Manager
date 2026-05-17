<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Remove terrain zones feature
 *
 * Drops the game_terrain_zones table and removes the terrain_zone_id column
 * from game_locations.
 */
class Migration_Remove_terrain_zones extends CI_Migration {

    public function up() {
        if ($this->db->field_exists('terrain_zone_id', 'game_locations')) {
            $this->dbforge->drop_column('game_locations', 'terrain_zone_id');
        }
        $this->dbforge->drop_table('game_terrain_zones', TRUE);
    }

    public function down() {
        // Recreate game_terrain_zones
        $this->dbforge->add_field([
            'id_terrain_zone' => [
                'type'           => 'TINYINT',
                'constraint'     => 3,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'zone_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => FALSE,
            ],
            'name_english' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => FALSE,
            ],
            'name_french' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => FALSE,
            ],
            'cost_multiplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => FALSE,
                'default'    => '1.00',
            ],
            'building_time_multiplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => FALSE,
                'default'    => '1.00',
            ],
            'description_english' => [
                'type' => 'TEXT',
                'null' => FALSE,
            ],
            'description_french' => [
                'type' => 'TEXT',
                'null' => FALSE,
            ],
        ]);
        $this->dbforge->add_key('id_terrain_zone', TRUE);
        $this->dbforge->create_table('game_terrain_zones', TRUE);

        // Restore terrain_zone_id on game_locations
        $this->dbforge->add_column('game_locations', [
            'terrain_zone_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => TRUE,
                'null'       => TRUE,
                'default'    => NULL,
            ],
        ]);
    }
}
