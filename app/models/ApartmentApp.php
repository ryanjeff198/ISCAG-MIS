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
            'iscag_students','date_applied'
        ];
        $safe = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $safe[$col] = $data[$col];
            }
        }
        if (empty($safe)) return false;

        $this->db->beginTransaction();
        try {
            $existing = $this->getInfo($userId);
            if (!$existing) {
                $safe['tenant_id'] = $userId;
                $cols = implode(',', array_keys($safe));
                $phs  = implode(',', array_map(fn($k) => ":$k", array_keys($safe)));
                $sql  = "INSERT INTO tenant_addinfo ($cols) VALUES ($phs)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($safe);
                $lastId = $this->db->lastInsertId();
            } else {
                $set = implode(',', array_map(fn($k) => "$k = :$k", array_keys($safe)));
                $sql = "UPDATE tenant_addinfo SET $set WHERE tenant_id = :tenant_id";
                $safe['tenant_id'] = $userId;
                $stmt = $this->db->prepare($sql);
                $stmt->execute($safe);
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
        $stmt = $this->db->prepare("SELECT * FROM apartmentsapp WHERE tenant_id = :uid ORDER BY application_id DESC LIMIT 1");
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

}
