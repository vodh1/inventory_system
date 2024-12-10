-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 06:04 PM
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
-- Database: `system`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `borrower_username` varchar(255) NOT NULL,
  `borrow_date` date NOT NULL,
  `return_date` date NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','active','returned','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`id`, `unit_id`, `borrower_username`, `borrow_date`, `return_date`, `purpose`, `status`, `created_at`, `updated_at`) VALUES
(95, 1234, 'andre', '2024-12-11', '2024-12-10', 'gimme ples', 'returned', '2024-12-10 11:03:21', '2024-12-10 12:51:56');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Electronics'),
(2, 'Furniture'),
(3, 'Tools');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`) VALUES
(1, 'Computer Science'),
(3, 'Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `max_borrow_days` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `category_id`, `name`, `description`, `max_borrow_days`, `image_path`, `created_at`, `updated_at`) VALUES
(29, 2, 'Printer', '123', 12, '../uploads/equipment/default_image_equipment.png', '2024-12-09 18:07:47', '2024-12-10 10:58:12'),
(30, 2, 'asd', '123', 12, '../uploads/equipment/default_image_equipment.png', '2024-12-09 18:08:54', '2024-12-10 10:58:12'),
(31, 1, 'test', 'test', 12, '../uploads/equipment/default_image_equipment.png', '2024-12-09 18:10:23', '2024-12-10 10:57:15'),
(32, 3, 'hammer', 'This is hammer', 12, '../uploads/equipment/default_image_equipment.png', '2024-12-10 07:22:09', '2024-12-10 10:58:12'),
(33, 3, 'screwdriver', 'this is scredriver', 12, '../uploads/equipment/default_image_equipment.png', '2024-12-10 07:22:22', '2024-12-10 10:58:12'),
(34, 1, 'projector', 'This is projector', 12, '../uploads/equipment/default_image_equipment.png', '2024-12-10 07:22:39', '2024-12-10 10:58:12'),
(35, 3, 'Ballpen', 'This is Ballpen', 7, '../uploads/equipment/default_image_equipment.png', '2024-12-10 10:15:28', '2024-12-10 10:58:12');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_units`
--

