<div class="w-full">

    <h2 class="h2"><?php echo $this->lang->line('lift_tech')['page_title']; ?></h2>
    <p><?php echo $this->lang->line('lift_tech')['intro']; ?></p>

    <?php
    // Display flash / info messages
    if (isset($infoMessage) && $infoMessage != '') {
        $msg_key = $infoMessage;
        if ($msg_key === 'not_enough_money') {
            echo '<div class="alert alert-error text-center">' . $this->lang->line('home')['not_enough_money'] . '</div>';
        } elseif ($msg_key === 'lift_tech_started') {
            echo '<div class="alert alert-success">' . $this->lang->line('lift_tech')['msg_started'] . '</div>';
        } elseif ($msg_key === 'lift_tech_already_researched') {
            echo '<div class="alert alert-warning">' . $this->lang->line('lift_tech')['msg_already'] . '</div>';
        } elseif ($msg_key === 'lift_tech_prereq_not_met') {
            echo '<div class="alert alert-warning">' . $this->lang->line('lift_tech')['msg_prereq'] . '</div>';
        } else {
            echo '<div class="alert alert-info">' . htmlspecialchars($msg_key) . '</div>';
        }
    }
    ?>

    <!-- Research Tree Cards -->
    <div class="grid gap-3">
    <?php foreach ($techs as $key => $tech): ?>
        <?php
        $status      = $tech['status'];
        $prereq_met  = $tech['prereq_met'];

        if ($status === 'completed') {
            $card_class  = 'border-success';
            $badge_class = 'badge-success';
            $badge_label = $this->lang->line('lift_tech')['status_completed'];
        } elseif ($status === 'in_progress') {
            $card_class  = 'border-warning';
            $badge_class = 'badge-warning';
            $badge_label = $this->lang->line('lift_tech')['status_in_progress'];
        } else {
            $card_class  = $prereq_met ? 'border-primary' : 'border-neutral';
            $badge_class = 'badge-neutral';
            $badge_label = $this->lang->line('lift_tech')['status_not_started'];
        }
        ?>
        <div class="md:col-span-6 lg:col-span-4">
            <div class="card h-full <?php echo $card_class; ?>">
                <div class="card-header flex justify-between items-center">
                    <strong><?php echo htmlspecialchars($tech['name']); ?></strong>
                    <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_label; ?></span>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo htmlspecialchars($tech['description']); ?></p>

                    <ul class="list-none small mb-2">
                        <li>
                            <strong><?php echo $this->lang->line('lift_tech')['col_cost']; ?>:</strong>
                            <?php echo number_format($tech['cost'], 0, ',', ' '); ?> €
                        </li>
                        <li>
                            <strong><?php echo $this->lang->line('lift_tech')['col_duration']; ?>:</strong>
                            <?php echo $tech['duration']; ?> <?php echo $this->lang->line('lift_tech')['duration_days']; ?>
                        </li>
                        <?php if (!is_null($tech['prerequisite']) && isset($techs[$tech['prerequisite']])): ?>
                        <li>
                            <strong><?php echo $this->lang->line('lift_tech')['prereq_needed']; ?></strong>
                            <?php echo htmlspecialchars($techs[$tech['prerequisite']]['name']); ?>
                        </li>
                        <?php endif; ?>
                        <?php if ($status === 'in_progress' && $tech['finish_at']): ?>
                        <li>
                            <strong><?php echo $this->lang->line('lift_tech')['finish_at']; ?>:</strong>
                            <?php echo htmlspecialchars($tech['finish_at']); ?> UTC
                        </li>
                        <?php endif; ?>
                    </ul>

                    <?php if ($status === 'completed'): ?>
                        <span class="text-success font-bold">✔ <?php echo $this->lang->line('lift_tech')['status_completed']; ?></span>
                    <?php elseif ($status === 'in_progress'): ?>
                        <?php
                        $ts_start  = $tech['started_at'] ? strtotime($tech['started_at']) : 0;
                        $ts_finish = $tech['finish_at']  ? strtotime($tech['finish_at'])  : 0;
                        $ts_now    = time();
                        if ($ts_start > 0 && $ts_finish > $ts_start) {
                            $lt_pct = min(100, max(0, round(($ts_now - $ts_start) / ($ts_finish - $ts_start) * 100)));
                        } else {
                            $lt_pct = 0;
                        }
                        ?>
                        <progress class="progress progress-warning w-full mb-1" style="height:12px;" title="<?php echo $lt_pct; ?>%" value="<?php echo $lt_pct; ?>" max="100"></progress>
                        <span class="text-warning font-bold">⏳ <?php echo $this->lang->line('lift_tech')['status_in_progress']; ?> (<?php echo $lt_pct; ?>%)</span>
                    <?php elseif ($prereq_met): ?>
                        <a href="<?php echo base_url() . 'lift_tech_controller/start_research/' . $currentResortID . '/' . htmlspecialchars($key); ?>"
                           class="btn btn-primary btn-sm"
                           onclick="return confirm('<?php echo addslashes($this->lang->line('lift_tech')['col_cost']) . ': ' . number_format($tech['cost'], 0, ',', ' ') . ' €. ' . $this->lang->line('home')['confirm'] ?? 'Confirm?'; ?>')">
                            <?php echo $this->lang->line('lift_tech')['btn_research']; ?>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-sm" disabled>
                            🔒 <?php echo $this->lang->line('lift_tech')['btn_locked']; ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

</div>

<!-- Lift Technology Generations -->
<div class="w-full mt-5">

    <h3 class="h3"><?php echo $this->lang->line('lift_tech')['generations_title']; ?></h3>
    <p><?php echo $this->lang->line('lift_tech')['generations_intro']; ?></p>

    <div class="overflow-x-auto">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th><?php echo $this->lang->line('lift_tech')['gen_col_name']; ?></th>
                    <th class="text-right"><?php echo $this->lang->line('lift_tech')['gen_col_cost']; ?></th>
                    <th class="text-right"><?php echo $this->lang->line('lift_tech')['gen_col_capacity']; ?></th>
                    <th class="text-right"><?php echo $this->lang->line('lift_tech')['gen_col_maintenance']; ?></th>
                    <th class="text-right"><?php echo $this->lang->line('lift_tech')['gen_col_breakdown']; ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (LIFT_GENERATIONS as $gen_key => $gen): ?>
                <?php $gen_name = ($site_lang === 'french') ? $gen['name_french'] : $gen['name_english']; ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($gen_name); ?></strong></td>
                    <td class="text-right"><?php echo number_format($gen['cost'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($gen['capacity'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($gen['maintenance_rate'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo $gen['breakdown_risk']; ?> %</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
