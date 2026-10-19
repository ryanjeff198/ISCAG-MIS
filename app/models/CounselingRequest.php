<?php

/**
 * CounselingRequest Model
 * Handles all database operations related to counseling requests,
 * scheduling, counselor assignment, conflict detection, and lifecycle tracking.
 */
class CounselingRequest
{
    protected string $table = 'counseling_requests';
    protected PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        // 1. Base table creation
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            gender ENUM('male', 'female') NOT NULL DEFAULT 'male',
            reason VARCHAR(255) NOT NULL,
            description TEXT NULL,
            session_type VARCHAR(50) DEFAULT 'In-Person',
            preferred_date DATE NULL,
            preferred_time VARCHAR(50) NULL,
            status VARCHAR(50) DEFAULT 'pending',
            counselor_id INT NULL,
            counselor_name VARCHAR(255) NULL,
            counselor_role VARCHAR(255) NULL,
            counselor_contact VARCHAR(255) NULL,
            appointment_date DATE NULL,
            start_time VARCHAR(20) NULL,
            end_time VARCHAR(20) NULL,
            final_session_type VARCHAR(50) DEFAULT 'In-Person',
            appointment_location VARCHAR(255) DEFAULT 'ISCAG Center, 2nd Floor Da\'wah Counseling Office',
            meeting_link VARCHAR(255) NULL,
            meeting_instructions TEXT NULL,
            counselor_notes TEXT NULL,
            rejection_reason TEXT NULL,
            completed_notes TEXT NULL,
            counselor_assigned_at DATETIME NULL,
            confirmed_at DATETIME NULL,
            completed_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id)
        )");

        // 2. Ensure columns exist on legacy installations
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM {$this->table}")->fetchAll(PDO::FETCH_COLUMN);
            $alterations = [
                'full_name' => "VARCHAR(255) NULL",
                'age' => "INT NULL",
                'civil_status' => "VARCHAR(50) NULL",
                'address' => "VARCHAR(255) NULL",
                'contact_number' => "VARCHAR(50) NULL",
                'description' => "TEXT NULL",
                'session_type' => "VARCHAR(50) DEFAULT 'In-Person'",
                'counselor_id' => "INT NULL",
                'counselor_name' => "VARCHAR(255) NULL",
                'counselor_role' => "VARCHAR(255) NULL",
                'counselor_contact' => "VARCHAR(255) NULL",
                'appointment_date' => "DATE NULL",
                'start_time' => "VARCHAR(20) NULL",
                'end_time' => "VARCHAR(20) NULL",
                'final_session_type' => "VARCHAR(50) DEFAULT 'In-Person'",
                'appointment_location' => "VARCHAR(255) DEFAULT 'ISCAG Center, 2nd Floor Da\\'wah Counseling Office'",
                'meeting_link' => "VARCHAR(255) NULL",
                'meeting_instructions' => "TEXT NULL",
                'counselor_notes' => "TEXT NULL",
                'rejection_reason' => "TEXT NULL",
                'completed_notes' => "TEXT NULL",
                'counselor_assigned_at' => "DATETIME NULL",
                'confirmed_at' => "DATETIME NULL",
                'completed_at' => "DATETIME NULL",
                'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
            ];

            foreach ($alterations as $col => $def) {
                if (!in_array($col, $cols)) {
                    $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN {$col} {$def}");
                }
            }

            // Ensure status is VARCHAR(50) so all state transitions work seamlessly
            $this->db->exec("ALTER TABLE {$this->table} MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
        } catch (Exception $e) {
            error_log("CounselingRequest schema check error: " . $e->getMessage());
        }

        // 3. Ensure counselors table exists & has seed data
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS counselors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NULL,
                name VARCHAR(255) NOT NULL,
                designation VARCHAR(255) NOT NULL,
                gender ENUM('male', 'female') NOT NULL DEFAULT 'male',
                email VARCHAR(255) NULL,
                phone VARCHAR(50) NULL,
                office_location VARCHAR(255) DEFAULT 'ISCAG Center, 2nd Floor Da\'wah Counseling Office',
                specialties VARCHAR(255) DEFAULT 'Family & Marital, Spiritual Guidance, Youth Mentorship',
                profile_photo VARCHAR(255) NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $cCount = (int)$this->db->query("SELECT COUNT(*) FROM counselors")->fetchColumn();
            if ($cCount === 0) {
                $stmt = $this->db->prepare("INSERT INTO counselors (tenant_id, name, designation, gender, email, phone, office_location, specialties, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([59, 'Ustaz Norman Ungasin', 'Head of Da\'wah & Senior Islamic Counselor', 'male', 'kizumikaze1@gmail.com', '+63 906 574 0819', 'ISCAG Center, 2nd Floor Da\'wah Office Room 201', 'Family Mediation, Marital Harmony, Spiritual Tazkiyah', 'active']);
                $stmt->execute([null, 'Ustaz Abdullah Al-Mansoor', 'Resident Islamic Scholar & Family Counselor', 'male', 'counseling.dawah@iscag.org', '+63 917 123 4567', 'ISCAG Center, 2nd Floor Da\'wah Office Room 202', 'Pre-Marital Consultation, Grief & Bereavement, Islamic Ethics', 'active']);
                $stmt->execute([null, 'Ustaz Bilal Siddiqui', 'Youth Advisor & Revert Support Counselor', 'male', 'youth.guidance@iscag.org', '+63 918 765 4321', 'ISCAG Center, 2nd Floor Youth & Da\'wah Wing', 'Revert Integration, Youth & Academic Mentorship, Personal Development', 'active']);
            }
        } catch (Exception $e) {
            error_log("Counselors table init error: " . $e->getMessage());
        }
    }

    public function create(array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function createAndGetId(array $data): int|false
    {
        if ($this->create($data)) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   COALESCE(NULLIF(r.full_name, ''), CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, ''))) AS full_name,
                   COALESCE(NULLIF(r.full_name, ''), CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, ''))) AS client_name,
                   t.first_name, t.last_name, t.email, 
                   COALESCE(NULLIF(r.contact_number, ''), t.contactnum) AS contact_number,
                   COALESCE(NULLIF(r.civil_status, ''), p.civil_status) AS civil_status,
                   COALESCE(NULLIF(r.address, ''), p.address) AS current_address,
                   COALESCE(r.age, TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE())) as age,
                   COALESCE(c.name, r.counselor_name) AS counselor_full_name,
                   COALESCE(c.designation, r.counselor_role) AS counselor_designation,
                   COALESCE(c.email, r.counselor_contact) AS counselor_contact_info,
                   c.phone AS counselor_phone,
                   c.office_location AS counselor_office,
                   c.profile_photo AS counselor_photo
            FROM {$this->table} r
            LEFT JOIN tenant_accounts t ON r.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON t.tenant_id = p.tenant_id
            LEFT JOIN counselors c ON r.counselor_id = c.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*,
                   COALESCE(NULLIF(r.full_name, ''), CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, ''))) AS full_name,
                   COALESCE(NULLIF(r.full_name, ''), CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, ''))) AS client_name,
                   t.first_name, t.last_name, t.email,
                   COALESCE(NULLIF(r.contact_number, ''), t.contactnum) AS contact_number,
                   COALESCE(NULLIF(r.civil_status, ''), p.civil_status) AS civil_status,
                   COALESCE(NULLIF(r.address, ''), p.address) AS current_address,
                   COALESCE(r.age, TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE())) as age,
                   COALESCE(c.name, r.counselor_name) AS counselor_full_name,
                   COALESCE(c.designation, r.counselor_role) AS counselor_designation,
                   COALESCE(c.email, r.counselor_contact) AS counselor_contact_info,
                   c.phone AS counselor_phone,
                   c.office_location AS counselor_office,
                   c.profile_photo AS counselor_photo
            FROM {$this->table} r
            LEFT JOIN tenant_accounts t ON r.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON t.tenant_id = p.tenant_id
            LEFT JOIN counselors c ON r.counselor_id = c.id
            WHERE r.tenant_id = :id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve the user's primary active request.
     * Prioritizes: Confirmed (active), then Approved (awaiting schedule), then Under Review, then Pending,
     * then the most recent request.
     */
    public function getActiveRequestForUser(int $userId): ?array
    {
        $all = $this->getByUser($userId);
        if (empty($all)) {
            return null;
        }

        $today = date('Y-m-d');

        // 1. Look for confirmed upcoming/today appointment
        foreach ($all as $req) {
            $st = strtolower($req['status'] ?? '');
            if ($st === 'confirmed' || $st === 'approved') {
                $aptDate = $req['appointment_date'] ?? $req['preferred_date'];
                if (!$aptDate || $aptDate >= $today) {
                    return $req;
                }
            }
        }

        // 2. Look for in-progress statuses
        foreach ($all as $req) {
            $st = strtolower($req['status'] ?? '');
            if (in_array($st, ['approved_awaiting_schedule', 'under_review', 'pending', 'rescheduled'])) {
                return $req;
            }
        }

        // 3. Fallback to the most recent record
        return $all[0];
    }

    public function getByGender(string $gender = 'male'): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   COALESCE(NULLIF(r.full_name, ''), CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, ''))) AS full_name,
                   COALESCE(NULLIF(r.full_name, ''), CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, ''))) AS client_name,
                   t.first_name, t.last_name, t.email, 
                   COALESCE(NULLIF(r.contact_number, ''), t.contactnum) AS contact_number,
                   COALESCE(NULLIF(r.civil_status, ''), p.civil_status) AS civil_status,
                   COALESCE(NULLIF(r.address, ''), p.address) AS current_address,
                   COALESCE(r.age, TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE())) as age,
                   COALESCE(c.name, r.counselor_name) AS counselor_full_name,
                   COALESCE(c.designation, r.counselor_role) AS counselor_designation,
                   COALESCE(c.email, r.counselor_contact) AS counselor_contact_info,
                   c.phone AS counselor_phone,
                   c.office_location AS counselor_office
            FROM {$this->table} r
            LEFT JOIN tenant_accounts t ON r.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON t.tenant_id = p.tenant_id
            LEFT JOIN counselors c ON r.counselor_id = c.id
            WHERE r.gender = :gender
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnalytics(string $gender = 'male'): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(status) = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN LOWER(status) = 'under_review' THEN 1 ELSE 0 END) as under_review,
                SUM(CASE WHEN LOWER(status) IN ('confirmed', 'approved') THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN LOWER(status) = 'approved_awaiting_schedule' THEN 1 ELSE 0 END) as awaiting_schedule,
                SUM(CASE WHEN LOWER(status) = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN LOWER(status) = 'no_show' THEN 1 ELSE 0 END) as no_show,
                SUM(CASE WHEN LOWER(status) = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM {$this->table}
            WHERE gender = :gender
        ");
        $stmt->execute(['gender' => $gender]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0, 'pending' => 0, 'under_review' => 0, 'confirmed' => 0, 'awaiting_schedule' => 0, 'completed' => 0, 'no_show' => 0, 'rejected' => 0
        ];
        $stats['approved'] = $stats['confirmed']; // backward compatibility

        // Breakdown by concern
        $stmt = $this->db->prepare("
            SELECT reason, COUNT(*) as count 
            FROM {$this->table} 
            WHERE gender = :gender 
            GROUP BY reason 
            ORDER BY count DESC
        ");
        $stmt->execute(['gender' => $gender]);
        $stats['concerns'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        return $stats;
    }

    /**
     * Check counselor availability and prevent scheduling conflicts.
     * Returns conflicting appointment array if conflict exists, or null if clear.
     */
    public function checkConflict(int $counselorId, string $date, string $startTime, string $endTime, ?int $excludeId = null): ?array
    {
        if (!$counselorId || !$date || !$startTime) {
            return null;
        }

        $sql = "SELECT id, reason, appointment_date, start_time, end_time, counselor_name 
                FROM {$this->table} 
                WHERE counselor_id = :cid 
                  AND appointment_date = :adate 
                  AND LOWER(status) IN ('confirmed', 'approved')";
        $params = ['cid' => $counselorId, 'adate' => $date];

        if ($excludeId) {
            $sql .= " AND id != :ex";
            $params['ex'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert times to 24h timestamp for interval overlap comparison
        $newStart = strtotime("$date $startTime");
        $newEnd   = $endTime ? strtotime("$date $endTime") : ($newStart + 3600);

        foreach ($appointments as $apt) {
            $existingStart = strtotime("{$apt['appointment_date']} {$apt['start_time']}");
            $existingEnd   = $apt['end_time'] ? strtotime("{$apt['appointment_date']} {$apt['end_time']}") : ($existingStart + 3600);

            // Interval overlap check: [start1 < end2] and [start2 < end1]
            if ($newStart < $existingEnd && $existingStart < $newEnd) {
                return $apt; // Conflict detected!
            }
        }

        return null;
    }

    /**
     * Accept request and set confirmed appointment schedule with assigned counselor.
     */
    public function acceptAndSchedule(int $id, array $data): bool
    {
        $counselorId = (int)($data['counselor_id'] ?? 0);
        $counselor = $this->getCounselorById($counselorId);

        $cName = $counselor['name'] ?? ($data['counselor_name'] ?? 'Assigned Counselor');
        $cRole = $counselor['designation'] ?? ($data['counselor_role'] ?? 'Islamic Counselor');
        $cContact = $counselor['email'] ?? ($data['counselor_contact'] ?? 'counseling@iscag.org');

        $sql = "UPDATE {$this->table} SET
                    status = 'confirmed',
                    counselor_id = :cid,
                    counselor_name = :cname,
                    counselor_role = :crole,
                    counselor_contact = :ccontact,
                    appointment_date = :adate,
                    start_time = :stime,
                    end_time = :etime,
                    final_session_type = :stype,
                    appointment_location = :aloc,
                    meeting_link = :mlink,
                    meeting_instructions = :minstr,
                    counselor_notes = :notes,
                    counselor_assigned_at = NOW(),
                    confirmed_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'cid'      => $counselorId ?: null,
            'cname'    => $cName,
            'crole'    => $cRole,
            'ccontact' => $cContact,
            'adate'    => $data['appointment_date'] ?? null,
            'stime'    => $data['start_time'] ?? null,
            'etime'    => $data['end_time'] ?? null,
            'stype'    => $data['final_session_type'] ?? 'In-Person',
            'aloc'     => $data['appointment_location'] ?? 'ISCAG Center, 2nd Floor Da\'wah Counseling Office',
            'mlink'    => $data['meeting_link'] ?? null,
            'minstr'   => $data['meeting_instructions'] ?? null,
            'notes'    => $data['counselor_notes'] ?? null,
            'id'       => $id
        ]);
    }

    public function setUnderReview(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'under_review' WHERE id = :id AND status = 'pending'");
        return $stmt->execute(['id' => $id]);
    }

    public function rejectRequest(int $id, string $reason): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'rejected', rejection_reason = :reason WHERE id = :id");
        return $stmt->execute(['reason' => trim($reason), 'id' => $id]);
    }

    public function markCompleted(int $id, ?string $notes = null): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'completed', completed_notes = :notes, completed_at = NOW() WHERE id = :id");
        return $stmt->execute(['notes' => $notes, 'id' => $id]);
    }

    public function getActiveCounselors(string $gender = 'male'): array
    {
        $stmt = $this->db->prepare("SELECT * FROM counselors WHERE status = 'active' AND (gender = :gender OR gender IS NULL) ORDER BY name ASC");
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCounselorById(int $id): ?array
    {
        if (!$id) return null;
        $stmt = $this->db->prepare("SELECT * FROM counselors WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => strtolower(trim($status)), 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
