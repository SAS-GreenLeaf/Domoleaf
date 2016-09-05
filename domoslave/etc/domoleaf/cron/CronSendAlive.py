#!/usr/bin/python3

import sys;
sys.path.append('/usr/lib/domoleaf');
from GLManager import *;

"""
Cron that sends the information that the slave is alive
"""
if __name__ == "__main__":
    GLManager.send_cron('send_alive')
