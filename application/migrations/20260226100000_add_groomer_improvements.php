<?php
/**
 * Migration: Add grooming_intensity to game_purchased_equipments
 *
 * Adds a grooming_intensity column to support per-groomer intensity settings
 * (light / standard / intensive) that scale the nightly quality bonus and
 * daily operating cost.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_groomer_improvements extends CI_Migration {

    public function up() {
        // Add grooming_intensity column if it doesn't already exist
        if (!$this->db->field_exists('grooming_intensity', 'game_purchased_equipments')) {
            $this->dbforge->add_column('game_purchased_equipments', [
                'grooming_intensity' => [
                    'type'       => "ENUM('light','standard','intensive')",
                    'default'    => 'standard',
                    'after'      => 'custom_name',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('grooming_intensity', 'game_purchased_equipments')) {
            $this->dbforge->drop_column('game_purchased_equipments', 'grooming_intensity');
        }
    }
}
