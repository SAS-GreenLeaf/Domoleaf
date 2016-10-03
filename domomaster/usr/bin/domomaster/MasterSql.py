import time;
import mysql.connector;
import sys;
sys.path.append('/usr/lib/domoleaf');
from MysqlHandler import *;
from DaemonConfigParser import *;
from Logger import *;
from inspect import currentframe, getframeinfo;
import MasterDaemon;
import utils;

MASTER_CONF_MYSQL_SECTION               = 'mysql';
MASTER_CONF_MYSQL_USER_ENTRY            = 'user';
MASTER_CONF_MYSQL_PASSWORD_ENTRY        = 'password';
MASTER_CONF_MYSQL_DB_NAME_ENTRY         = 'database_name';
MASTER_CONF_FILE                        = '/etc/domoleaf/master.conf';

class MasterSql:
    """
    Master daemon SQL management class.
    """
    def __init__(self, log_flag = True):
        self.logger = Logger(log_flag, '/var/log/domoleaf/domomaster.log');
        self._parser = DaemonConfigParser(MASTER_CONF_FILE);
        self.db_username = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION,
                                                            MASTER_CONF_MYSQL_USER_ENTRY);
        self.db_passwd = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION,
                                                          MASTER_CONF_MYSQL_PASSWORD_ENTRY);
        self.db_dbname = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION,
                                                          MASTER_CONF_MYSQL_DB_NAME_ENTRY);
        if not self.db_username or not self.db_passwd or not self.db_dbname:
            frameinfo = getframeinfo(currentframe());
            self.logger.debug("[ MASTER DAEMON "+frameinfo.filaname+":"+str(frameinfo.lineno)+" ]: initialization error: wrong or missing SQL configuration.");
            sys.exit(1);
        self.functions_transform = {
              0: utils.convert_none,
              1: utils.convert_temperature,
              2: utils.convert_hundred,
              3: utils.convert_float32
        };

    def insert_hostlist_in_db(self, hostlist, db):
        """
        Update of the table containing the hosts. Inserts each host in 'hostlist'.
        """
        for host in hostlist:
            db.update_datas_in_table('ip_monitor',
                                     {"mac_addr": host._MacAddr},
                                     {"last_update": str(time.time()).split('.')[0],
                                      "ip_addr": host._IpAddr,
                                      "hostname": host._Hostname.split('.')[0]});
        db.personnal_query("DELETE FROM ip_monitor WHERE last_update<"+str(time.time()-7200).split('.')[0]);
        db.updatedb();
        query = ''.join(["UPDATE room_device JOIN ip_monitor ON plus4=mac_addr SET addr=ip_addr WHERE protocol_id=6 AND plus4 != '' AND ip_addr != addr"]);
        self.mysql_handler_personnal_query(query, db);
        db.updatedb();

    def update_enocean_log(self, json_obj, db):
        """
        Update of the enocean log table with values from 'json_obj'
        """
        daemon_id = 0;
        daemons = db.get_datas_from_table_with_names('daemon', ['daemon_id', 'name', 'serial', 'secretkey']);
        for d in daemons:
            if json_obj['sender_name'] == d[2]:
                daemon_id = d[0];
                break;
        db.insert_datas_in_table('enocean_log',
                                 ['type', 'addr_src', 'addr_dest', 'eo_value', 't_date', 'daemon_id'],
                                 (json_obj['type'], json_obj['src_addr'], json_obj['dst_addr'],
                                  json_obj['value'], json_obj['date'], daemon_id));
        db.updatedb();
        return daemon_id;

    def update_knx_log(self, json_obj, db):
        """
        Update of the knx log table with values from 'json_obj'
        """
        daemon_id = 0;
        daemons = db.get_datas_from_table_with_names('daemon', ['daemon_id', 'name', 'serial', 'secretkey']);
        for d in daemons:
            if json_obj['sender_name'] == d[2]:
                daemon_id = d[0];
                break;
        db.insert_datas_in_table('knx_log', ["type", "addr_src", "addr_dest", "knx_value", "t_date", "daemon_id"],
                                 (json_obj['type'], json_obj['src_addr'], json_obj['dst_addr'], json_obj['value'],
                                  json_obj['date'],
                                  daemon_id));
        db.updatedb();
        return daemon_id;

    def update_room_device_option_write_long(self, json_obj, daemon_id, db):
        """
        Update of the table room_device_option with long KNX value
        """
        query  = ''.join(["SELECT room_device_option.option_id, room_device.room_device_id, function_answer, dpt_optiondef.dpt_id FROM room_device_option JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id WHERE daemon_id=",
                  str(daemon_id), " AND room_device_option.addr=\"", str(json_obj['dst_addr']), "\""]);
        res = self.mysql_handler_personnal_query(query, db);
        if not res:
            query = ''.join(["SELECT room_device_option.option_id, room_device.room_device_id, function_answer, dpt_optiondef.dpt_id FROM room_device_option JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id WHERE ",
                     str(daemon_id), " AND room_device_option.addr_plus=\"", str(json_obj['dst_addr']), "\""]);
            res = self.mysql_handler_personnal_query(query, db);
        for r in res:
            if int(r[0]) == 13:
                if not json_obj['value']:
                    up = 'UPDATE room_device_option SET opt_value=0 WHERE room_device_id=' + str(r[1]) + ' AND option_id=12';
                else:
                    up = 'UPDATE room_device_option SET opt_value=1 WHERE room_device_id=' + str(r[1]) + ' AND option_id=12';
                self.logger.debug('update_room_device_option write_long: up = ' + up);
                self.mysql_handler_personnal_query(up, db);
                query = ''.join(["UPDATE room_device_option SET opt_value=\"", str(json_obj['value']),
                         "\" WHERE room_device_id=", str(r[1]), " AND option_id=", str(r[0])]);
                self.mysql_handler_personnal_query(query, db);
            elif int(r[0]) == 72 or int(r[0]) == 388:
                val = int(json_obj['value']);
                res = utils.convert_temperature(val);
                query = ''.join(["UPDATE room_device_option JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id SET opt_value=\"",
                         str(res), "\" WHERE daemon_id=", str(daemon_id),
                         " AND room_device_option.addr=\"", str(json_obj['dst_addr']), "\"",
                         " OR ", "room_device_option.addr_plus=\"", str(json_obj['dst_addr']), "\""]);
                self.logger.debug('update_room_device_option write_long: query = ' + query);
                self.mysql_handler_personnal_query(query, db);
            else:
                val = self.functions_transform[r[2]](int(json_obj['value']));
                up = ''.join(["UPDATE room_device_option SET opt_value=\"", str(val),
                     "\" WHERE room_device_id=", str(r[1]), " AND option_id=\"", str(r[0]), "\""]);
                self.logger.debug('update_room_device_option write_long: up = ' + up)
                self.mysql_handler_personnal_query(up, db);
        return res

    def update_room_device_option_resp(self, json_obj, daemon_id, db):
        """
        Update of the table room_device_option with resp KNX value
        """
        query = ''.join(["SELECT option_id, room_device.room_device_id, function_answer FROM ",
              "room_device_option JOIN room_device ON ",
              "room_device_option.room_device_id=room_device.room_device_id ",
              "JOIN dpt_optiondef ON dpt_optiondef.option_id=room_device_option.option_id AND ",
              "dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id ",
              "WHERE daemon_id=", str(daemon_id), " AND room_device_option.addr=\"", str(json_obj['dst_addr']), "\""]);
        res = self.mysql_handler_personnal_query(query, db);
        for r in res:
            val = self.functions_transform[r[2]](int(json_obj['value']));
            query = ''.join(["UPDATE room_device_option JOIN room_device ON ",
                  "room_device_option.room_device_id=room_device.room_device_id SET ",
                  "opt_value=\"", str(val), "\" WHERE daemon_id=", str(daemon_id),
                  " AND room_device_option.addr=\"", str(json_obj['dst_addr']), "\""]);
            self.logger.debug("update_room_device_option resp query = "+query);
            self.mysql_handler_personnal_query(query, db);
        return res

    def update_room_device_option_write_short(self, json_obj, daemon_id, db):
        """
        Update of the table room_device_option with short KNX value
        """
        query = ''.join(["SELECT option_id, room_device.room_device_id FROM ",
              "room_device_option JOIN room_device ON ",
              "room_device_option.room_device_id=room_device.room_device_id WHERE ",
              "daemon_id=", str(daemon_id), " AND room_device_option.addr=\"",
              str(json_obj['dst_addr']), "\""]);
        #self.logger.debug("update_room_device_option write_short query : " + query);
        res = self.mysql_handler_personnal_query(query, db);
        if not res:
            query = ''.join(["SELECT option_id, room_device.room_device_id FROM ",
                  "room_device_option JOIN room_device ON ",
                  "room_device_option.room_device_id=room_device.room_device_id WHERE ",
                  "daemon_id=", str(daemon_id), " AND room_device_option.addr_plus=\"",
                  str(json_obj['dst_addr']), "\""]);
            #self.logger.debug("update_room_device_option write_short query : " + query);
            res = self.mysql_handler_personnal_query(query, db);
        for r in res:
            if (int(r[0]) == MasterDaemon.OPTION_ON_OFF or int(r[0]) == MasterDaemon.OPTION_UP_DOWN or int(r[0]) == MasterDaemon.OPTION_OPEN_CLOSE):
                up = 'UPDATE room_device_option SET opt_value=';
                if not json_obj['value']:
                    up += '0';
                else:
                    up += '255';
                up += ' WHERE room_device_id='+str(r[1])+" AND option_id=13";
                self.logger.debug("update_room_device_option write_short up1: "+up);
                self.mysql_handler_personnal_query(up, db);
            up = ''.join(["UPDATE room_device_option SET opt_value=", str(json_obj['value']),
                  " WHERE room_device_id=", str(r[1]), " AND option_id=", str(r[0]), ""]);
            self.logger.debug("update_room_device_option write_short up2: "+up);
            self.mysql_handler_personnal_query(up, db);
        return res

    def mysql_handler_personnal_query(self, query, db=0):
        """
        Sends personnal query to the database and returns the result
        """
        tmp = db;
        if not tmp:
            db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        res = db.personnal_query(query);
        db.updatedb();
        if not tmp:
            db.close();
        return res;

    def get_daemons(self, db):
        """
        Retrieves each daemon stored in the database
        """
        daemons = db.get_datas_from_table_with_names('daemon', ['daemon_id', 'name', 'serial', 'secretkey']);
        return daemons;
