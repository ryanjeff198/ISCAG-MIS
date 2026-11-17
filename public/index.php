<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');

/**
 * Front Controller
 *
 * All HTTP requests are routed through this file.
 * It matches the request URI against the route definitions,
 * loads the appropriate controller, and calls the mapped method.
 */

// Define the base path of the application (one level up from public/)
define('BASE_PATH', dirname(__DIR__));

// Base URL for generating links (Auto-detected)
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$baseUrl = str_replace('/public/index.php', '', $scriptPath);
define('BASE_URL', rtrim($baseUrl, '/'));

/**
 * Generate a full URL path relative to the application root.
 * Usage in views: <?= url('/login') ?>
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Generate an absolute URL including scheme and HTTP_HOST.
 * Useful for external redirects like payment gateways.
 */
function full_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . url($path);
}

/**
 * Generate a path to a public asset file.
 * Usage in views: <?= asset('css/homepage.css') ?>
 */
function asset(string $path): string
{
    $clean = ltrim($path, '/');
    $url = BASE_URL . '/public/' . $clean;
    if (strpos($clean, '?') === false) {
        $fullPath = BASE_PATH . '/public/' . $clean;
        if (is_file($fullPath)) {
            $url .= '?v=' . filemtime($fullPath);
        }
    }
    return $url;
}

/**
 * Format date consistently (e.g. "Nov 05, 2026")
 */
function format_date($val, string $fallback = '—'): string
{
    if (empty($val) || $val === '0000-00-00' || $val === '0000-00-00 00:00:00') return $fallback;
    $ts = is_numeric($val) ? (int)$val : strtotime((string)$val);
    if (!$ts || $ts <= 0) return $fallback;
    return date('M d, Y', $ts);
}

/**
 * Format datetime consistently for submitted/received timestamps (e.g. "Nov 05, 2026 • 5:59 AM")
 */
function format_datetime($val, string $fallback = '—'): string
{
    if (empty($val) || $val === '0000-00-00' || $val === '0000-00-00 00:00:00') return $fallback;
    $str = trim((string)$val);
    $ts = is_numeric($val) ? (int)$val : strtotime($str);
    if (!$ts || $ts <= 0) return $fallback;
    if (strlen($str) <= 10) {
        return date('M d, Y', $ts);
    }
    return date('M d, Y • h:i A', $ts);
}

/**
 * Format date & time as stacked HTML for admin tables
 */
function format_datetime_html($val, string $fallback = '—'): string
{
    if (empty($val) || $val === '0000-00-00' || $val === '0000-00-00 00:00:00') return $fallback;
    $str = trim((string)$val);
    $ts = is_numeric($val) ? (int)$val : strtotime($str);
    if (!$ts || $ts <= 0) return $fallback;
    
    $d = date('M d, Y', $ts);
    if (strlen($str) <= 10) {
        return '<span style="font-weight:600;color:var(--text-main);">' . $d . '</span>';
    }
    $t = date('h:i A', $ts);
    return '<span style="font-weight:600;color:var(--text-main);">' . $d . '</span><span style="display:block;font-size:0.75rem;color:var(--text-muted);margin-top:1px;">' . $t . '</span>';
}

// Start session with security settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_samesite', 'Lax');
    // ini_set('session.cookie_secure', 1); // Enable if using HTTPS
    session_start();
}

// Require Core Files
require_once BASE_PATH . '/app/helpers/Security.php';
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/app/helpers/TimeSim.php';

// Load route definitions
require_once BASE_PATH . '/routes/web.php';

// Parse the request URI and extract the route
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base URL prefix to get the clean route
$route = $requestUri;
if (BASE_URL !== '' && stripos($route, BASE_URL) === 0) {
    $route = substr($route, strlen(BASE_URL));
}

// Clean up: ensure leading slash, remove trailing slash
$route = '/' . trim($route, '/');

// Normalize: make empty route default to '/'
if ($route === '' || $route === '//') {
    $route = '/';
}

// ── DEVELOPER MODE: DYNAMIC PREVIEWER ──
// Allows direct access to any view file via /preview/...
if (str_contains($requestUri, '/preview/')) {
    $previewPath = explode('/preview/', $requestUri)[1];
    $viewPath = BASE_PATH . '/app/views/' . $previewPath;

    if (!file_exists($viewPath) && !str_ends_with($viewPath, '.php')) {
        $viewPath .= '.php';
    }

    if (file_exists($viewPath)) {
        require_once $viewPath;
        exit;
    }
}

// Match route
if (isset($routes[$route])) {
    [$controllerName, $method] = $routes[$route];

    // Load the controller file
    $controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';

    if (!file_exists($controllerFile)) {
        http_response_code(500);
        echo "Controller file [{$controllerName}.php] not found.";
        exit;
    }

    require_once $controllerFile;

    // Instantiate and call
    $controller = new $controllerName();

    if (!method_exists($controller, $method)) {
        http_response_code(500);
        echo "Method [{$method}] not found in [{$controllerName}].";
        exit;
    }

    $controller->$method();
} else {
    // 404 — No matching route (DISABLED)
    // require_once BASE_PATH . '/app/controllers/ErrorController.php';
    // ErrorController::show404();
    echo "404 - No matching route found in the MVC system.<br><br>";
    echo "<b>DEBUG INFO:</b><br>";
    echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
    echo "Calculated Route: " . $route . "<br>";
}
