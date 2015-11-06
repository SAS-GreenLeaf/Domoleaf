CREATE TABLE IF NOT EXISTS `knx_log` (
  `type` tinyint(3) unsigned NOT NULL,
  `addr_src` varchar(12) NOT NULL DEFAULT '',
  `addr_dest` varchar(12) NOT NULL DEFAULT '',
  `knx_value` int(10) unsigned NOT NULL,
  `t_date` bigint(20) unsigned NOT NULL,
  `daemon_id` int(10) unsigned DEFAULT NULL,
  KEY `t_date` (`t_date`),
  KEY `daemon_id` (`daemon_id`),
  CONSTRAINT `knx_log_ibfk_1` FOREIGN KEY (`daemon_id`) REFERENCES `daemon` (`daemon_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
