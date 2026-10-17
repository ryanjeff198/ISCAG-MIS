<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/controllers/Controller.php';

// check file encoding of AdminController.php
$content = file_get_contents(BASE_PATH . '/app/controllers/AdminController.php');
if (substr($content, 0, 2) === "\xFF\xFE" || substr($content, 0, 2) === "\xFE\xFF") {
    echo "AdminController is UTF-16!\n";
} else {
    echo "AdminController starts with: " . bin2hex(substr($content, 0, 10)) . "\n";
}

require_once BASE_PATH . '/app/controllers/AdminController.php';
$rc = new ReflectionClass('AdminController');
foreach ($rc->getMethods() as $m) {
    if (stripos($m->getName(), 'damayan') !== false || stripos($m->getName(), 'burial') !== false) {
        echo $m->getName() . " at lines " . $m->getStartLine() . "-" . $m->getEndLine() . "\n";
    }
}