CREATE TABLE `equipment_units` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `unit_code` varchar(255) NOT NULL,
  `status` enum('available','borrowed','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_units`
--

INSERT INTO `equipment_units` (`id`, `equipment_id`, `unit_code`, `status`, `created_at`) VALUES
(969, 29, 'UNIT-029-001', 'available', '2024-12-09 18:07:47'),
(970, 29, 'UNIT-029-002', 'available', '2024-12-09 18:07:47'),
(971, 29, 'UNIT-029-003', 'available', '2024-12-09 18:07:47'),
(972, 29, 'UNIT-029-004', 'available', '2024-12-09 18:07:47'),
(973, 29, 'UNIT-029-005', 'available', '2024-12-09 18:07:47'),
(974, 29, 'UNIT-029-006', 'available', '2024-12-09 18:07:47'),
(975, 29, 'UNIT-029-007', 'available', '2024-12-09 18:07:47'),
(976, 29, 'UNIT-029-008', 'available', '2024-12-09 18:07:47'),
(977, 29, 'UNIT-029-009', 'available', '2024-12-09 18:07:47'),
(978, 29, 'UNIT-029-010', 'available', '2024-12-09 18:07:47'),
(979, 29, 'UNIT-029-011', 'available', '2024-12-09 18:07:47'),
(980, 29, 'UNIT-029-012', 'available', '2024-12-09 18:07:47'),
(981, 29, 'UNIT-029-013', 'available', '2024-12-09 18:07:47'),
(982, 29, 'UNIT-029-014', 'available', '2024-12-09 18:07:47'),
(983, 29, 'UNIT-029-015', 'available', '2024-12-09 18:07:47'),
(984, 29, 'UNIT-029-016', 'available', '2024-12-09 18:07:47'),
(985, 29, 'UNIT-029-017', 'available', '2024-12-09 18:07:47'),
(986, 29, 'UNIT-029-018', 'available', '2024-12-09 18:07:47'),
(987, 29, 'UNIT-029-019', 'available', '2024-12-09 18:07:47'),
(988, 29, 'UNIT-029-020', 'available', '2024-12-09 18:07:47'),
(989, 29, 'UNIT-029-021', 'available', '2024-12-09 18:07:47'),
(990, 29, 'UNIT-029-022', 'available', '2024-12-09 18:07:47'),
(991, 29, 'UNIT-029-023', 'available', '2024-12-09 18:07:47'),
(992, 29, 'UNIT-029-024', 'available', '2024-12-09 18:07:47'),
(993, 29, 'UNIT-029-025', 'available', '2024-12-09 18:07:47'),
(994, 29, 'UNIT-029-026', 'available', '2024-12-09 18:07:47'),
(995, 29, 'UNIT-029-027', 'available', '2024-12-09 18:07:47'),
(996, 29, 'UNIT-029-028', 'available', '2024-12-09 18:07:47'),
(997, 29, 'UNIT-029-029', 'available', '2024-12-09 18:07:47'),
(998, 29, 'UNIT-029-030', 'available', '2024-12-09 18:07:47'),
(999, 29, 'UNIT-029-031', 'available', '2024-12-09 18:07:47'),
(1000, 29, 'UNIT-029-032', 'available', '2024-12-09 18:07:47'),
(1001, 30, 'UNIT-030-001', 'available', '2024-12-09 18:08:54'),
(1002, 30, 'UNIT-030-002', 'available', '2024-12-09 18:08:54'),
(1003, 30, 'UNIT-030-003', 'available', '2024-12-09 18:08:54'),
(1004, 30, 'UNIT-030-004', 'available', '2024-12-09 18:08:54'),
(1005, 30, 'UNIT-030-005', 'available', '2024-12-09 18:08:54'),
(1006, 30, 'UNIT-030-006', 'available', '2024-12-09 18:08:54'),
(1007, 30, 'UNIT-030-007', 'available', '2024-12-09 18:08:54'),
(1008, 30, 'UNIT-030-008', 'available', '2024-12-09 18:08:54'),
(1009, 30, 'UNIT-030-009', 'available', '2024-12-09 18:08:54'),
(1010, 30, 'UNIT-030-010', 'available', '2024-12-09 18:08:54'),
(1011, 30, 'UNIT-030-011', 'available', '2024-12-09 18:08:54'),
(1012, 30, 'UNIT-030-012', 'available', '2024-12-09 18:08:54'),
(1013, 30, 'UNIT-030-013', 'available', '2024-12-09 18:08:54'),
(1014, 30, 'UNIT-030-014', 'available', '2024-12-09 18:08:54'),
(1015, 30, 'UNIT-030-015', 'available', '2024-12-09 18:08:54'),
(1016, 30, 'UNIT-030-016', 'available', '2024-12-09 18:08:54'),
(1017, 30, 'UNIT-030-017', 'available', '2024-12-09 18:08:54'),
(1018, 30, 'UNIT-030-018', 'available', '2024-12-09 18:08:54'),
(1019, 30, 'UNIT-030-019', 'available', '2024-12-09 18:08:54'),
(1020, 30, 'UNIT-030-020', 'available', '2024-12-09 18:08:54'),
(1021, 30, 'UNIT-030-021', 'available', '2024-12-09 18:08:54'),
(1022, 30, 'UNIT-030-022', 'available', '2024-12-09 18:08:54'),
(1023, 30, 'UNIT-030-023', 'available', '2024-12-09 18:08:54'),
(1024, 30, 'UNIT-030-024', 'available', '2024-12-09 18:08:54'),
(1025, 30, 'UNIT-030-025', 'available', '2024-12-09 18:08:54'),
(1026, 30, 'UNIT-030-026', 'available', '2024-12-09 18:08:54'),
(1027, 30, 'UNIT-030-027', 'available', '2024-12-09 18:08:54'),
(1028, 30, 'UNIT-030-028', 'available', '2024-12-09 18:08:54'),
(1029, 30, 'UNIT-030-029', 'available', '2024-12-09 18:08:54'),
(1030, 30, 'UNIT-030-030', 'available', '2024-12-09 18:08:54'),
(1031, 30, 'UNIT-030-031', 'available', '2024-12-09 18:08:54'),
(1032, 30, 'UNIT-030-032', 'available', '2024-12-09 18:08:54'),
(1033, 31, 'UNIT-031-001', 'available', '2024-12-09 18:10:23'),
(1034, 31, 'UNIT-031-002', 'available', '2024-12-09 18:10:23'),
(1035, 31, 'UNIT-031-003', 'available', '2024-12-09 18:10:23'),
(1036, 31, 'UNIT-031-004', 'available', '2024-12-09 18:10:23'),
(1037, 31, 'UNIT-031-005', 'available', '2024-12-09 18:10:23'),
(1038, 31, 'UNIT-031-006', 'available', '2024-12-09 18:10:23'),
(1039, 31, 'UNIT-031-007', 'available', '2024-12-09 18:10:23'),
(1040, 31, 'UNIT-031-008', 'available', '2024-12-09 18:10:23'),
(1041, 31, 'UNIT-031-009', 'available', '2024-12-09 18:10:23'),
(1042, 31, 'UNIT-031-010', 'available', '2024-12-09 18:10:23'),
(1043, 31, 'UNIT-031-011', 'available', '2024-12-09 18:10:23'),
(1044, 31, 'UNIT-031-012', 'available', '2024-12-09 18:10:23'),
(1045, 32, 'UNIT-032-001', 'available', '2024-12-10 07:22:09'),
(1046, 32, 'UNIT-032-002', 'available', '2024-12-10 07:22:09'),
(1047, 32, 'UNIT-032-003', 'available', '2024-12-10 07:22:09'),
(1048, 32, 'UNIT-032-004', 'available', '2024-12-10 07:22:09'),
(1049, 32, 'UNIT-032-005', 'available', '2024-12-10 07:22:09'),
(1050, 32, 'UNIT-032-006', 'available', '2024-12-10 07:22:09'),
(1051, 32, 'UNIT-032-007', 'available', '2024-12-10 07:22:09'),
(1052, 32, 'UNIT-032-008', 'available', '2024-12-10 07:22:09'),
(1053, 32, 'UNIT-032-009', 'available', '2024-12-10 07:22:09'),
(1054, 32, 'UNIT-032-010', 'available', '2024-12-10 07:22:09'),
(1055, 32, 'UNIT-032-011', 'available', '2024-12-10 07:22:09'),
(1056, 32, 'UNIT-032-012', 'available', '2024-12-10 07:22:09'),
(1057, 32, 'UNIT-032-013', 'available', '2024-12-10 07:22:09'),
(1058, 32, 'UNIT-032-014', 'available', '2024-12-10 07:22:09'),
(1059, 32, 'UNIT-032-015', 'available', '2024-12-10 07:22:09'),
(1060, 32, 'UNIT-032-016', 'available', '2024-12-10 07:22:09'),
(1061, 32, 'UNIT-032-017', 'available', '2024-12-10 07:22:09'),
(1062, 32, 'UNIT-032-018', 'available', '2024-12-10 07:22:09'),
(1063, 32, 'UNIT-032-019', 'available', '2024-12-10 07:22:09'),
(1064, 32, 'UNIT-032-020', 'available', '2024-12-10 07:22:09'),
(1065, 32, 'UNIT-032-021', 'available', '2024-12-10 07:22:09'),
(1066, 32, 'UNIT-032-022', 'available', '2024-12-10 07:22:09'),
(1067, 32, 'UNIT-032-023', 'available', '2024-12-10 07:22:09'),
(1068, 32, 'UNIT-032-024', 'available', '2024-12-10 07:22:09'),
(1069, 32, 'UNIT-032-025', 'available', '2024-12-10 07:22:09'),
(1070, 32, 'UNIT-032-026', 'available', '2024-12-10 07:22:09'),
(1071, 32, 'UNIT-032-027', 'available', '2024-12-10 07:22:09'),
(1072, 32, 'UNIT-032-028', 'available', '2024-12-10 07:22:09'),
(1073, 32, 'UNIT-032-029', 'available', '2024-12-10 07:22:09'),
(1074, 32, 'UNIT-032-030', 'available', '2024-12-10 07:22:09'),
(1075, 32, 'UNIT-032-031', 'available', '2024-12-10 07:22:09'),
(1076, 32, 'UNIT-032-032', 'available', '2024-12-10 07:22:09'),
(1077, 32, 'UNIT-032-033', 'available', '2024-12-10 07:22:09'),
(1078, 32, 'UNIT-032-034', 'available', '2024-12-10 07:22:09'),
(1079, 32, 'UNIT-032-035', 'available', '2024-12-10 07:22:09'),
(1080, 32, 'UNIT-032-036', 'available', '2024-12-10 07:22:09'),
(1081, 32, 'UNIT-032-037', 'available', '2024-12-10 07:22:09'),
(1082, 32, 'UNIT-032-038', 'available', '2024-12-10 07:22:09'),
(1083, 32, 'UNIT-032-039', 'available', '2024-12-10 07:22:09'),
(1084, 32, 'UNIT-032-040', 'available', '2024-12-10 07:22:09'),
(1085, 32, 'UNIT-032-041', 'available', '2024-12-10 07:22:09'),
(1086, 32, 'UNIT-032-042', 'available', '2024-12-10 07:22:09'),
(1087, 32, 'UNIT-032-043', 'available', '2024-12-10 07:22:09'),
(1088, 32, 'UNIT-032-044', 'available', '2024-12-10 07:22:09'),
(1089, 32, 'UNIT-032-045', 'available', '2024-12-10 07:22:09'),
(1090, 32, 'UNIT-032-046', 'available', '2024-12-10 07:22:09'),
(1091, 32, 'UNIT-032-047', 'available', '2024-12-10 07:22:09'),
(1092, 32, 'UNIT-032-048', 'available', '2024-12-10 07:22:09'),
(1093, 32, 'UNIT-032-049', 'available', '2024-12-10 07:22:09'),
(1094, 32, 'UNIT-032-050', 'available', '2024-12-10 07:22:09'),
(1095, 32, 'UNIT-032-051', 'available', '2024-12-10 07:22:09'),
(1096, 32, 'UNIT-032-052', 'available', '2024-12-10 07:22:09'),
(1097, 32, 'UNIT-032-053', 'available', '2024-12-10 07:22:09'),
(1098, 32, 'UNIT-032-054', 'available', '2024-12-10 07:22:09'),
(1099, 32, 'UNIT-032-055', 'available', '2024-12-10 07:22:09'),
(1100, 32, 'UNIT-032-056', 'available', '2024-12-10 07:22:09'),
(1101, 32, 'UNIT-032-057', 'available', '2024-12-10 07:22:09'),
(1102, 32, 'UNIT-032-058', 'available', '2024-12-10 07:22:09'),
(1103, 32, 'UNIT-032-059', 'available', '2024-12-10 07:22:09'),
(1104, 32, 'UNIT-032-060', 'available', '2024-12-10 07:22:09'),
(1105, 32, 'UNIT-032-061', 'available', '2024-12-10 07:22:09'),
(1106, 32, 'UNIT-032-062', 'available', '2024-12-10 07:22:09'),
(1107, 32, 'UNIT-032-063', 'available', '2024-12-10 07:22:09'),
(1108, 32, 'UNIT-032-064', 'available', '2024-12-10 07:22:09'),
(1109, 32, 'UNIT-032-065', 'available', '2024-12-10 07:22:09'),
(1110, 32, 'UNIT-032-066', 'available', '2024-12-10 07:22:09'),
(1111, 32, 'UNIT-032-067', 'available', '2024-12-10 07:22:09'),
(1112, 32, 'UNIT-032-068', 'available', '2024-12-10 07:22:09'),
(1113, 32, 'UNIT-032-069', 'available', '2024-12-10 07:22:09'),
(1114, 32, 'UNIT-032-070', 'available', '2024-12-10 07:22:09'),
(1115, 32, 'UNIT-032-071', 'available', '2024-12-10 07:22:09'),
(1116, 32, 'UNIT-032-072', 'available', '2024-12-10 07:22:09'),
(1117, 32, 'UNIT-032-073', 'available', '2024-12-10 07:22:09'),
(1118, 32, 'UNIT-032-074', 'available', '2024-12-10 07:22:09'),
(1119, 32, 'UNIT-032-075', 'available', '2024-12-10 07:22:09'),
(1120, 32, 'UNIT-032-076', 'available', '2024-12-10 07:22:09'),
(1121, 32, 'UNIT-032-077', 'available', '2024-12-10 07:22:09'),
(1122, 32, 'UNIT-032-078', 'available', '2024-12-10 07:22:09'),
(1123, 32, 'UNIT-032-079', 'available', '2024-12-10 07:22:09'),
(1124, 32, 'UNIT-032-080', 'available', '2024-12-10 07:22:09'),
(1125, 32, 'UNIT-032-081', 'available', '2024-12-10 07:22:09'),
(1126, 32, 'UNIT-032-082', 'available', '2024-12-10 07:22:09'),
(1127, 32, 'UNIT-032-083', 'available', '2024-12-10 07:22:09'),
(1128, 32, 'UNIT-032-084', 'available', '2024-12-10 07:22:09'),
(1129, 32, 'UNIT-032-085', 'available', '2024-12-10 07:22:09'),
(1130, 32, 'UNIT-032-086', 'available', '2024-12-10 07:22:09'),
(1131, 32, 'UNIT-032-087', 'available', '2024-12-10 07:22:09'),
(1132, 32, 'UNIT-032-088', 'available', '2024-12-10 07:22:09'),
(1133, 32, 'UNIT-032-089', 'available', '2024-12-10 07:22:09'),
(1134, 32, 'UNIT-032-090', 'available', '2024-12-10 07:22:09'),
(1135, 32, 'UNIT-032-091', 'available', '2024-12-10 07:22:09'),
(1136, 32, 'UNIT-032-092', 'available', '2024-12-10 07:22:09'),
(1137, 32, 'UNIT-032-093', 'available', '2024-12-10 07:22:09'),
(1138, 32, 'UNIT-032-094', 'available', '2024-12-10 07:22:09'),
(1139, 32, 'UNIT-032-095', 'available', '2024-12-10 07:22:09'),
(1140, 32, 'UNIT-032-096', 'available', '2024-12-10 07:22:09'),
(1141, 32, 'UNIT-032-097', 'available', '2024-12-10 07:22:09'),
(1142, 32, 'UNIT-032-098', 'available', '2024-12-10 07:22:09'),
(1143, 32, 'UNIT-032-099', 'available', '2024-12-10 07:22:09'),
(1144, 32, 'UNIT-032-100', 'available', '2024-12-10 07:22:09'),
(1145, 32, 'UNIT-032-101', 'available', '2024-12-10 07:22:09'),
(1146, 32, 'UNIT-032-102', 'available', '2024-12-10 07:22:09'),
(1147, 32, 'UNIT-032-103', 'available', '2024-12-10 07:22:09'),
(1148, 32, 'UNIT-032-104', 'available', '2024-12-10 07:22:09'),
(1149, 32, 'UNIT-032-105', 'available', '2024-12-10 07:22:09'),
(1150, 32, 'UNIT-032-106', 'available', '2024-12-10 07:22:09'),
(1151, 32, 'UNIT-032-107', 'available', '2024-12-10 07:22:09'),
(1152, 32, 'UNIT-032-108', 'available', '2024-12-10 07:22:09'),
(1153, 32, 'UNIT-032-109', 'available', '2024-12-10 07:22:09'),
(1154, 32, 'UNIT-032-110', 'available', '2024-12-10 07:22:09'),
(1155, 32, 'UNIT-032-111', 'available', '2024-12-10 07:22:09'),
(1156, 32, 'UNIT-032-112', 'available', '2024-12-10 07:22:09'),
(1157, 32, 'UNIT-032-113', 'available', '2024-12-10 07:22:09'),
(1158, 32, 'UNIT-032-114', 'available', '2024-12-10 07:22:09'),
(1159, 32, 'UNIT-032-115', 'available', '2024-12-10 07:22:09'),
(1160, 32, 'UNIT-032-116', 'available', '2024-12-10 07:22:09'),
(1161, 32, 'UNIT-032-117', 'available', '2024-12-10 07:22:09'),
(1162, 32, 'UNIT-032-118', 'available', '2024-12-10 07:22:09'),
(1163, 32, 'UNIT-032-119', 'available', '2024-12-10 07:22:09'),
(1164, 32, 'UNIT-032-120', 'available', '2024-12-10 07:22:09'),
(1165, 32, 'UNIT-032-121', 'available', '2024-12-10 07:22:09'),
(1166, 32, 'UNIT-032-122', 'available', '2024-12-10 07:22:09'),
(1167, 32, 'UNIT-032-123', 'available', '2024-12-10 07:22:09'),
(1168, 33, 'UNIT-033-001', 'available', '2024-12-10 07:22:22'),
(1169, 33, 'UNIT-033-002', 'available', '2024-12-10 07:22:22'),
(1170, 33, 'UNIT-033-003', 'available', '2024-12-10 07:22:22'),
(1171, 33, 'UNIT-033-004', 'available', '2024-12-10 07:22:22'),
(1172, 33, 'UNIT-033-005', 'available', '2024-12-10 07:22:22'),
(1173, 33, 'UNIT-033-006', 'available', '2024-12-10 07:22:22'),
(1174, 33, 'UNIT-033-007', 'available', '2024-12-10 07:22:22'),
(1175, 33, 'UNIT-033-008', 'available', '2024-12-10 07:22:22'),
(1176, 33, 'UNIT-033-009', 'available', '2024-12-10 07:22:22'),
(1177, 33, 'UNIT-033-010', 'available', '2024-12-10 07:22:22'),
(1178, 33, 'UNIT-033-011', 'available', '2024-12-10 07:22:22'),
(1179, 33, 'UNIT-033-012', 'available', '2024-12-10 07:22:22'),
(1180, 33, 'UNIT-033-013', 'available', '2024-12-10 07:22:22'),
(1181, 33, 'UNIT-033-014', 'available', '2024-12-10 07:22:22'),
(1182, 33, 'UNIT-033-015', 'available', '2024-12-10 07:22:22'),
(1183, 33, 'UNIT-033-016', 'available', '2024-12-10 07:22:22'),
(1184, 33, 'UNIT-033-017', 'available', '2024-12-10 07:22:22'),
(1185, 33, 'UNIT-033-018', 'available', '2024-12-10 07:22:22'),
(1186, 33, 'UNIT-033-019', 'available', '2024-12-10 07:22:22'),
(1187, 33, 'UNIT-033-020', 'available', '2024-12-10 07:22:22'),
(1188, 33, 'UNIT-033-021', 'available', '2024-12-10 07:22:22'),
(1189, 33, 'UNIT-033-022', 'available', '2024-12-10 07:22:22'),
(1190, 33, 'UNIT-033-023', 'available', '2024-12-10 07:22:22'),
(1191, 33, 'UNIT-033-024', 'available', '2024-12-10 07:22:22'),
(1192, 33, 'UNIT-033-025', 'available', '2024-12-10 07:22:22'),
(1193, 33, 'UNIT-033-026', 'available', '2024-12-10 07:22:22'),
(1194, 33, 'UNIT-033-027', 'available', '2024-12-10 07:22:22'),
(1195, 33, 'UNIT-033-028', 'available', '2024-12-10 07:22:22'),
(1196, 33, 'UNIT-033-029', 'available', '2024-12-10 07:22:22'),
(1197, 33, 'UNIT-033-030', 'available', '2024-12-10 07:22:22'),
(1198, 33, 'UNIT-033-031', 'available', '2024-12-10 07:22:22'),
(1199, 33, 'UNIT-033-032', 'available', '2024-12-10 07:22:22'),
(1200, 34, 'UNIT-034-001', 'available', '2024-12-10 07:22:39'),
(1201, 34, 'UNIT-034-002', 'available', '2024-12-10 07:22:39'),
(1202, 34, 'UNIT-034-003', 'available', '2024-12-10 07:22:39'),
(1203, 34, 'UNIT-034-004', 'available', '2024-12-10 07:22:39'),
(1204, 34, 'UNIT-034-005', 'available', '2024-12-10 07:22:39'),
(1205, 34, 'UNIT-034-006', 'available', '2024-12-10 07:22:39'),
(1206, 34, 'UNIT-034-007', 'available', '2024-12-10 07:22:39'),
(1207, 34, 'UNIT-034-008', 'available', '2024-12-10 07:22:39'),
(1208, 34, 'UNIT-034-009', 'available', '2024-12-10 07:22:39'),
(1209, 34, 'UNIT-034-010', 'available', '2024-12-10 07:22:39'),
(1210, 34, 'UNIT-034-011', 'available', '2024-12-10 07:22:39'),
(1211, 34, 'UNIT-034-012', 'available', '2024-12-10 07:22:39'),
(1212, 34, 'UNIT-034-013', 'available', '2024-12-10 07:22:39'),
(1213, 34, 'UNIT-034-014', 'available', '2024-12-10 07:22:39'),
(1214, 34, 'UNIT-034-015', 'available', '2024-12-10 07:22:39'),
(1215, 34, 'UNIT-034-016', 'available', '2024-12-10 07:22:39'),
(1216, 34, 'UNIT-034-017', 'available', '2024-12-10 07:22:39'),
(1217, 34, 'UNIT-034-018', 'available', '2024-12-10 07:22:39'),
(1218, 34, 'UNIT-034-019', 'available', '2024-12-10 07:22:39'),
(1219, 34, 'UNIT-034-020', 'available', '2024-12-10 07:22:39'),
(1220, 34, 'UNIT-034-021', 'available', '2024-12-10 07:22:39'),
(1221, 34, 'UNIT-034-022', 'available', '2024-12-10 07:22:39'),
(1222, 34, 'UNIT-034-023', 'available', '2024-12-10 07:22:39'),
(1223, 34, 'UNIT-034-024', 'available', '2024-12-10 07:22:39'),
(1224, 34, 'UNIT-034-025', 'available', '2024-12-10 07:22:39'),
(1225, 34, 'UNIT-034-026', 'available', '2024-12-10 07:22:39'),
(1226, 34, 'UNIT-034-027', 'available', '2024-12-10 07:22:39'),
(1227, 34, 'UNIT-034-028', 'available', '2024-12-10 07:22:39'),
(1228, 34, 'UNIT-034-029', 'available', '2024-12-10 07:22:39'),
(1229, 34, 'UNIT-034-030', 'available', '2024-12-10 07:22:39'),
(1230, 34, 'UNIT-034-031', 'available', '2024-12-10 07:22:39'),
(1231, 34, 'UNIT-034-032', 'available', '2024-12-10 07:22:39'),
(1233, 35, 'UNIT-035-001', 'available', '2024-12-10 10:15:28'),
(1234, 35, 'UNIT-035-002', 'available', '2024-12-10 10:15:28'),
(1235, 35, 'UNIT-035-003', 'available', '2024-12-10 10:15:28');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Administrator'),
(2, 'Staff'),
(3, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `department_id`, `role_id`, `username`, `password`, `email`, `first_name`, `middle_name`, `last_name`, `age`, `address`, `contact_number`, `profile_image`, `created_at`, `updated_at`) VALUES
(11, 1, 1, 'andre', '1234', 'andrelee.cuyugan@gmail.com', 'Andre Lee', 'Rodriguez', 'Cuyugan', 22, 'Tetuan', '09009088', '', '2024-11-08 08:28:45', '2024-12-10 12:51:24'),
(12, 1, 3, 'Arman', 'asdzxc1!', 'yes@gmail.com', 'Arman', 'R', 'Cuyugan', 21, 'Tetuan', '09123456789', 'uploads/profile_images/6758077a22984.jpg', '2024-12-10 09:10:59', '2024-12-10 09:18:50'),
(13, 1, 2, 'tester', 'qweASD123!@#', 'xt202000397@wmsu.edu.ph', 'Axeeeee', 'R', 'Ligma', 32, 'gdx', '09123456789', '../assets/default-profile.png', '2024-12-10 09:14:56', '2024-12-10 09:14:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrower_name` (`borrower_username`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `equipment_units`
--
ALTER TABLE `equipment_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD KEY `fk_department_id` (`department_id`),
  ADD KEY `fk_role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `equipment_units`
--
ALTER TABLE `equipment_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1333;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`borrower_username`) REFERENCES `users` (`username`),
  ADD CONSTRAINT `borrowings_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `equipment_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `equipment_units`
--
ALTER TABLE `equipment_units`
  ADD CONSTRAINT `equipment_units_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_department_id` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
