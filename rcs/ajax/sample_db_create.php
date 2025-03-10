<?php
/*
Authendicated users details getting to navigate this sample db create page.
This page is used to give the create the new database and add columns
Api response is used to getting all data from the database.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
*/
session_start(); //start session
error_reporting(E_ALL); // The error reporting function
include_once('../api/configuration.php'); // Include configuration.php
$new_dbname = "whatsapp_messenger_4";
$new_indicator = "4";

$exp_result1 = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$new_dbname` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
// Create connection
$newconn = new mysqli($servername, $username, $password, $new_dbname);
// Check connection
if ($newconn->connect_error) {
  die("Connection failed: " . $newconn->connect_error);
} else {
  echo "Connected";
}
// create new database
mysqli_query($newconn, "SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
// create new table
$exp_result2 = mysqli_query($newconn, "CREATE TABLE IF NOT EXISTS `compose_whatsapp_$new_indicator` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
// create new table
$exp_result3 = mysqli_query($newconn, "CREATE TABLE IF NOT EXISTS `compose_whatsapp_status_$new_indicator` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
// create new table
$exp_result4 = mysqli_query($newconn, "CREATE TABLE IF NOT EXISTS `whatsapp_text_$new_indicator` (
  `whatsapp_text_id` int NOT NULL,
  `compose_whatsapp_id` int NOT NULL,
  `sms_type` varchar(50) NOT NULL,
  `whatsapp_text_title` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `text_data` varchar(2000) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT");

// create new table
$exp_result5 = mysqli_query($newconn, "ALTER TABLE `compose_whatsapp_$new_indicator`
ADD PRIMARY KEY (`compose_whatsapp_id`),
ADD KEY `user_id` (`user_id`,`store_id`,`whatspp_config_id`)");
$exp_result6 = mysqli_query($newconn, "ALTER TABLE `compose_whatsapp_status_$new_indicator`
ADD PRIMARY KEY (`comwtap_status_id`),
ADD KEY `compose_whatsapp_id` (`compose_whatsapp_id`)");
$exp_result7 = mysqli_query($newconn, "ALTER TABLE `whatsapp_text_$new_indicator`
ADD PRIMARY KEY (`whatsapp_text_id`),
ADD KEY `compose_whatsapp_id` (`compose_whatsapp_id`)");

// create new table
$exp_result8 = mysqli_query($newconn, "ALTER TABLE `compose_whatsapp_$new_indicator`
MODIFY `compose_whatsapp_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
$exp_result9 = mysqli_query($newconn, "ALTER TABLE `compose_whatsapp_status_$new_indicator`
MODIFY `comwtap_status_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
$exp_result10 = mysqli_query($newconn, "ALTER TABLE `whatsapp_text_$new_indicator`
MODIFY `whatsapp_text_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1");
?>