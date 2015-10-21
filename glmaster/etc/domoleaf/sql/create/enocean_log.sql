CREATE TABLE IF NOT EXISTS `enocean_log` (
  `type` tinyint(3) unsigned NOT NULL,
  `addr_src` varchar(15) NOT NULL DEFAULT '',
  `addr_dest` varchar(15) NOT NULL DEFAULT '',
  `eo_value` bigint(20) unsigned DEFAULT NULL,
  `t_date` bigint(20) unsigned DEFAULT NULL,
  `daemon_id` int(11) unsigned DEFAULT NULL,
  KEY `t_date` (`t_date`),
  KEY `daemon_id` (`daemon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
