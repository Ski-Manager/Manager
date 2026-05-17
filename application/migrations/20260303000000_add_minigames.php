<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Minigames feature
 *
 * Creates two tables:
 *   - game_minigames      : catalogue of available minigame types
 *   - game_minigame_plays : instances of minigames played by players
 *
 * Seeds the catalogue with three minigames:
 *   1. Lucky Slalom   – slot-machine luck game
 *   2. Snow Quiz      – ski trivia quiz
 *   3. Snowball Rush  – reflex / reaction game
 */
class Migration_Add_minigames extends CI_Migration {

    private $seed = [
        [
            'name_english'        => 'Lucky Slalom',
            'name_french'         => 'Slalom Chanceux',
            'description_english' => 'Spin the reels and try to line up three matching ski symbols! A fun luck-based game where matching icons reward you with bonus cash for your resort.',
            'description_french'  => 'Faites tourner les rouleaux et essayez d\'aligner trois symboles de ski identiques ! Un jeu de chance amusant où les icônes correspondantes vous récompensent avec du cash supplémentaire pour votre station.',
            'play_cost'           => 0,
            'max_reward_cash'     => 5000,
            'max_reward_reputation' => 0,
            'cooldown_hours'      => 24,
            'minigame_type'       => 'luck',
            'active'              => 1,
            'game_order'          => 1,
        ],
        [
            'name_english'        => 'Snow Quiz',
            'name_french'         => 'Quiz des Neiges',
            'description_english' => 'Test your knowledge of ski resorts and alpine culture with five multiple-choice questions. Answer correctly to earn reputation points for your resort!',
            'description_french'  => 'Testez vos connaissances sur les stations de ski et la culture alpine avec cinq questions à choix multiples. Répondez correctement pour gagner des points de réputation pour votre station !',
            'play_cost'           => 0,
            'max_reward_cash'     => 0,
            'max_reward_reputation' => 10,
            'cooldown_hours'      => 24,
            'minigame_type'       => 'quiz',
            'active'              => 1,
            'game_order'          => 2,
        ],
        [
            'name_english'        => 'Snowball Rush',
            'name_french'         => 'Ruée aux Boules de Neige',
            'description_english' => 'Catch as many snowballs as you can before the timer runs out! A fast-paced reflex game where a high score earns you bonus cash.',
            'description_french'  => 'Attrapez autant de boules de neige que possible avant la fin du temps imparti ! Un jeu de réflexes effréné où un score élevé vous rapporte du cash supplémentaire.',
            'play_cost'           => 0,
            'max_reward_cash'     => 3000,
            'max_reward_reputation' => 0,
            'cooldown_hours'      => 24,
            'minigame_type'       => 'skill',
            'active'              => 1,
            'game_order'          => 3,
        ],
    ];

    public function up() {

        // ---- game_minigames (catalogue) ----------------------------
        if (!$this->db->table_exists('game_minigames')) {
            $this->dbforge->add_field([
                'id_minigame' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'name_english' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'name_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'description_english' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'description_french' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'play_cost' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'max_reward_cash' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'max_reward_reputation' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'cooldown_hours' => [
                    'type'    => 'INT',
                    'default' => 24,
                ],
                'minigame_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'luck',
                ],
                'active' => [
                    'type'    => 'TINYINT',
                    'default' => 1,
                ],
                'game_order' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
            ]);
            $this->dbforge->add_key('id_minigame', TRUE);
            $this->dbforge->create_table('game_minigames');

            foreach ($this->seed as $row) {
                $this->db->insert('game_minigames', $row);
            }
        }

        // ---- game_minigame_plays (player play log) ------------------
        if (!$this->db->table_exists('game_minigame_plays')) {
            $this->dbforge->add_field([
                'id_play' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'id_resort' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                ],
                'id_minigame' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                ],
                'play_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'result' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'default'    => 'lose',
                ],
                'score' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_cash' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_reputation' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
            ]);
            $this->dbforge->add_key('id_play', TRUE);
            $this->dbforge->create_table('game_minigame_plays');
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_minigame_plays', TRUE);
        $this->dbforge->drop_table('game_minigames', TRUE);
    }
}
