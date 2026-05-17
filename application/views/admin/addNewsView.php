<div class="w-full">
<?php

echo '<legend>'.$title.'</legend>';
?>
<!-- START building table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "add_new_news");
             echo form_open("admin/admin_news_controller/add_new_news_validation", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg');?>
                <div class="form-group group_form">
                    <div class="md:col-span-4">
                        <label for="title_english" class="label"><?php echo $this->lang->line('admin_page')['title_english']; ?></label>
                     <input name="title_english" type="text" value="<?php echo set_value('title_english'); ?>" size="35" class="input input-sm"/>
                    </div>
                    <div class="md:col-span-4">
                         <label for="title_french" class="label"><?php echo $this->lang->line('admin_page')['title_french']; ?></label>
                        <input name="title_french" type="text" value="<?php echo set_value('title_french'); ?>" size="35" class="input input-sm" />
                    </div>
                    <div class="md:col-span-4">
                        <label for="active" class="label"><?php echo $this->lang->line('admin_page')['active']; ?>:</label>
                        <?php 
                        $active_button = array(
                            'name'          => 'active',
                            'id'            => 'active',
                            'value'         => '1',
                            'checked'       => TRUE
                           // 'checked'       => $data_player_object->active == 1 ? TRUE : FALSE
                        );
                        $not_active_button = array(
                            'name'          => 'active',
                            'id'            => 'not_active',
                            'value'         => '0',
                            'checked'       => FALSE
                           //'checked'       => $data_player_object->active == 0 ? TRUE : FALSE
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
                                'value' => set_value('content_english'),
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
                                'value' => set_value('content_french'),
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
                        <?php if (isset($add_news_error_title_english) && $add_news_error_title_english != '') echo $add_news_error_title_english.'<br>'; ?>
                        <?php if (isset($add_news_error_title_french) && $add_news_error_title_french != '') echo $add_news_error_title_french.'<br>'; ?>
                        <?php if (isset($add_news_error_content_english) && $add_news_error_content_english != '') echo $add_news_error_content_english.'<br>'; ?>
                        <?php if (isset($add_news_error_content_french) && $add_news_error_content_french != '') echo $add_news_error_content_french.'<br>'; ?>
                        <?php if (isset($add_news_error_active) && $add_news_error_active != '') echo $add_news_error_active.'<br>'; ?>
                    </span>
                <div class="md:col-span-4 padding_top_bot_15">
                    <?php echo '<a href="'.base_url('admin/admin_news_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                </div>
                <div class="md:col-span-4 align_right padding_top_bot_15">
                    <?php echo form_hidden('add_new_news', 'add_new_news'); ?>
                    <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                </div>
             
             </fieldset>
             <?php echo form_close(); ?>
            
            
         </div>
    </div>


</div>