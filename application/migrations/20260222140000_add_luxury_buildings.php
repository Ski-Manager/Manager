<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add luxury building type to game_buildings
 *
 * Inserts 3 levels for the new 'luxury' building type which represents
 * the Luxury Economy Layer: VIP chalets, helicopter skiing, private
 * instructors, and exclusive lounges.
 *
 * Design intent:
 *   - Small number of guests (low capacity, low PERC_TOURISTS_BUILDING = 5%)
 *   - Massive profit per guest (very high max_income)
 *
 * SQL equivalent:
 *   INSERT INTO `game_buildings` (`type`, `level`, `name_english`, `name_french`,
 *     `building_time`, `building_cost`, `capacity`, `reputation`, `max_income`,
 *     `daily_cost`)
 *   VALUES ('luxury', 1, 'VIP Chalet', 'Chalet VIP', 259200, 2000000, 20, 20, 40000, 0),
 *          ('luxury', 2, 'VIP Chalet with Heliport', 'Chalet VIP avec héliport', 432000, 5000000, 40, 35, 100000, 0),
 *          ('luxury', 3, 'Exclusive VIP Resort', 'Resort VIP Exclusif', 604800, 10000000, 60, 50, 200000, 0);
 */
class Migration_Add_luxury_buildings extends CI_Migration {

    private $levels = [
        [
            'level'          => 1,
            'name_english'   => 'VIP Chalet',
            'name_french'    => 'Chalet VIP',
            'building_time'  => 259200,   // 3 days in seconds (divided by ACCELERATOR_FACTOR at runtime)
            'building_cost'  => 2000000,  // 2 000 000 €
            'capacity'       => 20,       // 20 VIP guests
            'reputation'     => 20,
            'max_income'     => 40000,    // 40 000 € / day at full VIP occupancy
            'daily_cost'     => 0,
        ],
        [
            'level'          => 2,
            'name_english'   => 'VIP Chalet with Heliport',
            'name_french'    => 'Chalet VIP avec héliport',
            'building_time'  => 432000,   // 5 days
            'building_cost'  => 5000000,  // 5 000 000 €
            'capacity'       => 40,       // 40 VIP guests
            'reputation'     => 35,
            'max_income'     => 100000,   // 100 000 € / day at full VIP occupancy
            'daily_cost'     => 0,
        ],
        [
            'level'          => 3,
            'name_english'   => 'Exclusive VIP Resort',
            'name_french'    => 'Resort VIP Exclusif',
            'building_time'  => 604800,   // 7 days
            'building_cost'  => 10000000, // 10 000 000 €
            'capacity'       => 60,       // 60 VIP guests
            'reputation'     => 50,
            'max_income'     => 200000,   // 200 000 € / day at full VIP occupancy
            'daily_cost'     => 0,
        ],
    ];

    public function up() {
        foreach ($this->levels as $lvl) {
            // Skip if already inserted
            $exists = $this->db
                ->from('game_buildings')
                ->where('type', 'luxury')
                ->where('level', $lvl['level'])
                ->count_all_results();

            if ($exists > 0) {
                continue;
            }

            $this->db->insert('game_buildings', [
                'type'          => 'luxury',
                'level'         => $lvl['level'],
                'name_english'  => $lvl['name_english'],
                'name_french'   => $lvl['name_french'],
                'building_time' => $lvl['building_time'],
                'building_cost' => $lvl['building_cost'],
                'capacity'      => $lvl['capacity'],
                'reputation'    => $lvl['reputation'],
                'max_income'    => $lvl['max_income'],
                'daily_cost'    => $lvl['daily_cost'],
            ]);
        }
    }

    public function down() {
        $this->db->where('type', 'luxury')->delete('game_buildings');
    }
}
