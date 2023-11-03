-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 04, 2023 at 08:08 AM
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
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `code`, `price`, `created_at`, `updated_at`) VALUES
(17, 'jarallah 1', 'J-1', 24000, '2022-09-17 14:43:03', '2022-09-17 14:43:03'),
(19, 'Hamra Clinic', 'H 1', 12000, '2022-09-17 16:20:43', '2022-09-17 16:20:43'),
(20, 'Best', 'Best H', 308.596, '2022-09-25 17:38:21', '2022-09-25 17:38:21'),
(21, 'Dazzle', 'Dazzle D', 12, '2022-09-25 17:43:08', '2022-09-25 17:43:08'),
(22, 'Asayad Medical Center', 'Essayed C', 24, '2022-09-25 17:45:21', '2022-09-26 12:24:57'),
(23, 'Attaba clinic', 'Attaba clinic', 16.331, '2022-09-25 17:54:08', '2022-09-25 17:54:08'),
(24, 'McDonalds', 'Mc H', 24, '2022-09-25 18:11:52', '2022-09-26 10:28:15'),
(25, 'Alloaloah Clinic', 'Alloaloah D', 13, '2022-09-25 18:27:22', '2022-09-25 18:38:51'),
(27, 'Hassan Optics', 'Hassan O', 111, '2022-09-25 18:31:51', '2022-09-25 18:35:52'),
(28, 'Best', 'Best F', 92.687, '2022-09-26 10:12:46', '2022-09-26 10:23:16'),
(29, 'Chevrolet', 'Chevrolet F', 30, '2022-09-26 10:26:20', '2022-09-26 10:51:38'),
(30, 'McDonalds', 'McDonalds F', 64, '2022-09-26 10:32:53', '2022-09-26 10:55:08'),
(31, 'shako Mako', 'shako 1 F', 18, '2022-09-26 10:47:11', '2022-09-26 10:50:37'),
(32, 'shako Mako', 'shako Mako2 F', 26, '2022-09-26 10:48:37', '2022-09-26 10:50:49'),
(33, 'Loqma_alafih Restaurant', 'Loqma 1 F', 9, '2022-09-26 10:59:41', '2022-09-26 10:59:41'),
(34, 'Loqma_alafih Restaurant', 'Loqma 2 F', 18, '2022-09-26 11:04:22', '2022-09-26 11:04:22'),
(35, 'Best', 'Best J', 123.583, '2022-09-26 11:20:28', '2022-09-26 11:20:28'),
(36, 'Asayad Medical Center', 'Essayed J 1', 15, '2022-09-26 11:28:58', '2022-09-26 12:24:35'),
(37, 'Essayed Clinic', 'Essayed J2', 30, '2022-09-26 11:31:13', '2022-09-26 11:31:13'),
(38, 'Al Rathaan Electronics', 'Al Rathaan J', 9, '2022-09-26 11:49:05', '2022-09-26 12:11:30'),
(39, 'shako mako', 'shako mako J', 13, '2022-09-26 11:57:53', '2022-09-26 11:57:53'),
(40, 'Lavender Clinic', 'Lavender Clinic J', 11, '2022-09-26 12:00:05', '2022-09-26 12:00:05'),
(41, 'McDonalds', 'McDonalds J', 64, '2022-09-26 12:10:49', '2022-09-26 12:10:49'),
(42, 'McDonalds', 'McDonalds A', 64, '2022-09-26 12:18:13', '2022-09-26 12:18:13'),
(43, 'Asayad Medical Center', 'Asayad A', 15, '2022-09-26 12:26:42', '2022-09-26 12:26:42'),
(44, 'Best', 'Best  A', 154.479, '2022-09-26 12:45:24', '2022-09-26 13:54:12'),
(45, 'Gate Mall', 'Gate Mall A', 5, '2022-09-26 12:56:17', '2022-09-26 12:56:17'),
(46, 'Hateen', 'Hateen A', 13, '2022-09-26 13:19:24', '2022-09-26 13:19:24'),
(47, 'Pharmazone', 'Pharmazone A', 16, '2022-09-26 13:34:33', '2022-09-26 13:34:33'),
(48, 'Mughal Mahal', 'Mughal Mahal A', 14, '2022-09-26 13:44:30', '2022-09-26 13:44:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
