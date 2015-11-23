CREATE TABLE IF NOT EXISTS `mcuser_floor` (
  `mcuser_id` int(10) unsigned NOT NULL,
  `floor_id` int(10) unsigned NOT NULL,
  `floor_allowed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `floor_order` int(10) unsigned NOT NULL DEFAULT '0',
  `floor_bgimg` varchar(255) DEFAULT NULL,
  KEY `mcuser_id` (`mcuser_id`),
  KEY `floor_id` (`floor_id`),
  CONSTRAINT `mcuser_floor_ibfk_1` FOREIGN KEY (`mcuser_id`) REFERENCES `mcuser` (`mcuser_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mcuser_floor_ibfk_2` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`floor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
