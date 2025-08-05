-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 07:12 AM
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
-- Database: `attendancex`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_deletions`
--

CREATE TABLE `account_deletions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user` longtext DEFAULT NULL,
  `action_by` bigint(20) DEFAULT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `additional_salary_components`
--

CREATE TABLE `additional_salary_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL,
  `month_bs` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` enum('earning','deduction') NOT NULL DEFAULT 'earning',
  `remarks` text DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `checkin` time DEFAULT NULL,
  `checkout` time DEFAULT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `total_break` double DEFAULT NULL,
  `worked_hours` double DEFAULT NULL,
  `overtime_minute` double DEFAULT NULL,
  `short_minutes` double NOT NULL DEFAULT 0,
  `ip_address` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `attendance_by` varchar(255) DEFAULT NULL,
  `request_reason` longtext DEFAULT NULL,
  `late_checkin_reason` text DEFAULT NULL,
  `early_checkout_reason` text DEFAULT NULL,
  `location_log` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_requests`
--

CREATE TABLE `attendance_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `checkin` time DEFAULT NULL,
  `checkout` time DEFAULT NULL,
  `reason` longtext DEFAULT NULL,
  `action_reason` longtext DEFAULT NULL,
  `action_by` bigint(20) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_rules`
--

CREATE TABLE `attendance_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `swift_code` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `radius` double DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `radius`, `ip_address`, `latitude`, `longitude`, `description`, `order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Head Office', 100, NULL, '27.717135133600678', '85.31174508302566', NULL, NULL, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `branch_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `holidays` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `order`, `status`, `branch_id`, `created_at`, `updated_at`, `holidays`) VALUES
(1, 'General', NULL, NULL, 1, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_notices`
--

CREATE TABLE `department_notices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notice_id` bigint(20) DEFAULT NULL,
  `department_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_public_holidays`
--

CREATE TABLE `department_public_holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) DEFAULT NULL,
  `public_holiday_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_shifts`
--

CREATE TABLE `department_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) DEFAULT NULL,
  `shift_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_shifts`
--

INSERT INTO `department_shifts` (`id`, `department_id`, `shift_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `order` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `name`, `description`, `order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CEO', NULL, NULL, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(2, 'Managing Director', NULL, NULL, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(3, 'Sr. Accountant', NULL, NULL, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(4, 'Front Desk', NULL, NULL, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `employee_bank_details`
--

CREATE TABLE `employee_bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `swift_code` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `leavetype_id` bigint(20) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `no_of_days` double DEFAULT NULL,
  `reason` longtext DEFAULT NULL,
  `leave_taken` varchar(255) DEFAULT NULL,
  `action_by` bigint(20) DEFAULT NULL,
  `action_reason` longtext DEFAULT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_approvals`
--

CREATE TABLE `leave_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leave_id` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_notifications`
--

