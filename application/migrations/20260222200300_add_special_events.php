<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Special Events feature
 *
 * Creates two tables:
 *   - game_special_events        : catalogue of available special event types
 *   - game_started_special_events: instances of events started by players
 *
 * Also seeds the catalogue with an initial set of events (concerts,
 * festivals, firework shows …).
 */
class Migration_Add_special_events extends CI_Migration {

    // ---------------------------------------------------------------
    // Seed data – initial catalogue of special events
    // ---------------------------------------------------------------
    private $seed = [
        [
            'name_english'          => 'Après-Ski Party',
            'name_french'           => 'Soirée Après-Ski',
            'description_english'   => 'A lively après-ski party in the village square. Perfect for attracting night-life lovers and boosting the resort atmosphere after a day on the slopes.',
            'description_french'    => 'Une soirée animée sur la place du village. Parfaite pour attirer les amateurs de vie nocturne et dynamiser l\'ambiance de la station après une journée sur les pistes.',
            'running_cost'          => 5000,
            'expected_revenue'      => 12000,
            'expected_visitors'     => 300,
            'reputation_points'     => 5,
            'duration'              => 1,
            'required_prestige'     => 0,
            'open_stage'            => 0,
            'housing_complex'       => 1,
            'event_order'           => 1,
            'display_on_page'       => 1,
        ],
        [
            'name_english'          => 'Live Music Concert',
            'name_french'           => 'Concert de musique live',
            'description_english'   => 'Invite a well-known band to perform on your open stage and attract music fans from across the region.',
            'description_french'    => 'Invitez un groupe connu à se produire sur votre scène extérieure et attirez des fans de musique de toute la région.',
            'running_cost'          => 25000,
            'expected_revenue'      => 55000,
            'expected_visitors'     => 1200,
            'reputation_points'     => 20,
            'duration'              => 2,
            'required_prestige'     => 200,
            'open_stage'            => 1,
            'housing_complex'       => 2,
            'event_order'           => 2,
            'display_on_page'       => 1,
        ],
        [
            'name_english'          => 'Winter Fireworks Festival',
            'name_french'           => 'Festival de feux d\'artifice hivernal',
            'description_english'   => 'A spectacular multi-night fireworks display over the mountains, drawing crowds from far and wide for several evenings.',
            'description_french'    => 'Un spectaculaire feu d\'artifice sur plusieurs nuits au-dessus des montagnes, attirant la foule de loin pour plusieurs soirées.',
            'running_cost'          => 60000,
            'expected_revenue'      => 120000,
            'expected_visitors'     => 2500,
            'reputation_points'     => 50,
            'duration'              => 3,
            'required_prestige'     => 500,
            'open_stage'            => 1,
            'housing_complex'       => 3,
            'event_order'           => 3,
            'display_on_page'       => 1,
        ],
        [
            'name_english'          => 'International Snow Sculpture Championship',
            'name_french'           => 'Championnat international de sculpture sur neige',
            'description_english'   => 'Host teams of sculptors from around the world competing to create the most impressive snow sculpture. A major cultural event that puts your resort on the map.',
            'description_french'    => 'Accueillez des équipes de sculpteurs du monde entier en compétition pour créer la sculpture de neige la plus impressionnante. Un événement culturel majeur qui met votre station sur la carte.',
            'running_cost'          => 150000,
            'expected_revenue'      => 280000,
            'expected_visitors'     => 5000,
            'reputation_points'     => 100,
            'duration'              => 5,
            'required_prestige'     => 1500,
            'open_stage'            => 2,
            'housing_complex'       => 4,
            'event_order'           => 4,
            'display_on_page'       => 1,
        ],
    ];

    // ---------------------------------------------------------------
    public function up() {

        // ---- game_special_events (catalogue) -----------------------
        if (!$this->db->table_exists('game_special_events')) {
            $this->dbforge->add_field([
                'id_special_event' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'name_english' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'name_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'description_english' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'description_french' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'running_cost' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'expected_revenue' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'expected_visitors' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reputation_points' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'duration' => [
                    'type'    => 'INT',
                    'default' => 1,
                ],
                'required_prestige' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'open_stage' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'housing_complex' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'event_order' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'display_on_page' => [
                    'type'    => 'TINYINT',
                    'default' => 1,
                ],
            ]);
            $this->dbforge->add_key('id_special_event', TRUE);
            $this->dbforge->create_table('game_special_events');

            // Seed catalogue
            foreach ($this->seed as $row) {
                $this->db->insert('game_special_events', $row);
            }
        }

        // ---- game_started_special_events (player instances) --------
        if (!$this->db->table_exists('game_started_special_events')) {
            $this->dbforge->add_field([
                'id_started_special_event' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'id_resort' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                ],
                'id_special_event' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                ],
                'started_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'end_date' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'aggregated_visitors' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'aggregated_revenue' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'completed' => [
                    'type'    => 'TINYINT',
                    'default' => 0,
                ],
            ]);
            $this->dbforge->add_key('id_started_special_event', TRUE);
            $this->dbforge->create_table('game_started_special_events');
        }

        // ---- stat tables (used by add_cost_stat_table helper) ------
        foreach (['game_resort_rev_special_events', 'game_resort_cost_special_events'] as $tbl) {
            if (!$this->db->table_exists($tbl)) {
                $col = str_replace('game_resort_', '', $tbl);
                $this->dbforge->add_field([
                    'id_resort' => ['type' => 'INT', 'unsigned' => TRUE],
                    'date'      => ['type' => 'DATE'],
                    $col        => ['type' => 'INT', 'default' => 0],
                ]);
                $this->dbforge->add_key(['id_resort', 'date'], TRUE);
                $this->dbforge->create_table($tbl);
            }
        }
    }

    // ---------------------------------------------------------------
    public function down() {
        $this->dbforge->drop_table('game_started_special_events', TRUE);
        $this->dbforge->drop_table('game_special_events', TRUE);
        $this->dbforge->drop_table('game_resort_rev_special_events', TRUE);
        $this->dbforge->drop_table('game_resort_cost_special_events', TRUE);
    }
}
