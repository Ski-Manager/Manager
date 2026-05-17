<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Government_model
 *
 * Manages the game_resort_government table:
 *   compliance_score, tax_rate, tax_season, subsidy_available, subsidy_season,
 *   last_audit_result, last_audit_date, expansion_blocked,
 *   total_fines_paid, total_subsidies_received.
 */
class Government_model extends CI_Model {

    /**
     * get_government_DB    Returns the government row for a resort.
     *                      Creates the row with defaults if it does not exist yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_government_DB($id_resort) {
        $row = $this->db
            ->select('*')
            ->from('game_resort_government')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $this->db->insert('game_resort_government', [
                'id_resort'              => (int)$id_resort,
                'compliance_score'       => 50,
                'tax_rate'               => (float)rand(GOV_TAX_RATE_MIN, GOV_TAX_RATE_MAX),
                'tax_season'             => 0,
                'subsidy_available'      => 0,
                'subsidy_season'         => 0,
                'last_audit_result'      => 'none',
                'last_audit_date'        => NULL,
                'expansion_blocked'      => 0,
                'total_fines_paid'       => 0,
                'total_subsidies_received' => 0,
            ]);
            $row = $this->db
                ->select('*')
                ->from('game_resort_government')
                ->where('id_resort', (int)$id_resort)
                ->get()
                ->row();
        }
        return $row;
    }

    /**
     * update_government_DB     Saves updated government data for a resort.
     *
     * @param int   $id_resort
     * @param array $data  Associative array of columns to update
     * @return bool
     */
    public function update_government_DB($id_resort, array $data) {
        $this->db->trans_start();
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resort_government', $data);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * claim_subsidy_DB     Marks the available subsidy as claimed and returns the amount.
     *                      Returns 0 if no subsidy is available.
     *
     * @param int $id_resort
     * @return int  Amount claimed (€)
     */
    public function claim_subsidy_DB($id_resort) {
        $row = $this->get_government_DB($id_resort);
        $amount = (int)$row->subsidy_available;
        if ($amount <= 0) {
            return 0;
        }

        $this->db->trans_start();
        $this->db->set('subsidy_available',      0);
        $this->db->set('total_subsidies_received', 'total_subsidies_received + ' . $amount, FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resort_government');
        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE ? $amount : 0;
    }

    /**
     * get_all_resorts_government   Returns government rows for all resorts.
     *                              Used by the nightly cron job.
     *
     * @return CI_DB_result
     */
    public function get_all_resorts_government() {
        return $this->db
            ->select('*')
            ->from('game_resort_government')
            ->get();
    }
}
