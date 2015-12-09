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

class IP_IRManager:

    def __init__(self):
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();

    def send_to_gc(self, json_obj, dev, hostame):
        ir_port_list = {469: '2:1', 470: '2:2', 471: '2:3', 472: '4:1',
                        473: '4:2', 474: '4:3', 475: '5:1', 476: '5:2', 477: '5:3'};

        ir_port = ir_port_list[dev['dpt_id']];
        ir_addr = dev['addr_dst'];
        tcp_ip = dev['addr'];
        tcp_port = int(dev['plus1']);
        if not tcp_port:
            tcp_port = int(TCP_PORT);
        request = 'sendir,' + str(ir_port) + ',1,36000,1,1,';
        request += str(ir_addr);
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
        self.logger.info("Received Global Cache data :" + str(data));
