<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add microclimate_change_count to game_resorts
 *
 * Tracks how many times a player has changed the Microclimate settings
 * (Altitude, Slope Aspect, and derived Wind Risk) after initial resort creation.
 * The first setup at resort creation is free (count stays at 0).
 * Every change after that costs (change_count + 1) * MICROCLIMATE_CHANGE_BASE_COST.
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `microclimate_change_count` INT(4) NOT NULL DEFAULT 0;
 */
class Migration_Add_microclimate_change_count extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('game_resorts', [
            'microclimate_change_count' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => FALSE,
                'default'    => 0,
            ],
        ]);
    }

    public function down() {
        $this->dbforge->drop_column('game_resorts', 'microclimate_change_count');
    }
}
