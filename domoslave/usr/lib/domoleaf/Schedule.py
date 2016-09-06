#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import datetime;

LOG_FILE                = '/var/log/domoleaf/domomaster.log'

"""
Class representing a schedule
"""
class Schedule:

    def __init__(self, daemon):
        self.logger = Logger(False, LOG_FILE);
        self.sql = MasterSql();
        self.daemon = daemon;
        self.schedules_list = '';
        self.full_schedules_list = '';
        self.update_schedules_list(0);

    def update_schedules_list(self, db):
        """
        Update the schedule list from the database
        """
        self.logger.debug('Updating Schedules');
        query = ('SELECT trigger_schedules_list.id_schedule, id_smartcmd, '
                 'months, weekdays, days, hours, mins '
                 'FROM scenarios_list '
                 'JOIN trigger_schedules_list ON scenarios_list.id_schedule = trigger_schedules_list.id_schedule '
                0 'WHERE scenarios_list.id_schedule IS NOT NULL && id_trigger IS NULL && activated = 1 '
                 'ORDER BY id_scenario ');
        if not db:
            self.schedules_list = self.sql.mysql_handler_personnal_query(query);
        else:
            self.schedules_list = self.sql.mysql_handler_personnal_query(query, db);
        query = ('SELECT trigger_schedules_list.id_schedule, id_smartcmd, '
                 'months, weekdays, days, hours, mins '
                 'FROM scenarios_list '
                 'JOIN trigger_schedules_list ON scenarios_list.id_schedule = trigger_schedules_list.id_schedule '
                 'WHERE scenarios_list.id_schedule IS NOT NULL && activated = 1 '
                 'ORDER BY id_scenario ');
        if not db:
            self.full_schedules_list = self.sql.mysql_handler_personnal_query(query);
        else:
            self.full_schedules_list = self.sql.mysql_handler_personnal_query(query, db);


    def get_schedule_infos(self, id_schedule):
        """
        Retrieve schedule infos from its ID
        """
        schedules_list = self.full_schedules_list;
        for schedule in schedules_list:
            if (schedule[0] == id_schedule):
                return (schedule[2], schedule[3], schedule[4],
                        schedule[5], schedule[6]);
        return 0;

    def check_all_schedules(self, connection):
        """
        Test the schedules and starts a scenario if the test is valid
        """
        schedules_list = self.schedules_list;
        for schedule in schedules_list:
            if self.test_schedule(schedule[0]) == 1:
                self.launch_scenario(schedule[1], connection);

    def test_schedule(self, id_schedule):
        """
        Test if the schedule have to be started
        """
        if not self.full_schedules_list:
            return 0;
        months, weekdays, days, hours, mins = self.get_schedule_infos(id_schedule);
        now = datetime.datetime.now();
        curr_month = int(now.month) - 1;
        curr_weekday = int(now.strftime('%w'));
        curr_day = int(now.day) - 1;
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
        """
        Start a scenario in the smart command
        """
        jsonString = json.JSONEncoder().encode({
            "data": id_smartcmd
        });
        data = json.JSONDecoder().decode(jsonString);
        self.daemon.smartcmd_launch(data, connection, 0);
