<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['retail_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['retail_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'retail_settings_saved',
        'retail_save_error',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}
?>

<!-- ===== How it works ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:750px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['retail_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['retail_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['retail_mechanic_stock']; ?></li>
                <li><?php echo $this->lang->line('building')['retail_mechanic_pricing']; ?></li>
                <li><?php echo $this->lang->line('building')['retail_mechanic_popularity']; ?></li>
                <li><?php echo $this->lang->line('building')['retail_mechanic_seasonal']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Shop settings ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <form method="post" action="<?php echo base_url('retail_controller/save'); ?>">
            <input type="hidden" name="retail_form" value="1">

            <?php foreach ($shop_types as $shop_type): ?>
            <?php $shop = $shops[$shop_type]; ?>

            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:650px;">
                <h5 class="h5">
                    <?php echo $this->lang->line('building')['retail_shop_' . $shop_type]; ?>
                    <?php if ($shop->enabled == 1): ?>
                        <span class="badge badge-success ml-2"><?php echo $this->lang->line('building')['retail_open']; ?></span>
                    <?php else: ?>
                        <span class="badge badge-neutral ml-2"><?php echo $this->lang->line('building')['retail_closed']; ?></span>
                    <?php endif; ?>
                </h5>
                <p class="text-base-content/60 mb-2"><?php echo $this->lang->line('building')['retail_shop_' . $shop_type . '_desc']; ?></p>

                <!-- Enable / disable -->
                <div class="mb-2">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="enabled_<?php echo $shop_type; ?>"
                               name="enabled_<?php echo $shop_type; ?>" value="1"
                               <?php echo ($shop->enabled == 1) ? 'checked' : ''; ?>>
                        <label for="enabled_<?php echo $shop_type; ?>">
                            <?php echo $this->lang->line('building')['retail_enable_label']; ?>
                        </label>
                    </div>
                </div>

                <!-- Stock level -->
                <div class="mb-2">
                    <label for="stock_<?php echo $shop_type; ?>" class="label">
                        <?php echo $this->lang->line('building')['retail_stock_label']; ?>
                        <small class="text-base-content/60">(<?php echo $stock_min; ?>–<?php echo $stock_max; ?>)</small>
                    </label>
                    <input type="number" id="stock_<?php echo $shop_type; ?>"
                           name="stock_<?php echo $shop_type; ?>"
                           class="input input-sm" style="max-width:100px;"
                           min="<?php echo $stock_min; ?>" max="<?php echo $stock_max; ?>"
                           value="<?php echo (int)$shop->stock_level; ?>">
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['retail_stock_help']; ?></div>
                </div>

                <!-- Pricing strategy -->
                <div class="mb-2">
                    <label for="pricing_<?php echo $shop_type; ?>" class="label">
                        <?php echo $this->lang->line('building')['retail_pricing_label']; ?>
                    </label>
                    <select id="pricing_<?php echo $shop_type; ?>"
                            name="pricing_<?php echo $shop_type; ?>"
                            class="select select-sm" style="max-width:180px;">
                        <?php foreach (['budget', 'standard', 'premium'] as $strategy): ?>
                        <option value="<?php echo $strategy; ?>"
                            <?php echo ($shop->pricing_strategy === $strategy) ? 'selected' : ''; ?>>
                            <?php echo $this->lang->line('building')['retail_pricing_' . $strategy]; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['retail_pricing_help']; ?></div>
                </div>

                <!-- Seasonal items -->
                <div class="mb-2">
                    <div class="flex items-center gap-2">
                        <input class="toggle" type="checkbox"
                               id="seasonal_<?php echo $shop_type; ?>"
                               name="seasonal_<?php echo $shop_type; ?>" value="1"
                               <?php echo ($shop->seasonal_items == 1) ? 'checked' : ''; ?>>
                        <label for="seasonal_<?php echo $shop_type; ?>">
                            <?php echo $this->lang->line('building')['retail_seasonal_label']; ?>
                        </label>
                    </div>
                    <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['retail_seasonal_help']; ?></div>
                </div>

                <!-- Popularity display -->
                <div class="mt-2">
                    <small class="text-base-content/60">
                        <?php echo $this->lang->line('building')['retail_popularity_label']; ?>:
                        <strong><?php echo (int)$shop->popularity; ?> / 100</strong>
                    </small>
                    <progress class="progress <?php echo ($shop->popularity >= 70) ? 'progress-success' : (($shop->popularity >= 40) ? 'progress-warning' : 'progress-error'); ?> mt-1" style="height:6px; max-width:260px;" value="<?php echo (int)$shop->popularity; ?>" max="100"></progress>
                </div>
            </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-sm mb-3">
                <?php echo $this->lang->line('building')['retail_save_btn']; ?>
            </button>
        </form>

        <!-- ===== Revenue reference ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:650px;">
            <h5 class="h5"><?php echo $this->lang->line('building')['retail_revenue_guide_title']; ?></h5>
            <p class="text-base-content/60"><?php echo $this->lang->line('building')['retail_revenue_guide_desc']; ?></p>
            <table class="table table-sm" style="max-width:600px;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('building')['retail_col_shop']; ?></th>
                        <th><?php echo $this->lang->line('building')['retail_col_base_rev']; ?></th>
                        <th><?php echo $this->lang->line('building')['retail_col_seasonal_bonus']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $base_revenues = RETAIL_BASE_REVENUE;
                    foreach ($shop_types as $st):
                        $base = $base_revenues[$st] ?? 0;
                        $seasonal = round($base * RETAIL_SEASONAL_BONUS);
                    ?>
                    <tr>
                        <td><?php echo $this->lang->line('building')['retail_shop_' . $st]; ?></td>
                        <td><?php echo number_format($base, 0, '.', ' '); ?> €</td>
                        <td><?php echo number_format($seasonal, 0, '.', ' '); ?> €</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="text-sm opacity-60">
                <?php echo $this->lang->line('building')['retail_revenue_guide_note']; ?>
            </p>
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
