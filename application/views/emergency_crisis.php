<div class="w-full">

    <!-- DaisyUI radio-based tabs -->
    <div class="tabs tabs-bordered mb-3" id="emergencyCrisisTab">

        <!-- ===== Emergency & Rescue tab ===== -->
        <label class="tab" id="emergency-tab">
            <input type="radio" name="emergency_crisis_tabs" checked />
            <i class="fa-solid fa-hospital mr-1"></i><?php echo $this->lang->line('building')['emergency_title']; ?>
        </label>
        <div class="tab-content" id="emergency-panel">
            <h2 class="h2"><?php echo $this->lang->line('building')['emergency_title']; ?></h2>
            <p><?php echo $this->lang->line('building')['emergency_page_intro']; ?></p>

            <?php
            if (isset($infoMessage) && $infoMessage != '') {
                $msg_keys = [
                    'emergency_settings_saved',
                    'emergency_invalid_settings',
                    'emergency_save_error',
                ];
                if (in_array($infoMessage, $msg_keys, TRUE)) {
                    echo $this->lang->line('building')[$infoMessage];
                }
            }
            ?>

            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:700px;">
                <h4 class="h4"><?php echo $this->lang->line('building')['emergency_how_it_works']; ?></h4>
                <p><?php echo $this->lang->line('building')['emergency_how_it_works_desc']; ?></p>
                <ul>
                    <li><?php echo $this->lang->line('building')['emergency_mechanic_rescue']; ?></li>
                    <li><?php echo $this->lang->line('building')['emergency_mechanic_medical']; ?></li>
                    <li><?php echo $this->lang->line('building')['emergency_mechanic_insurance']; ?></li>
                    <li><?php echo $this->lang->line('building')['emergency_mechanic_reputation']; ?></li>
                </ul>
            </div>

            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:620px;">
                <h4 class="h4"><?php echo $this->lang->line('building')['emergency_settings_title']; ?></h4>
                <form method="post" action="<?php echo base_url('emergency_controller/save'); ?>">
                    <input type="hidden" name="emergency_form" value="1">

                    <div class="mb-3">
                        <label for="rescue_team_level" class="label">
                            <?php echo $this->lang->line('building')['emergency_rescue_label']; ?>
                        </label>
                        <select id="rescue_team_level" name="rescue_team_level" class="select select-sm" style="max-width:220px;">
                            <?php for ($i = 0; $i <= 3; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($rescue_team_level == $i) ? 'selected' : ''; ?>>
                                    <?php echo $this->lang->line('building')['emergency_level_' . $i]; ?>
                                    (<?php echo $rescue_cost[$i]; ?> €/<?php echo $this->lang->line('building')['emergency_per_night']; ?>)
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['emergency_rescue_help']; ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="medical_stations" class="label">
                            <?php echo $this->lang->line('building')['emergency_medical_label']; ?>
                        </label>
                        <select id="medical_stations" name="medical_stations" class="select select-sm" style="max-width:220px;">
                            <?php for ($i = 0; $i <= 3; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($medical_stations == $i) ? 'selected' : ''; ?>>
                                    <?php echo $this->lang->line('building')['emergency_level_' . $i]; ?>
                                    (<?php echo $medical_cost[$i]; ?> €/<?php echo $this->lang->line('building')['emergency_per_night']; ?>)
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['emergency_medical_help']; ?></div>
                    </div>

                    <div class="mb-3">
                        <div class="flex items-center gap-2">
                            <input class="toggle" type="checkbox"
                                   id="insurance_enabled" name="insurance_enabled" value="1"
                                   <?php echo ($insurance_enabled == 1) ? 'checked' : ''; ?>>
                            <label for="insurance_enabled">
                                <?php echo $this->lang->line('building')['emergency_insurance_label']; ?>
                                <small class="text-base-content/60">(<?php echo $insurance_daily_cost; ?> €/<?php echo $this->lang->line('building')['emergency_per_night']; ?>)</small>
                            </label>
                        </div>
                        <div class="text-sm opacity-60"><?php echo $this->lang->line('building')['emergency_insurance_help']; ?></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('building')['emergency_save_btn']; ?>
                    </button>
                </form>
            </div>

            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3" style="max-width:620px;">
                <h4 class="h4"><?php echo $this->lang->line('building')['emergency_status_title']; ?></h4>
                <div class="overflow-x-auto">
                <table class="table table-sm">
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_rescue_label']; ?></td>
                        <td><strong><?php echo $this->lang->line('building')['emergency_level_' . $rescue_team_level]; ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_medical_label']; ?></td>
                        <td><strong><?php echo $this->lang->line('building')['emergency_level_' . $medical_stations]; ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_response_time_label']; ?></td>
                        <td>
                            <strong><?php echo $current_response_time; ?> min</strong>
                            <?php if ($current_response_time < $fast_threshold): ?>
                                <span class="badge badge-success ml-1"><?php echo $this->lang->line('building')['emergency_response_fast']; ?></span>
                            <?php elseif ($current_response_time > $poor_threshold): ?>
                                <span class="badge badge-error ml-1"><?php echo $this->lang->line('building')['emergency_response_poor']; ?></span>
                            <?php else: ?>
                                <span class="badge badge-warning ml-1"><?php echo $this->lang->line('building')['emergency_response_average']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_insurance_status_label']; ?></td>
                        <td>
                            <?php if ($insurance_enabled == 1): ?>
                                <span class="badge badge-success"><?php echo $this->lang->line('building')['emergency_insurance_on']; ?></span>
                            <?php else: ?>
                                <span class="badge badge-error"><?php echo $this->lang->line('building')['emergency_insurance_off']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_daily_cost_label']; ?></td>
                        <td><strong>
                            <?php
                            $total_cost = $rescue_cost[$rescue_team_level]
                                        + $medical_cost[$medical_stations]
                                        + ($insurance_enabled ? $insurance_daily_cost : 0);
                            echo number_format($total_cost, 0, '.', ' '); ?> €
                        </strong></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_incident_chance_label']; ?></td>
                        <td><?php echo $incident_chance; ?>%</td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_fine_label']; ?></td>
                        <td>
                            <?php if ($insurance_enabled == 1): ?>
                                <?php echo number_format($fine_with_insurance, 0, '.', ' '); ?> €
                                <small class="text-base-content/60">(<?php echo $this->lang->line('building')['emergency_fine_insured_note']; ?>)</small>
                            <?php else: ?>
                                <span class="text-error"><?php echo number_format($fine_no_insurance, 0, '.', ' '); ?> €</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('building')['emergency_rep_effect_label']; ?></td>
                        <td>
                            <?php if ($current_response_time < $fast_threshold): ?>
                                <span class="text-success">+<?php echo $rep_fast_bonus; ?> <?php echo $this->lang->line('building')['emergency_rep_per_night']; ?></span>
                            <?php elseif ($current_response_time > $poor_threshold): ?>
                                <span class="text-error"><?php echo $rep_poor_penalty; ?> <?php echo $this->lang->line('building')['emergency_rep_per_night']; ?></span>
                            <?php else: ?>
                                <span class="text-base-content/60">0 <?php echo $this->lang->line('building')['emergency_rep_per_night']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                </div>
            </div>
        </div>

        <!-- ===== Crisis Events tab ===== -->
        <label class="tab" id="crisis-tab">
            <input type="radio" name="emergency_crisis_tabs" />
            <i class="fa-solid fa-triangle-exclamation mr-1"></i><?php echo $this->lang->line('logs')['crisis_title']; ?>
        </label>
        <div class="tab-content" id="crisis-panel">
            <?php echo $crisis_title; ?>
            <?php echo $crisis_intro; ?>

            <?php if ($crisis_resort_built): ?>

            <div class="card bg-base-100 shadow-sm "><div class="card-body mb-4">
                <h4 class="h4"><?php echo $this->lang->line('logs')['crisis_active_events']; ?></h4>

                <?php if (empty($active_crisis_events)): ?>
                    <div class="alert alert-success">
                        <?php echo $this->lang->line('logs')['crisis_no_active']; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($active_crisis_events as $event): ?>
                    <div class="alert alert-error flex justify-between items-start">
                        <div>
                            <strong>
                                <?php echo htmlspecialchars($this->lang->line('logs')['crisis_type_' . $event->event_type] ?? $event->event_type); ?>
                            </strong>
                            &mdash;
                            <?php echo htmlspecialchars($event->event_date); ?>
                            <br>
                            <?php echo htmlspecialchars($event->impact_description ?? ''); ?>
                        </div>
                        <form method="post" action="<?php echo base_url() . 'crisis_controller/resolve'; ?>" class="ml-3">
                            <input type="hidden" name="id_crisis" value="<?php echo (int)$event->id_crisis; ?>">
                            <button type="submit" class="btn btn-sm btn-outline btn-error">
                                <?php echo $this->lang->line('logs')['crisis_mark_resolved']; ?>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="card bg-base-100 shadow-sm"><div class="card-body">
                <h4 class="h4"><?php echo $this->lang->line('logs')['crisis_history']; ?></h4>

                <?php if (empty($all_crisis_events)): ?>
                    <div class="alert alert-info">
                        <?php echo $this->lang->line('logs')['crisis_no_history']; ?>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                    <table class="table table-zebra" id="crisisTable">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('logs')['datetime']; ?></th>
                                <th><?php echo $this->lang->line('logs')['type']; ?></th>
                                <th><?php echo $this->lang->line('logs')['crisis_impact']; ?></th>
                                <th><?php echo $this->lang->line('logs')['crisis_status']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_crisis_events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event->event_date); ?></td>
                                <td>
                                    <span class="badge <?php echo $event->is_resolved ? 'badge-neutral' : 'badge-error'; ?>">
                                        <?php echo htmlspecialchars($this->lang->line('logs')['crisis_type_' . $event->event_type] ?? $event->event_type); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($event->impact_description ?? ''); ?></td>
                                <td>
                                    <?php if ($event->is_resolved): ?>
                                        <span class="badge badge-success"><?php echo $this->lang->line('logs')['crisis_resolved']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-error"><?php echo $this->lang->line('logs')['crisis_unresolved']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>

            <?php endif; ?>
        </div>

    </div><!-- .tabs -->
</div>
