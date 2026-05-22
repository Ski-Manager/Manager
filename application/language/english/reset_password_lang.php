<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['reset_password']['title']			= 'Forgotten password or username?';
$lang['reset_password']['intro']			= 'If you have forgotten your password or username, you can reset it by entering your email address or username in the field below';
$lang['reset_password']['step_email_hint']	= 'After submitting, you will receive an email with a reset link. The link is valid for 24 hours. If you don\'t see the email, check your spam folder.';

$lang['reset_password']['reset_subject'] = 'Reset your Ski-Manager password';
$lang['reset_password']['reset_heading'] = 'Reset Your Password';
$lang['reset_password']['reset_body']    = 'You have requested to reset your password on Ski-Manager.net. Click the button below to choose a new password.';
$lang['reset_password']['reset_cta']     = 'Reset My Password';
$lang['reset_password']['reset_note']    = 'This link is valid for 24 hours. If you did not request a password reset, you can safely ignore this email. For any problems, contact us at <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';
// Legacy
$lang['reset_password']['reset_body1'] = 'You have requested to reset your password on ski-manager.net. Please click on the link below in order to reset your password.<br><br>';
$lang['reset_password']['reset_body2'] = 'Click to Reset password</a><br><br>';
$lang['reset_password']['reset_body3'] = 'This link is valid 24 hours.<br>If you haven\'t requested to reset your password, you can ignore this message.<br><br>Please contact us at '.CONST_ADMIN_EMAIL.' if you encounted any problem.<br><br>Sincerely,<br>/Ski-Manager team.';
$lang['reset_password']['email_sent_username'] = 'An email containing further instructions has been sent to the email address associated to the provided username. Check your spam folder if you haven\'t received anything in a few minutes or contact us at '.CONST_ADMIN_EMAIL.' for any question.';
$lang['reset_password']['email_sent_email'] = 'An email containing further instructions has been sent to the provided email address. Check your spam folder if you haven\'t received anything in a few minutes or contact us at '.CONST_ADMIN_EMAIL.' for any question.';

$lang['valid_email_or_username']			= 'The username or email is not valid.';


// confirm page to choose new password
$lang['reset_password']['choose_password_title']			= 'Enter your new password below';
$lang['reset_password']['password_updated']			= 'Your password has been updated successfully. You may now login to the site with your new password.';
