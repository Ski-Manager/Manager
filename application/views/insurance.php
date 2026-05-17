<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['insurance_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['insurance_page_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'insurance_settings_saved',
        'insurance_save_error',
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
            <h4 class="h4"><?php echo $this->lang->line('building')['insurance_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['insurance_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['insurance_mechanic_premium']; ?></li>
                <li><?php echo $this->lang->line('building')['insurance_mechanic_lift_accident']; ?></li>
                <li><?php echo $this->lang->line('building')['insurance_mechanic_storm']; ?></li>
                <li><?php echo $this->lang->line('building')['insurance_mechanic_claims']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Plan comparison ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['insurance_plans_title']; ?></h4>
            <table class="table table-sm table-bordered" style="max-width:660px;">
                <thead class="">
                    <tr>
                        <th><?php echo $this->lang->line('building')['insurance_col_plan']; ?></th>
                        <th><?php echo $this->lang->line('building')['insurance_col_premium']; ?></th>
                        <th><?php echo $this->lang->line('building')['insurance_col_lift_payout']; ?></th>
                        <th><?php echo $this->lang->line('building')['insurance_col_storm_payout']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $this->lang->line('building')['insurance_plan_none']; ?></td>
                        <td>—</td>
                        <td>—</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['insurance_plan_basic']; ?></td>
                        <td><?php echo number_format($daily_premium_basic, 0, '.', ' '); ?> €/<?php echo $this->lang->line('building')['insurance_per_day']; ?></td>
                        <td><?php echo number_format($lift_payout_basic, 0, '.', ' '); ?> €</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['insurance_plan_premium']; ?></td>
                        <td><?php echo number_format($daily_premium_premium, 0, '.', ' '); ?> €/<?php echo $this->lang->line('building')['insurance_per_day']; ?></td>
                        <td><?php echo number_format($lift_payout_premium, 0, '.', ' '); ?> €</td>
                        <td><?php echo number_format($storm_payout, 0, '.', ' '); ?> €/<?php echo $this->lang->line('building')['insurance_per_lift']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== Plan selection form ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['insurance_select_plan_title']; ?></h4>
            <form method="post" action="<?php echo base_url('insurance_controller/save'); ?>">
                <input type="hidden" name="insurance_form" value="1">

                <div class="flex flex-col gap-1 mb-3">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input class="radio radio-sm" type="radio" name="plan" id="plan_none" value="none"
                               <?php echo ($plan === 'none') ? 'checked' : ''; ?>>
                        <span class="label-text">
                            <?php echo $this->lang->line('building')['insurance_plan_none']; ?>
                            — <?php echo $this->lang->line('building')['insurance_plan_none_desc']; ?>
                        </span>
                    </label>
                    <label class="label cursor-pointer justify-start gap-3">
                        <input class="radio radio-sm" type="radio" name="plan" id="plan_basic" value="basic"
                               <?php echo ($plan === 'basic') ? 'checked' : ''; ?>>
                        <span class="label-text">
                            <?php echo $this->lang->line('building')['insurance_plan_basic']; ?>
                            — <?php echo $this->lang->line('building')['insurance_plan_basic_desc']; ?>
                        </span>
                    </label>
                    <label class="label cursor-pointer justify-start gap-3">
                        <input class="radio radio-sm" type="radio" name="plan" id="plan_premium" value="premium"
                               <?php echo ($plan === 'premium') ? 'checked' : ''; ?>>
                        <span class="label-text">
                            <?php echo $this->lang->line('building')['insurance_plan_premium']; ?>
                            — <?php echo $this->lang->line('building')['insurance_plan_premium_desc']; ?>
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('building')['insurance_save_btn']; ?>
                </button>
            </form>
        </div>

        <!-- ===== Current status ===== -->
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['insurance_status_title']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['insurance_active_plan_label']; ?></td>
                    <td>
                        <?php if ($plan === 'premium'): ?>
                            <span class="badge badge-warning"><?php echo $this->lang->line('building')['insurance_plan_premium']; ?></span>
                        <?php elseif ($plan === 'basic'): ?>
                            <span class="badge badge-success"><?php echo $this->lang->line('building')['insurance_plan_basic']; ?></span>
                        <?php else: ?>
                            <span class="badge badge-neutral"><?php echo $this->lang->line('building')['insurance_plan_none']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['insurance_daily_cost_label']; ?></td>
                    <td>
                        <?php
                        if ($plan === 'premium') {
                            echo '<strong>' . number_format($daily_premium_premium, 0, '.', ' ') . ' €</strong>';
                        } elseif ($plan === 'basic') {
                            echo '<strong>' . number_format($daily_premium_basic, 0, '.', ' ') . ' €</strong>';
                        } else {
                            echo '—';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['insurance_total_claims_label']; ?></td>
                    <td><strong><?php echo (int)$total_claims; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['insurance_total_claimed_label']; ?></td>
                    <td><strong><?php echo number_format($total_claimed_amount, 0, '.', ' '); ?> €</strong></td>
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
