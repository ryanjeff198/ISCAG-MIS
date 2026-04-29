-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2026 at 09:56 PM
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
(8, 42, 'Studio', 12, '2026-04-25', NULL, 'Assigned', NULL, '2026-04-25 18:13:00', 17, NULL, NULL, '2026-04-26 02:02:31', '2026-04-26 02:02:31', 0),
(9, 44, 'Studio', 12, '2026-04-26', NULL, 'Assigned', NULL, '2026-04-26 04:44:54', 18, NULL, NULL, '2026-04-26 12:44:54', '2026-04-26 12:44:54', 0),
(10, 45, 'Studio', 12, '2026-04-26', NULL, 'Assigned', NULL, '2026-04-26 11:20:17', 26, NULL, NULL, '2026-04-26 19:20:17', '2026-04-26 19:20:17', 0),
(11, 47, 'Studio', 12, '2026-04-27', NULL, 'Assigned', NULL, '2026-04-27 12:25:22', 42, NULL, NULL, '2026-04-27 20:20:20', '2026-04-27 20:20:20', 0),
(12, 49, 'Transient', 12, '2026-04-27', NULL, 'Assigned', NULL, '2026-04-27 12:25:22', 42, NULL, NULL, '2026-04-27 20:20:20', '2026-04-27 20:20:20', 0),
(13, 51, 'Transient', 12, '2026-04-27', NULL, 'Assigned', NULL, '2026-04-27 14:18:18', 71, NULL, NULL, '2026-04-27 22:18:18', '2026-04-27 22:18:18', 0),
(21, 58, 'Guest Room', 12, '2026-04-30', NULL, 'Assigned', NULL, '2026-04-29 17:53:11', 24, NULL, NULL, '2026-04-30 01:53:11', '2026-04-30 01:53:11', 0);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartment_types`
--

INSERT INTO `apartment_types` (`type_id`, `type_key`, `label`, `price`, `capacity`, `description`, `floor_area`, `bedrooms`, `bathroom`, `kitchen`, `parking`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'studio', 'Studio ', 4680.00, '1-2 pax', 'A compact and efficient studio-type living space ideal for individuals or couples seeking privacy and convenience. It features an open-plan layout that combines the sleeping, living, and dining areas into one well-designed space, along with a separate bathroom and a functional kitchenette. Perfect for short or long stays requiring simplicity, comfort, and practical living.', '', '1 (separate)', '1 ', 'Kitchenette', 'Shared lot', 1, 1, '2026-04-21 17:53:51', '2026-04-27 16:01:37'),
(2, '1br', 'One-Bedroom ', 6240.00, '2-3 pax', 'A comfortable one-bedroom apartment ideal for small families, couples, or Muslim guests who prefer a separate sleeping area and a private, respectful living space. It features a distinct living room, a private bedroom, a full bathroom, and a dining-kitchen area with ample counter space, suitable for short or long stays with comfort and privacy.', '', '1 (separate)', '1 (with shower)', 'Full kitchen', 'Shared lot', 1, 2, '2026-04-21 17:53:51', '2026-04-27 16:01:19'),
(3, '2br', 'Two-Bedroom ', 7000.00, '3-5 persons', 'A spacious two-bedroom apartment designed for small to growing families, couples, or Muslim guests seeking comfort and privacy. It includes a same size bedroom, a full living and dining area, a complete kitchen, and a bathroom. Ideal for families or guests looking for a peaceful and well-organized living space within the community housing complex.', '', '2 (separate)', '1 (with shower)', 'Full kitchen', 'Dedicated slot', 1, 3, '2026-04-21 17:53:51', '2026-04-27 16:01:47'),
(4, '1tr', 'Transient', 2500.00, '10 pax', 'A transient accommodation designed for short-term stays, typically accommodating 8–10 guests. It may consist of multiple bedrooms or shared sleeping areas, along with common facilities such as a living space, kitchen, and bathroom depending on the setup.\n\n1 month deposit', '', 'shared bedspace', '1 shared', NULL, NULL, 1, 0, '2026-04-21 18:54:28', '2026-04-28 14:09:50'),
(5, '1gr', 'Guest Room', 5000.00, '3-5 pax', 'A guest room accommodation designed for visiting guests, families, or travelers seeking a comfortable short-term stay. It is similar in layout to a two-bedroom unit, typically featuring a master bedroom, a second bedroom, a shared living area, a kitchen, and a bathroom. It is commonly used for Islamic visitors and families, providing a clean, private, and respectful space suitable for short stays.', '', '2 (separate)', '1', NULL, NULL, 1, 0, '2026-04-22 03:44:49', '2026-04-23 13:12:51'),
(6, '1bc', 'Bachelor', 2500.00, '1-2 pax', 'A compact bachelor-type unit designed for single occupants or couples seeking a simple and efficient living space. It features an open-plan layout that combines sleeping, living, and dining areas in one space, with a separate bathroom and a small kitchenette. Ideal for individuals who prefer a minimal, practical, and low-maintenance home for short or long stays.', '', '1', '1', NULL, NULL, 1, 0, '2026-04-23 12:56:11', '2026-04-23 13:16:26');

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
(18, 1, 'B1-07', 'Building 1', 'Occupied', 9, 44, 'Building 1, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-26 04:44:54'),
(19, 6, 'B1-08', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(20, 6, 'B1-09', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Transient', '2026-04-23 13:34:56', '2026-04-27 11:10:21'),
(21, 6, 'B1-10', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(22, 6, 'B1-11', 'Building 1', 'Available', NULL, NULL, 'Building 1, Floor 2 — Bachelor', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(23, 5, 'B1-12', 'Building 1', 'Occupied', NULL, NULL, 'Building 1, Floor 2 — Guest Room', '2026-04-23 13:34:56', '2026-04-29 15:58:48'),
(24, 5, 'B2-01', 'Building 2', 'Occupied', 21, 58, 'Building 2, Floor 1 — Transient', '2026-04-23 13:34:56', '2026-04-29 17:53:11'),
(25, 3, 'B2-02', 'Building 2', 'Available', NULL, NULL, 'Building 2, Floor 1 — Two-Bedroom Unit', '2026-04-23 13:34:56', '2026-04-23 16:59:17'),
(26, 1, 'B2-03', 'Building 2', 'Occupied', 10, 45, 'Building 2, Floor 1 — Studio Unit', '2026-04-23 13:34:56', '2026-04-26 11:20:17'),
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
(71, 4, 'B3-30', 'Building 3', 'Occupied', NULL, NULL, 'Building 3, Floor 4 — Transient', '2026-04-23 13:34:56', '2026-04-29 17:46:49'),
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
(5, 58, 21, 'Guest Room', 5000.00, 1000.00, 5000.00, '2026-04-29', '2027-10-29', 'Active', '2026-04-29 17:52:11', '2026-04-29 18:38:56');

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

--
-- Dumping data for table `lease_renewals`
--

INSERT INTO `lease_renewals` (`renewal_id`, `lease_id`, `tenant_id`, `requested_term_months`, `status`, `created_at`, `updated_at`) VALUES
(2, 5, 58, 12, 'Approved', '2026-04-29 18:38:01', '2026-04-29 18:38:56');

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
(17, 44, 'Room Assigned!', 'Congratulations! You have been assigned to Room B1-07 in Building 1. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-26 04:44:54'),
(18, 45, 'Room Assigned!', 'Congratulations! You have been assigned to Room B2-03 in Building 2. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-26 11:20:18'),
(19, 47, 'Room Assigned!', 'Congratulations! You have been assigned to Room B2-13 in Building 2. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 11:17:29'),
(20, 47, 'Room Assigned!', 'Congratulations! You have been assigned to Room B2-14 in Building 2. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 12:04:46'),
(21, 47, 'Room Assigned!', 'Congratulations! You have been assigned to Room B3-01 in Building 3. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 12:20:20'),
(22, 51, 'Room Assigned!', 'Congratulations! You have been assigned to Room B3-30 in Building 3. Your account has been upgraded to Tenant.', 'approval', 0, '2026-04-27 14:18:18'),
(29, 58, 'Application Approved!', 'Congratulations! Your apartment application has been approved. Please review and accept your Lease Contract to proceed to Initial Payments. A room will be assigned once payments are settled.', 'approval', 0, '2026-04-29 17:52:11'),
(30, 58, 'Room Assigned!', 'Your payment is confirmed and your lease is now Active! You have been assigned to Room B2-01 in Building 2. Welcome to your new home!', 'payment', 0, '2026-04-29 17:53:11'),
(31, 58, 'Contract Renewal Approved', 'Your lease contract has been successfully renewed and extended for another 12 months.', 'approval', 0, '2026-04-29 18:38:56');

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `lease_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `payment_type` enum('Deposit','Advance') NOT NULL,
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
(5, 5, 58, 'Deposit', 1000.00, 'Paid', '2026-04-30 01:53:08', 'PAY-2227010', '2026-04-29 17:52:55', '2026-04-29 17:53:08'),
(6, 5, 58, 'Advance', 5000.00, 'Paid', '2026-04-30 01:53:11', 'PAY-951471', '2026-04-29 17:52:55', '2026-04-29 17:53:11');

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
(35, 'Tiff', 'Sitti', 'Female', 'adfdacer@kld.edu.ph', '0965312489', '$2y$10$pWneXXBOJvIuewTthR3p7e0ApkMffo4IWNt2SopoAW4dXan40OGm.', '$2y$10$RJmdqk4cTf8MFMF/9JT.8.2bFoaelYoeeJEIRP.HaWl8RebV6px3S', 'Staff_Tenant', NULL, NULL, 1, NULL, NULL, NULL),
(42, 'Tiff', 'Tiff', 'Female', 'tiffany.kaamino091800@gmail.com', '09485269631', '$2y$10$kTQklM9tfHczeruuOABxo.db/Ndw8EVn/Pg79FdjYFi3upLNcnY1q', '$2y$10$ldRvocpu.6JrQMl04q/qo.PeeMpTWCzlLRsbfiLIncXsW..FDqm92', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL),
(43, 'Art', 'Dacer', 'Male', 'dacer.artdenver.kld@gmail.com', '09456123789', '$2y$10$9CNP.B7xg.IezZ8WcPT1COehxFDwzUMo2NRHBLsb8NqI1.x3njVz2', '$2y$10$0BJQm8RKzOUUlx5pur785.H4cTkjsIh0Eo0P333yRWQfzZ8ghXgVO', 'Staff_Tenant', NULL, NULL, 1, NULL, NULL, NULL),
(44, 'Arden', 'Dacer', 'Male', 'pdacer2@gmail.com', '09653124891', '$2y$10$fzBdyJ2uJGdium1t/GnKeeWanFR1DJvyfIDpQgss25h5IjsHOiscy', '$2y$10$Ch4jyvyt2vHdCIZsiIOIGuV0/h28.wLkp3.4jFpanISxcYf8RmfH.', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL),
(45, 'Tenant', 'Dacer', 'Male', 'artdenverforbesdacer@gmail.com', '09485269633', '$2y$10$hGKGjS4v1hn3YFTpAxwBGOCWnz5SfkVRustMrcg3uvzf5IsBrKNmi', '$2y$10$dLggCsJM4TYKJ6jN69.rtObztlJCYUdjhDUnFSu6wd5og1RL.E8/O', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL),
(51, 'Ryan', 'Felizardo', 'Male', 'rjfelizardo25@gmail.com', '+639065740811', '$2y$10$soTmL52U70IKn0zv3w4bmexBhgDc29rfDlzHc5Gz7c.c.sEjA01v.', '$2y$10$It7T6UXmZAer1C1HMwqzHOLCawHiKZx2YOfrDVQs3ihTIOsqZZaGW', 'Tenant', NULL, NULL, 1, NULL, 'image/jpeg', 'uploads/profiles/avatar_51_1777303278.jpg'),
(58, 'Kizumi', 'Kaze', 'Male', 'kizumikaze1@gmail.com', '+639065740815', '$2y$10$AflaHf99VKjCPlCwn5cYZORMI09mwvTttJsCjKiyafnsjiKDHC6Ja', '$2y$10$xVLHc30qUSrIRo4KekUHSegJEmll1MhNcGJFdgmPVCZVnD5o1ufYa', 'Tenant', NULL, NULL, 1, NULL, NULL, NULL);

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
(20, 42, 'rey', 'mis', '2', 'teryo', 'Married', 'sadsadsa', '1999-02-28', 'asdsad', 27, 'Female', 'asdasdas', 2, 'SDFDSFDSF', '', 'asdasdasd', 'dsadsad', 'FSDFDSFDSF', '2026-04-23', 'asdsadsa', 'dsadsadsad', 0, NULL, 0, NULL, '2026-04-25', '[]'),
(21, 44, 'rey', 'mis', '2', 'teryo', '', 'sadsadsa', '2026-04-08', 'asdsad', 0, 'Male', 'aasdas', 2, 'SDFDSFDSF', '', 'asdasdasd', 'dsad', 'asdsad', '2026-04-07', 'asdasdsad', 'sdasdasd', 0, NULL, 0, NULL, '2026-04-26', '[]'),
(22, 45, 'rey', 'na', '21', 'k', 'Single', 'ASDASDASD', '2025-01-28', 'ASDASD', 1, 'Male', 'asdasdasd', 2, 'SDFDSFDSF', '', 'asdsadsad', 'dsadsad', 'FSDFDSFDSF', '2026-04-29', '', '', 0, NULL, 0, NULL, '2026-04-26', '[]'),
(23, 47, 'qweqwe', 'qweqeq', 'F', 'qweqe', 'Single', 'Blk 10 Lot 34', '2000-07-21', 'Albay', 25, 'Male', 'qweqeq', 2, '213131', '', 'Jollibee', 'Blk 10 Lot 34', '09065740819', '0000-00-00', '', '', 0, NULL, 0, NULL, '2026-04-27', '[{\"name\":\"Koy Koy\",\"relation\":\"Son\",\"age\":\"12\",\"religion\":\"Islam\"}]'),
(24, 49, 'Felizardo', 'Ryan', 'Z', 'Hamza', 'Single', 'Blk 10 Lot 34', '1999-07-29', 'Albay', 26, 'Male', 'Maranao', 1, 'Tambay', '', 'Jollibee', 'Mcdo Corp', '09065740812', '2004-07-21', 'Fatima Salazar', '09194678123', 3, NULL, 0, NULL, '2026-04-27', '[{\"name\":\"Alhamdillah Salazar\",\"relation\":\"Son\",\"age\":\"22\",\"religion\":\"Islam\"}]'),
(26, 51, 'Felizardo', 'Ryan', '', '', 'Single', 'Blk 10 Lot 34', '2026-04-22', 'Albay', 0, 'Male', 'Maranao', 12, 'Tambay', '', 'Jollibee', 'Mcdo', '09065740811', '2026-04-08', 'Auster Pineda', '123541515123', 0, NULL, 0, NULL, '2026-04-27', '[{\"name\":\"Kaze Kizumi\",\"relation\":\"Son\",\"age\":\"12\",\"religion\":\"Islam\"}]'),
(34, 58, 'Kaze', 'Kizumi', '', '', 'Single', 'Blk 10 Lot 39', '2004-07-14', 'Blk 10 Lot 39', 21, 'Male', '', 2, 'saddsadas', '', 'Milwaukee', 'afasdaasdfs', '2313131', '0000-00-00', 'qwewqewqeq', '1235415151231', 0, '[]', 0, '', '2026-04-29', '[{\"name\":\"Ryan Jeff Felizardo\",\"relation\":\"Daughter\",\"age\":\"12\",\"religion\":\"Islam\"}]');

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
(62, 26, NULL, 'image/png', '2026-04-27 14:16:01', 'picture', 'uploads/tenants/doc_62_1777301970.png'),
(63, 26, NULL, 'image/png', '2026-04-27 14:16:49', 'proofofincome', 'uploads/tenants/doc_63_1777301970.png'),
(64, 26, NULL, 'image/jpeg', '2026-04-27 14:16:51', 'valididfront', 'uploads/tenants/doc_64_1777301970.jpg'),
(65, 26, NULL, 'image/jpeg', '2026-04-27 14:16:53', 'valididback', 'uploads/tenants/doc_65_1777301970.jpg'),
(66, 26, NULL, 'image/png', '2026-04-27 14:16:56', 'birthcert', 'uploads/tenants/doc_66_1777301970.png'),
(67, 26, NULL, 'image/png', '2026-04-27 14:16:59', 'nbi', 'uploads/tenants/doc_67_1777301970.png'),
(113, 34, NULL, 'image/jpeg', '2026-04-29 17:50:15', 'picture', 'uploads/tenants/doc_58_picture_1777485015.jpg'),
(114, 34, NULL, 'image/jpeg', '2026-04-29 17:51:02', 'proofofincome', 'uploads/tenants/doc_58_proofofincome_1777485062.jpg'),
(115, 34, NULL, 'image/jpeg', '2026-04-29 17:51:05', 'valididfront', 'uploads/tenants/doc_58_valididfront_1777485065.jpg'),
(116, 34, NULL, 'image/jpeg', '2026-04-29 17:51:08', 'valididback', 'uploads/tenants/doc_58_valididback_1777485068.jpg'),
(117, 34, NULL, 'image/jpeg', '2026-04-29 17:51:10', 'birthcert', 'uploads/tenants/doc_58_birthcert_1777485070.jpg'),
(118, 34, NULL, 'image/png', '2026-04-29 17:51:12', 'nbi', 'uploads/tenants/doc_58_nbi_1777485072.png');

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
(7, 58, '2026-04-29', 'Subaru', 'Basta', 'Hatchback', 'TXC-1234', '2026-04-29', NULL, 'Approved', NULL, '2026-04-29 18:42:32');

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
(9, 51, 'Farhan', '2004-07-15', 'Single', 'N/A', 'Blk 10 Lot 34', '2010', '2026-04-27 14:15:52', '2026-04-27 14:15:52'),
(16, 58, 'N/A', '2004-07-14', 'Single', 'saddsadas', 'Blk 10 Lot 39', 'N/A', '2026-04-29 17:49:59', '2026-04-29 17:50:04');

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
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_audit_logs_tenant` (`tenant_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billing_id`);

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
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_tenant` (`tenant_id`);

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
-- AUTO_INCREMENT for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `apartments_info`
--
ALTER TABLE `apartments_info`
  MODIFY `apartment_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `leases`
--
ALTER TABLE `leases`
  MODIFY `lease_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tenant_addfam`
--
ALTER TABLE `tenant_addfam`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_addinfo`
--
ALTER TABLE `tenant_addinfo`
  MODIFY `tenant_info` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tenant_addinfo_images`
--
ALTER TABLE `tenant_addinfo_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `tenant_family_members`
--
ALTER TABLE `tenant_family_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  MODIFY `parking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tenant_user_profiles`
--
ALTER TABLE `tenant_user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartmentsapp`
--
ALTER TABLE `apartmentsapp`
  ADD CONSTRAINT `fk_apartmentsapp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_app_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `apartments_info`
