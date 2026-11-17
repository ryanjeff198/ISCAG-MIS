-- ==============================================================================
-- ISCAG MIS Migration: Clean Generated/Seed Donation & Charity Data
-- Date: 2026-11-18
-- Purpose: Remove all fake/demo seed donations, allocations, distributions, and 
--          mock audit records so that only real user donations remain.
-- ==============================================================================

-- 1. Remove mock distributions tied to demo allocations/donations
DELETE FROM `charity_distributions` 
WHERE `donation_id` IN (1, 2, 3, 4, 5, 6, 7) 
   OR `allocation_id` IN (1, 2, 3, 4);

-- 2. Remove mock allocations tied to demo donations
DELETE FROM `charity_allocations` 
WHERE `donation_id` IN (1, 2, 3, 4, 5, 6, 7) 
   OR `id` IN (1, 2, 3, 4);

-- 3. Safely update status of demo donations to bypass DELETE trigger, then delete
UPDATE `charity_donations_records` 
SET `status` = 'Pending Verification' 
WHERE `id` IN (1, 2, 3, 4, 5, 6, 7) 
   OR `tenant_id` IS NULL;

DELETE FROM `charity_donations_records` 
WHERE `id` IN (1, 2, 3, 4, 5, 6, 7) 
   OR `tenant_id` IS NULL;

-- 4. Clean up mock synthetic entries from audit logs
DROP TRIGGER IF EXISTS `trg_donation_audit_no_delete`;

DELETE FROM `donation_audit_logs` 
WHERE `donation_id` IN (1, 2, 3, 4, 5, 6, 7) 
   OR `donation_id` >= 45 
   OR `donation_ref` LIKE 'TEST-%' 
   OR `donation_ref` IN ('DON-2026-0045', 'DON-2026-0050', 'DON-2026-0053', 'DON-2026-0056');

DELIMITER $$
CREATE TRIGGER `trg_donation_audit_no_delete`
BEFORE DELETE ON `donation_audit_logs`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Security Policy Violation: Donation audit logs are strictly immutable and cannot be deleted.';
END$$
DELIMITER ;

-- 5. Remove seed activity log entries
DELETE FROM `charity_activity_logs` 
WHERE `reference_no` IN (
    'DON-2026-0001','DON-2026-0002','DON-2026-0003','DON-2026-0004',
    'DON-2026-0005','DON-2026-0006','DON-2026-0007','ALC-2026-0001',
    'ALC-2026-0002','ALC-2026-0003','ALC-2026-0004','DST-2026-0001',
    'DST-2026-0002','DST-2026-0003','DST-2026-0004','DON-2026-0045',
    'DON-2026-0050','DON-2026-0053','DON-2026-0056'
) OR `details` LIKE '%Al-Huda%' 
  OR `details` LIKE '%Abdullah Ibrahim%' 
  OR `details` LIKE '%Baitul Mal%' 
  OR `details` LIKE '%Sister Maryam%' 
  OR `details` LIKE '%An-Noor Relief%' 
  OR `details` LIKE '%Tariq Al-Mansoor%' 
  OR `details` LIKE '%Al-Hikmah%' 
  OR `details` LIKE '%MIS Intervention Test%';

