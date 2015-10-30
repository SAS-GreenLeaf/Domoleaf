SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS product_option;

CREATE TABLE `product_option` (
  `product_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL DEFAULT '0',
  `addr` varchar(255) DEFAULT NULL,
  `dpt_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`option_id`),
  KEY `option_id` (`option_id`),
  KEY `dpt_id` (`dpt_id`),
  CONSTRAINT `product_option_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_option_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `optiondef` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_option_ibfk_3` FOREIGN KEY (`dpt_id`) REFERENCES `dpt` (`dpt_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `product_option` (`product_id`, `option_id`, `addr`, `dpt_id`)
VALUES
	(1, 355, '/image.jpg', 467),
	(1, 356, '/video/mjpg.cgi', 467),
	(6, 356, '/video/mjpg.cgi', 467),
	(629, 356, '/video/mjpg.cgi', 467),
	(629, 357, '/cgi/ptdc.cgi?command=set_relative_pos&posX=0&posY=3', 467),
	(629, 358, '/cgi/ptdc.cgi?command=set_relative_pos&posX=0&posY=-3', 467),
	(629, 359, '/cgi/ptdc.cgi?command=set_relative_pos&posX=-3&posY=0', 467),
	(629, 360, '/cgi/ptdc.cgi?command=set_relative_pos&posX=3&posY=0', 467),
	(629, 361, '/cgi/ptdc.cgi?command=go_home', 467);

SET FOREIGN_KEY_CHECKS=1;
