-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 03, 2026 at 07:14 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u618910819_clinicos`
--

-- --------------------------------------------------------

--
-- Table structure for table `abdm_care_contexts`
--

CREATE TABLE `abdm_care_contexts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `care_context_reference` varchar(100) NOT NULL,
  `display_name` varchar(200) NOT NULL,
  `hi_type` varchar(50) NOT NULL,
  `fhir_resource_type` varchar(50) DEFAULT NULL,
  `fhir_bundle_url` varchar(500) DEFAULT NULL,
  `pushed_at` datetime DEFAULT NULL,
  `status` enum('active','expired','revoked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `abdm_consents`
--

CREATE TABLE `abdm_consents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `consent_request_id` varchar(100) NOT NULL,
  `status` enum('REQUESTED','GRANTED','DENIED','REVOKED','EXPIRED') NOT NULL DEFAULT 'REQUESTED',
  `purpose` varchar(30) NOT NULL,
  `hi_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`hi_types`)),
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `consent_artefact` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`consent_artefact`)),
  `consent_artefact_id` varchar(100) DEFAULT NULL,
  `granted_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `abdm_hiu_links`
--

CREATE TABLE `abdm_hiu_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `hip_id` varchar(120) DEFAULT NULL,
  `care_context_reference` varchar(200) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `gateway_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_transcriptions`
--

CREATE TABLE `ai_transcriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `audio_file_path` varchar(500) DEFAULT NULL,
  `transcript` text DEFAULT NULL,
  `mapped_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mapped_fields`)),
  `summary` text DEFAULT NULL,
  `language_detected` varchar(10) DEFAULT NULL,
  `audio_duration_seconds` int(11) DEFAULT NULL,
  `api_cost` decimal(8,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `equipment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_mins` smallint(6) NOT NULL DEFAULT 15,
  `procedure_duration_minutes` int(11) DEFAULT NULL COMMENT 'Separate procedure time beyond consultation',
  `status` enum('booked','confirmed','checked_in','in_consultation','completed','cancelled','no_show','rescheduled') NOT NULL DEFAULT 'booked',
  `token_number` smallint(5) UNSIGNED DEFAULT NULL,
  `booking_source` enum('clinic_staff','online_booking','whatsapp','phone','walk_in') NOT NULL DEFAULT 'clinic_staff',
  `appointment_type` enum('new','followup','procedure','teleconsultation') NOT NULL DEFAULT 'new',
  `specialty` varchar(50) NOT NULL,
  `opd_department` varchar(120) DEFAULT NULL COMMENT 'OPD department / session label (Phase C)',
  `advance_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `confirmation_sent_at` datetime DEFAULT NULL,
  `reminder_24h_sent_at` datetime DEFAULT NULL,
  `reminder_2h_sent_at` datetime DEFAULT NULL,
  `pre_visit_answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pre_visit_answers`)),
  `pre_visit_token` varchar(64) DEFAULT NULL COMMENT 'Unique token for pre-visit form URL',
  `notes` text DEFAULT NULL,
  `teleconsult_meeting_url` varchar(1000) DEFAULT NULL,
  `pre_visit_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Questionnaire responses from patient' CHECK (json_valid(`pre_visit_data`)),
  `rescheduled_from_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `clinic_id`, `patient_id`, `doctor_id`, `service_id`, `room_id`, `equipment_id`, `location_id`, `scheduled_at`, `duration_mins`, `procedure_duration_minutes`, `status`, `token_number`, `booking_source`, `appointment_type`, `specialty`, `opd_department`, `advance_paid`, `razorpay_order_id`, `razorpay_payment_id`, `confirmation_sent_at`, `reminder_24h_sent_at`, `reminder_2h_sent_at`, `pre_visit_answers`, `pre_visit_token`, `notes`, `teleconsult_meeting_url`, `pre_visit_data`, `rescheduled_from_id`, `cancelled_reason`, `created_at`, `updated_at`, `deleted_at`, `appointment_date`, `appointment_time`) VALUES
