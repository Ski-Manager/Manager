<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['gov_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['gov_page_intro'] . '</p>';

// Info / action messages
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'gov_subsidy_claimed',
        'gov_no_subsidy_available',
        'bad_action',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        $alert_class = ($infoMessage === 'gov_subsidy_claimed') ? 'success' : 'warning';
        echo '<div class="alert alert-' . $alert_class . '">' . $this->lang->line('building')[$infoMessage] . '</div>';
    }
}

$compliance   = isset($gov->compliance_score)    ? (int)$gov->compliance_score    : 50;
$tax_rate     = isset($gov->tax_rate)            ? (float)$gov->tax_rate          : 3.0;
$tax_season   = isset($gov->tax_season)          ? (int)$gov->tax_season          : 0;
$subsidy      = isset($gov->subsidy_available)   ? (int)$gov->subsidy_available   : 0;
$audit_result = isset($gov->last_audit_result)   ? $gov->last_audit_result        : 'none';
$audit_date   = isset($gov->last_audit_date)     ? $gov->last_audit_date          : NULL;
$exp_blocked  = isset($gov->expansion_blocked)   ? (int)$gov->expansion_blocked   : 0;
$total_fines  = isset($gov->total_fines_paid)    ? (int)$gov->total_fines_paid    : 0;
$total_subs   = isset($gov->total_subsidies_received) ? (int)$gov->total_subsidies_received : 0;

$eco_rep = isset($eco_reputation) ? (int)$eco_reputation : 50;

// Compliance badge colour
if ($compliance >= 70) {
    $comp_class = 'success';
} elseif ($compliance >= $gov_compliance_restore_threshold) {
    $comp_class = 'warning';
} else {
    $comp_class = 'error';
}

?>

