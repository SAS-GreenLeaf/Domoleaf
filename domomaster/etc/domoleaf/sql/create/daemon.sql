CREATE TABLE IF NOT EXISTS `daemon` (
  `daemon_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL DEFAULT '',
  `serial` varchar(127) NOT NULL,
  `secretkey` varchar(127) NOT NULL DEFAULT '',
  `validation` tinyint(3) unsigned DEFAULT '0',
  `version` varchar(15) DEFAULT '',
  PRIMARY KEY (`daemon_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
