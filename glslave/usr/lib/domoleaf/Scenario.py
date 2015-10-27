#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
from Schedule import *;
from Trigger import *;
import time;

LOG_FILE                = '/var/log/glmaster.log'

class Scenario:

    def __init__(self, daemon):
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.scenarios_list = '';
        self.update_scenarios_list();

    def update_scenarios_list(self):
        query = ('SELECT  id_scenario, id_trigger, id_schedule, id_smartcmd '
                 'FROM scenarios_list '
                 'WHERE activated = 1 && id_trigger IS NOT NULL '
                 'ORDER BY id_scenario');
        res = self.sql.mysql_handler_personnal_query(query);
        self.scenarios_list = res;
        self.logger.info(res);

    def check_all_scenarios(self, global_state, trigger, schedule, connection):
        slist = self.scenarios_list;
        for scenario in slist:
            self.logger.info(scenario);
            if trigger.test_trigger(scenario[1], global_state) == 1:
                if (scenario[2] is None or
                    scenario[2] is not None and schedule.test_schedule(scenario[2]) ==  1):
                    self.launch_scenario(scenario[3], connection);
    
    def launch_scenario(self, id_smartcmd, connection):
        jsonString = json.JSONEncoder().encode({
            "data": id_smartcmd
        });
        data = json.JSONDecoder().decode(jsonString);
        self.daemon.smartcmd_launch(data, connection);
                                                
