<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add water_reservoir column and snowmaker staff type
 *
 * 1. Adds `water_reservoir` TINYINT(0-100) column to game_resorts.
 *    Represents the percentage of the resort's water supply available
 *    for snowmaking. Starts full (100). Depleted by active cannons/
 *    trail equipment each night; replenished by precipitation.
 *
 * 2. Inserts a 'snowmaker' staff type into game_staff so resorts can
 *    hire snowmaking operators. At least SNOWMAKING_MIN_STAFF snowmakers
 *    must be hired for artificial snow production to run each night.
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `water_reservoir` TINYINT NOT NULL DEFAULT 100
 *     AFTER `cannon_auto_start`;
 *
 *   INSERT INTO `game_staff` (position, name_english, name_french, salary, efficiency)
 *   VALUES ('snowmaker', 'Snowmaking Operator', 'Opérateur d\'enneigement', 250, 100)
 *   ON DUPLICATE KEY UPDATE position = position;
 */
class Migration_Add_water_reservoir extends CI_Migration {

    public function up() {
        // 1. Add water_reservoir column (only if missing)
        if (!$this->db->field_exists('water_reservoir', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', [
                'water_reservoir' => [
                    'type'       => 'TINYINT',
                    'constraint' => 3,
                    'null'       => FALSE,
                    'default'    => 100,
                    'after'      => 'cannon_auto_start',
                ],
            ]);
        }

        // 2. Seed 'snowmaker' staff type if not already present
        $exists = $this->db
            ->from('game_staff')
            ->where('position', 'snowmaker')
            ->count_all_results();

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

    public function down() {
        // Remove water_reservoir column
        if ($this->db->field_exists('water_reservoir', 'game_resorts')) {
            $this->dbforge->drop_column('game_resorts', 'water_reservoir');
        }

        // Remove the seeded snowmaker staff type
        $this->db->where('position', 'snowmaker')->delete('game_staff');
    }
}
