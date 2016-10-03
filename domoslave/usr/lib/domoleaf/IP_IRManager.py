#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import socket;

LOG_FILE = '/var/log/domoleaf/domomaster.log'
TCP_PORT = 4998
BUFFER_SIZE = 1024

"""
Class representing a gateway IP / Infrared passing through sockets
"""
class IP_IRManager:

    def __init__(self):
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();

    def send_to_gc(self, json_obj, dev, hostame):
        """
        Send a packet to global cache
        """
        ir_addr = dev['addr_dst'];
        tcp_ip = dev['addr'];
        tcp_port = int(dev['plus1']);
        if not tcp_port:
            tcp_port = int(TCP_PORT);

        request = str(ir_addr);
        request += '\r';

        data = '';
        try:
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM);
            s.connect((tcp_ip, tcp_port));
            s.send(request.encode());
            data = s.recv(BUFFER_SIZE).decode();
            s.close();
        except Exception as e:
            self.logger.error(e);
        self.logger.debug("Received Global Cache data :" + str(data));
