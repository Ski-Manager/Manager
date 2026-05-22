<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function redirect_ssl() {
    $CI =& get_instance();
    $class = $CI->router->fetch_class();
    $exclude =  array('');  // add more controller name to exclude ssl.

    // Detect HTTPS reliably: covers direct connections, reverse proxies, and load balancers.
    $is_https = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );

    if(!in_array($class,$exclude)) {
      // redirecting to ssl.
      $CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
      if (!$is_https) redirect($CI->uri->uri_string());
    } 
    else {
      // redirecting with no ssl.
      $CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
      if ($is_https) redirect($CI->uri->uri_string());
    }
}