<?php
// Buffer all output from this point so that setcookie() / header() always
// succeed even if the autoloader, Flare, or any other early-boot code emits
// warnings or notices (display_errors = 1 in dev mode).  The buffer is
// flushed automatically when the script finishes or CodeIgniter sends output.
ob_start();

// Set session cookie to SameSite=None so it works inside third-party iframes
// (e.g. CrazyGames). Requires Secure; both are safe because the site is HTTPS-only.
// PHP 8's positional-arg session_set_cookie_params() does NOT reset these ini values,
// so they survive CodeIgniter's internal session_set_cookie_params() call.
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure',   '1');

// --------------------------------------------------------------------
// COMPOSER AUTOLOADER
// --------------------------------------------------------------------
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// --------------------------------------------------------------------
// HOST & SUBDOMAIN SETUP
// --------------------------------------------------------------------
$host = $_SERVER['HTTP_HOST'];
$SERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $host;
define('HOST', $SERVER_NAME);

// Extract subdomain (if any)
$host3 = strrpos($host, '.');
$host2 = $host3 !== false ? substr($host, 0, $host3) : $host;
$host1 = strrpos($host2, '.');
$subdomain = ($host1 !== false) ? substr($host, 0, $host1) : '';

// default application folder (may be overridden below)
$application_folder = 'application';

// Strip port number from host for local-environment detection
// (HTTP_HOST may contain a port, e.g. "localhost:8080" or "127.0.0.1:443").
$host_bare = strtolower(explode(':', $host, 2)[0]);

if ($host_bare === 'localhost' || $host_bare === '127.0.0.1') {
    define('HOST_TYPE', 'localhost');
    define('SUBDOMAIN', false);
    $application_folder = 'application';
} else {
    if ($subdomain === 'beta') {
        // Beta staging environment — use same application folder as production
        define('HOST_TYPE', 'site');
        define('SUBDOMAIN', false);
        $application_folder = 'application';
    } elseif ($subdomain !== 'www' && $subdomain !== '') {
        define('HOST_TYPE', 'subdomain');
        define('SUBDOMAIN', $subdomain);
        $application_folder = 'test';
    } else {
        define('HOST_TYPE', 'site');
        define('SUBDOMAIN', false);
        $application_folder = 'application';
    }
}

// --------------------------------------------------------------------
// ENVIRONMENT SETUP
// --------------------------------------------------------------------
define('ENVIRONMENT', 'development');

