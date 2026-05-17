<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Season_pass_model
 *
 * Manages per-resort season ski pass settings stored in game_resort_season_passes.
 */
class Season_pass_model extends CI_Model {

    /**
     * get_settings_DB  Returns the season pass settings row for a resort.
     *                  Returns a default object when no row exists yet.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_settings_DB($id_resort) {
        $row = $this->db
            ->select('enabled, season_pass_price, passes_sold, current_season, early_bird_enabled, early_bird_discount_pct')
            ->from('game_resort_season_passes')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)[
                'enabled'                => 0,
                'season_pass_price'      => SEASON_PASS_DEFAULT_PRICE,
                'passes_sold'            => 0,
                'current_season'         => 0,
                'early_bird_enabled'     => 0,
                'early_bird_discount_pct' => SEASON_PASS_EARLY_BIRD_DEFAULT_DISCOUNT,
            ];
        } else {
            // Provide defaults for columns added after initial schema creation
            if (!isset($row->early_bird_enabled))      $row->early_bird_enabled      = 0;
            if (!isset($row->early_bird_discount_pct)) $row->early_bird_discount_pct = SEASON_PASS_EARLY_BIRD_DEFAULT_DISCOUNT;
        }

        return $row;
    }

    /**
     * save_settings_DB     Inserts or updates the season pass settings for a resort.
     *
     * @param int $id_resort
     * @param int $enabled           0 or 1
     * @param int $season_pass_price Price per pass (€)
     * @param int $early_bird_enabled       0 or 1
     * @param int $early_bird_discount_pct  Discount percentage for early buyers
     * @return bool
     */
    public function save_settings_DB($id_resort, $enabled, $season_pass_price, $early_bird_enabled = 0, $early_bird_discount_pct = SEASON_PASS_EARLY_BIRD_DEFAULT_DISCOUNT) {
        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_season_passes');

        $update_data = [
            'enabled'                => (int)$enabled,
            'season_pass_price'      => (int)$season_pass_price,
            'early_bird_enabled'     => (int)$early_bird_enabled,
            'early_bird_discount_pct' => (int)$early_bird_discount_pct,
        ];

        $insert_data = array_merge(['id_resort' => (int)$id_resort], $update_data);

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_season_passes', $update_data);
        } else {
            $this->db->insert('game_resort_season_passes', $insert_data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * update_passes_sold_DB    Updates the passes_sold and current_season columns.
     *
     * @param int $id_resort
     * @param int $passes_sold
     * @param int $current_season
     * @return bool
     */
    public function update_passes_sold_DB($id_resort, $passes_sold, $current_season) {
        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_season_passes');

        if ($exists == 0) {
            return false;
        }

        $this->db->trans_start();
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resort_season_passes', [
            'passes_sold'    => (int)$passes_sold,
            'current_season' => (int)$current_season,
        ]);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_all_enabled_DB   Returns all resorts that have season passes enabled.
     *
     * @return object  CI DB result object
     */
    public function get_all_enabled_DB() {
        return $this->db
            ->select('id_resort, season_pass_price, passes_sold, current_season, early_bird_enabled, early_bird_discount_pct')
            ->from('game_resort_season_passes')
            ->where('enabled', 1)
            ->get();
    }

    /**
     * calculate_passes_sold    Calculates expected pass sales based on reputation and price.
     *
     * @param int   $reputation
     * @param int   $price
     * @param bool  $early_bird_enabled
     * @return int
     */
    public function calculate_passes_sold($reputation, $price, $early_bird_enabled = false) {
        $price_factor = max(0.1, 1.0 - ($price - SEASON_PASS_BASE_PRICE) * SEASON_PASS_PRICE_SENSITIVITY);
        $passes       = (SEASON_PASS_BASE_SALES + (int)$reputation * SEASON_PASS_SALES_PER_REP) * $price_factor;
        if ($early_bird_enabled) {
            $passes = $passes * (1 + SEASON_PASS_EARLY_BIRD_SALES_BOOST);
        }
        return min(SEASON_PASS_MAX_PASSES, max(10, (int)floor($passes)));
    }
}
