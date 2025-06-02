-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 02, 2025 at 09:29 AM
-- Server version: 8.0.42-0ubuntu0.24.04.1
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hiernostine`
--
CREATE DATABASE IF NOT EXISTS `hiernostine` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `hiernostine`;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `preferences` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `preferences`, `created_at`, `updated_at`) VALUES
(1, 'Jude Mpoyo', 'mpoyojude0@gmail.com', '+243975889135', 'Av rashidi', '\"{\\\"sms_notifications\\\":\\\"1\\\"}\"', '2025-05-15 22:06:51', '2025-05-15 22:06:51'),
(2, 'Hiernostine', 'hieno@gmqil.com', '0999999999989', '', '\"{\\\"newsletter\\\":\\\"1\\\"}\"', '2025-05-17 18:45:02', '2025-05-17 18:45:02'),
(3, 'stone', 'stone@gmail.com', '+243975889135', 'Av rashidi', NULL, '2025-05-31 12:46:59', '2025-05-31 12:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `upload_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `order_id`, `file_name`, `file_path`, `file_type`, `file_size`, `upload_time`, `created_at`, `updated_at`) VALUES
(1, 2, 'code.png', 'uploads/orders/6829004a51d44_code.png', 'image/png', 119945, '2025-05-17 21:31:54', '2025-05-17 21:31:54', '2025-05-17 21:31:54'),
(2, 4, 'academy_software_foundation.png', 'uploads/orders/683afa5b4bacf_academy_software_foundation.png', 'image/png', 15636, '2025-05-31 12:47:23', '2025-05-31 12:47:23', '2025-05-31 12:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('paper','ink','plate','chemical','other') NOT NULL,
  `stock_quantity` decimal(10,2) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `min_stock_level` decimal(10,2) DEFAULT NULL,
  `cost_per_unit` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `type`, `stock_quantity`, `unit`, `min_stock_level`, `cost_per_unit`, `created_at`, `updated_at`) VALUES
