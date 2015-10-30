#!/bin/bash

#PREINST
mysql --defaults-file=/etc/mysql/debian.cnf < /etc/domoleaf/sql/preinst.sql

#CONF
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/application.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/device.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/optiondef.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/protocol.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/dpt.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/device_protocol.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/device_option.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/configuration.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/dpt_optiondef.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/product.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/product_option.sql

#INSTALLATION
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/daemon.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/daemon_protocol.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/floor.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/room.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/room_device.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/room_device_option.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/user.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/user_token.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/user_floor.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/user_room.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/user_device.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/smartcommand_list.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/smartcommand_elems.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/trigger_schedules_list.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/trigger_events_list.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/trigger_events_conditions.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/trigger_events_relations.sql

#LOG
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/enocean_log.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/ip_monitor.sql
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/create/knx_log.sql

#POSTINST
mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /etc/domoleaf/sql/postinst.sql
