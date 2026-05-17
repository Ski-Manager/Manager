<?php
// ── View helpers ──────────────────────────────────────────────────────────
$condition_key = $today_condition_key ?? '';

$hero_styles = [
    'Sunny'    => 'linear-gradient(135deg,#1565c0 0%,#1976d2 40%,#42a5f5 100%)',
    'Cloudy'   => 'linear-gradient(135deg,#455a64 0%,#607d8b 60%,#90a4ae 100%)',
    'Overcast' => 'linear-gradient(135deg,#37474f 0%,#546e7a 100%)',
    'Snowing'  => 'linear-gradient(135deg,#1a237e 0%,#283593 40%,#5c6bc0 80%,#9fa8da 100%)',
    'Raining'  => 'linear-gradient(135deg,#1a237e 0%,#1e3a5f 50%,#2d5986 100%)',
    'Storm'    => 'linear-gradient(135deg,#0d0d1a 0%,#1a1a2e 50%,#16213e 100%)',
    'Blizzard' => 'linear-gradient(135deg,#0d1b2a 0%,#1b2a3b 40%,#3d6080 80%,#9ecae1 100%)',
    'Fog'      => 'linear-gradient(135deg,#546e7a 0%,#78909c 50%,#b0bec5 100%)',
    'Windy'    => 'linear-gradient(135deg,#1b5e20 0%,#2e7d32 50%,#558b2f 100%)',
];
$hero_bg = $hero_styles[$condition_key] ?? 'linear-gradient(135deg,#1e3a5f 0%,#2d5986 100%)';

// Snow quality: dot color (inline hex, no broken Tailwind classes)
$sq_color_map = [
    'powder'  => '#60a5fa',
    'packed'  => '#22d3ee',
    'wet'     => '#2dd4bf',
    'slushy'  => '#facc15',
    'icy'     => '#fb923c',
    'poor'    => '#f87171',
];

$impact_cfg = [
    'up'          => ['icon'=>'bi-arrow-up-circle-fill',   'color'=>'text-success',          'label'=>$this->lang->line('weather')['visitors_up']],
    'down_medium' => ['icon'=>'bi-arrow-down-circle',      'color'=>'text-warning',           'label'=>$this->lang->line('weather')['visitors_down_medium']],
    'down_high'   => ['icon'=>'bi-arrow-down-circle-fill', 'color'=>'text-error',             'label'=>$this->lang->line('weather')['visitors_down_high']],
    'neutral'     => ['icon'=>'bi-dash-circle',            'color'=>'text-base-content/50',   'label'=>$this->lang->line('weather')['visitors_neutral']],
];

// Phase config: hex colors (prebuilt CSS lacks bg-sky-500 etc.)
$phase_cfg = [
    'early'   => ['label'=>$this->lang->line('weather')['season_phase_early'],   'hex'=>'#38bdf8', 'text'=>'#0c2a3d'],
    'buildup' => ['label'=>$this->lang->line('weather')['season_phase_buildup'], 'hex'=>'#3b82f6', 'text'=>'#ffffff'],
    'peak'    => ['label'=>$this->lang->line('weather')['season_phase_peak'],    'hex'=>'#22c55e', 'text'=>'#052e16'],
    'late'    => ['label'=>$this->lang->line('weather')['season_phase_late'],    'hex'=>'#f59e0b', 'text'=>'#1c1400'],
    'closing' => ['label'=>$this->lang->line('weather')['season_phase_closing'], 'hex'=>'#ef4444', 'text'=>'#ffffff'],
];

// Returns an inline style string — prebuilt CSS lacks text-blue-*, cyan-*, green-*, yellow-* classes
$temp_color = function($t) {
    if ($t === null) return 'color:oklch(var(--bc)/.4)';
    if ($t < -10) return 'color:#93c5fd';   // icy blue
    if ($t <   0) return 'color:#22d3ee';   // cold cyan
    if ($t <   5) return 'color:#4ade80';   // cool green
    return 'color:#facc15';                  // warm yellow
};
?>

<div class="w-full">
<div class="flex items-center justify-between mb-5">
    <h2 class="text-2xl font-bold flex items-center gap-2">
        <i class="bi bi-cloud-sun" aria-hidden="true"></i>
        <?php echo $this->lang->line('navbar')['weather']; ?>
    </h2>
