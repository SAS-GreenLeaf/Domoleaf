#!/usr/bin/python3

import logging;
from inspect import currentframe, getframeinfo;
from Crypto.Cipher import AES;
import smtplib;
from email.mime.multipart import MIMEMultipart;
from email.mime.text import MIMEText;
from email.utils import formataddr;
import copy;
import time;
import os;
import json;
import glob;
import select;
import socket;
import sys;
import fcntl;
import struct;
from subprocess import *
sys.path.append('/usr/lib/greenleaf');
from DeviceManager import *;
from DaemonConfigParser import *;
from MysqlHandler import *;
from Scanner import *;
from UpnpAudio import *;
from Logger import *;
import AESManager;
from Crypto.Cipher import AES;
from SlaveReceiver import *;
from CommandReceiver import *;
from MasterSql import *;
from KNXManager import *;
from UpnpAudio import *;
import utils;

LOG_FILE                = '/var/log/glmaster.log'
MASTER_CONF_FILE        = '/etc/greenleaf/master.conf';         # Configuration file name
SELECT_TIMEOUT          = 0.05;                                 # Timeout for the select
MAX_SLAVES              = 100;                                  # Max slave that can be connected at the same time
MAX_CMDS                = 100;                                  # Max mastercommand interface that can be connected at the same time
SLAVE_NAME_PREFIX       = 'SD3';                                # Slave box hostname prefix
MAX_DATA_LENGTH         = 4096;                                 # Max size of the socket buffer

########################################################
# Section and field names in master configuration file #
########################################################
MASTER_CONF_LISTEN_SECTION              = 'listen';
MASTER_CONF_LISTEN_PORT_SLAVE_ENTRY     = 'port_slave';
MASTER_CONF_LISTEN_PORT_CMD_ENTRY       = 'port_cmd';
MASTER_CONF_CONNECT_SECTION             = 'connection';
MASTER_CONF_CONNECT_PORT_ENTRY          = 'port';
MASTER_CONF_PKEY_SIZE_ENTRY             = 'size';

RELOAD_WEB_SERVER_COMMAND               = ["service", "nginx", "reload"]; # Command and args to reload nginx

PROTOCOL_KNX            = 1;    # KNX protocol id
PROTOCOL_ENOCEAN        = 2;    # EnOcean protocol id
PROTOCOL_IP             = 6;    # IP protocol id

# IDs for UPNP protocol
UPNP_PLAY           = 'play';               # Play command id
UPNP_PAUSE          = 'pause';              # Pause command id
UPNP_NEXT           = 'next';               # Next command id
UPNP_PREVIOUS       = 'prev';               # Prev command id
UPNP_STOP           = 'stop';               # Stop command id
UPNP_MUTE           = 'mute';               # Mute command id
UPNP_VOLUME_UP      = 'volume_up';          # Volume++ command id
UPNP_VOLUME_DOWN    = 'volume_down';        # Volume-- command id
UPNP_SET_VOLUME     = 'set_volume';         # Set volume command id

#################################################
# Packet types that can be received and treated #
#################################################
DATA_MONITOR_KNX              = 'monitor_knx';
DATA_MONITOR_IP               = 'monitor_ip';
DATA_MONITOR_ENOCEAN          = 'monitor_enocean';
DATA_MONITOR_BLUETOOTH        = 'monitor_bluetooth';
DATA_KNX_READ                 = 'knx_read';
DATA_KNX_WRITE_S              = 'knx_write_s';
DATA_KNX_WRITE_L              = 'knx_write_l';
DATA_SEND_TO_DEVICE           = 'send_to_device';
DATA_CRON_UPNP                = 'cron_upnp';
DATA_SEND_MAIL                = 'send_mail';
DATA_CHECK_SLAVE              = 'check_slave';
DATA_RELOAD_CAMERA            = 'reload_camera';
DATA_RELOAD_D3CONFIG          = 'reload_d3config';
DATA_BACKUP_DB_CREATE_LOCAL   = 'backup_db_create_local';
DATA_BACKUP_DB_REMOVE_LOCAL   = 'backup_db_remove_local';
DATA_BACKUP_DB_LIST_LOCAL     = 'backup_db_list_local';
DATA_BACKUP_DB_RESTORE_LOCAL  = 'backup_db_restore_local';
DATA_CHECK_USB                = 'check_usb';
DATA_BACKUP_DB_CREATE_USB     = 'backup_db_create_usb';
DATA_BACKUP_DB_REMOVE_USB     = 'backup_db_remove_usb';
DATA_BACKUP_DB_LIST_USB       = 'backup_db_list_usb';
DATA_BACKUP_DB_RESTORE_USB    = 'backup_db_restore_usb';
DATA_UPDATE                   = 'update';

