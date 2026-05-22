<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('slope')['guest_ai_title'] . '</h2>';
echo '<p>'  . $this->lang->line('slope')['guest_ai_intro'] . '</p>';

if (empty($scores)): ?>
    <div class="alert alert-info">
        <?php echo $this->lang->line('slope')['guest_ai_no_data']; ?>
    </div>
<?php else: ?>

<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

    <!-- Score legend -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
        <h5 class="h5"><?php echo $this->lang->line('slope')['guest_ai_legend_title']; ?></h5>
        <ul class="mb-0">
            <li><?php echo $this->lang->line('slope')['guest_ai_legend_difficulty']; ?></li>
            <li><?php echo $this->lang->line('slope')['guest_ai_legend_snow']; ?></li>
            <li><?php echo $this->lang->line('slope')['guest_ai_legend_crowd']; ?></li>
            <li><?php echo $this->lang->line('slope')['guest_ai_legend_lift']; ?></li>
            <li><?php echo $this->lang->line('slope')['guest_ai_legend_price']; ?></li>
        </ul>
    </div>

    <!-- Per-slope table -->
    <div class="overflow-x-auto">
    <table class="table table-zebra" id="guest_ai_table">
        <thead class="table-dark">
            <tr>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_slope']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_difficulty']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_snow']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_crowd']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_lift']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_price']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_total']; ?></th>
                <th><?php echo $this->lang->line('slope')['guest_ai_col_visitors']; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($scores as $row):
            $total = (float)$row->total_score;
            if ($total >= 75)      { $badge_class = 'badge-success'; }
            elseif ($total >= 50)  { $badge_class = 'badge-warning'; }
            else                   { $badge_class = 'badge-error'; }

            // Difficulty label
            $diff_id = isset($row->id_difficulty) ? (int)$row->id_difficulty : 0;
            $diff_label = ($diff_id >= 1 && $diff_id <= 4)
                ? $this->lang->line('slope')['diff_' . $diff_id]
                : '—';

            // Slope display name
            $slope_display = htmlspecialchars(
                ($row->slope_custom_name !== null && $row->slope_custom_name !== '')
                    ? $row->slope_custom_name
                    : $row->slope_name,
                ENT_QUOTES, 'UTF-8'
            );
        ?>
            <tr>
                <td><?php echo $slope_display; ?></td>
                <td>
                    <?php echo $diff_label; ?>
                    <progress class="progress progress-info w-full mt-1" style="height:6px;" value="<?php echo (int)$row->score_difficulty; ?>" max="100"></progress>
                    <small><?php echo (int)$row->score_difficulty; ?>/100</small>
                </td>
                <td>
                    <progress class="progress progress-primary w-full mt-1" style="height:6px;" value="<?php echo (int)$row->score_snow_quality; ?>" max="100"></progress>
                    <small><?php echo (int)$row->score_snow_quality; ?>/100</small>
                </td>
                <td>
                    <progress class="progress progress-success w-full mt-1" style="height:6px;" value="<?php echo (int)$row->score_crowd; ?>" max="100"></progress>
                    <small><?php echo (int)$row->score_crowd; ?>/100</small>
                </td>
                <td>
                    <progress class="progress progress-warning w-full mt-1" style="height:6px;" value="<?php echo (int)$row->score_lift_speed; ?>" max="100"></progress>
                    <small><?php echo (int)$row->score_lift_speed; ?>/100</small>
                </td>
                <td>
                    <progress class="progress progress-neutral w-full mt-1" style="height:6px;" value="<?php echo (int)$row->score_ticket_price; ?>" max="100"></progress>
                    <small><?php echo (int)$row->score_ticket_price; ?>/100</small>
                </td>
                <td>
                    <span class="badge <?php echo $badge_class; ?>" style="font-size:1em;">
                        <?php echo number_format($total, 1); ?>
                    </span>
                </td>
                <td><?php echo number_format((int)$row->daily_visitors, 0, '.', ' '); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- overflow-x-auto -->

    <p class="text-base-content/60"><small><?php echo $this->lang->line('slope')['guest_ai_last_updated']; ?></small></p>

</div>
<!-- end card -->

<?php endif; ?>
</div><!-- w-full offset -->
