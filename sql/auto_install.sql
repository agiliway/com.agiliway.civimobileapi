CREATE TABLE IF NOT EXISTS `civicrm_contact_push_notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_civicrm_contact_push_notification_contact_id` (`contact_id`),
  KEY `INDEX_token` (`token`),
  KEY `INDEX_platform` (`platform`),
  KEY `INDEX_created_date` (`created_date`),
  KEY `INDEX_modified_date` (`modified_date`),
  KEY `INDEX_is_active` (`is_active`),
  CONSTRAINT `FK_civicrm_contact_push_notification_contact_id` FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