CREATE TABLE `leave_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leave_id` bigint(20) DEFAULT NULL,
  `notified_user_id` bigint(20) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `requires_advance_application` tinyint(1) NOT NULL DEFAULT 0,
  `min_days_before` int(11) DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `gender` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `short_name`, `duration`, `requires_advance_application`, `min_days_before`, `is_paid`, `gender`, `description`, `order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sick Leave', 'SL', '5', 0, NULL, 1, 'Both', NULL, NULL, 1, '2025-07-31 05:04:31', '2025-07-31 05:04:31');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_17_050749_create_branches_table', 1),
(5, '2025_01_20_142017_create_departments_table', 1),
(6, '2025_01_21_101113_create_shifts_table', 1),
(7, '2025_01_21_153006_create_personal_access_tokens_table', 1),
(8, '2025_01_22_103810_create_notices_table', 1),
(9, '2025_01_22_110741_create_department_notices_table', 1),
(10, '2025_01_22_140904_create_attendances_table', 1),
(11, '2025_01_27_112256_create_settings_table', 1),
(12, '2025_01_31_102249_create_attendance_requests_table', 1),
(13, '2025_02_03_103257_create_leave_types_table', 1),
(14, '2025_02_06_102351_create_leaves_table', 1),
(15, '2025_02_10_140801_add_holidays_to_departments', 1),
(16, '2025_02_12_161504_create_designations_table', 1),
(17, '2025_02_13_114804_create_leave_approvals_table', 1),
(18, '2025_02_13_151503_add_two_columns_to_users_table', 1),
(19, '2025_02_14_110623_add_otp_to_users_table', 1),
(20, '2025_02_14_110853_create_notifications_table', 1),
(21, '2025_02_17_142734_create_public_holidays_table', 1),
(22, '2025_02_17_143731_create_department_public_holidays_table', 1),
(23, '2025_03_03_101329_create_permission_tables', 1),
(24, '2025_03_03_112817_add_columns_to_permissions_table', 1),
(25, '2025_03_24_104253_create_account_deletions_table', 1),
(26, '2025_03_28_122903_add_location_preference_to_users_table', 1),
(27, '2025_03_31_103416_create_department_shifts_table', 1),
(28, '2025_05_08_112621_add_column_to_users_table', 1),
(29, '2025_05_12_105815_create_notice_seens_table', 1),
(30, '2025_05_13_141410_create_attendance_rules_table', 1),
(31, '2025_05_13_153829_add_two_columns_to_attendances_table', 1),
(32, '2025_05_15_160602_add_device_flexible_columns_to_users_table', 1),
(33, '2025_05_21_143817_add_request_preference_to_users_table', 1),
(34, '2025_05_26_102500_add_columns_to_attendance_requests_table', 1),
(35, '2025_05_29_104622_add_marital_status_to_users_table', 1),
(36, '2025_05_29_114329_create_banks_table', 1),
(37, '2025_05_29_114810_create_employee_bank_details_table', 1),
(38, '2025_05_29_122921_create_salary_settings_table', 1),
(39, '2025_05_29_123222_create_additional_salary_components_table', 1),
(40, '2025_05_29_123549_create_monthly_payrolls_table', 1),
(41, '2025_05_29_125542_create_payroll_payments_table', 1),
(42, '2025_06_09_152247_add_effective_date_columns_to_salary_settings_table', 1),
(43, '2025_06_11_102331_add_is_taxable_column_to_additional_salary_components_table', 1),
(44, '2025_06_11_111620_add_more_columns_to_monthly_payrolls_table', 1),
(45, '2025_06_12_101014_add_salary_settings_column_to_monthly_payrolls_table', 1),
(46, '2025_06_12_105301_change_column_of_payroll_payments_table', 1),
(47, '2025_06_19_101208_create_feedback_table', 1),
(48, '2025_06_19_103345_create_leave_notifications_table', 1),
(49, '2025_06_30_141747_add_short_minutes_column_to_attendances_table', 1),
(50, '2025_07_03_104238_add_columns_to_monthly_payrolls_table', 1),
(51, '2025_07_16_113523_add_advance_application_columns_to_leave_types_table', 1),
(52, '2025_07_25_135237_add_location_log_to_attendances_table', 1);

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

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_payrolls`
--

CREATE TABLE `monthly_payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_unique_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL,
  `month_bs` varchar(255) DEFAULT NULL,
  `total_expected_working_days` int(11) DEFAULT NULL,
  `total_days_in_month` int(11) DEFAULT NULL,
  `present_days` int(11) DEFAULT NULL,
  `paid_leaves` int(11) DEFAULT NULL,
  `unpaid_leaves` int(11) DEFAULT NULL,
  `absent_days` int(11) DEFAULT NULL,
  `public_holidays` int(11) DEFAULT NULL,
  `weekends` int(11) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT NULL,
  `allowances` decimal(10,2) DEFAULT NULL,
  `overtime` decimal(10,2) DEFAULT NULL,
  `overtime_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `undertime` decimal(10,2) NOT NULL DEFAULT 0.00,
  `undertime_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `workingtime_details` longtext DEFAULT NULL,
  `additional_earnings` decimal(10,2) DEFAULT NULL,
  `additional_deductions` decimal(10,2) DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT NULL,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `paid_by` bigint(20) DEFAULT NULL,
  `per_day_salary` decimal(10,2) DEFAULT NULL,
  `absence_deduction` decimal(10,2) DEFAULT NULL,
  `total_earnings` decimal(10,2) DEFAULT NULL,
  `taxable_salary` decimal(10,2) DEFAULT NULL,
  `attendance_deduction` decimal(10,2) DEFAULT NULL,
  `remaining_salary` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `salary_settings` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notice_seens`
--

CREATE TABLE `notice_seens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notice_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `seen_by` bigint(20) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_payments`
--

