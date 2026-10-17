<?php
$content = file_get_contents('c:/xampp/htdocs/ISCAG-MIS/app/views/dashboard.php');
$lines = explode("\n", $content);
foreach ($lines as $idx => $line) {
    if (preg_match('/damayan|burial|death|cemetery|funeral/i', $line)) {
        echo ($idx + 1) . ": " . trim($line) . "\n";
    }
}
