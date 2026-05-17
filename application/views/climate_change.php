<div class="w-full">
    <h2 class="h2"><?php echo $this->lang->line('climate_change')['title']; ?></h2>
    <p class="text-base-content/60"><?php echo $this->lang->line('climate_change')['intro']; ?></p>

    <!-- Climate Level Card -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="grid grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <h4 class="h4"><i class="fa-solid fa-temperature-sun" aria-hidden="true"></i>
                    <?php echo $this->lang->line('climate_change')['climate_level_label']; ?>:
                    <span class="badge <?php echo $climate->climate_level >= 7 ? 'bg-error' : ($climate->climate_level >= 4 ? 'bg-warning' : 'bg-success'); ?>">
                        <?php echo $climate->climate_level; ?> / 10
                    </span>
                </h4>
                <p class="text-base-content/60 small"><?php echo $this->lang->line('climate_change')['current_season_label']; ?>: <?php echo $current_season; ?></p>
                <p><?php echo $this->lang->line('climate_change')['level_desc_' . min($climate->climate_level, 3)]; ?></p>

                <?php
                // Progress bar
                $pct = $climate->climate_level * 10;
                $bar_class = $climate->climate_level >= 7 ? 'progress-error' : ($climate->climate_level >= 4 ? 'progress-warning' : 'progress-success');
                ?>
                <div class="flex items-center gap-2 mb-3">
                    <progress class="progress <?php echo $bar_class; ?>" value="<?php echo $pct; ?>" max="100" style="flex:1;height:22px;"></progress>
                    <span class="text-sm font-semibold"><?php echo $pct; ?>%</span>
                </div>
            </div>

            <!-- Active effects -->
            <div class="md:col-span-6">
                <h5 class="h5"><?php echo $this->lang->line('climate_change')['active_effects']; ?></h5>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td><i class="fa-solid fa-cloud-snow text-primary" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['effect_snow_penalty']; ?>
                            </td>
                            <td class="<?php echo $effects['winter_snow_penalty'] > 0 ? 'text-error' : 'text-base-content/60'; ?>">
                                <?php echo $effects['winter_snow_penalty'] > 0 ? '-' . $effects['winter_snow_penalty'] . ' cm' : $this->lang->line('climate_change')['no_effect']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa-solid fa-coins text-warning" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['effect_cost_mult']; ?>
                            </td>
                            <td class="<?php echo $effects['snowmaking_cost_mult'] > 1.0 ? 'text-error' : 'text-base-content/60'; ?>">
                                <?php echo $effects['snowmaking_cost_mult'] > 1.0 ? 'x' . number_format($effects['snowmaking_cost_mult'], 2) : $this->lang->line('climate_change')['no_effect']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa-solid fa-mountain text-neutral-content" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['effect_glacier']; ?>
                            </td>
                            <td class="<?php echo $effects['glacier_loss'] > 0 ? 'text-error' : 'text-base-content/60'; ?>">
                                <?php echo $effects['glacier_loss'] > 0 ? '-' . $effects['glacier_loss'] . ' pts/day' : $this->lang->line('climate_change')['no_effect']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa-regular fa-calendar text-info" aria-hidden="true"></i>
                                <?php echo $this->lang->line('climate_change')['effect_season']; ?>
                            </td>
                            <td class="<?php echo $effects['season_length_penalty'] > 0 ? 'text-error' : 'text-base-content/60'; ?>">
                                <?php echo $effects['season_length_penalty'] > 0 ? '-' . $effects['season_length_penalty'] . ' days' : $this->lang->line('climate_change')['no_effect']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Adaptation Investments -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <h4 class="h4"><?php echo $this->lang->line('climate_change')['adaptation_title']; ?></h4>
        <p><?php echo $this->lang->line('climate_change')['adaptation_intro']; ?></p>

        <div class="grid grid-cols-12 gap-3">

            <!-- Snowmaking investment -->
            <div class="md:col-span-4 mb-3">
                <div class="card h-full <?php echo $climate->snowmaking_invest ? 'border-success' : ''; ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-snowflake2 text-primary" aria-hidden="true"></i>
                            <?php echo $this->lang->line('climate_change')['snowmaking_invest_label']; ?>
                        </h5>
                        <p class="card-text small"><?php echo $this->lang->line('climate_change')['snowmaking_invest_desc']; ?></p>
                        <p class="text-base-content/60 small"><?php echo $this->lang->line('climate_change')['cost']; ?>: <?php echo number_format($invest_costs['snowmaking'], 0, '.', ' '); ?> €</p>
                        <?php if ($climate->snowmaking_invest): ?>
                            <button class="btn btn-success w-full" disabled><i class="fa-regular fa-circle-check" aria-hidden="true"></i> <?php echo $this->lang->line('climate_change')['invested']; ?></button>
                        <?php else: ?>
                            <button class="btn btn-primary w-full invest-btn" data-type="snowmaking_invest">
                                <?php echo $this->lang->line('climate_change')['invest_btn']; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Altitude investment -->
            <div class="md:col-span-4 mb-3">
                <div class="card h-full <?php echo $climate->altitude_invest ? 'border-success' : ''; ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-chart-bar-steps text-warning" aria-hidden="true"></i>
                            <?php echo $this->lang->line('climate_change')['altitude_invest_label']; ?>
                        </h5>
                        <p class="card-text small"><?php echo $this->lang->line('climate_change')['altitude_invest_desc']; ?></p>
                        <p class="text-base-content/60 small"><?php echo $this->lang->line('climate_change')['cost']; ?>: <?php echo number_format($invest_costs['altitude'], 0, '.', ' '); ?> €</p>
                        <?php if ($climate->altitude_invest): ?>
                            <button class="btn btn-success w-full" disabled><i class="fa-regular fa-circle-check" aria-hidden="true"></i> <?php echo $this->lang->line('climate_change')['invested']; ?></button>
                        <?php else: ?>
                            <button class="btn btn-warning w-full invest-btn" data-type="altitude_invest">
                                <?php echo $this->lang->line('climate_change')['invest_btn']; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Diversify investment -->
            <div class="md:col-span-4 mb-3">
                <div class="card h-full <?php echo $climate->diversify_invest ? 'border-success' : ''; ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-shuffle text-success" aria-hidden="true"></i>
                            <?php echo $this->lang->line('climate_change')['diversify_invest_label']; ?>
                        </h5>
                        <p class="card-text small"><?php echo $this->lang->line('climate_change')['diversify_invest_desc']; ?></p>
                        <p class="text-base-content/60 small"><?php echo $this->lang->line('climate_change')['cost']; ?>: <?php echo number_format($invest_costs['diversify'], 0, '.', ' '); ?> €</p>
                        <?php if ($climate->diversify_invest): ?>
                            <button class="btn btn-success w-full" disabled><i class="fa-regular fa-circle-check" aria-hidden="true"></i> <?php echo $this->lang->line('climate_change')['invested']; ?></button>
                        <?php else: ?>
                            <button class="btn btn-success w-full invest-btn" data-type="diversify_invest">
                                <?php echo $this->lang->line('climate_change')['invest_btn']; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div><!-- .row -->
    </div><!-- adaptation card -->

</div><!-- .w-full -->

<script type="text/javascript">
var Settings = (typeof Settings === 'object' && Settings !== null) ? Settings : {};
Settings.climate_invest_failed = <?php echo json_encode($this->lang->line('climate_change')['invest_failed']); ?>;
Settings.climate_invested      = <?php echo json_encode($this->lang->line('climate_change')['invested']); ?>;
</script>
