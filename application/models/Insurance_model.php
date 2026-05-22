<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Insurance_model
 *
 * Manages per-resort insurance plan settings stored in game_resort_insurance.
 */
class Insurance_model extends CI_Model {

    /**
     * get_settings_DB  Returns the insurance row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('plan, total_claims, total_claimed_amount')
            ->from('game_resort_insurance')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'plan'                 => 'none',
                'total_claims'         => 0,
                'total_claimed_amount' => 0,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the insurance plan for a resort.
     *
     * @param int    $id_resort
     * @param string $plan     'none' | 'basic' | 'premium'
     * @return bool
     */
    public function save_settings_DB($id_resort, $plan) {
        $allowed = ['none', 'basic', 'premium'];
        if (!in_array($plan, $allowed, TRUE)) {
            $plan = 'none';
        }

        $data = [
            'id_resort'  => (int)$id_resort,
            'plan'       => $plan,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_insurance');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_insurance', $data);
        } else {
            $this->db->insert('game_resort_insurance', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * record_claim_DB  Increments the claims counter and adds the payout amount.
     *
     * @param int $id_resort
     * @param int $amount     € paid as claim
     * @return bool
     */
    public function record_claim_DB($id_resort, $amount) {
        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_insurance');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->set('total_claims',         'total_claims + 1',           FALSE);
            $this->db->set('total_claimed_amount', 'total_claimed_amount + ' . (int)$amount, FALSE);
            $this->db->set('updated_at',           date('Y-m-d H:i:s'));
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_insurance');
        } else {
            $this->db->insert('game_resort_insurance', [
                'id_resort'            => (int)$id_resort,
                'plan'                 => 'none',
                'total_claims'         => 1,
                'total_claimed_amount' => (int)$amount,
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
