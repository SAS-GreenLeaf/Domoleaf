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
from Schedule import *;
from Trigger import *;
from threading import Thread;
import time;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'

## Threaded class representing scenarios.
class Scenario(Thread):

    ## The constructor.
    #
    # @param daemon The daemon which instanciated this class.
    def __init__(self, daemon):
        Thread.__init__(self);
        ## Logger object for formatting and printing logs
        self.logger = Logger(False, LOG_FILE);
        ## SQL object for managing slave daemon database
        self.sql = MasterSql();
        ## Instance of the slave daemon
        self.daemon = daemon;
        ## List of scenarios
        self.scenarios_list = {};
        self.update_scenarios_list();

    ## Sets values of a scenario.
    #
    # @param global_state The new global state.
    # @param trigger The new trigger.
    # @param schedule The new schedule.
    # @param connection The new connection.
    # @param doList The new doList.
    def setValues(self, global_state, trigger, schedule, connection, doList):
        ## Global state object
        self.global_state = global_state;
        ## The trigger for the scenario
        self.trigger = trigger;
        ## The schedule for the scenario
        self.schedule = schedule;
        ## The connection to something
        self.connection = connection;
        ## The do list
        self.doList = doList;

    ## Starts the thread.
    def run(self):
        check_all_scenarios(self);

    ## Gets scenarios from a list.
    #
    # @param scenarios The scenarios list.
    #
    # @return An array of scenarios.
    def get_scenarios_tab(self, scenarios):
        scenarios_tab = {};
        self.logger.debug('\n\nGETTING SCENARIOS TAB\n');
        for d in scenarios:
            scHash = ''.join([str(d[4]), '_', str(d[5])]);
            if scHash not in scenarios_tab:
                scenarios_tab[scHash] = [];
            scenarios_tab[scHash].append(d)
        return scenarios_tab;

    ## Updates the list of scenarios in database.
    #
    # @param db The database handler (default 0).
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

    ## Starts a scenario.
    #
    # @param id_smartcmd ID of the smartcommand launching the scenario.
    # @param connection The connection used to communicate.
    def launch_scenario(self, id_smartcmd, connection):
        self.logger.debug('LAUNCH !!!');
        jsonString = json.JSONEncoder().encode({
            "data": id_smartcmd
        });
        data = json.JSONDecoder().decode(jsonString);
        self.daemon.smartcmd_launch(data, connection);

## Checks all the scenarios.
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
