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
                p.remarks,
                p.signature
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

}