<!-- ===== Status Overview ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12">

    <div class="grid gap-3 mb-3">

        <!-- Compliance Score -->
        <div class="md:col-span-4">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $this->lang->line('building')['gov_compliance_title']; ?></h5>
                    <p class="card-text">
                        <span class="badge badge-<?php echo $comp_class; ?>" style="font-size:1.2em;"><?php echo $compliance; ?> / 100</span>
                    </p>
                    <progress class="progress progress-<?php echo $comp_class; ?> w-full" style="height:12px;" value="<?php echo $compliance; ?>" max="100"></progress>
                    <small class="text-base-content/60"><?php echo $this->lang->line('building')['gov_compliance_desc']; ?></small>
                    <?php if ($exp_blocked): ?>
                    <div class="alert alert-error mt-2 p-1 small mb-0"><?php echo $this->lang->line('building')['gov_expansion_blocked_warning']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Regulation Tax Rate -->
        <div class="md:col-span-4">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $this->lang->line('building')['gov_tax_rate_title']; ?></h5>
                    <p class="card-text">
                        <span class="badge badge-info" style="font-size:1.2em;"><?php echo number_format($tax_rate, 1); ?> %</span>
                    </p>
                    <small class="text-base-content/60">
                        <?php echo $this->lang->line('building')['gov_tax_rate_desc']; ?><br>
                        <?php echo $this->lang->line('building')['gov_tax_rate_range']; ?>:
                        <?php echo $gov_tax_rate_min; ?>% – <?php echo $gov_tax_rate_max; ?>%
                    </small>
                    <?php if ($tax_season > 0): ?>
                    <div class="mt-1">
                        <small class="text-base-content/60"><?php echo $this->lang->line('building')['gov_tax_rate_season']; ?>: <?php echo $tax_season; ?></small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Safety Audit Status -->
        <div class="md:col-span-4">
            <div class="card h-full">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $this->lang->line('building')['gov_audit_title']; ?></h5>
                    <?php if ($audit_result === 'pass'): ?>
                        <span class="badge badge-success mb-1"><?php echo $this->lang->line('building')['gov_audit_pass']; ?></span>
                    <?php elseif ($audit_result === 'fail'): ?>
                        <span class="badge badge-error mb-1"><?php echo $this->lang->line('building')['gov_audit_fail']; ?></span>
                    <?php else: ?>
                        <span class="badge badge-neutral mb-1"><?php echo $this->lang->line('building')['gov_audit_none']; ?></span>
                    <?php endif; ?>
                    <?php if ($audit_date): ?>
                    <p class="small text-base-content/60 mb-0"><?php echo $this->lang->line('building')['gov_audit_last_date']; ?>: <?php echo htmlspecialchars($audit_date); ?></p>
                    <?php endif; ?>
                    <small class="text-base-content/60">
                        <?php echo $this->lang->line('building')['gov_audit_desc']; ?>
                    </small>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <!-- ===== Eco Subsidy ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:600px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['gov_subsidy_title']; ?></h4>
        <p class="small"><?php echo $this->lang->line('building')['gov_subsidy_desc']; ?></p>
        <p class="small text-base-content/60">
            <?php echo $this->lang->line('building')['gov_subsidy_eco_threshold']; ?>:
            <strong><?php echo $gov_subsidy_eco_threshold; ?></strong>
            &nbsp;|&nbsp;
            <?php echo $this->lang->line('building')['gov_subsidy_your_eco']; ?>:
            <strong><?php echo $eco_rep; ?></strong>
        </p>
        <?php if ($subsidy > 0): ?>
            <div class="alert alert-success p-2 mb-2">
                <?php echo $this->lang->line('building')['gov_subsidy_available_label']; ?>:
                <strong><?php echo number_format($subsidy, 0, ',', ' '); ?> €</strong>
            </div>
            <a href="<?php echo base_url('government_controller/claim_subsidy'); ?>">
                <button class="btn btn-success btn-sm"><?php echo $this->lang->line('building')['gov_subsidy_claim_btn']; ?></button>
            </a>
        <?php else: ?>
            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['gov_subsidy_none']; ?></span>
            <p class="small text-base-content/60 mt-1"><?php echo $this->lang->line('building')['gov_subsidy_how_to_earn']; ?></p>
        <?php endif; ?>
    </div>

    <!-- ===== How it works ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:600px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['gov_how_it_works_title']; ?></h4>
        <ul class="small">
            <li><?php echo $this->lang->line('building')['gov_mechanic_compliance']; ?></li>
            <li><?php echo $this->lang->line('building')['gov_mechanic_expansion']; ?></li>
            <li><?php echo $this->lang->line('building')['gov_mechanic_audit']; ?></li>
            <li><?php echo $this->lang->line('building')['gov_mechanic_subsidy']; ?></li>
            <li><?php echo $this->lang->line('building')['gov_mechanic_tax']; ?></li>
        </ul>
        <table class="table table-sm table-bordered mt-2">
            <thead class="">
                <tr>
                    <th><?php echo $this->lang->line('building')['gov_table_event']; ?></th>
                    <th><?php echo $this->lang->line('building')['gov_table_effect']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['gov_row_eco_high']; ?></td><td>+<?php echo GOV_COMPLIANCE_HIGH_ECO_BONUS; ?> <?php echo $this->lang->line('building')['gov_unit_compliance']; ?>/<?php echo $this->lang->line('building')['gov_unit_night']; ?></td></tr>
                <tr class="bg-error/10"><td><?php echo $this->lang->line('building')['gov_row_eco_low']; ?></td><td><?php echo GOV_COMPLIANCE_LOW_ECO_PENALTY; ?> <?php echo $this->lang->line('building')['gov_unit_compliance']; ?>/<?php echo $this->lang->line('building')['gov_unit_night']; ?></td></tr>
                <tr class="bg-error/10"><td><?php echo $this->lang->line('building')['gov_row_expansion_restricted']; ?></td><td><?php echo GOV_COMPLIANCE_RESTRICT_PENALTY; ?> <?php echo $this->lang->line('building')['gov_unit_compliance']; ?>/<?php echo $this->lang->line('building')['gov_unit_night']; ?></td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['gov_row_audit_pass']; ?></td><td>+<?php echo GOV_AUDIT_PASS_REWARD; ?> € &amp; +<?php echo GOV_COMPLIANCE_AUDIT_PASS_BONUS; ?> <?php echo $this->lang->line('building')['gov_unit_compliance']; ?></td></tr>
                <tr class="bg-error/10"><td><?php echo $this->lang->line('building')['gov_row_audit_fail']; ?></td><td>-<?php echo GOV_AUDIT_FAIL_FINE; ?> € &amp; <?php echo GOV_COMPLIANCE_AUDIT_FAIL_PENALTY; ?> <?php echo $this->lang->line('building')['gov_unit_compliance']; ?></td></tr>
                <tr><td><?php echo $this->lang->line('building')['gov_row_tax_rate']; ?></td><td><?php echo $gov_tax_rate_min; ?>–<?php echo $gov_tax_rate_max; ?>% <?php echo $this->lang->line('building')['gov_row_tax_rate_note']; ?></td></tr>
                <tr class="bg-success/10"><td><?php echo $this->lang->line('building')['gov_row_subsidy']; ?></td><td><?php echo number_format($gov_subsidy_amount, 0, ',', ' '); ?> € <?php echo $this->lang->line('building')['gov_row_subsidy_note']; ?></td></tr>
            </tbody>
        </table>
        <p class="small text-base-content/60"><?php echo $this->lang->line('building')['gov_updated_nightly']; ?></p>
    </div>

    <!-- ===== Lifetime stats ===== -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:600px;">
        <h4 class="h4"><?php echo $this->lang->line('building')['gov_stats_title']; ?></h4>
        <table class="table table-sm" style="max-width:520px;">
            <tr>
                <td><?php echo $this->lang->line('building')['gov_stats_fines']; ?></td>
                <td><strong><?php echo number_format($total_fines, 0, ',', ' '); ?> €</strong></td>
            </tr>
            <tr>
                <td><?php echo $this->lang->line('building')['gov_stats_subsidies']; ?></td>
                <td><strong><?php echo number_format($total_subs, 0, ',', ' '); ?> €</strong></td>
            </tr>
        </table>
    </div>

</div>
</div>
</div>
