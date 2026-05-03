<?php
/**
 * ISCAG User-Specific Time Jump Command
 * Usage: 
 *   php timejump.php [email] [+offset]
 *   php timejump.php jjfelizardoph@gmail.com +1 month
 *   php timejump.php jjfelizardoph@gmail.com reset
 */

require_once __DIR__ . '/config/database.php';
$db = getDbConnection();

$args = array_slice($argv, 1);

if (count($args) < 2) {
    echo "Usage:\n";
    echo "  php timejump.php [email] [+offset]\n";
    echo "  Example: php timejump.php jjfelizardoph@gmail.com +1 month\n";
    echo "  Example: php timejump.php jjfelizardoph@gmail.com reset\n\n";
    exit;
}

$email = $args[0];
$offset = implode(' ', array_slice($args, 1));

// Find user
$stmt = $db->prepare("SELECT tenant_id, first_name, last_name, time_offset FROM tenant_accounts WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Error: User with email '$email' not found.\n";
    exit;
}

if ($offset === 'reset') {
    $stmt = $db->prepare("UPDATE tenant_accounts SET time_offset = NULL WHERE tenant_id = ?");
    $stmt->execute([$user['tenant_id']]);
    echo "Simulation reset for {$user['first_name']} {$user['last_name']} (Real time).\n";
    exit;
}

// Validate offset
try {
    $test = new DateTime('now');
    @$test->modify($offset);
} catch (Exception $e) {
    echo "Error: Invalid time modifier '$offset'.\n";
    exit;
}

// Update database
$stmt = $db->prepare("UPDATE tenant_accounts SET time_offset = ? WHERE tenant_id = ?");
$stmt->execute([$offset, $user['tenant_id']]);

echo "Time jump successful for {$user['first_name']} {$user['last_name']}!\n";
echo "Active Offset: $offset\n";
echo "Simulated Date: " . $test->format('Y-m-d H:i:s') . "\n";
echo "Note: This ONLY affects this specific user.\n";
