<?php
/**
 * Seasonal_objectives_controller
 *
 * Displays per-season objective progress for the logged-in player and shows
 * end-of-season reward history.
 */
class Seasonal_objectives_controller extends CI_Controller {

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

        $ci->lang->load('home',                $this->siteLang);
        $ci->lang->load('login_form',          $this->siteLang);
        $ci->lang->load('navbar',              $this->siteLang);
        $ci->lang->load('seasonal_objectives', $this->siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('seasonal_objectives_model');
    }

    /**
     * index    Renders the seasonal-objectives page.
     */
    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $user_activated = $this->users_model->check_account_activated($currentUserID);
        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $data['title'] = '<h2>' . $this->lang->line('seasonal_objectives')['title'] . '</h2>';
        $data['intro']  = '<div>' . $this->lang->line('seasonal_objectives')['intro'] . '</div>';

        $current_season  = get_current_season($currentResortID) ?? 1;
        $day_of_season   = get_day_of_season($currentResortID);

        $data['current_season'] = (int) $current_season;
        $data['day_of_season']  = $day_of_season;
        $data['lang']           = $this->lang->line('seasonal_objectives');

        $name_col = 'name_' . $this->siteLang;
        $desc_col = 'description_' . $this->siteLang;

        // Build progress blocks for the current season
        $progress_rows = $this->seasonal_objectives_model->get_player_progress($currentResortID, $current_season);
        $data['objectives_html'] = $this->_build_objectives_html($progress_rows, $name_col, $desc_col);

        $data['main_content'] = 'seasonal_objectives';
        $this->load->view('templates/default', $data);
    }

    /**
     * _build_objectives_html   Generates the HTML table for objectives.
     *
     * @param object $rows       CI DB result object
     * @param string $name_col   Language column for the name
     * @param string $desc_col   Language column for the description
     * @return string
     */
    private function _build_objectives_html($rows, $name_col, $desc_col) {
        $L = $this->lang->line('seasonal_objectives');

        if ($rows->num_rows() === 0) {
            return '<p class="text-muted">' . htmlspecialchars($L['no_data'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $html  = '<div class="table-responsive">';
        $html .= '<table class="table table-hover">';
        $html .= '<thead><tr>';
        $html .= '<th>' . htmlspecialchars($L['objective'],    ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($L['description'],  ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($L['progress'],     ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($L['rewards'],      ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($L['status'],       ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '</tr></thead><tbody>';

        $count = 0;
        foreach ($rows->result() as $row) {
            $tr_class = ($count % 2 === 0) ? '' : 'table-secondary';
            $count++;

            $name        = htmlspecialchars($row->$name_col,  ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($row->$desc_col,  ENT_QUOTES, 'UTF-8');
            $target      = (int) $row->target_value;

            // Progress bar calculation
            $current = (int) $row->current_value;
            if ($target > 0) {
                $pct = min(100, round($current / $target * 100));
            } else {
                // target == 0 (no_lift_breakdowns): completed means 0 breakdowns
                $pct = ($row->failed == 0) ? 100 : 0;
            }

            $bar_class = ($pct >= 100) ? 'bg-success' : 'bg-info';
            if ($row->failed) {
                $bar_class = 'bg-danger';
            }

            if ($row->objective_key === 'no_lift_breakdowns') {
                $progress_label = $current . ' ' . ($current === 1 ? 'breakdown' : 'breakdowns');
            } else {
                $progress_label = number_format($current, 0, ',', ' ')
                    . ' ' . $L['out_of'] . ' '
                    . number_format($target, 0, ',', ' ');
            }

            $progress_html  = '<div class="progress" style="min-width:120px">';
            $progress_html .= '<div class="progress-bar ' . $bar_class . '" role="progressbar"'
                . ' style="width:' . $pct . '%"'
                . ' aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100">'
                . $pct . '%'
                . '</div></div>';
            $progress_html .= '<small class="text-muted">' . $progress_label . '</small>';

            // Rewards cell
            $rewards = [];
            if ((int) $row->reward_prestige > 0) {
                $rewards[] = '+' . number_format((int) $row->reward_prestige, 0, ',', ' ') . ' ' . $L['prestige'];
            }
            if ((int) $row->reward_cash > 0) {
                $rewards[] = '+' . number_format((int) $row->reward_cash, 0, ',', ' ') . ' ' . $L['cash'];
            }
            if ((int) $row->reward_genepis > 0) {
                $rewards[] = '+' . number_format((int) $row->reward_genepis, 0, ',', ' ') . ' ' . $L['genepis'];
            }
            $rewards_html = implode('<br>', $rewards);

            // Status badge
            if ($row->rewarded) {
                $badge = $row->completed
                    ? '<span class="badge bg-success">' . htmlspecialchars($L['completed'], ENT_QUOTES, 'UTF-8') . '</span>'
                    : '<span class="badge bg-danger">'  . htmlspecialchars($L['failed'],    ENT_QUOTES, 'UTF-8') . '</span>';
            } elseif ($row->failed) {
                $badge = '<span class="badge bg-danger">' . htmlspecialchars($L['failed'], ENT_QUOTES, 'UTF-8') . '</span>';
            } else {
                $badge = '<span class="badge bg-primary">' . htmlspecialchars($L['in_progress'], ENT_QUOTES, 'UTF-8') . '</span>';
            }

            $html .= '<tr class="' . $tr_class . '">';
            $html .= '<td class="align-middle"><strong>' . $name . '</strong></td>';
            $html .= '<td class="align-middle small">' . $description . '</td>';
            $html .= '<td class="align-middle">' . $progress_html . '</td>';
            $html .= '<td class="align-middle small">' . $rewards_html . '</td>';
            $html .= '<td class="align-middle text-center">' . $badge . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }
}
