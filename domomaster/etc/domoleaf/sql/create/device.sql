SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS device;

CREATE TABLE `device` (
  `device_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL DEFAULT '',
  `protocol_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned DEFAULT NULL,
  `namede` varchar(63) NOT NULL DEFAULT '',
  `namees` varchar(63) NOT NULL DEFAULT '',
  `namefr` varchar(63) NOT NULL DEFAULT '',
  `nameit` varchar(63) NOT NULL DEFAULT '',
  PRIMARY KEY (`device_id`),
  KEY `application_id` (`application_id`),
  KEY `protocol_id` (`protocol_id`),
  KEY `namede` (`namede`),
  KEY `namees` (`namees`),
  KEY `namefr` (`namefr`),
  KEY `nameit` (`nameit`),
  KEY `name` (`name`),
  CONSTRAINT `device_ibfk_1` FOREIGN KEY (`protocol_id`) REFERENCES `protocol` (`protocol_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `device_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `application` (`application_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `device` (`device_id`, `name`, `protocol_id`, `application_id`, `namede`, `namees`, `namefr`, `nameit`)
VALUES
	(2, 'Camera', 6, 13, '', '', 'Caméra', ''),
	(4, 'Lamp', 1, 1, '', '', 'Lampe', ''),
	(5, 'Sensor', 1, 17, '', '', 'Capteur', ''),
	(7, 'Heating system', 1, 2, '', '', 'Chauffage', ''),
	(9, 'Heating floor', 1, 2, '', '', 'Plancher chauffant', ''),
	(10, 'Rolling shutter', 1, 3, '', '', 'Volet roulant', ''),
	(11, 'Store banne', 1, 3, '', '', 'Store banne', ''),
	(15, 'CD player', 6, 6, '', '', 'Lecteur CD', ''),
	(16, 'Television', 6, 6, '', '', 'Télévision', ''),
	(17, 'Amplifier', 6, 6, '', '', 'Ampli', ''),
	(18, 'Watering pump', 1, 8, '', '', 'Pompe d\'arrosage', ''),
	(20, 'Boiler', 1, 10, '', '', 'Chaudière', ''),
	(22, 'Pool motor', 1, 8, '', '', 'Moteur de piscine', ''),
	(23, 'Pool heating', 1, 8, '', '', 'Réchauffeur de piscine', ''),
	(24, 'Heat pump', 1, 2, '', '', 'Pompe à chaleur', ''),
	(25, 'CMV fan', 1, 11, '', '', 'Ventilateur de VMC', ''),
	(28, 'Heating SPA', 1, 12, '', '', 'Réchauffeur de SPA', ''),
	(30, 'Siren', 1, 14, '', '', 'Sirène', ''),
	(31, 'Portal', 1, 15, '', '', 'Portail', ''),
	(33, 'Water heater', 1, 10, '', '', 'Chauffe eau', ''),
	(38, 'Switch', 1, 17, '', '', 'Interrupteur', ''),
	(43, 'Motion sensor', 1, 17, '', '', 'Détecteur de mouvements', ''),
	(47, 'Electricity meter', 1, 4, '', '', 'Compteur électrique', ''),
	(49, 'Thermostat', 1, 2, '', '', 'Thermostat', ''),
	(50, 'Network drive', 6, 6, '', '', 'Lecteur réseau', ''),
	(51, 'Binary input', 1, 17, '', '', 'Entrée Binaire', ''),
	(52, 'Sliding door', 1, 15, '', '', 'Baie Coulissante', ''),
	(54, 'Porte', 1, 15, '', '', 'Porte', ''),
	(60, 'Brightness sensor', 1, 17, '', '', 'Détecteur de luminosité', ''),
	(61, 'Weather station', 1, 17, '', '', 'Station Météo', ''),
	(68, 'Climatisation', 1, 5, '', '', 'Climatisation', ''),
	(85, 'Controleur RGBW', 1, 1, '', '', 'Controleur RGBW', ''),
	(86, 'Generic', 1, 17, '', '', 'Generique', '');

SET FOREIGN_KEY_CHECKS=1;