switch (ENVIRONMENT) {
    case 'development':
        // Keep development visibility high but hide PHP 8.2+ deprecations from legacy libs.
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

// --------------------------------------------------------------------
// FLARE ERROR MONITORING
// --------------------------------------------------------------------
if (class_exists(\Sentry\SentrySdk::class)) {
    \Sentry\init(['dsn' => getenv('SENTRY_DSN')]);
}

// --------------------------------------------------------------------
// MAINTENANCE MODE WITH TOKEN + IP BYPASS
// --------------------------------------------------------------------
$maintenance = false; // enable/disable maintenance mode

if ($maintenance) {

// secret token for bypass - should be moved to environment variable for production
$bypass_token = getenv('MAINTENANCE_BYPASS_TOKEN') ?: '';

// bypass cookie
$bypass_cookie_name = 'maintenance_bypass';
$bypass_cookie_lifetime = 7 * 24 * 60 * 60; // 7 days

// maintenance.html
$maintenance_page = 'maintenance.html';

// request path
$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$request_path = parse_url($request_uri, PHP_URL_PATH);
$is_requesting_maintenance_page = (basename($request_path) === $maintenance_page);

// current user IP
$remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

// --------------------------------------------------------------------
// IP BYPASS (configure via environment variable in production)
// --------------------------------------------------------------------
$allowed_ips_env = getenv('MAINTENANCE_ALLOWED_IPS');
$allowed_ips = $allowed_ips_env ? explode(',', $allowed_ips_env) : [
    '207.72.1.93',   // your IP - remove in production
];

$bypass_by_ip = in_array($remote_addr, $allowed_ips, true);

// --------------------------------------------------------------------
// COOKIE BYPASS
// --------------------------------------------------------------------
$bypass_by_cookie = (!empty($_COOKIE[$bypass_cookie_name]) 
    && $_COOKIE[$bypass_cookie_name] === $bypass_token);

// --------------------------------------------------------------------
// GET ?bypass=TOKEN
// --------------------------------------------------------------------
// GET ?bypass=TOKEN  OR  POST bypass=TOKEN (POST preferred to keep token out of URL/logs)
$bypass_by_get = false;
$_bypass_input = null;
$_bypass_from_post = false;
if (isset($_GET['bypass']) && is_string($_GET['bypass'])) {
    $_bypass_input = $_GET['bypass'];
} elseif (isset($_POST['bypass']) && is_string($_POST['bypass'])) {
    $_bypass_input = $_POST['bypass'];
    $_bypass_from_post = true;
}

if ($_bypass_input !== null && $_bypass_input === $bypass_token) {
    $bypass_by_get = true;

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
              || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
              || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    if (PHP_VERSION_ID >= 70300) {
        setcookie($bypass_cookie_name, $bypass_token, [
            'expires' => time() + $bypass_cookie_lifetime,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        setcookie($bypass_cookie_name, $bypass_token,
            time() + $bypass_cookie_lifetime, '/', '', $secure, true);
    }

    $_COOKIE[$bypass_cookie_name] = $bypass_token;
    $bypass_by_cookie = true;

    // PRG: redirect POST submissions to GET so the token never appears in the URL
    // and the browser does not offer to resubmit on refresh.
    if ($_bypass_from_post) {
        header('Location: /');
        exit();
    }
}

// --------------------------------------------------------------------
// BOT BYPASS (allow search engine crawlers and similar bots through)
// --------------------------------------------------------------------
$is_bot = false;
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $is_bot = (bool) preg_match(
        '/googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|sogou|exabot'
        . '|facebookexternalhit|ia_archiver|google-inspectiontool|googleother|apis-google'
        . '|google-adstxt-crawler|adstxtcrawler/i',
        $_SERVER['HTTP_USER_AGENT']
    );
}

// --------------------------------------------------------------------
// PATH EXEMPTIONS – allow login/register/beta paths even in maintenance
// so users can still log in and reach the beta T&C acceptance page
// --------------------------------------------------------------------
$exempt_segments = ['home_controller', 'login_controller', 'register_controller',
                    'beta_controller', 'reset_password_controller', 'language_switcher'];
$path_parts      = explode('/', ltrim((string)$request_path, '/'));
$path_segment    = strtolower(trim($path_parts[0] ?? ''));
$is_exempt_path  = $path_segment !== '' && in_array($path_segment, $exempt_segments, true);

// Always allow ads.txt through maintenance – ad networks (e.g. Google AdSense)
// must be able to fetch it at any time regardless of site status.
$is_ads_txt = ($request_path === '/ads.txt');

// --------------------------------------------------------------------
// FINAL DECISION
// --------------------------------------------------------------------
$is_bypassed = $bypass_by_cookie || $bypass_by_get || $bypass_by_ip || $is_bot || $is_exempt_path || $is_ads_txt;

if ($maintenance && !$is_bypassed && !$is_requesting_maintenance_page) {
    header('HTTP/1.1 503 Service Unavailable', true, 503);
    header('Retry-After: 3600');
    header('Location: ' . $maintenance_page);
    exit();
}

} // end maintenance mode block

// --------------------------------------------------------------------
// SYSTEM & APPLICATION PATHS (CodeIgniter standard setup)
// --------------------------------------------------------------------
$system_path = 'system';
$view_folder = '';

if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== false) {
    $system_path = $_temp . '/';
} else {
    $system_path = rtrim($system_path, '/') . '/';
}

if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Your system folder path does not appear to be set correctly.';
    exit(3);
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', str_replace('\\', '/', $system_path));
define('FCPATH', dirname(__FILE__) . '/');
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

// application folder
if (is_dir($application_folder)) {
    if (($_temp = realpath($application_folder)) !== false) {
        $application_folder = $_temp;
    }
    define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);
} else {
    if (!is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'Your application folder path does not appear to be set correctly.';
        exit(3);
    }
    define('APPPATH', BASEPATH . $application_folder . DIRECTORY_SEPARATOR);
}

// view folder
if (!is_dir($view_folder)) {
    if (!empty($view_folder) && is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
        $view_folder = APPPATH . $view_folder;
    } elseif (!is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'Your view folder path does not appear to be set correctly.';
        exit(3);
    } else {
        $view_folder = APPPATH . 'views';
    }
}

if (($_temp = realpath($view_folder)) !== false) {
    $view_folder = $_temp . DIRECTORY_SEPARATOR;
} else {
    $view_folder = rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR;
}

define('VIEWPATH', $view_folder);

require_once BASEPATH . 'core/CodeIgniter.php';
