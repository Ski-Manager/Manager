<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['env_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['env_page_intro'] . '</p>';

// Info / action messages
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'bad_action',
        'env_wildlife_zone_enabled', 'env_wildlife_zone_disabled',
        'env_solar_installed', 'env_solar_already_installed',
        'env_electric_groomer_purchased', 'env_not_enough_cash',
        'env_reforestation_planted', 'env_reforestation_max_reached',
        'env_water_recycling_installed', 'env_water_recycling_already_installed',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}

$eco_rep    = isset($env->eco_reputation)    ? (int)$env->eco_reputation    : 50;
$carbon     = isset($env->carbon_footprint)  ? (int)$env->carbon_footprint  : 0;
$noise      = isset($env->noise_pollution)   ? (int)$env->noise_pollution   : 0;
$wildlife   = isset($env->wildlife_zone)     ? (int)$env->wildlife_zone     : 0;
$solar      = isset($env->solar_panels)      ? (int)$env->solar_panels      : 0;
$eg_count   = isset($env->electric_groomers) ? (int)$env->electric_groomers : 0;
$restricted = isset($env->expansion_restricted) ? (int)$env->expansion_restricted : 0;
$tree_count      = isset($env->tree_count)      ? (int)$env->tree_count      : 0;
$water_recycling = isset($env->water_recycling) ? (int)$env->water_recycling : 0;

// Badge colour for eco reputation
if ($eco_rep >= 70) {
    $rep_class = 'success';
} elseif ($eco_rep >= 40) {
    $rep_class = 'warning';
} else {
    $rep_class = 'error';
}

// Carbon colour
$carbon_fine_threshold    = isset($carbon_fine_threshold)    ? (int)$carbon_fine_threshold    : 150;
$carbon_restrict_threshold = isset($carbon_restrict_threshold) ? (int)$carbon_restrict_threshold : 250;
$noise_fine_threshold     = isset($noise_fine_threshold)     ? (int)$noise_fine_threshold     : 80;

if ($carbon >= $carbon_restrict_threshold) {
    $carbon_class = 'error';
} elseif ($carbon >= $carbon_fine_threshold) {
    $carbon_class = 'warning';
} else {
    $carbon_class = 'success';
}

$noise_class = ($noise >= $noise_fine_threshold) ? 'error' : 'success';

$cash = isset($cash_player) ? (int)$cash_player : 0;
$reforestation_cost  = isset($reforestation_cost)  ? (int)$reforestation_cost  : 20000;
$water_recycling_cost = isset($water_recycling_cost) ? (int)$water_recycling_cost : 40000;

?>

