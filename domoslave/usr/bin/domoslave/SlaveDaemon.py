#!/usr/bin/python3

import hashlib;
from inspect import currentframe, getframeinfo;
from subprocess import call;
import json;
import time;
import codecs;
import socket;
import select;
import sys;
sys.path.append('/usr/lib/domoleaf');
from Scanner import *;
from DaemonConfigParser import *;
from Host import *;
from Logger import *;
import AESManager;
from Crypto.Cipher import AES;
from GLManager import *;
import os;

SLAVE_CONF_FILE                 = '/etc/domoleaf/slave.conf';
HOST_CONF_FILE                  = '/etc/domoleaf/hosts.conf';
MASTER_NAME_PREFIX              = 'MD3';
SLAVE_NAME_PREFIX               = 'SD3';
MAX_MASTERS                     = 100;
MAX_KNX                         = 100;
MAX_ENOCEAN                     = 100;
MAX_CRON                        = 100;
SELECT_TIMEOUT                  = 0.05;
TELEGRAM_LENGTH                 = 6 + 255;
SLAVE_CONF_KNX_SECTION          = 'knx';
SLAVE_CONF_KNX_PORT_ENTRY       = 'port';
SLAVE_CONF_KNX_INTERFACE        = 'interface';
SLAVE_CONF_ENOCEAN_SECTION      = 'enocean';
SLAVE_CONF_ENOCEAN_PORT_ENTRY   = 'port';
SLAVE_CONF_ENOCEAN_INTERFACE    = 'interface';
SLAVE_CONF_LISTEN_SECTION       = 'listen';
SLAVE_CONF_LISTEN_PORT_ENTRY    = 'port';
SLAVE_CONF_CONNECT_SECTION      = 'connect';
SLAVE_CONF_CONNECT_PORT_ENTRY   = 'port';
SLAVE_CONF_PKEY_AES_ENTRY       = 'aes';
SLAVE_CONF_CRON_SECTION         = 'cron';
SLAVE_CONF_CRON_PORT_ENTRY      = 'port';
CALL_GROUPSWRITE                = 'groupswrite';
CALL_GROUPWRITE                 = 'groupwrite';
CALL_GROUPREAD                  = 'groupread';
EIB_URL                         = 'ip:localhost';

RORG_NORMAL                     = 0xF6;
RORG_TEMPERATURE                = 0xA5;

PACKET_TYPE_RADIO_ERP1          = 0x01;

LOG_FILE                        = '/var/log/domoleaf/domoslave.log';

KNX_READ_REQUEST        = 'knx_read_request';
KNX_WRITE_SHORT         = 'knx_write_short';
KNX_WRITE_LONG          = 'knx_write_long';
KNX_WRITE_TEMP          = 'knx_write_temp';
CHECK_SLAVE             = 'check_slave';
MONITOR_IP              = 'monitor_ip';
DATA_UPDATE             = 'update';
SEND_TECH               = 'send_tech';
SEND_ALIVE              = 'send_alive';
SEND_INTERFACES         = 'send_interfaces';
REBOOT_D3               = 'reboot_d3';
WIFI_UPDATE             = 'wifi_update';

def individual2string(addr):
    """
    Conversion of a physical KNX address under uint16 form to string (e.g) 0.0.0
    """
    return ("{0}.{1}.{2}".format((addr >> 12) & 0x0f, (addr >> 8) & 0x0f, addr & 0xff));

def group2string(addr):
    """
    Conversion of a virtual KNX address under uint16 to string (e.g) 0/0/0
    """
    return ("{0}/{1}/{2}".format((addr >> 11) & 0x1f, (addr >> 8) & 0x07, addr & 0xff));

