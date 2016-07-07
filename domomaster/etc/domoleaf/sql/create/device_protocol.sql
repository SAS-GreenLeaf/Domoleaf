SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS device_protocol;

CREATE TABLE `device_protocol` (
  `device_id` int(10) unsigned NOT NULL DEFAULT '0',
  `protocol_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `device_id` (`device_id`,`protocol_id`),
  KEY `protocol_id` (`protocol_id`),
  CONSTRAINT `device_protocol_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `device` (`device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `device_protocol_ibfk_2` FOREIGN KEY (`protocol_id`) REFERENCES `protocol` (`protocol_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `device_protocol` (`device_id`, `protocol_id`)
VALUES
	(2, 6),
	(4, 1),
	(4, 3),
	(5, 1),
	(5, 2),
	(7, 1),
	(9, 1),
	(10, 1),
	(10, 3),
	(11, 1),
	(15, 6),
	(17, 6),
	(16, 1),
	(16, 6),
	(18, 1),
	(20, 1),
	(22, 1),
	(23, 1),
	(24, 1),
	(25, 1),
	(28, 1),
	(30, 1),
	(30, 6),
	(31, 1),
	(33, 1),
	(35, 6),
	(37, 6),
	(38, 1),
	(38, 2),
	(38, 3),
	(43, 1),
	(47, 1),
	(49, 1),
	(50, 1),
	(50, 6),
	(51, 1),
	(52, 1),
	(54, 1),
	(60, 1),
	(61, 1),
	(68, 1),
	(85, 1),
	(86, 1);

SET FOREIGN_KEY_CHECKS=1;
