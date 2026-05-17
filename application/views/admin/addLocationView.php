<div class="w-full">
<?php

echo '<legend>'.$title.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "add_new_location");
             echo form_open("admin/admin_location_controller/add_new_location_validation", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');
            ?>
                <div class="form-group group_form">
                    <div class="md:col-span-2">
                        <label for="id_group" class="label"><?php echo $this->lang->line('admin_page')['id_group']; ?></label>
                     <input name="id_group" class="id_group input input-sm" type="text" value="<?php echo set_value('id_group', $max_id_group); ?>" size="2" />
                    </div>
                    <div class="md:col-span-2">
                         <label for="length" class="label"><?php echo $this->lang->line('slope')['length']; ?></label>
                        <input name="length" type="text" value="<?php echo set_value('length'); ?>" size="3" class="input input-sm"/>
                        <?php echo $this->lang->line('slope')['length_unit']; ?>
                    </div>
                    <div class="md:col-span-6">
                         <label for="coordinates" class="label"><?php echo $this->lang->line('admin_page')['coordinates']; ?></label>
                        <input name="coordinates" type="text" value="<?php echo set_value('coordinates'); ?>" size="40" class="input input-sm"/>
                    </div>
                </div>
            <?php for ($i=1; $i<=2; $i++) { ?>
                <div class="form-group group_form">
                    <div class="col-md-1">
                        <?php   if ($i == 1) {
                                    echo $this->lang->line('admin_page')['start'];
                                }
                                else {
                                    echo $this->lang->line('admin_page')['stop'];
                                }
                        ?>
                    </div>
                    <div class="md:col-span-3">
                        <label for="id_sector" class="label"><?php echo $this->lang->line('admin_page')['id_sector']; ?></label>
                        <input name="id_sector[]" type="text" value="<?php echo set_value('id_sector[]'); ?>" size="1" class="input input-sm"/>
                    </div>
                    <div class="col-md-1">
                         <label for="area" class="label"><?php echo $this->lang->line('admin_page')['area']; ?></label>
                        <input name="area[]" type="text" value="<?php echo set_value('area[]', $max_id_area[$i-1]); ?>" size="1" class="input input-sm" />
                    </div><br>
                </div>
                <?php } ?>
                 
                        <span class="text-error">
                        <?php if (isset($add_location_error_area) && $add_location_error_area != '') echo $add_location_error_area.'<br>'; ?>
                        <?php if (isset($add_location_error_length) && $add_location_error_length != '') echo $add_location_error_length.'<br>'; ?>
                        <?php if (isset($add_location_error_coordinates) && $add_location_error_coordinates != '') echo $add_location_error_coordinates.'<br>'; ?>
                        <?php if (isset($add_location_error_id_sector) && $add_location_error_id_sector != '') echo $add_location_error_id_sector.'<br>'; ?>
                        <?php if (isset($add_location_error_id_group) && $add_location_error_id_group != '') echo $add_location_error_id_group.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_location_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('add_new_location', 'add_new_location'); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>