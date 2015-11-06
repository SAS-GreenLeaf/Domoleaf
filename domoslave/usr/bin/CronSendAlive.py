#!/usr/bin/python3

import sys;
sys.path.append('/usr/lib/domoleaf');
from GLManager import *;

if __name__ == "__main__":
    GLManager.send_cron('send_alive')
