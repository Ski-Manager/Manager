<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['maint_depth_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['maint_depth_intro'] . '</p>';

if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = ['maint_depth_saved', 'maint_depth_invalid_plan', 'maint_depth_save_error'];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}
?>

<!-- ===== How it works ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:720px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['maint_depth_how_it_works']; ?></h4>
            <p><?php echo $this->lang->line('building')['maint_depth_how_it_works_desc']; ?></p>
            <ul>
                <li><?php echo $this->lang->line('building')['maint_depth_mechanic_type']; ?></li>
                <li><?php echo $this->lang->line('building')['maint_depth_mechanic_age']; ?></li>
                <li><?php echo $this->lang->line('building')['maint_depth_mechanic_usage']; ?></li>
                <li><?php echo $this->lang->line('building')['maint_depth_mechanic_staff']; ?></li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== Plan selection ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <h4 class="h4"><?php echo $this->lang->line('building')['maint_depth_plans_title']; ?></h4>
        <form method="post" action="<?php echo base_url('maintenance_controller/save'); ?>">
            <input type="hidden" name="maintenance_depth_form" value="1">

            <div class="grid gap-3 mb-3">

                <!-- Basic plan -->
                <div class="col-span-12 md:col-span-4">
                    <div class="card h-full <?php echo ($maintenance_plan === 'basic') ? 'border-primary' : ''; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <input class="radio mr-2" type="radio" name="maintenance_plan"
                                       id="plan_basic" value="basic"
                                       <?php echo ($maintenance_plan === 'basic') ? 'checked' : ''; ?>>
                                <label for="plan_basic"><?php echo $this->lang->line('building')['maint_depth_plan_basic']; ?></label>
                            </h5>
                            <p class="card-text small">
                                <?php echo $this->lang->line('building')['maint_depth_plan_basic_desc']; ?>
                            </p>
                            <ul class="list-none small">
                                <li>💰 <?php echo $this->lang->line('building')['maint_depth_cost']; ?>: <strong><?php echo $this->lang->line('building')['maint_depth_free']; ?></strong></li>
                                <li>🔴 <?php echo $this->lang->line('building')['maint_depth_failure_reduction']; ?>: <strong><?php echo $this->lang->line('building')['maint_depth_none']; ?></strong></li>
                                <li>🔧 <?php echo $this->lang->line('building')['maint_depth_repair_discount']; ?>: <strong><?php echo $this->lang->line('building')['maint_depth_none']; ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Standard plan -->
                <div class="col-span-12 md:col-span-4">
                    <div class="card h-full <?php echo ($maintenance_plan === 'standard') ? 'border-primary' : ''; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <input class="radio mr-2" type="radio" name="maintenance_plan"
                                       id="plan_standard" value="standard"
                                       <?php echo ($maintenance_plan === 'standard') ? 'checked' : ''; ?>>
                                <label for="plan_standard"><?php echo $this->lang->line('building')['maint_depth_plan_standard']; ?></label>
                            </h5>
                            <p class="card-text small">
                                <?php echo $this->lang->line('building')['maint_depth_plan_standard_desc']; ?>
                            </p>
                            <ul class="list-none small">
                                <li>💰 <?php echo $this->lang->line('building')['maint_depth_cost']; ?>: <strong><?php echo number_format($maint_standard_cost); ?> € / <?php echo $this->lang->line('building')['maint_depth_per_lift_day']; ?></strong></li>
                                <li>🔴 <?php echo $this->lang->line('building')['maint_depth_failure_reduction']; ?>: <strong><?php echo $this->lang->line('building')['maint_depth_none']; ?></strong></li>
                                <li>🔧 <?php echo $this->lang->line('building')['maint_depth_repair_discount']; ?>: <strong>-<?php echo $maint_standard_repair_discount; ?>%</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Preventive plan -->
                <div class="col-span-12 md:col-span-4">
                    <div class="card h-full <?php echo ($maintenance_plan === 'preventive') ? 'border-success' : ''; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <input class="radio mr-2" type="radio" name="maintenance_plan"
                                       id="plan_preventive" value="preventive"
                                       <?php echo ($maintenance_plan === 'preventive') ? 'checked' : ''; ?>>
                                <label for="plan_preventive"><?php echo $this->lang->line('building')['maint_depth_plan_preventive']; ?></label>
                            </h5>
                            <p class="card-text small">
                                <?php echo $this->lang->line('building')['maint_depth_plan_preventive_desc']; ?>
                            </p>
                            <ul class="list-none small">
                                <li>💰 <?php echo $this->lang->line('building')['maint_depth_cost']; ?>: <strong><?php echo number_format($maint_preventive_cost); ?> € / <?php echo $this->lang->line('building')['maint_depth_per_lift_day']; ?></strong></li>
                                <li>🔴 <?php echo $this->lang->line('building')['maint_depth_failure_reduction']; ?>: <strong>-<?php echo $maint_preventive_failure_reduc; ?>%</strong></li>
                                <li>🔧 <?php echo $this->lang->line('building')['maint_depth_repair_discount']; ?>: <strong>-<?php echo $maint_preventive_repair_discount; ?>%</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div><!-- end row -->

            <button type="submit" class="btn btn-primary btn-sm">
                <?php echo $this->lang->line('building')['maint_depth_save_btn']; ?>
            </button>
        </form>
    </div>
</div>

<!-- ===== Key figures ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
    <div class="col-span-12">
        <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:560px;">
            <h4 class="h4"><?php echo $this->lang->line('building')['maint_depth_key_figures']; ?></h4>
            <table class="table table-sm" style="max-width:520px;">
                <tr>
                    <td><?php echo $this->lang->line('building')['maint_depth_current_plan']; ?></td>
                    <td><strong><?php echo $this->lang->line('building')['maint_depth_plan_' . $maintenance_plan]; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['maint_depth_open_lifts']; ?></td>
                    <td><strong><?php echo (int)$open_lifts_count; ?></strong></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['maint_depth_daily_plan_cost']; ?></td>
                    <td>
                        <?php
                        $daily_cost = 0;
                        if ($maintenance_plan === 'standard') {
                            $daily_cost = $open_lifts_count * MAINT_PLAN_STANDARD_COST_PER_LIFT;
                        } elseif ($maintenance_plan === 'preventive') {
                            $daily_cost = $open_lifts_count * MAINT_PLAN_PREVENTIVE_COST_PER_LIFT;
                        }
                        echo '<strong>' . number_format($daily_cost) . ' €</strong>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['maint_depth_avg_mechanic_eff']; ?></td>
                    <td>
                        <strong><?php echo (int)$avg_mechanic_efficiency; ?>%</strong>
                        <small class="text-base-content/60">
                            (<?php echo $this->lang->line('building')['maint_depth_staff_discount']; ?>:
                            <?php
                            $staff_discount = round(($avg_mechanic_efficiency / 100) * $maint_staff_max_discount);
                            echo $staff_discount;
                            ?>%)
                        </small>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('building')['maint_depth_base_failure']; ?></td>
                    <td><?php echo $maint_base_failure; ?>% <?php echo $this->lang->line('building')['maint_depth_per_lift_day']; ?></td>
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
