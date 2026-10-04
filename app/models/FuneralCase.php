<?php

/**
 * FuneralCase Model
 * Handles all database operations for the Islamic Funeral Death Certificate Management System.
 * Tables are auto-created if they don't exist.
 */
class FuneralCase
{
    protected $db;

    public function __construct()
    {
        $this->db = getDbConnection();
        $this->ensureTables();
    }

    /* ========================================================================
     * TABLE CREATION
     * ====================================================================== */

    private function ensureTables(): void
    {
        try {
            // Master funeral case
            $this->db->exec("CREATE TABLE IF NOT EXISTS funeral_cases (
                id INT AUTO_INCREMENT PRIMARY KEY,
                case_number VARCHAR(20) NOT NULL UNIQUE,
                tenant_id VARCHAR(50) NOT NULL,
                status ENUM(
                    'Draft','Submitted','Under Review','Information Verified',
                    'Requirements Incomplete','Ready for Registration',
                    'Submitted to LCRO','Registered','Returned for Correction',
                    'Completed','Rejected'
                ) DEFAULT 'Draft',
                -- Deceased Information
                deceased_first_name VARCHAR(100),
                deceased_middle_name VARCHAR(100),
                deceased_last_name VARCHAR(100),
                deceased_haj_name VARCHAR(100),
                deceased_sex ENUM('Male','Female'),
                deceased_dob DATE,
                deceased_dod DATE,
                deceased_tod TIME,
                deceased_place_of_death VARCHAR(255),
                deceased_address TEXT,
                deceased_civil_status VARCHAR(50),
                deceased_nationality VARCHAR(100) DEFAULT 'Filipino',
                deceased_religion VARCHAR(100) DEFAULT 'Islam',
                deceased_occupation VARCHAR(100),
                -- Family / Informant
                informant_name VARCHAR(200),
                informant_relationship VARCHAR(100),
                informant_contact VARCHAR(50),
                informant_email VARCHAR(200),
                informant_address TEXT,
                -- Islamic Funeral
                burial_rites_person VARCHAR(200),
                imam_name VARCHAR(200),
                surviving_spouses TEXT,
                burial_date DATE,
                burial_time TIME,
                burial_location VARCHAR(255),
                cemetery VARCHAR(255),
                grave_reference VARCHAR(100),
                -- Registration Tracking
                registration_reference VARCHAR(100),
                registration_place VARCHAR(255),
                registration_submitted_at DATETIME,
                registration_completed_at DATETIME,
                assigned_admin_id VARCHAR(50),
                assigned_admin_name VARCHAR(200),
                -- Correction
                correction_reason TEXT,
                rejection_reason TEXT,
                -- Timestamps
                reported_at DATETIME,
                verified_at DATETIME,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                completed_at DATETIME,
                INDEX idx_tenant (tenant_id),
                INDEX idx_status (status),
                INDEX idx_case (case_number)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Documents
            $this->db->exec("CREATE TABLE IF NOT EXISTS funeral_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                case_id INT NOT NULL,
                doc_name VARCHAR(200) NOT NULL,
                doc_type VARCHAR(100),
                file_path VARCHAR(500),
                file_name VARCHAR(255),
                upload_status ENUM('Not Uploaded','Uploaded','Under Review','Verified','Rejected','Resubmission Required') DEFAULT 'Not Uploaded',
                verified_by VARCHAR(200),
                verified_at DATETIME,
                rejection_reason TEXT,
                remarks TEXT,
                uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (case_id) REFERENCES funeral_cases(id) ON DELETE CASCADE,
                INDEX idx_case_doc (case_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // PSA Certificate Requests
            $this->db->exec("CREATE TABLE IF NOT EXISTS funeral_psa_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                case_id INT NOT NULL,
                psa_ref_number VARCHAR(100),
                requesting_party_name VARCHAR(200),
                requesting_party_address TEXT,
                num_copies INT DEFAULT 1,
                purpose VARCHAR(255),
                payment_status ENUM('Unpaid','Paid','Waived') DEFAULT 'Unpaid',
                request_status ENUM('Request Created','Request Validated','Request Submitted','Processing','Ready','Released','Received by Requester') DEFAULT 'Request Created',
                release_method VARCHAR(100),
                release_date DATE,
                delivery_info TEXT,
                remarks TEXT,
                requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (case_id) REFERENCES funeral_cases(id) ON DELETE CASCADE,
                INDEX idx_case_psa (case_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Audit Log
            $this->db->exec("CREATE TABLE IF NOT EXISTS funeral_case_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                case_id INT NOT NULL,
                user_id VARCHAR(50),
                admin_id VARCHAR(50),
                action VARCHAR(100) NOT NULL,
                module VARCHAR(50),
                description TEXT,
                status VARCHAR(50),
                ip_address VARCHAR(45),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (case_id) REFERENCES funeral_cases(id) ON DELETE CASCADE,
                INDEX idx_case_log (case_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Add columns for Certificate Tracking & Release Scheduling if not present
            $extraCols = [
                "ADD COLUMN IF NOT EXISTS certificate_type VARCHAR(100) DEFAULT 'Death Certificate'",
                "ADD COLUMN IF NOT EXISTS certificate_status VARCHAR(50) DEFAULT 'Requested'",
                "ADD COLUMN IF NOT EXISTS processing_started_at DATETIME NULL",
                "ADD COLUMN IF NOT EXISTS ready_at DATETIME NULL",
                "ADD COLUMN IF NOT EXISTS scheduled_release_date DATE NULL",
                "ADD COLUMN IF NOT EXISTS scheduled_release_time TIME NULL",
                "ADD COLUMN IF NOT EXISTS release_location VARCHAR(255) DEFAULT 'Masjid Office'",
                "ADD COLUMN IF NOT EXISTS assigned_staff_id VARCHAR(50) NULL",
                "ADD COLUMN IF NOT EXISTS assigned_staff_name VARCHAR(200) NULL",
                "ADD COLUMN IF NOT EXISTS release_method VARCHAR(100) DEFAULT 'Office Pick-up'",
                "ADD COLUMN IF NOT EXISTS release_notes TEXT NULL",
                "ADD COLUMN IF NOT EXISTS reschedule_reason TEXT NULL",
                "ADD COLUMN IF NOT EXISTS released_at DATETIME NULL",
                "ADD COLUMN IF NOT EXISTS released_by VARCHAR(200) NULL",
                "ADD COLUMN IF NOT EXISTS recipient_name VARCHAR(200) NULL"
            ];
            foreach ($extraCols as $colSql) {
                try {
                    $this->db->exec("ALTER TABLE funeral_cases $colSql");
                } catch (PDOException $pe) {
                    // Ignore column existence errors
                }
            }

        } catch (PDOException $e) {
            error_log("FuneralCase ensureTables error: " . $e->getMessage());
        }
    }

    /* ========================================================================
     * CASE NUMBER GENERATION
     * ====================================================================== */

    public function generateCaseNumber(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM funeral_cases WHERE case_number LIKE :p1 OR case_number LIKE :p2");
        $stmt->execute(['p1' => "DC-{$year}-%", 'p2' => "ISL-{$year}-%"]);
        $count = (int) $stmt->fetchColumn() + 1;
        return sprintf("DC-%s-%05d", $year, $count);
    }

    /* ========================================================================
     * CRUD OPERATIONS
     * ====================================================================== */

    public function create(array $d): ?int
    {
        try {
            $caseNum = $this->generateCaseNumber();
            $sql = "INSERT INTO funeral_cases (
                case_number, tenant_id, status, certificate_type, certificate_status,
                deceased_first_name, deceased_middle_name, deceased_last_name, deceased_haj_name,
                deceased_sex, deceased_dob, deceased_dod, deceased_tod,
                deceased_place_of_death, deceased_address, deceased_civil_status,
                deceased_nationality, deceased_religion, deceased_occupation,
                informant_name, informant_relationship, informant_contact, informant_email, informant_address,
                burial_rites_person, imam_name, surviving_spouses,
                burial_date, burial_time, burial_location, cemetery, grave_reference,
                reported_at, submitted_at
            ) VALUES (
                :case_number, :tenant_id, 'Submitted', 'Death Certificate', 'Requested',
                :d_fn, :d_mn, :d_ln, :d_haj,
                :d_sex, :d_dob, :d_dod, :d_tod,
                :d_pod, :d_addr, :d_civil,
                :d_nat, :d_rel, :d_occ,
                :i_name, :i_rel, :i_contact, :i_email, :i_addr,
                :b_rites, :b_imam, :b_spouses,
                :b_date, :b_time, :b_loc, :b_cem, :b_grave,
                NOW(), NOW()
            )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'case_number'  => $caseNum,
                'tenant_id'    => $d['tenant_id'],
                'd_fn'         => $d['deceased_first_name'] ?? '',
                'd_mn'         => $d['deceased_middle_name'] ?? '',
                'd_ln'         => $d['deceased_last_name'] ?? '',
                'd_haj'        => $d['deceased_haj_name'] ?? '',
                'd_sex'        => $d['deceased_sex'] ?? null,
                'd_dob'        => $d['deceased_dob'] ?? null,
                'd_dod'        => $d['deceased_dod'] ?? null,
                'd_tod'        => $d['deceased_tod'] ?? null,
                'd_pod'        => $d['deceased_place_of_death'] ?? '',
                'd_addr'       => $d['deceased_address'] ?? '',
                'd_civil'      => $d['deceased_civil_status'] ?? '',
                'd_nat'        => $d['deceased_nationality'] ?? 'Filipino',
                'd_rel'        => $d['deceased_religion'] ?? 'Islam',
                'd_occ'        => $d['deceased_occupation'] ?? '',
                'i_name'       => $d['informant_name'] ?? '',
                'i_rel'        => $d['informant_relationship'] ?? '',
                'i_contact'    => $d['informant_contact'] ?? '',
                'i_email'      => $d['informant_email'] ?? '',
                'i_addr'       => $d['informant_address'] ?? '',
                'b_rites'      => $d['burial_rites_person'] ?? '',
                'b_imam'       => $d['imam_name'] ?? '',
                'b_spouses'    => $d['surviving_spouses'] ?? '',
                'b_date'       => $d['burial_date'] ?? null,
                'b_time'       => $d['burial_time'] ?? null,
                'b_loc'        => $d['burial_location'] ?? '',
                'b_cem'        => $d['cemetery'] ?? '',
                'b_grave'      => $d['grave_reference'] ?? ''
            ]);
            $caseId = (int) $this->db->lastInsertId();

            // Create default document slots
            $defaultDocs = [
                ['Identification Document of Informant', 'identification'],
                ['Supporting Death Documentation', 'death_support'],
                ['Burial-Related Documentation', 'burial_doc'],
                ['Other Required Documents', 'other']
            ];
            $docStmt = $this->db->prepare("INSERT INTO funeral_documents (case_id, doc_name, doc_type, upload_status) VALUES (:cid, :name, :type, 'Not Uploaded')");
            foreach ($defaultDocs as $doc) {
                $docStmt->execute(['cid' => $caseId, 'name' => $doc[0], 'type' => $doc[1]]);
            }

            // Log for audit
            $this->addLog($caseId, $_SESSION['user_id'] ?? '', null, 'CERTIFICATE_REQUESTED', 'CERTIFICATE',
                "Death Certificate request submitted for " . trim(($d['deceased_first_name'] ?? '') . ' ' . ($d['deceased_last_name'] ?? '')),
                'Requested');

            if (class_exists('AuditLogger')) {
                AuditLogger::log('DAMAYAN', 'CERTIFICATE_REQUESTED', "Death certificate request {$caseNum} created");
            }

            return $caseId;
        } catch (PDOException $e) {
            error_log("FuneralCase create error: " . $e->getMessage());
            return null;
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM funeral_cases WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByCaseNumber(string $caseNum): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM funeral_cases WHERE case_number = :cn");
        $stmt->execute(['cn' => $caseNum]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByTenantId(string $tenantId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM funeral_cases WHERE tenant_id = :tid ORDER BY submitted_at DESC");
        $stmt->execute(['tid' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAll(string $statusFilter = ''): array
    {
        $sql = "SELECT fc.*, t.first_name AS t_first, t.last_name AS t_last, t.email AS t_email
                FROM funeral_cases fc
                LEFT JOIN tenant_accounts t ON fc.tenant_id = t.tenant_id
                " . ($statusFilter ? "WHERE fc.status = :st" : "") . "
                ORDER BY fc.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        if ($statusFilter) $stmt->execute(['st' => $statusFilter]);
        else $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateStatus(int $id, string $status, ?string $reason = null): bool
    {
        try {
            $fields = ['status = :status', 'updated_at = NOW()'];
            $params = ['status' => $status, 'id' => $id];

            if ($status === 'Information Verified') {
                $fields[] = 'verified_at = NOW()';
            } elseif ($status === 'Submitted to LCRO') {
                $fields[] = 'registration_submitted_at = NOW()';
            } elseif ($status === 'Registered') {
                $fields[] = 'registration_completed_at = NOW()';
            } elseif ($status === 'Completed') {
                $fields[] = 'completed_at = NOW()';
            } elseif ($status === 'Returned for Correction') {
                $fields[] = 'correction_reason = :reason';
                $params['reason'] = $reason ?? '';
            } elseif ($status === 'Rejected') {
                $fields[] = 'rejection_reason = :reason';
                $params['reason'] = $reason ?? '';
            }

            $sql = "UPDATE funeral_cases SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute($params);

            if ($ok) {
                $this->addLog($id, null, $_SESSION['user_id'] ?? null, 'STATUS_UPDATE', 'FUNERAL',
                    "Case status changed to: {$status}" . ($reason ? " — Reason: {$reason}" : ''), $status);
            }
            return $ok;
        } catch (PDOException $e) {
            error_log("FuneralCase updateStatus error: " . $e->getMessage());
            return false;
        }
    }

    public function updateRegistration(int $id, array $d): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE funeral_cases SET
                registration_reference = :ref, registration_place = :place,
                assigned_admin_id = :aid, assigned_admin_name = :aname,
                updated_at = NOW() WHERE id = :id");
            return $stmt->execute([
                'ref'   => $d['registration_reference'] ?? '',
                'place' => $d['registration_place'] ?? '',
                'aid'   => $d['assigned_admin_id'] ?? '',
                'aname' => $d['assigned_admin_name'] ?? '',
                'id'    => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ========================================================================
     * DOCUMENTS
     * ====================================================================== */

    public function getDocuments(int $caseId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM funeral_documents WHERE case_id = :cid ORDER BY id ASC");
        $stmt->execute(['cid' => $caseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateDocument(int $docId, array $d): bool
    {
        try {
            $fields = ['updated_at = NOW()'];
            $params = ['id' => $docId];

            if (isset($d['file_path'])) {
                $fields[] = 'file_path = :fp';
                $fields[] = 'file_name = :fn';
                $fields[] = 'upload_status = :us';
                $fields[] = 'uploaded_at = NOW()';
                $params['fp'] = $d['file_path'];
                $params['fn'] = $d['file_name'];
                $params['us'] = 'Uploaded';
            }
            if (isset($d['upload_status'])) {
                $fields[] = 'upload_status = :us2';
                $params['us2'] = $d['upload_status'];
            }
            if (isset($d['rejection_reason'])) {
                $fields[] = 'rejection_reason = :rr';
                $params['rr'] = $d['rejection_reason'];
            }
            if (isset($d['remarks'])) {
                $fields[] = 'remarks = :rem';
                $params['rem'] = $d['remarks'];
            }
            if (isset($d['verified_by'])) {
                $fields[] = 'verified_by = :vb';
                $fields[] = 'verified_at = NOW()';
                $params['vb'] = $d['verified_by'];
            }

            $sql = "UPDATE funeral_documents SET " . implode(', ', $fields) . " WHERE id = :id";
            return $this->db->prepare($sql)->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ========================================================================
     * PSA REQUESTS
     * ====================================================================== */

    public function createPsaRequest(int $caseId, array $d): ?int
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO funeral_psa_requests
                (case_id, requesting_party_name, requesting_party_address, num_copies, purpose)
                VALUES (:cid, :name, :addr, :copies, :purpose)");
            $stmt->execute([
                'cid'     => $caseId,
                'name'    => $d['requesting_party_name'] ?? '',
                'addr'    => $d['requesting_party_address'] ?? '',
                'copies'  => $d['num_copies'] ?? 1,
                'purpose' => $d['purpose'] ?? ''
            ]);
            $psaId = (int) $this->db->lastInsertId();

            $this->addLog($caseId, $_SESSION['user_id'] ?? '', null, 'PSA_REQUEST_CREATED', 'PSA',
                "PSA Death Certificate request created ({$d['num_copies']} copies)", 'Request Created');

            return $psaId;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getPsaRequest(int $caseId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM funeral_psa_requests WHERE case_id = :cid ORDER BY id DESC LIMIT 1");
        $stmt->execute(['cid' => $caseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePsaRequest(int $psaId, array $d): bool
    {
        try {
            $fields = ['updated_at = NOW()'];
            $params = ['id' => $psaId];

            foreach (['psa_ref_number','request_status','payment_status','release_method','release_date','delivery_info','remarks'] as $f) {
                if (isset($d[$f])) {
                    $fields[] = "{$f} = :{$f}";
                    $params[$f] = $d[$f];
                }
            }

            $sql = "UPDATE funeral_psa_requests SET " . implode(', ', $fields) . " WHERE id = :id";
            return $this->db->prepare($sql)->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ========================================================================
     * AUDIT LOG
     * ====================================================================== */

    public function addLog(int $caseId, ?string $userId, ?string $adminId, string $action, string $module, string $desc, string $status = ''): void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO funeral_case_logs
                (case_id, user_id, admin_id, action, module, description, status, ip_address)
                VALUES (:cid, :uid, :aid, :act, :mod, :desc, :st, :ip)");
            $stmt->execute([
                'cid'  => $caseId,
                'uid'  => $userId,
                'aid'  => $adminId,
                'act'  => $action,
                'mod'  => $module,
                'desc' => $desc,
                'st'   => $status,
                'ip'   => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);
        } catch (PDOException $e) {
            error_log("FuneralCase log error: " . $e->getMessage());
        }
    }

    public function getLogs(int $caseId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM funeral_case_logs WHERE case_id = :cid ORDER BY created_at DESC");
        $stmt->execute(['cid' => $caseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ========================================================================
     * ANALYTICS
     * ====================================================================== */

    public function getAnalytics(): array
    {
        try {
            $stats = [
                'total' => 0, 'new_reports' => 0, 'for_verification' => 0,
                'missing_req' => 0, 'ready_reg' => 0, 'submitted_lcro' => 0,
                'registered' => 0, 'psa_requests' => 0, 'completed' => 0
            ];

            $rows = $this->db->query("SELECT status, COUNT(*) as cnt FROM funeral_cases GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $s = $r['status'];
                $c = (int)$r['cnt'];
                $stats['total'] += $c;
                if ($s === 'Submitted') $stats['new_reports'] += $c;
                if ($s === 'Under Review') $stats['for_verification'] += $c;
                if ($s === 'Requirements Incomplete' || $s === 'Returned for Correction') $stats['missing_req'] += $c;
                if ($s === 'Ready for Registration') $stats['ready_reg'] += $c;
                if ($s === 'Submitted to LCRO') $stats['submitted_lcro'] += $c;
                if ($s === 'Registered') $stats['registered'] += $c;
                if ($s === 'Completed') $stats['completed'] += $c;
            }

            $stats['psa_requests'] = (int) $this->db->query("SELECT COUNT(*) FROM funeral_psa_requests")->fetchColumn();

            return $stats;
        } catch (PDOException $e) {
            return ['total'=>0,'new_reports'=>0,'for_verification'=>0,'missing_req'=>0,'ready_reg'=>0,'submitted_lcro'=>0,'registered'=>0,'psa_requests'=>0,'completed'=>0];
        }
    }

    /* ========================================================================
     * CERTIFICATE TRACKING & RELEASE SCHEDULING
     * ====================================================================== */

    /**
     * Get all certificate requests with filter and search
     */
    public function getAllCertificates(?string $statusFilter = '', ?string $search = ''): array
    {
        try {
            $sql = "SELECT fc.*, 
                           t.first_name AS t_first, t.last_name AS t_last, t.email AS t_email, t.contact_number AS t_contact
                    FROM funeral_cases fc
                    LEFT JOIN tenant_accounts t ON fc.tenant_id = t.tenant_id
                    WHERE 1=1";
            $params = [];

            if (!empty($statusFilter) && $statusFilter !== 'all') {
                $sql .= " AND fc.certificate_status = :st";
                $params['st'] = $statusFilter;
            }

            if (!empty($search)) {
                $sql .= " AND (fc.case_number LIKE :q 
                            OR fc.deceased_first_name LIKE :q 
                            OR fc.deceased_last_name LIKE :q 
                            OR fc.informant_name LIKE :q
                            OR t.first_name LIKE :q
                            OR t.last_name LIKE :q)";
                $params['q'] = "%$search%";
            }

            $sql .= " ORDER BY fc.updated_at DESC, fc.submitted_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("getAllCertificates error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update certificate status through the workflow stages:
     * Requested -> Under Review -> Processing -> Ready for Release -> Scheduled for Release -> Released
     * Optional: Rejected / Cancelled
     */
    public function updateCertificateStatus(int $caseId, string $status, ?string $reason = null, ?string $adminId = null, ?string $adminName = null): bool
    {
        try {
            $case = $this->findById($caseId);
            if (!$case) return false;

            $fields = ['certificate_status = :st', 'updated_at = NOW()'];
            $params = ['st' => $status, 'id' => $caseId];

            if ($status === 'Processing') {
                $fields[] = 'processing_started_at = COALESCE(processing_started_at, NOW())';
            } elseif ($status === 'Ready for Release') {
                $fields[] = 'ready_at = COALESCE(ready_at, NOW())';
            } elseif ($status === 'Cancelled' || $status === 'Rejected') {
                $fields[] = 'rejection_reason = :rr';
                $params['rr'] = $reason ?? '';
            }

            $sql = "UPDATE funeral_cases SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute($params);

            if ($ok) {
                $desc = "Certificate status updated to: {$status}" . ($reason ? " — Reason: {$reason}" : '');
                $actionCode = 'CERTIFICATE_' . strtoupper(str_replace(' ', '_', $status));
                $this->addLog($caseId, null, $adminId, $actionCode, 'CERTIFICATE', $desc, $status);

                if (class_exists('AuditLogger')) {
                    AuditLogger::log('DAMAYAN', $actionCode, "Case #{$case['case_number']} — {$desc} by " . ($adminName ?: 'Admin'));
                }
            }
            return $ok;
        } catch (PDOException $e) {
            error_log("updateCertificateStatus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Schedule a certificate release with validation against past dates/times
     */
    public function scheduleRelease(int $caseId, array $data, ?string $adminId = null, ?string $adminName = null): array
    {
        try {
            $case = $this->findById($caseId);
            if (!$case) {
                return ['success' => false, 'error' => 'Certificate request not found.'];
            }

            if (($case['certificate_status'] ?? '') === 'Released') {
                return ['success' => false, 'error' => 'This certificate has already been released and cannot be scheduled.'];
            }

            $date = trim($data['release_date'] ?? '');
            $time = trim($data['release_time'] ?? '');

            if (empty($date) || empty($time)) {
                return ['success' => false, 'error' => 'Both release date and release time are required.'];
            }

            // Prevent scheduling in the past
            $today = date('Y-m-d');
            $nowTime = date('H:i');
            if ($date < $today) {
                return ['success' => false, 'error' => 'Release date cannot be in the past. Please select a valid future date.'];
            }
            if ($date === $today && $time < $nowTime) {
                return ['success' => false, 'error' => 'Release time cannot be in the past for today. Please select a valid time.'];
            }

            $location = !empty($data['release_location']) ? trim($data['release_location']) : 'Masjid Office';
            $staffId = !empty($data['assigned_staff_id']) ? trim($data['assigned_staff_id']) : null;
            $staffName = !empty($data['assigned_staff_name']) ? trim($data['assigned_staff_name']) : null;
            $notes = !empty($data['release_notes']) ? trim($data['release_notes']) : null;

            $sql = "UPDATE funeral_cases SET 
                certificate_status = 'Scheduled for Release',
                scheduled_release_date = :s_date,
                scheduled_release_time = :s_time,
                release_location = :s_loc,
                assigned_staff_id = :s_sid,
                assigned_staff_name = :s_sname,
                release_notes = :s_notes,
                updated_at = NOW()
                WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                's_date'  => $date,
                's_time'  => $time,
                's_loc'   => $location,
                's_sid'   => $staffId,
                's_sname' => $staffName,
                's_notes' => $notes,
                'id'      => $caseId
            ]);

            if ($ok) {
                $formattedDate = date('F j, Y', strtotime($date));
                $formattedTime = date('g:i A', strtotime($time));
                $desc = "Release scheduled for {$formattedDate} at {$formattedTime} at {$location}" . ($staffName ? " (Staff: {$staffName})" : '');
                
                $this->addLog($caseId, null, $adminId, 'CERTIFICATE_RELEASE_SCHEDULED', 'CERTIFICATE', $desc, 'Scheduled for Release');

                if (class_exists('AuditLogger')) {
                    AuditLogger::log('DAMAYAN', 'CERTIFICATE_RELEASE_SCHEDULED', 
                        "Case #{$case['case_number']} — {$desc} by " . ($adminName ?: 'Admin Staff'));
                }

                return [
                    'success' => true,
                    'message' => 'Release scheduled successfully.',
                    'scheduled_date' => $formattedDate,
                    'scheduled_time' => $formattedTime,
                    'location' => $location
                ];
            }

            return ['success' => false, 'error' => 'Database update failed.'];
        } catch (PDOException $e) {
            error_log("scheduleRelease error: " . $e->getMessage());
            return ['success' => false, 'error' => 'An unexpected database error occurred.'];
        }
    }

    /**
     * Reschedule a certificate release, tracking previous and new date/time with audit trail
     */
    public function rescheduleRelease(int $caseId, array $data, ?string $adminId = null, ?string $adminName = null): array
    {
        try {
            $case = $this->findById($caseId);
            if (!$case) {
                return ['success' => false, 'error' => 'Certificate request not found.'];
            }

            $date = trim($data['release_date'] ?? '');
            $time = trim($data['release_time'] ?? '');
            $reason = trim($data['reschedule_reason'] ?? 'Schedule adjusted by staff');

            if (empty($date) || empty($time)) {
                return ['success' => false, 'error' => 'New release date and time are required.'];
            }

            $today = date('Y-m-d');
            $nowTime = date('H:i');
            if ($date < $today) {
                return ['success' => false, 'error' => 'New release date cannot be in the past.'];
            }
            if ($date === $today && $time < $nowTime) {
                return ['success' => false, 'error' => 'New release time cannot be in the past for today.'];
            }

            $prevDate = $case['scheduled_release_date'] ? date('F j, Y', strtotime($case['scheduled_release_date'])) : 'Not set';
            $prevTime = $case['scheduled_release_time'] ? date('g:i A', strtotime($case['scheduled_release_time'])) : 'Not set';
            $newDate = date('F j, Y', strtotime($date));
            $newTime = date('g:i A', strtotime($time));

            $location = !empty($data['release_location']) ? trim($data['release_location']) : ($case['release_location'] ?: 'Masjid Office');
            $staffId = !empty($data['assigned_staff_id']) ? trim($data['assigned_staff_id']) : $case['assigned_staff_id'];
            $staffName = !empty($data['assigned_staff_name']) ? trim($data['assigned_staff_name']) : $case['assigned_staff_name'];
            $notes = isset($data['release_notes']) ? trim($data['release_notes']) : $case['release_notes'];

            $sql = "UPDATE funeral_cases SET 
                certificate_status = 'Scheduled for Release',
                scheduled_release_date = :s_date,
                scheduled_release_time = :s_time,
                release_location = :s_loc,
                assigned_staff_id = :s_sid,
                assigned_staff_name = :s_sname,
                release_notes = :s_notes,
                reschedule_reason = :s_reason,
                updated_at = NOW()
                WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                's_date'   => $date,
                's_time'   => $time,
                's_loc'    => $location,
                's_sid'    => $staffId,
                's_sname'  => $staffName,
                's_notes'  => $notes,
                's_reason' => $reason,
                'id'       => $caseId
            ]);

            if ($ok) {
                $desc = "Release Schedule Updated — Previous: {$prevDate} at {$prevTime} | New: {$newDate} at {$newTime} | Reason: {$reason} | Changed by: " . ($adminName ?: 'Admin Staff');
                
                $this->addLog($caseId, null, $adminId, 'CERTIFICATE_RESCHEDULED', 'CERTIFICATE', $desc, 'Scheduled for Release');

                if (class_exists('AuditLogger')) {
                    AuditLogger::log('DAMAYAN', 'CERTIFICATE_RESCHEDULED', "Case #{$case['case_number']} — {$desc}");
                }

                return [
                    'success' => true,
                    'message' => 'Release rescheduled successfully.',
                    'scheduled_date' => $newDate,
                    'scheduled_time' => $newTime,
                    'location' => $location
                ];
            }

            return ['success' => false, 'error' => 'Database update failed.'];
        } catch (PDOException $e) {
            error_log("rescheduleRelease error: " . $e->getMessage());
            return ['success' => false, 'error' => 'An unexpected database error occurred.'];
        }
    }

    /**
     * Mark certificate as released to user
     */
    public function markAsReleased(int $caseId, array $data, ?string $adminId = null, ?string $adminName = null): array
    {
        try {
            $case = $this->findById($caseId);
            if (!$case) {
                return ['success' => false, 'error' => 'Certificate request not found.'];
            }

            $releasedBy = !empty($data['released_by']) ? trim($data['released_by']) : ($adminName ?: 'Admin Staff');
            $recipient  = !empty($data['recipient_name']) ? trim($data['recipient_name']) : ($case['informant_name'] ?: 'Authorized Requester');
            $method     = !empty($data['release_method']) ? trim($data['release_method']) : ($case['release_method'] ?: 'Office Pick-up');
            $notes      = isset($data['release_notes']) ? trim($data['release_notes']) : $case['release_notes'];

            $sql = "UPDATE funeral_cases SET 
                certificate_status = 'Released',
                status = 'Completed',
                released_at = NOW(),
                completed_at = COALESCE(completed_at, NOW()),
                released_by = :rel_by,
                recipient_name = :recip,
                release_method = :rmeth,
                release_notes = :rnotes,
                updated_at = NOW()
                WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                'rel_by' => $releasedBy,
                'recip'  => $recipient,
                'rmeth'  => $method,
                'rnotes' => $notes,
                'id'     => $caseId
            ]);

            if ($ok) {
                $nowStr = date('F j, Y, g:i A');
                $desc = "Certificate Released — Released to: {$recipient} by {$releasedBy} via {$method}" . ($notes ? " (Notes: {$notes})" : '');
                
                $this->addLog($caseId, null, $adminId, 'CERTIFICATE_RELEASED', 'CERTIFICATE', $desc, 'Released');

                if (class_exists('AuditLogger')) {
                    AuditLogger::log('DAMAYAN', 'CERTIFICATE_RELEASED', "Case #{$case['case_number']} — {$desc}");
                }

                return [
                    'success' => true,
                    'message' => 'Certificate successfully marked as released.',
                    'released_at' => $nowStr,
                    'recipient' => $recipient
                ];
            }

            return ['success' => false, 'error' => 'Database update failed.'];
        } catch (PDOException $e) {
            error_log("markAsReleased error: " . $e->getMessage());
            return ['success' => false, 'error' => 'An unexpected database error occurred.'];
        }
    }

    /**
     * Get staff list for assigned staff dropdown
     */
    public function getStaffList(): array
    {
        try {
            $stmt = $this->db->query("SELECT tenant_id, first_name, last_name, email, role 
                                      FROM tenant_accounts 
                                      WHERE role IN ('Staff_Damayan', 'Admin', 'Staff_Tenant', 'Staff_Male', 'Staff_Female')
                                      ORDER BY role = 'Staff_Damayan' DESC, first_name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get certificate tracking analytics
     */
    public function getCertificateAnalytics(): array
    {
        $stats = [
            'total'        => 0,
            'requested'    => 0,
            'under_review' => 0,
            'processing'   => 0,
            'ready'        => 0,
            'scheduled'    => 0,
            'released'     => 0,
            'cancelled'    => 0
        ];
        try {
            $rows = $this->db->query("SELECT certificate_status, COUNT(*) as cnt FROM funeral_cases GROUP BY certificate_status")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $s = strtolower(str_replace(' ', '_', $r['certificate_status'] ?? ''));
                $cnt = (int)$r['cnt'];
                $stats['total'] += $cnt;
                if ($s === 'requested') $stats['requested'] += $cnt;
                elseif ($s === 'under_review') $stats['under_review'] += $cnt;
                elseif ($s === 'processing') $stats['processing'] += $cnt;
                elseif ($s === 'ready_for_release' || $s === 'ready') $stats['ready'] += $cnt;
                elseif ($s === 'scheduled_for_release' || $s === 'scheduled') $stats['scheduled'] += $cnt;
                elseif ($s === 'released') $stats['released'] += $cnt;
                elseif ($s === 'cancelled' || $s === 'rejected') $stats['cancelled'] += $cnt;
            }
        } catch (PDOException $e) {}
        return $stats;
    }
}

