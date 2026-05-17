<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Fix missing columns on game_lift_module_upgrades
 *
 * Deployed databases may have the game_lift_module_upgrades table created by
 * an earlier migration that had either a different schema or was created before
 * all required columns were defined.  The previous "ensure" migration
 * (20260223050000) only issues CREATE TABLE IF NOT EXISTS, so an existing table
 * with the wrong schema is never updated.
 *
 * This migration idempotently adds every required column that is missing, and
 * upserts the five standard catalogue rows so that the table is always fully
 * populated.
 */
class Migration_Fix_lift_module_upgrades_schema extends CI_Migration {

    public function up() {
        // ----------------------------------------------------------------
        // 1. Ensure the catalogue table itself exists (safety net)
        // ----------------------------------------------------------------
        if (!$this->db->table_exists('game_lift_module_upgrades')) {
            $this->dbforge->add_field([
                'id_module' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'module_key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
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
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'description_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'cost' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'speed_bonus' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => '0.00',
                ],
                'throughput_bonus' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'capacity_bonus' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'reputation_bonus' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'daily_cost_increase' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
            ]);
            $this->dbforge->add_key('id_module', true);
            $this->dbforge->add_key('module_key');
            $this->dbforge->create_table('game_lift_module_upgrades');
        } else {
            // ----------------------------------------------------------------
            // 2. Add any missing columns to the existing table
            // ----------------------------------------------------------------
            $existing = $this->db->list_fields('game_lift_module_upgrades');

            if (!in_array('name_english', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'name_english' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 100,
                        'default'    => '',
                    ],
                ]);
            }

            if (!in_array('name_french', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'name_french' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 100,
                        'default'    => '',
                    ],
                ]);
            }

            if (!in_array('description_english', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'description_english' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'default'    => '',
                    ],
                ]);
            }

            if (!in_array('description_french', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'description_french' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'default'    => '',
                    ],
                ]);
            }

            if (!in_array('cost', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'cost' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'default'    => 0,
                    ],
                ]);
            }

            if (!in_array('speed_bonus', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'speed_bonus' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '5,2',
                        'default'    => '0.00',
                    ],
                ]);
            }

            if (!in_array('throughput_bonus', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'throughput_bonus' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'default'    => 0,
                    ],
                ]);
            }

            if (!in_array('capacity_bonus', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'capacity_bonus' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'default'    => 0,
                    ],
                ]);
            }

            if (!in_array('reputation_bonus', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'reputation_bonus' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'default'    => 0,
                    ],
                ]);
            }

            if (!in_array('daily_cost_increase', $existing, TRUE)) {
                $this->dbforge->add_column('game_lift_module_upgrades', [
                    'daily_cost_increase' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'default'    => 0,
                    ],
                ]);
            }
        }

        // ----------------------------------------------------------------
        // 3. Upsert the five standard catalogue rows
        // ----------------------------------------------------------------
        $modules = [
            [
                'module_key'          => 'motor',
                'name_english'        => 'Motor Upgrade',
                'name_french'         => 'Mise à niveau du moteur',
                'description_english' => 'Higher-power drive unit increases haul-rope speed and passenger throughput.',
                'description_french'  => 'Un moteur plus puissant augmente la vitesse du câble et le débit de passagers.',
                'cost'                => 150000,
                'speed_bonus'         => 1.00,
                'throughput_bonus'    => 300,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 5,
                'daily_cost_increase' => 200,
            ],
            [
                'module_key'          => 'chairs',
                'name_english'        => 'Chair Upgrade',
                'name_french'         => 'Mise à niveau des sièges',
                'description_english' => 'Wider, more ergonomic chairs increase per-vehicle capacity and rider comfort.',
                'description_french'  => 'Des sièges plus larges et ergonomiques augmentent la capacité et le confort.',
                'cost'                => 100000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 200,
                'capacity_bonus'      => 2,
                'reputation_bonus'    => 5,
                'daily_cost_increase' => 150,
            ],
            [
                'module_key'          => 'bubble_cover',
                'name_english'        => 'Bubble Cover',
                'name_french'         => 'Bulle coupe-vent',
                'description_english' => 'Retractable bubble wind shields protect riders in poor weather, boosting resort reputation.',
                'description_french'  => 'Des bulles rétractables protègent les passagers par mauvais temps et améliorent la réputation.',
                'cost'                => 75000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 0,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 10,
                'daily_cost_increase' => 100,
            ],
            [
                'module_key'          => 'night_lighting',
                'name_english'        => 'Night Lighting',
                'name_french'         => 'Éclairage nocturne',
                'description_english' => 'LED floodlight rig allows safe night operations and significantly boosts resort reputation.',
                'description_french'  => 'Un système d\'éclairage LED permet les opérations de nuit et améliore nettement la réputation.',
                'cost'                => 200000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 0,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 15,
                'daily_cost_increase' => 250,
            ],
            [
                'module_key'          => 'rfid_gates',
                'name_english'        => 'RFID Gates',
                'name_french'         => 'Portiques RFID',
                'description_english' => 'Automated RFID boarding gates eliminate ticket queues, maximising passenger throughput.',
                'description_french'  => 'Les portiques RFID automatisés suppriment les files d\'attente et maximisent le débit.',
                'cost'                => 50000,
                'speed_bonus'         => 0.00,
                'throughput_bonus'    => 400,
                'capacity_bonus'      => 0,
                'reputation_bonus'    => 5,
                'daily_cost_increase' => 50,
            ],
        ];

        $keys = array_column($modules, 'module_key');
        $existing_rows = $this->db
            ->select('module_key')
            ->where_in('module_key', $keys)
            ->get('game_lift_module_upgrades')
            ->result_array();
        $existing_keys = array_column($existing_rows, 'module_key');

        foreach ($modules as $module) {
            if (!in_array($module['module_key'], $existing_keys, TRUE)) {
                $this->db->insert('game_lift_module_upgrades', $module);
            } else {
                $this->db
                    ->where('module_key', $module['module_key'])
                    ->update('game_lift_module_upgrades', $module);
            }
        }

        // ----------------------------------------------------------------
        // 4. Ensure the player-installed modules table also exists
        // ----------------------------------------------------------------
        if (!$this->db->table_exists('game_created_lift_modules')) {
            $this->dbforge->add_field([
                'id_lift_module' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'id_resort' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'id_created_lifts' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'id_module' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'installed_at' => [
                    'type'    => 'TIMESTAMP',
                    'default' => 'CURRENT_TIMESTAMP',
                ],
            ]);
            $this->dbforge->add_key('id_lift_module', true);
            $this->dbforge->add_key('id_created_lifts');
            $this->dbforge->add_key('id_resort');
            $this->dbforge->create_table('game_created_lift_modules');
        }
    }

    public function down() {
        // Intentionally empty: dropping catalogue columns would destroy data
        // that cannot be trivially reconstructed. To fully roll back this
        // feature, run the down() of 20260222130000_add_lift_modular_upgrades.
    }
}
