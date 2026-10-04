<?php
/**
 * Unified Burial Services & Death Certificate Request
 * Renders the unified Death Certificate Request Form & Case Tracking view
 */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
if (!isset($_GET['tab'])) {
    $_GET['tab'] = 'request';
}
include __DIR__ . '/user_funeral-case.php';
