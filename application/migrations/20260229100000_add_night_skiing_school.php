<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add night ski school and weather auto-suspend columns to game_resorts
 *
 * night_skiing_school_enabled  TINYINT(1)  DEFAULT 0 – night ski school on/off
 * night_skiing_school_price    INT(4)      DEFAULT 0 – lesson price per person (€)
 * night_skiing_weather_suspend TINYINT(1)  DEFAULT 0 – auto-suspend on rain on/off
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `night_skiing_school_enabled`  TINYINT(1) NOT NULL DEFAULT 0,
 *     ADD COLUMN `night_skiing_school_price`    INT(4)     NOT NULL DEFAULT 0,
 *     ADD COLUMN `night_skiing_weather_suspend` TINYINT(1) NOT NULL DEFAULT 0;
 */
class Migration_Add_night_skiing_school extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'night_skiing_school_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_safety_level',
            ],
            'night_skiing_school_price' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_school_enabled',
            ],
            'night_skiing_weather_suspend' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_school_price',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'night_skiing_school_enabled');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_school_price');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_weather_suspend');
    }
}
