#!/bin/bash

#PREINST
mysql --defaults-file=/etc/mysql/debian.cnf < /etc/greenleaf/sql/preinst.sql

#CONF
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/application.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/device.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/optiondef.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/protocol.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/device_protocol.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/device_option.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/configuration.sql

#INSTALLATION
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/daemon.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/daemon_protocol.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/floor.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/room.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/room_device.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/room_device_option.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/user.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/user_token.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/user_floor.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/user_room.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/user_device.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/smartcommand.sql

#LOG
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/enocean_log.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/ip_monitor.sql
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/create/knx_log.sql

#POSTINST
mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /etc/greenleaf/sql/postinst.sql
