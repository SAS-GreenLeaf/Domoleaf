SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS product_option;

CREATE TABLE `product_option` (
  `product_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL DEFAULT '0',
  `addr` varchar(255) DEFAULT NULL,
  `addr_plus` varchar(255) DEFAULT NULL,
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
	(2, 12, '/pub/remote_control?code=[-password-]&key=power', 467),
	(2, 363, '/pub/remote_control?code=[-password-]&key=play', 467),
	(2, 364, '/pub/remote_control?code=[-password-]&key=play', 467),
	(2, 368, '/pub/remote_control?code=[-password-]&key=mute', 467),
	(2, 444, '/pub/remote_control?code=[-password-]&key=0', 467),
	(2, 445, '/pub/remote_control?code=[-password-]&key=1', 467),
	(2, 446, '/pub/remote_control?code=[-password-]&key=2', 467),
	(2, 447, '/pub/remote_control?code=[-password-]&key=3', 467),
	(2, 448, '/pub/remote_control?code=[-password-]&key=4', 467),
	(2, 449, '/pub/remote_control?code=[-password-]&key=5', 467),
	(2, 450, '/pub/remote_control?code=[-password-]&key=6', 467),
	(2, 451, '/pub/remote_control?code=[-password-]&key=7', 467),
	(2, 452, '/pub/remote_control?code=[-password-]&key=8', 467),
	(2, 453, '/pub/remote_control?code=[-password-]&key=9', 467),
	(2, 454, '/pub/remote_control?code=[-password-]&key=tv', 467),
	(2, 455, '/pub/remote_control?code=[-password-]&key=red', 467),
	(2, 456, '/pub/remote_control?code=[-password-]&key=green', 467),
	(2, 457, '/pub/remote_control?code=[-password-]&key=blue', 467),
	(2, 458, '/pub/remote_control?code=[-password-]&key=yellow', 467),
	(2, 459, '/pub/remote_control?code=[-password-]&key=up', 467),
	(2, 460, '/pub/remote_control?code=[-password-]&key=down', 467),
	(2, 461, '/pub/remote_control?code=[-password-]&key=left', 467),
	(2, 462, '/pub/remote_control?code=[-password-]&key=right', 467),
	(2, 463, '/pub/remote_control?code=[-password-]&key=vol_inc', 467),
	(2, 464, '/pub/remote_control?code=[-password-]&key=vol_dec', 467),
	(2, 465, '/pub/remote_control?code=[-password-]&key=prgm_inc', 467),
	(2, 466, '/pub/remote_control?code=[-password-]&key=prgm_dec', 467),
	(2, 467, '/pub/remote_control?code=[-password-]&key=rec', 467),
	(2, 468, '/pub/remote_control?code=[-password-]&key=bwd', 467),
	(2, 469, '/pub/remote_control?code=[-password-]&key=fwd', 467),
	(2, 470, '/pub/remote_control?code=[-password-]&key=ok', 467),
	(6, 356, '/video/mjpg.cgi', 467),
	(629, 356, '/video/mjpg.cgi', 467),
	(629, 357, '/cgi/ptdc.cgi?command=set_relative_pos&posX=0&posY=3', 467),
	(629, 358, '/cgi/ptdc.cgi?command=set_relative_pos&posX=0&posY=-3', 467),
	(629, 359, '/cgi/ptdc.cgi?command=set_relative_pos&posX=-3&posY=0', 467),
	(629, 360, '/cgi/ptdc.cgi?command=set_relative_pos&posX=3&posY=0', 467),
	(629, 361, '/cgi/ptdc.cgi?command=go_home', 467);

SET FOREIGN_KEY_CHECKS=1;
