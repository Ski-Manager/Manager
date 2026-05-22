<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <?php
        echo '<div class="md:col-span-12 padding_top_bot_15">'.$this->lang->line('reset_password')['choose_password_title'].'</div>';
        ?>

           <?php echo '<div class="md:col-span-12 padding_top_bot_15">'.$this->session->flashdata('msg').'</div>'; ?>
       <div class="w-full  padding_top_bot_15">  
             <?php $attributes = array("class" => "", "name" => "choose_password");
             echo form_open("reset_password_controller/choose_password", $attributes);
             echo '<div class="md:col-span-12">';
            echo form_password('password', '', 'placeholder="'.$this->lang->line('signup')['new_password'].'" class="password" size="25"');
                echo '<span class="light_info_text">'.$this->lang->line('signup')['info_password'].'</span><br>';
            if (isset($signup_error_password) && $signup_error_password != '')
                echo $signup_error_password; // Print only signup errors
            echo '</div>';
            echo '<div class="md:col-span-12">';
            echo form_password('password_confirm', '', 'placeholder="'.$this->lang->line('signup')['new_password_confirm'].'" class="password_confirm" size="25"');
            if (isset($signup_error_password_confirm) && $signup_error_password_confirm != '')
                echo $signup_error_password_confirm; // Print only signup errors
            echo '<br><br></div>';
            echo '<div class="md:col-span-12">';
            echo form_submit('edit_account_submit', $this->lang->line('home')['confirm'], 'class="btn btn-success"');   
            echo form_hidden('choose_password', 'choose_password');
            echo form_hidden('reset_code', $reset_code);
            echo '</div>';
             echo form_close(); ?>
         </div>

    </div>

</div>  