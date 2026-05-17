<div class="w-full">
    <?php

// General page title
echo $title;
echo $introLogs;

// Info Messages for specific actions
    if (isset($infoMessage))
        echo '<p>'.$this->lang->line('logs')[$infoMessage].'</p>';
    if ($resort_built) {
?>     
    <!-- START Building BLock -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3"> 
            <!-- Skeleton loading placeholder shown while AJAX data loads -->
            <div id="logs-skeleton" class="sm-skeleton-table" aria-busy="true" aria-label="<?php echo $this->lang->line('logs')['loading'] ?? 'Loading…'; ?>">
                <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="sm-skeleton-row">
                    <div class="skeleton sm-skeleton-cell" style="width:18%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:12%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:65%"></div>
                </div>
                <?php endfor; ?>
            </div>
            <table align="center" id="myTableLogs" class="table table-zebra achievements hidden" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th style="min-width:150px;"><?php echo $this->lang->line('logs')['datetime'];?></th>
                            <th><?php echo $this->lang->line('logs')['type'];?></th>
                            <th><?php echo $this->lang->line('home')['message'];?></th>
                        </tr>
                    </thead>
                </table>
    </div>
    <!-- END Building block -->
       
    </div>
    <?php } ?>
</div>