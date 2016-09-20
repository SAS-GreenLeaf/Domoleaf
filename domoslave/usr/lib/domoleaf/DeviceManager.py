#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import sys;
sys.path.append('/usr/lib/domoleaf');
from MysqlHandler import *;
from MasterSql import *;
from DaemonConfigParser import *;

LOAD_DEVICE_QUERY = ('SELECT protocol_id, device_id, room_device.daemon_id, '
                     'addr, plus1, plus2, plus3 '
                     'FROM room_device '
                     'JOIN daemon ON room_device.daemon_id=daemon.daemon_id '
                     'WHERE room_device_id = ');

LOAD_DEVICE_QUERY_IP = ('SELECT protocol_id, device_id, daemon_id, '
                        'addr, plus1, plus2, plus3 FROM room_device '
                        'WHERE room_device_id = ');

CHECK_ROOM_DEVICE_OPTIONS = ('SELECT room_device_option.option_id, addr, dpt_optiondef.dpt_id, dpt_optiondef.function_writing '
                             'FROM room_device_option '
                             'JOIN dpt_optiondef '
                             'ON room_device_option.dpt_id = dpt_optiondef.dpt_id '
                             'AND room_device_option.option_id = dpt_optiondef.option_id '
                             'AND protocol_id = ');

GET_DAEMON_FROM_ID = ('SELECT daemon_id, name, serial, secretkey '
                      'FROM daemon '
                      'WHERE daemon_id = ');

KNX_ID = 1;
IP_ID = 6;

OPTION_ON_OFF = 12;
OPTION_VAR = 13;
OPTION_UP_DOWN = 54;
OPTION_OPEN_CLOSE = 96;
OPTION_COLOR_R = 392;
OPTION_COLOR_G = 393;
OPTION_COLOR_B = 394;

KEY_PROTOCOL_ID = 'protocol_id';
KEY_DEVICE_ID = 'device_id';
KEY_DAEMON_ID = 'daemon_id';
KEY_ADDR = 'addr';
KEY_PLUS_1 = 'plus1';
KEY_PLUS_2 = 'plus2';
KEY_PLUS_3 = 'plus3';
KEY_ROOM_DEVICE_ID = 'room_device_id';

CONF_FILENAME = "/etc/domoleaf/master.conf";

MYSQL_CONF_SECTION = 'mysql';
MYSQL_CONF_USER_ENTRY = 'user';
MYSQL_CONF_PASSWORD_ENTRY = 'password';
MYSQL_CONF_DATABASE_ENTRY = 'database_name';

LOG_FILE        = '/var/log/domoleaf/domoslave.log';
LOG_FLAG        = True;

## Device management class. Manages the device described with the ID passed at construction.
class DeviceManager:

    ## The constructor.
    #
    # @param _id_elem ID of the device (default 0).
    # @param _option_id Option ID of the device (default 0)
    # @param _debug Debugging flag (default False)
    def __init__(self, _id_elem = 0, _option_id = 0, _debug = False):
        ## Instance of the logger object for formatting and printing
        self.logger = Logger(LOG_FLAG, LOG_FILE);
        self._id = _id_elem;
        self._option_id = _option_id;
        self._debug = _debug;


    ## Returns the device from the database.
    #
    # @param db The database handler.
    #
    # @return The device if no error.
    def load_from_db(self, db):
        res = db.personnal_query(LOAD_DEVICE_QUERY + str(self._id));
        if not len(res):
            res = db.personnal_query(LOAD_DEVICE_QUERY_IP + str(self._id));
        if not len(res):
            self.logger.error('[ DeviceManager ]: Error: No device with id '+str(self._id)+' in database.');
            return None;
        elif len(res) > 1:
            self.logger.error('[ DeviceManager ]: Dunno wut to do if more than one item in DB.');
            return None;
        obj = res[0];
        device = {
            KEY_PROTOCOL_ID: obj[0],
            KEY_DEVICE_ID: obj[1],
            KEY_DAEMON_ID: obj[2],
            KEY_ADDR: obj[3],
            KEY_PLUS_1: obj[4],
            KEY_PLUS_2: obj[5],
            KEY_PLUS_3: obj[6],
            KEY_ROOM_DEVICE_ID: self._id
        };
        query = CHECK_ROOM_DEVICE_OPTIONS + str(device[KEY_PROTOCOL_ID]) + ' WHERE room_device_id = ' + str(self._id);
        res = db.personnal_query(query);
        if not len(res):
            self.logger.error('[ DeviceManager ]: Error: No room_device_option for room_device_id \'' + str(self._id) + '\'');
            device['option_id'] = self._option_id;
            device['function_writing'] = 0;
            device['dpt_id'] = 0;
            if device['protocol_id'] != IP_ID:
                res = db.personnal_query(GET_DAEMON_FROM_ID + str(device['daemon_id']));
                device['daemon_name'] = res[0][2];
                device['daemon_secretkey'] = res[0][3];
            return device;
        device['addr_dst'] = 0;
        for d in res:
            if d[0] == self._option_id:
                device['addr_dst'] = d[1];
                device['function_writing'] = d[3];
                device['dpt_id'] = d[2];
                break;
        device['option_id'] = self._option_id;
        if device['protocol_id'] != IP_ID:
            res = db.personnal_query(GET_DAEMON_FROM_ID + str(device['daemon_id']));
            device['daemon_name'] = res[0][2];
            device['daemon_secretkey'] = res[0][3];
        return device;
