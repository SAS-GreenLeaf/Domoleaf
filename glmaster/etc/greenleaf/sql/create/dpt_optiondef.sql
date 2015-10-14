SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS dpt_optiondef;

CREATE TABLE `dpt_optiondef` (
  `dpt_id` int(11) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  KEY `dpt_id` (`dpt_id`),
  KEY `option_id` (`option_id`),
  CONSTRAINT `dpt_optiondef_ibfk_1` FOREIGN KEY (`dpt_id`) REFERENCES `dpt` (`dpt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dpt_optiondef_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dpt_optiondef` (`dpt_id`, `option_id`)
VALUES
	(2, 12),
	(51, 13),
	(2, 54),
	(73, 72),
	(74, 72),
	(90, 72),
	(76, 79),
	(152, 79),
	(153, 79),
	(2, 96),
	(467, 355),
	(467, 356),
	(467, 357),
	(467, 358),
	(467, 359),
	(467, 360),
	(467, 361),
	(468, 363),
	(468, 364),
	(468, 365),
	(468, 366),
	(468, 367),
	(468, 368),
	(468, 383),
	(76, 388),
	(152, 388),
	(153, 388),
	(51, 392),
	(51, 393),
	(51, 394),
	(91, 399);

SET FOREIGN_KEY_CHECKS=1;
