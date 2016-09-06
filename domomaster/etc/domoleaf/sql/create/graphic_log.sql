CREATE TABLE IF NOT EXISTS `graphic_log` (
  `date` bigint(20) unsigned NOT NULL,
  `value` int(11) unsigned NOT NULL,
  `room_device_id` int(11) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  KEY `room_device_id` (`room_device_id`),
  KEY `option_id` (`option_id`),
  KEY `date` (`date`),
  CONSTRAINT `graphic_log_ibfk_1` FOREIGN KEY (`room_device_id`) REFERENCES `room_device` (`room_device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `graphic_log_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
