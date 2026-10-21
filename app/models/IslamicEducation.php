<?php

class IslamicEducation {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS islamic_education_enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            birthdate DATE NULL,
            program_name VARCHAR(255) NOT NULL,
            status ENUM('pending', 'active', 'completed', 'dropped') DEFAULT 'pending',
            gender ENUM('male', 'female') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->db->exec($sql);

        // Migrations for Female Da'wah Registration fields from the official physical form
        $migrations = [
            "ADD COLUMN birthdate DATE NULL AFTER last_name",
            "ADD COLUMN middle_initial VARCHAR(10) NULL AFTER first_name",
            "ADD COLUMN muslim_name VARCHAR(150) NULL AFTER last_name",
            "ADD COLUMN muslim_type VARCHAR(50) DEFAULT 'Revert' AFTER muslim_name",
            "ADD COLUMN date_of_shahadah DATE NULL AFTER birthdate",
            "ADD COLUMN address TEXT NULL AFTER date_of_shahadah",
            "ADD COLUMN contact_number VARCHAR(50) NULL AFTER address",
            "ADD COLUMN facebook_account VARCHAR(150) NULL AFTER contact_number",
            "ADD COLUMN studied_islamic_before VARCHAR(10) DEFAULT 'No' AFTER facebook_account",
            "ADD COLUMN previous_school VARCHAR(255) NULL AFTER studied_islamic_before",
            "ADD COLUMN emergency_name VARCHAR(150) NULL AFTER previous_school",
            "ADD COLUMN emergency_relationship VARCHAR(100) NULL AFTER emergency_name",
            "ADD COLUMN emergency_address TEXT NULL AFTER emergency_relationship",
            "ADD COLUMN emergency_contact VARCHAR(50) NULL AFTER emergency_address",
            "ADD COLUMN is_minor TINYINT(1) DEFAULT 0 AFTER emergency_contact",
            "ADD COLUMN parent_name VARCHAR(150) NULL AFTER is_minor",
            "ADD COLUMN parent_address TEXT NULL AFTER parent_name",
            "ADD COLUMN parent_contact VARCHAR(50) NULL AFTER parent_address",
            "ADD COLUMN student_signature VARCHAR(150) NULL AFTER parent_contact",
            "ADD COLUMN guardian_signature VARCHAR(150) NULL AFTER student_signature",
            "ADD COLUMN batch_no VARCHAR(50) DEFAULT 'Batch 08' AFTER guardian_signature",
            "ADD COLUMN level VARCHAR(100) DEFAULT 'Basic' AFTER batch_no",
            "ADD COLUMN payment_method VARCHAR(50) DEFAULT 'Cash' AFTER level",
            "ADD COLUMN fee_amount DECIMAL(10,2) DEFAULT 120.00 AFTER payment_method",
            "ADD COLUMN payment_channel VARCHAR(100) NULL AFTER fee_amount",
            "ADD COLUMN payment_reference VARCHAR(100) NULL AFTER payment_channel",
            "ADD COLUMN approved_by VARCHAR(150) DEFAULT 'Sister Khadijah Dimaano' AFTER payment_reference",
            "ADD COLUMN prepared_by VARCHAR(150) DEFAULT 'IDFS Staff' AFTER approved_by",
            "ADD COLUMN payment_status VARCHAR(50) DEFAULT 'Pending' AFTER payment_reference",
            "ADD COLUMN rejection_reason TEXT NULL AFTER status",
            "ADD COLUMN total_sessions INT DEFAULT 12 AFTER rejection_reason",
            "ADD COLUMN sessions_attended INT DEFAULT 0 AFTER total_sessions",
            "ADD COLUMN sessions_completed INT DEFAULT 0 AFTER sessions_attended",
            "ADD COLUMN progress_percent INT DEFAULT 0 AFTER sessions_completed",
            "ADD COLUMN learning_status VARCHAR(50) DEFAULT 'Not Started' AFTER progress_percent"
        ];

        foreach ($migrations as $m) {
            try {
                $this->db->exec("ALTER TABLE islamic_education_enrollments " . $m);
            } catch (Exception $e) { /* Column already exists */ }
        }

