<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add micro-events table
 *
 * Creates the game_resort_micro_events table used to present clickable
 * quick-decision pop-ups to players while they are playing.  Each event
 * offers two choices (A / B) with different cash and reputation outcomes.
 *
 * Event types:
 *   - vip_queue_jump   : A VIP insists on skipping the lift queue
 *   - press_interview  : A journalist requests a resort interview
 *   - equipment_deal   : A supplier offers a last-minute equipment deal
 *   - lost_skier       : A skier has not returned from the mountain
 */
class Migration_Add_micro_events extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_micro_event' => [
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
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'accepted', 'declined', 'expired'],
                'default'    => 'pending',
            ],
            'choice_made' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => TRUE,
                'default'    => NULL,
            ],
            'cash_delta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => TRUE,
            ],
            'reputation_delta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => TRUE,
            ],
        ]);
        $this->dbforge->add_key('id_micro_event', TRUE);
        $this->dbforge->create_table('game_resort_micro_events', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_resort_micro_events', TRUE);
    }
}
