<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$db = getDbConnection();
$reqs = $db->query("SELECT id, ref_id, tenant_id, deceased_name, status, death_cert_status, cemetery_plot_status FROM burial_requests ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
print_r($reqs);
