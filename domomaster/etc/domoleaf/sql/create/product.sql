SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS product;

CREATE TABLE `product` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) DEFAULT NULL,
  `manufacturer_id` int(11) unsigned NOT NULL,
  `protocol_id` int(11) unsigned DEFAULT NULL,
  `device_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `name` (`name`),
  KEY `protocol_id` (`protocol_id`),
  KEY `device_id` (`device_id`),
  KEY `manufacturer_id` (`manufacturer_id`),
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`protocol_id`) REFERENCES `protocol` (`protocol_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_3` FOREIGN KEY (`device_id`) REFERENCES `device` (`device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_4` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `product` (`product_id`, `name`, `manufacturer_id`, `protocol_id`, `device_id`)
VALUES
	(1, 'DCS-930L', 1, 6, 2),
	(2, 'FreeBox Player', 15, 6, 16),
	(3, 'LiveBox Player', 16, 6, 16),
	(6, 'DCS-942L', 1, 6, 2),
	(629, 'TV-IP662PI', 2, 6, 2);

SET FOREIGN_KEY_CHECKS=1;
