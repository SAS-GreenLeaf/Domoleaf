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

LOG_FILE                       = '/var/log/domoleaf/domomaster.log'
TIME_INTERVAL                  = 600  #10min
TIME_RANGE_TO_CALC             = 3600 #1h
TIME_BEFORE_TIME_TO_CALC       = 1800 #30min

#TEST_TIMESTAMP                  = 1448274600

## Class calculating logs to have an average of values when there are too much.
class CalcLogs:

    ## The constructor.
    #
    # @param daemon The slave daemon which initialized this class instance.
    def __init__(self, daemon):
        ## The logger used for formating and printing
        self.logger = Logger(False, LOG_FILE);
        self.logger.debug('Init CalcLogs');

        ## SQL manager
        self.sql = MasterSql();

        ## The instance of the slave daemon
        self.daemon = daemon;

        ## List of the devices
        self.devices_list = {};
        self.devices_list_update();

    ## Updates the device list.
    #
    # Queries the database to fetch room_device and room_device_option.
    # The result is stored in a class variable.
    def devices_list_update(self):
        self.logger.debug('Updating Logs');
        query = ('SELECT room_device.daemon_id, room_device_option.addr_plus, room_device_option.addr, '
                 'room_device.room_device_id, room_device_option.option_id, room_device.name '
                 'FROM room_device '
                 'JOIN room_device_option '
                 'ON room_device.room_device_id = room_device_option.room_device_id '
                 'WHERE daemon_id IS NOT NULL '
                 'ORDER BY room_device_id');
        res = self.sql.mysql_handler_personnal_query(query);
        self.devices_list = self.get_tab_devices_list(res);

    ## Stores the device list in an array and returns it.
    #
    # @param res Array containing all informations about room_device and room_device_option.
    #
    # @return An array containing informations about the devices, with daemon_id as index.
    def get_tab_devices_list(self, res):
        tab = {};
        for r in res:
            daemon_id = r[0];
            addr_plus = r[1];
            addr = r[2];
            if daemon_id not in tab:
                tab[daemon_id] = {};
            if addr_plus:
                if daemon_id in tab and addr_plus not in tab[daemon_id]:
                    tab[daemon_id][addr_plus] = [];
                    r = r[3:];
                    tab[daemon_id][addr_plus].append(r);
            else:
                if daemon_id in tab and addr not in tab[daemon_id]:
                    tab[daemon_id][addr] = [];
                    r = r[3:];
                    tab[daemon_id][addr].append(r);
        return tab;

    ## Stores only the logs of specific devices.
    #
    # @return an array containing the logs.
    def get_only_good_logs(self, res):
        tab = [];
        tab_append = tab.append;
        self.logger.debug(self.devices_list);
        for r in res:
            if (r[1] in self.devices_list[r[0]]):
                self.logger.debug(r);
                log = [];
                log_append = log.append;
                log_append(r[2]);
                log_append(r[3]);
                log_append(self.devices_list[r[0]][r[1]][0][0]);
                log_append(self.devices_list[r[0]][r[1]][0][1]);
                log_append(r[4]);
                log_append(r[5]);
                log_append(r[1]);
                log_append(r[0]);
                tab_append(log);
        return tab;

    ## Gets the devices for which logs will be done.
    #
    # @param res Array containing all the room devices informations.
    #
    # @return An array containing the room devices for who the logs will be done.
    def get_good_time_range(self, res):
        end_tr = time.time() - TIME_BEFORE_TIME_TO_CALC - 1;
        init_tr = end_tr - TIME_RANGE_TO_CALC + 1;
        tab = [];
        append = tab.append;
        for r in res:
            if (r[0] <= end_tr):
                append(r);
        return (tab);

    ## Cuts the logs array.
    #
    # @param tab The array to cut.
    #
    # @return The cut dictionnary.
    def cut_tab(self, tab):
        cut_tab = {};
        cut_dict = {};
        for log in tab:
            device_id = log[2];
            option_id = log[3];
            if (device_id not in cut_tab):
                cut_tab[device_id] = {};
            if (option_id not in cut_tab[device_id]):
                cut_tab[device_id][option_id] = [];
            cut_tab[device_id][option_id].append(log);
        for device in cut_tab:
            cut_dict[device] = {};
            for option in cut_tab[device]:
                cut_dict[device][option] = [];
                for log in cut_tab[device][option]:
                    cut_dict[device][option].append(log);
        return cut_dict;

    ## Calculates average values of some data logged.
    #
    # @param dictlogs Array containing the logs.
    #
    # @return An array containing the average values calculated.
    def calc_average_values(self, dictlogs):
        dictaverage = {};
        for time_r in dictlogs:
            dictaverage[time_r] = {};
            for device in dictlogs[time_r]:
                dictaverage[time_r][device] = {};
                for option in dictlogs[time_r][device]:
                    dictaverage[time_r][device][option] = [];
                    t0 = -1;
                    t1 = 0;
                    avg = 0;
                    avg_elem = [];
                    append = avg_elem.append;
                    val = 0;
                    for elem in dictlogs[time_r][device][option]:
                        if (t0 == -1):
                            t0 = elem[0];
                            val = elem[1];
                            append(t0);
                        else:
                            t1 = elem[0];
                            avg += (t1 - t0) * val;
                            t0 = t1;
                            val = elem[1];
                    append(int(avg / TIME_INTERVAL));
                    append(device);
                    append(option);
                    dictaverage[time_r][device][option].append(avg_elem);
        return (dictaverage);

    ## Gets all the logs from the database.
    #
    # @param db The database handler.
    #
    # @return An array containing all the logs of the database.
    def get_logs(self, db):
        query = ('SELECT daemon_id, addr_dest, t_date, knx_value, type, addr_src '
                 'FROM knx_log '
                 'ORDER BY t_date');
        return self.sql.mysql_handler_personnal_query(query, db);

    ## Cuts a dictionnay depending on the time.
    #
    # @param dictlogs The array to cut.
    #
    # @return An array of the logs corresponding the right time range.
    def cut_dict_time(self, dictlogs):
        end_tr = time.time() - TIME_BEFORE_TIME_TO_CALC - 1;
        init_tr = end_tr - TIME_RANGE_TO_CALC + 1;
        init = init_tr;
        end = init + TIME_INTERVAL - 1;
        dicttime = {};
        tmpdict = {};
        n = int(TIME_RANGE_TO_CALC / TIME_INTERVAL);
        for time_r in range(n):
            dicttime[time_r] = {};
            tmpdict[time_r] = {};
            for device in dictlogs:
                dicttime[time_r][device] = {};
                tmpdict[time_r][device] = {};
                for option in dictlogs[device]:
                    dicttime[time_r][device][option] = [];
                    tmpdict[time_r][device][option] = [];
                    first = tmpdict[time_r][device][option];
                    if (not first):
                        first = list(dictlogs[device][option][0]);
                        first[0] = init;
                    last = list(first);
                    dicttime[time_r][device][option].append(first);
                    for log in dictlogs[device][option]:
                        date = log[0];
                        if (date >= init and date <= end):
                            dicttime[time_r][device][option].append(log);
                            last = list(log);
                        if (date > end):
                            break;
                    last[0] = end + 1;
                    dicttime[time_r][device][option].append(last);
                    tmpdict[time_r][device][option] = list(last);
            init += TIME_INTERVAL;
            end += TIME_INTERVAL;
        return dicttime;

    ## Inserts a graphic log in the database.
    #
    # @param dictlogs Dictionnay containing all the logs.
    # @param db The database handler.
    def save_graph_logs(self, dictlogs, db):
        query = ('INSERT INTO graphic_log '
                 '(date, value, room_device_id, option_id) '
                 'VALUES ');
        for time_r in dictlogs:
            for device in dictlogs[time_r]:
                for option in dictlogs[time_r][device]:
                    for log in dictlogs[time_r][device][option]:
                        query += '('+str(log[0])+', '+str(log[1])+', '+str(log[2])+', '+str(log[3])+'), ';
        query = query[:-2];
        res = self.sql.mysql_handler_personnal_query(query, db);

    ## Deletes the KNX logs from the database.
    #
    # @param dictlogs Dictionnay containing all the logs for all devices.
    # @param db The database handler.
    def delete_knx_logs(self, dictlogs, db):
        end_tr = time.time() - TIME_BEFORE_TIME_TO_CALC;
        last_logs = [];
        append = last_logs.append;
        for device in dictlogs:
            for option in dictlogs[device]:
                log = dictlogs[device][option][-1];
                append(log);
        query = ('DELETE FROM knx_log '
                 'WHERE t_date < ' + str(end_tr));
        res = self.sql.mysql_handler_personnal_query(query, db);
        query = ('DELETE FROM enocean_log '
                 'WHERE t_date < ' + str(end_tr));
        res = self.sql.mysql_handler_personnal_query(query, db);
        query = ('INSERT INTO knx_log '
                 '(type, addr_src, addr_dest, knx_value, t_date, daemon_id) '
                 'VALUES ');
        for log in last_logs:
            query += ('( '+str(log[4])+', '+'\''+str(log[5])+'\', '+
                      '\''+str(log[6])+'\', '+str(log[1])+', '+
                      str(log[0])+', '+str(log[7])+'), ');
        query = query[:-2];
        res = self.sql.mysql_handler_personnal_query(query, db);

    ## Sorts the logs by calling multiple function to print only the wanted logs.
    #
    # @param connection Not used here.
    # @param db The database handler.
    def sort_logs(self, connection, db):
        self.logger.debug('\n\nSorting Logs : \n');
        try:
            logs = self.get_logs(db);
            if not logs:
                return;
            tablogs = self.get_only_good_logs(logs);
            self.logger.debug('TABLOGS 1 :\n'+str(tablogs)+'\n');
            tablogs = self.get_good_time_range(tablogs);
            if not tablogs:
                return;
            self.logger.debug('TABLOGS 2 :\n'+str(tablogs)+'\n');
            dictlogs = self.cut_tab(tablogs);
            if not dictlogs:
                return;
            self.logger.debug('DICTLOGS :\n'+str(dictlogs)+'\n');
            dictlogstime = self.cut_dict_time(dictlogs);
            dictlogstime = self.calc_average_values(dictlogstime);
            if not dictlogstime:
                return;
            self.logger.debug('DICTLOGSTIME :\n'+str(dictlogstime)+'\n');
            self.logger.debug('Save Graph Logs\n');
            self.save_graph_logs(dictlogstime, db);
            self.logger.debug('Delete Logs\n');
            self.delete_knx_logs(dictlogs, db);
            self.logger.debug('OK\n\n');
        except Exception as e:
            self.logger.error(e);
