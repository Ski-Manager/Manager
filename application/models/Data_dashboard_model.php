<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data_dashboard_model     Provides data for the Data Dashboard Mode charts.
 *
 * Charts covered:
 *   - Traffic heatmap  : lift & slope effective usage
 *   - Profit breakdown : yesterday's revenue by source
 *   - Visitor segmentation : daily visitors grouped by slope difficulty
 *   - Accident probability : risk score per open slope
 */
class Data_dashboard_model extends CI_Model {

    /**
     * get_traffic_heatmap_DB   Returns open lifts and slopes with a usage-intensity
     *                          value (0-100) derived from condition and throughput/difficulty.
     *
     * @param int $id_resort
     * @return array  Two keys: 'lifts' (CI_DB_result) and 'slopes' (CI_DB_result)
     */
    public function get_traffic_heatmap_DB($id_resort) {
        $lifts = $this->db
            ->select('game_created_lifts.custom_name, game_lifts.name_english, game_lifts.name_french,
                      game_lifts.throughput, game_created_lifts.lift_condition')
            ->from('game_created_lifts')
            ->join('game_lifts',
                   'game_lifts.id_group = game_created_lifts.id_group AND game_lifts.level = game_created_lifts.level',
                   'inner')
            ->where('game_created_lifts.id_resort', (int)$id_resort)
            ->where('game_created_lifts.id_status', 1)
            ->order_by('game_lifts.throughput * game_created_lifts.lift_condition / 100', 'desc')
            ->get();

        $slopes = $this->db
            ->select('game_created_slopes.custom_name, game_slopes.name_english, game_slopes.name_french,
                      game_slopes.id_difficulty, game_created_slopes.slope_condition')
            ->from('game_created_slopes')
            ->join('game_slopes', 'game_slopes.id_slope = game_created_slopes.id_slope', 'inner')
            ->where('game_created_slopes.id_resort', (int)$id_resort)
            ->where('game_created_slopes.id_status', 1)
            ->order_by('game_created_slopes.slope_condition', 'desc')
            ->get();

        return ['lifts' => $lifts, 'slopes' => $slopes];
    }

    /**
     * get_profit_breakdown_DB  Returns yesterday's revenue for each income source.
     *
     * @param int    $id_resort
     * @param string $yesterday_GMT   Date string Y-m-d
     * @return array  Associative: source_key => amount (float)
     */
    public function get_profit_breakdown_DB($id_resort, $yesterday_GMT) {
        $sources = [
            'skipass'    => 'rev_skipass',
            'restaurant' => 'rev_restaurant',
            'hotel'      => 'rev_hotel',
            'rental'     => 'rev_rental',
            'leisure'    => 'rev_leisure',
            'luxury'     => 'rev_luxury',
            'medical'    => 'rev_medical',
            'skibus'     => 'rev_skibus',
            'instructor' => 'rev_instructor',
            'parking'    => 'rev_parking',
            'other'      => 'rev_other',
        ];

        $result = [];
        foreach ($sources as $key => $table_suffix) {
            $table = 'game_resort_' . $table_suffix;
            $col   = $table_suffix;
            $row = $this->db
                ->select($col)
                ->from($table)
                ->where('id_resort', (int)$id_resort)
                ->where('date', $yesterday_GMT)
                ->get()
                ->row();
            $result[$key] = ($row && isset($row->$col)) ? (float)$row->$col : 0.0;
        }

        return $result;
    }

    /**
     * get_visitor_segmentation_DB  Returns daily visitor totals grouped by slope difficulty.
     *                              Uses game_guest_ai scores (daily_visitors per slope × difficulty).
     *
     * @param int $id_resort
     * @return CI_DB_result  Columns: id_difficulty, total_visitors
     */
    public function get_visitor_segmentation_DB($id_resort) {
        return $this->db
            ->select('gs.id_difficulty, SUM(ga.daily_visitors) AS total_visitors')
            ->from('game_guest_ai ga')
            ->join('game_created_slopes gcs', 'gcs.id_created_slopes = ga.id_created_slope', 'inner')
            ->join('game_slopes gs',          'gs.id_slope = gcs.id_slope',                  'inner')
            ->where('ga.id_resort', (int)$id_resort)
            ->group_by('gs.id_difficulty')
            ->order_by('gs.id_difficulty', 'asc')
            ->get();
    }

    /**
     * get_accident_probability_DB  Returns open slopes with data needed to compute
     *                              a risk score (difficulty × inverse-condition).
     *
     * @param int $id_resort
     * @return CI_DB_result  Columns: name_english, name_french, custom_name, id_difficulty, slope_condition
     */
    public function get_accident_probability_DB($id_resort) {
        return $this->db
            ->select('game_created_slopes.custom_name, game_slopes.name_english, game_slopes.name_french,
                      game_slopes.id_difficulty, game_created_slopes.slope_condition')
            ->from('game_created_slopes')
            ->join('game_slopes', 'game_slopes.id_slope = game_created_slopes.id_slope', 'inner')
            ->where('game_created_slopes.id_resort', (int)$id_resort)
            ->where('game_created_slopes.id_status', 1)
            ->order_by('game_slopes.id_difficulty', 'desc')
            ->get();
    }

    /**
     * get_kpi_data_DB  Returns resort KPIs: cash, reputation, snow, visitors, slope/lift counts.
     */
    public function get_kpi_data_DB($id_resort) {
        $resort = $this->db->select('cash, reputation, snow_level, skipass_daily')
            ->from('game_resorts')
            ->where('id_resort', (int)$id_resort)
            ->get()->row();

        $total_visitors = (int)$this->db->from('game_guest_ai')
            ->where('id_resort', (int)$id_resort)
            ->count_all_results();

        $open_slopes = (int)$this->db->from('game_created_slopes')
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', 1)
            ->count_all_results();

        $total_slopes = (int)$this->db->from('game_created_slopes')
            ->where('id_resort', (int)$id_resort)
            ->count_all_results();

        $open_lifts = (int)$this->db->from('game_created_lifts')
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', 1)
            ->count_all_results();

        $total_lifts = (int)$this->db->from('game_created_lifts')
            ->where('id_resort', (int)$id_resort)
            ->count_all_results();

        return [
            'cash'           => $resort ? (int)$resort->cash           : 0,
            'reputation'     => $resort ? (int)$resort->reputation      : 0,
            'snow_level'     => $resort ? (int)$resort->snow_level      : 0,
            'skipass_daily'  => $resort ? (int)$resort->skipass_daily   : 0,
            'total_visitors' => $total_visitors,
            'open_slopes'    => $open_slopes,
            'total_slopes'   => $total_slopes,
            'open_lifts'     => $open_lifts,
            'total_lifts'    => $total_lifts,
        ];
    }

    /**
     * get_revenue_trend_DB  Returns per-day total revenue for the last $days days.
     *
     * @return array  [ ['date'=>'Y-m-d', 'total'=>float, 'skipass'=>float, 'other'=>float], … ]
     */
    public function get_revenue_trend_DB($id_resort, $days = 7) {
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = gmdate('Y-m-d', strtotime("-$i days"));
        }

        $rev_by_date = array_fill_keys($dates, ['skipass' => 0, 'restaurant' => 0, 'hotel' => 0, 'rental' => 0,
                                                 'leisure' => 0, 'luxury' => 0, 'medical' => 0, 'skibus' => 0,
                                                 'instructor' => 0, 'parking' => 0, 'other' => 0]);

        $sources = ['skipass','restaurant','hotel','rental','leisure','luxury','medical','skibus','instructor','parking','other'];
        foreach ($sources as $src) {
            $col  = 'rev_' . $src;
            $rows = $this->db->select("date, $col")
                ->from('game_resort_' . $col)
                ->where('id_resort', (int)$id_resort)
                ->where_in('date', $dates)
                ->get()->result();
            foreach ($rows as $row) {
                if (isset($rev_by_date[$row->date])) {
                    $rev_by_date[$row->date][$src] += (float)$row->$col;
                }
            }
        }

        $result = [];
        foreach ($rev_by_date as $date => $breakdown) {
            $result[] = ['date' => $date, 'total' => array_sum($breakdown)] + $breakdown;
        }
        return $result;
    }

    /**
     * get_cost_trend_DB  Returns per-day cost breakdown for the last $days days.
     *
     * @return array  [ ['date'=>'Y-m-d', 'upkeep'=>float, 'salaries'=>float, 'expenses'=>float, 'purchases'=>float], … ]
     */
    public function get_cost_trend_DB($id_resort, $days = 7) {
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = gmdate('Y-m-d', strtotime("-$i days"));
        }

        $cost_by_date = array_fill_keys($dates, ['upkeep' => 0, 'salaries' => 0, 'expenses' => 0, 'purchases' => 0]);
        $cost_types   = ['upkeep', 'salaries', 'expenses', 'purchases'];

        foreach ($cost_types as $ct) {
            // game_resort_expenses uses no 'cost_' prefix on either table or column
            if ($ct === 'expenses') {
                $table = 'game_resort_expenses';
                $col   = 'expenses';
            } else {
                $col   = 'cost_' . $ct;
                $table = 'game_resort_' . $col;
            }
            $rows = $this->db->select("date, $col")
                ->from($table)
                ->where('id_resort', (int)$id_resort)
                ->where_in('date', $dates)
                ->get()->result();
            foreach ($rows as $row) {
                if (isset($cost_by_date[$row->date])) {
                    $cost_by_date[$row->date][$ct] += (float)$row->$col;
                }
            }
        }

        $result = [];
        foreach ($cost_by_date as $date => $breakdown) {
            $result[] = ['date' => $date] + $breakdown;
        }
        return $result;
    }

    /**
     * get_slopes_detail_DB  Returns all slopes with visitor count for the table view.
     */
    public function get_slopes_detail_DB($id_resort) {
        $id_resort = (int)$id_resort;
        // Use FALSE on select to prevent CI3 from splitting COALESCE on its comma
        $this->db->select('gcs.id_created_slopes, gcs.custom_name, gs.name_english, gs.name_french, gs.id_difficulty, gcs.slope_condition, gcs.id_status');
        $this->db->select('COALESCE(ga.daily_visitors, 0) AS daily_visitors', FALSE);
        $this->db->from('game_created_slopes gcs');
        $this->db->join('game_slopes gs', 'gs.id_slope = gcs.id_slope', 'inner');
        $this->db->join('game_guest_ai ga', 'ga.id_created_slope = gcs.id_created_slopes AND ga.id_resort = ' . $id_resort, 'left');
        $this->db->where('gcs.id_resort', $id_resort);
        $this->db->order_by('gs.id_difficulty', 'desc');
        $this->db->order_by('gcs.slope_condition', 'asc');
        return $this->db->get();
    }

    /**
     * get_lifts_detail_DB  Returns all lifts with condition for the table view.
     */
    public function get_lifts_detail_DB($id_resort) {
        return $this->db
            ->select('gcl.id_created_lifts, gcl.custom_name, gl.name_english, gl.name_french,
                      gl.throughput, gcl.lift_condition, gcl.id_status')
            ->from('game_created_lifts gcl')
            ->join('game_lifts gl', 'gl.id_group = gcl.id_group AND gl.level = gcl.level', 'inner')
            ->where('gcl.id_resort', (int)$id_resort)
            ->order_by('gcl.lift_condition', 'asc')
            ->get();
    }
}
