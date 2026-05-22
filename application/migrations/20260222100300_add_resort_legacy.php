<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Resort Legacy System columns
 *
 * Adds:
 *   - `legacy_rating`     INT(3)       to game_resorts  (0-100 historical rating, NULL until earned)
 *   - `legendary_status`  TINYINT(1)   to game_resorts  (1 = Legendary Mountain unlocked)
 *   - `legacy_bonus_cash` INT(11)      to game_players  (cash bonus carried over to a new resort)
 *
 * SQL equivalents:
 *   ALTER TABLE `game_resorts`  ADD COLUMN `legacy_rating`     INT(3)       NULL     DEFAULT NULL AFTER `prestige`;
 *   ALTER TABLE `game_resorts`  ADD COLUMN `legendary_status`  TINYINT(1)   NOT NULL DEFAULT 0    AFTER `legacy_rating`;
 *   ALTER TABLE `game_players`  ADD COLUMN `legacy_bonus_cash` INT(11)      NOT NULL DEFAULT 0;
 */
class Migration_Add_resort_legacy extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'legacy_rating' => [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'prestige',
            ],
        ]);
        $this->dbforge->add_column('game_resorts', [
            'legendary_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => FALSE,
                'default'    => 0,
                'after'      => 'legacy_rating',
            ],
        ]);
        $this->dbforge->add_column('game_players', [
            'legacy_bonus_cash' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => FALSE,
                'default'    => 0,
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'legendary_status');
        $this->dbforge->drop_column('game_resorts', 'legacy_rating');
        $this->dbforge->drop_column('game_players', 'legacy_bonus_cash');
    }
}
