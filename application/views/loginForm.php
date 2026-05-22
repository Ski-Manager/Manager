<div id="login_form_wrapper">
    <?php
    echo '<div class="login-form-header">';
    echo '<div class="login-form-icon"><i class="fa-solid fa-mountain-sun"></i></div>';
    echo '</div>';
        echo '<h2 class="h2">'.$this->lang->line('login_form')['login_text'].'</h2>';
        $attributes = array('name' => 'login_form');
        echo form_open('login_controller/checkLogin', $attributes);

        // Username field with icon
        echo '<div class="mb-2">';
        echo '<label for="login_username" class="label">'.$this->lang->line('home')['username'].'</label>';
        echo '<div class="auth-input-group">';
        echo '<i class="fa-solid fa-user auth-input-icon" aria-hidden="true"></i>';
        echo form_input(array('name' => 'login_username', 'id' => 'login_username', 'class' => 'input input-sm w-full', 'placeholder' => $this->lang->line('home')['username'], 'autocomplete' => 'username'));
        echo '</div>';
        echo '</div>';
        if (isset($login_error_username))
                    echo $login_error_username; // Print only signup errors
        echo form_hidden('signin', 'signin');

        // Password field with icon
        echo '<div class="mb-2">';
        echo '<label for="login_password" class="label">'.$this->lang->line('home')['password'].'</label>';
        echo '<div class="auth-input-group">';
        echo '<i class="fa-solid fa-lock auth-input-icon" aria-hidden="true"></i>';
        echo form_password(array('name' => 'login_password', 'id' => 'login_password', 'placeholder' => $this->lang->line('home')['password'], 'class' => 'input input-sm w-full password', 'autocomplete' => 'current-password'));
        echo '</div>';
        echo '</div>';
        if (isset($login_error_password))
                    echo $login_error_password; // Print only signup errors
        echo $this->session->flashdata('error') ? '<div class="alert alert-error text-sm mb-2" data-no-auto-dismiss="1">' . $this->session->flashdata('error') . '</div>' : '';
        echo '<div class="mb-1 small text-center">'.anchor('reset_password_controller', $this->lang->line('login_form')['forgot_passowrd']).'</div>';
        echo '<div class="mb-2 text-center">'.form_submit('submit_login', $this->lang->line('login_form')['login_text'], "class='btn btn-primary btn-sm w-full auth-submit-btn'").'</div>';
        echo '<div class="auth-footer-links">'.anchor('register_controller', $this->lang->line('login_form')['login_create']).'</div>';
        echo form_close();

        if (isset($signin_errors)) echo $signin_errors; // Print only signin errors    
    ?>

    <?php if ($this->config->item('google_signin_enabled')): ?>
    <!-- Google Sign-In button -->
    <div class="auth-divider"><span><?= htmlspecialchars($this->lang->line('home')['or'] ?? 'or', ENT_QUOTES, 'UTF-8') ?></span></div>
    <div id="g_id_onload"
         data-client_id="<?php echo htmlspecialchars($this->config->item('google_client_id'), ENT_QUOTES, 'UTF-8'); ?>"
         data-callback="handleGoogleCredential"
         data-auto_prompt="false">
    </div>
    <div class="g_id_signin"
         data-type="standard"
         data-size="large"
         data-theme="outline"
         data-text="sign_in_with"
         data-shape="rectangular"
         data-logo_alignment="left">
    </div>
    <?php endif; ?>
    
</div><!--end of login form-->

