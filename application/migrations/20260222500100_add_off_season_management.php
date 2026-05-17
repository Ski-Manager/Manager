<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Off-Season Management buildings to game_buildings
 *
 * Adds 5 summer-activity building types (3 levels each) that generate
 * revenue year-round, independent of ski-season conditions:
 *
 *   - mountain_biking  : Mountain biking trails / park
 *   - hiking           : Hiking trail network
 *   - festival         : Festival grounds
 *   - wedding_venue    : Wedding venue
 *   - alpine_coaster   : Alpine coaster ride
 *
 * Also creates the game_resort_rev_off_season statistics table used by
 * NightlyMainJobs to track off-season daily revenue per resort.
 */
class Migration_Add_off_season_management extends CI_Migration {

    private $buildings = [
        // ----------------------------------------------------------------
        // Mountain Biking
        // ----------------------------------------------------------------
        [
            'type'          => 'mountain_biking',
            'level'         => 1,
            'name_english'  => 'Mountain Biking Trail',
            'name_french'   => 'Piste de VTT',
            'building_time' => 86400,
            'building_cost' => 400000,
            'reputation'    => 10,
            'capacity'      => 50,
            'max_income'    => 800,
            'daily_cost'    => 100,
        ],
        [
            'type'          => 'mountain_biking',
            'level'         => 2,
            'name_english'  => 'Mountain Biking Park',
            'name_french'   => 'Parc de VTT',
            'building_time' => 172800,
            'building_cost' => 800000,
            'reputation'    => 20,
            'capacity'      => 100,
            'max_income'    => 1600,
            'daily_cost'    => 200,
        ],
        [
            'type'          => 'mountain_biking',
            'level'         => 3,
            'name_english'  => 'Mountain Biking Resort',
            'name_french'   => 'Station de VTT',
            'building_time' => 345600,
            'building_cost' => 1500000,
            'reputation'    => 30,
            'capacity'      => 200,
            'max_income'    => 3000,
            'daily_cost'    => 400,
        ],
        // ----------------------------------------------------------------
        // Hiking
        // ----------------------------------------------------------------
        [
            'type'          => 'hiking',
            'level'         => 1,
            'name_english'  => 'Hiking Trail',
            'name_french'   => 'Sentier de randonnée',
            'building_time' => 86400,
            'building_cost' => 200000,
            'reputation'    => 8,
            'capacity'      => 80,
            'max_income'    => 400,
            'daily_cost'    => 50,
        ],
        [
            'type'          => 'hiking',
            'level'         => 2,
            'name_english'  => 'Hiking Network',
            'name_french'   => 'Réseau de randonnées',
            'building_time' => 172800,
            'building_cost' => 500000,
            'reputation'    => 16,
            'capacity'      => 160,
            'max_income'    => 900,
            'daily_cost'    => 100,
        ],
        [
            'type'          => 'hiking',
            'level'         => 3,
            'name_english'  => 'Guided Hiking Center',
            'name_french'   => 'Centre de randonnée guidée',
            'building_time' => 259200,
            'building_cost' => 1000000,
            'reputation'    => 25,
            'capacity'      => 320,
            'max_income'    => 1800,
            'daily_cost'    => 200,
        ],
        // ----------------------------------------------------------------
        // Festival
        // ----------------------------------------------------------------
        [
            'type'          => 'festival',
            'level'         => 1,
            'name_english'  => 'Festival Grounds',
            'name_french'   => 'Terrain de festival',
            'building_time' => 172800,
            'building_cost' => 600000,
            'reputation'    => 20,
            'capacity'      => 200,
            'max_income'    => 1500,
            'daily_cost'    => 200,
        ],
        [
            'type'          => 'festival',
            'level'         => 2,
            'name_english'  => 'Open-Air Festival Park',
            'name_french'   => 'Parc de festival en plein air',
            'building_time' => 259200,
            'building_cost' => 1200000,
            'reputation'    => 35,
            'capacity'      => 400,
            'max_income'    => 3000,
            'daily_cost'    => 400,
        ],
        [
            'type'          => 'festival',
            'level'         => 3,
            'name_english'  => 'Festival & Concert Hall',
            'name_french'   => 'Festival & Salle de concert',
            'building_time' => 432000,
            'building_cost' => 2500000,
            'reputation'    => 55,
            'capacity'      => 800,
            'max_income'    => 6000,
            'daily_cost'    => 800,
        ],
        // ----------------------------------------------------------------
        // Wedding Venue
        // ----------------------------------------------------------------
        [
            'type'          => 'wedding_venue',
            'level'         => 1,
            'name_english'  => 'Garden Wedding Venue',
            'name_french'   => 'Salle de mariage au jardin',
            'building_time' => 172800,
            'building_cost' => 500000,
            'reputation'    => 15,
            'capacity'      => 50,
            'max_income'    => 2000,
            'daily_cost'    => 150,
        ],
        [
            'type'          => 'wedding_venue',
            'level'         => 2,
            'name_english'  => 'Panorama Wedding Lodge',
            'name_french'   => 'Lodge panoramique de mariage',
            'building_time' => 259200,
            'building_cost' => 1000000,
            'reputation'    => 30,
            'capacity'      => 100,
            'max_income'    => 4000,
            'daily_cost'    => 300,
        ],
        [
            'type'          => 'wedding_venue',
            'level'         => 3,
            'name_english'  => 'Luxury Alpine Wedding Palace',
            'name_french'   => 'Palais de mariage alpin de luxe',
            'building_time' => 432000,
            'building_cost' => 2000000,
            'reputation'    => 50,
            'capacity'      => 200,
            'max_income'    => 8000,
            'daily_cost'    => 600,
        ],
        // ----------------------------------------------------------------
        // Alpine Coaster
        // ----------------------------------------------------------------
        [
            'type'          => 'alpine_coaster',
            'level'         => 1,
            'name_english'  => 'Alpine Coaster',
            'name_french'   => 'Luge alpine',
            'building_time' => 259200,
            'building_cost' => 800000,
            'reputation'    => 20,
            'capacity'      => 100,
            'max_income'    => 2000,
            'daily_cost'    => 200,
        ],
        [
            'type'          => 'alpine_coaster',
            'level'         => 2,
            'name_english'  => 'Alpine Coaster Plus',
            'name_french'   => 'Luge alpine Plus',
            'building_time' => 345600,
            'building_cost' => 1500000,
            'reputation'    => 35,
            'capacity'      => 200,
            'max_income'    => 4000,
            'daily_cost'    => 400,
        ],
        [
            'type'          => 'alpine_coaster',
            'level'         => 3,
            'name_english'  => 'Extreme Alpine Coaster',
            'name_french'   => 'Luge alpine extrême',
            'building_time' => 518400,
            'building_cost' => 3000000,
            'reputation'    => 50,
            'capacity'      => 300,
            'max_income'    => 7000,
            'daily_cost'    => 700,
        ],
    ];

    public function up() {
        // Create the per-resort off-season revenue statistics table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `game_resort_rev_off_season` (
                `id`        INT(11)      NOT NULL AUTO_INCREMENT,
                `id_resort` INT(11)      NOT NULL,
                `date`      DATE         NOT NULL,
                `rev_off_season` DECIMAL(15,2) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `idx_rev_off_season_resort_date` (`id_resort`, `date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        foreach ($this->buildings as $row) {
            // Only insert if this type+level combination does not exist yet
            $exists = $this->db
                ->where('type',  $row['type'])
                ->where('level', $row['level'])
                ->count_all_results('game_buildings');

            if ($exists === 0) {
                $this->db->insert('game_buildings', $row);
            }
        }
    }

    public function down() {
        $this->db->query("DROP TABLE IF EXISTS `game_resort_rev_off_season`");

        $types = array_unique(array_column($this->buildings, 'type'));
        foreach ($types as $type) {
            $this->db->delete('game_buildings', ['type' => $type]);
        }
    }
}
