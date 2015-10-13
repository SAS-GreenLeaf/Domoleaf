#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
from threading import Thread;
import time;

LOG_FILE                = '/var/log/glmaster.log'

class Smartcommand:

    def __init__(self, daemon, smartcmd_id = 0):
        self.logger = Logger(True, LOG_FILE);
        self.logger.info('Started SMARTCMD');
        self.smartcmd_id = smartcmd_id;
        self.sql = MasterSql();
        self.daemon = daemon;

    def launch_smartcmd(self, json_obj, connection):
        if (self.smartcmd_id == 0):
            self.logger.error('Invalid Smartcommand');
            return;

        query = 'SELECT room_device_id, option_id, option_value, time_lapse FROM smartcommand_elems WHERE smartcommand_id ="'+ str(self.smartcmd_id) +'" ORDER BY exec_id';
        res = self.sql.mysql_handler_personnal_query(query);
        delay_color = 0;
        for r in res:
            #self.logger.info(r);
            obj = {};
            obj['sync'] = 0;
            data = {};
            data['room_device_id'] = r[0];
            data['option_id'] = r[1];
            data['value'] = r[2];
            obj['data'] = data;
            obj['packet_type'] = 'smartcmd_launch';
            delay = r[3];
            if (data['option_id'] ==  392 or data['option_id'] ==  393 or data['option_id'] ==  394):
                delay_color = delay_color + 1;
            if (delay > 0 and delay_color <= 1):
                time.sleep(delay);
            if (delay_color >= 3):
                delay_color = 0;
            self.daemon.send_to_device(obj, connection);
