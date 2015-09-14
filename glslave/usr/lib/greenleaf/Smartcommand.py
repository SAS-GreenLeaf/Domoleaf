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

        query = 'SELECT room_device_id, option_id, option_value FROM smartcommand WHERE smartcommand_id ="'+ str(self.smartcmd_id) +'"';
        res = self.sql.mysql_handler_personnal_query(query);

        for r in res:
            obj = {};
            obj['sync'] = 0;
            data = {};
            data['room_device_id'] = r[0];
            data['option_id'] = r[1];
            data['value'] = r[2];
            obj['data'] = data;
            obj['packet_type'] = 'smartcmd_launch';
            self.daemon.send_to_device(obj, connection);