--
ALTER TABLE `apartments_info`
  ADD CONSTRAINT `fk_apartments_info_app` FOREIGN KEY (`application_id`) REFERENCES `apartmentsapp` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `apartment_type_images`
--
ALTER TABLE `apartment_type_images`
  ADD CONSTRAINT `apartment_type_images_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `apartment_types` (`type_id`) ON DELETE CASCADE;

--
-- Constraints for table `apartment_units`
--
ALTER TABLE `apartment_units`
  ADD CONSTRAINT `fk_unit_application` FOREIGN KEY (`application_id`) REFERENCES `apartmentsapp` (`application_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_unit_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Constraints for table `leases`
--
ALTER TABLE `leases`
  ADD CONSTRAINT `fk_lease_app` FOREIGN KEY (`application_id`) REFERENCES `apartmentsapp` (`application_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lease_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE;

--
-- Constraints for table `lease_renewals`
--
ALTER TABLE `lease_renewals`
  ADD CONSTRAINT `fk_ren_lease` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`lease_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ren_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_paym_lease` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`lease_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_paym_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE;

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
-- Constraints for table `tenant_addinfo_images`
--
ALTER TABLE `tenant_addinfo_images`
  ADD CONSTRAINT `tenant_addinfo_images_ibfk_1` FOREIGN KEY (`addinfo_id`) REFERENCES `tenant_addinfo` (`tenant_info`) ON DELETE CASCADE;

--
-- Constraints for table `tenant_parking`
--
ALTER TABLE `tenant_parking`
  ADD CONSTRAINT `fk_parking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tenant_parking_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenant_user_profiles`
--
ALTER TABLE `tenant_user_profiles`
  ADD CONSTRAINT `fk_tup_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenant_accounts` (`tenant_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
