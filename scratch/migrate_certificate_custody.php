<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

try {
    $pdo = getDbConnection();
    echo "Connecting to database...\n";

    $colsStmt = $pdo->query("SHOW COLUMNS FROM burial_requests");
    $existingCols = [];
    while ($r = $colsStmt->fetch(PDO::FETCH_ASSOC)) {
        $existingCols[] = $r['Field'];
    }

    $colsToAdd = [
        'certificate_in_department'    => "TINYINT(1) NOT NULL DEFAULT 0 AFTER death_cert_status",
        'certificate_received_dept_at' => "DATETIME NULL AFTER certificate_in_department",
        'certificate_received_dept_by' => "VARCHAR(150) NULL AFTER certificate_received_dept_at",
        'certificate_custody_notes'    => "TEXT NULL AFTER certificate_received_dept_by"
    ];

    foreach ($colsToAdd as $col => $def) {
        if (!in_array($col, $existingCols)) {
            $sql = "ALTER TABLE burial_requests ADD COLUMN {$col} {$def}";
            $pdo->exec($sql);
            echo "Added column: {$col}\n";
        } else {
            echo "Column already exists: {$col}\n";
        }
    }

    // Also update existing records that already have scheduled release date or released at so they are treated as in department
    $updated = $pdo->exec("UPDATE burial_requests 
        SET certificate_in_department = 1,
            certificate_received_dept_at = COALESCE(release_scheduled_at, NOW()),
            certificate_received_dept_by = 'Damayan Admin'
        WHERE (scheduled_release_date IS NOT NULL OR certificate_released_at IS NOT NULL)
          AND certificate_in_department = 0");
    echo "Updated {$updated} existing scheduled/released requests to certificate_in_department = 1.\n";

    echo "Migration completed successfully.\n";
} catch (\Throwable $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
