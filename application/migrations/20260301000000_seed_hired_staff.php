<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Seed game_hired_staff table with initial data
 *
 * Inserts the baseline hired-staff records so that the Hire Staff
 * overview table is populated on a fresh install.
 *
 * SQL equivalent:
 *   INSERT IGNORE INTO `game_hired_staff`
 *     (id_hired_staff, id_resort, id_staff, id_item_assigned,
 *      type_item_assigned, morale, on_strike, date_hired)
 *   VALUES ...
 */
class Migration_Seed_hired_staff extends CI_Migration {

    public function up() {
        $rows = [
            ['id_hired_staff' =>  6, 'id_resort' =>  2, 'id_staff' => 23, 'id_item_assigned' =>  2, 'type_item_assigned' => 'lift',    'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 22:26:37'],
            ['id_hired_staff' =>  7, 'id_resort' =>  2, 'id_staff' => 11, 'id_item_assigned' =>  2, 'type_item_assigned' => 'groomer', 'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 22:26:44'],
            ['id_hired_staff' =>  8, 'id_resort' =>  4, 'id_staff' =>  1, 'id_item_assigned' =>  3, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 23:33:06'],
            ['id_hired_staff' =>  9, 'id_resort' =>  4, 'id_staff' => 13, 'id_item_assigned' =>  1, 'type_item_assigned' => 'sector',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 23:33:15'],
            ['id_hired_staff' => 10, 'id_resort' =>  4, 'id_staff' =>  1, 'id_item_assigned' =>  4, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 23:33:25'],
            ['id_hired_staff' => 11, 'id_resort' =>  4, 'id_staff' =>  1, 'id_item_assigned' =>  6, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 23:33:35'],
            ['id_hired_staff' => 12, 'id_resort' =>  4, 'id_staff' => 25, 'id_item_assigned' =>  4, 'type_item_assigned' => 'skibus',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 23:33:54'],
            ['id_hired_staff' => 13, 'id_resort' =>  4, 'id_staff' =>  7, 'id_item_assigned' =>  3, 'type_item_assigned' => 'groomer', 'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-07 23:34:00'],
            ['id_hired_staff' => 14, 'id_resort' =>  4, 'id_staff' => 19, 'id_item_assigned' =>  3, 'type_item_assigned' => 'lift',    'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-08 08:12:17'],
            ['id_hired_staff' => 15, 'id_resort' =>  4, 'id_staff' => 24, 'id_item_assigned' =>  4, 'type_item_assigned' => 'lift',    'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-08 17:50:20'],
            ['id_hired_staff' => 16, 'id_resort' =>  4, 'id_staff' => 12, 'id_item_assigned' =>  6, 'type_item_assigned' => 'groomer', 'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-08 17:50:32'],
            ['id_hired_staff' => 18, 'id_resort' =>  4, 'id_staff' =>  1, 'id_item_assigned' =>  7, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-13 11:16:33'],
            ['id_hired_staff' => 19, 'id_resort' =>  9, 'id_staff' =>  1, 'id_item_assigned' =>  8, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-26 11:16:01'],
            ['id_hired_staff' => 20, 'id_resort' =>  9, 'id_staff' =>  7, 'id_item_assigned' =>  9, 'type_item_assigned' => 'groomer', 'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-26 11:18:18'],
            ['id_hired_staff' => 21, 'id_resort' =>  9, 'id_staff' => 19, 'id_item_assigned' =>  8, 'type_item_assigned' => 'lift',    'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-26 11:20:35'],
            ['id_hired_staff' => 22, 'id_resort' =>  9, 'id_staff' => 25, 'id_item_assigned' => 10, 'type_item_assigned' => 'skibus',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-26 11:28:35'],
            ['id_hired_staff' => 23, 'id_resort' =>  9, 'id_staff' => 13, 'id_item_assigned' =>  1, 'type_item_assigned' => 'sector',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-09-26 11:31:18'],
            ['id_hired_staff' => 24, 'id_resort' => 13, 'id_staff' =>  1, 'id_item_assigned' => 11, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-05 02:26:27'],
            ['id_hired_staff' => 25, 'id_resort' => 13, 'id_staff' => 13, 'id_item_assigned' =>  1, 'type_item_assigned' => 'sector',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-05 02:36:34'],
            ['id_hired_staff' => 26, 'id_resort' => 13, 'id_staff' => 19, 'id_item_assigned' => 11, 'type_item_assigned' => 'lift',    'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-05 02:36:46'],
            ['id_hired_staff' => 27, 'id_resort' => 13, 'id_staff' =>  7, 'id_item_assigned' => 11, 'type_item_assigned' => 'groomer', 'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-05 02:36:51'],
            ['id_hired_staff' => 28, 'id_resort' => 13, 'id_staff' => 25, 'id_item_assigned' => 12, 'type_item_assigned' => 'skibus',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-05 02:40:55'],
            // id_hired_staff=36: hired but not yet assigned to any item (NULL is valid for unassigned staff)
            ['id_hired_staff' => 36, 'id_resort' => 15, 'id_staff' =>  1, 'id_item_assigned' => NULL, 'type_item_assigned' => NULL,   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-06 10:49:37'],
            ['id_hired_staff' => 37, 'id_resort' =>  2, 'id_staff' => 28, 'id_item_assigned' => 15, 'type_item_assigned' => 'skibus',  'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-07 14:22:21'],
            ['id_hired_staff' => 38, 'id_resort' => 13, 'id_staff' =>  1, 'id_item_assigned' => 13, 'type_item_assigned' => 'slope',   'morale' => 100, 'on_strike' => 0, 'date_hired' => '2018-10-08 11:04:24'],
        ];

        foreach ($rows as $row) {
            // Only insert if a record with this primary key does not already exist
            $exists = $this->db
                ->where('id_hired_staff', $row['id_hired_staff'])
                ->count_all_results('game_hired_staff');
            if ($exists === 0) {
                $this->db->insert('game_hired_staff', $row);
            }
        }
    }

    public function down() {
        $ids = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 18,
                19, 20, 21, 22, 23, 24, 25, 26, 27, 28,
                36, 37, 38];
        $this->db->where_in('id_hired_staff', $ids)->delete('game_hired_staff');
    }
}
