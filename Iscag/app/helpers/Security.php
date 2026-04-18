<?php

/**
 * Security Helper
 * Provides CSRF protection and XSS escaping.
 */
class Security
{
    /**
     * Escape string for safe HTML output (XSS protection).
     */
    public static function e(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate or retrieve a CSRF token for the current session.
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify the submitted CSRF token.
     */
    public static function validateCsrf(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Short helper function for escaping.
 */
function e(string $string): string
{
    return Security::e($string);
}
