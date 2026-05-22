<div id="register_form">
    
    <?php 
    echo $this->session->flashdata('msg');
    if (isset($account_finalized)) {
        echo '<p class="alert alert-success text-center">';
        echo $this->lang->line('signup')['account_finalized'];
        echo '</p>';
    }
    else if (isset($account_merged)) {
        echo '<p class="alert alert-success text-center">';
        echo $this->lang->line('signup')['account_merged'];
        echo '</p>';
    }
    else if (isset($account_created)) {
        if (isset($not_activated)) {  ?>       <!-- To confirm that the account has been created -->
        <p class="alert alert-error text-center">
            <?php
            echo $resend_button_text;
         }
        else { ?>
             <p class="alert alert-success text-center">
                 <?php echo $this->lang->line('signup')['account_created'];
                 if (isset($referral_confirmed) && $referral_confirmed === true) {
                    echo '<br>'.$this->lang->line('signup')['referral_confirmed'];
                 }
        }  ?> 
             </p>  <?php
    }
    else if (isset($facebook_finalize) && $facebook_finalize === true) {    // there is no regular account with same email. Finalize facebook account
        ?>    
        <div class="w-full">
            <h1 class="h1 mb-3"><?php echo $this->lang->line('signup')['finalize_account'];?></h1>
            <?php echo '<div class="text-base-content/60 mb-3">'.$this->lang->line('signup')['finalize_account_info'].' ('.$facebook_email.')<br>';
            echo $this->lang->line('signup')['finalize_account_more_info'].'</div>';
            
            $attributes = array('id' => 'finalize_account');
            echo form_open('register_controller/finalize_account', $attributes);

            echo '<div class="mb-3">';
            echo '<label for="username_finalize" class="label">'.$this->lang->line('home')['username'].'</label>';
            echo '<div id="chars" class="hidden"></div>';
            $id_username = array('id' => 'username_finalize', 'name' => 'username', 'class' => 'input w-full');
            echo form_input('username', set_value('username', $this->lang->line('home')['username']), $id_username);
            echo '<p class="validator-hint">'.$this->lang->line('alpha_dash_space').'</p>';
            echo '<span class="errorTxt"></span>';
            if (isset($signup_error_username) && $signup_error_username != '')
                echo $signup_error_username;
            echo form_hidden('facebook_email', $facebook_email);
            echo '</div>';
            
            echo '<div class="mb-3">';
            echo '<label for="signup_referral_finalize" class="label">'.$this->lang->line('signup')['referral'].'</label>';
            $id_referral = array('id' => 'signup_referral_finalize', 'class' => 'input w-full');
            if (isset($posted_referral) && $posted_referral != '')
                echo form_input('signup_referral', set_value('signup_referral', $posted_referral), $id_referral);
            else
                echo form_input('signup_referral', set_value('signup_referral'), $id_referral);
            echo '<p class="validator-hint">'.$this->lang->line('signup')['info_referral'].'</p>';
            echo '</div>';
            
            echo '<div class="mb-3">';
            echo form_submit('submit_finalize_account', $this->lang->line('signup')['signup_finalize_account'], 'class="btn btn-success"');    
            echo '<span class="text-base-content/60 small ml-2">'.$this->lang->line('signup')['agree_newsletter'].'</span>'; 
            echo '</div>';
        echo form_close();
        echo '</div>';
    }
    else if (isset($facebook_merge) && $facebook_merge === true) {   // there is a regular account with same email. Ask if user wants to merge and ask for password
        ?>  
        <div class="w-full">
            <h1 class="h1 mb-3"><?php echo $this->lang->line('signup')['merge_account'];?></h1>
            <?php
            echo '<div class="text-base-content/60 mb-3">';
            echo $this->lang->line('signup')['merge_account_existing'].'<br>';
            echo $this->lang->line('signup')['merge_account_question'].'<br>';
            echo $this->lang->line('signup')['merge_account_password'].' ('.$facebook_email.')<br>';
            echo '</div>';
            
            $attributes = array('id' => 'merge_account');
            echo form_open('register_controller/merge_account', $attributes);

            echo '<div class="mb-3">';
            echo '<label for="password_merge" class="label">'.$this->lang->line('home')['password'].'</label>';
            echo '<div class="auth-input-group">';
            echo '<i class="fa-solid fa-lock auth-input-icon" aria-hidden="true"></i>';
            $id_password = array('class' => 'password input w-full', 'id' => 'password_merge', 'name' => 'password', 'placeholder' => $this->lang->line('home')['password']);
            echo form_password('password', '', $id_password);
            echo '</div>';
            echo '<p class="validator-hint">'.$this->lang->line('signup')['info_password'].'</p>';
            if (isset($signup_error_password) && $signup_error_password != '')
                echo '<br>'.$signup_error_password;
            echo form_hidden('facebook_email', $facebook_email);
            echo anchor('reset_password_controller', $this->lang->line('login_form')['forgot_passowrd']);
            echo '</div>';
            
            echo '<div class="mb-3">';
            echo form_submit('submit_merge_account', $this->lang->line('signup')['signup_merge_account'], 'class="btn btn-success"');   
            echo '</div>';
        echo form_close();
        echo '</div>';
    }
    else if (isset($facebook_existing) && $facebook_existing === true) {   // there is a regular account with same email. Ask if user wants to merge and ask for password
        ?>  
        <div class="w-full">
            <h1 class="h1 mb-3"><?php echo $this->lang->line('signup')['merge_account'];?></h1>
            <?php
            echo '<div class="mb-3">';
            echo $this->lang->line('signup')['fb_account_existing'].' ('.$facebook_email.').<br>';
            echo $this->lang->line('signup')['fb_account_existing2'].'<br>';
            echo '</div>';
            
            echo '<div>';
            echo anchor('reset_password_controller', $this->lang->line('login_form')['create_password']);                
            echo '</div>';
        echo '</div>';
    }
    else {?>    <!-- The account has not been created, display form -->
        <div class="w-full">
            <h1 class="h1 mb-3"><?php echo $this->lang->line('signup')['text'];?></h1>
            <?php

            $has_error_username         = isset($signup_error_username) && $signup_error_username != '';
            $has_error_email            = isset($signup_error_email) && $signup_error_email != '';
            $has_error_password         = isset($signup_error_password) && $signup_error_password != '';
            $has_error_password_confirm = isset($signup_error_password_confirm) && $signup_error_password_confirm != '';
            $has_error_country          = isset($signup_error_country) && $signup_error_country != '';
            $has_error_age              = isset($signup_error_age) && $signup_error_age != '';
            $has_error_referral         = isset($signup_error_signup_referral) && $signup_error_signup_referral != '';

            $attributes = array('id' => 'signup_form');
            echo form_open('register_controller/prepare_user_creation', $attributes);

            echo '<div class="grid gap-3">';

            // Username
            echo '<div class="md:col-span-8">';
            echo '<label for="username" class="label">'.$this->lang->line('home')['username'].'</label>';
            echo '<div class="auth-input-group">';
            echo '<i class="fa-solid fa-user auth-input-icon" aria-hidden="true"></i>';
            $id_username = array(
                'id'           => 'username',
                'class'        => 'input w-full validator' . ($has_error_username ? ' input-error' : ''),
                'placeholder'  => $this->lang->line('home')['username'],
                'autocomplete' => 'username',
                'required'     => true,
                'minlength'    => '3',
                'maxlength'    => '25',
            );
            echo form_input('username', set_value('username', ''), $id_username);
            echo '</div>';
            echo '<p class="validator-hint">'.$this->lang->line('alpha_dash_space').'</p>';
            if ($has_error_username) echo $signup_error_username;
            echo form_hidden('signup', 'signup');
            echo '</div>';

            // Email
            echo '<div class="md:col-span-8">';
            echo '<label for="email" class="label">'.$this->lang->line('home')['email'].'</label>';
            echo '<div class="auth-input-group">';
            echo '<i class="fa-solid fa-envelope auth-input-icon" aria-hidden="true"></i>';
            $id_email = array(
                'id'           => 'email',
                'type'         => 'email',
                'class'        => 'input w-full validator' . ($has_error_email ? ' input-error' : ''),
                'placeholder'  => $this->lang->line('home')['email'],
                'autocomplete' => 'email',
                'required'     => true,
                'maxlength'    => '45',
            );
            echo form_input('email', set_value('email', ''), $id_email);
            echo '</div>';
            echo '<p class="validator-hint">'.$this->lang->line('home')['email_invalid'].'</p>';
            if ($has_error_email) echo $signup_error_email;
            echo '</div>';

            // Password
            echo '<div class="md:col-span-8">';
            echo '<label for="password" class="label">'.$this->lang->line('home')['password'].'</label>';
            echo '<div class="auth-input-group">';
            echo '<i class="fa-solid fa-lock auth-input-icon" aria-hidden="true"></i>';
            $id_password = array(
                'class'        => 'input w-full validator password' . ($has_error_password ? ' input-error' : ''),
                'id'           => 'password',
                'placeholder'  => $this->lang->line('home')['password'],
                'autocomplete' => 'new-password',
                'required'     => true,
                'minlength'    => '4',
                'maxlength'    => '25',
            );
            echo form_password('password', '', $id_password);
            echo '</div>';
            echo '<p class="validator-hint">'.$this->lang->line('signup')['info_password'].'</p>';
            if ($has_error_password) echo $signup_error_password;
            echo '</div>';

            // Confirm password
            echo '<div class="md:col-span-8">';
            echo '<label for="password_confirm" class="label">'.$this->lang->line('signup')['password_confirm'].'</label>';
            echo '<div class="auth-input-group">';
            echo '<i class="fa-solid fa-lock auth-input-icon" aria-hidden="true"></i>';
            $id_confirm_password = array(
                'class'        => 'input w-full validator password_confirm' . ($has_error_password_confirm ? ' input-error' : ''),
                'id'           => 'password_confirm',
                'placeholder'  => $this->lang->line('signup')['password_confirm'],
                'autocomplete' => 'new-password',
                'required'     => true,
            );
            echo form_password('password_confirm', '', $id_confirm_password);
            echo '</div>';
            if ($has_error_password_confirm) echo $signup_error_password_confirm;
            echo '</div>';

            // Country – searchable picker (driaug/country-picker, vanilla JS adaptation)
            $preselected_country = set_value('country', '');
            echo '<div class="md:col-span-8">';
            echo '<label for="country-picker-btn" class="label">'.$this->lang->line('home')['country_field'].'</label>';
            echo '<div class="country-picker-wrapper" id="country-picker-container">';
            // Hidden input that is submitted with the form
            echo '<input type="hidden" name="country" id="country" value="'.htmlspecialchars($preselected_country, ENT_QUOTES, 'UTF-8').'">';
            // Trigger button
            echo '<button type="button" id="country-picker-btn"';
            echo ' class="country-picker-btn'.($has_error_country ? ' input-error' : '').'"';
            echo ' aria-haspopup="listbox" aria-expanded="false">';
            echo '<img id="country-picker-btn-flag" src="" alt="" class="country-picker-btn-flag" style="display:none;" aria-hidden="true">';
            echo '<i class="fa-solid fa-globe2 country-picker-btn-globe" id="country-picker-globe-icon" aria-hidden="true"></i>';
            echo '<span id="country-picker-btn-label" data-placeholder="'.htmlspecialchars($this->lang->line('home')['country_field'], ENT_QUOTES, 'UTF-8').'" class="country-picker-btn-text country-picker-placeholder">';
            echo htmlspecialchars($this->lang->line('home')['country_field'], ENT_QUOTES, 'UTF-8');
            echo '</span>';
            echo '<svg class="country-picker-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>';
            echo '</button>';
            // Dropdown panel (initially hidden via the `hidden` attribute)
            echo '<div id="country-picker-dropdown" class="country-picker-dropdown" hidden>';
            echo '<div class="country-picker-search-wrapper">';
            echo '<input type="text" id="country-picker-search" class="country-picker-search" placeholder="Search a country…" autocomplete="off">';
            echo '</div>';
            echo '<ul id="country-picker-list" class="country-picker-list" role="listbox" aria-label="'.htmlspecialchars($this->lang->line('home')['country_field'], ENT_QUOTES, 'UTF-8').'"></ul>';
            echo '</div>';
            echo '</div>';
            if ($has_error_country) echo $signup_error_country;
            echo '</div>';

            // Age
            echo '<div class="md:col-span-4">';
            echo '<label for="age" class="label">'.$this->lang->line('signup')['age'].'</label>';
            $id_age = array(
                'id'    => 'age',
                'class' => 'input w-full validator' . ($has_error_age ? ' input-error' : ''),
                'type'  => 'number',
                'min'   => '1',
                'max'   => '120',
            );
            echo form_input('age', set_value('age', ''), $id_age);
            if ($has_error_age) echo $signup_error_age;
            echo '</div>';

            // Referral
            echo '<div class="md:col-span-8">';
            echo '<label for="signup_referral" class="label">'.$this->lang->line('signup')['referral'].'</label>';
            $id_referral = array(
                'id'    => 'signup_referral',
                'class' => 'input w-full validator' . ($has_error_referral ? ' input-error' : ''),
            );
            if (isset($posted_referral) && $posted_referral != '')
                echo form_input('signup_referral', set_value('signup_referral', $posted_referral), $id_referral);
            else
                echo form_input('signup_referral', set_value('signup_referral'), $id_referral);
            echo '<p class="validator-hint">'.$this->lang->line('signup')['info_referral'].'</p>';
            if ($has_error_referral) echo $signup_error_signup_referral;
            echo '</div>';

            // Submit
            echo '<div class="col-span-12">';
            echo form_submit('submit_signup', $this->lang->line('signup')['signup_create'], 'class="btn btn-success"');
            echo '<span class="text-base-content/60 small ml-2">'.$this->lang->line('signup')['agree_newsletter'].'</span>';
            echo '</div>';

            echo '</div>'; // end .row

        echo form_close();
        echo '</div>';
    }
    ?>
</div><!--end of register form-->