class SlaveDaemon:
    """
    Main slave class
    It does communication between different monitors (KNX, EnOcean... in C) and the masters (servers)
    """
    def __init__(self, log_flag):
        self.logger = Logger(log_flag, LOG_FILE);
        self.logger.info('Started Domoleaf Slave daemon');
        print('######## SLAVE DAEMON #######')
        self.connected_masters = {};
        self.connected_knx = [];
        self.connected_enocean = [];
        self.connected_cron = [];
        self.clients = [];
        self._scanner = Scanner(HOST_CONF_FILE);
        self._scanner.scan(False);
        self._hostlist = self._scanner._HostList;
        self._parser = DaemonConfigParser(SLAVE_CONF_FILE);
        self.encrypt_keys = {};
        self.knx_sock = None;
        self.master_sock = None;
        self.enocean_sock = None;
        self.cron_sock = None;
        self.private_aes = hashlib.md5(self._parser.getValueFromSection('personnal_key', 'aes').encode()).hexdigest();
        self.functions = {
            KNX_READ_REQUEST    : self.knx_read_request,
            KNX_WRITE_SHORT     : self.knx_write_short,
            KNX_WRITE_LONG      : self.knx_write_long,
            KNX_WRITE_TEMP      : self.knx_write_temp,
            CHECK_SLAVE         : self.check_slave,
            MONITOR_IP          : self.monitor_ip,
            DATA_UPDATE         : self.update,
            SEND_TECH           : self.send_tech,
            SEND_ALIVE          : self.send_alive,
            SEND_INTERFACES     : self.send_interfaces,
            REBOOT_D3           : self.reboot_d3,
            WIFI_UPDATE         : self.wifi_update
        };

    def update(self, json_obj, connection):
        call(['apt-get', 'update']);
        call(['DEBIAN_FRONTEND=noninteractive', 'apt-get', 'install', 'domoslave', '-y']);
        version_file = open('/etc/domoleaf/.domoslave.version', 'r');
        if not version_file:
            self.logger.error('/etc/domoleaf/.domoslave.version: no such file or directory');
            print('/etc/domoleaf/.domoslave.version: no such file or directory');
            return;
        version = version_file.read();
        if '\n' in version:
            version = version.split('\n')[0];
        json_str = '{"packet_type": "update_finished", "aes_pass": "' + self.private_aes + '", "new_version": ' + version + '}'
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ')
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        # faut ouvrir une nouvelle socket pour envoyer la nouvelle version
        # connection.send(bytes(encrypt_IV, 'utf-8') + data);

    def run(self):
        """
        Initialization of the sockets for listenning incomming connections.
        Calls the loop function.
        """
        self.run = True;
        
        self.knx_sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        self.master_sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        self.enocean_sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        self.cron_sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
        
        port = self._parser.getValueFromSection(SLAVE_CONF_KNX_SECTION, SLAVE_CONF_KNX_PORT_ENTRY);
        if not port:
            sys.exit(2);
        port_master = self._parser.getValueFromSection(SLAVE_CONF_LISTEN_SECTION, SLAVE_CONF_LISTEN_PORT_ENTRY);
        if not port_master:
            sys.exit(2);
        port_enocean = self._parser.getValueFromSection(SLAVE_CONF_ENOCEAN_SECTION, SLAVE_CONF_ENOCEAN_PORT_ENTRY);
        if not port_enocean:
            sys.exit(2);
        port_cron = self._parser.getValueFromSection(SLAVE_CONF_CRON_SECTION, SLAVE_CONF_CRON_PORT_ENTRY);
        if not port_cron:
            sys.exit(2);
        
        self.knx_sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        self.master_sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        self.enocean_sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        self.cron_sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1);
        
        self.knx_sock.bind(('', int(port)));
        self.master_sock.bind(('', int(port_master)));
        self.enocean_sock.bind(('', int(port_enocean)));
        self.cron_sock.bind(('127.0.0.1', int(port_cron)));
        
        self.knx_sock.listen(MAX_KNX);
        self.master_sock.listen(MAX_MASTERS);
        self.enocean_sock.listen(MAX_ENOCEAN);
        self.cron_sock.listen(MAX_CRON);
        
        self.loop();

    def accept_knx(self):
        """
        Get available sockets for reading on the KNX socket.
        """
        rlist, wlist, elist = select.select([self.knx_sock], [], [], SELECT_TIMEOUT);
        for connection in rlist:
            new_knx, addr = connection.accept();
            self.connected_knx.append(new_knx);
        self.receive_from_knx(self.connected_knx);

    def accept_masters(self):
        """
        Get available sockets for reading on the master socket.
        """
        rlist, wlist, elist = select.select([self.master_sock], [], [], SELECT_TIMEOUT);
        masters_socks = [];
        for item in rlist:
            new_conn, addr = item.accept();
            masters_socks.append(new_conn);
        self.receive_from_masters(masters_socks);

    def accept_enocean(self):
        """
        Get available sockets for reading on the EnOcean socket.
        """
        rlist, wlist, elist = select.select([self.enocean_sock], [], [], SELECT_TIMEOUT);
        enocean_socks = [];
        for item in rlist:
            new_conn, addr = item.accept();
            enocean_socks.append(new_conn);
            self.connected_enocean.append(new_conn);
        self.receive_from_enocean(enocean_socks);
    
    def accept_cron(self):
        """
        Get available sockets for reading on the Cron socket.
        """
        rlist, wlist, elist = select.select([self.cron_sock], [], [], SELECT_TIMEOUT);
        cron_socks = [];
        for item in rlist:
            new_conn, addr = item.accept();
            cron_socks.append(new_conn);
            self.connected_cron.append(new_conn);
        self.receive_from_cron(cron_socks);
    
    def parse_data(self, data, connection):
        """
        Calls the wanted function with the packet_type described in 'data' (JSON syntax)
        """
        json_obj = json.JSONDecoder().decode(data);
        print(json_obj);
        if json_obj['packet_type'] in self.functions.keys():
            self.functions[json_obj['packet_type']](json_obj, connection);
        else:
            raise Exception(str(json_obj['packet_type']) + ": is not a valid packet type");

    def knx_read_request(self, json_obj, connection):
        """
        System call of "groupread" with parameters.
        """
        call(['knxtool', CALL_GROUPREAD, EIB_URL, json_obj['addr_to_read']]);

    def knx_write_temp(self, json_obj, connection):
        """
        System call of "groupwrite" with parameters.
        Almost the same as "knx_write_long" function, except that parameters are not the same
        """
        val = json_obj['value'].split(' ')
        call(['knxtool', CALL_GROUPWRITE, EIB_URL, json_obj['addr_to_send'], val[0], val[1]]);

    def knx_write_short(self, json_obj, connection):
        """
        System call of "groupswrite" with parameters.
        """
        call(['knxtool', CALL_GROUPSWRITE, EIB_URL, json_obj['addr_to_send'], str(json_obj['value'])]);

    def knx_write_long(self, json_obj, connection):
        """
        System call of "groupwrite" with parameters.
        """
        call(['knxtool', CALL_GROUPWRITE, EIB_URL, json_obj['addr_to_send'], str(json_obj['value'])]);

    def receive_from_masters(self, masters_to_read):
        """
        Read data comming from masters and call "parse_data" function.
        """
        for master in masters_to_read:
            data = master.recv(4096);
            decrypt_IV = data[:16].decode();
            decode_obj = AES.new(self.private_aes, AES.MODE_CBC, decrypt_IV);
            data2 = decode_obj.decrypt(data[16:]);
            self.parse_data(data2.decode(), master);

    def receive_from_knx(self, knx_to_read):
        """
        Read data from monitor KNX and transmits to master.
        """
        for knx in knx_to_read:
            data = knx.recv(TELEGRAM_LENGTH);
            if data:
                self.send_knx_data_to_masters(data);
            else:
                if knx in self.connected_knx:
                    knx.close();
                    self.connected_knx.remove(knx);

    def receive_from_enocean(self, enocean_to_read):
        """
        Read data from monitor EnOcean and transmits to master.
        """
        for enocean in enocean_to_read:
            data = enocean.recv(4096);
            if data:
                self.send_enocean_data_to_masters(data);
            else:
                if enocean in self.connected_enocean:
                    enocean.close();
                    self.connected_enocean.remove(enocean);
    
    def receive_from_cron(self, cron_to_read):
        """
        Receive data from Cron and execute it.
        """
        for cron in cron_to_read:
            data = cron.recv(4096);
            if data:
                json_str = json.JSONEncoder().encode(
                    {
                        "packet_type": data.decode()
                    }
                );
                self.parse_data(json_str, cron);
            else:
                if cron in self.connected_cron:
                    cron.close();
                    self.parse_data.remove(cron);
    
    def check_slave(self, json_obj, connection):
        """
        Callback called each time a check_slave packet is received.
        Used to confirm the existence of this daemon.
        """
        print("===== CHECK SLAVE =====");
        print(json_obj);
        print("=======================");
        interface_knx = self._parser.getValueFromSection(SLAVE_CONF_KNX_SECTION, SLAVE_CONF_KNX_INTERFACE);
        interface_enocean = self._parser.getValueFromSection(SLAVE_CONF_ENOCEAN_SECTION, SLAVE_CONF_ENOCEAN_INTERFACE);
        file = open('/etc/domoleaf/.domoslave.version', 'r');
        version = file.read().split('\n')[0];
        json_str = '{"packet_type": "check_slave", "aes_pass": "' + self.private_aes + '", "version": "' + version + '", "interface_knx": "' + interface_knx + '", "interface_enocean": "' + interface_enocean + '"}';
        master_hostname = str(json_obj['sender_name']);
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ')
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        connection.send(bytes(encrypt_IV, 'utf-8') + data);

    def monitor_ip(self, json_obj, connection):
        """
        Re scan the local network to refresh hostlist.
        """
        self._scanner.scan(True);
        self._hostlist = self._scanner._HostList;

    def loop(self):
        """
        Main daemon loop.
        """
        while self.run:
            try:
                self.accept_knx();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_knx: ' + str(e));
                print('in loop accept_knx: ' + str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            try:
                self.accept_masters();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_masters: ' + str(e));
                print('in loop accept_masters: ' + str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            try:
                self.accept_enocean();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_enocean: ' + str(e));
                print('in loop accept_enocean: ' + str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            try:
                self.accept_cron();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_cron: ' + str(e));
                print('in loop accept_cron: ' + str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            
    def stop(self):
        """
        Stop the daemon and closes all sockets.
        """
        for name, sock in self.connected_masters.items():
            sock.close();
        for knx in self.connected_knx:
            knx.close();
        self.knx_sock.close();

    def connect_to_masters(self):
        """
        Stored every device on network which have his hostname beginning by "MD3" and stores it
        in the self.connected_masters dict(), with hostnames as keys and sockets freshly open as values.
        """
        hostname = socket.gethostname()
        self.connected_masters = {};
        for host in self._hostlist:
            if MASTER_NAME_PREFIX in host._Hostname or str(host._IpAddr) == '127.0.0.1':
                port = self._parser.getValueFromSection(SLAVE_CONF_CONNECT_SECTION, SLAVE_CONF_CONNECT_PORT_ENTRY);
                if not port:
                    self.logger.error('in connect_to_masters: No ' + SLAVE_CONF_CONNECT_PORT_ENTRY + ' in ' + SLAVE_CONF_CONNECT_SECTION + ' section or maybe no such ' + SLAVE_CONF_CONNECT_SECTION + ' defined');
                    sys.exit(1);
                try:
                    self.logger.info('Connecting to ' + str(host._IpAddr) + ':' + str(port));
                    sock = socket.create_connection((host._IpAddr, port));
                    hostname = host._Hostname.split('.')[0];
                    self.connected_masters[host._Hostname] = sock;
                except Exception as e:
                    frameinfo = getframeinfo(currentframe());
                    self.logger.error('in connect_to_masters: ' + str(e));
                    pass;
        if SLAVE_NAME_PREFIX in hostname:
            port = self._parser.getValueFromSection(SLAVE_CONF_CONNECT_SECTION, SLAVE_CONF_CONNECT_PORT_ENTRY);
            if not port:
                self.logger.error('in connect_to_masters: No ' + SLAVE_CONF_CONNECT_PORT_ENTRY + ' in ' + SLAVE_CONF_CONNECT_SECTION + ' section or maybe no such ' + SLAVE_CONF_CONNECT_SECTION + ' defined');
                sys.exit(1);
            try:
                self.logger.info('Connecting to 127.0.0.1:' + str(port));
                sock = socket.create_connection(('127.0.0.1', port));
                self.connected_masters[hostname] = sock;
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in connect_to_masters: ' + str(e));
                pass;

    def send_knx_data_to_masters(self, data):
        """
        Converts 'data' from bytes to a clear KNX datagran, and sends it to available slaves.
        """
        ctrl = int(data[0]);
        src_addr = int.from_bytes(data[1:3], byteorder='big');
        dst_addr = int.from_bytes(data[3:5], byteorder='big');
        data_len = int.from_bytes(data[5:6], byteorder='big');
        telegram_data = data[6:7 + data_len];
        typ = -1;
        value = 0;
        if telegram_data[1] & 0xC0 == 0x00:             # read
            typ = 0;
        elif telegram_data[1] & 0xC0 == 0x40:           # resp
            typ = 1;
            if data_len == 2:
                value = int(telegram_data[1] & 0x0f);
            elif data_len > 2:
                value = int.from_bytes(telegram_data[2:data_len], byteorder='big');
        elif telegram_data[1] & 0xC0 == 0x80:           # write
            typ = 2;
            if data_len == 2:
                value = int(telegram_data[1] & 0x0f);
            elif data_len > 2:
                typ = 3;
                value = int.from_bytes(telegram_data[2:data_len], byteorder='big');
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "monitor_knx",
                "type": typ,
                "src_addr": individual2string(src_addr),
                "dst_addr": group2string(dst_addr),
                "date": str(time.time()).split('.')[0],
                "value": value,
                "sender_name": socket.gethostname()
            }
        );
        print('===== SENDING KNX DATA =====')
        print(json_str)
        print('============================')
        print()
        self.send_data_to_all_masters(json_str);

    def send_enocean_data_to_masters(self, data):
        """
        Converts 'data' from bytes to a clear EnOcean datagran, and sends it to available slaves.
        """
        self.connect_to_masters();
        if (data[4] == PACKET_TYPE_RADIO_ERP1): # si le packet_type == radio_erp1
            data_len = int.from_bytes(data[1:2], byteorder='big');
            opt_data_len = int(data[3]);
            print(str(data_len) + ' - ' + str(opt_data_len));
            src_str = "%X" % int.from_bytes(data[1+data_len:5+data_len], byteorder='big');
            if len(src_str) < 8:
                src_str = "0" + src_str;
            json_dict = {
                "packet_type": "monitor_enocean",
                "src_addr": src_str,
                "dst_addr": "%X" % int.from_bytes(data[261:265 + opt_data_len], byteorder='big'),
                "date": str(time.time()).split('.')[0],
                "sender_name": socket.gethostname(),
                "type": int(data[6])
            };
            if data[6] == RORG_NORMAL:
                json_dict['value'] = int(data[7]);
            elif data[6] == RORG_TEMPERATURE:
                json_dict['value'] = float(40 - ((40 / 255) * int(data[9])));
            json_str = json.JSONEncoder().encode(json_dict);
            self.send_data_to_all_masters(json_str);

    def send_data_to_all_masters(self, json_str):
        """
        Sends a string 'json_str' to available slaves on network.
        """
        self.connect_to_masters();
        # ici envoyer a tous les masters
        for name in self.connected_masters.keys():
            try:
                master = self.connected_masters[name];
                AES.key_size = 32;
                aes_IV = AESManager.get_IV();
                encode_obj = AES.new(self.private_aes, AES.MODE_CBC, aes_IV);
                spaces = 16 - len(json_str) % 16;
                data2 = encode_obj.encrypt(json_str + (spaces * ' '));
                print("Sending data to " + name);
                master.send(bytes(aes_IV, 'utf-8') + data2);
                print('Done.');
                master.close();
            except KeyError as e:
                self.logger.error('in send_data_to_all_masters: ' + str(e));
                print(e);
                pass;
    
    def send_tech(self, json_obj, connection):
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "send_tech",
                "info": GLManager.TechInfo()
            }
        );
        self.send_data_to_all_masters(json_str);
        
    def send_alive(self, json_obj, connection):
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "send_alive",
                "info": GLManager.TechAlive()
            }
        );
        self.send_data_to_all_masters(json_str);

    def send_interfaces(self, json_obj, connection):
        try:
            if os.path.exists('/tmp/eib'):
                call(['service', 'knxd', 'stop']);
            previous_val_knx = self._parser.getValueFromSection('knx', 'interface');
            previous_val_EnOcean = self._parser.getValueFromSection('enocean', 'interface');
            new_val = str(json_obj['interface_arg_knx'])
            self._parser.writeValueFromSection('knx', 'interface', new_val);
            self._parser.writeValueFromSection('enocean', 'interface', str(json_obj['interface_arg_EnOcean']));
            if previous_val_knx == '' or previous_val_knx == None:
                call(['update-rc.d', 'knxd', 'defaults']);
                call(['update-rc.d', 'knxd', 'enable']);
            if new_val == '' or new_val == None:
                Popen(['systemctl', '-q', 'disable', 'knxd']);
            else:
                knx_edit = 'KNXD_OPTS="-e 1.0.254 -D -T -S -u ';
                if json_obj['interface_knx'] == 'tpuarts':
                    knx_edit = knx_edit + json_obj['interface_knx'] + ':/dev/' + new_val + '"';
                else:
                    knx_edit = knx_edit + '-b ' + json_obj['interface_knx']  + ':' + new_val + '"';
                conf_knx = open('/etc/knxd.conf', 'w');
                conf_knx.write(knx_edit + '\n');
                conf_knx.close();
                Popen(['service', 'knxd', 'start']);
        except Exception as e:
            self.logger.error(e);
        json_str = '{"packet_type": "send_interfaces", "aes_pass": "' + self.private_aes + '"}';
        master_hostname = str(json_obj['sender_name']);
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ')
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        connection.send(bytes(encrypt_IV, 'utf-8') + data);
        if previous_val_EnOcean != str(json_obj['interface_arg_EnOcean']):
            call(['service', 'domoslave', 'restart']);

    def reboot_d3(self, json_obj, connection):
        call(['reboot']);

    def wifi_update(self, json_obj, connection):
        try:
            self._parser.writeValueFromSection('wifi', 'ssid', json_obj['ssid']);
            self._parser.writeValueFromSection('wifi', 'password', json_obj['password']);
            self._parser.writeValueFromSection('wifi', 'security', json_obj['security']);
            self._parser.writeValueFromSection('wifi', 'mode', json_obj['mode']);
        except Exception as e:
            self.logger.error(e);
        json_str = '{"packet_type": "wifi_update", "aes_pass": "' + self.private_aes + '"}';
        master_hostname = str(json_obj['sender_name']);
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ')
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        connection.send(bytes(encrypt_IV, 'utf-8') + data);
