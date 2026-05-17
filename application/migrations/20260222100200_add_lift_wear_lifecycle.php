<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add lift wear & lifecycle support
 *
 * Adds an `install_date` column to `game_created_lifts` that records when
 * each lift was first installed (construction completed).  This date is used
 * by the nightly-job and the lift controller to calculate:
 *   - Lift age in game-seasons
 *   - Age-based efficiency drop
 *   - Age-based maintenance cost scaling
 *   - Mandatory end-of-life detection
 */
class Migration_Add_lift_wear_lifecycle extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('install_date', 'game_created_lifts')) {
            $this->dbforge->add_column('game_created_lifts', [
                'install_date' => [
                    'type'       => 'DATE',
                    'null'       => TRUE,
                    'default'    => NULL,
                    'after'      => 'lift_condition',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('install_date', 'game_created_lifts')) {
            $this->dbforge->drop_column('game_created_lifts', 'install_date');
        }
    }
}
