<?php
/**
 * Quick diagnostic: Check what payments exist in the DB for the current tenant
 * Access: http://localhost/Iscag/ISCAG-MIS/scratch/debug_payment_state.php
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

session_start();

$userId = $_SESSION['user_id'] ?? 'NOT SET';
echo "<h2>Session Info</h2>";
echo "<pre>";
echo "user_id: $userId\n";
echo "session_id: " . session_id() . "\n";
echo "paymongo_pending: " . print_r($_SESSION['paymongo_pending_payment'] ?? 'NONE', true) . "\n";
echo "</pre>";

if (!is_numeric($userId)) {
    die("Not logged in. Please log in first.");
}

$db = getDbConnection();

// Get lease
$stmt = $db->prepare("SELECT * FROM leases WHERE tenant_id = ? ORDER BY lease_id DESC LIMIT 1");
$stmt->execute([$userId]);
$lease = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h2>Lease</h2><pre>" . print_r($lease, true) . "</pre>";

if ($lease) {
    // Get ALL payments
    $stmt = $db->prepare("SELECT payment_id, lease_id, tenant_id, payment_type, payment_status, amount, reference_number, payment_date FROM payments WHERE lease_id = ? ORDER BY payment_id ASC");
    $stmt->execute([$lease['lease_id']]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>All Payments for Lease #{$lease['lease_id']} (" . count($payments) . " total)</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Type</th><th>Status</th><th>Amount</th><th>Reference</th><th>Date</th></tr>";
    foreach ($payments as $p) {
        $color = $p['payment_status'] === 'Paid' ? '#90EE90' : ($p['payment_status'] === 'Pending' ? '#FFD700' : '#FF6B6B');
        echo "<tr style='background:$color'>";
        echo "<td>{$p['payment_id']}</td>";
        echo "<td>{$p['payment_type']}</td>";
        echo "<td>{$p['payment_status']}</td>";
        echo "<td>" . number_format($p['amount'], 2) . "</td>";
        echo "<td>{$p['reference_number']}</td>";
        echo "<td>{$p['payment_date']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check for paid recurring keys
    echo "<h2>Paid Recurring Keys (lowercase)</h2><ul>";
    foreach ($payments as $p) {
        if ($p['payment_status'] === 'Paid') {
            $typeLower = strtolower($p['payment_type']);
            if (strpos($typeLower, '-') !== false) {
                echo "<li>$typeLower</li>";
            }
        }
    }
    echo "</ul>";
}
?>
