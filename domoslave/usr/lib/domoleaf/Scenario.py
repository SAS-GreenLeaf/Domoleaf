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

    def get_scenarios_tab(self, devices, scenarios):
        scenarios_tab = {};
        self.logger.info('\n\nGETTING SCENARIOS TAB\n');
        for d in devices:
            daemon_id = d[0];
            addr_plus = d[1];
            addr = d[2];
            device_id = d[3];
            if (daemon_id not in scenarios_tab):
                scenarios_tab[daemon_id] = {};
            if (addr_plus):
                if (daemon_id in scenarios_tab and addr_plus not in scenarios_tab[daemon_id]):
                    scenarios_tab[daemon_id][addr_plus] = [];
                    for s in scenarios:
                        room_device_id = s[4];
                        if (device_id == room_device_id):
                            s = s[:4];
                            scenarios_tab[daemon_id][addr_plus].append(s);
            elif (addr):
                if (daemon_id in scenarios_tab and addr not in scenarios_tab[daemon_id]):
                    scenarios_tab[daemon_id][addr] = [];
                    for s in scenarios:
                        self.logger.info('S = ' + str(s));
                        room_device_id = s[4];
                        if (device_id == room_device_id):
                            s = s[:4];
                            scenarios_tab[daemon_id][addr].append(s);
        return scenarios_tab;
    
    def update_scenarios_list(self):
        self.logger.info('UPDATING SCENARIOS');
        
        query = ('SELECT room_device.daemon_id, room_device_option.addr_plus, room_device_option.addr, '
                 'room_device.room_device_id, room_device.name '
                 'FROM room_device '
                 'JOIN room_device_option '
                 'ON room_device.room_device_id = room_device_option.room_device_id '
                 'WHERE daemon_id IS NOT NULL '
                 'ORDER BY room_device_id');
        res = self.sql.mysql_handler_personnal_query(query);
        devices_list = res;

        query = ('SELECT id_scenario, trigger_events_conditions.id_trigger, id_schedule, '
                 'id_smartcmd, trigger_events_conditions.room_device_id '
                 'FROM trigger_events_conditions '
                 'JOIN scenarios_list '
                 'ON trigger_events_conditions.id_trigger=scenarios_list.id_trigger '
                 'WHERE activated = 1 && scenarios_list.id_trigger IS NOT NULL '
                 'ORDER BY id_scenario');
        res = self.sql.mysql_handler_personnal_query(query);
        scenarios_list = res;

        self.logger.info('S LIST = ' + str(scenarios_list) + '\n');
        scenarios_tab = self.get_scenarios_tab(devices_list, scenarios_list);
        self.logger.info('S TAB = ' + str(scenarios_tab) + '\n\n\n');
        self.scenarios_list = scenarios_tab;
        
    def check_all_scenarios(self, global_state, trigger, schedule, connection, json_obj):
        daemon_id = json_obj['daemon_id'];
        dst_addr = json_obj['dst_addr'];

        self.logger.info('CHECKING ALL SCENARIOS');
        self.logger.info('SCENARIOS LIST = ');
        self.logger.info(self.scenarios_list);
        self.logger.info('\n');

        if self.scenarios_list[daemon_id][dst_addr]:
            slist = self.scenarios_list[daemon_id][dst_addr];
        else:
            return;
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
                                                
