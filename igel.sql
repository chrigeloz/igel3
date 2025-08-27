-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 25, 2025 at 11:41 PM
-- Server version: 10.6.22-MariaDB-cll-lve-log
-- PHP Version: 8.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `chribhtl_igel`
--
CREATE DATABASE IF NOT EXISTS `chribhtl_igel` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chribhtl_igel`;

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

DROP TABLE IF EXISTS `animals`;
CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `finder_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `species` varchar(100) NOT NULL,
  `age` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female','Unknown') DEFAULT 'Unknown',
  `description` text DEFAULT NULL,
  `reason_for_admission` text DEFAULT NULL,
  `location_found` varchar(255) DEFAULT NULL,
  `date_admission` date DEFAULT NULL,
  `date_release` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `case_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `animals`
--
DROP TRIGGER IF EXISTS `animals_before_insert`;
DELIMITER $$
CREATE TRIGGER `animals_before_insert` BEFORE INSERT ON `animals` FOR EACH ROW BEGIN
    DECLARE seq INT;
    DECLARE pad_length INT DEFAULT 3; -- Change to your desired default padding length

    -- Ensure a row for the current year exists
    INSERT INTO case_code_seq (year, last_number)
    VALUES (YEAR(CURDATE()), 0)
    ON DUPLICATE KEY UPDATE year = year;

    -- Increment and get sequence
    UPDATE case_code_seq
    SET last_number = last_number + 1
    WHERE year = YEAR(CURDATE());

    SELECT last_number INTO seq
    FROM case_code_seq
    WHERE year = YEAR(CURDATE());

    -- If case_code not manually provided, generate it
    IF NEW.case_code IS NULL OR NEW.case_code = '' THEN
        SET NEW.case_code = CONCAT(
            LPAD(RIGHT(YEAR(CURDATE()), 2), 2, '0'),
            LPAD(seq, pad_length, '0')
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `case_code_seq`
--

DROP TABLE IF EXISTS `case_code_seq`;
CREATE TABLE `case_code_seq` (
  `year` int(11) NOT NULL,
  `last_number` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason_release` enum('','Died','Euthanised','Released','Readmitted') DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_medications`
--

DROP TABLE IF EXISTS `event_medications`;
CREATE TABLE `event_medications` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `medication_id` int(11) NOT NULL,
  `amount_used` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finders`
--

DROP TABLE IF EXISTS `finders`;
CREATE TABLE `finders` (
  `id` int(11) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

DROP TABLE IF EXISTS `medications`;
CREATE TABLE `medications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expiry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `finder_id` (`finder_id`);

--
-- Indexes for table `case_code_seq`
--
ALTER TABLE `case_code_seq`
  ADD PRIMARY KEY (`year`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Indexes for table `event_medications`
--
ALTER TABLE `event_medications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `medication_id` (`medication_id`);

--
-- Indexes for table `finders`
--
ALTER TABLE `finders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_finder` (`firstname`,`lastname`,`phone`);

--
-- Indexes for table `medications`
--
ALTER TABLE `medications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_medications`
--
ALTER TABLE `event_medications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finders`
--
ALTER TABLE `finders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medications`
--
ALTER TABLE `medications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`finder_id`) REFERENCES `finders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_medications`
--
ALTER TABLE `event_medications`
  ADD CONSTRAINT `event_medications_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_medications_ibfk_2` FOREIGN KEY (`medication_id`) REFERENCES `medications` (`id`);
COMMIT;
