CREATE TABLE IF NOT EXISTS `user_token` (
  `token` varchar(64) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL,
  `lastupdate` bigint(20) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
