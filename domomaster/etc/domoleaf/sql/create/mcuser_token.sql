CREATE TABLE IF NOT EXISTS `mcuser_token` (
  `token` varchar(64) NOT NULL DEFAULT '',
  `mcuser_id` int(10) unsigned NOT NULL,
  `lastupdate` bigint(20) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `token` (`token`),
  KEY `mcuser_id` (`mcuser_id`),
  CONSTRAINT `mcuser_token_ibfk_1` FOREIGN KEY (`mcuser_id`) REFERENCES `mcuser` (`mcuser_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
