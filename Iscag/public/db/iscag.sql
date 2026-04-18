-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2026 at 06:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iscag`
--

-- --------------------------------------------------------

--
-- Table structure for table `apartmentsapp`
--

CREATE TABLE `apartmentsapp` (
  `application_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `roomtype` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apartments_info`
--

CREATE TABLE `apartments_info` (
  `apartment_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `roomnumber` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_issuance`
--

CREATE TABLE `certificate_issuance` (
  `issuance_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `certificate_type` varchar(100) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `requested_by` varchar(150) DEFAULT NULL,
  `released_by` varchar(150) DEFAULT NULL,
  `date_requested` date DEFAULT NULL,
  `date_released` date DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `charitydonations`
--

CREATE TABLE `charitydonations` (
  `donation_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `dfirst_name` varchar(100) DEFAULT NULL,
  `dlast_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `corpse_disposal`
--

CREATE TABLE `corpse_disposal` (
  `disposal_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `disposal_type` varchar(100) DEFAULT NULL,
  `burial_permit_number` varchar(100) DEFAULT NULL,
  `burial_permit_date` date DEFAULT NULL,
  `transfer_permit_number` varchar(100) DEFAULT NULL,
  `transfer_permit_date` date DEFAULT NULL,
  `cemetery_name` varchar(150) DEFAULT NULL,
  `cemetery_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `death_attendant`
--

CREATE TABLE `death_attendant` (
  `attendant_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `attendant_type` varchar(100) DEFAULT NULL,
  `attendance_from` date DEFAULT NULL,
  `attendance_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `death_birth_details`
--

CREATE TABLE `death_birth_details` (
  `birth_detail_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `mother_age` int(11) DEFAULT NULL,
  `delivery_method` varchar(100) DEFAULT NULL,
  `pregnancy_length_weeks` int(11) DEFAULT NULL,
  `birth_type` varchar(100) DEFAULT NULL,
  `birth_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `death_records`
--

CREATE TABLE `death_records` (
  `deceased_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `registry_no` varchar(100) DEFAULT NULL,
  `city_municipality` varchar(150) DEFAULT NULL,
  `dfirst_name` varchar(100) DEFAULT NULL,
  `dmiddle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `death_date` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `residence_address` text DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `mother_maiden_name` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `death_registration`
--

CREATE TABLE `death_registration` (
  `deathreg_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `prepared_by` varchar(150) DEFAULT NULL,
  `prepared_position` varchar(150) DEFAULT NULL,
  `prepared_date` date DEFAULT NULL,
  `received_by` varchar(150) DEFAULT NULL,
  `registered_by` varchar(150) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deceasedmedcert`
--

CREATE TABLE `deceasedmedcert` (
  `medcert_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `immediate_cause` text DEFAULT NULL,
  `antecedent_cause` text DEFAULT NULL,
  `underlying_cause` text DEFAULT NULL,
  `other_conditions` text DEFAULT NULL,
  `maternal_condition` text DEFAULT NULL,
  `autopsy_done` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delayed_registration_affidavit`
--

CREATE TABLE `delayed_registration_affidavit` (
  `affidavit_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `affiant_name` varchar(150) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `cause_of_death` text DEFAULT NULL,
  `delay_reason` text DEFAULT NULL,
  `burial_place` text DEFAULT NULL,
  `burial_date` date DEFAULT NULL,
  `affidavit_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `embalmer_certificate`
--

CREATE TABLE `embalmer_certificate` (
  `assistant_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `assistant_name` varchar(150) DEFAULT NULL,
  `license_no` varchar(100) DEFAULT NULL,
  `issued_on` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `external_death_details`
--

CREATE TABLE `external_death_details` (
  `external_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `manner_of_death` varchar(150) DEFAULT NULL,
  `place_of_occurrence` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_conversion`
--

CREATE TABLE `female_conversion` (
  `conversion_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `registry_no` varchar(100) DEFAULT NULL,
  `province` varchar(150) DEFAULT NULL,
  `city_municipality` varchar(150) DEFAULT NULL,
  `conversion_date` date DEFAULT NULL,
  `prepared_by` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_conversion_registration`
--

CREATE TABLE `female_conversion_registration` (
  `registration_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `prepared_by` varchar(150) DEFAULT NULL,
  `registered_by` varchar(150) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_conversion_witnesses`
--

CREATE TABLE `female_conversion_witnesses` (
  `witness_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_convert_parents`
--

CREATE TABLE `female_convert_parents` (
  `parent_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `father_religion` varchar(100) DEFAULT NULL,
  `conversion_date` date DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
  `mother_religion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_convert_person`
--

CREATE TABLE `female_convert_person` (
  `person_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `ffirst_name` varchar(100) DEFAULT NULL,
  `fmiddle_name` varchar(100) DEFAULT NULL,
  `flast_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(150) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `residence` text DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `former_religion` varchar(100) DEFAULT NULL,
  `adopted_muslim_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_relationship`
--

CREATE TABLE `female_relationship` (
  `relation_id` int(11) NOT NULL,
  `female_id` int(11) NOT NULL,
  `rfirst_name` varchar(100) DEFAULT NULL,
  `rlast_name` varchar(100) DEFAULT NULL,
  `rmiddle_name` varchar(100) DEFAULT NULL,
  `contactnum` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `parentname` varchar(150) DEFAULT NULL,
  `fsignature` varchar(255) DEFAULT NULL,
  `parent_signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_school`
--

CREATE TABLE `female_school` (
  `female_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `sfirst_name` varchar(100) DEFAULT NULL,
  `slast_name` varchar(100) DEFAULT NULL,
  `smiddle_name` varchar(100) DEFAULT NULL,
  `muslim_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contactnum` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `shahadah_date` date DEFAULT NULL,
  `revert` tinyint(1) DEFAULT NULL,
  `born_muslim` tinyint(1) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `infant_death_cause`
--

CREATE TABLE `infant_death_cause` (
  `infant_cause_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `main_disease` text DEFAULT NULL,
  `other_conditions` text DEFAULT NULL,
  `maternal_disease` text DEFAULT NULL,
  `other_maternal_condition` text DEFAULT NULL,
  `other_circumstances` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `informant`
--

CREATE TABLE `informant` (
  `informant_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `attendance_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `male_conversion`
--

CREATE TABLE `male_conversion` (
  `conversion_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `registry_no` varchar(100) DEFAULT NULL,
  `province` varchar(150) DEFAULT NULL,
  `city_municipality` varchar(150) DEFAULT NULL,
  `conversion_date` date DEFAULT NULL,
  `prepared_by` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `male_conversion_registration`
--

CREATE TABLE `male_conversion_registration` (
  `registration_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `prepared_by` varchar(150) DEFAULT NULL,
  `registered_by` varchar(150) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `male_conversion_witnesses`
--

CREATE TABLE `male_conversion_witnesses` (
  `witness_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `male_convert_parents`
--

CREATE TABLE `male_convert_parents` (
  `parent_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `father_religion` varchar(100) DEFAULT NULL,
  `conversion_date` date DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL,
  `mother_religion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `male_convert_person`
--

CREATE TABLE `male_convert_person` (
  `person_id` int(11) NOT NULL,
  `conversion_id` int(11) NOT NULL,
  `cfirst_name` varchar(100) DEFAULT NULL,
  `cmiddle_name` varchar(100) DEFAULT NULL,
  `clast_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(150) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `residence` text DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `former_religion` varchar(100) DEFAULT NULL,
  `adopted_muslim_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marriages`
--

CREATE TABLE `marriages` (
  `marriage_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `registry_no` varchar(100) DEFAULT NULL,
  `province` varchar(150) DEFAULT NULL,
  `city_municipality` varchar(150) DEFAULT NULL,
  `marriage_date` date DEFAULT NULL,
  `marriage_time` time DEFAULT NULL,
  `marriage_place` text DEFAULT NULL,
  `prepared_by` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marriage_officiant`
--

CREATE TABLE `marriage_officiant` (
  `officiant_id` int(11) NOT NULL,
  `marriage_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `position` varchar(150) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `marriage_time` time DEFAULT NULL,
  `registry_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marriage_parents`
--

CREATE TABLE `marriage_parents` (
  `parent_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `father_name` varchar(150) DEFAULT NULL,
  `mother_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marriage_partners`
--

CREATE TABLE `marriage_partners` (
  `partner_id` int(11) NOT NULL,
  `marriage_id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `mfirst_name` varchar(100) DEFAULT NULL,
  `mmiddle_name` varchar(100) DEFAULT NULL,
  `mlast_name` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(150) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `residence` text DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marriage_witness`
--

CREATE TABLE `marriage_witness` (
  `witness_id` int(11) NOT NULL,
  `marriage_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `assistedby` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postmortem_certificate`
--

CREATE TABLE `postmortem_certificate` (
  `postmortem_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `doctor_name` varchar(150) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `date_signed` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proofpayment`
--

CREATE TABLE `proofpayment` (
  `proof_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `receipt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `report_name` varchar(150) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `date_generated` timestamp NOT NULL DEFAULT current_timestamp(),
  `generated_by` varchar(150) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statementofacc`
--

CREATE TABLE `statementofacc` (
  `bill_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `room` decimal(10,2) DEFAULT NULL,
  `waterbill` decimal(10,2) DEFAULT NULL,
  `parking` decimal(10,2) DEFAULT NULL,
  `contribution` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_accounts`
--

CREATE TABLE `tenant_accounts` (
  `tenant_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `email` varchar(150) NOT NULL,
  `contactnum` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `confirmpass` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_accounts`
--

INSERT INTO `tenant_accounts` (`tenant_id`, `first_name`, `last_name`, `sex`, `email`, `contactnum`, `password`, `confirmpass`, `role`, `otp_code`, `otp_expiry`, `is_verified`) VALUES
(9, 'Ryan', 'Felizardo', 'Male', 'rjfelizardo25@gmail.com', '+639065740819', '$2y$10$jyT2fqtWINEpx8jxsPN5defW3HSLFjOwKS8u6F0b7WH8vySob9koK', '$2y$10$wk/mUDxGcHRcj0zscnaq2O4cpqONXZGdVtSsr/6OPxX39an/67yeW', 'Admin', '101730', '2026-03-29 14:06:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tenant_addfam`
--

CREATE TABLE `tenant_addfam` (
  `family_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `relation` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `numofkids` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_addinfo`
--

CREATE TABLE `tenant_addinfo` (
  `tenant_info` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `familyname` varchar(100) DEFAULT NULL,
  `givenname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `muslimname` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `tribalaffliation` varchar(100) DEFAULT NULL,
  `numofmuslim` int(11) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `companyname` varchar(150) DEFAULT NULL,
  `companyadd` text DEFAULT NULL,
  `dateofshahadah` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_parking`
--

CREATE TABLE `tenant_parking` (
  `parking_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `vehiclename` varchar(100) DEFAULT NULL,
  `ownername` varchar(150) DEFAULT NULL,
  `typeofvehicle` varchar(100) DEFAULT NULL,
  `plateno` varchar(50) DEFAULT NULL,
  `datestarted` date DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_requirements`
--

CREATE TABLE `tenant_requirements` (
  `requirement_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `governmentid` varchar(255) DEFAULT NULL,
  `psa` varchar(255) DEFAULT NULL,
  `nbi` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `proofofincome` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_apartmentsapp_tenant` (`tenant_id`);

--
-- Indexes for table `apartments_info`
--
ALTER TABLE `apartments_info`
  ADD PRIMARY KEY (`apartment_id`),
  ADD KEY `fk_apartments_info_app` (`application_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_audit_logs_tenant` (`tenant_id`);

--
-- Indexes for table `certificate_issuance`
--
ALTER TABLE `certificate_issuance`
  ADD PRIMARY KEY (`issuance_id`),
  ADD KEY `fk_certificate_issuance_tenant` (`tenant_id`);

--
-- Indexes for table `charitydonations`
--
ALTER TABLE `charitydonations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `fk_charitydonations_tenant` (`tenant_id`);

--
-- Indexes for table `corpse_disposal`
--
ALTER TABLE `corpse_disposal`
  ADD PRIMARY KEY (`disposal_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `death_attendant`
--
ALTER TABLE `death_attendant`
  ADD PRIMARY KEY (`attendant_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `death_birth_details`
--
ALTER TABLE `death_birth_details`
  ADD PRIMARY KEY (`birth_detail_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `death_records`
--
ALTER TABLE `death_records`
  ADD PRIMARY KEY (`deceased_id`),
  ADD KEY `fk_death_records_tenant` (`tenant_id`);

--
-- Indexes for table `death_registration`
--
ALTER TABLE `death_registration`
  ADD PRIMARY KEY (`deathreg_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `deceasedmedcert`
--
ALTER TABLE `deceasedmedcert`
  ADD PRIMARY KEY (`medcert_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `delayed_registration_affidavit`
--
ALTER TABLE `delayed_registration_affidavit`
  ADD PRIMARY KEY (`affidavit_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `embalmer_certificate`
--
ALTER TABLE `embalmer_certificate`
  ADD PRIMARY KEY (`assistant_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `external_death_details`
--
ALTER TABLE `external_death_details`
  ADD PRIMARY KEY (`external_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `female_conversion`
--
ALTER TABLE `female_conversion`
  ADD PRIMARY KEY (`conversion_id`),
  ADD KEY `fk_female_conversion_tenant` (`tenant_id`);

--
-- Indexes for table `female_conversion_registration`
--
ALTER TABLE `female_conversion_registration`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `female_conversion_witnesses`
--
ALTER TABLE `female_conversion_witnesses`
  ADD PRIMARY KEY (`witness_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `female_convert_parents`
--
ALTER TABLE `female_convert_parents`
  ADD PRIMARY KEY (`parent_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `female_convert_person`
--
ALTER TABLE `female_convert_person`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `female_relationship`
--
ALTER TABLE `female_relationship`
  ADD PRIMARY KEY (`relation_id`),
  ADD KEY `fk_female_relationship_female` (`female_id`);

--
-- Indexes for table `female_school`
--
ALTER TABLE `female_school`
  ADD PRIMARY KEY (`female_id`),
  ADD UNIQUE KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `infant_death_cause`
--
ALTER TABLE `infant_death_cause`
  ADD PRIMARY KEY (`infant_cause_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `informant`
--
ALTER TABLE `informant`
  ADD PRIMARY KEY (`informant_id`),
  ADD KEY `fk_informant_deceased` (`deceased_id`);

--
-- Indexes for table `male_conversion`
--
ALTER TABLE `male_conversion`
  ADD PRIMARY KEY (`conversion_id`),
  ADD KEY `fk_male_conversion_tenant` (`tenant_id`);

--
-- Indexes for table `male_conversion_registration`
--
ALTER TABLE `male_conversion_registration`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `male_conversion_witnesses`
--
ALTER TABLE `male_conversion_witnesses`
  ADD PRIMARY KEY (`witness_id`),
  ADD KEY `fk_male_conversion_witnesses_conversion` (`conversion_id`);

--
-- Indexes for table `male_convert_parents`
--
ALTER TABLE `male_convert_parents`
  ADD PRIMARY KEY (`parent_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `male_convert_person`
--
ALTER TABLE `male_convert_person`
  ADD PRIMARY KEY (`person_id`),
  ADD UNIQUE KEY `conversion_id` (`conversion_id`);

--
-- Indexes for table `marriages`
--
ALTER TABLE `marriages`
  ADD PRIMARY KEY (`marriage_id`),
  ADD KEY `fk_marriages_tenant` (`tenant_id`);

--
-- Indexes for table `marriage_officiant`
--
ALTER TABLE `marriage_officiant`
  ADD PRIMARY KEY (`officiant_id`),
  ADD UNIQUE KEY `marriage_id` (`marriage_id`);

--
-- Indexes for table `marriage_parents`
--
ALTER TABLE `marriage_parents`
  ADD PRIMARY KEY (`parent_id`),
  ADD UNIQUE KEY `partner_id` (`partner_id`);

--
-- Indexes for table `marriage_partners`
--
ALTER TABLE `marriage_partners`
  ADD PRIMARY KEY (`partner_id`),
  ADD KEY `fk_marriage_partners_marriage` (`marriage_id`);

--
-- Indexes for table `marriage_witness`
--
ALTER TABLE `marriage_witness`
  ADD PRIMARY KEY (`witness_id`),
  ADD KEY `fk_marriage_witness_marriage` (`marriage_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_tenant` (`tenant_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_tenant` (`tenant_id`);

--
-- Indexes for table `postmortem_certificate`
--
ALTER TABLE `postmortem_certificate`
  ADD PRIMARY KEY (`postmortem_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `proofpayment`
--
ALTER TABLE `proofpayment`
  ADD PRIMARY KEY (`proof_id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_reports_tenant` (`tenant_id`);

--
-- Indexes for table `statementofacc`
--
ALTER TABLE `statementofacc`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `fk_statementofacc_tenant` (`tenant_id`);

--
-- Indexes for table `tenant_accounts`
--
ALTER TABLE `tenant_accounts`
  ADD PRIMARY KEY (`tenant_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tenant_addfam`
--
ALTER TABLE `tenant_addfam`
  ADD PRIMARY KEY (`family_id`),
  ADD UNIQUE KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `tenant_addinfo`
--
ALTER TABLE `tenant_addinfo`
  ADD PRIMARY KEY (`tenant_info`),
  ADD UNIQUE KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  ADD PRIMARY KEY (`parking_id`),
  ADD KEY `fk_tenant_parking_tenant` (`tenant_id`);

--
-- Indexes for table `tenant_requirements`
--
ALTER TABLE `tenant_requirements`
  ADD PRIMARY KEY (`requirement_id`),
  ADD KEY `fk_tenant_requirements_tenant` (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apartments_info`
--
ALTER TABLE `apartments_info`
  MODIFY `apartment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_issuance`
--
ALTER TABLE `certificate_issuance`
  MODIFY `issuance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `charitydonations`
--
ALTER TABLE `charitydonations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `corpse_disposal`
--
ALTER TABLE `corpse_disposal`
  MODIFY `disposal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `death_attendant`
--
ALTER TABLE `death_attendant`
  MODIFY `attendant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `death_birth_details`
--
ALTER TABLE `death_birth_details`
  MODIFY `birth_detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `death_records`
--
ALTER TABLE `death_records`
  MODIFY `deceased_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `death_registration`
--
ALTER TABLE `death_registration`
  MODIFY `deathreg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deceasedmedcert`
--
ALTER TABLE `deceasedmedcert`
  MODIFY `medcert_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delayed_registration_affidavit`
--
ALTER TABLE `delayed_registration_affidavit`
  MODIFY `affidavit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `embalmer_certificate`
--
ALTER TABLE `embalmer_certificate`
  MODIFY `assistant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `external_death_details`
--
ALTER TABLE `external_death_details`
  MODIFY `external_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_conversion`
--
ALTER TABLE `female_conversion`
  MODIFY `conversion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_conversion_registration`
--
ALTER TABLE `female_conversion_registration`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_conversion_witnesses`
--
ALTER TABLE `female_conversion_witnesses`
  MODIFY `witness_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_convert_parents`
--
ALTER TABLE `female_convert_parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_convert_person`
--
ALTER TABLE `female_convert_person`
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_relationship`
--
ALTER TABLE `female_relationship`
  MODIFY `relation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `female_school`
--
ALTER TABLE `female_school`
  MODIFY `female_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `infant_death_cause`
--
ALTER TABLE `infant_death_cause`
  MODIFY `infant_cause_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `informant`
--
ALTER TABLE `informant`
  MODIFY `informant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `male_conversion`
--
ALTER TABLE `male_conversion`
  MODIFY `conversion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `male_conversion_registration`
--
ALTER TABLE `male_conversion_registration`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `male_conversion_witnesses`
--
ALTER TABLE `male_conversion_witnesses`
  MODIFY `witness_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `male_convert_parents`
--
ALTER TABLE `male_convert_parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `male_convert_person`
--
ALTER TABLE `male_convert_person`
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marriages`
--
ALTER TABLE `marriages`
  MODIFY `marriage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marriage_officiant`
--
ALTER TABLE `marriage_officiant`
  MODIFY `officiant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marriage_parents`
--
ALTER TABLE `marriage_parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marriage_partners`
--
ALTER TABLE `marriage_partners`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marriage_witness`
--
ALTER TABLE `marriage_witness`
  MODIFY `witness_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postmortem_certificate`
--
ALTER TABLE `postmortem_certificate`
  MODIFY `postmortem_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proofpayment`
--
ALTER TABLE `proofpayment`
  MODIFY `proof_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statementofacc`
--
ALTER TABLE `statementofacc`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_accounts`
--
ALTER TABLE `tenant_accounts`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tenant_addfam`
--
ALTER TABLE `tenant_addfam`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_addinfo`
--
ALTER TABLE `tenant_addinfo`
  MODIFY `tenant_info` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  MODIFY `parking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_requirements`
--
ALTER TABLE `tenant_requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  ADD CONSTRAINT `fk_apartmentsapp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `apartments_info`
--
ALTER TABLE `apartments_info`
  ADD CONSTRAINT `fk_apartments_info_app` FOREIGN KEY (`application_id`) REFERENCES `apartmentsapp` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `certificate_issuance`
--
ALTER TABLE `certificate_issuance`
  ADD CONSTRAINT `fk_certificate_issuance_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `charitydonations`
--
ALTER TABLE `charitydonations`
  ADD CONSTRAINT `fk_charitydonations_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `corpse_disposal`
--
ALTER TABLE `corpse_disposal`
  ADD CONSTRAINT `fk_corpse_disposal_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `death_attendant`
--
ALTER TABLE `death_attendant`
  ADD CONSTRAINT `fk_death_attendant_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `death_birth_details`
--
ALTER TABLE `death_birth_details`
  ADD CONSTRAINT `fk_death_birth_details_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `death_records`
--
ALTER TABLE `death_records`
  ADD CONSTRAINT `fk_death_records_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `death_registration`
--
ALTER TABLE `death_registration`
  ADD CONSTRAINT `fk_death_registration_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deceasedmedcert`
--
ALTER TABLE `deceasedmedcert`
  ADD CONSTRAINT `fk_deceasedmedcert_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `delayed_registration_affidavit`
--
ALTER TABLE `delayed_registration_affidavit`
  ADD CONSTRAINT `fk_delayed_reg_affidavit_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `embalmer_certificate`
--
ALTER TABLE `embalmer_certificate`
  ADD CONSTRAINT `fk_embalmer_certificate_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `external_death_details`
--
ALTER TABLE `external_death_details`
  ADD CONSTRAINT `fk_external_death_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_conversion`
--
ALTER TABLE `female_conversion`
  ADD CONSTRAINT `fk_female_conversion_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_conversion_registration`
--
ALTER TABLE `female_conversion_registration`
  ADD CONSTRAINT `fk_female_conversion_registration_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `female_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_conversion_witnesses`
--
ALTER TABLE `female_conversion_witnesses`
  ADD CONSTRAINT `fk_female_conversion_witnesses_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `female_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_convert_parents`
--
ALTER TABLE `female_convert_parents`
  ADD CONSTRAINT `fk_female_convert_parents_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `female_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_convert_person`
--
ALTER TABLE `female_convert_person`
  ADD CONSTRAINT `fk_female_convert_person_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `female_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_relationship`
--
ALTER TABLE `female_relationship`
  ADD CONSTRAINT `fk_female_relationship_female` FOREIGN KEY (`female_id`) REFERENCES `female_school` (`female_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `female_school`
--
ALTER TABLE `female_school`
  ADD CONSTRAINT `fk_female_school_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `infant_death_cause`
--
ALTER TABLE `infant_death_cause`
  ADD CONSTRAINT `fk_infant_death_cause_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `informant`
--
ALTER TABLE `informant`
  ADD CONSTRAINT `fk_informant_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `male_conversion`
--
ALTER TABLE `male_conversion`
  ADD CONSTRAINT `fk_male_conversion_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `male_conversion_registration`
--
ALTER TABLE `male_conversion_registration`
  ADD CONSTRAINT `fk_male_conversion_registration_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `male_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `male_conversion_witnesses`
--
ALTER TABLE `male_conversion_witnesses`
  ADD CONSTRAINT `fk_male_conversion_witnesses_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `male_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `male_convert_parents`
--
ALTER TABLE `male_convert_parents`
  ADD CONSTRAINT `fk_male_convert_parents_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `male_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `male_convert_person`
--
ALTER TABLE `male_convert_person`
  ADD CONSTRAINT `fk_male_convert_person_conversion` FOREIGN KEY (`conversion_id`) REFERENCES `male_conversion` (`conversion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marriages`
--
ALTER TABLE `marriages`
  ADD CONSTRAINT `fk_marriages_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marriage_officiant`
--
ALTER TABLE `marriage_officiant`
  ADD CONSTRAINT `fk_marriage_officiant_marriage` FOREIGN KEY (`marriage_id`) REFERENCES `marriages` (`marriage_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marriage_parents`
--
ALTER TABLE `marriage_parents`
  ADD CONSTRAINT `fk_marriage_parents_partner` FOREIGN KEY (`partner_id`) REFERENCES `marriage_partners` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marriage_partners`
--
ALTER TABLE `marriage_partners`
  ADD CONSTRAINT `fk_marriage_partners_marriage` FOREIGN KEY (`marriage_id`) REFERENCES `marriages` (`marriage_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marriage_witness`
--
ALTER TABLE `marriage_witness`
  ADD CONSTRAINT `fk_marriage_witness_marriage` FOREIGN KEY (`marriage_id`) REFERENCES `marriages` (`marriage_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `postmortem_certificate`
--
ALTER TABLE `postmortem_certificate`
  ADD CONSTRAINT `fk_postmortem_deceased` FOREIGN KEY (`deceased_id`) REFERENCES `death_records` (`deceased_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `proofpayment`
--
ALTER TABLE `proofpayment`
  ADD CONSTRAINT `fk_proofpayment_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `statementofacc`
--
ALTER TABLE `statementofacc`
  ADD CONSTRAINT `fk_statementofacc_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenant_addfam`
--
ALTER TABLE `tenant_addfam`
  ADD CONSTRAINT `fk_tenant_addfam_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenant_addinfo`
--
ALTER TABLE `tenant_addinfo`
  ADD CONSTRAINT `fk_tenant_addinfo_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  ADD CONSTRAINT `fk_tenant_parking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenant_requirements`
--
ALTER TABLE `tenant_requirements`
  ADD CONSTRAINT `fk_tenant_requirements_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
