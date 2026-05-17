<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['lift_line_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['lift_line_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'lift_line_settings_saved',
        'lift_line_invalid_settings',
        'lift_line_save_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['lift_line_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['lift_line_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['lift_line_mechanic_queue']; ?></li>
                <li><?php echo $this->lang->line('building')['lift_line_mechanic_vip']; ?></li>
                <li><?php echo $this->lang->line('building')['lift_line_mechanic_breakdown']; ?></li>
                <li><?php echo $this->lang->line('building')['lift_line_mechanic_reputation']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Settings card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['lift_line_settings_title']; ?></h4>
            <form method="post" action="<?php echo base_url('lift_line_controller/save'); ?>">
                <input type="hidden" name="lift_line_form" value="1">

                <!-- Queue tolerance -->
                <div class="mb-3">
                    <label for="queue_tolerance_minutes" class="label">
                        <?php echo $this->lang->line('building')['lift_line_tolerance_label']; ?>
                        <small class="text-base-content/60">(<?php echo $min_tolerance; ?>–<?php echo $max_tolerance; ?> min)</small>
                    </label>
                    <input type="number" id="queue_tolerance_minutes" name="queue_tolerance_minutes"
                           class="input input-sm" style="max-width:120px;"
                           min="<?php echo $min_tolerance; ?>" max="<?php echo $max_tolerance; ?>"
                           value="<?php echo (int)$queue_tolerance_minutes; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['lift_line_tolerance_help']; ?></div>
                </div>

                <!-- VIP fast pass toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_fastpass_enabled" name="vip_fastpass_enabled" value="1"
                               <?php echo ($vip_fastpass_enabled == 1) ? 'checked' : ''; ?>>
                        <label for="vip_fastpass_enabled">
                            <?php echo $this->lang->line('building')['lift_line_vip_enable_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['lift_line_vip_help']; ?></div>
                </div>

                <!-- VIP price -->
                <div class="mb-3">
                    <label for="vip_fastpass_price" class="label">
                        <?php echo $this->lang->line('building')['lift_line_vip_price_label']; ?>
                        (€ / <?php echo $this->lang->line('building')['lift_line_vip_per_guest']; ?>)
                        <small class="text-base-content/60">(<?php echo $vip_min_price; ?>–<?php echo $vip_max_price; ?> €)</small>
                    </label>
                    <input type="number" id="vip_fastpass_price" name="vip_fastpass_price"
                           class="input input-sm" style="max-width:120px;"
                           min="<?php echo $vip_min_price; ?>" max="<?php echo $vip_max_price; ?>"
                           value="<?php echo (int)$vip_fastpass_price; ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['lift_line_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Key figures ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['lift_line_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['lift_line_tolerance_label']; ?></td>
                    <td><strong><?php echo (int)$queue_tolerance_minutes; ?> min</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['lift_line_vip_status_label']; ?></td>
                    <td>
                        <?php if ($vip_fastpass_enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['lift_line_vip_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['lift_line_vip_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['lift_line_vip_price_label']; ?></td>
                    <td><strong><?php echo (int)$vip_fastpass_price; ?> €</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['lift_line_rep_penalty_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['lift_line_rep_penalty_desc']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['lift_line_breakdown_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['lift_line_breakdown_desc']; ?></td>
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
