<?php

require_once BASE_PATH . '/config/database.php';

/**
 * ApartmentType Model
 * Handles CRUD for apartment_types, apartment_type_images, and apartment_units.
 */
class ApartmentType
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    // ═══════════════════════════════════════════
    //  APARTMENT TYPES
    // ═══════════════════════════════════════════

    /**
     * Get all active apartment types with their thumbnail image path.
     */
    public function getAllTypes(): array
    {
        $sql = "
            SELECT t.*, 
                   (SELECT i.image_id FROM apartment_type_images i 
                    WHERE i.type_id = t.type_id AND i.is_thumbnail = 1 
                    ORDER BY i.sort_order LIMIT 1) AS thumbnail_id
            FROM apartment_types t
            WHERE t.is_active = 1
            ORDER BY t.sort_order, t.type_id
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single apartment type by ID, including all images.
     */
    public function getTypeById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM apartment_types WHERE type_id = :id");
        $stmt->execute(['id' => $id]);
        $type = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$type) return null;

        $type['images'] = $this->getImagesByType($id);
        $type['available_count'] = $this->getAvailableCountByType($id);
        return $type;
    }

    /**
     * Get a type by its key (e.g., 'studio', '1br', '2br').
     */
    public function getTypeByKey(string $key): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM apartment_types WHERE type_key = :key AND is_active = 1");
        $stmt->execute(['key' => $key]);
        $type = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$type) return null;

        $type['images'] = $this->getImagesByType($type['type_id']);
        $type['available_count'] = $this->getAvailableCountByType($type['type_id']);
        return $type;
    }

    /**
     * Create a new apartment type.
     */
    public function createType(array $data): int
    {
        $fields = ['type_key', 'label', 'price', 'capacity', 'description',
                    'floor_area', 'bedrooms', 'bathroom', 'kitchen', 'parking', 'sort_order',
                    'inclusions', 'rules', 'security_deposit', 'advance_rent', 'other_fees', 
                    'min_lease', 'notice_period', 'queue_label'];
        $safe = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $safe[$f] = $data[$f];
            }
        }

        $cols = implode(',', array_keys($safe));
        $phs  = implode(',', array_map(fn($k) => ":$k", array_keys($safe)));
        $sql  = "INSERT INTO apartment_types ($cols) VALUES ($phs)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($safe);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing apartment type.
     */
    public function updateType(int $id, array $data): bool
    {
        $fields = ['type_key', 'label', 'price', 'capacity', 'description',
                    'floor_area', 'bedrooms', 'bathroom', 'kitchen', 'parking', 'sort_order', 'is_active',
                    'inclusions', 'rules', 'security_deposit', 'advance_rent', 'other_fees', 
                    'min_lease', 'notice_period', 'queue_label'];
        $safe = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $safe[$f] = $data[$f];
            }
        }
        if (empty($safe)) return false;

        $set = implode(',', array_map(fn($k) => "$k = :$k", array_keys($safe)));
        $safe['id'] = $id;
        $stmt = $this->db->prepare("UPDATE apartment_types SET $set WHERE type_id = :id");
        return $stmt->execute($safe);
    }

    /**
     * Soft-delete an apartment type (set is_active = 0).
     */
    public function deleteType(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE apartment_types SET is_active = 0 WHERE type_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ═══════════════════════════════════════════
    //  APARTMENT TYPE IMAGES
    // ═══════════════════════════════════════════

    /**
     * Get all images for a given type, ordered.
     */
    public function getImagesByType(int $typeId): array
    {
        $stmt = $this->db->prepare(
            "SELECT image_id, type_id, caption, is_thumbnail, sort_order, mime_type, created_at 
             FROM apartment_type_images WHERE type_id = :tid ORDER BY is_thumbnail DESC, sort_order"
        );
        $stmt->execute(['tid' => $typeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add an image to a type.
     */
    /**
     * Add an image to a type (BLOB storage).
     */
    public function addImage(int $typeId, ?string $binaryData, string $mimeType, string $caption = '', bool $isThumbnail = false, ?string $filePath = null): int
    {
        // If marking as thumbnail, unmark all others for this type
        if ($isThumbnail) {
            $this->db->prepare("UPDATE apartment_type_images SET is_thumbnail = 0 WHERE type_id = :tid")
                      ->execute(['tid' => $typeId]);
        }

        $maxSort = $this->db->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM apartment_type_images WHERE type_id = :tid");
        $maxSort->execute(['tid' => $typeId]);
        $nextSort = $maxSort->fetchColumn();

        $stmt = $this->db->prepare(
            "INSERT INTO apartment_type_images (type_id, image_data, mime_type, caption, is_thumbnail, sort_order, file_path) 
             VALUES (:tid, :data, :mime, :cap, :thumb, :sort, :path)"
        );
        $stmt->bindValue(':tid', $typeId, PDO::PARAM_INT);
        $stmt->bindValue(':data', $binaryData, $binaryData === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
        $stmt->bindValue(':mime', $mimeType, PDO::PARAM_STR);
        $stmt->bindValue(':cap', $caption, PDO::PARAM_STR);
        $stmt->bindValue(':thumb', $isThumbnail ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':sort', $nextSort, PDO::PARAM_INT);
        $stmt->bindValue(':path', $filePath, $filePath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get image BLOB data by image_id (for serving to browser).
     */
    public function getImageData(int $imageId): ?array
    {
        $stmt = $this->db->prepare("SELECT image_data, mime_type, file_path FROM apartment_type_images WHERE image_id = :id");
        $stmt->execute(['id' => $imageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return [
                'data' => $row['image_data'], 
                'mime' => $row['mime_type'] ?: 'image/jpeg',
                'file_path' => $row['file_path']
            ];
        }
        return null;
    }

    /**
     * Remove an image record (file deletion handled by caller).
     */
    /**
     * Remove an image record from DB (no file cleanup needed with BLOB).
     */
    public function removeImage(int $imageId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM apartment_type_images WHERE image_id = :id");
        return $stmt->execute(['id' => $imageId]);
    }

    /**
     * Set a specific image as the thumbnail for its type.
     */
    public function setThumbnail(int $imageId): bool
    {
        $stmt = $this->db->prepare("SELECT type_id FROM apartment_type_images WHERE image_id = :id");
        $stmt->execute(['id' => $imageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $this->db->prepare("UPDATE apartment_type_images SET is_thumbnail = 0 WHERE type_id = :tid")
                  ->execute(['tid' => $row['type_id']]);
        $this->db->prepare("UPDATE apartment_type_images SET is_thumbnail = 1 WHERE image_id = :id")
                  ->execute(['id' => $imageId]);
        return true;
    }

    // ═══════════════════════════════════════════
    //  APARTMENT UNITS
    // ═══════════════════════════════════════════

    /**
     * Get all units with their type info.
     */
    public function getAllUnits(): array
    {
        $sql = "
            SELECT u.*, t.label AS type_label, t.type_key, t.price
            FROM apartment_units u
            JOIN apartment_types t ON u.type_id = t.type_id
            ORDER BY u.building, u.room_number
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single unit by ID.
     */
    public function getUnitById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, t.label AS type_label, t.type_key FROM apartment_units u 
             JOIN apartment_types t ON u.type_id = t.type_id WHERE u.unit_id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create a new unit.
     */
    public function createUnit(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO apartment_units (type_id, room_number, building, status, description, application_id, tenant_id) 
             VALUES (:type_id, :room_number, :building, :status, :description, :application_id, :tenant_id)"
        );
        $stmt->execute([
            'type_id'        => $data['type_id'],
            'room_number'    => $data['room_number'],
            'building'       => $data['building'] ?? null,
            'status'         => $data['status'] ?? 'Available',
            'description'    => $data['description'] ?? '',
            'application_id' => $data['application_id'] ?? null,
            'tenant_id'      => $data['tenant_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a unit.
     */
    public function updateUnit(int $id, array $data): bool
    {
        $fields = ['type_id', 'room_number', 'building', 'status', 'description', 'application_id', 'tenant_id'];
        $safe = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $safe[$f] = $data[$f];
            }
        }
        if (empty($safe)) return false;

        $set = implode(',', array_map(fn($k) => "$k = :$k", array_keys($safe)));
        $safe['id'] = $id;
        $stmt = $this->db->prepare("UPDATE apartment_units SET $set WHERE unit_id = :id");
        return $stmt->execute($safe);
    }

    /**
     * Delete a unit.
     */
    public function deleteUnit(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM apartment_units WHERE unit_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if a unit exists (by building and room_number).
     */
    public function checkUnitExists(string $building, string $roomNumber, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM apartment_units WHERE building = :building AND room_number = :room_number";
        $params = ['building' => $building, 'room_number' => $roomNumber];
        
        if ($excludeId) {
            $sql .= " AND unit_id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get next available room number for a given building and floor.
     */
    public function getNextAvailableRoom(string $building, string $floor): string
    {
        $stmt = $this->db->prepare("
            SELECT room_number FROM apartment_units 
            WHERE building = :building AND room_number LIKE :floor_pattern
            ORDER BY room_number ASC
        ");
        $stmt->execute(['building' => $building, 'floor_pattern' => $floor . '%']);
        $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $takenRooms = array_map(function($r) {
            return (int) substr($r, 1);
        }, $existing);

        // Find the first gap between 1 and 12
        for ($i = 1; $i <= 12; $i++) {
            if (!in_array($i, $takenRooms)) {
                return $floor . str_pad($i, 2, '0', STR_PAD_LEFT);
            }
        }

        // If 1-12 are full, return max + 1
        $max = !empty($takenRooms) ? max($takenRooms) : 12;
        return $floor . str_pad($max + 1, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Count available units for a given type.
     */
    public function getAvailableCountByType(int $typeId): int
    {
        // First check if this type is Transient
        $stmtType = $this->db->prepare("SELECT type_key, label FROM apartment_types WHERE type_id = :tid");
        $stmtType->execute(['tid' => $typeId]);
        $typeInfo = $stmtType->fetch(PDO::FETCH_ASSOC);
        if (!$typeInfo) return 0;
        
        $label = $typeInfo['label'];
        $typeKey = $typeInfo['type_key'];
        $isTransient = $label && stripos($label, 'Transient') !== false;

        // Committed users: Approved (Lease sent) or Queued but not yet assigned to a physical unit_id
        $stmtCommitted = $this->db->prepare("
            SELECT COUNT(*) FROM apartmentsapp 
            WHERE (roomtype = :key OR roomtype = :label)
              AND status IN ('Approved', 'Verified', 'Queued')
              AND unit_id IS NULL
        ");
        $stmtCommitted->execute(['key' => $typeKey, 'label' => $label]);
        $committedCount = (int) $stmtCommitted->fetchColumn();

        if ($isTransient) {
            // Total capacity logic for Transient (10 slots per room)
            $stmt = $this->db->prepare("
                SELECT u.unit_id,
                       (SELECT COUNT(*) FROM apartmentsapp a WHERE a.unit_id = u.unit_id AND a.status = 'Assigned') as occupant_count
                FROM apartment_units u 
                WHERE u.type_id = :tid 
                  AND u.status IN ('Available', 'Occupied')
            ");
            $stmt->execute(['tid' => $typeId]);
            $units = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalPhysicalSlots = 0;
            $takenSlots = 0;
            foreach ($units as $u) {
                $totalPhysicalSlots += 10;
                $takenSlots += (int)$u['occupant_count'];
            }
            
            $availableSlots = max(0, $totalPhysicalSlots - $takenSlots - $committedCount);
            // Return Concept of "Units" as whole blocks of 10 for basic display
            // But usually the UI will use current_slots_left via getTypesForUserView
            return (int) ceil($availableSlots / 10);
        } else {
            // Normal behavior: physical Available rooms minus Committed applicants
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM apartment_units WHERE type_id = :tid AND status = 'Available'"
            );
            $stmt->execute(['tid' => $typeId]);
            $physicalAvailable = (int) $stmt->fetchColumn();

            return max(0, $physicalAvailable - $committedCount);
        }
    }

    /**
     * Get all distinct building names.
     */
    public function getBuildings(): array
    {
        return $this->db->query(
            "SELECT DISTINCT building FROM apartment_units WHERE building IS NOT NULL ORDER BY building"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get units filtered by building.
     */
    public function getUnitsByBuilding(string $building): array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, t.label AS type_label, t.type_key, t.price
            FROM apartment_units u
            JOIN apartment_types t ON u.type_id = t.type_id
            WHERE u.building = :building
            ORDER BY u.room_number
        ");
        $stmt->execute(['building' => $building]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ═══════════════════════════════════════════
    //  COMPOSITE: For User View
    // ═══════════════════════════════════════════

    /**
     * Get all active types with images + availability (optimized for user cards).
     */
    public function getTypesForUserView(): array
    {
        $types = $this->getAllTypes();
        foreach ($types as &$type) {
            $type['images'] = $this->getImagesByType($type['type_id']);
            $type['available_count'] = $this->getAvailableCountByType($type['type_id']);
            
            $isTransient = stripos($type['label'], 'Transient') !== false;
            $type['is_transient'] = $isTransient;
            
            if ($isTransient && $type['available_count'] > 0) {
                // Committed users: Approved but not yet assigned to a physical unit_id
                $stmtCommitted = $this->db->prepare("
                    SELECT COUNT(*) FROM apartmentsapp 
                    WHERE (roomtype = :key OR roomtype = :label)
                      AND status IN ('Approved', 'Verified')
                      AND unit_id IS NULL
                ");
                $stmtCommitted->execute(['key' => $type['type_key'], 'label' => $type['label']]);
                $committedCount = (int) $stmtCommitted->fetchColumn();

                // Find the next physical unit that has space
                $stmt = $this->db->prepare("
                    SELECT u.unit_id,
                           (SELECT COUNT(*) FROM apartmentsapp a WHERE a.unit_id = u.unit_id AND a.status = 'Assigned') as occupant_count
                    FROM apartment_units u 
                    WHERE u.type_id = :tid 
                      AND u.status IN ('Available', 'Occupied')
                    HAVING occupant_count < 10
                    ORDER BY occupant_count DESC, u.building, u.room_number 
                    LIMIT 1
                ");
                $stmt->execute(['tid' => $type['type_id']]);
                $nextUnit = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($nextUnit) {
                    $slotsInUnit = 10 - (int)$nextUnit['occupant_count'];
                    $type['current_slots_left'] = max(0, $slotsInUnit - $committedCount);
                } else {
                    $type['current_slots_left'] = 0;
                }
            } else {
                $type['current_slots_left'] = 0;
            }

            // Calculate current queue count for this type
            $stmtQ = $this->db->prepare("
                SELECT COUNT(*) FROM apartmentsapp 
                WHERE (roomtype = :key OR roomtype = :label)
                  AND status = 'Queued'
            ");
            $stmtQ->execute(['key' => $type['type_key'], 'label' => $type['label']]);
            $type['queue_count'] = (int) $stmtQ->fetchColumn();
        }
        return $types;
    }
}
