<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add 8-person chairlift and 12-person gondola to game_lifts
 *
 * Inserts 3 upgrade levels for each new lift variant (if not already present):
 *   - 8-person detachable chairlift  (Chair Lift, Grip 2, capacity 8)
 *   - 12-person gondola              (Cabin Lift, Grip 2, capacity 12)
 *
 * The migration resolves lift-type and grip-type IDs dynamically so it is not
 * coupled to hard-coded primary-key values.
 *
 * SQL equivalent (example – actual IDs resolved at runtime):
 *   INSERT INTO `game_lifts` (`id_group`, `level`, `lift_type`, `grip_type`,
 *     `name_english`, `name_french`, `building_time`, `base_cost`,
 *     `meter_cost`, `speed`, `reputation`, `capacity`, `throughput`,
 *     `daily_cost`)
 *   VALUES (...) -- repeated for levels 1-3 of each variant
 */
class Migration_Add_8p_chairlift_12p_gondola extends CI_Migration {

    // ---------------------------------------------------------------
    // Lift-variant definitions
    // Each entry: [lift_type_name, grip_type_id, capacity, levels[]]
    // levels[] index 0 = level 1, index 1 = level 2, index 2 = level 3
    // ---------------------------------------------------------------
    private $variants = [
        [
            'lift_type_name' => 'Chair Lift',
            'grip_type'      => 2,
            'capacity'       => 8,
            'levels' => [
                // level 1
                [
                    'name_english'  => '8-Person Chairlift',
                    'name_french'   => 'Télésiège 8 places',
                    'speed'         => 5,
                    'throughput'    => 2800,
                    'building_time' => 432000,
                    'base_cost'     => 1500000,
                    'meter_cost'    => 900,
                    'reputation'    => 45,
                    'daily_cost'    => 2200,
                ],
                // level 2
                [
                    'name_english'  => '8-Person Chairlift Plus',
                    'name_french'   => 'Télésiège 8 places Plus',
                    'speed'         => 6,
                    'throughput'    => 3200,
                    'building_time' => 518400,
                    'base_cost'     => 2000000,
                    'meter_cost'    => 1100,
                    'reputation'    => 55,
                    'daily_cost'    => 2600,
                ],
                // level 3
                [
                    'name_english'  => '8-Person Chairlift HD',
                    'name_french'   => 'Télésiège 8 places HD',
                    'speed'         => 7,
                    'throughput'    => 3600,
                    'building_time' => 604800,
                    'base_cost'     => 2500000,
                    'meter_cost'    => 1300,
                    'reputation'    => 65,
                    'daily_cost'    => 3000,
                ],
            ],
        ],
        [
            'lift_type_name' => 'Cabin Lift',
            'grip_type'      => 2,
            'capacity'       => 12,
            'levels' => [
                // level 1
                [
                    'name_english'  => '12-Person Gondola',
                    'name_french'   => 'Télécabine 12 places',
                    'speed'         => 5,
                    'throughput'    => 2500,
                    'building_time' => 518400,
                    'base_cost'     => 2000000,
                    'meter_cost'    => 1000,
                    'reputation'    => 55,
                    'daily_cost'    => 2500,
                ],
                // level 2
                [
                    'name_english'  => '12-Person Gondola Plus',
                    'name_french'   => 'Télécabine 12 places Plus',
                    'speed'         => 6,
                    'throughput'    => 2800,
                    'building_time' => 604800,
                    'base_cost'     => 2500000,
                    'meter_cost'    => 1200,
                    'reputation'    => 65,
                    'daily_cost'    => 3000,
                ],
                // level 3
                [
                    'name_english'  => '12-Person Gondola HD',
                    'name_french'   => 'Télécabine 12 places HD',
                    'speed'         => 7,
                    'throughput'    => 3200,
                    'building_time' => 691200,
                    'base_cost'     => 3000000,
                    'meter_cost'    => 1400,
                    'reputation'    => 75,
                    'daily_cost'    => 3500,
                ],
            ],
        ],
    ];

    public function up() {
        foreach ($this->variants as $variant) {
            // Resolve lift_type id
            $lt = $this->db
                ->select('id_lift_type')
                ->from('game_lift_types')
                ->where('name_english', $variant['lift_type_name'])
                ->limit(1)
                ->get()
                ->row();

            if (!$lt) {
                // lift type not found; skip this variant
                continue;
            }
            $lift_type_id = $lt->id_lift_type;

            // Check if this variant already exists (level 1 is sufficient)
            $exists = $this->db
                ->from('game_lifts')
                ->where('lift_type', $lift_type_id)
                ->where('grip_type', $variant['grip_type'])
                ->where('capacity',  $variant['capacity'])
                ->where('level', 1)
                ->count_all_results();

            if ($exists > 0) {
                continue;
            }

            // Determine next available id_group
            $max = $this->db
                ->select_max('id_group')
                ->from('game_lifts')
                ->get()
                ->row();
            $id_group = ($max && $max->id_group !== null) ? (int) $max->id_group + 1 : 1;

            // Insert 3 levels
            foreach ($variant['levels'] as $i => $lvl) {
                $this->db->insert('game_lifts', [
                    'id_group'      => $id_group,
                    'level'         => $i + 1,
                    'lift_type'     => $lift_type_id,
                    'grip_type'     => $variant['grip_type'],
                    'name_english'  => $lvl['name_english'],
                    'name_french'   => $lvl['name_french'],
                    'speed'         => $lvl['speed'],
                    'throughput'    => $lvl['throughput'],
                    'building_time' => $lvl['building_time'],
                    'base_cost'     => $lvl['base_cost'],
                    'meter_cost'    => $lvl['meter_cost'],
                    'reputation'    => $lvl['reputation'],
                    'capacity'      => $variant['capacity'],
                    'daily_cost'    => $lvl['daily_cost'],
                ]);
            }
        }
    }

    public function down() {
        foreach ($this->variants as $variant) {
            $lt = $this->db
                ->select('id_lift_type')
                ->from('game_lift_types')
                ->where('name_english', $variant['lift_type_name'])
                ->limit(1)
                ->get()
                ->row();

            if (!$lt) {
                continue;
            }

            // Identify the id_group that was inserted by this migration by
            // matching the level-1 English name, so we only remove what we added.
            $row = $this->db
                ->select('id_group')
                ->from('game_lifts')
                ->where('lift_type', $lt->id_lift_type)
                ->where('grip_type', $variant['grip_type'])
                ->where('capacity',  $variant['capacity'])
                ->where('level',     1)
                ->where('name_english', $variant['levels'][0]['name_english'])
                ->limit(1)
                ->get()
                ->row();

            if (!$row) {
                continue;
            }

            $this->db
                ->where('id_group', $row->id_group)
                ->delete('game_lifts');
        }
    }
}
