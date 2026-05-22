<div class="w-full">
    
    <?php
// General page title
echo $title;
echo $intro;
?>
<script>
Settings.ongoing_special_event = <?php echo json_encode($this->lang->line('special_events')['ongoing_event']); ?>;
</script>    
<!-- START Special Events Block -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">     
    
    <div class="col-span-2 center"> <?php echo $eventLogo;?> </div>
    <div class="col-span-10"> <?php echo $eventDesc;?>  </div>
    <div class="col-span-10 padding_top_bot_15"> <?php echo $for_help_with_events;?>  </div>
    <div class="col-span-10"> <?php echo $history_all_events;?>  </div>

    <?php if (isset($event_stats) && $event_stats !== null && (int)$event_stats->total_organized > 0) : ?>
    <div class="col-span-12 padding_top_bot_15">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $this->lang->line('special_events')['stats_title']; ?></h5>
                <div class="flex flex-wrap gap-3">
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$event_stats->total_organized, 0, ',', ' '); ?></div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('special_events')['stats_total_organized']; ?></div>
                    </div>
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$event_stats->total_visitors, 0, ',', ' '); ?></div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('special_events')['stats_total_visitors']; ?></div>
                    </div>
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-success"><?php echo number_format((int)$event_stats->total_revenue, 0, ',', ' '); ?>€</div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('special_events')['stats_total_revenue']; ?></div>
                    </div>
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary">+<?php echo number_format((int)$event_stats->total_reputation, 0, ',', ' '); ?></div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('special_events')['stats_total_reputation']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($event_history)) : ?>
    <div class="col-span-12 padding_top_bot_15">
        <h5 class="h5"><?php echo $this->lang->line('special_events')['history_title']; ?></h5>
        <?php if ($event_history->num_rows() > 0) : ?>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('special_events')['history_name']; ?></th>
                        <th><?php echo $this->lang->line('special_events')['history_end_date']; ?></th>
                        <th><?php echo $this->lang->line('special_events')['history_visitors']; ?></th>
                        <th><?php echo $this->lang->line('special_events')['history_revenue']; ?></th>
                        <th><?php echo $this->lang->line('special_events')['history_reputation']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($event_history->result() as $h) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($h->event_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($h->end_date, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format((int)$h->aggregated_visitors, 0, ',', ' '); ?></td>
                        <td><?php echo number_format((int)$h->aggregated_revenue, 0, ',', ' '); ?>€</td>
                        <td>+<?php echo number_format((int)$h->reputation_points, 0, ',', ' '); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p><?php echo $this->lang->line('special_events')['history_no_events']; ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="md:col-span-12">
        <div class="padding_top_bot_15">
            <div id="lastEventTable">
                <?php echo $lastEventTable; ?>
            </div>
            <?php echo $table_events; ?>
        </div>
    </div>

</div>
<!-- END Special Events block -->

</div>
