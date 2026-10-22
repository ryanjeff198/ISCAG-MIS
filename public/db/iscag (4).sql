-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 04:09 PM
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
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'system',
  `actor_name` varchar(255) DEFAULT 'System',
  `actor_id` int(11) DEFAULT NULL,
  `source_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `title`, `message`, `type`, `actor_name`, `actor_id`, `source_url`, `is_read`, `created_at`) VALUES
(1, 'New Application Received', 'Art Denver has submitted a new apartment application.', 'request', 'Art Denver', 61, '/admin/apartment/confirmation', 0, '2026-10-07 12:20:45'),
(2, 'Parking Application Received', 'Art Denver has submitted a parking application for 1 vehicle(s).', 'request', 'Art Denver', 61, '/admin/mis_admin/parking_approval', 0, '2026-10-07 15:37:55'),
(3, 'New Burial Assistance Request #BR-20261015-993C', 'New request submitted by Ryan Felizardo for deceased Sulaiman Tariq Macarambon (Male).', 'burial', 'Ryan Felizardo', 1, '/admin/damayan/burial-requests', 0, '2026-10-15 14:07:58'),
(4, 'New Burial Assistance Request #BR-20261015-2334', 'New request submitted by Ryan Felizardo for deceased Sulaiman Tariq Macarambon (Male).', 'burial', 'Ryan Felizardo', 1, '/admin/damayan/burial-requests', 0, '2026-10-15 14:10:53'),
(5, 'Requirements Submitted: #BR-20261015-2334', 'Applicant Ryan Felizardo submitted the requested additional information for burial request #BR-20261015-2334.', 'burial', 'Ryan Felizardo', 1, '/admin/damayan/burial-requests', 0, '2026-10-15 14:10:53'),
(6, 'New Burial Assistance Request #BR-20261015-9BDC', 'New request submitted by Ryan Felizardo for deceased Sulaiman Tariq Macarambon (Male).', 'burial', 'Ryan Felizardo', 1, '/admin/damayan/burial-requests', 0, '2026-10-15 16:03:07'),
(7, 'Requirements Submitted: #BR-20261015-9BDC', 'Applicant Ryan Felizardo submitted the requested additional information for burial request #BR-20261015-9BDC.', 'burial', 'Ryan Felizardo', 1, '/admin/damayan/burial-requests', 0, '2026-10-15 16:03:07'),
(8, 'New Damayan Request #DC-CPY-20261016-0F9F', 'New Request Another Death Certificate Copy request submitted by Ryan Felizardo for deceased Marwan Test Al-Mansoor.', 'damayan', 'Ryan Felizardo', 60, '/ISCAG-MIS/admin/damayan/burial-requests', 0, '2026-10-16 09:43:53'),
(9, 'New Damayan Request #DC-CPY-20261016-7F38', 'New Request Another Death Certificate Copy request submitted by Art Denver for deceased Art Denver Dacer.', 'damayan', 'Art Denver', 61, '/ISCAG-MIS/admin/damayan/burial-requests', 0, '2026-10-16 09:52:31'),
(10, 'New Damayan Request #DC-CPY-20261016-C2DB', 'New Request Another Death Certificate Copy request submitted by Maria Dela Cruz for deceased Juan Dela Cruz.', 'damayan', 'Maria Dela Cruz', 61, '/ISCAG-MIS/admin/damayan/burial-requests', 0, '2026-10-16 12:38:40'),
(11, 'New Damayan Request #DC-REG-20261018-76DC', 'New Death Certificate & Cemetery Plot request submitted by Art Denver for deceased Art Denver Dacer.', 'damayan', 'Art Denver', 61, '/ISCAG-MIS/admin/damayan/burial-requests', 0, '2026-10-18 09:07:58'),
(12, 'New Food Donation Request (DON-2026-0010)', 'Test Brother Zayd requested coordination for 15 sacks of Rice Sacks (25kg Sinandomeng) & Canned Halal Corned Beef. Preferred Drop-off: 2026-10-21.', 'charity', 'Test Brother Zayd', 1, '/admin/damayan/charity/donations?search=DON-2026-0010', 0, '2026-10-18 14:03:28'),
(13, 'New Cash Donation Record (DON-2026-0011)', 'Sister Aminah Hassan submitted a cash donation record of ₱5,000.00 for Burial Assistance (Janazah Support).', 'charity', 'Sister Aminah Hassan', 1, '/admin/damayan/charity/donations?search=DON-2026-0011', 0, '2026-10-18 14:03:28'),
(14, 'New Digital Donation (DON-2026-0012) via GCash', 'Dr. Farooq Rahman submitted ₱2,500.00 via GCash (Ref: GCASH-99882211). Proof uploaded, awaiting verification.', 'charity', 'Dr. Farooq Rahman', 1, '/admin/damayan/charity/donations?search=DON-2026-0012', 0, '2026-10-18 14:03:28'),
(15, 'New Cash Donation Record (DON-2026-0010)', 'Ahmad Test Donor submitted a cash donation record of ₱1,000.00 for Food Donation.', 'charity', 'Ahmad Test Donor', 1, '/admin/damayan/charity/donations?search=DON-2026-0010', 0, '2026-10-18 14:44:46'),
(16, 'New Digital Donation (DON-2026-0014) via GCash', 'Fatima Test Donor submitted ₱2,500.00 via GCash (Ref: GCASH-MED-99881). Proof uploaded, awaiting verification.', 'charity', 'Fatima Test Donor', 1, '/admin/damayan/charity/donations?search=DON-2026-0014', 0, '2026-10-18 14:44:46'),
(17, 'New Cash Donation Record (DON-2026-0015)', 'Brother Omar Test submitted a cash donation record of ₱500.00 for School Supplies Donation.', 'charity', 'Brother Omar Test', 1, '/admin/damayan/charity/donations?search=DON-2026-0015', 0, '2026-10-18 14:44:46'),
(18, 'New Digital Donation (DON-2026-0016) via Maya', 'Sister Maryam Test submitted ₱1,500.00 via Maya (Ref: MAYA-BURIAL-7762). Proof uploaded, awaiting verification.', 'charity', 'Sister Maryam Test', 1, '/admin/damayan/charity/donations?search=DON-2026-0016', 0, '2026-10-18 14:44:46'),
(19, 'New Cash Donation Record (DON-2026-0010)', 'Art Denver submitted a cash donation record of ₱500.00 for Medical Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0010', 0, '2026-10-18 15:24:16'),
(20, 'New Digital Donation (DON-2026-0018) via Bank Transfer (BDO)', 'Test Real Account Donor submitted ₱500.00 via Bank Transfer (BDO) (Ref: BDO-REF-TEST-001). Proof uploaded, awaiting verification.', 'charity', 'Test Real Account Donor', 1, '/admin/damayan/charity/donations?search=DON-2026-0018', 0, '2026-10-18 15:32:35'),
(21, 'New Cash Donation Record (DON-2026-0018)', 'Ryan Jeff Test Donor submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Ryan Jeff Test Donor', 1, '/admin/damayan/charity/donations?search=DON-2026-0018', 0, '2026-10-18 15:55:46'),
(22, 'New Cash Donation Record (DON-2026-0018)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0018', 0, '2026-10-18 16:06:28'),
(23, 'New Cash Donation Record (DON-2026-0021)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0021', 0, '2026-10-18 16:06:29'),
(24, 'New Cash Donation Record (DON-2026-0022)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0022', 0, '2026-10-18 16:06:30'),
(25, 'New Cash Donation Record (DON-2026-0023)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0023', 1, '2026-10-18 16:06:31'),
(26, 'New Cash Donation Record (DON-2026-0024)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0024', 1, '2026-10-18 16:06:31'),
(27, 'New Cash Donation Record (DON-2026-0025)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0025', 1, '2026-10-18 16:06:31'),
(28, 'New Cash Donation Record (DON-2026-0026)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0026', 1, '2026-10-18 16:09:22'),
(29, 'New Cash Donation Record (DON-2026-0027)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0027', 1, '2026-10-18 16:09:23'),
(30, 'New Cash Donation Record (DON-2026-0028)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0028', 1, '2026-10-18 16:09:23'),
(31, 'New Cash Donation Record (DON-2026-0029)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0029', 1, '2026-10-18 16:13:46'),
(32, 'New Cash Donation Record (DON-2026-0030)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0030', 1, '2026-10-18 16:13:56'),
(33, 'New Cash Donation Record (DON-2026-0031)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0031', 1, '2026-10-18 16:13:56'),
(34, 'New Cash Donation Record (DON-2026-0032)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0032', 1, '2026-10-18 16:13:57'),
(35, 'New Cash Donation Record (DON-2026-0033)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0033', 1, '2026-10-18 16:14:07'),
(36, 'New Cash Donation Record (DON-2026-0034)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0034', 1, '2026-10-18 16:15:25'),
(37, 'New Cash Donation Record (DON-2026-0035)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0035', 1, '2026-10-18 16:17:14'),
(38, 'New Cash Donation Record (DON-2026-0036)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0036', 1, '2026-10-18 16:22:59'),
(39, 'New Digital Donation (DON-2026-0037) via GCash', 'Art Denver submitted ₱500.00 via GCash (Ref: asdasdasd). Proof uploaded, awaiting verification.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0037', 1, '2026-10-18 16:24:31'),
(40, 'New Cash Donation Record (DON-2026-0038)', 'Art Denver submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Art Denver', 61, '/admin/damayan/charity/donations?search=DON-2026-0038', 1, '2026-10-18 16:25:16'),
(41, 'New Cash Donation Record (DON-2026-0039)', 'Pamela Brown submitted a cash donation record of ₱500.00 for Food Donation.', 'charity', 'Pamela Brown', 65, '/admin/damayan/charity/donations?search=DON-2026-0039', 1, '2026-10-20 13:13:01');

-- --------------------------------------------------------

--
-- Table structure for table `apartmentsapp`
--

CREATE TABLE `apartmentsapp` (
  `application_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `roomtype` varchar(100) DEFAULT NULL,
  `lease_term` int(11) NOT NULL DEFAULT 12,
  `date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `unit_id` int(11) DEFAULT NULL COMMENT 'Assigned room from apartment_units',
  `queue_position` int(11) DEFAULT NULL COMMENT 'Position in waitlist (NULL = not queued)',
  `verified_at` datetime DEFAULT NULL COMMENT 'When documents were verified',
  `accepted_at` datetime DEFAULT NULL COMMENT 'When application was accepted',
  `assigned_at` datetime DEFAULT NULL COMMENT 'When room was assigned',
  `status_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartmentsapp`
--

INSERT INTO `apartmentsapp` (`application_id`, `tenant_id`, `roomtype`, `lease_term`, `date`, `duration`, `status`, `reject_reason`, `updated_at`, `unit_id`, `queue_position`, `verified_at`, `accepted_at`, `assigned_at`, `status_seen`) VALUES
(4, 27, 'One-Bedroom', 12, '2026-04-19', NULL, 'Assigned', NULL, '2026-04-25 05:11:08', 30, NULL, NULL, '2026-04-25 13:11:08', '2026-04-25 13:11:08', 0),
(5, 28, 'One-Bedroom', 12, '2026-04-19', NULL, 'Rejected', 'Other', '2026-04-19 18:29:59', NULL, NULL, NULL, NULL, NULL, 0),
(7, 32, 'Studio', 12, '2026-04-24', NULL, 'Assigned', NULL, '2026-04-25 17:43:13', 16, NULL, NULL, '2026-04-26 01:43:13', '2026-04-26 01:43:13', 0),
(11, 47, 'Studio', 12, '2026-04-27', NULL, 'Assigned', NULL, '2026-04-27 12:25:22', 42, NULL, NULL, '2026-04-27 20:20:20', '2026-04-27 20:20:20', 0),
(12, 49, 'Transient', 12, '2026-04-27', NULL, 'Assigned', NULL, '2026-04-27 12:25:22', 42, NULL, NULL, '2026-04-27 20:20:20', '2026-04-27 20:20:20', 0),
(22, 60, 'Transient', 12, '2026-05-01', NULL, 'Assigned', NULL, '2026-04-30 23:36:30', 71, NULL, NULL, '2026-05-01 07:36:30', '2026-05-01 07:36:30', 0),
(23, 61, 'Transient', 12, '2026-10-07', NULL, 'Assigned', NULL, '2026-10-07 14:49:44', 71, NULL, NULL, '2026-10-07 22:49:44', '2026-10-07 22:49:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `apartment_types`
--

CREATE TABLE `apartment_types` (
  `type_id` int(11) NOT NULL,
  `type_key` varchar(20) NOT NULL,
  `label` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacity` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `floor_area` varchar(20) DEFAULT NULL,
  `bedrooms` varchar(50) DEFAULT NULL,
  `bathroom` varchar(50) DEFAULT NULL,
  `kitchen` varchar(50) DEFAULT NULL,
  `parking` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `inclusions` text DEFAULT NULL,
  `rules` text DEFAULT NULL,
  `security_deposit` varchar(100) DEFAULT '1 Month',
  `advance_rent` varchar(100) DEFAULT '1 Month',
  `other_fees` varchar(255) DEFAULT NULL,
  `min_lease` varchar(100) DEFAULT '3 Months',
  `notice_period` varchar(100) DEFAULT '25th day',
  `queue_label` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartment_types`
--

INSERT INTO `apartment_types` (`type_id`, `type_key`, `label`, `price`, `capacity`, `description`, `floor_area`, `bedrooms`, `bathroom`, `kitchen`, `parking`, `is_active`, `sort_order`, `created_at`, `updated_at`, `inclusions`, `rules`, `security_deposit`, `advance_rent`, `other_fees`, `min_lease`, `notice_period`, `queue_label`) VALUES
(1, 'studio', 'Studio ', 4680.00, '1-2 pax', 'A compact and efficient studio-type living space ideal for individuals or couples seeking privacy and convenience. It features an open-plan layout that combines the sleeping, living, and dining areas into one well-designed space, along with a separate bathroom and a functional kitchenette. Perfect for short or long stays requiring simplicity, comfort, and practical living.', '', '1 (separate)', '1 ', 'Kitchenette', 'Shared lot', 1, 1, '2026-04-21 17:53:51', '2026-04-27 16:01:37', NULL, NULL, '1 Month', '1 Month', NULL, '3 Months', '25th day', NULL),
(2, '1br', 'One-Bedroom ', 6240.00, '2-3 pax', 'A comfortable one-bedroom apartment ideal for small families, couples, or Muslim guests who prefer a separate sleeping area and a private, respectful living space. It features a distinct living room, a private bedroom, a full bathroom, and a dining-kitchen area with ample counter space, suitable for short or long stays with comfort and privacy.', '', '1 (separate)', '1 (with shower)', 'Full kitchen', 'Shared lot', 1, 2, '2026-04-21 17:53:51', '2026-04-27 16:01:19', NULL, NULL, '1 Month', '1 Month', NULL, '3 Months', '25th day', NULL),
(3, '2br', 'Two-Bedroom ', 7000.00, '3-5 persons', 'A spacious two-bedroom apartment designed for small to growing families, couples, or Muslim guests seeking comfort and privacy. It includes a same size bedroom, a full living and dining area, a complete kitchen, and a bathroom. Ideal for families or guests looking for a peaceful and well-organized living space within the community housing complex.', '', '2 (separate)', '1 (with shower)', 'Full kitchen', 'Dedicated slot', 1, 3, '2026-04-21 17:53:51', '2026-04-27 16:01:47', NULL, NULL, '1 Month', '1 Month', NULL, '3 Months', '25th day', NULL),
(4, '1tr', 'Transient', 2500.00, '10 pax', 'A transient accommodation designed for short-term stays, typically accommodating 8–10 guests. It may consist of multiple bedrooms or shared sleeping areas, along with common facilities such as a living space, kitchen, and bathroom depending on the setup.\n\n1 month deposit', '', 'shared bedspace', '1 shared', NULL, NULL, 1, 0, '2026-04-21 18:54:28', '2026-04-28 14:09:50', NULL, NULL, '1 Month', '1 Month', NULL, '3 Months', '25th day', NULL),
(5, '1gr', 'Guest Room', 5000.00, '3-5 pax', 'A guest room accommodation designed for visiting guests, families, or travelers seeking a comfortable short-term stay. It is similar in layout to a two-bedroom unit, typically featuring a master bedroom, a second bedroom, a shared living area, a kitchen, and a bathroom. It is commonly used for Islamic visitors and families, providing a clean, private, and respectful space suitable for short stays.', '', '2 (separate)', '1', NULL, NULL, 1, 0, '2026-04-22 03:44:49', '2026-04-23 13:12:51', NULL, NULL, '1 Month', '1 Month', NULL, '3 Months', '25th day', NULL),
(6, '1bc', 'Bachelor', 2500.00, '1-2 pax', 'A compact bachelor-type unit designed for single occupants or couples seeking a simple and efficient living space. It features an open-plan layout that combines sleeping, living, and dining areas in one space, with a separate bathroom and a small kitchenette. Ideal for individuals who prefer a minimal, practical, and low-maintenance home for short or long stays.', '', '1', '1', NULL, NULL, 1, 0, '2026-04-23 12:56:11', '2026-04-23 13:16:26', NULL, NULL, '1 Month', '1 Month', NULL, '3 Months', '25th day', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `apartment_type_images`
--

CREATE TABLE `apartment_type_images` (
  `image_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `image_data` longblob DEFAULT NULL,
  `mime_type` varchar(50) DEFAULT 'image/jpeg',
  `caption` varchar(100) DEFAULT NULL,
  `is_thumbnail` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartment_type_images`
--

INSERT INTO `apartment_type_images` (`image_id`, `type_id`, `file_path`, `image_data`, `mime_type`, `caption`, `is_thumbnail`, `sort_order`, `created_at`) VALUES
(37, 3, 'uploads/apartments/type_37_1777301970.jpg', NULL, 'image/jpeg', '', 1, 1, '2026-04-23 13:01:01'),
(38, 3, 'uploads/apartments/type_38_1777301970.jpg', NULL, 'image/jpeg', '', 0, 2, '2026-04-23 13:01:04'),
(39, 2, 'uploads/apartments/type_39_1777301970.jpg', NULL, 'image/jpeg', '', 0, 1, '2026-04-24 12:45:55'),
(40, 2, 'uploads/apartments/type_40_1777301970.jpg', NULL, 'image/jpeg', '', 1, 2, '2026-04-24 12:45:59'),
(42, 2, 'uploads/apartments/type_42_1777301970.jpg', NULL, 'image/jpeg', '', 0, 3, '2026-04-24 12:46:15'),
(52, 4, 'uploads/apartments/type_4_1777304913.jpg', NULL, 'image/jpeg', '', 1, 1, '2026-04-27 15:48:33'),
(53, 4, 'uploads/apartments/type_4_1777304918.jpg', NULL, 'image/jpeg', '', 0, 2, '2026-04-27 15:48:38'),
(54, 4, 'uploads/apartments/type_4_1777304920.jpg', NULL, 'image/jpeg', '', 0, 3, '2026-04-27 15:48:40'),
(55, 4, 'uploads/apartments/type_4_1777304923.jpg', NULL, 'image/jpeg', '', 0, 4, '2026-04-27 15:48:43'),
(59, 6, 'uploads/apartments/type_6_1777305135.jpg', NULL, 'image/jpeg', '', 1, 1, '2026-04-27 15:52:15'),
(60, 6, 'uploads/apartments/type_6_1777305148.jpg', NULL, 'image/jpeg', '', 0, 2, '2026-04-27 15:52:28'),
(61, 6, 'uploads/apartments/type_6_1777305150.jpg', NULL, 'image/jpeg', '', 0, 3, '2026-04-27 15:52:30'),
(64, 1, 'uploads/apartments/type_1_1777305366.jpg', NULL, 'image/jpeg', '', 1, 1, '2026-04-27 15:56:06'),
(65, 1, 'uploads/apartments/type_1_1777305375.jpg', NULL, 'image/jpeg', '', 0, 2, '2026-04-27 15:56:15'),
(66, 1, 'uploads/apartments/type_1_1777305389.jpg', NULL, 'image/jpeg', '', 0, 3, '2026-04-27 15:56:29'),
(67, 1, 'uploads/apartments/type_1_1777305391.jpg', NULL, 'image/jpeg', '', 0, 4, '2026-04-27 15:56:31'),
(71, 5, 'uploads/apartments/type_5_1777305535.jpg', NULL, 'image/jpeg', '', 1, 1, '2026-04-27 15:58:55'),
(73, 5, 'uploads/apartments/type_5_1777305555.jpg', NULL, 'image/jpeg', '', 0, 3, '2026-04-27 15:59:15'),
(74, 5, 'uploads/apartments/type_5_1777305567.jpg', NULL, 'image/jpeg', '', 0, 4, '2026-04-27 15:59:27'),
(75, 5, 'uploads/apartments/type_5_1777305573.jpg', NULL, 'image/jpeg', '', 0, 5, '2026-04-27 15:59:33'),
(76, 5, 'uploads/apartments/type_5_1777305596.jpg', NULL, 'image/jpeg', '', 0, 6, '2026-04-27 15:59:56');

-- --------------------------------------------------------

--
-- Table structure for table `apartment_units`
--

CREATE TABLE `apartment_units` (
  `unit_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `building` varchar(30) DEFAULT NULL,
  `status` enum('Available','Occupied','Reserved','Maintenance','Inactive') DEFAULT 'Available',
  `application_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartment_units`
--

