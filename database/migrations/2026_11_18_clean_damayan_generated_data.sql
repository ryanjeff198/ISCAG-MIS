-- Migration: Remove generated dummy and test records from Damayan module
-- Preserves only real registered user/tenant burial requests

DELETE FROM burial_activity_logs WHERE burial_request_id IN (54, 55, 56, 57, 58);
DELETE FROM deceased_records WHERE burial_request_id IN (54, 55, 56, 57, 58);
DELETE FROM burial_requests WHERE id IN (54, 55, 56, 57, 58) OR deceased_name LIKE '%Test%' OR deceased_name LIKE '%NonExistent%';

DELETE FROM funeral_case_logs;
DELETE FROM funeral_documents;
DELETE FROM funeral_psa_requests;
DELETE FROM funeral_cases;
