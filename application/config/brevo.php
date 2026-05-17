<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// --------------------------------------------------
// Brevo (formerly Sendinblue) account configuration
// --------------------------------------------------
// Replace the placeholder values below with the
// credentials from your Brevo account dashboard:
//   API key  : https://app.brevo.com/settings/keys/api
//   SMTP key : https://app.brevo.com/settings/keys/smtp
// --------------------------------------------------

// Brevo v3 API key
$config['brevo_api_key'] = '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';

// Brevo newsletter list ID (Settings > Contacts > Lists)
$config['brevo_newsletter_list_id'] = 7;

// Brevo SMTP relay credentials (used for transactional emails)
$config['brevo_smtp_host'] = 'smtp-relay.brevo.com';
$config['brevo_smtp_port'] = 587;
$config['brevo_smtp_user'] = 'noreply@ski-manager.net';
$config['brevo_smtp_pass'] = 'xsmtpsib-3e7be495f0e330ad5c6858060103dd9d0ceed57215be40725c10193b4f32e17b-sVgj5WLOgcVrB9g4';

$config['brevo_signup_form_id'] = '';
$config['brevo_unsubscribe_form_id'] = '';
