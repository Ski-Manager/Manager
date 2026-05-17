<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']    = 'smtp';
$config['smtp_host']   = 'email-smtp.us-east-1.amazonaws.com';  // SES SMTP endpoint
$config['smtp_port']   = 587;
$config['smtp_user']   = 'AKIAWPE2RAHDPSKY3DOQ';
$config['smtp_pass']   = 'BC7I6J37xs9MpnplIJujnRFkHcA7ACxeIwhp8686Rkzk';
$config['smtp_crypto'] = 'tls';
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
$config['wordwrap']    = TRUE;
