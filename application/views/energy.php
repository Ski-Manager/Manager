<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['energy_title'] . '</h2>';
echo '<p>'  . $this->lang->line('building')['energy_page_intro'] . '</p>';

// Info / action message
if (isset($infoMessage) && $infoMessage !== '') {
    $msg_keys = [
        'energy_solar_panel_bought', 'energy_solar_panel_sold',
        'energy_solar_max_reached', 'energy_solar_none_to_sell',
        'energy_hydro_built', 'energy_hydro_demolished',
        'energy_hydro_already_built', 'energy_hydro_not_built',
    ];
    if ($infoMessage === 'not_enough_money') {
        echo '<div class="alert alert-error text-center">' . $this->lang->line('home')['not_enough_money'] . '</div>';
    } elseif (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage] ?? '';
    }
}
?>

<!-- ===== Energy balance card ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:680px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['energy_balance_title']; ?></h4>
        <table class="table table-sm table-borderless" style="max-width:660px;">
            <thead class="">
                <tr>
                    <th colspan="3"><?php echo $this->lang->line('building')['energy_consumption_label']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $this->lang->line('building')['energy_lifts_label']; ?></td>
                    <td><?php echo (int)$open_lifts; ?> <?php echo $this->lang->line('building')['energy_unit_lifts']; ?></td>
                    <td><strong><?php echo (int)$lift_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['energy_cannons_label']; ?></td>
                    <td><?php echo (int)$active_cannons; ?> <?php echo $this->lang->line('building')['energy_unit_cannons']; ?></td>
                    <td><strong><?php echo (int)$cannon_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></strong></td>
                </tr>
                <tr class="bg-warning/10 font-bold">
                    <td><?php echo $this->lang->line('building')['energy_total_consumption']; ?></td>
                    <td></td>
                    <td><?php echo (int)$total_consumption_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></td>
                </tr>
            </tbody>
            <thead class="">
                <tr>
                    <th colspan="3"><?php echo $this->lang->line('building')['energy_production_label']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $this->lang->line('building')['energy_solar_label']; ?></td>
                    <td><?php echo (int)$solar_panels; ?> <?php echo $this->lang->line('building')['energy_unit_panels']; ?></td>
                    <td><strong class="text-success"><?php echo (int)$solar_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['energy_hydro_label']; ?></td>
                    <td><?php echo $hydro_plant ? $this->lang->line('building')['energy_built'] : $this->lang->line('building')['energy_not_built']; ?></td>
                    <td><strong class="text-success"><?php echo (int)$hydro_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></strong></td>
                </tr>
                <tr class="bg-success/10 font-bold">
                    <td><?php echo $this->lang->line('building')['energy_total_production']; ?></td>
                    <td></td>
                    <td><?php echo (int)$total_production_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></td>
                </tr>
            </tbody>
            <thead class="">
                <tr>
                    <th colspan="3"><?php echo $this->lang->line('building')['energy_grid_section']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $this->lang->line('building')['energy_grid_kwh_label']; ?></td>
                    <td></td>
                    <td><strong><?php echo (int)$net_kwh; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['energy_grid_rate_label']; ?></td>
                    <td></td>
                    <td><?php echo number_format($grid_cost_per_kwh, 2, '.', ''); ?> €/kWh</td>
                </tr>
                <tr class="<?php echo $daily_grid_cost > 0 ? 'bg-error/10' : 'bg-success/10'; ?> font-bold">
                    <td><?php echo $this->lang->line('building')['energy_daily_grid_cost']; ?></td>
                    <td></td>
                    <td><?php echo number_format($daily_grid_cost, 0, ',', ' '); ?> €</td>
                </tr>
                <?php if ($daily_savings > 0): ?>
                <tr class="text-success">
                    <td><?php echo $this->lang->line('building')['energy_daily_savings']; ?></td>
                    <td></td>
                    <td><?php echo number_format($daily_savings, 0, ',', ' '); ?> €</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p class="text-base-content/60 small"><?php echo $this->lang->line('building')['energy_night_skiing_note']; ?></p>
    </div>

    <!-- ===== Solar panels card ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['energy_solar_manage_title']; ?></h4>
        <p><?php echo $this->lang->line('building')['energy_solar_desc']; ?></p>
        <table class="table table-sm table-borderless" style="max-width:520px;">
            <tr>
                <td><?php echo $this->lang->line('building')['energy_solar_current']; ?></td>
                <td><strong><?php echo (int)$solar_panels; ?> / <?php echo (int)$solar_panel_max; ?></strong></td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['energy_solar_output_per_panel']; ?></td>
                <td><?php echo (int)$solar_kwh_per_panel; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['energy_solar_cost_per_panel']; ?></td>
                <td><?php echo number_format($solar_panel_cost, 0, ',', ' '); ?> €</td>
            </tr>
        </table>
        <div class="flex gap-2 flex-wrap">
            <?php if ($solar_panels < $solar_panel_max): ?>
            <a href="<?php echo base_url('energy_controller/buy_solar_panel'); ?>"
               class="btn btn-success btn-sm">
                <?php echo $this->lang->line('building')['energy_solar_buy_btn']; ?>
                (<?php echo number_format($solar_panel_cost, 0, ',', ' '); ?> €)
            </a>
            <?php endif; ?>
            <?php if ($solar_panels > 0): ?>
            <a href="<?php echo base_url('energy_controller/sell_solar_panel'); ?>"
               class="btn btn-warning btn-sm"
               onclick="return confirm('<?php echo $this->lang->line('building')['energy_solar_sell_confirm']; ?>')">
                <?php echo $this->lang->line('building')['energy_solar_sell_btn']; ?>
                (<?php echo number_format((int)round($solar_panel_cost * 0.5), 0, ',', ' '); ?> € <?php echo $this->lang->line('building')['energy_refund']; ?>)
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== Hydro plant card ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['energy_hydro_manage_title']; ?></h4>
        <p><?php echo $this->lang->line('building')['energy_hydro_desc']; ?></p>
        <table class="table table-sm table-borderless" style="max-width:520px;">
            <tr>
                <td><?php echo $this->lang->line('building')['energy_hydro_status_label']; ?></td>
                <td>
                    <?php if ($hydro_plant): ?>
                        <span class="badge badge-success"><?php echo $this->lang->line('building')['energy_built']; ?></span>
                    <?php else: ?>
                        <span class="badge badge-neutral"><?php echo $this->lang->line('building')['energy_not_built']; ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['energy_hydro_output']; ?></td>
                <td><?php echo (int)$hydro_kwh_per_day; ?> kWh/<?php echo $this->lang->line('building')['energy_day']; ?></td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['energy_hydro_cost_label']; ?></td>
                <td><?php echo number_format($hydro_plant_cost, 0, ',', ' '); ?> €</td>
            </tr>
        </table>
        <?php if (!$hydro_plant): ?>
        <a href="<?php echo base_url('energy_controller/build_hydro_plant'); ?>"
           class="btn btn-success btn-sm"
           onclick="return confirm('<?php echo $this->lang->line('building')['energy_hydro_build_confirm']; ?>')">
            <?php echo $this->lang->line('building')['energy_hydro_build_btn']; ?>
            (<?php echo number_format($hydro_plant_cost, 0, ',', ' '); ?> €)
        </a>
        <?php else: ?>
        <a href="<?php echo base_url('energy_controller/demolish_hydro_plant'); ?>"
           class="btn btn-error btn-sm"
           onclick="return confirm('<?php echo $this->lang->line('building')['energy_hydro_demolish_confirm']; ?>')">
            <?php echo $this->lang->line('building')['energy_hydro_demolish_btn']; ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- ===== Grid electricity card ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['energy_grid_title']; ?></h4>
        <p><?php echo $this->lang->line('building')['energy_grid_desc']; ?></p>
        <p class="mb-0">
            <span class="badge badge-info"><?php echo $this->lang->line('building')['energy_grid_always_on']; ?></span>
            &nbsp; <?php echo number_format($grid_cost_per_kwh, 2, '.', ''); ?> €/kWh
        </p>
    </div>

</div>
</div>