CREATE TABLE `payroll_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `monthly_payroll_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_date_bs` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `bank_detail_id` bigint(20) DEFAULT NULL,
  `paid_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `parent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `parent`, `created_at`, `updated_at`) VALUES
(1, 'view dashboard', 'web', 'Dashboard', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(2, 'view configuration', 'web', 'Configuration', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(3, 'view branch', 'web', 'Branch', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(4, 'create branch', 'web', 'Branch', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(5, 'edit branch', 'web', 'Branch', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(6, 'delete branch', 'web', 'Branch', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(7, 'view shift', 'web', 'Shift', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(8, 'create shift', 'web', 'Shift', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(9, 'edit shift', 'web', 'Shift', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(10, 'delete shift', 'web', 'Shift', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(11, 'view department', 'web', 'Department', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(12, 'create department', 'web', 'Department', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(13, 'edit department', 'web', 'Department', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(14, 'delete department', 'web', 'Department', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(15, 'view leavetype', 'web', 'Leavetype', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(16, 'create leavetype', 'web', 'Leavetype', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(17, 'edit leavetype', 'web', 'Leavetype', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(18, 'delete leavetype', 'web', 'Leavetype', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(19, 'view designation', 'web', 'Designation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(20, 'create designation', 'web', 'Designation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(21, 'edit designation', 'web', 'Designation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(22, 'delete designation', 'web', 'Designation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(23, 'view publicholiday', 'web', 'PublicHoliday', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(24, 'create publicholiday', 'web', 'PublicHoliday', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(25, 'edit publicholiday', 'web', 'PublicHoliday', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(26, 'delete publicholiday', 'web', 'PublicHoliday', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(27, 'view role', 'web', 'Role', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(28, 'create role', 'web', 'Role', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(29, 'edit role', 'web', 'Role', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(30, 'delete role', 'web', 'Role', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(31, 'view setting', 'web', 'Setting', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(32, 'update setting', 'web', 'Setting', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(33, 'change password', 'web', 'Setting', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(34, 'update calendar format', 'web', 'Setting', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(35, 'view employee', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(36, 'create employee', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(37, 'import excel', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(38, 'edit employee', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(39, 'reset device', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(40, 'app permissions', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(41, 'bank details', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(42, 'manage salary', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(43, 'delete employee', 'web', 'Employee', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(44, 'view appnotice', 'web', 'App_Notice', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(45, 'create appnotice', 'web', 'App_Notice', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(46, 'edit appnotice', 'web', 'App_Notice', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(47, 'delete appnotice', 'web', 'App_Notice', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(48, 'view attendancerequest', 'web', 'Attendance_Request', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(49, 'filter attendancerequest', 'web', 'Attendance_Request', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(50, 'manage attendancerequest', 'web', 'Attendance_Request', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(51, 'view accountdeletion', 'web', 'Account_Deletion', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(52, 'view notification', 'web', 'Notification', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(53, 'view feedback', 'web', 'Feedback', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(54, 'delete feedback', 'web', 'Feedback', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(55, 'view leave', 'web', 'Leave', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(56, 'view leaverequest', 'web', 'Leave_Request', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(57, 'filter leaverequest', 'web', 'Leave_Request', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(58, 'manage leaverequest', 'web', 'Leave_Request', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(59, 'view leavereport', 'web', 'Leave', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(60, 'view attendance', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(61, 'view allemployeesattendance', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(62, 'filter allemployeesattendance', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(63, 'view individualemployeeattendance', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(64, 'filter individualemployeeattendance', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(65, 'export attendancereport', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(66, 'view employeerules', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(67, 'reset employeerules', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(68, 'update employeerules', 'web', 'Attendance', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(69, 'view compensation', 'web', 'Compensation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(70, 'create compensation', 'web', 'Compensation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(71, 'edit compensation', 'web', 'Compensation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(72, 'delete compensation', 'web', 'Compensation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(73, 'filter compensation', 'web', 'Compensation', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(74, 'view individualpayrollreport', 'web', 'Payroll', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(75, 'view monthlypayrollreport', 'web', 'Payroll', '2025-07-31 05:04:31', '2025-07-31 05:04:31'),
(76, 'export excel', 'web', 'Payroll', '2025-07-31 05:04:31', '2025-07-31 05:04:31');

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
-- Table structure for table `public_holidays`
--

CREATE TABLE `public_holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `total_days` int(11) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'SUPER-ADMIN', 'web', '2025-07-31 05:04:32', '2025-07-31 05:04:32'),
(2, 'SENIOR-ADMIN', 'web', '2025-07-31 05:04:32', '2025-07-31 05:04:32'),
(3, 'ADMIN', 'web', '2025-07-31 05:04:32', '2025-07-31 05:04:32');

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
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1);

-- --------------------------------------------------------

--
-- Table structure for table `salary_settings`
--

