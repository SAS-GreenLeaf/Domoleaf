#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
from Schedule import *;
from Trigger import *;
import time;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'

class Scenario:

    def __init__(self, daemon):
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.scenarios_list = {};
        self.update_scenarios_list();

    def get_scenarios_tab(self, scenarios):
        scenarios_tab = {};
        self.logger.info('\n\nGETTING SCENARIOS TAB\n');
        for d in scenarios:
            scHash = str(d[4])+'_'+str(d[5])
            if (scHash not in scenarios_tab):
                scenarios_tab[scHash] = [];
            scenarios_tab[scHash].append(d)
        return scenarios_tab;
    
    def update_scenarios_list(self):
        self.logger.info('UPDATING SCENARIOS');

        query = ('SELECT id_scenario, trigger_events_conditions.id_trigger, id_schedule, '
                 'id_smartcmd, trigger_events_conditions.room_device_id, id_option '
                 'FROM trigger_events_conditions '
                 'JOIN scenarios_list '
                 'ON trigger_events_conditions.id_trigger=scenarios_list.id_trigger '
                 'WHERE activated = 1 && scenarios_list.id_trigger IS NOT NULL '
                 'ORDER BY id_scenario');
        scenarios_list = self.sql.mysql_handler_personnal_query(query);
        
        self.logger.info('S LIST = ' + str(scenarios_list) + '\n');
        self.scenarios_list = self.get_scenarios_tab(scenarios_list);
        self.logger.info('S TAB = ' + str(self.scenarios_list) + '\n\n\n');
        
    def check_all_scenarios(self, global_state, trigger, schedule, connection, doList):
        self.logger.info('CHECKING ALL SCENARIOS');
        self.logger.info('SCENARIOS LIST = ');
        self.logger.info(self.scenarios_list);
        self.logger.info('\n');
        for do in doList:
            slist = self.scenarios_list[str(do[1])+'_'+str(do[0])];
            self.logger.info('SLIST = ');
            self.logger.info(slist);
            for scenario in slist:
                self.logger.error(scenario);
                self.logger.info('Scenario : ' + str(scenario) + '\n\n');
                if trigger.test_trigger(scenario[1], global_state) == 1:
                    self.logger.info('Trigger OK');
                    if (scenario[2] is None or
                        scenario[2] is not None and schedule.test_schedule(scenario[2]) ==  1):
                        self.launch_scenario(scenario[3], connection);
    
    def launch_scenario(self, id_smartcmd, connection):
        self.logger.info('LAUNCH !!!');
        jsonString = json.JSONEncoder().encode({
            "data": id_smartcmd
        });
        data = json.JSONDecoder().decode(jsonString);
        self.daemon.smartcmd_launch(data, connection);
                                                
