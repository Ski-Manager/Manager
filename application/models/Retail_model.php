<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Retail_model
 *
 * Manages per-resort retail & amenities shop settings stored in
 * game_resort_retail.  Four shop types: ski_shop, souvenir_shop, cafe, bar.
 */
class Retail_model extends CI_Model {

    /** Canonical shop types. */
    public static function shop_types(): array {
        return ['ski_shop', 'souvenir_shop', 'cafe', 'bar'];
    }

    /**
     * get_all_shops_DB     Returns all four shop rows for a resort.
     *                      If a row does not exist yet, a default object is returned.
     *
     * @param int $id_resort
     * @return array   Associative array keyed by shop_type
     */
    public function get_all_shops_DB(int $id_resort): array {
        $rows = $this->db
            ->select('shop_type, enabled, stock_level, pricing_strategy, seasonal_items, popularity')
            ->from('game_resort_retail')
            ->where('id_resort', $id_resort)
            ->get()
            ->result();

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row->shop_type] = $row;
        }

        // Fill in defaults for any missing shop types
        foreach (self::shop_types() as $type) {
            if (!isset($indexed[$type])) {
                $indexed[$type] = (object)[
                    'shop_type'        => $type,
                    'enabled'          => 0,
                    'stock_level'      => 3,
                    'pricing_strategy' => 'standard',
                    'seasonal_items'   => 0,
                    'popularity'       => RETAIL_POPULARITY_DEFAULT,
                ];
            }
        }

        return $indexed;
    }

    /**
     * save_shop_DB     Inserts or updates a single shop row.
     *
     * @param int    $id_resort
     * @param string $shop_type
     * @param int    $enabled           0 or 1
     * @param int    $stock_level       1–5
     * @param string $pricing_strategy  'budget' | 'standard' | 'premium'
     * @param int    $seasonal_items    0 or 1
     * @return bool
     */
    public function save_shop_DB(int $id_resort, string $shop_type, int $enabled, int $stock_level, string $pricing_strategy, int $seasonal_items): bool {
        $data = [
            'id_resort'        => $id_resort,
            'shop_type'        => $shop_type,
            'enabled'          => $enabled,
            'stock_level'      => $stock_level,
            'pricing_strategy' => $pricing_strategy,
            'seasonal_items'   => $seasonal_items,
        ];

        $exists = $this->db
            ->where('id_resort', $id_resort)
            ->where('shop_type', $shop_type)
            ->count_all_results('game_resort_retail');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', $id_resort);
            $this->db->where('shop_type', $shop_type);
            $this->db->update('game_resort_retail', $data);
        } else {
            $data['popularity'] = RETAIL_POPULARITY_DEFAULT;
            $this->db->insert('game_resort_retail', $data);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * update_popularity_DB     Updates the popularity for a single shop.
     *
     * @param int    $id_resort
     * @param string $shop_type
     * @param int    $popularity   0–100
     * @return bool
     */
    public function update_popularity_DB(int $id_resort, string $shop_type, int $popularity): bool {
        $popularity = max(RETAIL_POPULARITY_MIN, min(RETAIL_POPULARITY_MAX, $popularity));

        $this->db->trans_start();
        $this->db->where('id_resort', $id_resort);
        $this->db->where('shop_type', $shop_type);
        $this->db->update('game_resort_retail', ['popularity' => $popularity]);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_enabled_shops_DB     Returns only enabled shop rows for a resort.
     *
     * @param int $id_resort
     * @return array of stdClass rows
     */
    public function get_enabled_shops_DB(int $id_resort): array {
        $rows = $this->db
            ->select('shop_type, stock_level, pricing_strategy, seasonal_items, popularity')
            ->from('game_resort_retail')
            ->where('id_resort', $id_resort)
            ->where('enabled', 1)
            ->get()
            ->result();

        return $rows;
    }
}
