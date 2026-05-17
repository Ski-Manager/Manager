<div class="w-full">    <?php    // General page title    echo $title;    echo $intro_update_account;    ?>        <?php echo $this->session->flashdata('msg'); ?>    <div class="card bg-base-100 shadow-sm "><div class="card-body mb-3">        <?php        // Info Messages for specific actions        if (isset($action)) {            echo '<p>' . $this->lang->line('signup')[$action] . '</p>';        }        ?>                <div class="md:col-span-4">            <?php echo form_open('account_controller/update_account'); ?>                        <div class="md:col-span-12">                <?php                echo $this->lang->line('home')['username'] . ': ' . form_input('username', set_value('username', $username), 'size="25" disabled');                echo form_hidden('update_account', 'update_account');                ?>            </div>            <br><br>                        <div class="md:col-span-12">                <?php                echo $this->lang->line('home')['email'] . ': ' . form_input('email', set_value('email', $email), 'size="25"');                if (isset($signup_error_email) && $signup_error_email != '') {                    echo '<br>' . $signup_error_email;                }                ?>            </div>            <br><br>            <div class="md:col-span-12">                <?php                echo $this->lang->line('signup')['new_password'] . ': ' . form_password('password', '', 'placeholder="'.$this->lang->line('signup')['new_password'].'" class="password" size="25"');                if (isset($signup_error_password) && $signup_error_password != '') {                    echo '<br>' . $signup_error_password;                }                ?>            </div>            <br><br>            <div class="md:col-span-12">                <?php                echo $this->lang->line('signup')['new_password_confirm'] . ': ' . form_password('password_confirm', '', 'placeholder="'.$this->lang->line('signup')['new_password_confirm'].'" class="password_confirm" size="25"');                if (isset($signup_error_password_confirm) && $signup_error_password_confirm != '') {                    echo '<br>' . $signup_error_password_confirm;                }                ?>            </div>            <br><br>            <div class="md:col-span-12">                <?php                echo $this->lang->line('home')['country_field'] . ': ' . form_input('country', set_value('country', $country), 'size="15"');                if (isset($signup_error_country) && $signup_error_country != '') {                    echo '<br>' . $signup_error_country;                }                ?>            </div>            <br><br>            <div class="md:col-span-12">                <?php                echo $this->lang->line('signup')['age'] . ': ' . form_input('age', set_value('age', $age), 'size="5"');                if (isset($signup_error_age) && $signup_error_age != '') {                    echo '<br>' . $signup_error_age;                }                ?>            </div>            <br><br>            <div class="md:col-span-12">                <?php                echo form_submit('edit_account_submit', $this->lang->line('signup')['edit_account_submit'], 'class="btn btn-success"');                echo form_close();                ?>            </div>        </div>        <?php if (isset($account_activated) && !$account_activated): ?>        <div class="md:col-span-8">            <div class="alert alert-warning">                <?php echo $this->lang->line('signup')['resend_verification_email_text']; ?>            </div>            <?php echo form_open('account_controller/resend_verification_email'); ?>            <?php echo form_hidden('resend_verification', 'resend_verification'); ?>            <?php echo form_submit('resend_verification_submit', $this->lang->line('signup')['resend_verification_email'], 'class="btn btn-warning"'); ?>            <?php echo form_close(); ?>        </div>        <br><br>        <?php endif; ?>        <div class="md:col-span-8">            <?php echo form_open('account_controller/send_email_reset_account'); ?>            <?php echo form_hidden('reset_account', 'reset_account'); ?>            <?php echo form_submit('reset_account_submit', $this->lang->line('signup')['reset_account'], 'class="btn btn-error"'); ?>            <?php echo ' ' . $this->lang->line('signup')['reset_account_text']; ?>            <?php echo form_close(); ?>        </div>        <br><br>        <div class="md:col-span-8">            <?php echo form_open('account_controller/send_email_delete_account'); ?>            <?php echo form_hidden('delete_account', 'delete_account'); ?>            <?php echo form_submit('delete_account_submit', $this->lang->line('signup')['delete_account'], 'class="btn btn-error"'); ?>            <?php echo ' ' . $this->lang->line('signup')['delete_account_text']; ?>            <?php echo form_close(); ?>        </div>        <br><br>        <div class="md:col-span-8">            <?php if (isset($user_has_linked_google) && $user_has_linked_google): ?>                <p><?php echo $this->lang->line('signup')['linked_google_account']; ?></p>            <?php else: ?>                <p><?php echo $this->lang->line('signup')['not_linked_google']; ?></p>                <?php if ($this->config->item('google_signin_enabled')): ?>                <div id="g_id_onload"                     data-client_id="<?php echo htmlspecialchars($this->config->item('google_client_id'), ENT_QUOTES, 'UTF-8'); ?>"                     data-callback="handleGoogleLinkCredential"                     data-auto_prompt="false"></div>                <div class="g_id_signin"                     data-type="standard"                     data-size="large"                     data-theme="outline"                     data-text="signin_with"                     data-shape="rectangular"                     data-logo_alignment="left"></div>                <script>                function handleGoogleLinkCredential(response) {                    var form = document.createElement('form');                    form.method = 'POST';                    form.action = Settings.base_url + 'account_controller/link_google_account';                    var input = document.createElement('input');                    input.type = 'hidden';                    input.name = 'id_token';                    input.value = response.credential;                    form.appendChild(input);                    document.body.appendChild(form);                    form.submit();                }                </script>                <?php endif; ?>            <?php endif; ?>        </div>    </div>

