-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 13, 2023 at 12:44 PM
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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `_lft` int UNSIGNED NOT NULL DEFAULT '0',
  `_rgt` int UNSIGNED NOT NULL DEFAULT '0',
  `parent_id` int UNSIGNED DEFAULT NULL,
  `salesman_type_id` int UNSIGNED NOT NULL,
  `direct_target` double DEFAULT NULL,
  `indirect_target` double DEFAULT NULL,
  `both_target` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `mobile`, `remember_token`, `deleted_at`, `created_at`, `updated_at`, `_lft`, `_rgt`, `parent_id`, `salesman_type_id`, `direct_target`, `indirect_target`, `both_target`) VALUES
(1, 'Admin', 'admin@admin.com', '$2y$10$5PQiLtzzVU4O8yFFVvDKU.PBb03RNJ3J4WKuj/PV2QUcYwoU5FgQC', NULL, 'jTW1pZg0RJ1HHZ3bm289BtItsLyobmIaqavdZaIRiFOZ9pLtsAF9cuv5HX8A', NULL, '2022-01-09 05:08:49', '2022-10-11 13:06:10', 16, 67, 33, 0, NULL, NULL, NULL),
(2, 'Jerome Schuster III', 'okunde@example.com', '$2y$10$EPlb.3ta23lYNh7DauixPe4pAnjZUGTd1V/Hv9tCDtmE0vMWXaLQ2', NULL, NULL, '2022-01-09 15:19:06', '2022-01-09 05:08:49', '2022-01-17 11:45:15', 1, 2, NULL, 0, NULL, NULL, NULL),
(3, 'Alec Wuckert', 'wiegand.don@example.org', '$2y$10$D4s06JrVxwQ/iK1B3Zw4Tu4eY9DYPaIVuXnNPs3e62RQ2jJrVVzGK', NULL, NULL, '2022-01-09 15:19:11', '2022-01-09 05:08:49', '2022-01-17 11:45:15', 3, 4, NULL, 0, NULL, NULL, NULL),
(4, 'Dr. Hayley Feeney DVM', 'rdonnelly@example.org', '$2y$10$TrTqfO5FddOr6mYxWxrF5.bQBktuJy0ES/RrN1775quwwQRfYz/pm', NULL, NULL, '2022-01-09 15:19:17', '2022-01-09 05:08:50', '2022-01-17 11:45:15', 5, 6, NULL, 0, NULL, NULL, NULL),
(5, 'Eldora Boyle', 'jframi@example.org', '$2y$10$vHN3ZpGoft.HrQKqXLb9y.UMLJTQJNrPrOlKarL/FB9X4P77tP3.O', NULL, NULL, '2022-01-09 15:19:24', '2022-01-09 05:08:50', '2022-01-17 11:45:15', 7, 8, NULL, 0, NULL, NULL, NULL),
(6, 'Prof. Elisabeth Torp IV', 'ignatius.brakus@example.net', '$2y$10$35/gW6iuasninA2DrMEXdebk21eZxKzB/iQT4Vnh0GGH6jUKFwvny', NULL, NULL, '2022-01-09 15:19:30', '2022-01-09 05:08:50', '2022-01-17 11:45:15', 9, 10, NULL, 0, NULL, NULL, NULL),
(7, 'Amar Abboud', 'amar.abboud@add-effect.com', '$2y$10$jMvDm2LI51k3.vzd8WrwiOe4tNIuFBWl7.5kUyM4G7cpiEzQpgB76', '67778668', 'sN5y8g5qUUIUyy5pwxwXRg2KrjJJXFRmjRMtgM7hAhbsZbo2v50JN1E17VCb', NULL, '2022-01-09 05:09:01', '2022-02-21 17:06:12', 38, 39, 18, 0, NULL, NULL, NULL),
(8, 'Gaspar Nemer', 'gaspar.nemer@add-effect.com', '$2y$10$EhOCOsUyE5ZlS91nBvuj4enjWGGGxxpQmUxV5jUpkzBWr/oSpNHUK', '50764455', 'nf8e5yruDFu1krb90ml11AdXamo04AGL5V8poNuZsDeTAQegdFYjKpkb0xGX', NULL, '2022-01-09 05:09:01', '2022-02-21 16:41:20', 19, 20, 14, 0, NULL, NULL, NULL),
(9, 'Farzat Wajukh', 'wajukh@add-effect.com', '$2y$10$cKMRrBhENh7BNOo9PqnQwud/29k9UrN3dGXWYEo7BGYhqMItQDhou', '99967885', 'o2PSkmaC2n6I4vz3SVwjGpAR4v03UaunBJI3mawqFwYW2x5ls2QjzFCtnPF3', NULL, '2022-01-09 05:09:01', '2022-02-21 17:07:13', 21, 22, 14, 0, NULL, NULL, NULL),
(10, 'Toni Chadrawi', 'toni.chadrawi@add-effect.com', '$2y$10$LupvnHo5hd.NylF8wVdFzeZmdNSbP6FgAXkVrNoaKKduG/Sq7Wb1y', '97772750', NULL, NULL, '2022-01-09 05:09:01', '2022-10-10 11:35:36', 32, 33, 17, 0, NULL, NULL, NULL),
(11, 'Essam Sedky', 'essam.sedky@add-effect.com', '$2y$10$ywgNWfG.aRa7tAlBKXmhPePNDaNNC.BWRkIHwoj6vQ8LlsYXgkHzG', '97970841', NULL, NULL, '2022-01-09 05:09:01', '2022-02-21 17:10:43', 34, 35, 17, 0, NULL, NULL, NULL),
(12, 'Mohamad Farhat', 'm.farhat@add-effect.com', '$2y$10$/V0tm6TvIQo1rwgovFk0xOAdxk4Da9kMBrDQKRWZWX/a3ZcTdGrNm', '65028024', '3khxEVrKaL0v0zMTA13J2hyoGuYmURssMUNP6w6ePjTrcuwiCm9bnRn1Z5C9', NULL, '2022-01-09 05:09:01', '2022-02-21 17:12:13', 23, 24, 14, 0, NULL, NULL, NULL),
(13, 'Tarek Mohy', 'tmohy@add-effect.com', '$2y$10$lHjxIhDD3l7qVa0B8C7WPuiy7uexx1mQUSNoJqHfpuiVC5c3Zv.8y', '97558503', 'XjYjTsbbRZer1UdcRQFPk3XJBBFkiLMKTvsvNtHXvYM0RfdzMngco1fBp4HQ', NULL, '2022-01-09 05:09:01', '2022-02-21 17:13:46', 25, 26, 14, 0, NULL, NULL, NULL),
(14, 'Ahmad Abdul-Aziz', 'aabdulaziz@add-effect.com', '$2y$10$Ov/lPQ5twaDvQp1slAnQaepVvzT8LR4Khg3bedLEVGgjDbgaB0R7q', '66215642', NULL, NULL, '2022-01-09 05:09:01', '2022-04-21 11:25:08', 18, 41, 19, 0, NULL, NULL, NULL),
(15, 'Louai Hassan', 'louai.hasan@add-effect.com', '$2y$10$1IFjPiyHQVrFlUYIAkdQEOhr1QGjNRwpbphpEEsPrJ35EEh2cuiQG', '50524432', NULL, NULL, '2022-01-09 05:09:01', '2022-04-21 11:25:54', 27, 28, 14, 0, NULL, NULL, NULL),
(16, 'Khalifa', 'khalifa@add-effect.com', '$2y$10$.0jOiENYkBzHg5v7FajWw.RyKOdCeZpvG0FJeWriRQhv9nFrKRffi', '94455531', NULL, NULL, '2022-01-09 05:09:01', '2022-04-21 11:26:10', 29, 30, 14, 0, NULL, NULL, NULL),
(17, 'Mohamed Said', 'msaid@add-effect.com', '$2y$10$h./WHjGoMWmbgWgpXTbheOzrfreA67q8xEsSYaPp.O7v0fDECaGU.', '51608515', 'VogVfso3Jsxl2xKG7OlfkpCBFGqpOa5ax9VuaNYT8mm2L447z2jcOL7oxOnI', NULL, '2022-01-09 05:09:01', '2022-04-21 11:26:33', 31, 36, 14, 0, NULL, NULL, NULL),
(18, 'Fadi Iskander', 'Fadi@add-effect.com', '$2y$10$uZFgggs0wektkKWBUHTH4uXwEnPfzCoV2/5ZuBUUsY1mRXMPFBsr6', '66483337', NULL, NULL, '2022-01-09 05:09:01', '2022-04-21 11:27:23', 37, 40, 14, 0, NULL, NULL, NULL),
(19, 'Mondy', 'admin1@admin.com', '$2y$10$Gr7MwlkOcpP17kAPApefEOwjohMndqqQt4JmSZTG8/FDuqFsGgWL6', '6666666666', 'JbPPchavbQ6bqe3mRRrZeAtfqjJnT79iXz60EghHyuY11Cq80mHpGMKEaF81', NULL, '2022-01-09 15:24:49', '2022-10-10 17:48:14', 17, 58, 1, 0, NULL, NULL, NULL),
(20, 'Ahmed Shehab', 'admin2@admin.com', '$2y$10$pQWFmpNpC0S35WbDBNwRju56upLkyNidHXUa02390WZx10wP0nOjG', '5555555', 'fl2F3Q6YQnQHJ82TRX5yqNkgu59fTcOwaYIHLhsfBKEAiSoNwAA0pSKXIwvQ', NULL, '2022-01-09 15:26:33', '2022-05-12 15:20:43', 11, 12, NULL, 0, NULL, NULL, NULL),
(21, 'Amr Al Massry', 'amr.massry@add-effect.com', '$2y$10$y6s4DRQmu65aoKp/oZVMj.zGbwVSb0so34wTtN.WR07xc293niC/e', '50299496‬', NULL, NULL, '2022-04-21 11:31:23', '2022-04-21 11:31:23', 42, 45, 19, 0, NULL, NULL, NULL),
(22, 'Rana Abo Diab', 'rana@add-effect.com', '$2y$10$7s4NqOqlwK21IvnckQ3geubkdAfboSxBg3YkET/FRCnMPOC7kpRGm', '97300321', NULL, NULL, '2022-04-21 11:32:13', '2022-04-21 11:32:13', 43, 44, 21, 0, NULL, NULL, NULL),
(23, 'Tony Assaf', 'tony@add-effect.com', '$2y$10$FiwJ7ORRUUJ16ALE7BbR8udHJsnLkSfptO6S56wRn7/kRRa1EV8bi', '97433966‬', NULL, NULL, '2022-04-21 11:33:03', '2022-04-21 11:33:03', 46, 47, 19, 0, NULL, NULL, NULL),
(24, 'Micheal', 'micheal@micheal.com', '$2y$10$tRwntSkJQrw424uOFKx55.YdMjQogbhS/K97IlxL4RWgkSEvIwzvW', '55627472', NULL, '2022-10-10 17:43:08', '2022-10-10 15:44:41', '2022-10-10 17:43:08', 48, 49, 19, 0, NULL, NULL, NULL),
(25, 'Micheal', 'micheal@admin.com', '$2y$10$euu8cTJmu8qm.uDsoSfTJ.fZEryCsgicMBJ0.F.mnmDQ5q3EEfSsq', '67890543', NULL, '2022-10-10 17:43:32', '2022-10-10 15:47:39', '2022-10-10 17:43:32', 50, 51, 19, 0, NULL, NULL, NULL),
(26, 'Micheal', 'admin232@admin.com', '$2y$10$3FcTreWt6ax8707Td2Vsrudv6H96KEITu2gm0DQlq1m/M0dzbOfnK', NULL, NULL, '2022-10-10 17:43:24', '2022-10-10 15:49:45', '2022-10-10 17:43:24', 52, 53, 19, 0, NULL, NULL, NULL),
(27, 'Micheal', 'admin244@admin.com', '$2y$10$z6.VXzZ947xBDAxnKE78m.jZURqWlhbfsJt0miY7UsWJK3OMfsVLC', '5679867', NULL, '2022-10-10 17:43:43', '2022-10-10 15:53:51', '2022-10-10 17:43:43', 54, 55, 19, 0, NULL, NULL, NULL),
(28, 'Account@add-effect.com', 'Account@add-effect.com', '$2y$10$8gDZmBGLu3QF4UYDQd0xauXbxMzc1I3MOqTEckF4DxxB4SRjI/Wt.', NULL, NULL, '2022-10-11 13:14:01', '2022-10-10 16:13:51', '2022-10-11 13:14:01', 56, 57, 19, 0, NULL, NULL, NULL),
(29, 'sales@add-effect.com', 'sales@add-effect.com', '$2y$10$mq61cvAsywlYwS4vxQsB0e2UPdUFEXQsd3IIUaz7/o3Ou6Wv5HGNG', NULL, NULL, '2022-10-11 13:04:20', '2022-10-10 16:19:46', '2022-10-11 13:04:20', 65, 66, 1, 0, NULL, NULL, NULL),
(30, 'Reservation@add-effect.com', 'Reservation@add-effect.com', '$2y$10$Wpc3MvG.Iz2wXzVBaDpRnuWfhmmjqmlTtyJZRVpfHW8LNoOQq6Tce', NULL, NULL, '2022-10-11 13:14:17', '2022-10-10 16:21:17', '2022-10-11 13:14:17', 59, 60, 1, 0, NULL, NULL, NULL),
(31, 'installation@add-effect.com', 'installation@add-effect.com', '$2y$10$CpP9oOprRVfh7e2CcB0VduyrNa3r/zZLZpnQCdBsOS3ac8o2t0AHu', NULL, NULL, '2022-10-11 13:14:30', '2022-10-10 16:22:44', '2022-10-11 13:14:30', 61, 62, 1, 0, NULL, NULL, NULL),
(32, 'Printing@add-effect.com', 'Printing@add-effect.com', '$2y$10$7qWM95KhV42kTg9JtyKIpOLyBw9PAghopFM.0cEQWVTPFsDpbPfTW', NULL, NULL, '2022-10-11 13:14:42', '2022-10-10 16:23:48', '2022-10-11 13:14:42', 63, 64, 1, 0, NULL, NULL, NULL),
(33, 'sales2@add-effect.com', 'sales2@add-effect.com', '$2y$10$J3lh7z2rig/2UT0sufVrx.cnBABKbmLyM7QrtiLOgT8uE1YWiZMKW', NULL, '56FHIYQ5YxJKAq5CSHz0IoRHSkANatqjF4faqLLdmMb0eIy9iQYZXNd8juiu', NULL, '2022-10-11 13:05:44', '2022-10-11 13:17:31', 15, 68, 34, 0, NULL, NULL, NULL),
(34, 'Account1@add-effect.com', 'Account1@add-effect.com', '$2y$10$0tC/h6kwQudb3mAl1vi29ebodraUPiFLO.4Qr/eKycb/vtswh.U9q', NULL, NULL, NULL, '2022-10-11 13:15:29', '2022-10-11 14:12:37', 14, 69, 35, 0, NULL, NULL, NULL),
(35, 'Reservation1@add-effect.com', 'Reservation1@add-effect.com', '$2y$10$V74evdJOn9StGA6/L3F6uOVEV5SnZx1gE2nanwlxR/8Ct4TulJ4vG', NULL, NULL, NULL, '2022-10-11 13:25:49', '2022-10-11 14:12:11', 13, 70, NULL, 0, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`),
  ADD KEY `admins__lft__rgt_parent_id_index` (`_lft`,`_rgt`,`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
