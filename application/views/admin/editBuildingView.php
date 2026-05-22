<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_building_type'].': '.$building_type.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "edit_building_type");
             echo form_open("admin/admin_building_controller/update_building_type", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
                 <div class="form-group group_form">
                    <div class="md:col-span-2">
                        <label for="type" class="label"><?php echo $this->lang->line('admin_page')['type']; ?></label>
                     <input name="type" type="text" value="<?php echo set_value($building_type, $building_type); ?>" size="15" class="input input-sm"/>
                    </div>
                </div>
                     
             <?php foreach ($building_type_array as $building_type_object) { ?>
                <div class="form-group group_form">
                    <div class="md:col-span-2">
                        <label for="id_building" class="label"><?php echo $this->lang->line('admin_page')['id_building']; ?></label>
                     <input name="id_building[]" type="text" value="<?php echo set_value('id_building[]', $building_type_object->id_building); ?>" size="3" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-2">
                         <label for="level" class="label"><?php echo $this->lang->line('home')['level']; ?></label>
                        <input name="level[]" type="text" value="<?php echo set_value('level[]', $building_type_object->level); ?>" size="1"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                        <label for="name_english" class="label"><?php echo $this->lang->line('admin_page')['name_english']; ?></label>
                     <input name="name_english[]" type="text" value="<?php echo set_value('name_english[]', $building_type_object->name_english); ?>" size="45" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="name_french" class="label"><?php echo $this->lang->line('admin_page')['name_french']; ?></label>
                        <input name="name_french[]" type="text" value="<?php echo set_value('name_french[]', $building_type_object->name_french); ?>" size="48"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="building_time" class="label"><?php echo $this->lang->line('home')['building_time']; ?></label>
                        <input name="building_time[]" type="text" value="<?php echo set_value('building_time[]', $building_type_object->building_time); ?>" size="12"  class="input input-sm"/>
                        <?php echo $this->lang->line('home')['seconds'];?>
                    </div>
                    <div class="md:col-span-4">
                         <label for="building_cost" class="label"><?php echo $this->lang->line('home')['cost']; ?></label>
                        <?php $building_cost_data = array(
                                'type'  => 'text',
                                'name'  => 'building_cost[]',
                                'id'    => 'building_cost',
                                'value' => set_value('building_cost[]', $building_type_object->building_cost),
                                'size' => '10'
                            );
                            echo form_input($building_cost_data);
                        ?> €
                    </div>
                    <div class="md:col-span-4">
                         <label for="reputation" class="label"><?php echo $this->lang->line('home')['reputation']; ?></label>
                        <?php $reputation_data = array(
                                'type'  => 'text',
                                'name'  => 'reputation[]',
                                'id'    => 'reputation',
                                'value' => set_value('reputation[]', $building_type_object->reputation),
                                'size' => '6'
                            );
                            echo form_input($reputation_data);
                        ?>
                    </div>
                    <div class="md:col-span-4">
                         <label for="capacity" class="label"><?php echo $this->lang->line('home')['capacity']; ?></label>
                        <input name="capacity[]" type="text" value="<?php echo set_value('capacity[]', $building_type_object->capacity); ?>" size="6"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="max_income" class="label"><?php echo $this->lang->line('building')['max_income']; ?></label>
                        <input name="max_income[]" type="text" value="<?php echo set_value('max_income[]', $building_type_object->max_income); ?>" size="10"  class="input input-sm"/> €
                    </div>
                    <div class="md:col-span-4">
                         <label for="daily_cost" class="label"><?php echo $this->lang->line('home')['daily_cost']; ?></label>
                        <input name="daily_cost[]" type="text" value="<?php echo set_value('daily_cost[]', $building_type_object->daily_cost); ?>" size="10"  class="input input-sm"/> €
                    </div><br>
                </div>
                <?php } ?>
                 
                        <span class="text-error"><?php if (isset($edit_building_type_error_daily_cost) && $edit_building_type_error_daily_cost != '') echo $edit_building_type_error_daily_cost.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_type) && $edit_building_type_error_type != '') echo $edit_building_type_error_type.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_max_income) && $edit_building_type_error_max_income != '') echo $edit_building_type_error_max_income.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_capacity) && $edit_building_type_error_capacity != '') echo $edit_building_type_error_capacity.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_reputation) && $edit_building_type_error_reputation != '') echo $edit_building_type_error_reputation.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_building_cost) && $edit_building_type_error_building_cost != '') echo $edit_building_type_error_building_cost.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_building_time) && $edit_building_type_error_building_time != '') echo $edit_building_type_error_building_time.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_name_french) && $edit_building_type_error_name_french != '') echo $edit_building_type_error_name_french.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_name_english) && $edit_building_type_error_name_english != '') echo $edit_building_type_error_name_english.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_level) && $edit_building_type_error_level != '') echo $edit_building_type_error_level.'<br>'; ?>
                        <?php if (isset($edit_building_type_error_id_building) && $edit_building_type_error_id_building != '') echo $edit_building_type_error_id_building.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_building_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('edit_building_type', 'edit_building_type'); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>