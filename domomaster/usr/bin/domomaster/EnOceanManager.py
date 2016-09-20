## @package domomaster
# Master daemon for D3 boxes.
#
# Developed by GreenLeaf.

import logging;
from Logger import *;
import sys;
sys.path.append('/usr/lib/domoleaf');
from MasterSql import *;
from DaemonConfigParser import *;

## Const variable containing the path of the log file
LOG_FILE                = '/var/log/domoleaf/domomaster.log';

## Flag saying whether the debug is enabled
DEBUG_MODE = False;

## Class managing the EnOcean protocol in D3 boxes.
class EnOceanManager:

    ## The constructor.
    #
    # @param slave_keys The aes keys of the slaves.
    def __init__(self, slave_keys):
        ## Logger object for formatting and printing
        self.logger = Logger(DEBUG_MODE, LOG_FILE);

        ## SQL manager for the master daemon
        self.sql = MasterSql();
        self._parser = DaemonConfigParser('/etc/domoleaf/master.conf');

        ## Object containing the AES keys for encrypted communications
        self.aes_slave_keys = slave_keys;

        ## Array containing functions associated with IDs
        self.functions_transform = {
              0: utils.convert_none,
              4: utils.eno_onoff
        };

    ## Updates the table room_device_option with EnOcean values.
    #
    # @param daemon_id The ID of the daemon.
    # @param json_obj JSON object containing the source address of the EnOcean device.
    # @param db The database handler.
    #
    # @return The result of the query.
    def update_room_device_option(self, daemon_id, json_obj, db):
        query = ''.join(["SELECT room_device_option.option_id, room_device.room_device_id, addr_plus, function_answer, room_device_option.dpt_id ",
              "FROM room_device_option JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id ",
              "JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND ",
              "dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id ",
              "WHERE daemon_id=", str(daemon_id), " AND room_device_option.addr=\"", str(json_obj['src_addr']), "\""]);
        res = self.sql.mysql_handler_personnal_query(query, db);
        result = []
        append = result.append
        if not res:
            query = ''.join(["SELECT room_device_option.option_id, room_device.room_device_id, addr_plus, function_answer, room_device_option.dpt_id ",
                  "FROM room_device_option JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id ",
                  "JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND ",
                  "dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id ",
                  "WHERE daemon_id=", str(daemon_id), " AND  room_device_option.addr_plus=\"", str(json_obj['src_addr']), "\""]);
            res = self.sql.mysql_handler_personnal_query(query, db);
        for r in res:
            val = self.functions_transform[r[3]](int(json_obj['value']), r[4]);
            if val is not None:
                append(r)
                up = ''.join(["UPDATE room_device_option SET opt_value=\"", str(val),
                      "\" WHERE room_device_id=", str(r[1]), " AND option_id=\"", str(r[0]), "\""]);
                self.sql.mysql_handler_personnal_query(up, db);
        return result
