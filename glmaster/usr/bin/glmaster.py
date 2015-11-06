#!/usr/bin/python3

import logging;
import sys;
sys.path.append("/usr/bin/glmaster");
import MasterDaemon;
import os;

LOG_FILE = '/var/log/domoleaf/domomaster.log'

log_flag = False;

"""
Master daemon main function
"""
if len(sys.argv) > 1:
    if sys.argv[1] == '--log':
        log_flag = True;

pid = os.fork();
if pid == 0:
    logging.basicConfig(filename=LOG_FILE, level=logging.DEBUG);
    try:
        MasterDaemon.MasterDaemon(log_flag).run();
    except Exception as e:
        logging.error(str(e));
else:
    pid_file = open('/var/run/glmaster.pid', 'w');
    pid_file.write(str(pid));
    pid_file.close();
