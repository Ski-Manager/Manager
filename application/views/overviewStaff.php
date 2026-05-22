<div class="w-full">
    <?php

// General page title
echo $title;
echo $introOverviewStaff;

// Info Messages for specific actions
    if (isset($infoMessage))
        echo '<p>'.$this->lang->line('overviewStaff')[$infoMessage].'</p>';

// Morale summary banner
    if (isset($morale_summary) && $morale_summary && $morale_summary->total_staff > 0) {
        $avg_morale = round($morale_summary->avg_morale);
        $strike_count = (int)$morale_summary->strike_count;
        if ($strike_count > 0) {
            echo '<div class="alert alert-error"><strong>'.$this->lang->line('overviewStaff')['strike_alert'].'</strong> '.
                 sprintf($this->lang->line('overviewStaff')['strike_count'], $strike_count).'</div>';
        } elseif ($avg_morale <= 50) {
            echo '<div class="alert alert-warning">'.$this->lang->line('overviewStaff')['low_morale_warning'].'</div>';
        }
    }

    if ($player_has_staff) {
?>
    <!-- Training info banner -->
    <div class="alert alert-info flex items-center gap-2 mb-3">
        <i class="fa-solid fa-graduation-cap text-xl"></i>
        <span>
            <?php echo htmlspecialchars($this->lang->line('overviewStaff')['train_info'], ENT_QUOTES, 'UTF-8'); ?>
            <strong><?php echo number_format(STAFF_TRAINING_COST); ?> €</strong>
            <?php echo htmlspecialchars($this->lang->line('overviewStaff')['train_info_xp'], ENT_QUOTES, 'UTF-8'); ?>
            <strong><?php echo STAFF_TRAINING_XP; ?> XP</strong>.
        </span>
    </div>

    <!-- START Staff Block -->
    <div class="card bg-base-100 shadow-sm"><div class="card-body">
        <div class="overflow-x-auto">
            <table class="table staff center" align="center">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_staff')['position'];?></th>
                            <th><?php echo $this->lang->line('common_staff')['efficiency'];?></th>
                            <th><?php echo $this->lang->line('common_staff')['skill_level'];?></th>
                            <th><?php echo $this->lang->line('common_staff')['salary'];?></th>
                            <th><?php echo $this->lang->line('overviewStaff')['hiring_date'];?></th>
                            <th><?php echo $this->lang->line('overviewStaff')['morale'];?></th>
                            <th><?php echo $this->lang->line('overviewStaff')['assigned_to'];?></th>
                            <th><?php echo $this->lang->line('home')['action'];?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php echo $rowsStaff;?>

                    
                    
                        </tr>
                    </tbody>
                </table>
        </div>
    </div>
    
    


<div id="dialog-confirm" style="display:none;">
<?php echo $this->lang->line('overviewStaff')['confirm_fire'];?>
</div>
  
    

    <!-- END Staff block -->

    <script>
    var TrainStaff = {
        url:         '<?php echo base_url("overview_staff_controller/train_staff"); ?>',
        msgSuccess:  <?php echo json_encode($this->lang->line('overviewStaff')['train_success']); ?>,
        msgLevelUp:  <?php echo json_encode($this->lang->line('overviewStaff')['train_level_up']); ?>,
        msgMax:      <?php echo json_encode($this->lang->line('overviewStaff')['train_max_level']); ?>,
        msgCooldown: <?php echo json_encode($this->lang->line('overviewStaff')['train_cooldown']); ?>,
        msgCash:     <?php echo json_encode($this->lang->line('overviewStaff')['train_not_enough_cash']); ?>,
        msgError:    <?php echo json_encode($this->lang->line('overviewStaff')['train_error']); ?>,
        maxLevel:    <?php echo STAFF_MAX_SKILL_LEVEL; ?>
    };
    </script>

    <?php } ?>
</div>