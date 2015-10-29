import configparser;
import sys;
import os;
sys.path.append("/usr/lib/domoleaf");
from DaemonConfigParser import *;

SLAVE_CONF_FILE_FROM            = '/etc/domoleaf/slave.conf.save';
SLAVE_CONF_FILE_TO              = '/etc/domoleaf/slave.conf';

if __name__ == "__main__":
    if os.path.exists('/etc/domoleaf/slave.conf.save'):
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
        