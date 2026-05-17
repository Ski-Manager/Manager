<div class="w-full">
    <?php
    echo $title;
    echo $introBuildingAccess;
    ?>

    <!-- Tourist information center -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="col-span-12"><?php echo $touristInfoTitle; ?></div>
        <div class="col-span-2 center"><?php echo $touristInfoLogo; ?></div>
        <div class="col-span-10"><?php echo $touristInfoDesc; ?></div>

        <?php if ($displayTouristInfoNotBuiltBlock) : ?>
            <div class="md:col-span-12">
                <table class="overflow-x-auto tourist_info" align="center">
                    <tbody>
                        <tr>
                            <th><?php echo $this->lang->line('home')['cost']; ?></th>
                            <td><?php echo $touristInfoBuildingCost; ?> €</td>
                            <td rowspan="3">
                                <a href="<?php echo base_url('building_access_controller/build_building/'.$currentResortID.'/tourist_info/1'); ?>">
                                    <button class="btn btn-success"><?php echo $this->lang->line('building')['build']; ?></button>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['building_time']; ?></th>
                            <td><div data-countdown="<?php echo $touristInfoBuildingTime; ?>"><?php echo $touristInfoBuildingTime; ?></div></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['reputation']; ?></th>
                            <td><?php echo $touristInfoReputation; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="md:col-span-12">
                <?php if (isset($infoMessage['tourist_info'])) echo $this->lang->line('building')[$infoMessage['tourist_info']]; ?>

                <?php
                $badge_class = 'badge-neutral';
                $status_label = $this->lang->line('tourist_info')['resort_construction'];
                if (isset($friendly_status_for_badge)) {
                    if ($friendly_status_for_badge === 'open') {
                        $badge_class = 'badge-success';
                        $status_label = $this->lang->line('tourist_info')['resort_open'];
                    } elseif ($friendly_status_for_badge === 'closed') {
                        $badge_class = 'badge-error';
                        $status_label = $this->lang->line('tourist_info')['resort_closed'];
                    }
                }
                ?>
                <div class="flex items-center gap-2 mb-3 mt-2 flex-wrap">
                    <span class="badge <?php echo $badge_class; ?> text-base px-3 py-2"><?php echo $status_label; ?></span>
                    <div>
                        <?php if (isset($pre_touristInfoBuildingStatus)) echo $pre_touristInfoBuildingStatus; ?>
                        <div class="inline" data-countdown="<?php echo $touristInfoBuildingStatus; ?>"><?php echo $tourist_info_status_to_show; ?></div>
                        <?php if (isset($post_touristInfoBuildingStatus)) echo $post_touristInfoBuildingStatus; ?>
                    </div>
                </div>
                <?php if (isset($friendly_status_for_badge) && $friendly_status_for_badge === 'closed') : ?>
                <div role="alert" class="alert alert-warning mb-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?php echo $this->lang->line('tourist_info')['resort_closed_warning']; ?></span>
                </div>
                <?php endif; ?>

                <!-- Ski pass prices -->
                <?php if (isset($skipassDailyValue)) : ?>
                <div class="grid gap-3 mb-3">
                    <div class="col-span-12"><strong><?php echo $this->lang->line('tourist_info')['current_prices_title']; ?></strong></div>
                    <div class="sm:col-span-6 md:col-span-3">
                        <div class="card border-primary h-full">
                            <div class="card-body text-center py-2">
                                <div class="text-base-content/60 small"><?php echo $this->lang->line('tourist_info')['daily_label']; ?></div>
                                <div class="text-2xl font-bold text-primary"><?php echo $skipassDailyValue; ?> €</div>
                                <?php if ($prestigePercentage > 0) : ?>
                                <div class="text-success small"><?php echo $this->lang->line('tourist_info')['effective_price_label']; ?>: <?php echo $skipassDailyEffective; ?> €</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-6 md:col-span-3">
                        <div class="card border-info h-full">
                            <div class="card-body text-center py-2">
                                <div class="text-base-content/60 small"><?php echo $this->lang->line('tourist_info')['weekly_label']; ?></div>
                                <div class="text-2xl font-bold text-info"><?php echo $skipassWeeklyValue; ?> €</div>
                                <?php if ($prestigePercentage > 0) : ?>
                                <div class="text-success small"><?php echo $this->lang->line('tourist_info')['effective_price_label']; ?>: <?php echo $skipassWeeklyEffective; ?> €</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-3 mb-3">
                    <h5 class="h5"><?php echo $this->lang->line('tourist_info')['skiPassLabel']; ?> <?php echo $this->lang->line('tourist_info')['inEuros']; ?></h5>
                    <div class="grid gap-3 items-end">
                        <div class="sm:col-span-4">
                            <label for="selectDays" class="label"><?php echo $this->lang->line('tourist_info')['oneDay']; ?></label>
                            <select id="selectDays" class="select"><?php echo $selectArrayOneDay; ?></select>
                        </div>
                        <div class="sm:col-span-4">
                            <label for="selectWeek" class="label"><?php echo $this->lang->line('tourist_info')['oneWeek']; ?></label>
                            <select id="selectWeek" class="select"><?php echo $selectArrayOneWeek; ?></select>
                        </div>
                        <div class="sm:col-span-4">
                            <button type="button" id="save_skipass_price" class="btn btn-primary w-full"><?php echo $this->lang->line('tourist_info')['save_skipass_price']; ?></button>
                        </div>
                    </div>
                </div>

                <?php echo $prestige_bonus_daily_text; ?>

                <!-- Dynamic Pricing -->
                <?php if (isset($vip_pass_price)) : ?>
                <div class="mt-3">
                    <h5 class="h5"><?php echo $this->lang->line('tourist_info')['dynamic_pricing_title']; ?></h5>
                    <p class="text-base-content/60 small"><?php echo $this->lang->line('tourist_info')['dynamic_pricing_desc']; ?></p>
                    <div class="grid gap-2 items-end">
                        <div class="sm:col-span-3">
                            <label for="vip_pass_price" class="label"><?php echo $this->lang->line('tourist_info')['vip_pass_label']; ?></label>
                            <input type="number" id="vip_pass_price" name="vip_pass_price" class="input w-full" min="0" max="<?php echo (int)$max_vip_pass_price; ?>" value="<?php echo (int)$vip_pass_price; ?>">
                            <div class="text-sm opacity-60"><?php echo $this->lang->line('tourist_info')['vip_pass_help']; ?></div>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="family_discount_pct" class="label"><?php echo $this->lang->line('tourist_info')['family_discount_label']; ?></label>
                            <input type="number" id="family_discount_pct" name="family_discount_pct" class="input w-full" min="0" max="<?php echo (int)$max_family_discount_pct; ?>" value="<?php echo (int)$family_discount_pct; ?>">
                            <div class="text-sm opacity-60"><?php echo $this->lang->line('tourist_info')['family_discount_help']; ?></div>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="group_discount_pct" class="label"><?php echo $this->lang->line('tourist_info')['group_discount_label']; ?></label>
                            <input type="number" id="group_discount_pct" name="group_discount_pct" class="input w-full" min="0" max="<?php echo (int)$max_group_discount_pct; ?>" value="<?php echo (int)$group_discount_pct; ?>">
                            <div class="text-sm opacity-60"><?php echo $this->lang->line('tourist_info')['group_discount_help']; ?></div>
                        </div>
                        <div class="sm:col-span-3">
                            <button type="button" id="save_dynamic_pricing" class="btn btn-primary w-full"><?php echo $this->lang->line('tourist_info')['save_dynamic_pricing']; ?></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- jQuery + AJAX -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
