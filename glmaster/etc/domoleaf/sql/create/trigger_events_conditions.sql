CREATE TABLE IF NOT EXISTS `trigger_events_conditions` (
  `id_trigger` int(11) unsigned NOT NULL,
  `id_condition` int(11) unsigned NOT NULL,
  `room_device_id` int(11) unsigned NOT NULL,
  `id_option` int(11) unsigned NOT NULL,
  `operator` int(11) unsigned NOT NULL,
  `value` varchar(100) NOT NULL DEFAULT '',
  KEY `id_trigger` (`id_trigger`),
  KEY `room_device_id` (`room_device_id`),
  KEY `id_option` (`id_option`),
  KEY `id_condition` (`id_condition`),
  CONSTRAINT `trigger_events_conditions_ibfk_1` FOREIGN KEY (`id_trigger`) REFERENCES `trigger_events_list` (`id_trigger`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `trigger_events_conditions_ibfk_2` FOREIGN KEY (`room_device_id`) REFERENCES `room_device` (`room_device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `trigger_events_conditions_ibfk_3` FOREIGN KEY (`id_option`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
