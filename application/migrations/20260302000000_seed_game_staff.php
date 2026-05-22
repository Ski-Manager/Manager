<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Seed game_staff table with the five standard position types
 *
 * The game_staff table stores the catalogue of staff available for hire
 * (the "employment agency"). Each row represents a tier/variant within a
 * position type, with increasing efficiency and salary.
 *
 * Without this seed the Hire-Staff page shows empty tables because
 * get_all_staff_DB() queries game_staff filtered by position and finds
 * no matching rows.
 *
 * Positions seeded: skipatrol, skiinstructor, liftmechanic,
 *                   mechanicGroomer, driver, snowmaker
 *
 * SQL equivalent (each row):
 *   INSERT IGNORE INTO `game_staff`
 *     (id_staff, position, name_english, name_french, efficiency, salary)
 *   VALUES (...)
 */
class Migration_Seed_game_staff extends CI_Migration {

    public function up() {
        $rows = [
            // ----- Ski Patrol (IDs 1–6) -----
            ['id_staff' =>  1, 'position' => 'skipatrol',      'name_english' => 'Ski patrol 1',              'name_french' => 'Pisteur secouriste 1',           'efficiency' =>  50, 'salary' => 1500],
            ['id_staff' =>  2, 'position' => 'skipatrol',      'name_english' => 'Ski patrol 2',              'name_french' => 'Pisteur secouriste 2',           'efficiency' =>  60, 'salary' => 1700],
            ['id_staff' =>  3, 'position' => 'skipatrol',      'name_english' => 'Ski patrol 3',              'name_french' => 'Pisteur secouriste 3',           'efficiency' =>  70, 'salary' => 1900],
            ['id_staff' =>  4, 'position' => 'skipatrol',      'name_english' => 'Ski patrol 4',              'name_french' => 'Pisteur secouriste 4',           'efficiency' =>  80, 'salary' => 2100],
            ['id_staff' =>  5, 'position' => 'skipatrol',      'name_english' => 'Ski patrol 5',              'name_french' => 'Pisteur secouriste 5',           'efficiency' =>  90, 'salary' => 2300],
            ['id_staff' =>  6, 'position' => 'skipatrol',      'name_english' => 'Ski patrol 6',              'name_french' => 'Pisteur secouriste 6',           'efficiency' => 100, 'salary' => 2500],
            // ----- Snow Groomer Mechanic (IDs 7–12) -----
            ['id_staff' =>  7, 'position' => 'mechanicGroomer','name_english' => 'Snow groomer mechanic 1',   'name_french' => 'Mecanicien dameuse 1',           'efficiency' =>  50, 'salary' => 1800],
            ['id_staff' =>  8, 'position' => 'mechanicGroomer','name_english' => 'Snow groomer mechanic 2',   'name_french' => 'Mecanicien dameuse 2',           'efficiency' =>  60, 'salary' => 2000],
            ['id_staff' =>  9, 'position' => 'mechanicGroomer','name_english' => 'Snow groomer mechanic 3',   'name_french' => 'Mecanicien dameuse 3',           'efficiency' =>  70, 'salary' => 2200],
            ['id_staff' => 10, 'position' => 'mechanicGroomer','name_english' => 'Snow groomer mechanic 4',   'name_french' => 'Mecanicien dameuse 4',           'efficiency' =>  80, 'salary' => 2400],
            ['id_staff' => 11, 'position' => 'mechanicGroomer','name_english' => 'Snow groomer mechanic 5',   'name_french' => 'Mecanicien dameuse 5',           'efficiency' =>  90, 'salary' => 2600],
            ['id_staff' => 12, 'position' => 'mechanicGroomer','name_english' => 'Snow groomer mechanic 6',   'name_french' => 'Mecanicien dameuse 6',           'efficiency' => 100, 'salary' => 2800],
            // ----- Ski/Snowboard Instructor (IDs 13–18) -----
            ['id_staff' => 13, 'position' => 'skiinstructor',  'name_english' => 'Ski/Snowboard instructor 1','name_french' => 'Moniteur de ski/Snowboard 1',    'efficiency' =>  50, 'salary' => 2000],
            ['id_staff' => 14, 'position' => 'skiinstructor',  'name_english' => 'Ski/Snowboard instructor 2','name_french' => 'Moniteur de ski/Snowboard 2',    'efficiency' =>  60, 'salary' => 2200],
            ['id_staff' => 15, 'position' => 'skiinstructor',  'name_english' => 'Ski/Snowboard instructor 3','name_french' => 'Moniteur de ski/Snowboard 3',    'efficiency' =>  70, 'salary' => 2400],
            ['id_staff' => 16, 'position' => 'skiinstructor',  'name_english' => 'Ski/Snowboard instructor 4','name_french' => 'Moniteur de ski/Snowboard 4',    'efficiency' =>  80, 'salary' => 2600],
            ['id_staff' => 17, 'position' => 'skiinstructor',  'name_english' => 'Ski/Snowboard instructor 5','name_french' => 'Moniteur de ski/Snowboard 5',    'efficiency' =>  90, 'salary' => 2800],
            ['id_staff' => 18, 'position' => 'skiinstructor',  'name_english' => 'Ski/Snowboard instructor 6','name_french' => 'Moniteur de ski/Snowboard 6',    'efficiency' => 100, 'salary' => 3000],
            // ----- Lift Mechanic (IDs 19–24) -----
            ['id_staff' => 19, 'position' => 'liftmechanic',   'name_english' => 'Lift mechanic 1',           'name_french' => 'Mécanicien de remonté 1',        'efficiency' =>  50, 'salary' => 1500],
            ['id_staff' => 20, 'position' => 'liftmechanic',   'name_english' => 'Lift mechanic 2',           'name_french' => 'Mécanicien de remonté 2',        'efficiency' =>  60, 'salary' => 1600],
            ['id_staff' => 21, 'position' => 'liftmechanic',   'name_english' => 'Lift mechanic 3',           'name_french' => 'Mécanicien de remonté 3',        'efficiency' =>  70, 'salary' => 1700],
            ['id_staff' => 22, 'position' => 'liftmechanic',   'name_english' => 'Lift mechanic 4',           'name_french' => 'Mécanicien de remonté 4',        'efficiency' =>  80, 'salary' => 1800],
            ['id_staff' => 23, 'position' => 'liftmechanic',   'name_english' => 'Lift mechanic 5',           'name_french' => 'Mécanicien de remonté 5',        'efficiency' =>  90, 'salary' => 1900],
            ['id_staff' => 24, 'position' => 'liftmechanic',   'name_english' => 'Lift mechanic 6',           'name_french' => 'Mécanicien de remonté 6',        'efficiency' => 100, 'salary' => 2000],
            // ----- Bus Driver (IDs 25–30) -----
            ['id_staff' => 25, 'position' => 'driver',         'name_english' => 'Bus driver 1',              'name_french' => 'Chauffeur de bus 1',             'efficiency' =>  50, 'salary' => 1500],
            ['id_staff' => 26, 'position' => 'driver',         'name_english' => 'Bus driver 2',              'name_french' => 'Chauffeur de bus 2',             'efficiency' =>  60, 'salary' => 1600],
            ['id_staff' => 27, 'position' => 'driver',         'name_english' => 'Bus driver 3',              'name_french' => 'Chauffeur de bus 3',             'efficiency' =>  70, 'salary' => 1700],
            ['id_staff' => 28, 'position' => 'driver',         'name_english' => 'Bus driver 4',              'name_french' => 'Chauffeur de bus 4',             'efficiency' =>  80, 'salary' => 1800],
            ['id_staff' => 29, 'position' => 'driver',         'name_english' => 'Bus driver 5',              'name_french' => 'Chauffeur de bus 5',             'efficiency' =>  90, 'salary' => 1900],
            ['id_staff' => 30, 'position' => 'driver',         'name_english' => 'Bus driver 6',              'name_french' => 'Chauffeur de bus 6',             'efficiency' => 100, 'salary' => 2000],
            // ----- Snowmaking Operator (IDs 31–33) -----
            ['id_staff' => 31, 'position' => 'snowmaker',      'name_english' => 'Basic Snowmaking Operator',  'name_french' => 'Opérateur d\'enneigement Débutant', 'efficiency' =>  50, 'salary' => 1800],
            ['id_staff' => 32, 'position' => 'snowmaker',      'name_english' => 'Skilled Snowmaking Operator','name_french' => 'Opérateur d\'enneigement Qualifié',  'efficiency' =>  70, 'salary' => 2700],
            ['id_staff' => 33, 'position' => 'snowmaker',      'name_english' => 'Expert Snowmaking Operator', 'name_french' => 'Opérateur d\'enneigement Expert',    'efficiency' =>  90, 'salary' => 3600],
        ];

        foreach ($rows as $row) {
            $exists = $this->db
                ->where('id_staff', $row['id_staff'])
                ->count_all_results('game_staff');
            if ($exists === 0) {
                $this->db->insert('game_staff', $row);
            }
        }
    }

    public function down() {
        $ids = range(1, 33);
        $this->db->where_in('id_staff', $ids)->delete('game_staff');
    }
}
