<div class="w-full">

    <h2 class="h2"><?php echo $this->lang->line($tree_key)['page_title']; ?></h2>
    <p><?php echo $this->lang->line($tree_key)['intro']; ?></p>

    <?php
    if (isset($infoMessage) && $infoMessage != '') {
        $msg_key = $infoMessage;
        if ($msg_key === 'not_enough_money') {
            echo '<div role="alert" class="alert alert-error text-center">' . $this->lang->line('home')['not_enough_money'] . '</div>';
        } elseif ($msg_key === 'upgrade_started') {
            echo '<div role="alert" class="alert alert-success">' . $this->lang->line($tree_key)['msg_started'] . '</div>';
        } elseif ($msg_key === 'upgrade_already_researched') {
            echo '<div role="alert" class="alert alert-warning">' . $this->lang->line($tree_key)['msg_already'] . '</div>';
        } elseif ($msg_key === 'upgrade_prereq_not_met') {
            echo '<div role="alert" class="alert alert-warning">' . $this->lang->line($tree_key)['msg_prereq'] . '</div>';
        } else {
            echo '<div role="alert" class="alert alert-info">' . htmlspecialchars($msg_key) . '</div>';
        }
    }
    ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($upgrades as $key => $upgrade): ?>
        <?php
        $status     = $upgrade['status'];
        $prereq_met = $upgrade['prereq_met'];

        if ($status === 'completed') {
            $card_border = 'border-2 border-success';
            $badge_class = 'badge badge-success';
            $badge_label = $this->lang->line($tree_key)['status_completed'];
        } elseif ($status === 'in_progress') {
            $card_border = 'border-2 border-warning';
            $badge_class = 'badge badge-warning';
            $badge_label = $this->lang->line($tree_key)['status_in_progress'];
        } else {
            $card_border = $prereq_met ? 'border-2 border-primary' : 'border-2 border-neutral';
            $badge_class = 'badge badge-neutral';
            $badge_label = $this->lang->line($tree_key)['status_not_started'];
        }
        ?>
        <div class="card bg-base-100 shadow h-full <?php echo $card_border; ?>">
            <div class="card-body">
                <div class="flex justify-between items-center mb-2">
                    <strong><?php echo htmlspecialchars($upgrade['name']); ?></strong>
                    <span class="<?php echo $badge_class; ?>"><?php echo $badge_label; ?></span>
                </div>
                <p><?php echo htmlspecialchars($upgrade['description']); ?></p>

                <ul class="list-none pl-0 text-sm mb-2 space-y-1">
                    <li>
                        <strong><?php echo $this->lang->line($tree_key)['col_cost']; ?>:</strong>
                        <?php echo number_format($upgrade['cost'], 0, ',', ' '); ?> €
                    </li>
                    <li>
                        <strong><?php echo $this->lang->line($tree_key)['col_duration']; ?>:</strong>
                        <?php echo $upgrade['duration']; ?> <?php echo $this->lang->line($tree_key)['duration_days']; ?>
                    </li>
                    <?php if (!is_null($upgrade['prerequisite']) && isset($upgrades[$upgrade['prerequisite']])): ?>
                    <li>
                        <strong><?php echo $this->lang->line($tree_key)['prereq_needed']; ?></strong>
                        <?php echo htmlspecialchars($upgrades[$upgrade['prerequisite']]['name']); ?>
                    </li>
                    <?php endif; ?>
                    <?php if ($status === 'in_progress' && $upgrade['finish_at']): ?>
                    <li>
                        <strong><?php echo $this->lang->line($tree_key)['finish_at']; ?>:</strong>
                        <?php echo htmlspecialchars($upgrade['finish_at']); ?> UTC
                    </li>
                    <?php endif; ?>
                </ul>

                <?php if ($status === 'completed'): ?>
                    <span class="text-success font-bold">✔ <?php echo $this->lang->line($tree_key)['status_completed']; ?></span>
                <?php elseif ($status === 'in_progress'): ?>
                    <span class="text-warning font-bold">⏳ <?php echo $this->lang->line($tree_key)['status_in_progress']; ?></span>
                <?php elseif ($prereq_met): ?>
                    <a href="<?php echo base_url() . $tree_key . '_controller/start_research/' . $currentResortID . '/' . htmlspecialchars($key); ?>"
                       class="btn btn-primary btn-sm"
                       onclick="return confirm('<?php echo addslashes($this->lang->line($tree_key)['col_cost']) . ': ' . number_format($upgrade['cost'], 0, ',', ' ') . ' €. ' . ($this->lang->line('home')['confirm'] ?? 'Confirm?'); ?>')">
                        <?php echo $this->lang->line($tree_key)['btn_research']; ?>
                    </a>
                <?php else: ?>
                    <button class="btn btn-neutral btn-sm" disabled>
                        🔒 <?php echo $this->lang->line($tree_key)['btn_locked']; ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

</div>
