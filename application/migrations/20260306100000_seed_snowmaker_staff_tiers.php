<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Seed game_staff with tiered snowmaker (snowmaking operator) entries
 *
 * The original water-reservoir migration inserted a single snowmaker row with
 * efficiency=100 / salary=250 (a placeholder). This migration replaces that
 * single entry with three properly-tiered rows that match the structure used
 * by every other staff position (Basic / Skilled / Expert).
 *
 * After this migration the Hire Staff page's dynamically-rendered "Snowmaking
 * operator" tab will offer three hiring options instead of one.
 */
class Migration_Seed_snowmaker_staff_tiers extends CI_Migration {

    public function up() {
        // Remove the original placeholder row (efficiency=100, salary=250)
        $this->db
            ->where('position', 'snowmaker')
            ->where('efficiency', 100)
            ->where('salary', 250)
            ->delete('game_staff');

        $rows = [
            ['position' => 'snowmaker', 'name_english' => 'Basic Snowmaking Operator',   'name_french' => 'Opérateur d\'enneigement Débutant', 'efficiency' => 50,  'salary' => 1800],
            ['position' => 'snowmaker', 'name_english' => 'Skilled Snowmaking Operator',  'name_french' => 'Opérateur d\'enneigement Qualifié',  'efficiency' => 70,  'salary' => 2700],
            ['position' => 'snowmaker', 'name_english' => 'Expert Snowmaking Operator',   'name_french' => 'Opérateur d\'enneigement Expert',    'efficiency' => 90,  'salary' => 3600],
        ];

        foreach ($rows as $row) {
            $exists = $this->db
                ->where('position', $row['position'])
                ->where('name_english', $row['name_english'])
                ->count_all_results('game_staff');
            if ($exists === 0) {
                $this->db->insert('game_staff', $row);
            }
        }
    }

    public function down() {
        // Remove the tiered rows
        $this->db
            ->where('position', 'snowmaker')
            ->where_in('name_english', [
                'Basic Snowmaking Operator',
                'Skilled Snowmaking Operator',
                'Expert Snowmaking Operator',
            ])
            ->delete('game_staff');

        // Restore the original placeholder row
        $exists = $this->db
            ->where('position', 'snowmaker')
            ->count_all_results('game_staff');
        if ($exists === 0) {
            $this->db->insert('game_staff', [
                'position'     => 'snowmaker',
                'name_english' => 'Snowmaking Operator',
                'name_french'  => 'Opérateur d\'enneigement',
                'salary'       => 250,
                'efficiency'   => 100,
            ]);
        }
    }
}
