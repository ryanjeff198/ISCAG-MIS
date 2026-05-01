<?php
$path = 'c:\xampp\htdocs\Iscag\app\views\Contract-2026.doc';
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(1);
}
$content = file_get_contents($path);
// Extract sequences of 10+ printable characters
preg_match_all('/[\x20-\x7E]{10,}/', $content, $matches);
foreach ($matches[0] as $match) {
    echo trim($match) . "\n";
}
