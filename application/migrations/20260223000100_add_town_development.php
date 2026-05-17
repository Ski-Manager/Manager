<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Local Town Development
 *
 * Creates the game_resort_town table that tracks per-resort town development
 * state: current level (0–5), accumulated growth points, and timestamp.
 */
class Migration_Add_town_development extends CI_Migration {

    public function up() {
        $fields = [
            'id_resort' => [
                'type'       => 'INT',
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'town_level' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => TRUE,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'growth_points' => [
                'type'       => 'INT',
                'unsigned'   => TRUE,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => TRUE,
                'default'    => NULL,
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id_resort', TRUE); // PRIMARY KEY
        $this->dbforge->create_table('game_resort_town', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_resort_town', TRUE);
    }
}
