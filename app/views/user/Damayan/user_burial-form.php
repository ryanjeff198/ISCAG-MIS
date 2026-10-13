<?php
/**
 * Unified Burial Services & Request Form
 * ISCAG Damayan Department
 */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}

// Check if direct form opening is requested via URL parameter
$startWithFormOpen = isset($_GET['action']) && in_array($_GET['action'], ['request', 'form', 'fill']);

// Render the comprehensive Burial Details & Request Form view
include __DIR__ . '/user_burial-details.php';
