<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Climate_change_model
 *
 * Handles all DB interactions for the Climate Change System.
 * The game_climate_change table stores one row per resort with:
 *   - climate_level        : integer 0-10, increases each season (season ≥ 3)
 *   - snowmaking_invest    : 1 if the player has invested in snowmaking adaptation
 *   - altitude_invest      : 1 if the player has invested in altitude adaptation
 *   - diversify_invest     : 1 if the player has invested in revenue diversification
 */
class Climate_change_model extends CI_Model {

    /**
     * get_climate_data_DB   Returns the climate row for a resort (or FALSE).
     *                       Applies zero defaults for investment columns that may
     *                       be absent when the table pre-dates the schema migration.
     */
    public function get_climate_data_DB($id_resort) {
        $query = $this->db
            ->select('*')
            ->from('game_climate_change')
            ->where('id_resort', $id_resort)
            ->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            foreach (['snowmaking_invest', 'altitude_invest', 'diversify_invest'] as $col) {
                $row->$col = (int)($row->$col ?? 0);
            }
            return $row;
        }
        return FALSE;
    }

    /**
     * init_climate_DB   Creates a row for a resort with climate_level = 0.
     *                   Returns TRUE on success, FALSE on failure.
     */
    public function init_climate_DB($id_resort) {
        $data = [
            'id_resort'          => $id_resort,
            'climate_level'      => 0,
            'snowmaking_invest'  => 0,
            'altitude_invest'    => 0,
            'diversify_invest'   => 0,
            'updated_at'         => date('Y-m-d H:i:s'),
        ];
        $this->db->trans_start();
        $this->db->insert('game_climate_change', $data);
        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE);
    }

    /**
     * increment_climate_level_DB   Increments climate_level by 1 (capped at 10).
     */
    public function increment_climate_level_DB($id_resort) {
        $this->db->trans_start();
        $this->db->set('climate_level', 'LEAST(climate_level + 1, 10)', FALSE);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_climate_change');
        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE);
    }

    /**
     * set_investment_DB   Sets one of the adaptation investment flags to 1.
     *
     * @param int    $id_resort
     * @param string $invest_type   'snowmaking_invest' | 'altitude_invest' | 'diversify_invest'
     */
    public function set_investment_DB($id_resort, $invest_type) {
        $allowed = ['snowmaking_invest', 'altitude_invest', 'diversify_invest'];
        if (!in_array($invest_type, $allowed, TRUE))
            return FALSE;
        $this->db->trans_start();
        $this->db->set($invest_type, 1);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_climate_change');
        $this->db->trans_complete();
        return ($this->db->trans_status() !== FALSE);
    }
}
