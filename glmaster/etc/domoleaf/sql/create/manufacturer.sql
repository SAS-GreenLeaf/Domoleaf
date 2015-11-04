SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS manufacturer;

CREATE TABLE `manufacturer` (
  `manufacturer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL DEFAULT '',
  PRIMARY KEY (`manufacturer_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `manufacturer` (`manufacturer_id`, `name`)
VALUES
	(1, 'D-Link'),
	(2, 'TRENDnet'),
	(3, 'Sonos');

SET FOREIGN_KEY_CHECKS=1;
