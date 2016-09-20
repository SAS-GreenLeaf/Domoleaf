#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

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

## Class representing multiple commands launched at the same time
class Smartcommand(Thread):

    ## The constructor.
    #
    # @param daemon The daemon object which instanciated this class.
    # @param smartcmd_id ID of a smartcommand (default 0).
    def __init__(self, daemon, smartcmd_id = 0):
        Thread.__init__(self);
        ## Logger object for formatting and printing logs
        self.logger = Logger(LOG_FLAG, LOG_FILE);
        self.logger.debug('Started SMARTCMD');
        ## The ID of the smart command
        self.smartcmd_id = smartcmd_id;
        ## SQL object for managing database
        self.sql = MasterSql();
        ## Instance of the slave daemon
        self.daemon = daemon;
        ## Array of option code for which the data is not important
        self.tab_except_http = [356, 357, 358, 359, 360, 361];
        ## Username to connect to the database
        self.db_username = daemon.db_username;
        ## Password to connect to the database
        self.db_passwd = daemon.db_passwd;
        ## Database name on which connect
        self.db_dbname = daemon.db_dbname;

    ## Setter for the connection.
    #
    # @param connection The connection object to set.
    def setValues(self, connection):
        ## Connection object
        self.connection = connection;

    ## Runs the smart command.
    def run(self):
        ## Database handler for querying database
        self.db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        launch_smartcommand(self);
        self.db.close();

## Selects the smart command in database and runs it with the good parameters.
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
