<?php

class Crisis_events_model extends CI_Model {

    /**
     * insert_crisis_event_DB   Records a new crisis event for a resort
     *
     * @param int    $id_resort          Resort ID
     * @param int    $id_player          Player ID
     * @param string $event_type         Event type key ('lift_failure', 'avalanche', 'power_outage', 'viral_negative')
     * @param string $event_date         Date/time string (Y-m-d H:i:s)
     * @param string $impact_description Human-readable description of the impact
     * @return bool
     */
    public function insert_crisis_event_DB($id_resort, $id_player, $event_type, $event_date, $impact_description) {
        $this->db->trans_start();
        $this->db->insert('game_resort_crisis_events', [
            'id_resort'          => $id_resort,
            'id_player'          => $id_player,
            'event_type'         => $event_type,
            'event_date'         => $event_date,
            'is_resolved'        => 0,
            'impact_description' => $impact_description,
        ]);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_active_crisis_events_DB  Returns unresolved crisis events for a player
     *
     * @param int $id_player    Player ID
     * @return object           CI query result
     */
    public function get_active_crisis_events_DB($id_player) {
        return $this->db
            ->select('*')
            ->from('game_resort_crisis_events')
            ->where('id_player', $id_player)
            ->where('is_resolved', 0)
            ->order_by('event_date', 'DESC')
            ->get();
    }

    /**
     * get_all_crisis_events_DB     Returns all crisis events for a player (active and resolved)
     *
     * @param int $id_player    Player ID
     * @return object           CI query result
     */
    public function get_all_crisis_events_DB($id_player) {
        return $this->db
            ->select('*')
            ->from('game_resort_crisis_events')
            ->where('id_player', $id_player)
            ->order_by('event_date', 'DESC')
            ->get();
    }

    /**
     * resolve_crisis_event_DB  Marks a crisis event as resolved
     *
     * @param int $id_crisis    Crisis event ID
     * @param int $id_player    Player ID (for ownership check)
     * @return bool
     */
    public function resolve_crisis_event_DB($id_crisis, $id_player) {
        $this->db->trans_start();
        $this->db->set('is_resolved', 1);
        $this->db->where('id_crisis', $id_crisis);
        $this->db->where('id_player', $id_player);
        $this->db->update('game_resort_crisis_events');
        $updated_rows = $this->db->affected_rows();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return $updated_rows > 0;
    }

    /**
     * count_active_crisis_events_DB    Counts unresolved crisis events for a player
     *
     * @param int $id_player    Player ID
     * @return int
     */
    public function count_active_crisis_events_DB($id_player) {
        return $this->db
            ->where('id_player', $id_player)
            ->where('is_resolved', 0)
            ->count_all_results('game_resort_crisis_events');
    }
}
