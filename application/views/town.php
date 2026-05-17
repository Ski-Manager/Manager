<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['town_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['town_intro'] . '</p>';

?>

<!-- ===== Neglect warning ===== -->
<?php if ($is_neglected): ?>
<div class="alert alert-warning">
    <strong><?php echo $this->lang->line('building')['town_neglect_warning_title']; ?></strong>
    <?php echo sprintf(
        $this->lang->line('building')['town_neglect_warning_desc'],
        (int)$town_level * (int)$neglect_penalty
    ); ?>
</div>
<?php endif; ?>

<!-- ===== Town status card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:620px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['town_status_label']; ?></h4>

        <?php
        $badge_classes = ['badge-neutral', 'badge-info', 'badge-primary', 'badge-success', 'badge-warning', 'badge-error'];
        $badge_class   = $badge_classes[$town_level] ?? 'badge-neutral';
        ?>
        <div class="mb-2">
            <span class="badge <?php echo $badge_class; ?>" style="font-size:1em;">
                <?php echo $this->lang->line('building')['town_level_label']; ?>
                <?php echo (int)$town_level; ?> –
                <?php echo htmlspecialchars($town_level_name); ?>
            </span>
        </div>

        <?php if ($is_max_level): ?>
            <p class="text-success font-bold"><?php echo $this->lang->line('building')['town_max_level']; ?></p>
        <?php else: ?>
            <p class="mb-1">
                <?php echo $this->lang->line('building')['town_progress_label']; ?>
                <strong><?php echo (int)$growth_points; ?></strong> /
                <?php echo (int)$thresholds[$town_level + 1]; ?>
                <?php echo $this->lang->line('building')['town_points_label']; ?>
                (<?php echo (int)$points_needed; ?> <?php echo $this->lang->line('building')['town_points_needed']; ?>)
            </p>
            <div class="flex justify-between items-center mb-1">
                <progress class="progress progress-info flex-1" style="height:18px;" value="<?php echo (int)$progress_pct; ?>" max="100"></progress>
                <span class="ml-2 text-sm font-bold text-info"><?php echo (int)$progress_pct; ?>%</span>
            </div>
            <p class="text-base-content/60 small mb-0">
                <?php echo $this->lang->line('building')['town_next_level_label']; ?>:
                <strong><?php echo htmlspecialchars($level_names[$town_level + 1]); ?></strong>
            </p>
        <?php endif; ?>
    </div>

    <!-- ===== Key figures ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:620px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['town_key_figures']; ?></h4>
        <table class="table table-sm" style="max-width:560px;">
            <tr>
                <td><?php echo $this->lang->line('building')['town_property_value_label']; ?></td>
                <td><strong><?php echo (int)$property_value_index; ?> %</strong>
                    <small class="text-base-content/60">(<?php echo $this->lang->line('building')['town_property_value_help']; ?>)</small>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['town_infrastructure_label']; ?></td>
                <td><strong><?php echo (int)$infrastructure_level; ?> / <?php echo TOWN_LEVEL_MAX; ?></strong></td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['town_open_hotels_label']; ?></td>
                <td><strong><?php echo (int)$open_hotels; ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- ===== Level overview ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:620px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['town_levels_title']; ?></h4>
        <table class="table table-sm table-bordered" style="max-width:560px;">
            <thead class="">
                <tr>
                    <th><?php echo $this->lang->line('building')['town_col_level']; ?></th>
                    <th><?php echo $this->lang->line('building')['town_col_name']; ?></th>
                    <th><?php echo $this->lang->line('building')['town_col_points']; ?></th>
                    <th><?php echo $this->lang->line('building')['town_col_property']; ?></th>
                </tr>
            </thead>
            <tbody>
            <?php for ($lvl = 0; $lvl <= TOWN_LEVEL_MAX; $lvl++): ?>
                <tr class="<?php echo ($lvl == $town_level) ? 'bg-info/10 font-bold' : ''; ?>">
                    <td><?php echo $lvl; ?></td>
                    <td><?php echo htmlspecialchars($level_names[$lvl]); ?></td>
                    <td><?php echo (int)$thresholds[$lvl]; ?>+</td>
                    <td><?php echo 100 + $lvl * TOWN_PROPERTY_VALUE_PER_LEVEL; ?> %</td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <!-- ===== How town grows ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:620px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['town_how_it_works']; ?></h4>
        <p><?php echo $this->lang->line('building')['town_how_it_works_desc']; ?></p>
        <ul>
            <li><?php echo sprintf(
                $this->lang->line('building')['town_growth_hotel_tip'],
                (int)$growth_per_hotel
            ); ?></li>
            <li><?php echo sprintf(
                $this->lang->line('building')['town_growth_reputation_tip'],
                (float)$growth_per_reputation
            ); ?></li>
            <li><?php echo $this->lang->line('building')['town_neglect_tip']; ?></li>
        </ul>
    </div>

</div>
</div>
