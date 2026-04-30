<?php
require 'config/database.php';
$db = getDbConnection();

$q = $db->query('SELECT * FROM apartment_units');
$units = $q->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($units) . " units to process...\n";

foreach ($units as $u) {
    $current = $u['room_number'];
    $building = $u['building'] ?? 'Building 1';
    
    // Extract building digit
    preg_match('/\d+/', $building, $bMatches);
    $bDigit = isset($bMatches[0]) ? $bMatches[0] : '1';
    
    // Clean room number
    $rDigits = preg_replace('/\D/', '', $current);
    
    $newRoomNumber = $current;
    
    if (strlen($rDigits) < 3 && strlen($rDigits) > 0) {
        // Old style (e.g. "1"), convert to "101" (assuming 1st floor)
        $newRoomNumber = "1" . str_pad($rDigits, 2, '0', STR_PAD_LEFT);
    } elseif (strlen($rDigits) == 4) {
        // If it's already "1101", we should probably strip the building digit if we want to store only [F][RR]
        // Actually, the user's latest request says "Building 1 + Floor 2 + Room 01 = 1201"
        // And my UI code does: bNum + rNum (where rNum is the last 3 digits)
        // So if it's "1101" in the DB, it works fine.
        // But to be consistent with the new modal (which saves [F][RR]), let's save only the last 3 digits.
        $newRoomNumber = substr($rDigits, -3);
    }
    
    if ($newRoomNumber !== $current) {
        $stmt = $db->prepare('UPDATE apartment_units SET room_number = ? WHERE unit_id = ?');
        $stmt->execute([$newRoomNumber, $u['unit_id']]);
        echo "Updated Unit ID {$u['unit_id']}: '{$current}' -> '{$newRoomNumber}'\n";
    }
}

echo "Migration complete.\n";