<!-- Difficulty Mode -->
<div class="card bg-base-100 shadow mb-4">
    <div class="card-body">
        <h2 class="card-title"><i class="fa-solid fa-sliders mr-2"></i>Game Difficulty</h2>
        <p class="text-base-content/70 text-sm mb-3">
            <strong>Easy Mode</strong> simplifies the interface by hiding advanced features and makes the game more forgiving — no crisis events, reduced reputation penalties, and double starting cash for new resorts.<br>
            <strong>Standard Mode</strong> is the full experience with all features enabled.
        </p>
        <?php echo form_open('account_controller/save_difficulty_mode'); ?>
            <div class="flex gap-3">
                <button type="submit" name="difficulty_mode" value="standard"
                    class="btn <?= (!isset($difficulty_mode) || $difficulty_mode == 0) ? 'btn-primary' : 'btn-outline' ?>">
                    <i class="fa-solid fa-mountain mr-1"></i> Standard
                </button>
                <button type="submit" name="difficulty_mode" value="easy"
                    class="btn <?= (isset($difficulty_mode) && $difficulty_mode == 1) ? 'btn-success' : 'btn-outline' ?>">
                    <i class="fa-solid fa-star mr-1"></i> Easy
                </button>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

    <style>
/* General Page Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    font-size: 16px;
}

/* Container Styling */
.w-full {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Form Input Fields */
input[type="text"],
input[type="email"],
input[type="password"],
input[type="number"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input[type="text"]:hover,
input[type="email"]:hover,
input[type="password"]:hover,
input[type="number"]:hover {
    border-color: #17a2b8;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
input[type="number"]:focus {
    outline: none;
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}

/* Buttons */
.btn {
    padding: 10px 15px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s, transform 0.1s;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-error {
    background-color: #dc3545;
    color: white;
}

.btn-error:hover {
    background-color: #c82333;
}

.btn:active {
    transform: scale(0.98);
}

/* Error Message Styling */
.error {
    color: #c0392b;
    font-size: 14px;
}

/* Success Message */
.success {
    color: #1e7e34;
    font-size: 14px;
}

/* Container Border */
.container-border {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    border-radius: 10px;
    background-color: #f8f9fa;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 600px) {
    .w-full {
        width: 100%;
        max-width: 100%;
        padding: 20px;
    }

    .container-border {
        width: 100%;
    }
}

@media (min-width: 768px) {
    .md:col-span-4 {
        width: 40%;
    }
    .md:col-span-8 {
        width: 55%;
    }
}

/* ── Dark mode overrides ─────────────────────────────────────────────────── */

[data-theme="dark"] body {
    background-color: #1a1d23;
    color: #e8eaf0;
}

[data-theme="dark"] .w-full {
    background: #252932;
    color: #e8eaf0;
}

[data-theme="dark"] .container-border {
    background-color: #252932;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.4);
}

[data-theme="dark"] input[type="text"],
[data-theme="dark"] input[type="email"],
[data-theme="dark"] input[type="password"],
[data-theme="dark"] input[type="number"] {
    background-color: #2e3340;
    border-color: rgba(255, 255, 255, 0.15);
    color: #e8eaf0;
}

[data-theme="dark"] input[type="text"]:hover,
[data-theme="dark"] input[type="email"]:hover,
[data-theme="dark"] input[type="password"]:hover,
[data-theme="dark"] input[type="number"]:hover {
    border-color: #17a2b8;
}

[data-theme="dark"] input[type="text"]:focus,
[data-theme="dark"] input[type="email"]:focus,
[data-theme="dark"] input[type="password"]:focus,
[data-theme="dark"] input[type="number"]:focus {
    background-color: #2e3340;
    border-color: #17a2b8;
    color: #e8eaf0;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}

[data-theme="dark"] .error {
    color: #d9a3a3;
}

[data-theme="dark"] .success {
    color: #a3d9b1;
}

</style></div>
