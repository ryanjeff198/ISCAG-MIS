<?php
$data = file_get_contents('c:\xampp\htdocs\Iscag\app\views\Contract-2026.doc');
$search = "RYAN";
$pos = strpos($data, $search);
if ($pos !== false) {
    $start = max(0, $pos - 200);
    $length = 2000;
    $chunk = substr($data, $start, $length);
    // Remove nulls and non-printables
    $clean = '';
    for ($i = 0; $i < strlen($chunk); $i++) {
        $ord = ord($chunk[$i]);
        if (($ord >= 32 && $ord <= 126) || $ord == 10 || $ord == 13) {
            $clean .= $chunk[$i];
        } else {
            // Keep potential Tagalog vowels/accents if any (usually not in basic ASCII)
            $clean .= '.';
        }
    }
    echo $clean;
} else {
    echo "Pattern not found";
}
