<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Home_controller';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Explicit login aliases for compatibility across links/callbacks.
$route['login'] = 'Login_controller';
$route['logout'] = 'Login_controller/logout';
$route['login/check'] = 'Login_controller/checkLogin';
$route['login_controller/checklogin'] = 'Login_controller/checkLogin';
$route['login_controller/googlecallback'] = 'Login_controller/googleCallback';

// Leaderboard aliases for stable links/AJAX across environments.
$route['leaderboard'] = 'Leaderboard_controller';
$route['leaderboard/data'] = 'Leaderboard_controller/getDataTable';
$route['leaderboard/data/country'] = 'Leaderboard_controller/getDataTableByCountry';
$route['leaderboard/data/slope'] = 'Leaderboard_controller/getDataTableBySlope';
$route['leaderboard_controller'] = 'Leaderboard_controller';
$route['leaderboard_controller/getDataTable'] = 'Leaderboard_controller/getDataTable';
$route['leaderboard_controller/getDataTableByCountry'] = 'Leaderboard_controller/getDataTableByCountry';
$route['leaderboard_controller/getDataTableBySlope'] = 'Leaderboard_controller/getDataTableBySlope';

// Compatibility aliases when URLs are unexpectedly prefixed with /admin.
$route['admin/leaderboard'] = 'Leaderboard_controller';
$route['admin/leaderboard_controller'] = 'Leaderboard_controller';
$route['admin/leaderboard_controller/(:any)'] = 'Leaderboard_controller/$1';

// Compatibility aliases for previously generated broken admin links.
$route['admin/admin_'] = 'admin/admin_player_controller';
$route['admin/admin_(:any)'] = 'admin/admin_$1';

// Cron routes — must be defined before the maintenance/closed-mode catch-all so
// scheduled jobs always reach their controllers regardless of site mode.
$route['crons/(:any)'] = 'crons/$1';

// For maintenance mode and closed mode. Gets variables from config/config.php
// Closed mode : Site not opened yet. show info and email link
$client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

if (!in_array($client_ip, (array) config_item('maintenance_ips')) && config_item('closed_mode')) {
    $route['beta_controller'] = "Beta_controller";
    $route['newsletter_controller'] = "Newsletter_controller";
    $route['contact_controller'] = "Contact_controller";
    $route['contact'] = "Contact_controller";
    $route['about'] = "About_controller";
    $route['privacy'] = "Privacy_controller";
    $route['cookies'] = "About_cookies_controller";
    $route['terms'] = "Terms_controller";
    $route['default_controller'] = "Maintenance_controller/closed";
    $route['(:any)'] = "Maintenance_controller/closed";
}
// Maintenance mode: after site opened but not accessible for maintenance
else if (!in_array($client_ip, (array) config_item('maintenance_ips')) && config_item('maintenance_mode')) {
    $route['contact_controller'] = "Contact_controller";
    $route['contact'] = "Contact_controller";
    $route['about'] = "About_controller";
    $route['privacy'] = "Privacy_controller";
    $route['cookies'] = "About_cookies_controller";
    $route['terms'] = "Terms_controller";
    $route['default_controller'] = "Maintenance_controller/maintenance";
    $route['(:any)'] = "Maintenance_controller/maintenance";
}

// Privacy policy / cookies page at its canonical SEO-friendly URL
$route['ski-manager/About_cookies'] = 'About_cookies_controller';

// Clean SEO-friendly URL aliases for legal pages
$route['privacy'] = 'Privacy_controller';
$route['cookies'] = 'About_cookies_controller';
$route['contact'] = 'Contact_controller';
$route['terms'] = 'Terms_controller';
$route['about'] = 'About_controller';

// CrazyGames SDK auth endpoint
$route['crazygames_controller/verify_token'] = 'Crazygames_controller/verify_token';

// Blog route
$route['blog'] = 'Blogs_controller';

// Game Guide (public, no login required)
$route['guide'] = 'Guide_controller';

// '/trails' is a common alias for ski slopes/trail management
$route['trails'] = 'Slope_controller';
$route['trails/(:any)'] = 'Slope_controller/$1';

// URI like '/en/about' -> use controller 'about'
//$route['^(en|fr)/(.+)$'] = "$2";
 
// '/en', '/de', '/fr'  URIs -> use default controller
//$route['^(en|fr)$'] = $route['default_controller'];
