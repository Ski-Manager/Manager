<?php
/**
 * Lift info/detail view — fully redesigned with DaisyUI + Tailwind
 *
 * Key $data variables (set by Lift_controller::show_info_block_lift):
 *   $lift_name, $id_sector, $lift_type, $grip_type, $length
 *   $lift_status_id_raw (1=open 2=closed 3=maintenance 4=construction 5=out-of-order)
 *   $pre_lift_status, $lift_status (may be countdown timestamp), $post_lift_status
 *   $var_lift_is_built, $current_level, $lift_speed, $lift_capacity, $lift_throughput
 *   $lift_condition, $lift_wear_pct, $lift_age_years, $lift_max_age_seasons
 *   $lift_efficiency_penalty, $lift_cost_multiplier
 *   $lift_type_img_name
 *   $levels_data[1..3], $button_upgrade_l2, $button_upgrade_l3, $button_rush[1..3]
 *   $modules_data[], $lift_blocked
 *   $in_edit_mode, $edit_form_open, $edit_name_current, $edit_form_close
 *   $lift_edit_url
 *   $action, $infoMessage, $errors, $body_data, $repair_cost
 */

// ── Status badge / ring config ─────────────────────────────────────────────
$_sid  = isset($lift_status_id_raw) ? (int)$lift_status_id_raw : 1;
$_scfg = [
    1 => ['badge' => 'badge-success', 'icon' => 'bi-check-circle-fill',   'ring' => 'border-success'],
    2 => ['badge' => 'badge-neutral', 'icon' => 'bi-dash-circle',          'ring' => 'border-neutral'],
    3 => ['badge' => 'badge-warning', 'icon' => 'bi-tools',                'ring' => 'border-warning'],
    4 => ['badge' => 'badge-info',    'icon' => 'bi-hammer',               'ring' => 'border-info'],
    5 => ['badge' => 'badge-error',   'icon' => 'bi-exclamation-octagon',  'ring' => 'border-error'],
][$_sid] ?? ['badge' => 'badge-neutral', 'icon' => 'bi-question-circle', 'ring' => 'border-neutral'];

// ── Condition colour ───────────────────────────────────────────────────────
$_wear    = isset($lift_wear_pct) ? max(0, min(100, (int)$lift_wear_pct)) : 0;
$_cond    = 100 - $_wear;
$_ccolor  = $_cond >= 80 ? 'success' : ($_cond >= 50 ? 'warning' : 'error');
?>
<?php if (isset($action) && $action === 'lift_not_found'): ?>
<div class="card bg-base-100 shadow-sm"><div class="card-body">
    <?php if (isset($infoMessage)) echo $infoMessage; ?>
</div></div>
<?php else: ?>