INSERT INTO `apartment_units` (`unit_id`, `type_id`, `room_number`, `building`, `status`, `application_id`, `tenant_id`, `description`, `created_at`, `updated_at`) VALUES
(12, 5, 'B1-01', 'Building 1', 'Occupied', NULL, NULL, 'Building 1, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-25 17:39:35'),
(13, 1, 'B1-02', 'Building 1', 'Occupied', 7, 32, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-24 11:22:34'),
(14, 5, 'B1-03', 'Building 1', 'Occupied', NULL, NULL, 'Building 1, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-29 15:02:57'),
(15, 5, 'B1-04', 'Building 1', 'Occupied', NULL, NULL, 'Building 1, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-29 15:07:52'),
(16, 1, 'B1-05', 'Building 1', 'Occupied', 7, 32, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-25 17:43:12'),
(17, 1, 'B1-06', 'Building 1', 'Occupied', 0, 0, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-25 18:02:31'),
(18, 1, 'B1-07', 'Building 1', 'Occupied', NULL, NULL, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-26 04:44:54'),
(19, 6, 'B1-08', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(20, 6, 'B1-09', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(21, 6, 'B1-10', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(22, 6, 'B1-11', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(23, 5, 'B1-12', 'Building 1', 'Occupied', NULL, NULL, 'Building 1, Floor 2 — Guest Room', '2026-04-23 13:34:56', '2026-04-29 15:58:48'),
(24, 5, 'B2-01', 'Building 2', 'Occupied', NULL, NULL, 'Building 2, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-29 17:53:11'),
(25, 3, 'B2-02', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(26, 1, 'B2-03', 'Building 2', 'Occupied', NULL, NULL, 'Building 2, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-26 11:20:17'),
(27, 5, 'B2-04', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(28, 3, 'B2-05', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(29, 6, 'B2-06', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(30, 2, 'B2-07', 'Building 2', 'Occupied', 4, 27, 'Building 2, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-25 05:11:08'),
(31, 3, 'B2-08', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(32, 6, 'B2-09', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(33, 3, 'B2-10', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(34, 5, 'B2-11', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(35, 6, 'B2-12', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(36, 1, 'B2-13', 'Building 2', 'Occupied', 0, 47, 'Building 2, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-04-27 11:17:29'),
(37, 1, 'B2-14', 'Building 2', 'Occupied', 0, 47, 'Building 2, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-04-27 12:04:46'),
(38, 6, 'B2-15', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(39, 2, 'B2-16', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(40, 5, 'B2-17', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(41, 5, 'B2-18', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(42, 1, 'B3-01', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-27 12:26:17'),
(43, 2, 'B3-02', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(44, 5, 'B3-03', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(45, 2, 'B3-04', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(46, 2, 'B3-05', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(47, 6, 'B3-06', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(48, 5, 'B3-07', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(49, 6, 'B3-08', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(50, 2, 'B3-09', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:04:03'),
(51, 2, 'B3-10', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(52, 3, 'B3-11', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(53, 5, 'B3-12', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:04:03'),
(54, 2, 'B3-13', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(55, 6, 'B3-14', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(56, 2, 'B3-15', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(57, 3, 'B3-16', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 2 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(58, 5, 'B3-17', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(59, 2, 'B3-18', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(60, 1, 'B3-19', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(61, 2, 'B3-20', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(62, 1, 'B3-21', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(63, 3, 'B3-22', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(64, 5, 'B3-23', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(65, 6, 'B3-24', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 3 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(66, 6, 'B3-25', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(67, 3, 'B3-26', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(68, 3, 'B3-27', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(69, 3, 'B3-28', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(70, 1, 'B3-29', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(71, 4, 'B3-30', 'Building 3', 'Occupied', 23, 61, 'Building 3, Floor 4 — Transient', '2026-04-23 13:34:56', '2026-10-07 14:49:44'),
(72, 2, 'B3-31', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(73, 5, 'B3-32', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(74, 1, 'B4-01', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(75, 1, 'B4-02', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(76, 1, 'B4-03', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(77, 1, 'B4-04', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(78, 1, 'B4-05', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(79, 3, 'B4-06', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(80, 5, 'B4-07', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(81, 5, 'B4-08', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(82, 2, 'B4-09', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(83, 6, 'B4-10', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(84, 2, 'B4-11', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(85, 5, 'B4-12', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(86, 3, 'B4-13', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(87, 1, 'B4-14', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(88, 4, 'B4-15', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(89, 1, 'B4-16', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(90, 5, 'B4-17', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(91, 6, 'B4-18', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(92, 1, 'B4-19', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(93, 5, 'B4-20', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(94, 6, 'B4-21', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(95, 1, 'B4-22', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:04:03'),
(96, 2, 'B4-23', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(97, 2, 'B4-24', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 3 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(98, 2, 'B4-25', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(99, 1, 'B4-26', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(100, 5, 'B4-27', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(101, 3, 'B4-28', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(102, 5, 'B4-29', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:04:03'),
(103, 5, 'B4-30', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(104, 1, 'B4-31', 'Building 4', 'Available', NULL, NULL, 'Building 4, Floor 4 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(105, 2, 'B5-01', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(106, 2, 'B5-02', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(107, 1, 'B5-03', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(108, 2, 'B5-04', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(109, 3, 'B5-05', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(110, 3, 'B5-06', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(111, 5, 'B5-07', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(112, 1, 'B5-08', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(113, 6, 'B5-09', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(114, 6, 'B5-10', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:04:03'),
(115, 2, 'B5-11', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(116, 2, 'B5-12', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(117, 2, 'B5-13', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(118, 4, 'B5-14', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(119, 6, 'B5-15', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(120, 1, 'B5-16', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(121, 5, 'B5-17', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(122, 5, 'B5-18', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(123, 3, 'B5-19', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(124, 5, 'B5-20', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(125, 6, 'B5-21', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(126, 1, 'B5-22', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(127, 3, 'B5-23', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(128, 6, 'B5-24', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 3 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(129, 3, 'B5-25', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(130, 1, 'B5-26', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Studio Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(131, 6, 'B5-27', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(132, 6, 'B5-28', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(133, 6, 'B5-29', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(134, 3, 'B5-30', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:04:03'),
(135, 6, 'B5-31', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(136, 3, 'B5-32', 'Building 5', 'Available', NULL, NULL, 'Building 5, Floor 4 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17');

--
-- Triggers `apartment_units`
--
DELIMITER $$
CREATE TRIGGER `trg_auto_vacate_room` BEFORE UPDATE ON `apartment_units` FOR EACH ROW BEGIN
            IF NEW.tenant_id IS NULL AND OLD.tenant_id IS NOT NULL THEN
                SET NEW.status = 'Available';
                SET NEW.application_id = NULL;
            END IF;
        END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `admin_id`, `admin_name`, `module`, `action`, `details`, `timestamp`) VALUES
(1, 59, 'Norman Ungasin', 'GOVERNANCE', 'TOGGLE_USER', 'Deactivated user account ID: 31', '2026-05-01 10:39:57'),
(2, 59, 'Norman Ungasin', 'GOVERNANCE', 'TOGGLE_USER', 'Activated user account ID: 31', '2026-05-01 10:40:07'),
(3, 59, 'Norman Ungasin', 'GOVERNANCE', 'TOGGLE_USER', 'Deactivated user account ID: 31', '2026-05-01 10:42:50'),
(4, 59, 'Norman Ungasin', 'GOVERNANCE', 'TOGGLE_USER', 'Activated user account ID: 31', '2026-05-01 10:42:56'),
(5, 59, 'Norman Ungasin', 'GOVERNANCE', 'TOGGLE_USER', 'Deactivated user account ID: 31', '2026-05-01 10:45:13'),
(6, 59, 'Norman Ungasin', 'GOVERNANCE', 'TOGGLE_USER', 'Activated user account ID: 31', '2026-05-01 10:45:16'),
(7, 59, 'Norman Ungasin', 'BROADCAST', 'SEND_NOTIFICATION', 'Sent \'Maintenance\' to 1 users (APARTMENT)', '2026-05-01 15:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `billing_id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Paid','Pending','Overdue') DEFAULT 'Pending',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`billing_id`, `tenant_id`, `amount`, `status`, `due_date`, `created_at`) VALUES
(1, 1, 3500.00, 'Paid', '2026-04-01', '2026-04-27 19:25:20'),
(2, 2, 7500.00, 'Pending', '2026-05-01', '2026-04-27 19:25:20'),
(3, 3, 5000.00, 'Overdue', '2026-03-15', '2026-04-27 19:25:20'),
(4, 1, 3500.00, 'Pending', '2026-05-01', '2026-04-27 19:25:20'),
(5, 2, 4000.00, 'Paid', '2026-03-01', '2026-04-27 19:25:20'),
(6, 3, 6000.00, 'Paid', '2026-02-01', '2026-04-27 19:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `broadcasts`
--

CREATE TABLE `broadcasts` (
  `broadcast_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target_group` varchar(50) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `broadcasts`
--

INSERT INTO `broadcasts` (`broadcast_id`, `title`, `message`, `target_group`, `sender_id`, `type`, `created_at`) VALUES
(1, 'Maintenance', 'Hello', 'APARTMENT', 59, 'system', '2026-05-01 15:16:53'),
(2, 'ADASD', 'ASDADSAD', 'ALL', 62, 'payment', '2026-10-21 16:09:27');

-- --------------------------------------------------------

--
-- Table structure for table `burial_activity_logs`
--

CREATE TABLE `burial_activity_logs` (
  `id` int(11) NOT NULL,
  `burial_request_id` int(11) NOT NULL,
  `ref_id` varchar(50) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `actor_name` varchar(200) DEFAULT 'Staff Admin',
  `actor_role` varchar(100) DEFAULT 'Staff_Damayan',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `burial_activity_logs`
--

INSERT INTO `burial_activity_logs` (`id`, `burial_request_id`, `ref_id`, `activity_type`, `title`, `details`, `actor_name`, `actor_role`, `created_at`) VALUES
(105, 38, 'DC-CPY-20261016-060F', 'encoded_link', 'Linked to Encoded Death Certificate', 'Automatically recognized and linked to Encoded Death Certificate #DC-LINK-TEST-001 (Deceased: Ahmad Ibn Tariq)', 'System', 'System', '2026-10-16 20:56:42'),
(106, 38, 'DC-CPY-20261016-060F', 'Submission', 'Application Submitted: Request Another Death Certificate Copy', 'Copy request filed for Ahmad Ibn Tariq. Reason: Official records archive. Copies: 2. Existing Cert #: DC-LINK-TEST-001. Linked to existing registered deceased record (#).', 'Fatima Tariq', 'Applicant', '2026-10-16 20:56:42'),
(107, 39, 'DC-CPY-20261016-F757', 'Submission', 'Application Submitted: Request Another Death Certificate Copy', 'Copy request filed for Zaid Al-Harith. Reason: Insurance claim. Copies: 1. Existing Cert #: DC-LINK-TEST-002. Existing deceased record unverified (requires staff verification).', 'Layla Al-Harith', 'Applicant', '2026-10-16 20:56:42'),
(108, 39, 'DC-CPY-20261016-F757', 'encoded_link', 'Linked to Encoded Death Certificate', 'Automatically recognized and linked to Encoded Death Certificate #DC-LINK-TEST-002 (Deceased: Zaid Al-Harith, Representative: Layla Al-Harith) by Staff Admin', 'Staff Admin', 'Staff_Damayan', '2026-10-16 20:56:42'),
(112, 40, 'DC-CPY-20261016-7699', 'encoded_link', 'Linked to Encoded Death Certificate', 'Automatically recognized and linked to Encoded Death Certificate #DC-LINK-TEST-001 (Deceased: Ahmad Ibn Tariq)', 'System', 'System', '2026-10-16 21:06:32'),
(113, 40, 'DC-CPY-20261016-7699', 'Submission', 'Application Submitted: Request Another Death Certificate Copy', 'Copy request filed for Ahmad Ibn Tariq. Reason: Official records archive. Copies: 2. Existing Cert #: DC-LINK-TEST-001. Linked to existing registered deceased record (#).', 'Fatima Tariq', 'Applicant', '2026-10-16 21:06:32'),
(114, 41, 'DC-CPY-20261016-1BE2', 'Submission', 'Application Submitted: Request Another Death Certificate Copy', 'Copy request filed for Zaid Al-Harith. Reason: Insurance claim. Copies: 1. Existing Cert #: DC-LINK-TEST-002. Existing deceased record unverified (requires staff verification).', 'Layla Al-Harith', 'Applicant', '2026-10-16 21:06:32'),
(115, 41, 'DC-CPY-20261016-1BE2', 'encoded_link', 'Linked to Encoded Death Certificate', 'Automatically recognized and linked to Encoded Death Certificate #DC-LINK-TEST-002 (Deceased: Zaid Al-Harith, Representative: Layla Al-Harith) by Staff Admin', 'Staff Admin', 'Staff_Damayan', '2026-10-16 21:06:32'),
(166, 45, 'DC-REG-20261018-76DC', 'Submission', 'Application Submitted: Death Certificate & Cemetery Plot', 'New registration filed by Art Denver for deceased Art Denver Dacer (Male).', 'Art Denver', 'Applicant', '2026-10-18 17:07:58'),
(167, 45, 'DC-REG-20261018-76DC', 'Duplicate Review', 'Duplicate Review: Confirmed Different Individual', 'Decision: Confirmed Different Individual.', 'Ethan Santos', 'Staff_Damayan', '2026-10-18 17:52:56'),
(168, 45, 'DC-REG-20261018-76DC', 'Status Update', 'Request Accepted', 'Request #DC-REG-20261018-76DC was approved and accepted for processing by Ethan Santos after successful certificate verification.', 'Ethan Santos', 'Staff_Damayan', '2026-10-18 17:53:08'),
(169, 45, 'DC-REG-20261018-76DC', 'Certificate Received', 'Death Certificate Received', 'Death certificate was marked as received on Oct 22, 2026 by Ethan Santos.', 'Ethan Santos', 'Staff_Damayan', '2026-10-22 03:13:20'),
(170, 45, 'DC-REG-20261018-76DC', 'Duplicate Review', 'Duplicate Review: Duplicate Request', 'Decision: Duplicate Request.', 'Ethan Santos', 'Staff_Damayan', '2026-10-22 03:13:56'),
(171, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: October 30, 2026 10:00 AM', 'Release claiming schedule confirmed for October 30, 2026 at 10:00 AM at Damayan Department by Damayan Chief Admin — Instructions: Please bring 2 valid government IDs and official receipt.', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:49:43'),
(172, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: November 5, 2026 2:30 PM', 'Release claiming schedule confirmed for November 5, 2026 at 2:30 PM at Damayan Department by Damayan Chief Admin — Instructions: Revised instructions: claim at window 2 — Reason: Coordinator emergency meeting', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:49:46'),
(173, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Schedule Cancelled', 'Release claiming schedule cancelled by Damayan Chief Admin — Reason: Hospital medical record re-examination required', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:49:50'),
(174, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: November 10, 2026 9:00 AM', 'Release claiming schedule confirmed for November 10, 2026 at 9:00 AM at Damayan Department by Damayan Chief Admin — Instructions: Claim at main desk', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:49:54'),
(175, 45, 'DC-REG-20261018-76DC', 'Release', 'Certificate Released', 'Certificate Released — Claimed by Fatima Salih (Daughter), processed by Damayan Chief Admin on October 22, 2026 9:49 AM (Notes: Verified with PhilID #9876-5432-1098)', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:49:57'),
(176, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: October 30, 2026 10:00 AM', 'Release claiming schedule confirmed for October 30, 2026 at 10:00 AM at Damayan Department by Damayan Chief Admin — Instructions: Please bring 2 valid government IDs and official receipt.', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:52:59'),
(177, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: November 5, 2026 2:30 PM', 'Release claiming schedule confirmed for November 5, 2026 at 2:30 PM at Damayan Department by Damayan Chief Admin — Instructions: Revised instructions: claim at window 2 — Reason: Coordinator emergency meeting', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:53:02'),
(178, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Schedule Cancelled', 'Release claiming schedule cancelled by Damayan Chief Admin — Reason: Hospital medical record re-examination required', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:53:06'),
(179, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: November 10, 2026 9:00 AM', 'Release claiming schedule confirmed for November 10, 2026 at 9:00 AM at Damayan Department by Damayan Chief Admin — Instructions: Claim at main desk', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:53:09'),
(180, 45, 'DC-REG-20261018-76DC', 'Release', 'Certificate Released', 'Certificate Released — Claimed by Fatima Salih (Daughter), processed by Damayan Chief Admin on October 22, 2026 9:53 AM (Notes: Verified with PhilID #9876-5432-1098)', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 15:53:13'),
(181, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Received in Department', 'Death certificate marked as received and in Damayan Department custody by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:22:09'),
(182, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Received in Department', 'Death certificate marked as received and in Damayan Department custody by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:25:00'),
(183, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: October 25, 2026 2:30 PM', 'Release claiming schedule confirmed for October 25, 2026 at 2:30 PM at Damayan Department by Test Admin — Instructions: Please bring valid ID', 'Test Admin', 'Staff_Damayan', '2026-10-22 17:25:00'),
(184, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Custody Reverted', 'Certificate custody status reverted by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:25:04'),
(185, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Received in Department', 'Death certificate marked as received and in Damayan Department custody by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:29:52'),
(186, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: October 25, 2026 2:30 PM', 'Release claiming schedule confirmed for October 25, 2026 at 2:30 PM at Damayan Department by Test Admin — Instructions: Please bring valid ID', 'Test Admin', 'Staff_Damayan', '2026-10-22 17:29:52'),
(187, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Custody Reverted', 'Certificate custody status reverted by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:29:56'),
(188, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Received in Department', 'Death certificate marked as received and in Damayan Department custody by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:31:52'),
(189, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: October 25, 2026 2:30 PM', 'Release claiming schedule confirmed for October 25, 2026 at 2:30 PM at Damayan Department by Test Admin — Instructions: Please bring valid ID', 'Test Admin', 'Staff_Damayan', '2026-10-22 17:31:52'),
(190, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Custody Reverted', 'Certificate custody status reverted by Admin Tester.', 'Admin Tester', 'Staff_Damayan', '2026-10-22 17:31:56'),
(191, 46, 'DC-CPY-20261022-929A', 'encoded_link', 'Linked to Encoded Death Certificate', 'Automatically recognized and linked to Encoded Death Certificate #2023-2-0001113 (Deceased: Juan Dela Cruz)', 'System', 'System', '2026-10-22 17:33:24'),
(192, 46, 'DC-CPY-20261022-929A', 'Submission', 'Application Submitted: Request Another Death Certificate Copy', 'Copy request filed for Juan Dela Cruz. Reason: Certified true copy for legal proceedings. Copies: 2. Existing Cert #: 2023-2-0001113. Linked to existing registered deceased record (#).', 'Test Relative', 'Applicant', '2026-10-22 17:33:24'),
(193, 45, 'DC-REG-20261018-76DC', 'Custody', 'Certificate Received in Department', 'Death certificate marked as received and in Damayan Department custody by Damayan Chief Admin.', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 17:58:51'),
(194, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: October 30, 2026 10:00 AM', 'Release claiming schedule confirmed for October 30, 2026 at 10:00 AM at Damayan Department by Damayan Chief Admin — Instructions: Please bring 2 valid government IDs and official receipt.', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 17:58:51'),
(195, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: November 5, 2026 2:30 PM', 'Release claiming schedule confirmed for November 5, 2026 at 2:30 PM at Damayan Department by Damayan Chief Admin — Instructions: Revised instructions: claim at window 2 — Reason: Coordinator emergency meeting', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 17:58:55'),
(196, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Schedule Cancelled', 'Release claiming schedule cancelled by Damayan Chief Admin — Reason: Hospital medical record re-examination required', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 17:58:58'),
(197, 45, 'DC-REG-20261018-76DC', 'Schedule', 'Release Scheduled: November 10, 2026 9:00 AM', 'Release claiming schedule confirmed for November 10, 2026 at 9:00 AM at Damayan Department by Damayan Chief Admin — Instructions: Claim at main desk', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 17:59:02'),
(198, 45, 'DC-REG-20261018-76DC', 'Release', 'Certificate Released', 'Certificate Released — Claimed by Fatima Salih (Daughter), processed by Damayan Chief Admin on October 22, 2026 11:59 AM (Notes: Verified with PhilID #9876-5432-1098)', 'Damayan Chief Admin', 'Staff_Damayan', '2026-10-22 17:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `burial_coordinations`
--

CREATE TABLE `burial_coordinations` (
  `id` int(11) NOT NULL,
  `burial_request_id` int(11) NOT NULL,
  `ghusl_status` enum('Pending','Scheduled','Volunteers Confirmed','In Progress','Completed') DEFAULT 'Pending',
  `ghusl_date` date DEFAULT NULL,
  `ghusl_time` time DEFAULT NULL,
  `ghusl_venue` varchar(255) DEFAULT 'ISCAG Ghusl Facility',
  `ghusl_volunteer_gender` enum('Male','Female') DEFAULT 'Male',
  `ghusl_assigned_volunteers` text DEFAULT NULL,
  `ghusl_volunteer_status` varchar(50) DEFAULT 'Pending',
  `ghusl_completed_at` datetime DEFAULT NULL,
  `ghusl_remarks` text DEFAULT NULL,
  `kafan_status` enum('Pending','In Preparation','Ready','Completed') DEFAULT 'Pending',
  `kafan_type` varchar(100) DEFAULT 'Standard 3-piece shroud',
  `kafan_prepared_by` varchar(200) DEFAULT NULL,
  `kafan_completed_at` datetime DEFAULT NULL,
  `kafan_remarks` text DEFAULT NULL,
  `janazah_status` enum('Pending','Scheduled','Completed') DEFAULT 'Pending',
  `janazah_date` date DEFAULT NULL,
  `janazah_time` time DEFAULT NULL,
  `janazah_prayer_time` varchar(100) DEFAULT 'After Dhuhr',
  `janazah_imam_name` varchar(200) DEFAULT NULL,
  `janazah_imam_contact` varchar(50) DEFAULT NULL,
  `janazah_masjid` varchar(255) DEFAULT 'ISCAG Main Masjid',
  `janazah_remarks` text DEFAULT NULL,
  `janazah_completed_at` datetime DEFAULT NULL,
  `transport_status` enum('Pending','Scheduled','En Route','Arrived','Completed') DEFAULT 'Pending',
  `transport_vehicle_type` varchar(100) DEFAULT 'ISCAG Ambulance',
  `transport_driver_name` varchar(200) DEFAULT NULL,
  `transport_driver_contact` varchar(50) DEFAULT NULL,
  `transport_pickup_location` varchar(255) DEFAULT NULL,
  `transport_destination` varchar(255) DEFAULT NULL,
  `transport_scheduled_datetime` datetime DEFAULT NULL,
  `transport_actual_departure` datetime DEFAULT NULL,
  `transport_actual_arrival` datetime DEFAULT NULL,
  `transport_remarks` text DEFAULT NULL,
  `transport_completed_at` datetime DEFAULT NULL,
  `cemetery_status` enum('Pending','Plot Reserved','Scheduled','Completed') DEFAULT 'Pending',
  `cemetery_name` varchar(255) DEFAULT 'ISCAG Designated Muslim Cemetery',
  `cemetery_plot_number` varchar(100) DEFAULT NULL,
  `cemetery_scheduled_date` date DEFAULT NULL,
  `cemetery_scheduled_time` time DEFAULT NULL,
  `actual_burial_date` datetime DEFAULT NULL,
  `cemetery_remarks` text DEFAULT NULL,
  `burial_completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `burial_requests`
--

CREATE TABLE `burial_requests` (
  `id` int(11) NOT NULL,
  `ref_id` varchar(50) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `deceased_id` int(11) DEFAULT NULL,
  `encoded_cert_id` int(11) DEFAULT NULL,
  `deceased_name` varchar(255) NOT NULL,
  `deceased_first_name` varchar(100) DEFAULT NULL,
  `deceased_middle_name` varchar(100) DEFAULT NULL,
  `deceased_last_name` varchar(100) DEFAULT NULL,
  `deceased_sex` enum('Male','Female') DEFAULT 'Male',
  `date_of_birth` date DEFAULT NULL,
  `date_of_death` date NOT NULL,
  `place_of_death` varchar(255) DEFAULT NULL,
  `residence` varchar(255) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT 'Filipino',
  `religion` varchar(100) DEFAULT 'Islam',
  `relationship` varchar(100) DEFAULT NULL,
  `informant_name` varchar(200) DEFAULT NULL,
  `informant_contact` varchar(50) DEFAULT NULL,
  `informant_email` varchar(200) DEFAULT NULL,
  `informant_address` text DEFAULT NULL,
  `requested_services` text DEFAULT NULL,
  `cemetery_preference` varchar(255) DEFAULT NULL,
  `target_timing` varchar(255) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `assigned_staff_id` int(11) DEFAULT NULL,
  `assigned_staff_name` varchar(200) DEFAULT NULL,
  `info_request_notes` text DEFAULT NULL,
  `info_requested_at` datetime DEFAULT NULL,
  `additional_info_submitted` text DEFAULT NULL,
  `additional_doc_path` varchar(255) DEFAULT NULL,
  `info_submitted_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `actual_burial_date` datetime DEFAULT NULL,
  `final_cemetery` varchar(255) DEFAULT NULL,
  `final_remarks` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` varchar(200) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `death_cert_requested` tinyint(1) DEFAULT 1,
  `death_cert_status` varchar(100) DEFAULT 'Request Received',
  `certificate_in_department` tinyint(1) NOT NULL DEFAULT 0,
  `certificate_received_dept_at` datetime DEFAULT NULL,
  `certificate_received_dept_by` varchar(150) DEFAULT NULL,
  `certificate_custody_notes` text DEFAULT NULL,
  `death_cert_prep_date` date DEFAULT NULL,
  `death_cert_certified_date` date DEFAULT NULL,
  `death_cert_submitted_lcro_date` date DEFAULT NULL,
  `death_cert_registered_date` date DEFAULT NULL,
  `death_cert_registry_no` varchar(100) DEFAULT NULL,
  `death_cert_lcro_ref` varchar(100) DEFAULT NULL,
  `death_cert_certified_copy_ref` varchar(100) DEFAULT NULL,
  `death_cert_responsible_personnel` varchar(200) DEFAULT NULL,
  `death_cert_remarks` text DEFAULT NULL,
  `death_cert_rejection_reason` text DEFAULT NULL,
  `cemetery_plot_requested` tinyint(1) DEFAULT 1,
  `cemetery_plot_status` enum('Pending Review','Under Review','Available for Assignment','Approved','Plot Assigned','Awaiting Plot Availability','Unavailable','Rejected') DEFAULT 'Pending Review',
  `cemetery_plot_id` int(11) DEFAULT NULL,
  `assigned_cemetery_name` varchar(255) DEFAULT NULL,
  `assigned_plot_number` varchar(100) DEFAULT NULL,
  `assigned_section` varchar(100) DEFAULT NULL,
  `plot_assigned_date` datetime DEFAULT NULL,
  `cemetery_plot_remarks` text DEFAULT NULL,
  `cemetery_plot_rejection_reason` text DEFAULT NULL,
  `time_of_death` time DEFAULT NULL,
  `request_type` enum('both','death_certificate','cemetery_plot') DEFAULT 'both',
  `cert_action_type` varchar(50) DEFAULT 'register',
  `existing_cert_no` varchar(100) DEFAULT NULL,
  `copy_reason` text DEFAULT NULL,
  `num_copies` int(11) DEFAULT 1,
  `existing_record_verified` tinyint(1) DEFAULT 0,
  `copy_issued_at` datetime DEFAULT NULL,
  `copy_received_at` datetime DEFAULT NULL,
  `copy_received_confirmed` tinyint(1) DEFAULT 0,
  `certificate_available_at` datetime DEFAULT NULL,
  `collection_remarks` text DEFAULT NULL,
  `availability_scheduled_at` datetime DEFAULT NULL,
  `availability_scheduled_by` varchar(200) DEFAULT NULL,
  `duplicate_check_status` varchar(50) DEFAULT 'unreviewed',
  `duplicate_match_details` text DEFAULT NULL,
  `duplicate_reviewed_by` varchar(200) DEFAULT NULL,
  `duplicate_reviewed_at` datetime DEFAULT NULL,
  `received_by_user_at` datetime DEFAULT NULL,
  `verification_result` varchar(50) DEFAULT 'Pending',
  `verification_remarks` text DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` varchar(200) DEFAULT NULL,
  `rejection_reason_category` varchar(255) DEFAULT NULL,
  `rejection_specific_reason` text DEFAULT NULL,
  `duplicate_review_decision` varchar(50) DEFAULT NULL,
  `duplicate_review_notes` text DEFAULT NULL,
  `rejected_by` varchar(200) DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `archived_by` varchar(200) DEFAULT NULL,
  `scheduled_release_date` date DEFAULT NULL,
  `scheduled_release_time` time DEFAULT NULL,
  `release_location` varchar(255) DEFAULT 'Damayan Department',
  `release_instructions` text DEFAULT NULL,
  `release_scheduled_by_id` int(11) DEFAULT NULL,
  `release_scheduled_by_name` varchar(200) DEFAULT NULL,
  `release_scheduled_at` datetime DEFAULT NULL,
  `certificate_released_at` datetime DEFAULT NULL,
  `certificate_released_by` varchar(200) DEFAULT NULL,
  `certificate_recipient_name` varchar(200) DEFAULT NULL,
  `release_notes` text DEFAULT NULL,
  `release_cancelled_at` datetime DEFAULT NULL,
  `release_cancelled_by` varchar(200) DEFAULT NULL,
  `release_cancellation_reason` text DEFAULT NULL,
  `reschedule_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `burial_requests`
--

INSERT INTO `burial_requests` (`id`, `ref_id`, `tenant_id`, `deceased_id`, `encoded_cert_id`, `deceased_name`, `deceased_first_name`, `deceased_middle_name`, `deceased_last_name`, `deceased_sex`, `date_of_birth`, `date_of_death`, `place_of_death`, `residence`, `civil_status`, `nationality`, `religion`, `relationship`, `informant_name`, `informant_contact`, `informant_email`, `informant_address`, `requested_services`, `cemetery_preference`, `target_timing`, `additional_notes`, `supporting_document`, `assigned_staff_id`, `assigned_staff_name`, `info_request_notes`, `info_requested_at`, `additional_info_submitted`, `additional_doc_path`, `info_submitted_at`, `rejection_reason`, `actual_burial_date`, `final_cemetery`, `final_remarks`, `completed_at`, `completed_by`, `status`, `submitted_at`, `updated_at`, `death_cert_requested`, `death_cert_status`, `certificate_in_department`, `certificate_received_dept_at`, `certificate_received_dept_by`, `certificate_custody_notes`, `death_cert_prep_date`, `death_cert_certified_date`, `death_cert_submitted_lcro_date`, `death_cert_registered_date`, `death_cert_registry_no`, `death_cert_lcro_ref`, `death_cert_certified_copy_ref`, `death_cert_responsible_personnel`, `death_cert_remarks`, `death_cert_rejection_reason`, `cemetery_plot_requested`, `cemetery_plot_status`, `cemetery_plot_id`, `assigned_cemetery_name`, `assigned_plot_number`, `assigned_section`, `plot_assigned_date`, `cemetery_plot_remarks`, `cemetery_plot_rejection_reason`, `time_of_death`, `request_type`, `cert_action_type`, `existing_cert_no`, `copy_reason`, `num_copies`, `existing_record_verified`, `copy_issued_at`, `copy_received_at`, `copy_received_confirmed`, `certificate_available_at`, `collection_remarks`, `availability_scheduled_at`, `availability_scheduled_by`, `duplicate_check_status`, `duplicate_match_details`, `duplicate_reviewed_by`, `duplicate_reviewed_at`, `received_by_user_at`, `verification_result`, `verification_remarks`, `verified_at`, `verified_by`, `rejection_reason_category`, `rejection_specific_reason`, `duplicate_review_decision`, `duplicate_review_notes`, `rejected_by`, `rejected_at`, `is_archived`, `archived_at`, `archived_by`, `scheduled_release_date`, `scheduled_release_time`, `release_location`, `release_instructions`, `release_scheduled_by_id`, `release_scheduled_by_name`, `release_scheduled_at`, `certificate_released_at`, `certificate_released_by`, `certificate_recipient_name`, `release_notes`, `release_cancelled_at`, `release_cancelled_by`, `release_cancellation_reason`, `reschedule_reason`) VALUES
(45, 'DC-REG-20261018-76DC', 61, 28, NULL, 'Art Denver Dacer', 'Art', 'Denver', 'Dacer', 'Male', '2026-10-14', '2026-10-18', 'Dasmariñas', 'Blk 78 Lot 3, Brgy. San Juan', 'Married', 'Filipino', 'Islam', 'Relative', 'Art Denver', '+639485269632', 'dacer2314@gmail.com', 'Blk 78 Lot 3, Brgy. San Juan', NULL, 'ISCAG Designated Muslim Cemetery', NULL, '', NULL, 63, 'Ethan Santos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Completed', '2026-10-18 17:07:58', '2026-10-22 17:59:06', 1, 'Completed', 1, '2026-10-22 17:22:09', 'Damayan Chief Admin', 'Physical certificate in department custody', NULL, NULL, NULL, '2026-10-22', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Available for Assignment', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'both', 'register', NULL, NULL, 1, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'unreviewed', NULL, 'Ethan Santos', '2026-10-22 03:13:56', NULL, 'Matched', NULL, NULL, NULL, NULL, NULL, 'Duplicate Request', '', NULL, NULL, 0, NULL, NULL, '2026-11-10', '09:00:00', 'Damayan Department', 'Claim at main desk', 1, 'Damayan Chief Admin', '2026-10-22 17:59:02', '2026-10-22 17:59:06', 'Damayan Chief Admin', 'Fatima Salih (Daughter)', 'Verified with PhilID #9876-5432-1098', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `burial_schedule_history`
--

CREATE TABLE `burial_schedule_history` (
  `id` int(11) NOT NULL,
  `burial_request_id` int(11) NOT NULL,
  `ref_id` varchar(50) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `previous_date` date DEFAULT NULL,
  `previous_time` time DEFAULT NULL,
  `new_date` date DEFAULT NULL,
  `new_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT 'Damayan Department',
  `instructions` text DEFAULT NULL,
  `change_reason` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `admin_name` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `burial_schedule_history`
--

INSERT INTO `burial_schedule_history` (`id`, `burial_request_id`, `ref_id`, `action_type`, `previous_date`, `previous_time`, `new_date`, `new_time`, `location`, `instructions`, `change_reason`, `admin_id`, `admin_name`, `created_at`) VALUES
(1, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-10-30', '10:00:00', 'Damayan Department', 'Please bring 2 valid government IDs and official receipt.', 'Initial schedule confirmed', 1, 'Damayan Chief Admin', '2026-10-22 15:49:43'),
(2, 45, 'DC-REG-20261018-76DC', 'RESCHEDULED', '2026-10-30', '10:00:00', '2026-11-05', '14:30:00', 'Damayan Department', 'Revised instructions: claim at window 2', 'Coordinator emergency meeting', 1, 'Damayan Chief Admin', '2026-10-22 15:49:46'),
(3, 45, 'DC-REG-20261018-76DC', 'CANCELLED', '2026-11-05', '14:30:00', NULL, NULL, 'Damayan Department', NULL, 'Hospital medical record re-examination required', 1, 'Damayan Chief Admin', '2026-10-22 15:49:50'),
(4, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-11-10', '09:00:00', 'Damayan Department', 'Claim at main desk', 'Initial schedule confirmed', 1, 'Damayan Chief Admin', '2026-10-22 15:49:54'),
(5, 45, 'DC-REG-20261018-76DC', 'RELEASED', '2026-11-10', '09:00:00', NULL, NULL, 'Damayan Department', 'Released to Fatima Salih (Daughter)', 'Verified with PhilID #9876-5432-1098', 1, 'Damayan Chief Admin', '2026-10-22 15:49:57'),
(6, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-10-30', '10:00:00', 'Damayan Department', 'Please bring 2 valid government IDs and official receipt.', 'Initial schedule confirmed', 1, 'Damayan Chief Admin', '2026-10-22 15:52:59'),
(7, 45, 'DC-REG-20261018-76DC', 'RESCHEDULED', '2026-10-30', '10:00:00', '2026-11-05', '14:30:00', 'Damayan Department', 'Revised instructions: claim at window 2', 'Coordinator emergency meeting', 1, 'Damayan Chief Admin', '2026-10-22 15:53:02'),
(8, 45, 'DC-REG-20261018-76DC', 'CANCELLED', '2026-11-05', '14:30:00', NULL, NULL, 'Damayan Department', NULL, 'Hospital medical record re-examination required', 1, 'Damayan Chief Admin', '2026-10-22 15:53:06'),
(9, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-11-10', '09:00:00', 'Damayan Department', 'Claim at main desk', 'Initial schedule confirmed', 1, 'Damayan Chief Admin', '2026-10-22 15:53:09'),
(10, 45, 'DC-REG-20261018-76DC', 'RELEASED', '2026-11-10', '09:00:00', NULL, NULL, 'Damayan Department', 'Released to Fatima Salih (Daughter)', 'Verified with PhilID #9876-5432-1098', 1, 'Damayan Chief Admin', '2026-10-22 15:53:13'),
(11, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-10-25', '14:30:00', 'Damayan Department', 'Please bring valid ID', 'Initial schedule confirmed', 1, 'Test Admin', '2026-10-22 17:25:00'),
(12, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-10-25', '14:30:00', 'Damayan Department', 'Please bring valid ID', 'Initial schedule confirmed', 1, 'Test Admin', '2026-10-22 17:29:52'),
(13, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-10-25', '14:30:00', 'Damayan Department', 'Please bring valid ID', 'Initial schedule confirmed', 1, 'Test Admin', '2026-10-22 17:31:52'),
(14, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-10-30', '10:00:00', 'Damayan Department', 'Please bring 2 valid government IDs and official receipt.', 'Initial schedule confirmed', 1, 'Damayan Chief Admin', '2026-10-22 17:58:51'),
(15, 45, 'DC-REG-20261018-76DC', 'RESCHEDULED', '2026-10-30', '10:00:00', '2026-11-05', '14:30:00', 'Damayan Department', 'Revised instructions: claim at window 2', 'Coordinator emergency meeting', 1, 'Damayan Chief Admin', '2026-10-22 17:58:55'),
(16, 45, 'DC-REG-20261018-76DC', 'CANCELLED', '2026-11-05', '14:30:00', NULL, NULL, 'Damayan Department', NULL, 'Hospital medical record re-examination required', 1, 'Damayan Chief Admin', '2026-10-22 17:58:58'),
(17, 45, 'DC-REG-20261018-76DC', 'SCHEDULED', NULL, NULL, '2026-11-10', '09:00:00', 'Damayan Department', 'Claim at main desk', 'Initial schedule confirmed', 1, 'Damayan Chief Admin', '2026-10-22 17:59:02'),
(18, 45, 'DC-REG-20261018-76DC', 'RELEASED', '2026-11-10', '09:00:00', NULL, NULL, 'Damayan Department', 'Released to Fatima Salih (Daughter)', 'Verified with PhilID #9876-5432-1098', 1, 'Damayan Chief Admin', '2026-10-22 17:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `burial_volunteers`
--

CREATE TABLE `burial_volunteers` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `role` varchar(100) DEFAULT 'Ghusl Volunteer',
  `availability_status` enum('Available','Busy','Unavailable') DEFAULT 'Available',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `burial_volunteers`
--

INSERT INTO `burial_volunteers` (`id`, `name`, `gender`, `phone`, `email`, `role`, `availability_status`, `created_at`) VALUES
(1, 'Tariq Al-Hassan', 'Male', '0917-111-2233', 'tariq.h@gmail.com', 'Lead Ghusl Washer (Brother)', 'Available', '2026-10-15 21:15:03'),
(2, 'Rashid Omar', 'Male', '0918-222-3344', 'rashid.o@gmail.com', 'Ghusl Assistant (Brother)', 'Available', '2026-10-15 21:15:03'),
(3, 'Bilal Ibrahim', 'Male', '0919-333-4455', 'bilal.i@gmail.com', 'Ghusl Assistant (Brother)', 'Available', '2026-10-15 21:15:03'),
(4, 'Farooq Ahmad', 'Male', '0920-444-5566', 'farooq.a@gmail.com', 'Ghusl Assistant (Brother)', 'Available', '2026-10-15 21:15:03'),
(5, 'Maryam Binti Yusuf', 'Female', '0921-555-6677', 'maryam.y@gmail.com', 'Lead Ghusl Washer (Sister)', 'Available', '2026-10-15 21:15:03'),
(6, 'Aminah Al-Zahra', 'Female', '0922-666-7788', 'aminah.z@gmail.com', 'Ghusl Assistant (Sister)', 'Available', '2026-10-15 21:15:03'),
(7, 'Fatima Noor', 'Female', '0923-777-8899', 'fatima.n@gmail.com', 'Ghusl Assistant (Sister)', 'Available', '2026-10-15 21:15:03'),
(8, 'Zaynab Abdullah', 'Female', '0924-888-9900', 'zaynab.a@gmail.com', 'Ghusl Assistant (Sister)', 'Available', '2026-10-15 21:15:03');

-- --------------------------------------------------------

--
-- Table structure for table `cemetery_plots`
--

CREATE TABLE `cemetery_plots` (
  `id` int(11) NOT NULL,
  `cemetery_name` varchar(255) NOT NULL DEFAULT 'ISCAG Designated Muslim Cemetery',
  `section` varchar(100) NOT NULL DEFAULT 'Section A',
  `plot_number` varchar(100) NOT NULL,
  `status` enum('Available','Reserved','Occupied','Unavailable') DEFAULT 'Available',
  `assigned_deceased_name` varchar(255) DEFAULT NULL,
  `assigned_burial_request_id` int(11) DEFAULT NULL,
  `assigned_tenant_id` int(11) DEFAULT NULL,
  `assigned_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cemetery_plots`
--

INSERT INTO `cemetery_plots` (`id`, `cemetery_name`, `section`, `plot_number`, `status`, `assigned_deceased_name`, `assigned_burial_request_id`, `assigned_tenant_id`, `assigned_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-01', 'Occupied', NULL, NULL, NULL, NULL, 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(2, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-02', 'Occupied', NULL, NULL, NULL, NULL, 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(3, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-03', '', 'Tariq Kareem Al-Husseini', 7, 1, '2026-10-16 01:44:36', 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-18 03:32:41'),
(4, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-04', 'Available', 'Tariq Kareem Al-Husseini', 9, 1, '2026-10-16 01:49:26', 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-17 00:33:36'),
(5, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-05', 'Available', 'Tariq Kareem Al-Husseini', 12, 1, '2026-10-16 02:14:22', 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-17 00:33:36'),
(6, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-06', 'Available', 'Tariq Kareem Al-Husseini', 15, 1, '2026-10-16 02:15:32', 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-17 00:33:36'),
(7, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-07', 'Available', 'Tariq Kareem Al-Husseini', 18, 1, '2026-10-16 02:16:10', 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-17 00:33:36'),
(8, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-08', 'Available', NULL, NULL, NULL, NULL, 'Verified array OK', '2026-10-16 01:17:50', '2026-10-16 18:33:27'),
(9, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-09', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(10, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-10', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(11, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-11', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(12, 'ISCAG Designated Muslim Cemetery', 'Section A', 'A-12', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation (facing Qiblah)', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(13, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-01', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(14, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-02', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(15, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-03', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(16, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-04', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(17, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-05', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(18, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-06', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(19, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-07', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(20, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-08', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(21, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-09', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(22, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-10', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-18 03:32:25'),
(23, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-11', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(24, 'ISCAG Designated Muslim Cemetery', 'Section B', 'B-12', 'Available', NULL, NULL, NULL, NULL, 'Sunnah orientation', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(25, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-01', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(26, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-02', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(27, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-03', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(28, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-04', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(29, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-05', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(30, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-06', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(31, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-07', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(32, 'Norzagaray Muslim Cemetery, Bulacan', 'Block 1', 'N1-08', 'Available', NULL, NULL, NULL, NULL, 'Caretaker Brother Hassan', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(33, 'Taguig Muslim Cemetery', 'Area 1', 'T1-01', 'Available', NULL, NULL, NULL, NULL, 'Taguig Community Muslim Section', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(34, 'Taguig Muslim Cemetery', 'Area 1', 'T1-02', 'Available', NULL, NULL, NULL, NULL, 'Taguig Community Muslim Section', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(35, 'Taguig Muslim Cemetery', 'Area 1', 'T1-03', 'Available', NULL, NULL, NULL, NULL, 'Taguig Community Muslim Section', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(36, 'Taguig Muslim Cemetery', 'Area 1', 'T1-04', 'Available', NULL, NULL, NULL, NULL, 'Taguig Community Muslim Section', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(37, 'Taguig Muslim Cemetery', 'Area 1', 'T1-05', 'Available', NULL, NULL, NULL, NULL, 'Taguig Community Muslim Section', '2026-10-16 01:17:50', '2026-10-16 01:17:50'),
(38, 'Taguig Muslim Cemetery', 'Area 1', 'T1-06', 'Available', NULL, NULL, NULL, NULL, 'Taguig Community Muslim Section', '2026-10-16 01:17:50', '2026-10-16 01:17:50');

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
-- Table structure for table `charity_activity_logs`
--

CREATE TABLE `charity_activity_logs` (
  `id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `performed_by` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charity_activity_logs`
--

INSERT INTO `charity_activity_logs` (`id`, `action_type`, `reference_no`, `details`, `performed_by`, `user_id`, `ip_address`, `created_at`) VALUES
(1, 'ALLOCATION_CONFIRMED', 'ALC-2026-0001', 'Allocated ₱20,000.00 to Damayan for \'Burial Assistance for Indigent Families\' from DON-2026-0001', 'Admin Staff', NULL, '127.0.0.1', '2026-10-17 20:52:43'),
(2, 'DISTRIBUTION_COMPLETED', 'DST-2026-0001', 'Distributed ₱12,000.00 Cash Aid to Family of Late Brother Omar (Department: Damayan, Allocation: ALC-2026-0001)', 'Admin Staff', NULL, '127.0.0.1', '2026-10-17 20:52:43'),
(3, 'ALLOCATION_CONFIRMED', 'ALC-2026-0002', 'Allocated 20 units to Dawah for \'Da\'wah Outreach and New Muslim Welfare Kits\' from DON-2026-0003', 'Admin Staff', NULL, '127.0.0.1', '2026-10-17 20:52:43'),
(4, 'DISTRIBUTION_COMPLETED', 'DST-2026-0002', 'Distributed 15x Sacks of Premium Rice (25kg) to Dawah Outreach Committee (Ustadz Farhan) (Department: Dawah, Allocation: ALC-2026-0002)', 'Admin Staff', NULL, '127.0.0.1', '2026-10-17 20:52:43'),
(5, 'DONATION_RECORDED', 'DON-2026-0006', 'Recorded monetary donation of ₱40,000.00 from Bro. Tariq Al-Mansoor', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(6, 'DONATION_RECORDED', 'DON-2026-0007', 'Recorded in-kind donation of 50x Winter Blanket & Emergency Relief Packs from Al-Hikmah Relief Group', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(7, 'ALLOCATION_CONFIRMED', 'ALC-2026-0003', 'Allocated ₱25,000.00 to Damayan for \'Burial Assistance Funding\' from DON-2026-0006', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(8, 'DISTRIBUTION_COMPLETED', 'DST-2026-0003', 'Distributed ₱15,000.00 Cash Aid to Family of Brother Kareem (Department: Damayan, Allocation: ALC-2026-0003)', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(9, 'DISTRIBUTION_COMPLETED', 'DST-2026-0004', 'Distributed ₱10,000.00 Cash Aid to Family of Sister Aminah (Department: Damayan, Allocation: ALC-2026-0003)', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(10, 'ALLOCATION_CONFIRMED', 'ALC-2026-0004', 'Allocated 20 units to Dawah for \'Outreach kits for new Muslims\' from DON-2026-0007', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(11, 'ALLOCATION_CANCELLED', 'ALC-2026-0004', 'Cancelled allocation: Outreach program postponed', 'Staff Tester', NULL, '127.0.0.1', '2026-10-17 21:03:06'),
(12, 'ACK_ISSUED', 'DON-2026-0006', 'Issued donation acknowledgment receipt', 'Staff Tester', 1, '127.0.0.1', '2026-10-17 21:03:06'),
(13, 'DONATION_RECORDED', 'DON-2026-0008', 'Recorded monetary donation of ₱10,000.00 from Art Denver', 'Art Denver', 61, '::1', '2026-10-18 11:22:16'),
(14, 'DONATION_RECORDED', 'DON-2026-0009', 'Recorded monetary donation of ₱500.00 from Art Denver', 'Art Denver', 61, '::1', '2026-10-18 11:24:35'),
(15, 'DONATION_RECORDED', 'DON-2026-0010', 'Recorded food (in-kind) donation of 15 sacks Rice Sacks (25kg Sinandomeng) & Canned Halal Corned Beef from Test Brother Zayd [Status: Pending Coordination]', 'Online Portal (Pending Staff Verification)', 1, '127.0.0.1', '2026-10-18 14:03:28'),
(16, 'DONATION_RECORDED', 'DON-2026-0011', 'Recorded cash (monetary) donation of ₱5,000.00 from Sister Aminah Hassan [Status: Pending Receipt]', 'Online Portal (Pending Staff Verification)', 1, '127.0.0.1', '2026-10-18 14:03:28'),
(17, 'DONATION_RECORDED', 'DON-2026-0012', 'Recorded digital (monetary) donation of ₱2,500.00 from Dr. Farooq Rahman [Status: Pending Verification]', 'Online Portal (Pending Staff Verification)', 1, '127.0.0.1', '2026-10-18 14:03:28'),
(18, 'DONATION_VERIFIED', 'DON-2026-0012', 'Updated donation DON-2026-0012 status to Available. Admin notes: Verified GCash Ref GCASH-99882211.', 'Damayan Staff', 1, '127.0.0.1', '2026-10-18 14:03:58'),
(19, 'DONATION_RECORDED', 'DON-2026-0010', 'Recorded cash (monetary) donation of ₱1,000.00 from Ahmad Test Donor [Status: Pending Receipt]', 'Ahmad Test Donor', 1, '127.0.0.1', '2026-10-18 14:44:46'),
(20, 'DONATION_RECORDED', 'DON-2026-0014', 'Recorded digital (monetary) donation of ₱2,500.00 from Fatima Test Donor [Status: Pending Verification]', 'Fatima Test Donor', 1, '127.0.0.1', '2026-10-18 14:44:46'),
(21, 'DONATION_RECORDED', 'DON-2026-0015', 'Recorded cash (monetary) donation of ₱500.00 from Brother Omar Test [Status: Pending Receipt]', 'Brother Omar Test', 1, '127.0.0.1', '2026-10-18 14:44:46'),
(22, 'DONATION_RECORDED', 'DON-2026-0016', 'Recorded digital (monetary) donation of ₱1,500.00 from Sister Maryam Test [Status: Pending Verification]', 'Sister Maryam Test', 1, '127.0.0.1', '2026-10-18 14:44:46'),
(23, 'DONATION_RECORDED', 'DON-2026-0010', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 15:24:16'),
(24, 'DONATION_RECORDED', 'DON-2026-0018', 'Recorded digital (monetary) donation of ₱500.00 from Test Real Account Donor [Status: Pending Verification]', 'Test System', 1, '127.0.0.1', '2026-10-18 15:32:35'),
(25, 'DONATION_RECORDED', 'DON-2026-0018', 'Recorded cash (monetary) donation of ₱500.00 from Ryan Jeff Test Donor [Status: Pending Receipt]', 'Tenant Donor', 1, '127.0.0.1', '2026-10-18 15:55:46'),
(26, 'DONATION_RECORDED', 'DON-2026-0018', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:06:28'),
(27, 'DONATION_RECORDED', 'DON-2026-0021', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:06:29'),
(28, 'DONATION_RECORDED', 'DON-2026-0022', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:06:30'),
(29, 'DONATION_RECORDED', 'DON-2026-0023', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:06:31'),
(30, 'DONATION_RECORDED', 'DON-2026-0024', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:06:31'),
(31, 'DONATION_RECORDED', 'DON-2026-0025', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:06:31'),
(32, 'DONATION_RECORDED', 'DON-2026-0026', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:09:22'),
(33, 'DONATION_RECORDED', 'DON-2026-0027', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:09:23'),
(34, 'DONATION_RECORDED', 'DON-2026-0028', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:09:23'),
(35, 'DONATION_RECORDED', 'DON-2026-0029', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:13:46'),
(36, 'DONATION_RECORDED', 'DON-2026-0030', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:13:56'),
(37, 'DONATION_RECORDED', 'DON-2026-0031', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:13:56'),
(38, 'DONATION_RECORDED', 'DON-2026-0032', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:13:57'),
(39, 'DONATION_RECORDED', 'DON-2026-0033', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:14:07'),
(40, 'DONATION_RECORDED', 'DON-2026-0034', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:15:25'),
(41, 'DONATION_RECORDED', 'DON-2026-0035', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:17:14'),
(42, 'DONATION_RECORDED', 'DON-2026-0036', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:22:59'),
(43, 'DONATION_RECORDED', 'DON-2026-0037', 'Recorded digital (monetary) donation of ₱500.00 from Art Denver [Status: Pending Verification]', 'Art Denver', 61, '::1', '2026-10-18 16:24:31'),
(44, 'DONATION_RECORDED', 'DON-2026-0038', 'Recorded cash (monetary) donation of ₱500.00 from Art Denver [Status: Pending Receipt]', 'Art Denver', 61, '::1', '2026-10-18 16:25:16'),
(45, 'DONATION_RECORDED', 'DON-2026-0039', 'Recorded cash (monetary) donation of ₱500.00 from Pamela Brown [Status: Pending Receipt]', 'Pamela Brown', 65, '::1', '2026-10-20 13:13:01'),
(46, 'DONATION_VERIFIED', 'DON-2026-0039', 'Updated donation DON-2026-0039 status to Pending Receipt. Admin notes: ', 'Ethan Santos', 63, '::1', '2026-10-21 18:26:17'),
(47, 'DONATION_EDITED', 'DON-2026-0039', 'Updated donation details for Pamela Brown', 'Ethan Santos', 63, '::1', '2026-10-21 18:26:45'),
(48, 'DONATION_EDITED', 'DON-2026-0039', 'Updated donation details for Pamela Brown', 'Ethan Santos', 63, '::1', '2026-10-21 18:27:00'),
(49, 'DONATION_VERIFIED', 'DON-2026-0039', 'Updated donation DON-2026-0039 status to Available. Admin notes: ', 'Ethan Santos', 63, '::1', '2026-10-21 18:27:23'),
(50, 'DONATION_EDITED', 'DON-2026-0039', 'Updated donation details for Pamela Brown', 'Ethan Santos', 63, '::1', '2026-10-21 18:27:47'),
(51, 'DONATION_EDITED', 'DON-2026-0039', 'Updated donation details for Pamela Brown', 'Ethan Santos', 63, '::1', '2026-10-21 18:27:53');

-- --------------------------------------------------------

--
-- Table structure for table `charity_allocations`
--

CREATE TABLE `charity_allocations` (
  `id` int(11) NOT NULL,
  `allocation_ref` varchar(50) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `donation_ref` varchar(50) NOT NULL,
  `receiving_department` enum('Damayan','Dawah') NOT NULL,
  `allocation_type` enum('monetary','in-kind') NOT NULL,
  `allocated_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allocated_quantity` int(11) NOT NULL DEFAULT 0,
  `distributed_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `distributed_quantity` int(11) NOT NULL DEFAULT 0,
  `purpose` text NOT NULL,
  `allocation_date` date NOT NULL,
  `allocated_by` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Pending Distribution','Partially Distributed','Distributed','Cancelled') NOT NULL DEFAULT 'Pending Distribution',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charity_allocations`
--

INSERT INTO `charity_allocations` (`id`, `allocation_ref`, `donation_id`, `donation_ref`, `receiving_department`, `allocation_type`, `allocated_amount`, `allocated_quantity`, `distributed_amount`, `distributed_quantity`, `purpose`, `allocation_date`, `allocated_by`, `remarks`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'ALC-2026-0001', 1, 'DON-2026-0001', 'Damayan', 'monetary', 20000.00, 0, 12000.00, 0, 'Burial Assistance for Indigent Families', '2026-10-05', 'Admin Staff', 'Allocated for indigent Muslim burial operations', 'Partially Distributed', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(2, 'ALC-2026-0002', 3, 'DON-2026-0003', 'Dawah', 'in-kind', 0.00, 20, 0.00, 15, 'Da\'wah Outreach and New Muslim Welfare Kits', '2026-10-10', 'Admin Staff', 'Relief support for new Muslim converts in need', 'Partially Distributed', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(3, 'ALC-2026-0003', 6, 'DON-2026-0006', 'Damayan', 'monetary', 25000.00, 0, 25000.00, 0, 'Burial Assistance Funding', '2026-10-17', 'Staff Tester', 'Assigned to Damayan operations', 'Distributed', 1, '2026-10-17 21:03:06', '2026-10-17 21:03:06'),
(4, 'ALC-2026-0004', 7, 'DON-2026-0007', 'Dawah', 'in-kind', 0.00, 20, 0.00, 0, 'Outreach kits for new Muslims', '2026-10-17', 'Staff Tester', ' [Cancelled: Outreach program postponed]', 'Cancelled', 1, '2026-10-17 21:03:06', '2026-10-17 21:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `charity_distributions`
--

CREATE TABLE `charity_distributions` (
  `id` int(11) NOT NULL,
  `distribution_ref` varchar(50) NOT NULL,
  `allocation_id` int(11) NOT NULL,
  `allocation_ref` varchar(50) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `donation_ref` varchar(50) NOT NULL,
  `receiving_department` enum('Damayan','Dawah') NOT NULL,
  `distribution_type` enum('monetary','in-kind') NOT NULL,
  `distributed_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `distributed_quantity` int(11) NOT NULL DEFAULT 0,
  `items_summary` text DEFAULT NULL,
  `purpose` text NOT NULL,
  `distribution_date` date NOT NULL,
  `distributed_by` varchar(255) NOT NULL,
  `received_by` varchar(255) NOT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `status` enum('Distributed','Cancelled') NOT NULL DEFAULT 'Distributed',
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charity_distributions`
--

INSERT INTO `charity_distributions` (`id`, `distribution_ref`, `allocation_id`, `allocation_ref`, `donation_id`, `donation_ref`, `receiving_department`, `distribution_type`, `distributed_amount`, `distributed_quantity`, `items_summary`, `purpose`, `distribution_date`, `distributed_by`, `received_by`, `supporting_document`, `status`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'DST-2026-0001', 1, 'ALC-2026-0001', 1, 'DON-2026-0001', 'Damayan', 'monetary', 12000.00, 0, '₱12,000.00 Cash Aid', 'Burial Shroud and Grave Assistance (Case #B-2026-012)', '2026-10-09', 'Admin Staff', 'Family of Late Brother Omar', NULL, 'Distributed', 'Immediate burial subsidy provided', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(2, 'DST-2026-0002', 2, 'ALC-2026-0002', 3, 'DON-2026-0003', 'Dawah', 'in-kind', 0.00, 15, '15x Sacks of Premium Rice (25kg)', 'Distributed to 15 New Convert Beneficiary Families', '2026-10-13', 'Admin Staff', 'Dawah Outreach Committee (Ustadz Farhan)', NULL, 'Distributed', 'Signed receiving acknowledgment on file', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(3, 'DST-2026-0003', 3, 'ALC-2026-0003', 6, 'DON-2026-0006', 'Damayan', 'monetary', 15000.00, 0, '₱15,000.00 Cash Aid', 'Grave excavation & burial aid for family', '2026-10-17', 'Staff Tester', 'Family of Brother Kareem', NULL, 'Distributed', 'Handed over in cash', 1, '2026-10-17 21:03:06', '2026-10-17 21:03:06'),
(4, 'DST-2026-0004', 3, 'ALC-2026-0003', 6, 'DON-2026-0006', 'Damayan', 'monetary', 10000.00, 0, '₱10,000.00 Cash Aid', 'Final portion of burial assistance', '2026-10-17', 'Staff Tester', 'Family of Sister Aminah', NULL, 'Distributed', 'Full allocation now disbursed', 1, '2026-10-17 21:03:06', '2026-10-17 21:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `charity_donations_records`
--

CREATE TABLE `charity_donations_records` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `donation_ref` varchar(50) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `donor_address` text DEFAULT NULL,
  `donation_type` enum('monetary','in-kind') NOT NULL,
  `donation_category` varchar(50) NOT NULL DEFAULT 'digital',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_description` text DEFAULT NULL,
  `target_program` varchar(150) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(50) DEFAULT 'packs',
  `allocated_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allocated_quantity` int(11) NOT NULL DEFAULT 0,
  `date_received` date NOT NULL,
  `preferred_dropoff_date` date DEFAULT NULL,
  `dropoff_location` varchar(255) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `received_by` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `ack_issued` tinyint(1) NOT NULL DEFAULT 0,
  `ack_issued_at` datetime DEFAULT NULL,
  `ack_issued_by` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Available',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charity_donations_records`
--

INSERT INTO `charity_donations_records` (`id`, `tenant_id`, `donation_ref`, `donor_name`, `donor_address`, `donation_type`, `donation_category`, `amount`, `item_description`, `target_program`, `quantity`, `unit`, `allocated_amount`, `allocated_quantity`, `date_received`, `preferred_dropoff_date`, `dropoff_location`, `payment_method`, `payment_reference`, `received_by`, `remarks`, `admin_notes`, `receipt_path`, `ack_issued`, `ack_issued_at`, `ack_issued_by`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'DON-2026-0001', 'Al-Huda Community Foundation', 'Ermita, Manila', 'monetary', 'digital', 50000.00, NULL, NULL, 0, 'packs', 20000.00, 0, '2026-09-29', NULL, NULL, 'Bank Transfer', 'BDO-TRX-982143', 'Admin Staff', 'General charity and welfare donation', NULL, NULL, 1, '2026-09-29 10:00:00', 'Admin Staff', 'Partially Allocated', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(2, NULL, 'DON-2026-0002', 'Haji Abdullah Ibrahim', 'Quiapo, Manila', 'monetary', 'digital', 25000.00, NULL, NULL, 0, 'packs', 0.00, 0, '2026-10-03', NULL, NULL, 'GCash', 'GC-90184712', 'Admin Staff', 'Designated for funeral and burial support', NULL, NULL, 1, '2026-10-03 10:00:00', 'Admin Staff', 'Available', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(3, NULL, 'DON-2026-0003', 'Baitul Mal Welfare Group', 'Greenhills, San Juan', 'in-kind', 'food', 0.00, 'Sacks of Premium Rice (25kg)', NULL, 60, 'packs', 0.00, 20, '2026-10-07', NULL, NULL, 'In-Person Delivery', 'DR-2026-081', 'Admin Staff', 'Community food assistance packs', NULL, NULL, 1, '2026-10-07 10:00:00', 'Admin Staff', 'Partially Allocated', NULL, '2026-10-17 20:52:43', '2026-10-18 12:57:32'),
(4, NULL, 'DON-2026-0004', 'Sister Maryam & Family', 'Pasig City', 'monetary', 'digital', 15000.00, NULL, NULL, 0, 'packs', 0.00, 0, '2026-10-12', NULL, NULL, 'Cash', 'CSH-00291', 'Admin Staff', 'Sadaqah Jariyah for community welfare', NULL, NULL, 1, '2026-10-12 10:00:00', 'Admin Staff', 'Available', NULL, '2026-10-17 20:52:43', '2026-10-17 20:52:43'),
(5, NULL, 'DON-2026-0005', 'An-Noor Relief Association', 'Taguig City', 'in-kind', 'food', 0.00, 'Complete Janazah Burial Shroud Kits (Kafan)', NULL, 20, 'packs', 0.00, 0, '2026-10-14', NULL, NULL, 'In-Person Delivery', 'DEL-7712', 'Admin Staff', 'Burial shroud cloth and camphor supplies', NULL, NULL, 0, NULL, NULL, 'Available', NULL, '2026-10-17 20:52:43', '2026-10-18 12:57:32'),
(6, NULL, 'DON-2026-0006', 'Bro. Tariq Al-Mansoor', 'Makati City', 'monetary', 'digital', 40000.00, NULL, NULL, 0, 'packs', 25000.00, 0, '2026-10-17', NULL, NULL, 'Bank Transfer', 'BDO-TEST-999', 'Staff Tester', 'Test monetary donation', NULL, NULL, 1, '2026-10-17 23:03:06', 'Staff Tester', 'Partially Allocated', 1, '2026-10-17 21:03:06', '2026-10-17 21:03:06'),
(7, NULL, 'DON-2026-0007', 'Al-Hikmah Relief Group', 'Quezon City', 'in-kind', 'food', 0.00, 'Winter Blanket & Emergency Relief Packs', NULL, 50, 'packs', 0.00, 0, '2026-10-17', NULL, NULL, 'In-Person Delivery', 'DEL-TEST-888', 'Staff Tester', 'Test in-kind donation', NULL, NULL, 0, NULL, NULL, 'Available', 1, '2026-10-17 21:03:06', '2026-10-18 12:57:32'),
(8, 61, 'DON-2026-0008', 'Art Denver', 'dacer2314@gmail.com', 'monetary', 'digital', 10000.00, NULL, 'General Welfare & Relief', 0, 'packs', 0.00, 0, '2026-10-18', NULL, NULL, 'GCash', '', 'Online Portal (Self-Service)', 'Online donation for General Welfare & Relief via GCash', NULL, '', 0, NULL, NULL, 'Available', 61, '2026-10-18 11:22:16', '2026-10-18 11:22:16'),
(9, 61, 'DON-2026-0009', 'Art Denver', 'dacer2314@gmail.com', 'monetary', 'digital', 500.00, NULL, 'Ramadan & Food Security', 0, 'packs', 0.00, 0, '2026-10-18', NULL, NULL, 'GCash', '', 'Online Portal (Self-Service)', 'Online donation for Ramadan & Food Security via GCash', NULL, '', 0, NULL, NULL, 'Available', 61, '2026-10-18 11:24:35', '2026-10-18 11:24:35'),
(17, 61, 'DON-2026-0010', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Medical Donation', 0, 'packs', 0.00, 0, '2026-10-18', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', '', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 15:24:16', '2026-10-18 15:24:16'),
(20, 61, 'DON-2026-0018', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:06:28', '2026-10-18 16:06:28'),
(21, 61, 'DON-2026-0021', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:06:29', '2026-10-18 16:06:29'),
(22, 61, 'DON-2026-0022', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:06:30', '2026-10-18 16:06:30'),
(23, 61, 'DON-2026-0023', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:06:31', '2026-10-18 16:06:31'),
(24, 61, 'DON-2026-0024', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:06:31', '2026-10-18 16:06:31'),
(25, 61, 'DON-2026-0025', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:06:31', '2026-10-18 16:06:31'),
(26, 61, 'DON-2026-0026', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:09:22', '2026-10-18 16:09:22'),
(27, 61, 'DON-2026-0027', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:09:23', '2026-10-18 16:09:23'),
(28, 61, 'DON-2026-0028', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:09:23', '2026-10-18 16:09:23'),
(29, 61, 'DON-2026-0029', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:13:46', '2026-10-18 16:13:46'),
(30, 61, 'DON-2026-0030', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:13:56', '2026-10-18 16:13:56'),
(31, 61, 'DON-2026-0031', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:13:56', '2026-10-18 16:13:56'),
(32, 61, 'DON-2026-0032', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:13:57', '2026-10-18 16:13:57'),
(33, 61, 'DON-2026-0033', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:14:07', '2026-10-18 16:14:07'),
(34, 61, 'DON-2026-0034', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:15:25', '2026-10-18 16:15:25'),
(35, 61, 'DON-2026-0035', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:17:14', '2026-10-18 16:17:14'),
(36, 61, 'DON-2026-0036', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:22:59', '2026-10-18 16:22:59'),
(37, 61, 'DON-2026-0037', 'Art Denver', '+639485269632', 'monetary', 'digital', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'GCash', 'asdasdasd', 'Online Portal (Pending Staff Verification)', '[Real Payment Account: Bank: GCash, Acct #: 0917-123-4567, Acct Name: ISCAG Relief & Damayan Fund, Branch: Official Merchant Account]', NULL, '', 0, NULL, NULL, 'Pending Verification', 61, '2026-10-18 16:24:31', '2026-10-18 16:24:31'),
(38, 61, 'DON-2026-0038', 'Art Denver', '+639485269632', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-19', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Online Portal (Pending Staff Verification)', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', NULL, '', 0, NULL, NULL, 'Pending Receipt', 61, '2026-10-18 16:25:16', '2026-10-18 16:25:16'),
(39, 65, 'DON-2026-0039', 'Pamela Brown', '09102458643', 'monetary', 'cash', 500.00, NULL, 'Food Donation', 0, 'packs', 0.00, 0, '2026-10-20', NULL, 'ISCAG Islamic Complex, #31 Jose Abad Santos St., Brgy. Salitran I, Dasmariñas City, Cavite 4114', 'Cash (ISCAG Office)', '', 'Ethan Santos', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk', '', '', 0, NULL, NULL, 'Available', 65, '2026-10-20 13:13:01', '2026-10-21 18:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `conversion_requests`
--

CREATE TABLE `conversion_requests` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) NOT NULL,
  `adopted_name` varchar(255) NOT NULL,
  `sex` varchar(20) DEFAULT 'Male',
  `civil_status` varchar(50) DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT 'Filipino',
  `dob` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `former_religion` varchar(100) DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `residence` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_religion` varchar(100) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_religion` varchar(100) DEFAULT NULL,
  `conversion_date` date NOT NULL,
  `witness1_name` varchar(255) DEFAULT NULL,
  `witness1_address` varchar(255) DEFAULT NULL,
  `witness2_name` varchar(255) DEFAULT NULL,
  `witness2_address` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversion_requests`
--

INSERT INTO `conversion_requests` (`id`, `tenant_id`, `fname`, `mname`, `lname`, `adopted_name`, `sex`, `civil_status`, `citizenship`, `dob`, `age`, `occupation`, `former_religion`, `pob`, `residence`, `father_name`, `father_religion`, `mother_name`, `mother_religion`, `conversion_date`, `witness1_name`, `witness1_address`, `witness2_name`, `witness2_address`, `status`, `created_at`) VALUES
(1, 60, 'Ryan', 'J', 'Felizardo', 'Rayyan ISCAG', 'Male', 'Single', 'Filipino', '1998-05-15', 28, 'Engineer', 'Christian', 'Manila', 'Taguig City', 'John Felizardo', 'Christian', 'Mary Felizardo', 'Christian', '2026-10-18', 'Ustadh Abdullah', 'ISCAG Mosque', 'Brother Ibrahim', 'ISCAG Center', 'Accepted', '2026-10-19 15:04:16');

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
-- Table structure for table `counseling_requests`
--

CREATE TABLE `counseling_requests` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `session_type` varchar(50) DEFAULT 'in_person',
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(20) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `start_time` varchar(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  `final_session_type` varchar(50) DEFAULT 'In-Person',
  `appointment_location` varchar(255) DEFAULT 'ISCAG Center, 2nd Floor Da''wah Counseling Office',
  `meeting_link` varchar(255) DEFAULT NULL,
  `meeting_instructions` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `counselor_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `completed_notes` text DEFAULT NULL,
  `counselor_id` int(11) DEFAULT NULL,
  `counselor_name` varchar(255) DEFAULT NULL,
  `counselor_role` varchar(255) DEFAULT NULL,
  `counselor_contact` varchar(255) DEFAULT NULL,
  `counselor_assigned_at` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `followup_time` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counseling_requests`
--

INSERT INTO `counseling_requests` (`id`, `tenant_id`, `gender`, `reason`, `description`, `session_type`, `preferred_date`, `preferred_time`, `appointment_date`, `start_time`, `end_time`, `final_session_type`, `appointment_location`, `meeting_link`, `meeting_instructions`, `status`, `counselor_notes`, `rejection_reason`, `completed_notes`, `counselor_id`, `counselor_name`, `counselor_role`, `counselor_contact`, `counselor_assigned_at`, `confirmed_at`, `completed_at`, `followup_date`, `followup_time`, `created_at`, `updated_at`, `full_name`, `age`, `civil_status`, `address`, `contact_number`) VALUES
(1, 31, 'male', 'Family / Marital Issues', 'Test concern for marital consultation', 'in_person', '2026-10-21', '10:30 AM - 11:30 AM', '2026-10-22', '10:00', '11:00', 'In-Person', 'ISCAG Center, 2nd Floor Da\'wah Counseling Office Room 204', '', '', 'confirmed', '', NULL, NULL, 2, 'Ustaz Abdullah Al-Mansoor', 'Resident Islamic Scholar & Family Counselor', 'counseling.dawah@iscag.org', '2026-10-20 03:51:18', '2026-10-20 03:51:18', NULL, NULL, NULL, '2026-10-18 19:26:28', '2026-10-19 19:51:18', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `counselors`
--

CREATE TABLE `counselors` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `office_location` varchar(255) DEFAULT 'ISCAG Center, 2nd Floor Da''wah Counseling Office',
  `specialties` varchar(255) DEFAULT 'Family & Marital, Spiritual Guidance, Youth Mentorship',
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counselors`
--

INSERT INTO `counselors` (`id`, `tenant_id`, `name`, `designation`, `gender`, `email`, `phone`, `office_location`, `specialties`, `profile_photo`, `status`, `created_at`) VALUES
(1, 59, 'Ustaz Norman Ungasin', 'Head of Da\'wah & Senior Islamic Counselor', 'male', 'kizumikaze1@gmail.com', '+63 906 574 0819', 'ISCAG Center, 2nd Floor Da\'wah Office Room 201', 'Family Mediation, Marital Harmony, Spiritual Tazkiyah', NULL, 'active', '2026-10-18 20:17:25'),
(2, NULL, 'Ustaz Abdullah Al-Mansoor', 'Resident Islamic Scholar & Family Counselor', 'male', 'counseling.dawah@iscag.org', '+63 917 123 4567', 'ISCAG Center, 2nd Floor Da\'wah Office Room 202', 'Pre-Marital Consultation, Grief & Bereavement, Islamic Ethics', NULL, 'active', '2026-10-18 20:17:25'),
(3, NULL, 'Ustaz Bilal Siddiqui', 'Youth Advisor & Revert Support Counselor', 'male', 'youth.guidance@iscag.org', '+63 918 765 4321', 'ISCAG Center, 2nd Floor Youth & Da\'wah Wing', 'Revert Integration, Youth & Academic Mentorship, Personal Development', NULL, 'active', '2026-10-18 20:17:25');

-- --------------------------------------------------------

--
-- Table structure for table `dawah_availability`
--

CREATE TABLE `dawah_availability` (
  `id` int(11) NOT NULL,
  `blocked_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `department` enum('male','female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dawah_death_certificates`
--

CREATE TABLE `dawah_death_certificates` (
  `id` int(11) NOT NULL,
  `source_type` varchar(50) DEFAULT 'encoded_certificate',
  `source_id` int(11) DEFAULT NULL,
  `registration_number` varchar(100) NOT NULL,
  `deceased_name` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT 'Male',
  `date_of_death` date NOT NULL,
  `time_of_death` time DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` varchar(100) DEFAULT NULL,
  `place_of_death` text DEFAULT NULL,
  `residence` text DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `religion` varchar(100) DEFAULT 'Islam',
  `citizenship` varchar(100) DEFAULT 'Filipino',
  `occupation` varchar(150) DEFAULT NULL,
  `informant_name` varchar(200) DEFAULT NULL,
  `informant_contact` varchar(50) DEFAULT NULL,
  `informant_relationship` varchar(100) DEFAULT NULL,
  `death_cert_date` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT 'Salatul Janazah & Religious Records',
  `transmission_notes` text DEFAULT NULL,
  `sent_by_id` int(11) DEFAULT NULL,
  `sent_by_name` varchar(150) DEFAULT 'Damayan Staff',
  `sent_at` datetime DEFAULT current_timestamp(),
  `status` enum('Received','Reviewed','Completed') DEFAULT 'Received',
  `acknowledged_by_id` int(11) DEFAULT NULL,
  `acknowledged_by_name` varchar(150) DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `province` varchar(150) DEFAULT 'Cavite',
  `city_municipality` varchar(150) DEFAULT 'City of Dasmariñas',
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `cause_of_death` text DEFAULT NULL,
  `cemetery_name` varchar(255) DEFAULT 'ISCAG Muslim Memorial Cemetery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dawah_death_certificates`
--

INSERT INTO `dawah_death_certificates` (`id`, `source_type`, `source_id`, `registration_number`, `deceased_name`, `first_name`, `middle_name`, `last_name`, `sex`, `date_of_death`, `time_of_death`, `date_of_birth`, `age`, `place_of_death`, `residence`, `civil_status`, `religion`, `citizenship`, `occupation`, `informant_name`, `informant_contact`, `informant_relationship`, `death_cert_date`, `file_path`, `file_name`, `purpose`, `transmission_notes`, `sent_by_id`, `sent_by_name`, `sent_at`, `status`, `acknowledged_by_id`, `acknowledged_by_name`, `acknowledged_at`, `created_at`, `updated_at`, `province`, `city_municipality`, `father_name`, `mother_name`, `cause_of_death`, `cemetery_name`) VALUES
(3, 'encoded_certificate', 8, '2026-0422', 'Art Denver Dacer', 'Art', 'Denver', 'Dacer', 'Male', '2012-10-18', NULL, '2023-01-10', '1808 yrs', 'Art Denver Dacer, asdsad, sadsad, sadasdsa, Dasmariñas, Cavite', 'asdsad, sadsad, sadasdsa, Dasmariñas, Cavite, Philippines', 'Single', 'Islam', 'Filipino', 'None', 'asdsadsadasdsadasdsadsadsadsa', '', '', '2026-10-18', NULL, 'Death_Certificate_Transmittal_2026-0422.html', 'Salatul Janazah & Burial Rites', '', 63, 'Ethan Santos', '2026-10-22 03:48:47', 'Received', NULL, NULL, NULL, '2026-10-22 03:48:47', '2026-10-22 03:48:47', 'Cavite', 'City of Dasmariñas', NULL, NULL, NULL, 'ISCAG Muslim Memorial Cemetery'),
(6, 'burial_request', 0, 'REQ-DC-REG-20261018-76DC', 'Art Denver Dacer', '', '', '', 'Male', '2026-10-18', NULL, NULL, '', '', '', '', 'Islam', 'Filipino', '', 'Art Denver', '', '', '2026-10-22', NULL, 'Death_Certificate_Transmittal_REQ-DC-REG-20261018-76DC.html', 'Salatul Janazah & Burial Rites', '', 63, 'Ethan Santos', '2026-10-22 03:51:40', 'Received', NULL, NULL, NULL, '2026-10-22 03:51:40', '2026-10-22 03:51:40', 'Cavite', 'City of Dasmariñas', NULL, NULL, NULL, 'ISCAG Muslim Memorial Cemetery'),
(7, 'encoded_certificate', 3, '2023-2-0001113', 'Juan Dela Cruz', '', '', '', 'Male', '2026-10-22', NULL, NULL, '', '', '', '', 'Islam', 'Filipino', '', 'Maria Dela Cruz', '', '', '2026-10-16', NULL, 'Death_Certificate_Transmittal_2023-2-0001113.html', 'Salatul Janazah & Burial Rites', '', 63, 'Ethan Santos', '2026-10-22 04:17:18', 'Reviewed', 63, 'Ethan Santos', '2026-10-22 20:00:25', '2026-10-22 04:17:18', '2026-10-22 20:00:25', 'Cavite', 'City of Dasmariñas', NULL, NULL, NULL, 'ISCAG Muslim Memorial Cemetery');

-- --------------------------------------------------------

--
-- Table structure for table `dawah_manual_schedules`
--

CREATE TABLE `dawah_manual_schedules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `department` enum('male','female') NOT NULL,
  `event_type` varchar(50) DEFAULT 'Class',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dawah_manual_schedules`
--

INSERT INTO `dawah_manual_schedules` (`id`, `title`, `description`, `event_date`, `event_time`, `department`, `event_type`, `created_at`) VALUES
(1, 'Lesson', '', '2026-10-21', '10:00:00', 'female', 'Seminar', '2026-10-21 11:35:39');

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
-- Table structure for table `deceased_records`
--

CREATE TABLE `deceased_records` (
  `id` int(11) NOT NULL,
  `burial_request_id` int(11) DEFAULT NULL,
  `ref_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `sex` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `date_of_birth` date DEFAULT NULL,
  `date_of_death` date NOT NULL,
  `time_of_death` time DEFAULT NULL,
  `place_of_death` varchar(255) DEFAULT NULL,
  `residence_address` text DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT 'Filipino',
  `religion` varchar(100) DEFAULT 'Islam',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deceased_records`
--

INSERT INTO `deceased_records` (`id`, `burial_request_id`, `ref_id`, `first_name`, `middle_name`, `last_name`, `full_name`, `sex`, `date_of_birth`, `date_of_death`, `time_of_death`, `place_of_death`, `residence_address`, `civil_status`, `nationality`, `religion`, `created_at`, `updated_at`) VALUES
(25, 42, 'DC-REG-20261016-6543', 'Juan', 'Duplicate Dela Cruz', '1792179924', 'Juan Duplicate Dela Cruz 1792179924', 'Male', NULL, '2026-10-15', NULL, 'Cotabato Regional Medical Center', NULL, 'Married', 'Filipino', 'Islam', '2026-10-17 03:45:24', '2026-10-17 03:45:24'),
(28, 45, 'DC-REG-20261018-76DC', 'Art', 'Denver', 'Dacer', 'Art Denver Dacer', 'Male', '2026-10-14', '2026-10-18', NULL, 'Dasmariñas', 'Blk 78 Lot 3, Brgy. San Juan', 'Married', 'Filipino', 'Islam', '2026-10-18 17:07:58', '2026-10-18 17:07:58');

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
-- Table structure for table `encoded_death_certificates`
--

CREATE TABLE `encoded_death_certificates` (
  `id` int(11) NOT NULL,
  `registration_number` varchar(100) NOT NULL,
  `deceased_name` varchar(255) NOT NULL,
  `representative_name` varchar(255) NOT NULL,
  `burial_request_id` int(11) DEFAULT NULL,
  `encoded_by_id` int(11) DEFAULT NULL,
  `encoded_by_name` varchar(150) NOT NULL DEFAULT 'Damayan Staff',
  `encoded_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `province` varchar(150) DEFAULT NULL,
  `city_municipality` varchar(150) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT 'Male',
  `date_of_death_day` int(11) DEFAULT NULL,
  `date_of_death_month` int(11) DEFAULT NULL,
  `date_of_death_year` int(11) DEFAULT NULL,
  `date_of_death` date DEFAULT NULL,
  `time_of_death` time DEFAULT NULL,
  `date_of_birth_day` int(11) DEFAULT NULL,
  `date_of_birth_month` int(11) DEFAULT NULL,
  `date_of_birth_year` int(11) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age_years` int(11) DEFAULT NULL,
  `age_months` int(11) DEFAULT NULL,
  `age_days` int(11) DEFAULT NULL,
  `age_hours` int(11) DEFAULT NULL,
  `age_unit` varchar(20) DEFAULT 'years',
  `age_display` varchar(100) DEFAULT NULL,
  `place_of_death_hospital` varchar(255) DEFAULT NULL,
  `place_of_death_house_no` varchar(100) DEFAULT NULL,
  `place_of_death_street` varchar(255) DEFAULT NULL,
  `place_of_death_barangay` varchar(150) DEFAULT NULL,
  `place_of_death_city` varchar(150) DEFAULT NULL,
  `place_of_death_province` varchar(150) DEFAULT NULL,
  `place_of_death` text DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `religion` varchar(100) DEFAULT 'Islam',
  `citizenship` varchar(100) DEFAULT 'Filipino',
  `residence_house_no` varchar(100) DEFAULT NULL,
  `residence_street` varchar(255) DEFAULT NULL,
  `residence_barangay` varchar(150) DEFAULT NULL,
  `residence_city` varchar(150) DEFAULT NULL,
  `residence_province` varchar(150) DEFAULT NULL,
  `residence_country` varchar(100) DEFAULT 'Philippines',
  `residence` text DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `encoded_death_certificates`
--

INSERT INTO `encoded_death_certificates` (`id`, `registration_number`, `deceased_name`, `representative_name`, `burial_request_id`, `encoded_by_id`, `encoded_by_name`, `encoded_at`, `created_at`, `updated_at`, `province`, `city_municipality`, `first_name`, `middle_name`, `last_name`, `sex`, `date_of_death_day`, `date_of_death_month`, `date_of_death_year`, `date_of_death`, `time_of_death`, `date_of_birth_day`, `date_of_birth_month`, `date_of_birth_year`, `date_of_birth`, `age_years`, `age_months`, `age_days`, `age_hours`, `age_unit`, `age_display`, `place_of_death_hospital`, `place_of_death_house_no`, `place_of_death_street`, `place_of_death_barangay`, `place_of_death_city`, `place_of_death_province`, `place_of_death`, `civil_status`, `religion`, `citizenship`, `residence_house_no`, `residence_street`, `residence_barangay`, `residence_city`, `residence_province`, `residence_country`, `residence`, `occupation`) VALUES
(3, '2023-2-0001113', 'Juan Dela Cruz', 'Maria Dela Cruz', 46, 63, 'Ethan Santos', '2026-10-16 20:16:41', '2026-10-16 20:16:41', '2026-10-22 17:33:24', NULL, NULL, NULL, NULL, NULL, 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'years', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Islam', 'Filipino', NULL, NULL, NULL, NULL, NULL, 'Philippines', NULL, NULL),
(8, '2026-0422', 'Art Denver Dacer', 'asdsadsadasdsadasdsadsadsadsa', NULL, 63, 'Ethan Santos', '2026-10-18 04:17:13', '2026-10-18 04:17:13', '2026-10-18 04:17:13', 'Cavite', 'City of Dasmariñas', 'Art', 'Denver', 'Dacer', 'Male', 18, 10, 2012, '2012-10-18', NULL, 10, 1, 2023, '2023-01-10', 1808, 10, NULL, NULL, 'years', '1808 yrs', 'Art Denver Dacer', 'asdsad', 'sadsad', 'sadasdsa', 'Dasmariñas', 'Cavite', 'Art Denver Dacer, asdsad, sadsad, sadasdsa, Dasmariñas, Cavite', 'Single', 'Islam', 'Filipino', 'asdsad', 'sadsad', 'sadasdsa', 'Dasmariñas', 'Cavite', 'Philippines', 'asdsad, sadsad, sadasdsa, Dasmariñas, Cavite, Philippines', 'None');

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
  `parent_signature` varchar(255) DEFAULT NULL,
  `emergency_name` varchar(150) DEFAULT NULL,
  `parent_address` text DEFAULT NULL,
  `parent_contact` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `female_relationship`
--

INSERT INTO `female_relationship` (`relation_id`, `female_id`, `rfirst_name`, `rlast_name`, `rmiddle_name`, `contactnum`, `address`, `relationship`, `age`, `birth_date`, `parentname`, `fsignature`, `parent_signature`, `emergency_name`, `parent_address`, `parent_contact`, `created_at`) VALUES
(1, 1, NULL, NULL, NULL, '09987654321', '123 Muslim Community, Dasmariñas, Cavite', 'Mother', NULL, NULL, 'Zainab Dela Cruz', 'Fatima Dela Cruz', 'Zainab Dela Cruz', 'Zainab Dela Cruz', '123 Muslim Community, Dasmariñas, Cavite', '09987654321', '2026-10-20 15:29:08');

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
  `status` varchar(50) DEFAULT NULL,
  `facebook_account` varchar(150) DEFAULT NULL,
  `studied_islamic_before` varchar(10) DEFAULT 'No',
  `studied_where` varchar(255) DEFAULT NULL,
  `batch` varchar(50) DEFAULT 'BATCH 08',
  `fee_level` varchar(50) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `fee_date` date DEFAULT NULL,
  `fee_received_by` varchar(100) DEFAULT NULL,
  `fee_approved_by` varchar(150) DEFAULT 'SISTER KHADIJAH DIMAANO',
  `approved_by` varchar(150) DEFAULT 'KHADIJAH DIMAANO',
  `prepared_by` varchar(150) DEFAULT 'IDFS STAFF',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funeral_cases`
--

CREATE TABLE `funeral_cases` (
  `id` int(11) NOT NULL,
  `case_number` varchar(20) NOT NULL,
  `tenant_id` varchar(50) NOT NULL,
  `status` enum('Draft','Submitted','Under Review','Information Verified','Requirements Incomplete','Ready for Registration','Submitted to LCRO','Registered','Returned for Correction','Completed','Rejected') DEFAULT 'Draft',
  `deceased_first_name` varchar(100) DEFAULT NULL,
  `deceased_middle_name` varchar(100) DEFAULT NULL,
  `deceased_last_name` varchar(100) DEFAULT NULL,
  `deceased_haj_name` varchar(100) DEFAULT NULL,
  `deceased_sex` enum('Male','Female') DEFAULT NULL,
  `deceased_dob` date DEFAULT NULL,
  `deceased_dod` date DEFAULT NULL,
  `deceased_tod` time DEFAULT NULL,
  `deceased_place_of_death` varchar(255) DEFAULT NULL,
  `deceased_address` text DEFAULT NULL,
  `deceased_civil_status` varchar(50) DEFAULT NULL,
  `deceased_nationality` varchar(100) DEFAULT 'Filipino',
  `deceased_religion` varchar(100) DEFAULT 'Islam',
  `deceased_occupation` varchar(100) DEFAULT NULL,
  `informant_name` varchar(200) DEFAULT NULL,
  `informant_relationship` varchar(100) DEFAULT NULL,
  `informant_contact` varchar(50) DEFAULT NULL,
  `informant_email` varchar(200) DEFAULT NULL,
  `informant_address` text DEFAULT NULL,
  `burial_rites_person` varchar(200) DEFAULT NULL,
  `imam_name` varchar(200) DEFAULT NULL,
  `surviving_spouses` text DEFAULT NULL,
  `burial_date` date DEFAULT NULL,
  `burial_time` time DEFAULT NULL,
  `burial_location` varchar(255) DEFAULT NULL,
  `cemetery` varchar(255) DEFAULT NULL,
  `grave_reference` varchar(100) DEFAULT NULL,
  `registration_reference` varchar(100) DEFAULT NULL,
  `registration_place` varchar(255) DEFAULT NULL,
  `registration_submitted_at` datetime DEFAULT NULL,
  `registration_completed_at` datetime DEFAULT NULL,
  `assigned_admin_id` varchar(50) DEFAULT NULL,
  `assigned_admin_name` varchar(200) DEFAULT NULL,
  `correction_reason` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reported_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `certificate_type` varchar(100) DEFAULT 'Death Certificate',
  `certificate_status` varchar(50) DEFAULT 'Requested',
  `processing_started_at` datetime DEFAULT NULL,
  `ready_at` datetime DEFAULT NULL,
  `scheduled_release_date` date DEFAULT NULL,
  `scheduled_release_time` time DEFAULT NULL,
  `release_location` varchar(255) DEFAULT 'Masjid Office',
  `assigned_staff_id` varchar(50) DEFAULT NULL,
  `assigned_staff_name` varchar(200) DEFAULT NULL,
  `release_method` varchar(100) DEFAULT 'Office Pick-up',
  `release_notes` text DEFAULT NULL,
  `reschedule_reason` text DEFAULT NULL,
  `released_at` datetime DEFAULT NULL,
  `released_by` varchar(200) DEFAULT NULL,
  `recipient_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funeral_cases`
--

INSERT INTO `funeral_cases` (`id`, `case_number`, `tenant_id`, `status`, `deceased_first_name`, `deceased_middle_name`, `deceased_last_name`, `deceased_haj_name`, `deceased_sex`, `deceased_dob`, `deceased_dod`, `deceased_tod`, `deceased_place_of_death`, `deceased_address`, `deceased_civil_status`, `deceased_nationality`, `deceased_religion`, `deceased_occupation`, `informant_name`, `informant_relationship`, `informant_contact`, `informant_email`, `informant_address`, `burial_rites_person`, `imam_name`, `surviving_spouses`, `burial_date`, `burial_time`, `burial_location`, `cemetery`, `grave_reference`, `registration_reference`, `registration_place`, `registration_submitted_at`, `registration_completed_at`, `assigned_admin_id`, `assigned_admin_name`, `correction_reason`, `rejection_reason`, `reported_at`, `verified_at`, `submitted_at`, `updated_at`, `completed_at`, `certificate_type`, `certificate_status`, `processing_started_at`, `ready_at`, `scheduled_release_date`, `scheduled_release_time`, `release_location`, `assigned_staff_id`, `assigned_staff_name`, `release_method`, `release_notes`, `reschedule_reason`, `released_at`, `released_by`, `recipient_name`) VALUES
(1, 'DC-2026-00001', '61', 'Submitted', 'Art', 'Denver', 'Dacer', '', 'Male', '2026-10-14', '2026-10-18', NULL, 'Dasmariñas', 'Blk 78 Lot 3, Brgy. San Juan', 'Married', 'Filipino', 'Islam', '', 'Art Denver', 'Relative', '+639485269632', '', 'Blk 78 Lot 3, Brgy. San Juan', '', '', '', NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-18 17:07:58', NULL, '2026-10-18 17:07:58', '2026-10-18 17:07:58', NULL, 'Death Certificate', 'Requested', NULL, NULL, NULL, NULL, 'Masjid Office', NULL, NULL, 'Office Pick-up', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `funeral_case_logs`
--

CREATE TABLE `funeral_case_logs` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `admin_id` varchar(50) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funeral_case_logs`
--

INSERT INTO `funeral_case_logs` (`id`, `case_id`, `user_id`, `admin_id`, `action`, `module`, `description`, `status`, `ip_address`, `created_at`) VALUES
(1, 1, '61', NULL, 'CERTIFICATE_REQUESTED', 'CERTIFICATE', 'Death Certificate request submitted for Art Dacer', 'Requested', '::1', '2026-10-18 17:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `funeral_documents`
--

CREATE TABLE `funeral_documents` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `doc_name` varchar(200) NOT NULL,
  `doc_type` varchar(100) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `upload_status` enum('Not Uploaded','Uploaded','Under Review','Verified','Rejected','Resubmission Required') DEFAULT 'Not Uploaded',
  `verified_by` varchar(200) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funeral_documents`
--

INSERT INTO `funeral_documents` (`id`, `case_id`, `doc_name`, `doc_type`, `file_path`, `file_name`, `upload_status`, `verified_by`, `verified_at`, `rejection_reason`, `remarks`, `uploaded_at`, `updated_at`) VALUES
(1, 1, 'Identification Document of Informant', 'identification', NULL, NULL, 'Not Uploaded', NULL, NULL, NULL, NULL, '2026-10-18 17:07:58', '2026-10-18 17:07:58'),
(2, 1, 'Supporting Death Documentation', 'death_support', NULL, NULL, 'Not Uploaded', NULL, NULL, NULL, NULL, '2026-10-18 17:07:58', '2026-10-18 17:07:58'),
(3, 1, 'Burial-Related Documentation', 'burial_doc', NULL, NULL, 'Not Uploaded', NULL, NULL, NULL, NULL, '2026-10-18 17:07:58', '2026-10-18 17:07:58'),
(4, 1, 'Other Required Documents', 'other', NULL, NULL, 'Not Uploaded', NULL, NULL, NULL, NULL, '2026-10-18 17:07:58', '2026-10-18 17:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `funeral_psa_requests`
--

CREATE TABLE `funeral_psa_requests` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `psa_ref_number` varchar(100) DEFAULT NULL,
  `requesting_party_name` varchar(200) DEFAULT NULL,
  `requesting_party_address` text DEFAULT NULL,
  `num_copies` int(11) DEFAULT 1,
  `purpose` varchar(255) DEFAULT NULL,
  `payment_status` enum('Unpaid','Paid','Waived') DEFAULT 'Unpaid',
  `request_status` enum('Request Created','Request Validated','Request Submitted','Processing','Ready','Released','Received by Requester') DEFAULT 'Request Created',
  `release_method` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `delivery_info` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `requested_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
-- Table structure for table `islamic_education_enrollments`
--

CREATE TABLE `islamic_education_enrollments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(10) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `muslim_name` varchar(150) DEFAULT NULL,
  `muslim_type` varchar(50) DEFAULT 'Revert',
  `birthdate` date DEFAULT NULL,
  `date_of_shahadah` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `facebook_account` varchar(150) DEFAULT NULL,
  `studied_islamic_before` varchar(10) DEFAULT 'No',
  `previous_school` varchar(255) DEFAULT NULL,
  `emergency_name` varchar(150) DEFAULT NULL,
  `emergency_relationship` varchar(100) DEFAULT NULL,
  `emergency_address` text DEFAULT NULL,
  `emergency_contact` varchar(50) DEFAULT NULL,
  `is_minor` tinyint(1) DEFAULT 0,
  `parent_name` varchar(150) DEFAULT NULL,
  `parent_address` text DEFAULT NULL,
  `parent_contact` varchar(50) DEFAULT NULL,
  `student_signature` varchar(150) DEFAULT NULL,
  `guardian_signature` varchar(150) DEFAULT NULL,
  `batch_no` varchar(50) DEFAULT 'Batch 08',
  `level` varchar(100) DEFAULT 'Basic',
  `payment_method` varchar(50) DEFAULT 'Cash',
  `fee_amount` decimal(10,2) DEFAULT 120.00,
  `payment_channel` varchar(100) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `approved_by` varchar(150) DEFAULT 'Sister Khadijah Dimaano',
  `prepared_by` varchar(150) DEFAULT 'IDFS Staff',
  `program_name` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `total_sessions` int(11) DEFAULT 12,
  `sessions_attended` int(11) DEFAULT 0,
  `sessions_completed` int(11) DEFAULT 0,
  `progress_percent` int(11) DEFAULT 0,
  `learning_status` varchar(50) DEFAULT 'Not Started',
  `gender` enum('male','female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `islamic_education_enrollments`
--

INSERT INTO `islamic_education_enrollments` (`id`, `tenant_id`, `first_name`, `middle_initial`, `last_name`, `muslim_name`, `muslim_type`, `birthdate`, `date_of_shahadah`, `address`, `contact_number`, `facebook_account`, `studied_islamic_before`, `previous_school`, `emergency_name`, `emergency_relationship`, `emergency_address`, `emergency_contact`, `is_minor`, `parent_name`, `parent_address`, `parent_contact`, `student_signature`, `guardian_signature`, `batch_no`, `level`, `payment_method`, `fee_amount`, `payment_channel`, `payment_reference`, `payment_status`, `approved_by`, `prepared_by`, `program_name`, `status`, `rejection_reason`, `total_sessions`, `sessions_attended`, `sessions_completed`, `progress_percent`, `learning_status`, `gender`, `created_at`, `updated_at`) VALUES
(2, 65, 'Pamela', '', 'Brown', '', 'Born-Muslim', '2026-10-16', NULL, 'asdsadsadsadsadas', '15256456456', '', 'No', '', 'Art Denver Dacer', 'asdasdsad', 'Blk 78 Lot 3, Brgy. San Juan', '09485269632', 0, 'asdadasd', 'asdsadsadsad', '231321456456', 'asdsadasdsad', 'asdsadsad', 'Batch 08', 'Level 1 — Basic Arabic & Tajweed', 'Cash', 120.00, NULL, 'ISCAG-RCPT-20261021-7D9D64', 'Paid', 'Sister Khadijah Dimaano', 'IDFS Staff', 'Islamic Education - Batch 08', 'Accepted', NULL, 12, 0, 0, 0, 'Not Started', 'female', '2026-10-20 15:52:47', '2026-10-21 10:45:56'),
(3, 65, 'Pamela', '', 'Brown', '', 'Born-Muslim', '2026-10-16', NULL, 'asdsadsadsadsadas', '15256456456', '', 'No', '', 'Art Denver Dacer', 'asdasdsad', 'Blk 78 Lot 3, Brgy. San Juan', '09485269632', 0, 'asdadasd', 'asdsadsadsad', '231321456456', 'asdsadasdsad', 'asdsadsad', 'Batch 08', 'Level 1 — Basic Arabic & Tajweed', 'Online Payment', 120.00, 'GCash', 'ISCAG-RCPT-20261021-253BAB', 'Paid', 'Sister Khadijah Dimaano', 'IDFS Staff', 'Islamic Education - Batch 08', 'Accepted', NULL, 12, 12, 0, 50, 'In Progress', 'female', '2026-10-20 15:52:59', '2026-10-21 14:09:59'),
(4, 65, 'Pamela', '', 'Brown', 'asdsadasdsa', 'Revert', '2026-10-08', '2026-10-21', 'asdsadsadas', 'dsadsadsa', 'dsadasdasd', 'No', '', 'sadsadsada', 'dasdsadsa', 'dsadsadsad', 'sdasdasdsa', 0, 'asdsadasdsad', 'asdasdasdsad', 'asdasdasd', 'asdsadasdsadsadsadasd', 'sadasdsad', 'Batch 08', 'Level 1 — Basic Arabic & Tajweed', 'Cash', 120.00, 'GCash', 'ISCAG-RCPT-20261021-AA83F4', 'Paid', 'Sister Khadijah Dimaano', 'IDFS Staff', 'Islamic Education - Batch 08', 'completed', NULL, 12, 8, 0, 33, 'In Progress', 'female', '2026-10-20 16:43:38', '2026-10-21 13:32:11');

-- --------------------------------------------------------

--
-- Table structure for table `islamic_education_sessions`
--

CREATE TABLE `islamic_education_sessions` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `session_number` int(11) NOT NULL,
  `session_title` varchar(255) NOT NULL,
  `is_attended` tinyint(1) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `attended_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `islamic_education_sessions`
--

INSERT INTO `islamic_education_sessions` (`id`, `enrollment_id`, `session_number`, `session_title`, `is_attended`, `is_completed`, `attended_at`, `completed_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Orientation & Arabic Alphabet (Huruf Hijaiyyah)', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:00', '2026-10-21 12:59:00'),
(2, 2, 2, 'Articulation Points & Phonetics (Makharijul Huruf)', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:00', '2026-10-21 12:59:00'),
(3, 2, 3, 'Harakat & Basic Vowels (Fathah, Kasrah, Dammah, Sukoon)', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(4, 2, 4, 'Rules of Madd (Elongation & Basic Tajweed)', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(5, 2, 5, 'Rules of Noon Sakinah & Tanween', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(6, 2, 6, 'Rules of Meem Sakinah & Qalqalah', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(7, 2, 7, 'Practical Qur\'an Recitation & Reading Fluency', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(8, 2, 8, 'Foundations of Taharah (Purification & Wudu Demonstration)', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(9, 2, 9, 'Conditions, Pillars & Obligatory Acts of Daily Salah', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(10, 2, 10, 'Practical Prayer Workshop & Common Mistakes in Salah', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(11, 2, 11, 'Essential Islamic Beliefs & Daily Du\'as (Hisnul Muslim)', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(12, 2, 12, 'Comprehensive Practical Assessment & Program Review', 0, 0, NULL, NULL, NULL, '2026-10-21 12:59:01', '2026-10-21 12:59:01'),
(13, 4, 1, 'Orientation & Arabic Alphabet (Huruf Hijaiyyah)', 1, 0, '2026-10-21 21:32:02', NULL, 'Attended orientation', '2026-10-21 13:15:45', '2026-10-21 13:32:02'),
(14, 4, 2, 'Articulation Points & Phonetics (Makharijul Huruf)', 1, 0, '2026-10-21 21:32:03', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:03'),
(15, 4, 3, 'Harakat & Basic Vowels (Fathah, Kasrah, Dammah, Sukoon)', 1, 0, '2026-10-21 21:32:05', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:05'),
(16, 4, 4, 'Rules of Madd (Elongation & Basic Tajweed)', 1, 0, '2026-10-21 21:32:05', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:05'),
(17, 4, 5, 'Rules of Noon Sakinah & Tanween', 1, 0, '2026-10-21 21:32:07', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:07'),
(18, 4, 6, 'Rules of Meem Sakinah & Qalqalah', 1, 0, '2026-10-21 21:32:08', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:08'),
(19, 4, 7, 'Practical Qur\'an Recitation & Reading Fluency', 1, 0, '2026-10-21 21:32:10', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:10'),
(20, 4, 8, 'Foundations of Taharah (Purification & Wudu Demonstration)', 1, 0, '2026-10-21 21:32:11', NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:32:11'),
(21, 4, 9, 'Conditions, Pillars & Obligatory Acts of Daily Salah', 0, 0, NULL, NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:24:30'),
(22, 4, 10, 'Practical Prayer Workshop & Common Mistakes in Salah', 0, 0, NULL, NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:24:30'),
(23, 4, 11, 'Essential Islamic Beliefs & Daily Du\'as (Hisnul Muslim)', 0, 0, NULL, NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:24:30'),
(24, 4, 12, 'Comprehensive Practical Assessment & Program Review', 0, 0, NULL, NULL, NULL, '2026-10-21 13:15:45', '2026-10-21 13:24:30'),
(25, 3, 1, 'Orientation & Arabic Alphabet (Huruf Hijaiyyah)', 1, 0, '2026-10-21 22:09:35', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:59'),
(26, 3, 2, 'Articulation Points & Phonetics (Makharijul Huruf)', 1, 0, '2026-10-21 22:09:40', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:58'),
(27, 3, 3, 'Harakat & Basic Vowels (Fathah, Kasrah, Dammah, Sukoon)', 1, 0, '2026-10-21 22:09:41', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:41'),
(28, 3, 4, 'Rules of Madd (Elongation & Basic Tajweed)', 1, 0, '2026-10-21 22:09:42', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:42'),
(29, 3, 5, 'Rules of Noon Sakinah & Tanween', 1, 0, '2026-10-21 22:09:43', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:43'),
(30, 3, 6, 'Rules of Meem Sakinah & Qalqalah', 1, 0, '2026-10-21 22:09:44', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:44'),
(31, 3, 7, 'Practical Qur\'an Recitation & Reading Fluency', 1, 0, '2026-10-21 22:09:46', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:46'),
(32, 3, 8, 'Foundations of Taharah (Purification & Wudu Demonstration)', 1, 0, '2026-10-21 22:09:45', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:45'),
(33, 3, 9, 'Conditions, Pillars & Obligatory Acts of Daily Salah', 1, 0, '2026-10-21 22:09:48', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:48'),
(34, 3, 10, 'Practical Prayer Workshop & Common Mistakes in Salah', 1, 0, '2026-10-21 22:09:49', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:49'),
(35, 3, 11, 'Essential Islamic Beliefs & Daily Du\'as (Hisnul Muslim)', 1, 0, '2026-10-21 22:09:50', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:50'),
(36, 3, 12, 'Comprehensive Practical Assessment & Program Review', 1, 0, '2026-10-21 22:09:52', NULL, NULL, '2026-10-21 13:32:29', '2026-10-21 14:09:52');

-- --------------------------------------------------------

--
-- Table structure for table `leases`
--

CREATE TABLE `leases` (
  `lease_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `unit_type` varchar(100) DEFAULT NULL COMMENT 'Preferred room type from application',
  `monthly_rent` decimal(10,2) DEFAULT 0.00,
  `deposit_amount` decimal(10,2) DEFAULT 0.00,
  `advance_amount` decimal(10,2) DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `lease_status` enum('Pending','Accepted','Rejected','Active','Expired') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leases`
--

INSERT INTO `leases` (`lease_id`, `tenant_id`, `application_id`, `unit_type`, `monthly_rent`, `deposit_amount`, `advance_amount`, `start_date`, `end_date`, `lease_status`, `created_at`, `updated_at`) VALUES
(6, 60, 22, 'Transient', 2500.00, 1000.00, 2500.00, '2026-05-01', '2027-05-01', 'Active', '2026-04-30 23:32:57', '2026-04-30 23:36:30'),
(7, 61, 23, 'Transient', 2500.00, 1000.00, 2500.00, '2026-10-07', '2027-10-07', 'Active', '2026-10-07 13:38:16', '2026-10-07 14:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `lease_renewals`
--

CREATE TABLE `lease_renewals` (
  `renewal_id` int(11) NOT NULL,
  `lease_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `requested_term_months` int(11) NOT NULL DEFAULT 12,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `marriage_requests`
--

CREATE TABLE `marriage_requests` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `groom_name` varchar(255) NOT NULL,
  `bride_name` varchar(255) NOT NULL,
  `marriage_date` date NOT NULL,
  `marriage_time` time NOT NULL,
  `marriage_venue` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marriage_requests`
--

INSERT INTO `marriage_requests` (`id`, `tenant_id`, `groom_name`, `bride_name`, `marriage_date`, `marriage_time`, `marriage_venue`, `status`, `created_at`) VALUES
(1, 60, 'Ryan Felizardo', 'Amina Santos', '2026-11-20', '10:00:00', 'ISCAG Main Mosque', 'Accepted', '2026-10-19 15:04:16');

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
-- Table structure for table `move_out_requests`
--

CREATE TABLE `move_out_requests` (
  `request_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `move_out_date` date DEFAULT NULL,
  `inspection_notes` text DEFAULT NULL,
  `damage_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `utility_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_refund` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `tenant_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(13, 32, 'Room Assigned!', 'Congratulations! You have been assigned to Room B1-02 in Building 1. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-24 11:22:34'),
(15, 27, 'Room Assigned!', 'Congratulations! You have been assigned to Room B2-07 in Building 2. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-25 05:11:08'),
(16, 0, 'Room Assigned!', 'Congratulations! You have been assigned to Room B1-06 in Building 1. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-25 18:02:31'),
(19, 47, 'Room Assigned!', 'Congratulations! You have been assigned to Room B2-13 in Building 2. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 11:17:29'),
(20, 47, 'Room Assigned!', 'Congratulations! You have been assigned to Room B2-14 in Building 2. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 12:04:46'),
(21, 47, 'Room Assigned!', 'Congratulations! You have been assigned to Room B3-01 in Building 3. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 12:20:20'),
(32, 60, 'Application Approved!', 'Congratulations! Your apartment application has been approved. Please review and accept your Lease Contract to proceed to Initial Payments. A room will be assigned once payments are settled.', 'approval', 1, '2026-04-30 23:32:57'),
(33, 60, 'Room Assigned!', 'Your payment is confirmed and your lease is now Active! You have been assigned to Room B3-30 in Building 3. Welcome to your new home!', 'payment', 1, '2026-04-30 23:36:30'),
(34, 60, 'Payment Received', 'Your payment for Rent Advance has been recorded (Ref: PAY-ADV-605984). Thank you for settling your dues!', 'payment', 1, '2026-05-01 13:22:34'),
(35, 60, 'Payment Received', 'Your payment for Water 2026 07 has been recorded (Ref: PAY-BLK-6093076). Thank you for settling your dues!', 'payment', 1, '2026-05-01 13:23:40'),
(36, 60, 'Payment Received', 'Your payment for Contribution 2026 06, Contribution 2026 07 has been recorded (Ref: PAY-BLK-3922295). Thank you for settling your dues!', 'payment', 1, '2026-05-01 14:08:29'),
(37, 60, 'Upcoming Payment Reminder - May 2026', 'Heads up! Your monthly bill for May 2026 is due on the 5th. Please settle your Rent, Water, and Contributions to avoid any late flags.', 'payment', 1, '2026-05-01 14:53:19'),
(38, 60, 'Maintenance', 'Hello', 'system', 1, '2026-05-01 15:16:53'),
(39, 61, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 1, '2026-10-07 07:46:14'),
(40, 61, 'Application Under Review', 'Your apartment application has been submitted successfully and is currently undergoing administrative review.', 'system', 1, '2026-10-07 12:20:45'),
(41, 62, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 0, '2026-10-07 13:25:08'),
(42, 61, 'Application Approved!', 'Congratulations! Your apartment application has been approved. Please review and accept your Lease Contract to proceed to Initial Payments. A room will be assigned once payments are settled.', 'approval', 1, '2026-10-07 13:38:16'),
(43, 61, 'Room Assigned!', 'Congratulations! You have been assigned to Room B3-30 in Building 3. Your account has been upgraded to Tenant.', 'approval', 1, '2026-10-07 14:49:44'),
(44, 61, 'Parking Request Submitted', 'Your parking application for 1 vehicle(s) has been successfully submitted and is under review.', 'system', 1, '2026-10-07 15:37:55'),
(45, 63, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 0, '2026-10-09 16:28:28'),
(46, 1, 'Burial Request Received: #BR-20261015-993C', 'Assalamu Alaikum. Your burial assistance request #BR-20261015-993C for Sulaiman Tariq Macarambon has been successfully submitted. Damayan staff will review and prepare the Islamic funeral arrangements shortly.', 'burial', 0, '2026-10-15 14:07:58'),
(47, 1, 'Burial Request Received: #BR-20261015-2334', 'Assalamu Alaikum. Your burial assistance request #BR-20261015-2334 for Sulaiman Tariq Macarambon has been successfully submitted. Damayan staff will review and prepare the Islamic funeral arrangements shortly.', 'burial', 0, '2026-10-15 14:10:53'),
(48, 1, 'Action Required: Additional Information for #BR-20261015-2334', 'Damayan coordinator Ethan Santos requested additional details for your burial request (#BR-20261015-2334): \"Please provide the official hospital certificate of death or embalming waiver for Islamic immediate burial.\". Please visit your Burial Details page to submit the requirements.', 'burial', 0, '2026-10-15 14:10:53'),
(49, 1, 'Requirements Received: #BR-20261015-2334', 'Thank you. Your additional information for burial request #BR-20261015-2334 has been submitted to the Damayan team for review.', 'burial', 0, '2026-10-15 14:10:53'),
(50, 1, 'Burial Request Accepted: #BR-20261015-2334', 'Assalamu Alaikum. Your burial assistance request #BR-20261015-2334 for Sulaiman Tariq Macarambon has been ACCEPTED for coordination. Coordinator assigned: Ethan Santos.', 'burial', 0, '2026-10-15 14:10:53'),
(51, 1, 'Ghusl Scheduled: #BR-20261015-2334', 'The ritual washing (Ghusl) for Sulaiman Tariq Macarambon has been scheduled on 2026-10-15 at 10:30 AM at ISCAG Ghusl Facility, Main Complex.', 'burial', 0, '2026-10-15 14:10:53'),
(52, 1, 'Janazah Prayer Scheduled: #BR-20261015-2334', 'Salat al-Janazah (funeral prayer) for Sulaiman Tariq Macarambon has been scheduled on 2026-10-15 (After Dhuhr) at ISCAG Main Masjid, Taguig City. Officiated by Imam: Ustadh Omar Farooq.', 'burial', 0, '2026-10-15 14:10:53'),
(53, 1, 'Burial Scheduled: #BR-20261015-2334', 'The burial for Sulaiman Tariq Macarambon has been scheduled at Norzagaray Muslim Cemetery, Bulacan (Plot: Section B, Plot 42) on 2026-10-15 at 3:30 PM.', 'burial', 0, '2026-10-15 14:10:53'),
(54, 1, 'Burial Service Completed: #BR-20261015-2334', 'Assalamu Alaikum. The Islamic funeral and burial services for Sulaiman Tariq Macarambon have been completed at Norzagaray Muslim Cemetery, Bulacan. May Allah (SWT) grant them forgiveness, elevate their status in Jannatul Firdaus, and grant patience and comfort to the bereaved family.', 'burial', 0, '2026-10-15 14:10:53'),
(55, 1, 'Burial Request Received: #BR-20261015-9BDC', 'Assalamu Alaikum. Your burial assistance request #BR-20261015-9BDC for Sulaiman Tariq Macarambon has been successfully submitted. Damayan staff will review and prepare the Islamic funeral arrangements shortly.', 'burial', 0, '2026-10-15 16:03:07'),
(56, 1, 'Action Required: Additional Information for #BR-20261015-9BDC', 'Damayan coordinator Ethan Santos requested additional details for your burial request (#BR-20261015-9BDC): \"Please provide the official hospital certificate of death or embalming waiver for Islamic immediate burial.\". Please visit your Burial Details page to submit the requirements.', 'burial', 0, '2026-10-15 16:03:07'),
(57, 1, 'Requirements Received: #BR-20261015-9BDC', 'Thank you. Your additional information for burial request #BR-20261015-9BDC has been submitted to the Damayan team for review.', 'burial', 0, '2026-10-15 16:03:07'),
(58, 1, 'Burial Request Accepted: #BR-20261015-9BDC', 'Assalamu Alaikum. Your burial assistance request #BR-20261015-9BDC for Sulaiman Tariq Macarambon has been ACCEPTED for coordination. Coordinator assigned: Ethan Santos.', 'burial', 0, '2026-10-15 16:03:07'),
(59, 1, 'Ghusl Scheduled: #BR-20261015-9BDC', 'The ritual washing (Ghusl) for Sulaiman Tariq Macarambon has been scheduled on 2026-10-15 at 10:30 AM at ISCAG Ghusl Facility, Main Complex.', 'burial', 0, '2026-10-15 16:03:08'),
(60, 1, 'Janazah Prayer Scheduled: #BR-20261015-9BDC', 'Salat al-Janazah (funeral prayer) for Sulaiman Tariq Macarambon has been scheduled on 2026-10-15 (After Dhuhr) at ISCAG Main Masjid, Taguig City. Officiated by Imam: Ustadh Omar Farooq.', 'burial', 0, '2026-10-15 16:03:08'),
(61, 1, 'Burial Scheduled: #BR-20261015-9BDC', 'The burial for Sulaiman Tariq Macarambon has been scheduled at Norzagaray Muslim Cemetery, Bulacan (Plot: Section B, Plot 42) on 2026-10-15 at 3:30 PM.', 'burial', 0, '2026-10-15 16:03:08'),
(62, 1, 'Burial Service Completed: #BR-20261015-9BDC', 'Assalamu Alaikum. The Islamic funeral and burial services for Sulaiman Tariq Macarambon have been completed at Norzagaray Muslim Cemetery, Bulacan. May Allah (SWT) grant them forgiveness, elevate their status in Jannatul Firdaus, and grant patience and comfort to the bereaved family.', 'burial', 0, '2026-10-15 16:03:08'),
(63, 1, 'Application Received: #DC-20261015-D44F', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-D44F.', 'damayan', 0, '2026-10-15 17:40:01'),
(64, 1, 'Application Received: #DC-20261015-9E6E', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-9E6E.', 'damayan', 0, '2026-10-15 17:43:16'),
(65, 1, 'Application Received: #DC-20261015-F4D8', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-F4D8.', 'damayan', 0, '2026-10-15 17:44:36'),
(66, 1, 'Death Certificate Update: #DC-20261015-F4D8', 'Your death certificate request (#DC-20261015-F4D8) status is now: Under Review.', 'damayan', 0, '2026-10-15 17:44:36'),
(67, 1, 'Death Certificate Update: #DC-20261015-F4D8', 'Your death certificate request (#DC-20261015-F4D8) status is now: Awaiting Authorized Certificate Preparation.', 'damayan', 0, '2026-10-15 17:44:36'),
(68, 1, 'Death Certificate Update: #DC-20261015-F4D8', 'Your death certificate request (#DC-20261015-F4D8) status is now: Certificate Prepared and Certified.', 'damayan', 0, '2026-10-15 17:44:36'),
(69, 1, 'Death Certificate Update: #DC-20261015-F4D8', 'Your death certificate request (#DC-20261015-F4D8) status is now: Submitted for Civil Registration.', 'damayan', 0, '2026-10-15 17:44:36'),
(70, 1, 'Death Certificate Update: #DC-20261015-F4D8', 'Your death certificate request (#DC-20261015-F4D8) status is now: Registered. Official Local Civil Registry No: LCRO-2026-44810.', 'damayan', 0, '2026-10-15 17:44:36'),
(71, 1, 'Cemetery Plot Update: #DC-20261015-F4D8', 'Your cemetery plot request (#DC-20261015-F4D8) status is now: Plot Assigned. Assigned Location: ISCAG Designated Muslim Cemetery, Section A, Plot Number: A-03.', 'damayan', 0, '2026-10-15 17:44:36'),
(72, 1, 'Application Received: #DC-20261015-B57D', 'Assalamu Alaikum. Your request for Cemetery Plot for Faris Mansoor has been successfully submitted. Reference Number: #DC-20261015-B57D.', 'damayan', 0, '2026-10-15 17:44:36'),
(73, 1, 'Application Received: #DC-20261015-2DC1', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-2DC1.', 'damayan', 0, '2026-10-15 17:49:26'),
(74, 1, 'Death Certificate Update: #DC-20261015-2DC1', 'Your death certificate request (#DC-20261015-2DC1) status is now: Under Review.', 'damayan', 0, '2026-10-15 17:49:26'),
(75, 1, 'Death Certificate Update: #DC-20261015-2DC1', 'Your death certificate request (#DC-20261015-2DC1) status is now: Awaiting Authorized Certificate Preparation.', 'damayan', 0, '2026-10-15 17:49:26'),
(76, 1, 'Death Certificate Update: #DC-20261015-2DC1', 'Your death certificate request (#DC-20261015-2DC1) status is now: Certificate Prepared and Certified.', 'damayan', 0, '2026-10-15 17:49:26'),
(77, 1, 'Death Certificate Update: #DC-20261015-2DC1', 'Your death certificate request (#DC-20261015-2DC1) status is now: Submitted for Civil Registration.', 'damayan', 0, '2026-10-15 17:49:26'),
(78, 1, 'Death Certificate Update: #DC-20261015-2DC1', 'Your death certificate request (#DC-20261015-2DC1) status is now: Registered. Official Local Civil Registry No: LCRO-2026-44810.', 'damayan', 0, '2026-10-15 17:49:26'),
(79, 1, 'Cemetery Plot Update: #DC-20261015-2DC1', 'Your cemetery plot request (#DC-20261015-2DC1) status is now: Plot Assigned. Assigned Location: ISCAG Designated Muslim Cemetery, Section A, Plot Number: A-04.', 'damayan', 0, '2026-10-15 17:49:26'),
(80, 1, 'Application Received: #DC-20261015-055C', 'Assalamu Alaikum. Your request for Cemetery Plot for Faris Mansoor has been successfully submitted. Reference Number: #DC-20261015-055C.', 'damayan', 0, '2026-10-15 17:49:26'),
(81, 1, 'Application Received: #DC-20261015-3A75', 'Assalamu Alaikum. Your request for Death Certificate Processing for Tariq Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-3A75.', 'damayan', 0, '2026-10-15 17:49:26'),
(82, 1, 'Application Received: #DC-20261015-97F6', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-97F6.', 'damayan', 0, '2026-10-15 18:14:22'),
(83, 1, 'Death Certificate Update: #DC-20261015-97F6', 'Your death certificate request (#DC-20261015-97F6) status is now: Under Review.', 'damayan', 0, '2026-10-15 18:14:22'),
(84, 1, 'Death Certificate Update: #DC-20261015-97F6', 'Your death certificate request (#DC-20261015-97F6) status is now: Awaiting Authorized Certificate Preparation.', 'damayan', 0, '2026-10-15 18:14:22'),
(85, 1, 'Death Certificate Update: #DC-20261015-97F6', 'Your death certificate request (#DC-20261015-97F6) status is now: Certificate Prepared and Certified.', 'damayan', 0, '2026-10-15 18:14:22'),
(86, 1, 'Death Certificate Update: #DC-20261015-97F6', 'Your death certificate request (#DC-20261015-97F6) status is now: Submitted for Civil Registration.', 'damayan', 0, '2026-10-15 18:14:22'),
(87, 1, 'Death Certificate Update: #DC-20261015-97F6', 'Your death certificate request (#DC-20261015-97F6) status is now: Registered. Official Local Civil Registry No: LCRO-2026-44810.', 'damayan', 0, '2026-10-15 18:14:22'),
(88, 1, 'Cemetery Plot Update: #DC-20261015-97F6', 'Your cemetery plot request (#DC-20261015-97F6) status is now: Plot Assigned. Assigned Location: ISCAG Designated Muslim Cemetery, Section A, Plot Number: A-05.', 'damayan', 0, '2026-10-15 18:14:22'),
(89, 1, 'Application Received: #DC-20261015-3EC2', 'Assalamu Alaikum. Your request for Cemetery Plot for Faris Mansoor has been successfully submitted. Reference Number: #DC-20261015-3EC2.', 'damayan', 0, '2026-10-15 18:14:22'),
(90, 1, 'Application Received: #DC-20261015-4448', 'Assalamu Alaikum. Your request for Death Certificate Processing for Tariq Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-4448.', 'damayan', 0, '2026-10-15 18:14:22'),
(91, 1, 'Application Received: #DC-20261015-8F14', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-8F14.', 'damayan', 0, '2026-10-15 18:15:32'),
(92, 1, 'Death Certificate Update: #DC-20261015-8F14', 'Your death certificate request (#DC-20261015-8F14) status is now: Under Review.', 'damayan', 0, '2026-10-15 18:15:32'),
(93, 1, 'Death Certificate Update: #DC-20261015-8F14', 'Your death certificate request (#DC-20261015-8F14) status is now: Awaiting Authorized Certificate Preparation.', 'damayan', 0, '2026-10-15 18:15:32'),
(94, 1, 'Death Certificate Update: #DC-20261015-8F14', 'Your death certificate request (#DC-20261015-8F14) status is now: Certificate Prepared and Certified.', 'damayan', 0, '2026-10-15 18:15:32'),
(95, 1, 'Death Certificate Update: #DC-20261015-8F14', 'Your death certificate request (#DC-20261015-8F14) status is now: Submitted for Civil Registration.', 'damayan', 0, '2026-10-15 18:15:32'),
(96, 1, 'Death Certificate Update: #DC-20261015-8F14', 'Your death certificate request (#DC-20261015-8F14) status is now: Registered. Official Local Civil Registry No: LCRO-2026-44810.', 'damayan', 0, '2026-10-15 18:15:32'),
(97, 1, 'Cemetery Plot Update: #DC-20261015-8F14', 'Your cemetery plot request (#DC-20261015-8F14) status is now: Plot Assigned. Assigned Location: ISCAG Designated Muslim Cemetery, Section A, Plot Number: A-06.', 'damayan', 0, '2026-10-15 18:15:32'),
(98, 1, 'Application Received: #DC-20261015-8EEE', 'Assalamu Alaikum. Your request for Cemetery Plot for Faris Mansoor has been successfully submitted. Reference Number: #DC-20261015-8EEE.', 'damayan', 0, '2026-10-15 18:15:32'),
(99, 1, 'Application Received: #DC-20261015-6BB5', 'Assalamu Alaikum. Your request for Death Certificate Processing for Tariq Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-6BB5.', 'damayan', 0, '2026-10-15 18:15:32'),
(100, 1, 'Application Received: #DC-20261015-80D2', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Tariq Kareem Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-80D2.', 'damayan', 0, '2026-10-15 18:16:10'),
(101, 1, 'Death Certificate Update: #DC-20261015-80D2', 'Your death certificate request (#DC-20261015-80D2) status is now: Under Review.', 'damayan', 0, '2026-10-15 18:16:10'),
(102, 1, 'Death Certificate Update: #DC-20261015-80D2', 'Your death certificate request (#DC-20261015-80D2) status is now: Awaiting Authorized Certificate Preparation.', 'damayan', 0, '2026-10-15 18:16:10'),
(103, 1, 'Death Certificate Update: #DC-20261015-80D2', 'Your death certificate request (#DC-20261015-80D2) status is now: Certificate Prepared and Certified.', 'damayan', 0, '2026-10-15 18:16:10'),
(104, 1, 'Death Certificate Update: #DC-20261015-80D2', 'Your death certificate request (#DC-20261015-80D2) status is now: Submitted for Civil Registration.', 'damayan', 0, '2026-10-15 18:16:10'),
(105, 1, 'Death Certificate Update: #DC-20261015-80D2', 'Your death certificate request (#DC-20261015-80D2) status is now: Registered. Official Local Civil Registry No: LCRO-2026-44810.', 'damayan', 0, '2026-10-15 18:16:10'),
(106, 1, 'Cemetery Plot Update: #DC-20261015-80D2', 'Your cemetery plot request (#DC-20261015-80D2) status is now: Plot Assigned. Assigned Location: ISCAG Designated Muslim Cemetery, Section A, Plot Number: A-07.', 'damayan', 0, '2026-10-15 18:16:10'),
(107, 1, 'Application Received: #DC-20261015-7480', 'Assalamu Alaikum. Your request for Cemetery Plot for Faris Mansoor has been successfully submitted. Reference Number: #DC-20261015-7480.', 'damayan', 0, '2026-10-15 18:16:10'),
(108, 1, 'Application Received: #DC-20261015-33AA', 'Assalamu Alaikum. Your request for Death Certificate Processing for Tariq Al-Husseini has been successfully submitted. Reference Number: #DC-20261015-33AA.', 'damayan', 0, '2026-10-15 18:16:10'),
(109, 60, 'Application Received: #DC-REG-20261015-CF96', 'Assalamu Alaikum. Your request for Death Certificate Registration for  has been successfully submitted. Reference Number: #DC-REG-20261015-CF96.', 'damayan', 0, '2026-10-15 20:17:37'),
(110, 60, 'Application Received: #DC-REG-20261015-7C08', 'Assalamu Alaikum. Your request for Death Certificate Registration for  has been successfully submitted. Reference Number: #DC-REG-20261015-7C08.', 'damayan', 0, '2026-10-15 20:18:21'),
(111, 60, 'Death Certificate Update: #DC-REG-20261015-7C08', 'Your death certificate request (#DC-REG-20261015-7C08) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-2026-9876.', 'damayan', 0, '2026-10-15 20:18:21'),
(112, 60, 'Application Received: #DC-REG-20261015-88BF', 'Assalamu Alaikum. Your request for Death Certificate Registration for  has been successfully submitted. Reference Number: #DC-REG-20261015-88BF.', 'damayan', 0, '2026-10-15 20:18:54'),
(113, 60, 'Death Certificate Update: #DC-REG-20261015-88BF', 'Your death certificate request (#DC-REG-20261015-88BF) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-2026-9876.', 'damayan', 0, '2026-10-15 20:18:54'),
(114, 60, 'Application Received: #DC-CPY-20261015-E277', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for  has been successfully submitted. Reference Number: #DC-CPY-20261015-E277.', 'damayan', 0, '2026-10-15 20:18:54'),
(115, 60, 'Application Received: #DC-REG-20261015-811A', 'Assalamu Alaikum. Your request for Death Certificate Registration for  has been successfully submitted. Reference Number: #DC-REG-20261015-811A.', 'damayan', 0, '2026-10-15 20:20:21'),
(116, 60, 'Death Certificate Update: #DC-REG-20261015-811A', 'Your death certificate request (#DC-REG-20261015-811A) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-1792095621.', 'damayan', 0, '2026-10-15 20:20:21'),
(117, 60, 'Application Received: #DC-CPY-20261015-1CDE', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for  has been successfully submitted. Reference Number: #DC-CPY-20261015-1CDE.', 'damayan', 0, '2026-10-15 20:20:21'),
(118, 60, 'Certificate Copy Ready / Issued: #DC-CPY-20261015-1CDE', 'Your requested death certificate copy has been processed and issued. Reference #: PSA-CPY-2026-7788. Please confirm receipt once you receive it.', 'damayan', 0, '2026-10-15 20:20:21'),
(119, 60, 'Application Received: #DC-REG-20261015-944C', 'Assalamu Alaikum. Your request for Death Certificate Registration for  has been successfully submitted. Reference Number: #DC-REG-20261015-944C.', 'damayan', 0, '2026-10-15 20:22:29'),
(120, 60, 'Death Certificate Update: #DC-REG-20261015-944C', 'Your death certificate request (#DC-REG-20261015-944C) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-1792095749.', 'damayan', 0, '2026-10-15 20:22:29'),
(121, 60, 'Application Received: #DC-CPY-20261015-DA73', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for  has been successfully submitted. Reference Number: #DC-CPY-20261015-DA73.', 'damayan', 0, '2026-10-15 20:22:29'),
(122, 60, 'Certificate Copy Ready / Issued: #DC-CPY-20261015-DA73', 'Your requested death certificate copy has been processed and issued. Reference #: PSA-CPY-2026-7788. Please confirm receipt once you receive it.', 'damayan', 0, '2026-10-15 20:22:29'),
(123, 60, 'Application Received: #DC-REG-20261015-55A2', 'Assalamu Alaikum. Your request for Death Certificate Registration for Tariq_1792095900 Ahmad Al-Mansoor has been successfully submitted. Reference Number: #DC-REG-20261015-55A2.', 'damayan', 0, '2026-10-15 20:25:00'),
(124, 60, 'Death Certificate Update: #DC-REG-20261015-55A2', 'Your death certificate request (#DC-REG-20261015-55A2) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-1792095900.', 'damayan', 0, '2026-10-15 20:25:00'),
(125, 60, 'Application Received: #DC-CPY-20261015-E6F4', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Tariq_1792095900  Al-Mansoor has been successfully submitted. Reference Number: #DC-CPY-20261015-E6F4.', 'damayan', 0, '2026-10-15 20:25:00'),
(126, 60, 'Certificate Copy Ready / Issued: #DC-CPY-20261015-E6F4', 'Your requested death certificate copy has been processed and issued. Reference #: PSA-CPY-2026-7788. Please confirm receipt once you receive it.', 'damayan', 0, '2026-10-15 20:25:00'),
(127, 60, 'Application Received: #DC-REG-20261015-7566', 'Assalamu Alaikum. Your request for Death Certificate Registration for Tariq_1792096111 Ahmad Al-Mansoor has been successfully submitted. Reference Number: #DC-REG-20261015-7566.', 'damayan', 0, '2026-10-15 20:28:31'),
(128, 60, 'Death Certificate Update: #DC-REG-20261015-7566', 'Your death certificate request (#DC-REG-20261015-7566) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-1792096111.', 'damayan', 0, '2026-10-15 20:28:31'),
(129, 60, 'Application Received: #DC-CPY-20261015-099B', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Tariq_1792096111  Al-Mansoor has been successfully submitted. Reference Number: #DC-CPY-20261015-099B.', 'damayan', 0, '2026-10-15 20:28:31'),
(130, 60, 'Certificate Copy Ready / Issued: #DC-CPY-20261015-099B', 'Your requested death certificate copy has been processed and issued. Reference #: PSA-CPY-2026-7788. Please confirm receipt once you receive it.', 'damayan', 0, '2026-10-15 20:28:31'),
(131, 60, 'Application Received: #DC-REG-20261015-E44F', 'Assalamu Alaikum. Your request for Death Certificate Registration for Tariq_1792096615 Ahmad Al-Mansoor has been successfully submitted. Reference Number: #DC-REG-20261015-E44F.', 'damayan', 0, '2026-10-15 20:36:55'),
(132, 60, 'Death Certificate Update: #DC-REG-20261015-E44F', 'Your death certificate request (#DC-REG-20261015-E44F) status is now: Registered. Official Local Civil Registry No: LCRO-MNL-1792096615.', 'damayan', 0, '2026-10-15 20:36:55'),
(133, 60, 'Application Received: #DC-CPY-20261015-6809', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Tariq_1792096615  Al-Mansoor has been successfully submitted. Reference Number: #DC-CPY-20261015-6809.', 'damayan', 0, '2026-10-15 20:36:55'),
(134, 60, 'Certificate Copy Ready / Issued: #DC-CPY-20261015-6809', 'Your requested death certificate copy has been processed and issued. Reference #: PSA-CPY-2026-7788. Please confirm receipt once you receive it.', 'damayan', 0, '2026-10-15 20:36:55'),
(135, 60, 'Application Received: #DC-CPY-20261016-0F9F', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Marwan Test Al-Mansoor has been successfully submitted. Reference Number: #DC-CPY-20261016-0F9F.', 'damayan', 0, '2026-10-16 09:43:53'),
(136, 61, 'Application Received: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Art Denver Dacer has been successfully submitted. Reference Number: #DC-CPY-20261016-7F38.', 'damayan', 1, '2026-10-16 09:52:31'),
(137, 61, 'Application Received: #DC-CPY-20261016-C2DB', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Juan Dela Cruz has been successfully submitted. Reference Number: #DC-CPY-20261016-C2DB.', 'damayan', 1, '2026-10-16 12:38:40'),
(138, 61, 'Death Certificate Update: #DC-CPY-20261016-C2DB', 'Your death certificate request (#DC-CPY-20261016-C2DB) status is now: Under Review.', 'damayan', 1, '2026-10-16 13:05:45'),
(139, 61, 'Cemetery Plot Update: #DC-CPY-20261016-C2DB', 'Your cemetery plot request (#DC-CPY-20261016-C2DB) status is now: Plot Assigned. Assigned Location: ISCAG Designated Muslim Cemetery, Section B, Plot Number: B-10.', 'damayan', 1, '2026-10-16 13:06:11'),
(140, 1, 'Application Received: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Juan Duplicate Dela Cruz 1792179924 has been successfully submitted. Reference Number: #DC-REG-20261016-6543.', 'damayan', 0, '2026-10-16 19:45:24'),
(141, 1, 'Application Received: #DC-REG-20261016-6505', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Juan Duplicate Dela Cruz 1792179975 has been successfully submitted. Reference Number: #DC-REG-20261016-6505.', 'damayan', 0, '2026-10-16 19:46:15'),
(142, 1, 'Application Received: #DC-REG-20261016-9206', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Juan Duplicate Dela Cruz 1792179975 has been successfully submitted. Reference Number: #DC-REG-20261016-9206.', 'damayan', 0, '2026-10-16 19:46:15'),
(143, 1, 'Request Accepted: #DC-REG-20261016-6505', 'Assalamu Alaikum. Your request #DC-REG-20261016-6505 for Juan Duplicate Dela Cruz 1792179975 has been accepted by Damayan staff and is now being processed.', 'damayan', 0, '2026-10-16 19:46:15'),
(144, 1, 'Certificate Collection Scheduled: #DC-REG-20261016-6505', 'Assalamu Alaikum. Your death certificate for Juan Duplicate Dela Cruz 1792179975 is scheduled to be available for collection on October 25, 2026 at 10:30 AM at the Damayan office. Instructions: Please proceed to Damayan Department Room 102 and bring a valid government ID.', 'damayan', 0, '2026-10-16 19:46:15'),
(145, 1, 'Certificate Ready for Collection: #DC-REG-20261016-6505', 'Assalamu Alaikum. Your death certificate for Juan Duplicate Dela Cruz 1792179975 is now printed and ready for physical collection at the Damayan office.', 'damayan', 0, '2026-10-16 19:46:15'),
(146, 1, 'Certificate Collection Confirmed: #DC-REG-20261016-6505', 'Collection of death certificate for Juan Duplicate Dela Cruz 1792179975 confirmed. Thank you.', 'damayan', 0, '2026-10-16 19:46:15'),
(147, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 15:54:54'),
(148, 61, 'Request Update: #DC-CPY-20261016-7F38 Rejected', 'Your request (#DC-CPY-20261016-7F38) was rejected. Reason: Test rejection for verification. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 15:54:54'),
(149, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 15:54:54'),
(150, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 15:58:09'),
(151, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 15:58:13'),
(152, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 15:58:15'),
(153, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 15:58:18'),
(154, 61, 'Request Accepted: #DC-CPY-20261016-C2DB', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-C2DB) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 15:58:21'),
(155, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 15:58:23'),
(156, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 15:59:23'),
(157, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 16:17:30'),
(158, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 16:17:36'),
(159, 1, 'Request Update: #DC-REG-20261016-6543 Rejected', 'Your request (#DC-REG-20261016-6543) was rejected. Reason: asdsad. Please contact the Damayan office if you have questions.', 'damayan', 0, '2026-10-17 16:17:55'),
(160, 61, 'Request Update: #DC-CPY-20261016-C2DB Rejected', 'Your request (#DC-CPY-20261016-C2DB) was rejected. Reason: asdsadsad. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 16:18:00'),
(161, 61, 'Request Update: #DC-CPY-20261016-7F38 Rejected', 'Your request (#DC-CPY-20261016-7F38) was rejected. Reason: sadsadsa. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 16:18:04'),
(162, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 16:19:13'),
(163, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been approved and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 16:20:12'),
(164, 61, 'Request Accepted: #DC-CPY-20261016-C2DB', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-C2DB) has been approved and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 16:20:20'),
(165, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 16:57:32'),
(166, 61, 'Request Update: #DC-CPY-20261016-7F38 Rejected', 'Your request (#DC-CPY-20261016-7F38) was rejected. Reason: Information does not match the physical death certificate. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 16:57:32'),
(167, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 16:58:13'),
(168, 61, 'Request Update: #DC-CPY-20261016-7F38 Rejected', 'Your request (#DC-CPY-20261016-7F38) was rejected. Reason: Information does not match the physical death certificate. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 16:58:13'),
(169, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 17:05:11'),
(170, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 17:05:20'),
(171, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 17:05:24'),
(172, 61, 'Request Accepted: #DC-CPY-20261016-7F38', 'Assalamu Alaikum. Your request (#DC-CPY-20261016-7F38) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-17 17:05:33'),
(173, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been verified and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 17:28:26'),
(174, 1, 'Request Accepted: #DC-REG-20261016-6543', 'Assalamu Alaikum. Your request (#DC-REG-20261016-6543) has been verified and accepted for processing by Damayan staff.', 'damayan', 0, '2026-10-17 17:31:11'),
(175, 61, 'Request Update: #DC-CPY-20261016-C2DB Rejected', 'Your request (#DC-CPY-20261016-C2DB) was rejected. Reason: Duplicate request. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 17:32:37'),
(176, 61, 'Request Update: #DC-CPY-20261016-7F38 Rejected', 'Your request (#DC-CPY-20261016-7F38) was rejected. Reason: Invalid or incorrect registration number. Please contact the Damayan office if you have questions.', 'damayan', 1, '2026-10-17 17:52:24'),
(177, 61, 'Application Received: #DC-REG-20261018-76DC', 'Assalamu Alaikum. Your request for Death Certificate & Cemetery Plot for Art Denver Dacer has been successfully submitted. Reference Number: #DC-REG-20261018-76DC.', 'damayan', 1, '2026-10-18 09:07:58'),
(178, 61, 'Request Accepted: #DC-REG-20261018-76DC', 'Assalamu Alaikum. Your request (#DC-REG-20261018-76DC) has been verified and accepted for processing by Damayan staff.', 'damayan', 1, '2026-10-18 09:53:08'),
(179, 1, 'Charity Donation Status: Available (DON-2026-0012)', 'Your digital donation (DON-2026-0012) has been officially verified and confirmed by ISCAG Damayan staff. Jazakumullahu Khayran!', 'charity', 0, '2026-10-18 14:03:58'),
(180, 31, 'Counseling Request Submitted', 'Your counseling consultation request (Family / Marital Issues) has been received under #MC-0001.', 'info', 0, '2026-10-18 19:26:28'),
(182, 31, 'Counseling Request Accepted', 'As-salamu alaykum. Your counseling request (#MC-0001) has been accepted by the Da\'wah Department. Our administration is preparing your session details.', 'success', 0, '2026-10-19 12:43:59'),
(183, 60, 'Marriage Reservation Accepted', 'As-salamu alaykum. Your marriage ceremony reservation (#MR-0001) for Ryan Felizardo & Amina Santos has been accepted for Nov 20, 2026 at 10:00:00 at ISCAG Main Mosque.', 'success', 0, '2026-10-19 19:17:57'),
(184, 60, 'Conversion Request Accepted', 'Alhamdulillah! Your Conversion to Islam (Shahadah) request (#CR-0001) for Rayyan ISCAG has been accepted. You can view your Certificate from your account.', 'success', 0, '2026-10-19 19:21:21'),
(185, 31, 'Counseling Appointment Confirmed', 'As-salamu alaykum. Your counseling appointment (#MC-0001) has been confirmed for Oct 22, 2026 at 10:00. Ustaz Abdullah Al-Mansoor has been assigned. Please visit your Counseling Portal to view details.', 'success', 0, '2026-10-19 19:51:18'),
(186, 65, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 0, '2026-10-20 13:12:37'),
(187, 65, 'Female Education Registration Submitted', 'Your registration for Da\'wah Female Section (#EDU-0002) has been submitted successfully and is pending review.', 'info', 0, '2026-10-20 15:52:47'),
(188, 65, 'Female Education Registration Submitted', 'Your registration for Da\'wah Female Section (#EDU-0003) has been submitted successfully and is pending review.', 'info', 0, '2026-10-20 15:52:59'),
(189, 65, 'Female Education Registration Submitted', 'Your registration for Da\'wah Female Section (#EDU-0004) has been submitted successfully and is pending review.', 'info', 0, '2026-10-20 16:43:38'),
(190, 65, 'Islamic Education Enrollment Accepted', 'Your enrollment application (#ER-0002) for Islamic Education - Batch 08 has been accepted. Welcome to the class!', 'success', 0, '2026-10-20 20:40:55'),
(191, 65, 'Islamic Education Enrollment Accepted', 'Your enrollment application (#ER-0003) for Islamic Education - Batch 08 has been accepted. Welcome to the class!', 'success', 0, '2026-10-20 20:40:58'),
(192, 65, 'Islamic Education Enrollment Update', 'Your enrollment application (#ER-0004) for Islamic Education - Batch 08 could not be accommodated at this time.', 'warning', 0, '2026-10-20 20:41:00'),
(193, 65, 'Registration Fee Payment Confirmed', 'Your registration fee (₱120.00) for Level 1 — Basic Arabic & Tajweed has been officially verified and marked as Paid by the Da\'wah Department.', 'success', 0, '2026-10-21 08:40:30'),
(194, 65, 'Registration Fee Payment Confirmed', 'Your registration fee (₱120.00) for Level 1 — Basic Arabic & Tajweed has been officially verified and marked as Paid by the Da\'wah Department.', 'success', 0, '2026-10-21 10:45:52'),
(195, 65, 'Registration Fee Payment Confirmed', 'Your registration fee (₱120.00) for Level 1 — Basic Arabic & Tajweed has been officially verified and marked as Paid by the Da\'wah Department.', 'success', 0, '2026-10-21 10:45:56'),
(196, 31, 'ADASD', 'ASDADSAD', 'payment', 0, '2026-10-21 16:09:27'),
(197, 59, 'ADASD', 'ASDADSAD', 'payment', 0, '2026-10-21 16:09:27'),
(198, 60, 'ADASD', 'ASDADSAD', 'payment', 0, '2026-10-21 16:09:27'),
(199, 61, 'ADASD', 'ASDADSAD', 'payment', 1, '2026-10-21 16:09:27'),
(200, 62, 'ADASD', 'ASDADSAD', 'payment', 0, '2026-10-21 16:09:27'),
(201, 63, 'ADASD', 'ASDADSAD', 'payment', 0, '2026-10-21 16:09:27'),
(202, 65, 'ADASD', 'ASDADSAD', 'payment', 0, '2026-10-21 16:09:27'),
(203, 65, 'Charity Donation Status: Pending Receipt (DON-2026-0039)', 'Your donation (DON-2026-0039) status was updated to Pending Receipt.', 'charity', 0, '2026-10-21 18:26:17'),
(204, 65, 'Charity Donation Status: Available (DON-2026-0039)', 'Your cash donation (DON-2026-0039) has been officially verified and confirmed by ISCAG Damayan staff. Jazakumullahu Khayran!', 'charity', 0, '2026-10-21 18:27:23'),
(205, 61, 'Death Certificate Received: #DC-REG-20261018-76DC', 'Assalamu Alaikum. The death certificate for case #DC-REG-20261018-76DC has been officially recorded as received.', 'damayan', 1, '2026-10-21 19:13:20'),
(206, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on October 30, 2026 at 10:00 AM. Instructions: Please bring 2 valid government IDs and official receipt.', 'damayan', 0, '2026-10-22 07:49:43'),
(207, 61, 'Updated Release Schedule: Death Certificate #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on November 5, 2026 at 2:30 PM. Instructions: Revised instructions: claim at window 2', 'damayan', 0, '2026-10-22 07:49:46'),
(208, 61, 'Claiming Schedule Cancelled: #DC-REG-20261018-76DC', 'Your death certificate release schedule has been cancelled. Reason: Hospital medical record re-examination required. The administration will update your schedule shortly.', 'damayan', 0, '2026-10-22 07:49:50'),
(209, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on November 10, 2026 at 9:00 AM. Instructions: Claim at main desk', 'damayan', 0, '2026-10-22 07:49:54'),
(210, 61, 'Death Certificate Released: #DC-REG-20261018-76DC', 'Your death certificate has been officially released and collected by Fatima Salih (Daughter) on October 22, 2026 9:49 AM.', 'damayan', 0, '2026-10-22 07:49:57'),
(211, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on October 30, 2026 at 10:00 AM. Instructions: Please bring 2 valid government IDs and official receipt.', 'damayan', 0, '2026-10-22 07:52:59'),
(212, 61, 'Updated Release Schedule: Death Certificate #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on November 5, 2026 at 2:30 PM. Instructions: Revised instructions: claim at window 2', 'damayan', 0, '2026-10-22 07:53:02'),
(213, 61, 'Claiming Schedule Cancelled: #DC-REG-20261018-76DC', 'Your death certificate release schedule has been cancelled. Reason: Hospital medical record re-examination required. The administration will update your schedule shortly.', 'damayan', 0, '2026-10-22 07:53:06'),
(214, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on November 10, 2026 at 9:00 AM. Instructions: Claim at main desk', 'damayan', 0, '2026-10-22 07:53:09'),
(215, 61, 'Death Certificate Released: #DC-REG-20261018-76DC', 'Your death certificate has been officially released and collected by Fatima Salih (Daughter) on October 22, 2026 9:53 AM.', 'damayan', 0, '2026-10-22 07:53:13'),
(216, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on October 25, 2026 at 2:30 PM. Instructions: Please bring valid ID', 'damayan', 0, '2026-10-22 09:25:00'),
(217, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on October 25, 2026 at 2:30 PM. Instructions: Please bring valid ID', 'damayan', 0, '2026-10-22 09:29:52'),
(218, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on October 25, 2026 at 2:30 PM. Instructions: Please bring valid ID', 'damayan', 0, '2026-10-22 09:31:52'),
(219, 1, 'Application Received: #DC-CPY-20261022-929A', 'Assalamu Alaikum. Your request for Request Another Death Certificate Copy for Juan Dela Cruz has been successfully submitted. Reference Number: #DC-CPY-20261022-929A.', 'damayan', 0, '2026-10-22 09:33:24'),
(220, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on October 30, 2026 at 10:00 AM. Instructions: Please bring 2 valid government IDs and official receipt.', 'damayan', 0, '2026-10-22 09:58:51'),
(221, 61, 'Updated Release Schedule: Death Certificate #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on November 5, 2026 at 2:30 PM. Instructions: Revised instructions: claim at window 2', 'damayan', 0, '2026-10-22 09:58:55'),
(222, 61, 'Claiming Schedule Cancelled: #DC-REG-20261018-76DC', 'Your death certificate release schedule has been cancelled. Reason: Hospital medical record re-examination required. The administration will update your schedule shortly.', 'damayan', 0, '2026-10-22 09:58:58'),
(223, 61, 'Death Certificate Ready for Release: #DC-REG-20261018-76DC', 'Your Death Certificate Is Ready for Release. Please go to the Damayan Department to claim your death certificate on November 10, 2026 at 9:00 AM. Instructions: Claim at main desk', 'damayan', 0, '2026-10-22 09:59:02'),
(224, 61, 'Death Certificate Released: #DC-REG-20261018-76DC', 'Your death certificate has been officially released and collected by Fatima Salih (Daughter) on October 22, 2026 11:59 AM.', 'damayan', 0, '2026-10-22 09:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `lease_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('Pending','Paid','Failed') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `lease_id`, `tenant_id`, `payment_type`, `amount`, `payment_status`, `payment_date`, `reference_number`, `created_at`, `updated_at`) VALUES
(7, 6, 60, 'Deposit', 1000.00, 'Paid', '2026-05-01 07:36:27', 'PAY-3092918', '2026-04-30 23:36:09', '2026-04-30 23:36:27'),
(8, 6, 60, 'Advance', 2500.00, 'Paid', '2026-05-01 07:36:30', 'PAY-7803835', '2026-04-30 23:36:09', '2026-04-30 23:36:30'),
(20, 6, 60, 'Rent-2026-06', 2500.00, 'Paid', '2026-05-01 09:12:05', 'PAY-7413520', '2026-05-01 01:12:05', '2026-05-01 01:12:05'),
(21, 6, 60, 'Water-2026-06', 100.00, 'Paid', '2026-05-01 09:12:08', 'PAY-5116146', '2026-05-01 01:12:08', '2026-05-01 01:12:08'),
(22, 6, 60, 'Rent-advance', 2500.00, 'Paid', '2026-05-01 20:58:20', 'PAY-ADV-7684737', '2026-05-01 12:58:20', '2026-05-01 12:58:20'),
(23, 6, 60, 'Rent-advance', 2500.00, 'Paid', '2026-05-01 21:22:34', 'PAY-ADV-605984', '2026-05-01 13:22:34', '2026-05-01 13:22:34'),
(24, 6, 60, 'Water-2026-07', 100.00, 'Paid', '2026-05-01 21:23:40', 'PAY-BLK-6093076', '2026-05-01 13:23:40', '2026-05-01 13:23:40'),
(25, 6, 60, 'Contribution-2026-06', 150.00, 'Paid', '2026-05-01 22:08:29', 'PAY-BLK-3922295', '2026-05-01 14:08:29', '2026-05-01 14:08:29'),
(26, 6, 60, 'Contribution-2026-07', 150.00, 'Paid', '2026-05-01 22:08:29', 'PAY-BLK-3922295', '2026-05-01 14:08:29', '2026-05-01 14:08:29'),
(27, 7, 61, 'Deposit', 1000.00, 'Pending', NULL, NULL, '2026-10-07 14:14:29', '2026-10-07 14:14:29'),
(28, 7, 61, 'Advance', 2500.00, 'Pending', NULL, NULL, '2026-10-07 14:14:29', '2026-10-07 14:14:29'),
(29, 7, 61, 'Water-Advance', 100.00, 'Pending', NULL, NULL, '2026-10-07 14:14:29', '2026-10-07 14:14:29'),
(30, 7, 61, 'Contribution-Advance', 150.00, 'Pending', NULL, NULL, '2026-10-07 14:14:29', '2026-10-07 14:14:29');

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
  `is_verified` tinyint(1) DEFAULT 0,
  `profile_picture` longblob DEFAULT NULL,
  `profile_picture_mime` varchar(50) DEFAULT NULL,
  `profile_picture_path` varchar(255) DEFAULT NULL,
  `time_offset` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_accounts`
--

INSERT INTO `tenant_accounts` (`tenant_id`, `first_name`, `last_name`, `sex`, `email`, `contactnum`, `password`, `confirmpass`, `role`, `otp_code`, `otp_expiry`, `is_verified`, `profile_picture`, `profile_picture_mime`, `profile_picture_path`, `time_offset`) VALUES
(31, 'Setsuna', 'Ignacio', 'Male', 'kazekizumi@gmail.com', '+639065740813', '$2y$10$Vtb/RzYdtONp3TKGfv9b3e0qStZoUoNlsPEsZW6E9./NsmfpFTw7O', '$2y$10$rRZsROrvbJ81tKezKq4is.RSE4QalbHtc9XhmdqQSgEnNYumtSyje', 'Staff_Tenant', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(59, 'Norman', 'Ungasin', 'Male', 'kizumikaze1@gmail.com', '+639065740819', '$2y$10$Fm.Qe7d9MwZfybGmJ/awaeHFhwIebTS4SFh0I7krDkbqTr2K8qRVO', '$2y$10$XO4gb1aTve/nzgiFFIH6/.N1tB2G.Y8uxjW9MydpGkPpyI7bJjOUm', 'Admin', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(60, 'Ryan', 'Felizardo', 'Male', 'rjfelizardo25@gmail.com', '+639065740815', '$2y$10$pfWKecIILX4GUvtb/rCLheUr6W8.Qy2G/4eQR72b6j.wI021Ror4e', '$2y$10$pfWKecIILX4GUvtb/rCLheUr6W8.Qy2G/4eQR72b6j.wI021Ror4e', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(61, 'Art', 'Denver', 'Male', 'dacer2314@gmail.com', '+639485269632', '$2y$10$53AbnZ/9zfZJJyA9L.HoM.1AqjeoPnbwaUIK03aojKBsRVnZQjCP6', '$2y$10$53AbnZ/9zfZJJyA9L.HoM.1AqjeoPnbwaUIK03aojKBsRVnZQjCP6', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(62, 'One', 'Dela Cruz', 'Male', 'artdenverforbesdacer@gmail.com', '+639485269631', '$2y$10$K4Wm1eqO8MqCmiNPZAee5ubl0BIEF66gEhXao/dlMm/MaQBFElVui', '$2y$10$K4Wm1eqO8MqCmiNPZAee5ubl0BIEF66gEhXao/dlMm/MaQBFElVui', 'Admin', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(63, 'Ethan', 'Santos', 'Male', 'addysedric30@gmail.com', '0917 123 4567', '$2y$10$VU4OorVtu9jpyinUq1/MFuWMrA73hjGukR5ySOLpO8LbSKhS6Jqe6', '$2y$10$VU4OorVtu9jpyinUq1/MFuWMrA73hjGukR5ySOLpO8LbSKhS6Jqe6', 'Staff_Male', NULL, NULL, 1, NULL, NULL, NULL, NULL),
(65, 'Pamela', 'Brown', 'Female', 'pdacer2@gmail.com', '09102458643', '$2y$10$HSTIrwDoYaO.kklbm5g4COEGCC318FObVvBlUsaoPsomo48IrJxW2', '$2y$10$YQI/DGHvsR4lbQaimBZ0UOjeKZPKIFqlCi9qEjdtMKrlqe1quuXoO', 'Staff_Female', NULL, NULL, 1, NULL, NULL, NULL, NULL);

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
  `civil_status` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `pob` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `tribalaffliation` varchar(100) DEFAULT NULL,
  `numofmuslim` int(11) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `monthly_income` varchar(50) DEFAULT NULL,
  `companyname` varchar(150) DEFAULT NULL,
  `companyadd` text DEFAULT NULL,
  `companyphone` varchar(50) DEFAULT NULL,
  `dateofshahadah` date DEFAULT NULL,
  `ref_name` varchar(255) DEFAULT NULL,
  `ref_contact` varchar(50) DEFAULT NULL,
  `iscag_students` int(11) DEFAULT NULL,
  `iscag_student_names` text DEFAULT NULL,
  `is_iscag_employee` tinyint(1) DEFAULT 0,
  `iscag_job_role` varchar(255) DEFAULT NULL,
  `date_applied` date DEFAULT NULL,
  `family_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_addinfo`
--

INSERT INTO `tenant_addinfo` (`tenant_info`, `tenant_id`, `familyname`, `givenname`, `middlename`, `muslimname`, `civil_status`, `address`, `birthdate`, `pob`, `age`, `sex`, `tribalaffliation`, `numofmuslim`, `occupation`, `monthly_income`, `companyname`, `companyadd`, `companyphone`, `dateofshahadah`, `ref_name`, `ref_contact`, `iscag_students`, `iscag_student_names`, `is_iscag_employee`, `iscag_job_role`, `date_applied`, `family_data`) VALUES
(17, 32, 'wqewqe', 'qwewqewq', 'F', 'wqewqeq', '', 'QWEWQEWQ', '2007-01-08', 'WAEQWEQW', 19, 'Male', 'QWEWQEWQ', 0, 'qweqewq', '', 'Mcdo', 'qweqewq', 'qweqeq', '2026-04-29', 'Inday Sara Duterter', '09194678123', 3, NULL, 0, NULL, '2026-04-24', '[{\"name\":\"Youngstown Tocino\",\"relation\":\"Son\",\"age\":\"22\",\"religion\":\"Islam\"},{\"name\":\"Rodrigo Roa Duterte\",\"relation\":\"Son\",\"age\":\"60\",\"religion\":\"Islam\"}]'),
(19, 0, 'rey', 'mis', '2', 'teryo', 'Single', 'ASDASDASD', '2018-07-25', '22', 7, 'Female', 'aasdas', 2, 'asdasd', '', 'sadsa', 'dsadsad', 'asdsadsa', '2026-04-21', '', '', 0, NULL, 0, NULL, '2026-04-24', '[]'),
(23, 47, 'qweqwe', 'qweqeq', 'F', 'qweqe', 'Single', 'Blk 10 Lot 34', '2000-07-21', 'Albay', 25, 'Male', 'qweqeq', 2, '213131', '', 'Jollibee', 'Blk 10 Lot 34', '09065740819', '0000-00-00', '', '', 0, NULL, 0, NULL, '2026-04-27', '[{\"name\":\"Koy Koy\",\"relation\":\"Son\",\"age\":\"12\",\"religion\":\"Islam\"}]'),
(24, 49, 'Felizardo', 'Ryan', 'Z', 'Hamza', 'Single', 'Blk 10 Lot 34', '1999-07-29', 'Albay', 26, 'Male', 'Maranao', 1, 'Tambay', '', 'Jollibee', 'Mcdo Corp', '09065740812', '2004-07-21', 'Fatima Salazar', '09194678123', 3, NULL, 0, NULL, '2026-04-27', '[{\"name\":\"Alhamdillah Salazar\",\"relation\":\"Son\",\"age\":\"22\",\"religion\":\"Islam\"}]'),
(35, 60, 'Felizardo', 'Ryan', 'J.', '', 'Single', 'Blk 10 Lot 39', '2004-07-22', 'Blk 10 Lot 39', 21, 'Male', 'Kolibugan', 2, 'Nurse', '', 'La Salle Corp', 'Burol Main', '013213567', '0000-00-00', 'Kyline Alcantara', '09194678122', 0, '[]', 0, '', '2026-04-30', '[]'),
(36, 61, 'Denver', 'Art', '', '', 'Single', 'Blk 78 Lot 3, Brgy. San Juan', '2026-10-07', 'Blk 78 Lot 3, Brgy. San Juan', 0, 'Male', '', 0, '123', '', '', '', '', '0000-00-00', '', '', 0, '[]', 0, '', '2026-10-07', '[]');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_addinfo_images`
--

CREATE TABLE `tenant_addinfo_images` (
  `id` int(11) NOT NULL,
  `addinfo_id` int(11) DEFAULT NULL,
  `image` longblob DEFAULT NULL,
  `mime_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `doc_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_addinfo_images`
--

INSERT INTO `tenant_addinfo_images` (`id`, `addinfo_id`, `image`, `mime_type`, `created_at`, `doc_type`, `file_path`) VALUES
(2, 0, NULL, 'image/png', '2026-04-27 11:16:50', 'proofofincome', 'uploads/tenants/doc_2_1777301970.png'),
(3, 0, NULL, 'image/png', '2026-04-27 11:16:55', 'valididback', 'uploads/tenants/doc_3_1777301970.png'),
(4, 0, NULL, 'image/png', '2026-04-27 11:16:58', 'birthcert', 'uploads/tenants/doc_4_1777301970.png'),
(5, 0, NULL, 'image/png', '2026-04-27 11:17:01', 'nbi', 'uploads/tenants/doc_5_1777301970.png'),
(6, 0, NULL, 'image/png', '2026-04-27 12:02:38', 'valididfront', 'uploads/tenants/doc_6_1777301970.png'),
(7, 0, NULL, 'image/png', '2026-04-27 12:18:30', 'picture', 'uploads/tenants/doc_7_1777301970.png'),
(126, 35, NULL, 'image/png', '2026-04-30 23:30:43', 'picture', 'uploads/tenants/doc_60_picture_1777591843.png'),
(127, 35, NULL, 'image/jpeg', '2026-04-30 23:30:50', 'proofofincome', 'uploads/tenants/doc_60_proofofincome_1777591850.jpg'),
(128, 35, NULL, 'image/jpeg', '2026-04-30 23:30:53', 'valididfront', 'uploads/tenants/doc_60_valididfront_1777591853.jpg'),
(129, 35, NULL, 'image/jpeg', '2026-04-30 23:30:54', 'valididback', 'uploads/tenants/doc_60_valididback_1777591854.jpg'),
(130, 35, NULL, 'image/jpeg', '2026-04-30 23:30:57', 'birthcert', 'uploads/tenants/doc_60_birthcert_1777591857.jpg'),
(131, 35, NULL, 'image/jpeg', '2026-04-30 23:30:59', 'nbi', 'uploads/tenants/doc_60_nbi_1777591859.jpg'),
(132, 36, NULL, 'image/png', '2026-10-07 07:48:17', 'picture', 'uploads/tenants/doc_61_picture_1791359297.png'),
(133, 36, NULL, 'image/png', '2026-10-07 12:20:18', 'proofofincome', 'uploads/tenants/doc_61_proofofincome_1791375618.png'),
(134, 36, NULL, 'image/png', '2026-10-07 12:20:28', 'valididback', 'uploads/tenants/doc_61_valididback_1791375628.png');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_family_members`
--

CREATE TABLE `tenant_family_members` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `relation` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenant_maintenance`
--

CREATE TABLE `tenant_maintenance` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Rejected') DEFAULT 'Pending',
  `admin_remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `signature` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_parking`
--

INSERT INTO `tenant_parking` (`parking_id`, `tenant_id`, `date`, `vehiclename`, `ownername`, `typeofvehicle`, `plateno`, `datestarted`, `signature`, `status`, `remarks`, `updated_at`) VALUES
(8, 61, '2026-10-07', 'Toyota', 'Art Denver Dacer', 'Pickup', 'ABC 123', '2026-10-07', NULL, 'Approved', NULL, '2026-10-07 15:46:18');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_user_profiles`
--

CREATE TABLE `tenant_user_profiles` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `muslim_name` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `revert_year` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_user_profiles`
--

INSERT INTO `tenant_user_profiles` (`id`, `tenant_id`, `muslim_name`, `birthdate`, `civil_status`, `occupation`, `address`, `revert_year`, `created_at`, `updated_at`) VALUES
(1, 25, 'Farhan', '2003-01-22', 'Single', 'qweqeq', 'Blk 10 Lot 39', '2026', '2026-04-19 10:54:04', '2026-04-19 11:17:58'),
(2, 27, 'Fatimah', '2004-04-26', 'Single', 'Social Worker', 'Blk 10 Lot 35', '2005', '2026-04-19 14:33:54', '2026-04-19 14:34:16'),
(3, 28, 'FFF', '2002-12-23', 'Single', '213321321', '123231321', '2026', '2026-04-19 15:05:03', '2026-04-19 15:05:18'),
(4, 47, 'Farhan', '2026-04-16', 'Single', 'Tambay', 'Blk 10 Lot 34', '2001', '2026-04-27 10:55:17', '2026-04-27 10:55:31'),
(6, 49, 'Hamza', '0000-00-00', 'Single', 'N/A', 'Blk 10 Lot 34', '2000', '2026-04-27 11:59:49', '2026-04-27 11:59:49'),
(17, 60, 'N/A', '0000-00-00', 'Single', 'Nurse', 'Blk 10 Lot 39', 'N/A', '2026-04-30 23:26:27', '2026-04-30 23:26:27'),
(18, 61, 'N/A', '2026-10-07', 'Single', '123', 'Blk 78 Lot 3, Brgy. San Juan', 'N/A', '2026-10-07 07:47:49', '2026-10-07 07:47:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_apartmentsapp_tenant` (`tenant_id`);

--
-- Indexes for table `apartment_types`
--
ALTER TABLE `apartment_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `apartment_type_images`
--
ALTER TABLE `apartment_type_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `apartment_type_images_ibfk_1` (`type_id`);

--
-- Indexes for table `apartment_units`
--
ALTER TABLE `apartment_units`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `fk_unit_application` (`application_id`),
  ADD KEY `fk_unit_tenant` (`tenant_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billing_id`);

--
-- Indexes for table `broadcasts`
--
ALTER TABLE `broadcasts`
  ADD PRIMARY KEY (`broadcast_id`);

--
-- Indexes for table `burial_activity_logs`
--
ALTER TABLE `burial_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `burial_request_id` (`burial_request_id`),
  ADD KEY `ref_id` (`ref_id`);

--
-- Indexes for table `burial_coordinations`
--
ALTER TABLE `burial_coordinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `burial_request_id` (`burial_request_id`);

--
-- Indexes for table `burial_requests`
--
ALTER TABLE `burial_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ref_id` (`ref_id`);

--
-- Indexes for table `burial_schedule_history`
--
ALTER TABLE `burial_schedule_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bsh_b_id` (`burial_request_id`),
  ADD KEY `idx_bsh_ref` (`ref_id`);

--
-- Indexes for table `burial_volunteers`
--
ALTER TABLE `burial_volunteers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cemetery_plots`
--
ALTER TABLE `cemetery_plots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_plot` (`cemetery_name`,`section`,`plot_number`);

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
-- Indexes for table `charity_activity_logs`
--
ALTER TABLE `charity_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_act_ref` (`reference_no`),
  ADD KEY `idx_act_type` (`action_type`),
  ADD KEY `idx_act_created` (`created_at`);

--
-- Indexes for table `charity_allocations`
--
ALTER TABLE `charity_allocations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `allocation_ref` (`allocation_ref`),
  ADD KEY `idx_alloc_dept` (`receiving_department`),
  ADD KEY `idx_alloc_status` (`status`),
  ADD KEY `idx_alloc_date` (`allocation_date`),
  ADD KEY `fk_alloc_donation` (`donation_id`);

--
-- Indexes for table `charity_distributions`
--
ALTER TABLE `charity_distributions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distribution_ref` (`distribution_ref`),
  ADD KEY `idx_dist_dept` (`receiving_department`),
  ADD KEY `idx_dist_date` (`distribution_date`),
  ADD KEY `idx_dist_alloc` (`allocation_id`);

--
-- Indexes for table `charity_donations_records`
--
ALTER TABLE `charity_donations_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `donation_ref` (`donation_ref`),
  ADD KEY `idx_donor` (`donor_name`),
  ADD KEY `idx_type` (`donation_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`date_received`);

--
-- Indexes for table `conversion_requests`
--
ALTER TABLE `conversion_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `corpse_disposal`
--
ALTER TABLE `corpse_disposal`
  ADD PRIMARY KEY (`disposal_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `counseling_requests`
--
ALTER TABLE `counseling_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `counselors`
--
ALTER TABLE `counselors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dawah_availability`
--
ALTER TABLE `dawah_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_date_dept` (`blocked_date`,`department`);

--
-- Indexes for table `dawah_death_certificates`
--
ALTER TABLE `dawah_death_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_number` (`registration_number`),
  ADD KEY `date_of_death` (`date_of_death`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `dawah_manual_schedules`
--
ALTER TABLE `dawah_manual_schedules`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `deceased_records`
--
ALTER TABLE `deceased_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `burial_request_id` (`burial_request_id`),
  ADD KEY `ref_id` (`ref_id`);

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
-- Indexes for table `encoded_death_certificates`
--
ALTER TABLE `encoded_death_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `registration_number_2` (`registration_number`),
  ADD KEY `deceased_name` (`deceased_name`),
  ADD KEY `encoded_at` (`encoded_at`);

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
  ADD KEY `idx_tenant_id` (`tenant_id`);

--
-- Indexes for table `funeral_cases`
--
ALTER TABLE `funeral_cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_case` (`case_number`);

--
-- Indexes for table `funeral_case_logs`
--
ALTER TABLE `funeral_case_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_case_log` (`case_id`);

--
-- Indexes for table `funeral_documents`
--
ALTER TABLE `funeral_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_case_doc` (`case_id`);

--
-- Indexes for table `funeral_psa_requests`
--
ALTER TABLE `funeral_psa_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_case_psa` (`case_id`);

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
-- Indexes for table `islamic_education_enrollments`
--
ALTER TABLE `islamic_education_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `islamic_education_sessions`
--
ALTER TABLE `islamic_education_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_enrollment_session` (`enrollment_id`,`session_number`);

--
-- Indexes for table `leases`
--
ALTER TABLE `leases`
  ADD PRIMARY KEY (`lease_id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_app` (`application_id`),
  ADD KEY `idx_status` (`lease_status`);

--
-- Indexes for table `lease_renewals`
--
ALTER TABLE `lease_renewals`
  ADD PRIMARY KEY (`renewal_id`),
  ADD KEY `idx_lease` (`lease_id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_status` (`status`);

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
-- Indexes for table `marriage_requests`
--
ALTER TABLE `marriage_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `marriage_witness`
--
ALTER TABLE `marriage_witness`
  ADD PRIMARY KEY (`witness_id`),
  ADD KEY `fk_marriage_witness_marriage` (`marriage_id`);

--
-- Indexes for table `move_out_requests`
--
ALTER TABLE `move_out_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_moveout_tenant` (`tenant_id`),
  ADD KEY `idx_moveout_unit` (`unit_id`),
  ADD KEY `idx_moveout_status` (`status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_tenant` (`tenant_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_lease` (`lease_id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_status` (`payment_status`);

--
-- Indexes for table `postmortem_certificate`
--
ALTER TABLE `postmortem_certificate`
  ADD PRIMARY KEY (`postmortem_id`),
  ADD UNIQUE KEY `deceased_id` (`deceased_id`);

--
-- Indexes for table `tenant_accounts`
--
ALTER TABLE `tenant_accounts`
  ADD PRIMARY KEY (`tenant_id`);

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
  ADD KEY `fk_tenant_addinfo_tenant` (`tenant_id`);

--
-- Indexes for table `tenant_addinfo_images`
--
ALTER TABLE `tenant_addinfo_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_addinfo_images_ibfk_1` (`addinfo_id`);

--
-- Indexes for table `tenant_family_members`
--
ALTER TABLE `tenant_family_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tenant_maintenance`
--
ALTER TABLE `tenant_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  ADD PRIMARY KEY (`parking_id`),
  ADD KEY `fk_tenant_parking_tenant` (`tenant_id`);

--
-- Indexes for table `tenant_user_profiles`
--
ALTER TABLE `tenant_user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenant_id` (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `apartment_types`
--
ALTER TABLE `apartment_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `apartment_type_images`
--
ALTER TABLE `apartment_type_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `apartment_units`
--
ALTER TABLE `apartment_units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `broadcasts`
--
ALTER TABLE `broadcasts`
  MODIFY `broadcast_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `burial_activity_logs`
--
ALTER TABLE `burial_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `burial_coordinations`
--
ALTER TABLE `burial_coordinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `burial_requests`
--
ALTER TABLE `burial_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `burial_schedule_history`
--
ALTER TABLE `burial_schedule_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `burial_volunteers`
--
ALTER TABLE `burial_volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cemetery_plots`
--
ALTER TABLE `cemetery_plots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
-- AUTO_INCREMENT for table `charity_activity_logs`
--
ALTER TABLE `charity_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `charity_allocations`
--
ALTER TABLE `charity_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `charity_distributions`
--
ALTER TABLE `charity_distributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `charity_donations_records`
--
ALTER TABLE `charity_donations_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `conversion_requests`
--
ALTER TABLE `conversion_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `corpse_disposal`
--
ALTER TABLE `corpse_disposal`
  MODIFY `disposal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counseling_requests`
--
ALTER TABLE `counseling_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `counselors`
--
ALTER TABLE `counselors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dawah_availability`
--
ALTER TABLE `dawah_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dawah_death_certificates`
--
ALTER TABLE `dawah_death_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dawah_manual_schedules`
--
ALTER TABLE `dawah_manual_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `deceased_records`
--
ALTER TABLE `deceased_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
-- AUTO_INCREMENT for table `encoded_death_certificates`
--
ALTER TABLE `encoded_death_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `relation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `female_school`
--
ALTER TABLE `female_school`
  MODIFY `female_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `funeral_cases`
--
ALTER TABLE `funeral_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `funeral_case_logs`
--
ALTER TABLE `funeral_case_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `funeral_documents`
--
ALTER TABLE `funeral_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `funeral_psa_requests`
--
ALTER TABLE `funeral_psa_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `islamic_education_enrollments`
--
ALTER TABLE `islamic_education_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `islamic_education_sessions`
--
ALTER TABLE `islamic_education_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `leases`
--
ALTER TABLE `leases`
  MODIFY `lease_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lease_renewals`
--
ALTER TABLE `lease_renewals`
  MODIFY `renewal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `marriage_requests`
--
ALTER TABLE `marriage_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `marriage_witness`
--
ALTER TABLE `marriage_witness`
  MODIFY `witness_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `move_out_requests`
--
ALTER TABLE `move_out_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `postmortem_certificate`
--
ALTER TABLE `postmortem_certificate`
  MODIFY `postmortem_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_accounts`
--
ALTER TABLE `tenant_accounts`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `tenant_addfam`
--
ALTER TABLE `tenant_addfam`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_addinfo`
--
ALTER TABLE `tenant_addinfo`
  MODIFY `tenant_info` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tenant_addinfo_images`
--
ALTER TABLE `tenant_addinfo_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `tenant_family_members`
--
ALTER TABLE `tenant_family_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_maintenance`
--
ALTER TABLE `tenant_maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  MODIFY `parking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tenant_user_profiles`
--
ALTER TABLE `tenant_user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartment_type_images`
--
ALTER TABLE `apartment_type_images`
  ADD CONSTRAINT `apartment_type_images_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `apartment_types` (`type_id`) ON DELETE CASCADE;

--
-- Constraints for table `burial_coordinations`
--
ALTER TABLE `burial_coordinations`
  ADD CONSTRAINT `burial_coordinations_ibfk_1` FOREIGN KEY (`burial_request_id`) REFERENCES `burial_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `charity_allocations`
--
ALTER TABLE `charity_allocations`
  ADD CONSTRAINT `fk_alloc_donation` FOREIGN KEY (`donation_id`) REFERENCES `charity_donations_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `charity_distributions`
--
ALTER TABLE `charity_distributions`
  ADD CONSTRAINT `fk_dist_allocation` FOREIGN KEY (`allocation_id`) REFERENCES `charity_allocations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversion_requests`
--
ALTER TABLE `conversion_requests`
  ADD CONSTRAINT `conversion_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`);

--
-- Constraints for table `counseling_requests`
--
ALTER TABLE `counseling_requests`
  ADD CONSTRAINT `counseling_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`);

--
-- Constraints for table `funeral_case_logs`
--
ALTER TABLE `funeral_case_logs`
  ADD CONSTRAINT `funeral_case_logs_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `funeral_cases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `funeral_documents`
--
ALTER TABLE `funeral_documents`
  ADD CONSTRAINT `funeral_documents_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `funeral_cases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `funeral_psa_requests`
--
ALTER TABLE `funeral_psa_requests`
  ADD CONSTRAINT `funeral_psa_requests_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `funeral_cases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `islamic_education_enrollments`
--
ALTER TABLE `islamic_education_enrollments`
  ADD CONSTRAINT `islamic_education_enrollments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE SET NULL;

--
-- Constraints for table `islamic_education_sessions`
--
ALTER TABLE `islamic_education_sessions`
  ADD CONSTRAINT `islamic_education_sessions_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `islamic_education_enrollments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marriage_requests`
--
ALTER TABLE `marriage_requests`
  ADD CONSTRAINT `marriage_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`);

--
-- Constraints for table `tenant_maintenance`
--
ALTER TABLE `tenant_maintenance`
  ADD CONSTRAINT `tenant_maintenance_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
