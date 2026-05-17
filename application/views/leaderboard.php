<div class="w-full">
    <?php

// General page title
echo $title;
echo $introLeaderboard;

// Info Messages for specific actions
    if (isset($infoMessage))
        echo '<p>'.$this->lang->line('logs')[$infoMessage].'</p>';
    if ($resort_built) {
?>
    <!-- DaisyUI radio-based tabs -->
    <div class="tabs tabs-bordered mb-3" id="leaderboardTab">

        <!-- ===== Global tab ===== -->
        <label class="tab">
            <input type="radio" name="leaderboard_tabs" id="lb-global-tab-radio" checked />
            <?php echo $this->lang->line('leaderboard')['tab_global'];?>
        </label>
        <div class="tab-content" id="lb-global-panel">
            <!-- Skeleton loading placeholder shown while AJAX data loads -->
            <div id="lb-global-skeleton" class="sm-skeleton-table" aria-busy="true" aria-label="<?php echo $this->lang->line('leaderboard')['loading'] ?? 'Loading…'; ?>">
                <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="sm-skeleton-row">
                    <div class="skeleton sm-skeleton-cell" style="width:3%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:12%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:15%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:9%"></div>
                </div>
                <?php endfor; ?>
            </div>
            <div class="overflow-x-auto hidden" id="lb-global-wrapper">
                <table align="center" id="myTableLeaderboard" class="table achievements table-zebra" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('leaderboard')['num'];?></th>
                            <th><?php echo $this->lang->line('home')['username'];?></th>
                            <th><?php echo $this->lang->line('home')['resort_name'];?></th>
                            <th><?php echo $this->lang->line('home')['reputation'];?></th>
                            <th><?php echo $this->lang->line('home')['prestige'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['cash'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['opening_date'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['lift_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['slope_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['staff_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['tournament_count'];?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- ===== By Region tab ===== -->
        <label class="tab" id="lb-region-tab">
            <input type="radio" name="leaderboard_tabs" id="lb-region-tab-radio" />
            <?php echo $this->lang->line('leaderboard')['tab_region'];?>
        </label>
        <div class="tab-content" id="lb-region-panel">
            <div id="lb-region-intro" class="mb-2"></div>
            <div id="lb-region-none" class="alert alert-info hidden">
                <?php echo $this->lang->line('leaderboard')['region_none'];?>
            </div>
            <!-- Skeleton loading placeholder shown while AJAX data loads -->
            <div id="lb-region-skeleton" class="sm-skeleton-table" aria-busy="true" aria-label="<?php echo $this->lang->line('leaderboard')['loading'] ?? 'Loading…'; ?>">
                <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="sm-skeleton-row">
                    <div class="skeleton sm-skeleton-cell" style="width:3%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:12%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:15%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:9%"></div>
                </div>
                <?php endfor; ?>
            </div>
            <div class="overflow-x-auto hidden" id="lb-region-wrapper">
                <table align="center" id="myTableLeaderboardRegion" class="table achievements table-zebra" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('leaderboard')['num'];?></th>
                            <th><?php echo $this->lang->line('home')['username'];?></th>
                            <th><?php echo $this->lang->line('home')['resort_name'];?></th>
                            <th><?php echo $this->lang->line('home')['reputation'];?></th>
                            <th><?php echo $this->lang->line('home')['prestige'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['cash'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['opening_date'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['lift_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['slope_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['staff_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['tournament_count'];?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- ===== By Slopes tab ===== -->
        <label class="tab" id="lb-slope-tab">
            <input type="radio" name="leaderboard_tabs" id="lb-slope-tab-radio" />
            <?php echo $this->lang->line('leaderboard')['tab_slope'];?>
        </label>
        <div class="tab-content" id="lb-slope-panel">
            <!-- Skeleton loading placeholder shown while AJAX data loads -->
            <div id="lb-slope-skeleton" class="sm-skeleton-table" aria-busy="true" aria-label="<?php echo $this->lang->line('leaderboard')['loading'] ?? 'Loading…'; ?>">
                <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="sm-skeleton-row">
                    <div class="skeleton sm-skeleton-cell" style="width:3%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:12%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:15%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:10%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:7%"></div>
                    <div class="skeleton sm-skeleton-cell" style="width:9%"></div>
                </div>
                <?php endfor; ?>
            </div>
            <div class="overflow-x-auto hidden" id="lb-slope-wrapper">
                <table align="center" id="myTableLeaderboardSlope" class="table achievements table-zebra" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('leaderboard')['num'];?></th>
                            <th><?php echo $this->lang->line('home')['username'];?></th>
                            <th><?php echo $this->lang->line('home')['resort_name'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['slope_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['lift_count'];?></th>
                            <th><?php echo $this->lang->line('home')['reputation'];?></th>
                            <th><?php echo $this->lang->line('home')['prestige'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['cash'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['opening_date'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['staff_count'];?></th>
                            <th><?php echo $this->lang->line('leaderboard')['tournament_count'];?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div><!-- /.tabs -->

    <div id="currentUserId" class="hidden" value="<?php echo $currentUserId;?>">
    </div>
    <script>
    window.Settings = window.Settings || {};
    window.Settings.days_ago = <?php echo json_encode($this->lang->line('leaderboard')['days_ago']); ?>;
    window.Settings.lb_region_intro = <?php echo json_encode($this->lang->line('leaderboard')['region_intro']); ?>;
    window.Settings.lb_slope_intro = <?php echo json_encode($this->lang->line('leaderboard')['slope_intro']); ?>;
    </script>
    <?php } ?>

</div>

