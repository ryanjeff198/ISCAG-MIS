<?php

/**
 * Front Controller
 *
 * All HTTP requests are routed through this file.
 * It matches the request URI against the route definitions,
 * loads the appropriate controller, and calls the mapped method.
 */

// Define the base path of the application (one level up from public/)
define('BASE_PATH', dirname(__DIR__));

// Base URL for generating links (adjust if your project is not at /Iscag)
define('BASE_URL', '/Iscag');

/**
 * Generate a full URL path relative to the application root.
 * Usage in views: <?= url('/login') ?>
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Generate a path to a public asset file.
 * Usage in views: <?= asset('css/homepage.css') ?>
 */
function asset(string $path): string
{
    return BASE_URL . '/public/' . ltrim($path, '/');
}

// Start session with security settings
ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1); // Enable if using HTTPS
session_start();

// Require Core Files
require_once BASE_PATH . '/app/helpers/Security.php';
require_once BASE_PATH . '/app/helpers/Auth.php';

// Load route definitions
require_once BASE_PATH . '/routes/web.php';

// Parse the request URI and extract the route
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base URL prefix (e.g. /Iscag) to get the clean route
$route = $requestUri;
if (BASE_URL !== '' && strpos($route, BASE_URL) === 0) {
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
    echo "404 - No matching route found in the MVC system.";
}
