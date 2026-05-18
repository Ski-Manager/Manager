<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
*/
switch (HOST_TYPE) {
    case 'subdomain':
        $config['base_url'] = 'https://' . SUBDOMAIN . '.ski-manager.net/';
        break;

    case 'localhost':
        if (strpos(SUBDOMAIN, 'test') !== false) {
            $config['base_url'] = 'https://localhost/test/';
        } else {
            $config['base_url'] = 'https://localhost/ski-manager/';
        }
        break;

    default:
        $config['base_url'] = 'https://www.ski-manager.net/';
        break;
}

// Prefer the current request host/protocol/path when available.
// This avoids cross-domain redirects (e.g. to www) that can break login callbacks.
if (PHP_SAPI !== 'cli' && !empty($_SERVER['HTTP_HOST'])) {
    $is_https = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );
    $scheme = $is_https ? 'https' : 'http';
    $script_name = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '';
    // Some rewrites report the current URI instead of index.php in SCRIPT_NAME.
    // In that case, force root to avoid generating nested base URLs like /admin/.
    // Also force root on Vercel where SCRIPT_NAME is /api/index.php.
    if (stripos(basename($script_name), 'index.php') === false
        || strpos($script_name, '/api/') !== false) {
        $script_dir = '/';
    } else {
        $script_dir = dirname($script_name);
    }
    $script_dir = trim(str_replace('\\', '/', (string) $script_dir), '/');
    $script_dir = ($script_dir === '' || $script_dir === '.') ? '' : $script_dir.'/';
    $config['base_url'] = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $script_dir;
}

//$config['base_url'] = 'https://127.0.0.1/ski-manager/';
//$config['base_url'] = 'https://localhost/ski-manager/';
//$config['base_url'] = 'https://www.ski-manager.net/';

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
*/
$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
*/
$config['uri_protocol'] = 'REQUEST_URI';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
*/
$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
*/
$config['language'] = 'english';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
*/
$config['enable_hooks'] = TRUE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-@';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
*/
$config['allow_get_array']      = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']   = 'c';
$config['function_trigger']     = 'm';
$config['directory_trigger']    = 'd';

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
*/
$config['log_threshold'] = 0;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Log File Extension
|--------------------------------------------------------------------------
*/
$config['log_file_extension'] = '';

