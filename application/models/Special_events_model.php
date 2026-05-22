<?php

class Special_events_model extends CI_Model {

    /**
     * get_all_special_events_data    Get all special events visible on the page
     *
     * @param  string $name_language        name_english / name_french
     * @param  string $description_language description_english / description_french
     * @param  int    $display_on_page_value
     * @return object CI DB query result
     */
    public function get_all_special_events_data($name_language, $description_language, $display_on_page_value) {
        $this->db->select('*');
        $this->db->where('display_on_page', $display_on_page_value);
        $this->db->from('game_special_events');
        $this->db->order_by('event_order', 'asc');
        $query = $this->db->get();
        return $query;
    }

    public function get_special_event_data($id_special_event) {
        $this->db->select('*');
        $this->db->from('game_special_events');
        $this->db->where('id_special_event', $id_special_event);
        $query = $this->db->get();
        return $query;
    }

    public function start_special_event_DB($data) {
        $insert = $this->db->insert('game_started_special_events', $data);
        return $insert;
    }

    public function select_last_special_event_player($currentResortID) {
        $this->db->select('*');
        $this->db->from('game_started_special_events');
        $this->db->where('id_resort', $currentResortID);
        $this->db->order_by('started_datetime', 'DESC');
        $query = $this->db->get();
        return $query;
    }

    public function get_building_friendly_name($infrastructure_type, $level_required, $name_language) {
        $this->db->select($name_language);
        $this->db->where('type', $infrastructure_type);
        $this->db->where('level', $level_required);
        $this->db->from('game_buildings');
        $this->db->limit('1');
        $query = $this->db->get();
        $result = $query->row();
        $name = $result->$name_language;
        return $name;
    }

    public function list_all_ongoing_special_events() {
        $this->db->select('*');
        $this->db->where('completed', 0);
        $this->db->from('game_started_special_events');
        $query = $this->db->get();
        return $query;
    }

    public function add_daily_stats_special_event_DB($id_started_special_event, $stat, $daily_value) {
        $this->db->trans_start();
        $this->db->set($stat, $stat . '+' . $daily_value, FALSE);
        $this->db->where('id_started_special_event', $id_started_special_event);
        $this->db->update('game_started_special_events');
        $this->db->limit('1');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $updated_rows;
    }

    public function mark_special_events_completed_DB($id_started_special_event) {
        $this->db->trans_start();
        $this->db->set('completed', 1);
        $this->db->where('id_started_special_event', $id_started_special_event);
        $this->db->update('game_started_special_events');
        $this->db->limit('1');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $updated_rows;
    }

    public function update_resort_column($currentUserID, $column, $value_to_add) {
        $this->db->set($column, $column . '+' . $value_to_add, FALSE);
        $this->db->where('id_player', $currentUserID);
        $this->db->limit(1);
        $this->db->update('game_resorts');
        if ($this->db->affected_rows() === 1) {
            return true;
        }
        return false;
    }

    public function history_count_special_event($currentResortID, $id_special_event) {
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('id_special_event', $id_special_event);
        $this->db->where('completed', 1);
        $count = $this->db->count_all_results('game_started_special_events');
        return $count;
    }

    public function history_count_all_special_events($currentResortID) {
        $this->db->where('id_resort', $currentResortID);
        $this->db->where('completed', 1);
        $count = $this->db->count_all_results('game_started_special_events');
        return $count;
    }

    public function get_special_event_history_with_info_DB($currentResortID, $name_language) {
        $allowed_lang_columns = ['name_english', 'name_french'];
        if (!in_array($name_language, $allowed_lang_columns)) {
            $name_language = 'name_english';
        }
        $this->db->select('se.id_started_special_event, se.id_special_event, se.started_datetime, se.end_date, se.aggregated_visitors, se.aggregated_revenue, e.' . $name_language . ' as event_name, e.reputation_points');
        $this->db->from('game_started_special_events se');
        $this->db->join('game_special_events e', 'se.id_special_event = e.id_special_event', 'left');
        $this->db->where('se.id_resort', $currentResortID);
        $this->db->where('se.completed', 1);
        $this->db->order_by('se.end_date', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        return $query;
    }

    public function get_special_event_stats_DB($currentResortID) {
        $this->db->select('COUNT(se.id_started_special_event) as total_organized, COALESCE(SUM(se.aggregated_visitors), 0) as total_visitors, COALESCE(SUM(se.aggregated_revenue), 0) as total_revenue, COALESCE(SUM(e.reputation_points), 0) as total_reputation');
        $this->db->from('game_started_special_events se');
        $this->db->join('game_special_events e', 'se.id_special_event = e.id_special_event', 'left');
        $this->db->where('se.id_resort', $currentResortID);
        $this->db->where('se.completed', 1);
        $query = $this->db->get();
        return $query;
    }
}

?>
