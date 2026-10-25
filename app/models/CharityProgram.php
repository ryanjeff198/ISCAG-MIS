<?php

class CharityProgram
{
    public const OFFICIAL_ISCAG_ADDRESS = 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114';

    protected PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
        $this->initTables();
    }

    /**
     * Ensure all tables exist.
     */
    public function initTables(): void
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS `charity_donations_records` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `donation_ref` VARCHAR(50) NOT NULL UNIQUE,
                `donor_name` VARCHAR(255) NOT NULL,
                `donor_address` TEXT NULL,
                `donation_type` ENUM('monetary', 'in-kind') NOT NULL,
                `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `item_description` TEXT NULL,
                `quantity` INT NOT NULL DEFAULT 0,
                `allocated_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `allocated_quantity` INT NOT NULL DEFAULT 0,
                `date_received` DATE NOT NULL,
                `payment_method` VARCHAR(100) NULL,
                `payment_reference` VARCHAR(100) NULL,
                `received_by` VARCHAR(255) NOT NULL,
                `remarks` TEXT NULL,
                `ack_issued` TINYINT(1) NOT NULL DEFAULT 0,
                `ack_issued_at` DATETIME NULL,
                `ack_issued_by` VARCHAR(255) NULL,
                `status` ENUM('Available', 'Partially Allocated', 'Fully Allocated', 'Cancelled') NOT NULL DEFAULT 'Available',
                `created_by` INT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_donor (`donor_name`),
                INDEX idx_type (`donation_type`),
                INDEX idx_status (`status`),
                INDEX idx_date (`date_received`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS `charity_allocations` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `allocation_ref` VARCHAR(50) NOT NULL UNIQUE,
                `donation_id` INT NOT NULL,
                `donation_ref` VARCHAR(50) NOT NULL,
                `receiving_department` ENUM('Damayan', 'Dawah') NOT NULL,
                `allocation_type` ENUM('monetary', 'in-kind') NOT NULL,
                `allocated_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `allocated_quantity` INT NOT NULL DEFAULT 0,
                `distributed_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `distributed_quantity` INT NOT NULL DEFAULT 0,
                `purpose` TEXT NOT NULL,
                `allocation_date` DATE NOT NULL,
                `allocated_by` VARCHAR(255) NOT NULL,
                `remarks` TEXT NULL,
                `status` ENUM('Pending Distribution', 'Partially Distributed', 'Distributed', 'Cancelled') NOT NULL DEFAULT 'Pending Distribution',
                `created_by` INT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_alloc_dept (`receiving_department`),
                INDEX idx_alloc_status (`status`),
                INDEX idx_alloc_date (`allocation_date`),
                CONSTRAINT `fk_alloc_donation` FOREIGN KEY (`donation_id`) REFERENCES `charity_donations_records`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS `charity_distributions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `distribution_ref` VARCHAR(50) NOT NULL UNIQUE,
                `allocation_id` INT NOT NULL,
                `allocation_ref` VARCHAR(50) NOT NULL,
                `donation_id` INT NOT NULL,
                `donation_ref` VARCHAR(50) NOT NULL,
                `receiving_department` ENUM('Damayan', 'Dawah') NOT NULL,
                `distribution_type` ENUM('monetary', 'in-kind') NOT NULL,
                `distributed_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `distributed_quantity` INT NOT NULL DEFAULT 0,
                `items_summary` TEXT NULL,
                `purpose` TEXT NOT NULL,
                `distribution_date` DATE NOT NULL,
                `distributed_by` VARCHAR(255) NOT NULL,
                `received_by` VARCHAR(255) NOT NULL,
                `supporting_document` VARCHAR(255) NULL,
                `status` ENUM('Distributed', 'Cancelled') NOT NULL DEFAULT 'Distributed',
                `remarks` TEXT NULL,
                `created_by` INT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_dist_dept (`receiving_department`),
                INDEX idx_dist_date (`distribution_date`),
                INDEX idx_dist_alloc (`allocation_id`),
                CONSTRAINT `fk_dist_allocation` FOREIGN KEY (`allocation_id`) REFERENCES `charity_allocations`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS `charity_activity_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `action_type` VARCHAR(50) NOT NULL,
                `reference_no` VARCHAR(50) NOT NULL,
                `details` TEXT NOT NULL,
                `performed_by` VARCHAR(255) NOT NULL,
                `user_id` INT NULL,
                `ip_address` VARCHAR(45) NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_act_ref (`reference_no`),
                INDEX idx_act_type (`action_type`),
                INDEX idx_act_created (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];

        foreach ($queries as $q) {
            try {
                $this->db->exec($q);
            } catch (PDOException $e) {
                // Table already exists or foreign key already exists
            }
        }
    }

    /**
     * Generate unique reference numbers
     */
    public function generateDonationRef(): string
    {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(id) FROM charity_donations_records");
        $next = ((int)$stmt->fetchColumn()) + 1;
        return sprintf("DON-%s-%04d", $year, $next);
    }

    public function generateAllocationRef(): string
    {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(id) FROM charity_allocations");
        $next = ((int)$stmt->fetchColumn()) + 1;
        return sprintf("ALC-%s-%04d", $year, $next);
    }

    public function generateDistributionRef(): string
    {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(id) FROM charity_distributions");
        $next = ((int)$stmt->fetchColumn()) + 1;
        return sprintf("DST-%s-%04d", $year, $next);
    }

    /**
     * Log activity
     */
    public function logActivity(string $actionType, string $refNo, string $details, string $performedBy, ?int $userId = null): void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO charity_activity_logs (action_type, reference_no, details, performed_by, user_id, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $actionType,
                $refNo,
                $details,
                $performedBy,
                $userId,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (PDOException $e) {
            // Silently ignore logging failures
        }
    }

    /**
     * Record a new donation
     */
    public function recordDonation(array $data, string $performedBy, ?int $userId = null): array
    {
        $donorName = trim($data['donor_name'] ?? '');
        $category = trim($data['donation_category'] ?? '');
        $donationType = $data['donation_type'] ?? '';

        // Auto-detect or normalize category & donation_type
        if (empty($category)) {
            if ($donationType === 'in-kind') {
                $category = 'food';
            } elseif (!empty($data['payment_method']) && stripos($data['payment_method'], 'cash') !== false) {
                $category = 'cash';
            } else {
                $category = 'digital';
            }
        }

        if ($category === 'food') {
            $donationType = 'in-kind';
        } else {
            $donationType = 'monetary';
        }

        $dateReceived = $data['date_received'] ?? date('Y-m-d');
        $receivedBy = trim($data['received_by'] ?? ($userId ? 'Online Portal (Pending Staff Verification)' : $performedBy));
        $donorAddress = trim($data['donor_address'] ?? '');
        $paymentMethod = trim($data['payment_method'] ?? ($category === 'cash' ? 'Cash (ISCAG Office)' : 'GCash'));
        $paymentRef = trim($data['payment_reference'] ?? '');
        $remarks = trim($data['remarks'] ?? '');
        $targetProgram = trim($data['target_program'] ?? ($category === 'food' ? 'Food Security & Ramadan Ration' : 'General Welfare & Relief'));
        $receiptPath = trim($data['receipt_path'] ?? '');
        $preferredDate = !empty($data['preferred_dropoff_date']) ? $data['preferred_dropoff_date'] : null;
        $dropoffLocation = trim($data['dropoff_location'] ?? self::OFFICIAL_ISCAG_ADDRESS);
        $unit = trim($data['unit'] ?? 'packs');

        if (empty($donorName)) {
            return ['success' => false, 'error' => 'Donor or sponsor name is required.'];
        }

        $amount = 0.00;
        $quantity = 0;
        $itemDesc = null;

        if ($donationType === 'monetary') {
            $amount = (float)($data['amount'] ?? 0);
            if ($amount <= 0) {
                return ['success' => false, 'error' => 'Please enter a valid donation amount greater than ₱0.00.'];
            }
        } else {
            $quantity = (int)($data['quantity'] ?? 0);
            $itemDesc = trim($data['item_description'] ?? '');
            if ($quantity <= 0) {
                return ['success' => false, 'error' => 'Please enter a valid item quantity greater than 0.'];
            }
            if (empty($itemDesc)) {
                return ['success' => false, 'error' => 'Food item or supply description is required.'];
            }
        }

        // Determine initial status based on category if not explicitly provided
        $status = $data['status'] ?? null;
        if (empty($status)) {
            if ($category === 'food') {
                $status = 'Pending Coordination';
            } elseif ($category === 'cash') {
                $status = 'Pending Receipt';
            } else {
                $status = 'Pending Verification';
            }
        }

        $tenantId = !empty($data['tenant_id']) ? (int)$data['tenant_id'] : null;
        $donationRef = $this->generateDonationRef();

        try {
            $stmt = $this->db->prepare("INSERT INTO charity_donations_records (
                tenant_id, donation_ref, donor_name, donor_address, donation_type, donation_category,
                amount, item_description, target_program, quantity, unit, allocated_amount, allocated_quantity,
                date_received, preferred_dropoff_date, dropoff_location, payment_method, payment_reference,
                received_by, remarks, receipt_path, ack_issued, status, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 0, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)");

            $stmt->execute([
                $tenantId, $donationRef, $donorName, $donorAddress, $donationType, $category,
                $amount, $itemDesc, $targetProgram, $quantity, $unit,
                $dateReceived, $preferredDate, $dropoffLocation, $paymentMethod, $paymentRef,
                $receivedBy, $remarks, $receiptPath, $status, $userId
            ]);

            $id = (int)$this->db->lastInsertId();

            $logDesc = sprintf(
                "Recorded %s (%s) donation of %s from %s [Status: %s]",
                $category,
                $donationType,
                $donationType === 'monetary' ? '₱' . number_format($amount, 2) : "{$quantity} {$unit} {$itemDesc}",
                $donorName,
                $status
            );
            $this->logActivity('DONATION_RECORDED', $donationRef, $logDesc, $performedBy, $userId);

            // Forward alert to Charity Admin notifications
            try {
                $notifTitle = match($category) {
                    'food' => "New Food Donation Request ({$donationRef})",
                    'cash' => "New Cash Donation Record ({$donationRef})",
                    default => "New Digital Donation ({$donationRef}) via {$paymentMethod}"
                };
                $notifMsg = match($category) {
                    'food' => "{$donorName} requested coordination for {$quantity} {$unit} of {$itemDesc}. Preferred Drop-off: " . ($preferredDate ?: 'TBD') . ".",
                    'cash' => "{$donorName} submitted a cash donation record of ₱" . number_format($amount, 2) . " for {$targetProgram}.",
                    default => "{$donorName} submitted ₱" . number_format($amount, 2) . " via {$paymentMethod} (Ref: " . ($paymentRef ?: 'N/A') . "). Proof uploaded, awaiting verification."
                };
                $sourceUrl = null;
                $notifStmt = $this->db->prepare("INSERT INTO admin_notifications (title, message, type, actor_name, actor_id, source_url, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
                $notifStmt->execute([$notifTitle, $notifMsg, 'charity', $donorName, $tenantId, $sourceUrl]);
            } catch (Exception $ne) {
                // Ignore admin notification failure
            }

            return ['success' => true, 'id' => $id, 'donation_ref' => $donationRef, 'status' => $status];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function updateDonation(int $id, array $data, string $performedBy, ?int $userId = null): array
    {
        $existing = $this->getDonationById($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'Donation record not found.'];
        }

        $donorName = trim($data['donor_name'] ?? $existing['donor_name']);
        $donorAddress = trim($data['donor_address'] ?? $existing['donor_address']);
        $dateReceived = $data['date_received'] ?? $existing['date_received'];
        $receivedBy = trim($data['received_by'] ?? $existing['received_by']);
        $paymentMethod = trim($data['payment_method'] ?? $existing['payment_method']);
        $paymentRef = trim($data['payment_reference'] ?? $existing['payment_reference']);
        $remarks = trim($data['remarks'] ?? $existing['remarks']);

        // Check if amount or quantity modification requested
        $amount = (float)$existing['amount'];
        $quantity = (int)$existing['quantity'];
        $itemDesc = $existing['item_description'];

        if (isset($data['amount']) && $existing['donation_type'] === 'monetary') {
            $newAmt = (float)$data['amount'];
            if ($newAmt < (float)$existing['allocated_amount']) {
                return ['success' => false, 'error' => 'Cannot reduce amount below already allocated ₱' . number_format($existing['allocated_amount'], 2) . '.'];
            }
            $amount = $newAmt;
        }

        if (isset($data['quantity']) && $existing['donation_type'] === 'in-kind') {
            $newQty = (int)$data['quantity'];
            if ($newQty < (int)$existing['allocated_quantity']) {
                return ['success' => false, 'error' => 'Cannot reduce quantity below already allocated ' . $existing['allocated_quantity'] . ' units.'];
            }
            $quantity = $newQty;
            if (!empty($data['item_description'])) {
                $itemDesc = trim($data['item_description']);
            }
        }

        // Recalculate status
        $status = 'Available';
        if ($existing['donation_type'] === 'monetary') {
            if ($existing['allocated_amount'] >= $amount) {
                $status = 'Fully Allocated';
            } elseif ($existing['allocated_amount'] > 0) {
                $status = 'Partially Allocated';
            }
        } else {
            if ($existing['allocated_quantity'] >= $quantity) {
                $status = 'Fully Allocated';
            } elseif ($existing['allocated_quantity'] > 0) {
                $status = 'Partially Allocated';
            }
        }

        try {
            $stmt = $this->db->prepare("UPDATE charity_donations_records SET
                donor_name = ?, donor_address = ?, date_received = ?, received_by = ?,
                payment_method = ?, payment_reference = ?, remarks = ?,
                amount = ?, quantity = ?, item_description = ?, status = ?
                WHERE id = ?");
            $stmt->execute([
                $donorName, $donorAddress, $dateReceived, $receivedBy,
                $paymentMethod, $paymentRef, $remarks,
                $amount, $quantity, $itemDesc, $status,
                $id
            ]);

            $this->logActivity('DONATION_EDITED', $existing['donation_ref'], "Updated donation details for {$donorName}", $performedBy, $userId);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to update donation: ' . $e->getMessage()];
        }
    }

    /**
     * Toggle or issue acknowledgment receipt
     */
    public function issueAcknowledgment(int $id, string $staffName, ?int $userId = null): array
    {
        $donation = $this->getDonationById($id);
        if (!$donation) {
            return ['success' => false, 'error' => 'Donation record not found.'];
        }

        $newIssued = $donation['ack_issued'] ? 0 : 1;
        $issuedAt = $newIssued ? date('Y-m-d H:i:s') : null;
        $issuedBy = $newIssued ? $staffName : null;

        try {
            $stmt = $this->db->prepare("UPDATE charity_donations_records SET ack_issued = ?, ack_issued_at = ?, ack_issued_by = ? WHERE id = ?");
            $stmt->execute([$newIssued, $issuedAt, $issuedBy, $id]);

            $action = $newIssued ? "Issued donation acknowledgment receipt" : "Revoked donation acknowledgment status";
            $this->logActivity('ACK_ISSUED', $donation['donation_ref'], $action, $staffName, $userId);

            return ['success' => true, 'ack_issued' => $newIssued, 'ack_issued_at' => $issuedAt, 'ack_issued_by' => $issuedBy];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to update acknowledgment: ' . $e->getMessage()];
        }
    }

    /**
     * Get donation by ID with full allocations and distributions
     */
    public function getDonationById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM charity_donations_records WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        // Add calculated available fields
        $row['available_amount'] = max(0, (float)$row['amount'] - (float)$row['allocated_amount']);
        $row['available_quantity'] = max(0, (int)$row['quantity'] - (int)$row['allocated_quantity']);

        // Load allocations
        $stmtAlloc = $this->db->prepare("SELECT * FROM charity_allocations WHERE donation_id = ? ORDER BY allocation_date DESC, id DESC");
        $stmtAlloc->execute([$id]);
        $row['allocations'] = $stmtAlloc->fetchAll(PDO::FETCH_ASSOC);

        // Load distributions
        $stmtDist = $this->db->prepare("SELECT * FROM charity_distributions WHERE donation_id = ? ORDER BY distribution_date DESC, id DESC");
        $stmtDist->execute([$id]);
        $row['distributions'] = $stmtDist->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * Get donation by Ref
     */
    public function getDonationByRef(string $ref): ?array
    {
        $stmt = $this->db->prepare("SELECT id FROM charity_donations_records WHERE donation_ref = ?");
        $stmt->execute([$ref]);
        $id = $stmt->fetchColumn();
        return $id ? $this->getDonationById((int)$id) : null;
    }

    /**
     * Get all donations with filtering
     */

    /**
     * Verify or update status of submitted donation (Digital, Cash, or Food)
     */
    public function verifyDonation(int $id, string $newStatus, ?string $adminNotes, string $staffName, ?int $userId = null): array
    {
        $donation = $this->getDonationById($id);
        if (!$donation) {
            return ['success' => false, 'error' => 'Donation record not found.'];
        }

        $validStatuses = ['Available', 'Pending Verification', 'Pending Coordination', 'Pending Receipt', 'Rejected', 'Cancelled', 'Partially Allocated', 'Fully Allocated'];
        if (!in_array($newStatus, $validStatuses)) {
            return ['success' => false, 'error' => 'Invalid donation status specified.'];
        }

        try {
            $stmt = $this->db->prepare("UPDATE charity_donations_records SET status = ?, admin_notes = ?, received_by = ? WHERE id = ?");
            $stmt->execute([$newStatus, $adminNotes, $staffName, $id]);

            $logDesc = "Updated donation {$donation['donation_ref']} status to {$newStatus}. Admin notes: {$adminNotes}";
            $this->logActivity('DONATION_VERIFIED', $donation['donation_ref'], $logDesc, $staffName, $userId);

            // Send notification to donor if linked to tenant
            if (!empty($donation['tenant_id'])) {
                try {
                    $uStmt = $this->db->prepare("INSERT INTO notifications (tenant_id, title, message, type, is_read, created_at) VALUES (?, ?, ?, 'charity', 0, NOW())");
                    $uTitle = "Charity Donation Status: {$newStatus} ({$donation['donation_ref']})";
                    if ($newStatus === 'Available') {
                        $uMsg = "Your {$donation['donation_category']} donation ({$donation['donation_ref']}) has been officially verified and confirmed by ISCAG Damayan staff. Jazakumullahu Khayran!";
                    } elseif ($newStatus === 'Rejected') {
                        $uMsg = "Your donation submission ({$donation['donation_ref']}) was reviewed. Staff note: " . ($adminNotes ?: 'Please review submission details.');
                    } else {
                        $uMsg = "Your donation ({$donation['donation_ref']}) status was updated to {$newStatus}." . ($adminNotes ? " Note: {$adminNotes}" : "");
                    }
                    $uStmt->execute([(int)$donation['tenant_id'], $uTitle, $uMsg]);
                } catch (Exception $ue) {
                    // Ignore user notification failure
                }
            }

            return ['success' => true, 'status' => $newStatus, 'donation_ref' => $donation['donation_ref']];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Failed to update donation verification: ' . $e->getMessage()];
        }
    }

    public function getAllDonations(array $filters = []): array
    {
        $sql = "SELECT * FROM charity_donations_records WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $sql .= " AND (donation_ref LIKE ? OR donor_name LIKE ? OR payment_reference LIKE ? OR item_description LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['donation_type']) && in_array($filters['donation_type'], ['monetary', 'in-kind'])) {
            $sql .= " AND donation_type = ?";
            $params[] = $filters['donation_type'];
        }

        if (!empty($filters['category']) && in_array($filters['category'], ['digital', 'cash', 'food'])) {
            $sql .= " AND donation_category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND date_received >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND date_received <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY date_received DESC, id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['available_amount'] = max(0, (float)$r['amount'] - (float)$r['allocated_amount']);
            $r['available_quantity'] = max(0, (int)$r['quantity'] - (int)$r['allocated_quantity']);
        }

        return $rows;
    }

    /**
     * Get donations made by a specific tenant
     */
    public function getDonationsByTenant(int $tenantId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM charity_donations_records WHERE tenant_id = ? ORDER BY date_received DESC, id DESC");
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get summary breakdown by target program
     */
    public function getProgramsSummary(): array
    {
        $sql = "SELECT 
                    COALESCE(NULLIF(target_program, ''), 'General Welfare & Relief') as program_name,
                    SUM(amount) as total_amount,
                    COUNT(*) as donation_count
                FROM charity_donations_records
                WHERE status != 'Cancelled'
                GROUP BY COALESCE(NULLIF(target_program, ''), 'General Welfare & Relief')
                ORDER BY total_amount DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get available donations for allocation
     */
    public function getAvailableDonations(array $filters = []): array
    {
        $all = $this->getAllDonations($filters);
        return array_values(array_filter($all, function($d) {
            if ($d['status'] === 'Cancelled' || $d['status'] === 'Fully Allocated') {
                return false;
            }
            if ($d['donation_type'] === 'monetary') {
                return $d['available_amount'] > 0;
            } else {
                return $d['available_quantity'] > 0;
            }
        }));
    }

    /**
     * Create an allocation
     */
    public function createAllocation(array $data, string $performedBy, ?int $userId = null): array
    {
        $donationId = (int)($data['donation_id'] ?? 0);
        $dept = trim($data['receiving_department'] ?? '');
        $purpose = trim($data['purpose'] ?? '');
        $allocDate = $data['allocation_date'] ?? date('Y-m-d');
        $allocatedBy = trim($data['allocated_by'] ?? $performedBy);
        $remarks = trim($data['remarks'] ?? '');

        if (!in_array($dept, ['Damayan', 'Dawah'])) {
            return ['success' => false, 'error' => 'Please select a valid receiving department (Damayan or Dawah).'];
        }
        if (empty($purpose)) {
            return ['success' => false, 'error' => 'Please specify the purpose of the allocation.'];
        }

        try {
            $this->db->beginTransaction();

            // Lock donation row for update to prevent race conditions
            $stmt = $this->db->prepare("SELECT * FROM charity_donations_records WHERE id = ? FOR UPDATE");
            $stmt->execute([$donationId]);
            $donation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$donation) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Selected donation not found.'];
            }
            if ($donation['status'] === 'Cancelled') {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Cannot allocate from a cancelled donation.'];
            }

            $allocAmt = 0.00;
            $allocQty = 0;
            $allocType = $donation['donation_type'];

            if ($allocType === 'monetary') {
                $allocAmt = (float)($data['allocated_amount'] ?? 0);
                $availableAmt = (float)$donation['amount'] - (float)$donation['allocated_amount'];
                if ($allocAmt <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Please enter an allocation amount greater than ₱0.00.'];
                }
                if ($allocAmt > $availableAmt) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Allocation amount (₱' . number_format($allocAmt, 2) . ') exceeds available balance of ₱' . number_format($availableAmt, 2) . '.'];
                }
            } else {
                $allocQty = (int)($data['allocated_quantity'] ?? 0);
                $availableQty = (int)$donation['quantity'] - (int)$donation['allocated_quantity'];
                if ($allocQty <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Please enter an allocation quantity greater than 0.'];
                }
                if ($allocQty > $availableQty) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Allocation quantity (' . $allocQty . ') exceeds available quantity of ' . $availableQty . ' units.'];
                }
            }

            $allocRef = $this->generateAllocationRef();

            // Insert allocation record
            $insStmt = $this->db->prepare("INSERT INTO charity_allocations (
                allocation_ref, donation_id, donation_ref, receiving_department, allocation_type,
                allocated_amount, allocated_quantity, distributed_amount, distributed_quantity,
                purpose, allocation_date, allocated_by, remarks, status, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 0.00, 0, ?, ?, ?, ?, 'Pending Distribution', ?)");

            $insStmt->execute([
                $allocRef, $donation['id'], $donation['donation_ref'], $dept, $allocType,
                $allocAmt, $allocQty,
                $purpose, $allocDate, $allocatedBy, $remarks, $userId
            ]);

            $allocId = (int)$this->db->lastInsertId();

            // Update parent donation allocated totals & status
            $newAllocatedAmt = (float)$donation['allocated_amount'] + $allocAmt;
            $newAllocatedQty = (int)$donation['allocated_quantity'] + $allocQty;

            $newStatus = 'Available';
            if ($allocType === 'monetary') {
                if ($newAllocatedAmt >= (float)$donation['amount']) {
                    $newStatus = 'Fully Allocated';
                } else {
                    $newStatus = 'Partially Allocated';
                }
            } else {
                if ($newAllocatedQty >= (int)$donation['quantity']) {
                    $newStatus = 'Fully Allocated';
                } else {
                    $newStatus = 'Partially Allocated';
                }
            }

            $updStmt = $this->db->prepare("UPDATE charity_donations_records SET allocated_amount = ?, allocated_quantity = ?, status = ? WHERE id = ?");
            $updStmt->execute([$newAllocatedAmt, $newAllocatedQty, $newStatus, $donation['id']]);

            $logDesc = sprintf(
                "Allocated %s to %s for '%s' from %s",
                $allocType === 'monetary' ? '₱' . number_format($allocAmt, 2) : "{$allocQty} units",
                $dept,
                $purpose,
                $donation['donation_ref']
            );
            $this->logActivity('ALLOCATION_CONFIRMED', $allocRef, $logDesc, $performedBy, $userId);

            $this->db->commit();
            return ['success' => true, 'id' => $allocId, 'allocation_ref' => $allocRef];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Cancel an allocation (only if no distributions made)
     */
    public function cancelAllocation(int $id, string $staffName, string $reason, ?int $userId = null): array
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT * FROM charity_allocations WHERE id = ? FOR UPDATE");
            $stmt->execute([$id]);
            $alloc = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$alloc) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Allocation record not found.'];
            }
            if ($alloc['status'] === 'Cancelled') {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Allocation is already cancelled.'];
            }
            if ((float)$alloc['distributed_amount'] > 0 || (int)$alloc['distributed_quantity'] > 0) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Cannot cancel an allocation that has active or completed distributions.'];
            }

            // Restore parent donation
            $donStmt = $this->db->prepare("SELECT * FROM charity_donations_records WHERE id = ? FOR UPDATE");
            $donStmt->execute([$alloc['donation_id']]);
            $donation = $donStmt->fetch(PDO::FETCH_ASSOC);

            if ($donation) {
                $newAllocAmt = max(0, (float)$donation['allocated_amount'] - (float)$alloc['allocated_amount']);
                $newAllocQty = max(0, (int)$donation['allocated_quantity'] - (int)$alloc['allocated_quantity']);

                $newStatus = 'Available';
                if ($donation['donation_type'] === 'monetary') {
                    if ($newAllocAmt >= (float)$donation['amount']) {
                        $newStatus = 'Fully Allocated';
                    } elseif ($newAllocAmt > 0) {
                        $newStatus = 'Partially Allocated';
                    }
                } else {
                    if ($newAllocQty >= (int)$donation['quantity']) {
                        $newStatus = 'Fully Allocated';
                    } elseif ($newAllocQty > 0) {
                        $newStatus = 'Partially Allocated';
                    }
                }

                $updDon = $this->db->prepare("UPDATE charity_donations_records SET allocated_amount = ?, allocated_quantity = ?, status = ? WHERE id = ?");
                $updDon->execute([$newAllocAmt, $newAllocQty, $newStatus, $donation['id']]);
            }

            // Update allocation
            $updAlloc = $this->db->prepare("UPDATE charity_allocations SET status = 'Cancelled', remarks = CONCAT(IFNULL(remarks,''), ' [Cancelled: ', ?, ']') WHERE id = ?");
            $updAlloc->execute([$reason ?: 'Cancelled by staff', $id]);

            $this->logActivity('ALLOCATION_CANCELLED', $alloc['allocation_ref'], "Cancelled allocation: " . ($reason ?: 'No reason given'), $staffName, $userId);

            $this->db->commit();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'error' => 'Failed to cancel allocation: ' . $e->getMessage()];
        }
    }

    /**
     * Get allocations with filters
     */
    public function getAllocations(array $filters = []): array
    {
        $sql = "SELECT a.*, d.donor_name, d.item_description, d.date_received as donation_date
                FROM charity_allocations a
                JOIN charity_donations_records d ON a.donation_id = d.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['department']) && in_array($filters['department'], ['Damayan', 'Dawah'])) {
            $sql .= " AND a.receiving_department = ?";
            $params[] = $filters['department'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['donation_ref'])) {
            $sql .= " AND a.donation_ref = ?";
            $params[] = $filters['donation_ref'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $sql .= " AND (a.allocation_ref LIKE ? OR a.donation_ref LIKE ? OR d.donor_name LIKE ? OR a.purpose LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND a.allocation_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND a.allocation_date <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY a.allocation_date DESC, a.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['remaining_allocated_amount'] = max(0, (float)$r['allocated_amount'] - (float)$r['distributed_amount']);
            $r['remaining_allocated_quantity'] = max(0, (int)$r['allocated_quantity'] - (int)$r['distributed_quantity']);
        }

        return $rows;
    }

    /**
     * Get allocation by ID with full distributions
     */
    public function getAllocationById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT a.*, d.donor_name, d.item_description, d.date_received as donation_date, d.amount as original_donation_amount, d.quantity as original_donation_quantity
                                    FROM charity_allocations a
                                    JOIN charity_donations_records d ON a.donation_id = d.id
                                    WHERE a.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        $row['remaining_allocated_amount'] = max(0, (float)$row['allocated_amount'] - (float)$row['distributed_amount']);
        $row['remaining_allocated_quantity'] = max(0, (int)$row['allocated_quantity'] - (int)$row['distributed_quantity']);

        // Fetch distributions
        $distStmt = $this->db->prepare("SELECT * FROM charity_distributions WHERE allocation_id = ? ORDER BY distribution_date DESC, id DESC");
        $distStmt->execute([$id]);
        $row['distributions'] = $distStmt->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * Get allocations that have undistributed balances
     */
    public function getUndistributedAllocations(): array
    {
        $sql = "SELECT a.*, d.donor_name, d.item_description
                FROM charity_allocations a
                JOIN charity_donations_records d ON a.donation_id = d.id
                WHERE a.status IN ('Pending Distribution', 'Partially Distributed')
                ORDER BY a.allocation_date DESC, a.id DESC";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['remaining_allocated_amount'] = max(0, (float)$r['allocated_amount'] - (float)$r['distributed_amount']);
            $r['remaining_allocated_quantity'] = max(0, (int)$r['allocated_quantity'] - (int)$r['distributed_quantity']);
        }

        return array_values(array_filter($rows, function($r) {
            if ($r['allocation_type'] === 'monetary') {
                return $r['remaining_allocated_amount'] > 0;
            } else {
                return $r['remaining_allocated_quantity'] > 0;
            }
        }));
    }

    /**
     * Record actual distribution
     */
    public function recordDistribution(array $data, ?string $docFilePath, string $performedBy, ?int $userId = null): array
    {
        $allocId = (int)($data['allocation_id'] ?? 0);
        $distDate = $data['distribution_date'] ?? date('Y-m-d');
        $distributedBy = trim($data['distributed_by'] ?? $performedBy);
        $receivedBy = trim($data['received_by'] ?? '');
        $purpose = trim($data['purpose'] ?? '');
        $remarks = trim($data['remarks'] ?? '');

        if (empty($receivedBy)) {
            return ['success' => false, 'error' => 'Please provide the name of the authorized recipient or beneficiary.'];
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT a.*, d.item_description FROM charity_allocations a JOIN charity_donations_records d ON a.donation_id = d.id WHERE a.id = ? FOR UPDATE");
            $stmt->execute([$allocId]);
            $alloc = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$alloc) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Linked allocation record not found.'];
            }
            if ($alloc['status'] === 'Cancelled') {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Cannot distribute from a cancelled allocation.'];
            }

            $distAmt = 0.00;
            $distQty = 0;
            $itemsSummary = '';

            if ($alloc['allocation_type'] === 'monetary') {
                $distAmt = (float)($data['distributed_amount'] ?? 0);
                $remAllocAmt = (float)$alloc['allocated_amount'] - (float)$alloc['distributed_amount'];
                if ($distAmt <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Please specify a distributed amount greater than ₱0.00.'];
                }
                if ($distAmt > $remAllocAmt) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Distributed amount (₱' . number_format($distAmt, 2) . ') exceeds remaining allocated balance of ₱' . number_format($remAllocAmt, 2) . '.'];
                }
                $itemsSummary = '₱' . number_format($distAmt, 2) . ' Cash Aid';
            } else {
                $distQty = (int)($data['distributed_quantity'] ?? 0);
                $remAllocQty = (int)$alloc['allocated_quantity'] - (int)$alloc['distributed_quantity'];
                if ($distQty <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Please specify a distributed quantity greater than 0.'];
                }
                if ($distQty > $remAllocQty) {
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Distributed quantity (' . $distQty . ') exceeds remaining allocated balance of ' . $remAllocQty . ' units.'];
                }
                $itemsSummary = "{$distQty}x " . ($alloc['item_description'] ?: 'In-Kind Items');
            }

            if (empty($purpose)) {
                $purpose = $alloc['purpose'];
            }

            $distRef = $this->generateDistributionRef();

            // Insert distribution
            $insStmt = $this->db->prepare("INSERT INTO charity_distributions (
                distribution_ref, allocation_id, allocation_ref, donation_id, donation_ref,
                receiving_department, distribution_type, distributed_amount, distributed_quantity,
                items_summary, purpose, distribution_date, distributed_by, received_by,
                supporting_document, status, remarks, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Distributed', ?, ?)");

            $insStmt->execute([
                $distRef, $alloc['id'], $alloc['allocation_ref'], $alloc['donation_id'], $alloc['donation_ref'],
                $alloc['receiving_department'], $alloc['allocation_type'], $distAmt, $distQty,
                $itemsSummary, $purpose, $distDate, $distributedBy, $receivedBy,
                $docFilePath, $remarks, $userId
            ]);

            $distId = (int)$this->db->lastInsertId();

            // Update allocation distributed totals and status
            $newDistributedAmt = (float)$alloc['distributed_amount'] + $distAmt;
            $newDistributedQty = (int)$alloc['distributed_quantity'] + $distQty;

            $newAllocStatus = 'Partially Distributed';
            if ($alloc['allocation_type'] === 'monetary') {
                if ($newDistributedAmt >= (float)$alloc['allocated_amount']) {
                    $newAllocStatus = 'Distributed';
                }
            } else {
                if ($newDistributedQty >= (int)$alloc['allocated_quantity']) {
                    $newAllocStatus = 'Distributed';
                }
            }

            $updAlloc = $this->db->prepare("UPDATE charity_allocations SET distributed_amount = ?, distributed_quantity = ?, status = ? WHERE id = ?");
            $updAlloc->execute([$newDistributedAmt, $newDistributedQty, $newAllocStatus, $alloc['id']]);

            $logDesc = sprintf(
                "Distributed %s to %s (Department: %s, Allocation: %s)",
                $itemsSummary,
                $receivedBy,
                $alloc['receiving_department'],
                $alloc['allocation_ref']
            );
            $this->logActivity('DISTRIBUTION_COMPLETED', $distRef, $logDesc, $performedBy, $userId);

            $this->db->commit();
            return ['success' => true, 'id' => $distId, 'distribution_ref' => $distRef];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get distributions with filters
     */
    public function getDistributions(array $filters = []): array
    {
        $sql = "SELECT dist.*, alloc.purpose as allocation_purpose, alloc.allocation_date, don.donor_name
                FROM charity_distributions dist
                JOIN charity_allocations alloc ON dist.allocation_id = alloc.id
                JOIN charity_donations_records don ON dist.donation_id = don.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['department']) && in_array($filters['department'], ['Damayan', 'Dawah'])) {
            $sql .= " AND dist.receiving_department = ?";
            $params[] = $filters['department'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND dist.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['donation_ref'])) {
            $sql .= " AND dist.donation_ref = ?";
            $params[] = $filters['donation_ref'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $sql .= " AND (dist.distribution_ref LIKE ? OR dist.allocation_ref LIKE ? OR dist.donation_ref LIKE ? OR dist.received_by LIKE ? OR dist.items_summary LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND dist.distribution_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND dist.distribution_date <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY dist.distribution_date DESC, dist.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get distribution by ID
     */
    public function getDistributionById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT dist.*, alloc.purpose as allocation_purpose, don.donor_name, don.item_description
                                    FROM charity_distributions dist
                                    JOIN charity_allocations alloc ON dist.allocation_id = alloc.id
                                    JOIN charity_donations_records don ON dist.donation_id = don.id
                                    WHERE dist.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Get real live dashboard statistics
     */
    public function getDashboardStats(): array
    {
        // 1. Total Donations Received
        $stmtRec = $this->db->query("SELECT
            COUNT(*) as total_count,
            SUM(CASE WHEN donation_type = 'monetary' THEN amount ELSE 0 END) as monetary_total,
            SUM(CASE WHEN donation_type = 'in-kind' THEN quantity ELSE 0 END) as in_kind_total,
            SUM(CASE WHEN donation_type = 'monetary' THEN 1 ELSE 0 END) as monetary_count,
            SUM(CASE WHEN donation_type = 'in-kind' THEN 1 ELSE 0 END) as in_kind_count
            FROM charity_donations_records
            WHERE status NOT IN ('Cancelled', 'Pending Verification', 'Pending Coordination', 'Pending Receipt', 'Rejected')");
        $rec = $stmtRec->fetch(PDO::FETCH_ASSOC) ?: [];

        // 2. Total Available Donations
        $stmtAvail = $this->db->query("SELECT
            COUNT(*) as available_count,
            SUM(CASE WHEN donation_type = 'monetary' THEN (amount - allocated_amount) ELSE 0 END) as available_amount,
            SUM(CASE WHEN donation_type = 'in-kind' THEN (quantity - allocated_quantity) ELSE 0 END) as available_quantity
            FROM charity_donations_records
            WHERE status IN ('Available', 'Partially Allocated')");
        $avail = $stmtAvail->fetch(PDO::FETCH_ASSOC) ?: [];

        // 3. Total Donations Allocated
        $stmtAlloc = $this->db->query("SELECT
            COUNT(*) as total_allocations,
            SUM(allocated_amount) as allocated_amount,
            SUM(allocated_quantity) as allocated_quantity,
            SUM(CASE WHEN receiving_department = 'Damayan' THEN allocated_amount ELSE 0 END) as damayan_allocated_amount,
            SUM(CASE WHEN receiving_department = 'Dawah' THEN allocated_amount ELSE 0 END) as dawah_allocated_amount,
            SUM(CASE WHEN receiving_department = 'Damayan' THEN allocated_quantity ELSE 0 END) as damayan_allocated_qty,
            SUM(CASE WHEN receiving_department = 'Dawah' THEN allocated_quantity ELSE 0 END) as dawah_allocated_qty
            FROM charity_allocations
            WHERE status != 'Cancelled'");
        $alloc = $stmtAlloc->fetch(PDO::FETCH_ASSOC) ?: [];

        // 4. Total Donations Distributed
        $stmtDist = $this->db->query("SELECT
            COUNT(*) as total_distributions,
            SUM(distributed_amount) as distributed_amount,
            SUM(distributed_quantity) as distributed_quantity,
            SUM(CASE WHEN receiving_department = 'Damayan' THEN distributed_amount ELSE 0 END) as damayan_distributed_amount,
            SUM(CASE WHEN receiving_department = 'Dawah' THEN distributed_amount ELSE 0 END) as dawah_distributed_amount,
            SUM(CASE WHEN receiving_department = 'Damayan' THEN distributed_quantity ELSE 0 END) as damayan_distributed_qty,
            SUM(CASE WHEN receiving_department = 'Dawah' THEN distributed_quantity ELSE 0 END) as dawah_distributed_qty
            FROM charity_distributions
            WHERE status = 'Distributed'");
        $dist = $stmtDist->fetch(PDO::FETCH_ASSOC) ?: [];

        // 5. Pending Distributions
        $stmtPending = $this->db->query("SELECT
            COUNT(*) as pending_count,
            SUM(allocated_amount - distributed_amount) as pending_amount,
            SUM(allocated_quantity - distributed_quantity) as pending_quantity
            FROM charity_allocations
            WHERE status IN ('Pending Distribution', 'Partially Distributed')");
        $pending = $stmtPending->fetch(PDO::FETCH_ASSOC) ?: [];

        // 6. Pending Donation Verifications (Digital, Cash, Food)
        $stmtPendingVerif = $this->db->query("SELECT
            COUNT(*) as pending_total,
            SUM(CASE WHEN donation_category = 'digital' THEN 1 ELSE 0 END) as digital_pending,
            SUM(CASE WHEN donation_category = 'cash' THEN 1 ELSE 0 END) as cash_pending,
            SUM(CASE WHEN donation_category = 'food' THEN 1 ELSE 0 END) as food_pending
            FROM charity_donations_records
            WHERE status IN ('Pending Verification', 'Pending Coordination', 'Pending Receipt')");
        $pendingVerif = $stmtPendingVerif->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'pending_verifications' => [
                'total' => (int)($pendingVerif['pending_total'] ?? 0),
                'digital' => (int)($pendingVerif['digital_pending'] ?? 0),
                'cash' => (int)($pendingVerif['cash_pending'] ?? 0),
                'food' => (int)($pendingVerif['food_pending'] ?? 0)
            ],
            'received' => [
                'count' => (int)($rec['total_count'] ?? 0),
                'monetary' => (float)($rec['monetary_total'] ?? 0),
                'in_kind' => (int)($rec['in_kind_total'] ?? 0),
                'monetary_count' => (int)($rec['monetary_count'] ?? 0),
                'in_kind_count' => (int)($rec['in_kind_count'] ?? 0)
            ],
            'available' => [
                'count' => (int)($avail['available_count'] ?? 0),
                'monetary' => max(0, (float)($avail['available_amount'] ?? 0)),
                'in_kind' => max(0, (int)($avail['available_quantity'] ?? 0))
            ],
            'allocated' => [
                'count' => (int)($alloc['total_allocations'] ?? 0),
                'monetary' => (float)($alloc['allocated_amount'] ?? 0),
                'in_kind' => (int)($alloc['allocated_quantity'] ?? 0),
                'damayan_monetary' => (float)($alloc['damayan_allocated_amount'] ?? 0),
                'dawah_monetary' => (float)($alloc['dawah_allocated_amount'] ?? 0),
                'damayan_in_kind' => (int)($alloc['damayan_allocated_qty'] ?? 0),
                'dawah_in_kind' => (int)($alloc['dawah_allocated_qty'] ?? 0)
            ],
            'distributed' => [
                'count' => (int)($dist['total_distributions'] ?? 0),
                'monetary' => (float)($dist['distributed_amount'] ?? 0),
                'in_kind' => (int)($dist['distributed_quantity'] ?? 0),
                'damayan_monetary' => (float)($dist['damayan_distributed_amount'] ?? 0),
                'dawah_monetary' => (float)($dist['dawah_distributed_amount'] ?? 0),
                'damayan_in_kind' => (int)($dist['damayan_distributed_qty'] ?? 0),
                'dawah_in_kind' => (int)($dist['dawah_distributed_qty'] ?? 0)
            ],
            'pending' => [
                'count' => (int)($pending['pending_count'] ?? 0),
                'monetary' => max(0, (float)($pending['pending_amount'] ?? 0)),
                'in_kind' => max(0, (int)($pending['pending_quantity'] ?? 0))
            ]
        ];
    }

    /**
     * Get recent activity logs
     */
    public function getActivityLogs(int $limit = 20): array
    {
        $stmt = $this->db->prepare("SELECT * FROM charity_activity_logs ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Seed initial demo records if tables are completely empty
     */
    public function seedInitialDataIfEmpty(string $staffName = 'Damayan Staff'): void
    {
        $count = (int)$this->db->query("SELECT COUNT(*) FROM charity_donations_records")->fetchColumn();
        if ($count > 0) {
            return;
        }

        $donations = [
            [
                'ref' => 'DON-2026-0001',
                'donor' => 'Al-Huda Community Foundation',
                'address' => 'Ermita, Manila',
                'type' => 'monetary',
                'amount' => 50000.00,
                'item' => null,
                'qty' => 0,
                'date' => date('Y-m-d', strtotime('-18 days')),
                'method' => 'Bank Transfer',
                'pay_ref' => 'BDO-TRX-982143',
                'recv_by' => $staffName,
                'remarks' => 'General charity and welfare donation',
                'ack' => 1
            ],
            [
                'ref' => 'DON-2026-0002',
                'donor' => 'Haji Abdullah Ibrahim',
                'address' => 'Quiapo, Manila',
                'type' => 'monetary',
                'amount' => 25000.00,
                'item' => null,
                'qty' => 0,
                'date' => date('Y-m-d', strtotime('-14 days')),
                'method' => 'GCash',
                'pay_ref' => 'GC-90184712',
                'recv_by' => $staffName,
                'remarks' => 'Designated for funeral and burial support',
                'ack' => 1
            ],
            [
                'ref' => 'DON-2026-0003',
                'donor' => 'Baitul Mal Welfare Group',
                'address' => 'Greenhills, San Juan',
                'type' => 'in-kind',
                'amount' => 0.00,
                'item' => 'Sacks of Premium Rice (25kg)',
                'qty' => 60,
                'date' => date('Y-m-d', strtotime('-10 days')),
                'method' => 'In-Person Delivery',
                'pay_ref' => 'DR-2026-081',
                'recv_by' => $staffName,
                'remarks' => 'Community food assistance packs',
                'ack' => 1
            ],
            [
                'ref' => 'DON-2026-0004',
                'donor' => 'Sister Maryam & Family',
                'address' => 'Pasig City',
                'type' => 'monetary',
                'amount' => 15000.00,
                'item' => null,
                'qty' => 0,
                'date' => date('Y-m-d', strtotime('-5 days')),
                'method' => 'Cash',
                'pay_ref' => 'CSH-00291',
                'recv_by' => $staffName,
                'remarks' => 'Sadaqah Jariyah for community welfare',
                'ack' => 1
            ],
            [
                'ref' => 'DON-2026-0005',
                'donor' => 'An-Noor Relief Association',
                'address' => 'Taguig City',
                'type' => 'in-kind',
                'amount' => 0.00,
                'item' => 'Complete Janazah Burial Shroud Kits (Kafan)',
                'qty' => 20,
                'date' => date('Y-m-d', strtotime('-3 days')),
                'method' => 'In-Person Delivery',
                'pay_ref' => 'DEL-7712',
                'recv_by' => $staffName,
                'remarks' => 'Burial shroud cloth and camphor supplies',
                'ack' => 0
            ]
        ];

        foreach ($donations as $d) {
            $stmt = $this->db->prepare("INSERT INTO charity_donations_records (
                donation_ref, donor_name, donor_address, donation_type, amount, item_description,
                quantity, allocated_amount, allocated_quantity, date_received, payment_method,
                payment_reference, received_by, remarks, ack_issued, ack_issued_at, ack_issued_by, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 0.00, 0, ?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
            $ackAt = $d['ack'] ? $d['date'] . ' 10:00:00' : null;
            $ackBy = $d['ack'] ? $staffName : null;
            $stmt->execute([
                $d['ref'], $d['donor'], $d['address'], $d['type'], $d['amount'], $d['item'],
                $d['qty'], $d['date'], $d['method'], $d['pay_ref'], $d['recv_by'], $d['remarks'],
                $d['ack'], $ackAt, $ackBy
            ]);
        }

        // Seed initial allocation & distribution to connect the entire workflow
        // Allocate ₱20,000 from DON-2026-0001 to Damayan for Funeral Assistance
        $this->createAllocation([
            'donation_id' => 1,
            'receiving_department' => 'Damayan',
            'allocated_amount' => 20000.00,
            'purpose' => 'Burial Assistance for Indigent Families',
            'allocation_date' => date('Y-m-d', strtotime('-12 days')),
            'allocated_by' => $staffName,
            'remarks' => 'Allocated for indigent Muslim burial operations'
        ], $staffName);

        // Distribute ₱12,000 from Allocation #1
        $this->recordDistribution([
            'allocation_id' => 1,
            'distributed_amount' => 12000.00,
            'purpose' => 'Burial Shroud and Grave Assistance (Case #B-2026-012)',
            'distribution_date' => date('Y-m-d', strtotime('-8 days')),
            'distributed_by' => $staffName,
            'received_by' => 'Family of Late Brother Omar',
            'remarks' => 'Immediate burial subsidy provided'
        ], null, $staffName);

        // Allocate 20 Sacks of Rice from DON-2026-0003 to Dawah
        $this->createAllocation([
            'donation_id' => 3,
            'receiving_department' => 'Dawah',
            'allocated_quantity' => 20,
            'purpose' => 'Da\'wah Outreach and New Muslim Welfare Kits',
            'allocation_date' => date('Y-m-d', strtotime('-7 days')),
            'allocated_by' => $staffName,
            'remarks' => 'Relief support for new Muslim converts in need'
        ], $staffName);

        // Distribute 15 Sacks of Rice
        $this->recordDistribution([
            'allocation_id' => 2,
            'distributed_quantity' => 15,
            'purpose' => 'Distributed to 15 New Convert Beneficiary Families',
            'distribution_date' => date('Y-m-d', strtotime('-4 days')),
            'distributed_by' => $staffName,
            'received_by' => 'Dawah Outreach Committee (Ustadz Farhan)',
            'remarks' => 'Signed receiving acknowledgment on file'
        ], null, $staffName);
    }
}
