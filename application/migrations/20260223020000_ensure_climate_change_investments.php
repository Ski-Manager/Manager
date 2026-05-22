<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Ensure adaptation-investment columns exist on game_climate_change.
 *
 * The original migration (20260223000000_add_climate_change.php) uses
 * CREATE TABLE IF NOT EXISTS, so databases that already had the table (created
 * without the investment columns) never received those columns.
 * This migration adds them idempotently via dbforge->add_column().
 */
class Migration_Ensure_climate_change_investments extends CI_Migration {

    public function up() {
        $existing = $this->db->list_fields('game_climate_change');

        if (!in_array('snowmaking_invest', $existing, TRUE)) {
            $this->dbforge->add_column('game_climate_change', [
                'snowmaking_invest' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                    'after'      => 'climate_level',
                ],
            ]);
        }

        if (!in_array('altitude_invest', $existing, TRUE)) {
            $this->dbforge->add_column('game_climate_change', [
                'altitude_invest' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                    'after'      => 'snowmaking_invest',
                ],
            ]);
        }

        if (!in_array('diversify_invest', $existing, TRUE)) {
            $this->dbforge->add_column('game_climate_change', [
                'diversify_invest' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 0,
                    'after'      => 'altitude_invest',
                ],
            ]);
        }
    }

    public function down() {
        // Intentionally empty: dropping these columns would destroy player
        // investment data. To fully roll back, run the down() of
        // 20260223000000_add_climate_change instead.
    }
}
