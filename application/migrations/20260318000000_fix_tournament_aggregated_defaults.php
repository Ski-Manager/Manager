<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Fix tournament aggregated column defaults
 *
 * Ensures aggregated_visitors and aggregated_revenue in game_started_tournaments
 * default to 0 (not NULL) so that incremental SQL updates (col = col + value)
 * work correctly. NULL + value = NULL in MySQL, which could silently produce
 * wrong totals and corrupt the player's cash when the tournament completes.
 */
class Migration_Fix_tournament_aggregated_defaults extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_started_tournaments')) {
            // Repair any existing NULL values before changing the column default
            $this->db->query(
                "UPDATE game_started_tournaments SET aggregated_visitors = 0 WHERE aggregated_visitors IS NULL"
            );
            $this->db->query(
                "UPDATE game_started_tournaments SET aggregated_revenue = 0 WHERE aggregated_revenue IS NULL"
            );

            $fields = [
                'aggregated_visitors' => [
                    'name'    => 'aggregated_visitors',
                    'type'    => 'INT',
                    'default' => 0,
                    'null'    => FALSE,
                ],
                'aggregated_revenue' => [
                    'name'    => 'aggregated_revenue',
                    'type'    => 'INT',
                    'default' => 0,
                    'null'    => FALSE,
                ],
            ];
            $this->dbforge->modify_column('game_started_tournaments', $fields);
        }
    }

    public function down() {
        // Reversing to nullable is safe; data is retained
        if ($this->db->table_exists('game_started_tournaments')) {
            $fields = [
                'aggregated_visitors' => [
                    'name' => 'aggregated_visitors',
                    'type' => 'INT',
                    'null' => TRUE,
                ],
                'aggregated_revenue' => [
                    'name' => 'aggregated_revenue',
                    'type' => 'INT',
                    'null' => TRUE,
                ],
            ];
            $this->dbforge->modify_column('game_started_tournaments', $fields);
        }
    }
}
