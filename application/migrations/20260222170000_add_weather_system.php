<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Weather System tables
 *
 * Creates the three tables that power the Weather System and seeds the
 * game_weather_conditions reference table with 35 weather condition records.
 *
 * Tables created:
 *   - game_weather_conditions  : reference data for each weather type
 *   - game_weather_forecast    : per-date forecast entries (condition + date)
 *   - game_extended_forecast   : player subscriptions to the 14-day forecast
 *
 * SQL equivalent:
 *   CREATE TABLE `game_weather_conditions` (
 *     `id_condition` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `name_english` VARCHAR(50) NOT NULL,
 *     `name_french`  VARCHAR(50) NOT NULL,
 *     `snow_level`   INT NOT NULL DEFAULT 0,
 *     `temperature`  INT NOT NULL DEFAULT 0,
 *     `wind_strength` INT UNSIGNED NOT NULL DEFAULT 0,
 *     `danger`       TINYINT(1) NOT NULL DEFAULT 0
 *   );
 *   CREATE TABLE `game_weather_forecast` (
 *     `id_forecast`  INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `date`         DATE NOT NULL UNIQUE,
 *     `id_condition` INT UNSIGNED NOT NULL
 *   );
 *   CREATE TABLE `game_extended_forecast` (
 *     `id_extended_forecast` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     `id_player`    INT UNSIGNED NOT NULL,
 *     `end_forecast` DATE NOT NULL
 *   );
 */
class Migration_Add_weather_system extends CI_Migration {

    // ------------------------------------------------------------------
    // 35 weather conditions used by calculate_weather() in NightlyMainJobs.
    // Weights in that cron: 1=>3,2=>3,3=>4,4=>4,5=>3,6=>3,7=>3,8=>3,
    //   9=>2,10=>2,11=>2,12=>1,13=>1,14=>1,15=>3,16=>5,17=>3,18=>3,
    //   19=>3,20=>3,21=>3,22=>3,23=>3,24=>3,25=>1,26=>1,27=>3,28=>3,
    //   29=>3,30=>3,31=>3,32=>3,33=>5,34=>4,35=>4  (sum = 100)
    // snow_level: daily change in cm (positive = snowfall, negative = melt)
    // temperature: °C   wind_strength: m/s   danger: 0|1
    // ------------------------------------------------------------------
    private $conditions = [
        // id  name_english  name_french         snow  temp  wind  danger
        [1,  'Sunny',    'Ensoleillé',    -2,   -5,   3,  0],
        [2,  'Sunny',    'Ensoleillé',    -3,    2,   2,  0],
        [3,  'Sunny',    'Ensoleillé',     0,   -3,   4,  0],
        [4,  'Sunny',    'Ensoleillé',    -1,    0,   3,  0],
        [5,  'Cloudy',   'Nuageux',        0,   -4,   5,  0],
        [6,  'Cloudy',   'Nuageux',       -2,    1,   6,  0],
        [7,  'Cloudy',   'Nuageux',        3,   -6,   4,  0],
        [8,  'Cloudy',   'Nuageux',        0,   -2,   7,  0],
        [9,  'Overcast', 'Couvert',       -3,    2,   8,  0],
        [10, 'Overcast', 'Couvert',        2,   -5,   6,  0],
        [11, 'Fog',      'Brouillard',     0,   -1,   3,  0],
        [12, 'Blizzard', 'Blizzard',      15,  -18,  25,  1],
        [13, 'Storm',    'Orageux',        -5,   4,  30,  1],
        [14, 'Blizzard', 'Blizzard',      20,  -20,  28,  1],
        [15, 'Snowing',  'Neigeux',        5,   -6,   5,  0],
        [16, 'Snowing',  'Neigeux',        8,   -8,   4,  0],
        [17, 'Snowing',  'Neigeux',        3,   -4,   6,  0],
        [18, 'Raining',  'Pluvieux',     -10,    5,  10,  0],
        [19, 'Raining',  'Pluvieux',      -8,    3,   8,  0],
        [20, 'Raining',  'Pluvieux',      -5,    4,   7,  0],
        [21, 'Fog',      'Brouillard',     0,    0,   2,  0],
        [22, 'Fog',      'Brouillard',     1,   -2,   3,  0],
        [23, 'Windy',    'Venteux',       -2,   -5,  18,  0],
        [24, 'Windy',    'Venteux',        0,   -3,  20,  0],
        [25, 'Storm',    'Orageux',        -3,   3,  28,  1],
        [26, 'Blizzard', 'Blizzard',      12,  -15,  22,  1],
        [27, 'Cloudy',   'Nuageux',        5,   -7,   5,  0],
        [28, 'Overcast', 'Couvert',        0,   -2,   7,  0],
        [29, 'Snowing',  'Neigeux',       10,  -10,   6,  0],
        [30, 'Sunny',    'Ensoleillé',     0,   -6,   3,  0],
        [31, 'Overcast', 'Couvert',       -4,    3,   9,  0],
        [32, 'Windy',    'Venteux',        2,   -7,  15,  0],
        [33, 'Cloudy',   'Nuageux',        2,   -4,   5,  0],
        [34, 'Sunny',    'Ensoleillé',     1,   -2,   3,  0],
        [35, 'Snowing',  'Neigeux',        6,   -7,   5,  0],
    ];

