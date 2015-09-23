CREATE TABLE IF NOT EXISTS `smartcommand_elems` (
  `smartcommand_id` int(10) unsigned NOT NULL,
  `exec_id` int(10) unsigned NOT NULL,
  `room_device_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `option_value` varchar(100) NOT NULL,
  `time_lapse` int(10) unsigned NOT NULL,
  KEY `smartcommand_id` (`smartcommand_id`),
  KEY `exec_id` (`exec_id`),
  KEY `room_device_id` (`room_device_id`),
  KEY `option_id` (`option_id`),
  CONSTRAINT `smartcommand_elems_ibfk_1` FOREIGN KEY (`smartcommand_id`) REFERENCES `smartcommand_list` (`smartcommand_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `smartcommand_elems_ibfk_2` FOREIGN KEY (`room_device_id`) REFERENCES `room_device` (`room_device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `smartcommand_elems_ibfk_3` FOREIGN KEY (`option_id`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;