$(document).ready(function() {
    // SAVE SKIPASS PRICE
    $('#save_skipass_price').on('click', function() {
        const daily = parseInt($('#selectDays').val());
        const weekly = parseInt($('#selectWeek').val());
$.post('<?php echo base_url("building_access_controller/save_skipass_prices"); ?>', { daily_price: daily, weekly_price: weekly })
        .done(function() { 
            smToast('<?php echo $this->lang->line('tourist_info')['save_success'] ?? 'Saved successfully!'; ?>', 'success');
        })
        .fail(function() { 
            smToast('<?php echo $this->lang->line('tourist_info')['save_error'] ?? 'Error saving skipass price.'; ?>', 'error');
        });
    });
// SAVE DYNAMIC PRICING
$('#save_dynamic_pricing').on('click', function() {
    const vip = parseInt($('#vip_pass_price').val());
    const family = parseInt($('#family_discount_pct').val());
    const group = parseInt($('#group_discount_pct').val());

    $.post('<?php echo base_url("building_access_controller/save_dynamic_pricing"); ?>', 
           { vip_pass_price: vip, family_discount_pct: family, group_discount_pct: group })
    .done(function(response) { 
        let msg = response;
        try {
            msg = JSON.parse(response);
        } catch(e) {
            // if parsing fails, keep original response
        }
        smToast(msg, 'success');
    })
    .fail(function() { 
        smToast('<?php echo $this->lang->line('tourist_info')['save_error'] ?? 'Error saving dynamic pricing.'; ?>', 'error');
    });
});
    // SAVE PARKING FEE (already points to parking_controller)
    $('#save_parking_fee').on('click', function() {
        const fee = parseInt($('#parking_fee_input').val());
        $.post('<?php echo base_url("parking_controller/save_fee"); ?>', { fee })
        .done(function() { 
            smToast('<?php echo $this->lang->line('tourist_info')['save_success'] ?? 'Saved successfully!'; ?>', 'success');
        })
        .fail(function() { 
            smToast('<?php echo $this->lang->line('tourist_info')['save_error'] ?? 'Error saving parking fee.'; ?>', 'error');
        });
    });
});
</script>
</div>




