#!/usr/bin/python3

## @package domomaster
# Master daemon for D3 boxes
#
# Developed by GreenLeaf.

from inspect import currentframe, getframeinfo;
from Crypto.Cipher import AES;
import smtplib;
from email.mime.multipart import MIMEMultipart;
from email.mime.text import MIMEText;
from email.utils import formataddr;
import copy;
import threading
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
sys.path.append('/usr/lib/domoleaf');
from DeviceManager import *;
from DaemonConfigParser import *;
from MysqlHandler import *;
from Scanner import *;
from UpnpAudio import *;
from Logger import *;
import AESManager;
from SlaveReceiver import *;
from CommandReceiver import *;
from MasterSql import *;
from KNXManager import *;
from EnOceanManager import *;
from UpnpAudio import *;
from IP_IRManager import *;
from Smartcommand import *;
from Trigger import *;
from Schedule import *;
from Scenario import *;
from CalcLogs import *;
import utils;
from GLManager import *;
from HttpReq import *;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'
MASTER_CONF_FILE        = '/etc/domoleaf/master.conf';          # Configuration file name
SELECT_TIMEOUT          = 0.05;                                 # Timeout for the select
MAX_SLAVES              = 100;                                  # Max slave that can be connected at the same time
MAX_CMDS                = 100;                                  # Max domoleaf interface that can be connected at the same time
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

############################################
# Section and field names for dB connexion #
############################################
MASTER_CONF_MYSQL_SECTION               = 'mysql';
MASTER_CONF_MYSQL_USER_ENTRY            = 'user';
MASTER_CONF_MYSQL_PASSWORD_ENTRY        = 'password';
MASTER_CONF_MYSQL_DB_NAME_ENTRY         = 'database_name';

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
DATA_MODIF_DATETIME           = 'modif_date';
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
DATA_CHECK_UPDATES            = 'check_updates';
DATA_UPDATE                   = 'update';
DATA_SMARTCMD_LAUNCH          = 'smartcmd_launch';
DATA_TRIGGERS_LIST_UPDATE     = 'triggers_list_update';
DATA_SCHEDULES_LIST_UPDATE    = 'schedules_list_update';
DATA_SCENARIOS_LIST_UPDATE    = 'scenarios_list_update';
DATA_CHECK_ALL_SCHEDULES      = 'check_all_schedules';
DATA_CALC_LOGS                = 'calc_logs';
DATA_SEND_ALIVE               = 'send_alive';
DATA_SEND_TECH                = 'send_tech';
DATA_SEND_INTERFACES          = 'send_interfaces';
DATA_SHUTDOWN_D3              = 'shutdown_d3';
DATA_REBOOT_D3                = 'reboot_d3';
DATA_WIFI_UPDATE              = 'wifi_update';
DATA_REMOTE_SQL               = 'remote_sql';

CAMERA_CONF_FILE              = '/etc/domoleaf/devices.conf';         # Path for the cameras configuration file

DEBUG_MODE = True;      # Debug flag

