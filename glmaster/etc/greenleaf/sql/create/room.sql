CREATE TABLE IF NOT EXISTS `room` (
  `room_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `room_name` varchar(63) NOT NULL DEFAULT '',
  `floor` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`room_id`),
  KEY `floor` (`floor`),
  CONSTRAINT `room_ibfk_1` FOREIGN KEY (`floor`) REFERENCES `floor` (`floor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
