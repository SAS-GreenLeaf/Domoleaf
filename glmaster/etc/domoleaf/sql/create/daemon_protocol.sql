CREATE TABLE IF NOT EXISTS `daemon_protocol` (
  `daemon_id` int(10) unsigned NOT NULL,
  `protocol_id` int(10) unsigned NOT NULL,
  KEY `daemon_id` (`daemon_id`),
  KEY `protocol_id` (`protocol_id`),
  CONSTRAINT `daemon_protocol_ibfk_1` FOREIGN KEY (`daemon_id`) REFERENCES `daemon` (`daemon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `daemon_protocol_ibfk_2` FOREIGN KEY (`protocol_id`) REFERENCES `protocol` (`protocol_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
