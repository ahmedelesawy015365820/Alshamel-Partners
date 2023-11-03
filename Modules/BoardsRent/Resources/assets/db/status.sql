-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 02, 2023 at 08:52 AM
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
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` bigint UNSIGNED NOT NULL,
  `name` longtext NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `name`, `color`, `icon`, `created_at`, `updated_at`) VALUES
(1, '{\"ar\": \"Pending\", \"en\": \"Pending\"}', '#3498DB', 'fa-cog', NULL, NULL),
(2, '{\"ar\": \"Waiting for Approval\", \"en\": \"Waiting for Approval\"}', '#6610F2', 'fa-thumbs-up', NULL, NULL),
(3, '{\"ar\": \"In-Progress\", \"en\": \"In-Progress\"}', '#FFC107', 'fa-shopping-cart', NULL, NULL),
(4, '{\"ar\": \"Completed\", \"en\": \"Completed\"}', '#28A745', 'fa-tag', NULL, NULL),
(5, '{\"ar\": \"Canceled\", \"en\": \"Canceled\"}', '#DC3545', 'fa-cloud-download-alt', NULL, NULL),
(6, '{\"en\":\"Rejected\",\"ar\":\"\\u0645\\u0631\\u0641\\u0648\\u0636\"}', '#E91818', 'fa-star', '2022-03-15 03:11:24', '2022-03-15 03:11:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
