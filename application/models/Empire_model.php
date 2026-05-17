<?php

class Empire_model extends CI_Model {

    /**
     * Definitions of the three purchasable subsidiary resort types.
     * These are the static catalogue entries; actual purchased records
     * live in game_empire_subsidiaries.
     */
    public static function get_subsidiary_type_catalogue() {
        return [
            'nearby_resort' => [
                'type'           => 'nearby_resort',
                'purchase_price' => 500000,
                'daily_revenue'  => 2000,
                'marketing_bonus'=> 1.10,
            ],
            'glacier_resort' => [
                'type'           => 'glacier_resort',
                'purchase_price' => 2000000,
                'daily_revenue'  => 5000,
                'marketing_bonus'=> 1.25,
            ],
            'budget_ski_hill' => [
                'type'           => 'budget_ski_hill',
                'purchase_price' => 200000,
                'daily_revenue'  => 800,
                'marketing_bonus'=> 1.05,
            ],
        ];
    }

    /**
     * get_subsidiaries_DB      Returns all subsidiaries owned by a resort.
     *
     * @param  int  $id_resort  Primary resort ID
     * @return object           CI_DB_result
     */
    public function get_subsidiaries_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_empire_subsidiaries')
            ->where('id_resort', (int)$id_resort)
            ->order_by('purchased_at', 'asc')
            ->get();
    }

    /**
     * count_subsidiaries_by_type_DB    Counts owned subsidiaries of a given type.
     *
     * @param  int     $id_resort
     * @param  string  $type
     * @return int
     */
    public function count_subsidiaries_by_type_DB($id_resort, $type) {
        return $this->db
            ->where('id_resort', (int)$id_resort)
            ->where('subsidiary_type', $type)
            ->count_all_results('game_empire_subsidiaries');
    }

    /**
     * purchase_subsidiary_DB   Inserts a new subsidiary and deducts cost from resort cash.
     *
     * @param  int     $id_resort
     * @param  string  $type
     * @param  string  $name
     * @param  int     $purchase_price
     * @param  int     $daily_revenue
     * @param  float   $marketing_bonus
     * @return bool
     */
    public function purchase_subsidiary_DB($id_resort, $type, $name, $purchase_price, $daily_revenue, $marketing_bonus) {
        $this->db->trans_start();

        // Deduct cost from resort cash
        $this->db->set('cash', 'cash - ' . (int)$purchase_price, FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resorts');

        // Insert new subsidiary record
        $this->db->insert('game_empire_subsidiaries', [
            'id_resort'       => (int)$id_resort,
            'subsidiary_type' => $type,
            'subsidiary_name' => $name,
            'purchase_price'  => (int)$purchase_price,
            'daily_revenue'   => (int)$daily_revenue,
            'marketing_bonus' => (float)$marketing_bonus,
            'purchased_at'    => gmdate('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_empire_stats_DB      Returns aggregate empire stats for a resort.
     *
     * @param  int  $id_resort
     * @return object|null  Row with total_daily_revenue, total_subsidiaries,
     *                      combined_marketing_bonus (product via PHP; raw sum here)
     */
    public function get_empire_stats_DB($id_resort) {
        return $this->db
            ->select('COUNT(*) as total_subsidiaries, SUM(daily_revenue) as total_daily_revenue, SUM(marketing_bonus - 1) as total_marketing_bonus_sum')
            ->from('game_empire_subsidiaries')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();
    }

    // =========================================================================
    // Franchise Mode helpers
    // =========================================================================

    /**
     * Branding tier definitions: tier => [label, branding_bonus, upgrade_cost]
     */
    public static function get_brand_tiers() {
        return [
            1 => ['label' => 'Local',         'branding_bonus' => 1.00, 'upgrade_cost' =>      0],
            2 => ['label' => 'Regional',       'branding_bonus' => 1.05, 'upgrade_cost' =>  50000],
            3 => ['label' => 'National',       'branding_bonus' => 1.12, 'upgrade_cost' => 200000],
            4 => ['label' => 'International',  'branding_bonus' => 1.20, 'upgrade_cost' => 500000],
        ];
    }

    /**
     * Cross-promotion type catalogue: type => [label, cost, guest_bonus, duration_days]
     */
    public static function get_cross_promo_catalogue() {
        return [
            'bundle_deal'      => ['label' => 'Multi-Resort Bundle Deal',   'cost' => 10000, 'guest_bonus' => 1.08, 'duration_days' => 14],
            'loyalty_transfer' => ['label' => 'Loyalty Points Transfer',    'cost' =>  5000, 'guest_bonus' => 1.05, 'duration_days' => 30],
            'joint_campaign'   => ['label' => 'Joint Marketing Campaign',   'cost' => 20000, 'guest_bonus' => 1.15, 'duration_days' =>  7],
            'referral_program' => ['label' => 'Cross-Resort Referral',      'cost' =>  3000, 'guest_bonus' => 1.03, 'duration_days' => 60],
        ];
    }

    /**
     * get_franchise_branding_DB    Returns the franchise branding row for a resort.
     *
     * @param  int  $id_resort
     * @return object|null
     */
    public function get_franchise_branding_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_franchise_branding')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();
    }

    /**
     * set_franchise_branding_DB    Upserts the franchise branding row.
     *
     * @param  int     $id_resort
     * @param  string  $brand_name
     * @param  int     $brand_tier   1-4
     * @param  float   $branding_bonus
     * @return bool
     */
    public function set_franchise_branding_DB($id_resort, $brand_name, $brand_tier, $branding_bonus) {
        $existing = $this->get_franchise_branding_DB($id_resort);
        $data = [
            'brand_name'     => $brand_name,
            'brand_tier'     => (int)$brand_tier,
            'branding_bonus' => (float)$branding_bonus,
            'updated_at'     => gmdate('Y-m-d H:i:s'),
        ];
        if ($existing) {
            $this->db->where('id_resort', (int)$id_resort)->update('game_franchise_branding', $data);
        } else {
            $data['id_resort'] = (int)$id_resort;
            $this->db->insert('game_franchise_branding', $data);
        }
        return $this->db->affected_rows() > 0;
    }

    /**
     * get_shared_staff_DB  Returns shared-staff allocation for a subsidiary.
     *
     * @param  int  $id_subsidiary
     * @return object|null
     */
    public function get_shared_staff_DB($id_subsidiary) {
        return $this->db
            ->select('*')
            ->from('game_franchise_shared_staff')
            ->where('id_subsidiary', (int)$id_subsidiary)
            ->get()
            ->row();
    }

    /**
     * get_all_shared_staff_DB  Returns shared-staff rows for every subsidiary of a resort.
     *
     * @param  int  $id_resort
     * @return array  of objects keyed by id_subsidiary
     */
    public function get_all_shared_staff_DB($id_resort) {
        $rows = $this->db
            ->select('gfss.*')
            ->from('game_franchise_shared_staff gfss')
            ->join('game_empire_subsidiaries ges', 'ges.id_subsidiary = gfss.id_subsidiary', 'inner')
            ->where('ges.id_resort', (int)$id_resort)
            ->get()
            ->result();
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r->id_subsidiary] = $r;
        }
        return $map;
    }

    /**
     * set_shared_staff_DB  Upserts the shared-staff allocation for a subsidiary.
     *
     * @param  int    $id_subsidiary
     * @param  int    $shared_staff_count  0-10
     * @param  float  $staff_bonus
     * @return bool
     */
    public function set_shared_staff_DB($id_subsidiary, $shared_staff_count, $staff_bonus) {
        $existing = $this->get_shared_staff_DB($id_subsidiary);
        $data = [
            'shared_staff_count' => (int)$shared_staff_count,
            'staff_bonus'        => (float)$staff_bonus,
            'updated_at'         => gmdate('Y-m-d H:i:s'),
        ];
        if ($existing) {
            $this->db->where('id_subsidiary', (int)$id_subsidiary)->update('game_franchise_shared_staff', $data);
        } else {
            $data['id_subsidiary'] = (int)$id_subsidiary;
            $this->db->insert('game_franchise_shared_staff', $data);
        }
        return $this->db->affected_rows() > 0;
    }

    /**
     * get_active_cross_promos_DB   Returns active cross-promotions for a resort.
     *
     * @param  int  $id_resort
     * @return array  of objects
     */
    public function get_active_cross_promos_DB($id_resort) {
        return $this->db
            ->select('*')
            ->from('game_franchise_cross_promos')
            ->where('id_resort', (int)$id_resort)
            ->where('is_active', 1)
            ->where('expires_at >', gmdate('Y-m-d H:i:s'))
            ->order_by('started_at', 'desc')
            ->get()
            ->result();
    }

    /**
     * launch_cross_promo_DB    Inserts a new cross-promotion and deducts cost.
     *
     * @param  int     $id_resort
     * @param  string  $promo_name
     * @param  string  $promo_type
     * @param  int     $cost
     * @param  float   $guest_bonus
     * @param  int     $duration_days
     * @return bool
     */
    public function launch_cross_promo_DB($id_resort, $promo_name, $promo_type, $cost, $guest_bonus, $duration_days) {
        $this->db->trans_start();

        $this->db->set('cash', 'cash - ' . (int)$cost, FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resorts');

        $now = gmdate('Y-m-d H:i:s');
        $this->db->insert('game_franchise_cross_promos', [
            'id_resort'  => (int)$id_resort,
            'promo_name' => $promo_name,
            'promo_type' => $promo_type,
            'cost'       => (int)$cost,
            'guest_bonus'=> (float)$guest_bonus,
            'is_active'  => 1,
            'started_at' => $now,
            'expires_at' => gmdate('Y-m-d H:i:s', strtotime("+{$duration_days} days")),
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_budget_transfers_DB  Returns the 20 most recent budget transfers for a resort.
     *
     * @param  int  $id_resort
     * @return array  of objects
     */
    public function get_budget_transfers_DB($id_resort) {
        return $this->db
            ->select('gfbx.*, ges.subsidiary_name')
            ->from('game_franchise_budget_xfers gfbx')
            ->join('game_empire_subsidiaries ges', 'ges.id_subsidiary = gfbx.id_subsidiary', 'left')
            ->where('gfbx.id_resort', (int)$id_resort)
            ->order_by('gfbx.transferred_at', 'desc')
            ->limit(20)
            ->get()
            ->result();
    }

    /**
     * transfer_budget_DB   Moves funds between the main resort cash and a subsidiary's
     *                      daily_revenue (representing its accumulated cash pool).
     *
     * direction 'to_subsidiary'   : deduct from resort, increase subsidiary daily_revenue
     * direction 'from_subsidiary' : deduct from subsidiary daily_revenue, add to resort
     *
     * @param  int     $id_resort
     * @param  int     $id_subsidiary
     * @param  int     $amount
     * @param  string  $direction   'to_subsidiary' | 'from_subsidiary'
     * @return bool
     */
    public function transfer_budget_DB($id_resort, $id_subsidiary, $amount, $direction) {
        $amount = (int)$amount;
        $this->db->trans_start();

        if ($direction === 'to_subsidiary') {
            // Deduct from main resort cash
            $this->db->set('cash', 'cash - ' . $amount, FALSE)
                     ->where('id_resort', (int)$id_resort)
                     ->update('game_resorts');
            // Credit subsidiary daily_revenue pool
            $this->db->set('daily_revenue', 'daily_revenue + ' . $amount, FALSE)
                     ->where('id_subsidiary', (int)$id_subsidiary)
                     ->where('id_resort',     (int)$id_resort)
                     ->update('game_empire_subsidiaries');
        } else {
            // Deduct from subsidiary daily_revenue pool
            $this->db->set('daily_revenue', 'daily_revenue - ' . $amount, FALSE)
                     ->where('id_subsidiary', (int)$id_subsidiary)
                     ->where('id_resort',     (int)$id_resort)
                     ->update('game_empire_subsidiaries');
            // Credit main resort cash
            $this->db->set('cash', 'cash + ' . $amount, FALSE)
                     ->where('id_resort', (int)$id_resort)
                     ->update('game_resorts');
        }

        $this->db->insert('game_franchise_budget_xfers', [
            'id_resort'      => (int)$id_resort,
            'id_subsidiary'  => (int)$id_subsidiary,
            'amount'         => $amount,
            'direction'      => $direction,
            'transferred_at' => gmdate('Y-m-d H:i:s'),
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