<!-- ===== Status overview ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <div class="grid gap-3 mb-3">

        <!-- Eco Reputation -->
        <div class="md:col-span-4">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $this->lang->line('building')['env_eco_reputation_title']; ?></h5>
                    <p class="card-text">
                        <span class="badge badge-<?php echo $rep_class; ?>" style="font-size:1.2em;"><?php echo $eco_rep; ?> / 100</span>
                        <?php if ($eco_rep >= 80): ?>
                        <span class="badge badge-success ml-1" title="<?php echo $this->lang->line('building')['env_green_cert_desc']; ?>">🌿 <?php echo $this->lang->line('building')['env_green_cert_badge']; ?></span>
                        <?php endif; ?>
                    </p>
                    <progress class="progress progress-<?php echo $rep_class; ?> w-full" style="height:12px;" value="<?php echo $eco_rep; ?>" max="100"></progress>
                    <small class="text-base-content/60"><?php echo $this->lang->line('building')['env_eco_reputation_desc']; ?></small>
                    <?php if ($eco_rep >= 80): ?>
                    <div class="alert alert-success mt-2 p-1 small mb-0"><?php echo $this->lang->line('building')['env_green_cert_achieved']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Carbon Footprint -->
        <div class="md:col-span-4">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $this->lang->line('building')['env_carbon_title']; ?></h5>
                    <p class="card-text">
                        <span class="badge badge-<?php echo $carbon_class; ?>" style="font-size:1.2em;"><?php echo $carbon; ?> CO₂e</span>
                    </p>
                    <?php
                    $carbon_max_display = max($carbon_restrict_threshold * 1.2, $carbon + 1);
                    $carbon_pct = min(100, round($carbon / $carbon_max_display * 100));
                    ?>
                    <progress class="progress progress-<?php echo $carbon_class; ?> w-full mb-2" style="height:12px;" title="<?php echo $carbon; ?> CO₂e" value="<?php echo $carbon_pct; ?>" max="100"></progress>
                    <small class="text-base-content/60">
                        <?php echo $this->lang->line('building')['env_carbon_fine_at']; ?> <?php echo $carbon_fine_threshold; ?> (<?php echo $this->lang->line('building')['env_carbon_fine_desc']; ?>).<br>
                        <?php echo $this->lang->line('building')['env_carbon_restrict_at']; ?> <?php echo $carbon_restrict_threshold; ?> (<?php echo $this->lang->line('building')['env_carbon_restrict_desc']; ?>).
                    </small>
                    <?php if ($restricted): ?>
                    <div class="alert alert-error mt-2 p-1 small mb-0"><?php echo $this->lang->line('building')['env_expansion_restricted_warning']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Noise Pollution -->
        <div class="md:col-span-4">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $this->lang->line('building')['env_noise_title']; ?></h5>
                    <p class="card-text">
                        <span class="badge badge-<?php echo $noise_class; ?>" style="font-size:1.2em;"><?php echo $noise; ?> dB</span>
                    </p>
                    <?php
                    $noise_max_display = max($noise_fine_threshold * 1.5, $noise + 1);
                    $noise_pct = min(100, round($noise / $noise_max_display * 100));
                    ?>
                    <progress class="progress progress-<?php echo $noise_class; ?> w-full mb-2" style="height:12px;" title="<?php echo $noise; ?> dB" value="<?php echo $noise_pct; ?>" max="100"></progress>
                    <small class="text-base-content/60">
                        <?php echo $this->lang->line('building')['env_noise_fine_at']; ?> <?php echo $noise_fine_threshold; ?> dB
                        <?php echo $this->lang->line('building')['env_noise_fine_desc']; ?>.
                    </small>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <!-- ===== Wildlife protection zone ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:600px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['env_wildlife_title']; ?></h4>
        <p><?php echo $this->lang->line('building')['env_wildlife_desc']; ?></p>
        <?php if ($wildlife): ?>
            <span class="badge badge-success mb-2"><?php echo $this->lang->line('building')['env_wildlife_on']; ?></span>
            <a href="<?php echo base_url('environment_controller/toggle_wildlife_zone/0'); ?>">
                <button class="btn btn-outline btn-error btn-sm"><?php echo $this->lang->line('building')['env_wildlife_disable']; ?></button>
            </a>
        <?php else: ?>
            <span class="badge badge-neutral mb-2"><?php echo $this->lang->line('building')['env_wildlife_off']; ?></span>
            <a href="<?php echo base_url('environment_controller/toggle_wildlife_zone/1'); ?>">
                <button class="btn btn-success btn-sm"><?php echo $this->lang->line('building')['env_wildlife_enable']; ?></button>
            </a>
        <?php endif; ?>
    </div>

    <!-- ===== Green Investments ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:600px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['env_investments_title']; ?></h4>

        <!-- Solar Panels -->
        <div class="mb-3">
            <h6 class="h6"><?php echo $this->lang->line('building')['env_solar_title']; ?></h6>
            <p class="small"><?php echo $this->lang->line('building')['env_solar_desc']; ?></p>
            <?php if ($solar): ?>
                <span class="badge badge-success"><?php echo $this->lang->line('building')['env_solar_active']; ?></span>
            <?php else: ?>
                <p class="small text-base-content/60"><?php echo $this->lang->line('building')['env_solar_cost_label']; ?>: <strong><?php echo number_format($solar_panels_cost, 0, ',', ' '); ?> €</strong></p>
                <?php if ($cash >= $solar_panels_cost): ?>
                    <a href="<?php echo base_url('environment_controller/invest_solar'); ?>">
                        <button class="btn btn-success btn-sm"><?php echo $this->lang->line('building')['env_solar_buy']; ?></button>
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled><?php echo $this->lang->line('building')['env_solar_buy']; ?></button>
                    <small class="text-error"><?php echo $this->lang->line('building')['env_not_enough_cash']; ?></small>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Electric Groomers -->
        <div>
            <h6 class="h6"><?php echo $this->lang->line('building')['env_electric_groomer_title']; ?></h6>
            <p class="small"><?php echo $this->lang->line('building')['env_electric_groomer_desc']; ?></p>
            <p class="small"><?php echo $this->lang->line('building')['env_electric_groomer_owned']; ?>: <strong><?php echo $eg_count; ?></strong></p>
            <p class="small text-base-content/60"><?php echo $this->lang->line('building')['env_electric_groomer_cost_label']; ?>: <strong><?php echo number_format($electric_groomer_cost, 0, ',', ' '); ?> €</strong></p>
            <?php if ($cash >= $electric_groomer_cost): ?>
                <a href="<?php echo base_url('environment_controller/invest_electric_groomer'); ?>">
                    <button class="btn btn-success btn-sm"><?php echo $this->lang->line('building')['env_electric_groomer_buy']; ?></button>
                </a>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled><?php echo $this->lang->line('building')['env_electric_groomer_buy']; ?></button>
                <small class="text-error"><?php echo $this->lang->line('building')['env_not_enough_cash']; ?></small>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Reforestation Program -->
        <div class="mb-3">
            <h6 class="h6"><?php echo $this->lang->line('building')['env_reforestation_title']; ?></h6>
            <p class="small"><?php echo $this->lang->line('building')['env_reforestation_desc']; ?></p>
            <p class="small"><?php echo $this->lang->line('building')['env_reforestation_owned']; ?>: <strong><?php echo $tree_count; ?> / <?php echo ENV_MAX_TREE_COUNT; ?></strong></p>
            <?php $tree_pct = (ENV_MAX_TREE_COUNT > 0) ? min(100, round($tree_count / ENV_MAX_TREE_COUNT * 100)) : 0; ?>
            <progress class="progress progress-success w-full mb-2" style="height:10px;" title="<?php echo $tree_count; ?>/<?php echo ENV_MAX_TREE_COUNT; ?>" value="<?php echo $tree_pct; ?>" max="100"></progress>
            <?php if ($tree_count >= ENV_MAX_TREE_COUNT): ?>
                <span class="badge badge-success"><?php echo $this->lang->line('building')['env_reforestation_max']; ?></span>
            <?php else: ?>
                <p class="small text-base-content/60"><?php echo $this->lang->line('building')['env_reforestation_cost_label']; ?>: <strong><?php echo number_format($reforestation_cost, 0, ',', ' '); ?> €</strong></p>
                <?php if ($cash >= $reforestation_cost): ?>
                    <a href="<?php echo base_url('environment_controller/invest_reforestation'); ?>">
                        <button class="btn btn-success btn-sm"><?php echo $this->lang->line('building')['env_reforestation_buy']; ?></button>
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled><?php echo $this->lang->line('building')['env_reforestation_buy']; ?></button>
                    <small class="text-error"><?php echo $this->lang->line('building')['env_not_enough_cash']; ?></small>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Water Recycling System -->
        <div>
            <h6 class="h6"><?php echo $this->lang->line('building')['env_water_recycling_title']; ?></h6>
            <p class="small"><?php echo $this->lang->line('building')['env_water_recycling_desc']; ?></p>
            <?php if ($water_recycling): ?>
                <span class="badge badge-success"><?php echo $this->lang->line('building')['env_water_recycling_active']; ?></span>
            <?php else: ?>
                <p class="small text-base-content/60"><?php echo $this->lang->line('building')['env_water_recycling_cost_label']; ?>: <strong><?php echo number_format($water_recycling_cost, 0, ',', ' '); ?> €</strong></p>
                <?php if ($cash >= $water_recycling_cost): ?>
                    <a href="<?php echo base_url('environment_controller/invest_water_recycling'); ?>">
                        <button class="btn btn-success btn-sm"><?php echo $this->lang->line('building')['env_water_recycling_buy']; ?></button>
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled><?php echo $this->lang->line('building')['env_water_recycling_buy']; ?></button>
                    <small class="text-error"><?php echo $this->lang->line('building')['env_not_enough_cash']; ?></small>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== How is it calculated? ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:600px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['env_how_calculated_title']; ?></h4>
        <table class="table table-sm table-bordered">
            <thead class="">
                <tr>
                    <th><?php echo $this->lang->line('building')['env_source']; ?></th>
                    <th><?php echo $this->lang->line('building')['env_carbon_impact']; ?></th>
                    <th><?php echo $this->lang->line('building')['env_noise_impact']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr><td><?php echo $this->lang->line('building')['env_source_lift']; ?></td><td>+5 / lift</td><td>+3 / lift</td></tr>
                <tr><td><?php echo $this->lang->line('building')['env_source_cannon']; ?></td><td>+8 / cannon</td><td>+10 / cannon</td></tr>
                <tr><td><?php echo $this->lang->line('building')['env_source_groomer']; ?></td><td>+10 / groomer</td><td>+5 / groomer</td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['env_source_electric_groomer']; ?></td><td>+2 / groomer</td><td>+1 / groomer</td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['env_source_solar']; ?></td><td>-20 %</td><td>–</td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['env_source_wildlife']; ?></td><td>–</td><td><?php echo $this->lang->line('building')['env_source_wildlife_noise_note']; ?></td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['env_source_reforestation']; ?></td><td>-5 / <?php echo $this->lang->line('building')['env_source_reforestation_unit']; ?></td><td>–</td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['env_source_water_recycling']; ?></td><td>–</td><td>-30 % <?php echo $this->lang->line('building')['env_source_water_recycling_note']; ?></td></tr>
            </tbody>
        </table>
        <p class="small text-base-content/60"><?php echo $this->lang->line('building')['env_updated_nightly']; ?></p>
    </div>

</div>
</div>
</div>
