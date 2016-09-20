#!/usr/bin/python3

## @package domoslave
# Slave daemon for D3 boxes.
#
# Developed by GreenLeaf.

import logging;
import sys;
sys.path.append("/usr/bin/domoslave");
import SlaveDaemon;
import os;

## Const variable containing the path of the log file
LOG_FILE = '/var/log/domoleaf/domoslave.log';

## Flag for whether logs should be printed or not
log_flag = False;

if len(sys.argv) > 1:
    if sys.argv[1] == '--log':
        log_flag = True;

## The pid of the new forked process
pid = os.fork();
if not pid:
    if log_flag:
        ## The filename for the logs
        filename = LOG_FILE;
        ## The level of logging
        level = logging.DEBUG;
        logging.basicConfig(filename=filename, level=level);
    try:
        SlaveDaemon.SlaveDaemon(log_flag).run();
    except Exception as e:
        logging.error(str(e));
else:
    ## The pid file for the forked process
    pid_file = open('/var/run/domoslave.pid', 'w');
    pid_file.write(str(pid));
    pid_file.close();
