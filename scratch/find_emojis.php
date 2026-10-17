<?php

function scanDirEmojis($dir) {
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = $dir . '/' . $f;
        if (is_dir($path)) {
            scanDirEmojis($path);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $lines = file($path);
            foreach ($lines as $num => $line) {
                if (preg_match('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1FA00}-\x{1FAFF}]/u', $line, $matches)) {
                    echo $path . ' line ' . ($num + 1) . ': ' . trim($line) . PHP_EOL;
                }
            }
        }
    }
}

scanDirEmojis('c:/xampp/htdocs/ISCAG-MIS/app/views/user/Damayan');
scanDirEmojis('c:/xampp/htdocs/ISCAG-MIS/app/views/admin/Staff_Admin/Admin-Damayan_Department');
