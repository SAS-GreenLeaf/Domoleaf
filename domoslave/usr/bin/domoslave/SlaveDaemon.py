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

WIFI_MODE_DISABLED              = '0';
WIFI_MODE_CLIENT                = '1';
WIFI_MODE_ACCESS_POINT          = '2';

WIFI_SECURITY_WPA               = '2';
WIFI_SECURITY_WPA2              = '3';

DNSMASQ_CONF_FILE               = '/etc/dnsmasq.conf';
HOSTAPD_CONF_FILE               = '/etc/domoleaf/hostapd.conf';
WPA_SUPPLICANT_CONF_FILE        = '/etc/domoleaf/wpa_supplicant.conf';

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
SHUTDOWN_D3             = 'shutdown_d3';
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
        self._scanner = Scanner();
        self._hostlist = [];
        myhostname = socket.gethostname().upper()
        if SLAVE_NAME_PREFIX in myhostname:
            self._scanner.scan();
            self._hostlist = self._scanner._HostList;
        else:
            self._hostlist.append(Host('', '127.0.0.1', myhostname));
        self._parser = DaemonConfigParser(SLAVE_CONF_FILE);
        self.encrypt_keys = {};
        self.knx_sock = None;
        self.master_sock = None;
        self.enocean_sock = None;
        self.cron_sock = None;
        self.private_aes = self._parser.getValueFromSection('personnal_key', 'aes');
        self.wifi_init(self._parser.getValueFromSection('wifi', 'ssid'), self._parser.getValueFromSection('wifi', 'password'), self._parser.getValueFromSection('wifi', 'encryption'), self._parser.getValueFromSection('wifi', 'mode'), 0);
        self.connect_port = self._parser.getValueFromSection(SLAVE_CONF_CONNECT_SECTION, SLAVE_CONF_CONNECT_PORT_ENTRY);
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
            SHUTDOWN_D3         : self.shutdown_d3,
            REBOOT_D3           : self.reboot_d3,
            WIFI_UPDATE         : self.wifi_update
        };

    def update(self, json_obj, connection):
        """
        Update the base system of the slave daemon
        """
        p = call(['dpkg', '--configure', '-a'])
        call(['apt-get', 'update']);
        call(['DEBIAN_FRONTEND=noninteractive', 'apt-get', 'install', 'domoslave', '-y']);
        version = os.popen("dpkg-query -W -f='${Version}\n' domoslave").read().split('\n')[0];
        json_str = '{"packet_type": "update_finished", "aes_pass": "' + self.private_aes + '", "new_version": ' + version + '}';
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ');
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
        self.knx_sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
        self.master_sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
        self.enocean_sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
        self.cron_sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1);
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
        self.send_monitor_ip()
        self.loop();

    def accept_knx(self):
        """
        Get available sockets for reading on the KNX socket.
        """
        rlist, wlist, elist = select.select([self.knx_sock], [], [], SELECT_TIMEOUT);
        append = self.connected_knx.append
        for connection in rlist:
            new_knx, addr = connection.accept();
            append(new_knx);
        self.receive_from_knx(self.connected_knx);

    def accept_masters(self):
        """
        Get available sockets for reading on the master socket.
        """
        rlist, wlist, elist = select.select([self.master_sock], [], [], SELECT_TIMEOUT);
        masters_socks = [];
        append = masters_socks.append;
        for item in rlist:
            new_conn, addr = item.accept();
            append(new_conn);
        self.receive_from_masters(masters_socks);

    def accept_enocean(self):
        """
        Get available sockets for reading on the EnOcean socket.
        """
        rlist, wlist, elist = select.select([self.enocean_sock], [], [], SELECT_TIMEOUT);
        enocean_socks = [];
        append = enocean_socks.append;
        append_connected = self.connected_enocean.append;
        for item in rlist:
            new_conn, addr = item.accept();
            append(new_conn);
            append_connected(new_conn);
        self.receive_from_enocean(enocean_socks);

    def accept_cron(self):
        """
        Get available sockets for reading on the Cron socket.
        """
        rlist, wlist, elist = select.select([self.cron_sock], [], [], SELECT_TIMEOUT);
        cron_socks = [];
        append = cron_socks.append;
        append_connected = self.connected_cron.append;
        for item in rlist:
            new_conn, addr = item.accept();
            append(new_conn);
            append_connected(new_conn);
        self.receive_from_cron(cron_socks);

    def parse_data(self, data, connection):
        """
        Calls the wanted function with the packet_type described in 'data' (JSON syntax)
        """
        json_obj = json.JSONDecoder().decode(data);
        if json_obj['packet_type'] in self.functions.keys():
            self.functions[json_obj['packet_type']](json_obj, connection);
        else:
            raise Exception(str(json_obj['packet_type'])+": is not a valid packet type");

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
            master.close();

    def receive_from_knx(self, knx_to_read):
        """
        Read data from monitor KNX and transmits to master.
        """
        for knx in knx_to_read:
            data = knx.recv(TELEGRAM_LENGTH);
            if data:
                self.send_knx_data_to_masters(data);
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
            if cron in self.connected_cron:
                cron.close();
                self.connected_cron.remove(cron);

    def check_slave(self, json_obj, connection):
        """
        Callback called each time a check_slave packet is received.
        Used to confirm the existence of this daemon.
        """
        interface_knx = self._parser.getValueFromSection(SLAVE_CONF_KNX_SECTION, SLAVE_CONF_KNX_INTERFACE);
        interface_enocean = self._parser.getValueFromSection(SLAVE_CONF_ENOCEAN_SECTION, SLAVE_CONF_ENOCEAN_INTERFACE);
        version = os.popen("dpkg-query -W -f='${Version}\n' domoslave").read().split('\n')[0];
        json_str = '{"packet_type": "check_slave", "aes_pass": "' + self.private_aes + '", "version": "' + version + '", "interface_knx": "' + interface_knx + '", "interface_enocean": "' + interface_enocean + '"}';
        master_hostname = str(json_obj['sender_name']);
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ');
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        connection.send(bytes(encrypt_IV, 'utf-8') + data);

    def monitor_ip(self, json_obj, connection):
        """
        Re scan the local network to refresh hostlist.
        """
        self._scanner.scan();
        self._hostlist = self._scanner._HostList;

    def send_monitor_ip(self):
        """
        Send a packet monitor_ip to all the masters available
        """
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "monitor_ip"
            }
        );
        self.send_data_to_all_masters(json_str);

    def loop(self):
        """
        Main daemon loop.
        """
        while self.run:
            try:
                self.accept_knx();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_knx: '+str(e));
                print('in loop accept_knx: ',str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            try:
                self.accept_masters();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_masters: '+str(e));
                print('in loop accept_masters: ',str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            try:
                self.accept_enocean();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_enocean: '+str(e));
                print('in loop accept_enocean: ',str(e));
            except KeyboardInterrupt as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop: Keyboard interrupt');
            try:
                self.accept_cron();
            except Exception as e:
                frameinfo = getframeinfo(currentframe());
                self.logger.error('in loop accept_cron: '+str(e));
                print('in loop accept_cron: ',str(e));
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
                if not self.connect_port:
                    self.logger.error('in connect_to_masters: No '+SLAVE_CONF_CONNECT_PORT_ENTRY+' in '+SLAVE_CONF_CONNECT_SECTION+' section or maybe no such '+SLAVE_CONF_CONNECT_SECTION+' defined');
                    sys.exit(1);
                try:
                    self.logger.debug('Connecting to '+str(host._IpAddr)+':'+str(self.connect_port));
                    sock = socket.create_connection((host._IpAddr, self.connect_port));
                    hostname = host._Hostname.split('.')[0];
                    self.connected_masters[host._Hostname] = sock;
                except Exception as e:
                    frameinfo = getframeinfo(currentframe());
                    self.logger.error('in connect_to_masters: '+str(e));
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
        self.send_data_to_all_masters(json_str);

    def send_enocean_data_to_masters(self, data):
        """
        Converts 'data' from bytes to a clear EnOcean datagran, and sends it to available slaves.
        """
        if (data[4] == PACKET_TYPE_RADIO_ERP1): # si le packet_type == radio_erp1
            data_len = int.from_bytes(data[1:2], byteorder='big');
            opt_data_len = int(data[3]);
            src_str = "%X" % int.from_bytes(data[1+data_len:5+data_len], byteorder='big');
            if len(src_str) < 8:
                src_str = "0"+src_str;
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
                master.send(bytes(aes_IV, 'utf-8') + data2);
                master.close();
            except KeyError as e:
                self.logger.error('in send_data_to_all_masters: '+str(e));
                print(e);
                pass;

    def send_tech(self, json_obj, connection):
        """
        Send the informations about the slave to all masters available
        """
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "send_tech",
                "info": GLManager.TechInfo()
            }
        );
        self.send_data_to_all_masters(json_str);

    def send_alive(self, json_obj, connection):
        """
        Send that the slave daemon is alive to all masters available
        """
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "send_alive",
                "info": GLManager.TechAlive()
            }
        );
        self.send_data_to_all_masters(json_str);

    def send_interfaces(self, json_obj, connection):
        """
        Send the protocol interface informations to all masters availables
        """
        try:
            if os.path.exists('/tmp/knxd'):
                call(['service', 'knxd', 'stop']);
            previous_val_knx = self._parser.getValueFromSection('knx', 'interface');
            previous_val_EnOcean = self._parser.getValueFromSection('enocean', 'interface');
            new_val = str(json_obj['interface_arg_knx'])
            self._parser.writeValueFromSection('knx', 'interface', new_val);
            self._parser.writeValueFromSection('knx', 'activated', str(json_obj['daemon_knx']));
            self._parser.writeValueFromSection('enocean', 'interface', str(json_obj['interface_arg_EnOcean']));
            if not previous_val_knx or previous_val_knx is None:
                call(['update-rc.d', 'knxd', 'defaults']);
                call(['update-rc.d', 'knxd', 'enable']);
            if not new_val or new_val is None:
                Popen(['systemctl', '-q', 'disable', 'knxd']);
            else:
                knx_edit = 'KNXD_OPTS="-e 1.0.254 -D -T -S -b ';
                if json_obj['interface_knx'] == 'tpuarts':
                    knx_edit += json_obj['interface_knx']+':/dev/'+new_val+'"';
                else:
                    knx_edit += json_obj['interface_knx']+':'+new_val+'"';
                conf_knx = open('/etc/knxd.conf', 'w');
                conf_knx.write(knx_edit+'\n');
                conf_knx.close();
                call(['service', 'knxd', 'start']);
                if json_obj['daemon_knx'] == 1:
                    if os.path.exists('/var/run/monitor_knx.pid'):
                        os.remove('/var/run/monitor_knx.pid');
                    Popen(['monitor_knx', 'ip:localhost', '--daemon']);
        except Exception as e:
            self.logger.error(e);
        json_str = '{"packet_type": "send_interfaces", "aes_pass": "' + self.private_aes + '"}';
        master_hostname = str(json_obj['sender_name']);
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ');
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        connection.send(bytes(encrypt_IV, 'utf-8') + data);
        if previous_val_EnOcean != str(json_obj['interface_arg_EnOcean']):
            call(['service', 'domoslave', 'restart']);

    def shutdown_d3(self, json_obj, connection):
        """
        Shut down the slave daemon
        """
        call(['poweroff']);

    def reboot_d3(self, json_obj, connection):
        """
        Reboot the slave daemon
        """
        call(['reboot']);

    def wifi_init(self, ssid, password, security, mode, opt):
        """
        Initialize the wifi protocol
        """
        try:
            ps_process = Popen(["ps", "-x"], stdout=PIPE);
            res = Popen(["grep", "hostapd"], stdin=ps_process.stdout, stdout=PIPE);
            res = res.stdout.read().decode().split("\n")[0].split(' ');
            ps_process.stdout.close();
            if res:
                while ('' in res):
                    res.remove('');
                call(['kill', '-9', res[0]]);
            ps_process = Popen(["ps", "-x"], stdout=PIPE);
            res = Popen(["grep", "wpa_supplicant"], stdin=ps_process.stdout, stdout=PIPE);
            res = res.stdout.read().decode().split("\n")[0].split(' ');
            ps_process.stdout.close();
            if res:
                while ('' in res):
                    res.remove('');
                call(['kill', '-9', res[0]]);
            call(['ifconfig', 'wlan0', 'down']);
            if mode == WIFI_MODE_DISABLED:
                if opt == 1:
                    call(['service', 'dnsmasq', 'stop']);
            elif mode == WIFI_MODE_CLIENT:
                call(['ifconfig', 'wlan0', 'up']);
                if opt == 1:
                    call(['service', 'dnsmasq', 'stop']);
                conf_file = open('/etc/network/interfaces', 'w');
                conf_str = ''.join(['auto lo\niface lo inet loopback\n\nallow-hotplug eth0\n',
                      'iface eth0 inet dhcp\n\nallow-hotplug usb0\niface usb0 inet dhcp\n\n',
                      'auto wlan0\niface wlan0 inet dhcp\n\twpa-conf ', WPA_SUPPLICANT_CONF_FILE, '\n']);
                conf_file.write(conf_str);
                conf_file.close();
                conf_file = open(WPA_SUPPLICANT_CONF_FILE, 'w');
                conf_str = ''.join(['ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n',
                      'update_config=1\nctrl_interface_group=0\neapol_version=1\n',
                      'ap_scan=1\n fast_reauth=1\n\n\nnetwork={\n\tdisabled=0\n',
                      '\tssid="', ssid, '"\n\tscan_ssid=0\n\tpriority=1\n']);
                if security == WIFI_SECURITY_WPA:
                    conf_str += ('\tproto=WPA\n\tkey_mgmt=WPA-PSK\n\tauth_alg=OPEN\n'+
                          '\tpairwise=TKIP CCMP\n\tgroup=TKIP CCMP\n\tpsk="'+password+'"\n');
                elif security == WIFI_SECURITY_WPA2:
                    conf_str += ('\tproto=RSN\n\tkey_mgmt=WPA-PSK\n\tauth_alg=OPEN\n\tpairwise=CCMP TKIP\n'+
                                   '\tgroup=CCMP TKIP\n\tpsk="'+password+'"\n');
                elif security == WIFI_SECURITY_WEP:
                    conf_str += '\tkey_mgmt=NONE\n\tauth_alg=SHARED\n';
                    if len(password) == 5 or len(password) == 10:
                        conf_str += '\tgroup=WEP40\n';
                    elif len(password) == 13 or len(password) == 26:
                        conf_str += '\tgroup=WEP104\n';
                    else:
                        conf_str += '\tgroup=WEP40 WEP104\n';
                    conf_str += '\twep_key0="'+password+'"\n\twep_tx_keyidx=0\n';
                conf_str += '\tpriority=1\n}\n';
                conf_file.write(conf_str);
                conf_file.close();
                call(['wpa_supplicant', '-Dnl80211', '-iwlan0', '-c' + WPA_SUPPLICANT_CONF_FILE , '-B']);
                call(['dhclient', 'wlan0']);
            elif mode == WIFI_MODE_ACCESS_POINT:
                call(['ifconfig', 'wlan0', '172.16.0.1', 'netmask', '255.255.255.0', 'up']);
                conf_file = open(HOSTAPD_CONF_FILE, 'w');
                conf_str = ''.join(['interface=wlan0\n\ndriver=nl80211\n\nssid=', ssid, '\n\n',
                      'hw_mode=g\n\nieee80211n=1\n\nchannel=6\n\nbeacon_int=100\n\n',
                      'dtim_period=2\n\nmax_num_sta=255\n\nrts_threshold=2347\n\n',
                      'fragm_threshold=2346\n\nmacaddr_acl=0\n\n']);
                if security == WIFI_SECURITY_WPA:
                    conf_str += ('auth_algs=1\n\nwpa=1\n\nwpa_passphrase='+password+'\n\n'+
                                 'wpa_key_mgmt=WPA-PSK\n\nwpa_pairwise=TKIP\n');
                elif security == WIFI_SECURITY_WPA2:
                    conf_str += ('auth_algs=1\n\nwpa=2\n\nwpa_passphrase='+password+'\n\n'+
                                 'wpa_key_mgmt=WPA-PSK\n\nwpa_pairwise=CCMP\n\nrsn_pairwise=CCMP\n');
                else:
                    self.logger.error('Wifi security = Unknown');
                conf_file.write(conf_str);
                conf_file.close();
                if opt == 1:
                    conf_file = open(DNSMASQ_CONF_FILE, 'w');
                    conf_str = 'domain-needed\ninterface=wlan0\ndhcp-range=172.16.0.2,172.16.0.254,12h\n';
                    conf_file.write(conf_str);
                    conf_file.close();
                    call(['service', 'dnsmasq', 'restart']);
                call(['iptables', '-t', 'nat', '-A', 'POSTROUTING', '-j', 'MASQUERADE']);
                call(['hostapd', HOSTAPD_CONF_FILE, '-B']);
            else:
                call(['ifconfig', 'wlan0', 'up']);
                self.logger.error('Wifi mode = Unknown');
        except Exception as e:
            self.logger.error(e);

    def wifi_update(self, json_obj, connection):
        """
        Update of the wifi informations
        """
        try:
            self._parser.writeValueFromSection('wifi', 'ssid', json_obj['ssid']);
            self._parser.writeValueFromSection('wifi', 'password', json_obj['password']);
            self._parser.writeValueFromSection('wifi', 'encryption', json_obj['security']);
            self._parser.writeValueFromSection('wifi', 'mode', json_obj['mode']);
            self.wifi_init(json_obj['ssid'], json_obj['password'], json_obj['security'], json_obj['mode'], 1);
        except Exception as e:
            self.logger.error(e);
        json_str = '{"packet_type": "wifi_update", "aes_pass": "' + self.private_aes + '"}';
        master_hostname = str(json_obj['sender_name']);
        encrypt_IV = AESManager.get_IV();
        spaces = 16 - len(json_str) % 16;
        json_str = json_str + (spaces * ' ');
        encode_obj = AES.new(self.private_aes, AES.MODE_CBC, encrypt_IV);
        data = encode_obj.encrypt(json_str);
        connection.send(bytes(encrypt_IV, 'utf-8') + data);
