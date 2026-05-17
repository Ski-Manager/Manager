<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add crisis events table
 *
 * Creates the game_resort_crisis_events table used to track rare
 * but impactful crisis events that affect individual resorts:
 *   - Major lift failure
 *   - Avalanche incident
 *   - Power outage
 *   - Viral negative media story
 */
class Migration_Add_crisis_events extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_crisis' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'id_player' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'event_date' => [
                'type' => 'DATETIME',
            ],
            'is_resolved' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'impact_description' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
        ]);
        $this->dbforge->add_key('id_crisis', TRUE);
        $this->dbforge->create_table('game_resort_crisis_events', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_resort_crisis_events', TRUE);
    }
}
