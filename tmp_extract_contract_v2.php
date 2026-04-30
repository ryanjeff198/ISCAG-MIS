<?php
$data = file_get_contents('c:\xampp\htdocs\Iscag\app\views\Contract-2026.doc');
// Word docs (old) use OLE. Word docs (new) are ZIP. 
// If it has 'KASUNDUAN', it's likely readable text embedded.
// We remove non-printable and handle potential spacing.
$clean = '';
for ($i = 0; $i < strlen($data); $i++) {
    $c = $data[$i];
    $ord = ord($c);
    if (($ord >= 32 && $ord <= 126) || $ord == 10 || $ord == 13) {
        $clean .= $c;
    } else {
        $clean .= ' ';
    }
}
// Reduce multiple spaces
$clean = preg_replace('/\s+/', ' ', $clean);
echo $clean;
