<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_staff'].': '.$staff_row->id_staff.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "update_staff_admin");
             echo form_open("admin/admin_staff_controller/update_staff_admin", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
                <div class="form-group group_form">   
                    <div class="col-md-1 mb-3">
                        <label for="id_staff" class="label"><?php echo $this->lang->line('admin_page')['id_staff']; ?></label>
                     <input name="id_staff" type="text" value="<?php echo set_value('id_staff', $staff_row->id_staff); ?>" size="1" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4 mb-3">
                        <label for="name_english" class="label"><?php echo $this->lang->line('admin_page')['name_english']; ?></label>
                     <input name="name_english" type="text" value="<?php echo set_value('name_english', $staff_row->name_english); ?>" size="35" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4 mb-3">
                         <label for="name_french" class="label"><?php echo $this->lang->line('admin_page')['name_french']; ?></label>
                        <input name="name_french" type="text" value="<?php echo set_value('name_french', $staff_row->name_french); ?>" size="35" class="input input-sm" />
                    </div>
                    <div class="md:col-span-3 mb-3">
                        <label for="position" class="label"><?php echo $this->lang->line('common_staff')['position']; ?></label>
                        <select id="position" name="position" class="select select-sm w-full"> 
                            <?php echo $select_position; ?>
                        </select>
                    </div>
                    <div class="md:col-span-6 mb-3">
                         <label for="efficiency" class="label"><?php echo $this->lang->line('common_staff')['efficiency']; ?></label>
                        <input name="efficiency" type="text" value="<?php echo set_value('efficiency', $staff_row->efficiency); ?>" size="2" class="input input-sm"/> %
                    </div>
                    <div class="md:col-span-6 mb-3">
                         <label for="salary" class="label"><?php echo $this->lang->line('common_staff')['salary']; ?></label>
                        <input name="salary" type="text" value="<?php echo set_value('salary', $staff_row->salary); ?>" size="6" class="input input-sm"/> €
                    </div><br>
                </div>
                 
                        <span class="text-error"><?php if (isset($edit_staff_error_id_staff) && $edit_staff_error_id_staff != '') echo $edit_staff_error_id_staff.'<br>'; ?>
                        <?php if (isset($edit_staff_error_name_english) && $edit_staff_error_name_english != '') echo $edit_staff_error_name_english.'<br>'; ?>
                        <?php if (isset($edit_staff_error_name_french) && $edit_staff_error_name_french != '') echo $edit_staff_error_name_french.'<br>'; ?>
                        <?php if (isset($edit_staff_error_position) && $edit_staff_error_position != '') echo $edit_staff_error_position.'<br>'; ?>
                        <?php if (isset($edit_staff_error_efficiency) && $edit_staff_error_efficiency != '') echo $edit_staff_error_efficiency.'<br>'; ?></span>
                        <?php if (isset($edit_staff_error_salary) && $edit_staff_error_salary != '') echo $edit_staff_error_salary.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_staff_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('edit_staff_admin', 'edit_staff_admin'); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>