        // Dedicated table for tracking learning sessions and attendance
        $sessionsTableSql = "CREATE TABLE IF NOT EXISTS islamic_education_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            enrollment_id INT NOT NULL,
            session_number INT NOT NULL,
            session_title VARCHAR(255) NOT NULL,
            is_attended TINYINT(1) DEFAULT 0,
            is_completed TINYINT(1) DEFAULT 0,
            attended_at DATETIME NULL,
            completed_at DATETIME NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_enrollment_session (enrollment_id, session_number),
            FOREIGN KEY (enrollment_id) REFERENCES islamic_education_enrollments(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->db->exec($sessionsTableSql);
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_enrollments 
            WHERE tenant_id = :tenant_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['tenant_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveForUser($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_enrollments 
            WHERE tenant_id = :tenant_id AND LOWER(status) IN ('pending', 'under_review', 'active', 'accepted', 'approved', 'enrolled')
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute(['tenant_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createRegistration($data) {
        $fields = [
            'tenant_id', 'first_name', 'last_name', 'middle_initial', 'muslim_name',
            'muslim_type', 'birthdate', 'date_of_shahadah', 'address', 'contact_number',
            'facebook_account', 'studied_islamic_before', 'previous_school',
            'emergency_name', 'emergency_relationship', 'emergency_address', 'emergency_contact',
            'is_minor', 'parent_name', 'parent_address', 'parent_contact',
            'student_signature', 'guardian_signature', 'batch_no', 'level',
            'payment_method', 'fee_amount', 'payment_channel', 'payment_reference', 'payment_status',
            'approved_by', 'prepared_by', 'program_name', 'gender', 'status'
        ];
        
        $insertCols = [];
        $insertVals = [];
        $params = [];

        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $insertCols[] = $f;
                $insertVals[] = ":$f";
                $params[$f] = $data[$f];
            }
        }

        $sql = "INSERT INTO islamic_education_enrollments (" . implode(', ', $insertCols) . ") 
                VALUES (" . implode(', ', $insertVals) . ")";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute($params);
        return $res ? (int)$this->db->lastInsertId() : 0;
    }

    public function getAllByGender($gender) {
        $stmt = $this->db->prepare("
            SELECT e.*, 
                   TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) as age
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender 
            ORDER BY e.created_at DESC
        ");
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCoursesSummary($gender) {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(NULLIF(TRIM(e.level), ''), TRIM(e.program_name)) AS course_title,
                COALESCE(NULLIF(TRIM(e.batch_no), ''), 'Batch 08') AS batch_no,
                COUNT(*) AS total_students,
                SUM(CASE WHEN LOWER(e.status) IN ('active', 'accepted', 'approved', 'enrolled') THEN 1 ELSE 0 END) AS active_count,
                SUM(CASE WHEN LOWER(e.status) IN ('pending', 'under_review') THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN LOWER(e.status) IN ('completed') THEN 1 ELSE 0 END) AS completed_count
            FROM islamic_education_enrollments e
            WHERE e.gender = :gender
            GROUP BY course_title, batch_no
            ORDER BY active_count DESC, total_students DESC
        ");
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnalytics($gender) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(status) IN ('pending', 'under_review') THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN LOWER(status) IN ('completed') THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN LOWER(status) IN ('active', 'accepted', 'approved', 'enrolled') THEN 1 ELSE 0 END) as active,
                COALESCE(ROUND(AVG(CASE WHEN LOWER(status) IN ('active', 'accepted', 'approved', 'enrolled', 'completed') THEN progress_percent ELSE NULL END)), 0) as avg_progress
            FROM islamic_education_enrollments 
            WHERE gender = :gender
        ");
        $stmt->execute(['gender' => $gender]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'pending' => 0, 'completed' => 0, 'active' => 0, 'avg_progress' => 0];

        // Age Group Breakdown (Using COALESCE for fallback)
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 0 AND 12 THEN 1 ELSE 0 END) as children,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 13 AND 19 THEN 1 ELSE 0 END) as youth,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 20 AND 39 THEN 1 ELSE 0 END) as adults,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 40 AND 59 THEN 1 ELSE 0 END) as middle_aged,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) >= 60 THEN 1 ELSE 0 END) as seniors
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender
        ");
        $stmt->execute(['gender' => $gender]);
        $stats['age_groups'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Granular Age Breakdown for Active Students
        $stmt = $this->db->prepare("
            SELECT TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) as specific_age,
                   COUNT(*) as count
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender AND e.status = 'active'
            GROUP BY specific_age
            HAVING specific_age IS NOT NULL
            ORDER BY specific_age ASC
        ");
        $stmt->execute(['gender' => $gender]);
        $stats['active_ages'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Monthly Enrollment by Age Group (Percentage based)
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(e.created_at, '%Y-%m') as month,
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 0 AND 12 THEN 'Children'
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 13 AND 19 THEN 'Youth'
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 20 AND 39 THEN 'Adults'
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 40 AND 59 THEN 'Middle-Aged'
                    ELSE 'Seniors'
                END as age_group,
                COUNT(*) as count
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender
            GROUP BY month, age_group
            ORDER BY month ASC
        ");
        $stmt->execute(['gender' => $gender]);
        $monthlyRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = [];
        foreach ($monthlyRaw as $row) {
            $m = $row['month'];
            if (!isset($monthlyData[$m])) $monthlyData[$m] = ['total' => 0, 'groups' => []];
            $monthlyData[$m]['groups'][$row['age_group']] = (int)$row['count'];
            $monthlyData[$m]['total'] += (int)$row['count'];
        }

        // Convert counts to percentages
        foreach ($monthlyData as $m => &$data) {
            foreach ($data['groups'] as $group => $count) {
                $data['groups'][$group] = round(($count / $data['total']) * 100, 1);
            }
        }
        $stats['monthly_demographics'] = $monthlyData;

        return $stats;
    }

    public function enroll($data) {
        $sql = "INSERT INTO islamic_education_enrollments (tenant_id, first_name, last_name, program_name, gender, status) 
                VALUES (:tenant_id, :first_name, :last_name, :program_name, :gender, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'tenant_id' => $data['tenant_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'program_name' => $data['program_name'],
            'gender' => $data['gender'],
            'status' => $data['status'] ?? 'pending'
        ]);
    }

    public function updateStatus(int $id, string $status, ?string $reason = null): bool {
        if ($reason !== null) {
            $stmt = $this->db->prepare("UPDATE islamic_education_enrollments SET status = :status, rejection_reason = :reason WHERE id = :id");
            return $stmt->execute(['status' => $status, 'reason' => $reason, 'id' => $id]);
        }
        $stmt = $this->db->prepare("UPDATE islamic_education_enrollments SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT e.*, 
                   TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) as age,
                   t.email
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_accounts t ON e.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePaymentInfo(int $id, array $data): bool {
        $allowed = ['payment_method', 'fee_amount', 'payment_channel', 'payment_reference', 'payment_status', 'status'];
        $sets = [];
        $params = ['id' => $id];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f = :$f";
                $params[$f] = $data[$f];
            }
        }
        if (empty($sets)) return false;
        $sql = "UPDATE islamic_education_enrollments SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Standard Islamic Education Curriculum (12 Sessions)
     */
    public static array $defaultCurriculum = [
        1 => 'Orientation & Arabic Alphabet (Huruf Hijaiyyah)',
        2 => 'Articulation Points & Phonetics (Makharijul Huruf)',
        3 => 'Harakat & Basic Vowels (Fathah, Kasrah, Dammah, Sukoon)',
        4 => 'Rules of Madd (Elongation & Basic Tajweed)',
        5 => 'Rules of Noon Sakinah & Tanween',
        6 => 'Rules of Meem Sakinah & Qalqalah',
        7 => 'Practical Qur\'an Recitation & Reading Fluency',
        8 => 'Foundations of Taharah (Purification & Wudu Demonstration)',
        9 => 'Conditions, Pillars & Obligatory Acts of Daily Salah',
        10 => 'Practical Prayer Workshop & Common Mistakes in Salah',
        11 => 'Essential Islamic Beliefs & Daily Du\'as (Hisnul Muslim)',
        12 => 'Comprehensive Practical Assessment & Program Review'
    ];

    /**
     * Ensure standard 12 learning sessions exist for this enrollment.
     */
    public function ensureSessionsSeeded(int $enrollmentId): void {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM islamic_education_sessions WHERE enrollment_id = :id");
        $stmt->execute(['id' => $enrollmentId]);
        $count = (int)$stmt->fetchColumn();

        if ($count === 0) {
            $insert = $this->db->prepare("
                INSERT INTO islamic_education_sessions 
                    (enrollment_id, session_number, session_title, is_attended, is_completed)
                VALUES 
                    (:enrollment_id, :session_number, :session_title, 0, 0)
            ");
            foreach (self::$defaultCurriculum as $num => $title) {
                $insert->execute([
                    'enrollment_id' => $enrollmentId,
                    'session_number' => $num,
                    'session_title' => $title
                ]);
            }
        }
    }

    /**
     * Automatically recalculate student progress based on attendance and completed learning sessions.
     * Rule: Do not use grades, scores, or attendance alone to determine program completion.
     */
    public function recalculateStudentProgress(int $enrollmentId): array {
        $this->ensureSessionsSeeded($enrollmentId);

        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN is_attended = 1 THEN 1 ELSE 0 END) as attended_count,
                SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_count
            FROM islamic_education_sessions 
            WHERE enrollment_id = :id
        ");
        $stmt->execute(['id' => $enrollmentId]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $totalSessions = (int)($counts['total_count'] ?? 12);
        if ($totalSessions <= 0) $totalSessions = 12;
        $attended = (int)($counts['attended_count'] ?? 0);
        $completed = (int)($counts['completed_count'] ?? 0);

        // Progress percentage formula: weighted equally by attendance and verified completed sessions
        $progressPercent = (int)round((($attended + $completed) / ($totalSessions * 2)) * 100);
        if ($progressPercent > 100) $progressPercent = 100;
        if ($progressPercent < 0) $progressPercent = 0;

        // Status Determination:
        // Attendance alone cannot trigger Completed.
        // Session completion alone cannot trigger Completed.
        // Full completion requires all required sessions to be both attended and completed.
        if ($attended === 0 && $completed === 0) {
            $learningStatus = 'Not Started';
        } elseif ($attended >= $totalSessions && $completed >= $totalSessions) {
            $learningStatus = 'Completed';
        } else {
            $learningStatus = 'In Progress';
        }

        // Auto-update enrollment table
        $updateSql = "
            UPDATE islamic_education_enrollments 
            SET total_sessions = :total,
                sessions_attended = :attended,
                sessions_completed = :completed,
                progress_percent = :progress,
                learning_status = :learning_status"
                . ($learningStatus === 'Completed' ? ", status = 'completed'" : "") . "
            WHERE id = :id
        ";
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute([
            'total' => $totalSessions,
            'attended' => $attended,
            'completed' => $completed,
            'progress' => $progressPercent,
            'learning_status' => $learningStatus,
            'id' => $enrollmentId
        ]);

        return [
            'total_sessions' => $totalSessions,
            'sessions_attended' => $attended,
            'sessions_completed' => $completed,
            'progress_percent' => $progressPercent,
            'learning_status' => $learningStatus
        ];
    }

    /**
     * Get detailed student progress, summary and session breakdown
     */
    public function getStudentProgress(int $enrollmentId): ?array {
        $student = $this->getById($enrollmentId);
        if (!$student) return null;

        $this->ensureSessionsSeeded($enrollmentId);
        $summary = $this->recalculateStudentProgress($enrollmentId);

        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_sessions 
            WHERE enrollment_id = :id 
            ORDER BY session_number ASC
        ");
        $stmt->execute(['id' => $enrollmentId]);
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Re-fetch refreshed student row
        $student = $this->getById($enrollmentId);

        return [
            'enrollment' => $student,
            'summary' => $summary,
            'sessions' => $sessions
        ];
    }

    /**
     * Update an individual session's attendance and completion state,
     * then automatically recalculate program progress.
     */
    public function updateSessionProgress(int $enrollmentId, int $sessionNumber, bool $isAttended, bool $isCompleted, ?string $notes = null): array {
        $this->ensureSessionsSeeded($enrollmentId);

        // Fetch current session state to preserve or update timestamps
        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_sessions 
            WHERE enrollment_id = :id AND session_number = :num
        ");
        $stmt->execute(['id' => $enrollmentId, 'num' => $sessionNumber]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        $attendedAt = $isAttended ? ($current['attended_at'] ?? date('Y-m-d H:i:s')) : null;
        $completedAt = $isCompleted ? ($current['completed_at'] ?? date('Y-m-d H:i:s')) : null;

        $update = $this->db->prepare("
            UPDATE islamic_education_sessions 
            SET is_attended = :is_attended,
                is_completed = :is_completed,
                attended_at = :attended_at,
                completed_at = :completed_at,
                notes = COALESCE(:notes, notes)
            WHERE enrollment_id = :enrollment_id AND session_number = :session_number
        ");
        $update->execute([
            'is_attended' => $isAttended ? 1 : 0,
            'is_completed' => $isCompleted ? 1 : 0,
            'attended_at' => $attendedAt,
            'completed_at' => $completedAt,
            'notes' => $notes !== null ? trim($notes) : null,
            'enrollment_id' => $enrollmentId,
            'session_number' => $sessionNumber
        ]);

        $summary = $this->recalculateStudentProgress($enrollmentId);
        return [
            'success' => true,
            'summary' => $summary,
            'session' => [
                'session_number' => $sessionNumber,
                'is_attended' => $isAttended ? 1 : 0,
                'is_completed' => $isCompleted ? 1 : 0,
                'attended_at' => $attendedAt,
                'completed_at' => $completedAt,
                'notes' => $notes
            ]
        ];
    }

    /**
     * Bulk update attendance and completion across multiple sessions.
     */
    public function bulkUpdateProgress(int $enrollmentId, array $sessionUpdates): array {
        $this->ensureSessionsSeeded($enrollmentId);

        foreach ($sessionUpdates as $up) {
            $num = (int)($up['session_number'] ?? 0);
            if ($num >= 1 && $num <= 12) {
                $isAtt = !empty($up['is_attended']);
                $isComp = !empty($up['is_completed']);
                $notes = isset($up['notes']) ? (string)$up['notes'] : null;
                $this->updateSessionProgress($enrollmentId, $num, $isAtt, $isComp, $notes);
            }
        }

        return $this->getStudentProgress($enrollmentId);
    }
}

