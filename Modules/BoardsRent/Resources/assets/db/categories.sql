-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 03, 2023 at 05:31 PM
-- Server version: 8.0.32-0ubuntu0.22.10.2
-- PHP Version: 7.2.34-38+ubuntu22.04.1+deb.sury.org+1

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
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` longtext NOT NULL,
  `_lft` int UNSIGNED NOT NULL DEFAULT '0',
  `_rgt` int UNSIGNED NOT NULL DEFAULT '0',
  `parent_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `_lft`, `_rgt`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, '{\"en\":\"mega\",\"ar\":\"mega\"}', 1, 2, NULL, '2022-09-03 12:24:28', '2022-09-03 12:24:28'),
(2, '{\"en\":\"Unipole\",\"ar\":\"Unipole\"}', 3, 4, NULL, '2022-09-03 12:28:52', '2022-09-03 12:28:52'),
(3, '{\"en\":\"Super Unipole\",\"ar\":\"Super Unipole \"}', 5, 6, NULL, '2022-09-03 12:32:13', '2022-09-03 12:32:13'),
(4, '{\"en\":\"LED\",\"ar\":\"LED \"}', 7, 8, NULL, '2022-09-03 12:35:36', '2022-09-03 12:35:36'),
(5, '{\"en\":\"Mupi\",\"ar\":\"Mupi \"}', 9, 10, NULL, '2022-09-03 12:39:32', '2022-09-03 12:39:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories__lft__rgt_parent_id_index` (`_lft`,`_rgt`,`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