<!-- ══════════════════════════════════════════════════════════════════════════
     HERO CARD — name, status, stats, image
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="card bg-base-100 shadow-sm mb-4">
<div class="card-body gap-4">

    <!-- ── Top row: name + status / image ────────────────────────────────── -->
    <div class="flex flex-wrap items-start justify-between gap-4">

        <!-- Left: title block -->
        <div class="flex-1 min-w-0">

            <!-- Name + edit -->
            <?php if (!empty($in_edit_mode)): ?>
                <?php echo $edit_form_open; ?>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <label for="lift_choose_name" class="font-semibold text-sm sr-only">
                        <?php echo htmlspecialchars($this->lang->line('lift')['name_edit'], ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                    <input type="text" name="lift_choose_name" id="lift_choose_name"
                           value="<?php echo htmlspecialchars($edit_name_current ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           maxlength="35" class="input border border-base-300 input-sm w-52"
                           aria-label="<?php echo htmlspecialchars($this->lang->line('lift')['name_edit'], ENT_QUOTES, 'UTF-8'); ?>"
                           autofocus>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
                        <?php echo $this->lang->line('home')['edit_name']; ?>
                    </button>
                    <a href="<?php echo base_url('lift_controller/show_info_block_lift/'
                        .(isset($id_group) ? (int)$id_group : '').'/'
                        .(isset($id_resort) ? (int)$id_resort : '').'/null/'
                        .(isset($id_group_location) ? (int)$id_group_location : '')); ?>"
                       class="btn btn-ghost btn-sm" aria-label="<?php echo $this->lang->line('home')['cancel'] ?? 'Cancel'; ?>">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </a>
                </div>
                <?php echo $edit_form_close; ?>
                <?php echo form_error('lift_choose_name'); ?>
            <?php else: ?>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="h2 mb-0"><?php echo htmlspecialchars($lift_name ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <?php if (!empty($lift_edit_url)): ?>
                        <a href="<?php echo $lift_edit_url; ?>"
                           class="btn btn-ghost btn-xs"
                           title="<?php echo $this->lang->line('home')['edit']; ?>"
                           aria-label="<?php echo $this->lang->line('home')['edit'] . ' ' . htmlspecialchars($lift_name ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Meta: sector · type · grip · length -->
            <p class="text-base-content/60 text-sm mb-3 flex flex-wrap gap-x-2 gap-y-0.5">
                <?php if (isset($id_sector)): ?>
                    <span><i class="bi bi-geo-alt me-1" aria-hidden="true"></i><?php
                        echo ($this->lang->line('resort')['sector'] ?? 'Sector').' '.(int)$id_sector;
                    ?></span><span aria-hidden="true">·</span>
                <?php endif; ?>
                <?php if (!empty($lift_type)): ?>
                    <span><?php echo htmlspecialchars($lift_type, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span aria-hidden="true">·</span>
                <?php endif; ?>
                <?php if (!empty($grip_type)): ?>
                    <span><?php echo htmlspecialchars($grip_type, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
                <?php if (isset($length)): ?>
                    <span aria-hidden="true">·</span>
                    <span><?php echo number_format((int)$length, 0, ',', ' '); ?> m</span>
                <?php endif; ?>
            </p>

            <!-- Status badge -->
            <div>
                <span class="badge <?php echo $_scfg['badge']; ?> gap-1 py-3 px-3 text-sm" role="status">
                    <i class="bi <?php echo $_scfg['icon']; ?>" aria-hidden="true"></i>
                    <?php echo $pre_lift_status ?? ''; ?>
                    <span data-countdown="<?php echo htmlspecialchars($lift_status ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo $lift_status ?? ''; ?>
                    </span>
                    <?php echo $post_lift_status ?? ''; ?>
                </span>
            </div>
        </div><!-- /left -->

        <!-- Right: lift image -->
        <?php if (!empty($lift_type_img_name)): ?>
        <div class="flex-shrink-0 flex items-center justify-center">
            <div class="tooltip tooltip-left" data-tip="<?php echo htmlspecialchars($lift_type ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <img src="<?php echo base_url('img/images/'.$lift_type_img_name.'.png'); ?>"
                     alt="<?php echo htmlspecialchars($lift_type ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                     class="h-32 w-auto object-contain rounded-lg"
                     onerror="this.parentElement.parentElement.style.display='none'">
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /top row -->

    <!-- ── Current-level stat pills ──────────────────────────────────────── -->
    <?php if (!empty($var_lift_is_built)): ?>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" role="region" aria-label="<?php echo $this->lang->line('lift')['build_info']; ?>">

        <div class="rounded-xl border border-base-300 p-3 text-center">
            <div class="text-2xl font-bold tabular-nums"><?php echo isset($lift_speed) ? $lift_speed : '—'; ?></div>
            <div class="text-xs text-base-content/60 mt-1 leading-snug">
                <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>
                <?php echo $this->lang->line('lift')['length_speed_column']; ?>
                <span class="block"><?php echo $this->lang->line('lift')['speed_unit']; ?></span>
            </div>
        </div>

        <div class="rounded-xl border border-base-300 p-3 text-center">
            <div class="text-2xl font-bold tabular-nums"><?php echo isset($lift_capacity) ? $lift_capacity : '—'; ?></div>
            <div class="text-xs text-base-content/60 mt-1 leading-snug">
                <i class="bi bi-people me-1" aria-hidden="true"></i>
                <?php echo $this->lang->line('lift')['capacity_seats']; ?>
            </div>
        </div>

        <div class="rounded-xl border border-base-300 p-3 text-center">
            <div class="text-2xl font-bold tabular-nums"><?php echo isset($lift_throughput) ? number_format((int)$lift_throughput, 0, ',', ' ') : '—'; ?></div>
            <div class="text-xs text-base-content/60 mt-1 leading-snug">
                <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>
                <?php echo $this->lang->line('lift')['throughput']; ?>
                <span class="block"><?php echo $this->lang->line('lift')['throughput_unit']; ?></span>
            </div>
        </div>

        <div class="rounded-xl border border-base-300 p-3 text-center">
            <div class="text-2xl font-bold tabular-nums text-<?php echo $_ccolor; ?>"><?php echo $_cond; ?>%</div>
            <div class="text-xs text-base-content/60 mt-1 mb-2 leading-snug">
                <i class="bi bi-wrench me-1" aria-hidden="true"></i>
                <?php echo $this->lang->line('slope')['condition']; ?>
            </div>
            <div class="w-full bg-base-200 rounded-full h-1.5"
                 role="progressbar" aria-valuenow="<?php echo $_cond; ?>" aria-valuemin="0" aria-valuemax="100"
                 aria-label="<?php echo $this->lang->line('slope')['condition']; ?>">
                <div class="bg-<?php echo $_ccolor; ?> h-1.5 rounded-full transition-all"
                     style="width:<?php echo $_cond; ?>%"></div>
            </div>
        </div>

    </div><!-- /stat pills -->

    <!-- ── Age & Wear row ─────────────────────────────────────────────────── -->
    <?php if (isset($lift_age_years)): ?>
    <div class="rounded-xl border border-base-300 p-3 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-sm">

        <div>
            <div class="text-xl font-bold"><?php echo (int)$lift_age_years; ?></div>
            <div class="text-xs text-base-content/60 leading-snug">
                <?php echo $this->lang->line('lift')['age']; ?>
                <span class="block">(<?php echo $this->lang->line('lift')['age_unit']; ?>)</span>
            </div>
            <?php if ((int)$lift_age_years >= (int)($lift_max_age_seasons ?? 999)): ?>
                <span class="badge badge-error badge-sm mt-1"><?php echo $this->lang->line('lift')['end_of_life_badge']; ?></span>
            <?php endif; ?>
        </div>

        <div>
            <?php $_wc = $_wear >= 50 ? 'error' : ($_wear >= 25 ? 'warning' : 'success'); ?>
            <div class="text-xl font-bold text-<?php echo $_wc; ?>"><?php echo $_wear; ?>%</div>
            <div class="text-xs text-base-content/60 leading-snug mb-1"><?php echo $this->lang->line('lift')['wear']; ?></div>
            <div class="w-full bg-base-200 rounded-full h-1.5"
                 role="progressbar" aria-valuenow="<?php echo $_wear; ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="bg-<?php echo $_wc; ?> h-1.5 rounded-full" style="width:<?php echo $_wear; ?>%"></div>
            </div>
        </div>

        <?php if (isset($lift_efficiency_penalty) && (float)$lift_efficiency_penalty > 0): ?>
        <div>
            <div class="text-xl font-bold text-warning">-<?php echo round((float)$lift_efficiency_penalty, 1); ?>%</div>
            <div class="text-xs text-base-content/60 leading-snug"><?php echo $this->lang->line('lift')['efficiency_penalty']; ?></div>
        </div>
        <?php endif; ?>

        <?php if (isset($lift_cost_multiplier) && (float)$lift_cost_multiplier > 1): ?>
        <div>
            <div class="text-xl font-bold text-warning">×<?php echo number_format((float)$lift_cost_multiplier, 2); ?></div>
            <div class="text-xs text-base-content/60 leading-snug"><?php echo $this->lang->line('lift')['maintenance_multiplier']; ?></div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; // age ?>
    <?php endif; // var_lift_is_built ?>

    <!-- ── Action alerts ─────────────────────────────────────────────────── -->
    <?php if (!empty($action) && $action === 'sector_locked'): ?>
        <div class="alert alert-warning"><?php echo $this->lang->line('resort')['sector_locked_lift']; ?></div>
    <?php elseif (!empty($action) && $action === 'ongoing_construction_lift'): ?>
        <div class="alert alert-info"><?php echo $this->lang->line('lift')['ongoing_construction_lift']; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)):       echo '<div>'.$errors.'</div>';        endif; ?>
    <?php if (!empty($infoMessage)):  echo '<div>'.$infoMessage.'</div>';   endif; ?>
    <?php if (!empty($body_data)):    echo '<div>'.$body_data.'</div>';     endif; ?>

</div><!-- /card-body -->
</div><!-- /hero card -->

<!-- ══════════════════════════════════════════════════════════════════════════
     LEVEL PROGRESSION
     ══════════════════════════════════════════════════════════════════════════ -->
<?php if (!empty($levels_data)): ?>
<?php $_cur = isset($current_level) ? (int)$current_level : 1; ?>
<div class="card bg-base-100 shadow-sm mb-4">
<div class="card-body">
    <h3 class="h3 mb-4"><?php echo $this->lang->line('lift')['build_info']; ?></h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

    <?php for ($_l = 1; $_l <= 3; $_l++):
        $_lv        = $levels_data[$_l];
        $_is_cur    = ($_l === $_cur);
        $_is_past   = ($_l < $_cur);
        $_bdr       = $_is_cur ? 'border-primary' : 'border-base-300';
    ?>
    <div class="rounded-xl border-2 <?php echo $_bdr; ?> p-4 flex flex-col gap-2"
         <?php if ($_is_cur) echo 'aria-current="step"'; ?>>

        <!-- Level header -->
        <div class="flex items-center justify-between mb-2">
            <span class="font-bold text-lg"><?php echo $this->lang->line('home')['level'].' '.$_l; ?></span>
            <?php if ($_is_cur): ?>
                <span class="badge badge-primary badge-sm">▶ <?php echo $this->lang->line('home')['status_built'] ?? 'Current'; ?></span>
            <?php elseif ($_is_past): ?>
                <span class="badge badge-neutral badge-sm">✓</span>
            <?php endif; ?>
        </div>

        <!-- Stats list -->
        <ul class="text-sm space-y-1.5 flex-1">

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i><?php echo $this->lang->line('lift')['length_speed_column']; ?>
                </span>
                <strong class="tabular-nums"><?php echo $_lv['speed']; ?> <?php echo $this->lang->line('lift')['speed_unit']; ?></strong>
            </li>

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-people me-1" aria-hidden="true"></i><?php echo $this->lang->line('lift')['capacity_seats']; ?>
                </span>
                <strong class="tabular-nums"><?php echo $_lv['capacity']; ?></strong>
            </li>

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i><?php echo $this->lang->line('lift')['throughput']; ?>
                </span>
                <strong class="tabular-nums"><?php echo number_format((int)$_lv['throughput'], 0, ',', ' '); ?></strong>
            </li>

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-cash me-1" aria-hidden="true"></i><?php echo $this->lang->line('home')['base_cost']; ?>
                </span>
                <strong class="tabular-nums"><?php echo number_format((int)$_lv['base_cost'], 0, ',', ' '); ?> €</strong>
            </li>

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-rulers me-1" aria-hidden="true"></i><?php echo $this->lang->line('home')['meter_cost']; ?>
                </span>
                <strong class="tabular-nums"><?php echo number_format((int)$_lv['meter_cost'], 0, ',', ' '); ?> €/m</strong>
            </li>

            <?php if ($_l > 1 && !is_null($_lv['upgrade_cost'])): ?>
            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-arrow-up-circle me-1" aria-hidden="true"></i><?php echo $this->lang->line('home')['upgrade_cost']; ?>
                </span>
                <strong class="tabular-nums"><?php echo number_format((int)$_lv['upgrade_cost'], 0, ',', ' '); ?> €</strong>
            </li>
            <?php endif; ?>

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-clock me-1" aria-hidden="true"></i><?php echo $this->lang->line('home')['building_time']; ?>
                </span>
                <strong><?php echo $_lv['building_time']; ?></strong>
            </li>

            <li class="flex justify-between items-baseline gap-1">
                <span class="text-base-content/60">
                    <i class="bi bi-star me-1" aria-hidden="true"></i><?php echo $this->lang->line('home')['reputation']; ?>
                </span>
                <strong class="tabular-nums"><?php echo number_format((int)$_lv['reputation'], 0, ',', ' '); ?></strong>
            </li>

        </ul><!-- /stats -->

        <!-- Upgrade / rush buttons -->
        <div class="flex flex-wrap gap-2 mt-2 pt-2 border-t border-base-200">
            <?php if ($_l === 2 && !empty($button_upgrade_l2)): echo $button_upgrade_l2; endif; ?>
            <?php if ($_l === 3 && !empty($button_upgrade_l3)): echo $button_upgrade_l3; endif; ?>
            <?php if (!empty($button_rush[$_l])): echo $button_rush[$_l]; endif; ?>
        </div>

    </div><!-- /level card -->
    <?php endfor; ?>

    </div><!-- /grid -->
</div><!-- /card-body -->
</div><!-- /levels card -->
<?php endif; // levels_data ?>

<!-- ══════════════════════════════════════════════════════════════════════════
     MODULAR UPGRADES
     ══════════════════════════════════════════════════════════════════════════ -->
<?php if (!empty($modules_data)): ?>
<div class="card bg-base-100 shadow-sm mb-4">
<div class="card-body">
    <h3 class="h3 mb-4"><?php echo $this->lang->line('lift')['modular_upgrades_title']; ?></h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    <?php foreach ($modules_data as $_mod):
        $_mbdr = $_mod['installed'] ? 'border-success' : 'border-base-300';
    ?>
    <div class="rounded-xl border-2 <?php echo $_mbdr; ?> p-4 flex flex-col gap-3">

        <!-- Name + installed badge -->
        <div class="flex items-start justify-between gap-2">
            <div>
                <div class="font-semibold"><?php echo htmlspecialchars($_mod['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php if (!empty($_mod['description'])): ?>
                    <div class="text-xs text-base-content/60 mt-0.5 leading-snug">
                        <?php echo htmlspecialchars($_mod['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($_mod['installed']): ?>
                <span class="badge badge-success badge-sm flex-shrink-0 gap-1">
                    <i class="bi bi-check-lg" aria-hidden="true"></i><?php echo $this->lang->line('lift')['modular_installed']; ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Bonus pills -->
        <div class="flex flex-wrap gap-1">
            <?php if ($_mod['speed_bonus'] > 0): ?>
                <span class="badge badge-outline badge-sm gap-1">
                    <i class="bi bi-speedometer2" aria-hidden="true"></i>+<?php echo $_mod['speed_bonus']; ?> <?php echo $this->lang->line('lift')['speed_unit']; ?>
                </span>
            <?php endif; ?>
            <?php if ($_mod['throughput_bonus'] > 0): ?>
                <span class="badge badge-outline badge-sm gap-1">
                    <i class="bi bi-arrow-repeat" aria-hidden="true"></i>+<?php echo number_format((int)$_mod['throughput_bonus'], 0, ',', ' '); ?>
                </span>
            <?php endif; ?>
            <?php if ($_mod['capacity_bonus'] > 0): ?>
                <span class="badge badge-outline badge-sm gap-1">
                    <i class="bi bi-people" aria-hidden="true"></i>+<?php echo $_mod['capacity_bonus']; ?>
                </span>
            <?php endif; ?>
            <?php if ($_mod['reputation_bonus'] > 0): ?>
                <span class="badge badge-outline badge-sm gap-1">
                    <i class="bi bi-star" aria-hidden="true"></i>+<?php echo number_format((int)$_mod['reputation_bonus'], 0, ',', ' '); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Cost row -->
        <div class="text-sm flex flex-wrap gap-3 text-base-content/60">
            <span>
                <i class="bi bi-cash me-1" aria-hidden="true"></i><?php echo number_format((int)$_mod['cost'], 0, ',', ' '); ?> €
            </span>
            <?php if ($_mod['daily_cost'] > 0): ?>
                <span>
                    <i class="bi bi-calendar-day me-1" aria-hidden="true"></i>
                    +<?php echo number_format((int)$_mod['daily_cost'], 0, ',', ' '); ?> €/<?php echo $this->lang->line('home')['day'] ?? 'day'; ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Action -->
        <div class="mt-auto">
            <?php if ($_mod['installed']): ?>
                <p class="text-success text-sm font-semibold">
                    <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i><?php echo $this->lang->line('lift')['modular_installed']; ?>
                </p>
            <?php elseif (!empty($lift_blocked)): ?>
                <button class="btn btn-warning btn-sm w-full" disabled aria-disabled="true">
                    <?php echo $this->lang->line('lift')['modular_install_btn']; ?>
                </button>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($_mod['install_url'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn btn-primary btn-sm w-full">
                    <i class="bi bi-box-arrow-in-down me-1" aria-hidden="true"></i>
                    <?php echo $this->lang->line('lift')['modular_install_btn']; ?>
                </a>
            <?php endif; ?>
        </div>

    </div><!-- /module card -->
    <?php endforeach; ?>

    </div><!-- /grid -->
</div><!-- /card-body -->
</div><!-- /modules card -->
<?php endif; // modules_data ?>

<!-- Repair confirmation dialog (hidden, shown by jQuery UI dialog) -->
<div id="dialog-confirm-repair_lift" style="display:none;">
    <?php echo $this->lang->line('lift')['confirm_destroy_item_part1']; ?>
    <?php echo isset($repair_cost) ? $repair_cost : ''; ?>
    <?php echo $this->lang->line('lift')['confirm_destroy_item_part2']; ?>
</div>

<?php endif; // not lift_not_found ?>

<script>
$(function () {
    if (typeof smInitTooltips === 'function') { smInitTooltips(document); }
});
</script>
