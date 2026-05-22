<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Environmental System
 *
 * Creates game_resort_environment table to track:
 *   - eco_reputation      : 0-100 environmental reputation score
 *   - carbon_footprint    : daily carbon output (from lifts, cannons, groomers)
 *   - noise_pollution     : daily noise level (from cannons, lifts, groomers)
 *   - wildlife_zone       : 1 if wildlife protection zone is active
 *   - solar_panels        : 1 if solar panels installed (green investment)
 *   - electric_groomers   : number of electric groomers purchased
 *   - expansion_restricted: 1 when pollution is too high
 */
class Migration_Add_environmental_system extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_environment' => [
                'type'           => 'INT',
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'     => 'INT',
                'unsigned' => TRUE,
                'null'     => FALSE,
            ],
            'eco_reputation' => [
                'type'       => 'INT',
                'null'       => FALSE,
                'default'    => 50,
            ],
            'carbon_footprint' => [
                'type'       => 'INT',
                'null'       => FALSE,
                'default'    => 0,
            ],
            'noise_pollution' => [
                'type'       => 'INT',
                'null'       => FALSE,
                'default'    => 0,
            ],
            'wildlife_zone' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'solar_panels' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'electric_groomers' => [
                'type'       => 'INT',
                'null'       => FALSE,
                'default'    => 0,
            ],
            'expansion_restricted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
            ],
        ]);

        $this->dbforge->add_key('id_environment', TRUE);
        $this->dbforge->add_key('id_resort');
        $this->dbforge->create_table('game_resort_environment', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_resort_environment', TRUE);
    }
}
