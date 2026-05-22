<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Vip_loyalty_model
 *
 * Manages per-resort VIP & loyalty programme settings stored in game_resort_vip_loyalty.
 */
class Vip_loyalty_model extends CI_Model {

    /**
     * get_settings_DB  Returns the VIP/loyalty settings row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('loyalty_enabled, loyalty_discount_pct, vip_private_lift, vip_premium_slopes, vip_concierge, vip_airport_transfer, vip_apreski_lounge')
            ->from('game_resort_vip_loyalty')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'loyalty_enabled'      => 0,
                'loyalty_discount_pct' => VIP_LOYALTY_DISCOUNT_DEFAULT,
                'vip_private_lift'     => 0,
                'vip_premium_slopes'   => 0,
                'vip_concierge'        => 0,
                'vip_airport_transfer' => 0,
                'vip_apreski_lounge'   => 0,
            ];
        } else {
            // Provide defaults for columns added after initial schema creation
            if (!isset($row->vip_airport_transfer)) $row->vip_airport_transfer = 0;
            if (!isset($row->vip_apreski_lounge))   $row->vip_apreski_lounge   = 0;
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates VIP/loyalty settings for a resort.
     *
     * @param int $id_resort
     * @param int $loyalty_enabled       0 or 1
     * @param int $loyalty_discount_pct  Discount percentage for loyal guests
     * @param int $vip_private_lift      0 or 1
     * @param int $vip_premium_slopes    0 or 1
     * @param int $vip_concierge         0 or 1
     * @param int $vip_airport_transfer  0 or 1
     * @param int $vip_apreski_lounge    0 or 1
     * @return bool
     */
    public function save_settings_DB($id_resort, $loyalty_enabled, $loyalty_discount_pct, $vip_private_lift, $vip_premium_slopes, $vip_concierge, $vip_airport_transfer = 0, $vip_apreski_lounge = 0) {
        $data = [
            'id_resort'            => (int)$id_resort,
            'loyalty_enabled'      => (int)$loyalty_enabled,
            'loyalty_discount_pct' => (int)$loyalty_discount_pct,
            'vip_private_lift'     => (int)$vip_private_lift,
            'vip_premium_slopes'   => (int)$vip_premium_slopes,
            'vip_concierge'        => (int)$vip_concierge,
            'vip_airport_transfer' => (int)$vip_airport_transfer,
            'vip_apreski_lounge'   => (int)$vip_apreski_lounge,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_vip_loyalty');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_vip_loyalty', $data);
        } else {
            $this->db->insert('game_resort_vip_loyalty', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
