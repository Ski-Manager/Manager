<?php
/**
 * Night Skiing API
 *
 * Vercel PHP serverless function.
 * Handles all night-skiing read/write operations, enforcing
 * CI session-based auth and resort ownership.
 *
 * Actions (POST with JSON body or POST form-encoded):
 *   get_resort_data      – resort settings + all trails for current resort
 *   save_resort_settings – update resort-level night skiing config
 *   toggle_night_skiing  – flip the top-level night_skiing flag
 *   toggle_trail         – flip night_skiing_enabled for one trail
 *   save_trail           – update light_type / brightness / pole_spacing for one trail
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

/* ───────────────────────────────────────────────
 * Helpers
 * ─────────────────────────────────────────────── */

function json_out(array $payload, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

function error_out(string $message, int $code = 400): void
{
    json_out(['success' => false, 'message' => $message], $code);
}

/**
 * Parse CodeIgniter's custom serialised session string.
 * Format: key1|serialised_value1;key2|serialised_value2;…
 */
function parse_ci_session(string $data): array
{
    $result = [];
    // Split on the pattern key|value; where key has no special chars
    $pattern = '/([a-zA-Z_][a-zA-Z0-9_]*)\|/';
    $keys    = [];
    $offsets = [];
    if (!preg_match_all($pattern, $data, $matches, PREG_OFFSET_CAPTURE)) {
        return $result;
    }
    foreach ($matches[1] as $i => $match) {
        $keys[]    = $match[0];
        $offsets[] = $matches[0][$i][1] + strlen($matches[0][$i][0]); // after the pipe
    }
    for ($i = 0; $i < count($keys); $i++) {
        $valueStart = $offsets[$i];
        $valueEnd   = ($i + 1 < count($offsets))
            ? $offsets[$i + 1] - strlen($keys[$i + 1]) - 1  // before next "key|"
            : strlen($data);
        $raw = substr($data, $valueStart, $valueEnd - $valueStart);
        $raw = rtrim($raw, ';');
        try {
            $unserialized = @unserialize($raw);
            $result[$keys[$i]] = ($unserialized !== false || $raw === 'b:0;') ? $unserialized : $raw;
        } catch (\Throwable $e) {
            $result[$keys[$i]] = $raw;
        }
    }
    return $result;
}

/* ───────────────────────────────────────────────
 * Database connection
 * ─────────────────────────────────────────────── */

function get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $host   = getenv('DB_HOST')     ?: 'localhost';
    $dbname = getenv('DB_NAME');
    $user   = getenv('DB_USER')     ?: '';
    $pass   = getenv('DB_PASS')     ?: '';
    $port   = (int) (getenv('DB_PORT') ?: 3306);

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/* ───────────────────────────────────────────────
 * Session / auth
 * ─────────────────────────────────────────────── */

/**
 * Reads the CI session cookie, looks up the session in the DB,
 * and returns ['id_player' => int, 'currentResortId' => int] on success,
 * or null on failure.
 */
function get_auth(): ?array
{
    // CI stores the session ID in a cookie. The cookie name may be prefixed.
    $sessionId = null;
    foreach ($_COOKIE as $name => $value) {
        if (str_ends_with($name, 'ci_session') || $name === 'ci_session') {
            $sessionId = $value;
            break;
        }
    }
    if ($sessionId === null) {
        return null;
    }

    // Strip any Vercel proxy prefix that might appear in the cookie value
    $sessionId = preg_replace('/[^a-f0-9]/i', '', $sessionId);
    if (strlen($sessionId) < 20) {
        return null;
    }

    try {
        $pdo  = get_pdo();
        $stmt = $pdo->prepare(
            'SELECT `data` FROM `ci_sessions` WHERE `id` = ? AND `timestamp` > ? LIMIT 1'
        );
        $stmt->execute([$sessionId, time() - 7200]); // 2-hour window
        $row  = $stmt->fetch();
    } catch (\Throwable $e) {
        return null;
    }

    if (!$row) {
        return null;
    }

    $session = parse_ci_session($row['data']);
    $playerId = isset($session['id_player']) ? (int) $session['id_player'] : 0;
    $resortId = isset($session['currentResortId']) ? (int) $session['currentResortId'] : 0;

    if ($playerId <= 0) {
        return null;
    }

    return ['id_player' => $playerId, 'currentResortId' => $resortId];
}

/**
 * Confirm the given resort belongs to the authenticated player.
 */
function owns_resort(PDO $pdo, int $idPlayer, int $idResort): bool
{
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_resorts` WHERE `id_resort` = ? AND `id_player` = ? LIMIT 1'
    );
    $stmt->execute([$idResort, $idPlayer]);
    return (bool) $stmt->fetch();
}

/* ───────────────────────────────────────────────
 * Log event helper
 * ─────────────────────────────────────────────── */

function log_event(PDO $pdo, int $idResort, string $category, int $isPositive, string $message): void
{
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO `game_resort_event_logs`
             (`id_resort`, `category`, `is_positive`, `date`, `description`)
             VALUES (?, ?, ?, NOW(), ?)'
        );
        $stmt->execute([$idResort, $category, $isPositive, $message]);
    } catch (\Throwable $e) {
        // Non-fatal; swallow silently
    }
}

/* ───────────────────────────────────────────────
 * Action handlers
 * ─────────────────────────────────────────────── */

function action_get_resort_data(PDO $pdo, int $idResort): void
{
    // Resort-level settings
    $stmt = $pdo->prepare(
        'SELECT
            `night_skiing`,
            `night_skiing_start_hour`,
            `night_skiing_end_hour`,
            `night_skiing_ticket_price`,
            `night_skiing_entertainment`,
            `night_skiing_safety_level`,
            `night_skiing_school_enabled`,
            `night_skiing_school_price`,
            `night_skiing_weather_suspend`,
            `night_skiing_torchlight`,
            `night_skiing_photo_enabled`,
            `night_skiing_photo_price`
         FROM `game_resorts`
         WHERE `id_resort` = ?
         LIMIT 1'
    );
    $stmt->execute([$idResort]);
    $resort = $stmt->fetch();

    if (!$resort) {
        error_out('Resort not found.', 404);
    }

    // Per-trail data joined with slope names
    $stmt = $pdo->prepare(
        'SELECT
            nst.`id_night_skiing_trail`,
            nst.`id_created_slope`,
            nst.`night_skiing_enabled`,
            nst.`light_type`,
            nst.`brightness`,
            nst.`pole_spacing`,
            cs.`slope_name`    AS slope_name,
            cs.`id_slope_type` AS slope_type,
            cs.`id_status`     AS slope_status
         FROM `game_night_skiing_trails` nst
         LEFT JOIN `game_created_slopes` cs
               ON nst.`id_created_slope` = cs.`id_created_slope`
         WHERE nst.`id_resort` = ?
         ORDER BY cs.`slope_name` ASC'
    );
    $stmt->execute([$idResort]);
    $trails = $stmt->fetchAll();

    // Also fetch slopes that are lit (have night_lighting module) but
    // not yet registered in game_night_skiing_trails so we can offer
    // the player the option to configure them.
    $stmt = $pdo->prepare(
        'SELECT
            cs.`id_created_slope`,
            cs.`slope_name`,
            cs.`id_slope_type`,
            cs.`id_status`
         FROM `game_created_slopes` cs
         WHERE cs.`id_resort` = ?
           AND cs.`id_created_slope` NOT IN (
               SELECT `id_created_slope`
               FROM `game_night_skiing_trails`
               WHERE `id_resort` = ?
           )
           AND cs.`id_status` != 0
         ORDER BY cs.`slope_name` ASC'
    );
    $stmt->execute([$idResort, $idResort]);
    $unregisteredSlopes = $stmt->fetchAll();

    json_out([
        'success'            => true,
        'resort'             => $resort,
        'trails'             => $trails,
        'unregistered_slopes'=> $unregisteredSlopes,
    ]);
}

function action_save_resort_settings(PDO $pdo, int $idResort, array $body): void
{
    $allowed = [
        'night_skiing_start_hour',
        'night_skiing_end_hour',
        'night_skiing_ticket_price',
        'night_skiing_entertainment',
        'night_skiing_safety_level',
        'night_skiing_school_enabled',
        'night_skiing_school_price',
        'night_skiing_weather_suspend',
        'night_skiing_torchlight',
        'night_skiing_photo_enabled',
        'night_skiing_photo_price',
    ];

    $setClauses = [];
    $params     = [];

    foreach ($allowed as $field) {
        if (!array_key_exists($field, $body)) {
            continue;
        }
        $value = $body[$field];

        // Type-cast and validate
        switch ($field) {
            case 'night_skiing_start_hour':
            case 'night_skiing_end_hour':
                $value = max(0, min(23, (int) $value));
                break;
            case 'night_skiing_ticket_price':
            case 'night_skiing_photo_price':
                $value = max(0, (int) $value);
                break;
            case 'night_skiing_school_price':
                $value = max(0.0, round((float) $value, 2));
                break;
            case 'night_skiing_safety_level':
                $value = max(0, min(10, (int) $value));
                break;
            case 'night_skiing_school_enabled':
            case 'night_skiing_weather_suspend':
            case 'night_skiing_torchlight':
            case 'night_skiing_photo_enabled':
                $value = $value ? 1 : 0;
                break;
            case 'night_skiing_entertainment':
                $value = substr(trim((string) $value), 0, 255);
                break;
        }

        $setClauses[] = "`{$field}` = ?";
        $params[]     = $value;
    }

    if (empty($setClauses)) {
        error_out('No valid fields provided.');
    }

    $params[] = $idResort;
    $sql      = 'UPDATE `game_resorts` SET ' . implode(', ', $setClauses) . ' WHERE `id_resort` = ?';
    $pdo->prepare($sql)->execute($params);

    json_out(['success' => true, 'message' => 'Night skiing settings saved.']);
}

function action_toggle_night_skiing(PDO $pdo, int $idResort, int $idPlayer, array $body): void
{
    $enabled = isset($body['enabled']) ? (int)(bool)$body['enabled'] : null;
    if ($enabled === null) {
        error_out('Missing "enabled" field.');
    }

    $pdo->prepare('UPDATE `game_resorts` SET `night_skiing` = ? WHERE `id_resort` = ?')
        ->execute([$enabled, $idResort]);

    $message = $enabled
        ? 'Night skiing has been enabled.'
        : 'Night skiing has been disabled.';

    log_event($pdo, $idResort, 'Building', $enabled, $message);

    json_out(['success' => true, 'enabled' => (bool) $enabled, 'message' => $message]);
}

function action_toggle_trail(PDO $pdo, int $idResort, array $body): void
{
    $trailId = isset($body['id_night_skiing_trail']) ? (int) $body['id_night_skiing_trail'] : 0;
    $enabled = isset($body['enabled']) ? (int)(bool)$body['enabled'] : null;

    if ($trailId <= 0 || $enabled === null) {
        error_out('Missing id_night_skiing_trail or enabled.');
    }

    // Confirm trail belongs to this resort
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_night_skiing_trails` WHERE `id_night_skiing_trail` = ? AND `id_resort` = ? LIMIT 1'
    );
    $stmt->execute([$trailId, $idResort]);
    if (!$stmt->fetch()) {
        error_out('Trail not found.', 404);
    }

    $pdo->prepare(
        'UPDATE `game_night_skiing_trails` SET `night_skiing_enabled` = ? WHERE `id_night_skiing_trail` = ?'
    )->execute([$enabled, $trailId]);

    json_out(['success' => true, 'id_night_skiing_trail' => $trailId, 'enabled' => (bool) $enabled]);
}

