<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_lift_group'].': '.$id_group.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "update_lift_admin");
             echo form_open("admin/admin_lift_controller/update_lift_admin", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
            <div class="form-group group_form">     
                <div class="md:col-span-2 mb-3">
                    <label for="id_group" class="label"><?php echo $this->lang->line('admin_page')['id_group']; ?></label>
                 <input name="id_group" type="text" value="<?php echo set_value('id_group', $id_group); ?>" size="2" class="input input-sm"/>
                </div>
                <div class="md:col-span-2 mb-3">
                    <label for="lift_type" class="label"><?php echo $this->lang->line('admin_page')['type']; ?></label>
                    <select id="lift_type" name="lift_type" class="select select-sm w-full"> 
                        <?php echo $select_lift_types; ?>
                    </select>
                </div>
                <div class="md:col-span-3 mb-3">
                    <label for="grip_type" class="label"><?php echo $this->lang->line('lift')['grip_type']; ?></label>
                    <select id="grip_type" name="grip_type" class="select select-sm w-full"> 
                        <?php echo $select_grip_types; ?>
                    </select>
                </div>
            </div>
             <?php foreach ($lift_array as $lift_object) { ?>
                
                <div class="form-group group_form">   
                    <div class="md:col-span-2">
                        <label for="id_lift" class="label"><?php echo $this->lang->line('admin_page')['id_lift']; ?></label>
                     <input name="id_lift[]" type="text" value="<?php echo set_value('id_lift[]', $lift_object->id_lift); ?>" size="3" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-2">
                         <label for="level" class="label"><?php echo $this->lang->line('home')['level']; ?></label>
                        <input name="level[]" type="text" value="<?php echo set_value('level[]', $lift_object->level); ?>" size="1" disabled class="input input-sm"/>
                    </div>
                    <div class="md:col-span-2">
                         <label for="speed" class="label"><?php echo $this->lang->line('lift')['length_speed_column']; ?></label>
                        <input name="speed[]" type="text" value="<?php echo set_value('speed[]', $lift_object->speed); ?>" size="2" class="input input-sm"/>
                        <?php echo $this->lang->line('lift')['speed_unit']; ?>
                    </div>
                    <div class="md:col-span-3">
                        <label for="name_english" class="label"><?php echo $this->lang->line('admin_page')['name_english']; ?></label>
                     <input name="name_english[]" type="text" value="<?php echo set_value('name_english[]', $lift_object->name_english); ?>" size="25" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-3">
                         <label for="name_french" class="label"><?php echo $this->lang->line('admin_page')['name_french']; ?></label>
                        <input name="name_french[]" type="text" value="<?php echo set_value('name_french[]', $lift_object->name_french); ?>" size="25"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-2">
                         <label for="capacity" class="label"><?php echo $this->lang->line('lift')['capacity_seats']; ?></label>
                        <input name="capacity[]" type="text" value="<?php echo set_value('capacity[]', $lift_object->capacity); ?>" size="1" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-3">
                        <label for="base_cost" class="label"><?php echo $this->lang->line('home')['base_cost']; ?></label>
                        <?php $base_cost_data = array(
                                'type'  => 'text',
                                'name'  => 'base_cost[]',
                                'id'    => 'base_cost',
                                'value' => set_value('base_cost[]', $lift_object->base_cost),
                                'size' => '12'
                            );
                            echo form_input($base_cost_data);
                        ?> €
                    </div>
                    <div class="md:col-span-3">
                         <label for="meter_cost" class="label"><?php echo $this->lang->line('home')['meter_cost']; ?></label>
                        <input name="meter_cost[]" type="text" value="<?php echo set_value('meter_cost[]', $lift_object->meter_cost); ?>" size="12"  class="input input-sm"/> €
                    </div>
                    <div class="md:col-span-3">
                         <label for="building_time" class="label"><?php echo $this->lang->line('home')['building_time']; ?></label>
                        <input name="building_time[]" type="text" value="<?php echo set_value('building_time[]', $lift_object->building_time); ?>" size="10"  class="input input-sm"/>
                        <?php echo $this->lang->line('home')['seconds']; ?>
                    </div>
                    <div class="md:col-span-2">
                         <label for="throughput" class="label"><?php echo $this->lang->line('lift')['throughput']; ?></label>
                        <input name="throughput[]" type="text" value="<?php echo set_value('throughput[]', $lift_object->throughput); ?>" size="6"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-2">
                        <label for="reputation" class="label"><?php echo $this->lang->line('home')['reputation']; ?></label>
                        <?php $reputation_data = array(
                                'type'  => 'text',
                                'name'  => 'reputation[]',
                                'id'    => 'reputation',
                                'value' => set_value('reputation[]', $lift_object->reputation),
                                'size' => '4'
                            );
                            echo form_input($reputation_data);
                        ?>
                    </div>
                    <div class="md:col-span-2">
                         <label for="daily_cost" class="label"><?php echo $this->lang->line('home')['daily_cost']; ?></label>
                        <input name="daily_cost[]" type="text" value="<?php echo set_value('daily_cost[]', $lift_object->daily_cost); ?>" size="6"  class="input input-sm"/> €
                    </div><br>
                </div>
                <?php } ?>
                 
                        <span class="text-error"><?php if (isset($edit_lift_error_daily_base_cost) && $edit_lift_error_daily_base_cost != '') echo $edit_lift_error_daily_base_cost.'<br>'; ?>
                        <span class="text-error"><?php if (isset($edit_lift_error_daily_meter_cost) && $edit_lift_error_daily_meter_cost != '') echo $edit_lift_error_daily_meter_cost.'<br>'; ?>
                        <?php if (isset($edit_lift_error_throughput) && $edit_lift_error_throughput != '') echo $edit_lift_error_throughput.'<br>'; ?>
                        <?php if (isset($edit_lift_error_speed) && $edit_lift_error_speed != '') echo $edit_lift_error_speed.'<br>'; ?>
                        <?php if (isset($edit_lift_error_capacity) && $edit_lift_error_capacity != '') echo $edit_lift_error_capacity.'<br>'; ?>
                        <?php if (isset($edit_lift_error_reputation) && $edit_lift_error_reputation != '') echo $edit_lift_error_reputation.'<br>'; ?>
                        <?php if (isset($edit_lift_error_building_cost) && $edit_lift_error_building_cost != '') echo $edit_lift_error_building_cost.'<br>'; ?>
                        <?php if (isset($edit_lift_error_building_time) && $edit_lift_error_building_time != '') echo $edit_lift_error_building_time.'<br>'; ?>
                        <?php if (isset($edit_lift_error_name_french) && $edit_lift_error_name_french != '') echo $edit_lift_error_name_french.'<br>'; ?>
                        <?php if (isset($edit_lift_error_name_english) && $edit_lift_error_name_english != '') echo $edit_lift_error_name_english.'<br>'; ?>
                        <?php if (isset($edit_lift_error_level) && $edit_lift_error_level != '') echo $edit_lift_error_level.'<br>'; ?>
                        <?php if (isset($edit_lift_error_type) && $edit_lift_error_type != '') echo $edit_lift_error_type.'<br>'; ?>
                        <?php if (isset($edit_lift_error_grip_type) && $edit_lift_error_grip_type != '') echo $edit_lift_error_grip_type.'<br>'; ?>
                        <?php if (isset($edit_lift_error_id_building) && $edit_lift_error_id_lift != '') echo $edit_lift_error_id_lift.'<br>'; ?>
                        <?php if (isset($edit_lift_error_id_group) && $edit_lift_error_id_group != '') echo $edit_lift_error_id_group.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_lift_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('edit_lift_admin', 'edit_lift_admin'); ?>
                    <?php echo form_hidden('id_group_posted', $id_group); ?>
                    <?php echo form_hidden('mode', $mode); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>