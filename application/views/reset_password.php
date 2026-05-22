<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">
        
        
        <?php
        echo '<h2 class="h2">'.$title.'</h2>'; 
        echo '<p class="text-base-content/60 mb-2">'.$introResetPass.'</p>';
        echo '<p class="text-base-content/60 mb-3"><small>'.$this->lang->line('reset_password')['step_email_hint'].'</small></p>';
        ?>

    
         
       <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "reset_request");
             echo form_open("reset_password_controller/reset_request", $attributes);?>
             <fieldset>
           
             <?php echo $this->session->flashdata('msg'); ?>
          
             <div class="mb-3">
                 <div class="md:col-span-12">
                     <label for="email_or_username" class="label"><?php echo $this->lang->line('home')['email'].' '.$this->lang->line('home')['or'].' '.$this->lang->line('home')['username']; ?></label>
                 </div>
                 <div class="md:col-span-8">
                     <input name="email_or_username" placeholder="<?php echo $this->lang->line('contact_form')['email_or_username_field']; ?>" type="text" class="input w-full" value="<?php echo set_value('email_or_username_field'); ?>" />
                     <span class="text-error"><?php if (isset($contact_error_email)) echo '<br>'.$contact_error_email ?></span>
                 </div>
             </div>

             <div class="mb-3">
                 <div class="md:col-span-12">
                     <?php echo $captcha['label']; ?>
                     <?php echo $captcha['img']; ?>
                     <?php echo $captcha['input']; ?>
                     <?php echo $captcha['refresh']; ?>
                 </div>
             </div>
             <div class="mb-3">
                 <div class="md:col-span-12">
                     <input name="submit" type="submit" class="btn btn-primary" value="<?php echo $this->lang->line('contact_form')['request']; ?>" />
                 </div>
             </div>
             </fieldset>
             <?php echo form_hidden('reset_request', 'reset_request');
             echo form_close(); ?>
         </div>

    </div>

</div>  