<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lift_network_controller
 *
 * Computes the Lift Network Efficiency metrics for the player's resort:
 *  - Transfer Efficiency  : share of total throughput that is currently operational.
 *  - Bottleneck Score     : how balanced throughput is across sectors (100 = perfectly balanced).
 *  - Network Redundancy   : share of built lifts that serve at least one slope.
 *  - Overlap Waste        : share of slope-lift assignments that cover the same slope as another lift.
 */
class Lift_network_controller extends CI_Controller {

    private $siteLang;

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        if ($ci->session->userdata('site_lang')) {
            $this->siteLang = $ci->session->userdata('site_lang');
        } else {
            $this->siteLang = 'english';
            $this->session->set_userdata('site_lang', $this->siteLang);
        }

        $ci->lang->load('home',       $this->siteLang);
        $ci->lang->load('navbar',     $this->siteLang);
        $ci->lang->load('lift',       $this->siteLang);
        $ci->lang->load('login_form', $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('item_model');
        $this->load->model('resort_model');
    }

    // -----------------------------------------------------------------------

    public function index() {
        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $resultResort = $this->resort_model->display_resort_info_DB($currentResortID);
        if ($resultResort->num_rows() == 0) {
            redirect('resort_controller');
        }

        $lifts = $this->item_model->get_lift_network_data($currentResortID)->result();

        $metrics = $this->_calculate_metrics($lifts);

        $data['metrics']         = $metrics;
        $data['currentResortID'] = $currentResortID;
        $data['main_content']    = 'lift_network';
        $this->load->view('templates/default', $data);
    }

    // -----------------------------------------------------------------------

    /**
     * _calculate_metrics   Derives the four network KPIs from raw lift rows.
     *
     * @param  array $lifts  Array of stdClass rows returned by get_lift_network_data
     * @return array         Associative array with keys:
     *                         transfer_efficiency, bottleneck_score,
     *                         network_redundancy, overlap_waste,
     *                         total_lifts, open_lifts
     */
    private function _calculate_metrics(array $lifts): array {
        $total_lifts        = count($lifts);

        if ($total_lifts === 0) {
            return [
                'transfer_efficiency' => 0,
                'bottleneck_score'    => 0,
                'network_redundancy'  => 0,
                'overlap_waste'       => 0,
                'network_score'       => 0,
                'total_lifts'         => 0,
                'open_lifts'          => 0,
                'capacity_suggestions' => [],
            ];
        }

        $total_throughput       = 0;
        $operational_throughput = 0;
        $open_lifts             = 0;
        $sector_throughput      = [];   // [id_sector => sum of open throughput] — all built sectors initialised to 0
        $lifts_with_slopes      = 0;
        $all_slope_assignments  = [];   // flat list of slope IDs assigned across all lifts
        $slope_assignment_count = 0;
        $capacity_suggestions   = [];   // per-lift upgrade / replace recommendations

        foreach ($lifts as $lift) {
            $throughput = (int) $lift->throughput;
            $status     = (string) $lift->id_status;
            $sector     = (string) $lift->id_sector;

            $total_throughput += $throughput;

            // Initialise every sector so that sectors with no open lifts score 0 in the bottleneck calculation.
            if (!array_key_exists($sector, $sector_throughput)) {
                $sector_throughput[$sector] = 0;
            }

            if ($status === '1') {   // open
                $operational_throughput += $throughput;
                $open_lifts++;
                $sector_throughput[$sector] += $throughput;

                // Capacity recommendation: compare current throughput with maximum achievable
                $max_throughput = (int) ($lift->max_throughput ?? $throughput);
                $can_upgrade    = $throughput < $max_throughput;   // i.e. lift is below LIFT_MAX_LEVEL
                $capacity_suggestions[] = [
                    'name'              => $lift->custom_name,
                    'throughput'        => $throughput,
                    'max_throughput'    => $max_throughput,
                    'level'             => (int) ($lift->level ?? 1),
                    'id_group_location' => (int) $lift->id_group_location,
                    'id_created_lifts'  => (int) $lift->id_created_lifts,
                    'id_group'          => (int) $lift->id_group_lift,
                    'can_upgrade'       => $can_upgrade,
                ];
            }

            // Collect slope assignments via location-area matching (same logic as get_deserved_slopes).
            // served_slopes is NULL (not empty string) when GROUP_CONCAT finds no rows, so !empty() is safe.
            $assigned_slopes = [];
            if (!empty($lift->served_slopes)) {
                $assigned_slopes = array_filter(
                    array_unique(array_map('intval', explode(',', (string) $lift->served_slopes)))
                );  // array_filter drops any 0 / false values that should never appear
            }
            if (!empty($assigned_slopes)) {
                $lifts_with_slopes++;
                $all_slope_assignments = array_merge($all_slope_assignments, $assigned_slopes);
                $slope_assignment_count += count($assigned_slopes);
            }
        }

        // 1. Transfer Efficiency
        $transfer_efficiency = ($total_throughput > 0)
            ? round(($operational_throughput / $total_throughput) * 100)
            : 0;

        // 2. Bottleneck Score – balance across sectors
        if (count($sector_throughput) <= 1) {
            // 0 or 1 sector: perfect balance by default (or no open lifts → 0)
            $bottleneck_score = (count($sector_throughput) === 1) ? 100 : 0;
        } else {
            $min_tp = min($sector_throughput);
            $max_tp = max($sector_throughput);
            $bottleneck_score = ($max_tp > 0) ? round(($min_tp / $max_tp) * 100) : 0;
        }

        // 3. Network Redundancy – share of built lifts that serve ≥1 slope
        $network_redundancy = round(($lifts_with_slopes / $total_lifts) * 100);

        // 4. Overlap Waste – duplicate slope assignments
        if ($slope_assignment_count > 0) {
            $unique_slopes = count(array_unique($all_slope_assignments));
            $overlap_count = $slope_assignment_count - $unique_slopes;
            $overlap_waste = round(($overlap_count / $slope_assignment_count) * 100);
        } else {
            $overlap_waste = 0;
        }

        // 5. Composite Network Score (weighted average)
        // Transfer Efficiency 30 % + Bottleneck Score 30 % + Network Redundancy 20 % + Overlap Efficiency 20 %
        $network_score = (int) round(
            $transfer_efficiency * 0.30 +
            $bottleneck_score    * 0.30 +
            $network_redundancy  * 0.20 +
            (100 - $overlap_waste) * 0.20
        );

        // Sort capacity suggestions by potential throughput gain (highest gain first)
        usort($capacity_suggestions, static function (array $a, array $b): int {
            return ($b['max_throughput'] - $b['throughput']) - ($a['max_throughput'] - $a['throughput']);
        });

        return [
            'transfer_efficiency'  => $transfer_efficiency,
            'bottleneck_score'     => $bottleneck_score,
            'network_redundancy'   => $network_redundancy,
            'overlap_waste'        => $overlap_waste,
            'network_score'        => $network_score,
            'total_lifts'          => $total_lifts,
            'open_lifts'           => $open_lifts,
            'capacity_suggestions' => $capacity_suggestions,
        ];
    }
}
