<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// SYSTEM MESSAGES
$lang['email_must_be_array'] = 'The email validation method must be passed an array.';
$lang['email_invalid_address'] = 'Invalid email address: %s';
$lang['email_attachment_missing'] = 'Unable to locate the following email attachment: %s';
$lang['email_attachment_unreadable'] = 'Unable to open this attachment: %s';
$lang['email_no_from'] = 'Cannot send mail with no "From" header.';
$lang['email_no_recipients'] = 'You must include recipients: To, Cc, or Bcc';
$lang['email_send_failure_phpmail'] = 'Unable to send email using PHP mail(). Your server might not be configured to send mail using this method.';
$lang['email_send_failure_sendmail'] = 'Unable to send email using PHP Sendmail. Your server might not be configured to send mail using this method.';
$lang['email_send_failure_smtp'] = 'Unable to send email using PHP SMTP. Your server might not be configured to send mail using this method.';
$lang['email_sent'] = 'Your message has been successfully sent using the following protocol: %s';
$lang['email_no_socket'] = 'Unable to open a socket to Sendmail. Please check settings.';
$lang['email_no_hostname'] = 'You did not specify a SMTP hostname.';
$lang['email_smtp_error'] = 'The following SMTP error was encountered: %s';
$lang['email_no_smtp_unpw'] = 'Error: You must assign a SMTP username and password.';
$lang['email_failed_smtp_login'] = 'Failed to send AUTH LOGIN command. Error: %s';
$lang['email_smtp_auth_un'] = 'Failed to authenticate username. Error: %s';
$lang['email_smtp_auth_pw'] = 'Failed to authenticate password. Error: %s';
$lang['email_smtp_data_failure'] = 'Unable to send data: %s';
$lang['email_exit_status'] = 'Exit status code: %s';

// CUSTOM MESSAGES
// EMAIL VALIDATION AFTER SIGNUP
$lang['email']['activation_subject']         = 'Please activate your account at Ski-Manager';
$lang['email']['activation_heading']         = 'Activate Your Account';
$lang['email']['activation_body']            = 'You have successfully registered at Ski-Manager. Click the button below to verify your email address and start managing your resort!';
$lang['email']['activation_cta']             = 'Activate My Account';
$lang['email']['activation_note']            = 'If you did not create this account, you can safely ignore this email. For any questions, contact us at <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';

$lang['email']['beta_activation_subject']    = 'Please activate your account at Ski-Manager Beta';
$lang['email']['beta_activation_heading']    = 'Welcome to the Ski-Manager Beta!';
$lang['email']['beta_activation_body']       = 'You have successfully registered for the Beta version of Ski-Manager. Click the button below to activate your account.<br><br>Please note: all progress made during the beta period will be erased at the end of the beta test. Beta-testers will be notified when beta testing is over.';
$lang['email']['beta_activation_cta']        = 'Activate Beta Account';

// Legacy HTML fragment keys – kept so that any remaining references do not break.
$lang['email']['activation_start_tags'] = '<!DOCTYPE html><html><head><meta charset="utf-8"/><title>Ski-Manager</title></head><body style="font-family:Arial,sans-serif;">';
$lang['email']['activation_dear']       = '<p>Dear ';
$lang['email']['activation_br']         = ',</p>';
$lang['email']['activation_body1']      = '<p>You have successfully registered at Ski-Manager, please <strong>';
$lang['email']['activation_body2']      = 'click here</a></strong> to activate your account.</p>';
$lang['email']['activation_end_tags']   = '</body></html>';

