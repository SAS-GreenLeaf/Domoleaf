#!/usr/bin/python3

import logging;

class Logger:
    """
    Logging class
    Takes log_flag (bool) to log or not, log_filename is the file to log in
    """
    def __init__(self, log_flag, log_filename):
        self.log_flag = log_flag;
        if self.log_flag:
            logging.basicConfig(filename = log_filename, level = logging.DEBUG);

    def info(self, msg):
        """
        Log an info message in the file
        """
        if self.log_flag:
            logging.info(msg);

    def error(self, msg):
        """
        Log an error message in the file
        """
        if self.log_flag:
            logging.error(msg);
