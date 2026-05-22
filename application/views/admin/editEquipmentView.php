<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_equipment_type'].': '.$equipment_type.'</legend>';
?>
<!-- START equipment table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "edit_equipment_type");
             echo form_open("admin/admin_equipment_controller/update_equipment_type", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
                 <div class="form-group group_form">
                    <div class="md:col-span-2 mb-3">
                        <label for="type" class="label"><?php echo $this->lang->line('admin_page')['type']; ?></label>
                        <select id="type" name="type" class="select select-sm w-full"> 
                            <?php echo $type_select; ?>
                        </select>
                    </div>
                </div>
                     
             <?php foreach ($equipment_type_array as $equipment_type_object) { ?>
                <div class="form-group group_form">
                    <div class="md:col-span-2">
                        <label for="id_equipment" class="label"><?php echo $this->lang->line('admin_page')['id_equipment']; ?></label>
                     <input name="id_equipment[]" type="text" value="<?php echo set_value('id_equipment[]', $equipment_type_object->id_equipment); ?>" size="3" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-2">
                         <label for="level" class="label"><?php echo $this->lang->line('home')['level']; ?></label>
                        <input name="level[]" type="text" value="<?php echo set_value('level[]', $equipment_type_object->level); ?>" size="1"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                        <label for="name_english" class="label"><?php echo $this->lang->line('admin_page')['name_english']; ?></label>
                     <input name="name_english[]" type="text" value="<?php echo set_value('name_english[]', $equipment_type_object->name_english); ?>" size="45" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="name_french" class="label"><?php echo $this->lang->line('admin_page')['name_french']; ?></label>
                        <input name="name_french[]" type="text" value="<?php echo set_value('name_french[]', $equipment_type_object->name_french); ?>" size="48"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="delivery_time" class="label"><?php echo $this->lang->line('home')['equipment_delivery_time']; ?></label>
                        <input name="delivery_time[]" type="text" value="<?php echo set_value('delivery_time[]', $equipment_type_object->delivery_time); ?>" size="10" class="input input-sm"/>
                        <?php echo $this->lang->line('home')['seconds']; ?>
                    </div>
                    <div class="md:col-span-4">
                         <label for="buying_cost" class="label"><?php echo $this->lang->line('common_equipment')['equipment_cost']; ?></label>
                        <?php $buying_cost_data = array(
                                'type'  => 'text',
                                'name'  => 'buying_cost[]',
                                'id'    => 'buying_cost',
                                'value' => set_value('buying_cost[]', $equipment_type_object->buying_cost),
                                'size' => '10'
                            );
                            echo form_input($buying_cost_data);
                        ?> €
                    </div>
                    <div class="md:col-span-4">
                         <label for="reputation" class="label"><?php echo $this->lang->line('home')['reputation']; ?></label>
                        <?php $reputation_data = array(
                                'type'  => 'text',
                                'name'  => 'reputation[]',
                                'id'    => 'reputation',
                                'value' => set_value('reputation[]', $equipment_type_object->reputation),
                                'size' => '6'
                            );
                            echo form_input($reputation_data);
                        ?>
                    </div>
                    <div class="md:col-span-4">
                         <label for="capacity" class="label"><?php echo $this->lang->line('home')['capacity']; ?></label>
                        <input name="capacity[]" type="text" value="<?php echo set_value('capacity[]', $equipment_type_object->capacity); ?>" size="6"  class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="max_income" class="label"><?php echo $this->lang->line('building')['max_income']; ?></label>
                        <input name="max_income[]" type="text" value="<?php echo set_value('max_income[]', $equipment_type_object->max_income); ?>" size="10"  class="input input-sm"/> €
                    </div>
                    <div class="md:col-span-4">
                         <label for="daily_cost" class="label"><?php echo $this->lang->line('home')['daily_cost']; ?></label>
                        <input name="daily_cost[]" type="text" value="<?php echo set_value('daily_cost[]', $equipment_type_object->daily_cost); ?>" size="10"  class="input input-sm"/> €
                    </div><br>
                </div>
                <?php } ?>
                 
                        <span class="text-error"><?php if (isset($edit_equipment_error_max_income) && $edit_equipment_error_max_income != '') echo $edit_equipment_error_max_income.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_daily_cost) && $edit_equipment_error_daily_cost != '') echo $edit_equipment_error_daily_cost.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_type) && $edit_equipment_error_type != '') echo $edit_equipment_error_type.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_max_income) && $edit_equipment_error_max_income != '') echo $edit_equipment_error_max_income.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_capacity) && $edit_equipment_error_capacity != '') echo $edit_equipment_error_capacity.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_reputation) && $edit_equipment_error_reputation != '') echo $edit_equipment_error_reputation.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_buying_cost) && $edit_equipment_error_buying_cost != '') echo $edit_equipment_error_buying_cost.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_delivery_time) && $edit_equipment_error_delivery_time != '') echo $edit_equipment_error_delivery_time.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_name_french) && $edit_equipment_error_name_french != '') echo $edit_equipment_error_name_french.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_name_english) && $edit_equipment_error_name_english != '') echo $edit_equipment_error_name_english.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_level) && $edit_equipment_error_level != '') echo $edit_equipment_error_level.'<br>'; ?>
                        <?php if (isset($edit_equipment_error_id_equipment) && $edit_equipment_error_id_equipment != '') echo $edit_equipment_error_id_equipment.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_equipment_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('edit_equipment_type', 'edit_equipment_type'); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>