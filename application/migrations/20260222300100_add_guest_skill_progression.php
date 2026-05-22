<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add game_guest_skill_progression table
 *
 * Tracks the skill-level distribution of guests per resort.
 * Beginners can level up to intermediate, and intermediate to advanced,
 * at the end of each season.  Advanced guests generate a revenue bonus.
 *
 * SQL equivalent:
 *   CREATE TABLE `game_guest_skill_progression` (
 *     `id_resort`        INT(11)      NOT NULL,
 *     `beginner_pct`     TINYINT(3)   NOT NULL DEFAULT 100,
 *     `intermediate_pct` TINYINT(3)   NOT NULL DEFAULT 0,
 *     `advanced_pct`     TINYINT(3)   NOT NULL DEFAULT 0,
 *     `seasons_played`   SMALLINT(5)  NOT NULL DEFAULT 0,
 *     `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *     PRIMARY KEY (`id_resort`)
 *   );
 */
class Migration_Add_guest_skill_progression extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id_resort' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => FALSE,
            ],
            'beginner_pct' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'null'       => FALSE,
                'default'    => 100,
            ],
            'intermediate_pct' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'advanced_pct' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'seasons_played' => [
                'type'       => 'SMALLINT',
                'constraint' => 5,
                'null'       => FALSE,
                'default'    => 0,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ],
        ]);
        $this->dbforge->add_key('id_resort', TRUE);
        $this->dbforge->create_table('game_guest_skill_progression');
    }

    public function down() {
        $this->dbforge->drop_table('game_guest_skill_progression');
    }
}
