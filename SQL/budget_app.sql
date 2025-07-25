-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 09:39 AM
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
-- Database: `budget_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('large','small','custom') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `remaining_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `user_id`, `name`, `type`, `total_amount`, `remaining_amount`, `created_at`, `updated_at`) VALUES
(22, 10, 'skibidi', 'large', 0.00, 0.00, '2025-07-25 01:44:57', '2025-07-25 01:58:50'),
(24, 11, 'test', 'large', 9000.00, 8766.00, '2025-07-25 03:49:30', '2025-07-25 03:49:47'),
(25, 11, 'Teh Hong Kai', 'small', 320.00, 297.00, '2025-07-25 03:51:55', '2025-07-25 03:59:46'),
(26, 11, '12345', 'large', 9000.00, 9000.00, '2025-07-25 04:06:54', '2025-07-25 04:06:54');

-- --------------------------------------------------------

--
-- Table structure for table `budget_items`
--

CREATE TABLE `budget_items` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `necessity` enum('high','medium','low') NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `aim` varchar(255) DEFAULT NULL,
  `time_range` varchar(255) DEFAULT NULL,
  `savings_percentage` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_items`
--

INSERT INTO `budget_items` (`id`, `budget_id`, `name`, `amount`, `necessity`, `start_date`, `end_date`, `duration_days`, `aim`, `time_range`, `savings_percentage`) VALUES
(93, 24, 'Project Materials', 5000.00, 'medium', '2025-07-25', '2025-08-01', 7, NULL, NULL, NULL),
(94, 24, 'Labor Costs', 2000.00, 'high', NULL, NULL, NULL, NULL, NULL, NULL),
(95, 24, 'Equipment Rental', 1500.00, 'medium', NULL, NULL, NULL, NULL, NULL, NULL),
(96, 24, 'Miscellaneous', 500.00, 'low', NULL, NULL, NULL, NULL, NULL, NULL),
(97, 25, 'Groceries', 150.00, 'high', NULL, NULL, NULL, NULL, '9am-11am', NULL),
(98, 25, 'Dining Out', 80.00, 'medium', NULL, NULL, NULL, NULL, NULL, NULL),
(99, 25, 'Entertainment', 50.00, 'low', NULL, NULL, NULL, NULL, NULL, NULL),
(100, 25, 'Transportation', 40.00, 'high', NULL, NULL, NULL, NULL, NULL, NULL),
(101, 26, 'Project Materials', 5000.00, 'medium', '2025-07-25', '2025-08-01', 7, NULL, NULL, NULL),
(102, 26, 'Labor Costs', 2000.00, 'high', NULL, NULL, NULL, NULL, NULL, NULL),
(103, 26, 'Equipment Rental', 1500.00, 'medium', NULL, NULL, NULL, NULL, NULL, NULL),
(104, 26, 'Miscellaneous', 500.00, 'low', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `spending_records`
--

CREATE TABLE `spending_records` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `necessity` enum('high','medium','low') NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spending_records`
--

INSERT INTO `spending_records` (`id`, `budget_id`, `item_id`, `name`, `amount`, `necessity`, `recorded_at`, `notes`) VALUES
(1, 22, NULL, 'Oung Ze Shen', 1234.00, 'high', '2025-07-25 01:45:23', 'skibidi toilet'),
(2, 24, 93, 'Teh Hong Kai', 234.00, 'medium', '2025-07-25 03:49:47', 'not cool'),
(3, 25, 97, 'sdcsfvd', 23.00, 'medium', '2025-07-25 03:59:46', 'sdsfdgbfd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`);

--
-- Indexes for table `spending_records`
--
ALTER TABLE `spending_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `budget_items`
--
ALTER TABLE `budget_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `spending_records`
--
ALTER TABLE `spending_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD CONSTRAINT `budget_items_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `spending_records`
--
ALTER TABLE `spending_records`
  ADD CONSTRAINT `spending_records_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `spending_records_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `budget_items` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
