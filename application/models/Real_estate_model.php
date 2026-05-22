<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Real_estate_model
 *
 * Manages private real estate development (game_real_estate table).
 * Properties can be developed, kept for rent, or sold.
 */
class Real_estate_model extends CI_Model {

    /**
     * get_properties_DB    Returns all real estate properties for a resort.
     *
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function get_properties_DB($id_resort) {
        return $this->db
            ->select('id_real_estate, id_resort, property_type, id_status, build_date, completion_date')
            ->from('game_real_estate')
            ->where('id_resort', (int)$id_resort)
            ->order_by('id_real_estate', 'ASC')
            ->get();
    }

    /**
     * get_renting_properties_DB    Returns all renting properties for a resort.
     *
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function get_renting_properties_DB($id_resort) {
        return $this->db
            ->select('id_real_estate, property_type')
            ->from('game_real_estate')
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', 1)   // 1 = renting
            ->get();
    }

    /**
     * count_under_construction_DB  Returns the number of properties under construction for a resort.
     *
     * @param int $id_resort
     * @return int
     */
    public function count_under_construction_DB($id_resort) {
        return (int)$this->db
            ->from('game_real_estate')
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', 4)   // 4 = under_construction
            ->count_all_results();
    }

    /**
     * build_property_DB    Inserts a new property under construction.
     *
     * @param int    $id_resort
     * @param string $property_type  Key from REAL_ESTATE_TYPES
     * @param int    $build_days     Days to build
     * @return bool
     */
    public function build_property_DB($id_resort, $property_type, $build_days) {
        $now           = gmdate('Y-m-d H:i:s');
        $completion    = gmdate('Y-m-d H:i:s', strtotime('+' . (int)$build_days . ' days'));
        $data = [
            'id_resort'       => (int)$id_resort,
            'property_type'   => $property_type,
            'id_status'       => 4,              // under construction
            'build_date'      => $now,
            'completion_date' => $completion,
        ];
        return $this->db->insert('game_real_estate', $data);
    }

    /**
     * update_status_DB     Updates the status of a property.
     *
     * @param int $id_real_estate
     * @param int $id_resort       Used to verify ownership
     * @param int $id_status       New status (1=renting, 2=for_sale, 3=sold)
     * @return bool
     */
    public function update_status_DB($id_real_estate, $id_resort, $id_status) {
        $this->db->where('id_real_estate', (int)$id_real_estate);
        $this->db->where('id_resort', (int)$id_resort);
        return $this->db->update('game_real_estate', ['id_status' => (int)$id_status]);
    }

    /**
     * check_completed_constructions_DB     Returns properties whose build time has elapsed
     *                                      but are still marked as under construction.
     *
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function check_completed_constructions_DB($id_resort) {
        return $this->db
            ->select('id_real_estate, property_type')
            ->from('game_real_estate')
            ->where('id_resort', (int)$id_resort)
            ->where('id_status', 4)
            ->where('completion_date <=', gmdate('Y-m-d H:i:s'))
            ->get();
    }

    /**
     * complete_construction_DB     Marks a completed property as renting (status 1).
     *
     * @param int $id_real_estate
     * @param int $id_resort
     * @return bool
     */
    public function complete_construction_DB($id_real_estate, $id_resort) {
        $this->db->where('id_real_estate', (int)$id_real_estate);
        $this->db->where('id_resort', (int)$id_resort);
        return $this->db->update('game_real_estate', ['id_status' => 1]);
    }

    /**
     * auto_complete_constructions_DB   Marks all completed constructions as renting
     *                                  for all resorts (used in nightly cron).
     *
     * @return bool
     */
    public function auto_complete_constructions_DB() {
        $this->db->where('id_status', 4);
        $this->db->where('completion_date <=', gmdate('Y-m-d H:i:s'));
        return $this->db->update('game_real_estate', ['id_status' => 1]);
    }

    /**
     * get_property_DB  Returns a single property row.
     *
     * @param int $id_real_estate
     * @param int $id_resort
     * @return CI_DB_result
     */
    public function get_property_DB($id_real_estate, $id_resort) {
        return $this->db
            ->select('id_real_estate, id_resort, property_type, id_status, build_date, completion_date')
            ->from('game_real_estate')
            ->where('id_real_estate', (int)$id_real_estate)
            ->where('id_resort', (int)$id_resort)
            ->get();
    }

    /**
     * get_all_renting_by_resort_DB     Returns aggregated rent income per resort
     *                                  (used in nightly cron).
     *
     * @return CI_DB_result  rows: id_resort, property_type, count
     */
    public function get_all_renting_by_resort_DB() {
        return $this->db
            ->select('id_resort, property_type, COUNT(*) as count')
            ->from('game_real_estate')
            ->where('id_status', 1)
            ->group_by(['id_resort', 'property_type'])
            ->get();
    }
}
