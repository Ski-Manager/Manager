<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Fix Mountain Master Plan Primary Key Column Name
 *
 * The `game_master_plans` table's primary key must be named `id_master_plan`.
 * If the table was created before the `add_mountain_master_plan` migration ran
 * (which uses CREATE TABLE IF NOT EXISTS), the primary key column may have been
 * created with the generic name `id` instead.  This migration renames it.
 */
class Migration_Fix_master_plan_primary_key extends CI_Migration {

    public function up() {
        // Only rename if 'id' exists AND 'id_master_plan' does not yet exist.
        $fields = $this->db->field_data('game_master_plans');
        if (!$fields) {
            return; // Table doesn't exist; nothing to fix.
        }

        $column_names = array_map(function($f) { return $f->name; }, $fields);

        if (!in_array('id_master_plan', $column_names, TRUE)
            && in_array('id', $column_names, TRUE)) {
            $this->db->query('
                ALTER TABLE `game_master_plans`
                    CHANGE COLUMN `id` `id_master_plan` INT NOT NULL AUTO_INCREMENT
            ');
        }
    }

    public function down() {
        $fields = $this->db->field_data('game_master_plans');
        if (!$fields) {
            return;
        }

        $column_names = array_map(function($f) { return $f->name; }, $fields);

        if (in_array('id_master_plan', $column_names, TRUE)
            && !in_array('id', $column_names, TRUE)) {
            $this->db->query('
                ALTER TABLE `game_master_plans`
                    CHANGE COLUMN `id_master_plan` `id` INT NOT NULL AUTO_INCREMENT
            ');
        }
    }
}
