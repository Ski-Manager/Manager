<?php

class Tournaments_model extends CI_Model{
    
    
    
    /**
     * get_all_tournaments_data      Get all tournaments and the status for the user
     * 
     * @param type $name_language           Name column for the specific language (name_english, name_french...)
     * @param type $description_language    Description column for the specific language (description_english, description_french...)
     * @return type                 Returns the query's results
     */
    public function get_all_tournaments_data($name_language, $description_language, $display_on_page_value){
        $this->db->select('*');
        $this->db->where('display_on_page', $display_on_page_value);
        $this->db->from('game_tournaments');
        $this->db->order_by('event_order', 'asc');
        $query = $this->db->get();
        return $query;
    }
    
    public function get_tournament_data($id_tournament){
        $this->db->select('*');
        $this->db->from('game_tournaments');
        $this->db->where('id_tournament', $id_tournament);
        $query = $this->db->get();
        return $query;
    }
    
    
    public function start_tournament_DB($data){
        $insert = $this->db->insert('game_started_tournaments', $data);
        return $insert;
    }
    
    
    public function select_last_tournament_player($currentResortID) {
        $this->db->select('*');
        $this->db->from('game_started_tournaments');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('started_datetime', 'DESC');
        $query = $this->db->get();
        return $query;
    }
    
    
    public function get_building_friendly_name($infrastructure_type, $level_required, $name_language){
        $this->db->select($name_language);
        $this->db->where('type', $infrastructure_type);
        $this->db->where('level', $level_required);
        $this->db->from('game_buildings');
        $this->db->limit('1');
        $query = $this->db->get();
        $result = $query->row();
        $name = $result->$name_language;
        return $name;       // Returns e.g. "Small sport campus"
    }
    
    public function list_all_ongoing_tournaments(){
        $this->db->select('*');
        $this->db->where('completed', 0);
        $this->db->from('game_started_tournaments');
        $query = $this->db->get();
        return $query;   
    }
    
    public function add_daily_stats_tournament_DB($id_started_tournament, $stat, $daily_value){
        $this->db->trans_start();           
        $this->db->set($stat, $stat.'+'.$daily_value,FALSE);
        $this->db->where('id_started_tournament', $id_started_tournament);
        $this->db->limit(1);
        $this->db->update('game_started_tournaments');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    public function mark_tournaments_completed_DB($id_started_tournament){
        $this->db->trans_start();
        $this->db->set('completed', 1);
        $this->db->where('id_started_tournament' , $id_started_tournament);
        $this->db->limit(1);
        $this->db->update('game_started_tournaments');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return false;
        }
        else {
            return $updated_rows;
        }
    }
    
    public function update_resort_column($currentUserID, $column, $value_to_add){
        $this->db->set($column, $column.'+'.$value_to_add,FALSE);
        $this->db->where('id_player' , $currentUserID);  
        $this->db->limit(1); 
        $this->db->update('game_resorts');
        if ($this->db->affected_rows() === 1 ){         // If one line was modified
            return true;
        } else {
            return false;                 // This should never happen
        }
    }
    
    
    public function history_count_tournament($currentResortID, $id_tournament){
        $this->db->where('id_resort', $currentResortID) ;   
        $this->db->where('id_tournament', $id_tournament) ;   
        $this->db->where('completed', 1);
        $count = $this->db->count_all_results('game_started_tournaments');
        return $count;
    }
    
    public function history_count_all_tournaments($currentResortID){
        $this->db->where('id_resort', $currentResortID) ;     
        $this->db->where('completed', 1);
        $count = $this->db->count_all_results('game_started_tournaments');
        return $count;
    }

    public function get_tournament_history_with_info_DB($currentResortID, $name_language) {
        $allowed_lang_columns = ['name_english', 'name_french'];
        if (!in_array($name_language, $allowed_lang_columns)) {
            $name_language = 'name_english';
        }
        $this->db->select('st.id_started_tournament, st.id_tournament, st.started_datetime, st.end_date, st.aggregated_visitors, st.aggregated_revenue, t.'.$name_language.' as tournament_name, t.tournament_points');
        $this->db->from('game_started_tournaments st');
        $this->db->join('game_tournaments t', 'st.id_tournament = t.id_tournament', 'left');
        $this->db->where('st.id_resort', $currentResortID);
        $this->db->where('st.completed', 1);
        $this->db->order_by('st.end_date', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query;
    }

    public function get_tournament_stats_DB($currentResortID) {
        $this->db->select('COUNT(st.id_started_tournament) as total_organized, COALESCE(SUM(st.aggregated_visitors), 0) as total_visitors, COALESCE(SUM(st.aggregated_revenue), 0) as total_revenue, COALESCE(SUM(t.tournament_points), 0) as total_prestige');
        $this->db->from('game_started_tournaments st');
        $this->db->join('game_tournaments t', 'st.id_tournament = t.id_tournament', 'left');
        $this->db->where('st.id_resort', $currentResortID);
        $this->db->where('st.completed', 1);
        $query = $this->db->get();
        return $query;
    }
    
    
}

?>