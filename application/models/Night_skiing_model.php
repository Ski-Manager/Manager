<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Night_skiing_model
 *
 * Manages per-trail night skiing settings (game_night_skiing_trails) and the
 * resort-level night skiing configuration (hours, night ticket price).
 */
class Night_skiing_model extends CI_Model {

    // -------------------------------------------------------------------------
    // Per-trail methods
    // -------------------------------------------------------------------------

    /**
     * get_trail_settings_DB    Returns all night skiing trail rows for a resort,
     *                          joined with game_created_slopes for the slope name
     *                          and status.
     *
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function get_trail_settings_DB($id_resort) {
        return $this->db
            ->select('gcs.id_created_slopes, gcs.id_slope, gcs.id_status, gcs.custom_name, gcs.quality,
                      gnst.id_night_skiing_trail, gnst.night_skiing_enabled,
                      gnst.light_type, gnst.brightness, gnst.pole_spacing,
                      gs.slope_type, gst.slope_type_name')
            ->from('game_created_slopes gcs')
            ->join('game_slopes gs', 'gs.id_slope = gcs.id_slope', 'left')
            ->join('game_slope_types gst', 'gst.id_slope_types = gs.slope_type', 'left')
            ->join('game_night_skiing_trails gnst',
                   'gnst.id_created_slope = gcs.id_created_slopes AND gnst.id_resort = ' . (int)$id_resort,
                   'left')
            ->where('gcs.id_resort', $id_resort)
            ->where('gcs.id_status', 1)   // only open slopes
            ->order_by('gcs.id_created_slopes', 'asc')
            ->get();
    }

    /**
     * save_trail_settings_DB   Inserts or updates the night skiing settings for a
     *                          single trail (upsert via ON DUPLICATE KEY UPDATE).
     *
     * @param int    $id_created_slope
     * @param int    $id_resort
     * @param int    $night_skiing_enabled  0 or 1
     * @param string $light_type            'led' | 'halogen' | 'metal_halide'
     * @param int    $brightness            1–5
     * @param int    $pole_spacing          15 | 25 | 35
     * @return bool
     */
    public function save_trail_settings_DB($id_created_slope, $id_resort, $night_skiing_enabled, $light_type, $brightness, $pole_spacing) {
        $sql = "INSERT INTO game_night_skiing_trails (id_created_slope, id_resort, night_skiing_enabled, light_type, brightness, pole_spacing)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                night_skiing_enabled = VALUES(night_skiing_enabled),
                light_type = VALUES(light_type),
                brightness = VALUES(brightness),
                pole_spacing = VALUES(pole_spacing)";

        $this->db->query($sql, [
            (int)$id_created_slope,
            (int)$id_resort,
            (int)$night_skiing_enabled,
            $light_type,
            (int)$brightness,
            (int)$pole_spacing
        ]);

        return true;
    }

    /**
     * get_night_skiing_trails_enabled_count    Returns how many open trails have
     *                                          night_skiing_enabled = 1 for a resort.
     *                                          Only counts slopes with id_status = 1
     *                                          (open), matching the cron's behaviour.
     *
     * @param int $id_resort
     * @return int
     */
    public function get_night_skiing_trails_enabled_count($id_resort) {
        return (int)$this->db
            ->from('game_night_skiing_trails gnst')
            ->join('game_created_slopes gcs', 'gcs.id_created_slopes = gnst.id_created_slope', 'inner')
            ->where('gnst.id_resort', (int)$id_resort)
            ->where('gnst.night_skiing_enabled', 1)
            ->where('gcs.id_status', 1)
            ->count_all_results();
    }

