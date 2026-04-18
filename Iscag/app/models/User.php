<?php

require_once BASE_PATH . '/config/database.php';

/**
 * User Model
 * Handles all database operations related to users (tenant_accounts).
 */
class User
{
    protected string $table = 'tenant_accounts';
    protected PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    /**
     * Create a new user record.
     */
    public function create(array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Find a user by ID.
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tenant_id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Check if a field value already exists.
     */
    public function exists(string $field, string $value): bool
    {
        // Security: Whitelist allowed fields to prevent SQL injection via dynamic column names
        $allowedFields = ['email', 'contactnum', 'tenant_id'];
        if (!in_array($field, $allowedFields)) {
            throw new Exception("Invalid field name for existence check.");
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$field} = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update user verification status and OTP.
     */
    public function updateOTP(string $email, string $otp, string $expiry): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET otp_code = :otp, otp_expiry = :expiry WHERE email = :email");
        return $stmt->execute([
            'otp' => $otp,
            'expiry' => $expiry,
            'email' => $email
        ]);
    }

    /**
     * Verify OTP and activate account.
     */
    public function verifyAccount(string $email, string $otp): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND otp_code = :otp LIMIT 1");
        $stmt->execute(['email' => $email, 'otp' => $otp]);
        $user = $stmt->fetch();

        if ($user) {
            // Check expiry in PHP
            $expiry = strtotime($user['otp_expiry']);
            if ($expiry < time()) {
                return false; // Expired
            }

            $update = $this->db->prepare("UPDATE {$this->table} SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = :email");
            return $update->execute(['email' => $email]);
        }

        return false;
    }

    /**
     * Authenticate user.
     */
    public function authenticate(string $email, string $password)
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Update user password.
     */
    public function updatePassword(string $email, string $password): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password, confirmpass = :confirmpass WHERE email = :email");
        return $stmt->execute(['password' => $hashed, 'confirmpass' => $hashed, 'email' => $email]);
    }

    /**
     * Fetch additional profile information.
     */
    public function getAdditionalInfo($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM tenant_addinfo WHERE tenant_id = :userId LIMIT 1");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Update user profile information across multiple tables.
     */
    public function updateProfile($userId, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Update core fields in tenant_accounts
            $sql1 = "UPDATE {$this->table} SET email = :email, contactnum = :phone";
            $params1 = [
                'email'  => $data['email'],
                'phone'  => $data['phone'],
                'userId' => $userId
            ];

            if (!empty($data['profile_picture'])) {
                $sql1 .= ", profile_picture = :profile_picture, profile_picture_mime = :profile_picture_mime";
            }

            $sql1 .= " WHERE tenant_id = :userId";

            $stmt1 = $this->db->prepare($sql1);
            $stmt1->bindValue(':email', $data['email']);
            $stmt1->bindValue(':phone', $data['phone']);
            $stmt1->bindValue(':userId', $userId, PDO::PARAM_INT);

            if (!empty($data['profile_picture'])) {
                $stmt1->bindValue(':profile_picture', $data['profile_picture'], PDO::PARAM_LOB);
                $stmt1->bindValue(':profile_picture_mime', $data['profile_picture_mime']);
            }

            $res1 = $stmt1->execute();

            // 2. Update additional info in tenant_addinfo
            // We check if the record exists first; if not, we should probably create it
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM tenant_addinfo WHERE tenant_id = :userId");
            $stmtCheck->execute(['userId' => $userId]);
            $exists = $stmtCheck->fetchColumn() > 0;

            if ($exists) {
                $stmt2 = $this->db->prepare("
                    UPDATE tenant_addinfo 
                    SET muslimname = :arabicName, 
                        birthdate = :dob, 
                        civil_status = :civil, 
                        occupation = :occupation, 
                        address = :address,
                        dateofshahadah = :revertYear
                    WHERE tenant_id = :userId
                ");
            } else {
                $stmt2 = $this->db->prepare("
                    INSERT INTO tenant_addinfo (tenant_id, muslimname, birthdate, civil_status, occupation, address, dateofshahadah)
                    VALUES (:userId, :arabicName, :dob, :civil, :occupation, :address, :revertYear)
                ");
            }

            $stmt2->bindValue(':arabicName', $data['arabicName']);
            $stmt2->bindValue(':dob', $data['dob']);
            $stmt2->bindValue(':civil', $data['civil']);
            $stmt2->bindValue(':occupation', $data['occupation']);
            $stmt2->bindValue(':address', $data['address']);
            $stmt2->bindValue(':userId', $userId, PDO::PARAM_INT);
            
            $revertVal = null;
            if (!empty($data['revertYear'])) {
                 $revertVal = $data['revertYear'] . "-01-01";
            }
            $stmt2->bindValue(':revertYear', $revertVal);

            $res2 = $stmt2->execute();

            return $this->db->commit();
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Profile Update Error: " . $e->getMessage());
            return false;
        }
    }
}
