<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add torchlight descent and night photography package columns to game_resorts
 *
 * night_skiing_torchlight     TINYINT(1)  DEFAULT 0 – torchlight descent on/off
 * night_skiing_photo_enabled  TINYINT(1)  DEFAULT 0 – night photography package on/off
 * night_skiing_photo_price    INT(4)      DEFAULT 0 – photography package price per person (€)
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `night_skiing_torchlight`    TINYINT(1) NOT NULL DEFAULT 0,
 *     ADD COLUMN `night_skiing_photo_enabled` TINYINT(1) NOT NULL DEFAULT 0,
 *     ADD COLUMN `night_skiing_photo_price`   INT(4)     NOT NULL DEFAULT 0;
 */
class Migration_Add_night_skiing_extra_features extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'night_skiing_torchlight' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_weather_suspend',
            ],
            'night_skiing_photo_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_torchlight',
            ],
            'night_skiing_photo_price' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'night_skiing_photo_enabled',
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'night_skiing_torchlight');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_photo_enabled');
        $this->dbforge->drop_column('game_resorts', 'night_skiing_photo_price');
    }
}
