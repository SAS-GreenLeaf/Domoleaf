SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS protocol;

CREATE TABLE `protocol` (
  `protocol_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `wired` tinyint(4) unsigned DEFAULT '0',
  `scripted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `namede` varchar(64) NOT NULL DEFAULT '',
  `namees` varchar(64) NOT NULL DEFAULT '',
  `namefr` varchar(64) NOT NULL DEFAULT '',
  `nameit` varchar(64) NOT NULL DEFAULT '',
  `specific_daemon` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`protocol_id`),
  KEY `wired` (`wired`),
  KEY `name` (`name`),
  KEY `namede` (`namede`),
  KEY `namees` (`namees`),
  KEY `namefr` (`namefr`),
  KEY `nameit` (`nameit`),
  KEY `specific_daemon` (`specific_daemon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `protocol` (`protocol_id`, `name`, `wired`, `scripted`, `namede`, `namees`, `namefr`, `nameit`, `specific_daemon`)
VALUES
	(1, 'KNX TP', 1, 1, '', '', 'KNX TP', '', 1),
	(2, 'EnOcean', 0, 0, '', '', 'EnOcean', '', 1),
	(3, 'Radio KNX', 0, 0, '', '', 'Radio KNX', '', 0),
	(4, 'CPL KNX', 1, 0, '', '', 'CPL KNX', '', 0),
	(5, 'IP KNX', 1, 0, '', '', 'IP KNX', '', 0),
	(6, 'IP', 1, 0, '', '', 'IP', '', 0),
	(7, 'Infrared', 0, 0, '', '', 'Infra rouge', '', 0),
	(8, 'RS232', 0, 0, '', '', 'RS232', '', 0),
	(9, 'Modbus', 1, 0, '', '', 'Modbus', '', 0);

SET FOREIGN_KEY_CHECKS=1;