    /**
     * get_enabled_trails_with_settings_DB  Returns rows from game_night_skiing_trails
     *                                      that have night_skiing_enabled = 1,
     *                                      joined with game_created_slopes.
     *
     * Used by the nightly cron to calculate per-trail electricity costs.
     *
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function get_enabled_trails_with_settings_DB($id_resort) {
        return $this->db
            ->select('gnst.id_created_slope, gnst.light_type, gnst.brightness, gnst.pole_spacing')
            ->from('game_night_skiing_trails gnst')
            ->join('game_created_slopes gcs', 'gcs.id_created_slopes = gnst.id_created_slope', 'inner')
            ->where('gnst.id_resort', (int)$id_resort)
            ->where('gnst.night_skiing_enabled', 1)
            ->where('gcs.id_status', 1)   // slope must be open
            ->get();
    }

    // -------------------------------------------------------------------------
    // Resort-level night settings
    // -------------------------------------------------------------------------

    /**
     * get_night_settings_DB    Returns night_skiing_start_hour, night_skiing_end_hour,
     *                          night_skiing_ticket_price, night_skiing_entertainment,
     *                          and night_skiing_safety_level for a resort.
     *
     * @param int $id_resort
     * @return object|null
     */
    public function get_night_settings_DB($id_resort) {
        try {
            $row = $this->db
                ->select('night_skiing_start_hour, night_skiing_end_hour, night_skiing_ticket_price,
                          night_skiing_entertainment, night_skiing_safety_level,
                          night_skiing_school_enabled, night_skiing_school_price,
                          night_skiing_weather_suspend,
                          night_skiing_torchlight,
                          night_skiing_photo_enabled, night_skiing_photo_price')
                ->from('game_resorts')
                ->where('id_resort', (int)$id_resort)
                ->get()
                ->row();
            return $row;
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::get_night_settings_DB – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * save_night_settings_DB   Persists resort-level night settings.
     *
     * @param int    $id_resort
     * @param int    $start_hour         15–21
     * @param int    $end_hour           19–23
     * @param int    $ticket_price       >= 0
     * @param string $entertainment      'none' | 'basic' | 'premium'
     * @param int    $safety_level       1–3
     * @param int    $school_enabled     0 | 1
     * @param int    $school_price       0–999
     * @param int    $weather_suspend    0 | 1
     * @param int    $torchlight         0 | 1
     * @param int    $photo_enabled      0 | 1
     * @param int    $photo_price        0–299
     * @return bool
     */
    public function save_night_settings_DB($id_resort, $start_hour, $end_hour, $ticket_price, $entertainment = 'none', $safety_level = 1, $school_enabled = 0, $school_price = 0, $weather_suspend = 0, $torchlight = 0, $photo_enabled = 0, $photo_price = 0) {
        $this->db->trans_start();
        // Lock the resort row to prevent concurrent updates
        $this->db->query("SELECT id_resort FROM game_resorts WHERE id_resort = ? FOR UPDATE", [(int)$id_resort]);
        
        $this->db->set('night_skiing_start_hour',      (int)$start_hour);
        $this->db->set('night_skiing_end_hour',        (int)$end_hour);
        $this->db->set('night_skiing_ticket_price',    (int)$ticket_price);
        $this->db->set('night_skiing_entertainment',   $entertainment);
        $this->db->set('night_skiing_safety_level',    (int)$safety_level);
        $this->db->set('night_skiing_school_enabled',  (int)$school_enabled);
        $this->db->set('night_skiing_school_price',    (int)$school_price);
        $this->db->set('night_skiing_weather_suspend', (int)$weather_suspend);
        $this->db->set('night_skiing_torchlight',      (int)$torchlight);
        $this->db->set('night_skiing_photo_enabled',   (int)$photo_enabled);
        $this->db->set('night_skiing_photo_price',     (int)$photo_price);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resorts');
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    // -------------------------------------------------------------------------
    // Night skiing events (Set 2 - Modern API)
    // -------------------------------------------------------------------------

    /**
     * get_scheduled_events  Fetches events for a resort in an optional date range.
     *
     * @param int         $id_resort
     * @param string|null $from_date  Inclusive lower bound (YYYY-MM-DD)
     * @param string|null $to_date    Inclusive upper bound (YYYY-MM-DD)
     * @param string|null $status     Optional status filter (e.g. 'scheduled')
     * @return array|false            Array of stdClass rows on success, FALSE on error
     */
    public function get_scheduled_events($id_resort, $from_date = null, $to_date = null, $status = null) {
        try {
            $this->db->from('game_night_skiing_events');
            $this->db->where('id_resort', (int) $id_resort);

            if ($from_date !== null) {
                if (!$this->is_valid_event_date($from_date)) {
                    return false;
                }
                $this->db->where('scheduled_date >=', $from_date);
            }

            if ($to_date !== null) {
                if (!$this->is_valid_event_date($to_date)) {
                    return false;
                }
                $this->db->where('scheduled_date <=', $to_date);
            }

            if ($status !== null) {
                $this->db->where('status', $status);
            }

            $this->db->order_by('scheduled_date', 'ASC');
            $this->db->order_by('id', 'ASC');

            $result = $this->db->get();
            return $result ? $result->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::get_scheduled_events – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * get_event_by_id  Returns a single event row.
     *
     * @param int $event_id
     * @return object|null
     */
    public function get_event_by_id($event_id) {
        $event_id = (int) $event_id;
        if ($event_id <= 0) {
            return null;
        }

        try {
            $row = $this->db
                ->where('id', $event_id)
                ->get('game_night_skiing_events')
                ->row();
            return $row ?: null;
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::get_event_by_id – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * create_event  Creates a new night skiing event with validation and
     *               duplicate protection.
     *
     * @param int    $id_resort
     * @param string $event_type
     * @param string $scheduled_date       YYYY-MM-DD
     * @param float  $visitor_bonus_pct    0–100
     * @param float  $revenue_multiplier   >= 0
     * @param int    $cost                 >= 0
     * @param int    $reputation_bonus     >= 0 (DB column is UNSIGNED)
     * @return int|false                   New event ID on success, FALSE on failure
     */
    public function create_event($id_resort, $event_type, $scheduled_date, $visitor_bonus_pct, $revenue_multiplier, $cost, $reputation_bonus) {
        $id_resort          = (int) $id_resort;
        $event_type         = trim((string) $event_type);
        $scheduled_date     = trim((string) $scheduled_date);
        $visitor_bonus_pct  = (float) $visitor_bonus_pct;
        $revenue_multiplier = (float) $revenue_multiplier;
        $cost               = (int) $cost;
        $reputation_bonus   = (int) $reputation_bonus;

        if ($id_resort <= 0 || $event_type === '' || !$this->is_valid_event_date($scheduled_date)) {
            return false;
        }

        if (!$this->validate_event_numbers($visitor_bonus_pct, $revenue_multiplier, $cost, $reputation_bonus)) {
            return false;
        }

        try {
            // Prevent duplicates for same resort / type / date
            $exists = $this->db
                ->where('id_resort', $id_resort)
                ->where('event_type', $event_type)
                ->where('scheduled_date', $scheduled_date)
                ->where_in('status', ['scheduled', 'completed'])
                ->count_all_results('game_night_skiing_events');

            if ($exists > 0) {
                return false;
            }

            $data = [
                'id_resort'          => $id_resort,
                'event_type'         => $event_type,
                'scheduled_date'     => $scheduled_date,
                'status'             => 'scheduled',
                'visitor_bonus_pct'  => $visitor_bonus_pct,
                'revenue_multiplier' => $revenue_multiplier,
                'cost'               => $cost,
                'reputation_bonus'   => $reputation_bonus,
            ];

            $this->db->trans_start();
            $this->db->insert('game_night_skiing_events', $data);
            $new_id = (int) $this->db->insert_id();
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return false;
            }

            return $new_id > 0 ? $new_id : false;
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::create_event – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * update_event  Updates mutable fields on an existing event.
     *
     * @param int   $event_id
     * @param array $data               Allowed keys: event_type, scheduled_date,
     *                                  visitor_bonus_pct, revenue_multiplier,
     *                                  cost, reputation_bonus, status
     * @return bool
     */
    public function update_event($event_id, array $data) {
        $event_id = (int) $event_id;
        if ($event_id <= 0 || empty($data)) {
            return false;
        }

        $update = [];

        if (isset($data['event_type'])) {
            $update['event_type'] = trim((string) $data['event_type']);
        }

        if (isset($data['scheduled_date'])) {
            $date = trim((string) $data['scheduled_date']);
            if (!$this->is_valid_event_date($date)) {
                return false;
            }
            $update['scheduled_date'] = $date;
        }

        if (isset($data['visitor_bonus_pct'])) {
            $update['visitor_bonus_pct'] = (float) $data['visitor_bonus_pct'];
        }
        if (isset($data['revenue_multiplier'])) {
            $update['revenue_multiplier'] = (float) $data['revenue_multiplier'];
        }
        if (isset($data['cost'])) {
            $update['cost'] = (int) $data['cost'];
        }
        if (isset($data['reputation_bonus'])) {
            $update['reputation_bonus'] = (int) $data['reputation_bonus'];
        }
        if (isset($data['status'])) {
            $update['status'] = (string) $data['status'];
        }

        if (isset($update['visitor_bonus_pct']) || isset($update['revenue_multiplier']) || isset($update['cost']) || isset($update['reputation_bonus'])) {
            $vb = $update['visitor_bonus_pct'] ?? null;
            $rm = $update['revenue_multiplier'] ?? null;
            $ct = $update['cost'] ?? null;
            $rb = $update['reputation_bonus'] ?? null;
            if (!$this->validate_event_numbers($vb, $rm, $ct, $rb)) {
                return false;
            }
        }

        if (empty($update)) {
            return false;
        }

        try {
            $this->db->trans_start();
            $this->db->where('id', $event_id);
            $this->db->update('game_night_skiing_events', $update);
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE && $affected > 0;
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::update_event – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * delete_event  Physically deletes a pending event.
     *
     * @param int $event_id
     * @return bool
     */
    public function delete_event($event_id) {
        $event_id = (int) $event_id;
        if ($event_id <= 0) {
            return false;
        }

        try {
            $this->db->trans_start();
            $this->db->where('id', $event_id);
            $this->db->where('status', 'scheduled');
            $this->db->delete('game_night_skiing_events');
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE && $affected > 0;
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::delete_event – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * complete_event  Marks an event as completed in a transaction-safe way.
     *
     * @param int $event_id
     * @return bool
     */
    public function complete_event($event_id) {
        $event_id = (int) $event_id;
        if ($event_id <= 0) {
            return false;
        }

        try {
            $this->db->trans_start();
            $this->db->set('status', 'completed');
            $this->db->where('id', $event_id);
            $this->db->update('game_night_skiing_events');
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            return $this->db->trans_status() !== FALSE && $affected > 0;
        } catch (Exception $e) {
            log_message('error', 'Night_skiing_model::complete_event – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * is_valid_event_date  Simple YYYY-MM-DD validator.
     *
     * @param string $date
     * @return bool
     */
    private function is_valid_event_date($date) {
        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * validate_event_numbers  Validates numeric fields for events. Any NULL
     *                          values are ignored (useful for partial updates).
     *
     * @param float|null $visitor_bonus_pct
     * @param float|null $revenue_multiplier
     * @param int|null   $cost
     * @param int|null   $reputation_bonus
     * @return bool
     */
    private function validate_event_numbers($visitor_bonus_pct = null, $revenue_multiplier = null, $cost = null, $reputation_bonus = null) {
        if ($visitor_bonus_pct !== null && ($visitor_bonus_pct < 0 || $visitor_bonus_pct > 100)) {
            return false;
        }

        if ($revenue_multiplier !== null && ($revenue_multiplier < 0 || $revenue_multiplier > 10)) {
            return false;
        }

        if ($cost !== null && ($cost < 0 || $cost > 100000)) {
            return false;
        }

        // DB column rep_bonus is currently UNSIGNED TINYINT, so clamp to >= 0
        if ($reputation_bonus !== null && ($reputation_bonus < 0 || $reputation_bonus > 5)) {
            return false;
        }

        return true;
    }

    // --- NIGHT SKIING EVENTS ---
    
    // get_upcoming_events_DB removed (use get_scheduled_events)

}
