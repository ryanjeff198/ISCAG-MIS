<?php
$data = file_get_contents('c:\xampp\htdocs\Iscag\app\views\Contract-2026.doc');
// Try UTF-16 search
$search = "R\x00Y\x00A\x00N";
$pos = strpos($data, $search);
if ($pos === false) {
    // Try regular search
    $search = "RYAN";
    $pos = strpos($data, $search);
}

if ($pos !== false) {
    $start = max(0, $pos - 400);
    $length = 4000;
    $chunk = substr($data, $start, $length);
    // Replace nulls with space to make UTF-16 quasi-readable
    $chunk = str_replace("\x00", "", $chunk);
    $clean = '';
    for ($i = 0; $i < strlen($chunk); $i++) {
        $ord = ord($chunk[$i]);
        if (($ord >= 32 && $ord <= 126) || $ord == 10 || $ord == 13) {
            $clean .= $chunk[$i];
        } else {
            $clean .= '.';
        }
    }
    echo $clean;
} else {
    echo "Pattern not found";
}
