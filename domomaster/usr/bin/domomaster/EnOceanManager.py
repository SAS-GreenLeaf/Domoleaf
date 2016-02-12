import logging;
from Logger import *;
import sys;
sys.path.append('/usr/lib/domoleaf');
from MasterSql import *;
from DaemonConfigParser import *;

LOG_FILE                = '/var/log/domoleaf/domomaster.log';
DEBUG_MODE = False;      # Debug flag

class EnOceanManager:
    """
    KNX management class
    """
    def __init__(self, slave_keys):
        self.logger = Logger(DEBUG_MODE, LOG_FILE);
        self.sql = MasterSql();
        self._parser = DaemonConfigParser('/etc/domoleaf/master.conf');
        self.aes_slave_keys = slave_keys;
        self.functions_transform = {
              0: utils.convert_none,
              4: utils.eno_onoff
        };
    
    def update_room_device_option(self, daemon_id, json_obj):
        """
        Update of the table room_device_option with EnOcean value
        """
        query  = "SELECT room_device_option.option_id, room_device.room_device_id, addr_plus, function_answer, room_device_option.dpt_id ";
        query += "FROM room_device_option ";
        query += "JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id ";
        query += "JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND ";
        query += "dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id ";
        query += "WHERE daemon_id=" + str(daemon_id) + " AND room_device_option.addr=\"";
        query += str(json_obj['src_addr']) + "\"";
        res = self.sql.mysql_handler_personnal_query(query);
        result = []
        
        if len(res) == 0:
            query  = "SELECT room_device_option.option_id, room_device.room_device_id, addr_plus, function_answer, room_device_option.dpt_id ";
            query += "FROM room_device_option ";
            query += "JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id ";
            query += "JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND ";
            query += "dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id ";
            query += "WHERE daemon_id=" + str(daemon_id) + " AND  room_device_option.addr_plus=\"";
            query += str(json_obj['src_addr']) + "\"";
            res = self.sql.mysql_handler_personnal_query(query);
        
        for r in res:
            val = self.functions_transform[r[3]](int(json_obj['value']), r[4]);
            if val is not None:
                result.append(r)
                up = "UPDATE room_device_option SET opt_value=\"" + str(val)
                up += "\" WHERE room_device_id=" + str(r[1]) + " AND option_id=\"" + str(r[0]) + "\"";
                self.sql.mysql_handler_personnal_query(up);
        return result
