<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Revamp Hire Staff System
 *
 * 1. Creates the `game_staff_candidates` table – a rolling per-resort candidate
 *    pool served by the employment agency.  Each candidate is a unique named
 *    individual with a specialization, a personality trait, a contract option
 *    and an availability window.
 *
 * 2. Extends `game_hired_staff` with career-progression columns:
 *      - experience_points  – accumulated on-the-job experience
 *      - skill_level        – career level 1-5 (promotes when XP threshold is met)
 *      - contract_months    – duration of the employment contract (3 / 6 / 12)
 *      - contract_start     – date the contract was signed (= hire date by default)
 *      - specialization     – bonus area (speed / safety / precision / endurance /
 *                             trainer / tech)
 *      - trait              – personality trait affecting morale dynamics
 *                             (hardworking / easygoing / sensitive / ambitious /
 *                              loyal)
 *
 * SQL equivalents:
 *   CREATE TABLE `game_staff_candidates` (...)
 *   ALTER TABLE `game_hired_staff` ADD COLUMN `experience_points` ...
 */
class Migration_Revamp_hire_staff extends CI_Migration {

    // -------------------------------------------------------------------------
    // UP
    // -------------------------------------------------------------------------
    public function up() {
        // -----------------------------------------------------------------
        // 1. Create game_staff_candidates
        // -----------------------------------------------------------------
        if ( ! $this->db->table_exists('game_staff_candidates')) {
            $this->dbforge->add_field([
                'id_candidate' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                // NULL = generic/agency pool visible to all resorts
                'id_resort' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => TRUE,
                    'default'  => NULL,
                ],
                'position' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'name_english' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'name_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'efficiency' => [
                    'type'       => 'TINYINT',
                    'constraint' => 3,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 50,
                ],
                'salary' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                    'default'  => 2000,
                ],
                // One-time signing bonus paid on hire (can be 0)
                'hire_bonus' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                    'default'  => 0,
                ],
                // specialization key: speed | safety | precision | endurance | trainer | tech
                'specialization' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                // trait key: hardworking | easygoing | sensitive | ambitious | loyal
                'trait' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                // Minimum contract in months (3 / 6 / 12)
                'contract_months' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 3,
                ],
                // Date after which the candidate is no longer available
                'available_until' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                    'default' => NULL,
                ],
                // 0 = available, 1 = already hired
                'is_hired' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                ],
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => FALSE,
                    'default' => '2000-01-01 00:00:00',
                ],
            ]);
            $this->dbforge->add_key('id_candidate', TRUE);  // PRIMARY KEY
            $this->dbforge->add_key('id_resort');
            $this->dbforge->add_key('position');
            $this->dbforge->create_table('game_staff_candidates');
        }

        // -----------------------------------------------------------------
        // 2. Add career-progression columns to game_hired_staff
        // -----------------------------------------------------------------
        $existing = $this->db->list_fields('game_hired_staff');

        if ( ! in_array('experience_points', $existing)) {
            $this->dbforge->add_column('game_hired_staff', [
                'experience_points' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                    'default'  => 0,
                    'after'    => 'on_strike',
                ],
            ]);
        }

        if ( ! in_array('skill_level', $existing)) {
            $this->dbforge->add_column('game_hired_staff', [
                'skill_level' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 1,
                    'after'      => 'experience_points',
                ],
            ]);
        }

        if ( ! in_array('contract_months', $existing)) {
            $this->dbforge->add_column('game_hired_staff', [
                'contract_months' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 3,
                    'after'      => 'skill_level',
                ],
            ]);
        }

        if ( ! in_array('contract_start', $existing)) {
            $this->dbforge->add_column('game_hired_staff', [
                'contract_start' => [
                    'type'  => 'DATETIME',
                    'null'  => TRUE,
                    'default' => NULL,
                    'after' => 'contract_months',
                ],
            ]);
        }

        if ( ! in_array('specialization', $existing)) {
            $this->dbforge->add_column('game_hired_staff', [
                'specialization' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                    'default'    => NULL,
                    'after'      => 'contract_start',
                ],
            ]);
        }

        if ( ! in_array('trait', $existing)) {
            $this->dbforge->add_column('game_hired_staff', [
                'trait' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                    'default'    => NULL,
                    'after'      => 'specialization',
                ],
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // DOWN
    // -------------------------------------------------------------------------
    public function down() {
        // Drop the candidate pool table
        $this->dbforge->drop_table('game_staff_candidates', TRUE);

        // Remove the new columns from game_hired_staff
        foreach (['trait','specialization','contract_start','contract_months','skill_level','experience_points'] as $col) {
            if (in_array($col, $this->db->list_fields('game_hired_staff'))) {
                $this->dbforge->drop_column('game_hired_staff', $col);
            }
        }
    }
}
