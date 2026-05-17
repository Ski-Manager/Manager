<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['season_pass_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['season_pass_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage !== '') {
    $msg_keys = [
        'season_pass_settings_saved',
        'season_pass_invalid_settings',
        'season_pass_save_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['season_pass_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['season_pass_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['season_pass_mechanic_sales']; ?></li>
                <li><?php echo $this->lang->line('building')['season_pass_mechanic_revenue']; ?></li>
                <li><?php echo $this->lang->line('building')['season_pass_mechanic_loyalty']; ?></li>
                <li><?php echo $this->lang->line('building')['season_pass_mechanic_renewal']; ?></li>
                <li><?php echo $this->lang->line('building')['season_pass_mechanic_early_bird']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Settings card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['season_pass_settings_title']; ?></h4>
            <form method="post" action="<?php echo base_url('season_pass_controller/save'); ?>">
                <input type="hidden" name="season_pass_form" value="1">

                <!-- Enable toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="enabled" name="enabled" value="1"
                               <?php echo ($enabled == 1) ? 'checked' : ''; ?>>
                        <label for="enabled">
                            <?php echo $this->lang->line('building')['season_pass_enable_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['season_pass_enable_help']; ?></div>
                </div>

                <!-- Season pass price -->
                <div class="mb-3">
                    <label for="season_pass_price" class="label">
                        <?php echo $this->lang->line('building')['season_pass_price_label']; ?>
                        <small class="text-base-content/60">(<?php echo $min_price; ?>–<?php echo $max_price; ?> €)</small>
                    </label>
                    <input type="number" id="season_pass_price" name="season_pass_price"
                           class="input input-sm" style="max-width:140px;"
                           min="<?php echo $min_price; ?>" max="<?php echo $max_price; ?>"
                           value="<?php echo (int)$season_pass_price; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['season_pass_price_help']; ?></div>
                </div>

                <!-- Early-bird discount toggle -->
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="early_bird_enabled" name="early_bird_enabled" value="1"
                               <?php echo ($early_bird_enabled == 1) ? 'checked' : ''; ?>>
                        <label for="early_bird_enabled">
                            <?php echo $this->lang->line('building')['season_pass_early_bird_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo sprintf($this->lang->line('building')['season_pass_early_bird_help'], (int)(SEASON_PASS_EARLY_BIRD_SALES_BOOST * 100)); ?></div>
                </div>

                <!-- Early-bird discount percentage -->
                <div class="mb-3">
                    <label for="early_bird_discount_pct" class="label">
                        <?php echo $this->lang->line('building')['season_pass_early_bird_discount_label']; ?>
                        <small class="text-base-content/60">(<?php echo $early_bird_min_discount; ?>–<?php echo $early_bird_max_discount; ?> %)</small>
                    </label>
                    <input type="number" id="early_bird_discount_pct" name="early_bird_discount_pct"
                           class="input input-sm" style="max-width:120px;"
                           min="<?php echo $early_bird_min_discount; ?>" max="<?php echo $early_bird_max_discount; ?>"
                           value="<?php echo (int)$early_bird_discount_pct; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['season_pass_early_bird_discount_help']; ?></div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['season_pass_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Preview / key figures ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['season_pass_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_status_label']; ?></td>
                    <td>
                        <?php if ($enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['season_pass_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['season_pass_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_price_label']; ?></td>
                    <td><strong><?php echo number_format((int)$season_pass_price, 0, '.', ' '); ?> €</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_estimated_sales']; ?></td>
                    <td><strong><?php echo number_format((int)$estimated_passes, 0, '.', ' '); ?></strong>
                        <small class="text-base-content/60"><?php echo $this->lang->line('building')['season_pass_passes_unit']; ?></small>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_passes_sold_label']; ?></td>
                    <td><strong><?php echo number_format((int)$passes_sold, 0, '.', ' '); ?></strong>
                        <small class="text-base-content/60"><?php echo $this->lang->line('building')['season_pass_passes_unit']; ?></small>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_daily_revenue_label']; ?></td>
                    <td><strong><?php echo number_format((int)$estimated_daily_revenue, 0, '.', ' '); ?> €</strong>
                        <small class="text-base-content/60"><?php echo $this->lang->line('building')['season_pass_per_day']; ?></small>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_loyalty_label']; ?></td>
                    <td><?php echo $this->lang->line('building')['season_pass_loyalty_desc']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['season_pass_early_bird_label']; ?></td>
                    <td>
                        <?php if ($early_bird_enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['season_pass_on']; ?></span>
                            <small class="text-base-content/60 ml-1">(<?php echo (int)$early_bird_discount_pct; ?> % <?php echo $this->lang->line('building')['season_pass_early_bird_discount_label_short']; ?>)</small>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['season_pass_off']; ?></span>
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
