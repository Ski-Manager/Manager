<?php

class Micro_events_model extends CI_Model {

    /**
     * Event definitions: cash and reputation consequences for each type and choice.
     * Keys are event_type strings; choices are 'a' (first option) and 'b' (second option).
     */
    public static function get_event_definitions() {
        return [
            'vip_queue_jump' => [
                'a' => ['cash' => MICRO_VIP_ACCEPT_CASH,    'rep' => MICRO_VIP_ACCEPT_REP],
                'b' => ['cash' => MICRO_VIP_DECLINE_CASH,   'rep' => MICRO_VIP_DECLINE_REP],
            ],
            'press_interview' => [
                'a' => ['cash' => MICRO_PRESS_ACCEPT_CASH,  'rep' => MICRO_PRESS_ACCEPT_REP],
                'b' => ['cash' => MICRO_PRESS_DECLINE_CASH, 'rep' => MICRO_PRESS_DECLINE_REP],
            ],
            'equipment_deal' => [
                'a' => ['cash' => MICRO_EQUIP_ACCEPT_CASH,  'rep' => MICRO_EQUIP_ACCEPT_REP],
                'b' => ['cash' => MICRO_EQUIP_DECLINE_CASH, 'rep' => MICRO_EQUIP_DECLINE_REP],
            ],
            'lost_skier' => [
                'a' => ['cash' => MICRO_SKIER_PATROL_CASH,  'rep' => MICRO_SKIER_PATROL_REP],
                'b' => ['cash' => MICRO_SKIER_WAIT_CASH,    'rep' => MICRO_SKIER_WAIT_REP],
            ],
        ];
    }

    /**
     * insert_micro_event_DB    Records a new pending micro-event for a resort.
     *
     * @param int    $id_resort   Resort ID
     * @param int    $id_player   Player ID
     * @param string $event_type  One of the event_type keys
     * @param string $created_at  Datetime string (Y-m-d H:i:s)
     * @param string $expires_at  Datetime string (Y-m-d H:i:s)
     * @return bool
     */
    public function insert_micro_event_DB($id_resort, $id_player, $event_type, $created_at, $expires_at) {
        $this->db->trans_start();
        $this->db->insert('game_resort_micro_events', [
            'id_resort'  => $id_resort,
            'id_player'  => $id_player,
            'event_type' => $event_type,
            'created_at' => $created_at,
            'expires_at' => $expires_at,
            'status'     => 'pending',
        ]);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * get_pending_micro_event_DB   Returns the oldest pending (non-expired) micro-event for a player.
     *
     * @param int $id_player    Player ID
     * @return object|null      Row object or null if none pending
     */
    public function get_pending_micro_event_DB($id_player) {
        $now = gmdate('Y-m-d H:i:s');
        $row = $this->db
            ->select('*')
            ->from('game_resort_micro_events')
            ->where('id_player', $id_player)
            ->where('status', 'pending')
            ->where('expires_at >', $now)
            ->order_by('created_at', 'ASC')
            ->limit(1)
            ->get()
            ->row();
        return $row ?: null;
    }

    /**
     * respond_to_event_DB  Records the player's choice and applies cash / reputation deltas.
     *
     * Cash is stored in game_resorts.cash; reputation in game_resorts.reputation.
     *
     * @param int    $id_micro_event   Micro-event ID
     * @param int    $id_player        Player ID (ownership check)
     * @param int    $id_resort        Resort ID (for cash / reputation updates)
     * @param string $choice           'a' or 'b'
     * @param int    $cash_delta       Cash change (can be negative)
     * @param int    $reputation_delta Reputation change (can be negative)
     * @return bool
     */
    public function respond_to_event_DB($id_micro_event, $id_player, $id_resort, $choice, $cash_delta, $reputation_delta) {
        $new_status = ($choice === 'a') ? 'accepted' : 'declined';

        $this->db->trans_start();

        // Mark the event as responded
        $this->db->set('status',           $new_status);
        $this->db->set('choice_made',      $choice);
        $this->db->set('cash_delta',       (int)$cash_delta);
        $this->db->set('reputation_delta', (int)$reputation_delta);
        $this->db->where('id_micro_event', (int)$id_micro_event);
        $this->db->where('id_player',      (int)$id_player);
        $this->db->where('status',         'pending');
        $this->db->update('game_resort_micro_events');
        $updated = $this->db->affected_rows();

        if ($updated > 0) {
            // Apply cash change to game_resorts.cash
            if ($cash_delta !== 0) {
                $this->db
                    ->set('cash', 'cash + ' . (int)$cash_delta, FALSE)
                    ->where('id_resort', (int)$id_resort)
                    ->update('game_resorts');
            }

            // Apply reputation change to game_resorts.reputation (floor at 0)
            if ($reputation_delta !== 0) {
                $this->db
                    ->set('reputation', 'GREATEST(0, reputation + ' . (int)$reputation_delta . ')', FALSE)
                    ->where('id_resort', (int)$id_resort)
                    ->update('game_resorts');
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE && $updated > 0;
    }

    /**
     * expire_old_events_DB     Marks all pending events past their expiry time as 'expired'.
     *
     * @return int  Number of events expired
     */
    public function expire_old_events_DB() {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->trans_start();
        $this->db
            ->set('status', 'expired')
            ->where('status', 'pending')
            ->where('expires_at <', $now)
            ->update('game_resort_micro_events');
        $count = $this->db->affected_rows();
        $this->db->trans_complete();
        return $count;
    }

    /**
     * count_pending_micro_events_DB    Counts non-expired pending micro-events for a player.
     *
     * @param int $id_player    Player ID
     * @return int
     */
    public function count_pending_micro_events_DB($id_player) {
        $now = gmdate('Y-m-d H:i:s');
        return $this->db
            ->where('id_player', $id_player)
            ->where('status', 'pending')
            ->where('expires_at >', $now)
            ->count_all_results('game_resort_micro_events');
    }
}
