-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 20, 2024 at 03:24 PM
-- Server version: 8.0.37
-- PHP Version: 8.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rcs_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `compose_rcs_status_tmpl_1`
--

CREATE TABLE `compose_rcs_status_tmpl_1` (
  `comrcs_status_id` int NOT NULL,
  `compose_rcs_id` int NOT NULL,
  `variable_values` text,
  `mobile_no` varchar(13) NOT NULL,
  `media_url` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `comments` varchar(100) NOT NULL,
  `comrcs_status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comrcs_entry_date` timestamp NOT NULL,
  `response_status` char(1) DEFAULT NULL,
  `response_message` varchar(100) DEFAULT NULL,
  `response_id` varchar(100) DEFAULT NULL,
  `corelation_id` varchar(50) NOT NULL,
  `response_date` timestamp NULL DEFAULT NULL,
  `delivery_status` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `delivery_date` timestamp NULL DEFAULT NULL,
  `read_date` timestamp NULL DEFAULT NULL,
  `read_status` char(1) DEFAULT NULL,
  `campaign_status` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `compose_rcs_tmp_1`
--

CREATE TABLE `compose_rcs_tmp_1` (
  `compose_rcs_id` int NOT NULL,
  `user_id` int NOT NULL,
  `store_id` int NOT NULL,
  `rcs_config_id` int NOT NULL,
  `mobile_nos` longblob NOT NULL,
  `sender_mobile_nos` longblob NOT NULL,
  `variable_values` longblob,
  `media_values` longblob,
  `rcs_content` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `message_type` varchar(50) NOT NULL,
  `total_mobileno_count` int DEFAULT NULL,
  `content_char_count` int NOT NULL,
  `content_message_count` int NOT NULL,
  `campaign_name` varchar(30) DEFAULT NULL,
  `campaign_id` varchar(10) DEFAULT NULL,
  `mobile_no_type` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `unique_template_id` varchar(30) NOT NULL,
  `template_id` varchar(10) DEFAULT NULL,
  `rcs_status` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `rcs_entry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `media_url` varchar(100) DEFAULT NULL,
  `reject_reason` varchar(50) DEFAULT NULL,
  `receiver_nos_path` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `compose_rcs_tmp_1`
--

INSERT INTO `compose_rcs_tmp_1` (`compose_rcs_id`, `user_id`, `store_id`, `rcs_config_id`, `mobile_nos`, `sender_mobile_nos`, `variable_values`, `media_values`, `rcs_content`, `message_type`, `total_mobileno_count`, `content_char_count`, `content_message_count`, `campaign_name`, `campaign_id`, `mobile_no_type`, `unique_template_id`, `template_id`, `rcs_status`, `rcs_entry_date`, `media_url`, `reject_reason`, `receiver_nos_path`) VALUES
(1, 1, 0, 1, 0x2d, 0x2d, 0x5b5d, 0x5b5d, 'te_pri_24820_001', 'TEXT', 2, 1, 2, 'ca_pri_233_1', '9LJYCGULH8', NULL, 'tmplt_pri_233_001', NULL, 'W', '2024-08-20 15:16:16', 'NULL', NULL, '/var/www/html/rcs/uploads/compose_variables/1_csv_1724166955707.csv');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `compose_rcs_status_tmpl_1`
--
ALTER TABLE `compose_rcs_status_tmpl_1`
  ADD PRIMARY KEY (`comrcs_status_id`),
  ADD KEY `compose_whatsapp_id` (`compose_rcs_id`),
  ADD KEY `mobile_no` (`mobile_no`),
  ADD KEY `report_group` (`media_url`);

--
-- Indexes for table `compose_rcs_tmp_1`
--
ALTER TABLE `compose_rcs_tmp_1`
  ADD PRIMARY KEY (`compose_rcs_id`),
  ADD KEY `user_id` (`user_id`,`store_id`,`rcs_config_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `compose_rcs_status_tmpl_1`
--
ALTER TABLE `compose_rcs_status_tmpl_1`
  MODIFY `comrcs_status_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compose_rcs_tmp_1`
--
ALTER TABLE `compose_rcs_tmp_1`
  MODIFY `compose_rcs_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
