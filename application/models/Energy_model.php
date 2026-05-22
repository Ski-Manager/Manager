<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Energy_model
 *
 * Manages the Power & Energy system for each resort.
 *
 * Database table: game_resort_energy
 *   id_resort        INT  (FK → game_resorts.id_resort, UNIQUE)
 *   solar_panels     INT  NOT NULL DEFAULT 0  (number of solar panel units)
 *   hydro_plant      TINYINT(1) NOT NULL DEFAULT 0  (0 = not built, 1 = built)
 *
 * The table is created automatically on first use (ensure_table_exists).
 */
class Energy_model extends CI_Model {

    /**
     * ensure_table_exists  Creates game_resort_energy if it does not yet exist.
     *                      Called from the controller constructor so the table is
     *                      always present before any model method runs.
     */
    public function ensure_table_exists() {
        if (!$this->db->table_exists('game_resort_energy')) {
            $this->db->query('
                CREATE TABLE game_resort_energy (
                    id_resort    INT          NOT NULL,
                    solar_panels INT          NOT NULL DEFAULT 0,
                    hydro_plant  TINYINT(1)   NOT NULL DEFAULT 0,
                    PRIMARY KEY (id_resort)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ');
        }
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * get_energy_settings_DB   Returns the energy row for a resort, or a default
     *                          object if no row exists yet.
     *
     * @param int $id_resort
     * @return object  { solar_panels, hydro_plant }
     */
    public function get_energy_settings_DB($id_resort) {
        $row = $this->db
            ->select('solar_panels, hydro_plant')
            ->from('game_resort_energy')
            ->where('id_resort', (int)$id_resort)
            ->get()
            ->row();

        if (!$row) {
            $row = (object)['solar_panels' => 0, 'hydro_plant' => 0];
        }
        return $row;
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * set_solar_panels_DB  Sets the number of solar panel units for a resort.
     *                      Inserts the row if it does not exist yet (upsert).
     *
     * @param int $id_resort
     * @param int $count      0 … ENERGY_SOLAR_PANEL_MAX
     * @return bool
     */
    public function set_solar_panels_DB($id_resort, $count) {
        return $this->_upsert($id_resort, ['solar_panels' => (int)$count]);
    }

    /**
     * set_hydro_plant_DB   Sets the hydro plant status for a resort.
     *
     * @param int $id_resort
     * @param int $status     0 = demolished, 1 = built
     * @return bool
     */
    public function set_hydro_plant_DB($id_resort, $status) {
        return $this->_upsert($id_resort, ['hydro_plant' => (int)$status]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * _upsert  Insert or update a row in game_resort_energy.
     *
     * @param int   $id_resort
     * @param array $fields      Associative array of column => value to set
     * @return bool
     */
    private function _upsert($id_resort, array $fields) {
        $exists = $this->db
            ->where('id_resort', (int)$id_resort)
            ->count_all_results('game_resort_energy');

        $this->db->trans_start();
        if ($exists > 0) {
            $this->db->where('id_resort', (int)$id_resort);
            $this->db->update('game_resort_energy', $fields);
        } else {
            $fields['id_resort'] = (int)$id_resort;
            $this->db->insert('game_resort_energy', $fields);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}