function action_save_trail(PDO $pdo, int $idResort, array $body): void
{
    $trailId    = isset($body['id_night_skiing_trail']) ? (int) $body['id_night_skiing_trail'] : 0;
    $lightType  = isset($body['light_type'])   ? trim((string) $body['light_type'])   : null;
    $brightness = isset($body['brightness'])   ? (int) $body['brightness']            : null;
    $poleSpacing= isset($body['pole_spacing']) ? (int) $body['pole_spacing']          : null;

    if ($trailId <= 0) {
        error_out('Missing id_night_skiing_trail.');
    }

    $validLightTypes  = ['led', 'metal_halide'];
    $validBrightness  = [1, 2, 3, 4, 5];
    $validPoleSpacing = [15, 25, 35];

    if ($lightType !== null && !in_array($lightType, $validLightTypes, true)) {
        error_out('Invalid light_type.');
    }
    if ($brightness !== null && !in_array($brightness, $validBrightness, true)) {
        error_out('Invalid brightness value (1–5).');
    }
    if ($poleSpacing !== null && !in_array($poleSpacing, $validPoleSpacing, true)) {
        error_out('Invalid pole_spacing (15, 25, or 35).');
    }

    // Confirm trail belongs to this resort
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_night_skiing_trails` WHERE `id_night_skiing_trail` = ? AND `id_resort` = ? LIMIT 1'
    );
    $stmt->execute([$trailId, $idResort]);
    if (!$stmt->fetch()) {
        error_out('Trail not found.', 404);
    }

    $setClauses = [];
    $params     = [];

    if ($lightType !== null)  { $setClauses[] = '`light_type` = ?';   $params[] = $lightType; }
    if ($brightness !== null) { $setClauses[] = '`brightness` = ?';   $params[] = $brightness; }
    if ($poleSpacing !== null){ $setClauses[] = '`pole_spacing` = ?'; $params[] = $poleSpacing; }

    if (empty($setClauses)) {
        error_out('No trail fields to update.');
    }

    $params[] = $trailId;
    $pdo->prepare(
        'UPDATE `game_night_skiing_trails` SET ' . implode(', ', $setClauses) . ' WHERE `id_night_skiing_trail` = ?'
    )->execute($params);

    json_out(['success' => true, 'id_night_skiing_trail' => $trailId]);
}

function action_add_trail(PDO $pdo, int $idResort, array $body): void
{
    $slopeId    = isset($body['id_created_slope']) ? (int) $body['id_created_slope'] : 0;
    $lightType  = isset($body['light_type'])   ? trim((string) $body['light_type'])  : 'led';
    $brightness = isset($body['brightness'])   ? (int) $body['brightness']           : 3;
    $poleSpacing= isset($body['pole_spacing']) ? (int) $body['pole_spacing']         : 25;

    $validLightTypes  = ['led', 'metal_halide'];
    $validBrightness  = [1, 2, 3, 4, 5];
    $validPoleSpacing = [15, 25, 35];

    if ($slopeId <= 0) {
        error_out('Missing id_created_slope.');
    }
    if (!in_array($lightType, $validLightTypes, true))  { error_out('Invalid light_type.'); }
    if (!in_array($brightness, $validBrightness, true)) { error_out('Invalid brightness.'); }
    if (!in_array($poleSpacing, $validPoleSpacing, true)){ error_out('Invalid pole_spacing.'); }

    // Confirm slope belongs to resort and not already registered
    $stmt = $pdo->prepare(
        'SELECT `slope_name` FROM `game_created_slopes`
         WHERE `id_created_slope` = ? AND `id_resort` = ? AND `id_status` != 0 LIMIT 1'
    );
    $stmt->execute([$slopeId, $idResort]);
    $slope = $stmt->fetch();
    if (!$slope) {
        error_out('Slope not found or not active.', 404);
    }

    // Check not already registered
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_night_skiing_trails` WHERE `id_created_slope` = ? AND `id_resort` = ? LIMIT 1'
    );
    $stmt->execute([$slopeId, $idResort]);
    if ($stmt->fetch()) {
        error_out('Slope is already registered for night skiing.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO `game_night_skiing_trails`
         (`id_resort`, `id_created_slope`, `night_skiing_enabled`, `light_type`, `brightness`, `pole_spacing`)
         VALUES (?, ?, 1, ?, ?, ?)'
    );
    $stmt->execute([$idResort, $slopeId, $lightType, $brightness, $poleSpacing]);
    $newId = (int) $pdo->lastInsertId();

    json_out([
        'success'               => true,
        'id_night_skiing_trail' => $newId,
        'slope_name'            => $slope['slope_name'],
    ]);
}

