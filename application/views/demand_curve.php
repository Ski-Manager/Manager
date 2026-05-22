<div class="w-full">
    <?php echo '<h2 class="h2">' . $this->lang->line('demand_curve')['title'] . '</h2>'; ?>
    <p class="text-base-content/60"><?php echo $this->lang->line('demand_curve')['intro']; ?></p>

    <!-- ============================================================ -->
    <!-- Active demand multipliers for this resort today              -->
    <!-- ============================================================ -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-4">
        <h4 class="h4"><?php echo $this->lang->line('demand_curve')['current_factors']; ?></h4>
        <div class="overflow-x-auto">
            <table class="table table-zebra" aria-label="<?php echo $this->lang->line('demand_curve')['current_factors']; ?>">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $this->lang->line('demand_curve')['factor']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['value']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['multiplier']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['effect']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Weather -->
                    <tr>
                        <td><i class="fa-solid fa-cloud-sun" aria-hidden="true"></i> <?php echo $this->lang->line('demand_curve')['weather']; ?></td>
                        <td><?php echo $weather_name !== '' ? htmlspecialchars($weather_name) : $this->lang->line('demand_curve')['unknown']; ?></td>
                        <td>
                            <?php
                            $weather_pct = round(($bonus_weather - 1) * 100);
                            $badge_cls = $bonus_weather > 1 ? 'success' : ($bonus_weather < 1 ? 'error' : 'neutral');
                            echo '<span class="badge badge-' . $badge_cls . '">×' . number_format($bonus_weather, 2) . '</span>';
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($weather_pct > 0) echo '<span class="text-success">+' . $weather_pct . '%</span>';
                            elseif ($weather_pct < 0) echo '<span class="text-error">' . $weather_pct . '%</span>';
                            else echo '<span class="text-base-content/60">0%</span>';
                            ?>
                        </td>
                    </tr>

                    <!-- Reputation -->
                    <tr>
                        <td><i class="fa-solid fa-star" aria-hidden="true"></i> <?php echo $this->lang->line('demand_curve')['reputation']; ?></td>
                        <td><?php echo number_format($reputation, 0, ',', ' '); ?> pts</td>
                        <td>
                            <?php
                            $rep_badge = $bonus_reputation >= 1.2 ? 'success' : ($bonus_reputation >= 1.0 ? 'info' : 'warning');
                            echo '<span class="badge badge-' . $rep_badge . '">×' . number_format($bonus_reputation, 2) . '</span>';
                            ?>
                        </td>
                        <td>
                            <?php
                            $rep_pct = round(($bonus_reputation - 1) * 100);
                            if ($rep_pct > 0) echo '<span class="text-success">+' . $rep_pct . '%</span>';
                            elseif ($rep_pct < 0) echo '<span class="text-error">' . $rep_pct . '%</span>';
                            else echo '<span class="text-base-content/60">0%</span>';
                            ?>
                        </td>
                    </tr>

                    <!-- Peak season / Holiday -->
                    <tr>
                        <td><i class="fa-regular fa-calendar-days" aria-hidden="true"></i> <?php echo $this->lang->line('demand_curve')['peak_season']; ?></td>
                        <td><?php echo $this->lang->line('demand_curve')['day_of_season']; ?> <?php echo htmlspecialchars((string)$day_of_season); ?>/135</td>
                        <td>
                            <?php
                            $ps_badge = $bonus_peak_season >= 1.2 ? 'success' : ($bonus_peak_season >= 1.0 ? 'info' : ($bonus_peak_season >= 0.85 ? 'warning' : 'neutral'));
                            echo '<span class="badge badge-' . $ps_badge . '">×' . number_format($bonus_peak_season, 2) . '</span>';
                            ?>
                        </td>
                        <td>
                            <?php
                            $ps_pct = round(($bonus_peak_season - 1) * 100);
                            if ($ps_pct > 0) echo '<span class="text-success">+' . $ps_pct . '%</span>';
                            elseif ($ps_pct < 0) echo '<span class="text-error">' . $ps_pct . '%</span>';
                            else echo '<span class="text-base-content/60">0%</span>';
                            ?>
                        </td>
                    </tr>

                    <!-- Price (informational) -->
                    <tr>
                        <td><i class="fa-solid fa-tag" aria-hidden="true"></i> <?php echo $this->lang->line('demand_curve')['price']; ?></td>
                        <td><?php echo $this->lang->line('demand_curve')['daily']; ?> €<?php echo $skipass_daily; ?> / <?php echo $this->lang->line('demand_curve')['weekly']; ?> €<?php echo $skipass_weekly; ?></td>
                        <td><span class="badge badge-info"><?php echo $this->lang->line('demand_curve')['price_in_slope_calc']; ?></span></td>
                        <td><?php echo $this->lang->line('demand_curve')['price_tooltip']; ?></td>
                    </tr>

                    <!-- Competition (informational) -->
                    <tr>
                        <td><i class="fa-solid fa-users" aria-hidden="true"></i> <?php echo $this->lang->line('demand_curve')['competition']; ?></td>
                        <td>—</td>
                        <td><span class="badge badge-neutral"><?php echo $this->lang->line('demand_curve')['coming_soon']; ?></span></td>
                        <td><?php echo $this->lang->line('demand_curve')['competition_tooltip']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Combined multiplier -->
        <?php
        $combined = $bonus_weather * $bonus_reputation * $bonus_peak_season;
        $combined_pct = round(($combined - 1) * 100);
        $combined_cls = $combined >= 1 ? 'success' : 'error';
        ?>
        <div class="alert alert-<?php echo $combined_cls; ?>" role="alert">
            <strong><?php echo $this->lang->line('demand_curve')['combined_multiplier']; ?>:</strong>
            ×<?php echo number_format($combined, 3); ?>
            (<?php echo $combined_pct >= 0 ? '+' : ''; ?><?php echo $combined_pct; ?>%)
            <small class="block text-base-content/60 mt-1"><?php echo $this->lang->line('demand_curve')['combined_note']; ?></small>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- Peak-season schedule table                                    -->
    <!-- ============================================================ -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <h4 class="h4"><?php echo $this->lang->line('demand_curve')['season_schedule']; ?></h4>
        <p class="text-base-content/60"><?php echo $this->lang->line('demand_curve')['season_schedule_intro']; ?></p>
        <div class="overflow-x-auto">
            <table class="table table-zebra" aria-label="<?php echo $this->lang->line('demand_curve')['season_schedule']; ?>">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $this->lang->line('demand_curve')['period']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['days']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['multiplier']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['effect']; ?></th>
                        <th><?php echo $this->lang->line('demand_curve')['status']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $day_int = (int)$day_of_season;
                    foreach ($peak_season_schedule as $row):
                        $is_current = ($day_int >= $row['day_start'] && $day_int <= $row['day_end']);
                        $pct = round(($row['multiplier'] - 1) * 100);
                        ?>
                        <tr class="<?php echo $is_current ? 'table-primary font-bold' : ''; ?>">
                            <td><?php echo $this->lang->line('demand_curve')[$row['label_key']]; ?></td>
                            <td><?php echo $row['day_start']; ?>–<?php echo $row['day_end']; ?></td>
                            <td><span class="badge badge-<?php echo $row['badge']; ?>">×<?php echo number_format($row['multiplier'], 2); ?></span></td>
                            <td>
                                <?php
                                if ($pct > 0) echo '<span class="text-success">+' . $pct . '%</span>';
                                elseif ($pct < 0) echo '<span class="text-error">' . $pct . '%</span>';
                                else echo '<span class="text-base-content/60">0%</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($is_current): ?>
                                    <span class="badge badge-primary"><?php echo $this->lang->line('demand_curve')['current']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-base-content/60 small"><?php echo $this->lang->line('demand_curve')['pricing_tip']; ?></p>
    </div>
</div>
