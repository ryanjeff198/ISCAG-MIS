<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$cols = $db->query("DESCRIBE tenant_accounts")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) echo "{$c['Field']} ({$c['Type']})\n";
