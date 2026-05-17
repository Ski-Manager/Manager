<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add category column to changelog table
 *
 * category  ENUM('feature','bug','other','enhancement')  DEFAULT 'other'
 *
 * SQL equivalent:
 *   ALTER TABLE `changelog`
 *     ADD COLUMN `category` ENUM('feature','bug','other','enhancement') NOT NULL DEFAULT 'other';
 */
class Migration_Add_changelog_category extends CI_Migration {

    public function up() {
        // If a legacy `type` column exists, rename it to `category` first
        if (!$this->db->field_exists('category', 'changelog') &&
             $this->db->field_exists('type', 'changelog')) {
            $this->db->query(
                "ALTER TABLE `changelog` CHANGE `type` `category` VARCHAR(50) NOT NULL DEFAULT 'other'"
            );
        }

        if ($this->db->field_exists('category', 'changelog')) {
            // Remap old VARCHAR values (including misspellings) to the new ENUM values
            $map = [
                'New Feature' => 'feature',
                'Bug Fix'     => 'bug',
                'Balance'     => 'enhancement',
                'UI'          => 'enhancement',
                'Other'       => 'other',
                'enhancment'  => 'enhancement',
            ];
            foreach ($map as $old => $new) {
                $this->db->query(
                    "UPDATE `changelog` SET `category` = ? WHERE `category` = ?",
                    [$new, $old]
                );
            }
            // Set any remaining unrecognised values to 'other'
            $this->db->query(
                "UPDATE `changelog` SET `category` = 'other'
                 WHERE `category` NOT IN ('feature','bug','other','enhancement')"
            );
            // Alter existing column to ENUM
            $this->db->query(
                "ALTER TABLE `changelog`
                 MODIFY COLUMN `category`
                 ENUM('feature','bug','other','enhancement') NOT NULL DEFAULT 'other'"
            );
            return;
        }
        $this->db->query(
            "ALTER TABLE `changelog`
             ADD COLUMN `category`
             ENUM('feature','bug','other','enhancement') NOT NULL DEFAULT 'other'
             AFTER `description`"
        );
    }

    public function down() {
        if ($this->db->field_exists('category', 'changelog')) {
            $this->dbforge->drop_column('changelog', 'category');
        }
    }
}
