<?php
$c = file_get_contents(__DIR__ . '/../app/controllers/AdminController.php');
preg_match_all('/function\s+(\w+)/', $c, $m, PREG_OFFSET_CAPTURE);
foreach ($m[1] as $match) {
    if (stripos($match[0], 'burial') !== false || stripos($match[0], 'damayan') !== false || stripos($match[0], 'cert') !== false) {
        $line = substr_count(substr($c, 0, $match[1]), "\n") + 1;
        echo "Line $line: {$match[0]}\n";
    }
}
