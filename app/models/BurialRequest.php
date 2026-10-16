<?php

/**
 * BurialRequest Model
 * Handles database operations for:
 * 1. Death Certificate Processing (LCRO coordination, tracking, verification, certification)
 * 2. Cemetery Plot Management (Inventory, availability checking, non-duplicate assignment)
 * 3. Deceased records, representative records, and audit logging.
 */
class BurialRequest
{
    protected PDO $db;

    public function __construct() {
        $this->db = getDbConnection();
        $this->initTables();
    }

    /**
     * Ensure all tables and columns exist
     */
    private function initTables(): void {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS burial_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ref_id VARCHAR(50) NOT NULL UNIQUE,
                tenant_id INT NOT NULL,
                deceased_id INT NULL,
                deceased_name VARCHAR(255) NOT NULL,
                deceased_first_name VARCHAR(100) NULL,
                deceased_middle_name VARCHAR(100) NULL,
                deceased_last_name VARCHAR(100) NULL,
                deceased_sex ENUM('Male','Female') DEFAULT 'Male',
                date_of_birth DATE NULL,
                date_of_death DATE NOT NULL,
                time_of_death TIME NULL,
                place_of_death VARCHAR(255) NULL,
                residence VARCHAR(255) NULL,
                religion VARCHAR(100) DEFAULT 'Islam',
                civil_status VARCHAR(50) NULL,
                nationality VARCHAR(100) DEFAULT 'Filipino',
                relationship VARCHAR(100) NULL,
                informant_name VARCHAR(200) NULL,
                informant_contact VARCHAR(50) NULL,
                informant_email VARCHAR(200) NULL,
                informant_address TEXT NULL,
                request_type ENUM('both','death_certificate','cemetery_plot') DEFAULT 'both',
                death_cert_requested TINYINT(1) DEFAULT 1,
                death_cert_status ENUM(
                    'Request Received',
                    'Under Review',
                    'Additional Information Required',
                    'Awaiting Authorized Certificate Preparation',
                    'Certificate Prepared and Certified',
                    'Submitted for Civil Registration',
                    'Registered',
                    'Certified Copy Requested or Received',
                    'Rejected'
                ) DEFAULT 'Request Received',
                death_cert_prep_date DATE NULL,
                death_cert_certified_date DATE NULL,
                death_cert_submitted_lcro_date DATE NULL,
                death_cert_registered_date DATE NULL,
                death_cert_registry_no VARCHAR(100) NULL,
                death_cert_lcro_ref VARCHAR(100) NULL,
                death_cert_certified_copy_ref VARCHAR(100) NULL,
                death_cert_responsible_personnel VARCHAR(200) NULL,
                death_cert_remarks TEXT NULL,
                death_cert_rejection_reason TEXT NULL,
                cemetery_plot_requested TINYINT(1) DEFAULT 1,
                cemetery_plot_status ENUM(
                    'Pending Review',
                    'Under Review',
                    'Available for Assignment',
                    'Approved',
                    'Plot Assigned',
                    'Awaiting Plot Availability',
                    'Unavailable',
                    'Rejected'
                ) DEFAULT 'Pending Review',
                cemetery_preference VARCHAR(255) NULL,
                cemetery_plot_id INT NULL,
                assigned_cemetery_name VARCHAR(255) NULL,
                assigned_plot_number VARCHAR(100) NULL,
                assigned_section VARCHAR(100) NULL,
                plot_assigned_date DATETIME NULL,
                cemetery_plot_remarks TEXT NULL,
                cemetery_plot_rejection_reason TEXT NULL,
                additional_notes TEXT NULL,
                supporting_document VARCHAR(255) NULL,
                assigned_staff_id INT NULL,
                assigned_staff_name VARCHAR(200) NULL,
                info_request_notes TEXT NULL,
                info_requested_at DATETIME NULL,
                additional_info_submitted TEXT NULL,
                additional_doc_path VARCHAR(255) NULL,
                info_submitted_at DATETIME NULL,
                status VARCHAR(50) DEFAULT 'Request Received',
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (tenant_id),
                INDEX (status),
                INDEX (death_cert_status),
                INDEX (cemetery_plot_status)
            )");

            $this->db->exec("CREATE TABLE IF NOT EXISTS deceased_records (
                id INT AUTO_INCREMENT PRIMARY KEY,
                burial_request_id INT NULL,
                ref_id VARCHAR(50) NULL,
                first_name VARCHAR(100) NOT NULL,
                middle_name VARCHAR(100) NULL,
                last_name VARCHAR(100) NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                sex ENUM('Male','Female') NOT NULL DEFAULT 'Male',
                date_of_birth DATE NULL,
                date_of_death DATE NOT NULL,
                time_of_death TIME NULL,
                place_of_death VARCHAR(255) NULL,
                residence_address TEXT NULL,
                civil_status VARCHAR(50) NULL,
                nationality VARCHAR(100) DEFAULT 'Filipino',
                religion VARCHAR(100) DEFAULT 'Islam',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (burial_request_id),
                INDEX (ref_id)
            )");

            $this->db->exec("CREATE TABLE IF NOT EXISTS cemetery_plots (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cemetery_name VARCHAR(255) NOT NULL DEFAULT 'ISCAG Designated Muslim Cemetery',
                section VARCHAR(100) NOT NULL DEFAULT 'Section A',
                plot_number VARCHAR(100) NOT NULL,
                status ENUM('Available', 'Reserved', 'Occupied', 'Unavailable') DEFAULT 'Available',
                assigned_deceased_name VARCHAR(255) NULL,
                assigned_burial_request_id INT NULL,
                assigned_tenant_id INT NULL,
                assigned_date DATETIME NULL,
                notes TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_plot (cemetery_name, section, plot_number)
            )");

            $this->db->exec("CREATE TABLE IF NOT EXISTS burial_activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                burial_request_id INT NOT NULL,
                ref_id VARCHAR(50) NOT NULL,
                activity_type VARCHAR(50) NOT NULL,
                title VARCHAR(255) NOT NULL,
                details TEXT NULL,
                actor_name VARCHAR(200) DEFAULT 'Staff Admin',
                actor_role VARCHAR(100) DEFAULT 'Staff_Damayan',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (burial_request_id),
                INDEX (ref_id)
            )");

            try {
                $this->db->exec("ALTER TABLE burial_requests ADD COLUMN deceased_id INT NULL AFTER tenant_id");
            } catch (\Throwable $e) {}

            $colsToAdd = [
                "cert_action_type VARCHAR(50) DEFAULT 'register'",
                "existing_cert_no VARCHAR(100) NULL",
                "copy_reason TEXT NULL",
                "num_copies INT DEFAULT 1",
                "existing_record_verified TINYINT(1) DEFAULT 0",
                "copy_issued_at DATETIME NULL",
                "copy_received_at DATETIME NULL",
                "copy_received_confirmed TINYINT(1) DEFAULT 0"
            ];
            foreach ($colsToAdd as $colDef) {
                try {
                    $this->db->exec("ALTER TABLE burial_requests ADD COLUMN $colDef");
                } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {
            error_log("BurialRequest initTables error: " . $e->getMessage());
        }
    }