(1, 3, 1, 1, NULL, NULL, NULL, NULL, '2026-03-26 10:00:00', 30, NULL, 'completed', NULL, 'clinic_staff', 'new', 'dermatology', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-26 22:23:31', '2026-03-26 22:43:20', NULL, NULL, NULL),
(2, 3, 1, 1, NULL, NULL, NULL, NULL, '2026-03-27 10:00:00', 30, NULL, 'completed', NULL, 'clinic_staff', 'new', 'dermatology', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:18:47', '2026-03-27 00:48:48', NULL, NULL, NULL),
(3, 3, 1, 7, NULL, NULL, NULL, NULL, '2026-03-29 10:45:00', 30, NULL, 'confirmed', NULL, 'online_booking', 'new', 'General MD', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '{\"reason\":\"Fever\",\"medications\":\"none\",\"allergies\":\"none\"}', 'psKqhGhivx98wyEMZr0vVb9iTlWwxa3jLEGywIb4Z0hWTEa7', 'i have some Fever and Suger issue', NULL, '{\"submitted_at\":\"2026-03-28T23:14:38+05:30\",\"source\":\"public_pre_visit\"}', NULL, NULL, '2026-03-28 23:14:15', '2026-03-28 23:14:38', NULL, NULL, NULL),
(4, 3, 2, 7, NULL, NULL, NULL, NULL, '2026-04-03 10:00:00', 30, NULL, 'in_consultation', NULL, 'clinic_staff', 'new', 'General MD', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'pTwJ7Bd7lAmRWH30v60OGAEzK9pL3pSsox4ufAcwA6RsuWMV', NULL, NULL, NULL, NULL, NULL, '2026-04-03 01:59:27', '2026-04-03 16:26:21', NULL, NULL, NULL),
(5, 3, 1, 1, NULL, NULL, NULL, NULL, '2026-04-03 11:45:00', 30, NULL, 'in_consultation', NULL, 'online_booking', 'new', 'general', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'XCZZn3QnfnaILTO4fZImCXKVO8yO2mjNBUaCR3iVN9K6UGvy', NULL, NULL, NULL, NULL, NULL, '2026-04-03 02:14:57', '2026-04-03 18:32:52', NULL, NULL, NULL),
(6, 3, 1, 7, NULL, NULL, NULL, NULL, '2026-04-03 21:16:00', 15, NULL, 'checked_in', NULL, 'walk_in', 'new', 'General MD', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fever', NULL, NULL, NULL, NULL, '2026-04-03 20:16:07', '2026-04-03 22:04:37', NULL, NULL, NULL),
(7, 5, 3, 13, NULL, NULL, NULL, NULL, '2026-04-04 12:30:00', 15, NULL, 'in_consultation', 1, 'clinic_staff', 'followup', 'General Medicine', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 'jn15y6H8tNYCOoEsgUkBsDBdIvgFQ9mEsah9jjeaCStro82A', 'jlbasl jbjlasbf', NULL, NULL, NULL, NULL, '2026-04-03 20:45:10', '2026-04-04 00:25:54', NULL, NULL, NULL),
(8, 5, 3, 13, NULL, NULL, NULL, NULL, '2026-04-03 20:49:00', 15, NULL, 'in_consultation', NULL, 'walk_in', 'new', 'General Medicine', NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fever', NULL, NULL, NULL, NULL, '2026-04-03 20:49:21', '2026-04-03 21:11:29', NULL, NULL, NULL),
(9, 3, 4, 7, NULL, NULL, NULL, NULL, '2026-04-04 10:06:00', 15, NULL, 'in_consultation', 1, 'walk_in', 'new', 'General MD', 'general Medicine', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Fever', NULL, NULL, NULL, NULL, '2026-04-04 00:06:43', '2026-04-04 00:12:56', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_services`
--

CREATE TABLE `appointment_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `duration_mins` smallint(6) NOT NULL DEFAULT 15,
  `advance_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `color_hex` char(7) NOT NULL DEFAULT '#1447E6',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `requires_room` tinyint(1) NOT NULL DEFAULT 0,
  `requires_equipment` tinyint(1) NOT NULL DEFAULT 0,
  `pre_visit_questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pre_visit_questions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `clinic_id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 1, 'created', 'App\\Models\\Bed', 9, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-2\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":9}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(2, 3, 1, 'created', 'App\\Models\\Bed', 10, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-3\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":10}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(3, 3, 1, 'created', 'App\\Models\\Bed', 11, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-4\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":11}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(4, 3, 1, 'created', 'App\\Models\\Bed', 12, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-5\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":12}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(5, 3, 1, 'created', 'App\\Models\\Bed', 13, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-6\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":13}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(6, 3, 1, 'created', 'App\\Models\\Bed', 14, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-7\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":14}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(7, 3, 1, 'created', 'App\\Models\\Bed', 15, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-8\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":15}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(8, 3, 1, 'created', 'App\\Models\\Bed', 16, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-9\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":16}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(9, 3, 1, 'created', 'App\\Models\\Bed', 17, NULL, '{\"clinic_id\":3,\"room_id\":1,\"bed_code\":\"R1-B-10\",\"status\":\"available\",\"updated_at\":\"2026-04-03 20:11:00\",\"created_at\":\"2026-04-03 20:11:00\",\"id\":17}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:41:00'),
(10, 3, 1, 'created', 'App\\Models\\PharmacyStock', 1, NULL, '{\"clinic_id\":3,\"item_id\":1,\"batch_number\":\"OPENING-20260403\",\"expiry_date\":\"2028-02-03 00:00:00\",\"quantity_in\":500,\"quantity_out\":0,\"quantity_available\":500,\"purchase_rate\":12.26,\"mrp\":12.26,\"supplier_id\":null,\"grn_id\":null,\"purchase_price\":12.26,\"selling_price\":12.26,\"updated_at\":\"2026-04-03 20:13:51\",\"created_at\":\"2026-04-03 20:13:51\",\"id\":1}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 14:43:51'),
(11, 5, 12, 'created', 'App\\Models\\PharmacyStock', 2, NULL, '{\"clinic_id\":5,\"item_id\":2,\"batch_number\":\"OPENING-20260403\",\"expiry_date\":\"2028-11-15 00:00:00\",\"quantity_in\":500,\"quantity_out\":0,\"quantity_available\":500,\"purchase_rate\":12.22,\"mrp\":12.22,\"supplier_id\":null,\"grn_id\":null,\"purchase_price\":12.22,\"selling_price\":12.22,\"updated_at\":\"2026-04-03 20:51:32\",\"created_at\":\"2026-04-03 20:51:32\",\"id\":2}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:21:32'),
(12, 5, 12, 'created', 'App\\Models\\Bed', 18, NULL, '{\"clinic_id\":5,\"room_id\":3,\"bed_code\":\"R3-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:08:41\",\"created_at\":\"2026-04-03 21:08:41\",\"id\":18}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:38:41'),
(13, 5, 12, 'created', 'App\\Models\\Bed', 19, NULL, '{\"clinic_id\":5,\"room_id\":4,\"bed_code\":\"R4-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:08:46\",\"created_at\":\"2026-04-03 21:08:46\",\"id\":19}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:38:46'),
(14, 5, 12, 'created', 'App\\Models\\Bed', 20, NULL, '{\"clinic_id\":5,\"room_id\":5,\"bed_code\":\"R5-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:08:53\",\"created_at\":\"2026-04-03 21:08:53\",\"id\":20}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:38:53'),
(15, 5, 12, 'created', 'App\\Models\\Bed', 21, NULL, '{\"clinic_id\":5,\"room_id\":12,\"bed_code\":\"R12-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:08:56\",\"created_at\":\"2026-04-03 21:08:56\",\"id\":21}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:38:56'),
(16, 5, 12, 'created', 'App\\Models\\Bed', 22, NULL, '{\"clinic_id\":5,\"room_id\":11,\"bed_code\":\"R11-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:00\",\"created_at\":\"2026-04-03 21:09:00\",\"id\":22}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:00'),
(17, 5, 12, 'created', 'App\\Models\\Bed', 23, NULL, '{\"clinic_id\":5,\"room_id\":10,\"bed_code\":\"R10-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:03\",\"created_at\":\"2026-04-03 21:09:03\",\"id\":23}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:03'),
(18, 5, 12, 'created', 'App\\Models\\Bed', 24, NULL, '{\"clinic_id\":5,\"room_id\":8,\"bed_code\":\"R8-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:07\",\"created_at\":\"2026-04-03 21:09:07\",\"id\":24}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:07'),
(19, 5, 12, 'created', 'App\\Models\\Bed', 25, NULL, '{\"clinic_id\":5,\"room_id\":9,\"bed_code\":\"R9-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:16\",\"created_at\":\"2026-04-03 21:09:16\",\"id\":25}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:16'),
(20, 5, 12, 'created', 'App\\Models\\Bed', 26, NULL, '{\"clinic_id\":5,\"room_id\":9,\"bed_code\":\"R9-B-2\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:16\",\"created_at\":\"2026-04-03 21:09:16\",\"id\":26}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:16'),
(21, 5, 12, 'created', 'App\\Models\\Bed', 27, NULL, '{\"clinic_id\":5,\"room_id\":7,\"bed_code\":\"R7-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:19\",\"created_at\":\"2026-04-03 21:09:19\",\"id\":27}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:19'),
(22, 5, 12, 'created', 'App\\Models\\Bed', 28, NULL, '{\"clinic_id\":5,\"room_id\":6,\"bed_code\":\"R6-B-1\",\"status\":\"available\",\"updated_at\":\"2026-04-03 21:09:24\",\"created_at\":\"2026-04-03 21:09:24\",\"id\":28}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 15:39:24'),
(24, 5, 12, 'updated', 'App\\Models\\PharmacyStock', 2, '{\"quantity_out\":0,\"quantity_available\":500,\"updated_at\":\"2026-04-03T15:21:32.000000Z\"}', '{\"quantity_out\":10,\"quantity_available\":490,\"updated_at\":\"2026-04-03 21:58:43\"}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 16:28:43'),
(25, 5, 12, 'created', 'App\\Models\\PharmacyDispensing', 1, NULL, '{\"clinic_id\":5,\"patient_id\":\"3\",\"dispensing_number\":\"RX-69CFEABBA1CA9\",\"dispensed_by\":12,\"dispensed_at\":\"2026-04-03 21:58:43\",\"payment_mode\":\"upi\",\"notes\":null,\"total_amount\":0,\"discount_amount\":0,\"paid_amount\":0,\"total\":0,\"created_at\":\"2026-04-03 21:58:43\",\"updated_at\":\"2026-04-03 21:58:43\",\"id\":1}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 16:28:43'),
(26, 5, 12, 'lab_results_saved', 'lab_orders', 4, NULL, NULL, '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 16:29:55'),
(27, 3, 10, 'lab_results_saved', 'lab_orders', 1, NULL, NULL, '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 16:32:27'),
(28, 3, 1, 'updated', 'App\\Models\\PharmacyStock', 1, '{\"quantity_out\":0,\"quantity_available\":500,\"updated_at\":\"2026-04-03T14:43:51.000000Z\"}', '{\"quantity_out\":10,\"quantity_available\":490,\"updated_at\":\"2026-04-04 00:05:46\"}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 18:35:46'),
(29, 3, 1, 'updated', 'App\\Models\\PharmacyStock', 1, '{\"quantity_out\":10,\"quantity_available\":490}', '{\"quantity_out\":30,\"quantity_available\":470}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 18:35:46'),
(30, 3, 1, 'created', 'App\\Models\\PharmacyDispensing', 2, NULL, '{\"clinic_id\":3,\"patient_id\":\"4\",\"dispensing_number\":\"RX-69D00882C1009\",\"dispensed_by\":1,\"dispensed_at\":\"2026-04-04 00:05:46\",\"payment_mode\":\"cash\",\"notes\":null,\"total_amount\":0,\"discount_amount\":0,\"paid_amount\":0,\"total\":0,\"created_at\":\"2026-04-04 00:05:46\",\"updated_at\":\"2026-04-04 00:05:46\",\"id\":2}', '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 18:35:46'),
(31, 3, 1, 'lab_results_saved', 'lab_orders', 2, NULL, NULL, '2409:40c2:1241:26bc:a105:6414:5dc6:4c05', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 18:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

CREATE TABLE `beds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `ward_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bed_number` varchar(255) NOT NULL,
  `bed_type` enum('general','icu','nicu','maternity','pediatric') NOT NULL DEFAULT 'general',
  `status` enum('available','occupied','cleaning','maintenance','reserved') NOT NULL DEFAULT 'available',
  `floor` varchar(255) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE `clinics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `online_booking_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `booking_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '{"advance_payment_required":false,"advance_amount":0,"slot_duration_default":15,"buffer_between_appointments":5}' CHECK (json_valid(`booking_settings`)),
  `plan` enum('trial','solo','small','group','enterprise') NOT NULL DEFAULT 'solo',
  `facility_type` varchar(40) NOT NULL DEFAULT 'clinic',
  `licensed_beds` smallint(5) UNSIGNED DEFAULT NULL,
  `hims_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hims_features`)),
  `specialties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`specialties`)),
  `owner_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `pan` varchar(12) DEFAULT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `address_line1` varchar(200) DEFAULT NULL,
  `address_line2` varchar(200) DEFAULT NULL,
  `city` varchar(100) NOT NULL DEFAULT 'Pune',
  `state` varchar(100) NOT NULL DEFAULT 'Maharashtra',
  `pincode` char(6) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `hfr_id` varchar(50) DEFAULT NULL,
  `hfr_facility_id` varchar(50) DEFAULT NULL,
  `hfr_status` enum('not_registered','pending','active') NOT NULL DEFAULT 'not_registered',
  `abdm_m1_live` tinyint(1) NOT NULL DEFAULT 0,
  `abdm_m2_live` tinyint(1) NOT NULL DEFAULT 0,
  `abdm_m3_live` tinyint(1) NOT NULL DEFAULT 0,
  `razorpay_account_id` varchar(100) DEFAULT NULL,
  `whatsapp_phone_number_id` varchar(50) DEFAULT NULL,
  `whatsapp_waba_id` varchar(50) DEFAULT NULL,
  `gsp_client_id` varchar(100) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `trial_ends_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `name`, `slug`, `online_booking_enabled`, `booking_settings`, `plan`, `facility_type`, `licensed_beds`, `hims_features`, `specialties`, `owner_user_id`, `gstin`, `pan`, `registration_number`, `address_line1`, `address_line2`, `city`, `state`, `pincode`, `phone`, `email`, `logo_url`, `hfr_id`, `hfr_facility_id`, `hfr_status`, `abdm_m1_live`, `abdm_m2_live`, `abdm_m3_live`, `razorpay_account_id`, `whatsapp_phone_number_id`, `whatsapp_waba_id`, `gsp_client_id`, `settings`, `is_active`, `trial_ends_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sharma Skin Clinic', 'sharma-skin-clinic-btsY0H', 0, NULL, 'small', 'clinic', NULL, NULL, '[\"dermatology\"]', NULL, NULL, NULL, NULL, NULL, NULL, 'Pune', 'Maharashtra', NULL, NULL, NULL, NULL, NULL, NULL, 'not_registered', 0, 0, 0, NULL, NULL, NULL, NULL, '{\"invoice_prefix\":\"SSC\"}', 1, '2026-04-25 21:17:44', '2026-03-26 21:17:44', '2026-03-26 21:17:44', NULL),
(3, 'Sharma Skin Clinic', 'sharma-skin-clinic-o7T6yA', 0, NULL, 'trial', 'hospital', 50, '{\"bed_management\":true,\"ipd\":true,\"opd_hospital\":true,\"emergency\":true,\"pharmacy_inventory\":true,\"pharmacy_op_dispensing\":true,\"lis_collection\":true,\"lis_results\":true,\"lis_reports_pdf\":true,\"nursing_notes\":true,\"vitals_chart\":true,\"billing_unified\":true}', '[\"dermatology\",\"Dermatology\",\"ENT\",\"Paediatrics\",\"General Practice\",\"Neurology\",\"Gynaecology\"]', 1, 'ZHDB7657ND', NULL, NULL, 'solapur', NULL, 'Pune', 'Maharashtra', '413005', '8983839143', 'sagar.bijja@gmail.com', NULL, NULL, NULL, 'not_registered', 0, 0, 0, NULL, NULL, NULL, NULL, '{\"invoice_prefix\":\"SSC\",\"enabled_product_modules\":[\"core_scheduling\",\"clinical_emr\",\"prescriptions\",\"clinical_media\",\"lab_orders\",\"care_coordination\",\"remote_monitoring\",\"billing_core\",\"messaging_whatsapp\",\"insurance_tpa\",\"quality_compliance\",\"analytics\",\"multi_location\",\"ai_documentation\",\"custom_emr_builder\",\"billing_gst_india\",\"abdm_india\"],\"setup_completed\":true,\"setup_completed_at\":\"2026-04-02 22:56:15\",\"default_gst_rate\":\"18\",\"payment_terms\":null,\"invoice_letterhead\":null,\"invoice_footer\":null,\"invoice_tagline\":null,\"default_invoice_format\":\"gst\",\"invoice_logo_path\":\"clinics\\/3\\/branding\\/Db6nkwqKq5Ts0JzjixmWtOh2fej0wpTLxERw4EBv.png\"}', 1, '2026-04-25 00:00:00', '2026-03-26 21:19:24', '2026-04-04 00:10:49', NULL),
(4, 'Sharma Skin Clinic', 'sharma-skin-clinic-Wx2W8t', 0, NULL, 'small', 'clinic', NULL, NULL, '[\"dermatology\"]', NULL, '27AADCS1234B1ZP', NULL, NULL, '123 MG Road', NULL, 'Pune', 'Maharashtra', '411001', '+912025534567', 'info@sharmaskin.com', NULL, NULL, NULL, 'not_registered', 0, 0, 0, NULL, NULL, NULL, NULL, '{\"default_gst_rate\":18,\"payment_terms\":\"Payment due within 7 days\",\"invoice_prefix\":\"SSC\"}', 1, '2026-05-02 22:47:47', '2026-04-02 22:47:47', '2026-04-02 22:47:47', NULL),
(5, 'Sagar hospital', 'sagar-hospital', 0, NULL, 'trial', 'hospital', 50, '{\"bed_management\":true,\"opd_hospital\":true,\"ipd\":true,\"emergency\":true,\"pharmacy_inventory\":true,\"pharmacy_ip_dispensing\":true,\"pharmacy_op_dispensing\":true,\"pharmacy_purchase_grn\":true,\"pharmacy_returns\":true,\"lis_collection\":true,\"lis_processing\":true,\"lis_results\":true,\"lis_reports_pdf\":true,\"lis_hl7\":true,\"billing_unified\":true,\"billing_insurance_extended\":true,\"billing_credit_corporate\":true,\"billing_gst_slabs\":true,\"mis_revenue\":true,\"nursing_notes\":true,\"mar\":true,\"vitals_chart\":true,\"nursing_care_plans\":true,\"nursing_handover\":true,\"analytics_census\":true,\"analytics_lab_tat\":true,\"analytics_pharmacy_alerts\":true,\"analytics_opd\":true}', '[\"general\"]', 12, NULL, NULL, NULL, NULL, NULL, 'Mumbai', 'Maharashtra', NULL, NULL, NULL, NULL, NULL, NULL, 'not_registered', 0, 0, 0, NULL, NULL, NULL, NULL, '{\"enabled_product_modules\":[\"core_scheduling\",\"clinical_emr\",\"prescriptions\",\"clinical_media\",\"lab_orders\",\"care_coordination\",\"remote_monitoring\",\"billing_core\",\"messaging_whatsapp\",\"insurance_tpa\",\"quality_compliance\",\"analytics\",\"multi_location\",\"ai_documentation\",\"custom_emr_builder\",\"billing_gst_india\",\"abdm_india\"],\"invoice_prefix\":\"CLN\",\"default_gst_rate\":\"18\",\"payment_terms\":null,\"invoice_letterhead\":null,\"invoice_footer\":null,\"invoice_tagline\":null,\"default_invoice_format\":\"gst\",\"invoice_logo_path\":\"clinics\\/5\\/branding\\/I9QSLkjG3guIZEw5KjMM1lVWMQZVDN8Pp70DcSmy.png\"}', 1, '2026-05-03 19:22:08', '2026-04-03 19:22:08', '2026-04-03 20:48:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clinic_equipment`
--

CREATE TABLE `clinic_equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `equipment_type` varchar(50) NOT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'laser, electrotherapy, imaging, dental_chair',
  `serial_number` varchar(100) DEFAULT NULL,
  `last_maintenance` date DEFAULT NULL,
  `next_maintenance` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_locations`
--

CREATE TABLE `clinic_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_rooms`
--

CREATE TABLE `clinic_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'consultation, procedure, lab, dental',
  `room_type` varchar(50) DEFAULT NULL,
  `capacity` tinyint(4) NOT NULL DEFAULT 1,
  `equipment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'List of equipment in room' CHECK (json_valid(`equipment`)),
  `available_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '{"mon":"09:00-18:00", ...}' CHECK (json_valid(`available_hours`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_subscriptions`
--

CREATE TABLE `clinic_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `plan` enum('solo','small','group','enterprise') NOT NULL,
  `status` enum('trial','active','paused','cancelled','expired') NOT NULL DEFAULT 'trial',
  `billing_cycle` enum('monthly','quarterly','annual') NOT NULL DEFAULT 'monthly',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `current_period_start` timestamp NULL DEFAULT NULL,
  `current_period_end` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `razorpay_subscription_id` varchar(255) DEFAULT NULL,
  `razorpay_plan_id` varchar(255) DEFAULT NULL,
  `razorpay_customer_id` varchar(255) DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_tpa_configs`
--

CREATE TABLE `clinic_tpa_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `tpa_code` varchar(20) NOT NULL,
  `tpa_name` varchar(200) NOT NULL,
  `empanelment_id` varchar(50) DEFAULT NULL,
  `provider_id` varchar(50) DEFAULT NULL,
  `rohini_id` varchar(20) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(15) DEFAULT NULL,
  `portal_url` varchar(500) DEFAULT NULL,
  `portal_username` varchar(100) DEFAULT NULL,
  `portal_password_encrypted` varchar(500) DEFAULT NULL,
  `supported_insurers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`supported_insurers`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_vendor_links`
--

CREATE TABLE `clinic_vendor_links` (
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_preferred` tinyint(1) NOT NULL DEFAULT 0,
  `linked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_emr_templates`
--

CREATE TABLE `custom_emr_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fields`)),
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`)),
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `version` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_lab_orders`
--

CREATE TABLE `dental_lab_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_type` varchar(100) DEFAULT NULL COMMENT 'Crown, Bridge, Denture, Orthodontic, Night Guard',
  `material` varchar(100) DEFAULT NULL COMMENT 'Zirconia, PFM, E-Max, Acrylic, Metal',
  `teeth_involved` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '[11, 12, 13] FDI numbers' CHECK (json_valid(`teeth_involved`)),
  `lab_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tooth_code` varchar(3) NOT NULL,
  `work_type` varchar(100) NOT NULL,
  `shade` varchar(20) DEFAULT NULL,
  `preparation_notes` text DEFAULT NULL,
  `lab_vendor` varchar(150) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `expected_delivery` date DEFAULT NULL,
  `actual_delivery` date DEFAULT NULL,
  `lab_charge` decimal(10,2) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` enum('sent','received','fitted','rejected') NOT NULL DEFAULT 'sent',
  `cost` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_teeth`
--

CREATE TABLE `dental_teeth` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `tooth_code` varchar(3) NOT NULL,
  `status` enum('present','missing','extracted','unerupted','impacted','implant') NOT NULL DEFAULT 'present',
  `caries` enum('none','initial','moderate','advanced') NOT NULL DEFAULT 'none',
  `caries_sites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`caries_sites`)),
  `restoration` enum('none','amalgam','composite','crown','bridge','rct','veneer','implant_crown') NOT NULL DEFAULT 'none',
  `mobility_grade` tinyint(4) DEFAULT NULL,
  `recession_mm` decimal(4,1) DEFAULT NULL,
  `bop` tinyint(1) DEFAULT NULL,
  `pocketing_mm` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pocketing_mm`)),
  `furcation` tinyint(4) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_tooth_history`
--

CREATE TABLE `dental_tooth_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `tooth_code` varchar(3) NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `procedure_done` varchar(150) NOT NULL,
  `material_used` varchar(100) DEFAULT NULL,
  `operator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration_mins` tinyint(4) NOT NULL DEFAULT 15,
  `max_patients` tinyint(3) UNSIGNED DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drug_interactions`
--

CREATE TABLE `drug_interactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `drug_a_id` bigint(20) UNSIGNED NOT NULL,
  `drug_b_id` bigint(20) UNSIGNED NOT NULL,
  `severity` enum('minor','moderate','major','contraindicated') NOT NULL DEFAULT 'moderate',
  `description` text DEFAULT NULL,
  `management` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_encounters`
--

CREATE TABLE `emergency_encounters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unknown_patient_label` varchar(200) DEFAULT NULL,
  `triage_level` tinyint(3) UNSIGNED DEFAULT NULL,
  `bay_label` varchar(60) DEFAULT NULL,
  `chief_complaint` text DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'registered',
  `arrived_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_mlc` tinyint(1) NOT NULL DEFAULT 0,
  `mlc_reference` varchar(200) DEFAULT NULL,
  `resus_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_visits`
--

CREATE TABLE `emergency_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_name` varchar(200) DEFAULT NULL COMMENT 'For unknown / trauma',
  `phone` varchar(30) DEFAULT NULL,
  `triage_level` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '1–5 ESI-style',
  `chief_complaint` varchar(500) DEFAULT NULL,
  `bay_number` varchar(40) DEFAULT NULL,
  `status` enum('registered','triaged','in_treatment','discharged','admitted','left_ama') NOT NULL DEFAULT 'registered',
  `ipd_admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discharged_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gst_sac_codes`
--

CREATE TABLE `gst_sac_codes` (
  `sac_code` varchar(10) NOT NULL,
  `description` varchar(300) NOT NULL,
  `service_category` varchar(100) NOT NULL,
  `gst_rate` decimal(5,2) NOT NULL,
  `is_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `notes` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_beds`
--

CREATE TABLE `hospital_beds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `bed_code` varchar(30) NOT NULL,
  `status` varchar(24) NOT NULL DEFAULT 'available',
  `current_admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gender_restriction` varchar(16) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_beds`
--

INSERT INTO `hospital_beds` (`id`, `clinic_id`, `room_id`, `bed_code`, `status`, `current_admission_id`, `gender_restriction`, `notes`, `status_changed_at`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'BED 1', 'available', NULL, NULL, NULL, '2026-04-01 23:03:42', '2026-04-01 22:45:16', '2026-04-01 23:03:42'),
(2, 1, 2, 'B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-02 19:09:12', '2026-04-02 19:09:12'),
(3, 1, 2, 'B-2', 'available', NULL, NULL, NULL, NULL, '2026-04-02 19:09:12', '2026-04-02 19:09:12'),
(4, 1, 2, 'B-3', 'available', NULL, NULL, NULL, NULL, '2026-04-02 19:09:12', '2026-04-02 19:09:12'),
(5, 1, 2, 'B-4', 'available', NULL, NULL, NULL, NULL, '2026-04-02 19:09:12', '2026-04-02 19:09:12'),
(6, 1, 2, 'B-5', 'available', NULL, NULL, NULL, NULL, '2026-04-02 19:09:12', '2026-04-02 19:09:12'),
(9, 3, 1, 'R1-B-2', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(10, 3, 1, 'R1-B-3', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(11, 3, 1, 'R1-B-4', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(12, 3, 1, 'R1-B-5', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(13, 3, 1, 'R1-B-6', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(14, 3, 1, 'R1-B-7', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(15, 3, 1, 'R1-B-8', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(16, 3, 1, 'R1-B-9', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(17, 3, 1, 'R1-B-10', 'available', NULL, NULL, NULL, NULL, '2026-04-03 20:11:00', '2026-04-03 20:11:00'),
(18, 5, 3, 'R3-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:08:41', '2026-04-03 21:08:41'),
(19, 5, 4, 'R4-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:08:46', '2026-04-03 21:08:46'),
(20, 5, 5, 'R5-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:08:53', '2026-04-03 21:08:53'),
(21, 5, 12, 'R12-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:08:56', '2026-04-03 21:08:56'),
(22, 5, 11, 'R11-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:00', '2026-04-03 21:09:00'),
(23, 5, 10, 'R10-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:03', '2026-04-03 21:09:03'),
(24, 5, 8, 'R8-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:07', '2026-04-03 21:09:07'),
(25, 5, 9, 'R9-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:16', '2026-04-03 21:09:16'),
(26, 5, 9, 'R9-B-2', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:16', '2026-04-03 21:09:16'),
(27, 5, 7, 'R7-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:19', '2026-04-03 21:09:19'),
(28, 5, 6, 'R6-B-1', 'available', NULL, NULL, NULL, NULL, '2026-04-03 21:09:24', '2026-04-03 21:09:24');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_opd_tokens`
--

CREATE TABLE `hospital_opd_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `department` varchar(120) NOT NULL,
  `token_number` int(10) UNSIGNED NOT NULL,
  `display_label` varchar(32) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'waiting',
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `registered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `chief_complaint` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `called_at` timestamp NULL DEFAULT NULL,
  `consultation_started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` varchar(16) NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_opd_tokens`
--

INSERT INTO `hospital_opd_tokens` (`id`, `clinic_id`, `patient_id`, `service_date`, `department`, `token_number`, `display_label`, `status`, `doctor_id`, `registered_by`, `chief_complaint`, `notes`, `called_at`, `consultation_started_at`, `completed_at`, `created_at`, `updated_at`, `priority`) VALUES
(1, 3, 1, '2026-04-02', 'Dermotology', 1, 'OPD-001', 'waiting', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-04-02 01:59:32', '2026-04-02 01:59:32', 'normal');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_rooms`
--

CREATE TABLE `hospital_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `ward_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `room_type` varchar(60) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_rooms`
--

INSERT INTO `hospital_rooms` (`id`, `clinic_id`, `ward_id`, `name`, `room_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '1001', NULL, 1, '2026-04-01 22:44:59', '2026-04-01 22:44:59'),
(2, 1, 2, 'Room 101', 'general', 1, '2026-04-02 19:09:07', '2026-04-02 19:09:07'),
(3, 5, 3, '101', 'general', 1, '2026-04-03 20:47:00', '2026-04-03 20:47:00'),
(4, 5, 3, '102', 'general', 1, '2026-04-03 20:47:12', '2026-04-03 20:47:12'),
(5, 5, 3, '103', 'general', 1, '2026-04-03 20:47:15', '2026-04-03 20:47:15'),
(6, 5, 3, '104', 'general', 1, '2026-04-03 20:47:19', '2026-04-03 20:47:19'),
(7, 5, 3, '105', 'general', 1, '2026-04-03 20:47:23', '2026-04-03 20:47:23'),
(8, 5, 3, '106', 'general', 1, '2026-04-03 20:47:26', '2026-04-03 20:47:26'),
(9, 5, 3, '107', 'general', 1, '2026-04-03 20:47:30', '2026-04-03 20:47:30'),
(10, 5, 3, '108', 'general', 1, '2026-04-03 20:47:34', '2026-04-03 20:47:34'),
(11, 5, 3, '109', 'general', 1, '2026-04-03 20:47:37', '2026-04-03 20:47:37'),
(12, 5, 3, '110', 'general', 1, '2026-04-03 20:47:41', '2026-04-03 20:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_settings`
--

CREATE TABLE `hospital_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_settings`
--

INSERT INTO `hospital_settings` (`id`, `clinic_id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 3, 'hospital_name', 'Sharma clinic', NULL, '2026-04-03 02:09:48'),
(2, 3, 'hospital_type', 'hospital', NULL, '2026-04-03 02:09:48'),
(3, 3, 'total_beds', '30', NULL, '2026-04-03 02:09:48'),
(4, 3, 'icu_beds', '10', NULL, '2026-04-03 02:09:48'),
(5, 3, 'emergency_beds', '10', NULL, '2026-04-03 02:09:48'),
(6, 3, 'registration_prefix', 'IPD', NULL, '2026-04-03 02:09:48'),
(7, 3, 'discharge_summary_footer', '', NULL, '2026-04-03 02:09:48'),
(8, 3, 'enable_ipd', '1', NULL, '2026-04-03 02:09:48'),
(9, 3, 'enable_pharmacy', '1', NULL, '2026-04-03 02:09:48'),
(10, 3, 'enable_lab', '1', NULL, '2026-04-03 02:09:48'),
(11, 3, 'enable_opd_queue', '1', NULL, '2026-04-03 02:09:48'),
(12, 5, 'hospital_name', 'Sagar hospital', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(13, 5, 'hospital_type', 'hospital', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(14, 5, 'total_beds', '30', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(15, 5, 'icu_beds', '10', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(16, 5, 'emergency_beds', '10', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(17, 5, 'registration_prefix', 'IPD', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(18, 5, 'discharge_summary_footer', '', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(19, 5, 'enable_ipd', '1', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(20, 5, 'enable_pharmacy', '1', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(21, 5, 'enable_lab', '1', '2026-04-03 19:35:52', '2026-04-03 19:35:52'),
(22, 5, 'enable_opd_queue', '1', '2026-04-03 19:35:52', '2026-04-03 19:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_wards`
--

CREATE TABLE `hospital_wards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `wing` varchar(60) DEFAULT NULL,
  `floor` varchar(30) DEFAULT NULL,
  `is_icu` tinyint(1) NOT NULL DEFAULT 0,
  `isolation_type` varchar(60) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_wards`
--

INSERT INTO `hospital_wards` (`id`, `clinic_id`, `name`, `code`, `wing`, `floor`, `is_icu`, `isolation_type`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'WARD A', 'WDA', NULL, '1st', 0, NULL, 1, 1, '2026-04-01 22:44:51', '2026-04-01 22:44:51'),
(2, 1, 'General Ward', NULL, 'A', 'Ground', 0, NULL, 0, 1, '2026-04-02 19:09:01', '2026-04-02 19:09:01'),
(3, 5, 'General Ward', 'GENERALW', NULL, 'Ground', 0, NULL, 0, 1, '2026-04-03 19:36:10', '2026-04-03 19:36:10'),
(4, 5, 'Special Unit', 'SPECIALU', NULL, 'First Floor', 0, NULL, 0, 1, '2026-04-03 19:36:37', '2026-04-03 19:36:37'),
(5, 5, 'ICU', 'ICU', NULL, 'Second Floor', 1, NULL, 0, 1, '2026-04-03 19:36:59', '2026-04-03 19:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `indian_drugs`
--

CREATE TABLE `indian_drugs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `generic_name` varchar(200) NOT NULL,
  `brand_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`brand_names`)),
  `drug_class` varchar(100) DEFAULT NULL,
  `form` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `manufacturer` varchar(150) DEFAULT NULL,
  `schedule` char(2) DEFAULT NULL,
  `interactions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interactions`)),
  `contraindications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`contraindications`)),
  `common_dosages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`common_dosages`)),
  `is_controlled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_claims`
--

CREATE TABLE `insurance_claims` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `insurance_company` varchar(200) NOT NULL,
  `policy_number` varchar(100) NOT NULL,
  `tpa_name` varchar(150) DEFAULT NULL,
  `tpa_id` varchar(100) DEFAULT NULL,
  `card_number` varchar(100) DEFAULT NULL,
  `sum_insured` decimal(12,2) DEFAULT NULL,
  `claim_type` enum('cashless','reimbursement') NOT NULL,
  `claim_amount` decimal(10,2) NOT NULL,
  `approved_amount` decimal(10,2) DEFAULT NULL,
  `settled_amount` decimal(10,2) DEFAULT NULL,
  `pre_auth_number` varchar(100) DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','rejected','settled') NOT NULL DEFAULT 'draft',
  `provisional_diagnosis` text DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `rejection_reason` text DEFAULT NULL,
  `submitted_at` date DEFAULT NULL,
  `settled_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_preauths`
--