/*
|--------------------------------------------------------------------------
| Log File Permissions
|--------------------------------------------------------------------------
*/
$config['log_file_permissions'] = 0644;

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Error Views Directory Path
|--------------------------------------------------------------------------
*/
$config['error_views_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
*/
$config['cache_path'] = '';
$config['cache_default'] = 'memcached';
$config['cache_backup']  = 'dummy';

/*
|--------------------------------------------------------------------------
| Cache Include Query String
|--------------------------------------------------------------------------
*/
$config['cache_query_string'] = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
*/
$config['encryption_key'] = getenv('CI_ENCRYPTION_KEY') ?: '';
/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
*/
$config['sess_driver']             = 'database';
$config['sess_cookie_name']        = 'ci_session';
$config['sess_expiration']         = 43200;
$config['sess_save_path']          = 'ci_sessions';
$config['sess_match_ip']           = FALSE;
$config['sess_time_to_update']     = 300;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
*/
$config['cookie_prefix']   = '';
$config['cookie_domain']   = '';
$config['cookie_path']     = '/';
$config['cookie_secure']   = TRUE;
$config['cookie_httponly'] = FALSE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
*/
$config['csrf_protection']   = FALSE;
$config['csrf_token_name']   = 'csrf_test_name';
$config['csrf_cookie_name']  = 'csrf_cookie_name';
$config['csrf_expire']       = 7200;
$config['csrf_regenerate']   = TRUE;
$config['csrf_exclude_uris'] = [];

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
*/
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
*/
$config['proxy_ips'] = '';

/*
|--------------------------------------------------------------------------
| Site Administrator Email Address
|--------------------------------------------------------------------------
*/
const CONST_ADMIN_EMAIL    = 'contact@ski-manager.net';
const CONST_TRACKING_EMAIL = 'contact@ski-manager.net';
const CONST_NOREPLY_EMAIL  = 'contact@ski-manager.net';

date_default_timezone_set("America/Detroit");

/*
|--------------------------------------------------------------------------
| GAME VARIABLES
|--------------------------------------------------------------------------
*/
const START_CASH                     = '18000000';
const START_SNOW                     = '40';
const SNOW_HISTORY_DAYS              = 7;      // Number of past days shown in the snow level history table on the cannon page
const DEFAULT_SKIPASS_DAILY          = '20';
const DEFAULT_SKIPASS_WEEKLY         = '130';
const DEFAULT_VIP_PASS_PRICE         = 0;    // VIP pass disabled by default
const DEFAULT_FAMILY_DISCOUNT_PCT    = 0;    // Family discount disabled by default

// Dynamic pricing constants
const VIP_VISITOR_FRACTION           = 0.05; // Fraction of daily visitors buying VIP passes (5%)
const FAMILY_VISITOR_FRACTION        = 0.25; // Fraction of daily visitors eligible for family discount (25%)
const FAMILY_DISCOUNT_DEMAND_BONUS   = 0.50; // Demand increase per 1% of family discount (0.5% more visitors per 1% discount)
const MIN_VIP_PASS_PRICE             = 0;    // 0 = disabled
const MAX_VIP_PASS_PRICE             = 500;  // Maximum VIP pass price (€)
const MIN_FAMILY_DISCOUNT_PCT        = 0;    // 0 = disabled
const MAX_FAMILY_DISCOUNT_PCT        = 50;   // Maximum family discount (%)
const ACTIVE_SECTORS                 = '5';
const GENEPIS                        = '30';
const COST_EXT_FORECAST              = '30';
const CRON_USERNAME        = 'Cronlogin';
const ADMIN_USERNAME       = 'Adminlogin';
const CRON_PASSWORD        = getenv('CRON_PASSWORD') ?: '';
const ADMIN_PASSWORD       = getenv('ADMIN_PASSWORD') ?: '';
const IMPERSONATE_PASSWORD = getenv('IMPERSONATE_PASSWORD') ?: '';
const SLOPE_METER_PRICE              = ['600', '2000', '1000', '250', '6000', '1500'];
const SLOPE_METER_BUILDING_TIME      = ['100', '200', '150', '40', '400', '160'];
const ACCELERATOR_FACTOR             = '2';
const MARKETING_COST_INCREASE_PER_LEVEL       = '1.03';
const MARKETING_CASH_INCREASE_PER_LEVEL       = '1.05';
const MARKETING_GENEPIS_INCREASE_PER_LEVEL    = '1.05';
const MARKETING_AFFLUENCE_INCREASE_PER_LEVEL  = '1.05';
const MARKETING_REP_INCREASE_PER_LEVEL        = '1.05';
const SEASON_BONUS                   = '3000000';
const LEGACY_SEASON_THRESHOLD        = 20;          // Minimum seasons before a historical rating is calculated
const LEGACY_LEGENDARY_MIN_RATING    = 50;          // Minimum rating (out of 100) to unlock Legendary Mountain status
const LEGACY_BONUS_CASH              = 5000000;     // Cash bonus reserved when Legendary Mountain status is earned; applied to the player's next new resort
const MICROCLIMATE_CHANGE_BASE_COST  = 50000;  // Base cost (€) for each microclimate change; actual cost = (change_count + 1) * base
const COST_GENEPIS_REPORT            = '20';
const SECONDS_PER_GENEPIS            = '7200';
const PRESTIGE_COEF                  = '0.001';
const MAX_SNOW_LEVEL                 = 200;    // Maximum snow level (cm) used for the snow level progress bar display
const LOW_SNOW_THRESHOLD             = 20;     // Snow level (cm) below which a low-snow warning is shown on the cannon page
const MAX_PATROL_PER_SLOPE           = 3;      // Maximum number of ski patrol staff that can be assigned to a single slope
// ----- Lift Wear & Lifecycle -----
const LIFT_MAX_LEVEL                     = 3;    // Maximum upgrade level for any lift
const LIFT_SEASON_DAYS                   = 135;   // Real days that equal one game-season (= 1 lift age-year)
const LIFT_MAX_AGE_SEASONS               = 20;    // Mandatory replacement after this many seasons
const LIFT_AGE_EFFICIENCY_DROP_PER_YEAR  = 0.015; // Efficiency (throughput) drop per season (1.5 %)
const LIFT_AGE_COST_MULTIPLIER_PER_YEAR  = 0.02;  // Daily maintenance cost increase per season (2 %)
// ----- Maintenance Depth -----
const MAINT_BASE_FAILURE_CHANCE          = 2;     // Base daily failure probability (%) for any open lift
const MAINT_AGE_FAILURE_PER_SEASON       = 0.15;  // Additional failure % added per game-season of lift age
const MAINT_USAGE_FAILURE_PER_10PCT      = 0.3;   // Additional failure % per 10 % of visitor/capacity load above 50 %
const MAINT_LIFT_TYPE_FAILURE_MULT       = [       // Failure probability multiplier by lift_type id
    1 => 0.6,  // surface lift / T-bar  (simple mechanics)
    2 => 1.0,  // chairlift             (standard)
    3 => 1.4,  // gondola               (complex)
    4 => 1.6,  // cable car             (very complex)
    5 => 1.1,  // 8-person chairlift
    6 => 1.5,  // 12-person gondola
];
const MAINT_PLAN_STANDARD_COST_PER_LIFT    = 500;   // Daily € cost per open lift on Standard plan
const MAINT_PLAN_PREVENTIVE_COST_PER_LIFT  = 1500;  // Daily € cost per open lift on Preventive plan
const MAINT_PLAN_STANDARD_REPAIR_DISCOUNT  = 0.15;  // Repair cost discount factor for Standard plan (15 %)
const MAINT_PLAN_PREVENTIVE_FAILURE_REDUCTION = 0.50; // Failure chance reduction factor for Preventive plan (50 %)
const MAINT_PLAN_PREVENTIVE_REPAIR_DISCOUNT   = 0.30; // Repair cost discount factor for Preventive plan (30 %)
const MAINT_STAFF_MAX_REPAIR_DISCOUNT      = 0.20;  // Maximum additional repair discount from staff skill (20 %)
const NIGHT_SKIING_ELECTRICITY_COST  = 500;    // Base nightly electricity cost (€) when night skiing is enabled
const NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE = 100;  // Additional electricity cost (€) per open slope per night
const NIGHT_SKIING_REVENUE_BONUS     = 0.15;   // Base bonus factor on skipass revenue from night skiing (15%)
const NIGHT_SKIING_SLOPE_REVENUE_FACTOR = 0.02; // Additional bonus per open slope beyond the first (2% per slope)

// Valid options for per-trail night skiing configuration
const NIGHT_SKIING_VALID_LIGHT_TYPES    = ['led', 'halogen', 'metal_halide'];
const NIGHT_SKIING_VALID_POLE_SPACINGS  = [15, 25, 35];  // metres between lighting poles

// Allowed operating-hour ranges for night skiing
const NIGHT_SKIING_MIN_START_HOUR = 15;  // Earliest allowed start hour (15:00)
const NIGHT_SKIING_MAX_START_HOUR = 21;  // Latest allowed start hour  (21:00)
const NIGHT_SKIING_MIN_END_HOUR   = 19;  // Earliest allowed end hour  (19:00)
const NIGHT_SKIING_MAX_END_HOUR   = 23;  // Latest allowed end hour    (23:00)

// Per-trail electricity cost multipliers (relative to NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE)
const NIGHT_SKIING_LIGHT_TYPE_COST = [
    'led'          => 1.0,   // Most energy-efficient
    'halogen'      => 1.3,   // Mid-range consumption
    'metal_halide' => 1.6,   // Highest consumption
];
const NIGHT_SKIING_POLE_SPACING_COST = [
    15 => 1.3,   // Dense spacing – more poles, higher cost
    25 => 1.0,   // Standard spacing
    35 => 0.8,   // Sparse spacing – fewer poles, lower cost
];
const NIGHT_SKIING_BRIGHTNESS_COST_FACTOR = 0.15; // Cost multiplier increment per brightness level above 1

// Per-trail revenue multipliers
const NIGHT_SKIING_LIGHT_TYPE_REVENUE = [
    'led'          => 1.0,   // Standard appeal
    'halogen'      => 1.1,   // Warm light – slightly more attractive
    'metal_halide' => 1.2,   // Bright daylight-like light – most attractive
];
const NIGHT_SKIING_POLE_SPACING_REVENUE = [
    15 => 1.1,   // Well-lit – most attractive
    25 => 1.0,   // Standard
    35 => 0.9,   // Dimmer – less attractive
];
const NIGHT_SKIING_BRIGHTNESS_REVENUE_FACTOR = 0.05; // Revenue multiplier increment per brightness level above 1

// Fraction of total daily visitors counted as night-session visitors (for night ticket revenue)
const NIGHT_SKIING_VISITOR_FRACTION = 0.25;

// Day-of-week visitor demand factor for dynamic demand
const NIGHT_SKIING_DOW_FACTOR = [
    1 => 0.85,  // Monday
    2 => 0.85,  // Tuesday
    3 => 0.90,  // Wednesday
    4 => 1.00,  // Thursday
    5 => 1.20,  // Friday
    6 => 1.30,  // Saturday
    7 => 1.10,  // Sunday
];

// Weather-based visitor demand factor
const NIGHT_SKIING_WEATHER_VISITOR_FACTOR = [
    'Sunny'   => 1.10,
    'Cloudy'  => 1.00,
    'Snowing' => 0.90,
];

// Peak season thresholds and factors for visitor demand
const NIGHT_SKIING_PEAK_HIGH_THRESHOLD = 1.3;
const NIGHT_SKIING_PEAK_HIGH_FACTOR = 1.20;
const NIGHT_SKIING_PEAK_NORMAL_FACTOR = 1.05;
const NIGHT_SKIING_OFFPEAK_FACTOR = 0.75;

// Night skiing entertainment options (resort-level)
const NIGHT_SKIING_VALID_ENTERTAINMENT = ['none', 'basic', 'premium'];
const NIGHT_SKIING_ENTERTAINMENT_COST = [
    'none'    => 0,    // No entertainment
    'basic'   => 200,  // Hot drinks + background music
    'premium' => 500,  // Live music + full bar
];
const NIGHT_SKIING_ENTERTAINMENT_REVENUE = [
    'none'    => 1.0,  // No bonus
    'basic'   => 1.10, // +10 % night revenue
    'premium' => 1.25, // +25 % night revenue
];

// Night skiing safety level (resort-level, 1–3)
const NIGHT_SKIING_SAFETY_MIN_LEVEL = 1;
const NIGHT_SKIING_SAFETY_MAX_LEVEL = 3;
const NIGHT_SKIING_SAFETY_COST = [
    1 => 50,   // Standard safety:  50 €/night
    2 => 150,  // Enhanced safety: 150 €/night
    3 => 300,  // Maximum safety:  300 €/night
];
const NIGHT_SKIING_SAFETY_REPUTATION_BONUS = [
    1 => 0,    // No reputation boost
    2 => 1,    // +1 reputation per night
    3 => 2,    // +2 reputation per night
];

// Night ski school (resort-level)
const NIGHT_SKIING_SCHOOL_VISITOR_FRACTION  = 0.08;  // Fraction of night visitors who sign up for lessons (8%)
const NIGHT_SKIING_SCHOOL_MAX_PRICE         = 999;   // Maximum allowed lesson price (€ per person)
const NIGHT_SKIING_SCHOOL_REPUTATION_BONUS  = 1;     // Reputation points gained per night when school is active

// Weather auto-suspend: cancels night skiing when it's raining (no revenue or costs that night)
const NIGHT_SKIING_WEATHER_SUSPEND_CONDITIONS = ['Raining'];

// Torchlight descent (resort-level)
const NIGHT_SKIING_TORCHLIGHT_COST            = 200;   // Nightly operating cost for torchlight descent (€)
const NIGHT_SKIING_TORCHLIGHT_VISITOR_BONUS   = 0.15;  // Night visitor count increase when torchlight is active (+15%)
const NIGHT_SKIING_TORCHLIGHT_REPUTATION_BONUS = 1;    // Reputation points gained per night when torchlight is active

// Night photography package (resort-level)
const NIGHT_SKIING_PHOTO_VISITOR_FRACTION  = 0.05;  // Fraction of night visitors who buy a photography package (5%)
const NIGHT_SKIING_PHOTO_MAX_PRICE         = 299;   // Maximum allowed photography package price (€ per person)
const NIGHT_SKIING_PHOTO_REPUTATION_BONUS  = 1;     // Reputation points gained per night when photo package is active

// Trail quality degradation from night skiing
const NIGHT_SKIING_GROOMING_SURCHARGE_PER_TRAIL = 75;
const NIGHT_SKIING_QUALITY_LOSS_BASE = 1.0;
const NIGHT_SKIING_QUALITY_LOSS_BRIGHTNESS_FACTOR = 0.5;

// Night skiing special events (one-off boosts)
const NIGHT_SKIING_EVENTS = [
    'fireworks'         => [
        'cost'               => 5000,
        'visitor_bonus_pct'  => 50,
        'revenue_multiplier' => 1.50,
        'rep_bonus'          => 3,
    ],
    'concert'           => [
        'cost'               => 15000,
        'visitor_bonus_pct'  => 80,
        'revenue_multiplier' => 2.00,
        'rep_bonus'          => 5,
    ],
    'night_race'        => [
        'cost'               => 2000,
        'visitor_bonus_pct'  => 25,
        'revenue_multiplier' => 1.25,
        'rep_bonus'          => 2,
    ],
    // Legacy support
    'dj_night'          => [
        'cost'               => 800,
        'visitor_bonus_pct'  => 30,
        'revenue_multiplier' => 1.35,
        'rep_bonus'          => 2,
    ],
    'race_night'        => [ // Old key for night race
        'cost'               => 1200,
        'visitor_bonus_pct'  => 20,
        'revenue_multiplier' => 1.20,
        'rep_bonus'          => 3,
    ],
    'torchlight_parade' => [
        'cost'               => 500,
        'visitor_bonus_pct'  => 40,
        'revenue_multiplier' => 1.15,
        'rep_bonus'          => 2,
    ],
];

/*
|--------------------------------------------------------------------------
| Trail Snowmaking Equipment
|--------------------------------------------------------------------------
*/
const SNOWMAKING_EQUIPMENT = [
    'lance_gun' => [
        'name'          => 'Lance Gun',
        'cost'          => 5000,
        'daily_cost'    => 50,
        'water_usage'   => 10, // per night
        'snow_output'   => 5,  // cm per night
        'min_temp'      => -2, // works below -2°C
        'description'   => 'Standard lance gun. Good output, requires cold temps.',
    ],
    'fan_gun' => [
        'name'          => 'Fan Gun',
        'cost'          => 12000,
        'daily_cost'    => 120,
        'water_usage'   => 20,
        'snow_output'   => 12,
        'min_temp'      => 0,  // works below 0°C
        'description'   => 'High-output fan gun. Works in marginal temps.',
    ],
    'snow_factory' => [
        'name'          => 'Snow Factory',
        'cost'          => 45000,
        'daily_cost'    => 350,
        'water_usage'   => 40,
        'snow_output'   => 25,
        'min_temp'      => 15, // works up to +15°C
        'description'   => 'All-weather snow factory. Produces snow even above freezing.',
    ],
];

/*
|--------------------------------------------------------------------------
| Guest Skill Progression
|--------------------------------------------------------------------------
*/
// Fraction of beginner guests that graduate to intermediate each season
const GUEST_SKILL_BEGINNER_TO_INTERMEDIATE_RATE = 0.20;
// Fraction of intermediate guests that graduate to advanced each season
const GUEST_SKILL_INTERMEDIATE_TO_ADVANCED_RATE = 0.15;
// Revenue multiplier for intermediate guests relative to beginners (1.0 = no bonus)
const GUEST_SKILL_INTERMEDIATE_REVENUE_BONUS = 0.10;
// Revenue multiplier for advanced guests relative to beginners
const GUEST_SKILL_ADVANCED_REVENUE_BONUS = 0.20;

/*
|--------------------------------------------------------------------------
| Visitor Needs System
|--------------------------------------------------------------------------
| Four need scores (0–100) are computed each night for every resort.
|
| VISITOR_NEEDS_HUNGER_PER_RESTAURANT : score points added per restaurant building
| VISITOR_NEEDS_FATIGUE_PER_MEDICAL   : score points added per medical building
| VISITOR_NEEDS_FATIGUE_PER_HOTEL     : score points added per hotel building
| VISITOR_NEEDS_WARMTH_COLD_PENALTY   : score deducted per °C below 0
| VISITOR_NEEDS_WARMTH_PER_LUXURY     : score points added per luxury building
| VISITOR_NEEDS_FUN_PER_LEISURE       : score points added per leisure building
| VISITOR_NEEDS_FUN_PER_OPEN_SLOPE    : score points added per open slope
| VISITOR_NEEDS_REVENUE_BONUS_MAX     : maximum revenue multiplier from satisfied needs
*/
const VISITOR_NEEDS_HUNGER_PER_RESTAURANT = 10;
const VISITOR_NEEDS_FATIGUE_PER_MEDICAL   = 10;
const VISITOR_NEEDS_FATIGUE_PER_HOTEL     = 5;
const VISITOR_NEEDS_WARMTH_COLD_PENALTY   = 3;
const VISITOR_NEEDS_WARMTH_PER_LUXURY     = 8;
const VISITOR_NEEDS_FUN_PER_LEISURE       = 10;
const VISITOR_NEEDS_FUN_PER_OPEN_SLOPE    = 5;
const VISITOR_NEEDS_REVENUE_BONUS_MAX     = 0.20;
// ---------------------------------------------------------------------------
// Power & Energy System
// ---------------------------------------------------------------------------
// Daily electricity consumption (kWh) per item when active
const ENERGY_LIFT_KWH_PER_DAY        = 50;    // kWh per open lift per day
const ENERGY_CANNON_KWH_PER_DAY      = 30;    // kWh per active snow cannon per day

// Grid electricity price (always available as fallback)
const ENERGY_GRID_COST_PER_KWH       = 0.15;  // €/kWh when buying from the grid

// Solar panels
const ENERGY_SOLAR_KWH_PER_PANEL     = 20;    // kWh produced per solar panel per day
const ENERGY_SOLAR_PANEL_COST        = 100000; // € per solar panel unit (purchase price)
const ENERGY_SOLAR_PANEL_MAX         = 20;    // maximum solar panels a resort can install

// Hydro plant
const ENERGY_HYDRO_KWH_PER_DAY       = 500;   // kWh produced per day from hydro plant
const ENERGY_HYDRO_PLANT_COST        = 5000000; // € to build the hydro plant
// Lift Line Management
const LIFT_LINE_DEFAULT_TOLERANCE     = 20;    // Default queue tolerance in minutes
const LIFT_LINE_MIN_TOLERANCE         = 5;     // Minimum allowed queue tolerance (minutes)
const LIFT_LINE_MAX_TOLERANCE         = 60;    // Maximum allowed queue tolerance (minutes)
const LIFT_LINE_REP_PENALTY_PER_MIN   = 0.5;   // Reputation lost per minute the queue exceeds tolerance
const LIFT_LINE_MAX_REP_PENALTY       = 20;    // Maximum daily reputation penalty from long lift queues
const LIFT_LINE_BREAKDOWN_CHANCE      = 10;    // % chance a lift breaks down (forced maintenance) when overloaded
const LIFT_LINE_OVERLOAD_RATIO        = 1.2;   // Visitors-to-capacity ratio above which a lift is "overloaded"
const LIFT_LINE_VIP_BYPASS_RATIO      = 0.20;  // Fraction of visitors that are VIP (bypass the regular queue)
const LIFT_LINE_VIP_REP_REDUCTION     = 0.50;  // When VIP fast pass is on, penalty reduced by this fraction
const LIFT_LINE_VIP_MIN_PRICE         = 10;    // Minimum daily VIP fast pass price (€)
const LIFT_LINE_VIP_MAX_PRICE         = 200;   // Maximum daily VIP fast pass price (€)
const LIFT_LINE_VIP_DEFAULT_PRICE     = 30;    // Default daily VIP fast pass price (€)

// VIP & Loyalty Programs
const VIP_LOYALTY_DISCOUNT_MIN          = 5;     // Minimum loyalty discount (%)
const VIP_LOYALTY_DISCOUNT_MAX          = 25;    // Maximum loyalty discount (%)
const VIP_LOYALTY_DISCOUNT_DEFAULT      = 10;    // Default loyalty discount (%)
const VIP_LOYALTY_VISITOR_PCT           = 0.08;  // Fraction of visitors treated as loyal (get discount)
const VIP_LOYALTY_AVG_PASS_PRICE        = 50;    // Average ski-pass price used to compute discount cost (€)
const VIP_LOYALTY_REP_BONUS             = 2;     // Nightly reputation gain when loyalty program is active
const VIP_PRIVATE_LIFT_COST             = 500;   // Nightly operating cost for private lift service (€)
const VIP_PRIVATE_LIFT_REP_BONUS        = 3;     // Nightly reputation gain from private lift service
const VIP_PRIVATE_LIFT_GUEST_RATIO      = 0.03;  // Fraction of daily visitors using the private lift
const VIP_PRIVATE_LIFT_REVENUE_PER_VISITOR = 80; // Premium revenue per private-lift guest (€)
const VIP_PREMIUM_SLOPES_COST           = 300;   // Nightly operating cost for premium slope access (€)
const VIP_PREMIUM_SLOPES_REP_BONUS      = 2;     // Nightly reputation gain from premium slopes
const VIP_CONCIERGE_COST                = 800;   // Nightly operating cost for concierge service (€)
const VIP_CONCIERGE_REP_BONUS           = 5;     // Nightly reputation gain from concierge service
const VIP_CONCIERGE_GUEST_RATIO         = 0.05;  // Fraction of daily visitors using concierge services
const VIP_CONCIERGE_REVENUE_PER_VISITOR = 30;    // Revenue per concierge guest (€)
// VIP airport transfer service
const VIP_AIRPORT_TRANSFER_COST             = 400;   // Nightly operating cost for airport transfer (€)
const VIP_AIRPORT_TRANSFER_REP_BONUS        = 3;     // Nightly reputation gain from airport transfer
const VIP_AIRPORT_TRANSFER_GUEST_RATIO      = 0.04;  // Fraction of daily visitors using the transfer
const VIP_AIRPORT_TRANSFER_REVENUE_PER_VISITOR = 50; // Premium revenue per transfer guest (€)
// VIP après-ski lounge
const VIP_APRESKI_LOUNGE_COST               = 600;   // Nightly operating cost for après-ski lounge (€)
const VIP_APRESKI_LOUNGE_REP_BONUS          = 4;     // Nightly reputation gain from the lounge
const VIP_APRESKI_LOUNGE_GUEST_RATIO        = 0.06;  // Fraction of daily visitors enjoying the lounge
const VIP_APRESKI_LOUNGE_REVENUE_PER_VISITOR = 40;  // Revenue per lounge guest (€)
// ============================================================
// Accessibility & Transportation
// ============================================================
// Shuttle service levels (0 = none, 1 = basic bus, 2 = tram, 3 = premium shuttle)
const TRANSPORT_SHUTTLE_MAX_LEVEL       = 3;    // Maximum shuttle level
const TRANSPORT_SHUTTLE_DAILY_COST      = [0 => 0, 1 => 200, 2 => 500, 3 => 900]; // € daily operating cost per level
const TRANSPORT_SHUTTLE_FAMILY_REP      = [0 => 0, 1 => 2,   2 => 4,   3 => 7];   // Reputation gained from families per level/night
const TRANSPORT_SHUTTLE_PRO_REP         = [0 => 0, 1 => 0,   2 => 1,   3 => 2];   // Reputation gained from pros per level/night
// Ski storage
const TRANSPORT_SKI_STORAGE_DAILY_COST  = 100;  // € daily cost when ski storage is enabled
const TRANSPORT_SKI_STORAGE_FAMILY_REP  = 3;    // Reputation bonus per night for families when ski storage enabled
// Gondola link between sections
const TRANSPORT_GONDOLA_DAILY_COST      = 400;  // € daily cost when gondola link is enabled
const TRANSPORT_GONDOLA_PRO_REP         = 4;    // Reputation bonus per night for pros when gondola link enabled
const TRANSPORT_GONDOLA_FAMILY_REP      = 2;    // Reputation bonus per night for families when gondola link enabled
// Visitor bonus: good transport infrastructure attracts more visitors (multiplier on daily count)
const TRANSPORT_VISITOR_BONUS_PER_LEVEL = 0.02; // +2% visitors per shuttle level
// Accommodation Upgrades
// ============================================================
// Players can select one accommodation tier for their resort.
// Each night the maintenance cost is deducted and a reputation
// bonus is awarded if the accommodation is enabled.
//
// Keys: accommodation_type (matches ENUM in game_resort_accommodations)
//   nightly_cost      : maintenance fee deducted every night (€)
//   reputation_bonus  : reputation points added every night
//   visitor_bonus_pct : fraction of additional daily visitors attracted (0.0–1.0)
//   upgrade_cost      : one-time cost to activate/upgrade to this tier (€)
const ACCOMMODATION_TYPES = [
    'cabin'         => ['nightly_cost' => 200,  'reputation_bonus' => 2, 'visitor_bonus_pct' => 0.02, 'upgrade_cost' => 50000],
    'lodge'         => ['nightly_cost' => 600,  'reputation_bonus' => 5, 'visitor_bonus_pct' => 0.05, 'upgrade_cost' => 150000],
    'luxury_hotel'  => ['nightly_cost' => 1500, 'reputation_bonus' => 10,'visitor_bonus_pct' => 0.10, 'upgrade_cost' => 500000],
];
// Crowding System
const CROWDING_DEFAULT_CAPACITY_LIMIT     = 500;  // Default daily visitor capacity limit
const CROWDING_MIN_CAPACITY               = 100;  // Minimum allowed daily capacity limit
const CROWDING_MAX_CAPACITY               = 10000; // Maximum allowed daily capacity limit
const CROWDING_DEFAULT_ALERT_THRESHOLD    = 80;   // Default % of capacity at which crowding becomes a problem
const CROWDING_MIN_ALERT_THRESHOLD        = 50;   // Minimum allowed alert threshold (%)
const CROWDING_MAX_ALERT_THRESHOLD        = 95;   // Maximum allowed alert threshold (%)
const CROWDING_REP_PENALTY_PER_PCT        = 0.3;  // Reputation lost per % of visitors over the alert threshold
const CROWDING_MAX_REP_PENALTY            = 25;   // Maximum daily reputation penalty from overcrowding
const CROWDING_TIMED_ENTRY_REP_REDUCTION  = 0.50; // When timed entry is on, penalty reduced by this fraction
const CROWDING_TIMED_ENTRY_REP_BONUS      = 3;    // Reputation bonus when timed entry prevents overcrowding

// Staff Morale constants
const MORALE_DEFAULT            = 75;   // Default morale when a staff member is hired
const MORALE_STRIKE_THRESHOLD   = 30;   // Morale at or below this value triggers a strike
const MORALE_LOW_THRESHOLD      = 50;   // Morale at or below this reduces efficiency/raises accidents
const MORALE_HIGH_THRESHOLD     = 70;   // Morale above this is considered "good"
const MORALE_MAX                = 100;
const MORALE_MIN                = 0;
// Morale adjustment factors (applied daily)
const MORALE_WEATHER_STORM      = -15;  // Storm / Blizzard
const MORALE_WEATHER_BAD        = -10;  // Raining
const MORALE_WEATHER_CLOUDY     = -5;   // Fog / Windy
const MORALE_WEATHER_NEUTRAL    = 0;    // Overcast / Cloudy
const MORALE_WEATHER_GOOD       = 5;    // Snowing
const MORALE_WEATHER_SUNNY      = 10;   // Sunny
const MORALE_PAY_HIGH           = 5;    // Monthly salary >= 3000
const MORALE_PAY_MED            = 2;    // Monthly salary >= 1500
const MORALE_PAY_LOW            = -5;   // Monthly salary < 1000
const MORALE_ASSIGNED           = 3;    // Staff is assigned to an item (feels useful)
const MORALE_UNASSIGNED         = -3;   // Staff is not assigned to any item
const MORALE_DAILY_RECOVERY     = 2;    // Morale drifts toward MORALE_DEFAULT each day

// ---------------------------------------------------------------------------
// Staff Candidate Pool & Career Progression
// ---------------------------------------------------------------------------
const CANDIDATE_POOL_SIZE       = 4;    // Candidates shown per position type
const CANDIDATE_REFRESH_COST    = 500;  // Cost (€) to refresh the candidate pool for one position
const CANDIDATE_EXPIRY_DAYS     = 7;    // Candidates expire after N days if not hired
const STAFF_MAX_SKILL_LEVEL     = 5;    // Maximum staff skill/career level (1-5)
const STAFF_XP_PER_LEVEL        = 500;  // Experience points needed to advance one skill level
const STAFF_SKILL_EFFICIENCY_BONUS = 5; // Efficiency bonus (%) per skill level above 1
const STAFF_TRAINING_COST           = 2000; // € cost per manual training session
const STAFF_TRAINING_XP             = 150;  // XP awarded per training session
const STAFF_TRAINING_COOLDOWN_HOURS = 24;   // Minimum hours between training sessions per staff member
// Daily on-the-job XP: awarded each night to staff assigned to an item
const STAFF_DAILY_XP                = 5;    // XP awarded per night when a staff member is assigned and working
// Trait effects on XP and morale
const STAFF_HARDWORKING_XP_MULT     = 1.5;  // XP multiplier for 'hardworking' trait (50 % more XP each day)
const STAFF_EASYGOING_RECOVERY_BONUS = 2;   // Extra morale recovery per night for 'easygoing' trait
const STAFF_SENSITIVE_WEATHER_MULT  = 1.5;  // Morale weather penalty multiplier for 'sensitive' trait (1.5× worse in bad weather)
const STAFF_AMBITIOUS_LEVELUP_MORALE = 15;  // Morale bonus when a staff member levels up ('ambitious' trait)
// Specialization effects on morale
const STAFF_ENDURANCE_WEATHER_FACTOR = 0.5; // Bad-weather morale delta multiplier for 'endurance' specialization (halved)
// Contract durations available (months)
const CONTRACT_SHORT            = 3;
const CONTRACT_MEDIUM           = 6;
const CONTRACT_LONG             = 12;
// Firing penalty multiplier (months of salary)
const FIRING_PENALTY_SHORT      = 1;    // 1 month if contract <= 3 months
const FIRING_PENALTY_MEDIUM     = 2;    // 2 months if contract <= 6 months
const FIRING_PENALTY_LONG       = 3;    // 3 months if contract > 6 months

// ---------------------------------------------------------------------------
// Realistic Snowmaking requirements
// ---------------------------------------------------------------------------
// Water reservoir: stored as a percentage (0-100) in game_resorts.water_reservoir
// Cannons and trail guns consume water each night; precipitation refills it.
// If the reservoir is empty (0), no artificial snow is produced that night.
// Players must purchase a water reservoir (one-time cost) before any snowmaking works.
const WATER_RESERVOIR_COST               = 150000; // one-time purchase price (€)
const SNOWMAKING_WATER_PER_CANNON_NIGHT  = 5;   // % of reservoir depleted per active building cannon per night
const SNOWMAKING_WATER_PER_TRAIL_NIGHT   = 2;   // % of reservoir depleted per active trail snowmaking unit per night
const SNOWMAKING_WATER_REFILL_SNOW       = 25;  // % refilled per snowing night
const SNOWMAKING_WATER_REFILL_RAIN       = 10;  // % refilled per rainy night

// Electricity: deducted each night when snowmaking is active
const SNOWMAKING_ELECTRICITY_PER_CANNON  = 200; // € per active building cannon per night
const SNOWMAKING_ELECTRICITY_PER_TRAIL   = 50;  // € per active trail snowmaking unit per night

// Staff: minimum number of snowmaking operators required for production
// Checked against hired staff of position 'snowmaker'
const SNOWMAKING_MIN_STAFF = 1;

// Snowmaking operating modes: output and cost multipliers applied to cannons each night
// 'normal' is the default; 'eco' saves costs at lower output; 'boost' maximises output at higher cost
const SNOWMAKING_MODES = ['normal', 'eco', 'boost'];
const SNOWMAKING_MODE_ECO_OUTPUT    = 0.70;  // 70 % output in eco mode
const SNOWMAKING_MODE_ECO_COST      = 0.70;  // 70 % cost in eco mode
const SNOWMAKING_MODE_BOOST_OUTPUT  = 1.40;  // 140 % output in boost mode
const SNOWMAKING_MODE_BOOST_COST    = 1.60;  // 160 % cost in boost mode

// Municipal water emergency refill: allows resorts to top up the reservoir instantly at high cost.
// Unlocked at Resort Level 3+ (3 or more open lifts).
// Only available when reservoir is below MUNICIPAL_WATER_MAX_RESERVOIR_PCT %.
// Applies an eco reputation penalty and a slight resort reputation penalty.
const MUNICIPAL_WATER_REFILL_COST         = 25000; // € per use (approx. 2–3× more than natural refill value)
const MUNICIPAL_WATER_REFILL_AMOUNT       = 20;    // % added to reservoir per use
const MUNICIPAL_WATER_MAX_RESERVOIR_PCT   = 20;    // only available when reservoir < this %
const MUNICIPAL_WATER_UNLOCK_LIFTS        = 3;     // minimum number of open lifts (Resort Level 3+)
const MUNICIPAL_WATER_ECO_PENALTY         = 5;     // eco reputation deducted per use
const MUNICIPAL_WATER_REP_PENALTY         = 2;     // resort reputation deducted per use

// Lift Technology Research Tree
// Each entry: name, description (en/fr), research cost (€), duration in days, prerequisite tech_key or null
const LIFT_TECH_TREE = [
    'loading_carpet' => [
        'name_english'        => 'Loading Carpets',
        'name_french'         => 'Tapis roulants d\'embarquement',
        'description_english' => 'Conveyor belt carpets at lift boarding zones ease the loading process for beginners and reduce queue congestion, improving overall lift throughput by 5%.',
        'description_french'  => 'Des tapis roulants aux zones d\'embarquement facilitent la montée pour les débutants et réduisent la congestion des files, améliorant le débit des remontées de 5 %.',
        'cost'                => 25000,
        'duration_days'       => 5,
        'prerequisite'        => null,
    ],
    'faster_loading' => [
        'name_english'        => 'Faster Chair Loading',
        'name_french'         => 'Embarquement accéléré',
        'description_english' => 'Advanced loading systems and staff training reduce boarding time, increasing lift throughput by 10%.',
        'description_french'  => 'Des systèmes d\'embarquement avancés et une formation du personnel réduisent le temps de montée, augmentant le débit des remontées de 10 %.',
        'cost'                => 50000,
        'duration_days'       => 7,
        'prerequisite'        => 'loading_carpet',
    ],
    'heated_seats' => [
        'name_english'        => 'Heated Seats',
        'name_french'         => 'Sièges chauffants',
        'description_english' => 'Electrically heated seats improve rider comfort and satisfaction, boosting visitor happiness.',
        'description_french'  => 'Des sièges chauffants électriquement améliorent le confort et la satisfaction des skieurs.',
        'cost'                => 75000,
        'duration_days'       => 10,
        'prerequisite'        => 'faster_loading',
    ],
    'bubble_covers' => [
        'name_english'        => 'Bubble Covers',
        'name_french'         => 'Bulles de protection',
        'description_english' => 'Protective bubble covers shield riders from wind and cold, increasing lift usage even in harsh weather.',
        'description_french'  => 'Des bulles protectrices protègent les skieurs du vent et du froid, augmentant l\'utilisation des remontées même par mauvais temps.',
        'cost'                => 100000,
        'duration_days'       => 14,
        'prerequisite'        => 'heated_seats',
    ],
    'ai_maintenance' => [
        'name_english'        => 'AI Maintenance System',
        'name_french'         => 'Système de maintenance IA',
        'description_english' => 'Artificial intelligence monitors lift components and schedules preventive maintenance, reducing maintenance costs by 15%.',
        'description_french'  => 'Une intelligence artificielle surveille les composants des remontées et planifie la maintenance préventive, réduisant les coûts de 15 %.',
        'cost'                => 120000,
        'duration_days'       => 21,
        'prerequisite'        => 'bubble_covers',
    ],
    'smart_snowmaking' => [
        'name_english'        => 'Smart Snowmaking',
        'name_french'         => 'Enneigement intelligent',
        'description_english' => 'AI-driven snow production optimises water and energy use, improving snowmaking output by 20%.',
        'description_french'  => 'La production de neige pilotée par IA optimise l\'utilisation de l\'eau et de l\'énergie, améliorant la production de neige de 20 %.',
        'cost'                => 150000,
        'duration_days'       => 30,
        'prerequisite'        => 'ai_maintenance',
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Lift Technology Generations
// Each entry: name (en/fr), cost (€), capacity (riders/hour), maintenance_rate (€/day), breakdown_risk (%)
// ─────────────────────────────────────────────────────────────────────────────
const LIFT_GENERATIONS = [
    'fixed_grip' => [
        'name_english'        => 'Old Fixed Grip',
        'name_french'         => 'Ancien télésiège fixe',
        'cost'                => 80000,
        'capacity'            => 800,
        'maintenance_rate'    => 200,
        'breakdown_risk'      => 12,
    ],
    'high_speed_detachable' => [
        'name_english'        => 'High-Speed Detachable',
        'name_french'         => 'Télécabine débrayable haute vitesse',
        'cost'                => 350000,
        'capacity'            => 2400,
        'maintenance_rate'    => 500,
        'breakdown_risk'      => 5,
    ],
    'gondola' => [
        'name_english'        => 'Gondola',
        'name_french'         => 'Télécabine',
        'cost'                => 500000,
        'capacity'            => 3000,
        'maintenance_rate'    => 650,
        'breakdown_risk'      => 4,
    ],
    '3s_cable' => [
        'name_english'        => '3S Cable System',
        'name_french'         => 'Système câble 3S',
        'cost'                => 900000,
        'capacity'            => 4000,
        'maintenance_rate'    => 900,
        'breakdown_risk'      => 2,
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Slope Upgrade Tree
// ─────────────────────────────────────────────────────────────────────────────
const SLOPE_UPGRADE_TREE = [
    'enhanced_grooming' => [
        'name_english'        => 'Enhanced Grooming',
        'name_french'         => 'Damage amélioré',
        'description_english' => 'Invest in advanced grooming protocols and schedule optimisation to deliver consistently perfect piste surfaces, improving guest satisfaction.',
        'description_french'  => 'Investissez dans des protocoles de dameuse avancés et l\'optimisation des horaires pour offrir des pistes parfaites, améliorant la satisfaction des clients.',
        'cost'                => 30000,
        'duration_days'       => 5,
        'prerequisite'        => null,
    ],
    'safety_netting' => [
        'name_english'        => 'Safety Netting System',
        'name_french'         => 'Système de filets de sécurité',
        'description_english' => 'Install high-visibility safety nets and edge barriers along all runs, reducing ski-patrol incidents and improving resort reputation.',
        'description_french'  => 'Installez des filets de sécurité haute visibilité et des barrières latérales sur toutes les pistes, réduisant les incidents et améliorant la réputation de la station.',
        'cost'                => 50000,
        'duration_days'       => 7,
        'prerequisite'        => 'enhanced_grooming',
    ],
    'terrain_improvement' => [
        'name_english'        => 'Terrain Improvement',
        'name_french'         => 'Amélioration du terrain',
        'description_english' => 'Re-grade and shape problem sections of runs to reduce ice patches and improve drainage, delivering smoother and safer slopes.',
        'description_french'  => 'Reconfigurez les sections problématiques des pistes pour réduire les plaques de glace et améliorer le drainage, offrant des pentes plus douces et plus sûres.',
        'cost'                => 80000,
        'duration_days'       => 10,
        'prerequisite'        => 'safety_netting',
    ],
    'advanced_piste_marking' => [
        'name_english'        => 'Advanced Piste Marking',
        'name_french'         => 'Balisage de piste avancé',
        'description_english' => 'Deploy LED-lit poles, QR-code information boards and colour-coded difficulty systems across all trails, boosting beginner confidence and resort prestige.',
        'description_french'  => 'Déployez des poteaux à LED, des panneaux QR et des systèmes de couleur de difficulté sur toutes les pistes, renforçant la confiance des débutants et le prestige de la station.',
        'cost'                => 60000,
        'duration_days'       => 7,
        'prerequisite'        => 'terrain_improvement',
    ],
    'premium_slope_surface' => [
        'name_english'        => 'Premium Slope Surface',
        'name_french'         => 'Surface de piste premium',
        'description_english' => 'Apply specially engineered base layer treatments and anti-ice compounds to high-traffic runs, dramatically extending the skiing season.',
        'description_french'  => 'Appliquez des traitements de couche de base spéciaux et des composés anti-glace sur les pistes à fort trafic, prolongeant considérablement la saison de ski.',
        'cost'                => 120000,
        'duration_days'       => 14,
        'prerequisite'        => 'advanced_piste_marking',
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Terrain Engineering Tree
// ─────────────────────────────────────────────────────────────────────────────
const TERRAIN_ENGINEERING_TREE = [
    'terrain_park_features' => [
        'name_english'        => 'Terrain Park Features',
        'name_french'         => 'Aménagements du terrain park',
        'description_english' => 'Build dedicated terrain park infrastructure with jumps, rails and boxes, attracting freestyle skiers and snowboarders to your resort.',
        'description_french'  => 'Construisez une infrastructure de terrain park dédiée avec des sauts, des rails et des boxes, attirant les skieurs freestyle et les snowboarders dans votre station.',
        'cost'                => 40000,
        'duration_days'       => 7,
        'prerequisite'        => null,
    ],
    'moguls_section' => [
        'name_english'        => 'Moguls Section',
        'name_french'         => 'Section de bosses',
        'description_english' => 'Designate and groom dedicated mogul fields, providing a challenging and rewarding experience for intermediate and advanced skiers.',
        'description_french'  => 'Aménagez et entretenez des champs de bosses dédiés, offrant une expérience exigeante et gratifiante aux skieurs intermédiaires et avancés.',
        'cost'                => 55000,
        'duration_days'       => 7,
        'prerequisite'        => 'terrain_park_features',
    ],
    'tree_runs' => [
        'name_english'        => 'Tree Runs',
        'name_french'         => 'Couloirs en forêt',
        'description_english' => 'Clear and mark natural gladed tree runs through forested areas, offering off-piste variety while keeping guests within resort boundaries.',
        'description_french'  => 'Défrichissez et balisez des couloirs naturels en forêt, offrant une diversité hors-piste tout en maintenant les skieurs dans les limites de la station.',
        'cost'                => 70000,
        'duration_days'       => 10,
        'prerequisite'        => 'moguls_section',
    ],
    'backcountry_access' => [
        'name_english'        => 'Backcountry Access',
        'name_french'         => 'Accès hors-piste',
        'description_english' => 'Open controlled backcountry gates with avalanche monitoring and patrol coverage, providing high-risk adventure terrain for expert skiers seeking extreme challenges.',
        'description_french'  => 'Ouvrez des portiques hors-piste contrôlés avec surveillance des avalanches et couverture des patrouilleurs, offrant un terrain d\'aventure à haut risque pour les skieurs experts en quête de défis extrêmes.',
        'cost'                => 100000,
        'duration_days'       => 14,
        'prerequisite'        => 'tree_runs',
    ],
    'advanced_slope_program' => [
        'name_english'        => 'Advanced Slope Program',
        'name_french'         => 'Programme pistes avancées',
        'description_english' => 'Develop a comprehensive portfolio of challenging black and double-black runs that attract and retain advanced skiers, boosting high-value guest numbers and resort prestige.',
        'description_french'  => 'Développez un portefeuille complet de pistes noires et rouge-noires exigeantes qui attirent et fidélisent les skieurs avancés, augmentant le nombre de clients à haute valeur et le prestige de la station.',
        'cost'                => 130000,
        'duration_days'       => 21,
        'prerequisite'        => 'backcountry_access',
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Snowmaking Upgrade Tree
// ─────────────────────────────────────────────────────────────────────────────
const SNOWMAKING_UPGRADE_TREE = [
    'water_efficiency' => [
        'name_english'        => 'Water Efficiency System',
        'name_french'         => 'Système d\'efficacité hydrique',
        'description_english' => 'Install variable-flow nozzles and pressure optimisation, reducing water consumption by 15% while maintaining snow output.',
        'description_french'  => 'Installez des buses à débit variable et l\'optimisation de la pression, réduisant la consommation d\'eau de 15 % tout en maintenant la production de neige.',
        'cost'                => 40000,
        'duration_days'       => 5,
        'prerequisite'        => null,
    ],
    'energy_recovery' => [
        'name_english'        => 'Energy Recovery',
        'name_french'         => 'Récupération d\'énergie',
        'description_english' => 'Fit heat exchangers and pump inverters to recover waste energy, cutting snowmaking electricity costs by 10%.',
        'description_french'  => 'Équipez des échangeurs de chaleur et des variateurs de pompe pour récupérer l\'énergie perdue, réduisant les coûts d\'électricité de l\'enneigement de 10 %.',
        'cost'                => 60000,
        'duration_days'       => 7,
        'prerequisite'        => 'water_efficiency',
    ],
    'automated_scheduling' => [
        'name_english'        => 'Automated Scheduling',
        'name_french'         => 'Planification automatisée',
        'description_english' => 'AI-driven scheduling syncs cannon activation with weather windows, maximising output while eliminating operator errors.',
        'description_french'  => 'La planification par IA synchronise l\'activation des canons avec les fenêtres météo, maximisant la production tout en éliminant les erreurs d\'opérateur.',
        'cost'                => 90000,
        'duration_days'       => 10,
        'prerequisite'        => 'energy_recovery',
    ],
    'high_altitude_guns' => [
        'name_english'        => 'High-Altitude Guns',
        'name_french'         => 'Canons haute altitude',
        'description_english' => 'Specialised compressor technology enables snow production above 2 500 m where thin air reduces standard cannon performance.',
        'description_french'  => 'La technologie de compresseur spécialisée permet la production de neige au-dessus de 2 500 m où l\'air raréfié réduit les performances des canons standard.',
        'cost'                => 110000,
        'duration_days'       => 14,
        'prerequisite'        => 'automated_scheduling',
    ],
    'underground_pipeline' => [
        'name_english'        => 'Underground Pipeline Network',
        'name_french'         => 'Réseau de canalisations enterrées',
        'description_english' => 'Replace surface hoses with a buried, insulated pipeline network, reducing heat loss and drastically cutting maintenance time.',
        'description_french'  => 'Remplacez les tuyaux de surface par un réseau de canalisations enterrées et isolées, réduisant les pertes de chaleur et diminuant considérablement le temps de maintenance.',
        'cost'                => 150000,
        'duration_days'       => 21,
        'prerequisite'        => 'high_altitude_guns',
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Marketing Upgrade Tree
// ─────────────────────────────────────────────────────────────────────────────
const MARKETING_UPGRADE_TREE = [
    'social_media_strategy' => [
        'name_english'        => 'Social Media Strategy',
        'name_french'         => 'Stratégie de réseaux sociaux',
        'description_english' => 'Develop a consistent social media presence and content calendar, boosting resort visibility and attracting a younger demographic.',
        'description_french'  => 'Développez une présence cohérente sur les réseaux sociaux et un calendrier de contenu, augmentant la visibilité de la station et attirant un public plus jeune.',
        'cost'                => 20000,
        'duration_days'       => 4,
        'prerequisite'        => null,
    ],
    'influencer_program' => [
        'name_english'        => 'Influencer Program',
        'name_french'         => 'Programme d\'influenceurs',
        'description_english' => 'Partner with ski and travel influencers for sponsored content, generating viral reach and increasing bookings from new markets.',
        'description_french'  => 'Associez-vous à des influenceurs ski et voyage pour du contenu sponsorisé, générant une portée virale et augmentant les réservations de nouveaux marchés.',
        'cost'                => 50000,
        'duration_days'       => 7,
        'prerequisite'        => 'social_media_strategy',
    ],
    'loyalty_program_upgrade' => [
        'name_english'        => 'Loyalty Program Upgrade',
        'name_french'         => 'Amélioration du programme de fidélité',
        'description_english' => 'Introduce a tiered rewards programme with exclusive perks for returning guests, increasing repeat visits and average spend.',
        'description_french'  => 'Introduisez un programme de récompenses à plusieurs niveaux avec des avantages exclusifs pour les clients fidèles, augmentant les visites répétées et les dépenses moyennes.',
        'cost'                => 70000,
        'duration_days'       => 10,
        'prerequisite'        => 'influencer_program',
    ],
    'international_advertising' => [
        'name_english'        => 'International Advertising Campaign',
        'name_french'         => 'Campagne publicitaire internationale',
        'description_english' => 'Launch multi-channel ad campaigns in key overseas markets, significantly increasing the share of international visitors.',
        'description_french'  => 'Lancez des campagnes publicitaires multicanaux sur les marchés étrangers clés, augmentant considérablement la part des visiteurs internationaux.',
        'cost'                => 100000,
        'duration_days'       => 14,
        'prerequisite'        => 'loyalty_program_upgrade',
    ],
    'brand_ambassador' => [
        'name_english'        => 'Brand Ambassador Partnership',
        'name_french'         => 'Partenariat ambassadeur de marque',
        'description_english' => 'Sign a professional skier or snowboarder as a brand ambassador, dramatically elevating resort prestige and global recognition.',
        'description_french'  => 'Signez un skieur ou snowboardeur professionnel comme ambassadeur de marque, élevant considérablement le prestige de la station et la reconnaissance mondiale.',
        'cost'                => 150000,
        'duration_days'       => 21,
        'prerequisite'        => 'international_advertising',
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Staff Upgrade Tree
// ─────────────────────────────────────────────────────────────────────────────
const STAFF_UPGRADE_TREE = [
    'staff_training_center' => [
        'name_english'        => 'Staff Training Center',
        'name_french'         => 'Centre de formation du personnel',
        'description_english' => 'Build a dedicated on-site training facility that continuously improves staff skill levels and reduces onboarding time for new hires.',
        'description_french'  => 'Construisez une installation de formation dédiée sur site qui améliore continuellement les compétences du personnel et réduit le temps d\'intégration des nouvelles recrues.',
        'cost'                => 40000,
        'duration_days'       => 7,
        'prerequisite'        => null,
    ],
    'performance_bonus_scheme' => [
        'name_english'        => 'Performance Bonus Scheme',
        'name_french'         => 'Système de primes de performance',
        'description_english' => 'Introduce a structured bonus programme that rewards high-performing employees, boosting morale and reducing staff turnover.',
        'description_french'  => 'Introduisez un programme de primes structuré qui récompense les employés les plus performants, stimulant le moral et réduisant le roulement du personnel.',
        'cost'                => 30000,
        'duration_days'       => 5,
        'prerequisite'        => 'staff_training_center',
    ],
    'advanced_instructor_cert' => [
        'name_english'        => 'Advanced Instructor Certification',
        'name_french'         => 'Certification avancée pour instructeurs',
        'description_english' => 'Fund industry-recognised certification courses for ski instructors, enabling higher lesson prices and attracting elite students.',
        'description_french'  => 'Financez des cours de certification reconnus par l\'industrie pour les moniteurs de ski, permettant des prix de cours plus élevés et attirant des étudiants d\'élite.',
        'cost'                => 60000,
        'duration_days'       => 10,
        'prerequisite'        => 'performance_bonus_scheme',
    ],
    'staff_accommodation' => [
        'name_english'        => 'Staff Accommodation',
        'name_french'         => 'Logement du personnel',
        'description_english' => 'Provide affordable on-mountain housing for seasonal staff, dramatically reducing turnover and making the resort more attractive to skilled candidates.',
        'description_french'  => 'Fournissez des logements abordables en montagne pour le personnel saisonnier, réduisant considérablement le roulement et rendant la station plus attrayante pour les candidats qualifiés.',
        'cost'                => 90000,
        'duration_days'       => 14,
        'prerequisite'        => 'advanced_instructor_cert',
    ],
    'expert_patrol_team' => [
        'name_english'        => 'Expert Patrol Team',
        'name_french'         => 'Équipe de patrouille experte',
        'description_english' => 'Train and certify a specialist avalanche and rescue patrol team, improving emergency response times and boosting guest confidence.',
        'description_french'  => 'Formez et certifiez une équipe spécialisée en avalanche et sauvetage, améliorant les temps de réponse aux urgences et renforçant la confiance des clients.',
        'cost'                => 120000,
        'duration_days'       => 21,
        'prerequisite'        => 'staff_accommodation',
    ],
];

const PERC_TOURISTS_BUILDING = [
    'tourist_info'     => 1,
    'access'           => 1,
    'cannon'           => 1,
    'day_only'         => 0.60,
    'hotel'            => 0.625,
    'restaurant'       => 0.65,
    'medical'          => 0.02,
    'rental'           => 0.60,
    'leisure'          => 0.30,
    'luxury'           => 0.05,
    'parking'          => 0.80,
    'facility'         => 0.35,
    'housing_complex'  => 1,
    'icerink'          => 1,
    'curling_center'   => 1,
    'open_stage'       => 1,
    // Off-season activities (year-round, independent of ski visitors)
    'mountain_biking'  => 1,
    'hiking'           => 1,
    'festival'         => 1,
    'wedding_venue'    => 1,
    'alpine_coaster'   => 1,
];

// Black Diamond / Extreme Zone difficulty (id_difficulty = 5)
const BLACK_DIAMOND_DIFFICULTY_ID        = 5;      // id_difficulty value for Black Diamond
const BLACK_DIAMOND_INJURY_MULTIPLIER    = 2.0;    // Injury risk multiplied by this factor on Black Diamond slopes
const BLACK_DIAMOND_REPUTATION_PER_SLOPE = 3;      // Nightly reputation bonus per open Black Diamond slope

// Star Rating System – thresholds (minimum reputation required for each star level)
const STAR_RATING_THRESHOLDS = [
    1 => 0,     // 1 star  : 0 – 99 reputation
    2 => 100,   // 2 stars : 100 – 249 reputation
    3 => 250,   // 3 stars : 250 – 499 reputation
    4 => 500,   // 4 stars : 500 – 999 reputation
    5 => 1000,  // 5 stars : 1 000+ reputation
];
const BLACK_DIAMOND_VISITOR_BONUS        = 0.15;   // 15% additional visitor attraction for expert guests
// Real Estate Development
const REAL_ESTATE_TYPES = [
    'ski_in_ski_out' => [
        'build_cost'      => 2000000,   // €2M build cost
        'build_time'      => 30,        // days to build
        'sale_price'      => 3500000,   // one-time sale revenue
        'daily_rent'      => 8000,      // daily rent income if kept
        'property_tax'    => 0.10,      // 10% tax on rent
    ],
    'luxury_chalet' => [
        'build_cost'      => 1200000,   // €1.2M build cost
        'build_time'      => 20,        // days to build
        'sale_price'      => 2000000,   // one-time sale revenue
        'daily_rent'      => 4500,      // daily rent income if kept
        'property_tax'    => 0.10,      // 10% tax on rent
    ],
    'condo' => [
        'build_cost'      => 500000,    // €500k build cost
        'build_time'      => 14,        // days to build
        'sale_price'      => 800000,    // one-time sale revenue
        'daily_rent'      => 1800,      // daily rent income if kept
        'property_tax'    => 0.10,      // 10% tax on rent
    ],
];
const REAL_ESTATE_STATUSES = [1 => 'renting', 2 => 'for_sale', 3 => 'sold', 4 => 'under_construction'];

// ---------------------------------------------------------------------------
// Mountain Master Plan System
// ---------------------------------------------------------------------------
const MASTER_PLAN_SUBMISSION_COST        = 50000;  // Cost (€) to submit a plan for government review
const MASTER_PLAN_REVISION_COST          = 200000; // Cost (€) to revise an approved/active plan mid-way
const MASTER_PLAN_REVISION_REP_PENALTY   = 10;     // Reputation points lost when revising an active plan
const MASTER_PLAN_APPROVAL_DAYS          = 3;      // Game-days before a submitted plan is auto-approved
const MASTER_PLAN_MAX_SLOPES             = 20;     // Maximum zoning limit for slopes
const MASTER_PLAN_MAX_LIFTS              = 15;     // Maximum zoning limit for lifts
const MASTER_PLAN_MAX_BUILDINGS          = 30;     // Maximum zoning limit for buildings
const MASTER_PLAN_DURATION_DAYS          = 1825; // Game-days an active plan remains valid before expiring (~5 years)
const MASTER_PLAN_VALID_STATUSES         = ['draft', 'submitted', 'approved', 'active', 'expired'];

// ---------------------------------------------------------------------------
// Ski Resort Quiz – secret access code
// ---------------------------------------------------------------------------
const SKI_QUIZ_SECRET_CODE = 'SKI_QUIZ_SECRET_CODE'; // Secret code required to access the ski resort quiz

// Crisis events configuration
const CRISIS_EVENT_PROBABILITY         = 5;    // Percentage chance (1-100) that a crisis event occurs each night per resort
const CRISIS_LIFT_FAILURE_REP_PENALTY  = 10;   // Reputation points lost on a major lift failure
const CRISIS_AVALANCHE_REP_PENALTY     = 15;   // Reputation points lost on an avalanche incident
const CRISIS_AVALANCHE_SNOW_LOSS       = 20;   // Snow level reduction (cm) from an avalanche
const CRISIS_POWER_OUTAGE_REP_PENALTY  = 5;    // Reputation points lost on a power outage
const CRISIS_POWER_OUTAGE_COST_PERC   = 0.15;  // Fraction of resort cash deducted as emergency cost
const CRISIS_POWER_OUTAGE_MIN_COST    = 500;   // Minimum emergency cost (€) regardless of cash level
const CRISIS_VIRAL_NEGATIVE_REP_PENALTY = 20;  // Reputation points lost on a viral negative media story
// ============================================================
// Micro-Events (Quick Decisions)
// ============================================================
// Percentage chance (1-100) that a micro-event is generated for each open resort per night
const MICRO_EVENT_PROBABILITY = 30;
// Expiry window in hours: pending micro-events expire after this many hours
const MICRO_EVENT_EXPIRY_HOURS = 24;
// Cash and reputation consequences for each event type and choice
// vip_queue_jump: A VIP guest wants to jump the lift queue
const MICRO_VIP_ACCEPT_CASH     =  800;  // € gained (guest pays extra)
const MICRO_VIP_ACCEPT_REP      =   -3;  // reputation lost (other guests unhappy)
const MICRO_VIP_DECLINE_CASH    =    0;
const MICRO_VIP_DECLINE_REP     =    4;  // reputation gained (fair treatment)
// press_interview: A journalist requests a resort interview
const MICRO_PRESS_ACCEPT_CASH   = -500;  // € spent (hosting / prep costs)
const MICRO_PRESS_ACCEPT_REP    =    8;  // reputation gained
const MICRO_PRESS_DECLINE_CASH  =    0;
const MICRO_PRESS_DECLINE_REP   =   -2;  // small reputation loss (journalist disappointed)
// equipment_deal: Supplier offers a last-minute discount
const MICRO_EQUIP_ACCEPT_CASH   = -400;  // € spent on discounted equipment
const MICRO_EQUIP_ACCEPT_REP    =    2;  // minor reputation gain (improved gear)
const MICRO_EQUIP_DECLINE_CASH  =    0;
const MICRO_EQUIP_DECLINE_REP   =    0;
// lost_skier: A skier has not returned from the mountain
const MICRO_SKIER_PATROL_CASH   = -300;  // € spent deploying patrol
const MICRO_SKIER_PATROL_REP    =    8;  // reputation gained (fast response)
const MICRO_SKIER_WAIT_CASH     =    0;
const MICRO_SKIER_WAIT_REP      =  -12;  // reputation lost (irresponsible)
// ============================================================
// Environmental System
// ============================================================
// Carbon footprint units added per active piece of equipment per night
const ENV_CARBON_PER_LIFT     = 5;   // per open lift
const ENV_CARBON_PER_CANNON   = 8;   // per active snow cannon
const ENV_CARBON_PER_GROOMER  = 10;  // per diesel groomer (non-electric)
const ENV_CARBON_PER_ELECTRIC_GROOMER = 2;  // per electric groomer
const ENV_SOLAR_CARBON_REDUCTION = 0.20;    // 20 % reduction when solar panels installed

// Noise pollution units per active piece of equipment per night
const ENV_NOISE_PER_LIFT      = 3;
const ENV_NOISE_PER_CANNON    = 10;
const ENV_NOISE_PER_GROOMER   = 5;
const ENV_NOISE_PER_ELECTRIC_GROOMER = 1;

// Thresholds
const ENV_CARBON_FINE_THRESHOLD     = 150;  // carbon > this triggers daily fine
const ENV_CARBON_RESTRICT_THRESHOLD = 250;  // carbon > this triggers fine + expansion restriction
const ENV_NOISE_FINE_THRESHOLD      = 80;   // noise > this triggers fine when wildlife zone is active

// Fine amounts (euros)
const ENV_CARBON_FINE_AMOUNT     = 500;
const ENV_CARBON_RESTRICT_FINE   = 1000;
const ENV_NOISE_FINE_AMOUNT      = 300;

// Eco reputation adjustment per night
const ENV_REP_SOLAR_BONUS          =  5;
const ENV_REP_ELECTRIC_GROOMER_BONUS = 2;  // per electric groomer
const ENV_REP_WILDLIFE_BONUS       =  5;
const ENV_REP_HIGH_CARBON_PENALTY  = -5;   // when carbon > ENV_CARBON_FINE_THRESHOLD
const ENV_REP_VERY_HIGH_CARBON_PENALTY = -10; // when carbon > ENV_CARBON_RESTRICT_THRESHOLD
const ENV_REP_HIGH_NOISE_PENALTY   = -5;   // when noise > ENV_NOISE_FINE_THRESHOLD

// Reforestation Program
const ENV_TREE_CARBON_REDUCTION    = 5;    // CO₂e reduced per tree planting per night
const ENV_TREE_REP_BONUS           = 2;    // eco rep bonus per tree owned per night
const ENV_MAX_TREE_COUNT           = 5;    // maximum number of reforestation investments

// Water Recycling System
const ENV_WATER_RECYCLING_NOISE_REDUCTION = 0.30; // 30 % noise reduction on snow cannons
const ENV_WATER_RECYCLING_REP_BONUS       = 3;    // eco rep bonus when water recycling is active

/*
|--------------------------------------------------------------------------
| CLIMATE CHANGE SYSTEM
|--------------------------------------------------------------------------
| climate_level increases by 1 each season (starting from season 3).
| Maximum level is 10.
|
| CLIMATE_SNOW_PENALTY_PER_LEVEL  : cm of natural snowfall reduced per climate level
| CLIMATE_COST_MULT_PER_LEVEL     : additional cost multiplier per level (0.05 = +5%/level)
| CLIMATE_GLACIER_LOSS_PER_LEVEL  : extra slope-condition points lost per day per level
| CLIMATE_SEASON_PENALTY_PER_LEVEL: days removed from the season end trigger per level
| CLIMATE_FIRST_SEASON            : seasons before climate change begins increasing
|
| Adaptation investment costs (€):
| CLIMATE_INVEST_SNOWMAKING : reduces snowmaking_cost_mult penalty by 50%
| CLIMATE_INVEST_ALTITUDE   : reduces winter_snow_penalty by 50%
| CLIMATE_INVEST_DIVERSIFY  : reduces visitor impact of climate effects by 50%
*/
const CLIMATE_SNOW_PENALTY_PER_LEVEL   = 1;     // cm reduced per level
const CLIMATE_COST_MULT_PER_LEVEL      = 0.05;  // +5% snowmaking cost per level
const CLIMATE_GLACIER_LOSS_PER_LEVEL   = 1;     // extra degradation points per level
const CLIMATE_SEASON_PENALTY_PER_LEVEL = 2;     // days shorter per level
const CLIMATE_FIRST_SEASON             = 3;     // climate starts increasing at this season

const CLIMATE_INVEST_SNOWMAKING        = 2000000;  // €2M
const CLIMATE_INVEST_ALTITUDE          = 3000000;  // €3M
const CLIMATE_INVEST_DIVERSIFY         = 2500000;  // €2.5M

/*
|--------------------------------------------------------------------------
| Bank – Investment Account
|--------------------------------------------------------------------------
|
| BANK_INVESTMENT_ANNUAL_RATE   : annual interest rate in percent (e.g. 3.0 = 3 %)
| BANK_INVESTMENT_MIN_DEPOSIT   : minimum single deposit amount in €
| BANK_INVESTMENT_MAX_BALANCE   : maximum total invested balance allowed per resort
*/
const BANK_INVESTMENT_ANNUAL_RATE = 3.0;      // 3 % per year ≈ 0.0082 % per day
const BANK_INVESTMENT_MIN_DEPOSIT = 100000;   // €100 000
const BANK_INVESTMENT_MAX_BALANCE = 50000000; // €50 000 000

/*
|--------------------------------------------------------------------------
| Insurance
|--------------------------------------------------------------------------
|
| Plans: none | basic | premium
|
| INSURANCE_DAILY_PREMIUM_BASIC      : daily premium charged for the basic plan (€)
| INSURANCE_DAILY_PREMIUM_PREMIUM    : daily premium charged for the premium plan (€)
|
| INSURANCE_LIFT_PAYOUT_BASIC        : cash payout per lift-accident claim (basic plan, €)
| INSURANCE_LIFT_PAYOUT_PREMIUM      : cash payout per lift-accident claim (premium plan, €)
|
| INSURANCE_STORM_PAYOUT_PER_LIFT    : cash payout per storm-damaged lift (premium plan only, €)
*/
const INSURANCE_DAILY_PREMIUM_BASIC      = 500;    // €/day – basic plan
const INSURANCE_DAILY_PREMIUM_PREMIUM    = 1500;   // €/day – premium plan
const INSURANCE_LIFT_PAYOUT_BASIC        = 10000;  // € per lift-accident claim (basic)
const INSURANCE_LIFT_PAYOUT_PREMIUM      = 25000;  // € per lift-accident claim (premium)
const INSURANCE_STORM_PAYOUT_PER_LIFT    = 8000;   // € per storm-damaged lift (premium only)

/*
| Experimental Tech & R&D System
|--------------------------------------------------------------------------
|
| Three research projects are available. Each can be started at normal pace
| or rushed (shorter duration, higher failure risk).
|
| bonus_type values:
|   'reputation'  – nightly reputation gain equal to bonus_value
|   'cost_saving' – nightly cash bonus equal to bonus_value (euros)
|
| RD_FAILURE_REP_PENALTY : reputation points lost on a failed experiment
| RD_ACCIDENT_COST       : cash deducted as accident clean-up cost on failure
*/
const RD_PROJECTS = [
    'advanced_lift_motors' => [
        'name_english'        => 'Advanced Lift Motors',
        'name_french'         => 'Moteurs de remontées avancés',
        'description_english' => 'Engineer next-generation lift drive systems for faster, smoother rides. Completed research boosts guest satisfaction, granting +3 reputation each night.',
        'description_french'  => 'Développez des systèmes d\'entraînement de remontées de nouvelle génération. La recherche complétée améliore la satisfaction des visiteurs, accordant +3 de réputation chaque nuit.',
        'cost'                => 200000,
        'rush_cost'           => 300000,
        'duration_days'       => 14,
        'rush_duration_days'  => 7,
        'failure_chance_normal' => 5,   // % chance of failure at completion
        'failure_chance_rush'   => 40,
        'bonus_type'          => 'reputation',
        'bonus_value'         => 3,
    ],
    'snowmaking_efficiency' => [
        'name_english'        => 'Snowmaking Efficiency',
        'name_french'         => 'Efficacité de l\'enneigement',
        'description_english' => 'Develop AI-optimised snow gun nozzles and scheduling algorithms. Reduces daily snow cannon operating costs by 500 € once completed.',
        'description_french'  => 'Développez des buses et des algorithmes d\'ordonnancement optimisés par IA. Réduit les coûts quotidiens d\'exploitation des canons à neige de 500 € une fois terminé.',
        'cost'                => 350000,
        'rush_cost'           => 500000,
        'duration_days'       => 21,
        'rush_duration_days'  => 10,
        'failure_chance_normal' => 10,
        'failure_chance_rush'   => 45,
        'bonus_type'          => 'cost_saving',
        'bonus_value'         => 500,
    ],
    'slope_treatment' => [
        'name_english'        => 'Experimental Slope Treatment',
        'name_french'         => 'Traitement expérimental des pistes',
        'description_english' => 'Test cutting-edge snow-binding compounds and surface treatments. Reduces nightly slope maintenance costs by 300 € when completed.',
        'description_french'  => 'Testez des composés de fixation de neige et des traitements de surface à la pointe de la technologie. Réduit les coûts de maintenance nocturne des pistes de 300 € une fois terminé.',
        'cost'                => 150000,
        'rush_cost'           => 220000,
        'duration_days'       => 10,
        'rush_duration_days'  => 5,
        'failure_chance_normal' => 15,
        'failure_chance_rush'   => 50,
        'bonus_type'          => 'cost_saving',
        'bonus_value'         => 300,
    ],
];
const RD_FAILURE_REP_PENALTY = 10;   // reputation points lost on a failed experiment
const RD_ACCIDENT_COST       = 50000; // cash deducted as accident cost on failure
/*
| Scenic Lifts
|--------------------------------------------------------------------------
|
| SCENIC_LIFT_DEFAULT_TICKET_PRICE    : default sightseeing ticket price (€)
| SCENIC_LIFT_MIN_TICKET_PRICE        : minimum allowed ticket price (€)
| SCENIC_LIFT_MAX_TICKET_PRICE        : maximum allowed ticket price (€)
| SCENIC_LIFT_TOURIST_RATIO           : fraction of daily visitors who buy a scenic ticket
| SCENIC_LIFT_DAILY_COST              : daily operating cost at default capacity level (€)
| SCENIC_LIFT_REP_BONUS_PER_DAY       : reputation gained per day when scenic lift is active
|
| Gondola capacity level (1–5):
| SCENIC_LIFT_MIN_CAPACITY            : minimum gondola capacity level
| SCENIC_LIFT_MAX_CAPACITY            : maximum gondola capacity level
| SCENIC_LIFT_DEFAULT_CAPACITY        : default capacity level (neutral – preserves existing revenue)
| SCENIC_LIFT_CAPACITY_COST_PER_LEVEL : extra daily cost added per level above/below default (€)
|                                       e.g. level 5 adds 2×€40 = €80 extra; level 1 saves €80
|
| Seasonal discount:
| SCENIC_LIFT_DISCOUNT_VISITOR_BOOST  : tourist-count multiplier during off-peak when discount is on
| SCENIC_LIFT_DISCOUNT_PRICE_FACTOR   : effective ticket-price multiplier during off-peak discount
| SCENIC_LIFT_OFFPEAK_THRESHOLD       : bonus_peak_season value below which off-peak rules apply
|
| Tour themes (tour_theme column: 0=standard, 1=nature, 2=sunset, 3=adventure):
| SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST    : visitor-count multiplier for Nature theme
| SCENIC_LIFT_THEME_NATURE_REP_BONUS        : extra rep/day for Nature theme
| SCENIC_LIFT_THEME_NATURE_EXTRA_COST       : extra daily cost for Nature theme (€)
| SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR     : ticket-price multiplier for Sunset theme
| SCENIC_LIFT_THEME_SUNSET_REP_BONUS        : extra rep/day for Sunset theme
| SCENIC_LIFT_THEME_SUNSET_EXTRA_COST       : extra daily cost for Sunset theme (€)
| SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST : visitor-count multiplier for Adventure theme
| SCENIC_LIFT_THEME_ADVENTURE_REP_BONUS     : extra rep/day for Adventure theme
| SCENIC_LIFT_THEME_ADVENTURE_EXTRA_COST    : extra daily cost for Adventure theme (€)
|
| Photography package:
| SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR : extra revenue per scenic visitor when package is active (€)
| SCENIC_LIFT_PHOTO_DAILY_COST          : extra daily operating cost for the photography package (€)
|
| VIP gondola:
| SCENIC_LIFT_VIP_VISITOR_FACTOR    : fraction of normal scenic visitors in VIP mode
| SCENIC_LIFT_VIP_PRICE_MULTIPLIER  : ticket-price multiplier in VIP mode
| SCENIC_LIFT_VIP_REP_BONUS         : extra rep/day in VIP mode
| SCENIC_LIFT_VIP_DAILY_COST        : extra daily operating cost for VIP mode (€)
*/
const SCENIC_LIFT_DEFAULT_TICKET_PRICE    = 20;    // €20
const SCENIC_LIFT_MIN_TICKET_PRICE        = 5;     // €5
const SCENIC_LIFT_MAX_TICKET_PRICE        = 50;    // €50
const SCENIC_LIFT_TOURIST_RATIO           = 0.15;  // 15 % of visitors buy a sightseeing ticket
const SCENIC_LIFT_DAILY_COST              = 200;   // €200 / day (at default capacity level 3)
const SCENIC_LIFT_REP_BONUS_PER_DAY       = 2;     // +2 reputation / day
const SCENIC_LIFT_MIN_CAPACITY            = 1;     // smallest gondola fleet
const SCENIC_LIFT_MAX_CAPACITY            = 5;     // largest gondola fleet
const SCENIC_LIFT_DEFAULT_CAPACITY        = 3;     // neutral – same throughput as original code
const SCENIC_LIFT_CAPACITY_COST_PER_LEVEL = 40;    // €40 extra (or saving) per level vs. default
const SCENIC_LIFT_DISCOUNT_VISITOR_BOOST  = 1.30;  // 30 % more scenic tourists during off-peak
const SCENIC_LIFT_DISCOUNT_PRICE_FACTOR   = 0.75;  // 25 % ticket discount during off-peak
const SCENIC_LIFT_OFFPEAK_THRESHOLD       = 1.0;   // bonus_peak_season below this = off-peak
// Tour themes (stored as tour_theme column value)
const SCENIC_LIFT_THEME_STANDARD          = 0;     // baseline – no modifier
const SCENIC_LIFT_THEME_NATURE            = 1;     // Nature & Wildlife
const SCENIC_LIFT_THEME_SUNSET            = 2;     // Sunset & Starlight
const SCENIC_LIFT_THEME_ADVENTURE         = 3;     // Adventure & Glacier
// Nature theme modifiers
const SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST = 1.10; // +10 % visitors
const SCENIC_LIFT_THEME_NATURE_REP_BONUS     = 1;    // +1 rep/day
const SCENIC_LIFT_THEME_NATURE_EXTRA_COST    = 30;   // €30 extra/day
// Sunset theme modifiers
const SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR  = 1.15; // +15 % effective ticket price
const SCENIC_LIFT_THEME_SUNSET_REP_BONUS     = 2;    // +2 rep/day
const SCENIC_LIFT_THEME_SUNSET_EXTRA_COST    = 50;   // €50 extra/day
// Adventure theme modifiers
const SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST = 1.20; // +20 % visitors
const SCENIC_LIFT_THEME_ADVENTURE_REP_BONUS     = 3;    // +3 rep/day
const SCENIC_LIFT_THEME_ADVENTURE_EXTRA_COST    = 80;   // €80 extra/day
// Photography package
const SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR = 8;    // €8 extra revenue per scenic visitor
const SCENIC_LIFT_PHOTO_DAILY_COST          = 60;   // €60 extra/day operating cost
// VIP gondola
const SCENIC_LIFT_VIP_VISITOR_FACTOR        = 0.60; // 60 % of normal scenic visitor count
const SCENIC_LIFT_VIP_PRICE_MULTIPLIER      = 2.0;  // ×2 ticket price
const SCENIC_LIFT_VIP_REP_BONUS             = 2;    // +2 rep/day
const SCENIC_LIFT_VIP_DAILY_COST            = 100;  // €100 extra/day

/*
|--------------------------------------------------------------------------
| Emergency & Rescue System
|--------------------------------------------------------------------------
|
| rescue_team_level  : 0=none, 1=basic, 2=standard, 3=advanced
| medical_stations   : 0=none, 1=basic, 2=standard, 3=advanced
| insurance_enabled  : 0=off, 1=on
|
| Daily operating costs per level (€):
|   EMERGENCY_RESCUE_COST  : avalanche rescue team daily cost by level
|   EMERGENCY_MEDICAL_COST : medical station daily cost by level
|   EMERGENCY_INSURANCE_DAILY_COST : daily insurance premium when enabled
|
| Response time (minutes):
|   EMERGENCY_RESPONSE_TIME_BASE          : base response time (no teams)
|   EMERGENCY_RESCUE_RESPONSE_REDUCTION   : minutes saved per rescue team level
|   EMERGENCY_MEDICAL_RESPONSE_REDUCTION  : minutes saved per medical station level
|
| Reputation effects per night:
|   EMERGENCY_REP_FAST_RESPONSE_BONUS : bonus when response time < threshold
|   EMERGENCY_RESPONSE_FAST_THRESHOLD : minutes – below this = fast response
|   EMERGENCY_REP_POOR_RESPONSE_PENALTY: penalty when response time > poor threshold
|   EMERGENCY_RESPONSE_POOR_THRESHOLD : minutes – above this = poor response
|
| Incident simulation:
|   EMERGENCY_INCIDENT_CHANCE_PCT : percent chance of an incident per night
|   EMERGENCY_FINE_NO_INSURANCE   : fine (€) when incident occurs without insurance
|   EMERGENCY_FINE_WITH_INSURANCE : fine (€) when incident occurs with insurance (reduced)
|   EMERGENCY_INCIDENT_REP_LOSS   : reputation lost per incident
*/
const EMERGENCY_RESCUE_COST             = [0 => 0, 1 => 300,  2 => 700,  3 => 1500]; // € / night
const EMERGENCY_MEDICAL_COST            = [0 => 0, 1 => 400,  2 => 900,  3 => 2000]; // € / night
const EMERGENCY_INSURANCE_DAILY_COST    = 500;   // € / night when insurance is on

const EMERGENCY_RESPONSE_TIME_BASE          = 45;  // minutes (no rescue team, no medical)
const EMERGENCY_RESCUE_RESPONSE_REDUCTION   = [0 => 0, 1 => 10, 2 => 20, 3 => 30]; // minutes saved
const EMERGENCY_MEDICAL_RESPONSE_REDUCTION  = [0 => 0, 1 => 5,  2 => 10, 3 => 15]; // minutes saved

const EMERGENCY_RESPONSE_FAST_THRESHOLD = 15;   // minutes – below = "fast response"
const EMERGENCY_RESPONSE_POOR_THRESHOLD = 40;   // minutes – above = "poor response"
const EMERGENCY_REP_FAST_RESPONSE_BONUS =  2;   // reputation gained per night
const EMERGENCY_REP_POOR_RESPONSE_PENALTY = -3; // reputation lost per night

const EMERGENCY_INCIDENT_CHANCE_PCT  = 5;     // % chance of an incident per night
const EMERGENCY_FINE_NO_INSURANCE    = 5000;  // € fine per incident without insurance
const EMERGENCY_FINE_WITH_INSURANCE  = 1000;  // € excess per incident with insurance
const EMERGENCY_INCIDENT_REP_LOSS    = 5;     // reputation lost per incident

/*
|--------------------------------------------------------------------------
| Season Ski Passes
|--------------------------------------------------------------------------
|
| SEASON_PASS_MIN_PRICE         : minimum price a player can charge per pass (€)
| SEASON_PASS_MAX_PRICE         : maximum price a player can charge per pass (€)
| SEASON_PASS_DEFAULT_PRICE     : default price when the feature is first enabled (€)
| SEASON_PASS_BASE_PRICE        : reference price used for the demand formula (€)
| SEASON_PASS_BASE_SALES        : base number of passes sold at average reputation
| SEASON_PASS_SALES_PER_REP     : extra passes sold per reputation point
| SEASON_PASS_PRICE_SENSITIVITY : demand reduction per € the price exceeds SEASON_PASS_BASE_PRICE
| SEASON_PASS_SEASON_LENGTH     : season length in days used to distribute daily revenue
| SEASON_PASS_LOYALTY_REP_BONUS : nightly reputation bonus when pass sales exceed the threshold
| SEASON_PASS_HIGH_SALES_THRESHOLD : minimum passes sold to earn the loyalty reputation bonus
| SEASON_PASS_MAX_PASSES        : hard cap on estimated passes to keep values realistic
*/
const SEASON_PASS_MIN_PRICE             = 200;    // €200 minimum
const SEASON_PASS_MAX_PRICE             = 2000;   // €2 000 maximum
const SEASON_PASS_DEFAULT_PRICE         = 500;    // €500 default
const SEASON_PASS_BASE_PRICE            = 500;    // reference price for demand formula
const SEASON_PASS_BASE_SALES            = 100;    // base passes sold at any reputation
const SEASON_PASS_SALES_PER_REP         = 0.5;    // +0.5 pass per reputation point
const SEASON_PASS_PRICE_SENSITIVITY     = 0.001;  // -0.1% demand per €1 above base price
const SEASON_PASS_SEASON_LENGTH         = 135;    // days per season
const SEASON_PASS_LOYALTY_REP_BONUS     = 2;      // +2 reputation/night when sales are high
const SEASON_PASS_HIGH_SALES_THRESHOLD  = 500;    // passes needed for the loyalty bonus
const SEASON_PASS_MAX_PASSES            = 50000;  // maximum realistic passes per season
// Early-bird discount option
const SEASON_PASS_EARLY_BIRD_MIN_DISCOUNT = 5;    // Minimum early-bird discount (%)
const SEASON_PASS_EARLY_BIRD_MAX_DISCOUNT = 30;   // Maximum early-bird discount (%)
const SEASON_PASS_EARLY_BIRD_DEFAULT_DISCOUNT = 10; // Default early-bird discount (%)
const SEASON_PASS_EARLY_BIRD_SALES_BOOST  = 0.20; // Fraction by which early-bird boosts pass sales (20%)

/*
|--------------------------------------------------------------------------
| Retail & Amenities
|--------------------------------------------------------------------------
|
| Four shop types are available: ski_shop, souvenir_shop, cafe, bar.
|
| RETAIL_BASE_REVENUE          : base daily revenue (€) per shop type at stock 3, standard pricing, popularity 50
| RETAIL_STOCK_MIN/MAX         : allowed range for the player-controlled stock level
| RETAIL_POPULARITY_DEFAULT    : starting popularity when a resort first opens a shop
| RETAIL_PRICING_MULTIPLIER    : revenue multiplier per pricing strategy
| RETAIL_PRICING_POP_DRIFT     : nightly popularity change per pricing strategy
| RETAIL_STOCK_POP_DRIFT       : nightly popularity change per stock level
| RETAIL_SEASONAL_BONUS        : revenue multiplier when seasonal items are enabled and the resort is open
| RETAIL_POPULARITY_BASE       : popularity value that produces a ×1.0 revenue multiplier
*/
const RETAIL_STOCK_MIN          = 1;
const RETAIL_STOCK_MAX          = 5;
const RETAIL_POPULARITY_MIN     = 0;
const RETAIL_POPULARITY_MAX     = 100;
const RETAIL_POPULARITY_DEFAULT = 50;
const RETAIL_POPULARITY_BASE    = 50;   // popularity at which revenue multiplier = 1.0

const RETAIL_BASE_REVENUE = [
    'ski_shop'      => 800,   // gear, warm clothing
    'souvenir_shop' => 300,   // souvenirs, keepsakes
    'cafe'          => 500,   // hot drinks, slope-side snacks
    'bar'           => 600,   // après-ski bar
];

const RETAIL_PRICING_MULTIPLIER = [
    'budget'   => 0.7,   // cheaper tickets → more sales volume
    'standard' => 1.0,   // balanced
    'premium'  => 1.4,   // higher per-sale revenue but popularity penalty
];

const RETAIL_PRICING_POP_DRIFT = [
    'budget'   =>  2,    // affordable → guests happier
    'standard' =>  0,    // neutral
    'premium'  => -3,    // perceived as expensive → gradual dissatisfaction
];

const RETAIL_STOCK_POP_DRIFT = [
    1 => -4,   // very low stock → frustrated guests
    2 => -2,
    3 =>  0,
    4 =>  1,
    5 =>  3,   // well-stocked → positive word-of-mouth
];

const RETAIL_SEASONAL_BONUS = 1.30;   // +30 % revenue boost with seasonal items during ski season

/*
|--------------------------------------------------------------------------
| Sponsorship & Branding
|--------------------------------------------------------------------------
|
| Five sponsor categories that players can sign at 3 contract levels.
|
| Per-type keys:
|   revenue_per_level          : daily €  income [lvl1, lvl2, lvl3]
|   min_reputation             : minimum resort reputation required [lvl1, lvl2, lvl3]
|   sign_cost                  : one-off signing fee in €            [lvl1, lvl2, lvl3]
|
| Type-specific bonus keys:
|   lift_equipment  → maintenance_saving_pct : fraction of daily lift upkeep saved
|   apparel         → visitor_bonus_pct      : extra visitor multiplier
|   event_title     → rep_bonus_per_level    : daily reputation gain
|
| Satisfaction mechanics:
|   SPONSORSHIP_SATISFACTION_DEFAULT   : starting value when a contract is signed
|   SPONSORSHIP_SATISFACTION_GAIN      : points gained per night when reputation OK
|   SPONSORSHIP_SATISFACTION_LOSS      : points lost  per night when reputation too low
|   SPONSORSHIP_CANCEL_REP_PENALTY     : reputation deducted when a sponsor walks away
*/
const SPONSORSHIP_TYPES = [
    'lift_equipment' => [
        'revenue_per_level'       => [400,   1000,  2500],
        'maintenance_saving_pct'  => [0.10,  0.20,  0.30],
        'min_reputation'          => [20,    40,    60],
        'sign_cost'               => [50000, 150000, 500000],
    ],
    'apparel' => [
        'revenue_per_level'       => [500,   1200,  3000],
        'visitor_bonus_pct'       => [0.03,  0.06,  0.10],
        'min_reputation'          => [30,    50,    70],
        'sign_cost'               => [60000, 180000, 600000],
    ],
    'energy_drink' => [
        'revenue_per_level'       => [300,   800,   2000],
        'min_reputation'          => [20,    35,    55],
        'sign_cost'               => [40000, 100000, 350000],
    ],
    'resort_map' => [
        'revenue_per_level'       => [200,   600,   1500],
        'min_reputation'          => [10,    20,    40],
        'sign_cost'               => [20000, 60000, 200000],
    ],
    'event_title' => [
        'revenue_per_level'       => [600,   1500,  4000],
        'rep_bonus_per_level'     => [1,     2,     3],
        'min_reputation'          => [40,    60,    80],
        'sign_cost'               => [80000, 250000, 800000],
    ],
];
const SPONSORSHIP_SATISFACTION_DEFAULT  = 70;   // Satisfaction when a contract is first signed
const SPONSORSHIP_SATISFACTION_MAX      = 100;  // Satisfaction ceiling
const SPONSORSHIP_SATISFACTION_GAIN     = 2;    // Points gained per night when reputation is OK
const SPONSORSHIP_SATISFACTION_LOSS     = 5;    // Points lost per night when reputation is too low
const SPONSORSHIP_CANCEL_REP_PENALTY    = 10;   // Reputation deducted when sponsor cancels contract

/*
|--------------------------------------------------------------------------
| Snow Groomer Improvements
|--------------------------------------------------------------------------
| Intensity multipliers applied to both the nightly slope-quality bonus
| and the groomer's daily operating cost.
|   light     : lighter passes, less fuel consumption
|   standard  : normal operation (baseline)
|   intensive : deep grooming, higher quality boost but higher fuel cost
*/
const GROOMER_INTENSITY_LIGHT     = 0.75;
const GROOMER_INTENSITY_STANDARD  = 1.0;
const GROOMER_INTENSITY_INTENSIVE = 1.5;

/*
|--------------------------------------------------------------------------
| Maintenance
|--------------------------------------------------------------------------
*/
$config['closed_mode']       = false;
$config['maintenance_mode']  = false;
$config['maintenance_ips']   = ['127.0.0.1'];

/*
|--------------------------------------------------------------------------
| Celebrity / VIP Visits
|--------------------------------------------------------------------------
|
| CELEBRITY_VISIT_CHANCE          : % probability each night that a celebrity visit occurs
| CELEBRITY_GOOD_SLOPE_THRESHOLD  : avg slope condition (0–100) required to count as "good slopes"
| CELEBRITY_REP_GOOD_SLOPES       : reputation awarded when slopes are in good condition
| CELEBRITY_REP_BASE              : reputation awarded when slopes are not in good condition
| CELEBRITY_REP_LIFT_FAIL         : reputation lost when a lift is in maintenance during the visit
| CELEBRITY_VISIT_HISTORY_DAYS    : number of past days shown in the visit history table
*/
const CELEBRITY_VISIT_CHANCE         = 15;   // % daily chance
const CELEBRITY_GOOD_SLOPE_THRESHOLD = 70;   // avg slope condition points
const CELEBRITY_REP_GOOD_SLOPES      = 25;   // big reputation spike
const CELEBRITY_REP_BASE             = 5;    // modest bonus for any visit
const CELEBRITY_REP_LIFT_FAIL        = 30;   // huge reputation loss
const CELEBRITY_VISIT_HISTORY_DAYS   = 30;   // days of history to display

/*
|--------------------------------------------------------------------------
| Mountain Cams (Webcams)
|--------------------------------------------------------------------------
|
| MOUNTAIN_CAM_MIN_CAMS          : minimum number of cameras (1)
| MOUNTAIN_CAM_MAX_CAMS          : maximum number of cameras (10)
| MOUNTAIN_CAM_DEFAULT_CAMS      : default camera count
| MOUNTAIN_CAM_DAILY_COST_BASE   : base daily operating cost (€) for 1 camera
| MOUNTAIN_CAM_DAILY_COST_PER_CAM: additional daily cost (€) per extra camera
| MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM : daily visitor demand multiplier per camera
| MOUNTAIN_CAM_REP_BONUS_PER_DAY : reputation gained per day while cams are active
| MOUNTAIN_CAM_VALID_QUALITIES   : allowed quality levels (1=Standard, 2=HD, 3=4K)
| MOUNTAIN_CAM_QUALITY_COST_MULT : daily cost multiplier per quality level
| MOUNTAIN_CAM_QUALITY_BOOST_MULT: visitor boost multiplier per quality level
| MOUNTAIN_CAM_STREAM_COST_MULT  : extra daily cost multiplier when live-stream mode is on
| MOUNTAIN_CAM_STREAM_VISITOR_MULT: extra visitor-boost multiplier when live-stream mode is on
| MOUNTAIN_CAM_SOCIAL_COST_PER_DAY: extra daily cost (€) for social-media sharing
| MOUNTAIN_CAM_SOCIAL_REP_BONUS  : extra reputation points per day from social-media sharing
| MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY  : extra daily cost (€) for night-vision mode
| MOUNTAIN_CAM_NIGHT_VISION_VISITOR_MULT  : extra visitor-boost multiplier when night-vision is on
| MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY: extra daily cost (€) for weather-overlay mode
| MOUNTAIN_CAM_WEATHER_OVERLAY_REP_BONUS  : extra reputation points per day from weather overlay
*/
const MOUNTAIN_CAM_MIN_CAMS            = 1;
const MOUNTAIN_CAM_MAX_CAMS            = 10;
const MOUNTAIN_CAM_DEFAULT_CAMS        = 1;
const MOUNTAIN_CAM_DAILY_COST_BASE     = 50;   // €50/day for the first camera
const MOUNTAIN_CAM_DAILY_COST_PER_CAM  = 30;   // €30/day per additional camera
const MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM = 0.005; // +0.5% daily visitors per camera
const MOUNTAIN_CAM_REP_BONUS_PER_DAY   = 1;    // +1 reputation/day while active
const MOUNTAIN_CAM_VALID_QUALITIES     = [1, 2, 3]; // 1=Standard, 2=HD, 3=4K
const MOUNTAIN_CAM_QUALITY_COST_MULT   = [1 => 1.0, 2 => 1.5, 3 => 2.5]; // cost multiplier
const MOUNTAIN_CAM_QUALITY_BOOST_MULT  = [1 => 1.0, 2 => 1.3, 3 => 1.6]; // visitor boost multiplier
const MOUNTAIN_CAM_STREAM_COST_MULT    = 1.4;  // live-stream adds 40% to daily cost
const MOUNTAIN_CAM_STREAM_VISITOR_MULT = 1.2;  // live-stream adds 20% to visitor boost
const MOUNTAIN_CAM_SOCIAL_COST_PER_DAY = 20;   // €20/day extra for social-media sharing
const MOUNTAIN_CAM_SOCIAL_REP_BONUS    = 2;    // +2 reputation/day from social-media sharing
const MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY   = 25;  // €25/day extra for night-vision mode
const MOUNTAIN_CAM_NIGHT_VISION_VISITOR_MULT   = 1.15; // night-vision adds 15% to visitor boost
const MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY = 10; // €10/day extra for weather-overlay
const MOUNTAIN_CAM_WEATHER_OVERLAY_REP_BONUS    = 1;  // +1 reputation/day from weather overlay

/*
|--------------------------------------------------------------------------
| Daily Login Bonus
|--------------------------------------------------------------------------
|
| DAILY_BONUS_STREAK_MAX   : streak is capped at this day for reward purposes
| DAILY_BONUS_CASH_BASE    : cash awarded on day 1 (€)
| DAILY_BONUS_CASH_PER_STREAK : extra cash added per streak day above 1 (€)
| DAILY_BONUS_REP_PER_TIER : reputation awarded at streak tiers (indexed 1–7)
*/
const DAILY_BONUS_STREAK_MAX        = 7;
const DAILY_BONUS_CASH_BASE         = 500;
const DAILY_BONUS_CASH_PER_STREAK   = 250;
const DAILY_BONUS_REP_PER_TIER      = [1 => 0, 2 => 0, 3 => 1, 4 => 1, 5 => 2, 6 => 2, 7 => 3];

/*
|--------------------------------------------------------------------------
| Idle Income System
|--------------------------------------------------------------------------
|
| Passive income that accumulates in game_resorts.pending_idle_income while
| the player is offline.  The nightly cron adds it every night and the
| player collects it the next time they visit their resort page.
|
| IDLE_INCOME_PER_OPEN_SLOPE   : € earned per open slope per day
| IDLE_INCOME_PER_OPEN_LIFT    : € earned per open lift per day
| IDLE_INCOME_MAX_DAYS         : maximum number of days that can accumulate
|                                (prevents runaway balances for long absences)
*/
const IDLE_INCOME_PER_OPEN_SLOPE = 500;    // € per open slope per night
const IDLE_INCOME_PER_OPEN_LIFT  = 800;    // € per open lift per night
const IDLE_INCOME_MAX_DAYS       = 7;      // cap: at most 7 days of idle income

/*
|--------------------------------------------------------------------------
| Government & Regulations
|--------------------------------------------------------------------------
|
| GOV_COMPLIANCE_BLOCK_THRESHOLD   : compliance score below this blocks expansion
| GOV_COMPLIANCE_RESTORE_THRESHOLD : compliance score at or above this lifts block
| GOV_AUDIT_CHANCE                 : % daily probability of a safety inspection audit
| GOV_AUDIT_PASS_THRESHOLD         : compliance score >= this => audit pass
| GOV_AUDIT_PASS_REWARD            : cash awarded for passing an audit (€)
| GOV_AUDIT_FAIL_FINE              : cash fine for failing an audit (€)
| GOV_COMPLIANCE_AUDIT_PASS_BONUS  : compliance points gained on audit pass
| GOV_COMPLIANCE_AUDIT_FAIL_PENALTY: compliance points lost on audit fail
| GOV_COMPLIANCE_HIGH_ECO_BONUS    : nightly compliance bonus when eco_reputation >= 70
| GOV_COMPLIANCE_LOW_ECO_PENALTY   : nightly compliance penalty when eco_reputation < 30
| GOV_COMPLIANCE_RESTRICT_PENALTY  : nightly compliance penalty when expansion_restricted
| GOV_TAX_RATE_MIN                 : minimum additional yearly regulation tax rate (%)
| GOV_TAX_RATE_MAX                 : maximum additional yearly regulation tax rate (%)
| GOV_SUBSIDY_ECO_THRESHOLD        : eco_reputation needed to qualify for eco subsidy
| GOV_SUBSIDY_AMOUNT               : eco subsidy cash reward when qualifying (€)
*/
const GOV_COMPLIANCE_BLOCK_THRESHOLD    = 20;   // expansion blocked below this score
const GOV_COMPLIANCE_RESTORE_THRESHOLD  = 40;   // expansion unblocked at/above this score
const GOV_AUDIT_CHANCE                  = 15;   // % daily chance of safety audit
const GOV_AUDIT_PASS_THRESHOLD          = 50;   // compliance >= this => audit pass
const GOV_AUDIT_PASS_REWARD             = 500;  // € reward for passing audit
const GOV_AUDIT_FAIL_FINE               = 2000; // € fine for failing audit
const GOV_COMPLIANCE_AUDIT_PASS_BONUS   = 5;    // compliance gain on audit pass
const GOV_COMPLIANCE_AUDIT_FAIL_PENALTY = -10;  // compliance loss on audit fail
const GOV_COMPLIANCE_HIGH_ECO_BONUS     = 3;    // nightly bonus when eco_rep >= 70
const GOV_COMPLIANCE_LOW_ECO_PENALTY    = -5;   // nightly penalty when eco_rep < 30
const GOV_COMPLIANCE_RESTRICT_PENALTY   = -5;   // nightly penalty when expansion_restricted
const GOV_TAX_RATE_MIN                  = 1;    // minimum regulation tax rate (%)
const GOV_TAX_RATE_MAX                  = 5;    // maximum regulation tax rate (%)
const GOV_SUBSIDY_ECO_THRESHOLD         = 60;   // eco_reputation to qualify for subsidy
const GOV_SUBSIDY_AMOUNT                = 10000;// € subsidy when eco_rep qualifies

/*
|--------------------------------------------------------------------------
| Home Page – Image Upload & hCaptcha
|--------------------------------------------------------------------------
*/
$config['home_upload_token']   = 'HOME_UPLOAD_TOKEN';
$config['hcaptcha_secret']     = 'HCAPTCHA_SECRET';
$config['hcaptcha_sitekey']    = 'HCAPTCHA_SITEKEY';
