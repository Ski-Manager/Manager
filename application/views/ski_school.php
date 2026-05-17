<?php
/**
 * Ski School view
 * Template: templates/default
 */

$L        = $lang;
$name_col = $name_col ?? 'name_english';
$desc_col = $desc_col ?? 'description_english';
$skill_labels = [
    'beginner'     => htmlspecialchars($L['skill_beginner']     ?? 'Beginner',     ENT_QUOTES, 'UTF-8'),
    'intermediate' => htmlspecialchars($L['skill_intermediate'] ?? 'Intermediate', ENT_QUOTES, 'UTF-8'),
    'advanced'     => htmlspecialchars($L['skill_advanced']     ?? 'Advanced',     ENT_QUOTES, 'UTF-8'),
];
$skill_badge = [
    'beginner'     => 'success',
    'intermediate' => 'info',
    'advanced'     => 'warning',
];
?>

<div class="w-full">

    <?php echo $title; ?>
    <?php echo $intro; ?>

    <?php if ($session_success !== NULL && is_array($session_success)): ?>
        <div class="alert alert-success" role="alert">
            <i class="fa-solid fa-circle-check mr-1"></i>
            <?php echo htmlspecialchars(
                sprintf($L['session_success'],
                    number_format((int)$session_success['revenue']),
                    (int)$session_success['rep_earned']
                ),
                ENT_QUOTES, 'UTF-8'
            ); ?>
        </div>
    <?php endif; ?>

    <?php if ($session_error): ?>
        <div class="alert alert-error" role="alert">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            <?php echo htmlspecialchars($session_error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- ===== Summary cards ===== -->
    <?php if ($totals): ?>
    <div class="grid gap-3 mb-4">
        <div class="col-span-6 md:col-span-3">
            <div class="card text-center h-full">
                <div class="card-body py-2">
                    <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$totals->total_sessions); ?></div>
                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['total_sessions'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
        <div class="col-span-6 md:col-span-3">
            <div class="card text-center h-full">
                <div class="card-body py-2">
                    <div class="text-2xl font-bold text-success"><?php echo number_format((int)$totals->total_revenue); ?> €</div>
                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['total_revenue'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
        <div class="col-span-6 md:col-span-3">
            <div class="card text-center h-full">
                <div class="card-body py-2">
                    <div class="text-2xl font-bold text-info"><?php echo number_format((int)$totals->total_rep); ?></div>
                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['total_rep'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
        <div class="col-span-6 md:col-span-3">
            <div class="card text-center h-full">
                <div class="card-body py-2">
                    <div class="text-2xl font-bold <?php echo $can_run_session ? 'text-warning' : 'text-error'; ?>">
                        <?php echo $sessions_today; ?> / <?php echo $max_per_day; ?>
                    </div>
                    <div class="small text-base-content/60"><?php echo htmlspecialchars($L['sessions_today'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid gap-3">

        <!-- ===== Run session form ===== -->
        <div class="col-span-12 lg:col-span-7">
            <div class="card h-full">
                <div class="card-header">
                    <h5 class="h5 mb-0">
                        <i class="fa-solid fa-user-video2 mr-2"></i>
                        <?php echo htmlspecialchars($L['run_session_heading'], ENT_QUOTES, 'UTF-8'); ?>
                    </h5>
                </div>
                <div class="card-body">

                    <?php if (!$can_run_session): ?>
                        <div class="alert alert-warning">
                            <i class="fa-regular fa-calendar-xmark mr-1"></i>
                            <?php echo htmlspecialchars(
                                sprintf($L['max_sessions_reached'], $max_per_day),
                                ENT_QUOTES, 'UTF-8'
                            ); ?>
                        </div>
                    <?php else: ?>
                        <?php echo form_open('ski_school_controller/run_session'); ?>

                        <!-- Lesson type picker -->
                        <div class="mb-3">
                            <label for="id_lesson_type" class="label">
                                <?php echo htmlspecialchars($L['lesson_type_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <select class="select" id="id_lesson_type" name="id_lesson_type" required
                                    onchange="updateGuestsMax(this)">
                                <?php foreach ($lesson_types->result() as $lt):
                                    $lt_name = htmlspecialchars($lt->$name_col ?? $lt->name_english, ENT_QUOTES, 'UTF-8');
                                    $lt_level = $skill_labels[$lt->skill_level] ?? ucfirst($lt->skill_level);
                                    $net = ((int)$lt->max_guests_per_session * (int)$lt->price_per_guest) - (int)$lt->instructor_cost;
                                ?>
                                    <option value="<?php echo (int)$lt->id_lesson_type; ?>"
                                            data-max="<?php echo (int)$lt->max_guests_per_session; ?>">
                                        <?php echo $lt_name; ?> (<?php echo $lt_level; ?>) — max <?php echo (int)$lt->max_guests_per_session; ?> guests
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Guests enrolled -->
                        <div class="mb-3">
                            <label for="guests_enrolled" class="label">
                                <?php echo htmlspecialchars($L['guests_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                            <input type="number" class="input" id="guests_enrolled" name="guests_enrolled"
                                   min="1" max="10" value="<?php echo set_value('guests_enrolled', 5); ?>" required>
                            <div class="text-sm opacity-60" id="guests_hint"></div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fa-regular fa-circle-play mr-1"></i>
                            <?php echo htmlspecialchars($L['run_button'], ENT_QUOTES, 'UTF-8'); ?>
                        </button>

                        <?php echo form_close(); ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- ===== Lesson type cards & tips ===== -->
        <div class="col-span-12 lg:col-span-5">

            <!-- Tips -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="h6 mb-0">
                        <i class="fa-solid fa-lightbulb mr-1"></i>
                        <?php echo htmlspecialchars($L['tips_heading'], ENT_QUOTES, 'UTF-8'); ?>
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li class="mb-1"><?php echo htmlspecialchars($L['tip_1'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li class="mb-1"><?php echo htmlspecialchars($L['tip_2'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li class="mb-1"><?php echo htmlspecialchars($L['tip_3'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?php echo htmlspecialchars($L['tip_4'], ENT_QUOTES, 'UTF-8'); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Lesson type overview table -->
            <div class="card">
                <div class="card-header">
                    <h6 class="h6 mb-0"><i class="fa-solid fa-table mr-1"></i> Lessons</h6>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-sm mb-0">
                            <thead class="">
                                <tr>
                                    <th><?php echo htmlspecialchars($L['lesson_col'] ?? 'Lesson', ENT_QUOTES, 'UTF-8'); ?></th>
                                    <th><?php echo htmlspecialchars($L['level_col'], ENT_QUOTES, 'UTF-8'); ?></th>
                                    <th><?php echo htmlspecialchars($L['price_per_guest'], ENT_QUOTES, 'UTF-8'); ?></th>
                                    <th><?php echo htmlspecialchars($L['rep_bonus_label'], ENT_QUOTES, 'UTF-8'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lesson_types->result() as $lt):
                                    $lt_name  = htmlspecialchars($lt->$name_col ?? $lt->name_english, ENT_QUOTES, 'UTF-8');
                                    $bcolor   = $skill_badge[$lt->skill_level] ?? 'neutral';
                                    $lt_level = $skill_labels[$lt->skill_level] ?? ucfirst($lt->skill_level);
                                ?>
                                <tr>
                                    <td><small><?php echo $lt_name; ?></small></td>
                                    <td><span class="badge badge-<?php echo $bcolor; ?>"><?php echo $lt_level; ?></span></td>
                                    <td><small><?php echo number_format((int)$lt->price_per_guest); ?> €</small></td>
                                    <td><small class="text-info">+<?php echo (int)$lt->rep_bonus; ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div><!-- .row -->

    <!-- ===== Session history ===== -->
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
                    <?php echo htmlspecialchars($L['no_history'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table table-sm align-middle">
                        <thead class="">
                            <tr>
                                <th><?php echo htmlspecialchars($L['date_col'],    ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['lesson_col'],  ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['level_col'],   ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['guests_col'],  ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['revenue_col'], ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($L['rep_col'],     ENT_QUOTES, 'UTF-8'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history->result() as $row):
                                $lt_name = htmlspecialchars(
                                    ($name_col === 'name_french' ? $row->name_french : null) ?? $row->name_english ?? '—',
                                    ENT_QUOTES, 'UTF-8'
                                );
                                $bcolor  = $skill_badge[$row->skill_level] ?? 'neutral';
                                $lt_level = $skill_labels[$row->skill_level] ?? ucfirst($row->skill_level ?? '');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row->session_date, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><small><?php echo $lt_name; ?></small></td>
                                <td><span class="badge badge-<?php echo $bcolor; ?>"><?php echo $lt_level; ?></span></td>
                                <td><?php echo (int)$row->guests_enrolled; ?></td>
                                <td class="<?php echo (int)$row->revenue >= 0 ? 'text-success' : 'text-error'; ?>">
                                    <?php echo number_format((int)$row->revenue); ?> €
                                </td>
                                <td>
                                    <?php if ((int)$row->rep_earned > 0): ?>
                                        <span class="text-info">+<?php echo (int)$row->rep_earned; ?></span>
                                    <?php else: ?>
                                        <span class="text-base-content/60">—</span>
                                    <?php endif; ?>
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

<script>
(function () {
    /**
     * Update the max attribute of the guests input based on the selected lesson type.
     */
    function updateGuestsMax(select) {
        var opt = select.options[select.selectedIndex];
        var max = parseInt(opt.getAttribute('data-max') || '10', 10);
        var inp = document.getElementById('guests_enrolled');
        if (!inp) return;
        inp.max = max;
        if (parseInt(inp.value, 10) > max) inp.value = max;
        document.getElementById('guests_hint').textContent =
            '<?php echo addslashes(sprintf($L['max_guests_hint'] ?? 'Maximum: %d guests', 0)); ?>'.replace('%d', max);
    }

    var sel = document.getElementById('id_lesson_type');
    if (sel) {
        updateGuestsMax(sel);
        sel.addEventListener('change', function () { updateGuestsMax(this); });
    }
})();
</script>
