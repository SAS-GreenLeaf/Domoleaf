#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import datetime;

LOG_FILE                = '/var/log/glmaster.log'

class Schedule:

    def __init__(self, daemon):
        self.logger = Logger(True, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.schedules_list = '';
        self.update_schedules_list();

    def update_schedules_list(self):
        self.logger.info('Updating Schedules');
        query = ('SELECT id_scenario, id_smartcmd, '
                 'months, weekdays, days, hours, mins '
                 'FROM scenarios_list '
                 'JOIN trigger_schedules_list ON scenarios_list.id_schedule = trigger_schedules_list.id_schedule '
                 'WHERE scenarios_list.id_schedule IS NOT NULL && id_trigger IS NULL && activated = 1 '
                 'ORDER BY id_scenario ');
        res = self.sql.mysql_handler_personnal_query(query);
        self.schedules_list = res;

    def check_all_schedules(self, connection):
        self.logger.info('Checking Schedules');
        try:
            schedules_list = self.schedules_list;
            for schedule in schedules_list:
                if self.test_schedule(schedule[2], schedule[3], schedule[4],
                                      schedule[5], schedule[6]) == 1:
                    self.logger.info('Scenario '+str(schedule[0])+' : OK\n\n\n');
                    self.launch_scenario(schedule[1], connection);
                else:
                    self.logger.info('Scenario '+str(schedule[0])+' : KO\n\n\n');
        except Exception as e:
            self.logger.error(e);

    def test_schedule(self, months, weekdays, days, hours, mins):
        now = datetime.datetime.now();
        curr_month = int(now.month);
        curr_weekday = int(now.strftime('%w'));
        curr_day = int(now.day);
        curr_hour = int(now.hour);
        curr_min = int(now.minute);

        months = list("{0:b}".format(int(months)).zfill(12));
        weekdays = list("{0:b}".format(int(weekdays)).zfill(7));
        days = list("{0:b}".format(int(days)).zfill(31));
        hours = list("{0:b}".format(int(hours)).zfill(24));
        mins = list(mins);
        
        if (int(months[curr_month]) == 1 and int(weekdays[curr_weekday]) == 1
            and int(days[curr_day]) == 1 and int(hours[curr_hour]) == 1
            and int(mins[curr_min]) == 1):
            return 1;
        return 0;

    def launch_scenario(self, id_smartcmd, connection):
        jsonString = json.JSONEncoder().encode({
            "data": id_smartcmd
        });
        data = json.JSONDecoder().decode(jsonString);
        self.logger.info('Launching SMARTCMD '+str(id_smartcmd)+'\n\n');
        self.daemon.smartcmd_launch(data, connection);
