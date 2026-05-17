<div class="w-full">
    <?php

// General title
echo $mainTitle;
echo $introEventVenues;
$building_type_array = array ('housing_complex' , 'icerink', 'curling_center', 'open_stage');

if ($hideBuilding != true) {
        foreach ($building_type_array as $type) {
?>        
    <!-- START Access Resort -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
            <div class="col-span-12"> <?php echo $title[$type];?> </div>
            <div class="col-span-2 center"> <?php echo $logo[$type];?> </div>
            <div class="col-span-10"> <?php echo $desc[$type];?>  </div>
            <div class="md:col-span-12"> 
        <?php
        //var_dump($infoMessage);
        if (isset($infoMessage[$type]) && $infoMessage[$type] != null)
            echo $this->lang->line('building')[$infoMessage[$type]];
        ?>
                <table class="table overflow-x-auto building">
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
                            <td><?php echo $infrastructureName[$type][1];?></td>
                            <td><?php echo $infrastructureName[$type][2];?></td>
                            <td><?php echo $infrastructureName[$type][3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['cost'];?></th>
                            <td><?php echo $buildingCost[$type][1];?> €</td>
                            <td><?php echo $buildingCost[$type][2];?> €</td>
                            <td><?php echo $buildingCost[$type][3];?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['building_time'];?></th>
                            <td><?php if (isset ($pre_buildingTime[$type][1])) echo $pre_buildingTime[$type][1];?><div data-countdown="<?php echo $buildingTime[$type][1];?>"><?php echo $buildingTime[$type][1];?></div><?php if (isset ($post_buildingTime[$type][1])) echo $post_buildingTime[$type][1];?></td>
                            <td><?php if (isset ($pre_buildingTime[$type][2])) echo $pre_buildingTime[$type][2];?><div data-countdown="<?php echo $buildingTime[$type][2];?>"><?php echo $buildingTime[$type][2];?></div><?php if (isset ($post_buildingTime[$type][2])) echo $post_buildingTime[$type][2];?></td>
                            <td><?php if (isset ($pre_buildingTime[$type][3])) echo $pre_buildingTime[$type][3];?><div data-countdown="<?php echo $buildingTime[$type][3];?>"><?php echo $buildingTime[$type][3];?></div><?php if (isset ($post_buildingTime[$type][3])) echo $post_buildingTime[$type][3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['reputation'];?></th>
                            <td><?php echo $reputation[$type][1];?></td>
                            <td><?php echo $reputation[$type][2];?></td>
                            <td><?php echo $reputation[$type][3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['capacity'];?></th>
                            <td><?php echo $capacity[$type][1];?></td>
                            <td><?php echo $capacity[$type][2];?></td>
                            <td><?php echo $capacity[$type][3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['daily_cost'];?></th>
                            <td><?php echo $daily_cost[$type][1];?></td>
                            <td><?php echo $daily_cost[$type][2];?></td>
                            <td><?php echo $daily_cost[$type][3];?></td>
                        </tr>
                        <tr>
                            <th></th>
                            <?php
                            echo $button[$type][1];
                            echo $button[$type][2];
                            echo $button[$type][3];
                            ?>
                        </tr>
                    </tbody>
                </table> 
            </div>
    </div>
    <!-- END Access Resort -->
    
    <?php } 
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