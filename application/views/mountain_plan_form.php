<div class="w-full">
<?php

$is_edit = isset($plan) && $plan !== null;
$title   = $is_edit
    ? $this->lang->line('building')['plan_edit_title']
    : $this->lang->line('building')['plan_new_title'];

echo '<h2 class="h2">' . $title . '</h2>';

// Info / action message
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = ['plan_validation_error', 'plan_not_editable', 'bad_action'];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}

?>

<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12 md:col-span-10 lg:col-span-8">

    <form method="post" action="<?php echo base_url('mountain_plan_controller/' . $form_action); ?>">

        <!-- Plan name -->
        <div class="mb-3">
            <label for="plan_name" class="label">
                <?php echo $this->lang->line('building')['plan_field_name']; ?> <span class="text-error">*</span>
            </label>
            <input type="text" name="plan_name" id="plan_name"
                   class="input w-full" maxlength="100" required
                   value="<?php echo isset($plan->plan_name) ? htmlspecialchars($plan->plan_name, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['plan_field_name_help']; ?></div>
        </div>

        <!-- Expansion strategy -->
        <div class="mb-3">
            <label for="expansion_strategy" class="label">
                <?php echo $this->lang->line('building')['plan_expansion_strategy']; ?> <span class="text-error">*</span>
            </label>
            <textarea name="expansion_strategy" id="expansion_strategy"
                      class="textarea w-full" rows="6" required><?php echo isset($plan->expansion_strategy) ? htmlspecialchars($plan->expansion_strategy, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['plan_expansion_strategy_help']; ?></div>
        </div>

        <!-- Environmental notes -->
        <div class="mb-3">
            <label for="environmental_notes" class="label">
                <?php echo $this->lang->line('building')['plan_environmental_notes']; ?> <span class="text-error">*</span>
            </label>
            <textarea name="environmental_notes" id="environmental_notes"
                      class="textarea w-full" rows="5" required><?php echo isset($plan->environmental_notes) ? htmlspecialchars($plan->environmental_notes, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['plan_environmental_notes_help']; ?></div>
        </div>

        <!-- Zoning limits -->
        <fieldset class="mb-3">
            <legend class="label"><?php echo $this->lang->line('building')['plan_zoning_limits']; ?></legend>
            <div class="grid gap-3">
                <div class="col-auto">
                    <label for="zoning_limit_slopes" class="label">
                        <?php echo $this->lang->line('building')['plan_zoning_slopes']; ?>
                    </label>
                    <input type="number" name="zoning_limit_slopes" id="zoning_limit_slopes"
                           class="input w-full" style="width:90px;"
                           min="1" max="<?php echo $max_slopes; ?>" required
                           value="<?php echo isset($plan->zoning_limit_slopes) ? (int)$plan->zoning_limit_slopes : 5; ?>">
                </div>
                <div class="col-auto">
                    <label for="zoning_limit_lifts" class="label">
                        <?php echo $this->lang->line('building')['plan_zoning_lifts']; ?>
                    </label>
                    <input type="number" name="zoning_limit_lifts" id="zoning_limit_lifts"
                           class="input w-full" style="width:90px;"
                           min="1" max="<?php echo $max_lifts; ?>" required
                           value="<?php echo isset($plan->zoning_limit_lifts) ? (int)$plan->zoning_limit_lifts : 3; ?>">
                </div>
                <div class="col-auto">
                    <label for="zoning_limit_buildings" class="label">
                        <?php echo $this->lang->line('building')['plan_zoning_buildings']; ?>
                    </label>
                    <input type="number" name="zoning_limit_buildings" id="zoning_limit_buildings"
                           class="input w-full" style="width:90px;"
                           min="1" max="<?php echo $max_buildings; ?>" required
                           value="<?php echo isset($plan->zoning_limit_buildings) ? (int)$plan->zoning_limit_buildings : 10; ?>">
                </div>
            </div>
            <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['plan_zoning_limits_help']; ?></div>
        </fieldset>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                <?php echo $this->lang->line('building')['plan_btn_save']; ?>
            </button>
            <a href="<?php echo base_url('mountain_plan_controller'); ?>" class="btn btn-ghost">
                <?php echo $this->lang->line('building')['cancel']; ?>
            </a>
        </div>
    </form>

</div>
</div>
</div>
