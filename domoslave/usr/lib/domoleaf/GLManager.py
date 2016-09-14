#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import socket
import sys;
sys.path.append('/usr/lib/domoleaf');
from DaemonConfigParser import *;
from Crypto.Cipher import AES;
from InfoSys import *;
import json;
import AESManager
import requests
import base64

SLAVE_CONF_FILE                 = '/etc/domoleaf/slave.conf';

## Class with static utils methods.
class GLManager:

    ## Sends a packet to use the cron cron_name.
    #
    # @param cron_name The name of the cron to use.
    def send_cron(cron_name):
        try:
            parser = DaemonConfigParser(SLAVE_CONF_FILE);
            port = parser.getValueFromSection('cron', 'port').encode()
            sock = socket.create_connection(('127.0.0.1', port));
            sock.send(bytes(cron_name, 'utf-8'));
            sock.close();
        except Exception as e:
            if 'sock' in locals():
                sock.close()

    ## Gets informations about the system.
    #
    # @return JSON object containing the wanted informations.
    def TechInfo():
        json_str = {
            "ip_private":  InfoSys.ipPrivate(),
            "ip_v6":       InfoSys.ipv6(),
            "ip_vpn":      InfoSys.ipVPN(),
            "mb_serial":   InfoSys.motherboardSerial(),
            "ssd_serial":  InfoSys.diskSerial(),
            "soft_master": InfoSys.softMaster(),
            "soft_slave":  InfoSys.softSlave(),
            "soft_knx":    InfoSys.softKNX(),
            "uptime":      InfoSys.uptime(),
            "temperature": InfoSys.temperature()
        };
        return json_str

    ## Informations about the fact that the D3 is alive.
    #
    # @return JSON Object containing the wanted informations.
    def TechAlive():
        json_str = {
            "uptime":      InfoSys.uptime(),
            "temperature": InfoSys.temperature()
        };
        return json_str

    ## Sends the request to the admin.
    #
    # @param obj_to_send The JSON Object to send.
    # @param admin_addr The address to who send the packet.
    # @param aes_key The AES keys to cypher the packet.
    def SendRequest(obj_to_send, admin_addr, aes_key):
        hostname = socket.gethostname()
        aes_IV = AESManager.get_IV();
        encode_obj = AES.new(aes_key, AES.MODE_CBC, aes_IV);
        spaces = 16 - len(obj_to_send) % 16;
        obj_to_send = encode_obj.encrypt(obj_to_send + (spaces * ' '));
        data = {
            "sender_name": hostname,
            "data": base64.b64encode(obj_to_send),
            "iv": aes_IV
        }
        r = requests.post("http://"+admin_addr+"/md_receive.php", data = data)
        return r
