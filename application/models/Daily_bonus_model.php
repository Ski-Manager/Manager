<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Daily_bonus_model
 *
 * Handles the daily login streak and bonus reward logic.
 * Bonuses are stored directly on game_players (login_streak, last_bonus_date)
 * and rewards are applied to game_resorts (cash, reputation) via id_player.
 */
class Daily_bonus_model extends CI_Model {

    /**
     * get_streak_info  Returns current streak data for a player.
     *
     * @param  int $id_player
     * @return object|null  {login_streak, last_bonus_date}
     */
    public function get_streak_info($id_player) {
        $row = $this->db
            ->select('login_streak, last_bonus_date')
            ->from('game_players')
            ->where('id_player', (int)$id_player)
            ->get()
            ->row();
        return $row;
    }

    /**
     * calculate_bonus  Determines how much cash and reputation to award
     *                  for a given streak day (1-based, capped at DAILY_BONUS_STREAK_MAX).
     *
     * @param  int $streak  1-based streak day
     * @return array        ['cash' => int, 'rep' => int]
     */
    public function calculate_bonus($streak) {
        $effective = min((int)$streak, DAILY_BONUS_STREAK_MAX);
        $cash = DAILY_BONUS_CASH_BASE + DAILY_BONUS_CASH_PER_STREAK * ($effective - 1);
        $rep_tiers = DAILY_BONUS_REP_PER_TIER;
        $rep  = isset($rep_tiers[$effective]) ? $rep_tiers[$effective] : $rep_tiers[DAILY_BONUS_STREAK_MAX];
        return ['cash' => (int)$cash, 'rep' => (int)$rep];
    }

    /**
     * check_and_claim  Checks whether the player can claim a daily bonus and claims it.
     *
     * Returns an array:
     *   'already_claimed' => true  – bonus already collected today
     *   'claimed'         => true  – bonus just awarded
     *   'streak'          => int   – new/current streak count
     *   'cash'            => int   – cash awarded (0 if already_claimed)
     *   'rep'             => int   – reputation awarded (0 if already_claimed)
     *
     * @param  int $id_player
     * @return array
     */
    public function check_and_claim($id_player) {
        $id_player  = (int)$id_player;
        $today      = gmdate('Y-m-d');
        $yesterday  = gmdate('Y-m-d', strtotime('-1 day'));

        $info = $this->get_streak_info($id_player);
        if (!$info) {
            return ['already_claimed' => false, 'claimed' => false, 'streak' => 0, 'cash' => 0, 'rep' => 0];
        }

        // Already claimed today?
        if ($info->last_bonus_date === $today) {
            return [
                'already_claimed' => true,
                'claimed'         => false,
                'streak'          => (int)$info->login_streak,
                'cash'            => 0,
                'rep'             => 0,
            ];
        }

        // Determine new streak
        if ($info->last_bonus_date === $yesterday) {
            $new_streak = (int)$info->login_streak + 1;
        } else {
            $new_streak = 1; // streak broken or first time
        }

        $bonus = $this->calculate_bonus($new_streak);

        $this->db->trans_start();

        // Update streak info on players table
        $this->db->set('login_streak',    $new_streak);
        $this->db->set('last_bonus_date', $today);
        $this->db->where('id_player', $id_player);
        $this->db->limit(1);
        $this->db->update('game_players');

        // Credit cash to resort
        if ($bonus['cash'] > 0) {
            $this->db->set('cash', 'cash+' . $bonus['cash'], FALSE);
            $this->db->where('id_player', $id_player);
            $this->db->limit(1);
            $this->db->update('game_resorts');
        }

        // Credit reputation to resort
        if ($bonus['rep'] > 0) {
            $this->db->set('reputation', 'reputation+' . $bonus['rep'], FALSE);
            $this->db->where('id_player', $id_player);
            $this->db->limit(1);
            $this->db->update('game_resorts');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['already_claimed' => false, 'claimed' => false, 'streak' => $new_streak, 'cash' => 0, 'rep' => 0];
        }

        return [
            'already_claimed' => false,
            'claimed'         => true,
            'streak'          => $new_streak,
            'cash'            => $bonus['cash'],
            'rep'             => $bonus['rep'],
        ];
    }

    /**
     * get_bonus_tiers  Returns the full bonus tier table for display purposes.
     *
     * @return array  Array of ['day' => int, 'cash' => int, 'rep' => int]
     */
    public function get_bonus_tiers() {
        $tiers = [];
        for ($day = 1; $day <= DAILY_BONUS_STREAK_MAX; $day++) {
            $bonus   = $this->calculate_bonus($day);
            $tiers[] = ['day' => $day, 'cash' => $bonus['cash'], 'rep' => $bonus['rep']];
        }
        return $tiers;
    }
}
