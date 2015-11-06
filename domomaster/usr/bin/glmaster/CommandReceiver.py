from threading import Thread;
import MasterDaemon;

class CommandReceiver(Thread):
    def __init__(self, connection, daemon):
        """
        Threaded class retrieving a domoleaf packet and sends it to treatment function
        """
        Thread.__init__(self);
        self.connection = connection;
        self.daemon = daemon;

    def run(self):
        """
        Thread run function overload
        """
        data = self.connection.recv(MasterDaemon.MAX_DATA_LENGTH).decode();
        print('===== FROM CMD =====')
        print(data);
        print('====================')
        print();
        self.daemon.parse_data(data, self.connection);
