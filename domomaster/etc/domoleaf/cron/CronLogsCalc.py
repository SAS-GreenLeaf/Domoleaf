#!/usr/bin/python3

import sys
sys.path.append('/usr/lib/domoleaf')
import socket
import json
from DaemonConfigParser import *;

if __name__ == "__main__":
    try:
        parser = DaemonConfigParser('/etc/domoleaf/master.conf')
        ip = '127.0.0.1'
        port = parser.getValueFromSection('listen', 'port_cmd')
        s = socket.create_connection((ip, port))
        obj = {
            "packet_type": "calc_logs",
            "data": []
        }
        obj_str = json.JSONEncoder().encode(obj)
        s.send(obj_str.encode())
        s.close()                                                        
    except Exception as e:
        print(str(e))
