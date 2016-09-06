#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
from Schedule import *;
from Trigger import *;
from threading import Thread;
import time;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'

class Scenario(Thread):

    def __init__(self, daemon):
        Thread.__init__(self);
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.scenarios_list = {};
        self.update_scenarios_list();

    def setValues(self, global_state, trigger, schedule, connection, doList):
        self.global_state = global_state;
        self.trigger = trigger;
        self.schedule = schedule;
        self.connection = connection;
        self.doList = doList;

    def run(self):
        check_all_scenarios(self);

    def get_scenarios_tab(self, scenarios):
        scenarios_tab = {};
        self.logger.debug('\n\nGETTING SCENARIOS TAB\n');
        for d in scenarios:
            scHash = ''.join([str(d[4]), '_', str(d[5])]);
            if scHash not in scenarios_tab:
                scenarios_tab[scHash] = [];
            scenarios_tab[scHash].append(d)
        return scenarios_tab;

    def update_scenarios_list(self, db=0):
        self.logger.debug('UPDATING SCENARIOS');
        query = ''.join(['SELECT id_scenario, trigger_events_conditions.id_trigger, id_schedule, ',
                 'id_smartcmd, trigger_events_conditions.room_device_id, id_option ',
                 'FROM trigger_events_conditions ',
                 'JOIN scenarios_list ',
                 'ON trigger_events_conditions.id_trigger=scenarios_list.id_trigger ',
                 'WHERE activated = 1 && scenarios_list.id_trigger IS NOT NULL ',
                 'ORDER BY id_scenario']);
        scenarios_list = self.sql.mysql_handler_personnal_query(query, db);
        self.logger.debug('S LIST = '+str(scenarios_list)+'\n');
        self.scenarios_list = self.get_scenarios_tab(scenarios_list);
        self.logger.debug('S TAB = '+str(self.scenarios_list)+'\n\n\n');

    def launch_scenario(self, id_smartcmd, connection):
        self.logger.debug('LAUNCH !!!');
        jsonString = json.JSONEncoder().encode({
            "data": id_smartcmd
        });
        data = json.JSONDecoder().decode(jsonString);
        self.daemon.smartcmd_launch(data, connection);

def check_all_scenarios(self):
    self.logger.debug('CHECKING ALL SCENARIOS');
    self.logger.debug('SCENARIOS LIST = ');
    self.logger.debug(self.scenarios_list);
    self.logger.debug('\n');
    for do in self.doList:
        slist = self.scenarios_list[str(do[1])+'_'+str(do[0])];
        self.logger.debug('SLIST = ');
        self.logger.debug(slist);
        for scenario in slist:
            self.logger.error(scenario);
            self.logger.debug('Scenario : '+str(scenario)+'\n\n');
            if self.trigger.test_trigger(scenario[1], self.global_state) == 1:
                self.logger.debug('Trigger OK');
                if (scenario[2] is None or
                    scenario[2] is not None and self.schedule.test_schedule(scenario[2]) ==  1):
                    self.launch_scenario(scenario[3], self.connection);
