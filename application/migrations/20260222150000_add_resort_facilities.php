<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Resort Facilities building type to game_buildings
 *
 * Inserts 3 levels of the 'facility' building type (if not already present):
 *   Level 1 – Basic Wellness Centre
 *   Level 2 – Spa & Fitness Complex
 *   Level 3 – Premium Resort Facility
 *
 * Each level has progressively higher cost, capacity, reputation and income.
 */
class Migration_Add_resort_facilities_v1 extends CI_Migration {

    private $facilities = [
        [
            'level'         => 1,
            'name_english'  => 'Basic Wellness Centre',
            'name_french'   => 'Centre de bien-être de base',
            'building_time' => 259200,   // 3 days in seconds
            'building_cost' => 400000,
            'reputation'    => 30,
            'capacity'      => 100,
            'max_income'    => 1500,
            'daily_cost'    => 200,
        ],
        [
            'level'         => 2,
            'name_english'  => 'Spa & Fitness Complex',
            'name_french'   => 'Complexe spa & fitness',
            'building_time' => 432000,   // 5 days in seconds
            'building_cost' => 750000,
            'reputation'    => 55,
            'capacity'      => 200,
            'max_income'    => 3000,
            'daily_cost'    => 400,
        ],
        [
            'level'         => 3,
            'name_english'  => 'Premium Resort Facility',
            'name_french'   => 'Équipement de station premium',
            'building_time' => 604800,   // 7 days in seconds
            'building_cost' => 1200000,
            'reputation'    => 80,
            'capacity'      => 350,
            'max_income'    => 5000,
            'daily_cost'    => 700,
        ],
    ];

    public function up() {
        // Check if the facility type already exists in game_buildings
        $exists = $this->db
            ->from('game_buildings')
            ->where('type', 'facility')
            ->count_all_results();

        if ($exists > 0) {
            return;
        }

        foreach ($this->facilities as $facility) {
            $this->db->insert('game_buildings', [
                'type'          => 'facility',
                'level'         => $facility['level'],
                'name_english'  => $facility['name_english'],
                'name_french'   => $facility['name_french'],
                'building_time' => $facility['building_time'],
                'building_cost' => $facility['building_cost'],
                'reputation'    => $facility['reputation'],
                'capacity'      => $facility['capacity'],
                'max_income'    => $facility['max_income'],
                'daily_cost'    => $facility['daily_cost'],
            ]);
        }
    }

    public function down() {
        $this->db
            ->where('type', 'facility')
            ->delete('game_buildings');
    }
}
