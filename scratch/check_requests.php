<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$rows = $db->query('SELECT id, ref_id, tenant_id, deceased_name, status, death_cert_status, cemetery_plot_status FROM burial_requests ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
