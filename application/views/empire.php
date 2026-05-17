<?php
$lang_e = $this->lang->line('empire');
?>
<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <h2 class="h2 text-center mb-3"><?php echo $lang_e['title']; ?></h2>
        <p class="mb-4"><?php echo $lang_e['intro']; ?></p>

        <?php if (!empty($feedback_msg)): ?>
            <div class="mb-3"><?php echo $feedback_msg; ?></div>
        <?php endif; ?>

        <!-- ===================== Empire Overview ===================== -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['stats_title']; ?>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-12 gap-3 text-center">
                            <div class="md:col-span-4 mb-2">
                                <div class="p-3 bg-base-200 rounded">
                                    <div class="text-3xl font-bold text-primary"><?php echo $stats_total_subsidiaries; ?></div>
                                    <div class="text-base-content/60 small"><?php echo $lang_e['stats_total_properties']; ?></div>
                                </div>
                            </div>
                            <div class="md:col-span-4 mb-2">
                                <div class="p-3 bg-base-200 rounded">
                                    <div class="text-3xl font-bold text-success">€<?php echo number_format($stats_total_daily_rev); ?></div>
                                    <div class="text-base-content/60 small"><?php echo $lang_e['stats_total_daily_rev']; ?></div>
                                </div>
                            </div>
                            <div class="md:col-span-4 mb-2">
                                <div class="p-3 bg-base-200 rounded">
                                    <div class="text-3xl font-bold text-info">×<?php echo number_format($combined_marketing_bonus, 2); ?></div>
                                    <div class="text-base-content/60 small"><?php echo $lang_e['stats_marketing_bonus']; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== Shared Finances ===================== -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['finances_title']; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['finances_intro']; ?></p>
                        <div class="grid grid-cols-12 gap-3">
                            <div class="md:col-span-6">
                                <strong><?php echo $lang_e['main_resort']; ?>:</strong>
                                <?php echo htmlspecialchars($resort_name, ENT_QUOTES, 'UTF-8'); ?>
                                &nbsp;&mdash;&nbsp;
                                <span class="text-success font-bold">€<?php echo number_format((int)$resort_cash); ?></span>
                            </div>
                            <div class="md:col-span-6">
                                <strong><?php echo $lang_e['stats_total_daily_rev']; ?>:</strong>
                                <span class="text-success font-bold">€<?php echo number_format($stats_total_daily_rev); ?></span>
                                / <?php echo $this->lang->line('home')['small_day'] ?? 'day'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== Shared Marketing ===================== -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['marketing_title']; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['marketing_intro']; ?></p>
                        <p>
                            <?php echo $lang_e['stats_marketing_bonus']; ?>:
                            <strong class="text-info">×<?php echo number_format($combined_marketing_bonus, 2); ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== Owned Subsidiaries ===================== -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['owned_title']; ?>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($subsidiaries)): ?>
                            <p class="p-3 mb-0 text-base-content/60"><?php echo $lang_e['no_subsidiaries']; ?></p>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra mb-0">
                                <thead class="">
                                    <tr>
                                        <th><?php echo $lang_e['col_name']; ?></th>
                                        <th><?php echo $lang_e['col_type']; ?></th>
                                        <th><?php echo $lang_e['col_daily_revenue']; ?></th>
                                        <th><?php echo $lang_e['col_marketing_bonus']; ?></th>
                                        <th><?php echo $lang_e['col_purchased_at']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subsidiaries as $sub): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sub->subsidiary_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo $lang_e[$sub->subsidiary_type . '_name']; ?></td>
                                        <td>€<?php echo number_format((int)$sub->daily_revenue); ?></td>
                                        <td>×<?php echo number_format((float)$sub->marketing_bonus, 2); ?></td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($sub->purchased_at)), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== Acquire a Property ===================== -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['purchase_title']; ?>
                    </div>
                    <div class="card-body">

                        <!-- Catalogue cards -->
                        <div class="grid grid-cols-12 gap-3 mb-4">
                            <?php foreach ($catalogue as $type => $def): ?>
                            <div class="md:col-span-4 mb-3">
                                <div class="card h-full catalogue-card border-2"
                                     id="card-<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"
                                     data-type="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"
                                     style="cursor:pointer;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $lang_e[$type . '_name']; ?></h5>
                                        <p class="card-text text-base-content/60 small"><?php echo $lang_e[$type . '_desc']; ?></p>
                                        <ul class="list-none mb-0 small">
                                            <li>
                                                <span class="text-error font-bold">
                                                    <?php echo $lang_e['purchase_price']; ?>:
                                                    €<?php echo number_format($def['purchase_price']); ?>
                                                </span>
                                            </li>
                                            <li>
                                                <span class="text-success">
                                                    <?php echo $lang_e['daily_revenue']; ?>:
                                                    €<?php echo number_format($def['daily_revenue']); ?>/day
                                                </span>
                                            </li>
                                            <li>
                                                <span class="text-info">
                                                    <?php echo $lang_e['marketing_bonus']; ?>:
                                                    ×<?php echo number_format($def['marketing_bonus'], 2); ?>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Purchase form -->
                        <?php echo form_open('empire_controller/purchase', ['id' => 'empire_purchase_form']); ?>
                            <input type="hidden" name="subsidiary_type" id="selected_type" value="">

                            <div class="mb-3">
                                <label for="subsidiary_name" class="font-bold">
                                    <?php echo $lang_e['choose_name']; ?>
                                </label>
                                <input type="text"
                                       class="input w-full"
                                       id="subsidiary_name"
                                       name="subsidiary_name"
                                       maxlength="100"
                                       placeholder="<?php echo htmlspecialchars($lang_e['name_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                                       required>
                            </div>

                            <div id="selected_type_display" class="mb-3 text-base-content/60 small" style="display:none;">
                                <?php echo $lang_e['choose_type']; ?>: <strong id="selected_type_label"></strong>
                            </div>

                            <button type="submit"
                                    id="btn_purchase"
                                    class="btn btn-primary"
                                    disabled>
                                <?php echo $lang_e['btn_purchase']; ?>
                            </button>
                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== Franchise Mode ===================== -->
        <hr class="my-4">
        <h3 class="h3 text-center mb-3"><?php echo $lang_e['franchise_title']; ?></h3>
        <p class="mb-4 text-base-content/60"><?php echo $lang_e['franchise_intro']; ?></p>

        <!-- ── Shared Branding ───────────────────────────────────────────── -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['franchise_branding_title']; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['franchise_branding_intro']; ?></p>

                        <?php if ($branding): ?>
                        <p class="mb-3">
                            <strong><?php echo $lang_e['franchise_current_brand']; ?>:</strong>
                            <?php echo htmlspecialchars($branding->brand_name, ENT_QUOTES, 'UTF-8'); ?>
                            &nbsp;&mdash;&nbsp;
                            <?php echo htmlspecialchars($brand_tiers[(int)$branding->brand_tier]['label'], ENT_QUOTES, 'UTF-8'); ?>
                            &nbsp;(×<?php echo number_format((float)$branding->branding_bonus, 2); ?>)
                        </p>
                        <?php else: ?>
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['franchise_no_brand']; ?></p>
                        <?php endif; ?>

                        <?php echo form_open('empire_controller/set_branding'); ?>
                            <div class="grid gap-3 items-end">
                                <div class="col-span-12 md:col-span-5">
                                    <label class="label"><?php echo $lang_e['franchise_brand_name_label']; ?></label>
                                    <input type="text"
                                           class="input w-full"
                                           name="brand_name"
                                           maxlength="100"
                                           value="<?php echo $branding ? htmlspecialchars($branding->brand_name, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                           required>
                                </div>
                                <div class="col-span-12 md:col-span-5">
                                    <label class="label"><?php echo $lang_e['franchise_brand_tier_label']; ?></label>
                                    <select class="select" name="brand_tier" id="brand_tier_select">
                                        <?php foreach ($brand_tiers as $tier_num => $tier_def): ?>
                                        <option value="<?php echo $tier_num; ?>"
                                            <?php echo ($branding && (int)$branding->brand_tier === $tier_num) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($tier_def['label'], ENT_QUOTES, 'UTF-8'); ?>
                                            (×<?php echo number_format($tier_def['branding_bonus'], 2); ?>)
                                            <?php if ($tier_def['upgrade_cost'] > 0): ?>
                                            — €<?php echo number_format($tier_def['upgrade_cost']); ?>
                                            <?php endif; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit" class="btn btn-primary w-full">
                                        <?php echo $lang_e['franchise_btn_set_branding']; ?>
                                    </button>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Share Staff ───────────────────────────────────────────────── -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['franchise_staff_title']; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['franchise_staff_intro']; ?></p>

                        <?php if (empty($subsidiaries)): ?>
                            <p class="text-base-content/60"><?php echo $lang_e['franchise_staff_no_subs']; ?></p>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra mb-0">
                                <thead class="">
                                    <tr>
                                        <th><?php echo $lang_e['franchise_staff_col_resort']; ?></th>
                                        <th><?php echo $lang_e['franchise_staff_col_count']; ?> (0–10)</th>
                                        <th><?php echo $lang_e['franchise_staff_col_bonus']; ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subsidiaries as $sub):
                                        $ss = isset($shared_staff[(int)$sub->id_subsidiary]) ? $shared_staff[(int)$sub->id_subsidiary] : null;
                                        $current_count = $ss ? (int)$ss->shared_staff_count : 0;
                                        $current_bonus = $ss ? (float)$ss->staff_bonus : 1.00;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sub->subsidiary_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php echo form_open('empire_controller/share_staff', ['class' => 'flex gap-2 items-center']); ?>
                                            <input type="hidden" name="id_subsidiary" value="<?php echo (int)$sub->id_subsidiary; ?>">
                                            <input type="number"
                                                   class="input input-sm"
                                                   name="shared_staff_count"
                                                   min="0" max="10"
                                                   value="<?php echo $current_count; ?>"
                                                   style="width:80px;">
                                        </td>
                                        <td>×<?php echo number_format($current_bonus, 2); ?></td>
                                        <td>
                                            <button type="submit" class="btn btn-sm btn-outline btn-primary">
                                                <?php echo $lang_e['franchise_btn_update_staff']; ?>
                                            </button>
                                            <?php echo form_close(); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Balance Budgets ───────────────────────────────────────────── -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['franchise_budget_title']; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['franchise_budget_intro']; ?></p>

                        <?php if (empty($subsidiaries)): ?>
                            <p class="text-base-content/60"><?php echo $lang_e['franchise_staff_no_subs']; ?></p>
                        <?php else: ?>
                        <?php echo form_open('empire_controller/transfer_budget'); ?>
                        <div class="grid gap-3 items-end mb-4">
                            <div class="md:col-span-4">
                                <label class="label"><?php echo $lang_e['franchise_budget_sub_label']; ?></label>
                                <select class="select" name="id_subsidiary" required>
                                    <?php foreach ($subsidiaries as $sub): ?>
                                    <option value="<?php echo (int)$sub->id_subsidiary; ?>">
                                        <?php echo htmlspecialchars($sub->subsidiary_name, ENT_QUOTES, 'UTF-8'); ?>
                                        (€<?php echo number_format((int)$sub->daily_revenue); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="label"><?php echo $lang_e['franchise_budget_amount']; ?></label>
                                <input type="number" class="input w-full" name="amount" min="1" required>
                            </div>
                            <div class="md:col-span-3">
                                <label class="label"><?php echo $lang_e['franchise_budget_direction']; ?></label>
                                <select class="select" name="direction">
                                    <option value="to_subsidiary"><?php echo $lang_e['franchise_to_sub']; ?></option>
                                    <option value="from_subsidiary"><?php echo $lang_e['franchise_from_sub']; ?></option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="btn btn-primary w-full">
                                    <?php echo $lang_e['franchise_btn_transfer']; ?>
                                </button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

                        <!-- Transfer history -->
                        <h6 class="h6 font-bold mb-2"><?php echo $lang_e['franchise_budget_history']; ?></h6>
                        <?php if (empty($budget_history)): ?>
                            <p class="text-base-content/60 small"><?php echo $lang_e['franchise_no_transfers']; ?></p>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="table table-sm table-zebra mb-0">
                                <thead class="">
                                    <tr>
                                        <th><?php echo $lang_e['franchise_col_subsidiary']; ?></th>
                                        <th><?php echo $lang_e['franchise_col_amount']; ?></th>
                                        <th><?php echo $lang_e['franchise_col_direction']; ?></th>
                                        <th><?php echo $lang_e['franchise_col_date']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($budget_history as $xfer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($xfer->subsidiary_name ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>€<?php echo number_format((int)$xfer->amount); ?></td>
                                        <td>
                                            <?php echo $xfer->direction === 'to_subsidiary'
                                                ? $lang_e['franchise_to_sub']
                                                : $lang_e['franchise_from_sub']; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($xfer->transferred_at)), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Cross-Promotions ─────────────────────────────────────────── -->
        <div class="grid grid-cols-12 gap-3 mb-4">
            <div class="col-span-12">
                <div class="card">
                    <div class="card-header font-bold">
                        <?php echo $lang_e['franchise_promo_title']; ?>
                    </div>
                    <div class="card-body">
                        <p class="text-base-content/60 small mb-3"><?php echo $lang_e['franchise_promo_intro']; ?></p>

                        <!-- Promo type cards -->
                        <div class="grid grid-cols-12 gap-3 mb-4">
                            <?php foreach ($promo_catalogue as $ptype => $pdef): ?>
                            <div class="md:col-span-3 mb-3">
                                <div class="card h-full border-2 promo-type-card"
                                     id="pcard-<?php echo htmlspecialchars($ptype, ENT_QUOTES, 'UTF-8'); ?>"
                                     data-type="<?php echo htmlspecialchars($ptype, ENT_QUOTES, 'UTF-8'); ?>"
                                     style="cursor:pointer;">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($pdef['label'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                        <ul class="list-none mb-0 small">
                                            <li><span class="text-error font-bold"><?php echo $lang_e['franchise_promo_cost']; ?>: €<?php echo number_format($pdef['cost']); ?></span></li>
                                            <li><span class="text-success"><?php echo $lang_e['franchise_promo_bonus']; ?>: ×<?php echo number_format($pdef['guest_bonus'], 2); ?></span></li>
                                            <li><span class="text-base-content/60"><?php echo $lang_e['franchise_promo_duration']; ?>: <?php echo $pdef['duration_days']; ?> <?php echo $lang_e['franchise_promo_days']; ?></span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Launch form -->
                        <?php echo form_open('empire_controller/launch_cross_promo', ['id' => 'promo_launch_form']); ?>
                            <input type="hidden" name="promo_type" id="selected_promo_type" value="">
                            <div class="grid gap-3 items-end">
                                <div class="md:col-span-8">
                                    <label class="label"><?php echo $lang_e['franchise_promo_name_label']; ?></label>
                                    <input type="text"
                                           class="input w-full"
                                           name="promo_name"
                                           maxlength="100"
                                           placeholder="e.g. Winter Wonderland Deal"
                                           required>
                                </div>
                                <div class="md:col-span-4">
                                    <div id="promo_type_display" class="mb-2 text-base-content/60 small" style="display:none;">
                                        <?php echo $lang_e['franchise_promo_type_label']; ?>: <strong id="promo_type_label"></strong>
                                    </div>
                                    <button type="submit" id="btn_launch_promo" class="btn btn-primary w-full" disabled>
                                        <?php echo $lang_e['franchise_btn_launch_promo']; ?>
                                    </button>
                                </div>
                            </div>
                        <?php echo form_close(); ?>

                        <!-- Active promotions table -->
                        <h6 class="h6 font-bold mt-4 mb-2"><?php echo $lang_e['franchise_active_promos']; ?></h6>
                        <?php if (empty($cross_promos)): ?>
                            <p class="text-base-content/60 small"><?php echo $lang_e['franchise_no_promos']; ?></p>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="table table-sm table-zebra mb-0">
                                <thead class="">
                                    <tr>
                                        <th><?php echo $lang_e['franchise_promo_col_name']; ?></th>
                                        <th><?php echo $lang_e['franchise_promo_col_type']; ?></th>
                                        <th><?php echo $lang_e['franchise_promo_col_bonus']; ?></th>
                                        <th><?php echo $lang_e['franchise_promo_col_expires']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cross_promos as $promo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($promo->promo_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(
                                                isset($promo_catalogue[$promo->promo_type])
                                                    ? $promo_catalogue[$promo->promo_type]['label']
                                                    : $promo->promo_type,
                                                ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>×<?php echo number_format((float)$promo->guest_bonus, 2); ?></td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($promo->expires_at)), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- .container-border -->
</div><!-- .w-full -->

<script>
var catalogueNames = {
    <?php
    $types = array_keys($catalogue);
    $last  = end($types);
    foreach ($catalogue as $type => $def):
        $comma = ($type !== $last) ? ',' : '';
    ?>
    '<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>': '<?php echo htmlspecialchars($lang_e[$type . '_name'], ENT_QUOTES, 'UTF-8'); ?>'<?php echo $comma; ?>

    <?php endforeach; ?>
};

function selectSubsidiaryType(type) {
    // Update hidden input
    document.getElementById('selected_type').value = type;

    // Highlight selected card
    document.querySelectorAll('.catalogue-card').forEach(function(card) {
        card.classList.remove('border-primary', 'bg-base-200');
    });
    var chosen = document.getElementById('card-' + type);
    if (chosen) {
        chosen.classList.add('border-primary', 'bg-base-200');
    }

    // Show selected type label
    document.getElementById('selected_type_label').textContent = catalogueNames[type] || type;
    document.getElementById('selected_type_display').style.display = '';

    // Enable submit button
    document.getElementById('btn_purchase').disabled = false;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.catalogue-card').forEach(function(card) {
        card.addEventListener('click', function() {
            selectSubsidiaryType(this.getAttribute('data-type'));
        });
    });

    // Cross-promo type card selection
    document.querySelectorAll('.promo-type-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var type  = this.getAttribute('data-type');
            var label = this.querySelector('.card-title') ? this.querySelector('.card-title').textContent : type;

            document.getElementById('selected_promo_type').value = type;

            document.querySelectorAll('.promo-type-card').forEach(function(c) {
                c.classList.remove('border-primary', 'bg-base-200');
            });
            this.classList.add('border-primary', 'bg-base-200');

            document.getElementById('promo_type_label').textContent = label;
            document.getElementById('promo_type_display').style.display = '';
            document.getElementById('btn_launch_promo').disabled = false;
        });
    });
});
</script>
