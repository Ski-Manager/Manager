<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Minigames_model extends CI_Model {

    /**
     * Ensure the game_minigames and game_minigame_plays tables exist and are
     * seeded with the initial minigame catalogue.
     * Called from the controller constructor so tables are always present.
     */
    public function ensure_tables_exist() {
        if (!$this->db->table_exists('game_minigames')) {
            $this->db->query('
                CREATE TABLE game_minigames (
                    id_minigame          INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    name_english         VARCHAR(100) NOT NULL DEFAULT \'\',
                    name_french          VARCHAR(100) NOT NULL DEFAULT \'\',
                    description_english  TEXT,
                    description_french   TEXT,
                    play_cost            INT NOT NULL DEFAULT 0,
                    max_reward_cash      INT NOT NULL DEFAULT 0,
                    max_reward_reputation INT NOT NULL DEFAULT 0,
                    cooldown_hours       INT NOT NULL DEFAULT 24,
                    minigame_type        VARCHAR(20) NOT NULL DEFAULT \'luck\',
                    active               TINYINT NOT NULL DEFAULT 1,
                    game_order           INT NOT NULL DEFAULT 0,
                    PRIMARY KEY (id_minigame)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ');
        }

        // Ensure all five minigames are seeded (idempotent per minigame_type)
        $this->_seed_minigame_if_missing('luck', [
            'name_english'          => 'Lucky Slalom',
            'name_french'           => 'Slalom Chanceux',
            'description_english'   => 'Spin the reels and try to line up three matching ski symbols! A fun luck-based game where matching icons reward you with bonus cash for your resort.',
            'description_french'    => 'Faites tourner les rouleaux et essayez d\'aligner trois symboles de ski identiques ! Un jeu de chance amusant où les icônes correspondantes vous récompensent avec du cash supplémentaire pour votre station.',
            'play_cost'             => 0,
            'max_reward_cash'       => 5000,
            'max_reward_reputation' => 0,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 1,
        ]);
        $this->_seed_minigame_if_missing('quiz', [
            'name_english'          => 'Snow Quiz',
            'name_french'           => 'Quiz des Neiges',
            'description_english'   => 'Test your knowledge of ski resorts and alpine culture with five multiple-choice questions. Answer correctly to earn reputation points for your resort!',
            'description_french'    => 'Testez vos connaissances sur les stations de ski et la culture alpine avec cinq questions à choix multiples. Répondez correctement pour gagner des points de réputation pour votre station !',
            'play_cost'             => 0,
            'max_reward_cash'       => 0,
            'max_reward_reputation' => 10,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 2,
        ]);
        $this->_seed_minigame_if_missing('skill', [
            'name_english'          => 'Snowball Rush',
            'name_french'           => 'Ruée aux Boules de Neige',
            'description_english'   => 'Catch as many snowballs as you can before the timer runs out! A fast-paced reflex game where a high score earns you bonus cash.',
            'description_french'    => 'Attrapez autant de boules de neige que possible avant la fin du temps imparti ! Un jeu de réflexes effréné où un score élevé vous rapporte du cash supplémentaire.',
            'play_cost'             => 0,
            'max_reward_cash'       => 3000,
            'max_reward_reputation' => 0,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 3,
        ]);
        $this->_seed_minigame_if_missing('grooming', [
            'name_english'          => 'Grooming Rush',
            'name_french'           => 'Ruée au Damage',
            'description_english'   => 'Race against the clock to groom as many slope sections as possible! Click the ungroomed tiles before time runs out and earn bonus cash and reputation for a well-prepared mountain.',
            'description_french'    => 'Battez la montre pour damer le plus de sections de piste possible ! Cliquez sur les cases non damées avant la fin du temps et gagnez du cash et de la réputation pour une montagne bien préparée.',
            'play_cost'             => 0,
            'max_reward_cash'       => 2000,
            'max_reward_reputation' => 5,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 4,
        ]);
        $this->_seed_minigame_if_missing('snowmaking', [
            'name_english'          => 'Snowmaking Challenge',
            'name_french'           => 'Défi d\'Enneigement',
            'description_english'   => 'Take control of the snow cannons! Fire at the perfect moment to build up snow coverage across your slopes. Precision timing earns you bonus cash and reputation.',
            'description_french'    => 'Prenez le contrôle des canons à neige ! Tirez au bon moment pour augmenter l\'enneigement de vos pistes. Une bonne précision vous rapporte du cash et de la réputation supplémentaires.',
            'play_cost'             => 0,
            'max_reward_cash'       => 4000,
            'max_reward_reputation' => 5,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 5,
        ]);
        $this->_seed_minigame_if_missing('avalanche', [
            'name_english'          => 'Avalanche Escape',
            'name_french'           => 'Fuite d\'Avalanche',
            'description_english'   => 'An avalanche is coming! Dodge falling snow boulders by moving left and right. The more you dodge, the higher your score. Quick reflexes earn you bonus cash and reputation!',
            'description_french'    => 'Une avalanche approche ! Esquivez les blocs de neige en vous déplaçant à gauche et à droite. Plus vous esquivez, plus votre score augmente. De bons réflexes vous rapportent du cash et de la réputation !',
            'play_cost'             => 0,
            'max_reward_cash'       => 3500,
            'max_reward_reputation' => 8,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 6,
        ]);
        $this->_seed_minigame_if_missing('liftline', [
            'name_english'          => 'Lift Line Manager',
            'name_french'           => 'Gestion de la File',
            'description_english'   => 'Memorise the sequence of coloured ski passes and repeat it! Each round adds one more colour. How long can you keep up? A sharp memory earns you reputation!',
            'description_french'    => 'Mémorisez la séquence de forfaits colorés et reproduisez-la ! Chaque manche ajoute une couleur. Jusqu\'où pouvez-vous aller ? Une bonne mémoire vous rapporte de la réputation !',
            'play_cost'             => 0,
            'max_reward_cash'       => 0,
            'max_reward_reputation' => 12,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 7,
        ]);
        $this->_seed_minigame_if_missing('icebreaker', [
            'name_english'          => 'Ice Breaker',
            'name_french'           => 'Brise-Glace',
            'description_english'   => 'Smash through layers of ice as fast as you can! Click rapidly to chip away at the ice before time runs out. The more you break, the more cash you earn!',
            'description_french'    => 'Brisez les couches de glace le plus vite possible ! Cliquez rapidement pour entamer la glace avant la fin du temps. Plus vous brisez, plus vous gagnez de cash !',
            'play_cost'             => 0,
            'max_reward_cash'       => 2500,
            'max_reward_reputation' => 0,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 8,
        ]);
        $this->_seed_minigame_if_missing('slalom', [
            'name_english'          => 'Slalom Race',
            'name_french'           => 'Course de Slalom',
            'description_english'   => 'Race through the gates! Each gate swings left or right – click the matching button before time runs out. Hit 5 out of 8 gates correctly to win bonus cash and reputation!',
            'description_french'    => 'Passez les portes en un temps record ! Chaque porte s\'ouvre à gauche ou à droite – cliquez le bon bouton avant la fin du temps. Passez 5 des 8 portes correctement pour gagner du cash et de la réputation !',
            'play_cost'             => 0,
            'max_reward_cash'       => 4000,
            'max_reward_reputation' => 6,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 9,
        ]);
        $this->_seed_minigame_if_missing('patrol', [
            'name_english'          => 'Ski Patrol Rush',
            'name_french'           => 'Patrouille de Ski',
            'description_english'   => 'Injured skiers need your help! Click on them before they disappear to earn rescue points. The faster you react, the more skiers you save. Rescue at least 5 out of 12 to win bonus cash and reputation!',
            'description_french'    => 'Des skieurs blessés ont besoin de votre aide ! Cliquez dessus avant qu\'ils ne disparaissent pour marquer des points. Plus vous réagissez vite, plus vous en sauvez. Secourez au moins 5 skieurs sur 12 pour gagner du cash et de la réputation !',
            'play_cost'             => 0,
            'max_reward_cash'       => 3000,
            'max_reward_reputation' => 8,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 10,
        ]);
        $this->_seed_minigame_if_missing('freestyle', [
            'name_english'          => 'Freestyle Jump',
            'name_french'           => 'Saut Freestyle',
            'description_english'   => 'A skier races down the ramp – press the button at exactly the right moment to launch the perfect jump! Hit the green zone for a high score and earn bonus cash and reputation for your resort.',
            'description_french'    => 'Un skieur dévale la rampe – appuyez sur le bouton exactement au bon moment pour effectuer le saut parfait ! Visez la zone verte pour un score élevé et gagnez du cash et de la réputation pour votre station.',
            'play_cost'             => 0,
            'max_reward_cash'       => 4500,
            'max_reward_reputation' => 7,
            'cooldown_hours'        => 24,
            'active'                => 1,
            'game_order'            => 11,
        ]);

        if (!$this->db->table_exists('game_minigame_plays')) {
            $this->db->query('
                CREATE TABLE game_minigame_plays (
                    id_play            INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    id_resort          INT UNSIGNED NOT NULL,
                    id_minigame        INT UNSIGNED NOT NULL,
                    play_datetime      DATETIME,
                    result             VARCHAR(10) NOT NULL DEFAULT \'lose\',
                    score              INT NOT NULL DEFAULT 0,
                    reward_cash        INT NOT NULL DEFAULT 0,
                    reward_reputation  INT NOT NULL DEFAULT 0,
                    PRIMARY KEY (id_play)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ');
        }

        if (!$this->db->table_exists('game_minigame_streaks')) {
            $this->db->query('
                CREATE TABLE game_minigame_streaks (
                    id_resort       INT UNSIGNED NOT NULL,
                    current_streak  INT NOT NULL DEFAULT 0,
                    best_streak     INT NOT NULL DEFAULT 0,
                    last_win_date   DATE,
                    PRIMARY KEY (id_resort)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ');
        }
    }

    /**
     * Insert a minigame row if no row with that minigame_type already exists.
     */
    private function _seed_minigame_if_missing($type, $data) {
        $exists = $this->db->where('minigame_type', $type)->get('game_minigames');
        if ($exists->num_rows() === 0) {
            $this->db->insert('game_minigames', array_merge($data, ['minigame_type' => $type]));
        }
    }

    /**
     * Get all active minigames ordered by game_order.
     */
    public function get_all_minigames() {
        $this->db->where('active', 1);
        $this->db->order_by('game_order', 'asc');
        return $this->db->get('game_minigames');
    }

    /**
     * Get a single minigame by its ID.
     */
    public function get_minigame($id_minigame) {
        $this->db->where('id_minigame', (int)$id_minigame);
        $this->db->where('active', 1);
        return $this->db->get('game_minigames');
    }

    /**
     * Get the most recent play of a specific minigame by a resort.
     */
    public function get_last_play($id_resort, $id_minigame) {
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->where('id_minigame', (int)$id_minigame);
        $this->db->order_by('play_datetime', 'DESC');
        $this->db->limit(1);
        return $this->db->get('game_minigame_plays');
    }

    /**
     * Record a minigame play result.
     */
    public function record_play($data) {
        return $this->db->insert('game_minigame_plays', $data);
    }

    /**
     * Get play history for a resort (most recent first, limited).
     *
     * @param  int      $id_resort
     * @param  int      $limit        Maximum number of rows to return
     * @param  int|null $id_minigame  Optional filter: only return plays of this game
     */
    public function get_play_history($id_resort, $limit = 25, $id_minigame = null) {
        $this->db->select('p.*, m.name_english, m.name_french');
        $this->db->from('game_minigame_plays p');
        $this->db->join('game_minigames m', 'p.id_minigame = m.id_minigame', 'left');
        $this->db->where('p.id_resort', (int)$id_resort);
        if ($id_minigame !== null) {
            $this->db->where('p.id_minigame', (int)$id_minigame);
        }
        $this->db->order_by('p.play_datetime', 'DESC');
        $this->db->limit($limit);
        return $this->db->get();
    }

    /**
     * Get aggregate stats for a resort (total wins, total cash earned, total rep earned, win rate).
     */
    public function get_stats($id_resort) {
        $this->db->select("COUNT(id_play) as total_plays,
            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as total_wins,
            COALESCE(SUM(reward_cash), 0) as total_cash_earned,
            COALESCE(SUM(reward_reputation), 0) as total_rep_earned,
            CASE WHEN COUNT(id_play) > 0
                THEN ROUND(SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) * 100 / COUNT(id_play))
                ELSE 0 END as win_rate");
        $this->db->where('id_resort', (int)$id_resort);
        return $this->db->get('game_minigame_plays');
    }

    /**
     * Check if enough time has passed since the last play (cooldown check).
     * Returns true if the player CAN play (cooldown elapsed or no previous play).
     */
    public function is_cooldown_elapsed($id_resort, $id_minigame, $cooldown_hours) {
        $last = $this->get_last_play($id_resort, $id_minigame);
        if ($last->num_rows() === 0) {
            return true;
        }
        $last_row = $last->row();
        $last_time = strtotime($last_row->play_datetime);
        $now = time();
        return ($now - $last_time) >= ($cooldown_hours * 3600);
    }

    /**
     * Get per-game statistics for a resort: plays, wins, win rate.
     * Returns an associative array keyed by id_minigame.
     *
     * @param  int $id_resort
     * @return array  [id_minigame => ['plays'=>int, 'wins'=>int, 'win_rate'=>float], …]
     */
    public function get_per_game_stats($id_resort) {
        $this->db->select("id_minigame,
            COUNT(id_play) as plays,
            SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins");
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->group_by('id_minigame');
        $query  = $this->db->get('game_minigame_plays');
        $result = [];
        foreach ($query->result() as $row) {
            $plays    = (int)$row->plays;
            $wins     = (int)$row->wins;
            $win_rate = ($plays > 0) ? round(($wins / $plays) * 100) : 0;
            $result[(int)$row->id_minigame] = [
                'plays'    => $plays,
                'wins'     => $wins,
                'win_rate' => $win_rate,
            ];
        }
        return $result;
    }

    /**
     * Get the personal best score for each minigame a resort has won at least once.
     * Only plays with result = 'win' are considered; non-winning plays are excluded
     * because a score of 0 on a loss should not overwrite a previous high score.
     * Returns an associative array keyed by id_minigame => best_score (int).
     *
     * @param  int $id_resort
     * @return array  [id_minigame => best_score_int, …]
     */
    public function get_best_scores($id_resort) {
        $this->db->select('id_minigame, MAX(score) as best_score');
        $this->db->where('id_resort', (int)$id_resort);
        $this->db->where('result', 'win');
        $this->db->group_by('id_minigame');
        $query  = $this->db->get('game_minigame_plays');
        $result = [];
        foreach ($query->result() as $row) {
            $result[(int)$row->id_minigame] = (int)$row->best_score;
        }
        return $result;
    }

    /**
     * Add a reward (cash or reputation) to a resort.
     * Follows the same pattern as Special_events_model::update_resort_column().
     *
     * @param int    $currentUserID Player user ID (game_resorts.id_player)
     * @param string $column        'cash' or 'reputation'
     * @param int    $amount        Amount to add (must be > 0)
     */
    public function add_reward($currentUserID, $column, $amount) {
        if ((int)$amount <= 0) {
            return false;
        }
        $allowed = ['cash', 'reputation'];
        if (!in_array($column, $allowed, true)) {
            return false;
        }
        $this->db->set($column, $column . '+' . (int)$amount, FALSE);
        $this->db->where('id_player', (int)$currentUserID);
        $this->db->limit(1);
        $this->db->update('game_resorts');
        return $this->db->affected_rows() === 1;
    }

    /**
     * Get the current win streak row for a resort, or null if none.
     *
     * @param  int $id_resort
     * @return object|null
     */
    public function get_streak($id_resort) {
        $this->db->where('id_resort', (int)$id_resort);
        $q = $this->db->get('game_minigame_streaks');
        return ($q->num_rows() > 0) ? $q->row() : null;
    }

    /**
     * Compute what the current streak will become after a win on $win_date,
     * without persisting any changes.  Used to decide whether to apply the
     * streak bonus before recording the play.
     *
     * @param  int    $id_resort
     * @param  string $win_date  Date string in 'Y-m-d' format (UTC)
     * @return int  The streak value that will apply for this win
     */
    public function compute_next_streak($id_resort, $win_date) {
        $row = $this->get_streak($id_resort);
        if ($row === null) {
            return 1;
        }
        $last_date = $row->last_win_date;
        $current   = (int)$row->current_streak;
        if ($last_date === $win_date) {
            return $current;
        }
        $yesterday = gmdate('Y-m-d', strtotime($win_date . ' -1 day'));
        return ($last_date === $yesterday) ? $current + 1 : 1;
    }

    /**
     * Update the daily win streak for a resort after a win.
     * - If the last win was yesterday, increment current_streak.
     * - If the last win was today, streak is unchanged (already counted).
     * - Otherwise (gap of 2+ days or first win), reset current_streak to 1.
     * - Update best_streak if current_streak exceeds it.
     *
     * @param  int    $id_resort
     * @param  string $win_date  Date string in 'Y-m-d' format (UTC)
     */
    public function update_streak($id_resort, $win_date) {
        $row = $this->get_streak($id_resort);
        if ($row === null) {
            // First win ever
            $this->db->insert('game_minigame_streaks', [
                'id_resort'      => (int)$id_resort,
                'current_streak' => 1,
                'best_streak'    => 1,
                'last_win_date'  => $win_date,
            ]);
            return;
        }

        $last_date = $row->last_win_date;
        $best      = (int)$row->best_streak;

        if ($last_date === $win_date) {
            // Already won today – no change
            return;
        }

        $current = $this->compute_next_streak($id_resort, $win_date);
        $best    = max($best, $current);

        $this->db->where('id_resort', (int)$id_resort);
        $this->db->update('game_minigame_streaks', [
            'current_streak' => $current,
            'best_streak'    => $best,
            'last_win_date'  => $win_date,
        ]);
    }
}
