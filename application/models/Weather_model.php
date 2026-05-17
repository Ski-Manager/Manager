<?php

class Weather_model extends CI_Model{
    
    public function select_weather_forecast($date){                
        $query = $this->db
            ->select('*')
            ->from('game_weather_forecast')
            ->where('date', $date)
            ->get();
        if ($query->num_rows() > 0)
            return $query;
        else
            return false;
    }
    
    
    public function test_forecast_status($currentUserID, $today_GMT){                
        $query = $this->db
            ->select('*')
            ->from('game_extended_forecast')
            ->where('id_player', $currentUserID)
            ->where('end_forecast>=', $today_GMT)
            ->get();
        if ($query->num_rows() > 0)
            return $query->row();
        else
            return false;
    }
    
    
    public function select_weather_conditions($IDtoBeSelected){                
        $query = $this->db
            ->select('*')
            ->from('game_weather_conditions')
            ->where('id_condition', $IDtoBeSelected)
            ->get();
        return $query;         // We return the selected array
    }
    
    public function get_forecast_dates_in_range($start_date, $end_date) {
        $query = $this->db
            ->select('date')
            ->from('game_weather_forecast')
            ->where('date >=', $start_date)
            ->where('date <=', $end_date)
            ->get();
        if ($query->num_rows() === 0) {
            return [];
        }
        return array_column($query->result_array(), 'date');
    }

    public function insert_weather_forecast_DB($date, $id_condition) {
        return $this->db->insert('game_weather_forecast', [
            'id_condition' => $id_condition,
            'date'         => $date,
        ]);
    }

    public function subscribe_extended_forecast_DB($data_ext_forecast) {  
        if ($insert = $this->db->insert('game_extended_forecast', $data_ext_forecast)) {                      
            return true;
        }
        else
            return false;
    }
    
    
}

?>