CREATE DATABASE IF NOT EXISTS `whatsapp_messenger_3` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `whatsapp_messenger_3`;

CREATE TABLE IF NOT EXISTS `compose_whatsapp_3` (
  `compose_whatsapp_id` int NOT NULL,
  `user_id` int NOT NULL,
  `store_id` int NOT NULL,
  `whatspp_config_id` int NOT NULL,
  `mobile_nos` longblob NOT NULL,
  `whatsapp_content` varchar(1000) NOT NULL,
  `message_type` varchar(50) NOT NULL,
  `total_mobileno_count` int DEFAULT NULL,
  `content_char_count` int NOT NULL,
  `content_message_count` int NOT NULL,
  `campaign_name` varchar(30) DEFAULT NULL,
  `whatsapp_status` char(1) NOT NULL,
  `whatsapp_entry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `compose_whatsapp_status_3` (
  `comwtap_status_id` int NOT NULL,
  `compose_whatsapp_id` int NOT NULL,
  `mobile_no` varchar(10) NOT NULL,
  `comments` varchar(100) NOT NULL,
  `comwtap_status` char(1) NOT NULL,
  `comwtap_entry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `response_status` char(1) DEFAULT NULL,
  `response_message` varchar(100) DEFAULT NULL,
  `response_id` varchar(50) DEFAULT NULL,
  `response_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `delivery_status` char(1) DEFAULT NULL,
  `read_status` char(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `whatsapp_text_3` (
  `whatsapp_text_id` int NOT NULL,
  `compose_whatsapp_id` int NOT NULL,
  `sms_type` varchar(50) NOT NULL,
  `whatsapp_text_title` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `text_data` varchar(25) DEFAULT NULL,
  `text_reply` varchar(50) DEFAULT NULL,
  `text_number` varchar(13) DEFAULT NULL,
  `text_address` varchar(100) DEFAULT NULL,
  `text_name` varchar(30) DEFAULT NULL,
  `text_url` varchar(100) DEFAULT NULL,
  `text_title` varchar(200) DEFAULT NULL,
  `text_description` varchar(200) DEFAULT NULL,
  `text_start_time` timestamp NULL DEFAULT NULL,
  `text_end_time` timestamp NULL DEFAULT NULL,
  `carousel_fileurl` varchar(100) DEFAULT NULL,
  `carousel_srno` int DEFAULT NULL,
  `whatsapp_text_status` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `whatsapp_text_entry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

ALTER TABLE `compose_whatsapp_3`
  ADD PRIMARY KEY (`compose_whatsapp_id`),
  ADD KEY `user_id` (`user_id`,`store_id`,`whatspp_config_id`);

ALTER TABLE `compose_whatsapp_status_3`
  ADD PRIMARY KEY (`comwtap_status_id`),
  ADD KEY `compose_whatsapp_id` (`compose_whatsapp_id`);

ALTER TABLE `whatsapp_text_3`
  ADD PRIMARY KEY (`whatsapp_text_id`),
  ADD KEY `compose_whatsapp_id` (`compose_whatsapp_id`);

ALTER TABLE `compose_whatsapp_3`
  MODIFY `compose_whatsapp_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `compose_whatsapp_status_3`
  MODIFY `comwtap_status_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `whatsapp_text_3`
  MODIFY `whatsapp_text_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;