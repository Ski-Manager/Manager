<div class="w-full">
<?php
echo '<h2 class="h2">' . $this->lang->line('building')['sponsorship_title'] . '</h2>';
echo '<p>'  . $this->lang->line('building')['sponsorship_page_intro'] . '</p>';

// Flash / info messages
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'sponsorship_signed',
        'sponsorship_terminated',
        'sponsorship_invalid_type',
        'sponsorship_invalid_level',
        'sponsorship_rep_too_low',
        'sponsorship_insufficient_funds',
        'sponsorship_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['sponsorship_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['sponsorship_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['sponsorship_mechanic_revenue']; ?></li>
                <li><?php echo $this->lang->line('building')['sponsorship_mechanic_bonus']; ?></li>
                <li><?php echo $this->lang->line('building')['sponsorship_mechanic_satisfaction']; ?></li>
                <li><?php echo $this->lang->line('building')['sponsorship_mechanic_cancel']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Active contracts ===== -->
<?php if (!empty($contracts)): ?>
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <h4 class="h4"><?php echo $this->lang->line('building')['sponsorship_active_title']; ?></h4>
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra" style="max-width:860px;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_sponsor']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_level']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_revenue']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_bonus']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_satisfaction']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_action']; ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contracts as $type => $contract): ?>
                    <?php
                    $cfg = $sponsor_types[$type];
                    $lvl = (int)$contract->contract_level;
                    $idx = $lvl - 1;
                    $rev = $cfg['revenue_per_level'][$idx];
                    $sat = (int)$contract->brand_satisfaction;

                    // Build bonus description
                    $bonus_desc = '—';
                    if ($type === 'lift_equipment') {
                        $pct = (int)round($cfg['maintenance_saving_pct'][$idx] * 100);
                        $bonus_desc = '-' . $pct . '% ' . $this->lang->line('building')['sponsorship_bonus_maintenance'];
                    } elseif ($type === 'apparel') {
                        $pct = (int)round($cfg['visitor_bonus_pct'][$idx] * 100);
                        $bonus_desc = '+' . $pct . '% ' . $this->lang->line('building')['sponsorship_bonus_visitors'];
                    } elseif ($type === 'event_title') {
                        $rep = $cfg['rep_bonus_per_level'][$idx];
                        $bonus_desc = '+' . $rep . ' ' . $this->lang->line('building')['sponsorship_bonus_reputation'];
                    }

                    $sat_class = $sat >= 60 ? 'bg-success' : ($sat >= 30 ? 'bg-warning' : 'bg-error');
                    ?>
                    <tr>
                        <td><?php echo $this->lang->line('building')['sponsorship_type_' . $type]; ?></td>
                        <td><?php echo $this->lang->line('building')['sponsorship_level_' . $lvl]; ?></td>
                        <td><?php echo number_format($rev, 0, '.', ' '); ?> €/<?php echo $this->lang->line('building')['sponsorship_per_day']; ?></td>
                        <td><?php echo $bonus_desc; ?></td>
                        <td>
                            <div class="flex items-center gap-2" style="min-width:90px">
                                <progress class="progress <?php echo str_replace('bg-', 'progress-', $sat_class); ?>" value="<?php echo $sat; ?>" max="100" style="flex:1"></progress>
                                <span class="text-xs opacity-70"><?php echo $sat; ?>%</span>
                            </div>
                        </td>
                        <td>
                            <form method="post" action="<?php echo base_url('sponsorship_controller/terminate'); ?>">
                                <input type="hidden" name="sponsorship_terminate_form" value="1">
                                <input type="hidden" name="sponsor_type" value="<?php echo htmlspecialchars($type); ?>">
                                <button type="submit" class="btn btn-error btn-sm"
                                        onclick="return confirm('<?php echo $this->lang->line('building')['sponsorship_terminate_confirm']; ?>')">
                                    <?php echo $this->lang->line('building')['sponsorship_terminate_btn']; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== Available sponsors ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <h4 class="h4"><?php echo $this->lang->line('building')['sponsorship_available_title']; ?></h4>
        <p class="text-base-content/60"><?php echo $this->lang->line('building')['sponsorship_available_desc']; ?></p>

        <?php foreach ($sponsor_types as $type => $cfg): ?>
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
            <h5 class="h5"><?php echo $this->lang->line('building')['sponsorship_type_' . $type]; ?></h5>
            <p class="text-base-content/60 mb-2"><?php echo $this->lang->line('building')['sponsorship_desc_' . $type]; ?></p>

            <!-- Levels table -->
            <table class="table table-sm mb-3" style="max-width:660px;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_level']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_revenue']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_bonus']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_min_rep']; ?></th>
                        <th><?php echo $this->lang->line('building')['sponsorship_col_sign_cost']; ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php for ($lvl = 1; $lvl <= 3; $lvl++): ?>
                    <?php
                    $idx        = $lvl - 1;
                    $rev        = $cfg['revenue_per_level'][$idx];
                    $min_rep    = $cfg['min_reputation'][$idx];
                    $sign_cost  = $cfg['sign_cost'][$idx];
                    $can_afford = $resort_cash >= $sign_cost;
                    $meets_rep  = $resort_reputation >= $min_rep;

                    // Bonus text
                    $bonus_txt = '—';
                    if ($type === 'lift_equipment') {
                        $bonus_txt = '-' . (int)round($cfg['maintenance_saving_pct'][$idx] * 100) . '% ' . $this->lang->line('building')['sponsorship_bonus_maintenance'];
                    } elseif ($type === 'apparel') {
                        $bonus_txt = '+' . (int)round($cfg['visitor_bonus_pct'][$idx] * 100) . '% ' . $this->lang->line('building')['sponsorship_bonus_visitors'];
                    } elseif ($type === 'event_title') {
                        $bonus_txt = '+' . $cfg['rep_bonus_per_level'][$idx] . ' ' . $this->lang->line('building')['sponsorship_bonus_reputation'];
                    } else {
                        $bonus_txt = $this->lang->line('building')['sponsorship_bonus_revenue_only'];
                    }
                    ?>
                    <tr>
                        <td><?php echo $this->lang->line('building')['sponsorship_level_' . $lvl]; ?></td>
                        <td><?php echo number_format($rev, 0, '.', ' '); ?> €/<?php echo $this->lang->line('building')['sponsorship_per_day']; ?></td>
                        <td><?php echo $bonus_txt; ?></td>
                        <td class="<?php echo $meets_rep ? '' : 'text-error'; ?>">
                            <?php echo $min_rep; ?> ★
                        </td>
                        <td><?php echo number_format($sign_cost, 0, '.', ' '); ?> €</td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>

            <!-- Sign form -->
            <?php if (isset($contracts[$type])): ?>
                <p class="text-success font-bold mb-0">
                    <?php echo $this->lang->line('building')['sponsorship_already_active']; ?>
                    (<?php echo $this->lang->line('building')['sponsorship_level_' . (int)$contracts[$type]->contract_level]; ?>)
                </p>
            <?php else: ?>
                <form method="post" action="<?php echo base_url('sponsorship_controller/sign'); ?>" class="flex items-end gap-2 flex-wrap">
                    <input type="hidden" name="sponsorship_form" value="1">
                    <input type="hidden" name="sponsor_type" value="<?php echo htmlspecialchars($type); ?>">
                    <div>
                        <label for="contract_level_<?php echo $type; ?>" class="mb-1">
                            <?php echo $this->lang->line('building')['sponsorship_select_level']; ?>
                        </label>
                        <select id="contract_level_<?php echo $type; ?>" name="contract_level" class="select select-sm" style="max-width:160px;">
                            <?php for ($lvl = 1; $lvl <= 3; $lvl++): ?>
                                <?php
                                $idx       = $lvl - 1;
                                $min_rep_l = $cfg['min_reputation'][$idx];
                                $cost_l    = $cfg['sign_cost'][$idx];
                                $disabled  = ($resort_reputation < $min_rep_l || $resort_cash < $cost_l) ? 'disabled' : '';
                                ?>
                                <option value="<?php echo $lvl; ?>" <?php echo $disabled; ?>>
                                    <?php echo $this->lang->line('building')['sponsorship_level_' . $lvl]; ?>
                                    (<?php echo number_format($cost_l, 0, '.', ' '); ?> €)
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('building')['sponsorship_sign_btn']; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

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
