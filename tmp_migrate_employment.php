<?php
require 'config/database.php';
$db = getDbConnection();
try {
    $db->exec("ALTER TABLE tenant_addinfo ADD COLUMN is_iscag_employee TINYINT(1) DEFAULT 0 AFTER iscag_student_names, ADD COLUMN iscag_job_role VARCHAR(255) DEFAULT NULL AFTER is_iscag_employee");
    echo "Success: Columns added.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Success: Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
