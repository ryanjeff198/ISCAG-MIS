<?php

/**
 * Database Configuration
 *
 * Fill in your database credentials below.
 * This file returns a PDO instance when called via getDbConnection().
 */

// Auto-detect Local (XAMPP) vs Production (InfinityFree / Live Server)
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1'])
    || (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') !== false)
    || (strpos(__DIR__, 'xampp') !== false)
    || (DIRECTORY_SEPARATOR === '\\');

if ($isLocal) {
    // ── Local Development (XAMPP) ──────────────────────────────────
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'iscag');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
} else {
    // ── InfinityFree / Live Hosting Credentials ───────────────────
    define('DB_HOST', 'sql106.infinityfree.com');
    define('DB_PORT', '3306');
    define('DB_NAME', 'if0_43246197_iscag'); // If your database name is different, change _iscag to your database suffix
    define('DB_USER', 'if0_43246197');
    define('DB_PASS', 'B0AYg0NXV5OJQUh');
    define('DB_CHARSET', 'utf8mb4');
}

/**
 * Get a PDO database connection.
 *
 * @return PDO
 */
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}
