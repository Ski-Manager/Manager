<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Seasonal Objectives
 *
 * Creates two tables:
 *   game_seasonal_objectives      – catalogue of objectives (shared, seeded here)
 *   game_player_seasonal_objectives – per-player, per-season progress
 *
 * Four objectives are seeded:
 *   1. hit_50k_visitors   – accumulate 50 000 visitors in the season
 *   2. maintain_reputation – keep reputation >= 1 000 throughout the season
 *   3. host_2_events       – host (complete) at least 2 tournaments in the season
 *   4. no_lift_breakdowns  – have zero lift breakdowns during the season
 */
class Migration_Add_seasonal_objectives extends CI_Migration {

    public function up() {
        // ----------------------------------------------------------------
        // 1. Catalogue table
        // ----------------------------------------------------------------
        if (!$this->db->table_exists('game_seasonal_objectives')) {
            $this->dbforge->add_field([
                'id_objective' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'objective_key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                    'unique'     => TRUE,
                ],
                'name_english' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 200,
                ],
                'name_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 200,
                ],
                'description_english' => [
                    'type' => 'TEXT',
                ],
                'description_french' => [
                    'type' => 'TEXT',
                ],
                'target_value' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_prestige' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_cash' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_genepis' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
            ]);
            $this->dbforge->add_key('id_objective', TRUE);
            $this->dbforge->create_table('game_seasonal_objectives');
        }

        // ----------------------------------------------------------------
        // 2. Player-progress table
        // ----------------------------------------------------------------
        if (!$this->db->table_exists('game_player_seasonal_objectives')) {
            $this->dbforge->add_field([
                'id_player_seasonal_obj' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'id_resort' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                ],
                'id_objective' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                ],
                'season' => [
                    'type'    => 'INT',
                    'default' => 1,
                ],
                'current_value' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'failed' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'completed' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'rewarded' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
            ]);
            $this->dbforge->add_key('id_player_seasonal_obj', TRUE);
            $this->dbforge->create_table('game_player_seasonal_objectives');
        }

        // ----------------------------------------------------------------
        // 3. Seed the four objectives (idempotent – skip if already present)
        // ----------------------------------------------------------------
        $objectives = [
            [
                'objective_key'       => 'hit_50k_visitors',
                'name_english'        => 'Peak Season',
                'name_french'         => 'Haute saison',
                'description_english' => 'Welcome 50,000 visitors to your resort during the season.',
                'description_french'  => 'Accueillir 50 000 visiteurs dans votre station durant la saison.',
                'target_value'        => 50000,
                'reward_prestige'     => 50,
                'reward_cash'         => 500000,
                'reward_genepis'      => 0,
            ],
            [
                'objective_key'       => 'maintain_reputation',
                'name_english'        => 'Guest Satisfaction',
                'name_french'         => 'Satisfaction des clients',
                'description_english' => 'Keep your resort\'s reputation above 1,000 throughout the entire season.',
                'description_french'  => 'Maintenir la réputation de votre station au-dessus de 1 000 tout au long de la saison.',
                'target_value'        => 1000,
                'reward_prestige'     => 75,
                'reward_cash'         => 250000,
                'reward_genepis'      => 0,
            ],
            [
                'objective_key'       => 'host_2_events',
                'name_english'        => 'Event Host',
                'name_french'         => 'Organisateur d\'évènements',
                'description_english' => 'Host and complete at least 2 tournaments during the season.',
                'description_french'  => 'Organiser et terminer au moins 2 tournois durant la saison.',
                'target_value'        => 2,
                'reward_prestige'     => 60,
                'reward_cash'         => 300000,
                'reward_genepis'      => 5,
            ],
            [
                'objective_key'       => 'no_lift_breakdowns',
                'name_english'        => 'Zero Downtime',
                'name_french'         => 'Zéro panne',
                'description_english' => 'Operate the entire season without any lift breakdown.',
                'description_french'  => 'Gérer toute la saison sans aucune panne de remontée mécanique.',
                'target_value'        => 0,
                'reward_prestige'     => 100,
                'reward_cash'         => 1000000,
                'reward_genepis'      => 10,
            ],
        ];

        foreach ($objectives as $obj) {
            $exists = $this->db
                ->where('objective_key', $obj['objective_key'])
                ->count_all_results('game_seasonal_objectives');
            if ($exists === 0) {
                $this->db->insert('game_seasonal_objectives', $obj);
            }
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_player_seasonal_objectives', TRUE);
        $this->dbforge->drop_table('game_seasonal_objectives', TRUE);
    }
}
