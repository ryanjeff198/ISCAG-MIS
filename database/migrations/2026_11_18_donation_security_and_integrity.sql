-- =========================================================================
-- ISCAG MIS Donation Security, Data Integrity & Audit Trail Migration
-- Date: November 18, 2026
-- Panel Recommendation: Strengthen database security, ensure data integrity,
-- establish proper access control and accountability across User, Staff, and MIS Admin.
-- =========================================================================

-- 1. Create Dedicated Immutable Donation Audit Trail Table
CREATE TABLE IF NOT EXISTS `donation_audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `donation_id` INT NOT NULL,
    `donation_ref` VARCHAR(50) NOT NULL,
    `action` VARCHAR(50) NOT NULL, -- 'SUBMISSION', 'VERIFICATION', 'REJECTION', 'CORRECTION', 'RECEIPT_GENERATED', 'ALLOCATION', 'DISTRIBUTION', 'REPORT_GENERATED'
    `actor_id` INT NULL,
    `actor_name` VARCHAR(255) NOT NULL,
    `actor_role` VARCHAR(50) NOT NULL, -- 'User/Donor', 'Staff_Damayan', 'Admin', 'System'
    `previous_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NULL,
    `previous_values` LONGTEXT NULL, -- JSON snapshot of previous fields
    `new_values` LONGTEXT NULL,      -- JSON snapshot of changed fields
    `reason` TEXT NULL,              -- Mandatory for rejections and corrections
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `integrity_hash` VARCHAR(64) NULL, -- SHA-256 seal of the audit log record
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_don_log_id` (`donation_id`),
    INDEX `idx_don_log_ref` (`donation_ref`),
    INDEX `idx_don_log_action` (`action`),
    INDEX `idx_don_log_actor` (`actor_id`, `actor_role`),
    INDEX `idx_don_log_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Data Cleaning: Convert blank string payment_references to NULL
UPDATE `charity_donations_records`
SET `payment_reference` = NULL
WHERE TRIM(`payment_reference`) = '' OR `payment_reference` IS NULL;

-- 3. Add Index for Fast Duplicate Detection on Payment Reference
ALTER TABLE `charity_donations_records`
ADD INDEX `idx_payment_ref` (`payment_reference`);

-- 4. Add Index on tenant_id for secure donor-scoped queries
ALTER TABLE `charity_donations_records`
ADD INDEX `idx_tenant_id` (`tenant_id`);

-- 5. Immutability Triggers on donation_audit_logs (Prevent UPDATE and DELETE)
DELIMITER $$

DROP TRIGGER IF EXISTS `trg_donation_audit_no_update`$$
CREATE TRIGGER `trg_donation_audit_no_update`
BEFORE UPDATE ON `donation_audit_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Security Policy Violation: Donation audit logs are strictly immutable and cannot be modified.';
END$$

DROP TRIGGER IF EXISTS `trg_donation_audit_no_delete`$$
CREATE TRIGGER `trg_donation_audit_no_delete`
BEFORE DELETE ON `donation_audit_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Security Policy Violation: Donation audit logs are strictly immutable and cannot be deleted.';
END$$

-- 6. Integrity Protection Trigger on charity_donations_records
-- Prevents deletion of verified or active donations (must be archived/cancelled with audit trail)
DROP TRIGGER IF EXISTS `trg_prevent_verified_donation_delete`$$
CREATE TRIGGER `trg_prevent_verified_donation_delete`
BEFORE DELETE ON `charity_donations_records`
FOR EACH ROW
BEGIN
    IF OLD.status IN ('Available', 'Partially Allocated', 'Fully Allocated', 'Verified') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Integrity Violation: Confirmed or verified donation records cannot be deleted. Corrections must be handled via documented audit trail.';
    END IF;
END$$

DELIMITER ;

-- 7. Legacy Data Reference Normalization
-- Ensures all verified legacy digital donations possess a documented reference tag
UPDATE `charity_donations_records` 
SET `payment_reference` = CONCAT('LEGACY-', UPPER(REPLACE(`payment_method`, ' ', '_')), '-', LPAD(`id`, 4, '0'))
WHERE `donation_category` = 'digital' AND (`payment_reference` IS NULL OR TRIM(`payment_reference`) = '') AND `status` IN ('Available', 'Partially Allocated', 'Fully Allocated');

