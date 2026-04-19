ALTER TABLE tenant_addinfo 
ADD COLUMN pob VARCHAR(100) AFTER birthdate,
ADD COLUMN age INT AFTER pob,
ADD COLUMN sex VARCHAR(20) AFTER age,
ADD COLUMN civil_status VARCHAR(50) AFTER sex,
ADD COLUMN monthly_income DECIMAL(10,2) AFTER occupation,
ADD COLUMN companyphone VARCHAR(50) AFTER companyadd,
ADD COLUMN ref_name VARCHAR(150) AFTER companyphone,
ADD COLUMN ref_contact VARCHAR(50) AFTER ref_name,
ADD COLUMN iscag_students INT DEFAULT 0 AFTER ref_contact,
ADD COLUMN date_applied DATE AFTER iscag_students,
ADD COLUMN family_data JSON AFTER date_applied;
