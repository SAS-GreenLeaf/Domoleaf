CREATE TABLE IF NOT EXISTS `smartcommand_list` (
  `smartcommand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `room_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`smartcommand_id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `smartcommand_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `smartcommand_list_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;