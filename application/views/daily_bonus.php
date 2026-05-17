<div class="w-full daily-bonus-page">

<!-- ===== Page header ===== -->
<div class="daily-bonus-header mb-4">
    <h2 class="h2 mb-1"><?php echo $this->lang->line('daily_bonus')['title']; ?></h2>
    <p class="text-base-content/60 mb-0"><?php echo $this->lang->line('daily_bonus')['intro']; ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

    <!-- ===== Streak card ===== -->
    <div class="card daily-streak-card shadow-sm">
        <div class="card-body text-center">

            <div class="streak-fire-wrap mb-2">
                <span class="streak-fire-emoji"><?php echo $current_streak > 0 ? '🔥' : '💤'; ?></span>
            </div>

            <div class="streak-count <?php echo $current_streak >= 7 ? 'streak-count--hot' : ''; ?>">
                <?php echo $current_streak; ?>
            </div>
            <div class="streak-unit"><?php echo $this->lang->line('daily_bonus')['consecutive_days']; ?></div>

            <!-- streak progress bar toward next milestone -->
            <?php
                $milestones = [1, 3, 7, 14, 30];
                $next_milestone = null;
                foreach ($milestones as $m) {
                    if ($current_streak < $m) { $next_milestone = $m; break; }
                }
                if ($next_milestone !== null):
                    $prev_milestone = 0;
                    foreach ($milestones as $m) { if ($m < $next_milestone) $prev_milestone = $m; }
                    $range = $next_milestone - $prev_milestone;
                    $progress = $range > 0 ? round(($current_streak - $prev_milestone) / $range * 100) : 100;
            ?>
            <div class="streak-progress-wrap mt-3 mb-1">
                <div class="streak-progress-track">
                    <div class="streak-progress-fill" style="width: <?php echo $progress; ?>%"></div>
                </div>
                <p class="streak-progress-label"><?php echo $current_streak; ?> / <?php echo $next_milestone; ?> days to next milestone</p>
            </div>
            <?php else: ?>
            <div class="badge badge-warning badge-lg mt-3">🏆 Max streak milestone reached!</div>
            <?php endif; ?>

            <?php if ($claim_success_cash !== NULL): ?>
            <div class="alert alert-success mt-3">
                <i class="fa-solid fa-fire mr-1"></i>
                <?php echo sprintf($this->lang->line('daily_bonus')['claim_success'], number_format($claim_success_cash), $claim_success_streak); ?>
            </div>
            <?php endif; ?>

        </div><!-- /.card-body -->
    </div><!-- /.daily-streak-card -->

    <!-- ===== Claim / status card ===== -->
    <div class="card shadow-sm">
        <div class="card-body flex flex-col justify-center">

            <?php if ($claimed_today): ?>

            <div class="claim-status-icon claim-status-icon--done">
                <i class="fa-regular fa-circle-check"></i>
            </div>
            <h4 class="text-center mb-1"><?php echo $this->lang->line('daily_bonus')['already_claimed_today']; ?></h4>
            <p class="text-center text-base-content/60 mb-0">
                <?php echo $this->lang->line('daily_bonus')['come_back_tomorrow']; ?>
            </p>
            <?php if ($last_bonus_date): ?>
            <p class="text-center text-base-content/50 small mt-2">
                <?php echo $this->lang->line('daily_bonus')['last_claimed']; ?>
                <strong><?php echo htmlspecialchars($last_bonus_date, ENT_QUOTES, 'UTF-8'); ?></strong>
            </p>
            <?php endif; ?>

            <?php else: ?>

            <div class="claim-status-icon claim-status-icon--pending">
                <i class="fa-solid fa-gift"></i>
            </div>
            <h4 class="text-center mb-2"><?php echo $this->lang->line('daily_bonus')['bonus_waiting']; ?></h4>

            <div class="claim-reward-preview mb-3">
                <span class="claim-reward-cash">+<?php echo number_format($next_bonus['cash']); ?> €</span>
                <?php if ($next_bonus['rep'] > 0): ?>
                <span class="claim-reward-sep">·</span>
                <span class="claim-reward-rep">+<?php echo $next_bonus['rep']; ?> <?php echo $this->lang->line('daily_bonus')['reputation']; ?></span>
                <?php endif; ?>
            </div>

            <?php echo form_open('daily_bonus_controller/claim'); ?>
                <button type="submit" class="btn btn-success btn-lg w-full">
                    <i class="fa-solid fa-fire mr-2"></i>
                    <?php echo $this->lang->line('daily_bonus')['claim_button']; ?>
                </button>
            <?php echo form_close(); ?>

            <p class="text-center text-base-content/50 small mt-2">
                <?php echo $this->lang->line('daily_bonus')['claim_on_next_login']; ?>
            </p>

            <?php endif; ?>

        </div>
    </div>

</div><!-- /.grid -->

<!-- ===== Bonus tiers table ===== -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="h4 mb-1"><?php echo $this->lang->line('daily_bonus')['bonus_tiers_title']; ?></h4>
        <p class="text-base-content/60 mb-3"><?php echo $this->lang->line('daily_bonus')['bonus_tiers_desc']; ?></p>

        <div class="daily-tiers-grid">
            <?php foreach ($bonus_tiers as $tier):
                $is_past    = ($tier['day'] < $current_streak);
                $is_current = ($tier['day'] === $current_streak);
                $is_next    = (!$claimed_today && $tier['day'] === ($current_streak + 1));
                $day_label  = ($tier['day'] == count($bonus_tiers)) ? $tier['day'].'+' : $tier['day'];
                $tier_class = 'daily-tier-card';
                if ($is_past)    $tier_class .= ' daily-tier-card--past';
                if ($is_current) $tier_class .= ' daily-tier-card--current';
                if ($is_next)    $tier_class .= ' daily-tier-card--next';
            ?>
            <div class="<?php echo $tier_class; ?>">
                <?php if ($is_current): ?>
                <span class="daily-tier-badge">★ Now</span>
                <?php elseif ($is_next): ?>
                <span class="daily-tier-badge daily-tier-badge--next">Next</span>
                <?php endif; ?>
                <div class="daily-tier-day"><?php echo $this->lang->line('daily_bonus')['day'].' '.$day_label; ?></div>
                <div class="daily-tier-cash"><?php echo number_format($tier['cash']); ?> €</div>
                <?php if ($tier['rep'] > 0): ?>
                <div class="daily-tier-rep">+<?php echo $tier['rep']; ?> rep</div>
                <?php endif; ?>
                <?php if ($is_past): ?>
                <div class="daily-tier-check"><i class="fa-solid fa-check"></i></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="text-base-content/50 small mt-3 mb-0">
            <?php echo $this->lang->line('daily_bonus')['streak_reset_notice']; ?>
        </p>
    </div>
</div>

</div><!-- /.daily-bonus-page -->
