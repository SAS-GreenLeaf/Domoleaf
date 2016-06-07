#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
from DaemonConfigParser import *;
from threading import Thread;
import time;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'
LOG_FLAG        = False;

class Smartcommand(Thread):

    def __init__(self, daemon, smartcmd_id = 0):
        Thread.__init__(self);
        self.logger = Logger(LOG_FLAG, LOG_FILE);
        self.logger.debug('Started SMARTCMD');
        self.smartcmd_id = smartcmd_id;
        self.sql = MasterSql();
        self.daemon = daemon;
        self.tab_except_http = [356, 357, 358, 359, 360, 361];
        self.db_username = daemon.db_username;
        self.db_passwd = daemon.db_passwd;
        self.db_dbname = daemon.db_dbname;

    def setValues(self, connection):
        self.connection = connection;

    def run(self):
        self.db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        launch_smartcommand(self);
        self.db.close();

def launch_smartcommand(self):
    if not self.smartcmd_id:
        self.logger.error('Invalid Smartcommand');
        return;
    query = ('SELECT smartcommand_elems.room_device_id, option_value, smartcommand_elems.option_id, time_lapse, opt_value, device_id '+
        'FROM smartcommand_elems '+
        'JOIN room_device_option '+
        'ON room_device_option.room_device_id=smartcommand_elems.room_device_id '+
        'AND room_device_option.option_id=smartcommand_elems.option_id '+
        'JOIN room_device ON room_device.room_device_id=smartcommand_elems.room_device_id '+
        'WHERE smartcommand_id ="'+str(self.smartcmd_id)+'" ORDER BY exec_id')
    res = self.sql.mysql_handler_personnal_query(query, self.db);
    delay_color = 0;
    for r in res:
        obj = {};
        obj['sync'] = 0;
        data = {};
        data['room_device_id'] = r[0];
        data['value'] = r[1];
        data['option_id'] = r[2];
        data['action'] = r[1];
        if r[5] == 86:
            data['value'] = r[4]
        elif data['option_id'] in self.tab_except_http:
            data['value'] = '';
        obj['data'] = data;
        obj['packet_type'] = 'smartcmd_launch';
        delay = r[3];
        if (data['option_id'] ==  392 or data['option_id'] ==  393 or data['option_id'] ==  394):
            delay_color += 1;
        if (delay > 0 and delay_color <= 1):
            time.sleep(delay);
        if (delay_color >= 3):
            delay_color = 0;
        self.daemon.send_to_device(obj, self.connection, self.db);