CREATE TABLE `insurance_preauths` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `tpa_code` varchar(20) NOT NULL,
  `insurance_company` varchar(200) NOT NULL,
  `policy_number` varchar(50) NOT NULL,
  `member_id` varchar(50) NOT NULL,
  `claim_type` enum('cashless','reimbursement') NOT NULL DEFAULT 'cashless',
  `admission_type` enum('planned','emergency') NOT NULL DEFAULT 'planned',
  `estimated_amount` decimal(12,2) NOT NULL,
  `approved_amount` decimal(12,2) DEFAULT NULL,
  `diagnosis_codes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`diagnosis_codes`)),
  `procedure_codes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`procedure_codes`)),
  `admission_date` date NOT NULL,
  `expected_discharge` date DEFAULT NULL,
  `treatment_details` text NOT NULL,
  `status` enum('pending','approved','partially_approved','rejected','query','cancelled') NOT NULL DEFAULT 'pending',
  `preauth_number` varchar(50) DEFAULT NULL,
  `tpa_remarks` text DEFAULT NULL,
  `query_details` text DEFAULT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(30) NOT NULL,
  `invoice_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `cgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `igst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `advance_adjusted` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','partial','paid','refunded','void') NOT NULL DEFAULT 'pending',
  `place_of_supply` char(2) NOT NULL DEFAULT '27',
  `reverse_charge` tinyint(1) NOT NULL DEFAULT 0,
  `irn` varchar(100) DEFAULT NULL,
  `ack_number` varchar(30) DEFAULT NULL,
  `irn_generated_at` datetime DEFAULT NULL,
  `is_insurance_claim` tinyint(1) NOT NULL DEFAULT 0,
  `insurer_name` varchar(150) DEFAULT NULL,
  `claim_id` varchar(100) DEFAULT NULL,
  `tpa_name` varchar(100) DEFAULT NULL,
  `pdf_url` varchar(500) DEFAULT NULL,
  `whatsapp_link_sent_at` datetime DEFAULT NULL,
  `email_sent_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `clinic_id`, `patient_id`, `visit_id`, `admission_id`, `ipd_admission_id`, `invoice_number`, `invoice_date`, `subtotal`, `discount_amount`, `discount_pct`, `cgst_amount`, `sgst_amount`, `igst_amount`, `total`, `advance_adjusted`, `paid`, `payment_status`, `place_of_supply`, `reverse_charge`, `irn`, `ack_number`, `irn_generated_at`, `is_insurance_claim`, `insurer_name`, `claim_id`, `tpa_name`, `pdf_url`, `whatsapp_link_sent_at`, `email_sent_at`, `notes`, `payment_link`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, NULL, NULL, 'CLN003-2026-0001', '2026-03-26', 999.98, 0.00, 0.00, 90.00, 90.00, 0.00, 1179.98, 0.00, 0.00, 'pending', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-26 22:45:43', '2026-03-26 22:45:43'),
(2, 3, 1, NULL, NULL, NULL, 'CLN003-2026-0002', '2026-03-26', 500.00, 0.00, 0.00, 45.00, 45.00, 0.00, 590.00, 0.00, 590.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-26 22:46:45', '2026-03-26 22:47:11'),
(3, 3, 1, NULL, NULL, NULL, 'CLN003-2026-0003', '2026-03-26', 1300.00, 0.00, 0.00, 117.00, 117.00, 0.00, 1534.00, 0.00, 0.00, 'pending', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-26 23:11:15', '2026-03-26 23:11:15'),
(4, 3, 1, 1, NULL, NULL, 'CLN003-2026-0004', '2026-03-27', 1200.00, 0.00, 0.00, 108.00, 108.00, 0.00, 1416.00, 0.00, 1416.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:33:27', '2026-03-27 00:46:48'),
(5, 3, 1, 2, NULL, NULL, 'CLN003-2026-0005', '2026-03-27', 2500.00, 0.00, 0.00, 225.00, 225.00, 0.00, 2950.00, 0.00, 2950.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:44:53', '2026-03-27 00:46:40'),
(6, 3, 1, NULL, NULL, NULL, 'CLN003-2026-0006', '2026-03-27', 4999.99, 0.00, 0.00, 450.00, 450.00, 0.00, 5899.99, 0.00, 5899.99, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:48:11', '2026-03-27 00:48:18'),
(7, 3, 1, 3, NULL, NULL, 'CLN003-2026-0007', '2026-03-27', 5000.00, 0.00, 0.00, 450.00, 450.00, 0.00, 5900.00, 0.00, 5900.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:56:35', '2026-03-27 00:56:38'),
(8, 3, 2, NULL, NULL, NULL, 'CLN003-2026-0008', '2026-04-03', 1500.00, 0.00, 0.00, 135.00, 135.00, 0.00, 1770.00, 0.00, 1770.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 02:02:45', '2026-04-03 02:03:48'),
(9, 3, 2, 4, NULL, NULL, 'CLN003-2026-0009', '2026-04-03', 500.00, 0.00, 0.00, 45.00, 45.00, 0.00, 590.00, 0.00, 590.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 02:04:08', '2026-04-03 02:04:11'),
(10, 5, 3, 10, NULL, NULL, 'CLN005-2026-0001', '2026-04-03', 1900.00, 0.00, 0.00, 171.00, 171.00, 0.00, 2242.00, 0.00, 2242.00, 'paid', '27', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 19:45:11', '2026-04-03 19:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(300) NOT NULL,
  `item_type` enum('service','procedure','product','consultation','package') NOT NULL DEFAULT 'service',
  `sac_code` varchar(10) DEFAULT NULL,
  `hsn_code` varchar(10) DEFAULT NULL,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(12,2) NOT NULL,
  `quantity` decimal(6,2) NOT NULL DEFAULT 1.00,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(12,2) NOT NULL,
  `cgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `sort_order` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `item_type`, `sac_code`, `hsn_code`, `gst_rate`, `unit_price`, `quantity`, `discount_pct`, `taxable_amount`, `cgst_amount`, `sgst_amount`, `total`, `sort_order`) VALUES
(1, 2, 'Dr Fees', 'service', '12221', NULL, 18.00, 500.00, 1.00, 0.00, 500.00, 45.00, 45.00, 590.00, 0),
(2, 3, 'asdsdas', 'service', '3453', NULL, 18.00, 1300.00, 1.00, 0.00, 1300.00, 117.00, 117.00, 1534.00, 0),
(3, 4, 'skin issue', 'service', '2311', NULL, 18.00, 1200.00, 1.00, 0.00, 1200.00, 108.00, 108.00, 1416.00, 0),
(4, 5, 'Skin', 'service', '8872', NULL, 18.00, 2500.00, 1.00, 0.00, 2500.00, 225.00, 225.00, 2950.00, 0),
(5, 6, 'ansbnsl', 'service', '23423', NULL, 18.00, 4999.99, 1.00, 0.00, 4999.99, 450.00, 450.00, 5899.99, 0),
(6, 7, 'color issue', 'service', '342', NULL, 18.00, 5000.00, 1.00, 0.00, 5000.00, 450.00, 450.00, 5900.00, 0),
(7, 8, 'fees', 'service', '352352', NULL, 18.00, 1500.00, 1.00, 0.00, 1500.00, 135.00, 135.00, 1770.00, 0),
(8, 9, '1000', 'service', '4124', NULL, 18.00, 500.00, 1.00, 0.00, 500.00, 45.00, 45.00, 590.00, 0),
(9, 10, 'Fees', 'service', '42646', NULL, 18.00, 1400.00, 1.00, 0.00, 1400.00, 126.00, 126.00, 1652.00, 0),
(10, 10, 'injection', 'service', '23525', NULL, 18.00, 500.00, 1.00, 0.00, 500.00, 45.00, 45.00, 590.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ipd_admissions`
--

CREATE TABLE `ipd_admissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `admission_number` varchar(40) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'admitted',
  `attending_doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `provisional_diagnosis` text DEFAULT NULL,
  `admitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `discharged_at` datetime DEFAULT NULL,
  `advance_paid_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `discharge_summary` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `discharge_date` timestamp NULL DEFAULT NULL,
  `bed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ward_id` bigint(20) UNSIGNED DEFAULT NULL,
  `primary_doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `diagnosis_at_admission` text DEFAULT NULL,
  `discharge_type` varchar(255) DEFAULT NULL,
  `final_diagnosis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipd_admissions`
--

INSERT INTO `ipd_admissions` (`id`, `clinic_id`, `patient_id`, `admission_number`, `status`, `attending_doctor_id`, `provisional_diagnosis`, `admitted_at`, `discharged_at`, `advance_paid_total`, `notes`, `discharge_summary`, `created_at`, `updated_at`, `discharge_date`, `bed_id`, `ward_id`, `primary_doctor_id`, `diagnosis_at_admission`, `discharge_type`, `final_diagnosis`) VALUES
(1, 3, 1, 'ADM-3-00001', 'discharged', 1, 'CAP', '2026-04-01 22:45:28', '2026-04-01 22:51:02', 0.00, NULL, NULL, '2026-04-01 22:45:28', '2026-04-01 22:51:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ipd_adt_audit_events`
--

CREATE TABLE `ipd_adt_audit_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hospital_bed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipd_adt_audit_events`
--

INSERT INTO `ipd_adt_audit_events` (`id`, `clinic_id`, `admission_id`, `hospital_bed_id`, `user_id`, `action`, `metadata`, `ip_address`, `created_at`) VALUES
(1, 3, 1, 1, 1, 'adt.bed_occupied', '{\"bed_code\":\"BED 1\"}', '2409:40c2:119d:5c4:2c3a:4b12:4542:37b4', '2026-04-01 22:45:28'),
(2, 3, 1, 1, 1, 'adt.quick_admit_bed', '{\"patient_id\":1,\"bed_code\":\"BED 1\"}', '2409:40c2:119d:5c4:2c3a:4b12:4542:37b4', '2026-04-01 22:45:28'),
(3, 3, 1, 1, 1, 'adt.discharge_bed_to_cleaning', '{\"bed_code\":\"BED 1\"}', '2409:40c2:119d:5c4:2c3a:4b12:4542:37b4', '2026-04-01 22:51:02');

-- --------------------------------------------------------

--
-- Table structure for table `ipd_bed_assignments`
--

CREATE TABLE `ipd_bed_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED NOT NULL,
  `hospital_bed_id` bigint(20) UNSIGNED NOT NULL,
  `started_at` timestamp NOT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `started_reason` varchar(32) NOT NULL DEFAULT 'unknown',
  `ended_reason` varchar(32) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_care_plans`
--

CREATE TABLE `ipd_care_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `admission_id` bigint(20) UNSIGNED NOT NULL,
  `goal` varchar(500) NOT NULL,
  `interventions` text DEFAULT NULL,
  `outcome_review` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_daily_charges`
--

CREATE TABLE `ipd_daily_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `unit_amount` decimal(12,2) NOT NULL,
  `package_code` varchar(64) DEFAULT NULL,
  `status` varchar(24) NOT NULL DEFAULT 'draft',
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_handover_notes`
--

CREATE TABLE `ipd_handover_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `admission_id` bigint(20) UNSIGNED NOT NULL,
  `shift` varchar(20) DEFAULT NULL COMMENT 'morning|evening|night',
  `summary` text NOT NULL,
  `concerns` text DEFAULT NULL,
  `handed_over_by` bigint(20) UNSIGNED NOT NULL,
  `received_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_medication_administrations`
--

CREATE TABLE `ipd_medication_administrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ipd_medication_order_id` bigint(20) UNSIGNED NOT NULL,
  `scheduled_for` timestamp NULL DEFAULT NULL,
  `administered_at` timestamp NULL DEFAULT NULL,
  `status` varchar(24) NOT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_medication_orders`
--

CREATE TABLE `ipd_medication_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED NOT NULL,
  `drug_name` varchar(200) NOT NULL,
  `dose` varchar(120) DEFAULT NULL,
  `route` varchar(64) DEFAULT NULL,
  `frequency` varchar(120) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `started_on` date NOT NULL,
  `ended_on` date DEFAULT NULL,
  `status` varchar(24) NOT NULL DEFAULT 'active',
  `prescribed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_progress_notes`
--

CREATE TABLE `ipd_progress_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `admission_id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` text NOT NULL,
  `note_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_vitals`
--

CREATE TABLE `ipd_vitals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `admission_id` bigint(20) UNSIGNED NOT NULL,
  `recorded_at` datetime NOT NULL,
  `temp_c` decimal(4,1) DEFAULT NULL,
  `bp_systolic` smallint(5) UNSIGNED DEFAULT NULL,
  `bp_diastolic` smallint(5) UNSIGNED DEFAULT NULL,
  `pulse` smallint(5) UNSIGNED DEFAULT NULL,
  `spo2` smallint(5) UNSIGNED DEFAULT NULL,
  `rr` smallint(5) UNSIGNED DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `gcs` tinyint(3) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_departments`
--

CREATE TABLE `lab_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_departments`
--

INSERT INTO `lab_departments` (`id`, `clinic_id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 5, 'General Laboratory', 'GEN', 1, '2026-04-03 21:09:55', '2026-04-03 21:09:55'),
(2, 3, 'General Laboratory', 'GEN', 1, '2026-04-03 22:02:15', '2026-04-03 22:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `lab_orders`
--

CREATE TABLE `lab_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_number` varchar(30) NOT NULL,
  `order_date` date DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'routine',
  `is_urgent` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(40) NOT NULL DEFAULT 'pending',
  `result_pdf_url` varchar(500) DEFAULT NULL,
  `result_pdf_s3_key` varchar(500) DEFAULT NULL,
  `result_sent_at` datetime DEFAULT NULL,
  `result_sent_to_patient` tinyint(1) NOT NULL DEFAULT 0,
  `fhir_resource_id` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `clinical_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `accession_number` varchar(48) DEFAULT NULL,
  `specimen_collected_at` timestamp NULL DEFAULT NULL,
  `accessioned_at` timestamp NULL DEFAULT NULL,
  `qc_status` varchar(24) DEFAULT NULL,
  `lis_notes` text DEFAULT NULL,
  `provider` varchar(50) DEFAULT NULL,
  `provider_name` varchar(100) DEFAULT NULL,
  `external_order_id` varchar(100) DEFAULT NULL,
  `tests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tests`)),
  `sample_collection_type` varchar(20) DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `collection_address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `result_url` varchar(500) DEFAULT NULL,
  `result_received_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `sample_collected_at` timestamp NULL DEFAULT NULL,
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_orders`
--

INSERT INTO `lab_orders` (`id`, `clinic_id`, `patient_id`, `doctor_id`, `visit_id`, `ipd_admission_id`, `vendor_id`, `order_number`, `order_date`, `priority`, `is_urgent`, `status`, `result_pdf_url`, `result_pdf_s3_key`, `result_sent_at`, `result_sent_to_patient`, `fhir_resource_id`, `total_amount`, `clinical_notes`, `created_at`, `updated_at`, `accession_number`, `specimen_collected_at`, `accessioned_at`, `qc_status`, `lis_notes`, `provider`, `provider_name`, `external_order_id`, `tests`, `sample_collection_type`, `collection_date`, `collection_address`, `notes`, `result_url`, `result_received_at`, `created_by`, `sample_collected_at`, `collected_by`) VALUES
(1, 3, 1, 1, NULL, NULL, NULL, 'LAB32604028491', NULL, 'routine', 0, 'completed', NULL, NULL, NULL, 0, NULL, 900.00, NULL, '2026-04-02 02:16:24', '2026-04-03 22:02:27', NULL, NULL, NULL, NULL, NULL, 'clinicos_lab', 'ClinicOS Network Lab', NULL, '[{\"code\":\"CBC\",\"name\":\"Complete Blood Count\",\"price\":350},{\"code\":\"KFT\",\"name\":\"Kidney Function Test\",\"price\":550}]', 'lab', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(2, 3, 1, 1, NULL, NULL, NULL, 'LAB32604022798', NULL, 'routine', 0, 'completed', NULL, NULL, NULL, 0, NULL, 1250.00, NULL, '2026-04-02 02:53:41', '2026-04-04 00:09:05', NULL, NULL, NULL, NULL, NULL, 'srl', 'SRL Diagnostics', NULL, '[{\"code\":\"ECG\",\"name\":\"Electrocardiogram\",\"price\":250},{\"code\":\"URINE\",\"name\":\"Urine Routine & Microscopy\",\"price\":100},{\"code\":\"VITB12\",\"name\":\"Vitamin B12\",\"price\":900}]', 'lab', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(3, 5, 3, 12, NULL, NULL, NULL, 'LAB52604039332', NULL, 'routine', 0, 'completed', NULL, NULL, NULL, 0, NULL, 1200.00, NULL, '2026-04-03 21:09:55', '2026-04-03 22:00:37', NULL, NULL, NULL, NULL, NULL, 'lal_pathlabs', 'Dr. Lal PathLabs', NULL, '[{\"code\":\"KFT\",\"name\":\"Kidney Function Test\",\"price\":550},{\"code\":\"LFT\",\"name\":\"Liver Function Test\",\"price\":650}]', 'lab', NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL),
(4, 5, 3, 12, NULL, NULL, NULL, 'LAB52604030566', NULL, 'routine', 0, 'completed', NULL, NULL, NULL, 0, NULL, 550.00, NULL, '2026-04-03 21:59:07', '2026-04-03 21:59:55', NULL, NULL, NULL, NULL, NULL, 'srl', 'SRL Diagnostics', NULL, '[{\"code\":\"KFT\",\"name\":\"Kidney Function Test\",\"price\":550}]', 'lab', NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL),
(5, 3, 4, 1, NULL, NULL, NULL, 'LAB32604040474', NULL, 'routine', 0, 'pending', NULL, NULL, NULL, 0, NULL, 1100.00, NULL, '2026-04-04 00:08:34', '2026-04-04 00:08:34', NULL, NULL, NULL, NULL, NULL, 'pathkind', 'Pathkind Labs', NULL, '[{\"code\":\"LIPID\",\"name\":\"Lipid Profile\",\"price\":450},{\"code\":\"LFT\",\"name\":\"Liver Function Test\",\"price\":650}]', 'lab', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lab_order_items`
--

CREATE TABLE `lab_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `is_abnormal` tinyint(1) NOT NULL DEFAULT 0,
  `is_critical` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_order_items`
--

INSERT INTO `lab_order_items` (`id`, `order_id`, `test_id`, `price`, `discount`, `status`, `created_at`, `updated_at`, `result_value`, `is_abnormal`, `is_critical`, `remarks`) VALUES
(1, 3, 1, 550.00, 0.00, 'pending', '2026-04-03 21:09:55', '2026-04-03 21:09:55', NULL, 0, 0, NULL),
(2, 3, 2, 650.00, 0.00, 'pending', '2026-04-03 21:09:55', '2026-04-03 21:09:55', NULL, 0, 0, NULL),
(3, 4, 1, 550.00, 0.00, 'completed', '2026-04-03 21:59:07', '2026-04-03 21:59:55', '200', 1, 1, NULL),
(4, 1, 3, 350.00, 0.00, 'completed', '2026-04-03 22:02:15', '2026-04-03 22:02:27', '120', 1, 1, NULL),
(5, 1, 4, 550.00, 0.00, 'completed', '2026-04-03 22:02:15', '2026-04-03 22:02:27', '170', 1, 1, NULL),
(6, 5, 5, 450.00, 0.00, 'pending', '2026-04-04 00:08:34', '2026-04-04 00:08:34', NULL, 0, 0, NULL),
(7, 5, 6, 650.00, 0.00, 'pending', '2026-04-04 00:08:34', '2026-04-04 00:08:34', NULL, 0, 0, NULL),
(8, 2, 7, 250.00, 0.00, 'completed', '2026-04-04 00:08:48', '2026-04-04 00:09:05', '120', 1, 0, NULL),
(9, 2, 8, 100.00, 0.00, 'completed', '2026-04-04 00:08:48', '2026-04-04 00:09:05', '100', 1, 0, NULL),
(10, 2, 9, 900.00, 0.00, 'completed', '2026-04-04 00:08:48', '2026-04-04 00:09:05', '234', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lab_order_tests`
--

CREATE TABLE `lab_order_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lab_order_id` bigint(20) UNSIGNED NOT NULL,
  `test_catalog_id` bigint(20) UNSIGNED DEFAULT NULL,
  `test_code` varchar(30) DEFAULT NULL,
  `test_name` varchar(200) NOT NULL,
  `is_urgent` tinyint(1) NOT NULL DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `result_value` varchar(200) DEFAULT NULL,
  `result_unit` varchar(50) DEFAULT NULL,
  `reference_range` varchar(100) DEFAULT NULL,
  `is_abnormal` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_partner_users`
--

CREATE TABLE `lab_partner_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_key` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_partner_users`
--

INSERT INTO `lab_partner_users` (`id`, `provider_key`, `name`, `email`, `password`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'thyrocare', 'Thyrocare Partner', 'partner.thyrocare@clinicos.demo', '$2y$12$yobfiKmHI7t/N3VolD/BB.glu7u9nap7wvmf0BIJ7iHTZ1GmXr2Sq', 1, 'a9YPOyxCbqkLjQVxWXmUfFcJm4o86tG6Gc9BRPWGLhrHe83eQOqCGSylQmvn', '2026-04-02 00:56:40', '2026-04-02 01:52:09'),
(2, 'lal_pathlabs', 'Lal PathLabs Partner', 'partner.lal@clinicos.demo', '$2y$12$Wu9gomNux/zL9IWNWxRCgOvdwxX.DAobdqfhJPmz/eN3JnbSrc3z2', 1, NULL, '2026-04-02 00:56:40', '2026-04-02 01:52:09'),
(3, 'clinicos_lab', 'ClinicOS Network Lab', 'lab.portal@clinicos.demo', '$2y$12$TBZVduFj/wC67GQS55OInOb/ElTKB9OpttP4RRvJCuAO1rWHVvnd2', 1, 'MQI8dsLSmF82UVnocM1Ujc87bMULogXZ1UqsuV8oIN9idwpyxQuEtLaW9rIh', '2026-04-02 01:48:23', '2026-04-02 01:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `sample_id` bigint(20) UNSIGNED DEFAULT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `value` text NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `normal_range` varchar(255) DEFAULT NULL,
  `is_abnormal` tinyint(1) NOT NULL DEFAULT 0,
  `is_critical` tinyint(1) NOT NULL DEFAULT 0,
  `result_date` timestamp NOT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `report_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_samples`
--

CREATE TABLE `lab_samples` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `sample_type` varchar(255) NOT NULL,
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `collected_at` timestamp NULL DEFAULT NULL,
  `received_by` bigint(20) UNSIGNED DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `status` enum('pending','collected','received','rejected','processing') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_samples`
--

INSERT INTO `lab_samples` (`id`, `clinic_id`, `order_id`, `item_id`, `barcode`, `sample_type`, `collected_by`, `collected_at`, `received_by`, `received_at`, `rejection_reason`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 4, 3, 'SMP-69CFEAE281D2A', 'blood_capillary', 12, '2026-04-03 21:59:22', NULL, NULL, NULL, 'collected', '2026-04-03 21:59:22', '2026-04-03 21:59:22'),
(2, 5, 3, 1, 'SMP-69CFEAEE70DCE', 'swab', 12, '2026-04-03 21:59:34', NULL, NULL, NULL, 'collected', '2026-04-03 21:59:34', '2026-04-03 21:59:34');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests_catalog`
--

CREATE TABLE `lab_tests_catalog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `test_code` varchar(255) NOT NULL,
  `test_name` varchar(255) NOT NULL,
  `test_type` enum('single','panel') NOT NULL DEFAULT 'single',
  `price` decimal(10,2) NOT NULL,
  `sample_type` enum('blood','urine','stool','swab','fluid','tissue','sputum','other') NOT NULL,
  `sample_volume` varchar(255) DEFAULT NULL,
  `container_type` varchar(255) DEFAULT NULL,
  `normal_range_male` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`normal_range_male`)),
  `normal_range_female` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`normal_range_female`)),
  `normal_range_child` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`normal_range_child`)),
  `unit` varchar(255) DEFAULT NULL,
  `tat_hours` int(11) NOT NULL DEFAULT 24,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_tests_catalog`
--

INSERT INTO `lab_tests_catalog` (`id`, `clinic_id`, `department_id`, `test_code`, `test_name`, `test_type`, `price`, `sample_type`, `sample_volume`, `container_type`, `normal_range_male`, `normal_range_female`, `normal_range_child`, `unit`, `tat_hours`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'KFT', 'Kidney Function Test', 'single', 550.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-03 21:09:55', '2026-04-03 21:09:55'),
(2, 5, 1, 'LFT', 'Liver Function Test', 'single', 650.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-03 21:09:55', '2026-04-03 21:09:55'),
(3, 3, 2, 'CBC', 'Complete Blood Count', 'single', 350.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-03 22:02:15', '2026-04-03 22:02:15'),
(4, 3, 2, 'KFT', 'Kidney Function Test', 'single', 550.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-03 22:02:15', '2026-04-03 22:02:15'),
(5, 3, 2, 'LIPID', 'Lipid Profile', 'single', 450.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-04 00:08:34', '2026-04-04 00:08:34'),
(6, 3, 2, 'LFT', 'Liver Function Test', 'single', 650.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-04 00:08:34', '2026-04-04 00:08:34'),
(7, 3, 2, 'ECG', 'Electrocardiogram', 'single', 250.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-04 00:08:48', '2026-04-04 00:08:48'),
(8, 3, 2, 'URINE', 'Urine Routine & Microscopy', 'single', 100.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-04 00:08:48', '2026-04-04 00:08:48'),
(9, 3, 2, 'VITB12', 'Vitamin B12', 'single', 900.00, 'blood', NULL, NULL, NULL, NULL, NULL, NULL, 24, 1, '2026-04-04 00:08:48', '2026-04-04 00:08:48');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_catalog`
--

CREATE TABLE `lab_test_catalog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `test_code` varchar(30) NOT NULL,
  `test_name` varchar(200) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `sample_type` varchar(50) DEFAULT NULL,
  `turnaround_hours` tinyint(4) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_test_panels`
--

CREATE TABLE `lab_test_panels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `panel_id` bigint(20) UNSIGNED NOT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_01_01_000001_create_core_tables', 1),
(2, '2026_03_26_000001_create_indian_drugs_table', 2),
(3, '2026_03_26_000002_create_whatsapp_automation_tables', 2),
(4, '2026_03_27_000001_add_pending_feature_tables', 2),
(5, '2026_03_27_000001_create_insurance_tables', 3),
(6, '2026_03_27_000002_create_lab_orders_table', 3),
(7, '2026_03_27_000003_create_custom_emr_templates_table', 3),
(8, '2026_03_27_000004_create_clinic_locations_table', 3),
(9, '2026_03_27_000006_phase3_completion_features', 3),
(10, '2026_03_28_000001_production_hardening', 3),
(11, '2026_03_28_000002_add_visit_prescription_whatsapp_columns', 3),
(12, '2026_04_02_100000_hims_phase_a_ipd_admissions_wards_rooms_beds', 4),
(13, '2026_04_03_100000_hims_phase_b_ipd_adt', 4),
(14, '2026_04_04_100000_hims_phase_b_plus_c_audit_opd_er', 5),
(15, '2026_04_05_000001_users_clinic_id_nullable_and_super_admin_role', 6),
(16, '2026_03_28_000003_create_drug_interactions_table', 7),
(17, '2026_03_28_000004_create_clinic_subscriptions_table', 7),
(18, '2026_04_01_120000_add_hims_foundation_to_clinics_table', 7),
(19, '2026_04_06_000001_ensure_clinics_hims_columns', 7),
(20, '2026_04_06_000002_extend_clinics_plan_enum_includes_trial', 8),
(21, '2026_04_07_000001_ipd_billing_lab_links', 9),
(22, '2026_04_08_000001_lab_partner_users_and_pharmacy_module', 10),
(23, '2026_04_09_000001_hims_bed_history_daily_charges_er_pharmacy_returns', 10),
(24, '2026_04_10_000001_hims_opd_er_polish_lis_emar', 11),
(25, '2026_04_11_000001_lab_orders_add_partner_integration_columns', 12),
(26, '2026_04_12_000001_lab_orders_status_column_string', 13),
(27, '2026_01_01_000000_create_permission_tables', 14),
(28, '2026_04_01_000001_create_hospital_structure_tables', 14),
(29, '2026_04_01_000002_create_ipd_tables', 14),
(30, '2026_04_01_000003_create_pharmacy_tables', 14),
(31, '2026_04_01_000004_create_lab_management_tables', 14),
(32, '2026_04_02_000001_seed_lab_technician_role', 14),
(33, '2026_04_02_000002_seed_pharmacist_role', 14),
(34, '2026_04_02_000003_create_audit_logs_table', 14),
(35, '2026_04_02_200000_fix_missing_hims_columns', 15),
(36, '2026_04_02_500000_fix_lab_orders_for_hims', 16),
(37, '2026_04_02_500001_make_visit_lesions_coords_nullable', 17),
(38, '2026_04_02_600000_add_pharmacist_lab_technician_to_users_enum', 18),
(39, '2026_04_02_300000_create_system_settings_table', 19),
(40, '2026_04_03_120000_add_result_columns_to_lab_order_items', 20),
(41, '2026_04_05_120000_ensure_invoices_admission_id_column', 21),
(42, '2026_04_03_000001_add_prescription_safety_ack_columns', 22),
(43, '2026_04_03_120000_add_admission_id_to_invoices', 22),
(44, '2026_04_06_100000_phase_cde_hospital_clinical_spine', 22),
(45, '2026_04_07_120000_lab_orders_accession_tracking', 22);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_queue`
--

CREATE TABLE `notification_queue` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `channel` enum('whatsapp','email','push','sms') NOT NULL DEFAULT 'whatsapp',
  `template_name` varchar(100) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `scheduled_at` datetime NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  `status` enum('pending','processing','sent','failed') NOT NULL DEFAULT 'pending',
  `attempts` tinyint(4) NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ophthal_refractions`
--

CREATE TABLE `ophthal_refractions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `refraction_type` enum('subjective','cycloplegic','manifest','contact_lens') NOT NULL DEFAULT 'subjective',
  `od_sphere` decimal(5,2) DEFAULT NULL,
  `od_cylinder` decimal(5,2) DEFAULT NULL,
  `od_axis` smallint(6) DEFAULT NULL,
  `od_add` decimal(4,2) DEFAULT NULL,
  `od_prism` decimal(4,2) DEFAULT NULL,
  `od_base` varchar(10) DEFAULT NULL,
  `os_sphere` decimal(5,2) DEFAULT NULL,
  `os_cylinder` decimal(5,2) DEFAULT NULL,
  `os_axis` smallint(6) DEFAULT NULL,
  `os_add` decimal(4,2) DEFAULT NULL,
  `os_prism` decimal(4,2) DEFAULT NULL,
  `os_base` varchar(10) DEFAULT NULL,
  `is_final_prescription` tinyint(1) NOT NULL DEFAULT 0,
  `pdf_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ophthal_va_logs`
--

CREATE TABLE `ophthal_va_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `va_od_unaided` varchar(10) DEFAULT NULL,
  `va_os_unaided` varchar(10) DEFAULT NULL,
  `va_od_pinhole` varchar(10) DEFAULT NULL,
  `va_os_pinhole` varchar(10) DEFAULT NULL,
  `va_od_bcva` varchar(10) DEFAULT NULL,
  `va_os_bcva` varchar(10) DEFAULT NULL,
  `iop_od_mmhg` decimal(4,1) DEFAULT NULL,
  `iop_os_mmhg` decimal(4,1) DEFAULT NULL,
  `iop_method` varchar(30) DEFAULT NULL,
  `iop_time` time DEFAULT NULL,
  `ac_grade_od` varchar(20) DEFAULT NULL,
  `cornea_od` varchar(100) DEFAULT NULL,
  `lens_od_locs` varchar(20) DEFAULT NULL,
  `ac_grade_os` varchar(20) DEFAULT NULL,
  `cornea_os` varchar(100) DEFAULT NULL,
  `lens_os_locs` varchar(20) DEFAULT NULL,
  `cdr_od` decimal(3,2) DEFAULT NULL,
  `cdr_os` decimal(3,2) DEFAULT NULL,
  `fundus_od_notes` text DEFAULT NULL,
  `fundus_os_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `dob` date DEFAULT NULL,
  `age_years` tinyint(3) UNSIGNED DEFAULT NULL,
  `sex` enum('M','F','O') DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `phone_alt` varchar(15) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `abha_id` varchar(20) DEFAULT NULL,
  `abha_address` varchar(100) DEFAULT NULL,
  `abha_verified` tinyint(1) NOT NULL DEFAULT 0,
  `abdm_consent_active` tinyint(1) NOT NULL DEFAULT 0,
  `known_allergies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`known_allergies`)),
  `chronic_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chronic_conditions`)),
  `current_medications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`current_medications`)),
  `family_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`family_history`)),
  `referred_by` varchar(200) DEFAULT NULL,
  `source` enum('walk_in','online_booking','referral','whatsapp','other') NOT NULL DEFAULT 'walk_in',
  `visit_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `last_visit_date` date DEFAULT NULL,
  `next_followup_date` date DEFAULT NULL,
  `photo_consent_given` tinyint(1) NOT NULL DEFAULT 0,
  `photo_consent_at` datetime DEFAULT NULL,
  `photo_consent_signature_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `clinic_id`, `name`, `dob`, `age_years`, `sex`, `blood_group`, `phone`, `phone_alt`, `email`, `address`, `abha_id`, `abha_address`, `abha_verified`, `abdm_consent_active`, `known_allergies`, `chronic_conditions`, `current_medications`, `family_history`, `referred_by`, `source`, `visit_count`, `last_visit_date`, `next_followup_date`, `photo_consent_given`, `photo_consent_at`, `photo_consent_signature_path`, `created_at`, `updated_at`, `deleted_at`, `date_of_birth`, `gender`) VALUES
(1, 3, 'Sagar Bijja', NULL, NULL, NULL, 'O+', '8983839143', NULL, 'sagar.bijja@gmail.com', 'C-31, Raghavendra Excellency geeta nagar solapur 413006', NULL, NULL, 0, 0, NULL, '\"daibetes\"', '\"not good\"', NULL, NULL, 'walk_in', 7, '2026-04-03', NULL, 0, NULL, NULL, '2026-03-26 22:15:13', '2026-04-03 18:33:18', NULL, NULL, NULL),
(2, 3, 'Samnan Kalyani', '1993-02-11', NULL, 'M', 'AB+', '8983839143', NULL, 'samu@gmail.com', 'Kumar Plams kondawa pune', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'walk_in', 2, '2026-04-03', NULL, 0, NULL, NULL, '2026-04-03 01:58:54', '2026-04-03 16:26:21', NULL, NULL, NULL),
(3, 5, 'Asad Shaikh', '1993-03-16', NULL, 'M', 'O-', '8983839143', NULL, 'asad@gmail.com', 'solapur', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'walk_in', 4, '2026-04-04', '2026-04-17', 0, NULL, NULL, '2026-04-03 19:42:19', '2026-04-04 00:25:54', NULL, NULL, NULL),
(4, 3, 'Asad Shaikh', '1993-03-16', NULL, 'M', 'O-', '8983839143', NULL, 'asad@gmail.com', 'Pune Kondawa', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'walk_in', 1, '2026-04-04', NULL, 0, NULL, NULL, '2026-04-04 00:02:34', '2026-04-04 00:12:56', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_family_members`
--

CREATE TABLE `patient_family_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `relation` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `linked_patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_photos`
--

CREATE TABLE `patient_photos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `storage_disk` varchar(32) NOT NULL DEFAULT 'public',
  `file_name` varchar(255) DEFAULT NULL,
  `s3_key` varchar(500) NOT NULL,
  `s3_bucket` varchar(100) NOT NULL DEFAULT 'clinicos-photos',
  `file_size_kb` int(10) UNSIGNED DEFAULT NULL,
  `mime_type` varchar(50) NOT NULL DEFAULT 'image/jpeg',
  `body_region` varchar(100) DEFAULT NULL,
  `body_subregion` varchar(100) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `consent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comparison_group` varchar(100) DEFAULT NULL COMMENT 'Group photos for before/after comparison',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `view_angle` varchar(30) DEFAULT NULL,
  `condition_tag` varchar(100) DEFAULT NULL,
  `procedure_tag` varchar(100) DEFAULT NULL,
  `photo_type` enum('before','after','progress','clinical') NOT NULL DEFAULT 'clinical',
  `pair_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consent_obtained` tinyint(1) NOT NULL DEFAULT 0,
  `consent_at` datetime DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `can_use_for_marketing` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_photos`
--

INSERT INTO `patient_photos` (`id`, `clinic_id`, `patient_id`, `visit_id`, `file_path`, `storage_disk`, `file_name`, `s3_key`, `s3_bucket`, `file_size_kb`, `mime_type`, `body_region`, `body_subregion`, `description`, `consent_id`, `comparison_group`, `sort_order`, `view_angle`, `condition_tag`, `procedure_tag`, `photo_type`, `pair_id`, `consent_obtained`, `consent_at`, `is_encrypted`, `can_use_for_marketing`, `uploaded_by`, `created_at`, `deleted_at`) VALUES
(1, 3, 1, NULL, NULL, 'public', NULL, 'patient_photos/clinic_3/patient_1/before_20260326_231858.jpeg', 'local', 8, 'image/jpeg', 'face', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'before', NULL, 1, '2026-03-26 23:18:58', 0, 0, 1, '2026-03-26 17:48:58', '2026-03-26 23:29:45'),
(2, 3, 1, NULL, NULL, 'public', NULL, 'patient_photos/clinic_3/patient_1/after_20260326_231921.jpeg', 'local', 123, 'image/jpeg', 'face', NULL, NULL, NULL, NULL, 0, NULL, 'asf', NULL, 'after', NULL, 1, '2026-03-26 23:19:21', 0, 0, 1, '2026-03-26 17:49:21', '2026-03-26 23:29:45'),
(3, 3, 1, NULL, NULL, 'public', NULL, 'patient_photos/clinic_3/patient_1/before_20260326_232309.jpeg', 'local', 8, 'image/jpeg', 'face', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'before', NULL, 1, '2026-03-26 23:23:09', 0, 0, 1, '2026-03-26 17:53:09', '2026-03-26 23:33:01'),
(4, 3, 1, NULL, NULL, 'public', NULL, 'patient_photos/clinic_3/patient_1/before_20260326_233324.png', 'local', 486, 'image/png', 'forehead', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'before', NULL, 1, '2026-03-26 23:33:24', 0, 0, 1, '2026-03-26 18:03:24', NULL),
(5, 3, 1, NULL, NULL, 'public', NULL, 'patient_photos/clinic_3/patient_1/after_20260326_233334.png', 'local', 6, 'image/png', 'forehead', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'after', NULL, 1, '2026-03-26 23:33:34', 0, 0, 1, '2026-03-26 18:03:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('upi','card','cash','netbanking','wallet','insurance','advance','razorpay') NOT NULL DEFAULT 'cash',
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_signature` varchar(300) DEFAULT NULL,
  `razorpay_refund_id` varchar(100) DEFAULT NULL,
  `refund_amount` decimal(12,2) DEFAULT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `notes` varchar(300) DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `clinic_id`, `invoice_id`, `patient_id`, `amount`, `payment_method`, `payment_date`, `razorpay_payment_id`, `razorpay_order_id`, `razorpay_signature`, `razorpay_refund_id`, `refund_amount`, `transaction_ref`, `notes`, `recorded_by`, `created_at`) VALUES
