CREATE TABLE IF NOT EXISTS `floor` (
  `floor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `floor_name` varchar(63) DEFAULT NULL,
  PRIMARY KEY (`floor_id`),
  KEY `name` (`floor_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