function action_remove_trail(PDO $pdo, int $idResort, array $body): void
{
    $trailId = isset($body['id_night_skiing_trail']) ? (int) $body['id_night_skiing_trail'] : 0;

    if ($trailId <= 0) {
        error_out('Missing id_night_skiing_trail.');
    }

    // Confirm ownership
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_night_skiing_trails` WHERE `id_night_skiing_trail` = ? AND `id_resort` = ? LIMIT 1'
    );
    $stmt->execute([$trailId, $idResort]);
    if (!$stmt->fetch()) {
        error_out('Trail not found.', 404);
    }

    $pdo->prepare('DELETE FROM `game_night_skiing_trails` WHERE `id_night_skiing_trail` = ?')
        ->execute([$trailId]);

    json_out(['success' => true]);
}

function action_get_events(PDO $pdo, int $idResort): void
{
    $stmt = $pdo->prepare(
        'SELECT `id`, `event_type`, `scheduled_date`, `status`, `visitor_bonus_pct`, `revenue_multiplier`, `cost`, `rep_bonus`
         FROM `game_night_skiing_events`
         WHERE `id_resort` = ?
         ORDER BY `scheduled_date` DESC
         LIMIT 20'
    );
    $stmt->execute([$idResort]);
    $events = $stmt->fetchAll();

    json_out([
        'success' => true,
        'events'  => $events,
    ]);
}

function action_schedule_event(PDO $pdo, int $idResort, array $body): void
{
    $eventType = isset($body['event_type']) ? trim((string) $body['event_type']) : '';
    $scheduledDate = isset($body['scheduled_date']) ? trim((string) $body['scheduled_date']) : '';

    if (empty($eventType) || empty($scheduledDate)) {
        error_out('Missing event_type or scheduled_date.');
    }

    // Validate event type exists in config
    $validTypes = ['dj_night', 'race_night', 'torchlight_parade'];
    if (!in_array($eventType, $validTypes, true)) {
        error_out('Invalid event_type.');
    }

    // Validate date format (YYYY-MM-DD) and that it's today or in the future
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduledDate)) {
        error_out('Invalid date format (use YYYY-MM-DD).');
    }

    // For simplicity, allow scheduling on today or any future date
    // (The cron will process pending events on their scheduled dates)

    // Get event config from database-equivalent constants
    // For API purposes, we hardcode the config here or fetch from a helper
    $eventConfigs = [
        'dj_night'          => ['cost' => 800, 'visitor_bonus_pct' => 30, 'revenue_multiplier' => 1.35, 'rep_bonus' => 2],
        'race_night'        => ['cost' => 1200, 'visitor_bonus_pct' => 20, 'revenue_multiplier' => 1.20, 'rep_bonus' => 3],
        'torchlight_parade' => ['cost' => 500, 'visitor_bonus_pct' => 40, 'revenue_multiplier' => 1.15, 'rep_bonus' => 2],
    ];
    $config = $eventConfigs[$eventType] ?? null;
    if (!$config) {
        error_out('Event configuration not found.');
    }

    // Check if already scheduled for this date
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_night_skiing_events`
         WHERE `id_resort` = ? AND `event_type` = ? AND `scheduled_date` = ? AND `status` IN (\'pending\', \'completed\')
         LIMIT 1'
    );
    $stmt->execute([$idResort, $eventType, $scheduledDate]);
    if ($stmt->fetch()) {
        error_out('Event already scheduled for this date.', 400);
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO `game_night_skiing_events`
             (`id_resort`, `event_type`, `scheduled_date`, `status`, `visitor_bonus_pct`, `revenue_multiplier`, `cost`, `rep_bonus`)
             VALUES (?, ?, ?, \'pending\', ?, ?, ?, ?)'
        );
        $stmt->execute([
            $idResort,
            $eventType,
            $scheduledDate,
            (float)($config['visitor_bonus_pct'] ?? 0),
            (float)($config['revenue_multiplier'] ?? 1.0),
            (int)($config['cost'] ?? 0),
            (int)($config['rep_bonus'] ?? 0),
        ]);
        $newId = (int) $pdo->lastInsertId();

        json_out([
            'success' => true,
            'id'      => $newId,
            'message' => 'Event scheduled successfully.',
        ]);
    } catch (\Throwable $e) {
        error_out('Failed to schedule event.', 500);
    }
}

