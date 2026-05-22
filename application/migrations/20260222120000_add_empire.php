<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add game_empire_subsidiaries table for Multi-Mountain Ownership
 *
 * Allows a player to purchase subsidiary resorts (nearby resort, glacier resort,
 * budget ski hill) and manage a resort empire with shared marketing/finances.
 *
 * SQL equivalent:
 *   CREATE TABLE `game_empire_subsidiaries` (
 *     `id_subsidiary`   INT(11) NOT NULL AUTO_INCREMENT,
 *     `id_resort`       INT(11) NOT NULL,
 *     `subsidiary_type` VARCHAR(50) NOT NULL,
 *     `subsidiary_name` VARCHAR(100) NOT NULL,
 *     `purchase_price`  INT(11) NOT NULL,
 *     `daily_revenue`   INT(11) NOT NULL DEFAULT 0,
 *     `marketing_bonus` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
 *     `purchased_at`    DATETIME NOT NULL,
 *     PRIMARY KEY (`id_subsidiary`),
 *     KEY `idx_id_resort` (`id_resort`)
 *   );
 */
class Migration_Add_empire extends CI_Migration {

    public function up() {
        $fields = [
            'id_subsidiary' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
                'null'       => FALSE,
            ],
            'subsidiary_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => FALSE,
            ],
            'subsidiary_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => FALSE,
            ],
            'purchase_price' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'daily_revenue' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'marketing_bonus' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => FALSE,
                'default'    => '1.00',
            ],
            'purchased_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id_subsidiary', TRUE);
        $this->dbforge->add_key('id_resort');
        $this->dbforge->create_table('game_empire_subsidiaries', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('game_empire_subsidiaries', TRUE);
    }
}