HOSTS_CONF                    = '/etc/greenleaf/hosts.conf';          # Path for the network configuration file
CAMERA_CONF_FILE              = '/etc/greenleaf/camera.conf';         # Path for the cameras configuration file

DEBUG_MODE = True;      # Debug flag

class MasterDaemon:
    """
    Main class of the master daemon
    It provides communication between master and slave boxes and a part of the database management
    """
    def __init__(self, log_flag):
        self.logger = Logger(log_flag, LOG_FILE);
        self.logger.info('Started Greenleaf Master Daemon');
        self.d3config = {};
        self.aes_slave_keys = {};
        self.aes_master_key = None;
        self.connected_clients = {};
        self.sql = MasterSql();
        self._parser = DaemonConfigParser(MASTER_CONF_FILE);
        self.get_aes_slave_keys();
        self.reload_camera(None, None);
        self.scanner = Scanner(HOSTS_CONF);
        self.scanner.scan(False);
        self.hostlist = self.scanner._HostList;
        self.sql.insert_hostlist_in_db(self.scanner._HostList);
        self.knx_manager = KNXManager(self.aes_slave_keys);
        self.reload_d3config(None, None);
        self.protocol_function = {
            PROTOCOL_KNX        : KNXManager.protocol_knx,
            PROTOCOL_ENOCEAN    : self.protocol_enocean,
            PROTOCOL_IP         : self.protocol_ip
        };
        self.upnp_function = {
            UPNP_PLAY           : self.upnp_set_play,
            UPNP_PAUSE          : self.upnp_set_pause,
            UPNP_NEXT           : self.upnp_set_next,
            UPNP_PREVIOUS       : self.upnp_set_prev,
            UPNP_STOP           : self.upnp_set_stop,
            UPNP_MUTE           : self.upnp_set_mute,
            UPNP_VOLUME_UP      : self.upnp_set_volume_up,
            UPNP_VOLUME_DOWN    : self.upnp_set_volume_down,
            UPNP_SET_VOLUME     : self.upnp_set_volume
        };
        self.enocean_function = {};
        self.data_function = {
            DATA_MONITOR_KNX                  : self.monitor_knx,
            DATA_MONITOR_IP                   : self.monitor_ip,
            DATA_MONITOR_ENOCEAN              : self.monitor_enocean,
            DATA_MONITOR_BLUETOOTH            : self.monitor_bluetooth,
            DATA_KNX_READ                     : self.knx_read,
            DATA_KNX_WRITE_S                  : self.knx_write_short,
            DATA_KNX_WRITE_L                  : self.knx_write_long,
            DATA_SEND_TO_DEVICE               : self.send_to_device,
            DATA_CRON_UPNP                    : self.cron_upnp,
            DATA_SEND_MAIL                    : self.send_mail,
            DATA_CHECK_SLAVE                  : self.check_slave,
            DATA_RELOAD_CAMERA                : self.reload_camera,
            DATA_RELOAD_D3CONFIG              : self.reload_d3config,
            DATA_BACKUP_DB_CREATE_LOCAL       : self.backup_db_create_local,
            DATA_BACKUP_DB_REMOVE_LOCAL       : self.backup_db_remove_local,
            DATA_BACKUP_DB_LIST_LOCAL         : self.backup_db_list_local,
            DATA_BACKUP_DB_RESTORE_LOCAL      : self.backup_db_restore_local,
            DATA_CHECK_USB                    : self.check_usb,
            DATA_BACKUP_DB_CREATE_USB         : self.backup_db_create_usb,
            DATA_BACKUP_DB_REMOVE_USB         : self.backup_db_remove_usb,
            DATA_BACKUP_DB_LIST_USB           : self.backup_db_list_usb,
            DATA_BACKUP_DB_RESTORE_USB        : self.backup_db_restore_usb,
            DATA_UPDATE                       : self.update
        };

    def get_aes_slave_keys(self):
        """
        Get the secretkeys of each slave daemon stored in database
        """
        query = "SELECT serial, secretkey FROM daemon";
        res = self.sql.mysql_handler_personnal_query(query);
        self_hostname = socket.gethostname();
        for r in res:
            if SLAVE_NAME_PREFIX in r[0] or 'MD3' in r[0]:
                self.aes_slave_keys[r[0]] = r[1];
            elif self_hostname == r[0]:
                self.aes_slave_keys[r[0]] = r[1];
                self.aes_master_key = r[1];
        print(self.aes_slave_keys)

    def stop(self):
        """
        Stops the daemon and closes sockets
        """
        flag = False;
        while not flag:
            flag = True;
            for client in self.connected_clients.values():
                flag = False;
                client.close();
                break;
        self.slave_connection.close();
        sys.exit(0);

    def run(self):
        """
        Initialization of the connections and accepting incomming communications
        """
        self.slave_connection = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        self.cmd_connection = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        self.slave_connection.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        self.cmd_connection.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        s_port = self._parser.getValueFromSection(MASTER_CONF_LISTEN_SECTION, MASTER_CONF_LISTEN_PORT_SLAVE_ENTRY);
        c_port = self._parser.getValueFromSection(MASTER_CONF_LISTEN_SECTION, MASTER_CONF_LISTEN_PORT_CMD_ENTRY);
        if not s_port:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in run: No slave listening port defined in ' + MASTER_CONF_FILE);
            sys.exit(1);
        if not c_port:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in run: No command listening port defined in ' + MASTER_CONF_FILE);
            sys.exit(1);
        self.slave_connection.bind(('', int(s_port)));
        self.slave_connection.listen(MAX_SLAVES);
        self.cmd_connection.bind(('', int(c_port)));
        self.cmd_connection.listen(MAX_CMDS);
        self.loop();

    def loop(self):
        """
        Main loop. Waits for new connections.
        """
        self.run = True;
        while self.run:
            try:
                rlist, wlist, elist = select.select([self.slave_connection], [], [], SELECT_TIMEOUT);
                for connection in rlist:
                    self.accept_new_slave_connection(connection);
                rlist, wlist, elist = select.select([self.cmd_connection], [], [], SELECT_TIMEOUT);
                for connection in rlist:
                    self.accept_new_cmd_connection(connection);
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.info('in loop: Keyboard interrupt: leaving program');
                print("[ MASTER DAEMON " + frameinfo.filename + ":" + str(frameinfo.lineno) + " ]: Keyboard Interrupt");
                self.stop();
                sys.exit(0);
            except ValueError as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Value error: ' + str(e));
                print("[ MASTER DAEMON " + frameinfo.filename + ":" + str(frameinfo.lineno) + "]: Value Error");
                print(e);
                pass;

    def accept_new_cmd_connection(self, connection):
        """
        Gets new mastercommand connections and threads the treatment.
        """
        new_connection, addr = connection.accept();
        r = CommandReceiver(new_connection, self);
        r.start();

    def accept_new_slave_connection(self, connection):
        """
        Gets new slave connections and threads the treatment.
        """
        new_connection, addr = connection.accept();
        for host in self.hostlist:
            if addr[0] == host._IpAddr:
                hostname = host._Hostname.split('.')[0];
                r = SlaveReceiver(new_connection, hostname, self);
                r.start();

    def parse_data(self, data, connection):
        """
        Once data are received whether from mastercommand or slave, the function of the packet_type in data is called.
        """
        json_obj = json.JSONDecoder().decode(data);
        if json_obj['packet_type'] in self.data_function.keys():
            self.data_function[json_obj['packet_type']](json_obj, connection);
        else:
            frameinfo = getframeinfo(currentframe());

    def update(self, json_obj, connection):
        call(['apt-get', 'update']);
        call(['apt-get', 'install', 'glmaster', 'glslave', '-y']);
        hostname = socket.gethostname();
        if '.' in hostname:
            hostname = hostname.split('.')[0];
        version_file = open('/etc/greenleaf/.glmaster.version', 'r');
        if not version_file:
            self.logger.error("File not found: /etc/greenleaf/.glmaster.version");
            print("File not found: /etc/greenleaf/.glmaster.version");
            return;
        version = version_file.read();
        if '\n' in version:
            version = version.split('\n')[0];
        query = 'UPDATE daemon SET version="' + version + '" WHERE name="' + hostname + '"';
        self.sql.mysql_handler_personnal_query(query);
        query = 'UPDATE configuration SET configuration_value="' + version + '" WHERE configuration_id=4';
        self.sql.mysql_handler_personnal_query(query);
        json_obj['data'].append(hostname);
        port = self._parser.getValueFromSection('connect', 'port');
        for host in self.hostlist:
            sock = socket.create_connection((host._IpAddr, port));
            if host._Hostname.startswith('MD3') or host._Hostname.startswith('SD3') and host._Hostname not in json_obj['data']:
                json_str = json.JSONEncoder().encode(json_obj);
                sock.send(bytes(json_str, 'utf-8'));
                data = sock.recv(4096);
                decrypt_IV = data[:16].decode();
                decode_obj = AES.new(self.aes_master_key, AES.MODE_CBC, decrypt_IV);
                data2 = decode_obj.decrypt(data[16:]).decode();
                version = data2['new_version'];
                query = 'UPDATE daemon SET version="' + version + '" WHERE name="' + host._Hostname + '"';
                self.sql.mysql_handler_personnal_query(query);

    def backup_db_create_local(self, json_obj, connection):
        path = '/etc/greenleaf/sql/backup/';
        filename = 'mastercommand_backup_';
        t = str(time.time());
        if '.' in t:
            t = t.split('.')[0];
        filename += t;
        filename += '.sql';
        os.system("mysqldump --defaults-file=/etc/mysql/debian.cnf mastercommand > " + path + filename);
        os.system('cd ' + path + ' && tar -czf ' + filename + '.tar.gz' + ' ' + filename);
        os.system('rm ' + path + filename);

    def backup_db_remove_local(self, json_obj, connection):
        filename = '/etc/greenleaf/sql/backup/mastercommand_backup_';
        filename += str(json_obj['data']);
        filename += '.sql.tar.gz';
        if str(json_obj['data'][0]) == '.' or str(json_obj['data'][0]) == '/':
            self.logger.error('The filename is corrupted. Aborting database file removing.')
            return;
        try:
            os.stat(filename);
        except Exception as e:
            try:
                filename = filename.split('.tar.gz')[0];
                os.stat(filename);
            except Exception as e:
                self.logger.error("The database file to remove does not exists.")
                self.logger.error(e)
                return;
        os.remove(filename);

    def backup_db_list_local(self, json_obj, connection):
        json_obj = [];
        backup_list = os.listdir('/etc/greenleaf/sql/backup/')
        for f in backup_list:
            s = os.stat('/etc/greenleaf/sql/backup/' + f);
            if '.sql' in f:
                f = f.split('.sql')[0];
                json_obj.append({"name": f, "size": s.st_size});
        json_sorted = sorted(json_obj, key=lambda json_obj: json_obj['name'], reverse=True);
        json_str = json.JSONEncoder().encode(json_sorted);
        connection.send(bytes(json_str, 'utf-8'));

    def backup_db_restore_local(self, json_obj, connection):
        path = '/etc/greenleaf/sql/backup/';
        filename = 'mastercommand_backup_';
        filename += str(json_obj['data']);
        filename += '.sql.tar.gz';
        if json_obj['data'][0] == '.' or json_obj['data'][0] == '/':
            self.logger.error('The filename is corrupted. Aborting database restoring.')
            return;
        try:
            os.stat(path + filename);
            os.system('cd ' + path + ' && tar -xzf ' + filename);
            os.system('mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < ' + path + filename.split('.tar.gz')[0]);
            os.system('rm ' + path + filename.split('.tar.gz')[0]);
            return;
        except Exception as e:
            try:
                filename = filename.split('.tar.gz')[0];
                os.stat(path + filename);
            except Exception as e:
                self.logger.error("The database file to restore does not exists.");
                self.logger.error(e);
                return;
        os.system('mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < ' + path + filename);

    def check_usb(self, json_obj, connection):
        try:
            sdx1 = glob.glob('/dev/sd?1')[0];
        except Exception as e:
            return;
        if (os.path.exists(sdx1) == 0):
            json_obj = 0;
        else:
            json_obj = 1;
        json_str = json.JSONEncoder().encode(json_obj);
        connection.send(bytes(json_str, 'utf-8'));

    def backup_db_list_usb(self, json_obj, connection):
        json_obj = [];
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/greenleaf/mnt');
        os.system('mkdir -p /etc/greenleaf/mnt/backup');
        backup_list = os.listdir('/etc/greenleaf/mnt/backup/')
        for f in backup_list:
            s = os.stat('/etc/greenleaf/mnt/backup/' + f);
            if '.sql' in f:
                f = f.split('.sql')[0];
                json_obj.append({"name": f, "size": s.st_size});
        os.system('umount /etc/greenleaf/mnt');
        json_sorted = sorted(json_obj, key=lambda json_obj: json_obj['name'], reverse=True);
        json_str = json.JSONEncoder().encode(json_sorted);
        connection.send(bytes(json_str, 'utf-8'));

    def backup_db_remove_usb(self, json_obj, connection):
        filename = '/etc/greenleaf/mnt/backup/mastercommand_backup_';
        filename += str(json_obj['data']);
        filename += '.sql.tar.gz';
        if str(json_obj['data'][0]) == '.' or str(json_obj['data'][0]) == '/':
            self.logger.error('The filename is corrupted. Aborting database file removing.')
            return;
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/greenleaf/mnt');
        path = '/etc/greenleaf/mnt/backup/';
        try:
            os.stat(filename);
        except Exception as e:
            try:
                filename = filename.split('.tar.gz')[0];
                os.stat(filename);
            except Exception as e:
                self.logger.error("The database file to remove does not exists.")
                self.logger.error(e)
                os.system('umount /etc/greenleaf/mnt');
                return;
        os.remove(filename);
        os.system('umount /etc/greenleaf/mnt');

    def backup_db_restore_usb(self, json_obj, connection):
        path = '/etc/greenleaf/mnt/backup/';
        filename = 'mastercommand_backup_';
        filename += str(json_obj['data']);
        filename += '.sql';
        if json_obj['data'][0] == '.' or json_obj['data'][0] == '/':
            self.logger.error('The filename is corrupted. Aborting database restoring.')
            return;
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/greenleaf/mnt');
        try:
            os.stat(path + filename);
            os.system('cp ' + path + filename + ' /tmp/ && umount /etc/greenleaf/mnt && cd /tmp/');
            os.system('mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /tmp/' + filename);
            os.remove('/tmp/' + filename);
            return;
        except Exception as e:
            try:
                filename = filename + '.tar.gz';
                os.stat(path + filename);
                os.system('cp ' + path + filename + ' /tmp/ && umount /etc/greenleaf/mnt && cd /tmp/ && tar -xzf ' + filename);
            except Exception as e:
                self.logger.error("The database file to restore does not exists.");
                self.logger.error(e);
                os.system('umount /etc/greenleaf/mnt');
                return;
        os.system('umount /etc/greenleaf/mnt');
        os.system('mysql --defaults-file=/etc/mysql/debian.cnf mastercommand < /tmp/' + filename.split('.tar.gz')[0]);
        os.remove('/tmp/' + filename);
        os.remove('/tmp/' + filename.split('.tar.gz')[0]);

    def backup_db_create_usb(self, json_obj, connection):
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/greenleaf/mnt');
        path = '/etc/greenleaf/mnt/backup/';
        filename = 'mastercommand_backup_';
        os.system('mkdir -p ' + path);
        t = str(time.time());
        if '.' in t:
            t = t.split('.')[0];
        filename += t;
        filename += '.sql';
        os.system("mysqldump --defaults-file=/etc/mysql/debian.cnf mastercommand > " + path + filename);
        os.system('cd ' + path + ' && tar -czf ' + filename + '.tar.gz' + ' ' + filename);
        os.system('rm ' + path + filename);
        os.system('umount /etc/greenleaf/mnt');

    def monitor_knx(self, json_obj, connection):
        """
        Callback called each time a monitor_knx packet is received.
        Updates room_device_option values in the database.
        """
        daemon_id = self.sql.update_knx_log(json_obj);
        
        self.knx_manager.update_room_device_option(daemon_id, json_obj);
        connection.close();

    def knx_write_short(self, json_obj, connection):
        """
        Callback called each time a knx_write_short packet is received.
        Updates room_device_option values in the database.
        """
        daemons = self.sql.get_daemons();
        slave_name = self.get_slave_name(json_obj, daemons);
        if slave_name is None:
            connection.close();
            return None;
        for host in self.hostlist:
            if slave_name in host._Hostname:
                self.knx_manager.send_knx_write_short_to_slave(host._Hostname, json_obj);
        connection.close();
        return None;

    def knx_write_long(self, json_obj, connection):
        """
        Callback called each time a knx_write_long packet is received.
        Updates room_device_option values in the database.
        """
        daemons = self.sql.get_daemons();
        slave_name = self.get_slave_name(json_obj, daemons);
        if slave_name is None:
            connection.close();
            return None;
        for host in self.hostlist:
            if slave_name in host._Hostname:
                self.knx_manager.send_knx_write_long_to_slave(host._Hostname, json_obj);
        connection.close();
        return None;

    def knx_read(self, json_obj, connection):
        """
        Callback called each time a knx_read packet is received.
        """
        daemons = self.sql.get_daemons();
        slave_name = self.get_slave_name(json_obj, daemons);
        if slave_name is None:
            return None;
        for host in self.hostlist:
            if slave_name in host._Hostname:
                self.knx_manager.send_knx_read_request_to_slave(host._Hostname, json_obj);
        connection.close();

    def monitor_ip(self, json_obj, connection):
        """
        Callback called each time a monitor_ip packet is received.
        A new local network scan is performed and the result stored in the database
        """
        self.scanner.scan(True);
        self.sql.insert_hostlist_in_db(self.scanner._HostList);
        self.hostlist = self.scanner._HostList;
        connection.close();

    def monitor_bluetooth(self, json_obj, connection):
        """
        TODO
        """
        connection.close();
        return None;

    def monitor_enocean(self, json_obj, connection):
        """
        Callback called each time a monitor_enocean packet is received.
        Stores the data in enocean_log table.
        """
        print('updating enocean log with ' + str(json_obj));
        daemon_id = self.sql.update_enocean_log(json_obj);
        print('[ ok ] Done update')
        connection.close();
        ###########################################################################################
        # A VOIR SI CETTE FONCTION EST UTILE (VOIR QUAND LES DEVICES ENOCEAN SERONT DANS LA BASE) #
        # self.enocean_manager.update_room_device_option(daemon_id, json_obj);                    #
        ###########################################################################################
        return None;

    def send_to_device(self, json_obj, connection):
        """
        Retrieves the good device in the database and builds the request to send.
        """
        dm = DeviceManager(int(json_obj['data']['room_device_id']), int(json_obj['data']['option_id']), DEBUG_MODE);
        dev = dm.load_from_db();
        if dev is None:
            connection.close();
            return ;
        hostname = '';
        for host in self.hostlist:
            if dev['daemon_name'] in host._Hostname:
                hostname = host._Hostname;
                break;
        if hostname != '':
            if dev['protocol_id'] == PROTOCOL_KNX:
                self.knx_manager.protocol_knx(json_obj, dev, hostname);
            elif dev['protocol_id'] == PROTOCOL_IP:
                json_obj['addr'] = dev['addr'];
                json_obj['port'] = dev['plus1'];
                self.protocol_function[dev['protocol_id']](json_obj, dev, hostname);
        connection.close();

    def protocol_enocean(self, json_obj, dev, hostname):
        """
        TODO
        """
        print("PROTOCOL ENOCEAN:");
        print(str(json_obj));
        print(hostname);
        print('=======');

    def upnp_set_play(self, json_obj, dev, hostname):
        """
        Send "play" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_play();

    def upnp_set_pause(self, json_obj, dev, hostname):
        """
        Send "pause" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_pause();

    def upnp_set_stop(self, json_obj, dev, hostname):
        """
        Send "stop" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_stop();

    def upnp_set_mute(self, json_obj, dev, hostname):
        """
        Send "mute" command to the Upnp device described in dev
        """
        mute = UpnpAudio(json_obj['addr'], int(json_obj['port'])).get_mute();
        mute = (int(mute)+1)%2;
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_mute(mute = mute);
        
    def upnp_set_next(self, json_obj, dev, hostname):
        """
        Send "next" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_next();

    def upnp_set_prev(self, json_obj, dev, hostname):
        """
        Send "prev" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_previous();

    def upnp_set_volume_up(self, json_obj, dev, hostname):
        """
        Send "volume_up" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_volume_inc();

    def upnp_set_volume_down(self, json_obj, dev, hostname):
        """
        Send "volume_down" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_volume_dec();

    def upnp_set_volume(self, json_obj, dev, hostname):
        """
        Send "set_volume" command to the Upnp device described in dev
        """
        UpnpAudio(json_obj['addr'], int(json_obj['port'])).set_volume(desired_volume = int(json_obj['data']['value']));

    def protocol_ip(self, json_obj, dev, hostname):
        """
        Callback called each time a protocol_ip packet is received.
        Calls the desired Upnp function.
        """
        print('PROTOCOL IP PACKET')
        if json_obj['data']['action'] in self.upnp_function.keys():
            self.upnp_function[json_obj['data']['action']](json_obj, dev, hostname);

    def get_ip_ifname(self, ifname):
        """
        Retrieves network interface name from IP address.
        """
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM);
        try:
            res = socket.inet_ntoa(fcntl.ioctl(s.fileno(),
                                               0x8915,
                                               struct.pack('256s', bytes(ifname, 'utf-8')))[20:24]);
            return res;
        except Exception as e:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in get_ip_ifname: ' + str(e));
            return None;

    def cron_upnp(self, json_obj, connection):
        """
        Callback called each time a cron_upnp packet is received.
        """
        local_ip = self.get_ip_ifname("eth0");
        if local_ip is None:
            connection.close();
            return None;
        query = "SELECT configuration_id, configuration_value FROM configuration";
        res = self.sql.mysql_handler_personnal_query(query);
        print(query);
        actions = json_obj['data'];
        for act in actions:
            if act['action'] == 'open':
                for r in res:
                    if int(r[0]) == int(act['configuration_id']):
                        if int(r[0]) == 1:
                            print(["upnpc", "-a", local_ip, str(r[1]), "80", act['protocol']]);
                            call(["upnpc", "-a", local_ip, str(r[1]), "80", act['protocol']]);
                        elif int(r[0]) == 2:
                            print(["upnpc", "-a", local_ip, str(r[1]), "443", act['protocol']]);
                            call(["upnpc", "-a", local_ip, str(r[1]), "443", act['protocol']]);
            elif act['action'] == 'close':
                for r in res:
                    if int(r[0]) == int(act['configuration_id']):
                        print('calling ' + str(["upnpc", "-d", local_ip, str(r[1]), act['protocol']]));
                        call(["upnpc", "-d", local_ip, str(r[1]), act['protocol']]);

    def reload_camera(self, json_obj, connection):
        """
        Generation of the file camera.conf located in /etc/greenleaf by default.
        """
        camera_file = open(CAMERA_CONF_FILE, 'w');
        query = "SELECT room_device_id, addr, plus1 FROM room_device WHERE protocol_id = 6";
        res = self.sql.mysql_handler_personnal_query(query);
        for r in res:
            if r[1] != '':
                camera_file.write("location /camera/" + str(r[0]))
                camera_file.write("/ {\n")
                camera_file.write("\tproxy_buffering off;\n")
                camera_file.write("\tproxy_pass http://" + str(r[1]))
                camera_file.write(":" + str(r[2]) + "/;\n}\n\n");
        camera_file.close();
        call(["systemctl", "restart", "nginx"]);

    def reload_d3config(self, json_obj, connection):
        """
        Loads port config. Reading in database and storing.
        """
        query = "SELECT configuration_id, configuration_value FROM configuration";
        res = self.sql.mysql_handler_personnal_query(query);
        for r in res:
            self.d3config[str(r[0])] = r[1];

    def check_slave(self, json_obj, connection):
        """
        Asks "check_slave" to the slave described in json_obj and waits for answer.
        """
        query = "SELECT serial, secretkey FROM daemon WHERE daemon_id=" + str(json_obj['data']['daemon_id']);
        res = self.sql.mysql_handler_personnal_query(query);
        if res is None or len(res) == 0:
            self.logger.error('in check_slave: No daemon for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in check_slave: Too much daemons for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        if hostname == socket.gethostname():
            file = open('/etc/greenleaf/.glslave.version', 'r');
            version = file.read().split('\n')[0];
            connection.send(bytes(version, 'utf-8'));
            query = 'UPDATE daemon SET validation=1, version="' + version + '" WHERE serial="' + socket.gethostname() + '"';
            self.sql.mysql_handler_personnal_query(query);
            connection.close();
            return ;
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if ip == '':
            self.logger.error('in check_slave: ' + hostname + ' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = '{"packet_type": "check_slave", "sender_name": "' + self_hostname + '"}';
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (176 - len(obj_to_send)) * ' '));
        rlist, wlist, elist = select.select([sock], [], [], SELECT_TIMEOUT * 10);
        val = '0';
        version = '';
        for s in rlist:
            data = sock.recv(4096);
            if not data:
                continue;
            decrypt_IV = data[:16].decode();
            host = None;
            for h in self.hostlist:
                if h._IpAddr == ip:
                    host = h;
            decode_obj = AES.new(res[0][1], AES.MODE_CBC, decrypt_IV);
            data2 = decode_obj.decrypt(data[16:]).decode();
            resp = json.JSONDecoder().decode(data2);
            hostname = host._Hostname;
            if '.' in host._Hostname:
                hostname = host._Hostname.split('.')[0];
            if str(self.aes_slave_keys[hostname]) == str(resp['aes_pass']):
                val = '1';
                version = resp['version'];
            connection.send(bytes(version, 'utf-8'));
        connection.close();
        query = 'UPDATE daemon SET validation=' + val + ', version="' + version + '" WHERE serial="' + hostname + '"';
        self.sql.mysql_handler_personnal_query(query);

    def get_secret_key(self, hostname):
        """
        Retrieves the secretkey of 'hostname' in the database.
        """
        query = 'SELECT serial, secretkey FROM daemon WHERE serial = \'' + hostname + '\'';
        res = self.sql.mysql_handler_personnal_query(query);
        for r in res:
            if r[0] == hostname:
                return str(r[1]);

    def send_mail(self, json_obj, connection):
        """
        Callback called each time a send_mail packet is received.
        The parameters are stored in 'json_obj'.
        """
        try:
            from_addr = formataddr((self.d3config['6'], self.d3config['5']));
            host = self.d3config['7'];
            secure = self.d3config['8']
            port = self.d3config['9'];
            username = self.d3config['10'];
            password = self.d3config['11'];
            msg = MIMEMultipart();
            mdr = json_obj['data']['object'];
            msg['Subject'] = json_obj['data']['object'];
            msg['From'] = from_addr;
            msg['To'] = json_obj['data']['destinator'];
            msg.attach(MIMEText(json_obj['data']['message']));
            server = smtplib.SMTP(host, port);
            if (secure == 2):
                server.ehlo();
                server.starttls();
                server.ehlo();
            if (username != '' and password != ''):
                server.login(self.d3config['5'], username);
            server.sendmail(from_addr, json_obj['data']['destinator'], msg.as_string());
            server.quit();
            connection.close();
        except Exception as e:
            self.logger.error('Error for sending mail');
            self.logger.error(e);
            connection.send(bytes('Error', 'utf-8'));
            connection.close();

    def get_slave_name(self, json_obj, daemons):
        """
        Retrieves the hostname of the daemon described by 'json_obj' in the 'daemons' list.
        """
        daemon_found = False;
        slave_name = '';
        for d in daemons:
            if int(json_obj['data']['daemon']) == int(d[0]):
                daemon_found = True;
                slave_name = str(d[2]);
                break;
        if daemon_found is False:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in get_slave_name: ' + str(json_obj['data']['daemon']));
            return None;
        if str(json_obj['data']['addr']).count('/') != 2:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in get_slave_name: ' + str(json_obj['data']['addr']));
            return None;
        return slave_name;

    def reload_web_server(self):
        """
        Call "systemctl restart nginx"
        """
        self.logger.info('Reloading web server...');
        call(RELOAD_WEB_SERVER_COMMAND);
        self.logger.info('[ OK ] Done reloading web server.');
