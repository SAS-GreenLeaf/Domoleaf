CREATE TABLE IF NOT EXISTS `ip_monitor` (
  `mac_addr` varchar(32) DEFAULT NULL,
  `ip_addr` varchar(32) DEFAULT NULL,
  `hostname` varchar(128) DEFAULT NULL,
  `last_update` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `mac_addr` (`mac_addr`),
  KEY `hostname` (`hostname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
