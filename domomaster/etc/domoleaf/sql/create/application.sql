SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS application;

CREATE TABLE `application` (
  `application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(31) NOT NULL DEFAULT '',
  `namede` varchar(31) NOT NULL DEFAULT '',
  `namees` varchar(31) NOT NULL DEFAULT '',
  `namefr` varchar(31) NOT NULL DEFAULT '',
  `nameit` varchar(31) NOT NULL DEFAULT '',
  PRIMARY KEY (`application_id`),
  KEY `name` (`name`),
  KEY `namede` (`namede`),
  KEY `namees` (`namees`),
  KEY `namefr` (`namefr`),
  KEY `nameit` (`nameit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `application` (`application_id`, `name`, `namede`, `namees`, `namefr`, `nameit`)
VALUES
	(1, 'Light', '', '', 'Lumière', ''),
	(2, 'Warming', '', '', 'Chauffage', ''),
	(3, 'Sunblind', '', '', 'Store', ''),
	(4, 'Consumption', '', '', 'Consommation', ''),
	(5, 'Air conditioning', '', '', 'Air conditionné', ''),
	(6, 'Audio/Video', '', '', 'Audio/Vidéo', ''),
	(7, 'Smartcommands', '', '', 'Smartcommands', ''),
	(8, 'Garden', '', '', 'Jardin', ''),
	(10, 'Furnace', '', '', 'Chaudière', ''),
	(11, 'Ventilation', '', '', 'Ventilation', ''),
	(12, 'Spa', '', '', 'Spa', ''),
	(13, 'CCTV', '', '', 'Vidéosurveillance', ''),
	(14, 'Alarm', '', '', 'Alarme', ''),
	(15, 'Glazed', '', '', 'Ouvrants', ''),
	(17, 'Control', '', '', 'Commande', '');

SET FOREIGN_KEY_CHECKS=1;