</div>

<!-- DaisyUI radio tabs (CSS-only, no JS required) -->
<div role="tablist" class="tabs tabs-bordered tabs-lg">

    <!-- ════════════════════════════════════════════════════════════
         TAB 1 — WEATHER
    ════════════════════════════════════════════════════════════ -->
    <input type="radio" name="wc_tabs" role="tab" class="tab font-semibold"
           aria-label="<?php echo $this->lang->line('weather')['title']; ?>" checked="checked" />

    <div role="tabpanel" class="tab-content pt-5 space-y-5">

        <!-- ── Hero: Current Conditions ───────────────────────── -->
        <div class="rounded-2xl overflow-hidden shadow-xl"
             style="background:<?php echo $hero_bg; ?>">

            <!-- Top accent bar -->
            <div style="height:3px;background:rgba(255,255,255,.25)"></div>

            <div class="p-5 text-white">

                <!-- Main row: icon / temp / condition | season info -->
                <div class="flex items-start justify-between gap-4 flex-wrap">

                    <!-- Left block: icon + temperature + condition -->
                    <div class="flex items-center gap-4">
                        <div style="background:rgba(0,0,0,.2);border-radius:1rem;padding:.75rem;backdrop-filter:blur(4px)">
                            <i class="bi <?php echo !empty($today_icon) ? $today_icon : 'bi-cloud'; ?>"
                               style="font-size:3rem;display:block;filter:drop-shadow(0 2px 6px rgba(0,0,0,.5));"
                               aria-hidden="true"></i>
                        </div>
                        <div>
                            <?php if (isset($today_temperature) && $today_temperature !== null): ?>
                            <div style="font-size:3.2rem;font-weight:800;line-height:1;text-shadow:0 2px 8px rgba(0,0,0,.4);letter-spacing:-.02em">
                                <?php echo number_format($today_temperature, 1); ?><span style="font-size:1.8rem;font-weight:600">°C</span>
                            </div>
                            <?php endif; ?>
                            <div style="font-size:1.05rem;font-weight:600;opacity:.9;margin-top:.2rem">
                                <?php echo $today_condition_name ?? $this->lang->line('weather')['no_forecast_data']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right block: phase badge + season/day -->
                    <?php if (isset($season_phase)): ?>
                    <?php $cur_phase = $phase_cfg[$season_phase] ?? $phase_cfg['early']; ?>
                    <div style="text-align:right">
                        <span style="display:inline-block;padding:.35rem .9rem;border-radius:9999px;font-size:.75rem;font-weight:700;letter-spacing:.04em;background:<?php echo $cur_phase['hex']; ?>;color:<?php echo $cur_phase['text']; ?>;box-shadow:0 2px 8px rgba(0,0,0,.3)">
                            <?php echo $cur_phase['label']; ?>
                        </span>
                        <?php if (isset($day_of_season)): ?>
                        <div style="font-size:.8rem;opacity:.75;margin-top:.4rem;font-weight:500">
                            <?php echo $this->lang->line('climate_change')['current_season_label']; ?>
                            <?php echo $current_season; ?> &nbsp;·&nbsp;
                            <?php echo $this->lang->line('weather')['day_unit']; ?> <?php echo $day_of_season; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div><!-- /main row -->

                <!-- Divider -->
                <div style="height:1px;background:rgba(255,255,255,.15);margin:1rem 0"></div>

                <!-- Stats row -->
                <div style="display:flex;flex-wrap:wrap;gap:.6rem">

                    <?php if (isset($today_wind)): ?>
                    <div style="display:flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:.75rem;padding:.5rem .9rem;backdrop-filter:blur(6px)">
                        <i class="bi bi-wind" style="font-size:1.1rem;opacity:.9" aria-hidden="true"></i>
                        <div>
                            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.06em;opacity:.65;font-weight:500"><?php echo $this->lang->line('weather')['wind_label'] ?? 'Wind'; ?></div>
                            <div style="font-size:.9rem;font-weight:700;line-height:1.2"><?php echo $today_wind; ?> m/s</div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($current_snow_level)): ?>
                    <div style="display:flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:.75rem;padding:.5rem .9rem;backdrop-filter:blur(6px)">
                        <i class="bi bi-snow" style="font-size:1.1rem;opacity:.9" aria-hidden="true"></i>
                        <div>
                            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.06em;opacity:.65;font-weight:500"><?php echo $this->lang->line('weather')['snow_depth_label'] ?? 'Snow depth'; ?></div>
                            <div style="font-size:.9rem;font-weight:700;line-height:1.2"><?php echo $current_snow_level; ?> cm</div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($snow_quality_key)): ?>
                    <?php $sq_dot = $sq_color_map[$snow_quality_key] ?? '#94a3b8'; ?>
                    <div style="display:flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:.75rem;padding:.5rem .9rem;backdrop-filter:blur(6px)">
                        <i class="bi bi-gem" style="font-size:1.1rem;color:<?php echo $sq_dot; ?>" aria-hidden="true"></i>
                        <div>
                            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.06em;opacity:.65;font-weight:500"><?php echo $this->lang->line('weather')['snow_quality_label'] ?? 'Snow quality'; ?></div>
                            <div style="font-size:.9rem;font-weight:700;line-height:1.2;color:<?php echo $sq_dot; ?>"><?php echo $this->lang->line('weather')['snow_quality_'.$snow_quality_key]; ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($seasonal_melt) && $seasonal_melt > 0): ?>
                    <div style="display:flex;align-items:center;gap:.6rem;background:rgba(255,100,0,.2);border:1px solid rgba(255,150,50,.35);border-radius:.75rem;padding:.5rem .9rem;backdrop-filter:blur(6px)">
                        <i class="bi bi-droplet-half" style="font-size:1.1rem;color:#fca5a5" aria-hidden="true"></i>
                        <div>
                            <div style="font-size:.6rem;text-transform:uppercase;letter-spacing:.06em;opacity:.65;font-weight:500"><?php echo $this->lang->line('weather')['melt_label'] ?? 'Melt rate'; ?></div>
                            <div style="font-size:.9rem;font-weight:700;line-height:1.2;color:#fca5a5">
                                &minus;<?php echo $seasonal_melt; ?> cm/<?php echo $this->lang->line('weather')['day_unit']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div><!-- /stats -->

            </div><!-- /card-body -->
        </div><!-- /hero -->

        <!-- ── Forecast strip ─────────────────────────────────── -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-5">

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                        <?php echo count($forecast_data ?? []); ?>-<?php echo $this->lang->line('weather')['title']; ?>
                    </h3>
                    <div><?php echo $button_subscribe; ?></div>
                </div>

                <?php if (!empty($forecast_data)): ?>
                <!-- Scrollable day-card strip -->
                <div class="flex gap-2 overflow-x-auto pb-3"
                     style="scrollbar-width:thin;"
                     role="region"
                     aria-label="<?php echo $this->lang->line('weather')['title']; ?>">
                    <?php foreach ($forecast_data as $idx => $day): ?>
                    <?php
                    $is_today = ($idx === 0);
                    $impact   = isset($day['impact_key']) ? ($impact_cfg[$day['impact_key']] ?? $impact_cfg['neutral']) : null;
                    $temp_sty = $day['has_data'] ? $temp_color($day['temperature']) : 'color:oklch(var(--bc)/.4)';
                    $snow_sty = isset($day['snow_num'])
                        ? ($day['snow_num'] > 0 ? 'color:#60a5fa' : ($day['snow_num'] < 0 ? 'color:#fb923c' : 'color:oklch(var(--bc)/.4)'))
                        : 'color:oklch(var(--bc)/.4)';
                    ?>
                    <!-- card class ensures [data-theme=dark] critical-CSS override (#1e2330) applies -->
                    <div class="card flex-none rounded-xl text-center transition-all<?php echo $is_today ? ' border-2 border-primary' : ''; ?>"
                         style="width:6.5rem;padding:.75rem .5rem;"
                         tabindex="0">
                        <!-- Today badge -->
                        <?php if ($is_today): ?>
                        <div class="badge badge-primary badge-xs mb-1" style="font-size:.6rem;">
                            <?php echo $this->lang->line('home')['today']; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Day label -->
                        <div class="text-xs font-bold uppercase tracking-wide mb-0"
                             style="<?php echo $is_today ? 'color:oklch(var(--p))' : 'opacity:.55'; ?>">
                            <?php echo htmlspecialchars($day['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="text-xs mb-2" style="opacity:.38;font-size:.68rem;">
                            <?php echo $day['date_label']; ?>
                        </div>

                        <?php if ($day['has_data']): ?>
                        <!-- Weather icon -->
                        <div class="mb-1">
                            <i class="bi <?php echo $day['icon']; ?>"
                               style="font-size:2rem;<?php echo $is_today ? 'color:oklch(var(--p))' : 'opacity:.8'; ?>"
                               aria-hidden="true"
                               title="<?php echo htmlspecialchars($day['name'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                        </div>

                        <!-- Temperature -->
                        <div class="text-2xl font-bold mb-1" style="line-height:1;<?php echo $temp_sty; ?>">
                            <?php echo number_format($day['temperature'], 0); ?>°
                        </div>

                        <!-- Snow delta -->
                        <div class="text-xs font-semibold mb-1" style="<?php echo $snow_sty; ?>">
                            <i class="bi bi-snow" aria-hidden="true"></i>
                            <?php echo $day['snow_delta']; ?>
                        </div>

                        <!-- Wind -->
                        <div class="text-xs mb-1" style="opacity:.5;font-size:.7rem;">
                            <i class="bi bi-wind" aria-hidden="true"></i>
                            <?php echo $day['wind']; ?> m/s
                        </div>

                        <!-- Visitor impact icon -->
                        <?php if ($impact): ?>
                        <div class="mt-1 <?php echo $impact['color']; ?>"
                             title="<?php echo htmlspecialchars($impact['label'], ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="bi <?php echo $impact['icon']; ?> text-sm"
                               aria-hidden="true"></i>
                            <span class="sr-only"><?php echo htmlspecialchars($impact['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Danger pill -->
                        <?php if (!empty($day['is_danger'])): ?>
                        <div class="badge badge-error badge-xs mt-1" role="alert">
                            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>

                        <?php else: ?>
                        <div class="py-4 text-xs italic" style="opacity:.3;">
                            <?php echo $this->lang->line('weather')['no_forecast_data']; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div><!-- /strip -->

                <!-- 14-day upgrade teaser (only when showing ≤3 days) -->
                <?php if (count($forecast_data) <= 3): ?>
                <div class="alert border border-info mt-4 flex items-start gap-3 text-sm" style="background:rgba(56,189,248,.1)">
                    <i class="bi bi-calendar3-event text-info text-xl flex-none" aria-hidden="true"></i>
                    <div>
                        <?php echo $this->lang->line('weather')['genepis_advantages1']; ?>
                        <br><span class="text-base-content/60"><?php echo $this->lang->line('weather')['genepis_advantages2']; ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hidden span kept for AJAX subscription table update; JS observer will reload page -->
                <span id="forecast_table" style="display:none;" aria-hidden="true"><?php echo $table; ?></span>

                <!-- Hidden confirm dialog text needed by subscription JS -->
                <div id="dialog-confirm_14day_forecast" style="display:none;">
                    <?php echo $this->lang->line('weather')['confirm_14day_forecast']; ?>
                </div>

                <?php else: ?>
                <p class="text-base-content/50 text-sm"><?php echo $this->lang->line('weather')['no_forecast_data']; ?></p>
                <?php endif; ?>

            </div>
        </div><!-- /forecast card -->

        <!-- ── Season timeline ────────────────────────────────── -->
        <?php if (isset($season_phase)): ?>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-5">
                <h4 class="font-semibold mb-3 flex items-center gap-2 text-base">
                    <i class="bi bi-calendar-range" aria-hidden="true"></i>
                    <?php echo $this->lang->line('weather')['season_phase_label']; ?>
                </h4>
                <div style="display:flex;gap:.375rem;min-height:2.5rem">
                    <?php foreach ($phase_cfg as $pk => $pcfg): ?>
                    <?php $active = ($pk === $season_phase); ?>
                    <div style="flex:1;display:flex;align-items:center;justify-content:center;border-radius:.5rem;padding:.4rem .25rem;font-size:.7rem;font-weight:600;text-align:center;transition:all .2s;<?php echo $active ? 'background:'.$pcfg['hex'].';color:'.$pcfg['text'].';box-shadow:0 2px 8px rgba(0,0,0,.25)' : 'background:var(--color-base-200,#2a2f3e);color:var(--color-base-content,#ccc);opacity:.55'; ?>"
                         <?php echo $active ? 'aria-current="step"' : ''; ?>>
                        <?php echo $pcfg['label']; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Info accordion ─────────────────────────────────── -->
        <details class="collapse collapse-arrow bg-base-200 rounded-xl">
            <summary class="collapse-title text-sm font-medium py-3">
                <i class="bi bi-info-circle mr-1" aria-hidden="true"></i>
                <?php echo $this->lang->line('weather')['intro_top']; ?>
            </summary>
            <div class="collapse-content text-sm text-base-content/70 space-y-1 pt-1">
                <p><?php echo $this->lang->line('weather')['intro']; ?></p>
                <p><?php echo $this->lang->line('weather')['intro2']; ?></p>
            </div>
        </details>

    </div><!-- /weather tab-content -->

    <!-- ════════════════════════════════════════════════════════════
         TAB 2 — CLIMATE CHANGE
    ════════════════════════════════════════════════════════════ -->
    <input type="radio" name="wc_tabs" role="tab" class="tab font-semibold"
           aria-label="<?php echo $this->lang->line('climate_change')['title']; ?>" />

    <div role="tabpanel" class="tab-content pt-5 space-y-5">

        <p class="text-base-content/60 text-sm"><?php echo $this->lang->line('climate_change')['intro']; ?></p>

        <!-- ── Climate level + Active effects (two columns) ───── -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Climate level card -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-5 gap-3">
                    <h4 class="card-title text-base">
                        <i class="bi bi-thermometer-half text-xl"
                           style="color:<?php echo $climate->climate_level >= 7 ? '#ef4444' : ($climate->climate_level >= 4 ? '#f59e0b' : '#22c55e'); ?>"
                           aria-hidden="true"></i>
                        <?php echo $this->lang->line('climate_change')['climate_level_label']; ?>
                    </h4>

                    <!-- Big number + label -->
                    <div class="flex items-end gap-2">
                        <span class="text-5xl font-bold" style="line-height:1;font-weight:800"
                              style="color:<?php echo $climate->climate_level >= 7 ? '#ef4444' : ($climate->climate_level >= 4 ? '#f59e0b' : '#22c55e'); ?>">
                            <?php echo $climate->climate_level; ?>
                        </span>
                        <span class="text-xl text-base-content/50 mb-1">/ 10</span>
                        <span class="badge mb-1 <?php echo $climate->climate_level >= 7 ? 'badge-error' : ($climate->climate_level >= 4 ? 'badge-warning' : 'badge-success'); ?>">
                            <?php
                            if ($climate->climate_level >= 7)     echo 'Severe';
                            elseif ($climate->climate_level >= 4) echo 'Moderate';
                            else                                  echo 'Normal';
                            ?>
                        </span>
                    </div>

                    <!-- Segmented bar (10 pips) -->
                    <div class="flex gap-1"
                         role="progressbar"
                         aria-valuenow="<?php echo $climate->climate_level; ?>"
                         aria-valuemin="0" aria-valuemax="10"
                         aria-label="<?php echo $this->lang->line('climate_change')['climate_level_label']; ?> <?php echo $climate->climate_level; ?>/10">
                        <?php for ($s = 1; $s <= 10; $s++): ?>
                        <?php
                        $filled = ($s <= $climate->climate_level);
                        if ($s <= 3)     $pip = $filled ? 'bg-success'  : 'bg-base-300';
                        elseif ($s <= 6) $pip = $filled ? 'bg-warning'  : 'bg-base-300';
                        else             $pip = $filled ? 'bg-error'     : 'bg-base-300';
                        ?>
                        <div class="flex-1 h-3.5 rounded-sm <?php echo $pip; ?> transition-all"></div>
                        <?php endfor; ?>
                    </div>

                    <p class="text-sm text-base-content/70 leading-snug">
                        <?php echo $this->lang->line('climate_change')['level_desc_'.min($climate->climate_level, 3)]; ?>
                    </p>
                    <p class="text-xs text-base-content/50">
                        <?php echo $this->lang->line('climate_change')['current_season_label']; ?>:
                        <strong><?php echo $current_season; ?></strong>
                    </p>
                </div>
            </div><!-- /climate level card -->

            <!-- Active effects card -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-5 gap-3">
                    <h4 class="card-title text-base">
                        <i class="bi bi-activity" aria-hidden="true"></i>
                        <?php echo $this->lang->line('climate_change')['active_effects']; ?>
                    </h4>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Snow penalty -->
                        <?php $snow_pen = $climate_effects['winter_snow_penalty'] > 0; ?>
                        <div class="card" style="<?php echo $snow_pen ? 'background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3)' : ''; ?>">
                            <div class="card-body p-3 gap-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-cloud-snow text-primary text-sm" aria-hidden="true"></i>
                                    <span style="font-size:.65rem;font-weight:600;line-height:1.2;opacity:.8"><?php echo $this->lang->line('climate_change')['effect_snow_penalty']; ?></span>
                                </div>
                                <div class="text-lg font-bold <?php echo $snow_pen ? 'text-error' : 'text-base-content/30'; ?>">
                                    <?php echo $snow_pen ? '&minus;'.$climate_effects['winter_snow_penalty'].' cm' : $this->lang->line('climate_change')['no_effect']; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Cost multiplier -->
                        <?php $cost_pen = $climate_effects['snowmaking_cost_mult'] > 1.0; ?>
                        <div class="card" style="<?php echo $cost_pen ? 'background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.3)' : ''; ?>">
                            <div class="card-body p-3 gap-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-coin text-warning text-sm" aria-hidden="true"></i>
                                    <span style="font-size:.65rem;font-weight:600;line-height:1.2;opacity:.8"><?php echo $this->lang->line('climate_change')['effect_cost_mult']; ?></span>
                                </div>
                                <div class="text-lg font-bold <?php echo $cost_pen ? 'text-warning' : 'text-base-content/30'; ?>">
                                    <?php echo $cost_pen ? '&times;'.number_format($climate_effects['snowmaking_cost_mult'],2) : $this->lang->line('climate_change')['no_effect']; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Glacier loss -->
                        <?php $glac_pen = $climate_effects['glacier_loss'] > 0; ?>
                        <div class="card" style="<?php echo $glac_pen ? 'background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3)' : ''; ?>">
                            <div class="card-body p-3 gap-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-mountain text-base-content/50 text-sm" aria-hidden="true"></i>
                                    <span style="font-size:.65rem;font-weight:600;line-height:1.2;opacity:.8"><?php echo $this->lang->line('climate_change')['effect_glacier']; ?></span>
                                </div>
                                <div class="text-lg font-bold <?php echo $glac_pen ? 'text-error' : 'text-base-content/30'; ?>">
                                    <?php echo $glac_pen ? '&minus;'.$climate_effects['glacier_loss'].' pts/day' : $this->lang->line('climate_change')['no_effect']; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Season length -->
                        <?php $seas_pen = $climate_effects['season_length_penalty'] > 0; ?>
                        <div class="card" style="<?php echo $seas_pen ? 'background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.3)' : ''; ?>">
                            <div class="card-body p-3 gap-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-calendar-minus text-info text-sm" aria-hidden="true"></i>
                                    <span style="font-size:.65rem;font-weight:600;line-height:1.2;opacity:.8"><?php echo $this->lang->line('climate_change')['effect_season']; ?></span>
                                </div>
                                <div class="text-lg font-bold <?php echo $seas_pen ? 'text-warning' : 'text-base-content/30'; ?>">
                                    <?php echo $seas_pen ? '&minus;'.$climate_effects['season_length_penalty'].' days' : $this->lang->line('climate_change')['no_effect']; ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- /effects grid -->

                </div>
            </div><!-- /effects card -->

        </div><!-- /two-column row -->

        <!-- ── Adaptation Investments ─────────────────────────── -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-5">
                <h4 class="card-title text-base mb-1">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    <?php echo $this->lang->line('climate_change')['adaptation_title']; ?>
                </h4>
                <p class="text-sm text-base-content/60 mb-4"><?php echo $this->lang->line('climate_change')['adaptation_intro']; ?></p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <!-- Snowmaking invest -->
                    <div class="card border-2 transition-all
                                <?php echo $climate->snowmaking_invest ? 'border-success' : 'border-base-200'; ?>">
                        <div class="card-body p-4 gap-2">
                            <div class="text-3xl text-primary" aria-hidden="true">
                                <i class="bi bi-snow2"></i>
                            </div>
                            <h5 class="font-bold text-sm"><?php echo $this->lang->line('climate_change')['snowmaking_invest_label']; ?></h5>
                            <p class="text-xs text-base-content/60 flex-1 leading-relaxed">
                                <?php echo $this->lang->line('climate_change')['snowmaking_invest_desc']; ?>
                            </p>
                            <div class="badge badge-outline badge-sm">
                                <?php echo $this->lang->line('climate_change')['cost']; ?>:
                                <?php echo number_format($invest_costs['snowmaking'], 0, '.', ' '); ?> €
                            </div>
                            <?php if ($climate->snowmaking_invest): ?>
                            <button class="btn btn-success btn-sm mt-1" disabled aria-disabled="true">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['invested']; ?>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-primary btn-sm mt-1 invest-btn" data-type="snowmaking_invest">
                                <?php echo $this->lang->line('climate_change')['invest_btn']; ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Altitude invest -->
                    <div class="card border-2 transition-all
                                <?php echo $climate->altitude_invest ? 'border-success' : 'border-base-200'; ?>">
                        <div class="card-body p-4 gap-2">
                            <div class="text-3xl text-warning" aria-hidden="true">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h5 class="font-bold text-sm"><?php echo $this->lang->line('climate_change')['altitude_invest_label']; ?></h5>
                            <p class="text-xs text-base-content/60 flex-1 leading-relaxed">
                                <?php echo $this->lang->line('climate_change')['altitude_invest_desc']; ?>
                            </p>
                            <div class="badge badge-outline badge-sm">
                                <?php echo $this->lang->line('climate_change')['cost']; ?>:
                                <?php echo number_format($invest_costs['altitude'], 0, '.', ' '); ?> €
                            </div>
                            <?php if ($climate->altitude_invest): ?>
                            <button class="btn btn-success btn-sm mt-1" disabled aria-disabled="true">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['invested']; ?>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-warning btn-sm mt-1 invest-btn" data-type="altitude_invest">
                                <?php echo $this->lang->line('climate_change')['invest_btn']; ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Diversify invest -->
                    <div class="card border-2 transition-all
                                <?php echo $climate->diversify_invest ? 'border-success' : 'border-base-200'; ?>">
                        <div class="card-body p-4 gap-2">
                            <div class="text-3xl text-success" aria-hidden="true">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <h5 class="font-bold text-sm"><?php echo $this->lang->line('climate_change')['diversify_invest_label']; ?></h5>
                            <p class="text-xs text-base-content/60 flex-1 leading-relaxed">
                                <?php echo $this->lang->line('climate_change')['diversify_invest_desc']; ?>
                            </p>
                            <div class="badge badge-outline badge-sm">
                                <?php echo $this->lang->line('climate_change')['cost']; ?>:
                                <?php echo number_format($invest_costs['diversify'], 0, '.', ' '); ?> €
                            </div>
                            <?php if ($climate->diversify_invest): ?>
                            <button class="btn btn-success btn-sm mt-1" disabled aria-disabled="true">
                                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['invested']; ?>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-success btn-sm mt-1 invest-btn" data-type="diversify_invest">
                                <?php echo $this->lang->line('climate_change')['invest_btn']; ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div><!-- /invest grid -->
            </div>
        </div><!-- /adaptation card -->

    </div><!-- /climate tab-content -->

</div><!-- /tabs -->

<script>
var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
Settings.forecast_not_subscribed = <?php echo json_encode($this->lang->line('weather')['forecast_not_subscribed']); ?>;
Settings.subscribe                = <?php echo json_encode($this->lang->line('weather')['subscribe']); ?>;
Settings.climate_invest_failed    = <?php echo json_encode($this->lang->line('climate_change')['invest_failed']); ?>;
Settings.climate_invested         = <?php echo json_encode($this->lang->line('climate_change')['invested']); ?>;

// When subscription JS updates #forecast_table (AJAX success), reload to show 14-day UI
(function () {
    var ft = document.getElementById('forecast_table');
    if (!ft) return;
    var orig = ft.innerHTML;
    new MutationObserver(function () {
        if (ft.innerHTML !== orig) window.location.reload();
    }).observe(ft, { childList: true, characterData: true, subtree: true });
}());
</script>
</div><!-- /.w-full -->
