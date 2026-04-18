<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

$tid = $_GET['tid'] ?? 4;
$q = $db->prepare("SELECT 
    valididfront_mime, valididback_mime, birthcert_mime, nbi_mime, picture_mime, proofofincome_mime,
    LENGTH(valididfront) as len_idfront,
    LENGTH(birthcert) as len_birth,
    LENGTH(proofofincome) as len_income
    FROM tenant_requirements WHERE tenant_id = ?");
$q->execute([$tid]);
$r = $q->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($r, JSON_PRETTY_PRINT);
