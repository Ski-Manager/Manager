<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Night Skiing Events Table
 *
 * Creates game_night_skiing_events table for scheduling special one-off
 * night skiing events (DJ nights, race nights, torchlight parades).
 */
class Migration_Add_night_skiing_events extends CI_Migration {

    public function up() {
        // Create night skiing events table
        $fields = [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'       => 'INT',
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => FALSE,
            ],
            'scheduled_date' => [
                'type'   => 'DATE',
                'null'   => FALSE,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
                'null'       => FALSE,
            ],
            'visitor_bonus_pct' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
                'null'       => FALSE,
            ],
            'revenue_multiplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,3',
                'default'    => 1.000,
                'null'       => FALSE,
            ],
            'cost' => [
                'type'       => 'INT',
                'unsigned'   => TRUE,
                'default'    => 0,
                'null'       => FALSE,
            ],
            'rep_bonus' => [
                'type'       => 'TINYINT',
                'unsigned'   => TRUE,
                'default'    => 0,
                'null'       => FALSE,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key(['id_resort', 'scheduled_date', 'status']);
        $this->dbforge->create_table('game_night_skiing_events', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_night_skiing_events', TRUE);
    }
}
