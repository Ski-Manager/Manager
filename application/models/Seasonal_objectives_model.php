<?php

class Seasonal_objectives_model extends CI_Model {

    /**
     * get_all_objectives   Returns all catalogue objectives.
     */
    public function get_all_objectives() {
        return $this->db
            ->select('*')
            ->from('game_seasonal_objectives')
            ->order_by('id_objective', 'asc')
            ->get();
    }

    /**
     * get_objective_by_key     Returns a single objective row by its key.
     *
     * @param string $key   e.g. 'hit_50k_visitors'
     */
    public function get_objective_by_key($key) {
        return $this->db
            ->select('*')
            ->from('game_seasonal_objectives')
            ->where('objective_key', $key)
            ->limit(1)
            ->get()
            ->row();
    }

    /**
     * get_player_progress   Returns the player's progress rows for a given season.
     *
     * @param int $id_resort
     * @param int $season
     */
    public function get_player_progress($id_resort, $season) {
        return $this->db
            ->select('pso.*, obj.objective_key, obj.name_english, obj.name_french,
                      obj.description_english, obj.description_french,
                      obj.target_value, obj.reward_prestige, obj.reward_cash, obj.reward_genepis')
            ->from('game_player_seasonal_objectives pso')
            ->join('game_seasonal_objectives obj', 'obj.id_objective = pso.id_objective', 'inner')
            ->where('pso.id_resort', $id_resort)
            ->where('pso.season', $season)
            ->order_by('pso.id_objective', 'asc')
            ->get();
    }

    /**
     * get_player_progress_for_objective   Single row for one objective in a season.
     *
     * @param int    $id_resort
     * @param int    $season
     * @param string $objective_key
     */
    public function get_player_progress_for_objective($id_resort, $season, $objective_key) {
        return $this->db
            ->select('pso.*')
            ->from('game_player_seasonal_objectives pso')
            ->join('game_seasonal_objectives obj', 'obj.id_objective = pso.id_objective', 'inner')
            ->where('pso.id_resort', $id_resort)
            ->where('pso.season', $season)
            ->where('obj.objective_key', $objective_key)
            ->limit(1)
            ->get()
            ->row();
    }

    /**
     * init_season_objectives   Creates progress rows for all catalogue objectives
     *                          for the given resort + season (only if not yet present).
     *
     * @param int $id_resort
     * @param int $season
     */
    public function init_season_objectives($id_resort, $season) {
        $objectives = $this->get_all_objectives();
        foreach ($objectives->result() as $obj) {
            $exists = $this->db
                ->where('id_resort', $id_resort)
                ->where('id_objective', $obj->id_objective)
                ->where('season', $season)
                ->count_all_results('game_player_seasonal_objectives');
            if ($exists === 0) {
                $this->db->insert('game_player_seasonal_objectives', [
                    'id_resort'     => $id_resort,
                    'id_objective'  => $obj->id_objective,
                    'season'        => $season,
                    'current_value' => 0,
                    'failed'        => 0,
                    'completed'     => 0,
                    'rewarded'      => 0,
                ]);
            }
        }
    }

    /**
     * increment_visitor_count   Adds daily visitors to the hit_50k_visitors objective.
     *
     * @param int $id_resort
     * @param int $season
     * @param int $daily_visitors
     */
    public function increment_visitor_count($id_resort, $season, $daily_visitors) {
        $obj = $this->get_objective_by_key('hit_50k_visitors');
        if (!$obj) {
            return;
        }
        $this->db->trans_start();
        $this->db
            ->set('current_value', 'current_value + ' . (int) $daily_visitors, FALSE)
            ->where('id_resort', $id_resort)
            ->where('id_objective', $obj->id_objective)
            ->where('season', $season)
            ->where('rewarded', 0)
            ->update('game_player_seasonal_objectives');
        $this->db->trans_complete();
    }

    /**
     * check_reputation   Updates the maintain_reputation objective:
     *                    stores current reputation and marks as failed if below target.
     *
     * @param int $id_resort
     * @param int $season
     * @param int $current_reputation
     * @param int $target  (from objective catalogue)
     */
    public function check_reputation($id_resort, $season, $current_reputation, $target) {
        $obj = $this->get_objective_by_key('maintain_reputation');
        if (!$obj) {
            return;
        }
        $this->db->trans_start();
        $update = [
            'current_value' => (int) $current_reputation,
        ];
        if ((int) $current_reputation < (int) $target) {
            $update['failed'] = 1;
        }
        $this->db
            ->set($update)
            ->where('id_resort', $id_resort)
            ->where('id_objective', $obj->id_objective)
            ->where('season', $season)
            ->where('rewarded', 0)
            ->update('game_player_seasonal_objectives');
        $this->db->trans_complete();
    }

    /**
     * record_lift_breakdown   Increments breakdown counter and marks the
     *                         no_lift_breakdowns objective as failed.
     *
     * @param int $id_resort
     * @param int $season
     */
    public function record_lift_breakdown($id_resort, $season) {
        $obj = $this->get_objective_by_key('no_lift_breakdowns');
        if (!$obj) {
            return;
        }
        $this->db->trans_start();
        $this->db
            ->set('current_value', 'current_value + 1', FALSE)
            ->set('failed', 1)
            ->where('id_resort', $id_resort)
            ->where('id_objective', $obj->id_objective)
            ->where('season', $season)
            ->where('rewarded', 0)
            ->update('game_player_seasonal_objectives');
        $this->db->trans_complete();
    }

    /**
     * count_completed_tournaments   Counts completed tournaments started on or after
     *                               a given date for a resort.
     *
     * @param int    $id_resort
     * @param string $season_start_date   e.g. '2025-11-01 00:00:00'
     * @return int
     */
    public function count_completed_tournaments($id_resort, $season_start_date) {
        return $this->db
            ->where('id_resort', $id_resort)
            ->where('completed', 1)
            ->where('started_datetime >=', $season_start_date)
            ->count_all_results('game_started_tournaments');
    }

    /**
     * evaluate_and_award   Called at end of season. Marks objectives completed/failed
     *                      and credits rewards for objectives that were completed.
     *
     * @param int    $id_resort
     * @param int    $id_player
     * @param int    $season          The season that just ended
     * @param string $season_start    Start date of the season (Y-m-d H:i:s)
     * @return array                  Summary: ['prestige'=>x, 'cash'=>y, 'genepis'=>z, 'completed'=>[...]]
     */
    public function evaluate_and_award($id_resort, $id_player, $season, $season_start) {
        $total_prestige = 0;
        $total_cash     = 0;
        $total_genepis  = 0;
        $completed_names = [];

        $rows = $this->get_player_progress($id_resort, $season);
        if ($rows->num_rows() === 0) {
            return compact('total_prestige', 'total_cash', 'total_genepis', 'completed_names');
        }

        foreach ($rows->result() as $row) {
            if ($row->rewarded) {
                continue;
            }

            $completed = false;

            switch ($row->objective_key) {
                case 'hit_50k_visitors':
                    $completed = ($row->current_value >= $row->target_value);
                    break;

                case 'maintain_reputation':
                    $completed = ($row->failed == 0);
                    break;

                case 'host_2_events':
                    $event_count = $this->count_completed_tournaments($id_resort, $season_start);
                    $this->db
                        ->set('current_value', (int) $event_count)
                        ->where('id_player_seasonal_obj', $row->id_player_seasonal_obj)
                        ->update('game_player_seasonal_objectives');
                    $completed = ($event_count >= $row->target_value);
                    break;

                case 'no_lift_breakdowns':
                    $completed = ($row->failed == 0);
                    break;
            }

            $update = [
                'completed' => (int) $completed,
                'rewarded'  => 1,
            ];
            $this->db
                ->set($update)
                ->where('id_player_seasonal_obj', $row->id_player_seasonal_obj)
                ->update('game_player_seasonal_objectives');

            if ($completed) {
                $total_prestige += (int) $row->reward_prestige;
                $total_cash     += (int) $row->reward_cash;
                $total_genepis  += (int) $row->reward_genepis;
                $completed_names[] = $row->name_english;
            }
        }

        if ($total_prestige > 0 || $total_cash > 0) {
            $this->db->trans_start();
            $this->db
                ->set('prestige', 'prestige + ' . $total_prestige, FALSE)
                ->set('cash', 'cash + ' . $total_cash, FALSE)
                ->where('id_resort', $id_resort)
                ->update('game_resorts');
            $this->db->trans_complete();
        }

        if ($total_genepis > 0) {
            $this->db->trans_start();
            $this->db
                ->set('genepis', 'genepis + ' . $total_genepis, FALSE)
                ->where('id_player', $id_player)
                ->update('game_players');
            $this->db->trans_complete();
        }

        return compact('total_prestige', 'total_cash', 'total_genepis', 'completed_names');
    }
}
