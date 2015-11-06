CREATE TABLE IF NOT EXISTS `trigger_events_relations` (
  `id_trigger` int(11) unsigned NOT NULL,
  `id_relation` int(11) unsigned NOT NULL,
  `id_condition_1` int(11) unsigned NOT NULL,
  `id_condition_2` int(11) unsigned NOT NULL,
  `operator` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_relation`),
  KEY `id_trigger` (`id_trigger`),
  KEY `id_condition_1` (`id_condition_1`),
  KEY `id_condition_2` (`id_condition_2`),
  CONSTRAINT `trigger_events_relations_ibfk_1` FOREIGN KEY (`id_trigger`) REFERENCES `trigger_events_list` (`id_trigger`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `trigger_events_relations_ibfk_2` FOREIGN KEY (`id_condition_1`) REFERENCES `trigger_events_conditions` (`id_condition`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `trigger_events_relations_ibfk_3` FOREIGN KEY (`id_condition_2`) REFERENCES `trigger_events_conditions` (`id_condition`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
