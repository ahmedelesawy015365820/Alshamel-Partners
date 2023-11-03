-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 02, 2023 at 08:40 AM
-- Server version: 8.0.32-0ubuntu0.22.10.2
-- PHP Version: 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `addeffect`
--

-- --------------------------------------------------------

--
-- Table structure for table `unitstatus`
--

CREATE TABLE `unitstatus` (
  `id` bigint UNSIGNED NOT NULL,
  `name` longtext NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `unitstatus`
--

INSERT INTO `unitstatus` (`id`, `name`, `color`, `created_at`, `updated_at`) VALUES
(1, '{\"ar\": \"Active\", \"en\": \"Active\"}', '#28A745', NULL, '2022-01-09 05:12:04'),
(2, '{\"ar\": \"Draft Saving\", \"en\": \"Draft Saving\"}', '#FFC107', NULL, '2022-01-09 05:12:20'),
(3, '{\"ar\": \"Out of Service\", \"en\": \"Out of Service\"}', '#DC3545', NULL, NULL),
(4, '{\"ar\": \"Booked\", \"en\": \"Booked\"}', '#3498DB', NULL, '2022-01-09 05:12:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `unitstatus`
--
ALTER TABLE `unitstatus`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `unitstatus`
--
ALTER TABLE `unitstatus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
