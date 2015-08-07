ALTER TABLE `floor` ADD `floor_background_url` VARCHAR(255) NULL ;
ALTER TABLE `room_device` ADD `pos_x_icon` VARCHAR(10) NOT NULL DEFAULT '0/0' , ADD `pos_y_icon` VARCHAR(10) NOT NULL DEFAULT '0/0' ;