    public function up() {
        // ---- game_weather_conditions --------------------------------
        if (!$this->db->table_exists('game_weather_conditions')) {
            $this->dbforge->add_field([
                'id_condition' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'name_english' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'name_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'snow_level' => [
                    'type'    => 'INT',
                    'null'    => FALSE,
                    'default' => 0,
                ],
                'temperature' => [
                    'type'    => 'INT',
                    'null'    => FALSE,
                    'default' => 0,
                ],
                'wind_strength' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 0,
                ],
                'danger' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                ],
            ]);
            $this->dbforge->add_key('id_condition', TRUE);
            $this->dbforge->create_table('game_weather_conditions');
        }

        // Seed the conditions (skip if already populated)
        $existing = $this->db->count_all('game_weather_conditions');
        if ($existing === 0) {
            foreach ($this->conditions as $c) {
                $this->db->insert('game_weather_conditions', [
                    'id_condition'  => $c[0],
                    'name_english'  => $c[1],
                    'name_french'   => $c[2],
                    'snow_level'    => $c[3],
                    'temperature'   => $c[4],
                    'wind_strength' => $c[5],
                    'danger'        => $c[6],
                ]);
            }
        }

        // ---- game_weather_forecast ----------------------------------
        if (!$this->db->table_exists('game_weather_forecast')) {
            $this->dbforge->add_field([
                'id_forecast' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'date' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'id_condition' => [
                    'type'     => 'INT',
                    'constraint' => 10,
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                ],
            ]);
            $this->dbforge->add_key('id_forecast', TRUE);
            $this->dbforge->add_key('date');
            $this->dbforge->create_table('game_weather_forecast');

            // Add unique constraint on date so each day has only one forecast
            $this->db->query('ALTER TABLE `game_weather_forecast` ADD UNIQUE KEY `uq_date` (`date`)');
        }

        // ---- game_extended_forecast ---------------------------------
        if (!$this->db->table_exists('game_extended_forecast')) {
            $this->dbforge->add_field([
                'id_extended_forecast' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'id_player' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                ],
                'end_forecast' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
            ]);
            $this->dbforge->add_key('id_extended_forecast', TRUE);
            $this->dbforge->add_key('id_player');
            $this->dbforge->create_table('game_extended_forecast');
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_extended_forecast', TRUE);
        $this->dbforge->drop_table('game_weather_forecast', TRUE);
        $this->dbforge->drop_table('game_weather_conditions', TRUE);
    }
}
