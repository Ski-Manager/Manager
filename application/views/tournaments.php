<div class="w-full">
    
    <?php

// General page title
echo $title;
echo $intro;
?>    
<!-- START Achievements Block -->
<div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">     
    
    <div class="col-span-2 center"> <?php echo $tournamentLogo;?> </div>
    <div class="col-span-10"> <?php echo $tournamentDesc;?>  </div>
    <div class="col-span-10 padding_top_bot_15"> <?php echo $for_help_with_tournaments;?>  </div>
    <div class="col-span-10"> <?php echo $history_all_tournaments;?>  </div>

    <?php if (isset($tournament_stats) && $tournament_stats !== null && (int)$tournament_stats->total_organized > 0) : ?>
    <div class="col-span-12 padding_top_bot_15">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $this->lang->line('tournaments')['stats_title']; ?></h5>
                <div class="flex flex-wrap gap-3">
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$tournament_stats->total_organized, 0, ',', ' '); ?></div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('tournaments')['stats_total_organized']; ?></div>
                    </div>
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$tournament_stats->total_visitors, 0, ',', ' '); ?></div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('tournaments')['stats_total_visitors']; ?></div>
                    </div>
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary"><?php echo number_format((int)$tournament_stats->total_revenue, 0, ',', ' '); ?>€</div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('tournaments')['stats_total_revenue']; ?></div>
                    </div>
                    <div class="text-center p-3 bg-base-200 rounded" style="min-width:130px;">
                        <div class="text-2xl font-bold text-primary">+<?php echo number_format((int)$tournament_stats->total_prestige, 0, ',', ' '); ?></div>
                        <div class="text-xs text-base-content opacity-60"><?php echo $this->lang->line('tournaments')['stats_total_prestige']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($tournament_history)) : ?>
    <div class="col-span-12 padding_top_bot_15">
        <h5 class="h5"><?php echo $this->lang->line('tournaments')['history_title']; ?></h5>
        <?php if ($tournament_history->num_rows() > 0) : ?>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('tournaments')['history_name']; ?></th>
                        <th><?php echo $this->lang->line('tournaments')['history_end_date']; ?></th>
                        <th><?php echo $this->lang->line('tournaments')['history_visitors']; ?></th>
                        <th><?php echo $this->lang->line('tournaments')['history_revenue']; ?></th>
                        <th><?php echo $this->lang->line('tournaments')['history_prestige']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tournament_history->result() as $h) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($h->tournament_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($h->end_date, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format((int)$h->aggregated_visitors, 0, ',', ' '); ?></td>
                        <td><?php echo number_format((int)$h->aggregated_revenue, 0, ',', ' '); ?>€</td>
                        <td>+<?php echo number_format((int)$h->tournament_points, 0, ',', ' '); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p><?php echo $this->lang->line('tournaments')['history_no_tournaments']; ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
        <div class="md:col-span-12"> 
            
            <?php 
            
            if (isset($infoMessage_text))
                echo $infoMessage_text;
            
            if ($hideList == FALSE) {
                echo '<div class="padding_top_bot_15">';
                    echo '<div id="lastTournamentTable">';
                        echo $lastTournamentTable;
                    echo '</div>';
                        echo $table_tournaments;
                echo '</div>';
            }
            
            ?>
                           

        </div>
</div>
<!-- END Achievements block -->

 </div>