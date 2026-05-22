<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Altitude & Microclimate System
 *
 * Adds two columns to `game_resorts`:
 *   - altitude  ENUM('low','medium','high')  DEFAULT 'medium'
 *   - aspect    ENUM('north','south','east','west') DEFAULT 'north'
 *
 * altitude effects (applied nightly per resort):
 *   low    – less snow reliability: snow accumulation  × 0.8, melt × 1.2
 *   medium – baseline (× 1.0)
 *   high   – more snow reliability: snow accumulation  × 1.2, melt × 0.8;
 *            +30 % build cost for lifts and slopes
 *
 * aspect effects (applied nightly per resort):
 *   north  – retains snow: melt × 0.8, accumulation × 1.0
 *   south  – melts faster: melt × 1.3, accumulation × 1.0
 *   east / west – neutral (× 1.0)
 */
class Migration_Add_altitude_microclimate extends CI_Migration {

    public function up() {
        // Add altitude column if not present
        $fields_altitude = [
            'altitude' => [
                'type'       => "ENUM('low','medium','high')",
                'default'    => 'medium',
                'null'       => false,
                'after'      => 'resort_description',
            ],
        ];
        if (!$this->db->field_exists('altitude', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', $fields_altitude);
        }

        // Add aspect column if not present
        $fields_aspect = [
            'aspect' => [
                'type'    => "ENUM('north','south','east','west')",
                'default' => 'north',
                'null'    => false,
                'after'   => 'altitude',
            ],
        ];
        if (!$this->db->field_exists('aspect', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', $fields_aspect);
        }
    }

    public function down() {
        if ($this->db->field_exists('aspect', 'game_resorts')) {
            $this->dbforge->drop_column('game_resorts', 'aspect');
        }
        if ($this->db->field_exists('altitude', 'game_resorts')) {
            $this->dbforge->drop_column('game_resorts', 'altitude');
        }
    }
}
