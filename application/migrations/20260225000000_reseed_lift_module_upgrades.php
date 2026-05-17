<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Reseed lift module upgrades catalogue
 *
 * Previous migrations left the game_lift_module_upgrades table in a broken
 * state (wrong number of rows, zero/null bonus values).  This migration
 * truncates the catalogue and re-inserts the five canonical module definitions
 * so that the Modular Upgrades table displays correct data.
 */
class Migration_Reseed_lift_module_upgrades extends CI_Migration {

    public function up() {
        // Safety net: ensure the table exists before truncating/inserting
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
        }

        // Clear all existing rows (removes duplicates and zero-value records)
        $this->db->truncate('game_lift_module_upgrades');

        // Insert the five canonical module definitions
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

        $this->db->insert_batch('game_lift_module_upgrades', $modules);
    }

    public function down() {
        // Re-truncate to undo the seeded rows. Note: this is intentionally
        // destructive for a catalogue table – any manually added rows will be
        // lost. The previous migration (20260224200000) will re-insert the
        // five canonical records if its up() is re-run.
        $this->db->truncate('game_lift_module_upgrades');
    }
}
