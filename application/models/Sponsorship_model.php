<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sponsorship_model
 *
 * Manages sponsor contracts stored in game_resort_sponsorships.
 */
class Sponsorship_model extends CI_Model {

    /**
     * get_sponsorships_DB  Returns all sponsorship rows for a resort.
     *
     * @param int $id_resort
     * @return array  of objects
     */
    public function get_sponsorships_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_resort_sponsorships')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->result();
    }

    /**
     * get_active_sponsorships_DB   Returns only active contracts for a resort.
     *
     * @param int $id_resort
     * @return array  of objects
     */
    public function get_active_sponsorships_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_resort_sponsorships')
            ->where('id_resort', (int)$id_resort)
            ->where('is_active', 1)
            ->get()
            ->result();
    }

    /**
     * get_all_active_sponsorships_DB   Returns every active contract (all resorts).
     *                                  Used by the nightly job.
     *
     * @return array  of objects
     */
    public function get_all_active_sponsorships_DB() {
        return $this->db
            ->select('game_resort_sponsorships.*, game_resorts.id_player')
            ->from('game_resort_sponsorships')
            ->join('game_resorts', 'game_resorts.id_resort = game_resort_sponsorships.id_resort', 'inner')
            ->where('game_resort_sponsorships.is_active', 1)
            ->get()
            ->result();
    }

    /**
     * sign_sponsor_DB  Inserts or replaces a sponsor contract for a resort.
     *
     * @param int    $id_resort
     * @param string $sponsor_type
     * @param int    $contract_level  1–3
     * @return bool
     */
    public function sign_sponsor_DB($id_resort, $sponsor_type, $contract_level) {
        $data = [
            'id_resort'          => (int)$id_resort,
            'sponsor_type'       => $sponsor_type,
            'contract_level'     => (int)$contract_level,
            'is_active'          => 1,
            'brand_satisfaction' => SPONSORSHIP_SATISFACTION_DEFAULT,
            'signed_at'          => date('Y-m-d H:i:s'),
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->where('sponsor_type', $sponsor_type)
            ->count_all_results('game_resort_sponsorships');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db
                ->where('id_resort', (int)$id_resort)
                ->where('sponsor_type', $sponsor_type)
                ->update('game_resort_sponsorships', $data);
        } else {
            $this->db->insert('game_resort_sponsorships', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * terminate_sponsor_DB  Deletes a sponsor contract.
     *
     * @param int    $id_resort
     * @param string $sponsor_type
     * @return bool
     */
    public function terminate_sponsor_DB($id_resort, $sponsor_type) {
        $this->db
            ->where('id_resort', (int)$id_resort)
            ->where('sponsor_type', $sponsor_type)
            ->delete('game_resort_sponsorships');
        return $this->db->affected_rows() > 0;
    }

    /**
     * update_satisfaction_DB   Adjusts brand satisfaction.
     *                          When satisfaction drops to 0 the contract is deleted.
     *
     * @param int    $id_resort
     * @param string $sponsor_type
     * @param int    $new_satisfaction  Clamped to 0–SPONSORSHIP_SATISFACTION_MAX
     * @return string  'cancelled' | 'updated'
     */
    public function update_satisfaction_DB($id_resort, $sponsor_type, $new_satisfaction) {
        $new_satisfaction = max(0, min((int)$new_satisfaction, SPONSORSHIP_SATISFACTION_MAX));

        if ($new_satisfaction <= 0) {
            $this->terminate_sponsor_DB($id_resort, $sponsor_type);
            return 'cancelled';
        }

        $this->db
            ->where('id_resort', (int)$id_resort)
            ->where('sponsor_type', $sponsor_type)
            ->update('game_resort_sponsorships', ['brand_satisfaction' => $new_satisfaction]);
        return 'updated';
    }

    /**
     * get_visitor_bonus_pct    Returns the visitor multiplier bonus (0.0–0.1)
     *                          granted by an active apparel sponsor.
     *
     * @param int $id_resort
     * @return float
     */
    public function get_visitor_bonus_pct($id_resort) {
        $row = $this->db
            ->select('contract_level')
            ->from('game_resort_sponsorships')
            ->where('id_resort', (int)$id_resort)
            ->where('sponsor_type', 'apparel')
            ->where('is_active', 1)
            ->get()
            ->row();

        if (!$row) {
            return 0.0;
        }
        $idx = (int)$row->contract_level - 1;
        return (float)(SPONSORSHIP_TYPES['apparel']['visitor_bonus_pct'][$idx] ?? 0.0);
    }

    /**
     * get_maintenance_saving_pct   Returns the fraction of daily lift upkeep saved
     *                              by an active lift_equipment sponsor (0.0–0.3).
     *
     * @param int $id_resort
     * @return float
     */
    public function get_maintenance_saving_pct($id_resort) {
        $row = $this->db
            ->select('contract_level')
            ->from('game_resort_sponsorships')
            ->where('id_resort', (int)$id_resort)
            ->where('sponsor_type', 'lift_equipment')
            ->where('is_active', 1)
            ->get()
            ->row();

        if (!$row) {
            return 0.0;
        }
        $idx = (int)$row->contract_level - 1;
        return (float)(SPONSORSHIP_TYPES['lift_equipment']['maintenance_saving_pct'][$idx] ?? 0.0);
    }
}
