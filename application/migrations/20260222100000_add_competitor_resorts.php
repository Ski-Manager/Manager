<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Competitor Resorts
 *
 * Creates:
 *   - game_competitor_resorts  : catalogue of AI-driven competitor resorts
 *   - game_player_competitors  : per-player state for each assigned competitor
 *
 * Players can view nearby AI resorts that compete for tourists, run marketing
 * campaigns, offer cheaper tickets, and invest in mega lifts.  Each night the
 * competitors evolve autonomously and apply a visitor-pressure penalty to the
 * player's resort.
 */
class Migration_Add_competitor_resorts extends CI_Migration {

    /** Seed data – 6 AI competitor resorts */
    private $seed = [
        [
            'name_english'       => 'Alpine Peak Resort',
            'name_french'        => 'Station du Sommet Alpin',
            'base_reputation'    => 70,
            'base_ticket_price'  => 45,
            'base_lift_level'    => 3,
        ],
        [
            'name_english'       => 'Glacier Valley Ski Area',
            'name_french'        => 'Domaine du Val Glacier',
            'base_reputation'    => 55,
            'base_ticket_price'  => 38,
            'base_lift_level'    => 2,
        ],
        [
            'name_english'       => 'Summit Ridge Resort',
            'name_french'        => 'Station de la Crête Sommitale',
            'base_reputation'    => 80,
            'base_ticket_price'  => 55,
            'base_lift_level'    => 4,
        ],
        [
            'name_english'       => 'Powder Bowl Mountain',
            'name_french'        => 'Montagne du Bol de Poudreuse',
            'base_reputation'    => 45,
            'base_ticket_price'  => 30,
            'base_lift_level'    => 1,
        ],
        [
            'name_english'       => 'Crystal Snow Park',
            'name_french'        => 'Parc Neige Cristal',
            'base_reputation'    => 65,
            'base_ticket_price'  => 50,
            'base_lift_level'    => 3,
        ],
        [
            'name_english'       => 'Northern Peaks Ski Resort',
            'name_french'        => 'Station des Pics du Nord',
            'base_reputation'    => 75,
            'base_ticket_price'  => 48,
            'base_lift_level'    => 4,
        ],
    ];

    public function up() {
        // ---------------------------------------------------------------
        // game_competitor_resorts – catalogue table (shared, AI resorts)
        // ---------------------------------------------------------------
        if (!$this->db->table_exists('game_competitor_resorts')) {
            $this->dbforge->add_field([
                'id_competitor'      => ['type' => 'INT',         'unsigned' => TRUE, 'auto_increment' => TRUE],
                'name_english'       => ['type' => 'VARCHAR',     'constraint' => 100],
                'name_french'        => ['type' => 'VARCHAR',     'constraint' => 100],
                'base_reputation'    => ['type' => 'TINYINT',     'unsigned' => TRUE, 'default' => 50],
                'base_ticket_price'  => ['type' => 'SMALLINT',    'unsigned' => TRUE, 'default' => 40],
                'base_lift_level'    => ['type' => 'TINYINT',     'unsigned' => TRUE, 'default' => 2],
            ]);
            $this->dbforge->add_key('id_competitor', TRUE);
            $this->dbforge->create_table('game_competitor_resorts');

            // Seed the catalogue
            foreach ($this->seed as $row) {
                $this->db->insert('game_competitor_resorts', $row);
            }
        }

        // ---------------------------------------------------------------
        // game_player_competitors – per-player state for each competitor
        // ---------------------------------------------------------------
        if (!$this->db->table_exists('game_player_competitors')) {
            $this->dbforge->add_field([
                'id_player_competitor' => ['type' => 'INT',      'unsigned' => TRUE, 'auto_increment' => TRUE],
                'id_resort'            => ['type' => 'INT',      'unsigned' => TRUE],
                'id_competitor'        => ['type' => 'INT',      'unsigned' => TRUE],
                'marketing_level'      => ['type' => 'TINYINT',  'unsigned' => TRUE, 'default' => 0],  // 0-10
                'ticket_discount'      => ['type' => 'TINYINT',  'unsigned' => TRUE, 'default' => 0],  // 0-50 %
                'lift_investment'      => ['type' => 'TINYINT',  'unsigned' => TRUE, 'default' => 0],  // 0-5
                'last_updated'         => ['type' => 'DATE',     'null' => TRUE],
            ]);
            $this->dbforge->add_key('id_player_competitor', TRUE);
            $this->dbforge->add_key(['id_resort', 'id_competitor']);
            $this->dbforge->create_table('game_player_competitors');
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_player_competitors',  TRUE);
        $this->dbforge->drop_table('game_competitor_resorts', TRUE);
    }
}
