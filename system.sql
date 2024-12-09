-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2024 at 04:56 PM
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
  `borrower_name` varchar(255) NOT NULL,
  `borrow_date` date NOT NULL,
  `return_date` date NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','active','returned') DEFAULT 'pending',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `equipment_name` varchar(255) DEFAULT NULL,
  `unit_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`id`, `unit_id`, `borrower_name`, `borrow_date`, `return_date`, `purpose`, `status`, `approval_status`, `created_at`, `equipment_name`, `unit_code`) VALUES
(86, 97, 'kalvin', '2024-11-09', '2024-11-09', 'office\r\n', 'returned', 'approved', '2024-11-09 05:18:33', 'Printer', 'UNIT-018-001'),
(87, 97, 'kalvin', '2024-11-09', '2024-11-09', 'dhfhd', 'returned', 'approved', '2024-11-09 06:02:17', 'Printer', 'UNIT-018-001'),
(88, 107, 'kalvin', '2024-11-09', '2024-11-09', 'ddhg', 'returned', 'approved', '2024-11-09 06:17:48', 'Projector', 'UNIT-019-001'),
(89, 108, 'kalvin', '2024-11-09', '2024-11-09', 'dhdgh', 'returned', 'approved', '2024-11-09 06:21:10', 'Projector', 'UNIT-019-002'),
(90, 109, 'rham', '2024-11-09', '2024-11-09', 'rham', 'returned', 'approved', '2024-11-09 06:24:13', 'Projector', 'UNIT-019-003');

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
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `max_borrow_days` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `available_units` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `description`, `category_id`, `max_borrow_days`, `image_path`, `created_at`, `available_units`) VALUES
(18, 'Printer', 'Printer', 1, 3, 'uploads/equipment/672ef085b7299.jpg', '2024-11-09 05:17:57', 8),
(19, 'Projector', 'sbgdghsgh', 1, 3, 'uploads/equipment/672efbd955a1c.jpg', '2024-11-09 06:06:17', 2);

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
(97, 18, 'UNIT-018-001', 'available', '2024-11-09 05:17:57'),
(98, 18, 'UNIT-018-002', 'available', '2024-11-09 05:17:57'),
(99, 18, 'UNIT-018-003', 'available', '2024-11-09 05:17:57'),
(100, 18, 'UNIT-018-004', 'available', '2024-11-09 05:17:57'),
(101, 18, 'UNIT-018-005', 'available', '2024-11-09 05:17:57'),
(102, 18, 'UNIT-018-006', 'available', '2024-11-09 05:17:57'),
(103, 18, 'UNIT-018-007', 'available', '2024-11-09 05:17:57'),
(104, 18, 'UNIT-018-008', 'available', '2024-11-09 05:17:57'),
(105, 18, 'UNIT-018-009', 'available', '2024-11-09 05:17:57'),
(106, 18, 'UNIT-018-010', 'available', '2024-11-09 05:17:57'),
(107, 19, 'UNIT-019-001', 'available', '2024-11-09 06:06:17'),
(108, 19, 'UNIT-019-002', 'available', '2024-11-09 06:06:17'),
(109, 19, 'UNIT-019-003', 'available', '2024-11-09 06:06:17'),
(110, 19, 'UNIT-019-004', 'available', '2024-11-09 06:06:17'),
(111, 19, 'UNIT-019-005', 'available', '2024-11-09 06:06:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `department`, `role`, `email`, `contact`, `created_at`, `first_name`, `last_name`, `age`, `address`, `contact_number`, `middle_name`) VALUES
(8, 'vinn', '1234', 'dddd', 'admin', 'lakuping@gmail.com', NULL, '2024-11-08 08:28:45', 'fbbfb', 'dhdb', 22, 'dbdbjbd', '09009088', 'sghs'),
(9, 'kalvin', '12345678', 'Computer Science', 'user', 'kalvinlakuping33@gmail.com', NULL, '2024-11-08 15:24:03', 'kalvin', 'lakuping', 20, 'taluksangay', '09072656256', 'alain'),
(10, 'rham', '123', 'Information Technology', 'user', 'gsfdgfsgf@gmail.com', NULL, '2024-11-09 06:23:34', 'rhamir', 'Jaafar', 26, 'sgfgfs', '0923664536', 'balang');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrower_name` (`borrower_name`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `equipment_units`
--
ALTER TABLE `equipment_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `equipment_units`
--
ALTER TABLE `equipment_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`borrower_name`) REFERENCES `users` (`username`),
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
  ADD CONSTRAINT `equipment_units_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
