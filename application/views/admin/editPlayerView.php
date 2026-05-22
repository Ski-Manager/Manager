<div class="w-full">
<?php

echo '<legend>'.$this->lang->line('admin_page')['edit_player'].':</legend>';
?>
<!-- START resort table -->
    <div class="w-full container-border padding_top_bot_15">
        <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "edit_admin_player");
             echo form_open("admin/admin_player_controller/update_admin_player", $attributes);?>
             <fieldset>
             <legend><?php echo $this->lang->line('admin_page')['edit_player']; ?></legend>
           
             <?php echo $this->session->flashdata('msg');
             ?>
            <div class="form-group">
                <div class="md:col-span-4">
                    <label for="id_player" class="label"><?php echo $this->lang->line('admin_page')['id_player']; ?></label>
                 <input name="id_player" type="text" value="<?php echo set_value('id_player', $data_player_object->id_player); ?>" size="6" class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_player_error_id_player)) echo '<br>'.$edit_player_error_id_player ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="username" class="label"><?php echo $this->lang->line('home')['username']; ?></label>
                 <input name="username" type="text" value="<?php echo set_value('username', $data_player_object->username); ?>" size="25" class="input input-sm"/>
                    <span class="text-error"><?php if (isset($edit_player_error_username)) echo '<br>'.$edit_player_error_username ?></span>
                </div>
                 <div class="md:col-span-4">
                     <label for="email" class="label"><?php echo $this->lang->line('home')['email']; ?></label>
                    <input name="email" type="text" value="<?php echo set_value('email', $data_player_object->email); ?>" size="35" class="input input-sm" />
                    <span class="text-error"><?php if (isset($edit_player_error_email)) echo '<br>'.$edit_player_error_email ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="hash_password" class="label"><?php echo $this->lang->line('home')['hash_password']; ?></label>
                    <input name="hash_password" type="text" value="<?php echo set_value('hash_password', $data_player_object->password); ?>" size="42" class="input input-sm" />
                    <span class="text-error"><?php if (isset($edit_player_error_hash_password)) echo '<br>'.$edit_player_error_hash_password ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="country" class="label"><?php echo $this->lang->line('home')['country_field']; ?></label>
                    <input name="country" type="text" value="<?php echo set_value('country', $data_player_object->country); ?>" size="30" class="input input-sm" />
                    <span class="text-error"><?php if (isset($edit_player_error_country)) echo '<br>'.$edit_player_error_country ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="age" class="label"><?php echo $this->lang->line('signup')['age']; ?></label>
                    <input name="age" type="text" value="<?php echo set_value('age', $data_player_object->age); ?>" size="5" class="input input-sm" />
                    <span class="text-error"><?php if (isset($edit_player_error_age)) echo '<br>'.$edit_player_error_age ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="registration_time" class="label"><?php echo $this->lang->line('admin_page')['registration_time']; ?></label>
                    <input name="registration_time" type="text" value="<?php echo set_value('registration_time', gmdate("H:i:s d/m/Y",strtotime($data_player_object->registration_time))); ?>" size="20" disabled class="input input-sm"/>
                </div>
                <div class="md:col-span-4">
                    <label for="preferred_lang" class="label"><?php echo $this->lang->line('admin_page')['preferred_lang']; ?></label>
                    <?php 
                        $options = array(
                            'french'         => 'French',
                            'english'           => 'english',
                            ''           => ''
                        );

                        $lang_dropdown = array('small', 'large');
                        echo form_dropdown('preferred_lang', $options, $data_player_object->preferred_lang);
                        ?>
                    <span class="text-error"><?php if (isset($edit_player_error_preferred_lang)) echo '<br>'.$edit_player_error_preferred_lang ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="genepis" class="label"><?php echo $this->lang->line('navbar')['genepis']; ?></label>
                    <input name="genepis" type="text" value="<?php echo set_value('genepis', $data_player_object->genepis); ?>" size="5" class="input input-sm" />
                    <span class="text-error"><?php if (isset($edit_player_error_genepis)) echo '<br>'.$edit_player_error_genepis ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="activated" class="label"><?php echo $this->lang->line('admin_page')['activated']; ?>:</label>
                    <?php 
                    $activated_button = array(
                        'name'          => 'activated',
                        'id'            => 'activated',
                        'value'         => '1',
                        'checked'       => $data_player_object->activated == 1 ? TRUE : FALSE
                    );
                    $not_activated_button = array(
                        'name'          => 'activated',
                        'id'            => 'not_activated',
                        'value'         => '0',
                        'checked'       => $data_player_object->activated == 0 ? TRUE : FALSE
                    );
                    echo form_radio($activated_button);
                    echo '<label for="activated" class="label">'.$this->lang->line('slope')['building_status_yes'].'</label> ';
                    echo form_radio($not_activated_button);
                    echo '<label for="not_activated" class="label">'.$this->lang->line('slope')['building_status_no'].'</label>';
                    ?>
                    <span class="text-error"><?php if (isset($edit_player_error_activated)) echo '<br>'.$edit_player_error_activated ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="is_admin" class="label"><?php echo $this->lang->line('admin_page')['is_admin']; ?>:</label>
                    <?php 
                        $is_admin_button = array(
                            'name'          => 'is_admin',
                            'id'            => 'is_admin',
                            'value'         => '1',
                            'checked'       => $data_player_object->is_admin == 1 ? TRUE : FALSE
                        );
                        $is_not_admin_button = array(
                            'name'          => 'is_admin',
                            'id'            => 'is_not_admin',
                            'value'         => '0',
                            'checked'       => $data_player_object->is_admin == 0 ? TRUE : FALSE
                        );
                        echo form_radio($is_admin_button);
                        echo '<label for="is_admin" class="label">'.$this->lang->line('slope')['building_status_yes'].'</label> ';
                        echo form_radio($is_not_admin_button);
                        echo '<label for="is_not_admin" class="label">'.$this->lang->line('slope')['building_status_no'].'</label>';
                    ?>
                    <span class="text-error"><?php if (isset($edit_player_error_is_admin)) echo '<br>'.$edit_player_error_is_admin ?></span>
                </div>
                <div class="md:col-span-4">
                    <label for="last_connection" class="label"><?php echo $this->lang->line('admin_page')['last_connection']; ?></label>
                    <input name="last_connection" type="text" value="<?php echo set_value('last_connection', gmdate("H:i:s d/m/Y",$data_player_object->last_connection)); ?>" size="20" disabled class="input input-sm"/>
                </div>
                 <div class="md:col-span-6 padding_top_bot_15">
                     <?php echo form_hidden('edit_admin_player', 'edit_admin_player'); ?>
                     <?php echo form_hidden('original_id_player', $data_player_object->id_player); ?>
                     <?php echo '<a href="'.base_url('admin/admin_player_controller').'"><button class="btn btn-primary" name"back_button">Back</button></a>'; ?>
                 </div>
                <div class="md:col-span-6 align_right padding_top_bot_15">
                     <input name="submit" type="submit" class="btn btn-success" value="<?php echo $this->lang->line('home')['confirm']; ?>" />
                 </div>
             </div>
             </fieldset>
             <?php echo form_close(); ?>
             <div class="md:col-span-12 padding_top_bot_15">
                 <div class="alert alert-warning items-start">
                     <strong><img src="<?php echo base_url('img/icons/impersonate.png'); ?>" style="width:20px;height:20px;vertical-align:middle;" alt="" aria-hidden="true"/> <?php echo $this->lang->line('admin_page')['impersonate']; ?></strong>
                     <div style="margin-top:8px;">
                         <?php $imp_attrs = array('name' => 'edit_impersonate_password', 'id' => 'edit_impersonate_password', 'placeholder' => $this->lang->line('home')['password'], 'class' => 'input w-full', 'style' => 'display:inline-block;width:auto;');
                         echo form_password($imp_attrs); ?>
                         <button type="button" class="btn btn-warning impersonate_button_edit" data-id_player="<?php echo htmlspecialchars($data_player_object->id_player, ENT_QUOTES, 'UTF-8'); ?>" style="margin-left:8px;">
                             <img src="<?php echo base_url('img/icons/impersonate.png'); ?>" style="width:16px;height:16px;vertical-align:middle;" alt="" aria-hidden="true"/> <?php echo $this->lang->line('admin_page')['impersonate']; ?>
                         </button>
                     </div>
                 </div>
             </div>
            
            
         </div>
    </div>


</div>
