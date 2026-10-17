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
     * Normalize registration number for robust matching:
     * Strips leading '#', 'No.', '№', whitespace, dashes, slashes, and lowercases.
     * e.g. '#2023-2-0001113' -> '202320001113'
     *      '2023-2-0001113'  -> '202320001113'
     */
    public static function normalizeRegNo(?string $val): string {
        if ($val === null) return '';
        $val = trim($val);
        $val = preg_replace('/^(#|no\.?|№)\s*/i', '', $val);
        return strtolower(preg_replace('/[\s\-_#\/]/', '', $val));
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
                status VARCHAR(50) DEFAULT 'Pending',
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
            try {
                $this->db->exec("ALTER TABLE burial_requests ADD COLUMN encoded_cert_id INT NULL AFTER deceased_id");
            } catch (\Throwable $e) {}
            try {
                $this->db->exec("ALTER TABLE encoded_death_certificates ADD COLUMN burial_request_id INT NULL AFTER representative_name");
            } catch (\Throwable $e) {}
            try {
                $this->db->exec("ALTER TABLE encoded_death_certificates CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            } catch (\Throwable $e) {}

            $encCols = [
                "province VARCHAR(150) NULL",
                "city_municipality VARCHAR(150) NULL",
                "first_name VARCHAR(100) NULL",
                "middle_name VARCHAR(100) NULL",
                "last_name VARCHAR(100) NULL",
                "sex ENUM('Male','Female') DEFAULT 'Male'",
                "date_of_death_day INT NULL",
                "date_of_death_month INT NULL",
                "date_of_death_year INT NULL",
                "date_of_death DATE NULL",
                "time_of_death TIME NULL",
                "date_of_birth_day INT NULL",
                "date_of_birth_month INT NULL",
                "date_of_birth_year INT NULL",
                "date_of_birth DATE NULL",
                "age_years INT NULL",
                "age_months INT NULL",
                "age_days INT NULL",
                "age_hours INT NULL",
                "age_unit VARCHAR(20) DEFAULT 'years'",
                "age_display VARCHAR(100) NULL",
                "place_of_death_hospital VARCHAR(255) NULL",
                "place_of_death_house_no VARCHAR(100) NULL",
                "place_of_death_street VARCHAR(255) NULL",
                "place_of_death_barangay VARCHAR(150) NULL",
                "place_of_death_city VARCHAR(150) NULL",
                "place_of_death_province VARCHAR(150) NULL",
                "place_of_death TEXT NULL",
                "civil_status VARCHAR(50) NULL",
                "religion VARCHAR(100) DEFAULT 'Islam'",
                "citizenship VARCHAR(100) DEFAULT 'Filipino'",
                "residence_house_no VARCHAR(100) NULL",
                "residence_street VARCHAR(255) NULL",
                "residence_barangay VARCHAR(150) NULL",
                "residence_city VARCHAR(150) NULL",
                "residence_province VARCHAR(150) NULL",
                "residence_country VARCHAR(100) DEFAULT 'Philippines'",
                "residence TEXT NULL",
                "occupation VARCHAR(150) NULL"
            ];
            foreach ($encCols as $ecDef) {
                try {
                    $this->db->exec("ALTER TABLE encoded_death_certificates ADD COLUMN $ecDef");
                } catch (\Throwable $e) {}
            }

            $colsToAdd = [
                "cert_action_type VARCHAR(50) DEFAULT 'register'",
                "existing_cert_no VARCHAR(100) NULL",
                "copy_reason TEXT NULL",
                "num_copies INT DEFAULT 1",
                "existing_record_verified TINYINT(1) DEFAULT 0",
                "encoded_cert_id INT NULL",
                "copy_issued_at DATETIME NULL",
                "copy_received_at DATETIME NULL",
                "copy_received_confirmed TINYINT(1) DEFAULT 0",
                "verification_result VARCHAR(50) DEFAULT 'Pending'",
                "verification_remarks TEXT NULL",
                "verified_at DATETIME NULL",
                "verified_by VARCHAR(200) NULL",
                "rejection_reason_category VARCHAR(255) NULL",
                "rejection_specific_reason TEXT NULL",
                "duplicate_review_decision VARCHAR(50) NULL",
                "duplicate_reviewed_by VARCHAR(200) NULL",
                "duplicate_reviewed_at DATETIME NULL",
                "duplicate_review_notes TEXT NULL",
                "rejected_by VARCHAR(200) NULL",
                "rejected_at DATETIME NULL",
                "is_archived TINYINT(1) DEFAULT 0",
                "archived_at DATETIME NULL",
                "archived_by VARCHAR(200) NULL"
            ];
            foreach ($colsToAdd as $colDef) {
                try {
                    $this->db->exec("ALTER TABLE burial_requests ADD COLUMN $colDef");
                } catch (\Throwable $e) {}
            }
            try {
                $this->db->exec("ALTER TABLE burial_requests MODIFY COLUMN status VARCHAR(50) DEFAULT 'Pending'");
            } catch (\Throwable $e) {}
            try {
                $this->db->exec("UPDATE burial_requests 
                                 SET status = 'Pending' 
                                 WHERE status IS NULL 
                                    OR status = '' 
                                    OR status NOT IN ('Accepted', 'Rejected', 'Completed')");
            } catch (\Throwable $e) {}
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

                // If not found yet, check encoded_death_certificates by registration number
                $encodedCertId = !empty($data['encoded_cert_id']) ? (int)$data['encoded_cert_id'] : null;
                if (!$encodedCertId && $existingCertNo) {
                    $normCertNo = self::normalizeRegNo($existingCertNo);
                    $encStmt = $this->db->prepare("SELECT * FROM encoded_death_certificates 
                                                   WHERE LOWER(TRIM(registration_number)) = LOWER(TRIM(:cno))
                                                      OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm
                                                   ORDER BY (LOWER(TRIM(registration_number)) = LOWER(TRIM(:cno))) DESC
                                                   LIMIT 1");
                    $encStmt->execute(['cno' => $existingCertNo, 'norm' => $normCertNo]);
                    $foundEnc = $encStmt->fetch(PDO::FETCH_ASSOC);
                    if ($foundEnc) {
                        $encodedCertId = (int)$foundEnc['id'];
                        $existingRecordVerified = 1;
                        if (empty($fullName) || $fullName === 'Applicant') {
                            $fullName = $foundEnc['deceased_name'];
                        }
                    }
                } elseif ($encodedCertId) {
                    $encStmt = $this->db->prepare("SELECT * FROM encoded_death_certificates WHERE id = :eid LIMIT 1");
                    $encStmt->execute(['eid' => $encodedCertId]);
                    $foundEnc = $encStmt->fetch(PDO::FETCH_ASSOC);
                    if ($foundEnc) {
                        $existingRecordVerified = 1;
                        if (empty($fullName) || $fullName === 'Applicant') {
                            $fullName = $foundEnc['deceased_name'];
                        }
                    }
                }

                // Try searching burial_requests by death_cert_registry_no or ref_id
                if (!$deceasedId && !$encodedCertId && $existingCertNo) {
                    $normCertNo = self::normalizeRegNo($existingCertNo);
                    $certStmt = $this->db->prepare("SELECT deceased_id FROM burial_requests 
                                                    WHERE (death_cert_registry_no = :cno1 OR ref_id = :cno2
                                                       OR (death_cert_registry_no IS NOT NULL AND death_cert_registry_no != '' AND REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(death_cert_registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm)) 
                                                      AND deceased_id IS NOT NULL LIMIT 1");
                    $certStmt->execute(['cno1' => $existingCertNo, 'cno2' => $existingCertNo, 'norm' => $normCertNo]);
                    $foundId = $certStmt->fetchColumn();
                    if ($foundId) {
                        $deceasedId = (int)$foundId;
                        $existingRecordVerified = 1;
                    }
                }

                if (!$deceasedId && !$encodedCertId && !empty($fullName)) {
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
            } else {
                $encodedCertId = !empty($data['encoded_cert_id']) ? (int)$data['encoded_cert_id'] : null;
                if (!$encodedCertId && !empty($existingCertNo)) {
                    $normCertNo = self::normalizeRegNo($existingCertNo);
                    $encStmt = $this->db->prepare("SELECT * FROM encoded_death_certificates 
                                                   WHERE LOWER(TRIM(registration_number)) = LOWER(TRIM(:cno))
                                                      OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm
                                                   ORDER BY (LOWER(TRIM(registration_number)) = LOWER(TRIM(:cno))) DESC
                                                   LIMIT 1");
                    $encStmt->execute(['cno' => $existingCertNo, 'norm' => $normCertNo]);
                    $foundEnc = $encStmt->fetch(PDO::FETCH_ASSOC);
                    if ($foundEnc) {
                        $encodedCertId = (int)$foundEnc['id'];
                        $existingRecordVerified = 1;
                    }
                }
            }

            $initialStatus = 'Pending';

            $sql = "INSERT INTO burial_requests (
                        ref_id, tenant_id, deceased_id, encoded_cert_id, deceased_name, deceased_first_name, deceased_middle_name, deceased_last_name,
                        deceased_sex, date_of_birth, date_of_death, time_of_death, place_of_death, residence, religion, civil_status,
                        nationality, relationship, informant_name, informant_contact, informant_email, informant_address,
                        request_type, death_cert_requested, death_cert_status,
                        cemetery_plot_requested, cemetery_plot_status, cemetery_preference,
                        additional_notes, supporting_document, status,
                        cert_action_type, existing_cert_no, copy_reason, num_copies, existing_record_verified,
                        submitted_at
                    ) VALUES (
                        :ref_id, :tenant_id, :deceased_id, :encoded_cert_id, :deceased_name, :deceased_first_name, :deceased_middle_name, :deceased_last_name,
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
                'encoded_cert_id' => $encodedCertId,
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

            if ($encodedCertId) {
                try {
                    $this->db->prepare("UPDATE encoded_death_certificates SET burial_request_id = :bid WHERE id = :eid")->execute([
                        'bid' => $burialId,
                        'eid' => $encodedCertId
                    ]);
                    $this->logActivity(
                        $burialId,
                        $refId,
                        'encoded_link',
                        'Linked to Encoded Death Certificate',
                        "Automatically recognized and linked to Encoded Death Certificate #{$existingCertNo} (Deceased: {$fullName})",
                        'System',
                        'System'
                    );
                } catch (\Throwable $e) {}
            }

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
                           p.cemetery_name AS plot_cemetery, p.section AS plot_section, p.plot_number AS plot_num, p.status AS plot_current_status,
                           enc.id AS encoded_cert_id,
                           enc.registration_number AS encoded_reg_no,
                           enc.deceased_name AS encoded_deceased_name,
                           enc.representative_name AS encoded_representative_name,
                           enc.encoded_by_name AS encoded_by_name,
                           enc.encoded_at AS encoded_at
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    LEFT JOIN deceased_records d ON (b.deceased_id = d.id OR (b.deceased_id IS NULL AND b.id = d.burial_request_id))
                    LEFT JOIN cemetery_plots p ON b.cemetery_plot_id = p.id
                    LEFT JOIN encoded_death_certificates enc ON (
                        b.encoded_cert_id = enc.id 
                        OR (b.existing_cert_no IS NOT NULL AND b.existing_cert_no != '' AND (
                            enc.registration_number COLLATE utf8mb4_general_ci = b.existing_cert_no COLLATE utf8mb4_general_ci
                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(enc.registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(b.existing_cert_no)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci
                        ))
                        OR (b.death_cert_registry_no IS NOT NULL AND b.death_cert_registry_no != '' AND (
                            enc.registration_number COLLATE utf8mb4_general_ci = b.death_cert_registry_no COLLATE utf8mb4_general_ci
                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(enc.registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(b.death_cert_registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci
                        ))
                    )
                    WHERE b.ref_id = :ref_id
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ref_id' => $refId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            if (!empty($row['encoded_cert_id'])) {
                $row['is_encoded_linked'] = true;
                $row['existing_record_verified'] = 1;
                // If persistent link in burial_requests wasn't set yet, persist it
                try {
                    $this->db->prepare("UPDATE burial_requests 
                                        SET encoded_cert_id = :eid, existing_record_verified = 1 
                                        WHERE id = :bid AND (encoded_cert_id IS NULL OR existing_record_verified = 0)")
                             ->execute(['eid' => $row['encoded_cert_id'], 'bid' => $row['id']]);
                    $this->db->prepare("UPDATE encoded_death_certificates 
                                        SET burial_request_id = :bid 
                                        WHERE id = :eid AND burial_request_id IS NULL")
                             ->execute(['bid' => $row['id'], 'eid' => $row['encoded_cert_id']]);
                } catch (\Throwable $e) {}
            } else {
                $row['is_encoded_linked'] = false;
            }

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
                           p.cemetery_name AS plot_cemetery, p.section AS plot_section, p.plot_number AS plot_num,
                           enc.id AS encoded_cert_id,
                           enc.registration_number AS encoded_reg_no,
                           enc.deceased_name AS encoded_deceased_name,
                           enc.representative_name AS encoded_representative_name
                    FROM burial_requests b
                    LEFT JOIN cemetery_plots p ON b.cemetery_plot_id = p.id
                    LEFT JOIN encoded_death_certificates enc ON (
                        b.encoded_cert_id = enc.id 
                        OR (b.existing_cert_no IS NOT NULL AND b.existing_cert_no != '' AND (
                            enc.registration_number COLLATE utf8mb4_general_ci = b.existing_cert_no COLLATE utf8mb4_general_ci
                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(enc.registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(b.existing_cert_no)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci
                        ))
                        OR (b.death_cert_registry_no IS NOT NULL AND b.death_cert_registry_no != '' AND (
                            enc.registration_number COLLATE utf8mb4_general_ci = b.death_cert_registry_no COLLATE utf8mb4_general_ci
                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(enc.registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(b.death_cert_registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci
                        ))
                    )
                    WHERE b.tenant_id = :tid 
                    ORDER BY b.submitted_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['tid' => $tenantId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($rows as &$r) {
                $r['is_encoded_linked'] = (!empty($r['encoded_cert_id']) || !empty($r['encoded_reg_no']));
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Get all requests for Staff Admin dashboard with filter & search
     */
    public function getAll(string $filter = 'all', string $search = '', bool $archivedOnly = false): array {
        try {
            $params = [];
            $sql = "SELECT b.*, 
                           t.first_name, t.last_name, t.email, t.contactnum,
                           p.cemetery_name AS plot_cemetery, p.section AS plot_section, p.plot_number AS plot_num,
                           enc.id AS encoded_cert_id,
                           enc.registration_number AS encoded_reg_no,
                           enc.deceased_name AS encoded_deceased_name,
                           enc.representative_name AS encoded_representative_name,
                           enc.encoded_by_name AS encoded_by_name,
                           enc.encoded_at AS encoded_at
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    LEFT JOIN cemetery_plots p ON b.cemetery_plot_id = p.id
                    LEFT JOIN encoded_death_certificates enc ON (
                        b.encoded_cert_id = enc.id 
                        OR (b.existing_cert_no IS NOT NULL AND b.existing_cert_no != '' AND (
                            enc.registration_number COLLATE utf8mb4_general_ci = b.existing_cert_no COLLATE utf8mb4_general_ci
                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(enc.registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(b.existing_cert_no)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci
                        ))
                        OR (b.death_cert_registry_no IS NOT NULL AND b.death_cert_registry_no != '' AND (
                            enc.registration_number COLLATE utf8mb4_general_ci = b.death_cert_registry_no COLLATE utf8mb4_general_ci
                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(enc.registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(b.death_cert_registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') COLLATE utf8mb4_general_ci
                        ))
                    )
                    WHERE 1=1";

            if ($archivedOnly) {
                $sql .= " AND b.is_archived = 1";
            } else {
                $sql .= " AND (b.is_archived = 0 OR b.is_archived IS NULL)";
            }

            $filter = strtolower(trim($filter));
            if ($filter === 'pending') {
                $sql .= " AND (b.status NOT IN ('Accepted', 'Rejected', 'Completed') AND b.status NOT LIKE 'Accept%' AND COALESCE(b.death_cert_status, '') NOT IN ('Rejected', 'Awaiting Authorized Certificate Preparation', 'Registered', 'Certified Copy Requested or Received'))";
            } elseif ($filter === 'accepted') {
                $sql .= " AND (b.status IN ('Accepted', 'Completed') OR b.status LIKE 'Accept%' OR COALESCE(b.death_cert_status, '') IN ('Awaiting Authorized Certificate Preparation', 'Registered', 'Certified Copy Requested or Received'))";
            } elseif ($filter === 'rejected') {
                $sql .= " AND (b.status = 'Rejected' OR COALESCE(b.death_cert_status, '') = 'Rejected')";
            } elseif ($filter === 'additional_info') {
                $sql .= " AND (b.death_cert_status = 'Additional Information Required' OR b.cemetery_plot_status = 'Additional Information Required')";
            } elseif ($filter === 'death_cert') {
                $sql .= " AND b.death_cert_requested = 1";
            }

            if (!empty($search)) {
                $sql .= " AND (b.ref_id LIKE :search OR b.deceased_name LIKE :search OR b.informant_name LIKE :search OR t.first_name LIKE :search OR t.last_name LIKE :search)";
                $params['search'] = "%$search%";
            }

            $sql .= " ORDER BY b.submitted_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($rows as &$r) {
                $r['is_encoded_linked'] = (!empty($r['encoded_cert_id']) || !empty($r['encoded_reg_no']));
            }
            return $rows;
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
            
            // Pending Review (unprocessed)
            $pendingCases = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE status NOT IN ('Accepted', 'Rejected', 'Completed') AND status NOT LIKE 'Accept%' AND COALESCE(death_cert_status, '') NOT IN ('Rejected', 'Awaiting Authorized Certificate Preparation', 'Registered', 'Certified Copy Requested or Received')")->fetchColumn();

            // Accepted Requests
            $acceptedCases = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE status IN ('Accepted', 'Completed') OR status LIKE 'Accept%' OR COALESCE(death_cert_status, '') IN ('Awaiting Authorized Certificate Preparation', 'Registered', 'Certified Copy Requested or Received')")->fetchColumn();

            // Rejected Requests
            $rejectedCases = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE status = 'Rejected' OR COALESCE(death_cert_status, '') = 'Rejected'")->fetchColumn();

            // Completed Cases (retained for backward compatibility)
            $completedCases = $acceptedCases;

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
                'pending' => $pendingCases,
                'accepted' => $acceptedCases,
                'rejected' => $rejectedCases,
                'completed' => $completedCases,
                'death_cert_pending' => $dcPending,
                'death_cert_info_req' => $dcInfoReq,
                'death_cert_progress' => $acceptedCases,
                'death_cert_in_progress' => $acceptedCases,
                'death_cert_registered' => $dcRegistered,
                'plot_pending' => $plotPending,
                'plot_approved' => $plotApproved,
                'plot_assigned' => $plotAssigned,
                'available_plots' => $availPlots,
                'occupied_plots' => $occupiedPlots,
                'total_plots' => $totalPlots
            ];
        } catch (\PDOException $e) {
            return [
                'total' => 0, 'pending' => 0, 'accepted' => 0, 'rejected' => 0, 'completed' => 0,
                'death_cert_pending' => 0, 'death_cert_info_req' => 0,
                'death_cert_progress' => 0, 'death_cert_in_progress' => 0, 'death_cert_registered' => 0,
                'plot_pending' => 0, 'plot_approved' => 0, 'plot_assigned' => 0,
                'available_plots' => 0, 'occupied_plots' => 0, 'total_plots' => 0
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

    /**
     * Record verification result and remarks for Death Certificate
     */
    public function recordVerification(int|string $burialId, string $result, string $remarks = '', string $staffName = 'Damayan Staff'): array {
        try {
            $burial = is_numeric($burialId) ? $this->findById((int)$burialId) : $this->findByRefId((string)$burialId);
            if (!$burial && !is_numeric($burialId)) {
                $stmt = $this->db->prepare("SELECT id FROM burial_requests WHERE ref_id = :ref LIMIT 1");
                $stmt->execute(['ref' => $burialId]);
                $foundId = $stmt->fetchColumn();
                if ($foundId) $burial = $this->findById((int)$foundId);
            }
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            $validResults = ['Matched', 'Mismatch', 'Unable to Verify'];
            if (!in_array($result, $validResults)) {
                return ['success' => false, 'error' => 'Invalid verification result'];
            }

            if ($result !== 'Matched' && empty(trim($remarks))) {
                return ['success' => false, 'error' => 'Remarks are required when recording a Mismatch or Unable to Verify.'];
            }

            $bId = (int)$burial['id'];
            $refId = $burial['ref_id'];

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET verification_result = :res, 
                                            verification_remarks = :rem, 
                                            verified_by = :vby, 
                                            verified_at = NOW() 
                                        WHERE id = :id");
            $ok = $stmt->execute([
                'res' => $result,
                'rem' => trim($remarks),
                'vby' => $staffName,
                'id' => $bId
            ]);

            if ($ok) {
                $this->logActivity(
                    $bId,
                    $refId,
                    'Certificate Verification',
                    "Verification Recorded: {$result}",
                    "Result: {$result}." . (!empty($remarks) ? " Remarks: {$remarks}" : ""),
                    $staffName,
                    'Staff_Damayan'
                );
                return [
                    'success' => true,
                    'verification_result' => $result,
                    'verification_remarks' => trim($remarks),
                    'verified_by' => $staffName,
                    'verified_at' => date('Y-m-d H:i:s')
                ];
            }
            return ['success' => false, 'error' => 'Failed to save verification result.'];
        } catch (\PDOException $e) {
            error_log("recordVerification error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Record duplicate review decision
     */
    public function reviewDuplicate(int|string $burialId, string $decision, string $notes = '', string $staffName = 'Damayan Staff'): array {
        try {
            $burial = is_numeric($burialId) ? $this->findById((int)$burialId) : $this->findByRefId((string)$burialId);
            if (!$burial && !is_numeric($burialId)) {
                $stmt = $this->db->prepare("SELECT id FROM burial_requests WHERE ref_id = :ref LIMIT 1");
                $stmt->execute(['ref' => $burialId]);
                $foundId = $stmt->fetchColumn();
                if ($foundId) $burial = $this->findById((int)$foundId);
            }
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            $bId = (int)$burial['id'];
            $refId = $burial['ref_id'];

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET duplicate_review_decision = :dec, 
                                            duplicate_reviewed_by = :rby, 
                                            duplicate_reviewed_at = NOW(),
                                            duplicate_review_notes = :notes
                                        WHERE id = :id");
            $ok = $stmt->execute([
                'dec' => $decision,
                'rby' => $staffName,
                'notes' => trim($notes),
                'id' => $bId
            ]);

            if ($ok) {
                $this->logActivity(
                    $bId,
                    $refId,
                    'Duplicate Review',
                    "Duplicate Review: {$decision}",
                    "Decision: {$decision}." . (!empty($notes) ? " Notes: {$notes}" : ""),
                    $staffName,
                    'Staff_Damayan'
                );
                return ['success' => true, 'decision' => $decision];
            }
            return ['success' => false, 'error' => 'Failed to record duplicate review.'];
        } catch (\PDOException $e) {
            error_log("reviewDuplicate error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Accept & Approve death certificate request for processing
     */
    public function acceptRequest(int|string $burialId, int $staffId = 0, string $staffName = 'Damayan Coordinator', string $notes = '', bool $skipVerification = true): array {
        try {
            $burial = is_numeric($burialId) ? $this->findById((int)$burialId) : $this->findByRefId((string)$burialId);
            if (!$burial && !is_numeric($burialId)) {
                $stmt = $this->db->prepare("SELECT id FROM burial_requests WHERE ref_id = :ref LIMIT 1");
                $stmt->execute(['ref' => $burialId]);
                $foundId = $stmt->fetchColumn();
                if ($foundId) $burial = $this->findById((int)$foundId);
            }
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            $bId = (int)$burial['id'];
            $refId = $burial['ref_id'];
            $tenantId = (int)$burial['tenant_id'];

            $newStatus = 'Accepted';
            $dcStatus = 'Awaiting Authorized Certificate Preparation';

            $sql = "UPDATE burial_requests 
                    SET status = :st, 
                        death_cert_status = :dc_st,
                        verification_result = 'Matched',
                        cemetery_plot_status = IF(cemetery_plot_requested = 1 AND cemetery_plot_status = 'Pending Review', 'Available for Assignment', cemetery_plot_status),
                        rejection_reason = NULL,
                        death_cert_rejection_reason = NULL,
                        cemetery_plot_rejection_reason = NULL,
                        rejection_reason_category = NULL,
                        rejection_specific_reason = NULL";
            $params = [
                'st' => $newStatus,
                'dc_st' => $dcStatus,
                'id' => $bId
            ];

            if ($staffId > 0) {
                $sql .= ", assigned_staff_id = :sid, assigned_staff_name = :sname";
                $params['sid'] = $staffId;
                $params['sname'] = $staffName;
            }

            if (!empty($notes)) {
                $sql .= ", additional_notes = CONCAT(COALESCE(additional_notes, ''), '\n[Accepted]: ', :notes)";
                $params['notes'] = $notes;
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute($params);

            if ($res) {
                $this->logActivity(
                    $bId, 
                    $refId, 
                    'Status Update', 
                    'Request Accepted', 
                    "Request #{$refId} was approved and accepted for processing by {$staffName} after successful certificate verification." . (!empty($notes) ? " Remarks: {$notes}" : ""),
                    $staffName, 
                    'Staff_Damayan'
                );

                // Notify User/Applicant
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        $tenantId,
                        "Request Accepted: #{$refId}",
                        "Assalamu Alaikum. Your request (#{$refId}) has been verified and accepted for processing by Damayan staff.",
                        'damayan'
                    );
                } catch (\Throwable $ne) {}

                return ['success' => true];
            }

            return ['success' => false, 'error' => 'Database update failed'];
        } catch (\PDOException $e) {
            error_log("BurialRequest::acceptRequest error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Reject request with mandatory reason dropdown and optional explanation
     */
    public function rejectRequest(int|string $burialId, string $reasonCategory, string $specificReason = '', string $staffName = 'Damayan Staff'): array {
        try {
            $burial = is_numeric($burialId) ? $this->findById((int)$burialId) : $this->findByRefId((string)$burialId);
            if (!$burial && !is_numeric($burialId)) {
                $stmt = $this->db->prepare("SELECT id FROM burial_requests WHERE ref_id = :ref LIMIT 1");
                $stmt->execute(['ref' => $burialId]);
                $foundId = $stmt->fetchColumn();
                if ($foundId) $burial = $this->findById((int)$foundId);
            }
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            $reasonCategory = trim($reasonCategory);
            $specificReason = trim($specificReason);

            if (empty($reasonCategory)) {
                return ['success' => false, 'error' => 'Please select a rejection reason.'];
            }

            if ($reasonCategory === 'Others' && empty($specificReason)) {
                return ['success' => false, 'error' => 'Please specify the rejection reason when selecting Others.'];
            }

            $fullReason = ($reasonCategory === 'Others') ? ("Others: " . $specificReason) : $reasonCategory;

            $bId = (int)$burial['id'];
            $refId = $burial['ref_id'];
            $tenantId = (int)$burial['tenant_id'];

            $stmt = $this->db->prepare("
                UPDATE burial_requests 
                SET status = 'Rejected',
                    death_cert_status = 'Rejected',
                    cemetery_plot_status = 'Rejected',
                    rejection_reason = :r1,
                    death_cert_rejection_reason = :r2,
                    cemetery_plot_rejection_reason = :r3,
                    rejection_reason_category = :rcat,
                    rejection_specific_reason = :rspec,
                    rejected_by = :rejby,
                    rejected_at = NOW()
                WHERE id = :id
            ");
            $res = $stmt->execute([
                'r1' => $fullReason,
                'r2' => $fullReason,
                'r3' => $fullReason,
                'rcat' => $reasonCategory,
                'rspec' => $specificReason,
                'rejby' => $staffName,
                'id' => $bId
            ]);

            if ($res) {
                $this->logActivity(
                    $bId, 
                    $refId, 
                    'Rejection', 
                    'Request Rejected', 
                    "Request #{$refId} was rejected by {$staffName}. Reason: {$fullReason}", 
                    $staffName, 
                    'Staff_Damayan'
                );

                // Notify User/Applicant
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        $tenantId,
                        "Request Update: #{$refId} Rejected",
                        "Your request (#{$refId}) was rejected. Reason: {$fullReason}. Please contact the Damayan office if you have questions.",
                        'damayan'
                    );
                } catch (\Throwable $ne) {}

                return ['success' => true];
            }

            return ['success' => false, 'error' => 'Database update failed'];
        } catch (\PDOException $e) {
            error_log("BurialRequest::rejectRequest error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
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
            $stmt = $this->db->query("SELECT tenant_id, tenant_id AS id, first_name, last_name, CONCAT(first_name, ' ', last_name) AS name, email, role 
                                      FROM tenant_accounts 
                                      WHERE role IN ('Staff_Damayan', 'Admin') 
                                      ORDER BY (role = 'Staff_Damayan') DESC, first_name ASC");
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
     * Searches both registered deceased records AND encoded death certificates
     */
    public function searchDeceasedRecords(string $query = ''): array {
        try {
            $query = ltrim(trim($query), '# ');
            $results = [];

            // 1. Search encoded_death_certificates
            if (empty($query)) {
                $encStmt = $this->db->query("SELECT id, registration_number AS death_cert_registry_no, registration_number,
                                                    deceased_name, representative_name, encoded_by_name, encoded_at,
                                                    'encoded_death_certificates' AS source, 1 AS is_encoded,
                                                    DATE(encoded_at) AS date_of_death
                                             FROM encoded_death_certificates 
                                             ORDER BY encoded_at DESC LIMIT 25");
                $encRecords = $encStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } else {
                $encStmt = $this->db->prepare("SELECT id, registration_number AS death_cert_registry_no, registration_number,
                                                      deceased_name, representative_name, encoded_by_name, encoded_at,
                                                      'encoded_death_certificates' AS source, 1 AS is_encoded,
                                                      DATE(encoded_at) AS date_of_death
                                               FROM encoded_death_certificates 
                                               WHERE registration_number LIKE :q1 
                                                  OR deceased_name LIKE :q2 
                                                  OR representative_name LIKE :q3 
                                               ORDER BY encoded_at DESC LIMIT 25");
                $searchTerm = "%$query%";
                $encStmt->execute([
                    'q1' => $searchTerm,
                    'q2' => $searchTerm,
                    'q3' => $searchTerm
                ]);
                $encRecords = $encStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            foreach ($encRecords as $er) {
                $results[] = $er;
            }

            // 2. Search deceased_records
            if (empty($query)) {
                $stmt = $this->db->query("SELECT d.*, d.full_name AS deceased_name, b.ref_id, b.death_cert_registry_no,
                                                 'deceased_records' AS source, 0 AS is_encoded
                                          FROM deceased_records d
                                          LEFT JOIN burial_requests b ON (d.id = b.deceased_id OR d.burial_request_id = b.id)
                                          ORDER BY d.created_at DESC LIMIT 25");
                $decRecords = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } else {
                $stmt = $this->db->prepare("SELECT d.*, d.full_name AS deceased_name, b.ref_id, b.death_cert_registry_no,
                                                   'deceased_records' AS source, 0 AS is_encoded
                                            FROM deceased_records d
                                            LEFT JOIN burial_requests b ON (d.id = b.deceased_id OR d.burial_request_id = b.id)
                                            WHERE d.full_name LIKE :q1 
                                               OR d.first_name LIKE :q2 
                                               OR d.last_name LIKE :q3 
                                               OR d.ref_id LIKE :q4 
                                               OR b.death_cert_registry_no LIKE :q5
                                            ORDER BY d.created_at DESC LIMIT 25");
                $searchTerm = "%$query%";
                $stmt->execute([
                    'q1' => $searchTerm,
                    'q2' => $searchTerm,
                    'q3' => $searchTerm,
                    'q4' => $searchTerm,
                    'q5' => $searchTerm
                ]);
                $decRecords = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            foreach ($decRecords as $dr) {
                $results[] = $dr;
            }

            return $results;
        } catch (\PDOException $e) {
            error_log("searchDeceasedRecords error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verify and link existing death certificate record for copy request
     * Supports both registered deceased records and encoded death certificates
     */
    public function verifyExistingRecord(int $burialId, ?int $recordId = null, string $staffName = 'Staff Admin', string $source = 'deceased_records'): array {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return ['success' => false, 'error' => 'Case not found'];

            if ($source === 'encoded_death_certificates' || $source === 'encoded') {
                $eid = $recordId ?: (!empty($burial['encoded_cert_id']) ? (int)$burial['encoded_cert_id'] : null);
                if (!$eid && !empty($burial['existing_cert_no'])) {
                    $foundEnc = $this->findEncodedByRegistrationNo($burial['existing_cert_no']);
                    if ($foundEnc) $eid = (int)$foundEnc['id'];
                }

                if (!$eid) {
                    return ['success' => false, 'error' => 'Encoded certificate record not found'];
                }

                $stmt = $this->db->prepare("SELECT * FROM encoded_death_certificates WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $eid]);
                $enc = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$enc) return ['success' => false, 'error' => 'Encoded death certificate not found'];

                $updateSql = "UPDATE burial_requests 
                              SET encoded_cert_id = :eid,
                                  existing_cert_no = COALESCE(NULLIF(existing_cert_no, ''), :reg_no),
                                  existing_record_verified = 1,
                                  deceased_name = COALESCE(NULLIF(deceased_name, ''), :dname),
                                  death_cert_status = IF(death_cert_status = 'Request Received', 'Under Review', death_cert_status),
                                  updated_at = NOW()
                              WHERE id = :id";
                $this->db->prepare($updateSql)->execute([
                    'eid' => $enc['id'],
                    'reg_no' => $enc['registration_number'],
                    'dname' => $enc['deceased_name'],
                    'id' => $burialId
                ]);

                $this->db->prepare("UPDATE encoded_death_certificates SET burial_request_id = :bid WHERE id = :eid")->execute([
                    'bid' => $burialId,
                    'eid' => $enc['id']
                ]);

                $this->logActivity(
                    $burialId,
                    $burial['ref_id'],
                    'record_verified',
                    'Linked to Encoded Death Certificate',
                    "Verified and linked to Encoded Death Certificate #{$enc['registration_number']} (Deceased: {$enc['deceased_name']}, Representative: {$enc['representative_name']}) by {$staffName}",
                    $staffName,
                    'Staff_Damayan'
                );

                return [
                    'success' => true,
                    'message' => "Successfully linked to Encoded Death Certificate #{$enc['registration_number']}",
                    'is_encoded' => true,
                    'encoded' => $enc
                ];
            }

            // Otherwise, link to registered deceased_records
            $deceasedId = $recordId ?: (!empty($burial['deceased_id']) ? (int)$burial['deceased_id'] : null);
            $decRecord = null;
            if ($deceasedId) {
                $decStmt = $this->db->prepare("SELECT * FROM deceased_records WHERE id = :did LIMIT 1");
                $decStmt->execute(['did' => $deceasedId]);
                $decRecord = $decStmt->fetch(PDO::FETCH_ASSOC);
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

    /**
     * Look up an encoded death certificate by registration number
     */
    public function findEncodedByRegistrationNo(string $regNo): ?array {
        $regNo = trim($regNo);
        if (empty($regNo)) return null;
        $norm = self::normalizeRegNo($regNo);
        try {
            $stmt = $this->db->prepare("SELECT * FROM encoded_death_certificates 
                WHERE LOWER(TRIM(registration_number)) = LOWER(TRIM(:reg))
                   OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm
                ORDER BY (LOWER(TRIM(registration_number)) = LOWER(TRIM(:reg))) DESC
                LIMIT 1");
            $stmt->execute(['reg' => $regNo, 'norm' => $norm]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Check if a Death Certificate Registration Number is a duplicate across:
     * 1. encoded_death_certificates
     * 2. burial_requests (official civil registrations: death_cert_registry_no)
     * 3. death_records (registry_no)
     */
    public function isDuplicateDeathCertRegNo(string $regNo, ?int $excludeEncodedId = null): ?array {
        $regNo = trim($regNo);
        if (empty($regNo)) return null;
        $norm = self::normalizeRegNo($regNo);

        try {
            // Check 1: encoded_death_certificates
            $sql1 = "SELECT id, registration_number, deceased_name, representative_name, encoded_by_name, encoded_at 
                     FROM encoded_death_certificates 
                     WHERE (LOWER(registration_number) = LOWER(:reg)
                        OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(registration_number)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm)";
            if ($excludeEncodedId) {
                $sql1 .= " AND id != " . (int)$excludeEncodedId;
            }
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute(['reg' => $regNo, 'norm' => $norm]);
            $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            if ($row1) {
                return [
                    'is_duplicate' => true,
                    'source' => 'Encoded Certificates Archive',
                    'registration_number' => $row1['registration_number'],
                    'deceased_name' => $row1['deceased_name'],
                    'representative_name' => $row1['representative_name'],
                    'encoded_by' => $row1['encoded_by_name'],
                    'date' => $row1['encoded_at']
                ];
            }

            // Check 2: burial_requests (official completed registrations only)
            $stmt2 = $this->db->prepare("SELECT id, ref_id, death_cert_registry_no, deceased_name, informant_name, submitted_at 
                                         FROM burial_requests 
                                         WHERE (LOWER(death_cert_registry_no) = LOWER(:reg)
                                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(death_cert_registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm) 
                                         LIMIT 1");
            $stmt2->execute(['reg' => $regNo, 'norm' => $norm]);
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($row2) {
                return [
                    'is_duplicate' => true,
                    'source' => 'Official Registered Records (# ' . $row2['ref_id'] . ')',
                    'registration_number' => $row2['death_cert_registry_no'],
                    'deceased_name' => $row2['deceased_name'],
                    'representative_name' => $row2['informant_name'],
                    'date' => $row2['submitted_at']
                ];
            }

            // Check 3: death_records
            $stmt3 = $this->db->prepare("SELECT deceased_id, registry_no, CONCAT_WS(' ', dfirst_name, dmiddle_name, last_name) as deceased_name 
                                         FROM death_records 
                                         WHERE (LOWER(registry_no) = LOWER(:reg)
                                            OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm) 
                                         LIMIT 1");
            $stmt3->execute(['reg' => $regNo, 'norm' => $norm]);
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            if ($row3) {
                return [
                    'is_duplicate' => true,
                    'source' => 'Civil Registry Records',
                    'registration_number' => $row3['registry_no'],
                    'deceased_name' => trim($row3['deceased_name']),
                    'representative_name' => 'N/A'
                ];
            }

            return null;
        } catch (\PDOException $e) {
            error_log("isDuplicateDeathCertRegNo error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Compare deceased's name and registration number against existing requests and records
     * Returns matching records for staff review
     */
    public function checkPotentialDuplicates(array|int|string $burial): array {
        if (!is_array($burial)) {
            $burial = is_numeric($burial) ? $this->findById((int)$burial) : $this->findByRefId((string)$burial);
        }
        if (!$burial) return [];

        $id = (int)$burial['id'];
        $deceasedName = trim($burial['deceased_name'] ?? '');
        $regNo = trim($burial['death_cert_registry_no'] ?? ($burial['existing_cert_no'] ?? ''));

        $duplicates = [];

        // 1. Check burial_requests for matching registration number (if non-empty)
        if (!empty($regNo)) {
            $stmt = $this->db->prepare("
                SELECT id, ref_id, deceased_name, status, death_cert_status, death_cert_registry_no, existing_cert_no, submitted_at
                FROM burial_requests
                WHERE id != :id 
                  AND (
                    (death_cert_registry_no IS NOT NULL AND death_cert_registry_no != '' AND LOWER(death_cert_registry_no) = LOWER(:reg1))
                    OR (existing_cert_no IS NOT NULL AND existing_cert_no != '' AND LOWER(existing_cert_no) = LOWER(:reg2))
                  )
                LIMIT 5
            ");
            $stmt->execute(['id' => $id, 'reg1' => $regNo, 'reg2' => $regNo]);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $duplicates[] = [
                    'match_type' => 'Exact Registration Number Match',
                    'source' => 'Burial Request #' . $row['ref_id'],
                    'ref_id' => $row['ref_id'],
                    'deceased_name' => $row['deceased_name'],
                    'reg_no' => $row['death_cert_registry_no'] ?: $row['existing_cert_no'],
                    'date' => $row['submitted_at'],
                    'status' => $row['status']
                ];
            }
        }

        // 2. Check burial_requests for matching deceased name (case-insensitive)
        if (mb_strlen($deceasedName) >= 3) {
            $stmt = $this->db->prepare("
                SELECT id, ref_id, deceased_name, status, death_cert_status, death_cert_registry_no, existing_cert_no, submitted_at
                FROM burial_requests
                WHERE id != :id AND LOWER(TRIM(deceased_name)) = LOWER(TRIM(:dname))
                LIMIT 5
            ");
            $stmt->execute(['id' => $id, 'dname' => $deceasedName]);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $alreadyAdded = false;
                foreach ($duplicates as $d) {
                    if (($d['ref_id'] ?? '') === $row['ref_id']) {
                        $alreadyAdded = true;
                        break;
                    }
                }
                if (!$alreadyAdded) {
                    $duplicates[] = [
                        'match_type' => 'Deceased Name Match',
                        'source' => 'Burial Request #' . $row['ref_id'],
                        'ref_id' => $row['ref_id'],
                        'deceased_name' => $row['deceased_name'],
                        'reg_no' => $row['death_cert_registry_no'] ?: ($row['existing_cert_no'] ?: 'N/A'),
                        'date' => $row['submitted_at'],
                        'status' => $row['status']
                    ];
                }
            }
        }

        // 3. Check encoded_death_certificates
        try {
            $encStmt = $this->db->prepare("
                SELECT id, registration_number, deceased_name, representative_name, encoded_at
                FROM encoded_death_certificates
                WHERE (
                    (:has_reg = 1 AND LOWER(registration_number) = LOWER(:reg))
                    OR (LOWER(TRIM(deceased_name)) = LOWER(TRIM(:dname)))
                )
                LIMIT 5
            ");
            $encStmt->execute([
                'has_reg' => !empty($regNo) ? 1 : 0,
                'reg' => $regNo,
                'dname' => $deceasedName
            ]);
            while ($row = $encStmt->fetch(\PDO::FETCH_ASSOC)) {
                $duplicates[] = [
                    'match_type' => (!empty($regNo) && strtolower($row['registration_number']) === strtolower($regNo)) ? 'Registration Number Match' : 'Deceased Name Match',
                    'source' => 'Encoded Certificates Archive',
                    'ref_id' => 'ENC-' . $row['id'],
                    'deceased_name' => $row['deceased_name'],
                    'reg_no' => $row['registration_number'],
                    'date' => $row['encoded_at'],
                    'status' => 'Archived/Encoded'
                ];
            }
        } catch (\PDOException $e) {}

        // 4. Check death_records if table exists
        try {
            $drStmt = $this->db->prepare("
                SELECT deceased_id, registry_no, CONCAT_WS(' ', dfirst_name, dmiddle_name, last_name) as deceased_name, date_of_death
                FROM death_records
                WHERE (
                    (:has_reg = 1 AND LOWER(registry_no) = LOWER(:reg))
                    OR (LOWER(TRIM(CONCAT_WS(' ', dfirst_name, dmiddle_name, last_name))) = LOWER(TRIM(:dname)))
                )
                LIMIT 5
            ");
            $drStmt->execute([
                'has_reg' => !empty($regNo) ? 1 : 0,
                'reg' => $regNo,
                'dname' => $deceasedName
            ]);
            while ($row = $drStmt->fetch(\PDO::FETCH_ASSOC)) {
                $duplicates[] = [
                    'match_type' => (!empty($regNo) && strtolower($row['registry_no']) === strtolower($regNo)) ? 'Registration Number Match' : 'Deceased Name Match',
                    'source' => 'Civil Registry Death Records',
                    'ref_id' => 'REC-' . $row['deceased_id'],
                    'deceased_name' => trim($row['deceased_name']),
                    'reg_no' => $row['registry_no'],
                    'date' => $row['date_of_death'] ?? 'N/A',
                    'status' => 'Civil Record'
                ];
            }
        } catch (\PDOException $e) {}

        return $duplicates;
    }


    /**
     * Encode an existing death certificate (Registration #, Deceased Name, Representative Name)
     * Automatically links any burial request having the same registration number!
     */
    public function encodeDeathCertificate(array $data, int $staffId, string $staffName): array {
        $regNo = trim($data['registration_number'] ?? ($data['registry_number'] ?? ''));
        $firstName = trim($data['first_name'] ?? '');
        $middleName = trim($data['middle_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $deceasedName = trim($data['deceased_name'] ?? '');
        if (empty($deceasedName) && (!empty($firstName) || !empty($lastName))) {
            $deceasedName = trim($firstName . ' ' . ($middleName ? $middleName . ' ' : '') . $lastName);
        }
        $representativeName = trim($data['representative_name'] ?? '');

        if (empty($regNo)) {
            return ['success' => false, 'error' => 'Registry Number is required.', 'message' => 'Registry Number is required.'];
        }
        if (empty($deceasedName)) {
            return ['success' => false, 'error' => 'Full Name of the Deceased (First and Last Name) is required.', 'message' => 'Full Name of the Deceased (First and Last Name) is required.'];
        }

        // Check for duplicate registration number
        $dup = $this->isDuplicateDeathCertRegNo($regNo);
        if ($dup) {
            $msg = "Registration number '{$regNo}' already exists in {$dup['source']} (Deceased: {$dup['deceased_name']}). Duplicate records are prevented.";
            return [
                'success' => false,
                'error' => $msg,
                'message' => $msg
            ];
        }

        // Additional registration and deceased details
        $province = trim($data['province'] ?? '');
        $cityMunicipality = trim($data['city_municipality'] ?? '');
        $sex = in_array(trim($data['sex'] ?? ''), ['Male', 'Female']) ? trim($data['sex']) : 'Male';

        $dodDay = !empty($data['date_of_death_day']) ? (int)$data['date_of_death_day'] : null;
        $dodMonth = !empty($data['date_of_death_month']) ? (int)$data['date_of_death_month'] : null;
        $dodYear = !empty($data['date_of_death_year']) ? (int)$data['date_of_death_year'] : null;
        $dateOfDeath = null;
        if ($dodYear && $dodMonth && $dodDay) {
            $dateOfDeath = sprintf('%04d-%02d-%02d', $dodYear, $dodMonth, $dodDay);
        } elseif (!empty($data['date_of_death'])) {
            $dateOfDeath = date('Y-m-d', strtotime($data['date_of_death']));
        }

        $timeOfDeath = null;
        if (!empty($data['time_of_death'])) {
            $timeOfDeath = date('H:i:s', strtotime($data['time_of_death']));
        }

        $dobDay = !empty($data['date_of_birth_day']) ? (int)$data['date_of_birth_day'] : null;
        $dobMonth = !empty($data['date_of_birth_month']) ? (int)$data['date_of_birth_month'] : null;
        $dobYear = !empty($data['date_of_birth_year']) ? (int)$data['date_of_birth_year'] : null;
        $dateOfBirth = null;
        if ($dobYear && $dobMonth && $dobDay) {
            $dateOfBirth = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);
        } elseif (!empty($data['date_of_birth'])) {
            $dateOfBirth = date('Y-m-d', strtotime($data['date_of_birth']));
        }

        $ageYears = isset($data['age_years']) && $data['age_years'] !== '' ? (int)$data['age_years'] : null;
        $ageMonths = isset($data['age_months']) && $data['age_months'] !== '' ? (int)$data['age_months'] : null;
        $ageDays = isset($data['age_days']) && $data['age_days'] !== '' ? (int)$data['age_days'] : null;
        $ageHours = isset($data['age_hours']) && $data['age_hours'] !== '' ? (int)$data['age_hours'] : null;
        $ageUnit = trim($data['age_unit'] ?? 'years');
        $ageDisplay = trim($data['age_display'] ?? '');
        if (empty($ageDisplay)) {
            if ($ageYears !== null && $ageYears > 0) $ageDisplay = "{$ageYears} yrs";
            elseif ($ageMonths !== null && $ageMonths > 0) $ageDisplay = "{$ageMonths} mos";
            elseif ($ageDays !== null && $ageDays > 0) $ageDisplay = "{$ageDays} days";
            elseif ($ageHours !== null && $ageHours > 0) $ageDisplay = "{$ageHours} hrs";
        }

        $podHospital = trim($data['place_of_death_hospital'] ?? '');
        $podHouseNo = trim($data['place_of_death_house_no'] ?? '');
        $podStreet = trim($data['place_of_death_street'] ?? '');
        $podBarangay = trim($data['place_of_death_barangay'] ?? '');
        $podCity = trim($data['place_of_death_city'] ?? '');
        $podProvince = trim($data['place_of_death_province'] ?? '');
        $placeOfDeath = trim($data['place_of_death'] ?? '');
        if (empty($placeOfDeath)) {
            $placeParts = array_filter([$podHospital, $podHouseNo, $podStreet, $podBarangay, $podCity, $podProvince]);
            $placeOfDeath = implode(', ', $placeParts);
        }

        $civilStatus = trim($data['civil_status'] ?? '');
        $religion = trim($data['religion'] ?? 'Islam');
        $citizenship = trim($data['citizenship'] ?? 'Filipino');

        $resHouseNo = trim($data['residence_house_no'] ?? '');
        $resStreet = trim($data['residence_street'] ?? '');
        $resBarangay = trim($data['residence_barangay'] ?? '');
        $resCity = trim($data['residence_city'] ?? '');
        $resProvince = trim($data['residence_province'] ?? '');
        $resCountry = trim($data['residence_country'] ?? 'Philippines');
        $residence = trim($data['residence'] ?? '');
        if (empty($residence)) {
            $resParts = array_filter([$resHouseNo, $resStreet, $resBarangay, $resCity, $resProvince, $resCountry]);
            $residence = implode(', ', $resParts);
        }

        $occupation = trim($data['occupation'] ?? '');

        try {
            $stmt = $this->db->prepare("INSERT INTO encoded_death_certificates 
                (registration_number, deceased_name, representative_name, province, city_municipality,
                 first_name, middle_name, last_name, sex,
                 date_of_death_day, date_of_death_month, date_of_death_year, date_of_death, time_of_death,
                 date_of_birth_day, date_of_birth_month, date_of_birth_year, date_of_birth,
                 age_years, age_months, age_days, age_hours, age_unit, age_display,
                 place_of_death_hospital, place_of_death_house_no, place_of_death_street, place_of_death_barangay, place_of_death_city, place_of_death_province, place_of_death,
                 civil_status, religion, citizenship,
                 residence_house_no, residence_street, residence_barangay, residence_city, residence_province, residence_country, residence,
                 occupation, encoded_by_id, encoded_by_name, encoded_at) 
                VALUES 
                (:reg, :dec, :rep, :prov, :city,
                 :fname, :mname, :lname, :sex,
                 :dod_d, :dod_m, :dod_y, :dod, :tod,
                 :dob_d, :dob_m, :dob_y, :dob,
                 :age_y, :age_m, :age_d, :age_h, :age_u, :age_disp,
                 :pod_hosp, :pod_hno, :pod_str, :pod_brgy, :pod_city, :pod_prov, :pod,
                 :civil, :rel, :citz,
                 :res_hno, :res_str, :res_brgy, :res_city, :res_prov, :res_cntry, :res,
                 :occ, :staff_id, :staff_name, NOW())");
            
            $res = $stmt->execute([
                'reg' => $regNo,
                'dec' => $deceasedName,
                'rep' => $representativeName ?: null,
                'prov' => $province ?: null,
                'city' => $cityMunicipality ?: null,
                'fname' => $firstName ?: null,
                'mname' => $middleName ?: null,
                'lname' => $lastName ?: null,
                'sex' => $sex,
                'dod_d' => $dodDay,
                'dod_m' => $dodMonth,
                'dod_y' => $dodYear,
                'dod' => $dateOfDeath,
                'tod' => $timeOfDeath ?: null,
                'dob_d' => $dobDay,
                'dob_m' => $dobMonth,
                'dob_y' => $dobYear,
                'dob' => $dateOfBirth,
                'age_y' => $ageYears,
                'age_m' => $ageMonths,
                'age_d' => $ageDays,
                'age_h' => $ageHours,
                'age_u' => $ageUnit,
                'age_disp' => $ageDisplay ?: null,
                'pod_hosp' => $podHospital ?: null,
                'pod_hno' => $podHouseNo ?: null,
                'pod_str' => $podStreet ?: null,
                'pod_brgy' => $podBarangay ?: null,
                'pod_city' => $podCity ?: null,
                'pod_prov' => $podProvince ?: null,
                'pod' => $placeOfDeath ?: null,
                'civil' => $civilStatus ?: null,
                'rel' => $religion ?: 'Islam',
                'citz' => $citizenship ?: 'Filipino',
                'res_hno' => $resHouseNo ?: null,
                'res_str' => $resStreet ?: null,
                'res_brgy' => $resBarangay ?: null,
                'res_city' => $resCity ?: null,
                'res_prov' => $resProvince ?: null,
                'res_cntry' => $resCountry ?: 'Philippines',
                'res' => $residence ?: null,
                'occ' => $occupation ?: null,
                'staff_id' => $staffId ?: null,
                'staff_name' => $staffName ?: 'Damayan Staff'
            ]);

            if ($res) {
                $newId = (int)$this->db->lastInsertId();

                // Automatically recognize and link any requests with the SAME registration number
                $norm = self::normalizeRegNo($regNo);
                $findReqs = $this->db->prepare("SELECT id, ref_id, deceased_name FROM burial_requests 
                                                WHERE LOWER(TRIM(existing_cert_no)) = LOWER(TRIM(:reg1)) 
                                                   OR LOWER(TRIM(death_cert_registry_no)) = LOWER(TRIM(:reg2))
                                                   OR (existing_cert_no IS NOT NULL AND existing_cert_no != '' AND REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(existing_cert_no)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm1)
                                                   OR (death_cert_registry_no IS NOT NULL AND death_cert_registry_no != '' AND REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(death_cert_registry_no)), '#', ''), ' ', ''), '-', ''), '/', '') = :norm2)");
                $findReqs->execute(['reg1' => $regNo, 'reg2' => $regNo, 'norm1' => $norm, 'norm2' => $norm]);
                $matchedReqs = $findReqs->fetchAll(PDO::FETCH_ASSOC) ?: [];

                $linkedCases = [];
                foreach ($matchedReqs as $mr) {
                    $this->db->prepare("UPDATE burial_requests 
                                        SET encoded_cert_id = :eid, 
                                            existing_record_verified = 1,
                                            updated_at = NOW() 
                                        WHERE id = :bid")->execute([
                        'eid' => $newId,
                        'bid' => $mr['id']
                    ]);

                    $this->db->prepare("UPDATE encoded_death_certificates 
                                        SET burial_request_id = :bid 
                                        WHERE id = :eid")->execute([
                        'bid' => $mr['id'],
                        'eid' => $newId
                    ]);

                    $this->logActivity(
                        (int)$mr['id'],
                        $mr['ref_id'],
                        'encoded_link',
                        'Linked to Encoded Death Certificate',
                        "Automatically recognized and linked to Encoded Death Certificate #{$regNo} (Deceased: {$deceasedName}, Representative: {$representativeName}) by {$staffName}",
                        $staffName,
                        'Staff_Damayan'
                    );

                    $linkedCases[] = $mr['ref_id'];
                }

                $msg = "Death certificate registration #{$regNo} successfully encoded and recorded in the database.";
                if (!empty($linkedCases)) {
                    $msg .= " Automatically recognized and linked to request(s): " . implode(', ', $linkedCases) . ".";
                }

                return [
                    'success' => true,
                    'id' => $newId,
                    'message' => $msg,
                    'linked_cases' => $linkedCases,
                    'record' => [
                        'id' => $newId,
                        'registration_number' => $regNo,
                        'deceased_name' => $deceasedName,
                        'representative_name' => $representativeName,
                        'encoded_by_name' => $staffName,
                        'encoded_at' => date('Y-m-d H:i:s')
                    ]
                ];
            }

            return ['success' => false, 'error' => 'Failed to save encoded death certificate into database.', 'message' => 'Failed to save encoded death certificate into database.'];
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || strpos($e->getMessage(), 'Duplicate') !== false) {
                $msg = "Registration number '{$regNo}' already exists in database.";
                return ['success' => false, 'error' => $msg, 'message' => $msg];
            }
            error_log("encodeDeathCertificate error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage(), 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Retrieve encoded death certificates with search support
     */
    public function getEncodedDeathCertificates(string $search = '', int $limit = 100, int $offset = 0): array {
        try {
            $search = trim($search);
            if (!empty($search)) {
                $term = "%{$search}%";
                $stmt = $this->db->prepare("SELECT * FROM encoded_death_certificates 
                    WHERE registration_number LIKE :q1 
                       OR deceased_name LIKE :q2 
                       OR representative_name LIKE :q3 
                       OR encoded_by_name LIKE :q4 
                    ORDER BY encoded_at DESC 
                    LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':q1', $term, PDO::PARAM_STR);
                $stmt->bindValue(':q2', $term, PDO::PARAM_STR);
                $stmt->bindValue(':q3', $term, PDO::PARAM_STR);
                $stmt->bindValue(':q4', $term, PDO::PARAM_STR);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->db->prepare("SELECT * FROM encoded_death_certificates 
                    ORDER BY encoded_at DESC 
                    LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\PDOException $e) {
            error_log("getEncodedDeathCertificates error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total encoded records
     */
    public function countEncodedDeathCertificates(string $search = ''): int {
        try {
            $search = trim($search);
            if (!empty($search)) {
                $term = "%{$search}%";
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM encoded_death_certificates 
                    WHERE registration_number LIKE :q1 
                       OR deceased_name LIKE :q2 
                       OR representative_name LIKE :q3 
                       OR encoded_by_name LIKE :q4");
                $stmt->execute(['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term]);
                return (int)$stmt->fetchColumn();
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) FROM encoded_death_certificates");
                return (int)$stmt->fetchColumn();
            }
        } catch (\PDOException $e) {
            return 0;
        }
    }

    /**
     * Get single encoded record by ID
     */
    public function getEncodedDeathCertificateById(int $id): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM encoded_death_certificates WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Archive a burial request (soft-delete into archive records)
     */
    public function archiveRequest(int|string $idOrRef, string $actorName = 'Staff Admin'): bool {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id, ref_id, cemetery_plot_id FROM burial_requests WHERE id = :id OR ref_id = :ref LIMIT 1");
            $stmt->execute([
                'id' => is_numeric($idOrRef) ? (int)$idOrRef : 0,
                'ref' => (string)$idOrRef
            ]);
            $req = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$req) {
                $this->db->rollBack();
                return false;
            }

            $reqId = (int)$req['id'];
            $refId = $req['ref_id'];
            $plotId = !empty($req['cemetery_plot_id']) ? (int)$req['cemetery_plot_id'] : null;

            // 1. Reset assigned cemetery plot if any so resources are freed
            if ($plotId) {
                $plotStmt = $this->db->prepare("UPDATE cemetery_plots 
                                                SET status = 'Available', 
                                                    assigned_deceased_name = NULL, 
                                                    assigned_burial_request_id = NULL, 
                                                    assigned_tenant_id = NULL, 
                                                    assigned_date = NULL 
                                                WHERE id = :pid");
                $plotStmt->execute(['pid' => $plotId]);
            }

            $plotStmt2 = $this->db->prepare("UPDATE cemetery_plots 
                                             SET status = 'Available', 
                                                 assigned_deceased_name = NULL, 
                                                 assigned_burial_request_id = NULL, 
                                                 assigned_tenant_id = NULL, 
                                                 assigned_date = NULL 
                                             WHERE assigned_burial_request_id = :req_id");
            $plotStmt2->execute(['req_id' => $reqId]);

            // 2. Soft-delete by setting is_archived = 1
            $upStmt = $this->db->prepare("UPDATE burial_requests 
                                          SET is_archived = 1, 
                                              archived_at = NOW(), 
                                              archived_by = :actor,
                                              updated_at = NOW() 
                                          WHERE id = :id");
            $upStmt->execute(['actor' => $actorName, 'id' => $reqId]);

            // 3. Log activity
            $this->logActivity($reqId, $refId, 'Archived', 'Burial Record Archived', "Burial request was moved to archive records by {$actorName}.", $actorName, 'Staff_Damayan');

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("BurialRequest::archiveRequest error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore an archived burial request back to active records
     */
    public function restoreRequest(int|string $idOrRef, string $actorName = 'Staff Admin'): bool {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id, ref_id FROM burial_requests WHERE id = :id OR ref_id = :ref LIMIT 1");
            $stmt->execute([
                'id' => is_numeric($idOrRef) ? (int)$idOrRef : 0,
                'ref' => (string)$idOrRef
            ]);
            $req = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$req) {
                $this->db->rollBack();
                return false;
            }

            $reqId = (int)$req['id'];
            $refId = $req['ref_id'];

            $upStmt = $this->db->prepare("UPDATE burial_requests 
                                          SET is_archived = 0, 
                                              archived_at = NULL, 
                                              archived_by = NULL,
                                              updated_at = NOW() 
                                          WHERE id = :id");
            $upStmt->execute(['id' => $reqId]);

            // Log activity
            $this->logActivity($reqId, $refId, 'Restored', 'Burial Record Restored', "Burial request was restored from archive to active records by {$actorName}.", $actorName, 'Staff_Damayan');

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("BurialRequest::restoreRequest error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteRequest(int|string $idOrRef, string $actorName = 'Staff Admin'): bool {
        return $this->archiveRequest($idOrRef, $actorName);
    }

    /**
     * Mark death certificate as received by staff/applicant
     */
    public function markCertificateReceived(int|string $idOrRef, ?string $receivedDate = null, string $actorName = 'Staff Admin'): array {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id, ref_id, tenant_id, cert_action_type, death_cert_status, copy_issued_at, death_cert_registered_date FROM burial_requests WHERE id = :id OR ref_id = :ref LIMIT 1");
            $stmt->execute([
                'id' => is_numeric($idOrRef) ? (int)$idOrRef : 0,
                'ref' => (string)$idOrRef
            ]);
            $req = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$req) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Burial case record not found.'];
            }

            $reqId = (int)$req['id'];
            $refId = $req['ref_id'];
            $certDate = !empty($receivedDate) ? $receivedDate : date('Y-m-d');
            $nowTs = date('Y-m-d H:i:s');
            $isCopy = (($req['cert_action_type'] ?? '') === 'copy');

            if ($isCopy) {
                $upStmt = $this->db->prepare("UPDATE burial_requests 
                                              SET copy_received_confirmed = 1,
                                                  copy_received_at = :ts,
                                                  copy_issued_at = COALESCE(copy_issued_at, :ts),
                                                  death_cert_registered_date = COALESCE(death_cert_registered_date, :dt),
                                                  death_cert_status = 'Certified Copy Requested or Received',
                                                  updated_at = NOW() 
                                              WHERE id = :id");
                $upStmt->execute([
                    'ts' => $nowTs,
                    'dt' => $certDate,
                    'id' => $reqId
                ]);
            } else {
                $upStmt = $this->db->prepare("UPDATE burial_requests 
                                              SET death_cert_registered_date = :dt,
                                                  death_cert_status = CASE 
                                                      WHEN death_cert_status IN ('Request Received', 'Under Review', 'Awaiting Authorized Certificate Preparation', 'Submitted for Civil Registration', 'Certificate Prepared and Certified') 
                                                      THEN 'Registered' 
                                                      ELSE death_cert_status 
                                                  END,
                                                  updated_at = NOW() 
                                              WHERE id = :id");
                $upStmt->execute([
                    'dt' => $certDate,
                    'id' => $reqId
                ]);
            }

            $this->logActivity(
                $reqId,
                $refId,
                'Certificate Received',
                'Death Certificate Received',
                "Death certificate was marked as received on " . date('M d, Y', strtotime($certDate)) . " by {$actorName}.",
                $actorName,
                'Staff_Damayan'
            );

            // Notify User
            if (!empty($req['tenant_id'])) {
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$req['tenant_id'],
                        "Death Certificate Received: #{$refId}",
                        "Assalamu Alaikum. The death certificate for case #{$refId} has been officially recorded as received.",
                        'damayan'
                    );
                } catch (\Throwable $e) {}
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Death certificate successfully marked as received.',
                'received_date' => $certDate,
                'formatted_date' => date('M d, Y', strtotime($certDate))
            ];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("BurialRequest::markCertificateReceived error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}



