CREATE TABLE IF NOT EXISTS `civicrm_civimobile_event_payment_info` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`event_id` int(10) unsigned NOT NULL,
`cmb_hash` TEXT,
`price_set` TEXT,
`contact_id` int(10) unsigned NULL,
`first_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
`last_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
`email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
`public_key` TEXT,
PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
