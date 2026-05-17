<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Scenic_lift_model
 *
 * Manages per-resort scenic-lift settings stored in game_resort_scenic_lifts.
 */
class Scenic_lift_model extends CI_Model {

    /**
     * get_settings_DB  Returns the scenic-lift settings row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('is_enabled, ticket_price, capacity_level, seasonal_discount, tour_theme, photography_package, vip_gondola')
            ->from('game_resort_scenic_lifts')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'is_enabled'          => 0,
                'ticket_price'        => SCENIC_LIFT_DEFAULT_TICKET_PRICE,
                'capacity_level'      => SCENIC_LIFT_DEFAULT_CAPACITY,
                'seasonal_discount'   => 0,
                'tour_theme'          => SCENIC_LIFT_THEME_STANDARD,
                'photography_package' => 0,
                'vip_gondola'         => 0,
            ];
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the scenic-lift settings for a resort.
     *
     * @param int $id_resort
     * @param int $is_enabled   0 or 1
     * @param int $ticket_price
     * @return bool
     */
    public function save_settings_DB($id_resort, $is_enabled, $ticket_price, $capacity_level, $seasonal_discount, $tour_theme, $photography_package, $vip_gondola) {
        $data = [
            'id_resort'           => (int)$id_resort,
            'is_enabled'          => (int)$is_enabled,
            'ticket_price'        => (int)$ticket_price,
            'capacity_level'      => (int)$capacity_level,
            'seasonal_discount'   => (int)$seasonal_discount,
            'tour_theme'          => (int)$tour_theme,
            'photography_package' => (int)$photography_package,
            'vip_gondola'         => (int)$vip_gondola,
        ];

        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_scenic_lifts');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_scenic_lifts', $data);
        } else {
            $this->db->insert('game_resort_scenic_lifts', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
