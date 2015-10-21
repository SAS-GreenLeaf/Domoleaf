SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS dpt_optiondef;

CREATE TABLE `dpt_optiondef` (
  `option_id` int(10) unsigned NOT NULL,
  `dpt_id` int(11) unsigned NOT NULL,
  KEY `dpt_id` (`dpt_id`),
  KEY `option_id` (`option_id`),
  CONSTRAINT `dpt_optiondef_ibfk_1` FOREIGN KEY (`dpt_id`) REFERENCES `dpt` (`dpt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dpt_optiondef_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `dpt_optiondef` (`option_id`, `dpt_id`)
VALUES
	(12, 2),
	(13, 51),
	(54, 2),
	(72, 73),
	(72, 74),
	(72, 90),
	(79, 76),
	(79, 152),
	(79, 153),
	(96, 2),
	(355, 467),
	(356, 467),
	(357, 467),
	(358, 467),
	(359, 467),
	(360, 467),
	(361, 467),
	(363, 468),
	(364, 468),
	(365, 468),
	(366, 468),
	(367, 468),
	(368, 468),
	(383, 468),
	(388, 73),
	(388, 74),
	(388, 90),
	(392, 51),
	(393, 51),
	(394, 51),
	(399, 106),
	(400, 2),
	(401, 2),
	(402, 2),
	(403, 2),
	(404, 2),
	(405, 2),
	(406, 2),
	(400, 51),
	(401, 51),
	(402, 51),
	(403, 51),
	(404, 51),
	(405, 51),
	(406, 51);

SET FOREIGN_KEY_CHECKS=1;
