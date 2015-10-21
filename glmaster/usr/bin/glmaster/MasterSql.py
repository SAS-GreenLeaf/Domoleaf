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
    def __init__(self, log_flag = False):
        self.logger = Logger(log_flag, '/var/log/glmaser.log');
        self._parser = DaemonConfigParser(MASTER_CONF_FILE);
        self.db_username = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION,
                                                            MASTER_CONF_MYSQL_USER_ENTRY);
        self.db_passwd = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION,
                                                          MASTER_CONF_MYSQL_PASSWORD_ENTRY);
        self.db_dbname = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION,
                                                          MASTER_CONF_MYSQL_DB_NAME_ENTRY);
        if not self.db_username or not self.db_passwd or not self.db_dbname:
            frameinfo = getframeinfo(currentframe());
            self.logger.info("[ MASTER DAEMON " + frameinfo.filaname + ":" + str(frameinfo.lineno) + " ]: initialization error: wrong or missing SQL configuration.");
            sys.exit(1);

    def insert_hostlist_in_db(self, hostlist):
        """
        Update of the table containing the hosts. Inserts each host in 'hostlist'.
        """
        db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        for host in hostlist:
            db.update_datas_in_table('ip_monitor',
                                     {"mac_addr": host._MacAddr},
                                     {"last_update": str(time.time()).split('.')[0],
                                      "ip_addr": host._IpAddr,
                                      "hostname": host._Hostname.split('.')[0]});
        db.personnal_query("DELETE FROM ip_monitor WHERE last_update<"+str(time.time()-7200).split('.')[0]);
        db.updatedb();
        db.close();

    def update_enocean_log(self, json_obj):
        """
        Update of the enocean log table with values from 'json_obj'
        """
        daemon_id = 0;
        db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
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
        db.close();
        return daemon_id;

    def update_knx_log(self, json_obj):
        """
        Update of the knx log table with values from 'json_obj'
        """
        daemon_id = 0;
        db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
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
        db.close();
        return daemon_id;

    def update_room_device_option_write_long(self, json_obj, daemon_id):
        """
        Update of the table room_device_option with long KNX value
        """
        query = "SELECT option_id, room_device.room_device_id, addr_plus FROM room_device_option ";
        query += "JOIN room_device ON room_device_option.room_device_id=room_device.room_device_id ";
        query += "WHERE daemon_id=" + str(daemon_id) + " AND room_device_option.addr=\"";
        query += str(json_obj['dst_addr']) + "\"";
        res = self.mysql_handler_personnal_query(query);
        
        if len(res) == 0:
            query = "SELECT option_id, room_device.room_device_id FROM ";
            query += "room_device_option JOIN room_device ON ";
            query += "room_device_option.room_device_id=room_device.room_device_id WHERE ";
            query += "daemon_id=" + str(daemon_id) + " AND room_device_option.addr_plus=\"";
            query += str(json_obj['dst_addr']) + "\"";
            res = self.mysql_handler_personnal_query(query);
        
        for r in res:
            if int(r[0]) == MasterDaemon.OPTION_VAR:
                up = 'UPDATE room_device_option SET valeur=';
                if json_obj['value'] == 0:
                    up += '0';
                else:
                    up += '1';
                up += " WHERE room_device_id=" + str(r[1]);
                up += " AND option_id=12";
                self.logger.info('update_room_device_option write_long: up = ' + up);
                self.mysql_handler_personnal_query(up);
                
                query = "UPDATE room_device_option SET ";
                query += "valeur=\"" + str(json_obj['value']) + "\" ";
                query += "WHERE room_device_id=" + str(r[1]) + " AND option_id="+str(r[0]);
                self.mysql_handler_personnal_query(query);
            elif int(r[0]) == MasterDaemon.OPTION_TEMPERATURE or int(r[0]) == MasterDaemon.OPTION_TEMPERATURE_W:
                val = int(json_obj['value']);
                res = utils.convert_temperature(val);
                query = "UPDATE room_device_option JOIN room_device ON ";
                query += "room_device_option.room_device_id=room_device.room_device_id SET ";
                query += "valeur=\"" + str(res) + "\" WHERE daemon_id=" + str(daemon_id);
                query += " AND room_device_option.addr=\"" + str(json_obj['dst_addr']) + "\"";
                self.logger.info('update_room_device_option write_long: query = ' + query);
                self.mysql_handler_personnal_query(query);
            else:
                up = "UPDATE room_device_option SET valeur=\"" + str(json_obj['value'])
                up += "\" WHERE room_device_id=" + str(r[1]) + " AND option_id=\"" + str(r[0]) + "\"";
                self.logger.info('update_room_device_option write_long: up = ' + up)
                self.mysql_handler_personnal_query(up);

    def update_room_device_option_resp(self, json_obj, daemon_id):
        """
        Update of the table room_device_option with resp KNX value
        """
        query = "SELECT option_id, room_device.room_device_id FROM ";
        query += "room_device_option JOIN room_device ON ";
        query += "room_device_option.room_device_id=room_device.room_device_id WHERE ";
        query += "daemon_id=" + str(daemon_id) + " AND room_device_option.addr=\"";
        query += str(json_obj['dst_addr']) + "\"";
        
        res = self.mysql_handler_personnal_query(query);
        if type(res).__name__ == 'list':
            for r in res:
                query = "UPDATE room_device_option JOIN room_device ON ";
                query += "room_device_option.room_device_id=room_device.room_device_id SET ";
                if int(r[0]) == OPTION_TEMPERATURE:
                    val = int(json_obj['value']);
                    val = utils.convert_temperature(val);
                    query += "valeur=\"" + str(val) + "\" WHERE daemon_id=" + str(daemon_id);
                    query += " AND room_device_option.addr=\"" + str(json_obj['dst_addr']) + "\"";
                    self.logger.info("update_room_device_option resp query = " + query);
                    self.mysql_handler_personnal_query(query);
                else:
                    query += "valeur=\"" + str(json_obj['value']) + "\" WHERE daemon_id=" + str(daemon_id);
                    query += " AND addr_plus=\"" + str(json_obj['dst_addr']) + "\"";
                    self.logger.info("update_room_device_option resp query = " + query);
                    self.mysql_handler_personnal_query(query);
        else:
            query = "UPDATE room_device_option JOIN room_device ON ";
            query += "room_device_option.room_device_id=room_device.room_device_id SET ";
            if int(r[0]) == OPTION_TEMPERATURE:
                val = int(json_obj['value']);
                val = utils.convert_temperature(val);
                query += "valeur=\"" + str(val) + "\" WHERE daemon_id=" + str(daemon_id);
                query += " AND room_device_option.addr=\"" + str(json_obj['dst_addr']) + "\"";
                self.logger.info("update_room_device_option resp query = " + query);
                self.mysql_handler_personnal_query(query);
            else:
                query += "valeur=\"" + str(json_obj['value']) + "\" WHERE daemon_id=" + str(daemon_id);
                query += " AND addr_plus=\"" + str(json_obj['dst_addr']) + "\"";
                self.logger.info('update_room_device_option resp query = ' + query);
                self.mysql_handler_personnal_query(query);

    def update_room_device_option_write_short(self, json_obj, daemon_id):
        """
        Update of the table room_device_option with short KNX value
        """
        query = "SELECT option_id, room_device.room_device_id FROM ";
        query += "room_device_option JOIN room_device ON ";
        query += "room_device_option.room_device_id=room_device.room_device_id WHERE ";
        query += "daemon_id=" + str(daemon_id) + " AND room_device_option.addr=\"";
        query += str(json_obj['dst_addr']) + "\"";
        self.logger.info("update_room_device_option write_short query : " + query);
        res = self.mysql_handler_personnal_query(query);
        
        if len(res) == 0:
            query = "SELECT option_id, room_device.room_device_id FROM ";
            query += "room_device_option JOIN room_device ON ";
            query += "room_device_option.room_device_id=room_device.room_device_id WHERE ";
            query += "daemon_id=" + str(daemon_id) + " AND room_device_option.addr_plus=\"";
            query += str(json_obj['dst_addr']) + "\"";
            self.logger.info("update_room_device_option write_short query : " + query);
            res = self.mysql_handler_personnal_query(query);
        
        for r in res:
            if (int(r[0]) == MasterDaemon.OPTION_ON_OFF or int(r[0]) == MasterDaemon.OPTION_UP_DOWN or int(r[0]) == MasterDaemon.OPTION_OPEN_CLOSE):
                up = 'UPDATE room_device_option SET valeur=';
                if json_obj['value'] == 0:
                    up += '0';
                else:
                    up += '255';
                up += ' WHERE room_device_id=' + str(r[1]) + " AND option_id=13";
                self.logger.info("update_room_device_option write_short up1: " + up)
                self.mysql_handler_personnal_query(up);
            up = "UPDATE room_device_option SET valeur=" + str(json_obj['value']);
            up += " WHERE room_device_id=" + str(r[1]) + " AND option_id=" + str(r[0]) + "";
            self.logger.info("update_room_device_option write_short up2: " + up)
            self.mysql_handler_personnal_query(up);

    def mysql_handler_personnal_query(self, query):
        """
        Sends personnal query to the database and returns the result
        """
        db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        res = db.personnal_query(query);
        db.updatedb();
        db.close();
        return res;

    def get_daemons(self):
        """
        Retrieves each daemon stored in the database
        """
        db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        daemons = db.get_datas_from_table_with_names('daemon', ['daemon_id', 'name', 'serial', 'secretkey']);
        return daemons;
