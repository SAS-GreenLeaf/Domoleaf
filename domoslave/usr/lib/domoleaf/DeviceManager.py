#!/usr/bin/python3

import sys;
sys.path.append('/usr/lib/domoleaf');
from MysqlHandler import *;
from DaemonConfigParser import *;

LOAD_DEVICE_QUERY = "SELECT protocol_id, device_id, room_device.daemon_id, addr, plus1, plus2, plus3 FROM room_device JOIN daemon ON room_device.daemon_id=daemon.daemon_id WHERE room_device_id = ";

LOAD_DEVICE_QUERY_IP = "SELECT protocol_id, device_id, daemon_id, addr, plus1, plus2, plus3 FROM room_device WHERE room_device_id = ";

CHECK_ROOM_DEVICE_OPTIONS = 'SELECT option_id, addr FROM room_device_option WHERE room_device_id = ';

GET_DAEMON_FROM_ID = 'SELECT daemon_id, name, serial, secretkey FROM daemon WHERE daemon_id = ';

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

class DeviceManager:
    """
    Device management class. Manages the device described with the ID passed at construction.
    """
    def __init__(self, _id_elem = 0, _option_id = 0, _debug = False):
        self.logger = Logger(LOG_FLAG, LOG_FILE);
        self._id = _id_elem;
        self._debug = _debug;
        self._option_id = _option_id;
        self._parser = DaemonConfigParser(CONF_FILENAME);
        self._db_name = self._parser.getValueFromSection(MYSQL_CONF_SECTION, MYSQL_CONF_USER_ENTRY);
        self._db_passwd = self._parser.getValueFromSection(MYSQL_CONF_SECTION, MYSQL_CONF_PASSWORD_ENTRY);
        self._db_dbname = self._parser.getValueFromSection(MYSQL_CONF_SECTION, MYSQL_CONF_DATABASE_ENTRY);

    ###############################################################
    # Va surement revoir cette fonction et remettre le mode debug #
    # suivant les options qu'il va falloir check ou pas           #
    ###############################################################
    def load_from_db(self):
        """
        Returns the device from the database.
        """
        if self._db_name is None:
            self.logger.error("[ DeviceManager ]: Mysql username not found in '" + CONF_FILENAME + "'");
            return None;
        if self._db_passwd is None:
            self.logger.error("[ DeviceManager ]: Mysql password not found in '" + CONF_FILENAME + "'");
            return None;
        if self._db_dbname is None:
            self.logger.error("[ DeviceManager ]: Mysql database name not found in '" + CONF_FILENAME + "'");
            return None;
        db = MysqlHandler(self._db_name, self._db_passwd, self._db_dbname);
        res = db.personnal_query(LOAD_DEVICE_QUERY + str(self._id));
        if len(res) == 0:
            res = db.personnal_query(LOAD_DEVICE_QUERY_IP + str(self._id));
        if len(res) == 0:
            self.logger.error('[ DeviceManager ]: Error: No device with id ' + str(self._id) + ' in database.');
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
        db.close();
        db = MysqlHandler(self._db_name, self._db_passwd, self._db_dbname);
        res = db.personnal_query(CHECK_ROOM_DEVICE_OPTIONS + str(self._id));
        db.close();
        if len(res) == 0:
            self.logger.error('[ DeviceManager ]: Error: No room_device_option for room_device_id \'' + str(self._id) + '\'');
            device['option_id'] = self._option_id;
            if device['protocol_id'] != IP_ID:
                db = MysqlHandler(self._db_name, self._db_passwd, self._db_dbname);
                res = db.personnal_query(GET_DAEMON_FROM_ID + str(device['daemon_id']));
                device['daemon_name'] = res[0][2];
                device['daemon_secretkey'] = res[0][3];
            db.close();
            return device;
        device['addr_dst'] = 0;
        for d in res:
            if d[0] == self._option_id:
                device['addr_dst'] = d[1];
                break;
        device['option_id'] = self._option_id;
        if device['protocol_id'] != IP_ID:
            db = MysqlHandler(self._db_name, self._db_passwd, self._db_dbname);
            res = db.personnal_query(GET_DAEMON_FROM_ID + str(device['daemon_id']));
            device['daemon_name'] = res[0][2];
            device['daemon_secretkey'] = res[0][3];
        db.close();
        return device;
