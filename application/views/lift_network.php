<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <h2 class="h2"><?php echo $this->lang->line('lift')['network_title']; ?></h2>
        <p class="text-base-content/60"><?php echo $this->lang->line('lift')['network_intro']; ?></p>

        <?php if ($metrics['total_lifts'] === 0): ?>
            <div class="alert alert-info"><?php echo $this->lang->line('lift')['network_no_lifts']; ?></div>
        <?php else: ?>

            <!-- Summary counts -->
            <div class="grid grid-cols-12 gap-3 mb-4">
                <div class="col-span-6 md:col-span-3">
                    <div class="card text-center shadow-sm h-full">
                        <div class="card-body">
                            <div class="display-6 font-bold"><?php echo $metrics['total_lifts']; ?></div>
                            <div class="text-base-content/60 small"><?php echo $this->lang->line('lift')['network_total_lifts']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-span-6 md:col-span-3">
                    <div class="card text-center shadow-sm h-full">
                        <div class="card-body">
                            <div class="display-6 font-bold"><?php echo $metrics['open_lifts']; ?></div>
                            <div class="text-base-content/60 small"><?php echo $this->lang->line('lift')['network_open_lifts']; ?></div>
                        </div>
                    </div>
                </div>
                <?php
                $ns = $metrics['network_score'];
                if ($ns >= 80)     { $ns_class = 'success'; }
                elseif ($ns >= 50) { $ns_class = 'warning'; }
                else               { $ns_class = 'error';  }
                ?>
                <div class="col-span-12 md:col-span-6">
                    <div class="card text-center shadow-sm h-full border-<?php echo htmlspecialchars($ns_class, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="card-body">
                            <div class="display-5 font-bold text-<?php echo htmlspecialchars($ns_class, ENT_QUOTES, 'UTF-8'); ?>"><?php echo intval($ns); ?>%</div>
                            <div class="text-base-content/60 small"><?php echo htmlspecialchars($this->lang->line('lift')['network_score'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <progress class="progress progress-<?php echo htmlspecialchars($ns_class, ENT_QUOTES, 'UTF-8'); ?> w-full mt-2" style="height:8px;" value="<?php echo intval($ns); ?>" max="100"></progress>
                            <p class="mb-0 mt-1 small text-<?php echo htmlspecialchars($ns_class, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($this->lang->line('lift')['network_score_help'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric cards -->
            <div class="grid gap-4">

                <?php
                /**
                 * Helper: render one metric card.
                 * $value      int      0-100
                 * $key        string   lang key prefix
                 * $higher_is_better bool
                 */
                $render_metric = function(int $value, string $key, bool $higher_is_better) {
                    $lang = $this->lang->line('lift');

                    if ($higher_is_better) {
                        if ($value >= 80)      { $class = 'success'; $badge = 'badge-success'; $tip_key = $key . '_good'; }
                        elseif ($value >= 50)  { $class = 'warning'; $badge = 'badge-warning'; $tip_key = $key . '_warning'; }
                        else                   { $class = 'error';  $badge = 'badge-error';  $tip_key = $key . '_bad'; }
                    } else {
                        // Lower is better (overlap waste)
                        if ($value <= 10)      { $class = 'success'; $badge = 'badge-success'; $tip_key = $key . '_good'; }
                        elseif ($value <= 30)  { $class = 'warning'; $badge = 'badge-warning'; $tip_key = $key . '_warning'; }
                        else                   { $class = 'error';  $badge = 'badge-error';  $tip_key = $key . '_bad'; }
                    }

                    $bar_value = $higher_is_better ? $value : (100 - $value);
                    ?>
                    <div class="md:col-span-6">
                        <div class="card shadow-sm h-full">
                            <div class="card-body">
                                <h5 class="card-title flex justify-between items-center">
                                    <?php echo htmlspecialchars($lang[$key], ENT_QUOTES, 'UTF-8'); ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $value; ?>%</span>
                                </h5>
                                <p class="card-text text-base-content/60 small">
                                    <?php echo htmlspecialchars($lang[$key . '_help'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <progress class="progress progress-<?php echo $class; ?> w-full mb-2" style="height:12px;" value="<?php echo $bar_value; ?>" max="100"></progress>
                                <p class="mb-0 small text-<?php echo $class; ?>">
                                    <?php echo htmlspecialchars($lang[$tip_key], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                };
                ?>

                <?php $render_metric($metrics['transfer_efficiency'], 'transfer_efficiency', true); ?>
                <?php $render_metric($metrics['bottleneck_score'],    'bottleneck_score',    true); ?>
                <?php $render_metric($metrics['network_redundancy'],  'network_redundancy',  true); ?>
                <?php $render_metric($metrics['overlap_waste'],       'overlap_waste',       false); ?>

            </div><!-- /.row -->

            <!-- Capacity Recommendations -->
            <div class="mt-5">
                <h4 class="h4"><?php echo $this->lang->line('lift')['network_capacity_title']; ?></h4>
                <p class="text-base-content/60 small"><?php echo $this->lang->line('lift')['network_capacity_intro']; ?></p>

                <?php $suggestions = $metrics['capacity_suggestions']; ?>
                <?php if (empty($suggestions)): ?>
                    <div class="alert alert-success"><?php echo $this->lang->line('lift')['network_capacity_none']; ?></div>
                <?php else: ?>
                <div class="space-y-1">
                    <?php foreach ($suggestions as $s): ?>
                        <?php
                        $lift_name    = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
                        $cur_tp       = $s['throughput'];
                        $max_tp       = $s['max_throughput'];
                        $level        = $s['level'];
                        $can_upgrade  = $s['can_upgrade'];
                        $next_level   = min($level + 1, LIFT_MAX_LEVEL);
                        $upgrade_url  = base_url('lift_controller/upgrade/' . (int)$s['id_group_location'] . '/' . (int)$s['id_created_lifts'] . '/' . (int)$currentResortID . '/' . (int)$next_level);
                        $gain_pct     = ($cur_tp > 0) ? round(($max_tp - $cur_tp) / $cur_tp * 100) : 0;

                        if ($can_upgrade) {
                            $alert_class = 'alert alert-warning p-2';
                            $icon        = '⚡';
                        } else {
                            $alert_class = 'p-3 border border-base-300 rounded bg-base-100-secondary';
                            $icon        = '🔄';
                        }
                        ?>
                        <div class="p-3 border border-base-300 rounded bg-base-100 <?php echo $alert_class; ?> flex flex-wrap justify-between items-center gap-2">
                            <div>
                                <strong><?php echo $icon . ' ' . $lift_name; ?></strong>
                                <span class="ml-2 text-base-content/60 small">
                                    <?php echo $this->lang->line('lift')['network_capacity_current']; ?>
                                    <strong><?php echo $cur_tp; ?></strong>
                                    <?php echo $this->lang->line('lift')['network_capacity_riders']; ?>
                                </span>
                                <?php if ($can_upgrade): ?>
                                    <span class="ml-2 badge badge-warning">
                                        <?php echo $this->lang->line('lift')['network_capacity_upgrade_tip']; ?>
                                        <strong><?php echo $max_tp; ?></strong>
                                        <?php echo $this->lang->line('lift')['network_capacity_riders']; ?>
                                        (+<?php echo $gain_pct; ?>%)
                                    </span>
                                <?php else: ?>
                                    <span class="ml-2 text-base-content/60 small">
                                        — <?php echo $this->lang->line('lift')['network_capacity_replace_tip']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($can_upgrade): ?>
                                <a href="<?php echo $upgrade_url; ?>" class="btn btn-warning btn-sm">
                                    <?php echo $this->lang->line('lift')['network_capacity_upgrade_btn']; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Satisfaction note -->
            <div class="alert alert-secondary mt-4">
                <small><?php echo htmlspecialchars($this->lang->line('lift')['network_satisfaction_note'], ENT_QUOTES, 'UTF-8'); ?></small>
            </div>

        <?php endif; ?>

    </div>
</div>
