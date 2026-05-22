<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['real_estate_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['real_estate_intro'] . '</p>';

// Info / action message
if (!empty($infoMessage)) {
    $msg_keys = [
        'real_estate_construction_started', 'real_estate_construction_ongoing',
        'real_estate_not_enough_money', 'real_estate_bad_type', 'real_estate_bad_action',
        'real_estate_sold', 'real_estate_set_renting', 'real_estate_set_for_sale',
        'tourist_info_required',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}

if (isset($hideContent) && $hideContent) {
    // already shown message above
} else {
?>

<!-- ===== Develop new property ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <h4 class="h4"><?php echo $this->lang->line('building')['real_estate_develop_title']; ?></h4>
    <p class="text-base-content/60 small"><?php echo $this->lang->line('building')['real_estate_develop_intro']; ?></p>

    <?php if ($under_construction_count > 0): ?>
        <div class="alert alert-warning"><?php echo $this->lang->line('building')['real_estate_construction_in_progress']; ?></div>
    <?php endif; ?>

    <div class="grid gap-3">
    <?php foreach ($property_types as $type_key => $type_config): ?>
        <?php
        $tax_pct = (int)($type_config['property_tax'] * 100);
        $net_daily_rent = round($type_config['daily_rent'] * (1 - $type_config['property_tax']));
        ?>
        <div class="col-span-12 md:col-span-4">
            <div class="card h-full">
                <div class="card-header bg-primary text-white">
                    <strong><?php echo $this->lang->line('building')['real_estate_type_' . $type_key]; ?></strong>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-2">
                        <tr>
                            <td><?php echo $this->lang->line('building')['real_estate_build_cost']; ?></td>
                            <td><strong><?php echo number_format($type_config['build_cost'], 0, ',', ' '); ?> €</strong></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('building')['real_estate_build_time']; ?></td>
                            <td><strong><?php echo $type_config['build_time']; ?> <?php echo $this->lang->line('building')['real_estate_days']; ?></strong></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('building')['real_estate_sale_price']; ?></td>
                            <td><strong><?php echo number_format($type_config['sale_price'], 0, ',', ' '); ?> €</strong></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('building')['real_estate_daily_rent']; ?></td>
                            <td><strong><?php echo number_format($net_daily_rent, 0, ',', ' '); ?> €</strong>
                                <small class="text-base-content/60">(<?php echo $this->lang->line('building')['real_estate_after_tax']; ?> <?php echo $tax_pct; ?>%)</small></td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <?php if ($under_construction_count == 0): ?>
                        <a href="<?php echo base_url('real_estate_controller/develop/' . htmlspecialchars($type_key, ENT_QUOTES, 'UTF-8')); ?>"
                           class="btn btn-success btn-sm"
                           onclick="return confirm('<?php echo $this->lang->line('building')['real_estate_confirm_develop']; ?>');">
                            <?php echo $this->lang->line('building')['real_estate_develop_btn']; ?>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-sm" disabled><?php echo $this->lang->line('building')['real_estate_develop_btn']; ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

</div>
</div>

<!-- ===== My properties ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mt-3">
<div class="col-span-12">

    <h4 class="h4"><?php echo $this->lang->line('building')['real_estate_my_properties']; ?></h4>

    <?php if (empty($properties)): ?>
        <p class="text-base-content/60"><?php echo $this->lang->line('building')['real_estate_no_properties']; ?></p>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="table table-sm table-bordered align-middle">
        <thead class="">
            <tr>
                <th>#</th>
                <th><?php echo $this->lang->line('building')['real_estate_col_type']; ?></th>
                <th><?php echo $this->lang->line('building')['real_estate_col_status']; ?></th>
                <th><?php echo $this->lang->line('building')['real_estate_col_completion']; ?></th>
                <th><?php echo $this->lang->line('building')['real_estate_col_net_rent']; ?></th>
                <th><?php echo $this->lang->line('building')['real_estate_col_actions']; ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($properties as $prop): ?>
            <?php
            $prop_type    = $prop->property_type;
            $prop_status  = (int)$prop->id_status;
            $type_cfg     = $property_types[$prop_type] ?? null;
            $net_rent     = $type_cfg ? round($type_cfg['daily_rent'] * (1 - $type_cfg['property_tax'])) : 0;
            $sale_p       = $type_cfg ? $type_cfg['sale_price'] : 0;
            $status_label = $statuses[$prop_status] ?? 'unknown';
            ?>
            <tr>
                <td><?php echo (int)$prop->id_real_estate; ?></td>
                <td><strong><?php echo $this->lang->line('building')['real_estate_type_' . $prop_type]; ?></strong></td>
                <td>
                    <?php if ($prop_status === 4): ?>
                        <span class="badge badge-warning"><?php echo $this->lang->line('building')['real_estate_status_under_construction']; ?></span>
                    <?php elseif ($prop_status === 1): ?>
                        <span class="badge badge-success"><?php echo $this->lang->line('building')['real_estate_status_renting']; ?></span>
                    <?php elseif ($prop_status === 2): ?>
                        <span class="badge badge-info"><?php echo $this->lang->line('building')['real_estate_status_for_sale']; ?></span>
                    <?php elseif ($prop_status === 3): ?>
                        <span class="badge badge-neutral"><?php echo $this->lang->line('building')['real_estate_status_sold']; ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($prop_status === 4): ?>
                        <?php echo htmlspecialchars($prop->completion_date); ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($prop_status === 1): ?>
                        <strong><?php echo number_format($net_rent, 0, ',', ' '); ?> €/<?php echo $this->lang->line('building')['real_estate_day']; ?></strong>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($prop_status === 1): ?>
                        <a href="<?php echo base_url('real_estate_controller/sell/' . $prop->id_real_estate); ?>"
                           class="btn btn-sm btn-primary mr-1"
                           onclick="return confirm('<?php echo $this->lang->line('building')['real_estate_confirm_sell']; ?> (<?php echo number_format($sale_p, 0, ',', ' '); ?> €)?');">
                            <?php echo $this->lang->line('building')['real_estate_sell_btn']; ?> (<?php echo number_format($sale_p, 0, ',', ' '); ?> €)
                        </a>
                    <?php elseif ($prop_status === 2): ?>
                        <a href="<?php echo base_url('real_estate_controller/toggle_rent/' . $prop->id_real_estate . '/1'); ?>"
                           class="btn btn-sm btn-success mr-1">
                            <?php echo $this->lang->line('building')['real_estate_keep_for_rent']; ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>

</div>
</div>

<!-- ===== How it works ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mt-3">
<div class="col-span-12">
    <h4 class="h4"><?php echo $this->lang->line('building')['real_estate_how_it_works']; ?></h4>
    <p><?php echo $this->lang->line('building')['real_estate_how_it_works_desc']; ?></p>
</div>
</div>

<?php } ?>
</div>
