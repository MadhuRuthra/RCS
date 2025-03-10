CREATE TABLE IF NOT EXISTS `compose_whatsapp_tmpl_19` (
							`compose_whatsapp_id` int NOT NULL,
							`user_id` int NOT NULL,
							`store_id` int NOT NULL,
							`whatspp_config_id` int NOT NULL,
							`mobile_nos` longblob NOT NULL,
							`sender_mobile_nos` longblob NOT NULL,
							`whatsapp_content` varchar(1000) NOT NULL,
							`message_type` varchar(50) NOT NULL,
							`total_mobileno_count` int DEFAULT NULL,
							`content_char_count` int NOT NULL,
							`content_message_count` int NOT NULL,
							`campaign_name` varchar(30) DEFAULT NULL,
							`whatsapp_status` char(1) NOT NULL,
							`whatsapp_entry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `compose_whatsapp_status_tmpl_19` (
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

CREATE TABLE IF NOT EXISTS `whatsapp_text_tmpl_19` (
							`compose_whatsapp_msgid` int(11) NOT NULL,
              `compose_whatsapp_id` int(11) NOT NULL,
              `whatspp_template` varchar(600) NOT NULL,
              `whatsapp_tmpl_category` varchar(50) NOT NULL,
              `whatsapp_tmpl_name` varchar(500) NOT NULL,
              `whatsapp_tmpl_language` varchar(20) NOT NULL,
              `whatsapp_tmpl_hdtext` varchar(60) DEFAULT NULL,
              `whatsapp_tmpl_body` varchar(2000) NOT NULL,
              `whatsapp_tmpl_footer` varchar(60) DEFAULT NULL,
              `whatsapp_tmpl_status` char(1) NOT NULL,
              `whatsapp_tmpl_entrydate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

ALTER TABLE `compose_whatsapp_tmpl_19`
						ADD PRIMARY KEY (`compose_whatsapp_id`),
						ADD KEY `user_id` (`user_id`,`store_id`,`whatspp_config_id`);

ALTER TABLE `compose_whatsapp_status_tmpl_19`
						ADD PRIMARY KEY (`comwtap_status_id`),
						ADD KEY `compose_whatsapp_id` (`compose_whatsapp_id`);

ALTER TABLE `whatsapp_text_tmpl_19`
						ADD PRIMARY KEY (`compose_whatsapp_msgid`),
						ADD KEY `compose_whatsapp_id` (`compose_whatsapp_id`);

ALTER TABLE `compose_whatsapp_tmpl_19`
						MODIFY `compose_whatsapp_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
ALTER TABLE `compose_whatsapp_status_tmpl_19`
						MODIFY `comwtap_status_id` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
ALTER TABLE `whatsapp_text_tmpl_19`
						MODIFY `compose_whatsapp_msgid` int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;