(1, 'A4', 'paper', 1000.00, 'pc', 100.00, 100.00, '2025-05-15 22:08:01', '2025-05-15 22:08:01');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `reference` varchar(50) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('received','in_preparation','in_printing','in_finishing','ready_for_delivery','delivered','canceled') DEFAULT 'received',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `reference`, `delivery_date`, `priority`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'ORD-20250516-000722', '2025-05-23', 'medium', 'received', 'j', '2025-05-15 22:07:22', '2025-05-15 22:07:22'),
(2, 1, 'ORD-20250516-000844', '2025-06-04', 'urgent', 'in_finishing', 'mnmnmnmn', '2025-05-15 22:08:44', '2025-05-17 20:47:03'),
(3, 1, 'ORD-20250516-014451', '2025-06-05', 'medium', 'ready_for_delivery', '', '2025-05-15 23:44:51', '2025-05-17 20:44:05'),
(4, 2, 'ORD-20250518-102040', '2025-05-19', 'high', 'received', 'lorem', '2025-05-18 08:20:40', '2025-05-18 08:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_materials`
--

CREATE TABLE `order_materials` (
  `order_id` int NOT NULL,
  `material_id` int NOT NULL,
  `quantity_used` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_materials`
--

INSERT INTO `order_materials` (`order_id`, `material_id`, `quantity_used`, `created_at`, `updated_at`) VALUES
(4, 1, 10.00, '2025-05-18 08:20:40', '2025-05-18 08:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_steps`
--

CREATE TABLE `production_steps` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `step` enum('prepress','printing','finishing','quality_check','packaging','shipping') NOT NULL,
  `status` enum('pending','in_progress','completed','on_hold','failed') DEFAULT 'pending',
  `assigned_to` varchar(100) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `comments` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `production_steps`
--

INSERT INTO `production_steps` (`id`, `order_id`, `step`, `status`, `assigned_to`, `start_time`, `end_time`, `comments`, `created_at`, `updated_at`) VALUES
(1, 1, 'prepress', 'pending', NULL, NULL, NULL, NULL, '2025-05-15 22:07:22', '2025-05-15 22:07:22'),
(2, 1, 'printing', 'pending', NULL, NULL, NULL, NULL, '2025-05-15 22:07:22', '2025-05-15 22:07:22'),
(3, 1, 'finishing', 'pending', NULL, NULL, NULL, NULL, '2025-05-15 22:07:22', '2025-05-15 22:07:22'),
(4, 1, 'quality_check', 'pending', NULL, NULL, NULL, NULL, '2025-05-15 22:07:22', '2025-05-15 22:07:22'),
(5, 1, 'packaging', 'pending', NULL, NULL, NULL, NULL, '2025-05-15 22:07:22', '2025-05-15 22:07:22'),
(6, 2, 'prepress', 'pending', '', NULL, '2025-05-17 22:45:00', '', '2025-05-15 22:08:44', '2025-05-17 20:46:07'),
(7, 2, 'printing', 'completed', '', NULL, '2025-05-17 22:46:17', '', '2025-05-15 22:08:44', '2025-05-17 20:46:17'),
(8, 2, 'finishing', 'completed', '', NULL, '2025-05-17 22:45:00', '', '2025-05-15 22:08:44', '2025-05-17 20:47:03'),
(9, 2, 'quality_check', 'completed', '', '2025-05-17 22:47:00', '2025-05-17 22:45:00', '', '2025-05-15 22:08:44', '2025-05-17 20:47:44'),
(10, 2, 'packaging', 'completed', '', NULL, '2025-05-17 22:45:00', '', '2025-05-15 22:08:44', '2025-05-17 20:45:00'),
(11, 3, 'prepress', 'completed', '', NULL, '2025-05-17 22:44:04', '', '2025-05-15 23:44:51', '2025-05-17 20:44:05'),
(12, 3, 'printing', 'completed', '', '2025-05-17 22:14:00', '2025-05-17 22:20:00', 'dkdkd', '2025-05-15 23:44:51', '2025-05-17 20:15:44'),
(13, 3, 'finishing', 'completed', '', NULL, '2025-05-17 22:43:09', '', '2025-05-15 23:44:51', '2025-05-17 20:43:10'),
(14, 3, 'quality_check', 'completed', '', '2025-05-17 22:47:00', '2025-05-17 22:48:00', '', '2025-05-15 23:44:51', '2025-05-17 20:43:32'),
(15, 3, 'packaging', 'completed', '', '2025-05-17 22:24:00', '2025-05-17 13:25:00', 'jkk', '2025-05-15 23:44:51', '2025-05-17 20:42:56'),
(16, 4, 'prepress', 'pending', NULL, NULL, NULL, NULL, '2025-05-18 08:20:40', '2025-05-18 08:20:40'),
(17, 4, 'printing', 'pending', NULL, NULL, NULL, NULL, '2025-05-18 08:20:40', '2025-05-18 08:20:40'),
(18, 4, 'finishing', 'pending', NULL, NULL, NULL, NULL, '2025-05-18 08:20:40', '2025-05-18 08:20:40'),
(19, 4, 'quality_check', 'pending', NULL, NULL, NULL, NULL, '2025-05-18 08:20:40', '2025-05-18 08:20:40'),
(20, 4, 'packaging', 'pending', NULL, NULL, NULL, NULL, '2025-05-18 08:20:40', '2025-05-18 08:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `password` varchar(600) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Jude Mpoyo', 'mpoyojude0@gmail.com', 'admin', '', '$2y$10$bbTnyUD15WbVhI1Zjf1tWeYyzi80DbN1xQTcs0wIsTx9G4vuFiize', '2025-04-21 06:18:27', '2025-05-18 09:23:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_priority` (`priority`);

--
-- Indexes for table `order_materials`
--
ALTER TABLE `order_materials`
  ADD PRIMARY KEY (`order_id`,`material_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_steps`
--
ALTER TABLE `production_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_steps_order` (`order_id`),
  ADD KEY `idx_production_steps_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production_steps`
--
ALTER TABLE `production_steps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_materials`
--
ALTER TABLE `order_materials`
  ADD CONSTRAINT `order_materials_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_materials_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `production_steps`
--
ALTER TABLE `production_steps`
  ADD CONSTRAINT `production_steps_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
