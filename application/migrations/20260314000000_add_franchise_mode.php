<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Franchise Mode tables
 *
 * Extends the Resort Empire with Franchise Mode features:
 *   - game_franchise_branding      : Shared brand name / tier applied across all subsidiaries
 *   - game_franchise_shared_staff  : Staff sharing allocations per subsidiary
 *   - game_franchise_cross_promos  : Cross-promotional campaigns spanning all empire resorts
 *   - game_franchise_budget_xfers  : Budget transfers between the main resort and subsidiaries
 */
class Migration_Add_franchise_mode extends CI_Migration {

    public function up() {

        // ── Franchise Branding ──────────────────────────────────────────────
        if (!$this->db->table_exists('game_franchise_branding')) {
            $this->dbforge->add_field([
                'id_resort' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                ],
                'brand_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                    'default'    => '',
                ],
                'brand_tier' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 1,
                ],
                'branding_bonus' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => FALSE,
                    'default'    => '1.00',
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('id_resort', TRUE);
            $this->dbforge->create_table('game_franchise_branding');
        }

        // ── Shared Staff Allocations ────────────────────────────────────────
        if (!$this->db->table_exists('game_franchise_shared_staff')) {
            $this->dbforge->add_field([
                'id_subsidiary' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                ],
                'shared_staff_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => FALSE,
                    'default'    => 0,
                ],
                'staff_bonus' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => FALSE,
                    'default'    => '1.00',
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('id_subsidiary', TRUE);
            $this->dbforge->create_table('game_franchise_shared_staff');
        }

        // ── Cross-Promotional Campaigns ─────────────────────────────────────
        if (!$this->db->table_exists('game_franchise_cross_promos')) {
            $this->dbforge->add_field([
                'id_promo' => [
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
                'promo_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'promo_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE,
                ],
                'cost' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => FALSE,
                    'default'    => 0,
                ],
                'guest_bonus' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => FALSE,
                    'default'    => '1.00',
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => FALSE,
                    'default'    => 1,
                ],
                'started_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE,
                ],
                'expires_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE,
                ],
            ]);
            $this->dbforge->add_key('id_promo', TRUE);
            $this->dbforge->add_key('id_resort');
            $this->dbforge->create_table('game_franchise_cross_promos');
        }

        // ── Budget Transfers ────────────────────────────────────────────────
        if (!$this->db->table_exists('game_franchise_budget_xfers')) {
            $this->dbforge->add_field([
                'id_transfer' => [
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
                'id_subsidiary' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                ],
                'amount' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => FALSE,
                ],
                'direction' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => FALSE,
                    'default'    => 'to_subsidiary',
                ],
                'transferred_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE,
                ],
            ]);
            $this->dbforge->add_key('id_transfer', TRUE);
            $this->dbforge->add_key('id_resort');
            $this->dbforge->create_table('game_franchise_budget_xfers');
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_franchise_budget_xfers',  TRUE);
        $this->dbforge->drop_table('game_franchise_cross_promos',  TRUE);
        $this->dbforge->drop_table('game_franchise_shared_staff',  TRUE);
        $this->dbforge->drop_table('game_franchise_branding',      TRUE);
    }
}
