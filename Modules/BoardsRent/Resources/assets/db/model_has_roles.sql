-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 13, 2023 at 12:48 PM
-- Server version: 8.0.33-0ubuntu0.22.10.1
-- PHP Version: 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alshamel_addeffect`
--

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Modules\\Admin\\Models\\Admin', 1),
(2, 'App\\Modules\\Admin\\Models\\Admin', 2),
(2, 'App\\Modules\\Admin\\Models\\Admin', 3),
(2, 'App\\Modules\\Admin\\Models\\Admin', 4),
(2, 'App\\Modules\\Admin\\Models\\Admin', 5),
(2, 'App\\Modules\\Admin\\Models\\Admin', 6),
(2, 'App\\Modules\\Admin\\Models\\Admin', 7),
(2, 'App\\Modules\\Admin\\Models\\Admin', 8),
(2, 'App\\Modules\\Admin\\Models\\Admin', 9),
(2, 'App\\Modules\\Admin\\Models\\Admin', 10),
(2, 'App\\Modules\\Admin\\Models\\Admin', 11),
(2, 'App\\Modules\\Admin\\Models\\Admin', 12),
(2, 'App\\Modules\\Admin\\Models\\Admin', 13),
(2, 'App\\Modules\\Admin\\Models\\Admin', 14),
(2, 'App\\Modules\\Admin\\Models\\Admin', 15),
(2, 'App\\Modules\\Admin\\Models\\Admin', 16),
(2, 'App\\Modules\\Admin\\Models\\Admin', 17),
(2, 'App\\Modules\\Admin\\Models\\Admin', 18),
(1, 'App\\Modules\\Admin\\Models\\Admin', 19),
(1, 'App\\Modules\\Admin\\Models\\Admin', 20),
(2, 'App\\Modules\\Admin\\Models\\Admin', 21),
(2, 'App\\Modules\\Admin\\Models\\Admin', 22),
(2, 'App\\Modules\\Admin\\Models\\Admin', 23),
(2, 'App\\Modules\\Admin\\Models\\Admin', 24),
(2, 'App\\Modules\\Admin\\Models\\Admin', 25),
(2, 'App\\Modules\\Admin\\Models\\Admin', 26),
(2, 'App\\Modules\\Admin\\Models\\Admin', 27),
(6, 'App\\Modules\\Admin\\Models\\Admin', 28),
(3, 'App\\Modules\\Admin\\Models\\Admin', 29),
(3, 'App\\Modules\\Admin\\Models\\Admin', 30),
(2, 'App\\Modules\\Admin\\Models\\Admin', 31),
(4, 'App\\Modules\\Admin\\Models\\Admin', 32),
(5, 'App\\Modules\\Admin\\Models\\Admin', 33),
(6, 'App\\Modules\\Admin\\Models\\Admin', 34),
(3, 'App\\Modules\\Admin\\Models\\Admin', 35);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
