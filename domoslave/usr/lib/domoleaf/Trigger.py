#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import time;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'

## Class representing a trigger.
class Trigger:

    ## The constructor.
    #
    # @param daemon The daemon object which instanciated this class.
    def __init__(self, daemon):
        ## Logger object for formatting and printing logs
        self.logger = Logger(False, LOG_FILE);
        ## SQL object for managing database
        self.sql = MasterSql();
        ## Instance of the slave daemon
        self.daemon = daemon;
        ## Trigger list
        self.triggers_list = '';
        self.update_triggers_list();

    ## Updates the trigger list.
    #
    # @param db The database handler (default 0).
    def update_triggers_list(self, db=0):
        self.logger.debug('Updating Triggers');
        query = ('SELECT trigger_events_list.id_trigger, '
                 'trigger_events_conditions.room_device_id, '
                 'trigger_events_conditions.id_option, trigger_events_conditions.operator, trigger_events_conditions.value '
                 'FROM trigger_events_list '
                 'JOIN trigger_events_conditions '
                 'ON trigger_events_list.id_trigger = trigger_events_conditions.id_trigger '
                 'ORDER BY trigger_events_list.id_trigger');
        res = self.sql.mysql_handler_personnal_query(query, db);
        self.triggers_list = res;
        self.logger.debug(res);

    ## Retrieves the trigger informations from its ID.
    #
    # @param id_trigger ID of the trigger.
    #
    # @return Array containing trigger informations.
    def get_trigger_info(self, id_trigger):
        triggers_list = self.triggers_list;
        trigger = [];
        append = trigger.append;
        for condition in triggers_list:
            if (condition[0] == id_trigger):
                append(condition);
        return trigger;

    ## Tests all the conditions in a trigger from its ID.
    #
    # @param id_trigger The ID of the trigger.
    # @param global_state Global state.
    #
    # @return 1 if the conditions are verified, else 0.
    def test_trigger(self, id_trigger, global_state):
        trigger = self.get_trigger_info(id_trigger);
        res = True;
        for condition in trigger:
            res = res and self.test_condition(condition, global_state);
        if res:
            return 1;
        return 0;

    ## Gets a device state.
    #
    # @param room_device_id ID of the device.
    # @param option_id Option ID of the device.
    # @param global_state Global state.
    #
    # @return The state of the device.
    def get_device_state(self, room_device_id, option_id, global_state):
        device_state = [];
        for elem in global_state:
            if elem[0] == room_device_id and elem[1] == option_id:
                device_state = elem;
                return device_state;
        return device_state;

    ## Tests the equivalence between the value of a device and the value of a condition.
    #
    # @param val_device Value to test.
    # @param val_condition Value compared.
    #
    # @return True if the values are equal, else False.
    def test_equ(self, val_device, val_condition):
        if (val_device == val_condition):
            return True;
        return False;

    ## Tests the superiority or the equivalence between the value of a device and the value of a condition.
    #
    # @param val_device Value to test.
    # @param val_condition Value compared.
    #
    # @return True if val_device >= val_condition, else False.
    def test_sup_equ(self, val_device, val_condition):
        val_device = float(val_device);
        val_condition = float(val_condition);
        if (val_device >= val_condition):
            return True;
        return False;

    ## Tests the inferiority or the equivalence between the value of a device and the value of a condition.
    #
    # @param val_device Value to test.
    # @param val_condition Value compared.
    #
    # @return True if val_device <= val_condition, else False.
    def test_inf_equ(self, val_device, val_condition):
        val_device = float(val_device);
        val_condition = float(val_condition);
        if (val_device <= val_condition):
            return True;
        return False;

    ## Tests multiple conditions.
    #
    # @param condition Condition to test.
    # @param global_state Global state.
    #
    # @return Function to test
    def test_condition(self, condition, global_state):
        device_state = self.get_device_state(condition[1], condition[2], global_state);
        if not device_state:
            self.logger.error('No Device State');
            return False;
        self.logger.debug(device_state);
        functab = {
            "0" : self.test_equ,
            "1" : self.test_sup_equ,
            "2" : self.test_inf_equ
        };
        return functab[str(condition[3])](device_state[2], condition[4]);
