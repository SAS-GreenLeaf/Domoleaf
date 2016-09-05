#!/usr/bin/python3

import sys;
sys.path.append('/usr/lib/domoleaf');
from GLManager import *;

"""
Cron that refreshes the list of connected devices on the local network
"""
if __name__ == "__main__":
    GLManager.send_cron('monitor_ip')
