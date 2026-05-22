<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller_constructor'][] = array(
                                'class'    => 'db_init',
                                'function' => 'set_sql_mode',
                                'filename' => 'db_init.php',
                                'filepath' => 'hooks'
                                );

$hook['post_controller_constructor'][] = array(
                                'function' => 'redirect_ssl',
                                'filename' => 'ssl.php',
                                'filepath' => 'hooks'
                                );

// Restore the user's saved language from a persistent cookie whenever the
// CI session is missing site_lang (e.g. after session expiry or a new
// browser session), and re-load any already-loaded translation files in
// the correct language.
$hook['post_controller_constructor'][] = array(
                                'class'    => 'LanguageLoader',
                                'function' => 'initialize',
                                'filename' => 'LanguageLoader.php',
                                'filepath' => 'hooks',
                                'params'   => ''
                                );


$hook['post_controller_constructor'][] = array(
                                'class'    => 'get_info_general',
                                'function' => 'prepare',
                                'filename' => 'get_info_general.php',
                                'filepath' => 'hooks',
                                'params'   => ''
                                );
$hook['post_controller_constructor'][] = array(
                                'class'    => 'get_info_general',
                                'function' => 'online_players',
                                'filename' => 'get_info_general.php',
                                'filepath' => 'hooks',
                                'params'   => ''
                                );
$hook['post_controller_constructor'][] = array(
                                'class'    => 'get_info_general',
                                'function' => 'get_info_general',
                                'filename' => 'get_info_general.php',
                                'filepath' => 'hooks',
                                'params'   => ''
                                );
$hook['post_controller_constructor'][] = array(
                                'class'    => 'get_info_general',
                                'function' =>'get_achievements',
                                'filename' => 'get_info_general.php',
                                'filepath' => 'hooks',
                                'params'   => ''
                                );