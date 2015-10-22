#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import time;

LOG_FILE                = '/var/log/glmaster.log'

class Trigger:

    def __init__(self, daemon):
        self.logger = Logger(True, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.triggers_list = '';
        self.update_triggers_list();

    def update_triggers_list(self):
        self.logger.info('Updating Triggers');
        query = ('SELECT trigger_events_list.id_trigger, trigger_events_list.id_smartcmd, '
                 'trigger_events_conditions.room_device_id, '
                 'trigger_events_conditions.id_option, trigger_events_conditions.operator, trigger_events_conditions.value '
                 'FROM trigger_events_list '
                 'JOIN trigger_events_conditions '
                 'ON trigger_events_list.id_trigger = trigger_events_conditions.id_trigger '
                 'ORDER BY trigger_events_list.id_trigger, trigger_events_list.id_smartcmd');
        res = self.sql.mysql_handler_personnal_query(query);
        self.triggers_list = res;

    def check_all_conditions(self, global_state, connection):
        self.logger.info('Checking Triggers Conditions');
        list = self.triggers_list;
        len_list = len(list);
        i = 0;
        while i < len_list:
            id_trigger = list[i][0];
            res = True;
            while (i < len_list and id_trigger == list[i][0]):
                id_smartcmd = list[i][1];
                res = res and self.test_condition(list[i], global_state);
                i = i + 1;
            if res == True:
                self.launch_trigger(id_smartcmd, connection);

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
        device_state = self.get_device_state(condition[2], condition[3], global_state);
        if not device_state:
            self.logger.error('No Device State');
            return False;
        functab = {
            "0" : self.test_equ,
            "1" : self.test_sup_equ,
            "2" : self.test_inf_equ
            };
        res = functab[str(condition[4])](device_state[2], condition[5]);
        return res;

    def launch_trigger(self, id_smartcmd, connection):
        jsonString = json.JSONEncoder().encode({
                "data": id_smartcmd
                })
        data = json.JSONDecoder().decode(jsonString);
        self.daemon.smartcmd_launch(data, connection);
