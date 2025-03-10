-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2023 at 08:14 AM
-- Server version: 8.0.27
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `whatsapp_messenger`
--

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_config`
--

CREATE TABLE IF NOT EXISTS `whatsapp_config` (
  `whatspp_config_id` int NOT NULL,
  `user_id` int NOT NULL,
  `store_id` int NOT NULL,
  `mobile_no` varchar(10) NOT NULL,
  `qr_code_allowed` char(1) NOT NULL,
  `whatspp_config_status` char(1) NOT NULL,
  `whatspp_config_entdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `phone_number_id` varchar(50) DEFAULT NULL,
  `whatsapp_business_acc_id` varchar(50) DEFAULT NULL,
  `bearer_token` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `wht_display_name` varchar(30) DEFAULT NULL,
  `wht_display_logo` varchar(100) DEFAULT NULL,
  `message_category_id` int DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `whatsapp_config`
--

INSERT INTO `whatsapp_config` (`whatspp_config_id`, `user_id`, `store_id`, `mobile_no`, `qr_code_allowed`, `whatspp_config_status`, `whatspp_config_entdate`, `phone_number_id`, `whatsapp_business_acc_id`, `bearer_token`, `wht_display_name`, `wht_display_logo`, `message_category_id`) VALUES
(1, 1, 1, '8610110464', 'A', 'Y', '2023-03-01 08:37:51', '103741605696935', '100175206060494', 'EAAlaTtm1XV0BANV3Lc8mA5kEO4BqWsCKudO6lNWGcVyl6O6wIK7mJqXCtPtpyjhO36ZA1eEGLra4Q21T7aEWns1VxqwcOFVR4BtQsxShdMB9zBIPjN4gaj3KTz5ZBHnEtO3WVkC26UdLpM75vIZBIZCw8eCRVus4NcZC7FZC3NhBFqpF3ntmGh13ZAZBdUcVtwJ9Mcout3A1ZCwZDZD', 'YeeJai Technologies', '1_1677659871701.png', 1),
(2, 1, 1, '9025181189', 'A', 'Y', '2023-03-01 08:38:13', '109025285456932', '116537601364482', 'EAAlaTtm1XV0BANV3Lc8mA5kEO4BqWsCKudO6lNWGcVyl6O6wIK7mJqXCtPtpyjhO36ZA1eEGLra4Q21T7aEWns1VxqwcOFVR4BtQsxShdMB9zBIPjN4gaj3KTz5ZBHnEtO3WVkC26UdLpM75vIZBIZCw8eCRVus4NcZC7FZC3NhBFqpF3ntmGh13ZAZBdUcVtwJ9Mcout3A1ZCwZDZD', 'YJ Watsp1', '1_1677659893272.png', 1),
(3, 1, 1, '9345450984', 'A', 'Y', '2023-03-01 08:38:32', '102155219488507', '111944291830521', 'EAAlaTtm1XV0BAHNphQiCsNRjQArcG5v9dcksaqFbYdf5Dti7Gz7rprDqaRBZAsMmOjALqgGcGQ1rGa2yTxw7GcEeXAre2MaN26wX17kt1PeLDm2TvfD47kcQC49WPJ9gxTby4vEGrg53BwjkNhUPryWnncZBZC0hZAPZCyGly3bbKpD9ysZB6DrQo4k3ndziZAofNlSxtxRTZBNkI14e0ZB2S', 'YJ Watsp2', '1_1677659912615.png', 1),
(4, 1, 1, '9943509864', 'A', 'Y', '2023-03-01 08:38:49', '101053762933513', '100777492961727', 'EAAlaTtm1XV0BAHNphQiCsNRjQArcG5v9dcksaqFbYdf5Dti7Gz7rprDqaRBZAsMmOjALqgGcGQ1rGa2yTxw7GcEeXAre2MaN26wX17kt1PeLDm2TvfD47kcQC49WPJ9gxTby4vEGrg53BwjkNhUPryWnncZBZC0hZAPZCyGly3bbKpD9ysZB6DrQo4k3ndziZAofNlSxtxRTZBNkI14e0ZB2S', 'YJ Watsp3', '1_1677659929818.png', 1),
(5, 2, 1, '9986012683', 'U', 'Y', '2023-03-01 08:39:40', '114726254883383', '102609756110071', 'EAAlaTtm1XV0BAJ7dfPiZAavePWvaSPZAqZCm33zmiBW2rUjW0ZBX2LSf3mKxPWen8yBi67hbSVwd3fwMvcAeTmQXOkVyY8uXEPRX7YNLTGvdZBL2RDwTlr5tms8iEWT9oNEIhRWa3RFEZA82QRZA40aAumb21rlDBNkBbDZCGYMOKgzNctMhsIc5Vel4BryU0kUPDcykUs8qVGewO6bNvzZCD', 'Slice Test', '2_1677659980864.png', 1),
(6, 2, 1, '9739045206', 'U', 'Y', '2023-03-01 08:40:03', '110696988623932', '115727271449853', 'EAAlaTtm1XV0BAOVl61mqfpSOEwbgyo4w2UAKCyF1WVZCjvQeoSWFgzH3dOAVnlyaIiasmrbjrEPB0nOZBCKdZC1HNA3DtD6RU29Hasj3Y7PIxCxZBgM0JkmkDjadydbZAUJ9ZBZBUiZAWgpL1X2QihO3K9hhkdt62oIG2AToij5ZBJ0dMWCBGriz3', 'Slice Test1', '2_1677660003186.png', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `whatsapp_config`
--
ALTER TABLE `whatsapp_config`
  ADD PRIMARY KEY (`whatspp_config_id`),
  ADD KEY `message_category_id` (`message_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `whatsapp_config`
--
ALTER TABLE `whatsapp_config`
  MODIFY `whatspp_config_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