    /**
     * Check for duplicate requests within 24 hours
     */
    public function checkDuplicate(int $tenantId, string $deceasedName, string $dateOfDeath): bool {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM burial_requests 
                                        WHERE tenant_id = :tid 
                                          AND LOWER(TRIM(deceased_name)) = LOWER(TRIM(:dname))
                                          AND date_of_death = :dod
                                          AND status NOT IN ('Rejected')
                                          AND submitted_at >= NOW() - INTERVAL 24 HOUR");
            $stmt->execute([
                'tid' => $tenantId,
                'dname' => $deceasedName,
                'dod' => $dateOfDeath
            ]);
            return ((int)$stmt->fetchColumn()) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Create a new request:
     * - Register Death Certificate (cert_action_type = 'register')
     * - Request Another Death Certificate (cert_action_type = 'copy')
     */
    public function create(array $data): array|bool {
        try {
            $this->db->beginTransaction();

            $certActionType = strtolower(trim($data['cert_action_type'] ?? 'register'));
            if (!in_array($certActionType, ['register', 'copy'])) {
                $certActionType = 'register';
            }

            if (empty($data['ref_id'])) {
                $prefix = ($certActionType === 'copy') ? 'DC-CPY-' : 'DC-REG-';
                $refId = $prefix . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            } else {
                $refId = $data['ref_id'];
            }

            $fName = trim($data['deceased_first_name'] ?? ($data['first_name'] ?? ''));
            $mName = trim($data['deceased_middle_name'] ?? ($data['middle_name'] ?? ''));
            $lName = trim($data['deceased_last_name'] ?? ($data['last_name'] ?? ''));
            $fullName = trim($data['deceased_name'] ?? "$fName $mName $lName");
            if (empty($fullName)) {
                $fullName = trim("$fName $lName");
            }
            if (!empty($fullName) && empty($fName) && empty($lName)) {
                $parts = explode(' ', $fullName);
                $fName = $parts[0] ?? '';
                $lName = count($parts) > 1 ? end($parts) : '';
                if (count($parts) > 2) {
                    $mName = implode(' ', array_slice($parts, 1, -1));
                }
            }

            $rawSex = $data['deceased_sex'] ?? ($data['gender'] ?? 'Male');
            $sex = in_array(ucfirst((string)$rawSex), ['Male', 'Female']) ? ucfirst((string)$rawSex) : 'Male';

            $requestType = $data['request_type'] ?? ($certActionType === 'copy' ? 'death_certificate' : 'both');
            $deathCertReq = 1;
            $plotReq = ($certActionType === 'copy') ? 0 : (($requestType === 'both' || $requestType === 'cemetery_plot') ? 1 : 0);

            $existingCertNo = !empty($data['existing_cert_no']) ? trim($data['existing_cert_no']) : null;
            $copyReason = !empty($data['copy_reason']) ? trim($data['copy_reason']) : null;
            $numCopies = !empty($data['num_copies']) ? max(1, (int)$data['num_copies']) : 1;

            $deceasedId = !empty($data['deceased_id']) ? (int)$data['deceased_id'] : null;
            $existingRecordVerified = 0;

            if ($certActionType === 'copy') {
                // If user provided deceased_id, verify it exists in deceased_records
                if ($deceasedId) {
                    $checkStmt = $this->db->prepare("SELECT id, full_name FROM deceased_records WHERE id = :did LIMIT 1");
                    $checkStmt->execute(['did' => $deceasedId]);
                    $foundDec = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    if ($foundDec) {
                        $existingRecordVerified = 1;
                    } else {
                        $deceasedId = null;
                    }
                }

                // If not found yet, try searching by existing_cert_no or deceased name
                if (!$deceasedId && $existingCertNo) {
                    $certStmt = $this->db->prepare("SELECT deceased_id FROM burial_requests WHERE (death_cert_registry_no = :cno1 OR ref_id = :cno2) AND deceased_id IS NOT NULL LIMIT 1");
                    $certStmt->execute(['cno1' => $existingCertNo, 'cno2' => $existingCertNo]);
                    $foundId = $certStmt->fetchColumn();
                    if ($foundId) {
                        $deceasedId = (int)$foundId;
                        $existingRecordVerified = 1;
                    }
                }

                if (!$deceasedId && !empty($fullName)) {
                    $sqlName = "SELECT id FROM deceased_records WHERE (LOWER(TRIM(full_name)) = LOWER(TRIM(:dname1)) OR LOWER(TRIM(CONCAT(first_name, ' ', last_name))) = LOWER(TRIM(:dname2)))";
                    $paramsName = ['dname1' => $fullName, 'dname2' => $fullName];
                    if (!empty($data['date_of_death'])) {
                        $sqlName .= " AND date_of_death = :dod";
                        $paramsName['dod'] = $data['date_of_death'];
                    }
                    $sqlName .= " LIMIT 1";
                    $nameStmt = $this->db->prepare($sqlName);
                    $nameStmt->execute($paramsName);
                    $foundId = $nameStmt->fetchColumn();
                    if ($foundId) {
                        $deceasedId = (int)$foundId;
                        $existingRecordVerified = 1;
                    }
                }
            }

            $initialStatus = 'Request Received';

            $sql = "INSERT INTO burial_requests (
                        ref_id, tenant_id, deceased_id, deceased_name, deceased_first_name, deceased_middle_name, deceased_last_name,
                        deceased_sex, date_of_birth, date_of_death, time_of_death, place_of_death, residence, religion, civil_status,
                        nationality, relationship, informant_name, informant_contact, informant_email, informant_address,
                        request_type, death_cert_requested, death_cert_status,
                        cemetery_plot_requested, cemetery_plot_status, cemetery_preference,
                        additional_notes, supporting_document, status,
                        cert_action_type, existing_cert_no, copy_reason, num_copies, existing_record_verified,
                        submitted_at
                    ) VALUES (
                        :ref_id, :tenant_id, :deceased_id, :deceased_name, :deceased_first_name, :deceased_middle_name, :deceased_last_name,
                        :deceased_sex, :dob, :dod, :tod, :pod, :residence, :religion, :civil_status,
                        :nationality, :relationship, :informant_name, :informant_contact, :informant_email, :informant_address,
                        :request_type, :death_cert_requested, :death_cert_status,
                        :cemetery_plot_requested, :cemetery_plot_status, :cemetery_preference,
                        :additional_notes, :supporting_document, :status,
                        :cert_action_type, :existing_cert_no, :copy_reason, :num_copies, :existing_record_verified,
                        NOW()
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ref_id' => $refId,
                'tenant_id' => (int)$data['tenant_id'],
                'deceased_id' => $deceasedId,
                'deceased_name' => $fullName,
                'deceased_first_name' => $fName,
                'deceased_middle_name' => $mName,
                'deceased_last_name' => $lName,
                'deceased_sex' => $sex,
                'dob' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'dod' => !empty($data['date_of_death']) ? $data['date_of_death'] : date('Y-m-d'),
                'tod' => !empty($data['time_of_death']) ? $data['time_of_death'] : null,
                'pod' => $data['place_of_death'] ?? null,
                'residence' => $data['residence'] ?? null,
                'religion' => $data['religion'] ?? 'Islam',
                'civil_status' => $data['civil_status'] ?? 'Married',
                'nationality' => $data['nationality'] ?? 'Filipino',
                'relationship' => $data['relationship'] ?? 'Family Member',
                'informant_name' => $data['informant_name'] ?? null,
                'informant_contact' => $data['informant_contact'] ?? null,
                'informant_email' => $data['informant_email'] ?? null,
                'informant_address' => $data['informant_address'] ?? null,
                'request_type' => $requestType,
                'death_cert_requested' => $deathCertReq,
                'death_cert_status' => 'Request Received',
                'cemetery_plot_requested' => $plotReq,
                'cemetery_plot_status' => $plotReq ? 'Pending Review' : 'Not Requested',
                'cemetery_preference' => $data['cemetery_preference'] ?? 'ISCAG Designated Muslim Cemetery',
                'additional_notes' => $data['additional_notes'] ?? null,
                'supporting_document' => $data['supporting_document'] ?? null,
                'status' => $initialStatus,
                'cert_action_type' => $certActionType,
                'existing_cert_no' => $existingCertNo,
                'copy_reason' => $copyReason,
                'num_copies' => $numCopies,
                'existing_record_verified' => $existingRecordVerified
            ]);

            $burialId = (int)$this->db->lastInsertId();

            // 1. Only create Deceased Record if registering new death (cert_action_type === 'register')
            // Important: Do not create a new death registration record when processing a request for another certificate copy.
            if ($certActionType === 'register' && !$deceasedId) {
                $decSql = "INSERT INTO deceased_records (
                                burial_request_id, ref_id, first_name, middle_name, last_name, full_name,
                                sex, date_of_birth, date_of_death, time_of_death, place_of_death, residence_address,
                                civil_status, nationality, religion, created_at
                           ) VALUES (
                                :brid, :ref_id, :fname, :mname, :lname, :fullname,
                                :sex, :dob, :dod, :tod, :pod, :residence,
                                :civil, :nat, :rel, NOW()
                           )";
                $decStmt = $this->db->prepare($decSql);
                $decStmt->execute([
                    'brid' => $burialId,
                    'ref_id' => $refId,
                    'fname' => $fName ?: $fullName,
                    'mname' => $mName,
                    'lname' => $lName ?: $fullName,
                    'fullname' => $fullName,
                    'sex' => $sex,
                    'dob' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                    'dod' => !empty($data['date_of_death']) ? $data['date_of_death'] : date('Y-m-d'),
                    'tod' => !empty($data['time_of_death']) ? $data['time_of_death'] : null,
                    'pod' => $data['place_of_death'] ?? null,
                    'residence' => $data['residence'] ?? null,
                    'civil' => $data['civil_status'] ?? 'Married',
                    'nat' => $data['nationality'] ?? 'Filipino',
                    'rel' => $data['religion'] ?? 'Islam'
                ]);
                $createdDecId = (int)$this->db->lastInsertId();
                $this->db->prepare("UPDATE burial_requests SET deceased_id = :dec_id, existing_record_verified = 1 WHERE id = :id")->execute([
                    'dec_id' => $createdDecId,
                    'id' => $burialId
                ]);
            }

