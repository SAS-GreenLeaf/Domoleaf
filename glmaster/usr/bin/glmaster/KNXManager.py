import socket;
import json;
import MasterDaemon;
import sys;
sys.path.append('/usr/lib/greenleaf');
from inspect import currentframe, getframeinfo;
import utils;
from MasterSql import *;
from DaemonConfigParser import *;
from Crypto.Cipher import AES;
import AESManager;
import os;
import hashlib;

OPTION_ON_OFF           = 12;   # Indice pour une action ON/OFF
OPTION_VAR              = 13;   # Indice pour une action de variation
OPTION_UP_DOWN          = 54;   # Indice pour une action de type monter/descendre
OPTION_STOP_UP_DOWN     = 365;
OPTION_OPEN_CLOSE       = 96;   # Indice pour une action de type ouvrir/fermer
OPTION_TEMPERATURE      = 72;   # Indice pour le traitement d'une temperature
OPTION_TEMPERATURE_W    = 388;  # Indice pour l'ecriture d'une temperature

KNX_RESPONSE            = 1;    # Donnee KNX de type RESPONSE
KNX_WRITE_SHORT         = 2;    # Donnee KNX de type ecriture courte (0 | 1)
KNX_WRITE_LONG          = 3;    # Donnee KNX de type ecriture longue (0x0 -> 0xFF)

class KNXManager:
    """
    KNX management class
    """
    def __init__(self, slave_keys):
        self.knx_function = {
            OPTION_ON_OFF       : self.send_knx_write_short_to_slave,
            OPTION_VAR          : self.send_knx_write_long_to_slave,
            OPTION_UP_DOWN      : self.send_knx_write_short_to_slave,
            OPTION_OPEN_CLOSE   : self.send_knx_write_short_to_slave,
            OPTION_STOP_UP_DOWN : self.send_knx_write_short_to_slave,
            OPTION_TEMPERATURE_W: self.send_knx_write_temp
        };
        self.sql = MasterSql();
        self._parser = DaemonConfigParser('/etc/greenleaf/master.conf');
        self.aes_slave_keys = slave_keys;

    def update_room_device_option(self, daemon_id, json_obj):
        """
        Update room_device_option table in database to set new values of the device described by 'json_obj'
        """
        if int(json_obj['type']) == KNX_RESPONSE:
            self.sql.update_room_device_option_resp(json_obj, daemon_id);
        elif int(json_obj['type']) == KNX_WRITE_SHORT:
            self.sql.update_room_device_option_write_short(json_obj, daemon_id);
        elif int(json_obj['type']) == KNX_WRITE_LONG:
            self.sql.update_room_device_option_write_long(json_obj, daemon_id);

    def protocol_knx(self, json_obj, dev, hostname):
        """
        KNX protocol data treatment function
        """
        new_obj = {
            "data": {
                "addr": str(dev['addr_dst']),
                "value": str(json_obj['data']['value'])
            }
        };
        self.knx_function[int(json_obj['data']['option_id'])](hostname, new_obj);

    def send_json_obj_to_slave(self, json_str, sock, hostname, aes_key, close_flag = True):
        """
        Send 'json_obj' to 'hostname' via 'sock'
        """
        hostname_key = '';
        if '.' in hostname:
            hostname_key = hostname.split('.')[0];
        else:
            hostname_key = hostname;
        AES.key_size = 32;
        aes_IV = AESManager.get_IV();
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        data2 = encode_obj.encrypt(json_str + (176 - len(json_str)) * ' ');
        sock.send(bytes(aes_IV, 'utf-8') + data2);
        if close_flag == True:
            sock.close();

    def send_knx_write_temp(self, hostname, json_obj):
        """
        Converts absolute value of temperature (Celsius) in 2 hexadecimal values for
        sending to KNX device
        """
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        val_str = json_obj['data']['value'];
        if '.' in val_str:
            val_str = val_str.split('.')[0];
        value = utils.convert_temperature_reverse(int(val_str));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_temp",
                "addr_to_send": json_obj['data']['addr'],
                "value": value[0] + ' ' + value[1]
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);

    def send_knx_write_long_to_slave(self, hostname, json_obj):
        """
        Constructs long write request and sends it to 'hostname'
        """
        port = self._parser.getValueFromSection('connect', 'port');
        if not port:
            sys.exit(4);
        sock = socket.create_connection((hostname, port));
        json_str = json.JSONEncoder().encode(
            {
                "packet_type": "knx_write_long",
                "addr_to_send": json_obj['data']['addr'],
                "value": hex(int(json_obj['data']['value']))
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);

    def send_knx_write_short_to_slave(self, hostname, json_obj):
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
                "addr_to_send": json_obj['data']['addr'],
                "value": json_obj['data']['value']
            }
        );
        self.send_json_obj_to_slave(json_str, sock, hostname, self.aes_slave_keys[hostname]);

    def send_knx_read_request_to_slave(self, hostname, json_obj):
        """
        Constructs short read request and sends it to 'hostname'
        """
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
