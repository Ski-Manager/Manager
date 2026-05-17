<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Snow Report
 *
 * Creates the game_snow_reports table which stores daily snow condition
 * reports published by resort owners. Publishing a quality snow report
 * temporarily boosts visitor affluence.
 *
 * Columns:
 *  id_report        – primary key
 *  id_resort        – owning resort
 *  report_date      – date the report was published (one per resort per day)
 *  snow_depth_cm    – current average snow depth in centimetres (0-500)
 *  fresh_snow_cm    – fresh snow in the last 24 h (0-200)
 *  conditions       – overall conditions: 'poor','fair','good','excellent'
 *  piste_coverage   – % of pistes covered (0-100)
 *  note             – optional free-text note from the resort owner (max 500 chars)
 *  rep_bonus        – reputation bonus awarded when this report was published
 *  created_at       – UTC timestamp
 */
class Migration_Add_snow_report extends CI_Migration {

    public function up() {
        if ($this->db->table_exists('game_snow_reports')) {
            return;
        }

        $this->dbforge->add_field([
            'id_report' => [
                'type'           => 'INT',
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ],
            'id_resort' => [
                'type'     => 'INT',
                'unsigned' => TRUE,
                'null'     => FALSE,
            ],
            'report_date' => [
                'type'       => 'DATE',
                'null'       => FALSE,
            ],
            'snow_depth_cm' => [
                'type'       => 'SMALLINT',
                'unsigned'   => TRUE,
                'default'    => 0,
            ],
            'fresh_snow_cm' => [
                'type'       => 'SMALLINT',
                'unsigned'   => TRUE,
                'default'    => 0,
            ],
            'conditions' => [
                'type'       => 'ENUM',
                'constraint' => ["'poor'", "'fair'", "'good'", "'excellent'"],
                'default'    => "'fair'",
            ],
            'piste_coverage' => [
                'type'       => 'TINYINT',
                'unsigned'   => TRUE,
                'default'    => 0,
            ],
            'note' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => TRUE,
                'default'    => NULL,
            ],
            'rep_bonus' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => FALSE,
            ],
        ]);

        $this->dbforge->add_key('id_report', TRUE);
        $this->dbforge->add_key('id_resort');
        $this->dbforge->add_key('report_date');
        $this->dbforge->create_table('game_snow_reports');
    }

    public function down() {
        $this->dbforge->drop_table('game_snow_reports', TRUE);
    }
}
