#!/usr/bin/python3

import sys
sys.path.append('/usr/lib/greenleaf')
import socket
import json
from DaemonConfigParser import *;

if __name__ == "__main__":
    try:
        parser = DaemonConfigParser('/etc/greenleaf/slave.conf')
        ip = parser.getValueFromSection('cron', 'address')
        port = parser.getValueFromSection('cron', 'port')
        s = socket.create_connection((ip, port))
        obj = {
            "packet_type": "cron_upnp",
            "data": [
                {
                    "action": "open",
                    "configuration_id": 1,
                    "protocol": "TCP"
                },
                {
                    "action": "close",
                    "configuration_id": 2,
                    "protocol": "TCP"
                }
            ]
        }
        obj_str = json.JSONEncoder().encode(obj)
        s.send(obj_str.encode())
        s.close()
    except Exception as e:
        print(str(e))
