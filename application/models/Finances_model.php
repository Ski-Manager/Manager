<?php

class Finances_model extends CI_Model{
    
    

    public function get_yesterday_specific_amount_DB($currentResortID, $type, $date_GMT){
        $query = $this->db
            ->select($type)
            ->from('game_resort_'.$type)
            ->where('date', $date_GMT)
            ->where('id_resort', $currentResortID)
            ->get();
        return $query->row($type);
    }
    
    public function get_lastXdays_specific_amount_DB($currentResortID, $type, $number_of_days, $today){
        $query = $this->db
            ->select('SUM('.$type.') as total')
            ->from('game_resort_'.$type)
            ->where('date>=', $number_of_days)
            ->where('date<=', $today)
            ->where('id_resort', $currentResortID)
            ->get();
        $total = $query->row('total');
        if ($total === null || $total < 0)
            $total = 0;
        return $total;
    }
    
    public function get_history_for_graph($currentResortID, $type, $graph_start_date){
        $today = strtotime('now');
        $today_GMT = gmdate('Y-m-d', $today); 
        $query = $this->db
            ->select('date, '.$type)
            ->from('game_resort_'.$type)
            ->where('date>=', $graph_start_date)
            ->where('id_resort', $currentResortID)
            ->where('date<', $today_GMT)
            ->order_by('date', 'asc')
            ->get();
        return $query;
    }
    
    public function get_history_for_graph_spec_date($currentResortID, $type, $date_GMT){
        $query = $this->db
            ->select('date, '.$type)
            ->from('game_resort_'.$type)
            ->where('date', $date_GMT)
            ->where('id_resort', $currentResortID)
            ->get();
        return $query->row($type);
    }
}

?>