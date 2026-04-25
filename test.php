<?php
define('BASE_PATH', __DIR__);
require 'config/database.php';
$db = getDbConnection();
$stmt = $db->query("SELECT * FROM apartmentsapp ORDER BY application_id DESC LIMIT 5");
var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));