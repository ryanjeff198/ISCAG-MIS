-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 01:14 PM
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
(1, 'Maintenance Request', 'Ryan Felizardo submitted a Electrical maintenance request.', 'request', 'Ryan Felizardo', 60, '/admin/mis_admin/maintenance', 1, '2026-05-02 09:11:35'),
(2, 'Maintenance Request', 'Ryan Felizardo submitted a Electrical maintenance request.', 'request', 'Ryan Felizardo', 60, '/admin/mis_admin/maintenance', 1, '2026-05-02 12:11:22'),
(3, 'Maintenance Request', 'Ryan Felizardo submitted a Pest Control maintenance request.', 'request', 'Ryan Felizardo', 60, '/admin/mis_admin/maintenance', 1, '2026-05-02 12:20:33'),
(4, 'New Application Received', 'Ryan Felizardo has submitted a new apartment application.', 'request', 'Ryan Felizardo', 61, '/admin/apartment/confirmation', 0, '2026-05-02 20:50:45'),
(5, 'Payment Received', 'Ryan Felizardo submitted a payment for: Initial Payment, Initial Payment (Ref: PAY-BLK-6846935).', 'payment', 'Ryan Felizardo', 61, '/admin/mis_admin/billing', 0, '2026-05-02 20:51:38'),
(6, 'Payment Received', 'Ryan Felizardo submitted a payment for: Rent Advance + 4 other items (Ref: PAY-ADV-3297983).', 'payment', 'Ryan Felizardo', 61, '/admin/mis_admin/billing', 0, '2026-05-02 21:05:03'),
(7, 'Payment Received', 'Ryan Felizardo submitted a payment for: Rent Advance, Water Advance, Contribution Advance (Ref: PAY-ADV-3638343).', 'payment', 'Ryan Felizardo', 61, '/admin/mis_admin/billing', 0, '2026-05-02 21:21:58'),
(8, 'Parking Application Received', 'Ryan Felizardo has submitted a parking application for 1 vehicle(s).', 'request', 'Ryan Felizardo', 61, '/admin/mis_admin/parking_approval', 1, '2026-05-02 21:30:53'),
(9, 'Payment Received', 'Ryan Felizardo submitted a payment for: Parking 8 2026 05, Parking 8 2026 06, Parking 8 2026 07 (Ref: PAY-BLK-3274482).', 'payment', 'Ryan Felizardo', 61, '/admin/mis_admin/billing', 0, '2026-05-02 21:32:28'),
(10, 'New Application Received', 'Ryan Felizardo has submitted a new apartment application.', 'request', 'Ryan Felizardo', 62, '/admin/apartment/confirmation', 0, '2026-05-02 21:59:06'),
(11, 'Payment Received', 'Ryan Felizardo submitted a payment for: Initial Payment, Initial Payment (Ref: PAY-BLK-8865257).', 'payment', 'Ryan Felizardo', 62, '/admin/mis_admin/billing', 0, '2026-05-02 22:09:03'),
(12, 'New Application Received', 'Ryan Felizardo has submitted a new apartment application.', 'request', 'Ryan Felizardo', 63, '/admin/apartment/confirmation', 0, '2026-05-02 22:13:06'),
(13, 'Payment Received', 'Ryan Felizardo submitted a payment for: Initial Payment + 3 other items (Ref: PAY-BLK-669919).', 'payment', 'Ryan Felizardo', 63, '/admin/mis_admin/billing', 0, '2026-05-02 22:19:02'),
(14, 'Maintenance Request', 'Ryan Felizardo submitted a Plumbing maintenance request.', 'request', 'Ryan Felizardo', 63, '/admin/mis_admin/maintenance', 0, '2026-05-03 11:05:16');

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
(25, 63, 'Guest Room', 12, '2026-05-03', NULL, 'Assigned', NULL, '2026-05-02 22:19:02', 34, NULL, NULL, '2026-05-03 06:19:02', '2026-05-03 06:19:02', 0);

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
(4, '1tr', 'Transient', 2500.00, '10 pax', 'A transient accommodation designed for short-term stays, typically accommodating 8–10 guests. It may consist of multiple bedrooms or shared sleeping areas, along with common facilities such as a living space, kitchen, and bathroom depending on the setup.\n\n1 month deposit', '', 'shared bedspace', '1 shared', NULL, NULL, 1, 0, '2026-04-21 18:54:28', '2026-05-03 10:54:10', '[\"Wi-Fi\"]', '[]', '1 Month', '1 Month', '', '3 Months', '25th day', ''),
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
(76, 5, 'uploads/apartments/type_5_1777305596.jpg', NULL, 'image/jpeg', '', 0, 6, '2026-04-27 15:59:56'),
(77, 4, 'uploads/apartments/type4_1776797717_1aef8950.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:16'),
(78, 4, 'uploads/apartments/type4_1776797737_3eda471b.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:16'),
(79, 1, 'uploads/apartments/type_1_1777305341.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:16'),
(80, 1, 'uploads/apartments/type_1_1777305349.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:16'),
(81, 4, 'uploads/apartments/type_4_1777304832.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(82, 4, 'uploads/apartments/type_4_1777304844.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(83, 4, 'uploads/apartments/type_4_1777304852.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(84, 4, 'uploads/apartments/type_4_1777304856.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(85, 4, 'uploads/apartments/type_4_1777304876.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(86, 4, 'uploads/apartments/type_4_1777304879.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(87, 4, 'uploads/apartments/type_4_1777304888.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(88, 4, 'uploads/apartments/type_4_1777304897.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(89, 4, 'uploads/apartments/type_4_1777304907.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(90, 4, 'uploads/apartments/type_4_1777304929.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(91, 5, 'uploads/apartments/type_5_1777305120.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(92, 5, 'uploads/apartments/type_5_1777305488.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(93, 5, 'uploads/apartments/type_5_1777305495.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(94, 5, 'uploads/apartments/type_5_1777305513.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(95, 5, 'uploads/apartments/type_5_1777305553.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17'),
(96, 6, 'uploads/apartments/type_6_1777305113.jpg', NULL, 'image/jpeg', '', 0, 0, '2026-05-03 10:50:17');

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
(12, 5, '00', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-05-02 23:28:10'),
(13, 1, 'B1-02', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
(14, 5, '00', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-05-02 23:28:13'),
(15, 5, '00', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Guest Room', '2026-04-23 13:34:56', '2026-05-02 23:28:18'),
(16, 1, 'B1-05', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
(17, 1, '00', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:40:58'),
(18, 1, '00', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:28:26'),
(19, 6, 'B1-08', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(20, 6, 'B1-09', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(21, 6, 'B1-10', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(22, 6, 'B1-11', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(23, 5, 'B1-12', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Guest Room', '2026-04-23 13:34:56', '2026-05-02 23:37:27'),
(24, 5, 'B2-01', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-05-02 23:37:27'),
(25, 3, 'B2-02', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(26, 1, 'B2-03', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:37:27'),
(27, 5, 'B2-04', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
(28, 3, 'B2-05', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(29, 6, 'B2-06', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(30, 2, 'B2-07', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
(31, 3, 'B2-08', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(32, 6, 'B2-09', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(33, 3, 'B2-10', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(34, 5, 'B2-11', 'Building 2', 'Occupied', 25, 63, 'Building 2, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-05-03 11:06:13'),
(35, 6, 'B2-12', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(36, 1, 'B2-13', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
(37, 1, '00', 'Building 2', 'Available', 0, 47, 'Building 2, Floor 2 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:39:41'),
(38, 6, '00', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-05-02 23:40:58'),
(39, 2, 'B2-16', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 2 — One-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(40, 5, 'B2-17', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(41, 5, 'B2-18', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 3 — Guest Room', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(42, 1, 'B3-01', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
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
(71, 4, 'B3-30', 'Building 3', 'Available', NULL, NULL, 'Building 3, Floor 4 — Transient', '2026-04-23 13:34:56', '2026-05-02 23:45:14'),
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
  `admin_role` varchar(50) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `admin_id`, `admin_name`, `admin_role`, `module`, `action`, `details`, `timestamp`) VALUES
(1, 59, 'Norman Ungasin', NULL, 'GOVERNANCE', 'TOGGLE_USER', 'Deactivated user account ID: 31', '2026-05-01 10:39:57'),
(2, 59, 'Norman Ungasin', NULL, 'GOVERNANCE', 'TOGGLE_USER', 'Activated user account ID: 31', '2026-05-01 10:40:07'),
(3, 59, 'Norman Ungasin', NULL, 'GOVERNANCE', 'TOGGLE_USER', 'Deactivated user account ID: 31', '2026-05-01 10:42:50'),
(4, 59, 'Norman Ungasin', NULL, 'GOVERNANCE', 'TOGGLE_USER', 'Activated user account ID: 31', '2026-05-01 10:42:56'),
(5, 59, 'Norman Ungasin', NULL, 'GOVERNANCE', 'TOGGLE_USER', 'Deactivated user account ID: 31', '2026-05-01 10:45:13'),
(6, 59, 'Norman Ungasin', NULL, 'GOVERNANCE', 'TOGGLE_USER', 'Activated user account ID: 31', '2026-05-01 10:45:16'),
(7, 59, 'Norman Ungasin', NULL, 'BROADCAST', 'SEND_NOTIFICATION', 'Sent \'Maintenance\' to 1 users (APARTMENT)', '2026-05-01 15:16:53'),
(8, 31, 'Setsuna Ignacio', NULL, 'APARTMENT', 'APPROVE_MAINTENANCE', 'Approved maintenance ID: 1', '2026-05-02 09:22:20'),
(9, 0, 'System', 'System', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 09:41:08'),
(10, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 09:41:13'),
(11, 0, 'System', 'System', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 09:41:26'),
(12, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 09:41:31'),
(13, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 09:43:18'),
(14, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 09:43:32'),
(15, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 09:43:35'),
(16, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 09:43:41'),
(17, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 11:51:43'),
(18, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:05:57'),
(19, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:06:02'),
(20, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:06:18'),
(21, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:06:27'),
(22, 60, 'Ryan Felizardo', 'Tenant', 'MAINTENANCE', 'SUBMIT_MAINTENANCE', 'Submitted Electrical maintenance request', '2026-05-02 12:11:22'),
(23, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:11:27'),
(24, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:11:42'),
(25, 59, 'Norman Ungasin', 'Admin', 'MAINTENANCE', 'APPROVE_MAINTENANCE', 'Approved maintenance ID: 2', '2026-05-02 12:12:03'),
(26, 59, 'Norman Ungasin', 'Admin', 'MAINTENANCE', 'RESOLVE_MAINTENANCE', 'Resolved maintenance ID: 1', '2026-05-02 12:12:11'),
(27, 59, 'Norman Ungasin', 'Admin', 'MAINTENANCE', 'RESOLVE_MAINTENANCE', 'Resolved maintenance ID: 2', '2026-05-02 12:12:14'),
(28, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:12:18'),
(29, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:16:43'),
(30, 60, 'Ryan Felizardo', 'Tenant', 'MAINTENANCE', 'SUBMIT_MAINTENANCE', 'Submitted Pest Control maintenance request', '2026-05-02 12:20:33'),
(31, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:20:37'),
(32, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:20:42'),
(33, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:21:01'),
(34, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:21:35'),
(35, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:21:37'),
(36, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:21:41'),
(37, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:22:01'),
(38, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:24:36'),
(39, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 12:25:22'),
(40, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 12:25:28'),
(41, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 15:20:36'),
(42, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 15:21:01'),
(43, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 20:36:59'),
(44, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 20:47:38'),
(45, 60, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 20:47:55'),
(46, 61, 'Ryan Felizardo', 'Guest', 'APARTMENT', 'SUBMIT_APP', 'Finalized and submitted apartment application', '2026-05-02 20:50:45'),
(47, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 20:51:04'),
(48, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'APARTMENT', 'APPROVE_APP', 'Approved application ID: 23 for Tenant ID: 61', '2026-05-02 20:51:16'),
(49, 61, 'Ryan Felizardo', 'Guest', 'BILLING', 'SUBMIT_PAYMENT', 'Submitted payment for 2 item(s). Ref: PAY-BLK-6846935', '2026-05-02 20:51:38'),
(50, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 20:52:16'),
(51, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 20:52:21'),
(52, 61, 'Ryan Felizardo', 'Tenant', 'BILLING', 'SUBMIT_PAYMENT', 'Submitted payment for 5 item(s). Ref: PAY-ADV-3297983', '2026-05-02 21:05:03'),
(53, 61, 'Ryan Felizardo', 'Tenant', 'BILLING', 'SUBMIT_PAYMENT', 'Submitted payment for 3 item(s). Ref: PAY-ADV-3638343', '2026-05-02 21:21:58'),
(54, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 21:29:32'),
(55, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 21:29:36'),
(56, 61, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 21:30:12'),
(57, 61, 'Ryan Felizardo', 'Tenant', 'PARKING', 'SUBMIT_PARKING', 'Submitted parking application for 1 vehicle(s)', '2026-05-02 21:30:53'),
(58, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 21:31:19'),
(59, 59, 'Norman Ungasin', 'Admin', 'PARKING', 'APPROVE_PARKING', 'Approved parking ID: 8', '2026-05-02 21:31:29'),
(60, 61, 'Ryan Felizardo', 'Tenant', 'BILLING', 'SUBMIT_PAYMENT', 'Submitted payment for 3 item(s). Ref: PAY-BLK-3274482', '2026-05-02 21:32:28'),
(61, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 21:56:18'),
(62, 61, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 21:56:37'),
(63, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 21:57:25'),
(64, 62, 'Ryan Felizardo', 'Guest', 'APARTMENT', 'SUBMIT_APP', 'Finalized and submitted apartment application', '2026-05-02 21:59:05'),
(65, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'APARTMENT', 'APPROVE_APP', 'Approved application ID: 24 for Tenant ID: 62', '2026-05-02 21:59:14'),
(66, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 22:00:45'),
(67, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 22:01:01'),
(68, 62, 'Ryan Felizardo', 'Guest', 'BILLING', 'SUBMIT_PAYMENT', 'Submitted payment for 2 item(s). Ref: PAY-BLK-8865257', '2026-05-02 22:09:03'),
(69, 62, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 22:10:47'),
(70, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 22:10:51'),
(71, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 22:11:14'),
(72, 63, 'Ryan Felizardo', 'Guest', 'APARTMENT', 'SUBMIT_APP', 'Finalized and submitted apartment application', '2026-05-02 22:13:06'),
(73, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'APARTMENT', 'APPROVE_APP', 'Approved application ID: 25 for Tenant ID: 63', '2026-05-02 22:13:12'),
(74, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 22:18:26'),
(75, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 22:18:47'),
(76, 63, 'Ryan Felizardo', 'Guest', 'BILLING', 'SUBMIT_PAYMENT', 'Submitted payment for 4 item(s). Ref: PAY-BLK-669919', '2026-05-02 22:19:02'),
(77, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 22:51:34'),
(78, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 23:25:15'),
(79, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 23:25:22'),
(80, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 23:25:33'),
(81, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 23:25:42'),
(82, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 23:26:48'),
(83, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 23:27:14'),
(84, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 23:29:04'),
(85, 31, 'Setsuna Ignacio', 'Staff_Damayan', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 23:29:37'),
(86, 31, 'Setsuna Ignacio', 'Staff_Damayan', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-02 23:29:42'),
(87, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-02 23:38:14'),
(88, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 09:08:05'),
(89, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 09:35:48'),
(90, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 09:41:49'),
(91, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 09:42:14'),
(92, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 09:44:25'),
(93, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-03 10:33:00'),
(94, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 10:33:17'),
(95, 59, 'Norman Ungasin', 'Admin', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-03 10:36:27'),
(96, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 10:36:37'),
(97, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-03 10:53:01'),
(98, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 10:53:34'),
(99, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-03 11:03:41'),
(100, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 11:03:49'),
(101, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 11:04:47'),
(102, 63, 'Ryan Felizardo', 'Tenant', 'MAINTENANCE', 'SUBMIT_MAINTENANCE', 'Submitted Plumbing maintenance request', '2026-05-03 11:05:16'),
(103, 63, 'Ryan Felizardo', 'Tenant', 'AUTH', 'LOGOUT', 'User logged out of the system', '2026-05-03 11:05:24'),
(104, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'AUTH', 'LOGIN', 'User logged into the system', '2026-05-03 11:05:29'),
(105, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'APARTMENT', 'UNIT_MAINTENANCE', 'Room for Tenant ID 63 set to MAINTENANCE status due to maintenance request ID: 4', '2026-05-03 11:05:44'),
(106, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'MAINTENANCE', 'APPROVE_MAINTENANCE', 'Approved maintenance ID: 4', '2026-05-03 11:05:44'),
(107, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'APARTMENT', 'UNIT_RESTORED', 'Room for Tenant ID 63 restored to OCCUPIED status after maintenance ID: 4 resolved', '2026-05-03 11:06:13'),
(108, 31, 'Setsuna Ignacio', 'Staff_Tenant', 'MAINTENANCE', 'RESOLVE_MAINTENANCE', 'Resolved maintenance ID: 4', '2026-05-03 11:06:13');

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
(1, 'Maintenance', 'Hello', 'APARTMENT', 59, 'system', '2026-05-01 15:16:53');

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
-- Table structure for table `counseling_requests`
--

CREATE TABLE `counseling_requests` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `reason` varchar(255) NOT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(20) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `islamic_education_enrollments`
--

CREATE TABLE `islamic_education_enrollments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `status` enum('pending','active','completed','dropped') DEFAULT 'pending',
  `gender` enum('male','female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, 61, 23, 'Guest Room', 5000.00, 1000.00, 5000.00, '2026-05-02', '2027-05-02', 'Active', '2026-05-02 20:51:16', '2026-05-02 20:51:38'),
(8, 62, 24, 'Studio', 4680.00, 1000.00, 4680.00, '2026-05-02', '2027-05-02', 'Active', '2026-05-02 21:59:14', '2026-05-02 22:09:03'),
(9, 63, 25, 'Guest Room', 5000.00, 1000.00, 5000.00, '2026-05-03', '2027-05-03', 'Active', '2026-05-02 22:13:12', '2026-05-02 22:19:02');

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
(39, 60, 'Maintenance Update', 'Your maintenance request for Electrical is now In Progress.', 'approval', 1, '2026-05-02 09:22:20'),
(40, 60, 'Maintenance Update', 'Your maintenance request for Electrical is now In Progress.', 'approval', 1, '2026-05-02 12:12:03'),
(41, 60, 'Maintenance Resolved', 'Your maintenance request for Electrical has been marked as Completed.', 'success', 1, '2026-05-02 12:12:11'),
(42, 60, 'Maintenance Resolved', 'Your maintenance request for Electrical has been marked as Completed.', 'success', 1, '2026-05-02 12:12:14'),
(43, 60, 'Maintenance Request Received', 'Your request for Pest Control maintenance has been received and is waiting for review.', 'info', 1, '2026-05-02 12:20:33'),
(44, 61, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 0, '2026-05-02 20:49:23'),
(45, 61, 'Application Under Review', 'Your apartment application has been submitted successfully and is currently undergoing administrative review.', 'system', 0, '2026-05-02 20:50:45'),
(46, 61, 'Application Approved!', 'Congratulations! Your apartment application has been approved. Please review and accept your Lease Contract to proceed to Initial Payments. A room will be assigned once payments are settled.', 'approval', 0, '2026-05-02 20:51:16'),
(47, 61, 'Room Assigned!', 'Your payment is confirmed and your lease is now Active! You have been assigned to Room B2-04 in Building 2. Welcome to your new home!', 'payment', 0, '2026-05-02 20:51:38'),
(48, 61, 'Payment Received', 'Your payment for Initial Payment, Initial Payment has been recorded (Ref: PAY-BLK-6846935). Thank you for settling your dues!', 'payment', 0, '2026-05-02 20:51:38'),
(49, 61, 'Payment Received', 'Your payment for Rent Advance + 4 other items has been recorded (Ref: PAY-ADV-3297983). Thank you for settling your dues!', 'payment', 0, '2026-05-02 21:05:03'),
(50, 61, 'Payment Received', 'Your payment for Rent Advance, Water Advance, Contribution Advance has been recorded (Ref: PAY-ADV-3638343). Thank you for settling your dues!', 'payment', 0, '2026-05-02 21:21:58'),
(51, 61, 'Upcoming Payment Reminder - May 2026', 'Heads up! Your monthly bill for May 2026 is due on the 5th. Please settle your Rent, Water, and Contributions to avoid any late flags.', 'payment', 0, '2026-05-02 21:30:12'),
(52, 61, 'Parking Request Submitted', 'Your parking application for 1 vehicle(s) has been successfully submitted and is under review.', 'system', 0, '2026-05-02 21:30:53'),
(53, 61, 'Payment Received', 'Your payment for Parking 8 2026 05, Parking 8 2026 06, Parking 8 2026 07 has been recorded (Ref: PAY-BLK-3274482). Thank you for settling your dues!', 'payment', 0, '2026-05-02 21:32:28'),
(54, 62, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 0, '2026-05-02 21:57:31'),
(55, 62, 'Application Under Review', 'Your apartment application has been submitted successfully and is currently undergoing administrative review.', 'system', 0, '2026-05-02 21:59:06'),
(56, 62, 'Application Approved!', 'Congratulations! Your apartment application has been approved. Please review and accept your Lease Contract to proceed to Initial Payments. A room will be assigned once payments are settled.', 'approval', 0, '2026-05-02 21:59:14'),
(57, 62, 'Room Assigned!', 'Your payment is confirmed and your lease is now Active! You have been assigned to Room B3-01 in Building 3. Welcome to your new home!', 'payment', 0, '2026-05-02 22:09:03'),
(58, 62, 'Payment Received', 'Your payment for Initial Payment, Initial Payment has been recorded (Ref: PAY-BLK-8865257). Thank you for settling your dues!', 'payment', 0, '2026-05-02 22:09:03'),
(59, 63, 'Welcome to ISCAG MIS', 'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.', 'info', 1, '2026-05-02 22:11:39'),
(60, 63, 'Application Under Review', 'Your apartment application has been submitted successfully and is currently undergoing administrative review.', 'system', 1, '2026-05-02 22:13:06'),
(61, 63, 'Application Approved!', 'Congratulations! Your apartment application has been approved. Please review and accept your Lease Contract to proceed to Initial Payments. A room will be assigned once payments are settled.', 'approval', 1, '2026-05-02 22:13:12'),
(62, 63, 'Room Assigned!', 'Your payment is confirmed and your lease is now Active! You have been assigned to Room B2-11 in Building 2. Welcome to your new home!', 'payment', 1, '2026-05-02 22:19:02'),
(63, 63, 'Payment Received', 'Your payment for Initial Payment + 3 other items has been recorded (Ref: PAY-BLK-669919). Thank you for settling your dues!', 'payment', 1, '2026-05-02 22:19:02'),
(64, 63, 'Upcoming Payment Reminder - May 2026', 'Heads up! Your monthly bill for May 2026 is due on the 5th. Please settle your Rent, Water, and Contributions to avoid any late flags.', 'payment', 1, '2026-05-02 23:25:42'),
(65, 63, 'Upcoming Payment Reminder - June 2026', 'Heads up! Your monthly bill for June 2026 is due on the 5th. Please settle your Rent, Water, and Contributions to avoid any late flags.', 'payment', 1, '2026-05-03 09:24:22'),
(66, 63, 'Maintenance Request Received', 'Your request for Plumbing maintenance has been received and is waiting for review.', 'info', 0, '2026-05-03 11:05:16'),
(67, 63, 'Maintenance Update', 'Your maintenance request for Plumbing is now In Progress.', 'approval', 0, '2026-05-03 11:05:44'),
(68, 63, 'Maintenance Resolved', 'Your maintenance request for Plumbing has been marked as Completed.', 'success', 0, '2026-05-03 11:06:13');

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
(27, 7, 61, 'Deposit', 1000.00, 'Paid', '2026-05-03 04:51:38', 'PAY-BLK-6846935', '2026-05-02 20:51:31', '2026-05-02 20:51:38'),
(28, 7, 61, 'Advance', 5000.00, 'Paid', '2026-05-03 04:51:38', 'PAY-BLK-6846935', '2026-05-02 20:51:31', '2026-05-02 20:51:38'),
(29, 7, 61, 'Rent-advance', 5000.00, 'Paid', '2026-05-03 05:05:03', 'PAY-ADV-3297983', '2026-05-02 21:05:03', '2026-05-02 21:05:03'),
(30, 7, 61, 'Water-advance', 300.00, 'Paid', '2026-05-03 05:05:03', 'PAY-ADV-3297983', '2026-05-02 21:05:03', '2026-05-02 21:05:03'),
(31, 7, 61, 'Water-advance', 300.00, 'Paid', '2026-05-03 05:05:03', 'PAY-ADV-3297983', '2026-05-02 21:05:03', '2026-05-02 21:05:03'),
(32, 7, 61, 'Contribution-advance', 150.00, 'Paid', '2026-05-03 05:05:03', 'PAY-ADV-3297983', '2026-05-02 21:05:03', '2026-05-02 21:05:03'),
(33, 7, 61, 'Contribution-advance', 150.00, 'Paid', '2026-05-03 05:05:03', 'PAY-ADV-3297983', '2026-05-02 21:05:03', '2026-05-02 21:05:03'),
(34, 7, 61, 'Rent-advance', 5000.00, 'Paid', '2026-05-03 05:21:58', 'PAY-ADV-3638343', '2026-05-02 21:21:58', '2026-05-02 21:21:58'),
(35, 7, 61, 'Water-advance', 300.00, 'Paid', '2026-05-03 05:21:58', 'PAY-ADV-3638343', '2026-05-02 21:21:58', '2026-05-02 21:21:58'),
(36, 7, 61, 'Contribution-advance', 150.00, 'Paid', '2026-05-03 05:21:58', 'PAY-ADV-3638343', '2026-05-02 21:21:58', '2026-05-02 21:21:58'),
(37, 7, 61, 'Parking-8-2026-05', 1000.00, 'Paid', '2026-05-03 05:32:28', 'PAY-BLK-3274482', '2026-05-02 21:32:28', '2026-05-02 21:32:28'),
(38, 7, 61, 'Parking-8-2026-06', 1000.00, 'Paid', '2026-05-03 05:32:28', 'PAY-BLK-3274482', '2026-05-02 21:32:28', '2026-05-02 21:32:28'),
(39, 7, 61, 'Parking-8-2026-07', 1000.00, 'Paid', '2026-05-03 05:32:28', 'PAY-BLK-3274482', '2026-05-02 21:32:28', '2026-05-02 21:32:28'),
(40, 8, 62, 'Deposit', 1000.00, 'Paid', '2026-05-03 06:09:03', 'PAY-BLK-8865257', '2026-05-02 21:59:23', '2026-05-02 22:09:03'),
(41, 8, 62, 'Advance', 4680.00, 'Paid', '2026-05-03 06:09:03', 'PAY-BLK-8865257', '2026-05-02 21:59:23', '2026-05-02 22:09:03'),
(42, 9, 63, 'Deposit', 1000.00, 'Paid', '2026-05-03 06:19:02', 'PAY-BLK-669919', '2026-05-02 22:13:31', '2026-05-02 22:19:02'),
(43, 9, 63, 'Advance', 5000.00, 'Paid', '2026-05-03 06:19:02', 'PAY-BLK-669919', '2026-05-02 22:13:31', '2026-05-02 22:19:02'),
(44, 9, 63, 'Water-Advance', 300.00, 'Paid', '2026-05-03 06:19:02', 'PAY-BLK-669919', '2026-05-02 22:13:31', '2026-05-02 22:19:02'),
(45, 9, 63, 'Contribution-Advance', 150.00, 'Paid', '2026-05-03 06:19:02', 'PAY-BLK-669919', '2026-05-02 22:13:31', '2026-05-02 22:19:02');

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
  `profile_picture_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenant_accounts`
--

INSERT INTO `tenant_accounts` (`tenant_id`, `first_name`, `last_name`, `sex`, `email`, `contactnum`, `password`, `confirmpass`, `role`, `otp_code`, `otp_expiry`, `is_verified`, `profile_picture`, `profile_picture_mime`, `profile_picture_path`) VALUES
(31, 'Setsuna', 'Ignacio', 'Male', 'kazekizumi@gmail.com', '+639065740813', '$2y$10$Vtb/RzYdtONp3TKGfv9b3e0qStZoUoNlsPEsZW6E9./NsmfpFTw7O', '$2y$10$rRZsROrvbJ81tKezKq4is.RSE4QalbHtc9XhmdqQSgEnNYumtSyje', 'Staff_Tenant', NULL, NULL, 1, NULL, NULL, NULL),
(59, 'Norman', 'Ungasin', 'Male', 'kizumikaze1@gmail.com', '+639065740819', '$2y$10$Fm.Qe7d9MwZfybGmJ/awaeHFhwIebTS4SFh0I7krDkbqTr2K8qRVO', '$2y$10$XO4gb1aTve/nzgiFFIH6/.N1tB2G.Y8uxjW9MydpGkPpyI7bJjOUm', 'Admin', NULL, NULL, 1, NULL, NULL, NULL),
(63, 'Ryan', 'Felizardo', 'Male', 'rjfelizardo25@gmail.com', '+639065740817', '$2y$10$9QiDmpv/qIyWCR.9Bre/N.zI40Xy9pJEAU3QAsFuSupe2DK7qzoz.', '$2y$10$5v2GNUzy324n8mpKz7NWAOKWv0v9DyGiTPPUNBF8fsJlR992FI3oy', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL);

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
(36, 61, 'Felizardo', 'Ryan', 'G', '', 'Single', 'Blk 10 Lot 34', '2014-06-11', 'Blk 10 Lot 34', 11, 'Male', '', 2, 'Student', '', 'Jollibee', 'Blk 10 Lot 34', '09065740819', '0000-00-00', 'Darren Espinosa', '09194678127', 0, '[]', 0, '', '2026-05-02', '[{\"name\":\"Katarina\",\"relation\":\"\",\"age\":\"\",\"religion\":\"Islam\"},{\"name\":\"Garen\",\"relation\":\"\",\"age\":\"\",\"religion\":\"Islam\"}]'),
(37, 62, 'Felizardo', 'Ryan', 'F', '', 'Single', 'Blk 10 Lot 34', '2004-01-03', 'Albay', 22, 'Male', '', 3, 'N/A', '', 'Jollibee', 'Blk 10 Lot 34', '09065740819', '0000-00-00', 'Darren Espinosa', '09194678127', 0, '[]', 0, '', '2026-05-02', '[{\"name\":\"Garen\",\"relation\":\"Son\",\"age\":\"12\",\"religion\":\"Islam\"},{\"name\":\"Katarina\",\"relation\":\"Daughter\",\"age\":\"12\",\"religion\":\"Islam\"}]'),
(38, 63, 'Felizardo', 'Ryan', 'F', '', 'Married', 'Blk 10 Lot 34', '2006-07-03', 'Albay', 19, 'Male', '', 2, 'N/A', '', 'Jollibee', 'Blk 10 Lot 34', '09065740819', '0000-00-00', 'Danilo Raizen', '09194678123', 0, '[]', 0, '', '2026-05-02', '[{\"name\":\"Yasuo\",\"relation\":\"Son\",\"age\":\"12\",\"religion\":\"Islam\"},{\"name\":\"Yone\",\"relation\":\"Son\",\"age\":\"12\",\"religion\":\"Islam\"}]');

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
(132, 36, NULL, 'image/png', '2026-05-02 20:49:51', 'picture', 'uploads/tenants/doc_61_picture_1777754991.png'),
(133, 36, NULL, 'image/png', '2026-05-02 20:50:34', 'proofofincome', 'uploads/tenants/doc_61_proofofincome_1777755034.png'),
(134, 36, NULL, 'image/png', '2026-05-02 20:50:37', 'valididfront', 'uploads/tenants/doc_61_valididfront_1777755037.png'),
(135, 36, NULL, 'image/jpeg', '2026-05-02 20:50:39', 'valididback', 'uploads/tenants/doc_61_valididback_1777755039.jpg'),
(136, 36, NULL, 'image/jpeg', '2026-05-02 20:50:41', 'birthcert', 'uploads/tenants/doc_61_birthcert_1777755041.jpg'),
(137, 36, NULL, 'image/png', '2026-05-02 20:50:43', 'nbi', 'uploads/tenants/doc_61_nbi_1777755043.png'),
(138, 37, NULL, 'image/png', '2026-05-02 21:58:04', 'picture', 'uploads/tenants/doc_62_picture_1777759084.png'),
(139, 37, NULL, 'image/png', '2026-05-02 21:58:52', 'proofofincome', 'uploads/tenants/doc_62_proofofincome_1777759132.png'),
(140, 37, NULL, 'image/jpeg', '2026-05-02 21:58:55', 'valididfront', 'uploads/tenants/doc_62_valididfront_1777759138.jpg'),
(141, 37, NULL, 'image/png', '2026-05-02 21:59:00', 'valididback', 'uploads/tenants/doc_62_valididback_1777759140.png'),
(142, 37, NULL, 'image/png', '2026-05-02 21:59:02', 'birthcert', 'uploads/tenants/doc_62_birthcert_1777759142.png'),
(143, 37, NULL, 'image/jpeg', '2026-05-02 21:59:04', 'nbi', 'uploads/tenants/doc_62_nbi_1777759144.jpg'),
(145, 38, NULL, 'image/jpeg', '2026-05-02 22:12:14', 'picture', 'uploads/tenants/doc_63_picture_1777759934.jpg'),
(146, 38, NULL, 'image/png', '2026-05-02 22:12:53', 'proofofincome', 'uploads/tenants/doc_63_proofofincome_1777759976.png'),
(147, 38, NULL, 'image/jpeg', '2026-05-02 22:12:58', 'valididfront', 'uploads/tenants/doc_63_valididfront_1777759978.jpg'),
(148, 38, NULL, 'image/jpeg', '2026-05-02 22:13:00', 'valididback', 'uploads/tenants/doc_63_valididback_1777759980.jpg'),
(149, 38, NULL, 'image/png', '2026-05-02 22:13:03', 'birthcert', 'uploads/tenants/doc_63_birthcert_1777759983.png'),
(150, 38, NULL, 'image/png', '2026-05-02 22:13:05', 'nbi', 'uploads/tenants/doc_63_nbi_1777759985.png');

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

--
-- Dumping data for table `tenant_family_members`
--

INSERT INTO `tenant_family_members` (`id`, `tenant_id`, `name`, `relation`, `age`, `religion`, `created_at`) VALUES
(1, 32, 'Youngstown Tocino', 'Son', 22, 'Islam', '2026-05-02 20:45:54'),
(2, 32, 'Rodrigo Roa Duterte', 'Son', 60, 'Islam', '2026-05-02 20:45:54'),
(3, 47, 'Koy Koy', 'Son', 12, 'Islam', '2026-05-02 20:45:54'),
(4, 49, 'Alhamdillah Salazar', 'Son', 22, 'Islam', '2026-05-02 20:45:54'),
(16, 61, 'Katarina', '', NULL, 'Islam', '2026-05-02 20:50:32'),
(17, 61, 'Garen', '', NULL, 'Islam', '2026-05-02 20:50:32'),
(33, 62, 'Garen', 'Son', 12, 'Islam', '2026-05-02 21:58:49'),
(34, 62, 'Katarina', 'Daughter', 12, 'Islam', '2026-05-02 21:58:49'),
(47, 63, 'Yasuo', 'Son', 12, 'Islam', '2026-05-02 22:12:52'),
(48, 63, 'Yone', 'Son', 12, 'Islam', '2026-05-02 22:12:52');

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

--
-- Dumping data for table `tenant_maintenance`
--

INSERT INTO `tenant_maintenance` (`id`, `tenant_id`, `category`, `description`, `attachment`, `status`, `admin_remarks`, `created_at`, `updated_at`) VALUES
(4, 63, 'Plumbing', 'Test', 'uploads/maintenance/maintenance_63_1777806316_69f72bec20fd3.png', 'Completed', 'Your maintenance request has been resolved/completed.', '2026-05-03 11:05:16', '2026-05-03 11:06:13');

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
(8, 61, '2026-05-02', 'Subaru', 'Kotaru', 'Sedan', 'ewqx371', '2026-05-02', NULL, 'Approved', NULL, '2026-05-02 21:31:29');

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
(18, 61, 'N/A', '2014-06-11', 'Single', 'Student', 'Blk 10 Lot 34', 'N/A', '2026-05-02 20:49:38', '2026-05-02 20:49:38'),
(19, 62, 'N/A', '2004-01-03', 'Single', 'N/A', 'Blk 10 Lot 34', 'N/A', '2026-05-02 21:57:41', '2026-05-02 21:57:53'),
(20, 63, 'N/A', '2006-07-03', 'Married', 'N/A', 'Blk 10 Lot 34', 'N/A', '2026-05-02 22:11:56', '2026-05-02 22:11:56');

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
-- Indexes for table `counseling_requests`
--
ALTER TABLE `counseling_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `dawah_availability`
--
ALTER TABLE `dawah_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_date_dept` (`blocked_date`,`department`);

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
-- Indexes for table `islamic_education_enrollments`
--
ALTER TABLE `islamic_education_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `apartment_types`
--
ALTER TABLE `apartment_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `apartment_type_images`
--
ALTER TABLE `apartment_type_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `apartment_units`
--
ALTER TABLE `apartment_units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `broadcasts`
--
ALTER TABLE `broadcasts`
  MODIFY `broadcast_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `counseling_requests`
--
ALTER TABLE `counseling_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dawah_availability`
--
ALTER TABLE `dawah_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `islamic_education_enrollments`
--
ALTER TABLE `islamic_education_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leases`
--
ALTER TABLE `leases`
  MODIFY `lease_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT for table `marriage_witness`
--
ALTER TABLE `marriage_witness`
  MODIFY `witness_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `postmortem_certificate`
--
ALTER TABLE `postmortem_certificate`
  MODIFY `postmortem_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_accounts`
--
ALTER TABLE `tenant_accounts`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `tenant_addinfo`
--
ALTER TABLE `tenant_addinfo`
  MODIFY `tenant_info` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tenant_addinfo_images`
--
ALTER TABLE `tenant_addinfo_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `tenant_family_members`
--
ALTER TABLE `tenant_family_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tenant_maintenance`
--
ALTER TABLE `tenant_maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  MODIFY `parking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tenant_user_profiles`
--
ALTER TABLE `tenant_user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  ADD CONSTRAINT `fk_apartmentsapp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `apartment_type_images`
--
ALTER TABLE `apartment_type_images`
  ADD CONSTRAINT `apartment_type_images_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `apartment_types` (`type_id`) ON DELETE CASCADE;

--
-- Constraints for table `counseling_requests`
--
ALTER TABLE `counseling_requests`
  ADD CONSTRAINT `counseling_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`);

--
-- Constraints for table `islamic_education_enrollments`
--
ALTER TABLE `islamic_education_enrollments`
  ADD CONSTRAINT `islamic_education_enrollments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE SET NULL;

--
-- Constraints for table `tenant_maintenance`
--
ALTER TABLE `tenant_maintenance`
  ADD CONSTRAINT `tenant_maintenance_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
