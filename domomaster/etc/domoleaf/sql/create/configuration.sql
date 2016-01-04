CREATE TABLE IF NOT EXISTS `configuration` (
  `configuration_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_value` varchar(255) DEFAULT NULL,
  `configuration_description` text,
  PRIMARY KEY (`configuration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT IGNORE INTO configuration (`configuration_id`, `configuration_value`, `configuration_description`)
VALUES
	(1, '', 'Port public HTTP'),
	(2, '', 'Port public SSL (HTTPS+SSH)'),
	(3, '0', 'Force HTTPS'),
	(4, '', 'Masters version'),
	(5, '', 'From email'),
	(6, '', 'From Name'),
	(7, '', 'SMTP Host'),
	(8, '0', 'SMTP Secure'),
	(9, '25', 'SMTP Port'),
	(10, '', 'SMTP Username'),
	(11, '', 'SMTP Password'),
	(12, '', 'Password reset key'),
	(13, '', 'Last Available Version'),
	(14, '0', 'High cost'),
	(15, '0', 'Low cost'),
	(16, '0-0', 'Low field 1'),
	(17, '0-0', 'Low field 2'),
	(18, '1', 'Currency');
