<div class="w-full">

    <h2 class="h2">🧪 <?php echo $this->lang->line('rd')['page_title']; ?></h2>
    <p><?php echo $this->lang->line('rd')['intro']; ?></p>

    <?php
    if (isset($infoMessage) && $infoMessage !== '') {
        if ($infoMessage === 'not_enough_money') {
            echo '<div class="alert alert-error text-center">' . $this->lang->line('home')['not_enough_money'] . '</div>';
        } elseif ($infoMessage === 'rd_started') {
            echo '<div class="alert alert-success">' . $this->lang->line('rd')['msg_started'] . '</div>';
        } elseif ($infoMessage === 'rd_already_active') {
            echo '<div class="alert alert-warning">' . $this->lang->line('rd')['msg_already_active'] . '</div>';
        } else {
            echo '<div class="alert alert-info">' . htmlspecialchars($infoMessage) . '</div>';
        }
    }
    ?>

    <!-- R&D Project Cards -->
    <div class="grid gap-3">
    <?php foreach ($projects as $key => $proj): ?>
        <?php
        $status = $proj['status'];

        if ($status === 'completed') {
            $card_class  = 'border-success';
            $badge_class = 'bg-success';
            $badge_label = $this->lang->line('rd')['status_completed'];
        } elseif ($status === 'in_progress') {
            $card_class  = 'border-warning';
            $badge_class = 'bg-warning';
            $badge_label = $this->lang->line('rd')['status_in_progress'];
        } elseif ($status === 'failed') {
            $card_class  = 'border-error';
            $badge_class = 'bg-error';
            $badge_label = $this->lang->line('rd')['status_failed'];
        } else {
            $card_class  = 'border-primary';
            $badge_class = 'bg-neutral';
            $badge_label = $this->lang->line('rd')['status_not_started'];
        }

        $bonus_label = ($proj['bonus_type'] === 'reputation')
            ? '+' . $proj['bonus_value'] . ' ' . $this->lang->line('rd')['bonus_reputation_night']
            : '-' . number_format($proj['bonus_value'], 0, ',', ' ') . ' € ' . $this->lang->line('rd')['bonus_cost_night'];
        ?>
        <div class="md:col-span-6 lg:col-span-4">
            <div class="card h-full <?php echo $card_class; ?>">
                <div class="card-header flex justify-between items-center">
                    <strong><?php echo htmlspecialchars($proj['name']); ?></strong>
                    <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_label; ?></span>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo htmlspecialchars($proj['description']); ?></p>

                    <ul class="list-none small mb-3">
                        <li>
                            <strong><?php echo $this->lang->line('rd')['col_bonus']; ?>:</strong>
                            <span class="text-success"><?php echo $bonus_label; ?></span>
                        </li>
                        <li>
                            <strong><?php echo $this->lang->line('rd')['col_cost_normal']; ?>:</strong>
                            <?php echo number_format($proj['cost'], 0, ',', ' '); ?> €
                            &nbsp;|&nbsp;
                            <strong><?php echo $this->lang->line('rd')['col_duration_normal']; ?>:</strong>
                            <?php echo $proj['duration_days']; ?> <?php echo $this->lang->line('rd')['duration_days']; ?>
                            &nbsp;|&nbsp;
                            <strong><?php echo $this->lang->line('rd')['col_failure_normal']; ?>:</strong>
                            <?php echo $proj['failure_chance_normal']; ?>%
                        </li>
                        <li class="text-error">
                            <strong><?php echo $this->lang->line('rd')['col_cost_rush']; ?>:</strong>
                            <?php echo number_format($proj['rush_cost'], 0, ',', ' '); ?> €
                            &nbsp;|&nbsp;
                            <strong><?php echo $this->lang->line('rd')['col_duration_rush']; ?>:</strong>
                            <?php echo $proj['rush_duration_days']; ?> <?php echo $this->lang->line('rd')['duration_days']; ?>
                            &nbsp;|&nbsp;
                            <strong><?php echo $this->lang->line('rd')['col_failure_rush']; ?>:</strong>
                            <?php echo $proj['failure_chance_rush']; ?>%
                        </li>
                        <?php if ($status === 'in_progress' && $proj['finish_at']): ?>
                        <li>
                            <strong><?php echo $this->lang->line('rd')['finish_at']; ?>:</strong>
                            <?php echo htmlspecialchars($proj['finish_at']); ?> UTC
                            <?php if ($proj['rushed']): ?>
                                <span class="badge badge-error ml-1"><?php echo $this->lang->line('rd')['mode_rush']; ?></span>
                            <?php endif; ?>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <?php if ($status === 'completed'): ?>
                        <span class="text-success font-bold">✔ <?php echo $this->lang->line('rd')['status_completed']; ?></span>
                    <?php elseif ($status === 'in_progress'): ?>
                        <?php
                        $ts_start  = $proj['started_at'] ? strtotime($proj['started_at']) : 0;
                        $ts_finish = $proj['finish_at']  ? strtotime($proj['finish_at'])  : 0;
                        $ts_now    = time();
                        if ($ts_start > 0 && $ts_finish > $ts_start) {
                            $rd_pct = min(100, max(0, round(($ts_now - $ts_start) / ($ts_finish - $ts_start) * 100)));
                        } else {
                            $rd_pct = 0;
                        }
                        ?>
                        <progress class="progress progress-warning w-full mb-1" style="height:12px;" title="<?php echo $rd_pct; ?>%" value="<?php echo $rd_pct; ?>" max="100"></progress>
                        <span class="text-warning font-bold">⏳ <?php echo $this->lang->line('rd')['status_in_progress']; ?> (<?php echo $rd_pct; ?>%)</span>
                    <?php elseif ($status === 'failed'): ?>
                        <p class="text-error mb-2">❌ <?php echo $this->lang->line('rd')['msg_failed_info']; ?></p>
                        <!-- Allow retry -->
                        <div class="flex gap-2 flex-wrap">
                            <a href="<?php echo base_url() . 'rd_controller/start_project/' . $currentResortID . '/' . htmlspecialchars($key) . '/normal'; ?>"
                               class="btn btn-primary btn-sm"
                               onclick="return confirm('<?php echo addslashes($this->lang->line('rd')['confirm_normal']) . ' ' . number_format($proj['cost'], 0, ',', ' ') . ' €?'; ?>')">
                                <?php echo $this->lang->line('rd')['btn_normal']; ?>
                                (<?php echo number_format($proj['cost'], 0, ',', ' '); ?> €)
                            </a>
                            <a href="<?php echo base_url() . 'rd_controller/start_project/' . $currentResortID . '/' . htmlspecialchars($key) . '/rush'; ?>"
                               class="btn btn-error btn-sm"
                               onclick="return confirm('<?php echo addslashes($this->lang->line('rd')['confirm_rush']) . ' ' . number_format($proj['rush_cost'], 0, ',', ' ') . ' €. ' . $this->lang->line('rd')['rush_warning'] . '?'; ?>')">
                                🚀 <?php echo $this->lang->line('rd')['btn_rush']; ?>
                                (<?php echo number_format($proj['rush_cost'], 0, ',', ' '); ?> €)
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex gap-2 flex-wrap">
                            <a href="<?php echo base_url() . 'rd_controller/start_project/' . $currentResortID . '/' . htmlspecialchars($key) . '/normal'; ?>"
                               class="btn btn-primary btn-sm <?php echo ($cash < $proj['cost']) ? 'disabled' : ''; ?>"
                               onclick="return confirm('<?php echo addslashes($this->lang->line('rd')['confirm_normal']) . ' ' . number_format($proj['cost'], 0, ',', ' ') . ' €?'; ?>')">
                                <?php echo $this->lang->line('rd')['btn_normal']; ?>
                                (<?php echo number_format($proj['cost'], 0, ',', ' '); ?> €)
                            </a>
                            <a href="<?php echo base_url() . 'rd_controller/start_project/' . $currentResortID . '/' . htmlspecialchars($key) . '/rush'; ?>"
                               class="btn btn-error btn-sm <?php echo ($cash < $proj['rush_cost']) ? 'disabled' : ''; ?>"
                               onclick="return confirm('<?php echo addslashes($this->lang->line('rd')['confirm_rush']) . ' ' . number_format($proj['rush_cost'], 0, ',', ' ') . ' €. ' . $this->lang->line('rd')['rush_warning'] . '?'; ?>')">
                                🚀 <?php echo $this->lang->line('rd')['btn_rush']; ?>
                                (<?php echo number_format($proj['rush_cost'], 0, ',', ' '); ?> €)
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- Failure/accident info note -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mt-4" style="max-width:680px;">
        <h5 class="h5">⚠️ <?php echo $this->lang->line('rd')['risk_title']; ?></h5>
        <p class="mb-0"><?php echo $this->lang->line('rd')['risk_desc']; ?></p>
    </div>

</div>
