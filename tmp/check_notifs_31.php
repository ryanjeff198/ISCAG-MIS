<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$u = getDbConnection()->query("SELECT * FROM notifications WHERE tenant_id = 31")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($u);
