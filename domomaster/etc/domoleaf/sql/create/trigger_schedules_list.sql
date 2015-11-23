CREATE TABLE IF NOT EXISTS `trigger_schedules_list` (
  `id_schedule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_name` varchar(100) NOT NULL DEFAULT '',
  `mcuser_id` int(10) unsigned NOT NULL,
  `months` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `weekdays` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `days` int(10) unsigned NOT NULL DEFAULT '0',
  `hours` int(10) unsigned NOT NULL DEFAULT '0',
  `mins` varchar(60) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_schedule`),
  KEY `mcuser_id` (`mcuser_id`),
  CONSTRAINT `trigger_schedules_list_ibfk_1` FOREIGN KEY (`mcuser_id`) REFERENCES `mcuser` (`mcuser_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
