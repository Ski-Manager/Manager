<?php

class Competitors_model extends CI_Model {

    /**
     * get_all_competitors  Returns the full catalogue of competitor resorts.
     */
    public function get_all_competitors() {
        return $this->db
            ->select('*')
            ->from('game_competitor_resorts')
            ->order_by('base_reputation', 'DESC')
            ->get();
    }

    /**
     * get_player_competitors   Returns state rows for a resort, joined with
     *                          catalogue info.
     *
     * @param int    $id_resort
     * @param string $name_lang  'name_english' or 'name_french'
     */
    public function get_player_competitors($id_resort, $name_lang = 'name_english') {
        $allowed = ['name_english', 'name_french'];
        if (!in_array($name_lang, $allowed)) {
            $name_lang = 'name_english';
        }
        return $this->db
            ->select('pc.id_player_competitor, pc.id_competitor, pc.id_resort,
                      pc.marketing_level, pc.ticket_discount, pc.lift_investment,
                      pc.last_updated,
                      cr.' . $name_lang . ' AS competitor_name,
                      cr.base_reputation, cr.base_ticket_price, cr.base_lift_level')
            ->from('game_player_competitors pc')
            ->join('game_competitor_resorts cr', 'pc.id_competitor = cr.id_competitor', 'inner')
            ->where('pc.id_resort', $id_resort)
            ->order_by('cr.base_reputation', 'DESC')
            ->get();
    }

    /**
     * assign_competitors_to_resort  Inserts default rows for a resort if none
     *                               exist yet (called lazily on first page visit).
     *
     * @param int $id_resort
     */
    public function assign_competitors_to_resort($id_resort) {
        $existing = $this->db
            ->where('id_resort', $id_resort)
            ->count_all_results('game_player_competitors');

        if ($existing > 0) {
            return;
        }

        $competitors = $this->db
            ->select('id_competitor')
            ->from('game_competitor_resorts')
            ->get();

        foreach ($competitors->result() as $c) {
            $this->db->insert('game_player_competitors', [
                'id_resort'       => $id_resort,
                'id_competitor'   => $c->id_competitor,
                'marketing_level' => 0,
                'ticket_discount' => 0,
                'lift_investment' => 0,
                'last_updated'    => gmdate('Y-m-d'),
            ]);
        }
    }

    /**
     * get_single_player_competitor  Returns one row by id_player_competitor.
     *
     * @param int $id_player_competitor
     * @param int $id_resort
     */
    public function get_single_player_competitor($id_player_competitor, $id_resort) {
        return $this->db
            ->select('*')
            ->from('game_player_competitors')
            ->where('id_player_competitor', $id_player_competitor)
            ->where('id_resort', $id_resort)
            ->get();
    }

    /**
     * counter_marketing_DB  Reduces competitor's marketing_level (min 0).
     *
     * @param int $id_player_competitor
     * @param int $id_resort
     * @param int $reduction  How many levels to remove
     */
    public function counter_marketing_DB($id_player_competitor, $id_resort, $reduction = 2) {
        $this->db->trans_start();
        $this->db->set('marketing_level',
            'GREATEST(0, CAST(marketing_level AS SIGNED) - ' . (int) $reduction . ')', FALSE);
        $this->db->where('id_player_competitor', $id_player_competitor);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_player_competitors');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * invest_mega_lift_DB  Reduces competitor's lift_investment (min 0).
     *
     * @param int $id_player_competitor
     * @param int $id_resort
     * @param int $reduction
     */
    public function invest_mega_lift_DB($id_player_competitor, $id_resort, $reduction = 1) {
        $this->db->trans_start();
        $this->db->set('lift_investment',
            'GREATEST(0, CAST(lift_investment AS SIGNED) - ' . (int) $reduction . ')', FALSE);
        $this->db->where('id_player_competitor', $id_player_competitor);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_player_competitors');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * deduct_cash_DB  Deducts cash from a resort.
     *
     * @param int $id_resort
     * @param int $amount
     */
    public function deduct_cash_DB($id_resort, $amount) {
        $this->db->trans_start();
        $this->db->set('cash', 'cash - ' . (int) $amount, FALSE);
        $this->db->where('id_resort', $id_resort);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * get_resort_cash  Returns current cash balance for a resort.
     *
     * @param int $id_resort
     */
    public function get_resort_cash($id_resort) {
        $row = $this->db
            ->select('cash')
            ->from('game_resorts')
            ->where('id_resort', $id_resort)
            ->get()
            ->row();
        return $row ? (int) $row->cash : 0;
    }

    // -----------------------------------------------------------------------
    // Nightly job helpers
    // -----------------------------------------------------------------------

    /**
     * get_all_player_competitors_for_nightly
     *   Returns all rows from game_player_competitors (used by nightly cron).
     */
    public function get_all_player_competitors_for_nightly() {
        return $this->db
            ->select('*')
            ->from('game_player_competitors')
            ->get();
    }

    /**
     * nightly_evolve_competitor  Applies nightly AI behaviour for one row.
     *
     * @param int $id_player_competitor
     * @param int $delta_marketing   Amount to add to marketing_level (can be 0)
     * @param int $delta_discount    Amount to add to ticket_discount   (can be 0)
     * @param int $delta_lift        Amount to add to lift_investment    (can be 0)
     */
    public function nightly_evolve_competitor(
        $id_player_competitor,
        $delta_marketing,
        $delta_discount,
        $delta_lift
    ) {
        $this->db->trans_start();
        if ($delta_marketing !== 0) {
            $this->db->set('marketing_level',
                'LEAST(10, GREATEST(0, CAST(marketing_level AS SIGNED) + ' . (int) $delta_marketing . '))', FALSE);
        }
        if ($delta_discount !== 0) {
            $this->db->set('ticket_discount',
                'LEAST(50, GREATEST(0, CAST(ticket_discount AS SIGNED) + ' . (int) $delta_discount . '))', FALSE);
        }
        if ($delta_lift !== 0) {
            $this->db->set('lift_investment',
                'LEAST(5, GREATEST(0, CAST(lift_investment AS SIGNED) + ' . (int) $delta_lift . '))', FALSE);
        }
        $this->db->set('last_updated', gmdate('Y-m-d'));
        $this->db->where('id_player_competitor', $id_player_competitor);
        $this->db->update('game_player_competitors');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * get_competitor_penalty  Calculates visitor-pressure penalty (0-20 %) for a resort.
     *
     * Formula:  attractiveness = marketing * 2 + discount * 0.4 + lift * 3
     *           penalty % = min(20, attractiveness * 0.5)
     *
     * @param int $id_resort
     * @return float  Penalty as a percentage (0–20)
     */
    public function get_competitor_penalty($id_resort) {
        $rows = $this->db
            ->select('marketing_level, ticket_discount, lift_investment')
            ->from('game_player_competitors')
            ->where('id_resort', $id_resort)
            ->get();

        if ($rows->num_rows() === 0) {
            return 0.0;
        }

        $total_attractiveness = 0.0;
        foreach ($rows->result() as $r) {
            $total_attractiveness += $r->marketing_level * 2
                + $r->ticket_discount * 0.4
                + $r->lift_investment * 3;
        }

        // Average across competitors so having more competitors doesn't
        // automatically multiply the penalty.
        $avg = $total_attractiveness / $rows->num_rows();
        return min(20.0, $avg * 0.5);
    }
}
