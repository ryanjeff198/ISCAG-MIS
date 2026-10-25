<?php
require_once 'config/database.php';
$db = getDbConnection();

echo "=== ENCODED DEATH CERTS ===\n";
$stmt = $db->query("SELECT * FROM encoded_death_certificates");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "=== BURIAL REQUESTS ===\n";
$stmt2 = $db->query("SELECT id, ref_id, tenant_id, deceased_name, existing_cert_no, encoded_cert_id, existing_record_verified, status, submitted_at FROM burial_requests ORDER BY id ASC");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));






