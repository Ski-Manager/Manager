<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_news'].': '.$id_news.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "edit_news_admin");
             echo form_open("admin/admin_news_controller/edit_news_validation", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
                 <div class="form-group group_form">
                    <div class="md:col-span-2">
                        <label for="id_news" class="label"><?php echo $this->lang->line('admin_page')['id_lift']; ?></label>
                     <input name="id_news" class="id_group input input-sm" type="text" value="<?php echo set_value('id_news', $id_news); ?>" size="2" />
                    </div>
                   <div class="md:col-span-4">
                        <label for="title_english" class="label"><?php echo $this->lang->line('admin_page')['title_english']; ?></label>
                     <input name="title_english" type="text" value="<?php echo set_value('title_english', $title_english); ?>" size="35" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="title_french" class="label"><?php echo $this->lang->line('admin_page')['title_french']; ?></label>
                        <input name="title_french" type="text" value="<?php echo set_value('title_french', $title_french); ?>" size="35" class="input input-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="active" class="label"><?php echo $this->lang->line('admin_page')['active']; ?>:</label>
                        <?php 
                        $active_button = array(
                            'name'          => 'active',
                            'id'            => 'active',
                            'value'         => '1',
                            'checked'       => $active == 1 ? TRUE : FALSE
                        );
                        $not_active_button = array(
                            'name'          => 'active',
                            'id'            => 'not_active',
                            'value'         => '0',
                            'checked'       => $active == 0 ? TRUE : FALSE
                        );
                        echo form_radio($active_button);
                        echo '<label for="active" class="label">'.$this->lang->line('slope')['building_status_yes'].'</label> ';
                        echo form_radio($not_active_button);
                        echo '<label for="not_active" class="label">'.$this->lang->line('slope')['building_status_no'].'</label>';
                        ?>
                        <span class="text-error"><?php if (isset($edit_player_error_activated)) echo '<br>'.$edit_player_error_activated ?></span>
                    </div>
                    <div class="md:col-span-6">
                         <label for="content_english" class="label"><?php echo $this->lang->line('admin_page')['content_english']; ?></label>
                        <?php $data_content_english = array(
                                'type'  => 'textarea',
                                'name'  => 'content_english',
                                'id'    => 'content_english',
                                'value' => set_value('content_english', $content_english),
                                'cols' => '70',
                                'rows' => '10',
                                'class' => 'textarea w-full',
                            );
                            echo form_textarea($data_content_english);
                        ?>
                    </div>
                    <div class="md:col-span-6">
                         <label for="content_french" class="label"><?php echo $this->lang->line('admin_page')['content_french']; ?></label>
                        <?php $data_content_french = array(
                                'type'  => 'textarea',
                                'name'  => 'content_french',
                                'id'    => 'content_french',
                                'value' => set_value('content_french', $content_french),
                                'cols' => '70',
                                'rows' => '10',
                                'class' => 'textarea w-full',
                            );
                            echo form_textarea($data_content_french);
                        ?>
                    </div>
                    <br>
                </div>                
                 
                       <span class="text-error">
                        <?php if (isset($edit_news_error_area) && $edit_news_error_area != '') echo $edit_news_error_area.'<br>'; ?>
                        <?php if (isset($edit_news_error_length) && $edit_news_error_length != '') echo $edit_news_error_length.'<br>'; ?>
                        <?php if (isset($edit_news_error_y_coordinates) && $edit_news_error_y_coordinates != '') echo $edit_news_error_y_coordinates.'<br>'; ?>
                        <?php if (isset($edit_news_error_x_coordinates) && $edit_news_error_x_coordinates != '') echo $edit_news_error_x_coordinates.'<br>'; ?>
                        <?php if (isset($edit_news_error_id_sector) && $edit_news_error_id_sector != '') echo $edit_news_error_id_sector.'<br>'; ?>
                        <?php if (isset($edit_news_error_id_group) && $edit_news_error_id_group != '') echo $edit_news_error_id_group.'<br>'; ?></span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_news_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                    <?php echo form_hidden('original_id_news', $original_id_news); ?>
                    <?php echo form_hidden('edit_news_admin', 'edit_news_admin'); ?>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>