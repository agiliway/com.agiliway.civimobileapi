CREATE TABLE IF NOT EXISTS `civicrm_contact_push_notification_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity_table` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `send_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_civicrm_contact_push_notification_message` (`contact_id`),
  KEY `INDEX_message` (`message`),
  KEY `INDEX_entity_table` (`entity_table`),
  KEY `INDEX_entity_record_id` (`entity_id`),
  KEY `INDEX_send_date` (`send_date`),
  KEY `INDEX_is_read` (`is_read`),
  CONSTRAINT `FK_civicrm_contact_push_notification_message` FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
