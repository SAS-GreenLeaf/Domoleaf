CREATE TABLE IF NOT EXISTS `room_device_option` (
  `room_device_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `addr` varchar(1023) DEFAULT NULL,
  `addr_plus` varchar(255) DEFAULT NULL,
  `dpt_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `valeur` varchar(15) NOT NULL DEFAULT '',
  KEY `room_device_id` (`room_device_id`),
  KEY `option_id` (`option_id`),
  KEY `addr_plus` (`addr_plus`),
  KEY `addr` (`addr`(767)),
  KEY `dpt_id` (`dpt_id`),
  CONSTRAINT `room_device_option_ibfk_1` FOREIGN KEY (`room_device_id`) REFERENCES `room_device` (`room_device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `room_device_option_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `room_device_option_ibfk_3` FOREIGN KEY (`dpt_id`) REFERENCES `dpt` (`dpt_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
