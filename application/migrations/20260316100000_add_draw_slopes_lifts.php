<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add draw_slopes_lifts
 *
 * Adds an `id_resort` column (nullable INT UNSIGNED) to game_slopes and
 * game_locations so that player-drawn slopes and lifts can be scoped to a
 * specific resort.
 *
 * - NULL  → admin-created global entry (visible to all players with sector access)
 * - > 0   → player-drawn entry, visible/buildable only by that resort
 */
class Migration_Add_draw_slopes_lifts extends CI_Migration {

    public function up() {
        // ── game_slopes ────────────────────────────────────────────────────────
        if (!$this->db->field_exists('id_resort', 'game_slopes')) {
            $this->dbforge->add_column('game_slopes', [
                'id_resort' => [
                    'type'       => 'INT',
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                    'default'    => NULL,
                    'after'      => 'id_slope',
                ],
            ]);
        }

        // ── game_locations ─────────────────────────────────────────────────────
        if (!$this->db->field_exists('id_resort', 'game_locations')) {
            $this->dbforge->add_column('game_locations', [
                'id_resort' => [
                    'type'       => 'INT',
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                    'default'    => NULL,
                    'after'      => 'id_location',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('id_resort', 'game_slopes')) {
            $this->dbforge->drop_column('game_slopes', 'id_resort');
        }
        if ($this->db->field_exists('id_resort', 'game_locations')) {
            $this->dbforge->drop_column('game_locations', 'id_resort');
        }
    }
}
