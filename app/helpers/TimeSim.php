<?php

class TimeSim {
    /**
     * Returns the simulated date/time for a specific user.
     * @param string|int|null $time Optional date string (e.g. "+1 month") or timestamp.
     * @param int|null $userId Specific user ID to check for offset.
     */
    public static function now($time = 'now', $userId = null) {
        $now = new \DateTime();
        
        // If $time is a specific absolute date string, just return that
        if ($time !== 'now' && !is_numeric($time)) {
             $now = new \DateTime($time, new \DateTimeZone('Asia/Manila'));
        } elseif (is_numeric($time)) {
             $now = new \DateTime("@$time", new \DateTimeZone('Asia/Manila'));
        } else {
             $now = new \DateTime('now', new \DateTimeZone('Asia/Manila'));
        }
        
        // Use provided userId, or fallback to session if available
        $uid = $userId ?? ($_SESSION['user_id'] ?? null);
        
        if ($uid) {
            $db = getDbConnection();
            $stmt = $db->prepare("SELECT time_offset FROM tenant_accounts WHERE tenant_id = ?");
            $stmt->execute([$uid]);
            $offset = $stmt->fetchColumn();
            
            if ($offset) {
                @$now->modify($offset);
            }
        }
        
        return $now;
    }

    /**
     * Simulated replacement for PHP date()
     */
    public static function date($format, $timestamp = null, $userId = null) {
        $time = ($timestamp === null) ? 'now' : $timestamp;
        return self::now($time, $userId)->format($format);
    }
}
