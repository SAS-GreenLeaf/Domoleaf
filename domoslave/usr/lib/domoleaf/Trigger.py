#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import time;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'

class Trigger:

    def __init__(self, daemon):
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.triggers_list = '';
        self.update_triggers_list();

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

    def get_trigger_info(self, id_trigger):
        triggers_list = self.triggers_list;
        trigger = [];
        append = trigger.append;
        for condition in triggers_list:
            if (condition[0] == id_trigger):
                append(condition);
        return trigger;

    def test_trigger(self, id_trigger, global_state):
        trigger = self.get_trigger_info(id_trigger);
        res = True;
        for condition in trigger:
            res = res and self.test_condition(condition, global_state);
        if res:
            return 1;
        return 0;

    def get_device_state(self, room_device_id, option_id, global_state):
        device_state = [];
        for elem in global_state:
            if elem[0] == room_device_id and elem[1] == option_id:
                device_state = elem;
                return device_state;
        return device_state;

    def test_equ(self, val_device, val_condition):
        if (val_device == val_condition):
            return True;
        return False;

    def test_sup_equ(self, val_device, val_condition):
        val_device = float(val_device);
        val_condition = float(val_condition);
        if (val_device >= val_condition):
            return True;
        return False;

    def test_inf_equ(self, val_device, val_condition):
        val_device = float(val_device);
        val_condition = float(val_condition);
        if (val_device <= val_condition):
            return True;
        return False;

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
