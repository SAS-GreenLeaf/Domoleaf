#!/usr/bin/python3

import sys;
import os;
import random;
import string;
from hashlib import sha1
sys.path.append("/usr/lib/domoleaf");
from DaemonConfigParser import *;

SLAVE_CONF_FILE_FROM            = '/etc/domoleaf/slave.conf.save';
SLAVE_CONF_FILE_TO              = '/etc/domoleaf/slave.conf';

def slave_conf_copy():
    file_from = DaemonConfigParser(SLAVE_CONF_FILE_FROM);
    file_to   = DaemonConfigParser(SLAVE_CONF_FILE_TO);
    
    #listen
    var = file_from.getValueFromSection('listen', 'port');
    file_to.writeValueFromSection('listen', 'port', var);
    
    #connect
    var = file_from.getValueFromSection('connect', 'port');
    file_to.writeValueFromSection('connect', 'port', var);
    
    #knx
    var = file_from.getValueFromSection('knx', 'port');
    file_to.writeValueFromSection('knx', 'port', var);
    var = file_from.getValueFromSection('knx', 'interface');
    file_to.writeValueFromSection('knx', 'interface', var);
    
    #enocean
    var = file_from.getValueFromSection('enocean', 'port');
    file_to.writeValueFromSection('enocean', 'port', var);
    var = file_from.getValueFromSection('enocean', 'interface');
    file_to.writeValueFromSection('enocean', 'interface', var);
    
    #cron
    var = file_from.getValueFromSection('cron', 'port');
    file_to.writeValueFromSection('cron', 'port', var);
    var = file_from.getValueFromSection('cron', 'address');
    file_to.writeValueFromSection('cron', 'address', var);
    
    #personnal_key
    var = file_from.getValueFromSection('personnal_key', 'aes');
    file_to.writeValueFromSection('personnal_key', 'aes', var);
    
    #openvpn
    var = file_from.getValueFromSection('openvpn', 'openvpnserver');
    file_to.writeValueFromSection('openvpn', 'openvpnserver', var);

def slave_conf_init():
    file = DaemonConfigParser(SLAVE_CONF_FILE_TO);
    
    #KEY
    KEY = ''.join(random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(128))
    KEY = sha1(KEY.encode('utf-8'))
    file.writeValueFromSection('personnal_key', 'aes', KEY.hexdigest());
    
    #KNX Interface
    knx_edit = 'KNXD_OPTS="-e 1.0.254 -D -T -S -u /tmp/knxd '
    if os.path.exists('/dev/ttyAMA0'):
        knx_edit = knx_edit + 'tpuarts:/dev/ttyAMA0"';
    elif os.path.exists('/dev/ttyS0'):
        knx_edit = knx_edit + 'tpuarts:/dev/ttyS0"';
    else:
        knx_edit = knx_edit + '-b ipt:127.0.0.1"';
    conf_knx = open('/etc/knxd.conf', 'w');
    conf_knx.write(knx_edit + '\n');
    conf_knx.close();

if __name__ == "__main__":
    #Upgrade
    if os.path.exists('/etc/domoleaf/slave.conf.save'):
        slave_conf_copy()
        os.remove('/etc/domoleaf/slave.conf.save');
    #New install
    else:
        slave_conf_init()
        