function action_cancel_event(PDO $pdo, int $idResort, array $body): void
{
    $eventId = isset($body['id']) ? (int) $body['id'] : 0;

    if ($eventId <= 0) {
        error_out('Missing event id.');
    }

    // Confirm ownership and that it's pending
    $stmt = $pdo->prepare(
        'SELECT 1 FROM `game_night_skiing_events`
         WHERE `id` = ? AND `id_resort` = ? AND `status` = \'pending\'
         LIMIT 1'
    );
    $stmt->execute([$eventId, $idResort]);
    if (!$stmt->fetch()) {
        error_out('Event not found or already completed.', 404);
    }

    $stmt = $pdo->prepare(
        'UPDATE `game_night_skiing_events` SET `status` = \'cancelled\' WHERE `id` = ?'
    );
    $stmt->execute([$eventId]);

    json_out(['success' => true, 'message' => 'Event cancelled.']);
}

/* ───────────────────────────────────────────────
 * Router
 * ─────────────────────────────────────────────── */

// Auth
$auth = get_auth();
if ($auth === null) {
    error_out('Unauthorised.', 401);
}
$idPlayer = $auth['id_player'];
$idResort = $auth['currentResortId'];

// Read action
$body   = [];
$rawBody = file_get_contents('php://input');
if ($rawBody !== false && strlen($rawBody) > 0) {
    $decoded = json_decode($rawBody, true);
    $body    = is_array($decoded) ? $decoded : [];
}
// Fall back to POST superglobal (form-encoded)
if (empty($body)) {
    $body = $_POST;
}

