CREATE TABLE IF NOT EXISTS `trigger_events_list` (
  `id_trigger` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_name` varchar(100) NOT NULL,
  `mcuser_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_trigger`),
  KEY `mcuser_id` (`mcuser_id`),
  CONSTRAINT `trigger_events_list_ibfk_2` FOREIGN KEY (`mcuser_id`) REFERENCES `mcuser` (`mcuser_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
