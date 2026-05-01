<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$u = getDbConnection()->query("SELECT * FROM notifications WHERE message LIKE '%RPT-003%' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo json_encode($u);
