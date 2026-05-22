<div class="w-full">

    <?php echo $title; ?>
    <?php echo $intro; ?>

    <?php if ($resort_built): ?>

    <!-- Active crisis events -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-4">
        <h4 class="h4"><?php echo $this->lang->line('logs')['crisis_active_events']; ?></h4>

        <?php if (empty($active_events)): ?>
            <div class="alert alert-success">
                <?php echo $this->lang->line('logs')['crisis_no_active']; ?>
            </div>
        <?php else: ?>
            <?php foreach ($active_events as $event): ?>
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

    <!-- All crisis events history -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <h4 class="h4"><?php echo $this->lang->line('logs')['crisis_history']; ?></h4>

        <?php if (empty($all_events)): ?>
            <div class="alert alert-info">
                <?php echo $this->lang->line('logs')['crisis_no_history']; ?>
            </div>
        <?php else: ?>
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
                    <?php foreach ($all_events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event->event_date); ?></td>
                        <td>
                            <span class="badge <?php echo $event->is_resolved ? 'bg-neutral' : 'bg-error'; ?>">
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
        <?php endif; ?>
    </div>

    <?php endif; ?>
</div>
