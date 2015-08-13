CREATE TABLE `user_device` (
  `user_id` int(10) unsigned NOT NULL,
  `room_device_id` int(10) unsigned NOT NULL,
  `device_allowed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `device_order` int(10) unsigned NOT NULL DEFAULT '0',
  `device_bgimg` varchar(255) NOT NULL DEFAULT '',
  KEY `user_id` (`user_id`),
  KEY `room_device_id` (`room_device_id`),
  CONSTRAINT `user_device_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_device_ibfk_2` FOREIGN KEY (`room_device_id`) REFERENCES `room_device` (`room_device_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
