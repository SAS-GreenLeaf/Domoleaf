CREATE TABLE IF NOT EXISTS `mcuser_device` (
  `mcuser_id` int(10) unsigned NOT NULL,
  `room_device_id` int(10) unsigned NOT NULL,
  `device_allowed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `device_order` int(10) unsigned NOT NULL DEFAULT '0',
  `device_bgimg` varchar(255) NOT NULL DEFAULT '',
  `device_posx` int(10) unsigned NOT NULL DEFAULT '0',
  `device_posy` int(10) unsigned NOT NULL DEFAULT '0',
  `device_counter` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `mcuser_id` (`mcuser_id`),
  KEY `room_device_id` (`room_device_id`),
  KEY `device_counter` (`device_counter`),
  CONSTRAINT `mcuser_device_ibfk_1` FOREIGN KEY (`mcuser_id`) REFERENCES `mcuser` (`mcuser_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mcuser_device_ibfk_2` FOREIGN KEY (`room_device_id`) REFERENCES `room_device` (`room_device_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
