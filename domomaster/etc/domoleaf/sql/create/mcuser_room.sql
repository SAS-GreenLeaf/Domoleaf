CREATE TABLE IF NOT EXISTS `mcuser_room` (
  `mcuser_id` int(10) unsigned NOT NULL,
  `room_id` int(10) unsigned NOT NULL,
  `room_allowed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `room_order` int(10) unsigned NOT NULL DEFAULT '0',
  `room_bgimg` varchar(255) DEFAULT NULL,
  KEY `mcuser_id` (`mcuser_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `mcuser_room_ibfk_1` FOREIGN KEY (`mcuser_id`) REFERENCES `mcuser` (`mcuser_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mcuser_room_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
