<?php
require 'config/database.php';
try {
    $db = getDbConnection();
    $smt = $db->query("SELECT action, details, timestamp FROM audit_logs WHERE module = 'APARTMENT' ORDER BY audit_id DESC LIMIT 4");
    echo json_encode($smt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
