<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Environment_model
 *
 * Manages the game_resort_environment table:
 *   eco_reputation, carbon_footprint, noise_pollution,
 *   wildlife_zone, solar_panels, electric_groomers,
 *   tree_count, water_recycling, expansion_restricted.
 */
class Environment_model extends CI_Model {

    /**
     * get_environment_DB   Returns the environment row for a resort.
     *                      If the row does not exist yet it is created with defaults.
     *
     * @param int $id_resort
     * @return object
     */
    public function get_environment_DB($id_resort) {
        $row = $this->db
            ->select('*')
            ->from('game_resort_environment')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $this->db->insert('game_resort_environment', [
                'id_resort'            => (int)$id_resort,
                'eco_reputation'       => 50,
                'carbon_footprint'     => 0,
                'noise_pollution'      => 0,
                'wildlife_zone'        => 0,
                'solar_panels'         => 0,
                'electric_groomers'    => 0,
                'tree_count'           => 0,
                'water_recycling'      => 0,
                'expansion_restricted' => 0,
            ]);
            $row = $this->db
                ->select('*')
                ->from('game_resort_environment')
                ->where('id_resort', (int)$id_resort)
                ->get()
                ->row();
        }
        return $row;
    }

    /**
     * update_environment_DB    Saves updated environmental data for a resort.
     *
     * @param int   $id_resort
     * @param array $data  Associative array of columns to update
     * @return bool
     */
    public function update_environment_DB($id_resort, array $data) {
        $this->db->trans_start();
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resort_environment', $data);
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * set_wildlife_zone_DB     Activates or deactivates the wildlife protection zone.
     *
     * @param int $id_resort
     * @param int $value  0 or 1
     * @return bool
     */
    public function set_wildlife_zone_DB($id_resort, $value) {
        return $this->update_environment_DB($id_resort, ['wildlife_zone' => (int)(bool)$value]);
    }

    /**
     * set_solar_panels_DB      Sets the solar panels flag.
     *
     * @param int $id_resort
     * @param int $value  0 or 1
     * @return bool
     */
    public function set_solar_panels_DB($id_resort, $value) {
        return $this->update_environment_DB($id_resort, ['solar_panels' => (int)(bool)$value]);
    }

    /**
     * increment_electric_groomers_DB   Increments electric groomer count by 1.
     *
     * @param int $id_resort
     * @return bool
     */
    public function increment_electric_groomers_DB($id_resort) {
        $this->db->trans_start();
        $this->db->set('electric_groomers', 'electric_groomers + 1', FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resort_environment');
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * increment_tree_count_DB  Increments the reforestation tree count by 1.
     *
     * @param int $id_resort
     * @return bool
     */
    public function increment_tree_count_DB($id_resort) {
        $this->db->trans_start();
        $this->db->set('tree_count', 'tree_count + 1', FALSE);
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_resort_environment');
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * set_water_recycling_DB   Sets the water recycling system flag.
     *
     * @param int $id_resort
     * @param int $value  0 or 1
     * @return bool
     */
    public function set_water_recycling_DB($id_resort, $value) {
        return $this->update_environment_DB($id_resort, ['water_recycling' => (int)(bool)$value]);
    }

    /**
     * get_all_resorts_environment  Returns environment rows for all resorts.
     *                              Used by the nightly cron job.
     *
     * @return CI_DB_result
     */
    public function get_all_resorts_environment() {
        return $this->db
            ->select('*')
            ->from('game_resort_environment')
            ->get();
    }
}
