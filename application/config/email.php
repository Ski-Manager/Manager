<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// --------------------------------------------------
// CodeIgniter Email Library — Brevo SMTP relay
// All transactional emails (verification, password
// reset, account management) are routed through
// Brevo's SMTP relay.
// Update these values to match config/brevo.php.
// --------------------------------------------------

$config['protocol']    = 'smtp';
$config['smtp_host']   = 'smtp-relay.brevo.com';
$config['smtp_port']   = 587;
$config['smtp_user']   = 'noreply@ski-manager.net';
$config['smtp_pass']   = 'xsmtpsib-3e7be495f0e330ad5c6858060103dd9d0ceed57215be40725c10193b4f32e17b-sVgj5WLOgcVrB9g4';
$config['smtp_crypto'] = 'tls';
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
