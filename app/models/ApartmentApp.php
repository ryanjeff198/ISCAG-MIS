<?php
require_once BASE_PATH . '/config/database.php';

class ApartmentApp {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // ─── tenant_addinfo ───────────────────────────────────
    public function getInfo($userId) {
        $stmt = $this->db->prepare("SELECT * FROM tenant_addinfo WHERE tenant_id = :uid LIMIT 1");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function saveInfo($userId, $data) {
        $allowed = [
            'familyname','givenname','middlename','muslimname',
            'civil_status','address','birthdate','pob','age','sex',
            'tribalaffliation','numofmuslim','occupation','monthly_income',
            'companyname','companyadd','companyphone',
            'dateofshahadah','ref_name','ref_contact',
            'iscag_students','date_applied','family_data'
        ];
        $safe = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $safe[$col] = $data[$col];
            }
        }
        // If no whitelisted fields were provided, still allow creating a minimal record
        $this->db->beginTransaction();
        try {
            $existing = $this->getInfo($userId);
            if (!$existing) {
                if (empty($safe)) {
                    // Create a minimal record with just tenant_id
                    $sql = "INSERT INTO tenant_addinfo (tenant_id) VALUES (:tenant_id)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['tenant_id' => $userId]);
                } else {
                    $safe['tenant_id'] = $userId;
                    $cols = implode(',', array_keys($safe));
                    $phs  = implode(',', array_map(fn($k) => ":$k", array_keys($safe)));
                    $sql  = "INSERT INTO tenant_addinfo ($cols) VALUES ($phs)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($safe);
                }
                $lastId = $this->db->lastInsertId();
            } else {
                if (!empty($safe)) {
                    $set = implode(',', array_map(fn($k) => "$k = :$k", array_keys($safe)));
                    $sql = "UPDATE tenant_addinfo SET $set WHERE tenant_id = :tenant_id";
                    $safe['tenant_id'] = $userId;
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($safe);
                }
                $lastId = $existing['tenant_info'];
            }
            $this->db->commit();
            return $lastId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("saveInfo failed: " . $e->getMessage());
            return false;
        }
    }

    public function saveInfoImage($infoId, $docType, $binaryData, $mimeType) {
        $this->db->beginTransaction();
        try {
            // Upsert logic for tenant_addinfo_images based on addinfo_id and doc_type
            $stmt = $this->db->prepare("SELECT id FROM tenant_addinfo_images WHERE addinfo_id = :info_id AND doc_type = :doc_type LIMIT 1");
            $stmt->execute(['info_id' => $infoId, 'doc_type' => $docType]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $sql = "UPDATE tenant_addinfo_images SET image = :data, mime_type = :mime WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':id', $existing['id'], PDO::PARAM_INT);
            } else {
                $sql = "INSERT INTO tenant_addinfo_images (addinfo_id, doc_type, image, mime_type) VALUES (:info_id, :doc_type, :data, :mime)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':info_id', $infoId, PDO::PARAM_INT);
                $stmt->bindValue(':doc_type', $docType, PDO::PARAM_STR);
            }
            $stmt->bindValue(':data', $binaryData, PDO::PARAM_LOB);
            $stmt->bindValue(':mime', $mimeType, PDO::PARAM_STR);
            $result = $stmt->execute();
            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("saveInfoImage failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAddInfoImage($infoId, $docType) {
        $stmt = $this->db->prepare("SELECT image, mime_type FROM tenant_addinfo_images WHERE addinfo_id = :info_id AND doc_type = :doc_type LIMIT 1");
        $stmt->execute(['info_id' => $infoId, 'doc_type' => $docType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['image'])) {
            return ['data' => $row['image'], 'mime' => $row['mime_type'] ?: 'image/jpeg'];
        }
        return null;
    }


    // ─── apartmentsapp (unit type) ────────────────────────
    public function getApplication($userId) {
        $sql = "SELECT a.*, u.room_number, u.building 
                FROM apartmentsapp a 
                LEFT JOIN apartment_units u ON a.unit_id = u.unit_id 
                WHERE a.tenant_id = :uid 
                ORDER BY a.application_id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function saveApplication($userId, $roomtype) {
        $this->db->beginTransaction();
        try {
            $existing = $this->getApplication($userId);
            if (!$existing) {
                $sql = "INSERT INTO apartmentsapp (tenant_id, roomtype, date, status) VALUES (:uid, :rt, CURDATE(), 'Pending')";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute(['uid' => $userId, 'rt' => $roomtype]);
            } else {
                $sql = "UPDATE apartmentsapp SET roomtype = :rt WHERE application_id = :aid";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute(['rt' => $roomtype, 'aid' => $existing['application_id']]);
            }
            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("saveApplication failed: " . $e->getMessage());
            return false;
        }
    }

    // ─── ADMIN DASHBOARD METHODS ──────────────────────────
    public function getAllApplications() {
        $sql = "
            SELECT 
                a.application_id as id,
                a.tenant_id as tenant_id,
                u.first_name,
                u.last_name,
                u.contactnum as contactnum,
                a.roomtype,
                a.date as submitted_at,
                a.status,
                a.reject_reason,
                a.updated_at,
                a.assigned_at,
                a.accepted_at,
                t.* 
            FROM apartmentsapp a
            JOIN tenant_accounts u ON a.tenant_id = u.tenant_id
            LEFT JOIN tenant_addinfo t ON a.tenant_id = t.tenant_id
            ORDER BY a.application_id DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateApplicationStatus($applicationId, $status, $reason = null) {
        $sql = "UPDATE apartmentsapp SET status = :status";
        $params = ['status' => $status, 'id' => $applicationId];
        
        if ($reason !== null) {
            $sql .= ", reject_reason = :reason";
            $params['reason'] = $reason;
        }
        
        $sql .= " WHERE application_id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getTenantIdByApplicationId($applicationId) {
        $stmt = $this->db->prepare("SELECT tenant_id FROM apartmentsapp WHERE application_id = :id LIMIT 1");
        $stmt->execute(['id' => $applicationId]);
        return $stmt->fetchColumn();
    }

    // ─── PARKING METHODS ──────────────────────────
    public function getAllParkingApplications() {
        $sql = "
            SELECT 
                p.parking_id as id,
                p.tenant_id as tenant_id,
                u.first_name,
                u.last_name,
                u.email,
                u.contactnum,
                p.ownername,
                p.vehiclename,
                p.plateno,
                p.typeofvehicle,
                p.datestarted,
                p.datestarted as submitted_at,
                p.status,
                p.remarks
            FROM tenant_parking p
            JOIN tenant_accounts u ON p.tenant_id = u.tenant_id
            ORDER BY p.parking_id DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateParkingStatus($parkingId, $status, $reason = null) {
        $sql = "UPDATE tenant_parking SET status = :status";
        $params = ['status' => $status, 'id' => $parkingId];

        if ($reason !== null) {
            $sql .= ", remarks = :reason";
            $params['reason'] = $reason;
        }

        $sql .= " WHERE parking_id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function saveParkingApplication($data) {
        $sql = "INSERT INTO tenant_parking (
            tenant_id, date, vehiclename, ownername, typeofvehicle, plateno, datestarted, status
        ) VALUES (
            :tenant_id, :date, :vehiclename, :ownername, :typeofvehicle, :plateno, :datestarted, 'Pending'
        )";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'tenant_id' => $data['tenant_id'],
            'date' => $data['date'] ?? date('Y-m-d'),
            'vehiclename' => $data['vehiclename'],
            'ownername' => $data['ownername'],
            'typeofvehicle' => $data['typeofvehicle'],
            'plateno' => $data['plateno'],
            'datestarted' => $data['datestarted']
        ]);
    }

    public function getParkingApplicationsByTenant($userId) {
        $stmt = $this->db->prepare("SELECT * FROM tenant_parking WHERE tenant_id = :uid ORDER BY parking_id DESC");
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // ─── TENANT SUBMISSION ──────────────────────────
    public function updateStatusByTenant($userId, $status) {
        $sql = "UPDATE apartmentsapp SET status = :status WHERE tenant_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['status' => $status, 'uid' => $userId]);
    }

    // ═══════════════════════════════════════════════════
    //  ROOM ASSIGNMENT & WAITLIST ENGINE
    // ═══════════════════════════════════════════════════

    /**
     * Core logic: After acceptance, assign a room OR add to waitlist.
     * 
     * Flow: 
     *   1. Look up the application's requested room type
     *   2. Find an available room of that type
     *   3a. If found → Assign room, mark unit Occupied, change role to Tenant
     *   3b. If none  → Calculate queue position, set status to Queued
     *
     * @return array ['result' => 'assigned'|'queued', 'unit_id' => int|null, 'queue_position' => int|null, 'room_number' => string|null]
     */
    public function assignOrQueue(int $applicationId): array
    {
        // Get the application
        $stmt = $this->db->prepare("SELECT * FROM apartmentsapp WHERE application_id = :id");
        $stmt->execute(['id' => $applicationId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$app) return ['result' => 'error', 'message' => 'Application not found'];

        // Map roomtype label → type_id
        $typeId = $this->getTypeIdByLabel($app['roomtype']);
        if (!$typeId) return ['result' => 'error', 'message' => 'Unknown room type: ' . $app['roomtype']];

        // Find an available room of this type (first come first serve — pick first available)
        $stmt = $this->db->prepare("
            SELECT unit_id, room_number, building 
            FROM apartment_units 
            WHERE type_id = :tid AND status = 'Available' 
            ORDER BY building, room_number 
            LIMIT 1
        ");
        $stmt->execute(['tid' => $typeId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            // ── ASSIGN: Room is available ──
            // Mark unit as Occupied
            $this->db->prepare("
                UPDATE apartment_units SET status = 'Occupied', tenant_id = :tid, application_id = :aid 
                WHERE unit_id = :uid
            ")->execute([
                'tid' => $app['tenant_id'],
                'aid' => $applicationId,
                'uid' => $room['unit_id']
            ]);

            // Update application
            $this->db->prepare("
                UPDATE apartmentsapp 
                SET status = 'Assigned', unit_id = :uid, assigned_at = NOW(), accepted_at = NOW()
                WHERE application_id = :aid
            ")->execute(['uid' => $room['unit_id'], 'aid' => $applicationId]);

            // Change role: Guest → Tenant
            $this->changeRoleToTenant($app['tenant_id']);

            return [
                'result' => 'assigned',
                'unit_id' => $room['unit_id'],
                'room_number' => $room['room_number'],
                'building' => $room['building'],
                'queue_position' => null
            ];
        } else {
            // ── QUEUE: No rooms available ──
            // Get next queue position for this room type
            $stmt = $this->db->prepare("
                SELECT COALESCE(MAX(queue_position), 0) + 1 
                FROM apartmentsapp 
                WHERE roomtype = :rt AND status = 'Queued'
            ");
            $stmt->execute(['rt' => $app['roomtype']]);
            $nextPos = (int) $stmt->fetchColumn();

            // Update application to Queued
            $this->db->prepare("
                UPDATE apartmentsapp 
                SET status = 'Queued', queue_position = :qp, accepted_at = NOW()
                WHERE application_id = :aid
            ")->execute(['qp' => $nextPos, 'aid' => $applicationId]);

            return [
                'result' => 'queued',
                'unit_id' => null,
                'room_number' => null,
                'building' => null,
                'queue_position' => $nextPos
            ];
        }
    }

    /**
     * When a tenant moves out (room becomes Available), 
     * check if there's anyone queued for that room type and auto-assign.
     */
    public function releaseRoom(int $unitId): array
    {
        // Get unit info
        $stmt = $this->db->prepare("
            SELECT u.*, t.label AS type_label 
            FROM apartment_units u 
            JOIN apartment_types t ON u.type_id = t.type_id 
            WHERE u.unit_id = :uid
        ");
        $stmt->execute(['uid' => $unitId]);
        $unit = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$unit) return ['result' => 'error', 'message' => 'Unit not found'];

        // Reset the unit to Available
        $this->db->prepare("
            UPDATE apartment_units 
            SET status = 'Available', tenant_id = NULL, application_id = NULL 
            WHERE unit_id = :uid
        ")->execute(['uid' => $unitId]);

        // Check if anyone is queued for this type
        $stmt = $this->db->prepare("
            SELECT application_id, tenant_id 
            FROM apartmentsapp 
            WHERE roomtype = :rt AND status = 'Queued' 
            ORDER BY queue_position ASC 
            LIMIT 1
        ");
        $stmt->execute(['rt' => $unit['type_label']]);
        $next = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($next) {
            // Auto-assign to next in queue
            return $this->assignOrQueue($next['application_id']);
        }

        return ['result' => 'released', 'message' => 'Room released. No one in queue.'];
    }

    /**
     * Get the waitlist for a specific room type.
     */
    public function getWaitlist(?string $roomtype = null): array
    {
        $sql = "
            SELECT a.*, u.first_name, u.last_name
            FROM apartmentsapp a
            JOIN tenant_accounts u ON a.tenant_id = u.tenant_id
            WHERE a.status = 'Queued'
        ";
        $params = [];
        if ($roomtype) {
            $sql .= " AND a.roomtype = :rt";
            $params['rt'] = $roomtype;
        }
        $sql .= " ORDER BY a.queue_position ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get queue position for a specific application.
     */
    public function getQueuePosition(int $applicationId): ?int
    {
        $stmt = $this->db->prepare("SELECT queue_position FROM apartmentsapp WHERE application_id = :id AND status = 'Queued'");
        $stmt->execute(['id' => $applicationId]);
        $pos = $stmt->fetchColumn();
        return $pos !== false ? (int) $pos : null;
    }

    /**
     * Get the assigned room details for a tenant.
     */
    public function getAssignedRoom(int $tenantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, t.label AS type_label, t.price, t.type_key, a.assigned_at
            FROM apartmentsapp a
            JOIN apartment_units u ON a.unit_id = u.unit_id
            JOIN apartment_types t ON u.type_id = t.type_id
            WHERE a.tenant_id = :tid AND a.status = 'Assigned'
            ORDER BY a.assigned_at DESC LIMIT 1
        ");
        $stmt->execute(['tid' => $tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ── Helper: Map room type label to type_id ──
    private function getTypeIdByLabel(string $label): ?int
    {
        // Match labels like "One-Bedroom" → apartment_types.label
        $stmt = $this->db->prepare("
            SELECT type_id FROM apartment_types 
            WHERE label LIKE :lbl OR type_key = :key
            LIMIT 1
        ");
        // Handle partial matches: "One-Bedroom" should match "One-Bedroom Unit"
        $stmt->execute(['lbl' => '%' . $label . '%', 'key' => $label]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    // ── Helper: Change role from Guest to Tenant ──
    private function changeRoleToTenant(int $tenantId): bool
    {
        $stmt = $this->db->prepare("UPDATE tenant_accounts SET role = 'Tenant' WHERE tenant_id = :tid AND role = 'Guest'");
        return $stmt->execute(['tid' => $tenantId]);
    }

}
