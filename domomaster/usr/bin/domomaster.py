#!/usr/bin/python3

## @package domomaster
# Master daemon for D3 boxes.
#
# Developed by GreenLeaf.

import logging;
import sys;
sys.path.append("/usr/bin/domomaster");
import MasterDaemon;
import os;

LOG_FILE = '/var/log/domoleaf/domomaster.log'

log_flag = False;

if len(sys.argv) > 1:
    if sys.argv[1] == '--log':
        log_flag = True;
pid = os.fork();
if not pid:
    if log_flag:
        logging.basicConfig(filename=LOG_FILE, level=logging.DEBUG);
    try:
        MasterDaemon.MasterDaemon(log_flag).run();
    except Exception as e:
        logging.error(str(e));
else:
    pid_file = open('/var/run/domomaster.pid', 'w');
    pid_file.write(str(pid));
    pid_file.close();