CREATE TABLE `salary_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `overtime_rate` decimal(8,2) DEFAULT NULL,
  `is_epf_enrolled` tinyint(1) NOT NULL DEFAULT 1,
  `is_cit_enrolled` tinyint(1) NOT NULL DEFAULT 0,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `is_deduction_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date DEFAULT NULL,
  `effective_date_bs` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'company_name', '\"Sajilo Attendance\"', NULL, NULL),
(2, 'company_logo', NULL, NULL, NULL),
(3, 'app_logo', NULL, NULL, NULL),
(4, 'company_information', '\"Sajilo attendance is a lead company dedicated to providing services with excellence and innovation\"', NULL, NULL),
(5, 'phone', '\"9800000000\"', NULL, NULL),
(6, 'smtp_email', '\"info@sajiloattendance.com\"', NULL, NULL),
(7, 'email', '\"info@sajiloattendance.com\"', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `start_grace_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `end_grace_time` time DEFAULT NULL,
  `total_time` double DEFAULT NULL,
  `lunch_start` time DEFAULT NULL,
  `lunch_end` time DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `department_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `start_time`, `start_grace_time`, `end_time`, `end_grace_time`, `total_time`, `lunch_start`, `lunch_end`, `description`, `order`, `status`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'Regular Shift', '09:00:00', '09:15:00', '17:00:00', '16:45:00', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-07-31 05:04:31', '2025-07-31 05:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `pan_number` varchar(255) DEFAULT NULL,
  `pan_photo` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `resign_date` date DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `department_id` bigint(20) DEFAULT NULL,
  `shift_id` bigint(20) DEFAULT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'Employee',
  `expo_token` text DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `location_preference` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `request_management` tinyint(1) NOT NULL DEFAULT 0,
  `device_flexible` tinyint(1) NOT NULL DEFAULT 0,
  `device_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_ids`)),
  `allow_attendance_request` tinyint(1) NOT NULL DEFAULT 1,
  `allow_leave_request` tinyint(1) NOT NULL DEFAULT 1,
  `platform` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `employee_id`, `email`, `status`, `email_verified_at`, `password`, `phone`, `gender`, `marital_status`, `pan_number`, `pan_photo`, `image`, `date_of_birth`, `join_date`, `resign_date`, `designation`, `branch_id`, `department_id`, `shift_id`, `user_type`, `expo_token`, `otp`, `location_preference`, `order`, `remember_token`, `created_at`, `updated_at`, `request_management`, `device_flexible`, `device_ids`, `allow_attendance_request`, `allow_leave_request`, `platform`) VALUES
(1, 'Super', 'Admin', NULL, 'default@sajiloattendance.com', NULL, NULL, '$2y$12$FItL50Nmh7WFgBEqkHAbC.APlDp.RJywIhMYYTLjDgZDlWfrym3oC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Admin', NULL, NULL, 1, NULL, NULL, '2025-07-31 05:04:31', '2025-07-31 05:04:31', 0, 0, NULL, 1, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_deletions`
--
ALTER TABLE `account_deletions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `additional_salary_components`
--
ALTER TABLE `additional_salary_components`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_requests`
--
ALTER TABLE `attendance_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_rules`
--
ALTER TABLE `attendance_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_notices`
--
ALTER TABLE `department_notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_public_holidays`
--
ALTER TABLE `department_public_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_shifts`
--
ALTER TABLE `department_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_bank_details`
--
ALTER TABLE `employee_bank_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_approvals`
--
ALTER TABLE `leave_approvals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_notifications`
--
ALTER TABLE `leave_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `monthly_payrolls`
--
ALTER TABLE `monthly_payrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notice_seens`
--
ALTER TABLE `notice_seens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `public_holidays`
--
ALTER TABLE `public_holidays`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `salary_settings`
--
ALTER TABLE `salary_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_deletions`
--
ALTER TABLE `account_deletions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `additional_salary_components`
--
ALTER TABLE `additional_salary_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_requests`
--
ALTER TABLE `attendance_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_rules`
--
ALTER TABLE `attendance_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `department_notices`
--
ALTER TABLE `department_notices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_public_holidays`
--
ALTER TABLE `department_public_holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_shifts`
--
ALTER TABLE `department_shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_bank_details`
--
ALTER TABLE `employee_bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_approvals`
--
ALTER TABLE `leave_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_notifications`
--
ALTER TABLE `leave_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `monthly_payrolls`
--
ALTER TABLE `monthly_payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_seens`
--
ALTER TABLE `notice_seens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_holidays`
--
ALTER TABLE `public_holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `salary_settings`
--
ALTER TABLE `salary_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
