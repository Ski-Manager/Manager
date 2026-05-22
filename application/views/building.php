<div class="w-full">
<?php

// General page title
echo $title;
echo $introBuilding;

// start ONLY IF TOURIST INFO BUILT
if ($hideBuilding != true) { ?>        
    <!-- START Building BLock -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
            <div class="col-span-2 center"> <?php echo $buildingLogo;?> </div>
            <div class="col-span-10"> <?php echo $buildingDesc;?>  </div>
            <div class="md:col-span-12"> <?php              
        if (isset($infoMessage) && $infoMessage == 'not_enough_money')
            echo '<div class="alert alert-error text-center">'.$this->lang->line('home')['not_enough_money'].'</div>';
        else if (isset($infoMessage) && $infoMessage != '')
            echo $this->lang->line('building')[$infoMessage];
        
        ?>
            </div>
            <?php if (isset($medicalTotalCount)) { ?>
            <div class="md:col-span-12 mb-2">
                <div class="card border-error medical-summary-card">
                    <div class="card-body py-2 flex items-center gap-3 flex-wrap">
                        <span class="badge badge-error"><?php echo $this->lang->line('building')['quantity']; ?>: <?php echo $medicalTotalCount; ?></span>
                        <span><strong><?php echo $this->lang->line('building')['total_capacity']; ?>:</strong> <span class="text-error font-bold ml-1"><?php echo $medicalTotalCapacity; ?></span></span>
                        <span><strong><?php echo $this->lang->line('building')['max_tourists']; ?>:</strong> <span class="font-bold ml-1"><?php echo $medicalMaxTourists; ?></span></span>
                        <span><strong><?php echo $this->lang->line('building')['max_income']; ?>:</strong> <span class="text-success font-bold ml-1"><?php echo $medicalTotalIncome; ?> €</span></span>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="md:col-span-12">
                <table class="table overflow-x-auto building_7th" align="center">
                    <thead>
                        <tr>
                            <th class="md:col-span-3"></th>
                            <th class="md:col-span-3"><?php echo $this->lang->line('home')['level'];?> 1</th>
                            <th class="md:col-span-3"><?php echo $this->lang->line('home')['level'];?> 2</th>
                            <th class="md:col-span-3"><?php echo $this->lang->line('home')['level'];?> 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><?php echo $this->lang->line('access')['infrastructure'];?></th>
                            <td><?php echo $buildingName[1];?></td>
                            <td><?php echo $buildingName[2];?></td>
                            <td><?php echo $buildingName[3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['cost'];?></th>
                            <td><?php echo $buildingCost[1];?> €</td>
                            <td><?php echo $buildingCost[2];?> €</td>
                            <td><?php echo $buildingCost[3];?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['building_time'];?></th>
                            <td>
                                <?php if (isset ($wait_status[1])) echo $pre_building_time[1];?>    <!-- PRE value is used for eventual tooltip is WAIT status -->
                                <div data-countdown="<?php echo $buildingTime[1];?>"><?php echo $buildingTime[1];?></div> <!-- Countdown div -->
                                <?php if (isset ($wait_status[1])) echo $post_building_time[1];?>   <!-- POST value is used for eventual tooltip is WAIT status, to close the div -->
                            </td>
                            <td>
                                <?php if (isset ($wait_status[2])) echo $pre_building_time[2];?>
                                <div data-countdown="<?php echo $buildingTime[2];?>"><?php echo $buildingTime[2];?></div>
                                <?php if (isset ($wait_status[2])) echo $post_building_time[2];?>
                            </td>
                            <td>
                                <?php if (isset ($wait_status[3])) echo $pre_building_time[3];?>
                                <div data-countdown="<?php echo $buildingTime[3];?>"><?php echo $buildingTime[3];?></div>
                                <?php if (isset ($wait_status[3])) echo $post_building_time[3];?>
                            </td>
                            
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['reputation'];?></th>
                            <td><?php echo $buildingReputation[1];?></td>
                            <td><?php echo $buildingReputation[2];?></td>
                            <td><?php echo $buildingReputation[3];?></td>
                        </tr>
                        <?php if (isset($buildingCapacity)) { ?>
                        <?php
                            $capacity_label = (isset($building_type) && $building_type == 'cannon')
                                ? $this->lang->line('building')['snow_output_per_cannon']
                                : $this->lang->line('home')['capacity'];
                            $capacity_unit = (isset($building_type) && $building_type == 'cannon')
                                ? ' '.$this->lang->line('building')['cm']
                                : '';
                        ?>
                        <tr>
                            <th><?php echo $capacity_label; ?></th>
                            <td><?php echo $buildingCapacity[1].$capacity_unit;?></td>
                            <td><?php echo $buildingCapacity[2].$capacity_unit;?></td>
                            <td><?php echo $buildingCapacity[3].$capacity_unit;?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th><?php if (isset($building_type) && $building_type == 'cannon')
                                         echo $this->lang->line('building')['daily_cost_per_cannon'];
                                        else
                                            echo $this->lang->line('building')['max_income_per_building'];
                                ?></th>
                            <td><?php echo $buildingMaxIncome[1];?></td>
                            <td><?php echo $buildingMaxIncome[2];?></td>
                            <td><?php echo $buildingMaxIncome[3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('building')['quantity'];?></th>
                            <td><?php echo $buildingQuantity[1];?></td>
                            <td><?php echo $buildingQuantity[2];?></td>
                            <td><?php echo $buildingQuantity[3];?></td>
                        </tr>
                        <tr>
                            <th></th>
                            <?php
                            echo $buildingButton[1];    // For BUILD button
                            echo $buildingButton[2];    // For UPGRADE button
                            echo $buildingButton[3];    // For UPGRADE button
                            ?>
                        </tr>
                    </tbody>
                </table> 
                
                
                <?php if (isset($building_type) && $building_type == 'cannon'){
                    echo '<table class="cannon_table overflow-x-auto"><tr><td>';
                    echo $active_cannons;
                    echo '</td><td>';
                    echo $start_cannon_button;
                    echo '</tr><tr><td>';
                    echo $inactive_cannons;
                    echo '</td><td>';
                    echo $stop_cannon_button;
                    echo '</td></tr></table>';

                    // Current snow level with progress bar
                    if (isset($current_snow_level)) {
                        // Low snow warning
                        if ($current_snow_level < LOW_SNOW_THRESHOLD) {
                            echo '<div class="alert alert-error">'.$this->lang->line('building')['low_snow_warning'].' ('.$current_snow_level.' '.$this->lang->line('building')['cm'].')</div>';
                        }
                        echo '<p><strong>'.$this->lang->line('building')['current_snow_level'].':</strong> '.$current_snow_level.' '.$this->lang->line('building')['cm'].'</p>';
                        if (isset($snow_quality_key)) {
                            $sq_label = $this->lang->line('weather')['snow_quality_'.$snow_quality_key];
                            $sq_badge = htmlspecialchars($snow_quality_badge, ENT_QUOTES, 'UTF-8');
                            echo '<p><strong>'.$this->lang->line('weather')['snow_quality_label'].':</strong> <span class="badge badge-'.$sq_badge.'">'.$sq_label.'</span></p>';
                        }
                        if (isset($snow_level_bar_percent)) {
                            $dm_class = 'progress-'.($snow_level_bar_class ?? 'success');
                            echo '<div class="flex items-center gap-2 mb-1" style="max-width:420px;" title="'.$current_snow_level.' cm">';
                            echo '<progress class="progress '.$dm_class.' flex-1" value="'.$snow_level_bar_percent.'" max="100"></progress>';
                            echo '<span class="text-xs opacity-70">'.$snow_level_bar_percent.'%</span>';
                            echo '</div>';
                        }
                    }

                    // Snow cannon production summary
                    if (isset($total_snow_output) || isset($total_daily_cost_cannons)) {
                        echo '<div class="card bg-base-100 shadow-sm" style="max-width:400px;margin-top:10px;"><div class="card-body">';
                        echo '<strong>'.$this->lang->line('building')['cannon_summary_title'].'</strong><br>';
                        echo $this->lang->line('building')['cannon_total_snow_output'].': <strong>'.(isset($total_snow_output) ? $total_snow_output : 0).' '.$this->lang->line('building')['cm'].'</strong><br>';
                        echo $this->lang->line('building')['cannon_total_daily_cost'].': <strong>'.(isset($total_daily_cost_cannons) ? $total_daily_cost_cannons : 0).' €</strong>';
                        echo '</div>';
                    }

                    // ── Realistic Snowmaking Requirements ──────────────────────────────
                    echo '<hr>';
                    echo '<h4 class="h4">'.$this->lang->line('building')['snowmaking_requirements_title'].'</h4>';

                    // Temperature
                    if (!empty($above_freezing)) {
                        echo $this->lang->line('building')['snowmaking_temp_blocked'];
                    }

                    // Water reservoir
                    $reservoir_purchased_v = !empty($water_reservoir_purchased);
                    if (!$reservoir_purchased_v) {
                        echo $this->lang->line('building')['water_reservoir_not_purchased'];
                        $cost_fmt_v = number_format(isset($water_reservoir_cost) ? $water_reservoir_cost : WATER_RESERVOIR_COST, 0, ',', ' ');
                        echo '<div class="card bg-base-100 shadow-sm "><div class="card-body mb-2" style="max-width:420px;">';
                        echo '<strong>'.$this->lang->line('building')['water_reservoir_buy_title'].'</strong><br>';
                        echo '<p class="mb-1">'.$this->lang->line('building')['water_reservoir_buy_desc'].'</p>';
                        echo '<p><strong>'.$this->lang->line('building')['water_reservoir_buy_cost'].':</strong> '.$cost_fmt_v.' €</p>';
                        $resort_id_v = isset($currentResortID) ? $currentResortID : '';
                        echo '<a href="'.base_url().'trail_snowmaking_controller/buy_water_reservoir/'.$resort_id_v.'"
                                onclick="return confirm(\''.$this->lang->line('building')['water_reservoir_buy_confirm'].'\')">
                                <button class="btn btn-primary btn-sm">'.$this->lang->line('building')['water_reservoir_buy_btn'].'</button>
                              </a>';
                        echo '</div>';
                    } else {
                        $water_level = isset($water_reservoir_level) ? (int)$water_reservoir_level : 100;
                        $water_bar_class_v = isset($water_bar_class) ? $water_bar_class : 'success';
                        echo '<p><strong>'.$this->lang->line('building')['snowmaking_water_label'].':</strong> '.$water_level.'%</p>';
                        echo '<div class="flex items-center gap-2 mb-2" style="max-width:320px;" title="'.$water_level.'%">';
                        echo '<progress class="progress progress-'.$water_bar_class_v.' flex-1" value="'.$water_level.'" max="100"></progress>';
                        echo '<span class="text-xs opacity-70">'.$water_level.'%</span>';
                        echo '</div>';
                        if ($water_level <= 0) {
                            echo $this->lang->line('building')['snowmaking_water_empty'];
                        } elseif ($water_level < 20) {
                            echo $this->lang->line('building')['snowmaking_water_low'];
                        }
                        echo '<small class="text-base-content/60">'.$this->lang->line('building')['snowmaking_water_refill_info'].'</small>';
                    }

                    // Snowmaking staff
                    $sm_staff = isset($snowmaker_count) ? (int)$snowmaker_count : 0;
                    $sm_req   = isset($snowmaker_required) ? (int)$snowmaker_required : SNOWMAKING_MIN_STAFF;
                    echo '<p class="mt-2"><strong>'.$this->lang->line('building')['snowmaking_staff_label'].':</strong> '.$sm_staff.' / '.$sm_req.'</p>';
                    if ($sm_staff < $sm_req) {
                        echo $this->lang->line('building')['snowmaking_staff_missing'];
                    }

                    // Electricity
                    $elec_per_cannon = isset($snowmaking_elec_per_cannon) ? (int)$snowmaking_elec_per_cannon : SNOWMAKING_ELECTRICITY_PER_CANNON;
                    echo '<p class="mt-2"><strong>'.$this->lang->line('building')['snowmaking_electricity_label'].':</strong> '.$elec_per_cannon.' €/cannon</p>';
                    // ── End Realistic Snowmaking Requirements ───────────────────────────

                    // Snow target level section
                    echo '<hr>';
                    echo '<h4 class="h4">'.$this->lang->line('building')['snow_target_title'].'</h4>';
                    $target = isset($cannon_target_snow) ? (int)$cannon_target_snow : 0;
                    if ($target > 0) {
                        echo '<p>'.$this->lang->line('building')['snow_target_current'].': <strong>'.$target.' '.$this->lang->line('building')['cm'].'</strong></p>';
                    } else {
                        echo '<p class="text-base-content/60">'.$this->lang->line('building')['snow_target_none'].'</p>';
                    }
                    if (isset($snow_target_form)) echo $snow_target_form;

                    // Auto-start threshold section
                    echo '<hr>';
                    echo '<h4 class="h4">'.$this->lang->line('building')['snow_auto_start_title'].'</h4>';
                    $auto_start = isset($cannon_auto_start) ? (int)$cannon_auto_start : 0;
                    if ($auto_start > 0) {
                        echo '<p>'.$this->lang->line('building')['snow_auto_start_current'].': <strong>'.$auto_start.' '.$this->lang->line('building')['cm'].'</strong></p>';
                    } else {
                        echo '<p class="text-base-content/60">'.$this->lang->line('building')['snow_auto_start_none'].'</p>';
                    }
                    if (isset($snow_auto_start_form)) echo $snow_auto_start_form;

                    // Snow level history table
                    echo '<hr>';
                    echo '<h4 class="h4">'.$this->lang->line('building')['snow_history_title'].'</h4>';
                    if (isset($snow_level_history) && count($snow_level_history) > 0) {
                        echo '<table class="table table-sm table-bordered" style="max-width:300px;">';
                        echo '<thead><tr>';
                        echo '<th>'.$this->lang->line('building')['snow_history_date'].'</th>';
                        echo '<th>'.$this->lang->line('building')['snow_history_level'].'</th>';
                        echo '</tr></thead><tbody>';
                        foreach ($snow_level_history as $history_row) {
                            echo '<tr>';
                            echo '<td>'.$history_row->date.'</td>';
                            echo '<td>'.(int)$history_row->snow_level.'</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p class="text-base-content/60">'.$this->lang->line('building')['snow_history_none'].'</p>';
                    }

                    // Individual cannon management table
                    if (isset($cannon_list) && count($cannon_list) > 0) {
                        echo '<hr>';
                        echo '<h4 class="h4">'.$this->lang->line('building')['individual_cannons_title'].'</h4>';
                        echo '<table class="table table-bordered overflow-x-auto">';
                        echo '<thead><tr>';
                        echo '<th>'.$this->lang->line('building')['cannon_number'].'</th>';
                        echo '<th>'.$this->lang->line('building')['cannon_level_col'].'</th>';
                        echo '<th>'.$this->lang->line('building')['cannon_snow_output_col'].'</th>';
                        echo '<th>'.$this->lang->line('building')['cannon_daily_cost_col'].'</th>';
                        echo '<th>'.$this->lang->line('building')['cannon_status_col'].'</th>';
                        echo '<th>'.$this->lang->line('building')['cannon_action_col'].'</th>';
                        echo '</tr></thead><tbody>';
                        foreach ($cannon_list as $cannon) {
                            echo '<tr>';
                            echo '<td>'.$cannon['number'].'</td>';
                            echo '<td>'.$cannon['level'].'</td>';
                            echo '<td>'.$cannon['capacity'].' '.$this->lang->line('building')['cm'].'</td>';
                            echo '<td>'.$cannon['daily_cost'].' €</td>';
                            echo '<td><span class="'.$cannon['status_class'].'">'.$cannon['status_label'].'</span></td>';
                            echo '<td>'.$cannon['toggle_button'].'</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    }

                    // Link to Snowmaking page
                    echo '<hr>';
                    echo '<a href="'.base_url().'trail_snowmaking_controller" class="btn btn-outline btn-sm">'.$this->lang->line('building')['snowmaking_page_link'].' &rarr;</a>';
                }?>
                
            </div>
    </div>
    <!-- END Building block -->
    <?php
}
// end ONLY IF TOURIST INFO BUILT
// Info Messages related to the building type
    if (isset($infoMessage) && $infoMessage == 'tourist_info_required'){
        echo $this->lang->line('building')[$infoMessage];
    }
    else if (isset($infoMessage) && $infoMessage == 'achievement_locked')
        echo $infoMessage_text;
    ?>
    
</div>
 