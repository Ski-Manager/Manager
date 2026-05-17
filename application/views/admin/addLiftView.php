<div class="w-full">
<?php

echo '<legend>'.$title.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "add_new_lift");
             echo form_open("admin/admin_lift_controller/add_new_lift_validation", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');
            //foreach ($lift_array as $lift_object) { 
            ?>
                <div class="form-group group_form">
                    <div class="md:col-span-2 mb-3">
                        <label for="id_group" class="label"><?php echo $this->lang->line('admin_page')['id_group']; ?></label>
                     <input name="id_group" class="id_group input input-sm" type="text" value="<?php echo set_value('id_group', $max_id_group); ?>" size="2" />
                    </div>
                    <div class="md:col-span-2 mb-3">
                        <label for="lift_type" class="label"><?php echo $this->lang->line('admin_page')['type']; ?></label>
                        <select id="lift_type" name="lift_type" class="select select-sm w-full lift_type"> 
                            <?php echo $select_lift_types; ?>
                        </select>
                    </div>
                    <div class="md:col-span-3 mb-3">
                        <label for="grip_type" class="label"><?php echo $this->lang->line('lift')['grip_type']; ?></label>
                        <select id="grip_type" name="grip_type" class="select select-sm w-full grip_type"> 
                            <?php echo $select_grip_types; ?>
                        </select>
                    </div>
                </div>
            <?php for ($i=1; $i<=3; $i++) { ?>
                <div class="form-group group_form">
                    <div class="md:col-span-2">
                         <label for="level" class="label"><?php echo $this->lang->line('home')['level']; ?></label>
                        <input name="level[]" type="text" value="<?php echo set_value('level[]', $i); ?>" size="1" disabled class="input input-sm"/>
                    </div>
                    
                    <div class="md:col-span-2">
                         <label for="speed" class="label"><?php echo $this->lang->line('lift')['length_speed_column']; ?></label>
                        <input name="speed[]" type="text" value="<?php echo set_value('speed[]'); ?>" size="2" class="input input-sm"/>
                        <?php echo $this->lang->line('lift')['speed_unit']; ?>
                    </div>
                    <div class="md:col-span-3">
                        <label for="name_english" class="label"><?php echo $this->lang->line('admin_page')['name_english']; ?></label>
                     <input name="name_english[]" type="text" value="<?php echo set_value('name_english[]'); ?>" size="25" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-3">
                         <label for="name_french" class="label"><?php echo $this->lang->line('admin_page')['name_french']; ?></label>
                        <input name="name_french[]" type="text" value="<?php echo set_value('name_french[]'); ?>" size="25" class="input input-sm" />
                    </div>
                    <div class="md:col-span-2">
                         <label for="capacity" class="label"><?php echo $this->lang->line('lift')['capacity_seats']; ?></label>
                        <input name="capacity[]" type="text" value="<?php echo set_value('capacity[]'); ?>" size="1" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-3">
                        <label for="base_cost" class="label"><?php echo $this->lang->line('home')['base_cost']; ?></label>
                        <?php $base_cost_data = array(
                                'type'  => 'text',
                                'name'  => 'base_cost[]',
                                'id'    => 'base_cost',
                                'value' => set_value('base_cost[]'),
                                'size' => '12'
                            );
                            echo form_input($base_cost_data);
                        ?> €
                    </div>
                    <div class="md:col-span-3">
                         <label for="meter_cost" class="label"><?php echo $this->lang->line('home')['meter_cost']; ?></label>
                        <input name="meter_cost[]" type="text" value="<?php echo set_value('meter_cost[]'); ?>" size="12"  class="input input-sm"/> €
                    </div>
                    <div class="md:col-span-3">
                         <label for="building_time" class="label"><?php echo $this->lang->line('home')['building_time']; ?></label>
                        <input name="building_time[]" type="text" value="<?php echo set_value('building_time[]'); ?>" size="10" class="input input-sm" />
                        <?php echo $this->lang->line('home')['seconds']; ?>
                    </div>
                    <div class="md:col-span-2">
                         <label for="throughput" class="label"><?php echo $this->lang->line('lift')['throughput']; ?></label>
                        <input name="throughput[]" type="text" value="<?php echo set_value('throughput[]'); ?>" size="6" class="input input-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="reputation" class="label"><?php echo $this->lang->line('home')['reputation']; ?></label>
                        <?php $reputation_data = array(
                                'type'  => 'text',
                                'name'  => 'reputation[]',
                                'id'    => 'reputation',
                                'value' => set_value('reputation[]'),
                                'size' => '4'
                            );
                            echo form_input($reputation_data);
                        ?>
                    </div>
                    <div class="md:col-span-2">
                         <label for="daily_cost" class="label"><?php echo $this->lang->line('home')['daily_cost']; ?></label>
                        <input name="daily_cost[]" type="text" value="<?php echo set_value('daily_cost[]'); ?>" size="6"  class="input input-sm"/> €
                    </div><br>
                </div>
                <?php } ?>
                 
                        <span class="text-error"><?php if (isset($add_lift_error_daily_cost) && $add_lift_error_daily_cost != '') echo $add_lift_error_daily_cost.'<br>'; ?>
                        <?php if (isset($add_lift_error_throughput) && $add_lift_error_throughput != '') echo $add_lift_error_throughput.'<br>'; ?>
                        <?php if (isset($add_lift_error_speed) && $add_lift_error_speed != '') echo $add_lift_error_speed.'<br>'; ?>
                        <?php if (isset($add_lift_error_capacity) && $add_lift_error_capacity != '') echo $add_lift_error_capacity.'<br>'; ?>
                        <?php if (isset($add_lift_error_reputation) && $add_lift_error_reputation != '') echo $add_lift_error_reputation.'<br>'; ?>
                        <?php if (isset($add_lift_error_base_cost) && $add_lift_error_base_cost != '') echo $add_lift_error_base_cost.'<br>'; ?>
                        <?php if (isset($add_lift_error_meter_cost) && $add_lift_error_meter_cost != '') echo $add_lift_error_meter_cost.'<br>'; ?>
                        <?php if (isset($add_lift_error_building_time) && $add_lift_error_building_time != '') echo $add_lift_error_building_time.'<br>'; ?>
                        <?php if (isset($add_lift_error_name_french) && $add_lift_error_name_french != '') echo $add_lift_error_name_french.'<br>'; ?>
                        <?php if (isset($add_lift_error_name_english) && $add_lift_error_name_english != '') echo $add_lift_error_name_english.'<br>'; ?>
                        <?php if (isset($add_lift_error_level) && $add_lift_error_level != '') echo $add_lift_error_level.'<br>'; ?>
                        <?php if (isset($add_lift_error_type) && $add_lift_error_type != '') echo $add_lift_error_type.'<br>'; ?>
                        <?php if (isset($add_lift_error_grip_type) && $add_lift_error_grip_type != '') echo $add_lift_error_grip_type.'<br>'; ?>
                        <?php if (isset($add_lift_error_id_building) && $add_lift_error_id_lift != '') echo $add_lift_error_id_lift.'<br>'; ?>
                        <?php if (isset($add_lift_error_id_group) && $add_lift_error_id_group != '') echo $add_lift_error_id_group.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_lift_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('add_new_lift', 'add_new_lift'); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>