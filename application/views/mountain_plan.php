<div class="w-full">
<?php

echo '<h2 class="h2">' . $this->lang->line('building')['plan_title'] . '</h2>';
echo '<p>' . $this->lang->line('building')['plan_intro'] . '</p>';

// Info / action message
if (isset($infoMessage) && $infoMessage != '') {
    $msg_keys = [
        'bad_action',
        'plan_created', 'plan_saved', 'plan_deleted',
        'plan_submitted', 'plan_activated', 'plan_revised',
        'plan_withdrawn', 'plan_duplicated',
        'plan_not_editable', 'plan_not_submittable', 'plan_not_activatable',
        'plan_not_revisable', 'plan_not_deletable', 'plan_not_withdrawable',
        'plan_not_enough_cash', 'plan_validation_error',
    ];
    if (in_array($infoMessage, $msg_keys, TRUE)) {
        echo $this->lang->line('building')[$infoMessage];
    }
}

?>

<!-- ===== Info box ===== -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
<div class="col-span-12 md:col-span-10 lg:col-span-8">

    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <h4 class="h4"><?php echo $this->lang->line('building')['plan_how_it_works']; ?></h4>
        <p><?php echo $this->lang->line('building')['plan_how_it_works_desc']; ?></p>
        <ul>
            <li><?php echo $this->lang->line('building')['plan_step_draft']; ?></li>
            <li><?php echo $this->lang->line('building')['plan_step_submit']; echo ' &mdash; <strong>' . $submission_cost . ' €</strong>'; ?></li>
            <li><?php echo $this->lang->line('building')['plan_step_review']; echo ' (' . $approval_days . ' ' . $this->lang->line('building')['plan_days'] . ')'; ?></li>
            <li><?php echo $this->lang->line('building')['plan_step_activate']; ?></li>
            <li><?php echo $this->lang->line('building')['plan_step_expire']; echo ' (' . $duration_days . ' ' . $this->lang->line('building')['plan_days'] . ')'; ?></li>
        </ul>
        <p class="text-warning">
            <strong><?php echo $this->lang->line('building')['plan_revision_warning']; ?></strong>
            <?php echo $this->lang->line('building')['plan_revision_desc']; echo ' ' . $revision_cost . ' €, -' . $revision_rep . ' ' . $this->lang->line('building')['plan_reputation']; ?>
        </p>
    </div>

    <!-- ===== New plan button ===== -->
    <div class="mb-3">
        <a href="<?php echo base_url('mountain_plan_controller/create'); ?>" class="btn btn-primary">
            <?php echo $this->lang->line('building')['plan_create_new']; ?>
        </a>
    </div>

    <!-- ===== Plan list ===== -->
    <?php if (empty($all_plans)): ?>
        <div class="alert alert-info">
            <?php echo $this->lang->line('building')['plan_none']; ?>
        </div>
    <?php else: ?>
        <?php foreach ($all_plans as $plan): ?>
            <?php
            $status_class = [
                'draft'     => 'neutral',
                'submitted' => 'warning',
                'approved'  => 'info',
                'active'    => 'success',
                'expired'   => 'dark',
            ][$plan->status] ?? 'neutral';
            $status_label = $this->lang->line('building')['plan_status_' . $plan->status] ?? $plan->status;
            ?>
            <div class="card mb-3">
                <div class="card-header flex justify-between items-center">
                    <span>
                        <strong><?php echo htmlspecialchars($plan->plan_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                        &nbsp;
                        <span class="badge badge-<?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                        <?php if ($plan->change_count > 0): ?>
                            <span class="badge badge-error ml-1"><?php echo $this->lang->line('building')['plan_revised_badge']; ?> &times;<?php echo (int)$plan->change_count; ?></span>
                        <?php endif; ?>
                    </span>
                    <small class="text-base-content/60"><?php echo $this->lang->line('building')['plan_created_on']; ?> <?php echo date('Y-m-d', strtotime($plan->created_at)); ?></small>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-12 gap-3 mb-2">
                        <div class="md:col-span-6">
                            <h6 class="h6"><?php echo $this->lang->line('building')['plan_expansion_strategy']; ?></h6>
                            <p><?php echo nl2br(htmlspecialchars($plan->expansion_strategy, ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                        <div class="md:col-span-6">
                            <h6 class="h6"><?php echo $this->lang->line('building')['plan_environmental_notes']; ?></h6>
                            <p><?php echo nl2br(htmlspecialchars($plan->environmental_notes, ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-3 mb-2">
                        <div class="col-auto">
                            <span class="badge badge-ghost border">
                                <?php echo $this->lang->line('building')['plan_zoning_slopes']; ?>: <?php echo (int)$plan->zoning_limit_slopes; ?>
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-ghost border">
                                <?php echo $this->lang->line('building')['plan_zoning_lifts']; ?>: <?php echo (int)$plan->zoning_limit_lifts; ?>
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-ghost border">
                                <?php echo $this->lang->line('building')['plan_zoning_buildings']; ?>: <?php echo (int)$plan->zoning_limit_buildings; ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($plan->status === 'submitted' && $plan->submitted_at): ?>
                        <p class="text-base-content/60 small">
                            <?php echo $this->lang->line('building')['plan_submitted_on']; ?>
                            <?php echo date('Y-m-d', strtotime($plan->submitted_at)); ?>.
                            <?php echo $this->lang->line('building')['plan_review_pending']; echo ' ' . $approval_days . ' ' . $this->lang->line('building')['plan_days']; ?>.
                        </p>
                    <?php endif; ?>

                    <?php if ($plan->status === 'approved' && $plan->approved_at): ?>
                        <p class="text-base-content/60 small">
                            <?php echo $this->lang->line('building')['plan_approved_on']; ?>
                            <?php echo date('Y-m-d', strtotime($plan->approved_at)); ?>.
                        </p>
                    <?php endif; ?>

                    <?php if ($plan->status === 'active' && $plan->activated_at): ?>
                        <p class="text-base-content/60 small">
                            <?php echo $this->lang->line('building')['plan_activated_on']; ?>
                            <?php echo date('Y-m-d', strtotime($plan->activated_at)); ?>.
                            <?php
                            $expires_on = date('Y-m-d', strtotime($plan->activated_at . ' +' . $duration_days . ' days'));
                            echo $this->lang->line('building')['plan_expires_on'] . ' ' . $expires_on . '.';
                            ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($plan->status === 'expired' && $plan->activated_at): ?>
                        <p class="text-base-content/60 small">
                            <?php
                            $expired_on = date('Y-m-d', strtotime($plan->activated_at . ' +' . $duration_days . ' days'));
                            echo $this->lang->line('building')['plan_expired_on'] . ' ' . $expired_on . '.';
                            ?>
                        </p>
                    <?php endif; ?>

                    <!-- Action buttons -->
                    <div class="mt-2 flex flex-wrap gap-2">
                        <?php if ($plan->status === 'draft'): ?>
                            <a href="<?php echo base_url('mountain_plan_controller/edit/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-secondary">
                                <?php echo $this->lang->line('building')['plan_btn_edit']; ?>
                            </a>
                            <a href="<?php echo base_url('mountain_plan_controller/submit/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-primary"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_submit'] . ' (' . $submission_cost . ' €)', ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_submit']; ?>
                            </a>
                            <a href="<?php echo base_url('mountain_plan_controller/delete/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-error"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_delete'], ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_delete']; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($plan->status === 'submitted'): ?>
                            <a href="<?php echo base_url('mountain_plan_controller/cancel_submission/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-warning"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_withdraw'], ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_withdraw']; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($plan->status === 'approved'): ?>
                            <a href="<?php echo base_url('mountain_plan_controller/activate/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-success"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_activate'], ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_activate']; ?>
                            </a>
                            <a href="<?php echo base_url('mountain_plan_controller/revise/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-warning"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_revise'] . ' (' . $revision_cost . ' €, -' . $revision_rep . ' ' . $this->lang->line('building')['plan_reputation'] . ')', ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_revise']; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($plan->status === 'active'): ?>
                            <span class="badge badge-success align-self-center"><?php echo $this->lang->line('building')['plan_currently_active']; ?></span>
                            <a href="<?php echo base_url('mountain_plan_controller/revise/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-warning"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_revise'] . ' (' . $revision_cost . ' €, -' . $revision_rep . ' ' . $this->lang->line('building')['plan_reputation'] . ')', ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_revise']; ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!in_array($plan->status, ['draft'], TRUE)): ?>
                            <a href="<?php echo base_url('mountain_plan_controller/duplicate/' . $plan->id_master_plan); ?>"
                               class="btn btn-sm btn-outline"
                               onclick="return confirm('<?php echo htmlspecialchars($this->lang->line('building')['plan_confirm_duplicate'], ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo $this->lang->line('building')['plan_btn_duplicate']; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
</div>
</div>
