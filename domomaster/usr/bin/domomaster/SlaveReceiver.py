## @package domomaster
# Master daemon for D3 boxes.
#
# Developed by GreenLeaf.

from threading import Thread;
import MasterDaemon;
from MysqlHandler import *;
from Crypto.Cipher import AES;
import json;
from MasterSql import *;
import hashlib;
from Logger import *;

log_flag = False;
LOG_FILE = '/var/log/domoleaf/domomaster.log'

## Threaded class retrieving data from salve daemons.
#
# Sends the data to a treatment function of the daemon.
class SlaveReceiver(Thread):

    # The constructor.
    #
    # @param connection Connection object used to communicate.
    # @param hostname The hostname.
    # @param daemon The MasterDaemon with the adapted treatment functions.
    def __init__(self, connection, hostname, daemon):
        self.logger = Logger(log_flag, LOG_FILE);
        Thread.__init__(self);
        self.connection = connection;
        self.daemon = daemon;
        self.connected_host = hostname;
        self.sql = MasterSql();
        self.db_username = daemon.db_username;
        self.db_passwd = daemon.db_passwd;
        self.db_dbname = daemon.db_dbname;

    ## Thread run function overload.
    def run(self):
        self.db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        self.logger.error('SELECT serial, secretkey, daemon_id FROM daemon WHERE serial=\''+self.connected_host+'\'');
        res = self.sql.mysql_handler_personnal_query('SELECT serial, secretkey, daemon_id FROM daemon WHERE serial=\''+self.connected_host+'\'', self.db);
        aes_key = '';
        for r in res:
            if r[0] == self.connected_host:
                aes_key = r[1];
                daemon_id = r[2];
                break;
        if not aes_key:
            return None;
        try:
            data = self.connection.recv(MasterDaemon.MAX_DATA_LENGTH);
            decrypt_IV = data[:16].decode();
            decode_obj = AES.new(aes_key, AES.MODE_CBC, decrypt_IV);
            data2 = decode_obj.decrypt(data[16:]).decode();
            flag = False;
            obj = data2;
            self.daemon.parse_data(obj, self.connection, daemon_id, self.db);
        except Exception as e:
            self.logger.error(e);
        self.db.close();
