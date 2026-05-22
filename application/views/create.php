<?php
// Pre-load language lines and errors for cleaner HTML
$lang = $this->lang->line('reset_password');
$lang_signup = $this->lang->line('signup');
$lang_home = $this->lang->line('home');
$flash_msg = $this->session->flashdata('msg');
$error_password = isset($signup_error_password) ? $signup_error_password : '';
$error_password_confirm = isset($signup_error_password_confirm) ? $signup_error_password_confirm : '';
?>

<div class="container mt-4 mb-4"> <?php // Use 'container' for centered content, or 'w-full' if full-width is needed ?>
    <div class="grid grid-cols-12 gap-3 justify-center"> <?php // Center the column ?>
        <div class="md:col-span-8 lg:col-span-6"> <?php // Define column width for medium/large screens ?>

            <?php // Display general flash messages above the form card ?>
            <?php if ($flash_msg): ?>
                <div class="alert alert-info" role="alert"> <?php // Use Bootstrap alert component ?>
                    <?= htmlspecialchars($flash_msg) ?>
                </div>
            <?php endif; ?>

            <div class="card"> <?php // Wrap form in a card for better visual structure ?>
                <div class="card-header">
                    <h4 class="card-title mb-0"><?= htmlspecialchars($lang['choose_password_title']) ?></h4>
                </div>
                <div class="card-body">

                    <?php
                    $attributes = [
                        "name"        => "choose_password",
                        "id"          => "choosePasswordForm" // Added ID for potential JS hooks
                        // Removed class="form-horizontal" as Bootstrap 5 handles layout differently
                    ];
                    echo form_open("reset_password_controller/choose_password", $attributes);
                    ?>

                        <?php // Password Field Group ?>
                        <div class="mb-3"> <?php // Margin bottom utility class ?>
                            <label for="password" class="label">
                                <?= htmlspecialchars($lang_signup['new_password']) ?>
                            </label>
                            <?php
                            $password_data = [
                                'name'        => 'password',
                                'id'          => 'password', // ID matches label 'for'
                                'placeholder' => htmlspecialchars($lang_signup['new_password']),
                                'class'       => 'input w-full' . ($error_password ? ' input-error' : ''), // Add DaisyUI input class and error state
                                'size'        => '25', // Size attribute is less relevant with Bootstrap controls
                                'required'    => true // Add HTML5 required attribute
                            ];
                            echo form_password($password_data);
                            ?>
                            <div id="passwordHelp" class="text-sm opacity-60">
                                <?= htmlspecialchars($lang_signup['info_password']) ?> <?php // Help text below input ?>
                            </div>
                            <?php if ($error_password): ?>
                                <div class="text-error text-sm mt-1">
                                    <?= $error_password; // Error message (assuming it's safe HTML or escaped in controller) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php // Password Confirmation Field Group ?>
                        <div class="mb-3">
                            <label for="password_confirm" class="label">
                                <?= htmlspecialchars($lang_signup['new_password_confirm']) ?>
                            </label>
                            <?php
                            $password_confirm_data = [
                                'name'        => 'password_confirm',
                                'id'          => 'password_confirm',
                                'placeholder' => htmlspecialchars($lang_signup['new_password_confirm']),
                                'class'       => 'input w-full' . ($error_password_confirm ? ' input-error' : ''),
                                'size'        => '25',
                                'required'    => true
                            ];
                            echo form_password($password_confirm_data);
                            ?>
                             <?php if ($error_password_confirm): ?>
                                <div class="text-error text-sm mt-1">
                                    <?= $error_password_confirm; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php // Hidden Fields ?>
                        <?= form_hidden('choose_password', 'choose_password') ?>
                        <?= form_hidden('reset_code', isset($reset_code) ? htmlspecialchars($reset_code) : '') ?>

                        <?php // Submit Button ?>
                        <div class="grid gap-2"> <?php // Optional: Makes button full-width ?>
                           <?php
                            $submit_data = [
                                'name'  => 'edit_account_submit',
                                'value' => $lang_home['confirm'],
                                'class' => 'btn btn-success btn-lg' // Made button larger
                            ];
                            echo form_submit($submit_data);
                           ?>
                        </div>

                    <?= form_close(); ?>
                </div> <?php // End card-body ?>
            </div> <?php // End card ?>

        </div> <?php // End col ?>
    </div> <?php // End row ?>
</div> <?php // End container ?>
