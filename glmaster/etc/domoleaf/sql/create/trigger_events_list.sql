CREATE TABLE IF NOT EXISTS `trigger_events_list` (
  `id_trigger` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trigger_name` varchar(100) NOT NULL,
  `id_smartcmd` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `activated` tinyint(4) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_trigger`),
  KEY `id_smartcmd` (`id_smartcmd`),
  KEY `user_id` (`user_id`),
  KEY `activated` (`activated`),
  CONSTRAINT `trigger_events_list_ibfk_1` FOREIGN KEY (`id_smartcmd`) REFERENCES `smartcommand_list` (`smartcommand_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `trigger_events_list_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
