<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['scenic_lift_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['scenic_lift_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'scenic_lift_settings_saved',
        'scenic_lift_invalid_settings',
        'scenic_lift_save_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['scenic_lift_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['scenic_lift_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_revenue']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_cost']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_reputation']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_capacity']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_discount']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_theme']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_photo']; ?></li>
                <li><?php echo $this->lang->line('building')['scenic_lift_mechanic_vip']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Settings card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['scenic_lift_settings_title']; ?></h4>
            <form method="post" action="<?php echo base_url('scenic_lift_controller/save'); ?>">
                <input type="hidden" name="scenic_lift_form" value="1">

                <!-- Enable toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="is_enabled" name="is_enabled" value="1"
                               <?php echo ($is_enabled == 1) ? 'checked' : ''; ?>>
                        <label for="is_enabled">
                            <?php echo $this->lang->line('building')['scenic_lift_enable_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['scenic_lift_enable_help']; ?></div>
                </div>

                <!-- Ticket price -->
                <div class="mb-3">
                    <label for="ticket_price" class="label">
                        <?php echo $this->lang->line('building')['scenic_lift_ticket_price_label']; ?>
                        (€ / <?php echo $this->lang->line('building')['scenic_lift_per_person']; ?>)
                        <small class="text-base-content/60">(<?php echo $min_ticket_price; ?>–<?php echo $max_ticket_price; ?> €)</small>
                    </label>
                    <input type="number" id="ticket_price" name="ticket_price"
                           class="input input-sm" style="max-width:120px;"
                           min="<?php echo $min_ticket_price; ?>" max="<?php echo $max_ticket_price; ?>"
                           value="<?php echo (int)$ticket_price; ?>">
                </div>

                <!-- Gondola capacity level -->
                <div class="mb-3">
                    <label for="capacity_level" class="label">
                        <?php echo $this->lang->line('building')['scenic_lift_capacity_label']; ?>
                        <small class="text-base-content/60">(<?php echo $min_capacity; ?>–<?php echo $max_capacity; ?>)</small>
                    </label>
                    <input type="number" id="capacity_level" name="capacity_level"
                           class="input input-sm" style="max-width:80px;"
                           min="<?php echo $min_capacity; ?>" max="<?php echo $max_capacity; ?>"
                           value="<?php echo (int)$capacity_level; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['scenic_lift_capacity_help']; ?></div>
                </div>

                <!-- Seasonal discount -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="seasonal_discount" name="seasonal_discount" value="1"
                               <?php echo ($seasonal_discount == 1) ? 'checked' : ''; ?>>
                        <label for="seasonal_discount">
                            <?php echo $this->lang->line('building')['scenic_lift_discount_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['scenic_lift_discount_help']; ?></div>
                </div>

                <!-- Tour theme -->
                <div class="mb-3">
                    <label for="tour_theme" class="label">
                        <?php echo $this->lang->line('building')['scenic_lift_tour_theme_label']; ?>
                    </label>
                    <select id="tour_theme" name="tour_theme" class="select select-sm" style="max-width:400px;">
                        <option value="0" <?php echo ($tour_theme == 0) ? 'selected' : ''; ?>>
                            <?php echo $this->lang->line('building')['scenic_lift_theme_standard']; ?>
                        </option>
                        <option value="1" <?php echo ($tour_theme == 1) ? 'selected' : ''; ?>>
                            <?php echo $this->lang->line('building')['scenic_lift_theme_nature']; ?>
                        </option>
                        <option value="2" <?php echo ($tour_theme == 2) ? 'selected' : ''; ?>>
                            <?php echo $this->lang->line('building')['scenic_lift_theme_sunset']; ?>
                        </option>
                        <option value="3" <?php echo ($tour_theme == 3) ? 'selected' : ''; ?>>
                            <?php echo $this->lang->line('building')['scenic_lift_theme_adventure']; ?>
                        </option>
                    </select>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['scenic_lift_tour_theme_help']; ?></div>
                </div>

                <!-- Photography package -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="photography_package" name="photography_package" value="1"
                               <?php echo ($photography_package == 1) ? 'checked' : ''; ?>>
                        <label for="photography_package">
                            <?php echo $this->lang->line('building')['scenic_lift_photo_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['scenic_lift_photo_help']; ?></div>
                </div>

                <!-- VIP gondola -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_gondola" name="vip_gondola" value="1"
                               <?php echo ($vip_gondola == 1) ? 'checked' : ''; ?>>
                        <label for="vip_gondola">
                            <?php echo $this->lang->line('building')['scenic_lift_vip_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['scenic_lift_vip_help']; ?></div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['scenic_lift_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Key figures ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['scenic_lift_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_status_label']; ?></td>
                    <td>
                        <?php if ($is_enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['scenic_lift_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['scenic_lift_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_ticket_price_label']; ?></td>
                    <td><strong><?php echo (int)$ticket_price; ?> €</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_capacity_label']; ?></td>
                    <td><strong><?php echo (int)$capacity_level; ?> / <?php echo $max_capacity; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_daily_revenue_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['scenic_lift_daily_revenue_desc']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_daily_cost_label']; ?></td>
                    <td><strong><?php echo number_format($actual_daily_cost, 0, ',', ' '); ?> €</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_rep_bonus_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['scenic_lift_rep_bonus_desc']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_discount_label']; ?></td>
                    <td>
                        <?php if ($seasonal_discount == 1): ?>
                            <span class="badge badge-info"><?php echo $this->lang->line('building')['scenic_lift_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['scenic_lift_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_tour_theme_current']; ?></td>
                    <td>
                        <?php
                        $theme_labels = [
                            0 => $this->lang->line('building')['scenic_lift_theme_standard'],
                            1 => $this->lang->line('building')['scenic_lift_theme_nature'],
                            2 => $this->lang->line('building')['scenic_lift_theme_sunset'],
                            3 => $this->lang->line('building')['scenic_lift_theme_adventure'],
                        ];
                        echo '<strong>' . htmlspecialchars($theme_labels[(int)$tour_theme] ?? $theme_labels[0]) . '</strong>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_photo_current']; ?></td>
                    <td>
                        <?php if ($photography_package == 1): ?>
                            <span class="badge badge-info"><?php echo $this->lang->line('building')['scenic_lift_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['scenic_lift_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['scenic_lift_vip_current']; ?></td>
                    <td>
                        <?php if ($vip_gondola == 1): ?>
                            <span class="badge badge-warning"><?php echo $this->lang->line('building')['scenic_lift_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['scenic_lift_off']; ?></span>
                        <?php endif; ?>
                    </td>
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
