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
     * Update user role.
     */
    public function updateRole(int $userId, string $role): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET role = :role WHERE tenant_id = :id");
        return $stmt->execute(['role' => $role, 'id' => $userId]);
    }

    public function getAdditionalInfo($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM tenant_user_profiles WHERE tenant_id = :userId LIMIT 1");
        $stmt->execute(['userId' => $userId]);
        $row = $stmt->fetch();
        
        if ($row) {
            // Map new column names back to expected keys for backward compatibility in views
            return [
                'muslimname' => $row['muslim_name'],
                'birthdate' => $row['birthdate'],
                'civil_status' => $row['civil_status'],
                'occupation' => $row['occupation'],
                'address' => $row['address'],
                'dateofshahadah' => $row['revert_year']
            ];
        }
        return [];
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
            if (!empty($data['profile_picture_path'])) {
                $sql1 .= ", profile_picture_path = :profile_picture_path, profile_picture = NULL";
            } else if (!empty($data['profile_picture'])) {
                $sql1 .= ", profile_picture = :profile_picture, profile_picture_mime = :profile_picture_mime";
            }

            $sql1 .= " WHERE tenant_id = :userId";

            $stmt1 = $this->db->prepare($sql1);
            $stmt1->bindValue(':email', $data['email']);
            $stmt1->bindValue(':phone', $data['phone']);
            $stmt1->bindValue(':userId', $userId, PDO::PARAM_INT);

            if (!empty($data['profile_picture_path'])) {
                $stmt1->bindValue(':profile_picture_path', $data['profile_picture_path']);
            } else if (!empty($data['profile_picture'])) {
                $stmt1->bindValue(':profile_picture', $data['profile_picture'], PDO::PARAM_LOB);
                $stmt1->bindValue(':profile_picture_mime', $data['profile_picture_mime']);
            }

            $res1 = $stmt1->execute();

            // 2. Update additional info in tenant_user_profiles
            // We check if the record exists first; if not, we should probably create it
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM tenant_user_profiles WHERE tenant_id = :userId");
            $stmtCheck->execute(['userId' => $userId]);
            $exists = $stmtCheck->fetchColumn() > 0;

            if ($exists) {
                $stmt2 = $this->db->prepare("
                    UPDATE tenant_user_profiles 
                    SET muslim_name = :arabicName, 
                        birthdate = :dob, 
                        civil_status = :civil, 
                        occupation = :occupation, 
                        address = :address,
                        revert_year = :revertYear
                    WHERE tenant_id = :userId
                ");
            } else {
                $stmt2 = $this->db->prepare("
                    INSERT INTO tenant_user_profiles (tenant_id, muslim_name, birthdate, civil_status, occupation, address, revert_year)
                    VALUES (:userId, :arabicName, :dob, :civil, :occupation, :address, :revertYear)
                ");
            }

            $stmt2->bindValue(':arabicName', $data['arabicName']);
            $stmt2->bindValue(':dob', $data['dob']);
            $stmt2->bindValue(':civil', $data['civil']);
            $stmt2->bindValue(':occupation', $data['occupation']);
            $stmt2->bindValue(':address', $data['address']);
            $stmt2->bindValue(':userId', $userId, PDO::PARAM_INT);
            
            $revertVal = $data['revertYear'] ?? null;
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
    
    /**
     * Delete an account and ALL related data (CLEANUP).
     */
    public function deleteAccount(int $userId): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Delete associated data first to satisfy constraints if any (Manual Cascade)
            
            // Explicitly vacate any unit occupied by this tenant to fire the MySQL trigger
            // Foreign Key cascades (ON DELETE SET NULL) skip triggers in MySQL!
            $this->db->prepare("UPDATE apartment_units SET tenant_id = NULL, status = 'Available', application_id = NULL WHERE tenant_id = ?")->execute([$userId]);
            
            // Profiles
            $this->db->prepare("DELETE FROM tenant_user_profiles WHERE tenant_id = ?")->execute([$userId]);
            
            // Apartment Applications and their sub-data
            $this->db->prepare("DELETE FROM apartmentsapp WHERE tenant_id = ?")->execute([$userId]);
            
            // Leases
            $this->db->prepare("DELETE FROM leases WHERE tenant_id = ?")->execute([$userId]);
            
            // Payments
            $this->db->prepare("DELETE FROM payments WHERE tenant_id = ?")->execute([$userId]);
            
            // Billing
            $this->db->prepare("DELETE FROM billing WHERE tenant_id = ?")->execute([$userId]);
            
            // Parking
            $this->db->prepare("DELETE FROM tenant_parking WHERE tenant_id = ?")->execute([$userId]);
            
            // Additional Info & Images
            $this->db->prepare("DELETE FROM tenant_addinfo_images WHERE addinfo_id IN (SELECT tenant_info FROM tenant_addinfo WHERE tenant_id = ?)")->execute([$userId]);
            $this->db->prepare("DELETE FROM tenant_addinfo WHERE tenant_id = ?")->execute([$userId]);
            
            // Family
            $this->db->prepare("DELETE FROM tenant_family_members WHERE tenant_id = ?")->execute([$userId]);

            // 2. Finally delete the core account
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE tenant_id = ?");
            $res = $stmt->execute([$userId]);

            $this->db->commit();
            return $res;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log("Delete Account Error: " . $e->getMessage());
            return false;
        }
    }
}
