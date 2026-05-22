<?php
/**
 * Migration: Add grooming_active to game_purchased_equipments
 *
 * Adds a grooming_active column (1 = active every night, 0 = standby/skip).
 * Standby groomers are excluded from nightly grooming runs and operating costs.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_grooming_active extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('grooming_active', 'game_purchased_equipments')) {
            $this->dbforge->add_column('game_purchased_equipments', [
                'grooming_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => TRUE,
                    'default'    => 1,
                    'after'      => 'grooming_intensity',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('grooming_active', 'game_purchased_equipments')) {
            $this->dbforge->drop_column('game_purchased_equipments', 'grooming_active');
        }
    }
}
