## @package domomaster
# Master daemon for D3 boxes.
#
# Developed by GreenLeaf.

import logging;
from Logger import *;
import socket;
import json;
import MasterDaemon;
import sys;
sys.path.append('/usr/lib/domoleaf');
from inspect import currentframe, getframeinfo;
import utils;
from MasterSql import *;
from DaemonConfigParser import *;
from Crypto.Cipher import AES;
import AESManager;
import os;
import hashlib;

LOG_FILE                = '/var/log/domoleaf/domomaster.log';

KNX_RESPONSE            = 1;    # Donnee KNX de type RESPONSE
KNX_WRITE_SHORT         = 2;    # Donnee KNX de type ecriture courte (0 | 1)
KNX_WRITE_LONG          = 3;    # Donnee KNX de type ecriture longue (0x0 -> 0xFF)

## Class managing the KNX protocol in D3 boxes.
class KNXManager:

    ## The constructor.
    #
    # @param slave_keys Array containing the AES keys of all the slaves.
    # @return None
    def __init__(self, slave_keys):
        ## Logger object for formatting and printing logs
        self.logger = Logger(True, LOG_FILE);
        ## SQL object for managing database
        self.sql = MasterSql();
        self._parser = DaemonConfigParser('/etc/domoleaf/master.conf');
        ## Object containing AES keys for encrypted communications
        self.aes_slave_keys = slave_keys;

    ## Updates room_device_option table in database to set new values of the device described by json_obj.
    #
    # @param daemon_id The ID of a slave daemon.
    # @param json_obj JSON object containing the values to update.
    # @param db The database handler.
    #
    # @return The result of the query.
    def update_room_device_option(self, daemon_id, json_obj, db):
        if int(json_obj['type']) == KNX_RESPONSE:
            return self.sql.update_room_device_option_resp(json_obj, daemon_id, db);
        elif int(json_obj['type']) == KNX_WRITE_SHORT:
            return self.sql.update_room_device_option_write_short(json_obj, daemon_id, db);
        elif int(json_obj['type']) == KNX_WRITE_LONG:
            return self.sql.update_room_device_option_write_long(json_obj, daemon_id, db);

    ## Sends a JSON object to a slave daemon.
    #
    # @param json_str The data to send.
    # @param sock The socket used to send the data.
    # @param hostname The hostname of the slave daemon.
    # @param aes_key The AES key of the slave daemon to encrypt data.
    # @return None
    def send_json_obj_to_slave(self, json_str, sock, hostname, aes_key):
        hostname_key = '';
        if '.' in hostname:
            hostname_key = hostname.split('.')[0];
        else:
            hostname_key = hostname;
        AES.key_size = 32;
        aes_IV = AESManager.get_IV();
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(json_str) % 16;
        data2 = encode_obj.encrypt(json_str + (spaces * ' '));
        sock.send(bytes(aes_IV, 'utf-8') + data2);

    ## Changes the speed value of a fan.
    #
    # @param json_obj JSON object containing the values to write.
    # @param dev Object containing informations about the device.
    # @param hostname The hostname of the slave to who send the packet.
    # @return None
    def send_knx_write_speed_fan(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        sock = socket.create_connection((hostname, port));
        if not port:
            sys.exit(4);
        if json_obj['data']['value'] == '1':
            query = ''.join(['SELECT option_id, addr, dpt_id FROM room_device_option WHERE room_device_id=',
                           str(dev['room_device_id']),
                           ' AND option_id IN(400, 401, 402, 403, 404, 405, 406) AND status=1']);
            res = self.sql.mysql_handler_personnal_query(query);
            for line in res:
                if str(line[2]) == "51" and str(line[0]) == str(json_obj['data']['option_id']):
                    val = str(line[0]).split('40')[1];
                    json_str = json.JSONEncoder().encode(
                        {
                            "packet_type": "knx_write_long",
                            "addr_to_send": line[1],
                            "value": val
                        }
                    );
                    self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
                    return;
                elif str(line[2]) == "2" and str(line[0]) != str(json_obj['data']['option_id']):
                    json_str = json.JSONEncoder().encode(
                        {
                            "packet_type": "knx_write_short",
                            "addr_to_send": line[1],
                            "value": "0"
                        }
                    );
                    self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_short",
                "addr_to_send": str(dev['addr_dst']),
                "value": json_obj['data']['value']
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();

    ## Converts absolute value of temperature (Celsius) in 2 hexadecimal values for sending to KNX device.
    #
    # @param json_obj JSON object containing the values to write.
    # @param dev Object describing the KNX device to who send the packet.
    # @param hostname The hostname of the slave daemon to who send the packet.
    # @return None
    def send_knx_write_temp(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        val_str = json_obj['data']['value'];
        if ',' in val_str:
            val_str = val_str.replace(',', '.')
        value = utils.convert_temperature_reverse(float(val_str));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_temp",
                "addr_to_send": str(dev['addr_dst']),
                "value": value[0] + ' ' + value[1]
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();

    ## Builds a "long write" request and sends it to "hostname".
    #
    # @param json_obj JSON object containing the new values.
    # @param dev The device to who send the request.
    # @param hostname The slave daemon to who send the packet.
    # @return None
    def send_knx_write_long_to_slave(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_long",
                "addr_to_send": str(dev['addr_dst']),
                "value": hex(int(json_obj['data']['value']))
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();

    ## Builds a "short read" request and sends it to "hostname".
    #
    # @param json_obj JSON object containing the new values.
    # @param dev The device to who send the request.
    # @param hostname The hostname of the slave daemon to who send the packet.
    # @return None
    def send_knx_write_short_to_slave(self, json_obj, dev, hostname):
        """
        Constructs short write request and sends it to 'hostname'
        """
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_short",
                "addr_to_send": str(dev['addr_dst']),
                "value": json_obj['data']['value']
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();

    ## Builds a "short read" request and sends it to the slave "hostname".
    #
    # @param hostname The slave daemon to who send the read request.
    # @param json_obj JSON object containing the data to send.
    # @return None
    def send_knx_read_request_to_slave(self, hostname, json_obj):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_read_request",
                "addr_to_read": json_obj['data']['addr']
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();

    ## Asks to send a "on" packet to a device.
    #
    # @param json_obj JSON object containing the new values.
    # @param dev The KNX device.
    # @param hostname The hostname of the slave daemon.
    # @return None
    def send_on(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_short",
                "addr_to_send": str(dev['addr_dst']),
                "value": "1"
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();
        return;

    ## Asks to send a "off" packet to a device.
    #
    # @param json_obj JSON object containing the new values.
    # @param dev The KNX device.
    # @param hostname The hostname of the slave daemon.
    # @return None
    def send_off(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_short",
                "addr_to_send": str(dev['addr_dst']),
                "value": "0"
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();
        return;

    ## Sends the new value of the temperature to thermostat.
    #
    # @param json_obj JSON object containing the new values.
    # @param dev The KNX device.
    # @param hostname The hostname of the slave daemon.
    # @return None
    def send_to_thermostat(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        if json_obj['data']['option_id'] == '412':
            val = 1;
        elif json_obj['data']['option_id'] == '413':
            val = 2;
        elif json_obj['data']['option_id'] == '414':
            val = 4;
        elif json_obj['data']['option_id'] == '415':
            val = 8;
        elif json_obj['data']['option_id'] == '416':
            val = 16;
        elif json_obj['data']['option_id'] == '417':
            val = 32;
        else:
            val = 0
        if val > 0:
            json_str = json.JSONEncoder().encode(
                {
                    "packet_type": "knx_write_long",
                    "addr_to_send": hex(int(dev['addr_dst'])),
                    "value": val
                }
            );
            sock = socket.create_connection((hostname, port));
            self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
            sock.close();
            return;

    ## Sends the new mode of the air conditioner.
    #
    # @param json_obj JSON object containing the new values.
    # @param dev The KNX device.
    # @param hostname The hostname of the slave daemon.
    # @return None
    def send_clim_mode(self, json_obj, dev, hostname):
        if json_obj['data']['option_id'] == '425': #Auto
            val = 0
        elif json_obj['data']['option_id'] == '426': #Heat
            val = 1
        elif json_obj['data']['option_id'] == '427': #Morning Warmup
            val = 2
        elif json_obj['data']['option_id'] == '428': #Cool
            val = 3
        elif json_obj['data']['option_id'] == '429': #Night Purge
            val = 4
        elif json_obj['data']['option_id'] == '430': #Precool
            val = 5
        elif json_obj['data']['option_id'] == '431': #Off
            val = 6
        elif json_obj['data']['option_id'] == '432': #Test
            val = 7
        elif json_obj['data']['option_id'] == '433': #Emergency Heat
            val = 8
        elif json_obj['data']['option_id'] == '434': #Fan only
            val = 9
        elif json_obj['data']['option_id'] == '435': #Free Cool
            val = 10
        elif json_obj['data']['option_id'] == '436': #Ice
            val = 11
        else:
            val = -1
        if val >= 0:
            json_str = json.JSONEncoder().encode(
                {
                    "packet_type": "knx_write_long",
                    "addr_to_send": str(dev['addr_dst']),
                    "value":  hex(int(val))
                }
            );
            sock = socket.create_connection((hostname, port));
            self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
            sock.close();
            return;

    ## Sends a value converted in percentages.
    #
    # @param json_obj Not used here.
    # @param dev Object describing the KNX device.
    # @param hostname Hostname of the slave daemon to who sent the packet.
    # @return None
    def send_knx_write_percent(self, json_obj, dev, hostname):
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_long",
                "addr_to_send": str(dev['addr_dst']),
                "value": hex(int(255*int(json_obj['data']['value'])/100))
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);
        sock.close();
