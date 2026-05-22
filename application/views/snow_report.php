<?php
/**
 * Snow Report view
 * Template: templates/default
 */

$L = $lang; // passed from controller
?>

<div class="w-full">

    <?php echo $title; ?>
    <?php echo $intro; ?>

    <?php if ($publish_success !== NULL): ?>
        <div class="alert alert-success" role="alert">
            <i class="fa-solid fa-circle-check mr-1"></i>
            <?php
            if ((int)$publish_success > 0) {
                echo htmlspecialchars(sprintf($L['publish_success_bonus'], (int)$publish_success), ENT_QUOTES, 'UTF-8');
            } else {
                echo htmlspecialchars($L['publish_success_no_bonus'], ENT_QUOTES, 'UTF-8');
            }
            ?>
        </div>
    <?php endif; ?>

    <?php if ($publish_error): ?>
        <div class="alert alert-error" role="alert">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            <?php echo htmlspecialchars($publish_error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-3">

        <!-- ===== Publish form ===== -->
        <div class="col-span-12 lg:col-span-6">
            <div class="card h-full">
                <div class="card-header">
                    <h5 class="h5 mb-0">
                        <i class="fa-solid fa-snowflake mr-2"></i>
                        <?php echo htmlspecialchars($L['publish_heading'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                </div>
                <div class="card-body">

                    <?php if ($today_report): ?>
                        <div class="alert alert-info">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            <?php echo htmlspecialchars($L['already_published_today'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php else: ?>
                        <?php echo form_open('snow_report_controller/publish'); ?>

                        <!-- Snow depth -->
                        <div class="mb-3">
                            <label for="snow_depth_cm" class="label">
                                <?php echo htmlspecialchars($L['snow_depth_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <input type="number" class="input" id="snow_depth_cm" name="snow_depth_cm"
                                   min="0" max="500" value="<?php echo set_value('snow_depth_cm', $game_snow_level); ?>" required>
                        </div>

                        <!-- Fresh snow -->
                        <div class="mb-3">
                            <label for="fresh_snow_cm" class="label">
                                <?php echo htmlspecialchars($L['fresh_snow_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <input type="number" class="input" id="fresh_snow_cm" name="fresh_snow_cm"
                                   min="0" max="200" value="<?php echo set_value('fresh_snow_cm', $game_fresh_snow); ?>">
                        </div>

                        <!-- Overall conditions -->
                        <div class="mb-3">
                            <label for="conditions" class="label">
                                <?php echo htmlspecialchars($L['conditions_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <select class="select" id="conditions" name="conditions" required>
                                <?php
                                $cond_options = ['poor', 'fair', 'good', 'excellent'];
                                foreach ($cond_options as $opt):
                                    $key    = 'conditions_' . $opt;
                                    $label  = htmlspecialchars($L[$key] ?? ucfirst($opt), ENT_QUOTES, 'UTF-8');
                                    $bonus  = htmlspecialchars($L['rep_bonus_' . $opt] ?? '', ENT_QUOTES, 'UTF-8');
                                    $sel    = (set_value('conditions') === $opt) ? ' selected' : ($opt === 'good' ? ' selected' : '');
                                ?>
                                    <option value="<?php echo $opt; ?>"<?php echo $sel; ?>>
                                        <?php echo $label; ?> — <?php echo $bonus; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Piste coverage -->
                        <div class="mb-3">
                            <label for="piste_coverage" class="label">
                                <?php echo htmlspecialchars($L['coverage_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <input type="number" class="input" id="piste_coverage" name="piste_coverage"
                                   min="0" max="100" value="<?php echo set_value('piste_coverage', 100); ?>" required>
                        </div>

                        <!-- Note -->
                        <div class="mb-3">
                            <label for="note" class="label">
                                <?php echo htmlspecialchars($L['note_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <textarea class="input" id="note" name="note" rows="3" maxlength="500"
                                      placeholder="<?php echo htmlspecialchars($L['note_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo set_value('note'); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane mr-1"></i>
                            <?php echo htmlspecialchars($L['publish_button'], ENT_QUOTES, 'UTF-8'); ?>
                        </button>

                        <?php echo form_close(); ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- ===== Tips, game data & bonuses ===== -->
        <div class="col-span-12 lg:col-span-6">

            <!-- Current game data panel -->
            <div class="card mb-3 border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h5 class="h5 mb-0">
                        <i class="fa-solid fa-database mr-2"></i>
                        <?php echo htmlspecialchars($L['game_data_heading'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="grid gap-2">
                        <div class="col-span-6">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">❄️</span>
                                <div>
                                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['game_snow_level_label'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="font-semibold"><?php echo (int)$game_snow_level; ?> cm</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-6">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌨</span>
                                <div>
                                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['game_fresh_snow_label'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="font-semibold">+<?php echo (int)$game_fresh_snow; ?> cm</div>
                                </div>
                            </div>
                        </div>
                        <?php if ($game_weather): ?>
                        <div class="col-span-6">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌡️</span>
                                <div>
                                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['game_temperature_label'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="font-semibold"><?php echo (int)$game_weather->temperature; ?>°C</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-6">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌬️</span>
                                <div>
                                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['game_weather_label'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="font-semibold">
                                        <?php echo htmlspecialchars($game_weather_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="mb-0 mt-2 small text-base-content/60">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        <?php echo htmlspecialchars($L['game_data_hint'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="h5 mb-0">
                        <i class="fa-solid fa-lightbulb mr-2"></i>
                        <?php echo htmlspecialchars($L['tips_heading'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li class="mb-1"><?php echo htmlspecialchars($L['tip_1'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li class="mb-1"><?php echo htmlspecialchars($L['tip_2'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?php echo htmlspecialchars($L['tip_3'], ENT_QUOTES, 'UTF-8'); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Latest report summary -->
            <?php if ($latest_report): ?>
                <div class="card">
                    <div class="card-header">
                        <h6 class="h6 mb-0">
                            <i class="fa-regular fa-calendar-check mr-1"></i>
                            <?php echo htmlspecialchars($L['latest_heading'], ENT_QUOTES, 'UTF-8'); ?>
                            <small class="text-base-content/60 ml-2"><?php echo htmlspecialchars($latest_report->report_date, ENT_QUOTES, 'UTF-8'); ?></small>
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <?php
                        $cond_key     = 'conditions_' . $latest_report->conditions;
                        $badge_colors = ['poor' => 'error', 'fair' => 'warning', 'good' => 'info', 'excellent' => 'success'];
                        $badge_class  = $badge_colors[$latest_report->conditions] ?? 'neutral';
                        ?>
                        <div class="flex flex-wrap gap-2 items-center mb-2">
                            <span class="badge badge-<?php echo $badge_class; ?> text-base">
                                <?php echo htmlspecialchars($L[$cond_key] ?? ucfirst($latest_report->conditions), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <small>❄️ <?php echo (int)$latest_report->snow_depth_cm; ?> cm</small>
                            <small>🌨 +<?php echo (int)$latest_report->fresh_snow_cm; ?> cm</small>
                            <small>🎿 <?php echo (int)$latest_report->piste_coverage; ?>%</small>
                        </div>
                        <?php if ($latest_report->note): ?>
                            <p class="mb-0 text-base-content/60 small fst-italic">
                                "<?php echo htmlspecialchars($latest_report->note, ENT_QUOTES, 'UTF-8'); ?>"
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- .row -->

    <!-- ===== Report history table ===== -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="h5 mb-0">
                <i class="fa-regular fa-clock-history mr-2"></i>
                <?php echo htmlspecialchars($L['history_heading'], ENT_QUOTES, 'UTF-8'); ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($history->num_rows() === 0): ?>
                <p class="text-base-content/60 mb-0">
                    <?php echo htmlspecialchars($L['no_reports_yet'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table table-sm align-middle">
                        <thead class="">
                            <tr>
                                <th><?php echo htmlspecialchars($L['date_col'],       ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['depth_col'],      ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['fresh_col'],      ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['conditions_col'], ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['coverage_col'],   ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['rep_bonus_col'],  ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['note_col'],       ENT_QUOTES, 'UTF-8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history->result() as $row):
                                $cond_key    = 'conditions_' . $row->conditions;
                                $badge_colors = ['poor' => 'error', 'fair' => 'warning', 'good' => 'info', 'excellent' => 'success'];
                                $badge_class  = $badge_colors[$row->conditions] ?? 'neutral';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row->report_date, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$row->snow_depth_cm; ?></td>
                                <td><?php echo (int)$row->fresh_snow_cm; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars($L[$cond_key] ?? ucfirst($row->conditions), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo (int)$row->piste_coverage; ?>%</td>
                                <td>
                                    <?php if ((int)$row->rep_bonus > 0): ?>
                                        <span class="text-success">+<?php echo (int)$row->rep_bonus; ?></span>
                                    <?php else: ?>
                                        <span class="text-base-content/60">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-base-content/60">
                                        <?php echo $row->note ? htmlspecialchars(mb_strimwidth($row->note, 0, 60, '…'), ENT_QUOTES, 'UTF-8') : '—'; ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- .w-full -->

