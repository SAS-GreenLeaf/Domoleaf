#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import logging;

## Logging class.
# Takes log_flag (bool) to log or not, log_filename is the file to log in
class Logger:

    ## The constructor.
    #
    # @param log_flag True or False, depending on wheter you want the logger to log or not.
    # @param log_filename The filename of the log file
    def __init__(self, log_flag, log_filename):
        ## True or False, depending on wheter you want the logger to log or not.
        self.log_flag = log_flag;
        if self.log_flag:
            logging.basicConfig(filename = log_filename, level = logging.DEBUG);

    ## Logs and info message in the file if the log_flag is True.
    #
    # @param msg The message to log.
    # @return None
    def info(self, msg):
        if self.log_flag:
            logging.info(msg);

    ## Logs an error message in the file if the log_flag is True.
    #
    # @param msg The message to log.
    # @return None
    def error(self, msg):
        if self.log_flag:
            logging.error(msg);

    ## Logs a debug message in the file if the log_flag is True.
    #
    # @param msg The message to log.
    # @return None
    def debug(self, msg):
        if self.log_flag:
            logging.debug(msg);
