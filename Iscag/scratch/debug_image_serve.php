<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

// 1. Check what's in apartment_record_confirmations
echo "=== Confirmation Records ===\n";
$res = $db->query("SELECT id, tenant_id, roomtype, status FROM apartment_record_confirmations ORDER BY id");
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "  ID={$r['id']}, tenant_id={$r['tenant_id']}, room={$r['roomtype']}, status={$r['status']}\n";
}

// 2. Check tenant_requirements columns
echo "\n=== tenant_requirements table columns ===\n";
$res = $db->query("DESCRIBE tenant_requirements");
$cols = $res->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo "  {$c['Field']} ({$c['Type']})\n";
}

// 3. Test the actual image retrieval for each confirmation tenant_id
echo "\n=== Image retrieval test ===\n";
foreach ($rows as $r) {
    $tid = $r['tenant_id'];
    $stmt = $db->prepare("SELECT 
        LENGTH(picture) as pic_len, picture_mime,
        (picture IS NOT NULL AND LENGTH(picture) > 0) as has_pic
        FROM tenant_requirements WHERE tenant_id = :uid LIMIT 1");
    $stmt->execute(['uid' => $tid]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($img) {
        echo "  tenant_id=$tid: has_picture=" . ($img['has_pic'] ? 'YES' : 'NO') 
             . ", length=" . ($img['pic_len'] ?? 'NULL') 
             . ", mime=" . ($img['picture_mime'] ?? 'NULL') . "\n";
        
        // Test base64_decode
        if ($img['has_pic']) {
            $stmt2 = $db->prepare("SELECT picture FROM tenant_requirements WHERE tenant_id = :uid LIMIT 1");
            $stmt2->execute(['uid' => $tid]);
            $raw = $stmt2->fetchColumn();
            $decoded = base64_decode($raw, true);
            if ($decoded === false) {
                echo "    *** base64_decode FAILED! Data may not be base64-encoded ***\n";
                echo "    First 50 chars: " . substr($raw, 0, 50) . "\n";
            } else {
                echo "    base64_decode OK, decoded size=" . strlen($decoded) . " bytes\n";
                // Check if it starts with a known image signature
                $hex = bin2hex(substr($decoded, 0, 4));
                echo "    Magic bytes: $hex\n";
                if (str_starts_with($hex, 'ffd8ff')) echo "    -> Valid JPEG!\n";
                elseif (str_starts_with($hex, '89504e47')) echo "    -> Valid PNG!\n";
                else echo "    -> Unknown format\n";
            }
        }
    } else {
        echo "  tenant_id=$tid: NO RECORD in tenant_requirements\n";
    }
}
