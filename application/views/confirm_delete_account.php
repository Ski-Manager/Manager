<div class="w-full">
    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">

        <?php
        echo '<h2 class="h2">'.$this->lang->line('signup')['confirm_delete_account_title'].'</h2>'; 
        ?>
        

          
       <div class="w-full  padding_top_bot_15">  

        <?php
            if (!isset($status) || ($status != 'delete_successful' && $status != 'invalid_code' && $status != 'code_expired')) {
                    echo form_open('reset_controller/proceed_delete_account');
                    echo '<div class="bold">'.$this->lang->line('signup')['confirm_delete_account_text'].'</div>';
                    $attributes_password = array('name' => 'password_confirm','id' => 'password_confirm', 'placeholder' => $this->lang->line('signup')['password_confirm'], 'class' => 'password', 'size' => '25');
                    echo $this->lang->line('signup')['password_confirm_delete'].': '.form_password($attributes_password).'<br>';   
                    echo form_hidden('confirm_delete', 'confirm_delete');
                    echo form_hidden('delete_code', $delete_code);
                    echo form_hidden('email', $email);
                    $attributes_submit = array('id' => 'confirm_delete_submit', 'class' => 'btn btn-error');
                    echo '<br>'.form_submit('confirm_delete_submit', $this->lang->line('signup')['confirm_delete_account'], $attributes_submit);
                    echo form_close();
            }
            ?>
         </div>
 <?php echo '<div class="md:col-span-12">'.$this->session->flashdata('msg').'</div>'; ?>
    </div>

</div>  