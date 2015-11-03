#!/usr/bin/python3

import configparser;
import sys;
import os;
import random;
import string;
from hashlib import sha1
import socket;
sys.path.append("/usr/lib/domoleaf");
from DaemonConfigParser import *;
from MysqlHandler import *;

MASTER_CONF_FILE_FROM            = '/etc/domoleaf/master.conf.save';
MASTER_CONF_FILE_TO              = '/etc/domoleaf/master.conf';
SLAVE_CONF_FILE                  = '/etc/domoleaf/master.conf';

def master_conf_copy():
    file_from = DaemonConfigParser(MASTER_CONF_FILE_FROM);
    file_to   = DaemonConfigParser(MASTER_CONF_FILE_TO);
    
    #listen
    var = file_from.getValueFromSection('listen', 'port_slave');
    file_to.writeValueFromSection('listen', 'port_slave', var);
    var = file_from.getValueFromSection('listen', 'port_cmd');
    file_to.writeValueFromSection('listen', 'port_cmd', var);
    
    #connect
    var = file_from.getValueFromSection('connect', 'port');
    file_to.writeValueFromSection('connect', 'port', var);
    
    #mysql
    var = file_from.getValueFromSection('mysql', 'user');
    file_to.writeValueFromSection('mysql', 'user', var);
    var = file_from.getValueFromSection('mysql', 'database_name');
    file_to.writeValueFromSection('mysql', 'database_name', var);
    
    #greenleaf
    var = file_from.getValueFromSection('greenleaf', 'commercial');
    file_to.writeValueFromSection('greenleaf', 'commercial', var);
    var = file_from.getValueFromSection('greenleaf', 'admin_addr');
    file_to.writeValueFromSection('greenleaf', 'admin_addr', var);

def master_conf_initdb():
    file = DaemonConfigParser(MASTER_CONF_FILE_TO);
    
    #mysql password
    password = ''.join(random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(128))
    password = sha1(password.encode('utf-8'))
    e9d487501675015782e50fa134b86a903d763a8a
    file.writeValueFromSection('mysql', 'password', password.hexdigest());
    os.system('sed -i "s/define(\'DB_PASSWORD\', \'domoleaf\')/define(\'DB_PASSWORD\', \''+password.hexdigest()+'\')/g" /etc/domoleaf/www/config.php')
    file_to.writeValueFromSection('mysql', 'password', password.hexdigest());
    
    #mysql user
    query1 = 'DELETE FROM user WHERE User="domoleaf"';
    query2 = 'DELETE FROM db WHERE User="domoleaf"';
    query3 = 'INSERT INTO user (Host, User, Password) VALUES (\'localhost\', \'domoleaf\', PASSWORD(\''+password.hexdigest()+'\'));';
    query4 = "INSERT INTO db (\`Host\`, \`Db\`, \`User\`, \`Select_priv\`, \`Insert_priv\`, \`Update_priv\`, \`Delete_priv\`, \`Create_priv\`, \`Drop_priv\`, \`Grant_priv\`, \`References_priv\`, \`Index_priv\`, \`Alter_priv\`, \`Create_tmp_table_priv\`, \`Lock_tables_priv\`, \`Create_view_priv\`, \`Show_view_priv\`, \`Create_routine_priv\`, \`Alter_routine_priv\`, \`Execute_priv\`, \`Event_priv\`, \`Trigger_priv\`) VALUES ('localhost','domoleaf','domoleaf','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');";
    query5 = 'FLUSH PRIVILEGES';
    
    self.sql.mysql_handler_personnal_query(query1);
    self.sql.mysql_handler_personnal_query(query2);
    self.sql.mysql_handler_personnal_query(query3);
    self.sql.mysql_handler_personnal_query(query4);
    self.sql.mysql_handler_personnal_query(query5);
    
def master_conf_init():
    file = DaemonConfigParser(SLAVE_CONF_FILE);
    personnal_key = file.getValueFromSection('personnal_key', 'aes');
    hostname = socket.gethostname();
    
    #KNX Interface
    if os.path.exists('/dev/ttyAMA0'):
        knx = 'ttyAMA0';
    elif os.path.exists('/dev/ttyS0'):
        knx = 'ttyS0';
    else:
        knx = '127.0.0.1';
    
    fic = open('/etc/domoleaf/.glslave.version','r')
    glslave = fic.readline();
    fic.close()
    
    query1 = "INSERT INTO daemon (name, serial, secretkey, validation, version) VALUES ('"+hostname+"','"+hostname+"','"+personnal_key+"',1,'"+glslave+"')"
    query1 = "INSERT INTO daemon_protocol (daemon_id, protocol_id, interface) VALUES (1,1,'"+knx+"')"
    self.sql.mysql_handler_personnal_query(query1);
    self.sql.mysql_handler_personnal_query(query2);

if __name__ == "__main__":
    #Upgrade
    if os.path.exists('/etc/domoleaf/master.conf.save'):
        master_conf_copy()
        os.remove('/etc/domoleaf/master.conf.save');
    else:
        master_conf_init()
    master_conf_initdb()
    