            // 2. Log activity
            if ($certActionType === 'copy') {
                $typeDesc = 'Request Another Death Certificate Copy';
                $logDetails = "Copy request filed for {$fullName}. Reason: " . ($copyReason ?: 'Not specified') . ". Copies: {$numCopies}.";
                if ($existingCertNo) $logDetails .= " Existing Cert #: {$existingCertNo}.";
                if ($existingRecordVerified) {
                    $logDetails .= " Linked to existing registered deceased record (#{$deceasedId}).";
                } else {
                    $logDetails .= " Existing deceased record unverified (requires staff verification).";
                }
            } else {
                $typeDesc = ($requestType === 'both') ? 'Death Certificate & Cemetery Plot' : (($requestType === 'death_certificate') ? 'Death Certificate Registration' : 'Cemetery Plot');
                $logDetails = "New registration filed by " . ($data['informant_name'] ?? 'Family Representative') . " for deceased {$fullName} ({$sex}).";
            }

            $this->logActivity($burialId, $refId, 'Submission', "Application Submitted: {$typeDesc}",
                $logDetails,
                $data['informant_name'] ?? 'Applicant', 'Applicant'
            );

            $this->db->commit();

            // 3. Send notification to User
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $notif->create(
                    (int)$data['tenant_id'],
                    "Application Received: #{$refId}",
                    "Assalamu Alaikum. Your request for {$typeDesc} for {$fullName} has been successfully submitted. Reference Number: #{$refId}.",
                    'damayan'
                );
            } catch (\Throwable $ne) {}

            // 4. Send notification to Admin/Staff
            try {
                require_once BASE_PATH . '/app/models/AdminNotification.php';
                $adminNotif = new AdminNotification();
                $adminNotif->create(
                    "New Damayan Request #{$refId}",
                    "New {$typeDesc} request submitted by " . ($data['informant_name'] ?? 'Family Representative') . " for deceased {$fullName}.",
                    'damayan',
                    $data['informant_name'] ?? 'Applicant',
                    (int)$data['tenant_id'],
                    url('/admin/damayan/burial-requests')
                );
            } catch (\Throwable $ane) {}

            return [
                'success' => true,
                'id' => $burialId,
                'ref_id' => $refId,
                'deceased_name' => $fullName
            ];
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("BurialRequest::create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log an action in the chronological audit trail
     */
    public function logActivity(int $burialId, string $refId, string $type, string $title, ?string $details = '', string $actorName = 'Staff Admin', string $actorRole = 'Staff_Damayan'): bool {
        try {
            $stmt = $this->db->prepare("INSERT INTO burial_activity_logs (
                burial_request_id, ref_id, activity_type, title, details, actor_name, actor_role, created_at
            ) VALUES (:brid, :ref_id, :type, :title, :details, :actor, :role, NOW())");
            return $stmt->execute([
                'brid' => $burialId,
                'ref_id' => $refId,
                'type' => $type,
                'title' => $title,
                'details' => $details,
                'actor' => $actorName,
                'role' => $actorRole
            ]);
        } catch (\Throwable $e) {
            error_log("logActivity error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get activity logs for a case
     */
    public function getActivityLogs(int $burialId): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM burial_activity_logs WHERE burial_request_id = :id ORDER BY created_at DESC");
            $stmt->execute(['id' => $burialId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getAuditLogs(int $burialId): array {
        return $this->getActivityLogs($burialId);
    }

    /**
     * Find single case by Ref ID
     */
    public function findByRefId(string $refId): ?array {
        try {
            $sql = "SELECT b.*, 
                           COALESCE(b.deceased_id, d.id) AS deceased_id,
                           t.first_name AS user_first_name, t.last_name AS user_last_name, 
                           t.email AS user_email, t.contactnum AS user_contact,
                           d.id AS deceased_record_id, d.first_name AS dec_fname, d.middle_name AS dec_mname, d.last_name AS dec_lname,
                           p.cemetery_name AS plot_cemetery, p.section AS plot_section, p.plot_number AS plot_num, p.status AS plot_current_status
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    LEFT JOIN deceased_records d ON (b.deceased_id = d.id OR (b.deceased_id IS NULL AND b.id = d.burial_request_id))
                    LEFT JOIN cemetery_plots p ON b.cemetery_plot_id = p.id
                    WHERE b.ref_id = :ref_id
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ref_id' => $refId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            $row['activity_logs'] = $this->getActivityLogs((int)$row['id']);
            return $row;
        } catch (\PDOException $e) {
            error_log("BurialRequest::findByRefId error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find by primary key ID
     */
    public function findById(int $id): ?array {
        try {
            $stmt = $this->db->prepare("SELECT ref_id FROM burial_requests WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $ref = $stmt->fetchColumn();
            return $ref ? $this->findByRefId($ref) : null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Get all requests for a specific user (enforcing RBAC privacy)
     */
    public function getByTenantId(int $tenantId): array {
        try {
            $sql = "SELECT b.*, 
                           p.cemetery_name AS plot_cemetery, p.section AS plot_section, p.plot_number AS plot_num
                    FROM burial_requests b
                    LEFT JOIN cemetery_plots p ON b.cemetery_plot_id = p.id
                    WHERE b.tenant_id = :tid 
                    ORDER BY b.submitted_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['tid' => $tenantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Get all requests for Staff Admin dashboard with filter & search
     */
    public function getAll(string $filter = 'all', string $search = ''): array {
        try {
            $params = [];
            $sql = "SELECT b.*, 
                           t.first_name, t.last_name, t.email, t.contactnum,
                           p.cemetery_name AS plot_cemetery, p.section AS plot_section, p.plot_number AS plot_num
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    LEFT JOIN cemetery_plots p ON b.cemetery_plot_id = p.id
                    WHERE 1=1";

            $filter = strtolower(trim($filter));
            if ($filter === 'pending') {
                $sql .= " AND (b.death_cert_status IN ('Request Received', 'Under Review') OR b.cemetery_plot_status IN ('Pending Review', 'Under Review'))";
            } elseif ($filter === 'additional_info') {
                $sql .= " AND (b.death_cert_status = 'Additional Information Required' OR b.cemetery_plot_status = 'Additional Information Required')";
            } elseif ($filter === 'death_cert') {
                $sql .= " AND b.death_cert_requested = 1";
            } elseif ($filter === 'cemetery_plot') {
                $sql .= " AND b.cemetery_plot_requested = 1";
            } elseif ($filter === 'completed') {
                $sql .= " AND (b.death_cert_status IN ('Registered', 'Certified Copy Requested or Received') OR b.cemetery_plot_status = 'Plot Assigned')";
            } elseif ($filter === 'rejected') {
                $sql .= " AND (b.death_cert_status = 'Rejected' OR b.cemetery_plot_status = 'Rejected')";
            }

            if (!empty($search)) {
                $sql .= " AND (b.ref_id LIKE :search OR b.deceased_name LIKE :search OR b.informant_name LIKE :search OR t.first_name LIKE :search OR t.last_name LIKE :search)";
                $params['search'] = "%$search%";
            }

            $sql .= " ORDER BY b.submitted_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            error_log("BurialRequest::getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get operational analytics for Damayan Staff Admin dashboard
     */
    public function getAnalytics(): array {
        try {
            $totalCases = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests")->fetchColumn();
            
            // Death Certificate stages
            $dcPending = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE death_cert_requested = 1 AND death_cert_status IN ('Request Received', 'Under Review')")->fetchColumn();
            $dcInfoReq = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE death_cert_requested = 1 AND death_cert_status = 'Additional Information Required'")->fetchColumn();
            $dcInProgress = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE death_cert_requested = 1 AND death_cert_status IN ('Awaiting Authorized Certificate Preparation', 'Certificate Prepared and Certified', 'Submitted for Civil Registration')")->fetchColumn();
            $dcRegistered = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE death_cert_requested = 1 AND death_cert_status IN ('Registered', 'Certified Copy Requested or Received')")->fetchColumn();

            // Cemetery Plot metrics
            $plotPending = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE cemetery_plot_requested = 1 AND cemetery_plot_status IN ('Pending Review', 'Under Review')")->fetchColumn();
            $plotApproved = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE cemetery_plot_requested = 1 AND cemetery_plot_status = 'Approved'")->fetchColumn();
            $plotAssigned = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE cemetery_plot_requested = 1 AND cemetery_plot_status = 'Plot Assigned'")->fetchColumn();

            // Cemetery Plot Inventory counts
            $availPlots = (int)$this->db->query("SELECT COUNT(*) FROM cemetery_plots WHERE status = 'Available'")->fetchColumn();
            $occupiedPlots = (int)$this->db->query("SELECT COUNT(*) FROM cemetery_plots WHERE status = 'Occupied'")->fetchColumn();
            $totalPlots = (int)$this->db->query("SELECT COUNT(*) FROM cemetery_plots")->fetchColumn();

            return [
                'total' => $totalCases,
                'pending' => $dcPending + $plotPending,
                'death_cert_pending' => $dcPending,
                'death_cert_info_req' => $dcInfoReq,
                'death_cert_progress' => $dcInProgress,
                'death_cert_in_progress' => $dcInProgress,
                'death_cert_registered' => $dcRegistered,
                'plot_pending' => $plotPending,
                'plot_approved' => $plotApproved,
                'plot_assigned' => $plotAssigned,
                'available_plots' => $availPlots,
                'occupied_plots' => $occupiedPlots,
                'total_plots' => $totalPlots,
                'completed' => $dcRegistered + $plotAssigned
            ];
        } catch (\PDOException $e) {
            return [
                'total' => 0, 'pending' => 0, 'death_cert_pending' => 0, 'death_cert_info_req' => 0,
                'death_cert_progress' => 0, 'death_cert_in_progress' => 0, 'death_cert_registered' => 0,
                'plot_pending' => 0, 'plot_approved' => 0, 'plot_assigned' => 0,
                'available_plots' => 0, 'occupied_plots' => 0, 'total_plots' => 0, 'completed' => 0
            ];
        }
    }

    /**
     * Update Deceased Record and synchronize with request
     */
    public function updateDeceasedRecord(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $fName = trim($data['first_name'] ?? '');
            $mName = trim($data['middle_name'] ?? '');
            $lName = trim($data['last_name'] ?? '');
            $fullName = trim("$fName $mName $lName");
            if (empty($fullName)) $fullName = trim($data['full_name'] ?? $burial['deceased_name']);

            $sex = in_array(ucfirst($data['sex'] ?? 'Male'), ['Male', 'Female']) ? ucfirst($data['sex']) : 'Male';

            // 1. Update deceased_records
            $decStmt = $this->db->prepare("UPDATE deceased_records 
                SET first_name = :fname, middle_name = :mname, last_name = :lname, full_name = :fullname,
                    sex = :sex, date_of_birth = :dob, date_of_death = :dod, time_of_death = :tod,
                    place_of_death = :pod, residence_address = :residence, civil_status = :civil,
                    nationality = :nat, religion = :rel, updated_at = NOW()
                WHERE burial_request_id = :id");
            $decStmt->execute([
                'fname' => $fName,
                'mname' => $mName,
                'lname' => $lName,
                'fullname' => $fullName,
                'sex' => $sex,
                'dob' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'dod' => !empty($data['date_of_death']) ? $data['date_of_death'] : date('Y-m-d'),
                'tod' => !empty($data['time_of_death']) ? $data['time_of_death'] : null,
                'pod' => $data['place_of_death'] ?? null,
                'residence' => $data['residence_address'] ?? ($data['residence'] ?? null),
                'civil' => $data['civil_status'] ?? null,
                'nat' => $data['nationality'] ?? 'Filipino',
                'rel' => $data['religion'] ?? 'Islam',
                'id' => $burialId
            ]);

            // 2. Sync burial_requests
            $brStmt = $this->db->prepare("UPDATE burial_requests 
                SET deceased_name = :dname, deceased_first_name = :fname, deceased_middle_name = :mname, deceased_last_name = :lname,
                    deceased_sex = :sex, date_of_birth = :dob, date_of_death = :dod, time_of_death = :tod, place_of_death = :pod,
                    residence = :residence, civil_status = :civil, nationality = :nat, religion = :rel
                WHERE id = :id");
            $brStmt->execute([
                'dname' => $fullName,
                'fname' => $fName,
                'mname' => $mName,
                'lname' => $lName,
                'sex' => $sex,
                'dob' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'dod' => !empty($data['date_of_death']) ? $data['date_of_death'] : date('Y-m-d'),
                'tod' => !empty($data['time_of_death']) ? $data['time_of_death'] : null,
                'pod' => $data['place_of_death'] ?? null,
                'residence' => $data['residence_address'] ?? ($data['residence'] ?? null),
                'civil' => $data['civil_status'] ?? null,
                'nat' => $data['nationality'] ?? 'Filipino',
                'rel' => $data['religion'] ?? 'Islam',
                'id' => $burialId
            ]);

            $this->logActivity($burialId, $burial['ref_id'], 'Deceased Record', 'Deceased Record Updated',
                "Deceased details updated for {$fullName} ({$sex}).", $staffName
            );

            return true;
        } catch (\PDOException $e) {
            error_log("updateDeceasedRecord error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Death Certificate Processing (Requirement 3.A)
     * Stages:
     * - Request Received
     * - Under Review
     * - Additional Information Required
     * - Awaiting Authorized Certificate Preparation
     * - Certificate Prepared and Certified
     * - Submitted for Civil Registration
     * - Registered
     * - Certified Copy Requested or Received
     * - Rejected
     */
    public function updateDeathCertStatus(int $burialId, $statusOrData, $dataOrStaff = [], string $staffName = 'Staff Admin'): array {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            if (is_string($statusOrData)) {
                $status = $statusOrData;
                $data = is_array($dataOrStaff) ? $dataOrStaff : [];
                $staff = !empty($staffName) ? $staffName : 'Staff Admin';
            } else {
                $data = is_array($statusOrData) ? $statusOrData : [];
                $status = $data['death_cert_status'] ?? 'Under Review';
                $staff = is_string($dataOrStaff) ? $dataOrStaff : 'Staff Admin';
            }

            $validStatuses = [
                'Request Received',
                'Under Review',
                'Additional Information Required',
                'Awaiting Authorized Certificate Preparation',
                'Certificate Prepared and Certified',
                'Submitted for Civil Registration',
                'Registered',
                'Certified Copy Requested or Received',
                'Rejected'
            ];
            if (!in_array($status, $validStatuses)) {
                $status = 'Under Review';
            }

            $stmt = $this->db->prepare("UPDATE burial_requests 
                SET death_cert_status = :dc_status,
                    death_cert_prep_date = :prep_date,
                    death_cert_certified_date = :cert_date,
                    death_cert_submitted_lcro_date = :lcro_date,
                    death_cert_registered_date = :reg_date,
                    death_cert_registry_no = :reg_no,
                    death_cert_lcro_ref = :lcro_ref,
                    death_cert_certified_copy_ref = :copy_ref,
                    death_cert_responsible_personnel = :personnel,
                    death_cert_remarks = :remarks,
                    death_cert_rejection_reason = :rejection_reason,
                    status = :overall_status,
                    updated_at = NOW()
                WHERE id = :id");

            $res = $stmt->execute([
                'dc_status' => $status,
                'overall_status' => $status,
                'prep_date' => !empty($data['death_cert_prep_date']) ? $data['death_cert_prep_date'] : null,
                'cert_date' => !empty($data['death_cert_certified_date']) ? $data['death_cert_certified_date'] : null,
                'lcro_date' => !empty($data['death_cert_submitted_lcro_date']) ? $data['death_cert_submitted_lcro_date'] : null,
                'reg_date' => !empty($data['death_cert_registered_date']) ? $data['death_cert_registered_date'] : null,
                'reg_no' => !empty($data['death_cert_registry_no']) ? trim($data['death_cert_registry_no']) : null,
                'lcro_ref' => !empty($data['death_cert_lcro_ref']) ? trim($data['death_cert_lcro_ref']) : null,
                'copy_ref' => !empty($data['death_cert_certified_copy_ref']) ? trim($data['death_cert_certified_copy_ref']) : null,
                'personnel' => !empty($data['death_cert_responsible_personnel']) ? trim($data['death_cert_responsible_personnel']) : $staff,
                'remarks' => !empty($data['death_cert_remarks']) ? trim($data['death_cert_remarks']) : null,
                'rejection_reason' => ($status === 'Rejected') ? trim($data['death_cert_rejection_reason'] ?? 'Requirements could not be validated.') : null,
                'id' => $burialId
            ]);

            if ($res) {
                $details = "Death Certificate stage updated to '{$status}'.";
                if (!empty($data['death_cert_registry_no'])) $details .= " Registry No: {$data['death_cert_registry_no']}.";
                if (!empty($data['death_cert_remarks'])) $details .= " Remarks: {$data['death_cert_remarks']}.";

                $this->logActivity($burialId, $burial['ref_id'], 'Death Certificate', "Death Certificate Stage: {$status}", $details, $staff);

                // Notify User
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notifMsg = "Your death certificate request (#{$burial['ref_id']}) status is now: {$status}.";
                    if (!empty($data['death_cert_registry_no'])) {
                        $notifMsg .= " Official Local Civil Registry No: {$data['death_cert_registry_no']}.";
                    }
                    $notif->create((int)$burial['tenant_id'], "Death Certificate Update: #{$burial['ref_id']}", $notifMsg, 'damayan');
                } catch (\Throwable $e) {}

                return ['success' => true];
            }

            return ['success' => false, 'error' => 'Database update failed'];
        } catch (\PDOException $e) {
            error_log("updateDeathCertStatus error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cemetery Plot Management (Requirement 3.B)
     * Handles availability checking, approval, rejection, and non-duplicate plot assignment.
     * Statuses:
     * - Pending Review
     * - Under Review
     * - Available for Assignment
     * - Approved
     * - Plot Assigned
     * - Awaiting Plot Availability
     * - Unavailable
     * - Rejected
     */
    public function updateCemeteryPlot(int $burialId, $statusOrData, $dataOrStaff = [], string $staffName = 'Staff Admin'): array {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            if (is_string($statusOrData)) {
                $status = $statusOrData;
                $data = is_array($dataOrStaff) ? $dataOrStaff : [];
                $staff = !empty($staffName) ? $staffName : 'Staff Admin';
            } else {
                $data = is_array($statusOrData) ? $statusOrData : [];
                $status = $data['cemetery_plot_status'] ?? 'Under Review';
                $staff = is_string($dataOrStaff) ? $dataOrStaff : 'Staff Admin';
            }

            $plotId = !empty($data['cemetery_plot_id']) ? (int)$data['cemetery_plot_id'] : null;
            $rejectionReason = ($status === 'Rejected') ? trim($data['cemetery_plot_rejection_reason'] ?? 'Request declined.') : null;
            $remarks = !empty($data['cemetery_plot_remarks']) ? trim($data['cemetery_plot_remarks']) : null;

            $assignedCemetery = null;
            $assignedSection = null;
            $assignedPlotNum = null;

            // If assigning a plot
            if ($plotId > 0) {
                // Check plot existence and availability
                $plotStmt = $this->db->prepare("SELECT * FROM cemetery_plots WHERE id = :pid");
                $plotStmt->execute(['pid' => $plotId]);
                $plot = $plotStmt->fetch(PDO::FETCH_ASSOC);

                if (!$plot) {
                    return ['success' => false, 'error' => 'Selected cemetery plot does not exist in inventory.'];
                }

                // Check if already assigned to another request (Duplicate Assignment Prevention)
                if ($plot['status'] === 'Occupied' && (int)$plot['assigned_burial_request_id'] !== $burialId) {
                    return [
                        'success' => false, 
                        'error' => "Plot {$plot['plot_number']} ({$plot['section']}) is already occupied / assigned to {$plot['assigned_deceased_name']}. Duplicate assignments are strictly prevented."
                    ];
                }

                if ($plot['status'] === 'Unavailable') {
                    return ['success' => false, 'error' => "Plot {$plot['plot_number']} is currently marked as Unavailable."];
                }

                // Release previous plot if case was reassigned from another plot
                if (!empty($burial['cemetery_plot_id']) && (int)$burial['cemetery_plot_id'] !== $plotId) {
                    $this->db->prepare("UPDATE cemetery_plots 
                        SET status = 'Available', assigned_deceased_name = NULL, assigned_burial_request_id = NULL, assigned_tenant_id = NULL, assigned_date = NULL 
                        WHERE id = :old_pid")->execute(['old_pid' => (int)$burial['cemetery_plot_id']]);
                }

                // Assign the new plot
                $this->db->prepare("UPDATE cemetery_plots 
                    SET status = 'Occupied', 
                        assigned_deceased_name = :dname, 
                        assigned_burial_request_id = :brid, 
                        assigned_tenant_id = :tid, 
                        assigned_date = NOW() 
                    WHERE id = :pid")->execute([
                    'dname' => $burial['deceased_name'],
                    'brid' => $burialId,
                    'tid' => (int)$burial['tenant_id'],
                    'pid' => $plotId
                ]);

                $assignedCemetery = $plot['cemetery_name'];
                $assignedSection = $plot['section'];
                $assignedPlotNum = $plot['plot_number'];
                $status = 'Plot Assigned';
            }

            // Update burial_requests
            $updateSql = "UPDATE burial_requests 
                SET cemetery_plot_status = :status,
                    cemetery_plot_id = :pid,
                    assigned_cemetery_name = :cname,
                    assigned_section = :section,
                    assigned_plot_number = :plot_num,
                    plot_assigned_date = " . ($plotId > 0 ? "NOW()" : "plot_assigned_date") . ",
                    cemetery_plot_remarks = :remarks,
                    cemetery_plot_rejection_reason = :rej_reason,
                    updated_at = NOW()
                WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                'status' => $status,
                'pid' => $plotId,
                'cname' => $assignedCemetery ?? $burial['assigned_cemetery_name'],
                'section' => $assignedSection ?? $burial['assigned_section'],
                'plot_num' => $assignedPlotNum ?? $burial['assigned_plot_number'],
                'remarks' => $remarks,
                'rej_reason' => $rejectionReason,
                'id' => $burialId
            ]);

            $details = "Cemetery plot status set to '{$status}'.";
            if ($plotId > 0) {
                $details .= " Assigned Plot: {$assignedPlotNum} ({$assignedSection}) at {$assignedCemetery}.";
            }
            if (!empty($remarks)) $details .= " Remarks: {$remarks}.";

            $this->logActivity($burialId, $burial['ref_id'], 'Cemetery Plot', "Cemetery Plot: {$status}", $details, $staffName);

            // Notify User
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $msg = "Your cemetery plot request (#{$burial['ref_id']}) status is now: {$status}.";
                if ($plotId > 0) {
                    $msg .= " Assigned Location: {$assignedCemetery}, {$assignedSection}, Plot Number: {$assignedPlotNum}.";
                } elseif ($status === 'Rejected') {
                    $msg .= " Reason: {$rejectionReason}.";
                }
                $notif->create((int)$burial['tenant_id'], "Cemetery Plot Update: #{$burial['ref_id']}", $msg, 'damayan');
            } catch (\Throwable $e) {}

            return ['success' => true, 'status' => $status, 'plot_number' => $assignedPlotNum];
        } catch (\PDOException $e) {
            error_log("updateCemeteryPlot error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get available plots from inventory
     */
    public function getAvailablePlots(?string $cemetery = null, ?string $section = null, ?int $includePlotId = null): array {
        try {
            $sql = "SELECT * FROM cemetery_plots WHERE status = 'Available'";
            $params = [];

            if ($includePlotId && $includePlotId > 0) {
                $sql .= " OR id = :include_id";
                $params['include_id'] = $includePlotId;
            }

            if ($cemetery) {
                $sql .= " AND cemetery_name = :cname";
                $params['cname'] = $cemetery;
            }
            if ($section) {
                $sql .= " AND section = :sec";
                $params['sec'] = $section;
            }

            $sql .= " ORDER BY cemetery_name ASC, section ASC, plot_number ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Get all plots in inventory for Staff Admin management
     */
    public function getAllPlots(string $filter = 'all', string $search = ''): array {
        try {
            $sql = "SELECT p.*, b.ref_id, b.deceased_name, t.first_name, t.last_name 
                    FROM cemetery_plots p
                    LEFT JOIN burial_requests b ON p.assigned_burial_request_id = b.id
                    LEFT JOIN tenant_accounts t ON p.assigned_tenant_id = t.tenant_id
                    WHERE 1=1";
            $params = [];

            $filter = ucfirst(strtolower(trim($filter)));
            if (in_array($filter, ['Available', 'Reserved', 'Occupied', 'Unavailable'])) {
                $sql .= " AND p.status = :status";
                $params['status'] = $filter;
            }

            if (!empty($search)) {
                $sql .= " AND (p.plot_number LIKE :s OR p.section LIKE :s OR p.cemetery_name LIKE :s OR p.assigned_deceased_name LIKE :s)";
                $params['s'] = "%$search%";
            }

            $sql .= " ORDER BY p.cemetery_name ASC, p.section ASC, p.plot_number ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Staff adds a new plot to inventory
     */
    public function addPlot(array $data): bool {
        try {
            $stmt = $this->db->prepare("INSERT INTO cemetery_plots 
                (cemetery_name, section, plot_number, status, notes) 
                VALUES (:cname, :sec, :plot, :status, :notes)");
            return $stmt->execute([
                'cname' => trim($data['cemetery_name'] ?? 'ISCAG Designated Muslim Cemetery'),
                'sec' => trim($data['section'] ?? 'Section A'),
                'plot' => trim($data['plot_number'] ?? ''),
                'status' => in_array($data['status'] ?? 'Available', ['Available', 'Unavailable', 'Reserved']) ? $data['status'] : 'Available',
                'notes' => trim($data['notes'] ?? '')
            ]);
        } catch (\PDOException $e) {
            error_log("addPlot error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Staff updates plot status/notes in inventory
     */
    public function updatePlotInventory(int $plotId, $statusOrData, string $notes = '', string $staffName = 'Staff'): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cemetery_plots WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $plotId]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$current) {
                return ['success' => false, 'error' => 'Plot not found'];
            }

            if (is_array($statusOrData)) {
                $status = $statusOrData['status'] ?? ($current['status'] ?? 'Available');
                $newNotes = array_key_exists('notes', $statusOrData) ? $statusOrData['notes'] : $current['notes'];
                $section = $statusOrData['section'] ?? $current['section'];
                $plotNumber = $statusOrData['plot_number'] ?? $current['plot_number'];
            } else {
                $status = (string)$statusOrData;
                $newNotes = ($notes !== '') ? $notes : $current['notes'];
                $section = $current['section'];
                $plotNumber = $current['plot_number'];
            }

            $upStmt = $this->db->prepare("UPDATE cemetery_plots 
                SET status = :st, notes = :notes, section = :sec, plot_number = :pnum 
                WHERE id = :id");
            $ok = $upStmt->execute([
                'st' => $status,
                'notes' => $newNotes,
                'sec' => $section,
                'pnum' => $plotNumber,
                'id' => $plotId
            ]);

            return ['success' => $ok];
        } catch (\PDOException $e) {
            error_log("updatePlotInventory error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Request additional information from applicant
     */
    public function requestAdditionalInfo(int $burialId, string $explanation, string $staffName): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET status = 'Additional Information Required',
                                            death_cert_status = IF(death_cert_requested = 1, 'Additional Information Required', death_cert_status),
                                            info_request_notes = :notes,
                                            info_requested_at = NOW() 
                                        WHERE id = :id");
            $res = $stmt->execute([
                'notes' => $explanation,
                'id' => $burialId
            ]);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Requirement', 'Additional Information Requested', $explanation, $staffName);

                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$burial['tenant_id'],
                        "Action Required: Requirements for #{$burial['ref_id']}",
                        "Damayan coordinator {$staffName} requested additional details for your request (#{$burial['ref_id']}): \"{$explanation}\". Please visit your Damayan portal to submit the requirements.",
                        'damayan'
                    );
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("requestAdditionalInfo error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * User submits additional information or documents
     */
    public function submitAdditionalInfo(int $burialId, int $tenantId, string $infoText, ?string $docPath = null, string $userName = 'Applicant'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial || (int)$burial['tenant_id'] !== $tenantId) return false;

            $sql = "UPDATE burial_requests 
                    SET status = 'Under Review',
                        death_cert_status = IF(death_cert_status = 'Additional Information Required', 'Under Review', death_cert_status),
                        additional_info_submitted = :info,
                        info_submitted_at = NOW()";
            $params = ['info' => $infoText, 'id' => $burialId];

            if ($docPath) {
                $sql .= ", additional_doc_path = :doc";
                $params['doc'] = $docPath;
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute($params);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Requirement', 'Additional Requirements Submitted',
                    "Applicant provided updated information: {$infoText}" . ($docPath ? " (Document Attached)" : ""),
                    $userName, 'Applicant'
                );

                try {
                    require_once BASE_PATH . '/app/models/AdminNotification.php';
                    $adminNotif = new AdminNotification();
                    $adminNotif->create(
                        "Requirements Submitted: #{$burial['ref_id']}",
                        "Applicant {$userName} submitted the requested additional information for request #{$burial['ref_id']}.",
                        'damayan',
                        $userName,
                        $tenantId,
                        url('/admin/damayan/burial-requests')
                    );
                } catch (\Throwable $e) {}

                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        $tenantId,
                        "Requirements Received: #{$burial['ref_id']}",
                        "Your additional information for request #{$burial['ref_id']} has been submitted to Damayan staff for review.",
                        'damayan'
                    );
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("submitAdditionalInfo error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign staff coordinator
     */
    public function assignStaff(int $burialId, int $staffId, string $staffName): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $stmt = $this->db->prepare("UPDATE burial_requests SET assigned_staff_id = :sid, assigned_staff_name = :sname WHERE id = :id");
            $res = $stmt->execute(['sid' => $staffId, 'sname' => $staffName, 'id' => $burialId]);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Staff Assignment', 'Case Coordinator Assigned', "Staff Coordinator set to {$staffName}.", $staffName);
            }
            return $res;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getVolunteers(?string $gender = null): array {
        return [];
    }

    public function getDocuments(int $burialId): array {
        return [];
    }

    /**
     * Get all Damayan staff
     */
    public function getAllStaff(): array {
        try {
            $stmt = $this->db->query("SELECT tenant_id AS id, CONCAT(first_name, ' ', last_name) AS name, email, role 
                                      FROM tenant_accounts 
                                      WHERE role IN ('Staff_Damayan', 'Admin') 
                                      ORDER BY role ASC, first_name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Get user's existing deceased records to allow pre-filling/reusing
     */
    public function getDeceasedRecords(int $tenantId): array {
        try {
            $stmt = $this->db->prepare("SELECT DISTINCT d.* 
                                        FROM deceased_records d
                                        LEFT JOIN burial_requests b ON (d.id = b.deceased_id OR d.burial_request_id = b.id)
                                        WHERE b.tenant_id = :tid 
                                        ORDER BY d.created_at DESC");
            $stmt->execute(['tid' => $tenantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Search deceased records across system (for linking or verifying copy requests)
     */
    public function searchDeceasedRecords(string $query = ''): array {
        try {
            $query = trim($query);
            if (empty($query)) {
                $stmt = $this->db->query("SELECT d.*, b.ref_id, b.death_cert_registry_no 
                                          FROM deceased_records d
                                          LEFT JOIN burial_requests b ON (d.id = b.deceased_id OR d.burial_request_id = b.id)
                                          ORDER BY d.created_at DESC LIMIT 50");
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            $stmt = $this->db->prepare("SELECT d.*, d.full_name AS deceased_name, b.ref_id, b.death_cert_registry_no 
                                        FROM deceased_records d
                                        LEFT JOIN burial_requests b ON (d.id = b.deceased_id OR d.burial_request_id = b.id)
                                        WHERE d.full_name LIKE :q1 
                                           OR d.first_name LIKE :q2 
                                           OR d.last_name LIKE :q3 
                                           OR d.ref_id LIKE :q4 
                                           OR b.death_cert_registry_no LIKE :q5
                                        ORDER BY d.created_at DESC LIMIT 50");
            $searchTerm = "%$query%";
            $stmt->execute([
                'q1' => $searchTerm,
                'q2' => $searchTerm,
                'q3' => $searchTerm,
                'q4' => $searchTerm,
                'q5' => $searchTerm
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            error_log("searchDeceasedRecords error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verify and link existing death certificate record for copy request
     * (Requirement 2 & 3: Staff must verify existing death certificate record is available)
     */
    public function verifyExistingRecord(int $burialId, ?int $deceasedId = null, string $staffName = 'Staff Admin'): array {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            $decRecord = null;
            if ($deceasedId) {
                $decStmt = $this->db->prepare("SELECT * FROM deceased_records WHERE id = :did LIMIT 1");
                $decStmt->execute(['did' => $deceasedId]);
                $decRecord = $decStmt->fetch(PDO::FETCH_ASSOC);
            } elseif (!empty($burial['deceased_id'])) {
                $decStmt = $this->db->prepare("SELECT * FROM deceased_records WHERE id = :did LIMIT 1");
                $decStmt->execute(['did' => (int)$burial['deceased_id']]);
                $decRecord = $decStmt->fetch(PDO::FETCH_ASSOC);
                $deceasedId = (int)$burial['deceased_id'];
            }

            if (!$decRecord && $deceasedId) {
                return ['success' => false, 'error' => 'Selected deceased record not found in system'];
            }

            $updateSql = "UPDATE burial_requests 
                          SET existing_record_verified = 1,
                              death_cert_status = IF(death_cert_status = 'Request Received', 'Under Review', death_cert_status),
                              updated_at = NOW()";
            $params = ['id' => $burialId];

            if ($decRecord) {
                $updateSql .= ", deceased_id = :did,
                                 deceased_name = :dname,
                                 deceased_first_name = :fname,
                                 deceased_middle_name = :mname,
                                 deceased_last_name = :lname,
                                 date_of_death = :dod";
                $params['did'] = $decRecord['id'];
                $params['dname'] = $decRecord['full_name'];
                $params['fname'] = $decRecord['first_name'];
                $params['mname'] = $decRecord['middle_name'];
                $params['lname'] = $decRecord['last_name'];
                $params['dod'] = $decRecord['date_of_death'];
            }
            $updateSql .= " WHERE id = :id";

            $stmt = $this->db->prepare($updateSql);
            $stmt->execute($params);

            $linkedName = $decRecord ? $decRecord['full_name'] : $burial['deceased_name'];
            $this->logActivity($burialId, $burial['ref_id'], 'Verification', 'Existing Record Verified',
                "Staff {$staffName} verified that the existing death certificate record for {$linkedName} is on file and authentic.",
                $staffName
            );

            // Notify user
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $notif->create(
                    (int)$burial['tenant_id'],
                    "Record Verified: #{$burial['ref_id']}",
                    "Your request for an additional death certificate copy (#{$burial['ref_id']}) has been verified by staff. Existing record confirmed.",
                    'damayan'
                );
            } catch (\Throwable $e) {}

            return ['success' => true, 'message' => 'Existing death certificate record verified successfully.'];
        } catch (\PDOException $e) {
            error_log("verifyExistingRecord error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Staff issues certificate copy
     * Distinguishes an existing registered cert from a copy request.
     * Copy is marked issued with reference/transmittal number.
     */
    public function issueCertificateCopy(int $burialId, string $copyRefNo, string $staffName = 'Staff Admin', ?string $remarks = null): array {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            $copyRefNo = trim($copyRefNo);
            if (empty($copyRefNo)) {
                return ['success' => false, 'error' => 'Certificate Copy Reference or Tracking Number is required.'];
            }

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET copy_issued_at = NOW(),
                                            death_cert_certified_copy_ref = :copy_ref,
                                            death_cert_status = 'Certified Copy Requested or Received',
                                            death_cert_responsible_personnel = :personnel,
                                            death_cert_remarks = COALESCE(:remarks, death_cert_remarks),
                                            status = 'Under Review',
                                            updated_at = NOW() 
                                        WHERE id = :id");
            $res = $stmt->execute([
                'copy_ref' => $copyRefNo,
                'personnel' => $staffName,
                'remarks' => $remarks,
                'id' => $burialId
            ]);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Copy Issuance', 'Certificate Copy Issued',
                    "Death Certificate copy issued with reference #{$copyRefNo} by {$staffName}. Awaiting user confirmation of receipt.",
                    $staffName
                );

                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$burial['tenant_id'],
                        "Certificate Copy Ready / Issued: #{$burial['ref_id']}",
                        "Your requested death certificate copy has been processed and issued. Reference #: {$copyRefNo}. Please confirm receipt once you receive it.",
                        'damayan'
                    );
                } catch (\Throwable $e) {}

                return ['success' => true, 'message' => 'Certificate copy marked as issued.'];
            }

            return ['success' => false, 'error' => 'Failed to issue certificate copy'];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * User or Staff confirms receipt of the certificate copy
     * The system must not mark a copy as received until this has been confirmed.
     */
    public function confirmCopyReceipt(int $burialId, ?int $tenantId = null, string $userName = 'Applicant'): array {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            if ($tenantId && (int)$burial['tenant_id'] !== $tenantId && !Auth::hasRole(['Admin', 'Staff_Damayan'])) {
                return ['success' => false, 'error' => 'Unauthorized'];
            }

            if (empty($burial['copy_issued_at'])) {
                return ['success' => false, 'error' => 'Certificate copy has not been issued yet. Cannot confirm receipt.'];
            }

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET copy_received_confirmed = 1,
                                            copy_received_at = NOW(),
                                            death_cert_status = 'Certified Copy Requested or Received',
                                            status = 'Completed',
                                            updated_at = NOW() 
                                        WHERE id = :id");
            $res = $stmt->execute(['id' => $burialId]);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Receipt Confirmation', 'Copy Receipt Confirmed',
                    "Receipt of death certificate copy confirmed by {$userName}. Request is now closed and marked completed.",
                    $userName, 'Applicant'
                );

                try {
                    require_once BASE_PATH . '/app/models/AdminNotification.php';
                    $adminNotif = new AdminNotification();
                    $adminNotif->create(
                        "Copy Received: #{$burial['ref_id']}",
                        "Applicant {$userName} confirmed receipt of death certificate copy for #{$burial['ref_id']}.",
                        'damayan',
                        $userName,
                        (int)$burial['tenant_id'],
                        url('/admin/damayan/burial-requests')
                    );
                } catch (\Throwable $e) {}

                return ['success' => true, 'message' => 'Receipt of death certificate copy confirmed successfully.'];
            }

            return ['success' => false, 'error' => 'Failed to confirm receipt'];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

