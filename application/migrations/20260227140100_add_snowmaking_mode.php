<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add snowmaking_mode column to game_resorts
 *
 * Allows players to choose a snowmaking operating mode:
 *   'normal' – standard output and cost (default)
 *   'eco'    – reduced output (×ECO_OUTPUT) and reduced cost (×ECO_COST)
 *   'boost'  – increased output (×BOOST_OUTPUT) at higher cost (×BOOST_COST)
 *
 * SQL equivalent:
 *   ALTER TABLE `game_resorts`
 *     ADD COLUMN `snowmaking_mode` VARCHAR(10) NOT NULL DEFAULT 'normal';
 */
class Migration_Add_snowmaking_mode extends CI_Migration {

    public function up() {
        if (!$this->db->field_exists('snowmaking_mode', 'game_resorts')) {
            $this->dbforge->add_column('game_resorts', [
                'snowmaking_mode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => FALSE,
                    'default'    => 'normal',
                ],
            ]);
        }
    }

    public function down() {
        if ($this->db->field_exists('snowmaking_mode', 'game_resorts')) {
            $this->dbforge->drop_column('game_resorts', 'snowmaking_mode');
        }
    }
}
