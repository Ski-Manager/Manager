<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Daily Login Bonus columns to game_players
 *
 * Adds two columns used to track per-player daily login streaks:
 *   - login_streak    : consecutive-day login count (resets when a day is skipped)
 *   - last_bonus_date : UTC date the last daily bonus was claimed (NULL = never claimed)
 */
class Migration_Add_daily_login_bonus extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_players', [
            'login_streak' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'last_bonus_date' => [
                'type'    => 'DATE',
                'null'    => TRUE,
                'default' => NULL,
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_players', 'login_streak');
        $this->dbforge->drop_column('game_players', 'last_bonus_date');
    }
}
