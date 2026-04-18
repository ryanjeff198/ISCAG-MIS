<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/models/ApartmentApp.php';

$tid = 4;
$type = 'picture';

$model = new ApartmentApp();
$result = $model->getRequirementImage($tid, $type);

if ($result) {
    echo "MIME: " . $result['mime'] . "\n";
    echo "Length: " . strlen($result['data']) . " bytes\n";
    echo "First 10 bytes (hex): " . bin2hex(substr($result['data'], 0, 10)) . "\n";
} else {
    echo "No image found for User $tid ($type)\n";
}
