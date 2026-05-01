<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$stmt = getDbConnection()->query("SELECT email, count(*) as c FROM tenant_accounts group by email having c > 1");
print_r($stmt->fetchAll());
