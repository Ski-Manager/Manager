<?php

class Statistics_model extends CI_Model{

    /**
     * get_lift_usage_DB    Returns lifts with their effective throughput for the resort
     *
     * @param int $currentResortID  ID of the resort
     * @return object               Query result
     */
    public function get_lift_usage_DB($currentResortID){
        return $this->db
            ->select('game_lifts.throughput, game_lifts.name_english, game_lifts.name_french, game_created_lifts.custom_name, game_created_lifts.lift_condition, game_created_lifts.id_status')
            ->from('game_created_lifts')
            ->join('game_lifts', 'game_lifts.id_group = game_created_lifts.id_group AND game_lifts.level = game_created_lifts.level', 'inner')
            ->where('game_created_lifts.id_resort', $currentResortID)
            ->where('game_created_lifts.id_status', 1)
            ->order_by('game_lifts.throughput * game_created_lifts.lift_condition / 100', 'desc')
            ->get();
    }

    /**
     * get_revenue_per_lift_DB  Returns lift daily costs and skipass revenue for the resort
     *
     * @param int $currentResortID  ID of the resort
     * @return object               Query result
     */
    public function get_revenue_per_lift_DB($currentResortID){
        return $this->db
            ->select('game_lifts.daily_cost, game_lifts.throughput, game_lifts.name_english, game_lifts.name_french, game_created_lifts.custom_name, game_created_lifts.lift_condition')
            ->from('game_created_lifts')
            ->join('game_lifts', 'game_lifts.id_group = game_created_lifts.id_group AND game_lifts.level = game_created_lifts.level', 'inner')
            ->where('game_created_lifts.id_resort', $currentResortID)
            ->where('game_created_lifts.id_status', 1)
            ->order_by('game_lifts.throughput', 'desc')
            ->get();
    }

    /**
     * get_skipass_revenue_DB   Returns yesterday's skipass revenue for the resort
     *
     * @param int    $currentResortID    ID of the resort
     * @param string $yesterday_GMT      Yesterday's date in Y-m-d format
     * @return float                     Skipass revenue
     */
    public function get_skipass_revenue_DB($currentResortID, $yesterday_GMT){
        $query = $this->db
            ->select('rev_skipass')
            ->from('game_resort_rev_skipass')
            ->where('id_resort', $currentResortID)
            ->where('date', $yesterday_GMT)
            ->get();
        return (float) $query->row('rev_skipass');
    }

    /**
     * get_slope_popularity_DB  Returns slopes with their condition for the resort, ordered by condition desc
     *
     * @param int $currentResortID  ID of the resort
     * @return object               Query result
     */
    public function get_slope_popularity_DB($currentResortID){
        return $this->db
            ->select('game_slopes.name_english, game_slopes.name_french, game_created_slopes.slope_condition, game_created_slopes.id_status')
            ->from('game_created_slopes')
            ->join('game_slopes', 'game_slopes.id_slope = game_created_slopes.id_slope', 'inner')
            ->where('game_created_slopes.id_resort', $currentResortID)
            ->where('game_created_slopes.id_status', 1)
            ->order_by('game_created_slopes.slope_condition', 'desc')
            ->get();
    }

    /**
     * get_satisfaction_history_DB  Returns reputation history for the resort from the given start date
     *
     * @param int    $currentResortID    ID of the resort
     * @param string $graph_start_date   Start date in Y-m-d format
     * @return object                    Query result
     */
    public function get_satisfaction_history_DB($currentResortID, $graph_start_date){
        $today_GMT = gmdate('Y-m-d', strtotime('now'));
        return $this->db
            ->select('date, reputation')
            ->from('game_resort_reputation')
            ->where('date>=', $graph_start_date)
            ->where('date<', $today_GMT)
            ->where('id_resort', $currentResortID)
            ->order_by('date', 'asc')
            ->get();
    }

    /**
     * get_weather_history_DB   Returns snow level history for the resort from the given start date
     *
     * @param int    $currentResortID    ID of the resort
     * @param string $graph_start_date   Start date in Y-m-d format
     * @return object                    Query result
     */
    public function get_weather_history_DB($currentResortID, $graph_start_date){
        $today_GMT = gmdate('Y-m-d', strtotime('now'));
        return $this->db
            ->select('date, snow_level')
            ->from('game_resort_snow_level')
            ->where('date>=', $graph_start_date)
            ->where('date<', $today_GMT)
            ->where('id_resort', $currentResortID)
            ->order_by('date', 'asc')
            ->get();
    }

    /**
     * get_visitor_count_history_DB     Returns daily visitor count history for the resort from the given start date
     *
     * @param int    $currentResortID    ID of the resort
     * @param string $graph_start_date   Start date in Y-m-d format
     * @return object                    Query result
     */
    public function get_visitor_count_history_DB($currentResortID, $graph_start_date){
        $today_GMT = gmdate('Y-m-d', strtotime('now'));
        return $this->db
            ->select('date, affluence')
            ->from('game_resort_affluence')
            ->where('date>=', $graph_start_date)
            ->where('date<', $today_GMT)
            ->where('id_resort', $currentResortID)
            ->order_by('date', 'asc')
            ->get();
    }

    /**
     * get_revenue_history_DB   Returns daily revenue history for the resort from the given start date
     *
     * @param int    $currentResortID    ID of the resort
     * @param string $graph_start_date   Start date in Y-m-d format
     * @return object                    Query result
     */
    public function get_revenue_history_DB($currentResortID, $graph_start_date){
        $today_GMT = gmdate('Y-m-d', strtotime('now'));
        return $this->db
            ->select('date, revenue')
            ->from('game_resort_revenue')
            ->where('date>=', $graph_start_date)
            ->where('date<', $today_GMT)
            ->where('id_resort', $currentResortID)
            ->order_by('date', 'asc')
            ->get();
    }

    /**
     * get_expenses_history_DB  Returns daily expenses history for the resort from the given start date
     *
     * @param int    $currentResortID    ID of the resort
     * @param string $graph_start_date   Start date in Y-m-d format
     * @return object                    Query result
     */
    public function get_expenses_history_DB($currentResortID, $graph_start_date){
        $today_GMT = gmdate('Y-m-d', strtotime('now'));
        return $this->db
            ->select('date, expenses')
            ->from('game_resort_expenses')
            ->where('date>=', $graph_start_date)
            ->where('date<', $today_GMT)
            ->where('id_resort', $currentResortID)
            ->order_by('date', 'asc')
            ->get();
    }
}

?>