## The main class of the master daemon.
#
# It provides communication between master and slave boxes and a part of the database management.
class MasterDaemon:

    ## The constructor
    #
    # Initializes and launches the MasterDaemon.
    # @param log_flag A flag saying if whether the logs should be written in the log file, or not.
    def __init__(self, log_flag):
        ## Logger object for formatting and printing logs
        self.logger = Logger(log_flag, LOG_FILE);
        self.logger.info('Started Domoleaf Master Daemon');
        ## Configuration object of the D3
        self.d3config = {};
        ## AES keys for the slave daemon to encrypt communications
        self.aes_slave_keys = {};
        ## AES key for the master daemon to decrypt incomming communications
        self.aes_master_key = None;
        ## Object containing the connected slave daemons
        self.connected_clients = {};
        ## SQL object for managing database
        self.sql = MasterSql();
        self._parser = DaemonConfigParser(MASTER_CONF_FILE);
        ## Username for the database, searched in configuration file
        self.db_username = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION, MASTER_CONF_MYSQL_USER_ENTRY);
        ## Password for the database, searched in configuration file
        self.db_passwd = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION, MASTER_CONF_MYSQL_PASSWORD_ENTRY)
        ## The database name, searched in configuration file
        self.db_dbname = self._parser.getValueFromSection(MASTER_CONF_MYSQL_SECTION, MASTER_CONF_MYSQL_DB_NAME_ENTRY);
        self.get_aes_slave_keys(0);
        self.reload_camera(None, None, 0);
        self._scanner = Scanner();
        ## The hostlist containing the hosts on the local network
        self.hostlist = [];
        self.hostlist.append(Host('', '127.0.0.1', socket.gethostname().upper()));
        ## KNX manager object for communications with KNX devices
        self.knx_manager = KNXManager(self.aes_slave_keys);
        ## EnOcean manager object for communications with EnOcean devices
        self.enocean_manager = EnOceanManager(self.aes_slave_keys);
        self.reload_d3config(None, None, 0);
        ## Trigger object manager
        self.trigger = Trigger(self);
        ## Scenario object manager
        self.scenario = Scenario(self);
        ## Schedule object manager
        self.schedule = Schedule(self);
        ## CalcLogs manager for optimising logs
        self.calcLogs = CalcLogs(self);

        ## Functions array with option ID
        self.functions = {
            1 : self.knx_manager.send_knx_write_short_to_slave,
            2 : self.knx_manager.send_knx_write_long_to_slave,
            3 : self.knx_manager.send_knx_write_speed_fan,
            4 : self.knx_manager.send_knx_write_temp,
            5 : IP_IRManager().send_to_gc,
            6 : self.knx_manager.send_on,
            7 : self.knx_manager.send_to_thermostat,
            8 : self.knx_manager.send_clim_mode,
            9 : HttpReq().http_action,
            10 : self.upnp_audio,
            11 : self.knx_manager.send_knx_write_percent,
            12 : self.knx_manager.send_off,
            13 : self.knx_manager.send_knx_write_short_to_slave_reverse
        };

        ## Functions array depending on data type
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
            DATA_MODIF_DATETIME               : self.modif_datetime,
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
            DATA_SMARTCMD_LAUNCH              : self.smartcmd_launch,
            DATA_TRIGGERS_LIST_UPDATE         : self.triggers_list_update,
            DATA_SCHEDULES_LIST_UPDATE        : self.schedules_list_update,
            DATA_SCENARIOS_LIST_UPDATE        : self.scenarios_list_update,
            DATA_CHECK_ALL_SCHEDULES          : self.check_schedules,
            DATA_CALC_LOGS                    : self.launch_calc_logs,
            DATA_CHECK_UPDATES                : self.check_updates,
            DATA_UPDATE                       : self.update,
            DATA_SEND_ALIVE                   : self.send_request,
            DATA_SEND_TECH                    : self.send_tech,
            DATA_SEND_INTERFACES              : self.send_interfaces,
            DATA_SHUTDOWN_D3                  : self.shutdown_d3,
            DATA_REBOOT_D3                    : self.reboot_d3,
            DATA_WIFI_UPDATE                  : self.wifi_update,
            DATA_REMOTE_SQL                   : self.remote_sql
        };

    ## Gets the secretkeys of each slave daemon stored in database.
    #
    # @param db The database handler used to query the database.
    # @return None
    def get_aes_slave_keys(self, db):
        query = "SELECT serial, secretkey FROM daemon";
        res = self.sql.mysql_handler_personnal_query(query, db);
        self_hostname = socket.gethostname();
        for r in res:
            if SLAVE_NAME_PREFIX in r[0] or 'MD3' in r[0]:
                self.aes_slave_keys[r[0]] = r[1];
            elif self_hostname == r[0]:
                self.aes_slave_keys[r[0]] = r[1];
                self.aes_master_key = r[1];

    ## Stops the daemon and closes sockets.
    #
    # @return None
    def stop(self):
        flag = False;
        while not flag:
            flag = True;
            for client in self.connected_clients.values():
                flag = False;
                client.close();
                break;
        self.slave_connection.close();
        sys.exit(0);

    ## Initializes the connections and accepts incomming communications.
    #
    # @return None
    def run(self):
        ## Main socket for listening incomming connections for slave daemons
        self.slave_connection = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        ## Main socket for listening incomming connections for MasterCommand
        self.cmd_connection = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        self.slave_connection.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
        self.cmd_connection.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
        self.slave_connection.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        self.cmd_connection.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        s_port = self._parser.getValueFromSection(MASTER_CONF_LISTEN_SECTION, MASTER_CONF_LISTEN_PORT_SLAVE_ENTRY);
        c_port = self._parser.getValueFromSection(MASTER_CONF_LISTEN_SECTION, MASTER_CONF_LISTEN_PORT_CMD_ENTRY);
        if not s_port:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in run: No slave listening port defined in '+MASTER_CONF_FILE);
            sys.exit(1);
        if not c_port:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in run: No command listening port defined in '+MASTER_CONF_FILE);
            sys.exit(1);
        self.slave_connection.bind(('', int(s_port)));
        self.slave_connection.listen(MAX_SLAVES);
        self.cmd_connection.bind(('', int(c_port)));
        self.cmd_connection.listen(MAX_CMDS);
        self.loop();

    ## Master daemon main loop.
    #
    # Waits for new connections.
    #
    # @return None
    def loop(self):
        ## Flag set True for running, False to stop the main loop
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
                self.stop();
                sys.exit(0);
            except ValueError as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Value error: '+str(e));
                pass;

    ## Gets new domoleaf connections and threads the treatment.
    #
    # @param connection The connection object used to listen and accept the incomming connection from the MasterCommand.
    # @return None
    def accept_new_cmd_connection(self, connection):
        new_connection, addr = connection.accept();
        r = CommandReceiver(new_connection, self);
        r.start();

    ## Gets new slave connections and threads the treatment.
    #
    # @param connection The connection object used to listen and accept the incomming connection from a slave.
    # @return None
    def accept_new_slave_connection(self, connection):
        new_connection, addr = connection.accept();
        myname = socket.gethostname();
        try:
            name = socket.gethostbyaddr(addr[0])[0]
        except socket.error as serr:
            name = 'localhost'
        if name == 'localhost':
            name = myname
        name = name.split('.')[0];
        r = SlaveReceiver(new_connection, name, self);
        r.start();

    ## Once data are received whether from domoleaf or slave, the function of the packet_type in data is called.
    #
    # @param data The data received to be parsed.
    # @param connection The connection object used to communicate.
    # @param daemon_id The ID of the slave daemon who sent the data.
    # @param db The database handler.
    # @return None
    def parse_data(self, data, connection, daemon_id, db):
        json_obj = json.JSONDecoder().decode(data);
        json_obj['daemon_id'] = daemon_id;
        if json_obj['packet_type'] in self.data_function.keys():
            self.data_function[json_obj['packet_type']](json_obj, connection, db);
        else:
            frameinfo = getframeinfo(currentframe());

    ## Checks if updates are available for the MasterDaemon package.
    #
    # If an update is available, it is installed.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db The database handler.
    # @return None
    def check_updates(self, json_obj, connection, db):
        query = 'SELECT configuration_value FROM configuration WHERE configuration_id=4';
        actual_version = self.sql.mysql_handler_personnal_query(query, db);
        if not actual_version:
            self.logger.error("CHECK_UPDATE : No Master Version");
            return;
        query = 'UPDATE configuration SET configuration_value="" WHERE configuration_id=13';
        self.sql.mysql_handler_personnal_query(query, db);
        p = call(['dpkg', '--configure', '-a'])
        p = Popen(['apt-get', 'update'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        output, error = p.communicate();
        p = Popen(['apt-show-versions',  '-u', 'domomaster'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        output, error = p.communicate();
        if not p.returncode:
            tab = output.decode("utf-8").split(" ");
            version = tab[-1].rsplit("\n")[0];
        else:
            version = actual_version[0][0];
        query = ''.join(['UPDATE configuration SET configuration_value="', version, '" WHERE configuration_id=13']);
        self.sql.mysql_handler_personnal_query(query, db);

    ## Updates the package list (apt) and the MasterDaemon.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Not used here.
    # @param db The database handler.
    # @return None
    def update(self, json_obj, connection, db):
        call(['apt-get', 'update']);
        p = Popen("DEBIAN_FRONTEND=noninteractive apt-get install domomaster domoslave -y ",
              shell=True, stdin=None, stdout=False, stderr=False,executable="/bin/bash");
        output, error = p.communicate();
        hostname = socket.gethostname();
        if '.' in hostname:
            hostname = hostname.split('.')[0];
        version = os.popen("dpkg-query -W -f='${Version}\n' domomaster").read().split('\n')[0];
        query = ''.join(['UPDATmon SET version="', version, '" WHERE name="', hostname, '"' ]);
        self.sql.mysql_handler_personnal_query(query, db);
        query = ''.join(['UPDATE configuration SET configuration_value="', version, '" WHERE configuration_id=4']);
        self.sql.mysql_handler_personnal_query(query, db);
        json_obj['data'].append(hostname);
        port = self._parser.getValueFromSection('connect', 'port');
        for host in self.hostlist:
            if (host._Hostname.startswith('MD3') or host._Hostname.startswith('SD3')) and host._Hostname not in json_obj['data']:
                sock = socket.create_connection((host._IpAddr, port));
                json_str = json.JSONEncoder().encode(json_obj);
                sock.send(bytes(json_str, 'utf-8'));
                data = sock.recv(4096);
                decrypt_IV = data[:16].decode();
                decode_obj = AES.new(self.aes_master_key, AES.MODE_CBC, decrypt_IV);
                data2 = decode_obj.decrypt(data[16:]).decode();
                version = data2['new_version'];
                query = ''.join(['UPDATE daemon SET version="', version, '" WHERE name="', host._Hostname, '"']);
                self.sql.mysql_handler_personnal_query(query, db);
                sock.close();

    ## Creates a backup of the database stored locally.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def backup_db_create_local(self, json_obj, connection, db):
        path = '/etc/domoleaf/sql/backup/';
        filename = 'domoleaf_backup_';
        t = str(time.time());
        if '.' in t:
            t = t.split('.')[0];
        filename += t+'.sql';
        os.system("mysqldump --defaults-file=/etc/mysql/debian.cnf domoleaf > "+path+filename);
        os.system('cd '+path+' && tar -czf '+filename+'.tar.gz'+' '+filename);
        os.system('rm '+path+filename);

    ## Remove a local database backup.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def backup_db_remove_local(self, json_obj, connection, db):
        filename = ''.join(['/etc/domoleaf/sql/backup/domoleaf_backup_', str(json_obj['data']), '.sql.tar.gz']);
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

    ## List all backups stored locally.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Connection object used to send the result.
    # @param db Not used here.
    # @return None
    def backup_db_list_local(self, json_obj, connection, db):
        json_obj = [];
        append = json_obj.append;
        backup_list = os.listdir('/etc/domoleaf/sql/backup/')
        for f in backup_list:
            s = os.stat('/etc/domoleaf/sql/backup/'+f);
            if '.sql' in f:
                g = f.split('.sql')[0];
                append({"name": g, "size": s.st_size});
        json_sorted = sorted(json_obj, key=lambda json_obj: json_obj['name'], reverse=True);
        json_str = json.JSONEncoder().encode(json_sorted);
        connection.send(bytes(json_str, 'utf-8'));

    ## Restores the database from a local backup
    #
    # @param json_obj JSON object containing some data.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def backup_db_restore_local(self, json_obj, connection, db):
        path = '/etc/domoleaf/sql/backup/';
        filename = ''.join(['domoleaf_backup_', str(json_obj['data']), '.sql.tar.gz']);
        if json_obj['data'][0] == '.' or json_obj['data'][0] == '/':
            self.logger.error('The filename is corrupted. Aborting database restoring.')
            return;
        try:
            os.stat(path+filename);
            os.system('cd '+path+' && tar -xzf '+filename);
            os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < '+path+filename.split('.tar.gz')[0]);
            os.system('rm '+path+filename.split('.tar.gz')[0]);
            return;
        except Exception as e:
            try:
                filename = filename.split('.tar.gz')[0];
                os.stat(path+filename);
            except Exception as e:
                self.logger.error("The database file to restore does not exists.");
                self.logger.error(e);
                return;
        os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < '+path+filename);

    ## Checks if an USB device is plugged in.
    #
    # @param json_obj JSON object containing data, and the result.
    # @param connection The connection object used to send the result.
    # @param db The database handler.
    # @return None
    def check_usb(self, json_obj, connection, db):
        try:
            sdx1 = glob.glob('/dev/sd?1')[0];
        except Exception as e:
            return;
        if not (os.path.exists(sdx1)):
            json_obj = 0;
        else:
            json_obj = 1;
        json_str = json.JSONEncoder().encode(json_obj);
        connection.send(bytes(json_str, 'utf-8'));

    ## List backups stored on an USB device
    #
    # @param json_obj JSON object reinitialized to store the result.
    # @param connection Connection object used to send the result.
    # @param db Not used here.
    # @return None
    def backup_db_list_usb(self, json_obj, connection, db):
        json_obj = [];
        append = json_obj.append
        sdx1 = glob.glob('/dev/sd?1')[0];
        if not (os.path.exists(sdx1)):
            return;
        os.system('mount '+sdx1+' /etc/domoleaf/mnt');
        os.system('mkdir -p /etc/domoleaf/mnt/backup');
        backup_list = os.listdir('/etc/domoleaf/mnt/backup/')
        for f in backup_list:
            s = os.stat('/etc/domoleaf/mnt/backup/'+f);
            if '.sql' in f:
                g = f.split('.sql')[0];
                append({"name": g, "size": s.st_size});
        os.system('umount /etc/domoleaf/mnt');
        json_sorted = sorted(json_obj, key=lambda json_obj: json_obj['name'], reverse=True);
        json_str = json.JSONEncoder().encode(json_sorted);
        connection.send(bytes(json_str, 'utf-8'));

    ## Removes a backup stored on an USB device
    #
    # @param json_obj JSON object containing data.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def backup_db_remove_usb(self, json_obj, connection, db):
        filename = ''.join(['/etc/domoleaf/mnt/backup/domoleaf_backup_', str(json_obj['data']), '.sql.tar.gz']);
        if str(json_obj['data'][0]) == '.' or str(json_obj['data'][0]) == '/':
            self.logger.error('The filename is corrupted. Aborting database file removing.')
            return;
        sdx1 = glob.glob('/dev/sd?1')[0];
        if not (os.path.exists(sdx1)):
            return;
        os.system('mount '+sdx1+' /etc/domoleaf/mnt');
        path = '/etc/domoleaf/mnt/backup/';
        try:
            os.stat(filename);
        except Exception as e:
            try:
                filename = filename.split('.tar.gz')[0];
                os.stat(filename);
            except Exception as e:
                self.logger.error("The database file to remove does not exists.")
                self.logger.error(e)
                os.system('umount /etc/domoleaf/mnt');
                return;
        os.remove(filename);
        os.system('umount /etc/domoleaf/mnt');

    ## Restores the database from a backup stored on an USB device
    #
    # @param json_obj JSON object containing some data.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def backup_db_restore_usb(self, json_obj, connection, db):
        path = '/etc/domoleaf/mnt/backup/';
        filename = ''.join(['domoleaf_backup_', str(json_obj['data']), '.sql']);
        if json_obj['data'][0] == '.' or json_obj['data'][0] == '/':
            self.logger.error('The filename is corrupted. Aborting database restoring.')
            return;
        sdx1 = glob.glob('/dev/sd?1')[0];
        if not (os.path.exists(sdx1)):
            return;
        os.system('mount '+sdx1+' /etc/domoleaf/mnt');
        try:
            os.stat(path+filename);
            os.system('cp '+path+filename+' /tmp/ && umount /etc/domoleaf/mnt && cd /tmp/');
            os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /tmp/'+filename);
            os.remove('/tmp/'+filename);
            return;
        except Exception as e:
            try:
                filename += '.tar.gz';
                os.stat(path+filename);
                os.system('cp '+path+filename+' /tmp/ && umount /etc/domoleaf/mnt && cd /tmp/ && tar -xzf '+filename);
            except Exception as e:
                self.logger.error("The database file to restore does not exists.");
                self.logger.error(e);
                os.system('umount /etc/domoleaf/mnt');
                return;
        os.system('umount /etc/domoleaf/mnt');
        os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /tmp/'+filename.split('.tar.gz')[0]);
        os.remove('/tmp/'+filename);
        os.remove('/tmp/'+filename.split('.tar.gz')[0]);

    ## Creates a backup of the database and stores it on an USB device.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def backup_db_create_usb(self, json_obj, connection, db):
        sdx1 = glob.glob('/dev/sd?1')[0];
        if not (os.path.exists(sdx1)):
            return;
        os.system('mount '+sdx1+' /etc/domoleaf/mnt');
        path = '/etc/domoleaf/mnt/backup/';
        filename = 'domoleaf_backup_';
        os.system('mkdir -p '+path);
        t = str(time.time());
        if '.' in t:
            t = t.split('.')[0];
        filename += t+'.sql';
        os.system("mysqldump --defaults-file=/etc/mysql/debian.cnf domoleaf > "+path+filename);
        os.system('cd '+path+' && tar -czf '+filename+'.tar.gz'+' '+filename);
        os.system('rm '+path +filename);
        os.system('umount /etc/domoleaf/mnt');

    ## Callback called each time a monitor_knx packet is received.
    # Updates room_device_option values in the database and checks scenarios.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def monitor_knx(self, json_obj, connection, db):
        daemon_id = self.sql.update_knx_log(json_obj, db);
        doList = self.knx_manager.update_room_device_option(daemon_id, json_obj, db);
        if doList:
            self.scenario.setValues(self.get_global_state(db), self.trigger, self.schedule, connection, doList);
            self.scenario.start();
        connection.close();

    ## Callback called each time a knx_write_short packet is received.
    # Updates room_device_option values in the database.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Connection object used to communicate.
    # @param db The database handler
    # @return None
    def knx_write_short(self, json_obj, connection, db):
        daemons = self.sql.get_daemons(db);
        slave_name = self.get_slave_name(json_obj, daemons);
        if slave_name is None:
            connection.close();
            return None;
        dev = {}
        dev["addr_dst"] = json_obj['data']['addr']
        slave_name = slave_name.split('.')[0];
        self.knx_manager.send_knx_write_short_to_slave(json_obj, dev, slave_name);
        connection.close();
        return None;

    ## Callback called each time a knx_write_long packet is received.
    # Updates room_device_option values in the database.
    #
    # @param json_obj JSON object containing some data.
    # @param connection The connection object used to communicate.
    # @param db The database handler.
    # @return None
    def knx_write_long(self, json_obj, connection, db):
        daemons = self.sql.get_daemons(db);
        slave_name = self.get_slave_name(json_obj, daemons);
        if slave_name is None:
            connection.close();
            return None;
        dev = {}
        dev["addr_dst"] = json_obj['data']['addr']
        slave_name = slave_name.split('.')[0];
        self.knx_manager.send_knx_write_long_to_slave(json_obj, dev, slave_name);
        connection.close();
        return None;

    ## Callback called each time a knx_read packet is received.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def knx_read(self, json_obj, connection, db):
        daemons = self.sql.get_daemons(db);
        slave_name = self.get_slave_name(json_obj, daemons);
        if slave_name is None:
            return None;
        slave_name = slave_name.split('.')[0];
        self.knx_manager.send_knx_read_request_to_slave(slave_name, json_obj);
        connection.close();

    ## Callback called each time a monitor_ip packet is received.
    # A new local network scan is performed and the result is stored in the database.
    #
    # @param json_obj Not used here.
    # @param connection Connection object, juste closed here.
    # @param db The database handler.
    # @return None
    def monitor_ip(self, json_obj, connection, db):
        self.scanner.scan();
        self.sql.insert_hostlist_in_db(self.scanner._HostList, db);
        self.hostlist = self.scanner._HostList;
        connection.close();

    ## FUNCTION NOT IMPLEMENTED
    #
    # @param json_obj UNUSED
    # @param connection UNUSED
    # @param db UNUSED
    # @return None
    def monitor_bluetooth(self, json_obj, connection, db):
        connection.close();
        return None;

    ## Callback called each time a monitor_enocean packet is received.
    # Stores the data in enocean_log table.
    #
    # @param json_obj JSON object containing the enocean packet data.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def monitor_enocean(self, json_obj, connection, db):
        daemon_id = self.sql.update_enocean_log(json_obj, db);
        doList = self.enocean_manager.update_room_device_option(daemon_id, json_obj, db);
        connection.close();
        if doList:
            self.scenario.setValues(self.get_global_state(db), self.trigger, self.schedule, connection, doList);
            self.scenario.start();
        return None;

    ## Retrieves the good device in the database and builds the request to send.
    #
    # @param json_obj JSON object containing data about room device and option.
    # @param connection Connection object, just closed here.
    # @param db The database handler.
    # @return None
    def send_to_device(self, json_obj, connection, db):
        hostname = '';
        dm = DeviceManager(int(json_obj['data']['room_device_id']), int(json_obj['data']['option_id']), DEBUG_MODE);
        dev = dm.load_from_db(db);
        if dev is None:
            connection.close();
            return ;
        if 'daemon_name' in dev:
            for host in self.hostlist:
                if dev['daemon_name'] == host._Hostname:
                    hostname = host._Hostname;
                    break;
        function_writing = int(dev['function_writing']);
        if (function_writing > 0):
            try:
                self.functions[function_writing](json_obj, dev, hostname);
            except Exception as e:
                self.logger.error(e);
        connection.close();

    ## Sends an UPnP packet to and audio device.
    #
    # @param json_obj JSON object containing the data about the audio device.
    # @param dev Other informations about the device.
    # @param hostname The hostname of the audio device.
    # @return None
    def upnp_audio(self, json_obj, dev, hostname):
        cmd = UpnpAudio(dev['addr'], int(dev['plus1']));
        cmd.action(json_obj);

    ## Gets network interface IP address from its name.
    #
    # @param ifname The name of the network interface.
    #
    # @return The IP address of the network interface.
    def get_ip_ifname(self, ifname):
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM);
        try:
            res = socket.inet_ntoa(fcntl.ioctl(s.fileno(),
                                               0x8915,
                                               struct.pack('256s', bytes(ifname, 'utf-8')))[20:24]);
            return res;
        except Exception as e:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in get_ip_ifname: '+str(e));
            return None;

    ## Callback called each time a cron_upnp packet is received.
    #
    # @param json_obj JSON object containing whether 'open' or 'close'.
    # @param connection Connection object, just closed here.
    # @param db Not used here.
    # @return None
    def cron_upnp(self, json_obj, connection, db):
        local_ip = self.get_ip_ifname("eth0");
        if local_ip is None:
            connection.close();
            return None;
        query = "SELECT configuration_id, configuration_value FROM configuration";
        res = self.sql.mysql_handler_personnal_query(query);
        actions = json_obj['data'];
        for act in actions:
            if act['action'] == 'open':
                for r in res:
                    if int(r[0]) == int(act['configuration_id']):
                        if int(r[0]) == 1:
                            call(["upnpc", "-a", local_ip, str(r[1]), "80", act['protocol']]);
                        elif int(r[0]) == 2:
                            call(["upnpc", "-a", local_ip, str(r[1]), "443", act['protocol']]);
            elif act['action'] == 'close':
                for r in res:
                    if int(r[0]) == int(act['configuration_id']):
                        call(["upnpc", "-d", str(r[1]), act['protocol']]);

    ## Generates the file /etc/domoleaf/devices.conf to reload informations about cameras.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db The database handler used to query the database.
    # @return None
    def reload_camera(self, json_obj, connection, db):
        camera_file = open(CAMERA_CONF_FILE, 'w');
        query = "SELECT room_device_id, addr, plus1 FROM room_device WHERE protocol_id = 6";
        res = self.sql.mysql_handler_personnal_query(query, db);
        for r in res:
            ip = str(r[1]);
            if r[1] and utils.is_valid_ip(ip):
                camera_file.write("location /device/"+str(r[0]));
                camera_file.write("/ {\n")
                camera_file.write("\tproxy_buffering off;\n")
                camera_file.write("\tproxy_pass http://"+ip);
                if str(r[2]).isdigit():
                    camera_file.write(":"+str(r[2])+"/;\n}\n\n");
                else:
                    camera_file.write(":/;\n}\n\n");
        camera_file.close();
        call(["service", "nginx", "restart"]);

    ## Loads port configuration from database and stores.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db The database handler.
    # @return None
    def reload_d3config(self, json_obj, connection, db):
        query = "SELECT configuration_id, configuration_value FROM configuration";
        res = self.sql.mysql_handler_personnal_query(query, db);
        for r in res:
            self.d3config[str(r[0])] = r[1];

    ## Asks "check_slave" to the slave described in json_obj and waits for answer.
    #
    # @param json_obj JSON object containing data about the daemon_id.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def check_slave(self, json_obj, connection, db):
        query = ''.join(["SELECT serial, secretkey FROM daemon WHERE daemon_id=", str(json_obj['data']['daemon_id'])]);
        res = self.sql.mysql_handler_personnal_query(query, db);
        if res is None or not res:
            self.logger.error('in check_slave: No daemon for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in check_slave: Too much daemons for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        self_hostname = socket.gethostname();
        if hostname == self_hostname:
            ip = '127.0.0.1';
        else:
            ip = '';
            for h in self.hostlist:
                if hostname in h._Hostname.upper():
                    ip = h._IpAddr;
        if not ip:
            self.logger.error('in check_slave: '+hostname+' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = ''.join(['{"packet_type": "check_slave", "sender_name": "', self_hostname, '"}']);
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        rlist, wlist, elist = select.select([sock], [], [], SELECT_TIMEOUT * 10);
        val = '0';
        version = '';
        interface_knx = '';
        interface_enocean = '';
        data = sock.recv(4096);
        if data:
            decrypt_IV = data[:16].decode();
            decode_obj = AES.new(res[0][1], AES.MODE_CBC, decrypt_IV);
            data2 = decode_obj.decrypt(data[16:]).decode();
            resp = json.JSONDecoder().decode(data2);
            if str(self.aes_slave_keys[hostname]) == str(resp['aes_pass']):
                val = '1';
                version = resp['version'];
                interface_knx = resp['interface_knx'];
                interface_enocean = resp['interface_enocean'];
            connection.send(bytes(version, 'utf-8'));
        connection.close();
        query = ''.join(['UPDATE daemon SET validation=', val, ', version="', version, '" WHERE serial="', hostname, '"']);
        self.sql.mysql_handler_personnal_query(query, db);
        query = ''.join(['UPDATE daemon_protocol SET interface="', interface_knx, '" WHERE daemon_id="', str(json_obj['data']['daemon_id']), '" AND protocol_id="1"']);
        self.sql.mysql_handler_personnal_query(query, db);
        query = ''.join(['UPDATE daemon_protocol SET interface="', interface_enocean, '" WHERE daemon_id="', str(json_obj['data']['daemon_id']), '" AND protocol_id="2"']);
        self.sql.mysql_handler_personnal_query(query, db);
        sock.close();

    ## Retrieves the secret key of 'hostname' in the database.
    #
    # @param hostname The hostname of who retrieve the secret key.
    #
    # @return The secret key if it is fond. Else, None.
    def get_secret_key(self, hostname):
        query = ''.join(['SELECT serial, secretkey FROM daemon WHERE serial = \'', hostname, '\'']);
        res = self.sql.mysql_handler_personnal_query(query);
        for r in res:
            if r[0] == hostname:
                return str(r[1]);
        return None;

    ## Callback called each time a send_mail packet is received.
    # The parameters are stored in 'json_obj'.
    #
    # @param json_obj JSON object containing data about the packet.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def send_mail(self, json_obj, connection, db):
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
            if not username and not password:
                server.login(self.d3config['5'], username);
            server.sendmail(from_addr, json_obj['data']['destinator'], msg.as_string());
            server.quit();
            connection.close();
        except Exception as e:
            self.logger.error('Error for sending mail');
            self.logger.error(e);
            connection.send(bytes('Error', 'utf-8'));
            connection.close();

    ## Changes the date / time of the system.
    #
    # @param json_obj JSON object containing the new date / time to set.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def modif_datetime(self, json_obj, connection, db):
        os.system('date --set '+json_obj['data'][0]);
        os.system('date --set '+json_obj['data'][1]);

    ## Retrieves the hostname of the daemon described by 'json_obj' in the daemons list.
    #
    # @param json_obj JSON object containing data about the daemon.
    # @param daemons The daemon list.
    #
    # @return The name of the slave.
    def get_slave_name(self, json_obj, daemons):
        daemon_found = False;
        slave_name = '';
        for d in daemons:
            if int(json_obj['data']['daemon']) == int(d[0]):
                daemon_found = True;
                slave_name = str(d[2]);
                break;
        if daemon_found is False:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in get_slave_name: '+str(json_obj['data']['daemon']));
            return None;
        if str(json_obj['data']['addr']).count('/') != 2:
            frameinfo = getframeinfo(currentframe());
            self.logger.error('in get_slave_name: '+str(json_obj['data']['addr']));
            return None;
        return slave_name;

    ## Calls the command "service reload nginx".
    #
    # @return None
    def reload_web_server(self):
        self.logger.debug('Reloading web server...');
        call(["service", "nginx", "reload"]);
        self.logger.debug('[ OK ] Done reloading web server.');

    ## Starts a smartcommand.
    #
    # @param json_obj JSON object containing informations about the smartcommand to start.
    # @param connection Connection object used to communicate.
    # @param db Not used here.
    # @return None
    def smartcmd_launch(self, json_obj, connection, db):
        s = Smartcommand(self, int(json_obj['data']))
        s.setValues(connection);
        s.start();

    ## Updates the trigger list in database.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db The database handler.
    # @return None
    def triggers_list_update(self, json_obj, connection, db):
        self.trigger.update_triggers_list(db);

    ## Updates the schedule list in database.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db The database handler.
    # @return None
    def schedules_list_update(self, json_obj, connection, db):
        self.schedule.update_schedules_list(db);

    ## Updates the scenario list in database.
    #
    # @param json_obj Not used here.
    # @param connection Not used here.
    # @param db The database handler.
    # @return None
    def scenarios_list_update(self, json_obj, connection, db):
        self.scenario.update_scenarios_list(db);

    ## Checks all schedules.
    #
    # @param json_obj Not used here.
    # @param connection Connection object used to communicate.
    # @param db Not used here.
    # @return None
    def check_schedules(self, json_obj, connection, db):
        self.schedule.check_all_schedules(connection);

    ## Starts the calc logs.
    #
    # @param json_obj Not used here.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def launch_calc_logs(self, json_obj, connection, db):
        try:
            self.calcLogs.sort_logs(connection, db);
        except Exception as e:
            self.logger.error(e);

    ## Retrieves all the options from database.
    #
    # @param db The database handler.
    #
    # @return An array containing the results of the query.
    def get_global_state(self, db):
        query = 'SELECT room_device_id, option_id, opt_value FROM room_device_option';
        res = self.sql.mysql_handler_personnal_query(query, db);
        filtered = [];
        append = filtered.append;
        for elem in res:
            if elem[2]:
                append(elem);
        global_state = [];
        if filtered:
            global_state = filtered;
        else:
            global_state = '';
        return global_state;

    ## Gets the HTTP and SSL option values from the database.
    #
    # @param json_obj JSON object in which the HTTP and SSL option values are stored.
    # @param connection Connection object used to communicate.
    # @param db The database handler.
    # @return None
    def send_tech(self, json_obj, connection, db):
        query = 'SELECT configuration_value FROM configuration WHERE configuration_id=1';
        http = self.sql.mysql_handler_personnal_query(query, db);
        query = 'SELECT configuration_value FROM configuration WHERE configuration_id=2';
        ssl = self.sql.mysql_handler_personnal_query(query, db);
        json_obj['info']['http'] = http[0][0];
        json_obj['info']['ssl']  = ssl[0][0];
        self.send_request(json_obj, connection, db)

    ## Gets the admin address from config file.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Not used here.
    # @param db Not used here.
    # @return None
    def send_request(self, json_obj, connection, db):
        if self._parser.getValueFromSection('greenleaf', 'commercial') == "1":
            admin_addr = self._parser.getValueFromSection('greenleaf', 'admin_addr')
            hostname = socket.gethostname()
            GLManager.SendRequest(str(json_obj), admin_addr, self.get_secret_key(hostname))

    ## Changes the protocol interfaces of the daemon.
    #
    # @param json_obj JSON object containing the daemon data.
    # @param connection Connection object, juste closed here.
    # @param db The database handler.
    # @return None
    def send_interfaces(self, json_obj, connection, db):
        query = ''.join(["SELECT serial, secretkey FROM daemon WHERE daemon_id=", str(json_obj['data']['daemon_id'])]);
        res = self.sql.mysql_handler_personnal_query(query, db);
        if res is None or not res:
            self.logger.error('in send_interfaces: No daemon for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in send_interfaces: Too much daemons for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if not ip:
            self.logger.error('in send_interfaces: '+hostname+' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = json.JSONEncoder().encode(
            {
                "packet_type": "send_interfaces",
                "sender_name": self_hostname,
                "interface_knx": json_obj['data']['interface_knx'],
                "interface_EnOcean": json_obj['data']['interface_EnOcean'],
                "interface_arg_knx": json_obj['data']['interface_arg_knx'],
                "interface_arg_EnOcean": json_obj['data']['interface_arg_EnOcean'],
                "daemon_knx": json_obj['data']['daemon_knx']
            }
        );
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        rlist, wlist, elist = select.select([sock], [], [], SELECT_TIMEOUT * 300);
        re = '';
        data = sock.recv(4096);
        if data:
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
                re = '1';
            connection.send(bytes(re, 'utf-8'));
        connection.close();
        sock.close();

    ## Asks "shutdown_d3" to the slave described in json_obj for shutdown daemon.
    #
    # @param json_obj JSON object containing data describing the daemon to shut down.
    # @param connection Used here to close the connection.
    # @param db The database handler.
    # @return None
    def shutdown_d3(self, json_obj, connection, db):
        query = ''.join(["SELECT serial, secretkey FROM daemon WHERE daemon_id=", str(json_obj['data']['daemon_id'])]);
        res = self.sql.mysql_handler_personnal_query(query, db);
        if res is None or not res:
            self.logger.error('in shutdown_d3: No daemon for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in shutdown_d3: Too much daemons for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if not ip:
            self.logger.error('in shutdown_d3: '+hostname+' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = ''.join(['{"packet_type": "shutdown_d3", "sender_name": "', self_hostname, '"}']);
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        connection.close();
        sock.close();

    ## Asks "reboot_d3" to the slave described in json_obj to reboot the daemon.
    #
    # @param json_obj JSON object containing the description of the slave to reboot.
    # @param connection Used here to close the connection.
    # @param db The database handler.
    # @return None
    def reboot_d3(self, json_obj, connection, db):
        query = ''.join(["SELECT serial, secretkey FROM daemon WHERE daemon_id=", str(json_obj['data']['daemon_id'])]);
        res = self.sql.mysql_handler_personnal_query(query, db);
        if res is None or not res:
            self.logger.error('in reboot_d3: No daemon for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in reboot_d3: Too much daemons for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        if (hostname == self_hostname):
            call(['reboot'])
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if not ip:
            self.logger.error('in reboot_d3: '+hostname+' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = ''.join(['{"packet_type": "reboot_d3", "sender_name": "', self_hostname, '"}']);
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        connection.close();
        sock.close();

    ## Send "wifi_update" to the slave described in json_obj for update the wifi configuration.
    #
    # @param json_obj JSON object containing some data.
    # @param connection User here to close the connection.
    # @param db The database handler.
    # @return None
    def wifi_update(self, json_obj, connection, db):
        query = ''.join(["SELECT serial, secretkey FROM daemon WHERE daemon_id=", str(json_obj['data']['daemon_id'])]);
        res = self.sql.mysql_handler_personnal_query(query, db);
        if res is None or not res:
            self.logger.error('in wifi_update: No daemon for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in wifi_update: Too much daemons for id '+str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if not ip:
            self.logger.error('in wifi_update: '+hostname+' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = ''.join(['{"packet_type": "wifi_update", "sender_name": "', str(self_hostname),
              '", "ssid": "', str(json_obj['data']['ssid']), '", "password": "',
              str(json_obj['data']['password']), '", "security": "', str(json_obj['data']['security']),
              '", "mode": "', str(json_obj['data']['mode']), '"}']);
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        rlist, wlist, elist = select.select([sock], [], [], SELECT_TIMEOUT * 300);
        re = '';
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
                re = '1';
            connection.send(bytes(re, 'utf-8'));
        connection.close();
        sock.close();

    ## Executes SQL command from the configurator.
    #
    # @param json_obj JSON object containing some data.
    # @param connection Used here to close the connection.
    # @return None
    def remote_sql(self, json_obj, connection, db):
        db = MasterSql();
        req = json_obj['data'].split(';');
        for item in req:
            if item != '':
                db.mysql_handler_personnal_query(item);
        connection.close();
        return;
