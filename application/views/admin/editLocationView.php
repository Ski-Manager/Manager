<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_location_group'].': '.$id_group.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "update_location_admin");
             echo form_open("admin/admin_location_controller/update_location_admin", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
                 <div class="form-group group_form">
                    <div class="md:col-span-2">
                        <label for="id_group" class="label"><?php echo $this->lang->line('admin_page')['id_group']; ?></label>
                     <input name="id_group" class="id_group input input-sm" type="text" value="<?php echo set_value('id_group', $id_group); ?>" size="2" />
                    </div>
                    <div class="md:col-span-2">
                         <label for="length" class="label"><?php echo $this->lang->line('slope')['length']; ?></label>
                        <input name="length" type="text" value="<?php echo set_value('length', $length); ?>" size="3" class="input input-sm"/>
                        <?php echo $this->lang->line('slope')['length_unit']; ?>
                    </div>
                </div>
            <?php foreach ($location_array as $location_object) {?>
                <div class="form-group group_form">
                    <div class="md:col-span-2">
                         <label for="id_location" class="label"><?php echo $this->lang->line('admin_page')['id_location']; ?></label>
                        <input name="id_location[]" type="text" value="<?php echo set_value('id_location[]', $location_object->id_location); ?>" size="1" class="input input-sm"/>
                    </div>
                    <div class="col-md-1">
                         <label for="id_sector" class="label"><?php echo $this->lang->line('admin_page')['id_sector']; ?></label>
                        <input name="id_sector[]" type="text" value="<?php echo set_value('id_sector[]', $location_object->id_sector); ?>" size="1" class="input input-sm"/>
                    </div>
                    <div class="col-md-1">
                         <label for="x_coordinates" class="label"><?php echo $this->lang->line('admin_page')['x_coordinates']; ?></label>
                        <input name="x_coordinates[]" type="text" value="<?php echo set_value('x_coordinates[]', $location_object->x_coordinates); ?>" size="6" class="input input-sm"/>
                    </div>
                    <div class="col-md-1">
                        <label for="y_coordinates" class="label"><?php echo $this->lang->line('admin_page')['y_coordinates']; ?></label>
                     <input name="y_coordinates[]" type="text" value="<?php echo set_value('y_coordinates[]', $location_object->y_coordinates); ?>" size="6" class="input input-sm"/>
                    </div>
                    <div class="col-md-1">
                         <label for="area" class="label"><?php echo $this->lang->line('admin_page')['area']; ?></label>
                        <input name="area[]" type="text" value="<?php echo set_value('area[]', $location_object->area); ?>" size="1"  class="input input-sm"/>
                    </div><br>
                </div>
                <?php } ?>
                 
                 
                       <span class="text-error">
                        <?php if (isset($edit_location_error_area) && $edit_location_error_area != '') echo $edit_location_error_area.'<br>'; ?>
                        <?php if (isset($edit_location_error_length) && $edit_location_error_length != '') echo $edit_location_error_length.'<br>'; ?>
                        <?php if (isset($edit_location_error_y_coordinates) && $edit_location_error_y_coordinates != '') echo $edit_location_error_y_coordinates.'<br>'; ?>
                        <?php if (isset($edit_location_error_x_coordinates) && $edit_location_error_x_coordinates != '') echo $edit_location_error_x_coordinates.'<br>'; ?>
                        <?php if (isset($edit_location_error_id_sector) && $edit_location_error_id_sector != '') echo $edit_location_error_id_sector.'<br>'; ?>
                        <?php if (isset($edit_location_error_id_group) && $edit_location_error_id_group != '') echo $edit_location_error_id_group.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_location_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('edit_location_admin', 'edit_location_admin'); ?>
                    <?php echo form_hidden('id_group_posted', $id_group); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>

         </div>
    </div>


</div>