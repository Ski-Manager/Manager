<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['accommodation_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['accommodation_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'accommodation_upgraded',
        'accommodation_enabled',
        'accommodation_disabled',
        'accommodation_invalid_type',
        'accommodation_not_enough_money',
        'accommodation_no_type_selected',
        'accommodation_save_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['accommodation_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['accommodation_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['accommodation_mechanic_cost']; ?></li>
                <li><?php echo $this->lang->line('building')['accommodation_mechanic_rep']; ?></li>
                <li><?php echo $this->lang->line('building')['accommodation_mechanic_visitors']; ?></li>
                <li><?php echo $this->lang->line('building')['accommodation_mechanic_upgrade']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Current Status ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['accommodation_current_status']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['accommodation_type_label']; ?></td>
                    <td>
                        <?php if ($accommodation_type === 'none'): ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['accommodation_none']; ?></span>
                        <?php else: ?>
                            <strong><?php echo $this->lang->line('building')['accommodation_type_' . $accommodation_type]; ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['accommodation_status_label']; ?></td>
                    <td>
                        <?php if ($is_enabled == 1): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['accommodation_on']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['accommodation_off']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($accommodation_type !== 'none' && isset($accommodation_types[$accommodation_type])): ?>
                <tr>
                    <td><?php echo $this->lang->line('building')['accommodation_nightly_cost_label']; ?></td>
                    <td><strong><?php echo number_format($accommodation_types[$accommodation_type]['nightly_cost']); ?> €</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['accommodation_rep_bonus_label']; ?></td>
                    <td><strong>+<?php echo $accommodation_types[$accommodation_type]['reputation_bonus']; ?> pts</strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['accommodation_visitor_bonus_label']; ?></td>
                    <td><strong>+<?php echo ($accommodation_types[$accommodation_type]['visitor_bonus_pct'] * 100); ?>%</strong></td>
                </tr>
                <?php endif; ?>
            </table>

            <?php if ($accommodation_type !== 'none'): ?>
            <!-- Toggle enable/disable -->
            <form method="post" action="<?php echo base_url('accommodation_controller/toggle'); ?>">
                <input type="hidden" name="accommodation_toggle_form" value="1">
                <button type="submit" class="btn btn-<?php echo ($is_enabled == 1) ? 'warning' : 'success'; ?> btn-sm">
                    <?php echo $this->lang->line('building')[$is_enabled == 1 ? 'accommodation_disable_btn' : 'accommodation_enable_btn']; ?>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ===== Upgrade / Select Tier ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['accommodation_upgrade_title']; ?></h4>
            <p><?php echo $this->lang->line('building')['accommodation_upgrade_desc']; ?></p>

            <table class="table table-sm" style="max-width:660px;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('building')['accommodation_col_type']; ?></th>
                        <th><?php echo $this->lang->line('building')['accommodation_col_cost']; ?></th>
                        <th><?php echo $this->lang->line('building')['accommodation_col_maintenance']; ?></th>
                        <th><?php echo $this->lang->line('building')['accommodation_col_rep']; ?></th>
                        <th><?php echo $this->lang->line('building')['accommodation_col_visitors']; ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($accommodation_types as $type => $info): ?>
                    <tr class="<?php echo ($accommodation_type === $type) ? 'bg-success/10' : ''; ?>">
                        <td>
                            <strong><?php echo $this->lang->line('building')['accommodation_type_' . $type]; ?></strong>
                            <?php if ($accommodation_type === $type): ?>
                                <span class="badge badge-success ml-1"><?php echo $this->lang->line('building')['accommodation_current_badge']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo number_format($info['upgrade_cost']); ?> €</td>
                        <td><?php echo number_format($info['nightly_cost']); ?> €/<?php echo $this->lang->line('building')['accommodation_per_night']; ?></td>
                        <td>+<?php echo $info['reputation_bonus']; ?> pts</td>
                        <td>+<?php echo ($info['visitor_bonus_pct'] * 100); ?>%</td>
                        <td>
                            <?php if ($accommodation_type !== $type): ?>
                            <form method="post" action="<?php echo base_url('accommodation_controller/upgrade'); ?>">
                                <input type="hidden" name="accommodation_upgrade_form" value="1">
                                <input type="hidden" name="accommodation_type" value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <?php echo $this->lang->line('building')['accommodation_upgrade_btn']; ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
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
