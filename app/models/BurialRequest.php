<?php

/**
 * BurialRequest Model
 * Handles all database operations related to burial service requests,
 * deceased records, Islamic funeral coordination (Ghusl, Kafan, Janazah,
 * Transportation, Cemetery), volunteer assignments, and activity logs.
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
                deceased_name VARCHAR(255) NOT NULL,
                deceased_first_name VARCHAR(100) NULL,
                deceased_middle_name VARCHAR(100) NULL,
                deceased_last_name VARCHAR(100) NULL,
                deceased_sex ENUM('Male','Female') DEFAULT 'Male',
                date_of_birth DATE NULL,
                date_of_death DATE NOT NULL,
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
                requested_services TEXT NULL,
                cemetery_preference VARCHAR(255) NULL,
                target_timing VARCHAR(255) NULL,
                additional_notes TEXT NULL,
                supporting_document VARCHAR(255) NULL,
                assigned_staff_id INT NULL,
                assigned_staff_name VARCHAR(200) NULL,
                info_request_notes TEXT NULL,
                info_requested_at DATETIME NULL,
                additional_info_submitted TEXT NULL,
                additional_doc_path VARCHAR(255) NULL,
                info_submitted_at DATETIME NULL,
                rejection_reason TEXT NULL,
                actual_burial_date DATETIME NULL,
                final_cemetery VARCHAR(255) NULL,
                final_remarks TEXT NULL,
                completed_at DATETIME NULL,
                completed_by VARCHAR(200) NULL,
                status VARCHAR(50) DEFAULT 'New Request',
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (tenant_id),
                INDEX (status)
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

            $this->db->exec("CREATE TABLE IF NOT EXISTS burial_coordinations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                burial_request_id INT NOT NULL UNIQUE,
                ghusl_status ENUM('Pending', 'Scheduled', 'Volunteers Confirmed', 'In Progress', 'Completed') DEFAULT 'Pending',
                ghusl_date DATE NULL,
                ghusl_time TIME NULL,
                ghusl_venue VARCHAR(255) DEFAULT 'ISCAG Ghusl Facility',
                ghusl_volunteer_gender ENUM('Male', 'Female') DEFAULT 'Male',
                ghusl_assigned_volunteers TEXT NULL,
                ghusl_volunteer_status VARCHAR(50) DEFAULT 'Pending',
                ghusl_completed_at DATETIME NULL,
                ghusl_remarks TEXT NULL,
                kafan_status ENUM('Pending', 'In Preparation', 'Ready', 'Completed') DEFAULT 'Pending',
                kafan_type VARCHAR(100) DEFAULT 'Standard 3-piece shroud',
                kafan_prepared_by VARCHAR(200) NULL,
                kafan_completed_at DATETIME NULL,
                kafan_remarks TEXT NULL,
                janazah_status ENUM('Pending', 'Scheduled', 'Completed') DEFAULT 'Pending',
                janazah_date DATE NULL,
                janazah_time TIME NULL,
                janazah_prayer_time VARCHAR(100) DEFAULT 'After Dhuhr',
                janazah_imam_name VARCHAR(200) NULL,
                janazah_imam_contact VARCHAR(50) NULL,
                janazah_masjid VARCHAR(255) DEFAULT 'ISCAG Main Masjid',
                janazah_remarks TEXT NULL,
                janazah_completed_at DATETIME NULL,
                transport_status ENUM('Pending', 'Scheduled', 'En Route', 'Arrived', 'Completed') DEFAULT 'Pending',
                transport_vehicle_type VARCHAR(100) DEFAULT 'ISCAG Ambulance',
                transport_driver_name VARCHAR(200) NULL,
                transport_driver_contact VARCHAR(50) NULL,
                transport_pickup_location VARCHAR(255) NULL,
                transport_destination VARCHAR(255) NULL,
                transport_scheduled_datetime DATETIME NULL,
                transport_actual_departure DATETIME NULL,
                transport_actual_arrival DATETIME NULL,
                transport_remarks TEXT NULL,
                transport_completed_at DATETIME NULL,
                cemetery_status ENUM('Pending', 'Plot Reserved', 'Scheduled', 'Completed') DEFAULT 'Pending',
                cemetery_name VARCHAR(255) DEFAULT 'ISCAG Designated Muslim Cemetery',
                cemetery_plot_number VARCHAR(100) NULL,
                cemetery_scheduled_date DATE NULL,
                cemetery_scheduled_time TIME NULL,
                actual_burial_date DATETIME NULL,
                cemetery_remarks TEXT NULL,
                burial_completed_at DATETIME NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (burial_request_id) REFERENCES burial_requests(id) ON DELETE CASCADE
            )");

            $this->db->exec("CREATE TABLE IF NOT EXISTS burial_volunteers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                gender ENUM('Male', 'Female') NOT NULL,
                phone VARCHAR(50) NOT NULL,
                email VARCHAR(200) NULL,
                role VARCHAR(100) DEFAULT 'Ghusl Volunteer',
                availability_status ENUM('Available', 'Busy', 'Unavailable') DEFAULT 'Available',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
        } catch (\Throwable $e) {
            error_log("BurialRequest initTables error: " . $e->getMessage());
        }
    }

    /**
     * Check if user already submitted a recent duplicate request for the same deceased
     */
    public function checkDuplicate(int $tenantId, string $deceasedName, string $dateOfDeath): bool {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM burial_requests 
                                        WHERE tenant_id = :tid 
                                          AND LOWER(TRIM(deceased_name)) = LOWER(TRIM(:dname))
                                          AND date_of_death = :dod
                                          AND status NOT IN ('Burial Completed', 'Rejected')
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
     * Create a new burial assistance request
     */
    public function create(array $data): array|bool {
        try {
            $this->db->beginTransaction();

            $refId = $data['ref_id'] ?? ('BR-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)));

            $fName = trim($data['deceased_first_name'] ?? '');
            $mName = trim($data['deceased_middle_name'] ?? '');
            $lName = trim($data['deceased_last_name'] ?? '');
            $fullName = trim($data['deceased_name'] ?? "$fName $mName $lName");
            if (empty($fullName)) {
                $fullName = trim("$fName $lName");
            }

            $sex = in_array(ucfirst($data['deceased_sex'] ?? 'Male'), ['Male', 'Female']) ? ucfirst($data['deceased_sex']) : 'Male';

            $requestedServices = is_array($data['requested_services'] ?? null)
                ? json_encode($data['requested_services'])
                : ($data['requested_services'] ?? json_encode(["Ghusl", "Kafn", "Janazah", "Plot", "Transport"]));

            $sql = "INSERT INTO burial_requests (
                        ref_id, tenant_id, deceased_name, deceased_first_name, deceased_middle_name, deceased_last_name,
                        deceased_sex, date_of_birth, date_of_death, place_of_death, residence, religion, civil_status,
                        nationality, relationship, informant_name, informant_contact, informant_email, informant_address,
                        requested_services, cemetery_preference, target_timing, additional_notes, supporting_document,
                        status, submitted_at
                    ) VALUES (
                        :ref_id, :tenant_id, :deceased_name, :deceased_first_name, :deceased_middle_name, :deceased_last_name,
                        :deceased_sex, :dob, :dod, :pod, :residence, :religion, :civil_status,
                        :nationality, :relationship, :informant_name, :informant_contact, :informant_email, :informant_address,
                        :requested_services, :cemetery_preference, :target_timing, :additional_notes, :supporting_document,
                        'New Request', NOW()
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ref_id' => $refId,
                'tenant_id' => (int)$data['tenant_id'],
                'deceased_name' => $fullName,
                'deceased_first_name' => $fName,
                'deceased_middle_name' => $mName,
                'deceased_last_name' => $lName,
                'deceased_sex' => $sex,
                'dob' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                'dod' => !empty($data['date_of_death']) ? $data['date_of_death'] : date('Y-m-d'),
                'pod' => $data['place_of_death'] ?? null,
                'residence' => $data['residence'] ?? null,
                'religion' => $data['religion'] ?? 'Islam',
                'civil_status' => $data['civil_status'] ?? 'Married',
                'nationality' => $data['nationality'] ?? 'Filipino',
                'relationship' => $data['relationship'] ?? 'Relative',
                'informant_name' => $data['informant_name'] ?? null,
                'informant_contact' => $data['informant_contact'] ?? null,
                'informant_email' => $data['informant_email'] ?? null,
                'informant_address' => $data['informant_address'] ?? null,
                'requested_services' => $requestedServices,
                'cemetery_preference' => $data['cemetery_preference'] ?? 'ISCAG Designated Muslim Cemetery',
                'target_timing' => $data['target_timing'] ?? 'Immediate (Sunnah)',
                'additional_notes' => $data['additional_notes'] ?? null,
                'supporting_document' => $data['supporting_document'] ?? null
            ]);

            $burialId = (int)$this->db->lastInsertId();

            // 1. Automatically create Deceased Record associated with request
            $decSql = "INSERT INTO deceased_records (
                            burial_request_id, ref_id, first_name, middle_name, last_name, full_name,
                            sex, date_of_birth, date_of_death, place_of_death, residence_address,
                            civil_status, nationality, religion, created_at
                       ) VALUES (
                            :brid, :ref_id, :fname, :mname, :lname, :fullname,
                            :sex, :dob, :dod, :pod, :residence,
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
                'pod' => $data['place_of_death'] ?? null,
                'residence' => $data['residence'] ?? null,
                'civil' => $data['civil_status'] ?? 'Married',
                'nat' => $data['nationality'] ?? 'Filipino',
                'rel' => $data['religion'] ?? 'Islam'
            ]);

            // 2. Automatically initialize Burial Coordinations
            $coordSql = "INSERT INTO burial_coordinations (
                            burial_request_id, ghusl_status, ghusl_venue, ghusl_volunteer_gender,
                            kafan_status, kafan_type, janazah_status, janazah_prayer_time, janazah_masjid,
                            transport_status, transport_vehicle_type, cemetery_status, cemetery_name
                         ) VALUES (
                            :brid, 'Pending', 'ISCAG Ghusl Facility', :vol_gender,
                            'Pending', :kafan_type, 'Pending', 'After Dhuhr', 'ISCAG Main Masjid',
                            'Pending', 'ISCAG Ambulance', 'Pending', :cemetery_name
                         )";
            $coordStmt = $this->db->prepare($coordSql);
            $coordStmt->execute([
                'brid' => $burialId,
                'vol_gender' => $sex,
                'kafan_type' => ($sex === 'Female' ? '5-piece shroud (Sister)' : '3-piece shroud (Brother)'),
                'cemetery_name' => !empty($data['cemetery_preference']) ? $data['cemetery_preference'] : 'ISCAG Designated Muslim Cemetery'
            ]);

            // 3. Log activity
            $this->logActivity($burialId, $refId, 'Submission', 'Burial Assistance Request Submitted',
                "Request filed by " . ($data['informant_name'] ?? 'Family Representative') . " for deceased {$fullName}.",
                $data['informant_name'] ?? 'Applicant', 'Applicant'
            );

            $this->db->commit();

            // 4. Send notification to User
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $notif->create(
                    (int)$data['tenant_id'],
                    "Burial Request Received: #{$refId}",
                    "Assalamu Alaikum. Your burial assistance request #{$refId} for {$fullName} has been successfully submitted. Damayan staff will review and prepare the Islamic funeral arrangements shortly.",
                    'burial'
                );
            } catch (\Throwable $ne) {}

            // 5. Send notification to Admin/Staff
            try {
                require_once BASE_PATH . '/app/models/AdminNotification.php';
                $adminNotif = new AdminNotification();
                $adminNotif->create(
                    "New Burial Assistance Request #{$refId}",
                    "New request submitted by " . ($data['informant_name'] ?? 'Family Representative') . " for deceased {$fullName} ({$sex}).",
                    'burial',
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
     * Log a coordination or case activity
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
     * Get activity logs for a burial request
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

    /**
     * Get single burial request with all joined information
     */
    public function findByRefId(string $refId): ?array {
        try {
            $sql = "SELECT b.*, 
                           t.first_name AS user_first_name, t.last_name AS user_last_name, 
                           t.email AS user_email, t.contactnum AS user_contact,
                           c.id AS coord_id, c.ghusl_status, c.ghusl_date, c.ghusl_time, c.ghusl_venue, 
                           c.ghusl_volunteer_gender, c.ghusl_assigned_volunteers, c.ghusl_volunteer_status, 
                           c.ghusl_completed_at, c.ghusl_remarks,
                           c.kafan_status, c.kafan_type, c.kafan_prepared_by, c.kafan_completed_at, c.kafan_remarks,
                           c.janazah_status, c.janazah_date, c.janazah_time, c.janazah_prayer_time, 
                           c.janazah_imam_name, c.janazah_imam_contact, c.janazah_masjid, c.janazah_remarks, c.janazah_completed_at,
                           c.transport_status, c.transport_vehicle_type, c.transport_driver_name, c.transport_driver_contact, 
                           c.transport_pickup_location, c.transport_destination, c.transport_scheduled_datetime, 
                           c.transport_actual_departure, c.transport_actual_arrival, c.transport_remarks, c.transport_completed_at,
                           c.cemetery_status, c.cemetery_name, c.cemetery_plot_number, c.cemetery_scheduled_date, 
                           c.cemetery_scheduled_time, c.actual_burial_date AS coord_burial_date, c.cemetery_remarks, c.burial_completed_at,
                           d.id AS deceased_record_id, d.first_name AS dec_fname, d.middle_name AS dec_mname, d.last_name AS dec_lname,
                           d.time_of_death
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    LEFT JOIN burial_coordinations c ON b.id = c.burial_request_id
                    LEFT JOIN deceased_records d ON b.id = d.burial_request_id
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
     * Get all requests for a specific tenant/user (enforces RBAC privacy)
     */
    public function getByTenantId(int $tenantId): array {
        try {
            $sql = "SELECT b.*, 
                           c.ghusl_status, c.ghusl_date, c.ghusl_time, c.ghusl_venue,
                           c.kafan_status, c.janazah_status, c.janazah_date, c.janazah_prayer_time, c.janazah_masjid,
                           c.transport_status, c.transport_vehicle_type, c.transport_scheduled_datetime,
                           c.cemetery_status, c.cemetery_name, c.cemetery_plot_number, c.cemetery_scheduled_date
                    FROM burial_requests b
                    LEFT JOIN burial_coordinations c ON b.id = c.burial_request_id
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
     * Get all burial requests for staff admin with filter & search
     */
    public function getAll(string $filter = 'all', string $search = ''): array {
        try {
            $params = [];
            $sql = "SELECT b.*, 
                           t.first_name, t.last_name, t.email, t.contactnum,
                           c.ghusl_status, c.ghusl_date, c.ghusl_time, c.ghusl_venue, c.ghusl_assigned_volunteers, c.ghusl_volunteer_status,
                           c.kafan_status,
                           c.janazah_status, c.janazah_date, c.janazah_prayer_time, c.janazah_imam_name, c.janazah_masjid,
                           c.transport_status, c.transport_vehicle_type, c.transport_driver_name, c.transport_scheduled_datetime,
                           c.cemetery_status, c.cemetery_name, c.cemetery_plot_number, c.cemetery_scheduled_date, c.actual_burial_date
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    LEFT JOIN burial_coordinations c ON b.id = c.burial_request_id
                    WHERE 1=1";

            $filter = strtolower(trim($filter));
            if ($filter === 'pending') {
                $sql .= " AND b.status IN ('New Request', 'Pending', 'Under Review', 'Additional Information Required')";
            } elseif ($filter === 'coordination' || $filter === 'active') {
                $sql .= " AND b.status IN (
                    'Accepted for Coordination', 'Pending Volunteer Assignment', 'Volunteers Confirmed',
                    'Ghusl Scheduled', 'Ghusl Completed', 'Kafan Prepared', 'Janazah Scheduled',
                    'Janazah Completed', 'Transportation Arranged', 'Burial Scheduled'
                )";
            } elseif ($filter === 'completed') {
                $sql .= " AND b.status = 'Burial Completed'";
            } elseif ($filter === 'rejected') {
                $sql .= " AND b.status = 'Rejected'";
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
            $stats = [
                'total' => 0,
                'pending' => 0,
                'coordination' => 0,
                'completed' => 0,
                'rejected' => 0,
                'awaiting_info' => 0,
                'by_day' => 0,
                'by_week' => 0,
                'by_month' => 0,
                'monthly_labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                'monthly_data' => array_fill(0, 12, 0)
            ];

            $res = $this->db->query("SELECT status, COUNT(*) as cnt FROM burial_requests GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($res as $r) {
                $st = strtolower($r['status']);
                $cnt = (int)$r['cnt'];
                $stats['total'] += $cnt;

                if (in_array($st, ['new request', 'pending', 'under review', 'additional information required'])) {
                    $stats['pending'] += $cnt;
                }
                if ($st === 'additional information required') {
                    $stats['awaiting_info'] += $cnt;
                }
                if (in_array($st, [
                    'accepted for coordination', 'pending volunteer assignment', 'volunteers confirmed',
                    'ghusl scheduled', 'ghusl completed', 'kafan prepared', 'janazah scheduled',
                    'janazah completed', 'transportation arranged', 'burial scheduled'
                ])) {
                    $stats['coordination'] += $cnt;
                }
                if ($st === 'burial completed') {
                    $stats['completed'] += $cnt;
                }
                if ($st === 'rejected') {
                    $stats['rejected'] += $cnt;
                }
            }

            $stats['by_day'] = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE submitted_at >= NOW() - INTERVAL 1 DAY")->fetchColumn();
            $stats['by_week'] = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE submitted_at >= NOW() - INTERVAL 7 DAY")->fetchColumn();
            $stats['by_month'] = (int)$this->db->query("SELECT COUNT(*) FROM burial_requests WHERE submitted_at >= NOW() - INTERVAL 30 DAY")->fetchColumn();

            $year = date('Y');
            for ($m = 1; $m <= 12; $m++) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM burial_requests WHERE YEAR(submitted_at) = :y AND MONTH(submitted_at) = :m");
                $stmt->execute(['y' => $year, 'm' => $m]);
                $stats['monthly_data'][$m - 1] = (int)$stmt->fetchColumn();
            }

            return $stats;
        } catch (\PDOException $e) {
            return [
                'total' => 0, 'pending' => 0, 'coordination' => 0, 'completed' => 0, 'rejected' => 0, 'awaiting_info' => 0,
                'by_day' => 0, 'by_week' => 0, 'by_month' => 0,
                'monthly_labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                'monthly_data' => array_fill(0, 12, 0)
            ];
        }
    }

    /**
     * Accept a request for coordination and assign staff coordinator
     */
    public function acceptRequest(int $burialId, ?int $staffId, string $staffName, string $notes = ''): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET status = 'Accepted for Coordination', 
                                            assigned_staff_id = :sid, 
                                            assigned_staff_name = :sname 
                                        WHERE id = :id");
            $res = $stmt->execute([
                'sid' => $staffId,
                'sname' => $staffName,
                'id' => $burialId
            ]);

            if ($res) {
                $details = "Request accepted by {$staffName}.";
                if (!empty($notes)) $details .= " Notes: {$notes}";
                $this->logActivity($burialId, $burial['ref_id'], 'Acceptance', 'Request Accepted for Coordination', $details, $staffName);

                // Notify User
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$burial['tenant_id'],
                        "Burial Request Accepted: #{$burial['ref_id']}",
                        "Assalamu Alaikum. Your burial assistance request #{$burial['ref_id']} for {$burial['deceased_name']} has been ACCEPTED for coordination. Coordinator assigned: {$staffName}.",
                        'burial'
                    );
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("acceptRequest error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Staff requests additional information/documents from the applicant
     */
    public function requestAdditionalInfo(int $burialId, string $explanation, string $staffName): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET status = 'Additional Information Required', 
                                            info_request_notes = :notes,
                                            info_requested_at = NOW() 
                                        WHERE id = :id");
            $res = $stmt->execute([
                'notes' => $explanation,
                'id' => $burialId
            ]);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Requirement', 'Additional Information Requested', $explanation, $staffName);

                // Notify User
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$burial['tenant_id'],
                        "Action Required: Additional Information for #{$burial['ref_id']}",
                        "Damayan coordinator {$staffName} requested additional details for your burial request (#{$burial['ref_id']}): \"{$explanation}\". Please visit your Burial Details page to submit the requirements.",
                        'burial'
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
     * Applicant responds to additional requirements
     */
    public function submitAdditionalInfo(int $burialId, int $tenantId, string $infoText, ?string $docPath = null, string $userName = 'Applicant'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial || (int)$burial['tenant_id'] !== $tenantId) return false;

            $sql = "UPDATE burial_requests 
                    SET status = 'Under Review',
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
                    "Applicant provided updated information: {$infoText}" . ($docPath ? " (Supporting Document Attached)" : ""),
                    $userName, 'Applicant'
                );

                // Notify Admin/Staff
                try {
                    require_once BASE_PATH . '/app/models/AdminNotification.php';
                    $adminNotif = new AdminNotification();
                    $adminNotif->create(
                        "Requirements Submitted: #{$burial['ref_id']}",
                        "Applicant {$userName} submitted the requested additional information for burial request #{$burial['ref_id']}.",
                        'burial',
                        $userName,
                        $tenantId,
                        url('/admin/damayan/burial-requests')
                    );
                } catch (\Throwable $e) {}

                // Notify User confirmation
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        $tenantId,
                        "Requirements Received: #{$burial['ref_id']}",
                        "Thank you. Your additional information for burial request #{$burial['ref_id']} has been submitted to the Damayan team for review.",
                        'burial'
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
     * Reject a request with recorded reason
     */
    public function rejectRequest(int $burialId, string $reason, string $staffName): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $stmt = $this->db->prepare("UPDATE burial_requests 
                                        SET status = 'Rejected', 
                                            rejection_reason = :reason 
                                        WHERE id = :id");
            $res = $stmt->execute([
                'reason' => $reason,
                'id' => $burialId
            ]);

            if ($res) {
                $this->logActivity($burialId, $burial['ref_id'], 'Rejection', 'Request Rejected', "Reason: {$reason}", $staffName);

                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$burial['tenant_id'],
                        "Burial Request Status Update: #{$burial['ref_id']}",
                        "Your burial assistance request #{$burial['ref_id']} could not be approved. Reason: {$reason}. Please contact the ISCAG Damayan office for further guidance.",
                        'burial'
                    );
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("rejectRequest error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign / Reassign Staff Coordinator
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
     * Update Burial Request Status
     */
    public function updateStatus($refOrId, string $status, string $notes = '', string $actorName = 'Staff Admin', string $actorRole = 'Staff_Damayan'): bool {
        try {
            $burial = is_numeric($refOrId) ? $this->findById((int)$refOrId) : $this->findByRefId($refOrId);
            if (!$burial) return false;

            $stmt = $this->db->prepare("UPDATE burial_requests SET status = :status WHERE id = :id");
            $res = $stmt->execute(['status' => $status, 'id' => (int)$burial['id']]);

            if ($res) {
                $details = "Status changed to '{$status}'.";
                if (!empty($notes)) $details .= " Notes: {$notes}";
                $this->logActivity((int)$burial['id'], $burial['ref_id'], 'Status Change', "Status Updated to {$status}", $details, $actorName, $actorRole);

                // Notify User
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        (int)$burial['tenant_id'],
                        "Burial Status Update: #{$burial['ref_id']}",
                        "Assalamu Alaikum. The status of your burial request #{$burial['ref_id']} for {$burial['deceased_name']} has been updated to: {$status}.",
                        'burial'
                    );
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("updateStatus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update Deceased Record & Sync with Burial Request
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
                    deceased_sex = :sex, date_of_birth = :dob, date_of_death = :dod, place_of_death = :pod,
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
                'pod' => $data['place_of_death'] ?? null,
                'residence' => $data['residence_address'] ?? ($data['residence'] ?? null),
                'civil' => $data['civil_status'] ?? null,
                'nat' => $data['nationality'] ?? 'Filipino',
                'rel' => $data['religion'] ?? 'Islam',
                'id' => $burialId
            ]);

            // 3. Update volunteer gender preference in coordination
            $this->db->prepare("UPDATE burial_coordinations SET ghusl_volunteer_gender = :sex WHERE burial_request_id = :id")
                     ->execute(['sex' => $sex, 'id' => $burialId]);

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
     * Coordinate Ghusl (Ritual Washing)
     */
    public function updateGhusl(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $status = $data['ghusl_status'] ?? 'Scheduled';
            $assignedVolunteers = is_array($data['ghusl_assigned_volunteers'] ?? null)
                ? implode(', ', $data['ghusl_assigned_volunteers'])
                : ($data['ghusl_assigned_volunteers'] ?? '');

            $completedAt = ($status === 'Completed') ? (!empty($data['ghusl_completed_at']) ? $data['ghusl_completed_at'] : date('Y-m-d H:i:s')) : null;

            $stmt = $this->db->prepare("UPDATE burial_coordinations 
                SET ghusl_status = :status,
                    ghusl_date = :gdate,
                    ghusl_time = :gtime,
                    ghusl_venue = :venue,
                    ghusl_assigned_volunteers = :vols,
                    ghusl_volunteer_status = :vol_status,
                    ghusl_completed_at = :completed_at,
                    ghusl_remarks = :remarks
                WHERE burial_request_id = :id");

            $res = $stmt->execute([
                'status' => $status,
                'gdate' => !empty($data['ghusl_date']) ? $data['ghusl_date'] : null,
                'gtime' => !empty($data['ghusl_time']) ? $data['ghusl_time'] : null,
                'venue' => $data['ghusl_venue'] ?? 'ISCAG Ghusl Facility',
                'vols' => $assignedVolunteers,
                'vol_status' => $data['ghusl_volunteer_status'] ?? 'Confirmed',
                'completed_at' => $completedAt,
                'remarks' => $data['ghusl_remarks'] ?? null,
                'id' => $burialId
            ]);

            if ($res) {
                // If Ghusl status is Scheduled, update overall request status if appropriate
                if ($status === 'Scheduled' && in_array($burial['status'], ['Accepted for Coordination', 'Under Review', 'New Request', 'Pending Volunteer Assignment'])) {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Ghusl Scheduled' WHERE id = :id")->execute(['id' => $burialId]);
                } elseif ($status === 'Completed' && $burial['status'] !== 'Burial Completed') {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Ghusl Completed' WHERE id = :id")->execute(['id' => $burialId]);
                }

                $details = "Ghusl ritual status: {$status}. Venue: " . ($data['ghusl_venue'] ?? 'ISCAG Ghusl Facility');
                if (!empty($data['ghusl_date'])) $details .= ". Schedule: {$data['ghusl_date']} {$data['ghusl_time']}";
                if (!empty($assignedVolunteers)) $details .= ". Assigned Volunteers: {$assignedVolunteers} ({$data['ghusl_volunteer_status']})";
                if (!empty($data['ghusl_remarks'])) $details .= ". Remarks: {$data['ghusl_remarks']}";

                $this->logActivity($burialId, $burial['ref_id'], 'Ghusl Coordination', 'Ghusl Coordination Updated', $details, $staffName);

                // Notify User if Scheduled or Completed
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    if ($status === 'Scheduled' && !empty($data['ghusl_date'])) {
                        $notif->create(
                            (int)$burial['tenant_id'],
                            "Ghusl Scheduled: #{$burial['ref_id']}",
                            "The ritual washing (Ghusl) for {$burial['deceased_name']} has been scheduled on {$data['ghusl_date']} at " . date('g:i A', strtotime($data['ghusl_time'])) . " at {$data['ghusl_venue']}.",
                            'burial'
                        );
                    } elseif ($status === 'Completed') {
                        $notif->create(
                            (int)$burial['tenant_id'],
                            "Ghusl Completed: #{$burial['ref_id']}",
                            "The ritual washing (Ghusl) for {$burial['deceased_name']} has been completed with dignity and care.",
                            'burial'
                        );
                    }
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("updateGhusl error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Manage Kafan (Burial Shroud) Preparation
     */
    public function updateKafan(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $status = $data['kafan_status'] ?? 'Ready';
            $completedAt = in_array($status, ['Ready', 'Completed']) ? date('Y-m-d H:i:s') : null;

            $stmt = $this->db->prepare("UPDATE burial_coordinations 
                SET kafan_status = :status,
                    kafan_type = :type,
                    kafan_prepared_by = :prep_by,
                    kafan_completed_at = :completed_at,
                    kafan_remarks = :remarks
                WHERE burial_request_id = :id");

            $res = $stmt->execute([
                'status' => $status,
                'type' => $data['kafan_type'] ?? ($burial['deceased_sex'] === 'Female' ? '5-piece shroud (Sister)' : '3-piece shroud (Brother)'),
                'prep_by' => $data['kafan_prepared_by'] ?? $staffName,
                'completed_at' => $completedAt,
                'remarks' => $data['kafan_remarks'] ?? null,
                'id' => $burialId
            ]);

            if ($res) {
                if ($status === 'Ready' && in_array($burial['status'], ['Ghusl Completed', 'Accepted for Coordination'])) {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Kafan Prepared' WHERE id = :id")->execute(['id' => $burialId]);
                }

                $details = "Kafan shroud status: {$status} ({$data['kafan_type']}). Prepared by: " . ($data['kafan_prepared_by'] ?? $staffName);
                if (!empty($data['kafan_remarks'])) $details .= ". Remarks: {$data['kafan_remarks']}";

                $this->logActivity($burialId, $burial['ref_id'], 'Kafan Preparation', 'Kafan Preparation Updated', $details, $staffName);
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("updateKafan error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Coordinate Salat al-Janazah (Funeral Prayer)
     */
    public function updateJanazah(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $status = $data['janazah_status'] ?? 'Scheduled';
            $completedAt = ($status === 'Completed') ? date('Y-m-d H:i:s') : null;

            $stmt = $this->db->prepare("UPDATE burial_coordinations 
                SET janazah_status = :status,
                    janazah_date = :jdate,
                    janazah_time = :jtime,
                    janazah_prayer_time = :ptime,
                    janazah_imam_name = :imam_name,
                    janazah_imam_contact = :imam_contact,
                    janazah_masjid = :masjid,
                    janazah_remarks = :remarks,
                    janazah_completed_at = :completed_at
                WHERE burial_request_id = :id");

            $res = $stmt->execute([
                'status' => $status,
                'jdate' => !empty($data['janazah_date']) ? $data['janazah_date'] : null,
                'jtime' => !empty($data['janazah_time']) ? $data['janazah_time'] : null,
                'ptime' => $data['janazah_prayer_time'] ?? 'After Dhuhr',
                'imam_name' => $data['janazah_imam_name'] ?? null,
                'imam_contact' => $data['janazah_imam_contact'] ?? null,
                'masjid' => $data['janazah_masjid'] ?? 'ISCAG Main Masjid',
                'remarks' => $data['janazah_remarks'] ?? null,
                'completed_at' => $completedAt,
                'id' => $burialId
            ]);

            if ($res) {
                if ($status === 'Scheduled' && $burial['status'] !== 'Burial Completed') {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Janazah Scheduled' WHERE id = :id")->execute(['id' => $burialId]);
                } elseif ($status === 'Completed' && $burial['status'] !== 'Burial Completed') {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Janazah Completed' WHERE id = :id")->execute(['id' => $burialId]);
                }

                $details = "Janazah Prayer status: {$status}. Masjid: " . ($data['janazah_masjid'] ?? 'ISCAG Main Masjid');
                if (!empty($data['janazah_date'])) $details .= ". Scheduled: {$data['janazah_date']} ({$data['janazah_prayer_time']})";
                if (!empty($data['janazah_imam_name'])) $details .= ". Officiating Imam: {$data['janazah_imam_name']}";
                if (!empty($data['janazah_remarks'])) $details .= ". Remarks: {$data['janazah_remarks']}";

                $this->logActivity($burialId, $burial['ref_id'], 'Janazah Coordination', 'Salat al-Janazah Scheduled', $details, $staffName);

                // Notify User
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    if ($status === 'Scheduled' && !empty($data['janazah_date'])) {
                        $prayerTimeStr = $data['janazah_prayer_time'] ?? '';
                        $notif->create(
                            (int)$burial['tenant_id'],
                            "Janazah Prayer Scheduled: #{$burial['ref_id']}",
                            "Salat al-Janazah (funeral prayer) for {$burial['deceased_name']} has been scheduled on {$data['janazah_date']} ({$prayerTimeStr}) at {$data['janazah_masjid']}. Officiated by Imam: {$data['janazah_imam_name']}.",
                            'burial'
                        );
                    }
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("updateJanazah error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Manage Transportation (Ambulance / Hearse)
     */
    public function updateTransport(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $status = $data['transport_status'] ?? 'Scheduled';
            $completedAt = ($status === 'Completed') ? date('Y-m-d H:i:s') : null;

            $stmt = $this->db->prepare("UPDATE burial_coordinations 
                SET transport_status = :status,
                    transport_vehicle_type = :vehicle,
                    transport_driver_name = :driver,
                    transport_driver_contact = :driver_contact,
                    transport_pickup_location = :pickup,
                    transport_destination = :destination,
                    transport_scheduled_datetime = :sched_dt,
                    transport_actual_departure = :departure,
                    transport_actual_arrival = :arrival,
                    transport_remarks = :remarks,
                    transport_completed_at = :completed_at
                WHERE burial_request_id = :id");

            $res = $stmt->execute([
                'status' => $status,
                'vehicle' => $data['transport_vehicle_type'] ?? 'ISCAG Ambulance',
                'driver' => $data['transport_driver_name'] ?? null,
                'driver_contact' => $data['transport_driver_contact'] ?? null,
                'pickup' => $data['transport_pickup_location'] ?? null,
                'destination' => $data['transport_destination'] ?? null,
                'sched_dt' => !empty($data['transport_scheduled_datetime']) ? $data['transport_scheduled_datetime'] : null,
                'departure' => !empty($data['transport_actual_departure']) ? $data['transport_actual_departure'] : null,
                'arrival' => !empty($data['transport_actual_arrival']) ? $data['transport_actual_arrival'] : null,
                'remarks' => $data['transport_remarks'] ?? null,
                'completed_at' => $completedAt,
                'id' => $burialId
            ]);

            if ($res) {
                if ($status === 'Scheduled' && $burial['status'] !== 'Burial Completed') {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Transportation Arranged' WHERE id = :id")->execute(['id' => $burialId]);
                }

                $details = "Transport status: {$status}. Vehicle: " . ($data['transport_vehicle_type'] ?? 'ISCAG Ambulance');
                if (!empty($data['transport_driver_name'])) $details .= ". Driver: {$data['transport_driver_name']}";
                if (!empty($data['transport_pickup_location'])) $details .= ". Pickup: {$data['transport_pickup_location']}";
                if (!empty($data['transport_destination'])) $details .= " -> Destination: {$data['transport_destination']}";

                $this->logActivity($burialId, $burial['ref_id'], 'Transportation', 'Transportation Coordination Updated', $details, $staffName);

                // Notify User if En Route or Arrived
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    if ($status === 'En Route') {
                        $notif->create(
                            (int)$burial['tenant_id'],
                            "Transportation Update: #{$burial['ref_id']}",
                            "The ISCAG funeral vehicle is currently en route for the transportation of {$burial['deceased_name']}.",
                            'burial'
                        );
                    } elseif ($status === 'Arrived') {
                        $notif->create(
                            (int)$burial['tenant_id'],
                            "Transportation Arrived: #{$burial['ref_id']}",
                            "The deceased ({$burial['deceased_name']}) has safely arrived at the designated destination.",
                            'burial'
                        );
                    }
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("updateTransport error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Manage Cemetery & Burial Arrangements
     */
    public function updateCemetery(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $status = $data['cemetery_status'] ?? 'Scheduled';

            $stmt = $this->db->prepare("UPDATE burial_coordinations 
                SET cemetery_status = :status,
                    cemetery_name = :cname,
                    cemetery_plot_number = :plot,
                    cemetery_scheduled_date = :cdate,
                    cemetery_scheduled_time = :ctime,
                    cemetery_remarks = :remarks
                WHERE burial_request_id = :id");

            $res = $stmt->execute([
                'status' => $status,
                'cname' => $data['cemetery_name'] ?? 'ISCAG Designated Muslim Cemetery',
                'plot' => $data['cemetery_plot_number'] ?? null,
                'cdate' => !empty($data['cemetery_scheduled_date']) ? $data['cemetery_scheduled_date'] : null,
                'ctime' => !empty($data['cemetery_scheduled_time']) ? $data['cemetery_scheduled_time'] : null,
                'remarks' => $data['cemetery_remarks'] ?? null,
                'id' => $burialId
            ]);

            if ($res) {
                if ($status === 'Scheduled' && $burial['status'] !== 'Burial Completed') {
                    $this->db->prepare("UPDATE burial_requests SET status = 'Burial Scheduled' WHERE id = :id")->execute(['id' => $burialId]);
                }

                $details = "Cemetery status: {$status}. Cemetery: " . ($data['cemetery_name'] ?? 'ISCAG Designated Muslim Cemetery');
                if (!empty($data['cemetery_plot_number'])) $details .= ". Plot #: {$data['cemetery_plot_number']}";
                if (!empty($data['cemetery_scheduled_date'])) $details .= ". Scheduled: {$data['cemetery_scheduled_date']} {$data['cemetery_scheduled_time']}";

                $this->logActivity($burialId, $burial['ref_id'], 'Cemetery Coordination', 'Burial & Cemetery Scheduled', $details, $staffName);

                // Notify User
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    if ($status === 'Scheduled' && !empty($data['cemetery_scheduled_date'])) {
                        $plotStr = !empty($data['cemetery_plot_number']) ? " (Plot: {$data['cemetery_plot_number']})" : "";
                        $notif->create(
                            (int)$burial['tenant_id'],
                            "Burial Scheduled: #{$burial['ref_id']}",
                            "The burial for {$burial['deceased_name']} has been scheduled at {$data['cemetery_name']}{$plotStr} on {$data['cemetery_scheduled_date']} at " . date('g:i A', strtotime($data['cemetery_scheduled_time'])) . ".",
                            'burial'
                        );
                    }
                } catch (\Throwable $e) {}
            }

            return $res;
        } catch (\PDOException $e) {
            error_log("updateCemetery error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete Burial Case (Explicit Confirmation Action)
     * Requirement 3.I:
     * After burial has taken place, records actual burial date, cemetery, completed services, remarks.
     * Preserves complete history.
     */
    public function completeBurialCase(int $burialId, array $data, string $staffName = 'Staff Admin'): bool {
        try {
            $burial = $this->findById($burialId);
            if (!$burial) return false;

            $actualBurialDate = !empty($data['actual_burial_date']) ? $data['actual_burial_date'] : date('Y-m-d H:i:s');
            $finalCemetery = $data['final_cemetery'] ?? ($burial['cemetery_name'] ?? 'ISCAG Designated Muslim Cemetery');
            $finalRemarks = $data['final_remarks'] ?? 'Burial rites completed in accordance with Islamic Sunnah.';

            $this->db->beginTransaction();

            // 1. Update burial_requests
            $stmt = $this->db->prepare("UPDATE burial_requests 
                SET status = 'Burial Completed',
                    actual_burial_date = :actual_date,
                    final_cemetery = :cemetery,
                    final_remarks = :remarks,
                    completed_at = NOW(),
                    completed_by = :staff
                WHERE id = :id");
            $stmt->execute([
                'actual_date' => $actualBurialDate,
                'cemetery' => $finalCemetery,
                'remarks' => $finalRemarks,
                'staff' => $staffName,
                'id' => $burialId
            ]);

            // 2. Mark all coordination modules completed
            $this->db->prepare("UPDATE burial_coordinations 
                SET ghusl_status = 'Completed',
                    ghusl_completed_at = COALESCE(ghusl_completed_at, NOW()),
                    kafan_status = 'Completed',
                    kafan_completed_at = COALESCE(kafan_completed_at, NOW()),
                    janazah_status = 'Completed',
                    janazah_completed_at = COALESCE(janazah_completed_at, NOW()),
                    transport_status = 'Completed',
                    transport_completed_at = COALESCE(transport_completed_at, NOW()),
                    cemetery_status = 'Completed',
                    actual_burial_date = :actual_date,
                    burial_completed_at = NOW()
                WHERE burial_request_id = :id")->execute([
                    'actual_date' => $actualBurialDate,
                    'id' => $burialId
                ]);

            // 3. Log Activity
            $this->logActivity($burialId, $burial['ref_id'], 'Completion', 'Burial Case Officially Completed',
                "Burial rites completed on {$actualBurialDate} at {$finalCemetery}. Final remarks: {$finalRemarks}. Completed by: {$staffName}.",
                $staffName
            );

            $this->db->commit();

            // 4. Notify User
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $notif->create(
                    (int)$burial['tenant_id'],
                    "Burial Service Completed: #{$burial['ref_id']}",
                    "Assalamu Alaikum. The Islamic funeral and burial services for {$burial['deceased_name']} have been completed at {$finalCemetery}. May Allah (SWT) grant them forgiveness, elevate their status in Jannatul Firdaus, and grant patience and comfort to the bereaved family.",
                    'burial'
                );
            } catch (\Throwable $e) {}

            return true;
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("completeBurialCase error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pool of registered Ghusl volunteers (filtered by gender if requested)
     */
    public function getVolunteers(?string $gender = null): array {
        try {
            if ($gender && in_array(ucfirst($gender), ['Male', 'Female'])) {
                $stmt = $this->db->prepare("SELECT * FROM burial_volunteers WHERE gender = :g ORDER BY availability_status ASC, name ASC");
                $stmt->execute(['g' => ucfirst($gender)]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }
            return $this->db->query("SELECT * FROM burial_volunteers ORDER BY gender ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Update volunteer availability status
     */
    public function updateVolunteerStatus(int $volunteerId, string $status): bool {
        try {
            $stmt = $this->db->prepare("UPDATE burial_volunteers SET availability_status = :status WHERE id = :id");
            return $stmt->execute(['status' => $status, 'id' => $volunteerId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get list of authorized Staff Admin / Coordinator accounts
     */
    public function getAllStaff(): array {
        try {
            return $this->db->query("SELECT tenant_id, first_name, last_name, email, role 
                                     FROM tenant_accounts 
                                     WHERE role IN ('Admin', 'Staff_Damayan') 
                                     ORDER BY first_name ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
