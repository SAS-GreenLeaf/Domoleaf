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

CAMERA_CONF_FILE              = '/etc/domoleaf/camera.conf';         # Path for the cameras configuration file

DEBUG_MODE = True;      # Debug flag

class MasterDaemon:
    """
    Main class of the master daemon
    It provides communication between master and slave boxes and a part of the database management
    """
    def __init__(self, log_flag):
        self.logger = Logger(log_flag, LOG_FILE);
        self.logger.info('Started Domoleaf Master Daemon');
        self.d3config = {};
        self.aes_slave_keys = {};
        self.aes_master_key = None
        self.connected_clients = {};
        self.sql = MasterSql();
        self._parser = DaemonConfigParser(MASTER_CONF_FILE);
        self.get_aes_slave_keys();
        self.reload_camera(None, None);
        self._scanner = Scanner();
        self.hostlist = [];
        self.hostlist.append(Host('', '127.0.0.1', socket.gethostname().upper()));
        self.knx_manager = KNXManager(self.aes_slave_keys);
        self.enocean_manager = EnOceanManager(self.aes_slave_keys);
        self.reload_d3config(None, None);
        self.trigger = Trigger(self);
        self.schedule = Schedule(self);
        self.scenario = Scenario(self);
        self.calcLogs = CalcLogs(self);
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
        };
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
            DATA_WIFI_UPDATE                  : self.wifi_update
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
        self.slave_connection.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
        self.cmd_connection.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
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
        Gets new domoleaf connections and threads the treatment.
        """
        new_connection, addr = connection.accept();
        r = CommandReceiver(new_connection, self);
        r.start();

    def accept_new_slave_connection(self, connection):
        """
        Gets new slave connections and threads the treatment.
        """
        new_connection, addr = connection.accept();
        name = socket.gethostbyaddr(addr[0])[0]
        myname = socket.gethostname();
        if name == 'localhost':
            name = myname
        if name == myname or name.startswith('MD3') or name.startswith('SD3'):
            name = name.split('.')[0];
            r = SlaveReceiver(new_connection, name, self);
            r.start();

    def parse_data(self, data, connection, daemon_id):
        """
        Once data are received whether from domoleaf or slave, the function of the packet_type in data is called.
        """
        json_obj = json.JSONDecoder().decode(data);
        json_obj['daemon_id'] = daemon_id;
        if json_obj['packet_type'] in self.data_function.keys():
            self.data_function[json_obj['packet_type']](json_obj, connection);
        else:
            frameinfo = getframeinfo(currentframe());

    def check_updates(self, json_obj, connection):
        query = 'SELECT configuration_value FROM configuration WHERE configuration_id=4';
        actual_version = self.sql.mysql_handler_personnal_query(query);
        if not actual_version:
            self.logger.error("CHECK_UPDATE : No Master Version");
            return;
        query = 'UPDATE configuration SET configuration_value="" WHERE configuration_id=13';
        self.sql.mysql_handler_personnal_query(query);
        p = call(['dpkg', '--configure', '-a'])
        p = Popen(['apt-get', 'update'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        output, error = p.communicate();
        p = Popen(['apt-show-versions',  '-u', 'domomaster'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        output, error = p.communicate();
        if p.returncode == 0:
            tab = output.decode("utf-8").split(" ");
            version = tab[-1].rsplit("\n")[0];
        else:
            version = actual_version[0][0];
        query = 'UPDATE configuration SET configuration_value="' + version + '" WHERE configuration_id=13';
        self.sql.mysql_handler_personnal_query(query);

    def update(self, json_obj, connection):
        call(['apt-get', 'update']);
        p = Popen("DEBIAN_FRONTEND=noninteractive apt-get install domomaster domoslave -y ",
              shell=True, stdin=None, stdout=False, stderr=False,executable="/bin/bash");
        output, error = p.communicate();
        hostname = socket.gethostname();
        if '.' in hostname:
            hostname = hostname.split('.')[0];
        version_file = open('/etc/domoleaf/.domomaster.version', 'r');
        if not version_file:
            self.logger.error("File not found: /etc/domoleaf/.domomaster.version");
            print("File not found: /etc/domoleaf/.domomaster.version");
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
            if (host._Hostname.startswith('MD3') or host._Hostname.startswith('SD3')) and host._Hostname not in json_obj['data']:
                sock = socket.create_connection((host._IpAddr, port));
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
        path = '/etc/domoleaf/sql/backup/';
        filename = 'domoleaf_backup_';
        t = str(time.time());
        if '.' in t:
            t = t.split('.')[0];
        filename += t;
        filename += '.sql';
        os.system("mysqldump --defaults-file=/etc/mysql/debian.cnf domoleaf > " + path + filename);
        os.system('cd ' + path + ' && tar -czf ' + filename + '.tar.gz' + ' ' + filename);
        os.system('rm ' + path + filename);

    def backup_db_remove_local(self, json_obj, connection):
        filename = '/etc/domoleaf/sql/backup/domoleaf_backup_';
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
        backup_list = os.listdir('/etc/domoleaf/sql/backup/')
        for f in backup_list:
            s = os.stat('/etc/domoleaf/sql/backup/' + f);
            if '.sql' in f:
                f = f.split('.sql')[0];
                json_obj.append({"name": f, "size": s.st_size});
        json_sorted = sorted(json_obj, key=lambda json_obj: json_obj['name'], reverse=True);
        json_str = json.JSONEncoder().encode(json_sorted);
        connection.send(bytes(json_str, 'utf-8'));

    def backup_db_restore_local(self, json_obj, connection):
        path = '/etc/domoleaf/sql/backup/';
        filename = 'domoleaf_backup_';
        filename += str(json_obj['data']);
        filename += '.sql.tar.gz';
        if json_obj['data'][0] == '.' or json_obj['data'][0] == '/':
            self.logger.error('The filename is corrupted. Aborting database restoring.')
            return;
        try:
            os.stat(path + filename);
            os.system('cd ' + path + ' && tar -xzf ' + filename);
            os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < ' + path + filename.split('.tar.gz')[0]);
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
        os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < ' + path + filename);

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
        os.system('mount ' + sdx1 + ' /etc/domoleaf/mnt');
        os.system('mkdir -p /etc/domoleaf/mnt/backup');
        backup_list = os.listdir('/etc/domoleaf/mnt/backup/')
        for f in backup_list:
            s = os.stat('/etc/domoleaf/mnt/backup/' + f);
            if '.sql' in f:
                f = f.split('.sql')[0];
                json_obj.append({"name": f, "size": s.st_size});
        os.system('umount /etc/domoleaf/mnt');
        json_sorted = sorted(json_obj, key=lambda json_obj: json_obj['name'], reverse=True);
        json_str = json.JSONEncoder().encode(json_sorted);
        connection.send(bytes(json_str, 'utf-8'));

    def backup_db_remove_usb(self, json_obj, connection):
        filename = '/etc/domoleaf/mnt/backup/domoleaf_backup_';
        filename += str(json_obj['data']);
        filename += '.sql.tar.gz';
        if str(json_obj['data'][0]) == '.' or str(json_obj['data'][0]) == '/':
            self.logger.error('The filename is corrupted. Aborting database file removing.')
            return;
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/domoleaf/mnt');
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

    def backup_db_restore_usb(self, json_obj, connection):
        path = '/etc/domoleaf/mnt/backup/';
        filename = 'domoleaf_backup_';
        filename += str(json_obj['data']);
        filename += '.sql';
        if json_obj['data'][0] == '.' or json_obj['data'][0] == '/':
            self.logger.error('The filename is corrupted. Aborting database restoring.')
            return;
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/domoleaf/mnt');
        try:
            os.stat(path + filename);
            os.system('cp ' + path + filename + ' /tmp/ && umount /etc/domoleaf/mnt && cd /tmp/');
            os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /tmp/' + filename);
            os.remove('/tmp/' + filename);
            return;
        except Exception as e:
            try:
                filename = filename + '.tar.gz';
                os.stat(path + filename);
                os.system('cp ' + path + filename + ' /tmp/ && umount /etc/domoleaf/mnt && cd /tmp/ && tar -xzf ' + filename);
            except Exception as e:
                self.logger.error("The database file to restore does not exists.");
                self.logger.error(e);
                os.system('umount /etc/domoleaf/mnt');
                return;
        os.system('umount /etc/domoleaf/mnt');
        os.system('mysql --defaults-file=/etc/mysql/debian.cnf domoleaf < /tmp/' + filename.split('.tar.gz')[0]);
        os.remove('/tmp/' + filename);
        os.remove('/tmp/' + filename.split('.tar.gz')[0]);

    def backup_db_create_usb(self, json_obj, connection):
        sdx1 = glob.glob('/dev/sd?1')[0];
        if (os.path.exists(sdx1) == 0):
            return;
        os.system('mount ' + sdx1 + ' /etc/domoleaf/mnt');
        path = '/etc/domoleaf/mnt/backup/';
        filename = 'domoleaf_backup_';
        os.system('mkdir -p ' + path);
        t = str(time.time());
        if '.' in t:
            t = t.split('.')[0];
        filename += t;
        filename += '.sql';
        os.system("mysqldump --defaults-file=/etc/mysql/debian.cnf domoleaf > " + path + filename);
        os.system('cd ' + path + ' && tar -czf ' + filename + '.tar.gz' + ' ' + filename);
        os.system('rm ' + path + filename);
        os.system('umount /etc/domoleaf/mnt');

    def monitor_knx(self, json_obj, connection):
        """
        Callback called each time a monitor_knx packet is received.
        Updates room_device_option values in the database and check scenarios.
        """
        daemon_id = self.sql.update_knx_log(json_obj);
        doList = self.knx_manager.update_room_device_option(daemon_id, json_obj);
        if len(doList) > 0:
            self.scenario.check_all_scenarios(self.get_global_state(), self.trigger, self.schedule, connection, doList);
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
        
        dev = {}
        dev["addr_dst"] = json_obj['data']['addr']
        slave_name = slave_name.split('.')[0];
        
        self.knx_manager.send_knx_write_short_to_slave(json_obj, dev, slave_name);
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
        dev = {}
        dev["addr_dst"] = json_obj['data']['addr']
        slave_name = slave_name.split('.')[0];
        
        self.knx_manager.send_knx_write_long_to_slave(json_obj, dev, slave_name);
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
        slave_name = slave_name.split('.')[0];
        self.knx_manager.send_knx_read_request_to_slave(slave_name, json_obj);
        connection.close();

    def monitor_ip(self, json_obj, connection):
        """
        Callback called each time a monitor_ip packet is received.
        A new local network scan is performed and the result stored in the database
        """
        self.scanner.scan();
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
        daemon_id = self.sql.update_enocean_log(json_obj);
        doList = self.enocean_manager.update_room_device_option(daemon_id, json_obj);
        connection.close();
        if len(doList) > 0:
            self.scenario.check_all_scenarios(self.get_global_state(), self.trigger, self.schedule, connection, doList);
        return None;

    def send_to_device(self, json_obj, connection):
        """
        Retrieves the good device in the database and builds the request to send.
        """
        hostname = '';
        dm = DeviceManager(int(json_obj['data']['room_device_id']), int(json_obj['data']['option_id']), DEBUG_MODE);
        dev = dm.load_from_db();

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
        #add scenario check here to allow trigger on write ???
        #self.scenario.check_all_scenarios(self.get_global_state(), self.trigger, self.schedule, connection, json_obj);

        connection.close();

    def upnp_audio(self, json_obj, dev, hostname):
        cmd = UpnpAudio(dev['addr'], int(dev['plus1']));
        cmd.action(json_obj);

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
                        print('calling ' + str(["upnpc", "-d", str(r[1]), act['protocol']]));
                        call(["upnpc", "-d", str(r[1]), act['protocol']]);

    def reload_camera(self, json_obj, connection):
        """
        Generation of the file camera.conf located in /etc/domoleaf by default.
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
        call(["service", "nginx", "restart"]);

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
        self_hostname = socket.gethostname();
        if hostname == self_hostname:
            ip = '127.0.0.1';
        else:
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
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = '{"packet_type": "check_slave", "sender_name": "' + self_hostname + '"}';
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
        query = 'UPDATE daemon SET validation=' + val + ', version="' + version + '" WHERE serial="' + hostname + '"';
        self.sql.mysql_handler_personnal_query(query);
        query = 'UPDATE daemon_protocol SET interface="' + interface_knx + '" WHERE daemon_id="' + str(json_obj['data']['daemon_id']) + '" AND protocol_id="1"';
        self.sql.mysql_handler_personnal_query(query);
        query = 'UPDATE daemon_protocol SET interface="' + interface_enocean + '" WHERE daemon_id="' + str(json_obj['data']['daemon_id']) + '" AND protocol_id="2"';
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
        Call "service reload nginx"
        """
        self.logger.info('Reloading web server...');
        call(["service", "nginx", "reload"]);
        self.logger.info('[ OK ] Done reloading web server.');

    def smartcmd_launch(self, json_obj, connection):
        Smartcommand(self, int(json_obj['data'])).launch_smartcmd(json_obj, connection);

    def triggers_list_update(self, json_obj, connection):
        self.trigger.update_triggers_list();

    def schedules_list_update(self, json_obj, connection):
        self.schedule.update_schedules_list();
        
    def scenarios_list_update(self, json_obj, connection):
        self.scenario.update_scenarios_list();

    def check_schedules(self, json_obj, connection):
        self.schedule.check_all_schedules(connection);

    def launch_calc_logs(self, json_obj, connection):
        try:
            self.calcLogs.sort_logs(connection);
        except Exception as e:
            self.logger.error(e);

    def get_global_state(self):
        query = ('SELECT room_device_id, option_id, opt_value FROM room_device_option');
        res = self.sql.mysql_handler_personnal_query(query);
        filtered = [];
        for elem in res:
            if (elem[2] != ''):
                filtered.append(elem);
        global_state = [];
        if (filtered != ''):
            global_state = filtered;
        else:
            global_state = '';
        return global_state;
    
    def send_tech(self, json_obj, connection):
        query = 'SELECT configuration_value FROM configuration WHERE configuration_id=1';
        http = self.sql.mysql_handler_personnal_query(query);
        query = 'SELECT configuration_value FROM configuration WHERE configuration_id=2';
        ssl = self.sql.mysql_handler_personnal_query(query);
        json_obj['info']['http'] = http[0][0];
        json_obj['info']['ssl']  = ssl[0][0];
        self.send_request(json_obj, connection)

    def send_request(self, json_obj, connection):
        if self._parser.getValueFromSection('greenleaf', 'commercial') == "1":
            admin_addr = self._parser.getValueFromSection('greenleaf', 'admin_addr')
            hostname = socket.gethostname()
            GLManager.SendRequest(str(json_obj), admin_addr, self.get_secret_key(hostname))

    def send_interfaces(self, json_obj, connection):
        query = "SELECT serial, secretkey FROM daemon WHERE daemon_id=" + str(json_obj['data']['daemon_id']);
        res = self.sql.mysql_handler_personnal_query(query);
        if res is None or len(res) == 0:
            self.logger.error('in send_interfaces: No daemon for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in send_interfaces: Too much daemons for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if ip == '':
            self.logger.error('in send_interfaces: ' + hostname + ' not in hostlist. Try perform network scan again.');
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

    def shutdown_d3(self, json_obj, connection):
        """
        Asks "shutdown_d3" to the slave described in json_obj for shutdown daemon.
        """
        query = "SELECT serial, secretkey FROM daemon WHERE daemon_id=" + str(json_obj['data']['daemon_id']);
        res = self.sql.mysql_handler_personnal_query(query);
        if res is None or len(res) == 0:
            self.logger.error('in shutdown_d3: No daemon for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in shutdown_d3: Too much daemons for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if ip == '':
            self.logger.error('in shutdown_d3: ' + hostname + ' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = '{"packet_type": "shutdown_d3", "sender_name": "' + self_hostname + '"}';
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        connection.close();

    def reboot_d3(self, json_obj, connection):
        """
        Asks "reboot_d3" to the slave described in json_obj for reboot daemon.
        """
        query = "SELECT serial, secretkey FROM daemon WHERE daemon_id=" + str(json_obj['data']['daemon_id']);
        res = self.sql.mysql_handler_personnal_query(query);
        if res is None or len(res) == 0:
            self.logger.error('in reboot_d3: No daemon for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in reboot_d3: Too much daemons for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if ip == '':
            self.logger.error('in reboot_d3: ' + hostname + ' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = '{"packet_type": "reboot_d3", "sender_name": "' + self_hostname + '"}';
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        sock.send(bytes(aes_IV, 'utf-8') + encode_obj.encrypt(obj_to_send + (spaces * ' ')));
        connection.close();

    def wifi_update(self, json_obj, connection):
        """
        Send "wifi_update" to the slave described in json_obj for update the wifi configuration.
        """
        query = "SELECT serial, secretkey FROM daemon WHERE daemon_id=" + str(json_obj['data']['daemon_id']);
        res = self.sql.mysql_handler_personnal_query(query);
        if res is None or len(res) == 0:
            self.logger.error('in wifi_update: No daemon for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        elif len(res) > 1:
            self.logger.error('in wifi_update: Too much daemons for id ' + str(json_obj['data']['daemon_id']));
            connection.close();
            return ;
        hostname = res[0][0];
        ip = '';
        for h in self.hostlist:
            if hostname in h._Hostname.upper():
                ip = h._IpAddr;
        if ip == '':
            self.logger.error('in wifi_update: ' + hostname + ' not in hostlist. Try perform network scan again.');
            connection.close();
            return ;
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((ip, port));
        self_hostname = socket.gethostname();
        if '.' in self_hostname:
            self_hostname = self_hostname.split('.')[0];
        aes_IV = AESManager.get_IV();
        aes_key = self.get_secret_key(hostname);
        obj_to_send = '{"packet_type": "wifi_update", "sender_name": "' + str(self_hostname) + '", "ssid": "' + str(json_obj['data']['ssid']) + '", "password": "' + str(json_obj['data']['password']) + '", "security": "' + str(json_obj['data']['security']) + '", "mode": "' + str(json_obj['data']['mode']) + '"}';
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
