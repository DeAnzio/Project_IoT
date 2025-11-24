-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 07:20 PM
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
-- Database: `iot_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(5) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `airsys`
--

CREATE TABLE `airsys` (
  `id` bigint(20) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `sensor_type` varchar(50) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `raw_value` text DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airsys`
--

INSERT INTO `airsys` (`id`, `device_id`, `sensor_type`, `value`, `raw_value`, `recorded_at`) VALUES
(1, 'esp32-unit-001', 'DHT11_temp', 25.8, 'h=65.00', '2025-11-11 14:31:10'),
(2, 'esp32-unit-001', 'DHT11_temp', 26.7, 'h=70.00', '2025-11-11 14:31:25'),
(3, 'esp32-unit-001', 'DHT11_temp', 27.1, 'h=66.00', '2025-11-11 14:31:40'),
(4, 'esp32-unit-001', 'DHT11_temp', 27.1, 'h=59.00', '2025-11-11 14:31:55'),
(5, 'esp32-unit-001', 'DHT11_temp', 27.1, 'h=55.00', '2025-11-11 14:32:10'),
(6, 'esp32-unit-001', 'DHT11_temp', 26.7, 'h=54.00', '2025-11-11 14:32:25'),
(7, 'esp32-unit-001', 'DHT11_temp', 26.7, 'h=53.00', '2025-11-11 14:32:40'),
(8, 'esp32-unit-001', 'DHT11_temp', 26.2, 'h=53.00', '2025-11-11 14:32:55'),
(9, 'esp32-unit-001', 'DHT11_temp', 25.8, 'h=53.00', '2025-11-11 14:33:10'),
(10, 'esp32-unit-001', 'DHT11_temp', 25.8, 'h=53.00', '2025-11-11 14:33:25'),
(11, 'esp32-unit-001', 'DHT11_temp', 25.8, 'h=53.00', '2025-11-11 14:34:02'),
(12, 'esp32-unit-001', 'DHT11_temp', 25.3, 'h=54.00', '2025-11-11 14:34:17'),
(13, 'esp32-unit-001', 'DHT11_temp', 25.3, 'h=54.00', '2025-11-11 14:34:32'),
(14, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:34:47'),
(15, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:35:02'),
(16, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:35:17'),
(17, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:35:32'),
(18, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:35:47'),
(19, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:36:02'),
(20, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:36:17'),
(21, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:36:32'),
(22, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:36:47'),
(23, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:37:02'),
(24, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:37:17'),
(25, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:37:32'),
(26, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:37:47'),
(27, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:38:02'),
(28, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:38:17'),
(29, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:38:32'),
(30, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:38:47'),
(31, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:39:02'),
(32, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:39:17'),
(33, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:39:32'),
(34, 'esp32-unit-001', 'DHT11_temp', 24.4, 'h=55.00', '2025-11-11 14:39:47'),
(35, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:40:02'),
(36, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:40:17'),
(37, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:40:32'),
(38, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:40:47'),
(39, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:41:02'),
(40, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:41:17'),
(41, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:41:32'),
(42, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:41:47'),
(43, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:42:02'),
(44, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:42:17'),
(45, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:42:32'),
(46, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:42:47'),
(47, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:43:02'),
(48, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=55.00', '2025-11-11 14:43:17'),
(49, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:43:32'),
(50, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:43:47'),
(51, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:44:02'),
(52, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:44:17'),
(53, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:44:32'),
(54, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:44:47'),
(55, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:45:02'),
(56, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:45:17'),
(57, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:45:32'),
(58, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:45:47'),
(59, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:46:02'),
(60, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:46:17'),
(61, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:46:32'),
(62, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:46:47'),
(63, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:47:02'),
(64, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:47:17'),
(65, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:47:32'),
(66, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:47:47'),
(67, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:48:02'),
(68, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:48:17'),
(69, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:48:32'),
(70, 'esp32-unit-001', 'DHT11_temp', 24.5, 'h=54.00', '2025-11-11 14:48:47'),
(71, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:49:02'),
(72, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:49:17'),
(73, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:49:32'),
(74, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:49:47'),
(75, 'esp32-unit-001', 'DHT11_temp', 24.8, 'h=54.00', '2025-11-11 14:50:02');

-- --------------------------------------------------------

--
-- Table structure for table `commands`
--

CREATE TABLE `commands` (
  `id` bigint(20) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `command` varchar(100) NOT NULL,
  `payload` text DEFAULT NULL,
  `status` enum('pending','executed','cancelled') DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `executed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commands`
--

INSERT INTO `commands` (`id`, `device_id`, `command`, `payload`, `status`, `created_at`, `executed_at`) VALUES
(1, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 13:52:49', '2025-11-11 14:06:54'),
(2, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 13:52:50', '2025-11-11 14:06:56'),
(3, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:08:31', '2025-11-11 14:08:31'),
(4, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:08:32', '2025-11-11 14:08:34'),
(5, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:08:36', '2025-11-11 14:08:38'),
(6, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:14:34', '2025-11-11 14:14:34'),
(7, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:14:35', '2025-11-11 14:14:37'),
(8, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:14:36', '2025-11-11 14:14:38'),
(9, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:14:37', '2025-11-11 14:14:41'),
(10, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:14:38', '2025-11-11 14:14:43'),
(11, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:14:39', '2025-11-11 14:14:45'),
(12, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:14:39', '2025-11-11 14:14:47'),
(13, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:14:41', '2025-11-11 14:14:49'),
(14, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:37', '2025-11-11 14:15:38'),
(15, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:39', '2025-11-11 14:15:41'),
(16, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:40', '2025-11-11 14:15:43'),
(17, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:41', '2025-11-11 14:15:44'),
(18, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:15:42', '2025-11-11 14:15:46'),
(19, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:43', '2025-11-11 14:15:49'),
(20, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:43', '2025-11-11 14:15:51'),
(21, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:43', '2025-11-11 14:15:53'),
(22, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:44', '2025-11-11 14:15:54'),
(23, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:44', '2025-11-11 14:15:56'),
(24, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:44', '2025-11-11 14:15:58'),
(25, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:45', '2025-11-11 14:16:00'),
(26, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:45', '2025-11-11 14:16:02'),
(27, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:45', '2025-11-11 14:16:05'),
(28, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:46', '2025-11-11 14:16:07'),
(29, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:46', '2025-11-11 14:16:08'),
(30, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:46', '2025-11-11 14:16:11'),
(31, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:15:47', '2025-11-11 14:16:13'),
(32, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:31:26', '2025-11-11 14:31:28'),
(33, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:27', '2025-11-11 14:31:30'),
(34, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:28', '2025-11-11 14:31:32'),
(35, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:29', '2025-11-11 14:31:34'),
(36, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:29', '2025-11-11 14:31:36'),
(37, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:31:31', '2025-11-11 14:31:38'),
(38, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:32', '2025-11-11 14:31:40'),
(39, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:48', '2025-11-11 14:31:50'),
(40, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:31:52', '2025-11-11 14:31:54'),
(41, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:34:43', '2025-11-11 14:34:43'),
(42, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:34:44', '2025-11-11 14:34:45'),
(43, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:34:47', '2025-11-11 14:34:47'),
(44, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:34:49', '2025-11-11 14:34:49'),
(45, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:34:50', '2025-11-11 14:34:51'),
(46, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:35:00', '2025-11-11 14:35:01'),
(47, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:35:35', '2025-11-11 14:35:37'),
(48, 'esp32-unit-001', 'lampu_on', 'lampu_on_ok', 'executed', '2025-11-11 14:39:02', '2025-11-11 14:39:03'),
(49, 'esp32-unit-001', 'lampu_off', 'lampu_off_ok', 'executed', '2025-11-11 14:39:04', '2025-11-11 14:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_id`, `description`, `last_seen`) VALUES
(1, 'esp32-unit-001', NULL, '2025-11-11 14:50:02');

-- --------------------------------------------------------

--
-- Table structure for table `firesys`
--

CREATE TABLE `firesys` (
  `id` bigint(20) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `sensor_type` varchar(50) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `raw_value` text DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secusys`
--

CREATE TABLE `secusys` (
  `id` bigint(20) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `sensor_type` varchar(50) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `raw_value` text DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `airsys`
--
ALTER TABLE `airsys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_id` (`device_id`);

--
-- Indexes for table `firesys`
--
ALTER TABLE `firesys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `secusys`
--
ALTER TABLE `secusys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `airsys`
--
ALTER TABLE `airsys`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `commands`
--
ALTER TABLE `commands`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `firesys`
--
ALTER TABLE `firesys`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `secusys`
--
ALTER TABLE `secusys`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `airsys`
--
ALTER TABLE `airsys`
  ADD CONSTRAINT `airsys_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`device_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
