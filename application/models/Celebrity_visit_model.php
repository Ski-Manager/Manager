<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Celebrity_visit_model
 *
 * Manages celebrity / VIP visit events stored in game_resort_celebrity_visits.
 */
class Celebrity_visit_model extends CI_Model {

    /**
     * ensure_table_exists  Creates the table if it does not exist yet.
     */
    public function ensure_table_exists() {
        if (!$this->db->table_exists('game_resort_celebrity_visits')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `game_resort_celebrity_visits` (
                    `id_visit`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_resort`   INT(11) UNSIGNED NOT NULL,
                    `visit_type`  ENUM('influencer','pro_skier','film_crew') NOT NULL,
                    `slopes_good` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                    `lift_failed` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                    `rep_change`  INT(6) NOT NULL DEFAULT 0,
                    `visit_date`  DATE NOT NULL,
                    PRIMARY KEY (`id_visit`),
                    KEY `idx_resort` (`id_resort`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }

    /**
     * log_visit_DB     Records a celebrity visit event for a resort.
     *
     * @param int    $id_resort
     * @param string $visit_type   'influencer' | 'pro_skier' | 'film_crew'
     * @param int    $slopes_good  1 if avg slope condition met the threshold, 0 otherwise
     * @param int    $lift_failed  1 if a lift was in maintenance during the visit, 0 otherwise
     * @param int    $rep_change   Net reputation change (positive = gain, negative = loss)
     * @param string $visit_date   Y-m-d
     * @return bool
     */
    public function log_visit_DB($id_resort, $visit_type, $slopes_good, $lift_failed, $rep_change, $visit_date) {
        $data = [
            'id_resort'   => (int)$id_resort,
            'visit_type'  => $visit_type,
            'slopes_good' => (int)$slopes_good,
            'lift_failed' => (int)$lift_failed,
            'rep_change'  => (int)$rep_change,
            'visit_date'  => $visit_date,
        ];
        $this->db->insert('game_resort_celebrity_visits', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * get_recent_visits_DB     Returns the most recent visit rows for a resort.
     *
     * @param int $id_resort
     * @param int $days         How many past days to retrieve
     * @return CI_DB_result
     */
    public function get_recent_visits_DB($id_resort, $days = CELEBRITY_VISIT_HISTORY_DAYS) {
        $cutoff = date('Y-m-d', strtotime('-' . (int)$days . ' days'));
        return $this->db
            ->select('visit_type, slopes_good, lift_failed, rep_change, visit_date')
            ->from('game_resort_celebrity_visits')
            ->where('id_resort', (int)$id_resort)
            ->where('visit_date >=', $cutoff)
            ->order_by('visit_date', 'DESC')
            ->get();
    }
}
