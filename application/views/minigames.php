<div class="w-full">

<?php
echo $title;
echo $intro;
?>

<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

    <?php
    $lang        = $this->lang->line('minigames');
    $name_col    = 'name_' . $this->session->userdata('site_lang');
    $base        = base_url();
    $submit_url  = $base . 'minigames_controller/submit';
    ?>

    <!-- Stats block -->
    <?php if (isset($minigame_stats) && $minigame_stats !== null && (int)$minigame_stats->total_plays > 0) : ?>
    <div class="grid grid-cols-12 gap-3 mb-3">
        <div class="col-span-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($lang['stats_title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    <div class="flex flex-wrap gap-3">
                        <div class="text-center p-3 bg-base-200 rounded" style="min-width:120px;">
                            <div class="text-2xl font-bold text-primary"><?php echo (int)$minigame_stats->total_plays; ?></div>
                            <div class="text-xs text-base-content opacity-60"><?php echo htmlspecialchars($lang['stats_total_plays'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                        <div class="text-center p-3 bg-base-200 rounded" style="min-width:120px;">
                            <div class="text-2xl font-bold text-success"><?php echo (int)$minigame_stats->total_wins; ?></div>
                            <div class="text-xs text-base-content opacity-60"><?php echo htmlspecialchars($lang['stats_total_wins'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                        <div class="text-center p-3 bg-base-200 rounded" style="min-width:120px;">
                            <?php
                            $wr = (int)$minigame_stats->win_rate;
                            $wr_class = $wr >= 60 ? 'text-success' : ($wr >= 35 ? 'text-warning' : 'text-error');
                            ?>
                            <div class="text-2xl font-bold <?php echo $wr_class; ?>"><?php echo $wr; ?>%</div>
                            <div class="text-xs text-base-content opacity-60"><?php echo htmlspecialchars($lang['stats_win_rate'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                        <div class="text-center p-3 bg-base-200 rounded" style="min-width:120px;">
                            <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$minigame_stats->total_cash_earned, 0, ',', ' '); ?>€</div>
                            <div class="text-xs text-base-content opacity-60"><?php echo htmlspecialchars($lang['stats_total_cash'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                        <div class="text-center p-3 bg-base-200 rounded" style="min-width:120px;">
                            <div class="text-2xl font-bold text-primary">+<?php echo (int)$minigame_stats->total_rep_earned; ?></div>
                            <div class="text-xs text-base-content opacity-60"><?php echo htmlspecialchars($lang['stats_total_rep'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                        <?php if (isset($win_streak) && $win_streak !== null && (int)$win_streak->current_streak > 0) : ?>
                        <div class="text-center p-3 bg-warning rounded" style="min-width:120px;--tw-bg-opacity:0.15;">
                            <div class="text-2xl font-bold text-warning">🔥 <?php echo (int)$win_streak->current_streak; ?></div>
                            <div class="text-xs text-base-content opacity-60"><?php echo htmlspecialchars($lang['streak_current'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php if ((int)$win_streak->best_streak > 1) : ?>
                            <div class="text-xs text-base-content opacity-50"><?php echo htmlspecialchars($lang['streak_best'], ENT_QUOTES, 'UTF-8'); ?>: <?php echo (int)$win_streak->best_streak; ?></div>
                            <?php endif; ?>
                            <?php if ((int)$win_streak->current_streak >= 3) : ?>
                            <div class="text-xs font-semibold text-warning">+10% <?php echo htmlspecialchars($lang['streak_bonus'] ?? 'bonus', ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Minigame cards -->
    <div class="grid gap-3 mb-4">
    <?php foreach ($minigames as $mg) :
        $gid     = (int)$mg->id_minigame;
        $status  = $game_status[$gid];
        $can_play = $status['can_play'];
        $gname   = htmlspecialchars($status['name'], ENT_QUOTES, 'UTF-8');
        $gdesc   = htmlspecialchars($status['description'], ENT_QUOTES, 'UTF-8');
        $mtype   = htmlspecialchars($mg->minigame_type, ENT_QUOTES, 'UTF-8');
    ?>
    <div class="md:col-span-4">
        <div class="card h-full shadow-sm" id="card-game-<?php echo $gid; ?>">
            <div class="card-header flex items-center gap-2">
                <?php if ($mg->minigame_type === 'luck') : ?>
                    <i class="fa-solid fa-dice-five text-2xl text-warning"></i>
                <?php elseif ($mg->minigame_type === 'quiz') : ?>
                    <i class="fa-regular fa-circle-question text-2xl text-info"></i>
                <?php elseif ($mg->minigame_type === 'snowmaking') : ?>
                    <i class="fa-solid fa-cloud-snow text-2xl text-primary"></i>
                <?php elseif ($mg->minigame_type === 'grooming') : ?>
                    <i class="fa-solid fa-snowflake2 text-2xl text-primary"></i>
                <?php elseif ($mg->minigame_type === 'avalanche') : ?>
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-error"></i>
                <?php elseif ($mg->minigame_type === 'liftline') : ?>
                    <i class="fa-solid fa-table-cells text-2xl text-success"></i>
                <?php elseif ($mg->minigame_type === 'icebreaker') : ?>
                    <i class="fa-solid fa-hammer text-2xl text-info"></i>
                <?php elseif ($mg->minigame_type === 'slalom') : ?>
                    <i class="fa-regular fa-flag text-2xl text-error"></i>
                <?php elseif ($mg->minigame_type === 'patrol') : ?>
                    <i class="fa-solid fa-heart-pulse text-2xl text-error"></i>
                <?php elseif ($mg->minigame_type === 'freestyle') : ?>
                    <i class="fa-regular fa-trophy text-2xl text-warning"></i>
                <?php elseif ($mg->minigame_type === 'biathlon') : ?>
                    <i class="fa-solid fa-bullseye text-2xl text-error"></i>
                <?php elseif ($mg->minigame_type === 'snowboard') : ?>
                    <i class="fa-solid fa-snowflake3 text-2xl text-primary"></i>
                <?php else : ?>
                    <i class="fa-solid fa-bolt text-2xl text-error"></i>
                <?php endif; ?>
                <strong><?php echo $gname; ?></strong>
                <span class="badge ml-auto <?php echo $can_play ? 'badge-success' : 'badge-neutral'; ?>">
                    <?php echo $can_play ? htmlspecialchars($lang['available'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($lang['on_cooldown'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
            <div class="card-body">
                <p class="card-text small"><?php echo $gdesc; ?></p>
                <ul class="list-none small mb-3">
                    <?php if ((int)$mg->max_reward_cash > 0) : ?>
                    <li><i class="fa-solid fa-euro-sign text-success mr-1"></i><?php echo htmlspecialchars($lang['max_reward_cash'], ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo number_format((int)$mg->max_reward_cash, 0, ',', ' '); ?>€</strong></li>
                    <?php endif; ?>
                    <?php if ((int)$mg->max_reward_reputation > 0) : ?>
                    <li><i class="fa-regular fa-star text-warning mr-1"></i><?php echo htmlspecialchars($lang['max_reward_rep'], ENT_QUOTES, 'UTF-8'); ?>: <strong>+<?php echo (int)$mg->max_reward_reputation; ?></strong></li>
                    <?php endif; ?>
                    <li><i class="fa-regular fa-clock text-neutral-content mr-1"></i><?php echo htmlspecialchars($lang['cooldown'], ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo (int)$mg->cooldown_hours; ?>h</strong></li>
                    <?php if (isset($best_scores[$gid])) : ?>
                    <li><i class="fa-regular fa-trophy text-warning mr-1"></i><?php echo htmlspecialchars($lang['best_score'], ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo (int)$best_scores[$gid]; ?></strong></li>
                    <?php endif; ?>
                    <?php if (isset($per_game_stats[$gid]) && $per_game_stats[$gid]['plays'] > 0) :
                        $pgs = $per_game_stats[$gid];
                        $pgs_wr_class = $pgs['win_rate'] >= 60 ? 'text-success' : ($pgs['win_rate'] >= 35 ? 'text-warning' : 'text-error');
                    ?>
                    <li><i class="fa-solid fa-chart-bar text-primary mr-1"></i><?php echo htmlspecialchars($lang['card_plays'], ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo $pgs['plays']; ?></strong> &nbsp;<?php echo htmlspecialchars($lang['card_wins'], ENT_QUOTES, 'UTF-8'); ?>: <strong><?php echo $pgs['wins']; ?></strong></li>
                    <li><i class="fa-solid fa-percent text-neutral-content mr-1"></i><?php echo htmlspecialchars($lang['card_win_rate'], ENT_QUOTES, 'UTF-8'); ?>: <strong class="<?php echo $pgs_wr_class; ?>"><?php echo $pgs['win_rate']; ?>%</strong></li>
                    <?php endif; ?>
                </ul>
                <!-- Game area (hidden until play) -->
                <div class="minigame-area" id="game-area-<?php echo $gid; ?>" data-type="<?php echo $mtype; ?>" style="display:none;"></div>
                <div id="result-<?php echo $gid; ?>"></div>
            </div>
            <div class="card-footer">
                <?php if ($can_play) : ?>
                <button class="btn btn-primary btn-sm w-full minigame-play-btn"
                        data-game-id="<?php echo $gid; ?>"
                        data-type="<?php echo $mtype; ?>"
                        data-submit-url="<?php echo htmlspecialchars($submit_url, ENT_QUOTES, 'UTF-8'); ?>">
                    <i class="fa-regular fa-circle-play mr-1"></i><?php echo htmlspecialchars($lang['play'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
                <?php else : ?>
                <button class="btn btn-secondary btn-sm w-full" disabled>
                    <i class="fa-regular fa-hourglass mr-1"></i><?php echo htmlspecialchars($lang['come_back_later'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div><!-- /.row -->

    <!-- Play history -->
    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12">
            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                <h5 class="h5 mb-0"><?php echo htmlspecialchars($lang['history_title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <!-- Game filter -->
                <form method="get" action="" class="flex items-center gap-2">
                    <label for="mg-filter" class="small mb-0"><?php echo htmlspecialchars($lang['history_filter_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <select id="mg-filter" name="game" class="select select-sm" style="max-width:200px;">
                        <option value="0"><?php echo htmlspecialchars($lang['history_filter_all'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php foreach ($minigames as $mg_opt) :
                            $opt_name_col = 'name_' . $this->session->userdata('site_lang');
                            $opt_name = isset($mg_opt->$opt_name_col) ? $mg_opt->$opt_name_col : $mg_opt->name_english;
                            $selected = (isset($filter_game) && (int)$filter_game === (int)$mg_opt->id_minigame) ? ' selected' : '';
                        ?>
                        <option value="<?php echo (int)$mg_opt->id_minigame; ?>"<?php echo $selected; ?>><?php echo htmlspecialchars($opt_name, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline"><i class="fa-solid fa-filter"></i></button>
                </form>
            </div>
            <?php if (isset($play_history) && $play_history->num_rows() > 0) : ?>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th><?php echo htmlspecialchars($lang['history_game'], ENT_QUOTES, 'UTF-8'); ?></th>
                            <th><?php echo htmlspecialchars($lang['history_date'], ENT_QUOTES, 'UTF-8'); ?></th>
                            <th><?php echo htmlspecialchars($lang['history_result'], ENT_QUOTES, 'UTF-8'); ?></th>
                            <th><?php echo htmlspecialchars($lang['history_score'], ENT_QUOTES, 'UTF-8'); ?></th>
                            <th><?php echo htmlspecialchars($lang['history_cash'], ENT_QUOTES, 'UTF-8'); ?></th>
                            <th><?php echo htmlspecialchars($lang['history_rep'], ENT_QUOTES, 'UTF-8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $site_lang = $this->session->userdata('site_lang');
                    $h_name_col = 'name_' . $site_lang;
                    foreach ($play_history->result() as $h) :
                        $hname = isset($h->$h_name_col) ? $h->$h_name_col : $h->name_english;
                        $hscore = (int)$h->score;
                        if ($h->result === 'win') {
                            $score_class = $hscore >= 80 ? 'text-success' : ($hscore >= 50 ? 'text-warning' : 'text-base-content opacity-50');
                        } else {
                            $score_class = 'text-base-content opacity-40';
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hname, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($h->play_datetime, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ($h->result === 'win') : ?>
                                    <span class="badge badge-success"><?php echo htmlspecialchars($lang['result_win'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php else : ?>
                                    <span class="badge badge-neutral"><?php echo htmlspecialchars($lang['result_lose'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong class="<?php echo $score_class; ?>"><?php echo $hscore > 0 ? $hscore : '–'; ?></strong></td>
                            <td><?php echo (int)$h->reward_cash > 0 ? '+' . number_format((int)$h->reward_cash, 0, ',', ' ') . '€' : '–'; ?></td>
                            <td><?php echo (int)$h->reward_reputation > 0 ? '+' . (int)$h->reward_reputation : '–'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else : ?>
            <p class="text-base-content/60 small"><?php echo htmlspecialchars($lang['history_no_plays'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.container-border -->
</div><!-- /.w-full -->

<!-- =====================================================================
     MINIGAME JAVASCRIPT
===================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
(function ($) {
    'use strict';

    /* ----------------------------------------------------------------
       Localised strings injected from PHP
    ---------------------------------------------------------------- */
    var LANG = {
        spin:          <?php echo json_encode($lang['slot_spin']); ?>,
        spinning:      <?php echo json_encode($lang['slot_spinning']); ?>,
        win:           <?php echo json_encode($lang['result_win']); ?>,
        lose:          <?php echo json_encode($lang['result_lose']); ?>,
        submit_error:  <?php echo json_encode($lang['submit_error']); ?>,
        cooldown_msg:  <?php echo json_encode($lang['cooldown_msg']); ?>,
        cash_earned:   <?php echo json_encode($lang['cash_earned']); ?>,
        rep_earned:    <?php echo json_encode($lang['rep_earned']); ?>,
        quiz_submit:   <?php echo json_encode($lang['quiz_submit']); ?>,
        quiz_score:    <?php echo json_encode($lang['quiz_score']); ?>,
        rush_start:    <?php echo json_encode($lang['rush_start']); ?>,
        rush_click:    <?php echo json_encode($lang['rush_click']); ?>,
        rush_time_up:  <?php echo json_encode($lang['rush_time_up']); ?>,
        rush_score:    <?php echo json_encode($lang['rush_score']); ?>,
        sm_fire:       <?php echo json_encode($lang['snowmaking_fire']); ?>,
        sm_shots:      <?php echo json_encode($lang['snowmaking_shots']); ?>,
        sm_coverage:   <?php echo json_encode($lang['snowmaking_coverage']); ?>,
        sm_perfect:    <?php echo json_encode($lang['snowmaking_perfect']); ?>,
        sm_good:       <?php echo json_encode($lang['snowmaking_good']); ?>,
        sm_miss:       <?php echo json_encode($lang['snowmaking_miss']); ?>,
        sm_done:       <?php echo json_encode($lang['snowmaking_done']); ?>,
        sm_score:      <?php echo json_encode($lang['snowmaking_score']); ?>,
        groom_start:   <?php echo json_encode($lang['groom_start']); ?>,
        groom_time_up: <?php echo json_encode($lang['groom_time_up']); ?>,
        groom_score:   <?php echo json_encode($lang['groom_score']); ?>,
        play_again:    <?php echo json_encode($lang['come_back_later']); ?>,
        av_start:      <?php echo json_encode($lang['avalanche_start']); ?>,
        av_dodge:      <?php echo json_encode($lang['avalanche_dodge']); ?>,
        av_hit:        <?php echo json_encode($lang['avalanche_hit']); ?>,
        av_time_up:    <?php echo json_encode($lang['avalanche_time_up']); ?>,
        av_score:      <?php echo json_encode($lang['avalanche_score']); ?>,
        ll_start:      <?php echo json_encode($lang['liftline_start']); ?>,
        ll_watch:      <?php echo json_encode($lang['liftline_watch']); ?>,
        ll_go:         <?php echo json_encode($lang['liftline_go']); ?>,
        ll_correct:    <?php echo json_encode($lang['liftline_correct']); ?>,
        ll_wrong:      <?php echo json_encode($lang['liftline_wrong']); ?>,
        ll_score:      <?php echo json_encode($lang['liftline_score']); ?>,
        ib_start:      <?php echo json_encode($lang['icebreaker_start']); ?>,
        ib_click:      <?php echo json_encode($lang['icebreaker_click']); ?>,
        ib_time_up:    <?php echo json_encode($lang['icebreaker_time_up']); ?>,
        ib_score:      <?php echo json_encode($lang['icebreaker_score']); ?>,
        sl_left:       <?php echo json_encode($lang['slalom_left']); ?>,
        sl_right:      <?php echo json_encode($lang['slalom_right']); ?>,
        sl_go:         <?php echo json_encode($lang['slalom_go']); ?>,
        sl_gate:       <?php echo json_encode($lang['slalom_gate']); ?>,
        sl_correct:    <?php echo json_encode($lang['slalom_correct']); ?>,
        sl_miss:       <?php echo json_encode($lang['slalom_miss']); ?>,
        sl_done:       <?php echo json_encode($lang['slalom_done']); ?>,
        sl_score:      <?php echo json_encode($lang['slalom_score']); ?>,
        streak_bonus:  <?php echo json_encode($lang['streak_bonus']); ?>,
        pt_start:      <?php echo json_encode($lang['patrol_start']); ?>,
        pt_rescue:     <?php echo json_encode($lang['patrol_rescue']); ?>,
        pt_time_up:    <?php echo json_encode($lang['patrol_time_up']); ?>,
        pt_score:      <?php echo json_encode($lang['patrol_score']); ?>,
        fs_start:      <?php echo json_encode($lang['freestyle_start']); ?>,
        fs_jump:       <?php echo json_encode($lang['freestyle_jump']); ?>,
        fs_perfect:    <?php echo json_encode($lang['freestyle_perfect']); ?>,
        fs_good:       <?php echo json_encode($lang['freestyle_good']); ?>,
        fs_early:      <?php echo json_encode($lang['freestyle_early']); ?>,
        fs_late:       <?php echo json_encode($lang['freestyle_late']); ?>,
        fs_score:      <?php echo json_encode($lang['freestyle_score']); ?>,
        bt_start:      <?php echo json_encode($lang['biathlon_start']); ?>,
        bt_shoot:      <?php echo json_encode($lang['biathlon_shoot']); ?>,
        bt_hit:        <?php echo json_encode($lang['biathlon_hit']); ?>,
        bt_miss:       <?php echo json_encode($lang['biathlon_miss']); ?>,
        bt_done:       <?php echo json_encode($lang['biathlon_done']); ?>,
        bt_score:      <?php echo json_encode($lang['biathlon_score']); ?>,
        bt_shot:       <?php echo json_encode($lang['biathlon_shot']); ?>,
        sb_start:      <?php echo json_encode($lang['snowboard_start']); ?>,
        sb_trick:      <?php echo json_encode($lang['snowboard_trick']); ?>,
        sb_perfect:    <?php echo json_encode($lang['snowboard_perfect']); ?>,
        sb_good:       <?php echo json_encode($lang['snowboard_good']); ?>,
        sb_early:      <?php echo json_encode($lang['snowboard_early']); ?>,
        sb_late:       <?php echo json_encode($lang['snowboard_late']); ?>,
        sb_done:       <?php echo json_encode($lang['snowboard_done']); ?>,
        sb_score:      <?php echo json_encode($lang['snowboard_score']); ?>,
        sb_run:        <?php echo json_encode($lang['snowboard_run']); ?>,
    };

    /* ================================================================
       1. LUCKY SLALOM – Slot-machine game
    ================================================================ */
    var SLOT_SYMBOLS = ['🎿', '⛷️', '🏔️', '❄️', '🌨️', '🎽'];

    function buildSlotMachine(gameId) {
        return '<div class="slot-machine text-center" id="slot-' + gameId + '">'
             + '  <div class="flex justify-center gap-2 my-2">'
             + '    <div class="slot-reel border rounded p-2 text-3xl" id="slot-r1-' + gameId + '" style="min-width:60px;">🎿</div>'
             + '    <div class="slot-reel border rounded p-2 text-3xl" id="slot-r2-' + gameId + '" style="min-width:60px;">🎿</div>'
             + '    <div class="slot-reel border rounded p-2 text-3xl" id="slot-r3-' + gameId + '" style="min-width:60px;">🎿</div>'
             + '  </div>'
             + '  <button class="btn btn-warning btn-sm" id="slot-spin-' + gameId + '">'
             + '    <i class="fa-solid fa-rotate-right mr-1"></i>' + LANG.spin
             + '  </button>'
             + '</div>';
    }

    function spinSlot(gameId, submitUrl) {
        var $btn = $('#slot-spin-' + gameId);
        $btn.prop('disabled', true).text(LANG.spinning);

        var reels = ['#slot-r1-' + gameId, '#slot-r2-' + gameId, '#slot-r3-' + gameId];
        var delays = [400, 700, 1000];
        var results = [];

        reels.forEach(function (sel, i) {
            var interval = setInterval(function () {
                $(sel).text(SLOT_SYMBOLS[Math.floor(Math.random() * SLOT_SYMBOLS.length)]);
            }, 80);
            setTimeout(function () {
                clearInterval(interval);
                var final = SLOT_SYMBOLS[Math.floor(Math.random() * SLOT_SYMBOLS.length)];
                $(sel).text(final);
                results.push(final);
                if (results.length === 3) {
                    // Client determines score hint (100 = jackpot, 50 = two match)
                    var score = 0;
                    if (results[0] === results[1] && results[1] === results[2]) score = 100;
                    else if (results[0] === results[1] || results[1] === results[2] || results[0] === results[2]) score = 50;
                    submitPlay(gameId, submitUrl, score);
                }
            }, delays[i]);
        });
    }

    /* ================================================================
       2. SNOW QUIZ – Multiple-choice trivia
    ================================================================ */
    var QUIZ_QUESTIONS = <?php
        $all_questions = [
            [
                'q'  => $lang['q1'],
                'opts' => [$lang['q1_a'], $lang['q1_b'], $lang['q1_c'], $lang['q1_d']],
                'ans' => 0,
            ],
            [
                'q'  => $lang['q2'],
                'opts' => [$lang['q2_a'], $lang['q2_b'], $lang['q2_c'], $lang['q2_d']],
                'ans' => 1,
            ],
            [
                'q'  => $lang['q3'],
                'opts' => [$lang['q3_a'], $lang['q3_b'], $lang['q3_c'], $lang['q3_d']],
                'ans' => 2,
            ],
            [
                'q'  => $lang['q4'],
                'opts' => [$lang['q4_a'], $lang['q4_b'], $lang['q4_c'], $lang['q4_d']],
                'ans' => 3,
            ],
            [
                'q'  => $lang['q5'],
                'opts' => [$lang['q5_a'], $lang['q5_b'], $lang['q5_c'], $lang['q5_d']],
                'ans' => 0,
            ],
            [
                'q'  => $lang['q6'],
                'opts' => [$lang['q6_a'], $lang['q6_b'], $lang['q6_c'], $lang['q6_d']],
                'ans' => 0,
            ],
            [
                'q'  => $lang['q7'],
                'opts' => [$lang['q7_a'], $lang['q7_b'], $lang['q7_c'], $lang['q7_d']],
                'ans' => 1,
            ],
            [
                'q'  => $lang['q8'],
                'opts' => [$lang['q8_a'], $lang['q8_b'], $lang['q8_c'], $lang['q8_d']],
                'ans' => 2,
            ],
            [
                'q'  => $lang['q9'],
                'opts' => [$lang['q9_a'], $lang['q9_b'], $lang['q9_c'], $lang['q9_d']],
                'ans' => 0,
            ],
            [
                'q'  => $lang['q10'],
                'opts' => [$lang['q10_a'], $lang['q10_b'], $lang['q10_c'], $lang['q10_d']],
                'ans' => 3,
            ],
            [
                'q'  => $lang['q11'],
                'opts' => [$lang['q11_a'], $lang['q11_b'], $lang['q11_c'], $lang['q11_d']],
                'ans' => 1,
            ],
            [
                'q'  => $lang['q12'],
                'opts' => [$lang['q12_a'], $lang['q12_b'], $lang['q12_c'], $lang['q12_d']],
                'ans' => 2,
            ],
            [
                'q'  => $lang['q13'],
                'opts' => [$lang['q13_a'], $lang['q13_b'], $lang['q13_c'], $lang['q13_d']],
                'ans' => 0,
            ],
            [
                'q'  => $lang['q14'],
                'opts' => [$lang['q14_a'], $lang['q14_b'], $lang['q14_c'], $lang['q14_d']],
                'ans' => 3,
            ],
            [
                'q'  => $lang['q15'],
                'opts' => [$lang['q15_a'], $lang['q15_b'], $lang['q15_c'], $lang['q15_d']],
                'ans' => 1,
            ],
        ];
        // Randomly pick 5 questions from the full pool for each page load.
        // Guard against a pool smaller than 5 (future-proof).
        $pick_count = min(5, count($all_questions));
        if ($pick_count === count($all_questions)) {
            $questions = $all_questions;
            shuffle($questions);
        } else {
            $keys = (array) array_rand($all_questions, $pick_count);
            shuffle($keys);
            $questions = [];
            foreach ($keys as $k) {
                $questions[] = $all_questions[$k];
            }
        }
        echo json_encode($questions);
    ?>;

    function buildQuiz(gameId) {
        var html = '<div id="quiz-' + gameId + '" class="quiz-container">';
        QUIZ_QUESTIONS.forEach(function (q, qi) {
            html += '<div class="mb-3 quiz-q" id="qq-' + gameId + '-' + qi + '">';
            html += '<p class="font-bold small mb-1">' + (qi + 1) + '. ' + escHtml(q.q) + '</p>';
            q.opts.forEach(function (opt, oi) {
                var rid = 'qr-' + gameId + '-' + qi + '-' + oi;
                html += '<label class="label cursor-pointer justify-start gap-2 py-0.5">'
                      + '<input class="radio radio-sm" type="radio" name="quiz-' + gameId + '-' + qi + '" id="' + rid + '" value="' + oi + '">'
                      + '<span class="label-text text-sm">' + escHtml(opt) + '</span>'
                      + '</label>';
            });
            html += '</div>';
        });
        html += '<button class="btn btn-success btn-sm" id="quiz-submit-' + gameId + '">'
              + '<i class="fa-regular fa-circle-check mr-1"></i>' + LANG.quiz_submit
              + '</button>';
        html += '</div>';
        return html;
    }

    function gradeQuiz(gameId, submitUrl) {
        var correct = 0;
        QUIZ_QUESTIONS.forEach(function (q, qi) {
            var sel = $('input[name="quiz-' + gameId + '-' + qi + '"]:checked').val();
            if (parseInt(sel, 10) === q.ans) correct++;
        });
        var score = Math.round((correct / QUIZ_QUESTIONS.length) * 100);
        submitPlay(gameId, submitUrl, score, LANG.quiz_score + ' ' + correct + '/' + QUIZ_QUESTIONS.length);
    }

    /* ================================================================
       3. SNOWBALL RUSH – Click-target reflex game
    ================================================================ */
    var RUSH_DURATION = 15; // seconds

    function buildRush(gameId) {
        return '<div id="rush-' + gameId + '" class="rush-container">'
             + '  <div id="rush-arena-' + gameId + '" class="rush-arena border rounded relative bg-base-200" style="height:140px;overflow:hidden;">'
             + '    <div class="text-center text-base-content/60 pt-4">' + LANG.rush_start + '</div>'
             + '  </div>'
             + '  <div class="flex justify-between mt-1">'
             + '    <span>' + LANG.rush_score + ': <strong id="rush-score-' + gameId + '">0</strong></span>'
             + '    <span id="rush-timer-' + gameId + '"></span>'
             + '  </div>'
             + '  <button class="btn btn-error btn-sm mt-2" id="rush-start-' + gameId + '">'
             + '    <i class="fa-solid fa-play mr-1"></i>' + LANG.rush_start
             + '  </button>'
             + '</div>';
    }

    function startRush(gameId, submitUrl) {
        var $arena  = $('#rush-arena-' + gameId);
        var $scoreEl = $('#rush-score-' + gameId);
        var $timer   = $('#rush-timer-' + gameId);
        var $btn     = $('#rush-start-' + gameId);
        var score    = 0;
        var timeLeft = RUSH_DURATION;
        $btn.prop('disabled', true);
        $arena.empty();

        var countdown = setInterval(function () {
            timeLeft--;
            $timer.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(countdown);
                clearInterval(spawner);
                $arena.empty();
                $arena.html('<div class="text-center pt-4">' + LANG.rush_time_up + '</div>');
                var finalScore = Math.min(100, Math.round((score / 15) * 100));
                submitPlay(gameId, submitUrl, finalScore, LANG.rush_score + ': ' + score);
            }
        }, 1000);

        var spawner = setInterval(function () {
            if (timeLeft <= 0) return;
            var left = Math.floor(Math.random() * 85);
            var top  = Math.floor(Math.random() * 70);
            var $ball = $('<div class="snowball" title="❄️" style="position:absolute;left:' + left + '%;top:' + top + '%;cursor:pointer;font-size:1.6rem;user-select:none;">❄️</div>');
            $ball.on('click', function () {
                score++;
                $scoreEl.text(score);
                $ball.remove();
            });
            $arena.append($ball);
            setTimeout(function () { $ball.remove(); }, 1200);
        }, 700);
    }

    /* ================================================================
       4. SNOWMAKING CHALLENGE – Precision timing game
       The player fires 5 shots by clicking when a moving needle is
       inside the optimal zone. Score = (points / 100) * 100.
       Perfect (in inner zone): 20 pts | Good (outer zone): 10 pts | Miss: 0 pts
    ================================================================ */
    var SM_SHOTS_TOTAL = 5;
    var SM_PERFECT_PTS = 20;
    var SM_GOOD_PTS    = 10;
    var SM_FRAME_TIME  = 16.67; // ms at 60 fps
    // Zone boundaries (as percentages of bar width)
    var SM_OUTER_MIN = 35, SM_OUTER_MAX = 65;  // good zone
    var SM_INNER_MIN = 43, SM_INNER_MAX = 57;  // perfect zone

    function buildSnowmaking(gameId) {
        return '<div id="snowmaking-' + gameId + '" class="snowmaking-container text-center">'
             + '  <div class="relative border rounded my-2" style="height:28px;background:#e5e7eb;overflow:hidden;">'
             + '    <div style="position:absolute;left:' + SM_OUTER_MIN + '%;width:' + (SM_OUTER_MAX - SM_OUTER_MIN) + '%;height:100%;background:rgba(37,99,235,0.25);"></div>'
             + '    <div style="position:absolute;left:' + SM_INNER_MIN + '%;width:' + (SM_INNER_MAX - SM_INNER_MIN) + '%;height:100%;background:rgba(37,99,235,0.55);"></div>'
             + '    <div id="sm-needle-' + gameId + '" style="position:absolute;top:0;left:0%;width:4px;height:100%;background:#1e3a8a;"></div>'
             + '    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">'
             + '      <small style="color:#374151;font-size:0.7rem;pointer-events:none;">❄️</small>'
             + '    </div>'
             + '  </div>'
             + '  <div class="flex justify-between small mb-2">'
             + '    <span>' + LANG.sm_shots + ': <strong id="sm-shots-' + gameId + '">' + SM_SHOTS_TOTAL + '</strong></span>'
             + '    <span>' + LANG.sm_coverage + ': <strong id="sm-cov-' + gameId + '">0</strong>%</span>'
             + '  </div>'
             + '  <div id="sm-hit-' + gameId + '" class="small mb-1" style="min-height:1.4em;"></div>'
             + '  <button class="btn btn-primary btn-sm px-4" id="sm-fire-' + gameId + '">'
             + '    <i class="fa-solid fa-snowflake mr-1"></i>' + LANG.sm_fire
             + '  </button>'
             + '</div>';
    }

    /* ================================================================
       4. GROOMING RUSH – Click-to-groom slope tiles
    ================================================================ */
    var GROOM_COLS     = 5;
    var GROOM_ROWS     = 3;
    var GROOM_DURATION = 12; // seconds

    function buildGrooming(gameId) {
        return '<div id="groom-' + gameId + '" class="groom-container">'
             + '  <div id="groom-grid-' + gameId + '" class="flex flex-wrap gap-1 justify-center border rounded p-2 bg-base-200" style="max-width:320px;margin:0 auto;">'
             + '    <div class="text-center text-base-content/60 w-full py-2 small">' + escHtml(LANG.groom_start) + '</div>'
             + '  </div>'
             + '  <div class="flex justify-between mt-1">'
             + '    <span class="small">' + escHtml(LANG.groom_score) + ': <strong id="groom-score-' + gameId + '">0</strong></span>'
             + '    <span id="groom-timer-' + gameId + '" class="small"></span>'
             + '  </div>'
             + '  <button class="btn btn-info btn-sm mt-2" id="groom-start-' + gameId + '">'
             + '    <i class="fa-solid fa-snowflake2 mr-1"></i>' + escHtml(LANG.groom_start)
             + '  </button>'
             + '</div>';
    }

    function startSnowmaking(gameId, submitUrl) {
        var shotsLeft = SM_SHOTS_TOTAL;
        var totalPoints = 0;
        var pos = 0;
        var dir = 1;
        var speed = 1.8; // percent per frame (~60fps target)

        var $needle = $('#sm-needle-' + gameId);
        var $shots  = $('#sm-shots-' + gameId);
        var $cov    = $('#sm-cov-' + gameId);
        var $hit    = $('#sm-hit-' + gameId);
        var $fire   = $('#sm-fire-' + gameId);

        var raf;
        var lastTime = null;

        function animate(ts) {
            if (!lastTime) { lastTime = ts; }
            var delta = (ts - lastTime) / SM_FRAME_TIME; // normalise to ~60fps
            lastTime = ts;
            pos += dir * speed * delta;
            if (pos >= 100) { pos = 100; dir = -1; }
            if (pos <= 0)   { pos = 0;   dir =  1; }
            $needle.css('left', pos.toFixed(1) + '%');
            raf = requestAnimationFrame(animate);
        }

        raf = requestAnimationFrame(animate);

        $fire.on('click.sm', function () {
            var hitPos = pos;
            var pts = 0;
            var hitMsg = '';

            if (hitPos >= SM_INNER_MIN && hitPos <= SM_INNER_MAX) {
                pts = SM_PERFECT_PTS;
                hitMsg = '<span class="text-primary font-bold">' + LANG.sm_perfect + '</span>';
            } else if (hitPos >= SM_OUTER_MIN && hitPos <= SM_OUTER_MAX) {
                pts = SM_GOOD_PTS;
                hitMsg = '<span class="text-success">' + LANG.sm_good + '</span>';
            } else {
                pts = 0;
                hitMsg = '<span class="text-neutral-content">' + LANG.sm_miss + '</span>';
            }

            totalPoints += pts;
            shotsLeft--;
            $shots.text(shotsLeft);
            var coveragePct = Math.round((totalPoints / (SM_SHOTS_TOTAL * SM_PERFECT_PTS)) * 100);
            $cov.text(coveragePct);
            $hit.html(hitMsg);

            if (shotsLeft <= 0) {
                cancelAnimationFrame(raf);
                $fire.off('click.sm').prop('disabled', true);
                $hit.html('<span class="font-bold">' + LANG.sm_done + '</span>');
                var score = Math.min(100, coveragePct);
                submitPlay(gameId, submitUrl, score, LANG.sm_score + ': ' + score + '%');
            }
        });
    }

    function startGrooming(gameId, submitUrl) {
        var $grid    = $('#groom-grid-' + gameId);
        var $scoreEl = $('#groom-score-' + gameId);
        var $timer   = $('#groom-timer-' + gameId);
        var $btn     = $('#groom-start-' + gameId);
        var groomed  = 0;
        var total    = GROOM_COLS * GROOM_ROWS;
        var timeLeft = GROOM_DURATION;
        $btn.prop('disabled', true);
        $grid.empty();

        for (var i = 0; i < total; i++) {
            var $cell = $('<div class="groom-cell" style="width:50px;height:40px;cursor:pointer;background:#cce5ff;border:1px solid #99c8f5;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;font-size:1.2rem;margin:2px;" data-groomed="0">🌨️</div>');
            $cell.on('click', function () {
                if ($(this).data('groomed') === 1) { return; }
                $(this).data('groomed', 1).css({'background': '#a8d5a2', 'border-color': '#6abf69'}).html('✅');
                groomed++;
                $scoreEl.text(groomed);
            });
            $grid.append($cell);
        }

        var countdown = setInterval(function () {
            timeLeft--;
            $timer.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(countdown);
                $grid.find('.groom-cell').off('click').css('cursor', 'default');
                $grid.append('<div class="w-full text-center small mt-1">' + escHtml(LANG.groom_time_up) + '</div>');
                var finalScore = Math.round((groomed / total) * 100);
                submitPlay(gameId, submitUrl, finalScore, LANG.groom_score + ': ' + groomed + '/' + total);
            }
        }, 1000);
    }

    /* ================================================================
       6. AVALANCHE ESCAPE – Dodge falling boulders
       The player controls a skier at the bottom of the arena and
       must dodge falling snow boulders by clicking left/right.
    ================================================================ */
    var AV_DURATION = 12; // seconds
    var AV_TOTAL_BOULDERS = 15;

    function buildAvalanche(gameId) {
        return '<div id="av-' + gameId + '" class="av-container">'
             + '  <div id="av-arena-' + gameId + '" class="border rounded relative bg-base-200" style="height:160px;overflow:hidden;">'
             + '    <div class="text-center text-base-content/60 pt-5">' + escHtml(LANG.av_start) + '</div>'
             + '  </div>'
             + '  <div class="flex justify-between mt-1">'
             + '    <span class="small">' + escHtml(LANG.av_score) + ': <strong id="av-score-' + gameId + '">0</strong></span>'
             + '    <span id="av-timer-' + gameId + '" class="small"></span>'
             + '  </div>'
             + '</div>';
    }

    function startAvalanche(gameId, submitUrl) {
        var $arena  = $('#av-arena-' + gameId);
        var $scoreEl = $('#av-score-' + gameId);
        var $timer  = $('#av-timer-' + gameId);
        var dodged  = 0;
        var missed  = 0;
        var total   = AV_TOTAL_BOULDERS;
        var timeLeft = AV_DURATION;
        var playerPos = 50; // percentage from left
        $arena.empty();

        // Player element
        var $player = $('<div style="position:absolute;bottom:4px;left:' + playerPos + '%;transform:translateX(-50%);font-size:1.5rem;transition:left 0.15s;user-select:none;">⛷️</div>');
        $arena.append($player);

        // Arrow key / click controls
        var $leftBtn = $('<button class="btn btn-outline btn-sm absolute" style="bottom:4px;left:4px;z-index:10;">⬅️</button>');
        var $rightBtn = $('<button class="btn btn-outline btn-sm absolute" style="bottom:4px;right:4px;z-index:10;">➡️</button>');
        $arena.append($leftBtn).append($rightBtn);

        $leftBtn.on('click', function () {
            playerPos = Math.max(5, playerPos - 15);
            $player.css('left', playerPos + '%');
        });
        $rightBtn.on('click', function () {
            playerPos = Math.min(95, playerPos + 15);
            $player.css('left', playerPos + '%');
        });

        // Keyboard support
        function onKey(e) {
            if (e.key === 'ArrowLeft' || e.key === 'a') {
                playerPos = Math.max(5, playerPos - 15);
                $player.css('left', playerPos + '%');
            } else if (e.key === 'ArrowRight' || e.key === 'd') {
                playerPos = Math.min(95, playerPos + 15);
                $player.css('left', playerPos + '%');
            }
        }
        $(document).on('keydown.av' + gameId, onKey);

        var boulderCount = 0;
        var spawner = setInterval(function () {
            if (boulderCount >= total || timeLeft <= 0) return;
            boulderCount++;
            var bLeft = Math.floor(Math.random() * 85) + 5;
            var $boulder = $('<div style="position:absolute;top:-20px;left:' + bLeft + '%;font-size:1.3rem;transition:top 1.8s linear;user-select:none;">🪨</div>');
            $arena.append($boulder);
            // Animate falling
            setTimeout(function () { $boulder.css('top', '140px'); }, 50);
            // Check collision at bottom
            setTimeout(function () {
                var bPos = bLeft;
                if (Math.abs(bPos - playerPos) < 12) {
                    missed++;
                } else {
                    dodged++;
                    $scoreEl.text(dodged);
                }
                $boulder.remove();
            }, 1850);
        }, (AV_DURATION * 1000) / total);

        var countdown = setInterval(function () {
            timeLeft--;
            $timer.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(countdown);
                clearInterval(spawner);
                $(document).off('keydown.av' + gameId);
                $leftBtn.off('click');
                $rightBtn.off('click');
                setTimeout(function () {
                    $arena.html('<div class="text-center pt-4">' + escHtml(LANG.av_time_up) + '</div>');
                    var finalScore = Math.min(100, Math.round((dodged / total) * 100));
                    submitPlay(gameId, submitUrl, finalScore, LANG.av_score + ': ' + dodged + '/' + total);
                }, 2000);
            }
        }, 1000);
    }

    /* ================================================================
       7. LIFT LINE MANAGER – Simon-says memory game
       Watch a sequence of coloured panels light up, then repeat.
       Each successful round adds one more colour.
    ================================================================ */
    var LL_COLORS = [
        {bg: '#dc3545', name: 'red'},
        {bg: '#198754', name: 'green'},
        {bg: '#0d6efd', name: 'blue'},
        {bg: '#ffc107', name: 'yellow'}
    ];
    var LL_MAX_ROUNDS = 10;

    function buildLiftLine(gameId) {
        var html = '<div id="ll-' + gameId + '" class="ll-container text-center">'
             + '  <div id="ll-status-' + gameId + '" class="small font-bold mb-2">' + escHtml(LANG.ll_watch) + '</div>'
             + '  <div id="ll-grid-' + gameId + '" class="flex justify-center gap-2 mb-2">';
        for (var i = 0; i < LL_COLORS.length; i++) {
            html += '<div class="ll-btn" data-index="' + i + '" style="width:55px;height:55px;border-radius:8px;cursor:pointer;opacity:0.5;background:' + LL_COLORS[i].bg + ';border:2px solid #333;"></div>';
        }
        html += '  </div>'
             + '  <div class="small">' + escHtml(LANG.ll_score) + ': <strong id="ll-score-' + gameId + '">0</strong></div>'
             + '</div>';
        return html;
    }

    function startLiftLine(gameId, submitUrl) {
        var $status = $('#ll-status-' + gameId);
        var $scoreEl = $('#ll-score-' + gameId);
        var $grid   = $('#ll-grid-' + gameId);
        var $btns   = $grid.find('.ll-btn');
        var sequence = [];
        var playerIndex = 0;
        var round = 0;
        var inputEnabled = false;

        function flashBtn(idx, duration) {
            var $b = $btns.eq(idx);
            $b.css('opacity', '1');
            setTimeout(function () { $b.css('opacity', '0.5'); }, duration);
        }

        function playSequence() {
            inputEnabled = false;
            $status.text(LANG.ll_watch);
            // Add a new random colour to the sequence
            sequence.push(Math.floor(Math.random() * LL_COLORS.length));
            var delay = 0;
            for (var i = 0; i < sequence.length; i++) {
                (function (idx, d) {
                    setTimeout(function () { flashBtn(idx, 400); }, d);
                })(sequence[i], delay);
                delay += 600;
            }
            setTimeout(function () {
                $status.text(LANG.ll_go);
                playerIndex = 0;
                inputEnabled = true;
            }, delay + 200);
        }

        $btns.on('click.ll', function () {
            if (!inputEnabled) return;
            var idx = parseInt($(this).data('index'), 10);
            flashBtn(idx, 200);
            if (idx === sequence[playerIndex]) {
                playerIndex++;
                if (playerIndex >= sequence.length) {
                    // Round cleared
                    round++;
                    $scoreEl.text(round);
                    if (round >= LL_MAX_ROUNDS) {
                        // Perfect game
                        endGame();
                        return;
                    }
                    $status.html('<span class="text-success">' + LANG.ll_correct + '</span>');
                    setTimeout(function () { playSequence(); }, 800);
                }
            } else {
                // Wrong
                endGame();
            }
        });

        function endGame() {
            inputEnabled = false;
            $btns.off('click.ll').css('cursor', 'default');
            var finalScore = Math.min(100, Math.round((round / LL_MAX_ROUNDS) * 100));
            $status.html(round >= LL_MAX_ROUNDS
                ? '<span class="text-success font-bold">' + LANG.ll_correct + '</span>'
                : '<span class="text-error">' + LANG.ll_wrong + '</span>');
            setTimeout(function () {
                submitPlay(gameId, submitUrl, finalScore, LANG.ll_score + ': ' + round + '/' + LL_MAX_ROUNDS);
            }, 600);
        }

        playSequence();
    }

    /* ================================================================
       8. ICE BREAKER – Rapid-click game
       Click as fast as possible to break through layers of ice.
       Each click chips away at a progress bar.
    ================================================================ */
    var IB_DURATION  = 10; // seconds
    var IB_TARGET    = 50; // clicks needed for 100% score

    function buildIceBreaker(gameId) {
        return '<div id="ib-' + gameId + '" class="ib-container text-center">'
             + '  <div class="relative border rounded my-2" style="height:32px;background:#e0f0ff;overflow:hidden;">'
             + '    <div id="ib-bar-' + gameId + '" style="position:absolute;top:0;left:0;height:100%;width:0%;background:linear-gradient(90deg,#0dcaf0,#0d6efd);transition:width 0.1s;"></div>'
             + '    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">'
             + '      <small id="ib-pct-' + gameId + '" style="font-weight:700;color:#1e3a8a;font-size:0.8rem;">0%</small>'
             + '    </div>'
             + '  </div>'
             + '  <div class="flex justify-between small mb-2">'
             + '    <span>' + escHtml(LANG.ib_score) + ': <strong id="ib-score-' + gameId + '">0</strong></span>'
             + '    <span id="ib-timer-' + gameId + '"></span>'
             + '  </div>'
             + '  <div id="ib-ice-' + gameId + '" class="border rounded p-3 mb-2 bg-base-200" style="cursor:pointer;user-select:none;font-size:2.5rem;min-height:90px;display:flex;align-items:center;justify-content:center;">'
             + '    🧊'
             + '  </div>'
             + '</div>';
    }

    function startIceBreaker(gameId, submitUrl) {
        var $bar    = $('#ib-bar-' + gameId);
        var $pct    = $('#ib-pct-' + gameId);
        var $scoreEl = $('#ib-score-' + gameId);
        var $timer  = $('#ib-timer-' + gameId);
        var $ice    = $('#ib-ice-' + gameId);
        var clicks  = 0;
        var timeLeft = IB_DURATION;
        var running = true;

        var iceEmojis = ['🧊', '❄️', '💎', '🔷', '🔹'];

        $ice.on('click.ib', function () {
            if (!running) return;
            clicks++;
            $scoreEl.text(clicks);
            var pct = Math.min(100, Math.round((clicks / IB_TARGET) * 100));
            $bar.css('width', pct + '%');
            $pct.text(pct + '%');
            // Visual feedback
            $ice.text(iceEmojis[Math.floor(Math.random() * iceEmojis.length)]);
            $ice.css('transform', 'scale(0.9)');
            setTimeout(function () { $ice.css('transform', 'scale(1)'); }, 80);
        });

        var countdown = setInterval(function () {
            timeLeft--;
            $timer.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(countdown);
                running = false;
                $ice.off('click.ib').css('cursor', 'default');
                $ice.html('<div class="small">' + escHtml(LANG.ib_time_up) + '</div>');
                var finalScore = Math.min(100, Math.round((clicks / IB_TARGET) * 100));
                submitPlay(gameId, submitUrl, finalScore, LANG.ib_score + ': ' + clicks);
            }
        }, 1000);
    }

    /* ================================================================
       9. SLALOM RACE – Reaction / direction game
       8 gates appear one by one; each gate goes left or right.
       Player clicks the matching button; score = % correct.
    ================================================================ */
    var SL_GATES     = 8;
    var SL_GATE_TIME = 2000; // ms to respond per gate

    function buildSlalom(gameId) {
        return '<div id="sl-' + gameId + '" class="sl-container text-center">'
             + '  <div class="mb-2 small font-bold" id="sl-info-' + gameId + '">' + escHtml(LANG.sl_go) + '</div>'
             + '  <div class="border rounded p-3 mb-2" id="sl-gate-' + gameId + '" style="min-height:80px;font-size:2rem;display:flex;align-items:center;justify-content:center;">🎿</div>'
             + '  <div class="flex justify-center gap-3 mb-2">'
             + '    <button id="sl-left-' + gameId + '" class="btn btn-outline btn-primary btn-sm px-4">' + escHtml(LANG.sl_left) + '</button>'
             + '    <button id="sl-right-' + gameId + '" class="btn btn-outline btn-primary btn-sm px-4">' + escHtml(LANG.sl_right) + '</button>'
             + '  </div>'
             + '  <div class="flex justify-between small px-1">'
             + '    <span>' + escHtml(LANG.sl_score) + ': <strong id="sl-score-' + gameId + '">0</strong>/' + SL_GATES + '</span>'
             + '    <span id="sl-timer-' + gameId + '"></span>'
             + '  </div>'
             + '</div>';
    }

    function startSlalom(gameId, submitUrl) {
        var $info    = $('#sl-info-' + gameId);
        var $gate    = $('#sl-gate-' + gameId);
        var $scoreEl = $('#sl-score-' + gameId);
        var $timer   = $('#sl-timer-' + gameId);
        var $left    = $('#sl-left-' + gameId);
        var $right   = $('#sl-right-' + gameId);

        var gateIndex  = 0;
        var correct    = 0;
        var answered   = false;
        var gateDir    = null;
        var gateTimer  = null;

        function nextGate() {
            if (gateIndex >= SL_GATES) {
                finish();
                return;
            }
            answered  = false;
            gateDir   = Math.random() < 0.5 ? 'left' : 'right';
            var arrow = gateDir === 'left' ? '← 🚩' : '🚩 →';
            $gate.html('<span style="font-size:2.2rem;">' + arrow + '</span>');
            $info.html(escHtml(LANG.sl_gate) + ' ' + (gateIndex + 1) + '/' + SL_GATES);

            var timeLeft = Math.ceil(SL_GATE_TIME / 1000);
            $timer.text(timeLeft + 's');
            var timerInterval = setInterval(function () {
                timeLeft--;
                $timer.text(timeLeft > 0 ? timeLeft + 's' : '');
            }, 1000);

            gateTimer = setTimeout(function () {
                clearInterval(timerInterval);
                if (!answered) {
                    $info.html('<span class="text-error">' + escHtml(LANG.sl_miss) + '</span>');
                    gateIndex++;
                    setTimeout(nextGate, 500);
                }
            }, SL_GATE_TIME);

            $left.off('click.sl').on('click.sl', function () {
                if (answered) return;
                clearTimeout(gateTimer);
                clearInterval(timerInterval);
                answered = true;
                if (gateDir === 'left') {
                    correct++;
                    $scoreEl.text(correct);
                    $info.html('<span class="text-success">' + escHtml(LANG.sl_correct) + '</span>');
                } else {
                    $info.html('<span class="text-error">' + escHtml(LANG.sl_miss) + '</span>');
                }
                gateIndex++;
                setTimeout(nextGate, 500);
            });
            $right.off('click.sl').on('click.sl', function () {
                if (answered) return;
                clearTimeout(gateTimer);
                clearInterval(timerInterval);
                answered = true;
                if (gateDir === 'right') {
                    correct++;
                    $scoreEl.text(correct);
                    $info.html('<span class="text-success">' + escHtml(LANG.sl_correct) + '</span>');
                } else {
                    $info.html('<span class="text-error">' + escHtml(LANG.sl_miss) + '</span>');
                }
                gateIndex++;
                setTimeout(nextGate, 500);
            });
        }

        function finish() {
            $left.off('click.sl').prop('disabled', true);
            $right.off('click.sl').prop('disabled', true);
            $gate.html('<span style="font-size:2rem;">🏁</span>');
            $info.html('<strong>' + escHtml(LANG.sl_done) + '</strong>');
            $timer.text('');
            var finalScore = Math.round((correct / SL_GATES) * 100);
            submitPlay(gameId, submitUrl, finalScore, LANG.sl_score + ': ' + correct + '/' + SL_GATES);
        }

        nextGate();
    }

    /* ================================================================
       10. SKI PATROL RUSH – Click injured skiers before they vanish
       12 injured skiers appear at random positions over 15 seconds.
       Click each one before it fades to earn a rescue point.
       Score = (rescued / PT_TOTAL) * 100. Win threshold: 40.
    ================================================================ */
    var PT_DURATION = 15; // seconds
    var PT_TOTAL    = 12; // skiers to rescue
    var PT_VISIBLE  = 1800; // ms each skier stays visible

    function buildPatrol(gameId) {
        return '<div id="pt-' + gameId + '" class="pt-container">'
             + '  <div id="pt-arena-' + gameId + '" class="border rounded relative bg-base-200" style="height:160px;overflow:hidden;">'
             + '    <div class="text-center text-base-content/60 pt-5">' + escHtml(LANG.pt_start) + '</div>'
             + '  </div>'
             + '  <div class="flex justify-between mt-1">'
             + '    <span class="small">' + escHtml(LANG.pt_score) + ': <strong id="pt-score-' + gameId + '">0</strong>/' + PT_TOTAL + '</span>'
             + '    <span id="pt-timer-' + gameId + '" class="small"></span>'
             + '  </div>'
             + '</div>';
    }

    function startPatrol(gameId, submitUrl) {
        var $arena   = $('#pt-arena-' + gameId);
        var $scoreEl = $('#pt-score-' + gameId);
        var $timer   = $('#pt-timer-' + gameId);
        var rescued  = 0;
        var spawned  = 0;
        var timeLeft = PT_DURATION;
        $arena.empty();

        var spawner = setInterval(function () {
            if (spawned >= PT_TOTAL || timeLeft <= 0) {
                clearInterval(spawner);
                return;
            }
            spawned++;
            var left = Math.floor(Math.random() * 80) + 5;
            var top  = Math.floor(Math.random() * 65) + 5;
            var $skier = $('<div style="position:absolute;left:' + left + '%;top:' + top + '%;cursor:pointer;font-size:1.8rem;user-select:none;transition:opacity 0.3s;" title="rescue">🤕</div>');
            $skier.on('click', function () {
                if ($skier.data('rescued')) { return; }
                $skier.data('rescued', true);
                $skier.text('🚑').css('cursor', 'default');
                rescued++;
                $scoreEl.text(rescued);
                setTimeout(function () { $skier.remove(); }, 300);
            });
            $arena.append($skier);
            // Auto-remove after visible window
            setTimeout(function () {
                if (!$skier.data('rescued')) {
                    $skier.css('opacity', '0');
                    setTimeout(function () { $skier.remove(); }, 300);
                }
            }, PT_VISIBLE);
        }, (PT_DURATION * 1000) / (PT_TOTAL + 1));

        var countdown = setInterval(function () {
            timeLeft--;
            $timer.text(timeLeft + 's');
            if (timeLeft <= 0) {
                clearInterval(countdown);
                clearInterval(spawner);
                $arena.find('div').off('click').css('cursor', 'default');
                setTimeout(function () {
                    $arena.html('<div class="text-center pt-4">' + escHtml(LANG.pt_time_up) + '</div>');
                    var finalScore = Math.min(100, Math.round((rescued / PT_TOTAL) * 100));
                    submitPlay(gameId, submitUrl, finalScore, LANG.pt_score + ': ' + rescued + '/' + PT_TOTAL);
                }, 500);
            }
        }, 1000);
    }

    /* ================================================================
       11. FREESTYLE JUMP – Timing precision game
       A progress bar fills from 0→100 over ~2.5 s.
       Player must click "Jump!" when the bar is inside the green zone
       (65–90 %). Perfect (78–87 %) = score 100, good (65–90 %) = score 75,
       otherwise = score proportional to distance (0–49).
       Win threshold: 50.
    ================================================================ */
    function buildFreestyle(gameId) {
        return '<div id="fs-' + gameId + '" class="fs-container text-center">'
             + '  <p class="small mb-1">' + escHtml(LANG.fs_start) + '</p>'
             + '  <div class="relative mb-2" style="height:28px;background:#e9ecef;border-radius:4px;overflow:hidden;">'
             + '    <div id="fs-bar-' + gameId + '" style="height:100%;width:0%;background:#0d6efd;transition:none;"></div>'
             + '    <div style="position:absolute;top:0;left:65%;width:25%;height:100%;background:rgba(25,135,84,0.35);pointer-events:none;" title="target zone"></div>'
             + '  </div>'
             + '  <div id="fs-feedback-' + gameId + '" class="small mb-2" style="min-height:1.2em;"></div>'
             + '  <button class="btn btn-warning btn-sm" id="fs-btn-' + gameId + '" disabled>'
             + '    🎿 ' + escHtml(LANG.fs_jump)
             + '  </button>'
             + '</div>';
    }

    function startFreestyle(gameId, submitUrl) {
        var $bar      = $('#fs-bar-' + gameId);
        var $feedback = $('#fs-feedback-' + gameId);
        var $btn      = $('#fs-btn-' + gameId);
        var progress  = 0;           // 0–100
        var duration  = 2500;        // ms for full sweep
        var interval  = 30;          // ms per tick
        var step      = (100 / (duration / interval));
        var jumped    = false;

        $btn.prop('disabled', false);

        var ticker = setInterval(function () {
            if (jumped) { clearInterval(ticker); return; }
            progress = Math.min(100, progress + step);
            $bar.css('width', progress + '%');
            // Auto-fail if bar runs out
            if (progress >= 100) {
                clearInterval(ticker);
                if (!jumped) {
                    jumped = true;
                    $btn.prop('disabled', true);
                    $feedback.html('<span class="text-error">' + escHtml(LANG.fs_late) + '</span>');
                    submitPlay(gameId, submitUrl, 10);
                }
            }
        }, interval);

        $btn.off('click.fs').on('click.fs', function () {
            if (jumped) return;
            jumped = true;
            clearInterval(ticker);
            $btn.prop('disabled', true);

            var score;
            var msg;
            if (progress >= 78 && progress <= 87) {
                score = 100;
                msg = '<span class="text-success font-bold">' + escHtml(LANG.fs_perfect) + '</span>';
            } else if (progress >= 65 && progress <= 90) {
                score = 75;
                msg = '<span class="text-success">' + escHtml(LANG.fs_good) + '</span>';
            } else if (progress < 65) {
                score = Math.max(0, Math.round(progress * 0.6));
                msg = '<span class="text-error">' + escHtml(LANG.fs_early) + '</span>';
            } else {
                score = Math.max(0, Math.round((100 - progress) * 0.6));
                msg = '<span class="text-error">' + escHtml(LANG.fs_late) + '</span>';
            }
            $feedback.html(msg);
            submitPlay(gameId, submitUrl, score, LANG.fs_score + ': ' + Math.round(progress) + '%');
        });
    }

    /* ================================================================
       12. BIATHLON – moving-target shooting game
       An oscillating marker moves left-right.
       Click "Shoot!" when marker is in the centre zone (42–58 %).
       5 shots total. Win threshold: 40 % (2 hits).
    ================================================================ */
    function buildBiathlon(gameId) {
        var html = '<div style="text-align:center;">';
        html += '<div style="position:relative;width:100%;max-width:320px;height:80px;border:2px solid #888;border-radius:6px;overflow:hidden;background:#d9ead3;margin:0 auto 8px;">';
        // green target zone in the centre
        html += '<div style="position:absolute;left:42%;top:0;width:16%;height:100%;background:rgba(40,167,69,0.35);pointer-events:none;"></div>';
        // centre crosshair marker
        html += '<div id="bt-mark-' + gameId + '" style="position:absolute;top:15%;font-size:1.8rem;line-height:1;transition:none;">🎯</div>';
        html += '</div>';
        html += '<div id="bt-info-' + gameId + '" class="mb-2 font-bold small"></div>';
        html += '<button id="bt-shoot-' + gameId + '" class="btn btn-error btn-sm">🔫 ' + escHtml(LANG.bt_shoot) + '</button>';
        html += '</div>';
        return html;
    }

    function startBiathlon(gameId, submitUrl) {
        var SHOTS   = 5;
        var hits    = 0;
        var shot    = 0;
        var pos     = 10; // 0-90 % left
        var dir     = 1;
        var speed   = 1.8;
        var running = true;
        var lastTs  = null;
        var raf;

        function updateInfo() {
            $('#bt-info-' + gameId).text(
                LANG.bt_shot + ' ' + (shot + 1) + '/' + SHOTS + '  –  ' + LANG.bt_score + ': ' + hits
            );
        }

        function tick(ts) {
            if (!lastTs) lastTs = ts;
            var delta = (ts - lastTs) / 16.67;
            lastTs = ts;
            pos += dir * speed * delta;
            if (pos >= 88) { pos = 88; dir = -1; }
            if (pos <= 2)  { pos = 2;  dir =  1; }
            $('#bt-mark-' + gameId).css('left', pos + '%');
            if (running) raf = requestAnimationFrame(tick);
        }

        updateInfo();
        raf = requestAnimationFrame(tick);

        $('#bt-shoot-' + gameId).on('click', function () {
            if (shot >= SHOTS) { return; }
            var inZone = (pos >= 42 && pos <= 58);
            if (inZone) {
                hits++;
                $(this).text('🎯 ' + LANG.bt_hit);
            } else {
                $(this).text('💨 ' + LANG.bt_miss);
            }
            shot++;
            updateInfo();
            if (shot >= SHOTS) {
                running = false;
                cancelAnimationFrame(raf);
                $(this).prop('disabled', true);
                var score = Math.round((hits / SHOTS) * 100);
                submitPlay(gameId, submitUrl, score, LANG.bt_done + ' ' + LANG.bt_score + ': ' + hits + '/' + SHOTS);
            } else {
                var $btn = $(this);
                setTimeout(function () { $btn.text('🔫 ' + LANG.bt_shoot); }, 600);
            }
        });
    }

    /* ================================================================
       13. SNOWBOARD TRICK – multi-attempt timing game
       A progress bar fills 0 → 100 over ~2.5 s across 3 attempts.
       Click "Trick!" when bar is in green zone (60–85 %).
       Win threshold: 50 % (land at least 2/3 tricks).
    ================================================================ */
    var SB_TRICKS = 3;

    function buildSnowboard(gameId) {
        var html = '<div style="text-align:center;">';
        html += '<div id="sb-run-' + gameId + '" class="mb-1 font-bold small"></div>';
        html += '<div style="background:#e0e0e0;border-radius:4px;height:22px;width:100%;position:relative;overflow:hidden;margin-bottom:6px;">';
        html += '<div id="sb-bar-' + gameId + '" style="height:100%;width:0%;background:#0d6efd;"></div>';
        // green zone overlay
        html += '<div style="position:absolute;left:60%;top:0;width:25%;height:100%;background:rgba(40,167,69,0.4);pointer-events:none;"></div>';
        html += '</div>';
        html += '<button id="sb-btn-' + gameId + '" class="btn btn-success btn-sm" disabled>' + escHtml(LANG.sb_start) + '</button>';
        html += '<div id="sb-feedback-' + gameId + '" class="mt-1 small"></div>';
        html += '</div>';
        return html;
    }

    function startSnowboard(gameId, submitUrl) {
        var current = 0; // 0-based trick index
        var hits    = 0; // tricks landed in zone
        var pct     = 0;
        var speed   = 1.8;
        var active  = false;
        var lastTs  = null;
        var raf;

        function updateHeader() {
            $('#sb-run-' + gameId).text(LANG.sb_run + ' ' + (current + 1) + '/' + SB_TRICKS);
        }

        function endTrick(landed) {
            active = false;
            cancelAnimationFrame(raf);
            if (landed) { hits++; }
            current++;
            if (current >= SB_TRICKS) {
                $('#sb-btn-' + gameId).prop('disabled', true);
                var score = Math.round((hits / SB_TRICKS) * 100);
                setTimeout(function () {
                    submitPlay(gameId, submitUrl, score, LANG.sb_done + ' ' + LANG.sb_score + ': ' + hits + '/' + SB_TRICKS);
                }, 700);
            } else {
                setTimeout(launchTrick, 800);
            }
        }

        function tick(ts) {
            if (!lastTs) lastTs = ts;
            var delta = (ts - lastTs) / 16.67;
            lastTs = ts;
            pct = Math.min(100, pct + speed * delta);
            $('#sb-bar-' + gameId).css('width', pct + '%');
            if (pct >= 100) {
                $('#sb-btn-' + gameId).prop('disabled', true);
                $('#sb-feedback-' + gameId).html('<span class="text-error">' + escHtml(LANG.sb_late) + '</span>');
                endTrick(false);
                return;
            }
            if (active) { raf = requestAnimationFrame(tick); }
        }

        function launchTrick() {
            updateHeader();
            pct = 0;
            active = true;
            lastTs = null;
            $('#sb-bar-' + gameId).css('width', '0%');
            $('#sb-feedback-' + gameId).text('');
            $('#sb-btn-' + gameId).prop('disabled', false).text('🏂 ' + LANG.sb_trick);
            raf = requestAnimationFrame(tick);
        }

        $('#sb-btn-' + gameId).on('click', function () {
            if (!active) { return; }
            var feedback;
            var landed;
            if (pct < 60) {
                feedback = '<span class="text-error">' + escHtml(LANG.sb_early) + '</span>';
                landed   = false;
            } else if (pct <= 85) {
                feedback = pct >= 72
                    ? '<span class="text-success font-bold">' + escHtml(LANG.sb_perfect) + '</span>'
                    : '<span class="text-success">' + escHtml(LANG.sb_good) + '</span>';
                landed = true;
            } else {
                feedback = '<span class="text-error">' + escHtml(LANG.sb_late) + '</span>';
                landed   = false;
            }
            $(this).prop('disabled', true);
            $('#sb-feedback-' + gameId).html(feedback);
            endTrick(landed);
        });

        launchTrick();
    }

    /* ================================================================
       Shared: submit play to server & show result
    ================================================================ */
    function submitPlay(gameId, submitUrl, score, extraMsg) {
        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: { id_minigame: gameId, score: score },
            dataType: 'json',
            success: function (res) {
                showResult(gameId, res, extraMsg);
            },
            error: function () {
                $('#result-' + gameId).html('<div class="alert alert-error mt-2">' + LANG.submit_error + '</div>');
            }
        });
    }

    function showResult(gameId, res, extraMsg) {
        if (!res.success) {
            var msg = res.message === 'on_cooldown' ? LANG.cooldown_msg : LANG.submit_error;
            $('#result-' + gameId).html('<div class="alert alert-warning mt-2">' + msg + '</div>');
            return;
        }

        var html = '';
        if (res.result === 'win') {
            html = '<div class="alert alert-success mt-2"><strong>🎉 ' + LANG.win + '!</strong>';
            if (res.reward_cash > 0)  html += ' ' + LANG.cash_earned + ': <strong>+' + formatNum(res.reward_cash) + '€</strong>';
            if (res.reward_rep  > 0)  html += ' ' + LANG.rep_earned  + ': <strong>+' + res.reward_rep + '</strong>';
            if (res.streak_bonus)     html += '<br><small class="text-warning font-bold">' + escHtml(LANG.streak_bonus) + '</small>';
            if (extraMsg) html += '<br><small>' + escHtml(extraMsg) + '</small>';
            html += '</div>';
        } else {
            html = '<div class="alert alert-secondary mt-2"><strong>' + LANG.lose + '</strong>';
            if (extraMsg) html += '<br><small>' + escHtml(extraMsg) + '</small>';
            html += '</div>';
        }
        $('#result-' + gameId).html(html);

        // Disable the play button and mark cooldown
        var $card = $('#card-game-' + gameId);
        $card.find('.minigame-play-btn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary').html('<i class="fa-regular fa-hourglass mr-1"></i>' + LANG.play_again);
        $card.find('.badge').removeClass('badge-success').addClass('badge-neutral').text(LANG.play_again);
    }

    /* ================================================================
       Boot: attach click handlers
    ================================================================ */
    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function formatNum(n) {
        return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '\u202f');
    }

    $(document).on('click', '.minigame-play-btn', function () {
        var gameId    = $(this).data('game-id');
        var gameType  = $(this).data('type');
        var submitUrl = $(this).data('submit-url');
        var $area     = $('#game-area-' + gameId);

        $(this).prop('disabled', true);
        $area.show();
        $('#result-' + gameId).empty();

        if (gameType === 'luck') {
            $area.html(buildSlotMachine(gameId));
            $('#slot-spin-' + gameId).on('click', function () {
                spinSlot(gameId, submitUrl);
            });
        } else if (gameType === 'quiz') {
            $area.html(buildQuiz(gameId));
            $('#quiz-submit-' + gameId).on('click', function () {
                $(this).prop('disabled', true);
                gradeQuiz(gameId, submitUrl);
            });
        } else if (gameType === 'skill') {
            $area.html(buildRush(gameId));
            $('#rush-start-' + gameId).on('click', function () {
                startRush(gameId, submitUrl);
            });
        } else if (gameType === 'snowmaking') {
            $area.html(buildSnowmaking(gameId));
            startSnowmaking(gameId, submitUrl);
        } else if (gameType === 'grooming') {
            $area.html(buildGrooming(gameId));
            $('#groom-start-' + gameId).on('click', function () {
                startGrooming(gameId, submitUrl);
            });
        } else if (gameType === 'avalanche') {
            $area.html(buildAvalanche(gameId));
            startAvalanche(gameId, submitUrl);
        } else if (gameType === 'liftline') {
            $area.html(buildLiftLine(gameId));
            startLiftLine(gameId, submitUrl);
        } else if (gameType === 'icebreaker') {
            $area.html(buildIceBreaker(gameId));
            startIceBreaker(gameId, submitUrl);
        } else if (gameType === 'slalom') {
            $area.html(buildSlalom(gameId));
            startSlalom(gameId, submitUrl);
        } else if (gameType === 'patrol') {
            $area.html(buildPatrol(gameId));
            startPatrol(gameId, submitUrl);
        } else if (gameType === 'freestyle') {
            $area.html(buildFreestyle(gameId));
            startFreestyle(gameId, submitUrl);
        } else if (gameType === 'biathlon') {
            $area.html(buildBiathlon(gameId));
            startBiathlon(gameId, submitUrl);
        } else if (gameType === 'snowboard') {
            $area.html(buildSnowboard(gameId));
            startSnowboard(gameId, submitUrl);
        }
    });

}(jQuery));
});
</script>
