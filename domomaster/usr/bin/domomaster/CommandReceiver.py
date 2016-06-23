from threading import Thread;
from MysqlHandler import *;
import MasterDaemon;

class CommandReceiver(Thread):
    def __init__(self, connection, daemon):
        """
        Threaded class retrieving a domoleaf packet and sends it to treatment function
        """
        Thread.__init__(self);
        self.connection = connection;
        self.daemon = daemon;
        self.db_username = daemon.db_username;
        self.db_passwd = daemon.db_passwd;
        self.db_dbname = daemon.db_dbname;

    def run(self):
        """
        Thread run function overload
        """
        self.db = MysqlHandler(self.db_username, self.db_passwd, self.db_dbname);
        data = self.connection.recv(MasterDaemon.MAX_DATA_LENGTH).decode();
        self.daemon.parse_data(data, self.connection, 0, self.db);
        self.db.close();