// RESET ACCOUNT EMAIL
$lang['email']['reset_account_subject'] = 'Reset account on Ski-Manager.net';
$lang['email']['reset_account_heading'] = 'Reset Your Account';
$lang['email']['reset_account_body']    = 'You have requested to reset your account on Ski-Manager.net. Click the button below to confirm. All your in-game data will be wiped and you will be able to start fresh.';
$lang['email']['reset_account_cta']     = 'Confirm Account Reset';
$lang['email']['reset_account_note']    = 'If you did not request this, please ignore this email or contact us at <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';
// Legacy
$lang['email']['reset_account_body1']   = '<p>You have requested to reset your account. Please <strong>';
$lang['email']['reset_account_body2']   = 'click here</a></strong> to reset your account.</p>';

$lang['email']['delete_account_subject'] = 'Delete account on Ski-Manager.net';
$lang['email']['delete_account_heading'] = 'Delete Your Account';
$lang['email']['delete_account_body']    = 'You have requested to permanently delete your account on Ski-Manager.net. Click the button below to confirm. <strong>This action cannot be undone.</strong>';
$lang['email']['delete_account_cta']     = 'Confirm Account Deletion';
$lang['email']['delete_account_note']    = 'If you did not request this, please ignore this email or contact us at <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';
// Legacy
$lang['email']['delete_account_body1']   = '<p>You have requested to delete your account. Please <strong>';
$lang['email']['delete_account_body2']   = 'click here</a></strong> to delete your account.</p>';

$lang['email']['invalid_code'] = 'The code is invalid, try again or  contact us at '.CONST_ADMIN_EMAIL .'.</div>';
$lang['email']['invalid_request'] = 'Invalid request. Please use the link provided in your email.';
$lang['email']['code_expired'] = 'The code has expired, request a new one or contact us at '.CONST_ADMIN_EMAIL .' if the problem persists.</div>';
$lang['email']['account_reset_success'] = 'Your account has been reinitialized successfully. You may now create a new resort.</div>';
$lang['email']['account_reset_failed'] = 'Your account could not be reinitialized. Try again or  contact us at '.CONST_ADMIN_EMAIL .'.</div>';
$lang['email']['account_delete_success'] = 'Your account has been deleted successfully. We are sad to let you go.</div>';
$lang['email']['account_delete_failed'] = 'Your account could not be deleted. Try again or  contact us at '.CONST_ADMIN_EMAIL .'.</div>';

// Vacation mode
$lang['email']['vacation_mode_subject'] = 'Ski-Manager account set to vacation mode';
$lang['email']['vacation_mode_heading'] = 'Your Account is in Vacation Mode';
$lang['email']['vacation_mode_body']    = 'You have been inactive on <a href="' . base_url() . '" style="color:#0ea5e9;">Ski-Manager.net</a> for 14 days, so your account has been put in Vacation Mode. This means any progress will be paused and your resort won\'t be open until you log in again.<br><br>To disable vacation mode, simply log in to your account.';
// Legacy
$lang['email']['vacation_mode_body1'] = '<p>You have been inactive on <a href="'.base_url().'">Ski-Manager.net</a> for 14 days so your account has been put in Vacation Mode. This means any progress will be paused and your resort won\'t be open until you login again.<br>To disable vacation mode, simply login to your account.</p>';
$lang['email']['vacation_mode_body2'] = '<br>Sincerely,<br>/Ski-Manager team.';

// ADMIN TRACKING
$lang['email']['tracking_delete_account'] = 'Account deleted';
$lang['email']['tracking_reset_account'] = 'Account reset';
$lang['email']['tracking_reset_password'] = 'Password reset';
$lang['email']['player_reset_account'] = 'A player has reset his account';
$lang['email']['player_delete_account'] = 'A player has deleted his account';
$lang['email']['player_reset_password'] = 'A player has reset his password';
$lang['email']['tracking_account_created'] = 'New account created';
$lang['email']['tracking_account_created_text'] = 'A new account created with the following details:';
$lang['email']['tracking_creation_time'] = 'Creation time';
$lang['email']['tracking_account_type'] = 'Account type';
$lang['email']['tracking_account_type_regular'] = 'Regular';
$lang['email']['tracking_account_type_facebook'] = 'Facebook';

