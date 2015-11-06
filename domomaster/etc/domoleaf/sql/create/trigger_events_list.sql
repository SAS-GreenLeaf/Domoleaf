CREATE TABLE IF NOT EXISTS `trigger_events_list` (
  `id_trigger` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_name` varchar(100) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_trigger`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `trigger_events_list_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
