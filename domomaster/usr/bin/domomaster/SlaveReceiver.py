from threading import Thread;
import MasterDaemon;
from Crypto.Cipher import AES;
import json;
from MasterSql import *;
import hashlib;
from Logger import *;

log_flag = False;
LOG_FILE = '/var/log/domoleaf/domomaster.log'

class SlaveReceiver(Thread):
    def __init__(self, connection, hostname, daemon):
        self.logger = Logger(log_flag, LOG_FILE);
        """
        Threaded class for reading from a slave and send the data to the treatment function
        """
        Thread.__init__(self);
        self.connection = connection;
        self.daemon = daemon;
        self.connected_host = hostname;
        self.sql = MasterSql();

    def run(self):
        """
        Thread run function overload
        """
        self.logger.error('SELECT serial, secretkey FROM daemon WHERE serial=\'' + self.connected_host + '\'')
        res = self.sql.mysql_handler_personnal_query('SELECT serial, secretkey FROM daemon WHERE serial=\'' + self.connected_host + '\'');
        aes_key = '';
        for r in res:
            if r[0] == self.connected_host:
                aes_key = r[1];
                break;
        if aes_key == '':
            return None;
        try:
            data = self.connection.recv(MasterDaemon.MAX_DATA_LENGTH);
            decrypt_IV = data[:16].decode();
            decode_obj = AES.new(aes_key, AES.MODE_CBC, decrypt_IV);
            data2 = decode_obj.decrypt(data[16:]).decode();
            flag = False;
            obj = data2;
            self.daemon.parse_data(obj, self.connection);
        except Exception as e:
            self.logger.error(e);
