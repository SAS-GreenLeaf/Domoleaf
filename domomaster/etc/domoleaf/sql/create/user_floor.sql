CREATE TABLE `user_floor` (
  `user_id` int(10) unsigned NOT NULL,
  `floor_id` int(10) unsigned NOT NULL,
  `floor_allowed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `floor_order` int(10) unsigned NOT NULL DEFAULT '0',
  `floor_bgimg` varchar(255) DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `floor_id` (`floor_id`),
  CONSTRAINT `user_floor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_floor_ibfk_2` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`floor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
