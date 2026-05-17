<?php

class Leaderboard_model extends CI_Model{

    /**
     * Base SQL fragment shared by all leaderboard queries.
     */
    private function _base_select($sandbox_mode) {
        $sm = (int)$sandbox_mode;
        return 'SELECT `game_resorts`.`resort_name`, `game_resorts`.`resort_country`, `game_resorts`.`cash`, `game_resorts`.`reputation`, `game_resorts`.`prestige`, `game_players_tbl`.`username`, `game_players_tbl`.`id_player`, `game_resorts`.`id_resort`, `game_resorts`.`creation_time_resort`,
game_created_lifts_tbl.Count AS lift_count,
game_created_slopes_tbl.Count AS slope_count,
game_hired_staff_tbl.Count AS staff_count,
game_started_tournaments_tbl.Count AS tournament_count
FROM `game_resorts`
INNER JOIN `game_players` AS `game_players_tbl` ON `game_resorts`.`id_player` = `game_players_tbl`.`id_player` AND `game_players_tbl`.`sandbox_mode` = '.$sm.'
LEFT OUTER JOIN (
    SELECT  `game_created_lifts`.`id_resort`, COUNT(`game_created_lifts`.`id_created_lifts`) AS Count FROM `game_created_lifts` GROUP BY `game_created_lifts`.`id_resort`
) AS `game_created_lifts_tbl` ON `game_resorts`.`id_resort` = `game_created_lifts_tbl`.`id_resort`
LEFT OUTER JOIN (
    SELECT  `game_created_slopes`.`id_resort`, COUNT(`game_created_slopes`.`id_created_slopes`) AS Count FROM `game_created_slopes` GROUP BY `game_created_slopes`.`id_resort`
) AS `game_created_slopes_tbl` ON `game_resorts`.`id_resort` = `game_created_slopes_tbl`.`id_resort`
LEFT OUTER JOIN (
    SELECT  `game_hired_staff`.`id_resort`, COUNT(`game_hired_staff`.`id_hired_staff`) AS Count FROM `game_hired_staff` GROUP BY `game_hired_staff`.`id_resort`
) AS `game_hired_staff_tbl` ON `game_resorts`.`id_resort` =`game_hired_staff_tbl`.`id_resort`
LEFT OUTER JOIN (
    SELECT  `game_started_tournaments`.`id_resort`, COUNT(`game_started_tournaments`.`id_started_tournament`) AS Count FROM `game_started_tournaments` GROUP BY `game_started_tournaments`.`id_resort`
) AS `game_started_tournaments_tbl` ON `game_resorts`.`id_resort` =`game_started_tournaments_tbl`.`id_resort`';
    }

    public function get_leaderboard_stats_DB($currentUserID, $sandbox_mode){
        return $this->db->query($this->_base_select($sandbox_mode).'
ORDER BY `game_resorts`.`reputation` DESC');
    }

    /**
     * Get leaderboard filtered to a specific country/region, sorted by reputation.
     *
     * @param int    $currentUserID
     * @param int    $sandbox_mode
     * @param string $country       Country name to filter by
     */
    public function get_leaderboard_by_country_DB($currentUserID, $sandbox_mode, $country) {
        $escaped = $this->db->escape($country);
        return $this->db->query($this->_base_select($sandbox_mode).'
WHERE `game_resorts`.`resort_country` = '.$escaped.'
ORDER BY `game_resorts`.`reputation` DESC');
    }

    /**
     * Get global leaderboard sorted by slope count (descending).
     *
     * @param int $currentUserID
     * @param int $sandbox_mode
     */
    public function get_leaderboard_by_slope_DB($currentUserID, $sandbox_mode) {
        return $this->db->query($this->_base_select($sandbox_mode).'
ORDER BY game_created_slopes_tbl.Count DESC, `game_resorts`.`reputation` DESC');
    }

    /**
     * Get the top N non-sandbox resorts by reputation (for the public home page).
     *
     * @param int $limit  Number of top resorts to return (default 3)
     * @return array      Array of row objects with resort_name, resort_country, username, reputation, prestige, lift_count, slope_count
     */
    public function get_top_resorts($limit = 3) {
        $sql = $this->_base_select(0) . ' ORDER BY `game_resorts`.`reputation` DESC LIMIT ' . (int)$limit;
        $result = $this->db->query($sql);
        if ($result && $result->num_rows() > 0) {
            return $result->result();
        }
        return [];
    }

    /**
     * Get the resort country for the given player.
     *
     * @param int $currentUserID
     * @return string|null
     */
    public function get_user_resort_country_DB($currentUserID) {
        $query = $this->db
            ->select('resort_country')
            ->from('game_resorts')
            ->where('id_player', (int)$currentUserID)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row()->resort_country;
        }
        return NULL;
    }
}
?>