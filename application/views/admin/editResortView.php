<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_resort'].':</legend>';
?>
<!-- START resort table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "edit_admin_resort");
             echo form_open("admin/admin_resort_controller/update_admin_resort", $attributes);?>
             <fieldset>
             <legend><?php echo $this->lang->line('admin_page')['edit_resort']; ?></legend>
           
             <?php echo $this->session->flashdata('msg');
             ?>
            <div class="form-group">
                <div class="md:col-span-4">
                    <label for="id_resort" class="label"><?php echo $this->lang->line('admin_page')['id_resort']; ?></label>
                 <input name="id_resort" type="text" value="<?php echo set_value('id_resort', $data_resort_object->id_resort); ?>" size="6" class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_error_id_resort)) echo '<br>'.$edit_resort_error_id_resort ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="resort_name" class="label"><?php echo $this->lang->line('home')['resort_name']; ?></label>
                 <input name="resort_name" type="text" value="<?php echo set_value('resort_name', $data_resort_object->resort_name); ?>" size="35" class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_error_resort_name)) echo '<br>'.$edit_resort_error_resort_name ?></span>
                </div>
                 <div class="md:col-span-4">
                     <label for="resort_country" class="label"><?php echo $this->lang->line('home')['country_field']; ?></label>
                    <input name="resort_country" type="text" value="<?php echo set_value('resort_country', $data_resort_object->resort_country); ?>" size="35"  class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_error_resort_country)) echo '<br>'.$edit_resort_error_resort_country ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="resort_description" class="label"><?php echo $this->lang->line('resort')['description_field_error']; ?></label>
                    <?php 
                    $data = array(
                        'name'        => 'resort_description',
                        'id'          => 'resort_description',
                        'value'       => set_value('resort_description', $data_resort_object->resort_description),
                        'rows'        => '6',
                        'class' => 'textarea w-full',
                        'cols'        => '50'
                    );
                    echo form_textarea($data);
                    ?>
                    <span class="text-error"><?php if (isset($edit_resort_error_hash_resort_description)) echo '<br>'.$edit_resort_error_hash_resort_description ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="cash" class="label"><?php echo $this->lang->line('admin_page')['Cash']; ?></label>
                    <input name="cash" type="text" value="<?php echo set_value('cash', $data_resort_object->cash); ?>" size="10"  class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_error_cash)) echo '<br>'.$edit_resort_error_cash ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="snow_level" class="label"><?php echo $this->lang->line('weather')['snow_level']; ?></label>
                    <input name="snow_level" type="text" value="<?php echo set_value('snow_level', $data_resort_object->snow_level); ?>" size="4"  class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_error_snow_level)) echo '<br>'.$edit_resort_error_snow_level ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="reputation" class="label"><?php echo $this->lang->line('home')['reputation']; ?></label>
                    <input name="reputation" type="text" value="<?php echo set_value('reputation', $data_resort_object->reputation); ?>" size="12"  class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_error_reputation)) echo '<br>'.$edit_resort_error_reputation ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="creation_time_resort" class="label"><?php echo $this->lang->line('admin_page')['creation_time_resort']; ?></label>
                    <input name="creation_time_resort" type="text" value="<?php echo set_value('creation_time_resort', gmdate("H:i:s d/m/Y",strtotime($data_resort_object->creation_time_resort))); ?>" size="20" disabled class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_resort_last_creation_time_resort)) echo '<br>'.$edit_resort_error_last_creation_time_resort ?></span>
                </div>
                <div class="md:col-span-4 mb-3">
                    <label for="skipass_daily" class="label"><?php echo $this->lang->line('home')['skipass_daily']; ?></label>
                    <select id="skipass_daily" name="skipass_daily" class="select select-sm w-full"> 
                        <?php echo $skipass_data['selectArrayOneDay']; ?>
                    </select>
                    <span class="text-error"><?php if (isset($edit_resort_error_skipass_daily)) echo '<br>'.$edit_resort_error_skipass_daily ?></span>
                </div>
                <div class="md:col-span-4 mb-3">
                    <label for="skipass_weekly" class="label"><?php echo $this->lang->line('home')['skipass_weekly']; ?></label>
                    <select id="skipass_weekly" name="skipass_weekly" class="select select-sm w-full">
                        <?php echo $skipass_data['selectArrayOneWeek']; ?>
                    </select>
                    <span class="text-error"><?php if (isset($edit_resort_error_skipass_weekly)) echo '<br>'.$edit_resort_error_skipass_weekly ?></span>
                </div>
                 <div class="md:col-span-4 padding_top_bot_15">
                     <?php echo form_hidden('edit_admin_resort', 'edit_admin_resort'); ?>
                     <?php echo form_hidden('original_id_resort', $data_resort_object->id_resort); ?>
                     <?php echo '<a href="'.base_url('admin/admin_resort_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                 </div>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                     <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                 </div>
             </div>
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>