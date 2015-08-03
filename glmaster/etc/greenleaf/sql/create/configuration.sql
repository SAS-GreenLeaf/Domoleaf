CREATE TABLE IF NOT EXISTS `configuration` (
  `configuration_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_value` varchar(255) DEFAULT NULL,
  `configuration_description` text,
  PRIMARY KEY (`configuration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
