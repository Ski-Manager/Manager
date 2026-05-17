<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['signup']['text']		= 'Create a new account';
$lang['signup']['account_info']		= 'Your account details';
$lang['signup']['new_password']		= 'New password';
$lang['signup']['password_confirm']		= 'Confirm password';
$lang['signup']['new_password_confirm']		= 'Confirm new password';
$lang['signup']['age']		= 'Age';
$lang['signup']['referral']		= 'Referral (Optional)';
$lang['signup']['referral_confirmed']		= 'Your referral was taken into account. You both, will be granted with an additional '.GENEPIS.' Génépis once you have reached a few achievements.';
$lang['signup']['info_referral']		= 'Enter the name of your referral if you have one so you both can get '.GENEPIS.' additional Génépis (Virtual currency on Ski-manager).';
$lang['signup']['signup_create']		= 'Create my account';
$lang['signup']['edit_account_submit']		= 'Update my account';
$lang['signup']['account_created']		= 'Registration successful. We\'ve sent an email to your email address. Open it up to activate your account and start playing.<br>If you haven\'t received the email in 15 minutes, check your spam folder or contact us at '.CONST_ADMIN_EMAIL .'.';
$lang['signup']['account_updated']		= '<div class="alert alert-success text-center">Account updated.</div>';
$lang['signup']['no_data_changed']		= '<div class="alert alert-warning text-center">No information has been changed.</div>';
$lang['signup']['account_not_activated']		= 'Your account hasn\'t been activated yet. Please check your inbox to activate your account. If you haven\'t received the email in 15 minutes, check your spam folder or contact us at '.CONST_ADMIN_EMAIL .'.';
$lang['signup']['intro_update_account']		= 'Here you can find your account details. To change your username, contact us at '.CONST_ADMIN_EMAIL .'.';
$lang['signup']['click_here']             = 'Click here';
$lang['signup']['to_send_email']          = ' to send a new activation email to';
$lang['alpha_dash_space']    = "The Username field may only contain alpha-numeric characters, underscores, and dashes/spaces.";
$lang['signup']['invalid_username']    = "The Username field contains invalid characters.<br>";
$lang['missing_username']    = "You need to enter a Username.";
$lang['signup']['info_password']    = "The Password need to be at least 4 characters long.<br>";
$lang['signup']['agree_newsletter']    = "By registering on the website, you will be automatically added to our newsletter's recipient list. You can unsubscribe at anytime from your account page.";


$lang['signup']['reset_account']    = "Reset account";
$lang['signup']['confirm_reset_account_title']    = "Confirmation of account reinitialisation.";
$lang['signup']['confirm_reset_account']    = "Confirm reset my account";
$lang['signup']['reset_account_text']    = "Resetting your account will delete your resort and all progress, however your account will still be active.";
$lang['signup']['confirm_reset_account_text']    = "Are you sure you want to reset your account? This action cannot be undone. Your resort will be destroyed and all progress will be lost. Your account will remain active.";
$lang['signup']['email_sent_reset_account']    = "An email with further instructions on how to reset your account has been sent to your email address.";
$lang['signup']['email_sent_reset_account_confirmed']    = "Your account has been reinitialised.";
$lang['signup']['password_confirm_reset']    = "Enter your password to confirm the reinitialisation of your account";

$lang['signup']['delete_account']    = "Delete account";
$lang['signup']['confirm_delete_account_title']    = "Confirmation of account deletion";
$lang['signup']['confirm_delete_account']    = "Confirm delete my account";
$lang['signup']['delete_account_text']    = "Deleting your account will delete your resort and any information related to your user.";
$lang['signup']['confirm_delete_account_text']    = "Are you sure you want to delete your account? This action cannot be undone. Your resort will be destroyed, all progress will be lost and any information related to your user will be deleted.";
$lang['signup']['email_sent_delete_account']    = "An email with further instructions on how to delete your account has been sent to your email address.";
$lang['signup']['email_sent_delete_account_confirmed']    = "Your account has been deleted.";
$lang['signup']['password_confirm_delete']    = "Enter your password to confirm the deletion of your account";


// Facebook account
$lang['signup']['finalize_account']    = "You're almost done!";
$lang['signup']['finalize_account_info']    = "You are about to create a new account on Ski-Manager using your login information from Facebook";
$lang['signup']['finalize_account_more_info']    = "We just need a little bit more to finalize your account.";
$lang['signup']['finalize_account_call_you']    = "What should we call you";
$lang['signup']['signup_finalize_account']    = "Finalize my account";
$lang['signup']['signup_merge_account']    = "Merge my accounts";
$lang['signup']['account_finalized']    = "Congratulations, your account has been finalized, you can start playing right away! Click <a href='".base_url()."genepis_controller'>here</a> to start building your resort.";

// Merging accounts
$lang['signup']['merge_account']    = "Email already registered";
$lang['signup']['merge_account_existing']    = "There is already an account registered on Ski-Manager with the email you entered in the Facebook login form.";
$lang['signup']['fb_account_existing']    = "There is already an account registered on Ski-Manager with the Facebook account currently used";
$lang['signup']['fb_account_existing2']    = "Use the Log in bottom on the left side to login with Facebook or create a standard password to login with your username, using the link below.";
$lang['signup']['merge_account_question']    = "Would you like to merge both accounts? If you want to login using Facebook, you need to merge both accounts, you will then, be able to login using both methods.";
$lang['signup']['merge_account_password']    = "To confirm the merge, enter the password associated to the provided email address";
$lang['signup']['signup_merge_account']    = "Merge my accounts";
$lang['signup']['account_merged']    = "Your accounts have been merge. You can now login using Facebook or your regular username/password.";

// account page
$lang['signup']['linked_facebook_account']    = "Your account is linked to Facebook";
$lang['signup']['not_linked_facebook']    = "Your account is not linked to Facebook";
$lang['signup']['linked_google_account']    = "Your account is linked to Google";
$lang['signup']['not_linked_google']    = "Your account is not linked to Google";
$lang['signup']['google_account_linked']    = "Your Google account has been successfully linked.";
$lang['signup']['currently_no_newsletter']    = "You are currently not registered to the newsletter.";
$lang['signup']['currently_newsletter']    = "You are currently registered to the newsletter.";
$lang['signup']['newsletter_max_freq']    = "The newsletter is sent maximum twice a month and will inform users about important updates and new features.";
$lang['signup']['newsletter_select_lang']    = "Select your language";
$lang['signup']['newsletter_email_conditions']    = "Your e-mail address is only used to send you our newsletter and information about the activities of ski-manager. You can always use the unsubscribe link included in the newsletter.";
$lang['signup']['unsubscribe']    = "Unsubscribe";
$lang['signup']['subscribe']    = "Subscribe";
$lang['signup']['newsletter']    = "Newsletter";

// Resend verification email
$lang['signup']['resend_verification_email']         = "Resend verification email";
$lang['signup']['resend_verification_email_text']    = "Your account has not been activated yet. Click the button to resend the activation email.";
$lang['signup']['email_sent_verification']           = "A new activation email has been sent to your email address.";
$lang['signup']['account_already_activated']         = "Your account is already activated.";
