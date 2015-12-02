CREATE TABLE `daemon` (
  `daemon_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL DEFAULT '',
  `serial` varchar(127) NOT NULL,
  `secretkey` varchar(127) NOT NULL DEFAULT '',
  `validation` tinyint(3) unsigned DEFAULT '0',
  `version` varchar(15) DEFAULT '',
  `wifi_ssid` varchar(63) DEFAULT NULL,
  `wifi_password` varchar(128) DEFAULT NULL,
  `wifi_security` tinyint(3) unsigned DEFAULT '3',
  `wifi_mode` tinyint(3) unsigned DEFAULT '0',
  `wifi_channel` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`daemon_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
