<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['crowding_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['crowding_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'crowding_settings_saved',
        'crowding_invalid_settings',
        'crowding_save_error',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}
?>

<!-- ===== How it works ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['crowding_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['crowding_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['crowding_mechanic_threshold']; ?></li>
                <li><?php echo $this->lang->line('building')['crowding_mechanic_timed_entry']; ?></li>
                <li><?php echo $this->lang->line('building')['crowding_mechanic_reputation']; ?></li>
                <li><?php echo $this->lang->line('building')['crowding_mechanic_bonus']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Settings card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['crowding_settings_title']; ?></h4>
            <form method="post" action="<?php echo base_url('crowding_controller/save'); ?>">
                <input type="hidden" name="crowding_form" value="1">

                <!-- Daily capacity limit -->
                <div class="mb-3">
                    <label for="capacity_limit" class="label">
                        <?php echo $this->lang->line('building')['crowding_capacity_label']; ?>
                        <small class="text-base-content/60">(<?php echo $min_capacity; ?>–<?php echo number_format($max_capacity); ?>)</small>
                    </label>
                    <input type="number" id="capacity_limit" name="capacity_limit"
                           class="input input-sm" style="max-width:140px;"
                           min="<?php echo $min_capacity; ?>" max="<?php echo $max_capacity; ?>"
                           value="<?php echo (int)$capacity_limit; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['crowding_capacity_help']; ?></div>
                </div>

                <!-- Crowd alert threshold -->
                <div class="mb-3">
                    <label for="crowd_alert_threshold" class="label">
                        <?php echo $this->lang->line('building')['crowding_threshold_label']; ?>
                        <small class="text-base-content/60">(<?php echo $min_threshold; ?>–<?php echo $max_threshold; ?> %)</small>
                    </label>
                    <input type="number" id="crowd_alert_threshold" name="crowd_alert_threshold"
                           class="input input-sm" style="max-width:120px;"
                           min="<?php echo $min_threshold; ?>" max="<?php echo $max_threshold; ?>"
                           value="<?php echo (int)$crowd_alert_threshold; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['crowding_threshold_help']; ?></div>
                </div>

                <!-- Timed entry toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="timed_entry_enabled" name="timed_entry_enabled" value="1"
                               <?php echo ($timed_entry_enabled == 1) ? 'checked' : ''; ?>>
                        <label for="timed_entry_enabled">
                            <?php echo $this->lang->line('building')['crowding_timed_entry_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['crowding_timed_entry_help']; ?></div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['crowding_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Key figures ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['crowding_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['crowding_capacity_label']; ?></td>
                    <td><strong><?php echo number_format((int)$capacity_limit); ?> <?php echo $this->lang->line('building')['crowding_visitors_per_day']; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['crowding_threshold_label']; ?></td>
                    <td><strong><?php echo (int)$crowd_alert_threshold; ?>%</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['crowding_timed_entry_status_label']; ?></td>
                    <td>
                        <?php if ($timed_entry_enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['crowding_timed_entry_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['crowding_timed_entry_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['crowding_rep_penalty_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['crowding_rep_penalty_desc']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['crowding_rep_bonus_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['crowding_rep_bonus_desc']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
</div>

<style>
.container-border {
    border: 1px solid #ccc;
    padding: 15px;
    margin-top: 20px;
    border-radius: 5px;
    background-color: #f9f9f9;
}
.padding_top_bot_15 {
    padding-top: 15px;
    padding-bottom: 15px;
}
</style>
