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
                    'floor_area', 'bedrooms', 'bathroom', 'kitchen', 'parking', 'sort_order'];
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
                    'floor_area', 'bedrooms', 'bathroom', 'kitchen', 'parking', 'sort_order', 'is_active'];
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
    public function addImage(int $typeId, string $binaryData, string $mimeType, string $caption = '', bool $isThumbnail = false): int
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
            "INSERT INTO apartment_type_images (type_id, image_data, mime_type, caption, is_thumbnail, sort_order) 
             VALUES (:tid, :data, :mime, :cap, :thumb, :sort)"
        );
        $stmt->bindValue(':tid', $typeId, PDO::PARAM_INT);
        $stmt->bindValue(':data', $binaryData, PDO::PARAM_LOB);
        $stmt->bindValue(':mime', $mimeType, PDO::PARAM_STR);
        $stmt->bindValue(':cap', $caption, PDO::PARAM_STR);
        $stmt->bindValue(':thumb', $isThumbnail ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':sort', $nextSort, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get image BLOB data by image_id (for serving to browser).
     */
    public function getImageData(int $imageId): ?array
    {
        $stmt = $this->db->prepare("SELECT image_data, mime_type FROM apartment_type_images WHERE image_id = :id");
        $stmt->execute(['id' => $imageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['image_data'])) {
            return ['data' => $row['image_data'], 'mime' => $row['mime_type'] ?: 'image/jpeg'];
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
            ORDER BY u.room_number
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
            "INSERT INTO apartment_units (type_id, room_number, status, description, application_id, tenant_id) 
             VALUES (:type_id, :room_number, :status, :description, :application_id, :tenant_id)"
        );
        $stmt->execute([
            'type_id'        => $data['type_id'],
            'room_number'    => $data['room_number'],
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
        $fields = ['type_id', 'room_number', 'status', 'description', 'application_id', 'tenant_id'];
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
     * Count available units for a given type.
     */
    public function getAvailableCountByType(int $typeId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM apartment_units WHERE type_id = :tid AND status = 'Available'"
        );
        $stmt->execute(['tid' => $typeId]);
        return (int) $stmt->fetchColumn();
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
        }
        return $types;
    }
}
