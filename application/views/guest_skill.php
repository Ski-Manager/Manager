<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['guest_skill_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['guest_skill_page_intro'] . '</p>';

?>

<!-- ===== Skill Distribution card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:640px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['guest_skill_distribution_title']; ?></h4>
        <p class="text-base-content/60 small"><?php echo $this->lang->line('building')['guest_skill_seasons_played_label']; ?>
            <strong><?php echo (int)$seasons_played; ?></strong>
        </p>

        <!-- Stacked bar (flex-based) -->
        <div class="flex rounded-lg overflow-hidden mb-3 gap-px" style="height:28px;" title="<?php echo $this->lang->line('building')['guest_skill_distribution_title']; ?>">
            <?php if ($beginner_pct > 0): ?>
            <div class="flex items-center justify-center text-xs font-semibold bg-neutral text-neutral-content transition-all"
                 style="width:<?php echo $beginner_pct; ?>%;min-width:<?php echo $beginner_pct >= 10 ? '0' : '0'; ?>px;">
                <?php if ($beginner_pct >= 10): ?>
                    <?php echo $this->lang->line('building')['guest_skill_beginner']; ?> <?php echo $beginner_pct; ?>%
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ($intermediate_pct > 0): ?>
            <div class="flex items-center justify-center text-xs font-semibold bg-primary text-primary-content transition-all"
                 style="width:<?php echo $intermediate_pct; ?>%;">
                <?php if ($intermediate_pct >= 10): ?>
                    <?php echo $this->lang->line('building')['guest_skill_intermediate']; ?> <?php echo $intermediate_pct; ?>%
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ($advanced_pct > 0): ?>
            <div class="flex items-center justify-center text-xs font-semibold bg-success text-success-content transition-all"
                 style="width:<?php echo $advanced_pct; ?>%;">
                <?php if ($advanced_pct >= 10): ?>
                    <?php echo $this->lang->line('building')['guest_skill_advanced']; ?> <?php echo $advanced_pct; ?>%
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Detail table -->
        <table class="table table-sm" style="max-width:520px;">
            <thead class="">
                <tr>
                    <th><?php echo $this->lang->line('building')['guest_skill_level']; ?></th>
                    <th><?php echo $this->lang->line('building')['guest_skill_share']; ?></th>
                    <th><?php echo $this->lang->line('building')['guest_skill_revenue_bonus_label']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-neutral"><?php echo $this->lang->line('building')['guest_skill_beginner']; ?></span></td>
                    <td><strong><?php echo $beginner_pct; ?> %</strong></td>
                    <td>&mdash;</td>
                </tr>
                <tr>
                    <td><span class="badge badge-primary"><?php echo $this->lang->line('building')['guest_skill_intermediate']; ?></span></td>
                    <td><strong><?php echo $intermediate_pct; ?> %</strong></td>
                    <td>+<?php echo $intermediate_revenue_bonus; ?> %</td>
                </tr>
                <tr>
                    <td><span class="badge badge-success"><?php echo $this->lang->line('building')['guest_skill_advanced']; ?></span></td>
                    <td><strong><?php echo $advanced_pct; ?> %</strong></td>
                    <td>+<?php echo $advanced_revenue_bonus; ?> %</td>
                </tr>
            </tbody>
        </table>

        <p class="mb-0">
            <?php echo $this->lang->line('building')['guest_skill_current_multiplier']; ?>
            <strong><?php echo number_format($revenue_multiplier, 2, '.', ''); ?> &times;</strong>
        </p>
    </div>

    <!-- ===== How it works card ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:640px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['guest_skill_how_it_works']; ?></h4>
        <ul>
            <li><?php echo sprintf($this->lang->line('building')['guest_skill_levelup_beginner'],   $beginner_to_intermediate_rate); ?></li>
            <li><?php echo sprintf($this->lang->line('building')['guest_skill_levelup_intermediate'], $intermediate_to_advanced_rate); ?></li>
            <li><?php echo $this->lang->line('building')['guest_skill_loyalty_note']; ?></li>
        </ul>
    </div>

</div>
</div>
