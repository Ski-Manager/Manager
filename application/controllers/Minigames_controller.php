<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minigames controller
 *
 * Three daily mini-games for players to earn small cash/reputation bonuses:
 *   1. Lucky Slalom   – slot-machine luck game
 *   2. Snow Quiz      – ski trivia quiz
 *   3. Snowball Rush  – reflex / reaction game
 *   4. Grooming Rush  – slope grooming tile game
 *   5. Snowmaking Challenge – precision timing game
 *   6. Avalanche Escape – dodge game
 *   7. Lift Line Manager – memory / sequence game
 *   8. Ice Breaker – rapid-click game
 */
class Minigames_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $ci =& get_instance();

        $siteLang = $ci->session->userdata('site_lang') ?: 'english';
        if (!$ci->session->userdata('site_lang')) {
            $this->session->set_userdata('site_lang', $siteLang);
        }

        $ci->lang->load('home',      $siteLang);
        $ci->lang->load('login_form', $siteLang);
        $ci->lang->load('navbar',    $siteLang);
        $ci->lang->load('minigames', $siteLang);
        $ci->lang->load('logs',      $siteLang);

        $logged_status = $this->session->userdata('is_logged_in');
        if (!isset($logged_status) || $logged_status != true) {
            redirect('home_controller');
        }

        $this->load->model('users_model');
        $this->load->model('resort_model');
        $this->load->model('logs_model');
        $this->load->model('minigames_model');
        $this->minigames_model->ensure_tables_exist();
    }

    public function index() {
        $currentUserID  = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);
        $user_activated  = $this->users_model->check_account_activated($currentUserID);

        if (!$user_activated) {
            $this->session->set_userdata('not_activated', true);
            redirect('register_controller');
        }

        $lang = $this->lang->line('minigames');

        $data['title'] = '<h2>' . $lang['titleMain'] . '</h2>';
        $data['intro'] = '<div>' . $lang['intro'] . '</div>';

        // Load all active minigames from DB
        $minigames_query = $this->minigames_model->get_all_minigames();
        $minigames       = $minigames_query->result();

        $name_col = 'name_' . $this->session->userdata('site_lang');
        $desc_col = 'description_' . $this->session->userdata('site_lang');

        // Build per-game cooldown status for the view
        $game_status = [];
        foreach ($minigames as $mg) {
            $elapsed  = $this->minigames_model->is_cooldown_elapsed($currentResortID, $mg->id_minigame, $mg->cooldown_hours);
            $last_play = $this->minigames_model->get_last_play($currentResortID, $mg->id_minigame);
            $last_row  = ($last_play->num_rows() > 0) ? $last_play->row() : null;

            $game_status[$mg->id_minigame] = [
                'can_play'    => $elapsed,
                'last_play'   => $last_row,
                'name'        => isset($mg->$name_col) ? $mg->$name_col : $mg->name_english,
                'description' => isset($mg->$desc_col) ? $mg->$desc_col : $mg->description_english,
            ];
        }

        $data['minigames']    = $minigames;
        $data['game_status']  = $game_status;
        $data['currentResortID'] = $currentResortID;

        // Stats block
        $stats = $this->minigames_model->get_stats($currentResortID);
        $data['minigame_stats'] = ($stats->num_rows() > 0) ? $stats->row() : null;

        // Best scores per minigame
        $data['best_scores'] = $this->minigames_model->get_best_scores($currentResortID);

        // Per-game play/win stats
        $data['per_game_stats'] = $this->minigames_model->get_per_game_stats($currentResortID);

        // Play history (25 most recent, optional game filter from query string)
        $filter_game = (int)$this->input->get('game', TRUE);
        $data['filter_game']  = $filter_game > 0 ? $filter_game : 0;
        $data['play_history'] = $this->minigames_model->get_play_history(
            $currentResortID,
            25,
            $filter_game > 0 ? $filter_game : null
        );

        // Win streak
        $data['win_streak'] = $this->minigames_model->get_streak($currentResortID);

        $data['main_content'] = 'minigames';
        $this->load->view('templates/default', $data);
    }

    /**
     * AJAX endpoint: submit a minigame result.
     *
     * POST parameters:
     *   id_minigame  int
     *   score        int   (0–100, validated client-side, re-validated server-side)
     *   result       string  'win' | 'lose'  (always recalculated server-side)
     */
    public function submit() {
        // Discard any output produced by hooks / model loading so the
        // response is guaranteed to be clean JSON.
        $limit = 10;
        while (ob_get_level() > 0 && $limit-- > 0) {
            ob_end_clean();
        }

        $currentUserID   = $this->users_model->get_user_id();
        $currentResortID = $this->users_model->get_resort_id($currentUserID);

        $id_minigame = (int)$this->input->post('id_minigame', TRUE);
        $score       = max(0, min(100, (int)$this->input->post('score', TRUE)));

        // Validate minigame exists
        $mg_query = $this->minigames_model->get_minigame($id_minigame);
        if ($mg_query->num_rows() === 0) {
            $this->_json_response(['success' => false, 'message' => 'invalid_game']);
            return;
        }
        $mg = $mg_query->row();

        // Cooldown check
        if (!$this->minigames_model->is_cooldown_elapsed($currentResortID, $id_minigame, $mg->cooldown_hours)) {
            $this->_json_response(['success' => false, 'message' => 'on_cooldown']);
            return;
        }

        // Server-side reward calculation (client score is a hint only)
        list($result, $reward_cash, $reward_rep) = $this->calculate_reward($mg, $score);

        // Apply win-streak bonus (10% extra when current streak >= 3)
        $streak_bonus = false;
        if ($result === 'win') {
            $effective_streak = $this->minigames_model->compute_next_streak($currentResortID, gmdate('Y-m-d'));
            if ($effective_streak >= 3) {
                $streak_bonus = true;
                $reward_cash = (int)round($reward_cash * 1.1);
                $reward_rep  = (int)round($reward_rep  * 1.1);
            }
        }

        $now = gmdate('Y-m-d H:i:s');

        $play_data = [
            'id_resort'         => $currentResortID,
            'id_minigame'       => $id_minigame,
            'play_datetime'     => $now,
            'result'            => $result,
            'score'             => $score,
            'reward_cash'       => $reward_cash,
            'reward_reputation' => $reward_rep,
        ];

        if (!$this->minigames_model->record_play($play_data)) {
            $this->_json_response(['success' => false, 'message' => 'db_error']);
            return;
        }

        // Apply rewards
        if ($reward_cash > 0) {
            $this->minigames_model->add_reward($currentUserID, 'cash', $reward_cash);
            $cash = $this->users_model->get_cash_player();
            $this->session->set_userdata('cash', $cash);
        }
        if ($reward_rep > 0) {
            $this->minigames_model->add_reward($currentUserID, 'reputation', $reward_rep);
        }

        // Log the action
        if ($result === 'win') {
            $lang = $this->lang->line('minigames');
            $player_lang = $this->users_model->get_user_preferred_lang($currentUserID);
            $this->lang->load('logs', $player_lang);
            $name_col  = 'name_' . $player_lang;
            $game_name = isset($mg->$name_col) ? $mg->$name_col : $mg->name_english;
            $log_text  = $game_name . ' – ' . $lang['log_won'];
            if ($reward_cash > 0) {
                $log_text .= ' +' . number_format($reward_cash, 0, ',', ' ') . '€';
            }
            if ($reward_rep > 0) {
                $home_lang = $this->lang->line('home');
                $rep_label = is_array($home_lang) ? ($home_lang['mini_reputation'] ?? 'reputation') : 'reputation';
                $log_text .= ' +' . $reward_rep . ' ' . $rep_label;
            }
            $logs_lang = $this->lang->line('logs');
            $log_type  = is_array($logs_lang) ? ($logs_lang['minigames'] ?? 'Minigames') : 'Minigames';
            $this->logs_model->call_notification_DB([
                'id_player' => $currentUserID,
                'type'      => $log_type,
                'data'      => $log_text,
            ]);
            log_user_action([
                'id_player' => $currentUserID,
                'type'      => $log_type,
                'data'      => $log_text,
            ]);

            // Update win streak
            $this->minigames_model->update_streak($currentResortID, gmdate('Y-m-d'));
        }

        // Fetch updated streak to return to client
        $updated_streak = $this->minigames_model->get_streak($currentResortID);
        $new_streak = ($updated_streak !== null) ? (int)$updated_streak->current_streak : 0;

        $this->_json_response([
            'success'       => true,
            'result'        => $result,
            'reward_cash'   => $reward_cash,
            'reward_rep'    => $reward_rep,
            'score'         => $score,
            'streak'        => $new_streak,
            'streak_bonus'  => $streak_bonus,
        ]);
    }

    /**
     * Send a JSON response, discarding any prior output (PHP notices, etc.).
     */
    private function _json_response($data) {
        // Discard ALL stray output buffers (PHP warnings, notices, etc.)
        $limit = 10;
        while (ob_get_level() > 0 && $limit-- > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Calculate the server-side reward for a minigame play.
     *
     * @param  object $mg    Minigame row from DB
     * @param  int    $score 0–100
     * @return array  [$result, $reward_cash, $reward_rep]
     */
    private function calculate_reward($mg, $score) {
        $type = $mg->minigame_type;

        if ($type === 'luck') {
            // Slot machine: 1-in-3 chance of winning, reward is random fraction of max
            $win = (mt_rand(1, 3) === 1);
            if (!$win) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * mt_rand(30, 100)) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * mt_rand(30, 100)) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'quiz') {
            // Quiz: win if score >= 60 (3/5 correct), reward scales with score
            if ($score < 60) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'skill') {
            // Skill/reflex: win if score >= 40, reward scales with score
            if ($score < 40) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'snowmaking') {
            // Snowmaking precision game: win if score >= 40, reward scales with score
            if ($score < 40) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'grooming') {
            // Grooming: win if score >= 50 (half the tiles groomed), reward scales with score
            if ($score < 50) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'avalanche') {
            // Avalanche Escape: win if score >= 40, reward scales with score
            if ($score < 40) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'liftline') {
            // Lift Line Manager: win if score >= 50, reward scales with score
            if ($score < 50) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'icebreaker') {
            // Ice Breaker: win if score >= 40, reward scales with score
            if ($score < 40) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'slalom') {
            // Slalom Race: win if score >= 60 (5/8 gates correct), reward scales with score
            if ($score < 60) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'patrol') {
            // Ski Patrol Rush: win if score >= 40 (5/12 skiers rescued), reward scales with score
            if ($score < 40) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'freestyle') {
            // Freestyle Jump: win if score >= 50 (landed in acceptable zone), reward scales with score
            if ($score < 50) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'biathlon') {
            // Biathlon: win if score >= 40 (2/5 shots hit), reward scales with score
            if ($score < 40) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        if ($type === 'snowboard') {
            // Snowboard Trick: win if score >= 50 (at least 2/3 tricks landed), reward scales with score
            if ($score < 50) {
                return ['lose', 0, 0];
            }
            $cash = (int)round(($mg->max_reward_cash * $score) / 100);
            $rep  = (int)round(($mg->max_reward_reputation * $score) / 100);
            return ['win', $cash, $rep];
        }

        // Fallback
        return ['lose', 0, 0];
    }
}
