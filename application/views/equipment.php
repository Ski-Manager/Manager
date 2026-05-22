<div class="w-full">
    <?php

// General page title
echo $title;
echo $introEquipment;

// start ONLY IF TOURIST INFO BUILT
if ($hideEquipment != true) { ?>        
    <!-- START equipment BLock -->
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">            
            <div class="col-span-2 center"> <?php echo $equipmentLogo;?> </div>
            <div class="col-span-10"> <?php echo $equipmentDesc;?>  </div>
            <div class="md:col-span-12"> 
             <?php   if (isset($infoMessage) && $infoMessage != '' && ( $infoMessage != 'not_enough_money' || $infoMessage == 'rush_completed'|| $infoMessage == 'not_enough_genepis'|| $infoMessage == 'already_completed'))
                        echo $this->lang->line('common_equipment')[$infoMessage];
                    else if (isset($infoMessage) && $infoMessage == 'not_enough_money'  )
                        echo $this->lang->line('home')[$infoMessage];
             ?>
                <table class="table overflow-x-auto building_6th" align="center">
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
                            <th><?php echo $this->lang->line('common_equipment')['equipment_name'];?></th>
                            <td><?php echo $equipmentName[1];?></td>
                            <td><?php echo $equipmentName[2];?></td>
                            <td><?php echo $equipmentName[3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('common_equipment')['equipment_cost'];?></th>
                            <td><?php echo $equipmentCost[1];?> €</td>
                            <td><?php echo $equipmentCost[2];?> €</td>
                            <td><?php echo $equipmentCost[3];?> €</td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['equipment_delivery_time'];?></th>
                            <td>
                                <?php if (isset ($wait_status[1])) echo $pre_delivery_time[1];?>    <!-- PRE value is used for eventual tooltip is WAIT status -->
                                <div data-countdown="<?php echo $deliveryTime[1];?>"><?php echo $deliveryTime[1];?></div> <!-- Countdown div -->
                                <?php if (isset ($wait_status[1])) echo $post_delivery_time[1];?>   <!-- POST value is used for eventual tooltip is WAIT status, to close the div -->
                            </td>
                            <td>
                                <?php if (isset ($wait_status[2])) echo $pre_delivery_time[2];?>
                                <div data-countdown="<?php echo $deliveryTime[2];?>"><?php echo $deliveryTime[2];?></div>
                                <?php if (isset ($wait_status[2])) echo $post_delivery_time[2];?>
                            </td>
                            <td>
                                <?php if (isset ($wait_status[3])) echo $pre_delivery_time[3];?>
                                <div data-countdown="<?php echo $deliveryTime[3];?>"><?php echo $deliveryTime[3];?></div>
                                <?php if (isset ($wait_status[3])) echo $post_delivery_time[3];?>
                            </td>
                            
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('home')['reputation'];?></th>
                            <td><?php echo $equipmentReputation[1];?></td>
                            <td><?php echo $equipmentReputation[2];?></td>
                            <td><?php echo $equipmentReputation[3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line($equipment_type)['coverage'];?></th>
                            <td><?php echo $coverage[1];?></td>
                            <td><?php echo $coverage[2];?></td>
                            <td><?php echo $coverage[3];?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('building')['quantity'];?></th>
                            <td><span id="quantity_1"><?php echo $equipmentQuantity[1];?></span></td>
                            <td><span id="quantity_2"><?php echo $equipmentQuantity[2];?></span></td>
                            <td><span id="quantity_3"><?php echo $equipmentQuantity[3];?></span></td>
                        </tr>
                        <tr>
                            <th></th>
                            <?php
                            echo $equipmentButton[1];    // For BUY button
                            echo $equipmentButton[2];    // For UPGRADE button
                            echo $equipmentButton[3];    // For UPGRADE button
                            ?>
                        </tr>
                    </tbody>
                </table> 
            </div>
            <div class="md:col-span-12">
            <!-- START assign equipment -->
                <?php echo $rowEquipment;   ?>
                <!-- END assign equipment -->
            </div>
    </div>
            <!-- END equipment block -->
    <?php
    
}
// end ONLY IF TOURIST INFO BUILT
// Info Messages related to the equipment type
    if (isset($infoMessage) && $infoMessage == 'tourist_info_required')
        echo $this->lang->line('building')[$infoMessage];
    ?>

    <div id="dialog-confirm-sellequip" style="display:none;">
<?php echo $this->lang->line('common_equipment')['confirm_sell_equip'];?>
</div>
    
</div>

 