$action = $_GET['action'] ?? ($body['action'] ?? '');

if (empty($action)) {
    error_out('Missing action parameter.');
}

// Read-only actions don't need a resort
if ($action === 'get_resort_data' && $idResort <= 0) {
    error_out('No active resort in session.', 400);
}

try {
    $pdo = get_pdo();
} catch (\Throwable $e) {
    error_out('Database connection failed.', 500);
}

// Enforce resort ownership for all write operations
$writeActions = ['save_resort_settings', 'toggle_night_skiing', 'toggle_trail', 'save_trail', 'add_trail', 'remove_trail', 'schedule_event', 'cancel_event'];
if (in_array($action, $writeActions, true)) {
    if ($idResort <= 0) {
        error_out('No active resort in session.', 400);
    }
    if (!owns_resort($pdo, $idPlayer, $idResort)) {
        error_out('You do not own this resort.', 403);
    }
}

switch ($action) {
    case 'get_resort_data':
        action_get_resort_data($pdo, $idResort);
        break;
    case 'save_resort_settings':
        action_save_resort_settings($pdo, $idResort, $body);
        break;
    case 'toggle_night_skiing':
        action_toggle_night_skiing($pdo, $idResort, $idPlayer, $body);
        break;
    case 'toggle_trail':
        action_toggle_trail($pdo, $idResort, $body);
        break;
    case 'save_trail':
        action_save_trail($pdo, $idResort, $body);
        break;
    case 'add_trail':
        action_add_trail($pdo, $idResort, $body);
        break;
    case 'remove_trail':
        action_remove_trail($pdo, $idResort, $body);
        break;
    case 'get_events':
        action_get_events($pdo, $idResort);
        break;
    case 'schedule_event':
        action_schedule_event($pdo, $idResort, $body);
        break;
    case 'cancel_event':
        action_cancel_event($pdo, $idResort, $body);
        break;
    default:
        error_out('Unknown action.', 400);
}
