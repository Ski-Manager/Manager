<div class="w-full">
    <?php

// General page title and intro
if (isset($pageTitle)) echo $pageTitle;
if (isset($introOffSeason)) echo $introOffSeason;

$building_type_array = ['mountain_biking', 'hiking', 'festival', 'wedding_venue', 'alpine_coaster'];

if (!isset($hideBuilding) || $hideBuilding != true) {
    foreach ($building_type_array as $type) {
?>
    <!-- START Off-Season Activity: <?php echo htmlspecialchars($type); ?> -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        <div class="col-span-12"><?php echo isset($title[$type]) ? $title[$type] : ''; ?></div>
        <div class="col-span-2 center"><?php echo isset($logo[$type]) ? $logo[$type] : ''; ?></div>
        <div class="col-span-10"><?php echo isset($desc[$type]) ? $desc[$type] : ''; ?></div>
        <div class="md:col-span-12">
            <?php
            if (isset($infoMessage[$type]) && $infoMessage[$type] != null)
                echo $this->lang->line('building')[$infoMessage[$type]];
            ?>
            <table class="table overflow-x-auto building">
                <thead>
                    <tr>
                        <th class="md:col-span-3"></th>
                        <th class="md:col-span-3"><?php echo $this->lang->line('home')['level']; ?> 1</th>
                        <th class="md:col-span-3"><?php echo $this->lang->line('home')['level']; ?> 2</th>
                        <th class="md:col-span-3"><?php echo $this->lang->line('home')['level']; ?> 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?php echo $this->lang->line('access')['infrastructure']; ?></th>
                        <td><?php echo isset($infrastructureName[$type][1]) ? $infrastructureName[$type][1] : ''; ?></td>
                        <td><?php echo isset($infrastructureName[$type][2]) ? $infrastructureName[$type][2] : ''; ?></td>
                        <td><?php echo isset($infrastructureName[$type][3]) ? $infrastructureName[$type][3] : ''; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('home')['cost']; ?></th>
                        <td><?php echo isset($buildingCost[$type][1]) ? $buildingCost[$type][1] : ''; ?> €</td>
                        <td><?php echo isset($buildingCost[$type][2]) ? $buildingCost[$type][2] : ''; ?> €</td>
                        <td><?php echo isset($buildingCost[$type][3]) ? $buildingCost[$type][3] : ''; ?> €</td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('home')['building_time']; ?></th>
                        <td>
                            <?php if (isset($pre_buildingTime[$type][1])) echo $pre_buildingTime[$type][1]; ?>
                            <div data-countdown="<?php echo isset($buildingTime[$type][1]) ? $buildingTime[$type][1] : ''; ?>"><?php echo isset($buildingTime[$type][1]) ? $buildingTime[$type][1] : ''; ?></div>
                            <?php if (isset($post_buildingTime[$type][1])) echo $post_buildingTime[$type][1]; ?>
                        </td>
                        <td>
                            <?php if (isset($pre_buildingTime[$type][2])) echo $pre_buildingTime[$type][2]; ?>
                            <div data-countdown="<?php echo isset($buildingTime[$type][2]) ? $buildingTime[$type][2] : ''; ?>"><?php echo isset($buildingTime[$type][2]) ? $buildingTime[$type][2] : ''; ?></div>
                            <?php if (isset($post_buildingTime[$type][2])) echo $post_buildingTime[$type][2]; ?>
                        </td>
                        <td>
                            <?php if (isset($pre_buildingTime[$type][3])) echo $pre_buildingTime[$type][3]; ?>
                            <div data-countdown="<?php echo isset($buildingTime[$type][3]) ? $buildingTime[$type][3] : ''; ?>"><?php echo isset($buildingTime[$type][3]) ? $buildingTime[$type][3] : ''; ?></div>
                            <?php if (isset($post_buildingTime[$type][3])) echo $post_buildingTime[$type][3]; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('home')['reputation']; ?></th>
                        <td><?php echo isset($reputation[$type][1]) ? $reputation[$type][1] : ''; ?></td>
                        <td><?php echo isset($reputation[$type][2]) ? $reputation[$type][2] : ''; ?></td>
                        <td><?php echo isset($reputation[$type][3]) ? $reputation[$type][3] : ''; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('home')['capacity']; ?></th>
                        <td><?php echo isset($capacity[$type][1]) ? $capacity[$type][1] : ''; ?></td>
                        <td><?php echo isset($capacity[$type][2]) ? $capacity[$type][2] : ''; ?></td>
                        <td><?php echo isset($capacity[$type][3]) ? $capacity[$type][3] : ''; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('off_season')['max_daily_income']; ?></th>
                        <td><?php echo isset($max_income[$type][1]) ? $max_income[$type][1] : ''; ?> €</td>
                        <td><?php echo isset($max_income[$type][2]) ? $max_income[$type][2] : ''; ?> €</td>
                        <td><?php echo isset($max_income[$type][3]) ? $max_income[$type][3] : ''; ?> €</td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('home')['daily_cost']; ?></th>
                        <td><?php echo isset($daily_cost[$type][1]) ? $daily_cost[$type][1] : ''; ?> €</td>
                        <td><?php echo isset($daily_cost[$type][2]) ? $daily_cost[$type][2] : ''; ?> €</td>
                        <td><?php echo isset($daily_cost[$type][3]) ? $daily_cost[$type][3] : ''; ?> €</td>
                    </tr>
                    <tr>
                        <th></th>
                        <?php
                        echo isset($button[$type][1]) ? $button[$type][1] : '<td></td>';
                        echo isset($button[$type][2]) ? $button[$type][2] : '<td></td>';
                        echo isset($button[$type][3]) ? $button[$type][3] : '<td></td>';
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- END Off-Season Activity: <?php echo htmlspecialchars($type); ?> -->

<?php
    } // end foreach
} // end if !hideBuilding

// Info messages shown outside the building blocks
if (isset($infoMessage) && is_string($infoMessage) && $infoMessage != '' && $infoMessage != 'tourist_info_required') {
    echo $this->lang->line('building')[$infoMessage];
} else if (isset($infoMessage) && $infoMessage == 'tourist_info_required') {
    echo $this->lang->line('building')[$infoMessage];
}
?>
</div>
