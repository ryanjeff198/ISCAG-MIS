<?php

/**
 * BurialRequest Model
 * Handles all database operations related to burial service requests.
 */
class BurialRequest
{
    protected $db;

    /**
     * Create a new burial request
     */
    public function create(array $data): bool {
        try {
            $sql = "INSERT INTO burial_requests (
                        ref_id, tenant_id, deceased_name, date_of_birth, date_of_death,
                        place_of_death, residence, religion, relationship, status, submitted_at
                    ) VALUES (
                        :ref_id, :tenant_id, :deceased_name, :dob, :dod,
                        :pod, :residence, :religion, :relationship, 'Pending', NOW()
                    )";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'ref_id' => $data['ref_id'],
                'tenant_id' => $data['tenant_id'],
                'deceased_name' => $data['deceased_name'],
                'dob' => $data['date_of_birth'] ?? null,
                'dod' => $data['date_of_death'],
                'pod' => $data['place_of_death'],
                'residence' => $data['residence'] ?? null,
                'religion' => $data['religion'] ?? 'Islam',
                'relationship' => $data['relationship']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function __construct() {
        $this->db = getDbConnection();
    }

    /**
     * Get all burial requests with requester names
     */
    public function getAll(): array {
        try {
            $sql = "SELECT b.*, t.first_name, t.last_name, t.email 
                    FROM burial_requests b
                    LEFT JOIN tenant_accounts t ON b.tenant_id = t.tenant_id
                    ORDER BY b.submitted_at DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            // Fallback mock data
            return [
                ['ref_id' => 'BR-2024-001', 'first_name' => 'Ahmad', 'last_name' => 'Abdullah', 'deceased_name' => 'Omar Abdullah', 'submitted_at' => date('Y-m-d'), 'status' => 'Pending'],
                ['ref_id' => 'BR-2024-002', 'first_name' => 'Mariam', 'last_name' => 'Ali', 'deceased_name' => 'Fatima Ali', 'submitted_at' => date('Y-m-d', strtotime('-2 days')), 'status' => 'Approved'],
                ['ref_id' => 'BR-2024-003', 'first_name' => 'Yusuf', 'last_name' => 'Khan', 'deceased_name' => 'Zainab Khan', 'submitted_at' => date('Y-m-d', strtotime('-5 days')), 'status' => 'Completed']
            ];
        }
    }

    /**
     * Get burial analytics for the Damayan dashboard
     */
    public function getAnalytics(): array {
        try {
            $sql = "SELECT status, COUNT(*) as count FROM burial_requests GROUP BY status";
            $results = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            $stats = [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'completed' => 0,
                'by_day' => 0,
                'by_week' => 0,
                'by_month' => 0
            ];
            
            foreach ($results as $row) {
                $status = strtolower($row['status']);
                $count = (int)$row['count'];
                $stats['total'] += $count;
                if ($status === 'pending') $stats['pending'] += $count;
                if ($status === 'approved' || $status === 'verified' || $status === 'arrived') $stats['approved'] += $count;
                if ($status === 'completed') $stats['completed'] += $count;
            }

            // Periodic stats (Last 24h, Last 7d, Last 30d)
            $stats['by_day'] = $this->db->query("SELECT COUNT(*) FROM burial_requests WHERE submitted_at >= NOW() - INTERVAL 1 DAY")->fetchColumn();
            $stats['by_week'] = $this->db->query("SELECT COUNT(*) FROM burial_requests WHERE submitted_at >= NOW() - INTERVAL 7 DAY")->fetchColumn();
            $stats['by_month'] = $this->db->query("SELECT COUNT(*) FROM burial_requests WHERE submitted_at >= NOW() - INTERVAL 30 DAY")->fetchColumn();
            
            // Monthly breakdown for the current year
            $stats['monthly_labels'] = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $stats['monthly_data'] = [];
            $year = date('Y');
            for ($m = 1; $m <= 12; $m++) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM burial_requests WHERE YEAR(submitted_at) = :y AND MONTH(submitted_at) = :m");
                $stmt->execute(['y' => $year, 'm' => $m]);
                $stats['monthly_data'][] = (int)$stmt->fetchColumn();
            }

            return $stats;
        } catch (PDOException $e) {
            // Fallback mock analytics
            return [
                'total' => 3, 'pending' => 1, 'approved' => 1, 'completed' => 1,
                'by_day' => 1, 'by_week' => 2, 'by_month' => 3
            ];
        }
    }

    /**
     * Find a burial request by its ref_id
     */
    public function findByRefId($id): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM burial_requests WHERE ref_id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getByTenantId($tenantId): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM burial_requests WHERE tenant_id = :tid ORDER BY submitted_at DESC");
            $stmt->execute(['tid' => $tenantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [
                ['ref_id' => 'BR-MOCK-001', 'deceased_name' => 'Sample Request (Mock)', 'relationship' => 'Relative', 'submitted_at' => date('Y-m-d'), 'status' => 'Pending']
            ];
        }
    }

    /**
     * Update burial request status
     */
    public function updateStatus($id, $status): bool {
        try {
            $stmt = $this->db->prepare("UPDATE burial_requests SET status = :status WHERE ref_id = :id");
            return $stmt->execute(['status' => $status, 'id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
