<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['vip_loyalty_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['vip_loyalty_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'vip_loyalty_settings_saved',
        'vip_loyalty_invalid_settings',
        'vip_loyalty_save_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['vip_loyalty_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['vip_loyalty_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['vip_loyalty_mechanic_loyalty']; ?></li>
                <li><?php echo $this->lang->line('building')['vip_loyalty_mechanic_private_lift']; ?></li>
                <li><?php echo $this->lang->line('building')['vip_loyalty_mechanic_premium_slopes']; ?></li>
                <li><?php echo $this->lang->line('building')['vip_loyalty_mechanic_concierge']; ?></li>
                <li><?php echo $this->lang->line('building')['vip_loyalty_mechanic_airport_transfer']; ?></li>
                <li><?php echo $this->lang->line('building')['vip_loyalty_mechanic_apreski_lounge']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Settings card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['vip_loyalty_settings_title']; ?></h4>
            <form method="post" action="<?php echo base_url('vip_loyalty_controller/save'); ?>">
                <input type="hidden" name="vip_loyalty_form" value="1">

                <!-- Loyalty programme toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="loyalty_enabled" name="loyalty_enabled" value="1"
                               <?php echo ($loyalty_enabled == 1) ? 'checked' : ''; ?>>
                        <label for="loyalty_enabled">
                            <?php echo $this->lang->line('building')['vip_loyalty_enable_loyalty_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['vip_loyalty_enable_loyalty_help']; ?></div>
                </div>

                <!-- Loyalty discount percentage -->
                <div class="mb-3">
                    <label for="loyalty_discount_pct" class="label">
                        <?php echo $this->lang->line('building')['vip_loyalty_discount_label']; ?>
                        <small class="text-base-content/60">(<?php echo $discount_min; ?>–<?php echo $discount_max; ?> %)</small>
                    </label>
                    <input type="number" id="loyalty_discount_pct" name="loyalty_discount_pct"
                           class="input input-sm" style="max-width:120px;"
                           min="<?php echo $discount_min; ?>" max="<?php echo $discount_max; ?>"
                           value="<?php echo (int)$loyalty_discount_pct; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['vip_loyalty_discount_help']; ?></div>
                </div>

                <!-- VIP private lift toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_private_lift" name="vip_private_lift" value="1"
                               <?php echo ($vip_private_lift == 1) ? 'checked' : ''; ?>>
                        <label for="vip_private_lift">
                            <?php echo $this->lang->line('building')['vip_loyalty_private_lift_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo sprintf($this->lang->line('building')['vip_loyalty_private_lift_help'], VIP_PRIVATE_LIFT_COST, VIP_PRIVATE_LIFT_REP_BONUS); ?></div>
                </div>

                <!-- VIP premium slopes toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_premium_slopes" name="vip_premium_slopes" value="1"
                               <?php echo ($vip_premium_slopes == 1) ? 'checked' : ''; ?>>
                        <label for="vip_premium_slopes">
                            <?php echo $this->lang->line('building')['vip_loyalty_premium_slopes_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo sprintf($this->lang->line('building')['vip_loyalty_premium_slopes_help'], VIP_PREMIUM_SLOPES_COST, VIP_PREMIUM_SLOPES_REP_BONUS); ?></div>
                </div>

                <!-- VIP concierge toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_concierge" name="vip_concierge" value="1"
                               <?php echo ($vip_concierge == 1) ? 'checked' : ''; ?>>
                        <label for="vip_concierge">
                            <?php echo $this->lang->line('building')['vip_loyalty_concierge_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo sprintf($this->lang->line('building')['vip_loyalty_concierge_help'], VIP_CONCIERGE_COST, VIP_CONCIERGE_REP_BONUS); ?></div>
                </div>

                <!-- VIP airport transfer toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_airport_transfer" name="vip_airport_transfer" value="1"
                               <?php echo ($vip_airport_transfer == 1) ? 'checked' : ''; ?>>
                        <label for="vip_airport_transfer">
                            <?php echo $this->lang->line('building')['vip_loyalty_airport_transfer_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo sprintf($this->lang->line('building')['vip_loyalty_airport_transfer_help'], VIP_AIRPORT_TRANSFER_COST, VIP_AIRPORT_TRANSFER_REP_BONUS); ?></div>
                </div>

                <!-- VIP après-ski lounge toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="vip_apreski_lounge" name="vip_apreski_lounge" value="1"
                               <?php echo ($vip_apreski_lounge == 1) ? 'checked' : ''; ?>>
                        <label for="vip_apreski_lounge">
                            <?php echo $this->lang->line('building')['vip_loyalty_apreski_lounge_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo sprintf($this->lang->line('building')['vip_loyalty_apreski_lounge_help'], VIP_APRESKI_LOUNGE_COST, VIP_APRESKI_LOUNGE_REP_BONUS); ?></div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['vip_loyalty_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Key figures ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['vip_loyalty_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_loyalty_status_label']; ?></td>
                    <td>
                        <?php if ($loyalty_enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['vip_loyalty_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['vip_loyalty_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_discount_label']; ?></td>
                    <td><strong><?php echo (int)$loyalty_discount_pct; ?> %</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_private_lift_label']; ?></td>
                    <td>
                        <?php if ($vip_private_lift == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['vip_loyalty_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['vip_loyalty_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_premium_slopes_label']; ?></td>
                    <td>
                        <?php if ($vip_premium_slopes == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['vip_loyalty_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['vip_loyalty_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_concierge_label']; ?></td>
                    <td>
                        <?php if ($vip_concierge == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['vip_loyalty_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['vip_loyalty_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_airport_transfer_label']; ?></td>
                    <td>
                        <?php if ($vip_airport_transfer == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['vip_loyalty_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['vip_loyalty_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_apreski_lounge_label']; ?></td>
                    <td>
                        <?php if ($vip_apreski_lounge == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['vip_loyalty_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['vip_loyalty_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_rep_gain_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['vip_loyalty_rep_gain_desc']; ?></td>
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