(1, 3, 2, 1, 590.00, 'upi', '2026-03-26 22:47:11', NULL, NULL, NULL, NULL, NULL, '2353462457346', NULL, 1, '2026-03-26 17:17:11'),
(2, 3, 4, 1, 1416.00, 'cash', '2026-03-27 00:34:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-26 19:04:12'),
(3, 3, 5, 1, 2950.00, 'cash', '2026-03-27 00:44:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-26 19:14:58'),
(4, 3, 6, 1, 5899.99, 'upi', '2026-03-27 00:48:18', NULL, NULL, NULL, NULL, NULL, '5642362456', NULL, 1, '2026-03-26 19:18:18'),
(5, 3, 7, 1, 5900.00, 'cash', '2026-03-27 00:56:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-26 19:26:38'),
(6, 3, 8, 2, 1770.00, 'cash', '2026-04-03 02:03:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 20:33:48'),
(7, 3, 9, 2, 590.00, 'cash', '2026-04-03 02:04:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 20:34:11'),
(8, 5, 10, 3, 2242.00, 'cash', '2026-04-03 19:47:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, '2026-04-03 14:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view lab orders', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(2, 'update lab samples', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(3, 'enter lab results', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(4, 'view patients', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(5, 'view lab catalog', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(6, 'view pharmacy', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(7, 'dispense medicine', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(8, 'manage inventory', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_categories`
--

CREATE TABLE `pharmacy_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_dispenses`
--

CREATE TABLE `pharmacy_dispenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ipd_admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_dispense_lines`
--

CREATE TABLE `pharmacy_dispense_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_dispense_id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_item_id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_stock_batch_id` bigint(20) UNSIGNED NOT NULL,
  `qty` decimal(14,3) NOT NULL,
  `unit_sale_rate` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_dispensing`
--

CREATE TABLE `pharmacy_dispensing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dispensed_by` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dispensing_number` varchar(255) NOT NULL,
  `dispensed_at` timestamp NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_mode` enum('cash','card','upi','credit') NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_dispensing`
--

INSERT INTO `pharmacy_dispensing` (`id`, `clinic_id`, `patient_id`, `admission_id`, `visit_id`, `dispensed_by`, `invoice_id`, `dispensing_number`, `dispensed_at`, `total_amount`, `discount_amount`, `paid_amount`, `payment_mode`, `notes`, `created_at`, `updated_at`, `total`) VALUES
(1, 5, 3, NULL, NULL, 12, NULL, 'RX-69CFEABBA1CA9', '2026-04-03 21:58:43', 0.00, 0.00, 0.00, 'upi', NULL, '2026-04-03 21:58:43', '2026-04-03 21:58:43', 0.00),
(2, 3, 4, NULL, NULL, 1, NULL, 'RX-69D00882C1009', '2026-04-04 00:05:46', 0.00, 0.00, 0.00, 'cash', NULL, '2026-04-04 00:05:46', '2026-04-04 00:05:46', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_dispensing_items`
--

CREATE TABLE `pharmacy_dispensing_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dispensing_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `instructions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_dispensing_items`
--

INSERT INTO `pharmacy_dispensing_items` (`id`, `dispensing_id`, `item_id`, `batch_number`, `quantity`, `unit_price`, `gst_amount`, `total_price`, `instructions`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'OPENING-20260403', 10, 0.00, 0.00, 0.00, NULL, '2026-04-03 21:58:43', '2026-04-03 21:58:43'),
(2, 2, 1, 'OPENING-20260403', 10, 0.00, 0.00, 0.00, NULL, '2026-04-04 00:05:46', '2026-04-04 00:05:46'),
(3, 2, 1, 'OPENING-20260403', 20, 0.00, 0.00, 0.00, NULL, '2026-04-04 00:05:46', '2026-04-04 00:05:46');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_items`
--

CREATE TABLE `pharmacy_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `sku` varchar(64) DEFAULT NULL,
  `unit` varchar(32) NOT NULL DEFAULT 'strip',
  `hsn_code` varchar(16) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reorder_level` int(11) NOT NULL DEFAULT 10,
  `mrp` decimal(10,2) DEFAULT NULL,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_items`
--

INSERT INTO `pharmacy_items` (`id`, `clinic_id`, `name`, `sku`, `unit`, `hsn_code`, `is_active`, `created_at`, `updated_at`, `reorder_level`, `mrp`, `gst_rate`) VALUES
(1, 3, 'Azitro 50mg', NULL, 'Tablet', '345346', 1, '2026-04-03 20:13:51', '2026-04-03 20:13:51', 10, 12.26, 5.00),
(2, 5, 'azitromicine 50mg', NULL, 'Capsule', '5326234', 1, '2026-04-03 20:51:32', '2026-04-03 20:51:32', 10, 12.22, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_purchases`
--

CREATE TABLE `pharmacy_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_number` varchar(255) NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `received_by` bigint(20) UNSIGNED NOT NULL,
  `received_date` date NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_purchase_items`
--

CREATE TABLE `pharmacy_purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `free_quantity` int(11) NOT NULL DEFAULT 0,
  `purchase_rate` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gst_rate` decimal(4,2) NOT NULL DEFAULT 12.00,
  `net_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_returns`
--

CREATE TABLE `pharmacy_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pharmacy_dispense_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_return_lines`
--

CREATE TABLE `pharmacy_return_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_return_id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_stock_batch_id` bigint(20) UNSIGNED NOT NULL,
  `qty` decimal(14,3) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_stock`
--

CREATE TABLE `pharmacy_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity_in` int(11) NOT NULL DEFAULT 0,
  `quantity_out` int(11) NOT NULL DEFAULT 0,
  `quantity_available` int(11) NOT NULL DEFAULT 0,
  `purchase_rate` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grn_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pharmacy_stock`
--

INSERT INTO `pharmacy_stock` (`id`, `clinic_id`, `item_id`, `batch_number`, `expiry_date`, `quantity_in`, `quantity_out`, `quantity_available`, `purchase_rate`, `mrp`, `supplier_id`, `grn_id`, `created_at`, `updated_at`, `purchase_price`, `selling_price`) VALUES
(1, 3, 1, 'OPENING-20260403', '2028-02-03', 500, 30, 470, 12.26, 12.26, NULL, NULL, '2026-04-03 20:13:51', '2026-04-04 00:05:46', 12.26, 12.26),
(2, 5, 2, 'OPENING-20260403', '2028-11-15', 500, 10, 490, 12.22, 12.22, NULL, NULL, '2026-04-03 20:51:32', '2026-04-03 21:58:43', 12.22, 12.22);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_stock_batches`
--

CREATE TABLE `pharmacy_stock_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_no` varchar(80) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `qty_on_hand` decimal(14,3) NOT NULL DEFAULT 0.000,
  `mrp` decimal(12,2) DEFAULT NULL,
  `cost_rate` decimal(12,2) DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_suppliers`
--

CREATE TABLE `pharmacy_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gst_number` varchar(255) DEFAULT NULL,
  `drug_license` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photo_consents`
--

CREATE TABLE `photo_consents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `consent_type` enum('internal','education','publication') NOT NULL,
  `consent_text` text NOT NULL,
  `signature_data` text DEFAULT NULL,
  `consented_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `revoked_at` timestamp NULL DEFAULT NULL,
  `witnessed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `physio_hep`
--

CREATE TABLE `physio_hep` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `treatment_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exercises` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '[{name, sets, reps, hold_time, frequency, instructions, image_url}]' CHECK (json_valid(`exercises`)),
  `exercise_name` varchar(150) NOT NULL,
  `sets` tinyint(4) DEFAULT NULL,
  `reps` tinyint(4) DEFAULT NULL,
  `hold_seconds` tinyint(4) DEFAULT NULL,
  `frequency_per_day` tinyint(4) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `precautions` text DEFAULT NULL,
  `progression_plan` text DEFAULT NULL,
  `prescribed_date` date DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `sent_via_whatsapp` tinyint(1) NOT NULL DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `whatsapp_sent_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `physio_treatment_plans`
--

CREATE TABLE `physio_treatment_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `diagnosis` varchar(300) NOT NULL,
  `referring_doctor` varchar(200) DEFAULT NULL,
  `total_sessions_planned` tinyint(4) DEFAULT NULL,
  `sessions_completed` tinyint(4) NOT NULL DEFAULT 0,
  `frequency` varchar(50) DEFAULT NULL COMMENT '3x/week, daily, etc.',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `short_term_goal` text DEFAULT NULL,
  `long_term_goal` text DEFAULT NULL,
  `status` enum('active','completed','discharged','dnf') NOT NULL DEFAULT 'active',
  `outcome_measures` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'FIM, Barthel scores over time' CHECK (json_valid(`outcome_measures`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `hpr_signed_ref` varchar(100) DEFAULT NULL,
  `fhir_resource_id` varchar(100) DEFAULT NULL,
  `pdf_url` varchar(500) DEFAULT NULL,
  `whatsapp_sent_at` datetime DEFAULT NULL,
  `whatsapp_message_id` varchar(100) DEFAULT NULL,
  `valid_days` tinyint(4) NOT NULL DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `safety_acknowledged_at` datetime DEFAULT NULL,
  `safety_override_reason` text DEFAULT NULL,
  `safety_acknowledged_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_drugs`
--

CREATE TABLE `prescription_drugs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prescription_id` bigint(20) UNSIGNED NOT NULL,
  `drug_db_id` bigint(20) UNSIGNED DEFAULT NULL,
  `drug_name` varchar(200) NOT NULL,
  `generic_name` varchar(200) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `form` varchar(50) DEFAULT NULL,
  `dose` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `route` varchar(30) NOT NULL DEFAULT 'oral',
  `duration` varchar(50) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `sort_order` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `drug_id` bigint(20) UNSIGNED DEFAULT NULL,
  `drug_name` varchar(255) NOT NULL,
  `dosage` varchar(255) NOT NULL,
  `frequency` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL DEFAULT 'oral',
  `instructions` text DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `is_substitutable` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_templates`
--

CREATE TABLE `prescription_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `medications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`medications`)),
  `instructions` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `razorpay_webhook_events`
--

CREATE TABLE `razorpay_webhook_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` varchar(120) NOT NULL,
  `event_type` varchar(120) DEFAULT NULL,
  `payload_json` longtext DEFAULT NULL,
  `payload_hash` varchar(64) DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `processing_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_specialty` varchar(120) DEFAULT NULL,
  `to_facility_name` varchar(200) DEFAULT NULL,
  `to_doctor_name` varchar(200) DEFAULT NULL,
  `urgency` varchar(32) NOT NULL DEFAULT 'routine',
  `reason` text DEFAULT NULL,
  `clinical_summary` text DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `clinic_id`, `patient_id`, `visit_id`, `from_doctor_id`, `to_specialty`, `to_facility_name`, `to_doctor_name`, `urgency`, `reason`, `clinical_summary`, `status`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, 1, 'skin', 'Sharma clinic', 'Yashm sharma', 'routine', 'absol hnasopjn cjoasnbfj', 'asdb fj dfhjsd njn bifhbskjdfbvbv ehfvsd likg syhd vnmds jov basuhvhsd vkha dspuivgbasdmnb vhj', 'completed', '2026-04-03 02:05:18', '2026-04-03 02:05:11', '2026-04-03 02:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'lab_technician', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46'),
(2, 'pharmacist', 'web', '2026-04-02 22:47:46', '2026-04-02 22:47:46');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(4, 2),
(6, 2),
(7, 2),
(8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `ward_id` bigint(20) UNSIGNED NOT NULL,
  `room_number` varchar(255) NOT NULL,
  `room_type` enum('general','private','semi_private','icu','isolation') NOT NULL DEFAULT 'general',
  `total_beds` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','owner','doctor','receptionist','nurse','staff','vendor_admin','lab_technician','pharmacist') NOT NULL DEFAULT 'staff',
  `specialty` varchar(50) DEFAULT NULL,
  `qualification` varchar(200) DEFAULT NULL,
  `registration_number` varchar(80) DEFAULT NULL,
  `hpr_id` varchar(30) DEFAULT NULL,
  `signature_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `clinic_id`, `name`, `email`, `phone`, `password`, `role`, `specialty`, `qualification`, `registration_number`, `hpr_id`, `signature_url`, `is_active`, `email_verified_at`, `remember_token`, `last_login_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Dr. Priya Sharma', 'demo@clinicos.com', '+919876543210', '$2y$12$090EFHcjw5Y6qLGL6cNrQuJLy03zS0IbgurA9heH8..r4Sb2YpqrG', 'owner', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'bud0eOxFgSdk2WpbAODsiAkd653jYCE7QoBVLLBSuZ30FQgXJ7TX25LA30LU', NULL, '2026-03-26 21:19:25', '2026-03-26 21:19:25', NULL),
(4, 1, 'Super Admin', 'superadmin@clinicos.com', NULL, '$2y$12$ysw09nf1Yvv/demDBELKVuhz4lNGvwSIp7.wPhm2SDknqST4rr0CO', 'super_admin', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-26 19:29:14', '5HAcfBBjWZVNfIhAiiV9cUdaLrM9zSdIcWiURg6ae6qJe1KuV9kqWuNJSwHS', NULL, '2026-03-26 19:29:14', '2026-03-27 01:13:39', NULL),
(6, 3, 'sagar bijja', 'sagar.bijja@gmail.com', '8983839143', '$2y$12$AWtNyclvUDTJMajP84.OKuU7b4pnfaIWAOmCpCXtEP4QLGtsp9Kze', 'receptionist', 'reception', NULL, NULL, NULL, NULL, 1, '2026-03-27 01:30:52', 'I4t4KlCgTDBkoyPjbyXT5wAUxpOsditKW9GGOP4czVulLl7eIx1yfRUuguPR', NULL, '2026-03-27 01:30:52', '2026-03-27 01:30:52', NULL),
(7, 3, 'Dr. Yash S', 'yash@democlinicos.com', '8983839143', '$2y$12$BGck4VEpFa6Ub24faeqWQu5FWPxMkt9/hrbx62aLU7/Wy73YJjkTW', 'doctor', 'General MD', NULL, NULL, NULL, NULL, 1, '2026-03-28 23:13:24', 'VcfEeT8NE77C8FgVzZzChmo8SzAiaTszA1NRw2aEE3DLuIH8lBsePmXTLy2y', NULL, '2026-03-28 23:13:24', '2026-03-28 23:13:24', NULL),
(8, NULL, 'Super Admin', 'admin@clinicos.com', NULL, '$2y$12$5fRvG2QvXMHdtyMgItxFjuK0GeDuqiajuCDxp2QJrIdi1Lk5sT/Va', 'super_admin', NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 22:28:48', '9ATepulw8177Ngrs0Le3tUmeW3jlirMQWhKPUb67BvpV0BE5J9alUuSvKlDz', NULL, '2026-04-01 22:28:48', '2026-04-01 22:28:48', NULL),
(10, 3, 'Dr. Lab', 'lab@clinicos.com', '8983839143', '$2y$12$Uj1iRTm2gMfOAoFi9otIsuf1EBIX1.17auA7Zfns0Hkn/A7DGoU/2', 'lab_technician', 'general medicine', NULL, NULL, NULL, NULL, 1, '2026-04-03 03:28:03', NULL, NULL, '2026-04-03 03:28:03', '2026-04-03 03:28:03', NULL),
(11, 3, 'Dr. pharma', 'pharma@clinicos.com', '8983839143', '$2y$12$wOyzHHtBEQM5woNPYiCXo.5UgciZJvKJNE7ZILfFH1xgSECVsUs0m', 'pharmacist', 'medicine', NULL, NULL, NULL, NULL, 1, '2026-04-03 03:30:42', NULL, NULL, '2026-04-03 03:30:42', '2026-04-03 03:30:42', NULL),
(12, 5, 'Dr. Sagar Bijja', 'sagar@clinicos.com', '8983839143', '$2y$12$6R0ew7bVcHj9SNr9I3r/JuMB/NsHYy/5ekQaTAJHrlTvp5WubDffO', 'owner', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'MnP52kYLK8S7ExoyLMrCtJwP6eqGXwBDUKC3JYE29ulceDQDsodWtB1P3bH5', NULL, '2026-04-03 19:22:09', '2026-04-03 19:22:09', NULL),
(13, 5, 'Dr. Samnan Kalyani', 'samnan@clinicos.com', '8983839143', '$2y$12$qx6Bnl33DVWiKaUycG3VKuvJN0TG05nrOkLli2CawoSlT5nhD5pxG', 'doctor', 'General Medicine', NULL, NULL, NULL, NULL, 1, '2026-04-03 19:38:00', NULL, NULL, '2026-04-03 19:38:00', '2026-04-03 19:38:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_labs`
--

CREATE TABLE `vendor_labs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `lab_chain` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `contact_phone` varchar(15) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `api_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `api_endpoint` varchar(300) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `specialty` varchar(50) NOT NULL,
  `visit_number` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `status` enum('draft','finalised') NOT NULL DEFAULT 'draft',
  `chief_complaint` varchar(500) DEFAULT NULL,
  `history` text DEFAULT NULL,
  `structured_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`structured_data`)),
  `diagnosis_code` varchar(20) DEFAULT NULL,
  `diagnosis_text` varchar(500) DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `followup_in_days` smallint(6) DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `ai_dictation_raw` text DEFAULT NULL,
  `ai_summary` text DEFAULT NULL,
  `fhir_bundle` longtext DEFAULT NULL,
  `fhir_resource_id` varchar(100) DEFAULT NULL,
  `abdm_care_context_id` varchar(100) DEFAULT NULL,
  `abdm_pushed_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `finalised_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `prescription_sent_whatsapp` tinyint(1) NOT NULL DEFAULT 0,
  `prescription_sent_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`id`, `clinic_id`, `patient_id`, `doctor_id`, `appointment_id`, `specialty`, `visit_number`, `status`, `chief_complaint`, `history`, `structured_data`, `diagnosis_code`, `diagnosis_text`, `plan`, `followup_in_days`, `followup_date`, `ai_dictation_raw`, `ai_summary`, `fhir_bundle`, `fhir_resource_id`, `abdm_care_context_id`, `abdm_pushed_at`, `started_at`, `finalised_at`, `created_at`, `updated_at`, `prescription_sent_whatsapp`, `prescription_sent_at`) VALUES
(1, 3, 1, 1, 2, 'general', 1, 'finalised', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:22:14', '2026-03-27 00:37:36', '2026-03-27 00:22:14', '2026-03-27 00:37:36', 0, NULL),
(2, 3, 1, 1, 2, 'general', 2, 'finalised', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:27:09', '2026-03-27 00:37:45', '2026-03-27 00:27:09', '2026-03-27 00:37:45', 0, NULL),
(3, 3, 1, 1, 2, 'general', 3, 'finalised', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-27 00:27:18', '2026-03-27 00:48:48', '2026-03-27 00:27:18', '2026-03-27 00:48:48', 0, NULL),
(4, 3, 2, 1, 4, 'general', 1, 'finalised', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 01:59:52', '2026-04-03 02:02:12', '2026-04-03 01:59:52', '2026-04-03 02:02:12', 0, NULL),
(5, 3, 1, 1, 5, 'general', 4, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 02:49:18', NULL, '2026-04-03 02:49:18', '2026-04-03 02:49:18', 0, NULL),
(6, 3, 1, 1, 5, 'general', 5, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 02:49:22', NULL, '2026-04-03 02:49:22', '2026-04-03 02:49:22', 0, NULL),
(7, 3, 2, 1, 4, 'general', 2, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 16:26:21', NULL, '2026-04-03 16:26:21', '2026-04-03 16:26:21', 0, NULL),
(8, 3, 1, 1, 5, 'general', 6, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 18:32:52', NULL, '2026-04-03 18:32:52', '2026-04-03 18:32:52', 0, NULL),
(9, 3, 1, 1, 5, 'general', 7, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 18:33:18', NULL, '2026-04-03 18:33:18', '2026-04-03 18:33:18', 0, NULL),
(10, 5, 3, 12, NULL, 'general', 1, 'finalised', NULL, NULL, '[]', NULL, 'Fever and headache', 'vbkjasdv kjnlsidugvclihasvdhjcvjad', 14, '2026-04-17', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 19:42:35', '2026-04-03 19:44:23', '2026-04-03 19:42:35', '2026-04-03 19:44:23', 0, NULL),
(11, 5, 3, 12, 8, 'general', 2, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 21:11:29', NULL, '2026-04-03 21:11:29', '2026-04-03 21:11:29', 0, NULL),
(12, 5, 3, 12, 8, 'general', 3, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-03 21:57:11', NULL, '2026-04-03 21:57:11', '2026-04-03 21:57:11', 0, NULL),
(13, 3, 4, 1, 9, 'general', 1, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-04 00:12:56', NULL, '2026-04-04 00:12:56', '2026-04-04 00:12:56', 0, NULL),
(14, 5, 3, 12, 7, 'general', 4, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-04 00:25:54', NULL, '2026-04-04 00:25:54', '2026-04-04 00:25:54', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visit_lesions`
--

CREATE TABLE `visit_lesions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `body_region` varchar(100) NOT NULL,
  `view` enum('front','back','left','right') NOT NULL DEFAULT 'front',
  `x_pct` decimal(5,2) DEFAULT 50.00,
  `y_pct` decimal(5,2) DEFAULT 50.00,
  `lesion_type` varchar(50) NOT NULL,
  `size_cm` decimal(5,2) DEFAULT NULL,
  `colour` varchar(50) DEFAULT NULL,
  `border` varchar(50) DEFAULT NULL,
  `surface` varchar(100) DEFAULT NULL,
  `distribution` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visit_lesions`
--

INSERT INTO `visit_lesions` (`id`, `visit_id`, `body_region`, `view`, `x_pct`, `y_pct`, `lesion_type`, `size_cm`, `colour`, `border`, `surface`, `distribution`, `notes`, `created_at`) VALUES
(1, 10, 'Cheek', 'front', NULL, NULL, 'Macule', NULL, NULL, NULL, NULL, NULL, 'treatment on going', '2026-04-03 14:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `visit_procedures`
--

CREATE TABLE `visit_procedures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `procedure_code` varchar(30) DEFAULT NULL,
  `sac_code` varchar(10) DEFAULT NULL COMMENT 'GST SAC code e.g. 999312',
  `procedure_name` varchar(150) NOT NULL,
  `specialty` varchar(50) NOT NULL,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters`)),
  `body_region` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `performed_by` varchar(200) DEFAULT NULL,
  `charge` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visit_scales`
--

CREATE TABLE `visit_scales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED NOT NULL,
  `scale_name` varchar(30) NOT NULL,
  `score` decimal(8,2) NOT NULL,
  `components` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`components`)),
  `interpretation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_clinic_daily_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_clinic_daily_summary` (
`clinic_id` bigint(20) unsigned
,`summary_date` date
,`total_visits` bigint(21)
,`unique_patients` bigint(21)
,`completed_visits` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `v_outstanding_invoices`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`u618910819_clinicos`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_outstanding_invoices`  AS SELECT `i`.`id` AS `id`, `i`.`clinic_id` AS `clinic_id`, `i`.`invoice_number` AS `invoice_number`, `i`.`invoice_date` AS `invoice_date`, `i`.`total` AS `total`, `i`.`paid` AS `paid`, `i`.`balance_due` AS `balance_due`, `i`.`payment_status` AS `payment_status`, `p`.`name` AS `patient_name`, `p`.`phone` AS `patient_phone`, to_days(curdate()) - to_days(`i`.`invoice_date`) AS `days_overdue` FROM (`invoices` `i` join `patients` `p` on(`p`.`id` = `i`.`patient_id`)) WHERE `i`.`payment_status` in ('pending','partial') AND `i`.`balance_due` > 0 ;
-- Error reading data for table u618910819_clinicos.v_outstanding_invoices: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `u618910819_clinicos`.`v_outstanding_invoices`' at line 1

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_today_appointments`
-- (See below for the actual view)
--
CREATE TABLE `v_today_appointments` (
`id` bigint(20) unsigned
,`clinic_id` bigint(20) unsigned
,`scheduled_at` datetime
,`duration_mins` smallint(6)
,`status` enum('booked','confirmed','checked_in','in_consultation','completed','cancelled','no_show','rescheduled')
,`token_number` smallint(5) unsigned
,`specialty` varchar(50)
,`appointment_type` enum('new','followup','procedure','teleconsultation')
,`patient_name` varchar(200)
,`patient_phone` varchar(15)
,`abha_id` varchar(20)
,`doctor_name` varchar(200)
,`service_name` varchar(150)
,`room_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `walk_in_queue`
--

CREATE TABLE `walk_in_queue` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `token_number` varchar(20) NOT NULL,
  `visit_type` varchar(30) NOT NULL DEFAULT 'walk_in',
  `chief_complaint` varchar(500) DEFAULT NULL,
  `status` enum('waiting','called','in_consultation','completed','cancelled') NOT NULL DEFAULT 'waiting',
  `checked_in_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `called_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `estimated_wait_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wards`
--

CREATE TABLE `wards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ward_type` enum('general','icu','nicu','picu','maternity','surgical','medical','orthopedic','pediatric','emergency','private','semi_private') NOT NULL DEFAULT 'general',
  `floor` varchar(255) DEFAULT NULL,
  `total_beds` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wearable_readings`
--

CREATE TABLE `wearable_readings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `device_type` varchar(64) NOT NULL,
  `source` varchar(32) NOT NULL DEFAULT 'csv_import',
  `recorded_at` timestamp NULL DEFAULT NULL,
  `systolic` smallint(5) UNSIGNED DEFAULT NULL,
  `diastolic` smallint(5) UNSIGNED DEFAULT NULL,
  `heart_rate` smallint(5) UNSIGNED DEFAULT NULL,
  `glucose_mg_dl` smallint(5) UNSIGNED DEFAULT NULL,
  `raw` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_messages`
--

CREATE TABLE `whatsapp_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `direction` enum('outbound','inbound') NOT NULL,
  `wa_message_id` varchar(100) DEFAULT NULL,
  `wa_phone_from` varchar(20) DEFAULT NULL,
  `wa_phone_to` varchar(20) DEFAULT NULL,
  `template_name` varchar(100) DEFAULT NULL,
  `message_type` enum('text','template','image','document','audio') NOT NULL DEFAULT 'text',
  `body` text DEFAULT NULL,
  `media_url` varchar(500) DEFAULT NULL,
  `trigger_type` enum('appointment_confirmation','reminder_24h','reminder_2h','prescription','payment_link','recall','hep','result','birthday','manual','inbound_reply') DEFAULT NULL,
  `related_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('queued','sent','delivered','read','failed','error') NOT NULL DEFAULT 'queued',
  `error_code` varchar(20) DEFAULT NULL,
  `error_message` varchar(300) DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_reminders`
--

CREATE TABLE `whatsapp_reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('appointment_before_1d','appointment_before_1h','follow_up','birthday') NOT NULL DEFAULT 'appointment_before_1d',
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_reminders`
--

INSERT INTO `whatsapp_reminders` (`id`, `clinic_id`, `type`, `template_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'appointment_before_1d', NULL, 1, NULL, '2026-04-03 04:52:02'),
(2, 3, 'appointment_before_1h', NULL, 1, NULL, '2026-04-03 04:52:04'),
(3, 3, 'birthday', NULL, 1, NULL, '2026-04-03 04:52:08');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_scheduled`
--

CREATE TABLE `whatsapp_scheduled` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('appointment_reminder','prescription','follow_up','birthday','custom') NOT NULL DEFAULT 'custom',
  `content` text NOT NULL,
  `scheduled_for` timestamp NOT NULL,
  `status` enum('pending','sent','failed','cancelled') NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_templates`
--

CREATE TABLE `whatsapp_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('appointment_reminder','prescription','follow_up','birthday','custom') NOT NULL DEFAULT 'custom',
  `content` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_templates`
--

INSERT INTO `whatsapp_templates` (`id`, `clinic_id`, `name`, `type`, `content`, `variables`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'appointment_confirmation', 'appointment_reminder', 'Hi {{patient_name}}, your appointment is confirmed!\n\n📅 Date: {{date}}\n⏰ Time: {{time}}\n👨‍⚕️ Doctor: Dr. {{doctor_name}}\n🏥 {{clinic_name}}\n\nPlease arrive 10 minutes early. Reply CANCEL to cancel.', '[\"patient_name\",\"date\",\"time\",\"doctor_name\",\"clinic_name\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(2, 3, 'appointment_reminder_24h', 'appointment_reminder', 'Reminder: You have an appointment tomorrow!\n\n📅 {{date}} at {{time}}\n👨‍⚕️ Dr. {{doctor_name}}\n🏥 {{clinic_name}}\n\nSee you there!', '[\"patient_name\",\"date\",\"time\",\"doctor_name\",\"clinic_name\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(3, 3, 'appointment_reminder_2h', 'appointment_reminder', 'Hi {{patient_name}}, your appointment with Dr. {{doctor_name}} is in 2 hours ({{time}}). See you soon! — {{clinic_name}}', '[\"patient_name\",\"doctor_name\",\"time\",\"clinic_name\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(4, 3, 'prescription_ready', 'prescription', 'Hi {{patient_name}}, your prescription from Dr. {{doctor_name}} is ready. You can view it in your patient portal or collect it from {{clinic_name}}.\n\nGet well soon! 🙏', '[\"patient_name\",\"doctor_name\",\"clinic_name\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(5, 3, 'lab_results_ready', 'custom', 'Dear {{patient_name}}, your lab results for order #{{order_number}} are ready. Please visit {{clinic_name}} to collect your report or contact your doctor for details.', '[\"patient_name\",\"order_number\",\"clinic_name\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(6, 3, 'follow_up_reminder', 'follow_up', 'Hi {{patient_name}}, this is a reminder for your follow-up visit with Dr. {{doctor_name}} at {{clinic_name}}.\n\n📅 Scheduled: {{date}}\n\nPlease book your appointment if you haven\'t already.', '[\"patient_name\",\"doctor_name\",\"clinic_name\",\"date\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(7, 3, 'payment_reminder', 'custom', 'Hi {{patient_name}}, you have a pending payment of ₹{{amount}} for Invoice #{{invoice_number}} at {{clinic_name}}.\n\nPay online: {{payment_link}}\n\nPlease ignore if already paid.', '[\"patient_name\",\"amount\",\"invoice_number\",\"clinic_name\",\"payment_link\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(8, 3, 'birthday_greeting', 'birthday', '🎂 Happy Birthday, {{patient_name}}! Wishing you good health and happiness from all of us at {{clinic_name}}.\n\nTake care of yourself! 🙏', '[\"patient_name\",\"clinic_name\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(9, 3, 'ipd_admission_notify', 'custom', 'Dear {{relative_name}}, {{patient_name}} has been admitted to {{clinic_name}}.\n\n🏥 Ward: {{ward}}\n🛏 Bed: {{bed}}\n👨‍⚕️ Doctor: Dr. {{doctor_name}}\n📅 Date: {{date}}\n\nVisiting hours: 10 AM - 12 PM, 4 PM - 6 PM', '[\"relative_name\",\"patient_name\",\"clinic_name\",\"ward\",\"bed\",\"doctor_name\",\"date\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(10, 3, 'discharge_summary', 'custom', 'Dear {{patient_name}}, you have been discharged from {{clinic_name}}.\n\n📋 Discharge Summary has been shared with you.\n💊 Please follow the prescribed medications.\n📅 Follow-up: {{followup_date}}\n\nWishing you a speedy recovery!', '[\"patient_name\",\"clinic_name\",\"followup_date\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13'),
(11, 3, 'pharmacy_dispensing', 'custom', 'Hi {{patient_name}}, your medicines have been dispensed from {{clinic_name}} Pharmacy.\n\n💊 Items: {{item_count}} medicines\n💰 Total: ₹{{total}}\n\nPlease follow the dosage instructions carefully.', '[\"patient_name\",\"clinic_name\",\"item_count\",\"total\"]', 1, '2026-04-03 04:52:13', '2026-04-03 04:52:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abdm_care_contexts`
--
ALTER TABLE `abdm_care_contexts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `abdm_care_contexts_care_context_reference_unique` (`care_context_reference`),
  ADD KEY `abdm_care_contexts_clinic_id_foreign` (`clinic_id`),
  ADD KEY `abdm_care_contexts_patient_id_index` (`patient_id`),
  ADD KEY `abdm_care_contexts_visit_id_foreign` (`visit_id`);

--
-- Indexes for table `abdm_consents`
--
ALTER TABLE `abdm_consents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `abdm_consents_consent_request_id_unique` (`consent_request_id`),
  ADD KEY `abdm_consents_clinic_id_foreign` (`clinic_id`),
  ADD KEY `abdm_consents_patient_id_index` (`patient_id`),
  ADD KEY `abdm_consents_status_index` (`status`);

--
-- Indexes for table `abdm_hiu_links`
--
ALTER TABLE `abdm_hiu_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abdm_hiu_links_patient_id_foreign` (`patient_id`),
  ADD KEY `abdm_hiu_links_clinic_id_patient_id_index` (`clinic_id`,`patient_id`);

--
-- Indexes for table `ai_transcriptions`
--
ALTER TABLE `ai_transcriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_transcriptions_clinic_id_created_at_index` (`clinic_id`,`created_at`),
  ADD KEY `ai_transcriptions_visit_id_index` (`visit_id`),
  ADD KEY `ai_transcriptions_user_id_index` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_pre_visit_token_unique` (`pre_visit_token`),
  ADD KEY `appointments_clinic_id_scheduled_at_index` (`clinic_id`,`scheduled_at`),
  ADD KEY `appointments_doctor_id_scheduled_at_index` (`doctor_id`,`scheduled_at`),
  ADD KEY `appointments_patient_id_index` (`patient_id`),
  ADD KEY `appointments_clinic_id_status_scheduled_at_index` (`clinic_id`,`status`,`scheduled_at`);

--
-- Indexes for table `appointment_services`
--
ALTER TABLE `appointment_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_services_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_clinic_id_action_created_at_index` (`clinic_id`,`action`,`created_at`),
  ADD KEY `audit_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`);

--
-- Indexes for table `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beds_room_id_foreign` (`room_id`),
  ADD KEY `beds_clinic_id_index` (`clinic_id`),
  ADD KEY `beds_ward_id_index` (`ward_id`),
  ADD KEY `beds_clinic_id_status_index` (`clinic_id`,`status`);

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clinics_slug_unique` (`slug`),
  ADD KEY `clinics_slug_index` (`slug`),
  ADD KEY `clinics_plan_index` (`plan`),
  ADD KEY `clinics_city_index` (`city`),
  ADD KEY `clinics_facility_type_index` (`facility_type`);

--
-- Indexes for table `clinic_equipment`
--
ALTER TABLE `clinic_equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_equipment_clinic_id_index` (`clinic_id`),
  ADD KEY `clinic_equipment_room_id_foreign` (`room_id`);

--
-- Indexes for table `clinic_locations`
--
ALTER TABLE `clinic_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_locations_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `clinic_rooms`
--
ALTER TABLE `clinic_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_rooms_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `clinic_subscriptions`
--
ALTER TABLE `clinic_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clinic_subscriptions_razorpay_subscription_id_unique` (`razorpay_subscription_id`),
  ADD KEY `clinic_subscriptions_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `clinic_tpa_configs`
--
ALTER TABLE `clinic_tpa_configs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clinic_tpa_configs_clinic_id_tpa_code_unique` (`clinic_id`,`tpa_code`);

--
-- Indexes for table `clinic_vendor_links`
--
ALTER TABLE `clinic_vendor_links`
  ADD PRIMARY KEY (`clinic_id`,`vendor_id`),
  ADD KEY `clinic_vendor_links_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `custom_emr_templates`
--
ALTER TABLE `custom_emr_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_emr_templates_created_by_foreign` (`created_by`),
  ADD KEY `custom_emr_templates_clinic_id_specialty_index` (`clinic_id`,`specialty`),
  ADD KEY `custom_emr_templates_clinic_id_is_active_index` (`clinic_id`,`is_active`);

--
-- Indexes for table `dental_lab_orders`
--
ALTER TABLE `dental_lab_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dental_lab_orders_patient_id_foreign` (`patient_id`),
  ADD KEY `dental_lab_orders_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `dental_lab_orders_doctor_id_foreign` (`doctor_id`),
  ADD KEY `dental_lab_orders_lab_id_foreign` (`lab_id`);

--
-- Indexes for table `dental_teeth`
--
ALTER TABLE `dental_teeth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dental_teeth_patient_id_tooth_code_unique` (`patient_id`,`tooth_code`),
  ADD KEY `dental_teeth_clinic_id_patient_id_index` (`clinic_id`,`patient_id`);

--
-- Indexes for table `dental_tooth_history`
--
ALTER TABLE `dental_tooth_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dental_tooth_history_visit_id_foreign` (`visit_id`),
  ADD KEY `dental_tooth_history_patient_id_tooth_code_index` (`patient_id`,`tooth_code`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_availability_clinic_id_foreign` (`clinic_id`),
  ADD KEY `doctor_availability_doctor_id_day_of_week_index` (`doctor_id`,`day_of_week`);

--
-- Indexes for table `drug_interactions`
--
ALTER TABLE `drug_interactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drug_interactions_drug_a_id_drug_b_id_unique` (`drug_a_id`,`drug_b_id`),
  ADD KEY `drug_interactions_drug_b_id_foreign` (`drug_b_id`);

--
-- Indexes for table `emergency_encounters`
--
ALTER TABLE `emergency_encounters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emergency_encounters_patient_id_foreign` (`patient_id`),
  ADD KEY `emergency_encounters_created_by_foreign` (`created_by`),
  ADD KEY `er_encounters_clinic_stat_arr_idx` (`clinic_id`,`status`,`arrived_at`),
  ADD KEY `emergency_encounters_ipd_admission_id_foreign` (`ipd_admission_id`);

--
-- Indexes for table `emergency_visits`
--
ALTER TABLE `emergency_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emergency_visits_patient_id_foreign` (`patient_id`),
  ADD KEY `emergency_visits_ipd_admission_id_foreign` (`ipd_admission_id`),
  ADD KEY `emergency_visits_registered_by_foreign` (`registered_by`),
  ADD KEY `emergency_visits_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `emergency_visits_clinic_id_registered_at_index` (`clinic_id`,`registered_at`);

--
-- Indexes for table `gst_sac_codes`
--
ALTER TABLE `gst_sac_codes`
  ADD PRIMARY KEY (`sac_code`);

--
-- Indexes for table `hospital_beds`
--
ALTER TABLE `hospital_beds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_beds_room_id_bed_code_unique` (`room_id`,`bed_code`),
  ADD KEY `hospital_beds_current_admission_id_foreign` (`current_admission_id`),
  ADD KEY `hospital_beds_clinic_id_status_index` (`clinic_id`,`status`);

--
-- Indexes for table `hospital_opd_tokens`
--
ALTER TABLE `hospital_opd_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_opd_tokens_clinic_id_service_date_token_number_unique` (`clinic_id`,`service_date`,`token_number`),
  ADD KEY `hospital_opd_tokens_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_opd_tokens_doctor_id_foreign` (`doctor_id`),
  ADD KEY `hospital_opd_tokens_registered_by_foreign` (`registered_by`),
  ADD KEY `hopd_tok_clinic_filter_idx` (`clinic_id`,`service_date`,`department`,`status`);

--
-- Indexes for table `hospital_rooms`
--
ALTER TABLE `hospital_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_rooms_ward_id_foreign` (`ward_id`),
  ADD KEY `hospital_rooms_clinic_id_ward_id_index` (`clinic_id`,`ward_id`);

--
-- Indexes for table `hospital_settings`
--
ALTER TABLE `hospital_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_settings_clinic_id_key_unique` (`clinic_id`,`key`),
  ADD KEY `hospital_settings_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `hospital_wards`
--
ALTER TABLE `hospital_wards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_wards_clinic_id_is_active_index` (`clinic_id`,`is_active`);

--
-- Indexes for table `indian_drugs`
--
ALTER TABLE `indian_drugs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indian_drugs_generic_name_index` (`generic_name`);
ALTER TABLE `indian_drugs` ADD FULLTEXT KEY `indian_drugs_generic_name_fulltext` (`generic_name`);

--
-- Indexes for table `insurance_claims`
--
ALTER TABLE `insurance_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `insurance_claims_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `insurance_claims_patient_id_index` (`patient_id`),
  ADD KEY `insurance_claims_invoice_id_index` (`invoice_id`),
  ADD KEY `insurance_claims_policy_number_index` (`policy_number`);

--
-- Indexes for table `insurance_preauths`
--
ALTER TABLE `insurance_preauths`
  ADD PRIMARY KEY (`id`),
  ADD KEY `insurance_preauths_created_by_foreign` (`created_by`),
  ADD KEY `insurance_preauths_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `insurance_preauths_patient_id_index` (`patient_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_clinic_id_invoice_date_index` (`clinic_id`,`invoice_date`),
  ADD KEY `invoices_patient_id_index` (`patient_id`),
  ADD KEY `invoices_clinic_id_payment_status_index` (`clinic_id`,`payment_status`),
  ADD KEY `invoices_ipd_admission_id_foreign` (`ipd_admission_id`),
  ADD KEY `invoices_admission_id_index` (`admission_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_index` (`invoice_id`);

--
-- Indexes for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ipd_admissions_clinic_id_admission_number_unique` (`clinic_id`,`admission_number`),
  ADD KEY `ipd_admissions_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `ipd_admissions_patient_id_status_index` (`patient_id`,`status`),
  ADD KEY `ipd_admissions_attending_doctor_id_foreign` (`attending_doctor_id`),
  ADD KEY `ipd_admissions_clinic_id_attending_doctor_id_index` (`clinic_id`,`attending_doctor_id`);

--
-- Indexes for table `ipd_adt_audit_events`
--
ALTER TABLE `ipd_adt_audit_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_adt_audit_events_hospital_bed_id_foreign` (`hospital_bed_id`),
  ADD KEY `ipd_adt_audit_events_user_id_foreign` (`user_id`),
  ADD KEY `ipd_adt_audit_events_clinic_id_created_at_index` (`clinic_id`,`created_at`),
  ADD KEY `ipd_adt_audit_events_admission_id_created_at_index` (`admission_id`,`created_at`);

--
-- Indexes for table `ipd_bed_assignments`
--
ALTER TABLE `ipd_bed_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_bed_assignments_clinic_id_foreign` (`clinic_id`),
  ADD KEY `ipd_bed_assignments_ipd_admission_id_ended_at_index` (`ipd_admission_id`,`ended_at`),
  ADD KEY `ipd_bed_assignments_hospital_bed_id_ended_at_index` (`hospital_bed_id`,`ended_at`);

--
-- Indexes for table `ipd_care_plans`
--
ALTER TABLE `ipd_care_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_care_plans_clinic_id_foreign` (`clinic_id`),
  ADD KEY `ipd_care_plans_updated_by_foreign` (`updated_by`),
  ADD KEY `ipd_care_plans_admission_id_index` (`admission_id`);

--
-- Indexes for table `ipd_daily_charges`
--
ALTER TABLE `ipd_daily_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_daily_charges_clinic_id_foreign` (`clinic_id`),
  ADD KEY `ipd_daily_charges_invoice_id_foreign` (`invoice_id`),
  ADD KEY `ipd_daily_charges_created_by_foreign` (`created_by`),
  ADD KEY `ipd_daily_charges_ipd_admission_id_status_index` (`ipd_admission_id`,`status`);

--
-- Indexes for table `ipd_handover_notes`
--
ALTER TABLE `ipd_handover_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_handover_notes_clinic_id_foreign` (`clinic_id`),
  ADD KEY `ipd_handover_notes_handed_over_by_foreign` (`handed_over_by`),
  ADD KEY `ipd_handover_notes_received_by_foreign` (`received_by`),
  ADD KEY `ipd_handover_notes_admission_id_created_at_index` (`admission_id`,`created_at`);

--
-- Indexes for table `ipd_medication_administrations`
--
ALTER TABLE `ipd_medication_administrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_medication_administrations_ipd_medication_order_id_foreign` (`ipd_medication_order_id`),
  ADD KEY `ipd_medication_administrations_recorded_by_foreign` (`recorded_by`);

--
-- Indexes for table `ipd_medication_orders`
--
ALTER TABLE `ipd_medication_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_medication_orders_clinic_id_foreign` (`clinic_id`),
  ADD KEY `ipd_medication_orders_prescribed_by_foreign` (`prescribed_by`),
  ADD KEY `ipd_medication_orders_ipd_admission_id_status_index` (`ipd_admission_id`,`status`);

--
-- Indexes for table `ipd_progress_notes`
--
ALTER TABLE `ipd_progress_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_progress_notes_clinic_id_foreign` (`clinic_id`),
  ADD KEY `ipd_progress_notes_author_id_foreign` (`author_id`),
  ADD KEY `ipd_progress_notes_admission_id_note_at_index` (`admission_id`,`note_at`);

--
-- Indexes for table `ipd_vitals`
--
ALTER TABLE `ipd_vitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_vitals_recorded_by_foreign` (`recorded_by`),
  ADD KEY `ipd_vitals_admission_id_recorded_at_index` (`admission_id`,`recorded_at`),
  ADD KEY `ipd_vitals_clinic_id_recorded_at_index` (`clinic_id`,`recorded_at`);

--
-- Indexes for table `lab_departments`
--
ALTER TABLE `lab_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_departments_clinic_id_index` (`clinic_id`),
  ADD KEY `lab_departments_clinic_id_is_active_index` (`clinic_id`,`is_active`);

--
-- Indexes for table `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_orders_order_number_unique` (`order_number`),
  ADD KEY `lab_orders_patient_id_foreign` (`patient_id`),
  ADD KEY `lab_orders_doctor_id_foreign` (`doctor_id`),
  ADD KEY `lab_orders_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `lab_orders_vendor_id_status_index` (`vendor_id`,`status`),
  ADD KEY `lab_orders_visit_id_index` (`visit_id`),
  ADD KEY `lab_orders_ipd_admission_id_foreign` (`ipd_admission_id`),
  ADD KEY `lab_orders_created_by_foreign` (`created_by`),
  ADD KEY `lab_orders_provider_index` (`provider`),
  ADD KEY `lab_orders_collected_by_foreign` (`collected_by`);

--
-- Indexes for table `lab_order_items`
--
ALTER TABLE `lab_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_order_items_order_id_index` (`order_id`),
  ADD KEY `lab_order_items_test_id_index` (`test_id`),
  ADD KEY `lab_order_items_order_id_status_index` (`order_id`,`status`);

--
-- Indexes for table `lab_order_tests`
--
ALTER TABLE `lab_order_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_order_tests_lab_order_id_index` (`lab_order_id`);

--
-- Indexes for table `lab_partner_users`
--
ALTER TABLE `lab_partner_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_partner_users_email_unique` (`email`),
  ADD KEY `lab_partner_users_provider_key_index` (`provider_key`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_results_sample_id_foreign` (`sample_id`),
  ADD KEY `lab_results_verified_by_foreign` (`verified_by`),
  ADD KEY `lab_results_clinic_id_index` (`clinic_id`),
  ADD KEY `lab_results_order_item_id_index` (`order_item_id`),
  ADD KEY `lab_results_test_id_index` (`test_id`),
  ADD KEY `lab_results_clinic_id_result_date_index` (`clinic_id`,`result_date`),
  ADD KEY `lab_results_clinic_id_is_critical_index` (`clinic_id`,`is_critical`);

--
-- Indexes for table `lab_samples`
--
ALTER TABLE `lab_samples`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_samples_barcode_unique` (`barcode`),
  ADD KEY `lab_samples_item_id_foreign` (`item_id`),
  ADD KEY `lab_samples_collected_by_foreign` (`collected_by`),
  ADD KEY `lab_samples_received_by_foreign` (`received_by`),
  ADD KEY `lab_samples_clinic_id_index` (`clinic_id`),
  ADD KEY `lab_samples_order_id_index` (`order_id`),
  ADD KEY `lab_samples_barcode_index` (`barcode`),
  ADD KEY `lab_samples_clinic_id_status_index` (`clinic_id`,`status`);

--
-- Indexes for table `lab_tests_catalog`
--
ALTER TABLE `lab_tests_catalog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_tests_catalog_clinic_id_index` (`clinic_id`),
  ADD KEY `lab_tests_catalog_department_id_index` (`department_id`),
  ADD KEY `lab_tests_catalog_clinic_id_is_active_index` (`clinic_id`,`is_active`),
  ADD KEY `lab_tests_catalog_clinic_id_test_code_index` (`clinic_id`,`test_code`);

--
-- Indexes for table `lab_test_catalog`
--
ALTER TABLE `lab_test_catalog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_test_catalog_vendor_id_test_code_unique` (`vendor_id`,`test_code`);

--
-- Indexes for table `lab_test_panels`
--
ALTER TABLE `lab_test_panels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_test_panels_panel_id_test_id_unique` (`panel_id`,`test_id`),
  ADD KEY `lab_test_panels_test_id_foreign` (`test_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notification_queue`
--
ALTER TABLE `notification_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_queue_status_scheduled_at_index` (`status`,`scheduled_at`),
  ADD KEY `notification_queue_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `ophthal_refractions`
--
ALTER TABLE `ophthal_refractions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ophthal_refractions_visit_id_foreign` (`visit_id`),
  ADD KEY `ophthal_refractions_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `ophthal_va_logs`
--
ALTER TABLE `ophthal_va_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ophthal_va_logs_patient_id_foreign` (`patient_id`),
  ADD KEY `ophthal_va_logs_visit_id_index` (`visit_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patients_clinic_id_phone_index` (`clinic_id`,`phone`),
  ADD KEY `patients_abha_id_index` (`abha_id`),
  ADD KEY `patients_clinic_id_name_index` (`clinic_id`,`name`),
  ADD KEY `patients_clinic_id_last_visit_date_index` (`clinic_id`,`last_visit_date`);

--
-- Indexes for table `patient_family_members`
--
ALTER TABLE `patient_family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_family_members_clinic_id_foreign` (`clinic_id`),
  ADD KEY `patient_family_members_patient_id_index` (`patient_id`);

--
-- Indexes for table `patient_photos`
--
ALTER TABLE `patient_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_photos_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `patient_photos_patient_id_body_region_index` (`patient_id`,`body_region`),
  ADD KEY `patient_photos_visit_id_index` (`visit_id`),
  ADD KEY `patient_photos_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `patient_photos_consent_id_foreign` (`consent_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_razorpay_payment_id_unique` (`razorpay_payment_id`),
  ADD KEY `payments_patient_id_foreign` (`patient_id`),
  ADD KEY `payments_invoice_id_index` (`invoice_id`),
  ADD KEY `payments_clinic_id_payment_date_index` (`clinic_id`,`payment_date`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pharmacy_categories`
--
ALTER TABLE `pharmacy_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_categories_parent_id_foreign` (`parent_id`),
  ADD KEY `pharmacy_categories_clinic_id_index` (`clinic_id`),
  ADD KEY `pharmacy_categories_clinic_id_parent_id_index` (`clinic_id`,`parent_id`);

--
-- Indexes for table `pharmacy_dispenses`
--
ALTER TABLE `pharmacy_dispenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_dispenses_patient_id_foreign` (`patient_id`),
  ADD KEY `pharmacy_dispenses_user_id_foreign` (`user_id`),
  ADD KEY `pharmacy_dispenses_visit_id_foreign` (`visit_id`),
  ADD KEY `pharmacy_dispenses_ipd_admission_id_foreign` (`ipd_admission_id`),
  ADD KEY `pharmacy_dispenses_clinic_id_patient_id_index` (`clinic_id`,`patient_id`);

--
-- Indexes for table `pharmacy_dispense_lines`
--
ALTER TABLE `pharmacy_dispense_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_dispense_lines_pharmacy_dispense_id_foreign` (`pharmacy_dispense_id`),
  ADD KEY `pharmacy_dispense_lines_pharmacy_item_id_foreign` (`pharmacy_item_id`),
  ADD KEY `pharmacy_dispense_lines_pharmacy_stock_batch_id_foreign` (`pharmacy_stock_batch_id`);

--
-- Indexes for table `pharmacy_dispensing`
--
ALTER TABLE `pharmacy_dispensing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pharmacy_dispensing_dispensing_number_unique` (`dispensing_number`),
  ADD KEY `pharmacy_dispensing_visit_id_foreign` (`visit_id`),
  ADD KEY `pharmacy_dispensing_dispensed_by_foreign` (`dispensed_by`),
  ADD KEY `pharmacy_dispensing_invoice_id_foreign` (`invoice_id`),
  ADD KEY `pharmacy_dispensing_clinic_id_index` (`clinic_id`),
  ADD KEY `pharmacy_dispensing_patient_id_index` (`patient_id`),
  ADD KEY `pharmacy_dispensing_admission_id_index` (`admission_id`),
  ADD KEY `pharmacy_dispensing_clinic_id_dispensed_at_index` (`clinic_id`,`dispensed_at`);

--
-- Indexes for table `pharmacy_dispensing_items`
--
ALTER TABLE `pharmacy_dispensing_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_dispensing_items_dispensing_id_index` (`dispensing_id`),
  ADD KEY `pharmacy_dispensing_items_item_id_index` (`item_id`);

--
-- Indexes for table `pharmacy_items`
--
ALTER TABLE `pharmacy_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_items_clinic_id_name_index` (`clinic_id`,`name`);

--
-- Indexes for table `pharmacy_purchases`
--
ALTER TABLE `pharmacy_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pharmacy_purchases_purchase_number_unique` (`purchase_number`),
  ADD KEY `pharmacy_purchases_received_by_foreign` (`received_by`),
  ADD KEY `pharmacy_purchases_clinic_id_index` (`clinic_id`),
  ADD KEY `pharmacy_purchases_clinic_id_payment_status_index` (`clinic_id`,`payment_status`),
  ADD KEY `pharmacy_purchases_supplier_id_index` (`supplier_id`);

--
-- Indexes for table `pharmacy_purchase_items`
--
ALTER TABLE `pharmacy_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_purchase_items_purchase_id_index` (`purchase_id`),
  ADD KEY `pharmacy_purchase_items_item_id_index` (`item_id`);

--
-- Indexes for table `pharmacy_returns`
--
ALTER TABLE `pharmacy_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_returns_clinic_id_foreign` (`clinic_id`),
  ADD KEY `pharmacy_returns_patient_id_foreign` (`patient_id`),
  ADD KEY `pharmacy_returns_pharmacy_dispense_id_foreign` (`pharmacy_dispense_id`),
  ADD KEY `pharmacy_returns_user_id_foreign` (`user_id`);

--
-- Indexes for table `pharmacy_return_lines`
--
ALTER TABLE `pharmacy_return_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_return_lines_pharmacy_return_id_foreign` (`pharmacy_return_id`),
  ADD KEY `pharmacy_return_lines_pharmacy_stock_batch_id_foreign` (`pharmacy_stock_batch_id`);

--
-- Indexes for table `pharmacy_stock`
--
ALTER TABLE `pharmacy_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_stock_supplier_id_foreign` (`supplier_id`),
  ADD KEY `pharmacy_stock_grn_id_foreign` (`grn_id`),
  ADD KEY `pharmacy_stock_clinic_id_index` (`clinic_id`),
  ADD KEY `pharmacy_stock_item_id_index` (`item_id`),
  ADD KEY `pharmacy_stock_item_id_expiry_date_index` (`item_id`,`expiry_date`),
  ADD KEY `pharmacy_stock_clinic_id_item_id_index` (`clinic_id`,`item_id`);

--
-- Indexes for table `pharmacy_stock_batches`
--
ALTER TABLE `pharmacy_stock_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_stock_batches_pharmacy_item_id_foreign` (`pharmacy_item_id`),
  ADD KEY `pharmacy_stock_batches_clinic_id_pharmacy_item_id_index` (`clinic_id`,`pharmacy_item_id`);

--
-- Indexes for table `pharmacy_suppliers`
--
ALTER TABLE `pharmacy_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_suppliers_clinic_id_index` (`clinic_id`),
  ADD KEY `pharmacy_suppliers_clinic_id_is_active_index` (`clinic_id`,`is_active`);

--
-- Indexes for table `photo_consents`
--
ALTER TABLE `photo_consents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_consents_witnessed_by_foreign` (`witnessed_by`),
  ADD KEY `photo_consents_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `photo_consents_patient_id_index` (`patient_id`);

--
-- Indexes for table `physio_hep`
--
ALTER TABLE `physio_hep`
  ADD PRIMARY KEY (`id`),
  ADD KEY `physio_hep_patient_id_foreign` (`patient_id`),
  ADD KEY `physio_hep_visit_id_index` (`visit_id`),
  ADD KEY `physio_hep_clinic_id_foreign` (`clinic_id`),
  ADD KEY `physio_hep_treatment_plan_id_foreign` (`treatment_plan_id`);

--
-- Indexes for table `physio_treatment_plans`
--
ALTER TABLE `physio_treatment_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `physio_treatment_plans_clinic_id_foreign` (`clinic_id`),
  ADD KEY `physio_treatment_plans_visit_id_foreign` (`visit_id`),
  ADD KEY `physio_treatment_plans_patient_id_index` (`patient_id`),
  ADD KEY `physio_treatment_plans_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescriptions_clinic_id_foreign` (`clinic_id`),
  ADD KEY `prescriptions_doctor_id_foreign` (`doctor_id`),
  ADD KEY `prescriptions_visit_id_index` (`visit_id`),
  ADD KEY `prescriptions_patient_id_index` (`patient_id`),
  ADD KEY `prescriptions_safety_acknowledged_by_foreign` (`safety_acknowledged_by`);

--
-- Indexes for table `prescription_drugs`
--
ALTER TABLE `prescription_drugs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_drugs_prescription_id_index` (`prescription_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_items_visit_id_foreign` (`visit_id`),
  ADD KEY `prescription_items_drug_id_foreign` (`drug_id`);

--
-- Indexes for table `prescription_templates`
--
ALTER TABLE `prescription_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_templates_clinic_id_foreign` (`clinic_id`),
  ADD KEY `prescription_templates_created_by_foreign` (`created_by`);

--
-- Indexes for table `razorpay_webhook_events`
--
ALTER TABLE `razorpay_webhook_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `razorpay_webhook_events_event_id_unique` (`event_id`),
  ADD KEY `razorpay_webhook_events_payload_hash_index` (`payload_hash`),
  ADD KEY `razorpay_webhook_events_invoice_id_index` (`invoice_id`),
  ADD KEY `razorpay_webhook_events_razorpay_payment_id_index` (`razorpay_payment_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrals_patient_id_foreign` (`patient_id`),
  ADD KEY `referrals_visit_id_foreign` (`visit_id`),
  ADD KEY `referrals_from_doctor_id_foreign` (`from_doctor_id`),
  ADD KEY `referrals_clinic_id_status_index` (`clinic_id`,`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_clinic_id_index` (`clinic_id`),
  ADD KEY `rooms_ward_id_index` (`ward_id`),
  ADD KEY `rooms_clinic_id_is_active_index` (`clinic_id`,`is_active`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_clinic_id_role_index` (`clinic_id`,`role`),
  ADD KEY `users_phone_index` (`phone`);

--
-- Indexes for table `vendor_labs`
--
ALTER TABLE `vendor_labs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visits_patient_id_foreign` (`patient_id`),
  ADD KEY `visits_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `visits_doctor_id_created_at_index` (`doctor_id`,`created_at`),
  ADD KEY `visits_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `visits_appointment_id_foreign` (`appointment_id`);

--
-- Indexes for table `visit_lesions`
--
ALTER TABLE `visit_lesions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_lesions_visit_id_index` (`visit_id`);

--
-- Indexes for table `visit_procedures`
--
ALTER TABLE `visit_procedures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_procedures_clinic_id_foreign` (`clinic_id`),
  ADD KEY `visit_procedures_visit_id_index` (`visit_id`);

--
-- Indexes for table `visit_scales`
--
ALTER TABLE `visit_scales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_scales_visit_id_scale_name_unique` (`visit_id`,`scale_name`);

--
-- Indexes for table `walk_in_queue`
--
ALTER TABLE `walk_in_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `walk_in_queue_doctor_id_foreign` (`doctor_id`),
  ADD KEY `walk_in_queue_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `walk_in_queue_clinic_id_checked_in_at_index` (`clinic_id`,`checked_in_at`),
  ADD KEY `walk_in_queue_patient_id_index` (`patient_id`);

--
-- Indexes for table `wards`
--
ALTER TABLE `wards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wards_clinic_id_index` (`clinic_id`),
  ADD KEY `wards_clinic_id_ward_type_index` (`clinic_id`,`ward_type`);

--
-- Indexes for table `wearable_readings`
--
ALTER TABLE `wearable_readings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wearable_readings_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `wearable_readings_patient_id_recorded_at_index` (`patient_id`,`recorded_at`);

--
-- Indexes for table `whatsapp_messages`
--
ALTER TABLE `whatsapp_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `whatsapp_messages_wa_message_id_unique` (`wa_message_id`),
  ADD KEY `whatsapp_messages_clinic_id_patient_id_index` (`clinic_id`,`patient_id`),
  ADD KEY `whatsapp_messages_wa_message_id_index` (`wa_message_id`),
  ADD KEY `whatsapp_messages_trigger_type_related_id_index` (`trigger_type`,`related_id`),
  ADD KEY `whatsapp_messages_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `whatsapp_reminders`
--
ALTER TABLE `whatsapp_reminders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `whatsapp_reminders_clinic_id_type_unique` (`clinic_id`,`type`),
  ADD KEY `whatsapp_reminders_template_id_foreign` (`template_id`);

--
-- Indexes for table `whatsapp_scheduled`
--
ALTER TABLE `whatsapp_scheduled`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_scheduled_patient_id_foreign` (`patient_id`),
  ADD KEY `whatsapp_scheduled_appointment_id_foreign` (`appointment_id`),
  ADD KEY `whatsapp_scheduled_template_id_foreign` (`template_id`),
  ADD KEY `whatsapp_scheduled_clinic_id_status_scheduled_for_index` (`clinic_id`,`status`,`scheduled_for`);

--
-- Indexes for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `whatsapp_templates_clinic_id_type_index` (`clinic_id`,`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abdm_care_contexts`
--
ALTER TABLE `abdm_care_contexts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `abdm_consents`
--
ALTER TABLE `abdm_consents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `abdm_hiu_links`
--
ALTER TABLE `abdm_hiu_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_transcriptions`
--
ALTER TABLE `ai_transcriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `appointment_services`
--
ALTER TABLE `appointment_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `beds`
--
ALTER TABLE `beds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `clinic_equipment`
--
ALTER TABLE `clinic_equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinic_locations`
--
ALTER TABLE `clinic_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinic_rooms`
--
ALTER TABLE `clinic_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinic_subscriptions`
--
ALTER TABLE `clinic_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinic_tpa_configs`
--
ALTER TABLE `clinic_tpa_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_emr_templates`
--
ALTER TABLE `custom_emr_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_lab_orders`
--
ALTER TABLE `dental_lab_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_teeth`
--
ALTER TABLE `dental_teeth`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_tooth_history`
--
ALTER TABLE `dental_tooth_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drug_interactions`
--
ALTER TABLE `drug_interactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_encounters`
--
ALTER TABLE `emergency_encounters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_visits`
--
ALTER TABLE `emergency_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_beds`
--
ALTER TABLE `hospital_beds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `hospital_opd_tokens`
--
ALTER TABLE `hospital_opd_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_rooms`
--
ALTER TABLE `hospital_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `hospital_settings`
--
ALTER TABLE `hospital_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `hospital_wards`
--
ALTER TABLE `hospital_wards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `indian_drugs`
--
ALTER TABLE `indian_drugs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `insurance_claims`
--
ALTER TABLE `insurance_claims`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `insurance_preauths`
--
ALTER TABLE `insurance_preauths`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ipd_adt_audit_events`
--
ALTER TABLE `ipd_adt_audit_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ipd_bed_assignments`
--
ALTER TABLE `ipd_bed_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_care_plans`
--
ALTER TABLE `ipd_care_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_daily_charges`
--
ALTER TABLE `ipd_daily_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_handover_notes`
--
ALTER TABLE `ipd_handover_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_medication_administrations`
--
ALTER TABLE `ipd_medication_administrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_medication_orders`
--
ALTER TABLE `ipd_medication_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_progress_notes`
--
ALTER TABLE `ipd_progress_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_vitals`
--
ALTER TABLE `ipd_vitals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_departments`
--
ALTER TABLE `lab_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lab_orders`
--
ALTER TABLE `lab_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_order_items`
--
ALTER TABLE `lab_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lab_order_tests`
--
ALTER TABLE `lab_order_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_partner_users`
--
ALTER TABLE `lab_partner_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_samples`
--
ALTER TABLE `lab_samples`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lab_tests_catalog`
--
ALTER TABLE `lab_tests_catalog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lab_test_catalog`
--
ALTER TABLE `lab_test_catalog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_test_panels`
--
ALTER TABLE `lab_test_panels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `notification_queue`
--
ALTER TABLE `notification_queue`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ophthal_refractions`
--
ALTER TABLE `ophthal_refractions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ophthal_va_logs`
--
ALTER TABLE `ophthal_va_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patient_family_members`
--
ALTER TABLE `patient_family_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_photos`
--
ALTER TABLE `patient_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_categories`
--
ALTER TABLE `pharmacy_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_dispenses`
--
ALTER TABLE `pharmacy_dispenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_dispense_lines`
--
ALTER TABLE `pharmacy_dispense_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_dispensing`
--
ALTER TABLE `pharmacy_dispensing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pharmacy_dispensing_items`
--
ALTER TABLE `pharmacy_dispensing_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pharmacy_items`
--
ALTER TABLE `pharmacy_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pharmacy_purchases`
--
ALTER TABLE `pharmacy_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_purchase_items`
--
ALTER TABLE `pharmacy_purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_returns`
--
ALTER TABLE `pharmacy_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_return_lines`
--
ALTER TABLE `pharmacy_return_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_stock`
--
ALTER TABLE `pharmacy_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pharmacy_stock_batches`
--
ALTER TABLE `pharmacy_stock_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_suppliers`
--
ALTER TABLE `pharmacy_suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `photo_consents`
--
ALTER TABLE `photo_consents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `physio_hep`
--
ALTER TABLE `physio_hep`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `physio_treatment_plans`
--
ALTER TABLE `physio_treatment_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_drugs`
--
ALTER TABLE `prescription_drugs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_templates`
--
ALTER TABLE `prescription_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `razorpay_webhook_events`
--
ALTER TABLE `razorpay_webhook_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `vendor_labs`
--
ALTER TABLE `vendor_labs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `visit_lesions`
--
ALTER TABLE `visit_lesions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visit_procedures`
--
ALTER TABLE `visit_procedures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visit_scales`
--
ALTER TABLE `visit_scales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `walk_in_queue`
--
ALTER TABLE `walk_in_queue`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wards`
--
ALTER TABLE `wards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wearable_readings`
--
ALTER TABLE `wearable_readings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_messages`
--
ALTER TABLE `whatsapp_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_reminders`
--
ALTER TABLE `whatsapp_reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `whatsapp_scheduled`
--
ALTER TABLE `whatsapp_scheduled`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------

--
-- Structure for view `v_clinic_daily_summary`
--
DROP TABLE IF EXISTS `v_clinic_daily_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u618910819_clinicos`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_clinic_daily_summary`  AS SELECT `visits`.`clinic_id` AS `clinic_id`, cast(`visits`.`created_at` as date) AS `summary_date`, count(0) AS `total_visits`, count(distinct `visits`.`patient_id`) AS `unique_patients`, sum(case when `visits`.`status` = 'finalised' then 1 else 0 end) AS `completed_visits` FROM `visits` GROUP BY `visits`.`clinic_id`, cast(`visits`.`created_at` as date) ;

-- --------------------------------------------------------

--
-- Structure for view `v_today_appointments`
--
DROP TABLE IF EXISTS `v_today_appointments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u618910819_clinicos`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_today_appointments`  AS SELECT `a`.`id` AS `id`, `a`.`clinic_id` AS `clinic_id`, `a`.`scheduled_at` AS `scheduled_at`, `a`.`duration_mins` AS `duration_mins`, `a`.`status` AS `status`, `a`.`token_number` AS `token_number`, `a`.`specialty` AS `specialty`, `a`.`appointment_type` AS `appointment_type`, `p`.`name` AS `patient_name`, `p`.`phone` AS `patient_phone`, `p`.`abha_id` AS `abha_id`, `u`.`name` AS `doctor_name`, `s`.`name` AS `service_name`, `r`.`name` AS `room_name` FROM ((((`appointments` `a` join `patients` `p` on(`p`.`id` = `a`.`patient_id`)) join `users` `u` on(`u`.`id` = `a`.`doctor_id`)) left join `appointment_services` `s` on(`s`.`id` = `a`.`service_id`)) left join `clinic_rooms` `r` on(`r`.`id` = `a`.`room_id`)) WHERE cast(`a`.`scheduled_at` as date) = curdate() AND `a`.`deleted_at` is null AND `a`.`status` <> 'cancelled' ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abdm_care_contexts`
--
ALTER TABLE `abdm_care_contexts`
  ADD CONSTRAINT `abdm_care_contexts_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `abdm_care_contexts_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `abdm_care_contexts_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `abdm_consents`
--
ALTER TABLE `abdm_consents`
  ADD CONSTRAINT `abdm_consents_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `abdm_consents_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `abdm_hiu_links`
--
ALTER TABLE `abdm_hiu_links`
  ADD CONSTRAINT `abdm_hiu_links_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `abdm_hiu_links_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_transcriptions`
--
ALTER TABLE `ai_transcriptions`
  ADD CONSTRAINT `ai_transcriptions_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_transcriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ai_transcriptions_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_services`
--
ALTER TABLE `appointment_services`
  ADD CONSTRAINT `appointment_services_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `beds_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `beds_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `beds_ward_id_foreign` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_equipment`
--
ALTER TABLE `clinic_equipment`
  ADD CONSTRAINT `clinic_equipment_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_equipment_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `clinic_rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `clinic_locations`
--
ALTER TABLE `clinic_locations`
  ADD CONSTRAINT `clinic_locations_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_rooms`
--
ALTER TABLE `clinic_rooms`
  ADD CONSTRAINT `clinic_rooms_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_tpa_configs`
--
ALTER TABLE `clinic_tpa_configs`
  ADD CONSTRAINT `clinic_tpa_configs_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_vendor_links`
--
ALTER TABLE `clinic_vendor_links`
  ADD CONSTRAINT `clinic_vendor_links_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_vendor_links_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_labs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_emr_templates`
--
ALTER TABLE `custom_emr_templates`
  ADD CONSTRAINT `custom_emr_templates_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `custom_emr_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `dental_lab_orders`
--
ALTER TABLE `dental_lab_orders`
  ADD CONSTRAINT `dental_lab_orders_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dental_lab_orders_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dental_lab_orders_lab_id_foreign` FOREIGN KEY (`lab_id`) REFERENCES `vendor_labs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dental_lab_orders_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `dental_teeth`
--
ALTER TABLE `dental_teeth`
  ADD CONSTRAINT `dental_teeth_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dental_teeth_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dental_tooth_history`
--
ALTER TABLE `dental_tooth_history`
  ADD CONSTRAINT `dental_tooth_history_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `dental_tooth_history_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`);

--
-- Constraints for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `doctor_availability_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_availability_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drug_interactions`
--
ALTER TABLE `drug_interactions`
  ADD CONSTRAINT `drug_interactions_drug_a_id_foreign` FOREIGN KEY (`drug_a_id`) REFERENCES `indian_drugs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drug_interactions_drug_b_id_foreign` FOREIGN KEY (`drug_b_id`) REFERENCES `indian_drugs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_encounters`
--
ALTER TABLE `emergency_encounters`
  ADD CONSTRAINT `emergency_encounters_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_encounters_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `emergency_encounters_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `emergency_encounters_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `emergency_visits`
--
ALTER TABLE `emergency_visits`
  ADD CONSTRAINT `emergency_visits_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_visits_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `emergency_visits_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `emergency_visits_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `hospital_beds`
--
ALTER TABLE `hospital_beds`
  ADD CONSTRAINT `hospital_beds_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_beds_current_admission_id_foreign` FOREIGN KEY (`current_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_beds_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `hospital_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_opd_tokens`
--
ALTER TABLE `hospital_opd_tokens`
  ADD CONSTRAINT `hospital_opd_tokens_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_opd_tokens_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_opd_tokens_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_opd_tokens_registered_by_foreign` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hospital_rooms`
--
ALTER TABLE `hospital_rooms`
  ADD CONSTRAINT `hospital_rooms_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_rooms_ward_id_foreign` FOREIGN KEY (`ward_id`) REFERENCES `hospital_wards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_wards`
--
ALTER TABLE `hospital_wards`
  ADD CONSTRAINT `hospital_wards_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `insurance_claims`
--
ALTER TABLE `insurance_claims`
  ADD CONSTRAINT `insurance_claims_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `insurance_claims_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `insurance_claims_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `insurance_preauths`
--
ALTER TABLE `insurance_preauths`
  ADD CONSTRAINT `insurance_preauths_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `insurance_preauths_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `insurance_preauths_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_admissions`
--
ALTER TABLE `ipd_admissions`
  ADD CONSTRAINT `ipd_admissions_attending_doctor_id_foreign` FOREIGN KEY (`attending_doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_admissions_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_admissions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_adt_audit_events`
--
ALTER TABLE `ipd_adt_audit_events`
  ADD CONSTRAINT `ipd_adt_audit_events_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_adt_audit_events_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_adt_audit_events_hospital_bed_id_foreign` FOREIGN KEY (`hospital_bed_id`) REFERENCES `hospital_beds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_adt_audit_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ipd_bed_assignments`
--
ALTER TABLE `ipd_bed_assignments`
  ADD CONSTRAINT `ipd_bed_assignments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_bed_assignments_hospital_bed_id_foreign` FOREIGN KEY (`hospital_bed_id`) REFERENCES `hospital_beds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_bed_assignments_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_care_plans`
--
ALTER TABLE `ipd_care_plans`
  ADD CONSTRAINT `ipd_care_plans_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_care_plans_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_care_plans_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `ipd_daily_charges`
--
ALTER TABLE `ipd_daily_charges`
  ADD CONSTRAINT `ipd_daily_charges_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_daily_charges_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_daily_charges_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_daily_charges_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_handover_notes`
--
ALTER TABLE `ipd_handover_notes`
  ADD CONSTRAINT `ipd_handover_notes_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_handover_notes_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_handover_notes_handed_over_by_foreign` FOREIGN KEY (`handed_over_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ipd_handover_notes_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ipd_medication_administrations`
--
ALTER TABLE `ipd_medication_administrations`
  ADD CONSTRAINT `ipd_medication_administrations_ipd_medication_order_id_foreign` FOREIGN KEY (`ipd_medication_order_id`) REFERENCES `ipd_medication_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_medication_administrations_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ipd_medication_orders`
--
ALTER TABLE `ipd_medication_orders`
  ADD CONSTRAINT `ipd_medication_orders_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_medication_orders_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_medication_orders_prescribed_by_foreign` FOREIGN KEY (`prescribed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ipd_progress_notes`
--
ALTER TABLE `ipd_progress_notes`
  ADD CONSTRAINT `ipd_progress_notes_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_progress_notes_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_progress_notes_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_vitals`
--
ALTER TABLE `ipd_vitals`
  ADD CONSTRAINT `ipd_vitals_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_vitals_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_vitals_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_departments`
--
ALTER TABLE `lab_departments`
  ADD CONSTRAINT `lab_departments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD CONSTRAINT `lab_orders_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_orders_collected_by_foreign` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_orders_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lab_orders_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_orders_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `lab_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_labs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_orders_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_order_items`
--
ALTER TABLE `lab_order_items`
  ADD CONSTRAINT `lab_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `lab_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_order_items_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `lab_tests_catalog` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_order_tests`
--
ALTER TABLE `lab_order_tests`
  ADD CONSTRAINT `lab_order_tests_lab_order_id_foreign` FOREIGN KEY (`lab_order_id`) REFERENCES `lab_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `lab_results_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `lab_order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_sample_id_foreign` FOREIGN KEY (`sample_id`) REFERENCES `lab_samples` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_results_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `lab_tests_catalog` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_samples`
--
ALTER TABLE `lab_samples`
  ADD CONSTRAINT `lab_samples_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_samples_collected_by_foreign` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_samples_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `lab_order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_samples_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `lab_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_samples_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_tests_catalog`
--
ALTER TABLE `lab_tests_catalog`
  ADD CONSTRAINT `lab_tests_catalog_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_tests_catalog_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `lab_departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_test_catalog`
--
ALTER TABLE `lab_test_catalog`
  ADD CONSTRAINT `lab_test_catalog_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_labs` (`id`);

--
-- Constraints for table `lab_test_panels`
--
ALTER TABLE `lab_test_panels`
  ADD CONSTRAINT `lab_test_panels_panel_id_foreign` FOREIGN KEY (`panel_id`) REFERENCES `lab_tests_catalog` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_test_panels_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `lab_tests_catalog` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_queue`
--
ALTER TABLE `notification_queue`
  ADD CONSTRAINT `notification_queue_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ophthal_refractions`
--
ALTER TABLE `ophthal_refractions`
  ADD CONSTRAINT `ophthal_refractions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `ophthal_refractions_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ophthal_va_logs`
--
ALTER TABLE `ophthal_va_logs`
  ADD CONSTRAINT `ophthal_va_logs_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `ophthal_va_logs_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_family_members`
--
ALTER TABLE `patient_family_members`
  ADD CONSTRAINT `patient_family_members_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_family_members_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_photos`
--
ALTER TABLE `patient_photos`
  ADD CONSTRAINT `patient_photos_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_photos_consent_id_foreign` FOREIGN KEY (`consent_id`) REFERENCES `photo_consents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_photos_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `patient_photos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `payments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `pharmacy_categories`
--
ALTER TABLE `pharmacy_categories`
  ADD CONSTRAINT `pharmacy_categories_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `pharmacy_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_dispenses`
--
ALTER TABLE `pharmacy_dispenses`
  ADD CONSTRAINT `pharmacy_dispenses_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispenses_ipd_admission_id_foreign` FOREIGN KEY (`ipd_admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_dispenses_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispenses_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_dispense_lines`
--
ALTER TABLE `pharmacy_dispense_lines`
  ADD CONSTRAINT `pharmacy_dispense_lines_pharmacy_dispense_id_foreign` FOREIGN KEY (`pharmacy_dispense_id`) REFERENCES `pharmacy_dispenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispense_lines_pharmacy_item_id_foreign` FOREIGN KEY (`pharmacy_item_id`) REFERENCES `pharmacy_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispense_lines_pharmacy_stock_batch_id_foreign` FOREIGN KEY (`pharmacy_stock_batch_id`) REFERENCES `pharmacy_stock_batches` (`id`);

--
-- Constraints for table `pharmacy_dispensing`
--
ALTER TABLE `pharmacy_dispensing`
  ADD CONSTRAINT `pharmacy_dispensing_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `ipd_admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_dispensing_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispensing_dispensed_by_foreign` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pharmacy_dispensing_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_dispensing_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_dispensing_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_dispensing_items`
--
ALTER TABLE `pharmacy_dispensing_items`
  ADD CONSTRAINT `pharmacy_dispensing_items_dispensing_id_foreign` FOREIGN KEY (`dispensing_id`) REFERENCES `pharmacy_dispensing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_dispensing_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `pharmacy_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_items`
--
ALTER TABLE `pharmacy_items`
  ADD CONSTRAINT `pharmacy_items_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_purchases`
--
ALTER TABLE `pharmacy_purchases`
  ADD CONSTRAINT `pharmacy_purchases_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_purchases_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pharmacy_purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `pharmacy_suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_purchase_items`
--
ALTER TABLE `pharmacy_purchase_items`
  ADD CONSTRAINT `pharmacy_purchase_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `pharmacy_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `pharmacy_purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_returns`
--
ALTER TABLE `pharmacy_returns`
  ADD CONSTRAINT `pharmacy_returns_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_returns_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_returns_pharmacy_dispense_id_foreign` FOREIGN KEY (`pharmacy_dispense_id`) REFERENCES `pharmacy_dispenses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_returns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_return_lines`
--
ALTER TABLE `pharmacy_return_lines`
  ADD CONSTRAINT `pharmacy_return_lines_pharmacy_return_id_foreign` FOREIGN KEY (`pharmacy_return_id`) REFERENCES `pharmacy_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_return_lines_pharmacy_stock_batch_id_foreign` FOREIGN KEY (`pharmacy_stock_batch_id`) REFERENCES `pharmacy_stock_batches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_stock`
--
ALTER TABLE `pharmacy_stock`
  ADD CONSTRAINT `pharmacy_stock_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_stock_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `pharmacy_purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pharmacy_stock_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `pharmacy_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_stock_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `pharmacy_suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pharmacy_stock_batches`
--
ALTER TABLE `pharmacy_stock_batches`
  ADD CONSTRAINT `pharmacy_stock_batches_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_stock_batches_pharmacy_item_id_foreign` FOREIGN KEY (`pharmacy_item_id`) REFERENCES `pharmacy_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_suppliers`
--
ALTER TABLE `pharmacy_suppliers`
  ADD CONSTRAINT `pharmacy_suppliers_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `photo_consents`
--
ALTER TABLE `photo_consents`
  ADD CONSTRAINT `photo_consents_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `photo_consents_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `photo_consents_witnessed_by_foreign` FOREIGN KEY (`witnessed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `physio_hep`
--
ALTER TABLE `physio_hep`
  ADD CONSTRAINT `physio_hep_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `physio_hep_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `physio_hep_treatment_plan_id_foreign` FOREIGN KEY (`treatment_plan_id`) REFERENCES `physio_treatment_plans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `physio_hep_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `physio_treatment_plans`
--
ALTER TABLE `physio_treatment_plans`
  ADD CONSTRAINT `physio_treatment_plans_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `physio_treatment_plans_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `physio_treatment_plans_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `physio_treatment_plans_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prescriptions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `prescriptions_safety_acknowledged_by_foreign` FOREIGN KEY (`safety_acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prescriptions_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`);

--
-- Constraints for table `prescription_drugs`
--
ALTER TABLE `prescription_drugs`
  ADD CONSTRAINT `prescription_drugs_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `prescription_items_drug_id_foreign` FOREIGN KEY (`drug_id`) REFERENCES `indian_drugs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prescription_items_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_templates`
--
ALTER TABLE `prescription_templates`
  ADD CONSTRAINT `prescription_templates_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referrals_from_doctor_id_foreign` FOREIGN KEY (`from_doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `referrals_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referrals_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_ward_id_foreign` FOREIGN KEY (`ward_id`) REFERENCES `wards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visits_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visits_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `visits_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `visit_lesions`
--
ALTER TABLE `visit_lesions`
  ADD CONSTRAINT `visit_lesions_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visit_procedures`
--
ALTER TABLE `visit_procedures`
  ADD CONSTRAINT `visit_procedures_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_procedures_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visit_scales`
--
ALTER TABLE `visit_scales`
  ADD CONSTRAINT `visit_scales_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `walk_in_queue`
--
ALTER TABLE `walk_in_queue`
  ADD CONSTRAINT `walk_in_queue_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `walk_in_queue_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `walk_in_queue_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wards`
--
ALTER TABLE `wards`
  ADD CONSTRAINT `wards_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wearable_readings`
--
ALTER TABLE `wearable_readings`
  ADD CONSTRAINT `wearable_readings_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wearable_readings_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_messages`
--
ALTER TABLE `whatsapp_messages`
  ADD CONSTRAINT `whatsapp_messages_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_messages_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `whatsapp_reminders`
--
ALTER TABLE `whatsapp_reminders`
  ADD CONSTRAINT `whatsapp_reminders_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_reminders_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `whatsapp_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `whatsapp_scheduled`
--
ALTER TABLE `whatsapp_scheduled`
  ADD CONSTRAINT `whatsapp_scheduled_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `whatsapp_scheduled_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_scheduled_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_scheduled_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `whatsapp_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  ADD CONSTRAINT `whatsapp_templates_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
